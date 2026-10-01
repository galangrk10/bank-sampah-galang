<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sukses") { header("location:../login.php"); exit; }
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") { header("location:../login.php"); exit; }
include "../koneksi.php";

$query_user = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users");
$total_user = mysqli_fetch_assoc($query_user)['total'];
$query_stok = mysqli_query($koneksi, "SELECT COALESCE(SUM(stok_kg),0) AS total FROM sampah");
$total_stok = mysqli_fetch_assoc($query_stok)['total'];
$query_setor = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM transaksi WHERE tipe='setor' AND status='disetujui'");
$total_setor = mysqli_fetch_assoc($query_setor)['total'];
$query_saldo_admin = mysqli_query($koneksi, "SELECT COALESCE(SUM(saldo),0) AS total FROM users WHERE role='admin'");
$saldo_admin = mysqli_fetch_assoc($query_saldo_admin)['total'];

$chart_labels=[];
$chart_values=[];
$q_chart = mysqli_query($koneksi, "
    SELECT DATE_FORMAT(tanggal,'%b %Y') AS bulan, COUNT(*) AS jumlah
    FROM transaksi
    WHERE tipe='setor' AND status='disetujui'
    GROUP BY YEAR(tanggal), MONTH(tanggal)
    ORDER BY YEAR(tanggal) DESC, MONTH(tanggal) DESC
    LIMIT 6
");
while($r=mysqli_fetch_assoc($q_chart)){
    array_unshift($chart_labels,$r['bulan']);
    array_unshift($chart_values,(int)$r['jumlah']);
}
if(!$chart_labels){
    $chart_labels=['Belum ada'];
    $chart_values=[0];
}

$q_top = mysqli_query($koneksi,"SELECT jenis_sampah, stok_kg FROM sampah ORDER BY stok_kg DESC LIMIT 5");
$top_sampah=[];
while($r=mysqli_fetch_assoc($q_top)){
    $top_sampah[]=$r;
}
$page = isset($_GET['page']) ? $_GET['page'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard Admin - Bank Sampah</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="app-shell">
<aside class="sidebar" id="sidebar">
    <a class="brand" href="dashboard.php"><span class="brand-icon">♻</span> Bank Sampah</a>
    <div class="sidebar-nav">
        <div class="sidebar-label">Menu Utama</div>
        <a class="nav-link <?= $page==''?'active':'' ?>" href="dashboard.php"><span class="nav-icon">⌂</span> Dashboard</a>
        <a class="nav-link <?= $page=='user'?'active':'' ?>" href="dashboard.php?page=user"><span class="nav-icon">♟</span> Data User</a>
        <a class="nav-link <?= $page=='sampah'?'active':'' ?>" href="dashboard.php?page=sampah"><span class="nav-icon">♻</span> Data Sampah</a>
        <a class="nav-link <?= $page=='transaksi'?'active':'' ?>" href="dashboard.php?page=transaksi"><span class="nav-icon">▤</span> Riwayat Transaksi</a>
        <a class="nav-link <?= $page=='penjualan'?'active':'' ?>" href="dashboard.php?page=penjualan"><span class="nav-icon">▣</span> Jual ke Pengepul</a>
        <a class="nav-link <?= $page=='tarik'?'active':'' ?>" href="dashboard.php?page=tarik"><span class="nav-icon">↕</span> Tarik Saldo</a>
    </div>
    <div class="sidebar-bottom">
        <div class="user-mini"><div class="avatar">A</div><div><strong><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin'); ?></strong><small>Administrator</small></div></div>
        <a class="nav-link mt-2" href="../logout.php"><span class="nav-icon">↪</span> Logout</a>
    </div>
</aside>
<main class="main-area">
<header class="topbar">
    <div class="d-flex align-items-center gap-2">
        <button class="mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('show')">☰</button>
        <div class="topbar-title"><h1><?= $page==''?'Dashboard Overview':'Bank Sampah'; ?></h1><p><?= $page==''?'Selamat datang kembali, berikut ringkasan sistem hari ini.':'Kelola data Bank Sampah'; ?></p></div>
    </div>
    <div class="search-box"><span>⌕</span><input type="text" placeholder="Search..."></div>
</header>
<section class="content-area">
<?php if ($page == '') { ?>
<div class="dashboard-heading"><div><h2>Dashboard Overview</h2><p>Ringkasan aktivitas Bank Sampah.</p></div></div>
<div class="row g-3">
    <div class="col-xl-3 col-md-6"><div class="stat-card"><div class="stat-top"><span class="stat-label">Total User</span><span class="stat-icon">♟</span></div><div class="stat-value"><?= $total_user; ?></div><div class="stat-sub">Semua pengguna</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="stat-card"><div class="stat-top"><span class="stat-label">Total Stok Sampah</span><span class="stat-icon">♻</span></div><div class="stat-value"><?= $total_stok; ?> Kg</div><div class="stat-sub">Sampah tersedia</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="stat-card"><div class="stat-top"><span class="stat-label">Total Setoran</span><span class="stat-icon">▤</span></div><div class="stat-value"><?= $total_setor; ?></div><div class="stat-sub">Setoran disetujui</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="stat-card"><div class="stat-top"><span class="stat-label">Saldo Admin</span><span class="stat-icon">Rp</span></div><div class="stat-value">Rp <?= number_format($saldo_admin,0,',','.'); ?></div><div class="stat-sub">Saldo seluruh admin</div></div></div>
</div>
<div class="analytics-grid">
    <div class="panel"><div class="panel-title"><div><h3>Analitik Setoran</h3><span>Jumlah setoran disetujui per bulan</span></div><span>6 bulan</span></div><div class="chart-wrap"><canvas id="setoranChart"></canvas></div></div>
    <div class="panel"><div class="panel-title"><div><h3>Stok Sampah Teratas</h3><span>Jenis dengan stok terbesar</span></div></div>
        <div class="top-list">
        <?php if($top_sampah){ foreach($top_sampah as $i=>$s){ ?>
            <div class="top-item"><div class="top-thumb">♻</div><div><strong><?= htmlspecialchars($s['jenis_sampah']); ?></strong><small>Stok tersedia</small></div><div class="ms-auto"><?= $s['stok_kg']; ?> Kg</div></div>
        <?php }} else { ?><p class="text-muted mb-0">Belum ada data sampah.</p><?php } ?>
        </div>
    </div>
</div>
<?php } elseif ($page == 'user') { include "user/index.php"; }
elseif ($page == 'sampah') { include "sampah/index.php"; }
elseif ($page == 'transaksi') { include "transaksi/index.php"; }
elseif ($page == 'penjualan') { include "penjualan/index.php"; }
elseif ($page == 'tarik') { include "tarik.php"; }
else { ?><div class="alert alert-danger">Halaman tidak ditemukan.</div><?php } ?>
</section>
</main></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<?php if($page==''){ ?><script src="https://cdn.jsdelivr.net/npm/chart.js"></script><script>
new Chart(document.getElementById('setoranChart'),{type:'line',data:{labels:<?= json_encode($chart_labels); ?>,datasets:[{label:'Setoran',data:<?= json_encode($chart_values); ?>,borderWidth:2,tension:.35,fill:false}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}},x:{grid:{display:false}}}}});
</script><?php } ?>
</body></html>
