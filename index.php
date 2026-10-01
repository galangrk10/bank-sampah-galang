<?php

session_start();

// Kalau belum login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sukses") {

    header("location:login.php");
    exit;

}

// Kalau sudah login, arahkan berdasarkan role

if (!isset($_SESSION['role'])) {

    session_destroy();

    header("location:login.php");
    exit;

}

if ($_SESSION['role'] == "siswa") {

    header("location:siswa/dashboard.php");
    exit;

}

elseif ($_SESSION['role'] == "guru") {

    header("location:guru/dashboard.php");
    exit;

}

elseif ($_SESSION['role'] == "admin") {

    header("location:admin/dashboard.php");
    exit;

}

elseif ($_SESSION['role'] == "pengepul") {

    header("location:pengepul/dashboard.php");
    exit;

}

// Kalau role tidak dikenal
else {

    session_destroy();

    header("location:login.php");
    exit;

}

?>
