<?php
include '../../config/connect.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../../login.php");
    exit();
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM buku_222274 WHERE id_buku = $id";
$result = $conn->query($sql);
if($result->num_rows == 0){
    die("Buku tidak ditemukan.");
}
$buku = $result->fetch_assoc();

if(isset($_POST['submit'])){
    $judul = $conn->real_escape_string($_POST['judul']);
    $penulis = $conn->real_escape_string($_POST['penulis']);
    $penerbit = $conn->real_escape_string($_POST['penerbit']);
    $tahun = $conn->real_escape_string($_POST['tahun_terbit']);
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $stok = intval($_POST['stok']);

    $sql_update = "UPDATE buku_222274 SET 
        judul='$judul', penulis='$penulis', penerbit='$penerbit', 
        tahun_terbit='$tahun', kategori='$kategori', stok=$stok 
        WHERE id_buku=$id";
    
    if($conn->query($sql_update) === TRUE){
        header("Location: buku.php");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<?php include '../templates/header_sidebar.php'; ?>

<div id="main-content">
  <h2 class="fw-bold text-primary mb-4">
    <span class="material-symbols-outlined align-middle">edit</span> Edit Buku
  </h2>

  <?php if(isset($error)) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form action="" method="POST">
        <div class="mb-3">
          <label for="judul" class="form-label">Judul Buku</label>
          <input type="text" name="judul" id="judul" class="form-control" value="<?= $buku['judul'] ?>" required>
        </div>
        <div class="mb-3">
          <label for="penulis" class="form-label">Pengarang</label>
          <input type="text" name="penulis" id="penulis" class="form-control" value="<?= $buku['penulis'] ?>">
        </div>
        <div class="mb-3">
          <label for="penerbit" class="form-label">Penerbit</label>
          <input type="text" name="penerbit" id="penerbit" class="form-control" value="<?= $buku['penerbit'] ?>">
        </div>
        <div class="mb-3">
          <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
          <input type="number" name="tahun_terbit" id="tahun_terbit" class="form-control" value="<?= $buku['tahun_terbit'] ?>" min="1900" max="<?= date('Y') ?>">
        </div>
        <div class="mb-3">
          <label for="kategori" class="form-label">Kategori</label>
          <input type="text" name="kategori" id="kategori" class="form-control" value="<?= $buku['kategori'] ?>">
        </div>
        <div class="mb-3">
          <label for="stok" class="form-label">Stok</label>
          <input type="number" name="stok" id="stok" class="form-control" value="<?= $buku['stok'] ?>" min="0">
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Update</button>
        <a href="buku.php" class="btn btn-secondary">Kembali</a>
      </form>
    </div>
  </div>
</div>

<?php include '../templates/footer.php'; ?>
