<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require '../config/connect.php';
require 'templates/header.php';
require 'templates/navbar.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$id_anggota = $_SESSION['user']['id_anggota_222274'];
$namaUser   = $_SESSION['user']['nama_222274'] ?? 'Pengguna';

// Ambil ID buku dari query string
$id_buku = intval($_GET['id_buku'] ?? 0);
if (!$id_buku) {
    $_SESSION['pinjam_error'] = "ID buku tidak valid.";
    header("Location: Katalog.php");
    exit();
}

// Ambil data buku (judul + stok)
$stmt = $conn->prepare("SELECT judul_222274, stok_222274, img_222274 FROM buku_222274 WHERE id_buku_222274 = ?");
$stmt->bind_param("i", $id_buku);
$stmt->execute();
$res  = $stmt->get_result();
$buku = $res->fetch_assoc();
$stmt->close();

if (!$buku) {
    $_SESSION['pinjam_error'] = "Buku tidak ditemukan.";
    header("Location: Katalog.php");
    exit();
}

// Pesan sukses / error dari proses sebelumnya
$flash_success = $_SESSION['pinjam_success'] ?? null;
if (isset($_SESSION['pinjam_success'])) unset($_SESSION['pinjam_success']);
$flash_error = $_SESSION['pinjam_error'] ?? null;
if (isset($_SESSION['pinjam_error'])) unset($_SESSION['pinjam_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pinjam Buku - <?= htmlspecialchars($buku['judul_222274']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero { margin-top: 1.5rem; margin-bottom: 1rem; }
        .book-thumb { width:100px; height:140px; object-fit:cover; border-radius:6px; }
    </style>
</head>
<body>
<div class="container py-4">

    <section class="hero">
        <h1 class="h3">Form Peminjaman Buku</h1>
        <p class="text-muted">Halo, <strong><?= htmlspecialchars($namaUser) ?></strong></p>
    </section>

    <?php if ($flash_success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($flash_success) ?></div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($flash_error) ?></div>
    <?php endif; ?>

    <div class="card mb-4 p-3 shadow-sm">
        <div class="d-flex gap-3">
            <?php
            $img = $buku['img_222274'] ? '../uploads/sampul/' . $buku['img_222274'] : 'https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=400';
            ?>
            <img src="<?= htmlspecialchars($img) ?>" alt="Sampul" class="book-thumb">
            <div class="flex-grow-1">
                <h5 class="mb-1"><?= htmlspecialchars($buku['judul_222274']) ?></h5>
                <p class="mb-1 text-muted">Stok: <strong><?= (int)$buku['stok_222274'] ?></strong></p>
                <p class="small text-muted">Silakan konfirmasi peminjaman. Permohonan akan dikirim ke admin untuk disetujui.</p>
            </div>
        </div>
    </div>

    <form action="pinjam_proses.php" method="POST" class="card p-3 shadow-sm">
        <input type="hidden" name="id_buku" value="<?= $id_buku ?>">

        <div class="mb-3">
            <label class="form-label">Tanggal Pinjam</label>
            <input type="date" name="tanggal_pinjam" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Kembali (perkiraan)</label>
            <input type="date" name="tanggal_kembali" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
        </div>

        <?php if ((int)$buku['stok_222274'] <= 0): ?>
            <div class="alert alert-warning">Stok buku habis. Anda tidak dapat mengajukan peminjaman saat ini.</div>
            <a href="Katalog.php" class="btn btn-secondary">Kembali ke Katalog</a>
        <?php else: ?>
            <button type="submit" class="btn btn-primary w-100">Kirim Permohonan</button>
            <a href="Katalog.php" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
        <?php endif; ?>

    </form>

</div>
</body>
</html>
