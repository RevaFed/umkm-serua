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

$id_target = (int)($_POST['id_admin'] ?? 0);
$id_login  = $_SESSION['id_admin'];

$baru = $_POST['password_baru'] ?? '';
$konf = $_POST['password_konfirmasi'] ?? '';

/* VALIDASI DASAR */
if ($id_target === 0 || empty($baru) || empty($konf)) {
    $_SESSION['alert'] = 'reset_gagal';
    header("Location: ../public/admin/admin.php");
    exit;
}

/* CEGAH RESET PASSWORD AKUN SENDIRI */
if ($id_target === $id_login) {
    $_SESSION['alert'] = 'reset_diri_sendiri';
    header("Location: ../public/admin/admin.php");
    exit;
}

/* PASSWORD HARUS SAMA */
if ($baru !== $konf) {
    $_SESSION['alert'] = 'password_tidak_sama';
    header("Location: ../public/admin/admin.php");
    exit;
}

/* HASH PASSWORD */
$password_hash = password_hash($baru, PASSWORD_DEFAULT);

/* UPDATE PASSWORD (AMAN) */
$stmt = $conn->prepare("
    UPDATE tbl_admin
    SET password = ?
    WHERE id_admin = ?
");
$stmt->bind_param("si", $password_hash, $id_target);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    $_SESSION['alert'] = 'reset_gagal';
    header("Location: ../public/admin/admin.php");
    exit;
}

/* ALERT SUKSES */
$_SESSION['alert'] = 'reset_password_ok';

/* REDIRECT */
header("Location: ../public/admin/admin.php");
exit;
