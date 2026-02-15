<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AdminNotificationService;

class ChatController extends Controller
{
    /**
     * Customer: Show chat page with admin
     */
    public function customerChat()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            return back()->with('error', 'Admin belum tersedia untuk chat.');
        }

        // Paginate messages (latest 50)
        $messages = ChatMessage::conversation(Auth::id(), $admin->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse();

        // Mark admin's messages as read
        ChatMessage::markAsRead($admin->id, Auth::id());

        return view('store.chat', compact('messages', 'admin'));
    }

    /**
     * Customer: Send message to admin
     */
    public function customerSend(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            return response()->json(['error' => 'Admin tidak tersedia.'], 404);
        }
        
        // Sanitize message to prevent XSS
        $sanitizedMessage = strip_tags($request->message);

        $message = ChatMessage::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $admin->id,
            'message'     => $sanitizedMessage,
        ]);

        // Broadcast via WebSocket (Reverb) — graceful fallback to polling
        try {
            event(new MessageSent($message));
        } catch (\Exception $e) {
            // Silently fail — polling akan tetap bekerja sebagai fallback
        }

        // Notify admin about new chat
        try {
            AdminNotificationService::newChat($message);
        } catch (\Exception $e) {
            // Silently fail — chat should still work
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id'         => $message->id,
                'message'    => $message->message,
                'sender_id'  => $message->sender_id,
                'created_at' => $message->created_at->format('H:i'),
                'is_mine'    => true,
            ],
        ]);
    }

    /**
     * Customer: Poll for new messages (AJAX)
     */
    public function customerPoll(Request $request)
    {
        $lastId = $request->get('last_id', 0);
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            return response()->json(['messages' => []]);
        }

        $messages = ChatMessage::conversation(Auth::id(), $admin->id)
            ->where('id', '>', $lastId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id'         => $msg->id,
                    'message'    => $msg->message,
                    'sender_id'  => $msg->sender_id,
                    'created_at' => $msg->created_at->format('H:i'),
                    'is_mine'    => $msg->sender_id === Auth::id(),
                ];
            });

        // Mark admin messages as read
        ChatMessage::markAsRead($admin->id, Auth::id());

        return response()->json(['messages' => $messages]);
    }

    /**
     * Admin: Show list of conversations
     */
    public function adminIndex()
    {
        $adminId = Auth::id();
        
        // Get all messages involving admin, ordered by latest first
        $allMessages = ChatMessage::where(function ($q) use ($adminId) {
                $q->where('sender_id', $adminId)
                  ->orWhere('receiver_id', $adminId);
            })
            ->orderByDesc('created_at')
            ->get();
        
        // Group by customer (the other person in conversation)
        $conversationsData = [];
        
        foreach ($allMessages as $message) {
            $customerId = $message->sender_id === $adminId 
                ? $message->receiver_id 
                : $message->sender_id;
            
            // Only keep the latest message per customer
            if (!isset($conversationsData[$customerId])) {
                $conversationsData[$customerId] = (object) [
                    'customer_id' => $customerId,
                    'last_message_at' => $message->created_at,
                    'last_message' => $message,
                ];
            }
        }
        
        // Sort by latest message
        $conversations = collect($conversationsData)->sortByDesc('last_message_at')->values();
        
        // Get customer details
        $customerIds = $conversations->pluck('customer_id');
        $customers = User::whereIn('id', $customerIds)->get()->keyBy('id');
        
        // Get unread counts
        $unreadCounts = ChatMessage::where('receiver_id', $adminId)
            ->where('is_read', false)
            ->selectRaw('sender_id, COUNT(*) as count')
            ->groupBy('sender_id')
            ->pluck('count', 'sender_id');
        
        return view('admin.chat.index', compact('conversations', 'customers', 'unreadCounts'));
    }

    /**
     * Admin: Show conversation with a specific customer
     */
    public function adminChat(User $user)
    {
        // Paginate messages (latest 50)
        $messages = ChatMessage::conversation(Auth::id(), $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse();

        // Mark customer's messages as read
        ChatMessage::markAsRead($user->id, Auth::id());

        return view('admin.chat.show', compact('messages', 'user'));
    }

    /**
     * Admin: Send message to customer
     */
    public function adminSend(Request $request, User $user)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);
        
        // Sanitize message to prevent XSS
        $sanitizedMessage = strip_tags($request->message);

        $message = ChatMessage::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $user->id,
            'message'     => $sanitizedMessage,
        ]);

        // Broadcast via WebSocket (Reverb)
        try {
            event(new MessageSent($message));
        } catch (\Exception $e) {
            // Silently fail — polling fallback
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id'         => $message->id,
                'message'    => $message->message,
                'sender_id'  => $message->sender_id,
                'created_at' => $message->created_at->format('H:i'),
                'is_mine'    => true,
            ],
        ]);
    }

    /**
     * Admin: Poll for new messages from customer (AJAX)
     */
    public function adminPoll(Request $request, User $user)
    {
        $lastId = $request->get('last_id', 0);

        $messages = ChatMessage::conversation(Auth::id(), $user->id)
            ->where('id', '>', $lastId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id'         => $msg->id,
                    'message'    => $msg->message,
                    'sender_id'  => $msg->sender_id,
                    'created_at' => $msg->created_at->format('H:i'),
                    'is_mine'    => $msg->sender_id === Auth::id(),
                ];
            });

        // Mark customer messages as read
        ChatMessage::markAsRead($user->id, Auth::id());

        return response()->json(['messages' => $messages]);
    }

    /**
     * Get unread count for navbar badge
     */
    public function unreadCount()
    {
        $count = ChatMessage::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
