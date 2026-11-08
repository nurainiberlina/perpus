-- ============================================================
-- DATABASE: perpustakaan
-- Sistem Peminjaman Buku Perpustakaan Kampus
-- ============================================================

CREATE DATABASE IF NOT EXISTS perpustakaan;
USE perpustakaan;

-- Tabel data buku
CREATE TABLE IF NOT EXISTS buku (
    id_buku       VARCHAR(10) PRIMARY KEY,
    judul         VARCHAR(150) NOT NULL,
    penulis       VARCHAR(100) NOT NULL,
    penerbit      VARCHAR(100) NOT NULL,
    tahun_terbit  YEAR NOT NULL,
    stok          INT NOT NULL DEFAULT 0,
    status        ENUM('Tersedia', 'Habis') NOT NULL DEFAULT 'Tersedia'
) ENGINE=InnoDB;

-- Tabel data peminjaman
-- tanggal_kembali        = tanggal jatuh tempo (dijanjikan saat form peminjaman diisi)
-- tanggal_kembali_aktual = tanggal saat buku benar-benar dikembalikan (diisi sistem, bukan input mahasiswa)
CREATE TABLE IF NOT EXISTS peminjaman (
    id_peminjaman        VARCHAR(10) PRIMARY KEY,
    nim                  VARCHAR(15) NOT NULL,
    id_buku              VARCHAR(10) NOT NULL,
    tanggal_pinjam       DATE NOT NULL,
    tanggal_kembali      DATE NOT NULL,
    tanggal_kembali_aktual DATE NULL,
    status_peminjaman    ENUM('Dipinjam', 'Kembali') NOT NULL DEFAULT 'Dipinjam',
    FOREIGN KEY (id_buku) REFERENCES buku(id_buku)
) ENGINE=InnoDB;

-- Data contoh (sesuai gambar pada soal)
INSERT INTO buku (id_buku, judul, penulis, penerbit, tahun_terbit, stok, status) VALUES
('BK001', 'Pemrograman Web Dasar', 'Andi Setiawan', 'Informatika Press', 2021, 5, 'Tersedia'),
('BK002', 'Struktur Data dan Algoritma', 'Budi Santoso', 'Elex Media Komputindo', 2020, 3, 'Tersedia'),
('BK003', 'Basis Data', 'Siti Aminah', 'Andi Offset', 2019, 0, 'Habis'),
('BK004', 'Jaringan Komputer', 'Rizky Pratama', 'Media Komputindo', 2022, 7, 'Tersedia'),
('BK005', 'Keamanan Web', 'Dewi Lestari', 'Penerbit IT', 2021, 2, 'Tersedia');
