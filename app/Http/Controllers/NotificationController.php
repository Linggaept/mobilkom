<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        auth()->user()->unreadNotifications->markAsRead();
        return view('components.notifications', compact('notifications'));
    }

    public function markRead(Request $request)
    {
        if ($request->id) {
            $notif = auth()->user()->notifications()->find($request->id);
            if ($notif) $notif->markAsRead();
        } else {
            auth()->user()->unreadNotifications->markAsRead();
        }
        return response()->json(['success' => true]);
    }

    public function getUnread()
    {
        $unread = auth()->user()->unreadNotifications->take(10)->map(function ($n) {
            return [
                'id'         => $n->id,
                'data'       => $n->data,
                'created_at' => $n->created_at->diffForHumans(),
            ];
        });
        return response()->json([
            'count'         => auth()->user()->unreadNotifications->count(),
            'notifications' => $unread,
        ]);
    }
}