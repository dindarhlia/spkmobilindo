<?php
// Include Header (Sudah ada session start & functions)
include 'header.php';

// Logika Hapus Data
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id'])) {
    $id_hapus = $_GET['id'];
    if (hapusMobil($id_hapus) > 0) {
        echo "<script>
                alert('Data mobil berhasil dihapus!');
                document.location.href = 'data_mobil.php';
              </script>";
    } else {
        echo "<script>alert('Gagal menghapus data.');</script>";
    }
}

// Ambil semua data mobil
$mobil = query("SELECT * FROM tb_alternatif ORDER BY id_mobil DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-car-front-fill me-2"></i> Data Mobil Bekas</h3>
    <a href="form_mobil.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Mobil</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama Mobil</th>
                        <th>Tahun</th>
                        <th>Transmisi</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach ($mobil as $m) : ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td>
                            <img src="../foto_mobil/<?= $m['foto_mobil']; ?>" width="80" height="50" style="object-fit: cover; border-radius: 5px;">
                        </td>
                        <td class="fw-bold"><?= $m['nama_mobil']; ?> <br> <small class="text-muted"><?= $m['merek']; ?></small></td>
                        <td><?= $m['tahun']; ?></td>
                        <td>
                            <?php if($m['transmisi'] == 'Matic'): ?>
                                <span class="badge bg-info text-dark">Matic</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Manual</span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-bold text-primary">Rp <?= number_format($m['harga_asli'], 0, ',', '.'); ?></td>
                        <td>
                            <a href="form_mobil.php?id=<?= $m['id_mobil']; ?>" class="btn btn-sm btn-warning text-white"><i class="bi bi-pencil-square"></i></a>
                            <a href="data_mobil.php?aksi=hapus&id=<?= $m['id_mobil']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?');"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($mobil)) : ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data mobil. Silakan tambah data.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>