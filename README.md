# Sistem Peminjaman Buku Perpustakaan Kampus

Aplikasi PHP native (tanpa framework) untuk studi kasus CRUD buku +
transaksi peminjaman dengan Service Layer, validasi, exception
handling, logging, dan pola PRG (Post-Redirect-Get).

## Cara Menjalankan

1. **Buat database**
   Import `database.sql` ke MySQL, misalnya lewat phpMyAdmin atau CLI:
   ```
   mysql -u root -p < database.sql
   ```

2. **Atur koneksi database** (jika perlu) di `config/Database.php`
   — defaultnya host `127.0.0.1`, user `root`, password kosong.

3. **Jalankan server PHP bawaan** dari folder `public/`:
   ```
   cd public
   php -S localhost:8000
   ```
   Lalu buka `http://localhost:8000` di browser.

   (Bisa juga diletakkan di htdocs XAMPP/Laragon seperti biasa.)

## Struktur Folder

```
perpustakaan/
├── database.sql                 # skema + data contoh
├── bootstrap.php                # autoload class + siapkan service
├── config/Database.php          # koneksi PDO ke MySQL
├── src/
│   ├── Logger.php               # pencatat log ke logs/app.log
│   ├── Validators/Validator.php # validasi input (NIM, tanggal, dll)
│   ├── Exceptions/               # 4 exception khusus sesuai soal
│   ├── Repositories/              # query SQL mentah (tanpa aturan bisnis)
│   │   ├── BukuRepository.php
│   │   └── PeminjamanRepository.php
│   └── Services/                  # SEMUA aturan bisnis ada di sini
│       ├── BukuService.php
│       └── PeminjamanService.php
├── public/                       # yang diakses browser (thin controller)
│   ├── index.php
│   ├── buku.php
│   └── peminjaman.php
├── views/                        # tampilan HTML (Bootstrap 5)
│   ├── buku_view.php
│   └── peminjaman_view.php
└── logs/app.log                  # log otomatis transaksi
```

## Alur Arsitektur (mengapa dipisah begini)

```
Browser --> Controller (public/*.php) --> Service (aturan bisnis)
                                              --> Validator (cek input)
                                              --> Repository (query DB)
                                              --> Logger (catat aktivitas)
```

Controller sengaja dibuat "bodoh" (thin): hanya menerima input dan
menampilkan hasil. Semua keputusan bisnis (boleh/tidak boleh pinjam,
kapan stok berkurang, dsb.) ada di Service — sesuai syarat soal
nomor 8.
