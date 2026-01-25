<?php
require "auth.php";
require_once "../../config/database.php";

/* ANTI CACHE */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

$id_warga = $_SESSION['id_warga'];

/* DATA UMKM WARGA */
$qUmkm = mysqli_query($conn, "
  SELECT 
    id_umkm,
    nama_usaha,
    jenis_usaha,
    created_at,
    status
  FROM tbl_umkm
  WHERE id_warga = '$id_warga'
  ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Status UMKM</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../../assets/styles/warga.css">
<link rel="stylesheet" href="../../assets/plugins/datatables/datatables.min.css">
</head>

<body>

<div class="wrapper">
<?php include "sidebar.php"; ?>

<main class="content">
<?php include "topbar.php"; ?>

<?php if (isset($_SESSION['alert']) && $_SESSION['alert'] === 'sukses'): ?>
  <div class="alert alert-success alert-dismissible fade show">
    <strong>Berhasil!</strong> Pengajuan UMKM berhasil dikirim.
    <button class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php unset($_SESSION['alert']); endif; ?>

<div class="card-box">
  <h5 class="mb-3">
    <i class="fas fa-search"></i> Status Pengajuan UMKM
  </h5>

  <div class="table-responsive">
    <table id="tableUmkmWarga" class="table table-striped table-hover align-middle">
      <thead>
        <tr>
          <th width="40">#</th>
          <th>Nama Usaha</th>
          <th>Jenis Usaha</th>
          <th>Tanggal Pengajuan</th>
          <th>Status</th>
          <th width="160">Aksi</th>
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
               class="btn btn-sm btn-primary mb-2">
              <i class="fas fa-eye"></i> Detail
            </a>

            <?php if ($row['status'] === 'disetujui'): ?>
              <a href="../../uploads/surat/<?= $row['id_umkm'] ?>.pdf"
                 target="_blank"
                 class="btn btn-sm btn-success ms-1">
                <i class="fas fa-file-pdf"></i> Surat
              </a>
            <?php endif; ?>
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
  $('#tableUmkmWarga').DataTable({
    pageLength: 10,
    lengthChange: false,
    ordering: true,
    searching: true,
    info: false,
    language: {
      search: "Cari UMKM:",
      zeroRecords: "Belum ada pengajuan UMKM",
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

<!-- ANTI BACK CACHE -->
<script>
window.addEventListener("pageshow", function (event) {
  if (event.persisted) window.location.reload();
});
</script>

</body>
</html>
