PRD Sistem Peminjaman Buku Perpustakaan
Nov 23, 2026 · @nayy
Ringkasan
Sistem Peminjaman Buku Perpustakaan adalah aplikasi web berbasis Laravel untuk mencatat peminjaman, perpanjangan, pengembalian, dan denda buku. Aplikasi dipakai tiga peran: admin yang mengelola data buku dan akun, pustakawan yang melayani transaksi harian, dan anggota yang mencari buku serta melihat riwayat pinjamnya. Hasil akhirnya berupa aplikasi yang berjalan online (demo live), repositori GitHub, dan dokumentasi API.
Latar Belakang dan Tujuan
Pencatatan peminjaman secara manual rentan salah: stok tidak sinkron, keterlambatan tidak terpantau, dan denda dihitung dengan tangan. Aplikasi ini menggantikannya dengan data terpusat.
Tujuan:
• Transaksi pinjam dan kembali tercatat, dan stok buku berubah otomatis.
• Keterlambatan terdeteksi dan denda dihitung otomatis.
• Setiap peran hanya mengakses fitur sesuai tugasnya.
• Fitur inti bisa dipakai lewat web dan lewat API.
Ukuran keberhasilan (usulan):
• Satu transaksi peminjaman selesai dalam satu form tanpa edit stok manual.
• Denda benar pada tiga skenario uji: tepat waktu, terlambat 1 hari, terlambat lebih dari 7 hari.
• Seluruh CRUD berjalan tanpa error pada demo live.
Peran Pengguna
Ada tiga peran. Pustakawan sama dengan "petugas" di use case diagram; di database nilainya pustakawan.
Peran
Tugas
Akses utama
Admin
Mengelola sistem
CRUD buku, kategori, penulis, penerbit; kelola akun pustakawan dan anggota; lihat data member dan petugas; dashboard transaksi dan buku
Pustakawan
Melayani transaksi harian
Proses peminjaman, perpanjangan, pengembalian; cek keterlambatan; laporan peminjaman, pengembalian, dan denda; catat pembayaran denda; dashboard
Anggota
Peminjam
Registrasi dan login; cari buku dan lihat detail; riwayat pinjam sendiri; cek keterlambatan dan denda sendiri
Ruang Lingkup
Termasuk:
• Autentikasi dengan pembatasan akses per peran
• CRUD data master: buku, kategori, penulis, penerbit, akun
• Transaksi peminjaman, perpanjangan, pengembalian, dan denda
• Laporan dan dashboard
• REST API dengan dokumentasi, dan tampilan SPA (Fase 2)
Tidak termasuk (asumsi, bisa diubah):
• Pembayaran online; denda dicatat manual oleh pustakawan
• Notifikasi email atau WhatsApp
• Reservasi buku dan buku digital
• Pemindai barcode
Kebutuhan Fungsional
ID
Modul
Kebutuhan
Peran
FR-01
Autentikasi
Login dan logout; registrasi anggota baru
Semua
FR-02
Data master
CRUD buku, kategori, penulis, penerbit dengan validasi (ISBN unik, stok tidak negatif)
Admin
FR-03
Akun
CRUD akun pustakawan dan anggota; kode anggota unik
Admin
FR-04
Katalog
Cari buku berdasarkan judul, penulis, atau kategori; lihat detail buku
Semua
FR-05
Peminjaman
Pilih anggota dan satu atau lebih buku; cek stok dan batas pinjam; simpan transaksi; stok berkurang
Pustakawan
FR-06
Perpanjangan
Perpanjang jatuh tempo selama belum melewati batas perpanjangan
Pustakawan
FR-07
Pengembalian
Catat pengembalian per buku; stok bertambah; transaksi selesai bila semua buku kembali
Pustakawan
FR-08
Keterlambatan
Daftar pinjaman yang lewat jatuh tempo; anggota melihat miliknya sendiri
Pustakawan, Anggota
FR-09
Denda
Denda terbentuk otomatis saat terlambat; pembayaran mengubah status menjadi lunas
Pustakawan
FR-10
Laporan
Laporan peminjaman, pengembalian, dan denda dengan filter tanggal; riwayat transaksi
Pustakawan
FR-11
Dashboard
Ringkasan transaksi dan buku
Admin, Pustakawan
FR-12
API
Endpoint REST untuk modul di atas dengan autentikasi token (Fase 2)
Semua
Aturan Bisnis
Nilai berikut adalah usulan awal dan disimpan sebagai konstanta di model Loan, jadi mudah diubah jika dosen menetapkan angka lain.
Aturan
Nilai
Lama pinjam
7 hari
Maksimal perpanjangan
2 kali, masing-masing 7 hari
Denda keterlambatan
Rp1.000 per hari
Maksimal pinjaman aktif per anggota
3 buku
Stok
Berkurang 1 saat dipinjam, bertambah 1 saat dikembalikan
Peminjaman ditolak
Jika stok buku 0 atau batas pinjam tercapai
Model Data
Delapan tabel, sesuai file erd_perpustakaan.drawio. Semua tabel kecuali loan_items punya created_at dan updated_at.
Tabel
Kolom utama
Relasi
users
name, email, password, role, member_code, phone, address
Punya banyak loans (sebagai peminjam dan sebagai petugas)
categories
name
Punya banyak books
authors
name
Punya banyak books
publishers
name
Punya banyak books
books
category_id, author_id, publisher_id, title, isbn, year, stock
Punya banyak loan_items
loans
user_id, librarian_id, loan_date, due_date, extend_count, status (dipinjam / selesai)
Punya banyak loan_items dan fines
loan_items
loan_id, book_id, returned_at
Penghubung loans dan books
fines
loan_id, days_late, amount, status (belum / lunas), paid_at
Milik satu loan
Kebutuhan Non-Fungsional
• Keamanan: password di-hash, proteksi CSRF, otorisasi per peran lewat middleware atau policy.
• Validasi: semua input divalidasi di sisi server dan pesan error ditampilkan jelas.
• Performa: daftar memakai pagination dan eager loading untuk relasi.
• Tampilan: responsif di layar laptop dan ponsel.
• Pemeliharaan: kode rapi mengikuti konvensi Laravel, riwayat commit teratur di GitHub.
• Ketersediaan: aplikasi dapat diakses online untuk demo.
Teknologi dan Arsitektur
Aplikasi memakai pola MVC Laravel, dengan lapisan API terpisah untuk tampilan SPA.
Bagian
Pilihan
Framework
Laravel (PHP 8.2 atau lebih baru), Eloquent ORM
Tampilan Fase 1
Blade dengan Bootstrap atau Tailwind (belum dipilih)
Database
MySQL atau SQLite
API
REST dengan Laravel Sanctum untuk token
SPA (Fase 2)
Belum ditentukan
Dokumentasi API
Postman atau Swagger
Repositori
GitHub
Demo live
Hosting gratis seperti Railway atau Render
Rencana Fase dan Timeline
Pengerjaan terbagi tiga fase sesuai jadwal tugas, dengan target minggu 7, 11, dan 15.
Fase
Target
Isi
Hasil
1. Core CRUD
Minggu 7
Desain sistem (use case, ERD), migration dan model Eloquent, CRUD (routing, controller, Blade, validasi), flowchart alur sistem
Desain lengkap dan CRUD berjalan
2. API dan SPA
Minggu 11
Rancangan tabel, rancangan database, rancangan dashboard, dokumentasi API (Postman atau Swagger)
API berfungsi, SPA dan dokumentasi
3. Final
Minggu 15
Tampilan final aplikasi, repositori GitHub, demo live
Aplikasi online dan siap dipresentasikan
Kriteria Penerimaan dan Deliverable
Proyek dianggap selesai bila semua butir berikut terpenuhi:
[ ] Dokumen desain: use case, ERD, dan flowchart
[ ] Migration dan seeder berjalan dengan php artisan migrate --seed
[ ] CRUD buku, kategori, penulis, penerbit, dan akun berjalan dengan validasi
[ ] Alur peminjaman, perpanjangan, pengembalian, dan denda berjalan dari awal sampai akhir
[ ] Akses dibatasi per peran (admin, pustakawan, anggota)
[ ] Laporan dan dashboard menampilkan data benar
[ ] API terdokumentasi di Postman atau Swagger
[ ] Kode di GitHub dengan README cara instalasi
[ ] Demo live bisa diakses online
Asumsi, Risiko, dan Pertanyaan Terbuka
Asumsi:
• Aturan bisnis (7 hari, 2 kali perpanjangan, Rp1.000 per hari, 3 buku) adalah usulan, belum dari dosen.
• Pembayaran denda dicatat manual, tanpa payment gateway.
Risiko:
• Deploy untuk demo live baru dicoba di akhir sehingga rawan masalah konfigurasi; sebaiknya diuji lebih awal.
• Pilihan teknologi SPA belum ditentukan dan bisa menggeser jadwal Fase 2.
Pertanyaan terbuka:
• Apakah anggota dengan denda belum lunas boleh meminjam lagi?
• Apakah dosen menetapkan jumlah tabel minimal atau database tertentu?
• UI memakai Bootstrap atau Tailwind?