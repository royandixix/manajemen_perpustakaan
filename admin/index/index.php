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
?>

<?php include __DIR__ . '/../templates/header_sidebar.php'; ?>

<div id="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">
            <span class="material-symbols-outlined align-middle">dashboard</span> Dashboard Admin
        </h2>
        <span class="text-muted">Selamat datang, <strong><?= htmlspecialchars($namaAdmin, ENT_QUOTES, 'UTF-8') ?></strong></span>
    </div>

    <div class="row g-4">
        <?php
        // Query semua data
        $resBuku = mysqli_query($conn, "SELECT COUNT(*) AS total FROM buku_222274");
        $resAnggota = mysqli_query($conn, "SELECT COUNT(*) AS total FROM anggota_222274");
        $resDipinjam = mysqli_query($conn, "SELECT COUNT(*) AS total FROM peminjaman_222274 WHERE status_222274='dipinjam'");
        
        $totalBuku = mysqli_fetch_assoc($resBuku)['total'] ?? 0;
        $totalAnggota = mysqli_fetch_assoc($resAnggota)['total'] ?? 0;
        $totalDipinjam = mysqli_fetch_assoc($resDipinjam)['total'] ?? 0;

        $cards = [
            ['icon'=>'menu_book','title'=>'Total Buku','count'=>$totalBuku,'color'=>'primary'],
            ['icon'=>'group','title'=>'Total Anggota','count'=>$totalAnggota,'color'=>'success'],
            ['icon'=>'assignment_return','title'=>'Buku Dipinjam','count'=>$totalDipinjam,'color'=>'warning'],
        ];

        foreach($cards as $card):
        ?>
        <div class="col-md-4">
            <div class="card text-center p-4 shadow-sm border-0 dashboard-card">
                <span class="material-symbols-outlined fs-1 mb-2 text-<?= $card['color'] ?>"><?= $card['icon'] ?></span>
                <h5 class="fw-semibold"><?= $card['title'] ?></h5>
                <h2 class="fw-bold text-<?= $card['color'] ?> counter" data-target="<?= $card['count'] ?>">0</h2>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <footer class="text-center mt-5 mb-3 text-secondary small">
        &copy; <?= date('Y') ?> PerpustakaanKu. Semua hak dilindungi.
    </footer>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>

<!-- Bootstrap JS & dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<script src="css/js/jquery-3.3.1.min.js"></script>
<script src="css/js/popper.min.js"></script>
<script src="css/js/bootstrap.min.js"></script>
<script src="css/js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Counter JS -->
<script src="../js/index.js"></script>

<style>
.dashboard-card {
    border-radius: 12px;
    transition: transform 0.3s, box-shadow 0.3s;
}
.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}
</style>
