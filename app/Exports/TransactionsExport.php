<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Order::with(['user', 'items.produk'])->where('status', 'completed');

        if (!empty($this->filters['from'])) {
            $query->whereDate('created_at', '>=', $this->filters['from']);
        }
        if (!empty($this->filters['to'])) {
            $query->whereDate('created_at', '<=', $this->filters['to']);
        }
        if (!empty($this->filters['status']) && $this->filters['status'] !== 'all') {
            $query->where('status', $this->filters['status']);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'No. Order',
            'Tanggal',
            'Pelanggan',
            'Email',
            'No. HP',
            'Produk',
            'Total (Rp)',
            'Ongkir (Rp)',
            'Metode Bayar',
            'Status',
        ];
    }

    public function map($order): array
    {
        $products = $order->items->map(fn($item) =>
            ($item->nama_produk ?? $item->produk?->nama ?? 'N/A') . ' x' . $item->qty . 'kg'
        )->implode('; ');

        return [
            $order->order_number,
            $order->created_at->format('d/m/Y H:i'),
            $order->user?->name ?? 'Guest',
            $order->user?->email ?? '-',
            $order->user?->no_hp ?? '-',
            $products,
            number_format($order->total_price, 0, ',', '.'),
            number_format($order->shipping_cost ?? 0, 0, ',', '.'),
            $order->payment_method ?? 'Transfer',
            ucfirst($order->status),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0891B2']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Transaksi';
    }
}
