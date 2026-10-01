<?php

$query = mysqli_query($koneksi, "
    SELECT id_user, nama, username, role
    FROM users
    ORDER BY id_user DESC
");

?>
<h1>Data User</h1>

<div class="card mt-3">

    <div class="card-body">

        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Role</th>
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
                        <?= $data['nama']; ?>
                    </td>

                    <td>
                        <?= $data['username']; ?>
                    </td>

                    <td>
                        <?= $data['role']; ?>
                    </td>

                </tr>

            <?php

            }

            ?>

            </tbody>

        </table>

    </div>

</div>
