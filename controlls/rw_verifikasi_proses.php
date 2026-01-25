<?php
session_start();
require_once "../config/database.php";

/* AUTH RW */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'rw') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/rw/umkm.php");
    exit;
}

$id_umkm = (int)($_POST['id_umkm'] ?? 0);
$aksi    = $_POST['aksi'] ?? '';
$catatan = trim($_POST['catatan'] ?? '');

$id_rw = $_SESSION['id_admin'];
$rw    = $_SESSION['rw'];

if ($id_umkm === 0 || !in_array($aksi, ['setujui','tolak'])) {
    header("Location: ../public/rw/umkm.php");
    exit;
}

/* ================= VALIDASI UMKM ================= */
$stmt = $conn->prepare("
  SELECT u.id_umkm
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE u.id_umkm = ?
    AND u.status = 'menunggu_rw'
    AND w.rw = ?
");
$stmt->bind_param("is", $id_umkm, $rw);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    header("Location: ../public/rw/umkm.php");
    exit;
}

/* ================= SETUJUI RW ================= */
if ($aksi === 'setujui') {

    /* GENERATE NOMOR SURAT */
    $nomor_surat = "UMKM/" . date('Y') . "/RW-" . $rw . "/" . str_pad($id_umkm, 4, '0', STR_PAD_LEFT);

    /* UPDATE UMKM */
    $stmt = $conn->prepare("
      UPDATE tbl_umkm SET
        status = 'disetujui',
        approved_rw_by = ?,
        approved_rw_at = NOW(),
        catatan_penolakan = NULL
      WHERE id_umkm = ?
    ");
    $stmt->bind_param("ii", $id_rw, $id_umkm);
    $stmt->execute();

    /* SIMPAN LEGALISASI */
    $file_surat = NULL; // akan digenerate PDF
    $stmt = $conn->prepare("
      INSERT INTO tbl_legalisasi
      (id_umkm, id_admin, nomor_surat, file_surat, tanggal_terbit)
      VALUES (?, ?, ?, ?, CURDATE())
    ");
    $stmt->bind_param("iiss", $id_umkm, $id_rw, $nomor_surat, $file_surat);
    $stmt->execute();

    /* BLOCKCHAIN: VERIFIKASI RW */
    $hash1 = hash('sha256', $id_umkm.'verifikasi_rw'.time());
    $stmt = $conn->prepare("
      INSERT INTO tbl_transaksi_blockchain
      (id_umkm, tipe_transaksi, hash_tx, tanggal_tx)
      VALUES (?, 'verifikasi_rw', ?, NOW())
    ");
    $stmt->bind_param("is", $id_umkm, $hash1);
    $stmt->execute();

    /* BLOCKCHAIN: SURAT TERBIT */
    $hash2 = hash('sha256', $id_umkm.'surat_terbit'.time());
    $stmt = $conn->prepare("
      INSERT INTO tbl_transaksi_blockchain
      (id_umkm, tipe_transaksi, hash_tx, tanggal_tx)
      VALUES (?, 'surat_pengantar_terbit', ?, NOW())
    ");
    $stmt->bind_param("is", $id_umkm, $hash2);
    $stmt->execute();

    $_SESSION['alert'] = 'rw_setujui_ok';
    header("Location: generate_surat_umkm.php?id=".$id_umkm);
    exit;
}

/* ================= TOLAK RW ================= */
if ($aksi === 'tolak') {

    if ($catatan === '') {
        $_SESSION['alert'] = 'catatan_wajib';
        header("Location: ../public/rw/detail_umkm.php?id=".$id_umkm);
        exit;
    }

    $stmt = $conn->prepare("
      UPDATE tbl_umkm SET
        status = 'ditolak_rw',
        approved_rw_by = ?,
        approved_rw_at = NOW(),
        catatan_penolakan = ?
      WHERE id_umkm = ?
    ");
    $stmt->bind_param("isi", $id_rw, $catatan, $id_umkm);
    $stmt->execute();

    // ❌ TIDAK ADA BLOCKCHAIN JIKA DITOLAK

    $_SESSION['alert'] = 'rw_tolak_ok';
    header("Location: ../public/rw/riwayat.php");
    exit;
}
