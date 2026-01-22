<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id_admin = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/admin/pengaturan.php");
    exit;
}

/* ================= PROFIL ================= */
if ($_POST['aksi'] === 'profil') {

    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $jabatan  = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $no_hp    = mysqli_real_escape_string($conn, $_POST['no_hp']);

    mysqli_query($conn, "
      UPDATE tbl_admin SET
      nama='$nama',
      username='$username',
      jabatan='$jabatan',
      no_hp='$no_hp'
      WHERE id_admin='$id_admin'
    ");

    $_SESSION['alert'] = 'profil_ok';
}

/* ================= PASSWORD ================= */
if ($_POST['aksi'] === 'password') {

    $lama = $_POST['password_lama'];
    $baru = $_POST['password_baru'];
    $konf = $_POST['password_konfirmasi'];

    $q = mysqli_query($conn, "SELECT password FROM tbl_admin WHERE id_admin='$id_admin'");
    $data = mysqli_fetch_assoc($q);

    if (!password_verify($lama, $data['password'])) {
        $_SESSION['alert'] = 'password_salah';
        header("Location: ../public/admin/pengaturan.php");
        exit;
    }

    if ($baru !== $konf) {
        $_SESSION['alert'] = 'password_salah';
        header("Location: ../public/admin/pengaturan.php");
        exit;
    }

    $hash = password_hash($baru, PASSWORD_DEFAULT);

    mysqli_query($conn, "
      UPDATE tbl_admin SET password='$hash'
      WHERE id_admin='$id_admin'
    ");

    $_SESSION['alert'] = 'password_ok';
}

header("Location: ../public/admin/pengaturan.php");
exit;
