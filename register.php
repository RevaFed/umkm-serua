<?php
session_start();

// ambil flash alert lalu hapus (biar tidak muncul lagi saat refresh)
$alert = $_SESSION['alert'] ?? '';
unset($_SESSION['alert']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Akun | Sistem Perizinan UMKM RT 01 RW 02</title>

  <!-- Bootstrap LOCAL -->
  <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">

  <!-- Font Awesome LOCAL -->
  <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">

  <link rel="stylesheet" href="assets/styles/register-styles.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>

<div class="register-card">

  <!-- ALERT -->
  <?php if ($alert): ?>
    <div class="alert 
      <?php
        if ($alert === 'sukses') echo 'alert-success';
        elseif ($alert === 'nik_ada' || $alert === 'username_ada') echo 'alert-warning';
        else echo 'alert-danger';
      ?>
      alert-dismissible fade show mb-3" role="alert">

      <?php if ($alert === 'sukses'): ?>
        <i class="fas fa-check-circle"></i>
        <strong>Berhasil!</strong> Akun berhasil didaftarkan. Silakan login.
      <?php elseif ($alert === 'nik_ada'): ?>
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Peringatan!</strong> NIK sudah terdaftar.
      <?php elseif ($alert === 'username_ada'): ?>
        <i class="fas fa-user-times"></i>
        <strong>Peringatan!</strong> Username sudah digunakan.
      <?php else: ?>
        <i class="fas fa-times-circle"></i>
        <strong>Gagal!</strong> Terjadi kesalahan saat pendaftaran.
      <?php endif; ?>

      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- BACK -->
  <div class="mb-2">
    <a href="login.php" class="btn-back">
      <i class="fas fa-arrow-left"></i> Kembali
    </a>
  </div>

  <!-- TITLE -->
  <div class="text-center mb-3">
    <i class="fas fa-user-plus text-primary fs-5"></i>
    <h6 class="fw-bold mb-0">Daftar Akun Warga</h6>
    <small class="text-muted">RT 01 RW 02 Kelurahan Serua</small>
  </div>

  <!-- FORM -->
  <form action="controlls/register_process.php" method="post">
    <div class="row g-2">

      <!-- KIRI -->
      <div class="col-md-6">
        <div class="mb-2">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" name="nama_lengkap" class="form-control" required>
        </div>

        <div class="mb-2">
          <label class="form-label">NIK</label>
          <input type="text" name="nik" maxlength="16" class="form-control" required>
        </div>

        <div class="mb-2">
          <label class="form-label">Tanggal Lahir</label>
          <input type="date" name="tanggal_lahir" class="form-control">
        </div>

        <div class="mb-2">
          <label class="form-label">Alamat (KTP)</label>
          <textarea name="alamat" class="form-control"></textarea>
        </div>

        <div class="mb-2">
          <label class="form-label">Agama</label>
          <select name="agama" class="form-select">
            <option value="">-- Pilih --</option>
            <option>Islam</option>
            <option>Kristen</option>
            <option>Katolik</option>
            <option>Hindu</option>
            <option>Buddha</option>
            <option>Konghucu</option>
          </select>
        </div>
      </div>

      <!-- KANAN -->
      <div class="col-md-6">
        <div class="mb-2">
          <label class="form-label">Status Perkawinan</label>
          <select name="status_perkawinan" class="form-select">
            <option value="">-- Pilih --</option>
            <option>Belum Kawin</option>
            <option>Kawin</option>
            <option>Cerai</option>
          </select>
        </div>

        <div class="mb-2">
          <label class="form-label">No. HP</label>
          <input type="text" name="no_hp" class="form-control">
        </div>

        <div class="mb-2">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control">
        </div>

        <div class="mb-2">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
      </div>

    </div>

    <div class="d-grid mt-2">
      <button type="submit" class="btn btn-primary btn-register">
        <i class="fas fa-user-check"></i> Daftar
      </button>
    </div>
  </form>

  <div class="footer-text">
    © 2026 Sistem Perizinan UMKM<br>
    RT 01 RW 02 - Kelurahan Serua - Kecamatan Ciputat
  </div>

</div>

<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- auto hide alert -->
<script>
setTimeout(() => {
  const alert = document.querySelector('.alert');
  if (alert) alert.classList.remove('show');
}, 3500);
</script>

</body>
</html>
