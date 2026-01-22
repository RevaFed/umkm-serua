<?php
session_start();
require_once "../../config/database.php";

/* AUTH ADMIN */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

/* DATA WARGA + JUMLAH UMKM */
$q = mysqli_query($conn, "
  SELECT 
    w.id_warga,
    w.nama_lengkap,
    w.nik,
    w.no_hp,
    w.email,
    COUNT(u.id_umkm) AS total_umkm
  FROM tbl_warga w
  LEFT JOIN tbl_umkm u ON w.id_warga = u.id_warga
  GROUP BY w.id_warga
  ORDER BY w.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Warga</title>
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

<div class="card-box">
  <h5 class="mb-3"><i class="fas fa-users"></i> Data Warga</h5>

  <div class="table-responsive">
    <table id="tableWarga" class="table table-striped table-hover align-middle">
      <thead>
        <tr>
          <th width="40">#</th>
          <th>Nama Lengkap</th>
          <th>NIK</th>
          <th>No HP</th>
          <th>Email</th>
          <th width="80">UMKM</th>
          <th width="120">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php $no=1; while ($w = mysqli_fetch_assoc($q)): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($w['nama_lengkap']) ?></td>
          <td><?= htmlspecialchars($w['nik']) ?></td>
          <td><?= htmlspecialchars($w['no_hp']) ?></td>
          <td><?= htmlspecialchars($w['email']) ?></td>
          <td>
            <span class="badge bg-primary"><?= $w['total_umkm'] ?></span>
          </td>
          <td>
            <a href="detail_warga.php?id=<?= $w['id_warga'] ?>"
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
  $('#tableWarga').DataTable({
    pageLength: 10,
    lengthChange: false,
    ordering: true,
    searching: true,
    info: false,
    language: {
      search: "Cari Warga:",
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

</body>
</html>
