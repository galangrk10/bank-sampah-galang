<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'sukses') { header('Location: ../login.php'); exit; }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }
include '../koneksi.php';
$id_admin = (int)($_SESSION['id_user'] ?? 0);
$nominal = filter_input(INPUT_POST, 'nominal', FILTER_VALIDATE_INT);
if (!$nominal || $nominal < 50000) { header('Location: dashboard.php?page=topup&error=nominal'); exit; }
$stmt = mysqli_prepare($koneksi, "UPDATE users SET saldo = saldo + ? WHERE id_user = ? AND role = 'admin'");
if (!$stmt) { die('Gagal menyiapkan top up: ' . htmlspecialchars(mysqli_error($koneksi))); }
mysqli_stmt_bind_param($stmt, 'ii', $nominal, $id_admin);
if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) === 1) { header('Location: dashboard.php?page=topup&success=1'); exit; }
http_response_code(400);
echo 'Top up gagal. Pastikan akun admin ditemukan.';
?>
