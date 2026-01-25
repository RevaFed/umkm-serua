<?php
require_once "../assets/plugins/fpdf/fpdf.php";
require_once "../config/database.php";

$id_umkm = (int)($_GET['id'] ?? 0);
if ($id_umkm === 0) exit("ID tidak valid");

/* DATA UMKM + WARGA + LEGALISASI */
$q = mysqli_query($conn, "
  SELECT 
    u.nama_usaha, u.jenis_usaha,
    w.nama_lengkap, w.nik, w.alamat,
    l.nomor_surat, l.tanggal_terbit
  FROM tbl_umkm u
  JOIN tbl_warga w ON u.id_warga = w.id_warga
  JOIN tbl_legalisasi l ON u.id_umkm = l.id_umkm
  WHERE u.id_umkm = '$id_umkm'
");

$data = mysqli_fetch_assoc($q);
if (!$data) exit("Data tidak ditemukan");

/* INIT PDF */
$pdf = new FPDF();
$pdf->AddPage();

/* HEADER */
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,7,'PEMERINTAH KELURAHAN SERUA',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,6,'KECAMATAN CIPUTAT',0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,6,'SURAT PENGANTAR USAHA MIKRO',0,1,'C');
$pdf->Ln(3);

$pdf->SetFont('Arial','',11);
$pdf->Cell(0,6,'Nomor: '.$data['nomor_surat'],0,1,'C');
$pdf->Ln(8);

/* ISI */
$pdf->SetFont('Arial','',11);
$pdf->MultiCell(0,7,
"Dengan ini menerangkan bahwa:\n\n".
"Nama          : {$data['nama_lengkap']}\n".
"NIK           : {$data['nik']}\n".
"Alamat        : {$data['alamat']}\n\n".
"Adalah benar warga Kelurahan Serua yang memiliki usaha:\n\n".
"Nama Usaha    : {$data['nama_usaha']}\n".
"Jenis Usaha   : {$data['jenis_usaha']}\n\n".
"Surat ini dibuat sebagai surat pengantar untuk keperluan perizinan usaha mikro.\n\n".
"Demikian surat ini dibuat agar dapat dipergunakan sebagaimana mestinya."
);

$pdf->Ln(10);

/* TTD */
$pdf->Cell(0,6,'Serua, '.date('d F Y', strtotime($data['tanggal_terbit'])),0,1,'R');
$pdf->Ln(15);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,6,'Ketua RW',0,1,'R');
$pdf->Ln(20);
$pdf->Cell(0,6,'( ................................ )',0,1,'R');

/* SAVE FILE */
$namaFile = "surat_umkm_{$id_umkm}.pdf";
$path = "../uploads/surat/".$namaFile;
$pdf->Output('F', $path);

/* UPDATE FILE SURAT (JIKA BELUM) */
mysqli_query($conn, "
  UPDATE tbl_legalisasi
  SET file_surat = '$namaFile'
  WHERE id_umkm = '$id_umkm'
");

/* REDIRECT */
header("Location: ../uploads/surat/".$namaFile);
exit;
