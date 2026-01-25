<?php
session_start();
require_once "../config/database.php";

/* ===============================
   AUTH ADMIN
================================ */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$aksi = $_POST['aksi'] ?? '';

/* ===============================
   HELPER : NORMALISASI ROLE
================================ */
function normalisasiRole($role, &$rt, &$rw)
{
    if ($role === 'admin') {
        $rt = null;
        $rw = null;
    }

    if ($role === 'rw') {
        $rt = null;
    }
}

/* ===============================
   TAMBAH ADMIN / RT / RW
================================ */
if ($aksi === 'tambah') {

    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $jabatan  = trim($_POST['jabatan']);
    $no_hp    = trim($_POST['no_hp']);

    $role = $_POST['role'];
    $rt   = $_POST['rt'] ?? null;
    $rw   = $_POST['rw'] ?? null;

    /* NORMALISASI */
    normalisasiRole($role, $rt, $rw);

    /* VALIDASI */
    if ($role === 'rt' && (!$rt || !$rw)) {
        $_SESSION['alert'] = 'rt_wajib';
        header("Location: ../public/admin/admin_tambah.php");
        exit;
    }

    if ($role === 'rw' && !$rw) {
        $_SESSION['alert'] = 'rw_wajib';
        header("Location: ../public/admin/admin_tambah.php");
        exit;
    }

    /* INSERT */
    $stmt = $conn->prepare("
        INSERT INTO tbl_admin 
        (nama, username, password, jabatan, no_hp, role, rt, rw)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssssss",
        $nama,
        $username,
        $password,
        $jabatan,
        $no_hp,
        $role,
        $rt,
        $rw
    );

    $stmt->execute();

    $_SESSION['alert'] = 'tambah_ok';
}

/* ===============================
   EDIT ADMIN / RT / RW
================================ */
if ($aksi === 'edit') {

    $id_admin = (int)$_POST['id_admin'];

    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $jabatan  = trim($_POST['jabatan']);
    $no_hp    = trim($_POST['no_hp']);

    $role = $_POST['role'];
    $rt   = $_POST['rt'] ?? null;
    $rw   = $_POST['rw'] ?? null;

    /* NORMALISASI */
    normalisasiRole($role, $rt, $rw);

    /* VALIDASI */
    if ($role === 'rt' && (!$rt || !$rw)) {
        $_SESSION['alert'] = 'rt_wajib';
        header("Location: ../public/admin/admin_edit.php?id=".$id_admin);
        exit;
    }

    if ($role === 'rw' && !$rw) {
        $_SESSION['alert'] = 'rw_wajib';
        header("Location: ../public/admin/admin_edit.php?id=".$id_admin);
        exit;
    }

    /* UPDATE */
    $stmt = $conn->prepare("
        UPDATE tbl_admin SET
            nama = ?,
            username = ?,
            jabatan = ?,
            no_hp = ?,
            role = ?,
            rt = ?,
            rw = ?
        WHERE id_admin = ?
    ");

    $stmt->bind_param(
        "sssssssi",
        $nama,
        $username,
        $jabatan,
        $no_hp,
        $role,
        $rt,
        $rw,
        $id_admin
    );

    $stmt->execute();

    $_SESSION['alert'] = 'edit_ok';
}

/* ===============================
   REDIRECT
================================ */
header("Location: ../public/admin/admin.php");
exit;
