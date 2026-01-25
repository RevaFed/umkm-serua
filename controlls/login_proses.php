<?php
session_start();
require "../config/database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['alert'] = 'kosong';
    header("Location: ../login.php");
    exit;
}

/* ===============================
   CEK LOGIN ADMIN (RT / RW / ADMIN)
================================ */
$stmt = $conn->prepare("
    SELECT 
        id_admin,
        nama,
        password,
        jabatan,
        role,
        rt,
        rw
    FROM tbl_admin
    WHERE username = ?
");
$stmt->bind_param("s", $username);
$stmt->execute();
$admin = $stmt->get_result();

if ($admin->num_rows === 1) {
    $data = $admin->fetch_assoc();

    if (!password_verify($password, $data['password'])) {
        $_SESSION['alert'] = 'password_salah';
        header("Location: ../login.php");
        exit;
    }

    // SET SESSION ADMIN
    $_SESSION['login']     = true;
    $_SESSION['role']      = $data['role'];      // rt | rw | admin
    $_SESSION['id_admin'] = $data['id_admin'];
    $_SESSION['nama']      = $data['nama'];
    $_SESSION['jabatan']   = $data['jabatan'];
    $_SESSION['rt']        = $data['rt'];
    $_SESSION['rw']        = $data['rw'];

    // REDIRECT SESUAI ROLE
    if ($data['role'] === 'rt') {
        header("Location: ../public/rt/dashboard.php");
    } elseif ($data['role'] === 'rw') {
        header("Location: ../public/rw/dashboard.php");
    } else {
        header("Location: ../public/admin/dashboard.php");
    }
    exit;
}

/* ===============================
   CEK LOGIN WARGA
================================ */
$stmt = $conn->prepare("
    SELECT 
        id_warga,
        nama_lengkap,
        password
    FROM tbl_warga
    WHERE username = ? OR nik = ?
");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$warga = $stmt->get_result();

if ($warga->num_rows === 1) {
    $data = $warga->fetch_assoc();

    if (!password_verify($password, $data['password'])) {
        $_SESSION['alert'] = 'password_salah';
        header("Location: ../login.php");
        exit;
    }

    // SET SESSION WARGA
    $_SESSION['login']    = true;
    $_SESSION['role']     = 'warga';
    $_SESSION['id_warga'] = $data['id_warga'];
    $_SESSION['nama']     = $data['nama_lengkap'];

    header("Location: ../public/warga/dashboard.php");
    exit;
}

/* ===============================
   JIKA SEMUA GAGAL
================================ */
$_SESSION['alert'] = 'username_salah';
header("Location: ../login.php");
exit;
