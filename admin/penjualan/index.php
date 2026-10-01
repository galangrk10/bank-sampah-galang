<?php

include __DIR__ . "/../../koneksi.php";

// =========================
// DATA PENGEPUL
// =========================

$query_pengepul = mysqli_query($koneksi, "
    SELECT *
    FROM users
    WHERE role = 'pengepul'
");

// =========================
// DATA SAMPAH
// =========================

$query_sampah = mysqli_query($koneksi, "
    SELECT *
    FROM sampah
    WHERE stok_kg > 0
");

?>
<h1>Jual Sampah ke Pengepul</h1>

<div class="card mt-4">

    <div class="card-body">

        <form
            action="penjualan/simpan.php"
            method="POST">

            <!-- PENGEPUL -->

            <div class="mb-3">

                <label class="form-label">
                    Pengepul
                </label>

                <select
                    name="id_pengepul"
                    class="form-select"
                    required>

                    <option value="">
                        -- Pilih Pengepul --
                    </option>

                    <?php while ($data = mysqli_fetch_assoc($query_pengepul)) { ?>

                        <option value="<?= $data['id_user']; ?>">

                            <?= htmlspecialchars($data['nama']); ?>

                        </option>

                    <?php } ?>

                </select>

            </div>

            <!-- SAMPAH -->

            <div class="mb-3">

                <label class="form-label">
                    Jenis Sampah
                </label>

                <select
                    name="id_sampah"
                    class="form-select"
                    required>

                    <option value="">
                        -- Pilih Sampah --
                    </option>

                    <?php while ($data = mysqli_fetch_assoc($query_sampah)) { ?>

                        <option value="<?= $data['id_sampah']; ?>">

                            <?= htmlspecialchars($data['jenis_sampah']); ?>

                            -
                            Stok <?= $data['stok_kg']; ?> Kg

                            -
                            Rp <?= number_format(
                                $data['harga_per_kg'],
                                0,
                                ',',
                                '.'
                            ); ?>/Kg

                        </option>

                    <?php } ?>

                </select>

            </div>

            <!-- BERAT -->

            <div class="mb-3">

                <label class="form-label">
                    Berat Sampah (Kg)
                </label>

                <input
                    type="number"
                    name="berat"
                    class="form-control"
                    min="0.1"
                    step="0.1"
                    required>

            </div>

            <!-- BUTTON -->

            <button
                type="submit"
                name="jual"
                class="btn btn-primary">

                Jual ke Pengepul

            </button>

        </form>

    </div>

</div>

<?php
$query_penjualan = mysqli_query($koneksi, "
    SELECT transaksi.*, users.nama AS nama_pengepul, sampah.jenis_sampah
    FROM transaksi
    JOIN users ON transaksi.id_pengepul = users.id_user
    JOIN sampah ON transaksi.id_sampah = sampah.id_sampah
    WHERE transaksi.tipe = 'jual_pengepul'
    ORDER BY transaksi.id_transaksi DESC
");
?>

<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title">Riwayat Penjualan ke Pengepul</h5>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pengepul</th>
                        <th>Sampah</th>
                        <th>Berat</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                <?php if (mysqli_num_rows($query_penjualan) > 0) { ?>
                    <?php while ($data = mysqli_fetch_assoc($query_penjualan)) { ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($data['nama_pengepul']); ?></td>
                            <td><?= htmlspecialchars($data['jenis_sampah']); ?></td>
                            <td><?= $data['berat_kg']; ?> Kg</td>
                            <td>Rp <?= number_format($data['total_rp'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if ($data['status'] == 'pending') { ?>
                                    <span class="badge bg-warning text-dark">Menunggu Pengepul</span>
                                <?php } elseif ($data['status'] == 'disetujui') { ?>
                                    <span class="badge bg-success">Diterima</span>
                                <?php } else { ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">Belum ada penjualan.</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

