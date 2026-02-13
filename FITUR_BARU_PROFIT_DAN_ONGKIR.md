# 📊 FITUR BARU: Analisa Margin Keuntungan & Ongkir Dinamis

## ✅ Fitur yang Telah Diimplementasikan

### 🎯 1. Analisa Margin Keuntungan (Profit Tracking)

**Deskripsi**: Sistem sekarang dapat menghitung keuntungan bersih dari setiap transaksi dengan membandingkan harga jual dengan harga modal.

#### Database Changes:
- ✅ `produks.harga_modal` - Menyimpan harga modal/supplier per kg
- ✅ `order_items.cost_price` - Snapshot harga modal saat order dibuat

#### Logic Flow:
1. **Input Produk**: Admin memasukkan harga jual + harga modal saat create/edit produk
2. **Saat Checkout**: Sistem menyimpan snapshot `cost_price` dari produk ke `order_items`
3. **Perhitungan Profit**: 
   ```
   Profit per Item = (Harga Jual - Harga Modal) × Qty
   Gross Profit Order = Σ(Profit per Item)
   ```

#### Dashboard Metrics (BARU):
- **Total Keuntungan Bersih** (All Time)
- **Keuntungan Hari Ini**
- **Keuntungan Bulan Ini**
- **Grafik Dual Line**: Omset vs Profit (7 hari terakhir)

#### UI Enhancements:
- ✅ Form Produk: Field "Harga Modal" dengan tooltip
- ✅ Dashboard: Card profit warna ungu/violet dengan icon `fa-chart-pie`
- ✅ Chart: 2 garis (Cyan = Omset, Violet = Profit)

---

### 🚚 2. Area Pengiriman & Ongkir Dinamis

**Deskripsi**: Admin dapat membuat zona pengiriman dengan harga ongkir berbeda. Sistem otomatis mendeteksi zona berdasarkan alamat customer.

#### Database Tables:
```sql
shipping_zones:
  - zone_name (VARCHAR) - "Zona A - Kota Pusat"
  - areas (JSON) - ["Kelurahan Sudirman", "Kecamatan Denpasar Barat"]
  - cost (DECIMAL) - Ongkir untuk zona ini
  - is_active (BOOLEAN)

orders:
  - shipping_cost (DECIMAL) - Ongkir yang dikenakan
  - shipping_zone_id (FK)
```

#### Logic Flow:
1. **Admin Setup**: Admin membuat zona dengan list area (kecamatan/kelurahan)
2. **Saat Checkout**: 
   - Sistem ambil alamat dari `users.alamat`
   - Loop semua zona aktif, cek apakah alamat cocok (fuzzy match)
   - Jika cocok → Set `shipping_cost` dan `shipping_zone_id`
   - Jika tidak cocok → Ongkir = 0
3. **Grand Total**: `total_price + shipping_cost`

#### Admin Features:
- ✅ CRUD Zona Pengiriman (`/admin/shipping-zones`)
- ✅ Statistik: Total zona, zona aktif, rata-rata ongkir
- ✅ Toggle aktif/nonaktif per zona
- ✅ Tampilan list area dengan badge

#### Matching Algorithm:
```php
function coversArea(string $area): bool
{
    // Normalize & lowercase
    $normalizedArea = strtolower(trim($area));
    
    // Loop setiap area di zona
    foreach ($this->areas as $zone) {
        // Fuzzy match: "sudirman" cocok dengan "Kel. Sudirman Timur"
        if (str_contains($normalizedArea, $zone) || 
            str_contains($zone, $normalizedArea)) {
            return true;
        }
    }
    return false;
}
```

---

## 📁 File-file yang Diubah/Dibuat

### ✅ Migrations (4 files)
1. `2026_02_10_153152_add_cost_price_to_produks_table.php`
2. `2026_02_10_153205_add_cost_price_to_order_items_table.php`
3. `2026_02_10_153235_create_shipping_zones_table.php`
4. `2026_02_10_153245_add_shipping_cost_to_orders_table.php`

### ✅ Models
- `app/Models/Produk.php` - Added `harga_modal`
- `app/Models/Order.php` - Added profit methods & shipping relation
- `app/Models/OrderItem.php` - Added `cost_price`
- `app/Models/ShippingZone.php` - NEW model dengan `coversArea()` method

### ✅ Controllers
- `app/Http/Controllers/StoreController.php` - Checkout logic + shipping detection
- `app/Http/Controllers/AdminDashboardController.php` - Profit calculation
- `app/Http/Controllers/ShippingZoneController.php` - NEW CRUD controller

### ✅ Views
**Produk Forms:**
- `resources/views/admin/produk/create.blade.php`
- `resources/views/admin/produk/edit.blade.php`

**Shipping Zones (NEW):**
- `resources/views/admin/shipping-zones/index.blade.php`
- `resources/views/admin/shipping-zones/create.blade.php`
- `resources/views/admin/shipping-zones/edit.blade.php`

**Dashboard:**
- `resources/views/admin/dashboard.blade.php` - Added profit card & dual-line chart

### ✅ Routes
- `routes/web.php` - Added `Route::resource('shipping-zones')`

---

## 🎨 Design Highlights

### Profit Card (Dashboard)
- **Color**: Violet/Purple gradient (`#8b5cf6 → #7c3aed`)
- **Icon**: `fas fa-chart-pie`
- **Badge**: "Profit" with trending-up icon
- **Sub-text**: "Hari ini: Rp X"

### Chart Enhancement
- **Type**: Dual-line chart
- **Line 1 (Omset)**: Cyan `#22d3ee` dengan fill gradient
- **Line 2 (Profit)**: Violet `#a78bfa` tanpa fill
- **Legend**: Ditampilkan di atas chart dengan dot style

### Shipping Zones UI
- **Stats Cards**: Total zona, Zona aktif, Ongkir rata-rata
- **Table**: Nama zona, Badge area (max 3 + overflow), Ongkir, Status toggle
- **Form**: Textarea untuk input area (comma-separated)

---

## 🚀 Cara Penggunaan

### Setup Awal:
1. **Set Harga Modal Produk**: 
   - Masuk ke `/admin/produk/create` atau `/admin/produk/{id}/edit`
   - Isi "Harga Modal per Kg" (contoh: Rp 18.000 untuk Lele)

2. **Buat Zona Pengiriman**:
   - Masuk ke `/admin/shipping-zones/create`
   - Nama zona: "Zona A - Kota Pusat"
   - Wilayah: `Sudirman, Gatsu, Denpasar Barat` (pisah koma)
   - Ongkir: `10000`
   - Centang "Aktifkan"

### Testing:
1. **Test Profit**: 
   - Buat order via customer
   - Cek dashboard admin → Lihat card "Keuntungan Bersih"
   - Hover chart → Tooltip menunjukkan Omset vs Profit

2. **Test Ongkir**:
   - Pastikan user alamatnya mengandung kata dari zona (misal: "Jl. Sudirman No. 10")
   - Checkout → Ongkir otomatis terdeteksi
   - Cek `orders` table → `shipping_cost` dan `shipping_zone_id` terisi

---

## ⚠️ Catatan Penting

### Profit Calculation:
- Hanya menghitung dari **completed orders**
- Jika produk tidak punya `harga_modal` (default 0), profit = harga jual × qty
- Update produk lama: Perlu set `harga_modal` secara manual

### Shipping Detection:
- **Case-insensitive** dan **fuzzy match**
- Jika ada typo di alamat → Bisa tidak terdeteksi
- Jika tidak ada zona yang cocok → `shipping_cost` = 0
- Admin bisa edit ongkir manual di order (jika diperlukan)

### Performance:
- Profit dashboard: Load semua completed orders + items (bisa lambat jika data banyak)
- **Optimization**: Tambahkan cache atau limit ke order bulan ini saja
- Shipping detection: Loop zona aktif saat checkout (OK untuk <100 zona)

---

## 🎯 Fitur Future (Opsional)

1. **Profit by Category**: Lele lebih menguntungkan atau Ikan Mas?
2. **Margin Percentage**: Tampilkan margin % di samping profit Rp
3. **Shipping Zone Map**: Visual map zona pengiriman
4. **Auto-detect Area**: Integrasi API Google Maps untuk auto-detect kecamatan dari alamat
5. **Variable Pricing**: Ongkir beda untuk berat berbeda (misal: <5kg, 5-10kg, >10kg)

---

**Status**: ✅ PRODUCTION READY  
**Tanggal**: 10 Februari 2026  
**Database**: ✅ Migrated  
**Testing**: ✅ Manual tested  
**Documentation**: ✅ Complete
