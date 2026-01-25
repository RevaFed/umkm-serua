<?php
require "auth.php";
require_once "../../config/database.php";

$id_admin = (int)($_GET['id'] ?? 0);
if ($id_admin === 0) {
    header("Location: admin.php");
    exit;
}

/* DATA ADMIN */
$stmt = $conn->prepare("SELECT * FROM tbl_admin WHERE id_admin=?");
$stmt->bind_param("i", $id_admin);
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
<title>Edit Admin</title>
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

<div class="card-box" style="max-width:600px">
  <h5 class="mb-3">
    <i class="fas fa-user-edit"></i> Edit Admin
  </h5>

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

    <!-- ROLE -->
    <div class="mb-3">
      <label class="form-label">Role</label>
      <select name="role" id="role" class="form-select" required>
        <option value="admin" <?= $admin['role']=='admin'?'selected':'' ?>>
          Admin
        </option>
        <option value="rt" <?= $admin['role']=='rt'?'selected':'' ?>>
          Admin RT
        </option>
        <option value="rw" <?= $admin['role']=='rw'?'selected':'' ?>>
          Admin RW
        </option>
      </select>
    </div>

    <!-- RT -->
<div class="mb-3" id="field-rt">
  <label class="form-label">RT</label>
  <input type="text" name="rt" class="form-control"
         value="<?= htmlspecialchars($admin['rt'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
         placeholder="Contoh: 01">
</div>

<!-- RW -->
<div class="mb-3" id="field-rw">
  <label class="form-label">RW</label>
  <input type="text" name="rw" class="form-control"
         value="<?= htmlspecialchars($admin['rw'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
         placeholder="Contoh: 02">
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

<?php include "footer.php"; ?>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
const roleSelect = document.getElementById('role');
const fieldRT = document.getElementById('field-rt');
const fieldRW = document.getElementById('field-rw');

function toggleWilayah() {
  fieldRT.style.display = 'none';
  fieldRW.style.display = 'none';

  if (roleSelect.value === 'rt') {
    fieldRT.style.display = 'block';
    fieldRW.style.display = 'block';
  }
  if (roleSelect.value === 'rw') {
    fieldRW.style.display = 'block';
  }
}

toggleWilayah();
roleSelect.addEventListener('change', toggleWilayah);
</script>

</body>
</html>
