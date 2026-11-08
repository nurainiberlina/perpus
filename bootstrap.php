<?php

/**
 * bootstrap.php
 * -------------------------------------------------------------
 * File ini dipanggil di awal setiap controller. Isinya:
 *  1. Menyalakan session (untuk pesan flash setelah redirect / PRG).
 *  2. Autoload sederhana: setiap kali sebuah class dipakai
 *     (misalnya BukuService), PHP otomatis mencari file class
 *     tersebut di folder-folder di bawah ini, tanpa kita perlu
 *     menulis require satu-satu.
 *  3. Membuat objek Repository & Service yang siap dipakai
 *     controller (dependency injection sederhana).
 */

session_start();

spl_autoload_register(function (string $class) {
    $folders = [
        __DIR__ . '/config/',
        __DIR__ . '/src/',
        __DIR__ . '/src/Exceptions/',
        __DIR__ . '/src/Repositories/',
        __DIR__ . '/src/Services/',
        __DIR__ . '/src/Validators/',
    ];

    foreach ($folders as $folder) {
        $file = $folder . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

$db = Database::getConnection();

$bukuRepo       = new BukuRepository($db);
$peminjamanRepo = new PeminjamanRepository($db);

$bukuService       = new BukuService($bukuRepo, $peminjamanRepo);
$peminjamanService = new PeminjamanService($db, $bukuRepo, $peminjamanRepo);

/**
 * Helper kecil untuk mengambil & langsung menghapus pesan flash
 * dari session (dipakai oleh view setelah proses PRG).
 */
function ambilFlash(string $key): ?string
{
    if (isset($_SESSION[$key])) {
        $pesan = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $pesan;
    }
    return null;
}
