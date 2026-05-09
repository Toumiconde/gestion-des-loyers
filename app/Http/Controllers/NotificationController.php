<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead(Request $request)
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        Auth::user()->unreadNotifications->markAsRead(); // Mark as read when viewing list
        return view('notifications.index', compact('notifications'));
    }
}
