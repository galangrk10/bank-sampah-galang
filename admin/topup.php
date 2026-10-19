<?php
$id_admin = (int)($_SESSION['id_user'] ?? 0);
$q_saldo = mysqli_query($koneksi, "SELECT saldo FROM users WHERE id_user=$id_admin AND role='admin'");
$data_saldo = $q_saldo ? mysqli_fetch_assoc($q_saldo) : null;
$saldo_sekarang = (float)($data_saldo['saldo'] ?? 0);
?>
<div class="dashboard-heading"><div><h2>Top Up Saldo Admin</h2><p>Tambahkan dana ke saldo admin agar setoran siswa/guru bisa disetujui.</p></div></div>
<div class="row g-3">
  <div class="col-lg-5"><div class="card shadow-sm border-0 p-4">
    <div class="text-muted mb-2">Saldo Admin Saat Ini</div><h2 class="mb-4">Rp <?= number_format($saldo_sekarang, 0, ',', '.'); ?></h2>
    <?php if(isset($_GET['success'])) { ?><div class="alert alert-success">Saldo admin berhasil ditambahkan.</div><?php } ?>
    <?php if(isset($_GET['error'])) { ?><div class="alert alert-danger">Nominal top up tidak valid.</div><?php } ?>
    <form action="topup_simpan.php" method="POST" onsubmit="return confirm('Tambahkan nominal ini ke saldo admin?')">
      <div class="mb-3"><label for="nominal" class="form-label">Nominal Top Up (Rp)</label><input type="number" id="nominal" name="nominal" class="form-control" min="50000" step="1" placeholder="Minimum: 50000" required></div>
      <button type="submit" class="btn btn-primary">＋ Tambah Saldo</button>
    </form><div class="form-text mt-3">Minimum top up Rp 50.000.</div>
  </div></div>

</div>
