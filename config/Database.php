<?php

/**
 * Database
 * -------------------------------------------------------------
 * Bertugas HANYA untuk membuka koneksi ke MySQL memakai PDO.
 * Dibuat "singleton" (getConnection selalu mengembalikan koneksi
 * yang sama) supaya seluruh aplikasi tidak membuka koneksi baru
 * berulang-ulang.
 */
class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $host   = '127.0.0.1';
            $dbname = 'perpustakaan';
            $user   = 'root';
            $pass   = '';

            $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

            self::$connection = new PDO($dsn, $user, $pass, [
                // Kalau ada error SQL, langsung lempar sebagai Exception
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Hasil query otomatis berbentuk array asosiatif
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return self::$connection;
    }
}
