<?php
require_once __DIR__ . "/../assets/plugins/fpdf/fpdf.php";

function generateSuratUMKM($data, $pathFile = '')
{
    $pdf = new FPDF();
    $data['tanggal_lahir'] = empty($data['tanggal_lahir']) ? '-' : $data['tanggal_lahir'];
$data['agama'] = empty($data['agama']) ? '-' : $data['agama'];
$data['status_perkawinan'] = empty($data['status_perkawinan']) ? '-' : $data['status_perkawinan'];


    // HILANGKAN MARGIN
    $pdf->SetMargins(0, 0, 0);
    $pdf->SetAutoPageBreak(false);
    $pdf->AddPage();

    /* ===== KOP ===== */
    $pdf->Image(__DIR__."/../assets/img/kop_surat.png", 0, -55, 210);
    $pdf->Ln(45);

 /* ===== JUDUL ===== */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,5,'SURAT PENGANTAR',0,1,'C');

/* garis bawah judul */
$y = $pdf->GetY();
$pdf->Line(80, $y, 130, $y);
$pdf->Ln(3);

$pdf->SetFont('Arial','',11);
$pdf->Cell(0,5,'Nomor : '.$data['nomor_surat'],0,1,'C');
    $pdf->Ln(6);


 /* ===== ISI ===== */
$pdf->SetFont('Arial','',11);
$pdf->SetLeftMargin(25);
$pdf->SetRightMargin(25);

/* fallback aman */
$tglLahir = $data['tanggal_lahir'] ?? '-';
$agama    = $data['agama'] ?? '-';
$status   = $data['status_perkawinan'] ?? '-';

/* tujuan DIKETIK PERMANEN */
$maksud = "Sebagai pengantar untuk keperluan administrasi perizinan usaha.";

$pdf->MultiCell(0,7,
    "Yang bertanda tangan di bawah ini Ketua RT dan RW Kelurahan Serua, ".
    "Kecamatan Ciputat, Kota Tangerang Selatan, dengan ini menerangkan bahwa:"
);

$pdf->Ln(5);

$pdf->Cell(50,7,'Nama',0,0);
$pdf->Cell(0,7,': '.$data['nama_lengkap'],0,1);

$pdf->Cell(50,7,'NIK',0,0);
$pdf->Cell(0,7,': '.$data['nik'],0,1);

$pdf->Cell(50,7,'Tanggal Lahir',0,0);
$pdf->Cell(0,7,': '.$tglLahir,0,1);

$pdf->Cell(50,7,'Agama',0,0);
$pdf->Cell(0,7,': '.$agama,0,1);

$pdf->Cell(50,7,'Status Perkawinan',0,0);
$pdf->Cell(0,7,': '.$status,0,1);

$pdf->Cell(50,7,'Alamat',0,0);
$pdf->MultiCell(0,7,': '.$data['alamat']);

$pdf->Ln(5);

$pdf->Cell(50,7,'Maksud / Tujuan',0,0);
$pdf->MultiCell(0,7,': '.$maksud);

$pdf->Ln(8);

$pdf->MultiCell(0,7,
    "Demikian surat pengantar ini kami buat untuk dipergunakan sebagaimana mestinya."
);


    /* ===== OUTPUT ===== */
    if ($pathFile === '') {
        // PREVIEW (INLINE)
        $pdf->Output('I', 'preview_surat_umkm.pdf');
    } else {
        // SIMPAN FILE
        $pdf->Output('F', $pathFile);
    }
}
