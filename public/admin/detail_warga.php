<?php
session_start();
require_once "../../config/database.php";

/* AUTH ADMIN */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

/* VALIDASI ID */
if (!isset($_GET['id'])) {
    header("Location: warga.php");
    exit;
}

$id_warga = (int) $_GET['id'];

/* DATA WARGA */
$qW = mysqli_query($conn, "SELECT * FROM tbl_warga WHERE id_warga='$id_warga'");
if (mysqli_num_rows($qW) === 0) {
    header("Location: warga.php");
    exit;
}
$warga = mysqli_fetch_assoc($qW);

/* DATA UMKM */
$qU = mysqli_query($conn, "
  SELECT id_umkm, nama_usaha, jenis_usaha, created_at
  FROM tbl_umkm
  WHERE id_warga='$id_warga'
  ORDER BY created_at DESC
");

/* JUMLAH UMKM */
$total_umkm = mysqli_num_rows($qU);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Warga | Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../../assets/styles/admin-styles.css">
</head>
<body>
 <div class="overlay" id="overlay"></div>
<div class="wrapper">
<?php include "sidebar.php"; ?>

<main class="content">
<?php include "topbar.php"; ?>

<h5 class="mb-3"><i class="fas fa-user"></i> Detail Warga</h5>

<!-- RINGKASAN -->
<div class="row g-3 mb-3">
  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-icon bg-primary">
        <i class="fas fa-store"></i>
      </div>
      <div>Total UMKM</div>
      <h4><?= $total_umkm ?></h4>
    </div>
  </div>
</div>

<!-- TAB -->
<ul class="nav nav-tabs mb-3">
  <li class="nav-item">
    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profil">
      Profil Warga
    </button>
  </li>
  <li class="nav-item">
    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#umkm">
      UMKM
    </button>
  </li>
</ul>

<div class="tab-content">

<!-- ================= PROFIL WARGA ================= -->
<div class="tab-pane fade show active" id="profil">
  <div class="card-box">

    <div class="row g-4">

      <!-- IDENTITAS -->
      <div class="col-md-6">
        <div class="border rounded p-3 h-100">
          <h6 class="mb-3 text-primary">
            <i class="fas fa-id-card"></i> Identitas
          </h6>

          <div class="mb-2">
            <small class="text-muted">Nama Lengkap</small>
            <div class="fw-semibold"><?= htmlspecialchars($warga['nama_lengkap']) ?></div>
          </div>

          <div class="mb-2">
            <small class="text-muted">NIK</small>
            <div class="fw-semibold"><?= htmlspecialchars($warga['nik']) ?></div>
          </div>

          <div class="mb-2">
            <small class="text-muted">Tanggal Lahir</small>
            <div><?= $warga['tanggal_lahir'] ?: '-' ?></div>
          </div>

          <div class="mb-2">
            <small class="text-muted">Agama</small>
            <div><?= $warga['agama'] ?: '-' ?></div>
          </div>

          <div>
            <small class="text-muted">Status Perkawinan</small>
            <div><?= $warga['status_perkawinan'] ?: '-' ?></div>
          </div>
        </div>
      </div>

      <!-- KONTAK -->
      <div class="col-md-6">
        <div class="border rounded p-3 h-100">
          <h6 class="mb-3 text-success">
            <i class="fas fa-phone"></i> Kontak
          </h6>

          <div class="mb-2">
            <small class="text-muted">No HP</small>
            <div><?= htmlspecialchars($warga['no_hp']) ?></div>
          </div>

          <div class="mb-2">
            <small class="text-muted">Email</small>
            <div><?= htmlspecialchars($warga['email']) ?: '-' ?></div>
          </div>

          <div class="mb-2">
            <small class="text-muted">Username</small>
            <div><?= htmlspecialchars($warga['username']) ?></div>
          </div>

          <div>
            <small class="text-muted">Tanggal Daftar</small>
            <div><?= date('d-m-Y', strtotime($warga['created_at'])) ?></div>
          </div>
        </div>
      </div>

      <!-- ALAMAT -->
      <div class="col-md-12">
        <div class="border rounded p-3">
          <h6 class="mb-2 text-warning">
            <i class="fas fa-map-marker-alt"></i> Alamat Lengkap
          </h6>
          <p class="mb-0"><?= nl2br(htmlspecialchars($warga['alamat'])) ?></p>
        </div>
      </div>

    </div>

  </div>
</div>


<!-- ================= UMKM WARGA ================= -->
<div class="tab-pane fade" id="umkm">
<div class="card-box">

<?php if ($total_umkm > 0): ?>
<div class="table-responsive">
<table class="table table-hover align-middle">
  <thead>
    <tr>
      <th>#</th>
      <th>Nama Usaha</th>
      <th>Jenis Usaha</th>
      <th>Tanggal Pengajuan</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
  <?php $no=1; mysqli_data_seek($qU, 0); while ($u = mysqli_fetch_assoc($qU)): ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= htmlspecialchars($u['nama_usaha']) ?></td>
      <td><?= htmlspecialchars($u['jenis_usaha']) ?></td>
      <td><?= date('d-m-Y', strtotime($u['created_at'])) ?></td>
      <td>
        <a href="detail_umkm.php?id=<?= $u['id_umkm'] ?>"
           class="btn btn-sm btn-primary">
          <i class="fas fa-eye"></i> Detail UMKM
        </a>
      </td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
</div>
<?php else: ?>
  <div class="text-muted">Warga ini belum memiliki UMKM.</div>
<?php endif; ?>

</div>
</div>

</div>

<a href="warga.php" class="btn btn-secondary mt-3">
  <i class="fas fa-arrow-left"></i> Kembali
</a>

</main>
</div>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<?php include "footer.php"?>
</body>
</html>
