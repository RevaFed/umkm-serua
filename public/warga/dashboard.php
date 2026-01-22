<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
require_once "../../config/database.php";
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'warga') {
    header("Location: ../../login.php");
    exit;
}

$id_warga = $_SESSION['id_warga'];

/* TOTAL PENGAJUAN */
$qTotal = mysqli_query($conn, "
  SELECT COUNT(*) AS total 
  FROM tbl_umkm 
  WHERE id_warga='$id_warga'
");
$total = mysqli_fetch_assoc($qTotal)['total'];

/* SUDAH DISAHKAN (ADA SURAT) */
$qSetuju = mysqli_query($conn, "
  SELECT COUNT(*) AS setuju
  FROM tbl_umkm u
  JOIN tbl_legalisasi l ON u.id_umkm = l.id_umkm
  WHERE u.id_warga='$id_warga'
");
$setuju = mysqli_fetch_assoc($qSetuju)['setuju'];

/* DALAM PROSES */
$proses = $total - $setuju;

/* UMKM TERAKHIR */
$qLast = mysqli_query($conn, "
  SELECT u.*, l.nomor_surat
  FROM tbl_umkm u
  LEFT JOIN tbl_legalisasi l ON u.id_umkm = l.id_umkm
  WHERE u.id_warga='$id_warga'
  ORDER BY u.created_at DESC
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
<?php include "sidebar.php"?>

<main class="content">
<?php include "topbar.php"?>

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

<!-- STATUS -->
<div class="card-box d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <strong>Status UMKM Terakhir</strong><br>

    <?php if ($last): ?>
      <?= $last['nama_usaha'] ?><br>
      <small class="text-muted">
        Diajukan <?= date('d F Y', strtotime($last['created_at'])) ?>
      </small>
    <?php else: ?>
      <em>Belum ada pengajuan</em>
    <?php endif; ?>

  </div>

  <?php if ($last && $last['nomor_surat']): ?>
    <span class="badge bg-success px-3 py-2">Surat Terbit</span>
  <?php elseif ($last): ?>
    <span class="badge bg-warning text-dark px-3 py-2">Dalam Verifikasi</span>
  <?php endif; ?>
</div>

</main>
</div>

<script>
const btn = document.getElementById("btnMenu");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");

btn.onclick = () => {
  sidebar.classList.add("show");
  overlay.classList.add("show");
};

overlay.onclick = () => {
  sidebar.classList.remove("show");
  overlay.classList.remove("show");
};
</script>

</body>
</html>
