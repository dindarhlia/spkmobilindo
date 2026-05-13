<?php 
session_start();
require '../config/functions.php';

if (!isset($_SESSION['status_login'])) {
    header("Location: login.php");
    exit;
}

// ==========================================
// LOGIKA OPSI B: PROSES HAPUS DATA
// ==========================================
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_hapus = $_GET['id'];
    
    if (hapusMobil($id_hapus) > 0) {
        echo "<script>
                alert('Data mobil berhasil dihapus!');
                document.location.href = 'data_mobil.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus data!');
                document.location.href = 'data_mobil.php';
              </script>";
    }
}

// Ambil data mobil terbaru untuk ditampilkan di tabel
$mobil = query("SELECT * FROM tb_alternatif");
?>

<?php include 'header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Data Mobil</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Data Mobil</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <a href="form_mobil.php" class="btn btn-primary btn-sm">Tambah Mobil</a>
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama Mobil</th>
                        <th>Merek</th>
                        <th>Harga</th>
                        <th>Tahun</th>
                        <th>KM</th>
                        <th>Pajak</th> 
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach($mobil as $m) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td>
                            <img src="../foto_mobil/<?= $m['foto_mobil']; ?>" width="100" class="img-thumbnail">
                        </td>
                        <td><?= $m['nama_mobil']; ?></td>
                        <td><?= $m['merek']; ?></td>
                        <td>Rp <?= number_format($m['harga_asli'], 0, ',', '.'); ?></td>
                        <td><?= $m['tahun']; ?></td>
                        <td><?= number_format($m['jarak_tempuh'], 0, ',', '.'); ?> km</td>
                        <td>
                            <span class="badge <?= ($m['pajak_kendaraan'] == 'Hidup') ? 'bg-success' : 'bg-danger'; ?>">
                                <?= $m['pajak_kendaraan']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="form_mobil.php?id=<?= $m['id_mobil']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            
                            <a href="data_mobil.php?aksi=hapus&id=<?= $m['id_mobil']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>