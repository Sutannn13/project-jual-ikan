<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display wishlist page
     */
    public function index()
    {
        $wishlists = Wishlist::where('user_id', Auth::id())
            ->with('produk')
            ->latest()
            ->paginate(12);

        return view('store.wishlist', compact('wishlists'));
    }

    /**
     * Toggle wishlist (add/remove)
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
        ]);

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('produk_id', $request->produk_id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Produk dihapus dari wishlist.';
            $wishlisted = false;
        } else {
            Wishlist::create([
                'user_id'   => Auth::id(),
                'produk_id' => $request->produk_id,
            ]);
            $message = 'Produk ditambahkan ke wishlist!';
            $wishlisted = true;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status'     => $wishlisted ? 'added' : 'removed',
                'wishlisted' => $wishlisted,
                'message'    => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Remove from wishlist
     */
    public function remove(Produk $produk)
    {
        Wishlist::where('user_id', Auth::id())
            ->where('produk_id', $produk->id)
            ->delete();

        return back()->with('success', 'Produk dihapus dari wishlist.');
    }
}
