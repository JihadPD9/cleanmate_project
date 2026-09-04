<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\BuktiPiketController;
use App\Http\Controllers\Api\JadwalPiketController;
use App\Http\Controllers\Api\SanksiController;
use App\Http\Controllers\Api\NotificationController;

/*
|--------------------------------------------------------------------------
| Public Routes (Login)
|--------------------------------------------------------------------------
*/
Route::post('/admin/login', [AuthController::class, 'loginAdmin']);
Route::post('/siswa/login', [AuthController::class, 'loginSiswa']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Common Routes (Bisa diakses Admin & Siswa)
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Fitur Khusus ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Master Tasks (CRUD)
        Route::apiResource('tasks', TaskController::class);

        // Management Bukti Piket
        Route::get('/bukti-piket', [BuktiPiketController::class, 'index']);
        Route::patch('/bukti-piket/{id}/status', [BuktiPiketController::class, 'updateStatus']);

        // Management Jadwal Piket
        Route::get('/jadwal-piket', [JadwalPiketController::class, 'index']);
        Route::post('/jadwal-piket', [JadwalPiketController::class, 'store']);
        Route::delete('/jadwal-piket/{id}', [JadwalPiketController::class, 'destroy']);

        // Master Sanksi
        Route::get('/sanksi-master', [SanksiController::class, 'indexMaster']);
        Route::post('/sanksi-master', [SanksiController::class, 'storeMaster']);
        Route::put('/sanksi-master/{id}', [SanksiController::class, 'updateMaster']); // Edit Master Sanksi
        Route::delete('/sanksi-master/{id}', [SanksiController::class, 'destroyMaster']);

        // Management Sanksi Siswa
        Route::get('/sanksi-siswa', [SanksiController::class, 'indexSiswa']);
        Route::post('/sanksi-siswa', [SanksiController::class, 'storeSiswa']);
        Route::put('/sanksi-siswa/{id}', [SanksiController::class, 'updateSiswa']); // Edit Sanksi Siswa
        Route::delete('/sanksi-siswa/{id}', [SanksiController::class, 'destroySiswa']); // Hapus Sanksi Siswa
        Route::patch('/sanksi-siswa/{id}/status', [SanksiController::class, 'updateStatusSiswa']);
    });

    /*
    |--------------------------------------------------------------------------
    | Fitur Khusus SISWA
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:siswa')->prefix('siswa')->group(function () {
        // Task List (Siswa hanya bisa lihat daftar tugas)
        Route::get('/tasks', [TaskController::class, 'index']);

        // Bukti Piket
        Route::get('/bukti-piket', [BuktiPiketController::class, 'me']);
        Route::post('/bukti-piket', [BuktiPiketController::class, 'store']);

        // Jadwal Piket
        Route::get('/jadwal-piket', [JadwalPiketController::class, 'me']);
        Route::get('/jadwal-piket/kelas', [JadwalPiketController::class, 'index']); // Melihat jadwal sekelas

        // Sanksi Saya
        Route::get('/sanksi-siswa', [SanksiController::class, 'meSiswa']);

        // Inbox / Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    });

});