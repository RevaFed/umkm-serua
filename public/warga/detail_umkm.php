<?php
session_start();
require_once "../../config/database.php";

/* ANTI CACHE */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

/* AUTH WARGA */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'warga') {
    header("Location: ../login.php");
    exit;
}

$id_warga = $_SESSION['id_warga'];

/* VALIDASI ID */
if (!isset($_GET['id'])) {
    header("Location: status_umkm.php");
    exit;
}

$id_umkm = (int) $_GET['id'];

/* AMBIL DATA UMKM (PASTI MILIK WARGA) */
$qUmkm = mysqli_query($conn, "
  SELECT u.*, l.nomor_surat, l.file_surat, l.tanggal_terbit, l.blockchain_tx
  FROM tbl_umkm u
  LEFT JOIN tbl_legalisasi l ON u.id_umkm = l.id_umkm
  WHERE u.id_umkm = '$id_umkm' AND u.id_warga = '$id_warga'
");

if (mysqli_num_rows($qUmkm) === 0) {
    header("Location: status_umkm.php");
    exit;
}

$umkm = mysqli_fetch_assoc($qUmkm);

/* DOKUMEN */
$qDok = mysqli_query($conn, "
  SELECT jenis_dokumen, file_path, created_at
  FROM tbl_dokumen_umkm
  WHERE id_umkm = '$id_umkm'
  ORDER BY created_at ASC
");

/* BLOCKCHAIN TIMELINE */
$qBC = mysqli_query($conn, "
  SELECT tipe_transaksi, hash_tx, tanggal_tx
  FROM tbl_transaksi_blockchain
  WHERE id_umkm = '$id_umkm'
  ORDER BY tanggal_tx ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail UMKM</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../../assets/styles/warga.css">
</head>
<body>

<div class="wrapper">
<?php include "sidebar.php"; ?>

<main class="content">
<?php include "topbar.php"; ?>

<!-- ================= DATA UMKM ================= -->
<div class="card-box mb-3">
  <h5 class="mb-3"><i class="fas fa-store"></i> Detail UMKM</h5>

  <div class="row g-3">
    <div class="col-md-6">
      <strong>Nama Usaha</strong><br>
      <?= htmlspecialchars($umkm['nama_usaha']) ?>
    </div>
    <div class="col-md-6">
      <strong>Jenis Usaha</strong><br>
      <?= htmlspecialchars($umkm['jenis_usaha']) ?>
    </div>
    <div class="col-md-6">
      <strong>Tahun Mulai</strong><br>
      <?= $umkm['tahun_mulai'] ?: '-' ?>
    </div>
    <div class="col-md-6">
      <strong>Jumlah Karyawan</strong><br>
      <?= $umkm['jumlah_karyawan'] ?: '-' ?>
    </div>
    <div class="col-md-6">
      <strong>Tanggal Pengajuan</strong><br>
      <?= date('d-m-Y', strtotime($umkm['created_at'])) ?>
    </div>
  </div>

  <hr>

  <strong>Status</strong><br>
  <?php if ($umkm['nomor_surat']): ?>
    <span class="badge bg-success">Disetujui</span>
    <div class="mt-2">
      <strong>Nomor Surat:</strong> <?= htmlspecialchars($umkm['nomor_surat']) ?><br>
      <strong>Tanggal Terbit:</strong> <?= date('d-m-Y', strtotime($umkm['tanggal_terbit'])) ?><br>
      <a href="../../uploads/surat/<?= $umkm['file_surat'] ?>" target="_blank" class="btn btn-sm btn-success mt-2">
        <i class="fas fa-file-pdf"></i> Lihat Surat
      </a>
    </div>
  <?php else: ?>
    <span class="badge bg-warning text-dark">Menunggu Verifikasi</span>
  <?php endif; ?>
</div>

<!-- ================= DOKUMEN ================= -->
<div class="card-box mb-3">
  <h6><i class="fas fa-folder-open"></i> Dokumen</h6>

<div class="row g-3">
<?php if (mysqli_num_rows($qDok) > 0): ?>
  <?php while ($d = mysqli_fetch_assoc($qDok)): 
    $ext = strtolower(pathinfo($d['file_path'], PATHINFO_EXTENSION));
    $fileUrl = "../../uploads/dokumen_umkm/" . $d['file_path'];
  ?>
    <div class="col-md-4">
      <div class="card h-100 shadow-sm">
        <div class="card-body text-center">
          <strong><?= htmlspecialchars($d['jenis_dokumen']) ?></strong>
          <div class="mt-2">

            <?php if (in_array($ext, ['jpg','jpeg','png'])): ?>
              <img src="<?= $fileUrl ?>" class="img-fluid rounded mb-2"
                   style="max-height:180px; object-fit:cover;">
            <?php else: ?>
              <i class="fas fa-file-pdf fa-4x text-danger mb-2"></i>
            <?php endif; ?>

          </div>

          <small class="text-muted d-block mb-2">
            <?= date('d-m-Y', strtotime($d['created_at'])) ?>
          </small>

          <a href="<?= $fileUrl ?>" target="_blank" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-eye"></i> Lihat
          </a>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
<?php else: ?>
  <div class="text-muted">Tidak ada dokumen.</div>
<?php endif; ?>
</div>

<!-- ================= BLOCKCHAIN TIMELINE ================= -->
<div class="card-box">
  <h6><i class="fas fa-link"></i> Timeline Blockchain</h6>

  <?php if (mysqli_num_rows($qBC) > 0): ?>
    <ul class="list-group">
      <?php while ($bc = mysqli_fetch_assoc($qBC)): ?>
        <li class="list-group-item">
          <strong><?= strtoupper(str_replace('_',' ', $bc['tipe_transaksi'])) ?></strong><br>
          <small class="text-muted"><?= date('d-m-Y H:i', strtotime($bc['tanggal_tx'])) ?></small>
          <div class="mt-1 text-break">
            <code><?= $bc['hash_tx'] ?></code>
          </div>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <div class="text-muted">Belum ada transaksi blockchain.</div>
  <?php endif; ?>
</div>

</main>
</div>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
