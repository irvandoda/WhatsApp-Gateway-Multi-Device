<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;

class NotificationController extends Controller
{
    public function index()
    {
        $notification = Notification::latest()->first();
        return view('theme::pages.admin.notifications.index', compact('notification'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:4000',
        ]);

        Notification::updateOrCreate([], [
            'message' => $request->message,
        ]);

        User::query()->update(['notification_seen' => false]);

        return redirect()->back()->with('alert', ['type' => 'success', 'msg' => __('Notification sent successfully!')]);
    }
}
