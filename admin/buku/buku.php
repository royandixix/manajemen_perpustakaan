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
    $conn->query("DELETE FROM buku_222274 WHERE id_buku = $id");
    header("Location: buku.php");
    exit();
}

// Ambil semua data buku
$sql = "SELECT * FROM buku_222274 ORDER BY id_buku ASC";
$result = $conn->query($sql);
?>

<?php include '../templates/header_sidebar.php'; ?>

<div id="main-content">
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
        <table class="table table-striped table-hover table-bordered text-center align-middle" id="tabelBuku">
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
            <?php if($result->num_rows > 0): ?>
              <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $row['id_buku'] ?></td>
                  <td>
                    <img src="<?= !empty($row['img_url']) ? $row['img_url'] : 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=80&q=80' ?>" 
                         alt="<?= htmlspecialchars($row['judul']) ?>" width="60">
                  </td>
                  <td><?= htmlspecialchars($row['judul']) ?></td>
                  <td><?= htmlspecialchars($row['penulis']) ?></td>
                  <td><?= htmlspecialchars($row['penerbit']) ?></td>
                  <td><?= $row['tahun_terbit'] ?></td>
                  <td><?= htmlspecialchars($row['kategori']) ?></td>
                  <td><?= $row['stok'] ?></td>
                  <td>
                    <a href="edit_buku.php?id=<?= $row['id_buku'] ?>" class="btn btn-warning btn-sm">
                      <span class="material-symbols-outlined align-middle">edit</span> Edit
                    </a>
                    <a href="buku.php?hapus=<?= $row['id_buku'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus buku?')">
                      <span class="material-symbols-outlined align-middle">delete</span> Hapus
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
</body>
</html>
