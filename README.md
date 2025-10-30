# Barber Booking

Barber Booking adalah Mini Project 1 untuk mata kuliah Pemrograman Web Lanjut (PWL) yang membangun fondasi aplikasi booking barbershop berbasis Laravel 6. Fokus saat ini adalah pengelolaan data barber dan penyiapan struktur yang mudah dikembangkan menjadi sistem pemesanan layanan.

## Fitur Utama
- Manajemen layanan barbershop melalui modul Services (model `app/Service.php`, migrasi `database/migrations/2025_10_30_000000_create_services_table.php`, resource controller & tampilan Blade).
- Manajemen data barber melalui model `app/Barber.php` dan migrasi `database/migrations/2025_10_28_112930_create_barbers_tabel.php`.
- Kolom-kolom penting meliputi nama, spesialisasi, tahun pengalaman, nomor telepon, rata-rata rating, dan status aktif.
- Seeder contoh (`ServiceSeeder`, `BarberFakerSeeder`) untuk menghasilkan data dummy sehingga pengujian awal lebih mudah.
- Konfigurasi dasar Laravel (auth scaffolding, logging, queue, jobs) siap dipakai ketika modul lanjutan ditambahkan.

## Persyaratan Sistem
- PHP 7.2.5 ke atas (disarankan PHP 8.0)
- Composer
- MySQL / MariaDB
- Node.js & npm (opsional untuk kompilasi asset memakai Laravel Mix)

## Langkah Instalasi
1. Clone repositori lalu masuk ke folder proyek.
2. Install dependency PHP:
   ```bash
   composer install
   ```
3. Salin file lingkungan kemudian atur koneksi basis data:
   ```bash
   cp .env.example .env   # atau duplikasi manual di Windows
   ```
   Sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` dengan server lokal.
4. Generate application key (lewati jika `APP_KEY` sudah ada di `.env`):
   ```bash
   php artisan key:generate
   ```
5. Jalankan migrasi untuk membuat tabel:
   ```bash
   php artisan migrate
   ```
6. (Opsional) Isi data dummy memakai seeder:
   ```bash
   php artisan db:seed --class=BarberFakerSeeder
   ```
   Untuk satu contoh barber statis gunakan `BarbersSeeder`.
7. (Opsional) Instal dependency front-end dan kompilasi asset:
   ```bash
   npm install
   npm run dev
   ```

## Menjalankan Aplikasi
Gunakan server development Laravel:
```bash
php artisan serve
```
Aplikasi dapat diakses di `http://localhost:8000`.

## Struktur Direktori Penting
- `app/Service.php` - Model Eloquent untuk entitas layanan.
- `app/Barber.php` - Model Eloquent untuk entitas barber.
- `database/migrations/2025_10_30_000000_create_services_table.php` - Skema tabel `services`.
- `database/migrations/2025_10_28_112930_create_barbers_tabel.php` - Skema tabel `barbers`.
- `database/seeds/ServiceSeeder.php`, `database/seeds/BarberFakerSeeder.php` - Seeder data dummy berbasis Faker.
- `resources/views/services` - Halaman CRUD admin untuk layanan.
- `resources/views` - Tempat untuk menambahkan antarmuka Blade lainnya.
- `routes/web.php` - Titik awal menambahkan rute web atau dashboard booking.

## Pengembangan Lanjutan
- Membuat modul booking lengkap (slot layanan, jadwal barber, histori transaksi).
- Menambahkan autentikasi terpisah untuk customer dan barber.
- Menyediakan API JSON agar mudah diintegrasikan dengan aplikasi mobile.
- Membangun tampilan dashboard manajemen dan halaman landing page.

## Testing
Jalankan seluruh pengujian dengan:
```bash
vendor/bin/phpunit
```

## Lisensi
Proyek mengikuti lisensi MIT yang dibawa oleh kerangka Laravel.
