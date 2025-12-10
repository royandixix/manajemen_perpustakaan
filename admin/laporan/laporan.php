<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../../config/connect.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

// Hari ini
$today = date('Y-m-d');

// Minggu ini
$week_start = date('Y-m-d', strtotime("monday this week"));
$week_end   = date('Y-m-d', strtotime("sunday this week"));

// Bulan ini
$month_start = date('Y-m-01');
$month_end   = date('Y-m-t');

// Hitung statistik
$hari_ini = $conn->query("SELECT COUNT(*) AS total FROM peminjaman_222274 WHERE tanggal_pinjam_222274 = '$today'")->fetch_assoc()['total'];
$minggu_ini = $conn->query("SELECT COUNT(*) AS total FROM peminjaman_222274 WHERE tanggal_pinjam_222274 BETWEEN '$week_start' AND '$week_end'")->fetch_assoc()['total'];
$bulan_ini = $conn->query("SELECT COUNT(*) AS total FROM peminjaman_222274 WHERE tanggal_pinjam_222274 BETWEEN '$month_start' AND '$month_end'")->fetch_assoc()['total'];
$total_semua = $conn->query("SELECT COUNT(*) AS total FROM peminjaman_222274")->fetch_assoc()['total'];
$return_today = $conn->query("SELECT COUNT(*) AS total FROM pengembalian_222274 WHERE tanggal_dikembalikan_222274 = '$today'")->fetch_assoc()['total'];

// List laporan
$laporan_list = $conn->query("
    SELECT p.*, a.nama_222274 AS nama_anggota, b.judul_222274 AS judul_buku
    FROM peminjaman_222274 p
    JOIN anggota_222274 a ON p.id_anggota_222274 = a.id_anggota_222274
    JOIN buku_222274 b ON p.id_buku_222274 = b.id_buku_222274
    ORDER BY p.tanggal_pinjam_222274 DESC
");

include '../templates/header_sidebar.php';
?>

<div id="main-content" class="container mt-4">

<h2 class="fw-bold text-primary mb-4">
    <span class="material-symbols-outlined align-middle me-1">bar_chart</span>
    Laporan Peminjaman Buku
</h2>

<!-- Tombol PDF -->
<a href="cetak_laporan_pdf.php" target="_blank" class="btn btn-danger mb-3">
    <span class="material-symbols-outlined align-middle">picture_as_pdf</span> Cetak PDF
</a>

<!-- STATISTIK -->
<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card p-3 shadow-sm text-center border-start border-primary border-4">
            <h6 class="text-secondary">Hari Ini</h6>
            <h2 class="fw-bold text-primary"><?= $hari_ini ?></h2>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card p-3 shadow-sm text-center border-start border-success border-4">
            <h6 class="text-secondary">Minggu Ini</h6>
            <h2 class="fw-bold text-success"><?= $minggu_ini ?></h2>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card p-3 shadow-sm text-center border-start border-warning border-4">
            <h6 class="text-secondary">Bulan Ini</h6>
            <h2 class="fw-bold text-warning"><?= $bulan_ini ?></h2>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card p-3 shadow-sm text-center border-start border-dark border-4">
            <h6 class="text-secondary">Total Semua</h6>
            <h2 class="fw-bold text-dark"><?= $total_semua ?></h2>
        </div>
    </div>
</div>

<!-- TABEL LAPORAN -->
<div class="card p-4 shadow-sm mt-4 mb-5">
    <h5 class="fw-bold mb-3">Tabel Laporan Peminjaman</h5>

    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Anggota</th>
                    <th>Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = $laporan_list->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_peminjaman_222274'] ?></td>
                    <td><?= htmlspecialchars($row['nama_anggota']) ?></td>
                    <td><?= htmlspecialchars($row['judul_buku']) ?></td>
                    <td><?= $row['tanggal_pinjam_222274'] ?></td>
                    <td><?= $row['tanggal_kembali_222274'] ?: "-" ?></td>

                    <td>
                        <?php 
                            $s = $row['status_222274'];
                            $color = [
                                "dipinjam" => "warning",
                                "dikembalikan" => "success",
                                "menunggu_konfirmasi_admin" => "primary",
                                "dibatalkan" => "danger"
                            ][$s] ?? "secondary";
                        ?>
                        <span class="badge bg-<?= $color ?>"><?= str_replace("_"," ", ucfirst($s)) ?></span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>

        </table>
    </div>
</div>

<footer class="text-center mt-4 mb-2 text-secondary">
    &copy; <?= date('Y') ?> PerpustakaanKu
</footer>

</div>
</body>
</html>
