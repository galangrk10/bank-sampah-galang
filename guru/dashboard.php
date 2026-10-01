<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sukses") { header("location:../login.php"); exit; }
if (!isset($_SESSION['role']) || $_SESSION['role'] != "guru") { header("location:../login.php"); exit; }
include "../koneksi.php";
$id_user = $_SESSION['id_user'];
$data = mysqli_fetch_array(mysqli_query(
    $koneksi,
    "SELECT * FROM users WHERE id_user='$id_user'"
));
$page = isset($_GET['page']) ? $_GET['page'] : '';
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Dashboard Guru - Bank Sampah</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="../style.css"></head><body>
<div class="app-shell"><aside class="sidebar" id="sidebar"><a class="brand" href="dashboard.php"><span class="brand-icon">♻</span> Bank Sampah</a><div class="sidebar-nav"><div class="sidebar-label">Menu</div><a class="nav-link <?= $page==''?'active':'' ?>" href="dashboard.php"><span class="nav-icon">⌂</span> Dashboard</a><a class="nav-link <?= $page=='index'?'active':'' ?>" href="dashboard.php?page=index"><span class="nav-icon">♻</span> Sampah</a><a class="nav-link <?= $page=='setor'?'active':'' ?>" href="dashboard.php?page=setor"><span class="nav-icon">↑</span> Setor Sampah</a><a class="nav-link <?= $page=='tarik'?'active':'' ?>" href="dashboard.php?page=tarik"><span class="nav-icon">↕</span> Tarik Saldo</a></div><div class="sidebar-bottom"><div class="user-mini"><div class="avatar">G</div><div><strong><?= htmlspecialchars($_SESSION['nama']); ?></strong><small>Guru</small></div></div><a class="nav-link mt-2" href="../logout.php"><span class="nav-icon">↪</span> Logout</a></div></aside>
<main class="main-area"><header class="topbar"><div class="d-flex align-items-center gap-2"><button class="mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('show')">☰</button><div class="topbar-title"><h1><?= $page==''?'Dashboard Overview':'Bank Sampah'; ?></h1><p><?= $page==''?'Ringkasan saldo dan aktivitas akunmu.':'Kelola aktivitas sampah dan saldo'; ?></p></div></div></header><section class="content-area">
<?php if($page==''){ ?><div class="dashboard-heading"><div><h2>Dashboard Guru</h2><p>Selamat datang, <b><?= htmlspecialchars($data['nama']); ?></b>.</p></div></div><div class="row g-3"><div class="col-lg-6"><div class="stat-card"><div class="stat-top"><span class="stat-label">Saldo Anda</span><span class="stat-icon">Rp</span></div><div class="stat-value">Rp <?= number_format($data['saldo'],0,',','.'); ?></div><div class="stat-sub">Saldo yang dapat digunakan</div></div></div><div class="col-lg-6"><div class="stat-card"><div class="stat-top"><span class="stat-label">Aksi Cepat</span><span class="stat-icon">↑</span></div><div class="mt-3"><a href="dashboard.php?page=setor" class="btn btn-primary me-2">Setor Sampah</a><a href="dashboard.php?page=tarik" class="btn btn-outline-primary">Tarik Saldo</a></div></div></div></div>
<?php } elseif($page=='index'){ include 'index.php'; } elseif($page=='setor'){ include 'setor.php'; } elseif($page=='tarik'){ include 'tarik.php'; } else { ?><div class="alert alert-danger">Halaman tidak ditemukan.</div><?php } ?>
</section></main></div><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script></body></html>
