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
   RIWAYAT VERIFIKASI RT
===================== */
$qRiwayat = mysqli_query($conn, "
  SELECT 
    u.id_umkm,
    u.nama_usaha,
    u.jenis_usaha,
    u.status,
    u.approved_rt_at,
    u.catatan_penolakan,
    w.nama_lengkap
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE w.rt = '$rt'
    AND w.rw = '$rw'
    AND u.status IN ('menunggu_rw','ditolak_rt')
  ORDER BY u.approved_rt_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Riwayat Verifikasi RT</title>
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
  <h5 class="mb-3">
    <i class="fas fa-history"></i> Riwayat Verifikasi RT
  </h5>

  <div class="table-responsive">
    <table id="tableRiwayatRT" class="table table-striped table-hover align-middle">
      <thead>
        <tr>
          <th width="40">#</th>
          <th>Nama Usaha</th>
          <th>Jenis</th>
          <th>Warga</th>
          <th>Status</th>
          <th>Tanggal Verifikasi</th>
          <th>Catatan</th>
          <th width="120">Aksi</th>
        </tr>
      </thead>
     <tbody>
<?php
$no = 1;
while ($row = mysqli_fetch_assoc($qRiwayat)):
?>
<tr>
  <td><?= $no++ ?></td>
  <td><?= htmlspecialchars($row['nama_usaha']) ?></td>
  <td><?= htmlspecialchars($row['jenis_usaha']) ?></td>
  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
  <td>
    <?php if ($row['status'] === 'menunggu_rw'): ?>
      <span class="badge bg-success">Disetujui RT</span>
    <?php else: ?>
      <span class="badge bg-danger">Ditolak RT</span>
    <?php endif; ?>
  </td>
  <td><?= date('d-m-Y H:i', strtotime($row['approved_rt_at'])) ?></td>
  <td>
    <?= $row['catatan_penolakan']
      ? htmlspecialchars($row['catatan_penolakan'])
      : '<span class="text-muted">-</span>' ?>
  </td>
  <td>
    <a href="detail_umkm_riwayat.php?id=<?= $row['id_umkm'] ?>"
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
  $('#tableRiwayatRT').DataTable({
    pageLength: 10,
    lengthChange: false,
    ordering: true,
    searching: true,
    info: false,
    order: [[5, 'desc']],
    language: {
      search: "Cari UMKM:",
      zeroRecords: "Belum ada riwayat verifikasi",
      paginate: {
        next: "›",
        previous: "‹"
      }
    },
    columnDefs: [
      { orderable: false, targets: [0,6,7] }
    ]
  });
});
</script>

</body>
</html>
