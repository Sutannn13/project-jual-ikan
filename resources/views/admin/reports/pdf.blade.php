<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan - FishMarket</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; color: #1f2937; font-size: 12px; }
        .header { background: linear-gradient(135deg, #0369a1, #0ea5e9); color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 24px; margin-bottom: 5px; }
        .header p { font-size: 12px; opacity: 0.8; }
        .meta { padding: 15px 30px; background: #f0f9ff; display: flex; justify-content: space-between; }
        .meta p { font-size: 11px; color: #075985; }
        .content { padding: 20px 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        thead th { background: #0ea5e9; color: white; padding: 10px 12px; font-size: 11px; text-align: left; text-transform: uppercase; letter-spacing: 0.5px; }
        tbody td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #0c4a6e !important; color: white; font-weight: bold; font-size: 13px; }
        .total-row td { border: none; padding: 12px; }
        .footer { text-align: center; padding: 20px; font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; margin-top: 30px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
        .badge-lele { background: #fef3c7; color: #92400e; }
        .badge-mas { background: #e0f2fe; color: #075985; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🐟 FishMarket</h1>
        <p>Laporan Penjualan — Dicetak: {{ now()->format('d M Y, H:i') }} WIB</p>
    </div>

    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Order ID</th>
                    <th>Pelanggan</th>
                    <th>Items</th>
                    <th>Qty (Kg)</th>
                    <th class="text-right">Total</th>
                    <th class="text-center">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="font-weight: bold; color: #0369a1;">{{ $order->order_number }}</td>
                    <td>{{ $order->user->name }}</td>
                    <td>
                        @foreach($order->items as $item)
                            <span class="badge {{ $item->produk->kategori === 'Lele' ? 'badge-lele' : 'badge-mas' }}">{{ $item->produk->kategori }}</span>
                            {{ $item->produk->nama }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </td>
                    <td>
                        @foreach($order->items as $item)
                            {{ $item->qty }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </td>
                    <td class="text-right" style="font-weight: bold;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $order->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5" class="text-right">GRAND TOTAL</td>
                    <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} FishMarket — Laporan ini digenerate secara otomatis oleh sistem.
    </div>
</body>
</html>
