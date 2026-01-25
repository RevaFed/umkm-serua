<?php
// pastikan session sudah aktif
$nama_rt = $_SESSION['nama'] ?? 'RT';
$rt      = $_SESSION['rt'] ?? '';
$rw      = $_SESSION['rw'] ?? '';
?>

<!-- ================= TOPBAR RT ================= -->
<div class="topbar">
  <div class="d-flex align-items-center gap-3">
    <button class="hamburger" id="btnMenu">
      <i class="fas fa-bars"></i>
    </button>

    <strong>
      Dashboard RT
      <?php if ($rt && $rw): ?>
        <span class="text-muted fw-normal"> | RT <?= $rt ?> / RW <?= $rw ?></span>
      <?php endif; ?>
    </strong>
  </div>

  <span class="text-muted">
    Halo, <b><?= htmlspecialchars($nama_rt) ?></b>
  </span>
</div>
