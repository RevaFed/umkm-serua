<?php
require_once "config/database.php";

/* DATA BLOCKCHAIN TERAKHIR PER UMKM */
$q = mysqli_query($conn, "
  SELECT 
    w.nama_lengkap,
    u.nama_usaha,
    tb.tipe_transaksi,
    tb.hash_tx,
    tb.tanggal_tx,
    u.id_umkm
  FROM tbl_transaksi_blockchain tb
  JOIN tbl_umkm u ON tb.id_umkm = u.id_umkm
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE tb.id_tx IN (
    SELECT MAX(id_tx)
    FROM tbl_transaksi_blockchain
    GROUP BY id_umkm
  )
  ORDER BY tb.tanggal_tx DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Blockchain | Kelurahan Serua</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- BOOTSTRAP -->
<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
<!-- FONT AWESOME -->
<link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
<!-- DATATABLES -->
<link rel="stylesheet" href="assets/datatables/datatables.min.css">

<style>
/* ===== GLOBAL ===== */
html, body {
  height: 100%;
}

body {
  background: #f4f6f9;
  display: flex;
  flex-direction: column;
}

/* ===== HEADER ===== */
.navbar {
  margin-bottom: 40px; /* 🔥 JARAK HEADER KE KONTEN */
}

/* ===== MAIN CONTENT ===== */
.main-content {
  flex: 1;
  animation: fadeUp .6s ease;
}

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(20px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ===== TITLE ===== */
.page-title {
  font-weight: 700;
}

/* ===== HASH STYLE ===== */
.hash-mini {
  font-family: monospace;
  font-size: 12px;
  background: #eef1f4;
  padding: 6px 10px;
  border-radius: 6px;
  display: inline-block;
}

/* ===== TABLE EFFECT ===== */
tbody tr {
  transition: all .25s ease;
}
tbody tr:hover {
  background-color: #eef4ff;
}

/* ===== FOOTER ===== */
footer {
  background: #031633;
  color: #bfc7d5;
}
</style>
</head>

<body>

<!-- ================= HEADER ================= -->
<nav class="navbar navbar-dark bg-primary shadow-sm">
  <div class="container">
    <span class="navbar-brand fw-semibold">
      <i class="fas fa-link"></i> Daftar Transaksi Blockchain
    </span>
  </div>
</nav>

<!-- ================= MAIN ================= -->
<div class="container main-content">

  <!-- TITLE -->
  <div class="mb-4">
    <h4 class="page-title mb-1">Log Blockchain UMKM</h4>
    <p class="text-muted mb-0">
      Transparansi proses perizinan UMKM berbasis blockchain
      di Kelurahan Serua.
    </p>
  </div>

  <!-- INFO DOWNLOAD SURAT -->
<div class="alert alert-info d-flex align-items-start mb-4" role="alert">
  <i class="fas fa-info-circle fa-lg me-3 mt-1"></i>
  <div>
    <strong>Informasi:</strong><br>
    Surat legalitas / surat pengantar yang telah terbit
    <strong>dapat diunduh melalui akun masing-masing pemilik UMKM</strong>.
  </div>
</div>

  <!-- TABLE -->
  <div class="card shadow-sm">
    <div class="card-body">

      <div class="table-responsive">
        <table id="tableBlockchain"
               class="table table-bordered table-striped align-middle mb-0">
          <thead class="table-dark text-center">
            <tr>
              <th width="50">No</th>
              <th>Nama Warga</th>
              <th>Nama Usaha</th>
              <th>Status</th>
              <th>Hash Transaksi Terakhir</th>
              <th width="80">Aksi</th>
            </tr>
          </thead>
          <tbody>

<?php $no=1; while ($row = mysqli_fetch_assoc($q)): ?>
<tr>
  <td class="text-center"><?= $no++ ?></td>
  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
  <td><?= htmlspecialchars($row['nama_usaha']) ?></td>
  <td class="text-center">
    <?php if ($row['tipe_transaksi'] === 'surat_pengantar_terbit'): ?>
      <span class="badge bg-success">Legalitas Terbit</span>
    <?php elseif ($row['tipe_transaksi'] === 'verifikasi_rt_rw'): ?>
      <span class="badge bg-warning text-dark">Verifikasi RT/RW</span>
    <?php else: ?>
      <span class="badge bg-secondary">Pengajuan</span>
    <?php endif; ?>
  </td>
  <td>
    <span class="hash-mini">
      <?= substr($row['hash_tx'], 0, 28) ?>...
    </span>
  </td>
  <td class="text-center">
    <a href="blockchain_detail.php?id=<?= $row['id_umkm'] ?>"
       class="btn btn-sm btn-primary">
      <i class="fas fa-eye"></i>
    </a>
  </td>
</tr>
<?php endwhile; ?>

          </tbody>
        </table>
      </div>

    </div>
  </div>

</div>

<!-- ================= FOOTER ================= -->
<footer class="py-3 mt-5">
  <div class="container text-center">
    <small>
      © 2026 Sistem Perizinan UMKM <br>
      Kelurahan Serua – Kecamatan Ciputat
    </small>
  </div>
</footer>

<!-- ================= JS ================= -->
<script src="assets/jquery/jquery.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/datatables/datatables.min.js"></script>

<script>
$(document).ready(function () {
  $('#tableBlockchain').DataTable({
    pageLength: 10,
    ordering: true,
    searching: true,
    lengthChange: false,
    info: false,
    language: {
      search: "Cari:",
      zeroRecords: "Data tidak ditemukan",
      paginate: { next: "›", previous: "‹" }
    },
    columnDefs: [
      { orderable: false, targets: [0,5] }
    ]
  });
});
</script>

</body>
</html>
