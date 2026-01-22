-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 22, 2026 at 04:41 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `umkm`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id_admin` int NOT NULL,
  `nama` varchar(150) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id_admin`, `nama`, `username`, `password`, `jabatan`, `no_hp`, `created_at`) VALUES
(1, 'admin', 'admin', '$2y$10$W8lmgQ4fYuHFctG9uGEv4OS4UPMfNT08dm5wq226c1SLYAk.lcYB2', 'admin', '12345678', '2026-01-22 12:56:32'),
(6, 'adidut', 'adjie', '$2y$10$N..3wFKuU0LLr84TyGrBQOXL5uEl59kSDLFOUd1.huR0QFulowbiK', 'admin', '123', '2026-01-22 15:22:01');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_dokumen_umkm`
--

CREATE TABLE `tbl_dokumen_umkm` (
  `id_dokumen` int NOT NULL,
  `id_umkm` int NOT NULL,
  `jenis_dokumen` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_dokumen_umkm`
--

INSERT INTO `tbl_dokumen_umkm` (`id_dokumen`, `id_umkm`, `jenis_dokumen`, `file_path`, `created_at`) VALUES
(19, 4, 'Foto KTP', 'dok_697231785432a.png', '2026-01-22 14:17:28'),
(20, 4, 'Foto KK', 'dok_6972317854ef9.png', '2026-01-22 14:17:28'),
(21, 4, 'Sertifikat Halal', 'dok_697231785588b.png', '2026-01-22 14:17:28'),
(22, 4, 'Foto Menu Jualan', 'dok_697231785618d.png', '2026-01-22 14:17:28'),
(23, 4, 'Foto Tempat Usaha', 'dok_6972317856a03.png', '2026-01-22 14:17:28'),
(24, 4, 'Foto Pemilik & Usaha', 'dok_69723178573ae.png', '2026-01-22 14:17:28'),
(25, 5, 'Foto KTP', 'dok_6972361a20007.png', '2026-01-22 14:37:14'),
(26, 5, 'Foto KK', 'dok_6972361a2090a.png', '2026-01-22 14:37:14'),
(27, 5, 'Sertifikat Halal', 'dok_6972361a212c3.png', '2026-01-22 14:37:14'),
(28, 5, 'Foto Menu Jualan', 'dok_6972361a21bb5.png', '2026-01-22 14:37:14'),
(29, 5, 'Foto Tempat Usaha', 'dok_6972361a2245a.png', '2026-01-22 14:37:14'),
(30, 5, 'Foto Pemilik & Usaha', 'dok_6972361a22f8e.png', '2026-01-22 14:37:14');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_legalisasi`
--

CREATE TABLE `tbl_legalisasi` (
  `id_legalisasi` int NOT NULL,
  `id_umkm` int NOT NULL,
  `id_admin` int NOT NULL,
  `nomor_surat` varchar(100) DEFAULT NULL,
  `file_surat` varchar(255) DEFAULT NULL,
  `tanggal_terbit` date DEFAULT NULL,
  `blockchain_tx` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_legalisasi`
--

INSERT INTO `tbl_legalisasi` (`id_legalisasi`, `id_umkm`, `id_admin`, `nomor_surat`, `file_surat`, `tanggal_terbit`, `blockchain_tx`, `created_at`) VALUES
(1, 4, 1, '470/UMKM/20121', NULL, '2026-01-22', 'cde162f9e0f17b21b412c4e9cdbb27a2c4b0563ea12f35ca6d0822b021d7c288', '2026-01-22 14:46:06'),
(2, 4, 1, '470/UMKM/20121', NULL, '2026-01-22', '6bc0da0c4cf8129f1a93f512aa34d50e99d671ee944c690197cee496d6cbb39d', '2026-01-22 14:46:29'),
(3, 5, 1, '600/UMKM /2025', 'SURAT_UMKM_5.pdf', '2026-01-22', 'a8b0becf1a57f3831823183d59be3d53e4daa78165b9a1127e7046c1b8ed403c', '2026-01-22 16:20:04'),
(4, 5, 1, '600/UMKM /2025', 'SURAT_UMKM_5.pdf', '2026-01-22', '87091c78087675cba1865b0db39204d2c7ec78a4f6a0d9c3a510f63e47c0f4ff', '2026-01-22 16:22:43');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transaksi_blockchain`
--

CREATE TABLE `tbl_transaksi_blockchain` (
  `id_tx` int NOT NULL,
  `id_umkm` int NOT NULL,
  `tipe_transaksi` enum('pengajuan','verifikasi_rt_rw','surat_pengantar_terbit') NOT NULL,
  `hash_tx` varchar(255) DEFAULT NULL,
  `tanggal_tx` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_transaksi_blockchain`
--

INSERT INTO `tbl_transaksi_blockchain` (`id_tx`, `id_umkm`, `tipe_transaksi`, `hash_tx`, `tanggal_tx`) VALUES
(4, 4, 'pengajuan', 'a64e8f450036dcdbbb6c34d9e7426bb3f0e8b0a1de1a73af5f85f62b34c7d116', '2026-01-22 21:17:28'),
(5, 5, 'pengajuan', '65a7ff905d3518479eb186ed868587ac860e1853be3db971cb98575607fd3d29', '2026-01-22 21:37:14'),
(6, 4, 'verifikasi_rt_rw', 'c462b5b4627f5af3275f3e73417c1efeee627f190f7ce531442b6998f6abcd10', '2026-01-22 21:41:20'),
(7, 4, 'verifikasi_rt_rw', 'b0f8bf19b48a3604e7c8fa356879f483de560acd9bac6b7837f80971a1f03028', '2026-01-22 21:43:38'),
(8, 4, 'verifikasi_rt_rw', '656d8840fec27e97a2de1d69a503eb93a8eaea5c231d781e4f356c7d8f5861aa', '2026-01-22 21:44:05'),
(9, 4, 'verifikasi_rt_rw', '61150fdb3e8d94854af75dbbde8055562a385d883c5b753f9ceb96fc45e88af0', '2026-01-22 21:44:31'),
(10, 4, 'verifikasi_rt_rw', '58ec03d53779d3d125e017dccb2e05b178a51754958ae3aae2f02c92f2459e65', '2026-01-22 21:44:42'),
(11, 4, 'verifikasi_rt_rw', '741616e180594ce67ca1d125bc106ef7b72e66ce0a33a03202259d4a33d61c78', '2026-01-22 21:45:08'),
(12, 4, 'verifikasi_rt_rw', '1b075b05d95ef980bbff3b1c8a12d15cdb40b40427856f1b4e7e41b6ca68d711', '2026-01-22 21:45:40'),
(13, 4, 'surat_pengantar_terbit', 'cde162f9e0f17b21b412c4e9cdbb27a2c4b0563ea12f35ca6d0822b021d7c288', '2026-01-22 21:46:06'),
(14, 4, 'surat_pengantar_terbit', '6bc0da0c4cf8129f1a93f512aa34d50e99d671ee944c690197cee496d6cbb39d', '2026-01-22 21:46:29'),
(15, 5, 'verifikasi_rt_rw', '4e8d64282f032b3c4743e87f59927156f67ad2488f1816a927bb67c2caf7fce0', '2026-01-22 23:18:57'),
(16, 5, 'surat_pengantar_terbit', '87091c78087675cba1865b0db39204d2c7ec78a4f6a0d9c3a510f63e47c0f4ff', '2026-01-22 23:22:43');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_umkm`
--

CREATE TABLE `tbl_umkm` (
  `id_umkm` int NOT NULL,
  `id_warga` int NOT NULL,
  `nama_usaha` varchar(150) NOT NULL,
  `jenis_usaha` varchar(100) NOT NULL,
  `tahun_mulai` year DEFAULT NULL,
  `jumlah_karyawan` int DEFAULT NULL,
  `tanggal_pengajuan` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('diajukan','verifikasi','disetujui','ditolak') DEFAULT 'diajukan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_umkm`
--

INSERT INTO `tbl_umkm` (`id_umkm`, `id_warga`, `nama_usaha`, `jenis_usaha`, `tahun_mulai`, `jumlah_karyawan`, `tanggal_pengajuan`, `created_at`, `status`) VALUES
(4, 5, 'PT MAJU MUNDUR ENAK', 'Kuliner', '2025', 1, '2026-01-22', '2026-01-22 14:17:28', 'diajukan'),
(5, 5, 'PT GAJAH DUDUK', 'Perdagangan', '2012', 100, '2026-01-22', '2026-01-22 14:37:14', 'diajukan');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_warga`
--

CREATE TABLE `tbl_warga` (
  `id_warga` int NOT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `nik` char(16) NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text,
  `agama` varchar(30) DEFAULT NULL,
  `status_perkawinan` varchar(30) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_warga`
--

INSERT INTO `tbl_warga` (`id_warga`, `nama_lengkap`, `nik`, `tanggal_lahir`, `alamat`, `agama`, `status_perkawinan`, `no_hp`, `email`, `username`, `password`, `created_at`) VALUES
(1, 'Adidut', '3172040808980001', '2026-01-21', 'Jl.Ciputat', 'Kristen', 'Belum Kawin', '0857756994299', 'genix369@gmail.com', 'dut', '$2y$10$ZXmXGURv1sLKFjYZhTNwA.QWXoHO3t6yNJtZ2pxvpgfLIbY5XXZim', '2026-01-21 16:30:04'),
(2, 'tyas', '1313128716', '2026-01-21', 'xiu bsd', 'Islam', 'Belum Kawin', '187318', 'genix369@gmail.com', '123', '$2y$10$54Ukq8XFHYM95Clg9iap1ucSetRago8oY7C5xZBqYmntXAkttik02', '2026-01-21 16:30:48'),
(3, 'uvuyv', '37656764564', '2026-01-22', 'dkvnm', 'Katolik', 'Belum Kawin', '08674665', 'genix369@gmail.com', 'a', '$2y$10$9C0j9xG7G4AxZ5u8GAqFJuNmxhJwHT//nMWI9T.6o0pW3PAoPW4J.', '2026-01-21 17:20:10'),
(4, 'kjb', '44543423', '2026-01-15', 'tybnklmlkh', 'Kristen', 'Belum Kawin', '098786765', 'genix369@gmail.com', 'knkj', '$2y$10$VeNSVQgQxd5426cpKesXk.tpUTmJj7xN.6OXLmGwcAplt20LjCsmG', '2026-01-21 17:23:03'),
(5, 'ajiboy', '1234566789', '2026-01-22', 'Mampang', 'Islam', 'Belum Kawin', '088098980', 'genix369@gmail.com', 'user01', '$2y$10$On/4Nnha8mUcWyOeQlvIveBUGJv9IyNCCtFlVAepln0he/HOgWSQu', '2026-01-22 12:00:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `tbl_dokumen_umkm`
--
ALTER TABLE `tbl_dokumen_umkm`
  ADD PRIMARY KEY (`id_dokumen`),
  ADD KEY `fk_dokumen_umkm` (`id_umkm`);

--
-- Indexes for table `tbl_legalisasi`
--
ALTER TABLE `tbl_legalisasi`
  ADD PRIMARY KEY (`id_legalisasi`),
  ADD KEY `fk_legalisasi_umkm` (`id_umkm`),
  ADD KEY `fk_legalisasi_admin` (`id_admin`);

--
-- Indexes for table `tbl_transaksi_blockchain`
--
ALTER TABLE `tbl_transaksi_blockchain`
  ADD PRIMARY KEY (`id_tx`),
  ADD KEY `fk_blockchain_umkm` (`id_umkm`);

--
-- Indexes for table `tbl_umkm`
--
ALTER TABLE `tbl_umkm`
  ADD PRIMARY KEY (`id_umkm`),
  ADD KEY `fk_umkm_warga` (`id_warga`);

--
-- Indexes for table `tbl_warga`
--
ALTER TABLE `tbl_warga`
  ADD PRIMARY KEY (`id_warga`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_dokumen_umkm`
--
ALTER TABLE `tbl_dokumen_umkm`
  MODIFY `id_dokumen` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tbl_legalisasi`
--
ALTER TABLE `tbl_legalisasi`
  MODIFY `id_legalisasi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_transaksi_blockchain`
--
ALTER TABLE `tbl_transaksi_blockchain`
  MODIFY `id_tx` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tbl_umkm`
--
ALTER TABLE `tbl_umkm`
  MODIFY `id_umkm` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_warga`
--
ALTER TABLE `tbl_warga`
  MODIFY `id_warga` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_dokumen_umkm`
--
ALTER TABLE `tbl_dokumen_umkm`
  ADD CONSTRAINT `fk_dokumen_umkm` FOREIGN KEY (`id_umkm`) REFERENCES `tbl_umkm` (`id_umkm`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_legalisasi`
--
ALTER TABLE `tbl_legalisasi`
  ADD CONSTRAINT `fk_legalisasi_admin` FOREIGN KEY (`id_admin`) REFERENCES `tbl_admin` (`id_admin`),
  ADD CONSTRAINT `fk_legalisasi_umkm` FOREIGN KEY (`id_umkm`) REFERENCES `tbl_umkm` (`id_umkm`);

--
-- Constraints for table `tbl_transaksi_blockchain`
--
ALTER TABLE `tbl_transaksi_blockchain`
  ADD CONSTRAINT `fk_blockchain_umkm` FOREIGN KEY (`id_umkm`) REFERENCES `tbl_umkm` (`id_umkm`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_umkm`
--
ALTER TABLE `tbl_umkm`
  ADD CONSTRAINT `fk_umkm_warga` FOREIGN KEY (`id_warga`) REFERENCES `tbl_warga` (`id_warga`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
