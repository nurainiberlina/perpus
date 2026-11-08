<?php

/**
 * Validator
 * -------------------------------------------------------------
 * Kumpulan fungsi validasi input. Dipanggil oleh Service SEBELUM
 * data diproses/disimpan, supaya data kotor tidak pernah sampai
 * ke database.
 * Kalau tidak valid, langsung lempar InvalidArgumentException
 * berisi pesan yang jelas untuk ditampilkan ke pengguna.
 */
class Validator
{
    public static function validateNim(string $nim): void
    {
        if (trim($nim) === '') {
            throw new InvalidArgumentException("NIM wajib diisi.");
        }
        if (!preg_match('/^[0-9]{8,15}$/', $nim)) {
            throw new InvalidArgumentException("Format NIM tidak valid (harus angka, 8-15 digit).");
        }
    }

    public static function validateIdBuku(string $idBuku): void
    {
        if (trim($idBuku) === '') {
            throw new InvalidArgumentException("ID Buku wajib diisi.");
        }
    }

    public static function validateTanggal(string $tanggal, string $label): void
    {
        if (trim($tanggal) === '') {
            throw new InvalidArgumentException("{$label} wajib diisi.");
        }
        $d = DateTime::createFromFormat('Y-m-d', $tanggal);
        if (!$d || $d->format('Y-m-d') !== $tanggal) {
            throw new InvalidArgumentException("{$label} tidak valid, gunakan format tanggal yang benar.");
        }
    }

    public static function validateTanggalKembaliSetelahPinjam(string $tglPinjam, string $tglKembali): void
    {
        if ($tglKembali < $tglPinjam) {
            throw new InvalidArgumentException("Tanggal kembali tidak boleh sebelum tanggal pinjam.");
        }
    }

    public static function validateTahunTerbit($tahun): void
    {
        $tahunSekarang = (int) date('Y');
        if (!is_numeric($tahun) || $tahun < 1900 || $tahun > $tahunSekarang) {
            throw new InvalidArgumentException("Tahun terbit tidak valid.");
        }
    }

    public static function validateStok($stok): void
    {
        if (!is_numeric($stok) || (int) $stok < 0) {
            throw new InvalidArgumentException("Stok tidak boleh negatif.");
        }
    }
}
