
<?php
session_start();
require_once "../../config/database.php";

/* ANTI CACHE */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

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

$id_umkm = (int) $_GET['id'];

/* ================= DATA UMKM + WARGA ================= */
$qUmkm = mysqli_query($conn, "
  SELECT 
    u.*,
    w.nama_lengkap, w.nik, w.alamat, w.no_hp, w.email,
    l.nomor_surat, l.file_surat, l.tanggal_terbit, l.blockchain_tx
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  LEFT JOIN tbl_legalisasi l ON u.id_umkm = l.id_umkm
  WHERE u.id_umkm = '$id_umkm'
");

if (mysqli_num_rows($qUmkm) === 0) {
    header("Location: umkm.php");
    exit;
}

$umkm = mysqli_fetch_assoc($qUmkm);

/* ================= DOKUMEN ================= */
$qDok = mysqli_query($conn, "
  SELECT jenis_dokumen, file_path, created_at
  FROM tbl_dokumen_umkm
  WHERE id_umkm = '$id_umkm'
  ORDER BY created_at ASC
");

/* ================= BLOCKCHAIN ================= */
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
<title>Detail UMKM | Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../../assets/styles/admin-styles.css">
<link rel="stylesheet" href="../../assets/styles/admin_detail_umkm.css">
</head>
<body>
 <div class="overlay" id="overlay"></div>
<div class="wrapper">
<?php include "sidebar.php"; ?>

<main class="content">
<?php include "topbar.php"; ?>

<!-- ================= DATA WARGA ================= -->
<div class="card-box mb-4">
  <h5><i class="fas fa-user"></i> Data Warga</h5>

  <div class="row g-4">
    <div class="col-md-6">
      <div class="info-label">Nama Lengkap</div>
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

    <div class="col-md-6">
      <div class="info-label">No HP</div>
      <div class="info-value"><?= htmlspecialchars($umkm['no_hp']) ?></div>
    </div>

    <div class="col-md-6">
      <div class="info-label">Email</div>
      <div class="info-value"><?= htmlspecialchars($umkm['email']) ?></div>
    </div>
  </div>
</div>


<!-- ================= DATA UMKM ================= -->
<div class="card-box mb-4">
  <h5><i class="fas fa-store"></i> Data UMKM</h5>

  <div class="row g-4">
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

  <hr>

  <?php if ($umkm['nomor_surat']): ?>
    <span class="badge bg-success">Disetujui</span>
    <div class="mt-3">
      <div class="info-label">Nomor Surat</div>
      <div class="info-value"><?= htmlspecialchars($umkm['nomor_surat']) ?></div>

      <div class="info-label mt-2">Tanggal Terbit</div>
      <div class="info-value"><?= date('d-m-Y', strtotime($umkm['tanggal_terbit'])) ?></div>

      <a href="../../uploads/surat/<?= $umkm['file_surat'] ?>" target="_blank"
         class="btn btn-success btn-sm mt-3">
        <i class="fas fa-file-pdf"></i> Lihat Surat
      </a>
    </div>
  <?php else: ?>
    <span class="badge bg-warning text-dark">Menunggu Verifikasi</span>
  <?php endif; ?>
</div>


<!-- ================= DOKUMEN ================= -->
<div class="card-box mb-4">
  <h5><i class="fas fa-folder-open"></i> Dokumen</h5>

  <div class="row g-3">
  <?php if (mysqli_num_rows($qDok) > 0): ?>
    <?php while ($d = mysqli_fetch_assoc($qDok)): 
      $ext = strtolower(pathinfo($d['file_path'], PATHINFO_EXTENSION));
      $url = "../../uploads/dokumen_umkm/" . $d['file_path'];
    ?>
      <div class="col-md-4">
        <div class="card doc-card h-100">
          <div class="card-body text-center">

            <strong><?= htmlspecialchars($d['jenis_dokumen']) ?></strong>

            <div class="mt-3">
              <?php if (in_array($ext, ['jpg','jpeg','png'])): ?>
                <img src="<?= $url ?>" 
                     class="img-fluid rounded"
                     style="max-height:180px;object-fit:cover">
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
    <?php endwhile; ?>
  <?php else: ?>
    <div class="col-12 text-muted text-center">
      Tidak ada dokumen UMKM
    </div>
  <?php endif; ?>
  </div>
</div>



<!-- ================= BLOCKCHAIN ================= -->
<div class="card-box mb-4">
  <h5><i class="fas fa-link"></i> Timeline Blockchain</h5>

  <ul class="list-group timeline">
    <?php while ($bc = mysqli_fetch_assoc($qBC)): ?>
      <li class="list-group-item">
        <strong><?= strtoupper(str_replace('_',' ', $bc['tipe_transaksi'])) ?></strong><br>
        <small class="text-muted"><?= date('d-m-Y H:i', strtotime($bc['tanggal_tx'])) ?></small>
        <div><code><?= $bc['hash_tx'] ?></code></div>
      </li>
    <?php endwhile; ?>
  </ul>
</div>


<!-- ================= ACTION ================= -->
<?php if (!$umkm['nomor_surat']): ?>
<div class="card-box">
  <h5><i class="fas fa-check-circle"></i> Aksi Admin</h5>
  <a href="verifikasi_umkm.php?id=<?= $id_umkm ?>" class="btn btn-success">
    <i class="fas fa-check"></i> Verifikasi & Terbitkan Surat
  </a>
</div>
<?php endif; ?>

</main>
</div>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<?php include "footer.php"?>
</body>
</html>
