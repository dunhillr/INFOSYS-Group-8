<?php

namespace App\Http\Controllers;

use App\Models\SystemNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = SystemNotification::where('user_id', Auth::id())->latest()->paginate(10);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(SystemNotification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === Auth::id(), 403);
        $notification->update(['is_read' => true]);
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(): RedirectResponse
    {
        SystemNotification::where('user_id', Auth::id())->where('is_read', false)->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }
}
