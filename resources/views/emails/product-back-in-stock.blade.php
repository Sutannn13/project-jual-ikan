@component('mail::message')
# Produk Tersedia Kembali! 🐟

Halo **{{ $user->name }}**,

Kabar baik! Produk yang Anda simpan di wishlist kini tersedia kembali:

---

**{{ $produk->nama }}**

**Harga:** Rp {{ number_format($produk->harga, 0, ',', '.') }} / Kg

**Stok Tersedia:** {{ number_format($produk->stok, 1) }} Kg

---

Segera pesan sebelum kehabisan!

@component('mail::button', ['url' => url('/produk/' . $produk->id)])
Lihat Produk
@endcomponent

---

*Anda menerima email ini karena Anda mengaktifkan notifikasi restock untuk produk ini di wishlist.*

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
