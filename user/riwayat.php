<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: ../login.php");
  exit();
}

require '../config/connect.php';
include 'templates/header.php';
include 'templates/navbar.php';

$namaUser   = $_SESSION['user']['nama_222274'] ?? 'Pengguna';
$id_anggota = $_SESSION['user']['id_anggota_222274'];

// 🔁 Kembalikan buku
if (isset($_POST['return_buku'])) {
  $id = intval($_POST['id_peminjaman']);
  $cek = mysqli_query($conn, "SELECT * FROM peminjaman_222274 WHERE id_peminjaman=$id AND id_anggota=$id_anggota AND status='dipinjam'");
  if (mysqli_num_rows($cek)) {
    $p = mysqli_fetch_assoc($cek);
    mysqli_query($conn, "UPDATE peminjaman_222274 SET status='dikembalikan', tanggal_kembali=CURDATE() WHERE id_peminjaman=$id");
    mysqli_query($conn, "UPDATE buku_222274 SET stok = stok+1 WHERE id_buku={$p['id_buku']}");
    $_SESSION['msg_success'] = "✅ Buku berhasil dikembalikan!";
    header("Location: riwayat.php");
    exit;
  }
}

// 🗑 Hapus riwayat
if (isset($_POST['hapus_riwayat'])) {
  $id = intval($_POST['id_peminjaman']);
  mysqli_query($conn, "DELETE FROM peminjaman_222274 WHERE id_peminjaman=$id AND id_anggota=$id_anggota");
  $_SESSION['msg_delete'] = "🗑️ Riwayat berhasil dihapus!";
  header("Location: riwayat.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Riwayat Peminjaman Buku</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background:#f7f9fc; font-family:'Inter',sans-serif; }
.hero {
  background: linear-gradient(135deg,#4e73df,#224abe);
  color:#fff; padding:3rem 1rem; border-radius:1rem;
  text-align:center; margin-bottom:2rem;
}
.hero h1 { font-weight:700; margin-bottom:.5rem; }
.card-book {
  border:none; border-radius:1rem;
  box-shadow:0 4px 12px rgba(0,0,0,0.05);
  transition:all .3s ease-in-out;
}
.card-book:hover { transform:translateY(-4px); box-shadow:0 6px 20px rgba(0,0,0,0.1); }
.book-img { width:85px; height:120px; border-radius:.8rem; object-fit:cover; }
.status-badge {
  font-size:.75rem; font-weight:600;
  border-radius:50px; padding:5px 12px;
}
.status-dipinjam { background:#0d6efd; color:#fff; }
.status-dikembalikan { background:#198754; color:#fff; }
.btn-return { background:#ffc107; color:#fff; border:none; font-weight:600; border-radius:.6rem; }
.btn-return:hover { background:#e0a800; }
.btn-hapus { background:#dc3545; color:#fff; border:none; font-weight:600; border-radius:.6rem; }
.btn-hapus:hover { background:#b02a37; }
.empty-state {
  text-align:center; padding:4rem 1rem;
  color:#6c757d;
}
.empty-state i { font-size:4rem; color:#c0c4cc; margin-bottom:1rem; }
</style>
</head>
<body>

<div class="container py-4">
  <section class="hero shadow-sm">
    <h1><i class="bi bi-clock-history"></i> Riwayat Peminjaman</h1>
    <p class="mb-0">Selamat datang, <b><?= htmlspecialchars($namaUser) ?></b></p>
  </section>

  <?php if(isset($_SESSION['msg_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
      <i class="bi bi-check-circle-fill me-2"></i><?= $_SESSION['msg_success']; unset($_SESSION['msg_success']); ?>
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if(isset($_SESSION['msg_delete'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
      <i class="bi bi-trash3-fill me-2"></i><?= $_SESSION['msg_delete']; unset($_SESSION['msg_delete']); ?>
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="row g-4">
  <?php
  $q = mysqli_query($conn, "SELECT p.*,b.judul,b.img FROM peminjaman_222274 p JOIN buku_222274 b ON p.id_buku=b.id_buku WHERE id_anggota=$id_anggota ORDER BY p.tanggal_pinjam DESC");
  if (mysqli_num_rows($q)):
    while($r=mysqli_fetch_assoc($q)):
      $img = $r['img'] ?: 'https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=400';
      $statusClass = $r['status']=='dipinjam' ? 'status-dipinjam' : 'status-dikembalikan';
      $tanggalKembali = $r['tanggal_kembali'] ?: '-';
  ?>
    <div class="col-md-6 col-lg-4">
      <div class="card card-book p-3 h-100">
        <div class="d-flex align-items-start">
          <img src="<?= htmlspecialchars($img) ?>" class="book-img me-3" alt="">
          <div>
            <h5 class="fw-semibold mb-1"><i class="bi bi-book-half text-primary me-1"></i><?= htmlspecialchars($r['judul']) ?></h5>
            <p class="text-muted small mb-1">
              <i class="bi bi-calendar-event me-1 text-secondary"></i>Pinjam: <?= $r['tanggal_pinjam'] ?><br>
              <i class="bi bi-calendar-check me-1 text-success"></i>Kembali: <?= $tanggalKembali ?>
            </p>
            <span class="status-badge <?= $statusClass ?>"><i class="bi bi-circle-fill me-1"></i><?= ucfirst($r['status']) ?></span>
          </div>
        </div>
        <div class="mt-3 d-flex justify-content-between">
          <?php if($r['status']=='dipinjam'): ?>
            <button class="btn btn-return w-50 me-2" data-id="<?= $r['id_peminjaman'] ?>" data-judul="<?= htmlspecialchars($r['judul']) ?>" data-bs-toggle="modal" data-bs-target="#returnModal"><i class="bi bi-arrow-return-left me-1"></i>Kembalikan</button>
          <?php endif; ?>
          <form method="POST" class="w-50">
            <input type="hidden" name="id_peminjaman" value="<?= $r['id_peminjaman'] ?>">
            <button type="submit" name="hapus_riwayat" onclick="return confirm('Yakin ingin menghapus riwayat ini?')" class="btn btn-hapus w-100"><i class="bi bi-trash3 me-1"></i>Hapus</button>
          </form>
        </div>
      </div>
    </div>
  <?php endwhile; else: ?>
    <div class="empty-state">
      <i class="bi bi-inbox"></i>
      <h5>Belum ada riwayat peminjaman</h5>
      <p>Coba pinjam buku di halaman katalog.</p>
    </div>
  <?php endif; mysqli_close($conn); ?>
  </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST">
      <input type="hidden" name="id_peminjaman" id="modalId">
      <div class="modal-content border-0 rounded-4 shadow">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-arrow-repeat me-1"></i> Konfirmasi Pengembalian</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Yakin ingin mengembalikan buku <b>"<span id="modalJudul"></span>"</b>?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i> Batal</button>
          <button type="submit" name="return_buku" class="btn btn-return"><i class="bi bi-check-circle me-1"></i> Kembalikan</button>
        </div>
      </div>
    </form>
  </div>
</div>


<script src ></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.btn-return').forEach(btn=>{
  btn.addEventListener('click',()=>{
    document.getElementById('modalId').value=btn.dataset.id;
    document.getElementById('modalJudul').textContent=btn.dataset.judul;
  });
});
</script>

</body>
</html>
