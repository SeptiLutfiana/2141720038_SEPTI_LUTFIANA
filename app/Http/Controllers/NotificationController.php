<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAllAsRead(Request $request)
    {
        $roleMap = [
            1 => 'adminsdm',
            2 => 'supervisor',
            3 => 'mentor',
            4 => 'karyawan',
        ];

        $user = Auth::user();
        $role = session('active_role');
        $untukRole = $roleMap[$role] ?? 'karyawan';

        // Ambil notifikasi yang sesuai role
        $filtered = $user->unreadNotifications->filter(function ($notification) use ($untukRole) {
            return isset($notification->data['untuk_role']) && $notification->data['untuk_role'] === $untukRole;
        });
        $filtered->each->markAsRead();

        return back()->with('msg-success', 'Semua notifikasi untuk peran ' . ucfirst($untukRole) . ' telah ditandai dibaca.');
    }
}
