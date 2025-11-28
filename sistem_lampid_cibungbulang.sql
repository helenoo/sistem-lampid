-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20250813.7569edf1cc
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 09, 2025 at 02:38 AM
-- Server version: 8.4.3
-- PHP Version: 8.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistem_lampid_cibungbulang`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_penduduk`
--

CREATE TABLE `data_penduduk` (
  `id_data` int NOT NULL,
  `id_desa` int NOT NULL,
  `bulan` int NOT NULL,
  `tahun` int NOT NULL,
  `penduduk_bulan_lalu` int DEFAULT '0',
  `lahir` int DEFAULT '0',
  `mati` int DEFAULT '0',
  `pindah` int DEFAULT '0',
  `datang` int DEFAULT '0',
  `penduduk_bulan_ini` int DEFAULT '0',
  `kartu_keluarga` int DEFAULT '0',
  `ktp` int DEFAULT '0',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `data_penduduk`
--

INSERT INTO `data_penduduk` (`id_data`, `id_desa`, `bulan`, `tahun`, `penduduk_bulan_lalu`, `lahir`, `mati`, `pindah`, `datang`, `penduduk_bulan_ini`, `kartu_keluarga`, `ktp`, `last_updated`) VALUES
(1, 9, 7, 2025, 8594, 9, 9, 9, 1, 8586, 2571, 5642, '2025-09-08 04:49:17'),
(2, 9, 2, 2025, 7623, 2, 1, 6, 3, 7362, 1232, 1423, '2025-09-08 06:59:48');

-- --------------------------------------------------------

--
-- Table structure for table `desa`
--

CREATE TABLE `desa` (
  `id_desa` int NOT NULL,
  `nama_desa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `desa`
--

INSERT INTO `desa` (`id_desa`, `nama_desa`) VALUES
(1, 'Situ Udik'),
(2, 'Situ Ilir'),
(3, 'Sukamaju'),
(4, 'Cibatok I'),
(5, 'Cibatok II'),
(6, 'Ciaruteun Udik'),
(7, 'Cemplang'),
(8, 'Galuga'),
(9, 'Dukuh'),
(10, 'Cijujung'),
(11, 'Cimanggu I'),
(12, 'Cimanggu II'),
(13, 'Leuweungkolot'),
(14, 'Girimulya'),
(15, 'Ciaruteun Ilir');

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_uploads`
--

CREATE TABLE `dokumen_uploads` (
  `id_upload` int NOT NULL,
  `id_desa` int NOT NULL,
  `bulan` int NOT NULL,
  `tahun` int NOT NULL,
  `tipe_dokumen` enum('perkembangan_penduduk','kelompok_umur') NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path_file` varchar(255) NOT NULL,
  `tgl_upload` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `dokumen_uploads`
--

INSERT INTO `dokumen_uploads` (`id_upload`, `id_desa`, `bulan`, `tahun`, `tipe_dokumen`, `nama_file`, `path_file`, `tgl_upload`) VALUES
(5, 9, 1, 2026, 'perkembangan_penduduk', 'DATA PERKEMBANGAN PENDUDUK.xlsx', '../uploads/2026-01_9_perkembangan_penduduk_1757316596.xlsx', '2025-09-08 07:29:56'),
(6, 9, 1, 2025, 'kelompok_umur', 'LAPORAN BULANAN PENDUDUK MENURUT KELOMPOK UMUR.docx', '../uploads/2025-01_9_kelompok_umur_1757384992.docx', '2025-09-09 02:29:53'),
(7, 9, 1, 2027, 'kelompok_umur', 'LAPORAN BULANAN PENDUDUK MENURUT KELOMPOK UMUR.docx', '../uploads/2027-01_9_kelompok_umur_1757385030.docx', '2025-09-09 02:30:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `id_desa` int DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','desa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `id_desa`, `username`, `password`, `role`) VALUES
(1, NULL, 'kecamatancibungbulang116', '$2y$10$rnNu5fWeiu/dluObQ7kNrOyrUIZDKPYeii6/bnLivcfX.UuJ9g7la', 'admin'),
(2, 1, 'user_situ_udik', '$2y$12$Ox6wIaEYSJhyJMjpCmRhWe2OdzXGMVMcxykpibNFvQd/4sX7gM4nW', 'desa'),
(3, 2, 'user_situ_ilir', '$2y$12$Ox6wIaEYSJhyJMjpCmRhWe2OdzXGMVMcxykpibNFvQd/4sX7gM4nW', 'desa'),
(4, 3, 'user_sukamaju', '$2y$12$Ox6wIaEYSJhyJMjpCmRhWe2OdzXGMVMcxykpibNFvQd/4sX7gM4nW', 'desa'),
(5, 4, 'user_cibatok1', '$2y$12$Ox6wIaEYSJhyJMjpCmRhWe2OdzXGMVMcxykpibNFvQd/4sX7gM4nW', 'desa'),
(6, 5, 'user_cibatok2', '$2y$12$Ox6wIaEYSJhyJMjpCmRhWe2OdzXGMVMcxykpibNFvQd/4sX7gM4nW', 'desa'),
(7, 6, 'user_ciaruteun_udik', '$2y$12$Ox6wIaEYSJhyJMjpCmRhWe2OdzXGMVMcxykpibNFvQd/4sX7gM4nW', 'desa'),
(8, 7, 'user_cemplang', '$2y$12$Ox6wIaEYSJhyJMjpCmRhWe2OdzXGMVMcxykpibNFvQd/4sX7gM4nW', 'desa'),
(9, 8, 'user_galuga', '$2y$12$Ox6wIaEYSJhyJMjpCmRhWe2OdzXGMVMcxykpibNFvQd/4sX7gM4nW', 'desa'),
(10, 9, 'user_dukuh', '$2y$12$Ox6wIaEYSJhyJMjpCmRhWe2OdzXGMVMcxykpibNFvQd/4sX7gM4nW', 'desa'),
(11, 10, 'user_cijujung', '$2y$12$Ox6wIaEYSJhyJMjpCmRhWe2OdzXGMVMcxykpibNFvQd/4sX7gM4nW', 'desa'),
(12, 11, 'user_cimanggu1', '$2y$12$Ox6wIaEYSJhyJMjpCmRhWe2OdzXGMVMcxykpibNFvQd/4sX7gM4nW', 'desa'),
(13, 12, 'user_cimanggu2', '$2y$12$Ox6wIaEYSJhyJMjpCmRhWe2OdzXGMVMcxykpibNFvQd/4sX7gM4nW', 'desa'),
(14, 13, 'user_leuweungkolot', '$2y$12$Ox6wIaEYSJhyJMjpCmRhWe2OdzXGMVMcxykpibNFvQd/4sX7gM4nW', 'desa'),
(15, 14, 'user_girimulya', '$2y$12$Ox6wIaEYSJhyJMjpCmRhWe2OdzXGMVMcxykpibNFvQd/4sX7gM4nW', 'desa'),
(16, 15, 'user_ciaruteun_ilir', '$2y$12$Ox6wIaEYSJhyJMjpCmRhWe2OdzXGMVMcxykpibNFvQd/4sX7gM4nW', 'desa');

-- --------------------------------------------------------

--
-- Table structure for table `web_info`
--

CREATE TABLE `web_info` (
  `id_info` int NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `luas_wilayah` varchar(25) NOT NULL,
  `jumlah_penduduk` int NOT NULL,
  `jumlah_rw` int DEFAULT '0',
  `jumlah_rt` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `web_info`
--

INSERT INTO `web_info` (`id_info`, `judul`, `deskripsi`, `luas_wilayah`, `jumlah_penduduk`, `jumlah_rw`, `jumlah_rt`) VALUES
(1, 'SISTEM LAMPID KECAMATAN CIBUNGBULANG', 'Selamat datang di Sistem Laporan Informasi Kependudukan (LAMPID) Kecamatan Cibungbulang. Silakan login untuk melanjutkan.', '56432,54 km²', 75432, 153, 256);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_penduduk`
--
ALTER TABLE `data_penduduk`
  ADD PRIMARY KEY (`id_data`),
  ADD UNIQUE KEY `desa_bulan_tahun` (`id_desa`,`bulan`,`tahun`);

--
-- Indexes for table `desa`
--
ALTER TABLE `desa`
  ADD PRIMARY KEY (`id_desa`);

--
-- Indexes for table `dokumen_uploads`
--
ALTER TABLE `dokumen_uploads`
  ADD PRIMARY KEY (`id_upload`),
  ADD KEY `id_desa` (`id_desa`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_desa` (`id_desa`);

--
-- Indexes for table `web_info`
--
ALTER TABLE `web_info`
  ADD PRIMARY KEY (`id_info`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_penduduk`
--
ALTER TABLE `data_penduduk`
  MODIFY `id_data` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `desa`
--
ALTER TABLE `desa`
  MODIFY `id_desa` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `dokumen_uploads`
--
ALTER TABLE `dokumen_uploads`
  MODIFY `id_upload` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `web_info`
--
ALTER TABLE `web_info`
  MODIFY `id_info` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `data_penduduk`
--
ALTER TABLE `data_penduduk`
  ADD CONSTRAINT `data_penduduk_ibfk_1` FOREIGN KEY (`id_desa`) REFERENCES `desa` (`id_desa`);

--
-- Constraints for table `dokumen_uploads`
--
ALTER TABLE `dokumen_uploads`
  ADD CONSTRAINT `dokumen_uploads_ibfk_1` FOREIGN KEY (`id_desa`) REFERENCES `desa` (`id_desa`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_desa`) REFERENCES `desa` (`id_desa`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
