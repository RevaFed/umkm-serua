<?php
require "auth.php";
require_once "../../config/database.php";

/* ANTI CACHE */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

/* =====================
   STATISTIK UMKM
===================== */

// Total UMKM
$qTotal = mysqli_query($conn, "
  SELECT COUNT(*) AS total 
  FROM tbl_umkm
");
$total_umkm = mysqli_fetch_assoc($qTotal)['total'];

// Menunggu RT
$qWaitRT = mysqli_query($conn, "
  SELECT COUNT(*) AS total 
  FROM tbl_umkm 
  WHERE status = 'menunggu_rt'
");
$menunggu_rt = mysqli_fetch_assoc($qWaitRT)['total'];

// Menunggu RW
$qWaitRW = mysqli_query($conn, "
  SELECT COUNT(*) AS total 
  FROM tbl_umkm 
  WHERE status = 'menunggu_rw'
");
$menunggu_rw = mysqli_fetch_assoc($qWaitRW)['total'];

// Disetujui
$qApproved = mysqli_query($conn, "
  SELECT COUNT(*) AS total 
  FROM tbl_umkm 
  WHERE status = 'disetujui'
");
$approved = mysqli_fetch_assoc($qApproved)['total'];

// Ditolak
$qRejected = mysqli_query($conn, "
  SELECT COUNT(*) AS total 
  FROM tbl_umkm 
  WHERE status IN ('ditolak_rt','ditolak_rw')
");
$rejected = mysqli_fetch_assoc($qRejected)['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin | UMKM</title>
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

<!-- ================= STATISTIC ================= -->
<div class="row g-4 mb-4">

  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon bg-primary"><i class="fas fa-store"></i></div>
      <div>Total UMKM</div>
      <h3><?= $total_umkm ?></h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon bg-warning"><i class="fas fa-user-clock"></i></div>
      <div>Menunggu RT</div>
      <h3><?= $menunggu_rt ?></h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon bg-info"><i class="fas fa-users"></i></div>
      <div>Menunggu RW</div>
      <h3><?= $menunggu_rw ?></h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon bg-success"><i class="fas fa-check"></i></div>
      <div>Disetujui</div>
      <h3><?= $approved ?></h3>
    </div>
  </div>

</div>

<div class="row g-4 mb-4">
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon bg-danger"><i class="fas fa-times"></i></div>
      <div>Ditolak</div>
      <h3><?= $rejected ?></h3>
    </div>
  </div>
</div>

<!-- ================= TABLE ================= -->
<div class="table-box">
  <h6 class="mb-3">Pengajuan UMKM Terbaru</h6>

  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
      <thead>
        <tr>
          <th>Nama Usaha</th>
          <th>Jenis Usaha</th>
          <th>Tanggal</th>
          <th>Status</th>
          <th width="120">Aksi</th>
        </tr>
      </thead>
      <tbody>

<?php
$qLatest = mysqli_query($conn, "
  SELECT id_umkm, nama_usaha, jenis_usaha, created_at, status
  FROM tbl_umkm
  ORDER BY created_at DESC
  LIMIT 10
");

while ($row = mysqli_fetch_assoc($qLatest)):
?>
        <tr>
          <td><?= htmlspecialchars($row['nama_usaha']) ?></td>
          <td><?= htmlspecialchars($row['jenis_usaha']) ?></td>
          <td><?= date('d-m-Y', strtotime($row['created_at'])) ?></td>
          <td>
            <?php
            switch ($row['status']) {
              case 'menunggu_rt':
                echo '<span class="badge bg-warning text-dark">Menunggu RT</span>';
                break;
              case 'menunggu_rw':
                echo '<span class="badge bg-info text-dark">Menunggu RW</span>';
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
          </td>
          <td>
            <a href="detail_umkm.php?id=<?= $row['id_umkm'] ?>" 
               class="btn btn-sm btn-primary">
              <i class="fas fa-eye"></i> Detail
            </a>
          </td>
        </tr>
<?php endwhile; ?>

      </tbody>
    </table>
  </div>
</div>

</main>
</div>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<?php include "footer.php"; ?>
</body>
</html>
