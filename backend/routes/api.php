<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\BuktiPiketController;
use App\Http\Controllers\Api\JadwalPiketController;
use App\Http\Controllers\Api\SanksiController;

// Public Route
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Butuh Token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth & Profile
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Master Tasks
    Route::apiResource('tasks', TaskController::class);

    // Bukti Piket
    Route::get('/bukti-piket', [BuktiPiketController::class, 'index']); // Admin lihat semua
    Route::get('/bukti-piket/me', [BuktiPiketController::class, 'me']); // Siswa lihat punya sendiri
    Route::post('/bukti-piket', [BuktiPiketController::class, 'store']); // Siswa upload
    Route::patch('/bukti-piket/{id}/status', [BuktiPiketController::class, 'updateStatus']); // Admin approval

    // Jadwal Piket
    Route::get('/jadwal-piket', [JadwalPiketController::class, 'index']);
    Route::get('/jadwal-piket/me', [JadwalPiketController::class, 'me']);
    Route::post('/jadwal-piket', [JadwalPiketController::class, 'store']);
    Route::delete('/jadwal-piket/{id}', [JadwalPiketController::class, 'destroy']);

    // Master Sanksi
    Route::get('/sanksi-master', [SanksiController::class, 'indexMaster']);
    Route::post('/sanksi-master', [SanksiController::class, 'storeMaster']);
    Route::delete('/sanksi-master/{id}', [SanksiController::class, 'destroyMaster']);

    // Sanksi Siswa
    Route::get('/sanksi-siswa', [SanksiController::class, 'indexSiswa']);
    Route::get('/sanksi-siswa/me', [SanksiController::class, 'meSiswa']);
    Route::post('/sanksi-siswa', [SanksiController::class, 'storeSiswa']);
    Route::patch('/sanksi-siswa/{id}/status', [SanksiController::class, 'updateStatusSiswa']);
});