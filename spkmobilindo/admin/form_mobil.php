<?php
include 'header.php';

// Cek Mode: Tambah atau Ubah?
if (isset($_GET['id'])) {
    $judul = "Ubah Data Mobil";
    $is_edit = true;
    $id_mobil = $_GET['id'];
    $data = cariMobil($id_mobil); // Fungsi dari functions.php 
} else {
    $judul = "Tambah Mobil Baru";
    $is_edit = false;
}

// Proses Simpan Data
if (isset($_POST['submit'])) {
    if ($is_edit) {
        // Mode Ubah
        if (ubahMobil($_POST) > 0) {
            echo "<script>alert('Data berhasil diubah!'); document.location.href = 'data_mobil.php';</script>";
        } else {
            echo "<script>alert('Data gagal diubah / Tidak ada perubahan.');</script>";
        }
    } else {
        // Mode Tambah
        if (tambahMobil($_POST) > 0) {
            echo "<script>alert('Data berhasil ditambahkan!'); document.location.href = 'data_mobil.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data!');</script>";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 fw-bold"><?= $judul; ?></h5>
            </div>
            <div class="card-body p-4">
                <form action="" method="post" enctype="multipart/form-data">
                    
                    <?php if($is_edit): ?>
                        <input type="hidden" name="id_mobil" value="<?= $data['id_mobil']; ?>">
                        <input type="hidden" name="gambarLama" value="<?= $data['foto_mobil']; ?>">
                    <?php endif; ?>

                    <h6 class="fw-bold text-secondary mb-3">Informasi Kendaraan</h6>
                    <div class="mb-3">
                        <label class="form-label">Nama Mobil</label>
                        <input type="text" name="nama_mobil" class="form-control" required value="<?= $is_edit ? $data['nama_mobil'] : ''; ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Merek</label>
                            <input type="text" name="merek" class="form-control" required placeholder="Contoh: Toyota" value="<?= $is_edit ? $data['merek'] : ''; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tahun Produksi</label>
                            <input type="number" name="tahun" class="form-control" required value="<?= $is_edit ? $data['tahun'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" name="harga_asli" class="form-control" required value="<?= $is_edit ? $data['harga_asli'] : ''; ?>">
                            <div class="form-text text-danger">*Otomatis jadi Kriteria Harga (Cost)</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jarak Tempuh (KM)</label>
                            <input type="number" name="jarak_tempuh" class="form-control" required value="<?= $is_edit ? $data['jarak_tempuh'] : ''; ?>">
                            <div class="form-text text-danger">*Otomatis jadi Kriteria KM (Cost)</div>
                        </div>
                    </div>
                    
                    <hr>
                    <h6 class="fw-bold text-secondary mb-3">Penilaian Kriteria (MOORA)</h6>
                    <div class="alert alert-info small py-2">
                        Isi penilaian kondisi secara objektif (Skala 1 - 5). <br>
                        1 = Sangat Kurang/Buruk, 5 = Sangat Baik/Sempurna.
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kondisi Mesin (1-5)</label>
                            <select name="kondisi_mesin" class="form-select" required>
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <option value="<?= $i; ?>" <?= ($is_edit && $data['nilai_mesin'] == $i) ? 'selected' : ''; ?>><?= $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kondisi Bodi (1-5)</label>
                            <select name="kondisi_bodi" class="form-select" required>
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <option value="<?= $i; ?>" <?= ($is_edit && $data['nilai_bodi'] == $i) ? 'selected' : ''; ?>><?= $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kelengkapan Surat (1-5)</label>
                            <select name="kelengkapan_surat" class="form-select" required>
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <option value="<?= $i; ?>" <?= ($is_edit && $data['nilai_surat'] == $i) ? 'selected' : ''; ?>><?= $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pajak Kendaraan</label>
                        <select name="pajak_kendaraan" class="form-select" required>
                            <option value="" disabled <?= (!$is_edit) ? 'selected' : ''; ?>>-- Pilih Status Pajak --</option>
                            <option value="Hidup Panjang (> 6 Bulan)" <?= ($is_edit && $data['pajak_kendaraan'] == 'Hidup Panjang (> 6 Bulan)') ? 'selected' : ''; ?>>Hidup Panjang (> 6 Bulan)</option>
                            <option value="Hidup Pendek (< 6 Bulan)" <?= ($is_edit && $data['pajak_kendaraan'] == 'Hidup Pendek (< 6 Bulan)') ? 'selected' : ''; ?>>Hidup Pendek (< 6 Bulan)</option>
                            <option value="Telat / Mati 1 - 2 Tahun" <?= ($is_edit && $data['pajak_kendaraan'] == 'Telat / Mati 1 - 2 Tahun') ? 'selected' : ''; ?>>Telat / Mati 1 - 2 Tahun</option>
                            <option value="Mati 3 - 4 Tahun" <?= ($is_edit && $data['pajak_kendaraan'] == 'Mati 3 - 4 Tahun') ? 'selected' : ''; ?>>Mati 3 - 4 Tahun</option>
                            <option value="Mati > 4 Tahun" <?= ($is_edit && $data['pajak_kendaraan'] == 'Mati > 4 Tahun') ? 'selected' : ''; ?>>Mati > 4 Tahun</option>
                        </select>
                        <div class="form-text text-danger">*Otomatis dikonversi menjadi bobot nilai 1 - 5 pada matriks penilaian MOORA.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi Tambahan</label>
                        <textarea name="deskripsi" class="form-control" rows="3"><?= $is_edit ? $data['deskripsi'] : ''; ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Foto Mobil</label>
                        <?php if($is_edit && !empty($data['foto_mobil'])): ?>
                            <div class="mb-3">
                                <img src="../foto_mobil/<?= $data['foto_mobil']; ?>" 
                                     width="120" 
                                     class="img-thumbnail shadow-sm" 
                                     style="cursor: pointer; transition: 0.3s;" 
                                     data-bs-toggle="modal" 
                                     data-bs-target="#modalPreviewFoto"
                                     title="Klik untuk memperbesar"
                                     onmouseover="this.style.opacity='0.8'" 
                                     onmouseout="this.style.opacity='1'">
                                <div class="form-text text-primary mt-1"><i class="bi bi-zoom-in"></i> Klik gambar untuk memperbesar</div>
                            </div>

                            <div class="modal fade" id="modalPreviewFoto" tabindex="-1" aria-labelledby="modalPreviewFotoLabel" aria-hidden="true">
                              <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg">
                                  <div class="modal-header bg-dark text-white border-0">
                                    <h5 class="modal-title" id="modalPreviewFotoLabel"><i class="bi bi-image me-2"></i>Preview Foto: <?= $data['nama_mobil']; ?></h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body text-center p-0 bg-dark">
                                    <img src="../foto_mobil/<?= $data['foto_mobil']; ?>" class="img-fluid w-100" alt="Preview Foto">
                                  </div>
                                </div>
                              </div>
                            </div>
                        <?php endif; ?>
                        
                        <input type="file" name="foto" class="form-control">
                        <div class="form-text">*Kosongkan jika tidak ingin mengganti foto saat ini.</div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="submit" class="btn btn-primary btn-lg">Simpan Data</button>
                        <a href="data_mobil.php" class="btn btn-light border">Batal</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>