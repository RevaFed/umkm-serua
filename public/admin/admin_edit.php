<?php
session_start();
require_once "../../config/database.php";

/* AUTH */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

$id_admin = (int)($_GET['id'] ?? 0);
if ($id_admin === 0) {
    header("Location: admin.php");
    exit;
}

/* DATA ADMIN */
$q = mysqli_query($conn, "SELECT * FROM tbl_admin WHERE id_admin='$id_admin'");
if (mysqli_num_rows($q) === 0) {
    header("Location: admin.php");
    exit;
}
$admin = mysqli_fetch_assoc($q);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Admin</title>
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

<div class="card-box" style="max-width:600px">
  <h5 class="mb-3"><i class="fas fa-user-edit"></i> Edit Admin</h5>

  <form action="../../controlls/admin_proses.php" method="POST">
    <input type="hidden" name="aksi" value="edit">
    <input type="hidden" name="id_admin" value="<?= $admin['id_admin'] ?>">

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

    <div class="d-flex gap-2">
      <button class="btn btn-primary">
        <i class="fas fa-save"></i> Simpan Perubahan
      </button>
      <a href="admin.php" class="btn btn-secondary">Kembali</a>
    </div>
  </form>
</div>

</main>
</div>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<?php include "footer.php"?>
</body>
</html>
