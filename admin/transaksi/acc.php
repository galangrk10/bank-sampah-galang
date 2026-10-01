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

mysqli_begin_transaction($koneksi);

try {

    // Ambil hanya transaksi SETOR yang masih PENDING.
    $query = mysqli_query($koneksi, "
        SELECT *
        FROM transaksi
        WHERE id_transaksi='$id_transaksi'
        AND tipe='setor'
        AND status='pending'
        FOR UPDATE
    ");

    if (!$query || mysqli_num_rows($query) == 0) {
        throw new Exception("Setoran tidak ditemukan atau sudah diproses.");
    }

    $data = mysqli_fetch_assoc($query);

    $id_user = $data['id_user'];
    $id_sampah = $data['id_sampah'];
    $berat = $data['berat_kg'];
    $total = $data['total_rp'];

    // 1. Tandai setoran diterima.
    $update_status = mysqli_query($koneksi, "
        UPDATE transaksi
        SET status='disetujui'
        WHERE id_transaksi='$id_transaksi'
        AND status='pending'
    ");

    if (!$update_status || mysqli_affected_rows($koneksi) != 1) {
        throw new Exception("Status setoran gagal diubah.");
    }

    // 2. Baru tambahkan stok.
    $update_stok = mysqli_query($koneksi, "
        UPDATE sampah
        SET stok_kg = stok_kg + $berat
        WHERE id_sampah='$id_sampah'
    ");

    if (!$update_stok || mysqli_affected_rows($koneksi) != 1) {
        throw new Exception("Stok sampah gagal diperbarui.");
    }

    // 3. Baru tambahkan saldo siswa/guru.
    $update_saldo = mysqli_query($koneksi, "
        UPDATE users
        SET saldo = saldo + $total
        WHERE id_user='$id_user'
    ");

    if (!$update_saldo || mysqli_affected_rows($koneksi) != 1) {
        throw new Exception("Saldo user gagal diperbarui.");
    }

    mysqli_commit($koneksi);

    header("Location: ../dashboard.php?page=transaksi");
    exit;

} catch (Exception $e) {

    mysqli_rollback($koneksi);

    die("Gagal menerima setoran: " . $e->getMessage());
}

?>
