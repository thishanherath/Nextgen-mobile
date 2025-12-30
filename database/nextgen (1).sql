-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2025 at 10:59 PM
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
-- Database: `nextgen`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `first_name`, `last_name`, `phone`, `address`, `city`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 3, '', '', '0123456789', 'jhadsjgagsd', 'sajdhashdg', 0, '2025-05-20 08:18:27', '2025-05-20 08:18:27'),
(2, 3, '', '', '0769856854', 'go matara', 'Matara', 0, '2025-05-20 08:25:45', '2025-05-20 08:25:45');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `cta_text` varchar(100) DEFAULT NULL,
  `cta_link` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `image`, `title`, `subtitle`, `cta_text`, `cta_link`, `is_active`, `sort_order`) VALUES
(1, 'assets/images/hero-banner2.jpg', 'Mega Sale on Accessories', 'Discover unbeatable deals on top brands.', 'Shop Now', 'products.php', 1, 1),
(2, 'assets/images/hero-banner.jpg', '', 'Up to 50% off on selected items!', 'Shop Accessories', 'products.php?category=accessories', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Smartphones', 'smartphones', '2025-05-18 19:53:15'),
(2, 'Tablets', 'tablets', '2025-05-18 19:53:15'),
(3, 'Accessories', 'accessories', '2025-05-18 19:53:15'),
(4, 'Wearables', 'wearables', '2025-05-18 19:53:15');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_movements`
--

CREATE TABLE `inventory_movements` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `change` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `shipping_address_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_address` text NOT NULL,
  `billing_address` text NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tracking_number` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `shipping_address_id`, `total_amount`, `status`, `shipping_address`, `billing_address`, `payment_method`, `created_at`, `updated_at`, `tracking_number`) VALUES
(3, 3, NULL, 1120000.00, 'delivered', 'Beach Road, Palaminai -11, Arayampathy., Batticaloa', '', '', '2025-05-20 08:44:44', '2025-05-20 11:07:03', NULL),
(4, 3, NULL, 1120000.00, 'pending', 'Beach Road, Palaminai -11, Arayampathy.', '', '', '2025-05-20 08:51:21', '2025-05-20 11:53:02', NULL),
(5, 3, NULL, 238000.00, 'delivered', 'Beach Road, Palaminai -11, Arayampathy.', '', '', '2025-05-20 09:24:55', '2025-05-20 11:53:08', NULL),
(6, 6, NULL, 1100000.00, 'shipped', 'Matara, Kamburupitiya', '', '', '2025-05-20 11:46:14', '2025-05-20 11:53:47', NULL),
(7, 7, NULL, 388500.00, 'pending', '123 Roard, high level road, Colombo 5', '', '', '2025-05-20 18:08:01', '2025-05-20 18:08:01', NULL),
(8, 7, NULL, 300000.00, 'pending', 'Abs road, Polonnaruwa', '', 'card', '2025-05-20 19:14:13', '2025-05-20 19:14:13', NULL),
(9, 7, NULL, 300000.00, 'cancelled', 'Ab road, Akurana', '', 'card', '2025-05-20 19:36:50', '2025-05-20 19:41:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `created_at`) VALUES
(3, 3, 1, 1, 420000.00, '2025-05-20 08:44:44'),
(4, 3, 2, 2, 350000.00, '2025-05-20 08:44:44'),
(5, 4, 1, 1, 420000.00, '2025-05-20 08:51:21'),
(6, 4, 2, 2, 350000.00, '2025-05-20 08:51:21'),
(7, 5, 3, 1, 220000.00, '2025-05-20 09:24:55'),
(8, 5, 14, 1, 18000.00, '2025-05-20 09:24:55'),
(9, 6, 3, 5, 220000.00, '2025-05-20 11:46:14'),
(10, 7, 14, 2, 18000.00, '2025-05-20 18:08:01'),
(11, 7, 8, 2, 12000.00, '2025-05-20 18:08:01'),
(12, 7, 18, 5, 35000.00, '2025-05-20 18:08:01'),
(13, 7, 21, 1, 150000.00, '2025-05-20 18:08:01'),
(14, 7, 20, 1, 3500.00, '2025-05-20 18:08:01'),
(15, 8, 9, 1, 300000.00, '2025-05-20 19:14:13'),
(16, 9, 9, 1, 300000.00, '2025-05-20 19:36:50');

-- --------------------------------------------------------

--
-- Table structure for table `order_notes`
--

CREATE TABLE `order_notes` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `brand`, `category_id`, `description`, `price`, `image`, `stock`, `created_at`, `updated_at`) VALUES
(1, 'iPhone 14 Pro', 'Apple', 1, 'Latest Apple flagship smartphone.', 420000.00, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=400&q=80', 10, '2025-05-18 21:34:12', '2025-05-18 21:34:12'),
(2, 'Samsung Galaxy S23', 'Samsung', 1, 'Flagship Samsung smartphone.', 350000.00, 'https://scr.wfcdn.de/25940/Samsung-Galaxy-S23-Ultra-1673639126-0-0.jpg', 15, '2025-05-18 21:34:12', '2025-05-20 09:05:07'),
(3, 'Xiaomi 13 Pro', 'Xiaomi', 1, 'High-end Xiaomi smartphone.', 220000.00, 'https://i02.appmifile.com/324_operator_in/03/03/2023/de94b40a14b8e329c491e7a4b752635f.jpg', 20, '2025-05-18 21:34:12', '2025-05-20 11:37:44'),
(4, 'Oppo Reno 8', 'Oppo', 1, 'Popular Oppo smartphone.', 180000.00, 'https://th.bing.com/th/id/R.fd7704b33fd43fdff0de542eca597dff?rik=IzSQTsmR4UicEQ&pid=ImgRaw&r=0', 12, '2025-05-18 21:34:12', '2025-05-20 09:07:28'),
(5, 'Apple Watch Series 8', 'Apple', 4, 'Latest Apple smartwatch.', 120000.00, 'https://th.bing.com/th/id/R.820e0d30bdd7ed91c20fdde68682c150?rik=0w4MTMNFO99PNQ&pid=ImgRaw&r=0', 8, '2025-05-18 21:34:12', '2025-05-20 09:08:11'),
(6, 'Samsung Galaxy Tab S8', 'Samsung', 2, 'High-end Samsung tablet.', 200000.00, 'https://th.bing.com/th/id/R.dc0d5e8b38a87d69f6ee37921dedac85?rik=ybCK%2fi5HG2Qvbg&pid=ImgRaw&r=0', 7, '2025-05-18 21:34:12', '2025-05-20 09:09:19'),
(7, 'JBL Wireless Earbuds', 'JBL', 3, 'Premium wireless earbuds.', 25000.00, 'https://th.bing.com/th/id/OIP.mcrPFzdtQEDJSCoViFgqhAHaGI?w=233&h=193&c=7&r=0&o=5&dpr=1.3&pid=1.7', 30, '2025-05-18 21:34:12', '2025-05-20 09:10:00'),
(8, 'Mi Band 7', 'Xiaomi', 4, 'Affordable fitness band.', 12000.00, 'https://th.bing.com/th/id/OIP.nhdu6--E49XBIUwzlVh7ugHaHa?rs=1&pid=ImgDetMain', 25, '2025-05-18 21:34:12', '2025-05-20 09:11:29'),
(9, 'Google Pixel 7', 'Google', 1, 'Google\'s latest smartphone.', 300000.00, 'https://th.bing.com/th/id/OIP.Kz7YK3nqAITvq74Q9C-l1AHaHa?rs=1&pid=ImgDetMain', 10, '2025-05-18 21:34:12', '2025-05-20 09:12:25'),
(10, 'OnePlus 11', 'OnePlus', 1, 'Flagship killer smartphone.', 250000.00, 'https://www.notebookcheck.net/fileadmin/Notebooks/News/_nc3/FoWDR2nacAEL4pu.jpeg', 14, '2025-05-18 21:34:12', '2025-05-20 09:13:21'),
(11, 'iPad Pro', 'Apple', 2, 'Powerful Apple tablet.', 350000.00, 'https://cdn.shopify.com/s/files/1/0541/0437/files/Screen_Shot_2022-07-25_at_9.53.26_PM_480x480.png?v=1658811238', 6, '2025-05-18 21:34:12', '2025-05-20 09:14:21'),
(12, 'Samsung Galaxy Buds 2', 'Samsung', 3, 'Wireless earbuds from Samsung.', 22000.00, 'https://th.bing.com/th/id/OIP.U4WbrntB9KmnwpVt2ESX3QHaHa?rs=1&pid=ImgDetMain', 18, '2025-05-18 21:34:12', '2025-05-20 09:15:09'),
(13, 'Sony WH-1000XM4', 'Sony', 3, 'Industry-leading noise cancelling headphones.', 80000.00, 'https://th.bing.com/th/id/R.078669461024b334bf96ad520c57c0f6?rik=vNMJZyvs3lJOow&pid=ImgRaw&r=0', 9, '2025-05-18 21:34:12', '2025-05-20 09:15:52'),
(14, 'Fitbit Charge 5', 'Fitbit', 4, 'Advanced fitness tracker.', 18000.00, 'https://th.bing.com/th/id/OIP.w_LUIN-YafCE3O8xuU95SwHaHa?rs=1&pid=ImgDetMain', 20, '2025-05-18 21:34:12', '2025-05-20 09:16:44'),
(15, 'Huawei MatePad 11', 'Huawei', 2, 'Versatile Android tablet.', 120000.00, 'https://th.bing.com/th/id/OIP.SV2KvOVN0WzNGn5ZeOxrXAHaHa?w=800&h=800&rs=1&pid=ImgDetMain', 8, '2025-05-18 21:34:12', '2025-05-20 09:18:05'),
(16, 'Realme GT Neo 3', 'Realme', 1, 'Affordable flagship smartphone.', 160000.00, 'https://th.bing.com/th/id/OIP.vPZa-1olhZqFN6hcq4r-dQHaHa?rs=1&pid=ImgDetMain', 13, '2025-05-18 21:34:12', '2025-05-20 09:18:54'),
(17, 'Lenovo Tab P11', 'Lenovo', 2, 'Android tablet for work and play.', 90000.00, 'https://th.bing.com/th/id/OIP.qhSyNDSneHH24OjPw9D2zwHaFQ?rs=1&pid=ImgDetMain', 11, '2025-05-18 21:34:12', '2025-05-20 09:19:31'),
(18, 'Amazfit GTR 3', 'Amazfit', 4, 'Smartwatch with long battery life.', 35000.00, 'https://ucarecdn.com/bb2173b9-7558-48d1-8ef7-4ca7df82fe6e/-/format/auto/-/preview/3000x3000/-/quality/lighter/1_01.jpg', 15, '2025-05-18 21:34:12', '2025-05-20 09:20:16'),
(19, 'Anker PowerCore 20000', 'Anker', 3, 'High-capacity power bank.', 10000.00, 'https://down-sg.img.susercontent.com/file/sg-11134207-7rbn4-lnb8hpru2v2fc7', 40, '2025-05-18 21:34:12', '2025-05-20 09:21:07'),
(20, 'Baseus Car Charger', 'Baseus', 3, 'Fast charging car charger.', 3500.00, 'https://th.bing.com/th/id/OIP.qNy5B2D_X9py3Vzozgd75QHaHa?rs=1&pid=ImgDetMain', 50, '2025-05-18 21:34:12', '2025-05-20 09:21:37'),
(21, 'Go Pro 12', 'GoPro', 3, 'GoPro offers a range of cameras, accessories, apps and subscriptions for your creative needs. Whether you are into surf, moto, ski, travel or adventure, you can find the perfect GoPro for you.', 150000.00, 'uploads/products/product_1747742245.jpeg', 29, '2025-05-20 11:57:25', '2025-05-20 11:57:25');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(1, 21, 7, 1, 'Good product', '2025-05-20 19:23:12'),
(2, 2, 7, 1, 'Best product', '2025-05-20 19:28:25'),
(3, 8, 7, 5, 'Good', '2025-05-20 19:32:04'),
(4, 7, 7, 3, 'Good product', '2025-05-20 19:33:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `otp` varchar(10) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `name`, `email`, `password`, `phone`, `address`, `city`, `profile_picture`, `otp`, `is_verified`, `role`, `created_at`, `updated_at`) VALUES
(2, NULL, NULL, 'Dinidu', 'diniduonline5@gmail.com', '$2y$10$I8eEoCEpeChqrT0eUBEWhuRvabu27fqEyIyUVbi8R9WpNzkzsCc56', NULL, NULL, NULL, NULL, NULL, 0, 'user', '2025-05-19 05:31:52', '2025-05-19 05:31:52'),
(3, 'Hajith', 'Mohamed', 'Hajith', 'hanoufaatif@gmail.com', '$2y$10$W7nnoGj59S/Is9KxrSQMeel6jSOrQ82EXsFJVspmQ5or4txwoWVEO', '0740523954', 'Beach Road, Palaminai -11, Arayampathy.', 'Batticaloa', 'uploads/profile_pictures/profile_3_1747663630.png', NULL, 0, 'user', '2025-05-19 12:56:34', '2025-05-20 09:01:39'),
(5, NULL, NULL, 'Admin', 'admin@gmail.com', '$2y$10$T7mKs9qlWK53elTxMh/TvOcOFki1dJzvJNjnXJOJtg.1XmmcLwP2.', NULL, NULL, NULL, NULL, NULL, 0, 'admin', '2025-05-20 10:01:53', '2025-05-20 10:02:37'),
(6, NULL, NULL, 'Isuru', 'isuru5@gmai.com', '$2y$10$bj6WaTivdHj2mFtAulwhEOfH826Sv.qnm4M2BqvZOKzuHx3PikxtO', '0740523954', '', NULL, 'uploads/profile_pictures/profile_6_1747741449.png', NULL, 0, 'user', '2025-05-20 11:43:02', '2025-05-20 11:44:09'),
(7, NULL, NULL, 'Deshan', 'deshan@gmail.com', '$2y$10$1I5HU1NV.S510JU1E7OIMOPti7MPV3xXTI3YcKT9rQWW0xRz6YfK6', '', '', NULL, 'uploads/profile_pictures/profile_7_1747764329.png', NULL, 0, 'user', '2025-05-20 18:04:02', '2025-05-20 18:05:29'),
(8, NULL, NULL, 'Nimesh', 'nimesh@gmail.com', '$2y$10$cd0XvYOY96QQZ8UN.V.XMeezoaxKGBSfnGL/3UTmYiIORGXNyXZVC', NULL, NULL, NULL, NULL, NULL, 0, 'user', '2025-05-20 19:46:00', '2025-05-20 19:46:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_logs`
--

CREATE TABLE `user_activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_communications`
--

CREATE TABLE `user_communications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT 'message',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(1, 2, 1, '2025-05-19 10:26:16'),
(2, 3, 1, '2025-05-19 12:57:17'),
(4, 3, 3, '2025-05-20 09:27:31'),
(5, 6, 6, '2025-05-20 11:44:46'),
(6, 7, 20, '2025-05-20 18:06:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `inventory_movements`
--
ALTER TABLE `inventory_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_shipping_address` (`shipping_address_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_notes`
--
ALTER TABLE `order_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_communications`
--
ALTER TABLE `user_communications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inventory_movements`
--
ALTER TABLE `inventory_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `order_notes`
--
ALTER TABLE `order_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_communications`
--
ALTER TABLE `user_communications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_movements`
--
ALTER TABLE `inventory_movements`
  ADD CONSTRAINT `inventory_movements_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `inventory_movements_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_shipping_address` FOREIGN KEY (`shipping_address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_notes`
--
ALTER TABLE `order_notes`
  ADD CONSTRAINT `order_notes_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_notes_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD CONSTRAINT `user_activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_communications`
--
ALTER TABLE `user_communications`
  ADD CONSTRAINT `user_communications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_communications_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
