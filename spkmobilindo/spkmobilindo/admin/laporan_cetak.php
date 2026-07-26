<?php
/**
 * File: admin/laporan_cetak.php
 */
session_start();
if (!isset($_SESSION['status_login'])) {
    header("Location: login.php");
    exit;
}

require '../config/functions.php';

date_default_timezone_set('Asia/Jakarta');

// Ambil data matriks lengkap dari core function hitungMOORA()
$moora       = hitungMOORA();
$kriteria    = $moora['kriteria'];
$alternatif  = $moora['alternatif'];
$matriks_x   = $moora['matriks_x'];
$pembagi     = $moora['pembagi'];
$normalisasi = $moora['normalisasi'];
$ranking     = $moora['ranking'];

$bulanIndo = [
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
];
$tanggal_cetak = strtr(date("d F Y"), $bulanIndo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Perhitungan Lengkap SPK MOORA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #fff;
            color: #000;
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            padding: 15px 25px;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop-surat h2 {
            font-size: 22px;
            font-weight: bold;
            margin: 0;
        }
        .judul-laporan {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            margin-top: 15px;
            margin-bottom: 25px;
            text-decoration: underline;
        }
        h6 {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        /* CSS Pengunci Ukuran Tabel agar kriteria C1-C7 muat rata di kertas A4 portrait */
        .table-cetak {
            width: 100% !important;
            border-color: #000 !important;
            margin-bottom: 15px;
            table-layout: fixed;
            word-wrap: break-word;
        }
        .table-cetak th, .table-cetak td {
            border: 1px solid #000 !important;
            padding: 4px !important;
            text-align: center;
            vertical-align: middle;
            font-size: 8.5pt !important;
        }
        .table-cetak th {
            background-color: #f2f2f2 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .text-left {
            text-align: left !important;
            padding-left: 5px !important;
        }
        .ttd-container {
            margin-top: 25px;
            float: right;
            text-align: center;
            width: 220px;
        }
        .space-ttd {
            height: 60px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; }
            @page {
                size: A4 portrait;
                margin: 10mm 8mm 10mm 8mm;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print d-flex justify-content-between my-3">
        <a href="hitung_spk.php" class="btn btn-secondary btn-sm">&larr; Kembali</a>
        <button onclick="window.print();" class="btn btn-primary btn-sm">🖨️ Cetak Laporan</button>
    </div>

    <div class="kop-surat">
        <h2>SADULUR MOBILINDO</h2>
        <div class="small text-secondary">Jl. Raya KSU No. 52, Tirtajaya, Kec. Sukmajaya, Kota Depok, Jawa Barat</div>
    </div>

    <div class="judul-laporan">
        LAPORAN PERHITUNGAN METODE MOORA DAN PERANGKINGAN
    </div>

    <h6>Hasil Akhir Perankingan</h6>
    <table class="table table-cetak" style="table-layout: auto;">
        <thead>
            <tr>
                <th style="width: 10%;">Peringkat</th>
                <th class="text-left">Nama Mobil</th>
                <th>Harga Unit</th>
                <th>Nilai Akhir (Yi)</th>
            </tr>
        </thead>
        <tbody>
            <?php $rank = 1; foreach ($ranking as $r) : ?>
            <tr <?= ($rank == 1) ? 'style="font-weight: bold; background-color: #f5fdf9;"' : ''; ?>>
                <td><strong>#<?= $rank++; ?></strong></td>
                <td class="text-left"><?= $r['nama_mobil']; ?></td>
                <td>Rp <?= number_format($r['harga'], 0, ',', '.'); ?></td>
                <td class="text-primary fw-bold"><?= number_format($r['nilai_akhir'], 6); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="ttd-container">
        <p>Depok, <?= $tanggal_cetak; ?></p>
        <p>Mengetahui,</p>
        <p style="font-weight: bold; margin-top: 5px;">Owner Sadulur Mobilindo</p>
        <div class="space-ttd"></div>
        <p style="text-decoration: underline; font-weight: bold;">.......................................</p>
    </div>

</body>
</html>