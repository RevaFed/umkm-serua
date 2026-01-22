<?php
// pastikan session sudah aktif di halaman utama
$nama_warga = $_SESSION['nama'] ?? 'Warga';
?>

<!-- TOPBAR -->
<div class="topbar">
  <div class="d-flex align-items-center gap-3">
    <button class="hamburger" id="btnMenu">
      <i class="fas fa-bars"></i>
    </button>
    <strong>Dashboard Warga</strong>
  </div>
  <span class="text-muted">
    Halo, <b><?= htmlspecialchars($nama_warga) ?></b>
  </span>
</div>
