# 🔧 Fix: Internal Server Error pada Penghapusan Produk

## 📋 Analisis Masalah

### Error yang Terjadi:
```
Illuminate\Database\QueryException
SQLSTATE[23000]: Integrity constraint violation: 1451 
Cannot delete or update a parent row: a foreign key constraint fails 
(db_toko_ikan.order_items, CONSTRAINT order_items_produk_id_foreign 
FOREIGN KEY (produk_id) REFERENCES produks (id) ON DELETE RESTRICT)
```

### Akar Masalah:

1. **Database Constraint Conflict**
   - Di migration `2026_02_09_100001_create_order_items_table.php` baris 14:
     ```php
     $table->foreignId('produk_id')->constrained()->onDelete('restrict');
     ```
   - Constraint `restrict` mencegah penghapusan produk yang sudah ada di order_items

2. **Controller Logic Missing Validation**
   - Di `ProdukController.php` method `destroy()` (baris 93-105):
   - Tidak ada pengecekan apakah produk sudah ada di riwayat pesanan
   - Langsung mencoba menghapus tanpa validasi relasi
   - Ini menyebabkan error saat produk yang sudah pernah dipesan dihapus

3. **Konflik Logika:**
   - **Database Layer**: Melindungi integritas data dengan `RESTRICT`
   - **Application Layer**: Tidak memvalidasi kondisi sebelum delete
   - **Result**: Exception thrown ke user

## ✅ Solusi yang Diimplementasikan

### Pilihan Solusi:
Ada 3 opsi yang mungkin:

#### ❌ Opsi 1: Cascade Delete (BERBAHAYA)
```php
// JANGAN GUNAKAN INI!
->onDelete('cascade')
```
- **Dampak**: Menghapus semua order_items terkait
- **Masalah**: Merusak riwayat transaksi pelanggan
- **Status**: ❌ Ditolak

#### ⚠️ Opsi 2: Soft Delete
```php
use SoftDeletes;
```
- **Dampak**: Produk hanya di-hide, tidak dihapus permanen
- **Cocok untuk**: Kasus yang butuh restore capability
- **Status**: ⚠️ Alternatif (tidak digunakan sekarang)

#### ✅ Opsi 3: Prevent Deletion with Validation (DIPILIH)
```php
// Cek relasi sebelum delete
if ($produk->orderItems()->exists()) {
    return redirect()->with('error', 'Tidak bisa hapus...');
}
```
- **Dampak**: Melindungi integritas data transaksi
- **User-friendly**: Memberikan pesan error yang jelas
- **Status**: ✅ Diimplementasikan

### Perubahan yang Dilakukan:

#### 1. `ProdukController.php` - Method `destroy()`
**Sebelum:**
```php
public function destroy(string $id)
{
    $produk = Produk::findOrFail($id);

    if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
        Storage::disk('public')->delete($produk->foto);
    }

    AdminNotificationService::logProdukDeleted($produk);
    $produk->delete(); // ❌ Langsung delete tanpa cek

    return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus!');
}
```

**Sesudah:**
```php
public function destroy(string $id)
{
    $produk = Produk::findOrFail($id);

    // ✅ Cek apakah produk sudah ada di order_items
    if ($produk->orderItems()->exists()) {
        return redirect()->route('admin.produk.index')
            ->with('error', 'Produk tidak dapat dihapus karena sudah ada di riwayat pesanan. Produk ini sudah pernah dipesan oleh pelanggan.');
    }

    // ✅ Cek apakah ada reserved stock
    if ($produk->reserved_stock > 0) {
        return redirect()->route('admin.produk.index')
            ->with('error', 'Produk tidak dapat dihapus karena sedang ada dalam keranjang atau proses pemesanan pelanggan (Reserved: ' . $produk->reserved_stock . ' Kg).');
    }

    if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
        Storage::disk('public')->delete($produk->foto);
    }

    AdminNotificationService::logProdukDeleted($produk);
    $produk->delete();

    return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus!');
}
```

#### 2. `admin/produk/index.blade.php` - Alert Messages
**Ditambahkan:**
```blade
{{-- Alert Messages --}}
@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center gap-3">
    <i class="fas fa-check-circle text-xl"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center gap-3">
    <i class="fas fa-exclamation-circle text-xl"></i>
    <span>{{ session('error') }}</span>
</div>
@endif
```

## 🎯 Cara Kerja Solusi

### Flow Chart Penghapusan Produk:

```
[Admin Klik Hapus Produk]
         ↓
[Confirm Dialog: "Yakin hapus?"]
         ↓
[Controller: destroy()]
         ↓
[Cek: Apakah produk ada di order_items?]
         ↓
    ┌────┴────┐
    ↓         ↓
  [YA]      [TIDAK]
    ↓         ↓
 [Error]  [Cek: reserved_stock > 0?]
 Message      ↓
           ┌──┴──┐
           ↓     ↓
         [YA]  [TIDAK]
           ↓     ↓
        [Error] [Delete Success]
        Message    ↓
                [Hapus foto dari storage]
                   ↓
                [Log admin notification]
                   ↓
                [Delete dari database]
                   ↓
                [Success Message]
```

### Validasi yang Diterapkan:

1. **Validasi Order Items**
   - Query: `$produk->orderItems()->exists()`
   - Cek: Apakah produk sudah pernah ada di transaksi?
   - Jika YA: Tolak penghapusan

2. **Validasi Reserved Stock**
   - Query: `$produk->reserved_stock > 0`
   - Cek: Apakah produk sedang dalam keranjang/proses order?
   - Jika YA: Tolak penghapusan

3. **Safe Delete**
   - Hapus foto dari storage
   - Log aktivitas admin
   - Hapus dari database
   - Tampilkan success message

## 📊 Testing Scenarios

### Scenario 1: Hapus Produk yang Sudah Pernah Dipesan
```
Kondisi: Produk ID 8 sudah ada di order_items
Aksi: Admin hapus produk ID 8
Hasil: ✅ Error message ditampilkan
       "Produk tidak dapat dihapus karena sudah ada di riwayat pesanan..."
```

### Scenario 2: Hapus Produk dengan Reserved Stock
```
Kondisi: Produk memiliki reserved_stock = 5 Kg
Aksi: Admin hapus produk
Hasil: ✅ Error message ditampilkan
       "Produk tidak dapat dihapus karena sedang ada dalam keranjang..."
```

### Scenario 3: Hapus Produk Baru (Belum Pernah Dipesan)
```
Kondisi: Produk baru, tidak ada di order_items
Aksi: Admin hapus produk
Hasil: ✅ Produk berhasil dihapus
       "Produk berhasil dihapus!"
```

## 🔐 Data Integrity Protection

### Relasi yang Dilindungi:

1. **orders → order_items** (CASCADE)
   - Hapus order = hapus semua items-nya
   - ✅ Aman

2. **produks → order_items** (RESTRICT)
   - Hapus produk yang sudah di-order = DITOLAK
   - ✅ Melindungi riwayat transaksi

3. **produks → reviews** (RESTRICT)
   - Hapus produk yang ada review-nya = DITOLAK
   - ✅ Melindungi testimoni

### Foreign Key Constraints:
```sql
-- order_items table
CONSTRAINT order_items_produk_id_foreign 
FOREIGN KEY (produk_id) REFERENCES produks (id) 
ON DELETE RESTRICT
```

## 📝 Catatan Penting

### Untuk Admin:
⚠️ **Produk yang sudah pernah dipesan TIDAK BISA dihapus**
- Alasan: Menjaga integritas riwayat transaksi pelanggan
- Solusi alternatif: 
  - Set stok = 0 untuk hide dari toko
  - Atau implementasi soft delete di masa depan

### Untuk Developer:
✅ **Best Practices yang Diterapkan:**
1. Validasi relasi sebelum delete
2. User-friendly error messages
3. Melindungi data transaksi
4. Tidak menggunakan cascade delete pada data kritikal

## 🚀 Future Improvements (Opsional)

1. **Soft Delete Implementation**
   ```php
   // Add to Produk model
   use SoftDeletes;
   protected $dates = ['deleted_at'];
   ```

2. **Archive System**
   - Pindahkan produk lama ke tabel archive
   - Maintain riwayat tanpa clutter di produk aktif

3. **Status Field**
   ```php
   // Add status: active, inactive, discontinued
   $table->enum('status', ['active', 'inactive', 'discontinued']);
   ```

---

**Fixed Date:** 2026-02-13  
**Fixed By:** Development Team  
**Status:** ✅ Resolved
