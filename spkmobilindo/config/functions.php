<?php
/**
 * File: config/functions.php
 * Deskripsi: Memuat semua fungsi CRUD Database dan Logika Perhitungan MOORA
 */

// Hubungkan ke database
require 'koneksi.php';

// ============================================================
// BAGIAN 1: FUNGSI CRUD (CREATE, READ, UPDATE, DELETE) UMUM
// ============================================================

// Fungsi untuk mengambil data (SELECT) dan mengubahnya jadi Array Associative
function query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Fungsi Tambah Mobil Baru
function tambahMobil($data) {
    global $conn;

    // 1. Ambil data dari form
    $nama_mobil = htmlspecialchars($data['nama_mobil']);
    $merek = htmlspecialchars($data['merek']);
    $harga = htmlspecialchars($data['harga_asli']); // Input Harga (Angka)
    $tahun = htmlspecialchars($data['tahun']);
    $jarak = htmlspecialchars($data['jarak_tempuh']);
    $transmisi = htmlspecialchars($data['transmisi']); // Manual/Matic
    $deskripsi = htmlspecialchars($data['deskripsi']);

    // 2. Upload Gambar
    $foto = upload();
    if (!$foto) {
        return false; // Gagal upload
    }

    // 3. Masukkan ke tabel tb_alternatif
    $query_mobil = "INSERT INTO tb_alternatif 
                    VALUES (NULL, '$nama_mobil', '$merek', '$harga', '$tahun', '$jarak', '$transmisi', '$deskripsi', '$foto')";
    
    mysqli_query($conn, $query_mobil);
    
    // Ambil ID mobil yang baru saja dibuat
    $id_mobil_baru = mysqli_insert_id($conn);

    // 4. Otomatis isi tb_penilaian dengan Konversi Nilai untuk MOORA
    // Kita harus memetakan input form ke 7 Kriteria (C1 - C7)
    // Asumsi ID Kriteria di DB urut 1-7 sesuai db_spkmobilindo.sql
    
    // C1: Harga (Ambil langsung angka)
    simpanNilai($id_mobil_baru, 1, $harga);
    
    // C2: Kondisi Mesin (Inputan user berupa angka 1-5)
    simpanNilai($id_mobil_baru, 2, $data['kondisi_mesin']);
    
    // C3: Kondisi Bodi (Inputan user berupa angka 1-5)
    simpanNilai($id_mobil_baru, 3, $data['kondisi_bodi']);
    
    // C4: Tahun (Ambil langsung angka tahun)
    simpanNilai($id_mobil_baru, 4, $tahun);
    
    // C5: Jarak Tempuh (Ambil langsung angka KM)
    simpanNilai($id_mobil_baru, 5, $jarak);
    
    // C6: Kelengkapan Surat (Inputan user 1-5)
    simpanNilai($id_mobil_baru, 6, $data['kelengkapan_surat']);
    
    // C7: Transmisi (Konversi Text ke Angka)
    // Matic = 5, Manual = 3 (Bisa disesuaikan kebijakannya)
    $nilai_transmisi = ($transmisi == 'Matic') ? 5 : 3;
    simpanNilai($id_mobil_baru, 7, $nilai_transmisi);

    return mysqli_affected_rows($conn);
}

// Helper untuk insert ke tb_penilaian
function simpanNilai($id_mobil, $id_kriteria, $nilai) {
    global $conn;
    $query = "INSERT INTO tb_penilaian VALUES (NULL, $id_mobil, $id_kriteria, '$nilai')";
    mysqli_query($conn, $query);
}

// Fungsi Hapus Mobil
function hapusMobil($id) {
    global $conn;
    // Hapus data (tb_penilaian akan terhapus otomatis karena Cascade di database)
    mysqli_query($conn, "DELETE FROM tb_alternatif WHERE id_mobil = $id");
    return mysqli_affected_rows($conn);
}

// Fungsi Upload Gambar
function upload() {
    $namaFile = $_FILES['foto']['name'];
    $ukuranFile = $_FILES['foto']['size'];
    $error = $_FILES['foto']['error'];
    $tmpName = $_FILES['foto']['tmp_name'];

    // Cek apakah ada gambar yang diupload
    if ($error === 4) {
        echo "<script>alert('Pilih gambar terlebih dahulu!');</script>";
        return false;
    }

    // Cek ekstensi gambar
    $ekstensiGambarValid = ['jpg', 'jpeg', 'png'];
    $ekstensiGambar = explode('.', $namaFile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));

    if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
        echo "<script>alert('Yang anda upload bukan gambar!');</script>";
        return false;
    }

    // Generate nama baru agar tidak duplikat
    $namaFileBaru = uniqid();
    $namaFileBaru .= '.';
    $namaFileBaru .= $ekstensiGambar;

    // Pindahkan file ke folder foto_mobil
    // Pastikan folder ini ada di root project (sejajar folder config)
    move_uploaded_file($tmpName, '../foto_mobil/' . $namaFileBaru);

    return $namaFileBaru;
}


// ============================================================
// BAGIAN 2: LOGIKA METODE MOORA (CORE ALGORITHM)
// ============================================================

/**
 * Fungsi Utama: hitungMOORA()
 * Mengembalikan array lengkap berisi data mentah, normalisasi, dan ranking akhir.
 */
function hitungMOORA() {
    global $conn;

    // 1. Ambil Data Kriteria & Bobot
    $kriteria = query("SELECT * FROM tb_kriteria ORDER BY id_kriteria ASC");
    
    // 2. Ambil Data Alternatif (Mobil)
    $alternatif = query("SELECT * FROM tb_alternatif");

    // Jika data kosong, kembalikan array kosong
    if (empty($alternatif)) return [];

    // 3. Persiapan Matriks Keputusan (X)
    // Kita butuh array [id_mobil][id_kriteria] => nilai
    $matriks_x = [];
    foreach ($alternatif as $alt) {
        $id_mobil = $alt['id_mobil'];
        $penilaian = query("SELECT * FROM tb_penilaian WHERE id_mobil = $id_mobil");
        
        foreach ($penilaian as $nilai) {
            $matriks_x[$id_mobil][$nilai['id_kriteria']] = $nilai['nilai'];
        }
    }

    // 4. Menghitung Pembagi Normalisasi (Akar dari Penjumlahan Kuadrat)
    // Rumus: sqrt(sum(x^2)) per kriteria
    $pembagi = [];
    foreach ($kriteria as $k) {
        $id_k = $k['id_kriteria'];
        $sum_kuadrat = 0;
        
        foreach ($alternatif as $alt) {
            $id_m = $alt['id_mobil'];
            $nilai = $matriks_x[$id_m][$id_k] ?? 0; // Pakai 0 jika null
            $sum_kuadrat += pow($nilai, 2);
        }
        
        $pembagi[$id_k] = sqrt($sum_kuadrat);
    }

    // 5. Normalisasi Matriks (R) & Normalisasi Terbobot
    // Rumus Normalisasi: Xij / Pembagi
    // Rumus Terbobot: Normalisasi * Bobot
    $matriks_ternormalisasi = [];
    $nilai_optimasi = []; // Yi

    foreach ($alternatif as $alt) {
        $id_m = $alt['id_mobil'];
        $total_benefit = 0;
        $total_cost = 0;

        foreach ($kriteria as $k) {
            $id_k = $k['id_kriteria'];
            $type = $k['atribut']; // benefit atau cost
            $bobot = $k['bobot'];
            
            // Cegah division by zero
            $nilai_pembagi = ($pembagi[$id_k] == 0) ? 1 : $pembagi[$id_k];
            
            // Hitung Normalisasi
            $raw_value = $matriks_x[$id_m][$id_k] ?? 0;
            $r_value = $raw_value / $nilai_pembagi;
            
            // Simpan data normalisasi (untuk ditampilkan di laporan/skripsi)
            $matriks_ternormalisasi[$id_m][$id_k] = $r_value;

            // Hitung Terbobot langsung untuk Yi
            $weighted_value = $r_value * $bobot;

            // Pisahkan Benefit dan Cost
            if ($type == 'benefit') {
                $total_benefit += $weighted_value;
            } else {
                $total_cost += $weighted_value; // Cost dijumlahkan dulu
            }
        }

        // 6. Hitung Nilai Akhir (Yi)
        // Rumus: Max(Benefit) - Min(Cost)
        $yi = $total_benefit - $total_cost;
        
        $nilai_optimasi[] = [
            'id_mobil' => $id_m,
            'nama_mobil' => $alt['nama_mobil'],
            'foto' => $alt['foto_mobil'],
            'harga' => $alt['harga_asli'],
            'nilai_akhir' => $yi
        ];
    }

    // 7. Perangkingan (Sort Descending berdasarkan Nilai Akhir Yi)
    usort($nilai_optimasi, function($a, $b) {
        return $b['nilai_akhir'] <=> $a['nilai_akhir'];
    });

    return [
        'kriteria' => $kriteria,
        'alternatif' => $alternatif,
        'matriks_x' => $matriks_x, // Data mentah
        'pembagi' => $pembagi, // Nilai akar pembagi
        'normalisasi' => $matriks_ternormalisasi, // Matriks R
        'ranking' => $nilai_optimasi // Hasil Akhir
    ];
}




// --- FITUR EDIT/UPDATE ---

// Fungsi Ubah Mobil
function ubahMobil($data) {
    global $conn;

    $id = $data['id_mobil'];
    $nama_mobil = htmlspecialchars($data['nama_mobil']);
    $merek = htmlspecialchars($data['merek']);
    $harga = htmlspecialchars($data['harga_asli']);
    $tahun = htmlspecialchars($data['tahun']);
    $jarak = htmlspecialchars($data['jarak_tempuh']);
    $transmisi = htmlspecialchars($data['transmisi']);
    $deskripsi = htmlspecialchars($data['deskripsi']);
    $gambarLama = htmlspecialchars($data['gambarLama']);

    // Cek apakah user pilih gambar baru atau tidak
    if ($_FILES['foto']['error'] === 4) {
        $foto = $gambarLama;
    } else {
        $foto = upload();
    }

    // Update tabel tb_alternatif
    $query = "UPDATE tb_alternatif SET
                nama_mobil = '$nama_mobil',
                merek = '$merek',
                harga_asli = '$harga',
                tahun = '$tahun',
                jarak_tempuh = '$jarak',
                transmisi = '$transmisi',
                deskripsi = '$deskripsi',
                foto_mobil = '$foto'
              WHERE id_mobil = $id";
    
    mysqli_query($conn, $query);

    // Update tabel tb_penilaian (Nilai MOORA)
    // Kita update satu per satu sesuai ID Kriteria
    updateNilai($id, 1, $harga); // C1
    updateNilai($id, 2, $data['kondisi_mesin']); // C2
    updateNilai($id, 3, $data['kondisi_bodi']); // C3
    updateNilai($id, 4, $tahun); // C4
    updateNilai($id, 5, $jarak); // C5
    updateNilai($id, 6, $data['kelengkapan_surat']); // C6
    
    $nilai_transmisi = ($transmisi == 'Matic') ? 5 : 3;
    updateNilai($id, 7, $nilai_transmisi); // C7

    return mysqli_affected_rows($conn);
}

// Helper Update Nilai
function updateNilai($id_mobil, $id_kriteria, $nilai) {
    global $conn;
    $query = "UPDATE tb_penilaian SET nilai = '$nilai' WHERE id_mobil = $id_mobil AND id_kriteria = $id_kriteria";
    mysqli_query($conn, $query);
}

// Fungsi ambil data spesifik untuk form Edit
function cariMobil($id) {
    global $conn;
    // Ambil data mobil
    $mobil = query("SELECT * FROM tb_alternatif WHERE id_mobil = $id")[0];
    
    // Ambil nilai kriteria mobil tersebut untuk pre-fill form (Mesin, Bodi, Surat)
    // Kita butuh nilai spesifik untuk dropdown
    $nilai_mesin = query("SELECT nilai FROM tb_penilaian WHERE id_mobil = $id AND id_kriteria = 2")[0]['nilai'];
    $nilai_bodi = query("SELECT nilai FROM tb_penilaian WHERE id_mobil = $id AND id_kriteria = 3")[0]['nilai'];
    $nilai_surat = query("SELECT nilai FROM tb_penilaian WHERE id_mobil = $id AND id_kriteria = 6")[0]['nilai'];

    // Masukkan ke array mobil agar mudah diakses di form
    $mobil['nilai_mesin'] = $nilai_mesin;
    $mobil['nilai_bodi'] = $nilai_bodi;
    $mobil['nilai_surat'] = $nilai_surat;

    return $mobil;
}






// --- TAMBAHAN UNTUK FITUR UPDATE KRITERIA ---

// Fungsi Update Kriteria
function ubahKriteria($data) {
    global $conn;
    
    $id = $data['id_kriteria'];
    $nama = htmlspecialchars($data['nama_kriteria']);
    $atribut = htmlspecialchars($data['atribut']); // benefit atau cost
    $bobot = htmlspecialchars($data['bobot']);
    
    $query = "UPDATE tb_kriteria SET
                nama_kriteria = '$nama',
                atribut = '$atribut',
                bobot = '$bobot'
              WHERE id_kriteria = $id";
              
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

// Fungsi ambil 1 kriteria
function cariKriteria($id) {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM tb_kriteria WHERE id_kriteria = $id");
    return mysqli_fetch_assoc($result);
}

?>