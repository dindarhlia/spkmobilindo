<?php 
// File: simulasi.php
include 'header.php';

// Inisialisasi Variabel Hasil agar tidak error saat pertama kali load
$hasil_rekomendasi = [];
$is_submitted = false;

// PROSES SAAT TOMBOL DITEKAN
if (isset($_POST['hitung'])) {
    $is_submitted = true;
    
    // 1. Ambil Input Preferensi User (Skala 1-5)
    $bobot_input = [
        1 => $_POST['p_harga'],    // C1 (Cost)
        2 => $_POST['p_mesin'],    // C2 (Benefit)
        3 => $_POST['p_bodi'],     // C3 (Benefit)
        4 => $_POST['p_tahun'],    // C4 (Benefit)
        5 => $_POST['p_km'],       // C5 (Cost)
        6 => $_POST['p_surat'],    // C6 (Benefit)
        7 => $_POST['p_pajak']     // C7 (Benefit)
    ];
    
    // 2. Normalisasi Bobot Input User (Total bobot harus = 1)
    $total_bobot_input = array_sum($bobot_input);
    $bobot_baru = [];
    foreach ($bobot_input as $id_kriteria => $nilai) {
        $bobot_baru[$id_kriteria] = $nilai / $total_bobot_input;
    }

    // 3. Jalankan Logika MOORA (Versi Custom untuk Simulasi)
    $alternatif = query("SELECT * FROM tb_alternatif");
    $penilaian_db = query("SELECT * FROM tb_penilaian");
    
    // Susun Matriks X
    $matriks_x = [];
    foreach ($penilaian_db as $p) {
        $matriks_x[$p['id_mobil']][$p['id_kriteria']] = $p['nilai'];
    }
    
    // B. Hitung Pembagi (Akar Kuadrat)
    $pembagi = [];
    for ($k=1; $k<=7; $k++) {
        $sum_kuadrat = 0;
        foreach ($alternatif as $alt) {
            $val = $matriks_x[$alt['id_mobil']][$k] ?? 0;
            $sum_kuadrat += pow($val, 2);
        }
        $pembagi[$k] = sqrt($sum_kuadrat);
    }
    
    // C. Normalisasi & Optimasi (Yi) pakai Bobot Baru User
    foreach ($alternatif as $alt) {
        $id_m = $alt['id_mobil'];
        $total_benefit = 0;
        $total_cost = 0;
        
        // C1(Harga) & C5(KM) adalah COST. Sisanya BENEFIT.
        $arr_cost = [1, 5]; 
        
        for ($k=1; $k<=7; $k++) {
            $pembagi_k = ($pembagi[$k] == 0) ? 1 : $pembagi[$k];
            $raw = $matriks_x[$id_m][$k] ?? 0;
            
            // Normalisasi
            $r = $raw / $pembagi_k;
            
            // Terbobot (Pakai $bobot_baru dari input user)
            $y = $r * $bobot_baru[$k];
            
            if (in_array($k, $arr_cost)) {
                $total_cost += $y;
            } else {
                $total_benefit += $y;
            }
        }
        
        $nilai_akhir = $total_benefit - $total_cost;
        
        $hasil_rekomendasi[] = [
            'data' => $alt,
            'skor' => $nilai_akhir
        ];
    }
    
    // 4. Urutkan dari Skor Tertinggi (Ranking)
    usort($hasil_rekomendasi, function($a, $b) {
        return $b['skor'] <=> $a['skor'];
    });
    
    // Ambil 3 Teratas saja
    $top_3 = array_slice($hasil_rekomendasi, 0, 3);
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
                        <p class="small text-muted mb-3">Seberapa penting faktor-faktor berikut bagi Anda?</p>
                        
                        <?php 
                        $form_items = [
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
                            <button type="submit" name="hitung" id="btnSubmit" class="btn btn-dark py-2" disabled>
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
            <?php else: ?>
                
                <h4 class="fw-bold mb-4 text-success">Top 3 Rekomendasi Untuk Anda:</h4>
                
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

                <div class="alert alert-info mt-4 small">
                    <i class="bi bi-info-circle-fill"></i> Hasil ini dihitung menggunakan metode MOORA berdasarkan bobot prioritas yang baru saja Anda masukkan.
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

        // Fungsi untuk mengecek apakah semua select sudah terisi
        function periksaPilihan() {
            let semuaTerisi = true;
            selects.forEach(function(select) {
                if (select.value === "") {
                    semuaTerisi = false;
                }
            });

            // Jika semua sudah dipilih, aktifkan tombol & sembunyikan pesan
            if (semuaTerisi) {
                btnSubmit.removeAttribute('disabled');
                pesanPeringatan.style.display = 'none';
            } else {
                btnSubmit.setAttribute('disabled', 'disabled');
                pesanPeringatan.style.display = 'block';
            }
        }

        // Jalankan fungsi cek setiap kali pengguna mengganti pilihan dropdown
        selects.forEach(function(select) {
            select.addEventListener('change', periksaPilihan);
        });
    });
</script>

<?php include 'footer.php'; ?>