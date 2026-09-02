<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Task::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
        ]);

        $task = Task::create($request->all());

        return response()->json([
            'message' => 'Tugas berhasil ditambahkan',
            'data'    => $task
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
        ]);

        $task->update($request->all());

        return response()->json([
            'message' => 'Tugas berhasil diperbarui',
            'data'    => $task
        ]);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json([
            'message' => 'Tugas berhasil dihapus'
        ]);
    }
}