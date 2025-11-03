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

// Fungsi hapus peminjaman
if (isset($_GET['hapus_id'])) {
    $id_hapus = intval($_GET['hapus_id']);
    $peminjaman = $conn->query("SELECT id_buku FROM peminjaman_222274 WHERE id_peminjaman = $id_hapus")->fetch_assoc();
    if ($peminjaman) {
        $id_buku = $peminjaman['id_buku'];
        if ($conn->query("DELETE FROM peminjaman_222274 WHERE id_peminjaman = $id_hapus")) {
            // Tambah stok buku kembali
            $conn->query("UPDATE buku_222274 SET stok = stok + 1 WHERE id_buku = $id_buku");
            $success = "Peminjaman berhasil dihapus.";
        } else {
            $error = "Gagal menghapus peminjaman: " . $conn->error;
        }
    } else {
        $error = "Data peminjaman tidak ditemukan.";
    }
}

// Proses tambah peminjaman jika form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_anggota = intval($_POST['anggota']);
    $id_buku = intval($_POST['buku']);
    $tgl_pinjam = $_POST['tanggal_pinjam'];
    $tgl_kembali = $_POST['tanggal_kembali'];

    $cekBuku = $conn->query("SELECT stok FROM buku_222274 WHERE id_buku = $id_buku")->fetch_assoc();
    if($cekBuku['stok'] > 0) {
        $sql = "INSERT INTO peminjaman_222274 (id_anggota, id_buku, tanggal_pinjam, tanggal_kembali, status)
                VALUES ('$id_anggota', '$id_buku', '$tgl_pinjam', '$tgl_kembali', 'dipinjam')";
        if ($conn->query($sql)) {
            $conn->query("UPDATE buku_222274 SET stok = stok - 1 WHERE id_buku = $id_buku");
            $success = "Peminjaman berhasil disimpan!";
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "Stok buku tidak tersedia!";
    }
}

// Ambil daftar anggota & buku untuk dropdown
$anggota_list = $conn->query("SELECT id_anggota_222274, nama_222274 FROM anggota_222274 ORDER BY nama_222274 ASC");
$buku_list = $conn->query("SELECT id_buku, judul, stok FROM buku_222274 ORDER BY judul ASC");

// Ambil data peminjaman untuk tabel
$peminjaman_list = $conn->query(
    "SELECT p.id_peminjaman, a.nama_222274 AS nama_anggota, b.judul AS judul_buku,
            p.tanggal_pinjam, p.tanggal_kembali
     FROM peminjaman_222274 p
     JOIN anggota_222274 a ON p.id_anggota = a.id_anggota_222274
     JOIN buku_222274 b ON p.id_buku = b.id_buku
     ORDER BY p.tanggal_pinjam DESC"
);
?>

<?php include '../templates/header_sidebar.php'; ?>

<div id="main-content" class="container mt-4">
  <h2 class="fw-bold text-primary mb-4">
    <span class="material-symbols-outlined align-middle me-1">library_add</span>
    Transaksi Peminjaman Buku
  </h2>

  <?php if(isset($success)) echo '<div class="alert alert-success">'.$success.'</div>'; ?>
  <?php if(isset($error)) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>

  <!-- Form Peminjaman -->
  <div class="card p-4 mb-4 shadow-sm">
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Pilih Anggota</label>
        <select class="form-select" name="anggota" required>
          <option value="">-- Pilih Anggota --</option>
          <?php while($angg = $anggota_list->fetch_assoc()): ?>
            <option value="<?= $angg['id_anggota_222274'] ?>"><?= $angg['nama_222274'] ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Pilih Buku</label>
        <select class="form-select" name="buku" required>
          <option value="">-- Pilih Buku --</option>
          <?php while($buku = $buku_list->fetch_assoc()): ?>
            <option value="<?= $buku['id_buku'] ?>" data-stok="<?= $buku['stok'] ?>">
              <?= $buku['judul'] ?> (stok: <?= $buku['stok'] ?>)
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Tanggal Pinjam</label>
          <input type="date" class="form-control" name="tanggal_pinjam" id="tanggalPinjam" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Tanggal Kembali</label>
          <input type="date" class="form-control" name="tanggal_kembali" id="tanggalKembali" required readonly>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100 mt-2">
        <span class="material-symbols-outlined align-middle me-1">save</span>
        Simpan Peminjaman
      </button>
    </form>
  </div>

  <!-- Tabel Transaksi -->
  <div class="card shadow-sm p-4">
    <h5 class="mb-3">
      <span class="material-symbols-outlined align-middle text-success">list_alt</span>
      Daftar Transaksi Peminjaman
    </h5>
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle text-center">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>Nama Anggota</th>
            <th>Judul Buku</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if($peminjaman_list->num_rows > 0): ?>
            <?php while($row = $peminjaman_list->fetch_assoc()): ?>
              <tr>
                <td><?= $row['id_peminjaman'] ?></td>
                <td><?= htmlspecialchars($row['nama_anggota']) ?></td>
                <td><?= htmlspecialchars($row['judul_buku']) ?></td>
                <td><?= $row['tanggal_pinjam'] ?></td>
                <td><?= $row['tanggal_kembali'] ?></td>
                <td>
                  <a href="edit_peminjaman.php?id=<?= $row['id_peminjaman'] ?>" class="btn btn-sm btn-warning">
                    <span class="material-symbols-outlined">edit</span> Edit
                  </a>
                  <a href="?hapus_id=<?= $row['id_peminjaman'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus peminjaman ini?');">
                    <span class="material-symbols-outlined">delete</span> Hapus
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6">Belum ada transaksi peminjaman.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
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

    const today = new Date().toISOString().split('T')[0];
    tanggalPinjam.value = today;

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
