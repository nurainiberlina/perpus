<?php

/**
 * Controller: Peminjaman & Pengembalian Buku
 * -------------------------------------------------------------
 * Sama seperti buku.php: controller ini tipis, hanya meneruskan
 * data form ke PeminjamanService dan menerapkan pola PRG supaya
 * form tidak terkirim dua kali saat halaman di-refresh.
 *
 * Catatan: sesuai soal, "Data peminjaman tidak boleh dihapus
 * sembarangan" -- karena itu di halaman ini TIDAK ada tombol/aksi
 * hapus peminjaman sama sekali. Yang ada hanya aksi "Kembalikan".
 */

require_once __DIR__ . '/../bootstrap.php';

$action = $_GET['action'] ?? 'list';

try {
    // --- PROSES PEMINJAMAN BARU (POST) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'pinjam') {
        $peminjamanService->pinjam($_POST);
        $_SESSION['flash_success'] = "Peminjaman berhasil dicatat.";
        header('Location: peminjaman.php');   // <-- REDIRECT setelah POST
        exit;
    }

    // --- PROSES PENGEMBALIAN (POST) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'kembalikan') {
        $peminjamanService->kembalikan($_POST['id_peminjaman']);
        $_SESSION['flash_success'] = "Pengembalian berhasil dicatat.";
        header('Location: peminjaman.php');   // <-- REDIRECT setelah POST
        exit;
    }
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
    header('Location: peminjaman.php');
    exit;
}

// --- GET: tampilkan halaman ---
$daftarPeminjaman   = $peminjamanService->getAll();
$daftarBukuTersedia = $bukuService->getAll();

require __DIR__ . '/../views/peminjaman_view.php';
