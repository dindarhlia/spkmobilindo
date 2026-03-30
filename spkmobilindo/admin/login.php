<?php
/**
 * File: admin/login.php
 * Deskripsi: Halaman autentikasi untuk staf/admin masuk ke sistem
 */

session_start();
require '../config/koneksi.php'; // Mundur satu folder untuk akses config

// Cek apakah user sudah login? Jika ya, lempar langsung ke dashboard
if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == true) {
    header("Location: index.php");
    exit;
}

// Proses saat tombol Login ditekan
if (isset($_POST['login'])) {
    // Ambil inputan user & amankan dari SQL Injection sederhana
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Enkripsi password inputan dengan MD5 (Sesuai data dummy database)
    // Catatan Skripsi: MD5 sebenarnya sudah jadul, tapi untuk skripsi S1 seringkali masih diterima.
    // Jika dosen minta yang lebih aman, nanti bisa kita ganti pakai password_hash().
    $password_enc = md5($password);

    // Cek kredensial di database
    $query = "SELECT * FROM tb_admin WHERE username = '$username' AND password = '$password_enc'";
    $result = mysqli_query($conn, $query);

    // Hitung jumlah data yang ditemukan
    if (mysqli_num_rows($result) === 1) {
        // Jika ditemukan 1 akun yang cocok
        $row = mysqli_fetch_assoc($result);
        
        // Set Session Server
        $_SESSION['status_login'] = true;
        $_SESSION['id_admin'] = $row['id_admin'];
        $_SESSION['nama_admin'] = $row['nama_lengkap'];
        
        // Redirect ke Dashboard
        echo "<script>
                alert('Login Berhasil! Selamat Datang, " . $row['nama_lengkap'] . "');
                document.location.href = 'index.php';
              </script>";
    } else {
        // Jika gagal
        $error = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sadulur Mobilindo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .login-header {
            background: #0d6efd;
            color: white;
            padding: 30px 20px;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="card login-card">
        <div class="login-header">
            <h4 class="mb-0 fw-bold">Admin Area</h4>
            <p class="small mb-0 opacity-75">Sadulur Mobilindo</p>
        </div>
        <div class="card-body p-4">
            
            <?php if(isset($error)) : ?>
                <div class="alert alert-danger text-center mb-3" role="alert">
                    Username atau Password Salah!
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control form-control-lg" id="username" name="username" placeholder="Masukkan username" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Masukkan password" required>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" name="login" class="btn btn-primary btn-lg">Masuk Sistem</button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <a href="../index.php" class="text-decoration-none text-secondary small">← Kembali ke Halaman Depan</a>
            </div>
        </div>
    </div>

</body>
</html>