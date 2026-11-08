<?php

/**
 * Logger
 * -------------------------------------------------------------
 * Kelas kecil untuk mencatat (logging) apa saja yang terjadi di
 * aplikasi ke sebuah file teks (logs/app.log). Ini memenuhi
 * syarat soal: "Setiap transaksi peminjaman dan pengembalian
 * harus tercatat pada log aplikasi."
 */
class Logger
{
    private static string $logFile = __DIR__ . '/../logs/app.log';

    public static function info(string $message): void
    {
        self::write('INFO', $message);
    }

    public static function error(string $message): void
    {
        self::write('ERROR', $message);
    }

    private static function write(string $level, string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $line = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
        file_put_contents(self::$logFile, $line, FILE_APPEND | LOCK_EX);
    }
}
