<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('is_admin', true)
            ->latest()
            ->paginate(10);

        return view('admin.notification', compact('notifications'));
    }

    public function read(Notification $notification)
    {
        $notification->update(['is_viewed' => true]);

        return redirect()->route('admin.booking.show', $notification->trip_id);
    }

    public function clientRead(Notification $notification)
    {
        $notification->update(['is_viewed' => true]);
        return redirect()->back();
    }
}
