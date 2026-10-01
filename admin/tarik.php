<?php
$id_user = $_SESSION['id_user'];

$query = mysqli_query($koneksi, "
    SELECT saldo
    FROM users
    WHERE id_user='$id_user'
    AND role='admin'
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo '<div class="alert alert-danger">Data admin tidak ditemukan.</div>';
    exit;
}

$saldo = $data['saldo'];
?>
<h1>Tarik Saldo Admin</h1>

<div class="card shadow-sm p-4 mt-3">

    <h5>Saldo Admin Saat Ini</h5>

    <h2 class="mt-2">
        Rp <?= number_format($saldo, 0, ',', '.'); ?>
    </h2>

    <hr>

    <form action="tarik_simpan.php" method="POST">

        <div class="mb-3">
            <label class="form-label">Nominal Penarikan</label>

            <input
                type="number"
                name="nominal"
                class="form-control"
                min="1"
                max="<?= (int)$saldo; ?>"
                placeholder="Masukkan nominal"
                required
            >

            <div class="form-text">
                Maksimal penarikan: Rp <?= number_format($saldo, 0, ',', '.'); ?>
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

<hr class="my-4">

<h3>Riwayat Tarik Saldo Admin</h3>>
<?php
$riwayat = mysqli_query($koneksi, "
    SELECT id_tarik, nominal, status, tanggal
    FROM tarik_saldo
    WHERE id_user='$id_user'
    ORDER BY id_tarik DESC
");
?>
<div class="table-responsive mt-3">
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Nominal</th>
            <th>Status</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; while ($r = mysqli_fetch_assoc($riwayat)) { ?>
        <tr>
            <td><?= $no++; ?></td>
            <td>Rp <?= number_format($r['nominal'], 0, ',', '.'); ?></td>
            <td><?= htmlspecialchars($r['status']); ?></td>
            <td><?= htmlspecialchars($r['tanggal']); ?></td>
        </tr>
        <?php } ?>

        <?php if (mysqli_num_rows($riwayat) == 0) { ?>
        <tr>
            <td colspan="4" class="text-center">Belum ada riwayat penarikan.</td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</div>
