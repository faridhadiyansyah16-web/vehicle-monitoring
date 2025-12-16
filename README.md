# Vehicle Monitoring (Laravel)

Aplikasi monitoring kendaraan perusahaan berbasis Laravel untuk mengelola pemesanan, kendaraan, driver, log BBM, log servis, persetujuan, serta dashboard penggunaan dengan ekspor CSV/Excel.

## Fitur
- Pemesanan kendaraan dengan alur persetujuan berjenjang.
- Manajemen kendaraan dan driver.
- Log BBM dan log servis per kendaraan.
- Dashboard penggunaan dengan periode 3/6/12 bulan.
- Ekspor data:
  - CSV: pemesanan
  - Excel: pemesanan, kendaraan, driver, log BBM, log servis
  - Excel Usage: ringkasan jumlah pemakaian per bulan

## Persyaratan
- PHP 8.1+
- Ekstensi PHP: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`, `zip`
- MySQL 5.7+/8+
- Composer
- Node.js dan npm (opsional, untuk Vite)

## Instalasi
```bash
# 1) Masuk ke direktori aplikasi
cd laravel_app

# 2) Install dependencies PHP
composer install

# 3) Siapkan environment
cp .env.example .env
php artisan key:generate

# 4) Konfigurasi koneksi database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=vehicle_monitoring
# DB_USERNAME=...
# DB_PASSWORD=...

# 5) Migrasi database
php artisan migrate
```

### Mengaktifkan ekstensi PHP (Windows/XAMPP)
- Buka `php.ini` dan aktifkan:
  - `extension=gd`
  - `extension=zip`
- Jika menggunakan konfigurasi khusus, sesuaikan `PHPRC` agar PHP CLI membaca file `php.ini` tersebut.

## Menjalankan Aplikasi
```bash
# Opsi A: Laravel built-in server
php artisan serve
# akses: http://127.0.0.1:8000

# Opsi B: PHP dev server dengan router Laravel (direkomendasikan untuk rute ekspor)
php -S 127.0.0.1:8010 server.php
# akses: http://127.0.0.1:8010
```

## Akun dan Akses
- Rute aplikasi berada di balik autentikasi (`auth`). Buat akun melalui halaman register atau seeding sesuai kebutuhan.
- Akun default (hasil seeding bawaan):
  - Email: `test@example.com`
  - Password: `password`
  - Role: `user` (ubah ke `admin`/`approver` via update user jika diperlukan)

## Ekspor Data
- CSV pemesanan: `GET /reports/bookings.csv` (mengikuti query filter)
- Excel pemesanan: `GET /reports/bookings.xlsx?status=&vehicle_id=&from=&to=`
- Excel kendaraan: `GET /reports/vehicles.xlsx`
- Excel driver: `GET /reports/drivers.xlsx`
- Excel BBM: `GET /reports/fuel_logs.xlsx?vehicle_id=<ID>&from=&to=`
- Excel servis: `GET /reports/service_logs.xlsx?vehicle_id=<ID>&from=&to=`
- Excel usage: `GET /reports/usage.xlsx?months=3|6|12`

## Dashboard
- Pilih periode 3/6/12 bulan di bagian atas.
- Tombol “Export Usage” untuk mengunduh ringkasan pemakaian per bulan.
- KPI cards menampilkan ringkasan cepat: total kendaraan, pending, approved.

## Pengembangan Frontend
- Bootstrap 5 via CDN untuk komponen UI.
- Chart.js untuk grafik pemakaian.
- Stylesheet kustom: `public/css/app.css` (tema biru brand).

## Tips Troubleshooting
- Jika ekspor Excel gagal:
  - Pastikan `ext-gd` dan `ext-zip` aktif.
  - Jalankan server dengan `server.php` agar rute Laravel tidak ditangani sebagai file statis.
- Jika unduhan tidak muncul di webview IDE, buka URL di browser eksternal.

## Lisensi
Proyek ini menggunakan lisensi MIT.
