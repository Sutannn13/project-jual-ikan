<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SupportTicketController extends Controller
{
    /**
     * Customer: List tiket saya
     */
    public function index()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())
            ->with(['order', 'latestMessage'])
            ->latest()
            ->paginate(10);

        return view('store.tickets.index', compact('tickets'));
    }

    /**
     * Customer: Form buat tiket baru
     */
    public function create(Request $request)
    {
        $orders = Auth::user()->orders()->latest()->take(20)->get();
        $selectedOrderId = $request->get('order_id');
        return view('store.tickets.create', compact('orders', 'selectedOrderId'));
    }

    /**
     * Customer: Simpan tiket baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject'    => 'required|string|max:255',
            'category'   => 'required|in:order_issue,payment,product_quality,delivery,other',
            'order_id'   => 'nullable|exists:orders,id',
            'message'    => 'required|string|max:2000',
            'priority'   => 'nullable|in:low,medium,high',
            'attachment'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        // Verify order ownership if order_id provided
        if ($request->order_id) {
            $order = Auth::user()->orders()->find($request->order_id);
            if (!$order) {
                return back()->with('error', 'Order tidak ditemukan.');
            }
        }

        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id'       => Auth::id(),
            'order_id'      => $request->order_id,
            'subject'       => $request->subject,
            'category'      => $request->category,
            'priority'      => $request->priority ?? 'medium',
            'status'        => 'open',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('ticket_attachments', 'public');
        }

        TicketMessage::create([
            'ticket_id'  => $ticket->id,
            'user_id'    => Auth::id(),
            'message'    => $request->message,
            'is_admin'   => false,
            'attachment'  => $attachmentPath,
            'created_at' => now(),
        ]);

        // Notify admin
        try {
            AdminNotificationService::create(
                'ticket',
                "Tiket support baru #{$ticket->ticket_number}: {$ticket->subject}",
                $ticket->id,
                SupportTicket::class
            );
        } catch (\Exception $e) {}

        return redirect()->route('tickets.show', $ticket)->with('success', 'Tiket berhasil dibuat! Tim kami akan segera merespons.');
    }

    /**
     * Customer: Lihat detail tiket
     */
    public function show(SupportTicket $ticket)
    {
        if ($ticket->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $ticket->load(['messages.user', 'order', 'assignedAdmin']);

        return view('store.tickets.show', compact('ticket'));
    }

    /**
     * Customer: Balas tiket
     */
    public function reply(Request $request, SupportTicket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        if (in_array($ticket->status, ['closed', 'resolved'])) {
            return back()->with('error', 'Tiket sudah ditutup, tidak bisa dibalas.');
        }

        $request->validate([
            'message'    => 'required|string|max:2000',
            'attachment'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('ticket_attachments', 'public');
        }

        TicketMessage::create([
            'ticket_id'  => $ticket->id,
            'user_id'    => Auth::id(),
            'message'    => $request->message,
            'is_admin'   => false,
            'attachment'  => $attachmentPath,
            'created_at' => now(),
        ]);

        // Update status to open if it was waiting_customer
        if ($ticket->status === 'waiting_customer') {
            $ticket->update(['status' => 'open']);
        }

        // Notify admin
        try {
            AdminNotificationService::create(
                'ticket_reply',
                "Balasan tiket #{$ticket->ticket_number} dari {$ticket->user->name}",
                $ticket->id,
                SupportTicket::class
            );
        } catch (\Exception $e) {}

        return back()->with('success', 'Balasan terkirim!');
    }

    // ================================================================
    // ADMIN METHODS
    // ================================================================

    /**
     * Admin: List semua tiket
     */
    public function adminIndex(Request $request)
    {
        $query = SupportTicket::with(['user', 'order', 'assignedAdmin', 'latestMessage']);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $tickets = $query->latest()->paginate(15);

        $statusCounts = [
            'open'             => SupportTicket::where('status', 'open')->count(),
            'in_progress'      => SupportTicket::where('status', 'in_progress')->count(),
            'waiting_customer' => SupportTicket::where('status', 'waiting_customer')->count(),
            'resolved'         => SupportTicket::where('status', 'resolved')->count(),
            'closed'           => SupportTicket::where('status', 'closed')->count(),
        ];

        return view('admin.tickets.index', compact('tickets', 'statusCounts'));
    }

    /**
     * Admin: Lihat detail tiket
     */
    public function adminShow(SupportTicket $ticket)
    {
        $ticket->load(['messages.user', 'order.items.produk', 'user', 'assignedAdmin']);
        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * Admin: Balas tiket
     */
    public function adminReply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message'    => 'required|string|max:2000',
            'attachment'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'status'     => 'nullable|in:open,in_progress,waiting_customer,resolved,closed',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('ticket_attachments', 'public');
        }

        TicketMessage::create([
            'ticket_id'  => $ticket->id,
            'user_id'    => Auth::id(),
            'message'    => $request->message,
            'is_admin'   => true,
            'attachment'  => $attachmentPath,
            'created_at' => now(),
        ]);

        // Update status & assign
        $updateData = ['assigned_to' => Auth::id()];
        if ($request->filled('status')) {
            $updateData['status'] = $request->status;
            if ($request->status === 'closed' || $request->status === 'resolved') {
                $updateData['closed_at'] = now();
            }
        } else {
            $updateData['status'] = 'waiting_customer';
        }
        $ticket->update($updateData);

        return back()->with('success', 'Balasan terkirim ke customer!');
    }

    /**
     * Admin: Update status tiket
     */
    public function adminUpdateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,waiting_customer,resolved,closed',
        ]);

        $updateData = ['status' => $request->status];
        if (in_array($request->status, ['closed', 'resolved'])) {
            $updateData['closed_at'] = now();
        }

        $ticket->update($updateData);

        return back()->with('success', "Status tiket diubah ke {$ticket->status_label}.");
    }
}
