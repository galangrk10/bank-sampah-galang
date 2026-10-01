<?php

session_start();

if (!isset($_SESSION['status']) || $_SESSION['status'] != "sukses") {
    header("location:../login.php");
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] != "siswa") {
    header("location:../login.php");
    exit;
}

include "../koneksi.php";

$id_user = $_SESSION['id_user'];

$id_sampah = $_POST['id_sampah'] ?? '';
$berat_kg = $_POST['berat_kg'] ?? '';

// Cek input
if (empty($id_sampah) || empty($berat_kg)) {
    echo "Data belum lengkap!";
    exit;
}

// Ambil data sampah
$query = mysqli_query($koneksi, "
    SELECT *
    FROM sampah
    WHERE id_sampah='$id_sampah'
");

$sampah = mysqli_fetch_array($query);

if (!$sampah) {
    echo "Data sampah tidak ditemukan!";
    exit;
}

// Hitung total uang
$harga_per_kg = $sampah['harga_per_kg'];
$total_rp = $berat_kg * $harga_per_kg;

// Simpan sebagai PENDING.
// Saldo user dan stok sampah BELUM berubah.
// Perubahan baru dilakukan setelah admin melakukan ACC.
$simpan = mysqli_query($koneksi, "
    INSERT INTO transaksi
    (
        id_user,
        id_sampah,
        tipe,
        berat_kg,
        total_rp,
        status,
        tanggal
    )
    VALUES
    (
        '$id_user',
        '$id_sampah',
        'setor',
        '$berat_kg',
        '$total_rp',
        'pending',
        NOW()
    )
");

if (!$simpan) {
    echo "Gagal menyimpan transaksi!";
    echo "<br>";
    echo mysqli_error($koneksi);
    exit;
}

// Kembali ke riwayat setoran.
// Saldo belum bertambah karena masih pending.
header("location:dashboard.php?page=index");
exit;

?>
