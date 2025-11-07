<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config/connect.php';
session_start();

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['register-nama'];
    $username = $_POST['register-username'];
    $password = password_hash($_POST['register-password'], PASSWORD_DEFAULT);
    $role = $_POST['register-role'];

    if ($role === 'user') {
        $alamat = $_POST['register-alamat'];
        $no_telp = $_POST['register-no_telp'];

        // 🧩 Gunakan prepared statement agar lebih aman
        $stmt = $conn->prepare("INSERT INTO anggota_222274 
            (nama_222274, email_222274, password_222274, alamat_222274, no_telp_222274, tanggal_daftar_222274)
            VALUES (?, ?, ?, ?, ?, CURDATE())");
        $stmt->bind_param("sssss", $nama, $username, $password, $alamat, $no_telp);
    } else { 
        // 🧩 Tabel admin_222274, bukan admin
        $stmt = $conn->prepare("INSERT INTO admin_222274 
            (username_222274, password_222274, nama_lengkap_222274)
            VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $nama);
    }

    if ($stmt->execute()) {
        $successMsg = 'Registrasi berhasil! Silakan <a href="login.php" class="alert-link">login</a>.';
    } else {
        $errorMsg = "Registrasi gagal: " . $stmt->error;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registrasi Perpustakaan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body, html {
  height: 100%; margin:0;
  font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1950&q=80')
    no-repeat center center fixed; background-size: cover;
}
.overlay { background-color: rgba(0,0,0,0.6); position:absolute; top:0; left:0; width:100%; height:100%; }
.container-register { position: relative; z-index:2; height:100%; display:flex; justify-content:center; align-items:center; }
.card-register { background: rgba(255,255,255,0.95); border-radius:15px; padding:30px; max-width:450px; width:100%; box-shadow:0 8px 20px rgba(0,0,0,0.3);}
.btn-success { border-radius:50px; }
.form-control, .form-select, textarea { border-radius:10px; }
.login-link { color: #0d6efd; text-decoration:none; }
.login-link:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="overlay"></div>
<div class="container-register">
  <div class="card-register">
    <h3 class="text-center mb-4">Registrasi Perpustakaan</h3>

    <?php if ($successMsg): ?>
      <div class="alert alert-success"><?= $successMsg ?></div>
    <?php elseif ($errorMsg): ?>
      <div class="alert alert-danger"><?= $errorMsg ?></div>
    <?php endif; ?>

    <form action="" method="post">
      <div class="mb-3">
        <label for="register-nama" class="form-label">Nama Lengkap</label>
        <input type="text" class="form-control" id="register-nama" name="register-nama" placeholder="Masukkan nama lengkap" required>
      </div>

      <div class="mb-3">
        <label for="register-username" class="form-label">Email</label>
        <input type="email" class="form-control" id="register-username" name="register-username" placeholder="Masukkan email" required>
      </div>

      <div class="mb-3">
        <label for="register-password" class="form-label">Password</label>
        <input type="password" class="form-control" id="register-password" name="register-password" placeholder="Masukkan password" required>
      </div>

      <div class="mb-3">
        <label for="register-role" class="form-label">Role</label>
        <select class="form-select" id="register-role" name="register-role" required onchange="toggleUserFields(this.value)">
          <option value="" selected>Pilih role</option>
          <option value="admin">Admin</option>
          <option value="user">User</option>
        </select>
      </div>

      <div id="user-fields" style="display:none;">
        <div class="mb-3">
          <label for="register-alamat" class="form-label">Alamat</label>
          <textarea class="form-control" id="register-alamat" name="register-alamat" placeholder="Masukkan alamat"></textarea>
        </div>
        <div class="mb-3">
          <label for="register-no_telp" class="form-label">No. Telepon</label>
          <input type="text" class="form-control" id="register-no_telp" name="register-no_telp" placeholder="Masukkan nomor telepon">
        </div>
      </div>

      <button type="submit" class="btn btn-success w-100">Daftar</button>
      <p class="text-center mt-3 mb-0">Sudah punya akun? <a href="login.php" class="login-link">Login</a></p>
    </form>
  </div>
</div>

<script>
function toggleUserFields(role){
  document.getElementById('user-fields').style.display = (role === 'user') ? 'block' : 'none';
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
