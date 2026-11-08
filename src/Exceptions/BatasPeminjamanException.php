<?php

/**
 * Dilempar ketika seorang mahasiswa (berdasarkan NIM) sudah
 * memiliki 3 buku yang statusnya masih "Dipinjam" dan mencoba
 * meminjam buku ke-4.
 */
class BatasPeminjamanException extends Exception
{
    public function __construct(string $message = "Mahasiswa telah mencapai batas maksimal peminjaman.")
    {
        parent::__construct($message);
    }
}
