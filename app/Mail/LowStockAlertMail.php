<?php

namespace App\Mail;

use App\Models\Produk;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Produk $produk,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "⚠️ Stok Rendah: {$this->produk->nama} ({$this->produk->stok} Kg)"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.low-stock-alert');
    }
}
