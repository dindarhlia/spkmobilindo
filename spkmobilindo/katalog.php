<?php
// File: katalog.php
include 'header.php';

// Logika Pencarian
if (isset($_POST['cari'])) {
    $keyword = $_POST['keyword'];
    // Cari berdasarkan nama mobil atau merek
    $query = "SELECT * FROM tb_alternatif WHERE 
              nama_mobil LIKE '%$keyword%' OR 
              merek LIKE '%$keyword%'";
    $mobil = query($query);
} else {
    // Jika tidak mencari, tampilkan semua mobil
    $mobil = query("SELECT * FROM tb_alternatif ORDER BY id_mobil DESC");
}
?>

<div class="bg-primary text-white py-5 mb-5">
    <div class="container text-center">
        <h1 class="fw-bold">Katalog Mobil Bekas</h1>
        <p class="lead">Pilih mobil berkualitas sesuai kebutuhan dan budget Anda.</p>
    </div>
</div>

<div class="container mb-5">
    
    <div class="row justify-content-center mb-5">
        <div class="col-md-6">
            <form action="" method="post">
                <div class="input-group input-group-lg shadow-sm">
                    <input type="text" name="keyword" class="form-control border-0" placeholder="Cari merek atau nama mobil..." autocomplete="off">
                    <button class="btn btn-dark px-4" type="submit" name="cari"><i class="bi bi-search"></i> Cari</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <?php if(empty($mobil)) : ?>
            <div class="col-12 text-center py-5">
                <img src="assets/img/empty.svg" alt="" width="150" class="mb-3 opacity-50"> <h4 class="text-muted">Mobil tidak ditemukan.</h4>
                <p>Coba kata kunci lain atau hubungi admin.</p>
                <a href="katalog.php" class="btn btn-outline-primary mt-2">Reset Pencarian</a>
            </div>
        <?php else : ?>
            
            <?php foreach($mobil as $m) : ?>
            <div class="col-md-4 mb-4">
                <div class="card card-mobil h-100 border-0 shadow-sm">
                    <div class="position-absolute top-0 end-0 bg-warning text-dark fw-bold px-3 py-1 rounded-bottom-start shadow-sm">
                        <?= $m['tahun']; ?>
                    </div>

                    <img src="foto_mobil/<?= $m['foto_mobil']; ?>" class="card-img-top" alt="<?= $m['nama_mobil']; ?>" style="height: 220px; object-fit: cover;">
                    
                    <div class="card-body">
                        <small class="text-muted text-uppercase fw-bold"><?= $m['merek']; ?></small>
                        <h5 class="card-title fw-bold text-dark mt-1"><?= $m['nama_mobil']; ?></h5>
                        
                        <h4 class="text-primary fw-bold my-3">Rp <?= number_format($m['harga_asli'], 0, ',', '.'); ?></h4>
                        
                        <div class="row small text-secondary mb-3">
                            <div class="col-6 border-end">
                                <i class="bi bi-speedometer2 me-1"></i> <?= number_format($m['jarak_tempuh']); ?> KM
                            </div>
                            <div class="col-6 ps-3">
                                <i class="bi bi-gear-wide-connected me-1"></i> <?= $m['transmisi']; ?>
                            </div>
                        </div>

                        <div class="d-grid">
                            <a href="detail.php?id=<?= $m['id_mobil']; ?>" class="btn btn-outline-dark">Lihat Spesifikasi</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>

</div>

<?php include 'footer.php'; ?>