# 📱 QUICK START: E-WALLET PAYMENT GATEWAY

## ✅ SUDAH DIIMPLEMENTASI!

E-Wallet (Dana, GoPay, QRIS, ShopeePay) sekarang **tersedia sebagai pilihan metode pembayaran** di halaman checkout!

---

## 🎯 CARA AKTIVASI (3 LANGKAH!)

### 1️⃣ Daftar Midtrans & Dapatkan API Keys

🔗 https://dashboard.midtrans.com/register

**Sandbox Keys** (untuk testing):

- Server Key: `SB-Mid-server-xxxxx`
- Client Key: `SB-Mid-client-xxxxx`

---

### 2️⃣ Update File `.env`

```env
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```

⚠️ Ganti `xxxxxxxxxxxxx` dengan key asli dari dashboard!

---

### 3️⃣ Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 🚀 CARA PAKAI (CUSTOMER)

### **Step by Step:**

1. **Belanja** → Add produk ke cart
2. **Buka Cart** → Klik icon keranjang
3. **Pilih Pembayaran:**
    ```
    ○ Transfer Bank
    ● E-Wallet (Dana, GoPay, QRIS) ← PILIH INI!
    ○ COD (Bayar di Tempat)
    ```
4. **Klik** "Bayar dengan E-Wallet"
5. **Popup Midtrans** muncul otomatis
6. **Pilih** Dana/GoPay/QRIS
7. **Scan QR** → Bayar
8. ✅ **SELESAI!** Order otomatis "Paid"

---

## 🎨 TAMPILAN BARU

### **Halaman Cart:**

```
┌─────────────────────────────┐
│  Metode Pembayaran          │
├─────────────────────────────┤
│  ○ 🏦 Transfer Bank         │
│  ● 📱 E-Wallet              │ ← BARU!
│      ⚡ Otomatis & instan!  │
│  ○ 🚚 COD                   │
└─────────────────────────────┘
```

### **Flow:**

```
Cart → Pilih E-Wallet → Checkout
     → Popup Midtrans (AUTO)
     → Bayar → ✅ Paid (2 detik!)
```

---

## ⚡ KEUNTUNGAN E-WALLET

| Fitur        | E-Wallet       | Transfer Manual |
| ------------ | -------------- | --------------- |
| Konfirmasi   | ✅ Otomatis    | ❌ Manual admin |
| Waktu        | ⚡ 2 detik     | ⏳ 1-24 jam     |
| Upload Bukti | ❌ Tidak perlu | ✅ Harus upload |

---

## 📁 FILE YANG DIUBAH

1. ✅ `app/Http/Controllers/StoreController.php`
    - Handle payment_method = 'ewallet'

2. ✅ `app/Http/Controllers/PaymentController.php`
    - Enable Dana, GoPay, QRIS, ShopeePay

3. ✅ `resources/views/store/cart.blade.php`
    - Tambah pilihan E-Wallet di checkout

4. ✅ `resources/views/store/order-success.blade.php`
    - Auto-trigger popup Midtrans
    - Conditional display berdasarkan payment method

5. ✅ `config/midtrans.php`
    - Konfigurasi Midtrans (sudah ada)

---

## 🧪 TESTING

### **Sandbox Mode:**

1. Login sebagai customer
2. Add produk → Cart
3. **Pilih "E-Wallet"**
4. Klik "Bayar dengan E-Wallet"
5. **Popup muncul otomatis!**
6. Pilih Dana → Scan QR
7. Status jadi "Paid" instant! ✅

**Link Simulator QR:**  
https://simulator.sandbox.midtrans.com/qris/index

---

## 🔧 TROUBLESHOOTING

### **Popup Tidak Muncul?**

1. Check browser console (F12)
2. Pastikan API keys sudah di `.env`
3. Clear cache: `php artisan config:clear`

---

### **Payment Success Tapi Order Tetap Pending?**

1. Cek webhook URL di Midtrans Dashboard
2. Set webhook: `https://domain-anda.com/payment/notification`
3. Untuk localhost, gunakan **ngrok**

---

## 🔐 WEBHOOK SETUP

### **Midtrans Dashboard:**

**Settings → Configuration**

```
Payment Notification URL:
https://yourdomain.com/payment/notification

Finish Redirect URL:
https://yourdomain.com/order/{order_id}/success
```

⚠️ Ganti `yourdomain.com` dengan domain asli!

---

## 💰 BIAYA (MDR)

- Dana: ~2.0%
- GoPay: ~2.0%
- QRIS: ~0.7% (paling murah)

**Contoh:**

- Customer bayar: Rp 100.000
- MDR (2%): -Rp 2.000
- Anda terima: **Rp 98.000**

---

## 📞 SUPPORT

- **Docs:** https://docs.midtrans.com
- **Email:** support@midtrans.com
- **Panduan Lengkap:** `PANDUAN_SETUP_DANA_PAYMENT.md`

---

## ✅ CHECKLIST GO-LIVE

- [ ] API Keys production di `.env`
- [ ] `MIDTRANS_IS_PRODUCTION=true`
- [ ] Webhook URL production
- [ ] SSL/HTTPS aktif
- [ ] Testing 3x berhasil
- [ ] Email notification OK
- [ ] Payment methods diaktifkan di Midtrans

---

## 🎉 DONE!

Customer sekarang bisa bayar dengan:

- ⚡ **Dana** - Instant
- ⚡ **GoPay** - Instant
- ⚡ **QRIS** - Universal
- ⚡ **ShopeePay** - Instant

**NO MORE:**

- ❌ Upload bukti transfer
- ❌ Tunggu verifikasi admin
- ❌ Konfirmasi manual

**SEMUA OTOMATIS!** 🚀

---

**Last Updated:** 16 Februari 2026  
**Version:** 2.0 - E-Wallet di Checkout
