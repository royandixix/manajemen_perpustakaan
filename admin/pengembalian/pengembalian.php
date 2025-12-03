<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

include '../../config/connect.php';

$id_admin = $_SESSION['admin']['id_admin_222274'];

// =============================================================
// KONFIRMASI PENGEMBALIAN BUKU
// =============================================================
if (isset($_GET['konfirmasi_id'])) {
    $id_peminjaman = intval($_GET['konfirmasi_id']);

    // Ambil data peminjaman yang statusnya menunggu konfirmasi pengembalian
    $data = $conn->query("
        SELECT * 
        FROM peminjaman_222274 
        WHERE id_peminjaman_222274 = $id_peminjaman 
          AND status_222274 = 'menunggu_konfirmasi_pengembalian'
    ")->fetch_assoc();

    if ($data) {
        $id_buku = $data['id_buku_222274'];

        // Update status peminjaman → dikembalikan, catat admin, tanggal_kembali
        $conn->query("
            UPDATE peminjaman_222274
            SET status_222274 = 'dikembalikan',
                tanggal_kembali_222274 = CURDATE(),
                id_admin_222274 = $id_admin
            WHERE id_peminjaman_222274 = $id_peminjaman
        ");

        // Tambahkan stok buku
        $conn->query("
            UPDATE buku_222274
            SET stok_222274 = stok_222274 + 1
            WHERE id_buku_222274 = $id_buku
        ");

        // Tambahkan record ke tabel pengembalian
        $conn->query("
            INSERT INTO pengembalian_222274
            (id_peminjaman_222274, tanggal_dikembalikan_222274, denda_222274)
            VALUES ($id_peminjaman, CURDATE(), 0)
        ");

        $success = "Pengembalian berhasil dikonfirmasi. Stok buku diperbarui.";
    } else {
        $error = "Peminjaman tidak ditemukan atau status tidak valid.";
    }
}

// =============================================================
// AMBIL DATA PEMINJAMAN YANG MENUNGGU KONFIRMASI PENGEMBALIAN
// =============================================================
$peminjaman_list = $conn->query("
    SELECT p.*, a.nama_222274 AS nama_anggota, b.judul_222274 AS judul_buku
    FROM peminjaman_222274 p
    JOIN anggota_222274 a ON p.id_anggota_222274 = a.id_anggota_222274
    JOIN buku_222274 b ON p.id_buku_222274 = b.id_buku_222274
    WHERE p.status_222274 = 'menunggu_konfirmasi_pengembalian'
    ORDER BY p.tanggal_pinjam_222274 DESC
");

include '../templates/header_sidebar.php';
?>

<div id="main-content" class="container mt-4">

    <h2 class="fw-bold text-primary mb-4 text-center">
        <span class="material-symbols-outlined align-middle me-1">assignment_return</span>
        Konfirmasi Pengembalian Buku
    </h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($peminjaman_list->num_rows > 0): ?>
                        <?php while ($row = $peminjaman_list->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id_peminjaman_222274'] ?></td>
                                <td><?= htmlspecialchars($row['nama_anggota']) ?></td>
                                <td><?= htmlspecialchars($row['judul_buku']) ?></td>
                                <td><?= $row['tanggal_pinjam_222274'] ?></td>
                                <td><?= $row['tanggal_kembali_222274'] ?: '-' ?></td>
                                <td>
                                    <a href="?konfirmasi_id=<?= $row['id_peminjaman_222274'] ?>"
                                       class="btn btn-success btn-sm"
                                       onclick="return confirm('Konfirmasi pengembalian buku ini?')">
                                        Konfirmasi
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Tidak ada pengembalian yang menunggu konfirmasi.</td>
                        </tr>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
