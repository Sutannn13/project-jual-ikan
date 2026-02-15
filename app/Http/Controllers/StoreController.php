<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Mail\OrderStatusMail;
use App\Mail\LowStockAlertMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\AdminNotificationService;

class StoreController extends Controller
{
    public function index()
    {
        $produks = Produk::where('stok', '>', 0)->latest()->get();
        $banners = \App\Models\Banner::active()->position('hero')->orderBy('sort_order')->get();
        return view('home', compact('produks', 'banners'));
    }

    public function catalog(Request $request)
    {
        $query = Produk::where('stok', '>', 0);

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }

        // Advanced filters
        if ($request->filled('min_price')) {
            $query->where('harga_per_kg', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('harga_per_kg', '<=', $request->max_price);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('harga_per_kg', 'asc');
                break;
            case 'price_high':
                $query->orderBy('harga_per_kg', 'desc');
                break;
            case 'name_az':
                $query->orderBy('nama', 'asc');
                break;
            case 'popular':
                $query->withCount('orderItems')->orderByDesc('order_items_count');
                break;
            case 'rating':
                $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating');
                break;
            default:
                $query->latest();
                break;
        }

        $produks = $query->paginate(12);

        // Get min/max prices for filter UI
        $priceRange = Produk::selectRaw('MIN(harga_per_kg) as min, MAX(harga_per_kg) as max')->first();

        return view('store.catalog', compact('produks', 'priceRange'));
    }

    public function show(Produk $produk)
    {
        $produk->load(['reviews.user']);
        
        // Get completed orders that contain this product (for review eligibility)
        $completedOrders = collect();
        if (Auth::check()) {
            $completedOrders = Order::where('user_id', Auth::id())
                ->where('status', 'completed')
                ->whereHas('items', function ($q) use ($produk) {
                    $q->where('produk_id', $produk->id);
                })
                ->get();
        }

        // Check if product is wishlisted
        $isWishlisted = false;
        if (Auth::check()) {
            $isWishlisted = \App\Models\Wishlist::where('user_id', Auth::id())
                ->where('produk_id', $produk->id)
                ->exists();
        }

        // Related products
        $relatedProducts = Produk::where('kategori', $produk->kategori)
            ->where('id', '!=', $produk->id)
            ->where('stok', '>', 0)
            ->limit(4)
            ->get();

        return view('store.show', compact('produk', 'completedOrders', 'isWishlisted', 'relatedProducts'));
    }

    /**
     * Checkout dari keranjang
     */
    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong!');
        }

        // Validate shipping address
        $userAddress = Auth::user()->alamat ?? '';
        if (empty(trim($userAddress))) {
            return redirect()->route('cart.index')->with('error', 'Silakan lengkapi alamat pengiriman di profil Anda terlebih dahulu.');
        }

        // Check if user already has pending order (prevent double checkout)
        $existingPending = Order::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'waiting_payment'])
            ->exists();
        
        if ($existingPending) {
            return redirect()->route('my.orders')->with('error', 'Anda masih memiliki pesanan yang belum dibayar. Selesaikan pembayaran terlebih dahulu.');
        }

        $order = DB::transaction(function () use ($cart, $request, $userAddress) {
            $orderNumber = Order::generateOrderNumber();
            $totalPrice = 0;

            // Detect shipping zone based on user address
            $shippingZone = \App\Models\ShippingZone::where('is_active', true)->get()
                ->first(fn($zone) => $zone->coversArea($userAddress));
            
            $shippingCost = $shippingZone ? $shippingZone->cost : 0;

            // Validate and RESERVE stock (don't deduct yet)
            $totalPrice = 0;
            foreach ($cart as $produkId => $item) {
                $produk = Produk::lockForUpdate()->find($produkId);

                // Cek apakah produk masih ada (belum di-soft delete)
                if (!$produk) {
                    throw new \Exception("Produk tidak tersedia lagi. Silakan perbarui keranjang Anda.");
                }

                if (!$produk->reserveStock($item['qty'])) {
                    throw new \Exception("Stok {$produk->nama} tidak mencukupi. Tersedia: {$produk->availableStock} Kg");
                }
                
                // Calculate total while we have the product
                $totalPrice += $produk->harga_per_kg * $item['qty'];
            }

            $grandTotal = $totalPrice + $shippingCost;

            $order = Order::create([
                'user_id'          => Auth::id(),
                'order_number'     => $orderNumber,
                'total_price'      => $grandTotal, // Grand total (termasuk ongkir)
                'shipping_cost'    => $shippingCost,
                'shipping_zone_id' => $shippingZone?->id,
                'status'           => 'pending',
                'payment_deadline' => now()->addHours(24),
            ]);

            foreach ($cart as $produkId => $item) {
                $produk = Produk::findOrFail($produkId);
                $subtotal = $produk->harga_per_kg * $item['qty'];

                OrderItem::create([
                    'order_id'      => $order->id,
                    'produk_id'     => $produk->id,
                    'price_per_kg'  => $produk->harga_per_kg, // Snapshot price
                    'qty'           => $item['qty'],
                    'subtotal'      => $subtotal,
                    'cost_price'    => $produk->harga_modal,
                ]);
            }

            // Clear cart
            session()->forget('cart');

            return $order;
        });

        // Send low stock alerts OUTSIDE transaction (async)
        try {
            $this->checkLowStockAlerts($cart);
        } catch (\Exception $e) {
            Log::error('Failed to send low stock alert', ['error' => $e->getMessage()]);
        }

        // Trigger admin notification
        try {
            AdminNotificationService::orderCreated($order);
        } catch (\Exception $e) {
            Log::error('Failed to create order notification', ['error' => $e->getMessage()]);
        }

        return redirect()->route('order.success', $order->id)
            ->with('success', 'Pesanan berhasil dibuat! Silakan upload bukti pembayaran dalam 24 jam.');
    }

    /**
     * Halaman sukses order - user upload bukti bayar di sini
     */
    public function orderSuccess(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        $order->load('items.produk');
        return view('store.order-success', compact('order'));
    }

    /**
     * Upload bukti pembayaran
     */
    public function uploadPaymentProof(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        // Hanya bisa upload jika status pending atau waiting_payment (untuk upload ulang)
        // Juga izinkan jika payment proof ditolak (ada rejection_reason)
        if (!in_array($order->status, ['pending', 'waiting_payment'])) {
            return back()->with('error', 'Pesanan sudah diproses dan tidak bisa diupdate bukti pembayarannya.');
        }

        // Jangan izinkan upload jika sudah terverifikasi (paid atau lebih lanjut)
        if (in_array($order->status, ['paid', 'confirmed', 'out_for_delivery', 'completed'])) {
            return back()->with('error', 'Pembayaran sudah diverifikasi, tidak bisa upload ulang.');
        }

        // Cek apakah deadline sudah lewat (hanya untuk status pending)
        if ($order->status === 'pending' && $order->isPaymentExpired()) {
            return back()->with('error', 'Waktu pembayaran sudah habis. Pesanan akan otomatis dibatalkan.');
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'payment_proof.required' => 'Bukti pembayaran wajib diupload.',
            'payment_proof.image' => 'File harus berupa gambar.',
            'payment_proof.mimes' => 'Format file harus JPG atau PNG.',
            'payment_proof.max' => 'Ukuran file maksimal 5MB. Coba kompres gambar terlebih dahulu.',
        ]);

        // Hapus bukti lama jika ada
        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        // Simpan bukti baru
        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        // Reset Midtrans data jika customer memilih bayar manual
        $order->update([
            'payment_proof' => $path,
            'payment_uploaded_at' => now(),
            'status' => 'waiting_payment', // Menunggu verifikasi admin
            'rejection_reason' => null, // Reset alasan penolakan jika upload ulang
            'midtrans_snap_token' => null, // Clear Midtrans token (customer pilih bayar manual)
            'midtrans_transaction_id' => null,
            'payment_method' => 'manual_transfer', // Mark as manual payment
        ]);

        \Illuminate\Support\Facades\Log::info("Payment proof uploaded for order {$order->order_number}", [
            'user_id' => Auth::id(),
            'file_path' => $path,
            'status' => 'waiting_payment',
        ]);

        // Trigger URGENT admin notification
        try {
            AdminNotificationService::paymentUploaded($order);
        } catch (\Exception $e) {
            Log::error('Failed to create payment notification', ['error' => $e->getMessage()]);
        }

        return back()->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');
    }

    /**
     * Customer membatalkan order sendiri (hanya jika masih pending)
     */
    public function cancelOrder(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        // Hanya bisa batalkan jika status masih pending atau waiting_payment
        if (!in_array($order->status, ['pending', 'waiting_payment'])) {
            return back()->with('error', 'Pesanan sudah diproses dan tidak dapat dibatalkan.');
        }

        DB::transaction(function () use ($order) {
            // Lock order row to prevent race condition
            $order = Order::lockForUpdate()->findOrFail($order->id);

            // Double-check status inside transaction
            if (!in_array($order->status, ['pending', 'waiting_payment'])) {
                throw new \Exception('Pesanan sudah diproses.');
            }

            // Release reserved stock
            foreach ($order->items as $item) {
                $produk = Produk::lockForUpdate()->find($item->produk_id);
                if ($produk) {
                    $produk->releaseStock($item->qty);
                }
            }

            $order->update(['status' => 'cancelled']);
        });

        // Notify admin
        try {
            AdminNotificationService::orderCancelled($order);
        } catch (\Exception $e) {
            Log::error('Failed to create cancel notification', ['error' => $e->getMessage()]);
        }

        return redirect()->route('my.orders')->with('success', "Pesanan {$order->order_number} berhasil dibatalkan.");
    }

    public function myOrders(Request $request)
    {
        $tab = $request->get('tab', 'active');
        
        $query = Order::where('user_id', Auth::id())->with('items.produk');
        
        if ($tab === 'history') {
            // Riwayat: completed dan cancelled
            $query->whereIn('status', ['completed', 'cancelled']);
        } else {
            // Aktif: pending, waiting_payment, paid, confirmed, out_for_delivery
            $query->whereIn('status', ['pending', 'waiting_payment', 'paid', 'confirmed', 'out_for_delivery']);
        }
        
        $orders = $query->latest()->paginate(10)->withQueryString();
        
        // Count untuk badge di tab
        $activeCount = Order::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'waiting_payment', 'paid', 'confirmed', 'out_for_delivery'])
            ->count();
        $historyCount = Order::where('user_id', Auth::id())
            ->whereIn('status', ['completed', 'cancelled'])
            ->count();

        return view('store.my-orders', compact('orders', 'tab', 'activeCount', 'historyCount'));
    }

    public function trackOrder(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        $order->load('items.produk');
        return view('store.track-order', compact('order'));
    }

    /**
     * Check low stock after checkout and send email alerts to admin
     */
    private function checkLowStockAlerts(array $cart): void
    {
        try {
            foreach (array_keys($cart) as $produkId) {
                $produk = Produk::find($produkId);
                if ($produk && $produk->isLowStock() && !$produk->low_stock_notified) {
                    $admins = User::where('role', 'admin')->get();
                    foreach ($admins as $admin) {
                        Mail::to($admin->email)->send(new LowStockAlertMail($produk));
                    }
                    $produk->update(['low_stock_notified' => true]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send low stock alert', ['error' => $e->getMessage()]);
        }
    }
}
