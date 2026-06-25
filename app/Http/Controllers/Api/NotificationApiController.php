<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(20);
        return response()->json($notifications);
    }

    public function unread(Request $request)
    {
        return response()->json([
            'count' => $request->user()->unreadNotifications()->count(),
            'notifications' => $request->user()->unreadNotifications()->take(10)->get(),
        ]);
    }

    public function markRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['message' => 'Ditandakan dibaca.']);
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'Semua ditandakan dibaca.']);
    }
}
