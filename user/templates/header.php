<?php
// Mulai session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil nama user dari session, default 'Pengguna'
$namaUser = 'Pengguna';
if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
    $namaUser = $_SESSION['user']['nama_222274'] ?? 'Pengguna';
}

// Lindungi output agar aman di HTML
$namaUser = htmlspecialchars($namaUser, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PerpustakaanKu</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<style>
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; }
.card { border-radius: 15px; transition: transform 0.2s ease, box-shadow 0.2s ease; }
.card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
.status-badge { padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
.status-aktif { background-color: #d1e7dd; color: #0f5132; }
.status-terlambat { background-color: #f8d7da; color: #842029; }
.status-selesai { background-color: #cff4fc; color: #055160; }
</style>
</head>
<body class="pt-5">
