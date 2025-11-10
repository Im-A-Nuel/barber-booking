# Midtrans Payment Gateway Integration

## Overview
Sistem pembayaran barber booking telah diintegrasikan dengan Midtrans Payment Gateway, memungkinkan customer untuk melakukan pembayaran online dengan berbagai metode pembayaran.

## Fitur yang Ditambahkan

### 1. Payment Gateway Support
- Pembayaran via Midtrans Snap (pop-up payment)
- Support berbagai metode pembayaran:
  - GoPay
  - ShopeePay
  - QRIS
  - Virtual Account (BCA, BNI, BRI, Permata, dll)
  - Kartu Kredit/Debit
  - E-Channel (Mandiri Bill Payment)

### 2. Database Schema
Ditambahkan field baru ke tabel `payments`:
- `payment_type` - Tipe pembayaran (manual, gateway, crypto)
- `gateway_name` - Nama payment gateway (midtrans, xendit, dll)
- `transaction_id` - Transaction ID dari payment gateway
- `payment_url` - URL pembayaran untuk redirect
- `gateway_response` - Raw response dari gateway (JSON)
- `crypto_currency`, `crypto_amount`, `crypto_address`, `crypto_tx_hash` - untuk future crypto payment
- `expires_at` - Waktu kadaluarsa pembayaran

## Setup & Configuration

### 1. Install Dependencies
```bash
composer require midtrans/midtrans-php
```

### 2. Environment Configuration
Tambahkan ke file `.env`:
```env
MIDTRANS_SERVER_KEY=SB-Mid-server-xxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxx
MIDTRANS_IS_PRODUCTION=false
```

### 3. Mendapatkan API Keys

#### Sandbox (Testing)
1. Daftar di https://dashboard.sandbox.midtrans.com/
2. Login dan buka Settings → Access Keys
3. Copy Server Key dan Client Key ke `.env`

#### Production
1. Daftar di https://dashboard.midtrans.com/
2. Lengkapi proses verifikasi bisnis
3. Login dan buka Settings → Access Keys
4. Copy Production keys
5. Set `MIDTRANS_IS_PRODUCTION=true` di `.env`

### 4. Run Migration
```bash
php artisan migrate
```

## Cara Menggunakan

### Untuk Customer

#### 1. Membuat Pembayaran Gateway
1. Buka detail booking yang sudah dikonfirmasi
2. Pada bagian "Informasi Pembayaran", klik tombol **"Bayar via Gateway"**
3. Anda akan diarahkan ke halaman pembayaran
4. Klik tombol **"Bayar Sekarang"**
5. Pop-up Midtrans Snap akan muncul
6. Pilih metode pembayaran yang diinginkan
7. Ikuti instruksi pembayaran
8. Setelah pembayaran berhasil, Anda akan diarahkan kembali ke detail booking

#### 2. Cek Status Pembayaran
Jika pembayaran masih pending:
1. Buka detail booking
2. Pada bagian "Informasi Pembayaran", klik tombol **"Cek Status Pembayaran"**
3. Sistem akan mengecek status terbaru dari Midtrans

### Testing dengan Midtrans Sandbox

#### Test Cards (Credit/Debit Card)
- **Success**: 4811 1111 1111 1114
- **Failure**: 4911 1111 1111 1113
- **Challenge**: 4411 1111 1111 1118

CVV: 123
Expiry: Any future date
OTP/3DS: 112233

#### GoPay
- Nomor HP: 081234567890
- PIN: 123456

#### Virtual Account
- Simulasi pembayaran otomatis tersedia di dashboard sandbox

## Webhook/Callback

### URL Callback
```
POST https://your-domain.com/payments/midtrans/callback
```

### Testing Webhook Locally
Gunakan ngrok atau expose untuk testing webhook:

1. Install ngrok: https://ngrok.com/download
2. Jalankan:
```bash
ngrok http 8000
```
3. Copy HTTPS URL yang diberikan ngrok
4. Set di Midtrans Dashboard → Settings → Configuration:
   - Payment Notification URL: https://xxx.ngrok.io/payments/midtrans/callback
   - Finish Redirect URL: https://xxx.ngrok.io/bookings/{booking_id}
   - Error Redirect URL: https://xxx.ngrok.io/bookings/{booking_id}

### Webhook Flow
1. Customer menyelesaikan pembayaran di Midtrans
2. Midtrans mengirim notification ke callback URL
3. System memproses notification dan update status payment
4. Jika payment berhasil, booking status otomatis diupdate ke "completed"

## Security Considerations

### 1. Signature Verification
Midtrans mengirim signature untuk verifikasi authenticity webhook. PaymentGatewayService sudah handle ini secara otomatis.

### 2. HTTPS Required
**PENTING**: Production environment HARUS menggunakan HTTPS untuk:
- Webhook callback URL
- Seluruh aplikasi (untuk keamanan data customer)

### 3. API Keys Protection
- Jangan commit API keys ke git
- Gunakan `.env` untuk menyimpan keys
- Pastikan `.env` ada di `.gitignore`
- Rotate keys secara berkala di production

## Flow Diagram

```
Customer → Pilih Booking → Confirm Booking (Admin/Stylist)
    ↓
Booking Status = Confirmed
    ↓
Customer → Klik "Bayar via Gateway"
    ↓
System → Create Payment Record (status: pending)
    ↓
System → Get Snap Token from Midtrans
    ↓
Show Payment Page → Customer klik "Bayar Sekarang"
    ↓
Midtrans Snap Popup → Customer pilih metode & bayar
    ↓
Midtrans → Send Webhook to /payments/midtrans/callback
    ↓
System → Update Payment Status
    ↓
If status = paid → Update Booking Status to Completed
    ↓
Customer → Redirect ke Detail Booking (payment success)
```

## Troubleshooting

### Payment tidak terupdate setelah bayar
1. Cek log Laravel: `storage/logs/laravel.log`
2. Cek webhook sampai ke server (cek log)
3. Pastikan webhook URL accessible dari internet
4. Gunakan ngrok untuk testing local

### Error: "Failed to create payment"
1. Cek API keys di `.env`
2. Pastikan Midtrans SDK terinstall
3. Cek connection ke Midtrans API
4. Lihat error detail di log

### Webhook tidak jalan di local
1. Gunakan ngrok atau expose
2. Set webhook URL di Midtrans dashboard
3. Test dengan simulator di Midtrans dashboard

### Payment stuck di pending
Customer bisa:
1. Klik tombol "Cek Status Pembayaran" di detail booking
2. Atau admin bisa manual update via payments.edit

## API Reference

### PaymentGatewayService Methods

#### `createMidtransPayment(Booking $booking, $method = 'gateway')`
Create payment dan get Snap token dari Midtrans.

**Returns:** Array dengan keys:
- `snap_token` - Token untuk Midtrans Snap
- `payment` - Payment model instance

#### `handleMidtransCallback($notificationData)`
Handle webhook notification dari Midtrans.

**Returns:** Updated Payment model

#### `getPaymentStatus($orderId)`
Get current payment status dari Midtrans.

**Returns:** Midtrans transaction status object

## Future Enhancements (Crypto Payment)

Database schema sudah support untuk cryptocurrency payment. Untuk implementasi:

1. Integrate dengan crypto payment gateway (Coinbase Commerce, NOWPayments, dll)
2. Update PaymentGatewayService dengan crypto methods
3. Create crypto payment view
4. Handle price volatility (convert IDR to crypto real-time)

## Support

Untuk pertanyaan atau issue terkait Midtrans integration:
1. Cek dokumentasi Midtrans: https://docs.midtrans.com/
2. Contact Midtrans support: support@midtrans.com
3. Midtrans Slack Community: https://midtrans.com/slack-invite

## Credits

- Midtrans PHP SDK: https://github.com/Midtrans/midtrans-php
- Midtrans Documentation: https://docs.midtrans.com/
