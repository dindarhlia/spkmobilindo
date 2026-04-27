<?php
/**
 * File: config/koneksi.php
 * Fungsi: Menghubungkan script PHP dengan Database MySQL
 */

// Konfigurasi Database
$host = "localhost";
$user = "root";      // Default user XAMPP/Laragon
$pass = "";          // Kosongkan jika tidak mengatur password database
$db   = "db_spkmobilindo"; // Nama database Anda

// Melakukan koneksi menggunakan MySQLi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek apakah koneksi berhasil
if (!$conn) {
    // Memberikan pesan error yang lebih spesifik untuk memudahkan perbaikan di laptop baru
    die("Koneksi Database Gagal! Periksa apakah MySQL di XAMPP sudah menyala dan database '" . $db . "' sudah di-import. Error: " . mysqli_connect_error());
}

// Set charset ke utf8 agar pembacaan karakter simbol (seperti Rp) tidak berantakan
mysqli_set_charset($conn, "utf8");
?>