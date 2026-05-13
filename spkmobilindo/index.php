<?php
// File: index.php
include 'header.php';

// Ambil 3 mobil terbaru untuk ditampilkan di Home
// Menggunakan fungsi query() dari config/functions.php
$mobil_terbaru = query("SELECT * FROM tb_alternatif ORDER BY id_mobil DESC LIMIT 3");
?>

<section class="hero-section text-center py-5 bg-white">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Temukan Mobil Impian Anda</h1>
        <p class="lead mb-4">Kami membantu Anda memilih mobil bekas berkualitas dengan rekomendasi cerdas sistem SPK MOORA.</p>
        <a href="simulasi.php" class="btn btn-primary btn-lg px-5 py-3 shadow">Cari Rekomendasi Sekarang</a>
    </div>
</section>

<section class="py-5">
    <div class="container text-center">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="p-4 bg-white rounded shadow-sm h-100 border border-light">
                    <h3 class="text-primary fw-bold">Terpercaya</h3>
                    <p>Unit mobil di Sadulur Mobilindo telah lolos inspeksi ketat kondisi mesin dan bodi.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded shadow-sm h-100 border border-light">
                    <h3 class="text-primary fw-bold">Objektif</h3>
                    <p>Sistem kami menggunakan metode MOORA untuk memberikan penilaian yang adil tanpa bias.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded shadow-sm h-100 border border-light">
                    <h3 class="text-primary fw-bold">Transparan</h3>
                    <p>Harga dan kondisi mobil ditampilkan apa adanya sesuai data lapangan.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Stok Terbaru</h2>
            <p class="text-muted">Pilihan mobil bekas berkualitas yang baru saja masuk showroom.</p>
        </div>

        <div class="row">
            <?php if(empty($mobil_terbaru)) : ?>
                <div class="col-12 text-center">
                    <div class="alert alert-warning">Belum ada data mobil yang diinput. Silakan login Admin untuk menambah data.</div>
                </div>
            <?php else : ?>
                <?php foreach($mobil_terbaru as $m) : ?>
                    <div class="col-md-4 mb-4">
                        <div class="card card-mobil h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                            <img src="foto_mobil/<?= $m['foto_mobil']; ?>" class="card-img-top" alt="<?= $m['nama_mobil']; ?>" style="height: 220px; object-fit: cover;">
                            
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-1"><?= $m['nama_mobil']; ?></h5>
                                <p class="text-muted small mb-2"><?= $m['merek']; ?> | <?= $m['tahun']; ?></p>
                                <h4 class="text-primary fw-bold mb-3">Rp <?= number_format($m['harga_asli'], 0, ',', '.'); ?></h4>
                                
                                <ul class="list-unstyled small text-secondary mb-4">
                                    <li class="mb-1"><i class="bi bi-speedometer2 me-2"></i>KM: <?= number_format($m['jarak_tempuh'], 0, ',', '.'); ?></li>
                                    <li>
                                        <i class="bi bi-calendar-check me-2"></i>Pajak: 
                                        <?php if($m['pajak_kendaraan'] == 'Hidup') : ?>
                                            <span class="text-success fw-bold">Hidup</span>
                                        <?php else : ?>
                                            <span class="text-danger fw-bold">Mati</span>
                                        <?php endif; ?>
                                    </li>
                                </ul>
                                
                                <div class="d-grid">
                                    <a href="detail.php?id=<?= $m['id_mobil']; ?>" class="btn btn-outline-primary rounded-pill">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="katalog.php" class="btn btn-dark px-5 py-2 rounded-pill shadow">Lihat Semua Mobil</a>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>