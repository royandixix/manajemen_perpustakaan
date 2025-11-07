<?php
session_start();
require '../config/connect.php';

if (!isset($_SESSION['user']) || !isset($_POST['id'])) {
    echo json_encode(['success'=>false,'msg'=>'Akses ditolak']);
    exit;
}

$id_anggota = $_SESSION['user']['id_anggota_222274'];
$id = intval($_POST['id']);

// Cek buku yang sedang dipinjam
$cek = mysqli_query($conn, "SELECT * FROM peminjaman_222274 WHERE id_peminjaman=$id AND id_anggota=$id_anggota AND status='dipinjam'");
if(mysqli_num_rows($cek)){
    $p = mysqli_fetch_assoc($cek);

    // Update status peminjaman
    mysqli_query($conn, "UPDATE peminjaman_222274 SET status='dikembalikan', tanggal_kembali=CURDATE() WHERE id_peminjaman=$id");
    // Update stok buku
    mysqli_query($conn, "UPDATE buku_222274 SET stok=stok+1 WHERE id_buku={$p['id_buku']}");
    // Tambahkan record pengembalian
    mysqli_query($conn, "INSERT INTO pengembalian_222274 (id_peminjaman, tanggal_dikembalikan, denda) VALUES ($id, CURDATE(), 0)");

    echo json_encode(['success'=>true,'msg'=>'Buku berhasil dikembalikan']);
}else{
    echo json_encode(['success'=>false,'msg'=>'Buku tidak bisa dikembalikan']);
}
?>
