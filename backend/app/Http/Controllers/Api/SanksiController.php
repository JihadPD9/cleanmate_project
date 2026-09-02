<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sanksi;
use App\Models\SanksiSiswa;
use Illuminate\Http\Request;

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

    // Admin hapus master sanksi
    public function destroyMaster($id)
    {
        $sanksi = Sanksi::findOrFail($id);
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
            'user_id'     => 'required|exists:users,id',
            'sanksi_id'   => 'required|exists:sanksis,id',
            'tipe_sanksi' => 'required|in:individu,kelompok',
            'alasan'      => 'required|string',
        ]);

        $sanksiSiswa = SanksiSiswa::create([
            'user_id'             => $request->user_id,
            'sanksi_id'           => $request->sanksi_id,
            'tipe_sanksi'         => $request->tipe_sanksi,
            'alasan'              => $request->alasan,
            'status_penyelesaian' => 'belum',
        ]);

        return response()->json([
            'message' => 'Sanksi berhasil diberikan kepada siswa',
            'data'    => $sanksiSiswa->load(['user:id,name', 'sanksi'])
        ], 201);
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