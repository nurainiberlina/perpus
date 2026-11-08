<?php

/**
 * Dilempar ketika mahasiswa mencoba meminjam buku yang stoknya 0
 * (atau buku dengan status "Habis").
 */
class BukuTidakTersediaException extends Exception
{
    public function __construct(string $message = "Buku tidak tersedia.")
    {
        parent::__construct($message);
    }
}
