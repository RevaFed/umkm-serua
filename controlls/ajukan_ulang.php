<?php
session_start();
require_once "../config/database.php";

/* AUTH WARGA */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'warga') {
  header("Location: ../login.php");
  exit;
}

$id_warga = $_SESSION['id_warga'];
$id_umkm  = (int)($_GET['id'] ?? 0);

/* VALIDASI */
$stmt = $conn->prepare("
  SELECT id_umkm
  FROM tbl_umkm
  WHERE id_umkm = ?
    AND id_warga = ?
    AND status IN ('ditolak_rt','ditolak_rw')
");
$stmt->bind_param("ii", $id_umkm, $id_warga);
$stmt->execute();
$cek = $stmt->get_result();

if ($cek->num_rows === 0) {
  header("Location: ../public/warga/status_umkm.php");
  exit;
}

/* RESET STATUS */
$conn->query("
  UPDATE tbl_umkm
  SET status = 'menunggu_rt',
      catatan_penolakan = NULL,
      approved_rt_by = NULL,
      approved_rw_by = NULL
  WHERE id_umkm = $id_umkm
");

/* BLOCKCHAIN LOG */
$hash = hash('sha256', $id_umkm . 'pengajuan_ulang' . time());
$stmt = $conn->prepare("
  INSERT INTO tbl_transaksi_blockchain
  (id_umkm, tipe_transaksi, hash_tx, tanggal_tx)
  VALUES (?, 'pengajuan_ulang', ?, NOW())
");
$stmt->bind_param("is", $id_umkm, $hash);
$stmt->execute();

$_SESSION['alert'] = 'ajukan_ulang_ok';
header("Location: ../public/warga/status_umkm.php");
exit;
