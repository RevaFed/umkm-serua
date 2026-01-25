<?php
require "auth.php";
require_once "../../config/database.php";

/* ANTI CACHE */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

$id_warga = $_SESSION['id_warga'];

/* TOTAL PENGAJUAN */
$qTotal = mysqli_query($conn, "
  SELECT COUNT(*) AS total
  FROM tbl_umkm
  WHERE id_warga = '$id_warga'
");
$total = mysqli_fetch_assoc($qTotal)['total'];

/* DISSETUJUI */
$qSetuju = mysqli_query($conn, "
  SELECT COUNT(*) AS total
  FROM tbl_umkm
  WHERE id_warga = '$id_warga'
    AND status = 'disetujui'
");
$setuju = mysqli_fetch_assoc($qSetuju)['total'];

/* DALAM PROSES */
$qProses = mysqli_query($conn, "
  SELECT COUNT(*) AS total
  FROM tbl_umkm
  WHERE id_warga = '$id_warga'
    AND status IN ('menunggu_rt','menunggu_rw')
");
$proses = mysqli_fetch_assoc($qProses)['total'];

/* UMKM TERAKHIR */
$qLast = mysqli_query($conn, "
  SELECT nama_usaha, created_at, status
  FROM tbl_umkm
  WHERE id_warga = '$id_warga'
  ORDER BY created_at DESC
  LIMIT 1
");
$last = mysqli_fetch_assoc($qLast);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Warga | UMKM</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../../assets/styles/warga.css">
</head>
<body>

<div class="overlay" id="overlay"></div>

<div class="wrapper">
<?php include "sidebar.php"; ?>

<main class="content">
<?php include "topbar.php"; ?>

<!-- CARDS -->
<div class="row g-4 mb-4">

  <div class="col-12 col-md-4">
    <div class="card-box">
      <div class="card-icon"><i class="fas fa-file-alt"></i></div>
      <div>Total Pengajuan</div>
      <h3 class="mt-1"><?= $total ?></h3>
    </div>
  </div>

  <div class="col-12 col-md-4">
    <div class="card-box">
      <div class="card-icon"><i class="fas fa-clock"></i></div>
      <div>Dalam Proses</div>
      <h3 class="mt-1"><?= $proses ?></h3>
    </div>
  </div>

  <div class="col-12 col-md-4">
    <div class="card-box">
      <div class="card-icon"><i class="fas fa-check-circle"></i></div>
      <div>Disetujui</div>
      <h3 class="mt-1"><?= $setuju ?></h3>
    </div>
  </div>

</div>

<!-- STATUS TERAKHIR -->
<div class="card-box d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <strong>Status UMKM Terakhir</strong><br>

    <?php if ($last): ?>
      <?= htmlspecialchars($last['nama_usaha']) ?><br>
      <small class="text-muted">
        Diajukan <?= date('d F Y', strtotime($last['created_at'])) ?>
      </small>
    <?php else: ?>
      <em>Belum ada pengajuan</em>
    <?php endif; ?>

  </div>

  <?php if ($last): ?>
    <?php
      switch ($last['status']) {
        case 'menunggu_rt':
          echo '<span class="badge bg-warning text-dark px-3 py-2">Menunggu RT</span>';
          break;
        case 'menunggu_rw':
          echo '<span class="badge bg-info text-dark px-3 py-2">Menunggu RW</span>';
          break;
        case 'ditolak_rt':
        case 'ditolak_rw':
          echo '<span class="badge bg-danger px-3 py-2">Ditolak</span>';
          break;
        case 'disetujui':
          echo '<span class="badge bg-success px-3 py-2">Disetujui</span>';
          break;
      }
    ?>
  <?php endif; ?>
</div>

</main>
</div>
<?php include "footer.php";?>
</body>
</html>
