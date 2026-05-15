-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 15, 2026 at 05:00 PM
-- Server version: 10.6.25-MariaDB-cll-lve
-- PHP Version: 8.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `motorfix_motor`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'The user who performed the action. NULL for system actions.',
  `action` varchar(255) NOT NULL COMMENT 'E.g., login_success, password_failed, product_deleted, user_created',
  `ip_address` varchar(64) DEFAULT NULL COMMENT 'Hashed IP address of the user',
  `details` text DEFAULT NULL COMMENT 'JSON-encoded context about the activity',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `ip_address`, `details`, `timestamp`) VALUES
(1, 4, 'car_make_created', '512b1613c8a64813d8383076b91993b5166496ac484e5f61ac65918f56a4c7a2', '{\"name\":\"Toyota\"}', '2025-10-15 11:43:30'),
(2, 4, 'product_created', '512b1613c8a64813d8383076b91993b5166496ac484e5f61ac65918f56a4c7a2', '{\"product_id\":\"2\",\"name\":\"HH67\"}', '2025-10-15 12:06:31'),
(3, 4, 'car_model_created', '512b1613c8a64813d8383076b91993b5166496ac484e5f61ac65918f56a4c7a2', '{\"model\":\"Supra\"}', '2025-10-15 12:14:43'),
(4, 4, 'product_created', '512b1613c8a64813d8383076b91993b5166496ac484e5f61ac65918f56a4c7a2', '{\"product_id\":\"3\",\"name\":\"BOSCH spare\"}', '2025-10-15 12:19:21'),
(5, 4, 'car_make_created', '512b1613c8a64813d8383076b91993b5166496ac484e5f61ac65918f56a4c7a2', '{\"name\":\"VOLVO\"}', '2025-10-15 13:03:21'),
(6, 4, 'image_deleted', 'd2f4c825f56b806cd46fd393de3bcee93a765e3ce5412479fadda4f06e27b6c3', '{\"image_id\":5}', '2025-10-16 08:23:32'),
(7, 4, 'image_deleted', 'd2f4c825f56b806cd46fd393de3bcee93a765e3ce5412479fadda4f06e27b6c3', '{\"image_id\":6}', '2025-10-16 08:23:41'),
(8, 4, 'product_created', 'd2f4c825f56b806cd46fd393de3bcee93a765e3ce5412479fadda4f06e27b6c3', '{\"product_id\":\"4\",\"name\":\"eng\"}', '2025-10-16 08:25:09'),
(9, 4, 'product_updated', 'd2f4c825f56b806cd46fd393de3bcee93a765e3ce5412479fadda4f06e27b6c3', '{\"product_id\":3,\"name\":\"BOSCH spare\"}', '2025-10-16 08:31:45'),
(10, 4, 'product_deleted', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"product_id\":3,\"name\":\"BOSCH spare\"}', '2025-10-16 18:31:49'),
(11, 4, 'product_deleted', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"product_id\":4,\"name\":\"eng\"}', '2025-10-16 18:31:53'),
(12, 4, 'product_deleted', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"product_id\":2,\"name\":\"HH67\"}', '2025-10-16 18:31:57'),
(13, 4, 'stock_deleted', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"inventory_id\":1,\"product\":\"Toyota Supra Engine\",\"quantity\":\"13\"}', '2025-10-16 18:33:18'),
(14, 4, 'car_make_deleted', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"id\":2,\"name\":\"VOLVO\"}', '2025-10-16 18:33:47'),
(15, 4, 'car_model_deleted', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"id\":1,\"name\":\"Supra\"}', '2025-10-16 18:33:58'),
(16, 4, 'car_make_deleted', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"id\":1,\"name\":\"Toyota\"}', '2025-10-16 18:34:03'),
(17, 4, 'product_deleted', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"product_id\":1,\"name\":\"Toyota Supra Engine\"}', '2025-10-16 18:43:51'),
(18, 4, 'category_deleted', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"id\":1,\"name\":\"Engine\"}', '2025-10-16 18:44:06'),
(19, 4, 'car_make_created', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"name\":\"Toyota\"}', '2025-10-16 18:44:36'),
(20, 4, 'category_created', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"name\":\"Engine\"}', '2025-10-16 18:44:44'),
(21, 4, 'car_model_created', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"model\":\"C546\"}', '2025-10-16 18:45:04'),
(22, 4, 'product_created', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"product_id\":\"5\",\"name\":\"FT67 Engine\"}', '2025-10-16 18:45:58'),
(23, 4, 'stock_added', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"product_id\":5,\"quantity\":3}', '2025-10-16 18:46:38'),
(24, 4, 'stock_added', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"product_id\":5,\"quantity\":13}', '2025-10-16 18:47:00'),
(25, 4, 'category_created', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"name\":\"Tire\"}', '2025-10-16 18:57:15'),
(26, 4, 'product_created', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"product_id\":\"6\",\"name\":\"Tire all season\"}', '2025-10-16 18:57:34'),
(27, 4, 'stock_added', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"product_id\":6,\"quantity\":30}', '2025-10-16 18:57:49'),
(28, 4, 'sale_recorded', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"sale_id\":\"2\",\"total\":600,\"profit\":600}', '2025-10-16 19:10:41'),
(29, 4, 'expense_recorded', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"count\":1,\"total_amount\":4000,\"date\":\"2025-10-17\"}', '2025-10-17 05:04:18'),
(30, 4, 'sale_recorded', '51641268d21cc9c1bcc5ba56cd93b445e8fd28593e5e323a5cff5acf301f2276', '{\"sale_id\":\"3\",\"total\":2400,\"profit\":2400}', '2025-10-17 05:25:15'),
(31, 4, 'sale_recorded', 'aae5b3e9cb5a9d93c3655049b2da8103da88de9ff2ef95952301af328bf758d6', '{\"sale_id\":\"4\",\"total\":70000,\"profit\":20000}', '2025-10-19 15:34:47'),
(32, 4, 'sale_recorded', 'aae5b3e9cb5a9d93c3655049b2da8103da88de9ff2ef95952301af328bf758d6', '{\"sale_id\":\"5\",\"total\":800,\"profit\":800}', '2025-10-19 15:35:05'),
(33, 4, 'expense_recorded', 'aae5b3e9cb5a9d93c3655049b2da8103da88de9ff2ef95952301af328bf758d6', '{\"count\":2,\"total_amount\":400,\"date\":\"2025-10-19\"}', '2025-10-19 15:35:35'),
(34, 4, 'category_created', '953ff633f3f6bbfcc9d8d10541edba33fefc4bbcb3b7f54666b5ba224e223e16', '{\"name\":\"CRV machine\"}', '2025-10-20 12:45:16'),
(35, 4, 'product_created', '953ff633f3f6bbfcc9d8d10541edba33fefc4bbcb3b7f54666b5ba224e223e16', '{\"product_id\":\"7\",\"name\":\"Green CRV machine\"}', '2025-10-20 12:45:56'),
(37, 4, 'stock_deleted', '953ff633f3f6bbfcc9d8d10541edba33fefc4bbcb3b7f54666b5ba224e223e16', '{\"inventory_id\":3,\"product\":\"Tire all season\",\"quantity\":\"11\"}', '2025-10-20 12:48:57'),
(38, 4, 'stock_deleted', '953ff633f3f6bbfcc9d8d10541edba33fefc4bbcb3b7f54666b5ba224e223e16', '{\"inventory_id\":2,\"product\":\"FT67 Engine\",\"quantity\":\"15\"}', '2025-10-20 12:49:06'),
(39, 4, 'car_model_deleted', '953ff633f3f6bbfcc9d8d10541edba33fefc4bbcb3b7f54666b5ba224e223e16', '{\"id\":2,\"name\":\"C546\"}', '2025-10-20 12:49:37'),
(40, 4, 'car_make_deleted', '953ff633f3f6bbfcc9d8d10541edba33fefc4bbcb3b7f54666b5ba224e223e16', '{\"id\":3,\"name\":\"Toyota\"}', '2025-10-20 12:49:41'),
(43, 4, 'car_make_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"name\":\"Toyota\"}', '2025-10-21 05:03:35'),
(44, 4, 'car_make_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"name\":\"Nissan\"}', '2025-10-21 05:03:44'),
(45, 4, 'car_make_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"name\":\"Isuzu\"}', '2025-10-21 05:04:01'),
(46, 4, 'car_make_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"name\":\"Honda\"}', '2025-10-21 05:04:18'),
(47, 4, 'car_make_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"name\":\"Mazda\"}', '2025-10-21 05:04:32'),
(48, 4, 'car_make_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"name\":\"Subaru\"}', '2025-10-21 05:04:42'),
(49, 4, 'car_model_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"model\":\"Corolla Fielder\"}', '2025-10-21 05:05:09'),
(50, 4, 'car_model_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"model\":\"Axio\"}', '2025-10-21 05:05:36'),
(51, 4, 'car_model_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"model\":\"Vitz\"}', '2025-10-21 05:05:59'),
(52, 4, 'car_model_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"model\":\"Passo\"}', '2025-10-21 05:06:18'),
(53, 4, 'car_model_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"model\":\"D-Max\"}', '2025-10-21 05:06:33'),
(54, 4, 'car_model_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"model\":\"RAV4\"}', '2025-10-21 05:06:55'),
(55, 4, 'car_model_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"model\":\"Fit\"}', '2025-10-21 05:07:19'),
(56, 4, 'car_model_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"model\":\"Jazz\"}', '2025-10-21 05:08:19'),
(57, 4, 'car_model_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"model\":\"Demio\"}', '2025-10-21 05:08:39'),
(58, 4, 'car_model_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"model\":\"Mazda2\"}', '2025-10-21 05:09:12'),
(59, 4, 'car_model_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"model\":\"Land Cruiser Prado\"}', '2025-10-21 05:09:40'),
(60, 4, 'car_model_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"model\":\"Forester\"}', '2025-10-21 05:09:58'),
(61, 4, 'car_model_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"model\":\"X-Trail\"}', '2025-10-21 05:10:15'),
(62, 4, 'car_model_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"model\":\"Harrier\"}', '2025-10-21 05:10:35'),
(63, 4, 'car_model_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"model\":\"Vanguard\"}', '2025-10-21 05:11:05'),
(64, 4, 'category_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"name\":\"metre\"}', '2025-10-21 05:37:03'),
(65, 4, 'category_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"name\":\"other\"}', '2025-10-21 05:37:13'),
(66, 4, 'product_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"product_id\":\"8\",\"name\":\"metre\"}', '2025-10-21 05:37:42'),
(67, 4, 'product_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"product_id\":\"9\",\"name\":\"test product\"}', '2025-10-21 05:38:08'),
(68, 4, 'product_created', '7d16c4404b562c723e7ae4805615c745163cb7b6818b8c17740a142a4ce0370e', '{\"product_id\":\"10\",\"name\":\"screw\"}', '2025-10-21 05:38:51'),
(69, 4, 'category_deleted', '25293dd6d5c959700eaa742768a42b74306ae63fb2a9110b151f5d44de10562e', '{\"id\":7,\"name\":\"tools\"}', '2025-10-23 11:29:14'),
(70, 4, 'category_created', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '{\"name\":\"Products\"}', '2025-10-27 16:46:40'),
(71, 4, 'category_updated', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '{\"id\":12,\"name\":\"Product\"}', '2025-10-27 16:49:09');

-- --------------------------------------------------------

--
-- Table structure for table `blog_media`
--

CREATE TABLE `blog_media` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `author_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Changed to NULL to allow ON DELETE SET NULL',
  `media_type` enum('image','video') NOT NULL,
  `file_path` varchar(255) NOT NULL COMMENT 'Path to the file in /u/media/blog/',
  `file_size` int(11) NOT NULL DEFAULT 0 COMMENT 'File size in bytes',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL COMMENT 'Will store Quill Delta (JSON) for rich content',
  `featured_image_url` varchar(255) DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `address` text NOT NULL,
  `Maps_pin` varchar(255) DEFAULT NULL,
  `contact_person_name` varchar(100) DEFAULT NULL,
  `contact_person_phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `address`, `Maps_pin`, `contact_person_name`, `contact_person_phone`, `is_active`, `created_at`) VALUES
(1, 'Main Branch', 'Jekima Plaza, Jogoo Road\r\nNairobi, Kenya', '', '', '', 1, '2025-10-14 12:25:12');

-- --------------------------------------------------------

--
-- Table structure for table `car_makes`
--

CREATE TABLE `car_makes` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `car_makes`
--

INSERT INTO `car_makes` (`id`, `name`) VALUES
(7, 'Honda'),
(6, 'Isuzu'),
(8, 'Mazda'),
(5, 'Nissan'),
(9, 'Subaru'),
(4, 'Toyota');

-- --------------------------------------------------------

--
-- Table structure for table `car_models`
--

CREATE TABLE `car_models` (
  `id` int(10) UNSIGNED NOT NULL,
  `make_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `year_start` smallint(6) DEFAULT NULL,
  `year_end` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `car_models`
--

INSERT INTO `car_models` (`id`, `make_id`, `name`, `year_start`, `year_end`) VALUES
(3, 4, 'Corolla Fielder', NULL, NULL),
(4, 4, 'Axio', NULL, NULL),
(5, 4, 'Vitz', NULL, NULL),
(6, 4, 'Passo', NULL, NULL),
(7, 6, 'D-Max', NULL, NULL),
(8, 4, 'RAV4', NULL, NULL),
(9, 7, 'Fit', NULL, NULL),
(10, 7, 'Jazz', NULL, NULL),
(11, 8, 'Demio', NULL, NULL),
(12, 8, 'Mazda2', NULL, NULL),
(13, 4, 'Land Cruiser Prado', NULL, NULL),
(14, 9, 'Forester', NULL, NULL),
(15, 5, 'X-Trail', NULL, NULL),
(16, 4, 'Harrier', NULL, NULL),
(17, 4, 'Vanguard', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `session_id` bigint(20) UNSIGNED NOT NULL,
  `sender` enum('visitor','agent') NOT NULL,
  `message_text` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `session_id`, `sender`, `message_text`, `is_read`, `timestamp`) VALUES
(1, 1, 'visitor', 'hi', 1, '2025-10-21 07:11:55'),
(2, 1, 'agent', 'hello', 1, '2025-10-21 07:12:11'),
(3, 1, 'visitor', 'how are you', 1, '2025-10-21 07:12:33'),
(4, 1, 'agent', 'i am good', 1, '2025-10-21 07:12:50'),
(5, 2, 'visitor', 'hi', 1, '2025-10-21 07:57:40'),
(6, 2, 'agent', 'how are you', 1, '2025-10-21 07:57:59'),
(7, 2, 'visitor', 'i am good', 1, '2025-10-21 07:58:13'),
(8, 3, 'visitor', 'hi', 1, '2025-10-21 07:58:47'),
(9, 4, 'visitor', 'hi', 1, '2025-10-21 08:10:55'),
(10, 4, 'visitor', 'hello', 1, '2025-10-21 08:11:02'),
(11, 4, 'agent', 'hi', 1, '2025-10-21 08:11:36'),
(12, 4, 'visitor', 'how are you', 1, '2025-10-21 08:11:48'),
(13, 4, 'agent', 'good', 1, '2025-10-21 08:11:59'),
(14, 5, 'visitor', 'hi', 1, '2025-10-21 08:23:59'),
(15, 5, 'agent', 'hi', 1, '2025-10-21 08:24:07'),
(16, 5, 'agent', 'how are you', 1, '2025-10-21 08:24:33'),
(17, 5, 'visitor', 'i am good', 1, '2025-10-21 08:24:42'),
(18, 6, 'visitor', 'hi', 1, '2025-10-21 08:39:23'),
(19, 6, 'agent', 'hi', 1, '2025-10-21 08:40:02'),
(20, 6, 'visitor', 'how are you', 1, '2025-10-21 08:40:25'),
(21, 6, 'agent', 'im', 1, '2025-10-21 08:46:45'),
(22, 6, 'agent', 'okay', 1, '2025-10-21 08:47:01'),
(23, 6, 'visitor', 'yes', 1, '2025-10-21 08:47:06');

-- --------------------------------------------------------

--
-- Table structure for table `chat_sessions`
--

CREATE TABLE `chat_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visitor_identifier` varchar(64) NOT NULL COMMENT 'Hashed IP or a unique session token',
  `visitor_name` varchar(100) DEFAULT NULL,
  `visitor_email` varchar(100) DEFAULT NULL,
  `visitor_phone` varchar(20) DEFAULT NULL,
  `assigned_user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'The customer care agent handling the chat',
  `status` enum('active','closed','offline_message') NOT NULL,
  `last_visitor_heartbeat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_agent_heartbeat` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `chat_sessions`
--

INSERT INTO `chat_sessions` (`id`, `visitor_identifier`, `visitor_name`, `visitor_email`, `visitor_phone`, `assigned_user_id`, `status`, `last_visitor_heartbeat`, `last_agent_heartbeat`, `created_at`) VALUES
(1, 'visitor_d3f88ce606239dcbdb5d6b410a613aaf', NULL, 'leonardmuia89@gmail.com', NULL, 5, 'closed', '2025-10-21 08:22:16', NULL, '2025-10-21 07:11:48'),
(2, 'session_4051c60a67610c1c6937c1d1eb1f3554', 'Anonymous Visitor', 'noemail@motorfix.co.ke', NULL, 5, 'closed', '2025-10-21 07:58:21', NULL, '2025-10-21 07:57:23'),
(3, 'session_4051c60a67610c1c6937c1d1eb1f3554', 'Anonymous Visitor', 'noemail@motorfix.co.ke', NULL, NULL, 'closed', '2025-10-21 08:23:18', NULL, '2025-10-21 07:58:25'),
(4, 'session_90f300884de1b827f047f5c7e8f5b868', 'Anonymous Visitor', 'noemail@motorfix.co.ke', NULL, 5, 'closed', '2025-10-21 08:22:07', NULL, '2025-10-21 08:10:51'),
(5, 'session_4051c60a67610c1c6937c1d1eb1f3554', 'leon', 'noemail@motorfix.co.ke', NULL, 5, 'closed', '2025-10-21 08:25:10', '2025-10-21 08:24:33', '2025-10-21 08:23:26'),
(6, 'session_4051c60a67610c1c6937c1d1eb1f3554', 'Anonymous Visitor', 'noemail@motorfix.co.ke', NULL, 5, 'closed', '2025-10-21 08:47:21', '2025-10-21 08:47:01', '2025-10-21 08:39:19'),
(7, 'session_68b4059769e70e41fecf2841bc268bd2', 'Tainya', 'noemail@motorfix.co.ke', NULL, NULL, 'closed', '2025-10-22 05:35:02', NULL, '2025-10-22 05:34:38');

-- --------------------------------------------------------

--
-- Table structure for table `contact_submissions`
--

CREATE TABLE `contact_submissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `contact_submissions`
--

INSERT INTO `contact_submissions` (`id`, `name`, `email`, `phone`, `subject`, `message`, `is_read`, `created_at`) VALUES
(1, 'Leon Muia', 'leonardmuia89@gmail.com', '0740080455', 'General Inquiry', 'test', 0, '2025-10-22 19:41:52'),
(2, 'Leonard Muia', 'leonardmuia89@gmail.com', '0700038188', 'General Inquiry', 'test mail', 0, '2025-10-22 20:02:36'),
(3, 'leon', 'not.provided@motorfix.co.ke', '', 'Other', 'yess', 0, '2025-10-30 08:14:24');

-- --------------------------------------------------------

--
-- Table structure for table `expense_records`
--

CREATE TABLE `expense_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'Employee who recorded the expense',
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `expense_records`
--

INSERT INTO `expense_records` (`id`, `branch_id`, `user_id`, `description`, `amount`, `expense_date`, `created_at`) VALUES
(1, 1, 4, 'Rent', 4000.00, '2025-10-17', '2025-10-17 05:04:18'),
(2, 1, 4, 'fare', 200.00, '2025-10-19', '2025-10-19 15:35:35'),
(3, 1, 4, 'token', 200.00, '2025-10-19', '2025-10-19 15:35:35');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `branch_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `purchase_price` decimal(10,2) DEFAULT 0.00 COMMENT 'Optional cost price per item, used for profit calculation',
  `selling_price` decimal(10,2) NOT NULL COMMENT 'The fixed selling price at this branch',
  `last_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logged_in_devices`
--

CREATE TABLE `logged_in_devices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `session_id` varchar(255) NOT NULL COMMENT 'PHP session ID for targeted logout',
  `remember_token` varchar(255) DEFAULT NULL COMMENT 'Token for persistent "Remember Me" login',
  `ip_address` varchar(255) NOT NULL,
  `user_agent` text NOT NULL COMMENT 'Browser and OS information',
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_seen` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1 for active session, 0 for logged out'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `logged_in_devices`
--

INSERT INTO `logged_in_devices` (`id`, `user_id`, `session_id`, `remember_token`, `ip_address`, `user_agent`, `login_time`, `last_seen`, `is_active`) VALUES
(1, 1, 't6d36qbhhmb076t3psdm3k0nm4', NULL, '102.0.24.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-14 11:01:02', '2025-10-14 11:17:15', 0),
(2, 1, 'dljb4f6avtcq97vo0k8krm3aen', NULL, '102.0.24.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-14 11:17:28', '2025-10-14 11:20:47', 0),
(3, 1, 'pf58n1umj3mnjrqqckmicl3idf', NULL, '102.0.24.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-14 11:21:09', '2025-10-14 12:00:33', 0),
(4, 1, '232buuj3kcm6pmstmd7umvppj4', NULL, '102.0.24.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-14 12:03:35', '2025-10-14 13:30:14', 0),
(5, 1, 'hnqsm7srjifi7tvr27degofu9p', NULL, '102.0.24.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-14 13:10:13', '2025-10-14 13:32:13', 0),
(6, 4, '6mbagukf35gpa4g3ikev4h8qvb', NULL, '102.0.24.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-14 13:32:24', '2025-10-14 13:32:37', 0),
(7, 1, '7a01tdtj29fu9epc48bif8p4lh', NULL, '102.0.24.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-14 13:33:56', '2025-10-14 13:40:21', 0),
(8, 4, '36tqovbsbihn6hmf4u9e25d0kt', NULL, '102.0.24.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-14 13:34:46', '2025-10-14 13:40:30', 0),
(9, 4, 'bs6dfa07l54rjl9h0fqprksh7j', NULL, '102.0.24.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-14 14:27:35', '2025-10-14 14:56:01', 0),
(10, 1, '7ddr4ulhvk3865ia4j1nsgb7a8', NULL, '102.0.24.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-14 14:53:53', '2025-10-20 11:17:06', 0),
(11, 4, 'qccq8k8sqhe7l51cdvtogisivb', NULL, '102.0.24.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-14 14:57:14', '2025-10-20 11:16:51', 0),
(12, 4, 'odkjft9efuo4qhnrriovgqvggj', NULL, '102.0.24.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-14 15:32:35', '2025-10-20 11:16:45', 0),
(13, 4, 'pvajlqvf4shu62i3gqavdu4nqo', NULL, '213.199.49.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-15 09:29:52', '2025-10-15 09:31:36', 0),
(14, 1, '7v98jcs1i56n9b72v8u5095jqv', NULL, '213.199.49.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-15 09:31:54', '2025-10-20 11:16:40', 0),
(15, 5, '9dginaaeeb9ff4ossi24bla9o3', NULL, '102.0.24.164', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-15 09:35:50', '2025-10-15 09:35:59', 0),
(16, 1, '1v8mc8g5b7gsm752vttdfuj3si', NULL, '102.0.24.164', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-15 09:36:22', '2025-10-20 11:16:34', 0),
(17, 4, '2q8981dotqfpodhp7o9nug65bt', NULL, '102.0.24.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-15 10:19:52', '2025-10-20 11:16:25', 0),
(18, 4, '3p1vkihv95ahh4f77mt84k7fur', NULL, '102.0.24.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-15 11:24:42', '2025-10-20 11:16:17', 0),
(19, 4, 'gc7upljmiji7t99fcisqs2ojio', NULL, '102.0.24.164', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-15 13:02:06', '2025-10-20 11:16:13', 0),
(20, 4, 'olout4ijc7st18k2h4udk5roit', NULL, '154.159.252.146', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-16 07:57:47', '2025-10-20 11:15:54', 0),
(21, 1, 'no8gaobt7h9a3nvcrst6aq8b7d', NULL, '154.159.252.146', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-16 08:01:35', '2025-10-20 11:15:47', 0),
(22, 4, 'akhjimkmsknrkg76em4rnidlj9', NULL, '105.160.4.87', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-16 18:16:43', '2025-10-20 11:15:41', 0),
(23, 4, 'enrip5jb8gh8okvpqd5u7kpl40', NULL, '105.160.4.87', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-17 04:49:23', '2025-10-20 11:15:34', 0),
(24, 4, 'kuala7rbt323l017gig3qoj5m8', NULL, '154.159.252.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-17 17:52:48', '2025-10-17 17:58:03', 0),
(25, 4, 's8gb3oi7ihoc6kktb71v1bhhl9', NULL, '154.159.252.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-17 17:59:11', '2025-10-19 15:15:03', 0),
(26, 5, '3e8d92qb3isvlmmj06elcfnak1', NULL, '154.159.252.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-19 14:25:46', '2025-10-19 14:52:18', 0),
(27, 1, '9b9b0cea9ae945c573b0f20a327d5c7b43d51c203b97601bcda68eeb35cd3303', NULL, '154.159.252.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-19 14:52:40', '2025-10-19 14:53:48', 0),
(28, 1, 'ff99008d6c2722f5413b4211891ca34bc21d5f7ba0365485c32fe7baa555f813', NULL, '154.159.252.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-19 14:55:46', '2025-10-20 11:15:27', 0),
(29, 1, '09df0588dade8b00d42bde3d2bc322c36c10c04ecd95fe6b80e81a0a4186790e', NULL, '154.159.252.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-19 15:04:32', '2025-10-19 15:06:04', 0),
(30, 1, '809fe02aeb140c6102c40bdc20171ad8460f4bbcc65daae0d1f2c9776c92670b', NULL, '154.159.252.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-19 15:06:40', '2025-10-19 15:19:27', 0),
(31, 1, 'd41cb88b323b2f6899624588c6b0469f7eac296aa8d6b4a4d05dae0727b60da5', NULL, '154.159.252.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-19 15:08:28', '2025-10-20 11:03:11', 0),
(32, 4, 'd7511fdd2404424e64ea55ac23684f2af4d953e36bed97a5e3cc152e324374ec', NULL, '154.159.252.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-19 15:19:44', '2025-10-20 11:15:22', 0),
(33, 4, 'a1674ec1bf4892c1f92abda84f7b15e929fcad6ae782da39a5e10ba5e053a08d', NULL, '154.159.252.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-19 15:23:27', '2025-10-20 11:15:11', 0),
(34, 4, 'e25924fda2436b893ac6e740e3704988fc7452f1412aae0c1b4869580457e3d6', NULL, '154.159.252.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-19 15:28:47', '2025-10-20 11:15:02', 0),
(35, 4, '3l5dtb7sscr7fncgdilk81gnpf', NULL, '154.159.252.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-19 15:30:31', '2025-10-19 15:31:19', 0),
(36, 4, '45t1jkq10rcgaak8m9fifv0knj', NULL, '154.159.252.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-19 15:31:39', '2025-10-19 15:58:54', 0),
(37, 5, '5a68bf48a4ee8357827e2e9b907afad5b1b3d396ff85ee4ebf25de6e03cce9a9', NULL, '154.159.252.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-19 15:59:11', '2025-10-19 16:00:40', 0),
(38, 4, 'kpp63ue1tmm5k39pe7b409145v', NULL, '154.159.252.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-19 16:01:00', '2025-10-20 11:14:55', 0),
(39, 4, '896ceeb93b0dc3b56f85fb79dcb2b0b9eecfab786a5111e25df584100387d3f6', NULL, '154.159.252.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-19 16:02:00', '2025-10-20 11:14:49', 0),
(40, 5, '6c3fb666d1fc58118508c9199d1416edf58b234a78d61a63c9fa1b6cd0838c95', NULL, '154.159.252.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-19 16:02:52', '2025-10-19 16:02:57', 0),
(41, 1, '797bc6fe045cdc2f41c440518cd6a4b52e559aef5934cba931a40629892f468a', NULL, '154.159.252.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-19 16:03:33', '2025-10-19 16:03:38', 0),
(42, 4, 'b68fa02e6155b20745a1bdef1349eb3c856bd2a4335c981643176965bbf00296', NULL, '154.159.252.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-19 16:03:53', '2025-10-20 11:14:44', 0),
(43, 4, '7a9db05367f20de3ff984cc2fe8ddff135e66447004d750265789705f1dd7b31', NULL, '154.159.252.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-20 11:03:27', '2025-10-20 11:14:36', 0),
(44, 4, '84a8354e9ece4012e0c9203764f63b614e049bd104f7bebbafa8e37cd7d4c514', NULL, '154.159.252.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-20 11:07:22', '2025-10-20 11:14:19', 0),
(45, 4, '8f5ki07ooqd1vito3gbcepmrko', '75b47be3dba13560de900e781d42b6516fbad0d59055b78804d7eee7d467f3cd', '154.159.252.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-20 11:12:46', '2025-10-20 11:13:07', 0),
(46, 1, 'ogcr3qtverd7tmhnk7705rv2o6', 'ea8e22acdded242c9c5468880801fd8455f5f624a5d14b11f251a41cd7033939', '154.159.252.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-20 11:13:24', '2025-10-20 11:13:24', 1),
(47, 4, '9qlgtm54kflajkqibbnj02jhok', 'f086c843b202fb4a56ee795e22633642e4a6a74ca7ff93cb505b42f820928daf', '154.159.252.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-20 11:17:57', '2025-10-23 09:43:30', 0),
(48, 4, 'ha5t1fmmiplf6dsq243g4btmuf', 'e0b67971974d930aa4047f9e88fed05a85ea49ee623d9e349380fdd2e7de8363', '154.159.252.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-20 11:23:52', '2025-10-20 11:24:36', 0),
(49, 5, '4bl150c9eo7kt0po1fop5epcvm', '3cc08afce621d596844beca0f8fd663e4398baedc13e3d64667710d0ea3feed6', '154.159.252.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-20 11:25:02', '2025-10-21 04:44:08', 1),
(50, 4, 'fp86jev9visd519us8pegcr5tr', NULL, '196.250.215.135', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-20 11:29:36', '2025-10-20 11:29:36', 1),
(51, 4, 'f9rl1l8ehnngd5p0gulouqgtvr', NULL, '196.250.215.135', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-20 11:39:00', '2025-10-20 11:39:00', 1),
(52, 5, '36e2vv30m2d8p6sq8v2qtga9v2', 'ad9f2f6922ae959f309d68bb04cab623b4dc96606d7c0c38b68f71f59fbec9c1', '154.159.252.235', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-21 08:04:22', '2025-10-21 08:04:22', 1),
(53, 5, '1jug0e56le27fpujn3tcokgpfm', NULL, '154.159.252.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-22 05:42:15', '2025-10-22 05:42:15', 1),
(54, 4, 'oq8qhk6at5ra57p9upk3sa9kkj', NULL, '196.250.215.210', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-22 15:24:08', '2025-10-22 15:24:59', 0),
(55, 4, 'r1ifog8eh44ji0pmqcb4h175l1', NULL, '196.250.215.210', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-22 15:25:27', '2025-10-22 15:25:33', 0),
(56, 1, 'lnti7o8buk36c6m37jiiiub210', NULL, '196.250.215.210', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-22 15:26:19', '2025-10-22 15:27:23', 0),
(57, 5, 'm90d155lc72hbpk7d8edtqgfeh', NULL, '196.250.215.210', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-22 15:27:47', '2025-10-22 15:35:29', 0),
(58, 5, 'd0bo014amqmg7udfcva3058fv5', NULL, '154.159.252.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-22 18:12:42', '2025-10-22 18:12:42', 1),
(59, 1, 'n90h7studsula67ejm9oih3i2e', '9dc9bf30876553cdb5fc6d995813dc8e6fe02e870d7d1a2d20736896095d01a4', '197.232.62.225', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-23 09:43:41', '2025-10-23 09:43:41', 1),
(60, 1, 'ujgpi22e55ndv7t69f0jmru437', '8dca16282e993b3ae06679f9ecb9d90a198ff1adc2a3305127b9223dea935209', '197.232.62.225', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-23 09:43:44', '2025-10-23 09:50:42', 0),
(61, 4, 'qg7vmei74i5goavad9illekcpl', '79eb7d8efd8812f9bd36dda23813b8b95de45bd478c335885d4638cd7ce1576c', '197.232.62.225', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-23 09:51:05', '2025-10-30 19:17:51', 1),
(62, 1, 'v5plp77f456s5pabmj4okm0td4', 'a63b63688bc7777128eff19843ca6a6502657140dba1594afff30f64fc7d71c6', '197.232.62.225', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-23 09:57:21', '2025-10-23 14:08:24', 1),
(63, 1, '3ut89o7i3ka3sf3f7gbpjshced', '29bffc87759c920b21ef8fc0307d56ffc114f00088093ca91c5727d5dc3f6b8d', '154.159.252.15', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 12:04:44', '2025-10-29 12:04:44', 1);

-- --------------------------------------------------------

--
-- Table structure for table `page_views`
--

CREATE TABLE `page_views` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Link to product if it is a product page view',
  `url` varchar(255) NOT NULL,
  `visitor_identifier` varchar(64) NOT NULL COMMENT 'Hashed IP or a unique session token',
  `view_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `page_views`
--

INSERT INTO `page_views` (`id`, `product_id`, `url`, `visitor_identifier`, `view_timestamp`) VALUES
(52, 13, 'https://motorfix.co.ke/product?id=13', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 16:50:15'),
(53, 13, 'https://motorfix.co.ke/product?id=13', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 16:50:22'),
(54, 13, 'https://motorfix.co.ke/product?id=13', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 16:56:30'),
(55, 13, 'https://motorfix.co.ke/product?id=13', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 16:56:50'),
(56, 13, 'https://motorfix.co.ke/product?id=13', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 16:57:17'),
(57, 27, 'https://motorfix.co.ke/product?id=27', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 17:16:22'),
(58, 27, 'https://motorfix.co.ke/product?id=27', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 17:19:51'),
(59, 34, 'https://motorfix.co.ke/product?id=34', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 17:20:08'),
(60, 34, 'https://motorfix.co.ke/product?id=34', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 17:22:49'),
(61, 30, 'https://motorfix.co.ke/product?id=30', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 17:25:00'),
(62, 30, 'https://motorfix.co.ke/product?id=30', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 17:27:00'),
(63, 18, 'https://motorfix.co.ke/product?id=18', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 17:31:15'),
(64, 13, 'https://motorfix.co.ke/product?id=13', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 17:47:06'),
(65, 13, 'https://motorfix.co.ke/product?id=13', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 17:49:37'),
(66, 13, 'https://motorfix.co.ke/product?id=13', '37252070f768e1ec0f90c9e577fdf29f2bb62e90f7521248175fcd2ec956bbac', '2025-10-27 17:49:40'),
(67, 13, 'https://motorfix.co.ke/product?id=13', 'c4a1cd2a152ca4a2823656cff29a237fad9a305626e8ab70871261075a7d02d0', '2025-10-27 17:49:41'),
(68, 13, 'https://motorfix.co.ke/product?id=13', 'c4a1cd2a152ca4a2823656cff29a237fad9a305626e8ab70871261075a7d02d0', '2025-10-27 17:49:41'),
(69, 13, 'https://motorfix.co.ke/product?id=13', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 17:50:51'),
(70, 13, 'https://motorfix.co.ke/product?id=13', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 17:51:21'),
(71, 13, 'https://motorfix.co.ke/product?id=13', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 17:51:45'),
(72, 13, 'https://motorfix.co.ke/product?id=13', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 17:56:33'),
(73, 13, 'https://motorfix.co.ke/product?id=13', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 17:57:02'),
(74, 13, 'https://motorfix.co.ke/product?id=13', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 18:06:24'),
(75, 13, 'https://motorfix.co.ke/product?id=13', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 18:06:36'),
(76, 13, 'https://motorfix.co.ke/product?id=13', '01bdc758a70a669f9f7469616f7107b24b8bbe0d69076db44bd36c4f3948d33a', '2025-10-27 18:21:24'),
(77, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 11:53:02'),
(78, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:00:33'),
(79, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:01:07'),
(80, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:02:29'),
(81, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:02:57'),
(82, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:03:36'),
(83, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:04:06'),
(84, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:11:32'),
(85, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:11:44'),
(86, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:13:54'),
(87, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:13:58'),
(88, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:18:39'),
(89, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:19:43'),
(90, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:19:56'),
(91, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:20:09'),
(92, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:27:42'),
(93, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:27:48'),
(94, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:32:50'),
(95, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:36:24'),
(96, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:39:48'),
(97, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:40:40'),
(98, 13, 'https://motorfix.co.ke/product?id=13', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:40:57'),
(99, 34, 'https://motorfix.co.ke/product?id=34', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:41:03'),
(100, 30, 'https://motorfix.co.ke/product?id=30', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 12:41:06'),
(101, 30, 'https://motorfix.co.ke/product?id=30', '187890971d3cdd4c4cf298135261687e1b8fe02f96befa1e4e0aba0b27c91115', '2025-10-29 12:41:08'),
(102, 30, 'https://motorfix.co.ke/product?id=30', '187890971d3cdd4c4cf298135261687e1b8fe02f96befa1e4e0aba0b27c91115', '2025-10-29 12:41:09'),
(103, 30, 'https://motorfix.co.ke/product?id=30', '37252070f768e1ec0f90c9e577fdf29f2bb62e90f7521248175fcd2ec956bbac', '2025-10-29 12:41:09'),
(104, 25, 'https://motorfix.co.ke/product?id=25', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 14:25:01'),
(105, 26, 'https://motorfix.co.ke/product?id=26', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 14:26:14'),
(106, 30, 'https://motorfix.co.ke/product?id=30', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 14:33:03'),
(107, 30, 'https://motorfix.co.ke/product?id=30', '60162be5f4fd17104b99932427d05b299c29f0fea254b9a0c6e3320c098a5e06', '2025-10-29 14:37:34'),
(108, 33, 'https://motorfix.co.ke/product?id=33', '46579e810a29fc6b0b068e4a90cb75303fda3379a474fb13632ba8d6becdc2d3', '2025-10-29 17:14:18'),
(109, 33, 'https://motorfix.co.ke/product?id=33', '0d09019d2fbf3a13af5f6c2c32f76c68b08821b0428ae7c17cdc79a7848cf2b4', '2025-10-29 17:14:23'),
(110, 33, 'https://motorfix.co.ke/product?id=33', '121d132571f2c2a4394c2a5c6d74867475eed39fd22edfe42966b496c01afb32', '2025-10-29 17:14:24'),
(111, 33, 'https://motorfix.co.ke/product?id=33', '1ba8a68ca70a6867ef58fcba9c00035101c97f2464c6384e5fa46f64605b759b', '2025-10-29 17:14:27'),
(112, 33, 'https://motorfix.co.ke/product?id=33', '1ff0a7b897be2159170f337915cdd44e6eff04670dd4c43ec056cdb2267c9691', '2025-10-29 17:17:54'),
(113, 30, 'https://motorfix.co.ke/product?id=30', '46579e810a29fc6b0b068e4a90cb75303fda3379a474fb13632ba8d6becdc2d3', '2025-10-29 17:28:59'),
(114, 30, 'https://motorfix.co.ke/product?id=30', '46579e810a29fc6b0b068e4a90cb75303fda3379a474fb13632ba8d6becdc2d3', '2025-10-29 17:36:21'),
(115, 34, 'https://motorfix.co.ke/product?id=34', '1ff0a7b897be2159170f337915cdd44e6eff04670dd4c43ec056cdb2267c9691', '2025-10-29 20:19:00'),
(116, 30, 'https://motorfix.co.ke/product?id=30', '1ff0a7b897be2159170f337915cdd44e6eff04670dd4c43ec056cdb2267c9691', '2025-10-29 20:20:19'),
(117, 19, 'https://motorfix.co.ke/product?id=19', '46579e810a29fc6b0b068e4a90cb75303fda3379a474fb13632ba8d6becdc2d3', '2025-10-29 20:56:25'),
(118, 19, 'https://motorfix.co.ke/product?id=19', '187890971d3cdd4c4cf298135261687e1b8fe02f96befa1e4e0aba0b27c91115', '2025-10-29 20:56:34'),
(119, 19, 'https://motorfix.co.ke/product?id=19', '87722a79c8e80e88ca9962e8ac9591e5279d9ecd2f06d944252b291c354276d8', '2025-10-29 20:56:34'),
(120, 19, 'https://motorfix.co.ke/product?id=19', '7222bda0ed902c3ba14a6bd8e38f416578d6737dc3cc2303f6223fe5f24baaa1', '2025-10-29 20:56:35'),
(121, 30, 'https://motorfix.co.ke/product?id=30', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 04:48:30'),
(122, 34, 'https://motorfix.co.ke/product?id=34', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 04:50:49'),
(123, 34, 'https://motorfix.co.ke/product?id=34', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 04:50:54'),
(124, 34, 'https://motorfix.co.ke/product?id=34', 'fc1bcc754e77ab4ab7f0cf1c5068a649fced5dc4534a763ee2244f54f7e3d728', '2025-10-30 04:50:57'),
(125, 34, 'https://motorfix.co.ke/product?id=34', '0d09019d2fbf3a13af5f6c2c32f76c68b08821b0428ae7c17cdc79a7848cf2b4', '2025-10-30 04:50:57'),
(126, 34, 'https://motorfix.co.ke/product?id=34', '187890971d3cdd4c4cf298135261687e1b8fe02f96befa1e4e0aba0b27c91115', '2025-10-30 04:50:57'),
(127, 34, 'https://motorfix.co.ke/product?id=34', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 04:50:57'),
(128, 34, 'https://motorfix.co.ke/product?id=34', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 04:51:00'),
(129, 34, 'https://motorfix.co.ke/product?id=34', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 04:51:02'),
(130, 28, 'https://motorfix.co.ke/product?id=28', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 04:51:11'),
(131, 28, 'https://motorfix.co.ke/product?id=28', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 04:51:21'),
(132, 28, 'https://motorfix.co.ke/product?id=28', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 04:51:25'),
(133, 28, 'https://motorfix.co.ke/product?id=28', 'fc1bcc754e77ab4ab7f0cf1c5068a649fced5dc4534a763ee2244f54f7e3d728', '2025-10-30 04:51:27'),
(134, 28, 'https://motorfix.co.ke/product?id=28', '187890971d3cdd4c4cf298135261687e1b8fe02f96befa1e4e0aba0b27c91115', '2025-10-30 04:51:27'),
(135, 28, 'https://motorfix.co.ke/product?id=28', '0d09019d2fbf3a13af5f6c2c32f76c68b08821b0428ae7c17cdc79a7848cf2b4', '2025-10-30 04:51:27'),
(136, 28, 'https://motorfix.co.ke/product?id=28', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 04:51:28'),
(137, 28, 'https://motorfix.co.ke/product?id=28', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 04:51:31'),
(138, 28, 'https://motorfix.co.ke/product?id=28', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 04:51:33'),
(139, 28, 'https://motorfix.co.ke/product?id=28', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 04:51:36'),
(140, 28, 'https://motorfix.co.ke/product?id=28', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 04:51:38'),
(141, 28, 'https://motorfix.co.ke/product?id=28', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 04:51:48'),
(142, 34, 'https://motorfix.co.ke/product?id=34', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 05:08:50'),
(143, 34, 'https://motorfix.co.ke/product?id=34', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 05:09:55'),
(144, 34, 'https://motorfix.co.ke/product?id=34', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 05:10:00'),
(145, 34, 'https://motorfix.co.ke/product?id=34', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 05:12:59'),
(146, 34, 'https://motorfix.co.ke/product?id=34', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 05:14:19'),
(147, 34, 'https://motorfix.co.ke/product?id=34', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 05:24:52'),
(148, 34, 'https://motorfix.co.ke/product?id=34', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 05:27:52'),
(149, 34, 'https://motorfix.co.ke/product?id=34', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 05:29:26'),
(150, 34, 'https://motorfix.co.ke/product?id=34', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 05:34:14'),
(151, 34, 'https://motorfix.co.ke/product?id=34', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 05:38:39'),
(152, 34, 'https://motorfix.co.ke/product?id=34', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 05:39:23'),
(153, 34, 'https://motorfix.co.ke/product?id=34', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 05:40:09'),
(154, 33, 'https://motorfix.co.ke/product?id=33', '3aa1157e928a49069959bfb7ddd72648dfa31be64a1b28f8949d878fcc563fb0', '2025-10-30 05:43:06'),
(155, 28, 'https://motorfix.co.ke/product?id=28', 'db37037a962c8c61f9f08c575eae65e92778f35159a556e328842b8ffba16715', '2025-10-30 08:10:31'),
(156, 30, 'https://motorfix.co.ke/product?id=30', '918f804eacaccef908748e1b6dc1699e77fcfa9ac321d6d0702d66152410d838', '2025-10-30 08:21:30'),
(157, 28, 'https://motorfix.co.ke/product?id=28', 'be7f4aad747fd5a3b84e28ab5ca0aacea417e753781a68435d40fa730bd78e01', '2025-10-30 09:51:57'),
(158, 14, 'https://motorfix.co.ke/product?id=14', 'ef9feda1d26a61b5cf365707d61a24171b11ffd6b2cdb1eac4495642bdb7d51f', '2025-10-30 12:46:50'),
(159, 30, 'https://motorfix.co.ke/product?id=30', '6d649930bd1f1510c2df2e915898427fde8f1dce821336b568a7c14b4e784696', '2025-10-30 12:46:58'),
(160, 19, 'https://motorfix.co.ke/product?id=19', '4518d75f68f34b497a207a209ab8639bf93696e8e2296393eb3f657ea01cad1a', '2025-10-30 12:49:05'),
(161, 29, 'https://motorfix.co.ke/product?id=29', '5ea97e83df44685e3b9778750194554c394a2b1027eb63e8ebb25e8f00293231', '2025-10-30 12:49:17'),
(162, 32, 'https://motorfix.co.ke/product?id=32', 'a38832616ed01bf9f2c859387038d18016ff7346a84e0894812c8aca3e9c8334', '2025-10-30 12:50:52'),
(163, 27, 'https://motorfix.co.ke/product?id=27', '5ea97e83df44685e3b9778750194554c394a2b1027eb63e8ebb25e8f00293231', '2025-10-30 12:52:15'),
(164, 33, 'https://motorfix.co.ke/product?id=33', 'cbf4de814a992b5e56ec03dd8dc2d615d0d85332d6bcbea06f31e74e04f81d12', '2025-10-30 12:52:41'),
(165, 26, 'https://motorfix.co.ke/product?id=26', '82c0d2ecba20568b43b2db11237d8ccc7fa65cc977410ab81557445f6bfcc6d5', '2025-10-30 12:52:55'),
(166, 31, 'https://motorfix.co.ke/product?id=31', '6d5d12359c2ba4bb7766e651ea88c9680c58f4ce2c9bcaa6ff7e02f03c4fafc3', '2025-10-30 12:54:46'),
(167, 35, 'https://motorfix.co.ke/product?id=35', '935a412cf24b934a2835b59a64d787e0af454ffc475fdbfc2321ae0c4bfd5c1e', '2025-10-30 12:55:22'),
(168, 28, 'https://motorfix.co.ke/product?id=28', 'c00148583d1eb73d84b397942ba13e9c0fd6cded13b4297c5e761d607fdf293c', '2025-10-30 12:57:49'),
(169, 34, 'https://motorfix.co.ke/product?id=34', '71ff0c5e39aa8c150178298f992182aa7e537bb00cc9138fd9df91408cc29643', '2025-10-30 12:58:17'),
(170, 33, 'https://motorfix.co.ke/product?id=33', '02901161edd7bdf3776b6ebe0c1ea17ef6e1477e57cceebdf47736bce1c751d9', '2025-10-30 16:56:55'),
(171, 34, 'https://motorfix.co.ke/product?id=34', '02901161edd7bdf3776b6ebe0c1ea17ef6e1477e57cceebdf47736bce1c751d9', '2025-10-30 18:47:40'),
(172, 19, 'https://motorfix.co.ke/product?id=19', 'ec8abc97d548af782de28fd48409d99afd0884a56aea7c492b7bb7aa8d9431b8', '2025-10-30 19:18:16'),
(173, 36, 'https://motorfix.co.ke/product?id=36', 'ec8abc97d548af782de28fd48409d99afd0884a56aea7c492b7bb7aa8d9431b8', '2025-10-30 19:20:01'),
(174, 19, 'https://motorfix.co.ke/product?id=19', '02901161edd7bdf3776b6ebe0c1ea17ef6e1477e57cceebdf47736bce1c751d9', '2025-10-30 19:44:52'),
(175, 19, 'https://motorfix.co.ke/product?id=19', '02901161edd7bdf3776b6ebe0c1ea17ef6e1477e57cceebdf47736bce1c751d9', '2025-10-30 19:45:03'),
(176, 28, 'https://motorfix.co.ke/product?id=28', '02901161edd7bdf3776b6ebe0c1ea17ef6e1477e57cceebdf47736bce1c751d9', '2025-10-30 19:45:26'),
(177, 28, 'https://motorfix.co.ke/product?id=28', '02901161edd7bdf3776b6ebe0c1ea17ef6e1477e57cceebdf47736bce1c751d9', '2025-10-30 19:46:53'),
(178, 31, 'https://motorfix.co.ke/product?id=31', '02901161edd7bdf3776b6ebe0c1ea17ef6e1477e57cceebdf47736bce1c751d9', '2025-10-30 19:47:22'),
(179, 31, 'https://motorfix.co.ke/product?id=31', '02901161edd7bdf3776b6ebe0c1ea17ef6e1477e57cceebdf47736bce1c751d9', '2025-10-30 19:50:00'),
(180, 34, 'https://motorfix.co.ke/product?id=34', '02901161edd7bdf3776b6ebe0c1ea17ef6e1477e57cceebdf47736bce1c751d9', '2025-10-30 19:52:20'),
(181, 32, 'https://motorfix.co.ke/product?id=32', '02901161edd7bdf3776b6ebe0c1ea17ef6e1477e57cceebdf47736bce1c751d9', '2025-10-30 19:54:26'),
(182, 32, 'https://motorfix.co.ke/product?id=32', '187890971d3cdd4c4cf298135261687e1b8fe02f96befa1e4e0aba0b27c91115', '2025-10-30 19:54:31'),
(183, 32, 'https://motorfix.co.ke/product?id=32', '3c9aa7386d15231b856607ec0ceb1878e23a818a25cbf7f491a6737a78210eb2', '2025-10-30 19:54:32'),
(184, 32, 'https://motorfix.co.ke/product?id=32', '3c9aa7386d15231b856607ec0ceb1878e23a818a25cbf7f491a6737a78210eb2', '2025-10-30 19:54:32'),
(185, 36, 'https://motorfix.co.ke/product?id=36', '02901161edd7bdf3776b6ebe0c1ea17ef6e1477e57cceebdf47736bce1c751d9', '2025-10-30 19:55:01'),
(186, 36, 'https://motorfix.co.ke/product?id=36', '02901161edd7bdf3776b6ebe0c1ea17ef6e1477e57cceebdf47736bce1c751d9', '2025-10-30 19:55:12'),
(187, 35, 'https://motorfix.co.ke/product?id=35', '02901161edd7bdf3776b6ebe0c1ea17ef6e1477e57cceebdf47736bce1c751d9', '2025-10-30 19:55:17'),
(188, 36, 'https://motorfix.co.ke/product?id=36', '02901161edd7bdf3776b6ebe0c1ea17ef6e1477e57cceebdf47736bce1c751d9', '2025-10-30 19:55:39'),
(189, 36, 'https://motorfix.co.ke/product?id=36', '02e7dc9322b7d5ed2424cb1f187bdc9d279962674b1b98ef43b321f65c8dc51f', '2025-10-30 21:08:11'),
(190, 36, 'https://motorfix.co.ke/product?id=36', '187890971d3cdd4c4cf298135261687e1b8fe02f96befa1e4e0aba0b27c91115', '2025-10-30 21:08:16'),
(191, 36, 'https://motorfix.co.ke/product?id=36', '3c9aa7386d15231b856607ec0ceb1878e23a818a25cbf7f491a6737a78210eb2', '2025-10-30 21:08:17'),
(192, 36, 'https://motorfix.co.ke/product?id=36', '37252070f768e1ec0f90c9e577fdf29f2bb62e90f7521248175fcd2ec956bbac', '2025-10-30 21:08:17'),
(193, 35, 'https://motorfix.co.ke/product?id=35', '02e7dc9322b7d5ed2424cb1f187bdc9d279962674b1b98ef43b321f65c8dc51f', '2025-10-30 21:08:47'),
(194, 35, 'https://motorfix.co.ke/product?id=35', '02e7dc9322b7d5ed2424cb1f187bdc9d279962674b1b98ef43b321f65c8dc51f', '2025-10-30 21:08:50'),
(195, 34, 'https://motorfix.co.ke/product?id=34', '02e7dc9322b7d5ed2424cb1f187bdc9d279962674b1b98ef43b321f65c8dc51f', '2025-10-30 21:09:12'),
(196, 36, 'https://motorfix.co.ke/product?id=36', '02e7dc9322b7d5ed2424cb1f187bdc9d279962674b1b98ef43b321f65c8dc51f', '2025-10-30 21:09:16'),
(197, 36, 'https://motorfix.co.ke/product?id=36', '02e7dc9322b7d5ed2424cb1f187bdc9d279962674b1b98ef43b321f65c8dc51f', '2025-10-30 21:09:26'),
(198, 36, 'https://motorfix.co.ke/product?id=36', 'ec8abc97d548af782de28fd48409d99afd0884a56aea7c492b7bb7aa8d9431b8', '2025-10-30 21:26:04'),
(199, 33, 'https://motorfix.co.ke/product?id=33', '5b312f933123e0efc52eecee123cad7644efc04544170955498099d66cfe4797', '2025-11-02 13:56:26'),
(200, 33, 'https://motorfix.co.ke/product?id=33', '187890971d3cdd4c4cf298135261687e1b8fe02f96befa1e4e0aba0b27c91115', '2025-11-02 13:56:28'),
(201, 33, 'https://motorfix.co.ke/product?id=33', '7222bda0ed902c3ba14a6bd8e38f416578d6737dc3cc2303f6223fe5f24baaa1', '2025-11-02 13:56:29'),
(202, 33, 'https://motorfix.co.ke/product?id=33', '7222bda0ed902c3ba14a6bd8e38f416578d6737dc3cc2303f6223fe5f24baaa1', '2025-11-02 13:56:29'),
(203, 34, 'https://motorfix.co.ke/product?id=34', 'c59493fbf7985bd79b5eccb6694798b62f5c4122a200689a266111fc70d8c02c', '2025-11-03 13:42:14'),
(204, 28, 'https://motorfix.co.ke/product?id=28', '49f17bc34f31ee5250380ad8a7f11c8a572078bdf7c080d9125358ee8865fd46', '2025-11-05 04:35:43'),
(205, 30, 'https://motorfix.co.ke/product?id=30', 'ce96ed6e1eaf81ea54ebef78d8e878ae216c86b3f7511864eb8e327ff3196ad1', '2025-11-05 04:48:38'),
(206, 32, 'https://motorfix.co.ke/product?id=32', 'ac07c1427c3346b0da13293a7de6dbc7ac02a5716630c92285164107c8340539', '2025-11-05 05:24:04'),
(207, 34, 'https://motorfix.co.ke/product?id=34', '9f0cd97f49f5754a383fb502f2c8fb53d3b317ab90c3c7b2d8ba8c9ccb1a99d3', '2025-11-05 05:38:01'),
(208, 26, 'https://motorfix.co.ke/product?id=26', '3ec37308ab8bf118d094356d2f01580d64428b91883ecc418d8868289d382a43', '2025-11-05 06:09:59'),
(209, 27, 'https://motorfix.co.ke/product?id=27', '34d71971958031eb38d18ca7daa6392143b703a752c559ab1b58265fee9b8af0', '2025-11-05 06:51:06'),
(210, 29, 'https://motorfix.co.ke/product?id=29', '3ec37308ab8bf118d094356d2f01580d64428b91883ecc418d8868289d382a43', '2025-11-05 07:05:47'),
(211, 35, 'https://motorfix.co.ke/product?id=35', '3183ef51550f466f088fd9d104c954b0149138f5ea504983a6b5b52e527b9e5c', '2025-11-05 08:17:32'),
(212, 33, 'https://motorfix.co.ke/product?id=33', '8fa37b1b6acfcdf42405688e4d49b64e4b67b2e6ca63f0761110bc051f5d3d3e', '2025-11-05 09:12:09'),
(213, 19, 'https://motorfix.co.ke/product?id=19', '97f12d638f1bec0e6d94e985901ee8c60640445da6e6f332a0cf9638627e7c0e', '2025-11-05 09:15:04'),
(214, 34, 'https://motorfix.co.ke/product?id=34', '3a282c9174943590261a6a713a121d894a694572ec571ec66abecf8a63a2a725', '2025-11-05 18:37:28'),
(215, 26, 'https://motorfix.co.ke/product?id=26', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-11-09 12:07:10'),
(216, 19, 'https://motorfix.co.ke/product?id=19', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-11-09 12:07:10'),
(217, 28, 'https://motorfix.co.ke/product?id=28', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-11-09 12:07:11'),
(218, 27, 'https://motorfix.co.ke/product?id=27', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-11-09 12:07:11'),
(219, 29, 'https://motorfix.co.ke/product?id=29', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-11-09 12:07:11'),
(220, 31, 'https://motorfix.co.ke/product?id=31', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-11-09 12:07:12'),
(221, 32, 'https://motorfix.co.ke/product?id=32', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-11-09 12:07:12'),
(222, 30, 'https://motorfix.co.ke/product?id=30', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-11-09 12:07:12'),
(223, 34, 'https://motorfix.co.ke/product?id=34', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-11-09 12:07:12'),
(224, 30, 'https://motorfix.co.ke/product?id=30', '0ab2e97e39fdc5138082d4a42f9bcb49c471b9aeb6a959b635266092cf4508d0', '2025-11-10 01:44:41'),
(225, 28, 'https://motorfix.co.ke/product?id=28', '2511f811bb54665bdc7dcbae1d6e414b72ce7e6cccb1829319fc488474eefa19', '2025-11-11 15:14:39'),
(226, 27, 'https://motorfix.co.ke/product?id=27', '2511f811bb54665bdc7dcbae1d6e414b72ce7e6cccb1829319fc488474eefa19', '2025-11-11 15:14:40'),
(227, 33, 'https://motorfix.co.ke/product?id=33', '2511f811bb54665bdc7dcbae1d6e414b72ce7e6cccb1829319fc488474eefa19', '2025-11-11 15:14:41'),
(228, 34, 'https://motorfix.co.ke/product?id=34', 'b3178d87030409977dbefd6f4ddd79e6a7b876471f5578fc3067d7e8d173a1ac', '2025-11-12 04:35:42'),
(229, 19, 'https://motorfix.co.ke/product?id=19', 'b41fa6e7461551d2b7fb9d3706884cdcedd3a1c79e74b8813b7c791dfe91289d', '2025-11-13 05:32:12'),
(230, 35, 'https://motorfix.co.ke/product?id=35', '438f0778a9d28b1785e42477a391d19a8657d089794e71cfffb985c46e460d77', '2025-11-13 07:13:43'),
(231, 33, 'https://motorfix.co.ke/product?id=33', 'de61a0e2cb845125ef3f4162dd34b4496c9f283235cc1bea103bb253880d1f32', '2025-11-14 09:53:31'),
(232, 28, 'https://motorfix.co.ke/product?id=28', 'de61a0e2cb845125ef3f4162dd34b4496c9f283235cc1bea103bb253880d1f32', '2025-11-14 09:53:33'),
(233, 28, 'https://motorfix.co.ke/product?id=28', 'de61a0e2cb845125ef3f4162dd34b4496c9f283235cc1bea103bb253880d1f32', '2025-11-14 09:53:35'),
(234, 28, 'https://motorfix.co.ke/product?id=28', 'de61a0e2cb845125ef3f4162dd34b4496c9f283235cc1bea103bb253880d1f32', '2025-11-14 09:53:36'),
(235, 28, 'https://motorfix.co.ke/product?id=28', 'de61a0e2cb845125ef3f4162dd34b4496c9f283235cc1bea103bb253880d1f32', '2025-11-14 09:53:38'),
(236, 28, 'https://motorfix.co.ke/product?id=28', 'de61a0e2cb845125ef3f4162dd34b4496c9f283235cc1bea103bb253880d1f32', '2025-11-14 09:53:39'),
(237, 28, 'https://motorfix.co.ke/product?id=28', 'de61a0e2cb845125ef3f4162dd34b4496c9f283235cc1bea103bb253880d1f32', '2025-11-14 09:53:40'),
(238, 28, 'https://motorfix.co.ke/product?id=28', 'de61a0e2cb845125ef3f4162dd34b4496c9f283235cc1bea103bb253880d1f32', '2025-11-14 09:53:44'),
(239, 28, 'https://motorfix.co.ke/product?id=28', 'de61a0e2cb845125ef3f4162dd34b4496c9f283235cc1bea103bb253880d1f32', '2025-11-14 09:53:45'),
(240, 28, 'https://motorfix.co.ke/product?id=28', 'de61a0e2cb845125ef3f4162dd34b4496c9f283235cc1bea103bb253880d1f32', '2025-11-14 09:53:46'),
(241, 28, 'https://motorfix.co.ke/product?id=28', 'de61a0e2cb845125ef3f4162dd34b4496c9f283235cc1bea103bb253880d1f32', '2025-11-14 09:53:50'),
(242, 31, 'https://www.motorfix.co.ke/product?id=31', '5e1fdba40d4e2d80489396eb498367b35327e7d8f84e94c3b29cf4318ce1851a', '2025-11-14 16:12:03'),
(243, 36, 'https://www.motorfix.co.ke/product?id=36', '5e1fdba40d4e2d80489396eb498367b35327e7d8f84e94c3b29cf4318ce1851a', '2025-11-14 16:12:07'),
(244, 35, 'https://www.motorfix.co.ke/product?id=35', '5e1fdba40d4e2d80489396eb498367b35327e7d8f84e94c3b29cf4318ce1851a', '2025-11-14 16:12:08'),
(245, 34, 'https://www.motorfix.co.ke/product?id=34', '5e1fdba40d4e2d80489396eb498367b35327e7d8f84e94c3b29cf4318ce1851a', '2025-11-14 16:12:09'),
(246, 28, 'https://motorfix.co.ke/product?id=28', '640d4361c507452c4b422118f0b452ecb7e87948a8cfee5e34be1b47ef422786', '2025-11-15 02:53:32'),
(247, 34, 'https://motorfix.co.ke/product?id=34', 'b41fa6e7461551d2b7fb9d3706884cdcedd3a1c79e74b8813b7c791dfe91289d', '2025-11-15 20:09:30'),
(248, 19, 'https://motorfix.co.ke/product?id=19', 'e84dad68ff407790f0bcc01bd6c3a708d41ffc22cb000e739cd49215a12bcfa4', '2025-11-16 12:29:02'),
(249, 26, 'https://motorfix.co.ke/product?id=26', 'e84dad68ff407790f0bcc01bd6c3a708d41ffc22cb000e739cd49215a12bcfa4', '2025-11-16 12:29:02'),
(250, 27, 'https://motorfix.co.ke/product?id=27', 'e84dad68ff407790f0bcc01bd6c3a708d41ffc22cb000e739cd49215a12bcfa4', '2025-11-16 12:29:02'),
(251, 28, 'https://motorfix.co.ke/product?id=28', 'e84dad68ff407790f0bcc01bd6c3a708d41ffc22cb000e739cd49215a12bcfa4', '2025-11-16 12:29:02'),
(252, 29, 'https://motorfix.co.ke/product?id=29', 'e84dad68ff407790f0bcc01bd6c3a708d41ffc22cb000e739cd49215a12bcfa4', '2025-11-16 12:29:03'),
(253, 33, 'https://motorfix.co.ke/product?id=33', 'cb1358fb2d90c84d7ff236147063077bc480ab6e3da73c1f0e1caf45c06bc869', '2025-11-16 13:22:36'),
(254, 33, 'https://motorfix.co.ke/product?id=33', 'fc1bcc754e77ab4ab7f0cf1c5068a649fced5dc4534a763ee2244f54f7e3d728', '2025-11-16 13:22:40'),
(255, 33, 'https://motorfix.co.ke/product?id=33', '3c9aa7386d15231b856607ec0ceb1878e23a818a25cbf7f491a6737a78210eb2', '2025-11-16 13:22:40'),
(256, 33, 'https://motorfix.co.ke/product?id=33', '3c9aa7386d15231b856607ec0ceb1878e23a818a25cbf7f491a6737a78210eb2', '2025-11-16 13:22:40'),
(257, 32, 'https://motorfix.co.ke/product?id=32', 'cb1358fb2d90c84d7ff236147063077bc480ab6e3da73c1f0e1caf45c06bc869', '2025-11-17 09:13:36'),
(258, 33, 'https://motorfix.co.ke/product?id=33', 'cd93961f31c0d611103a7e36dc96d6959bc35b29eb975a918931f7e97c8d0570', '2025-11-18 03:51:41'),
(259, 26, 'https://motorfix.co.ke/product?id=26', '5f92dd23c3d97459106c0d637f348db1869d9bfd2392a0a854c526f7b325f6c5', '2025-11-18 08:49:55'),
(260, 19, 'https://motorfix.co.ke/product?id=19', '71d8af82bc434d34d270194c6610884f8f81dcad1b1669f7d5425361ec8fb5c4', '2025-11-18 14:04:59'),
(261, 29, 'https://motorfix.co.ke/product?id=29', '86eb39fb22152eee0437531f4fd5dc95484de5ceae793974f78e4c6b840cd8b0', '2025-11-18 18:23:59'),
(262, 28, 'https://motorfix.co.ke/product?id=28', 'e87c2494dcb0d173de4694755cf733837efff1af1b01d2b0170a6f569615b597', '2025-11-18 23:05:18'),
(263, 32, 'https://motorfix.co.ke/product?id=32', '04abc2c5fdec0f15bf148adf21ebd80bc1e634212d348dc2e101a4aa03cc5e6b', '2025-11-19 06:48:32'),
(264, 27, 'https://motorfix.co.ke/product?id=27', '9aa46fee9fd679cafd0c479e0cf034ca6039bebdea7f2c854d930b1f21b8080d', '2025-11-19 16:04:31'),
(265, 36, 'https://motorfix.co.ke/product?id=36', '18094a9c6ee9c4dee4a3a5425833455614ce4f5c990858dc533f31268d8d1e61', '2025-11-22 21:30:10'),
(266, 19, 'https://motorfix.co.ke/product?id=19', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-11-23 12:51:05'),
(267, 26, 'https://motorfix.co.ke/product?id=26', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-11-23 12:51:05'),
(268, 28, 'https://motorfix.co.ke/product?id=28', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-11-23 12:51:06'),
(269, 27, 'https://motorfix.co.ke/product?id=27', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-11-23 12:51:06'),
(270, 29, 'https://motorfix.co.ke/product?id=29', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-11-23 12:51:06'),
(271, 30, 'https://motorfix.co.ke/product?id=30', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-11-23 12:51:07'),
(272, 32, 'https://motorfix.co.ke/product?id=32', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-11-23 12:51:07'),
(273, 31, 'https://motorfix.co.ke/product?id=31', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-11-23 12:51:07'),
(274, 34, 'https://motorfix.co.ke/product?id=34', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-11-23 12:51:08'),
(275, 19, 'https://motorfix.co.ke/product?id=19', '8c0814f9cad3e604f8c3b0f66aaccfc74c591d7001d754d616f3b2163e075dd5', '2025-11-26 06:52:51'),
(276, 19, 'https://motorfix.co.ke/product?id=19%27', '8c0814f9cad3e604f8c3b0f66aaccfc74c591d7001d754d616f3b2163e075dd5', '2025-11-26 06:52:52'),
(277, 28, 'https://motorfix.co.ke/product?id=28', '6be0f824627edfecf0465d632add13a1383c7d4d8b0cd8dc3de1f193b3345091', '2025-11-26 15:46:49'),
(278, 33, 'https://motorfix.co.ke/product?id=33', '6be0f824627edfecf0465d632add13a1383c7d4d8b0cd8dc3de1f193b3345091', '2025-11-26 15:52:51'),
(279, 30, 'https://motorfix.co.ke/product?id=30', 'eebd5a3acfc5c42c242283fde8fd1ed610a7e24aa26b06cedb4a810617dbad5d', '2025-11-26 17:07:25'),
(280, 26, 'https://motorfix.co.ke/product?id=26', 'eebd5a3acfc5c42c242283fde8fd1ed610a7e24aa26b06cedb4a810617dbad5d', '2025-11-26 17:07:28'),
(281, 29, 'https://motorfix.co.ke/product?id=29', 'eebd5a3acfc5c42c242283fde8fd1ed610a7e24aa26b06cedb4a810617dbad5d', '2025-11-26 17:07:30'),
(282, 19, 'https://motorfix.co.ke/product?id=19', 'eebd5a3acfc5c42c242283fde8fd1ed610a7e24aa26b06cedb4a810617dbad5d', '2025-11-26 17:07:33'),
(283, 34, 'https://motorfix.co.ke/product?id=34', 'eebd5a3acfc5c42c242283fde8fd1ed610a7e24aa26b06cedb4a810617dbad5d', '2025-11-26 17:07:35'),
(284, 35, 'https://motorfix.co.ke/product?id=35', 'eebd5a3acfc5c42c242283fde8fd1ed610a7e24aa26b06cedb4a810617dbad5d', '2025-11-26 17:07:37'),
(285, 28, 'https://motorfix.co.ke/product?id=28', 'eebd5a3acfc5c42c242283fde8fd1ed610a7e24aa26b06cedb4a810617dbad5d', '2025-11-26 17:07:38'),
(286, 36, 'https://motorfix.co.ke/product?id=36', 'eebd5a3acfc5c42c242283fde8fd1ed610a7e24aa26b06cedb4a810617dbad5d', '2025-11-26 17:07:40'),
(287, 32, 'https://motorfix.co.ke/product?id=32', 'eebd5a3acfc5c42c242283fde8fd1ed610a7e24aa26b06cedb4a810617dbad5d', '2025-11-26 17:07:41'),
(288, 31, 'https://motorfix.co.ke/product?id=31', 'eebd5a3acfc5c42c242283fde8fd1ed610a7e24aa26b06cedb4a810617dbad5d', '2025-11-26 17:07:42'),
(289, 27, 'https://motorfix.co.ke/product?id=27', 'eebd5a3acfc5c42c242283fde8fd1ed610a7e24aa26b06cedb4a810617dbad5d', '2025-11-26 17:07:43'),
(290, 33, 'https://motorfix.co.ke/product?id=33', '6be0f824627edfecf0465d632add13a1383c7d4d8b0cd8dc3de1f193b3345091', '2025-11-26 17:45:30'),
(291, 36, 'https://motorfix.co.ke/product?id=36', '53f57b85f7bcafd2626be8871787cfb0aebc76de97b9450af0a5d76d6c61e6de', '2025-11-26 18:06:09'),
(292, 35, 'https://motorfix.co.ke/product?id=35', '53f57b85f7bcafd2626be8871787cfb0aebc76de97b9450af0a5d76d6c61e6de', '2025-11-26 18:06:09'),
(293, 34, 'https://motorfix.co.ke/product?id=34', '53f57b85f7bcafd2626be8871787cfb0aebc76de97b9450af0a5d76d6c61e6de', '2025-11-26 18:06:10'),
(294, 33, 'https://motorfix.co.ke/product?id=33', '53f57b85f7bcafd2626be8871787cfb0aebc76de97b9450af0a5d76d6c61e6de', '2025-11-26 18:06:10'),
(295, 32, 'https://motorfix.co.ke/product?id=32', '53f57b85f7bcafd2626be8871787cfb0aebc76de97b9450af0a5d76d6c61e6de', '2025-11-26 18:06:10'),
(296, 30, 'https://motorfix.co.ke/product?id=30', '53f57b85f7bcafd2626be8871787cfb0aebc76de97b9450af0a5d76d6c61e6de', '2025-11-26 18:06:32'),
(297, 31, 'https://motorfix.co.ke/product?id=31', '53f57b85f7bcafd2626be8871787cfb0aebc76de97b9450af0a5d76d6c61e6de', '2025-11-26 18:06:32'),
(298, 28, 'https://motorfix.co.ke/product?id=28', '53f57b85f7bcafd2626be8871787cfb0aebc76de97b9450af0a5d76d6c61e6de', '2025-11-26 18:06:32'),
(299, 29, 'https://motorfix.co.ke/product?id=29', '53f57b85f7bcafd2626be8871787cfb0aebc76de97b9450af0a5d76d6c61e6de', '2025-11-26 18:06:32'),
(300, 27, 'https://motorfix.co.ke/product?id=27', '53f57b85f7bcafd2626be8871787cfb0aebc76de97b9450af0a5d76d6c61e6de', '2025-11-26 18:06:32'),
(301, 26, 'https://motorfix.co.ke/product?id=26', '53f57b85f7bcafd2626be8871787cfb0aebc76de97b9450af0a5d76d6c61e6de', '2025-11-26 18:06:55'),
(302, 19, 'https://motorfix.co.ke/product?id=19', '53f57b85f7bcafd2626be8871787cfb0aebc76de97b9450af0a5d76d6c61e6de', '2025-11-26 18:06:55'),
(303, 14, 'https://motorfix.co.ke/product?id=14', 'eb6b376f9507e4dcfc6592954370d12a40f33d1f03b3788a0fb8ec52605516d9', '2025-11-27 09:01:28'),
(304, 26, 'https://www.motorfix.co.ke/product?id=26', '97f29077e5e062810146fb11ac2bfdfe42ad80da6eaf75b9e8a9b8224d3aedfe', '2025-11-30 13:08:42'),
(305, 36, 'https://motorfix.co.ke/product?id=36', '46b8959990dd17a189fef099e97f0576a9c71a7a3003480a23ecd553aed23bd9', '2025-11-30 15:31:51'),
(306, 31, 'https://motorfix.co.ke/product?id=31', '1de2a5a6f2ab0ec1fe775db479e8cc73c0c1558e5130584cb2f4dd19cab526b9', '2025-11-30 17:43:51'),
(307, 35, 'https://motorfix.co.ke/product?id=35', 'c8aaad654a2f0106ec8b5a0932286e27aa4255294422c0b52523ee0bced0fae0', '2025-11-30 19:31:23'),
(308, 34, 'https://motorfix.co.ke/product?id=34', '2dddcd40f0a670f44cdfdd1d83675562b19344aef20eb5a22ae48c96e769b5f6', '2025-11-30 21:23:57'),
(309, 27, 'https://motorfix.co.ke/product?id=27', '04df0e7ab46a9de36a0198a1552d0792de8881b368d0eb99d9e4ffb236d5f81c', '2025-11-30 22:22:30'),
(310, 26, 'https://motorfix.co.ke/product?id=26', '43dedbf582749637476aa56607fc2ae92aaac508c5068b4a57389ff431d0f350', '2025-12-01 00:01:42'),
(311, 30, 'https://motorfix.co.ke/product?id=30', '31a6b97728139400b7bfcd57c6e097a594abaab85205b72c45007c7e625bb1ae', '2025-12-01 01:39:31'),
(312, 28, 'https://motorfix.co.ke/product?id=28', '3d928431d16350e0e5e9c5b67634eeb51cb877280e9a9cfae718617b6a014864', '2025-12-01 01:41:56'),
(313, 19, 'https://motorfix.co.ke/product?id=19', 'fb04572df73cc640d64d05c87f8fe0aa2bd234f33a94c6cdcca51623be3404fd', '2025-12-01 04:20:40'),
(314, 29, 'https://motorfix.co.ke/product?id=29', '464252f918098969df140531e4531e85a35e14f152685a67543630b40975f65d', '2025-12-01 07:22:13'),
(315, 32, 'https://motorfix.co.ke/product?id=32', '68eb9f09885839c4c1c4b0e38048b1bcbe1a76d5b197a8d0ce7cbadd3d3ab24b', '2025-12-01 07:28:16'),
(316, 33, 'https://motorfix.co.ke/product?id=33', 'efd0392d75bba25cad5c12dc340415033ff40db333d9ae02203225d03e628b96', '2025-12-01 07:35:43'),
(317, 36, 'https://motorfix.co.ke/product?id=36', 'f1183037da333b3435b2a9bc35f3c7d3fa258d6c9d38bebd15db18e4b44604e6', '2025-12-02 13:49:37'),
(318, 26, 'https://motorfix.co.ke/product?id=26', 'd150228a370800a59f2c05316775bf524ce9a974c5e2fc067fc8f6b0aa13430c', '2025-12-02 16:09:41'),
(319, 30, 'https://motorfix.co.ke/product?id=30', '0eff9d080ba395f5835a3dc24cd9027bdc15cda9d8e1f7cc83dfaba6fbb662d2', '2025-12-02 16:23:02'),
(320, 28, 'https://motorfix.co.ke/product?id=28', 'db7a3102957686f89b8635c895db15ef461154e8c600b7a700c3aacaa2edaaf5', '2025-12-02 17:18:17'),
(321, 34, 'https://motorfix.co.ke/product?id=34', 'db7a3102957686f89b8635c895db15ef461154e8c600b7a700c3aacaa2edaaf5', '2025-12-02 17:18:17'),
(322, 36, 'https://motorfix.co.ke/product?id=36', 'db7a3102957686f89b8635c895db15ef461154e8c600b7a700c3aacaa2edaaf5', '2025-12-02 17:18:17'),
(323, 19, 'https://motorfix.co.ke/product?id=19', 'db7a3102957686f89b8635c895db15ef461154e8c600b7a700c3aacaa2edaaf5', '2025-12-02 17:18:17'),
(324, 27, 'https://motorfix.co.ke/product?id=27', 'db7a3102957686f89b8635c895db15ef461154e8c600b7a700c3aacaa2edaaf5', '2025-12-02 17:18:17'),
(325, 26, 'https://motorfix.co.ke/product?id=26', 'db7a3102957686f89b8635c895db15ef461154e8c600b7a700c3aacaa2edaaf5', '2025-12-02 17:18:17'),
(326, 31, 'https://motorfix.co.ke/product?id=31', 'db7a3102957686f89b8635c895db15ef461154e8c600b7a700c3aacaa2edaaf5', '2025-12-02 17:18:17'),
(327, 32, 'https://motorfix.co.ke/product?id=32', 'db7a3102957686f89b8635c895db15ef461154e8c600b7a700c3aacaa2edaaf5', '2025-12-02 17:18:17'),
(328, 35, 'https://motorfix.co.ke/product?id=35', 'db7a3102957686f89b8635c895db15ef461154e8c600b7a700c3aacaa2edaaf5', '2025-12-02 17:18:17'),
(329, 33, 'https://motorfix.co.ke/product?id=33', 'db7a3102957686f89b8635c895db15ef461154e8c600b7a700c3aacaa2edaaf5', '2025-12-02 17:18:17'),
(330, 30, 'https://motorfix.co.ke/product?id=30', 'db7a3102957686f89b8635c895db15ef461154e8c600b7a700c3aacaa2edaaf5', '2025-12-02 17:18:17'),
(331, 29, 'https://motorfix.co.ke/product?id=29', 'db7a3102957686f89b8635c895db15ef461154e8c600b7a700c3aacaa2edaaf5', '2025-12-02 17:18:17'),
(332, 33, 'https://motorfix.co.ke/product?id=33', '73df8ab646b930a8fd1f94d0599791ee8a34b80c13d8660c19cb426f1f6cf551', '2025-12-02 19:18:59'),
(333, 32, 'https://motorfix.co.ke/product?id=32', 'eb1521ea543a60f10e10004466730be2bcae2d5b0fbeabe0479786faf2ac006f', '2025-12-02 19:28:48'),
(334, 28, 'https://motorfix.co.ke/product?id=28', 'c0e274943088fdf3385a31fab9488b13db7d4e9a156e6b5c74696bd152aa486f', '2025-12-02 19:41:41'),
(335, 29, 'https://motorfix.co.ke/product?id=29', 'b1714bbf56540f346930afb37e37cca712eb7bd36ec3a64bf282c2f2ce78ea25', '2025-12-02 20:21:50'),
(336, 35, 'https://motorfix.co.ke/product?id=35', '6b1f3cef53a89a8328507cd7e8c7a81b5357bd7c4419cbc674da75d50b56f3e1', '2025-12-02 21:24:32'),
(337, 31, 'https://motorfix.co.ke/product?id=31', '595f4cb6549e3e1d4773caf92f4bd20d8e11a00fd3ebb46b21b8c61128cfdc59', '2025-12-02 23:09:08'),
(338, 27, 'https://motorfix.co.ke/product?id=27', 'dd9779b9b23d62521aa206d75cf450618ae50ed7641f21746c19d189e7fee4be', '2025-12-02 23:42:55'),
(339, 19, 'https://motorfix.co.ke/product?id=19', '1e81f6610b6e3eef4b0ded14fe0644cd874786bfb39c4ce88df87e0e2dcc4235', '2025-12-02 23:49:51'),
(340, 34, 'https://motorfix.co.ke/product?id=34', '383e11f7cd08d32047409b8443c1f948d31fc83b7fdb97405fd2764fd24f8df4', '2025-12-03 04:42:54'),
(341, 33, 'https://www.motorfix.co.ke/product?id=33', '30ee0b7805a132cf7276b3bd0d069c059a4c79e9e0ede9baab80be6144889509', '2025-12-04 06:06:56'),
(342, 36, 'https://www.motorfix.co.ke/product?id=36', '30ee0b7805a132cf7276b3bd0d069c059a4c79e9e0ede9baab80be6144889509', '2025-12-04 06:08:00'),
(343, 36, 'https://motorfix.co.ke/product?id=36', '4af238e8c037332cc95e4fd274da1343778f9710906f6738cf41e01ea3f009bb', '2025-12-04 07:24:36'),
(344, 27, 'https://www.motorfix.co.ke/product?id=27', '30ee0b7805a132cf7276b3bd0d069c059a4c79e9e0ede9baab80be6144889509', '2025-12-04 16:23:11'),
(345, 34, 'https://motorfix.co.ke/product?id=34', '463897c2a35435e2ec806610c565ebda8c29df0345aeaff5f55d6c524862d6cd', '2025-12-04 17:25:54'),
(346, 26, 'https://motorfix.co.ke/product?id=26', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-12-05 07:05:54'),
(347, 19, 'https://motorfix.co.ke/product?id=19', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-12-05 07:05:54'),
(348, 28, 'https://motorfix.co.ke/product?id=28', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-12-05 07:05:55'),
(349, 27, 'https://motorfix.co.ke/product?id=27', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-12-05 07:05:55'),
(350, 29, 'https://motorfix.co.ke/product?id=29', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-12-05 07:05:55'),
(351, 31, 'https://motorfix.co.ke/product?id=31', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-12-05 07:05:56'),
(352, 32, 'https://motorfix.co.ke/product?id=32', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-12-05 07:05:56'),
(353, 30, 'https://motorfix.co.ke/product?id=30', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-12-05 07:05:56'),
(354, 34, 'https://motorfix.co.ke/product?id=34', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-12-05 07:05:57'),
(355, 33, 'https://motorfix.co.ke/product?id=33', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-12-05 07:05:57'),
(356, 35, 'https://motorfix.co.ke/product?id=35', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-12-05 07:05:57'),
(357, 36, 'https://motorfix.co.ke/product?id=36', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2025-12-05 07:05:57'),
(358, 31, 'https://motorfix.co.ke/product?id=31', 'f1c26e7e1ad402ab6e65abb972896579756939611fd23173953e95a10c174c38', '2025-12-05 10:49:12'),
(359, 30, 'https://motorfix.co.ke/product?id=30', '689d6fbd8ea5fd5da5e5bb0c7ccea9a3169bb67f8730ae6fc1c531896f14489e', '2025-12-06 02:29:37'),
(360, 31, 'https://motorfix.co.ke/product?id=31', 'de9b8ca97f6e097ffac23ed447341d1ad6e85ddcf8180b3442459defe8a79f9e', '2025-12-06 02:36:39'),
(361, 28, 'https://motorfix.co.ke/product?id=28', 'b28a05d67f74cac34093ecfe4af9dbb6eb093d451f5d1280ac7ff1d0b93dacdc', '2025-12-06 02:41:40'),
(362, 33, 'https://motorfix.co.ke/product?id=33', 'a3a77b13f329cf88e519f825cc4ce2f3123c43298f1f04beacaf6e773aca78de', '2025-12-06 02:55:08'),
(363, 29, 'https://motorfix.co.ke/product?id=29', '5b8be1c622b544c2f4e7a15f1f528b2a19710d70cb913957b49ffb682048361e', '2025-12-06 03:04:44'),
(364, 35, 'https://motorfix.co.ke/product?id=35', '40c62ee930ef705f28d71499e3697740ca0567d3d40a09ece600ef9a21cd97b1', '2025-12-06 03:24:50'),
(365, 26, 'https://motorfix.co.ke/product?id=26', 'a941548ec660a1ace657de6eb702a25e7477978f2563fe412e1ac6d61ed8900c', '2025-12-06 03:50:08'),
(366, 27, 'https://motorfix.co.ke/product?id=27', '2b48a737d65c70cfb308a0bff0da7e2877c718396092e3ee31ac446af88c9843', '2025-12-06 03:59:27'),
(367, 34, 'https://motorfix.co.ke/product?id=34', '2c7d358b5564bade821791809a4e642ba9b85fb9efe95edcc31e0b94c111d4a2', '2025-12-06 04:09:06'),
(368, 32, 'https://motorfix.co.ke/product?id=32', '935a412cf24b934a2835b59a64d787e0af454ffc475fdbfc2321ae0c4bfd5c1e', '2025-12-06 04:14:36'),
(369, 19, 'https://motorfix.co.ke/product?id=19', 'f919dafd1ce7745af1bc1ddb49b62944b0fe65f28c5b266f93d72de219560b1b', '2025-12-06 04:47:31'),
(370, 36, 'https://motorfix.co.ke/product?id=36', '7771e66e02780f5e60d2d1c4ee00b2bbb721f13682cf0c684fec3247d18fcf1c', '2025-12-06 04:58:43'),
(371, 19, 'https://motorfix.co.ke/product?id=19', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-12-07 13:43:39'),
(372, 26, 'https://motorfix.co.ke/product?id=26', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-12-07 13:43:39'),
(373, 27, 'https://motorfix.co.ke/product?id=27', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-12-07 13:43:40'),
(374, 28, 'https://motorfix.co.ke/product?id=28', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-12-07 13:43:40'),
(375, 29, 'https://motorfix.co.ke/product?id=29', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-12-07 13:43:40'),
(376, 30, 'https://motorfix.co.ke/product?id=30', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-12-07 13:43:41'),
(377, 32, 'https://motorfix.co.ke/product?id=32', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-12-07 13:43:41'),
(378, 31, 'https://motorfix.co.ke/product?id=31', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-12-07 13:43:41'),
(379, 35, 'https://motorfix.co.ke/product?id=35', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-12-07 13:43:41'),
(380, 33, 'https://motorfix.co.ke/product?id=33', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-12-07 13:43:41'),
(381, 34, 'https://motorfix.co.ke/product?id=34', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-12-07 13:43:41'),
(382, 36, 'https://motorfix.co.ke/product?id=36', '353ada0211511dbc2fa9e853e91ef2dfaa4b8e7886c60441a28f18f51840f061', '2025-12-07 13:43:42'),
(383, 32, 'https://motorfix.co.ke/product?id=32', '81e3a34155adc8f167770ef42cac17536e08561aed9868b0c94caf55daca8463', '2025-12-09 04:31:57'),
(384, 19, 'https://motorfix.co.ke/product?id=19', 'b13a11fd268d0390c8f7ef49b281da0e6467c5376df639411220c241058a1c30', '2025-12-10 08:42:36'),
(385, 28, 'https://www.motorfix.co.ke/product?id=28', '96657d222c12b2af6a9f95c1efd444b1eaba6e8ae13269156d3db5e3bcfc06be', '2025-12-12 03:50:22'),
(386, 26, 'https://www.motorfix.co.ke/product?id=26', '96657d222c12b2af6a9f95c1efd444b1eaba6e8ae13269156d3db5e3bcfc06be', '2025-12-12 03:51:55'),
(387, 26, 'https://www.motorfix.co.ke/product?id=26', '96657d222c12b2af6a9f95c1efd444b1eaba6e8ae13269156d3db5e3bcfc06be', '2025-12-12 03:53:00'),
(388, 35, 'https://motorfix.co.ke/product?id=35', '438f0778a9d28b1785e42477a391d19a8657d089794e71cfffb985c46e460d77', '2025-12-12 14:03:16'),
(389, 31, 'https://www.motorfix.co.ke/product?id=31', 'a570b969d0e7782d7e5f2c5f987122c16079faf68ccd80358c5427904ff8d014', '2025-12-12 23:02:58'),
(390, 36, 'https://www.motorfix.co.ke/product?id=36', 'a570b969d0e7782d7e5f2c5f987122c16079faf68ccd80358c5427904ff8d014', '2025-12-12 23:03:02'),
(391, 35, 'https://www.motorfix.co.ke/product?id=35', 'a570b969d0e7782d7e5f2c5f987122c16079faf68ccd80358c5427904ff8d014', '2025-12-12 23:03:03'),
(392, 34, 'https://www.motorfix.co.ke/product?id=34', 'a570b969d0e7782d7e5f2c5f987122c16079faf68ccd80358c5427904ff8d014', '2025-12-12 23:03:04'),
(393, 26, 'https://motorfix.co.ke/product?id=26', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2025-12-14 14:11:59'),
(394, 19, 'https://motorfix.co.ke/product?id=19', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2025-12-14 14:11:59'),
(395, 27, 'https://motorfix.co.ke/product?id=27', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2025-12-14 14:12:00'),
(396, 28, 'https://motorfix.co.ke/product?id=28', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2025-12-14 14:12:00'),
(397, 29, 'https://motorfix.co.ke/product?id=29', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2025-12-14 14:12:00'),
(398, 30, 'https://motorfix.co.ke/product?id=30', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2025-12-14 14:12:01'),
(399, 32, 'https://motorfix.co.ke/product?id=32', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2025-12-14 14:12:01'),
(400, 31, 'https://motorfix.co.ke/product?id=31', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2025-12-14 14:12:01'),
(401, 33, 'https://motorfix.co.ke/product?id=33', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2025-12-14 14:12:02'),
(402, 36, 'https://motorfix.co.ke/product?id=36', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2025-12-14 14:12:02'),
(403, 33, 'https://motorfix.co.ke/product?id=33', '0bd42ca54bdf3f31d403fbac8d57219ee35f07e50d0d81431bf5f612d8f74ff5', '2025-12-16 09:59:11'),
(404, 33, 'https://motorfix.co.ke/product?id=33', 'b20513ae296a94c10bb7d7f200ee0cd1bed1497d32f42273ef837e4711fe3d6e', '2025-12-17 05:23:25'),
(405, 33, 'https://motorfix.co.ke/product?id=33', 'a1723a00da6633a5b6899be5e1535b80f1a30286285eff0e4afcdf18111e90c1', '2025-12-18 03:31:31'),
(406, 19, 'https://motorfix.co.ke/product?id=19', '6c4c2350cea7f733fd3b066c7791cac2535efb96e3c977737f5edcbc0c5cec21', '2025-12-20 08:28:40'),
(407, 32, 'https://motorfix.co.ke/product?id=32', '0e9f93d67d2767faa28943aa76ec606e9aff1e6ab3e5fbd1df321ca90a9924be', '2025-12-20 13:52:53');
INSERT INTO `page_views` (`id`, `product_id`, `url`, `visitor_identifier`, `view_timestamp`) VALUES
(408, 19, 'https://motorfix.co.ke/product?id=19', '53ec9dcdd8d68d5848bfc4d674f914884f182210ec676360c557fa7bb94b676d', '2025-12-20 21:13:21'),
(409, 19, 'https://motorfix.co.ke/product?id=19', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2025-12-21 14:44:27'),
(410, 26, 'https://motorfix.co.ke/product?id=26', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2025-12-21 14:44:27'),
(411, 28, 'https://motorfix.co.ke/product?id=28', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2025-12-21 14:44:28'),
(412, 27, 'https://motorfix.co.ke/product?id=27', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2025-12-21 14:44:28'),
(413, 29, 'https://motorfix.co.ke/product?id=29', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2025-12-21 14:44:28'),
(414, 30, 'https://motorfix.co.ke/product?id=30', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2025-12-21 14:44:29'),
(415, 31, 'https://motorfix.co.ke/product?id=31', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2025-12-21 14:44:29'),
(416, 32, 'https://motorfix.co.ke/product?id=32', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2025-12-21 14:44:29'),
(417, 34, 'https://motorfix.co.ke/product?id=34', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2025-12-21 14:44:30'),
(418, 33, 'https://motorfix.co.ke/product?id=33', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2025-12-21 14:44:30'),
(419, 35, 'https://motorfix.co.ke/product?id=35', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2025-12-21 14:44:30'),
(420, 36, 'https://motorfix.co.ke/product?id=36', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2025-12-21 14:44:30'),
(421, 28, 'https://motorfix.co.ke/product?id=28', '75bca399af191317754a914a78af1e258bffacd074e364d5c0c1afaca292ea8d', '2025-12-23 01:23:09'),
(422, 34, 'https://motorfix.co.ke/product?id=34', 'c8c0db1de31292223fc25edc5ec0924f3829030cc152df362a10aebf05863eae', '2025-12-26 20:22:52'),
(423, 28, 'https://motorfix.co.ke/product?id=28', '34e6f99ed1e85d680267091be3acae4154d4d003098a0e80515055828ec54ae7', '2025-12-27 11:18:08'),
(424, 19, 'https://motorfix.co.ke/product?id=19', '7c25941156a3b388c766edcff950efdccfbcacbfc4e02067ffec223a147392e0', '2025-12-28 15:30:02'),
(425, 26, 'https://motorfix.co.ke/product?id=26', '7c25941156a3b388c766edcff950efdccfbcacbfc4e02067ffec223a147392e0', '2025-12-28 15:30:02'),
(426, 28, 'https://motorfix.co.ke/product?id=28', '7c25941156a3b388c766edcff950efdccfbcacbfc4e02067ffec223a147392e0', '2025-12-28 15:30:03'),
(427, 27, 'https://motorfix.co.ke/product?id=27', '7c25941156a3b388c766edcff950efdccfbcacbfc4e02067ffec223a147392e0', '2025-12-28 15:30:03'),
(428, 29, 'https://motorfix.co.ke/product?id=29', '7c25941156a3b388c766edcff950efdccfbcacbfc4e02067ffec223a147392e0', '2025-12-28 15:30:03'),
(429, 30, 'https://motorfix.co.ke/product?id=30', '7c25941156a3b388c766edcff950efdccfbcacbfc4e02067ffec223a147392e0', '2025-12-28 15:30:04'),
(430, 31, 'https://motorfix.co.ke/product?id=31', '7c25941156a3b388c766edcff950efdccfbcacbfc4e02067ffec223a147392e0', '2025-12-28 15:30:04'),
(431, 32, 'https://motorfix.co.ke/product?id=32', '7c25941156a3b388c766edcff950efdccfbcacbfc4e02067ffec223a147392e0', '2025-12-28 15:30:04'),
(432, 33, 'https://motorfix.co.ke/product?id=33', '7c25941156a3b388c766edcff950efdccfbcacbfc4e02067ffec223a147392e0', '2025-12-28 15:30:05'),
(433, 36, 'https://motorfix.co.ke/product?id=36', '7c25941156a3b388c766edcff950efdccfbcacbfc4e02067ffec223a147392e0', '2025-12-28 15:30:05'),
(434, 28, 'https://motorfix.co.ke/product?id=28', 'd0102a20aceaf41e13bcbca046745fa99ee103f7b93a8904e221bba4a0206d24', '2026-01-02 08:10:04'),
(435, 28, 'https://motorfix.co.ke/product?id=28', '53ec9dcdd8d68d5848bfc4d674f914884f182210ec676360c557fa7bb94b676d', '2026-01-04 00:02:52'),
(436, 19, 'https://www.motorfix.co.ke/product?id=19', 'f7e8912f88f67569411608c17d9f6d358c41944d47a7e3b79e9778359f684a82', '2026-01-11 11:01:47'),
(437, 31, 'https://www.motorfix.co.ke/product?id=31', '1614580b1dd11af7e91a05a1a8bdb83c3a1471be93881bce6b88f2b927a6f3aa', '2026-01-12 02:12:51'),
(438, 36, 'https://www.motorfix.co.ke/product?id=36', '1614580b1dd11af7e91a05a1a8bdb83c3a1471be93881bce6b88f2b927a6f3aa', '2026-01-12 02:12:55'),
(439, 35, 'https://www.motorfix.co.ke/product?id=35', '1614580b1dd11af7e91a05a1a8bdb83c3a1471be93881bce6b88f2b927a6f3aa', '2026-01-12 02:12:56'),
(440, 34, 'https://www.motorfix.co.ke/product?id=34', '1614580b1dd11af7e91a05a1a8bdb83c3a1471be93881bce6b88f2b927a6f3aa', '2026-01-12 02:12:57'),
(441, 26, 'https://motorfix.co.ke/product?id=26', 'be7f4aad747fd5a3b84e28ab5ca0aacea417e753781a68435d40fa730bd78e01', '2026-01-13 06:42:55'),
(442, 28, 'https://www.motorfix.co.ke/product?id=28', 'ec9c04c53c990e6e9fa60828d1b8db7c22f52de794e2ab972b2454a8c79138cf', '2026-01-18 22:39:01'),
(443, 36, 'https://www.motorfix.co.ke/product?id=36', '8b2e41ee5037d30e9f6c0ab61216b31502117fe1504fe979441e4183444f6015', '2026-01-19 02:46:37'),
(444, 32, 'https://www.motorfix.co.ke/product?id=32', '4d464c9bdb8498e2be9d51a1a497dd1903f79e516058af07297aabd605e8b321', '2026-01-19 04:25:45'),
(445, 19, 'https://www.motorfix.co.ke/product?id=19', '1321f0ec874e2fdc61e1a5112ddec3d8d38909a69b6cf2547e7756d3e2df852f', '2026-01-19 05:08:16'),
(446, 34, 'https://www.motorfix.co.ke/product?id=34', '46f60032b70e3c47e6a0c60cffe6965827607fb789047ed8e9c328cc469ebe06', '2026-01-19 06:35:57'),
(447, 29, 'https://www.motorfix.co.ke/product?id=29', 'd34da6ce69c8261199c5f4d0f00b835d8898afecd40aed5e71a97949d3920f69', '2026-01-19 09:13:19'),
(448, 31, 'https://www.motorfix.co.ke/product?id=31', '95125848e0c9061e1391beba36785a23b5cfbd16159b58d31fc46c0bf3200b48', '2026-01-19 09:14:12'),
(449, 29, 'https://www.motorfix.co.ke/product?id=29', '71e113e3947e2941adeef823f3385c60ce7799dedef1e5352f1fc7be1c4e55b9', '2026-01-19 11:56:36'),
(450, 26, 'https://www.motorfix.co.ke/product?id=26', 'b13821c30b5a3c4957af99ee89294bedcaf9a5efd876f48d2599fee6b1fd7120', '2026-01-19 12:55:32'),
(451, 30, 'https://www.motorfix.co.ke/product?id=30', 'f8627f488951af161f676f44a1ea9ac27f43d0dfa19705d8797638e33c86455c', '2026-01-19 13:48:24'),
(452, 27, 'https://www.motorfix.co.ke/product?id=27', '540c4568e9af1e681999737ce8c597a5c3500172bd5abccc87d3e1d355769053', '2026-01-19 15:37:47'),
(453, 19, 'https://www.motorfix.co.ke/product?id=19', '0eff9d080ba395f5835a3dc24cd9027bdc15cda9d8e1f7cc83dfaba6fbb662d2', '2026-01-22 08:04:36'),
(454, 14, 'https://motorfix.co.ke/product?id=14', 'de320bbf1d2f97fdb7d0ad9cbdfc90a7144278d3d31c14be0e2a277c1bc83907', '2026-01-22 10:34:33'),
(455, 35, 'https://www.motorfix.co.ke/product?id=35', '2bf71b7b54cb1389507bd18d00a9966ac7cefab66f32eef36f692f368166117f', '2026-01-22 15:42:20'),
(456, 33, 'https://www.motorfix.co.ke/product?id=33', '6b58fa06eb14c3a3c03b5cedfd7dadab1227304e773eb693ffa8bcb9a0dc27f0', '2026-01-22 20:14:49'),
(457, 29, 'https://www.motorfix.co.ke/product?id=29', '18f5f98c33e160429b9c9afa7c510d12423bbc1fd1644cc873919aba224bff6e', '2026-01-23 04:15:32'),
(458, 26, 'https://motorfix.co.ke/product?id=26', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2026-01-25 17:20:07'),
(459, 19, 'https://motorfix.co.ke/product?id=19', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2026-01-25 17:20:07'),
(460, 28, 'https://motorfix.co.ke/product?id=28', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2026-01-25 17:20:08'),
(461, 27, 'https://motorfix.co.ke/product?id=27', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2026-01-25 17:20:08'),
(462, 29, 'https://motorfix.co.ke/product?id=29', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2026-01-25 17:20:08'),
(463, 32, 'https://motorfix.co.ke/product?id=32', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2026-01-25 17:20:09'),
(464, 30, 'https://motorfix.co.ke/product?id=30', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2026-01-25 17:20:09'),
(465, 31, 'https://motorfix.co.ke/product?id=31', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2026-01-25 17:20:09'),
(466, 33, 'https://motorfix.co.ke/product?id=33', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2026-01-25 17:20:09'),
(467, 36, 'https://motorfix.co.ke/product?id=36', '4da83183fd307535d6e14a9acc9cc9d86c12060b8dee86c340c53d8dc3944c5b', '2026-01-25 17:20:10'),
(468, 19, 'https://motorfix.co.ke/product?id=19', '3bce13f9bc875957b89f4abfa99f9a4506b3d95d1dbcff19d566eab4a90cf9df', '2026-01-28 08:47:34'),
(469, 35, 'https://motorfix.co.ke/product?id=35', '5b04b811d6a4002a48456a3da2562d6140fb6eebbb48ba029e913dcb8d213d7b', '2026-01-30 04:57:09'),
(470, 31, 'https://motorfix.co.ke/product?id=31', '5b04b811d6a4002a48456a3da2562d6140fb6eebbb48ba029e913dcb8d213d7b', '2026-01-30 04:57:14'),
(471, 32, 'https://motorfix.co.ke/product?id=32', '5b04b811d6a4002a48456a3da2562d6140fb6eebbb48ba029e913dcb8d213d7b', '2026-01-30 04:57:18'),
(472, 26, 'https://motorfix.co.ke/product?id=26', '5b04b811d6a4002a48456a3da2562d6140fb6eebbb48ba029e913dcb8d213d7b', '2026-01-30 04:57:19'),
(473, 29, 'https://motorfix.co.ke/product?id=29', '5b04b811d6a4002a48456a3da2562d6140fb6eebbb48ba029e913dcb8d213d7b', '2026-01-30 04:57:21'),
(474, 19, 'https://motorfix.co.ke/product?id=19', '5b04b811d6a4002a48456a3da2562d6140fb6eebbb48ba029e913dcb8d213d7b', '2026-01-30 04:57:22'),
(475, 30, 'https://motorfix.co.ke/product?id=30', '5b04b811d6a4002a48456a3da2562d6140fb6eebbb48ba029e913dcb8d213d7b', '2026-01-30 04:57:23'),
(476, 27, 'https://motorfix.co.ke/product?id=27', '5b04b811d6a4002a48456a3da2562d6140fb6eebbb48ba029e913dcb8d213d7b', '2026-01-30 04:57:24'),
(477, 28, 'https://motorfix.co.ke/product?id=28', 'a8ee4c90813262d668601b1f60e90876acb876fc0409def7f7f9712697e1d3c5', '2026-02-01 21:12:34'),
(478, 32, 'https://motorfix.co.ke/product?id=32', 'ef3bd4277991ce165c7c33d58ba4892235b3f6020f7064cf87d67e0c96a58f20', '2026-02-04 04:52:34'),
(479, 30, 'https://www.motorfix.co.ke/product?id=30', '8d156f83c97b0258b0e7fee430e3054ad7d3be596eb9a87382b070573fa25cc5', '2026-02-06 13:07:47'),
(480, 34, 'https://www.motorfix.co.ke/product?id=34', 'c69847c8e07210ade74a41b564804e9bd3ea4883fdb845ff47a5f8b18d3b6e80', '2026-02-06 16:18:45'),
(481, 35, 'https://www.motorfix.co.ke/product?id=35', '22e012344ee0a907c88a3bbf67613f0cbdf224994da04e9ce7df8060966d6747', '2026-02-06 16:19:51'),
(482, 27, 'https://www.motorfix.co.ke/product?id=27', '11ccc0047c9d7d9b4d0a868890bb45e8e0a5d69dbe969ca4c6c534ede4448e99', '2026-02-06 18:55:57'),
(483, 28, 'https://www.motorfix.co.ke/product?id=28', '1eed895f83b021c5c33780893d3f8eb9c1803bf759757632490390507350d51b', '2026-02-06 19:34:10'),
(484, 32, 'https://www.motorfix.co.ke/product?id=32', 'bd85414622babef2475461943242aadb792e1599c4b9a6f0dc948651543bb0eb', '2026-02-06 21:00:31'),
(485, 36, 'https://www.motorfix.co.ke/product?id=36', '20b05b957715422b565bb07621c25f2102c1f59dcd4df41f6167bf1238e7c1b3', '2026-02-06 21:11:24'),
(486, 31, 'https://www.motorfix.co.ke/product?id=31', '9255eb8617e94b9e2993932588010f07573ce1b6dfc8ce9583f2d2edc09e2b3a', '2026-02-06 22:50:33'),
(487, 26, 'https://www.motorfix.co.ke/product?id=26', 'ba800eedf02289437431f5b1e1663654581a66259df511c18efb527d3189c511', '2026-02-06 23:31:43'),
(488, 33, 'https://www.motorfix.co.ke/product?id=33', '1f0c4c6d3f51f121a9615a88c3304354ee7e17b213fa1dcb3a25f87368b38040', '2026-02-07 05:22:26'),
(489, 26, 'https://motorfix.co.ke/product?id=26', '11d4f027e6a43911a2691af5f49e31e89e60019a0d14b80dc326b14214570bd7', '2026-02-08 17:22:13'),
(490, 19, 'https://motorfix.co.ke/product?id=19', '11d4f027e6a43911a2691af5f49e31e89e60019a0d14b80dc326b14214570bd7', '2026-02-08 17:22:13'),
(491, 28, 'https://motorfix.co.ke/product?id=28', '11d4f027e6a43911a2691af5f49e31e89e60019a0d14b80dc326b14214570bd7', '2026-02-08 17:22:13'),
(492, 27, 'https://motorfix.co.ke/product?id=27', '11d4f027e6a43911a2691af5f49e31e89e60019a0d14b80dc326b14214570bd7', '2026-02-08 17:22:13'),
(493, 29, 'https://motorfix.co.ke/product?id=29', '11d4f027e6a43911a2691af5f49e31e89e60019a0d14b80dc326b14214570bd7', '2026-02-08 17:22:14'),
(494, 32, 'https://motorfix.co.ke/product?id=32', '11d4f027e6a43911a2691af5f49e31e89e60019a0d14b80dc326b14214570bd7', '2026-02-08 17:22:15'),
(495, 31, 'https://motorfix.co.ke/product?id=31', '11d4f027e6a43911a2691af5f49e31e89e60019a0d14b80dc326b14214570bd7', '2026-02-08 17:22:15'),
(496, 30, 'https://motorfix.co.ke/product?id=30', '11d4f027e6a43911a2691af5f49e31e89e60019a0d14b80dc326b14214570bd7', '2026-02-08 17:22:15'),
(497, 35, 'https://motorfix.co.ke/product?id=35', '11d4f027e6a43911a2691af5f49e31e89e60019a0d14b80dc326b14214570bd7', '2026-02-08 17:22:15'),
(498, 34, 'https://motorfix.co.ke/product?id=34', '11d4f027e6a43911a2691af5f49e31e89e60019a0d14b80dc326b14214570bd7', '2026-02-08 17:22:15'),
(499, 33, 'https://motorfix.co.ke/product?id=33', '11d4f027e6a43911a2691af5f49e31e89e60019a0d14b80dc326b14214570bd7', '2026-02-08 17:22:15'),
(500, 36, 'https://motorfix.co.ke/product?id=36', '11d4f027e6a43911a2691af5f49e31e89e60019a0d14b80dc326b14214570bd7', '2026-02-08 17:22:15'),
(501, 31, 'https://www.motorfix.co.ke/product?id=31', '590f9ae78cfbbde36e0ef8cc017bc2eec7ffd300bd62d5b4b6852fedc908b5d1', '2026-02-10 18:16:47'),
(502, 36, 'https://www.motorfix.co.ke/product?id=36', '590f9ae78cfbbde36e0ef8cc017bc2eec7ffd300bd62d5b4b6852fedc908b5d1', '2026-02-10 18:16:51'),
(503, 35, 'https://www.motorfix.co.ke/product?id=35', '590f9ae78cfbbde36e0ef8cc017bc2eec7ffd300bd62d5b4b6852fedc908b5d1', '2026-02-10 18:16:52'),
(504, 34, 'https://www.motorfix.co.ke/product?id=34', '590f9ae78cfbbde36e0ef8cc017bc2eec7ffd300bd62d5b4b6852fedc908b5d1', '2026-02-10 18:16:53'),
(505, 33, 'https://motorfix.co.ke/product?id=33', '36abf1449f2f2e6478e4fb098a843a242e4f9eb119442e3e6bedac7491c3beaa', '2026-02-12 01:34:50'),
(506, 19, 'https://motorfix.co.ke/product?id=19', '64f1561d902275fa6b92620631a83b93d8a5295880448875ea19f283ad5e09aa', '2026-02-13 12:21:02'),
(507, 27, 'https://motorfix.co.ke/product?id=27', '91a14ddf1a085ce3643a13b31537e3cdd9cc7aca7c8c57e4ebba14701da092e6', '2026-02-13 22:55:56'),
(508, 19, 'https://motorfix.co.ke/product?id=19', '556323304448b9f3342ecee77ee3f78eae1af5a00d78cd188bb8ea18c50994bc', '2026-02-15 02:59:09'),
(509, 26, 'https://motorfix.co.ke/product?id=26', 'b0da55c15bca40284f1b7a2c96c876a3a7908a3f5818b97cb7651e0dcc7279c9', '2026-02-15 19:44:46'),
(510, 19, 'https://motorfix.co.ke/product?id=19', 'b0da55c15bca40284f1b7a2c96c876a3a7908a3f5818b97cb7651e0dcc7279c9', '2026-02-15 19:44:46'),
(511, 28, 'https://motorfix.co.ke/product?id=28', 'b0da55c15bca40284f1b7a2c96c876a3a7908a3f5818b97cb7651e0dcc7279c9', '2026-02-15 19:44:47'),
(512, 27, 'https://motorfix.co.ke/product?id=27', 'b0da55c15bca40284f1b7a2c96c876a3a7908a3f5818b97cb7651e0dcc7279c9', '2026-02-15 19:44:47'),
(513, 29, 'https://motorfix.co.ke/product?id=29', 'b0da55c15bca40284f1b7a2c96c876a3a7908a3f5818b97cb7651e0dcc7279c9', '2026-02-15 19:44:47'),
(514, 30, 'https://motorfix.co.ke/product?id=30', 'b0da55c15bca40284f1b7a2c96c876a3a7908a3f5818b97cb7651e0dcc7279c9', '2026-02-15 19:44:48'),
(515, 31, 'https://motorfix.co.ke/product?id=31', 'b0da55c15bca40284f1b7a2c96c876a3a7908a3f5818b97cb7651e0dcc7279c9', '2026-02-15 19:44:48'),
(516, 32, 'https://motorfix.co.ke/product?id=32', 'b0da55c15bca40284f1b7a2c96c876a3a7908a3f5818b97cb7651e0dcc7279c9', '2026-02-15 19:44:48'),
(517, 34, 'https://motorfix.co.ke/product?id=34', 'b0da55c15bca40284f1b7a2c96c876a3a7908a3f5818b97cb7651e0dcc7279c9', '2026-02-15 19:44:49'),
(518, 26, 'https://motorfix.co.ke/product?id=26', '98ea9067f72d25daa4e74adcf57108e95c7aed4fa20888a970f3697be8404085', '2026-02-15 22:57:54'),
(519, 28, 'https://motorfix.co.ke/product?id=28', 'c0c64a15abeaaa6092ea1fed1f1f0ea224a43a6d2860b8809ac51d68c558ef8b', '2026-02-15 22:58:16'),
(520, 33, 'https://motorfix.co.ke/product?id=33', '4cb0d4c9c3e386a9ce50ea4fa14ba0346b323c4c632325817c814cb7903d18e1', '2026-02-15 23:02:06'),
(521, 29, 'https://motorfix.co.ke/product?id=29', '3c7dc8011ccf6daeb3df2e5127d8d014c19892c600d7cce6259df1c32e71cd1a', '2026-02-15 23:57:53'),
(522, 30, 'https://motorfix.co.ke/product?id=30', '4017dfcb1582c1dc53a54c9140d4959eebdc542797217c55462e1cef21153888', '2026-02-16 00:22:47'),
(523, 31, 'https://motorfix.co.ke/product?id=31', '1744057afb96d967b212b4ad510c8631882a6a7de6cc07fc8ca1adbf86b67fa4', '2026-02-16 00:48:26'),
(524, 27, 'https://motorfix.co.ke/product?id=27', '1c50749d59cf7d8cf0f9588f15e7c7a47d3e038f05c5f819f1d9967baa31a767', '2026-02-16 00:48:29'),
(525, 32, 'https://motorfix.co.ke/product?id=32', '1a26cb8b9be1833484ca90b67007ca3aa5afb8d7053f489736a1434d2c486c9a', '2026-02-16 00:55:49'),
(526, 35, 'https://motorfix.co.ke/product?id=35', 'b82115378bda06b745606ae3aecff71cc6bd40a797ad0b6431084e0b21ece060', '2026-02-16 01:21:06'),
(527, 36, 'https://motorfix.co.ke/product?id=36', 'b82115378bda06b745606ae3aecff71cc6bd40a797ad0b6431084e0b21ece060', '2026-02-16 03:13:19'),
(528, 29, 'https://motorfix.co.ke/product?id=29', 'c6c7064c7543dd1b909da534d4dc28cb426f62a4be53c11afe2d89f1af1028a4', '2026-02-18 06:33:09'),
(529, 34, 'https://www.motorfix.co.ke/product?id=34', '042c7e4477331012741d68fb69cc7c5666910a1786c1f340ba6922ccc4aa9200', '2026-02-19 08:02:32'),
(530, 28, 'https://www.motorfix.co.ke/product?id=28', '042c7e4477331012741d68fb69cc7c5666910a1786c1f340ba6922ccc4aa9200', '2026-02-19 09:36:31'),
(531, 26, 'https://motorfix.co.ke/product?id=26', '30ac10efda75f16967984ad9a0d91709e1fa47ededf61daac6699f94856e1437', '2026-02-22 20:26:48'),
(532, 19, 'https://motorfix.co.ke/product?id=19', '30ac10efda75f16967984ad9a0d91709e1fa47ededf61daac6699f94856e1437', '2026-02-22 20:26:48'),
(533, 28, 'https://motorfix.co.ke/product?id=28', '30ac10efda75f16967984ad9a0d91709e1fa47ededf61daac6699f94856e1437', '2026-02-22 20:26:48'),
(534, 27, 'https://motorfix.co.ke/product?id=27', '30ac10efda75f16967984ad9a0d91709e1fa47ededf61daac6699f94856e1437', '2026-02-22 20:26:48'),
(535, 29, 'https://motorfix.co.ke/product?id=29', '30ac10efda75f16967984ad9a0d91709e1fa47ededf61daac6699f94856e1437', '2026-02-22 20:26:49'),
(536, 32, 'https://motorfix.co.ke/product?id=32', '30ac10efda75f16967984ad9a0d91709e1fa47ededf61daac6699f94856e1437', '2026-02-22 20:26:49'),
(537, 31, 'https://motorfix.co.ke/product?id=31', '30ac10efda75f16967984ad9a0d91709e1fa47ededf61daac6699f94856e1437', '2026-02-22 20:26:49'),
(538, 35, 'https://motorfix.co.ke/product?id=35', '30ac10efda75f16967984ad9a0d91709e1fa47ededf61daac6699f94856e1437', '2026-02-22 20:26:50'),
(539, 28, 'https://motorfix.co.ke/product?id=28', '2a8764774998e2cb2cc4b135f60c790594935d89081b88b7e9aa9cf74a2061eb', '2026-02-25 15:46:35'),
(540, 28, 'https://motorfix.co.ke/product?id=28', 'eb0bc9ab66b69ce12d3d830c3da7c7da69d681b530e387efaf18d50d1a8ba45d', '2026-02-25 15:46:37'),
(541, 28, 'https://motorfix.co.ke/product?id=28', '94a4c9d186c58f28320a1bc8c41e2f66423b4f27d5581a4622a8d0bcbd9c8911', '2026-02-25 15:46:38'),
(542, 28, 'https://motorfix.co.ke/product?id=28', '2a8764774998e2cb2cc4b135f60c790594935d89081b88b7e9aa9cf74a2061eb', '2026-02-25 15:46:38'),
(543, 14, 'https://www.motorfix.co.ke/product?id=14', 'cf0a079bac28c937d022d988495e940e55768facc75f0c813bcaeefcb25cf70f', '2026-02-26 18:25:35'),
(544, 14, 'https://www.motorfix.co.ke/product?id=14', 'b1830823c326d5c9a4f4d714e2365f3c443f58feea6962c59c9ca4a09e50cb9e', '2026-02-26 18:25:37'),
(545, 14, 'https://www.motorfix.co.ke/product?id=14', '357212b6bb6efabc290387d8e921604acd396ea3000c2eef22ed48b4c6cd0625', '2026-02-26 18:25:38'),
(546, 14, 'https://www.motorfix.co.ke/product?id=14', '17c80ed4469f2be74843191dae6d4f38668af50ce12548bd8b8e0ddd89ba001a', '2026-02-26 18:25:38'),
(547, 31, 'https://www.motorfix.co.ke/product?id=31', 'cf0a079bac28c937d022d988495e940e55768facc75f0c813bcaeefcb25cf70f', '2026-02-26 18:25:58'),
(548, 31, 'https://www.motorfix.co.ke/product?id=31', 'b1830823c326d5c9a4f4d714e2365f3c443f58feea6962c59c9ca4a09e50cb9e', '2026-02-26 18:26:00'),
(549, 31, 'https://www.motorfix.co.ke/product?id=31', 'daed6c381366a2041e6a9e066b68e3c5d0013b1e31c2d3f10b7a920027cca19c', '2026-02-26 18:26:00'),
(550, 31, 'https://www.motorfix.co.ke/product?id=31', '17c80ed4469f2be74843191dae6d4f38668af50ce12548bd8b8e0ddd89ba001a', '2026-02-26 18:26:00'),
(551, 34, 'https://www.motorfix.co.ke/product?id=34', 'cf0a079bac28c937d022d988495e940e55768facc75f0c813bcaeefcb25cf70f', '2026-02-26 18:31:22'),
(552, 19, 'https://motorfix.co.ke/product?id=19', 'a3c33a5f59435892797711961fed032ce9d4464551e38c38ea53e43d06508e50', '2026-02-27 09:37:11'),
(553, 26, 'https://motorfix.co.ke/product?id=26', 'a3c33a5f59435892797711961fed032ce9d4464551e38c38ea53e43d06508e50', '2026-02-27 09:37:11'),
(554, 27, 'https://motorfix.co.ke/product?id=27', 'a3c33a5f59435892797711961fed032ce9d4464551e38c38ea53e43d06508e50', '2026-02-27 09:37:11'),
(555, 28, 'https://motorfix.co.ke/product?id=28', 'a3c33a5f59435892797711961fed032ce9d4464551e38c38ea53e43d06508e50', '2026-02-27 09:37:12'),
(556, 29, 'https://motorfix.co.ke/product?id=29', 'a3c33a5f59435892797711961fed032ce9d4464551e38c38ea53e43d06508e50', '2026-02-27 09:37:12'),
(557, 31, 'https://motorfix.co.ke/product?id=31', 'a3c33a5f59435892797711961fed032ce9d4464551e38c38ea53e43d06508e50', '2026-02-27 09:37:12'),
(558, 32, 'https://motorfix.co.ke/product?id=32', 'a3c33a5f59435892797711961fed032ce9d4464551e38c38ea53e43d06508e50', '2026-02-27 09:37:13'),
(559, 33, 'https://motorfix.co.ke/product?id=33', 'a3c33a5f59435892797711961fed032ce9d4464551e38c38ea53e43d06508e50', '2026-02-27 09:37:13'),
(560, 34, 'https://motorfix.co.ke/product?id=34', 'a3c33a5f59435892797711961fed032ce9d4464551e38c38ea53e43d06508e50', '2026-02-27 09:37:13'),
(561, 35, 'https://motorfix.co.ke/product?id=35', 'a3c33a5f59435892797711961fed032ce9d4464551e38c38ea53e43d06508e50', '2026-02-27 09:37:13'),
(562, 28, 'https://motorfix.co.ke/product?id=28', '4b168acb87858facd5ebd935001a8437561e02b8f4a858394b635afb9ddd3e04', '2026-02-28 17:51:50'),
(563, 31, 'https://motorfix.co.ke/product?id=31', '9215ec210936cf89ab5ee03c908774a4ccb2add313cb71bdd093cd82c13c8822', '2026-03-01 00:21:55'),
(564, 19, 'https://motorfix.co.ke/product?id=19', 'e84dad68ff407790f0bcc01bd6c3a708d41ffc22cb000e739cd49215a12bcfa4', '2026-03-01 21:11:55'),
(565, 26, 'https://motorfix.co.ke/product?id=26', 'e84dad68ff407790f0bcc01bd6c3a708d41ffc22cb000e739cd49215a12bcfa4', '2026-03-01 21:11:55'),
(566, 27, 'https://motorfix.co.ke/product?id=27', 'e84dad68ff407790f0bcc01bd6c3a708d41ffc22cb000e739cd49215a12bcfa4', '2026-03-01 21:11:56'),
(567, 28, 'https://motorfix.co.ke/product?id=28', 'e84dad68ff407790f0bcc01bd6c3a708d41ffc22cb000e739cd49215a12bcfa4', '2026-03-01 21:11:56'),
(568, 29, 'https://motorfix.co.ke/product?id=29', 'e84dad68ff407790f0bcc01bd6c3a708d41ffc22cb000e739cd49215a12bcfa4', '2026-03-01 21:11:56'),
(569, 30, 'https://motorfix.co.ke/product?id=30', 'e84dad68ff407790f0bcc01bd6c3a708d41ffc22cb000e739cd49215a12bcfa4', '2026-03-01 21:11:57'),
(570, 31, 'https://motorfix.co.ke/product?id=31', 'e84dad68ff407790f0bcc01bd6c3a708d41ffc22cb000e739cd49215a12bcfa4', '2026-03-01 21:11:57'),
(571, 32, 'https://motorfix.co.ke/product?id=32', 'e84dad68ff407790f0bcc01bd6c3a708d41ffc22cb000e739cd49215a12bcfa4', '2026-03-01 21:11:57'),
(572, 33, 'https://motorfix.co.ke/product?id=33', 'e84dad68ff407790f0bcc01bd6c3a708d41ffc22cb000e739cd49215a12bcfa4', '2026-03-01 21:11:57'),
(573, 36, 'https://motorfix.co.ke/product?id=36', 'e84dad68ff407790f0bcc01bd6c3a708d41ffc22cb000e739cd49215a12bcfa4', '2026-03-01 21:11:58'),
(574, 31, 'https://motorfix.co.ke/product?id=31', 'bc28fc8da613d072172c51fa78e332ce0eec3e38433bd5f230ed62a5ffc5887b', '2026-03-03 07:03:23'),
(575, 30, 'https://motorfix.co.ke/product?id=30', '61db44f9b9ce06d7f18bba26c563ae258623aaca657562f5ef79ec4418d2ce49', '2026-03-03 09:50:16'),
(576, 32, 'https://www.motorfix.co.ke/product?id=32', '8fe9177bfe716ccb9d2ac3e144fd33577d6eee1f08942061a8a75acfc4261390', '2026-03-04 14:27:17'),
(577, 27, 'https://www.motorfix.co.ke/product?id=27', '8fe9177bfe716ccb9d2ac3e144fd33577d6eee1f08942061a8a75acfc4261390', '2026-03-04 14:27:20'),
(578, 34, 'https://motorfix.co.ke/product?id=34', '32d444f7e50984d36fbb2711a884036d4f82462b253e5ca9153eb9f629889517', '2026-03-05 12:16:26'),
(579, 26, 'https://motorfix.co.ke/product?id=26', 'c8dff0ea55dd3a30b180f5c9202d0e88ba4a97b949871ed7ef4f36c7a067b9b8', '2026-03-08 22:01:58'),
(580, 19, 'https://motorfix.co.ke/product?id=19', 'c8dff0ea55dd3a30b180f5c9202d0e88ba4a97b949871ed7ef4f36c7a067b9b8', '2026-03-08 22:01:58'),
(581, 27, 'https://motorfix.co.ke/product?id=27', 'c8dff0ea55dd3a30b180f5c9202d0e88ba4a97b949871ed7ef4f36c7a067b9b8', '2026-03-08 22:01:59'),
(582, 28, 'https://motorfix.co.ke/product?id=28', 'c8dff0ea55dd3a30b180f5c9202d0e88ba4a97b949871ed7ef4f36c7a067b9b8', '2026-03-08 22:01:59'),
(583, 29, 'https://motorfix.co.ke/product?id=29', 'c8dff0ea55dd3a30b180f5c9202d0e88ba4a97b949871ed7ef4f36c7a067b9b8', '2026-03-08 22:01:59'),
(584, 30, 'https://motorfix.co.ke/product?id=30', 'c8dff0ea55dd3a30b180f5c9202d0e88ba4a97b949871ed7ef4f36c7a067b9b8', '2026-03-08 22:02:00'),
(585, 32, 'https://motorfix.co.ke/product?id=32', 'c8dff0ea55dd3a30b180f5c9202d0e88ba4a97b949871ed7ef4f36c7a067b9b8', '2026-03-08 22:02:00'),
(586, 31, 'https://motorfix.co.ke/product?id=31', 'c8dff0ea55dd3a30b180f5c9202d0e88ba4a97b949871ed7ef4f36c7a067b9b8', '2026-03-08 22:02:00'),
(587, 33, 'https://motorfix.co.ke/product?id=33', 'c8dff0ea55dd3a30b180f5c9202d0e88ba4a97b949871ed7ef4f36c7a067b9b8', '2026-03-08 22:02:00'),
(588, 34, 'https://motorfix.co.ke/product?id=34', 'c8dff0ea55dd3a30b180f5c9202d0e88ba4a97b949871ed7ef4f36c7a067b9b8', '2026-03-08 22:02:00'),
(589, 35, 'https://motorfix.co.ke/product?id=35', 'c8dff0ea55dd3a30b180f5c9202d0e88ba4a97b949871ed7ef4f36c7a067b9b8', '2026-03-08 22:02:00'),
(590, 36, 'https://motorfix.co.ke/product?id=36', 'c8dff0ea55dd3a30b180f5c9202d0e88ba4a97b949871ed7ef4f36c7a067b9b8', '2026-03-08 22:02:01'),
(591, 28, 'https://motorfix.co.ke/product?id=28', '32d444f7e50984d36fbb2711a884036d4f82462b253e5ca9153eb9f629889517', '2026-03-11 11:31:12'),
(592, 31, 'https://www.motorfix.co.ke/product?id=31', '65efd617827b60a62e1c27c510b2598d886c5040c282f8b1b6546d662c54026f', '2026-03-11 17:12:26'),
(593, 36, 'https://www.motorfix.co.ke/product?id=36', '65efd617827b60a62e1c27c510b2598d886c5040c282f8b1b6546d662c54026f', '2026-03-11 17:12:30'),
(594, 35, 'https://www.motorfix.co.ke/product?id=35', '65efd617827b60a62e1c27c510b2598d886c5040c282f8b1b6546d662c54026f', '2026-03-11 17:12:31'),
(595, 34, 'https://www.motorfix.co.ke/product?id=34', '65efd617827b60a62e1c27c510b2598d886c5040c282f8b1b6546d662c54026f', '2026-03-11 17:12:32'),
(596, 19, 'https://motorfix.co.ke/product?id=19', '9cd931787484c3ef6303309b87647a66d2d3f94e876717a166529dfc0dd53af8', '2026-03-12 09:42:56'),
(597, 32, 'https://www.motorfix.co.ke/product?id=32', '51e1d253df6ee56c073aa1f02af77489ea8de90d02d1f79acf485280fe2a8550', '2026-03-15 09:35:23'),
(598, 36, 'https://www.motorfix.co.ke/product?id=36', 'ec9aaadc44b4380dc5b2b6dd5a9179cec3199c88cba9526b3f0e3b842f9607da', '2026-03-15 09:35:26'),
(599, 35, 'https://www.motorfix.co.ke/product?id=35', 'd67b95c5cf8267e10c6612d16e3f6d451702a21e1ea47196135dd706254eafb6', '2026-03-15 09:35:35'),
(600, 26, 'https://www.motorfix.co.ke/product?id=26', 'ec9aaadc44b4380dc5b2b6dd5a9179cec3199c88cba9526b3f0e3b842f9607da', '2026-03-15 09:35:37'),
(601, 33, 'https://www.motorfix.co.ke/product?id=33', 'ec9aaadc44b4380dc5b2b6dd5a9179cec3199c88cba9526b3f0e3b842f9607da', '2026-03-15 09:35:41'),
(602, 19, 'https://www.motorfix.co.ke/product?id=19', 'd67b95c5cf8267e10c6612d16e3f6d451702a21e1ea47196135dd706254eafb6', '2026-03-15 09:35:43'),
(603, 28, 'https://www.motorfix.co.ke/product?id=28', '34a6aad57443a592a18e0711a0a53f278deb46136445f5982796869808dfb142', '2026-03-15 09:35:47'),
(604, 27, 'https://www.motorfix.co.ke/product?id=27', '51e1d253df6ee56c073aa1f02af77489ea8de90d02d1f79acf485280fe2a8550', '2026-03-15 09:35:49'),
(605, 28, 'https://motorfix.co.ke/product?id=28', '404c264c919282e0054d4fe964791fbe3819d8f1afcbba6f7d88b2aced22936b', '2026-03-15 16:48:05'),
(606, 19, 'https://motorfix.co.ke/product?id=19', 'd4ae0b283e3067f2ee3479a3ed0e3359db3ece8f63433c7b07b0980d60edc82f', '2026-03-15 22:22:08'),
(607, 26, 'https://motorfix.co.ke/product?id=26', 'd4ae0b283e3067f2ee3479a3ed0e3359db3ece8f63433c7b07b0980d60edc82f', '2026-03-15 22:22:08'),
(608, 28, 'https://motorfix.co.ke/product?id=28', 'd4ae0b283e3067f2ee3479a3ed0e3359db3ece8f63433c7b07b0980d60edc82f', '2026-03-15 22:22:09'),
(609, 27, 'https://motorfix.co.ke/product?id=27', 'd4ae0b283e3067f2ee3479a3ed0e3359db3ece8f63433c7b07b0980d60edc82f', '2026-03-15 22:22:09'),
(610, 29, 'https://motorfix.co.ke/product?id=29', 'd4ae0b283e3067f2ee3479a3ed0e3359db3ece8f63433c7b07b0980d60edc82f', '2026-03-15 22:22:09'),
(611, 32, 'https://motorfix.co.ke/product?id=32', 'd4ae0b283e3067f2ee3479a3ed0e3359db3ece8f63433c7b07b0980d60edc82f', '2026-03-15 22:22:10'),
(612, 30, 'https://motorfix.co.ke/product?id=30', 'd4ae0b283e3067f2ee3479a3ed0e3359db3ece8f63433c7b07b0980d60edc82f', '2026-03-15 22:22:10'),
(613, 31, 'https://motorfix.co.ke/product?id=31', 'd4ae0b283e3067f2ee3479a3ed0e3359db3ece8f63433c7b07b0980d60edc82f', '2026-03-15 22:22:10'),
(614, 35, 'https://motorfix.co.ke/product?id=35', 'd4ae0b283e3067f2ee3479a3ed0e3359db3ece8f63433c7b07b0980d60edc82f', '2026-03-15 22:22:10'),
(615, 34, 'https://motorfix.co.ke/product?id=34', 'd4ae0b283e3067f2ee3479a3ed0e3359db3ece8f63433c7b07b0980d60edc82f', '2026-03-15 22:22:10'),
(616, 33, 'https://motorfix.co.ke/product?id=33', 'd4ae0b283e3067f2ee3479a3ed0e3359db3ece8f63433c7b07b0980d60edc82f', '2026-03-15 22:22:10'),
(617, 36, 'https://motorfix.co.ke/product?id=36', 'd4ae0b283e3067f2ee3479a3ed0e3359db3ece8f63433c7b07b0980d60edc82f', '2026-03-15 22:22:11'),
(618, 14, 'https://motorfix.co.ke/product?id=14', 'cbe628e927b2d7b218547bd8984d399ec269b9a845ec2353b33383157189c63d', '2026-03-22 20:39:36'),
(619, 19, 'https://motorfix.co.ke/product?id=19', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-03-22 23:37:06'),
(620, 26, 'https://motorfix.co.ke/product?id=26', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-03-22 23:37:06'),
(621, 28, 'https://motorfix.co.ke/product?id=28', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-03-22 23:37:07'),
(622, 27, 'https://motorfix.co.ke/product?id=27', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-03-22 23:37:07'),
(623, 29, 'https://motorfix.co.ke/product?id=29', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-03-22 23:37:08'),
(624, 32, 'https://motorfix.co.ke/product?id=32', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-03-22 23:37:09'),
(625, 31, 'https://motorfix.co.ke/product?id=31', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-03-22 23:37:09'),
(626, 30, 'https://motorfix.co.ke/product?id=30', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-03-22 23:37:09'),
(627, 35, 'https://motorfix.co.ke/product?id=35', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-03-22 23:37:09'),
(628, 34, 'https://motorfix.co.ke/product?id=34', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-03-22 23:37:09'),
(629, 33, 'https://motorfix.co.ke/product?id=33', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-03-22 23:37:09'),
(630, 36, 'https://motorfix.co.ke/product?id=36', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-03-22 23:37:10'),
(631, 19, 'https://www.motorfix.co.ke/product?id=19', '7c6228c5728e4ff5f24e335315fe48c3a44c828ae6d50886884f8775d4813746', '2026-03-24 08:37:37'),
(632, 30, 'https://www.motorfix.co.ke/product?id=30', '7c6228c5728e4ff5f24e335315fe48c3a44c828ae6d50886884f8775d4813746', '2026-03-24 08:38:13'),
(633, 31, 'https://www.motorfix.co.ke/product?id=31', '10d0cfc11b1eeac30f297711bf0b0ef9fa8ff3401ddb989d1231331b51161816', '2026-03-28 14:50:43'),
(634, 32, 'https://www.motorfix.co.ke/product?id=32', '4520286199c314003c8eaa0a68cdcbd740bd4912c406150404f8b16929db1160', '2026-03-28 14:50:52'),
(635, 36, 'https://motorfix.co.ke/product?id=36', 'c3b84a53ae434dfcb888f221c317c8c3f3058f52d644156501b6d979e73f27ef', '2026-03-29 09:42:26'),
(636, 27, 'https://motorfix.co.ke/product?id=27', 'c3b84a53ae434dfcb888f221c317c8c3f3058f52d644156501b6d979e73f27ef', '2026-03-29 09:42:29'),
(637, 29, 'https://motorfix.co.ke/product?id=29', 'c3b84a53ae434dfcb888f221c317c8c3f3058f52d644156501b6d979e73f27ef', '2026-03-29 09:42:34'),
(638, 19, 'https://motorfix.co.ke/product?id=19', 'c3b84a53ae434dfcb888f221c317c8c3f3058f52d644156501b6d979e73f27ef', '2026-03-29 09:42:36'),
(639, 34, 'https://motorfix.co.ke/product?id=34', 'c3b84a53ae434dfcb888f221c317c8c3f3058f52d644156501b6d979e73f27ef', '2026-03-29 09:42:38'),
(640, 30, 'https://motorfix.co.ke/product?id=30', 'c3b84a53ae434dfcb888f221c317c8c3f3058f52d644156501b6d979e73f27ef', '2026-03-29 09:42:40'),
(641, 26, 'https://motorfix.co.ke/product?id=26', 'c3b84a53ae434dfcb888f221c317c8c3f3058f52d644156501b6d979e73f27ef', '2026-03-29 09:42:41'),
(642, 33, 'https://motorfix.co.ke/product?id=33', 'c3b84a53ae434dfcb888f221c317c8c3f3058f52d644156501b6d979e73f27ef', '2026-03-29 09:42:42'),
(643, 35, 'https://motorfix.co.ke/product?id=35', 'c3b84a53ae434dfcb888f221c317c8c3f3058f52d644156501b6d979e73f27ef', '2026-03-29 09:42:44'),
(644, 31, 'https://motorfix.co.ke/product?id=31', 'c3b84a53ae434dfcb888f221c317c8c3f3058f52d644156501b6d979e73f27ef', '2026-03-29 09:42:45'),
(645, 28, 'https://motorfix.co.ke/product?id=28', 'c3b84a53ae434dfcb888f221c317c8c3f3058f52d644156501b6d979e73f27ef', '2026-03-29 09:42:47'),
(646, 32, 'https://motorfix.co.ke/product?id=32', 'c3b84a53ae434dfcb888f221c317c8c3f3058f52d644156501b6d979e73f27ef', '2026-03-29 09:42:47'),
(647, 28, 'https://www.motorfix.co.ke/product?id=28', '810e6ac4658fedeabfb9cbd49af762233891f9258093ca1b19b65f1871af96cc', '2026-04-01 04:36:04'),
(648, 34, 'https://www.motorfix.co.ke/product?id=34', 'c7f272e5e812980e35183e7c1089624d9637b0151548bb8c33bcf4d975cfda0f', '2026-04-01 04:36:07'),
(649, 27, 'https://www.motorfix.co.ke/product?id=27', '8ddae5135dbaaaed5d3b7e963d9d2b2748494def299f4bccb5a6696edbe1282d', '2026-04-01 04:36:14'),
(650, 30, 'https://www.motorfix.co.ke/product?id=30', '8c31c7888f14755daef653de3342392f5db9cc05ad14ceb4635688b710e1ed38', '2026-04-01 04:36:17'),
(651, 28, 'https://motorfix.co.ke/product?id=28', 'c59493fbf7985bd79b5eccb6694798b62f5c4122a200689a266111fc70d8c02c', '2026-04-01 13:08:29'),
(652, 33, 'https://www.motorfix.co.ke/product?id=33', '393bb41dd23ea0b6a32bbda4b746279ff9eb855c2332169d63a4c14bd62ff4a6', '2026-04-01 13:11:05'),
(653, 27, 'https://www.motorfix.co.ke/product?id=27', '3775d239ec7a0a9d07316415d5cc157326322cf7e7b7be571a81a7f85bc5e986', '2026-04-01 17:46:44'),
(654, 33, 'https://www.motorfix.co.ke/product?id=33', '3b88d816365e54abdb16d23bae580759a63175000e619cc33a9a6bb333af0abe', '2026-04-01 17:46:47'),
(655, 29, 'https://www.motorfix.co.ke/product?id=29', '378470b695d373081a1999e5e96837c6f56f004eba75757d5e59d535ae9a88c5', '2026-04-01 17:46:49'),
(656, 31, 'https://www.motorfix.co.ke/product?id=31', '9b8d55efdafdf2dfe42ecd5303de0b47953df023062d934d1bd804441ae6ac32', '2026-04-01 17:46:54'),
(657, 34, 'https://www.motorfix.co.ke/product?id=34', '9b8d55efdafdf2dfe42ecd5303de0b47953df023062d934d1bd804441ae6ac32', '2026-04-01 17:46:56'),
(658, 30, 'https://www.motorfix.co.ke/product?id=30', '9b8d55efdafdf2dfe42ecd5303de0b47953df023062d934d1bd804441ae6ac32', '2026-04-01 17:46:57'),
(659, 26, 'https://motorfix.co.ke/product?id=26', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-04-06 06:17:45'),
(660, 19, 'https://motorfix.co.ke/product?id=19', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-04-06 06:17:45'),
(661, 28, 'https://motorfix.co.ke/product?id=28', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-04-06 06:17:46'),
(662, 27, 'https://motorfix.co.ke/product?id=27', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-04-06 06:17:46'),
(663, 29, 'https://motorfix.co.ke/product?id=29', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-04-06 06:17:46'),
(664, 32, 'https://motorfix.co.ke/product?id=32', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-04-06 06:17:47'),
(665, 31, 'https://motorfix.co.ke/product?id=31', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-04-06 06:17:47'),
(666, 35, 'https://motorfix.co.ke/product?id=35', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-04-06 06:17:47'),
(667, 19, 'https://motorfix.co.ke/product?id=19', '01edf6dc86f83d987b17ffd6aaa56b9bbc2cd6768ec3874cb7fbadf8d8165e2d', '2026-04-06 20:56:33'),
(668, 33, 'https://motorfix.co.ke/product?id=33', '01edf6dc86f83d987b17ffd6aaa56b9bbc2cd6768ec3874cb7fbadf8d8165e2d', '2026-04-06 20:56:36'),
(669, 30, 'https://motorfix.co.ke/product?id=30', '01edf6dc86f83d987b17ffd6aaa56b9bbc2cd6768ec3874cb7fbadf8d8165e2d', '2026-04-06 20:56:38'),
(670, 26, 'https://motorfix.co.ke/product?id=26', '01edf6dc86f83d987b17ffd6aaa56b9bbc2cd6768ec3874cb7fbadf8d8165e2d', '2026-04-06 20:56:40'),
(671, 36, 'https://motorfix.co.ke/product?id=36', '01edf6dc86f83d987b17ffd6aaa56b9bbc2cd6768ec3874cb7fbadf8d8165e2d', '2026-04-06 20:56:43'),
(672, 27, 'https://motorfix.co.ke/product?id=27', '01edf6dc86f83d987b17ffd6aaa56b9bbc2cd6768ec3874cb7fbadf8d8165e2d', '2026-04-06 20:56:44'),
(673, 31, 'https://motorfix.co.ke/product?id=31', '01edf6dc86f83d987b17ffd6aaa56b9bbc2cd6768ec3874cb7fbadf8d8165e2d', '2026-04-06 20:56:46'),
(674, 28, 'https://motorfix.co.ke/product?id=28', '01edf6dc86f83d987b17ffd6aaa56b9bbc2cd6768ec3874cb7fbadf8d8165e2d', '2026-04-06 20:56:47'),
(675, 35, 'https://motorfix.co.ke/product?id=35', '01edf6dc86f83d987b17ffd6aaa56b9bbc2cd6768ec3874cb7fbadf8d8165e2d', '2026-04-06 20:56:49'),
(676, 29, 'https://motorfix.co.ke/product?id=29', '01edf6dc86f83d987b17ffd6aaa56b9bbc2cd6768ec3874cb7fbadf8d8165e2d', '2026-04-06 20:56:50'),
(677, 34, 'https://motorfix.co.ke/product?id=34', '01edf6dc86f83d987b17ffd6aaa56b9bbc2cd6768ec3874cb7fbadf8d8165e2d', '2026-04-06 20:56:51'),
(678, 32, 'https://motorfix.co.ke/product?id=32', '01edf6dc86f83d987b17ffd6aaa56b9bbc2cd6768ec3874cb7fbadf8d8165e2d', '2026-04-06 20:56:52'),
(679, 29, 'https://motorfix.co.ke/product?id=29', '8aace8bfd42841e572cd8727457cfa9034b679c7b7675a09957179b3bc61f45f', '2026-04-08 09:42:57'),
(680, 29, 'https://motorfix.co.ke/product?id=29', '6b1f3cef53a89a8328507cd7e8c7a81b5357bd7c4419cbc674da75d50b56f3e1', '2026-04-11 01:05:51'),
(681, 31, 'https://www.motorfix.co.ke/product?id=31', '2add8e7e8455ddc29e610a969409f891c09660b10bad61c4e04f62b441f8c200', '2026-04-11 12:33:15'),
(682, 36, 'https://www.motorfix.co.ke/product?id=36', '2add8e7e8455ddc29e610a969409f891c09660b10bad61c4e04f62b441f8c200', '2026-04-11 12:33:19'),
(683, 35, 'https://www.motorfix.co.ke/product?id=35', '2add8e7e8455ddc29e610a969409f891c09660b10bad61c4e04f62b441f8c200', '2026-04-11 12:33:20'),
(684, 34, 'https://www.motorfix.co.ke/product?id=34', '2add8e7e8455ddc29e610a969409f891c09660b10bad61c4e04f62b441f8c200', '2026-04-11 12:33:21'),
(685, 26, 'https://motorfix.co.ke/product?id=26', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-13 05:46:14'),
(686, 19, 'https://motorfix.co.ke/product?id=19', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-13 05:46:14'),
(687, 28, 'https://motorfix.co.ke/product?id=28', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-13 05:46:15'),
(688, 27, 'https://motorfix.co.ke/product?id=27', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-13 05:46:15'),
(689, 29, 'https://motorfix.co.ke/product?id=29', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-13 05:46:16'),
(690, 32, 'https://motorfix.co.ke/product?id=32', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-13 05:46:16'),
(691, 30, 'https://motorfix.co.ke/product?id=30', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-13 05:46:16'),
(692, 31, 'https://motorfix.co.ke/product?id=31', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-13 05:46:16'),
(693, 35, 'https://motorfix.co.ke/product?id=35', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-13 05:46:17'),
(694, 31, 'https://www.motorfix.co.ke/product?id=31', 'ad427d925d03f196582a2b5241aa5ef688a636e759fdbacbefcdda5ee2198a0e', '2026-04-14 08:41:55'),
(695, 34, 'https://www.motorfix.co.ke/product?id=34', 'ad427d925d03f196582a2b5241aa5ef688a636e759fdbacbefcdda5ee2198a0e', '2026-04-14 08:42:06'),
(696, 30, 'https://www.motorfix.co.ke/product?id=30', 'ad427d925d03f196582a2b5241aa5ef688a636e759fdbacbefcdda5ee2198a0e', '2026-04-14 08:42:08'),
(697, 27, 'https://www.motorfix.co.ke/product?id=27', '8b4e6af052dab567e64ec8e4e6410d13650931c0b4594243e468a07c086fb09e', '2026-04-14 08:42:11'),
(698, 36, 'https://motorfix.co.ke/product?id=36', '9050c172ad422b0dc37e25c246b01fa42699c72a3af29d06bed19746a9189de5', '2026-04-14 12:13:40'),
(699, 14, 'https://motorfix.co.ke/product?id=14', '0ef1c06d075e143e7b06d8e715f44bb62176ee27b95f6e537a819465cf929961', '2026-04-16 07:30:03'),
(700, 19, 'https://motorfix.co.ke/product?id=19', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-20 02:16:21'),
(701, 26, 'https://motorfix.co.ke/product?id=26', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-20 02:16:21'),
(702, 28, 'https://motorfix.co.ke/product?id=28', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-20 02:16:22'),
(703, 27, 'https://motorfix.co.ke/product?id=27', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-20 02:16:22'),
(704, 29, 'https://motorfix.co.ke/product?id=29', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-20 02:16:23'),
(705, 30, 'https://motorfix.co.ke/product?id=30', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-20 02:16:23'),
(706, 14, 'https://www.motorfix.co.ke/product?id=14', 'b818a4cc56a244d81bae7483fb12d6c72a38945ca6d6488e1e237c936913b13e', '2026-04-22 07:33:50'),
(707, 26, 'https://www.motorfix.co.ke/product?id=26', '639d6566450b9ed14bb1d7e14e0b7d53f290e27bc07d685fbd7cb5e076ddd918', '2026-04-22 13:31:33'),
(708, 33, 'https://www.motorfix.co.ke/product?id=33', '6e6dfa673b98df3fb29fc67500a6d230fb1fcd3f58653f124b97a79554843854', '2026-04-22 18:02:12'),
(709, 34, 'https://motorfix.co.ke/product?id=34', '6a17efb5234343f73f60ca084265f096116ced461205ab4cb79f410b821cf018', '2026-04-23 20:24:16'),
(710, 27, 'https://motorfix.co.ke/product?id=27', '64374887fb4c22d44d48072db8c08290f8f6363ec980414ed75f1213e6943164', '2026-04-24 18:30:57'),
(711, 33, 'https://motorfix.co.ke/product?id=33', '64374887fb4c22d44d48072db8c08290f8f6363ec980414ed75f1213e6943164', '2026-04-24 18:30:59'),
(712, 26, 'https://motorfix.co.ke/product?id=26', '64374887fb4c22d44d48072db8c08290f8f6363ec980414ed75f1213e6943164', '2026-04-24 18:31:01'),
(713, 29, 'https://motorfix.co.ke/product?id=29', '64374887fb4c22d44d48072db8c08290f8f6363ec980414ed75f1213e6943164', '2026-04-24 18:31:04'),
(714, 30, 'https://motorfix.co.ke/product?id=30', '64374887fb4c22d44d48072db8c08290f8f6363ec980414ed75f1213e6943164', '2026-04-24 18:31:06'),
(715, 35, 'https://motorfix.co.ke/product?id=35', '64374887fb4c22d44d48072db8c08290f8f6363ec980414ed75f1213e6943164', '2026-04-24 18:31:09'),
(716, 34, 'https://motorfix.co.ke/product?id=34', '64374887fb4c22d44d48072db8c08290f8f6363ec980414ed75f1213e6943164', '2026-04-24 18:31:10'),
(717, 19, 'https://motorfix.co.ke/product?id=19', '64374887fb4c22d44d48072db8c08290f8f6363ec980414ed75f1213e6943164', '2026-04-24 18:31:11'),
(718, 32, 'https://motorfix.co.ke/product?id=32', '64374887fb4c22d44d48072db8c08290f8f6363ec980414ed75f1213e6943164', '2026-04-24 18:31:12'),
(719, 31, 'https://motorfix.co.ke/product?id=31', '93c565cf4455408540a87157f27c085808e5f5112871cbf53ea8e4798c748660', '2026-04-24 23:17:42'),
(720, 30, 'https://motorfix.co.ke/product?id=30', 'b226415dea2063edf0ac476584e9bf2208de2019c389e180f7e11da544924875', '2026-04-25 17:55:23'),
(721, 26, 'https://motorfix.co.ke/product?id=26', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-27 14:40:10'),
(722, 19, 'https://motorfix.co.ke/product?id=19', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-27 14:40:10'),
(723, 27, 'https://motorfix.co.ke/product?id=27', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-27 14:40:10'),
(724, 28, 'https://motorfix.co.ke/product?id=28', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-27 14:40:10'),
(725, 29, 'https://motorfix.co.ke/product?id=29', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-27 14:40:11'),
(726, 31, 'https://motorfix.co.ke/product?id=31', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-27 14:40:11'),
(727, 30, 'https://motorfix.co.ke/product?id=30', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-27 14:40:11'),
(728, 32, 'https://motorfix.co.ke/product?id=32', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-27 14:40:11'),
(729, 34, 'https://motorfix.co.ke/product?id=34', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-27 14:40:12'),
(730, 33, 'https://motorfix.co.ke/product?id=33', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-27 14:40:12'),
(731, 35, 'https://motorfix.co.ke/product?id=35', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-27 14:40:12'),
(732, 36, 'https://motorfix.co.ke/product?id=36', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-04-27 14:40:12'),
(733, 28, 'https://motorfix.co.ke/product?id=28', 'd54248cc757a7baa9357b142732452e9c308bdd1a3d26d2de7789ee7955802fc', '2026-04-29 00:09:12'),
(734, 32, 'https://motorfix.co.ke/product?id=32', '5d14751508a08fc9516aca039361f10ccbd574f6a80922948a53c4bfd7a31c66', '2026-05-03 01:16:03'),
(735, 26, 'https://motorfix.co.ke/product?id=26', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-05-05 02:20:47'),
(736, 19, 'https://motorfix.co.ke/product?id=19', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-05-05 02:20:47'),
(737, 27, 'https://motorfix.co.ke/product?id=27', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-05-05 02:20:48'),
(738, 28, 'https://motorfix.co.ke/product?id=28', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-05-05 02:20:48'),
(739, 29, 'https://motorfix.co.ke/product?id=29', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-05-05 02:20:48'),
(740, 30, 'https://motorfix.co.ke/product?id=30', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-05-05 02:20:49'),
(741, 31, 'https://motorfix.co.ke/product?id=31', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-05-05 02:20:49'),
(742, 32, 'https://motorfix.co.ke/product?id=32', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-05-05 02:20:49'),
(743, 34, 'https://motorfix.co.ke/product?id=34', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-05-05 02:20:50'),
(744, 35, 'https://motorfix.co.ke/product?id=35', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-05-05 02:20:50'),
(745, 33, 'https://motorfix.co.ke/product?id=33', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-05-05 02:20:50'),
(746, 36, 'https://motorfix.co.ke/product?id=36', '896d9b2b2818a5452ff5dc04d8196e19df1797858d87cd57479dd29d7fb75938', '2026-05-05 02:20:51'),
(747, 26, 'https://www.motorfix.co.ke/product?id=26', 'c5e8a0c271b1c8dfa30ca5a682fefd315e59eed3d82fbb11253a9688043278af', '2026-05-07 23:32:55'),
(748, 19, 'https://motorfix.co.ke/product?id=19', '4fecf8ee11ed211cade00275f151083bbdba36683e8fd97944a332069dcba3ee', '2026-05-09 08:36:23'),
(749, 32, 'https://motorfix.co.ke/product?id=32', '4fecf8ee11ed211cade00275f151083bbdba36683e8fd97944a332069dcba3ee', '2026-05-09 08:36:23'),
(750, 27, 'https://www.motorfix.co.ke/product?id=27', '8e2dea40dec51df83dfed59506584af293b0642aad2eca90b27107d9330ef740', '2026-05-10 06:37:56'),
(751, 30, 'https://www.motorfix.co.ke/product?id=30', '785980a317c88250c8a93bdf69ca2729af29ee7e818bdf716a73ad3a5e81f865', '2026-05-10 06:38:02'),
(752, 28, 'https://www.motorfix.co.ke/product?id=28', '785980a317c88250c8a93bdf69ca2729af29ee7e818bdf716a73ad3a5e81f865', '2026-05-10 06:38:07'),
(753, 33, 'https://www.motorfix.co.ke/product?id=33', 'a6b7cf905001b4c25f62bdff7b64b341a747abbe39ac524c4e0d1a3a62b73755', '2026-05-10 06:38:47'),
(754, 29, 'https://www.motorfix.co.ke/product?id=29', '8e2dea40dec51df83dfed59506584af293b0642aad2eca90b27107d9330ef740', '2026-05-10 06:39:01'),
(755, 35, 'https://www.motorfix.co.ke/product?id=35', '8e2dea40dec51df83dfed59506584af293b0642aad2eca90b27107d9330ef740', '2026-05-10 06:39:03'),
(756, 26, 'https://www.motorfix.co.ke/product?id=26', '8e2dea40dec51df83dfed59506584af293b0642aad2eca90b27107d9330ef740', '2026-05-10 06:39:21'),
(757, 30, 'https://www.motorfix.co.ke/product?id=30', '785980a317c88250c8a93bdf69ca2729af29ee7e818bdf716a73ad3a5e81f865', '2026-05-10 06:39:48'),
(758, 31, 'https://www.motorfix.co.ke/product?id=31', '785980a317c88250c8a93bdf69ca2729af29ee7e818bdf716a73ad3a5e81f865', '2026-05-10 06:39:48'),
(759, 34, 'https://www.motorfix.co.ke/product?id=34', 'a6b7cf905001b4c25f62bdff7b64b341a747abbe39ac524c4e0d1a3a62b73755', '2026-05-10 06:39:50'),
(760, 33, 'https://www.motorfix.co.ke/product?id=33', '8e2dea40dec51df83dfed59506584af293b0642aad2eca90b27107d9330ef740', '2026-05-10 06:39:51');
INSERT INTO `page_views` (`id`, `product_id`, `url`, `visitor_identifier`, `view_timestamp`) VALUES
(761, 27, 'https://www.motorfix.co.ke/product?id=27', '8e2dea40dec51df83dfed59506584af293b0642aad2eca90b27107d9330ef740', '2026-05-10 06:39:53'),
(762, 35, 'https://www.motorfix.co.ke/product?id=35', '8e2dea40dec51df83dfed59506584af293b0642aad2eca90b27107d9330ef740', '2026-05-10 06:39:55'),
(763, 29, 'https://www.motorfix.co.ke/product?id=29', '8e2dea40dec51df83dfed59506584af293b0642aad2eca90b27107d9330ef740', '2026-05-10 06:39:55'),
(764, 26, 'https://www.motorfix.co.ke/product?id=26', '8e2dea40dec51df83dfed59506584af293b0642aad2eca90b27107d9330ef740', '2026-05-10 06:39:57'),
(765, 28, 'https://www.motorfix.co.ke/product?id=28', '785980a317c88250c8a93bdf69ca2729af29ee7e818bdf716a73ad3a5e81f865', '2026-05-10 06:40:08'),
(766, 32, 'https://www.motorfix.co.ke/product?id=32', 'cf3cf4874d990d25bfe15feaf669632bf0c6c6eb600c41550ed42681de763cd2', '2026-05-10 06:41:59'),
(767, 32, 'https://www.motorfix.co.ke/product?id=32', '785980a317c88250c8a93bdf69ca2729af29ee7e818bdf716a73ad3a5e81f865', '2026-05-10 06:42:26'),
(768, 29, 'https://www.motorfix.co.ke/product?id=29', '785980a317c88250c8a93bdf69ca2729af29ee7e818bdf716a73ad3a5e81f865', '2026-05-10 06:42:27'),
(769, 26, 'https://www.motorfix.co.ke/product?id=26', '785980a317c88250c8a93bdf69ca2729af29ee7e818bdf716a73ad3a5e81f865', '2026-05-10 06:42:28'),
(770, 33, 'https://www.motorfix.co.ke/product?id=33', '8e2dea40dec51df83dfed59506584af293b0642aad2eca90b27107d9330ef740', '2026-05-10 06:42:32'),
(771, 27, 'https://www.motorfix.co.ke/product?id=27', 'cf3cf4874d990d25bfe15feaf669632bf0c6c6eb600c41550ed42681de763cd2', '2026-05-10 06:42:32'),
(772, 35, 'https://www.motorfix.co.ke/product?id=35', '8e2dea40dec51df83dfed59506584af293b0642aad2eca90b27107d9330ef740', '2026-05-10 06:42:37'),
(773, 34, 'https://www.motorfix.co.ke/product?id=34', 'a6b7cf905001b4c25f62bdff7b64b341a747abbe39ac524c4e0d1a3a62b73755', '2026-05-10 06:42:38'),
(774, 28, 'https://www.motorfix.co.ke/product?id=28', 'cf3cf4874d990d25bfe15feaf669632bf0c6c6eb600c41550ed42681de763cd2', '2026-05-10 06:42:39'),
(775, 30, 'https://www.motorfix.co.ke/product?id=30', 'a6b7cf905001b4c25f62bdff7b64b341a747abbe39ac524c4e0d1a3a62b73755', '2026-05-10 06:42:41'),
(776, 31, 'https://motorfix.co.ke/product?id=31', 'e76f7153bb949ccc593e7838547c15844a7914a805533cd7061ac20c82297fb2', '2026-05-11 05:31:41'),
(777, 33, 'https://motorfix.co.ke/product?id=33', '2eca840946ddc4cef9f528bf6348763a0375d6895b1e33f8561c49028b435ad2', '2026-05-11 05:31:45'),
(778, 36, 'https://motorfix.co.ke/product?id=36', '2eca840946ddc4cef9f528bf6348763a0375d6895b1e33f8561c49028b435ad2', '2026-05-11 05:31:47'),
(779, 30, 'https://motorfix.co.ke/product?id=30', '2a38e21e46fe5d1d5d97dca0b760de8988c59eed78a55d5280ed335fa97b735d', '2026-05-11 05:31:47'),
(780, 32, 'https://motorfix.co.ke/product?id=32', 'e76f7153bb949ccc593e7838547c15844a7914a805533cd7061ac20c82297fb2', '2026-05-11 05:31:50'),
(781, 26, 'https://motorfix.co.ke/product?id=26', 'e76f7153bb949ccc593e7838547c15844a7914a805533cd7061ac20c82297fb2', '2026-05-11 05:31:50'),
(782, 29, 'https://motorfix.co.ke/product?id=29', '2a38e21e46fe5d1d5d97dca0b760de8988c59eed78a55d5280ed335fa97b735d', '2026-05-11 05:31:53'),
(783, 35, 'https://motorfix.co.ke/product?id=35', '2a38e21e46fe5d1d5d97dca0b760de8988c59eed78a55d5280ed335fa97b735d', '2026-05-11 05:32:02'),
(784, 27, 'https://motorfix.co.ke/product?id=27', '91fe5d1ae078b81adecd33ab656ce03f537b38bee35dac2a54ffa841c7951c8f', '2026-05-11 05:32:05'),
(785, 34, 'https://motorfix.co.ke/product?id=34', '91fe5d1ae078b81adecd33ab656ce03f537b38bee35dac2a54ffa841c7951c8f', '2026-05-11 05:32:11'),
(786, 28, 'https://motorfix.co.ke/product?id=28', '91fe5d1ae078b81adecd33ab656ce03f537b38bee35dac2a54ffa841c7951c8f', '2026-05-11 05:32:13'),
(787, 26, 'https://motorfix.co.ke/product?id=26', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-05-12 04:29:15'),
(788, 19, 'https://motorfix.co.ke/product?id=19', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-05-12 04:29:15'),
(789, 27, 'https://motorfix.co.ke/product?id=27', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-05-12 04:29:16'),
(790, 28, 'https://motorfix.co.ke/product?id=28', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-05-12 04:29:16'),
(791, 29, 'https://motorfix.co.ke/product?id=29', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-05-12 04:29:16'),
(792, 30, 'https://motorfix.co.ke/product?id=30', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-05-12 04:29:17'),
(793, 32, 'https://motorfix.co.ke/product?id=32', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-05-12 04:29:17'),
(794, 35, 'https://motorfix.co.ke/product?id=35', '3291f81127f2ce2e1ef70876945e3f4ff3a1ce8ebacd8f6a01313ab9bf859707', '2026-05-12 04:29:18'),
(795, 31, 'https://www.motorfix.co.ke/product?id=31', '4efece8cf261063c383254fdd0a858ad7494b23bd22bd45760c72b44c8a2e343', '2026-05-12 09:25:58'),
(796, 36, 'https://www.motorfix.co.ke/product?id=36', '4efece8cf261063c383254fdd0a858ad7494b23bd22bd45760c72b44c8a2e343', '2026-05-12 09:26:02'),
(797, 35, 'https://www.motorfix.co.ke/product?id=35', '4efece8cf261063c383254fdd0a858ad7494b23bd22bd45760c72b44c8a2e343', '2026-05-12 09:26:03'),
(798, 34, 'https://www.motorfix.co.ke/product?id=34', '4efece8cf261063c383254fdd0a858ad7494b23bd22bd45760c72b44c8a2e343', '2026-05-12 09:26:04'),
(799, 28, 'https://motorfix.co.ke/product?id=28', 'f4d3921d1931f6cca77eacb00acde58f3cc782bbee95fd23fbfb564fb8e453aa', '2026-05-15 10:54:50'),
(800, 33, 'https://motorfix.co.ke/product?id=33', 'f4d3921d1931f6cca77eacb00acde58f3cc782bbee95fd23fbfb564fb8e453aa', '2026-05-15 11:23:28');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `purpose` enum('setup','reset') NOT NULL COMMENT 'Distinguishes between new account setup and password reset.',
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `purpose`, `expires_at`, `created_at`) VALUES
(1, 'leonard@thealaikagroup.org', 'e5b33cbfbe096cd453c214342aca201b326792d8d3d86c043caddeca526c72aa', 'reset', '2025-10-14 07:31:01', '2025-10-14 12:01:01'),
(2, 'leonardmuia89@gmail.com', 'dcafdab708d0b596b560e8c9925162d9173c6ea46360a08469c1bf7da8506f12', 'setup', '2025-10-14 07:34:04', '2025-10-14 12:04:04'),
(3, 'leonardmuia89@gmail.com', '968a98332d2d71059bce7445031ee2b6341ae0bbee3a4ff5f8388090c03115f3', 'setup', '2025-10-14 08:00:27', '2025-10-14 12:30:27'),
(4, 'leonard@thealaikagroup.org', '67812d554c466ebf00d244b4f096711af9913611be78c2ce2dd37c2b777a8af1', 'reset', '2025-10-14 08:02:02', '2025-10-14 12:32:02'),
(8, 'mufe465@gmail.com', 'bfe7b6bacd3c6d61af257aa3be9770622dde92c9ed7c20384cf5fa761238b948', 'setup', '2025-10-23 10:44:30', '2025-10-23 09:44:30'),
(9, 'mufe465@gmail.com', '4b7c28bcbb3f314880c7420f4153963bd0c2924dccd648c51aa4dbddd958f240', 'setup', '2025-10-23 10:46:35', '2025-10-23 09:46:35'),
(11, 'mufe465@gmail.com', '8a429dcc62b5acc93906a9a5483f8a697708f24fdf8f522b846dbafe7898795a', 'setup', '2025-10-23 11:02:31', '2025-10-23 10:02:31'),
(12, 'tainyafelz@gmail.com', '14e0dff4624b981de40cab051170f9fd714803c417a50f13ce25ba9a6421ed90', 'setup', '2025-10-23 12:22:18', '2025-10-23 11:22:18'),
(13, 'tainyafelz@gmail.com', 'da739928ce30f12b1c6a5f6344aa29d8569e5c4ad9b514d873134902625c3f27', 'setup', '2025-10-23 15:09:00', '2025-10-23 14:09:00'),
(14, 'mufe465@gmail.com', '656892b0dcd2e6d75187138244fe08c7ac28146cf530fd4987ff4247beb68eda', 'reset', '2025-10-23 16:02:52', '2025-10-23 15:02:52');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `manufacturer_part_number` varchar(100) DEFAULT NULL COMMENT 'Optional part number for warranty/identification',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `sku`, `name`, `description`, `manufacturer_part_number`, `is_active`, `created_at`, `updated_at`) VALUES
(13, 12, NULL, 'Inline injector pumps', '', NULL, 0, '2025-10-27 16:49:39', '2025-10-29 17:00:30'),
(14, 12, NULL, 'Diesel fuel head rotors', '', NULL, 1, '2025-10-27 16:53:42', '2025-10-29 17:47:56'),
(15, 12, NULL, 'Fuel injector common rail', '', NULL, 0, '2025-10-27 16:54:10', '2025-10-29 16:58:39'),
(16, 12, NULL, 'Injector sleeves/cups', '', NULL, 0, '2025-10-27 16:54:49', '2025-10-29 17:00:19'),
(17, 12, NULL, 'Oil seal', '', NULL, 0, '2025-10-27 16:55:15', '2025-10-29 17:01:26'),
(18, 12, NULL, 'High-Pressure Common Rail (HPCR) fuel pump', '', NULL, 0, '2025-10-27 16:55:43', '2025-10-29 16:58:54'),
(19, 12, NULL, 'Diesel Shut-Off Solenoid Valves', '', NULL, 1, '2025-10-27 16:58:31', '2025-10-29 17:48:04'),
(20, 12, NULL, 'Distributor Rotor / Plunger', '', NULL, 0, '2025-10-27 16:59:18', '2025-10-29 16:59:29'),
(21, 12, NULL, 'Fuel Injection pumps', '', NULL, 0, '2025-10-27 17:01:10', '2025-10-29 16:58:02'),
(22, 12, NULL, 'Distributor injector pumps', '', NULL, 0, '2025-10-27 17:02:45', '2025-10-29 16:57:38'),
(23, 12, NULL, 'Common Rail injector pumps', '', NULL, 0, '2025-10-27 17:03:25', '2025-10-29 16:56:57'),
(24, 12, NULL, 'Nozzles', '', NULL, 0, '2025-10-27 17:04:31', '2025-10-29 17:01:08'),
(25, 12, NULL, 'Unit pumps', '', NULL, 0, '2025-10-27 17:04:59', '2025-10-29 17:01:57'),
(26, 12, NULL, 'Plunger elements', '', NULL, 1, '2025-10-27 17:05:25', '2025-10-27 17:05:25'),
(27, 12, NULL, 'Hand primers', '', NULL, 1, '2025-10-27 17:06:04', '2025-10-27 17:06:04'),
(28, 12, NULL, 'Gasket kits', '', NULL, 1, '2025-10-27 17:06:31', '2025-10-27 17:06:31'),
(29, 12, NULL, 'Block elements', '', NULL, 1, '2025-10-27 17:07:07', '2025-10-27 17:07:07'),
(30, 12, NULL, 'Nozzle testers', '', NULL, 1, '2025-10-27 17:07:35', '2025-10-27 17:07:35'),
(31, 12, NULL, 'Delivery valves', '', NULL, 1, '2025-10-27 17:07:59', '2025-10-27 17:07:59'),
(32, 12, NULL, 'Timing pistons', '', NULL, 1, '2025-10-27 17:10:35', '2025-10-29 17:48:35'),
(33, 12, NULL, 'Pre-supply pumps', '', NULL, 1, '2025-10-27 17:11:55', '2025-10-29 17:48:26'),
(34, 12, NULL, 'CRV machines', '', NULL, 1, '2025-10-27 17:13:50', '2025-10-29 17:47:39'),
(35, 12, NULL, 'Drive shafts', '', NULL, 1, '2025-10-27 17:15:39', '2025-10-29 17:48:12'),
(36, 12, NULL, 'Diesel fuel injection pump test bench', '', NULL, 1, '2025-10-30 19:18:23', '2025-10-30 19:18:23');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `description`, `created_at`) VALUES
(4, 'CRV machine', '', '2025-10-20 12:45:16'),
(6, 'other', '', '2025-10-21 05:37:13'),
(8, 'Tools', NULL, '2025-10-23 11:29:46'),
(9, 'Screws', '', '2025-10-23 12:25:21'),
(12, 'Product', '', '2025-10-27 16:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL COMMENT 'Path to the image file in /media/products/',
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_primary`, `created_at`) VALUES
(19, 13, 'prod_68ffa2a322751_1761583779.jpeg', 1, '2025-10-27 16:49:39'),
(20, 14, 'prod_68ffa39662129_1761584022.jpeg', 1, '2025-10-27 16:53:42'),
(21, 15, 'prod_68ffa3b22132a_1761584050.jpeg', 1, '2025-10-27 16:54:10'),
(22, 16, 'prod_68ffa3d97313a_1761584089.jpeg', 1, '2025-10-27 16:54:49'),
(23, 17, 'prod_68ffa3f342633_1761584115.jpeg', 1, '2025-10-27 16:55:15'),
(24, 18, 'prod_68ffa40fd17d1_1761584143.jpeg', 1, '2025-10-27 16:55:43'),
(25, 19, 'prod_68ffa4b7bca62_1761584311.jpeg', 1, '2025-10-27 16:58:31'),
(26, 20, 'prod_68ffa4e61bc09_1761584358.jpeg', 1, '2025-10-27 16:59:18'),
(27, 21, 'prod_68ffa5568a221_1761584470.jpeg', 1, '2025-10-27 17:01:10'),
(28, 22, 'prod_68ffa5b5df806_1761584565.jpeg', 1, '2025-10-27 17:02:45'),
(29, 23, 'prod_68ffa5dd5400d_1761584605.jpeg', 1, '2025-10-27 17:03:25'),
(30, 24, 'prod_68ffa61fa3ea6_1761584671.jpeg', 1, '2025-10-27 17:04:31'),
(31, 25, 'prod_68ffa63bb789d_1761584699.jpeg', 1, '2025-10-27 17:04:59'),
(35, 29, 'prod_68ffa6bbcd203_1761584827.jpeg', 1, '2025-10-27 17:07:07'),
(38, 32, 'prod_68ffa78b395de_1761585035.jpg', 1, '2025-10-27 17:10:35'),
(41, 35, 'prod_68ffa8bba6153_1761585339.jpg', 1, '2025-10-27 17:15:39'),
(42, 27, 'prod_68ffa9ad0dc3a_1761585581.jpeg', 0, '2025-10-27 17:19:41'),
(43, 34, 'prod_68ffaa5eb0ab5_1761585758.png', 0, '2025-10-27 17:22:38'),
(44, 30, 'prod_68ffab5f0cd79_1761586015.jpg', 0, '2025-10-27 17:26:55'),
(45, 26, 'prod_68ffabe6d82f1_1761586150.jpeg', 0, '2025-10-27 17:29:10'),
(47, 36, 'prod_6903b9ffbfd23_1761851903.png', 1, '2025-10-30 19:18:23'),
(48, 33, 'prod_6903c01c20bfe_1761853468.png', 0, '2025-10-30 19:44:28'),
(49, 28, 'prod_6903c0aad0d7c_1761853610.png', 0, '2025-10-30 19:46:50'),
(50, 31, 'prod_6903c1623b82b_1761853794.jpeg', 0, '2025-10-30 19:49:54');

-- --------------------------------------------------------

--
-- Table structure for table `product_vehicle_compatibility`
--

CREATE TABLE `product_vehicle_compatibility` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `model_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(2, 'admin'),
(3, 'customer_care'),
(1, 'super_admin');

-- --------------------------------------------------------

--
-- Table structure for table `sales_records`
--

CREATE TABLE `sales_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'Employee who made the sale',
  `total_amount` decimal(10,2) NOT NULL,
  `amount_paid_cash` decimal(10,2) DEFAULT 0.00,
  `amount_paid_mobile` decimal(10,2) DEFAULT 0.00,
  `sale_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sales_records`
--

INSERT INTO `sales_records` (`id`, `branch_id`, `user_id`, `total_amount`, `amount_paid_cash`, `amount_paid_mobile`, `sale_timestamp`) VALUES
(2, 1, 4, 600.00, 600.00, 0.00, '2025-10-16 19:10:41'),
(3, 1, 4, 2400.00, 0.00, 2400.00, '2025-10-17 05:25:15'),
(4, 1, 4, 70000.00, 70000.00, 0.00, '2025-10-19 15:34:47'),
(5, 1, 4, 800.00, 0.00, 800.00, '2025-10-19 15:35:05');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `quantity_sold` int(11) NOT NULL,
  `selling_price_at_sale` decimal(10,2) NOT NULL COMMENT 'Price per item at the moment of sale',
  `purchase_price_at_sale` decimal(10,2) NOT NULL COMMENT 'Cost price at moment of sale for accurate profit calculation'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `branch_id` int(10) UNSIGNED NOT NULL COMMENT 'The branch where the activity occurred',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'Employee who initiated the movement',
  `quantity_change` int(11) NOT NULL COMMENT 'Positive for additions, negative for deductions',
  `movement_type` enum('initial_stock','new_purchase','sale','transfer_out','transfer_in','return','adjustment') NOT NULL,
  `notes` text DEFAULT NULL COMMENT 'E.g., Transfer to Nairobi Branch, Stocktaking adjustment',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` tinyint(3) UNSIGNED NOT NULL,
  `branch_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Branch the employee is assigned to. NULL for super_admin.',
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `online_status` tinyint(1) DEFAULT 0,
  `last_login_timestamp` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `branch_id`, `name`, `email`, `password_hash`, `is_active`, `online_status`, `last_login_timestamp`, `created_at`) VALUES
(1, 1, NULL, 'Leonard Muia', 'leonard@thealaikagroup.org', '$argon2id$v=19$m=65536,t=4,p=1$NkJSTXQ2amt5QlZ6dHF5NA$j+krkQgONLF5wBkDfhV7CJQZP5t2ABzwDJ6/kviJQUM', 1, 1, '2025-10-29 12:04:44', '2025-10-13 15:30:55'),
(4, 2, 1, 'Leon', 'mufe465@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$Sm5wcDBOcDZUSHFPSnozRQ$6cUtS6PHvMrct5XRnTlHf/s0Zag4Bg66E6vWLf5cwpc', 1, 1, '2025-10-30 19:17:51', '2025-10-14 13:30:50'),
(5, 3, NULL, 'Leon Care', 'lennytishay@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$OC4wZnNIUHdYVXI0NlhLcA$L4pnWsdpAuddjD0Ram+s4VGQfQb2msX9HiwQ8h0hAMI', 1, 0, '2025-10-22 18:12:42', '2025-10-15 09:33:09'),
(11, 2, 1, 'Invited User', 'tainyafelz@gmail.com', '', 0, 0, NULL, '2025-10-23 14:09:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `blog_media`
--
ALTER TABLE `blog_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `car_makes`
--
ALTER TABLE `car_makes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `car_models`
--
ALTER TABLE `car_models`
  ADD PRIMARY KEY (`id`),
  ADD KEY `make_id` (`make_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status_assigned_user` (`status`,`assigned_user_id`),
  ADD KEY `assigned_user_id` (`assigned_user_id`);

--
-- Indexes for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_records`
--
ALTER TABLE `expense_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_product_branch` (`product_id`,`branch_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `logged_in_devices`
--
ALTER TABLE `logged_in_devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_id` (`session_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_remember_token` (`remember_token`);

--
-- Indexes for table `page_views`
--
ALTER TABLE `page_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_url_visitor` (`url`,`visitor_identifier`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_email_purpose` (`email`,`purpose`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_name` (`name`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_vehicle_compatibility`
--
ALTER TABLE `product_vehicle_compatibility`
  ADD PRIMARY KEY (`product_id`,`model_id`),
  ADD KEY `model_id` (`model_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `sales_records`
--
ALTER TABLE `sales_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `stock_movements_ibfk_1` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `blog_media`
--
ALTER TABLE `blog_media`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `car_makes`
--
ALTER TABLE `car_makes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `car_models`
--
ALTER TABLE `car_models`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `expense_records`
--
ALTER TABLE `expense_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `logged_in_devices`
--
ALTER TABLE `logged_in_devices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `page_views`
--
ALTER TABLE `page_views`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=801;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sales_records`
--
ALTER TABLE `sales_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `blog_media`
--
ALTER TABLE `blog_media`
  ADD CONSTRAINT `blog_media_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `blog_media_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `car_models`
--
ALTER TABLE `car_models`
  ADD CONSTRAINT `car_models_ibfk_1` FOREIGN KEY (`make_id`) REFERENCES `car_makes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `chat_sessions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  ADD CONSTRAINT `chat_sessions_ibfk_1` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `expense_records`
--
ALTER TABLE `expense_records`
  ADD CONSTRAINT `expense_records_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `expense_records_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `logged_in_devices`
--
ALTER TABLE `logged_in_devices`
  ADD CONSTRAINT `logged_in_devices_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `page_views`
--
ALTER TABLE `page_views`
  ADD CONSTRAINT `page_views_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_vehicle_compatibility`
--
ALTER TABLE `product_vehicle_compatibility`
  ADD CONSTRAINT `product_vehicle_compatibility_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_vehicle_compatibility_ibfk_2` FOREIGN KEY (`model_id`) REFERENCES `car_models` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sales_records`
--
ALTER TABLE `sales_records`
  ADD CONSTRAINT `sales_records_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sales_records_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales_records` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_movements_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_movements_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
