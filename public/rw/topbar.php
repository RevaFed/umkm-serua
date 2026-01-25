<?php
$nama = $_SESSION['nama'] ?? 'RW';
$rw   = $_SESSION['rw'] ?? '';
?>

<!-- ================= TOPBAR RW ================= -->
<div class="topbar">
  <div class="d-flex align-items-center gap-3">
    <button class="hamburger" id="btnMenu">
      <i class="fas fa-bars"></i>
    </button>
    <strong>Dashboard RW <?= htmlspecialchars($rw) ?></strong>
  </div>

  <span class="text-muted">
    Halo, <b><?= htmlspecialchars($nama) ?></b>
  </span>
</div>
