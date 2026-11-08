<?php

/**
 * PeminjamanService
 * -------------------------------------------------------------
 * Jantung dari seluruh aturan bisnis soal ini. Semua pengecekan
 * (stok, batas maksimal pinjam, dsb.) ditaruh di sini, BUKAN di
 * controller ataupun di halaman form.
 *
 * Aturan bisnis yang ditangani:
 *  1. Buku hanya bisa dipinjam kalau stok > 0.
 *  2. Satu mahasiswa (NIM) maksimal 3 buku yang statusnya "Dipinjam".
 *  3. Setelah pinjam berhasil, stok buku otomatis berkurang 1.
 *  4. Setelah buku dikembalikan, stok otomatis bertambah 1.
 */
class PeminjamanService
{
    private const MAKS_PINJAM_PER_MAHASISWA = 3;

    private PDO $db;
    private BukuRepository $bukuRepo;
    private PeminjamanRepository $peminjamanRepo;

    public function __construct(PDO $db, BukuRepository $bukuRepo, PeminjamanRepository $peminjamanRepo)
    {
        $this->db = $db;
        $this->bukuRepo = $bukuRepo;
        $this->peminjamanRepo = $peminjamanRepo;
    }

    public function getAll(): array
    {
        return $this->peminjamanRepo->findAll();
    }

    /**
     * Memproses peminjaman buku baru.
     * Urutan: validasi input -> cek aturan bisnis -> simpan (dalam transaksi DB).
     */
    public function pinjam(array $data): void
    {
        $nim            = trim($data['nim'] ?? '');
        $idBuku         = trim($data['id_buku'] ?? '');
        $tanggalPinjam  = trim($data['tanggal_pinjam'] ?? '');
        $tanggalKembali = trim($data['tanggal_kembali'] ?? '');

        // --- VALIDASI INPUT ---
        Validator::validateNim($nim);
        Validator::validateIdBuku($idBuku);
        Validator::validateTanggal($tanggalPinjam, 'Tanggal pinjam');
        Validator::validateTanggal($tanggalKembali, 'Tanggal kembali');
        Validator::validateTanggalKembaliSetelahPinjam($tanggalPinjam, $tanggalKembali);

        // --- CEK KETERSEDIAAN BUKU ---
        $buku = $this->bukuRepo->findById($idBuku);
        if ($buku === null) {
            throw new BukuTidakDitemukanException();
        }

        // ATURAN BISNIS #1 & #5: stok harus > 0
        if ((int) $buku['stok'] <= 0) {
            throw new BukuTidakTersediaException();
        }

        // ATURAN BISNIS #2: maksimal 3 buku aktif per mahasiswa
        if ($this->peminjamanRepo->countAktifByNim($nim) >= self::MAKS_PINJAM_PER_MAHASISWA) {
            throw new BatasPeminjamanException();
        }

        // --- SIMPAN DALAM TRANSAKSI ---
        // Transaksi DB memastikan "insert peminjaman" dan "kurangi stok"
        // selalu terjadi berdua, atau gagal berdua (tidak ada data setengah jalan).
        $this->db->beginTransaction();
        try {
            $idBaru = $this->peminjamanRepo->generateNextId();

            $this->peminjamanRepo->insert([
                'id_peminjaman'   => $idBaru,
                'nim'             => $nim,
                'id_buku'         => $idBuku,
                'tanggal_pinjam'  => $tanggalPinjam,
                'tanggal_kembali' => $tanggalKembali,
            ]);

            // ATURAN BISNIS #3: stok berkurang 1
            $this->bukuRepo->updateStok($idBuku, -1);

            $this->db->commit();

            Logger::info("PEMINJAMAN berhasil: {$idBaru} | NIM={$nim} | Buku={$idBuku} | Jatuh tempo={$tanggalKembali}");
        } catch (Exception $e) {
            $this->db->rollBack();
            Logger::error("PEMINJAMAN gagal: NIM={$nim} | Buku={$idBuku} | Sebab: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Memproses pengembalian buku berdasarkan ID Peminjaman.
     */
    public function kembalikan(string $idPeminjaman): void
    {
        $pinjaman = $this->peminjamanRepo->findById($idPeminjaman);
        if ($pinjaman === null) {
            throw new PeminjamanTidakDitemukanException();
        }

        if ($pinjaman['status_peminjaman'] === 'Kembali') {
            throw new RuntimeException("Transaksi ini sudah dikembalikan sebelumnya.");
        }

        $this->db->beginTransaction();
        try {
            $this->peminjamanRepo->markKembali($idPeminjaman, date('Y-m-d'));

            // ATURAN BISNIS #4: stok bertambah 1
            $this->bukuRepo->updateStok($pinjaman['id_buku'], +1);

            $this->db->commit();

            Logger::info("PENGEMBALIAN berhasil: {$idPeminjaman} | NIM={$pinjaman['nim']} | Buku={$pinjaman['id_buku']}");
        } catch (Exception $e) {
            $this->db->rollBack();
            Logger::error("PENGEMBALIAN gagal: {$idPeminjaman} | Sebab: " . $e->getMessage());
            throw $e;
        }
    }
}
