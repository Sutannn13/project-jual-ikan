# Fitur Review Produk - Dokumentasi

## 📋 Overview

Fitur ini memungkinkan pelanggan untuk mereview produk yang sudah mereka beli, dengan logika validasi yang ketat memastikan hanya pembeli yang sudah menyelesaikan transaksi yang bisa memberikan review.

## ✨ Fitur Utama

### 1. **Validasi Review**

- ✅ Hanya user yang sudah login yang bisa review
- ✅ Hanya pesanan dengan status **COMPLETED** yang bisa direview
- ✅ User hanya bisa review produk yang **benar-benar ada** dalam pesanannya
- ✅ Satu produk dalam satu pesanan hanya bisa direview **satu kali**
- ✅ User bisa mereview produk yang sama jika membeli di pesanan berbeda

### 2. **Cara Kerja**

#### **Untuk User:**

1. **Melihat Produk yang Bisa Direview:**
    - Buka halaman "Pesanan Saya" (`/my-orders`)
    - Tab "Riwayat" menampilkan pesanan completed
    - Setiap item dalam pesanan completed menampilkan:
        - ✅ "Sudah Direview" (jika sudah direview)
        - 🌟 Tombol "Beri Review" (jika belum direview)

2. **Menulis Review:**
    - Klik tombol "Beri Review" pada produk
    - Anda akan diarahkan ke halaman form review
    - Pilih rating (1-5 bintang) - **WAJIB**
    - Tulis komentar (opsional, max 1000 karakter)
    - Klik "Kirim Review"

3. **Review di Halaman Produk:**
    - Semua review produk ditampilkan di halaman detail produk
    - User yang sudah mereview bisa menghapus review mereka sendiri
    - Jika user punya pesanan completed dengan produk tersebut, form review akan muncul

## 🔧 Implementasi Teknis

### **Model Updates:**

#### **User Model** (`app/Models/User.php`)

```php
// Cek apakah user sudah pernah membeli produk
public function hasPurchased($produkId): bool

// Cek apakah user sudah mereview produk di order tertentu
public function hasReviewed($produkId, $orderId): bool

// Dapatkan semua order completed yang memiliki produk tertentu
public function completedOrdersWithProduct($produkId)
```

#### **Produk Model** (`app/Models/Produk.php`)

```php
// Cek apakah user bisa mereview produk ini
public function canBeReviewedBy($userId): bool

// Dapatkan order completed untuk user tertentu
public function completedOrdersForUser($userId)
```

#### **Order Model** (`app/Models/Order.php`)

```php
// Relasi ke reviews
public function reviews()
```

#### **OrderItem Model** (`app/Models/OrderItem.php`)

```php
// Cek apakah item sudah direview
public function hasReviewByUser($userId): bool

// Dapatkan review untuk item ini
public function getReviewByUser($userId)
```

### **Controller:**

#### **ReviewController** (`app/Http/Controllers/ReviewController.php`)

**Method `create()`:**

- Menampilkan form review untuk produk dari pesanan tertentu
- Validasi:
    - Order milik user yang login
    - Order status = completed
    - Produk ada dalam pesanan
    - Belum pernah direview

**Method `store()`:**

- Menyimpan review baru
- Validasi:
    - Rating required (1-5)
    - Comment optional (max 1000)
    - Order completed dan punya produk tersebut
    - Belum pernah direview
- Kirim notifikasi ke admin setelah review dibuat

**Method `destroy()`:**

- Hapus review
- Hanya pembuat review yang bisa menghapus

### **Routes:**

```php
// Di dalam middleware auth
Route::get('/order/{order}/review/{produk}', [ReviewController::class, 'create'])
    ->name('review.create');

Route::post('/produk/{produk}/review', [ReviewController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('review.store');

Route::delete('/review/{review}', [ReviewController::class, 'destroy'])
    ->name('review.destroy');
```

### **Views:**

1. **`resources/views/store/review-form.blade.php`**
    - Form review dengan rating bintang interaktif
    - Info pesanan dan produk
    - Validasi client-side
    - Tips untuk menulis review yang baik

2. **`resources/views/store/my-orders.blade.php`** (Updated)
    - Section review di pesanan completed
    - Tombol "Beri Review" / "Sudah Direview"
    - Daftar produk yang bisa direview

3. **`resources/views/store/show.blade.php`** (Existing)
    - Form review untuk user yang sudah beli
    - List semua review produk
    - Rating summary

## 🎯 Logika Bisnis

### **Database Structure:**

Tabel `reviews`:

```sql
- id
- user_id (FK to users)
- produk_id (FK to produks)
- order_id (FK to orders)
- rating (1-5)
- comment (nullable)
- timestamps
- UNIQUE KEY (user_id, produk_id, order_id)
```

**Unique constraint** memastikan user tidak bisa review produk yang sama dalam satu pesanan lebih dari sekali.

### **Flow Review:**

```
User Order Produk → Status = Completed →
Tombol "Beri Review" Muncul →
User Klik & Isi Form →
Validasi Backend →
Review Disimpan →
Muncul di Halaman Produk
```

### **Notifikasi:**

- Admin menerima notifikasi saat ada review baru
- Menggunakan `AdminNotificationService::newReview()`

## 🎨 UI/UX Features

### **Halaman Form Review:**

- ⭐ Rating interaktif dengan hover effect
- 📝 Textarea untuk komentar
- 💡 Tips menulis review yang baik
- 📦 Info pesanan dan produk
- ✅ Validasi real-time

### **Halaman My Orders:**

- 🔖 Badge status untuk setiap item
- 🌟 Tombol review yang eye-catching
- ✓ Indikator "Sudah Direview"
- 📱 Responsive mobile-first design

### **Halaman Detail Produk:**

- ⭐ Rating summary dengan bintang
- 💬 List review dari pelanggan
- 👤 Info reviewer (nama, waktu)
- 🗑️ Tombol hapus untuk review sendiri

## 🔒 Security & Validation

1. **Authorization:**
    - User harus login
    - Order harus milik user
    - Hanya bisa review produk yang dibeli

2. **Validation:**
    - Rating required (1-5)
    - Comment max 1000 karakter
    - Throttling: max 10 review per menit

3. **Data Integrity:**
    - Unique constraint di database
    - Double check di controller
    - Soft delete untuk produk

## 📊 Testing Scenarios

### **Test Case 1: Review Produk yang Sudah Dibeli**

1. Login sebagai user
2. Beli produk dan complete order
3. Buka "Pesanan Saya"
4. Klik "Beri Review" pada produk
5. Isi rating dan comment
6. Submit → ✅ Berhasil

### **Test Case 2: Coba Review Tanpa Beli**

1. Login sebagai user
2. Buka halaman produk yang belum pernah dibeli
3. Form review tidak muncul → ✅ Benar

### **Test Case 3: Review Produk yang Sama 2x dalam 1 Order**

1. Sudah review produk A dalam order #1
2. Coba review lagi produk A dalam order #1
3. Error: "Sudah memberikan review" → ✅ Benar

### **Test Case 4: Review Produk yang Sama di Order Berbeda**

1. Sudah review produk A dalam order #1
2. Beli lagi produk A dalam order #2 (completed)
3. Review produk A dalam order #2 → ✅ Berhasil

### **Test Case 5: Hapus Review Sendiri**

1. User sudah review produk
2. Buka halaman detail produk
3. Klik tombol hapus pada review sendiri → ✅ Berhasil

## 🚀 Future Enhancements

- [ ] Upload foto/video review
- [ ] Helpful/unhelpful votes untuk review
- [ ] Reply dari admin ke review
- [ ] Filter review by rating
- [ ] Verified purchase badge
- [ ] Review rewards/points

## 📝 Notes

- Review hanya bisa dibuat untuk pesanan yang **completed**
- User bisa review produk yang sama berkali-kali (dari order berbeda)
- Admin bisa melihat semua review di dashboard
- Review dihitung untuk rating produk secara otomatis
- Soft delete produk tetap bisa menampilkan review lama

---

**Created:** 2026-02-15
**Last Updated:** 2026-02-15
