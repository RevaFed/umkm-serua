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
   VALIDASI ID
=============================== */
$id_umkm = (int)($_GET['id'] ?? 0);
if ($id_umkm === 0) {
    header("Location: riwayat.php");
    exit;
}

/* ===============================
   DATA UMKM + WARGA
=============================== */
$stmt = $conn->prepare("
  SELECT 
    u.*,
    w.nama_lengkap, w.nik, w.alamat, w.no_hp,
    l.nomor_surat, l.file_surat, l.tanggal_terbit
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  LEFT JOIN tbl_legalisasi l ON u.id_umkm = l.id_umkm
  WHERE u.id_umkm = ?
");
$stmt->bind_param("i", $id_umkm);
$stmt->execute();
$umkm = $stmt->get_result()->fetch_assoc();

if (!$umkm) {
    header("Location: riwayat.php");
    exit;
}

/* ===============================
   DOKUMEN UMKM
=============================== */
$qDok = $conn->prepare("
  SELECT jenis_dokumen, file_path, created_at
  FROM tbl_dokumen_umkm
  WHERE id_umkm = ?
  ORDER BY created_at ASC
");
$qDok->bind_param("i", $id_umkm);
$qDok->execute();
$dokumen = $qDok->get_result();

/* ===============================
   TIMELINE BLOCKCHAIN
=============================== */
$qBC = $conn->prepare("
  SELECT tipe_transaksi, hash_tx, tanggal_tx
  FROM tbl_transaksi_blockchain
  WHERE id_umkm = ?
  ORDER BY tanggal_tx ASC
");
$qBC->bind_param("i", $id_umkm);
$qBC->execute();
$blockchain = $qBC->get_result();

/* ===============================
   LABEL STATUS
=============================== */
$statusLabel = [
  'menunggu_rt' => 'Menunggu Verifikasi RT',
  'ditolak_rt'  => 'Ditolak RT',
  'menunggu_rw' => 'Menunggu Verifikasi RW',
  'ditolak_rw'  => 'Ditolak RW',
  'disetujui'   => 'Disetujui'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Riwayat UMKM</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../../assets/styles/admin-styles.css">
</head>

<body>
<div class="wrapper">
<?php include "sidebar.php"; ?>

<main class="content">
<?php include "topbar.php"; ?>

<!-- ================= STATUS ================= -->
<div class="card-box mb-4">
  <h5>Status Pengajuan</h5>
  <span class="badge bg-primary">
    <?= $statusLabel[$umkm['status']] ?? 'Tidak Diketahui' ?>
  </span>
</div>

<!-- ================= DATA WARGA ================= -->
<div class="card-box mb-4">
  <h5><i class="fas fa-user"></i> Data Warga</h5>
  <p><strong>Nama:</strong> <?= htmlspecialchars($umkm['nama_lengkap']) ?></p>
  <p><strong>NIK:</strong> <?= htmlspecialchars($umkm['nik']) ?></p>
  <p><strong>Alamat:</strong> <?= htmlspecialchars($umkm['alamat']) ?></p>
  <p><strong>No HP:</strong> <?= htmlspecialchars($umkm['no_hp']) ?></p>
</div>

<!-- ================= DATA UMKM ================= -->
<div class="card-box mb-4">
  <h5><i class="fas fa-store"></i> Data UMKM</h5>
  <p><strong>Nama Usaha:</strong> <?= htmlspecialchars($umkm['nama_usaha']) ?></p>
  <p><strong>Jenis Usaha:</strong> <?= htmlspecialchars($umkm['jenis_usaha']) ?></p>
  <p><strong>Tahun Mulai:</strong> <?= $umkm['tahun_mulai'] ?: '-' ?></p>
  <p><strong>Jumlah Karyawan:</strong> <?= $umkm['jumlah_karyawan'] ?: '-' ?></p>
  <p><strong>Tanggal Pengajuan:</strong> <?= date('d-m-Y', strtotime($umkm['created_at'])) ?></p>
</div>

<!-- ================= SURAT ================= -->
<?php if ($umkm['nomor_surat']): ?>
<div class="card-box mb-4">
  <h5><i class="fas fa-file-pdf"></i> Surat Pengantar</h5>
  <p><strong>Nomor Surat:</strong> <?= htmlspecialchars($umkm['nomor_surat']) ?></p>
  <p><strong>Tanggal Terbit:</strong> <?= date('d-m-Y', strtotime($umkm['tanggal_terbit'])) ?></p>
  <a href="../../uploads/surat/<?= $umkm['file_surat'] ?>" target="_blank"
     class="btn btn-success btn-sm">
    <i class="fas fa-eye"></i> Lihat Surat
  </a>
</div>
<?php endif; ?>

<!-- ================= DOKUMEN ================= -->
<div class="card-box mb-4">
  <h5><i class="fas fa-folder-open"></i> Dokumen UMKM</h5>
  <div class="row g-3">
<?php if ($dokumen->num_rows > 0): while ($d = $dokumen->fetch_assoc()):
  $ext = strtolower(pathinfo($d['file_path'], PATHINFO_EXTENSION));
  $url = "../../uploads/dokumen_umkm/" . $d['file_path'];
?>
  <div class="col-md-4">
    <div class="card h-100 shadow-sm">
      <div class="card-body text-center">
        <strong><?= htmlspecialchars($d['jenis_dokumen']) ?></strong>
        <div class="mt-2">
          <?php if (in_array($ext,['jpg','jpeg','png'])): ?>
            <img src="<?= $url ?>" class="img-fluid rounded"
                 style="max-height:180px;object-fit:cover">
          <?php else: ?>
            <i class="fas fa-file-pdf fa-4x text-danger"></i>
          <?php endif; ?>
        </div>
        <a href="<?= $url ?>" target="_blank"
           class="btn btn-outline-primary btn-sm mt-3">
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
<div class="card-box mb-4">
  <h5><i class="fas fa-link"></i> Timeline Blockchain</h5>
  <ul class="list-group">
<?php while ($bc = $blockchain->fetch_assoc()): ?>
  <li class="list-group-item">
    <strong><?= strtoupper(str_replace('_',' ', $bc['tipe_transaksi'])) ?></strong><br>
    <small class="text-muted"><?= date('d-m-Y H:i', strtotime($bc['tanggal_tx'])) ?></small>
    <div class="text-break"><code><?= $bc['hash_tx'] ?></code></div>
  </li>
<?php endwhile; ?>
  </ul>
</div>

<a href="riwayat.php" class="btn btn-secondary">
  <i class="fas fa-arrow-left"></i> Kembali
</a>

</main>
</div>

<?php include "footer.php";?>
</body>
</html>
