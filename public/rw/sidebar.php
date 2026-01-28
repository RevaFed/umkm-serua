<!-- ================= SIDEBAR RW ================= -->
<aside class="sidebar" id="sidebar">
  <h5><i class="fas fa-user-tie"></i> RW UMKM</h5>

  <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':'' ?>">
    <i class="fas fa-home"></i> Dashboard
  </a>

    <a href="profil.php" class="<?= basename($_SERVER['PHP_SELF'])=='profil.php'?'active':'' ?>">
    <i class="fas fa-user"></i> Profil
  </a>

  <a href="umkm.php" class="<?= basename($_SERVER['PHP_SELF'])=='umkm.php'?'active':'' ?>">
    <i class="fas fa-file-signature"></i> Verifikasi UMKM
  </a>

  <a href="riwayat.php" class="<?= basename($_SERVER['PHP_SELF'])=='riwayat.php'?'active':'' ?>">
    <i class="fas fa-history"></i> Riwayat
  </a>

  <hr>

  <a href="../../controlls/logout.php">
    <i class="fas fa-sign-out-alt"></i> Logout
  </a>
</aside>
