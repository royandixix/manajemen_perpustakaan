<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

require '../config/connect.php';
include 'templates/header.php';
include 'templates/navbar.php';

$id_anggota = $_SESSION['user']['id_anggota_222274'];
$namaUser   = htmlspecialchars($_SESSION['user']['nama_222274'] ?? 'Pengguna');

// =============================================================
// 🟢 AJUKAN PENGEMBALIAN BUKU (USER)
// =============================================================
if (isset($_POST['return_buku'])) {
    $id = intval($_POST['id_peminjaman']);

    // Cek apakah buku masih dipinjam
    $stmt = $conn->prepare("
        SELECT id_buku_222274 
        FROM peminjaman_222274 
        WHERE id_peminjaman_222274=? 
          AND id_anggota_222274=? 
          AND status_222274='dipinjam'
    ");
    $stmt->bind_param("ii", $id, $id_anggota);
    $stmt->execute();
    $cek = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($cek) {
        // Update status ke menunggu konfirmasi pengembalian
        $stmtUp = $conn->prepare("
            UPDATE peminjaman_222274 
            SET status_222274='menunggu_konfirmasi_pengembalian'
            WHERE id_peminjaman_222274=?
        ");
        $stmtUp->bind_param("i", $id);
        $stmtUp->execute();
        $stmtUp->close();

        $_SESSION['msg_success'] = "Permintaan pengembalian dikirim. Menunggu konfirmasi admin!";
        header("Location: riwayat.php");
        exit();
    }
}

// =============================================================
// 🗑 HAPUS RIWAYAT (HANYA SUDAH DIKEMBALIKAN)
// =============================================================
if (isset($_POST['hapus_riwayat'])) {
    $id = intval($_POST['id_peminjaman']);

    $stmtDel = $conn->prepare("
        DELETE FROM peminjaman_222274 
        WHERE id_peminjaman_222274=? 
          AND id_anggota_222274=? 
          AND status_222274='dikembalikan'
    ");
    $stmtDel->bind_param("ii", $id, $id_anggota);
    $stmtDel->execute();
    $stmtDel->close();

    $_SESSION['msg_delete'] = "Riwayat berhasil dihapus!";
    header("Location: riwayat.php");
    exit();
}

// =============================================================
// AMBIL DATA RIWAYAT PEMINJAMAN
// =============================================================
$stmtList = $conn->prepare("
    SELECT p.id_peminjaman_222274, p.id_buku_222274, 
           p.tanggal_pinjam_222274, p.tanggal_kembali_222274, p.status_222274,
           b.judul_222274, b.img_222274, b.stok_222274
    FROM peminjaman_222274 p
    JOIN buku_222274 b ON p.id_buku_222274 = b.id_buku_222274
    WHERE p.id_anggota_222274=?
    ORDER BY p.tanggal_pinjam_222274 DESC
");
$stmtList->bind_param("i", $id_anggota);
$stmtList->execute();
$riwayat = $stmtList->get_result();
$stmtList->close();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Peminjaman Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f7f9fc;
            font-family: 'Inter', sans-serif;
        }

        .hero {
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: #fff;
            padding: 3rem 1rem;
            border-radius: 1rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .card-book {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .book-img {
            width: 85px;
            height: 120px;
            border-radius: .8rem;
            object-fit: cover;
        }

        .status-badge {
            font-size: .75rem;
            border-radius: 50px;
            padding: 5px 12px;
        }

        .status-dipinjam {
            background: #0d6efd;
            color: #fff;
        }

        .status-dikembalikan {
            background: #198754;
            color: #fff;
        }

        .status-menunggu {
            background: #ffc107;
            color: #fff;
        }

        .btn-return {
            background: #ffc107;
            color: #fff;
        }

        .btn-hapus {
            background: #dc3545;
            color: #fff;
        }

        .alert-wait {
            background: #fff3cd;
            color: #856404;
            border-radius: .5rem;
            text-align: center;
            padding: 0.5rem;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="container py-4">
        <section class="hero shadow-sm">
            <h1><i class="bi bi-clock-history"></i> Riwayat Peminjaman</h1>
            <p>Selamat datang, <b><?= htmlspecialchars($namaUser) ?></b></p>
        </section>

        <?php if (isset($_SESSION['msg_success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['msg_success'];
                                                unset($_SESSION['msg_success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['msg_delete'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['msg_delete'];
                                            unset($_SESSION['msg_delete']); ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <?php if ($riwayat->num_rows > 0): ?>
                <?php while ($r = $riwayat->fetch_assoc()):
                    $img = $r['img_222274'] ? "../uploads/sampul/" . $r['img_222274']
                        : "https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=400";

                    $statusClass = match ($r['status_222274']) {
                        "dipinjam" => "status-dipinjam",
                        "dikembalikan" => "status-dikembalikan",
                        "menunggu_konfirmasi_pengembalian" => "status-menunggu",
                        "menunggu_konfirmasi_admin" => "status-menunggu",
                        "dibatalkan" => "status-menunggu",
                        default => "status-menunggu",
                    };
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-book p-3 h-100">
                            <div class="d-flex">
                                <img src="<?= htmlspecialchars($img) ?>" class="book-img me-3">
                                <div>
                                    <h5><?= htmlspecialchars($r['judul_222274']) ?></h5>
                                    <p class="small text-muted">
                                        Pinjam: <?= $r['tanggal_pinjam_222274'] ?><br>
                                        Kembali: <?= $r['tanggal_kembali_222274'] ?: '-' ?><br>
                                        Stok: <?= $r['stok_222274'] ?>
                                    </p>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <?= ucfirst(str_replace('_', ' ', $r['status_222274'])) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="mt-3">
                                <?php if ($r['status_222274'] == "dipinjam"): ?>
                                    <form method="POST">
                                        <input type="hidden" name="id_peminjaman" value="<?= $r['id_peminjaman_222274'] ?>">
                                        <button type="submit" name="return_buku"
                                            onclick="return confirm('Ajukan pengembalian buku ini?')"
                                            class="btn btn-return w-100">
                                            <i class="bi bi-arrow-return-left"></i> Ajukan Pengembalian
                                        </button>
                                    </form>
                                <?php elseif ($r['status_222274'] == "menunggu_konfirmasi_pengembalian"): ?>
                                    <div class="alert-wait">Sedang menunggu konfirmasi admin</div>
                                <?php elseif ($r['status_222274'] == "dikembalikan"): ?>
                                    <form method="POST">
                                        <input type="hidden" name="id_peminjaman" value="<?= $r['id_peminjaman_222274'] ?>">
                                        <button type="submit" name="hapus_riwayat"
                                            onclick="return confirm('Hapus riwayat ini?')"
                                            class="btn btn-hapus w-100">
                                            <i class="bi bi-trash"></i> Hapus Riwayat
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <h5>Belum ada riwayat peminjaman</h5>
                </div>
            <?php endif; ?>
        </div>

</body>

</html>