<?php
require "auth.php";
require_once "../../config/database.php";

/* ===============================
   ANTI CACHE
=============================== */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

/* ===============================
   AUTH WARGA
=============================== */
$id_warga = $_SESSION['id_warga'];

/* ===============================
   VALIDASI ID
=============================== */
$id_umkm = (int)($_GET['id'] ?? 0);
if ($id_umkm === 0) {
    header("Location: status_umkm.php");
    exit;
}

/* ===============================
   DATA UMKM (MILIK WARGA)
=============================== */
$stmt = $conn->prepare("
  SELECT *
  FROM tbl_umkm
  WHERE id_umkm = ? AND id_warga = ?
");
$stmt->bind_param("ii", $id_umkm, $id_warga);
$stmt->execute();
$umkm = $stmt->get_result()->fetch_assoc();

if (!$umkm) {
    header("Location: status_umkm.php");
    exit;
}

/* ===============================
   DOKUMEN UMKM
=============================== */
$stmt = $conn->prepare("
  SELECT jenis_dokumen, file_path, created_at
  FROM tbl_dokumen_umkm
  WHERE id_umkm = ?
  ORDER BY created_at ASC
");
$stmt->bind_param("i", $id_umkm);
$stmt->execute();
$qDok = $stmt->get_result();

/* ===============================
   BLOCKCHAIN TIMELINE
=============================== */
$stmt = $conn->prepare("
  SELECT tipe_transaksi, hash_tx, tanggal_tx
  FROM tbl_transaksi_blockchain
  WHERE id_umkm = ?
  ORDER BY tanggal_tx ASC
");
$stmt->bind_param("i", $id_umkm);
$stmt->execute();
$qBC = $stmt->get_result();

/* ===============================
   MAP LABEL BLOCKCHAIN
=============================== */
$bcLabel = [
  'pengajuan'              => 'Pengajuan UMKM',
  'verifikasi_rt'          => 'Verifikasi RT',
  'verifikasi_rw'          => 'Verifikasi RW',
  'surat_pengantar_terbit' => 'Surat Pengantar Terbit'
];
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

<!-- ================= STATUS ================= -->
<div class="card-box mb-3">
  <h6>Status Pengajuan</h6>
<?php
switch ($umkm['status']) {
  case 'menunggu_rt':
    echo '<span class="badge bg-warning text-dark">Menunggu Verifikasi RT</span>';
    break;
  case 'menunggu_rw':
    echo '<span class="badge bg-info text-dark">Menunggu Verifikasi RW</span>';
    break;
  case 'ditolak_rt':
    echo '<span class="badge bg-danger">Ditolak RT</span>';
    break;
  case 'ditolak_rw':
    echo '<span class="badge bg-danger">Ditolak RW</span>';
    break;
  case 'disetujui':
    echo '<span class="badge bg-success">Disetujui</span>';
    break;
}
?>
</div>

<!-- ================= CATATAN PENOLAKAN ================= -->
<?php if (in_array($umkm['status'], ['ditolak_rt','ditolak_rw'])): ?>
<div class="card-box mb-3 border border-danger">
  <h6 class="text-danger">
    <i class="fas fa-exclamation-circle"></i> Pengajuan Ditolak
  </h6>

  <p class="mb-2">
    <strong>Alasan Penolakan:</strong><br>
    <?= htmlspecialchars($umkm['catatan_penolakan']) ?>
  </p>

  <a href="../../controlls/edit_umkm.php?id=<?= $id_umkm ?>"
     class="btn btn-warning">
    <i class="fas fa-edit"></i> Perbaiki & Ajukan Ulang
  </a>
</div>
<?php endif; ?>


<!-- ================= DATA UMKM ================= -->
<div class="card-box mb-3">
  <h5><i class="fas fa-store"></i> Data UMKM</h5>

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
</div>

<!-- ================= SURAT ================= -->
<?php if ($umkm['status'] === 'disetujui'): ?>
<div class="card-box mb-3">
  <h6><i class="fas fa-file-pdf"></i> Surat Pengantar</h6>
  <a href="../../uploads/surat/surat_umkm_<?= $umkm['id_umkm'] ?>.pdf"
     target="_blank"
     class="btn btn-success btn-sm">
    <i class="fas fa-eye"></i> Lihat Surat
  </a>
</div>
<?php endif; ?>

<!-- ================= DOKUMEN ================= -->
<div class="card-box mb-3">
  <h6><i class="fas fa-folder-open"></i> Dokumen UMKM</h6>

  <div class="row g-3">
<?php if ($qDok->num_rows > 0): while ($d = $qDok->fetch_assoc()):
  $ext = strtolower(pathinfo($d['file_path'], PATHINFO_EXTENSION));
  $url = "../../uploads/dokumen_umkm/" . $d['file_path'];
?>
  <div class="col-md-4">
    <div class="card h-100 shadow-sm">
      <div class="card-body text-center">
        <strong><?= htmlspecialchars($d['jenis_dokumen']) ?></strong>

        <div class="mt-2">
<?php if (in_array($ext, ['jpg','jpeg','png','webp'])): ?>
          <img src="<?= $url ?>" class="img-fluid rounded"
               style="max-height:180px;object-fit:cover;">
<?php else: ?>
          <i class="fas fa-file-pdf fa-4x text-danger"></i>
<?php endif; ?>
        </div>

        <small class="text-muted d-block mt-2">
          <?= date('d-m-Y', strtotime($d['created_at'])) ?>
        </small>

        <a href="<?= $url ?>" target="_blank"
           class="btn btn-sm btn-outline-primary mt-2">
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

<!-- ================= BLOCKCHAIN ================= -->
<div class="card-box">
  <h6><i class="fas fa-link"></i> Timeline Blockchain</h6>

<?php if ($qBC->num_rows > 0): ?>
  <ul class="list-group">
<?php while ($bc = $qBC->fetch_assoc()): ?>
    <li class="list-group-item">
      <strong><?= $bcLabel[$bc['tipe_transaksi']] ?? $bc['tipe_transaksi'] ?></strong><br>
      <small class="text-muted">
        <?= date('d-m-Y H:i', strtotime($bc['tanggal_tx'])) ?>
      </small>
      <div class="mt-1 text-break">
        <code><?= htmlspecialchars($bc['hash_tx']) ?></code>
      </div>
    </li>
<?php endwhile; ?>
  </ul>
<?php else: ?>
  <div class="text-muted">Belum ada transaksi blockchain.</div>
<?php endif; ?>
</div>

<a href="status_umkm.php" class="btn btn-secondary mt-3">
  <i class="fas fa-arrow-left"></i> Kembali
</a>

</main>
</div>

<?php include "footer.php";?>
</body>
</html>
