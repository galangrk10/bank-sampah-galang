<?php

session_start();

if (!isset($_SESSION['status']) || $_SESSION['status'] != "sukses") {
    header("location:../../login.php");
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("location:../../login.php");
    exit;
}

include "../../koneksi.php";

$id_transaksi = $_GET['id'] ?? '';

if ($id_transaksi == '') {
    header("Location: ../dashboard.php?page=transaksi");
    exit;
}

$query = mysqli_query($koneksi, "
    UPDATE transaksi
    SET status='ditolak'
    WHERE id_transaksi='$id_transaksi'
    AND tipe='setor'
    AND status='pending'
");

if (!$query) {
    die("Gagal menolak setoran: " . mysqli_error($koneksi));
}

header("Location: ../dashboard.php?page=transaksi");
exit;

?>
