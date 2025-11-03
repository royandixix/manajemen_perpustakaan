<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

include '../../config/connect.php';

// Hapus pengembalian
if (isset($_GET['hapus_id'])) {
    $id_hapus = intval($_GET['hapus_id']);
    $peminjaman = $conn->query("SELECT id_buku FROM peminjaman_222274 WHERE id_peminjaman = $id_hapus")->fetch_assoc();
    if ($peminjaman) {
        $id_buku = $peminjaman['id_buku'];
        if ($conn->query("DELETE FROM peminjaman_222274 WHERE id_peminjaman = $id_hapus")) {
            // Tambah stok buku
            $conn->query("UPDATE buku_222274 SET stok = stok + 1 WHERE id_buku = $id_buku");
            $success = "Data pengembalian berhasil dihapus.";
        } else {
            $error = "Gagal menghapus data pengembalian: " . $conn->error;
        }
    }
}

// Ambil daftar peminjaman
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
  <h2 class="fw-bold text-primary mb-4 text-center">
    <span class="material-symbols-outlined align-middle me-1">assignment_return</span>
    Transaksi Pengembalian Buku
  </h2>

  <?php if(isset($success)) echo '<div class="alert alert-success">'.$success.'</div>'; ?>
  <?php if(isset($error)) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>

  <!-- Table Card -->
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered align-middle mb-0">
          <thead class="table-primary text-center">
            <tr>
              <th>ID</th>
              <th>Nama Anggota</th>
              <th>Judul Buku</th>
              <th>Tanggal Pinjam</th>
              <th>Tanggal Kembali</th>
              <th>Denda</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody class="text-center">
            <?php if($peminjaman_list->num_rows > 0): ?>
                <?php while($row = $peminjaman_list->fetch_assoc()): ?>
                    <?php
                        // Hitung denda (Rp 1000/hari keterlambatan)
                        $today = new DateTime();
                        $tgl_kembali = new DateTime($row['tanggal_kembali']);
                        $denda = 0;
                        if($today > $tgl_kembali){
                            $diff = $today->diff($tgl_kembali)->days;
                            $denda = $diff * 1000;
                        }
                    ?>
                    <tr>
                        <td><?= $row['id_peminjaman'] ?></td>
                        <td><?= htmlspecialchars($row['nama_anggota']) ?></td>
                        <td><?= htmlspecialchars($row['judul_buku']) ?></td>
                        <td><?= $row['tanggal_pinjam'] ?></td>
                        <td><?= $row['tanggal_kembali'] ?></td>
                        <td>Rp <?= number_format($denda,0,",",".") ?></td>
                        <td>
                          <a href="edit_pengembalian.php?id=<?= $row['id_peminjaman'] ?>" class="btn btn-sm btn-warning">
                              <span class="material-symbols-outlined">edit</span> Edit
                          </a>
                          <a href="?hapus_id=<?= $row['id_peminjaman'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data pengembalian ini?');">
                              <span class="material-symbols-outlined">delete</span> Hapus
                          </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">Belum ada data pengembalian.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="text-center text-muted mt-5 mb-3">
    &copy; <?= date('Y') ?> <strong>PerpustakaanKu</strong>. Semua hak dilindungi.
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
