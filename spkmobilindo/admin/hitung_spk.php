<?php
include 'header.php';

// JALANKAN PERHITUNGAN MOORA
// Fungsi ini mengembalikan array lengkap (Matriks X, R, dan Yi)
$hasil = hitungMOORA();

// Jika data kosong atau fungsi mengembalikan array kosong, tampilkan peringatan
if (empty($hasil) || !isset($hasil['ranking']) || count($hasil['ranking']) == 0) {
    echo "<div class='container mt-4'><div class='alert alert-warning shadow-sm'>
            <i class='bi bi-exclamation-triangle-fill me-2'></i> 
            Data Mobil atau Nilai Penilaian masih kosong. Silakan input data mobil dan lengkapi penilaian terlebih dahulu.
          </div></div>";
    include 'footer.php';
    exit;
}

// Urai variabel agar kodingan di bawah lebih bersih
$kriteria    = $hasil['kriteria'];
$alternatif  = $hasil['alternatif'];
$matriks_x   = $hasil['matriks_x'];
$pembagi     = $hasil['pembagi'];
$normalisasi = $hasil['normalisasi'];
$ranking     = $hasil['ranking'];
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-calculator me-2 text-primary"></i> Perhitungan Metode MOORA</h3>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm d-print-none">
            <i class="bi bi-printer me-1"></i> Cetak Laporan
        </button>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-table me-2"></i>Tahap 1: Matriks Keputusan (X)</h6>
        </div>
        <div class="card-body table-responsive">
            <p class="small text-muted">Mengonversi data alternatif ke dalam nilai angka berdasarkan kriteria yang ditentukan.</p>
            <table class="table table-bordered table-sm text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" class="align-middle">Alternatif (Mobil)</th>
                        <th colspan="<?= count($kriteria); ?>">Kriteria</th>
                    </tr>
                    <tr>
                        <?php foreach($kriteria as $k): ?>
                            <th title="<?= $k['nama_kriteria']; ?>">
                                <?= $k['kode_kriteria']; ?><br>
                                <span class="badge bg-secondary" style="font-size: 0.7rem;"><?= strtoupper($k['atribut']); ?></span>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($alternatif as $alt): ?>
                    <tr>
                        <td class="text-start ps-3 fw-medium"><?= $alt['nama_mobil']; ?></td>
                        <?php foreach($kriteria as $k): ?>
                            <td>
                                <?php 
                                $nilai = $matriks_x[$alt['id_mobil']][$k['id_kriteria']] ?? 0;
                                echo ($k['id_kriteria'] == 1 || $k['id_kriteria'] == 5) ? number_format($nilai, 0, ',', '.') : $nilai; 
                                ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    
                    <tr class="table-info fw-bold">
                        <td class="text-start ps-3">Nilai Pembagi (Akar)</td>
                        <?php foreach($kriteria as $k): ?>
                            <td><?= number_format($pembagi[$k['id_kriteria']], 4); ?></td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-grid-3x3 me-2"></i>Tahap 2: Matriks Ternormalisasi (R)</h6>
        </div>
        <div class="card-body table-responsive">
            <p class="small text-muted">Hasil bagi antara Matriks Keputusan dengan Nilai Pembagi.</p>
            <table class="table table-bordered table-sm text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Alternatif</th>
                        <?php foreach($kriteria as $k): ?>
                            <th><?= $k['kode_kriteria']; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($alternatif as $alt): ?>
                    <tr>
                        <td class="text-start ps-3 fw-medium"><?= $alt['nama_mobil']; ?></td>
                        <?php foreach($kriteria as $k): ?>
                            <td>
                                <?php 
                                $norm = $normalisasi[$alt['id_mobil']][$k['id_kriteria']] ?? 0;
                                echo number_format($norm, 4); 
                                ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-primary text-white py-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-trophy me-2"></i>Tahap 3: Hasil Optimasi & Perangkingan</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
                <div class="fs-1 me-3">🏆</div>
                <div>
                    <h5 class="alert-heading mb-1">Rekomendasi Terbaik</h5>
                    <p class="mb-0">Berdasarkan perhitungan MOORA, mobil <strong><?= $ranking[0]['nama_mobil']; ?></strong> adalah pilihan terbaik dengan nilai optimasi sebesar <strong><?= number_format($ranking[0]['nilai_akhir'], 6); ?></strong>.</p>
                </div>
            </div>

            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width: 100px;">Peringkat</th>
                        <th>Mobil</th>
                        <th>Harga Mobil</th>
                        <th class="text-end pe-4">Nilai Akhir (Yi)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach($ranking as $r): ?>
                    <tr <?= ($no == 1) ? 'class="table-success"' : ''; ?>>
                        <td class="text-center">
                            <?php if($no == 1): ?>
                                <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> 1</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?= $no; ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="../foto_mobil/<?= !empty($r['foto']) ? $r['foto'] : 'default.jpg'; ?>" 
                                     alt="Mobil" class="rounded shadow-sm me-3" style="width: 60px; height: 40px; object-fit: cover;">
                                <span class="fw-bold"><?= $r['nama_mobil']; ?></span>
                            </div>
                        </td>
                        <td>Rp <?= number_format($r['harga'], 0, ',', '.'); ?></td>
                        <td class="text-end pe-4 fw-bold text-primary">
                            <?= number_format($r['nilai_akhir'], 6); ?>
                        </td>
                    </tr>
                    <?php $no++; endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>