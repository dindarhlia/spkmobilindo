-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2026 at 09:06 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

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
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','owner') NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_admin`
--

INSERT INTO `tb_admin` (`id_admin`, `username`, `password`, `nama_lengkap`, `role`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'Admin Sadulur Mobilindo', 'admin'),
(2, 'owner', '5be057accb25758101fa5eadbbd79503', 'Owner Sadulur Mobilindo', 'owner');

-- --------------------------------------------------------

--
-- Table structure for table `tb_alternatif`
--

CREATE TABLE `tb_alternatif` (
  `id_mobil` int(11) NOT NULL,
  `nama_mobil` varchar(100) NOT NULL,
  `merek` varchar(50) NOT NULL,
  `harga_asli` bigint(20) DEFAULT NULL,
  `tahun` int(11) DEFAULT NULL,
  `jarak_tempuh` int(11) DEFAULT NULL,
  `pajak_kendaraan` varchar(50) DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `foto_mobil` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_alternatif`
--

INSERT INTO `tb_alternatif` (`id_mobil`, `nama_mobil`, `merek`, `harga_asli`, `tahun`, `jarak_tempuh`, `pajak_kendaraan`, `deskripsi`, `foto_mobil`) VALUES
(10, 'Nissan Grand Livina', 'Nissan', 115000000, 2014, 92500, 'Hidup Panjang (> 6 Bulan)', 'Varian Highway Star dengan bodi kit orisinal yang masih lengkap. Pajak hidup, interior bersih, dan performa mesin masih sangat stabil untuk pemakaian harian.', '69ef573c610cb.jpg'),
(11, 'Nissan Serena C26', 'Nissan', 135000000, 2013, 110200, 'Hidup Panjang (> 6 Bulan)', 'Varian Highway Star, Harga lebih ekonomis karena status pajak mati, sangat cocok untuk pembeli yang berniat melakukan balik nama langsung.', '69ef581849e90.jpg'),
(13, 'Honda CR-V Gen 2', 'Honda', 80000000, 2005, 185000, 'Hidup Pendek (< 6 Bulan)', 'SUV legendaris yang tangguh. Meskipun pajak mati dan kilometer sudah tinggi, kaki-kaki masih cukup nyaman. Harga disesuaikan agar terjangkau bagi pemula.', '69ef591c13c67.jpg'),
(14, 'Toyota Camry', 'Toyota', 120000000, 2010, 140000, 'Hidup Panjang (> 6 Bulan)', 'Menawarkan kenyamanan sedan kelas pejabat. Pajak hidup terjamin, mesin halus tanpa rembesan oli, dan semua fitur elektrikal kabin berfungsi dengan normal.', '69ef59a654a5a.jpg'),
(15, 'Nissan Grand Livina', 'Nissan', 98000000, 2013, 125400, 'Hidup Panjang (> 6 Bulan)', 'Varian SV Facelift dengan tampilan elegan warna hitam solid. Kondisi bodi sangat rapi, pajak hidup panjang (09.29), dan mesin tergolong sehat. Cocok untuk mobil operasional keluarga dengan harga di bawah 100 juta.', '69ef6432ec33e.jpg'),
(16, 'Honda Freed', 'Honda', 142000000, 2010, 115000, 'Hidup Panjang (> 6 Bulan)', 'Tipe PSD dengan pintu geser otomatis kiri-kanan berfungsi normal. Interior captain seat yang nyaman, pajak hidup (06.30), dan bodi abu-abu metalik yang masih orisinal.', '69ef64d548bd4.jpg'),
(17, 'Nissan X-Trail T30', 'Nissan', 75000000, 2007, 168000, 'Hidup Panjang (> 6 Bulan)', 'SUV tangguh dengan kenyamanan khas Nissan. Pajak hidup (10.29), bodi terlihat kokoh meskipun ada pemakaian wajar. Sangat bertenaga untuk penggunaan luar kota.', '69ef6d44143f9.jpg'),
(18, 'Chevrolet Sonic/Aveo', 'Chevrolet', 89000000, 2012, 98600, 'Hidup Panjang (> 6 Bulan)', 'Pajak hidup (08.28), bodi hitam mulus, dan interior yang kedap suara. Pilihan bagi yang mencari mobil kompak non-mainstream.', '69ef6b67d2914.jpg'),
(19, 'Nissan X-Trail T31', 'Nissan', 118000000, 2011, 132000, 'Telat / Mati 1 - 2 Tahun', 'Generasi T31 yang lebih modern dengan bagasi laci fungsional. Kondisi mesin halus, namun pajak mati. Unit sangat stabil dan nyaman dikendarai di berbagai medan.', '69ef6cf456656.jpg'),
(20, 'Daihatsu Xenia', 'Daihatsu', 105000000, 2014, 108000, 'Hidup Panjang (> 6 Bulan)', 'Mobil sejuta umat yang ekonomis. Pajak hidup, bodi perak metalik, perawatan sangat mudah dan murah.', '69ef6f3f7ac32.jpg'),
(21, 'Honda Jazz', 'Honda', 78000000, 2006, 155000, 'Hidup Panjang (> 6 Bulan)', 'Warna kuning mencolok, mesin VTEC responsif, bodi rapi. Harga ekonomis menyesuaikan status pajak.', '69ef6fc91e366.jpg'),
(22, 'Nissan X-Trail T31 - Merah', 'Nissan', 115000000, 2011, 135000, 'Hidup Panjang (> 6 Bulan)', 'SUV tangguh dengan warna merah yang jarang ada (rare). Dilengkapi aksesori lampu atap orisinal, bodi kekar, mesin bertenaga, dan pajak hidup (01.31). Sangat cocok untuk penggemar SUV bergaya petualang.', '69f056fa95a59.jpg'),
(23, 'Nissan Serena C24 - Putih', 'Nissan', 90000000, 2010, 145000, 'Hidup Panjang (> 6 Bulan)', 'Tipe Highway Star dengan pintu geser elektrik, interior mewah, pajak hidup (10.28), dan bodi putih mulus. Pilihan keluarga yang mengutamakan kenyamanan kabin.', '69f060fbd7004.jpg'),
(24, 'Ford EcoSport', 'Ford', 105000000, 2014, 98000, 'Hidup Panjang (> 6 Bulan)', 'Compact SUV dengan fitur melimpah di kelasnya. Varian tertinggi (Titanium) dengan ban cadangan \"konde\" di belakang, bodi rapi, pajak hidup (03.30), dan fitur sunroof orisinal yang masih berfungsi baik', '69f061ecd2fb1.jpg'),
(25, 'Nissan Grand Livina Facelift - Perak', 'Nissan', 110000000, 2012, 112000, 'Hidup Pendek (< 6 Bulan)', 'Model facelift dengan transmisi X-Tronic CVT yang halus. Warna perak metalik, bodi orisinal, interior rapi, dan pajak hidup (07.29). Mobil keluarga yang stabil dan irit bahan bakar.', '69f0627276b44.jpg'),
(26, 'Wuling Cortez - Cokelat', 'Wuling', 145000000, 2018, 75000, 'Hidup Panjang (> 6 Bulan)', 'MPV modern dengan fitur kelas atas. Kondisi bodi sangat mulus, kilometer tergolong rendah untuk tahunnya, dan pajak hidup (01.30)', '69f0655c75de4.jpg'),
(27, 'Mazda 2 R (SkyActiv)', 'Mazda', 165000000, 2013, 82000, 'Hidup Panjang (> 6 Bulan)', 'Hatchback premium dengan teknologi SkyActiv dan warna Soul Red khas Mazda. Kondisi sangat terawat, cat orisinal, interior mewah dengan fitur modern, dan pajak hidup (01.27).', '69f068841f681.jpg'),
(29, 'Honda CR-V Gen 2', 'Honda', 95000000, 2005, 174998, 'Hidup Panjang (> 6 Bulan)', 'Unit modifikasi eye-catching dengan cat bunglon (Chameleon Paint) hijau-ungu, pajak hidup (08.29), dan mesin 2.400cc yang responsif. Pilihan menarik bagi pecinta SUV yang ingin tampil beda.', '69f069d52be7f.jpg'),
(30, 'Honda CR-V 2.4', 'Honda', 175000000, 2012, 130000, 'Hidup Panjang (> 6 Bulan)', 'mesin terawat dan bertenaga', '6a3e3746cdf55.jpg'),
(32, 'Mitsubishi Pajero Sport', 'Mitsubishi', 448000000, 2022, 74090, 'Hidup Panjang (> 6 Bulan)', 'Kondisi bodi putih mulus, mesin diesel bertenaga, fitur keamanan aktif lengkap, dan pajak hidup (09.29). Unit tangguh yang siap digunakan di segala medan.', '6a41fb51ae4fe.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kriteria`
--

CREATE TABLE `tb_kriteria` (
  `id_kriteria` int(11) NOT NULL,
  `kode_kriteria` varchar(5) NOT NULL,
  `nama_kriteria` varchar(100) NOT NULL,
  `atribut` enum('benefit','cost') NOT NULL,
  `bobot` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kriteria`
--

INSERT INTO `tb_kriteria` (`id_kriteria`, `kode_kriteria`, `nama_kriteria`, `atribut`, `bobot`) VALUES
(1, 'C1', 'Harga', 'cost', 0.16),
(2, 'C2', 'Kondisi Mesin', 'benefit', 0.17),
(3, 'C3', 'Kondisi Bodi', 'benefit', 0.15),
(4, 'C4', 'Tahun Produksi', 'benefit', 0.09),
(5, 'C5', 'Jarak Tempuh (KM)', 'cost', 0.1),
(6, 'C6', 'Kelengkapan Surat', 'benefit', 0.18),
(7, 'C7', 'Pajak Kendaraan', 'benefit', 0.15);

-- --------------------------------------------------------

--
-- Table structure for table `tb_penilaian`
--

CREATE TABLE `tb_penilaian` (
  `id_penilaian` int(11) NOT NULL,
  `id_mobil` int(11) NOT NULL,
  `id_kriteria` int(11) NOT NULL,
  `nilai` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_penilaian`
--

INSERT INTO `tb_penilaian` (`id_penilaian`, `id_mobil`, `id_kriteria`, `nilai`) VALUES
(64, 10, 1, 2),
(65, 10, 2, 4),
(66, 10, 3, 4),
(67, 10, 4, 4),
(68, 10, 5, 2),
(69, 10, 6, 5),
(70, 10, 7, 5),
(71, 11, 1, 2),
(72, 11, 2, 3),
(73, 11, 3, 4),
(74, 11, 4, 3),
(75, 11, 5, 3),
(76, 11, 6, 5),
(77, 11, 7, 5),
(85, 13, 1, 1),
(86, 13, 2, 3),
(87, 13, 3, 3),
(88, 13, 4, 2),
(89, 13, 5, 4),
(90, 13, 6, 4),
(91, 13, 7, 4),
(92, 14, 1, 2),
(93, 14, 2, 4),
(94, 14, 3, 3),
(95, 14, 4, 3),
(96, 14, 5, 3),
(97, 14, 6, 5),
(98, 14, 7, 5),
(99, 15, 1, 1),
(100, 15, 2, 4),
(101, 15, 3, 4),
(102, 15, 4, 3),
(103, 15, 5, 3),
(104, 15, 6, 5),
(105, 15, 7, 5),
(106, 16, 1, 2),
(107, 16, 2, 4),
(108, 16, 3, 4),
(109, 16, 4, 3),
(110, 16, 5, 3),
(111, 16, 6, 5),
(112, 16, 7, 5),
(113, 17, 1, 1),
(114, 17, 2, 3),
(115, 17, 3, 3),
(116, 17, 4, 2),
(117, 17, 5, 4),
(118, 17, 6, 5),
(119, 17, 7, 5),
(120, 18, 1, 1),
(121, 18, 2, 4),
(122, 18, 3, 4),
(123, 18, 4, 3),
(124, 18, 5, 2),
(125, 18, 6, 5),
(126, 18, 7, 5),
(127, 19, 1, 2),
(128, 19, 2, 4),
(129, 19, 3, 4),
(130, 19, 4, 3),
(131, 19, 5, 3),
(132, 19, 6, 5),
(133, 19, 7, 3),
(134, 20, 1, 2),
(135, 20, 2, 4),
(136, 20, 3, 3),
(137, 20, 4, 4),
(138, 20, 5, 3),
(139, 20, 6, 5),
(140, 20, 7, 5),
(141, 21, 1, 1),
(142, 21, 2, 3),
(143, 21, 3, 4),
(144, 21, 4, 2),
(145, 21, 5, 4),
(146, 21, 6, 4),
(147, 21, 7, 5),
(148, 22, 1, 2),
(149, 22, 2, 4),
(150, 22, 3, 4),
(151, 22, 4, 3),
(152, 22, 5, 3),
(153, 22, 6, 5),
(154, 22, 7, 5),
(155, 23, 1, 1),
(156, 23, 2, 3),
(157, 23, 3, 4),
(158, 23, 4, 3),
(159, 23, 5, 3),
(160, 23, 6, 5),
(161, 23, 7, 5),
(162, 24, 1, 2),
(163, 24, 2, 4),
(164, 24, 3, 4),
(165, 24, 4, 4),
(166, 24, 5, 2),
(167, 24, 6, 5),
(168, 24, 7, 5),
(169, 25, 1, 2),
(170, 25, 2, 4),
(171, 25, 3, 4),
(172, 25, 4, 3),
(173, 25, 5, 3),
(174, 25, 6, 5),
(175, 25, 7, 4),
(176, 26, 1, 2),
(177, 26, 2, 4),
(178, 26, 3, 5),
(179, 26, 4, 4),
(180, 26, 5, 2),
(181, 26, 6, 5),
(182, 26, 7, 5),
(183, 27, 1, 3),
(184, 27, 2, 5),
(185, 27, 3, 5),
(186, 27, 4, 3),
(187, 27, 5, 2),
(188, 27, 6, 5),
(189, 27, 7, 5),
(197, 29, 1, 1),
(198, 29, 2, 3),
(199, 29, 3, 4),
(200, 29, 4, 2),
(201, 29, 5, 4),
(202, 29, 6, 5),
(203, 29, 7, 5),
(204, 30, 1, 3),
(205, 30, 2, 5),
(206, 30, 3, 4),
(207, 30, 4, 3),
(208, 30, 5, 3),
(209, 30, 6, 5),
(210, 30, 7, 5),
(218, 32, 1, 5),
(219, 32, 2, 5),
(220, 32, 3, 5),
(221, 32, 4, 5),
(222, 32, 5, 2),
(223, 32, 6, 5),
(224, 32, 7, 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `tb_alternatif`
--
ALTER TABLE `tb_alternatif`
  ADD PRIMARY KEY (`id_mobil`);

--
-- Indexes for table `tb_kriteria`
--
ALTER TABLE `tb_kriteria`
  ADD PRIMARY KEY (`id_kriteria`);

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
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_alternatif`
--
ALTER TABLE `tb_alternatif`
  MODIFY `id_mobil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `tb_kriteria`
--
ALTER TABLE `tb_kriteria`
  MODIFY `id_kriteria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tb_penilaian`
--
ALTER TABLE `tb_penilaian`
  MODIFY `id_penilaian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=225;

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
