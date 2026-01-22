<?php
session_start();
require_once "../../config/database.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

$id_login = $_SESSION['id'];

$q = mysqli_query($conn, "
  SELECT id_admin, nama, username, jabatan, no_hp, created_at
  FROM tbl_admin
  ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Manajemen Admin</title>
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

<!-- ALERT -->
<?php if (isset($_SESSION['alert'])): ?>
  <?php if ($_SESSION['alert'] === 'reset_password_ok'): ?>
    <div class="alert alert-success alert-dismissible fade show">
      Password admin berhasil direset.
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($_SESSION['alert'] === 'password_tidak_sama'): ?>
    <div class="alert alert-danger alert-dismissible fade show">
      Password tidak sama.
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($_SESSION['alert'] === 'hapus_ok'): ?>
    <div class="alert alert-success alert-dismissible fade show">
      Admin berhasil dihapus.
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($_SESSION['alert'] === 'hapus_gagal'): ?>
    <div class="alert alert-danger alert-dismissible fade show">
      Tidak boleh menghapus akun sendiri.
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
<?php unset($_SESSION['alert']); endif; ?>

<div class="card-box">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5><i class="fas fa-user-shield"></i> Manajemen Admin</h5>
    <a href="admin_tambah.php" class="btn btn-primary btn-sm">
      <i class="fas fa-plus"></i> Tambah Admin
    </a>
  </div>

  <div class="table-responsive">
    <table id="tableAdmin" class="table table-striped table-hover align-middle">
      <thead>
        <tr>
          <th width="40">#</th>
          <th>Nama</th>
          <th>Username</th>
          <th>Jabatan</th>
          <th>No HP</th>
          <th>Tanggal</th>
          <th width="130">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php $no=1; while ($a = mysqli_fetch_assoc($q)): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($a['nama']) ?></td>
          <td><?= htmlspecialchars($a['username']) ?></td>
          <td><?= htmlspecialchars($a['jabatan']) ?></td>
          <td><?= htmlspecialchars($a['no_hp']) ?></td>
          <td><?= date('d-m-Y', strtotime($a['created_at'])) ?></td>
          <td>
            <a href="admin_edit.php?id=<?= $a['id_admin'] ?>" 
               class="btn btn-sm btn-warning">
              <i class="fas fa-edit"></i>
            </a>

            <a href="admin_reset_password.php?id=<?= $a['id_admin'] ?>" 
               class="btn btn-sm btn-secondary">
              <i class="fas fa-key"></i>
            </a>

            <?php if ($a['id_admin'] != $id_login): ?>
            <a href="../../controlls/admin_hapus.php?id=<?= $a['id_admin'] ?>"
               onclick="return confirm('Hapus admin ini?')"
               class="btn btn-sm btn-danger">
              <i class="fas fa-trash"></i>
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
<?php include "footer.php"?>
<script>
$(document).ready(function () {
  $('#tableAdmin').DataTable({
    pageLength: 10,
    lengthChange: false,
    ordering: true,
    searching: true,
    info: false,
    language: {
      search: "Cari Admin:",
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
