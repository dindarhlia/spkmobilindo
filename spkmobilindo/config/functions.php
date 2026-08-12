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

// =========================================================================
// MESIN KONVERSI OTOMATIS (Menerjemahkan Angka Riil ke Skala 1-5 MOORA)
// =========================================================================

function konversiNilaiHarga($harga) {
    if ($harga < 100000000) return 1;
    if ($harga >= 100000000 && $harga <= 149999999) return 2;
    if ($harga >= 150000000 && $harga <= 299999999) return 3;
    if ($harga >= 300000000 && $harga <= 400000000) return 4;
    if ($harga > 400000000) return 5;
    return 1; 
}

function konversiNilaiTahun($tahun) {
    if ($tahun > 2018) return 5;
    if ($tahun >= 2014 && $tahun <= 2018) return 4;
    if ($tahun >= 2009 && $tahun <= 2013) return 3;
    if ($tahun >= 2004 && $tahun <= 2008) return 2;
    if ($tahun < 2004) return 1;
    return 1;
}

function konversiNilaiKM($km) {
    if ($km < 50000) return 1;
    if ($km >= 50000 && $km <= 99999) return 2;
    if ($km >= 100000 && $km <= 149999) return 3;
    if ($km >= 150000 && $km <= 199999) return 4;
    if ($km >= 200000) return 5;
    return 5; 
}

function konversiNilaiPajak($status_pajak) {
    if ($status_pajak == 'Hidup Panjang (> 6 Bulan)') return 5;
    if ($status_pajak == 'Hidup Pendek (< 6 Bulan)') return 4;
    if ($status_pajak == 'Telat / Mati 1 - 2 Tahun') return 3;
    if ($status_pajak == 'Mati 3 - 4 Tahun') return 2;
    if ($status_pajak == 'Mati > 4 Tahun') return 1;
    return 3; 
}

// [REVISI] Fungsi Baru untuk Konversi Merek Mobil
function konversiNilaiMerek($merek) {
    // Ubah ke huruf kecil dan hilangkan spasi berlebih agar pencocokan akurat
    $m = strtolower(trim($merek)); 
    if (in_array($m, ['toyota', 'honda'])) return 5;
    if (in_array($m, ['daihatsu', 'suzuki', 'mitsubishi'])) return 4;
    if (in_array($m, ['nissan', 'mazda', 'wuling'])) return 3;
    if (in_array($m, ['chevrolet', 'ford', 'hyundai'])) return 2;
    return 1; // Merek lainnya
}

// Fungsi bantu untuk memastikan KM dan Harga terhindar dari error desimal
function bersihkanAngka($angka) {
    return preg_replace("/[^0-9]/", "", (string)$angka);
}

// =========================================================================
// FUNGSI CRUD DATA MOBIL
// =========================================================================

function upload() {
    $namaFile   = $_FILES['foto']['name'];
    $tmpName    = $_FILES['foto']['tmp_name'];
    $error      = $_FILES['foto']['error'];

    // Jika admin sengaja tidak upload foto (error 4)
    if ($error === 4) return ''; 
    
    // Jika ada error upload (misal ukuran melebih batas maksimal server)
    if ($error !== 0) return false;

    $ekstensiValid = ['jpg', 'jpeg', 'png'];
    $ekstensiParts = explode('.', $namaFile);
    $ekstensi      = strtolower(end($ekstensiParts));

    if (!in_array($ekstensi, $ekstensiValid)) return false;

    $namaBaru = uniqid() . '.' . $ekstensi;
    move_uploaded_file($tmpName, '../foto_mobil/' . $namaBaru);
    return $namaBaru;
}

function tambahMobil($data) {
    global $conn;

    $nama_mobil = mysqli_real_escape_string($conn, $data['nama_mobil']);
    $merek      = mysqli_real_escape_string($conn, $data['merek']);
    $deskripsi  = mysqli_real_escape_string($conn, $data['deskripsi']);
    $pajak      = mysqli_real_escape_string($conn, $data['pajak_kendaraan'] ?? ''); 
    
    $harga = bersihkanAngka($data['harga_asli']); 
    $tahun = htmlspecialchars($data['tahun']);
    $jarak = bersihkanAngka($data['jarak_tempuh']); 

    $foto = upload();
    if ($foto === false) return -1; 

    $query_mobil = "INSERT INTO tb_alternatif 
                    (nama_mobil, merek, harga_asli, tahun, jarak_tempuh, pajak_kendaraan, deskripsi, foto_mobil) 
                    VALUES 
                    ('$nama_mobil', '$merek', '$harga', '$tahun', '$jarak', '$pajak', '$deskripsi', '$foto')";
    
    $insert_berhasil = mysqli_query($conn, $query_mobil);
    if (!$insert_berhasil) return 0; 
    
    $id_baru = mysqli_insert_id($conn);

    // Otomatisasi konversi riil ke 1-5
    $nilai_harga = konversiNilaiHarga($harga);
    $nilai_tahun = konversiNilaiTahun($tahun);
    $nilai_jarak = konversiNilaiKM($jarak);
    $nilai_pajak = konversiNilaiPajak($pajak);
    $nilai_merek = konversiNilaiMerek($merek); // [REVISI] Generate nilai merek

    // Simpan nilai matriks
    simpanNilai($id_baru, 1, $nilai_harga); 
    simpanNilai($id_baru, 2, $data['kondisi_mesin']); 
    simpanNilai($id_baru, 3, $data['kondisi_bodi']);  
    simpanNilai($id_baru, 4, $nilai_tahun); 
    simpanNilai($id_baru, 5, $nilai_jarak); 
    simpanNilai($id_baru, 6, $data['kelengkapan_surat']); 
    simpanNilai($id_baru, 7, $nilai_pajak); 
    simpanNilai($id_baru, 8, $nilai_merek); // [REVISI] Insert ID Kriteria 8

    return 1;
}

function ubahMobil($data) {
    global $conn;

    $id         = $data['id_mobil'];
    $nama_mobil = mysqli_real_escape_string($conn, $data['nama_mobil']);
    $merek      = mysqli_real_escape_string($conn, $data['merek']);
    $deskripsi  = mysqli_real_escape_string($conn, $data['deskripsi']);
    $pajak      = mysqli_real_escape_string($conn, $data['pajak_kendaraan'] ?? ''); 
    $gambarLama = $data['gambarLama'];

    $harga = bersihkanAngka($data['harga_asli']);
    $tahun = htmlspecialchars($data['tahun']);
    $jarak = bersihkanAngka($data['jarak_tempuh']);

    if ($_FILES['foto']['error'] === 4) {
        $foto = $gambarLama;
    } else {
        $foto = upload();
        if ($foto === false) return -1; 
    }

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
    
    $update_berhasil = mysqli_query($conn, $query);
    if (!$update_berhasil) return 0;

    $nilai_harga = konversiNilaiHarga($harga);
    $nilai_tahun = konversiNilaiTahun($tahun);
    $nilai_jarak = konversiNilaiKM($jarak);
    $nilai_pajak = konversiNilaiPajak($pajak);
    $nilai_merek = konversiNilaiMerek($merek); // [REVISI] Generate nilai merek update

    updateNilai($id, 1, $nilai_harga);
    updateNilai($id, 2, $data['kondisi_mesin']);
    updateNilai($id, 3, $data['kondisi_bodi']);
    updateNilai($id, 4, $nilai_tahun);
    updateNilai($id, 5, $nilai_jarak); 
    updateNilai($id, 6, $data['kelengkapan_surat']);
    updateNilai($id, 7, $nilai_pajak);
    updateNilai($id, 8, $nilai_merek); // [REVISI] Update ID Kriteria 8

    return 1; 
}

function simpanNilai($id_mobil, $id_kriteria, $nilai) {
    global $conn;
    $query = "INSERT INTO tb_penilaian (id_mobil, id_kriteria, nilai) VALUES ($id_mobil, $id_kriteria, '$nilai')";
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

// =========================================================================
// FUNGSI MOORA SPK - UNTUK ADMIN (BOBOT DATABASE)
// =========================================================================

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

// =========================================================================
// FUNGSI MOORA SPK - KHUSUS PUBLIK / SIMULASI (BOBOT DINAMIS & PRE-FILTER)
// =========================================================================

// [REVISI] Menambahkan parameter $keyword opsional untuk Pre-Filter Free Text
function hitungMOORA_publik($bobot_dinamis, $keyword = "") {
    global $conn;
    
    $kriteria = query("SELECT * FROM tb_kriteria ORDER BY id_kriteria ASC");
    
    // [REVISI] Memfilter alternatif menggunakan LIKE jika user mengisi Free Text
    if (!empty($keyword)) {
        $kw = mysqli_real_escape_string($conn, $keyword);
        $alternatif = query("SELECT * FROM tb_alternatif WHERE nama_mobil LIKE '%$kw%' OR deskripsi LIKE '%$kw%'");
    } else {
        $alternatif = query("SELECT * FROM tb_alternatif");
    }

    // Jika filter text tidak menemukan satupun mobil, hentikan perhitungan
    if (empty($alternatif)) return [];

    // 1. Susun Matriks Keputusan (X) dari tb_penilaian
    $matriks_x = [];
    foreach ($alternatif as $alt) {
        $id_m = $alt['id_mobil'];
        $penilaian = query("SELECT * FROM tb_penilaian WHERE id_mobil = $id_m");
        foreach ($penilaian as $val) {
            $matriks_x[$id_m][$val['id_kriteria']] = $val['nilai'];
        }
    }

    // 2. Hitung Nilai Pembagi
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

    // 3. Matriks Ternormalisasi Berbobot (R * W) & Optimasi (Yi)
    $ranking = [];
    foreach ($alternatif as $alt) {
        $id_m = $alt['id_mobil'];
        $total_benefit = 0;
        $total_cost = 0;

        foreach ($kriteria as $k) {
            $id_k = $k['id_kriteria'];
            $nilai_pembagi = ($pembagi[$id_k] == 0) ? 1 : $pembagi[$id_k];
            
            // Proses Normalisasi
            $r = ($matriks_x[$id_m][$id_k] ?? 0) / $nilai_pembagi;
            
            // INJEKSI BOBOT DINAMIS: Menggunakan bobot hasil normalisasi inputan user
            $bobot_aktif = $bobot_dinamis[$id_k] ?? 0;
            $weighted = $r * $bobot_aktif;

            // Penentuan Klasifikasi Atribut (Benefit / Cost)
            $atribut = strtolower($k['atribut']);
            
            // STRICT RULE: Klasifikasi mutlak untuk Pajak Kendaraan (7) sebagai Benefit
            // dan (Opsional) Merek Mobil (8) sebagai Benefit jika belum diset di Database
            if ($id_k == 7 || $id_k == 8) {
                $atribut = 'benefit'; 
            }

            if ($atribut == 'benefit') {
                $total_benefit += $weighted;
            } else {
                $total_cost += $weighted;
            }
        }

        // 4. Hitung Nilai Akhir
        $nilai_akhir = $total_benefit - $total_cost;

        $ranking[] = [
            'id_mobil'    => $id_m,
            'nama_mobil'  => $alt['nama_mobil'],
            'foto'        => $alt['foto_mobil'],
            'harga'       => $alt['harga_asli'],
            'nilai_akhir' => $nilai_akhir,
            'skor'        => $nilai_akhir,
            'data'        => $alt
        ];
    }

    // 5. Perangkingan
    usort($ranking, function($a, $b) {
        return $b['nilai_akhir'] <=> $a['nilai_akhir'];
    });

    return $ranking;
}

// =========================================================================
// UTILITY FUNCTIONS
// =========================================================================

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