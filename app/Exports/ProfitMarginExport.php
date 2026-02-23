<?php

namespace App\Exports;

use App\Models\Produk;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProfitMarginExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function query()
    {
        return Produk::withTrashed()
            ->select('produks.*')
            ->selectRaw('(SELECT COALESCE(SUM(oi.qty), 0) FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE oi.produk_id = produks.id AND o.status = "completed") AS total_qty_sold')
            ->selectRaw('(SELECT COALESCE(SUM(oi.total_price), 0) FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE oi.produk_id = produks.id AND o.status = "completed") AS total_revenue')
            ->selectRaw('(SELECT COALESCE(SUM(oi.qty * COALESCE(oi.harga_modal, produks.harga_modal, 0)), 0) FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE oi.produk_id = produks.id AND o.status = "completed") AS total_cost')
            ->latest();
    }

    public function headings(): array
    {
        return [
            'Produk',
            'Kategori',
            'Harga Jual (Rp/Kg)',
            'Harga Modal (Rp/Kg)',
            'Margin (Rp/Kg)',
            'Margin (%)',
            'Stok (Kg)',
            'Total Terjual (Kg)',
            'Total Pendapatan (Rp)',
            'Total Modal (Rp)',
            'Total Keuntungan (Rp)',
        ];
    }

    public function map($produk): array
    {
        $hargaJual  = (float) $produk->harga_per_kg;
        $hargaModal = (float) $produk->harga_modal;
        $margin     = $hargaJual - $hargaModal;
        $marginPct  = $hargaJual > 0 ? round(($margin / $hargaJual) * 100, 1) : 0;
        $totalProfit = (float) $produk->total_revenue - (float) $produk->total_cost;

        return [
            $produk->nama . ($produk->deleted_at ? ' (dihapus)' : ''),
            $produk->kategori,
            number_format($hargaJual, 0, ',', '.'),
            number_format($hargaModal, 0, ',', '.'),
            number_format($margin, 0, ',', '.'),
            $marginPct . '%',
            $produk->stok,
            $produk->total_qty_sold,
            number_format($produk->total_revenue, 0, ',', '.'),
            number_format($produk->total_cost, 0, ',', '.'),
            number_format($totalProfit, 0, ',', '.'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7C3AED']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Margin Keuntungan';
    }
}
