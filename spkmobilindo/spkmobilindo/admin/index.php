<?php
/**
 * File: admin/index.php
 */

include 'header.php';

// Statistik Dasar
$mobil = query("SELECT * FROM tb_alternatif");
$jml_mobil = count($mobil);
$total = ($jml_mobil > 0) ? $jml_mobil : 1; 

if ($_SESSION['role'] === 'admin') {
    $kriteria = query("SELECT * FROM tb_kriteria");
    $jml_kriteria = count($kriteria);
    $result_mahal = mysqli_query($conn, "SELECT * FROM tb_alternatif ORDER BY harga_asli DESC LIMIT 1");
    $mobil_mahal = mysqli_fetch_assoc($result_mahal);
} else {
    $result_rata = mysqli_query($conn, "SELECT AVG(harga_asli) as rata_rata FROM tb_alternatif");
    $row_rata = mysqli_fetch_assoc($result_rata);
    $rata_rata_harga = $row_rata['rata_rata'] ?? 0;
    
    // Mengambil tahun berjalan secara otomatis dari sistem
    $tahun_sekarang = date('Y'); 
}
?>

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
        <div class="card bg-primary text-white h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="display-4 me-3"><i class="bi bi-car-front"></i></div>
                <div>
                    <h5 class="mb-0 small">Total Mobil</h5>
                    <h2 class="fw-bold mb-0"><?= $jml_mobil; ?> Unit</h2>
                </div>
            </div>
        </div>
    </div>

    <?php if ($_SESSION['role'] === 'admin') : ?>
    <div class="col-md-4">
        <div class="card bg-success text-white h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="display-4 me-3"><i class="bi bi-sliders"></i></div>
                <div>
                    <h5 class="mb-0 small">Kriteria MOORA</h5>
                    <h2 class="fw-bold mb-0"><?= $jml_kriteria; ?> Kriteria</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-dark h-100 border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title small fw-bold"><i class="bi bi-cash-coin me-2"></i>Harga Tertinggi</h5>
                <?php if($mobil_mahal) : ?>
                    <p class="fw-bold mb-0 small"><?= $mobil_mahal['nama_mobil']; ?></p>
                    <p class="mb-0 h5 fw-bold">Rp <?= number_format($mobil_mahal['harga_asli'], 0, ',', '.'); ?></p>
                <?php else : ?>
                    <p class="mb-0 small">- Belum ada data -</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php else : ?>
    <div class="col-md-4">
        <div class="card bg-info text-white h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="display-4 me-3"><i class="bi bi-currency-dollar"></i></div>
                <div>
                    <h5 class="mb-0 small">Rata-rata Harga</h5>
                    <h2 class="fw-bold mb-0">Rp <?= number_format($rata_rata_harga, 0, ',', '.'); ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-dark text-white h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="display-4 me-3"><i class="bi bi-calendar-check"></i></div>
                <div>
                    <h5 class="mb-0 small">Stok Terbaru</h5>
                    <h2 class="fw-bold mb-0">Tahun <?= $tahun_sekarang; ?></h2>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">
            <?= ($_SESSION['role'] === 'admin') ? 'Distribusi Status Pajak Kendaraan' : 'Distribusi Merek Mobil'; ?>
        </h5>
    </div>
    <div class="card-body">
        <?php if ($_SESSION['role'] === 'admin') : ?>
            <?php
                // PERBAIKAN: Menghitung status pajak dengan strpos()
                $hidup = 0; $mati = 0;
                foreach($mobil as $m) {
                    // Jika kata 'Hidup' ditemukan di kalimat status pajak
                    if(strpos($m['pajak_kendaraan'], 'Hidup') !== false) {
                        $hidup++;
                    } else {
                        $mati++;
                    }
                }
                $persen_hidup = ($hidup / $total) * 100;
                $persen_mati = ($mati / $total) * 100;
            ?>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1 small">
                    <span>Pajak Hidup (<?= $hidup; ?> Unit)</span>
                    <span><?= round($persen_hidup); ?>%</span>
                </div>
                <div class="progress" style="height: 15px;"><div class="progress-bar bg-success" style="width: <?= $persen_hidup; ?>%"></div></div>
            </div>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1 small">
                    <span>Pajak Mati / Telat (<?= $mati; ?> Unit)</span>
                    <span><?= round($persen_mati); ?>%</span>
                </div>
                <div class="progress" style="height: 15px;"><div class="progress-bar bg-danger" style="width: <?= $persen_mati; ?>%"></div></div>
            </div>
        <?php else : ?>
            <?php
                $merek_dist = [];
                foreach($mobil as $m) {
                    $merek = $m['merek'];
                    if(!isset($merek_dist[$merek])) $merek_dist[$merek] = 0;
                    $merek_dist[$merek]++;
                }
                $colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-dark'];
                $i = 0;
                foreach($merek_dist as $merek => $jml) :
                    $persen = ($jml / $total) * 100;
                    $color = $colors[$i % count($colors)]; $i++;
            ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1 small">
                        <span><?= $merek; ?> (<?= $jml; ?> Unit)</span>
                        <span><?= round($persen); ?>%</span>
                    </div>
                    <div class="progress" style="height: 15px;"><div class="progress-bar <?= $color; ?>" style="width: <?= $persen; ?>%"></div></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>