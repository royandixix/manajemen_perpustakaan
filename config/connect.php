<?php
$host = "localhost";
$user = "root";     // default XAMPP
$pass = "";         // default XAMPP
$db   = "perpustakaan_db_222274";

$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
