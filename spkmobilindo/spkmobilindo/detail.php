<?php
// File: detail.php
include 'header.php';

// Ambil ID dari URL
$id = $_GET['id'];

// Query data mobil berdasarkan ID
$data = query("SELECT * FROM tb_alternatif WHERE id_mobil = $id")[0];

// Jika ID tidak ditemukan, kembalikan ke katalog
if (!$data) {
    echo "<script>document.location.href = 'katalog.php';</script>";
    exit;
}

// Ambil Data Nilai Kriteria dari tb_penilaian
$nilai_mesin = query("SELECT nilai FROM tb_penilaian WHERE id_mobil = $id AND id_kriteria = 2")[0]['nilai'];
$nilai_bodi = query("SELECT nilai FROM tb_penilaian WHERE id_mobil = $id AND id_kriteria = 3")[0]['nilai'];
$nilai_surat = query("SELECT nilai FROM tb_penilaian WHERE id_mobil = $id AND id_kriteria = 6")[0]['nilai'];

// Fungsi Helper sederhana untuk ubah angka 1-5 jadi Label Text
function getLabelKualitas($angka) {
    if ($angka == 5) return "Sangat Baik / Lengkap";
    if ($angka == 4) return "Baik";
    if ($angka == 3) return "Cukup";
    if ($angka == 2) return "Kurang";
    return "Buruk";
}
?>

<div class="container py-5">
    
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="katalog.php">Katalog</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $data['nama_mobil']; ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow overflow-hidden rounded-3">
                <img src="foto_mobil/<?= $data['foto_mobil']; ?>" class="img-fluid w-100" alt="<?= $data['nama_mobil']; ?>" style="object-fit: cover; max-height: 500px;">
            </div>
        </div>

        <div class="col-md-5">
            <h2 class="fw-bold mb-1"><?= $data['nama_mobil']; ?></h2>
            <p class="text-muted fs-5 mb-3"><?= $data['merek']; ?> | Tahun <?= $data['tahun']; ?></p>
            
            <h1 class="text-primary fw-bold mb-4">Rp <?= number_format($data['harga_asli'], 0, ',', '.'); ?></h1>

            <div class="card bg-light border-0 mb-4 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Spesifikasi Utama</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-secondary" width="45%">Jarak Tempuh</td>
                            <td class="fw-bold">: <?= number_format($data['jarak_tempuh'], 0, ',', '.'); ?> km</td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Pajak Kendaraan</td>
                            <td>: 
                                <?php 
                                // PERBAIKAN BADGE PAJAK
                                $bg_pajak = (strpos($data['pajak_kendaraan'], 'Hidup') !== false) ? 'bg-success' : 'bg-danger'; 
                                ?>
                                <span class="badge <?= $bg_pajak; ?>"><?= $data['pajak_kendaraan']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Kondisi Mesin</td>
                            <td class="fw-bold">: <?= getLabelKualitas($nilai_mesin); ?></td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Kondisi Bodi</td>
                            <td class="fw-bold">: <?= getLabelKualitas($nilai_bodi); ?></td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Kelengkapan Dokumen</td>
                            <td class="fw-bold text-success">: <?= getLabelKualitas($nilai_surat); ?> <i class="bi bi-check-circle-fill"></i></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold">Deskripsi Penjual:</h6>
                <p class="text-secondary" style="text-align: justify;"><?= nl2br($data['deskripsi']); ?></p>
            </div>

            <div class="d-grid gap-2">
                <?php 
                    $pesan = "Halo Sadulur Mobilindo, saya tertarik dengan mobil " . $data['nama_mobil'] . " tahun " . $data['tahun'] . ". Apakah unit masih tersedia?";
                    $linkWA = "https://wa.me/628872667070?text=" . urlencode($pesan);
                ?>
                <a href="<?= $linkWA; ?>" target="_blank" class="btn btn-success btn-lg">
                    <i class="bi bi-whatsapp me-2"></i> Hubungi via WhatsApp
                </a>
                <a href="katalog.php" class="btn btn-outline-secondary">Kembali ke Katalog</a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>