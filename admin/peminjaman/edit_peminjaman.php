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

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: peminjaman.php");
    exit();
}

$id_peminjaman = intval($_GET['id']);

// Ambil data peminjaman + buku + anggota
$data = $conn->query("
    SELECT p.*, 
           a.nama_222274 AS nama_anggota,
           b.judul_222274 AS judul_buku,
           b.stok_222274, b.img_222274
    FROM peminjaman_222274 p
    JOIN anggota_222274 a ON p.id_anggota_222274 = a.id_anggota_222274
    JOIN buku_222274 b ON p.id_buku_222274 = b.id_buku_222274
    WHERE p.id_peminjaman_222274 = $id_peminjaman
")->fetch_assoc();

if (!$data) {
    die("Data peminjaman tidak ditemukan.");
}

// Proses update tanggal
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tgl_pinjam = $_POST['tanggal_pinjam'];
    $tgl_kembali = $_POST['tanggal_kembali'];

    // Cek jumlah buku yang dipinjam anggota (maks 2 buku)
    $stmtCheck = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM peminjaman_222274 
        WHERE id_anggota_222274=? AND status_222274='dipinjam' AND id_peminjaman_222274<>?
    ");
    $stmtCheck->bind_param("ii", $data['id_anggota_222274'], $id_peminjaman);
    $stmtCheck->execute();
    $totalDipinjam = $stmtCheck->get_result()->fetch_assoc()['total'];
    $stmtCheck->close();

    if ($totalDipinjam >= 2) {
        $error = "Anggota sudah meminjam 2 buku, tidak bisa menambah lagi.";
    } else {
        // Update tanggal peminjaman
        $stmt = $conn->prepare("
            UPDATE peminjaman_222274 
            SET tanggal_pinjam_222274=?, tanggal_kembali_222274=? 
            WHERE id_peminjaman_222274=?
        ");
        $stmt->bind_param("ssi", $tgl_pinjam, $tgl_kembali, $id_peminjaman);

        if ($stmt->execute()) {
            $success = "Tanggal peminjaman berhasil diperbarui!";
            $data['tanggal_pinjam_222274'] = $tgl_pinjam;
            $data['tanggal_kembali_222274'] = $tgl_kembali;
        } else {
            $error = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

include '../templates/header_sidebar.php';
?>

<div id="main-content" class="container mt-4">
  <h2 class="fw-bold text-primary mb-4">
    <span class="material-symbols-outlined align-middle me-1">edit_note</span>
    Edit Peminjaman (Admin)
  </h2>

  <?php if(isset($success)) echo '<div class="alert alert-success">'.$success.'</div>'; ?>
  <?php if(isset($error)) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>

  <div class="card p-4 shadow-sm">

      <!-- Info Anggota -->
      <div class="mb-3">
        <label class="form-label fw-bold">Nama Anggota</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama_anggota']) ?>" disabled>
      </div>

      <!-- Info Buku -->
      <div class="mb-3">
        <label class="form-label fw-bold">Judul Buku</label>
        <input type="text" class="form-control" 
               value="<?= htmlspecialchars($data['judul_buku']) ?> (stok: <?= $data['stok_222274'] ?>)" disabled>
      </div>

      <?php if($data['img_222274']): ?>
        <div class="mb-3">
            <img src="../../uploads/sampul/<?= htmlspecialchars($data['img_222274']) ?>" 
                 class="img-fluid mb-3" style="max-width:150px;">
        </div>
      <?php endif; ?>

      <form method="POST">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Tanggal Pinjam</label>
            <input type="date" class="form-control" name="tanggal_pinjam" id="tanggalPinjam"
                   value="<?= $data['tanggal_pinjam_222274'] ?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Tanggal Kembali</label>
            <input type="date" class="form-control" name="tanggal_kembali" id="tanggalKembali"
                   value="<?= $data['tanggal_kembali_222274'] ?>" required readonly>
          </div>
        </div>

        <button type="submit" class="btn btn-success w-100 mt-2">
          <span class="material-symbols-outlined align-middle me-1">save</span>
          Update Tanggal
        </button>
      </form>
  </div>

  <footer class="text-center mt-4 mb-2 text-secondary">
    &copy; <?= date('Y') ?> PerpustakaanKu.
  </footer>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tanggalPinjam = document.getElementById('tanggalPinjam');
    const tanggalKembali = document.getElementById('tanggalKembali');

    function updateTanggalKembali() {
        const pinjam = new Date(tanggalPinjam.value);
        const kembali = new Date(pinjam);
        kembali.setDate(pinjam.getDate() + 7); 
        tanggalKembali.value = kembali.toISOString().split('T')[0];
    }

    updateTanggalKembali();
    tanggalPinjam.addEventListener('change', updateTanggalKembali);
});
</script>

</body>
</html>
