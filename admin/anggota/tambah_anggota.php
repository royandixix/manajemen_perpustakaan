<?php
// Tampilkan error untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../config/connect.php';
session_start();

// Cek session admin
if (!isset($_SESSION['admin'])) {
    header("Location: ../../login.php");
    exit();
}

// Proses form jika submit
if(isset($_POST['submit'])) {
    $nama = $conn->real_escape_string($_POST['nama']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $alamat = $conn->real_escape_string($_POST['alamat']);
    $no_telp = $conn->real_escape_string($_POST['no_telp']);
    $tanggal_daftar = date('Y-m-d');

    $sql = "INSERT INTO anggota_222274 
        (nama_222274, email_222274, password_222274, alamat_222274, no_telp_222274, tanggal_daftar_222274)
        VALUES ('$nama', '$email', '$password', '$alamat', '$no_telp', '$tanggal_daftar')";

    if($conn->query($sql) === TRUE){
        $success = "Anggota berhasil ditambahkan!";
        // Kosongkan form setelah berhasil submit
        $_POST = array();
    } else {
        $error = "Error: " . $conn->error;
    }
}

?>

<?php include '../templates/header_sidebar.php'; ?>

<div id="main-content">
    <h2 class="fw-bold text-primary mb-4">
        <span class="material-symbols-outlined align-middle">person_add</span>
        Tambah Anggota
    </h2>

    <?php if(isset($success)) echo '<div class="alert alert-success">'.$success.'</div>'; ?>
    <?php if(isset($error)) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan nama anggota" value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Masukkan email anggota" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Masukkan password" required>
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" name="alamat" id="alamat" rows="3" placeholder="Masukkan alamat"><?= isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : '' ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="no_telp" class="form-label">No. Telp</label>
                    <input type="text" class="form-control" name="no_telp" id="no_telp" placeholder="Masukkan nomor telepon" value="<?= isset($_POST['no_telp']) ? htmlspecialchars($_POST['no_telp']) : '' ?>">
                </div>
                <button type="submit" name="submit" class="btn btn-primary">
                    <span class="material-symbols-outlined align-middle">save</span> Simpan
                </button>
                <a href="anggota.php" class="btn btn-secondary">
                    <span class="material-symbols-outlined align-middle">arrow_back</span> Kembali
                </a>
            </form>
        </div>
    </div>

    <footer class="text-center mt-4 mb-2 text-secondary">
        &copy; <?= date('Y') ?> PerpustakaanKu. Semua hak dilindungi.
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
