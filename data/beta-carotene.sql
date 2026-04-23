-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2026-04-23 06:08:52
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `beta-carotene`
--
CREATE DATABASE IF NOT EXISTS `beta-carotene` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `beta-carotene`;

-- --------------------------------------------------------

--
-- テーブルの構造 `carcon_lines`
--

CREATE TABLE `carcon_lines` (
  `id` int(11) NOT NULL,
  `classroom_id` int(11) DEFAULT NULL,
  `carcon_staff_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `carcon_lines`
--

INSERT INTO `carcon_lines` (`id`, `classroom_id`, `carcon_staff_id`, `date`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-04-18', '2026-04-13 16:08:32', '2026-04-13 16:08:32'),
(2, 2, NULL, '2026-04-18', '2026-04-13 16:08:32', '2026-04-13 16:08:32'),
(3, 3, 2, '2026-04-18', '2026-04-13 16:08:32', '2026-04-13 16:08:32'),
(4, 1, 3, '2026-04-25', '2026-04-13 16:08:32', '2026-04-13 16:08:32'),
(5, 2, 4, '2026-04-25', '2026-04-13 16:08:32', '2026-04-13 16:08:32'),
(6, 3, NULL, '2026-04-25', '2026-04-13 16:08:32', '2026-04-13 16:08:32');

-- --------------------------------------------------------

--
-- テーブルの構造 `carcon_request_reservations`
--

CREATE TABLE `carcon_request_reservations` (
  `id` int(11) NOT NULL,
  `request_carcon_reservation_detail_id` int(11) NOT NULL,
  `change_carcon_reservation_detail_id` int(11) DEFAULT NULL,
  `request_meeting_type` int(11) DEFAULT NULL,
  `change_meeting_type` int(11) DEFAULT NULL,
  `request_status_id` int(11) NOT NULL,
  `request_type` int(4) NOT NULL DEFAULT 0 COMMENT '0: 変更申請 1: キャンセル申請',
  `reject_message` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `carcon_request_reservations`
--

INSERT INTO `carcon_request_reservations` (`id`, `request_carcon_reservation_detail_id`, `change_carcon_reservation_detail_id`, `request_meeting_type`, `change_meeting_type`, `request_status_id`, `request_type`, `reject_message`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, 1, 1, 0, NULL, '2026-04-13 16:04:44', '2026-04-13 16:04:44'),
(2, 3, 4, NULL, NULL, 1, 0, NULL, '2026-04-13 16:04:44', '2026-04-13 16:04:44'),
(3, 5, NULL, 2, NULL, 2, 0, NULL, '2026-04-13 16:04:44', '2026-04-17 16:10:32'),
(4, 6, NULL, 1, NULL, 1, 0, NULL, '2026-04-15 11:35:37', '2026-04-15 11:35:37'),
(5, 7, NULL, 2, NULL, 2, 0, NULL, '2026-04-15 11:35:58', '2026-04-15 11:35:58'),
(6, 8, NULL, 2, NULL, 3, 0, NULL, '2026-04-15 11:36:13', '2026-04-15 11:36:13'),
(7, 9, NULL, NULL, NULL, 1, 1, NULL, '2026-04-20 11:52:16', '2026-04-20 11:52:16');

-- --------------------------------------------------------

--
-- テーブルの構造 `carcon_reservations`
--

CREATE TABLE `carcon_reservations` (
  `id` int(11) NOT NULL,
  `carcon_reservation_detail_id` int(11) NOT NULL,
  `carcon_line_id` int(11) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `carcon_reservations`
--

INSERT INTO `carcon_reservations` (`id`, `carcon_reservation_detail_id`, `carcon_line_id`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0, '2026-04-13 16:11:38', '2026-04-13 16:11:38'),
(2, 2, 1, 0, '2026-04-13 16:11:38', '2026-04-13 16:11:38'),
(3, 3, 1, 0, '2026-04-13 16:11:38', '2026-04-13 16:11:38'),
(4, 4, 1, 0, '2026-04-13 16:11:38', '2026-04-13 16:11:38'),
(5, 5, 1, 0, '2026-04-13 16:11:38', '2026-04-13 16:11:38'),
(6, 6, 2, 0, '2026-04-13 16:11:38', '2026-04-13 16:11:38'),
(7, 7, 2, 0, '2026-04-13 16:11:38', '2026-04-13 16:11:38'),
(8, 8, 3, 0, '2026-04-13 16:11:38', '2026-04-13 16:11:38'),
(9, 9, 3, 0, '2026-04-13 16:11:38', '2026-04-13 16:11:38'),
(10, 10, 3, 0, '2026-04-13 16:11:38', '2026-04-13 16:11:38');

-- --------------------------------------------------------

--
-- テーブルの構造 `carcon_reservation_details`
--

CREATE TABLE `carcon_reservation_details` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `meeting_type` int(11) NOT NULL,
  `meeting_url` varchar(255) NOT NULL,
  `meeting_id` varchar(255) NOT NULL,
  `meeting_passcode` varchar(255) NOT NULL,
  `slot_index` int(4) NOT NULL,
  `is_plus_carcon` int(4) NOT NULL DEFAULT 0 COMMENT '0: 必須キャリコン 1: キャリコンプラス',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `carcon_reservation_details`
--

INSERT INTO `carcon_reservation_details` (`id`, `student_id`, `meeting_type`, `meeting_url`, `meeting_id`, `meeting_passcode`, `slot_index`, `is_plus_carcon`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'https://www.google.com', '000 0000 000', '000000', 0, 0, '2026-04-13 15:59:30', '2026-04-13 15:59:30'),
(2, 2, 2, 'https://www.google.com', '000 0000 000', '000000', 1, 0, '2026-04-13 15:59:30', '2026-04-13 15:59:30'),
(3, 3, 1, 'https://www.google.com', '000 0000 000', '000000', 3, 0, '2026-04-13 15:59:30', '2026-04-13 15:59:30'),
(4, 4, 1, 'https://www.google.com', '000 0000 000', '000000', 4, 0, '2026-04-13 15:59:30', '2026-04-13 15:59:30'),
(5, 5, 2, 'https://www.google.com', '000 0000 000', '000000', 5, 0, '2026-04-13 15:59:30', '2026-04-17 16:10:32'),
(6, 20, 1, 'https://www.google.com', '000 0000 000', '000000', 0, 0, '2026-04-13 15:59:30', '2026-04-13 15:59:30'),
(7, 21, 1, 'https://www.google.com', '000 0000 000', '000000', 1, 0, '2026-04-13 15:59:30', '2026-04-13 15:59:30'),
(8, 22, 2, 'https://www.google.com', '000 0000 000', '000000', 2, 0, '2026-04-13 15:59:30', '2026-04-13 15:59:30'),
(9, 23, 1, 'https://www.google.com', '000 0000 000', '000000', 3, 0, '2026-04-13 15:59:30', '2026-04-13 15:59:30'),
(10, 24, 2, 'https://www.google.com', '000 0000 000', '000000', 4, 0, '2026-04-13 15:59:30', '2026-04-13 15:59:30');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_admin_staffs`
--

CREATE TABLE `m_admin_staffs` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_admin_staffs`
--

INSERT INTO `m_admin_staffs` (`id`, `staff_id`, `last_name`, `first_name`, `password`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 'admin', '管理', '太郎', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 0, '2026-04-13 15:41:53', '2026-04-23 13:07:29');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_carcon_staffs`
--

CREATE TABLE `m_carcon_staffs` (
  `id` int(11) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_carcon_staffs`
--

INSERT INTO `m_carcon_staffs` (`id`, `last_name`, `first_name`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, '田中', '太郎', 0, '2026-04-13 15:40:23', '2026-04-16 15:05:13'),
(2, '鈴木', '太郎', 0, '2026-04-13 15:40:23', '2026-04-16 15:05:16'),
(3, '佐藤', '花子', 0, '2026-04-13 15:40:23', '2026-04-16 15:05:22'),
(4, '高橋', '花子', 0, '2026-04-13 15:40:23', '2026-04-16 15:05:23');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_classrooms`
--

CREATE TABLE `m_classrooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_classrooms`
--

INSERT INTO `m_classrooms` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '6A', '2026-04-13 15:38:31', '2026-04-13 15:38:31'),
(2, '6B', '2026-04-13 15:38:31', '2026-04-13 15:38:31'),
(3, '6C', '2026-04-13 15:38:31', '2026-04-13 15:38:31'),
(4, '6D', '2026-04-13 15:38:31', '2026-04-13 15:38:31'),
(5, '6E', '2026-04-13 15:38:31', '2026-04-13 15:38:31'),
(6, '7A', '2026-04-13 15:38:31', '2026-04-13 15:38:31'),
(7, '7B', '2026-04-13 15:38:31', '2026-04-13 15:38:31'),
(8, '7C', '2026-04-13 15:38:31', '2026-04-13 15:38:31'),
(9, '7D', '2026-04-13 15:38:31', '2026-04-13 15:38:31'),
(10, '7E', '2026-04-13 15:38:31', '2026-04-13 15:38:31');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_courses`
--

CREATE TABLE `m_courses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `course_type` int(11) NOT NULL,
  `classroom_id` int(11) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_courses`
--

INSERT INTO `m_courses` (`id`, `name`, `start_date`, `end_date`, `course_type`, `classroom_id`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 'WEBプログラミング', '2025-11-01', '2026-04-30', 1, 1, 0, '2026-04-13 15:35:45', '2026-04-15 13:02:21'),
(2, 'JAVAプログラミング', '2026-02-01', '2026-06-30', 2, 2, 0, '2026-04-13 15:35:45', '2026-04-15 13:02:24'),
(3, 'WEBデザイン', '2026-05-01', '2026-08-31', 1, 3, 0, '2026-04-13 15:35:45', '2026-04-15 13:02:26');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_course_types`
--

CREATE TABLE `m_course_types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_course_types`
--

INSERT INTO `m_course_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '公共職業訓練', '2026-04-13 15:23:48', '2026-04-13 16:38:04'),
(2, '求職者支援訓練', '2026-04-13 15:23:48', '2026-04-13 15:24:01');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_enrollments`
--

CREATE TABLE `m_enrollments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_enrollments`
--

INSERT INTO `m_enrollments` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '在校中', '2026-04-13 15:29:58', '2026-04-13 15:29:58'),
(2, '中退', '2026-04-13 15:29:58', '2026-04-13 15:29:58'),
(3, '支援中OB', '2026-04-13 15:29:58', '2026-04-13 15:29:58'),
(4, '支援不要OB', '2026-04-13 15:29:58', '2026-04-13 15:29:58');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_meeting_types`
--

CREATE TABLE `m_meeting_types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_meeting_types`
--

INSERT INTO `m_meeting_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '対面', '2026-04-13 15:27:12', '2026-04-13 15:27:12'),
(2, 'ZOOM', '2026-04-13 15:27:12', '2026-04-13 15:27:12');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_request_statuses`
--

CREATE TABLE `m_request_statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_request_statuses`
--

INSERT INTO `m_request_statuses` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '申請中', '2026-04-13 15:25:18', '2026-04-13 15:25:18'),
(2, '承認済み', '2026-04-13 15:25:18', '2026-04-13 15:25:18'),
(3, '棄却', '2026-04-13 15:25:18', '2026-04-13 15:25:18');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_students`
--

CREATE TABLE `m_students` (
  `id` int(11) NOT NULL,
  `student_no` int(11) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `login_id` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_students`
--

INSERT INTO `m_students` (`id`, `student_no`, `last_name`, `first_name`, `login_id`, `password`, `course_id`, `enrollment_id`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 1, '工藤', '新一', '2025116A1', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 1, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:02:44'),
(2, 2, '毛利', '蘭', '2025116A2', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 1, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:02:49'),
(3, 3, '江戸川', 'コナン', '2025116A3', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 1, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:03:03'),
(4, 4, '孫', '悟空', '2025116A4', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 1, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:03:08'),
(5, 5, '野比', 'のび太', '2025116A5', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 1, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:03:11'),
(6, 6, '月野', 'うさぎ', '2025116A6', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 1, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:03:14'),
(7, 7, 'うずまき', 'ナルト', '2025116A7', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 1, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:03:17'),
(8, 8, 'モンキー', 'ルフィ', '2025116A8', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 1, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:03:21'),
(9, 9, '竈門', '炭治郎', '2025116A9', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 1, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:03:24'),
(10, 10, '五条', '悟', '2025116A10', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 1, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:00:33'),
(11, 11, '綾波', 'レイ', '2025116A11', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 1, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:00:36'),
(12, 12, '碇', 'シンジ', '2025116A12', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 1, 2, 0, '2026-04-13 15:51:57', '2026-04-23 13:00:38'),
(13, 1, '阿部', '寛', '2026026B13', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 2, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:03:33'),
(14, 2, '新垣', '結衣', '2026026B14', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 2, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:03:38'),
(15, 3, '福山', '雅治', '2026026B15', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 2, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:03:43'),
(16, 4, '星野', '源', '2026026B16', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 2, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:03:58'),
(17, 5, '橋本', '環奈', '2026026B17', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 2, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:04:02'),
(18, 6, '大谷', '翔平', '2026026B18', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 2, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:04:06'),
(19, 7, '浜辺', '美波', '2026026B19', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 2, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:04:10'),
(20, 8, '菅田', '将暉', '2026026B20', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 2, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:04:13'),
(21, 9, '中村', '倫也', '2026026B21', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 2, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:04:17'),
(22, 1, '佐藤', '健', '2026056C22', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 3, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:04:20'),
(23, 2, '高橋', '一生', '2026056C23', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 3, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:04:23'),
(24, 3, '吉岡', '里帆', '2026056C24', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 3, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:04:27'),
(25, 4, '山田', '涼介', '2026056C25', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 3, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:04:31'),
(26, 5, '石田', 'ゆり子', '2026056C26', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 3, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:04:34'),
(27, 6, '桜木', '花道', '2026056C27', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 3, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:04:38'),
(28, 7, '流川', '楓', '2026056C28', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 3, 1, 0, '2026-04-13 15:51:57', '2026-04-23 13:04:41'),
(29, 8, '日向', '翔陽', '2026056C29', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 3, 4, 0, '2026-04-13 15:51:57', '2026-04-23 13:04:44'),
(30, 9, '影山', '飛雄', '2026056C30', '$2y$10$GhvCQm/9n.Z0S5sgYWTJUulNKjBn6FRGg1sRflvE94XytigA0Q.Ju', 3, 2, 0, '2026-04-13 15:51:57', '2026-04-23 13:04:48');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `carcon_lines`
--
ALTER TABLE `carcon_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `classroom_id` (`classroom_id`),
  ADD KEY `carcon_staff_id` (`carcon_staff_id`);

--
-- テーブルのインデックス `carcon_request_reservations`
--
ALTER TABLE `carcon_request_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_carcon_reservation_detail_id` (`request_carcon_reservation_detail_id`),
  ADD KEY `change_carcon_reservation_detail_id` (`change_carcon_reservation_detail_id`),
  ADD KEY `request_meeting_type` (`request_meeting_type`),
  ADD KEY `change_meeting_type` (`change_meeting_type`),
  ADD KEY `request_status_id` (`request_status_id`);

--
-- テーブルのインデックス `carcon_reservations`
--
ALTER TABLE `carcon_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carcon_line_id` (`carcon_line_id`),
  ADD KEY `carcon_reservation_detail_id` (`carcon_reservation_detail_id`);

--
-- テーブルのインデックス `carcon_reservation_details`
--
ALTER TABLE `carcon_reservation_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `meeting_type` (`meeting_type`);

--
-- テーブルのインデックス `m_admin_staffs`
--
ALTER TABLE `m_admin_staffs`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `m_carcon_staffs`
--
ALTER TABLE `m_carcon_staffs`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `m_classrooms`
--
ALTER TABLE `m_classrooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- テーブルのインデックス `m_courses`
--
ALTER TABLE `m_courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_type` (`course_type`),
  ADD KEY `classroom_id` (`classroom_id`);

--
-- テーブルのインデックス `m_course_types`
--
ALTER TABLE `m_course_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- テーブルのインデックス `m_enrollments`
--
ALTER TABLE `m_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- テーブルのインデックス `m_meeting_types`
--
ALTER TABLE `m_meeting_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- テーブルのインデックス `m_request_statuses`
--
ALTER TABLE `m_request_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- テーブルのインデックス `m_students`
--
ALTER TABLE `m_students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login_id` (`login_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `enrollment_id` (`enrollment_id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `carcon_lines`
--
ALTER TABLE `carcon_lines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- テーブルの AUTO_INCREMENT `carcon_request_reservations`
--
ALTER TABLE `carcon_request_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- テーブルの AUTO_INCREMENT `carcon_reservations`
--
ALTER TABLE `carcon_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- テーブルの AUTO_INCREMENT `carcon_reservation_details`
--
ALTER TABLE `carcon_reservation_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- テーブルの AUTO_INCREMENT `m_admin_staffs`
--
ALTER TABLE `m_admin_staffs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- テーブルの AUTO_INCREMENT `m_carcon_staffs`
--
ALTER TABLE `m_carcon_staffs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- テーブルの AUTO_INCREMENT `m_classrooms`
--
ALTER TABLE `m_classrooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- テーブルの AUTO_INCREMENT `m_courses`
--
ALTER TABLE `m_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- テーブルの AUTO_INCREMENT `m_course_types`
--
ALTER TABLE `m_course_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- テーブルの AUTO_INCREMENT `m_enrollments`
--
ALTER TABLE `m_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- テーブルの AUTO_INCREMENT `m_meeting_types`
--
ALTER TABLE `m_meeting_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- テーブルの AUTO_INCREMENT `m_request_statuses`
--
ALTER TABLE `m_request_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- テーブルの AUTO_INCREMENT `m_students`
--
ALTER TABLE `m_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `carcon_lines`
--
ALTER TABLE `carcon_lines`
  ADD CONSTRAINT `carcon_lines_ibfk_1` FOREIGN KEY (`classroom_id`) REFERENCES `m_classrooms` (`id`),
  ADD CONSTRAINT `carcon_lines_ibfk_2` FOREIGN KEY (`carcon_staff_id`) REFERENCES `m_carcon_staffs` (`id`);

--
-- テーブルの制約 `carcon_request_reservations`
--
ALTER TABLE `carcon_request_reservations`
  ADD CONSTRAINT `carcon_request_reservations_ibfk_1` FOREIGN KEY (`request_carcon_reservation_detail_id`) REFERENCES `carcon_reservation_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carcon_request_reservations_ibfk_2` FOREIGN KEY (`change_carcon_reservation_detail_id`) REFERENCES `carcon_reservation_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carcon_request_reservations_ibfk_3` FOREIGN KEY (`request_meeting_type`) REFERENCES `m_meeting_types` (`id`),
  ADD CONSTRAINT `carcon_request_reservations_ibfk_4` FOREIGN KEY (`change_meeting_type`) REFERENCES `m_meeting_types` (`id`),
  ADD CONSTRAINT `carcon_request_reservations_ibfk_5` FOREIGN KEY (`request_status_id`) REFERENCES `m_request_statuses` (`id`);

--
-- テーブルの制約 `carcon_reservations`
--
ALTER TABLE `carcon_reservations`
  ADD CONSTRAINT `carcon_reservations_ibfk_1` FOREIGN KEY (`carcon_line_id`) REFERENCES `carcon_lines` (`id`),
  ADD CONSTRAINT `carcon_reservations_ibfk_2` FOREIGN KEY (`carcon_reservation_detail_id`) REFERENCES `carcon_reservation_details` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `carcon_reservation_details`
--
ALTER TABLE `carcon_reservation_details`
  ADD CONSTRAINT `carcon_reservation_details_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `m_students` (`id`),
  ADD CONSTRAINT `carcon_reservation_details_ibfk_2` FOREIGN KEY (`meeting_type`) REFERENCES `m_meeting_types` (`id`);

--
-- テーブルの制約 `m_courses`
--
ALTER TABLE `m_courses`
  ADD CONSTRAINT `m_courses_ibfk_1` FOREIGN KEY (`course_type`) REFERENCES `m_course_types` (`id`),
  ADD CONSTRAINT `m_courses_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `m_classrooms` (`id`);

--
-- テーブルの制約 `m_students`
--
ALTER TABLE `m_students`
  ADD CONSTRAINT `m_students_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `m_courses` (`id`),
  ADD CONSTRAINT `m_students_ibfk_4` FOREIGN KEY (`enrollment_id`) REFERENCES `m_enrollments` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
