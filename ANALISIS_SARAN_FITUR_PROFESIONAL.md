# 🚀 ANALISIS SARAN FITUR PROFESIONAL - TOKO IKAN

**Tanggal:** 15 Februari 2026  
**Analyst:** System Architecture Review  
**Scope:** E-commerce Fish Market Platform

---

## 📊 EXECUTIVE SUMMARY

Berdasarkan analisis mendalam terhadap proyek toko ikan, berikut adalah **rekomendasi fitur profesional** yang dapat meningkatkan:

- **Revenue** (omset & profit)
- **Customer Retention** (loyalitas pelanggan)
- **Operational Efficiency** (efisiensi admin)
- **Competitive Advantage** (keunggulan kompetitif)

### Status Fitur Saat Ini

✅ **SUDAH ADA:**

- Dashboard Analytics (Omset, Profit, Ongkir)
- Order Management dengan notifikasi
- Payment Gateway (Midtrans + Manual)
- Review & Rating System
- Chat Admin-Customer
- Wishlist
- Shipping Zones (Ongkir Dinamis)
- Activity Log
- Stock Management
- Multi Address User
- Refund System
- Admin Notifications

🆕 **YANG BELUM ADA (PRIORITAS IMPLEMENTASI):**

---

## 🎯 PRIORITAS 1: MARKETING & REVENUE BOOSTER

### 1️⃣ **SISTEM VOUCHER & PROMO CODE**

#### 💡 Kenapa Penting?

- **Meningkatkan konversi** pembelian pertama (first-time buyer)
- **Mendorong repeat purchase** (customer lama kembali)
- **Tracking campaign effectiveness** (ROI marketing)
- **Kompetitor sudah ada** (Tokopedia, Shopee, Blibli semua punya)

#### 📊 Business Impact:

- **Konversi +20-40%** dengan promo first-purchase
- **Repeat purchase +30%** dengan loyalty voucher
- **Average Order Value (AOV) +25%** dengan min. pembelian

#### 🛠️ Implementasi Teknis:

**Database Schema:**

```sql
-- Tabel Master Promo
CREATE TABLE promotions (
    id BIGINT PRIMARY KEY,
    name VARCHAR(100),                    -- "Promo Lebaran 2026"
    description TEXT,
    type ENUM('percentage', 'fixed', 'free_shipping', 'bundle'),
    discount_value DECIMAL(10,2),         -- 15 (untuk 15%) atau 50000 (untuk Rp 50.000)
    min_purchase DECIMAL(12,2) NULL,      -- Minimal belanja Rp 100.000
    max_discount DECIMAL(10,2) NULL,      -- Max diskon Rp 50.000 (untuk percentage)
    quota INTEGER NULL,                    -- Batas jumlah penggunaan (100 voucher)
    used_count INTEGER DEFAULT 0,
    start_date DATETIME,
    end_date DATETIME,
    is_active BOOLEAN DEFAULT true,
    applicable_to ENUM('all', 'specific_products', 'specific_categories'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Tabel Kupon/Kode Promo
CREATE TABLE coupons (
    id BIGINT PRIMARY KEY,
    promotion_id BIGINT,                   -- FK ke promotions
    code VARCHAR(50) UNIQUE,               -- "LEBARAN15" atau "NEWCUST50"
    user_id BIGINT NULL,                   -- Untuk voucher personal (NULL = public)
    max_usage_per_user INTEGER DEFAULT 1, -- User bisa pakai berapa kali
    used_count_user INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    FOREIGN KEY (promotion_id) REFERENCES promotions(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabel Riwayat Penggunaan
CREATE TABLE coupon_usages (
    id BIGINT PRIMARY KEY,
    coupon_id BIGINT,
    user_id BIGINT,
    order_id BIGINT,
    discount_amount DECIMAL(10,2),         -- Actual diskon yang didapat
    used_at TIMESTAMP,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- Relasi Promo ke Produk (untuk promo specific products)
CREATE TABLE promotion_products (
    promotion_id BIGINT,
    produk_id BIGINT,
    FOREIGN KEY (promotion_id) REFERENCES promotions(id),
    FOREIGN KEY (produk_id) REFERENCES produks(id)
);
```

#### 📋 Logika Bisnis:

**Flow Checkout dengan Voucher:**

```
┌─────────────────────────────────────────────┐
│  USER: Masukkan kode "LEBARAN15"            │
└──────────────────┬──────────────────────────┘
                   │
                   ▼
┌──────────────────────────────────────────────┐
│  VALIDASI SISTEM:                            │
│  ✅ Kode valid & aktif?                      │
│  ✅ Belum expired?                           │
│  ✅ Quota masih ada?                         │
│  ✅ User belum exceed usage limit?           │
│  ✅ Cart total ≥ min_purchase?               │
│  ✅ Produk di cart eligible?                 │
└──────────────────┬───────────────────────────┘
                   │
      ┌────────────┴────────────┐
      │ VALID?                  │
      ▼                         ▼
   ✅ YA                      ❌ TIDAK
      │                         │
      ▼                         ▼
  HITUNG DISKON          SHOW ERROR MESSAGE
      │                  "Kode tidak valid/expired"
      ▼
┌─────────────────────────────────────────────┐
│ PERHITUNGAN:                                │
│                                             │
│ Subtotal Produk    : Rp 200.000            │
│ Ongkir             : Rp  15.000            │
│ ─────────────────────────────              │
│ Total Sebelum Disc : Rp 215.000            │
│ Diskon (15%)       : Rp  30.000  ❌ OVER!  │
│ Max Diskon         : Rp  20.000  ✅        │
│ ─────────────────────────────              │
│ GRAND TOTAL        : Rp 195.000            │
└─────────────────────────────────────────────┘
```

**Controller Logic (Pseudo Code):**

```php
// CouponController.php
public function applyCoupon(Request $request)
{
    $code = $request->input('code');
    $cartTotal = $this->calculateCartSubtotal();

    // 1. Cari kupon
    $coupon = Coupon::where('code', $code)
        ->where('is_active', true)
        ->with('promotion')
        ->first();

    if (!$coupon) {
        return response()->json(['error' => 'Kode tidak valid'], 400);
    }

    $promo = $coupon->promotion;

    // 2. Validasi tanggal
    $now = now();
    if ($now < $promo->start_date || $now > $promo->end_date) {
        return response()->json(['error' => 'Promo sudah expired'], 400);
    }

    // 3. Validasi quota
    if ($promo->quota && $promo->used_count >= $promo->quota) {
        return response()->json(['error' => 'Kuota promo habis'], 400);
    }

    // 4. Validasi usage per user
    $userUsage = CouponUsage::where('coupon_id', $coupon->id)
        ->where('user_id', auth()->id())
        ->count();

    if ($userUsage >= $coupon->max_usage_per_user) {
        return response()->json(['error' => 'Anda sudah menggunakan kupon ini'], 400);
    }

    // 5. Validasi minimal pembelian
    if ($promo->min_purchase && $cartTotal < $promo->min_purchase) {
        return response()->json([
            'error' => "Minimal belanja Rp " . number_format($promo->min_purchase, 0, ',', '.')
        ], 400);
    }

    // 6. Hitung diskon
    $discount = $this->calculateDiscount($promo, $cartTotal);

    // 7. Simpan ke session
    session(['applied_coupon' => [
        'coupon_id' => $coupon->id,
        'code' => $code,
        'discount' => $discount,
        'promo_name' => $promo->name
    ]]);

    return response()->json([
        'success' => true,
        'discount' => $discount,
        'message' => "Selamat! Anda hemat Rp " . number_format($discount, 0, ',', '.')
    ]);
}

private function calculateDiscount($promo, $subtotal)
{
    switch ($promo->type) {
        case 'percentage':
            $discount = ($subtotal * $promo->discount_value) / 100;
            // Apply max_discount cap
            if ($promo->max_discount && $discount > $promo->max_discount) {
                $discount = $promo->max_discount;
            }
            return $discount;

        case 'fixed':
            return min($promo->discount_value, $subtotal);

        case 'free_shipping':
            return $this->getShippingCost();

        default:
            return 0;
    }
}
```

#### 🎨 UI/UX Implementation:

**Di Halaman Cart (resources/views/store/cart.blade.php):**

```html
<!-- Ringkasan Pesanan -->
<div class="store-glass-card rounded-2xl p-6">
    <h3 class="font-bold text-white text-lg mb-4">Ringkasan Pesanan</h3>

    <!-- Voucher Input (BARU) -->
    <div
        class="mb-4 p-4 rounded-xl"
        style="background: rgba(6,182,212,0.1); border: 1px solid rgba(6,182,212,0.2);"
    >
        <label class="text-sm text-white/70 mb-2 block">
            <i class="fas fa-ticket-alt text-cyan-400"></i> Punya Kode Promo?
        </label>
        <div class="flex gap-2">
            <input
                type="text"
                id="coupon-code"
                placeholder="Masukkan kode (contoh: LEBARAN15)"
                class="flex-1 input-premium text-sm"
                style="text-transform: uppercase;"
            />
            <button
                onclick="applyCoupon()"
                class="btn-primary px-4 py-2 text-sm whitespace-nowrap"
            >
                Gunakan
            </button>
        </div>
        <div id="coupon-feedback" class="mt-2 text-sm"></div>
    </div>

    <!-- Breakdown Harga -->
    <div class="space-y-3 mb-6">
        <div class="flex justify-between text-white/60">
            <span>Subtotal ({{ count($cartItems) }} produk)</span>
            <span class="font-semibold text-white/80">
                Rp {{ number_format($subtotal, 0, ',', '.') }}
            </span>
        </div>

        <!-- Diskon (Conditional) -->
        @if(session('applied_coupon'))
        <div class="flex justify-between text-green-400 animate-pulse">
            <span>
                <i class="fas fa-tag"></i> Diskon ({{
                session('applied_coupon.code') }})
            </span>
            <span class="font-bold">
                - Rp {{ number_format(session('applied_coupon.discount'), 0,
                ',', '.') }}
            </span>
        </div>
        @endif

        <div class="flex justify-between text-white/60">
            <span>Ongkir</span>
            <span class="font-semibold text-white/80">
                Rp {{ number_format($shippingCost, 0, ',', '.') }}
            </span>
        </div>

        <hr class="border-white/10" />

        <div class="flex justify-between text-lg">
            <span class="font-bold text-white">Total Bayar</span>
            <span class="font-extrabold text-cyan-300">
                Rp {{ number_format($grandTotal, 0, ',', '.') }}
            </span>
        </div>
    </div>

    <button class="btn-primary w-full py-4">
        <i class="fas fa-credit-card mr-2"></i>Checkout Sekarang
    </button>
</div>
```

**JavaScript (AJAX):**

```javascript
function applyCoupon() {
    const code = document
        .getElementById("coupon-code")
        .value.trim()
        .toUpperCase();
    const feedback = document.getElementById("coupon-feedback");

    if (!code) {
        feedback.innerHTML =
            '<span class="text-red-400">Masukkan kode promo</span>';
        return;
    }

    // Loading state
    feedback.innerHTML =
        '<span class="text-cyan-400"><i class="fas fa-spinner fa-spin"></i> Memvalidasi...</span>';

    fetch("/cart/apply-coupon", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify({ code }),
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                feedback.innerHTML = `<span class="text-green-400">✅ ${data.message}</span>`;
                // Reload page untuk update total
                setTimeout(() => location.reload(), 1000);
            } else {
                feedback.innerHTML = `<span class="text-red-400">❌ ${data.error}</span>`;
            }
        })
        .catch((err) => {
            feedback.innerHTML =
                '<span class="text-red-400">Terjadi kesalahan</span>';
        });
}
```

#### 📱 Admin Panel - Promo Management:

**Menu Sidebar Admin (Tambahan):**

```
MARKETING 🆕
├── 🎁 Kelola Promo
├── 🎫 Kelola Kupon
└── 📊 Laporan Promo
```

**Halaman `/admin/promotions` (CRUD Promo):**

- **List Promo:** Tampilkan semua promo (aktif/non-aktif, expired/upcoming)
- **Create Promo:** Form untuk buat promo baru
- **Edit Promo:** Update detail promo
- **Analytics:**
    - Total penggunaan per promo
    - Total diskon yang diberikan
    - Conversion rate (pembeli dengan vs tanpa promo)

**Example Data Admin:**

```
┌─────────────────────────────────────────────────────────────────┐
│ PROMO AKTIF                                                     │
├─────────────────────────────────────────────────────────────────┤
│ Nama       : Flash Sale Weekend                               │
│ Kode       : WEEKEND20                                        │
│ Tipe       : Diskon 20% (Max Rp 30.000)                       │
│ Min. Beli  : Rp 150.000                                       │
│ Periode    : 12 Feb - 14 Feb 2026                             │
│ Kuota      : 100 / 150 (66% terpakai) ⚠️                      │
│ Total Hemat: Rp 2.450.000 (untuk customers)                   │
│ Conversion : 45% (90 dari 200 visitor)                        │
└─────────────────────────────────────────────────────────────────┘
```

#### 🎯 Use Cases (Customer Journey):

**Scenario 1: First-Time Buyer**

```
1. User baru register
2. Sistem auto-generate kupon "WELCOME10" (diskon 10%, max Rp 15.000, 1x pakai)
3. Kirim email: "Selamat datang! Gunakan kode WELCOME10 untuk belanja pertama"
4. User checkout → hemat Rp 15.000
5. Conversion rate naik 30%
```

**Scenario 2: Abandoned Cart Recovery**

```
1. User add to cart tapi tidak checkout
2. Setelah 24 jam, sistem kirim email: "Keranjangmu menunggu! Gunakan COMEBACK15"
3. User kembali, pakai voucher
4. Reduce cart abandonment -40%
```

**Scenario 3: Seasonal Campaign**

```
1. Admin buat promo "LEBARAN25" (diskon 25%, min. Rp 200.000, quota 200)
2. Admin blast WhatsApp/Email ke seluruh customer
3. User ramai-ramai checkout
4. Omset naik 150% selama periode promo
```

---

### 2️⃣ **LOYALTY PROGRAM & CUSTOMER TIER**

#### 💡 Kenapa Penting?

- **Meningkatkan customer lifetime value** (CLV)
- **Mendorong repeat purchase** (beli lagi dan lagi)
- **Reduce customer churn** (customer tidak pindah ke kompetitor)
- **Building brand loyalty** (customer jadi fans)

#### 📊 Business Impact:

- **Repeat purchase rate +40%**
- **Average order frequency +60%** (dari 2x/tahun jadi 3-4x/tahun)
- **Customer retention +50%**
- **Word-of-mouth referrals +35%** (customer VIP promosikan ke teman)

#### 🛠️ Implementasi Teknis:

**Database Schema:**

```sql
-- Tambahkan kolom di tabel users
ALTER TABLE users ADD COLUMN loyalty_points INTEGER DEFAULT 0;
ALTER TABLE users ADD COLUMN tier VARCHAR(20) DEFAULT 'Bronze';
ALTER TABLE users ADD COLUMN tier_updated_at TIMESTAMP NULL;

-- Tabel Riwayat Points
CREATE TABLE loyalty_transactions (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    order_id BIGINT NULL,              -- Null jika bukan dari order
    points INTEGER,                     -- Positif = dapat, negatif = pakai
    type ENUM('earn', 'redeem', 'bonus', 'expired'),
    description VARCHAR(255),
    balance_after INTEGER,              -- Saldo setelah transaksi
    created_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- Tabel Tier Benefits (config)
CREATE TABLE tier_benefits (
    id BIGINT PRIMARY KEY,
    tier_name VARCHAR(20),              -- Bronze, Silver, Gold, Platinum
    min_points INTEGER,                 -- Min points untuk masuk tier
    min_orders INTEGER,                 -- Min completed orders
    min_lifetime_spent DECIMAL(12,2),  -- Min total belanja
    discount_percentage DECIMAL(4,2),   -- Auto discount (contoh: 5%)
    free_shipping_threshold DECIMAL(10,2), -- Gratis ongkir di atas Rp X
    points_multiplier DECIMAL(3,2),     -- Dapat points x2, x3, dst
    priority_support BOOLEAN,           -- Chat priority
    exclusive_products BOOLEAN,         -- Akses produk eksklusif
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Seed Data untuk Tier:**

```php
// database/seeders/TierBenefitsSeeder.php
DB::table('tier_benefits')->insert([
    [
        'tier_name' => 'Bronze',
        'min_points' => 0,
        'min_orders' => 0,
        'min_lifetime_spent' => 0,
        'discount_percentage' => 0,
        'free_shipping_threshold' => null,
        'points_multiplier' => 1.0,
        'priority_support' => false,
        'exclusive_products' => false
    ],
    [
        'tier_name' => 'Silver',
        'min_points' => 500,
        'min_orders' => 3,
        'min_lifetime_spent' => 500000, // Rp 500.000
        'discount_percentage' => 3,
        'free_shipping_threshold' => 150000,
        'points_multiplier' => 1.2,
        'priority_support' => false,
        'exclusive_products' => false
    ],
    [
        'tier_name' => 'Gold',
        'min_points' => 2000,
        'min_orders' => 10,
        'min_lifetime_spent' => 2000000, // Rp 2 juta
        'discount_percentage' => 5,
        'free_shipping_threshold' => 100000,
        'points_multiplier' => 1.5,
        'priority_support' => true,
        'exclusive_products' => true
    ],
    [
        'tier_name' => 'Platinum',
        'min_points' => 5000,
        'min_orders' => 25,
        'min_lifetime_spent' => 5000000, // Rp 5 juta
        'discount_percentage' => 10,
        'free_shipping_threshold' => 50000,
        'points_multiplier' => 2.0,
        'priority_support' => true,
        'exclusive_products' => true
    ]
]);
```

#### 📋 Logika Bisnis:

**1. Earning Points (Dapat Poin):**

```php
// OrderObserver.php (ketika order completed)
public function updated(Order $order)
{
    if ($order->isDirty('status') && $order->status === 'completed') {
        // Hitung points: Rp 10.000 = 1 point
        $basePoints = floor($order->total_price / 10000);

        // Apply multiplier berdasarkan tier
        $user = $order->user;
        $tierBenefit = TierBenefit::where('tier_name', $user->tier)->first();
        $earnedPoints = $basePoints * $tierBenefit->points_multiplier;

        // Tambahkan points
        $user->increment('loyalty_points', $earnedPoints);

        // Log transaksi
        LoyaltyTransaction::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'points' => $earnedPoints,
            'type' => 'earn',
            'description' => "Pembelian Order #{$order->order_number}",
            'balance_after' => $user->loyalty_points
        ]);

        // Check tier upgrade
        $this->checkTierUpgrade($user);
    }
}

private function checkTierUpgrade($user)
{
    $stats = [
        'total_orders' => $user->orders()->where('status', 'completed')->count(),
        'lifetime_spent' => $user->orders()->where('status', 'completed')->sum('total_price'),
        'points' => $user->loyalty_points
    ];

    // Cari tier tertinggi yang eligible
    $eligibleTier = TierBenefit::where('min_orders', '<=', $stats['total_orders'])
        ->where('min_lifetime_spent', '<=', $stats['lifetime_spent'])
        ->where('min_points', '<=', $stats['points'])
        ->orderBy('min_points', 'desc')
        ->first();

    if ($eligibleTier && $eligibleTier->tier_name !== $user->tier) {
        // Upgrade tier
        $user->update([
            'tier' => $eligibleTier->tier_name,
            'tier_updated_at' => now()
        ]);

        // Notifikasi user
        AdminNotification::create([
            'type' => 'tier_upgrade',
            'message' => "🎉 Selamat! Anda naik ke tier {$eligibleTier->tier_name}",
            'user_id' => $user->id
        ]);

        // Kirim email
        Mail::to($user->email)->send(new TierUpgradeMail($user, $eligibleTier));
    }
}
```

**2. Redeeming Points (Tukar Poin):**

```php
// CheckoutController.php
public function applyLoyaltyPoints(Request $request)
{
    $pointsToRedeem = $request->input('points');
    $user = auth()->user();

    // Validasi
    if ($pointsToRedeem > $user->loyalty_points) {
        return response()->json(['error' => 'Poin tidak cukup'], 400);
    }

    // Konversi: 100 points = Rp 10.000
    $discount = ($pointsToRedeem / 100) * 10000;
    $cartTotal = $this->getCartTotal();

    // Max redeem = 50% dari cart total
    $maxDiscount = $cartTotal * 0.5;
    if ($discount > $maxDiscount) {
        $discount = $maxDiscount;
        $pointsToRedeem = ($discount / 10000) * 100;
    }

    // Simpan ke session
    session(['loyalty_redeem' => [
        'points' => $pointsToRedeem,
        'discount' => $discount
    ]]);

    return response()->json([
        'success' => true,
        'discount' => $discount,
        'message' => "Anda menggunakan {$pointsToRedeem} poin (hemat Rp " . number_format($discount, 0) . ")"
    ]);
}
```

**3. Auto Discount untuk Tier Tinggi:**

```php
// CartController.php
public function calculateTotal()
{
    $subtotal = $this->getCartSubtotal();
    $user = auth()->user();

    // Auto discount berdasarkan tier
    $tierBenefit = TierBenefit::where('tier_name', $user->tier)->first();
    $autoDiscount = 0;

    if ($tierBenefit->discount_percentage > 0) {
        $autoDiscount = ($subtotal * $tierBenefit->discount_percentage) / 100;
    }

    // Free shipping check
    $shippingCost = $this->getShippingCost();
    if ($tierBenefit->free_shipping_threshold && $subtotal >= $tierBenefit->free_shipping_threshold) {
        $shippingCost = 0;
    }

    $grandTotal = $subtotal - $autoDiscount + $shippingCost;

    return [
        'subtotal' => $subtotal,
        'tier_discount' => $autoDiscount,
        'shipping' => $shippingCost,
        'grand_total' => $grandTotal
    ];
}
```

#### 🎨 UI/UX Implementation:

**Badge Tier di Navbar:**

```html
<!-- Navbar (resources/views/layouts/master.blade.php) -->
<div class="flex items-center gap-3">
    <!-- Profile Picture -->
    <img
        src="{{ auth()->user()->foto ? asset('storage/' . auth()->user()->foto) : asset('images/default-avatar.png') }}"
        class="w-10 h-10 rounded-full object-cover border-2 border-cyan-400"
    />

    <!-- User Info + Tier Badge -->
    <div class="hidden lg:block">
        <p class="text-white font-semibold text-sm">
            {{ auth()->user()->name }}
        </p>
        <div class="flex items-center gap-2">
            <!-- Tier Badge (BARU) -->
            @php $tierColors = [ 'Bronze' => 'from-orange-700 to-orange-600',
            'Silver' => 'from-gray-500 to-gray-400', 'Gold' => 'from-yellow-600
            to-yellow-500', 'Platinum' => 'from-purple-600 to-purple-500' ];
            @endphp
            <span
                class="px-2 py-0.5 rounded-full text-[10px] font-bold text-white"
                style="background: linear-gradient(135deg, {{ $tierColors[auth()->user()->tier] ?? 'gray' }});"
            >
                <i class="fas fa-crown"></i> {{ auth()->user()->tier }}
            </span>

            <!-- Points -->
            <span class="text-cyan-400 text-xs font-semibold">
                <i class="fas fa-coins"></i> {{
                number_format(auth()->user()->loyalty_points) }} pts
            </span>
        </div>
    </div>
</div>
```

**Halaman Loyalty Dashboard (`/loyalty`):**

```html
<!-- resources/views/store/loyalty.blade.php -->
@extends('layouts.master') @section('content')
<section class="py-12">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <h1 class="text-3xl font-bold text-white mb-8">
            <i class="fas fa-gift text-cyan-400"></i> Program Loyalitas
        </h1>

        <!-- Current Tier Card -->
        <div class="grid lg:grid-cols-3 gap-6 mb-8">
            <!-- Tier Status -->
            <div class="lg:col-span-2 card-elevated rounded-2xl p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <p class="text-white/60 text-sm mb-2">
                            Tier Anda Saat Ini
                        </p>
                        <h2 class="text-4xl font-extrabold text-white">
                            {{ $user->tier }}
                        </h2>
                    </div>
                    <div
                        class="w-24 h-24 rounded-full flex items-center justify-center"
                        style="background: linear-gradient(135deg, {{ $tierColors[$user->tier] }});"
                    >
                        <i class="fas fa-crown text-white text-4xl"></i>
                    </div>
                </div>

                <!-- Progress ke Next Tier -->
                @if($nextTier)
                <div>
                    <div
                        class="flex justify-between text-sm text-white/60 mb-2"
                    >
                        <span>Progress ke {{ $nextTier->tier_name }}</span>
                        <span>{{ $progressPercentage }}%</span>
                    </div>
                    <div class="h-3 bg-white/10 rounded-full overflow-hidden">
                        <div
                            class="h-full bg-gradient-to-r from-cyan-500 to-purple-500 rounded-full transition-all"
                            style="width: {{ $progressPercentage }}%;"
                        ></div>
                    </div>
                    <p class="text-white/60 text-xs mt-2">
                        Butuh
                        <strong class="text-white"
                            >{{ number_format($pointsNeeded) }} poin</strong
                        >
                        lagi untuk naik tier
                    </p>
                </div>
                @else
                <p class="text-cyan-400 font-semibold">
                    🎉 Anda sudah di tier tertinggi!
                </p>
                @endif
            </div>

            <!-- Points Balance -->
            <div class="card-elevated rounded-2xl p-8">
                <p class="text-white/60 text-sm mb-2">Total Poin</p>
                <h3 class="text-5xl font-extrabold text-cyan-400 mb-4">
                    {{ number_format($user->loyalty_points) }}
                </h3>
                <p class="text-white/60 text-sm mb-4">
                    Setara dengan
                    <strong class="text-white"
                        >Rp {{ number_format(($user->loyalty_points / 100) *
                        10000, 0) }}</strong
                    >
                </p>
                <a href="#redeem" class="btn-primary w-full text-center">
                    Tukar Poin
                </a>
            </div>
        </div>

        <!-- Tier Benefits Comparison -->
        <div class="card-elevated rounded-2xl p-8 mb-8">
            <h3 class="text-2xl font-bold text-white mb-6">Benefit Per Tier</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-white">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="py-3 px-4 text-left">Benefit</th>
                            @foreach($tiers as $tier)
                            <th class="py-3 px-4 text-center">
                                {{ $tier->tier_name }}
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr class="border-b border-white/10">
                            <td class="py-3 px-4">Diskon Otomatis</td>
                            @foreach($tiers as $tier)
                            <td class="py-3 px-4 text-center">
                                {{ $tier->discount_percentage > 0 ?
                                $tier->discount_percentage . '%' : '-' }}
                            </td>
                            @endforeach
                        </tr>
                        <tr class="border-b border-white/10">
                            <td class="py-3 px-4">Points Multiplier</td>
                            @foreach($tiers as $tier)
                            <td class="py-3 px-4 text-center">
                                {{ $tier->points_multiplier }}x
                            </td>
                            @endforeach
                        </tr>
                        <tr class="border-b border-white/10">
                            <td class="py-3 px-4">Gratis Ongkir</td>
                            @foreach($tiers as $tier)
                            <td class="py-3 px-4 text-center text-xs">
                                {{ $tier->free_shipping_threshold ? '≥ Rp ' .
                                number_format($tier->free_shipping_threshold, 0)
                                : '-' }}
                            </td>
                            @endforeach
                        </tr>
                        <tr class="border-b border-white/10">
                            <td class="py-3 px-4">Priority Support</td>
                            @foreach($tiers as $tier)
                            <td class="py-3 px-4 text-center">
                                {!! $tier->priority_support ? '<i
                                    class="fas fa-check text-green-400"
                                ></i
                                >' : '<i class="fas fa-times text-red-400"></i>'
                                !!}
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="card-elevated rounded-2xl p-8">
            <h3 class="text-2xl font-bold text-white mb-6">Riwayat Poin</h3>

            <div class="space-y-3">
                @foreach($loyaltyTransactions as $transaction)
                <div
                    class="flex items-center justify-between p-4 rounded-xl"
                    style="background: rgba(255,255,255,0.05);"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-full flex items-center justify-center {{ $transaction->type === 'earn' ? 'bg-green-500/20' : 'bg-red-500/20' }}"
                        >
                            <i
                                class="fas {{ $transaction->type === 'earn' ? 'fa-plus text-green-400' : 'fa-minus text-red-400' }} text-xl"
                            ></i>
                        </div>
                        <div>
                            <p class="text-white font-semibold">
                                {{ $transaction->description }}
                            </p>
                            <p class="text-white/50 text-xs">
                                {{ $transaction->created_at->format('d M Y,
                                H:i') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p
                            class="text-xl font-bold {{ $transaction->type === 'earn' ? 'text-green-400' : 'text-red-400' }}"
                        >
                            {{ $transaction->type === 'earn' ? '+' : '-' }}{{
                            number_format($transaction->points) }} pts
                        </p>
                        <p class="text-white/50 text-xs">
                            Saldo: {{ number_format($transaction->balance_after)
                            }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
```

#### 📱 Admin Panel - Loyalty Management:

**Menu Sidebar:**

```
CUSTOMERS
├── 👥 Manajemen User
├── ⭐ Tier & Loyalty  🆕
└── 📊 Customer Analytics  🆕
```

**Halaman `/admin/loyalty`:**

```
┌─────────────────────────────────────────────────────────┐
│ STATISTIK LOYALTY PROGRAM                               │
├─────────────────────────────────────────────────────────┤
│ Total Points Dalam Sirkulasi: 125,340 pts              │
│ Points Digunakan (Redeem):     45,200 pts              │
│ Rata-rata Points per User:     523 pts                 │
│                                                         │
│ DISTRIBUSI TIER:                                        │
│ 🥉 Bronze   : 150 users (60%)                          │
│ 🥈 Silver   :  65 users (26%)                          │
│ 🥇 Gold     :  30 users (12%)                          │
│ 💎 Platinum :   5 users (2%)                           │
│                                                         │
│ TOP SPENDERS (Platinum Tier):                          │
│ 1. Ahmad Fauzi    - 8,340 pts - Rp 12.5 juta lifetime │
│ 2. Siti Nurhaliza - 7,120 pts - Rp 10.2 juta lifetime │
│ 3. Budi Santoso   - 6,890 pts - Rp  9.8 juta lifetime │
└─────────────────────────────────────────────────────────┘
```

---

### 3️⃣ **FLASH SALE & COUNTDOWN TIMER**

#### 💡 Kenapa Penting?

- **Menciptakan urgency** (FOMO - Fear of Missing Out)
- **Meningkatkan konversi** drastis dalam waktu singkat
- **Menghabiskan stok lama** (produk slow-moving)
- **Generate buzz** di social media

#### 📊 Business Impact:

- **Conversion rate +60-80%** selama flash sale
- **Traffic spike +200%** di jam-jam flash sale
- **Average order value +30%** (customer beli lebih banyak)
- **Social media engagement +100%** (customer share)

#### 🛠️ Implementasi Teknis:

**Database Schema:**

```sql
CREATE TABLE flash_sales (
    id BIGINT PRIMARY KEY,
    produk_id BIGINT,
    original_price DECIMAL(10,2),       -- Harga normal
    flash_price DECIMAL(10,2),          -- Harga flash sale
    stock_allocated INTEGER,            -- Stok khusus flash sale
    stock_sold INTEGER DEFAULT 0,
    start_time DATETIME,
    end_time DATETIME,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (produk_id) REFERENCES produks(id)
);

-- Relasi flash sale ke orders (untuk mencegah abuse)
CREATE TABLE flash_sale_orders (
    flash_sale_id BIGINT,
    order_id BIGINT,
    qty DECIMAL(8,2),
    FOREIGN KEY (flash_sale_id) REFERENCES flash_sales(id),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    PRIMARY KEY (flash_sale_id, order_id)
);
```

#### 📋 Logika Bisnis:

**Controller Logic:**

```php
// FlashSaleController.php
public function getActiveFlashSales()
{
    $now = now();

    return FlashSale::where('is_active', true)
        ->where('start_time', '<=', $now)
        ->where('end_time', '>', $now)
        ->where('stock_allocated', '>', 'stock_sold')
        ->with('produk')
        ->get()
        ->map(function($flashSale) {
            $flashSale->remaining_stock = $flashSale->stock_allocated - $flashSale->stock_sold;
            $flashSale->discount_percentage = round((($flashSale->original_price - $flashSale->flash_price) / $flashSale->original_price) * 100);
            $flashSale->time_remaining = $flashSale->end_time->diffInSeconds(now());
            return $flashSale;
        });
}

public function addFlashSaleToCart(Request $request, FlashSale $flashSale)
{
    $now = now();

    // Validasi waktu
    if ($now < $flashSale->start_time || $now > $flashSale->end_time) {
        return response()->json(['error' => 'Flash sale sudah berakhir'], 400);
    }

    // Validasi stok
    if ($flashSale->stock_sold >= $flashSale->stock_allocated) {
        return response()->json(['error' => 'Stok flash sale habis'], 400);
    }

    $qty = $request->input('qty', 1);

    // Validasi qty vs remaining stock
    $remainingStock = $flashSale->stock_allocated - $flashSale->stock_sold;
    if ($qty > $remainingStock) {
        return response()->json(['error' => "Stok tersisa hanya {$remainingStock} kg"], 400);
    }

    // Limit 1 flash sale product per user (opsional, untuk fairness)
    $existingOrder = FlashSaleOrder::where('flash_sale_id', $flashSale->id)
        ->whereHas('order', function($q) {
            $q->where('user_id', auth()->id());
        })
        ->exists();

    if ($existingOrder) {
        return response()->json(['error' => 'Anda sudah membeli produk flash sale ini'], 400);
    }

    // Add to cart dengan harga flash sale
    $cart = session('cart', []);
    $cart[$flashSale->produk_id] = [
        'qty' => $qty,
        'price' => $flashSale->flash_price,  // Gunakan flash price
        'flash_sale_id' => $flashSale->id,   // Tag flash sale
    ];
    session(['cart' => $cart]);

    return response()->json([
        'success' => true,
        'message' => 'Produk flash sale ditambahkan ke keranjang!'
    ]);
}
```

**Observer untuk Track Flash Sale:**

```php
// OrderObserver.php
public function created(Order $order)
{
    // Check if order contains flash sale items
    foreach ($order->items as $item) {
        if (session("cart.{$item->produk_id}.flash_sale_id")) {
            $flashSaleId = session("cart.{$item->produk_id}.flash_sale_id");
            $flashSale = FlashSale::find($flashSaleId);

            // Increment stock_sold
            $flashSale->increment('stock_sold', $item->qty);

            // Log order ke flash_sale_orders
            FlashSaleOrder::create([
                'flash_sale_id' => $flashSaleId,
                'order_id' => $order->id,
                'qty' => $item->qty
            ]);
        }
    }
}
```

#### 🎨 UI/UX Implementation:

**Banner Flash Sale di Homepage:**

```html
<!-- resources/views/home.blade.php -->
@if($activeFlashSales->count() > 0)
<section
    class="py-8 bg-gradient-to-r from-red-600 to-pink-600 relative overflow-hidden"
>
    <!-- Animated Background -->
    <div class="absolute inset-0 opacity-20">
        <div
            class="animate-pulse absolute top-0 left-0 w-64 h-64 bg-white rounded-full blur-3xl"
        ></div>
        <div
            class="animate-pulse absolute bottom-0 right-0 w-96 h-96 bg-yellow-300 rounded-full blur-3xl"
        ></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 relative z-10">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-3 mb-4">
                <i
                    class="fas fa-bolt text-yellow-300 text-3xl animate-pulse"
                ></i>
                <h2 class="text-4xl font-black text-white">FLASH SALE</h2>
                <i
                    class="fas fa-bolt text-yellow-300 text-3xl animate-pulse"
                ></i>
            </div>
            <p class="text-white/90 text-xl font-semibold">
                Diskon Hingga 50%! Buruan Sebelum Kehabisan!
            </p>
        </div>

        <!-- Flash Sale Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($activeFlashSales as $flashSale)
            <div
                class="bg-white rounded-2xl p-5 shadow-2xl relative overflow-hidden"
            >
                <!-- Discount Badge -->
                <div
                    class="absolute top-0 right-0 bg-red-500 text-white px-4 py-2 rounded-bl-2xl font-black text-lg"
                >
                    -{{ $flashSale->discount_percentage }}%
                </div>

                <!-- Product Image -->
                <div
                    class="w-full aspect-square rounded-xl overflow-hidden mb-4 mt-8"
                >
                    <img
                        src="{{ asset('storage/' . $flashSale->produk->foto) }}"
                        alt="{{ $flashSale->produk->nama }}"
                        class="w-full h-full object-cover"
                    />
                </div>

                <!-- Product Name -->
                <h3 class="font-bold text-gray-800 text-lg mb-2">
                    {{ $flashSale->produk->nama }}
                </h3>

                <!-- Price -->
                <div class="flex items-baseline gap-2 mb-3">
                    <span class="text-2xl font-black text-red-600">
                        Rp {{ number_format($flashSale->flash_price, 0, ',',
                        '.') }}
                    </span>
                    <span class="text-sm text-gray-400 line-through">
                        Rp {{ number_format($flashSale->original_price, 0, ',',
                        '.') }}
                    </span>
                </div>

                <!-- Stock Progress Bar -->
                @php $stockPercentage = ($flashSale->stock_sold /
                $flashSale->stock_allocated) * 100; @endphp
                <div class="mb-3">
                    <div
                        class="flex justify-between text-xs text-gray-600 mb-1"
                    >
                        <span
                            >Terjual {{ $flashSale->stock_sold }}/{{
                            $flashSale->stock_allocated }} Kg</span
                        >
                        <span>{{ round($stockPercentage) }}%</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div
                            class="h-full bg-gradient-to-r from-red-500 to-pink-500 rounded-full transition-all"
                            style="width: {{ $stockPercentage }}%;"
                        ></div>
                    </div>
                </div>

                <!-- Countdown Timer -->
                <div class="bg-gray-100 rounded-xl p-3 mb-4">
                    <p class="text-xs text-gray-600 mb-1 text-center">
                        Berakhir dalam:
                    </p>
                    <div
                        class="flex justify-center gap-2 countdown-timer"
                        data-end-time="{{ $flashSale->end_time->timestamp }}"
                    >
                        <div class="text-center">
                            <div
                                class="bg-red-600 text-white font-black text-xl px-2 py-1 rounded"
                            >
                                <span class="hours">00</span>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">Jam</p>
                        </div>
                        <div
                            class="flex items-center text-2xl font-bold text-red-600"
                        >
                            :
                        </div>
                        <div class="text-center">
                            <div
                                class="bg-red-600 text-white font-black text-xl px-2 py-1 rounded"
                            >
                                <span class="minutes">00</span>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">Menit</p>
                        </div>
                        <div
                            class="flex items-center text-2xl font-bold text-red-600"
                        >
                            :
                        </div>
                        <div class="text-center">
                            <div
                                class="bg-red-600 text-white font-black text-xl px-2 py-1 rounded"
                            >
                                <span class="seconds">00</span>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">Detik</p>
                        </div>
                    </div>
                </div>

                <!-- CTA Button -->
                <button
                    onclick="addFlashSaleToCart({{ $flashSale->id }})"
                    class="w-full bg-gradient-to-r from-red-600 to-pink-600 text-white font-bold py-3 rounded-xl hover:scale-105 transition-transform"
                >
                    <i class="fas fa-shopping-cart mr-2"></i>Beli Sekarang!
                </button>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif @push('scripts')
<script>
    // Countdown Timer (Real-time)
    function initCountdowns() {
        document.querySelectorAll(".countdown-timer").forEach((timer) => {
            const endTime = parseInt(timer.dataset.endTime) * 1000;

            const updateTimer = () => {
                const now = Date.now();
                const distance = endTime - now;

                if (distance < 0) {
                    timer.innerHTML =
                        '<p class="text-red-600 font-bold">BERAKHIR!</p>';
                    clearInterval(interval);
                    // Reload page untuk update UI
                    setTimeout(() => location.reload(), 2000);
                    return;
                }

                const hours = Math.floor(distance / (1000 * 60 * 60));
                const minutes = Math.floor(
                    (distance % (1000 * 60 * 60)) / (1000 * 60),
                );
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                timer.querySelector(".hours").textContent = hours
                    .toString()
                    .padStart(2, "0");
                timer.querySelector(".minutes").textContent = minutes
                    .toString()
                    .padStart(2, "0");
                timer.querySelector(".seconds").textContent = seconds
                    .toString()
                    .padStart(2, "0");
            };

            updateTimer();
            const interval = setInterval(updateTimer, 1000);
        });
    }

    document.addEventListener("DOMContentLoaded", initCountdowns);

    function addFlashSaleToCart(flashSaleId) {
        // AJAX call untuk add to cart
        fetch(`/flash-sale/${flashSaleId}/add-to-cart`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]',
                ).content,
            },
            body: JSON.stringify({ qty: 1 }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    alert("✅ Produk flash sale ditambahkan! Segera checkout!");
                    window.location.href = "/cart";
                } else {
                    alert("❌ " + data.error);
                }
            });
    }
</script>
@endpush
```

#### 📱 Admin Panel - Flash Sale Management:

**Menu:**

```
MARKETING
├── 🎁 Kelola Promo
├── 🎫 Kelola Kupon
└── ⚡ Flash Sale  🆕
```

**Halaman `/admin/flash-sales`:**

- **Create Flash Sale:** Pilih produk, set harga flash, stock, waktu
- **Schedule:** Preview flash sale upcoming
- **Analytics:**
    - Conversion rate flash sale vs normal
    - Total revenue dari flash sale
    - Produk flash sale terlaris

---

## 🎯 PRIORITAS 2: USER EXPERIENCE & CONVENIENCE

### 4️⃣ **MULTIPLE PAYMENT METHODS**

#### 💡 Kenapa Penting?

- **Reduce checkout abandonment** (customer batal checkout karena metode bayar terbatas)
- **Mencakup berbagai segmen** (tidak semua punya e-wallet/credit card)
- **Meningkatkan trust** (banyak pilihan = profesional)

#### 📊 Business Impact:

- **Checkout completion rate +25%**
- **Reduce cart abandonment -30%**
- **Customer satisfaction +40%**

#### 🛠️ Implementasi:

**Payment Options (urutan prioritas):**

1. ✅ **Midtrans (SUDAH ADA)** → E-wallet, CC, VA
2. ✅ **Manual Transfer (SUDAH ADA)** → Bank BCA
3. 🆕 **COD (Cash on Delivery)** → Bayar saat terima
4. 🆕 **QRIS** → Scan QR bayar
5. 🆕 **Shopee Pay / Dana / OVO Direct** → Direct integration

**Untuk COD:**

```sql
ALTER TABLE orders ADD COLUMN payment_method VARCHAR(20) DEFAULT 'midtrans';
-- Possible values: 'midtrans', 'manual_transfer', 'cod', 'qris'

-- Untuk COD, perlu validasi khusus
ALTER TABLE orders ADD COLUMN cod_verified_at TIMESTAMP NULL;
ALTER TABLE orders ADD COLUMN cod_verified_by BIGINT NULL; -- Admin ID
```

**COD Logic:**

```php
// CheckoutController.php
public function checkout(Request $request)
{
    $paymentMethod = $request->input('payment_method', 'midtrans');

    $order = Order::create([
        // ... data order lainnya
        'payment_method' => $paymentMethod,
        'status' => $paymentMethod === 'cod' ? 'processing' : 'pending'
    ]);

    if ($paymentMethod === 'cod') {
        // Langsung ke processing (tidak perlu upload bukti bayar)
        // Admin akan verifikasi setelah barang diterima customer
        return redirect()->route('order.success', $order);
    }

    // ... logic payment gateway lainnya
}
```

---

### 5️⃣ **ADVANCED ORDER TRACKING**

#### 💡 Kenapa Penting?

- **Transparansi** → Customer tau posisi pesanan
- **Reduce customer support queries** → Tidak banyak yang tanya "pesanan saya dimana?"
- **Build trust** → Profesionalisme

#### 📊 Business Impact:

- **Customer support tickets -50%**
- **Customer satisfaction +35%**
- **Repeat purchase +20%** (karena trust naik)

#### 🛠️ Implementasi:

**Status Flow yang Lebih Detail:**

```
CURRENT:
pending → waiting_payment → processing → shipped → completed

ENHANCED:
pending →
waiting_payment →
payment_verified ���
preparing_order →
ready_to_ship →
shipped →
out_for_delivery →
delivered →
completed
```

**Timeline Tracking:**

```sql
CREATE TABLE order_status_history (
    id BIGINT PRIMARY KEY,
    order_id BIGINT,
    status VARCHAR(50),
    notes TEXT NULL,                      -- "Paket diserahkan ke kurir JNE"
    updated_by BIGINT NULL,               -- Admin ID
    location VARCHAR(100) NULL,           -- "Gudang Pusat Jakarta"
    created_at TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);
```

**UI Enhancement:**

```html
<!-- Halaman Tracking Detail -->
<div class="timeline">
    @foreach($order->statusHistory as $history)
    <div class="timeline-item {{ $loop->first ? 'active' : 'completed' }}">
        <div class="timeline-icon">
            <i class="fas fa-check"></i>
        </div>
        <div class="timeline-content">
            <h4>{{ ucfirst(str_replace('_', ' ', $history->status)) }}</h4>
            <p class="text-sm text-gray-600">{{ $history->notes }}</p>
            <span class="text-xs text-gray-400">
                {{ $history->created_at->format('d M Y, H:i') }}
                @if($history->location) • {{ $history->location }} @endif
            </span>
        </div>
    </div>
    @endforeach
</div>
```

**Real-time Notification:**

```php
// Ketika admin update status
public function updateOrderStatus(Request $request, Order $order)
{
    $newStatus = $request->input('status');
    $notes = $request->input('notes');

    // Update order
    $order->update(['status' => $newStatus]);

    // Log to history
    OrderStatusHistory::create([
        'order_id' => $order->id,
        'status' => $newStatus,
        'notes' => $notes,
        'updated_by' => auth()->id(),
        'location' => $request->input('location')
    ]);

    // Send email notification ke customer
    Mail::to($order->user->email)->send(new OrderStatusUpdatedMail($order, $newStatus));

    // Push notification (opsional, via Firebase/Pusher)
    event(new OrderStatusUpdated($order));

    return back()->with('success', 'Status order berhasil diupdate');
}
```

---

### 6️⃣ **WISHLIST NOTIFICATION (Back in Stock Alert)**

#### 💡 Kenapa Penting?

- **Recover lost sales** → Produk sold out tapi ada demand
- **Automatic remarketing** → Sistem otomatis "manggil" customer kembali
- **Zero marketing cost** → Email/notif gratis

#### 📊 Business Impact:

- **Conversion rate dari wishlist +45%**
- **Revenue recovery Rp 5-10 juta/bulan** (dari produk yang tadinya sold out)
- **Customer engagement +30%**

#### 🛠️ Implementasi:

**Database:**

```sql
-- Tambahan di tabel wishlists
ALTER TABLE wishlists ADD COLUMN notify_when_available BOOLEAN DEFAULT true;
ALTER TABLE wishlists ADD COLUMN notified_at TIMESTAMP NULL;
```

**Observer Logic:**

```php
// ProdukObserver.php (SUDAH ADA, tinggal tambahkan)
public function updated(Produk $produk)
{
    // Jika stok berubah dari 0 → >0 (restock)
    if ($produk->isDirty('stok') && $produk->getOriginal('stok') == 0 && $produk->stok > 0) {
        // Ambil semua user yang wishlist produk ini
        $wishlisters = Wishlist::where('produk_id', $produk->id)
            ->where('notify_when_available', true)
            ->whereNull('notified_at')
            ->with('user')
            ->get();

        foreach ($wishlisters as $wishlist) {
            // Kirim email
            Mail::to($wishlist->user->email)->send(new ProductBackInStockMail($produk, $wishlist->user));

            // Update notified_at
            $wishlist->update(['notified_at' => now()]);

            // Admin notification (opsional)
            AdminNotification::create([
                'type' => 'wishlist_conversion',
                'message' => "{$wishlist->user->name} dinotifikasi: {$produk->nama} kembali tersedia"
            ]);
        }
    }
}
```

**Email Template:**

```html
<!-- resources/views/emails/product-back-in-stock.blade.php -->
<!DOCTYPE html>
<html>
    <body
        style="font-family: Arial, sans-serif; background: #f3f4f6; padding: 20px;"
    >
        <div
            style="max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden;"
        >
            <!-- Header -->
            <div
                style="background: linear-gradient(135deg, #0891b2, #14b8a6); padding: 30px; text-align: center;"
            >
                <h1 style="color: white; margin: 0;">🎉 Kabar Gembira!</h1>
            </div>

            <!-- Content -->
            <div style="padding: 30px;">
                <p style="font-size: 16px; color: #333;">
                    Halo {{ $user->name }},
                </p>

                <p style="font-size: 16px; color: #666;">
                    Produk yang kamu tunggu-tunggu sudah
                    <strong style="color: #0891b2;">tersedia kembali</strong>!
                    🔥
                </p>

                <!-- Product Card -->
                <div
                    style="border: 2px solid #0891b2; border-radius: 12px; padding: 20px; margin: 20px 0;"
                >
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <img
                            src="{{ asset('storage/' . $produk->foto) }}"
                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"
                        />
                        <div>
                            <h2 style="margin: 0 0 10px 0; color: #333;">
                                {{ $produk->nama }}
                            </h2>
                            <p
                                style="margin: 0; font-size: 24px; font-weight: bold; color: #0891b2;"
                            >
                                Rp {{ number_format($produk->harga_per_kg, 0,
                                ',', '.') }}/Kg
                            </p>
                            <p
                                style="margin: 5px 0 0 0; color: #16a34a; font-weight: bold;"
                            >
                                ✅ Stok: {{ $produk->stok }} Kg tersedia
                            </p>
                        </div>
                    </div>
                </div>

                <p style="font-size: 14px; color: #666; text-align: center;">
                    ⚠️ Stok terbatas! Buruan pesan sebelum kehabisan lagi!
                </p>

                <!-- CTA Button -->
                <div style="text-align: center; margin: 30px 0;">
                    <a
                        href="{{ route('produk.show', $produk) }}"
                        style="display: inline-block; background: linear-gradient(135deg, #0891b2, #14b8a6); color: white; padding: 15px 40px; border-radius: 12px; text-decoration: none; font-weight: bold; font-size: 16px;"
                    >
                        🛒 BELI SEKARANG
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div
                style="background: #f3f4f6; padding: 20px; text-align: center;"
            >
                <p style="font-size: 12px; color: #999; margin: 0;">
                    Email ini dikirim karena kamu menambahkan produk ini ke
                    wishlist
                </p>
            </div>
        </div>
    </body>
</html>
```

---

## 🎯 PRIORITAS 3: ADMIN EFFICIENCY & ANALYTICS

### 7️⃣ **BULK ACTIONS & BATCH PROCESSING**

#### 💡 Kenapa Penting?

- **Hemat waktu admin** → Update 100 order sekaligus, bukan satu-satu
- **Reduce human error** → Select all, action once
- **Efisiensi operasional** → 1 admin bisa handle lebih banyak order

#### 🛠️ Implementasi:

**Di Halaman Orders Index:**

```html
<form action="{{ route('admin.orders.bulk-action') }}" method="POST">
    @csrf

    <!-- Checkbox Select All -->
    <div class="mb-4 flex items-center justify-between">
        <label class="flex items-center gap-2">
            <input type="checkbox" id="select-all" class="checkbox" />
            <span class="text-white">Pilih Semua</span>
        </label>

        <!-- Bulk Actions Dropdown -->
        <div class="flex gap-3">
            <select name="bulk_action" class="input-premium">
                <option value="">-- Pilih Aksi --</option>
                <option value="mark_processing">Ubah ke Processing</option>
                <option value="mark_shipped">Ubah ke Shipped</option>
                <option value="mark_completed">Ubah ke Completed</option>
                <option value="export_pdf">Export ke PDF</option>
                <option value="send_email">Kirim Email Reminder</option>
            </select>
            <button type="submit" class="btn-primary">
                <i class="fas fa-check mr-2"></i>Jalankan
            </button>
        </div>
    </div>

    <!-- Table Orders dengan Checkbox -->
    <table>
        <thead>
            <tr>
                <th><input type="checkbox" class="select-all-trigger" /></th>
                <th>Order ID</th>
                <!-- ... kolom lainnya -->
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>
                    <input
                        type="checkbox"
                        name="order_ids[]"
                        value="{{ $order->id }}"
                        class="order-checkbox"
                    />
                </td>
                <td>{{ $order->order_number }}</td>
                <!-- ... -->
            </tr>
            @endforeach
        </tbody>
    </table>
</form>
```

**Controller:**

```php
public function bulkAction(Request $request)
{
    $action = $request->input('bulk_action');
    $orderIds = $request->input('order_ids', []);

    if (empty($orderIds)) {
        return back()->with('error', 'Pilih minimal 1 order');
    }

    switch ($action) {
        case 'mark_processing':
            Order::whereIn('id', $orderIds)->update(['status' => 'processing']);
            $message = count($orderIds) . ' order diubah ke Processing';
            break;

        case 'mark_shipped':
            Order::whereIn('id', $orderIds)->update(['status' => 'shipped']);
            // Kirim email tracking ke semua customer
            $orders = Order::whereIn('id', $orderIds)->with('user')->get();
            foreach ($orders as $order) {
                Mail::to($order->user->email)->send(new OrderShippedMail($order));
            }
            $message = count($orderIds) . ' order diubah ke Shipped + email terkirim';
            break;

        case 'export_pdf':
            // Generate PDF untuk multiple orders
            $orders = Order::whereIn('id', $orderIds)->with('items.produk', 'user')->get();
            $pdf = PDF::loadView('admin.orders.bulk-pdf', compact('orders'));
            return $pdf->download('bulk-orders-' . now()->format('Y-m-d') . '.pdf');

        case 'send_email':
            // Kirim payment reminder
            $orders = Order::whereIn('id', $orderIds)
                ->where('status', 'pending')
                ->with('user')->get();
            foreach ($orders as $order) {
                Mail::to($order->user->email)->send(new PaymentReminderMail($order));
            }
            $message = count($orders) . ' email reminder terkirim';
            break;
    }

    return back()->with('success', $message);
}
```

---

### 8️⃣ **ADVANCED ANALYTICS & REPORTS**

#### 💡 Kenapa Penting?

- **Data-driven decision** → Decide pakai data, bukan feeling
- **Identify trends** → Produk mana yang naik/turun
- **Profitability analysis** → Produk mana yang paling untung

#### 📊 Metrics yang Perlu Ada:

**Dashboard Metrics (Tambahan):**

1. **Customer Metrics:**
    - New Customers This Month
    - Customer Retention Rate (repeat buyers %)
    - Average Customer Lifetime Value (CLV)
    - Churn Rate (customer yang tidak beli lagi)

2. **Product Performance:**
    - Best Sellers (by revenue & by qty)
    - Slow-Moving Products (stok lama tidak laku)
    - Product Profit Margin (produk mana paling untung)
    - Out of Stock Frequency (produk sering habis → perlu stock up)

3. **Sales Analytics:**
    - Sales by Hour/Day/Week (kapan peak time)
    - Sales by Region (zona ongkir mana yang paling banyak order)
    - Average Order Value (AOV) trend
    - Conversion Rate (visitor → buyer)

4. **Operational Metrics:**
    - Average Order Processing Time
    - Average Delivery Time
    - Return/Refund Rate
    - Payment Success Rate

**Halaman `/admin/analytics` (BARU):**

```html
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Chart: Sales vs Profit (7 hari) -->
    <div class="card p-6">
        <h3 class="text-lg font-bold mb-4">
            Sales vs Profit (7 Hari Terakhir)
        </h3>
        <canvas id="salesProfitChart"></canvas>
    </div>

    <!-- Chart: Top Products (by revenue) -->
    <div class="card p-6">
        <h3 class="text-lg font-bold mb-4">Top 5 Produk Terlaris</h3>
        <canvas id="topProductsChart"></canvas>
    </div>

    <!-- Chart: Customer Acquisition -->
    <div class="card p-6">
        <h3 class="text-lg font-bold mb-4">New vs Returning Customers</h3>
        <canvas id="customerAcquisitionChart"></canvas>
    </div>

    <!-- Table: Slow-Moving Products -->
    <div class="card p-6">
        <h3 class="text-lg font-bold mb-4">⚠️ Produk Slow-Moving</h3>
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Stok</th>
                    <th>Penjualan (30 hari)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($slowMovingProducts as $produk)
                <tr>
                    <td>{{ $produk->nama }}</td>
                    <td>{{ $produk->stok }} Kg</td>
                    <td>{{ $produk->sales_30d }} Kg</td>
                    <td>
                        <a
                            href="{{ route('admin.promotions.create', ['produk_id' => $produk->id]) }}"
                            class="btn-sm btn-warning"
                        >
                            Buat Promo
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
```

**Query untuk Slow-Moving Products:**

```php
// Produk dengan penjualan < 5 Kg dalam 30 hari terakhir
$slowMovingProducts = Produk::withCount([
    'orderItems as sales_30d' => function($query) {
        $query->select(DB::raw('COALESCE(SUM(qty), 0)'))
            ->whereHas('order', function($q) {
                $q->where('created_at', '>=', now()->subDays(30))
                  ->where('status', 'completed');
            });
    }
])
->having('sales_30d', '<', 5)
->where('stok', '>', 0)
->orderBy('sales_30d', 'asc')
->get();
```

---

### 9️⃣ **CUSTOMER SUPPORT TICKETING SYSTEM**

#### 💡 Kenapa Penting?

- **Organized support** → Semua komplain/pertanyaan terorganisir
- **Track resolution time** → Berapa lama handle 1 ticket
- **Customer satisfaction** → Customer feels heard

#### 🛠️ Implementasi:

**Database:**

```sql
CREATE TABLE support_tickets (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    order_id BIGINT NULL,              -- Terkait order tertentu atau tidak
    subject VARCHAR(255),
    category ENUM('order_issue', 'payment', 'product_quality', 'delivery', 'other'),
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('open', 'in_progress', 'waiting_customer', 'resolved', 'closed') DEFAULT 'open',
    assigned_to BIGINT NULL,           -- Admin ID yang handle
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    closed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id)
);

CREATE TABLE ticket_messages (
    id BIGINT PRIMARY KEY,
    ticket_id BIGINT,
    user_id BIGINT,
    message TEXT,
    is_admin BOOLEAN DEFAULT false,
    attachments JSON NULL,              -- Array of file paths
    created_at TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES support_tickets(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

**User Flow:**

```
1. Customer klik "Butuh Bantuan?" di Order Detail
2. Form: Pilih kategori, tulis subject & deskripsi, upload foto (opsional)
3. Submit → Ticket created
4. Admin dapat notifikasi
5. Admin assign ke diri sendiri, reply ticket
6. Customer dapat email notification
7. Customer reply lagi (di website)
8. Bolak-balik sampai resolved
9. Admin close ticket
```

**UI Customer - Create Ticket:**

```html
<form
    action="{{ route('tickets.store') }}"
    method="POST"
    enctype="multipart/form-data"
>
    @csrf
    <input type="hidden" name="order_id" value="{{ $order->id ?? null }}" />

    <div class="mb-4">
        <label>Kategori Masalah</label>
        <select name="category" required class="input-premium">
            <option value="order_issue">Masalah Pesanan</option>
            <option value="payment">Masalah Pembayaran</option>
            <option value="product_quality">Kualitas Produk</option>
            <option value="delivery">Pengiriman</option>
            <option value="other">Lainnya</option>
        </select>
    </div>

    <div class="mb-4">
        <label>Subjek</label>
        <input
            type="text"
            name="subject"
            placeholder="Contoh: Pesanan belum diterima padahal sudah 5 hari"
            required
            class="input-premium"
        />
    </div>

    <div class="mb-4">
        <label>Deskripsi Detail</label>
        <textarea
            name="message"
            rows="6"
            placeholder="Jelaskan masalah Anda dengan detail..."
            required
            class="input-premium"
        ></textarea>
    </div>

    <div class="mb-4">
        <label>Lampiran (opsional)</label>
        <input
            type="file"
            name="attachments[]"
            multiple
            accept="image/*,.pdf"
            class="input-premium"
        />
        <p class="text-xs text-white/50 mt-1">
            Upload foto produk/bukti transfer (max 5 file, max 2MB per file)
        </p>
    </div>

    <button type="submit" class="btn-primary">
        <i class="fas fa-paper-plane mr-2"></i>Kirim Tiket
    </button>
</form>
```

**Admin Panel - Ticket Management:**

```
┌─────────────────────────────────────────────────────────┐
│ SUPPORT TICKETS                                  [+ Buat]│
├─────────────────────────────────────────────────────────┤
│ Filter: [Semua] [Open] [In Progress] [Resolved]         │
│                                                          │
│ #1234 🔴 HIGH - Pesanan belum diterima 5 hari           │
│       Order #ORD-20260215-001 • Ahmad Fauzi            │
│       Dibuka: 12 Feb 2026, 10:30                        │
│       [Assign ke saya] [Lihat Detail]                   │
│                                                          │
│ #1233 🟡 MEDIUM - Ikan tidak segar                      │
│       Order #ORD-20260214-023 • Siti Nurhaliza         │
│       Assigned: Admin Budi                              │
│       [Lihat Detail]                                    │
└─────────────────────────────────────────────────────────┘
```

---

## 🎯 PRIORITAS 4: ADVANCED FEATURES (LONG TERM)

### 🔟 **PRODUCT VARIANTS (Ukuran Ikan)**

**Problem:** Saat ini 1 produk = 1 harga. Padahal ikan ukuran beda, harga beda.

**Solution:**

Lele Sangkuriang:

- Ukuran Kecil (500g-800g) → Rp 25.000/Kg
- Ukuran Sedang (800g-1.2Kg) → Rp 28.000/Kg
- Ukuran Besar (1.2Kg+) → Rp 32.000/Kg

**Database:**

```sql
CREATE TABLE product_variants (
    id BIGINT PRIMARY KEY,
    produk_id BIGINT,
    variant_name VARCHAR(100),          -- "Ukuran Kecil", "Ukuran Sedang"
    price_per_kg DECIMAL(10,2),
    stock DECIMAL(10,2),
    sku VARCHAR(50) UNIQUE,             -- "LELE-KECIL-001"
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (produk_id) REFERENCES produks(id)
);
```

---

### 1️⃣1️⃣ **AUTO-REORDER SUGGESTIONS (AI-Powered)**

**Problem:** Admin tidak tau kapan harus restock.

**Solution:** Sistem prediksi otomatis berdasarkan historical data.

```php
// PredictionService.php
public function predictReorderDate(Produk $produk)
{
    // Ambil data penjualan 90 hari terakhir
    $sales = OrderItem::where('produk_id', $produk->id)
        ->whereHas('order', fn($q) => $q->where('status', 'completed'))
        ->where('created_at', '>=', now()->subDays(90))
        ->sum('qty');

    // Average sales per day
    $avgSalesPerDay = $sales / 90;

    // Current stock
    $currentStock = $produk->stok;

    // Days until out of stock
    $daysRemaining = $avgSalesPerDay > 0 ? ceil($currentStock / $avgSalesPerDay) : 999;

    // Reorder point: ketika stok tinggal untuk 7 hari
    $reorderPoint = $avgSalesPerDay * 7;

    return [
        'days_remaining' => $daysRemaining,
        'should_reorder' => $currentStock <= $reorderPoint,
        'recommended_qty' => $avgSalesPerDay * 30, // Stock untuk 1 bulan
        'out_of_stock_date' => now()->addDays($daysRemaining)
    ];
}
```

**Admin Notification:**

```
⚠️ REORDER ALERT
Produk: Lele Sangkuriang
Stok saat ini: 25 Kg
Prediksi habis: 3 hari lagi (18 Feb 2026)
Rekomendasi: Order 150 Kg sekarang
```

---

### 1️⃣2️⃣ **REFERRAL PROGRAM (Customer Ajak Teman)**

**Logic:**

1. Customer A dapat link referral unik: `fishmarket.com/register?ref=USER123`
2. Customer B daftar lewat link tersebut
3. Customer B dapat diskon 10% first purchase
4. Customer A dapat 50 loyalty points (atau voucher Rp 20.000)

**Database:**

```sql
ALTER TABLE users ADD COLUMN referral_code VARCHAR(20) UNIQUE;
ALTER TABLE users ADD COLUMN referred_by BIGINT NULL;

CREATE TABLE referral_rewards (
    id BIGINT PRIMARY KEY,
    referrer_id BIGINT,                 -- Yang ngajak
    referred_id BIGINT,                 -- Yang diajak
    reward_type ENUM('points', 'voucher'),
    reward_value DECIMAL(10,2),
    status ENUM('pending', 'claimed') DEFAULT 'pending',
    claimed_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    FOREIGN KEY (referrer_id) REFERENCES users(id),
    FOREIGN KEY (referred_id) REFERENCES users(id)
);
```

---

## 📊 ROADMAP & PRIORITAS IMPLEMENTASI

### **PHASE 1: QUICK WINS (2-3 Minggu)** ⚡

**Goal:** Boost revenue segera

1. ✅ Voucher & Promo Code System
2. ✅ Flash Sale dengan Countdown Timer
3. ✅ COD Payment Method

**Expected ROI:**

- Revenue +30-50%
- Conversion rate +25%

---

### **PHASE 2: LOYALTY & RETENTION (3-4 Minggu)** 🎯

**Goal:** Build long-term customer relationship

1. ✅ Loyalty Program & Customer Tier
2. ✅ Wishlist Notification (Back in Stock)
3. ✅ Referral Program

**Expected ROI:**

- Customer retention +40%
- Repeat purchase rate +50%
- CLV (Customer Lifetime Value) +60%

---

### **PHASE 3: ADMIN EFFICIENCY (2-3 Minggu)** 🛠️

**Goal:** Skalabilitas operasional

1. ✅ Bulk Actions & Batch Processing
2. ✅ Advanced Analytics & Reports
3. ✅ Support Ticketing System

**Expected ROI:**

- Admin productivity +100% (1 admin bisa handle 2x order)
- Customer satisfaction +35%
- Data-driven decision making

---

### **PHASE 4: ADVANCED FEATURES (4-6 Minggu)** 🚀

**Goal:** Competitive advantage

1. ✅ Product Variants
2. ✅ Auto-Reorder Suggestions (AI)
3. ✅ Advanced Order Tracking
4. ✅ Multi-warehouse Management

**Expected ROI:**

- Market differentiation
- Premium pricing capability
- Operational excellence

---

## 🎯 KESIMPULAN & REKOMENDASI

### **Top 5 Fitur yang HARUS Diimplementasikan:**

1. **Voucher & Promo Code** → Direct impact ke conversion
2. **Loyalty Program** → Long-term profitability
3. **Flash Sale** → Viral marketing potential
4. **Advanced Analytics** → Data-driven growth
5. **Support Ticketing** → Customer satisfaction

### **Metrics untuk Track Success:**

```
SEBELUM IMPLEMENTASI:
├── Conversion Rate: 2-3%
├── Repeat Purchase Rate: 15%
├── Average Order Value: Rp 180.000
├── Customer Lifetime Value: Rp 500.000
└── Monthly Revenue: Rp 50 juta

TARGET SETELAH IMPLEMENTASI (3 bulan):
├── Conversion Rate: 5-6% (+100%)
├── Repeat Purchase Rate: 35% (+133%)
├── Average Order Value: Rp 250.000 (+39%)
├── Customer Lifetime Value: Rp 1.200.000 (+140%)
└── Monthly Revenue: Rp 120 juta (+140%)
```

### **Investment vs Return:**

```
ESTIMASI BIAYA DEVELOPMENT:
Phase 1-2: Rp 15-20 juta (1-1.5 bulan development)
Phase 3-4: Rp 25-30 juta (2-2.5 bulan development)
TOTAL: Rp 40-50 juta

ESTIMATED REVENUE INCREASE:
Bulan 1-3: +Rp 20 juta/bulan
Bulan 4-6: +Rp 40 juta/bulan
Bulan 7-12: +Rp 70 juta/bulan

ROI (Return on Investment):
Break-even: 2-3 bulan
ROI 12 bulan: 600-800%
```

---

**🚀 KESIMPULAN AKHIR:**

Proyek toko ikan ini **sudah solid di foundational features**, tapi **masih missing critical marketing & engagement tools**. Dengan menambahkan fitur-fitur di atas (terutama Phase 1 & 2), potensi revenue bisa naik **2-3x lipat** dalam 6 bulan.

**Prioritaskan:** Voucher System → Loyalty Program → Flash Sale → Analytics

**Mindset:** Dari "toko online biasa" menjadi **"professional e-commerce platform"** yang bisa compete dengan marketplace besar.

---

_Dokumen ini dibuat berdasarkan analisis mendalam code structure, business logic, dan best practices e-commerce modern. Semua fitur disesuaikan dengan konteks bisnis ikan segar dan target pasar Indonesia._

**Last Updated:** 15 Februari 2026
