@component('mail::message')
# ⚠️ Peringatan Stok Rendah

Halo Admin,

Produk berikut memiliki stok yang rendah dan perlu segera diperhatikan:

---

**Produk:** {{ $produk->nama }}

**Kategori:** {{ $produk->kategori }}

**Stok Tersisa:** {{ number_format($produk->stok, 1) }} Kg

**Batas Minimum:** {{ number_format($produk->low_stock_threshold, 1) }} Kg

**Harga:** Rp {{ number_format($produk->harga_per_kg, 0, ',', '.') }}/Kg

---

Segera lakukan restock agar pelanggan tidak kehabisan produk ini.

@component('mail::button', ['url' => route('admin.produk.edit', $produk)])
Update Stok Produk
@endcomponent

Terima kasih,<br>
**Sistem FishMarket**
@endcomponent
