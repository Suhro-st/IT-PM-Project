-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 30, 2026 at 07:41 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce_kampus`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `service_id` int NOT NULL,
  `nama_layanan` varchar(150) NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `status` enum('Menunggu','Diproses','Selesai','Dibatalkan') DEFAULT 'Menunggu',
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `status_pembayaran` enum('Belum Dibayar','Menunggu Verifikasi','Berhasil','Ditolak') DEFAULT 'Belum Dibayar',
  `tanggal_pesan` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `service_id` (`service_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `service_id`, `nama_layanan`, `harga`, `status`, `metode_pembayaran`, `status_pembayaran`, `tanggal_pesan`) VALUES
(1, 2, 2, 'Desain Grafis', 50000.00, 'Menunggu', NULL, 'Belum Dibayar', '2026-09-30 07:40:08'),
(2, 2, 3, 'Pembuatan Website', 150000.00, 'Menunggu', NULL, 'Belum Dibayar', '2026-09-30 07:40:08'),
(3, 2, 4, 'Edit Video', 75000.00, 'Menunggu', NULL, 'Belum Dibayar', '2026-09-30 07:40:08'),
(4, 2, 5, 'Jasa Pengetikan', 20000.00, 'Menunggu', NULL, 'Belum Dibayar', '2026-09-30 07:40:08'),
(5, 2, 6, 'Pembuatan PPT', 35000.00, 'Menunggu', NULL, 'Belum Dibayar', '2026-09-30 07:40:08');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `nama_layanan` varchar(150) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `user_id`, `nama_layanan`, `kategori`, `deskripsi`, `harga`, `status`, `created_at`) VALUES
(2, 1, 'Desain Grafis', 'Desain', 'Jasa pembuatan poster dan logo.', 50000.00, 'Aktif', '2026-09-29 13:00:39'),
(3, 1, 'Pembuatan Website', 'Pemrograman', 'Jasa pembuatan website sederhana.', 150000.00, 'Aktif', '2026-09-29 13:00:39'),
(4, 1, 'Edit Video', 'Multimedia', 'Jasa mengedit video untuk tugas dan konten.', 75000.00, 'Aktif', '2026-09-29 13:00:39'),
(5, 1, 'Jasa Pengetikan', 'Pengetikan', 'Jasa mengetik makalah dan laporan.', 20000.00, 'Aktif', '2026-09-29 13:00:39'),
(6, 1, 'Pembuatan PPT', 'Presentasi', 'Jasa pembuatan slide presentasi.', 35000.00, 'Aktif', '2026-09-29 13:00:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'disaster', 'disaster@gmail.com', '$2y$10$rZlsK9olHW4MaP34wV8HRuKtT09Zx/n2XznyvCWSQIz7dC1CRJFpu', 'user', '2026-09-29 12:29:19'),
(2, 'ulok', 'ulok@gmail.com', '$2y$10$PxRg9V52fCiWZsgG7dhsWu/Ps2SiMVsxEosZ9h860s958Sx6SEQUq', 'user', '2026-09-29 13:17:43');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
