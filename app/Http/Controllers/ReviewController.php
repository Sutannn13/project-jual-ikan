<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Produk;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AdminNotificationService;

class ReviewController extends Controller
{
    /**
     * Store a new review for a product
     */
    public function store(Request $request, Produk $produk)
    {
        $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'comment'  => 'nullable|string|max:1000',
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->firstOrFail();

        // Check if already reviewed this product for this order
        $existing = Review::where('user_id', Auth::id())
            ->where('produk_id', $produk->id)
            ->where('order_id', $order->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah memberikan review untuk produk ini pada pesanan tersebut.');
        }

        // Verify product was in the order
        $orderHasProduct = $order->items()->where('produk_id', $produk->id)->exists();
        if (!$orderHasProduct) {
            return back()->with('error', 'Produk ini tidak ada dalam pesanan tersebut.');
        }

        Review::create([
            'user_id'   => Auth::id(),
            'produk_id' => $produk->id,
            'order_id'  => $order->id,
            'rating'    => $request->rating,
            'comment'   => $request->comment,
        ]);

        // Notify admin
        try {
            $review = Review::where('user_id', Auth::id())
                ->where('produk_id', $produk->id)
                ->where('order_id', $order->id)
                ->first();
            if ($review) {
                AdminNotificationService::newReview($review);
            }
        } catch (\Exception $e) {
            // Silent
        }

        return back()->with('success', 'Review berhasil ditambahkan! Terima kasih atas feedback Anda.');
    }

    /**
     * Delete a review (by the reviewer)
     */
    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $review->delete();

        return back()->with('success', 'Review berhasil dihapus.');
    }
}
