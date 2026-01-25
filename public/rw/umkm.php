<?php
require "auth.php";
require_once "../../config/database.php";

/* ANTI CACHE */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

$rw = $_SESSION['rw'];

/* =====================
   DATA UMKM MENUNGGU RW
===================== */
$qUmkm = mysqli_query($conn, "
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
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Verifikasi UMKM | RW</title>
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

<div class="card-box">
  <h5 class="mb-3">
    <i class="fas fa-file-signature"></i>
    Daftar UMKM Menunggu Verifikasi RW
  </h5>

  <div class="table-responsive">
    <table id="tableUmkmRW" class="table table-striped table-hover align-middle">
      <thead>
        <tr>
          <th width="40">#</th>
          <th>Nama Usaha</th>
          <th>Jenis Usaha</th>
          <th>Nama Warga</th>
          <th>Tanggal Verifikasi RT</th>
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
          <td><?= date('d-m-Y H:i', strtotime($row['approved_rt_at'])) ?></td>
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
    pageLength: 10,
    lengthChange: false,
    ordering: true,
    searching: true,
    info: false,
    order: [[4, 'desc']],
    language: {
      search: "Cari UMKM:",
      zeroRecords: "Tidak ada UMKM menunggu verifikasi RW",
      paginate: {
        next: "›",
        previous: "‹"
      }
    },
    columnDefs: [
      { orderable: false, targets: [0,5] }
    ]
  });
});
</script>

</body>
</html>
