<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Mengambil semua pesan inbox milik siswa yang sedang login
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications;

        return response()->json([
            'unread_count' => $request->user()->unreadNotifications->count(),
            'data'         => $notifications
        ]);
    }

    // Tandai pesan sebagai 'sudah dibaca'
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'message' => 'Pesan ditandai sebagai dibaca'
        ]);
    }

    // Tandai semua pesan sebagai 'sudah dibaca'
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'Semua pesan ditandai sebagai dibaca'
        ]);
    }
}