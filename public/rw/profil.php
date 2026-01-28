<?php
require "auth.php";
require_once "../../config/database.php";

$rw = $_SESSION['rw'];

/* DATA RW */
$stmt = $conn->prepare("
  SELECT nama, ttd, stempel
  FROM tbl_admin
  WHERE role = 'rw'
    AND rw = ?
  LIMIT 1
");
$stmt->bind_param("s", $rw);
$stmt->execute();
$rwData = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil RW</title>
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

<!-- ================= PROFIL RW ================= -->
<div class="card-box mb-3">
  <h5><i class="fas fa-user"></i> Profil RW</h5>
  <p><strong>Nama:</strong> <?= htmlspecialchars($rwData['nama']) ?></p>
  <p><strong>RW:</strong> <?= htmlspecialchars($rw) ?></p>
</div>

<!-- ================= TTD & STEMPEL RW ================= -->
<div class="card-box">
  <h5><i class="fas fa-signature"></i> Tanda Tangan & Stempel RW</h5>

  <form action="../../controlls/profil_proses_rw.php"
        method="POST"
        enctype="multipart/form-data">

    <!-- TTD RW -->
    <div class="mb-3">
      <label class="form-label">Tanda Tangan RW (PNG transparan)</label>

      <?php if (!empty($rwData['ttd'])): ?>
        <div class="mb-2">
          <img src="../../uploads/ttd/<?= $rwData['ttd'] ?>"
               style="max-height:120px">
        </div>
      <?php endif; ?>

      <input type="file" name="ttd"
             class="form-control"
             accept="image/*">
    </div>

    <!-- STEMPEL RW -->
    <div class="mb-3">
      <label class="form-label">Stempel RW (PNG transparan)</label>

      <?php if (!empty($rwData['stempel'])): ?>
        <div class="mb-2">
          <img src="../../uploads/stempel/<?= $rwData['stempel'] ?>"
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
