<?php
session_start();
require_once "../../config/database.php";

/* ANTI CACHE */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

/* AUTH WARGA */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'warga') {
    header("Location: ../login.php");
    exit;
}

$id_warga = $_SESSION['id_warga'];

/* DATA RIWAYAT UMKM */
$qRiwayat = mysqli_query($conn, "
  SELECT 
    u.id_umkm,
    u.nama_usaha,
    u.jenis_usaha,
    u.created_at,
    l.nomor_surat,
    l.file_surat,
    l.tanggal_terbit
  FROM tbl_umkm u
  LEFT JOIN tbl_legalisasi l ON u.id_umkm = l.id_umkm
  WHERE u.id_warga = '$id_warga'
  ORDER BY u.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Riwayat UMKM</title>
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

<div class="card-box">
  <h5 class="mb-3"><i class="fas fa-history"></i> Riwayat Pengajuan UMKM</h5>

  <div class="table-responsive">
    <table id="tableRiwayatUmkm" class="table table-striped table-hover align-middle">
      <thead>
        <tr>
          <th width="40">#</th>
          <th>Nama Usaha</th>
          <th>Jenis Usaha</th>
          <th>Tgl Pengajuan</th>
          <th>Status</th>
          <th>Keterangan</th>
          <th width="160">Aksi</th>
        </tr>
      </thead>
      <tbody>

<?php
$no = 1;
if (mysqli_num_rows($qRiwayat) > 0):
while ($row = mysqli_fetch_assoc($qRiwayat)):
?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['nama_usaha']) ?></td>
          <td><?= htmlspecialchars($row['jenis_usaha']) ?></td>
          <td><?= date('d-m-Y', strtotime($row['created_at'])) ?></td>
          <td>
            <?php if ($row['nomor_surat']): ?>
              <span class="badge bg-success">Disetujui</span>
            <?php else: ?>
              <span class="badge bg-warning text-dark">Menunggu</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($row['nomor_surat']): ?>
              Surat terbit <?= date('d-m-Y', strtotime($row['tanggal_terbit'])) ?>
            <?php else: ?>
              Dalam proses verifikasi RT/RW
            <?php endif; ?>
          </td>
          <td>
            <a href="detail_umkm.php?id=<?= $row['id_umkm'] ?>"
               class="btn btn-sm btn-primary">
              <i class="fas fa-eye"></i> Detail
            </a>

            <?php if ($row['file_surat']): ?>
              <a href="../../uploads/surat/<?= $row['file_surat'] ?>"
                 target="_blank"
                 class="btn btn-sm btn-success">
                <i class="fas fa-file-pdf"></i> Surat
              </a>
            <?php endif; ?>
          </td>
        </tr>
<?php endwhile; endif; ?>

      </tbody>
    </table>
  </div>
</div>

</main>
</div>

<!-- ================= JS ================= -->
<script src="../../assets/plugins/jquery/jquery.min.js"></script>
<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/plugins/datatables/datatables.min.js"></script>

<script>
$(document).ready(function () {
  $('#tableRiwayatUmkm').DataTable({
    pageLength: 10,
    lengthChange: false,
    ordering: true,
    searching: true,
    info: false,
    order: [[3, 'desc']],
    language: {
      search: "Cari UMKM:",
      zeroRecords: "Belum ada riwayat pengajuan UMKM",
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
