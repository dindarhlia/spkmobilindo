<?php
/**
 * File: admin/index.php
 * Deskripsi: Dashboard utama Admin. Menampilkan statistik jumlah mobil dan kriteria.
 */

session_start();
require '../config/functions.php'; // Memanggil functions yang ada di folder config luar

// Cek Keamanan: Apakah user sudah login?
if (!isset($_SESSION['status_login'])) {
    header("Location: login.php");
    exit;
}

// Statistik Sederhana untuk Dashboard
// Menggunakan query() dari functions.php tapi kita hitung manual jumlahnya
$mobil = query("SELECT * FROM tb_alternatif");
$jml_mobil = count($mobil);

$kriteria = query("SELECT * FROM tb_kriteria");
$jml_kriteria = count($kriteria);

// Mencari Mobil dengan Harga Termahal (Contoh Statistik)
// Query manual khusus untuk dashboard
$result_mahal = mysqli_query($conn, "SELECT * FROM tb_alternatif ORDER BY harga_asli DESC LIMIT 1");
$mobil_mahal = mysqli_fetch_assoc($result_mahal);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SPK MOORA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        /* Styling khusus Sidebar Admin */
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color: #212529;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 16px;
            color: #b5b5b5;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover {
            color: #fff;
            background-color: #343a40;
        }
        .sidebar a.active {
            background-color: #0d6efd;
            color: white;
        }
        .main-content {
            margin-left: 250px; /* Sama dengan lebar sidebar */
            padding: 30px;
        }
        .card-stat {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        .card-stat:hover {
            transform: translateY(-5px);
        }
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="text-center mb-4">
            <h5 class="text-white fw-bold">PANEL ADMIN</h5>
            <small class="text-muted">Sadulur Mobilindo</small>
        </div>
        
        <a href="index.php" class="active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
        <a href="data_mobil.php"><i class="bi bi-car-front-fill me-2"></i> Data Mobil</a>
        <a href="data_kriteria.php"><i class="bi bi-list-task me-2"></i> Data Kriteria</a>
        <a href="hitung_spk.php"><i class="bi bi-calculator me-2"></i> Perhitungan SPK</a>
        <a href="laporan_cetak.php"><i class="bi bi-file-earmark-pdf me-2"></i> Laporan</a>
        
        <hr class="border-secondary mx-3">
        
        <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
    </div>

    <div class="main-content">
        
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold">Dashboard Overview</h2>
                <p class="text-muted">Selamat datang kembali, <strong><?= $_SESSION['nama_admin']; ?></strong>!</p>
            </div>
            <a href="../index.php" target="_blank" class="btn btn-outline-primary">
                <i class="bi bi-globe me-1"></i> Lihat Website Publik
            </a>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card card-stat bg-primary text-white h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="display-4 me-3"><i class="bi bi-car-front"></i></div>
                        <div>
                            <h5 class="card-title mb-0">Total Mobil</h5>
                            <h2 class="fw-bold mb-0"><?= $jml_mobil; ?> Unit</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-stat bg-success text-white h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="display-4 me-3"><i class="bi bi-sliders"></i></div>
                        <div>
                            <h5 class="card-title mb-0">Kriteria MOORA</h5>
                            <h2 class="fw-bold mb-0"><?= $jml_kriteria; ?> Kriteria</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-stat bg-warning text-dark h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-cash-coin me-2"></i>Harga Tertinggi</h5>
                        <?php if($mobil_mahal) : ?>
                            <p class="fw-bold mb-0"><?= $mobil_mahal['nama_mobil']; ?></p>
                            <p class="mb-0">Rp <?= number_format($mobil_mahal['harga_asli']); ?></p>
                        <?php else : ?>
                            <p class="mb-0">- Belum ada data -</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Distribusi Jenis Transmisi Mobil</h5>
            </div>
            <div class="card-body">
                <?php
                    // Hitung manual jumlah Matic vs Manual untuk grafik
                    $matic = 0; 
                    $manual = 0;
                    foreach($mobil as $m) {
                        if($m['transmisi'] == 'Matic') $matic++;
                        else $manual++;
                    }
                    // Hindari division by zero jika data kosong
                    $total = $jml_mobil > 0 ? $jml_mobil : 1; 
                    $persen_matic = ($matic / $total) * 100;
                    $persen_manual = ($manual / $total) * 100;
                ?>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Matic (<?= $matic; ?> Unit)</span>
                        <span><?= round($persen_matic); ?>%</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: <?= $persen_matic; ?>%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Manual (<?= $manual; ?> Unit)</span>
                        <span><?= round($persen_manual); ?>%</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $persen_manual; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

    </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>