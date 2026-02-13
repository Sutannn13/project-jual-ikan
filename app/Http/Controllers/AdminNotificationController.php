<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    /**
     * Halaman semua notifikasi (full page)
     */
    public function index(Request $request)
    {
        $query = AdminNotification::latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->get('filter') === 'unread') {
            $query->where('is_read', false);
        }

        $notifications = $query->paginate(20)->withQueryString();

        $typeCounts = [
            'all'              => AdminNotification::count(),
            'unread'           => AdminNotification::where('is_read', false)->count(),
            'new_order'        => AdminNotification::where('type', 'new_order')->where('is_read', false)->count(),
            'payment_uploaded' => AdminNotification::where('type', 'payment_uploaded')->where('is_read', false)->count(),
            'new_chat'         => AdminNotification::where('type', 'new_chat')->where('is_read', false)->count(),
            'low_stock'        => AdminNotification::where('type', 'low_stock')->where('is_read', false)->count(),
        ];

        return view('admin.notifications.index', compact('notifications', 'typeCounts'));
    }

    /**
     * API: Get unread notifications (untuk bell dropdown)
     */
    public function getUnread()
    {
        $notifications = AdminNotification::unread()->take(15)->get();

        return response()->json([
            'count'        => AdminNotification::unreadCount(),
            'urgent_count' => AdminNotification::urgentCount(),
            'notifications'=> $notifications->map(function ($n) {
                return [
                    'id'          => $n->id,
                    'type'        => $n->type,
                    'priority'    => $n->priority,
                    'title'       => $n->title,
                    'message'     => $n->message,
                    'icon'        => $n->resolved_icon,
                    'color'       => $n->resolved_color,
                    'action_url'  => $n->action_url,
                    'action_text' => $n->action_text,
                    'created_at'  => $n->created_at->diffForHumans(),
                    'time_short'  => $n->created_at->format('H:i'),
                ];
            }),
        ]);
    }

    /**
     * Mark single notification as read & redirect
     */
    public function markAsRead($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['status' => 'success']);
        }

        // Redirect ke action_url jika ada
        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return back();
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead()
    {
        AdminNotification::markAllAsRead();

        if (request()->expectsJson()) {
            return response()->json(['status' => 'success']);
        }

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        AdminNotification::findOrFail($id)->delete();

        if (request()->expectsJson()) {
            return response()->json(['status' => 'success']);
        }

        return back()->with('success', 'Notifikasi dihapus.');
    }

    /**
     * Delete all read notifications
     */
    public function clearRead()
    {
        AdminNotification::where('is_read', true)->delete();

        return back()->with('success', 'Notifikasi yang sudah dibaca berhasil dihapus.');
    }
}
