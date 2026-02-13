<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\AdminNotificationService;

class ProdukController extends Controller
{
    public function index()
    {
        $produks = Produk::latest()->paginate(10);
        return view('admin.produk.index', compact('produks'));
    }

    public function create()
    {
        return view('admin.produk.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'        => 'required|string|max:255',
            'kategori'    => 'required|in:Lele,Ikan Mas',
            'harga_per_kg'=> 'required|numeric|min:1000',
            'harga_modal' => 'required|numeric|min:0',
            'stok'        => 'required|numeric|min:0',
            'low_stock_threshold' => 'required|numeric|min:0',
            'foto'        => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'deskripsi'   => 'nullable|string',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('produk', 'public');
        }

        Produk::create($validated);

        $produk = Produk::latest()->first();
        AdminNotificationService::logProdukCreated($produk);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil ditambahkan!');
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
            'nama'        => 'required|string|max:255',
            'kategori'    => 'required|in:Lele,Ikan Mas',
            'harga_per_kg'=> 'required|numeric|min:1000',
            'harga_modal' => 'required|numeric|min:0',
            'stok'        => 'required|numeric|min:0',
            'low_stock_threshold' => 'required|numeric|min:0',
            'foto'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'deskripsi'   => 'nullable|string',
        ], [
            'stok.min' => 'Stok tidak boleh negatif.',
        ]);
        
        // Validate stock is not less than reserved stock
        if ($validated['stok'] < $produk->reserved_stock) {
            return back()->withErrors([
                'stok' => "Stok tidak boleh kurang dari stok yang di-reserve ({$produk->reserved_stock} Kg). Stok tersedia yang bisa diubah: " . ($produk->stok - $produk->reserved_stock) . " Kg"
            ])->withInput();
        }

        if ($request->hasFile('foto')) {
            if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
                Storage::disk('public')->delete($produk->foto);
            }
            $validated['foto'] = $request->file('foto')->store('produk', 'public');
        }

        $oldValues = $produk->only(['nama', 'harga_per_kg', 'stok', 'kategori', 'harga_modal']);
        $produk->update($validated);
        $newValues = $produk->fresh()->only(['nama', 'harga_per_kg', 'stok', 'kategori', 'harga_modal']);
        AdminNotificationService::logProdukUpdated($produk, ['old' => $oldValues, 'new' => $newValues]);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil diupdate!');
    }

    public function destroy(string $id)
    {
        $produk = Produk::findOrFail($id);

        // Cek apakah produk masih memiliki reserved stock (sedang dalam proses pemesanan aktif)
        if ($produk->reserved_stock > 0) {
            return redirect()->route('admin.produk.index')->with('error', 'Produk tidak dapat dihapus karena sedang ada dalam keranjang atau proses pemesanan pelanggan (Reserved: ' . $produk->reserved_stock . ' Kg).');
        }

        // Karena menggunakan SoftDeletes, kita tidak perlu menghapus file foto dulu
        // agar bisa di-restore jika diperlukan.
        // if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
        //    Storage::disk('public')->delete($produk->foto);
        // }

        AdminNotificationService::logProdukDeleted($produk);
        
        // Ini akan melakukan Soft Delete (mengisi kolom deleted_at)
        $produk->delete();

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus (diarsipkan)!');
    }
}