<?php
session_start();
require_once "../config/database.php";

/* =====================
   AUTH WARGA
===================== */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'warga') {
    header("Location: ../login.php");
    exit;
}

$id_warga = $_SESSION['id_warga'];

/* HARUS POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/warga/status_umkm.php");
    exit;
}

/* =====================
   AMBIL DATA
===================== */
$id_umkm          = (int)($_POST['id_umkm'] ?? 0);
$nama_usaha       = trim($_POST['nama_usaha'] ?? '');
$jenis_usaha      = trim($_POST['jenis_usaha'] ?? '');
$tahun_mulai      = $_POST['tahun_mulai'] ?? null;
$jumlah_karyawan  = $_POST['jumlah_karyawan'] ?? null;

/* VALIDASI DASAR */
if ($id_umkm === 0 || $nama_usaha === '' || $jenis_usaha === '') {
    $_SESSION['alert'] = 'data_tidak_lengkap';
    header("Location: ../public/warga/edit_umkm.php?id=".$id_umkm);
    exit;
}

/* =====================
   CEK UMKM (AMAN)
===================== */
$stmt = $conn->prepare("
  SELECT status
  FROM tbl_umkm
  WHERE id_umkm = ?
    AND id_warga = ?
    AND status IN ('ditolak_rt','ditolak_rw')
");
$stmt->bind_param("ii", $id_umkm, $id_warga);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header("Location: ../public/warga/status_umkm.php");
    exit;
}

/* =====================
   UPDATE UMKM
===================== */
$stmt = $conn->prepare("
  UPDATE tbl_umkm
  SET
    nama_usaha = ?,
    jenis_usaha = ?,
    tahun_mulai = ?,
    jumlah_karyawan = ?,
    status = 'menunggu_rt',
    catatan_penolakan = NULL,
    approved_rt_by = NULL,
    approved_rt_at = NULL,
    approved_rw_by = NULL,
    approved_rw_at = NULL
  WHERE id_umkm = ?
");
$stmt->bind_param(
    "ssiii",
    $nama_usaha,
    $jenis_usaha,
    $tahun_mulai,
    $jumlah_karyawan,
    $id_umkm
);
$stmt->execute();

/* =====================
   BLOCKCHAIN LOG
===================== */
$hash = hash('sha256', $id_umkm . 'pengajuan_ulang' . time());

$stmt = $conn->prepare("
  INSERT INTO tbl_transaksi_blockchain
  (id_umkm, tipe_transaksi, hash_tx, tanggal_tx)
  VALUES (?, 'pengajuan', ?, NOW())
");
$stmt->bind_param("is", $id_umkm, $hash);
$stmt->execute();

/* =====================
   REDIRECT
===================== */
$_SESSION['alert'] = 'edit_berhasil';
header("Location: ../public/warga/status_umkm.php");
exit;
