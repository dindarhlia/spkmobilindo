<?php
include 'header.php';

// LOGIKA UPDATE DATA
if (isset($_POST['simpan_kriteria'])) {
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
    $mode_edit = true;
    $kriteria_edit = cariKriteria($_GET['id']);
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
                <div class="alert alert-info small mb-3">
                    <i class="bi bi-info-circle"></i> 
                    Total Bobot saat ini: 
                    <strong>
                    <?php 
                        $total_bobot = 0;
                        foreach($kriteria as $k) $total_bobot += $k['bobot'];
                        echo $total_bobot; 
                    ?>
                    </strong>. 
                    (Pastikan total mendekati 1.0 atau 100% agar perhitungan akurat).
                </div>

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
                                <a href="data_kriteria.php?aksi=edit&id=<?= $k['id_kriteria']; ?>" class="btn btn-sm btn-warning text-white">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
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