<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../config/connect.php'; 
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../../login.php");
    exit();
}

// Hapus buku
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    // Hapus file gambar jika ada
    $res = $conn->query("SELECT img FROM buku_222274 WHERE id_buku = $id");
    if($res && $res->num_rows > 0){
        $data = $res->fetch_assoc();
        if(!empty($data['img']) && file_exists('../../public/img/'.$data['img'])){
            unlink('../../public/img/'.$data['img']);
        }
    }
    $conn->query("DELETE FROM buku_222274 WHERE id_buku = $id");
    header("Location: buku.php");
    exit();
}

// Ambil semua data buku
$sql = "SELECT * FROM buku_222274 ORDER BY id_buku ASC";
$result = $conn->query($sql);
?>

<?php include '../templates/header_sidebar.php'; ?>

<div id="main-content" class="px-4 py-3">
  <h2 class="fw-bold text-primary mb-4">
    <span class="material-symbols-outlined align-middle">menu_book</span> Manajemen Buku
  </h2>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <a href="tambah_buku.php" class="btn btn-success">
      <span class="material-symbols-outlined align-middle me-1">add_circle</span> Tambah Buku
    </a>
    <input type="text" class="form-control w-25" placeholder="Cari buku..." id="searchBuku">
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle text-center" id="tabelBuku">
          <thead class="table-primary">
            <tr>
              <th>ID</th>
              <th>Gambar</th>
              <th>Judul Buku</th>
              <th>Pengarang</th>
              <th>Penerbit</th>
              <th>Tahun</th>
              <th>Kategori</th>
              <th>Stok</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if($result && $result->num_rows > 0): ?>
              <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $row['id_buku'] ?></td>
                  <td>
                    <img src="<?= !empty($row['img']) ? '../../public/img/'.$row['img'] : 'https://via.placeholder.com/60x80?text=No+Image' ?>" 
                         alt="<?= htmlspecialchars($row['judul']) ?>" width="60" class="rounded">
                  </td>
                  <td><?= htmlspecialchars($row['judul']) ?></td>
                  <td><?= htmlspecialchars($row['penulis']) ?></td>
                  <td><?= htmlspecialchars($row['penerbit']) ?></td>
                  <td><?= $row['tahun_terbit'] ?></td>
                  <td><?= htmlspecialchars($row['kategori']) ?></td>
                  <td><?= $row['stok'] ?></td>
                  <td>
                    <a href="edit_buku.php?id=<?= $row['id_buku'] ?>" class="btn btn-warning btn-sm mb-1">
                      <span class="material-symbols-outlined align-middle">edit</span>
                    </a>
                    <a href="buku.php?hapus=<?= $row['id_buku'] ?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Yakin hapus buku?')">
                      <span class="material-symbols-outlined align-middle">delete</span>
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="9">Belum ada data buku.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <footer class="text-center mt-4 mb-2 text-secondary">
    &copy; <?= date('Y') ?> PerpustakaanKu. Semua hak dilindungi.
  </footer>
</div>

<?php include '../templates/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Filter search
const searchInput = document.getElementById('searchBuku');
searchInput.addEventListener('keyup', function() {
  const filter = searchInput.value.toLowerCase();
  const rows = document.querySelectorAll('#tabelBuku tbody tr');
  rows.forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
  });
});
</script>
