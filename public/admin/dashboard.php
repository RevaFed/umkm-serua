<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

require_once "../../config/database.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

/* =====================
   STATISTIK
===================== */

// Total UMKM
$qTotal = mysqli_query($conn, "SELECT COUNT(*) total FROM tbl_umkm");
$total_umkm = mysqli_fetch_assoc($qTotal)['total'];

// Menunggu verifikasi
$qPending = mysqli_query($conn, "
  SELECT COUNT(*) total
  FROM tbl_umkm u
  LEFT JOIN tbl_legalisasi l ON u.id_umkm = l.id_umkm
  WHERE l.id_umkm IS NULL
");
$pending = mysqli_fetch_assoc($qPending)['total'];

// Disetujui
$qApproved = mysqli_query($conn, "SELECT COUNT(*) total FROM tbl_legalisasi");
$approved = mysqli_fetch_assoc($qApproved)['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin | UMKM</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../../assets/plugins/datatables/datatables.min.css">
<link rel="stylesheet" href="../../assets/styles/admin-styles.css">
</head>

<body>
  <div class="overlay" id="overlay"></div>

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
      <div class="stat-icon bg-warning"><i class="fas fa-clock"></i></div>
      <div>Menunggu Verifikasi</div>
      <h3><?= $pending ?></h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon bg-success"><i class="fas fa-check"></i></div>
      <div>Disetujui</div>
      <h3><?= $approved ?></h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon bg-danger"><i class="fas fa-times"></i></div>
      <div>Ditolak</div>
      <h3>0</h3>
    </div>
  </div>

</div>

<!-- ================= TABLE ================= -->
<div class="table-box">
  <h6 class="mb-3">Pengajuan UMKM Terbaru</h6>

  <div class="table-responsive">
    <table id="tableUmkm" class="table table-striped table-hover align-middle">
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
  SELECT u.*, 
         IF(l.id_umkm IS NULL, 'Menunggu', 'Disetujui') AS status
  FROM tbl_umkm u
  LEFT JOIN tbl_legalisasi l ON u.id_umkm = l.id_umkm
  ORDER BY u.created_at DESC
  LIMIT 50
");

while ($row = mysqli_fetch_assoc($qLatest)):
?>
        <tr>
          <td><?= htmlspecialchars($row['nama_usaha']) ?></td>
          <td><?= htmlspecialchars($row['jenis_usaha']) ?></td>
          <td><?= date('d-m-Y', strtotime($row['created_at'])) ?></td>
          <td>
            <?php if ($row['status'] === 'Disetujui'): ?>
              <span class="badge bg-success">Disetujui</span>
            <?php else: ?>
              <span class="badge bg-warning text-dark">Menunggu</span>
            <?php endif; ?>
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

<?php include "footer.php"?>
<script>
$(document).ready(function () {
  $('#tableUmkm').DataTable({
    pageLength: 5,
    lengthChange: false,
    ordering: true,
    searching: true,
    info: false,
    language: {
      search: "Cari UMKM:",
      zeroRecords: "Data tidak ditemukan",
      paginate: {
        next: "›",
        previous: "‹"
      }
    }
  });
});
</script>

<!-- ANTI BACK CACHE -->
<script>
window.addEventListener("pageshow", function (event) {
  if (event.persisted) window.location.reload();
});
</script>

</body>
</html>
