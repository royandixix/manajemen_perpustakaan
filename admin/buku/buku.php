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
    $conn->query("DELETE FROM buku_222274 WHERE id_buku_222274 = $id");
    header("Location: buku.php");
    exit();
}

// Ambil data buku, termasuk deskripsi
$sql = "SELECT * FROM buku_222274 ORDER BY id_buku_222274 ASC";
$result = $conn->query($sql);
?>

<?php include '../templates/header_sidebar.php'; ?>

<style>
/* ... tetap pakai style lama ... */
td img { border-radius:8px; transition: transform 0.3s ease;}
td img:hover { transform: scale(1.2);}
</style>

<div id="main-content">
<h2 class="fw-bold mb-4 mt-5">
    <span class="material-symbols-outlined align-middle me-2 text-primary">menu_book</span> Manajemen Buku
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
    <th>No</th>
    <th>Gambar</th>
    <th>Judul Buku</th>
    <th>Deskripsi</th> <!-- kolom baru -->
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
    <?php $no = 1; ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <?php
        $imgField = !empty($row['img_222274']) ? trim($row['img_222274']) : '';
        $imgSrc = !empty($imgField)
            ? "../../uploads/sampul/" . $imgField
            : "https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=80&q=80";
        ?>
        <tr>
            <td><?= $no ?></td>
            <td><img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($row['judul_222274']) ?>" width="55" height="75"></td>
            <td><?= htmlspecialchars($row['judul_222274']) ?></td>
            <td><?= htmlspecialchars($row['deskripsi_222274'] ?? '-') ?></td> <!-- tampilkan deskripsi -->
            <td><?= htmlspecialchars($row['penulis_222274']) ?></td>
            <td><?= htmlspecialchars($row['penerbit_222274']) ?></td>
            <td><?= $row['tahun_terbit_222274'] ?></td>
            <td><span class="badge bg-primary"><?= htmlspecialchars($row['kategori_222274']) ?></span></td>
            <td><span class="badge bg-<?= $row['stok_222274'] > 0 ? 'success' : 'danger' ?>"><?= $row['stok_222274'] ?></span></td>
            <td>
                <a href="edit_buku.php?id=<?= $row['id_buku_222274'] ?>" class="btn btn-warning btn-sm me-1">
                    <span class="material-symbols-outlined align-middle">edit</span>
                </a>
                <a href="buku.php?hapus=<?= $row['id_buku_222274'] ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Yakin ingin menghapus buku ini?')">
                    <span class="material-symbols-outlined align-middle">delete</span>
                </a>
            </td>
        </tr>
    <?php $no++; endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="10" class="text-center text-muted py-3">Belum ada data buku.</td>
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">

<script>
$(document).ready(function() {
    $('#example').DataTable({
        responsive: true,
        pageLength: 6,
        order: [],
        language: {
            search: "🔍 Cari Buku:",
            lengthMenu: "Tampilkan _MENU_ entri",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ buku",
            paginate: { previous: "◀", next: "▶" },
            zeroRecords: "Tidak ditemukan buku yang sesuai",
            infoEmpty: "Tidak ada data"
        }
    });
});
</script>

<?php include '../templates/footer.php'; ?>
