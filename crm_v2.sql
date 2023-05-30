-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2023 at 10:51 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crm_v2`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `indonesia_cities`
--

CREATE TABLE `indonesia_cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` char(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `province_code` char(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `indonesia_districts`
--

CREATE TABLE `indonesia_districts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` char(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city_code` char(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `indonesia_provinces`
--

CREATE TABLE `indonesia_provinces` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` char(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `indonesia_villages`
--

CREATE TABLE `indonesia_villages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `district_code` char(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_countries`
--

CREATE TABLE `lc_countries` (
  `id` int(10) UNSIGNED NOT NULL,
  `lc_region_id` tinyint(3) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `official_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso_alpha_2` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso_alpha_3` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso_numeric` smallint(6) DEFAULT NULL,
  `geoname_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `international_phone` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `languages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`languages`)),
  `tld` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Top-level domain',
  `wmo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Country abbreviations by the World Meteorological Organization',
  `emoji` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_hex` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`color_hex`)),
  `color_rgb` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`color_rgb`)),
  `coordinates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`coordinates`)),
  `coordinates_limit` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`coordinates_limit`)),
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_countries_geographical`
--

CREATE TABLE `lc_countries_geographical` (
  `id` int(10) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lc_country_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `features_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`properties`)),
  `geometry` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`geometry`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_countries_translations`
--

CREATE TABLE `lc_countries_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lc_country_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_regions`
--

CREATE TABLE `lc_regions` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lc_region_translations`
--

CREATE TABLE `lc_region_translations` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `lc_region_id` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_agenda_speaker`
--

CREATE TABLE `tbl_agenda_speaker` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` char(11) CHARACTER SET utf8mb4 DEFAULT NULL,
  `sch_prog_id` bigint(20) UNSIGNED DEFAULT NULL,
  `partner_prog_id` bigint(20) UNSIGNED DEFAULT NULL,
  `eduf_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sch_pic_id` bigint(20) UNSIGNED DEFAULT NULL,
  `univ_pic_id` bigint(20) UNSIGNED DEFAULT NULL,
  `partner_pic_id` bigint(20) UNSIGNED DEFAULT NULL,
  `empl_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ALL-In PIC',
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `priority` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `speaker_type` enum('school','university','partner','internal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_asset`
--

CREATE TABLE `tbl_asset` (
  `asset_id` char(7) NOT NULL,
  `asset_name` varchar(255) DEFAULT NULL,
  `asset_merktype` varchar(255) DEFAULT NULL,
  `asset_dateachieved` date DEFAULT NULL,
  `asset_amount` int(11) DEFAULT NULL,
  `asset_running_stock` int(11) NOT NULL DEFAULT 0,
  `asset_unit` varchar(50) DEFAULT NULL,
  `asset_condition` varchar(255) DEFAULT NULL,
  `asset_notes` varchar(255) DEFAULT NULL,
  `asset_status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_asset_returned`
--

CREATE TABLE `tbl_asset_returned` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `asset_used_id` bigint(20) UNSIGNED NOT NULL,
  `returned_date` date NOT NULL,
  `amount_returned` int(11) NOT NULL DEFAULT 1,
  `condition` enum('Good','Not Good') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Good',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_asset_used`
--

CREATE TABLE `tbl_asset_used` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `asset_id` char(7) CHARACTER SET utf8mb4 NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `used_date` date NOT NULL,
  `amount_used` int(11) NOT NULL DEFAULT 1,
  `condition` enum('Good','Not Good') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Good',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_axis`
--

CREATE TABLE `tbl_axis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top` double(8,2) NOT NULL,
  `left` double(8,2) NOT NULL,
  `scaleX` double(8,2) NOT NULL,
  `scaleY` double(8,2) NOT NULL,
  `angle` double(8,2) NOT NULL,
  `flipX` tinyint(4) NOT NULL COMMENT '0: False, 1: True',
  `flipY` tinyint(4) NOT NULL COMMENT '0: False, 1: True',
  `type` enum('invoice','receipt') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_client`
--

CREATE TABLE `tbl_client` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `st_id` char(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(22) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_desc` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `insta` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` int(11) DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sch_id` char(8) CHARACTER SET utf8mb4 DEFAULT NULL,
  `st_grade` int(11) DEFAULT NULL,
  `lead_id` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eduf_id` bigint(20) UNSIGNED DEFAULT NULL,
  `event_id` char(11) CHARACTER SET utf8mb4 DEFAULT NULL,
  `st_levelinterest` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `graduation_year` char(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `st_abryear` char(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `st_statusact` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'status aktif client',
  `st_note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `st_statuscli` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: prospective, 1: potential, 2: current, 3: completed',
  `st_password` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$2y$10$SWFdY4TqrTDzPlRqcG7F6.FpdeeMNLGllgHaaD8nIRDthqBQFTI1i',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_client_abrcountry`
--

CREATE TABLE `tbl_client_abrcountry` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `tag_id` bigint(20) UNSIGNED DEFAULT NULL,
  `country_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'used when countries that shown in client are all country',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_client_additional_info`
--

CREATE TABLE `tbl_client_additional_info` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `category` enum('mail','phone') COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_client_event`
--

CREATE TABLE `tbl_client_event` (
  `clientevent_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `event_id` char(11) CHARACTER SET utf8mb4 DEFAULT NULL,
  `lead_id` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `eduf_id` bigint(20) UNSIGNED DEFAULT NULL,
  `partner_id` char(9) CHARACTER SET utf8mb4 DEFAULT NULL,
  `status` int(11) NOT NULL,
  `joined_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_client_mentor`
--

CREATE TABLE `tbl_client_mentor` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clientprog_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_client_prog`
--

CREATE TABLE `tbl_client_prog` (
  `clientprog_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `prog_id` char(11) CHARACTER SET utf8mb4 NOT NULL,
  `lead_id` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `eduf_lead_id` bigint(20) UNSIGNED DEFAULT NULL,
  `partner_id` char(9) CHARACTER SET utf8mb4 DEFAULT NULL,
  `clientevent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `first_discuss_date` date DEFAULT NULL,
  `last_discuss_date` date DEFAULT NULL,
  `followup_date` date DEFAULT NULL,
  `meeting_date` date DEFAULT NULL,
  `meeting_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0: pending, 1: success, 2: failed, 3: refund',
  `statusprog_date` date DEFAULT NULL,
  `initconsult_date` date DEFAULT NULL,
  `assessmentsent_date` date DEFAULT NULL,
  `negotiation_date` date DEFAULT NULL,
  `reason_id` bigint(20) UNSIGNED DEFAULT NULL,
  `test_date` date DEFAULT NULL,
  `last_class` date DEFAULT NULL,
  `diag_score` int(11) NOT NULL DEFAULT 0,
  `test_score` int(11) NOT NULL DEFAULT 0,
  `price_from_tutor` bigint(20) NOT NULL DEFAULT 0,
  `our_price_tutor` bigint(20) NOT NULL DEFAULT 0,
  `total_price_tutor` bigint(20) NOT NULL DEFAULT 0,
  `duration_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_uni` int(11) NOT NULL DEFAULT 0,
  `total_foreign_currency` bigint(20) NOT NULL DEFAULT 0,
  `foreign_currency_exchange` int(11) NOT NULL DEFAULT 0,
  `foreign_currency` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_idr` bigint(20) NOT NULL DEFAULT 0,
  `installment_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prog_running_status` int(11) NOT NULL DEFAULT 0 COMMENT '0: not yet, 1: ongoing, 2: done',
  `prog_start_date` date DEFAULT NULL,
  `prog_end_date` date DEFAULT NULL,
  `empl_id` bigint(20) UNSIGNED NOT NULL,
  `success_date` date DEFAULT NULL,
  `failed_date` date DEFAULT NULL,
  `refund_date` date DEFAULT NULL,
  `refund_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timesheet_link` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trial_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_client_relation`
--

CREATE TABLE `tbl_client_relation` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED NOT NULL,
  `child_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_client_roles`
--

CREATE TABLE `tbl_client_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_corp`
--

CREATE TABLE `tbl_corp` (
  `corp_id` char(9) NOT NULL,
  `corp_name` varchar(255) DEFAULT NULL,
  `corp_industry` varchar(255) DEFAULT NULL,
  `corp_mail` varchar(255) DEFAULT NULL,
  `corp_phone` varchar(255) DEFAULT NULL,
  `corp_insta` varchar(255) DEFAULT NULL,
  `corp_site` varchar(255) DEFAULT NULL,
  `corp_region` varchar(255) DEFAULT NULL,
  `corp_address` text DEFAULT NULL,
  `corp_note` text DEFAULT NULL,
  `corp_password` varchar(255) DEFAULT NULL,
  `country_type` enum('Indonesia','Overseas') NOT NULL,
  `type` enum('Corporate','Individual Professional','Tutoring Center','Course Center','Agent','Community','NGO') NOT NULL,
  `partnership_type` enum('Market Sharing','Program Collaborator','Internship','External Mentor') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_corp_partner_event`
--

CREATE TABLE `tbl_corp_partner_event` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `corp_id` char(9) CHARACTER SET utf8mb4 NOT NULL,
  `event_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_corp_pic`
--

CREATE TABLE `tbl_corp_pic` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `corp_id` char(9) CHARACTER SET utf8mb4 NOT NULL,
  `pic_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pic_mail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pic_linkedin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pic_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_pic` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_curriculum`
--

CREATE TABLE `tbl_curriculum` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_curriculum`
--

INSERT INTO `tbl_curriculum` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'American Curriculum', '2023-05-19 07:43:15', '2023-05-19 07:43:15'),
(2, 'Australian Curriculum', '2023-05-19 07:43:15', '2023-05-19 07:43:15'),
(3, 'Canadian Curriculum', '2023-05-19 07:43:15', '2023-05-19 07:43:15'),
(4, 'European Curriculum', '2023-05-19 07:43:15', '2023-05-19 07:43:15'),
(5, 'French Curriculum', '2023-05-19 07:43:15', '2023-05-19 07:43:15'),
(6, 'GCE', '2023-05-19 07:43:15', '2023-05-19 07:43:15'),
(7, 'IB', '2023-05-19 07:43:15', '2023-05-19 07:43:15'),
(8, 'IGCSE', '2023-05-19 07:43:15', '2023-05-19 07:43:15'),
(9, 'Korean Curriculum', '2023-05-19 07:43:15', '2023-05-19 07:43:15'),
(10, 'National Curriculum', '2023-05-19 07:43:15', '2023-05-19 07:43:15'),
(11, 'Singapore Curriculum', '2023-05-19 07:43:15', '2023-05-19 07:43:15');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_department`
--

CREATE TABLE `tbl_department` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dept_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_department`
--

INSERT INTO `tbl_department` (`id`, `dept_name`, `created_at`, `updated_at`) VALUES
(1, 'Client Management', '2023-05-19 07:53:24', '2023-05-19 07:53:24'),
(2, 'Business Development', '2023-05-19 07:53:24', '2023-05-19 07:53:24'),
(3, 'Finance & Operation', '2023-05-19 07:53:24', '2023-05-19 07:53:24'),
(4, 'Product Development', '2023-05-19 07:53:24', '2023-05-19 07:53:24'),
(5, 'HR', '2023-05-19 07:53:24', '2023-05-19 07:53:24'),
(6, 'IT', '2023-05-19 07:53:24', '2023-05-19 07:53:24');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_dreams_major`
--

CREATE TABLE `tbl_dreams_major` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `major_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_dreams_uni`
--

CREATE TABLE `tbl_dreams_uni` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `univ_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_eduf_lead`
--

CREATE TABLE `tbl_eduf_lead` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sch_id` char(8) CHARACTER SET utf8mb4 DEFAULT NULL,
  `corp_id` char(9) CHARACTER SET utf8mb4 DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `intr_pic` bigint(20) UNSIGNED NOT NULL,
  `ext_pic_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ext_pic_mail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ext_pic_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_discussion_date` date DEFAULT NULL,
  `last_discussion_date` date DEFAULT NULL,
  `event_start` date DEFAULT NULL,
  `event_end` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_eduf_review`
--

CREATE TABLE `tbl_eduf_review` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `eduf_id` bigint(20) UNSIGNED NOT NULL,
  `reviewer_name` bigint(20) UNSIGNED NOT NULL,
  `score` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `review` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_events`
--

CREATE TABLE `tbl_events` (
  `event_id` char(11) CHARACTER SET utf8mb4 NOT NULL,
  `event_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_startdate` datetime DEFAULT NULL,
  `event_enddate` datetime DEFAULT NULL,
  `event_target` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_event_pic`
--

CREATE TABLE `tbl_event_pic` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `empl_id` bigint(20) UNSIGNED NOT NULL COMMENT 'internal pic',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_event_speaker`
--

CREATE TABLE `tbl_event_speaker` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` char(11) CHARACTER SET utf8mb4 NOT NULL,
  `sch_pic_id` bigint(20) UNSIGNED DEFAULT NULL,
  `univ_pic_id` bigint(20) UNSIGNED DEFAULT NULL,
  `corp_pic_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `priority` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_followup`
--

CREATE TABLE `tbl_followup` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clientprog_id` bigint(20) UNSIGNED NOT NULL,
  `followup_date` date NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Not yet, 1: Done',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_interest_prog`
--

CREATE TABLE `tbl_interest_prog` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `prog_id` char(11) CHARACTER SET utf8mb4 NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_inv`
--

CREATE TABLE `tbl_inv` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inv_id` char(50) CHARACTER SET utf8mb4 NOT NULL,
  `clientprog_id` bigint(20) UNSIGNED NOT NULL,
  `ref_id` bigint(20) UNSIGNED DEFAULT NULL,
  `inv_category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inv_price` bigint(20) DEFAULT NULL,
  `inv_earlybird` bigint(20) DEFAULT NULL,
  `inv_discount` bigint(20) DEFAULT NULL,
  `inv_totalprice` bigint(20) NOT NULL DEFAULT 0,
  `inv_words` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inv_price_idr` bigint(20) DEFAULT NULL,
  `inv_earlybird_idr` bigint(20) DEFAULT NULL,
  `inv_discount_idr` bigint(20) DEFAULT NULL,
  `inv_totalprice_idr` bigint(20) NOT NULL,
  `inv_words_idr` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session` int(11) NOT NULL DEFAULT 0,
  `duration` int(11) NOT NULL DEFAULT 0,
  `inv_paymentmethod` enum('Full Payment','Installment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `inv_duedate` date DEFAULT NULL,
  `inv_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inv_tnc` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inv_status` int(11) NOT NULL DEFAULT 1 COMMENT '1: success, 2: refund',
  `curs_rate` int(11) NOT NULL DEFAULT 0,
  `currency` enum('gbp','usd','sgd','idr') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'idr',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `send_to_client` enum('sent','not sent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not sent',
  `reminded` int(11) NOT NULL COMMENT 'jumlah reminder terkirim'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_invb2b`
--

CREATE TABLE `tbl_invb2b` (
  `invb2b_num` bigint(20) UNSIGNED NOT NULL,
  `invb2b_id` char(50) CHARACTER SET utf8mb4 NOT NULL,
  `schprog_id` bigint(20) UNSIGNED DEFAULT NULL,
  `partnerprog_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ref_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invb2b_price` int(11) DEFAULT NULL,
  `invb2b_priceidr` int(11) DEFAULT NULL,
  `invb2b_participants` int(11) DEFAULT NULL,
  `invb2b_disc` int(11) DEFAULT NULL,
  `invb2b_discidr` int(11) DEFAULT NULL,
  `invb2b_totprice` int(11) DEFAULT NULL,
  `invb2b_totpriceidr` int(11) DEFAULT NULL,
  `invb2b_words` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invb2b_wordsidr` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invb2b_date` date NOT NULL,
  `invb2b_duedate` date DEFAULT NULL,
  `invb2b_pm` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invb2b_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invb2b_tnc` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invb2b_status` int(11) NOT NULL DEFAULT 1 COMMENT '1: Success, 2: Refund',
  `curs_rate` bigint(20) DEFAULT NULL,
  `currency` enum('gbp','usd','sgd','idr') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_full_amount` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reminded` int(11) NOT NULL COMMENT 'jumlah reminder terkirim'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_invdtl`
--

CREATE TABLE `tbl_invdtl` (
  `invdtl_id` bigint(20) UNSIGNED NOT NULL,
  `invb2b_id` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `inv_id` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `invdtl_installment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invdtl_duedate` date DEFAULT NULL,
  `invdtl_percentage` double(8,2) DEFAULT NULL,
  `invdtl_amount` int(11) DEFAULT NULL,
  `invdtl_amountidr` int(11) DEFAULT NULL,
  `invdtl_status` tinyint(4) NOT NULL DEFAULT 0,
  `invdtl_cursrate` bigint(20) DEFAULT NULL,
  `invdtl_currency` enum('gbp','usd','sgd','idr') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_inv_attachment`
--

CREATE TABLE `tbl_inv_attachment` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inv_id` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `invb2b_id` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `currency` enum('idr','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sign_status` enum('not yet','signed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not yet',
  `approve_date` datetime DEFAULT NULL,
  `send_to_client` enum('not sent','sent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not sent',
  `attachment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_lead`
--

CREATE TABLE `tbl_lead` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `main_lead` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_lead` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `score` bigint(20) NOT NULL DEFAULT 0,
  `color_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_login_log`
--

CREATE TABLE `tbl_login_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_type_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '0: logout, 1: login',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_main_menus`
--

CREATE TABLE `tbl_main_menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mainmenu_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_no` int(11) NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_main_menus`
--

INSERT INTO `tbl_main_menus` (`id`, `mainmenu_name`, `order_no`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'Master', 1, 'bi bi-bookmark', '2023-05-19 07:45:43', '2023-05-19 07:45:43'),
(2, 'Client', 2, 'bi bi-people-fill', '2023-05-19 07:45:43', '2023-05-19 07:45:43'),
(3, 'Instance', 3, 'bi bi-building', '2023-05-19 07:45:43', '2023-05-19 07:45:43'),
(4, 'Program', 4, 'bi bi-calendar2-event', '2023-05-19 07:45:43', '2023-05-19 07:45:43'),
(5, 'Invoice', 5, 'bi bi-receipt', '2023-05-19 07:45:43', '2023-05-19 07:45:43'),
(6, 'Receipt', 6, 'bi bi-receipt-cutoff', '2023-05-19 07:45:43', '2023-05-19 07:45:43'),
(7, 'Users', 7, 'bi bi-person-workspace', '2023-05-19 07:45:43', '2023-05-19 07:45:43'),
(8, 'Report', 8, 'bi bi-printer', '2023-05-19 07:45:43', '2023-05-19 07:45:43');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_main_prog`
--

CREATE TABLE `tbl_main_prog` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `prog_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prog_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_main_prog`
--

INSERT INTO `tbl_main_prog` (`id`, `prog_name`, `prog_status`, `created_at`, `updated_at`) VALUES
(1, 'Admissions Mentoring', 1, '2023-05-19 07:45:54', '2023-05-19 07:45:54'),
(2, 'Career Exploration', 1, '2023-05-19 07:45:54', '2023-05-19 07:45:54'),
(3, 'Application Bootcamp', 1, '2023-05-19 07:45:54', '2023-05-19 07:45:54'),
(4, 'Academic & Test Preparation', 1, '2023-05-19 07:45:54', '2023-05-19 07:45:54'),
(5, 'Events & Info Sessions', 1, '2023-05-19 07:45:54', '2023-05-19 07:45:54');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_major`
--

CREATE TABLE `tbl_major` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_menus`
--

CREATE TABLE `tbl_menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mainmenu_id` bigint(20) UNSIGNED NOT NULL,
  `submenu_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `submenu_link` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_no` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_menus`
--

INSERT INTO `tbl_menus` (`id`, `mainmenu_id`, `submenu_name`, `submenu_link`, `order_no`, `created_at`, `updated_at`) VALUES
(1, 1, 'Assets', 'master/asset', 1, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(2, 1, 'Curriculum', 'master/curriculum', 2, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(3, 1, 'Position', 'master/position', 3, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(4, 1, 'Lead Source', 'master/lead', 4, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(5, 1, 'Major', 'master/major', 5, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(6, 1, 'Program', 'master/program', 6, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(7, 1, 'Event', 'master/event', 7, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(8, 1, 'External Edufair', 'master/edufair', 8, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(9, 1, 'Purchase Request', 'master/purchase', 9, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(10, 1, 'Vendors', 'master/vendor', 10, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(11, 1, 'University Tag Score', 'master/university-tags', 11, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(12, 1, 'Sales Target', 'master/sales-target', 12, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(13, 2, 'Students', 'client/student?st=potential', 1, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(14, 2, 'Mentees', 'client/mentee?st=active', 2, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(15, 2, 'Parents', 'client/parent', 3, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(16, 2, 'Teacher/Counselor', 'client/teacher-counselor', 4, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(17, 3, 'Partner', 'instance/corporate', 1, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(18, 3, 'School', 'instance/school', 2, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(19, 3, 'Universities', 'instance/university', 3, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(20, 4, 'Referral', 'program/referral', 1, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(21, 4, 'Client Event', 'program/event', 2, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(22, 4, 'Client Program', 'program/client', 3, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(23, 4, 'Partner Program', 'program/corporate', 4, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(24, 4, 'School Program', 'program/school', 5, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(25, 5, 'Client Program', 'invoice/client-program?s=needed', 1, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(26, 5, 'Partner Program', 'invoice/corporate-program/status/needed', 2, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(27, 5, 'School Program', 'invoice/school-program/status/needed', 3, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(28, 5, 'Referral', 'invoice/referral/status/needed', 4, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(29, 5, 'Refund', 'invoice/refund/status/needed', 5, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(30, 6, 'Client Program', 'receipt/client-program', 1, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(31, 6, 'Partner Program', 'receipt/corporate-program', 2, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(32, 6, 'School Program', 'receipt/school-program', 3, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(33, 6, 'Referral Program', 'receipt/referral', 4, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(34, 7, 'Employee', 'user/employee', 1, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(35, 7, 'Volunteer', 'user/volunteer', 2, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(36, 8, 'Sales Tracking', 'report/sales', 1, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(37, 8, 'Event Tracking', 'report/event', 2, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(38, 8, 'Partnership', 'report/partnership', 3, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(39, 8, 'Invoice & Receipt', 'report/invoice', 4, '2023-05-19 07:46:55', '2023-05-19 07:46:55'),
(40, 8, 'Unpaid Payment', 'report/unpaid', 5, '2023-05-19 07:46:55', '2023-05-19 07:46:55');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_menusdtl`
--

CREATE TABLE `tbl_menusdtl` (
  `menusdtl_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `copy` tinyint(1) NOT NULL DEFAULT 0,
  `export` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_menusdtl`
--

INSERT INTO `tbl_menusdtl` (`menusdtl_id`, `menu_id`, `department_id`, `copy`, `export`, `created_at`, `updated_at`) VALUES
(1, 25, 3, 0, 0, '2023-04-12 07:12:45', '2023-04-12 07:12:45'),
(2, 26, 3, 0, 0, '2023-04-12 07:12:46', '2023-04-12 07:12:46'),
(3, 27, 3, 0, 0, '2023-04-12 07:12:47', '2023-04-12 07:12:47'),
(4, 28, 3, 0, 0, '2023-04-12 07:12:47', '2023-04-12 07:12:47'),
(5, 29, 3, 0, 0, '2023-04-12 07:12:48', '2023-04-12 07:12:48'),
(6, 30, 3, 0, 0, '2023-04-12 07:12:52', '2023-04-12 07:12:52'),
(7, 31, 3, 0, 0, '2023-04-12 07:12:53', '2023-04-12 07:12:53'),
(8, 32, 3, 0, 0, '2023-04-12 07:12:53', '2023-04-12 07:12:53'),
(9, 33, 3, 0, 0, '2023-04-12 07:12:55', '2023-04-12 07:12:55'),
(10, 39, 3, 0, 0, '2023-04-12 07:13:03', '2023-04-12 07:13:03'),
(11, 40, 3, 0, 0, '2023-04-12 07:13:08', '2023-04-12 07:13:08'),
(12, 13, 1, 0, 0, '2023-04-12 07:13:38', '2023-04-12 07:13:38'),
(13, 14, 1, 0, 0, '2023-04-12 07:13:39', '2023-04-12 07:13:39'),
(14, 15, 1, 0, 0, '2023-04-12 07:13:40', '2023-04-12 07:13:40'),
(15, 16, 1, 0, 0, '2023-04-12 07:13:41', '2023-04-12 07:13:41'),
(16, 10, 3, 0, 0, '2023-04-12 07:13:53', '2023-04-12 07:13:53'),
(17, 9, 3, 0, 0, '2023-04-12 07:13:54', '2023-04-12 07:13:54'),
(18, 1, 3, 0, 0, '2023-04-12 07:14:12', '2023-04-12 07:14:12'),
(19, 2, 1, 0, 0, '2023-04-12 07:14:26', '2023-04-12 07:14:26'),
(20, 6, 1, 0, 0, '2023-04-12 07:14:31', '2023-04-12 07:14:31'),
(21, 4, 1, 0, 0, '2023-04-12 07:14:34', '2023-04-12 07:14:34'),
(22, 7, 1, 0, 0, '2023-04-12 07:14:36', '2023-04-12 07:14:36'),
(23, 8, 1, 0, 0, '2023-04-12 07:14:37', '2023-04-12 07:14:37'),
(24, 12, 1, 0, 0, '2023-04-12 07:14:45', '2023-04-12 07:14:45'),
(25, 22, 1, 0, 0, '2023-04-12 07:15:12', '2023-04-12 07:15:12'),
(26, 21, 1, 0, 0, '2023-04-12 07:15:12', '2023-04-12 07:15:12'),
(27, 36, 1, 0, 0, '2023-04-12 07:15:22', '2023-04-12 07:15:22'),
(28, 37, 1, 0, 0, '2023-04-12 07:15:24', '2023-04-12 07:15:24'),
(29, 6, 2, 0, 0, '2023-04-12 07:16:02', '2023-04-12 07:16:02'),
(30, 8, 2, 0, 0, '2023-04-12 07:16:12', '2023-04-12 07:16:12'),
(31, 7, 2, 0, 0, '2023-04-12 07:16:34', '2023-04-12 07:16:34'),
(32, 17, 2, 0, 0, '2023-04-12 07:16:34', '2023-04-12 07:16:34'),
(33, 18, 2, 0, 0, '2023-04-12 07:16:35', '2023-04-12 07:16:35'),
(34, 19, 2, 0, 0, '2023-04-12 07:16:36', '2023-04-12 07:16:36'),
(35, 21, 2, 0, 0, '2023-04-12 07:16:46', '2023-04-12 07:16:46'),
(36, 23, 2, 0, 0, '2023-04-12 07:16:47', '2023-04-12 07:16:47'),
(37, 24, 2, 0, 0, '2023-04-12 07:16:48', '2023-04-12 07:16:48'),
(38, 20, 2, 0, 0, '2023-04-12 07:16:49', '2023-04-12 07:16:49'),
(39, 37, 2, 0, 0, '2023-04-12 07:16:55', '2023-04-12 07:16:55'),
(40, 38, 2, 0, 0, '2023-04-12 07:16:56', '2023-04-12 07:16:56'),
(41, 3, 3, 0, 0, '2023-04-12 07:18:28', '2023-04-12 07:18:28'),
(42, 5, 3, 0, 0, '2023-04-12 07:18:42', '2023-04-12 07:18:42'),
(43, 34, 3, 0, 0, '2023-04-12 07:20:07', '2023-04-12 07:20:07'),
(44, 35, 3, 0, 0, '2023-04-12 07:20:10', '2023-04-12 07:20:10'),
(45, 5, 1, 0, 0, '2023-04-12 07:20:31', '2023-04-12 07:20:31'),
(46, 11, 2, 0, 0, '2023-04-12 07:21:40', '2023-04-12 07:21:40'),
(47, 2, 2, 0, 0, '2023-04-12 07:21:47', '2023-04-12 07:21:47');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_menus_user`
--

CREATE TABLE `tbl_menus_user` (
  `menusdtl_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `copy` tinyint(1) NOT NULL DEFAULT 0,
  `export` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_partner`
--

CREATE TABLE `tbl_partner` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pt_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pt_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pt_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pt_institution` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pt_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_partner_agreement`
--

CREATE TABLE `tbl_partner_agreement` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `corp_id` char(9) CHARACTER SET utf8mb4 NOT NULL,
  `agreement_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `agreement_type` tinyint(4) NOT NULL COMMENT '0: Referral Mutual Agreement, 1: Partnership Agreement, 2: Speaker Agreement, 3: University Agent',
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `corp_pic` bigint(20) UNSIGNED NOT NULL,
  `empl_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_partner_prog`
--

CREATE TABLE `tbl_partner_prog` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `corp_id` char(9) CHARACTER SET utf8mb4 NOT NULL,
  `prog_id` char(11) CHARACTER SET utf8mb4 NOT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `first_discuss` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refund_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refund_date` date DEFAULT NULL,
  `status` int(11) NOT NULL COMMENT '0: Pending, 1: Success, 2: Rejected 3: Refund 4: Accepted 5: Cancel',
  `participants` bigint(20) DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `denied_date` date DEFAULT NULL,
  `success_date` date DEFAULT NULL,
  `cancel_date` date DEFAULT NULL,
  `accepted_date` date DEFAULT NULL,
  `pending_date` date DEFAULT NULL,
  `total_fee` double DEFAULT NULL,
  `is_corporate_scheme` tinyint(4) NOT NULL,
  `reason_id` bigint(20) UNSIGNED DEFAULT NULL,
  `empl_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ALL-In PIC',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_partner_prog_attachment`
--

CREATE TABLE `tbl_partner_prog_attachment` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `partner_prog_id` bigint(20) UNSIGNED NOT NULL,
  `corprog_file` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `corprog_attach` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_partner_prog_partner`
--

CREATE TABLE `tbl_partner_prog_partner` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `partnerprog_id` bigint(20) UNSIGNED NOT NULL,
  `corp_id` char(9) CHARACTER SET utf8mb4 NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_partner_prog_sch`
--

CREATE TABLE `tbl_partner_prog_sch` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `partnerprog_id` bigint(20) UNSIGNED NOT NULL,
  `sch_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_partner_prog_univ`
--

CREATE TABLE `tbl_partner_prog_univ` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `partnerprog_id` bigint(20) UNSIGNED NOT NULL,
  `univ_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_position`
--

CREATE TABLE `tbl_position` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `position_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_position`
--

INSERT INTO `tbl_position` (`id`, `position_name`, `created_at`, `updated_at`) VALUES
(1, 'CEO', '2022-11-15 04:49:43', '2023-04-12 08:25:36'),
(2, 'COO', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(3, 'Human Resources & Marketing Supervisor', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(4, 'Client Management & Marketing Supervisor', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(5, 'Educational Research & Business Development', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(6, 'Graphic and Interaction Designer', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(7, 'Finance & Operation', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(8, 'Mentor/ Education Consultant', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(9, 'Software Engineer Associate', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(10, 'Client Management', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(11, 'Marketing Communication & Research', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(12, 'Data Analyst ', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(13, 'Media & Communication', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(14, 'Corporate Marketing Manager', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(15, 'Team Digital', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(16, 'Community Engagement & Research Associate', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(17, 'Client Management Associate Intern', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(18, 'Media & Community Associate', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(19, 'Social Media Intern', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(20, 'Mentor Associate', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(21, 'Product Research Freelance', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(22, 'Junior Software Engineer', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(23, 'Market & Product Research Analyst Intern', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(24, 'Human Resources Analyst Intern', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(25, 'Product Associate', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(26, 'Product Associate Intern', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(27, 'Marketing & Product Research Analyst Intern', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(28, 'Growth Marketing & Business Associate', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(29, 'Digital Marketing Manager', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(30, 'Experiential Learning Associate', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(31, 'Program Development Intern', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(32, 'Market & Product Research Analyst', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(33, 'Digital Marketing Intern', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(34, 'Sales & Marketing Intern', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(35, 'Copywriting Intern', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(36, 'Marketing Intern', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(37, 'Graphic Design Intern', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(38, 'Program Developer Intern', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(39, 'Marketing & Product Research Intern', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(40, 'Human Resources Intern', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(41, 'Client Management Supervisior', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(42, 'HR Part Time', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(43, 'Content & Copywriter Specialist', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(44, 'Program Development Part Time', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(45, 'Copywriting Part-time', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(46, 'Program Manager', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(47, 'Sales', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(48, 'System Development Associate', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(49, 'HR Manager', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(50, 'Associate Business Development', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(51, 'Associate Mentor', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(52, 'Associate HR Generalist', '2022-11-15 04:49:43', '2022-11-15 04:49:43'),
(53, 'Associate General Affair', '2022-11-15 04:49:43', '2022-11-15 04:49:43');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_prog`
--

CREATE TABLE `tbl_prog` (
  `prog_id` char(11) NOT NULL,
  `main_prog_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sub_prog_id` bigint(20) UNSIGNED DEFAULT NULL,
  `prog_main` varchar(255) DEFAULT NULL,
  `main_number` int(11) DEFAULT NULL,
  `prog_sub` varchar(255) DEFAULT NULL,
  `prog_program` varchar(255) DEFAULT NULL,
  `prog_type` varchar(255) DEFAULT NULL,
  `prog_mentor` varchar(50) NOT NULL,
  `prog_payment` varchar(25) NOT NULL,
  `prog_scope` enum('public','mentee','school','partner') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_purchase_dtl`
--

CREATE TABLE `tbl_purchase_dtl` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` char(255) CHARACTER SET utf8mb4 NOT NULL,
  `item` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` int(11) NOT NULL,
  `price_per_unit` int(11) NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_purchase_request`
--

CREATE TABLE `tbl_purchase_request` (
  `purchase_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `requested_by` bigint(20) UNSIGNED NOT NULL,
  `purchase_department` bigint(20) UNSIGNED NOT NULL,
  `purchase_statusrequest` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_requestdate` date NOT NULL,
  `purchase_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_attachment` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reason`
--

CREATE TABLE `tbl_reason` (
  `reason_id` bigint(20) UNSIGNED NOT NULL,
  `reason_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_receipt`
--

CREATE TABLE `tbl_receipt` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `receipt_id` char(50) CHARACTER SET utf8mb4 NOT NULL,
  `receipt_cat` enum('student','school','partner','referral') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inv_id` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `invdtl_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invb2b_id` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `receipt_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_cheque` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_amount` int(11) DEFAULT NULL,
  `receipt_words` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_amount_idr` int(11) DEFAULT NULL,
  `receipt_words_idr` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_status` int(11) NOT NULL DEFAULT 1 COMMENT '1: success, 2: refund',
  `download_other` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Not Yet, 1: Downloaded',
  `download_idr` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Not Yet, 1: Downloaded',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_receipt_attachment`
--

CREATE TABLE `tbl_receipt_attachment` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `receipt_id` char(50) CHARACTER SET utf8mb4 NOT NULL,
  `currency` enum('idr','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sign_status` enum('not yet','signed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not yet',
  `approve_date` datetime DEFAULT NULL,
  `send_to_client` enum('not sent','sent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not sent',
  `attachment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_status` enum('not yet','requested') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not yet',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_referral`
--

CREATE TABLE `tbl_referral` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `partner_id` char(9) CHARACTER SET utf8mb4 NOT NULL,
  `prog_id` char(11) CHARACTER SET utf8mb4 DEFAULT NULL,
  `empl_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Internal PIC',
  `referral_type` enum('In','Out') COLLATE utf8mb4_unicode_ci NOT NULL,
  `additional_prog_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number_of_student` bigint(20) NOT NULL DEFAULT 0,
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `curs_rate` int(11) DEFAULT NULL,
  `revenue` bigint(20) NOT NULL DEFAULT 0,
  `revenue_other` int(11) DEFAULT NULL,
  `ref_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_refund`
--

CREATE TABLE `tbl_refund` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invb2b_id` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `inv_id` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `total_payment` int(11) NOT NULL,
  `total_paid` int(11) NOT NULL,
  `refund_amount` double NOT NULL,
  `percentage_refund` double NOT NULL,
  `tax_amount` double NOT NULL,
  `tax_percentage` double NOT NULL,
  `total_refunded` double NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_roles`
--

CREATE TABLE `tbl_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_roles`
--

INSERT INTO `tbl_roles` (`id`, `role_name`, `created_at`, `updated_at`) VALUES
(1, 'Employee', '2022-10-02 18:57:04', '2022-10-02 18:57:04'),
(2, 'Mentor', '2022-10-02 18:57:04', '2022-10-02 18:57:04'),
(3, 'Editor', '2022-10-02 18:57:04', '2022-10-02 18:57:04'),
(4, 'Tutor', '2022-10-02 18:57:04', '2022-10-02 18:57:04'),
(5, 'Mentee', '2022-10-02 18:57:04', '2022-10-02 18:57:04'),
(6, 'Parent', '2022-10-02 18:57:04', '2022-10-02 18:57:04'),
(7, 'Teacher/Counselor', '2022-10-02 18:57:04', '2022-10-02 18:57:04'),
(8, 'Admin', '2022-10-06 02:47:40', '2022-10-06 02:47:40'),
(9, 'Client', '2022-10-06 02:49:15', '2022-10-06 02:49:15'),
(10, 'Finance', '2022-10-06 02:49:15', '2022-10-06 02:49:15'),
(11, 'BizDev', '2022-10-06 02:49:15', '2022-10-06 02:49:15'),
(12, 'HR', '2022-10-06 02:49:15', '2022-10-06 02:49:15'),
(13, 'Associate Editor', '2022-10-19 01:33:02', '2022-10-19 01:33:02'),
(14, 'Senior Editor', '2022-10-19 01:33:02', '2022-10-19 01:33:02'),
(15, 'Managing Editor', '2022-10-19 01:33:02', '2022-10-19 01:33:02'),
(16, 'Student', '2022-12-04 21:52:02', '2022-12-04 21:52:02'),
(17, 'Alumni', '2023-01-01 20:54:10', '2023-01-01 20:54:10');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sales_target`
--

CREATE TABLE `tbl_sales_target` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `prog_id` char(11) CHARACTER SET utf8mb4 DEFAULT NULL,
  `month_year` date NOT NULL,
  `total_participant` int(11) NOT NULL,
  `total_target` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sch`
--

CREATE TABLE `tbl_sch` (
  `sch_id` char(8) NOT NULL,
  `sch_name` varchar(255) DEFAULT NULL,
  `sch_type` varchar(255) DEFAULT NULL,
  `sch_mail` varchar(255) DEFAULT NULL,
  `sch_phone` varchar(255) DEFAULT NULL,
  `sch_insta` varchar(255) DEFAULT NULL,
  `sch_city` varchar(255) DEFAULT NULL,
  `sch_location` varchar(255) DEFAULT NULL,
  `sch_score` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_schdetail`
--

CREATE TABLE `tbl_schdetail` (
  `schdetail_id` bigint(20) UNSIGNED NOT NULL,
  `sch_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `schdetail_fullname` varchar(50) DEFAULT NULL,
  `schdetail_email` varchar(50) DEFAULT NULL,
  `schdetail_grade` varchar(50) NOT NULL,
  `schdetail_position` varchar(50) DEFAULT NULL,
  `schdetail_phone` varchar(25) DEFAULT NULL,
  `is_pic` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sch_curriculum`
--

CREATE TABLE `tbl_sch_curriculum` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sch_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `curriculum_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sch_event`
--

CREATE TABLE `tbl_sch_event` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sch_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `event_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sch_prog`
--

CREATE TABLE `tbl_sch_prog` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sch_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `prog_id` char(11) CHARACTER SET utf8mb4 NOT NULL,
  `first_discuss` date DEFAULT NULL,
  `status` int(11) NOT NULL COMMENT '0: Pending, 1: Success, 2: Rejected 3: Refund 4: Accepted 5: Cancel',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes_detail` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refund_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refund_date` date DEFAULT NULL,
  `running_status` enum('Not yet','On going','Done') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_hours` int(11) DEFAULT NULL,
  `total_fee` double DEFAULT NULL,
  `participants` int(11) DEFAULT NULL,
  `place` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_program_date` date DEFAULT NULL,
  `start_program_date` date DEFAULT NULL,
  `success_date` date DEFAULT NULL,
  `cancel_date` date DEFAULT NULL,
  `accepted_date` date DEFAULT NULL,
  `pending_date` date DEFAULT NULL,
  `reason_id` bigint(20) UNSIGNED DEFAULT NULL,
  `denied_date` date DEFAULT NULL,
  `empl_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ALL-In PIC',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sch_prog_attach`
--

CREATE TABLE `tbl_sch_prog_attach` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `schprog_id` bigint(20) UNSIGNED NOT NULL,
  `schprog_file` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `schprog_attach` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sch_prog_partner`
--

CREATE TABLE `tbl_sch_prog_partner` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `schprog_id` bigint(20) UNSIGNED NOT NULL,
  `corp_id` char(9) CHARACTER SET utf8mb4 NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sch_prog_school`
--

CREATE TABLE `tbl_sch_prog_school` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `schprog_id` bigint(20) UNSIGNED NOT NULL,
  `sch_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sch_prog_univ`
--

CREATE TABLE `tbl_sch_prog_univ` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `schprog_id` bigint(20) UNSIGNED NOT NULL,
  `univ_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sch_visit`
--

CREATE TABLE `tbl_sch_visit` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sch_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `internal_pic` bigint(20) UNSIGNED NOT NULL,
  `school_pic` bigint(20) UNSIGNED NOT NULL,
  `visit_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` enum('waiting','visited') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_scoring_param`
--

CREATE TABLE `tbl_scoring_param` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_score` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_scoring_param`
--

INSERT INTO `tbl_scoring_param` (`id`, `category`, `max_score`, `created_at`, `updated_at`) VALUES
(1, 'School', 6, '2023-05-19 07:46:41', '2023-05-19 07:46:41'),
(2, 'Lead', 4, '2023-05-19 07:46:41', '2023-05-19 07:46:41'),
(3, 'Graduation Year', 7, '2023-05-19 07:46:41', '2023-05-19 07:46:41'),
(4, 'Destination', 6, '2023-05-19 07:46:41', '2023-05-19 07:46:41'),
(5, 'Type of Client', 4, '2023-05-19 07:46:41', '2023-05-19 07:46:41');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_speaker`
--

CREATE TABLE `tbl_speaker` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sp_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sp_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sp_institution` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sub_prog`
--

CREATE TABLE `tbl_sub_prog` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `main_prog_id` bigint(20) UNSIGNED NOT NULL,
  `sub_prog_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_prog_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_sub_prog`
--

INSERT INTO `tbl_sub_prog` (`id`, `main_prog_id`, `sub_prog_name`, `sub_prog_status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Admissions Mentoring', 1, '2023-05-19 07:47:04', '2023-05-19 07:47:04'),
(2, 1, 'Essay Clinic', 1, '2023-05-19 07:47:04', '2023-05-19 07:47:04'),
(3, 1, 'Interview Preparation', 1, '2023-05-19 07:47:04', '2023-05-19 07:47:04'),
(4, 2, 'JuniorXplorer', 1, '2023-05-19 07:47:04', '2023-05-19 07:47:04'),
(5, 2, 'PassionXplorer', 1, '2023-05-19 07:47:04', '2023-05-19 07:47:04'),
(6, 2, 'Summer Science Research Program', 1, '2023-05-19 07:47:04', '2023-05-19 07:47:04'),
(7, 4, 'Academic Tutoring', 1, '2023-05-19 07:47:04', '2023-05-19 07:47:04'),
(8, 4, 'ACT Prep', 1, '2023-05-19 07:47:04', '2023-05-19 07:47:04'),
(9, 4, 'SAT Last Minute', 1, '2023-05-19 07:47:04', '2023-05-19 07:47:04'),
(10, 4, 'SAT Last Minute Subject', 1, '2023-05-19 07:47:04', '2023-05-19 07:47:04'),
(11, 4, 'SAT Prep', 1, '2023-05-19 07:47:04', '2023-05-19 07:47:04'),
(12, 4, 'SAT Subject', 1, '2023-05-19 07:47:04', '2023-05-19 07:47:04'),
(13, 4, 'Subject Tutoring', 1, '2023-05-19 07:47:04', '2023-05-19 07:47:04');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tag`
--

CREATE TABLE `tbl_tag` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_tag`
--

INSERT INTO `tbl_tag` (`id`, `name`, `score`, `created_at`, `updated_at`) VALUES
(1, 'US', 6, '2023-05-19 07:47:10', '2023-05-19 07:47:10'),
(2, 'UK', 6, '2023-05-19 07:47:10', '2023-05-19 07:47:10'),
(3, 'Canada', 4, '2023-05-19 07:47:10', '2023-05-19 07:47:10'),
(4, 'Australia', 4, '2023-05-19 07:47:10', '2023-05-19 07:47:10'),
(5, 'Asia', 3, '2023-05-19 07:47:10', '2023-05-19 07:47:10'),
(6, 'Other', 1, '2023-05-19 07:47:10', '2023-05-19 07:47:10');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_univ`
--

CREATE TABLE `tbl_univ` (
  `univ_id` char(8) NOT NULL,
  `univ_name` varchar(255) DEFAULT NULL,
  `univ_address` text DEFAULT NULL,
  `univ_country` varchar(255) DEFAULT NULL,
  `tag` bigint(20) UNSIGNED DEFAULT NULL,
  `univ_email` varchar(255) DEFAULT NULL,
  `univ_phone` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_univ_event`
--

CREATE TABLE `tbl_univ_event` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `univ_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `event_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_univ_pic`
--

CREATE TABLE `tbl_univ_pic` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `univ_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_pic` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_educations`
--

CREATE TABLE `tbl_user_educations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `univ_id` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `major_id` bigint(20) UNSIGNED NOT NULL,
  `degree` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `graduation_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_roles`
--

CREATE TABLE `tbl_user_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `extended_id` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tutor_subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `feehours` bigint(20) DEFAULT NULL,
  `feesession` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_user_roles`
--

INSERT INTO `tbl_user_roles` (`id`, `user_id`, `role_id`, `extended_id`, `tutor_subject`, `feehours`, `feesession`, `created_at`, `updated_at`) VALUES
(1, 10, 8, NULL, NULL, NULL, NULL, '2023-05-19 08:44:56', '2023-05-19 08:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_type`
--

CREATE TABLE `tbl_user_type` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type_name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_user_type`
--

INSERT INTO `tbl_user_type` (`id`, `type_name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Full-Time', 1, '2023-05-19 07:47:18', '2023-05-19 07:47:18'),
(2, 'Part-Time', 1, '2023-05-19 07:47:18', '2023-05-19 07:47:18'),
(3, 'Probation', 1, '2023-05-19 07:47:18', '2023-05-19 07:47:18'),
(4, 'Internship', 1, '2023-05-19 07:47:18', '2023-05-19 07:47:18');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_type_detail`
--

CREATE TABLE `tbl_user_type_detail` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_type_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deactivated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_user_type_detail`
--

INSERT INTO `tbl_user_type_detail` (`id`, `user_type_id`, `user_id`, `department_id`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`, `deactivated_at`) VALUES
(10, 1, 10, 6, NULL, NULL, 1, '2023-04-03 03:06:12', '2023-04-03 03:06:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_vendor`
--

CREATE TABLE `tbl_vendor` (
  `vendor_id` char(7) NOT NULL,
  `vendor_name` varchar(255) DEFAULT NULL,
  `vendor_address` varchar(255) DEFAULT NULL,
  `vendor_phone` varchar(255) DEFAULT NULL,
  `vendor_type` varchar(255) DEFAULT NULL,
  `vendor_material` varchar(255) DEFAULT NULL,
  `vendor_size` varchar(255) DEFAULT NULL,
  `vendor_unitprice` int(11) NOT NULL DEFAULT 0,
  `vendor_processingtime` varchar(50) DEFAULT NULL,
  `vendor_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_vendor_type`
--

CREATE TABLE `tbl_vendor_type` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_vendor_type`
--

INSERT INTO `tbl_vendor_type` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Flyer', '2023-05-19 07:47:24', '2023-05-19 07:47:24'),
(2, 'Infopack', '2023-05-19 07:47:24', '2023-05-19 07:47:24'),
(3, 'Poster', '2023-05-19 07:47:24', '2023-05-19 07:47:24'),
(4, 'Name Card', '2023-05-19 07:47:24', '2023-05-19 07:47:24'),
(5, 'ID Card staff', '2023-05-19 07:47:24', '2023-05-19 07:47:24'),
(6, 'Sticker', '2023-05-19 07:47:24', '2023-05-19 07:47:24'),
(7, 'Voucher', '2023-05-19 07:47:24', '2023-05-19 07:47:24'),
(8, 'Totte bag', '2023-05-19 07:47:24', '2023-05-19 07:47:24'),
(9, 'T-shirt', '2023-05-19 07:47:24', '2023-05-19 07:47:24'),
(10, 'Banner', '2023-05-19 07:47:24', '2023-05-19 07:47:24'),
(11, 'Letterhead', '2023-05-19 07:47:24', '2023-05-19 07:47:24'),
(12, 'Print BW', '2023-05-19 07:47:24', '2023-05-19 07:47:24'),
(13, 'Print Colour', '2023-05-19 07:47:24', '2023-05-19 07:47:24'),
(14, 'Notaris', '2023-05-19 07:47:24', '2023-05-19 07:47:24');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_volunt`
--

CREATE TABLE `tbl_volunt` (
  `volunt_id` char(8) NOT NULL,
  `volunt_firstname` varchar(255) DEFAULT NULL,
  `volunt_lastname` varchar(255) DEFAULT NULL,
  `volunt_address` varchar(255) DEFAULT NULL,
  `volunt_mail` varchar(255) DEFAULT NULL,
  `volunt_phone` varchar(255) DEFAULT NULL,
  `volunt_position` bigint(20) UNSIGNED DEFAULT NULL,
  `volunt_major` bigint(20) UNSIGNED DEFAULT NULL,
  `volunt_graduatedfr` char(8) DEFAULT NULL,
  `volunt_idcard` varchar(255) DEFAULT NULL,
  `volunt_npwp` varchar(255) DEFAULT NULL,
  `empl_insurance` varchar(255) DEFAULT NULL,
  `health_insurance` varchar(255) DEFAULT NULL,
  `volunt_npwp_number` bigint(20) DEFAULT NULL,
  `volunt_nik` bigint(20) NOT NULL,
  `volunt_bank_accnumber` bigint(20) NOT NULL,
  `volunt_bank_accname` varchar(255) NOT NULL,
  `volunt_cv` varchar(255) NOT NULL,
  `volunt_status` int(2) DEFAULT 1,
  `volunt_lasteditdate` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nip` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extended_id` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `datebirth` date DEFAULT NULL,
  `position_id` bigint(20) UNSIGNED DEFAULT NULL,
  `password` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hiredate` date DEFAULT NULL,
  `nik` bigint(20) DEFAULT NULL,
  `idcard` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cv` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bankname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bankacc` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `health_insurance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empl_insurance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `export` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uuid`, `nip`, `extended_id`, `first_name`, `last_name`, `address`, `email`, `email_verified_at`, `phone`, `emergency_contact`, `datebirth`, `position_id`, `password`, `hiredate`, `nik`, `idcard`, `cv`, `bankname`, `bankacc`, `npwp`, `tax`, `active`, `health_insurance`, `empl_insurance`, `export`, `notes`, `remember_token`, `created_at`, `updated_at`) VALUES
(10, '194b2590-e9d1-4b1d-9080-a5dc74bfc914', NULL, 'EMPL-0010', 'Admin', 'ALL-in Edu', '-', 'admin@all-inedu.com', NULL, '-', '-', '2006-04-10', 9, '$2y$10$7EQmRIYjdNAp.49CJQ.wCOL7Fm.u8CS2UXgMsMxK8CBCloFJrmzmW', '2006-04-10', 1234567890, NULL, NULL, '', '', '', NULL, 1, NULL, NULL, 1, NULL, NULL, '2023-04-03 03:06:12', '2023-04-03 03:06:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `indonesia_cities`
--
ALTER TABLE `indonesia_cities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `indonesia_cities_code_unique` (`code`),
  ADD KEY `indonesia_cities_province_code_foreign` (`province_code`);

--
-- Indexes for table `indonesia_districts`
--
ALTER TABLE `indonesia_districts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `indonesia_districts_code_unique` (`code`),
  ADD KEY `indonesia_districts_city_code_foreign` (`city_code`);

--
-- Indexes for table `indonesia_provinces`
--
ALTER TABLE `indonesia_provinces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `indonesia_provinces_code_unique` (`code`);

--
-- Indexes for table `indonesia_villages`
--
ALTER TABLE `indonesia_villages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `indonesia_villages_code_unique` (`code`),
  ADD KEY `indonesia_villages_district_code_foreign` (`district_code`);

--
-- Indexes for table `lc_countries`
--
ALTER TABLE `lc_countries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lc_countries_lc_region_id_iso_alpha_2_unique` (`lc_region_id`,`iso_alpha_2`),
  ADD UNIQUE KEY `lc_countries_uuid_unique` (`uuid`);

--
-- Indexes for table `lc_countries_geographical`
--
ALTER TABLE `lc_countries_geographical`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lc_countries_geographical_lc_country_id_foreign` (`lc_country_id`);

--
-- Indexes for table `lc_countries_translations`
--
ALTER TABLE `lc_countries_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lc_countries_translations_lc_country_id_locale_unique` (`lc_country_id`,`locale`),
  ADD KEY `lc_countries_translations_locale_index` (`locale`);

--
-- Indexes for table `lc_regions`
--
ALTER TABLE `lc_regions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lc_regions_uuid_unique` (`uuid`);

--
-- Indexes for table `lc_region_translations`
--
ALTER TABLE `lc_region_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lc_region_translations_lc_region_id_locale_unique` (`lc_region_id`,`locale`),
  ADD UNIQUE KEY `lc_region_translations_slug_locale_unique` (`slug`,`locale`),
  ADD KEY `lc_region_translations_locale_index` (`locale`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `tbl_agenda_speaker`
--
ALTER TABLE `tbl_agenda_speaker`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_agenda_speaker_event_id_foreign` (`event_id`),
  ADD KEY `tbl_agenda_speaker_sch_prog_id_foreign` (`sch_prog_id`),
  ADD KEY `tbl_agenda_speaker_partner_prog_id_foreign` (`partner_prog_id`),
  ADD KEY `tbl_agenda_speaker_sch_pic_id_foreign` (`sch_pic_id`),
  ADD KEY `tbl_agenda_speaker_univ_pic_id_foreign` (`univ_pic_id`),
  ADD KEY `tbl_agenda_speaker_partner_pic_id_foreign` (`partner_pic_id`),
  ADD KEY `tbl_agenda_speaker_empl_id_foreign` (`empl_id`),
  ADD KEY `tbl_agenda_speaker_eduf_id_foreign` (`eduf_id`);

--
-- Indexes for table `tbl_asset`
--
ALTER TABLE `tbl_asset`
  ADD PRIMARY KEY (`asset_id`);

--
-- Indexes for table `tbl_asset_returned`
--
ALTER TABLE `tbl_asset_returned`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_asset_returned_asset_used_id_foreign` (`asset_used_id`);

--
-- Indexes for table `tbl_asset_used`
--
ALTER TABLE `tbl_asset_used`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_asset_used_asset_id_foreign` (`asset_id`),
  ADD KEY `tbl_asset_used_user_id_foreign` (`user_id`);

--
-- Indexes for table `tbl_axis`
--
ALTER TABLE `tbl_axis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_client`
--
ALTER TABLE `tbl_client`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `st_id` (`st_id`),
  ADD KEY `tbl_client_sch_id_foreign` (`sch_id`),
  ADD KEY `tbl_client_eduf_id_foreign` (`eduf_id`),
  ADD KEY `tbl_client_event_id_foreign` (`event_id`),
  ADD KEY `tbl_client_lead_id_foreign` (`lead_id`);

--
-- Indexes for table `tbl_client_abrcountry`
--
ALTER TABLE `tbl_client_abrcountry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_client_abrcountry_client_id_foreign` (`client_id`),
  ADD KEY `tbl_client_abrcountry_tag_id_foreign` (`tag_id`);

--
-- Indexes for table `tbl_client_additional_info`
--
ALTER TABLE `tbl_client_additional_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_client_additional_info_client_id_foreign` (`client_id`);

--
-- Indexes for table `tbl_client_event`
--
ALTER TABLE `tbl_client_event`
  ADD PRIMARY KEY (`clientevent_id`),
  ADD KEY `tbl_client_event_event_id_foreign` (`event_id`),
  ADD KEY `tbl_client_event_lead_id_foreign` (`lead_id`),
  ADD KEY `tbl_client_event_eduf_id_foreign` (`eduf_id`),
  ADD KEY `tbl_client_event_client_id_foreign` (`client_id`),
  ADD KEY `tbl_client_event_partner_id_foreign` (`partner_id`);

--
-- Indexes for table `tbl_client_mentor`
--
ALTER TABLE `tbl_client_mentor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_client_mentor_user_id_foreign` (`user_id`),
  ADD KEY `tbl_client_mentor_clientprog_id_foreign` (`clientprog_id`);

--
-- Indexes for table `tbl_client_prog`
--
ALTER TABLE `tbl_client_prog`
  ADD PRIMARY KEY (`clientprog_id`),
  ADD KEY `tbl_client_prog_lead_id_foreign` (`lead_id`),
  ADD KEY `tbl_client_prog_eduf_lead_id_foreign` (`eduf_lead_id`),
  ADD KEY `tbl_client_prog_clientevent_id_foreign` (`clientevent_id`),
  ADD KEY `tbl_client_prog_reason_id_foreign` (`reason_id`),
  ADD KEY `tbl_client_prog_empl_id_foreign` (`empl_id`),
  ADD KEY `tbl_client_prog_client_id_foreign` (`client_id`),
  ADD KEY `tbl_client_prog_partner_id_foreign` (`partner_id`),
  ADD KEY `tbl_client_prog_prog_id_foreign` (`prog_id`);

--
-- Indexes for table `tbl_client_relation`
--
ALTER TABLE `tbl_client_relation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_client_relation_parent_id_foreign` (`parent_id`),
  ADD KEY `tbl_client_relation_child_id_foreign` (`child_id`);

--
-- Indexes for table `tbl_client_roles`
--
ALTER TABLE `tbl_client_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_client_roles_client_id_foreign` (`client_id`),
  ADD KEY `tbl_client_roles_role_id_foreign` (`role_id`);

--
-- Indexes for table `tbl_corp`
--
ALTER TABLE `tbl_corp`
  ADD PRIMARY KEY (`corp_id`);

--
-- Indexes for table `tbl_corp_partner_event`
--
ALTER TABLE `tbl_corp_partner_event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_corp_partner_event_corp_id_foreign` (`corp_id`),
  ADD KEY `tbl_corp_partner_event_event_id_foreign` (`event_id`);

--
-- Indexes for table `tbl_corp_pic`
--
ALTER TABLE `tbl_corp_pic`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_corp_pic_corp_id_foreign` (`corp_id`);

--
-- Indexes for table `tbl_curriculum`
--
ALTER TABLE `tbl_curriculum`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_department`
--
ALTER TABLE `tbl_department`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_dreams_major`
--
ALTER TABLE `tbl_dreams_major`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_dreams_major_client_id_foreign` (`client_id`),
  ADD KEY `tbl_dreams_major_major_id_foreign` (`major_id`);

--
-- Indexes for table `tbl_dreams_uni`
--
ALTER TABLE `tbl_dreams_uni`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_dreams_uni_univ_id_foreign` (`univ_id`),
  ADD KEY `tbl_dreams_uni_client_id_foreign` (`client_id`);

--
-- Indexes for table `tbl_eduf_lead`
--
ALTER TABLE `tbl_eduf_lead`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_eduf_lead_sch_id_foreign` (`sch_id`),
  ADD KEY `tbl_eduf_lead_intr_pic_foreign` (`intr_pic`),
  ADD KEY `tbl_eduf_lead_corp_id_foreign` (`corp_id`);

--
-- Indexes for table `tbl_eduf_review`
--
ALTER TABLE `tbl_eduf_review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_eduf_review_eduf_id_foreign` (`eduf_id`),
  ADD KEY `tbl_eduf_review_reviewer_name_foreign` (`reviewer_name`);

--
-- Indexes for table `tbl_events`
--
ALTER TABLE `tbl_events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `tbl_event_pic`
--
ALTER TABLE `tbl_event_pic`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_event_pic_event_id_foreign` (`event_id`),
  ADD KEY `tbl_event_pic_empl_id_foreign` (`empl_id`);

--
-- Indexes for table `tbl_event_speaker`
--
ALTER TABLE `tbl_event_speaker`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_event_speaker_event_id_foreign` (`event_id`),
  ADD KEY `tbl_event_speaker_sch_pic_id_foreign` (`sch_pic_id`),
  ADD KEY `tbl_event_speaker_univ_pic_id_foreign` (`univ_pic_id`),
  ADD KEY `tbl_event_speaker_corp_pic_id_foreign` (`corp_pic_id`);

--
-- Indexes for table `tbl_followup`
--
ALTER TABLE `tbl_followup`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_followup_clientprog_id_foreign` (`clientprog_id`);

--
-- Indexes for table `tbl_interest_prog`
--
ALTER TABLE `tbl_interest_prog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_interest_prog_client_id_foreign` (`client_id`),
  ADD KEY `tbl_interest_prog_prog_id_foreign` (`prog_id`);

--
-- Indexes for table `tbl_inv`
--
ALTER TABLE `tbl_inv`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tbl_inv_inv_id_unique` (`inv_id`),
  ADD KEY `tbl_inv_clientprog_id_foreign` (`clientprog_id`),
  ADD KEY `tbl_inv_ref_id_foreign` (`ref_id`);

--
-- Indexes for table `tbl_invb2b`
--
ALTER TABLE `tbl_invb2b`
  ADD PRIMARY KEY (`invb2b_num`),
  ADD UNIQUE KEY `tbl_invb2b_invb2b_id_unique` (`invb2b_id`),
  ADD KEY `tbl_invb2b_schprog_id_foreign` (`schprog_id`),
  ADD KEY `tbl_invb2b_partnerprog_id_foreign` (`partnerprog_id`),
  ADD KEY `tbl_invb2b_ref_id_foreign` (`ref_id`);

--
-- Indexes for table `tbl_invdtl`
--
ALTER TABLE `tbl_invdtl`
  ADD PRIMARY KEY (`invdtl_id`),
  ADD KEY `tbl_invdtl_invb2b_id_foreign` (`invb2b_id`),
  ADD KEY `tbl_invdtl_inv_id_foreign` (`inv_id`);

--
-- Indexes for table `tbl_inv_attachment`
--
ALTER TABLE `tbl_inv_attachment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_inv_attachment_inv_id_foreign` (`inv_id`),
  ADD KEY `tbl_inv_attachment_invb2b_id_foreign` (`invb2b_id`);

--
-- Indexes for table `tbl_lead`
--
ALTER TABLE `tbl_lead`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `extended_id` (`lead_id`);

--
-- Indexes for table `tbl_login_log`
--
ALTER TABLE `tbl_login_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_login_log_user_type_id_foreign` (`user_type_id`);

--
-- Indexes for table `tbl_main_menus`
--
ALTER TABLE `tbl_main_menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_main_prog`
--
ALTER TABLE `tbl_main_prog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_major`
--
ALTER TABLE `tbl_major`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_menus`
--
ALTER TABLE `tbl_menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_menus_mainmenu_id_foreign` (`mainmenu_id`);

--
-- Indexes for table `tbl_menusdtl`
--
ALTER TABLE `tbl_menusdtl`
  ADD PRIMARY KEY (`menusdtl_id`),
  ADD KEY `tbl_menusdtl_department_id_foreign` (`department_id`),
  ADD KEY `tbl_menusdtl_menu_id_foreign` (`menu_id`);

--
-- Indexes for table `tbl_menus_user`
--
ALTER TABLE `tbl_menus_user`
  ADD PRIMARY KEY (`menusdtl_id`),
  ADD KEY `tbl_menus_user_menu_id_foreign` (`menu_id`),
  ADD KEY `tbl_menus_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `tbl_partner`
--
ALTER TABLE `tbl_partner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_partner_agreement`
--
ALTER TABLE `tbl_partner_agreement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_partner_agreement_corp_id_foreign` (`corp_id`),
  ADD KEY `tbl_partner_agreement_corp_pic_foreign` (`corp_pic`),
  ADD KEY `tbl_partner_agreement_empl_id_foreign` (`empl_id`);

--
-- Indexes for table `tbl_partner_prog`
--
ALTER TABLE `tbl_partner_prog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_partner_prog_corp_id_foreign` (`corp_id`),
  ADD KEY `tbl_partner_prog_prog_id_foreign` (`prog_id`),
  ADD KEY `tbl_partner_prog_empl_id_foreign` (`empl_id`),
  ADD KEY `tbl_partner_prog_reason_id_foreign` (`reason_id`);

--
-- Indexes for table `tbl_partner_prog_attachment`
--
ALTER TABLE `tbl_partner_prog_attachment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_partner_prog_attachment_partner_prog_id_foreign` (`partner_prog_id`);

--
-- Indexes for table `tbl_partner_prog_partner`
--
ALTER TABLE `tbl_partner_prog_partner`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_partner_prog_partner_partnerprog_id_foreign` (`partnerprog_id`),
  ADD KEY `tbl_partner_prog_partner_corp_id_foreign` (`corp_id`);

--
-- Indexes for table `tbl_partner_prog_sch`
--
ALTER TABLE `tbl_partner_prog_sch`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_partner_prog_sch_partnerprog_id_foreign` (`partnerprog_id`),
  ADD KEY `tbl_partner_prog_sch_sch_id_foreign` (`sch_id`);

--
-- Indexes for table `tbl_partner_prog_univ`
--
ALTER TABLE `tbl_partner_prog_univ`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_partner_prog_univ_partnerprog_id_foreign` (`partnerprog_id`),
  ADD KEY `tbl_partner_prog_univ_univ_id_foreign` (`univ_id`);

--
-- Indexes for table `tbl_position`
--
ALTER TABLE `tbl_position`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_prog`
--
ALTER TABLE `tbl_prog`
  ADD PRIMARY KEY (`prog_id`) USING BTREE,
  ADD KEY `tbl_prog_sub_prog_id_foreign` (`sub_prog_id`),
  ADD KEY `tbl_prog_main_prog_id_foreign` (`main_prog_id`);

--
-- Indexes for table `tbl_purchase_dtl`
--
ALTER TABLE `tbl_purchase_dtl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_purchase_dtl_purchase_id_foreign` (`purchase_id`);

--
-- Indexes for table `tbl_purchase_request`
--
ALTER TABLE `tbl_purchase_request`
  ADD PRIMARY KEY (`purchase_id`),
  ADD KEY `tbl_purchase_request_purchase_department_foreign` (`purchase_department`),
  ADD KEY `tbl_purchase_request_requested_by_foreign` (`requested_by`);

--
-- Indexes for table `tbl_reason`
--
ALTER TABLE `tbl_reason`
  ADD PRIMARY KEY (`reason_id`);

--
-- Indexes for table `tbl_receipt`
--
ALTER TABLE `tbl_receipt`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tbl_receipt_receipt_id_unique` (`receipt_id`),
  ADD KEY `tbl_receipt_inv_id_foreign` (`inv_id`),
  ADD KEY `tbl_receipt_invdtl_id_foreign` (`invdtl_id`),
  ADD KEY `tbl_receipt_invb2b_id_foreign` (`invb2b_id`);

--
-- Indexes for table `tbl_receipt_attachment`
--
ALTER TABLE `tbl_receipt_attachment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_receipt_attachment_receipt_id_foreign` (`receipt_id`);

--
-- Indexes for table `tbl_referral`
--
ALTER TABLE `tbl_referral`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_referral_partner_id_foreign` (`partner_id`),
  ADD KEY `tbl_referral_prog_id_foreign` (`prog_id`),
  ADD KEY `tbl_referral_empl_id_foreign` (`empl_id`);

--
-- Indexes for table `tbl_refund`
--
ALTER TABLE `tbl_refund`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_refund_invb2b_id_foreign` (`invb2b_id`),
  ADD KEY `tbl_refund_inv_id_foreign` (`inv_id`);

--
-- Indexes for table `tbl_roles`
--
ALTER TABLE `tbl_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_sales_target`
--
ALTER TABLE `tbl_sales_target`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_sales_target_prog_id_foreign` (`prog_id`);

--
-- Indexes for table `tbl_sch`
--
ALTER TABLE `tbl_sch`
  ADD PRIMARY KEY (`sch_id`);

--
-- Indexes for table `tbl_schdetail`
--
ALTER TABLE `tbl_schdetail`
  ADD PRIMARY KEY (`schdetail_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `tbl_sch_curriculum`
--
ALTER TABLE `tbl_sch_curriculum`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_sch_curriculum_sch_id_foreign` (`sch_id`),
  ADD KEY `tbl_sch_curriculum_curriculum_id_foreign` (`curriculum_id`);

--
-- Indexes for table `tbl_sch_event`
--
ALTER TABLE `tbl_sch_event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_sch_event_sch_id_foreign` (`sch_id`),
  ADD KEY `tbl_sch_event_event_id_foreign` (`event_id`);

--
-- Indexes for table `tbl_sch_prog`
--
ALTER TABLE `tbl_sch_prog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_sch_prog_sch_id_foreign` (`sch_id`),
  ADD KEY `tbl_sch_prog_prog_id_foreign` (`prog_id`),
  ADD KEY `tbl_sch_prog_empl_id_foreign` (`empl_id`),
  ADD KEY `tbl_sch_prog_reason_id_foreign` (`reason_id`);

--
-- Indexes for table `tbl_sch_prog_attach`
--
ALTER TABLE `tbl_sch_prog_attach`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_sch_prog_attach_schprog_id_foreign` (`schprog_id`);

--
-- Indexes for table `tbl_sch_prog_partner`
--
ALTER TABLE `tbl_sch_prog_partner`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_sch_prog_partner_schprog_id_foreign` (`schprog_id`),
  ADD KEY `tbl_sch_prog_partner_corp_id_foreign` (`corp_id`);

--
-- Indexes for table `tbl_sch_prog_school`
--
ALTER TABLE `tbl_sch_prog_school`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_sch_prog_school_schprog_id_foreign` (`schprog_id`),
  ADD KEY `tbl_sch_prog_school_sch_id_foreign` (`sch_id`);

--
-- Indexes for table `tbl_sch_prog_univ`
--
ALTER TABLE `tbl_sch_prog_univ`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_sch_prog_univ_schprog_id_foreign` (`schprog_id`),
  ADD KEY `tbl_sch_prog_univ_univ_id_foreign` (`univ_id`);

--
-- Indexes for table `tbl_sch_visit`
--
ALTER TABLE `tbl_sch_visit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_sch_visit_sch_id_foreign` (`sch_id`),
  ADD KEY `tbl_sch_visit_internal_pic_foreign` (`internal_pic`),
  ADD KEY `tbl_sch_visit_school_pic_foreign` (`school_pic`);

--
-- Indexes for table `tbl_scoring_param`
--
ALTER TABLE `tbl_scoring_param`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_speaker`
--
ALTER TABLE `tbl_speaker`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_sub_prog`
--
ALTER TABLE `tbl_sub_prog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_sub_prog_main_prog_id_foreign` (`main_prog_id`);

--
-- Indexes for table `tbl_tag`
--
ALTER TABLE `tbl_tag`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_univ`
--
ALTER TABLE `tbl_univ`
  ADD PRIMARY KEY (`univ_id`),
  ADD KEY `tbl_univ_tag_foreign` (`tag`);

--
-- Indexes for table `tbl_univ_event`
--
ALTER TABLE `tbl_univ_event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_univ_event_event_id_foreign` (`event_id`),
  ADD KEY `tbl_univ_event_univ_id_foreign` (`univ_id`);

--
-- Indexes for table `tbl_univ_pic`
--
ALTER TABLE `tbl_univ_pic`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_univ_pic_univ_id_foreign` (`univ_id`);

--
-- Indexes for table `tbl_user_educations`
--
ALTER TABLE `tbl_user_educations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_user_educations_user_id_foreign` (`user_id`),
  ADD KEY `tbl_user_educations_univ_id_foreign` (`univ_id`),
  ADD KEY `tbl_user_educations_major_id_foreign` (`major_id`);

--
-- Indexes for table `tbl_user_roles`
--
ALTER TABLE `tbl_user_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_user_roles_user_id_foreign` (`user_id`),
  ADD KEY `tbl_user_roles_role_id_foreign` (`role_id`),
  ADD KEY `extended_id` (`extended_id`) USING BTREE;

--
-- Indexes for table `tbl_user_type`
--
ALTER TABLE `tbl_user_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_user_type_detail`
--
ALTER TABLE `tbl_user_type_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_user_type_detail_user_type_id_foreign` (`user_type_id`),
  ADD KEY `tbl_user_type_detail_user_id_foreign` (`user_id`),
  ADD KEY `tbl_user_type_detail_department_id_foreign` (`department_id`);

--
-- Indexes for table `tbl_vendor`
--
ALTER TABLE `tbl_vendor`
  ADD PRIMARY KEY (`vendor_id`);

--
-- Indexes for table `tbl_vendor_type`
--
ALTER TABLE `tbl_vendor_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_volunt`
--
ALTER TABLE `tbl_volunt`
  ADD PRIMARY KEY (`volunt_id`),
  ADD KEY `tbl_volunt_volunt_graduatedfr_foreign` (`volunt_graduatedfr`),
  ADD KEY `tbl_volunt_volunt_major_foreign` (`volunt_major`),
  ADD KEY `tbl_volunt_volunt_position_foreign` (`volunt_position`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `extended_id` (`extended_id`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD UNIQUE KEY `users_uuid_unique` (`uuid`),
  ADD KEY `users_position_id_foreign` (`position_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `indonesia_cities`
--
ALTER TABLE `indonesia_cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `indonesia_districts`
--
ALTER TABLE `indonesia_districts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `indonesia_provinces`
--
ALTER TABLE `indonesia_provinces`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `indonesia_villages`
--
ALTER TABLE `indonesia_villages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_countries`
--
ALTER TABLE `lc_countries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_countries_geographical`
--
ALTER TABLE `lc_countries_geographical`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_countries_translations`
--
ALTER TABLE `lc_countries_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_regions`
--
ALTER TABLE `lc_regions`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc_region_translations`
--
ALTER TABLE `lc_region_translations`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_agenda_speaker`
--
ALTER TABLE `tbl_agenda_speaker`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_asset_returned`
--
ALTER TABLE `tbl_asset_returned`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_asset_used`
--
ALTER TABLE `tbl_asset_used`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_axis`
--
ALTER TABLE `tbl_axis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_client`
--
ALTER TABLE `tbl_client`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_client_abrcountry`
--
ALTER TABLE `tbl_client_abrcountry`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_client_additional_info`
--
ALTER TABLE `tbl_client_additional_info`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_client_event`
--
ALTER TABLE `tbl_client_event`
  MODIFY `clientevent_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_client_mentor`
--
ALTER TABLE `tbl_client_mentor`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_client_prog`
--
ALTER TABLE `tbl_client_prog`
  MODIFY `clientprog_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_client_relation`
--
ALTER TABLE `tbl_client_relation`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_client_roles`
--
ALTER TABLE `tbl_client_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_corp_partner_event`
--
ALTER TABLE `tbl_corp_partner_event`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_corp_pic`
--
ALTER TABLE `tbl_corp_pic`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_curriculum`
--
ALTER TABLE `tbl_curriculum`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_department`
--
ALTER TABLE `tbl_department`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_dreams_major`
--
ALTER TABLE `tbl_dreams_major`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_dreams_uni`
--
ALTER TABLE `tbl_dreams_uni`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_eduf_lead`
--
ALTER TABLE `tbl_eduf_lead`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_eduf_review`
--
ALTER TABLE `tbl_eduf_review`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_event_pic`
--
ALTER TABLE `tbl_event_pic`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_event_speaker`
--
ALTER TABLE `tbl_event_speaker`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_followup`
--
ALTER TABLE `tbl_followup`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_interest_prog`
--
ALTER TABLE `tbl_interest_prog`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_inv`
--
ALTER TABLE `tbl_inv`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_invb2b`
--
ALTER TABLE `tbl_invb2b`
  MODIFY `invb2b_num` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_invdtl`
--
ALTER TABLE `tbl_invdtl`
  MODIFY `invdtl_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_inv_attachment`
--
ALTER TABLE `tbl_inv_attachment`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_lead`
--
ALTER TABLE `tbl_lead`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_login_log`
--
ALTER TABLE `tbl_login_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_main_menus`
--
ALTER TABLE `tbl_main_menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_main_prog`
--
ALTER TABLE `tbl_main_prog`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_major`
--
ALTER TABLE `tbl_major`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_menus`
--
ALTER TABLE `tbl_menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `tbl_menusdtl`
--
ALTER TABLE `tbl_menusdtl`
  MODIFY `menusdtl_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `tbl_menus_user`
--
ALTER TABLE `tbl_menus_user`
  MODIFY `menusdtl_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_partner`
--
ALTER TABLE `tbl_partner`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_partner_agreement`
--
ALTER TABLE `tbl_partner_agreement`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_partner_prog`
--
ALTER TABLE `tbl_partner_prog`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_partner_prog_attachment`
--
ALTER TABLE `tbl_partner_prog_attachment`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_partner_prog_partner`
--
ALTER TABLE `tbl_partner_prog_partner`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_partner_prog_sch`
--
ALTER TABLE `tbl_partner_prog_sch`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_partner_prog_univ`
--
ALTER TABLE `tbl_partner_prog_univ`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_position`
--
ALTER TABLE `tbl_position`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `tbl_purchase_dtl`
--
ALTER TABLE `tbl_purchase_dtl`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_reason`
--
ALTER TABLE `tbl_reason`
  MODIFY `reason_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_receipt`
--
ALTER TABLE `tbl_receipt`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_receipt_attachment`
--
ALTER TABLE `tbl_receipt_attachment`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_referral`
--
ALTER TABLE `tbl_referral`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_refund`
--
ALTER TABLE `tbl_refund`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_roles`
--
ALTER TABLE `tbl_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tbl_sales_target`
--
ALTER TABLE `tbl_sales_target`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_schdetail`
--
ALTER TABLE `tbl_schdetail`
  MODIFY `schdetail_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_sch_curriculum`
--
ALTER TABLE `tbl_sch_curriculum`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_sch_event`
--
ALTER TABLE `tbl_sch_event`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_sch_prog`
--
ALTER TABLE `tbl_sch_prog`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_sch_prog_attach`
--
ALTER TABLE `tbl_sch_prog_attach`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_sch_prog_partner`
--
ALTER TABLE `tbl_sch_prog_partner`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_sch_prog_school`
--
ALTER TABLE `tbl_sch_prog_school`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_sch_prog_univ`
--
ALTER TABLE `tbl_sch_prog_univ`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_sch_visit`
--
ALTER TABLE `tbl_sch_visit`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_scoring_param`
--
ALTER TABLE `tbl_scoring_param`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_speaker`
--
ALTER TABLE `tbl_speaker`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_sub_prog`
--
ALTER TABLE `tbl_sub_prog`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tbl_tag`
--
ALTER TABLE `tbl_tag`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_univ_event`
--
ALTER TABLE `tbl_univ_event`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_univ_pic`
--
ALTER TABLE `tbl_univ_pic`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_user_educations`
--
ALTER TABLE `tbl_user_educations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_user_roles`
--
ALTER TABLE `tbl_user_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=851;

--
-- AUTO_INCREMENT for table `tbl_user_type`
--
ALTER TABLE `tbl_user_type`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_user_type_detail`
--
ALTER TABLE `tbl_user_type_detail`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_vendor_type`
--
ALTER TABLE `tbl_vendor_type`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `indonesia_cities`
--
ALTER TABLE `indonesia_cities`
  ADD CONSTRAINT `indonesia_cities_province_code_foreign` FOREIGN KEY (`province_code`) REFERENCES `indonesia_provinces` (`code`) ON UPDATE CASCADE;

--
-- Constraints for table `indonesia_districts`
--
ALTER TABLE `indonesia_districts`
  ADD CONSTRAINT `indonesia_districts_city_code_foreign` FOREIGN KEY (`city_code`) REFERENCES `indonesia_cities` (`code`) ON UPDATE CASCADE;

--
-- Constraints for table `indonesia_villages`
--
ALTER TABLE `indonesia_villages`
  ADD CONSTRAINT `indonesia_villages_district_code_foreign` FOREIGN KEY (`district_code`) REFERENCES `indonesia_districts` (`code`) ON UPDATE CASCADE;

--
-- Constraints for table `lc_countries`
--
ALTER TABLE `lc_countries`
  ADD CONSTRAINT `lc_countries_lc_region_id_foreign` FOREIGN KEY (`lc_region_id`) REFERENCES `lc_regions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lc_countries_geographical`
--
ALTER TABLE `lc_countries_geographical`
  ADD CONSTRAINT `lc_countries_geographical_lc_country_id_foreign` FOREIGN KEY (`lc_country_id`) REFERENCES `lc_countries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lc_countries_translations`
--
ALTER TABLE `lc_countries_translations`
  ADD CONSTRAINT `lc_countries_translations_lc_country_id_foreign` FOREIGN KEY (`lc_country_id`) REFERENCES `lc_countries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lc_region_translations`
--
ALTER TABLE `lc_region_translations`
  ADD CONSTRAINT `lc_region_translations_lc_region_id_foreign` FOREIGN KEY (`lc_region_id`) REFERENCES `lc_regions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_agenda_speaker`
--
ALTER TABLE `tbl_agenda_speaker`
  ADD CONSTRAINT `tbl_agenda_speaker_eduf_id_foreign` FOREIGN KEY (`eduf_id`) REFERENCES `tbl_eduf_lead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_agenda_speaker_empl_id_foreign` FOREIGN KEY (`empl_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_agenda_speaker_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `tbl_events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_agenda_speaker_partner_pic_id_foreign` FOREIGN KEY (`partner_pic_id`) REFERENCES `tbl_corp_pic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_agenda_speaker_partner_prog_id_foreign` FOREIGN KEY (`partner_prog_id`) REFERENCES `tbl_partner_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_agenda_speaker_sch_pic_id_foreign` FOREIGN KEY (`sch_pic_id`) REFERENCES `tbl_schdetail` (`schdetail_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_agenda_speaker_sch_prog_id_foreign` FOREIGN KEY (`sch_prog_id`) REFERENCES `tbl_sch_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_agenda_speaker_univ_pic_id_foreign` FOREIGN KEY (`univ_pic_id`) REFERENCES `tbl_univ_pic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_asset_returned`
--
ALTER TABLE `tbl_asset_returned`
  ADD CONSTRAINT `tbl_asset_returned_asset_used_id_foreign` FOREIGN KEY (`asset_used_id`) REFERENCES `tbl_asset_used` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_asset_used`
--
ALTER TABLE `tbl_asset_used`
  ADD CONSTRAINT `tbl_asset_used_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `tbl_asset` (`asset_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_asset_used_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_client`
--
ALTER TABLE `tbl_client`
  ADD CONSTRAINT `tbl_client_eduf_id_foreign` FOREIGN KEY (`eduf_id`) REFERENCES `tbl_eduf_lead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `tbl_events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `tbl_lead` (`lead_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_sch_id_foreign` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tbl_client_abrcountry`
--
ALTER TABLE `tbl_client_abrcountry`
  ADD CONSTRAINT `tbl_client_abrcountry_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_abrcountry_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tbl_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_client_additional_info`
--
ALTER TABLE `tbl_client_additional_info`
  ADD CONSTRAINT `tbl_client_additional_info_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_client_event`
--
ALTER TABLE `tbl_client_event`
  ADD CONSTRAINT `tbl_client_event_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_event_eduf_id_foreign` FOREIGN KEY (`eduf_id`) REFERENCES `tbl_eduf_lead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_event_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `tbl_events` (`event_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_event_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `tbl_lead` (`lead_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_event_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `tbl_corp` (`corp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_client_mentor`
--
ALTER TABLE `tbl_client_mentor`
  ADD CONSTRAINT `tbl_client_mentor_clientprog_id_foreign` FOREIGN KEY (`clientprog_id`) REFERENCES `tbl_client_prog` (`clientprog_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_mentor_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tbl_client_prog`
--
ALTER TABLE `tbl_client_prog`
  ADD CONSTRAINT `tbl_client_prog_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_prog_clientevent_id_foreign` FOREIGN KEY (`clientevent_id`) REFERENCES `tbl_client_event` (`clientevent_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_prog_eduf_lead_id_foreign` FOREIGN KEY (`eduf_lead_id`) REFERENCES `tbl_eduf_lead` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_prog_empl_id_foreign` FOREIGN KEY (`empl_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_prog_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `tbl_lead` (`lead_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_prog_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `tbl_corp` (`corp_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_prog_prog_id_foreign` FOREIGN KEY (`prog_id`) REFERENCES `tbl_prog` (`prog_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_prog_reason_id_foreign` FOREIGN KEY (`reason_id`) REFERENCES `tbl_reason` (`reason_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_client_relation`
--
ALTER TABLE `tbl_client_relation`
  ADD CONSTRAINT `tbl_client_relation_child_id_foreign` FOREIGN KEY (`child_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_relation_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_client_roles`
--
ALTER TABLE `tbl_client_roles`
  ADD CONSTRAINT `tbl_client_roles_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_client_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `tbl_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_corp_partner_event`
--
ALTER TABLE `tbl_corp_partner_event`
  ADD CONSTRAINT `tbl_corp_partner_event_corp_id_foreign` FOREIGN KEY (`corp_id`) REFERENCES `tbl_corp` (`corp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_corp_partner_event_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `tbl_events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_corp_pic`
--
ALTER TABLE `tbl_corp_pic`
  ADD CONSTRAINT `tbl_corp_pic_corp_id_foreign` FOREIGN KEY (`corp_id`) REFERENCES `tbl_corp` (`corp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_dreams_major`
--
ALTER TABLE `tbl_dreams_major`
  ADD CONSTRAINT `tbl_dreams_major_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_dreams_major_major_id_foreign` FOREIGN KEY (`major_id`) REFERENCES `tbl_major` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_dreams_uni`
--
ALTER TABLE `tbl_dreams_uni`
  ADD CONSTRAINT `tbl_dreams_uni_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_dreams_uni_univ_id_foreign` FOREIGN KEY (`univ_id`) REFERENCES `tbl_univ` (`univ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_eduf_lead`
--
ALTER TABLE `tbl_eduf_lead`
  ADD CONSTRAINT `tbl_eduf_lead_corp_id_foreign` FOREIGN KEY (`corp_id`) REFERENCES `tbl_corp` (`corp_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_eduf_lead_intr_pic_foreign` FOREIGN KEY (`intr_pic`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_eduf_lead_sch_id_foreign` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tbl_eduf_review`
--
ALTER TABLE `tbl_eduf_review`
  ADD CONSTRAINT `tbl_eduf_review_eduf_id_foreign` FOREIGN KEY (`eduf_id`) REFERENCES `tbl_eduf_lead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_eduf_review_reviewer_name_foreign` FOREIGN KEY (`reviewer_name`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_event_pic`
--
ALTER TABLE `tbl_event_pic`
  ADD CONSTRAINT `tbl_event_pic_empl_id_foreign` FOREIGN KEY (`empl_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_event_pic_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `tbl_events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_event_speaker`
--
ALTER TABLE `tbl_event_speaker`
  ADD CONSTRAINT `tbl_event_speaker_corp_pic_id_foreign` FOREIGN KEY (`corp_pic_id`) REFERENCES `tbl_corp_pic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_event_speaker_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `tbl_events` (`event_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_event_speaker_sch_pic_id_foreign` FOREIGN KEY (`sch_pic_id`) REFERENCES `tbl_schdetail` (`schdetail_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_event_speaker_univ_pic_id_foreign` FOREIGN KEY (`univ_pic_id`) REFERENCES `tbl_univ_pic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_followup`
--
ALTER TABLE `tbl_followup`
  ADD CONSTRAINT `tbl_followup_clientprog_id_foreign` FOREIGN KEY (`clientprog_id`) REFERENCES `tbl_client_prog` (`clientprog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_interest_prog`
--
ALTER TABLE `tbl_interest_prog`
  ADD CONSTRAINT `tbl_interest_prog_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_interest_prog_prog_id_foreign` FOREIGN KEY (`prog_id`) REFERENCES `tbl_prog` (`prog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_inv`
--
ALTER TABLE `tbl_inv`
  ADD CONSTRAINT `tbl_inv_clientprog_id_foreign` FOREIGN KEY (`clientprog_id`) REFERENCES `tbl_client_prog` (`clientprog_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_inv_ref_id_foreign` FOREIGN KEY (`ref_id`) REFERENCES `tbl_referral` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_invb2b`
--
ALTER TABLE `tbl_invb2b`
  ADD CONSTRAINT `tbl_invb2b_partnerprog_id_foreign` FOREIGN KEY (`partnerprog_id`) REFERENCES `tbl_partner_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_invb2b_ref_id_foreign` FOREIGN KEY (`ref_id`) REFERENCES `tbl_referral` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_invb2b_schprog_id_foreign` FOREIGN KEY (`schprog_id`) REFERENCES `tbl_sch_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_invdtl`
--
ALTER TABLE `tbl_invdtl`
  ADD CONSTRAINT `tbl_invdtl_inv_id_foreign` FOREIGN KEY (`inv_id`) REFERENCES `tbl_inv` (`inv_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_invdtl_invb2b_id_foreign` FOREIGN KEY (`invb2b_id`) REFERENCES `tbl_invb2b` (`invb2b_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_inv_attachment`
--
ALTER TABLE `tbl_inv_attachment`
  ADD CONSTRAINT `tbl_inv_attachment_inv_id_foreign` FOREIGN KEY (`inv_id`) REFERENCES `tbl_inv` (`inv_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_inv_attachment_invb2b_id_foreign` FOREIGN KEY (`invb2b_id`) REFERENCES `tbl_invb2b` (`invb2b_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_login_log`
--
ALTER TABLE `tbl_login_log`
  ADD CONSTRAINT `tbl_login_log_user_type_id_foreign` FOREIGN KEY (`user_type_id`) REFERENCES `tbl_user_type_detail` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_menus`
--
ALTER TABLE `tbl_menus`
  ADD CONSTRAINT `tbl_menus_mainmenu_id_foreign` FOREIGN KEY (`mainmenu_id`) REFERENCES `tbl_main_menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_menusdtl`
--
ALTER TABLE `tbl_menusdtl`
  ADD CONSTRAINT `tbl_menusdtl_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `tbl_department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_menusdtl_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `tbl_menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_menus_user`
--
ALTER TABLE `tbl_menus_user`
  ADD CONSTRAINT `tbl_menus_user_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `tbl_menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_menus_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_partner_agreement`
--
ALTER TABLE `tbl_partner_agreement`
  ADD CONSTRAINT `tbl_partner_agreement_corp_id_foreign` FOREIGN KEY (`corp_id`) REFERENCES `tbl_corp` (`corp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_partner_agreement_corp_pic_foreign` FOREIGN KEY (`corp_pic`) REFERENCES `tbl_corp_pic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_partner_agreement_empl_id_foreign` FOREIGN KEY (`empl_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_partner_prog`
--
ALTER TABLE `tbl_partner_prog`
  ADD CONSTRAINT `tbl_partner_prog_corp_id_foreign` FOREIGN KEY (`corp_id`) REFERENCES `tbl_corp` (`corp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_partner_prog_empl_id_foreign` FOREIGN KEY (`empl_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_partner_prog_prog_id_foreign` FOREIGN KEY (`prog_id`) REFERENCES `tbl_prog` (`prog_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_partner_prog_reason_id_foreign` FOREIGN KEY (`reason_id`) REFERENCES `tbl_reason` (`reason_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_partner_prog_attachment`
--
ALTER TABLE `tbl_partner_prog_attachment`
  ADD CONSTRAINT `tbl_partner_prog_attachment_partner_prog_id_foreign` FOREIGN KEY (`partner_prog_id`) REFERENCES `tbl_partner_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_partner_prog_partner`
--
ALTER TABLE `tbl_partner_prog_partner`
  ADD CONSTRAINT `tbl_partner_prog_partner_corp_id_foreign` FOREIGN KEY (`corp_id`) REFERENCES `tbl_corp` (`corp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_partner_prog_partner_partnerprog_id_foreign` FOREIGN KEY (`partnerprog_id`) REFERENCES `tbl_partner_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_partner_prog_sch`
--
ALTER TABLE `tbl_partner_prog_sch`
  ADD CONSTRAINT `tbl_partner_prog_sch_partnerprog_id_foreign` FOREIGN KEY (`partnerprog_id`) REFERENCES `tbl_partner_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_partner_prog_sch_sch_id_foreign` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_partner_prog_univ`
--
ALTER TABLE `tbl_partner_prog_univ`
  ADD CONSTRAINT `tbl_partner_prog_univ_partnerprog_id_foreign` FOREIGN KEY (`partnerprog_id`) REFERENCES `tbl_partner_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_partner_prog_univ_univ_id_foreign` FOREIGN KEY (`univ_id`) REFERENCES `tbl_univ` (`univ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_prog`
--
ALTER TABLE `tbl_prog`
  ADD CONSTRAINT `tbl_prog_main_prog_id_foreign` FOREIGN KEY (`main_prog_id`) REFERENCES `tbl_main_prog` (`id`),
  ADD CONSTRAINT `tbl_prog_sub_prog_id_foreign` FOREIGN KEY (`sub_prog_id`) REFERENCES `tbl_sub_prog` (`id`);

--
-- Constraints for table `tbl_purchase_dtl`
--
ALTER TABLE `tbl_purchase_dtl`
  ADD CONSTRAINT `tbl_purchase_dtl_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `tbl_purchase_request` (`purchase_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_purchase_request`
--
ALTER TABLE `tbl_purchase_request`
  ADD CONSTRAINT `tbl_purchase_request_purchase_department_foreign` FOREIGN KEY (`purchase_department`) REFERENCES `tbl_department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_purchase_request_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_receipt`
--
ALTER TABLE `tbl_receipt`
  ADD CONSTRAINT `tbl_receipt_inv_id_foreign` FOREIGN KEY (`inv_id`) REFERENCES `tbl_inv` (`inv_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_receipt_invb2b_id_foreign` FOREIGN KEY (`invb2b_id`) REFERENCES `tbl_invb2b` (`invb2b_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_receipt_invdtl_id_foreign` FOREIGN KEY (`invdtl_id`) REFERENCES `tbl_invdtl` (`invdtl_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_receipt_attachment`
--
ALTER TABLE `tbl_receipt_attachment`
  ADD CONSTRAINT `tbl_receipt_attachment_receipt_id_foreign` FOREIGN KEY (`receipt_id`) REFERENCES `tbl_receipt` (`receipt_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_referral`
--
ALTER TABLE `tbl_referral`
  ADD CONSTRAINT `tbl_referral_empl_id_foreign` FOREIGN KEY (`empl_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_referral_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `tbl_corp` (`corp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_referral_prog_id_foreign` FOREIGN KEY (`prog_id`) REFERENCES `tbl_prog` (`prog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_refund`
--
ALTER TABLE `tbl_refund`
  ADD CONSTRAINT `tbl_refund_inv_id_foreign` FOREIGN KEY (`inv_id`) REFERENCES `tbl_inv` (`inv_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_refund_invb2b_id_foreign` FOREIGN KEY (`invb2b_id`) REFERENCES `tbl_invb2b` (`invb2b_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_sales_target`
--
ALTER TABLE `tbl_sales_target`
  ADD CONSTRAINT `tbl_sales_target_prog_id_foreign` FOREIGN KEY (`prog_id`) REFERENCES `tbl_prog` (`prog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_schdetail`
--
ALTER TABLE `tbl_schdetail`
  ADD CONSTRAINT `tbl_schdetail_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_sch_curriculum`
--
ALTER TABLE `tbl_sch_curriculum`
  ADD CONSTRAINT `tbl_sch_curriculum_curriculum_id_foreign` FOREIGN KEY (`curriculum_id`) REFERENCES `tbl_curriculum` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_sch_curriculum_sch_id_foreign` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_sch_event`
--
ALTER TABLE `tbl_sch_event`
  ADD CONSTRAINT `tbl_sch_event_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `tbl_events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_sch_event_sch_id_foreign` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_sch_prog`
--
ALTER TABLE `tbl_sch_prog`
  ADD CONSTRAINT `tbl_sch_prog_empl_id_foreign` FOREIGN KEY (`empl_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_sch_prog_prog_id_foreign` FOREIGN KEY (`prog_id`) REFERENCES `tbl_prog` (`prog_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_sch_prog_reason_id_foreign` FOREIGN KEY (`reason_id`) REFERENCES `tbl_reason` (`reason_id`),
  ADD CONSTRAINT `tbl_sch_prog_sch_id_foreign` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_sch_prog_attach`
--
ALTER TABLE `tbl_sch_prog_attach`
  ADD CONSTRAINT `tbl_sch_prog_attach_schprog_id_foreign` FOREIGN KEY (`schprog_id`) REFERENCES `tbl_sch_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_sch_prog_partner`
--
ALTER TABLE `tbl_sch_prog_partner`
  ADD CONSTRAINT `tbl_sch_prog_partner_corp_id_foreign` FOREIGN KEY (`corp_id`) REFERENCES `tbl_corp` (`corp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_sch_prog_partner_schprog_id_foreign` FOREIGN KEY (`schprog_id`) REFERENCES `tbl_sch_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_sch_prog_school`
--
ALTER TABLE `tbl_sch_prog_school`
  ADD CONSTRAINT `tbl_sch_prog_school_sch_id_foreign` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_sch_prog_school_schprog_id_foreign` FOREIGN KEY (`schprog_id`) REFERENCES `tbl_sch_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_sch_prog_univ`
--
ALTER TABLE `tbl_sch_prog_univ`
  ADD CONSTRAINT `tbl_sch_prog_univ_schprog_id_foreign` FOREIGN KEY (`schprog_id`) REFERENCES `tbl_sch_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_sch_prog_univ_univ_id_foreign` FOREIGN KEY (`univ_id`) REFERENCES `tbl_univ` (`univ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_sch_visit`
--
ALTER TABLE `tbl_sch_visit`
  ADD CONSTRAINT `tbl_sch_visit_internal_pic_foreign` FOREIGN KEY (`internal_pic`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_sch_visit_sch_id_foreign` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_sch_visit_school_pic_foreign` FOREIGN KEY (`school_pic`) REFERENCES `tbl_schdetail` (`schdetail_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tbl_sub_prog`
--
ALTER TABLE `tbl_sub_prog`
  ADD CONSTRAINT `tbl_sub_prog_main_prog_id_foreign` FOREIGN KEY (`main_prog_id`) REFERENCES `tbl_main_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_univ`
--
ALTER TABLE `tbl_univ`
  ADD CONSTRAINT `tbl_univ_tag_foreign` FOREIGN KEY (`tag`) REFERENCES `tbl_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_univ_event`
--
ALTER TABLE `tbl_univ_event`
  ADD CONSTRAINT `tbl_univ_event_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `tbl_events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_univ_event_univ_id_foreign` FOREIGN KEY (`univ_id`) REFERENCES `tbl_univ` (`univ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_univ_pic`
--
ALTER TABLE `tbl_univ_pic`
  ADD CONSTRAINT `tbl_univ_pic_univ_id_foreign` FOREIGN KEY (`univ_id`) REFERENCES `tbl_univ` (`univ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_user_educations`
--
ALTER TABLE `tbl_user_educations`
  ADD CONSTRAINT `tbl_user_educations_major_id_foreign` FOREIGN KEY (`major_id`) REFERENCES `tbl_major` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_user_educations_univ_id_foreign` FOREIGN KEY (`univ_id`) REFERENCES `tbl_univ` (`univ_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_user_educations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_user_roles`
--
ALTER TABLE `tbl_user_roles`
  ADD CONSTRAINT `tbl_user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `tbl_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_user_type_detail`
--
ALTER TABLE `tbl_user_type_detail`
  ADD CONSTRAINT `tbl_user_type_detail_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `tbl_department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_user_type_detail_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_user_type_detail_user_type_id_foreign` FOREIGN KEY (`user_type_id`) REFERENCES `tbl_user_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_volunt`
--
ALTER TABLE `tbl_volunt`
  ADD CONSTRAINT `tbl_volunt_volunt_graduatedfr_foreign` FOREIGN KEY (`volunt_graduatedfr`) REFERENCES `tbl_univ` (`univ_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_volunt_volunt_major_foreign` FOREIGN KEY (`volunt_major`) REFERENCES `tbl_major` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_volunt_volunt_position_foreign` FOREIGN KEY (`volunt_position`) REFERENCES `tbl_position` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `tbl_position` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
