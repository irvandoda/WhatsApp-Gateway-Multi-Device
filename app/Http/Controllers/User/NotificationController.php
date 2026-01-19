<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsSeen()
    {
        $user = Auth::user();
		$user->notification_seen = true;
		$user->save();

        return response()->json(['success' => true]);
    }
}
