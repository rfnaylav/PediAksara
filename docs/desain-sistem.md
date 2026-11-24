# Desain Sistem Perpustakaan

Dokumen ini merangkum desain web MVC Fase 1 dari PRD. Peran `pustakawan` juga merupakan petugas layanan.

## Use case

```mermaid
flowchart LR
    Admin[Admin] --> Master[Kelola buku, kategori, penulis, penerbit]
    Admin --> Akun[Kelola akun anggota dan pustakawan]
    Admin --> Dashboard[Dashboard]
    Pustakawan[Pustakawan] --> Transaksi[Catat peminjaman, perpanjangan, pengembalian]
    Pustakawan --> Pantau[Periksa keterlambatan dan denda]
    Pustakawan --> Laporan[Lihat laporan]
    Pustakawan --> Dashboard
    Anggota[Anggota] --> Daftar[Registrasi dan login]
    Anggota --> Katalog[Cari dan lihat katalog]
    Anggota --> Riwayat[Lihat riwayat dan keterlambatan sendiri]
```

## ERD

```mermaid
erDiagram
    USERS ||--o{ LOANS : meminjam
    USERS o|--o{ LOANS : melayani
    CATEGORIES ||--o{ BOOKS : mengelompokkan
    AUTHORS ||--o{ BOOKS : menulis
    PUBLISHERS ||--o{ BOOKS : menerbitkan
    LOANS ||--|{ LOAN_ITEMS : berisi
    BOOKS ||--o{ LOAN_ITEMS : dipinjam
    LOANS ||--o| FINES : memiliki
    USERS {
        bigint id PK
        string name
        string email UK
        string password
        string role
        string member_code UK
        string phone
        text address
    }
    CATEGORIES {
        bigint id PK
        string name UK
    }
    AUTHORS {
        bigint id PK
        string name UK
    }
    PUBLISHERS {
        bigint id PK
        string name UK
    }
    BOOKS {
        bigint id PK
        bigint category_id FK
        bigint author_id FK
        bigint publisher_id FK
        string title
        string isbn UK
        smallint year
        int stock
    }
    LOANS {
        bigint id PK
        bigint user_id FK
        bigint librarian_id FK
        date loan_date
        date due_date
        int extend_count
        string status
    }
    LOAN_ITEMS {
        bigint id PK
        bigint loan_id FK
        bigint book_id FK
        date returned_at
    }
    FINES {
        bigint id PK
        bigint loan_id FK
        int days_late
        int amount
        string status
        datetime paid_at
    }
```

## Alur peminjaman dan pengembalian

```mermaid
flowchart TD
    A[Pustakawan memilih anggota dan buku] --> B{Anggota punya denda belum lunas?}
    B -- Ya --> X[Tolak peminjaman]
    B -- Tidak --> C{Batas 3 buku dan stok tersedia?}
    C -- Tidak --> X
    C -- Ya --> D[Buat transaksi dan item pinjaman]
    D --> E[Kurangi stok setiap buku]
    E --> F{Buku dikembalikan?}
    F -- Tidak --> G{Sudah melewati jatuh tempo?}
    G -- Ya --> H[Scheduler menghitung denda Rp1.000 per hari per buku]
    H --> F
    G -- Tidak --> F
    F -- Ya --> I[Catat tanggal kembali dan tambah stok]
    I --> J{Semua buku pada transaksi kembali?}
    J -- Tidak --> F
    J -- Ya --> K[Tandai transaksi selesai]
    K --> L{Denda belum lunas?}
    L -- Ya --> M[Pustakawan mencatat pembayaran]
    L -- Tidak --> N[Selesai]
    M --> N
```

## Aturan implementasi

- Satu transaksi boleh berisi maksimal tiga buku; anggota juga dibatasi maksimal tiga buku aktif secara total.
- Denda dihitung Rp1.000 per hari per buku terlambat dan diringkas per transaksi.
- Peminjaman baru ditolak selama ada denda belum lunas.
- Penghapusan buku/master/akun yang sudah direferensikan transaksi ditolak agar riwayat tetap utuh.
- Scheduler `loans:sync-fines` dijalankan setiap hari pukul 00:10 untuk memperbarui denda pinjaman aktif yang terlambat.
