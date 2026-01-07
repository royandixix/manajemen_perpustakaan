<?php
// Tampilkan error (sementara untuk debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include koneksi DB
include '../../config/connect.php';
session_start();

// Cek session admin
if (!isset($_SESSION['admin'])) {
  header("Location: ../../login.php");
  exit();
}

// Ambil data anggota
$sql = "SELECT * FROM anggota_222274 ORDER BY id_anggota_222274 ASC";
$result = $conn->query($sql);

?>
<?php include '../templates/header_sidebar.php'; ?>

<div id="main-content">
  <h2 class="fw-bold text-primary mb-4">
    <span class="material-symbols-outlined align-middle">group</span>
    Manajemen Anggota
  </h2>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Daftar Anggota</h5>
    <a href="tambah_anggota.php" class="btn btn-success">
      <span class="material-symbols-outlined align-middle">person_add</span> Tambah Anggota
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle text-center">
          <thead class="table-primary">
            <tr>
              <th>ID</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Alamat</th>
              <th>No. Telp</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if($result->num_rows > 0): ?>
              <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $row['id_anggota_222274'] ?></td>
                  <td><?= $row['nama_222274'] ?></td>
                  <td><?= $row['email_222274'] ?></td>
                  <td><?= $row['alamat_222274'] ?></td>
                  <td><?= $row['no_telp_222274'] ?></td>
                  <td>
                    <a href="edit_anggota.php?id=<?= $row['id_anggota_222274'] ?>" class="btn btn-warning btn-sm">
                      <span class="material-symbols-outlined align-middle">edit</span> Edit
                    </a>
                    <a href="hapus_anggota.php?id=<?= $row['id_anggota_222274'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus anggota?')">
                      <span class="material-symbols-outlined align-middle">delete</span> Hapus
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6">Belum ada anggota.</td>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
