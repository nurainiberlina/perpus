<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Buku - Perpustakaan Kampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand">📚 Perpustakaan Kampus</span>
        <div>
            <a href="buku.php" class="btn btn-outline-light btn-sm me-2">Data Buku</a>
            <a href="peminjaman.php" class="btn btn-outline-light btn-sm">Peminjaman</a>
        </div>
    </div>
</nav>

<div class="container">

    <?php if ($pesan = ambilFlash('flash_success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars($pesan) ?></div>
    <?php endif; ?>

    <?php if ($pesan = ambilFlash('flash_error')): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($pesan) ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <?= $bukuEdit ? 'Edit Buku: ' . htmlspecialchars($bukuEdit['id_buku']) : 'Tambah Buku Baru' ?>
        </div>
        <div class="card-body">
            <!--
                action="buku.php?action=store" atau "update" -> ini contoh nyata
                pola PRG: form mengirim POST ke sini, controller memproses lalu
                me-redirect balik ke buku.php (GET) sehingga form ini bersih lagi.
            -->
            <form method="POST" action="buku.php?action=<?= $bukuEdit ? 'update' : 'store' ?>" class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">ID Buku</label>
                    <input type="text" name="id_buku" class="form-control" required
                           value="<?= htmlspecialchars($bukuEdit['id_buku'] ?? '') ?>"
                           <?= $bukuEdit ? 'readonly' : '' ?>>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Judul</label>
                    <input type="text" name="judul" class="form-control" required
                           value="<?= htmlspecialchars($bukuEdit['judul'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Penulis</label>
                    <input type="text" name="penulis" class="form-control" required
                           value="<?= htmlspecialchars($bukuEdit['penulis'] ?? '') ?>">
                </div>

                <div class="col-md-5">
                    <label class="form-label">Penerbit</label>
                    <input type="text" name="penerbit" class="form-control" required
                           value="<?= htmlspecialchars($bukuEdit['penerbit'] ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="number" name="tahun_terbit" class="form-control" required
                           value="<?= htmlspecialchars($bukuEdit['tahun_terbit'] ?? date('Y')) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" min="0" class="form-control" required
                           value="<?= htmlspecialchars($bukuEdit['stok'] ?? 0) ?>">
                    <div class="form-text">Status "Tersedia"/"Habis" dihitung otomatis dari stok.</div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <?= $bukuEdit ? 'Simpan Perubahan' : 'Tambah Buku' ?>
                    </button>
                    <?php if ($bukuEdit): ?>
                        <a href="buku.php" class="btn btn-secondary">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Daftar Buku</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Penerbit</th>
                    <th>Tahun</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($daftarBuku as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['id_buku']) ?></td>
                        <td><?= htmlspecialchars($b['judul']) ?></td>
                        <td><?= htmlspecialchars($b['penulis']) ?></td>
                        <td><?= htmlspecialchars($b['penerbit']) ?></td>
                        <td><?= htmlspecialchars($b['tahun_terbit']) ?></td>
                        <td><?= htmlspecialchars($b['stok']) ?></td>
                        <td>
                            <span class="badge <?= $b['status'] === 'Tersedia' ? 'bg-success' : 'bg-danger' ?>">
                                <?= htmlspecialchars($b['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="buku.php?action=edit&id=<?= urlencode($b['id_buku']) ?>"
                               class="btn btn-warning btn-sm">Edit</a>
                            <a href="buku.php?action=delete&id=<?= urlencode($b['id_buku']) ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Yakin hapus buku ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($daftarBuku)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-3">Belum ada data buku.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
