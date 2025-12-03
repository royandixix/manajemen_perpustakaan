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
    $penulis = $conn->real_escape_string($_POST['penulis']);
    $penerbit = $conn->real_escape_string($_POST['penerbit']);
    $tahun = intval($_POST['tahun_terbit']);
    $kategori_pilih = $conn->real_escape_string($_POST['kategori']);
    $kategori_baru = $conn->real_escape_string($_POST['kategori_baru']);
    $stok = intval($_POST['stok']);

    // === FIX DUPLIKAT KATEGORI ===
    if (!empty($kategori_baru)) {

        // cek apakah kategori baru sudah ada
        $cek = $conn->query("SELECT nama_kategori_222274 
                             FROM kategori_222274 
                             WHERE nama_kategori_222274 = '$kategori_baru'");

        if ($cek->num_rows == 0) {
            // kalau belum ada → insert
            $conn->query("INSERT INTO kategori_222274 (nama_kategori_222274) VALUES ('$kategori_baru')");
        }

        // pakai kategori baru
        $kategori_final = $kategori_baru;

    } else {
        // memakai kategori yang dipilih user di dropdown
        $kategori_final = $kategori_pilih;
    }

    // ========= Upload Gambar =========
    $namaFile = null;

    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "../../uploads/sampul/";

        // pastikan folder ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

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
                (judul_222274, penulis_222274, penerbit_222274, tahun_terbit_222274, kategori_222274, stok_222274, img_222274)
                VALUES ('$judul', '$penulis', '$penerbit', '$tahun', '$kategori_final', '$stok', '$namaFile')";

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

<style>
/* TIDAK DIUBAH — SAME UI */
body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #eef2f3, #dfe9f3); min-height:100vh; color:#333; }
#main-content { padding:2rem; animation: fadeIn 0.6s ease-in-out; }
@keyframes fadeIn { from{opacity:0;transform:translateY(10px);} to{opacity:1;transform:translateY(0);} }
h2 { font-weight:700; background:linear-gradient(90deg,#4facfe,#00f2fe); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
.card { background: rgba(255,255,255,0.85); backdrop-filter: blur(12px); border-radius:20px; border:1px solid rgba(255,255,255,0.5); box-shadow:0 8px 25px rgba(0,0,0,0.08); transition:all 0.3s ease; }
.card:hover { transform:translateY(-4px); }
.form-control, .form-select { border-radius:15px; border:1px solid #ccc; transition: all 0.25s ease; }
.form-control:focus, .form-select:focus { box-shadow:0 0 0 0.2rem rgba(0,123,255,0.25); transform:scale(1.01); }
.btn { border-radius:25px; transition:all 0.25s ease; }
.btn-primary { background: linear-gradient(135deg,#4facfe,#00f2fe); border:none; }
.btn-primary:hover { transform:translateY(-2px); box-shadow:0 8px 15px rgba(79,172,254,0.4); }
.btn-secondary:hover { transform:scale(1.05); }
footer { color:#6c757d; font-size:0.9rem; }
</style>

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
</body>
</html>
