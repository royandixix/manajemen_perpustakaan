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

/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/
$filter = $_GET['filter'] ?? '';
$bulan  = $_GET['bulan'] ?? '';
$tahun  = date('Y');

$where = "";

// Validasi filter
if ($filter == '7hari') {
    $where = "WHERE p.tanggal_pinjam_222274 >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($filter == '30hari') {
    $where = "WHERE p.tanggal_pinjam_222274 >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
} elseif ($filter == 'bulan_ini') {
    $where = "WHERE MONTH(p.tanggal_pinjam_222274)=MONTH(CURDATE()) 
              AND YEAR(p.tanggal_pinjam_222274)=YEAR(CURDATE())";
} elseif ($filter == 'bulan' && $bulan && $bulan >= 1 && $bulan <= 12) {
    $where = "WHERE MONTH(p.tanggal_pinjam_222274)='$bulan' 
              AND YEAR(p.tanggal_pinjam_222274)='$tahun'";
}

/*
|--------------------------------------------------------------------------
| STATISTIK
|--------------------------------------------------------------------------
*/
$today = date('Y-m-d');
$week_start  = date('Y-m-d', strtotime("monday this week"));
$week_end    = date('Y-m-d', strtotime("sunday this week"));
$month_start = date('Y-m-01');
$month_end   = date('Y-m-t');

$hari_ini = $conn->query("SELECT COUNT(*) total FROM peminjaman_222274 WHERE tanggal_pinjam_222274='$today'")->fetch_assoc()['total'];
$minggu_ini = $conn->query("SELECT COUNT(*) total FROM peminjaman_222274 WHERE tanggal_pinjam_222274 BETWEEN '$week_start' AND '$week_end'")->fetch_assoc()['total'];
$bulan_ini = $conn->query("SELECT COUNT(*) total FROM peminjaman_222274 WHERE tanggal_pinjam_222274 BETWEEN '$month_start' AND '$month_end'")->fetch_assoc()['total'];
$total_semua = $conn->query("SELECT COUNT(*) total FROM peminjaman_222274")->fetch_assoc()['total'];

/*
|--------------------------------------------------------------------------
| DATA LAPORAN
|--------------------------------------------------------------------------
*/
$laporan_list = $conn->query("
    SELECT 
        p.id_peminjaman_222274,
        p.tanggal_pinjam_222274,
        p.status_222274,
        a.nama_222274 AS nama_anggota,
        b.judul_222274 AS judul_buku,
        k.tanggal_dikembalikan_222274
    FROM peminjaman_222274 p
    JOIN anggota_222274 a ON p.id_anggota_222274 = a.id_anggota_222274
    JOIN buku_222274 b ON p.id_buku_222274 = b.id_buku_222274
    LEFT JOIN pengembalian_222274 k 
        ON p.id_peminjaman_222274 = k.id_peminjaman_222274
    $where
    ORDER BY p.tanggal_pinjam_222274 DESC
");

include '../templates/header_sidebar.php';
?>

<div id="main-content" class="container mt-4">

    <h2 class="fw-bold text-primary mb-4">
        <span class="material-symbols-outlined align-middle me-1">bar_chart</span>
        Laporan Peminjaman Buku
    </h2>

    <!-- BUTTON FILTER -->
    <div class="mb-3">
        <a href="?filter=7hari" class="btn btn-outline-primary btn-sm">7 Hari Terakhir</a>
        <a href="?filter=30hari" class="btn btn-outline-success btn-sm">30 Hari Terakhir</a>
        <a href="?filter=bulan_ini" class="btn btn-outline-warning btn-sm">Bulan Ini</a>
    </div>

    <!-- PILIH BULAN -->
    <form method="GET" class="row g-3 mb-3">
        <input type="hidden" name="filter" value="bulan">
        <div class="col-md-4">
            <select name="bulan" class="form-control" required>
                <option value="">-- Pilih Bulan --</option>
                <?php
                $bulanNama = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember'
                ];
                foreach ($bulanNama as $i => $b) {
                    $sel = ($bulan == $i) ? 'selected' : '';
                    echo "<option value='$i' $sel>$b</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary">Tampilkan</button>
        </div>

        <?php if ($filter): ?>
            <div class="col-md-3">
                <a target="_blank"
                    href="cetak_laporan_fpdf.php?filter=<?= urlencode($filter) ?>&bulan=<?= urlencode($bulan) ?>"
                    class="btn btn-danger">
                    <span class="material-symbols-outlined align-middle">picture_as_pdf</span>
                    Cetak PDF
                </a>

            </div>
        <?php endif; ?>
    </form>

    <!-- STATISTIK -->
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card p-3 shadow-sm text-center border-start border-primary border-4">
                <h6>Hari Ini</h6>
                <h2 class="fw-bold text-primary"><?= $hari_ini ?></h2>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card p-3 shadow-sm text-center border-start border-success border-4">
                <h6>Minggu Ini</h6>
                <h2 class="fw-bold text-success"><?= $minggu_ini ?></h2>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card p-3 shadow-sm text-center border-start border-warning border-4">
                <h6>Bulan Ini</h6>
                <h2 class="fw-bold text-warning"><?= $bulan_ini ?></h2>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card p-3 shadow-sm text-center border-start border-dark border-4">
                <h6>Total Semua</h6>
                <h2 class="fw-bold text-dark"><?= $total_semua ?></h2>
            </div>
        </div>
    </div>

    <!-- TABEL LAPORAN -->
    <div class="card p-4 shadow-sm mt-4 mb-5">
        <h5 class="fw-bold mb-3">Tabel Laporan</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Dikembalikan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if ($laporan_list->num_rows > 0):
                        while ($row = $laporan_list->fetch_assoc()):
                    ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_anggota']) ?></td>
                                <td><?= htmlspecialchars($row['judul_buku']) ?></td>
                                <td><?= $row['tanggal_pinjam_222274'] ?></td>
                                <td><?= $row['tanggal_dikembalikan_222274'] ?? '-' ?></td>
                                <td>
                                    <?php
                                    $color = [
                                        'dipinjam' => 'warning',
                                        'dikembalikan' => 'success',
                                        'dibatalkan' => 'danger'
                                    ][$row['status_222274']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $color ?>">
                                        <?= ucfirst($row['status_222274']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php
                        endwhile;
                    else:
                        ?>
                        <tr>
                            <td colspan="6">Data tidak ditemukan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="text-center text-secondary mb-3">
        &copy; <?= date('Y') ?> PerpustakaanKu
    </footer>

</div>