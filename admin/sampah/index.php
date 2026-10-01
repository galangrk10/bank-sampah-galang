<?php

$query = mysqli_query($koneksi, "
    SELECT *
    FROM sampah
    ORDER BY id_sampah DESC
");

if (!$query) {
    die("Query error: " . mysqli_error($koneksi));
}

?>
<h1>Data Sampah</h1>

<p class="text-muted">
    Data stok sampah yang tersedia di Bank Sampah.
</p>

<div class="card mt-3">

    <div class="card-body">

        <table class="table table-bordered table-striped">

            <thead class="table-primary">

                <tr>

                    <th>No</th>

                    <th>Jenis Sampah</th>

                    <th>Harga per Kg</th>

                    <th>Stok</th>

                </tr>

            </thead>

            <tbody>

            <?php

            $no = 1;

            while ($data = mysqli_fetch_assoc($query)) {

            ?>

                <tr>

                    <td>
                        <?= $no++; ?>
                    </td>

                    <td>
                        <?= $data['jenis_sampah']; ?>
                    </td>

                    <td>
                        Rp <?= number_format(
                            $data['harga_per_kg'],
                            0,
                            ',',
                            '.'
                        ); ?>
                    </td>

                    <td>
                        <?= $data['stok_kg']; ?> Kg
                    </td>

                </tr>

            <?php

            }

            ?>

            </tbody>

        </table>

    </div>

</div>
