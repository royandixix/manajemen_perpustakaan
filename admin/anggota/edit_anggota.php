        <?php
        // tampilkan error untuk debugging
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        include '../../config/connect.php';
        session_start();

        // cek session admin
        if (!isset($_SESSION['admin'])) {
            header("Location: ../../login.php");
            exit();
        }

        // Ambil id anggota dari URL
        if (!isset($_GET['id'])) {
            header("Location: anggota.php");
            exit();
        }

        $id = (int) $_GET['id'];

        // Ambil data anggota dari database
        $sql = "SELECT * FROM anggota_222274 WHERE id_anggota_222274 = $id";
        $result = $conn->query($sql);

        if ($result->num_rows == 0) {
            die("Anggota tidak ditemukan.");
        }

        $anggota = $result->fetch_assoc();

        // Proses form jika submit
        if (isset($_POST['submit'])) {
            $nama = $conn->real_escape_string($_POST['nama']);
            $email = $conn->real_escape_string($_POST['email']);
            $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $anggota['password_222274'];
            $alamat = $conn->real_escape_string($_POST['alamat']);
            $no_telp = $conn->real_escape_string($_POST['no_telp']);

            $sql_update = "UPDATE anggota_222274 SET 
                nama_222274='$nama',
                email_222274='$email',
                password_222274='$password',
                alamat_222274='$alamat',
                no_telp_222274='$no_telp'
                WHERE id_anggota_222274=$id";

            if ($conn->query($sql_update) === TRUE) {
                $success = "Data anggota berhasil diupdate!";
                // refresh data setelah update
                $anggota = $conn->query($sql)->fetch_assoc();
            } else {
                $error = "Error: " . $conn->error;
            }
        }
        ?>      

        

        <?php include '../templates/header_sidebar.php'; ?>

        <div id="main-content">
            <h2 class="fw-bold text-primary mb-4">
                <span class="material-symbols-outlined align-middle">edit</span>
                Edit Anggota
            </h2>

            <?php if (isset($success)) echo '<div class="alert alert-success">' . $success . '</div>'; ?>
            <?php if (isset($error)) echo '<div class="alert alert-danger">' . $error . '</div>'; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" name="nama" id="nama" value="<?= htmlspecialchars($anggota['nama_222274']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($anggota['email_222274']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password <small>(kosongkan jika tidak diubah)</small></label>
                            <input type="password" class="form-control" name="password" id="password">
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" name="alamat" id="alamat" rows="3"><?= htmlspecialchars($anggota['alamat_222274']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="no_telp" class="form-label">No. Telp</label>
                            <input type="text" class="form-control" name="no_telp" id="no_telp" value="<?= htmlspecialchars($anggota['no_telp_222274']) ?>">
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">
                            <span class="material-symbols-outlined align-middle">save</span> Update
                        </button>
                        <a href="anggota.php" class="btn btn-secondary">
                            <span class="material-symbols-outlined align-middle">arrow_back</span> Kembali
                        </a>
                    </form>
                </div>
            </div>

            <footer class="text-center mt-4 mb-2 text-secondary">
                &copy; <?= date('Y') ?> PerpustakaanKu. Semua hak dilindungi.