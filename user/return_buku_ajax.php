<?php
session_start();
require '../config/connect.php';

// Pastikan USER login
if (!isset($_SESSION['user']) || !isset($_POST['id'])) {
    echo json_encode(['success'=>false, 'msg'=>'Anda harus login sebagai anggota']);
    exit;
}

$id_anggota = intval($_SESSION['user']['id_anggota_222274']);
$id_peminjaman = intval($_POST['id']);

// Cek apakah peminjaman milik user & masih dipinjam
$stmt = $conn->prepare("
    SELECT id_peminjaman_222274
    FROM peminjaman_222274
    WHERE id_peminjaman_222274 = ?
      AND id_anggota_222274 = ?
      AND status_222274 = 'dipinjam'
");
$stmt->bind_param("ii", $id_peminjaman, $id_anggota);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['success'=>false,'msg'=>'Data tidak valid atau buku sudah diajukan untuk dikembalikan']);
    exit;
}

// Update status ke menunggu admin
$stmtUp = $conn->prepare("
    UPDATE peminjaman_222274
    SET status_222274 = 'menunggu_konfirmasi_pengembalian'
    WHERE id_peminjaman_222274 = ?
");
$stmtUp->bind_param("i", $id_peminjaman);
$stmtUp->execute();
$stmtUp->close();

echo json_encode([
    'success' => true,
    'msg' => 'Permohonan pengembalian dikirim. Menunggu konfirmasi admin.'
]);
