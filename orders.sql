-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2025 at 11:48 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tastyphv1`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `confirmed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `supplier_id`, `seller_id`, `payment_method`, `payment_proof`, `total_price`, `order_date`, `status`, `confirmed`) VALUES
(1, 1, 1, NULL, 'cash', NULL, 200.00, '2025-06-09 02:04:36', 'delivered', 0),
(2, 1, 1, NULL, 'cash', NULL, 1790.00, '2025-06-09 02:05:09', 'delivered', 0),
(3, 3, 1, NULL, 'cash', NULL, 40000.00, '2025-06-09 02:07:21', 'cancelled', 0),
(4, 1, 1, NULL, 'cash', NULL, 2182.00, '2025-06-09 15:36:25', 'delivered', 0),
(5, 1, 1, NULL, 'cash', NULL, 200.00, '2025-06-11 15:04:02', 'delivered', 0),
(6, 3, 1, NULL, 'cash', NULL, 213.00, '2025-06-11 15:21:14', 'delivered', 0),
(7, 1, 1, NULL, 'cash', NULL, 400.00, '2025-06-11 17:42:36', 'delivered', 0),
(8, 1, 1, NULL, 'cash', NULL, 266.00, '2025-06-11 19:38:30', 'cancelled', 0),
(9, 1, 1, NULL, 'cash', NULL, 1757.00, '2025-06-11 20:13:20', 'delivered', 0),
(10, 1, 1, NULL, 'cash', NULL, 978.00, '2025-06-11 22:13:41', 'delivered', 0),
(11, 3, 1, NULL, 'cash', NULL, 6420.00, '2025-06-11 22:18:47', 'delivered', 0),
(12, 3, 1, NULL, 'cash', NULL, 1228.00, '2025-06-11 22:29:39', 'delivered', 0),
(13, 3, 1, NULL, 'cash', NULL, 1900.00, '2025-06-11 22:33:49', 'delivered', 0),
(14, 1, 1, NULL, 'cash', NULL, 50.00, '2025-06-13 14:27:48', 'delivered', 0),
(15, 1, 1, NULL, 'cash', NULL, 200.00, '2025-06-16 20:36:20', 'delivered', 0),
(16, 1, 1, NULL, 'cash', NULL, 526.00, '2025-06-16 20:57:21', 'delivered', 0),
(17, 1, 1, NULL, 'cash', NULL, 1065.00, '2025-06-16 20:58:24', 'delivered', 0),
(18, 1, 1, NULL, 'cash', NULL, 852.00, '2025-06-16 21:06:04', 'delivered', 0),
(19, 1, 1, NULL, 'cash', NULL, 263.00, '2025-06-16 21:21:22', 'delivered', 0),
(20, 1, 1, NULL, 'cash', NULL, 386.00, '2025-06-16 21:24:35', 'delivered', 0),
(21, 2, 1, NULL, 'cash', NULL, 1321.00, '2025-06-16 21:26:05', 'cancelled', 0),
(22, 1, 1, NULL, 'cash', NULL, 1045.00, '2025-06-16 21:26:39', 'delivered', 0),
(23, 1, 1, NULL, 'cash', NULL, 1707.00, '2025-06-16 21:32:35', 'delivered', 0),
(24, 1, 1, NULL, 'cash', NULL, 200.00, '2025-06-20 12:27:07', 'delivered', 0),
(25, 1, 2, NULL, 'cash', NULL, 2976.00, '2025-06-23 00:08:06', 'delivered', 0),
(26, 1, 2, NULL, 'cash', NULL, 400.00, '2025-06-23 01:49:57', 'delivered', 0),
(27, 1, 2, NULL, 'cash', NULL, 2826.00, '2025-06-23 02:54:20', 'delivered', 0),
(28, 1, 2, NULL, 'cash', NULL, 132.00, '2025-06-23 03:06:57', 'delivered', 0),
(29, 1, 2, NULL, 'cash', NULL, 348.00, '2025-06-23 03:13:50', 'delivered', 0),
(30, 1, 2, NULL, 'cash', NULL, 2526.00, '2025-06-23 03:26:05', 'delivered', 0),
(31, 1, 2, NULL, 'cash', NULL, 132.00, '2025-06-23 03:41:55', 'delivered', 0),
(32, 1, 2, NULL, 'cash', NULL, 460.00, '2025-06-25 02:47:44', 'pending', 0),
(33, 1, 1, NULL, 'cash', NULL, 200.00, '2025-06-25 16:22:37', 'delivered', 0),
(34, 1, 2, NULL, 'card', NULL, 116.00, '2025-06-27 15:56:18', 'pending', 0),
(35, 2, NULL, NULL, 'cash', NULL, 6.00, '2025-07-02 17:38:22', 'pending', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_applications` (`supplier_id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`seller_id`) REFERENCES `seller_applications` (`seller_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
