<?php

/**
 * BukuRepository
 * -------------------------------------------------------------
 * Tugasnya HANYA bicara dengan tabel `buku` di database (query
 * SQL mentah). Tidak ada aturan bisnis di sini — aturan bisnis
 * ada di BukuService. Pemisahan ini membuat kode lebih rapi dan
 * gampang diuji/diganti.
 */
class BukuRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** Ambil semua buku */
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM buku ORDER BY id_buku ASC");
        return $stmt->fetchAll();
    }

    /** Cari satu buku berdasarkan ID, null jika tidak ada */
    public function findById(string $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM buku WHERE id_buku = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function insert(array $data): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO buku (id_buku, judul, penulis, penerbit, tahun_terbit, stok, status)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['id_buku'],
            $data['judul'],
            $data['penulis'],
            $data['penerbit'],
            $data['tahun_terbit'],
            $data['stok'],
            $data['status'],
        ]);
    }

    public function update(string $id, array $data): void
    {
        $stmt = $this->db->prepare(
            "UPDATE buku
             SET judul = ?, penulis = ?, penerbit = ?, tahun_terbit = ?, stok = ?, status = ?
             WHERE id_buku = ?"
        );
        $stmt->execute([
            $data['judul'],
            $data['penulis'],
            $data['penerbit'],
            $data['tahun_terbit'],
            $data['stok'],
            $data['status'],
            $id,
        ]);
    }

    public function delete(string $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM buku WHERE id_buku = ?");
        $stmt->execute([$id]);
    }

    /**
     * Menambah/mengurangi stok sekaligus otomatis memperbarui status
     * ('Tersedia' / 'Habis') supaya keduanya selalu konsisten.
     * $delta: -1 saat dipinjam, +1 saat dikembalikan.
     */
    public function updateStok(string $id, int $delta): void
    {
        $stmt = $this->db->prepare("UPDATE buku SET stok = stok + ? WHERE id_buku = ?");
        $stmt->execute([$delta, $id]);

        $stmt = $this->db->prepare(
            "UPDATE buku SET status = CASE WHEN stok > 0 THEN 'Tersedia' ELSE 'Habis' END WHERE id_buku = ?"
        );
        $stmt->execute([$id]);
    }
}
