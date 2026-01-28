-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 28, 2026 at 03:06 PM
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
  `role` enum('rt','rw','admin') NOT NULL,
  `rt` varchar(10) DEFAULT NULL,
  `rw` varchar(10) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ttd` varchar(100) DEFAULT NULL,
  `stempel` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id_admin`, `nama`, `username`, `password`, `role`, `rt`, `rw`, `jabatan`, `no_hp`, `created_at`, `ttd`, `stempel`) VALUES
(1, 'admin', 'admin', '$2y$10$W8lmgQ4fYuHFctG9uGEv4OS4UPMfNT08dm5wq226c1SLYAk.lcYB2', 'admin', NULL, NULL, 'admin', '12345678', '2026-01-22 12:56:32', NULL, NULL),
(8, 'Asep Kusnadi Ginanjar', 'asep', '$2y$10$ZHeDszhpIJnX0DfNaKtKG.7hkIft7/v21QNCdOST9ViqHqw53fNQS', 'rt', '01', '02', 'RT', '123456789', '2026-01-23 12:54:07', 'ttd_rt_01_02.png', 'stempel_rt_01_02.png'),
(9, 'Adjie Masaid Utomo', 'adjie', '$2y$10$FFhMFmmm4iabhBEBy2PB8OqVn9eFEGTF.glL9VXLyix9WiBbMXWky', 'rw', NULL, '02', 'RW', '123456789', '2026-01-23 12:54:28', 'ttd_rw_02.png', 'stempel_rw_02.png');

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
(71, 16, 'Foto KTP', 'dok_697a258f5006e.png', '2026-01-28 15:04:47');

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
(15, 16, 9, 'SK/UMKM/001/2026', 'surat_umkm_16.pdf', '2026-01-28', NULL, '2026-01-28 15:05:34');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transaksi_blockchain`
--

CREATE TABLE `tbl_transaksi_blockchain` (
  `id_tx` int NOT NULL,
  `id_umkm` int NOT NULL,
  `tipe_transaksi` enum('pengajuan','verifikasi_rt','verifikasi_rw','surat_pengantar_terbit','pengajuan_ulang') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `hash_tx` varchar(255) DEFAULT NULL,
  `tanggal_tx` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_transaksi_blockchain`
--

INSERT INTO `tbl_transaksi_blockchain` (`id_tx`, `id_umkm`, `tipe_transaksi`, `hash_tx`, `tanggal_tx`) VALUES
(59, 16, 'pengajuan', 'd587c1810fe4763ea881a31fbcfadb740afe1f652c937505fc913dd35aa02bf9', '2026-01-28 22:04:47'),
(60, 16, 'verifikasi_rt', 'ef6540b9f1c673d14423941d0e9df72611ed37bc48075eb468d8f39f3b2fcd98', '2026-01-28 22:05:02'),
(61, 16, 'surat_pengantar_terbit', '4306fd91ccd120b4678459898a7c5ebde5cdfc64219c7c99ade29bcc678dcffa', '2026-01-28 22:05:34');

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
  `status` enum('menunggu_rt','ditolak_rt','menunggu_rw','ditolak_rw','disetujui','pengajuan_ulang') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'menunggu_rt',
  `approved_rt_by` int DEFAULT NULL,
  `approved_rt_at` datetime DEFAULT NULL,
  `approved_rw_by` int DEFAULT NULL,
  `approved_rw_at` datetime DEFAULT NULL,
  `catatan_penolakan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_umkm`
--

INSERT INTO `tbl_umkm` (`id_umkm`, `id_warga`, `nama_usaha`, `jenis_usaha`, `tahun_mulai`, `jumlah_karyawan`, `tanggal_pengajuan`, `created_at`, `status`, `approved_rt_by`, `approved_rt_at`, `approved_rw_by`, `approved_rw_at`, `catatan_penolakan`) VALUES
(16, 6, 'PT MENCARI CINTA MESRA (MCM)', 'Jasa', '2019', 1000, '2026-01-28', '2026-01-28 15:04:47', 'disetujui', 8, '2026-01-28 22:05:02', 9, '2026-01-28 22:05:34', NULL);

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
  `rt` varchar(5) DEFAULT '01',
  `rw` varchar(5) DEFAULT '02',
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

INSERT INTO `tbl_warga` (`id_warga`, `nama_lengkap`, `nik`, `tanggal_lahir`, `alamat`, `rt`, `rw`, `agama`, `status_perkawinan`, `no_hp`, `email`, `username`, `password`, `created_at`) VALUES
(6, 'Topik', '1234567890', '2026-01-23', 'Roxy Tercinta', '01', '02', 'Kristen', 'Belum Kawin', '123456789', 'topik.mencaricinta@gmail.com', 'topik', '$2y$10$X2v3O2UMIFbAaza24z0K/.ZUIK.21Zi7aRWBv9phFU754grUYo6YW', '2026-01-23 13:09:41');

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
  ADD UNIQUE KEY `id_umkm` (`id_umkm`),
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
  ADD KEY `fk_umkm_warga` (`id_warga`),
  ADD KEY `fk_umkm_rt` (`approved_rt_by`),
  ADD KEY `fk_umkm_rw` (`approved_rw_by`);

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
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_dokumen_umkm`
--
ALTER TABLE `tbl_dokumen_umkm`
  MODIFY `id_dokumen` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `tbl_legalisasi`
--
ALTER TABLE `tbl_legalisasi`
  MODIFY `id_legalisasi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_transaksi_blockchain`
--
ALTER TABLE `tbl_transaksi_blockchain`
  MODIFY `id_tx` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `tbl_umkm`
--
ALTER TABLE `tbl_umkm`
  MODIFY `id_umkm` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tbl_warga`
--
ALTER TABLE `tbl_warga`
  MODIFY `id_warga` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  ADD CONSTRAINT `fk_umkm_rt` FOREIGN KEY (`approved_rt_by`) REFERENCES `tbl_admin` (`id_admin`),
  ADD CONSTRAINT `fk_umkm_rw` FOREIGN KEY (`approved_rw_by`) REFERENCES `tbl_admin` (`id_admin`),
  ADD CONSTRAINT `fk_umkm_warga` FOREIGN KEY (`id_warga`) REFERENCES `tbl_warga` (`id_warga`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
