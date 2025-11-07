-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 07, 2025 at 04:35 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpustakaan_db_222274`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_222274`
--

CREATE TABLE `admin_222274` (
  `id_admin_222274` int(11) NOT NULL,
  `username_222274` varchar(50) DEFAULT NULL,
  `password_222274` varchar(255) DEFAULT NULL,
  `nama_lengkap_222274` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_222274`
--

INSERT INTO `admin_222274` (`id_admin_222274`, `username_222274`, `password_222274`, `nama_lengkap_222274`) VALUES
(1, 'admin@gmail.com', '$2y$10$3k3p8YEJivcP/ewAzBG8JOfwIf5tleA4pG9N8FFdZSSraQSEQXa3K', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `anggota_222274`
--

CREATE TABLE `anggota_222274` (
  `id_anggota_222274` int(11) NOT NULL,
  `nama_222274` varchar(100) DEFAULT NULL,
  `email_222274` varchar(100) DEFAULT NULL,
  `password_222274` varchar(255) DEFAULT NULL,
  `alamat_222274` text DEFAULT NULL,
  `no_telp_222274` varchar(20) DEFAULT NULL,
  `tanggal_daftar_222274` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anggota_222274`
--

INSERT INTO `anggota_222274` (`id_anggota_222274`, `nama_222274`, `email_222274`, `password_222274`, `alamat_222274`, `no_telp_222274`, `tanggal_daftar_222274`) VALUES
(1, 'adil', 'adil@gmail.com', '$2y$10$r8jYxDVOjET3pg0K4naAs.4UuorXsxq3MXH6rG1kPUcFdGoSmxMAi', 'makassar', '081347018612', '2025-11-02'),
(2, 'mines', 'mines@gmail.com', '$2y$10$BmJftFTAMCU.Ara1j8Riiuy0ZQIpsZrSf9lp6ndqW0p6Ds8wNL.Mu', 'mamuju', '081347018612', '2025-11-02'),
(3, 'evto', 'evto@gmail.com', '$2y$10$bS8mm1WAhe4ZLtaGvZ/ahO/Uvf9h6qjSzRuXzP9xqstfRdkvF5koO', 'makassar', '081347018613', '2025-11-03');

-- --------------------------------------------------------

--
-- Table structure for table `buku_222274`
--

CREATE TABLE `buku_222274` (
  `id_buku` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `penulis` varchar(100) DEFAULT NULL,
  `penerbit` varchar(100) DEFAULT NULL,
  `tahun_terbit` year(4) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buku_222274`
--

INSERT INTO `buku_222274` (`id_buku`, `judul`, `penulis`, `penerbit`, `tahun_terbit`, `kategori`, `stok`, `img`) VALUES
(1, 'wkwkw', 'wkwkw', 'wkwkw', '2004', 'Pemrograman', 0, NULL),
(2, 'gas', 'gas', 'gas', '2004', 'Pemrograma', 9, NULL),
(3, 'mantapu', 'kode', 'manusia', '2004', 'Jaringan', 11, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kategori_222274`
--

CREATE TABLE `kategori_222274` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_222274`
--

INSERT INTO `kategori_222274` (`id_kategori`, `nama_kategori`) VALUES
(2, 'Database'),
(4, 'Jaringan'),
(1, 'Pemrograman'),
(3, 'Web');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman_222274`
--

CREATE TABLE `peminjaman_222274` (
  `id_peminjaman` int(11) NOT NULL,
  `id_anggota` int(11) NOT NULL,
  `id_buku` int(11) NOT NULL,
  `tanggal_pinjam` date DEFAULT curdate(),
  `tanggal_kembali` date DEFAULT NULL,
  `status` enum('dipinjam','dikembalikan') DEFAULT 'dipinjam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peminjaman_222274`
--

INSERT INTO `peminjaman_222274` (`id_peminjaman`, `id_anggota`, `id_buku`, `tanggal_pinjam`, `tanggal_kembali`, `status`) VALUES
(1, 2, 1, '2025-11-08', '2025-11-14', 'dipinjam'),
(16, 3, 2, '2025-11-07', '2025-11-07', 'dikembalikan'),
(17, 3, 2, '2025-11-07', '2025-11-07', 'dikembalikan'),
(18, 3, 2, '2025-11-07', '2025-11-07', 'dikembalikan'),
(19, 3, 3, '2025-11-07', '2025-11-07', 'dikembalikan');

-- --------------------------------------------------------

--
-- Table structure for table `pengembalian_222274`
--

CREATE TABLE `pengembalian_222274` (
  `id_pengembalian` int(11) NOT NULL,
  `id_peminjaman` int(11) NOT NULL,
  `tanggal_dikembalikan` date DEFAULT curdate(),
  `denda` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengembalian_222274`
--

INSERT INTO `pengembalian_222274` (`id_pengembalian`, `id_peminjaman`, `tanggal_dikembalikan`, `denda`) VALUES
(1, 16, '2025-11-07', 0.00),
(2, 17, '2025-11-07', 0.00),
(3, 18, '2025-11-07', 0.00),
(4, 19, '2025-11-07', 0.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_222274`
--
ALTER TABLE `admin_222274`
  ADD PRIMARY KEY (`id_admin_222274`),
  ADD UNIQUE KEY `username` (`username_222274`);

--
-- Indexes for table `anggota_222274`
--
ALTER TABLE `anggota_222274`
  ADD PRIMARY KEY (`id_anggota_222274`),
  ADD UNIQUE KEY `email` (`email_222274`);

--
-- Indexes for table `buku_222274`
--
ALTER TABLE `buku_222274`
  ADD PRIMARY KEY (`id_buku`);

--
-- Indexes for table `kategori_222274`
--
ALTER TABLE `kategori_222274`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indexes for table `peminjaman_222274`
--
ALTER TABLE `peminjaman_222274`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `id_anggota` (`id_anggota`),
  ADD KEY `id_buku` (`id_buku`);

--
-- Indexes for table `pengembalian_222274`
--
ALTER TABLE `pengembalian_222274`
  ADD PRIMARY KEY (`id_pengembalian`),
  ADD KEY `id_peminjaman` (`id_peminjaman`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_222274`
--
ALTER TABLE `admin_222274`
  MODIFY `id_admin_222274` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `anggota_222274`
--
ALTER TABLE `anggota_222274`
  MODIFY `id_anggota_222274` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `buku_222274`
--
ALTER TABLE `buku_222274`
  MODIFY `id_buku` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kategori_222274`
--
ALTER TABLE `kategori_222274`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `peminjaman_222274`
--
ALTER TABLE `peminjaman_222274`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `pengembalian_222274`
--
ALTER TABLE `pengembalian_222274`
  MODIFY `id_pengembalian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `peminjaman_222274`
--
ALTER TABLE `peminjaman_222274`
  ADD CONSTRAINT `peminjaman_222274_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `anggota_222274` (`id_anggota_222274`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `peminjaman_222274_ibfk_2` FOREIGN KEY (`id_buku`) REFERENCES `buku_222274` (`id_buku`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pengembalian_222274`
--
ALTER TABLE `pengembalian_222274`
  ADD CONSTRAINT `pengembalian_222274_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman_222274` (`id_peminjaman`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
