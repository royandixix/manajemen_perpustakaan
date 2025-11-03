<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../config/connect.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../../login.php");
    exit();
}

// Cek ID buku
if (!isset($_GET['id'])) {
    header("Location: buku.php");
    exit();
}
$id_buku = intval($_GET['id']);

// Ambil data buku
$result = $conn->query("SELECT * FROM buku_222274 WHERE id_buku = $id_buku");
if ($result->num_rows == 0) die("Buku tidak ditemukan.");
$buku = $result->fetch_assoc();

// Daftar kategori
$kategori_list = ['Pemrograman', 'Matematika', 'Web', 'Jaringan'];

$success = $error = '';

// Folder upload
$target_dir = __DIR__ . '/../../public/img/';
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0775, true);
    chmod($target_dir, 0775);
}

// Pastikan folder writable
if (!is_writable($target_dir)) {
    die("Folder public/img/ tidak bisa ditulis. Jalankan: sudo chown -R _www:_www public/img");
}

if (isset($_POST['submit'])) {
    $judul = $conn->real_escape_string($_POST['judul']);
    $penulis = $conn->real_escape_string($_POST['penulis']);
    $penerbit = $conn->real_escape_string($_POST['penerbit']);
    $tahun = intval($_POST['tahun_terbit']);
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $stok = intval($_POST['stok']);
    $img_name = $buku['img'] ?? '';

    // Upload gambar baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $allowed_ext = ['jpg','jpeg','png','gif','webp'];
        $file_tmp  = $_FILES['gambar']['tmp_name'];
        $file_ext  = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            $new_name = uniqid('buku_') . '.' . $file_ext;
            $dest = $target_dir . $new_name;

            if (move_uploaded_file($file_tmp, $dest)) {
                // hapus gambar lama jika ada
                if (!empty($img_name) && file_exists($target_dir . $img_name)) {
                    @unlink($target_dir . $img_name);
                }
                $img_name = $new_name;
            } else {
                $error = "Gagal memindahkan file, periksa permission folder public/img/";
            }
        } else {
            $error = "Format gambar tidak didukung! Gunakan jpg, jpeg, png, gif, webp.";
        }
    }

    // Update database
    if (!$error) {
        $stmt = $conn->prepare("UPDATE buku_222274 SET judul=?, penulis=?, penerbit=?, tahun_terbit=?, kategori=?, stok=?, img=? WHERE id_buku=?");
        $stmt->bind_param("sssisisi", $judul, $penulis, $penerbit, $tahun, $kategori, $stok, $img_name, $id_buku);
        if ($stmt->execute()) {
            $success = "Buku berhasil diupdate!";
            $buku['img'] = $img_name;
        } else {
            $error = "Error: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<?php include '../templates/header_sidebar.php'; ?>

<div id="main-content">
  <h2 class="fw-bold text-primary mb-4">
    <span class="material-symbols-outlined align-middle">edit</span> Edit Buku
  </h2>

  <?php if ($success) echo '<div class="alert alert-success">'.$success.'</div>'; ?>
  <?php if ($error) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="judul" class="form-label">Judul Buku</label>
          <input type="text" name="judul" id="judul" class="form-control" required value="<?= htmlspecialchars($buku['judul']) ?>">
        </div>

        <div class="mb-3">
          <label for="penulis" class="form-label">Pengarang</label>
          <input type="text" name="penulis" id="penulis" class="form-control" value="<?= htmlspecialchars($buku['penulis']) ?>">
        </div>

        <div class="mb-3">
          <label for="penerbit" class="form-label">Penerbit</label>
          <input type="text" name="penerbit" id="penerbit" class="form-control" value="<?= htmlspecialchars($buku['penerbit']) ?>">
        </div>

        <div class="mb-3">
          <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
          <input type="number" name="tahun_terbit" id="tahun_terbit" class="form-control" min="1900" max="<?= date('Y') ?>" value="<?= $buku['tahun_terbit'] ?>">
        </div>

        <div class="mb-3">
          <label for="kategori" class="form-label">Kategori</label>
          <select name="kategori" id="kategori" class="form-select">
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($kategori_list as $kat): ?>
              <option value="<?= $kat ?>" <?= $buku['kategori']==$kat?'selected':'' ?>><?= $kat ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3">
          <label for="stok" class="form-label">Stok</label>
          <input type="number" name="stok" id="stok" class="form-control" value="<?= $buku['stok'] ?>" min="0">
        </div>

        <div class="mb-3">
          <label for="gambar" class="form-label">Gambar Buku</label>
          <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
          <?php if (!empty($buku['img']) && file_exists($target_dir . $buku['img'])): ?>
            <img src="../../public/img/<?= htmlspecialchars($buku['img']) ?>" alt="Gambar Buku" width="100" class="mt-2">
          <?php endif; ?>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">
          <span class="material-symbols-outlined align-middle">save</span> Simpan
        </button>
        <a href="buku.php" class="btn btn-secondary">
          <span class="material-symbols-outlined align-middle">arrow_back</span> Kembali
        </a>
      </form>
    </div>
  </div>
</div>

<?php include '../templates/footer.php'; ?>
