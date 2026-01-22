<?php
session_start();
require_once "../config/database.php";

/* AUTH ADMIN */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

/* HARUS POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/admin/admin.php");
    exit;
}

$id_admin = (int) ($_POST['id_admin'] ?? 0);
$baru     = $_POST['password_baru'] ?? '';
$konf     = $_POST['password_konfirmasi'] ?? '';

/* VALIDASI */
if ($id_admin === 0 || empty($baru) || empty($konf)) {
    $_SESSION['alert'] = 'reset_gagal';
    header("Location: ../public/admin/admin.php");
    exit;
}

if ($baru !== $konf) {
    $_SESSION['alert'] = 'password_tidak_sama';
    header("Location: ../public/admin/admin.php");
    exit;
}

/* HASH PASSWORD BARU */
$password_hash = password_hash($baru, PASSWORD_DEFAULT);

/* UPDATE PASSWORD */
$update = mysqli_query($conn, "
    UPDATE tbl_admin
    SET password = '$password_hash'
    WHERE id_admin = '$id_admin'
");

if (!$update) {
    die("Gagal reset password: " . mysqli_error($conn));
}

/* ALERT SUKSES */
$_SESSION['alert'] = 'reset_password_ok';

/* REDIRECT */
header("Location: ../public/admin/admin.php");
exit;
