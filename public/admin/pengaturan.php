<?php
session_start();
require_once "../../config/database.php";

/* AUTH ADMIN */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

$id_admin = $_SESSION['id'];

/* AMBIL DATA ADMIN */
$q = mysqli_query($conn, "SELECT * FROM tbl_admin WHERE id_admin='$id_admin'");
$admin = mysqli_fetch_assoc($q);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pengaturan Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../../assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../../assets/styles/admin-styles.css">
</head>
<body>
   <div class="overlay" id="overlay"></div>

<div class="wrapper">
<?php include "sidebar.php"; ?>

<main class="content">
<?php include "topbar.php"; ?>

<h5 class="mb-3"><i class="fas fa-cog"></i> Pengaturan Admin</h5>

<!-- ALERT -->
<?php if (isset($_SESSION['alert'])): ?>
  <?php if ($_SESSION['alert'] === 'profil_ok'): ?>
    <div class="alert alert-success alert-dismissible fade show">
      Profil berhasil diperbarui.
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($_SESSION['alert'] === 'password_ok'): ?>
    <div class="alert alert-success alert-dismissible fade show">
      Password berhasil diubah.
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($_SESSION['alert'] === 'password_salah'): ?>
    <div class="alert alert-danger alert-dismissible fade show">
      Password lama salah.
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
<?php unset($_SESSION['alert']); endif; ?>

<!-- TAB -->
<ul class="nav nav-tabs mb-3">
  <li class="nav-item">
    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profil">
      Profil Admin
    </button>
  </li>
  <li class="nav-item">
    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#password">
      Ubah Password
    </button>
  </li>
</ul>

<div class="tab-content">

<!-- ================= PROFIL ================= -->
<div class="tab-pane fade show active" id="profil">
<div class="card-box">
<form action="proses_pengaturan.php" method="POST">
<input type="hidden" name="aksi" value="profil">

<div class="mb-3">
  <label class="form-label">Nama</label>
  <input type="text" name="nama" class="form-control"
         value="<?= htmlspecialchars($admin['nama']) ?>" required>
</div>

<div class="mb-3">
  <label class="form-label">Username</label>
  <input type="text" name="username" class="form-control"
         value="<?= htmlspecialchars($admin['username']) ?>" required>
</div>

<div class="mb-3">
  <label class="form-label">Jabatan</label>
  <input type="text" name="jabatan" class="form-control"
         value="<?= htmlspecialchars($admin['jabatan']) ?>">
</div>

<div class="mb-3">
  <label class="form-label">No HP</label>
  <input type="text" name="no_hp" class="form-control"
         value="<?= htmlspecialchars($admin['no_hp']) ?>">
</div>

<button class="btn btn-primary">
  <i class="fas fa-save"></i> Simpan Profil
</button>
</form>
</div>
</div>

<!-- ================= PASSWORD ================= -->
<div class="tab-pane fade" id="password">
<div class="card-box">
<form action="../../controlls/proses_pengaturan.php" method="POST">
<input type="hidden" name="aksi" value="password">

<div class="mb-3">
  <label class="form-label">Password Lama</label>
  <input type="password" name="password_lama" class="form-control" required>
</div>

<div class="mb-3">
  <label class="form-label">Password Baru</label>
  <input type="password" name="password_baru" class="form-control" required>
</div>

<div class="mb-3">
  <label class="form-label">Ulangi Password Baru</label>
  <input type="password" name="password_konfirmasi" class="form-control" required>
</div>

<button class="btn btn-warning">
  <i class="fas fa-key"></i> Ubah Password
</button>
</form>
</div>
</div>

</div>
</main>
</div>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<?php include "footer.php"?>
</body>
</html>
