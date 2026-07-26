<?php
include 'header.php';

// LOGIKA UPDATE DATA
if (isset($_POST['simpan_kriteria'])) {
    // Cek Keamanan: Hanya admin yang boleh edit
    if ($_SESSION['role'] !== 'admin') {
        echo "<script>
                alert('Akses Ditolak! Owner hanya dapat melihat data.');
                document.location.href = 'data_kriteria.php';
              </script>";
        exit;
    }

    // =========================================================================
    // [BARU] LOGIKA VALIDASI KEAMANAN BOBOT MOORA (SERVER-SIDE VALIDATION)
    // =========================================================================
    $id_kriteria = $_POST['id_kriteria'];
    $bobot_baru  = (float) $_POST['bobot'];

    // 1. Ambil total bobot selain kriteria yang saat ini sedang diubah
    $cek_bobot = query("SELECT SUM(bobot) as total_sementara FROM tb_kriteria WHERE id_kriteria != '$id_kriteria'")[0];
    $total_sementara = (float) $cek_bobot['total_sementara'];

    // 2. Kalkulasikan total sementara di database dengan inputan bobot baru di form
    $kalkulasi_total = round($total_sementara + $bobot_baru, 2);

    // 3. Eksekusi Penolakan jika melebihi batas 1.00
    if ($kalkulasi_total > 1.00) {
        echo "<script>
                alert('GAGAL DISIMPAN: Total akumulasi bobot kriteria wajib bernilai 1.00.\\n(Sistem mendeteksi total akan menjadi $kalkulasi_total jika disimpan)');
                history.back(); // Kembalikan ke form edit tanpa mereset inputan
              </script>";
        exit; // Hentikan eksekusi script agar data salah tidak masuk ke database
    }
    // =========================================================================


    // Jika lolos validasi bobot (<= 1.00), jalankan proses ubahKriteria
    if (ubahKriteria($_POST) > 0) {
        echo "<script>
                alert('Kriteria berhasil diupdate!');
                document.location.href = 'data_kriteria.php';
              </script>";
    } else {
        echo "<script>
                alert('Tidak ada perubahan data.');
                document.location.href = 'data_kriteria.php';
              </script>";
    }
}

// LOGIKA TAMPILAN: Apakah sedang mode EDIT atau mode LIHAT?
$mode_edit = false;
$kriteria_edit = null;

if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit' && isset($_GET['id'])) {
    // Hanya admin yang bisa masuk mode edit
    if ($_SESSION['role'] === 'admin') {
        $mode_edit = true;
        $kriteria_edit = cariKriteria($_GET['id']);
    } else {
        echo "<script>alert('Akses Ditolak! Anda tidak memiliki izin untuk mengedit.');</script>";
    }
}

// Ambil semua data kriteria
$kriteria = query("SELECT * FROM tb_kriteria ORDER BY id_kriteria ASC");
?>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-list-task me-2"></i> Data Kriteria & Bobot</h5>
            </div>
            <div class="card-body">
                
                <!-- [BARU] PERINGATAN VISUAL DINAMIS -->
                <?php 
                    $total_bobot_mentah = 0;
                    foreach($kriteria as $k) {
                        $total_bobot_mentah += $k['bobot'];
                    }
                    $total_bobot = round($total_bobot_mentah, 2);
                ?>
                
                <?php if ($total_bobot == 1.00): ?>
                    <div class="alert alert-success d-flex align-items-center small mb-4 shadow-sm border-0" role="alert">
                        <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                        <div>
                            Total Bobot saat ini: <strong><?= $total_bobot; ?></strong>. <br>
                            Distribusi bobot sudah valid (100%) dan integritas perhitungan algoritma MOORA terjamin akurat.
                        </div>
                    </div>
                <?php elseif ($total_bobot > 1.00): ?>
                    <div class="alert alert-danger d-flex align-items-center small mb-4 shadow-sm border-0" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                        <div>
                            <strong>Peringatan Sistem!</strong> Total Bobot saat ini: <strong><?= $total_bobot; ?></strong>.<br>
                            Akumulasi bobot <b>TIDAK BOLEH</b> melebihi 1.0. Integritas algoritma MOORA akan cacat. Harap kurangi bobot kriteria!
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning d-flex align-items-center small mb-4 shadow-sm border-0" role="alert">
                        <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                        <div>
                            Total Bobot saat ini: <strong><?= $total_bobot; ?></strong>.<br>
                            Sangat disarankan total mencapai pas 1.0 (100%) agar distribusi prioritas lebih proporsional.
                        </div>
                    </div>
                <?php endif; ?>
                <!-- END PERINGATAN VISUAL DINAMIS -->

                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Kriteria</th>
                            <th>Atribut</th>
                            <th>Bobot</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kriteria as $k) : ?>
                        <tr class="<?= ($mode_edit && $kriteria_edit['id_kriteria'] == $k['id_kriteria']) ? 'table-primary' : ''; ?>">
                            <td class="text-center fw-bold"><?= $k['kode_kriteria']; ?></td>
                            <td><?= $k['nama_kriteria']; ?></td>
                            <td>
                                <?php if($k['atribut'] == 'benefit'): ?>
                                    <span class="badge bg-success">Benefit</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Cost</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $k['bobot']; ?></td>
                            <td class="text-center">
                                <?php if ($_SESSION['role'] === 'admin') : ?>
                                <a href="data_kriteria.php?aksi=edit&id=<?= $k['id_kriteria']; ?>" class="btn btn-sm btn-warning text-white">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <?php else : ?>
                                    <span class="badge bg-secondary">View Only</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if($mode_edit): ?>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-light">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0 fw-bold">Edit Kriteria: <?= $kriteria_edit['kode_kriteria']; ?></h6>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <input type="hidden" name="id_kriteria" value="<?= $kriteria_edit['id_kriteria']; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Kriteria</label>
                        <input type="text" name="nama_kriteria" class="form-control" value="<?= $kriteria_edit['nama_kriteria']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Atribut</label>
                        <select name="atribut" class="form-select">
                            <option value="benefit" <?= ($kriteria_edit['atribut'] == 'benefit') ? 'selected' : ''; ?>>Benefit (Semakin Tinggi Semakin Bagus)</option>
                            <option value="cost" <?= ($kriteria_edit['atribut'] == 'cost') ? 'selected' : ''; ?>>Cost (Semakin Rendah Semakin Bagus)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bobot Nilai</label>
                        <input type="number" step="0.01" name="bobot" class="form-control" value="<?= $kriteria_edit['bobot']; ?>" required>
                        <div class="form-text">Gunakan desimal (contoh: 0.25)</div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="simpan_kriteria" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="data_kriteria.php" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>