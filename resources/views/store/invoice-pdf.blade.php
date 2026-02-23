<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; background: #fff; }
        .header { background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%); color: white; padding: 24px 32px; }
        .header h1 { font-size: 22px; font-weight: 800; letter-spacing: 1px; }
        .header p { font-size: 10px; opacity: 0.85; margin-top: 2px; }
        .header-right { float: right; text-align: right; }
        .header-right .invoice-num { font-size: 16px; font-weight: 700; }
        .header::after { content: ''; display: table; clear: both; }
        .content { padding: 24px 32px; }
        .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #0891b2; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; margin-bottom: 10px; margin-top: 16px; }
        .info-grid { display: table; width: 100%; }
        .info-col { display: table-cell; width: 50%; vertical-align: top; padding-right: 16px; }
        .info-col:last-child { padding-right: 0; padding-left: 16px; }
        .info-row { margin-bottom: 5px; }
        .info-label { font-size: 10px; color: #6b7280; font-weight: 600; }
        .info-value { font-size: 11px; color: #111827; font-weight: 500; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead tr { background: #0891b2; color: white; }
        thead th { padding: 8px 10px; text-align: left; font-size: 10px; font-weight: 700; letter-spacing: 0.5px; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody tr td { padding: 8px 10px; font-size: 11px; border-bottom: 1px solid #f3f4f6; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals { margin-top: 12px; float: right; min-width: 240px; }
        .total-row { display: table; width: 100%; margin-bottom: 4px; }
        .total-label { display: table-cell; font-size: 11px; color: #6b7280; padding-right: 12px; }
        .total-value { display: table-cell; font-size: 11px; color: #111827; text-align: right; font-weight: 500; }
        .grand-total { border-top: 2px solid #0891b2; padding-top: 6px; margin-top: 6px; }
        .grand-total .total-label { font-size: 13px; font-weight: 700; color: #111827; }
        .grand-total .total-value { font-size: 13px; font-weight: 800; color: #0891b2; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef9c3; color: #713f12; }
        .status-cancelled { background: #fee2e2; color: #7f1d1d; }
        .status-default { background: #e0f2fe; color: #0c4a6e; }
        .footer { margin-top: 32px; padding: 16px 32px; border-top: 1px solid #e5e7eb; font-size: 10px; color: #9ca3af; text-align: center; }
        .clearfix::after { content: ''; display: table; clear: both; }
        .note-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 10px 14px; margin-top: 16px; font-size: 10px; color: #78350f; }
        .watermark-paid { position: fixed; top: 40%; left: 50%; transform: translate(-50%,-50%) rotate(-35deg); font-size: 70px; font-weight: 900; color: rgba(5,150,105,0.07); z-index: 0; letter-spacing: 8px; pointer-events: none; }
    </style>
</head>
<body>

@if($order->status === 'completed')
<div class="watermark-paid">LUNAS</div>
@endif

{{-- HEADER --}}
<div class="header">
    <div class="header-right">
        <div class="invoice-num">{{ $order->order_number }}</div>
        <div style="font-size:10px; opacity:0.8; margin-top:3px;">Tanggal: {{ $order->created_at->format('d M Y') }}</div>
        <div style="margin-top:6px;">
            @php
                $statusClass = match($order->status) {
                    'completed' => 'status-completed',
                    'pending', 'waiting_payment' => 'status-pending',
                    'cancelled' => 'status-cancelled',
                    default => 'status-default',
                };
            @endphp
            <span class="status-badge {{ $statusClass }}">{{ $order->status_label }}</span>
        </div>
    </div>
    <div>
        <h1>🐟 FishMarket</h1>
        <p>Invoice / Bukti Pesanan</p>
        <p style="margin-top:6px; font-size:9px;">Toko Ikan Segar Berkualitas</p>
    </div>
</div>

{{-- CONTENT --}}
<div class="content">

    {{-- INFO SECTION --}}
    <div class="section-title">Informasi Transaksi</div>
    <div class="info-grid">
        <div class="info-col">
            <div class="info-row">
                <div class="info-label">Pelanggan</div>
                <div class="info-value">{{ $order->user?->name ?? 'Guest' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $order->user?->email ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">No. HP</div>
                <div class="info-value">{{ $order->user?->no_hp ?? '-' }}</div>
            </div>
        </div>
        <div class="info-col">
            <div class="info-row">
                <div class="info-label">Tanggal Order</div>
                <div class="info-value">{{ $order->created_at->format('d M Y, H:i') }} WIB</div>
            </div>
            @if($order->payment_method)
            <div class="info-row">
                <div class="info-label">Metode Bayar</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</div>
            </div>
            @endif
            @if($order->shippingZone)
            <div class="info-row">
                <div class="info-label">Zona Pengiriman</div>
                <div class="info-value">{{ $order->shippingZone->nama }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- ALAMAT --}}
    @if($order->user?->alamat)
    <div class="info-row" style="margin-top:8px;">
        <div class="info-label">Alamat Pengiriman</div>
        <div class="info-value">{{ $order->user->alamat }}</div>
    </div>
    @endif

    {{-- ITEMS TABLE --}}
    <div class="section-title" style="margin-top: 20px;">Detail Produk</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Produk</th>
                <th class="text-center">Jumlah</th>
                <th class="text-right">Harga/Kg</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->nama_produk ?? $item->produk?->nama ?? 'N/A' }}</td>
                <td class="text-center">{{ $item->qty }} Kg</td>
                <td class="text-right">Rp {{ number_format($item->price_per_kg, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTALS --}}
    <div class="clearfix">
        <div class="totals">
            <div class="total-row">
                <span class="total-label">Subtotal:</span>
                <span class="total-value">Rp {{ number_format($order->total_price - ($order->shipping_cost ?? 0), 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Ongkos Kirim:</span>
                <span class="total-value">Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="total-row grand-total">
                <span class="total-label">Total Pembayaran:</span>
                <span class="total-value">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- NOTE --}}
    <div class="note-box" style="margin-top: 60px;">
        <strong>Catatan:</strong> Invoice ini merupakan bukti pesanan resmi dari FishMarket.
        Simpan dokumen ini sebagai bukti transaksi Anda.
        @if($order->status !== 'completed')
        Pesanan ini <strong>belum selesai</strong> — status saat ini: {{ $order->status_label }}.
        @endif
    </div>

</div>

{{-- FOOTER --}}
<div class="footer">
    <p>Terima kasih telah berbelanja di <strong>FishMarket</strong> &bull; {{ config('app.url') }}</p>
    <p style="margin-top:4px;">Dicetak: {{ now()->format('d M Y, H:i') }} WIB &bull; Invoice {{ $order->order_number }}</p>
</div>

</body>
</html>
