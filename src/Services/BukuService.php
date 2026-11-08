<?php

/**
 * BukuService
 * -------------------------------------------------------------
 * Di sinilah SEMUA aturan bisnis seputar data buku ditempatkan
 * (bukan di Controller). Controller hanya memanggil method di
 * kelas ini.
 *
 * Aturan bisnis yang ditangani:
 *  - Buku yang sedang dipinjam tidak boleh dihapus.
 *  - Status buku ('Tersedia'/'Habis') selalu mengikuti nilai stok.
 */
class BukuService
{
    private BukuRepository $bukuRepo;
    private PeminjamanRepository $peminjamanRepo;

    public function __construct(BukuRepository $bukuRepo, PeminjamanRepository $peminjamanRepo)
    {
        $this->bukuRepo = $bukuRepo;
        $this->peminjamanRepo = $peminjamanRepo;
    }

    public function getAll(): array
    {
        return $this->bukuRepo->findAll();
    }

    /** Ambil satu buku, lempar exception kalau tidak ada */
    public function getById(string $id): array
    {
        $buku = $this->bukuRepo->findById($id);
        if ($buku === null) {
            throw new BukuTidakDitemukanException();
        }
        return $buku;
    }

    public function tambah(array $data): void
    {
        $idBuku = trim($data['id_buku'] ?? '');
        Validator::validateIdBuku($idBuku);
        Validator::validateTahunTerbit($data['tahun_terbit'] ?? '');
        Validator::validateStok($data['stok'] ?? '');

        if ($this->bukuRepo->findById($idBuku) !== null) {
            throw new InvalidArgumentException("ID Buku '{$idBuku}' sudah digunakan.");
        }

        $stok = (int) $data['stok'];

        $this->bukuRepo->insert([
            'id_buku'      => $idBuku,
            'judul'        => trim($data['judul']),
            'penulis'      => trim($data['penulis']),
            'penerbit'     => trim($data['penerbit']),
            'tahun_terbit' => (int) $data['tahun_terbit'],
            'stok'         => $stok,
            // status otomatis mengikuti stok, admin tidak perlu memilih manual
            'status'       => $stok > 0 ? 'Tersedia' : 'Habis',
        ]);

        Logger::info("Buku baru ditambahkan: {$idBuku} - {$data['judul']} (stok awal: {$stok})");
    }

    public function ubah(string $id, array $data): void
    {
        // pastikan datanya memang ada, kalau tidak -> BukuTidakDitemukanException
        $this->getById($id);

        Validator::validateTahunTerbit($data['tahun_terbit'] ?? '');
        Validator::validateStok($data['stok'] ?? '');

        $stok = (int) $data['stok'];

        $this->bukuRepo->update($id, [
            'judul'        => trim($data['judul']),
            'penulis'      => trim($data['penulis']),
            'penerbit'     => trim($data['penerbit']),
            'tahun_terbit' => (int) $data['tahun_terbit'],
            'stok'         => $stok,
            'status'       => $stok > 0 ? 'Tersedia' : 'Habis',
        ]);

        Logger::info("Buku diubah: {$id}");
    }

    public function hapus(string $id): void
    {
        // pastikan datanya ada
        $this->getById($id);

        // ATURAN BISNIS #6: buku yang sedang dipinjam tidak boleh dihapus
        if ($this->peminjamanRepo->isBukuSedangDipinjam($id)) {
            throw new RuntimeException("Buku sedang dipinjam dan tidak boleh dihapus.");
        }

        $this->bukuRepo->delete($id);
        Logger::info("Buku dihapus: {$id}");
    }
}
