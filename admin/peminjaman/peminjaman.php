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

$id_admin = $_SESSION['admin']['id_admin_222274'];

// =============================================================
// KONFIRMASI PEMINJAMAN USER
// =============================================================
if (isset($_GET['konfirmasi_id'])) {
    $id = intval($_GET['konfirmasi_id']);
    $aksi = $_GET['aksi'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM peminjaman_222274 WHERE id_peminjaman_222274 = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($p) {
        $id_buku = $p['id_buku_222274'];
        $status = $p['status_222274'];

        if ($status == 'menunggu_konfirmasi_admin') {

            if ($aksi == 'setuju') {
                // Set status DIPINJAM
                $stmtUp = $conn->prepare("
                    UPDATE peminjaman_222274 
                    SET status_222274 = 'dipinjam', id_admin_222274 = ? 
                    WHERE id_peminjaman_222274 = ?
                ");
                $stmtUp->bind_param("ii", $id_admin, $id);
                $stmtUp->execute();
                $stmtUp->close();

                // Kurangi stok buku
                $stmtStok = $conn->prepare("UPDATE buku_222274 SET stok_222274 = stok_222274 - 1 WHERE id_buku_222274 = ?");
                $stmtStok->bind_param("i", $id_buku);
                $stmtStok->execute();
                $stmtStok->close();

                $success = "Peminjaman berhasil disetujui.";

            } elseif ($aksi == 'tolak') {
                // Set status DIBATALKAN
                $stmtUp = $conn->prepare("
                    UPDATE peminjaman_222274 
                    SET status_222274 = 'dibatalkan', id_admin_222274 = ? 
                    WHERE id_peminjaman_222274 = ?
                ");
                $stmtUp->bind_param("ii", $id_admin, $id);
                $stmtUp->execute();
                $stmtUp->close();

                $success = "Peminjaman berhasil ditolak.";
            }

        } else {
            $error = "Status peminjaman tidak valid untuk dikonfirmasi.";
        }
    } else {
        $error = "Data peminjaman tidak ditemukan.";
    }
}

// =============================================================
// HAPUS PEMINJAMAN
// =============================================================
if (isset($_GET['hapus_id'])) {
    $id = intval($_GET['hapus_id']);

    $stmt = $conn->prepare("SELECT * FROM peminjaman_222274 WHERE id_peminjaman_222274 = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($p) {
        $id_buku = $p['id_buku_222274'];
        $status  = $p['status_222274'];

        $stmtDel = $conn->prepare("DELETE FROM peminjaman_222274 WHERE id_peminjaman_222274 = ?");
        $stmtDel->bind_param("i", $id);
        $stmtDel->execute();
        $stmtDel->close();

        // Jika masih DIPINJAM → kembalikan stok
        if ($status == 'dipinjam') {
            $stmtStok = $conn->prepare("UPDATE buku_222274 SET stok_222274 = stok_222274 + 1 WHERE id_buku_222274 = ?");
            $stmtStok->bind_param("i", $id_buku);
            $stmtStok->execute();
            $stmtStok->close();
        }

        $success = "Peminjaman berhasil dihapus.";
    } else {
        $error = "Data peminjaman tidak ditemukan.";
    }
}

// =============================================================
// DATA UNTUK FORM TAMBAH
// =============================================================
$anggota_list = $conn->query("SELECT * FROM anggota_222274 ORDER BY nama_222274 ASC");
$buku_list    = $conn->query("SELECT * FROM buku_222274 ORDER BY judul_222274 ASC");

// =============================================================
// DATA TABEL PEMINJAMAN
// =============================================================
$peminjaman_list = $conn->query("
    SELECT p.*, a.nama_222274 AS nama_anggota, b.judul_222274 AS judul_buku
    FROM peminjaman_222274 p
    JOIN anggota_222274 a ON p.id_anggota_222274 = a.id_anggota_222274
    JOIN buku_222274 b ON p.id_buku_222274 = b.id_buku_222274
    ORDER BY p.id_peminjaman_222274 DESC
");

include '../templates/header_sidebar.php';
?>

<div id="main-content" class="container mt-4">

<h2 class="fw-bold text-primary mb-4">
    <span class="material-symbols-outlined align-middle me-1">library_books</span>
    Transaksi Peminjaman Buku
</h2>

<?php if(isset($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if(isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- FORM TAMBAH PEMINJAMAN -->
<div class="card p-4 mb-4 shadow-sm">
    <h5 class="fw-bold mb-3">Tambah Peminjaman Manual</h5>
    <form method="POST" action="tambah_peminjaman.php">
        <div class="mb-3">
            <label class="form-label">Pilih Anggota</label>
            <select class="form-select" name="anggota" required>
                <option value="">-- Pilih Anggota --</option>
                <?php while($a = $anggota_list->fetch_assoc()): ?>
                    <option value="<?= $a['id_anggota_222274'] ?>"><?= htmlspecialchars($a['nama_222274']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Pilih Buku</label>
            <select class="form-select" name="buku" required>
                <option value="">-- Pilih Buku --</option>
                <?php while($b = $buku_list->fetch_assoc()): ?>
                    <option value="<?= $b['id_buku_222274'] ?>">
                        <?= htmlspecialchars($b['judul_222274']) ?> (stok: <?= $b['stok_222274'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button class="btn btn-primary w-100">
            <span class="material-symbols-outlined align-middle me-1">save</span>
            Simpan Peminjaman
        </button>
    </form>
</div>

<!-- TABEL PEMINJAMAN -->
<div class="card p-4 shadow-sm">
    <h5 class="fw-bold mb-3">Daftar Transaksi Peminjaman</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Anggota</th>
                    <th>Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $peminjaman_list->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_peminjaman_222274'] ?></td>
                    <td><?= htmlspecialchars($row['nama_anggota']) ?></td>
                    <td><?= htmlspecialchars($row['judul_buku']) ?></td>
                    <td><?= $row['tanggal_pinjam_222274'] ?></td>
                    <td><?= $row['tanggal_kembali_222274'] ?: '-' ?></td>
                    <td><?= ucfirst(str_replace('_',' ',$row['status_222274'])) ?></td>
                    <td>
                        <?php if($row['status_222274'] == 'menunggu_konfirmasi_admin'): ?>
                            <a href="?konfirmasi_id=<?= $row['id_peminjaman_222274'] ?>&aksi=setuju" class="btn btn-success btn-sm">Setujui</a>
                            <a href="?konfirmasi_id=<?= $row['id_peminjaman_222274'] ?>&aksi=tolak" class="btn btn-danger btn-sm">Tolak</a>
                        <?php else: ?>
                            <a href="edit_peminjaman.php?id=<?= $row['id_peminjaman_222274'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="?hapus_id=<?= $row['id_peminjaman_222274'] ?>" onclick="return confirm('Hapus peminjaman ini?')" class="btn btn-danger btn-sm">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<footer class="text-center mt-4 mb-2 text-secondary">
    &copy; <?= date('Y') ?> PerpustakaanKu
</footer>

</div>
</body>
</html>
