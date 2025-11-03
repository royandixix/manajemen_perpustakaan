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

// Proses form jika submit
if (isset($_POST['submit'])) {
  $judul = $conn->real_escape_string($_POST['judul']);
  $penulis = $conn->real_escape_string($_POST['penulis']);
  $penerbit = $conn->real_escape_string($_POST['penerbit']);
  $tahun = intval($_POST['tahun_terbit']);
  $kategori = $conn->real_escape_string($_POST['kategori']);
  $stok = intval($_POST['stok']);

  $sql = "INSERT INTO buku_222274 (judul, penulis, penerbit, tahun_terbit, kategori, stok)
            VALUES ('$judul', '$penulis', '$penerbit', '$tahun', '$kategori', '$stok')";

  if ($conn->query($sql)) {
    $success = "Buku berhasil ditambahkan!";
  } else {
    $error = "Error: " . $conn->error;
  }
}

// Daftar kategori (hardcode dulu)
$kategori_list = [];
$res = $conn->query("SELECT nama_kategori FROM kategori_222274 ORDER BY nama_kategori ASC");
while($row = $res->fetch_assoc()){
    $kategori_list[] = $row['nama_kategori'];
}

?>

<?php include '../templates/header_sidebar.php'; ?>

<div id="main-content">
  <h2 class="fw-bold text-primary mb-4">
    <span class="material-symbols-outlined align-middle">add_circle</span> Tambah Buku
  </h2>

  <?php if (isset($success)) echo '<div class="alert alert-success">' . $success . '</div>'; ?>
  <?php if (isset($error)) echo '<div class="alert alert-danger">' . $error . '</div>'; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form action="" method="POST">
        <div class="mb-3">
          <label for="judul" class="form-label">Judul Buku</label>
          <input type="text" name="judul" id="judul" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="penulis" class="form-label">Pengarang</label>
          <input type="text" name="penulis" id="penulis" class="form-control">
        </div>

        <div class="mb-3">
          <label for="penerbit" class="form-label">Penerbit</label>
          <input type="text" name="penerbit" id="penerbit" class="form-control">
        </div>

        <div class="mb-3">
          <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
          <input type="number" name="tahun_terbit" id="tahun_terbit" class="form-control" min="1900" max="<?= date('Y') ?>">
        </div>

        <?php
        $kategori_list = ['Pemrograma', 'matematika', 'Web', 'Jaringan'];
        ?>
        <div class="mb-3">
          <label for="kategori" class="form-label">Kategori</label>
          <select name="kategori" id="kategori" class="form-select">
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($kategori_list as $kat): ?>
              <option value="<?= $kat ?>"><?= $kat ?></option>
            <?php endforeach; ?>
          </select>
        </div>


        <div class="mb-3">
          <label for="stok" class="form-label">Stok</label>
          <input type="number" name="stok" id="stok" class="form-control" value="0" min="0">
        </div>

        <button type="submit" name="submit" class="btn btn-primary">
          <span class="material-symbols-outlined align-middle">save</span> Simpan
        </button>
        <a href="buku.php" class="btn btn-secondary">
          <span class="material-symbols-outlined align-middle">arrow_back</span> Kembali
        </a>
      </form>
    </div>
  </div>
</div>

<?php include '../templates/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>