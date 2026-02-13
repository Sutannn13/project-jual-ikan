<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Menampilkan halaman keranjang
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $id => $item) {
            $produk = Produk::find($id);
            if ($produk) {
                $subtotal = $produk->harga_per_kg * $item['qty'];
                $cartItems[] = [
                    'produk' => $produk,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal,
                ];
                $total += $subtotal;
            }
        }

        return view('store.cart', compact('cartItems', 'total'));
    }

    /**
     * Menambah item ke keranjang
     */
    public function add(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'qty' => 'required|numeric|min:0.5|max:500',
        ], [
            'qty.max' => 'Maksimal pembelian 500 Kg per produk.',
        ]);

        $produk = Produk::findOrFail($request->produk_id);

        // Validasi stok
        if ($produk->stok < $request->qty) {
            return back()->with('error', "Stok {$produk->nama} tidak mencukupi. Tersedia: {$produk->stok} Kg");
        }

        $cart = session()->get('cart', []);
        $produkId = $request->produk_id;

        // Jika produk sudah ada di keranjang, tambahkan qty
        if (isset($cart[$produkId])) {
            $newQty = $cart[$produkId]['qty'] + $request->qty;
            
            // Validasi total qty tidak melebihi stok
            if ($newQty > $produk->stok) {
                return back()->with('error', "Total qty melebihi stok tersedia ({$produk->stok} Kg)");
            }
            
            $cart[$produkId]['qty'] = $newQty;
        } else {
            $cart[$produkId] = [
                'qty' => $request->qty,
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', "{$produk->nama} ({$request->qty} Kg) ditambahkan ke keranjang!");
    }

    /**
     * Update qty item di keranjang
     */
    public function update(Request $request, $produkId)
    {
        $request->validate([
            'qty' => 'required|numeric|min:0.5|max:500',
        ], [
            'qty.max' => 'Maksimal pembelian 500 Kg per produk.',
        ]);

        $produk = Produk::findOrFail($produkId);
        $cart = session()->get('cart', []);

        if (!isset($cart[$produkId])) {
            return back()->with('error', 'Item tidak ditemukan di keranjang.');
        }

        if ($request->qty > $produk->stok) {
            return back()->with('error', "Stok {$produk->nama} tidak mencukupi. Tersedia: {$produk->stok} Kg");
        }

        $cart[$produkId]['qty'] = $request->qty;
        session()->put('cart', $cart);

        return back()->with('success', 'Keranjang berhasil diperbarui!');
    }

    /**
     * Hapus item dari keranjang
     */
    public function remove($produkId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$produkId])) {
            $produk = Produk::find($produkId);
            unset($cart[$produkId]);
            session()->put('cart', $cart);
            return back()->with('success', "{$produk->nama} dihapus dari keranjang.");
        }

        return back()->with('error', 'Item tidak ditemukan di keranjang.');
    }

    /**
     * Kosongkan keranjang
     */
    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Keranjang berhasil dikosongkan!');
    }

    /**
     * Hitung jumlah item di keranjang (untuk badge di navbar)
     */
    public static function getCartCount(): int
    {
        $cart = session()->get('cart', []);
        return count($cart);
    }

    /**
     * Get cart data untuk API/AJAX
     */
    public function getCartData()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $total = 0;
        $count = count($cart);

        foreach ($cart as $id => $item) {
            $produk = Produk::find($id);
            if ($produk) {
                $subtotal = $produk->harga_per_kg * $item['qty'];
                $cartItems[] = [
                    'id' => $produk->id,
                    'nama' => $produk->nama,
                    'kategori' => $produk->kategori,
                    'harga_per_kg' => $produk->harga_per_kg,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal,
                    'foto' => $produk->foto ? asset('storage/' . $produk->foto) : null,
                ];
                $total += $subtotal;
            }
        }

        return response()->json([
            'items' => $cartItems,
            'total' => $total,
            'count' => $count,
        ]);
    }
}
