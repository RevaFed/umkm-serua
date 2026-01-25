<?php
session_start();
require_once "../../config/database.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'warga') {
    header("Location: ../login.php");
    exit;
}

$id_warga = $_SESSION['id_warga'];
$qWarga = mysqli_query($conn, "SELECT * FROM tbl_warga WHERE id_warga='$id_warga'");
$warga = mysqli_fetch_assoc($qWarga);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Ajukan UMKM</title>
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
<?php if (isset($_SESSION['alert']) && $_SESSION['alert'] === 'error'): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Gagal!</strong> <?= $_SESSION['msg']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php unset($_SESSION['alert'], $_SESSION['msg']); endif; ?>


<div class="card-form">

<h5 class="mb-3"><i class="fas fa-file-alt"></i> Pengajuan UMKM</h5>

<form action="../../controlls/proses_ajukan_umkm.php" method="POST" enctype="multipart/form-data">

<!-- ================= TAB HEADER ================= -->
<ul class="nav nav-tabs mb-3" id="umkmTab" role="tablist">
  <li class="nav-item">
    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#data-diri" type="button">
      <i class="fas fa-user"></i> Data Diri
    </button>
  </li>
  <li class="nav-item">
    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#data-umkm" type="button">
      <i class="fas fa-store"></i> Data UMKM
    </button>
  </li>
</ul>

<!-- ================= TAB CONTENT ================= -->
<div class="tab-content">

<!-- ===== TAB DATA DIRI ===== -->
<div class="tab-pane fade show active" id="data-diri">

<div class="row g-3">

  <div class="col-md-6">
    <label class="form-label">Nama Lengkap</label>
    <input type="text" class="form-control" value="<?= htmlspecialchars($warga['nama_lengkap']) ?>" readonly>
  </div>

  <div class="col-md-6">
    <label class="form-label">Tanggal Lahir</label>
    <input type="date" class="form-control" value="<?= $warga['tanggal_lahir'] ?>" readonly>
  </div>

  <div class="col-md-12">
    <label class="form-label">Alamat Lengkap</label>
    <textarea class="form-control" rows="2" readonly><?= htmlspecialchars($warga['alamat']) ?></textarea>
  </div>

  <div class="col-md-4">
    <label class="form-label">Agama</label>
    <input type="text" class="form-control" value="<?= $warga['agama'] ?>" readonly>
  </div>

  <div class="col-md-4">
    <label class="form-label">Status Perkawinan</label>
    <input type="text" class="form-control" value="<?= $warga['status_perkawinan'] ?>" readonly>
  </div>

  <div class="col-md-4">
    <label class="form-label">No Telepon</label>
    <input type="text" class="form-control" value="<?= $warga['no_hp'] ?>" readonly>
  </div>

  <div class="col-md-6">
    <label class="form-label">Email</label>
    <input type="email" class="form-control" value="<?= $warga['email'] ?>" readonly>
  </div>

</div>

<div class="mt-3 text-end">
  <button class="btn btn-primary" type="button"
    onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('[data-bs-target=\'#data-umkm\']')).show()">
    Lanjut ke Data UMKM →
  </button>
</div>

</div>

<!-- ===== TAB DATA UMKM ===== -->
<div class="tab-pane fade" id="data-umkm">

<div class="row g-3">

  <div class="col-md-6">
    <label class="form-label">Nama Usaha</label>
    <input type="text" name="nama_usaha" class="form-control" required>
  </div>

  <div class="col-md-6">
    <label class="form-label">Jenis Usaha</label>
    <select name="jenis_usaha" class="form-select" required>
      <option value="">-- Pilih --</option>
      <option>Perdagangan</option>
      <option>Jasa</option>
      <option>Kuliner</option>
      <option>Industri Rumah Tangga</option>
    </select>
  </div>

  <div class="col-md-6">
    <label class="form-label">Tahun Mulai</label>
    <input type="number" name="tahun_mulai" class="form-control">
  </div>

  <div class="col-md-6">
    <label class="form-label">Jumlah Karyawan</label>
    <input type="number" name="jumlah_karyawan" class="form-control">
  </div>

</div>

<hr>

<h6>Upload Dokumen</h6>
<div class="row g-3">
<?php
$dokumen = [
  "Foto KTP",
  "Foto Akta Kelahiran",
  "Foto KK",
  "Sertifikat Halal",
  "Foto Menu Jualan",
  "Foto Tempat Usaha",
  "Foto Pemilik & Usaha"
];
foreach ($dokumen as $d):
?>
  <div class="col-md-6">
    <label class="form-label"><?= $d ?></label>
    <input type="file" name="dokumen[]" class="form-control" required>
    <input type="hidden" name="jenis_dokumen[]" value="<?= $d ?>">
  </div>
<?php endforeach; ?>
</div>

<div class="mt-4 d-flex justify-content-between">
  <button type="button" class="btn btn-light"
    onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('[data-bs-target=\'#data-diri\']')).show()">
    ← Kembali
  </button>

  <button type="submit" class="btn btn-success px-4">
    <i class="fas fa-paper-plane"></i> Kirim Pengajuan
  </button>
</div>

</div>

</div>
</form>
</div>

</main>
</div>

<?php include "footer.php";?>
</body>
</html>
