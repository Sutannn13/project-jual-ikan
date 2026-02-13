@component('mail::message')
# Update Status Pesanan

Halo **{{ $order->user->name }}**,

Status pesanan Anda telah diperbarui:

---

**Nomor Pesanan:** {{ $order->order_number }}

**Status Sebelumnya:** {{ match($oldStatus) {
    'pending' => 'Menunggu Pembayaran',
    'waiting_payment' => 'Menunggu Verifikasi',
    'paid' => 'Pembayaran Dikonfirmasi',
    'confirmed' => 'Pesanan Dikonfirmasi',
    'out_for_delivery' => 'Dalam Pengiriman',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan',
    default => $oldStatus,
} }}

**Status Baru:** {{ match($newStatus) {
    'pending' => 'Menunggu Pembayaran',
    'waiting_payment' => 'Menunggu Verifikasi',
    'paid' => 'Pembayaran Dikonfirmasi',
    'confirmed' => 'Pesanan Dikonfirmasi',
    'out_for_delivery' => 'Dalam Pengiriman',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan',
    default => $newStatus,
} }}

**Total:** Rp {{ number_format($order->total_price, 0, ',', '.') }}

---

@if($newStatus === 'pending' && $order->rejection_reason)
**⚠️ Alasan Penolakan:** {{ $order->rejection_reason }}

Silakan upload ulang bukti pembayaran yang benar.
@endif

@if($newStatus === 'paid')
Pembayaran Anda telah berhasil diverifikasi. Pesanan akan segera diproses.
@endif

@if($newStatus === 'confirmed')
Pesanan Anda telah dikonfirmasi dan sedang disiapkan untuk pengiriman.

@if($order->delivery_note)
**Catatan Pengiriman:** {{ $order->delivery_note }}
@endif
@if($order->courier_name)
**Kurir:** {{ $order->courier_name }} ({{ $order->courier_phone }})
@endif
@endif

@if($newStatus === 'out_for_delivery')
Pesanan Anda sedang dalam perjalanan ke alamat Anda!

@if($order->tracking_number)
**Nomor Resi:** {{ $order->tracking_number }}
@endif
@endif

@if($newStatus === 'completed')
Terima kasih telah berbelanja di FishMarket! 🐟

Jangan lupa berikan review untuk produk yang Anda beli.
@endif

@if($newStatus === 'cancelled')
Pesanan Anda telah dibatalkan. Jika ini tidak sesuai, silakan hubungi admin kami.
@endif

@component('mail::button', ['url' => route('order.track', $order)])
Lacak Pesanan
@endcomponent

Terima kasih,<br>
**FishMarket**
@endcomponent
