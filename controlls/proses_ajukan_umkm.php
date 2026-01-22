<?php
session_start();
require_once "../config/database.php";

/* AUTH */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'warga') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ajukan_umkm.php");
    exit;
}

$id_warga = $_SESSION['id_warga'];

// ================= VALIDASI INPUT =================
if (empty($_POST['nama_usaha']) || empty($_POST['jenis_usaha'])) {
    $_SESSION['alert'] = 'error';
    $_SESSION['msg'] = 'Nama usaha dan jenis usaha wajib diisi.';
    header("Location: ajukan_umkm.php");
    exit;
}

if (empty($_FILES['dokumen']['name'][0])) {
    $_SESSION['alert'] = 'error';
    $_SESSION['msg'] = 'Dokumen wajib diupload.';
    header("Location: ajukan_umkm.php");
    exit;
}


/* =====================
   DATA UMKM
===================== */
$nama_usaha      = mysqli_real_escape_string($conn, $_POST['nama_usaha']);
$jenis_usaha     = mysqli_real_escape_string($conn, $_POST['jenis_usaha']);
$tahun_mulai     = $_POST['tahun_mulai'] ?: null;
$jumlah_karyawan = $_POST['jumlah_karyawan'] ?: null;

/* =====================
   INSERT UMKM
===================== */
$insertUmkm = mysqli_query($conn, "
  INSERT INTO tbl_umkm
  (id_warga, nama_usaha, jenis_usaha, tahun_mulai, jumlah_karyawan, tanggal_pengajuan)
  VALUES
  ('$id_warga', '$nama_usaha', '$jenis_usaha', '$tahun_mulai', '$jumlah_karyawan', CURDATE())
");

if (!$insertUmkm) {
    $_SESSION['alert'] = 'gagal';
    header("Location: ajukan_umkm.php");
    exit;
}

$id_umkm = mysqli_insert_id($conn);

/* =====================
   UPLOAD DOKUMEN
===================== */
$uploadDir = "../uploads/dokumen_umkm/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

foreach ($_FILES['dokumen']['name'] as $i => $namaFile) {
    if ($_FILES['dokumen']['error'][$i] === 0) {
        $ext = pathinfo($namaFile, PATHINFO_EXTENSION);
        $newName = uniqid('dok_') . '.' . $ext;
        move_uploaded_file($_FILES['dokumen']['tmp_name'][$i], $uploadDir . $newName);

        $jenisDok = mysqli_real_escape_string($conn, $_POST['jenis_dokumen'][$i]);

        mysqli_query($conn, "
          INSERT INTO tbl_dokumen_umkm (id_umkm, jenis_dokumen, file_path)
          VALUES ('$id_umkm', '$jenisDok', '$newName')
        ");
    }
}

/* =====================
   BLOCKCHAIN PENGAJUAN
   (AUDIT HASH)
===================== */
$payload = $id_umkm . '|' . $id_warga . '|' . date('Y-m-d H:i:s');
$hash_pengajuan = hash('sha256', $payload);

mysqli_query($conn, "
  INSERT INTO tbl_transaksi_blockchain
  (id_umkm, tipe_transaksi, hash_tx, tanggal_tx)
  VALUES
  ('$id_umkm', 'pengajuan', '$hash_pengajuan', NOW())
");

/* =====================
   SELESAI
===================== */
$_SESSION['alert'] = 'sukses';
header("Location: ../public/warga/status_umkm.php");
exit;
