<?php

namespace App\Exports;

use App\Models\StockIn;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StockExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = StockIn::with(['produk', 'user'])->latest();

        if (!empty($this->filters['produk_id'])) {
            $query->where('produk_id', $this->filters['produk_id']);
        }
        if (!empty($this->filters['from'])) {
            $query->whereDate('created_at', '>=', $this->filters['from']);
        }
        if (!empty($this->filters['to'])) {
            $query->whereDate('created_at', '<=', $this->filters['to']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Produk',
            'Jumlah (Kg)',
            'Stok Sebelum (Kg)',
            'Stok Sesudah (Kg)',
            'Harga Modal (Rp)',
            'Supplier',
            'Tanggal Masuk',
            'Tgl Kedaluwarsa',
            'Status Kedaluwarsa',
            'Dicatat Oleh',
            'Catatan',
        ];
    }

    public function map($stockIn): array
    {
        $expiryLabel = 'N/A';
        if ($stockIn->expiry_date) {
            $expiryLabel = match ($stockIn->expiry_status) {
                'expired'  => 'KADALUWARSA',
                'critical' => 'Kritis (<=1 hr)',
                'warning'  => 'Peringatan (<=3 hr)',
                default    => 'OK',
            };
        }

        return [
            $stockIn->id,
            $stockIn->produk?->nama ?? 'N/A',
            $stockIn->qty,
            $stockIn->stok_sebelum,
            $stockIn->stok_sesudah,
            $stockIn->harga_modal ? number_format($stockIn->harga_modal, 0, ',', '.') : '-',
            $stockIn->supplier ?? '-',
            $stockIn->created_at->format('d/m/Y H:i'),
            $stockIn->expiry_date?->format('d/m/Y') ?? '-',
            $expiryLabel,
            $stockIn->user?->name ?? '-',
            $stockIn->catatan ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Stok Masuk';
    }
}
