<?php
$id_user = $_SESSION['id_user'];

$query = mysqli_query($koneksi, "
    SELECT saldo
    FROM users
    WHERE id_user='$id_user'
");

$data = mysqli_fetch_array($query);
?>
<h1>Tarik Saldo</h1>

<div class="card p-4 mt-3">

    <h4>Saldo Anda</h4>

    <h2>
        Rp <?= number_format($data['saldo'], 0, ',', '.'); ?>
    </h2>

    <hr>

    <form action="simpan_tarik.php" method="POST">

        <div class="mb-3">
            <label class="form-label">
                Nominal Penarikan
            </label>

            <input
                type="number"
                name="nominal"
                class="form-control"
                min="1"
                max="<?= $data['saldo']; ?>"
                placeholder="Masukkan nominal"
                required
            >

            <div class="form-text">
                Maksimal penarikan:
                Rp <?= number_format($data['saldo'], 0, ',', '.'); ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            Tarik Saldo
        </button>

        <a href="dashboard.php" class="btn btn-secondary">
            Kembali
        </a>

    </form>

</div>
