<?php 
// File: simulasi.php
include 'header.php';

// Inisialisasi Variabel Hasil agar tidak error saat pertama kali load
$top_3 = [];
$is_submitted = false;
$pesan_kosong = false; // Penanda jika freetext tidak membuahkan hasil

// KAMUS ARRAY ESTIMASI JARAK (JABODETABEK)
$estimasi_jarak = [
    'depok'             => 'Satu Kota (Estimasi 0 - 15 KM)',
    'jakarta selatan'   => 'Estimasi 15 - 25 KM',
    'jakarta timur'     => 'Estimasi 20 - 30 KM',
    'jakarta pusat'     => 'Estimasi 25 - 35 KM',
    'jakarta barat'     => 'Estimasi 30 - 40 KM',
    'jakarta utara'     => 'Estimasi 35 - 45 KM',
    'bogor'             => 'Estimasi 20 - 40 KM',
    'tangerang'         => 'Estimasi 30 - 50 KM',
    'tangerang selatan' => 'Estimasi 15 - 25 KM',
    'bekasi'            => 'Estimasi 25 - 45 KM'
];

$jarak_customer_asli = '';
$jarak_customer_lower = '';

// PROSES SAAT TOMBOL DITEKAN
if (isset($_POST['hitung'])) {
    $is_submitted = true;
    
    // Tangkap Input Pencarian & Jarak (Non-Algoritmik)
    $keyword_filter = trim($_POST['freetext']);
    
    // Tangkap Variabel Jarak dan ubah ke lowercase untuk pencocokan array
    if (isset($_POST['jarak_customer']) && $_POST['jarak_customer'] !== '') {
        $jarak_customer_asli = trim($_POST['jarak_customer']);
        $jarak_customer_lower = strtolower($jarak_customer_asli);
    }

    // Ambil Input Mentah Preferensi User (Skala 1-5) -> Algoritmik
    $input_mentah = [
        1 => (float)$_POST['p_harga'],    // C1 (Cost)
        2 => (float)$_POST['p_mesin'],    // C2 (Benefit)
        3 => (float)$_POST['p_bodi'],     // C3 (Benefit)
        4 => (float)$_POST['p_tahun'],    // C4 (Benefit)
        5 => (float)$_POST['p_km'],       // C5 (Cost)
        6 => (float)$_POST['p_surat'],    // C6 (Benefit)
        7 => (float)$_POST['p_pajak'],    // C7 (Benefit) 
        8 => (float)$_POST['p_merek']     // C8 (Benefit)
    ];
    
    // Normalisasi Bobot Input User (Total Sigma = 1.00)
    $total_input = array_sum($input_mentah);
    $bobot_dinamis = [];
    foreach ($input_mentah as $id_kriteria => $nilai) {
        if ($total_input > 0) {
            $bobot_dinamis[$id_kriteria] = $nilai / $total_input;
        } else {
            // Pengaman (Fail-safe)
            $bobot_dinamis[$id_kriteria] = 1 / 8;
        }
    }

    // Panggil Fungsi Otak SPK MOORA (Variabel jarak_customer SAMA SEKALI TIDAK DIMASUKKAN KESINI)
    $hasil_rekomendasi = hitungMOORA_publik($bobot_dinamis, $keyword_filter);
    
    if(empty($hasil_rekomendasi)) {
        $pesan_kosong = true;
    } else {
        // Ambil 3 Teratas Saja untuk ditampilkan
        $top_3 = array_slice($hasil_rekomendasi, 0, 3);
    }
}
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold"><i class="bi bi-sliders text-primary"></i> Simulasi SPK MOORA</h2>
        <p class="text-muted col-md-8 mx-auto">
            Bingung memilih? Tentukan prioritas Anda sendiri! <br>
            Sistem kami akan menghitung ulang rekomendasi mobil terbaik berdasarkan apa yang <b>menurut Anda</b> paling penting.
        </p>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 fw-bold">Atur Prioritas Anda</h5>
                </div>
                <div class="card-body bg-light">
                    <form action="" method="post" id="formSimulasi">
                        
                        <!-- Filter Opsional -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-primary">Pencarian Spesifik (Opsional)</label>
                            <input type="text" name="freetext" class="form-control form-control-sm" placeholder="Cth: Hitam, Sunroof..." autocomplete="off" value="<?= isset($_POST['freetext']) ? $_POST['freetext'] : ''; ?>">
                            <div class="form-text mt-1 text-muted" style="font-size: 11px; line-height: 1.2;">Kosongkan jika ingin menilai semua stok mobil.</div>
                        </div>

                        <!-- Input Jarak Tempat Tinggal Customer -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-primary">Asal Kota Anda (Opsional)</label>
                            <input type="text" name="jarak_customer" class="form-control form-control-sm" placeholder="Cth: Depok, Bogor..." autocomplete="off" value="<?= isset($_POST['jarak_customer']) ? $_POST['jarak_customer'] : ''; ?>">
                            <div class="form-text mt-1 text-muted" style="font-size: 11px; line-height: 1.2;">Hanya untuk estimasi pengiriman logistik, tidak mempengaruhi algoritma rekomendasi.</div>
                        </div>

                        <hr>
                        <!-- PERBAIKAN: Tulisan dibuat tebal (bold) dan berwarna biru (text-primary) -->
                        <p class="fw-bold text-primary mb-3">Seberapa penting faktor-faktor berikut bagi Anda?</p>
                        
                        <?php 
                        $form_items = [
                            'p_merek' => 'Merek Mobil',
                            'p_harga' => 'Harga Kendaraan',
                            'p_mesin' => 'Kondisi Mesin',
                            'p_bodi'  => 'Kondisi Bodi',
                            'p_tahun' => 'Tahun Produksi',
                            'p_km'    => 'Jarak Tempuh',
                            'p_surat' => 'Kelengkapan Surat',
                            'p_pajak' => 'Pajak Kendaraan'
                        ];
                        ?>

                        <?php foreach($form_items as $name => $label): ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold small"><?= $label; ?></label>
                            <select name="<?= $name; ?>" class="form-select form-select-sm preferensi-select" required>
                                <option value="" disabled selected>-- Pilih --</option>
                                <option value="1">Tidak Penting</option>
                                <option value="2">Kurang Penting</option>
                                <option value="3">Cukup Penting</option>
                                <option value="4">Penting</option>
                                <option value="5">Sangat Penting</option>
                            </select>
                        </div>
                        <?php endforeach; ?>

                        <div class="d-grid mt-4">
                            <div id="pesanPeringatan" class="text-danger small text-center mb-2 fw-bold">
                                *Silakan lengkapi semua preferensi di atas terlebih dahulu.
                            </div>
                            <button type="submit" name="hitung" id="btnSubmit" class="btn btn-primary py-2" disabled>
                                <i class="bi bi-search"></i> Temukan Mobil Terbaik
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <?php if(!$is_submitted): ?>
                <div class="text-center py-5 text-muted border rounded bg-white h-100 d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-arrow-left-circle display-4 mb-3 text-secondary"></i>
                    <h5>Silakan isi prioritas di samping kiri</h5>
                    <p>Pilih seluruh tingkat kepentingan kriteria, lalu klik tombol "Temukan Mobil Terbaik" untuk melihat hasilnya.</p>
                </div>
            <?php elseif($pesan_kosong): ?>
                <div class="text-center py-5 border rounded bg-white h-100 d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-search text-danger display-4 mb-3"></i>
                    <h5 class="text-danger">Mobil Tidak Ditemukan!</h5>
                    <p class="text-muted">Tidak ada mobil yang cocok dengan pencarian spesifik "<b><?= $_POST['freetext']; ?></b>".<br>Silakan hapus atau ubah kata kunci Anda.</p>
                </div>
            <?php else: ?>
                
                <!-- LOGIKA TAMPILAN SAPAAN & ESTIMASI JARAK -->
                <?php if(empty($jarak_customer_asli)): ?>
                    <div class="alert alert-info shadow-sm border-0 mb-4">
                        <h5 class="fw-bold text-dark mb-2">Top 3 Rekomendasi Untuk Anda!</h5>
                        <p class="mb-0 small text-dark">Berikut adalah 3 rekomendasi mobil terbaik yang paling sesuai dengan prioritas Anda! Silakan hubungi kami untuk menjadwalkan cek unit langsung di showroom.</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info shadow-sm border-0 mb-4">
                        <h5 class="fw-bold text-dark mb-3">Top 3 Rekomendasi Untuk Anda!</h5>
                        <ul class="list-unstyled small text-dark mb-3">
                            <li class="mb-1">📍 <b>Lokasi Anda:</b> <?= htmlspecialchars($jarak_customer_asli); ?></li>
                            <li>🚗 <b>Estimasi Jarak ke Showroom:</b> 
                                <?php 
                                // Cek apakah kota ada di dalam kamus array
                                if (array_key_exists($jarak_customer_lower, $estimasi_jarak)) {
                                    echo $estimasi_jarak[$jarak_customer_lower];
                                } else {
                                    echo "Menyesuaikan titik lokasi akurat Anda";
                                }
                                ?>
                            </li>
                        </ul>
                        <p class="mb-0 small text-dark">Berikut adalah 3 rekomendasi mobil terbaik untuk Anda! Tim Sadulur Mobilindo siap melayani konsultasi tujuan pengiriman unit ke lokasi Anda. Silakan klik tombol WhatsApp untuk negosiasi lebih lanjut.</p>
                    </div>
                <?php endif; ?>
                <!-- END LOGIKA TAMPILAN SAPAAN -->
                
                <?php foreach($top_3 as $index => $res): 
                    $m = $res['data'];
                ?>
                <div class="card card-mobil border-0 shadow-sm mb-3 overflow-hidden">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="foto_mobil/<?= $m['foto_mobil']; ?>" class="img-fluid h-100 w-100" style="object-fit: cover;" alt="...">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge bg-success mb-2">Peringkat #<?= $index + 1; ?></span>
                                        <h5 class="card-title fw-bold mb-1"><?= $m['nama_mobil']; ?></h5>
                                        <p class="text-muted small mb-2"><?= $m['merek']; ?> | <?= $m['tahun']; ?></p>
                                    </div>
                                    <h4 class="text-primary fw-bold">Rp <?= number_format($m['harga_asli'], 0, ',', '.'); ?></h4>
                                </div>
                                <p class="card-text small text-secondary mb-3">
                                    <?= substr($m['deskripsi'], 0, 100); ?>...
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="small text-muted">
                                        <i class="bi bi-star-fill text-warning"></i> Skor Kecocokan: 
                                        <b><?= number_format($res['skor'], 4); ?></b>
                                    </div>
                                    <a href="detail.php?id=<?= $m['id_mobil']; ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="alert alert-secondary mt-4 small border-0">
                    <i class="bi bi-info-circle-fill"></i> Hasil ini dihitung menggunakan algoritma MOORA.
                    <?php if(!empty($_POST['freetext'])): ?>
                        (Telah difilter menggunakan kata kunci: <b><?= htmlspecialchars($_POST['freetext']); ?></b>)
                    <?php endif; ?>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const selects = document.querySelectorAll('.preferensi-select');
        const btnSubmit = document.getElementById('btnSubmit');
        const pesanPeringatan = document.getElementById('pesanPeringatan');

        function periksaPilihan() {
            let semuaTerisi = true;
            selects.forEach(function(select) {
                if (select.value === "") {
                    semuaTerisi = false;
                }
            });

            if (semuaTerisi) {
                btnSubmit.removeAttribute('disabled');
                pesanPeringatan.style.display = 'none';
            } else {
                btnSubmit.setAttribute('disabled', 'disabled');
                pesanPeringatan.style.display = 'block';
            }
        }

        selects.forEach(function(select) {
            select.addEventListener('change', periksaPilihan);
        });
    });
</script>

<?php include 'footer.php'; ?>