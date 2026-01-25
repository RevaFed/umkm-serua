<?php
require_once "config/database.php";

/* ===============================
   VALIDASI ID
================================ */
$id_umkm = (int)($_GET['id'] ?? 0);
if ($id_umkm === 0) {
    header("Location: blockchain-list.php");
    exit;
}

/* ===============================
   INFO UMKM + PEMILIK
================================ */
$stmt = $conn->prepare("
  SELECT 
    u.nama_usaha,
    w.nama_lengkap
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE u.id_umkm = ?
");
$stmt->bind_param("i", $id_umkm);
$stmt->execute();
$info = $stmt->get_result()->fetch_assoc();

if (!$info) {
    header("Location: blockchain-list.php");
    exit;
}

/* ===============================
   DATA BLOCKCHAIN (TIMELINE)
================================ */
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
   MAPPING TRANSAKSI
================================ */
$map = [
  'pengajuan' => [
    'icon' => 'file-alt',
    'bg'   => 'bg-secondary',
    'label'=> 'Pengajuan UMKM',
    'desc' => 'UMKM diajukan oleh warga melalui sistem.'
  ],
  'verifikasi_rt' => [
    'icon' => 'user-check',
    'bg'   => 'bg-info',
    'label'=> 'Verifikasi RT',
    'desc' => 'UMKM diverifikasi oleh Ketua RT.'
  ],
  'verifikasi_rw' => [
    'icon' => 'users-cog',
    'bg'   => 'bg-warning',
    'label'=> 'Verifikasi RW',
    'desc' => 'UMKM diverifikasi oleh Ketua RW.'
  ],
  'surat_pengantar_terbit' => [
    'icon' => 'file-signature',
    'bg'   => 'bg-success',
    'label'=> 'Legalitas Terbit',
    'desc' => 'Surat legalitas UMKM resmi diterbitkan.'
  ],
    'pengajuan_ulang' => [
    'icon'  => 'redo',
    'bg'    => 'bg-secondary',
    'label' => 'Pengajuan Ulang',
    'desc'  => 'UMKM diajukan kembali setelah perbaikan dokumen.'
  ],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Blockchain UMKM</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/fontawesome/css/all.min.css">

<style>
html,body{height:100%}
body{
  background:#f4f6f9;
  display:flex;
  flex-direction:column
}
.main-content{
  flex:1;
  animation:fadeUp .6s ease
}
@keyframes fadeUp{
  from{opacity:0;transform:translateY(20px)}
  to{opacity:1;transform:translateY(0)}
}

/* TIMELINE */
.timeline{
  position:relative;
  padding-left:45px;
  margin-top:40px
}
.timeline::before{
  content:"";
  position:absolute;
  left:18px;
  top:0;
  width:4px;
  height:100%;
  background:#0d6efd;
  border-radius:10px
}
.timeline-step{
  position:relative;
  margin-bottom:40px
}
.timeline-icon{
  position:absolute;
  left:-2px;
  top:0;
  width:40px;
  height:40px;
  border-radius:50%;
  color:#fff;
  display:flex;
  align-items:center;
  justify-content:center
}
.timeline-content{
  background:#fff;
  padding:20px 25px;
  border-radius:12px;
  box-shadow:0 10px 30px rgba(0,0,0,.08)
}
.hash-box{
  font-family:monospace;
  font-size:12px;
  background:#f1f3f5;
  padding:10px;
  border-radius:6px;
  word-break:break-all
}
footer{background:#031633;color:#bfc7d5}
</style>
</head>

<body>

<!-- HEADER -->
<nav class="navbar navbar-dark bg-primary shadow-sm mb-4">
  <div class="container">
    <span class="navbar-brand fw-semibold">
      <i class="fas fa-link"></i> Detail Blockchain UMKM
    </span>
  </div>
</nav>

<!-- CONTENT -->
<div class="container main-content">

  <h4 class="fw-bold">Riwayat Blockchain UMKM</h4>
  <p class="text-muted">
    Halaman ini menampilkan jejak proses legal UMKM
    yang tercatat permanen dalam sistem blockchain.
  </p>

  <!-- INFO UMKM -->
  <div class="card shadow-sm mt-4">
    <div class="card-body">
      <strong>Nama Usaha:</strong>
      <?= htmlspecialchars($info['nama_usaha']) ?><br>
      <strong>Nama Pemilik:</strong>
      <?= htmlspecialchars($info['nama_lengkap']) ?>
    </div>
  </div>

  <!-- TIMELINE -->
  <div class="timeline">

<?php if ($qBC->num_rows > 0): ?>
<?php while ($bc = $qBC->fetch_assoc()):
  $cfg = $map[$bc['tipe_transaksi']] ?? null;
  if (!$cfg) continue;
?>
  <div class="timeline-step">
    <div class="timeline-icon <?= $cfg['bg'] ?>">
      <i class="fas fa-<?= $cfg['icon'] ?>"></i>
    </div>
    <div class="timeline-content">
      <span class="badge <?= $cfg['bg'] ?> mb-2">
        <?= $cfg['label'] ?>
      </span>
      <p class="mb-1"><?= $cfg['desc'] ?></p>
      <small class="text-muted">
        <?= date('d F Y, H:i', strtotime($bc['tanggal_tx'])) ?> WIB
      </small>
      <div class="hash-box mt-2">
        <?= htmlspecialchars($bc['hash_tx']) ?>
      </div>
    </div>
  </div>
<?php endwhile; ?>
<?php else: ?>
  <div class="alert alert-warning mt-4">
    Belum ada transaksi blockchain untuk UMKM ini.
  </div>
<?php endif; ?>

  </div>

  <a href="blockchain-list.php" class="btn btn-secondary mt-4">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
  </a>

</div>

<!-- FOOTER -->
<footer class="py-3 mt-5">
  <div class="container text-center">
    <small>
      © 2026 Sistem Perizinan UMKM<br>
      Kelurahan Serua – Kecamatan Ciputat
    </small>
  </div>
</footer>

<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
