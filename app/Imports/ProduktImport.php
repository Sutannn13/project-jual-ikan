<?php

namespace App\Imports;

use App\Models\Produk;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Throwable;

class ProduktImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    protected int $imported = 0;

    /**
     * CSV/Excel columns (with heading row):
     * nama | kategori | harga_per_kg | harga_modal | stok | low_stock_threshold | deskripsi
     */
    public function model(array $row): ?Produk
    {
        // Skip empty rows
        if (empty($row['nama'])) return null;

        $kategori = $row['kategori'] ?? 'Lele';
        // Normalize category
        $allowedCategories = ['Lele', 'Ikan Mas', 'Ikan Nila', 'Ikan Patin', 'Ikan Bawal'];
        if (!in_array($kategori, $allowedCategories)) {
            $kategori = 'Lele';
        }

        $hargaPerKg = (float) str_replace(['.', ',', 'Rp', ' '], ['', '.', '', ''], $row['harga_per_kg'] ?? 0);
        $hargaModal = (float) str_replace(['.', ',', 'Rp', ' '], ['', '.', '', ''], $row['harga_modal'] ?? 0);
        $stok = (float) str_replace(',', '.', $row['stok'] ?? 0);

        $this->imported++;

        return new Produk([
            'nama'               => trim($row['nama']),
            'kategori'           => $kategori,
            'harga_per_kg'       => max(0, $hargaPerKg),
            'harga_modal'        => max(0, $hargaModal),
            'stok'               => max(0, $stok),
            'low_stock_threshold'=> (float) ($row['low_stock_threshold'] ?? 10),
            'deskripsi'          => $row['deskripsi'] ?? null,
            'low_stock_notified' => false,
        ]);
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }
}
