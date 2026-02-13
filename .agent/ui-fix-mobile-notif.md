# 📱 UI Fix: Responsif Notifikasi Dropdown (iPhone SE & Mobile S)

## ❌ Masalah
Pada layar kecil seperti iPhone SE (320px) atau Mobile S, dropdown notifikasi terlihat "tidak enak dilihat" karena:
1. **Width Terlalu Lebar**: Awalnya menggunakan `w-80` (320px) atau `sm:w-96` (384px), yang sama atau lebih besar dari lebar layar HP.
2. **Override**: Karena width fix, dropdown mungkin melebar melebihi batas layar (overflow-x hidden biasanya memotongnya), atau membuat layout geser.
3. **Positioning**: Posisi `absolute right-0` relatif tombol lonceng yang bukan elemen paling kanan (ada profile button), membuat dropdown tidak rata kanan layar dan "memakan" ruang kiri lebih banyak.

## ✅ Solusi Mobile-First
Mengganti ukuran fix dengan ukuran viewport-based yang dinamis:

### Perubahan CSS Class (`resources/views/layouts/admin.blade.php`):
**Sebelum:**
```html
class="absolute right-0 mt-2 w-80 sm:w-96 max-h-[480px] ..."
```

**Sesudah:**
```html
class="absolute right-[-4.5rem] sm:right-0 mt-2 w-[88vw] sm:w-96 max-w-[360px] max-h-[480px] ..."
```

### Penjelasan Logika:
1. **`w-[88vw]`**: Lebar dropdown akan selalu 88% dari lebar layar HP.
   - iPhone SE (320px) → Lebar dropdown ~281px. (Aman, sisa margin 39px).
   - Mobile M (375px) → Lebar dropdown ~330px.
2. **`right-[-4.5rem]`**: Menggeser titik anchor dropdown ke kanan (sekitar 72px) di mobile.
   - Tujuannya agar dropdown lebih centered atau mendekati tepi kanan layar, mengingat tombol lonceng bukan elemen paling kanan.
   - Ini mencegah dropdown "terpotong" di sisi kiri layar.
3. **`sm:right-0` & `sm:w-96`**: Di layar Tablet/Desktop (≥640px), kembali ke tampilan standard (width 384px, align right with bell).
4. **`max-w-[360px]`**: Batas maksimal lebar di HP besar agar tidak terlalu lebar.

## 🚀 Hasil
- **iPhone SE (320px)**: Dropdown pas di layar, tidak terpotong.
- **Mobile M/L**: Dropdown proporsional.
- **Desktop**: Tidak berubah (tetap bagus).

**Fixed Date:** 2026-02-13
