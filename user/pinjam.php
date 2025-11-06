<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../config/connect.php';


if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$id_anggota = $_SESSION['user']['id_anggota_222274'] ?? 0;

// Ambil ID buku dari GET, bukan POST
$id_buku = $_GET['id_buku'] ?? 0;

if (!$id_buku) {
    $_SESSION['pinjam_error'] = "ID buku tidak valid.";
    header("Location: katalog.php");
    exit();
}

// Ambil data buku
$sql = "SELECT judul, stok FROM buku_222274 WHERE id_buku = $id_buku";
$res = mysqli_query($conn, $sql);

if (!$res) {
    die("Query error: " . mysqli_error($conn));
}

$buku = mysqli_fetch_assoc($res);

if (!$buku) {
    $_SESSION['pinjam_error'] = "Buku tidak ditemukan.";
    header("Location: katalog.php");
    exit();
}

$judul_buku = $buku['judul'];

// Cek stok buku
if ($buku['stok'] <= 0) {
    $_SESSION['pinjam_error'] = "Stok buku habis!";
    header("Location: katalog.php");
    exit();
}

// Tambah ke tabel peminjaman
$sqlInsert = "INSERT INTO peminjaman_222274 (id_anggota, id_buku, tanggal_pinjam, status)
              VALUES ($id_anggota, $id_buku, CURDATE(), 'dipinjam')";
if (!mysqli_query($conn, $sqlInsert)) {
    die("Gagal insert peminjaman: " . mysqli_error($conn));
}

// Kurangi stok
$sqlUpdate = "UPDATE buku_222274 SET stok = stok - 1 WHERE id_buku = $id_buku";
if (!mysqli_query($conn, $sqlUpdate)) {
    die("Gagal update stok: " . mysqli_error($conn));
}

$_SESSION['pinjam_success'] = "Berhasil meminjam buku: $judul_buku";
header("Location: riwayat.php");
exit();
?>

