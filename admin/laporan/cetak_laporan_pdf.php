<?php
require '../../vendor/autoload.php';
include '../../config/connect.php';

use Dompdf\Dompdf;

// Ambil data laporan
$data = $conn->query("
    SELECT p.*, a.nama_222274 AS nama_anggota, b.judul_222274 AS judul_buku
    FROM peminjaman_222274 p
    JOIN anggota_222274 a ON p.id_anggota_222274 = a.id_anggota_222274
    JOIN buku_222274 b ON p.id_buku_222274 = b.id_buku_222274
    ORDER BY p.tanggal_pinjam_222274 DESC
");

$html = "
<h2 style='text-align:center'>Laporan Peminjaman Buku</h2>
<table width='100%' border='1' cellspacing='0' cellpadding='6'>
<tr style='background:#dce6f7; font-weight:bold'>
    <td>ID</td>
    <td>Anggota</td>
    <td>Buku</td>
    <td>Tgl Pinjam</td>
    <td>Tgl Kembali</td>
    <td>Status</td>
</tr>";

while ($row = $data->fetch_assoc()) {
    $html .= "
    <tr>
        <td>{$row['id_peminjaman_222274']}</td>
        <td>{$row['nama_anggota']}</td>
        <td>{$row['judul_buku']}</td>
        <td>{$row['tanggal_pinjam_222274']}</td>
        <td>" . ($row['tanggal_kembali_222274'] ?: '-') . "</td>
        <td>{$row['status_222274']}</td>
    </tr>";
}

$html .= "</table>";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("laporan_peminjaman.pdf", ["Attachment" => false]);
exit;
