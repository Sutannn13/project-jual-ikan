<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\ProductImage;
use App\Imports\ProduktImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\AdminNotificationService;

class ProdukController extends Controller
{
    public function index()
    {
        // Narik data dari database, diurutkan dari yang terbaru, dibagi 10 per halaman
        $produks = Produk::latest()->paginate(10);

        // Jika yang minta adalah API (Postman / Angular)
        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Berhasil mengambil daftar produk',
                'data'    => $produks
            ], 200);
        }

        // Jika yang minta adalah Web Browser biasa
        return view('admin.produk.index', compact('produks'));
    }

    public function create()
    {
        return view('admin.produk.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'                => 'required|string|max:255',
            'kategori'            => 'required|string|max:100',
            'harga_per_kg'        => 'required|numeric|min:1000',
            'harga_modal'         => 'required|numeric|min:0',
            'stok'                => 'required|numeric|min:0',
            'low_stock_threshold' => 'required|numeric|min:0',
            'foto'                => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'fotos.*'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'deskripsi'           => 'nullable|string',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('produk', 'public');
        }

        $produk = Produk::create($validated);

        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $i => $file) {
                $path = $file->store('produk', 'public');
                ProductImage::create([
                    'produk_id'  => $produk->id,
                    'path'       => $path,
                    'is_primary' => $i === 0 && !$produk->foto,
                    'sort_order' => $i,
                ]);
            }
        }

        if (!$produk->foto) {
            $first = $produk->productImages()->first();
            if ($first) {
                $produk->update(['foto' => $first->path]);
            }
        }

        AdminNotificationService::logProdukCreated($produk);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Produk berhasil ditambahkan!',
                'data'    => $produk
            ], 201);
        }

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    // --- FUNGSI SHOW HARUS DI SINI, DI LUAR FUNGSI STORE ---
    public function show(string $id)
    {
        $produk = Produk::findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Detail produk ditemukan',
                'data'    => $produk
            ], 200);
        }

        return view('admin.produk.show', compact('produk'));
    }

    public function edit(string $id)
    {
        $produk = Produk::findOrFail($id);
        return view('admin.produk.edit', compact('produk'));
    }

    public function update(Request $request, string $id)
    {
        $produk = Produk::findOrFail($id);

        $validated = $request->validate([
            'nama'                => 'required|string|max:255',
            'kategori'            => 'required|string|max:100',
            'harga_per_kg'        => 'required|numeric|min:1000',
            'harga_modal'         => 'required|numeric|min:0',
            'stok'                => 'required|numeric|min:0',
            'low_stock_threshold' => 'required|numeric|min:0',
            'foto'                => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'fotos.*'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'deskripsi'           => 'nullable|string',
        ], [
            'stok.min' => 'Stok tidak boleh negatif.',
        ]);

        if ($validated['stok'] < $produk->reserved_stock) {
            return back()->withErrors([
                'stok' => "Stok tidak boleh kurang dari stok yang di-reserve ({$produk->reserved_stock} Kg)."
            ])->withInput();
        }

        if ($request->hasFile('foto')) {
            if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
                Storage::disk('public')->delete($produk->foto);
            }
            $validated['foto'] = $request->file('foto')->store('produk', 'public');
        } else {
            unset($validated['foto']);
        }

        $oldValues = $produk->only(['nama', 'harga_per_kg', 'stok', 'kategori', 'harga_modal']);
        $produk->update($validated);
        $newValues = $produk->fresh()->only(['nama', 'harga_per_kg', 'stok', 'kategori', 'harga_modal']);
        AdminNotificationService::logProdukUpdated($produk, ['old' => $oldValues, 'new' => $newValues]);

        if ($request->hasFile('fotos')) {
            $existingCount = $produk->productImages()->count();
            foreach ($request->file('fotos') as $i => $file) {
                $path = $file->store('produk', 'public');
                ProductImage::create([
                    'produk_id'  => $produk->id,
                    'path'       => $path,
                    'is_primary' => false,
                    'sort_order' => $existingCount + $i,
                ]);
            }
        }

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil diupdate!');
    }

    public function deleteImage(ProductImage $image)
    {
        $produk = $image->produk;
        if (Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $next = $produk->productImages()->first();
            if ($next) {
                $next->update(['is_primary' => true]);
                $produk->update(['foto' => $next->path]);
            }
        }

        return back()->with('success', 'Foto berhasil dihapus!');
    }

    public function setPrimaryImage(ProductImage $image)
    {
        $produkId = $image->produk_id;
        ProductImage::where('produk_id', $produkId)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);
        Produk::where('id', $produkId)->update(['foto' => $image->path]);

        return back()->with('success', 'Foto utama berhasil diubah!');
    }

    public function importForm()
    {
        return view('admin.produk.import');
    }

    public function importTemplate()
    {
        $headers = ['nama', 'kategori', 'harga_per_kg', 'harga_modal', 'stok', 'low_stock_threshold', 'deskripsi'];
        $sampleRows = [
            ['Ikan Nila Segar', 'Ikan Nila', 35000, 22000, 50, 10, 'Ikan nila segar kualitas premium'],
            ['Ikan Mas', 'Ikan Mas', 28000, 18000, 40, 8, ''],
        ];

        $response = response()->streamDownload(function () use ($headers, $sampleRows) {
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);
            foreach ($sampleRows as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, 'template-import-produk.csv', ['Content-Type' => 'text/csv']);

        return $response;
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:10240',
        ]);

        try {
            $import = new ProduktImport();
            Excel::import($import, $request->file('file'));
            $errorsCollection = $import->errors();
            $imported = $import->getImportedCount();

            $msg = "Berhasil mengimpor {$imported} produk!";
            if ($errorsCollection->count() > 0) {
                $msg .= " Ada {$errorsCollection->count()} baris yang dilewati karena error.";
            }

            return redirect()->route('admin.produk.index')->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $produk = Produk::findOrFail($id);

        if ($produk->reserved_stock > 0) {
            $msg = "Produk tidak dapat dihapus karena ada pesanan aktif (Reserved: {$produk->reserved_stock} Kg).";
            if (request()->expectsJson()) {
                return response()->json(['message' => $msg], 422);
            }
            return redirect()->route('admin.produk.index')->with('error', $msg);
        }

        AdminNotificationService::logProdukDeleted($produk);

        $produk->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Produk berhasil dihapus!'], 200);
        }

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus!');
    }
}
