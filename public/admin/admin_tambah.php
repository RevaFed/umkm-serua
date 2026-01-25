<?php
require "auth.php";
require_once "../../config/database.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Admin</title>
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
    <i class="fas fa-user-plus"></i> Tambah Admin
  </h5>

  <form action="../../controlls/admin_proses.php" method="POST">
    <input type="hidden" name="aksi" value="tambah">

    <div class="mb-3">
      <label class="form-label">Nama</label>
      <input type="text" name="nama" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Jabatan</label>
      <input type="text" name="jabatan" class="form-control">
    </div>

    <div class="mb-3">
      <label class="form-label">No HP</label>
      <input type="text" name="no_hp" class="form-control">
    </div>

    <!-- ROLE -->
    <div class="mb-3">
      <label class="form-label">Role</label>
      <select name="role" id="role" class="form-select" required>
        <option value="">-- Pilih Role --</option>
        <option value="admin">Admin</option>
        <option value="rt">Admin RT</option>
        <option value="rw">Admin RW</option>
      </select>
    </div>

    <!-- RT -->
    <div class="mb-3" id="field-rt" style="display:none;">
      <label class="form-label">RT</label>
      <input type="text" name="rt" class="form-control" placeholder="Contoh: 01">
    </div>

    <!-- RW -->
    <div class="mb-3" id="field-rw" style="display:none;">
      <label class="form-label">RW</label>
      <input type="text" name="rw" class="form-control" placeholder="Contoh: 02">
    </div>

    <div class="d-flex gap-2">
      <button class="btn btn-primary">
        <i class="fas fa-save"></i> Simpan
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

roleSelect.addEventListener('change', function () {
  fieldRT.style.display = 'none';
  fieldRW.style.display = 'none';

  if (this.value === 'rt') {
    fieldRT.style.display = 'block';
    fieldRW.style.display = 'block';
  }

  if (this.value === 'rw') {
    fieldRW.style.display = 'block';
  }
});
</script>

</body>
</html>
