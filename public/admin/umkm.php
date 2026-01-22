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

/* DATA UMKM */
$qUmkm = mysqli_query($conn, "
  SELECT 
    u.id_umkm,
    u.nama_usaha,
    u.jenis_usaha,
    u.created_at,
    w.nama_lengkap,
    IF(l.id_umkm IS NULL, 'Menunggu', 'Disetujui') AS status
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  LEFT JOIN tbl_legalisasi l ON u.id_umkm = l.id_umkm
  ORDER BY u.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data UMKM | Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../../assets/styles/admin-styles.css">
<link rel="stylesheet" href="../../assets/plugins/datatables/datatables.min.css">
</head>

<body>
 <div class="overlay" id="overlay"></div>
<div class="wrapper">
<?php include "sidebar.php"; ?>

<main class="content">
<?php include "topbar.php"; ?>

<div class="table-box">
  <h5 class="mb-3">Data Pengajuan UMKM</h5>

  <div class="table-responsive">
    <table id="tableUmkm" class="table table-striped table-hover align-middle">
      <thead>
        <tr>
          <th width="40">#</th>
          <th>Nama Usaha</th>
          <th>Jenis Usaha</th>
          <th>Nama Warga</th>
          <th>Tanggal</th>
          <th>Status</th>
          <th width="120">Aksi</th>
        </tr>
      </thead>
      <tbody>

<?php
$no = 1;
while ($row = mysqli_fetch_assoc($qUmkm)):
?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['nama_usaha']) ?></td>
          <td><?= htmlspecialchars($row['jenis_usaha']) ?></td>
          <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
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
    pageLength: 10,
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
    },
    columnDefs: [
      { orderable: false, targets: [0,6] }
    ]
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
