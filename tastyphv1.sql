-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2025 at 11:15 PM
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
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `usertype` varchar(50) NOT NULL DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `email`, `usertype`, `created_at`) VALUES
(1, 'admin', '$2y$10$biY6vnGyZwGpOwG9f95MIuqZNTCEe7slAYttFuquxMGK6zWzbeIga', 'admin@gmail.com', 'admin', '2025-05-31 06:08:59'),
(3, 'admin123', '$2y$10$Uihyp8OvLwPrT/8mSPpu5.136qQcW7DmJxLPiav4bk/RuFKFS2yI6', 'admin1@gmail.com', 'admin', '2025-05-31 06:19:08');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `supplier_id`, `name`, `slug`, `description`, `image_url`, `is_active`, `created_at`, `updated_at`) VALUES
(4, 1, 'Sugar', 'sugar', 'all kinds of sugar', 'uploads/categories/category_2_1748798840.jpg', 1, '2025-06-02 01:27:20', '2025-06-02 01:27:20'),
(5, 1, 'Egg', 'egg', 'all kinds of egg', 'uploads/categories/category_2_1748799380.jpg', 1, '2025-06-02 01:36:20', '2025-06-02 01:36:20'),
(9, 1, 'Rice Flour', 'Rice Flour', 'All kinds of Rice Flour', 'uploads/categories/category_2_1748799484.jpg', 1, '2025-06-02 01:38:04', '2025-06-02 01:38:04'),
(12, 1, 'Salt', 'Salt', 'All kinds of Salt', 'uploads/categories/category_2_1748799688.jpg', 1, '2025-06-02 01:41:28', '2025-06-02 01:41:28'),
(13, 1, 'Baking Powder', 'Baking Powder', 'All kinds of Baking Powder', 'uploads/categories/category_2_1748800065.jpg', 1, '2025-06-02 01:47:45', '2025-06-02 01:47:45'),
(14, 1, 'Butter', 'Butter', 'All kinds of Butter', 'uploads/categories/category_2_1748800180.jpg', 1, '2025-06-02 01:49:40', '2025-06-02 01:49:40'),
(15, 1, 'Fresh Milk', 'Fresh Milk', 'All kinds of Fresh Milk', 'uploads/categories/category_2_1748800261.jpg', 1, '2025-06-02 01:51:01', '2025-06-02 01:51:01'),
(16, 1, 'Cheese', 'Cheese', 'All kinds of Cheese', 'uploads/categories/category_2_1748800427.jpg', 1, '2025-06-02 01:53:47', '2025-06-02 01:53:47'),
(17, 1, 'Coconut Milk', 'Coconut Milk', 'All kinds of Coconut Milk', 'uploads/categories/category_2_1748800548.jpg', 1, '2025-06-02 01:55:48', '2025-06-02 01:55:48'),
(18, 1, 'Grated Coconut', 'Grated Coconut', 'All kinds of Grated Coconut', 'uploads/categories/category_2_1748800684.jpg', 1, '2025-06-02 01:58:04', '2025-06-02 01:58:04'),
(19, 1, 'Banana Leaves', 'Banana Leaves', 'All kinds of Banana Leaves', 'uploads/categories/category_2_1748800768.jpg', 1, '2025-06-02 01:59:28', '2025-06-02 01:59:28');

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `ingredient_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `ingredient_name` varchar(150) NOT NULL,
  `slug` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `quantity_value` decimal(10,2) NOT NULL,
  `unit_type` enum('g','kg','ml','l','pcs','pack','bottle','can') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`ingredient_id`, `supplier_id`, `category_id`, `ingredient_name`, `slug`, `description`, `image_url`, `price`, `discount_price`, `stock`, `quantity_value`, `unit_type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 17, 'Coconut Milk', 'coconut-milk', 'Coconut Milk', 'uploads/ingredients/ingredient_1_1748803467.jpg', 50.00, NULL, 29, 165.00, 'ml', 1, '2025-06-02 02:44:27', '2025-06-02 03:54:51'),
(3, 1, 16, 'Cheese', 'cheese', 'Cheese', 'uploads/ingredients/ingredient_1_1748812131.jpg', 222.00, NULL, 121, 430.00, 'g', 1, '2025-06-02 05:08:51', '2025-06-02 05:08:51');

-- --------------------------------------------------------

--
-- Table structure for table `ingredient_variants`
--

CREATE TABLE `ingredient_variants` (
  `variant_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `variant_name` varchar(150) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `quantity_value` decimal(10,2) NOT NULL,
  `unit_type` enum('g','kg','ml','l','pcs','pack','bottle','can') NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredient_variants`
--

INSERT INTO `ingredient_variants` (`variant_id`, `ingredient_id`, `supplier_id`, `variant_name`, `price`, `discount_price`, `stock`, `quantity_value`, `unit_type`, `image_url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '213', 123.00, 123.00, 123, 123.00, 'kg', 'uploads/variants/variant_1_1748809709.jpg', 1, '2025-06-02 04:28:29', '2025-06-02 04:28:29'),
(2, 1, 1, '123', 213.00, 123.00, 123, 123.00, 'g', 'uploads/variants/variant_1_1748812068.jpg', 1, '2025-06-02 05:07:48', '2025-06-02 05:07:48');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_text` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `reply_to` int(11) DEFAULT NULL,
  `pinned` tinyint(1) DEFAULT 0,
  `is_read` tinyint(1) DEFAULT 0,
  `sent_at` datetime DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `sender_id`, `receiver_id`, `message_text`, `image_url`, `reply_to`, `pinned`, `is_read`, `sent_at`, `deleted_at`) VALUES
(42, 2, 1, 'asd', NULL, NULL, 0, 1, '2025-06-01 22:47:37', NULL),
(43, 2, 1, 'asd', NULL, NULL, 0, 1, '2025-06-01 22:47:43', NULL),
(44, 2, 1, 'asd', NULL, NULL, 0, 1, '2025-06-01 22:50:14', NULL),
(45, 2, 1, 'asdsadasdasd', NULL, NULL, 0, 1, '2025-06-01 22:50:54', NULL),
(46, 2, 1, 'asd', NULL, NULL, 0, 1, '2025-06-01 22:54:40', NULL),
(47, 2, 1, 'asd', NULL, NULL, 0, 1, '2025-06-01 23:07:30', NULL),
(48, 2, 1, 'asd', NULL, NULL, 0, 1, '2025-06-01 23:08:17', NULL),
(49, 3, 1, 'asd', NULL, NULL, 0, 1, '2025-06-01 23:10:40', NULL),
(50, 1, 3, 'sad', NULL, NULL, 0, 1, '2025-06-01 23:17:32', NULL),
(51, 3, 1, 'asd', NULL, NULL, 0, 1, '2025-06-01 23:17:43', NULL),
(52, 3, 1, 'hoi', NULL, NULL, 0, 1, '2025-06-01 23:17:49', NULL),
(53, 1, 3, 'yeah', NULL, NULL, 0, 1, '2025-06-01 23:18:02', NULL),
(54, 3, 1, 'wala langi love yo u basuhdfaiuw fb', NULL, NULL, 0, 1, '2025-06-01 23:18:12', NULL),
(55, 1, 3, 'gagi ajjaaj', NULL, NULL, 0, 1, '2025-06-01 23:18:16', NULL),
(56, 1, 3, 'asd', NULL, NULL, 0, 1, '2025-06-01 23:21:07', NULL),
(57, 3, 1, 'asd', NULL, NULL, 0, 1, '2025-06-01 23:21:25', NULL),
(58, 3, 1, 'haah', NULL, NULL, 0, 1, '2025-06-01 23:21:37', NULL),
(59, 3, 1, 'hi', NULL, NULL, 0, 1, '2025-06-01 23:48:57', NULL),
(60, 1, 3, 'hello', NULL, NULL, 0, 1, '2025-06-01 23:49:07', NULL),
(61, 3, 1, 'hahha', NULL, NULL, 0, 1, '2025-06-01 23:49:10', NULL),
(62, 1, 3, 'ngi', NULL, NULL, 0, 1, '2025-06-01 23:49:12', NULL),
(63, 3, 1, 'real time??', NULL, NULL, 0, 1, '2025-06-01 23:49:18', NULL),
(64, 1, 3, 'yeah', NULL, NULL, 0, 1, '2025-06-01 23:49:21', NULL),
(65, 3, 1, 'naol', NULL, NULL, 0, 1, '2025-06-01 23:49:24', NULL),
(66, 1, 3, 'hahaha', NULL, NULL, 0, 1, '2025-06-01 23:49:26', NULL),
(67, 3, 1, 'yawa ka', NULL, NULL, 0, 1, '2025-06-01 23:49:30', NULL),
(68, 1, 3, 'shut up nigger', NULL, NULL, 0, 1, '2025-06-01 23:49:33', NULL),
(69, 3, 1, '👍👌👌👌', NULL, NULL, 0, 1, '2025-06-01 23:49:46', NULL),
(70, 1, 3, 'lol bye', NULL, NULL, 0, 1, '2025-06-01 23:49:54', NULL),
(71, 3, 1, 'k', NULL, NULL, 0, 1, '2025-06-01 23:49:58', NULL),
(72, 1, 2, 'sad', NULL, NULL, 0, 1, '2025-06-02 00:04:09', NULL),
(73, 1, 3, 'asd', NULL, NULL, 0, 1, '2025-06-02 00:04:12', NULL),
(74, 1, 2, 'asd', NULL, NULL, 0, 1, '2025-06-02 00:06:06', NULL),
(75, 1, 3, 'asd', NULL, NULL, 0, 1, '2025-06-02 00:06:08', NULL),
(76, 1, 2, 'asd', NULL, NULL, 0, 1, '2025-06-02 00:09:54', NULL),
(77, 3, 1, 'asd', NULL, NULL, 0, 1, '2025-06-02 00:14:11', NULL),
(78, 3, 1, 'asdasdas', NULL, NULL, 0, 1, '2025-06-02 00:16:21', NULL),
(79, 1, 3, 'asdsad', NULL, NULL, 0, 1, '2025-06-02 00:16:34', NULL),
(80, 3, 1, 'asd', NULL, NULL, 0, 1, '2025-06-02 00:17:12', NULL),
(81, 1, 3, 'asdasd', NULL, NULL, 0, 1, '2025-06-02 00:17:21', NULL),
(82, 3, 1, 'asdas', NULL, NULL, 0, 1, '2025-06-02 00:17:26', NULL),
(83, 1, 3, 'asd', NULL, NULL, 0, 1, '2025-06-02 00:18:23', NULL),
(84, 1, 3, 'sadas', NULL, NULL, 0, 1, '2025-06-02 00:18:39', NULL),
(85, 3, 1, 'asdasd', NULL, NULL, 0, 1, '2025-06-02 00:19:07', NULL),
(86, 1, 3, 'asdsad', NULL, NULL, 0, 1, '2025-06-02 00:21:03', NULL),
(87, 3, 1, 'asds', NULL, NULL, 0, 1, '2025-06-02 00:21:11', NULL),
(88, 1, 3, 'asdas', NULL, NULL, 0, 1, '2025-06-02 00:22:38', NULL),
(89, 3, 1, 'asdas', NULL, NULL, 0, 1, '2025-06-02 00:22:44', NULL),
(90, 3, 1, 'asdas', NULL, NULL, 0, 1, '2025-06-02 00:22:49', NULL),
(91, 3, 1, 'asd', NULL, NULL, 0, 1, '2025-06-02 00:22:54', NULL),
(92, 3, 1, 'asdsa', NULL, NULL, 0, 1, '2025-06-02 00:23:16', NULL),
(93, 1, 3, 'asdasd', NULL, NULL, 0, 1, '2025-06-02 00:23:22', NULL),
(94, 1, 3, 'asdsa', NULL, NULL, 0, 1, '2025-06-02 00:23:34', NULL),
(95, 3, 1, 'asdsa', NULL, NULL, 0, 1, '2025-06-02 00:23:41', NULL),
(96, 1, 3, 'asdsa', NULL, NULL, 0, 1, '2025-06-02 00:23:51', NULL),
(97, 3, 1, 'asdasd', NULL, NULL, 0, 1, '2025-06-02 00:23:57', NULL),
(98, 1, 3, 'asd', NULL, NULL, 0, 1, '2025-06-02 00:24:05', NULL),
(99, 3, 1, 'asd', NULL, NULL, 0, 1, '2025-06-02 00:24:39', NULL),
(100, 1, 3, 'asdsad', NULL, NULL, 0, 1, '2025-06-02 00:24:46', NULL),
(101, 3, 1, 'sadasd', NULL, NULL, 0, 1, '2025-06-02 00:24:51', NULL),
(102, 1, 3, 'asdas', NULL, NULL, 0, 1, '2025-06-02 00:25:06', NULL),
(103, 1, 3, 'asdas', NULL, NULL, 0, 1, '2025-06-02 00:25:15', NULL),
(104, 1, 3, 'asd', NULL, NULL, 0, 1, '2025-06-02 00:25:59', NULL),
(105, 3, 1, 'asd', NULL, NULL, 0, 1, '2025-06-02 00:26:06', NULL),
(106, 1, 3, 'asdasd', NULL, NULL, 0, 1, '2025-06-02 00:26:14', NULL),
(107, 3, 1, 'asdas', NULL, NULL, 0, 1, '2025-06-02 00:28:56', NULL),
(108, 1, 3, 'asdas', NULL, NULL, 0, 1, '2025-06-02 00:29:42', NULL),
(109, 3, 1, 'asdsad', NULL, NULL, 0, 1, '2025-06-02 00:29:48', NULL),
(110, 1, 3, 'ehh', NULL, NULL, 0, 1, '2025-06-02 00:29:54', NULL),
(111, 3, 1, 'yey', NULL, NULL, 0, 1, '2025-06-02 00:30:00', NULL),
(112, 1, 3, 'haha', NULL, NULL, 0, 1, '2025-06-02 00:30:07', NULL),
(113, 1, 3, 'maem wala baya mi ana na baligya ha ayaw sig yaga yaga saakoa', NULL, NULL, 0, 1, '2025-06-02 00:33:07', NULL),
(114, 3, 1, 'ayy hahahahah sori', NULL, NULL, 0, 1, '2025-06-02 00:33:22', NULL),
(115, 2, 3, 'asd', NULL, NULL, 0, 1, '2025-06-02 01:05:07', NULL),
(116, 3, 2, 'hi', NULL, NULL, 0, 1, '2025-06-02 01:05:18', NULL),
(117, 2, 3, 'yeah i', NULL, NULL, 0, 1, '2025-06-02 01:05:26', NULL),
(118, 3, 2, 'asdas', NULL, NULL, 0, 1, '2025-06-02 01:05:32', NULL),
(119, 3, 2, 'asd', NULL, NULL, 0, 1, '2025-06-02 01:05:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `sender_id`, `receiver_id`, `message`, `is_read`, `created_at`) VALUES
(2, NULL, 1, 'Your seller application was submitted successfully.', 1, '2025-05-31 19:25:51'),
(3, NULL, 2, 'Your supplier application was submitted successfully.', 1, '2025-05-31 21:42:54'),
(4, NULL, 3, 'Your seller application was submitted successfully.', 1, '2025-06-01 23:12:56');

-- --------------------------------------------------------

--
-- Table structure for table `pre_order_list`
--

CREATE TABLE `pre_order_list` (
  `pre_order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `preferred_date` date DEFAULT NULL,
  `preferred_time` varchar(50) DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `status` enum('pending','approved','declined','delivered') DEFAULT 'pending',
  `request_date` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `full_address` text DEFAULT NULL,
  `decline_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pre_order_list`
--

INSERT INTO `pre_order_list` (`pre_order_id`, `user_id`, `seller_id`, `product_name`, `quantity`, `unit`, `preferred_date`, `preferred_time`, `additional_notes`, `status`, `request_date`, `updated_at`, `full_address`, `decline_reason`) VALUES
(1, 1, 4, 'bibingka', 12, 'pcs', '2025-06-02', '2:00pm', 'asd', 'approved', '2025-06-01 16:56:18', '2025-06-01 19:47:42', 'De Guzman Street, Purok 20, Crossing Bayabas, Toril District, Davao City, Davao Region, 8025, Philippines', NULL),
(2, 1, 4, 'asd', 123, 'pcs', '2025-06-01', '3:00pm', 'asdasddddddddddddddddddddddddddddddddddddddddddddddddddddasdasddddddddddddddddddddddddddddddddddddddddddddddddddddasdasddddddddddddddddddddddddddddddddddddddddddddddddddddasdasdddddddddddddddddddddddddddddddddddddddddddddddddddd', 'approved', '2025-06-01 18:20:39', '2025-06-01 19:47:38', 'Cosmopolitan Funeral Chapel, De Guzman Street, Purok 20, Crossing Bayabas, Toril District, Davao City, Davao Region, 8025, Philippines', NULL),
(3, 1, 4, 'asd', 123, 'pcs', '2025-06-01', '3:00pm', 'asdasddddddddddddddddddddddddddddddddddddddddddddddddddddasdasddddddddddddddddddddddddddddddddddddddddddddddddddddasdasddddddddddddddddddddddddddddddddddddddddddddddddddddasdasdddddddddddddddddddddddddddddddddddddddddddddddddddd', 'approved', '2025-06-01 18:21:13', '2025-06-01 19:47:35', 'Cosmopolitan Funeral Chapel, De Guzman Street, Purok 20, Crossing Bayabas, Toril District, Davao City, Davao Region, 8025, Philippines', NULL),
(4, 1, 4, 'asd', 123, 'pcs', '2025-06-01', '3:00pm', 'asdasddddddddddddddddddddddddddddddddddddddddddddddddddddasdasddddddddddddddddddddddddddddddddddddddddddddddddddddasdasddddddddddddddddddddddddddddddddddddddddddddddddddddasdasdddddddddddddddddddddddddddddddddddddddddddddddddddd', 'approved', '2025-06-01 18:21:19', '2025-06-01 19:47:31', 'Cosmopolitan Funeral Chapel, De Guzman Street, Purok 20, Crossing Bayabas, Toril District, Davao City, Davao Region, 8025, Philippines', NULL),
(5, 1, 4, 'asd', 123, 'pcs', '2025-06-01', '3:00pm', 'asdasddddddddddddddddddddddddddddddddddddddddddddddddddddasdasddddddddddddddddddddddddddddddddddddddddddddddddddddasdasddddddddddddddddddddddddddddddddddddddddddddddddddddasdasdddddddddddddddddddddddddddddddddddddddddddddddddddd', 'approved', '2025-06-01 18:21:24', '2025-06-01 19:47:27', 'Cosmopolitan Funeral Chapel, De Guzman Street, Purok 20, Crossing Bayabas, Toril District, Davao City, Davao Region, 8025, Philippines', NULL),
(6, 1, 4, 'dsad', 1, '1', '2025-06-14', '3:00pm', 'dsa', 'declined', '2025-06-01 18:41:12', '2025-06-01 19:27:41', 'Cosmopolitan Funeral Chapel, De Guzman Street, Purok 20, Crossing Bayabas, Toril District, Davao City, Davao Region, 8025, Philippines', 'iw'),
(7, 1, 4, 'dsad', 1, '1', '2025-06-14', '3:00pm', 'dsa', 'approved', '2025-06-01 18:42:41', '2025-06-01 19:27:29', 'Cosmopolitan Funeral Chapel, De Guzman Street, Purok 20, Crossing Bayabas, Toril District, Davao City, Davao Region, 8025, Philippines', NULL),
(8, 2, 4, 'ss', 1, '1', '2025-06-06', '3:00pm', 'asd', 'declined', '2025-06-01 18:43:06', '2025-06-01 19:17:46', 'Bato-Kilate-Tagurano Road, Purok 9, Bato, Toril District, Davao City, Davao Region, 8000, Philippines', 'yawa HAHAHAHA'),
(9, 2, 4, 'turon', 5, 'pcs', '2025-06-01', '3:00pm', 'naka selopin pls', 'approved', '2025-06-01 19:06:24', '2025-06-01 19:17:31', 'Bato-Kilate-Tagurano Road, Purok 9, Bato, Toril District, Davao City, Davao Region, 8000, Philippines', NULL),
(10, 1, 4, 'arra', 1, 'pcs', '2025-06-06', '3:00pm', 'asd', 'declined', '2025-06-01 20:21:32', '2025-06-01 20:21:46', 'Cosmopolitan Funeral Chapel, De Guzman Street, Purok 20, Crossing Bayabas, Toril District, Davao City, Davao Region, 8025, Philippines', 'di diay ko hahaha'),
(11, 2, 4, 'asd', 123, 'asd', '0000-00-00', '', 'sad', 'pending', '2025-06-01 22:25:18', '2025-06-01 22:25:18', 'Bato-Kilate-Tagurano Road, Purok 9, Bato, Toril District, Davao City, Davao Region, 8000, Philippines', NULL),
(12, 3, 4, 's', 1, 's', '2025-06-13', '3:00pm', 's', 'pending', '2025-06-01 23:17:12', '2025-06-01 23:17:12', 'Cebu North Road, Lugo, Cebu, Central Visayas, 6008, Philippines', NULL),
(13, 3, 4, 'otn', 21, 'pcs', '2025-06-04', '3:00pm', 'asd', 'pending', '2025-06-02 00:28:12', '2025-06-02 00:28:12', 'Cebu North Road, Lugo, Cebu, Central Visayas, 6008, Philippines', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `seller_applications`
--

CREATE TABLE `seller_applications` (
  `seller_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `store_address` text DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `full_address` text DEFAULT NULL,
  `business_permit` varchar(255) DEFAULT NULL,
  `health_permit` varchar(255) DEFAULT NULL,
  `application_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `profile_pics` varchar(255) DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `last_active` datetime DEFAULT NULL,
  `store_status` enum('active','inactive') DEFAULT 'inactive',
  `is_public` tinyint(1) DEFAULT 0,
  `cover_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seller_applications`
--

INSERT INTO `seller_applications` (`seller_id`, `user_id`, `business_name`, `description`, `store_address`, `latitude`, `longitude`, `full_address`, `business_permit`, `health_permit`, `application_date`, `status`, `profile_pics`, `reviewed_by`, `reviewed_at`, `last_active`, `store_status`, `is_public`, `cover_photo`) VALUES
(4, 1, 'Aaron\'s kakanin', 'asd', 'Bato-Kilate-Tagurano Road, Purok 9, Bato, Toril District, Davao City, Davao Region, 8000, Philippines', 7.0315146, 125.4782009, 'Bato-Kilate-Tagurano Road, Purok 9, Bato, Toril District, Davao City, Davao Region, 8000, Philippines', 'permit_1748690751_683ae73f22ccb.jpg', 'health_1748690751_683ae73f22fe3.jpg', '2025-05-31 19:25:51', 'approved', 'uploads/seller/profile_1_1748758548.jpg', 3, '2025-06-01 11:50:39', NULL, 'active', 1, 'uploads/seller/cover_1_1748758217.png'),
(5, 3, 'Miki\'s Delicious Kakanin', 'i love you', 'Ward 1-Calajoan Access Road, Calajo-an, Minglanilla, Cebu, Central Visayas, 6064, Philippines', 10.2412421, 123.8017864, 'Ward 1-Calajoan Access Road, Calajo-an, Minglanilla, Cebu, Central Visayas, 6064, Philippines', 'permit_1748790776_683c6df8c2a5d.png', 'health_1748790776_683c6df8c2da7.jpg', '2025-06-01 23:12:56', 'approved', 'uploads/seller/profile_3_1748790960.jpg', 3, '2025-06-01 23:13:33', NULL, 'active', 1, 'uploads/seller/cover_3_1748790945.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_applications`
--

CREATE TABLE `supplier_applications` (
  `supplier_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `store_address` varchar(255) NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `full_address` text DEFAULT NULL,
  `business_license` varchar(255) DEFAULT NULL,
  `application_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `profile_pics` varchar(255) DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `last_active` datetime DEFAULT NULL,
  `store_status` enum('active','inactive') DEFAULT 'inactive',
  `is_public` tinyint(1) DEFAULT 0,
  `cover_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier_applications`
--

INSERT INTO `supplier_applications` (`supplier_id`, `user_id`, `business_name`, `description`, `store_address`, `latitude`, `longitude`, `full_address`, `business_license`, `application_date`, `status`, `profile_pics`, `reviewed_by`, `reviewed_at`, `last_active`, `store_status`, `is_public`, `cover_photo`) VALUES
(1, 2, 'Kurumi\'s Palengke', 'asd', 'Riverside-Bacaca Road, Purok 27, Ma-a, Talomo District, Davao City, Davao Region, 8000, Philippines', 7.0809619, 125.5842018, 'Riverside-Bacaca Road, Purok 27, Ma-a, Talomo District, Davao City, Davao Region, 8000, Philippines', 'license_1748698974_683b075e41b7a.png', '2025-05-31 21:42:54', 'approved', 'uploads/supplier/profile_2_1748797143.jpg', 3, '2025-06-01 11:50:35', NULL, 'active', 1, 'uploads/supplier/cover_2_1748797059.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `contact_number` varchar(15) NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `streetname` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `usertype` enum('user','seller','admin','supplier') DEFAULT 'user',
  `profile_pics` varchar(255) DEFAULT 'path/to/default/profile/pic.jpg',
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `full_address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `contact_number`, `country_id`, `postal_code`, `streetname`, `email`, `password`, `usertype`, `profile_pics`, `latitude`, `longitude`, `gender`, `created_at`, `updated_at`, `full_address`) VALUES
(1, 'aaron', '', 'jhon', '2025-05-06', '09294999087', NULL, '8025', 'De Guzman Street', 'aaron@gmail.com', '$2y$10$WwPlm2wTHoSR5xHKV0DVU.80lg9f.ghvuQ2O3tdpbDBGERf8/xVsC', 'seller', 'uploads/profile_pics/profile_1_1748532349.jpg', 7.0152546, 125.4987365, 'male', '2025-05-23 06:04:51', '2025-06-01 09:01:47', 'Cosmopolitan Funeral Chapel, De Guzman Street, Purok 20, Crossing Bayabas, Toril District, Davao City, Davao Region, 8025, Philippines'),
(2, 'kurumi', '', 'L', '2025-05-02', '09294999233', NULL, '8000', 'R. Castillo Street', 'aa@gmail.com', '$2y$10$yeg8BacZduR3o14Q90B67er68kPftTlZjIGaCtskjW1aO1cz461NC', 'supplier', 'uploads/profile_pics/profile_2_1748698172.jpg', 7.0990636, 125.6400561, 'female', '2025-05-23 07:19:14', '2025-06-01 16:42:50', 'R. Castillo Street, Lapu-Lapu, Agdao District, Buhangin District, Davao City, Davao Region, 8000, Philippines'),
(3, 'Miki', '', 'Frenchfriieess', '2025-01-16', '09294999087', NULL, '6008', 'Cebu North Road', 'miki@gmail.com', '$2y$10$8X0tfv7wMTSD6bNqFhSOBuEZLF/V6VLvy5HQIX7wfnxqEy67srdFe', 'seller', 'uploads/profile_pics/profile_3_1748790899.jpg', 10.8116251, 123.9901556, 'female', '2025-06-01 15:10:13', '2025-06-01 15:15:20', 'Cebu North Road, Lugo, Cebu, Central Visayas, 6008, Philippines');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_supplier_category` (`supplier_id`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`ingredient_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `ingredient_variants`
--
ALTER TABLE `ingredient_variants`
  ADD PRIMARY KEY (`variant_id`),
  ADD KEY `ingredient_id` (`ingredient_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `reply_to` (`reply_to`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `notifications_ibfk_1` (`sender_id`),
  ADD KEY `notifications_ibfk_2` (`receiver_id`);

--
-- Indexes for table `pre_order_list`
--
ALTER TABLE `pre_order_list`
  ADD PRIMARY KEY (`pre_order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `seller_applications`
--
ALTER TABLE `seller_applications`
  ADD PRIMARY KEY (`seller_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `supplier_applications`
--
ALTER TABLE `supplier_applications`
  ADD PRIMARY KEY (`supplier_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `country_id` (`country_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ingredient_variants`
--
ALTER TABLE `ingredient_variants`
  MODIFY `variant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pre_order_list`
--
ALTER TABLE `pre_order_list`
  MODIFY `pre_order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `seller_applications`
--
ALTER TABLE `seller_applications`
  MODIFY `seller_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `supplier_applications`
--
ALTER TABLE `supplier_applications`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_supplier_category` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_applications` (`supplier_id`) ON DELETE CASCADE;

--
-- Constraints for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD CONSTRAINT `ingredients_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_applications` (`supplier_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ingredients_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `ingredient_variants`
--
ALTER TABLE `ingredient_variants`
  ADD CONSTRAINT `ingredient_variants_ibfk_1` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`ingredient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ingredient_variants_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_applications` (`supplier_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`reply_to`) REFERENCES `messages` (`message_id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pre_order_list`
--
ALTER TABLE `pre_order_list`
  ADD CONSTRAINT `pre_order_list_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pre_order_list_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `seller_applications` (`seller_id`);

--
-- Constraints for table `seller_applications`
--
ALTER TABLE `seller_applications`
  ADD CONSTRAINT `seller_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `supplier_applications`
--
ALTER TABLE `supplier_applications`
  ADD CONSTRAINT `supplier_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
