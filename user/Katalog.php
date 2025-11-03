<?php
session_start();
$pageTitle = "Katalog Buku - PerpustakaanKu";

// Koneksi database
include 'config/connect.php';

// Include template header dan navbar
include 'templates/header.php';
include 'templates/navbar.php';
?>

<section class="hero mt-5">
  <div class="container">
    <h1 class="display-5 mb-3">📚 Katalog Buku Perpustakaan</h1>
    <p class="lead">Telusuri daftar buku yang tersedia di perpustakaan</p>
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
            $img = !empty($buku['img_url']) ? $buku['img_url'] : 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=500&q=80';
            $status = ($buku['stok'] > 0) ? 'Tersedia' : 'Tidak Tersedia';
            $statusClass = ($buku['stok'] > 0) ? 'status-aktif' : 'status-tidak';
            ?>
            <div class="col-md-3 mb-4 book-card" data-kategori="<?= htmlspecialchars($buku['kategori']) ?>">
              <div class="card h-100">
                <img src="<?= $img ?>" class="card-img-top" alt="<?= htmlspecialchars($buku['judul']) ?>">
                <div class="card-body">
                  <h5 class="card-title"><?= htmlspecialchars($buku['judul']) ?></h5>
                  <p class="card-text text-muted mb-1">Pengarang: <?= htmlspecialchars($buku['penulis']) ?></p>
                  <p class="card-text text-muted mb-1">Tahun Terbit: <?= htmlspecialchars($buku['tahun_terbit']) ?></p>
                  <p class="card-text text-muted mb-1">Kategori: <?= htmlspecialchars($buku['kategori']) ?></p>
                  <p class="card-text"><span class="status-badge <?= $statusClass ?>"><?= $status ?></span></p>
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

<style>
.status-badge {
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: bold;
    font-size: 0.9rem;
}
.status-aktif { background-color: #198754; color: #fff; }
.status-tidak { background-color: #dc3545; color: #fff; }
</style>

<script>
const categoryFilter = document.getElementById('categoryFilter');
categoryFilter.addEventListener('change', function() {
    const selected = this.value;
    document.querySelectorAll('.book-card').forEach(card => {
        card.style.display = selected === '' || card.getAttribute('data-kategori') === selected ? 'block' : 'none';
    });
});
</script>

<?php include 'templates/footer.php'; ?>
