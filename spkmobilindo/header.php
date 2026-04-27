<?php
// File: header.php
require_once 'config/functions.php'; // Pastikan functions.php terpanggil di setiap halaman
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sadulur Mobilindo - SPK Mobil Bekas</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="/spkmobilindo/assets/css/custom.css" rel="stylesheet"/>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            height: 80vh;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .card-mobil { transition: transform 0.3s; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .card-mobil:hover { transform: translateY(-10px); }
        .navbar-brand { font-weight: 700; color: #0d6efd !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="/spkmobilindo/index.php">
        <i class="bi bi-car-front-fill"></i> SADULUR MOBILINDO
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="/spkmobilindo/index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="/spkmobilindo/katalog.php">Daftar Mobil</a></li>
        <li class="nav-item"><a class="nav-link fw-bold text-primary" href="/spkmobilindo/simulasi.php">Rekomendasi (SPK)</a></li>
        <li class="nav-item ms-3">
            <a class="btn btn-outline-dark btn-sm mt-1" href="/spkmobilindo/admin/login.php">Login Staff</a>
        </li>
      </ul>
    </div>
  </div>
</nav>