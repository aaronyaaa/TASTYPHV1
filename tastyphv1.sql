-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2025 at 08:11 PM
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
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `ingredient_id` int(11) DEFAULT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total_price` decimal(10,2) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','saved','ordered') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `rating` decimal(3,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`ingredient_id`, `supplier_id`, `category_id`, `ingredient_name`, `slug`, `description`, `image_url`, `price`, `discount_price`, `stock`, `quantity_value`, `unit_type`, `is_active`, `rating`, `created_at`, `updated_at`) VALUES
(1, 1, 17, 'Coconut Milk', 'coconut-milk', 'Coconut Milk', 'uploads/ingredients/ingredient_1_1748803467.jpg', 50.00, NULL, 31, 165.00, 'ml', 1, 0.00, '2025-06-02 02:44:27', '2025-06-03 00:32:32'),
(3, 1, 16, 'Cheese', 'cheese', 'Cheese', 'uploads/ingredients/ingredient_1_1748812131.jpg', 222.00, NULL, 125, 430.00, 'g', 1, 0.00, '2025-06-02 05:08:51', '2025-06-03 00:28:01'),
(4, 1, 16, 'Cheeser', 'cheeser', 'asd', 'uploads/ingredients/ingredient_1_1748887868.jpg', 240.00, NULL, 222, 250.00, 'g', 1, 0.00, '2025-06-03 02:11:08', '2025-06-03 02:11:08'),
(5, 1, 16, 'Cow Cheese', 'cow-cheese', 'asd', 'uploads/ingredients/ingredient_1_1748887969.jpg', 250.00, NULL, 222, 1000.00, 'l', 1, 0.00, '2025-06-03 02:12:49', '2025-06-03 02:12:49'),
(6, 1, 16, 'American Cheese', 'american-cheese', 'asd', 'uploads/ingredients/ingredient_1_1748888022.jpg', 200.00, NULL, 222, 500.00, 'ml', 1, 0.00, '2025-06-03 02:13:42', '2025-06-03 02:13:42'),
(7, 1, 16, 'Aussie Cheese', 'aussie-cheese', 'asd', 'uploads/ingredients/ingredient_1_1748888095.jpg', 266.00, NULL, 123, 250.00, 'pcs', 1, 0.00, '2025-06-03 02:14:55', '2025-06-03 02:14:55');

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
  `rating` decimal(3,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredient_variants`
--

INSERT INTO `ingredient_variants` (`variant_id`, `ingredient_id`, `supplier_id`, `variant_name`, `price`, `discount_price`, `stock`, `quantity_value`, `unit_type`, `image_url`, `is_active`, `rating`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '213', 123.00, 123.00, 123, 123.00, 'kg', 'uploads/variants/variant_1_1748809709.jpg', 1, 0.00, '2025-06-02 04:28:29', '2025-06-02 04:28:29'),
(2, 1, 1, '123', 213.00, 123.00, 123, 123.00, 'g', 'uploads/variants/variant_1_1748812068.jpg', 1, 0.00, '2025-06-02 05:07:48', '2025-06-02 05:07:48');

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
(165, 1, 2, 'asd', NULL, NULL, 0, 1, '2025-06-09 01:02:18', NULL),
(166, 1, 3, 'otin', NULL, NULL, 0, 1, '2025-06-09 01:24:06', NULL),
(167, 3, 2, 'hi', NULL, NULL, 0, 1, '2025-06-09 02:08:57', NULL),
(168, 2, 3, 'hello', NULL, NULL, 0, 1, '2025-06-09 02:09:08', NULL);

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
(1, 1, 1, NULL, 'cash', NULL, 200.00, '2025-06-09 02:04:36', 'pending', 0),
(2, 1, 1, NULL, 'cash', NULL, 1790.00, '2025-06-09 02:05:09', 'pending', 0),
(3, 3, 1, NULL, 'cash', NULL, 40000.00, '2025-06-09 02:07:21', 'pending', 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `ingredient_id` int(11) DEFAULT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) GENERATED ALWAYS AS (`unit_price` * `quantity`) STORED,
  `supplier_id` int(11) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `ingredient_id`, `variant_id`, `quantity`, `unit_price`, `supplier_id`, `seller_id`) VALUES
(1, 1, NULL, 6, NULL, 1, 200.00, 1, NULL),
(2, 2, NULL, 7, NULL, 1, 266.00, 1, NULL),
(3, 2, NULL, 3, NULL, 2, 222.00, 1, NULL),
(4, 2, NULL, 4, NULL, 2, 240.00, 1, NULL),
(5, 2, NULL, 5, NULL, 2, 250.00, 1, NULL),
(6, 2, NULL, 1, NULL, 2, 50.00, 1, NULL),
(7, 3, NULL, 6, NULL, 200, 200.00, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `audience` enum('public','friends','only_me') NOT NULL DEFAULT 'only_me',
  `location` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `reactions_count` int(11) DEFAULT 0,
  `comments_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `content`, `audience`, `location`, `is_active`, `reactions_count`, `comments_count`, `created_at`, `updated_at`) VALUES
(26, 1, 's', 'public', NULL, 1, 0, 0, '2025-06-04 22:51:20', '2025-06-04 22:51:20'),
(27, 1, 's', 'public', NULL, 1, 0, 0, '2025-06-04 22:51:20', '2025-06-04 22:51:20'),
(28, 1, 's', 'public', NULL, 1, 0, 0, '2025-06-04 22:53:09', '2025-06-04 22:53:09'),
(29, 1, 's', 'public', NULL, 1, 0, 0, '2025-06-04 22:53:09', '2025-06-04 22:53:09'),
(30, 1, 'a', 'public', NULL, 1, 0, 0, '2025-06-04 22:56:23', '2025-06-04 22:56:23'),
(31, 1, 'a', 'public', NULL, 1, 0, 0, '2025-06-04 22:56:23', '2025-06-04 22:56:23'),
(32, 1, 's', 'public', NULL, 1, 0, 0, '2025-06-04 22:59:29', '2025-06-04 22:59:29'),
(33, 1, 'd', 'public', NULL, 1, 0, 0, '2025-06-04 22:59:42', '2025-06-04 22:59:42'),
(34, 1, 's', 'public', NULL, 1, 0, 0, '2025-06-04 23:00:23', '2025-06-04 23:00:23'),
(35, 1, 'yawa', 'public', NULL, 1, 0, 0, '2025-06-04 23:01:21', '2025-06-04 23:01:21'),
(36, 1, 'hoi', 'public', NULL, 1, 0, 0, '2025-06-04 23:02:27', '2025-06-04 23:02:27'),
(37, 1, 'hoi', 'public', NULL, 1, 0, 0, '2025-06-04 23:02:27', '2025-06-04 23:02:27'),
(38, 1, 'shit\r\n', 'public', NULL, 1, 0, 0, '2025-06-04 23:05:30', '2025-06-04 23:05:30'),
(39, 1, 'ad', 'public', NULL, 1, 0, 0, '2025-06-04 23:08:20', '2025-06-04 23:08:20'),
(40, 2, 'hoi', 'public', NULL, 1, 0, 0, '2025-06-04 23:12:41', '2025-06-04 23:12:41'),
(41, 2, 'nigga', 'public', NULL, 1, 0, 0, '2025-06-05 02:15:53', '2025-06-05 02:15:53');

-- --------------------------------------------------------

--
-- Table structure for table `post_comments`
--

CREATE TABLE `post_comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `parent_comment_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_media`
--

CREATE TABLE `post_media` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `media_type` enum('image','video','gif') DEFAULT 'image',
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_media`
--

INSERT INTO `post_media` (`id`, `post_id`, `file_path`, `media_type`, `sort_order`, `created_at`) VALUES
(6, 40, 'uploads/posts/picture/68412749be12b_IMG20250329172225.jpg', 'image', 0, '2025-06-05 05:12:41'),
(7, 41, 'uploads/posts/picture/68415239aab22_theres-a-brainrot-in-the-vietnamese-wuwa-fandom-where-v0-0dxzfww7qz0f1.jpg', 'image', 0, '2025-06-05 08:15:53');

-- --------------------------------------------------------

--
-- Table structure for table `post_reactions`
--

CREATE TABLE `post_reactions` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reaction_type` enum('like','love','haha','wow','sad','angry') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_reactions`
--

INSERT INTO `post_reactions` (`id`, `post_id`, `user_id`, `reaction_type`, `created_at`) VALUES
(28, 40, 2, 'like', '2025-06-05 08:15:28'),
(29, 41, 2, 'angry', '2025-06-05 08:16:01'),
(30, 38, 1, 'like', '2025-06-08 03:56:07');

-- --------------------------------------------------------

--
-- Table structure for table `post_shares`
--

CREATE TABLE `post_shares` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `shared_by_user_id` int(11) NOT NULL,
  `shared_to_user_id` int(11) DEFAULT NULL,
  `share_comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_tags`
--

CREATE TABLE `post_tags` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `tagged_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(13, 3, 4, 'otn', 21, 'pcs', '2025-06-04', '3:00pm', 'asd', 'approved', '2025-06-02 00:28:12', '2025-06-09 01:23:57', 'Cebu North Road, Lugo, Cebu, Central Visayas, 6008, Philippines', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `rating` decimal(3,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(4, 1, 'Aaron\'s kakanin', 'asd', 'Bato-Kilate-Tagurano Road, Purok 9, Bato, Toril District, Davao City, Davao Region, 8000, Philippines', 7.0315146, 125.4782009, 'Bato-Kilate-Tagurano Road, Purok 9, Bato, Toril District, Davao City, Davao Region, 8000, Philippines', 'permit_1748690751_683ae73f22ccb.jpg', 'health_1748690751_683ae73f22fe3.jpg', '2025-05-31 19:25:51', 'approved', 'uploads/seller/profile_1_1748758548.jpg', 3, '2025-06-04 15:22:03', NULL, 'active', 1, 'uploads/seller/cover_1_1748758217.png'),
(5, 3, 'Miki\'s Delicious Kakanin', 'i love you', 'Salsa Village, Crossing Bayabas, Toril District, Davao City, Davao Region, 8025, Philippines', 7.0191200, 125.4927063, 'Salsa Village, Crossing Bayabas, Toril District, Davao City, Davao Region, 8025, Philippines', 'permit_1748790776_683c6df8c2a5d.png', 'health_1748790776_683c6df8c2da7.jpg', '2025-06-01 23:12:56', 'approved', 'uploads/seller/profile_3_1748790960.jpg', 3, '2025-06-01 23:13:33', NULL, 'active', 1, 'uploads/seller/cover_3_1748790945.jpg');

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
  `rating` decimal(3,2) DEFAULT 0.00,
  `cover_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier_applications`
--

INSERT INTO `supplier_applications` (`supplier_id`, `user_id`, `business_name`, `description`, `store_address`, `latitude`, `longitude`, `full_address`, `business_license`, `application_date`, `status`, `profile_pics`, `reviewed_by`, `reviewed_at`, `last_active`, `store_status`, `is_public`, `rating`, `cover_photo`) VALUES
(1, 2, 'Kurumi\'s Palengke', 'asd', 'San Vicente, Daliao, Toril District, Davao City, Davao Region, 8025, Philippines', 7.0071936, 125.5039501, 'San Vicente, Daliao, Toril District, Davao City, Davao Region, 8025, Philippines', 'license_1748698974_683b075e41b7a.png', '2025-05-31 21:42:54', 'approved', 'uploads/supplier/profile_2_1748797143.jpg', 3, '2025-06-01 11:50:35', NULL, 'active', 1, 0.00, 'uploads/supplier/cover_2_1748797059.png');

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
  `cover_photo` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `status` enum('online','offline') DEFAULT 'offline',
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `full_address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `contact_number`, `country_id`, `postal_code`, `streetname`, `email`, `password`, `usertype`, `profile_pics`, `cover_photo`, `latitude`, `longitude`, `gender`, `status`, `is_public`, `created_at`, `updated_at`, `full_address`) VALUES
(1, 'aaronyaaa', '', 'jhon', '2025-05-06', '09294999087', NULL, '8000', 'Bayabas-Eden Road, Purok 9', 'aaron@gmail.com', '$2y$10$WwPlm2wTHoSR5xHKV0DVU.80lg9f.ghvuQ2O3tdpbDBGERf8/xVsC', 'seller', 'uploads/profile_pics/profile_1_1748532349.jpg', 'uploads/users/cover/cover_1_1749056553.jpg', 7.0272297, 125.4845438, 'male', 'online', 1, '2025-05-23 06:04:51', '2025-06-05 03:08:40', 'Bayabas-Eden Road, Purok 9, Bato, Toril District, Davao City, Davao Region, 8000, Philippines'),
(2, 'Kurumi', 'L', 'Tokisaki', '2025-05-02', '09294999233', NULL, '8025', 'MacArthur Highway, Salsa Village', 'aa@gmail.com', '$2y$10$yeg8BacZduR3o14Q90B67er68kPftTlZjIGaCtskjW1aO1cz461NC', 'supplier', 'uploads/users/profile/profile_2_1749111714.jpg', 'uploads/users/cover/cover_2_1749111686.jpg', 7.0142131, 125.4900896, 'female', 'online', 1, '2025-05-23 07:19:14', '2025-06-05 08:21:54', 'MacArthur Highway, Salsa Village, Lizada, Toril District, Davao City, Davao Region, 8025, Philippines'),
(3, 'Miki', '', 'Frenchfriieess', '2025-01-16', '09294999087', NULL, '8022', 'Adelfa Street', 'miki@gmail.com', '$2y$10$8X0tfv7wMTSD6bNqFhSOBuEZLF/V6VLvy5HQIX7wfnxqEy67srdFe', 'seller', 'uploads/profile_pics/profile_3_1748790899.jpg', NULL, 7.0946580, 125.4973172, 'female', 'offline', 0, '2025-06-01 15:10:13', '2025-06-04 07:43:47', 'Adelfa Street, Purok 1, Santo Niño, Tugbok District, Davao City, Davao Region, 8022, Philippines'),
(4, 'Darlyn', NULL, 'Dollrain', NULL, '', NULL, NULL, NULL, 'doll@gmail.com', '$2y$10$MhKI5eRxo9R2Xq3xs8gGiOWJB/MgV0996O3YyJWB3Jwx5HgyCqxQ.', 'user', 'path/to/default/profile/pic.jpg', NULL, NULL, NULL, NULL, 'offline', 0, '2025-06-02 18:54:48', '2025-06-02 18:54:48', NULL);

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
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `fk_cart_user` (`user_id`),
  ADD KEY `fk_cart_product` (`product_id`),
  ADD KEY `fk_cart_ingredient` (`ingredient_id`),
  ADD KEY `fk_cart_variant` (`variant_id`);

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
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `ingredient_id` (`ingredient_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_posts_user` (`user_id`);

--
-- Indexes for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comments_post` (`post_id`),
  ADD KEY `fk_comments_user` (`user_id`),
  ADD KEY `fk_comments_parent` (`parent_comment_id`);

--
-- Indexes for table `post_media`
--
ALTER TABLE `post_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_postmedia_post` (`post_id`);

--
-- Indexes for table `post_reactions`
--
ALTER TABLE `post_reactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `post_id` (`post_id`,`user_id`),
  ADD KEY `fk_reactions_user` (`user_id`);

--
-- Indexes for table `post_shares`
--
ALTER TABLE `post_shares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_shares_post` (`post_id`),
  ADD KEY `fk_shares_user` (`shared_by_user_id`);

--
-- Indexes for table `post_tags`
--
ALTER TABLE `post_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `post_id` (`post_id`,`tagged_user_id`),
  ADD KEY `fk_posttags_user` (`tagged_user_id`);

--
-- Indexes for table `pre_order_list`
--
ALTER TABLE `pre_order_list`
  ADD PRIMARY KEY (`pre_order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_products_ingredient` (`ingredient_id`),
  ADD KEY `fk_products_seller` (`seller_id`),
  ADD KEY `fk_products_category` (`category_id`);

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
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ingredient_variants`
--
ALTER TABLE `ingredient_variants`
  MODIFY `variant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `post_comments`
--
ALTER TABLE `post_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `post_media`
--
ALTER TABLE `post_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `post_reactions`
--
ALTER TABLE `post_reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `post_shares`
--
ALTER TABLE `post_shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_tags`
--
ALTER TABLE `post_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pre_order_list`
--
ALTER TABLE `pre_order_list`
  MODIFY `pre_order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_ingredient` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`ingredient_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_variant` FOREIGN KEY (`variant_id`) REFERENCES `ingredient_variants` (`variant_id`) ON DELETE SET NULL;

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
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_applications` (`supplier_id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`seller_id`) REFERENCES `seller_applications` (`seller_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`ingredient_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `ingredient_variants` (`variant_id`) ON DELETE SET NULL;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD CONSTRAINT `fk_comments_parent` FOREIGN KEY (`parent_comment_id`) REFERENCES `post_comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comments_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_media`
--
ALTER TABLE `post_media`
  ADD CONSTRAINT `fk_postmedia_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_reactions`
--
ALTER TABLE `post_reactions`
  ADD CONSTRAINT `fk_reactions_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reactions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_shares`
--
ALTER TABLE `post_shares`
  ADD CONSTRAINT `fk_shares_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_shares_user` FOREIGN KEY (`shared_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_tags`
--
ALTER TABLE `post_tags`
  ADD CONSTRAINT `fk_posttags_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_posttags_user` FOREIGN KEY (`tagged_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pre_order_list`
--
ALTER TABLE `pre_order_list`
  ADD CONSTRAINT `pre_order_list_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pre_order_list_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `seller_applications` (`seller_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_products_ingredient` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`ingredient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_products_seller` FOREIGN KEY (`seller_id`) REFERENCES `seller_applications` (`seller_id`) ON DELETE CASCADE;

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
