<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (($_SESSION['status'] ?? '') !== 'sukses' || ($_SESSION['role'] ?? '') !== 'pengepul') {
    header('Location: ../login.php');
    exit;
}
require_once '../koneksi.php';
$query = mysqli_query($koneksi, 'SELECT id_sampah, jenis_sampah, harga_per_kg FROM sampah ORDER BY jenis_sampah ASC');
if (!$query) die('Gagal mengambil data: ' . mysqli_error($koneksi));
$pesan = $_SESSION['pesan_jenis_sampah'] ?? '';
unset($_SESSION['pesan_jenis_sampah']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jenis Sampah - Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <a class="brand" href="dashboard.php"><span class="brand-icon">♻</span> Bank Sampah</a>
        <div class="sidebar-nav">
            <div class="sidebar-label">Menu</div>
            <a class="nav-link" href="dashboard.php"><span class="nav-icon">⌂</span> Dashboard</a>
            <a class="nav-link" href="index.php"><span class="nav-icon">▣</span> Pembelian</a><a class="nav-link" href="data_sampah.php"><span class="nav-icon">♻</span> Data Sampah</a>
            <a class="nav-link" href="riwayat.php"><span class="nav-icon">▤</span> Riwayat</a>
            <a class="nav-link active" href="jenis_sampah.php"><span class="nav-icon">＋</span> Jenis Sampah</a>
        </div>
        <div class="sidebar-bottom">
            <div class="user-mini"><div class="avatar">P</div><div><strong><?= htmlspecialchars($_SESSION['nama'] ?? 'Pengepul'); ?></strong><small>Pengepul</small></div></div>
            <a class="nav-link mt-2" href="../logout.php"><span class="nav-icon">↪</span> Logout</a>
        </div>
    </aside>
    <main class="main-area">
        <header class="topbar"><div class="topbar-title"><h1>Jenis Sampah</h1><p>Tambahkan jenis sampah yang tersedia untuk pencatatan.</p></div></header>
        <section class="content-area">
            <?php if ($pesan !== ''): ?><div class="alert alert-info alert-dismissible fade show" role="alert"><?= htmlspecialchars($pesan); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button></div><?php endif; ?>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="panel">
                        <div class="panel-title"><div><h3>Tambah Jenis Sampah</h3><span>Tambahkan jenis sampah beserta harga per kilogram.</span></div></div>
                        <form action="simpan_jenis_sampah.php" method="post">
                            <div class="mb-3"><label for="jenis_sampah" class="form-label">Nama Jenis Sampah</label><input type="text" class="form-control" id="jenis_sampah" name="jenis_sampah" maxlength="100" required placeholder="Contoh: Botol plastik"></div>
                            <div class="mb-3"><label for="harga_per_kg" class="form-label">Harga per Kg (Rp)</label><input type="number" class="form-control" id="harga_per_kg" name="harga_per_kg" min="0" step="1" required placeholder="Contoh: 2500"></div>
                            <button type="submit" class="btn btn-success">Simpan Jenis Sampah</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="panel">
                        <div class="panel-title"><div><h3>Daftar Jenis Sampah</h3><span>Data diambil dari katalog sampah Bank Sampah.</span></div></div>
                        <div class="table-responsive"><table class="table align-middle"><thead><tr><th>No</th><th>Jenis Sampah</th><th>Harga/Kg</th></tr></thead><tbody>
                        <?php $no = 1; if (mysqli_num_rows($query) > 0): while ($data = mysqli_fetch_assoc($query)): ?>
                            <tr><td><?= $no++; ?></td><td><?= htmlspecialchars($data['jenis_sampah']); ?></td><td>Rp <?= number_format((int)$data['harga_per_kg'], 0, ',', '.'); ?></td></tr>
                        <?php endwhile; else: ?><tr><td colspan="3" class="text-center text-muted py-4">Belum ada jenis sampah.</td></tr><?php endif; ?>
                        </tbody></table></div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
