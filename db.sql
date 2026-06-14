-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.16.0.7229
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for db_silat 
CREATE DATABASE IF NOT EXISTS `db_silat` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `db_silat`;

-- Dumping structure for table latihan.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table latihan.cache: ~0 rows (approximately)

-- Dumping structure for table latihan.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table latihan.cache_locks: ~0 rows (approximately)

-- Dumping structure for table latihan.detail_peserta_tanding
CREATE TABLE IF NOT EXISTS `detail_peserta_tanding` (
  `id_detail` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_partai` bigint(20) unsigned NOT NULL,
  `id_peserta` bigint(20) unsigned NOT NULL,
  `no_urut` int(11) DEFAULT NULL,
  `sudut` varchar(255) DEFAULT NULL,
  `nilai_median` decimal(16,15) NOT NULL DEFAULT 0.000000000000000,
  `nilai_deviasi` decimal(16,15) NOT NULL DEFAULT 0.000000000000000,
  `hukuman_ketua` decimal(5,3) NOT NULL DEFAULT 0.000,
  `total_nilai` decimal(5,3) NOT NULL DEFAULT 0.000,
  `waktu_tampil` int(11) NOT NULL DEFAULT 0,
  `is_timer_running` tinyint(1) NOT NULL DEFAULT 0,
  `is_disqualifikasi` tinyint(1) NOT NULL DEFAULT 0,
  `status_mulai` tinyint(1) NOT NULL DEFAULT 0,
  `is_pemenang` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `detail_peserta_tanding_id_partai_foreign` (`id_partai`),
  KEY `detail_peserta_tanding_id_peserta_foreign` (`id_peserta`),
  CONSTRAINT `detail_peserta_tanding_id_partai_foreign` FOREIGN KEY (`id_partai`) REFERENCES `partai` (`id_partai`) ON DELETE CASCADE,
  CONSTRAINT `detail_peserta_tanding_id_peserta_foreign` FOREIGN KEY (`id_peserta`) REFERENCES `peserta` (`id_peserta`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table latihan.detail_peserta_tanding: ~11 rows (approximately)
INSERT INTO `detail_peserta_tanding` (`id_detail`, `id_partai`, `id_peserta`, `no_urut`, `sudut`, `nilai_median`, `nilai_deviasi`, `hukuman_ketua`, `total_nilai`, `waktu_tampil`, `is_timer_running`, `is_disqualifikasi`, `status_mulai`, `is_pemenang`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, NULL, 'Biru', 9.100000000000000, 0.000000000000000, 0.000, 9.100, 42, 0, 0, 2, 1, '2026-06-03 03:24:19', '2026-06-03 03:25:57'),
	(2, 1, 2, NULL, 'Merah', 9.100000000000000, 0.000000000000000, 0.500, 8.600, 9, 0, 0, 2, 0, '2026-06-03 03:24:19', '2026-06-03 03:38:29'),
	(12, 3, 12, 1, NULL, 9.660000000000000, 0.265894716006167, 0.500, 9.160, 121, 0, 0, 2, 0, '2026-06-11 02:43:04', '2026-06-12 00:22:54'),
	(13, 3, 13, 2, NULL, 9.765000000000000, 0.291933211539900, 0.000, 9.765, 14, 0, 0, 2, 0, '2026-06-11 02:43:04', '2026-06-12 00:27:00'),
	(14, 3, 14, 3, NULL, 9.880000000000000, 0.047434164902526, 0.000, 9.880, 117, 0, 0, 2, 0, '2026-06-11 02:43:04', '2026-06-12 00:48:58'),
	(15, 4, 15, 1, NULL, 9.455000000000000, 0.237841859225831, 0.000, 9.455, 178, 0, 0, 2, 0, '2026-06-13 05:39:24', '2026-06-13 05:39:47'),
	(16, 4, 16, 2, NULL, 0.000000000000000, 0.000000000000000, 0.000, 0.000, 0, 0, 0, 0, 0, '2026-06-13 05:39:24', '2026-06-13 05:39:24'),
	(17, 4, 17, 3, NULL, 0.000000000000000, 0.000000000000000, 0.000, 0.000, 0, 0, 0, 0, 0, '2026-06-13 05:39:24', '2026-06-13 05:39:24'),
	(27, 2, 27, 1, NULL, 0.000000000000000, 0.000000000000000, 0.000, 9.000, 200, 0, 1, 2, 0, '2026-06-13 06:55:24', '2026-06-13 07:56:32'),
	(28, 2, 28, 2, NULL, 0.000000000000000, 0.000000000000000, 0.000, 9.000, 0, 0, 0, 2, 0, '2026-06-13 06:55:24', '2026-06-13 07:56:32'),
	(29, 2, 29, 3, NULL, 0.000000000000000, 0.000000000000000, 0.000, 0.000, 0, 0, 0, 2, 0, '2026-06-13 06:55:24', '2026-06-13 07:56:32'),
	(32, 2, 32, 4, NULL, 0.000000000000000, 0.000000000000000, 0.000, 0.000, 0, 0, 0, 0, 0, '2026-06-13 22:51:30', '2026-06-13 22:51:30');

-- Dumping structure for table latihan.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table latihan.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table latihan.input_nilai_juri
CREATE TABLE IF NOT EXISTS `input_nilai_juri` (
  `id_nilai` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_detail` bigint(20) unsigned NOT NULL,
  `no_juri` int(11) NOT NULL,
  `nilai_a` decimal(5,2) NOT NULL DEFAULT 0.00,
  `nilai_b` decimal(5,2) NOT NULL DEFAULT 0.00,
  `nilai_c` decimal(5,2) DEFAULT 0.00,
  `skor_akhir_juri` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_nilai`),
  KEY `input_nilai_juri_id_detail_foreign` (`id_detail`),
  CONSTRAINT `input_nilai_juri_id_detail_foreign` FOREIGN KEY (`id_detail`) REFERENCES `detail_peserta_tanding` (`id_detail`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table latihan.input_nilai_juri: ~36 rows (approximately)
INSERT INTO `input_nilai_juri` (`id_nilai`, `id_detail`, `no_juri`, `nilai_a`, `nilai_b`, `nilai_c`, `skor_akhir_juri`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 0.00, 0.00, 0.00, 9.10, NULL, NULL),
	(2, 1, 2, 0.00, 0.00, 0.00, 9.10, NULL, NULL),
	(3, 1, 3, 0.00, 0.00, 0.00, 9.10, NULL, NULL),
	(4, 1, 4, 0.00, 0.00, 0.00, 9.10, NULL, NULL),
	(5, 1, 5, 0.00, 0.00, 0.00, 9.10, NULL, NULL),
	(6, 1, 6, 0.00, 0.00, 0.00, 9.10, NULL, NULL),
	(7, 2, 1, 0.00, 0.00, 0.00, 9.10, NULL, NULL),
	(8, 2, 2, 0.00, 0.00, 0.00, 9.10, NULL, NULL),
	(9, 2, 3, 0.00, 0.00, 0.00, 9.10, NULL, NULL),
	(10, 2, 4, 0.00, 0.00, 0.00, 9.10, NULL, NULL),
	(11, 2, 5, 0.00, 0.00, 0.00, 9.10, NULL, NULL),
	(12, 2, 6, 0.00, 0.00, 0.00, 9.10, NULL, NULL),
	(43, 12, 1, 0.24, 0.21, 0.23, 9.78, '2026-06-12 00:22:55', '2026-06-12 00:25:29'),
	(44, 12, 2, 0.17, 0.20, 0.23, 9.70, '2026-06-12 00:22:55', '2026-06-12 00:26:07'),
	(45, 12, 3, 0.18, 0.15, 0.19, 9.62, '2026-06-12 00:22:55', '2026-06-12 00:26:28'),
	(46, 12, 4, 0.00, 0.00, 0.00, 9.10, '2026-06-12 00:22:55', '2026-06-12 00:22:55'),
	(47, 12, 5, 0.00, 0.00, 0.00, 9.10, '2026-06-12 00:22:55', '2026-06-12 00:22:55'),
	(48, 12, 6, 0.00, 0.00, 0.00, 9.10, '2026-06-12 00:22:55', '2026-06-12 00:22:55'),
	(49, 13, 1, 0.00, 0.00, 0.00, 9.10, '2026-06-12 00:27:00', '2026-06-12 00:27:00'),
	(50, 13, 2, 0.25, 0.21, 0.22, 9.78, '2026-06-12 00:27:00', '2026-06-12 00:29:09'),
	(51, 13, 3, 0.16, 0.24, 0.25, 9.75, '2026-06-12 00:27:00', '2026-06-12 00:28:04'),
	(52, 13, 4, 0.20, 0.27, 0.22, 9.79, '2026-06-12 00:27:00', '2026-06-12 00:28:27'),
	(53, 13, 5, 0.00, 0.00, 0.00, 9.10, '2026-06-12 00:27:00', '2026-06-12 00:27:00'),
	(54, 13, 6, 0.00, 0.00, 0.00, 9.10, '2026-06-12 00:27:00', '2026-06-12 00:27:00'),
	(55, 14, 1, 0.25, 0.28, 0.28, 9.91, '2026-06-12 00:48:59', '2026-06-12 00:56:55'),
	(56, 14, 2, 0.28, 0.28, 0.28, 9.94, '2026-06-12 00:48:59', '2026-06-12 00:56:33'),
	(57, 14, 3, 0.26, 0.28, 0.21, 9.85, '2026-06-12 00:48:59', '2026-06-12 00:57:38'),
	(58, 14, 4, 0.28, 0.22, 0.22, 9.82, '2026-06-12 00:48:59', '2026-06-12 00:58:06'),
	(59, 14, 5, 0.00, 0.00, 0.00, 9.10, '2026-06-12 00:48:59', '2026-06-12 00:48:59'),
	(60, 14, 6, 0.00, 0.00, 0.00, 9.10, '2026-06-12 00:48:59', '2026-06-12 00:48:59'),
	(67, 15, 1, 0.04, 0.04, 0.02, 9.20, '2026-06-13 05:39:48', '2026-06-13 06:06:41'),
	(68, 15, 2, 0.20, 0.19, 0.19, 9.68, '2026-06-13 05:39:48', '2026-06-13 06:14:35'),
	(69, 15, 3, 0.05, 0.04, 0.04, 9.23, '2026-06-13 05:39:48', '2026-06-13 06:15:14'),
	(70, 15, 4, 0.22, 0.17, 0.21, 9.70, '2026-06-13 05:39:48', '2026-06-13 06:06:28'),
	(71, 15, 5, 0.00, 0.00, 0.00, 9.10, '2026-06-13 05:39:48', '2026-06-13 05:39:48'),
	(72, 15, 6, 0.00, 0.00, 0.00, 9.10, '2026-06-13 05:39:48', '2026-06-13 05:39:48');

-- Dumping structure for table latihan.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table latihan.job_batches: ~0 rows (approximately)

-- Dumping structure for table latihan.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table latihan.jobs: ~0 rows (approximately)

-- Dumping structure for table latihan.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table latihan.migrations: ~11 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_04_20_080505_ref_kategori', 1),
	(5, '2026_04_20_080639_ref_kelas', 1),
	(6, '2026_04_20_080706_peserta', 1),
	(7, '2026_04_20_080730_partai', 1),
	(8, '2026_04_20_080751_status_kontrol_juri', 1),
	(9, '2026_04_20_080816_detail_peserta_tanding', 1),
	(10, '2026_04_20_081138_input_nilai_juri', 1),
	(11, '2026_04_20_094224_create_partais_table', 1),
	(12, '2026_04_21_063401_add_no_urut_to_detail_peserta_tanding', 2),
	(13, '2026_04_29_125357_add_is_timer_running_to_detail_peserta_tanding_table', 3),
	(14, '2026_05_13_095628_add_role_to_users_table', 4);

-- Dumping structure for table latihan.partai
CREATE TABLE IF NOT EXISTS `partai` (
  `id_partai` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_kategori` bigint(20) unsigned NOT NULL,
  `id_kelas` bigint(20) unsigned NOT NULL,
  `no_partai` varchar(10) NOT NULL,
  `babak` varchar(255) DEFAULT NULL,
  `gelanggang` varchar(255) DEFAULT NULL,
  `status_pertandingan` varchar(255) NOT NULL DEFAULT 'Waiting',
  `timer_live` varchar(255) NOT NULL DEFAULT '03:00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_partai`),
  KEY `partai_id_kategori_foreign` (`id_kategori`),
  KEY `partai_id_kelas_foreign` (`id_kelas`),
  CONSTRAINT `partai_id_kategori_foreign` FOREIGN KEY (`id_kategori`) REFERENCES `ref_kategori` (`id_kategori`),
  CONSTRAINT `partai_id_kelas_foreign` FOREIGN KEY (`id_kelas`) REFERENCES `ref_kelas` (`id_kelas`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table latihan.partai: ~2 rows (approximately)
INSERT INTO `partai` (`id_partai`, `id_kategori`, `id_kelas`, `no_partai`, `babak`, `gelanggang`, `status_pertandingan`, `timer_live`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, '1', 'Penyisihan', 'A', 'Waiting', '03:00', '2026-06-03 03:24:19', '2026-06-03 03:24:19'),
	(2, 1, 2, 'POOL B', 'Final', 'A', 'Waiting', '03:00', '2026-06-03 03:24:42', '2026-06-03 03:24:42'),
	(3, 2, 1, 'POOL C', 'Final', 'A', 'Waiting', '03:00', '2026-06-05 04:56:24', '2026-06-05 04:56:24'),
	(4, 2, 1, 'POOL D', 'Final', 'A', 'Waiting', '03:00', '2026-06-11 02:40:00', '2026-06-11 02:40:00');

-- Dumping structure for table latihan.partais
CREATE TABLE IF NOT EXISTS `partais` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table latihan.partais: ~0 rows (approximately)

-- Dumping structure for table latihan.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table latihan.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table latihan.peserta
CREATE TABLE IF NOT EXISTS `peserta` (
  `id_peserta` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_peserta` varchar(255) NOT NULL,
  `kontingen` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_peserta`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table latihan.peserta: ~12 rows (approximately)
INSERT INTO `peserta` (`id_peserta`, `nama_peserta`, `kontingen`, `created_at`, `updated_at`) VALUES
	(1, 'RIAN HIDAYAT', 'UNIVERSITAS PAMULANG', '2026-06-03 03:24:19', '2026-06-03 03:24:19'),
	(2, 'M. RIZAL\'UDIN', 'SMKN 2 PANDEGLANG', '2026-06-03 03:24:19', '2026-06-03 03:24:19'),
	(12, 'RIAN HIDAYAT & UJANG', 'UNIVERSITAS PAMULANG', '2026-06-11 02:43:04', '2026-06-11 02:43:04'),
	(13, 'RIZALUDIN & TB HERU', 'SMKN 2 PANDEGLANG', '2026-06-11 02:43:04', '2026-06-11 02:43:04'),
	(14, 'MAWARID & SEPTYAN', 'SMKN 1 PANDEGLANG', '2026-06-11 02:43:04', '2026-06-11 02:43:04'),
	(15, 'ASEP  & SAEP', 'MAN 1 PDG', '2026-06-13 05:39:24', '2026-06-13 05:39:24'),
	(16, 'HENDRA', 'SMKN 1 PANDEGLANG', '2026-06-13 05:39:24', '2026-06-13 05:39:24'),
	(17, 'AHMAD MUKRIM', 'SMKN 2 PANDEGLANG', '2026-06-13 05:39:24', '2026-06-13 05:39:24'),
	(27, 'DINEZ', 'SMKN 2 PANDEGLANG', '2026-06-13 06:55:24', '2026-06-13 22:51:30'),
	(28, 'LUTFI', 'SMKN 1 PANDEGLANG', '2026-06-13 06:55:24', '2026-06-13 22:51:30'),
	(29, 'RITA', 'SMKN 1 PANDEGLANG', '2026-06-13 06:55:24', '2026-06-13 22:51:30'),
	(32, 'SASA', 'SMAN 1 SUMEDANG', '2026-06-13 22:51:30', '2026-06-13 22:51:30');

-- Dumping structure for table latihan.ref_kategori
CREATE TABLE IF NOT EXISTS `ref_kategori` (
  `id_kategori` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table latihan.ref_kategori: ~4 rows (approximately)
INSERT INTO `ref_kategori` (`id_kategori`, `nama_kategori`, `created_at`, `updated_at`) VALUES
	(1, 'SOLO', '2026-04-20 04:01:58', '2026-04-20 04:01:58'),
	(2, 'GANDA', '2026-04-20 23:55:23', '2026-04-20 23:55:23'),
	(3, 'TUNGGAL', '2026-04-20 23:55:23', '2026-04-20 23:55:23'),
	(4, 'REGU', '2026-04-20 23:55:27', '2026-04-20 23:55:27');

-- Dumping structure for table latihan.ref_kelas
CREATE TABLE IF NOT EXISTS `ref_kelas` (
  `id_kelas` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `label_kelas` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kelas`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table latihan.ref_kelas: ~2 rows (approximately)
INSERT INTO `ref_kelas` (`id_kelas`, `label_kelas`, `created_at`, `updated_at`) VALUES
	(1, 'A PUTRA DEWASA', '2026-06-03 03:23:14', '2026-06-03 03:23:14'),
	(2, 'POOL KAIDAH', '2026-06-03 03:23:33', '2026-06-03 03:23:33');

-- Dumping structure for table latihan.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table latihan.sessions: ~2 rows (approximately)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('m5qOc1QBkO2Tk1IO2tEaCfharCPseOMyp6Pkr15W', NULL, '192.168.1.10', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.3912.77', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZURGYWZUQjRlSXdKRFRmdXVhT0FNdTFreFRQMjdncldRVHJnTTVFYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Njc6Imh0dHA6Ly8xOTIuMTY4LjEuNzo4MDAwL2FkbWluL21hbmFqZW1lbi9zb2xvL2dldC1qdXJpLXNjb3Jlcy80L2JpcnUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1777646331),
	('nLRf2YgjaRGqqSAmJnYP6ymXGm7RvsBcosf2Enkp', NULL, '127.0.0.1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36 Edg/147.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSHFyeWkzcnZqUHFlZkU0NXRCa0ZEMGpkVFVpMTdDNk1DeE9TNWhtSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9tYW5hamVtZW4vc29sby9nZXQtanVyaS1zY29yZXMvNC9iaXJ1Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777646329);

-- Dumping structure for table latihan.status_kontrol_juri
CREATE TABLE IF NOT EXISTS `status_kontrol_juri` (
  `id_kontrol` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_partai` bigint(20) unsigned NOT NULL,
  `no_juri` int(11) NOT NULL,
  `is_on` tinyint(1) NOT NULL DEFAULT 0,
  `status_input` enum('Proses','Selesai') NOT NULL DEFAULT 'Proses',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kontrol`),
  KEY `status_kontrol_juri_id_partai_foreign` (`id_partai`),
  CONSTRAINT `status_kontrol_juri_id_partai_foreign` FOREIGN KEY (`id_partai`) REFERENCES `partai` (`id_partai`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table latihan.status_kontrol_juri: ~24 rows (approximately)
INSERT INTO `status_kontrol_juri` (`id_kontrol`, `id_partai`, `no_juri`, `is_on`, `status_input`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 'Proses', NULL, NULL),
	(2, 1, 2, 1, 'Proses', NULL, NULL),
	(3, 1, 3, 1, 'Proses', NULL, NULL),
	(4, 1, 4, 1, 'Proses', NULL, NULL),
	(5, 1, 5, 0, 'Proses', NULL, NULL),
	(6, 1, 6, 0, 'Proses', NULL, NULL),
	(7, 2, 1, 1, 'Selesai', NULL, '2026-06-13 06:45:59'),
	(8, 2, 2, 1, 'Selesai', NULL, '2026-06-13 06:45:59'),
	(9, 2, 3, 1, 'Selesai', NULL, '2026-06-13 06:45:59'),
	(10, 2, 4, 1, 'Selesai', NULL, '2026-06-13 06:45:59'),
	(11, 2, 5, 0, 'Proses', NULL, '2026-06-13 06:45:59'),
	(12, 2, 6, 0, 'Proses', NULL, '2026-06-13 06:45:59'),
	(13, 3, 1, 1, 'Selesai', NULL, '2026-06-12 00:48:58'),
	(14, 3, 2, 1, 'Selesai', NULL, '2026-06-12 00:48:58'),
	(15, 3, 3, 1, 'Selesai', NULL, '2026-06-12 00:48:58'),
	(16, 3, 4, 1, 'Selesai', NULL, '2026-06-12 00:48:58'),
	(17, 3, 5, 0, 'Proses', NULL, '2026-06-12 00:48:58'),
	(18, 3, 6, 0, 'Proses', NULL, '2026-06-12 00:48:58'),
	(19, 4, 1, 1, 'Selesai', NULL, '2026-06-13 05:39:47'),
	(20, 4, 2, 1, 'Selesai', NULL, '2026-06-13 05:39:47'),
	(21, 4, 3, 1, 'Selesai', NULL, '2026-06-13 05:39:47'),
	(22, 4, 4, 1, 'Selesai', NULL, '2026-06-13 05:39:47'),
	(23, 4, 5, 0, 'Proses', NULL, '2026-06-13 05:39:47'),
	(24, 4, 6, 0, 'Proses', NULL, '2026-06-13 05:39:47');

-- Dumping structure for table latihan.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','juri') NOT NULL DEFAULT 'juri',
  `no_juri` int(11) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table latihan.users: ~3 rows (approximately)
INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `role`, `no_juri`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Test User', '', 'test@example.com', '2026-04-20 03:56:30', '$2y$12$WzkTCWguuaju4JQljNOOieUwI.9236H1zAiGi9AjxfXeNu1nCuRhG', 'juri', NULL, 'LPQyBMtETt', '2026-04-20 03:56:30', '2026-04-20 03:56:30'),
	(3, 'Administrator', 'admin', 'admin@gmail.com', NULL, '$2y$12$qLA8od.VlQK01F3VkVLDheAhiFSYz5eWZ/avfhH3iJrvTWPkpHmIK', 'admin', NULL, NULL, '2026-05-13 03:13:39', '2026-05-13 03:13:39'),
	(4, 'Juri 1', 'juri1', 'juri1@gmail.com', NULL, '$2y$12$xX7WWXaTwfAlUOAE6qD1W.ocALCmymC66GO/5EjV8EGo5putKTMyG', 'juri', 1, NULL, '2026-05-13 03:13:50', '2026-05-13 03:13:50'),
	(5, 'Juri ', 'juri', 'juri@gmail.com', NULL, '$2y$12$cwxEgEmZiQKrLVq.16NNVOlNYEmYSDiYmIEq4oOt0AvT5YEZb7YdW', 'juri', NULL, NULL, '2026-05-13 03:51:56', '2026-05-13 03:51:56');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
