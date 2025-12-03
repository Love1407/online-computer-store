-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 03, 2025 at 06:05 AM
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
-- Database: `online_computer_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `category_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `group_id`, `category_name`) VALUES
(1, 1, 'Gaming Laptops'),
(2, 1, 'Business Laptops'),
(3, 1, 'Student Laptops'),
(4, 1, 'Ultrabooks'),
(5, 1, '2-in-1 Convertible Laptops'),
(6, 2, 'Pre-Built Gaming PCs'),
(7, 2, 'Office / Home Desktops'),
(8, 2, 'Workstations'),
(9, 2, 'Mini PCs'),
(10, 2, 'All-in-One PCs'),
(11, 3, 'Processors (CPU)'),
(12, 3, 'Graphics Cards (GPU)'),
(13, 3, 'Motherboards'),
(14, 3, 'RAM'),
(15, 3, 'Internal Storage'),
(16, 3, 'Power Supply Units'),
(17, 3, 'Cooling Systems'),
(18, 3, 'PC Cabinets'),
(19, 4, 'Gaming Monitors'),
(20, 4, 'Office Monitors'),
(21, 4, 'Curved Monitors'),
(22, 5, 'Keyboards'),
(23, 5, 'Mouse'),
(24, 5, 'Headsets & Microphones'),
(25, 5, 'Webcams & Streaming'),
(26, 6, 'Data Cables'),
(27, 6, 'Adapters');

-- --------------------------------------------------------

--
-- Table structure for table `groups_h`
--

CREATE TABLE `groups_h` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups_h`
--

INSERT INTO `groups_h` (`id`, `name`) VALUES
(1, 'Laptops'),
(2, 'Desktop Computers'),
(3, 'PC Components'),
(4, 'Displays & Monitors'),
(5, 'Peripherals & Accessories'),
(6, 'Cables & Adapters');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `apartment` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `shipping_method` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `card_number` varchar(50) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_history`
--

CREATE TABLE `order_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `original_price` decimal(10,2) NOT NULL,
  `deal_price` decimal(10,2) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `is_on_sale` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `group_id` int(11) DEFAULT NULL,
  `subcategory_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `description`, `original_price`, `deal_price`, `image_url`, `category_id`, `stock`, `is_on_sale`, `created_at`, `updated_at`, `group_id`, `subcategory_id`) VALUES
(254, 'ASUS ROG Strix G16', 'Intel Core i9-13980HX, RTX 4070, 16GB DDR5, 1TB SSD, 16\" QHD 240Hz', 189999.00, 174999.00, 'uploads/img_692f13209820a.png', 1, 15, 1, '2025-12-02 15:47:21', '2025-12-02 16:26:08', 1, 1),
(255, 'Intel Core i9-14900K', '24 cores, 32 threads, 6.0GHz Boost, LGA1700', 59999.00, 56999.00, 'uploads/img_692f133d0c132.png', 11, 30, 1, '2025-12-02 15:47:21', '2025-12-02 16:26:37', 3, 26),
(256, 'Dell P2423DE', '24\" FHD, IPS, USB-C Hub, Height Adjustable', 24999.00, 23499.00, 'uploads/img_692f136cd9cb2.png', 20, 40, 1, '2025-12-02 15:47:21', '2025-12-02 16:27:24', 4, 48),
(257, 'HP 15s', 'Intel Core i3-1215U, 8GB DDR4, 512GB SSD, 15.6\" FHD', 39999.00, 36999.00, 'uploads/img_692f1384c7f70.png', 3, 50, 1, '2025-12-02 15:47:21', '2025-12-02 16:27:48', 1, 6),
(258, 'ASUS ROG Strix GT35', 'Intel Core i9-13900K, RTX 4090, 64GB DDR5, 2TB NVMe + 2TB HDD', 549999.00, 519999.00, 'uploads/img_692f139937649.png', 6, 8, 1, '2025-12-02 15:47:21', '2025-12-02 16:28:09', 2, 14),
(259, 'Logitech G915 TKL', 'Wireless Mechanical, Low Profile, RGB, LIGHTSPEED', 17999.00, 16999.00, 'uploads/img_692f13ac5301d.png', 22, 25, 1, '2025-12-02 15:47:21', '2025-12-02 16:28:28', 5, 52),
(260, 'Corsair Vengeance DDR4', '32GB (2x16GB), 3600MHz, CL18', 9999.00, 8999.00, 'uploads/img_692f13c7c74a1.png', 14, 50, 1, '2025-12-02 15:47:21', '2025-12-02 16:28:55', 3, 34),
(261, 'MSI Titan GT77', 'Intel Core i9-13950HX, RTX 4090, 64GB DDR5, 2TB SSD, 17.3\" 4K 144Hz', 549999.00, 549999.00, 'uploads/img_692f13dc324ba.png', 1, 5, 0, '2025-12-02 15:47:21', '2025-12-02 16:29:16', 1, 1),
(262, 'Samsung 990 PRO', '2TB NVMe Gen 4, 7450MB/s Read, 6900MB/s Write', 19999.00, 18499.00, 'uploads/img_692f1401070dc.png', 15, 35, 1, '2025-12-02 15:47:21', '2025-12-02 16:29:53', 3, 37),
(263, 'Dell OptiPlex 7010 Plus', 'Intel Core i7-13700, 16GB DDR5, 512GB NVMe, Intel UHD 770', 89999.00, 84999.00, 'uploads/img_692f141ba4113.png', 7, 40, 1, '2025-12-02 15:47:21', '2025-12-02 16:30:19', 2, 17),
(264, 'Cable Matters 48Gbps HDMI 2.1', '3m, 8K@60Hz, 4K@120Hz, eARC, Dynamic HDR', 2499.00, 2299.00, 'uploads/img_692f142e2c234.jpg', 26, 100, 1, '2025-12-02 15:47:21', '2025-12-02 16:30:38', 6, 62),
(265, 'NVIDIA GeForce RTX 4090', '24GB GDDR6X, 2.52GHz Boost, 16384 CUDA Cores', 179999.00, 169999.00, 'uploads/img_692f144a19a0f.png', 12, 15, 1, '2025-12-02 15:47:21', '2025-12-02 16:31:06', 3, 28),
(266, 'Alienware m18', 'Intel Core i9-13900HX, RTX 4080, 32GB DDR5, 2TB SSD, 18\" QHD+ 165Hz', 399999.00, 369999.00, 'uploads/img_692f145b49449.png', 1, 8, 1, '2025-12-02 15:47:21', '2025-12-02 16:31:23', 1, 1),
(267, 'ASUS ROG Swift PG32UQX', '32\" 4K, 144Hz, Mini LED, G-Sync Ultimate, HDR1400', 299999.00, 279999.00, 'uploads/img_692f14700264e.png', 19, 8, 1, '2025-12-02 15:47:21', '2025-12-02 16:31:44', 4, 46),
(268, 'Lenovo IdeaPad Slim 3', 'AMD Ryzen 5 5500U, 8GB DDR4, 512GB SSD, 15.6\" FHD', 44999.00, 41999.00, 'uploads/img_692f148424a0d.png', 3, 45, 1, '2025-12-02 15:47:21', '2025-12-02 16:32:04', 1, 6),
(269, 'Corsair RM1000e', '1000W, 80+ Gold, Fully Modular', 14999.00, 13999.00, 'uploads/img_692f14e622319.jpeg', 16, 30, 1, '2025-12-02 15:47:21', '2025-12-02 16:33:42', 3, 40),
(270, 'Dell Latitude 9440', 'Intel Core i7-1365U, 16GB DDR5, 512GB SSD, 14\" FHD+, 24hr Battery', 159999.00, 149999.00, 'uploads/img_692f14a19549e.png', 2, 25, 1, '2025-12-02 15:47:21', '2025-12-02 16:32:33', 1, 4),
(271, 'Logitech G Pro X Superlight', 'Wireless Gaming, 25K HERO Sensor, 63g, RGB', 13999.00, 12999.00, 'uploads/img_692f14fd7dd83.png', 23, 35, 1, '2025-12-02 15:47:21', '2025-12-02 16:34:05', 5, 54),
(272, 'ASUS ROG Maximus Z790 Hero', 'Intel Z790, LGA1700, DDR5, PCIe 5.0, WiFi 7', 54999.00, 51999.00, 'uploads/img_692fc2d925036.png', 13, 25, 1, '2025-12-02 15:47:21', '2025-12-03 04:55:53', 3, 31),
(273, 'Razer Blade 15', 'Intel Core i7-13800H, RTX 4060, 16GB DDR5, 1TB SSD, 15.6\" FHD 360Hz', 249999.00, 234999.00, 'uploads/img_692fc2c0981af.png', 1, 12, 1, '2025-12-02 15:47:21', '2025-12-03 04:55:28', 1, 1),
(274, 'Noctua NH-D15', 'Dual Tower Air Cooler, 140mm Fans, AM5/LGA1700', 9999.00, 9499.00, 'uploads/img_692fc2abe2f82.jpeg', 17, 40, 1, '2025-12-02 15:47:21', '2025-12-03 04:55:07', 3, 42),
(275, 'HP Omen 17', 'AMD Ryzen 9 7940HS, RTX 4070, 32GB DDR5, 1TB SSD, 17.3\" QHD 165Hz', 219999.00, 219999.00, 'uploads/img_692fc28d9c9cf.png', 1, 10, 0, '2025-12-02 15:47:21', '2025-12-03 04:54:37', 1, 1),
(276, 'MSI Aegis Ti5', 'Intel Core i9-13900KF, RTX 4080, 32GB DDR5, 2TB NVMe', 449999.00, 449999.00, 'uploads/img_692fc27b3879b.png', 6, 10, 0, '2025-12-02 15:47:21', '2025-12-03 04:54:19', 2, 14),
(277, 'Intel Core i7-14700K', '20 cores, 28 threads, 5.6GHz Boost, LGA1700', 44999.00, 44999.00, 'uploads/img_692fc2631149b.png', 11, 35, 0, '2025-12-02 15:47:21', '2025-12-03 04:53:55', 3, 26),
(278, 'ASUS VivoBook 15', 'Intel Core i3-1215U, 8GB DDR4, 256GB SSD, 15.6\" FHD', 37999.00, 34999.00, 'uploads/img_692fc244c13ec.png', 3, 55, 1, '2025-12-02 15:47:21', '2025-12-03 04:53:24', 1, 6),
(279, 'Kingston Fury Beast DDR5', '32GB (2x16GB), 6000MHz, CL36', 14999.00, 13999.00, 'uploads/img_692fc22c487cf.png', 14, 45, 1, '2025-12-02 15:47:21', '2025-12-03 04:53:00', 3, 35),
(280, 'HP ProDesk 600 G9', 'Intel Core i5-13500, 16GB DDR5, 512GB NVMe, Intel UHD 770', 74999.00, 74999.00, 'uploads/img_692fc21b8410d.png', 7, 35, 0, '2025-12-02 15:47:21', '2025-12-03 04:52:43', 2, 17),
(281, 'Samsung Odyssey Neo G9', '49\" Dual QHD, 240Hz, Mini LED, G-Sync, 1000R Curve', 249999.00, 249999.00, 'uploads/img_692fc2051e36a.png', 19, 10, 0, '2025-12-02 15:47:21', '2025-12-03 04:52:21', 4, 46),
(282, 'ASUS ROG Zephyrus G14', 'AMD Ryzen 9 7940HS, RTX 4060, 16GB DDR5, 1TB SSD, 14\" QHD 165Hz, VR Ready', 179999.00, 164999.00, 'uploads/img_692fc1efd050b.png', 1, 18, 1, '2025-12-02 15:47:21', '2025-12-03 04:51:59', 1, 2),
(283, 'Anker USB-C to USB-C 100W', '2m, USB 3.2 Gen 2, 10Gbps Data, E-Marker Chip', 1499.00, 1299.00, 'uploads/img_692fc1d022046.jpg', 26, 120, 1, '2025-12-02 15:47:21', '2025-12-03 04:51:28', 6, 63),
(284, 'WD Black SN850X', '2TB NVMe Gen 4, 7300MB/s Read, 6600MB/s Write', 18999.00, 18999.00, 'uploads/img_692fc1c02e028.jpeg', 15, 40, 0, '2025-12-02 15:47:21', '2025-12-03 04:51:12', 3, 37),
(285, 'Alienware Aurora R15', 'Intel Core i9-13900F, RTX 4070 Ti, 32GB DDR5, 1TB NVMe + 2TB HDD', 359999.00, 334999.00, 'uploads/img_692fc1b347f66.png', 6, 12, 1, '2025-12-02 15:47:21', '2025-12-03 04:50:59', 2, 14),
(286, 'G.Skill Ripjaws V DDR4', '16GB (2x8GB), 3200MHz, CL16', 4999.00, 4999.00, 'uploads/img_692fc1a12a4bf.png', 14, 60, 0, '2025-12-02 15:47:21', '2025-12-03 04:50:41', 3, 34),
(287, 'LG UltraGear 27GR95QE-B', '27\" QHD OLED, 240Hz, G-Sync, 0.03ms', 99999.00, 94999.00, 'uploads/img_692fc1887dbcc.png', 19, 15, 1, '2025-12-02 15:47:21', '2025-12-03 04:50:16', 4, 46),
(288, 'Acer Predator Helios 16', 'Intel Core i7-13700HX, RTX 4070, 16GB DDR5, 1TB SSD, 16\" WQXGA 240Hz, VR Ready', 199999.00, 199999.00, 'uploads/img_692fc177bc874.png', 1, 14, 0, '2025-12-02 15:47:21', '2025-12-03 04:49:59', 1, 2),
(289, 'SteelSeries Arctis Nova Pro Wireless', 'Gaming Headset, Active ANC, Dual Wireless, 38hr Battery', 34999.00, 32999.00, 'uploads/img_692fc1670fe74.png', 24, 20, 1, '2025-12-02 15:47:21', '2025-12-03 04:49:43', 5, 56),
(290, 'Lenovo ThinkPad X1 Carbon Gen 11', 'Intel Core i7-1355U, 32GB LPDDR5, 1TB SSD, 14\" WUXGA, 18hr Battery', 189999.00, 189999.00, 'uploads/img_692fc15275b93.png', 2, 20, 0, '2025-12-02 15:47:21', '2025-12-03 04:49:22', 1, 4),
(291, 'Intel Core i5-14600K', '14 cores, 20 threads, 5.3GHz Boost, LGA1700', 29999.00, 27999.00, 'uploads/img_692fc13d9dc7c.png', 11, 40, 1, '2025-12-02 15:47:21', '2025-12-03 04:49:01', 3, 26),
(292, 'NVIDIA GeForce RTX 4080 SUPER', '16GB GDDR6X, 2.55GHz Boost, 10240 CUDA Cores', 119999.00, 119999.00, 'uploads/img_692fc12737040.png', 12, 20, 0, '2025-12-02 15:47:21', '2025-12-03 04:48:39', 3, 28),
(293, 'Corsair Vengeance i7400', 'Intel Core i7-13700K, RTX 4090, 64GB DDR5, 2TB NVMe, RGB', 499999.00, 499999.00, 'uploads/img_692fc11165b3c.png', 6, 7, 0, '2025-12-02 15:47:21', '2025-12-03 04:48:17', 2, 15),
(294, 'Acer Chromebook 315', 'Intel Celeron N4020, 4GB RAM, 64GB eMMC, 15.6\" HD', 24999.00, 22999.00, 'uploads/img_692fc0fc6924b.png', 3, 60, 1, '2025-12-02 15:47:21', '2025-12-03 04:47:56', 1, 7),
(295, 'Lian Li O11 Dynamic EVO', 'Mid Tower, Tempered Glass, ATX/Mini-ITX, RGB Support', 17999.00, 16999.00, 'uploads/img_692fc0e5642c9.png', 18, 30, 1, '2025-12-02 15:47:21', '2025-12-03 04:47:33', 3, 44),
(296, 'HP EliteBook 840 G10', 'Intel Core i5-1335U, 16GB DDR5, 512GB SSD, 14\" FHD, 20hr Battery', 134999.00, 124999.00, 'uploads/img_692fc0c188ecb.png', 2, 30, 1, '2025-12-02 15:47:21', '2025-12-03 04:46:57', 1, 4),
(297, 'Belkin Ultra High Speed HDMI', '2m, 8K@60Hz, 4K@120Hz, Dolby Vision, Braided', 2999.00, 2999.00, 'uploads/img_692fc069442b0.jpg', 26, 80, 0, '2025-12-02 15:47:21', '2025-12-03 04:45:29', 6, 62),
(298, 'Lenovo Legion Pro 7i', 'Intel Core i9-13900HX, RTX 4080, 32GB DDR5, 2TB SSD, 16\" WQXGA 240Hz, RGB', 379999.00, 349999.00, 'uploads/img_692fc04173643.png', 1, 7, 1, '2025-12-02 15:47:21', '2025-12-03 04:44:49', 1, 3),
(299, 'MSI MPG Z790 Carbon WiFi', 'Intel Z790, LGA1700, DDR5, PCIe 5.0, WiFi 6E', 44999.00, 44999.00, 'uploads/img_692fc028ae063.png', 13, 30, 0, '2025-12-02 15:47:21', '2025-12-03 04:44:24', 3, 31),
(300, 'ASUS TUF Gaming VG28UQL1A', '28\" 4K, 144Hz, IPS, G-Sync Compatible, HDR400', 54999.00, 54999.00, 'uploads/img_692fc0145a912.png', 19, 20, 0, '2025-12-02 15:47:21', '2025-12-03 04:44:04', 4, 47),
(301, 'CyberPowerPC Gamer Xtreme VR', 'Intel Core i7-13700KF, RTX 4070, 32GB DDR5, 1TB NVMe, RGB', 289999.00, 269999.00, 'uploads/img_692fc000bf974.png', 6, 15, 1, '2025-12-02 15:47:21', '2025-12-03 04:43:44', 2, 15),
(302, 'Crucial P5 Plus', '1TB NVMe Gen 4, 6600MB/s Read, 5000MB/s Write', 9999.00, 9299.00, 'uploads/img_692fbfeb762a3.jpeg', 15, 45, 1, '2025-12-02 15:47:21', '2025-12-03 04:43:23', 3, 37),
(303, 'AMD Ryzen 9 7950X', '16 cores, 32 threads, 5.7GHz Boost, AM5', 54999.00, 54999.00, 'uploads/img_692fbfdecc97e.png', 11, 28, 0, '2025-12-02 15:47:21', '2025-12-03 04:43:10', 3, 27),
(304, 'Keychron K8 Pro', 'Wireless Mechanical, Hot-Swappable, RGB, QMK/VIA', 9999.00, 9999.00, 'uploads/img_692fbfaba4610.png', 22, 30, 0, '2025-12-02 15:47:21', '2025-12-03 04:42:19', 5, 52),
(305, 'MSI Raider GE78', 'Intel Core i9-13980HX, RTX 4090, 64GB DDR5, 4TB SSD, 17\" QHD+ 240Hz, RGB', 499999.00, 499999.00, 'uploads/img_692fbf8046da7.png', 1, 4, 0, '2025-12-02 15:47:21', '2025-12-03 04:41:36', 1, 3),
(306, 'Lenovo ThinkCentre M90q Gen 3', 'Intel Core i7-13700T, 16GB DDR5, 512GB NVMe, Compact', 94999.00, 89999.00, 'uploads/img_692fbf672256b.png', 7, 30, 1, '2025-12-02 15:47:21', '2025-12-03 04:41:11', 2, 18),
(307, 'HP E24 G5', '23.8\" FHD, IPS, Low Blue Light, VGA/HDMI/DP', 14999.00, 14999.00, 'uploads/img_692fbe87c14b5.png', 20, 45, 0, '2025-12-02 15:47:21', '2025-12-03 04:37:27', 4, 48),
(308, 'ASUS ExpertBook B9', 'Intel Core i7-1355U, 16GB LPDDR5, 1TB SSD, 14\" FHD, 22hr Battery', 169999.00, 169999.00, 'uploads/img_692fbe72df1d8.png', 2, 15, 0, '2025-12-02 15:47:21', '2025-12-03 04:37:06', 1, 4),
(309, 'HP Chromebook 14a', 'Intel Celeron N4500, 4GB RAM, 64GB eMMC, 14\" HD', 26999.00, 26999.00, 'uploads/img_692fbe625a643.png', 3, 50, 0, '2025-12-02 15:47:21', '2025-12-03 04:36:50', 1, 7),
(310, 'NVIDIA GeForce RTX 4070 Ti SUPER', '16GB GDDR6X, 2.61GHz Boost, 8448 CUDA Cores', 89999.00, 84999.00, 'uploads/img_692fbe4e5b373.png', 12, 25, 1, '2025-12-02 15:47:21', '2025-12-03 04:36:30', 3, 28),
(311, 'Gigabyte B760 AORUS Elite', 'Intel B760, LGA1700, DDR5, PCIe 4.0', 19999.00, 18999.00, 'uploads/img_692fbe399b7d0.png', 13, 40, 1, '2025-12-02 15:47:21', '2025-12-03 04:36:09', 3, 31),
(312, 'NZXT Player Three Prime', 'AMD Ryzen 7 7800X3D, RTX 4070 Ti, 32GB DDR5, 2TB NVMe, eSports', 349999.00, 349999.00, 'uploads/img_692fbe283c915.png', 6, 10, 0, '2025-12-02 15:47:21', '2025-12-03 04:35:52', 2, 16),
(313, 'Corsair Dominator Platinum DDR5', '64GB (2x32GB), 6400MHz, CL32', 34999.00, 34999.00, 'uploads/img_692fbe13838e4.png', 14, 30, 0, '2025-12-02 15:47:21', '2025-12-03 04:35:31', 3, 35),
(314, 'Dell Latitude 7440', 'Intel Core i7-1365U, 16GB DDR5, 512GB SSD, 14\" FHD Touch, 15hr Battery', 149999.00, 139999.00, 'uploads/img_692fbe00162a4.png', 2, 22, 1, '2025-12-02 15:47:21', '2025-12-03 04:35:12', 1, 5),
(315, 'Razer DeathAdder V3 Pro', 'Wireless Gaming, Focus Pro 30K Sensor, 63g, RGB', 14999.00, 14999.00, 'uploads/img_692fbdea6394e.png', 23, 30, 0, '2025-12-02 15:47:21', '2025-12-03 04:34:50', 5, 54),
(316, 'Samsung 870 EVO', '2TB SATA SSD, 560MB/s Read, 530MB/s Write', 14999.00, 14999.00, 'uploads/img_692fbdcaa10bd.jpeg', 15, 50, 0, '2025-12-02 15:47:21', '2025-12-03 04:34:18', 3, 38),
(317, 'Gigabyte M32U', '32\" 4K, 144Hz, IPS, FreeSync Premium Pro, HDR400', 59999.00, 56999.00, 'uploads/img_692f244889291.png', 19, 18, 1, '2025-12-02 15:47:21', '2025-12-02 17:39:20', 4, 47),
(318, 'ASUS ExpertCenter D7 Mini', 'Intel Core i5-13500T, 8GB DDR5, 512GB NVMe, Compact', 59999.00, 59999.00, 'uploads/img_692f2437442fd.png', 7, 45, 0, '2025-12-02 15:47:21', '2025-12-02 17:39:03', 2, 18),
(319, 'Apple Thunderbolt 4 Pro Cable', '1.8m, 40Gbps, 100W Charging, Braided', 12900.00, 12900.00, 'uploads/img_692f242543908.jpg', 26, 50, 0, '2025-12-02 15:47:21', '2025-12-02 17:38:45', 6, 63),
(320, 'Lenovo ThinkPad X1 Yoga Gen 8', 'Intel Core i7-1355U, 16GB LPDDR5, 512GB SSD, 14\" WUXGA Touch', 179999.00, 179999.00, 'uploads/img_692f2417545c2.png', 2, 18, 0, '2025-12-02 15:47:21', '2025-12-02 17:38:31', 1, 5),
(321, 'AMD Ryzen 7 7800X3D', '8 cores, 16 threads, 5.0GHz Boost, 96MB Cache, AM5', 44999.00, 41999.00, 'uploads/img_692f2407ba8c2.png', 11, 32, 1, '2025-12-02 15:47:21', '2025-12-02 17:38:15', 3, 27),
(322, 'Lenovo ThinkPad E14', 'Intel Core i5-1235U, 16GB DDR4, 512GB SSD, 14\" FHD, Programming', 64999.00, 59999.00, 'uploads/img_692f23f681499.png', 3, 35, 1, '2025-12-02 15:47:21', '2025-12-02 17:37:58', 1, 8),
(323, 'EVGA SuperNOVA 850 G7', '850W, 80+ Gold, Fully Modular', 12999.00, 12999.00, 'uploads/img_692f2381f25b7.jpeg', 16, 35, 0, '2025-12-02 15:47:21', '2025-12-02 17:36:01', 3, 40),
(324, 'HP Z8 G5', 'Intel Xeon W9-3495X, RTX A6000, 128GB DDR5 ECC, 4TB NVMe, AI/ML', 899999.00, 849999.00, 'uploads/img_692f23756d8bc.png', 8, 5, 1, '2025-12-02 15:47:21', '2025-12-02 17:35:49', 2, 19),
(325, 'BenQ PD2706UA', '27\" 4K, IPS, USB-C 96W PD, HDR400, Color Accurate', 54999.00, 51999.00, 'uploads/img_692f23663b60d.png', 20, 25, 1, '2025-12-02 15:47:21', '2025-12-02 17:35:34', 4, 49),
(326, 'Acer Aspire 5', 'Intel Core i5-1235U, 8GB DDR4, 512GB SSD, 15.6\" FHD', 49999.00, 49999.00, 'uploads/img_692f232451654.png', 3, 40, 0, '2025-12-02 15:47:21', '2025-12-02 17:34:28', 1, 6),
(327, 'Glorious GMMK Pro', 'Custom Mechanical, Gasket Mount, Hot-Swappable, CNC Aluminum', 19999.00, 18999.00, 'uploads/img_692f2306d93d6.png', 22, 20, 1, '2025-12-02 15:47:21', '2025-12-02 17:33:58', 5, 53),
(328, 'NVIDIA GeForce RTX 4060 Ti', '8GB GDDR6, 2.54GHz Boost, 4352 CUDA Cores', 44999.00, 44999.00, 'uploads/img_692f22e98f2b1.png', 12, 30, 0, '2025-12-02 15:47:21', '2025-12-02 17:33:29', 3, 28),
(329, 'Dell XPS 13 Plus', 'Intel Core i7-1360P, 16GB LPDDR5, 512GB SSD, 13.4\" FHD+', 169999.00, 154999.00, 'uploads/img_692f22d676bcf.png', 4, 20, 1, '2025-12-02 15:47:21', '2025-12-02 17:33:10', 1, 9),
(330, 'G.Skill Trident Z5 RGB DDR5', '32GB (2x16GB), 6400MHz, CL32, RGB', 19999.00, 18499.00, 'uploads/img_692f22c46e984.png', 14, 40, 1, '2025-12-02 15:47:21', '2025-12-02 17:32:52', 3, 36),
(331, 'Fractal Design Meshify 2 Compact', 'Mini Tower, Mesh Front, Micro-ATX/Mini-ITX', 9999.00, 9999.00, 'uploads/img_692f215cab567.png', 18, 35, 0, '2025-12-02 15:47:21', '2025-12-02 17:26:52', 3, 44),
(332, 'Lenovo ThinkStation P620', 'AMD Threadripper PRO 5995WX, RTX A5000, 128GB DDR4, 2TB NVMe, AI/ML', 749999.00, 749999.00, 'uploads/img_692f213cd7e02.png', 8, 6, 0, '2025-12-02 15:47:21', '2025-12-02 17:26:20', 2, 19),
(333, 'AMD Radeon RX 7900 XTX', '24GB GDDR6, 2.5GHz Boost, 96 CUs', 99999.00, 94999.00, 'uploads/img_692f20e46ebdf.png', 12, 18, 1, '2025-12-02 15:47:21', '2025-12-02 17:24:52', 3, 29),
(334, 'Dell Inspiron 15 3520', 'Intel Core i7-1255U, 16GB DDR4, 512GB SSD, 15.6\" FHD, Programming', 69999.00, 69999.00, 'uploads/img_692f20cf8bb07.png', 3, 30, 0, '2025-12-02 15:47:21', '2025-12-02 17:24:31', 1, 8),
(335, 'LG 27UP850-W', '27\" 4K, IPS, USB-C 96W PD, HDR400, Height Adjustable', 49999.00, 49999.00, 'uploads/img_692f20bd869bf.png', 20, 30, 0, '2025-12-02 15:47:21', '2025-12-02 17:24:13', 4, 49),
(336, 'MacBook Air M2', 'Apple M2, 8GB Unified, 256GB SSD, 13.6\" Liquid Retina', 119900.00, 119900.00, 'uploads/img_692f20a9b614c.png', 4, 25, 0, '2025-12-02 15:47:21', '2025-12-02 17:23:53', 1, 9),
(337, 'ASUS ROG Crosshair X670E Hero', 'AMD X670E, AM5, DDR5, PCIe 5.0, WiFi 6E', 54999.00, 54999.00, 'uploads/img_692f209bf18df.png', 13, 22, 0, '2025-12-02 15:47:21', '2025-12-02 17:23:39', 3, 32),
(338, 'Corsair Dark Core RGB Pro SE', 'Wireless Gaming, 18K Sensor, Qi Charging, RGB', 9999.00, 9299.00, 'uploads/img_692f208b38c47.png', 23, 28, 1, '2025-12-02 15:47:21', '2025-12-02 17:23:23', 5, 55),
(339, 'Intel NUC 13 Pro', 'Intel Core i7-1360P, 16GB DDR5, 512GB NVMe, Fanless', 74999.00, 69999.00, 'uploads/img_692f207c41845.png', 9, 25, 1, '2025-12-02 15:47:21', '2025-12-02 17:23:08', 2, 21),
(340, 'Crucial MX500', '1TB SATA SSD, 560MB/s Read, 510MB/s Write', 7999.00, 7499.00, 'uploads/img_692fbdda2cb11.jpeg', 15, 55, 1, '2025-12-02 15:47:21', '2025-12-03 04:34:34', 3, 38),
(341, 'AmazonBasics Cat8 Ethernet', '3m, 40Gbps, Shielded, Gold-Plated, 2000MHz', 1299.00, 1099.00, 'uploads/img_692f206124d6b.png', 26, 150, 1, '2025-12-02 15:47:21', '2025-12-02 17:22:41', 6, 64),
(342, 'HP Spectre x360 14', 'Intel Core i7-1355U, 16GB LPDDR5, 1TB SSD, 13.5\" WUXGA+', 179999.00, 169999.00, 'uploads/img_692f204a7c89f.png', 4, 15, 1, '2025-12-02 15:47:21', '2025-12-02 17:22:18', 1, 9),
(343, 'Samsung Odyssey G9 Neo', '49\" Dual QHD, 240Hz, Mini LED, 1000R, Quantum HDR 2000', 229999.00, 214999.00, 'uploads/img_692f203c0d43e.png', 21, 8, 1, '2025-12-02 15:47:21', '2025-12-02 17:22:04', 4, 50),
(344, 'Thermaltake Toughpower GF3', '850W, 80+ Gold, Fully Modular', 11999.00, 11299.00, 'uploads/img_692f202dc2c10.jpeg', 16, 40, 1, '2025-12-02 15:47:21', '2025-12-02 17:21:49', 3, 40),
(345, 'AMD Ryzen 5 7600X', '6 cores, 12 threads, 5.3GHz Boost, AM5', 24999.00, 24999.00, 'uploads/img_692f201fed651.png', 11, 45, 0, '2025-12-02 15:47:21', '2025-12-02 17:21:35', 3, 27),
(346, 'Dell Precision 7960', 'Intel Xeon W7-3465X, RTX A5500, 64GB DDR5, 2TB NVMe, Video Editing', 549999.00, 519999.00, 'uploads/img_692f200fdac75.png', 8, 8, 1, '2025-12-02 15:47:21', '2025-12-02 17:21:19', 2, 20),
(347, 'HyperX Cloud Alpha Wireless', 'Gaming Headset, DTS Spatial Audio, 300hr Battery', 19999.00, 19999.00, 'uploads/img_692f2002ed352.png', 24, 25, 0, '2025-12-02 15:47:21', '2025-12-02 17:21:06', 5, 56),
(348, 'Lenovo Yoga Slim 7 Pro', 'AMD Ryzen 7 7840HS, 16GB LPDDR5, 1TB SSD, 14.5\" 3K, 18hr Battery', 129999.00, 129999.00, 'uploads/img_692f1ff3e6b2c.png', 4, 22, 0, '2025-12-02 15:47:21', '2025-12-02 17:20:51', 1, 10),
(349, 'AMD Radeon RX 7800 XT', '16GB GDDR6, 2.43GHz Boost, 60 CUs', 64999.00, 64999.00, 'uploads/img_692f1ea5a26aa.png', 12, 22, 0, '2025-12-02 15:47:21', '2025-12-02 17:15:17', 3, 29),
(350, 'MSI MAG B650 Tomahawk WiFi', 'AMD B650, AM5, DDR5, PCIe 4.0, WiFi 6E', 24999.00, 23999.00, 'uploads/img_692f1e7ac0cb3.png', 13, 35, 1, '2025-12-02 15:47:21', '2025-12-02 17:14:34', 3, 32),
(351, 'ASUS PN64', 'AMD Ryzen 9 7940HS, 32GB DDR5, 1TB NVMe, Fanless', 94999.00, 94999.00, 'uploads/img_692f1e661c53d.png', 9, 20, 0, '2025-12-02 15:47:21', '2025-12-02 17:14:14', 2, 21),
(352, 'Seagate BarraCuda', '4TB HDD, 7200RPM, 256MB Cache', 8999.00, 8999.00, 'uploads/img_692f1e562cd73.jpeg', 15, 60, 0, '2025-12-02 15:47:21', '2025-12-02 17:13:58', 3, 39),
(353, 'ASUS ZenBook 14 OLED', 'Intel Core i7-1355U, 16GB LPDDR5, 512GB SSD, 14\" 2.8K OLED, 20hr Battery', 119999.00, 109999.00, 'uploads/img_692f1e42679b2.png', 4, 28, 1, '2025-12-02 15:47:21', '2025-12-02 17:13:38', 1, 10),
(354, 'MSI MEG 342C QD-OLED', '34\" UWQHD, 175Hz, QD-OLED, 1800R, DisplayHDR 400', 119999.00, 119999.00, 'uploads/img_692f1e2f8bca8.png', 21, 12, 0, '2025-12-02 15:47:21', '2025-12-02 17:13:19', 4, 50),
(355, 'SteelSeries Rival 3', 'Wired Gaming, TrueMove Core Sensor, RGB', 2999.00, 2999.00, 'uploads/img_692f1e20b4fd5.png', 23, 40, 0, '2025-12-02 15:47:21', '2025-12-02 17:13:04', 5, 55),
(356, 'Corsair Vengeance RGB Pro DDR4', '32GB (2x16GB), 3600MHz, CL18, RGB', 11999.00, 11999.00, 'uploads/img_692f1d28eb4c2.png', 14, 38, 0, '2025-12-02 15:47:21', '2025-12-02 17:08:56', 3, 36),
(357, 'HP Z4 G5', 'Intel Xeon W5-3435X, RTX 4000 Ada, 64GB DDR5, 2TB NVMe, Video Editing', 429999.00, 429999.00, 'uploads/img_692f1d17b73cc.png', 8, 10, 0, '2025-12-02 15:47:21', '2025-12-02 17:08:39', 2, 20),
(358, 'Anker 735 GaNPrime', '65W USB-C Power Adapter, 3-Port, Foldable, GaN Technology', 4999.00, 4699.00, 'uploads/img_692f1d03a22aa.jpg', 27, 60, 1, '2025-12-02 15:47:21', '2025-12-02 17:08:19', 6, 65),
(359, 'Dell XPS 13 2-in-1', 'Intel Core i7-1250U, 16GB LPDDR5, 512GB SSD, 13.4\" FHD+ Touch', 159999.00, 159999.00, 'uploads/img_692f1cf73e999.png', 4, 18, 0, '2025-12-02 15:47:21', '2025-12-02 17:08:07', 1, 11),
(360, 'Cooler Master MWE 650 Bronze V2', '650W, 80+ Bronze, Non-Modular', 5999.00, 5999.00, 'uploads/img_692f1ce8bf592.jpeg', 16, 50, 0, '2025-12-02 15:47:21', '2025-12-02 17:07:52', 3, 41),
(361, 'OnLogic Helix HX500', 'Intel Core i7-1265U, 16GB DDR5, 512GB NVMe, Industrial', 124999.00, 114999.00, 'uploads/img_692f1cd814ab3.png', 9, 15, 1, '2025-12-02 15:47:21', '2025-12-02 17:07:36', 2, 22),
(362, 'WD Blue', '2TB HDD, 7200RPM, 256MB Cache', 5499.00, 4999.00, 'uploads/img_692f1c21b4216.jpeg', 15, 65, 1, '2025-12-02 15:47:21', '2025-12-02 17:04:33', 3, 39),
(363, 'Shure SM7B', 'Studio Cardioid Dynamic Mic, XLR, Professional Broadcasting', 39999.00, 37999.00, 'uploads/img_692f1beb70666.jpeg', 24, 15, 1, '2025-12-02 15:47:21', '2025-12-02 17:03:39', 5, 57),
(364, 'Microsoft Surface Pro 9', 'Intel Core i7-1255U, 16GB LPDDR5, 512GB SSD, 13\" PixelSense, Detachable', 159999.00, 149999.00, 'uploads/img_692f1bdc5b7dc.png', 5, 30, 1, '2025-12-02 15:47:21', '2025-12-02 17:03:24', 1, 12),
(365, 'ASUS ROG Strix Z790-E Gaming', 'Intel Z790, LGA1700, DDR5, PCIe 5.0, WiFi 6E, RGB', 49999.00, 49999.00, 'uploads/img_692f1bcc2bd25.png', 13, 28, 0, '2025-12-02 15:47:21', '2025-12-02 17:03:08', 3, 33),
(366, 'Dell Alienware AW3423DWF', '34\" UWQHD, 165Hz, QD-OLED, 1800R, FreeSync Premium Pro', 94999.00, 89999.00, 'uploads/img_692f1ab034db5.png', 21, 15, 1, '2025-12-02 15:47:21', '2025-12-02 16:58:24', 4, 51),
(367, 'HP Elite x2 G9', 'Intel Core i7-1265U, 16GB DDR5, 512GB SSD, 13\" WUXGA+, Detachable', 169999.00, 169999.00, 'uploads/img_692f13022e3c0.png', 5, 20, 0, '2025-12-02 15:47:21', '2025-12-02 16:25:38', 1, 12),
(368, 'be quiet! Dark Rock Pro 4', 'Dual Tower Air Cooler, 135mm Fans, AM5/LGA1700', 8999.00, 8999.00, 'uploads/img_692f12f55072c.jpeg', 17, 35, 0, '2025-12-02 15:47:21', '2025-12-02 16:25:25', 3, 42),
(369, 'Advantech MIC-770', 'Intel Core i5-1235U, 16GB DDR4, 512GB SSD, Industrial Fanless', 149999.00, 149999.00, 'uploads/img_692f12cc21012.png', 9, 12, 0, '2025-12-02 15:47:21', '2025-12-02 16:24:44', 2, 22),
(370, 'Blue Yeti X', 'Studio USB Condenser Mic, 4 Patterns, LED Metering', 17999.00, 17999.00, 'uploads/img_692f12b1643a4.jpeg', 24, 22, 0, '2025-12-02 15:47:21', '2025-12-02 16:24:17', 5, 57),
(371, 'Apple 140W USB-C Power Adapter', 'GaN, Fast Charge, Compatible with MacBook Pro 16\"', 7900.00, 7900.00, 'uploads/img_692f12830cf8b.jpg', 27, 40, 0, '2025-12-02 15:47:21', '2025-12-02 16:23:31', 6, 65),
(372, 'Lenovo Yoga 9i Gen 8', 'Intel Core i7-1360P, 16GB LPDDR5, 1TB SSD, 14\" 2.8K OLED Touch, Foldable', 179999.00, 164999.00, 'uploads/img_692f1276a8456.png', 5, 25, 1, '2025-12-02 15:47:21', '2025-12-02 16:23:18', 1, 13),
(373, 'NVIDIA RTX 6000 Ada', '48GB GDDR6, 2.5GHz, 18176 CUDA Cores, AI/ML', 699999.00, 669999.00, 'uploads/img_692f126452529.png', 12, 8, 1, '2025-12-02 15:47:21', '2025-12-02 16:23:00', 3, 30),
(374, 'HP Envy 34 AIO', 'Intel Core i9-13900, 32GB DDR5, 1TB NVMe + 2TB HDD, 34\" 5K Touch', 279999.00, 259999.00, 'uploads/img_692f122aa1e45.png', 10, 10, 1, '2025-12-02 15:47:21', '2025-12-02 16:22:02', 2, 23),
(375, 'Tofu65 Aluminum Kit', 'Custom 65%, Hot-Swappable PCB, CNC Case, Switches Included', 14999.00, 14999.00, 'uploads/img_692f12197b787.png', 22, 15, 0, '2025-12-02 15:47:21', '2025-12-02 16:21:45', 5, 53),
(376, 'LG 34WP65C-B', '34\" UWQHD, 160Hz, VA, 1800R, HDR10', 39999.00, 39999.00, 'uploads/img_692f120235724.png', 21, 20, 0, '2025-12-02 15:47:21', '2025-12-02 16:21:22', 4, 51),
(377, 'HP Spectre x360 16', 'Intel Core i7-13700H, 32GB LPDDR5, 1TB SSD, 16\" 3K+ OLED Touch, Foldable', 229999.00, 229999.00, 'uploads/img_692f11f0c38d1.png', 5, 15, 0, '2025-12-02 15:47:21', '2025-12-02 16:21:04', 1, 13),
(378, 'Corsair iCUE H150i Elite LCD', '360mm AIO Liquid Cooler, LCD Display, RGB', 24999.00, 23499.00, 'uploads/img_692f11db8a93e.jpeg', 17, 25, 1, '2025-12-02 15:47:21', '2025-12-02 16:20:43', 3, 43),
(379, 'Dell Inspiron 27 7720', 'Intel Core i7-13700, 16GB DDR5, 1TB NVMe, 27\" QHD Touch', 169999.00, 169999.00, 'uploads/img_692f11ce73d52.png', 10, 15, 0, '2025-12-02 15:47:21', '2025-12-02 16:20:30', 2, 23),
(380, 'Sony WH-1000XM5', 'Wireless Headphones, Industry Leading ANC, 30hr Battery', 29999.00, 27999.00, 'uploads/img_692f11c1ed7b8.png', 24, 30, 1, '2025-12-02 15:47:21', '2025-12-02 16:20:17', 5, 58),
(381, 'EVGA 600 W1', '600W, 80+ White, Non-Modular', 4499.00, 3999.00, 'uploads/img_692f11b34d993.jpeg', 16, 55, 1, '2025-12-02 15:47:21', '2025-12-02 16:20:03', 3, 41),
(382, 'Cable Matters USB-C to HDMI 8K', 'DisplayPort Alt Mode, 8K@60Hz, 4K@120Hz Adapter', 2999.00, 2799.00, 'uploads/img_692f1167c5dc5.jpg', 27, 70, 1, '2025-12-02 15:47:21', '2025-12-02 16:18:47', 6, 66),
(383, 'Lenovo ThinkCentre M90a Pro', 'Intel Core i7-13700, 16GB DDR5, 512GB NVMe, 23.8\" FHD, Business', 134999.00, 124999.00, 'uploads/img_692f115b0a150.png', 10, 20, 1, '2025-12-02 15:47:21', '2025-12-02 16:18:35', 2, 24),
(384, 'NZXT Kraken X63', '280mm AIO Liquid Cooler, RGB', 14999.00, 14999.00, 'uploads/img_692f114ca4073.jpeg', 17, 30, 0, '2025-12-02 15:47:21', '2025-12-02 16:18:20', 3, 43),
(385, 'Bose QuietComfort 45', 'Wireless Headphones, Active ANC, 24hr Battery', 26999.00, 26999.00, 'uploads/img_692f11162843e.png', 24, 28, 0, '2025-12-02 15:47:21', '2025-12-02 16:17:26', 5, 58),
(386, 'HP ProOne 440 G9', 'Intel Core i5-13500, 16GB DDR5, 512GB NVMe, 23.8\" FHD, Business', 99999.00, 99999.00, 'uploads/img_692f11017383e.png', 10, 25, 0, '2025-12-02 15:47:21', '2025-12-02 16:17:05', 2, 24),
(387, 'Corsair 5000D Airflow RGB', 'Mid Tower, Tempered Glass, ATX, 4x RGB Fans', 14999.00, 13999.00, 'uploads/img_692f10c45d8a7.jpeg', 18, 28, 1, '2025-12-02 15:47:21', '2025-12-02 16:16:04', 3, 45),
(388, 'Elgato Game Capture 4K60 Pro MK.2', 'PCIe Capture Card, 4K60 HDR10, Low Latency', 29999.00, 28499.00, 'uploads/img_692f10b15eea2.jpeg', 25, 18, 1, '2025-12-02 15:47:21', '2025-12-02 16:15:45', 5, 59),
(389, 'iMac 24\" M3', 'Apple M3, 8GB Unified, 256GB SSD, 24\" 4.5K Retina, Home', 139900.00, 129900.00, 'uploads/img_692f1099da077.png', 10, 18, 1, '2025-12-02 15:47:21', '2025-12-02 16:15:21', 2, 25),
(390, 'NZXT H7 Flow RGB', 'Mid Tower, Mesh Panel, ATX, RGB Lighting', 13999.00, 13999.00, 'uploads/img_692f108d6091c.jpeg', 18, 32, 0, '2025-12-02 15:47:21', '2025-12-02 16:15:09', 3, 45),
(391, 'AVerMedia Live Gamer Bolt', 'Thunderbolt 3 Capture Card, 4K60 HDR, Zero-Lag Passthrough', 34999.00, 34999.00, 'uploads/img_692f10459ea77.jpeg', 25, 15, 0, '2025-12-02 15:47:21', '2025-12-02 16:13:57', 5, 59),
(392, 'Anker USB-C Hub 7-in-1', 'HDMI 4K@60Hz, USB 3.0, SD/TF, 100W PD, Aluminum', 3999.00, 3999.00, 'uploads/img_692f0ff017dcb.jpg', 27, 80, 0, '2025-12-02 15:47:21', '2025-12-02 16:12:32', 6, 66),
(393, 'Elgato Key Light Air', 'LED Panel, 1400 Lumens, WiFi Control, Edge-Lit', 13999.00, 12999.00, 'uploads/img_692f0fce8d35c.jpeg', 25, 25, 1, '2025-12-02 15:47:21', '2025-12-02 16:11:58', 5, 60),
(394, 'Neewer 2-Pack RGB LED Light', '360° Full Color, APP Control, Dimmable 2500K-8500K', 8999.00, 8999.00, 'uploads/img_692f0fb9512a4.jpeg', 25, 30, 0, '2025-12-02 15:47:21', '2025-12-02 16:11:37', 5, 60),
(395, 'Elgato Green Screen MT', 'Collapsible Chroma Key Panel, Auto-Locking Frame, Wrinkle-Free', 19999.00, 18999.00, 'uploads/img_692f0f60d46c5.jpeg', 25, 20, 1, '2025-12-02 15:47:21', '2025-12-02 16:10:08', 5, 61);

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `subcategory_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subcategories`
--

INSERT INTO `subcategories` (`id`, `group_id`, `category_id`, `subcategory_name`) VALUES
(1, 1, 1, 'High-End Gaming Laptops'),
(2, 1, 1, 'VR Ready Laptops'),
(3, 1, 1, 'RGB Gaming Laptops'),
(4, 1, 2, 'Long Battery Laptops'),
(5, 1, 2, 'Touchscreen Business Laptops'),
(6, 1, 3, 'Budget Laptops'),
(7, 1, 3, 'Chromebooks'),
(8, 1, 3, 'Programming Laptops'),
(9, 1, 4, 'High-End Ultrabooks'),
(10, 1, 4, 'Long Battery Ultrabooks'),
(11, 1, 4, 'Touch Ultrabooks'),
(12, 1, 5, 'Detachable Laptops'),
(13, 1, 5, 'Foldable Laptops'),
(14, 2, 6, 'High-End Gaming PCs'),
(15, 2, 6, 'RGB Gaming PCs'),
(16, 2, 6, 'eSports PCs'),
(17, 2, 7, 'High-End Office Desktops'),
(18, 2, 7, 'Compact Desktops'),
(19, 2, 8, 'AI/ML Workstations'),
(20, 2, 8, 'Video Editing Workstations'),
(21, 2, 9, 'Fanless Mini PCs'),
(22, 2, 9, 'Industrial Mini PCs'),
(23, 2, 10, 'Touch AIO PCs'),
(24, 2, 10, 'Business AIO PCs'),
(25, 2, 10, 'Home AIO PCs'),
(26, 3, 11, 'Intel CPUs'),
(27, 3, 11, 'AMD CPUs'),
(28, 3, 12, 'NVIDIA GPUs'),
(29, 3, 12, 'AMD GPUs'),
(30, 3, 12, 'AI GPUs'),
(31, 3, 13, 'Intel Boards'),
(32, 3, 13, 'AMD Boards'),
(33, 3, 13, 'Gaming Boards'),
(34, 3, 14, 'DDR4 RAM'),
(35, 3, 14, 'DDR5 RAM'),
(36, 3, 14, 'RGB RAM'),
(37, 3, 15, 'NVMe SSD'),
(38, 3, 15, 'SATA SSD'),
(39, 3, 15, 'HDD'),
(40, 3, 16, 'Modular PSU'),
(41, 3, 16, 'Non-Modular PSU'),
(42, 3, 17, 'Air Coolers'),
(43, 3, 17, 'Liquid Coolers'),
(44, 3, 18, 'Mini Tower'),
(45, 3, 18, 'RGB Cabinets'),
(46, 4, 19, 'High-End Gaming Monitors'),
(47, 4, 19, '4K Gaming Monitors'),
(48, 4, 20, 'Full HD'),
(49, 4, 20, '4K Monitors'),
(50, 4, 21, 'High-End Curved Monitors'),
(51, 4, 21, 'Ultra-Wide Curved'),
(52, 5, 22, 'Wireless'),
(53, 5, 22, 'Custom'),
(54, 5, 23, 'Wireless Mouse'),
(55, 5, 23, 'RGB Mouse'),
(56, 5, 24, 'Gaming Headsets'),
(57, 5, 24, 'Studio Mics'),
(58, 5, 24, 'Noise Cancellation'),
(59, 5, 25, 'Capture Cards'),
(60, 5, 25, 'Lights'),
(61, 5, 25, 'Green Screens'),
(62, 6, 26, 'High-End HDMI'),
(63, 6, 26, 'USB-C'),
(64, 6, 26, 'LAN Cables'),
(65, 6, 27, 'Power Adapters'),
(66, 6, 27, 'Display Adapters');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `is_admin`, `created_at`) VALUES
(2, 'Admin', 'admin@gmail.com', '$2y$10$wgYsrtgmBD28grtijNX9Wu/llv.WTYK.WgpAOmAz/d5zYzb6s9rMW', 1, '2025-11-26 15:57:15'),
(3, 'lovepreet', 'lovepreet@gmail.com', '$2y$10$aBEuG0hsUxe9Hs0EZeUxkOaBKG7XW/1HxfF.xYsIjyAyt0SPEvVtK', 0, '2025-12-02 07:13:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `groups_h`
--
ALTER TABLE `groups_h`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_history`
--
ALTER TABLE `order_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_products_group` (`group_id`),
  ADD KEY `fk_products_category` (`category_id`),
  ADD KEY `fk_products_subcategory` (`subcategory_id`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sub_group` (`group_id`),
  ADD KEY `fk_sub_category` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_history`
--
ALTER TABLE `order_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=396;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups_h` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_history`
--
ALTER TABLE `order_history`
  ADD CONSTRAINT `order_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_history_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_products_group` FOREIGN KEY (`group_id`) REFERENCES `groups_h` (`id`),
  ADD CONSTRAINT `fk_products_subcat` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_products_subcategory` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `fk_sub_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sub_group` FOREIGN KEY (`group_id`) REFERENCES `groups_h` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups_h` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
