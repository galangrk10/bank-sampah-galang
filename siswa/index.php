<?php

$id_user = $_SESSION['id_user'];

$query = mysqli_query($koneksi, "
    SELECT
        transaksi.*,
        sampah.jenis_sampah
    FROM transaksi
    JOIN sampah
        ON transaksi.id_sampah = sampah.id_sampah
    WHERE transaksi.id_user='$id_user'
    AND transaksi.tipe='setor'
    ORDER BY transaksi.id_transaksi DESC
");

?>
<h1>Data Sampah</h1>

<a href="dashboard.php?page=setor" class="btn btn-primary">
    + Setor Sampah
</a>

<div class="card p-3 mt-3">

    <table class="table table-bordered table-striped">

        <thead>

            <tr>
                <th>No</th>
                <th>Jenis Sampah</th>
                <th>Berat</th>
                <th>Total</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>

        </thead>

        <tbody>

        <?php

        $no = 1;

        if (mysqli_num_rows($query) == 0) {

        ?>

            <tr>
                <td colspan="6" class="text-center">
                    Belum ada setoran.
                </td>
            </tr>

        <?php

        } else {

            while ($data = mysqli_fetch_array($query)) {

        ?>

            <tr>

                <td><?= $no++; ?></td>

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
                            Menunggu Persetujuan
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

            </tr>

        <?php

            }

        }

        ?>

        </tbody>

    </table>

</div>
