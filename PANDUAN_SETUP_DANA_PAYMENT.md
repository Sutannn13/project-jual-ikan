# 🚀 PANDUAN SETUP PAYMENT GATEWAY DANA (E-WALLET)

## ✅ IMPLEMENTASI SELESAI! (UPDATE TERBARU)

Payment gateway otomatis dengan **Dana** (dan e-wallet lainnya) sudah **SIAP DIGUNAKAN**! 🎉

### 🆕 UPDATE: E-Wallet Terintegrasi di Checkout!

Customer sekarang bisa langsung pilih metode pembayaran **E-Wallet** di halaman keranjang belanja, bersama Transfer Bank dan COD!

---

## 📋 FITUR YANG SUDAH DIIMPLEMENTASI

✅ **Pembayaran Otomatis**: Dana, GoPay, ShopeePay, QRIS  
✅ **Verifikasi Otomatis**: Pesanan langsung dikonfirmasi setelah bayar  
✅ **Pilihan di Checkout**: E-Wallet, Transfer Bank, atau COD  
✅ **Auto-Trigger Popup**: Popup Midtrans muncul otomatis jika pilih E-Wallet  
✅ **Fallback to Manual**: Customer bisa switch ke transfer manual jika mau  
✅ **Webhook Handle**: Notifikasi otomatis dari Midtrans  
✅ **Stock Management**: Stok otomatis dikurangi setelah payment sukses  
✅ **Email Notification**: Email otomatis ke customer

---

## 🎯 CARA AKTIVASI (MUDAH!)

### **Step 1: Daftar Akun Midtrans**

1. Buka: https://dashboard.midtrans.com/register
2. Isi form pendaftaran dengan data bisnis Anda
3. Verifikasi email
4. Lengkapi dokumen:
    - **KTP** pemilik usaha
    - **NPWP** (opsional untuk testing, wajib untuk production)
    - **Foto toko/produk**

### **Step 2: Dapatkan API Keys**

#### **Untuk Testing (Sandbox):**

1. Login ke [Midtrans Dashboard](https://dashboard.midtrans.com)
2. Pilih environment: **Sandbox** (pojok kanan atas)
3. Ke menu: **Settings → Access Keys**
4. Copy kedua keys:
    ```
    Server Key: SB-Mid-server-xxxxxxxxxxxxx
    Client Key: SB-Mid-client-xxxxxxxxxxxxx
    ```

#### **Untuk Production (Live - Setelah Testing OK):**

1. Switch environment ke: **Production**
2. Aktivasi payment methods (Dana, GoPay, dll)
3. Copy production keys

---

### **Step 3: Update File .env**

Buka file `.env` di root project, tambahkan:

```env
# Midtrans Payment Gateway
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```

**⚠️ PENTING:**

- Ganti `xxxxxxxxxxxxx` dengan key asli dari Midtrans
- Jangan share keys ini ke siapapun!
- Set `MIDTRANS_IS_PRODUCTION=false` untuk testing
- Ganti ke `true` saat sudah siap production

---

### **Step 4: Clear Cache**

Jalankan command ini di terminal:

```bash
php artisan config:clear
php artisan cache:clear
```

---

### **Step 5: Setup Webhook di Midtrans**

1. Login Midtrans Dashboard
2. **Settings → Configuration**
3. Isi:
    - **Payment Notification URL**: `https://domain-anda.com/payment/notification`
    - **Finish Redirect URL**: `https://domain-anda.com/order/{order_id}/success`
    - **Unfinish Redirect URL**: `https://domain-anda.com/my-orders`
    - **Error Redirect URL**: `https://domain-anda.com/my-orders`

**⚠️ Ganti `domain-anda.com` dengan domain asli toko Anda!**

**Untuk Testing Lokal (Development):**
Gunakan ngrok untuk expose localhost:

```bash
ngrok http 8000
```

Lalu gunakan URL ngrok sebagai webhook URL.

---

## 🧪 TESTING PEMBAYARAN (UPDATE!)

### **Testing di Sandbox Mode:**

1. Login sebagai customer
2. Add produk ke cart
3. **Buka keranjang** → Klik icon cart
4. **Pilih metode pembayaran:**
    - Pilih **"E-Wallet (Dana, GoPay, QRIS)"**
5. Klik tombol **"Bayar dengan E-Wallet"**
6. Halaman Order Success muncul
7. **Popup Midtrans** akan muncul **OTOMATIS** dalam 1 detik
8. Pilih metode di popup:
    - **Dana** → QR Code akan muncul
    - **GoPay** → QR Code untuk scan
    - **QRIS** → Universal QR Code
    - **ShopeePay** → Deeplink ke app

### **Test Cards/Methods (Sandbox):**

| Metode        | Cara Test                         | Hasil   |
| ------------- | --------------------------------- | ------- |
| **Dana**      | Scan QR yang muncul (simulator)   | Success |
| **GoPay**     | Scan QR atau klik "Demo Payment"  | Success |
| **QRIS**      | Scan atau klik "Simulate Payment" | Success |
| **ShopeePay** | Klik deeplink → simulator         | Success |

**Link Simulator:** https://simulator.sandbox.midtrans.com/qris/index

### **Flow Testing:**

1. ✅ Customer pilih **E-Wallet** di halaman cart
2. ✅ Klik "Bayar dengan E-Wallet"
3. ✅ Redirect ke halaman Order Success
4. ✅ **Popup Midtrans AUTO-MUNCUL** (tidak perlu klik)
5. ✅ Customer pilih Dana/GoPay/QRIS
6. ✅ QR Code muncul atau redirect ke app
7. ✅ Customer scan/bayar
8. ✅ **Status otomatis berubah ke "Paid"** (instan!)
9. ✅ **Email konfirmasi dikirim**
10. ✅ **Stok produk otomatis berkurang**

**⚠️ Penting:** Popup akan muncul otomatis setelah 1 detik. Jika tidak muncul, customer bisa klik tombol "Bayar Sekarang" di halaman.

---

## 🎨 TAMPILAN UNTUK CUSTOMER (UPDATE!)

### **Halaman Cart/Checkout:**

```
┌────────────────────────────────────┐
│   🛒 Keranjang Belanja             │
├────────────────────────────────────┤
│   [Produk 1] - Rp 50.000          │
│   [Produk 2] - Rp 42.500          │
├────────────────────────────────────┤
│   📦 Ringkasan Pesanan             │
│   Subtotal: Rp 92.500             │
│   Ongkir:   Rp 10.000             │
│   Total:    Rp 102.500            │
│                                    │
│   🎯 Metode Pembayaran             │
│   ○ 🏦 Transfer Bank               │
│   ● 📱 E-Wallet (Dana, GoPay, QRIS)│ ← DIPILIH
│       ⚡ Otomatis & instan!        │
│   ○ 🚚 COD (Bayar di Tempat)      │
│                                    │
│   [💳 BAYAR DENGAN E-WALLET]      │
└────────────────────────────────────┘
```

### **Halaman Order Success (Jika Pilih E-Wallet):**

```
┌────────────────────────────────────┐
│   [💜] Pembayaran E-Wallet         │
│                                    │
│   Popup akan muncul otomatis...   │
│                                    │
│   [⚡ BAYAR SEKARANG]              │
│   Dana | GoPay | QRIS             │
│                                    │
│   💡 Pembayaran otomatis & instan! │
│                                    │
│   ────── Atau transfer manual ──── │
│   (Klik untuk lihat opsi manual)  │
└────────────────────────────────────┘
```

### **Popup Midtrans (Muncul Otomatis):**

```
┌────────────────────────────────────┐
│   Pilih Metode Pembayaran          │
├────────────────────────────────────┤
│   [📱] Dana                        │
│   [🟢] GoPay                       │
│   [🛍️] ShopeePay                  │
│   [📊] QRIS (Universal)            │
└────────────────────────────────────┘
```

---

## 💰 BIAYA TRANSAKSI (MDR - Merchant Discount Rate)

| Metode        | Biaya   | Keterangan                    |
| ------------- | ------- | ----------------------------- |
| **Dana**      | ~2.0%   | Dipotong dari total transaksi |
| **GoPay**     | ~2.0%   | Per transaksi                 |
| **ShopeePay** | ~2.0%   | Per transaksi                 |
| **QRIS**      | ~0.7%\* | Paling murah                  |

\*Biaya bisa berbeda tergantung kesepakatan dengan Midtrans.

**Contoh:**

- Customer bayar: Rp 100.000
- Biaya MDR (2%): Rp 2.000
- Anda terima: Rp 98.000

**💡 Tips:**

- Biaya MDR bisa dinegosiasi jika volume transaksi besar
- QRIS biasanya memiliki MDR paling rendah

---

## 🔐 KEAMANAN

✅ **SSL/HTTPS Wajib** untuk production  
✅ **Signature Verification** sudah diimplementasi  
✅ **CSRF Protection** aktif  
✅ **Server Key** tidak pernah di-expose ke client  
✅ **Webhook Validation** untuk prevent fake notifications

---

## 🐛 TROUBLESHOOTING

### **1. Tombol "Bayar Sekarang" Tidak Muncul**

**Penyebab:** Order status bukan "pending"

**Solusi:**

- Pastikan order masih dalam status pending
- Belum upload bukti transfer manual
- Payment deadline belum expired

---

### **2. Popup Midtrans Tidak Muncul**

**Penyebab:** JavaScript error atau API key salah

**Solusi:**

```bash
# 1. Cek browser console (F12)
# 2. Pastikan Midtrans script loaded:
View Page Source → cari: snap.js

# 3. Clear cache:
php artisan config:clear

# 4. Cek .env:
MIDTRANS_SERVER_KEY=SB-Mid-server-... (harus diisi!)
MIDTRANS_CLIENT_KEY=SB-Mid-client-... (harus diisi!)
```

---

### **3. Payment Success Tapi Order Tetap Pending**

**Penyebab:** Webhook URL tidak accessible atau signature invalid

**Solusi:**

```bash
# 1. Cek webhook URL di Midtrans Dashboard
# 2. Test webhook manually:
curl -X POST https://domain-anda.com/payment/notification

# 3. Cek log Laravel:
tail -f storage/logs/laravel.log

# 4. Pastikan route webhook accessible (tanpa auth):
Route::post('/payment/notification', ...) // Sudah correct!
```

---

### **4. Error: "Invalid Signature"**

**Penyebab:** Server key salah atau signature verification failed

**Solusi:**

- Pastikan `MIDTRANS_SERVER_KEY` di `.env` sama dengan yang di dashboard
- Clear config cache: `php artisan config:clear`
- Cek environment (Sandbox vs Production)

---

### **5. Dana/E-Wallet Tidak Muncul di Popup**

**Penyebab:** Payment method belum diaktifkan di Midtrans

**Solusi:**

1. Login Midtrans Dashboard
2. **Settings → Payment Settings**
3. Aktifkan:
    - ✅ Dana
    - ✅ GoPay
    - ✅ ShopeePay
    - ✅ QRIS
4. Save

---

### **6. Ngrok untuk Testing Webhook (Localhost)**

```bash
# Install ngrok
# Download dari: https://ngrok.com/download

# Jalankan:
ngrok http 8000

# Copy URL ngrok yang muncul:
Forwarding: https://xxxx-xxx-xxx.ngrok.io → http://localhost:8000

# Set di Midtrans webhook:
https://xxxx-xxx-xxx.ngrok.io/payment/notification
```

---

## 📊 MONITORING TRANSAKSI

### **Di Midtrans Dashboard:**

1. **Transactions** → Lihat semua transaksi
2. Filter by:
    - Payment method (Dana, GoPay, dll)
    - Status (Success, Pending, Failed)
    - Date range

### **Di Admin Toko Ikan:**

1. Login sebagai admin
2. **Kelola Pesanan**
3. Order yang dibayar via e-wallet akan ada:
    - `payment_method: "dana"` (atau gopay, qris, dll)
    - `midtrans_transaction_id: "xxxxx"`
    - Status otomatis "Paid"

---

## 🚀 AKTIVASI PRODUCTION

Setelah testing OK, aktivasi production:

### **1. Update .env:**

```env
MIDTRANS_SERVER_KEY=Mid-server-xxxxxxxxxxxxx  # Tanpa "SB-"
MIDTRANS_CLIENT_KEY=Mid-client-xxxxxxxxxxxxx  # Tanpa "SB-"
MIDTRANS_IS_PRODUCTION=true  # ⚠️ PENTING!
```

### **2. Update Webhook URL:**

Ganti dengan domain production (bukan ngrok):

```
https://tokoikan.com/payment/notification
```

### **3. Aktivasi Payment Methods:**

1. Midtrans Dashboard → Production Environment
2. **Settings → Payment Settings**
3. Aktifkan Dana, GoPay, QRIS, ShopeePay
4. **Submit dokumen bisnis** (KTP, NPWP, Foto Produk)
5. **Tunggu approval** (~1-3 hari kerja)

### **4. SSL Certificate (WAJIB!):**

Production **HARUS** menggunakan HTTPS!

**Gratis:** Let's Encrypt (via cPanel/Plesk)

```bash
# Atau manual:
certbot --nginx -d tokoikan.com
```

---

## ✨ KEUNTUNGAN PAYMENT GATEWAY OTOMATIS

| Fitur                   | Manual Transfer      | E-Wallet (Dana)     |
| ----------------------- | -------------------- | ------------------- |
| **Konfirmasi**          | Manual (admin check) | ✅ Otomatis instant |
| **Waktu Proses**        | 1-24 jam             | ⚡ 1-2 detik        |
| **Upload Bukti**        | ❌ Harus upload      | ✅ Tidak perlu      |
| **Verifikasi Admin**    | ❌ Harus manual      | ✅ Tidak perlu      |
| **Email Notif**         | Manual trigger       | ✅ Otomatis         |
| **Stock Update**        | Manual               | ✅ Otomatis         |
| **Customer Experience** | ⭐⭐⭐               | ⭐⭐⭐⭐⭐          |

---

## 📞 SUPPORT & REFERENSI

- **Midtrans Docs:** https://docs.midtrans.com
- **Snap Integration:** https://docs.midtrans.com/docs/snap-integration-guide
- **Testing Payment:** https://docs.midtrans.com/docs/testing-payment
- **Webhook:** https://docs.midtrans.com/docs/http-notification
- **Support:** support@midtrans.com

---

## 🎓 CARA PAKAI UNTUK CUSTOMER (UPDATE!)

### **Customer Flow - Sekarang Lebih Mudah!**

#### **METODE A: Pilih E-Wallet di Checkout** ⚡ (RECOMMENDED!)

1. **Belanja** → Add produk ke cart
2. **Buka Cart** → Klik keranjang belanja
3. **Pilih Metode Pembayaran:**
    - 📱 **E-Wallet (Dana, GoPay, QRIS)** ← PILIH INI!
    - 🏦 Transfer Bank (Manual)
    - 🚚 COD (Bayar di Tempat)
4. **Klik "Bayar dengan E-Wallet"**
5. **Popup Midtrans** muncul otomatis
6. **Pilih Dana** (atau GoPay/QRIS)
7. **Scan QR Code** → Bayar di aplikasi Dana
8. ✅ **SELESAI!** Pesanan otomatis dikonfirmasi!

**Waktu proses:** ⚡ **1-2 detik** setelah pembayaran!

---

#### **METODE B: Transfer Manual** (Cara Lama)

1. **Checkout** → Pilih Transfer Bank
2. **Upload Bukti Transfer**
3. ⏳ Tunggu admin verifikasi (1-24 jam)

---

### **Perbandingan:**

| Aspek            | E-Wallet (Baru)    | Transfer Manual        |
| ---------------- | ------------------ | ---------------------- |
| **Waktu**        | ⚡ 2 detik         | ⏳ 1-24 jam            |
| **Verifikasi**   | ✅ Otomatis        | ❌ Manual admin        |
| **Upload Bukti** | ❌ Tidak perlu     | ✅ Harus upload        |
| **Status Order** | ✅ Langsung "Paid" | ⏳ Menunggu verifikasi |

---

## 📸 SCREENSHOT TESTING (UPDATE!)

Buat screenshot ini untuk dokumentasi:

1. ✅ **Halaman Cart** dengan 3 pilihan metode pembayaran (Transfer, E-Wallet, COD)
2. ✅ **Pilihan E-Wallet** terpilih (warna violet/ungu)
3. ✅ **Halaman Order Success** dengan popup auto-trigger
4. ✅ **Popup Midtrans** dengan pilihan e-wallet
5. ✅ **QR Code Dana/GoPay** untuk payment
6. ✅ **Status order** berubah jadi "Paid" otomatis
7. ✅ **Email konfirmasi** pembayaran ke customer

---

## 🎯 TODO NEXT (OPSIONAL)

- [ ] **Refund System**: Handle pembatalan otomatis via Midtrans
- [ ] **Installment**: Cicilan untuk produk mahal
- [ ] **Promo Code**: Diskon via payment gateway
- [ ] **Multi Currency**: Support USD/SGD
- [ ] **Split Payment**: Bagi hasil otomatis

---

## ✅ CHECKLIST SEBELUM GO LIVE

- [ ] API Keys production sudah diisi di `.env`
- [ ] `MIDTRANS_IS_PRODUCTION=true`
- [ ] Webhook URL sudah di-set dengan domain production
- [ ] SSL Certificate aktif (HTTPS)
- [ ] Testing payment berhasil (minimal 3x)
- [ ] Email notification berfungsi
- [ ] Admin bisa lihat transaksi Midtrans
- [ ] Dokumentasi/SOP untuk tim

---

## 🎉 SELAMAT!

Payment gateway Dana sudah **SIAP DIGUNAKAN!** 🚀

Customer sekarang bisa bayar dengan:

- ⚡ **Dana** - Instant
- ⚡ **GoPay** - Instant
- ⚡ **QRIS** - Universal
- ⚡ **ShopeePay** - Instant

**Tidak perlu lagi:**

- ❌ Upload bukti transfer
- ❌ Tunggu verifikasi admin
- ❌ Konfirmasi manual

Semua **OTOMATIS!** 🎯

---

**Dibuat dengan ❤️ untuk Toko Ikan**  
**Last Updated:** 16 Februari 2026
