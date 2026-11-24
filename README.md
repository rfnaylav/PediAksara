# Sistem Perpustakaan

Aplikasi web perpustakaan dengan Laravel 12, Blade, Tailwind CSS, dan Eloquent. Implementasi saat ini mencakup alur web Fase 1 dari PRD.

## Persyaratan

- PHP 8.2 atau lebih baru dan Composer
- Node.js dan npm untuk membangun aset frontend
- SQLite (default) atau MySQL

## Instalasi lokal

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Jika file `.env` sudah ada, jangan menyalinnya ulang. Atur koneksi database di `.env` sebelum menjalankan migration.

Untuk membuat akun administrator secara interaktif:

```powershell
php artisan app:create-admin
```

Perintah meminta nama, email, serta kata sandi dan konfirmasinya. Kata sandi admin minimal 12 karakter. Alternatifnya, isi `ADMIN_NAME`, `ADMIN_EMAIL`, dan `ADMIN_PASSWORD` di `.env` sebelum menjalankan `php artisan migrate --seed`. Jangan gunakan kredensial contoh pada server produksi.

## Fitur Fase 1

- Registrasi anggota, login, logout, dan pembatasan akses untuk admin, pustakawan, dan anggota.
- CRUD buku, kategori, penulis, penerbit, serta akun anggota dan pustakawan.
- Unggah sampul buku JPG/PNG/WebP hingga 2 MB; sampul muncul pada katalog dan detail buku.
- Pencarian katalog berdasarkan judul, penulis, dan kategori.
- Peminjaman beberapa buku, batas tiga buku aktif per anggota, dan pengurangan stok otomatis.
- Perpanjangan hingga dua kali, masing-masing tujuh hari.
- Pengembalian per buku, penambahan stok, dan denda Rp1.000 per hari.
- Peminjaman baru diblokir selama anggota memiliki denda belum lunas.
- Dashboard, daftar keterlambatan, pencatatan pembayaran denda, dan laporan transaksi dengan filter tanggal.
- Denda pinjaman yang masih berjalan disinkronkan setiap hari oleh scheduler.

API bertoken dan SPA tercantum sebagai Fase 2 pada PRD dan belum termasuk dalam implementasi web ini.

Jalankan scheduler Laravel di lingkungan produksi atau lokal agar denda pinjaman yang belum dikembalikan terus diperbarui:

```powershell
php artisan schedule:work
```

## Pengujian

```powershell
php artisan test
```

Database default untuk pengujian memakai SQLite in-memory.
