<?php
/**
 * File: config/functions.php
 */

require 'koneksi.php';

function query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Fungsi baru khusus untuk memastikan jarak tempuh (KM) benar-benar bulat
function bersihkanAngkaKM($angka) {
    return intval(preg_replace("/[^0-9]/", "", $angka));
}

function tambahMobil($data) {
    global $conn;

    $nama_mobil = mysqli_real_escape_string($conn, $data['nama_mobil']);
    $merek      = mysqli_real_escape_string($conn, $data['merek']);
    $deskripsi  = mysqli_real_escape_string($conn, $data['deskripsi']);
    $pajak      = mysqli_real_escape_string($conn, $data['pajak_kendaraan']); 
    
    // KEMBALIKAN KE FUNGSI AWAL UNTUK HARGA DAN TAHUN
    $harga = htmlspecialchars($data['harga_asli']); 
    $tahun = htmlspecialchars($data['tahun']);
    
    // GUNAKAN FUNGSI BARU KHUSUS UNTUK JARAK TEMPUH (KM)
    $jarak = bersihkanAngkaKM($data['jarak_tempuh']); 

    $foto = upload();
    if (!$foto) return false; 

    $query_mobil = "INSERT INTO tb_alternatif 
                    VALUES (NULL, '$nama_mobil', '$merek', '$harga', '$tahun', '$jarak', '$pajak', '$deskripsi', '$foto')";
    
    mysqli_query($conn, $query_mobil);
    $id_baru = mysqli_insert_id($conn);

    // Simpan Nilai MOORA (Harga & Tahun pakai fungsi lama, KM pakai fungsi baru)
    simpanNilai($id_baru, 1, $harga); 
    simpanNilai($id_baru, 2, $data['kondisi_mesin']); 
    simpanNilai($id_baru, 3, $data['kondisi_bodi']);  
    simpanNilai($id_baru, 4, $tahun); 
    simpanNilai($id_baru, 5, $jarak); // Jarak tempuh dipastikan bulat
    simpanNilai($id_baru, 6, $data['kelengkapan_surat']); 
    
    $nilai_pajak = ($pajak == 'Hidup') ? 5 : 3;
    simpanNilai($id_baru, 7, $nilai_pajak); 

    return mysqli_affected_rows($conn);
}

function ubahMobil($data) {
    global $conn;

    $id         = $data['id_mobil'];
    $nama_mobil = mysqli_real_escape_string($conn, $data['nama_mobil']);
    $merek      = mysqli_real_escape_string($conn, $data['merek']);
    $deskripsi  = mysqli_real_escape_string($conn, $data['deskripsi']);
    $pajak      = mysqli_real_escape_string($conn, $data['pajak_kendaraan']); 
    $gambarLama = $data['gambarLama'];

    // KEMBALIKAN KE FUNGSI AWAL UNTUK HARGA DAN TAHUN
    $harga = htmlspecialchars($data['harga_asli']);
    $tahun = htmlspecialchars($data['tahun']);
    
    // GUNAKAN FUNGSI BARU KHUSUS UNTUK JARAK TEMPUH (KM)
    $jarak = bersihkanAngkaKM($data['jarak_tempuh']);

    $foto = ($_FILES['foto']['error'] === 4) ? $gambarLama : upload();

    $query = "UPDATE tb_alternatif SET
                nama_mobil      = '$nama_mobil',
                merek           = '$merek',
                harga_asli      = '$harga',
                tahun           = '$tahun',
                jarak_tempuh    = '$jarak',
                pajak_kendaraan = '$pajak',
                deskripsi       = '$deskripsi',
                foto_mobil      = '$foto'
              WHERE id_mobil = $id";
    
    mysqli_query($conn, $query);

    // Update Nilai MOORA (Harga & Tahun pakai fungsi lama, KM pakai fungsi baru)
    updateNilai($id, 1, $harga);
    updateNilai($id, 2, $data['kondisi_mesin']);
    updateNilai($id, 3, $data['kondisi_bodi']);
    updateNilai($id, 4, $tahun);
    updateNilai($id, 5, $jarak); // Jarak tempuh dipastikan bulat
    updateNilai($id, 6, $data['kelengkapan_surat']);
    
    $nilai_pajak = ($pajak == 'Hidup') ? 5 : 3;
    updateNilai($id, 7, $nilai_pajak);

    return true; 
}

function simpanNilai($id_mobil, $id_kriteria, $nilai) {
    global $conn;
    $query = "INSERT INTO tb_penilaian VALUES (NULL, $id_mobil, $id_kriteria, '$nilai')";
    mysqli_query($conn, $query);
}

function updateNilai($id_mobil, $id_kriteria, $nilai) {
    global $conn;
    $query = "UPDATE tb_penilaian SET nilai = '$nilai' WHERE id_mobil = $id_mobil AND id_kriteria = $id_kriteria";
    mysqli_query($conn, $query);
}

function hapusMobil($id) {
    global $conn;
    mysqli_query($conn, "DELETE FROM tb_alternatif WHERE id_mobil = $id");
    return mysqli_affected_rows($conn);
}

function upload() {
    $namaFile   = $_FILES['foto']['name'];
    $tmpName    = $_FILES['foto']['tmp_name'];
    $error      = $_FILES['foto']['error'];

    if ($error === 4) return false;

    $ekstensiValid = ['jpg', 'jpeg', 'png'];
    $ekstensi      = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    if (!in_array($ekstensi, $ekstensiValid)) return false;

    $namaBaru = uniqid() . '.' . $ekstensi;
    move_uploaded_file($tmpName, '../foto_mobil/' . $namaBaru);
    return $namaBaru;
}

function hitungMOORA() {
    global $conn;
    
    $kriteria   = query("SELECT * FROM tb_kriteria ORDER BY id_kriteria ASC");
    $alternatif = query("SELECT * FROM tb_alternatif");

    if (empty($alternatif)) return [];

    $matriks_x = [];
    foreach ($alternatif as $alt) {
        $id_m = $alt['id_mobil'];
        $penilaian = query("SELECT * FROM tb_penilaian WHERE id_mobil = $id_m");
        foreach ($penilaian as $val) {
            $matriks_x[$id_m][$val['id_kriteria']] = $val['nilai'];
        }
    }

    $pembagi = [];
    foreach ($kriteria as $k) {
        $id_k = $k['id_kriteria'];
        $sum_kuadrat = 0;
        foreach ($alternatif as $alt) {
            $nilai = $matriks_x[$alt['id_mobil']][$id_k] ?? 0;
            $sum_kuadrat += pow($nilai, 2);
        }
        $pembagi[$id_k] = sqrt($sum_kuadrat);
    }

    $normalisasi = [];
    $ranking = [];
    foreach ($alternatif as $alt) {
        $id_m = $alt['id_mobil'];
        $total_benefit = 0;
        $total_cost = 0;

        foreach ($kriteria as $k) {
            $id_k = $k['id_kriteria'];
            $nilai_pembagi = ($pembagi[$id_k] == 0) ? 1 : $pembagi[$id_k];
            $r = ($matriks_x[$id_m][$id_k] ?? 0) / $nilai_pembagi;
            $normalisasi[$id_m][$id_k] = $r; 
            $weighted = $r * $k['bobot'];

            if ($k['atribut'] == 'benefit') {
                $total_benefit += $weighted;
            } else {
                $total_cost += $weighted;
            }
        }

        $nilai_akhir = $total_benefit - $total_cost;

        $ranking[] = [
            'id_mobil'    => $id_m,
            'nama_mobil'  => $alt['nama_mobil'],
            'foto'        => $alt['foto_mobil'],
            'harga'       => $alt['harga_asli'],
            'nilai_akhir' => $nilai_akhir,
            'data'        => $alt
        ];
    }

    usort($ranking, function($a, $b) {
        return $b['nilai_akhir'] <=> $a['nilai_akhir'];
    });

    return [
        'kriteria'    => $kriteria,
        'alternatif'  => $alternatif,
        'matriks_x'   => $matriks_x,
        'pembagi'     => $pembagi,
        'normalisasi' => $normalisasi,
        'ranking'     => $ranking
    ];
}

function cariMobil($id) {
    $m = query("SELECT * FROM tb_alternatif WHERE id_mobil = $id");
    if(empty($m)) return false;
    $res = $m[0];
    $res['nilai_mesin'] = query("SELECT nilai FROM tb_penilaian WHERE id_mobil=$id AND id_kriteria=2")[0]['nilai'] ?? 0;
    $res['nilai_bodi']  = query("SELECT nilai FROM tb_penilaian WHERE id_mobil=$id AND id_kriteria=3")[0]['nilai'] ?? 0;
    $res['nilai_surat'] = query("SELECT nilai FROM tb_penilaian WHERE id_mobil=$id AND id_kriteria=6")[0]['nilai'] ?? 0;
    return $res;
}

function ubahKriteria($data) {
    global $conn;
    $id = $data['id_kriteria'];
    $nama = mysqli_real_escape_string($conn, $data['nama_kriteria']);
    $atr = $data['atribut'];
    $bbt = $data['bobot'];
    mysqli_query($conn, "UPDATE tb_kriteria SET nama_kriteria='$nama', atribut='$atr', bobot='$bbt' WHERE id_kriteria=$id");
    return mysqli_affected_rows($conn);
}

function cariKriteria($id) {
    global $conn;
    return mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_kriteria WHERE id_kriteria=$id"));
}
?>