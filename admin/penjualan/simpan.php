<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . "/../../koneksi.php";

// =========================
// CEK LOGIN
// =========================

if (
    !isset($_SESSION['status']) ||
    $_SESSION['status'] != "sukses"
) {
    header("location:../../login.php");
    exit;
}

// =========================
// CEK ROLE
// =========================

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] != "admin"
) {
    header("location:../../login.php");
    exit;
}

// =========================
// CEK FORM
// =========================

if (!isset($_POST['jual'])) {

    header("Location: ../dashboard.php?page=penjualan");
    exit;

}

$id_admin = $_SESSION['id_user'];
$id_pengepul = $_POST['id_pengepul'];
$id_sampah = $_POST['id_sampah'];
$berat = $_POST['berat'];

// =========================
// VALIDASI BERAT
// =========================

if ($berat <= 0) {

    die("Berat sampah harus lebih dari 0.");

}

// =========================
// CEK PENGEPUL
// =========================

$cek_pengepul = mysqli_query($koneksi, "

    SELECT id_user
    FROM users
    WHERE id_user='$id_pengepul'
    AND role='pengepul'

");

if (!$cek_pengepul) {

    die(
        "Gagal mengecek pengepul: "
        . mysqli_error($koneksi)
    );

}

if (mysqli_num_rows($cek_pengepul) == 0) {

    die("Pengepul tidak ditemukan.");

}

// =========================
// AMBIL DATA SAMPAH
// =========================

$query_sampah = mysqli_query($koneksi, "

    SELECT *
    FROM sampah
    WHERE id_sampah='$id_sampah'

");

if (!$query_sampah) {

    die(
        "Gagal mengambil data sampah: "
        . mysqli_error($koneksi)
    );

}

$sampah = mysqli_fetch_assoc($query_sampah);

if (!$sampah) {

    die("Data sampah tidak ditemukan.");

}

// =========================
// CEK STOK
// =========================

if ($berat > $sampah['stok_kg']) {

    die(
        "Stok tidak cukup. "
        . "Stok tersedia: "
        . $sampah['stok_kg']
        . " Kg."
    );

}

// =========================
// HITUNG TOTAL UANG
// =========================

$harga_per_kg = $sampah['harga_per_kg'];

$total = $berat * $harga_per_kg;

// =========================
// MULAI TRANSAKSI DATABASE
// =========================

mysqli_begin_transaction($koneksi);

try {

    // =========================
    // 1. SIMPAN TRANSAKSI
    // =========================

    // Penjualan masuk PENDING dulu.
    // Stok dan saldo admin baru berubah setelah pengepul menerima.
    $simpan = mysqli_query($koneksi, "

        INSERT INTO transaksi
        (
            id_user,
            id_pengepul,
            id_sampah,
            tipe,
            berat_kg,
            total_rp,
            status,
            tanggal
        )

        VALUES
        (
            '$id_admin',
            '$id_pengepul',
            '$id_sampah',
            'jual_pengepul',
            '$berat',
            '$total',
            'pending',
            NOW()
        )

    ");

    if (!$simpan) {
        throw new Exception(
            "Gagal menyimpan transaksi: "
            . mysqli_error($koneksi)
        );
    }

    // =========================
    // SELESAI
    // =========================

    mysqli_commit($koneksi);

    // Kembali ke halaman jual
    header("Location: ../dashboard.php?page=penjualan");
    exit;

} catch (Exception $e) {

    // Kalau salah satu gagal,
    // semua perubahan dibatalkan

    mysqli_rollback($koneksi);

    die($e->getMessage());

}

?>
