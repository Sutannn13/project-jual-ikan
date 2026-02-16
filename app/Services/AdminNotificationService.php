<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Produk;
use App\Models\Review;
use App\Models\ChatMessage;
use App\Models\User;

class AdminNotificationService
{
    // ═══════════════════════════════════════
    //  NOTIFICATION CREATORS
    // ═══════════════════════════════════════

    /**
     * Order baru dibuat oleh customer
     */
    public static function orderCreated(Order $order): void
    {
        AdminNotification::create([
            'type'         => 'new_order',
            'priority'     => 'medium',
            'title'        => 'Pesanan Baru',
            'message'      => "Pesanan #{$order->order_number} dari {$order->user->name} — Rp " . number_format($order->total_price, 0, ',', '.'),
            'icon'         => 'fas fa-shopping-cart',
            'color'        => 'cyan',
            'related_id'   => $order->id,
            'related_type' => Order::class,
            'action_url'   => route('admin.orders.show', $order),
            'action_text'  => 'Lihat Pesanan',
        ]);

        ActivityLog::log('created', "Pesanan baru #{$order->order_number} dibuat oleh {$order->user->name}", 'Order', $order->id);
    }

    /**
     * Customer upload bukti bayar — URGENT!
     */
    public static function paymentUploaded(Order $order): void
    {
        AdminNotification::create([
            'type'         => 'payment_uploaded',
            'priority'     => 'urgent',
            'title'        => 'Verifikasi Pembayaran!',
            'message'      => "Pesanan #{$order->order_number} mengunggah bukti bayar. Segera verifikasi!",
            'icon'         => 'fas fa-money-bill-wave',
            'color'        => 'red',
            'related_id'   => $order->id,
            'related_type' => Order::class,
            'action_url'   => route('admin.orders.show', $order),
            'action_text'  => 'Verifikasi',
        ]);

        ActivityLog::log('uploaded', "Bukti pembayaran pesanan #{$order->order_number} diupload oleh {$order->user->name}", 'Order', $order->id);
    }

    /**
     * Admin verifikasi pembayaran
     */
    public static function paymentVerified(Order $order): void
    {
        ActivityLog::log('verified', "Pembayaran pesanan #{$order->order_number} diverifikasi", 'Order', $order->id);
    }

    /**
     * Admin tolak pembayaran
     */
    public static function paymentRejected(Order $order, string $reason): void
    {
        ActivityLog::log('rejected', "Pembayaran pesanan #{$order->order_number} ditolak. Alasan: {$reason}", 'Order', $order->id);
    }

    /**
     * Admin konfirmasi pesanan
     */
    public static function orderConfirmed(Order $order): void
    {
        ActivityLog::log('confirmed', "Pesanan #{$order->order_number} dikonfirmasi & siap dikirim", 'Order', $order->id);
    }

    /**
     * Status order berubah
     */
    public static function orderStatusChanged(Order $order, string $oldStatus, string $newStatus): void
    {
        ActivityLog::log(
            'status_changed',
            "Status pesanan #{$order->order_number} diubah: {$oldStatus} → {$newStatus}",
            'Order',
            $order->id,
            ['old_status' => $oldStatus, 'new_status' => $newStatus]
        );
    }

    /**
     * Customer batalkan order
     */
    public static function orderCancelled(Order $order): void
    {
        $cancelledBy = auth()->user();
        $isCustomer = $cancelledBy && $cancelledBy->role !== 'admin';

        AdminNotification::create([
            'type'         => 'order_cancelled',
            'priority'     => 'medium',
            'title'        => 'Pesanan Dibatalkan',
            'message'      => "Pesanan #{$order->order_number} dibatalkan oleh " . ($isCustomer ? $order->user->name : 'Admin'),
            'icon'         => 'fas fa-times-circle',
            'color'        => 'orange',
            'related_id'   => $order->id,
            'related_type' => Order::class,
            'action_url'   => route('admin.orders.show', $order),
            'action_text'  => 'Lihat Detail',
        ]);

        ActivityLog::log('cancelled', "Pesanan #{$order->order_number} dibatalkan oleh " . ($isCustomer ? $order->user->name : 'Admin'), 'Order', $order->id);
    }

    /**
     * Chat baru dari customer ke admin
     */
    public static function newChat(ChatMessage $message): void
    {
        // Hanya notif jika pengirim bukan admin
        $sender = $message->sender;
        if ($sender && $sender->role === 'admin') return;

        // Hindari spam notifikasi — cek jika ada notif chat unread dari user yang sama dalam 5 menit terakhir
        $recentExists = AdminNotification::where('type', 'new_chat')
            ->where('is_read', false)
            ->where('related_type', User::class)
            ->where('related_id', $sender->id)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();

        if ($recentExists) return;

        AdminNotification::create([
            'type'         => 'new_chat',
            'priority'     => 'medium',
            'title'        => 'Pesan Baru',
            'message'      => "{$sender->name}: " . \Illuminate\Support\Str::limit($message->message, 60),
            'icon'         => 'fas fa-comments',
            'color'        => 'green',
            'related_id'   => $sender->id,
            'related_type' => User::class,
            'action_url'   => route('admin.chat.show', $sender),
            'action_text'  => 'Balas',
        ]);
    }

    /**
     * Review baru ditambahkan customer
     */
    public static function newReview(Review $review): void
    {
        $review->load(['user', 'produk']);

        AdminNotification::create([
            'type'         => 'new_review',
            'priority'     => 'low',
            'title'        => 'Review Baru',
            'message'      => "{$review->user->name} beri {$review->rating}★ untuk {$review->produk->nama}",
            'icon'         => 'fas fa-star',
            'color'        => 'yellow',
            'related_id'   => $review->id,
            'related_type' => Review::class,
            'action_url'   => route('produk.show', $review->produk),
            'action_text'  => 'Lihat',
        ]);

        ActivityLog::log('created', "{$review->user->name} memberikan review {$review->rating}★ untuk {$review->produk->nama}", 'Review', $review->id);
    }

    /**
     * Stok produk menipis
     */
    public static function lowStock(Produk $produk): void
    {
        // Hindari duplikasi — cek notif low_stock unread untuk produk yang sama
        $exists = AdminNotification::where('type', 'low_stock')
            ->where('is_read', false)
            ->where('related_id', $produk->id)
            ->where('related_type', Produk::class)
            ->exists();

        if ($exists) return;

        AdminNotification::create([
            'type'         => 'low_stock',
            'priority'     => 'high',
            'title'        => 'Stok Menipis!',
            'message'      => "Stok {$produk->nama} tinggal {$produk->stok} Kg. Segera restock!",
            'icon'         => 'fas fa-exclamation-triangle',
            'color'        => 'orange',
            'related_id'   => $produk->id,
            'related_type' => Produk::class,
            'action_url'   => route('admin.produk.edit', $produk),
            'action_text'  => 'Update Stok',
        ]);

        ActivityLog::log('low_stock', "Stok {$produk->nama} menipis: {$produk->stok} Kg tersisa", 'Produk', $produk->id);
    }

    /**
     * Stok produk habis
     */
    public static function stockOut(Produk $produk): void
    {
        $exists = AdminNotification::where('type', 'stock_out')
            ->where('is_read', false)
            ->where('related_id', $produk->id)
            ->where('related_type', Produk::class)
            ->exists();

        if ($exists) return;

        AdminNotification::create([
            'type'         => 'stock_out',
            'priority'     => 'urgent',
            'title'        => 'Stok Habis!',
            'message'      => "Stok {$produk->nama} sudah HABIS! Produk tidak bisa dibeli.",
            'icon'         => 'fas fa-box-open',
            'color'        => 'red',
            'related_id'   => $produk->id,
            'related_type' => Produk::class,
            'action_url'   => route('admin.produk.edit', $produk),
            'action_text'  => 'Restock',
        ]);
    }

    // ═══════════════════════════════════════
    //  ACTIVITY LOG SHORTCUTS
    // ═══════════════════════════════════════

    public static function logProdukCreated(Produk $produk): void
    {
        ActivityLog::log('created', "Produk baru '{$produk->nama}' ditambahkan (Stok: {$produk->stok} Kg)", 'Produk', $produk->id);
    }

    public static function logProdukUpdated(Produk $produk, array $changes): void
    {
        ActivityLog::log('updated', "Produk '{$produk->nama}' diperbarui", 'Produk', $produk->id, $changes);
    }

    public static function logProdukDeleted(Produk $produk): void
    {
        ActivityLog::log('deleted', "Produk '{$produk->nama}' diarsipkan (soft deleted). Riwayat pesanan aman.", 'Produk', $produk->id);
    }

    public static function logUserDeleted(User $user): void
    {
        ActivityLog::log('deleted', "User '{$user->name}' ({$user->email}) dihapus", 'User', $user->id);
    }

    public static function logPasswordReset(User $user): void
    {
        ActivityLog::log('updated', "Password user '{$user->name}' direset", 'User', $user->id);
    }

    /**
     * Generic notification creator
     */
    public static function create(string $type, string $message, ?int $relatedId = null, ?string $relatedType = null): void
    {
        AdminNotification::create([
            'type'         => $type,
            'priority'     => 'medium',
            'title'        => ucfirst(str_replace('_', ' ', $type)),
            'message'      => $message,
            'icon'         => 'fas fa-info-circle',
            'color'        => 'cyan',
            'related_id'   => $relatedId,
            'related_type' => $relatedType,
        ]);
    }
}
