<?php
if(session_status()===PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['status']) || $_SESSION['status']!="sukses"){header("location:../login.php");exit;}
if(!isset($_SESSION['role']) || $_SESSION['role']!="pengepul"){header("location:../login.php");exit;}
include "../koneksi.php";
$id_pengepul=$_SESSION['id_user'];
$q1=mysqli_query($koneksi,"SELECT COUNT(*) AS total FROM transaksi WHERE id_pengepul='$id_pengepul' AND tipe='jual_pengepul' AND status='disetujui'");
$data_transaksi=mysqli_fetch_assoc($q1);
$q2=mysqli_query($koneksi,"SELECT COALESCE(SUM(berat_kg),0) AS total FROM transaksi WHERE id_pengepul='$id_pengepul' AND tipe='jual_pengepul' AND status='disetujui'");
$data_kg=mysqli_fetch_assoc($q2);
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Dashboard Pengepul - Bank Sampah</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="../style.css"></head><body>
<div class="app-shell"><aside class="sidebar"><a class="brand" href="dashboard.php"><span class="brand-icon">♻</span> Bank Sampah</a><div class="sidebar-nav"><div class="sidebar-label">Menu</div><a class="nav-link active" href="dashboard.php"><span class="nav-icon">⌂</span> Dashboard</a><a class="nav-link" href="index.php"><span class="nav-icon">▣</span> Pembelian</a><a class="nav-link" href="riwayat.php"><span class="nav-icon">▤</span> Riwayat</a></div><div class="sidebar-bottom"><div class="user-mini"><div class="avatar">P</div><div><strong><?= htmlspecialchars($_SESSION['nama']); ?></strong><small>Pengepul</small></div></div><a class="nav-link mt-2" href="../logout.php"><span class="nav-icon">↪</span> Logout</a></div></aside>
<main class="main-area"><header class="topbar"><div class="topbar-title"><h1>Dashboard Overview</h1><p>Ringkasan pembelian sampah yang sudah diterima.</p></div></header><section class="content-area"><div class="dashboard-heading"><div><h2>Dashboard Pengepul</h2><p>Selamat datang, <b><?= htmlspecialchars($_SESSION['nama']); ?></b>.</p></div></div><div class="row g-3"><div class="col-lg-6"><div class="stat-card"><div class="stat-top"><span class="stat-label">Total Pembelian</span><span class="stat-icon">▣</span></div><div class="stat-value"><?= $data_transaksi['total']; ?></div><div class="stat-sub">Transaksi disetujui</div></div></div><div class="col-lg-6"><div class="stat-card"><div class="stat-top"><span class="stat-label">Total Sampah Dibeli</span><span class="stat-icon">♻</span></div><div class="stat-value"><?= $data_kg['total']; ?> Kg</div><div class="stat-sub">Berat sampah yang diterima</div></div></div></div></section></main></div></body></html>
