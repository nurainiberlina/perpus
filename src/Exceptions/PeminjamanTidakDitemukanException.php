<?php

/**
 * Dilempar ketika ID Peminjaman yang mau dikembalikan / dilihat
 * tidak ditemukan di tabel peminjaman.
 */
class PeminjamanTidakDitemukanException extends Exception
{
    public function __construct(string $message = "Transaksi peminjaman tidak ditemukan.")
    {
        parent::__construct($message);
    }
}
