<?php
session_start();
require "../config/database.php";

// pastikan form dikirim via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../register.php");
    exit;
}

// ===============================
// AMBIL & BERSIHKAN DATA
// ===============================
$nama_lengkap      = trim($_POST['nama_lengkap']);
$nik               = trim($_POST['nik']);
$tanggal_lahir     = $_POST['tanggal_lahir'] ?? null;
$alamat            = trim($_POST['alamat']);
$agama             = $_POST['agama'] ?? null;
$status_perkawinan = $_POST['status_perkawinan'] ?? null;
$no_hp             = trim($_POST['no_hp']);
$email             = trim($_POST['email']);
$username          = trim($_POST['username']);
$password_plain    = $_POST['password'];

// ===============================
// VALIDASI WAJIB
// ===============================
if (
    empty($nama_lengkap) ||
    empty($nik) ||
    empty($username) ||
    empty($password_plain)
) {
    $_SESSION['alert'] = 'gagal';
    header("Location: ../register.php");
    exit;
}

// ===============================
// HASH PASSWORD
// ===============================
$password = password_hash($password_plain, PASSWORD_DEFAULT);

// ===============================
// CEK NIK SUDAH ADA?
// ===============================
$stmt = $conn->prepare("SELECT id_warga FROM tbl_warga WHERE nik = ?");
$stmt->bind_param("s", $nik);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $_SESSION['alert'] = 'nik_ada';
    header("Location: ../register.php");
    exit;
}
$stmt->close();

// ===============================
// CEK USERNAME SUDAH ADA?
// ===============================
$stmt = $conn->prepare("SELECT id_warga FROM tbl_warga WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $_SESSION['alert'] = 'username_ada';
    header("Location: ../register.php");
    exit;
}
$stmt->close();

// ===============================
// INSERT DATA WARGA
// ===============================
$stmt = $conn->prepare("
    INSERT INTO tbl_warga (
        nama_lengkap,
        nik,
        tanggal_lahir,
        alamat,
        agama,
        status_perkawinan,
        no_hp,
        email,
        username,
        password
    ) VALUES (?,?,?,?,?,?,?,?,?,?)
");

$stmt->bind_param(
    "ssssssssss",
    $nama_lengkap,
    $nik,
    $tanggal_lahir,
    $alamat,
    $agama,
    $status_perkawinan,
    $no_hp,
    $email,
    $username,
    $password
);

if ($stmt->execute()) {
    $_SESSION['alert'] = 'sukses';
} else {
    $_SESSION['alert'] = 'gagal';
}

$stmt->close();
$conn->close();

// ===============================
// REDIRECT TANPA PARAMETER URL
// ===============================
header("Location: ../register.php");
exit;
