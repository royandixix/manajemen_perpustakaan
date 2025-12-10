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

// === PROSES SUBMIT ===
if (isset($_POST['submit'])) {
    $judul = $conn->real_escape_string($_POST['judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $penulis = $conn->real_escape_string($_POST['penulis']);
    $penerbit = $conn->real_escape_string($_POST['penerbit']);
    $tahun = intval($_POST['tahun_terbit']);
    $kategori_pilih = $conn->real_escape_string($_POST['kategori']);
    $kategori_baru = $conn->real_escape_string($_POST['kategori_baru']);
    $stok = intval($_POST['stok']);

    // === FIX DUPLIKAT KATEGORI ===
    if (!empty($kategori_baru)) {
        $cek = $conn->query("SELECT nama_kategori_222274 FROM kategori_222274 WHERE nama_kategori_222274 = '$kategori_baru'");
        if ($cek->num_rows == 0) {
            $conn->query("INSERT INTO kategori_222274 (nama_kategori_222274) VALUES ('$kategori_baru')");
        }
        $kategori_final = $kategori_baru;
    } else {
        $kategori_final = $kategori_pilih;
    }

    // ========= Upload Gambar =========
    $namaFile = null;
    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "../../uploads/sampul/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];

        if (!in_array($ext, $allowed)) {
            $error = "Format gambar tidak valid! (Hanya JPG, PNG, GIF, WEBP)";
        } else {
            $namaFile = "buku_" . time() . "." . $ext;
            $target_file = $target_dir . $namaFile;
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                $error = "Gagal upload gambar!";
            }
        }
    }

    // ========= INSERT DATA BUKU =========
    if (!isset($error)) {
        $sql = "INSERT INTO buku_222274 
                (judul_222274, deskripsi_222274, penulis_222274, penerbit_222274, tahun_terbit_222274, kategori_222274, stok_222274, img_222274)
                VALUES ('$judul', '$deskripsi', '$penulis', '$penerbit', '$tahun', '$kategori_final', '$stok', '$namaFile')";
        if ($conn->query($sql)) {
            $_SESSION['flash'] = "Buku berhasil ditambahkan!";
            header("Location: buku.php");
            exit();
        } else {
            $error = "Gagal menambahkan buku: " . $conn->error;
        }
    }
}

// Ambil kategori
$kategori_list = [];
$res = $conn->query("SELECT nama_kategori_222274 FROM kategori_222274 ORDER BY nama_kategori_222274 ASC");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $kategori_list[] = $row['nama_kategori_222274'];
    }
} else {
    $kategori_list = ['Pemrograman', 'Matematika', 'Web', 'Jaringan'];
}
?>

<?php include '../templates/header_sidebar.php'; ?>

<div id="main-content">
<h2 class="fw-bold mb-4 mt-5">
    <span class="material-symbols-outlined align-middle me-1 text-primary">add_circle</span> Tambah Buku
</h2>

<div class="card shadow-sm p-4">
    <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>

        <div class="mb-3">
            <label class="form-label">Judul Buku</label>
            <input type="text" name="judul" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Deskripsi Buku</label>
            <textarea name="deskripsi" class="form-control" rows="4" placeholder="Isi deskripsi buku"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Penulis</label>
            <input type="text" name="penulis" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Penerbit</label>
            <input type="text" name="penerbit" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Tahun Terbit</label>
            <input type="number" name="tahun_terbit" class="form-control" min="1900" max="<?= date('Y') ?>">
        </div>

        <!-- DROPDOWN KATEGORI -->
        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="kategori" class="form-select">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategori_list as $kat): ?>
                    <option value="<?= htmlspecialchars($kat) ?>"><?= htmlspecialchars($kat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- INPUT KATEGORI BARU -->
        <div class="mb-3">
            <label class="form-label">Tambah Kategori Baru (Opsional)</label>
            <input type="text" name="kategori_baru" class="form-control" placeholder="Contoh: Sains, Novel, Database">
        </div>

        <div class="mb-3">
            <label class="form-label">Stok</label>
            <input type="number" name="stok" class="form-control" min="0" value="0">
        </div>

        <div class="mb-3">
            <label class="form-label">Upload Sampul Buku</label>
            <input type="file" name="gambar" class="form-control" accept="image/*">
        </div>

        <button type="submit" name="submit" class="btn btn-primary me-2">
            <span class="material-symbols-outlined align-middle me-1">save</span> Simpan
        </button>

        <a href="buku.php" class="btn btn-secondary">
            <span class="material-symbols-outlined align-middle me-1">arrow_back</span> Kembali
        </a>

    </form>
</div>

<footer class="text-center mt-4">
    © <?= date('Y') ?> <strong>PerpustakaanKu</strong>
</footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
<?php if (isset($error)): ?>
Swal.fire({ icon:'error', title:'Gagal', text:'<?= addslashes($error) ?>' });
<?php endif; ?>
</script>

<?php include '../templates/footer.php'; ?>
