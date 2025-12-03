<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$pageTitle = "Katalog Buku - PerpustakaanKu";

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$namaUser = $_SESSION['user']['nama'] ?? 'Pengguna';

require '../config/connect.php';
include 'templates/header.php';
include 'templates/navbar.php';
?>

<style>
/* UI tetap sama seperti versi sebelumnya */
body { background-color: #f8f9fa; font-family: "Inter", sans-serif; }
.hero { background: linear-gradient(to right, #4e73df, #224abe); color: #fff; padding: 3rem 0; border-radius: 0.8rem; text-align: center; }
.hero h1 { font-weight: 600; }
.card { border: none; border-radius: 0.8rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: all 0.2s ease-in-out; }
.card:hover { transform: translateY(-4px); box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
.card-img-top { border-top-left-radius: 0.8rem; border-top-right-radius: 0.8rem; height: 220px; object-fit: cover; }
.status-badge { display: inline-block; padding: 4px 10px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; color: #fff; }
.status-aktif { background-color: #1cc88a; }
.status-tidak { background-color: #e74a3b; }
.btn-primary { background-color: #4e73df; border: none; border-radius: 0.5rem; transition: background-color 0.2s ease; }
.btn-primary:hover { background-color: #3659d0; }
</style>

<section class="hero mt-4 container">
    <h1 class="display-5">Katalog Buku</h1>
    <p class="mb-0">Selamat datang, <?= htmlspecialchars($namaUser) ?></p>
</section>

<div class="container mt-5">
    <div class="filter-box mb-4">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Filter Kategori</label>
                <select class="form-select" id="categoryFilter">
                    <option value="">Semua</option>
                    <?php
                    $catQuery = "SELECT DISTINCT kategori_222274 FROM buku_222274";
                    $catResult = mysqli_query($conn, $catQuery);
                    while ($cat = mysqli_fetch_assoc($catResult)) {
                        echo '<option value="' . htmlspecialchars($cat['kategori_222274']) . '">' . htmlspecialchars($cat['kategori_222274']) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>

    <div class="row" id="bookList">
        <?php
        $sql = "SELECT * FROM buku_222274 ORDER BY judul_222274 ASC";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($buku = mysqli_fetch_assoc($result)) {
                $img = !empty($buku['img_222274'])
                    ? "../uploads/sampul/" . $buku['img_222274']
                    : 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=500&q=80';

                $stok = (int)$buku['stok_222274'];
                $statusClass = ($stok > 0) ? 'status-aktif' : 'status-tidak';
        ?>
                <div class="col-md-3 mb-4 book-card" data-kategori="<?= htmlspecialchars($buku['kategori_222274']) ?>">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" alt="<?= htmlspecialchars($buku['judul_222274']) ?>">
                        <div class="card-body">
                            <h5 class="card-title mb-2"><?= htmlspecialchars($buku['judul_222274']) ?></h5>
                            <p class="card-text text-muted mb-1">Penulis: <?= htmlspecialchars($buku['penulis_222274']) ?></p>
                            <p class="card-text text-muted mb-1">Tahun: <?= htmlspecialchars($buku['tahun_terbit_222274']) ?></p>
                            <p class="card-text text-muted mb-1">Kategori: <?= htmlspecialchars($buku['kategori_222274']) ?></p>
                            <p class="card-text mt-2">
                                <span class="status-badge <?= $statusClass ?>">Stok: <?= $stok ?></span>
                            </p>

                            <?php if ($stok > 0): ?>
                                <!-- Klik Pinjam diarahkan ke form -->
                                <a href="form_pinjam.php?id_buku=<?= $buku['id_buku_222274'] ?>" class="btn btn-primary btn-sm w-100 mt-2">Pinjam</a>
                            <?php else: ?>
                                <p class="text-center text-danger mt-2 mb-0">Stok habis</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "<p class='text-center text-muted'>Belum ada buku yang tersedia.</p>";
        }
        ?>
    </div>
</div>

<script>
const categoryFilter = document.getElementById('categoryFilter');
categoryFilter.addEventListener('change', function() {
    const selected = this.value.toLowerCase();
    document.querySelectorAll('.book-card').forEach(card => {
        const kategori = card.getAttribute('data-kategori').toLowerCase();
        card.style.display = selected === '' || kategori === selected ? 'block' : 'none';
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
mysqli_close($conn);
include 'templates/footer.php';
?>
