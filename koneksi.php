<?php
$koneksi = mysqli_connect("localhost", "root", "", "bank_sampah_galang");

if (!$koneksi) {
    die("Koneksi gagal : " . mysqli_connect_error());
}

// Pastikan kolom status dan id_pengepul tersedia untuk alur penjualan ke pengepul.
$cek_status = mysqli_query($koneksi, "
    SELECT COUNT(*) AS jumlah
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'transaksi'
    AND COLUMN_NAME = 'status'
");

if ($cek_status && mysqli_fetch_assoc($cek_status)['jumlah'] == 0) {
    mysqli_query($koneksi, "ALTER TABLE transaksi ADD status VARCHAR(20) NOT NULL DEFAULT 'pending' AFTER total_rp");
}

$cek_pengepul = mysqli_query($koneksi, "
    SELECT COUNT(*) AS jumlah
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'transaksi'
    AND COLUMN_NAME = 'id_pengepul'
");

if ($cek_pengepul && mysqli_fetch_assoc($cek_pengepul)['jumlah'] == 0) {
    mysqli_query($koneksi, "ALTER TABLE transaksi ADD id_pengepul INT NULL AFTER id_user");
}
?>
