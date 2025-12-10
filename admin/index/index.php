<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

$namaAdmin = $_SESSION['admin']['nama'] ?? 'Admin';
require __DIR__ . '/../../config/connect.php';

// Query semua data untuk kartu statistik
$resBuku = mysqli_query($conn, "SELECT COUNT(*) AS total FROM buku_222274");
$resAnggota = mysqli_query($conn, "SELECT COUNT(*) AS total FROM anggota_222274");
$resDipinjam = mysqli_query($conn, "SELECT COUNT(*) AS total FROM peminjaman_222274 WHERE status_222274='dipinjam'");
$resKembali = mysqli_query($conn, "SELECT COUNT(*) AS total FROM peminjaman_222274 WHERE status_222274='dikembalikan'");

$totalBuku = mysqli_fetch_assoc($resBuku)['total'] ?? 0;
$totalAnggota = mysqli_fetch_assoc($resAnggota)['total'] ?? 0;
$totalDipinjam = mysqli_fetch_assoc($resDipinjam)['total'] ?? 0;
$totalKembali = mysqli_fetch_assoc($resKembali)['total'] ?? 0;

// Data peminjaman per bulan (6 bulan terakhir)
$dataBulan = [];
for($i = 5; $i >= 0; $i--) {
    $bulan = date('Y-m', strtotime("-$i months"));
    $queryBulan = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman_222274 WHERE DATE_FORMAT(tanggal_pinjam_222274, '%Y-%m') = '$bulan'");
    $result = mysqli_fetch_assoc($queryBulan);
    $dataBulan[] = [
        'bulan' => date('M Y', strtotime("-$i months")),
        'total' => $result['total'] ?? 0
    ];
}

// Data buku terpopuler (top 5)
// Coba ambil struktur tabel dulu untuk cek nama kolom
$checkColumns = mysqli_query($conn, "SHOW COLUMNS FROM buku_222274");
$columnNames = [];
while($col = mysqli_fetch_assoc($checkColumns)) {
    $columnNames[] = $col['Field'];
}

// Cari kolom ID yang tepat (bisa id_222274, id_buku_222274, atau lainnya)
$idColumn = 'id_222274';
if(in_array('id_buku_222274', $columnNames)) {
    $idColumn = 'id_buku_222274';
} elseif(in_array('kode_222274', $columnNames)) {
    $idColumn = 'kode_222274';
}

$queryPopuler = mysqli_query($conn, "
    SELECT b.judul_222274, COUNT(*) as jumlah_pinjam
    FROM peminjaman_222274 p
    JOIN buku_222274 b ON p.id_buku_222274 = b.$idColumn
    GROUP BY b.judul_222274
    ORDER BY jumlah_pinjam DESC
    LIMIT 5
");
$bukuPopuler = [];
if($queryPopuler) {
    while($row = mysqli_fetch_assoc($queryPopuler)) {
        $bukuPopuler[] = $row;
    }
}

// Jika masih kosong, buat data dummy agar tidak error
if(empty($bukuPopuler)) {
    $bukuPopuler = [
        ['judul_222274' => 'Belum ada data', 'jumlah_pinjam' => 0]
    ];
}

// Data peminjaman per hari (7 hari terakhir)
$dataHarian = [];
for($i = 6; $i >= 0; $i--) {
    $tanggal = date('Y-m-d', strtotime("-$i days"));
    $queryHarian = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman_222274 WHERE DATE(tanggal_pinjam_222274) = '$tanggal'");
    $result = mysqli_fetch_assoc($queryHarian);
    $dataHarian[] = [
        'hari' => date('D, d M', strtotime("-$i days")),
        'total' => $result['total'] ?? 0
    ];
}
?>

<?php include __DIR__ . '/../templates/header_sidebar.php'; ?>

<div id="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">
            <span class="material-symbols-outlined align-middle">dashboard</span> Dashboard Admin
        </h2>
        <span class="text-muted">Selamat datang, <strong><?= htmlspecialchars($namaAdmin, ENT_QUOTES, 'UTF-8') ?></strong></span>
    </div>

    <!-- Kartu Statistik dengan Gradient -->
    <div class="row g-4 mb-4">
        <?php
        $cards = [
            ['icon'=>'menu_book','title'=>'Total Buku','count'=>$totalBuku,'color'=>'primary','gradient'=>'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'],
            ['icon'=>'group','title'=>'Total Anggota','count'=>$totalAnggota,'color'=>'success','gradient'=>'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'],
            ['icon'=>'assignment_return','title'=>'Buku Dipinjam','count'=>$totalDipinjam,'color'=>'warning','gradient'=>'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'],
            ['icon'=>'check_circle','title'=>'Dikembalikan','count'=>$totalKembali,'color'=>'info','gradient'=>'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)'],
        ];

        foreach($cards as $card):
        ?>
        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card shadow-sm border-0 h-100" style="background: <?= $card['gradient'] ?>;">
                <div class="card-body text-white text-center p-4">
                    <span class="material-symbols-outlined fs-1 mb-3 d-block"><?= $card['icon'] ?></span>
                    <h6 class="fw-semibold mb-2 text-white-50"><?= $card['title'] ?></h6>
                    <h2 class="fw-bold counter mb-0" data-target="<?= $card['count'] ?>">0</h2>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Baris Pertama Diagram -->
    <div class="row g-4 mb-4">
        <!-- Grafik Peminjaman Per Bulan -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold mb-0">
                            <span class="material-symbols-outlined align-middle text-primary">trending_up</span>
                            Tren Peminjaman (6 Bulan Terakhir)
                        </h5>
                        <span class="badge bg-primary">Bulanan</span>
                    </div>
                    <canvas id="chartPeminjaman" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Diagram Status Peminjaman -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-4">
                        <span class="material-symbols-outlined align-middle text-success">donut_small</span>
                        Status Peminjaman
                    </h5>
                    <canvas id="chartStatus" height="200"></canvas>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded" style="background: rgba(102, 126, 234, 0.1);">
                            <span><i class="bi bi-circle-fill text-primary me-2"></i>Dipinjam</span>
                            <strong class="text-primary"><?= $totalDipinjam ?></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background: rgba(67, 233, 123, 0.1);">
                            <span><i class="bi bi-circle-fill text-success me-2"></i>Dikembalikan</span>
                            <strong class="text-success"><?= $totalKembali ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Baris Kedua Diagram -->
    <div class="row g-4 mb-4">
        <!-- Buku Terpopuler -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold mb-0">
                            <span class="material-symbols-outlined align-middle text-danger">local_fire_department</span>
                            Buku Terpopuler
                        </h5>
                        <span class="badge bg-danger">Top 5</span>
                    </div>
                    <canvas id="chartBukuPopuler" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- Aktivitas Harian -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold mb-0">
                            <span class="material-symbols-outlined align-middle text-warning">calendar_today</span>
                            Aktivitas 7 Hari Terakhir
                        </h5>
                        <span class="badge bg-warning">Harian</span>
                    </div>
                    <canvas id="chartHarian" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Cepat -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-4">
                        <span class="material-symbols-outlined align-middle text-info">insights</span>
                        Ringkasan Statistik
                    </h5>
                    <div class="row text-center">
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 rounded" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <h3 class="text-white mb-1"><?= $totalBuku + $totalAnggota ?></h3>
                                <small class="text-white-50">Total Entitas</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 rounded" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <h3 class="text-white mb-1"><?= $totalDipinjam + $totalKembali ?></h3>
                                <small class="text-white-50">Total Peminjaman</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 rounded" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <h3 class="text-white mb-1"><?= $totalDipinjam > 0 ? number_format(($totalDipinjam / $totalBuku) * 100, 1) : 0 ?>%</h3>
                                <small class="text-white-50">Tingkat Peminjaman</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 rounded" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <h3 class="text-white mb-1"><?= ($totalDipinjam + $totalKembali) > 0 ? number_format(($totalKembali / ($totalDipinjam + $totalKembali)) * 100, 1) : 0 ?>%</h3>
                                <small class="text-white-50">Tingkat Pengembalian</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5 mb-3 text-secondary small">
        &copy; <?= date('Y') ?> PerpustakaanKu. Semua hak dilindungi.
    </footer>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>

<!-- Bootstrap JS & Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
.dashboard-card {
    border-radius: 16px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.dashboard-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.25) !important;
}

.card {
    border-radius: 16px;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.12) !important;
}

.material-symbols-outlined {
    font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 48;
}

.badge {
    padding: 0.5rem 1rem;
    font-weight: 600;
}

canvas {
    max-height: 300px;
}
</style>

<script>
// Data dari PHP
const dataPeminjaman = <?= json_encode($dataBulan) ?>;
const bukuPopuler = <?= json_encode($bukuPopuler) ?>;
const dataHarian = <?= json_encode($dataHarian) ?>;

// Counter Animation dengan Easing
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.counter');
    
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000;
        const startTime = performance.now();
        
        const updateCounter = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function (ease out cubic)
            const easeOut = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(easeOut * target);
            
            counter.textContent = current;
            
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target;
            }
        };
        
        requestAnimationFrame(updateCounter);
    });
});

// Konfigurasi Chart.js Global
Chart.defaults.font.family = "'Segoe UI', 'Roboto', sans-serif";
Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0,0,0,0.85)';
Chart.defaults.plugins.tooltip.padding = 12;
Chart.defaults.plugins.tooltip.cornerRadius = 8;
Chart.defaults.plugins.tooltip.titleFont = { size: 14, weight: 'bold' };
Chart.defaults.plugins.tooltip.bodyFont = { size: 13 };

// 1. Chart Peminjaman Per Bulan (Area Line Chart)
const ctxPeminjaman = document.getElementById('chartPeminjaman').getContext('2d');
const gradientPeminjaman = ctxPeminjaman.createLinearGradient(0, 0, 0, 300);
gradientPeminjaman.addColorStop(0, 'rgba(102, 126, 234, 0.4)');
gradientPeminjaman.addColorStop(1, 'rgba(102, 126, 234, 0.01)');

new Chart(ctxPeminjaman, {
    type: 'line',
    data: {
        labels: dataPeminjaman.map(d => d.bulan),
        datasets: [{
            label: 'Jumlah Peminjaman',
            data: dataPeminjaman.map(d => d.total),
            borderColor: '#667eea',
            backgroundColor: gradientPeminjaman,
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointBackgroundColor: '#667eea',
            pointBorderColor: '#fff',
            pointBorderWidth: 3,
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: '#667eea'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Peminjaman: ' + context.parsed.y + ' buku';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    font: { size: 12 }
                },
                grid: { color: 'rgba(0,0,0,0.06)' }
            },
            x: {
                ticks: { font: { size: 11 } },
                grid: { display: false }
            }
        }
    }
});

// 2. Chart Status Peminjaman (Doughnut)
const ctxStatus = document.getElementById('chartStatus').getContext('2d');
new Chart(ctxStatus, {
    type: 'doughnut',
    data: {
        labels: ['Dipinjam', 'Dikembalikan'],
        datasets: [{
            data: [<?= $totalDipinjam ?>, <?= $totalKembali ?>],
            backgroundColor: ['#667eea', '#43e97b'],
            borderWidth: 0,
            hoverOffset: 20
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        },
        cutout: '70%'
    }
});

// 3. Chart Buku Terpopuler (Horizontal Bar)
const ctxBukuPopuler = document.getElementById('chartBukuPopuler').getContext('2d');
new Chart(ctxBukuPopuler, {
    type: 'bar',
    data: {
        labels: bukuPopuler.map(b => {
            const judul = b.judul_222274 || 'Tidak Ada Judul';
            return judul.length > 30 ? judul.substring(0, 30) + '...' : judul;
        }),
        datasets: [{
            label: 'Jumlah Dipinjam',
            data: bukuPopuler.map(b => b.jumlah_pinjam),
            backgroundColor: [
                'rgba(102, 126, 234, 0.8)',
                'rgba(245, 87, 108, 0.8)',
                'rgba(79, 172, 254, 0.8)',
                'rgba(67, 233, 123, 0.8)',
                'rgba(251, 197, 49, 0.8)'
            ],
            borderColor: [
                '#667eea',
                '#f5576c',
                '#4facfe',
                '#43e97b',
                '#fbc531'
            ],
            borderWidth: 2,
            borderRadius: 10,
            barThickness: 30
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Dipinjam: ' + context.parsed.x + ' kali';
                    }
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    font: { size: 11 }
                },
                grid: { color: 'rgba(0,0,0,0.06)' }
            },
            y: {
                ticks: { font: { size: 11 } },
                grid: { display: false }
            }
        }
    }
});

// 4. Chart Aktivitas Harian (Bar Chart)
const ctxHarian = document.getElementById('chartHarian').getContext('2d');
const gradientHarian = ctxHarian.createLinearGradient(0, 0, 0, 300);
gradientHarian.addColorStop(0, 'rgba(251, 197, 49, 0.8)');
gradientHarian.addColorStop(1, 'rgba(251, 197, 49, 0.2)');

new Chart(ctxHarian, {
    type: 'bar',
    data: {
        labels: dataHarian.map(d => d.hari),
        datasets: [{
            label: 'Peminjaman',
            data: dataHarian.map(d => d.total),
            backgroundColor: gradientHarian,
            borderColor: '#fbc531',
            borderWidth: 2,
            borderRadius: 8,
            barThickness: 40
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Peminjaman: ' + context.parsed.y + ' buku';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    font: { size: 11 }
                },
                grid: { color: 'rgba(0,0,0,0.06)' }
            },
            x: {
                ticks: { font: { size: 10 } },
                grid: { display: false }
            }
        }
    }
});
</script>