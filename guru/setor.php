<h1>Setor Sampah</h1>

<div class="card p-4 mt-3">

    <form action="simpan_setor.php" method="POST">

        <div class="mb-3">

            <label class="form-label">
                Jenis Sampah
            </label>

            <select name="id_sampah" class="form-select" required>

                <option value="">
                    -- Pilih Jenis Sampah --
                </option>

                <?php

                $query = mysqli_query(
                    $koneksi,
                    "SELECT * FROM sampah ORDER BY jenis_sampah ASC"
                );

                while ($data = mysqli_fetch_array($query)) {

                ?>

                    <option value="<?= $data['id_sampah']; ?>">

                        <?= $data['jenis_sampah']; ?>
                        -
                        Rp <?= number_format(
                            $data['harga_per_kg'],
                            0,
                            ',',
                            '.'
                        ); ?>/Kg

                    </option>

                <?php

                }

                ?>

            </select>

        </div>

        <div class="mb-3">

            <label class="form-label">
                Berat Sampah (Kg)
            </label>

            <input
                type="number"
                name="berat_kg"
                class="form-control"
                min="1"
                step="1"
                placeholder="Masukkan berat sampah"
                required
            >

        </div>

        <button type="submit" class="btn btn-primary">
            Setor Sampah
        </button>

        <a
            href="dashboard.php?page=index"
            class="btn btn-secondary"
        >
            Kembali
        </a>

    </form>

</div>
