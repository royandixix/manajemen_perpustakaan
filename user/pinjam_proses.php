<?php
session_start();
include '../config/connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$id_anggota = intval($_SESSION['user']['id_anggota_222274'] ?? 0);
$id_buku = intval($_POST['id_buku'] ?? 0);
$tanggal_pinjam = $_POST['tanggal_pinjam'] ?? null;
$tanggal_kembali = $_POST['tanggal_kembali'] ?? null;

if (!$id_buku || !$tanggal_pinjam || !$tanggal_kembali) {
    $_SESSION['pinjam_error'] = "Data tidak lengkap!";
    header("Location: Katalog.php");
    exit();
}

// Basic date validation (YYYY-MM-DD)
$dp = DateTime::createFromFormat('Y-m-d', $tanggal_pinjam);
$dk = DateTime::createFromFormat('Y-m-d', $tanggal_kembali);
if (!$dp || !$dk) {
    $_SESSION['pinjam_error'] = "Format tanggal tidak valid.";
    header("Location: form_pinjam.php?id_buku={$id_buku}");
    exit();
}

// =========================
// CEK JUMLAH PEMINJAMAN USER (MAX 2)
// =========================
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total 
    FROM peminjaman_222274
    WHERE id_anggota_222274 = ?
      AND status_222274 IN ('dipinjam','menunggu_konfirmasi_admin')
");
$stmt->bind_param("i", $id_anggota);
$stmt->execute();
$res = $stmt->get_result();
$total = (int)($res->fetch_assoc()['total'] ?? 0);
$stmt->close();

if ($total >= 2) {
    $_SESSION['pinjam_error'] = "Maksimal 2 buku yang masih aktif atau menunggu konfirmasi.";
    header("Location: Katalog.php");
    exit();
}

// =========================
// CEK USER SUDAH PINJAM BUKU YANG SAMA
// =========================
$stmt = $conn->prepare("
    SELECT id_peminjaman_222274 
    FROM peminjaman_222274
    WHERE id_anggota_222274 = ?
      AND id_buku_222274 = ?
      AND status_222274 IN ('dipinjam','menunggu_konfirmasi_admin')
");
$stmt->bind_param("ii", $id_anggota, $id_buku);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    $stmt->close();
    $_SESSION['pinjam_error'] = "Buku ini sudah Anda ajukan atau sedang dipinjam.";
    header("Location: Katalog.php");
    exit();
}
$stmt->close();

// =========================
// TRANSAKSI: CEK STOK (LOCK) & INSERT PERMOHONAN
// =========================
try {
    // mulai transaksi
    $conn->begin_transaction();

    // Ambil stok dengan lock supaya tidak terjadi race condition
    $stmt = $conn->prepare("SELECT stok_222274 FROM buku_222274 WHERE id_buku_222274 = ? FOR UPDATE");
    $stmt->bind_param("i", $id_buku);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    if (!$row) {
        $conn->rollback();
        $_SESSION['pinjam_error'] = "Buku tidak ditemukan.";
        header("Location: Katalog.php");
        exit();
    }

    $stok = (int)$row['stok_222274'];
    if ($stok <= 0) {
        $conn->rollback();
        $_SESSION['pinjam_error'] = "Stok buku habis!";
        header("Location: Katalog.php");
        exit();
    }

    // INSERT PERMOHONAN: status menunggu konfirmasi admin
    $stmt = $conn->prepare("
        INSERT INTO peminjaman_222274 
        (id_anggota_222274, id_buku_222274, id_admin_222274, tanggal_pinjam_222274, tanggal_kembali_222274, status_222274)
        VALUES (?, ?, NULL, ?, ?, 'menunggu_konfirmasi_admin')
    ");
    $stmt->bind_param("iiss", $id_anggota, $id_buku, $tanggal_pinjam, $tanggal_kembali);
    $ok = $stmt->execute();
    $stmt->close();

    if (!$ok) {
        $conn->rollback();
        $_SESSION['pinjam_error'] = "Gagal menyimpan permohonan. Silakan coba lagi.";
        header("Location: form_pinjam.php?id_buku={$id_buku}");
        exit();
    }

    // Commit transaksi (stok hanya berubah saat admin menyetujui)
    $conn->commit();

    $_SESSION['pinjam_success'] = "Permohonan peminjaman berhasil dikirim. Menunggu konfirmasi admin.";
    header("Location: riwayat.php");
    exit();

} catch (Exception $e) {
    // rollback on error
    if ($conn->errno) $conn->rollback();
    $_SESSION['pinjam_error'] = "Terjadi kesalahan: " . ($e->getMessage() ?: 'unknown');
    header("Location: Katalog.php");
    exit();
}
