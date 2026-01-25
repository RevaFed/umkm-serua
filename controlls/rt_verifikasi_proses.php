<?php
session_start();
require_once "../config/database.php";

/* AUTH RT */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'rt') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/rt/umkm.php");
    exit;
}

$id_umkm = (int)($_POST['id_umkm'] ?? 0);
$aksi    = $_POST['aksi'] ?? '';
$catatan = trim($_POST['catatan'] ?? '');

$id_rt = $_SESSION['id_admin'];
$rt    = $_SESSION['rt'];
$rw    = $_SESSION['rw'];

if ($id_umkm === 0 || !in_array($aksi, ['setujui','tolak'])) {
    header("Location: ../public/rt/umkm.php");
    exit;
}

/* ================= VALIDASI UMKM ================= */
$stmt = $conn->prepare("
  SELECT u.id_umkm
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE u.id_umkm = ?
    AND u.status = 'menunggu_rt'
    AND w.rt = ?
    AND w.rw = ?
");
$stmt->bind_param("iss", $id_umkm, $rt, $rw);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    header("Location: ../public/rt/umkm.php");
    exit;
}

/* ================= SETUJUI ================= */
if ($aksi === 'setujui') {

    $stmt = $conn->prepare("
      UPDATE tbl_umkm SET
        status = 'menunggu_rw',
        approved_rt_by = ?,
        approved_rt_at = NOW(),
        catatan_penolakan = NULL
      WHERE id_umkm = ?
    ");
    $stmt->bind_param("ii", $id_rt, $id_umkm);
    $stmt->execute();

    /* BLOCKCHAIN (VALID ENUM) */
    $hash = hash('sha256', $id_umkm.'verifikasi_rt'.time());

    $stmt = $conn->prepare("
      INSERT INTO tbl_transaksi_blockchain
      (id_umkm, tipe_transaksi, hash_tx, tanggal_tx)
      VALUES (?, 'verifikasi_rt', ?, NOW())
    ");
    $stmt->bind_param("is", $id_umkm, $hash);
    $stmt->execute();

    $_SESSION['alert'] = 'rt_setujui_ok';
    header("Location: ../public/rt/umkm.php");
    exit;
}

/* ================= TOLAK ================= */
if ($aksi === 'tolak') {

    if ($catatan === '') {
        $_SESSION['alert'] = 'catatan_wajib';
        header("Location: ../public/rt/detail_umkm.php?id=".$id_umkm);
        exit;
    }

    $stmt = $conn->prepare("
      UPDATE tbl_umkm SET
        status = 'ditolak_rt',
        approved_rt_by = ?,
        approved_rt_at = NOW(),
        catatan_penolakan = ?
      WHERE id_umkm = ?
    ");
    $stmt->bind_param("isi", $id_rt, $catatan, $id_umkm);
    $stmt->execute();

    // ❌ TIDAK ADA BLOCKCHAIN JIKA DITOLAK

    $_SESSION['alert'] = 'rt_tolak_ok';
    header("Location: ../public/rt/riwayat.php");
    exit;
}
