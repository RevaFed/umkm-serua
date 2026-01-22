<?php
session_start();
require_once "../../config/database.php";

/* ANTI CACHE */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

/* AUTH ADMIN */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

/* VALIDASI ID */
if (!isset($_GET['id'])) {
    header("Location: umkm.php");
    exit;
}

$id_umkm  = (int) $_GET['id'];
$id_admin = $_SESSION['id'];

/* AMBIL DATA UMKM */
$q = mysqli_query($conn, "
  SELECT u.nama_usaha, u.created_at, w.nama_lengkap
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE u.id_umkm = '$id_umkm'
");

if (mysqli_num_rows($q) === 0) {
    header("Location: umkm.php");
    exit;
}

$data = mysqli_fetch_assoc($q);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Verifikasi UMKM</title>
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

<div class="card-box">
  <h5><i class="fas fa-check-circle"></i> Verifikasi UMKM</h5>

  <p>
    <strong>Nama Usaha:</strong> <?= htmlspecialchars($data['nama_usaha']) ?><br>
    <strong>Nama Warga:</strong> <?= htmlspecialchars($data['nama_lengkap']) ?><br>
    <strong>Tanggal Pengajuan:</strong> <?= date('d-m-Y', strtotime($data['created_at'])) ?>
  </p>

  <div class="alert alert-warning">
    Pastikan seluruh dokumen dan data UMKM sudah diverifikasi sebelum melanjutkan.
  </div>

  <form action="../../controlls/proses_verifikasi_umkm.php" method="POST">
    <input type="hidden" name="id_umkm" value="<?= $id_umkm ?>">

    <div class="d-flex gap-2">
      <button type="submit" name="aksi" value="verifikasi"
              class="btn btn-success">
        <i class="fas fa-check"></i> Verifikasi UMKM
      </button>

      <a href="detail_umkm.php?id=<?= $id_umkm ?>" class="btn btn-secondary">
        Kembali
      </a>
    </div>
  </form>
</div>

</main>
</div>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<?php include "footer.php"?>
</body>
</html>
