<?php
session_start();
require_once "../config/database.php";
require_once "../vendor/fpdf/fpdf.php";

/* AUTH */
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id_umkm  = (int) $_POST['id_umkm'];
$id_admin = $_SESSION['id'];
$nomor    = mysqli_real_escape_string($conn, $_POST['nomor_surat']);

/* AMBIL DATA */
$q = mysqli_query($conn, "
  SELECT u.nama_usaha, w.nama_lengkap
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  WHERE u.id_umkm = '$id_umkm'
");
$data = mysqli_fetch_assoc($q);

/* =====================
   GENERATE PDF (FPDF)
===================== */
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

$pdf->Cell(0,10,'SURAT LEGALISASI USAHA',0,1,'C');
$pdf->Ln(5);

$pdf->Cell(0,8,'Nomor : '.$nomor,0,1);
$pdf->Ln(5);

$pdf->MultiCell(0,8,
  "Dengan ini menerangkan bahwa:\n\n".
  "Nama Usaha : ".$data['nama_usaha']."\n".
  "Pemilik    : ".$data['nama_lengkap']."\n\n".
  "Adalah benar usaha yang berada di wilayah Kelurahan Serua."
);

$pdf->Ln(15);
$pdf->Cell(0,8,'Serua, '.date('d-m-Y'),0,1,'R');
$pdf->Ln(15);
$pdf->Cell(0,8,'Lurah Serua',0,1,'R');

/* SIMPAN FILE */
$nama_file = "SURAT_UMKM_$id_umkm.pdf";
$path_file = "../uploads/surat/$nama_file";
$pdf->Output('F', $path_file);

/* =====================
   BLOCKCHAIN HASH
===================== */
$hash_final = hash_file('sha256', $path_file);

/* =====================
   SIMPAN DB
===================== */
mysqli_query($conn, "
  INSERT INTO tbl_legalisasi
  (id_umkm, id_admin, nomor_surat, file_surat, tanggal_terbit, blockchain_tx)
  VALUES
  ('$id_umkm','$id_admin','$nomor','$nama_file',CURDATE(),'$hash_final')
");

$tipe_transaksi = 'surat_pengantar_terbit';

mysqli_query($conn, "
  INSERT INTO tbl_transaksi_blockchain
  (id_umkm, tipe_transaksi, hash_tx, tanggal_tx)
  VALUES
  ('$id_umkm', '$tipe_transaksi', '$hash_final', NOW())
");

/* REDIRECT */
$_SESSION['alert'] = 'legalisasi_sukses';
header("Location: ../public/admin/detail_umkm.php?id=$id_umkm");
exit;
