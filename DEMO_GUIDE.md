# Demo Guide - Payment Gateway Integration

## 🎯 Panduan Cepat untuk Demo Tugas Akhir

Dokumen ini berisi langkah-langkah sederhana untuk demo payment gateway integration di kelas.

---

## 📋 Persiapan Sebelum Demo

1. **Start Server**
   ```bash
   php artisan serve
   ```
   Server akan berjalan di: http://localhost:8000

2. **Pastikan Database Sudah Running**
   - MySQL harus aktif
   - Database `barber_booking` sudah ada

3. **Login Credentials**
   - **Customer**: email yang ada di database dengan role 'customer'
   - **Admin**: email yang ada di database dengan role 'admin'
   - **Stylist**: email yang ada di database dengan role 'stylist'

---

## 🎬 Skenario Demo - Flow Lengkap

### **Skenario 1: Demo Payment Gateway (Lengkap dengan Midtrans)**

#### Langkah 1: Customer Membuat Booking
1. Login sebagai **customer**
2. Klik menu **"Bookings"** → **"Buat Booking Baru"**
3. Pilih:
   - Service: Pilih layanan (misal: Haircut)
   - Stylist: Pilih stylist yang tersedia
   - Tanggal & Waktu: Pilih slot yang available
4. Isi notes (optional)
5. Klik **"Booking Sekarang"**
6. Booking berhasil dibuat dengan status **"Pending"**

#### Langkah 2: Admin/Stylist Konfirmasi Booking
1. Logout dari customer
2. Login sebagai **admin** atau **stylist**
3. Buka menu **"Kelola Booking"**
4. Cari booking yang baru dibuat
5. Klik tombol **"Konfirmasi"** (hijau)
6. Booking status berubah menjadi **"Confirmed"**

#### Langkah 3: Customer Membayar via Gateway
1. Logout dari admin
2. Login kembali sebagai **customer**
3. Buka **"Bookings"** → Klik booking yang sudah dikonfirmasi
4. Di bagian "Informasi Pembayaran", ada 2 tombol:
   - **"Bayar Manual"** (pembayaran manual lama)
   - **"Bayar via Gateway"** (payment gateway baru)
5. Klik tombol **"Bayar via Gateway"**
6. Anda akan masuk ke halaman gateway payment
7. Klik tombol **"Bayar Sekarang"**
8. Popup Midtrans Snap akan muncul

#### Langkah 4A: Bayar dengan Test Card (Real Midtrans Demo)
1. Di popup Midtrans, pilih **"Credit/Debit Card"**
2. Isi dengan test card:
   - Card Number: `4811 1111 1111 1114`
   - Expiry: Any future date (contoh: 12/25)
   - CVV: `123`
3. Klik **"Pay"**
4. Masukkan OTP: `112233`
5. Payment akan sukses!
6. Anda akan diarahkan kembali ke detail booking
7. Status booking otomatis berubah menjadi **"Completed"**
8. Status payment menjadi **"Lunas"**

#### Langkah 4B: Bayar dengan Simulasi (Untuk Demo Cepat)
Jika popup Midtrans tidak muncul atau mau demo cepat:
1. Setelah klik "Bayar via Gateway", tutup saja popup Midtrans
2. Payment akan tercatat dengan status **"Pending"**
3. Kembali ke detail booking
4. Di bagian "Informasi Pembayaran", ada alert warning:
   - "Pembayaran sedang menunggu konfirmasi"
5. Klik tombol **"Simulasi Pembayaran Sukses (Demo)"**
6. Confirm popup
7. Payment langsung berubah menjadi **"Lunas"**
8. Booking status otomatis menjadi **"Completed"**

---

### **Skenario 2: Demo Tanpa Midtrans (Pure Simulasi)**

**Paling Simpel untuk Demo Kelas!**

1. Customer buat booking → Admin konfirmasi (sama seperti sebelumnya)
2. Customer klik **"Bayar via Gateway"**
3. **Langsung tutup popup Midtrans** (close tanpa bayar)
4. Payment tercatat sebagai **"Pending"**
5. Kembali ke detail booking
6. Klik **"Simulasi Pembayaran Sukses (Demo)"**
7. ✅ Done! Payment sukses, booking completed

**Waktu demo: ~2 menit**

---

## 🔍 Fitur yang Bisa Ditunjukkan

### 1. **Payment List dengan Filter**
- Buka menu **"Riwayat Pembayaran"**
- Ada filter:
  - Status (Lunas/Pending/Gagal)
  - Metode (Cash/Transfer/E-Wallet/dll)
  - Tanggal
- Payment gateway akan tampil dengan badge **"Gateway"** dan nama gateway (Midtrans)
- Ada tombol simulasi untuk payment yang pending

### 2. **Payment Receipt**
- Di riwayat pembayaran, klik icon **"Bukti Pembayaran"**
- Akan muncul invoice professional
- Bisa di-print (tombol print tersedia)

### 3. **Payment Details di Booking**
- Transaction ID untuk payment gateway
- Tipe pembayaran (Manual/Gateway/Crypto)
- Gateway name (Midtrans)

---

## 💡 Tips untuk Presentasi

### Apa yang Harus Dijelaskan:

1. **Problem**
   - Sistem lama hanya support pembayaran manual
   - Customer harus transfer manual dan konfirmasi admin
   - Tidak ada otomasi

2. **Solution**
   - Integrasi Payment Gateway (Midtrans)
   - Support berbagai metode pembayaran online:
     - GoPay, ShopeePay, QRIS
     - Virtual Account (BCA, BNI, BRI, dll)
     - Kartu Kredit/Debit
   - Otomatis update status booking setelah pembayaran sukses
   - Real-time payment notification via webhook

3. **Technical Implementation**
   - Migration: Menambah field untuk gateway payment
   - Service Layer: PaymentGatewayService untuk handle Midtrans
   - Webhook: Menerima notification dari Midtrans
   - Security: API key protection, HTTPS ready

4. **Future Enhancement**
   - Crypto payment support (database schema sudah ready)
   - Multiple payment gateway (Xendit, iPaymu, dll)
   - Payment analytics dashboard

---

## 🚨 Troubleshooting Demo

### Problem: Popup Midtrans tidak muncul
**Solusi**:
- Cek internet connection
- Atau langsung pakai fitur "Simulasi Pembayaran Sukses"

### Problem: Payment tidak update setelah bayar di Midtrans
**Solusi**:
- Klik tombol "Cek Status Pembayaran"
- Atau pakai fitur "Simulasi Pembayaran Sukses"

### Problem: Booking tidak bisa dikonfirmasi
**Solusi**:
- Pastikan login sebagai admin atau stylist
- Booking harus berstatus "Pending"

---

## 📊 Flow Diagram untuk Presentasi

```
┌─────────────┐
│  Customer   │
│ Buat Booking│
└──────┬──────┘
       │
       ↓
┌─────────────┐
│Admin/Stylist│
│  Konfirmasi │
└──────┬──────┘
       │
       ↓
┌─────────────────────┐
│    Customer         │
│ Pilih Metode Bayar: │
│ - Manual            │
│ - Gateway ← NEW!    │
└──────┬──────────────┘
       │
       ↓
┌─────────────────────┐
│   Gateway Payment   │
│ (Midtrans Snap)     │
│ - Pilih Metode      │
│ - Bayar             │
└──────┬──────────────┘
       │
       ↓
┌─────────────────────┐
│    Webhook          │
│ Midtrans → System   │
│ Update Status       │
└──────┬──────────────┘
       │
       ↓
┌─────────────────────┐
│  Auto Update:       │
│ - Payment: PAID     │
│ - Booking: COMPLETE │
└─────────────────────┘
```

---

## 🎓 Poin-poin untuk Nilai Presentasi

### **Technical Skills** ✅
- Laravel integration dengan third-party API
- Service layer pattern
- Webhook implementation
- Database migration & schema design
- Security (API key protection)

### **Problem Solving** ✅
- Identifikasi masalah payment manual
- Solusi dengan automation
- Handle edge cases (pending payment, timeout, dll)

### **Best Practices** ✅
- Separation of concerns (Service class)
- Transaction handling (DB::beginTransaction)
- Logging untuk debugging
- Documentation lengkap

### **User Experience** ✅
- Multiple payment options
- Real-time status update
- Clear payment receipt
- Demo mode untuk testing

---

## 📞 Q&A yang Mungkin Ditanya Dosen

### Q: "Kenapa pakai Midtrans?"
**A**: Midtrans adalah payment gateway terpopuler di Indonesia, support banyak payment method, dokumentasi lengkap, dan mudah diintegrate dengan Laravel.

### Q: "Bagaimana keamanan payment?"
**A**:
- API keys disimpan di .env (tidak di-commit ke git)
- Webhook signature verification dari Midtrans
- HTTPS untuk production
- PCI DSS compliant (handled by Midtrans)

### Q: "Berapa biaya pakai Midtrans?"
**A**:
- Sandbox (testing): Gratis
- Production: Fee 2.9% + Rp 2.000 per transaksi (untuk kartu kredit)
- Tergantung payment method yang digunakan

### Q: "Bagaimana handle jika payment gagal?"
**A**:
- Payment tercatat dengan status "failed"
- Customer bisa retry payment
- Admin bisa manual update status
- Auto-expire setelah 24 jam

### Q: "Apa bedanya dengan payment manual?"
**A**:
- Manual: Customer transfer → screenshot → admin verify (slow)
- Gateway: Customer bayar → otomatis verify → booking complete (fast)

---

## ✅ Checklist Demo

Sebelum demo, pastikan:
- [ ] Server running (`php artisan serve`)
- [ ] Database running (MySQL)
- [ ] Ada minimal 1 customer, 1 admin, 1 stylist
- [ ] Ada minimal 1 service dan 1 stylist active
- [ ] Internet connection OK (untuk Midtrans Snap)
- [ ] Browser tidak block popup (untuk Midtrans)
- [ ] Test payment flow sekali sebelum presentasi

---

## 🎉 Selamat Demo!

**Durasi ideal demo**: 5-10 menit

**Good luck!** 🚀
