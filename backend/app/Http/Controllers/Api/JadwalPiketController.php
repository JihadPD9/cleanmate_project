<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalPiket;
use Illuminate\Http\Request;

class JadwalPiketController extends Controller
{
    // Mengambil seluruh jadwal piket (dikelompokkan per hari atau urut)
    public function index()
    {
        $jadwal = JadwalPiket::with('user:id,name,email')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
            ->get();

        return response()->json([
            'data' => $jadwal
        ]);
    }

    // Mengambil jadwal piket siswa yang sedang login
    public function me(Request $request)
    {
        $jadwal = JadwalPiket::where('user_id', $request->user()->id)->get();

        return response()->json([
            'data' => $jadwal
        ]);
    }

    // Admin menambahkan siswa ke jadwal piket hari tertentu
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'hari'    => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
        ]);

        // Cek apakah siswa sudah ada di hari tersebut
        $exists = JadwalPiket::where('user_id', $request->user_id)
            ->where('hari', $request->hari)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Siswa sudah terdaftar di jadwal piket hari ini.'
            ], 422);
        }

        $jadwal = JadwalPiket::create([
            'user_id' => $request->user_id,
            'hari'    => $request->hari,
        ]);

        return response()->json([
            'message' => 'Jadwal piket berhasil ditambahkan',
            'data'    => $jadwal->load('user:id,name,email')
        ], 201);
    }

    // Admin menghapus siswa dari jadwal piket
    public function destroy($id)
    {
        $jadwal = JadwalPiket::findOrFail($id);
        $jadwal->delete();

        return response()->json([
            'message' => 'Jadwal piket berhasil dihapus'
        ]);
    }
}