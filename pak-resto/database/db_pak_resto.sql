-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 08, 2026 at 03:55 PM
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
-- Database: `db_pak_resto`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `status_item` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail`, `id_pesanan`, `id_menu`, `jumlah`, `subtotal`, `status_item`) VALUES
(1, 1, 1, 2, 16000.00, 'selesai'),
(2, 2, 1, 1, 8000.00, 'selesai'),
(3, 3, 1, 1, 8000.00, 'selesai'),
(4, 4, 1, 1, 8000.00, 'selesai'),
(5, 5, 1, 1, 8000.00, 'selesai'),
(6, 6, 1, 1, 8000.00, 'selesai'),
(7, 7, 3, 1, 10000.00, 'selesai'),
(8, 8, 1, 1, 8000.00, 'selesai'),
(9, 9, 3, 1, 10000.00, 'selesai'),
(10, 10, 1, 1, 8000.00, 'selesai'),
(11, 10, 3, 1, 10000.00, 'selesai'),
(12, 11, 1, 3, 24000.00, 'selesai'),
(13, 12, 1, 2, 16000.00, 'selesai'),
(14, 12, 3, 2, 20000.00, 'selesai'),
(15, 13, 1, 1, 8000.00, 'selesai'),
(16, 13, 3, 1, 10000.00, 'selesai'),
(17, 14, 1, 1, 8000.00, 'selesai'),
(18, 15, 1, 2, 16000.00, 'dibatalkan'),
(19, 16, 1, 13, 104000.00, 'dibatalkan'),
(20, 17, 1, 1, 8000.00, 'selesai');

-- --------------------------------------------------------

--
-- Table structure for table `meja`
--

CREATE TABLE `meja` (
  `id_meja` int(11) NOT NULL,
  `nomor_meja` varchar(10) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `status_meja` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meja`
--

INSERT INTO `meja` (`id_meja`, `nomor_meja`, `kapasitas`, `status_meja`) VALUES
(1, 'M01', 2, 'tersedia'),
(2, 'M02', 2, 'tersedia'),
(3, 'M03', 4, 'tersedia'),
(4, 'M04', 4, 'tersedia'),
(5, 'M05', 6, 'tersedia'),
(6, 'M06', 6, 'tersedia'),
(7, 'M07', 8, 'tersedia'),
(8, 'M08', 8, 'tersedia'),
(9, 'M09', 4, 'tersedia'),
(10, 'M10', 2, 'tersedia'),
(11, 'M11', 6, 'tersedia'),
(12, 'M12', 4, 'tersedia'),
(13, 'M13', 8, 'tersedia'),
(14, 'M14', 2, 'tersedia'),
(15, 'M15', 6, 'tersedia'),
(16, 'M16', 4, 'tersedia'),
(17, 'M17', 8, 'tersedia'),
(18, 'M18', 2, 'tersedia'),
(19, 'M19', 6, 'tersedia'),
(20, 'M20', 4, 'tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `kategori` varchar(20) NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `stok_menu` int(11) NOT NULL,
  `status_ketersediaan` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id_menu`, `nama_menu`, `kategori`, `harga`, `stok_menu`, `status_ketersediaan`) VALUES
(1, 'Es Jeruk', 'Minuman', 8000.00, 12, 'tersedia'),
(3, 'Wedang Cemue', 'Minuman', 6767.00, 67, 'tersedia'),
(5, 'Nasi Goreng Kambing', 'Makanan', 21000.00, 20, 'tersedia'),
(6, 'Nasi Goreng Spesial', 'Makanan', 18000.00, 25, 'tersedia'),
(7, 'Mie Goreng Jawa', 'Makanan', 16000.00, 20, 'tersedia'),
(8, 'Ayam Geprek', 'Makanan', 20000.00, 18, 'tersedia'),
(9, 'Ayam Bakar', 'Makanan', 22000.00, 15, 'tersedia'),
(10, 'Soto Ayam', 'Makanan', 15000.00, 20, 'tersedia'),
(11, 'Bakso Kuah', 'Makanan', 17000.00, 22, 'tersedia'),
(12, 'Kwetiau Goreng', 'Makanan', 18000.00, 16, 'tersedia'),
(13, 'Nasi Ayam Teriyaki', 'Makanan', 23000.00, 14, 'tersedia'),
(14, 'Kentang Goreng', 'Makanan', 12000.00, 30, 'tersedia'),
(15, 'Roti Bakar Cokelat', 'Makanan', 13000.00, 20, 'tersedia'),
(16, 'Es Teh Manis', 'Minuman', 5000.00, 50, 'tersedia'),
(17, 'Es Jeruk', 'Minuman', 8000.00, 35, 'tersedia'),
(18, 'Teh Hangat', 'Minuman', 5000.00, 40, 'tersedia'),
(19, 'Jeruk Hangat', 'Minuman', 8000.00, 30, 'tersedia'),
(20, 'Kopi Hitam', 'Minuman', 10000.00, 25, 'tersedia'),
(21, 'Kopi Susu', 'Minuman', 12000.00, 25, 'tersedia'),
(22, 'Cappuccino', 'Minuman', 15000.00, 18, 'tersedia'),
(23, 'Air Mineral', 'Minuman', 4000.00, 60, 'tersedia'),
(24, 'Es Cokelat', 'Minuman', 12000.00, 25, 'tersedia'),
(25, 'Jus Alpukat', 'Minuman', 15000.00, 20, 'tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `pegawai`
--

CREATE TABLE `pegawai` (
  `id_pegawai` int(11) NOT NULL,
  `nama_pegawai` varchar(100) NOT NULL,
  `role` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pegawai`
--

INSERT INTO `pegawai` (`id_pegawai`, `nama_pegawai`, `role`, `username`, `password`) VALUES
(1, 'Andi', 'pelayan', 'pelayan', '$2y$10$3M/R4ehKMUKwcDtdJNqWc.ptoYByesDGh7hmSCKRdZV0m.vj6N9dW'),
(2, 'Budi', 'koki', 'koki', '$2y$10$MCKaHFyGP09YyXAXs3QjeOXZdFRhLHdhN/f1xH5nEI1NxwJEnbDyK'),
(3, 'Citra', 'kasir', 'kasir', '$2y$10$XWpY98KCAwpJveM1AfpfdOnB7mYVsjyK8o4R8/1/gASP7vAkDs7Ia');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_pegawai` int(11) NOT NULL,
  `total_bayar` decimal(12,2) NOT NULL,
  `metode_bayar` varchar(20) NOT NULL,
  `status_validasi` varchar(20) NOT NULL,
  `waktu_bayar` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pesanan`, `id_pegawai`, `total_bayar`, `metode_bayar`, `status_validasi`, `waktu_bayar`) VALUES
(1, 1, 3, 16000.00, 'transfer', 'berhasil', '2026-08-08 17:13:28'),
(2, 2, 3, 8000.00, 'qris', 'berhasil', '2026-08-08 17:18:24'),
(3, 3, 3, 8000.00, 'tunai', 'berhasil', '2026-08-08 17:18:27'),
(4, 4, 3, 8000.00, 'qris', 'berhasil', '2026-08-08 17:29:46'),
(5, 5, 3, 8000.00, 'tunai', 'berhasil', '2026-08-08 17:31:07'),
(6, 6, 3, 8000.00, 'debit', 'berhasil', '2026-08-08 17:36:43'),
(7, 7, 3, 10000.00, 'qris', 'berhasil', '2026-08-08 18:22:05'),
(8, 8, 3, 8000.00, 'tunai', 'berhasil', '2026-08-08 13:25:12'),
(9, 9, 3, 10000.00, 'tunai', 'berhasil', '2026-08-08 13:26:29'),
(10, 10, 3, 18000.00, 'tunai', 'berhasil', '2026-08-08 13:26:33'),
(11, 11, 3, 24000.00, 'transfer', 'berhasil', '2026-08-08 13:26:38'),
(12, 12, 3, 36000.00, 'tunai', 'berhasil', '2026-08-08 13:55:31'),
(13, 13, 3, 18000.00, 'qris', 'berhasil', '2026-08-08 15:33:36'),
(14, 14, 3, 8000.00, 'qris', 'berhasil', '2026-08-08 15:38:07'),
(15, 17, 3, 8000.00, 'qris', 'berhasil', '2026-08-08 15:38:21');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `id_meja` int(11) NOT NULL,
  `id_pegawai` int(11) NOT NULL,
  `jumlah_pelanggan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `status_pemesanan` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_meja`, `id_pegawai`, `jumlah_pelanggan`, `tanggal`, `waktu`, `status_pemesanan`) VALUES
(1, 1, 1, 2, '2026-08-08', '12:11:12', 'selesai'),
(2, 1, 1, 1, '2026-08-08', '12:13:43', 'selesai'),
(3, 2, 1, 1, '2026-08-08', '12:17:25', 'selesai'),
(4, 1, 1, 1, '2026-08-08', '12:27:55', 'selesai'),
(5, 1, 1, 1, '2026-08-08', '12:30:07', 'selesai'),
(6, 1, 1, 1, '2026-08-08', '12:36:20', 'selesai'),
(7, 1, 1, 1, '2026-08-08', '13:21:40', 'selesai'),
(8, 1, 1, 1, '2026-08-08', '13:24:38', 'selesai'),
(9, 1, 1, 1, '2026-08-08', '13:25:39', 'selesai'),
(10, 2, 1, 2, '2026-08-08', '13:25:49', 'selesai'),
(11, 3, 1, 1, '2026-08-08', '13:25:55', 'selesai'),
(12, 3, 1, 3, '2026-08-08', '13:54:42', 'selesai'),
(13, 2, 1, 2, '2026-08-08', '14:28:41', 'selesai'),
(14, 1, 1, 1, '2026-08-08', '14:45:58', 'selesai'),
(15, 3, 1, 2, '2026-08-08', '14:53:36', 'dibatalkan'),
(16, 3, 1, 1, '2026-08-08', '15:14:04', 'dibatalkan'),
(17, 3, 1, 1, '2026-08-08', '15:17:42', 'selesai');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `fk_detail_pesanan` (`id_pesanan`),
  ADD KEY `fk_detail_menu` (`id_menu`);

--
-- Indexes for table `meja`
--
ALTER TABLE `meja`
  ADD PRIMARY KEY (`id_meja`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexes for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`id_pegawai`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `fk_pembayaran_pesanan` (`id_pesanan`),
  ADD KEY `fk_pembayaran_pegawai` (`id_pegawai`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `fk_pesanan_meja` (`id_meja`),
  ADD KEY `fk_pesanan_pegawai` (`id_pegawai`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `meja`
--
ALTER TABLE `meja`
  MODIFY `id_meja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `pegawai`
--
ALTER TABLE `pegawai`
  MODIFY `id_pegawai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `fk_detail_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`),
  ADD CONSTRAINT `fk_detail_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `fk_pembayaran_pegawai` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`),
  ADD CONSTRAINT `fk_pembayaran_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`);

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `fk_pesanan_meja` FOREIGN KEY (`id_meja`) REFERENCES `meja` (`id_meja`),
  ADD CONSTRAINT `fk_pesanan_pegawai` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
