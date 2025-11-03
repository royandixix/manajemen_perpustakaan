<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f8f9fa;
}

/* Navbar */
.navbar {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1030;
    background: linear-gradient(90deg, #0d6efd, #007bff);
    color: white;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

/* Sidebar */
#sidebar {
    width: 250px;
    min-height: 100vh;
    background-color: #fff;
    padding-top: 70px;
    position: fixed;
    top: 0;
    left: 0;
    box-shadow: 2px 0 8px rgba(0,0,0,0.1);
    z-index: 1020;
    transition: all 0.3s;
}

#sidebar .nav-link {
    border-radius: 8px;
    margin-bottom: 6px;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.25s;
    padding: 10px 12px;
}

#sidebar .nav-link.active {
    background-color: #0d6efd;
    color: #fff !important;
    box-shadow: 0 3px 8px rgba(13,110,253,0.3);
}

#sidebar .nav-link:hover {
    background-color: #e9f2ff;
    color: #0d6efd;
    transform: translateX(4px);
}

#main-content {
    margin-left: 250px;
    padding: 20px;
    padding-top: 80px;
}

@media (max-width: 991px) {
    #sidebar {
        position: relative;
        width: 100%;
        min-height: auto;
        top: 0;
        box-shadow: none;
        padding-top: 10px;
    }
    #main-content {
        margin-left: 0;
        padding-top: 100px;
    }
}
</style>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
<div class="container-fluid">
    <a class="navbar-brand" href="index.php">
        <span class="material-symbols-outlined">library_books</span> Perpustakaan
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item me-3">
                <span class="text-white fw-semibold">
                    👋 Halo, <?= htmlspecialchars($_SESSION['admin']) ?>
                </span>
            </li>
            <li class="nav-item">
                <a href="../logout.php" class="btn btn-outline-light btn-sm">
                    <span class="material-symbols-outlined align-middle">logout</span> Keluar
                </a>
            </li>
        </ul>
    </div>
</div>
</nav>

<!-- Sidebar -->
<div id="sidebar" class="bg-white border-end shadow-sm position-fixed">
<h5 class="text-center text-primary fw-bold mb-3">Admin Panel</h5>
<ul class="nav flex-column px-3">
    <li class="nav-item mb-1">
        <a class="nav-link <?= $current_page=='index.php' ? 'active' : '' ?>" href="../index/index.php">
            <span class="material-symbols-outlined">dashboard</span> Dashboard
        </a>
    </li>
    <li class="nav-item mb-1">
        <a class="nav-link <?= $current_page=='buku.php' ? 'active' : '' ?>" href="../buku/buku.php">
            <span class="material-symbols-outlined">menu_book</span> Buku
        </a>
    </li>
    <li class="nav-item mb-1">
        <a class="nav-link <?= $current_page=='anggota.php' ? 'active' : '' ?>" href="../anggota/anggota.php">
            <span class="material-symbols-outlined">group</span> Anggota
        </a>
    </li>
    <li class="nav-item mb-1">
        <a class="nav-link <?= $current_page=='peminjaman.php' ? 'active' : '' ?>" href="../peminjaman/peminjaman.php">
            <span class="material-symbols-outlined">library_add</span> Peminjaman
        </a>
    </li>
    <li class="nav-item mb-1">
        <a class="nav-link <?= $current_page=='pengembalian.php' ? 'active' : '' ?>" href="../pengembalian/pengembalian.php">
            <span class="material-symbols-outlined">assignment_return</span> Pengembalian
        </a>
    </li>
</ul>
</div>
