-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2021 at 02:01 PM
-- Server version: 10.4.16-MariaDB
-- PHP Version: 7.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `login_lengkap`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_access_menu`
--

CREATE TABLE `tb_access_menu` (
  `id` int(11) NOT NULL,
  `id_role` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_access_menu`
--

INSERT INTO `tb_access_menu` (`id`, `id_role`, `id_menu`) VALUES
(1, 1, 1),
(2, 1, 2),
(6, 2, 2),
(7, 2, 3),
(9, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `tb_menu`
--

CREATE TABLE `tb_menu` (
  `id` int(11) NOT NULL,
  `menu` varchar(122) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_menu`
--

INSERT INTO `tb_menu` (`id`, `menu`) VALUES
(1, 'admin'),
(2, 'Manager'),
(3, 'Api'),
(4, 'userss');

-- --------------------------------------------------------

--
-- Table structure for table `tb_menu_list`
--

CREATE TABLE `tb_menu_list` (
  `id` int(11) NOT NULL,
  `nama_menu` varchar(122) NOT NULL,
  `url` varchar(111) NOT NULL,
  `icon` varchar(111) NOT NULL,
  `id_menu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_menu_list`
--

INSERT INTO `tb_menu_list` (`id`, `nama_menu`, `url`, `icon`, `id_menu`) VALUES
(1, 'Dashboard', 'admin/admin', 'ti-home', 1),
(2, 'Produksi', '#', 'ti-package', 2),
(3, 'Jobmix', '#', 'ti-layout-grid2-alt', 2),
(5, 'Menu Management', '#', 'ti-direction-alt', 1),
(6, 'Icons', 'admin/icon', 'ti-info-alt', 1),
(7, 'api', '#', 'ti-server', 3);

-- --------------------------------------------------------

--
-- Table structure for table `tb_role`
--

CREATE TABLE `tb_role` (
  `id` int(11) NOT NULL,
  `role` varchar(122) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_role`
--

INSERT INTO `tb_role` (`id`, `role`) VALUES
(1, 'Superadmin'),
(2, 'Manager'),
(3, 'staff');

-- --------------------------------------------------------

--
-- Table structure for table `tb_submenu`
--

CREATE TABLE `tb_submenu` (
  `id` int(11) NOT NULL,
  `submenu` varchar(122) NOT NULL,
  `url_sub` varchar(111) NOT NULL,
  `id_menu_list` int(11) NOT NULL,
  `order_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_submenu`
--

INSERT INTO `tb_submenu` (`id`, `submenu`, `url_sub`, `id_menu_list`, `order_by`) VALUES
(1, 'Pabrik Aceh', 'aceh/aceh', 2, 1),
(2, 'Pabrik Aceh', 'manager/jobmix', 3, 1),
(3, 'Pabrik Sadang', 'produksi', 2, 2),
(4, 'Menu List', 'admin/menu/menulist', 5, 2),
(5, 'Submenu', 'admin/menu/submenu', 5, 3),
(6, 'Access Menu', 'admin/menu/role', 5, 4),
(7, 'Menu Utama', 'admin/menu/menuutama', 5, 1),
(9, 'Pabrik Sadang', 'manager', 3, 2),
(10, 'api sukses', 'asa', 7, 1),
(11, 'api gagal', 'a', 7, 2),
(12, 'Pabrik Mojokerto', 'produksi', 2, 3),
(13, 'Pabrik jogja solo', 'produksi', 2, 4),
(14, 'Pabrik Mojokerto', 'jobmix', 3, 3),
(15, 'Pabrik jogja solo', 'jobmix', 3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id` int(11) NOT NULL,
  `username` varchar(122) NOT NULL,
  `email` varchar(111) NOT NULL,
  `password` varchar(233) NOT NULL,
  `image` varchar(100) NOT NULL,
  `id_role` int(11) NOT NULL,
  `date_created` int(30) NOT NULL,
  `is_active` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id`, `username`, `email`, `password`, `image`, `id_role`, `date_created`, `is_active`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$9ZoRL.vx2SGERWWTF7Dvz.PNjREFhvmRHUhGOWJ4TFDRRbUngAf/i', 'IMG02548-20151102-17131.jpg', 1, 1610222131, 1),
(2, 'manager', 'manager@gmail.com', '$2y$10$nzxPuLA3EQvy5ARIotqe6OAp7ecOoIHMnWe28iMY6K89S.Fpvj9WC', 'IMG02548-20151102-1713.jpg', 2, 1610528928, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_access_menu`
--
ALTER TABLE `tb_access_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_menu`
--
ALTER TABLE `tb_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_menu_list`
--
ALTER TABLE `tb_menu_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_menu` (`id_menu`);

--
-- Indexes for table `tb_role`
--
ALTER TABLE `tb_role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_submenu`
--
ALTER TABLE `tb_submenu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_access_menu`
--
ALTER TABLE `tb_access_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tb_menu`
--
ALTER TABLE `tb_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_menu_list`
--
ALTER TABLE `tb_menu_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tb_role`
--
ALTER TABLE `tb_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_submenu`
--
ALTER TABLE `tb_submenu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_menu_list`
--
ALTER TABLE `tb_menu_list`
  ADD CONSTRAINT `tb_menu_list_ibfk_1` FOREIGN KEY (`id_menu`) REFERENCES `tb_menu` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
