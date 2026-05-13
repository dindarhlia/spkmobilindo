<?php 
// File: admin/laporan_cetak.php
session_start();
require '../config/functions.php'; 

// Set Zona Waktu ke WIB
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['status_login'])) {
    header("Location: login.php");
    exit;
}

$merek_filter = isset($_GET['merek']) ? $_GET['merek'] : '';

// Ambil Data Hasil Perhitungan MOORA
$hasil = hitungMOORA();
$ranking = $hasil['ranking'];

if ($merek_filter != '') {
    $ranking = array_filter($ranking, function($item) use ($merek_filter) {
        return strtolower($item['data']['merek'] ?? '') == strtolower($merek_filter);
    });
}

// Konversi Tanggal ke Bahasa Indonesia
$bulanIndo = [
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
];
$tanggal_cetak = strtr(date("d F Y"), $bulanIndo);
$waktu_cetak = date("H:i");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan SPK - Sadulur Mobilindo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            padding: 40px;
            background-color: white;
        }
        .header-laporan {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px double black; 
            padding-bottom: 20px;
        }
        .judul-showroom {
            font-size: 26px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .alamat-showroom {
            font-size: 14px;
        }
        .judul-dokumen {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin-top: 20px;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        .table thead th {
            vertical-align: middle;
            text-align: center;
            border-color: black !important;
        }
        .table tbody td {
            vertical-align: middle;
            border-color: black !important;
        }
        .ttd-area {
            float: right;
            width: 250px;
            text-align: center;
            margin-top: 50px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print mb-4 d-flex justify-content-between">
        <a href="index.php" class="btn btn-secondary">← Kembali</a>
        <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak Laporan</button>
    </div>

    <div class="header-laporan">
        <div class="judul-showroom">SADULUR MOBILINDO</div>
        <div class="alamat-showroom">
            Jl. Raya KSU No. 52, Tirtajaya, Kec. Sukmajaya, Kota Depok, Jawa Barat<br>
            Telp: +62 813 1409 0617 | Email: info@sadulurmobilindo.com
        </div>
    </div>

    <div class="judul-dokumen">
        LAPORAN HASIL REKOMENDASI MOBIL BEKAS (METODE MOORA)
    </div>
    
    <div class="mb-4">
        <table class="table table-sm table-borderless w-50" style="font-size: 14px;">
            <tr>
                <td width="30%">Filter Merek</td>
                <td>: <?= ($merek_filter == '') ? 'Semua Merek' : strtoupper($merek_filter); ?></td>
            </tr>
            <tr>
                <td>Waktu Cetak</td>
                <td>: <?= $tanggal_cetak; ?> | <?= $waktu_cetak; ?> WIB</td>
            </tr>
        </table>
    </div>

    <table class="table table-bordered border-dark">
        <thead class="text-center">
            <tr>
                <th width="50">No</th>
                <th>Nama Mobil</th>
                <th>Merek</th>
                <th>Tahun</th>
                <th>Harga</th>
                <th>Nilai Optimasi (Yi)</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($ranking)): ?>
                <tr><td colspan="6" class="text-center py-4">Data tidak ditemukan.</td></tr>
            <?php else: ?>
                <?php $no = 1; foreach ($ranking as $r) : ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= $r['nama_mobil']; ?></td>
                    <td class="text-center"><?= $r['data']['merek'] ?? '-'; ?></td> 
                    <td class="text-center"><?= $r['data']['tahun'] ?? '-'; ?></td>
                    <td>Rp <?= number_format($r['harga'], 0, ',', '.'); ?></td>
                    <td class="text-center fw-bold"><?= number_format($r['nilai_akhir'], 6); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="ttd-area">
        <p>Depok, <?= $tanggal_cetak; ?></p>
        <br><br><br><br>
        <p><strong><u><?= $_SESSION['nama_admin']; ?></u></strong></p>
    </div>

</body>
</html>