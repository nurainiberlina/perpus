<?php

require_once __DIR__ . '/../bootstrap.php';

$action = $_GET['action'] ?? 'list';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'store') {
        $bukuService->tambah($_POST);
        $_SESSION['flash_success'] = "Buku berhasil ditambahkan.";
        header('Location: buku.php');   // <-- REDIRECT setelah POST
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
        $bukuService->ubah($_POST['id_buku'], $_POST);
        $_SESSION['flash_success'] = "Buku berhasil diubah.";
        header('Location: buku.php');   // <-- REDIRECT setelah POST
        exit;
    }

    if ($action === 'delete' && isset($_GET['id'])) {
        $bukuService->hapus($_GET['id']);
        $_SESSION['flash_success'] = "Buku berhasil dihapus.";
        header('Location: buku.php');
        exit;
    }
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
    header('Location: buku.php');
    exit;
}

$daftarBuku = $bukuService->getAll();

$bukuEdit = null;
if ($action === 'edit' && isset($_GET['id'])) {
    try {
        $bukuEdit = $bukuService->getById($_GET['id']);
    } catch (Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
    }
}

require __DIR__ . '/../views/buku_view.php';
