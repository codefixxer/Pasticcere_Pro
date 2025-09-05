<?php
// In the NotificationController

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Fetch unread notifications for the logged-in user
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())  // Filter by logged-in user's ID
            ->where('is_read', false)  // Fetch only unread notifications
            ->latest()
            ->get();

        return view('frontend.notifications.index', compact('notifications'));
    }

    // Mark a specific notification as read
 // In NotificationController.php

// In NotificationController.php

public function markAsRead($notificationId)
{
    // Ensure notification belongs to the logged-in user
    $notification = Notification::where('user_id', Auth::id())
                                ->where('id', $notificationId)
                                ->firstOrFail();  // If notification doesn't exist, throw error
    
    // Mark as read and update status
    $notification->update(['is_read' => true]);

    // Redirect to the blogs page
    return redirect()->route('blogs');
}



// In NotificationController.php

public function markAllAsRead()
{
    // Mark all unread notifications for the logged-in user as read
    Notification::where('user_id', Auth::id())
                ->where('is_read', false)  // Only mark unread notifications
                ->update(['is_read' => true]);

    // Redirect to the blogs page
    return redirect()->route('blogs');
}


    // Fetch unread notifications for the logged-in user
    public function unread(Request $request)
    {
        $userId = Auth::id();
        $notifications = Notification::where('user_id', $userId)  // Ensure notifications are for the logged-in user
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'message', 'created_at']);

        return response()->json([
            'count' => $notifications->count(),
            'items' => $notifications->map(fn($n) => [
                'id'      => $n->id,
                'title'   => $n->title,
                'message' => $n->message,
                'time'    => $n->created_at->diffForHumans(),
            ]),
        ]);
    }
}
