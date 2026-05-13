<?php
/**
 * File: admin/header.php
 */

// Langkah 1: Cek apakah sesi sudah berjalan, jika belum baru jalankan session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Langkah 2: Panggil fungsi database
require_once '../config/functions.php'; 

// Langkah 3: Cek Keamanan Login
if (!isset($_SESSION['status_login']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Sadulur Mobilindo</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        .sidebar { height: 100vh; position: fixed; top: 0; left: 0; width: 250px; background-color: #212529; padding-top: 20px; z-index: 999; }
        .sidebar a { padding: 15px 25px; text-decoration: none; font-size: 16px; color: #b5b5b5; display: block; transition: 0.3s; }
        .sidebar a:hover { color: #fff; background-color: #343a40; }
        .sidebar a.active { background-color: #0d6efd; color: white; }
        .main-content { margin-left: 250px; padding: 30px; }
        @media (max-width: 768px) { .sidebar { width: 100%; height: auto; position: relative; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="text-center mb-4">
        <h5 class="text-white fw-bold">PANEL <?= strtoupper($_SESSION['role']); ?></h5>
        <small class="text-muted">Sadulur Mobilindo</small>
    </div>
    
    <a href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <a href="data_mobil.php"><i class="bi bi-car-front-fill me-2"></i> Data Mobil</a>
    <a href="data_kriteria.php"><i class="bi bi-list-task me-2"></i> Data Kriteria</a>
    <a href="hitung_spk.php"><i class="bi bi-calculator me-2"></i> Perhitungan SPK</a>
    <a href="laporan_cetak.php"><i class="bi bi-file-earmark-pdf me-2"></i> Laporan</a>
    
    <hr class="border-secondary mx-3">
    
    <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
</div>

<div class="main-content">