<?php

namespace App\Mail;

use App\Models\Produk;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductBackInStockMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Produk $produk,
        public User $user,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🎉 {$this->produk->nama} Kembali Tersedia! - FishMarket"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.product-back-in-stock');
    }
}
