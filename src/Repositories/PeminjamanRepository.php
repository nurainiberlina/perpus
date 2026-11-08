<?php

/**
 * PeminjamanRepository
 * -------------------------------------------------------------
 * Sama seperti BukuRepository tapi untuk tabel `peminjaman`.
 * Semua query mentah (SELECT/INSERT/UPDATE) tabel peminjaman
 * dikumpulkan di sini.
 */
class PeminjamanRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** Ambil semua data peminjaman, sekaligus judul bukunya (join) */
    public function findAll(): array
    {
        $stmt = $this->db->query(
            "SELECT p.*, b.judul, b.penulis
             FROM peminjaman p
             JOIN buku b ON p.id_buku = b.id_buku
             ORDER BY p.id_peminjaman DESC"
        );
        return $stmt->fetchAll();
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM peminjaman WHERE id_peminjaman = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Hitung berapa buku yang SEDANG dipinjam oleh satu NIM */
    public function countAktifByNim(string $nim): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM peminjaman WHERE nim = ? AND status_peminjaman = 'Dipinjam'"
        );
        $stmt->execute([$nim]);
        return (int) $stmt->fetchColumn();
    }

    /** Cek apakah sebuah buku sedang ada yang meminjam (untuk aturan larangan hapus buku) */
    public function isBukuSedangDipinjam(string $idBuku): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM peminjaman WHERE id_buku = ? AND status_peminjaman = 'Dipinjam'"
        );
        $stmt->execute([$idBuku]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /** Membuat ID peminjaman berurutan otomatis: PJ001, PJ002, dst. */
    public function generateNextId(): string
    {
        $stmt = $this->db->query("SELECT id_peminjaman FROM peminjaman ORDER BY id_peminjaman DESC LIMIT 1");
        $last = $stmt->fetchColumn();
        $nomor = $last ? ((int) substr($last, 2)) + 1 : 1;
        return 'PJ' . str_pad((string) $nomor, 3, '0', STR_PAD_LEFT);
    }

    public function insert(array $data): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO peminjaman (id_peminjaman, nim, id_buku, tanggal_pinjam, tanggal_kembali, status_peminjaman)
             VALUES (?, ?, ?, ?, ?, 'Dipinjam')"
        );
        $stmt->execute([
            $data['id_peminjaman'],
            $data['nim'],
            $data['id_buku'],
            $data['tanggal_pinjam'],
            $data['tanggal_kembali'],
        ]);
    }

    /** Menandai transaksi sebagai sudah dikembalikan */
    public function markKembali(string $id, string $tanggalKembaliAktual): void
    {
        $stmt = $this->db->prepare(
            "UPDATE peminjaman
             SET status_peminjaman = 'Kembali', tanggal_kembali_aktual = ?
             WHERE id_peminjaman = ?"
        );
        $stmt->execute([$tanggalKembaliAktual, $id]);
    }
}
