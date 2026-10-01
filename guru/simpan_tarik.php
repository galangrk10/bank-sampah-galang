<?php

session_start();

if ($_SESSION['status'] != "sukses") {
    header("location:../login.php");
    exit;
}

if ($_SESSION['role'] != "siswa") {
    header("location:../login.php");
    exit;
}

include "../koneksi.php";

$id_user = $_SESSION['id_user'];
$nominal = $_POST['nominal'];

// Ambil saldo siswa
$query = mysqli_query($koneksi, "
    SELECT saldo
    FROM users
    WHERE id_user='$id_user'
");

$data = mysqli_fetch_array($query);

$saldo = $data['saldo'];

// Cek nominal
if (empty($nominal)) {
    echo "Nominal belum diisi!";
    exit;
}

if ($nominal <= 0) {
    echo "Nominal harus lebih dari 0!";
    exit;
}

// Cek saldo
if ($nominal > $saldo) {
    echo "Saldo tidak mencukupi!";
    exit;
}

// Simpan data penarikan
$simpan = mysqli_query($koneksi, "
    INSERT INTO tarik_saldo
    (
        id_user,
        nominal,
        status,
        tanggal
    )
    VALUES
    (
        '$id_user',
        '$nominal',
        'sukses',
        NOW()
    )
");

// Kalau gagal insert
if (!$simpan) {
    echo "Gagal menyimpan penarikan!";
    echo "<br>";
    echo mysqli_error($koneksi);
    exit;
}

// Kurangi saldo
$update = mysqli_query($koneksi, "
    UPDATE users
    SET saldo = saldo - '$nominal'
    WHERE id_user='$id_user'
");

// Kalau gagal update saldo
if (!$update) {
    echo "Gagal mengurangi saldo!";
    echo "<br>";
    echo mysqli_error($koneksi);
    exit;
}

// Kembali ke dashboard
header("location:dashboard.php");

exit;

?>
