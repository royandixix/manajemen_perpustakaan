<?php
$namaUser = $_SESSION['user'] ?? 'Pengguna';
?>

<nav class="navbar navbar-expand-lg bg-white shadow-sm fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="Katalog.php">
      <span class="material-icons align-middle me-1">menu_book</span> PerpustakaanKu
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
        <li class="nav-item">
          <a class="nav-link" href="Katalog.php">Katalog Buku</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="riwayat.php">Riwayat Peminjaman</a>
        </li>

        <!-- Dropdown akun -->
        <li class="nav-item dropdown position-relative">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="accountDropdown">
            <span class="material-icons align-middle me-1">account_circle</span>
            <?= htmlspecialchars($namaUser) ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">Profil Saya</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="../login.php?logout=1">Keluar</a></li>
          </ul>
        </li>
      </ul>

      <form class="d-flex ms-3" role="search">
        <input class="form-control me-2" type="search" placeholder="Cari buku..." disabled>
        <button class="btn btn-outline-primary" type="button" disabled>Cari</button>
      </form>
    </div>
  </div>
</nav>

<style>
/* Hover effect link */
.navbar-nav .nav-link {
  transition: color 0.2s ease-in-out;
}
.navbar-nav .nav-link:hover {
  color: #0d6efd;
}

/* Dropdown akun hover */
.nav-item.dropdown {
  position: relative;
}
.nav-item.dropdown:hover > .dropdown-menu {
  display: block;
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

/* CSS smooth dropdown */
.dropdown-menu {
  display: none;
  opacity: 0;
  visibility: hidden;
  transform: translateY(10px);
  transition: all 0.25s ease-in-out;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}
</style>

<script>
// Mobile friendly: klik tetap bisa toggle dropdown
const dropdown = document.querySelector('.nav-item.dropdown .dropdown-toggle');
dropdown.addEventListener('click', function(e) {
  if (window.innerWidth < 992) { // lg breakpoint
    e.preventDefault();
    this.parentElement.classList.toggle('show');
    this.nextElementSibling.classList.toggle('show');
  }
});
</script>
