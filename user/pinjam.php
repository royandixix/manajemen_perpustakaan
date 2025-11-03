<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['id_anggota_222274'])) {
    header("Location: login.php");
    exit();
}

// Koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "perpustakaan_db_222274";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil id_buku dari GET
if (!isset($_GET['id_buku'])) {
    header("Location: katalog.php");
    exit();
}

$id_buku = intval($_GET['id_buku']);
$id_anggota = $_SESSION['id_anggota_222274'];

// Cek stok buku
$bukuQuery = "SELECT stok FROM buku_222274 WHERE id_buku = $id_buku";
$bukuResult = mysqli_query($conn, $bukuQuery);
if (!$bukuResult || mysqli_num_rows($bukuResult) == 0) {
    die("Buku tidak ditemukan.");
}

$buku = mysqli_fetch_assoc($bukuResult);
if ($buku['stok'] <= 0) {
    die("Buku stok habis.");
}

// Insert ke tabel peminjaman
$pinjamQuery = "INSERT INTO peminjaman_222274 (id_anggota, id_buku, tanggal_pinjam, status) VALUES ($id_anggota, $id_buku, CURDATE(), 'dipinjam')";
mysqli_query($conn, $pinjamQuery);

// Kurangi stok buku
$updateStok = "UPDATE buku_222274 SET stok = stok - 1 WHERE id_buku = $id_buku";
mysqli_query($conn, $updateStok);

// Redirect ke katalog dengan pesan sukses
header("Location: katalog.php?success=1");
exit();
?>
