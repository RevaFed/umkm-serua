<?php
require "auth.php";
require_once "../../config/database.php";

$rt = $_SESSION['rt'];
$rw = $_SESSION['rw'];

/* DATA RT */
$stmt = $conn->prepare("
  SELECT nama, ttd, stempel
  FROM tbl_admin
  WHERE role = 'rt'
    AND rt = ?
    AND rw = ?
  LIMIT 1
");
$stmt->bind_param("ss", $rt, $rw);
$stmt->execute();
$rtData = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil RT</title>
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

<div class="card-box mb-3">
  <h5><i class="fas fa-user"></i> Profil RT</h5>
  <p><strong>Nama:</strong> <?= htmlspecialchars($rtData['nama']) ?></p>
  <p><strong>RT / RW:</strong> <?= $rt ?> / <?= $rw ?></p>
</div>

<div class="card-box">
  <h5><i class="fas fa-signature"></i> Tanda Tangan & Stempel RT</h5>

  <form action="../../controlls/profil_proses_rt.php"
        method="POST"
        enctype="multipart/form-data">

    <!-- TTD RT -->
    <div class="mb-3">
      <label class="form-label">Tanda Tangan RT (PNG transparan)</label>

      <?php if (!empty($rtData['ttd'])): ?>
        <div class="mb-2">
          <img src="../../uploads/ttd/<?= $rtData['ttd'] ?>"
               style="max-height:120px">
        </div>
      <?php endif; ?>

      <input type="file" name="ttd"
             class="form-control"
             accept="image/*">
    </div>

    <!-- STEMPEL RT -->
    <div class="mb-3">
      <label class="form-label">Stempel RT (PNG transparan)</label>

      <?php if (!empty($rtData['stempel'])): ?>
        <div class="mb-2">
          <img src="../../uploads/stempel/<?= $rtData['stempel'] ?>"
               style="max-height:120px">
        </div>
      <?php endif; ?>

      <input type="file" name="stempel"
             class="form-control"
             accept="image/*">
    </div>

    <button class="btn btn-primary">
      <i class="fas fa-save"></i> Simpan
    </button>
  </form>
</div>

</main>
</div>

<?php include "footer.php"; ?>
</body>
</html>
