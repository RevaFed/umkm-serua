<?php
require_once "../config/database.php";
session_start();

$rw = $_SESSION['rw'];

if (!$rw) {
  header("Location: ../login.php");
  exit;
}

/* ===== TTD RW ===== */
if (!empty($_FILES['ttd']['name'])) {
  $ext = pathinfo($_FILES['ttd']['name'], PATHINFO_EXTENSION);
  $namaTTD = "ttd_rw_{$rw}.".$ext;

  move_uploaded_file(
    $_FILES['ttd']['tmp_name'],
    "../uploads/ttd/".$namaTTD
  );

  mysqli_query($conn, "
    UPDATE tbl_admin
    SET ttd = '$namaTTD'
    WHERE role = 'rw'
      AND rw = '$rw'
  ");
}

/* ===== STEMPEL RW ===== */
if (!empty($_FILES['stempel']['name'])) {
  $ext = pathinfo($_FILES['stempel']['name'], PATHINFO_EXTENSION);
  $namaStempel = "stempel_rw_{$rw}.".$ext;

  move_uploaded_file(
    $_FILES['stempel']['tmp_name'],
    "../uploads/stempel/".$namaStempel
  );

  mysqli_query($conn, "
    UPDATE tbl_admin
    SET stempel = '$namaStempel'
    WHERE role = 'rw'
      AND rw = '$rw'
  ");
}

header("Location: ../public/rw/profil.php");
exit;
