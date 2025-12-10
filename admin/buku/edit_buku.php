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

$id = intval($_GET['id']);
$sql = "SELECT * FROM buku_222274 WHERE id_buku_222274 = $id";
$result = $conn->query($sql);
if($result->num_rows == 0){
    die("Buku tidak ditemukan.");
}
$buku = $result->fetch_assoc();

// Ambil kategori untuk dropdown
$kategori_list = [];
$res = $conn->query("SELECT nama_kategori_222274 FROM kategori_222274 ORDER BY nama_kategori_222274 ASC");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $kategori_list[] = $row['nama_kategori_222274'];
    }
} else {
    $kategori_list = ['Pemrograman', 'Matematika', 'Web', 'Jaringan'];
}

if(isset($_POST['submit'])){
    $judul = $conn->real_escape_string($_POST['judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $penulis = $conn->real_escape_string($_POST['penulis']);
    $penerbit = $conn->real_escape_string($_POST['penerbit']);
    $tahun = intval($_POST['tahun_terbit']);
    $kategori_pilih = $conn->real_escape_string($_POST['kategori']);
    $kategori_baru = $conn->real_escape_string($_POST['kategori_baru']);
    $stok = intval($_POST['stok']);

    // Jika kategori baru diisi → simpan dan jadikan kategori buku
    if(!empty($kategori_baru)){
        $conn->query("INSERT IGNORE INTO kategori_222274 (nama_kategori_222274) VALUES ('$kategori_baru')");
        $kategori_final = $kategori_baru;
    } else {
        $kategori_final = $kategori_pilih;
    }

    // ========= Upload Gambar (opsional) =========
    $namaFile = $buku['img_222274']; // default: tetap gambar lama

    if(!empty($_FILES['gambar']['name'])){
        $target_dir = "../../uploads/sampul/";
        if(!is_dir($target_dir)){
            mkdir($target_dir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];

        if(!in_array($ext, $allowed)){
            $error = "Format gambar tidak valid! (Hanya JPG, PNG, GIF, WEBP)";
        } else {
            $namaFile = "buku_" . time() . "." . $ext;
            $target_file = $target_dir . $namaFile;

            if(!move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)){
                $error = "Gagal upload gambar!";
            }
        }
    }

    // ========= UPDATE DATA =========
    if(!isset($error)){
        $sql_update = "UPDATE buku_222274 SET 
            judul_222274='$judul',
            deskripsi_222274='$deskripsi',
            penulis_222274='$penulis',
            penerbit_222274='$penerbit',
            tahun_terbit_222274=$tahun,
            kategori_222274='$kategori_final',
            stok_222274=$stok,
            img_222274='$namaFile'
            WHERE id_buku_222274=$id";

        if($conn->query($sql_update) === TRUE){
            header("Location: buku.php");
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<?php include '../templates/header_sidebar.php'; ?>

<div id="main-content">
<h2 class="fw-bold mb-4 mt-5">
    <span class="material-symbols-outlined align-middle me-1 text-primary">edit</span> Edit Buku
</h2>

<?php if(isset($error)): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<div class="card shadow-sm p-4">
<form action="" method="POST" enctype="multipart/form-data">

    <div class="mb-3">
        <label class="form-label">Judul Buku</label>
        <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($buku['judul_222274']) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Deskripsi Buku</label>
        <textarea name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($buku['deskripsi_222274'] ?? '') ?></textarea>

    </div>

    <div class="mb-3">
        <label class="form-label">Penulis</label>
        <input type="text" name="penulis" class="form-control" value="<?= htmlspecialchars($buku['penulis_222274']) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Penerbit</label>
        <input type="text" name="penerbit" class="form-control" value="<?= htmlspecialchars($buku['penerbit_222274']) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Tahun Terbit</label>
        <input type="number" name="tahun_terbit" class="form-control" min="1900" max="<?= date('Y') ?>" value="<?= $buku['tahun_terbit_222274'] ?>">
    </div>

    <!-- Dropdown kategori -->
    <div class="mb-3">
        <label class="form-label">Kategori</label>
        <select name="kategori" class="form-select">
            <option value="">-- Pilih Kategori --</option>
            <?php foreach($kategori_list as $kat): ?>
                <option value="<?= htmlspecialchars($kat) ?>" <?= $buku['kategori_222274']==$kat ? 'selected' : '' ?>><?= htmlspecialchars($kat) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Input kategori baru -->
    <div class="mb-3">
        <label class="form-label">Tambah Kategori Baru (Opsional)</label>
        <input type="text" name="kategori_baru" class="form-control" placeholder="Contoh: Sains, Novel, Database">
    </div>

    <div class="mb-3">
        <label class="form-label">Stok</label>
        <input type="number" name="stok" class="form-control" min="0" value="<?= $buku['stok_222274'] ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Upload Gambar Sampul (Opsional)</label>
        <input type="file" name="gambar" class="form-control" accept="image/*">
        <?php if(!empty($buku['img_222274'])): ?>
            <img src="../../uploads/sampul/<?= htmlspecialchars($buku['img_222274']) ?>" alt="" width="60" height="80" class="mt-2">
        <?php endif; ?>
    </div>

    <button type="submit" name="submit" class="btn btn-primary me-2">Update</button>
    <a href="buku.php" class="btn btn-secondary">Kembali</a>
</form>
</div>
</div>

<?php include '../templates/footer.php'; ?>
