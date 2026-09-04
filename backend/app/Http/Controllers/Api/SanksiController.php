<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sanksi;
use App\Models\User;
use App\Models\JadwalPiket;
use App\Models\SanksiSiswa;
use Illuminate\Http\Request;
use App\Notifications\SanksiNotification;

class SanksiController extends Controller
{
    // ================= MASTER SANKSI =================
    
    // Ambil semua master sanksi
    public function indexMaster()
    {
        return response()->json([
            'data' => Sanksi::all()
        ]);
    }

    // Admin tambah master sanksi
    public function storeMaster(Request $request)
    {
        $request->validate([
            'nama_sanksi' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
        ]);

        $sanksi = Sanksi::create($request->all());

        return response()->json([
            'message' => 'Master sanksi berhasil ditambahkan',
            'data'    => $sanksi
        ], 201);
    }

    // Admin edit master sanksi
    public function updateMaster(Request $request, $id)
    {
        $sanksi = Sanksi::findOrFail($id);

        // Cek apakah master sanksi ini sedang aktif digunakan oleh siswa/kelompok yang statusnya 'belum'
        $sedangDigunakan = SanksiSiswa::where('sanksi_id', $id)
            ->where('status_penyelesaian', 'belum')
            ->exists();

        if ($sedangDigunakan) {
            return response()->json([
                'message' => 'Master sanksi tidak dapat diubah karena sedang diberikan kepada siswa/kelompok yang belum menyelesaikan sanksi tersebut. Harap selesaikan sanksi pada siswa/kelompok terlebih dahulu!'
            ], 422);
        }

        $request->validate([
            'nama_sanksi' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
        ]);

        $sanksi->update($request->all());

        return response()->json([
            'message' => 'Master sanksi berhasil diperbarui',
            'data'    => $sanksi
        ]);
    }

    // Admin hapus master sanksi
    public function destroyMaster($id)
    {
        $sanksi = Sanksi::findOrFail($id);

        // Cek apakah master sanksi ini sedang aktif digunakan oleh siswa/kelompok yang statusnya 'belum'
        $sedangDigunakan = SanksiSiswa::where('sanksi_id', $id)
            ->where('status_penyelesaian', 'belum')
            ->exists();

        if ($sedangDigunakan) {
            return response()->json([
                'message' => 'Master sanksi tidak dapat dihapus karena sedang diberikan kepada siswa/kelompok yang belum menyelesaikan sanksi tersebut. Harap selesaikan sanksi pada siswa/kelompok terlebih dahulu!'
            ], 422);
        }

        $sanksi->delete();

        return response()->json([
            'message' => 'Master sanksi berhasil dihapus'
        ]);
    }

    // ================= SANKSI SISWA =================

    // Ambil daftar sanksi siswa (Admin lihat semua)
    public function indexSiswa()
    {
        $sanksiSiswa = SanksiSiswa::with(['user:id,name,email', 'sanksi'])
            ->latest()
            ->get();

        return response()->json([
            'data' => $sanksiSiswa
        ]);
    }

    // Ambil sanksi milik siswa yang sedang login
    public function meSiswa(Request $request)
    {
        $sanksi = SanksiSiswa::with('sanksi')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'data' => $sanksi
        ]);
    }

    // Admin memberikan sanksi ke siswa
    public function storeSiswa(Request $request)
    {
        $request->validate([
            'user_id'     => 'nullable|exists:users,id',
            'hari'        => 'nullable|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'sanksi_id'   => 'required|exists:sanksis,id',
            'tipe_sanksi' => 'required|in:individu,kelompok',
            'alasan'      => 'required|string',
        ]);

        // Scenario A: Sanksi Individu
        if ($request->tipe_sanksi === 'individu') {
            if (!$request->user_id) {
                return response()->json(['message' => 'User ID wajib diisi untuk sanksi individu.'], 422);
            }

            $sanksiSiswa = SanksiSiswa::create([
                'user_id'             => $request->user_id,
                'sanksi_id'           => $request->sanksi_id,
                'tipe_sanksi'         => 'individu',
                'alasan'              => $request->alasan,
                'status_penyelesaian' => 'belum',
            ]);

            // Kirim inbox ke siswa tersebut
            $user = User::find($request->user_id);
            if ($user) {
                $user->notify(new SanksiNotification($sanksiSiswa));
            }

            return response()->json([
                'message' => 'Sanksi individu berhasil diberikan dan inbox terkirim.',
                'data'    => $sanksiSiswa
            ], 201);
        }

        // Scenario B: Sanksi Kelompok (Semua siswa yang piket di hari tersebut)
        if ($request->tipe_sanksi === 'kelompok' && $request->hari) {
            $siswaPiket = JadwalPiket::where('hari', $request->hari)->get();

            if ($siswaPiket->isEmpty()) {
                return response()->json(['message' => "Tidak ada siswa yang terdaftar di jadwal piket hari {$request->hari}."], 404);
            }

            foreach ($siswaPiket as $piket) {
                $sanksiSiswa = SanksiSiswa::create([
                    'user_id'             => $piket->user_id,
                    'sanksi_id'           => $request->sanksi_id,
                    'tipe_sanksi'         => 'kelompok',
                    'alasan'              => $request->alasan,
                    'status_penyelesaian' => 'belum',
                ]);

                // Kirim inbox ke masing-masing anggota kelompok
                $user = User::find($piket->user_id);
                if ($user) {
                    $user->notify(new SanksiNotification($sanksiSiswa));
                }
            }

            return response()->json([
                'message' => "Sanksi kelompok hari {$request->hari} berhasil diberikan ke semua anggota piket.",
            ], 201);
        }
    }

    // Admin meng-edit record sanksi siswa
    public function updateSiswa(Request $request, $id)
    {
        $sanksiSiswa = SanksiSiswa::findOrFail($id);

        $request->validate([
            'sanksi_id' => 'required|exists:sanksis,id',
            'alasan'    => 'required|string',
        ]);

        $sanksiSiswa->update([
            'sanksi_id' => $request->sanksi_id,
            'alasan'    => $request->alasan,
        ]);

        return response()->json([
            'message' => 'Data sanksi siswa berhasil diperbarui',
            'data'    => $sanksiSiswa->load(['user:id,name', 'sanksi'])
        ]);
    }

    // Admin menghapus sanksi siswa
    public function destroySiswa($id)
    {
        $sanksiSiswa = SanksiSiswa::findOrFail($id);
        $sanksiSiswa->delete();

        return response()->json([
            'message' => 'Data sanksi siswa berhasil dihapus'
        ]);
    }

    // Admin / Siswa meng-update status sanksi (misal: dari 'belum' ke 'selesai')
    public function updateStatusSiswa(Request $request, $id)
    {
        $request->validate([
            'status_penyelesaian' => 'required|in:belum,selesai',
        ]);

        $sanksiSiswa = SanksiSiswa::findOrFail($id);
        $sanksiSiswa->update([
            'status_penyelesaian' => $request->status_penyelesaian,
        ]);

        return response()->json([
            'message' => 'Status penyelesaian sanksi berhasil diperbarui',
            'data'    => $sanksiSiswa
        ]);
    }
}