<?php
session_start();
require_once "../config/database.php";


$id_target = (int)($_GET['id'] ?? 0);
$id_login  = $_SESSION['id'];

if ($id_target === 0) {
    header("Location: ../public/admin/admin.php");
    exit;
}

/* PROTEKSI: tidak boleh hapus diri sendiri */
if ($id_target === $id_login) {
    $_SESSION['alert'] = 'hapus_gagal';
    header("Location: ../public/admin/admin.php");
    exit;
}

/* HAPUS */
mysqli_query($conn, "DELETE FROM tbl_admin WHERE id_admin='$id_target'");

$_SESSION['alert'] = 'hapus_ok';
header("Location: ../public/admin/admin.php");
exit;
