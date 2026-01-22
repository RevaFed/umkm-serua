<?php
session_start();
require_once "../../config/database.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

$id_target = (int)($_GET['id'] ?? 0);
$id_login  = $_SESSION['id'];

if ($id_target === 0) {
    header("Location: admin.php");
    exit;
}

/* DATA ADMIN TARGET */
$q = mysqli_query($conn, "SELECT id_admin, nama FROM tbl_admin WHERE id_admin='$id_target'");
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
<title>Reset Password Admin</title>
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

<div class="card-box" style="max-width:500px">
  <h5 class="mb-3"><i class="fas fa-key"></i> Reset Password Admin</h5>

  <p>
    Admin: <strong><?= htmlspecialchars($admin['nama']) ?></strong>
  </p>

  <form action="../../controlls/admin_reset_password_proses.php" method="POST">
    <input type="hidden" name="id_admin" value="<?= $admin['id_admin'] ?>">

    <div class="mb-3">
      <label class="form-label">Password Baru</label>
      <input type="password" name="password_baru" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Ulangi Password Baru</label>
      <input type="password" name="password_konfirmasi" class="form-control" required>
    </div>

    <div class="d-flex gap-2">
      <button class="btn btn-warning">
        <i class="fas fa-key"></i> Reset Password
      </button>
      <a href="admin.php" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>

</main>
</div>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<?php include "footer.php"?>
</body>
</html>
