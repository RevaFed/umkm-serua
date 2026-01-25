<?php
require "auth.php";
require_once "../../config/database.php";

/* ANTI CACHE */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

$rt = $_SESSION['rt'];
$rw = $_SESSION['rw'];

/* =====================
   STATISTIK UMKM RT
===================== */

// Total UMKM wilayah RT
$qTotal = mysqli_query($conn, "
  SELECT COUNT(*) AS total
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE w.rt = '$rt' AND w.rw = '$rw'
");
$total_umkm = mysqli_fetch_assoc($qTotal)['total'];

// Menunggu verifikasi RT
$qMenunggu = mysqli_query($conn, "
  SELECT COUNT(*) AS total
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE w.rt = '$rt' AND w.rw = '$rw'
    AND u.status = 'menunggu_rt'
");
$menunggu_rt = mysqli_fetch_assoc($qMenunggu)['total'];

// Sudah diverifikasi RT (lanjut RW)
$qVerified = mysqli_query($conn, "
  SELECT COUNT(*) AS total
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE w.rt = '$rt' AND w.rw = '$rw'
    AND u.status = 'menunggu_rw'
");
$verified_rt = mysqli_fetch_assoc($qVerified)['total'];

// Ditolak RT
$qRejected = mysqli_query($conn, "
  SELECT COUNT(*) AS total
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE w.rt = '$rt' AND w.rw = '$rw'
    AND u.status = 'ditolak_rt'
");
$ditolak_rt = mysqli_fetch_assoc($qRejected)['total'];

/* =====================
   UMKM MENUNGGU RT
===================== */
$qList = mysqli_query($conn, "
  SELECT 
    u.id_umkm,
    u.nama_usaha,
    u.jenis_usaha,
    u.created_at,
    w.nama_lengkap
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE w.rt = '$rt' AND w.rw = '$rw'
    AND u.status = 'menunggu_rt'
  ORDER BY u.created_at ASC
  LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard RT</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../../assets/styles/admin-styles.css">
</head>

<body>
  <div class="overlay" id="overlay"></div>

<div class="wrapper">
<?php include "sidebar.php"; ?>

<main class="content">
<?php include "topbar.php"; ?>

<!-- ================= STATISTIK ================= -->
<div class="row g-4 mb-4">

  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon bg-primary">
        <i class="fas fa-store"></i>
      </div>
      <div>Total UMKM</div>
      <h3><?= $total_umkm ?></h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon bg-warning">
        <i class="fas fa-clock"></i>
      </div>
      <div>Menunggu RT</div>
      <h3><?= $menunggu_rt ?></h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon bg-success">
        <i class="fas fa-check"></i>
      </div>
      <div>Diverifikasi RT</div>
      <h3><?= $verified_rt ?></h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon bg-danger">
        <i class="fas fa-times"></i>
      </div>
      <div>Ditolak RT</div>
      <h3><?= $ditolak_rt ?></h3>
    </div>
  </div>

</div>

<!-- ================= LIST UMKM MENUNGGU ================= -->
<div class="card-box">
  <h6 class="mb-3">
    <i class="fas fa-file-alt"></i> UMKM Menunggu Verifikasi RT
  </h6>

  <?php if (mysqli_num_rows($qList) > 0): ?>
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
      <thead>
        <tr>
          <th>Nama Usaha</th>
          <th>Jenis</th>
          <th>Warga</th>
          <th>Tanggal</th>
          <th width="120">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($u = mysqli_fetch_assoc($qList)): ?>
        <tr>
          <td><?= htmlspecialchars($u['nama_usaha']) ?></td>
          <td><?= htmlspecialchars($u['jenis_usaha']) ?></td>
          <td><?= htmlspecialchars($u['nama_lengkap']) ?></td>
          <td><?= date('d-m-Y', strtotime($u['created_at'])) ?></td>
          <td>
            <a href="detail_umkm.php?id=<?= $u['id_umkm'] ?>"
               class="btn btn-sm btn-primary">
              <i class="fas fa-eye"></i> Detail
            </a>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <div class="text-muted">Tidak ada UMKM menunggu verifikasi RT.</div>
  <?php endif; ?>
</div>

</main>
</div>

<?php include "footer.php";?>
</body>
</html>
