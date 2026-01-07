<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../../config/connect.php';

use Dompdf\Dompdf;
require '../../vendor/autoload.php';

// Cek login admin
if (!isset($_SESSION['admin'])) {
    header("Location: ../../login.php");
    exit();
}

// Filter
$filter = $_GET['filter'] ?? '';
$bulan  = $_GET['bulan'] ?? '';
$tahun  = date('Y');

$where = "";
if ($filter == '7hari') {
    $where = "WHERE p.tanggal_pinjam_222274 >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($filter == '30hari') {
    $where = "WHERE p.tanggal_pinjam_222274 >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
} elseif ($filter == 'bulan_ini') {
    $where = "WHERE MONTH(p.tanggal_pinjam_222274)=MONTH(CURDATE()) AND YEAR(p.tanggal_pinjam_222274)=YEAR(CURDATE())";
} elseif ($filter == 'bulan' && $bulan && $bulan >=1 && $bulan <= 12) {
    $where = "WHERE MONTH(p.tanggal_pinjam_222274)='$bulan' AND YEAR(p.tanggal_pinjam_222274)='$tahun'";
}

// Ambil data
$laporan_list = $conn->query("
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
");

// Pakai logo online
$logoURL = 'https://upload.wikimedia.org/wikipedia/commons/a/ab/Logo_TV_2015.png'; // ganti dengan URL logo kamu

// HTML PDF
$html = '
<html>
<head>
<style>
body { font-family: sans-serif; font-size:12px; }
table { border-collapse: collapse; width: 100%; margin-top: 20px;}
table, th, td { border:1px solid black; }
th, td { padding:5px; text-align:center; }
th { background-color: #d1e7fd; }
.badge { padding:2px 5px; color:white; border-radius:3px; }
.dipinjam { background-color: #ffc107; }
.dikembalikan { background-color: #198754; }
.dibatalkan { background-color: #dc3545; }
</style>
</head>
<body>

<div style="text-align:center;">
    <img src="'.$logoURL.'" width="80" alt="Logo"><br>
    <h2>Laporan Peminjaman Buku</h2>
</div>

<table>
<tr>
    <th>No</th>
    <th>Anggota</th>
    <th>Buku</th>
    <th>Tgl Pinjam</th>
    <th>Tgl Dikembalikan</th>
    <th>Status</th>
</tr>';

$no = 1;
if ($laporan_list->num_rows > 0) {
    while ($row = $laporan_list->fetch_assoc()) {
        $statusClass = [
            'dipinjam' => 'dipinjam',
            'dikembalikan' => 'dikembalikan',
            'dibatalkan' => 'dibatalkan'
        ][$row['status_222274']] ?? 'secondary';

        $html .= "<tr>
            <td>{$no}</td>
            <td>".htmlspecialchars($row['nama_anggota'])."</td>
            <td>".htmlspecialchars($row['judul_buku'])."</td>
            <td>{$row['tanggal_pinjam_222274']}</td>
            <td>".($row['tanggal_dikembalikan_222274'] ?? '-')."</td>
            <td><span class='badge {$statusClass}'>".ucfirst($row['status_222274'])."</span></td>
        </tr>";
        $no++;
    }
} else {
    $html .= '<tr><td colspan="6">Data tidak ditemukan</td></tr>';
}

$html .= '</table></body></html>';

// Generate PDF
$dompdf = new Dompdf();
$dompdf->set_option('isRemoteEnabled', true); // wajib agar bisa load logo online
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("laporan_peminjaman.pdf", ["Attachment" => false]);
