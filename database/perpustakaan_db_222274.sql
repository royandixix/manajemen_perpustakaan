-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 10, 2025 at 11:14 PM
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
(3, 'evto', 'evto@gmail.com', '$2y$10$bS8mm1WAhe4ZLtaGvZ/ahO/Uvf9h6qjSzRuXzP9xqstfRdkvF5koO', 'makassar', '081347018613', '2025-11-03'),
(4, 'tenxii', 'tenxi@gmail.com', '$2y$10$cHaigYsWNfLacHnst77w0e8xpfSBoRDodLpCUOZNZ4yv4IpUPpI.S', 'makassar', '081347018612', '2025-12-04'),
(5, 'desta', 'desta@gmail.com', '$2y$10$wi8cKZp/qhNAiHTcclgLRO/9IoHx/vApEmPUDRVCNQNxmX5Jh7hja', 'makassar', '081347018612', '2025-12-04');

-- --------------------------------------------------------

--
-- Table structure for table `buku_222274`
--

CREATE TABLE `buku_222274` (
  `id_buku_222274` int(11) NOT NULL,
  `judul_222274` varchar(150) NOT NULL,
  `penulis_222274` varchar(100) DEFAULT NULL,
  `penerbit_222274` varchar(100) DEFAULT NULL,
  `tahun_terbit_222274` year(4) DEFAULT NULL,
  `kategori_222274` varchar(100) DEFAULT NULL,
  `stok_222274` int(11) DEFAULT 0,
  `img_222274` varchar(255) DEFAULT NULL,
  `deskripsi_222274` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buku_222274`
--

INSERT INTO `buku_222274` (`id_buku_222274`, `judul_222274`, `penulis_222274`, `penerbit_222274`, `tahun_terbit_222274`, `kategori_222274`, `stok_222274`, `img_222274`, `deskripsi_222274`) VALUES
(4, 'agama', 'ustad abdul somad', 'gabriel', '2021', 'agama', 6, 'buku_1764782441.png', 'Buku Agama menyajikan pemahaman mendalam tentang ajaran, nilai, dan praktik dalam kehidupan beragama. Buku ini membahas aspek-aspek spiritual, moral, dan sosial yang dapat membimbing pembaca untuk menjalani kehidupan dengan penuh kesadaran dan kedamaian.\r\n\r\nDitulis dengan bahasa yang mudah dipahami, buku ini cocok bagi siapa saja yang ingin memperdalam pengetahuan tentang agama, baik untuk refleksi pribadi maupun sebagai panduan dalam berinteraksi dengan lingkungan sekitar. Setiap bab dirancang untuk menghadirkan wawasan yang inspiratif, menekankan pentingnya nilai-nilai kebaikan, toleransi, dan pengembangan diri melalui ajaran agama.');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_222274`
--

CREATE TABLE `kategori_222274` (
  `id_kategori_222274` int(11) NOT NULL,
  `nama_kategori_222274` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_222274`
--

INSERT INTO `kategori_222274` (`id_kategori_222274`, `nama_kategori_222274`) VALUES
(5, 'agama'),
(2, 'Database'),
(4, 'Jaringan'),
(1, 'Pemrograman'),
(3, 'Web');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman_222274`
--

CREATE TABLE `peminjaman_222274` (
  `id_peminjaman_222274` int(11) NOT NULL,
  `id_anggota_222274` int(11) NOT NULL,
  `id_buku_222274` int(11) NOT NULL,
  `id_admin_222274` int(11) DEFAULT NULL,
  `tanggal_pinjam_222274` date DEFAULT NULL,
  `tanggal_kembali_222274` date DEFAULT NULL,
  `status_222274` enum('dipinjam','dikembalikan','menunggu_konfirmasi_admin','dibatalkan','menunggu_konfirmasi_pengembalian') DEFAULT 'menunggu_konfirmasi_admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peminjaman_222274`
--

INSERT INTO `peminjaman_222274` (`id_peminjaman_222274`, `id_anggota_222274`, `id_buku_222274`, `id_admin_222274`, `tanggal_pinjam_222274`, `tanggal_kembali_222274`, `status_222274`) VALUES
(26, 3, 4, 1, '2025-12-04', '2025-12-04', 'dikembalikan'),
(27, 3, 4, 1, '2025-12-10', '2025-12-17', 'dipinjam');

-- --------------------------------------------------------

--
-- Table structure for table `pengembalian_222274`
--

CREATE TABLE `pengembalian_222274` (
  `id_pengembalian_222274` int(11) NOT NULL,
  `id_peminjaman_222274` int(11) NOT NULL,
  `tanggal_dikembalikan_222274` date DEFAULT NULL,
  `denda_222274` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengembalian_222274`
--

INSERT INTO `pengembalian_222274` (`id_pengembalian_222274`, `id_peminjaman_222274`, `tanggal_dikembalikan_222274`, `denda_222274`) VALUES
(7, 26, '2025-12-04', 0.00);

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
  ADD PRIMARY KEY (`id_buku_222274`);

--
-- Indexes for table `kategori_222274`
--
ALTER TABLE `kategori_222274`
  ADD PRIMARY KEY (`id_kategori_222274`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori_222274`);

--
-- Indexes for table `peminjaman_222274`
--
ALTER TABLE `peminjaman_222274`
  ADD PRIMARY KEY (`id_peminjaman_222274`),
  ADD KEY `id_anggota` (`id_anggota_222274`),
  ADD KEY `id_buku` (`id_buku_222274`);

--
-- Indexes for table `pengembalian_222274`
--
ALTER TABLE `pengembalian_222274`
  ADD PRIMARY KEY (`id_pengembalian_222274`),
  ADD KEY `id_peminjaman` (`id_peminjaman_222274`);

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
  MODIFY `id_anggota_222274` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `buku_222274`
--
ALTER TABLE `buku_222274`
  MODIFY `id_buku_222274` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kategori_222274`
--
ALTER TABLE `kategori_222274`
  MODIFY `id_kategori_222274` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `peminjaman_222274`
--
ALTER TABLE `peminjaman_222274`
  MODIFY `id_peminjaman_222274` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `pengembalian_222274`
--
ALTER TABLE `pengembalian_222274`
  MODIFY `id_pengembalian_222274` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `peminjaman_222274`
--
ALTER TABLE `peminjaman_222274`
  ADD CONSTRAINT `peminjaman_222274_ibfk_1` FOREIGN KEY (`id_anggota_222274`) REFERENCES `anggota_222274` (`id_anggota_222274`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `peminjaman_222274_ibfk_2` FOREIGN KEY (`id_buku_222274`) REFERENCES `buku_222274` (`id_buku_222274`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pengembalian_222274`
--
ALTER TABLE `pengembalian_222274`
  ADD CONSTRAINT `pengembalian_222274_ibfk_1` FOREIGN KEY (`id_peminjaman_222274`) REFERENCES `peminjaman_222274` (`id_peminjaman_222274`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
