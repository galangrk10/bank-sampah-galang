<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'sukses') { header('Location: ../login.php'); exit; }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengepul') { header('Location: ../login.php'); exit; }
include '../koneksi.php';
$id_pengepul = (int) $_SESSION['id_user'];
$sql = "SELECT s.id_sampah, s.jenis_sampah, s.harga_per_kg,
               COALESCE(SUM(t.berat_kg), 0) AS stok_dibeli,
               COUNT(t.id_transaksi) AS jumlah_transaksi
        FROM sampah s
        LEFT JOIN transaksi t ON t.id_sampah = s.id_sampah
             AND t.id_pengepul = $id_pengepul
             AND t.tipe = 'jual_pengepul'
             AND t.status = 'disetujui'
        GROUP BY s.id_sampah, s.jenis_sampah, s.harga_per_kg
        HAVING stok_dibeli > 0
        ORDER BY s.jenis_sampah ASC";
$query = mysqli_query($koneksi, $sql);
if (!$query) die('Gagal mengambil stok pengepul: ' . htmlspecialchars(mysqli_error($koneksi)));
$total_kg = 0;
$rows = [];
while ($row = mysqli_fetch_assoc($query)) { $rows[] = $row; $total_kg += (float)$row['stok_dibeli']; }
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Data Sampah Pengepul</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="../style.css"></head><body>
<div class="app-shell"><aside class="sidebar"><a class="brand" href="dashboard.php"><span class="brand-icon">♻</span> Bank Sampah</a><div class="sidebar-nav"><div class="sidebar-label">Menu</div><a class="nav-link" href="dashboard.php"><span class="nav-icon">⌂</span> Dashboard</a><a class="nav-link" href="index.php"><span class="nav-icon">▣</span> Pembelian</a><a class="nav-link" href="data_sampah.php"><span class="nav-icon">♻</span> Data Sampah</a><a class="nav-link" href="riwayat.php"><span class="nav-icon">▤</span> Riwayat</a><a class="nav-link" href="jenis_sampah.php"><span class="nav-icon">＋</span> Jenis Sampah</a></div><div class="sidebar-bottom"><div class="user-mini"><div class="avatar">P</div><div><strong><?= htmlspecialchars($_SESSION['nama']); ?></strong><small>Pengepul</small></div></div><a class="nav-link mt-2" href="../logout.php"><span class="nav-icon">↪</span> Logout</a></div></aside>
<main class="main-area"><header class="topbar"><div class="topbar-title"><h1>Data Sampah</h1><p>Stok sampah dari pembelian admin yang sudah diterima.</p></div></header><section class="content-area"><div class="row g-3 mb-3"><div class="col-md-6"><div class="stat-card"><div class="stat-top"><span class="stat-label">Total Stok Sampah</span><span class="stat-icon">♻</span></div><div class="stat-value"><?= number_format($total_kg, 2, ',', '.'); ?> Kg</div><div class="stat-sub">Hanya pembelian yang sudah diterima</div></div></div><div class="col-md-6"><div class="stat-card"><div class="stat-top"><span class="stat-label">Jenis Sampah Dimiliki</span><span class="stat-icon">▣</span></div><div class="stat-value"><?= count($rows); ?></div><div class="stat-sub">Jenis dengan stok pembelian di atas 0</div></div></div></div><div class="panel"><div class="panel-title"><div><h3>Stok Sampah Pengepul</h3><span>Stok bertambah otomatis saat pembelian berstatus Diterima.</span></div></div><div class="table-responsive"><table class="table"><thead><tr><th>No</th><th>Jenis Sampah</th><th>Jumlah Pembelian Diterima</th><th>Total Stok Dibeli</th><th>Harga/Kg di Katalog</th></tr></thead><tbody><?php if (count($rows)): foreach ($rows as $i => $row): ?><tr><td><?= $i + 1; ?></td><td><?= htmlspecialchars($row['jenis_sampah']); ?></td><td><?= (int)$row['jumlah_transaksi']; ?> kali</td><td><strong><?= number_format((float)$row['stok_dibeli'], 2, ',', '.'); ?> Kg</strong></td><td>Rp <?= number_format((float)$row['harga_per_kg'], 0, ',', '.'); ?></td></tr><?php endforeach; else: ?><tr><td colspan="5" class="text-center text-muted py-4">Belum ada stok. Terima pembelian dari admin terlebih dahulu.</td></tr><?php endif; ?></tbody></table></div></div></section></main></div></body></html>
