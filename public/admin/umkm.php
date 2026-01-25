<?php
require "auth.php";
require_once "../../config/database.php";

/* ANTI CACHE */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

/* DATA UMKM (ADMIN = MONITORING) */
$qUmkm = mysqli_query($conn, "
  SELECT 
    u.id_umkm,
    u.nama_usaha,
    u.jenis_usaha,
    u.created_at,
    u.status,
    w.nama_lengkap
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
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
<div class="wrapper">

<?php include "sidebar.php"; ?>

<main class="content">
<?php include "topbar.php"; ?>

<div class="table-box">
  <h5 class="mb-3">
    <i class="fas fa-store"></i> Data Pengajuan UMKM
  </h5>

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
              default:
                echo '<span class="badge bg-secondary">Tidak Diketahui</span>';
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

<?php include "footer.php"; ?>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/plugins/datatables/datatables.min.js"></script>

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
