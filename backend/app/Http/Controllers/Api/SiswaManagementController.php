<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SiswaManagementController extends Controller
{
    // Ambil semua data siswa
    public function index()
    {
        $siswa = User::where('role', 'siswa')
            ->with(['jadwalPikets:id,user_id,hari']) // Mengambil data jadwal piket terkait
            ->select('id', 'name', 'email', 'created_at')
            ->latest()
            ->get();

        return response()->json([
            'data' => $siswa
        ]);
    }

    // Tambah data siswa baru
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $siswa = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'siswa',
        ]);

        return response()->json([
            'message' => 'Data siswa berhasil ditambahkan',
            'data'    => $siswa->only(['id', 'name', 'email', 'created_at'])
        ], 201);
    }

    // Detail 1 data siswa
    public function show($id)
    {
        $siswa = User::where('role', 'siswa')
            ->select('id', 'name', 'email', 'created_at')
            ->findOrFail($id);

        return response()->json([
            'data' => $siswa
        ]);
    }

    // Edit data siswa
    public function update(Request $request, $id)
    {
        $siswa = User::where('role', 'siswa')->findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6', // Password opsional saat edit
        ]);

        $dataUpdate = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        // Jika admin mengisi password baru
        if ($request->filled('password')) {
            $dataUpdate['password'] = Hash::make($request->password);
        }

        $siswa->update($dataUpdate);

        return response()->json([
            'message' => 'Data siswa berhasil diperbarui',
            'data'    => $siswa->only(['id', 'name', 'email', 'updated_at'])
        ]);
    }

    // Hapus data siswa
    public function destroy($id)
    {
        $siswa = User::where('role', 'siswa')->findOrFail($id);
        $siswa->delete();

        return response()->json([
            'message' => 'Data siswa berhasil dihapus'
        ]);
    }
}