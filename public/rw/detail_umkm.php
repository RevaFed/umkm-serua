<?php
require "auth.php";
require_once "../../config/database.php";

/* ANTI CACHE */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

$rw = $_SESSION['rw'];

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
    w.nama_lengkap, w.nik, w.alamat, w.no_hp
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE u.id_umkm = ?
    AND u.status = 'menunggu_rw'
    AND w.rw = ?
");
$stmt->bind_param("is", $id_umkm, $rw);
$stmt->execute();
$umkm = $stmt->get_result()->fetch_assoc();

if (!$umkm) {
    header("Location: umkm.php");
    exit;
}

/* ================= DATA ADMIN RW ================= */
$stmt = $conn->prepare("
  SELECT nama, ttd, stempel
  FROM tbl_admin
  WHERE role = 'rw'
    AND rw = ?
  LIMIT 1
");
$stmt->bind_param("s", $rw);
$stmt->execute();
$adminRW = $stmt->get_result()->fetch_assoc();

$ttdLengkap = !empty($adminRW['ttd']) && !empty($adminRW['stempel']);

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
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail UMKM | RW</title>
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
<div class="card-box mb-3">
  <h6>Status Pengajuan</h6>
  <span class="badge bg-warning text-dark">Menunggu Verifikasi RW</span>
  <div class="text-muted mt-1">
    Diverifikasi RT pada:
    <?= date('d-m-Y H:i', strtotime($umkm['approved_rt_at'])) ?>
  </div>
</div>

<!-- ================= DATA WARGA ================= -->
<div class="card-box mb-3">
  <h5><i class="fas fa-user"></i> Data Warga</h5>
  <p><strong>Nama:</strong> <?= htmlspecialchars($umkm['nama_lengkap']) ?></p>
  <p><strong>NIK:</strong> <?= htmlspecialchars($umkm['nik']) ?></p>
  <p><strong>Alamat:</strong> <?= htmlspecialchars($umkm['alamat']) ?></p>
  <p><strong>No HP:</strong> <?= htmlspecialchars($umkm['no_hp']) ?></p>
</div>

<!-- ================= DATA UMKM ================= -->
<div class="card-box mb-3">
  <h5><i class="fas fa-store"></i> Data UMKM</h5>
  <p><strong>Nama Usaha:</strong> <?= htmlspecialchars($umkm['nama_usaha']) ?></p>
  <p><strong>Jenis Usaha:</strong> <?= htmlspecialchars($umkm['jenis_usaha']) ?></p>
  <p><strong>Tahun Mulai:</strong> <?= $umkm['tahun_mulai'] ?: '-' ?></p>
  <p><strong>Jumlah Karyawan:</strong> <?= $umkm['jumlah_karyawan'] ?: '-' ?></p>
</div>

<!-- ================= DOKUMEN ================= -->
<div class="card-box mb-3">
  <h5><i class="fas fa-folder-open"></i> Dokumen</h5>
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
              <img src="<?= $url ?>" class="img-fluid rounded" style="max-height:180px">
            <?php else: ?>
              <i class="fas fa-file-pdf fa-4x text-danger"></i>
            <?php endif; ?>
          </div>
          <a href="<?= $url ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
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

<!-- ================= TTD & STEMPEL RW ================= -->
<div class="card-box mb-3">
  <h5><i class="fas fa-signature"></i> Tanda Tangan & Stempel RW</h5>

  <?php if ($ttdLengkap): ?>
    <div class="row">
      <div class="col-md-6 text-center">
        <p class="mb-1"><strong>Tanda Tangan RW</strong></p>
        <img src="../../uploads/ttd/<?= $adminRW['ttd'] ?>" style="max-height:120px">
      </div>
      <div class="col-md-6 text-center">
        <p class="mb-1"><strong>Stempel RW</strong></p>
        <img src="../../uploads/stempel/<?= $adminRW['stempel'] ?>" style="max-height:120px">
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-warning mb-0">
      <i class="fas fa-exclamation-triangle"></i>
      <strong>TTD & Stempel RW belum diunggah.</strong><br>
      Upload terlebih dahulu di <b>Profil RW</b>.
    </div>
  <?php endif; ?>
</div>

<!-- ================= AKSI RW ================= -->
<div class="card-box">
  <h5><i class="fas fa-gavel"></i> Keputusan RW</h5>

  <form action="../../controlls/rw_verifikasi_proses.php" method="POST">
    <input type="hidden" name="id_umkm" value="<?= $id_umkm ?>">

    <div class="mb-3">
  <label class="form-label">
    Nomor Surat <span class="text-danger">*</span>
  </label>
  <input type="text"
         name="nomor_surat"
         class="form-control"
         placeholder="Contoh: 005/SK-UMKM/RW-02/II/2026"
         required>
  <small class="text-muted">
    Nomor surat diisi manual sesuai arsip RW
  </small>
</div>


    <div class="mb-3">
      <label class="form-label">Catatan (wajib jika ditolak)</label>
      <textarea name="catatan" class="form-control" rows="3"></textarea>
    </div>

    <div class="d-flex gap-2">
      <button name="aksi" value="setujui"
              class="btn btn-success"
              <?= !$ttdLengkap ? 'disabled' : '' ?>
              onclick="return confirm('Setujui dan terbitkan surat UMKM ini?')">
        <i class="fas fa-check"></i> Setujui & Terbitkan Surat
      </button>

      <button name="aksi" value="tolak"
              class="btn btn-danger"
              onclick="return confirm('Tolak UMKM ini?')">
        <i class="fas fa-times"></i> Tolak
      </button>

      <a href="umkm.php" class="btn btn-secondary">Kembali</a>
    </div>
  </form>
</div>

</main>
</div>

<?php include "footer.php"; ?>
</body>
</html>
