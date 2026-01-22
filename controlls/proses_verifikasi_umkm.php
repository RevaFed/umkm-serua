<?php
session_start();
require_once "../config/database.php";

/* AUTH */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: umkm.php");
    exit;
}

$id_umkm  = (int) $_POST['id_umkm'];
$id_admin = $_SESSION['id'];

/* =====================
   BLOCKCHAIN VERIFIKASI
===================== */
$payload = $id_umkm . '|' . $id_admin . '|verifikasi|' . date('Y-m-d H:i:s');
$hash_verifikasi = hash('sha256', $payload);

mysqli_query($conn, "
  INSERT INTO tbl_transaksi_blockchain
  (id_umkm, tipe_transaksi, hash_tx, tanggal_tx)
  VALUES
  ('$id_umkm', 'verifikasi_rt_rw', '$hash_verifikasi', NOW())
");

/* ALERT */
$_SESSION['alert'] = 'verifikasi_sukses';

/* LANJUT KE LEGALISASI */
header("Location: ../public/admin/legalisasi_umkm.php?id=$id_umkm");
exit;
