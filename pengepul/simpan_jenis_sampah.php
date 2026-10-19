<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (($_SESSION['status'] ?? '') !== 'sukses' || ($_SESSION['role'] ?? '') !== 'pengepul') {
    header('Location: ../login.php');
    exit;
}
require_once '../koneksi.php';

$jenis = trim($_POST['jenis_sampah'] ?? '');
$harga = filter_input(INPUT_POST, 'harga_per_kg', FILTER_VALIDATE_INT);

if ($jenis === '' || mb_strlen($jenis) > 100 || $harga === false || $harga === null || $harga < 0) {
    $_SESSION['pesan_jenis_sampah'] = 'Nama jenis sampah dan harga per Kg wajib diisi dengan benar.';
    header('Location: jenis_sampah.php');
    exit;
}

$cek = mysqli_prepare($koneksi, 'SELECT id_sampah FROM sampah WHERE LOWER(jenis_sampah) = LOWER(?) LIMIT 1');
mysqli_stmt_bind_param($cek, 's', $jenis);
mysqli_stmt_execute($cek);
$hasil = mysqli_stmt_get_result($cek);
if (mysqli_num_rows($hasil) > 0) {
    $_SESSION['pesan_jenis_sampah'] = 'Jenis sampah tersebut sudah ada.';
    header('Location: jenis_sampah.php');
    exit;
}

$simpan = mysqli_prepare($koneksi, 'INSERT INTO sampah (jenis_sampah, harga_per_kg, stok_kg) VALUES (?, ?, 0)');
mysqli_stmt_bind_param($simpan, 'si', $jenis, $harga);
if (mysqli_stmt_execute($simpan)) {
    $_SESSION['pesan_jenis_sampah'] = 'Jenis sampah berhasil ditambahkan. Stok awal 0 Kg.';
} else {
    $_SESSION['pesan_jenis_sampah'] = 'Gagal menambahkan jenis sampah: ' . mysqli_error($koneksi);
}
header('Location: jenis_sampah.php');
exit;
