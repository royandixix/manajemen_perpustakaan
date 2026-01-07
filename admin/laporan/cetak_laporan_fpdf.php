<?php
// Tampilkan semua error (debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../../config/connect.php';

// Cek login admin
if (!isset($_SESSION['admin'])) {
    header("Location: ../../login.php");
    exit();
}

// Include Composer Autoload (FPDF sudah lewat composer)
require '../../vendor/autoload.php';

// Ambil filter
$filter = $_GET['filter'] ?? '';
$bulan  = $_GET['bulan'] ?? '';
$tahun  = date('Y');

$where = "";
$periode_text = "Semua Data";

if ($filter == '7hari') {
    $where = "WHERE p.tanggal_pinjam_222274 >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    $periode_text = "7 Hari Terakhir";
} elseif ($filter == '30hari') {
    $where = "WHERE p.tanggal_pinjam_222274 >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    $periode_text = "30 Hari Terakhir";
} elseif ($filter == 'bulan_ini') {
    $where = "WHERE MONTH(p.tanggal_pinjam_222274)=MONTH(CURDATE()) AND YEAR(p.tanggal_pinjam_222274)=YEAR(CURDATE())";
    $periode_text = "Bulan " . date('F Y');
} elseif ($filter == 'bulan' && $bulan && $bulan >= 1 && $bulan <= 12) {
    $where = "WHERE MONTH(p.tanggal_pinjam_222274)='$bulan' AND YEAR(p.tanggal_pinjam_222274)='$tahun'";
    $bulan_nama = date('F', mktime(0, 0, 0, $bulan, 1));
    $periode_text = "Bulan $bulan_nama $tahun";
}

// Ambil data laporan
$query = "
    SELECT p.tanggal_pinjam_222274, p.status_222274,
           a.nama_222274 AS nama_anggota,
           b.judul_222274 AS judul_buku,
           k.tanggal_dikembalikan_222274
    FROM peminjaman_222274 p
    JOIN anggota_222274 a ON p.id_anggota_222274 = a.id_anggota_222274
    JOIN buku_222274 b ON p.id_buku_222274 = b.id_buku_222274
    LEFT JOIN pengembalian_222274 k ON p.id_peminjaman_222274 = k.id_peminjaman_222274
    $where
    ORDER BY p.tanggal_pinjam_222274 DESC
";
$result = $conn->query($query);

// Hitung statistik
$total = $result->num_rows;
$stat_query = "
    SELECT 
        SUM(CASE WHEN p.status_222274='dipinjam' THEN 1 ELSE 0 END) as dipinjam,
        SUM(CASE WHEN p.status_222274='dikembalikan' THEN 1 ELSE 0 END) as dikembalikan,
        SUM(CASE WHEN p.status_222274='dibatalkan' THEN 1 ELSE 0 END) as dibatalkan
    FROM peminjaman_222274 p
    $where
";
$stat_result = $conn->query($stat_query);
$stats = $stat_result->fetch_assoc();

// Reset pointer result
$result->data_seek(0);

// Path logo
$logo = realpath(__DIR__ . '/../../assets/images/logo.jpg');

// Buat PDF dengan class custom
class PDF extends FPDF {
    private $periode;
    private $logo_path;
    
    function __construct($periode = '', $logo = '') {
        parent::__construct('L', 'mm', 'A4');
        $this->periode = $periode;
        $this->logo_path = $logo;
    }
    
    // Header
    function Header() {
        // Logo
        if(file_exists($this->logo_path)){
            $this->Image($this->logo_path, 15, 10, 25);
        }
        
        // Judul Institusi
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(33, 37, 41);
        $this->Cell(0, 8, 'PERPUSTAKAAN DIGITAL', 0, 1, 'C');
        
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(108, 117, 125);
        $this->Cell(0, 5, 'Jl. Contoh No. 123, Manado, Sulawesi Utara', 0, 1, 'C');
        $this->Cell(0, 5, 'Telp: (0431) 123456 | Email: perpustakaan@example.com', 0, 1, 'C');
        
        // Garis pemisah
        $this->SetLineWidth(0.8);
        $this->SetDrawColor(0, 123, 255);
        $this->Line(15, 35, 282, 35);
        $this->SetLineWidth(0.3);
        $this->Line(15, 36, 282, 36);
        
        $this->Ln(5);
        
        // Judul Laporan
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(33, 37, 41);
        $this->Cell(0, 8, 'LAPORAN PEMINJAMAN BUKU', 0, 1, 'C');
        
        // Periode
        $this->SetFont('Arial', 'I', 10);
        $this->SetTextColor(108, 117, 125);
        $this->Cell(0, 6, 'Periode: ' . $this->periode, 0, 1, 'C');
        
        // Tanggal cetak
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, 'Dicetak pada: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
        
        $this->Ln(5);
    }

    // Footer
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(108, 117, 125);
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    // Colored table
    function FancyTable($header, $data, $w) {
        // Header tabel
        $this->SetFillColor(0, 123, 255);
        $this->SetTextColor(255, 255, 255);
        $this->SetDrawColor(0, 123, 255);
        $this->SetLineWidth(0.3);
        $this->SetFont('Arial', 'B', 10);
        
        for($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();
        
        // Data tabel
        $this->SetFillColor(248, 249, 250);
        $this->SetTextColor(33, 37, 41);
        $this->SetFont('Arial', '', 9);
        $this->SetDrawColor(222, 226, 230);
        
        $fill = false;
        $no = 1;
        
        foreach($data as $row) {
            // Cek tinggi baris (untuk teks panjang)
            $nb = 0;
            $nb = max($nb, $this->NbLines($w[1], $row['nama_anggota']));
            $nb = max($nb, $this->NbLines($w[2], $row['judul_buku']));
            $h = 6 * $nb;
            
            // Cek page break
            $this->CheckPageBreak($h);
            
            // Draw cells
            $x = $this->GetX();
            $y = $this->GetY();
            
            // No
            $this->Rect($x, $y, $w[0], $h, 'D');
            $this->Cell($w[0], $h, $no, 0, 0, 'C', $fill);
            
            // Nama Anggota
            $x = $this->GetX();
            $this->MultiCell($w[1], 6, $row['nama_anggota'], 'LR', 'L', $fill);
            $this->SetXY($x + $w[1], $y);
            
            // Judul Buku
            $x = $this->GetX();
            $this->MultiCell($w[2], 6, $row['judul_buku'], 'LR', 'L', $fill);
            $this->SetXY($x + $w[2], $y);
            
            // Tanggal Pinjam
            $this->Cell($w[3], $h, date('d/m/Y', strtotime($row['tanggal_pinjam_222274'])), 'LR', 0, 'C', $fill);
            
            // Tanggal Kembali
            $tgl_kembali = $row['tanggal_dikembalikan_222274'] 
                ? date('d/m/Y', strtotime($row['tanggal_dikembalikan_222274'])) 
                : '-';
            $this->Cell($w[4], $h, $tgl_kembali, 'LR', 0, 'C', $fill);
            
            // Status dengan warna
            $status = $row['status_222274'];
            $status_text = ucfirst($status);
            
            if($status == 'dipinjam') {
                $this->SetFillColor(255, 243, 205);
                $this->SetTextColor(133, 100, 4);
            } elseif($status == 'dikembalikan') {
                $this->SetFillColor(209, 231, 221);
                $this->SetTextColor(15, 81, 50);
            } elseif($status == 'dibatalkan') {
                $this->SetFillColor(248, 215, 218);
                $this->SetTextColor(132, 32, 41);
            } else {
                $this->SetFillColor(233, 236, 239);
                $this->SetTextColor(73, 80, 87);
            }
            
            $this->Cell($w[5], $h, $status_text, 'LR', 0, 'C', true);
            
            // Reset color
            $this->SetFillColor(248, 249, 250);
            $this->SetTextColor(33, 37, 41);
            
            $this->Ln();
            $fill = !$fill;
            $no++;
        }
        
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    
    function CheckPageBreak($h) {
        if($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }
    
    function NbLines($w, $txt) {
        $cw = &$this->CurrentFont['cw'];
        if($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if($nb > 0 and $s[$nb-1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while($i < $nb) {
            $c = $s[$i];
            if($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if($l > $wmax) {
                if($sep == -1) {
                    if($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

// Buat PDF
$pdf = new PDF($periode_text, $logo);
$pdf->AliasNbPages();
$pdf->AddPage();

// Box Statistik
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(33, 37, 41);
$pdf->Cell(0, 8, 'Ringkasan Data', 0, 1, 'L');

$box_width = 60;
$box_height = 20;
$spacing = 7;

// Total Transaksi
$pdf->SetFillColor(13, 110, 253);
$pdf->SetTextColor(255, 255, 255);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $box_width, $box_height, 'F');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($box_width, 8, 'Total Transaksi', 0, 0, 'C');
$pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 16);
$x = $pdf->GetX();
$pdf->Cell($box_width, 12, $total, 0, 0, 'C');
$pdf->SetXY($x + $box_width + $spacing, $pdf->GetY() - 8);

// Dipinjam
$pdf->SetFillColor(255, 193, 7);
$pdf->SetTextColor(255, 255, 255);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $box_width, $box_height, 'F');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($box_width, 8, 'Sedang Dipinjam', 0, 0, 'C');
$pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 16);
$x = $pdf->GetX();
$pdf->Cell($box_width, 12, $stats['dipinjam'], 0, 0, 'C');
$pdf->SetXY($x + $box_width + $spacing, $pdf->GetY() - 8);

// Dikembalikan
$pdf->SetFillColor(25, 135, 84);
$pdf->SetTextColor(255, 255, 255);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $box_width, $box_height, 'F');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($box_width, 8, 'Dikembalikan', 0, 0, 'C');
$pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 16);
$x = $pdf->GetX();
$pdf->Cell($box_width, 12, $stats['dikembalikan'], 0, 0, 'C');
$pdf->SetXY($x + $box_width + $spacing, $pdf->GetY() - 8);

// Dibatalkan
$pdf->SetFillColor(220, 53, 69);
$pdf->SetTextColor(255, 255, 255);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $box_width, $box_height, 'F');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($box_width, 8, 'Dibatalkan', 0, 0, 'C');
$pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell($box_width, 12, $stats['dibatalkan'], 0, 0, 'C');

$pdf->Ln(20);

// Tabel Data
$pdf->SetTextColor(33, 37, 41);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'Detail Transaksi', 0, 1, 'L');

$header = ['No', 'Nama Anggota', 'Judul Buku', 'Tgl Pinjam', 'Tgl Kembali', 'Status'];
$w = [12, 55, 70, 30, 30, 30];

// Konversi result ke array
$data = [];
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

if(count($data) > 0) {
    $pdf->FancyTable($header, $data, $w);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->SetTextColor(108, 117, 125);
    $pdf->Cell(0, 10, 'Tidak ada data untuk periode yang dipilih', 0, 1, 'C');
}

// Tanda tangan (opsional)
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(33, 37, 41);
$pdf->Cell(0, 6, 'Manado, ' . date('d F Y'), 0, 1, 'R');
$pdf->Cell(0, 6, 'Kepala Perpustakaan', 0, 1, 'R');
$pdf->Ln(15);
$pdf->SetFont('Arial', 'BU', 10);
$pdf->Cell(0, 6, '(_____________________)', 0, 1, 'R');

// Output PDF
$filename = 'Laporan_Peminjaman_' . date('YmdHis') . '.pdf';
$pdf->Output('I', $filename);
?>