<?php
session_start();

if (!isset($_SESSION['status']) || $_SESSION['status'] != "sukses") {
    header("location:../login.php");
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("location:../login.php");
    exit;
}

include "../koneksi.php";

$id_user = $_SESSION['id_user'];
$nominal = isset($_POST['nominal']) ? (int)$_POST['nominal'] : 0;

if ($nominal <= 0) {
    echo "Nominal harus lebih dari 0!";
    exit;
}

mysqli_begin_transaction($koneksi);

try {
    // Kurangi saldo hanya jika saldo admin masih mencukupi.
    $update = mysqli_query($koneksi, "
        UPDATE users
        SET saldo = saldo - $nominal
        WHERE id_user='$id_user'
        AND role='admin'
        AND saldo >= $nominal
    ");

    if (!$update || mysqli_affected_rows($koneksi) != 1) {
        throw new Exception("Saldo admin tidak mencukupi atau data admin tidak ditemukan.");
    }

    // Catat penarikan sebagai sukses.
    $simpan = mysqli_query($koneksi, "
        INSERT INTO tarik_saldo
        (id_user, nominal, status, tanggal)
        VALUES
        ('$id_user', '$nominal', 'sukses', NOW())
    ");

    if (!$simpan) {
        throw new Exception("Gagal menyimpan riwayat penarikan: " . mysqli_error($koneksi));
    }

    mysqli_commit($koneksi);

    header("Location: dashboard.php?page=tarik");
    exit;

} catch (Throwable $e) {
    mysqli_rollback($koneksi);
    echo "Gagal menarik saldo: " . htmlspecialchars($e->getMessage());
}
?>
