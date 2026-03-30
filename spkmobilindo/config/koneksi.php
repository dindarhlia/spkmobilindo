<?php
/**
 * File: config/koneksi.php
 * Fungsi: Menghubungkan script PHP dengan Database MySQL
 */

// Konfigurasi Database
// Sesuaikan dengan settingan XAMPP/Laragon anda
$host = "localhost";
$user = "root";      // Default user XAMPP biasanya 'root'
$pass = "";          // Default password XAMPP biasanya kosong
$db   = "db_spkmobilindo"; // Nama database yang tadi kita buat

// Melakukan koneksi menggunakan MySQLi (Style Procedural)
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek apakah koneksi berhasil
if (!$conn) {
    // Jika gagal, matikan proses dan tampilkan pesan error
    // Ini penting untuk debugging saat development
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

// Jika file ini diakses langsung dan layar putih (blank), 
// berarti koneksi BERHASIL.
?>