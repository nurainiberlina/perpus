<?php

/**
 * Dilempar ketika ID Buku yang diminta (untuk dilihat, diedit,
 * dihapus, atau dipinjam) tidak ada di tabel buku.
 */
class BukuTidakDitemukanException extends Exception
{
    public function __construct(string $message = "Data buku tidak ditemukan.")
    {
        parent::__construct($message);
    }
}
