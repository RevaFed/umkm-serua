<?php
require "auth.php";
require_once "../../config/database.php";

/* ANTI CACHE */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

$rw = $_SESSION['rw'];

/* =====================
   STATISTIK
===================== */

/* Menunggu verifikasi RW */
$qMenunggu = mysqli_query($conn, "
  SELECT COUNT(*) AS total
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE u.status = 'menunggu_rw'
    AND w.rw = '$rw'
");
$menunggu = mysqli_fetch_assoc($qMenunggu)['total'];

/* Disetujui (surat terbit) */
$qSetuju = mysqli_query($conn, "
  SELECT COUNT(*) AS total
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE u.status = 'disetujui'
    AND w.rw = '$rw'
");
$setuju = mysqli_fetch_assoc($qSetuju)['total'];

/* Ditolak RW */
$qTolak = mysqli_query($conn, "
  SELECT COUNT(*) AS total
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE u.status = 'ditolak_rw'
    AND w.rw = '$rw'
");
$ditolak = mysqli_fetch_assoc($qTolak)['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard RW | UMKM</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../../assets/styles/admin-styles.css">
<link rel="stylesheet" href="../../assets/plugins/datatables/datatables.min.css">
</head>

<body>

<div class="wrapper">
<?php include "sidebar.php"; ?>

<main class="content">
<?php include "topbar.php"; ?>

<!-- ================= STATISTIK ================= -->
<div class="row g-4 mb-4">

  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-icon bg-warning">
        <i class="fas fa-clock"></i>
      </div>
      <div>Menunggu Verifikasi RW</div>
      <h3><?= $menunggu ?></h3>
    </div>
  </div>

  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-icon bg-success">
        <i class="fas fa-check-circle"></i>
      </div>
      <div>Disetujui</div>
      <h3><?= $setuju ?></h3>
    </div>
  </div>

  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-icon bg-danger">
        <i class="fas fa-times-circle"></i>
      </div>
      <div>Ditolak RW</div>
      <h3><?= $ditolak ?></h3>
    </div>
  </div>

</div>

<!-- ================= TABLE ================= -->
<div class="card-box">
  <h6 class="mb-3">
    <i class="fas fa-file-signature"></i>
    Pengajuan UMKM Menunggu RW
  </h6>

  <div class="table-responsive">
    <table id="tableUmkmRW" class="table table-striped table-hover align-middle">
      <thead>
        <tr>
          <th>Nama Usaha</th>
          <th>Jenis Usaha</th>
          <th>Warga</th>
          <th>Tanggal RT</th>
          <th>Status</th>
          <th width="120">Aksi</th>
        </tr>
      </thead>
      <tbody>

<?php
$qLatest = mysqli_query($conn, "
  SELECT 
    u.id_umkm,
    u.nama_usaha,
    u.jenis_usaha,
    u.approved_rt_at,
    w.nama_lengkap
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE u.status = 'menunggu_rw'
    AND w.rw = '$rw'
  ORDER BY u.approved_rt_at DESC
  LIMIT 50
");

while ($row = mysqli_fetch_assoc($qLatest)):
?>
        <tr>
          <td><?= htmlspecialchars($row['nama_usaha']) ?></td>
          <td><?= htmlspecialchars($row['jenis_usaha']) ?></td>
          <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
          <td><?= date('d-m-Y H:i', strtotime($row['approved_rt_at'])) ?></td>
          <td>
            <span class="badge bg-warning text-dark">
              Menunggu RW
            </span>
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

<?php include "footer.php";?>

<script>
$(document).ready(function () {
  $('#tableUmkmRW').DataTable({
    pageLength: 5,
    lengthChange: false,
    ordering: true,
    searching: true,
    info: false,
    language: {
      search: "Cari UMKM:",
      zeroRecords: "Tidak ada UMKM menunggu verifikasi RW",
      paginate: {
        next: "›",
        previous: "‹"
      }
    },
    columnDefs: [
      { orderable: false, targets: [5] }
    ]
  });
});
</script>

</body>
</html>
