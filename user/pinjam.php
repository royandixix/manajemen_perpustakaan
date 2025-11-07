<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../config/connect.php';

// Pastikan user login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$id_anggota = $_SESSION['user']['id_anggota_222274'] ?? 0;
$id_buku = intval($_GET['id_buku'] ?? 0);

if (!$id_buku) {
    $_SESSION['pinjam_error'] = "ID buku tidak valid.";
    header("Location: Katalog.php");
    exit();
}

// Ambil data buku
$stmt = $conn->prepare("SELECT judul, stok FROM buku_222274 WHERE id_buku=?");
$stmt->bind_param("i", $id_buku);
$stmt->execute();
$res = $stmt->get_result();
$buku = $res->fetch_assoc();

if (!$buku) {
    $_SESSION['pinjam_error'] = "Buku tidak ditemukan.";
    header("Location: Katalog.php");
    exit();
}

// Cek stok
if ($buku['stok'] <= 0) {
    $_SESSION['pinjam_error'] = "Stok buku habis!";
    header("Location: Katalog.php");
    exit();
}

// Cek user sudah pinjam buku yang sama belum dikembalikan
$stmtCek = $conn->prepare("SELECT * FROM peminjaman_222274 WHERE id_anggota=? AND id_buku=? AND status='dipinjam'");
$stmtCek->bind_param("ii", $id_anggota, $id_buku);
$stmtCek->execute();
$resCek = $stmtCek->get_result();
if ($resCek->num_rows > 0) {
    $_SESSION['pinjam_error'] = "Kamu sudah meminjam buku ini dan belum mengembalikannya.";
    header("Location: Katalog.php");
    exit();
}

// Insert peminjaman
$stmtInsert = $conn->prepare("INSERT INTO peminjaman_222274 (id_anggota, id_buku, tanggal_pinjam, status) VALUES (?, ?, CURDATE(), 'dipinjam')");
$stmtInsert->bind_param("ii", $id_anggota, $id_buku);
$stmtInsert->execute();

// Kurangi stok buku
$stmtUpdate = $conn->prepare("UPDATE buku_222274 SET stok=stok-1 WHERE id_buku=?");
$stmtUpdate->bind_param("i", $id_buku);
$stmtUpdate->execute();

$_SESSION['pinjam_success'] = "Berhasil meminjam buku: {$buku['judul']}";
header("Location: riwayat.php");
exit();
