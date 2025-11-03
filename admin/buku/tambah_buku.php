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

$kategori_list = ['Pemrograman', 'Matematika', 'Web', 'Jaringan'];
$success = $error = '';

// Folder upload
$target_dir = realpath(__DIR__ . '/../../public/img/buku') . '/';
if (!is_dir($target_dir)) mkdir($target_dir, 0775, true);

if (isset($_POST['submit'])) {
    $judul = $conn->real_escape_string($_POST['judul']);
    $penulis = $conn->real_escape_string($_POST['penulis']);
    $penerbit = $conn->real_escape_string($_POST['penerbit']);
    $tahun = intval($_POST['tahun_terbit']);
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $stok = intval($_POST['stok']);
    $img_name = null;

    // Upload gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed_ext = ['jpg','jpeg','png','gif','webp'];
        $file_tmp  = $_FILES['gambar']['tmp_name'];
        $file_ext  = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            $new_name = uniqid('buku_') . '.' . $file_ext;
            $dest = $target_dir . $new_name;

            if (is_writable($target_dir)) {
                if (move_uploaded_file($file_tmp, $dest)) {
                    $img_name = 'buku/' . $new_name; // simpan path relatif
                } else {
                    $error = "Gagal memindahkan file, periksa permission folder.";
                }
            } else {
                $error = "Folder tidak bisa ditulis.";
            }
        } else {
            $error = "Format gambar tidak didukung!";
        }
    }

    if (!$error) {
        $sql = "INSERT INTO buku_222274 
                (judul, penulis, penerbit, tahun_terbit, kategori, stok, img)
                VALUES 
                ('$judul','$penulis','$penerbit',$tahun,'$kategori',$stok,'$img_name')";
        if ($conn->query($sql)) {
            $success = "Buku berhasil ditambahkan!";
        } else {
            $error = "Error: ".$conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Buku</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Tambah Buku</h2>

    <?php if ($success) echo '<div class="alert alert-success">'.$success.'</div>'; ?>
    <?php if ($error) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Judul Buku</label>
            <input type="text" name="judul" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Pengarang</label>
            <input type="text" name="penulis" class="form-control">
        </div>

        <div class="mb-3">
            <label>Penerbit</label>
            <input type="text" name="penerbit" class="form-control">
        </div>

        <div class="mb-3">
            <label>Tahun Terbit</label>
            <input type="number" name="tahun_terbit" class="form-control" min="1900" max="<?= date('Y') ?>">
        </div>

        <div class="mb-3">
            <label>Kategori</label>
            <select name="kategori" class="form-select">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategori_list as $kat): ?>
                    <option value="<?= $kat ?>"><?= $kat ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Stok</label>
            <input type="number" name="stok" class="form-control" min="0">
        </div>

        <div class="mb-3">
            <label>Gambar Buku</label>
            <input type="file" name="gambar" class="form-control" accept="image/*">
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
        <a href="buku.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
