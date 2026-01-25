<?php
require "auth.php";
require_once "../../config/database.php";

$id_umkm = (int)($_GET['id'] ?? 0);
if ($id_umkm === 0) {
    header("Location: riwayat.php");
    exit;
}

$rt = $_SESSION['rt'];
$rw = $_SESSION['rw'];

/* DATA UMKM */
$stmt = $conn->prepare("
  SELECT 
    u.*,
    w.nama_lengkap, w.nik, w.alamat, w.no_hp
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE u.id_umkm = ?
    AND w.rt = ?
    AND w.rw = ?
");
$stmt->bind_param("iss", $id_umkm, $rt, $rw);
$stmt->execute();
$umkm = $stmt->get_result()->fetch_assoc();

if (!$umkm) {
    header("Location: riwayat.php");
    exit;
}

/* DOKUMEN */
$stmt = $conn->prepare("
  SELECT jenis_dokumen, file_path, created_at
  FROM tbl_dokumen_umkm
  WHERE id_umkm = ?
");
$stmt->bind_param("i", $id_umkm);
$stmt->execute();
$qDok = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Riwayat UMKM</title>

<link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../../assets/styles/admin-styles.css">

<style>
.info-label { font-size:13px;color:#6c757d }
.info-value { font-weight:600 }
.doc-card img { max-height:160px; object-fit:cover }
</style>
</head>
<body>
<div class="overlay" id="overlay"></div>
<div class="wrapper">
<?php include "sidebar.php"; ?>
<main class="content">
<?php include "topbar.php"; ?>

<!-- STATUS -->
<div class="card-box mb-3 text-center">
<?php
switch ($umkm['status']) {
  case 'menunggu_rw':
    echo '<span class="badge bg-success px-4 py-2">Disetujui RT</span>';
    break;
  case 'ditolak_rt':
    echo '<span class="badge bg-danger px-4 py-2">Ditolak RT</span>';
    break;
}
?>
</div>

<!-- DATA WARGA -->
<div class="card-box mb-4">
  <h6><i class="fas fa-user"></i> Data Warga</h6>
  <div class="row g-3 mt-1">
    <div class="col-md-6">
      <div class="info-label">Nama</div>
      <div class="info-value"><?= htmlspecialchars($umkm['nama_lengkap']) ?></div>
    </div>
    <div class="col-md-6">
      <div class="info-label">NIK</div>
      <div class="info-value"><?= htmlspecialchars($umkm['nik']) ?></div>
    </div>
    <div class="col-md-12">
      <div class="info-label">Alamat</div>
      <div class="info-value"><?= htmlspecialchars($umkm['alamat']) ?></div>
    </div>
  </div>
</div>

<!-- DATA UMKM -->
<div class="card-box mb-4">
  <h6><i class="fas fa-store"></i> Data UMKM</h6>
  <div class="row g-3 mt-1">
    <div class="col-md-6">
      <div class="info-label">Nama Usaha</div>
      <div class="info-value"><?= htmlspecialchars($umkm['nama_usaha']) ?></div>
    </div>
    <div class="col-md-6">
      <div class="info-label">Jenis Usaha</div>
      <div class="info-value"><?= htmlspecialchars($umkm['jenis_usaha']) ?></div>
    </div>
    <div class="col-md-4">
      <div class="info-label">Tahun Mulai</div>
      <div class="info-value"><?= $umkm['tahun_mulai'] ?: '-' ?></div>
    </div>
    <div class="col-md-4">
      <div class="info-label">Jumlah Karyawan</div>
      <div class="info-value"><?= $umkm['jumlah_karyawan'] ?: '-' ?></div>
    </div>
    <div class="col-md-4">
      <div class="info-label">Tanggal Pengajuan</div>
      <div class="info-value"><?= date('d-m-Y', strtotime($umkm['created_at'])) ?></div>
    </div>
  </div>
</div>

<!-- CATATAN -->
<?php if ($umkm['catatan_penolakan']): ?>
<div class="card-box mb-4 border-start border-danger border-4">
  <h6><i class="fas fa-comment"></i> Catatan RT</h6>
  <p class="mb-0"><?= nl2br(htmlspecialchars($umkm['catatan_penolakan'])) ?></p>
</div>
<?php endif; ?>

<!-- DOKUMEN -->
<div class="card-box mb-4">
  <h6><i class="fas fa-folder-open"></i> Dokumen</h6>
  <div class="row g-3 mt-2">

<?php if ($qDok->num_rows > 0): while ($d = $qDok->fetch_assoc()):
  $ext = strtolower(pathinfo($d['file_path'], PATHINFO_EXTENSION));
  $url = "../../uploads/dokumen_umkm/".$d['file_path'];
?>
<div class="col-md-4">
  <div class="card doc-card h-100">
    <div class="card-body text-center">
      <strong><?= htmlspecialchars($d['jenis_dokumen']) ?></strong>

      <div class="mt-2">
      <?php if (in_array($ext,['jpg','jpeg','png'])): ?>
        <img src="<?= $url ?>" class="img-fluid rounded">
      <?php else: ?>
        <i class="fas fa-file-pdf fa-4x text-danger"></i>
      <?php endif; ?>
      </div>

      <a href="<?= $url ?>" target="_blank"
         class="btn btn-outline-primary btn-sm mt-2">
        <i class="fas fa-eye"></i> Lihat
      </a>
    </div>
  </div>
</div>
<?php endwhile; else: ?>
<div class="text-muted">Tidak ada dokumen.</div>
<?php endif; ?>

  </div>
</div>

<a href="riwayat.php" class="btn btn-secondary">
  <i class="fas fa-arrow-left"></i> Kembali
</a>

</main>
</div>

<?php include "footer.php";?>
</body>
</html>
