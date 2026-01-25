<?php
session_start();
require_once "../config/database.php";

/* AUTH ADMIN */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id_target = (int)($_GET['id'] ?? 0);
$id_login  = $_SESSION['id_admin'];

if ($id_target === 0) {
    header("Location: ../public/admin/admin.php");
    exit;
}

/* PROTEKSI: tidak boleh hapus akun sendiri */
if ($id_target === $id_login) {
    $_SESSION['alert'] = 'hapus_gagal';
    header("Location: ../public/admin/admin.php");
    exit;
}

/* CEK DATA ADA ATAU TIDAK */
$stmt = $conn->prepare("SELECT id_admin FROM tbl_admin WHERE id_admin = ?");
$stmt->bind_param("i", $id_target);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['alert'] = 'hapus_gagal';
    header("Location: ../public/admin/admin.php");
    exit;
}

/* HAPUS DATA (AMAN) */
$stmt = $conn->prepare("DELETE FROM tbl_admin WHERE id_admin = ?");
$stmt->bind_param("i", $id_target);
$stmt->execute();

$_SESSION['alert'] = 'hapus_ok';
header("Location: ../public/admin/admin.php");
exit;
