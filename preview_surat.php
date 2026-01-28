<?php
require_once __DIR__ . "/controlls/../config/database.php";
require_once __DIR__ . "/controlls/../assets/plugins/fpdf/fpdf.php";

/* ===============================
   MODE & VALIDASI
================================ */
$id_umkm = (int)($_GET['id'] ?? 0);
$mode    = $_GET['mode'] ?? 'preview'; // preview | save

if ($id_umkm === 0) {
    exit("ID tidak valid");
}

function tanggalIndo($tanggal = null)
{
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April',
             'Mei', 'Juni', 'Juli', 'Agustus',
             'September', 'Oktober', 'November', 'Desember'
    ];

    $tgl = $tanggal ? date('Y-m-d', strtotime($tanggal)) : date('Y-m-d');
    $pecah = explode('-', $tgl);

    return $pecah[2].' '.$bulan[(int)$pecah[1]].' '.$pecah[0];
}


/* ===============================
   AMBIL DATA
================================ */
$q = mysqli_query($conn, "
    SELECT 
        w.nama_lengkap,
        w.nik,
        w.tanggal_lahir,
        w.agama,
        w.status_perkawinan,
        w.alamat,
        l.nomor_surat,
        l.tanggal_terbit,

        -- RT
        (SELECT a.nama FROM tbl_admin a 
         WHERE a.role='rt' AND a.rt='01' AND a.rw='02' LIMIT 1) AS nama_rt,
        (SELECT a.ttd FROM tbl_admin a 
         WHERE a.role='rt' AND a.rt='01' AND a.rw='02' LIMIT 1) AS ttd_rt,
        (SELECT a.stempel FROM tbl_admin a 
         WHERE a.role='rt' AND a.rt='01' AND a.rw='02' LIMIT 1) AS stempel_rt,

        -- RW
        (SELECT a.nama FROM tbl_admin a 
         WHERE a.role='rw' AND a.rw='02' LIMIT 1) AS nama_rw,
        (SELECT a.ttd FROM tbl_admin a 
         WHERE a.role='rw' AND a.rw='02' LIMIT 1) AS ttd_rw,
        (SELECT a.stempel FROM tbl_admin a 
         WHERE a.role='rw' AND a.rw='02' LIMIT 1) AS stempel_rw

    FROM tbl_umkm u
    JOIN tbl_warga w ON u.id_warga = w.id_warga
    JOIN tbl_legalisasi l ON u.id_umkm = l.id_umkm
    WHERE u.id_umkm = '$id_umkm'
");


$data = mysqli_fetch_assoc($q);
if (!$data) {
    exit("Data tidak ditemukan");
}

/* ===============================
   NORMALISASI DATA (ANTI ERROR)
================================ */
$data['tanggal_lahir']     = empty($data['tanggal_lahir']) ? '-' : $data['tanggal_lahir'];
$data['agama']             = empty($data['agama']) ? '-' : $data['agama'];
$data['status_perkawinan'] = empty($data['status_perkawinan']) ? '-' : $data['status_perkawinan'];

/* ===============================
   GENERATE PDF
================================ */
$pdf = new FPDF();
$pdf->SetMargins(0, 0, 0);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();

/* ===== KOP ===== */
$pdf->Image(__DIR__."/assets/img/kop_surat.png", 0, -56, 210);
$pdf->SetY(50);

/* ===== JUDUL ===== */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,5,'SURAT PENGANTAR',0,1,'C');

$y = $pdf->GetY();
$pdf->Line(80, $y, 130, $y);
$pdf->Ln(3);

$pdf->SetFont('Arial','',11);
$pdf->Cell(0,5,'Nomor : '.$data['nomor_surat'],0,1,'C');
$pdf->Ln(6);

/* ===== ISI ===== */
$pdf->SetLeftMargin(25);
$pdf->SetRightMargin(25);
$pdf->SetFont('Arial','',11);

$maksud = "Sebagai pengantar untuk keperluan administrasi perizinan usaha.";

$pdf->MultiCell(0,7,
    "Yang bertanda tangan di bawah ini Ketua RT 01 dan RW 02 Kelurahan Serua, ".
    "Kecamatan Ciputat, Kota Tangerang Selatan, dengan ini menerangkan bahwa:"
);

$pdf->Ln(5);

$pdf->Cell(50,7,'Nama',0,0);
$pdf->Cell(0,7,': '.$data['nama_lengkap'],0,1);

$pdf->Cell(50,7,'NIK',0,0);
$pdf->Cell(0,7,': '.$data['nik'],0,1);

$pdf->Cell(50,7,'Tanggal Lahir',0,0);
$pdf->Cell(0,7,': '.$data['tanggal_lahir'],0,1);

$pdf->Cell(50,7,'Agama',0,0);
$pdf->Cell(0,7,': '.$data['agama'],0,1);

$pdf->Cell(50,7,'Status Perkawinan',0,0);
$pdf->Cell(0,7,': '.$data['status_perkawinan'],0,1);

$pdf->Cell(50,7,'Alamat',0,0);
$pdf->MultiCell(0,7,': '.$data['alamat']);

$pdf->Ln(5);

$pdf->Cell(50,7,'Maksud / Tujuan',0,0);
$pdf->MultiCell(0,7,': '.$maksud);

$pdf->Ln(8);

$pdf->MultiCell(0,7,
    "Demikian surat pengantar ini kami buat untuk dipergunakan sebagaimana mestinya."
);

/* ===============================
   TANDA TANGAN RT & RW (FINAL FIX)
================================ */

$pdf->Ln(18);

/* ===============================
   KONFIGURASI POSISI
================================ */
$xRW        = 25;    // kiri
$xRT        = 150;   // kanan
$lebarKolom = 60;
$jarakTTD   = 22;

/* ===============================
   SIMPAN Y AWAL (KUNCI SEJAJAR)
================================ */
$yTTD = $pdf->GetY();

/* ===============================
   RT (KANAN)
================================ */
$pdf->SetXY($xRT, $yTTD);
$pdf->SetFont('Arial','',11);
$pdf->Cell($lebarKolom, 6, 'Serua, '.tanggalIndo(), 0, 1, 'L');

$pdf->SetX($xRT);
$pdf->SetFont('Arial','B',11);
$pdf->Cell($lebarKolom, 6, 'Mengetahui,', 0, 1, 'L');

$pdf->SetX($xRT);
$pdf->Cell($lebarKolom, 6, 'Ketua Rukun Tetangga 01', 0, 1, 'L');

/* ===============================
   RW (KIRI) — SEJAJAR RT
================================ */
$pdf->SetXY($xRW, $yTTD + 12);
$pdf->SetFont('Arial','B',11);
$pdf->Cell($lebarKolom, 6, 'Ketua Rukun Warga 02', 0, 1, 'L');

/* ===============================
   TURUN BERSAMA UNTUK NAMA
================================ */
$yNama = $yTTD + 12 + $jarakTTD;

/* NAMA RT */
$pdf->SetXY($xRT, $yNama);
$pdf->SetFont('Arial','BU',11);
$pdf->Cell($lebarKolom, 6, $data['nama_rt'], 0, 1, 'C');

/* NAMA RW */
$pdf->SetXY($xRW, $yNama);
$pdf->Cell($lebarKolom, 6, $data['nama_rw'], 0, 1, 'C');

$pdf->SetFont('Arial','',11);

/* ===============================
   STEMPEL & TTD (PAKAI Y SAMA)
================================ */

/* RW */
if (!empty($data['stempel_rw'])) {
    $pdf->Image(
        __DIR__."/uploads/stempel/".$data['stempel_rw'],
        25,
        $yNama - 30,
        99
    );
}

if (!empty($data['ttd_rw'])) {
    $pdf->Image(
        __DIR__."/uploads/ttd/".$data['ttd_rw'],
        32,
        $yNama - 25,
        30
    );
}

/* RT */
if (!empty($data['stempel_rt'])) {
    $pdf->Image(
        __DIR__."/uploads/stempel/".$data['stempel_rt'],
        110,
        $yNama - 30,
        90
    );
}

if (!empty($data['ttd_rt'])) {
    $pdf->Image(
        __DIR__."/uploads/ttd/".$data['ttd_rt'],
        160,
        $yNama - 25,
        30
    );
}


/* ===============================
   OUTPUT
================================ */
if ($mode === 'preview') {

    // === PREVIEW (TANPA SIMPAN) ===
    $pdf->Output('I', 'preview_surat_umkm.pdf');
    exit;

} else {

    // === SIMPAN FILE ===
    $folder = __DIR__ . "/uploads/surat";
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $namaFile = "surat_umkm_{$id_umkm}.pdf";
    $pathFile = $folder . "/" . $namaFile;

    $pdf->Output('F', $pathFile);

    // UPDATE DB
    mysqli_query($conn, "
        UPDATE tbl_legalisasi
        SET file_surat = '$namaFile'
        WHERE id_umkm = '$id_umkm'
    ");

    // REDIRECT KE FILE
    header("Location: uploads/surat/" . $namaFile);
    exit;
}
