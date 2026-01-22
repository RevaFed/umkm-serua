<?php
require_once "config/database.php";

/* VALIDASI ID */
if (!isset($_GET['id'])) {
  header("Location: blockchain-list.php");
  exit;
}

$id_umkm = (int) $_GET['id'];

/* DATA UMKM + WARGA */
$qInfo = mysqli_query($conn, "
  SELECT u.nama_usaha, w.nama_lengkap
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE u.id_umkm = '$id_umkm'
");
$info = mysqli_fetch_assoc($qInfo);

/* DATA BLOCKCHAIN (TIMELINE) */
$qBC = mysqli_query($conn, "
  SELECT tipe_transaksi, hash_tx, tanggal_tx
  FROM tbl_transaksi_blockchain
  WHERE id_umkm = '$id_umkm'
  ORDER BY tanggal_tx ASC
");

/* STATUS AKHIR */
$status_akhir = 'Pengajuan';
if (mysqli_num_rows($qBC) > 0) {
  $last = mysqli_fetch_assoc(
    mysqli_query($conn,"
      SELECT tipe_transaksi 
      FROM tbl_transaksi_blockchain 
      WHERE id_umkm='$id_umkm'
      ORDER BY tanggal_tx DESC LIMIT 1
    ")
  );
  if ($last['tipe_transaksi'] === 'surat_pengantar_terbit') {
    $status_akhir = 'Legalitas Terbit';
  } elseif ($last['tipe_transaksi'] === 'verifikasi_rt_rw') {
    $status_akhir = 'Verifikasi RT/RW';
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Transaksi Blockchain | Kelurahan Serua</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/fontawesome/css/all.min.css">

<style>
html,body{height:100%}
body{background:#f4f6f9;display:flex;flex-direction:column}
.main-content{flex:1;animation:fadeIn .6s ease}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}

.timeline{position:relative;padding-left:40px;margin-top:40px}
.timeline::before{
  content:"";position:absolute;left:18px;top:0;width:4px;height:100%;
  background:#0d6efd;border-radius:10px
}
.timeline-step{
  position:relative;margin-bottom:40px;opacity:0;
  transform:translateY(30px);animation:stepFade .6s ease forwards
}
.timeline-step:nth-child(1){animation-delay:.2s}
.timeline-step:nth-child(2){animation-delay:.4s}
.timeline-step:nth-child(3){animation-delay:.6s}
@keyframes stepFade{to{opacity:1;transform:translateY(0)}}

.timeline-icon{
  position:absolute;left:-2px;top:0;width:40px;height:40px;
  background:#0d6efd;color:#fff;border-radius:50%;
  display:flex;align-items:center;justify-content:center
}
.timeline-content{
  background:#fff;padding:20px 25px;border-radius:12px;
  box-shadow:0 10px 30px rgba(0,0,0,.08)
}
.hash-box{
  font-family:monospace;font-size:12px;background:#f1f3f5;
  padding:10px;border-radius:6px;word-break:break-all
}
footer{background:#031633;color:#bfc7d5}
</style>
</head>

<body>

<!-- HEADER -->
<nav class="navbar navbar-dark bg-primary shadow-sm mb-4">
  <div class="container">
    <span class="navbar-brand">
      <i class="fas fa-link"></i> Transaksi Blockchain UMKM
    </span>
  </div>
</nav>

<div class="container main-content">

  <h4 class="fw-bold">Riwayat Transaksi Blockchain</h4>
  <p class="text-muted">
    Alur proses perizinan UMKM yang dicatat dalam blockchain.
  </p>

  <!-- INFO UMKM -->
  <div class="card mt-4">
    <div class="card-body">
      <strong>Nama Usaha :</strong> <?= htmlspecialchars($info['nama_usaha']) ?><br>
      <strong>Nama Warga :</strong> <?= htmlspecialchars($info['nama_lengkap']) ?><br>
      <strong>Status Akhir :</strong>
      <span class="badge bg-success"><?= $status_akhir ?></span>
    </div>
  </div>

  <!-- TIMELINE -->
  <div class="timeline">

<?php
$delay = 0;
while ($bc = mysqli_fetch_assoc($qBC)):
  $delay += 0.2;

  if ($bc['tipe_transaksi'] === 'pengajuan') {
    $icon = 'file-alt'; $bg = 'bg-warning';
    $label = 'Pengajuan UMKM';
    $desc  = 'Pengajuan UMKM dilakukan oleh warga melalui sistem.';
  } elseif ($bc['tipe_transaksi'] === 'verifikasi_rt_rw') {
    $icon = 'user-check'; $bg = 'bg-primary';
    $label = 'Verifikasi RT/RW';
    $desc  = 'Data UMKM diverifikasi oleh petugas.';
  } else {
    $icon = 'file-signature'; $bg = 'bg-success';
    $label = 'Surat Legalisasi';
    $desc  = 'Surat legalisasi UMKM diterbitkan.';
  }
?>
  <div class="timeline-step" style="animation-delay:<?= $delay ?>s">
    <div class="timeline-icon <?= $bg ?>">
      <i class="fas fa-<?= $icon ?>"></i>
    </div>
    <div class="timeline-content">
      <span class="badge <?= $bg ?> mb-2"><?= $label ?></span>
      <p class="mb-1"><?= $desc ?></p>
      <small class="text-muted">
        <?= date('d F Y, H:i', strtotime($bc['tanggal_tx'])) ?> WIB
      </small>
      <div class="hash-box mt-2">
        <?= $bc['hash_tx'] ?>
      </div>
    </div>
  </div>
<?php endwhile; ?>

  </div>
</div>

<footer class="py-3 mt-4">
  <div class="container text-center">
    <small>
      © 2026 Sistem Perizinan UMKM <br>
      Kelurahan Serua – Kecamatan Ciputat
    </small>
  </div>
</footer>

<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
