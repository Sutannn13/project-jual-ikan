<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StockIn;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    /**
     * Daftar riwayat stock in
     */
    public function index(Request $request)
    {
        $query = StockIn::with(['produk', 'user'])->latest();

        if ($request->filled('produk_id')) {
            $query->where('produk_id', $request->produk_id);
        }

        $stockIns = $query->paginate(20);
        $produks = Produk::orderBy('nama')->get();

        return view('admin.stock-in.index', compact('stockIns', 'produks'));
    }

    /**
     * Form tambah stok
     */
    public function create()
    {
        $produks = Produk::orderBy('nama')->get();
        return view('admin.stock-in.create', compact('produks'));
    }

    /**
     * Proses tambah stok
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'produk_id'   => 'required|exists:produks,id',
            'qty'         => 'required|numeric|min:0.5|max:10000',
            'harga_modal' => 'nullable|numeric|min:0',
            'supplier'    => 'nullable|string|max:255',
            'catatan'     => 'nullable|string|max:1000',
            'expiry_date' => 'nullable|date|after:today',
        ], [
            'qty.min' => 'Jumlah stok minimal 0.5 Kg.',
            'qty.max' => 'Jumlah stok maksimal 10.000 Kg per input.',
        ]);

        $result = DB::transaction(function () use ($validated) {
            $produk = Produk::lockForUpdate()->findOrFail($validated['produk_id']);
            $stokSebelum = $produk->stok;

            // Tambah stok
            $produk->increment('stok', $validated['qty']);

            // Update harga modal jika diisi
            if (!empty($validated['harga_modal'])) {
                $produk->update(['harga_modal' => $validated['harga_modal']]);
            }

            // Reset low stock notification jika stok sudah di atas threshold
            if ($produk->fresh()->stok > $produk->low_stock_threshold) {
                $produk->update(['low_stock_notified' => false]);
            }

            // catat stock in
            $stockIn = StockIn::create([
                'produk_id'    => $produk->id,
                'user_id'      => Auth::id(),
                'qty'          => $validated['qty'],
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSebelum + $validated['qty'],
                'harga_modal'  => $validated['harga_modal'] ?? $produk->harga_modal,
                'supplier'     => $validated['supplier'] ?? null,
                'catatan'      => $validated['catatan'] ?? null,
                'expiry_date'  => $validated['expiry_date'] ?? null,
            ]);

            // Activity Log
            ActivityLog::log(
                'stock_in',
                "Restok {$produk->nama}: +{$validated['qty']} Kg (Stok: {$stokSebelum} → " . ($stokSebelum + $validated['qty']) . " Kg)" .
                    ($validated['supplier'] ? " dari {$validated['supplier']}" : '') .
                    (!empty($validated['expiry_date']) ? " | Exp: {$validated['expiry_date']}" : ''),
                'Produk',
                $produk->id,
                [
                    'qty_added'    => $validated['qty'],
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSebelum + $validated['qty'],
                    'supplier'     => $validated['supplier'] ?? null,
                    'expiry_date'  => $validated['expiry_date'] ?? null,
                ]
            );

            return $stockIn;
        });

        return redirect()->route('admin.stock-in.index')
            ->with('success', "Berhasil menambah stok {$result->qty} Kg untuk {$result->produk->nama}!");
    }

    /**
     * Expiry date alert: list stok yang expired/hampir expired
     */
    public function expiryAlert()
    {
        $expiring = StockIn::with(['produk', 'user'])
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays(7))
            ->orderBy('expiry_date')
            ->get();

        $expired  = $expiring->filter(fn($s) => $s->isExpired());
        $critical = $expiring->filter(fn($s) => !$s->isExpired() && $s->isExpiringSoon(1));
        $warning  = $expiring->filter(fn($s) => !$s->isExpired() && !$s->isExpiringSoon(1) && $s->isExpiringSoon(7));

        return view('admin.stock-in.expiry-alert', compact('expired', 'critical', 'warning'));
    }
}
