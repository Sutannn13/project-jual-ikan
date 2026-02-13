<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $oldStatus,
        public string $newStatus,
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->newStatus) {
            'pending'          => "Pesanan {$this->order->order_number} - Menunggu Pembayaran",
            'waiting_payment'  => "Pesanan {$this->order->order_number} - Bukti Pembayaran Diterima",
            'paid'             => "Pesanan {$this->order->order_number} - Pembayaran Diverifikasi",
            'confirmed'        => "Pesanan {$this->order->order_number} - Pesanan Dikonfirmasi",
            'out_for_delivery' => "Pesanan {$this->order->order_number} - Dalam Pengiriman",
            'completed'        => "Pesanan {$this->order->order_number} - Pesanan Selesai",
            'cancelled'        => "Pesanan {$this->order->order_number} - Pesanan Dibatalkan",
            default            => "Update Pesanan {$this->order->order_number}",
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order-status');
    }
}
