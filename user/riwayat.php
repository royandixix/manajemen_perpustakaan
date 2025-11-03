<?php
include 'templates/header.php';
include 'templates/navbar.php';
?>

<div class="container mt-5 pt-4">
  <h1 class="display-6 mb-4">Riwayat Peminjaman Saya</h1>
  <div class="row">
    <!-- Contoh Buku -->
    <div class="col-md-3 mb-4">
      <div class="card h-100">
        <img src="https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=500&q=80" class="card-img-top" alt="Buku">
        <div class="card-body">
          <h5 class="card-title">Belajar HTML & CSS</h5>
          <p class="card-text text-muted mb-1">📅 Pinjam: 2025-10-01</p>
          <p class="card-text text-muted mb-1">📅 Kembali: 2025-10-08</p>
          <p class="card-text"><span class="status-badge status-aktif">Aktif</span></p>
          <p class="card-text small text-danger">Denda: Rp 0</p>
        </div>
      </div>
    </div>
    <!-- Tambahkan buku lain di sini -->
  </div>
</div>

<?php
include 'templates/footer.php';
?>
