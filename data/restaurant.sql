-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 10, 2025 at 02:04 PM
-- Server version: 8.0.30
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restaurant`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail` int NOT NULL,
  `id_pesanan` int DEFAULT NULL,
  `id_menu` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meja`
--

CREATE TABLE `meja` (
  `id_meja` int NOT NULL,
  `status` enum('kosong','terisi','selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'kosong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meja`
--

INSERT INTO `meja` (`id_meja`, `status`) VALUES
(1, 'kosong'),
(2, 'kosong'),
(3, 'kosong'),
(4, 'kosong'),
(5, 'kosong'),
(6, 'kosong'),
(7, 'kosong'),
(8, 'kosong'),
(9, 'kosong'),
(10, 'kosong'),
(11, 'kosong'),
(12, 'kosong');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int NOT NULL,
  `nama_menu` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `harga` int NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `gambar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stok` int NOT NULL,
  `status_tersedia` enum('habis','tersedia') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_chef` int DEFAULT NULL,
  `kategori` enum('makanan','minuman','dessert') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'makanan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id_menu`, `nama_menu`, `harga`, `deskripsi`, `gambar`, `stok`, `status_tersedia`, `id_chef`, `kategori`) VALUES
(19, 'Sirloin Entrée Steak', 118000, NULL, 'menu_686fb9799ff2d.png', 13, 'tersedia', 1, 'makanan'),
(20, 'Rib Eye Entrée Steak', 118000, NULL, 'menu_686fbadb863c4.png', 15, 'tersedia', 1, 'makanan'),
(21, 'Tenderloin Entrée Steak', 118000, NULL, 'menu_686fbb449bd00.png', 15, 'tersedia', 1, 'makanan'),
(22, 'US Prime Tomahawk', 1950000, NULL, 'menu_686fbbb79053c.png', 15, 'tersedia', 1, 'makanan'),
(23, 'US Prime T-Bone', 950000, NULL, 'menu_686fbbf80beff.png', 15, 'tersedia', 1, 'makanan'),
(24, 'Garlic Roasted Chicken', 110000, NULL, 'menu_686fbc9c38476.png', 15, 'tersedia', 1, 'makanan'),
(25, 'Chicken Teriyaki', 75000, NULL, 'menu_686fbcd432ffb.png', 15, 'tersedia', 1, 'makanan'),
(26, 'Asian Chicken BBQ', 75000, NULL, 'menu_686fbd097078c.png', 15, 'tersedia', 1, 'makanan'),
(27, 'Prime Short Ribs', 370000, NULL, 'menu_686fbdb67da09.png', 15, 'tersedia', 1, 'makanan'),
(28, 'Wagyu A5 Steak', 270000, NULL, 'menu_686fbe22edbdc.png', 15, 'tersedia', 1, 'makanan'),
(29, 'Avocado Juice', 35000, NULL, 'menu_686fbe8a797a5.jpeg', 15, 'tersedia', 1, 'minuman'),
(30, 'Banana Smoothies', 43000, NULL, 'menu_686fbebe86833.jpeg', 15, 'tersedia', 1, 'minuman'),
(31, 'Frappe Caramel Latte', 46000, NULL, 'menu_686fc0a5c6f8c.jpeg', 15, 'tersedia', 1, 'minuman'),
(32, 'Iced Tea', 28000, NULL, 'menu_686fc0b9bce7b.jpeg', 13, 'tersedia', 1, 'minuman'),
(33, 'Water Mineral', 27000, NULL, 'menu_686fc0cfbc222.png', 15, 'tersedia', 1, 'minuman'),
(34, 'Vanilla Milkshake', 39000, NULL, 'menu_686fc0ed397f2.png', 15, 'tersedia', 1, 'minuman'),
(35, 'Espresso', 21000, NULL, 'menu_686fc10e180d1.png', 15, 'tersedia', 1, 'minuman'),
(36, 'Coffee Aren', 36000, NULL, 'menu_686fc126baeb6.png', 15, 'tersedia', 1, 'minuman'),
(37, 'Vietnam Drip', 31000, NULL, 'menu_686fc1451ea98.png', 15, 'tersedia', 1, 'minuman'),
(38, 'V60', 31000, NULL, 'menu_686fc14ff06a3.png', 15, 'tersedia', 1, 'minuman'),
(39, 'Ice Cream', 12000, NULL, 'menu_686fc17f21d0c.png', 14, 'tersedia', 1, 'dessert'),
(40, 'Chocolate Lava', 59000, NULL, 'menu_686fc1a4af6ba.png', 15, 'tersedia', 1, 'dessert'),
(41, 'Banana Split', 49000, NULL, 'menu_686fc1c486d01.png', 15, 'tersedia', 1, 'dessert'),
(42, 'Choco Mille Crepes', 69000, NULL, 'menu_686fc1fa034b7.png', 15, 'tersedia', 1, 'dessert'),
(43, 'Brownies Cheezie Sundae', 49000, NULL, 'menu_686fc2205d336.png', 15, 'tersedia', 1, 'dessert'),
(44, 'Classic Tiramisu', 69000, NULL, 'menu_686fc2515fe4d.png', 15, 'tersedia', 1, 'dessert'),
(45, 'Creme Brulee', 59000, NULL, 'menu_686fc278042cc.png', 15, 'tersedia', 1, 'dessert'),
(46, 'Cheesecake', 75000, NULL, 'menu_686fc2b69e8f7.png', 15, 'tersedia', 1, 'dessert'),
(47, 'Chocolate Caramel Cake', 45000, NULL, 'menu_686fc35000c96.png', 15, 'tersedia', 1, 'dessert'),
(48, 'Waffle', 35000, NULL, 'menu_686fc37e28b19.png', 15, 'tersedia', 1, 'dessert');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int NOT NULL,
  `id_meja` int DEFAULT NULL,
  `waktu_masuk` datetime DEFAULT CURRENT_TIMESTAMP,
  `waktu_keluar` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int NOT NULL,
  `id_pelanggan` int DEFAULT NULL,
  `id_meja` int DEFAULT NULL,
  `id_kasir` int DEFAULT NULL,
  `total` int DEFAULT NULL,
  `waktu_bayar` datetime DEFAULT CURRENT_TIMESTAMP,
  `metode_bayar` enum('tunai','non-tunai') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int NOT NULL,
  `id_pelanggan` int DEFAULT NULL,
  `id_meja` int DEFAULT NULL,
  `waktu_pesan` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('menunggu','dimasak','siap','diantar','dibayar') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('chef','waiter','cashier','owner') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_lengkap` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password_hash`, `role`, `nama_lengkap`) VALUES
(1, 'chef001', 'admin', 'chef', 'Aldo'),
(2, 'pelayan002', 'admin', 'waiter', 'Yogi'),
(3, 'kasir003', 'admin', 'cashier', 'Dimas'),
(4, 'owner004', 'admin', 'owner', 'Randi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_menu` (`id_menu`);

--
-- Indexes for table `meja`
--
ALTER TABLE `meja`
  ADD PRIMARY KEY (`id_meja`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`),
  ADD KEY `id_chef` (`id_chef`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD KEY `id_meja` (`id_meja`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pelanggan` (`id_pelanggan`),
  ADD KEY `id_meja` (`id_meja`),
  ADD KEY `fk_kasir` (`id_kasir`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `id_pelanggan` (`id_pelanggan`),
  ADD KEY `id_meja` (`id_meja`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `meja`
--
ALTER TABLE `meja`
  MODIFY `id_meja` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`),
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`);

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`id_chef`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD CONSTRAINT `pelanggan_ibfk_1` FOREIGN KEY (`id_meja`) REFERENCES `meja` (`id_meja`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `fk_kasir` FOREIGN KEY (`id_kasir`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`),
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`id_meja`) REFERENCES `meja` (`id_meja`);

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`),
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`id_meja`) REFERENCES `meja` (`id_meja`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
