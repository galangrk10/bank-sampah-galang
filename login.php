<?php

session_start();

include "koneksi.php";

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "
        SELECT *
        FROM users
        WHERE username='$username'
        AND password='$password'
    ");

    if (!$query) {
        die("Query error: " . mysqli_error($koneksi));
    }

    $data = mysqli_fetch_assoc($query);

    if ($data) {

        // SIMPAN SESSION
        $_SESSION['status'] = "sukses";
        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];

        // REDIRECT BERDASARKAN ROLE

        if ($data['role'] == "admin") {

            header("location:admin/dashboard.php");
            exit;

        }

        elseif ($data['role'] == "siswa") {

            header("location:siswa/dashboard.php");
            exit;

        }

        elseif ($data['role'] == "guru") {

            header("location:guru/dashboard.php");
            exit;

        }

        elseif ($data['role'] == "pengepul") {

            header("location:pengepul/dashboard.php");
            exit;

        }

        else {

            echo "Role tidak dikenali: ";
            echo $data['role'];

        }

    } else {

        echo "<script>
                alert('Username atau password salah!');
              </script>";

    }

}

?>
<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Login - Bank Sampah</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="style.css">

</head>

<body class="login-page">

<div class="login-wrap">

    <div class="login-brand-side">
        <div class="login-brand-icon">♻</div>
        <h1>Bank Sampah</h1>
        <p>Kelola sampah, transaksi, saldo, dan pengguna dengan lebih mudah.</p>
        <div class="login-brand-stat">
            <div><strong>♻</strong><span>Ramah Lingkungan</span></div>
            <div><strong>✓</strong><span>Data Terorganisir</span></div>
        </div>
    </div>

    <div class="login-form-side">
        <div class="login-form-inner">
            <div class="login-logo">♻</div>
            <h2>Selamat Datang 👋</h2>
            <p class="login-subtitle">Masuk ke akun Bank Sampah kamu</p>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>

                <button type="submit" name="login" class="btn btn-primary login-btn">
                    Masuk ke Dashboard <span>→</span>
                </button>
            </form>
        </div>
    </div>

</div>

</body>

</html>
