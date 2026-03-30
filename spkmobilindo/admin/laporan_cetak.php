<?php
// File: admin/laporan_cetak.php
session_start();
require '../config/functions.php'; // Ambil data dan fungsi

// Cek Login (Security)
if (!isset($_SESSION['status_login'])) {
    header("Location: login.php");
    exit;
}

// Ambil Data Hasil Perhitungan MOORA
$hasil = hitungMOORA();
$ranking = $hasil['ranking'];

// Ambil Tanggal Hari ini untuk Tanda Tangan
$tanggal_cetak = date("d F Y");

// Array Bulan Indo untuk konversi tanggal (Opsional agar lebih formal)
$bulanIndo = [
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
];
$tanggal_cetak = strtr($tanggal_cetak, $bulanIndo);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan SPK - Sadulur Mobilindo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Styling Khusus untuk Cetak (Kertas A4) */
        body {
            font-family: "Times New Roman", Times, serif;
            padding: 40px;
        }
        .header-laporan {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px double black; /* Garis bawah kop surat */
            padding-bottom: 20px;
        }
        .judul-showroom {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .alamat-showroom {
            font-size: 14px;
        }
        .judul-dokumen {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 20px;
            text-decoration: underline;
        }
        .ttd-area {
            float: right;
            width: 250px;
            text-align: center;
            margin-top: 50px;
        }
        
        /* CSS agar tombol print hilang saat dicetak kertas */
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="no-print mb-4">
        <a href="index.php" class="btn btn-secondary">← Kembali ke Dashboard</a>
        <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak Laporan</button>
    </div>

    <div class="header-laporan">
        <div class="judul-showroom">SADULUR MOBILINDO</div>
        <div class="alamat-showroom">
            Jl. Raya Pamulang No. 12, Tangerang Selatan, Banten<br>
            Telp: +62 812-3456-7890 | Email: info@sadulurmobilindo.com
        </div>
    </div>

    <div class="judul-dokumen">
        LAPORAN HASIL REKOMENDASI MOBIL BEKAS (METODE MOORA)
    </div>

    <p>Berikut adalah hasil perhitungan sistem pendukung keputusan pemilihan mobil bekas terbaik berdasarkan kriteria yang telah ditetapkan (Harga, Tahun, Kondisi, dll):</p>

    <table class="table table-bordered border-dark">
        <thead class="table-light border-dark text-center">
            <tr>
                <th width="5%">Peringkat</th>
                <th width="35%">Nama Mobil</th>
                <th width="25%">Merek & Tahun</th>
                <th width="20%">Nilai Optimasi (Yi)</th>
                <th width="15%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($ranking)): ?>
                <tr>
                    <td colspan="5" class="text-center">Belum ada data mobil yang dihitung.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach($ranking as $r): ?>
                <tr>
                    <td class="text-center fw-bold"><?= $no; ?></td>
                    <td><?= $r['nama_mobil']; ?></td>
                    <td><?= $r['data']['merek'] ?? '-'; ?> (<?= $r['data']['tahun'] ?? '-'; ?>)</td>
                    <td class="text-center fw-bold"><?= number_format($r['nilai_akhir'], 6); ?></td>
                    <td class="text-center">
                        <?php if($no == 1): ?>
                            <span class="badge border border-dark text-dark">Sangat Direkomendasikan</span>
                        <?php elseif($no <= 3): ?>
                            <span class="badge border border-dark text-dark">Direkomendasikan</span>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php $no++; endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="ttd-area">
        <p>Tangerang Selatan, <?= $tanggal_cetak; ?></p>
        <br><br><br>
        <p><strong><u><?= $_SESSION['nama_admin']; ?></u></strong><br>
        Administrator</p>
    </div>

</body>
</html>