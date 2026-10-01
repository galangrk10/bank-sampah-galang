<?php

// ==========================================
// RIWAYAT / PERSETUJUAN SETORAN
// ==========================================

$query_setor = mysqli_query($koneksi, "

    SELECT
        transaksi.*,
        users.nama,
        users.role,
        sampah.jenis_sampah

    FROM transaksi

    JOIN users
        ON transaksi.id_user = users.id_user

    JOIN sampah
        ON transaksi.id_sampah = sampah.id_sampah

    WHERE transaksi.tipe = 'setor'

    ORDER BY transaksi.id_transaksi DESC

");

if (!$query_setor) {
    die("Query setoran error: " . mysqli_error($koneksi));
}

// ==========================================
// RIWAYAT PENARIKAN
// ==========================================

$query_tarik = mysqli_query($koneksi, "

    SELECT
        tarik_saldo.*,
        users.nama,
        users.role

    FROM tarik_saldo

    JOIN users
        ON tarik_saldo.id_user = users.id_user

    ORDER BY tarik_saldo.id_tarik DESC

");

if (!$query_tarik) {
    die("Query penarikan error: " . mysqli_error($koneksi));
}

?>
<h1>Riwayat Transaksi</h1>

<p class="text-muted">
    Setoran siswa/guru harus disetujui terlebih dahulu sebelum saldo dan stok bertambah.
</p>

<!-- ==========================================
     SETORAN
========================================== -->

<div class="card mt-4">

    <div class="card-header">
        <h4 class="mb-0">Persetujuan & Riwayat Setoran</h4>
    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-striped">

                <thead class="table-primary">

                    <tr>
                        <th>No</th>
                        <th>Nama User</th>
                        <th>Role</th>
                        <th>Jenis Sampah</th>
                        <th>Berat</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>

                </thead>

                <tbody>

                <?php

                $no = 1;

                if (mysqli_num_rows($query_setor) == 0) {

                ?>

                    <tr>
                        <td colspan="9" class="text-center">
                            Belum ada setoran.
                        </td>
                    </tr>

                <?php

                } else {

                    while ($data = mysqli_fetch_assoc($query_setor)) {

                ?>

                    <tr>

                        <td><?= $no++; ?></td>

                        <td><?= htmlspecialchars($data['nama']); ?></td>

                        <td>
                            <span class="badge bg-primary">
                                <?= htmlspecialchars($data['role']); ?>
                            </span>
                        </td>

                        <td><?= htmlspecialchars($data['jenis_sampah']); ?></td>

                        <td><?= $data['berat_kg']; ?> Kg</td>

                        <td>
                            Rp <?= number_format(
                                $data['total_rp'],
                                0,
                                ',',
                                '.'
                            ); ?>
                        </td>

                        <td>

                            <?php if ($data['status'] == 'pending') { ?>

                                <span class="badge bg-warning text-dark">
                                    Pending
                                </span>

                            <?php } elseif ($data['status'] == 'disetujui') { ?>

                                <span class="badge bg-success">
                                    Diterima
                                </span>

                            <?php } else { ?>

                                <span class="badge bg-danger">
                                    Ditolak
                                </span>

                            <?php } ?>

                        </td>

                        <td><?= $data['tanggal']; ?></td>

                        <td>

                            <?php if ($data['status'] == 'pending') { ?>

                                <a
                                    href="transaksi/acc.php?id=<?= $data['id_transaksi']; ?>"
                                    class="btn btn-success btn-sm"
                                    onclick="return confirm('Terima setoran ini?')">
                                    Terima
                                </a>

                                <a
                                    href="transaksi/tolak.php?id=<?= $data['id_transaksi']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Tolak setoran ini?')">
                                    Tolak
                                </a>

                            <?php } else { ?>

                                -

                            <?php } ?>

                        </td>

                    </tr>

                <?php

                    }

                }

                ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- ==========================================
     RIWAYAT PENARIKAN
========================================== -->

<div class="card mt-4 mb-4">

    <div class="card-header">

        <h4 class="mb-0">
            Riwayat Penarikan Saldo
        </h4>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-striped">

                <thead class="table-warning">

                    <tr>
                        <th>No</th>
                        <th>Nama User</th>
                        <th>Role</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>

                </thead>

                <tbody>

                <?php

                $no = 1;

                while ($data = mysqli_fetch_assoc($query_tarik)) {

                ?>

                    <tr>

                        <td><?= $no++; ?></td>

                        <td><?= htmlspecialchars($data['nama']); ?></td>

                        <td>
                            <span class="badge bg-secondary">
                                <?= htmlspecialchars($data['role']); ?>
                            </span>
                        </td>

                        <td>
                            Rp <?= number_format(
                                $data['nominal'],
                                0,
                                ',',
                                '.'
                            ); ?>
                        </td>

                        <td>

                            <?php
                            if ($data['status'] == 'sukses') {
                                echo '<span class="badge bg-success">Berhasil</span>';
                            } elseif ($data['status'] == 'pending') {
                                echo '<span class="badge bg-warning text-dark">Pending</span>';
                            } else {
                                echo '<span class="badge bg-danger">Ditolak</span>';
                            }
                            ?>

                        </td>

                        <td><?= $data['tanggal']; ?></td>

                    </tr>

                <?php

                }

                ?>

                </tbody>

            </table>

        </div>

    </div>

</div>
