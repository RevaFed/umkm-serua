<?php
require "auth.php";
require_once "../../config/database.php";

/* ANTI CACHE */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

/* VALIDASI ID */
$id_umkm = (int)($_GET['id'] ?? 0);
if ($id_umkm === 0) {
    header("Location: umkm.php");
    exit;
}

/* ================= DATA UMKM + WARGA ================= */
$stmt = $conn->prepare("
  SELECT 
    u.*,
    w.nama_lengkap, w.nik, w.alamat, w.no_hp, w.email,
    l.nomor_surat, l.file_surat, l.tanggal_terbit, l.blockchain_tx
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  LEFT JOIN tbl_legalisasi l ON u.id_umkm = l.id_umkm
  WHERE u.id_umkm = ?
");
$stmt->bind_param("i", $id_umkm);
$stmt->execute();
$umkm = $stmt->get_result()->fetch_assoc();

if (!$umkm) {
    header("Location: umkm.php");
    exit;
}

/* ================= DOKUMEN ================= */
$stmt = $conn->prepare("
  SELECT jenis_dokumen, file_path, created_at
  FROM tbl_dokumen_umkm
  WHERE id_umkm = ?
  ORDER BY created_at ASC
");
$stmt->bind_param("i", $id_umkm);
$stmt->execute();
$qDok = $stmt->get_result();

/* ================= BLOCKCHAIN ================= */
$stmt = $conn->prepare("
  SELECT tipe_transaksi, hash_tx, tanggal_tx
  FROM tbl_transaksi_blockchain
  WHERE id_umkm = ?
  ORDER BY tanggal_tx ASC
");
$stmt->bind_param("i", $id_umkm);
$stmt->execute();
$qBC = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail UMKM | Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../../assets/styles/admin-styles.css">
<link rel="stylesheet" href="../../assets/styles/admin_detail_umkm.css">
</head>
<body>

<div class="wrapper">
<?php include "sidebar.php"; ?>

<main class="content">
<?php include "topbar.php"; ?>

<!-- ================= STATUS ================= -->
<div class="card-box mb-4">
  <h5>Status Pengajuan</h5>
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

<!-- ================= DATA WARGA ================= -->
<div class="card-box mb-4">
  <h5><i class="fas fa-user"></i> Data Warga</h5>
  <p><strong>Nama:</strong> <?= htmlspecialchars($umkm['nama_lengkap']) ?></p>
  <p><strong>NIK:</strong> <?= htmlspecialchars($umkm['nik']) ?></p>
  <p><strong>Alamat:</strong> <?= htmlspecialchars($umkm['alamat']) ?></p>
  <p><strong>No HP:</strong> <?= htmlspecialchars($umkm['no_hp']) ?></p>
  <p><strong>Email:</strong> <?= htmlspecialchars($umkm['email']) ?></p>
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

<!-- ================= SURAT (JIKA ADA) ================= -->
<?php if ($umkm['status'] === 'disetujui' && $umkm['nomor_surat']): ?>
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
  <?php if ($qDok->num_rows > 0): while ($d = $qDok->fetch_assoc()): 
    $file = $d['file_path'];
    $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $url  = "../../uploads/dokumen_umkm/" . $file;
  ?>
    <div class="col-md-4">
      <div class="card h-100 shadow-sm">
        <div class="card-body text-center">

          <strong><?= htmlspecialchars($d['jenis_dokumen']) ?></strong>

          <div class="mt-3">
            <?php if (in_array($ext, ['jpg','jpeg','png','webp'])): ?>
              <img src="<?= $url ?>" 
                   class="img-fluid rounded"
                   style="max-height:180px;object-fit:cover;">
            <?php else: ?>
              <i class="fas fa-file-pdf fa-4x text-danger"></i>
            <?php endif; ?>
          </div>

          <a href="<?= $url ?>" target="_blank"
             class="btn btn-outline-primary btn-sm mt-3">
            <i class="fas fa-eye"></i> Lihat Dokumen
          </a>

        </div>
      </div>
    </div>
  <?php endwhile; else: ?>
    <div class="col-12 text-muted text-center">
      Tidak ada dokumen UMKM
    </div>
  <?php endif; ?>
  </div>
</div>

<!-- ================= BLOCKCHAIN ================= -->
<div class="card-box mb-4">
  <h5><i class="fas fa-link"></i> Timeline Blockchain</h5>
  <ul class="list-group">
    <?php while ($bc = $qBC->fetch_assoc()): ?>
      <li class="list-group-item">
        <strong><?= strtoupper(str_replace('_',' ', $bc['tipe_transaksi'])) ?></strong><br>
        <small><?= date('d-m-Y H:i', strtotime($bc['tanggal_tx'])) ?></small>
        <div><code><?= $bc['hash_tx'] ?></code></div>
      </li>
    <?php endwhile; ?>
  </ul>
</div>

</main>
</div>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<?php include "footer.php"; ?>
</body>
</html>
