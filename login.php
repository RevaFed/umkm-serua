<?php
session_start();

// ambil alert lalu hapus supaya tidak muncul terus
$alert = $_SESSION['alert'] ?? '';
unset($_SESSION['alert']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login | Sistem Perizinan UMKM Kelurahan Serua</title>

  <!-- Bootstrap LOCAL -->
  <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">

  <!-- Font Awesome LOCAL -->
  <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">

  <link rel="stylesheet" href="assets/styles/login-styles.css">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<!-- ALERT -->
<?php if ($alert): ?>
<div class="container position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index:9999;max-width:420px">
  <div class="alert 
    <?php
      if ($alert === 'logout') echo 'alert-success';
      elseif ($alert === 'kosong') echo 'alert-warning';
      else echo 'alert-danger';
    ?>
    alert-dismissible fade show shadow" role="alert">

    <?php if ($alert === 'logout'): ?>
      <i class="fas fa-check-circle"></i>
      <strong>Logout berhasil.</strong> Sampai jumpa kembali.
    <?php elseif ($alert === 'kosong'): ?>
      <i class="fas fa-exclamation-triangle"></i>
      <strong>Perhatian!</strong> Username dan password wajib diisi.
    <?php elseif ($alert === 'username_salah'): ?>
      <i class="fas fa-times-circle"></i>
      <strong>Gagal!</strong> Username / NIK tidak ditemukan.
    <?php elseif ($alert === 'password_salah'): ?>
      <i class="fas fa-lock"></i>
      <strong>Gagal!</strong> Password salah.
    <?php endif; ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
</div>
<?php endif; ?>

<!-- LOGIN CARD -->
<div class="login-card">

  <!-- BACK -->
  <div class="mb-3">
    <a href="index.html" class="btn-back">
      <i class="fas fa-arrow-left"></i> Kembali ke Beranda
    </a>
  </div>

  <!-- TITLE -->
  <div class="text-center mb-4">
    <i class="fas fa-user-lock fa-2x text-primary mb-2"></i>
    <h5 class="login-title mb-1">Login Sistem</h5>
    <p class="text-muted mb-0">
      Sistem Perizinan UMKM Kelurahan Serua
    </p>
  </div>

  <!-- FORM -->
  <form action="controlls/login_proses.php" method="post">
    <div class="mb-3">
      <label class="form-label">Username / NIK</label>
      <input
        type="text"
        name="username"
        class="form-control"
        placeholder="Masukkan username atau NIK"
        required
      >
    </div>

    <div class="mb-3">
      <label class="form-label">Password</label>
      <input
        type="password"
        name="password"
        class="form-control"
        placeholder="Masukkan password"
        required
      >
    </div>

    <div class="d-grid mt-4">
      <button type="submit" class="btn btn-primary btn-login">
        <i class="fas fa-sign-in-alt"></i> Login
      </button>
    </div>
  </form>

  <!-- LINK DAFTAR -->
  <div class="text-center mt-3">
    <span class="text-muted">Belum punya akun?</span>
    <a href="register.php" class="text-primary fw-semibold">
      Daftar
    </a>
  </div>

  <!-- FOOTER -->
  <div class="text-center footer-text">
    © 2026 Sistem Perizinan UMKM<br>
    RT 01 RW 02 – Kelurahan Serua – Kecamatan Ciputat
  </div>

</div>

<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
