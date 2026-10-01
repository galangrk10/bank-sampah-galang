<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['status']) || $_SESSION['status'] != "sukses") {
    header("location:../login.php");
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pengepul") {
    header("location:../login.php");
    exit;
}

include "../koneksi.php";

$id_pengepul = $_SESSION['id_user'];
$id_transaksi = $_GET['id'] ?? '';

if ($id_transaksi == '') {
    header("Location: index.php");
    exit;
}

mysqli_begin_transaction($koneksi);

try {

    // Ambil transaksi yang masih pending dan memang milik pengepul yang login.
    $query = mysqli_query($koneksi, "
        SELECT *
        FROM transaksi
        WHERE id_transaksi='$id_transaksi'
        AND id_pengepul='$id_pengepul'
        AND tipe='jual_pengepul'
        AND status='pending'
        FOR UPDATE
    ");

    if (!$query || mysqli_num_rows($query) == 0) {
        throw new Exception("Transaksi tidak ditemukan atau sudah diproses.");
    }

    $data = mysqli_fetch_assoc($query);
    $id_sampah = $data['id_sampah'];
    $berat = $data['berat_kg'];
    $total = $data['total_rp'];

    // Pastikan stok masih cukup saat pengepul menerima.
    $cek_stok = mysqli_query($koneksi, "
        SELECT stok_kg
        FROM sampah
        WHERE id_sampah='$id_sampah'
        FOR UPDATE
    ");

    if (!$cek_stok || mysqli_num_rows($cek_stok) == 0) {
        throw new Exception("Data sampah tidak ditemukan.");
    }

    $stok = mysqli_fetch_assoc($cek_stok);

    if ($berat > $stok['stok_kg']) {
        throw new Exception("Stok sampah sudah tidak cukup.");
    }

    // 1. Ubah status transaksi.
    $update_transaksi = mysqli_query($koneksi, "
        UPDATE transaksi
        SET status='disetujui'
        WHERE id_transaksi='$id_transaksi'
        AND status='pending'
    ");

    if (!$update_transaksi || mysqli_affected_rows($koneksi) != 1) {
        throw new Exception("Status transaksi gagal diubah.");
    }

    // 2. Kurangi stok karena pembelian sudah diterima pengepul.
    $update_stok = mysqli_query($koneksi, "
        UPDATE sampah
        SET stok_kg = stok_kg - $berat
        WHERE id_sampah='$id_sampah'
    ");

    if (!$update_stok || mysqli_affected_rows($koneksi) != 1) {
        throw new Exception("Stok gagal diperbarui.");
    }

    // 3. Tambahkan uang hasil penjualan ke saldo admin.
    $update_admin = mysqli_query($koneksi, "
        UPDATE users
        SET saldo = saldo + $total
        WHERE role='admin'
    ");

    if (!$update_admin || mysqli_affected_rows($koneksi) < 1) {
        throw new Exception("Saldo admin gagal diperbarui.");
    }

    mysqli_commit($koneksi);

    header("Location: index.php");
    exit;

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    die("Gagal menerima transaksi: " . $e->getMessage());
}
