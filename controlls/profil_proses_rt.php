<?php
require_once "../config/database.php";
session_start();

$rt = $_SESSION['rt'];
$rw = $_SESSION['rw'];

if (!$rt || !$rw) {
  header("Location: ../login.php");
  exit;
}

/* ===== TTD ===== */
if (!empty($_FILES['ttd']['name'])) {
  $ext = pathinfo($_FILES['ttd']['name'], PATHINFO_EXTENSION);
  $namaTTD = "ttd_rt_{$rt}_{$rw}.".$ext;

  move_uploaded_file(
    $_FILES['ttd']['tmp_name'],
    "../uploads/ttd/".$namaTTD
  );

  mysqli_query($conn, "
    UPDATE tbl_admin
    SET ttd = '$namaTTD'
    WHERE role = 'rt'
      AND rt = '$rt'
      AND rw = '$rw'
  ");
}

/* ===== STEMPEL ===== */
if (!empty($_FILES['stempel']['name'])) {
  $ext = pathinfo($_FILES['stempel']['name'], PATHINFO_EXTENSION);
  $namaStempel = "stempel_rt_{$rt}_{$rw}.".$ext;

  move_uploaded_file(
    $_FILES['stempel']['tmp_name'],
    "../uploads/stempel/".$namaStempel
  );

  mysqli_query($conn, "
    UPDATE tbl_admin
    SET stempel = '$namaStempel'
    WHERE role = 'rt'
      AND rt = '$rt'
      AND rw = '$rw'
  ");
}

header("Location: ../public/rt/profil.php");
exit;
