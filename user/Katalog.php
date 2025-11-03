<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$pageTitle = "Katalog Buku - PerpustakaanKu";

// Koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "perpustakaan_db_222274";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

// Cek login user/admin
$userLoggedIn = isset($_SESSION['user']);
$namaUser = $userLoggedIn ? $_SESSION['user']['nama_222274'] : null;

include 'templates/header.php';
include 'templates/navbar.php';
?>

<section class="hero mt-5">
  <div class="container">
    <h1 class="display-5 mb-3">📚 Katalog Buku Perpustakaan</h1>
    <?php if ($userLoggedIn): ?>
        <p class="lead">Selamat datang, <?= htmlspecialchars($namaUser) ?>!</p>
    <?php else: ?>
        <p class="lead">Telusuri daftar buku yang tersedia di perpustakaan</p>
    <?php endif; ?>
  </div>
</section>

<div class="container mt-5">
  <div class="row mb-4">
    <div class="col-md-4">
      <label class="form-label fw-semibold">Filter Kategori</label>
      <select class="form-select" id="categoryFilter">
        <option value="">Semua</option>
        <?php
        $catQuery = "SELECT DISTINCT kategori FROM buku_222274";
        $catResult = mysqli_query($conn, $catQuery);
        while($cat = mysqli_fetch_assoc($catResult)) {
            echo '<option value="'.htmlspecialchars($cat['kategori']).'">'.htmlspecialchars($cat['kategori']).'</option>';
        }
        ?>
      </select>
    </div>
  </div>

  <div class="row" id="bookList">
    <?php
    $sql = "SELECT * FROM buku_222274 ORDER BY judul ASC";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($buku = mysqli_fetch_assoc($result)) {
            $img = !empty($buku['img']) ? $buku['img'] : 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=500&q=80';
            $status = ($buku['stok'] > 0) ? 'Tersedia' : 'Tidak Tersedia';
            $statusClass = ($buku['stok'] > 0) ? 'status-aktif' : 'status-tidak';
            ?>
            <div class="col-md-3 mb-4 book-card" data-kategori="<?= htmlspecialchars($buku['kategori']) ?>">
              <div class="card h-100">
                <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" alt="<?= htmlspecialchars($buku['judul']) ?>">
                <div class="card-body">
                  <h5 class="card-title"><?= htmlspecialchars($buku['judul']) ?></h5>
                  <p class="card-text text-muted mb-1">Pengarang: <?= htmlspecialchars($buku['penulis']) ?></p>
                  <p class="card-text text-muted mb-1">Tahun Terbit: <?= htmlspecialchars($buku['tahun_terbit']) ?></p>
                  <p class="card-text text-muted mb-1">Kategori: <?= htmlspecialchars($buku['kategori']) ?></p>
                  <p class="card-text"><span class="status-badge <?= $statusClass ?>"><?= $status ?></span></p>

                  <?php if ($userLoggedIn): ?>
                    <?php if ($buku['stok'] > 0): ?>
                        <form method="POST" action="pinjam.php">
                            <input type="hidden" name="id_buku" value="<?= $buku['id_buku'] ?>">
                            <input type="hidden" name="id_anggota" value="<?= $_SESSION['user']['id_anggota_222274'] ?>">
                            <button type="submit" class="btn btn-primary btn-sm mt-2">Pinjam</button>
                        </form>
                    <?php else: ?>
                        <p class="text-danger">Stok habis</p>
                    <?php endif; ?>
                  <?php else: ?>
                    <p><a href="login.php">Login untuk meminjam</a></p>
                  <?php endif; ?>

                </div>
              </div>
            </div>
            <?php
        }
    } else {
        echo "<p class='text-center'>Belum ada buku yang tersedia.</p>";
    }
    ?>
  </div>
</div>

<script>
const categoryFilter = document.getElementById('categoryFilter');
categoryFilter.addEventListener('change', function() {
    const selected = this.value;
    document.querySelectorAll('.book-card').forEach(card => {
        card.style.display = selected === '' || card.getAttribute('data-kategori') === selected ? 'block' : 'none';
    });
});
</script>

<?php
mysqli_close($conn);
include 'templates/footer.php';
?>
