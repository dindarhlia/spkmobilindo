<?php
include 'header.php';

// JALANKAN PERHITUNGAN MOORA
// Fungsi ini mengembalikan array lengkap (Matriks X, R, dan Yi)
$hasil = hitungMOORA();

// Jika data kosong, tampilkan peringatan
if (empty($hasil)) {
    echo "<div class='alert alert-warning'>Data Mobil atau Nilai Penilaian masih kosong. Silakan input data mobil terlebih dahulu.</div>";
    include 'footer.php';
    exit;
}

// Urai variabel dari array hasil agar kodingan di bawah lebih pendek
$kriteria = $hasil['kriteria'];
$alternatif = $hasil['alternatif'];
$matriks_x = $hasil['matriks_x'];
$pembagi = $hasil['pembagi'];
$normalisasi = $hasil['normalisasi'];
$ranking = $hasil['ranking'];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-calculator me-2"></i> Perhitungan MOORA</h3>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer"></i> Cetak</button>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0 fw-bold text-primary">Tahap 1: Matriks Keputusan (X)</h6>
    </div>
    <div class="card-body table-responsive">
        <p class="small text-muted">Matriks awal berdasarkan konversi data mobil ke angka.</p>
        <table class="table table-bordered table-sm text-center">
            <thead class="table-secondary">
                <tr>
                    <th rowspan="2" class="align-middle">Alternatif (Mobil)</th>
                    <th colspan="<?= count($kriteria); ?>">Kriteria</th>
                </tr>
                <tr>
                    <?php foreach($kriteria as $k): ?>
                        <th><?= $k['kode_kriteria']; ?><br><small class="fw-light"><?= $k['atribut']; ?></small></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($alternatif as $alt): ?>
                <tr>
                    <td class="text-start fw-bold"><?= $alt['nama_mobil']; ?></td>
                    <?php foreach($kriteria as $k): ?>
                        <td>
                            <?php 
                            // Tampilkan nilai mentah
                            $nilai = $matriks_x[$alt['id_mobil']][$k['id_kriteria']] ?? 0;
                            echo number_format($nilai, 0, ',', '.'); 
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
                
                <tr class="table-info fw-bold">
                    <td class="text-start">Pembagi (Akar)</td>
                    <?php foreach($kriteria as $k): ?>
                        <td><?= number_format($pembagi[$k['id_kriteria']], 2); ?></td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0 fw-bold text-primary">Tahap 2: Matriks Ternormalisasi (R)</h6>
    </div>
    <div class="card-body table-responsive">
        <p class="small text-muted">Rumus: Nilai X dibagi Nilai Akar Pembagi.</p>
        <table class="table table-bordered table-sm text-center">
            <thead class="table-secondary">
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
                    <td class="text-start fw-bold"><?= $alt['nama_mobil']; ?></td>
                    <?php foreach($kriteria as $k): ?>
                        <td>
                            <?php 
                            $norm = $normalisasi[$alt['id_mobil']][$k['id_kriteria']] ?? 0;
                            echo number_format($norm, 4); // 4 digit desimal
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0 fw-bold">Tahap 3: Hasil Perangkingan (Nilai Optimasi Yi)</h6>
    </div>
    <div class="card-body">
        <div class="alert alert-success">
            <i class="bi bi-trophy-fill me-2"></i> Mobil Terbaik Rekomendasi Sistem: 
            <strong><?= $ranking[0]['nama_mobil']; ?></strong> dengan nilai <strong><?= number_format($ranking[0]['nilai_akhir'], 4); ?></strong>
        </div>

        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Peringkat</th>
                    <th>Nama Mobil</th>
                    <th>Harga</th>
                    <th>Nilai Optimasi (Yi)</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; foreach($ranking as $r): ?>
                <tr>
                    <td class="text-center"><span class="badge rounded-pill bg-dark">#<?= $rank++; ?></span></td>
                    <td class="fw-bold">
                        <img src="../foto_mobil/<?= $r['foto']; ?>" width="50" class="rounded me-2">
                        <?= $r['nama_mobil']; ?>
                    </td>
                    <td>Rp <?= number_format($r['harga'], 0, ',', '.'); ?></td>
                    <td class="fw-bold text-primary"><?= number_format($r['nilai_akhir'], 6); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>