<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: ../login.php");
  exit();
}
?>

<?php include  '../templates/header_sidebar.php'?>

<div id="main-content">
  <h2 class="fw-bold text-primary mb-4">
    <span class="material-symbols-outlined align-middle">dashboard</span> Dashboard Admin
  </h2>

  <div class="row g-4 mt-3">
    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm border-0">
        <span class="material-symbols-outlined text-primary fs-1 mb-2">menu_book</span>
        <h5 class="fw-semibold">Total Buku</h5>
        <h2 class="text-primary fw-bold">1.200</h2>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm border-0">
        <span class="material-symbols-outlined text-success fs-1 mb-2">group</span>
        <h5 class="fw-semibold">Total Anggota</h5>
        <h2 class="text-success fw-bold">350</h2>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm border-0">
        <span class="material-symbols-outlined text-warning fs-1 mb-2">assignment_return</span>
        <h5 class="fw-semibold">Buku Dipinjam</h5>
        <h2 class="text-warning fw-bold">75</h2>
      </div>
    </div>
  </div>

  <footer class="text-center mt-4 mb-2 text-secondary small">
    &copy; <?= date('Y') ?> PerpustakaanKu. Semua hak dilindungi.
  </footer>
</div>




<?php include 'templates/footer.php'; ?>

<script s
