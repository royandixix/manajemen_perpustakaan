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
    $_SESSION['flash'] = "Buku berhasil ditambahkan!";
    header("Location: buku.php");
    exit();
  } else {
    $error = "Gagal menambahkan buku: " . $conn->error;
  }
}

// Ambil kategori dari tabel kategori (jika ada)
$kategori_list = [];
$res = $conn->query("SELECT nama_kategori FROM kategori_222274 ORDER BY nama_kategori ASC");
if ($res && $res->num_rows > 0) {
  while ($row = $res->fetch_assoc()) {
      $kategori_list[] = $row['nama_kategori'];
  }
} else {
  $kategori_list = ['Pemrograman', 'Matematika', 'Web', 'Jaringan'];
}
?>

<?php include '../templates/header_sidebar.php'; ?>

<!-- ===== STYLE FUTURISTIC GLASS ===== -->
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #eef2f3, #dfe9f3);
  min-height: 100vh;
  color: #333;
}
#main-content {
  padding: 2rem;
  animation: fadeIn 0.6s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
h2 {
  font-weight: 700;
  background: linear-gradient(90deg, #4facfe, #00f2fe);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.card {
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(12px);
  border-radius: 20px;
  border: 1px solid rgba(255,255,255,0.5);
  box-shadow: 0 8px 25px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
}
.card:hover { transform: translateY(-4px); }
.form-control, .form-select {
  border-radius: 15px;
  border: 1px solid #ccc;
  transition: all 0.25s ease;
}
.form-control:focus, .form-select:focus {
  box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
  transform: scale(1.01);
}
.btn {
  border-radius: 25px;
  transition: all 0.25s ease;
}
.btn-primary {
  background: linear-gradient(135deg, #4facfe, #00f2fe);
  border: none;
}
.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 15px rgba(79,172,254,0.4);
}
.btn-secondary:hover {
  transform: scale(1.05);
}
footer {
  color: #6c757d;
  font-size: 0.9rem;
}
</style>

<!-- ===== MAIN CONTENT ===== -->
<div id="main-content">
  <h2 class="fw-bold mb-4 mt-5">
    <span class="material-symbols-outlined align-middle me-1 text-primary">add_circle</span>
    Tambah Buku
  </h2>

  <div class="card shadow-sm p-4">
    <form action="" method="POST" class="needs-validation" novalidate>
      <div class="mb-3">
        <label for="judul" class="form-label">Judul Buku</label>
        <input type="text" name="judul" id="judul" class="form-control" placeholder="Masukkan judul buku..." required>
      </div>

      <div class="mb-3">
        <label for="penulis" class="form-label">Penulis</label>
        <input type="text" name="penulis" id="penulis" class="form-control" placeholder="Masukkan nama penulis...">
      </div>

      <div class="mb-3">
        <label for="penerbit" class="form-label">Penerbit</label>
        <input type="text" name="penerbit" id="penerbit" class="form-control" placeholder="Masukkan penerbit buku...">
      </div>

      <div class="mb-3">
        <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
        <input type="number" name="tahun_terbit" id="tahun_terbit" class="form-control" 
               min="1900" max="<?= date('Y') ?>" placeholder="Misal: 2022">
      </div>

      <div class="mb-3">
        <label for="kategori" class="form-label">Kategori</label>
        <select name="kategori" id="kategori" class="form-select" required>
          <option value="">-- Pilih Kategori --</option>
          <?php foreach ($kategori_list as $kat): ?>
            <option value="<?= htmlspecialchars($kat) ?>"><?= htmlspecialchars($kat) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="stok" class="form-label">Stok</label>
        <input type="number" name="stok" id="stok" class="form-control" min="0" value="0">
      </div>

      <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-primary me-2">
          <span class="material-symbols-outlined align-middle me-1">save</span> Simpan
        </button>
        <a href="buku.php" class="btn btn-secondary">
          <span class="material-symbols-outlined align-middle me-1">arrow_back</span> Kembali
        </a>
      </div>
    </form>
  </div>

  <footer class="text-center mt-4">
    &copy; <?= date('Y') ?> <strong>PerpustakaanKu</strong> — Desain oleh <span class="text-primary">AI UI</span>
  </footer>
</div>

<!-- ===== SCRIPT FUTURISTIC ===== -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
<?php if (isset($error)): ?>
Swal.fire({
  icon: 'error',
  title: 'Gagal!',
  text: '<?= addslashes($error) ?>',
  confirmButtonColor: '#3085d6'
});
<?php endif; ?>

// Validasi form Bootstrap
(() => {
  'use strict';
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>

<?php include '../templates/footer.php'; ?>
</body>
</html>
