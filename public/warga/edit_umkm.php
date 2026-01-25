<?php
require "auth.php";
require_once "../../config/database.php";

/* AUTH */
if ($_SESSION['role'] !== 'warga') {
    header("Location: ../../login.php");
    exit;
}

$id_warga = $_SESSION['id_warga'];
$id_umkm  = (int)($_GET['id'] ?? 0);

/* VALIDASI */
$stmt = $conn->prepare("
  SELECT *
  FROM tbl_umkm
  WHERE id_umkm = ?
    AND id_warga = ?
    AND status IN ('ditolak_rt','ditolak_rw')
");
$stmt->bind_param("ii", $id_umkm, $id_warga);
$stmt->execute();
$umkm = $stmt->get_result()->fetch_assoc();

if (!$umkm) {
    header("Location: status_umkm.php");
    exit;
}

/* DOKUMEN */
$dok = $conn->prepare("
  SELECT jenis_dokumen, file_path
  FROM tbl_dokumen_umkm
  WHERE id_umkm = ?
");
$dok->bind_param("i", $id_umkm);
$dok->execute();
$qDok = $dok->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit UMKM</title>
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

<h5 class="mb-3"><i class="fas fa-edit"></i> Perbaiki Pengajuan UMKM</h5>

<!-- ALASAN PENOLAKAN -->
<div class="alert alert-danger">
  <strong>Ditolak <?= strtoupper(substr($umkm['status'], -2)) ?></strong><br>
  Alasan:<br>
  <?= nl2br(htmlspecialchars($umkm['catatan_penolakan'])) ?>
</div>

<form action="../../controlls/edit_umkm_proses.php"
      method="POST"
      enctype="multipart/form-data">

<input type="hidden" name="id_umkm" value="<?= $id_umkm ?>">

<div class="card-box mb-3">
  <div class="mb-3">
    <label class="form-label">Nama Usaha</label>
    <input type="text" name="nama_usaha"
           class="form-control"
           value="<?= htmlspecialchars($umkm['nama_usaha']) ?>"
           required>
  </div>

  <div class="mb-3">
    <label class="form-label">Jenis Usaha</label>
    <input type="text" name="jenis_usaha"
           class="form-control"
           value="<?= htmlspecialchars($umkm['jenis_usaha']) ?>"
           required>
  </div>

  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Tahun Mulai</label>
      <input type="number" name="tahun_mulai"
             class="form-control"
             value="<?= $umkm['tahun_mulai'] ?>">
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Jumlah Karyawan</label>
      <input type="number" name="jumlah_karyawan"
             class="form-control"
             value="<?= $umkm['jumlah_karyawan'] ?>">
    </div>
  </div>
</div>

<!-- DOKUMEN -->
<div class="card-box mb-3">
  <h6><i class="fas fa-folder-open"></i> Dokumen</h6>

  <?php while ($d = $qDok->fetch_assoc()): ?>
    <div class="mb-2">
      <strong><?= htmlspecialchars($d['jenis_dokumen']) ?></strong><br>
      <a href="../../uploads/dokumen_umkm/<?= $d['file_path'] ?>"
         target="_blank"
         class="btn btn-sm btn-outline-primary mb-2">
        <i class="fas fa-eye"></i> Lihat Dokumen Lama
      </a>

      <input type="file"
             name="dokumen[<?= htmlspecialchars($d['jenis_dokumen']) ?>]"
             class="form-control">
      <small class="text-muted">
        Kosongkan jika tidak ingin mengganti
      </small>
    </div>
  <?php endwhile; ?>
</div>

<div class="d-flex gap-2">
  <button class="btn btn-success">
    <i class="fas fa-paper-plane"></i> Ajukan Ulang
  </button>
  <a href="status_umkm.php" class="btn btn-secondary">Batal</a>
</div>

</form>

</main>
</div>

<?php include "footer.php";?>
</body>
</html>
