-- phpMyAdmin SQL Dump
-- Version: 5.2.0
-- Host: 127.0.0.1
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `db_spkmobilindo`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_admin`
--

CREATE TABLE `tb_admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_admin`
-- Password default: admin123 (menggunakan MD5)
--

INSERT INTO `tb_admin` (`id_admin`, `username`, `password`, `nama_lengkap`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'Admin Sadulur Mobilindo');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kriteria`
-- Sesuai Proposal Skripsi: 7 Kriteria
--

CREATE TABLE `tb_kriteria` (
  `id_kriteria` int(11) NOT NULL,
  `kode_kriteria` varchar(5) NOT NULL,
  `nama_kriteria` varchar(100) NOT NULL,
  `atribut` enum('benefit','cost') NOT NULL,
  `bobot` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_kriteria`
-- Bobot awal diset rata/dummy, nanti bisa diedit di halaman Admin
--

INSERT INTO `tb_kriteria` (`id_kriteria`, `kode_kriteria`, `nama_kriteria`, `atribut`, `bobot`) VALUES
(1, 'C1', 'Harga', 'cost', 0.25),
(2, 'C2', 'Kondisi Mesin', 'benefit', 0.15),
(3, 'C3', 'Kondisi Bodi', 'benefit', 0.10),
(4, 'C4', 'Tahun Produksi', 'benefit', 0.15),
(5, 'C5', 'Jarak Tempuh (KM)', 'cost', 0.15),
(6, 'C6', 'Kelengkapan Surat', 'benefit', 0.10),
(7, 'C7', 'Jenis Transmisi', 'benefit', 0.10);

-- --------------------------------------------------------

--
-- Table structure for table `tb_alternatif`
-- Tabel ini menyimpan data Mobil Bekas (Stok Showroom)
--

CREATE TABLE `tb_alternatif` (
  `id_mobil` int(11) NOT NULL,
  `nama_mobil` varchar(100) NOT NULL,
  `merek` varchar(50) NOT NULL,
  `harga_asli` double NOT NULL,
  `tahun` int(4) NOT NULL,
  `jarak_tempuh` int(11) NOT NULL,
  `transmisi` enum('Manual','Matic') NOT NULL,
  `deskripsi` text NOT NULL,
  `foto_mobil` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_alternatif` (Data Dummy untuk Test)
--

INSERT INTO `tb_alternatif` (`id_mobil`, `nama_mobil`, `merek`, `harga_asli`, `tahun`, `jarak_tempuh`, `transmisi`, `deskripsi`, `foto_mobil`) VALUES
(1, 'Toyota Avanza G', 'Toyota', 120000000, 2018, 50000, 'Manual', 'Kondisi mulus, pajak hidup.', 'avanza.jpg'),
(2, 'Honda Jazz RS', 'Honda', 180000000, 2019, 35000, 'Matic', 'Siap pakai, service record rutin.', 'jazz.jpg'),
(3, 'Daihatsu Xenia Xi', 'Daihatsu', 95000000, 2015, 80000, 'Manual', 'Mesin kering, kaki-kaki empuk.', 'xenia.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tb_penilaian`
-- Tabel ini menyimpan Nilai Konversi untuk perhitungan MOORA
-- Menghubungkan Mobil (Alternatif) dengan Kriteria
--

CREATE TABLE `tb_penilaian` (
  `id_penilaian` int(11) NOT NULL,
  `id_mobil` int(11) NOT NULL,
  `id_kriteria` int(11) NOT NULL,
  `nilai` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_penilaian`
-- Contoh penilaian untuk 3 mobil di atas terhadap 7 kriteria
--

INSERT INTO `tb_penilaian` (`id_penilaian`, `id_mobil`, `id_kriteria`, `nilai`) VALUES
-- Nilai Toyota Avanza (ID 1)
(1, 1, 1, 120000000), -- C1 Harga
(2, 1, 2, 4),         -- C2 Mesin (Skala 1-5, misal 4=Baik)
(3, 1, 3, 4),         -- C3 Bodi
(4, 1, 4, 2018),      -- C4 Tahun
(5, 1, 5, 50000),     -- C5 KM
(6, 1, 6, 5),         -- C6 Surat (5=Lengkap)
(7, 1, 7, 3),         -- C7 Transmisi (3=Manual, asumsi Matic=5)

-- Nilai Honda Jazz (ID 2)
(8, 2, 1, 180000000),
(9, 2, 2, 5),
(10, 2, 3, 5),
(11, 2, 4, 2019),
(12, 2, 5, 35000),
(13, 2, 6, 5),
(14, 2, 7, 5),        -- Matic diberi nilai 5

-- Nilai Daihatsu Xenia (ID 3)
(15, 3, 1, 95000000),
(16, 3, 2, 3),
(17, 3, 3, 3),
(18, 3, 4, 2015),
(19, 3, 5, 80000),
(20, 3, 6, 4),
(21, 3, 7, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `tb_kriteria`
--
ALTER TABLE `tb_kriteria`
  ADD PRIMARY KEY (`id_kriteria`);

--
-- Indexes for table `tb_alternatif`
--
ALTER TABLE `tb_alternatif`
  ADD PRIMARY KEY (`id_mobil`);

--
-- Indexes for table `tb_penilaian`
--
ALTER TABLE `tb_penilaian`
  ADD PRIMARY KEY (`id_penilaian`),
  ADD KEY `id_mobil` (`id_mobil`),
  ADD KEY `id_kriteria` (`id_kriteria`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_admin`
--
ALTER TABLE `tb_admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_kriteria`
--
ALTER TABLE `tb_kriteria`
  MODIFY `id_kriteria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tb_alternatif`
--
ALTER TABLE `tb_alternatif`
  MODIFY `id_mobil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_penilaian`
--
ALTER TABLE `tb_penilaian`
  MODIFY `id_penilaian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_penilaian`
--
ALTER TABLE `tb_penilaian`
  ADD CONSTRAINT `tb_penilaian_ibfk_1` FOREIGN KEY (`id_mobil`) REFERENCES `tb_alternatif` (`id_mobil`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_penilaian_ibfk_2` FOREIGN KEY (`id_kriteria`) REFERENCES `tb_kriteria` (`id_kriteria`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;