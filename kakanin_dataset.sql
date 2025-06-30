-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2025 at 08:13 PM
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
-- Table structure for table `kakanin_dataset`
--

CREATE TABLE `kakanin_dataset` (
  `id` int(11) NOT NULL,
  `recipe_title` varchar(255) NOT NULL,
  `ingredient_list` text NOT NULL,
  `steps` text DEFAULT NULL,
  `servings` int(11) DEFAULT NULL,
  `prep_time` varchar(50) DEFAULT NULL,
  `cook_time` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kakanin_dataset`
--

INSERT INTO `kakanin_dataset` (`id`, `recipe_title`, `ingredient_list`, `steps`, `servings`, `prep_time`, `cook_time`, `notes`, `created_at`) VALUES
(1, 'Turon', 'Banana 6 pcs, Sugar 300 g, Jackfruit 150 g, Lumpia Wrapper 6 pcs, Oil 480 ml', '1. Coat banana with sugar. 2. Wrap in lumpia wrapper with jackfruit. 3. Fry in oil until golden.', 6, '10 minutes', '15 minutes', 'Sweet banana rolls wrapped and fried until golden.', '2025-06-28 02:11:23'),
(2, 'Sapin-Sapin', 'Glutinous Rice Flour 240 g, Coconut Milk 960 ml, Sugar 200 g, Ube 150 g, Jackfruit 75 g, Food Coloring 2 ml', '1. Mix ingredients per layer. 2. Steam layer by layer. 3. Top with latik.', 8, '20 minutes', '30 minutes', 'Layered sticky rice cake flavored with ube and jackfruit.', '2025-06-28 02:11:23'),
(3, 'Bibingka', 'Rice Flour 250 g, Coconut Milk 400 ml, Sugar 100 g, Salted Egg 1 pc, Cheese 50 g, Banana Leaf 1 pc', '1. Mix batter. 2. Pour into banana leaf-lined mold. 3. Top with cheese and egg. 4. Bake.', 6, '15 minutes', '25 minutes', 'Traditional rice cake baked in banana leaf, topped with salted egg and cheese.', '2025-06-28 02:11:23'),
(4, 'Puto', 'Rice Flour 200 g, Sugar 100 g, Baking Powder 10 g, Milk 150 ml, Cheese 50 g', '1. Mix all ingredients. 2. Pour into molds. 3. Steam until cooked.', 10, '10 minutes', '20 minutes', 'Steamed rice cake usually served with cheese on top.', '2025-06-28 02:11:23'),
(5, 'Kutsinta', 'Tapioca Flour 200 g, Brown Sugar 150 g, Lye Water 5 ml, Annatto 2 ml', '1. Mix ingredients. 2. Pour into molds. 3. Steam until set.', 10, '15 minutes', '30 minutes', 'Sticky rice cake with a jelly-like texture, often served with grated coconut.', '2025-06-28 02:11:24'),
(6, 'Palitaw', 'Glutinous Rice Flour 200 g, Water 100 ml, Sugar 50 g, Sesame Seeds 30 g, Coconut 100 g', '1. Form dough balls. 2. Boil until they float. 3. Roll in coconut and sugar mixture.', 8, '10 minutes', '10 minutes', 'Flat rice cake boiled and coated in coconut and sugar.', '2025-06-28 02:11:24'),
(7, 'Biko', 'Sticky Rice 300 g, Coconut Milk 400 ml, Brown Sugar 150 g, Latik 50 g', '1. Cook rice. 2. Simmer with coconut milk and sugar. 3. Top with latik.', 6, '15 minutes', '40 minutes', 'Sticky sweet rice cake topped with coconut curds (latik).', '2025-06-28 02:11:24'),
(8, 'Maja Blanca', 'Coconut Milk 400 ml, Cornstarch 100 g, Sugar 100 g, Corn 100 g, Milk 100 ml', '1. Boil ingredients. 2. Pour into tray. 3. Chill until set.', 6, '10 minutes', '20 minutes', 'Coconut pudding dessert with corn kernels.', '2025-06-28 02:11:24'),
(9, 'Cassava Cake', 'Grated Cassava 400 g, Coconut Milk 300 ml, Sugar 100 g, Egg 1 pc, Condensed Milk 200 ml', '1. Mix all ingredients. 2. Bake until top is golden.', 6, '20 minutes', '40 minutes', 'Sweet, soft, and chewy cassava-based cake.', '2025-06-28 02:11:24'),
(10, 'Suman', 'Glutinous Rice 250 g, Coconut Milk 300 ml, Salt 2 g, Banana Leaf 2 pcs', '1. Mix rice and coconut milk. 2. Wrap in banana leaves. 3. Steam until cooked.', 6, '30 minutes', '60 minutes', 'Rice and coconut wrapped in banana leaf and steamed.', '2025-06-28 02:11:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kakanin_dataset`
--
ALTER TABLE `kakanin_dataset`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kakanin_dataset`
--
ALTER TABLE `kakanin_dataset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
