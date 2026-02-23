<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Menampilkan halaman keranjang
     */
    public function index()
    {
        $items = CartItem::with('produk')
            ->where('user_id', Auth::id())
            ->get();

        $removedItems = [];
        $cartItems    = [];
        $total        = 0;

        foreach ($items as $item) {
            $produk = $item->produk;

            if (!$produk || $produk->trashed()) {
                $removedItems[] = $produk ? $produk->nama : "Produk #" . $item->produk_id;
                $item->delete();
                continue;
            }

            $subtotal = $produk->harga_per_kg * $item->qty;
            $cartItems[] = [
                'produk'   => $produk,
                'qty'      => $item->qty,
                'subtotal' => $subtotal,
            ];
            $total += $subtotal;
        }

        if (!empty($removedItems)) {
            session()->flash('warning', 'Beberapa produk dihapus dari keranjang karena tidak tersedia: ' . implode(', ', $removedItems));
        }

        return view('store.cart', compact('cartItems', 'total'));
    }

    /**
     * Menambah item ke keranjang (database)
     */
    public function add(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'qty'       => 'required|numeric|min:0.5|max:500',
        ], [
            'qty.max' => 'Maksimal pembelian 500 Kg per produk.',
        ]);

        $produk    = Produk::findOrFail($request->produk_id);
        $available = $produk->availableStock;

        if ($available < $request->qty) {
            return back()->with('error', "Stok {$produk->nama} tidak mencukupi. Tersedia: {$available} Kg");
        }

        $cartItem = CartItem::firstOrNew([
            'user_id'   => Auth::id(),
            'produk_id' => $request->produk_id,
        ]);

        $newQty = ($cartItem->exists ? $cartItem->qty : 0) + $request->qty;

        if ($newQty > $available) {
            return back()->with('error', "Total qty melebihi stok tersedia ({$available} Kg)");
        }

        $cartItem->qty = $newQty;
        $cartItem->save();

        return back()
            ->with('success', "{$produk->nama} ({$request->qty} Kg) ditambahkan ke keranjang!")
            ->with('cart_added', true);
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

        if ($request->qty > $produk->availableStock) {
            return back()->with('error', "Stok {$produk->nama} tidak mencukupi. Tersedia: {$produk->availableStock} Kg");
        }

        CartItem::where('user_id', Auth::id())
                ->where('produk_id', $produkId)
                ->update(['qty' => $request->qty]);

        return back()->with('success', 'Keranjang berhasil diperbarui!');
    }

    /**
     * Hapus item dari keranjang
     */
    public function remove($produkId)
    {
        $produk  = Produk::withTrashed()->find($produkId);
        $deleted = CartItem::where('user_id', Auth::id())
                           ->where('produk_id', $produkId)
                           ->delete();

        if ($deleted) {
            return back()->with('success', ($produk?->nama ?? 'Item') . ' dihapus dari keranjang.');
        }

        return back()->with('error', 'Item tidak ditemukan di keranjang.');
    }

    /**
     * Kosongkan keranjang
     */
    public function clear()
    {
        CartItem::where('user_id', Auth::id())->delete();
        return back()->with('success', 'Keranjang berhasil dikosongkan!');
    }

    /**
     * Hitung jumlah item di keranjang (untuk badge di navbar)
     */
    public static function getCartCount(): int
    {
        if (!Auth::check()) {
            return 0;
        }
        return CartItem::where('user_id', Auth::id())->count();
    }

    /**
     * Get cart data untuk API/AJAX
     */
    public function getCartData()
    {
        $items = CartItem::with('produk')
            ->where('user_id', Auth::id())
            ->get();

        $cartItems = [];
        $total     = 0;

        foreach ($items as $item) {
            $produk = $item->produk;
            if ($produk && !$produk->trashed()) {
                $subtotal    = $produk->harga_per_kg * $item->qty;
                $cartItems[] = [
                    'id'           => $produk->id,
                    'nama'         => $produk->nama,
                    'kategori'     => $produk->kategori,
                    'harga_per_kg' => $produk->harga_per_kg,
                    'qty'          => $item->qty,
                    'subtotal'     => $subtotal,
                    'foto'         => $produk->foto ? asset('storage/' . $produk->foto) : null,
                ];
                $total += $subtotal;
            }
        }

        return response()->json([
            'items' => $cartItems,
            'total' => $total,
            'count' => count($cartItems),
        ]);
    }

    /**
     * Get cart items as array for checkout (used by StoreController)
     */
    public static function getDbCartItems(int $userId): array
    {
        return CartItem::with('produk')
            ->where('user_id', $userId)
            ->get()
            ->filter(fn($item) => $item->produk && !$item->produk->trashed())
            ->map(fn($item) => [
                'produk' => $item->produk,
                'qty'    => (float) $item->qty,
            ])
            ->values()
            ->toArray();
    }
}
