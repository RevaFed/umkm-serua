<?php
require "auth.php";
require_once "../../config/database.php";

$id_target = (int)($_GET['id'] ?? 0);
$id_login  = $_SESSION['id_admin'];

if ($id_target === 0) {
    header("Location: admin.php");
    exit;
}

/* CEGAH RESET PASSWORD AKUN SENDIRI */
if ($id_target === $id_login) {
    $_SESSION['alert'] = 'reset_diri_sendiri';
    header("Location: admin.php");
    exit;
}

/* DATA ADMIN TARGET */
$stmt = $conn->prepare("
    SELECT id_admin, nama 
    FROM tbl_admin 
    WHERE id_admin = ?
");
$stmt->bind_param("i", $id_target);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin.php");
    exit;
}

$admin = $result->fetch_assoc();
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

<div class="wrapper">
<?php include "sidebar.php"; ?>

<main class="content">
<?php include "topbar.php"; ?>

<div class="card-box" style="max-width:500px">
  <h5 class="mb-3">
    <i class="fas fa-key"></i> Reset Password Admin
  </h5>

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

<?php include "footer.php"; ?>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
