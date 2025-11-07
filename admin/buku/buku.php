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
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  $conn->query("DELETE FROM buku_222274 WHERE id_buku = $id");
  header("Location: buku.php");
  exit();
}

$sql = "SELECT * FROM buku_222274 ORDER BY id_buku ASC";
$result = $conn->query($sql);
?>

<?php include '../templates/header_sidebar.php'; ?>

<!-- ====== STYLE FUTURISTIC GLASS ====== -->
<style>
  body {
    font-family: "Poppins", sans-serif;
    background: linear-gradient(135deg, #eef2f3, #dfe9f3);
    min-height: 100vh;
    color: #333;
  }

  #main-content {
    padding: 2rem;
    animation: fadeIn 0.7s ease-in-out;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  h2 {
    font-weight: 700;
    color: #3b3b98;
  }

  .card {
    backdrop-filter: blur(12px);
    background: rgba(255, 255, 255, 0.8);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
  }

  .card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
  }

  table.dataTable {
    border-radius: 15px;
    overflow: hidden;
  }

  thead {
    background: linear-gradient(90deg, #4b6cb7, #182848);
    color: #fff;
    font-weight: 600;
  }

  tbody tr {
    transition: all 0.3s ease;
  }

  tbody tr:hover {
    background: rgba(75, 108, 183, 0.08);
    transform: scale(1.01);
  }

  td img {
    border-radius: 8px;
    transition: transform 0.3s ease;
  }

  td img:hover {
    transform: scale(1.2);
  }

  .btn {
    border-radius: 30px;
    transition: all 0.25s ease;
  }

  .btn-warning:hover {
    background-color: #f39c12;
    transform: scale(1.05);
  }

  .btn-danger:hover {
    background-color: #e74c3c;
    transform: scale(1.05);
  }

  .btn-success {
    background: linear-gradient(135deg, #2ecc71, #27ae60);
    border: none;
  }

  .btn-success:hover {
    box-shadow: 0 6px 14px rgba(46, 204, 113, 0.4);
    transform: translateY(-2px);
  }

  .badge {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
  }

  footer {
    font-size: 0.9rem;
    color: #6c757d;
  }

  .dataTables_wrapper .dataTables_filter input {
    border-radius: 20px;
    border: 1px solid #ced4da;
    padding: 6px 12px;
    background-color: #fff;
  }

  .dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 10px;
    padding: 5px 10px;
    background: rgba(75, 108, 183, 0.1);
    border: none !important;
    margin: 2px;
    transition: 0.2s;
  }

  .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #4b6cb7 !important;
    color: white !important;
  }

  .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #182848 !important;
    color: white !important;
  }
</style>

<!-- ====== MAIN CONTENT ====== -->
<div id="main-content">
  <h2 class="fw-bold mb-4 mt-5">
    <span class="material-symbols-outlined align-middle me-2 text-primary">menu_book</span>
    Manajemen Buku
  </h2>

  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <a href="tambah_buku.php" class="btn btn-success">
      <span class="material-symbols-outlined align-middle me-1">add_circle</span> Tambah Buku
    </a>
  </div>

  <div class="card p-3">
    <div class="table-responsive">
      <table id="example" class="table table-hover align-middle" style="width:100%">
        <thead>
          <tr>
            <th>ID</th>
            <th>Gambar</th>
            <th>Judul Buku</th>
            <th>Penulis</th>
            <th>Penerbit</th>
            <th>Tahun</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $row['id_buku'] ?></td>
                <td>
                  <img src="<?= !empty($row['img_url']) ? $row['img_url'] : 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=80&q=80' ?>"
                    alt="<?= htmlspecialchars($row['judul']) ?>" width="55" height="75">
                </td>
                <td><?= htmlspecialchars($row['judul']) ?></td>
                <td><?= htmlspecialchars($row['penulis']) ?></td>
                <td><?= htmlspecialchars($row['penerbit']) ?></td>
                <td><?= $row['tahun_terbit'] ?></td>
                <td><span class="badge bg-primary"><?= htmlspecialchars($row['kategori']) ?></span></td>
                <td><span class="badge bg-<?= $row['stok'] > 0 ? 'success' : 'danger' ?>"><?= $row['stok'] ?></span></td>
                <td>
                  <a href="edit_buku.php?id=<?= $row['id_buku'] ?>" class="btn btn-warning btn-sm me-1">
                    <span class="material-symbols-outlined align-middle">edit</span>
                  </a>
                  <a href="buku.php?hapus=<?= $row['id_buku'] ?>"
                    class="btn btn-danger btn-sm"
                    onclick="return confirm('Yakin ingin menghapus buku ini?')">
                    <span class="material-symbols-outlined align-middle">delete</span>
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="9" class="text-center text-muted py-3">Belum ada data buku.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <footer class="text-center mt-4">
    &copy; <?= date('Y') ?> <strong>PerpustakaanKu</strong> — Desain oleh <span class="text-primary">AI UI</span>
  </footer>
</div>

<!-- ====== SCRIPTS ====== -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">

<script>
  $(document).ready(function() {
    $('#example').DataTable({
      responsive: true,
      pageLength: 6,
      order: [[0, 'asc']],
      language: {
        search: "🔍 Cari Buku:",
        lengthMenu: "Tampilkan _MENU_ entri",
        info: "Menampilkan _START_ - _END_ dari _TOTAL_ buku",
        paginate: {
          previous: "◀",
          next: "▶"
        },
        zeroRecords: "Tidak ditemukan buku yang sesuai",
        infoEmpty: "Tidak ada data"
      }
    });
  });
</script>

<?php include '../templates/footer.php'; ?>
</body>
</html>