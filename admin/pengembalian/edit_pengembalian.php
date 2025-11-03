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

// Ambil ID peminjaman
if (!isset($_GET['id'])) {
    header("Location: pengembalian.php");
    exit();
}

$id_peminjaman = intval($_GET['id']);

// Ambil data peminjaman
$data = $conn->query(
    "SELECT p.*, a.nama_222274 AS nama_anggota, b.judul AS judul_buku
     FROM peminjaman_222274 p
     JOIN anggota_222274 a ON p.id_anggota = a.id_anggota_222274
     JOIN buku_222274 b ON p.id_buku = b.id_buku
     WHERE p.id_peminjaman = $id_peminjaman"
)->fetch_assoc();

if (!$data) {
    die("Data pengembalian tidak ditemukan.");
}

// Proses form jika submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tgl_kembali_baru = $_POST['tanggal_kembali'];

    $update = $conn->query(
        "UPDATE peminjaman_222274 
         SET tanggal_kembali = '$tgl_kembali_baru' 
         WHERE id_peminjaman = $id_peminjaman"
    );

    if ($update) {
        $success = "Tanggal kembali berhasil diupdate.";
        $data['tanggal_kembali'] = $tgl_kembali_baru;
    } else {
        $error = "Gagal update tanggal kembali: " . $conn->error;
    }
}

// Hitung denda otomatis (misal 1000/hari keterlambatan)
$today = new DateTime();
$tgl_kembali = new DateTime($data['tanggal_kembali']);
$denda = 0;
if($today > $tgl_kembali){
    $diff = $today->diff($tgl_kembali)->days;
    $denda = $diff * 1000;
}
?>

<?php include '../templates/header_sidebar.php'; ?>

<div id="main-content" class="container mt-4">
  <h2 class="fw-bold text-primary mb-4">
    <span class="material-symbols-outlined align-middle me-1">edit_calendar</span>
    Edit Pengembalian Buku
  </h2>

  <?php if(isset($success)) echo '<div class="alert alert-success">'.$success.'</div>'; ?>
  <?php if(isset($error)) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>

  <div class="card p-4 shadow-sm">
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Nama Anggota</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama_anggota']) ?>" disabled>
      </div>

      <div class="mb-3">
        <label class="form-label">Judul Buku</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($data['judul_buku']) ?>" disabled>
      </div>

      <div class="mb-3">
        <label class="form-label">Tanggal Pinjam</label>
        <input type="date" class="form-control" value="<?= $data['tanggal_pinjam'] ?>" disabled>
      </div>

      <div class="mb-3">
        <label class="form-label">Tanggal Kembali</label>
        <input type="date" class="form-control" name="tanggal_kembali" value="<?= $data['tanggal_kembali'] ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Denda Saat Ini</label>
        <input type="text" class="form-control" value="Rp <?= number_format($denda,0,",",".") ?>" disabled>
      </div>

      <button type="submit" class="btn btn-primary">
        <span class="material-symbols-outlined">save</span> Update
      </button>
      <a href="pengembalian.php" class="btn btn-secondary">
        <span class="material-symbols-outlined">arrow_back</span> Kembali
      </a>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
