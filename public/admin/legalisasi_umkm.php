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

/* DATA UMKM + WARGA */
$q = mysqli_query($conn, "
  SELECT 
    u.nama_usaha, u.created_at,
    w.nama_lengkap,
    l.id_legalisasi
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  LEFT JOIN tbl_legalisasi l ON u.id_umkm = l.id_umkm
  WHERE u.id_umkm = '$id_umkm'
");

if (mysqli_num_rows($q) === 0) {
    header("Location: umkm.php");
    exit;
}

$data = mysqli_fetch_assoc($q);

/* JIKA SUDAH LEGALISASI */
if ($data['id_legalisasi']) {
    $_SESSION['alert'] = 'sudah_legal';
    header("Location: detail_umkm.php?id=$id_umkm");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Legalisasi UMKM</title>
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
  <h5><i class="fas fa-file-signature"></i> Legalisasi UMKM</h5>

  <p>
    <strong>Nama Usaha:</strong> <?= htmlspecialchars($data['nama_usaha']) ?><br>
    <strong>Nama Warga:</strong> <?= htmlspecialchars($data['nama_lengkap']) ?><br>
    <strong>Tanggal Pengajuan:</strong> <?= date('d-m-Y', strtotime($data['created_at'])) ?>
  </p>

  <div class="alert alert-info">
    Setelah surat diterbitkan, status UMKM akan <strong>RESMI DISETUJUI</strong>
    dan <strong>tidak dapat diubah</strong>.
  </div>

  <form action="../../controlls/proses_legalisasi_umkm.php" method="POST">
    <input type="hidden" name="id_umkm" value="<?= $id_umkm ?>">

    <div class="mb-3">
      <label class="form-label">Nomor Surat</label>
      <input type="text" name="nomor_surat" class="form-control"
             placeholder="Contoh: 470/UMKM/RT01/2026" required>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-success">
        <i class="fas fa-file-signature"></i> Terbitkan Surat
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
