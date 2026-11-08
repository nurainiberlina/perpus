<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peminjaman Buku - Perpustakaan Kampus</title>
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
        <div class="card-header">Form Peminjaman</div>
        <div class="card-body">
            <!-- POST ke action=pinjam, lalu controller redirect kembali ke sini (pola PRG) -->
            <form method="POST" action="peminjaman.php?action=pinjam" class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">NIM Mahasiswa</label>
                    <input type="text" name="nim" class="form-control" placeholder="Contoh: 20241001" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Pilih Buku</label>
                    <select name="id_buku" class="form-select" required>
                        <option value="">-- Pilih Buku --</option>
                        <?php foreach ($daftarBukuTersedia as $b): ?>
                            <option value="<?= htmlspecialchars($b['id_buku']) ?>"
                                <?= $b['stok'] <= 0 ? 'disabled' : '' ?>>
                                <?= htmlspecialchars($b['id_buku'] . ' - ' . $b['judul'] . ' (stok: ' . $b['stok'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Tanggal Pinjam</label>
                    <input type="date" name="tanggal_pinjam" class="form-control"
                           value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Tanggal Kembali</label>
                    <input type="date" name="tanggal_kembali" class="form-control"
                           value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Pinjam</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Daftar Peminjaman</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                <tr>
                    <th>ID Peminjaman</th>
                    <th>NIM</th>
                    <th>Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Jatuh Tempo</th>
                    <th>Tgl Kembali Aktual</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($daftarPeminjaman as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['id_peminjaman']) ?></td>
                        <td><?= htmlspecialchars($p['nim']) ?></td>
                        <td><?= htmlspecialchars($p['judul']) ?></td>
                        <td><?= htmlspecialchars($p['tanggal_pinjam']) ?></td>
                        <td><?= htmlspecialchars($p['tanggal_kembali']) ?></td>
                        <td><?= htmlspecialchars($p['tanggal_kembali_aktual'] ?? '-') ?></td>
                        <td>
                            <span class="badge <?= $p['status_peminjaman'] === 'Dipinjam' ? 'bg-warning text-dark' : 'bg-success' ?>">
                                <?= htmlspecialchars($p['status_peminjaman']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($p['status_peminjaman'] === 'Dipinjam'): ?>
                                <!-- POST, bukan link biasa, supaya perubahan status hanya lewat form -->
                                <form method="POST" action="peminjaman.php?action=kembalikan" class="d-inline">
                                    <input type="hidden" name="id_peminjaman" value="<?= htmlspecialchars($p['id_peminjaman']) ?>">
                                    <button type="submit" class="btn btn-success btn-sm"
                                            onclick="return confirm('Tandai buku ini sudah dikembalikan?');">
                                        Kembalikan
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($daftarPeminjaman)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-3">Belum ada data peminjaman.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
