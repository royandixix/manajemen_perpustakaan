<?php
session_start();
include '../../config/connect.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

$id = intval($_GET['id']);

// Ambil data peminjaman + anggota + buku
$data = $conn->query("
    SELECT p.*, a.nama_222274, b.judul_222274, b.stok_222274, b.img_222274
    FROM peminjaman_222274 p
    JOIN anggota_222274 a ON p.id_anggota_222274 = a.id_anggota_222274
    JOIN buku_222274 b ON p.id_buku_222274 = b.id_buku_222274
    WHERE id_peminjaman_222274 = $id
")->fetch_assoc();

if (!$data) {
    die("Data tidak ditemukan");
}

// Proses update tanggal kembali
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tgl_kembali = $_POST['tanggal_kembali'];

    $stmt = $conn->prepare("
        UPDATE peminjaman_222274 
        SET tanggal_kembali_222274 = ? 
        WHERE id_peminjaman_222274 = ?
    ");
    $stmt->bind_param("si", $tgl_kembali, $id);

    if ($stmt->execute()) {
        $success = "Tanggal kembali berhasil diperbarui!";
        $data['tanggal_kembali_222274'] = $tgl_kembali;
    } else {
        $error = "Gagal memperbarui tanggal kembali: " . $stmt->error;
    }

    $stmt->close();
}

include '../templates/header_sidebar.php';
?>

<div class="container mt-4">

  <h2 class="fw-bold text-primary mb-3">
    Edit Tanggal Pengembalian
  </h2>

  <?php if(isset($success)) echo '<div class="alert alert-success">'.$success.'</div>'; ?>
  <?php if(isset($error)) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>

  <div class="card p-4 shadow-sm">

    <!-- Info Anggota -->
    <div class="mb-3">
      <label class="fw-bold">Nama Anggota</label>
      <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama_222274']) ?>" disabled>
    </div>

    <!-- Info Buku -->
    <div class="mb-3">
      <label class="fw-bold">Judul Buku</label>
      <input type="text" class="form-control" value="<?= htmlspecialchars($data['judul_222274']) ?> (stok: <?= $data['stok_222274'] ?>)" disabled>
    </div>

    <?php if($data['img_222274']): ?>
        <div class="mb-3">
            <img src="../../uploads/sampul/<?= htmlspecialchars($data['img_222274']) ?>" class="img-fluid mb-3" style="max-width:150px;">
        </div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="fw-bold">Tanggal Kembali</label>
        <input type="date" class="form-control" name="tanggal_kembali" 
               value="<?= $data['tanggal_kembali_222274'] ?>" required>
      </div>

      <button class="btn btn-primary w-100">
        Simpan Perubahan
      </button>
    </form>

  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
