<?php
// pastikan session sudah aktif di halaman utama
$nama_admin = $_SESSION['nama'] ?? 'Admin';
?>

<!-- TOPBAR -->
<div class="topbar">
  <div class="d-flex align-items-center gap-3">
    <!-- HAMBURGER (MOBILE) -->
    <button class="hamburger" id="btnMenu">
      <i class="fas fa-bars"></i>
    </button>

    <strong>Dashboard Admin</strong>
  </div>

  <span class="text-muted">
    Login sebagai <b><?= htmlspecialchars($nama_admin) ?></b>
  </span>
</div>
