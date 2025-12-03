<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'config/connect.php';

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginNama = trim($_POST['login-nama'] ?? '');
    $password = $_POST['login-password'] ?? '';
    $role = $_POST['login-role'] ?? '';

    if ($loginNama === '' || $password === '' || $role === '') {
        $loginError = "Semua field harus diisi!";
    } else {
        if ($role === 'admin') {
            // Admin tetap pakai username
            $sql = "SELECT * FROM admin_222274 WHERE username_222274 = ? LIMIT 1";
        } else {
            // Anggota login pakai nama
            $sql = "SELECT * FROM anggota_222274 WHERE nama_222274 = ? LIMIT 1";
        }

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $loginNama);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password_222274'])) {
                if ($role === 'admin') {
                    $_SESSION['admin'] = [
                        'id_admin_222274' => $user['id_admin_222274'],
                        'nama' => $user['nama_lengkap_222274']
                    ];
                    header("Location: admin/index/index.php");
                } else {
                    $_SESSION['user'] = [
                        'id_anggota_222274' => $user['id_anggota_222274'],
                        'nama' => $user['nama_222274']
                    ];
                    header("Location: user/Katalog.php");
                }
                exit();
            } else {
                $loginError = "Password salah!";
            }
        } else {
            $loginError = $role === 'admin' ? "Username admin tidak ditemukan!" : "Nama anggota tidak ditemukan!";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Perpustakaan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body, html {
    height:100%; margin:0;
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1950&q=80') no-repeat center center fixed;
    background-size: cover;
}
.overlay { background-color: rgba(0,0,0,0.6); position:absolute; top:0; left:0; width:100%; height:100%; }
.container-login { position: relative; z-index: 2; height: 100%; display:flex; justify-content:center; align-items:center; }
.card-login { background: rgba(255,255,255,0.95); border-radius:15px; padding:30px; max-width:400px; width:100%; box-shadow:0 8px 20px rgba(0,0,0,0.3); }
.btn-primary { border-radius:50px; }
.form-control, .form-select { border-radius:10px; }
.register-link { color:#0d6efd; text-decoration:none; }
.register-link:hover { text-decoration:underline; }
</style>
</head>
<body>
<div class="overlay"></div>
<div class="container-login">
  <div class="card-login">
    <h3 class="text-center mb-4">Login Perpustakaan</h3>

    <?php if($loginError): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($loginError) ?></div>
    <?php endif; ?>

    <form action="" method="post">
      <div class="mb-3">
        <label for="login-nama" class="form-label">Email / Username</label>
        <input type="text" class="form-control" id="login-nama" name="login-nama" placeholder="Masukkan email atau username" required>
      </div>

      <div class="mb-3">
        <label for="login-password" class="form-label">Password</label>
        <input type="password" class="form-control" id="login-password" name="login-password" placeholder="Masukkan password" required>
      </div>

      <div class="mb-3">
        <label for="login-role" class="form-label">Masuk Sebagai</label>
        <select class="form-select" id="login-role" name="login-role" required>
          <option value="" selected>Pilih role</option>
          <option value="admin">Admin</option>
          <option value="user">Anggota</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary w-100">Login</button>
      <p class="text-center mt-3">Belum punya akun? <a href="register.php" class="register-link">Daftar</a></p>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
