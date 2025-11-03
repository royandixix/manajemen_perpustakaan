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

// Pastikan ada parameter id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: peminjaman.php");
    exit();
}

$id_peminjaman = intval($_GET['id']);

// Ambil data peminjaman sesuai ID
$data = $conn->query(
    "SELECT * FROM peminjaman_222274 WHERE id_peminjaman = $id_peminjaman"
)->fetch_assoc();

if (!$data) {
    die("Data peminjaman tidak ditemukan.");
}

// Ambil daftar anggota & buku untuk dropdown
$anggota_list = $conn->query("SELECT id_anggota_222274, nama_222274 FROM anggota_222274 ORDER BY nama_222274 ASC");
$buku_list = $conn->query("SELECT id_buku, judul, stok FROM buku_222274 ORDER BY judul ASC");

// Proses form jika submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_anggota = intval($_POST['anggota']);
    $id_buku = intval($_POST['buku']);
    $tgl_pinjam = $_POST['tanggal_pinjam'];
    $tgl_kembali = $_POST['tanggal_kembali'];

    // Ambil stok buku
    $stok_buku = $conn->query("SELECT stok FROM buku_222274 WHERE id_buku = $id_buku")->fetch_assoc()['stok'];

    // Update peminjaman
    $sql = "UPDATE peminjaman_222274 SET
                id_anggota = $id_anggota,
                id_buku = $id_buku,
                tanggal_pinjam = '$tgl_pinjam',
                tanggal_kembali = '$tgl_kembali'
            WHERE id_peminjaman = $id_peminjaman";

    if ($conn->query($sql)) {
        $success = "Peminjaman berhasil diperbarui!";
        // Refresh data
        $data = $conn->query("SELECT * FROM peminjaman_222274 WHERE id_peminjaman = $id_peminjaman")->fetch_assoc();
    } else {
        $error = "Error: " . $conn->error;
    }
}

?>

<?php include '../templates/header_sidebar.php'; ?>

<div id="main-content" class="container mt-4">
  <h2 class="fw-bold text-primary mb-4">
    <span class="material-symbols-outlined align-middle me-1">edit_note</span>
    Edit Transaksi Peminjaman
  </h2>

  <?php if(isset($success)) echo '<div class="alert alert-success">'.$success.'</div>'; ?>
  <?php if(isset($error)) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>

  <div class="card p-4 shadow-sm">
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Pilih Anggota</label>
        <select class="form-select" name="anggota" required>
          <option value="">-- Pilih Anggota --</option>
          <?php while($angg = $anggota_list->fetch_assoc()): ?>
            <option value="<?= $angg['id_anggota_222274'] ?>" <?= $angg['id_anggota_222274'] == $data['id_anggota'] ? 'selected' : '' ?>>
              <?= $angg['nama_222274'] ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Pilih Buku</label>
        <select class="form-select" name="buku" required>
          <option value="">-- Pilih Buku --</option>
          <?php while($buku = $buku_list->fetch_assoc()): ?>
            <option value="<?= $buku['id_buku'] ?>" <?= $buku['id_buku'] == $data['id_buku'] ? 'selected' : '' ?>>
              <?= $buku['judul'] ?> (stok: <?= $buku['stok'] ?>)
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Tanggal Pinjam</label>
          <input type="date" class="form-control" name="tanggal_pinjam" id="tanggalPinjam" value="<?= $data['tanggal_pinjam'] ?>" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Tanggal Kembali</label>
          <input type="date" class="form-control" name="tanggal_kembali" id="tanggalKembali" value="<?= $data['tanggal_kembali'] ?>" required readonly>
        </div>
      </div>

      <button type="submit" class="btn btn-success w-100 mt-2">
        <span class="material-symbols-outlined align-middle me-1">save</span>
        Update Peminjaman
      </button>
    </form>
  </div>

  <footer class="text-center mt-4 mb-2 text-secondary">
    &copy; <?= date('Y') ?> PerpustakaanKu. Semua hak dilindungi.
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tanggalPinjam = document.getElementById('tanggalPinjam');
    const tanggalKembali = document.getElementById('tanggalKembali');

    function updateTanggalKembali() {
        const pinjam = new Date(tanggalPinjam.value);
        const kembali = new Date(pinjam);
        kembali.setDate(pinjam.getDate() + 7); // default 7 hari
        tanggalKembali.value = kembali.toISOString().split('T')[0];
    }

    updateTanggalKembali();
    tanggalPinjam.addEventListener('change', updateTanggalKembali);
});
</script>
</body>
</html>
