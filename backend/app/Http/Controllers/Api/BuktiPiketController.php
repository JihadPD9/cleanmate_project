<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BuktiPiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BuktiPiketController extends Controller
{
    // Mengambil semua data bukti piket (untuk admin)
    public function index()
    {
        $bukti = BuktiPiket::with(['user:id,name,email', 'tasks'])->latest()->get();

        return response()->json(['data' => $bukti]);
    }

    // Mengambil bukti piket milik siswa yang sedang login
    public function me(Request $request)
    {
        $bukti = BuktiPiket::with('tasks')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json(['data' => $bukti]);
    }

    // Mengambil status bukti piket kelompok hari ini (untuk Dashboard Siswa)
    public function todayStatus(Request $request)
    {
        $today = Carbon::today()->toDateString();

        // Cari bukti piket kelompok yang paling terbaru untuk hari ini
        $buktiHariIni = BuktiPiket::with(['user:id,name', 'tasks'])
            ->whereDate('tanggal', $today)
            ->latest()
            ->first();

        return response()->json([
            'has_uploaded' => $buktiHariIni ? true : false,
            'data'         => $buktiHariIni
        ]);
    }

    // Siswa upload bukti piket
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'   => 'required|date',
            'foto_1'    => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'foto_2'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'deskripsi' => 'nullable|string',
            'tasks'     => 'required', // Array ID task yang dikerjakan
            'tasks.*'   => 'exists:tasks,id',
        ]);

        // Proteksi Kelompok: Cek apakah hari ini sudah ada bukti piket kelompok yang 'pending' atau 'approved'
        $existingBukti = BuktiPiket::whereDate('tanggal', $request->tanggal)
            ->whereIn('status_approval', ['pending', 'approved'])
            ->with('user:id,name')
            ->first();

        if ($existingBukti) {
            $uploaderName = $existingBukti->user ? $existingBukti->user->name : 'Siswa lain';
            $statusMsg = $existingBukti->status_approval === 'approved' 
                ? 'sudah disetujui oleh Admin.' 
                : 'sedang menunggu verifikasi Admin.';

            return response()->json([
                'message' => "Bukti piket kelompok hari ini sudah diunggah oleh {$uploaderName} dan {$statusMsg}"
            ], 422);
        }

        // Upload foto
        $foto1Path = $request->file('foto_1')->store('bukti_piket', 'public');
        $foto2Path = $request->hasFile('foto_2') 
            ? $request->file('foto_2')->store('bukti_piket', 'public') 
            : null;

        // Simpan data bukti piket
        $bukti = BuktiPiket::create([
            'user_id'         => $request->user()->id,
            'tanggal'         => $request->tanggal,
            'foto_1'          => $foto1Path,
            'foto_2'          => $foto2Path,
            'deskripsi'       => $request->deskripsi,
            'status_approval' => 'pending',
        ]);

        // Sync relasi ke tabel pivot detail_bukti_tugas
        $bukti->tasks()->attach($request->tasks);

        return response()->json([
            'message' => 'Bukti piket berhasil diunggah',
            'data'    => $bukti->load('tasks')
        ], 201);
    }

    // Admin approve atau reject bukti piket
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_approval' => 'required|in:approved,rejected',
            'catatan_admin'   => 'nullable|string',
        ]);

        $bukti = BuktiPiket::findOrFail($id);
        $bukti->update([
            'status_approval' => $request->status_approval,
            'catatan_admin'   => $request->catatan_admin,
        ]);

        return response()->json([
            'message' => 'Status bukti piket berhasil diperbarui',
            'data'    => $bukti
        ]);
    }
}