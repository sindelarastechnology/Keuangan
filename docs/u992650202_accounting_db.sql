-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 29, 2026 at 11:32 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u992650202_accounting_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'asset',
  `normal_balance` enum('debit','credit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `is_header` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `code`, `name`, `category`, `normal_balance`, `parent_id`, `is_header`, `is_active`, `description`, `created_at`, `updated_at`) VALUES
(1, '1000-00-000', 'Harta', 'asset', 'debit', NULL, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(2, '1100-00-000', 'Kas', 'asset', 'debit', 1, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(3, '1100-00-010', 'Kas Kecil', 'asset', 'debit', 2, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(4, '1100-00-020', 'Kas', 'asset', 'debit', 2, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(5, '1200-00-000', 'Bank', 'asset', 'debit', 1, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(6, '1200-00-010', 'Bank BCA', 'asset', 'debit', 5, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(7, '1200-00-020', 'Bank Lain', 'asset', 'debit', 5, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(8, '1300-00-000', 'Piutang Dagang', 'asset', 'debit', 1, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(9, '1300-00-010', 'Piutang Giro', 'asset', 'debit', 8, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(10, '1300-00-020', 'Piutang Usaha', 'asset', 'debit', 8, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(11, '1300-00-021', 'Piutang Sementara', 'asset', 'debit', 8, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(12, '1300-00-030', 'Piutang Usaha (USD)', 'asset', 'debit', 8, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(13, '1300-00-031', 'Piutang Sementara (USD)', 'asset', 'debit', 8, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(14, '1300-00-040', 'Cadangan Kerugian Piutang', 'asset', 'credit', 8, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(15, '1300-00-050', 'Piutang Non Usaha', 'asset', 'debit', 8, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(16, '1300-00-098', 'Uang Muka Pembelian', 'asset', 'debit', 8, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(17, '1300-00-099', 'Uang Muka Pembelian (USD)', 'asset', 'debit', 8, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(18, '1400-00-000', 'Persediaan', 'asset', 'debit', 1, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(19, '1400-00-010', 'Persediaan 1', 'asset', 'debit', 18, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(20, '1400-00-020', 'Persediaan 2', 'asset', 'debit', 18, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(21, '1400-00-030', 'Persediaan 3', 'asset', 'debit', 18, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(22, '1400-00-040', 'Persediaan 4', 'asset', 'debit', 18, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(23, '1400-00-098', 'Persediaan Dalam Perjalanan Beli', 'asset', 'debit', 18, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(24, '1400-00-099', 'Persediaan Dalam Perjalanan Jual', 'asset', 'debit', 18, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(25, '1500-00-000', 'Biaya Dibayar Dimuka', 'asset', 'debit', 1, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(26, '1500-00-010', 'Pajak Dibayar di Muka', 'asset', 'debit', 25, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(27, '1500-00-020', 'Asuransi Dibayar di Muka', 'asset', 'debit', 25, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(28, '1500-00-030', 'PPN Masukan', 'asset', 'debit', 25, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(29, '1500-00-040', 'PPh 23 Dibayar Dimuka', 'asset', 'debit', 25, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(30, '1600-00-000', 'Investasi Jangka Panjang', 'asset', 'debit', 1, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(31, '1600-00-010', 'Investasi Saham', 'asset', 'debit', 30, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(32, '1600-00-020', 'Investasi Obligasi', 'asset', 'debit', 30, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(33, '1700-00-000', 'Harta Tetap Berwujud', 'asset', 'debit', 1, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(34, '1700-00-010', 'Tanah', 'asset', 'debit', 33, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(35, '1700-00-020', 'Bangunan', 'asset', 'debit', 33, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(36, '1700-00-021', 'Akumulasi Penyusutan Bangunan', 'asset', 'credit', 33, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(37, '1700-00-030', 'Mesin dan Peralatan', 'asset', 'debit', 33, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(38, '1700-00-031', 'Akumulasi Penyusutan Mesin dan Peralatan', 'asset', 'credit', 33, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(39, '1700-00-040', 'Mebel dan Alat Tulis Kantor', 'asset', 'debit', 33, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(40, '1700-00-041', 'Akumulasi Penyusutan Mebel dan ATK', 'asset', 'credit', 33, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(41, '1700-00-050', 'Kendaraan', 'asset', 'debit', 33, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(42, '1700-00-051', 'Akumulasi Penyusutan Kendaraan', 'asset', 'credit', 33, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(43, '1700-00-070', 'Harta Lainnya', 'asset', 'debit', 33, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(44, '1700-00-071', 'Akumulasi Penyusutan Harta Lainnya', 'asset', 'credit', 33, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(45, '1800-00-000', 'Harta Tetap Tidak Berwujud', 'asset', 'debit', 1, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(46, '1800-00-010', 'Hak Merek', 'asset', 'debit', 45, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(47, '1800-00-020', 'Hak Cipta', 'asset', 'debit', 45, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(48, '1800-00-030', 'Good Will', 'asset', 'debit', 45, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(49, '1900-00-000', 'Harta Lainnya', 'asset', 'debit', 1, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(50, '1900-00-020', 'Biaya Pra Operasi dan Operasi', 'asset', 'debit', 49, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(51, '1900-00-021', 'Akumulasi Amortisasi Pra Operasi dan Operasi', 'asset', 'credit', 49, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(52, '2000-00-000', 'Kewajiban', 'liability', 'credit', NULL, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(53, '2100-00-000', 'Hutang Lancar', 'liability', 'credit', 52, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(54, '2100-00-010', 'Wesel Bayar', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(55, '2100-00-015', 'Hutang Giro', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(56, '2100-00-020', 'Hutang Usaha', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(57, '2100-00-025', 'Hutang BHP USO', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(58, '2100-00-030', 'Hutang Konsinyasi', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(59, '2100-00-040', 'Uang Muka Penjualan', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(60, '2100-00-055', 'Hutang Deviden', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(61, '2100-00-060', 'Hutang Bunga', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(62, '2100-00-065', 'Biaya yang Masih Harus Dibayar', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(63, '2100-00-075', 'Kartu Kredit', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(64, '2100-00-080', 'Hutang Pajak Penjualan', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(65, '2100-00-082', 'Hutang Komisi Penjualan', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(66, '2100-00-085', 'Hutang Gaji', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(67, '2100-00-098', 'Uang Muka Penjualan', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(68, '2100-00-099', 'Uang Muka Penjualan (USD)', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(69, '2100-00-070', 'Hutang PPN Keluaran', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(70, '2100-00-071', 'Hutang PPh 23', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(71, '2100-00-072', 'Hutang PPh 21', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(72, '2100-00-073', 'Hutang PPh Pasal 4 Ayat 2', 'liability', 'credit', 53, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(73, '2200-00-000', 'Pendapatan yang diterima di muka', 'liability', 'credit', 52, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(74, '2200-00-010', 'Sewa Diterima di Muka', 'liability', 'credit', 73, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(75, '2300-00-000', 'Hutang Jangka Panjang', 'liability', 'credit', 52, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(76, '2300-00-010', 'UTANG PEMBIAYAAN', 'liability', 'credit', 75, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(77, '2300-00-020', 'Hutang Bank', 'liability', 'credit', 75, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(78, '3000-00-000', 'Modal', 'equity', 'credit', NULL, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(79, '3100-00-000', 'Modal', 'equity', 'credit', 78, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(80, '3100-00-010', 'Saham Preferen', 'equity', 'credit', 79, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(81, '3100-00-020', 'Modal Disetor', 'equity', 'credit', 79, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(82, '3200-00-000', 'Laba', 'equity', 'credit', 78, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(83, '3200-00-010', 'Laba ditahan', 'equity', 'credit', 82, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(84, '3200-00-020', 'Laba Tahun Berjalan', 'equity', 'credit', 82, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(85, '3200-00-099', 'Historical Balancing', 'equity', 'credit', 82, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(86, '4000-00-000', 'Pendapatan', 'revenue', 'credit', NULL, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(87, '4100-00-000', 'Pendapatan Usaha', 'revenue', 'credit', 86, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(88, '4100-00-010', 'PENJUALAN BW', 'revenue', 'credit', 87, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(89, '4100-00-020', 'REGESTRASI', 'revenue', 'credit', 87, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(90, '4100-00-030', 'Penjualan Produk 3', 'revenue', 'credit', 87, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(91, '4200-00-000', 'Pendapatan Lain', 'revenue', 'credit', 86, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(92, '4200-00-040', 'Penjualan Lain', 'revenue', 'credit', 91, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(93, '4200-00-070', 'Potongan Penjualan', 'revenue', 'credit', 91, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(94, '4200-00-080', 'Pendapatan Denda Keterlambatan', 'revenue', 'credit', 91, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(95, '4200-00-090', 'Pendapatan atas Pengantaran', 'revenue', 'credit', 91, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(96, '4300-00-000', 'Pendapatan Lain-lain', 'revenue', 'credit', 86, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(97, '5000-00-000', 'Biaya atas Pendapatan', 'cogs', 'debit', NULL, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(98, '5100-00-000', 'Biaya Produksi', 'cogs', 'debit', 97, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(99, '5100-00-010', 'PEMBELIAN BW', 'cogs', 'debit', 98, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(100, '5100-00-020', 'Pembelian Barang', 'cogs', 'debit', 98, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 04:05:52'),
(101, '5100-00-030', 'Biaya 3', 'cogs', 'debit', 98, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(102, '5100-00-040', 'Komisi Penjualan', 'cogs', 'debit', 98, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(103, '5100-00-070', 'PSB', 'cogs', 'debit', 98, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(104, '5100-00-080', 'KERJASAMA', 'cogs', 'debit', 98, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(105, '5200-00-000', 'Biaya Lain', 'cogs', 'debit', 97, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(106, '5200-00-010', 'Kerugian Piutang', 'cogs', 'debit', 105, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(107, '5200-00-020', 'Biaya Denda Keterlambatan', 'cogs', 'debit', 105, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(108, '5200-00-030', 'Pemakaian Barang', 'cogs', 'debit', 105, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(109, '6100-00-000', 'Pengeluaran Operasional', 'expense', 'debit', NULL, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(110, '6110-00-000', 'Biaya Operasional', 'expense', 'debit', 109, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(111, '6110-00-010', 'Gaji Direksi dan Karyawan', 'expense', 'debit', 110, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(112, '6110-00-030', 'Listrik, Air dan Telpon', 'expense', 'debit', 110, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(113, '6110-00-031', 'Admin Bank', 'expense', 'debit', 110, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(114, '6110-00-032', 'Konsumsi', 'expense', 'debit', 110, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(115, '6110-00-033', 'Ongkir', 'expense', 'debit', 110, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(116, '6110-00-050', 'Promosi dan Iklan', 'expense', 'debit', 110, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(117, '6110-00-060', 'Administrasi Kantor', 'expense', 'debit', 110, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(118, '6110-00-070', 'OPERASIONAL', 'expense', 'debit', 110, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(119, '6110-00-080', 'LAIN-LAIN', 'expense', 'debit', 110, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(120, '6600-00-000', 'Biaya Non Operasional', 'expense', 'debit', 109, 1, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(121, '6600-00-010', 'Penyusutan Bangunan', 'expense', 'debit', 120, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(122, '6600-00-011', 'Penyusutan Mesin dan Peralatan', 'expense', 'debit', 120, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(123, '6600-00-012', 'Penyusutan Mebel dan ATK', 'expense', 'debit', 120, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(124, '6600-00-013', 'Penyusutan Kendaraan', 'expense', 'debit', 120, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(125, '6600-00-015', 'Penyusutan Harta Lainnya', 'expense', 'debit', 120, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(126, '6600-00-016', 'Amortisasi Pra Operasi dan Operasi', 'expense', 'debit', 120, 0, 1, NULL, '2026-05-10 03:56:51', '2026-05-10 03:56:51');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `event` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_id` bigint UNSIGNED NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `event`, `auditable_type`, `auditable_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'created', 'App\\Models\\Journal', 25, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00004\",\"total_debit\":1189202,\"total_credit\":1189202,\"description\":\"Saldo Awal: Bank Umum\"}', '192.168.110.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 00:19:29'),
(2, 1, 'created', 'App\\Models\\Journal', 26, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00005\",\"total_debit\":881228,\"total_credit\":881228,\"description\":\"Beban: lain lain (EXP-2026-0001)\"}', '192.168.110.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 00:27:48'),
(3, 1, 'created', 'App\\Models\\Journal', 27, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00006\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00001 (Pendapatan Lain-lain)\"}', '192.168.110.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 00:28:39'),
(4, 1, 'created', 'App\\Models\\Journal', 28, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00007\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00002 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:06:39'),
(5, 1, 'created', 'App\\Models\\Journal', 29, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00008\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00003 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:08:57'),
(6, 1, 'created', 'App\\Models\\Journal', 30, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00009\",\"total_debit\":203000,\"total_credit\":203000,\"description\":\"Beban: Makan Siang (EXP-2026-0002)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:13:48'),
(7, 1, 'created', 'App\\Models\\Journal', 31, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00010\",\"total_debit\":658000,\"total_credit\":658000,\"description\":\"Beban: TRANSFER (EXP-2026-0003)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:13:58'),
(8, 1, 'created', 'App\\Models\\Journal', 32, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00011\",\"total_debit\":30000,\"total_credit\":30000,\"description\":\"Beban: TRANSFER (EXP-2026-0004)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:14:06'),
(9, 1, 'created', 'App\\Models\\Journal', 33, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00012\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00004 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:16:56'),
(10, 1, 'created', 'App\\Models\\Journal', 34, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00013\",\"total_debit\":203000,\"total_credit\":203000,\"description\":\"Beban: TARIK TUNAI (EXP-2026-0005)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:23:47'),
(11, 1, 'created', 'App\\Models\\Journal', 35, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00014\",\"total_debit\":275000,\"total_credit\":275000,\"description\":\"Beban: TRANSFER (EXP-2026-0006)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:23:59'),
(12, 1, 'created', 'App\\Models\\Journal', 36, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00015\",\"total_debit\":100000,\"total_credit\":100000,\"description\":\"Beban: TRANSFER (EXP-2026-0007)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:24:11'),
(13, 1, 'created', 'App\\Models\\Journal', 37, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00016\",\"total_debit\":112000,\"total_credit\":112000,\"description\":\"Beban: TRANSFER (EXP-2026-0008)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:24:21'),
(14, 1, 'created', 'App\\Models\\Journal', 38, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00017\",\"total_debit\":19950,\"total_credit\":19950,\"description\":\"Beban: APLIKASI BELI (EXP-2026-0009)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:24:36'),
(15, 1, 'created', 'App\\Models\\Journal', 39, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00018\",\"total_debit\":16000,\"total_credit\":16000,\"description\":\"Beban: APLIKASI BELI (EXP-2026-0010)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:24:47'),
(16, 1, 'created', 'App\\Models\\Journal', 40, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00019\",\"total_debit\":114000,\"total_credit\":114000,\"description\":\"Beban: TRANSFER (EXP-2026-0011)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:24:58'),
(17, 1, 'created', 'App\\Models\\Journal', 41, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00020\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00005 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:30:59'),
(18, 1, 'created', 'App\\Models\\Journal', 42, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00021\",\"total_debit\":190000,\"total_credit\":190000,\"description\":\"Beban: MAKAN MALAM (EXP-2026-0012)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:32:53'),
(19, 1, 'created', 'App\\Models\\Journal', 43, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00022\",\"total_debit\":100000,\"total_credit\":100000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00007 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:35:18'),
(20, 1, 'created', 'App\\Models\\Journal', 44, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00023\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00006 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:35:27'),
(21, 1, 'created', 'App\\Models\\Journal', 45, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00024\",\"total_debit\":203000,\"total_credit\":203000,\"description\":\"Beban: TRANSFER (EXP-2026-0014)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:38:48'),
(22, 1, 'created', 'App\\Models\\Journal', 46, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00025\",\"total_debit\":100000,\"total_credit\":100000,\"description\":\"Beban: TRANSFER (EXP-2026-0013)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:39:04'),
(23, 1, 'created', 'App\\Models\\Journal', 47, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00026\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00008 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-08 02:40:47'),
(24, 1, 'created', 'App\\Models\\Journal', 48, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00027\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00009 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-10 03:06:47'),
(25, 1, 'created', 'App\\Models\\Journal', 49, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00028\",\"total_debit\":50000,\"total_credit\":50000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00010 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-10 03:06:52'),
(26, 1, 'created', 'App\\Models\\Journal', 50, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00029\",\"total_debit\":600000,\"total_credit\":600000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00011 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-10 03:06:58'),
(27, 1, 'created', 'App\\Models\\Journal', 51, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00030\",\"total_debit\":1509200,\"total_credit\":1509200,\"description\":\"Beban: BELANJA PERLENGKAPAN\\/BAHAN (EXP-2026-0018)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-10 03:16:07'),
(28, 1, 'created', 'App\\Models\\Journal', 52, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00031\",\"total_debit\":250000,\"total_credit\":250000,\"description\":\"Beban: TRANSFER (EXP-2026-0017)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-10 03:16:16'),
(29, 1, 'created', 'App\\Models\\Journal', 53, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00032\",\"total_debit\":50000,\"total_credit\":50000,\"description\":\"Beban: TRANSFER (EXP-2026-0016)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-10 03:16:25'),
(30, 1, 'created', 'App\\Models\\Journal', 54, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00033\",\"total_debit\":1718920,\"total_credit\":1718920,\"description\":\"Beban: BELANJA PERLENGKAPAN\\/BAHAN (EXP-2026-0019)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-10 03:16:35'),
(31, 1, 'created', 'App\\Models\\Journal', 55, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00034\",\"total_debit\":403000,\"total_credit\":403000,\"description\":\"Beban: TARIK TUNAI (EXP-2026-0015)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-10 03:16:48'),
(32, 1, 'created', 'App\\Models\\Journal', 56, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00035\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00012 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-10 03:18:35'),
(33, 1, 'created', 'App\\Models\\Journal', 57, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00036\",\"total_debit\":10000,\"total_credit\":10000,\"description\":\"Beban: TUKAR SALDO CASH (EXP-2026-0020)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-10 03:19:45'),
(34, 1, 'created', 'App\\Models\\Journal', 58, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00037\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00013 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-10 19:26:47'),
(35, 1, 'created', 'App\\Models\\Journal', 59, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00038\",\"total_debit\":215000,\"total_credit\":215000,\"description\":\"Beban: TRANSFER (EXP-2026-0023)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-11 03:00:00'),
(36, 1, 'created', 'App\\Models\\Journal', 60, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00039\",\"total_debit\":260000,\"total_credit\":260000,\"description\":\"Beban: TRANSFER (EXP-2026-0021)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-11 03:00:10'),
(37, 1, 'created', 'App\\Models\\Journal', 61, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00040\",\"total_debit\":600000,\"total_credit\":600000,\"description\":\"Beban: BELANJA PERLENGKAPAN\\/BAHAN (EXP-2026-0022)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-11 03:00:20'),
(38, 1, 'created', 'App\\Models\\Journal', 62, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00041\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00014 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 03:10:17'),
(39, 1, 'created', 'App\\Models\\Journal', 63, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00042\",\"total_debit\":303000,\"total_credit\":303000,\"description\":\"Beban: MAKAN SIANG (EXP-2026-0024)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 03:12:09'),
(40, 1, 'created', 'App\\Models\\Journal', 64, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00043\",\"total_debit\":361770,\"total_credit\":361770,\"description\":\"Beban: BELANJA (EXP-2026-0025)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 03:12:16'),
(41, 1, 'created', 'App\\Models\\Journal', 65, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00044\",\"total_debit\":20000,\"total_credit\":20000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00016 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 03:13:59'),
(42, 1, 'created', 'App\\Models\\Journal', 66, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00045\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00015 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 03:14:08'),
(43, 1, 'created', 'App\\Models\\Journal', 67, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00046\",\"total_debit\":300000,\"total_credit\":300000,\"description\":\"Beban: TRANSFER (EXP-2026-0027)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 03:15:46'),
(44, 1, 'created', 'App\\Models\\Journal', 68, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00047\",\"total_debit\":323000,\"total_credit\":323000,\"description\":\"Beban: TARIK TUNAI (EXP-2026-0026)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 03:15:55'),
(45, 1, 'created', 'App\\Models\\Journal', 69, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00048\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00017 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-13 02:50:13'),
(46, 1, 'created', 'App\\Models\\Journal', 70, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00049\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00018 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-14 01:47:36'),
(47, 1, 'created', 'App\\Models\\Journal', 71, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00050\",\"total_debit\":893000,\"total_credit\":893000,\"description\":\"Beban: TRANSFER BAYAR SEABANK (EXP-2026-0028)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-14 01:48:58'),
(48, 1, 'created', 'App\\Models\\Journal', 72, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00051\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00019 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-15 01:06:42'),
(49, 1, 'created', 'App\\Models\\Journal', 73, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00052\",\"total_debit\":100000,\"total_credit\":100000,\"description\":\"Beban: TRANSFER (EXP-2026-0029)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-15 01:07:35'),
(50, 1, 'created', 'App\\Models\\Journal', 74, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00053\",\"total_debit\":153000,\"total_credit\":153000,\"description\":\"Beban: KONSUMSI (EXP-2026-0030)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-15 01:11:21'),
(51, 1, 'created', 'App\\Models\\Journal', 75, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00054\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00020 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-16 23:49:48'),
(52, 1, 'created', 'App\\Models\\Journal', 76, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00055\",\"total_debit\":594540,\"total_credit\":594540,\"description\":\"Beban: PEMBAYARAN VIRTUAL ACCOUNT (EXP-2026-0031)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-16 23:52:05'),
(53, 1, 'created', 'App\\Models\\Journal', 77, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00056\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00021 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-16 23:54:10'),
(54, 1, 'created', 'App\\Models\\Journal', 78, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00057\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00022 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-17 21:09:52'),
(55, 1, 'created', 'App\\Models\\Journal', 79, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00058\",\"total_debit\":2509593,\"total_credit\":2509593,\"description\":\"Beban: BELANJA KAIN (EXP-2026-0032)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 01:30:00'),
(56, 1, 'created', 'App\\Models\\Journal', 80, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00059\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00023 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 20:37:09'),
(57, 1, 'created', 'App\\Models\\Journal', 81, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00060\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00024 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-23 18:21:54'),
(58, 1, 'created', 'App\\Models\\Journal', 82, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00061\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00025 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-23 18:22:00'),
(59, 1, 'created', 'App\\Models\\Journal', 83, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00062\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00026 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-23 18:22:08'),
(60, 1, 'created', 'App\\Models\\Journal', 84, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00063\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00027 (Pendapatan Lain-lain)\"}', '192.168.110.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 03:18:38'),
(61, 1, 'created', 'App\\Models\\Journal', 85, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00064\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00028 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 00:41:45'),
(62, 1, 'created', 'App\\Models\\Journal', 86, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00065\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00029 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 23:13:30'),
(63, 1, 'created', 'App\\Models\\Journal', 87, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00066\",\"total_debit\":200000,\"total_credit\":200000,\"description\":\"Beban: TRANSFER SEABANK (EXP-2026-0034)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 23:41:12'),
(64, 1, 'created', 'App\\Models\\Journal', 88, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00067\",\"total_debit\":2000000,\"total_credit\":2000000,\"description\":\"Beban: TRANSFER JAGO (EXP-2026-0036)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 23:41:22'),
(65, 1, 'created', 'App\\Models\\Journal', 89, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00068\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN TRANSFER BANK JAGO-SEABANK (EXP-2026-0035)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 23:41:29'),
(66, 1, 'created', 'App\\Models\\Journal', 90, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00069\",\"total_debit\":500000,\"total_credit\":500000,\"description\":\"Beban: TRANSFER JAGO (EXP-2026-0033)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 23:41:36'),
(67, 1, 'created', 'App\\Models\\Journal', 91, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00070\",\"total_debit\":17750,\"total_credit\":17750,\"description\":\"Beban: BELI PULPEN 12PCS (EXP-2026-0039)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 18:42:43'),
(68, 1, 'created', 'App\\Models\\Journal', 92, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00071\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: BIAYA ADMIN TRANSFER BANK JAGO-SEABANK (EXP-2026-0040)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 18:42:51'),
(69, 1, 'created', 'App\\Models\\Journal', 93, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00072\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: BIAYA TRANSFER BANK JAGO-SEABANK (EXP-2026-0038)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 18:42:59'),
(70, 1, 'created', 'App\\Models\\Journal', 94, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00073\",\"total_debit\":846550,\"total_credit\":846550,\"description\":\"Beban: BELANJA PLASTIK BENING 4 BENDEL (EXP-2026-0037)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 18:43:09'),
(71, 1, 'created', 'App\\Models\\Journal', 95, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00074\",\"total_debit\":552,\"total_credit\":552,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00030 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 23:39:35'),
(72, 1, 'created', 'App\\Models\\Journal', 96, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00075\",\"total_debit\":61125,\"total_credit\":61125,\"description\":\"Beban: BELI STIKER SIZE (EXP-2026-0042)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 23:41:45'),
(73, 1, 'created', 'App\\Models\\Journal', 97, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00076\",\"total_debit\":110,\"total_credit\":110,\"description\":\"Beban: PAJAK BUNGA BANK JAGO (EXP-2026-0041)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 23:41:54'),
(74, 1, 'created', 'App\\Models\\Journal', 98, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00077\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: BIAYA ADMIN TRANSFER BANK JAGO-SEABANK (EXP-2026-0043)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 23:42:00'),
(75, 1, 'created', 'App\\Models\\Journal', 99, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00078\",\"total_debit\":915000,\"total_credit\":915000,\"description\":\"Beban: TRANSFER (EXP-2026-0044)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 02:00:36'),
(76, 1, 'created', 'App\\Models\\Journal', 100, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00079\",\"total_debit\":738607,\"total_credit\":738607,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00031 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 02:45:46'),
(77, 1, 'created', 'App\\Models\\Journal', 101, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00080\",\"total_debit\":738625,\"total_credit\":738625,\"description\":\"Beban: TRANSFER (EXP-2026-0045)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 02:46:58'),
(78, 1, 'created', 'App\\Models\\Journal', 102, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00081\",\"total_debit\":200000,\"total_credit\":200000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/08\\/00032 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 00:07:30'),
(79, 1, 'created', 'App\\Models\\Journal', 103, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00082\",\"total_debit\":153000,\"total_credit\":153000,\"description\":\"Beban: MAKAN SIANG (EXP-2026-0046)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 00:08:59'),
(80, 1, 'created', 'App\\Models\\Journal', 104, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00083\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN BANK TRANSFER (EXP-2026-0047)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 00:09:06'),
(81, 1, 'created', 'App\\Models\\Journal', 105, NULL, '{\"number\":\"JNL\\/2026\\/08\\/00084\",\"total_debit\":38207722,\"total_credit\":38207722,\"description\":\"Jurnal Penutup \\u2014 Agustus 2026\"}', '192.168.110.2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 18:58:36'),
(82, 1, 'created', 'App\\Models\\Journal', 106, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00001\",\"total_debit\":1189202,\"total_credit\":1189202,\"description\":\"Saldo Awal \\u2014 September 2026 (carry forward dari Agustus 2026)\"}', '192.168.110.2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 18:58:36'),
(83, 1, 'created', 'App\\Models\\Journal', 107, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00002\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00001 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 19:10:24'),
(84, 1, 'created', 'App\\Models\\Journal', 108, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00003\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00002 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 19:10:31'),
(85, 1, 'created', 'App\\Models\\Journal', 109, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00004\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00003 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 19:10:39'),
(86, 1, 'created', 'App\\Models\\Journal', 110, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00005\",\"total_debit\":146000,\"total_credit\":146000,\"description\":\"Beban: TRANSFER (EXP-2026-0048)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 19:13:01'),
(87, 1, 'created', 'App\\Models\\Journal', 111, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00006\",\"total_debit\":397970,\"total_credit\":397970,\"description\":\"Beban: BELI PLASTIK HITAM PACKING 12PACK (EXP-2026-0049)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-02 19:13:09'),
(88, 1, 'created', 'App\\Models\\Journal', 112, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00007\",\"total_debit\":950000,\"total_credit\":950000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00004 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 02:48:19'),
(89, 1, 'created', 'App\\Models\\Journal', 113, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00008\",\"total_debit\":477190,\"total_credit\":477190,\"description\":\"Beban: TRANSFER (EXP-2026-0050)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 02:50:00'),
(90, 1, 'created', 'App\\Models\\Journal', 114, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00009\",\"total_debit\":100000,\"total_credit\":100000,\"description\":\"Beban: MAKAN SIANG (EXP-2026-0051)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 02:50:10'),
(91, 1, 'created', 'App\\Models\\Journal', 115, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00010\",\"total_debit\":950000,\"total_credit\":950000,\"description\":\"Pembatalan penerimaan RCP\\/2026\\/09\\/00004\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 02:53:24'),
(92, 1, 'updated', 'App\\Models\\Journal', 112, '{\"type\":\"normal\"}', '{\"type\":\"void\",\"reversed_by_journal_id\":115}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 02:53:24'),
(93, 1, 'created', 'App\\Models\\Journal', 116, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00011\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00005 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 02:54:14'),
(94, 1, 'created', 'App\\Models\\Journal', 117, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00012\",\"total_debit\":397970,\"total_credit\":397970,\"description\":\"Pembatalan beban EXP-2026-0049\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 02:55:47'),
(95, 1, 'updated', 'App\\Models\\Journal', 111, '{\"type\":\"normal\"}', '{\"type\":\"void\",\"reversed_by_journal_id\":117}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 02:55:47'),
(96, 1, 'created', 'App\\Models\\Journal', 118, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00013\",\"total_debit\":397972,\"total_credit\":397972,\"description\":\"Beban: BELI PLASTIK HITAM (EXP-2026-0052)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 02:56:53'),
(97, 1, 'created', 'App\\Models\\Journal', 119, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00014\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00006 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 02:07:42'),
(98, 1, 'created', 'App\\Models\\Journal', 120, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00015\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00007 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-06 19:20:20'),
(99, 1, 'created', 'App\\Models\\Journal', 121, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00016\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00008 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-06 19:20:28'),
(100, 1, 'created', 'App\\Models\\Journal', 122, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00017\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN TF BANK JAGO-BRI (EXP-2026-0054)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-06 19:22:45'),
(101, 1, 'created', 'App\\Models\\Journal', 123, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00018\",\"total_debit\":203000,\"total_credit\":203000,\"description\":\"Beban: MAKAN SIANG (EXP-2026-0053)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-06 19:22:56'),
(102, 1, 'created', 'App\\Models\\Journal', 124, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00019\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00009 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-07 23:49:21'),
(103, 1, 'created', 'App\\Models\\Journal', 125, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00020\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00010 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 20:31:06'),
(104, 1, 'created', 'App\\Models\\Journal', 126, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00021\",\"total_debit\":400000,\"total_credit\":400000,\"description\":\"Beban: TRANSFER (EXP-2026-0055)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 20:36:31'),
(105, 1, 'created', 'App\\Models\\Journal', 127, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00022\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN TRANSFER BANK JAGO (EXP-2026-0059)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 20:36:39'),
(106, 1, 'created', 'App\\Models\\Journal', 128, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00023\",\"total_debit\":2058370,\"total_credit\":2058370,\"description\":\"Beban: BELANJA BAHAN (EXP-2026-0056)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 20:36:50'),
(107, 1, 'created', 'App\\Models\\Journal', 129, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00024\",\"total_debit\":1670497,\"total_credit\":1670497,\"description\":\"Beban: BELANJA BAHAN (EXP-2026-0058)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 20:37:00'),
(108, 1, 'created', 'App\\Models\\Journal', 130, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00025\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: BIAYA TRANSFER BANK JAGO (EXP-2026-0057)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 20:37:08'),
(109, 1, 'created', 'App\\Models\\Journal', 131, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00026\",\"total_debit\":203000,\"total_credit\":203000,\"description\":\"Beban: TARIK TUNAI-KOPI (EXP-2026-0060)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 02:38:13'),
(110, 1, 'created', 'App\\Models\\Journal', 132, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00027\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN TRANSFER BANK JAGO (EXP-2026-0061)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 02:38:20'),
(111, 1, 'created', 'App\\Models\\Journal', 133, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00028\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00011 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 18:51:26'),
(112, 1, 'created', 'App\\Models\\Journal', 134, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00029\",\"total_debit\":44500,\"total_credit\":44500,\"description\":\"Beban: carry forward dari Agustus 2026 (EXP-2026-0063)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 18:54:59'),
(113, 1, 'created', 'App\\Models\\Journal', 135, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00030\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00012 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-10 20:00:50'),
(114, 1, 'created', 'App\\Models\\Journal', 136, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00031\",\"total_debit\":172200,\"total_credit\":172200,\"description\":\"Beban: BELI MATA AYAM 3PACK (EXP-2026-0066)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-10 20:44:40'),
(115, 1, 'created', 'App\\Models\\Journal', 137, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00032\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN TRANSFER BANK JAGO (EXP-2026-0067)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-10 20:44:53'),
(116, 1, 'created', 'App\\Models\\Journal', 138, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00033\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN TRANSFER BANK JAGO (EXP-2026-0065)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-10 20:44:58'),
(117, 1, 'created', 'App\\Models\\Journal', 139, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00034\",\"total_debit\":320700,\"total_credit\":320700,\"description\":\"Beban: BELI ZIPPER KUNING-OREN-JOTOL MASING MASING 2 (EXP-2026-0064)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-10 20:45:08'),
(118, 1, 'created', 'App\\Models\\Journal', 140, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00035\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00013 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-11 18:42:35'),
(119, 1, 'created', 'App\\Models\\Journal', 141, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00036\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00015 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-13 20:48:48'),
(120, 1, 'created', 'App\\Models\\Journal', 142, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00037\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00014 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-13 20:48:56'),
(121, 1, 'created', 'App\\Models\\Journal', 143, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00038\",\"total_debit\":200000,\"total_credit\":200000,\"description\":\"Beban: TRANSFER (EXP-2026-0068)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-13 20:51:34'),
(122, 1, 'created', 'App\\Models\\Journal', 144, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00039\",\"total_debit\":200000,\"total_credit\":200000,\"description\":\"Pembatalan beban EXP-2026-0068\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-13 23:17:33'),
(123, 1, 'updated', 'App\\Models\\Journal', 143, '{\"type\":\"normal\"}', '{\"type\":\"void\",\"reversed_by_journal_id\":144}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-13 23:17:33'),
(124, 1, 'created', 'App\\Models\\Journal', 145, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00040\",\"total_debit\":250000,\"total_credit\":250000,\"description\":\"Beban: TRANSFER (EXP-2026-0069)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-13 23:18:35'),
(125, 1, 'created', 'App\\Models\\Journal', 146, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00041\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00016 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 18:40:43'),
(126, 1, 'created', 'App\\Models\\Journal', 147, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00042\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN BANK JAGO (EXP-2026-0071)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 18:42:47'),
(127, 1, 'created', 'App\\Models\\Journal', 148, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00043\",\"total_debit\":103000,\"total_credit\":103000,\"description\":\"Beban: TARIK TUNAI (EXP-2026-0070)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-14 18:42:55'),
(128, 1, 'created', 'App\\Models\\Journal', 149, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00044\",\"total_debit\":688950,\"total_credit\":688950,\"description\":\"Beban: BELI ZIPPER PUTIH 30 (EXP-2026-0072)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 01:32:55'),
(129, 1, 'created', 'App\\Models\\Journal', 150, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00045\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN BANK JAGO (EXP-2026-0073)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-15 01:33:01'),
(130, 1, 'created', 'App\\Models\\Journal', 151, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00046\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00017 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 00:18:51'),
(131, 1, 'created', 'App\\Models\\Journal', 152, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00047\",\"total_debit\":1000000,\"total_credit\":1000000,\"description\":\"Beban: TRANSFER (EXP-2026-0074)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 00:31:39'),
(132, 1, 'created', 'App\\Models\\Journal', 153, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00048\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN BANK JAGO (EXP-2026-0075)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 00:31:45'),
(133, 1, 'created', 'App\\Models\\Journal', 154, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00049\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN BANK JAGO (EXP-2026-0077)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 00:32:03'),
(134, 1, 'created', 'App\\Models\\Journal', 155, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00050\",\"total_debit\":1000000,\"total_credit\":1000000,\"description\":\"Beban: TRANSFER (EXP-2026-0076)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 00:32:10'),
(135, 1, 'created', 'App\\Models\\Journal', 156, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00051\",\"total_debit\":415800,\"total_credit\":415800,\"description\":\"Beban: BELI PLASTIK BENING (EXP-2026-0078)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 00:37:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `event`, `auditable_type`, `auditable_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(136, 1, 'created', 'App\\Models\\Journal', 157, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00052\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN BANK JAGO (EXP-2026-0079)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 00:38:04'),
(137, 1, 'created', 'App\\Models\\Journal', 158, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00053\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00018 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-16 18:35:00'),
(138, 1, 'created', 'App\\Models\\Journal', 159, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00054\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00019 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-17 18:34:20'),
(139, 1, 'created', 'App\\Models\\Journal', 160, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00055\",\"total_debit\":2110321,\"total_credit\":2110321,\"description\":\"Beban: TRANSFER (EXP-2026-0080)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-17 23:34:52'),
(140, 1, 'created', 'App\\Models\\Journal', 161, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00056\",\"total_debit\":170000,\"total_credit\":170000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00022 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-20 18:53:22'),
(141, 1, 'created', 'App\\Models\\Journal', 162, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00057\",\"total_debit\":200000,\"total_credit\":200000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00021 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-20 18:53:30'),
(142, 1, 'created', 'App\\Models\\Journal', 163, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00058\",\"total_debit\":500000,\"total_credit\":500000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00020 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-20 18:53:44'),
(143, 1, 'created', 'App\\Models\\Journal', 164, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00059\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00023 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-20 18:54:18'),
(144, 1, 'created', 'App\\Models\\Journal', 165, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00060\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00024 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-20 18:54:39'),
(145, 1, 'created', 'App\\Models\\Journal', 166, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00061\",\"total_debit\":497000,\"total_credit\":497000,\"description\":\"Beban: TARIK TUNAI KONSUMSI (EXP-2026-0081)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-20 19:00:46'),
(146, 1, 'created', 'App\\Models\\Journal', 167, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00062\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN BANK JAGO (EXP-2026-0084)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-20 19:00:52'),
(147, 1, 'created', 'App\\Models\\Journal', 168, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00063\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN BANK JAGO (EXP-2026-0082)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-20 19:00:59'),
(148, 1, 'created', 'App\\Models\\Journal', 169, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00064\",\"total_debit\":367000,\"total_credit\":367000,\"description\":\"Beban: TARIK TUNAI + TITIPAN MB SALVA (EXP-2026-0083)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-20 19:01:05'),
(149, 1, 'created', 'App\\Models\\Journal', 170, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00065\",\"total_debit\":473074,\"total_credit\":473074,\"description\":\"Beban: BELI PLASTIK HITAM 15 (EXP-2026-0085)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-20 20:31:06'),
(150, 1, 'created', 'App\\Models\\Journal', 171, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00066\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN BANK JAGO (EXP-2026-0086)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-20 20:31:13'),
(151, 1, 'created', 'App\\Models\\Journal', 172, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00067\",\"total_debit\":262000,\"total_credit\":262000,\"description\":\"Beban: BELI MINYAK OBRAS 5 (EXP-2026-0087)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 03:11:03'),
(152, 1, 'created', 'App\\Models\\Journal', 173, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00068\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN BANK JAGO (EXP-2026-0088)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 03:11:08'),
(153, 1, 'created', 'App\\Models\\Journal', 174, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00069\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00025 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 18:31:11'),
(154, 1, 'created', 'App\\Models\\Journal', 175, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00070\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN BANK JAGO (EXP-2026-0092)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 18:36:32'),
(155, 1, 'created', 'App\\Models\\Journal', 176, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00071\",\"total_debit\":400000,\"total_credit\":400000,\"description\":\"Beban: TRANSFER (EXP-2026-0089)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 18:36:39'),
(156, 1, 'created', 'App\\Models\\Journal', 177, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00072\",\"total_debit\":18000,\"total_credit\":18000,\"description\":\"Beban: SHELY CASH (EXP-2026-0091)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 18:36:47'),
(157, 1, 'created', 'App\\Models\\Journal', 178, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00073\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN BANK JAGO (EXP-2026-0090)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 18:36:54'),
(158, 1, 'created', 'App\\Models\\Journal', 179, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00074\",\"total_debit\":10000,\"total_credit\":10000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00026 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 23:39:08'),
(159, 1, 'created', 'App\\Models\\Journal', 180, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00075\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN BANK JAGO (EXP-2026-0094)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 18:07:59'),
(160, 1, 'created', 'App\\Models\\Journal', 181, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00076\",\"total_debit\":203000,\"total_credit\":203000,\"description\":\"Beban: TARIK TUNAI (EXP-2026-0093)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 18:08:06'),
(161, 1, 'created', 'App\\Models\\Journal', 182, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00077\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00027 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 18:28:08'),
(162, 1, 'created', 'App\\Models\\Journal', 183, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00078\",\"total_debit\":50000,\"total_credit\":50000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00029 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 20:35:05'),
(163, 1, 'created', 'App\\Models\\Journal', 184, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00079\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00028 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 20:35:15'),
(164, 1, 'created', 'App\\Models\\Journal', 185, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00080\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN BANK (EXP-2026-0096)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 20:38:12'),
(165, 1, 'created', 'App\\Models\\Journal', 186, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00081\",\"total_debit\":203000,\"total_credit\":203000,\"description\":\"Beban: TARIK TUNAI (EXP-2026-0095)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 20:38:20'),
(166, 1, 'created', 'App\\Models\\Journal', 187, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00082\",\"total_debit\":1400000,\"total_credit\":1400000,\"description\":\"Beban: TRANSFER (EXP-2026-0097)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 20:42:52'),
(167, 1, 'created', 'App\\Models\\Journal', 188, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00083\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN BANK JAGO (EXP-2026-0098)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 20:42:59'),
(168, 1, 'created', 'App\\Models\\Journal', 189, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00084\",\"total_debit\":100000,\"total_credit\":100000,\"description\":\"Beban: TRANSFER (EXP-2026-0099)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 18:30:58'),
(169, 1, 'created', 'App\\Models\\Journal', 190, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00085\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN BANK JAGO (EXP-2026-0100)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 18:31:13'),
(170, 1, 'created', 'App\\Models\\Journal', 191, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00086\",\"total_debit\":200000,\"total_credit\":200000,\"description\":\"Beban: TARIK TUNAI (EXP-2026-0101)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 18:31:20'),
(171, 1, 'created', 'App\\Models\\Journal', 192, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00087\",\"total_debit\":2500,\"total_credit\":2500,\"description\":\"Beban: ADMIN BANK JAGO (EXP-2026-0102)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 18:31:27'),
(172, 1, 'created', 'App\\Models\\Journal', 193, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00088\",\"total_debit\":650000,\"total_credit\":650000,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00030 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 18:32:18'),
(173, 1, 'created', 'App\\Models\\Journal', 194, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00089\",\"total_debit\":449,\"total_credit\":449,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00031 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 23:43:50'),
(174, 1, 'created', 'App\\Models\\Journal', 195, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00090\",\"total_debit\":89,\"total_credit\":89,\"description\":\"Kas Masuk Lainnya: RCP\\/2026\\/09\\/00032 (Pendapatan Lain-lain)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 23:43:57'),
(175, 1, 'created', 'App\\Models\\Journal', 196, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00091\",\"total_debit\":89,\"total_credit\":89,\"description\":\"Pembatalan penerimaan RCP\\/2026\\/09\\/00032\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 23:55:33'),
(176, 1, 'updated', 'App\\Models\\Journal', 195, '{\"type\":\"normal\"}', '{\"type\":\"void\",\"reversed_by_journal_id\":196}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 23:55:33'),
(177, 1, 'created', 'App\\Models\\Journal', 197, NULL, '{\"number\":\"JNL\\/2026\\/09\\/00092\",\"total_debit\":89,\"total_credit\":89,\"description\":\"Beban: PAJAK BUNGA BANK JAGO (EXP-2026-0103)\"}', '192.168.110.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 23:56:33');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('customer','supplier','both') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `city` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ar_account_id` bigint UNSIGNED DEFAULT NULL,
  `ap_account_id` bigint UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `code`, `name`, `type`, `email`, `phone`, `address`, `city`, `tax_number`, `contact_person`, `ar_account_id`, `ap_account_id`, `is_active`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 'SUP-SIMULASI', 'Supplier Simulasi', 'supplier', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2026-05-10 04:21:49', '2026-05-10 04:21:49', NULL),
(4, 'CUS-SIMULASI', 'Customer Simulasi', 'customer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2026-05-10 04:21:49', '2026-05-10 04:21:49', NULL),
(6, NULL, 'Test Customer', 'customer', 'test@example.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2026-08-07 20:54:32', '2026-08-07 20:54:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `credit_notes`
--

CREATE TABLE `credit_notes` (
  `id` bigint UNSIGNED NOT NULL,
  `number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_id` bigint UNSIGNED NOT NULL,
  `period_id` bigint UNSIGNED NOT NULL,
  `invoice_id` bigint UNSIGNED DEFAULT NULL,
  `journal_id` bigint UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `subtotal` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `discount_percent` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `discount_amount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `tax_amount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `tax_method` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `applied_amount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `remaining_amount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `status` enum('draft','posted','applied','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `credit_note_items`
--

CREATE TABLE `credit_note_items` (
  `id` bigint UNSIGNED NOT NULL,
  `credit_note_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `invoice_item_id` bigint UNSIGNED DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` decimal(15,4) NOT NULL DEFAULT '1.0000',
  `unit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `subtotal` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `revenue_account_id` bigint UNSIGNED DEFAULT NULL,
  `cost_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `debit_notes`
--

CREATE TABLE `debit_notes` (
  `id` bigint UNSIGNED NOT NULL,
  `number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_id` bigint UNSIGNED NOT NULL,
  `period_id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED DEFAULT NULL,
  `journal_id` bigint UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `subtotal` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `discount_percent` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `discount_amount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `tax_amount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `tax_method` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `applied_amount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `remaining_amount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `status` enum('draft','posted','applied','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `debit_note_items`
--

CREATE TABLE `debit_note_items` (
  `id` bigint UNSIGNED NOT NULL,
  `debit_note_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `purchase_item_id` bigint UNSIGNED DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` decimal(15,4) NOT NULL DEFAULT '1.0000',
  `unit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `subtotal` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `account_id` bigint UNSIGNED DEFAULT NULL,
  `cost_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deferred_revenues`
--

CREATE TABLE `deferred_revenues` (
  `id` bigint UNSIGNED NOT NULL,
  `period_id` bigint UNSIGNED NOT NULL,
  `liability_account_id` bigint UNSIGNED NOT NULL,
  `revenue_account_id` bigint UNSIGNED NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(18,4) NOT NULL,
  `remaining_amount` decimal(18,4) NOT NULL,
  `start_date` date NOT NULL,
  `total_months` int NOT NULL,
  `months_recognized` int NOT NULL DEFAULT '0',
  `monthly_amount` decimal(18,4) NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `journal_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_counters`
--

CREATE TABLE `document_counters` (
  `id` bigint UNSIGNED NOT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` smallint NOT NULL,
  `month` smallint DEFAULT NULL,
  `last_number` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_counters`
--

INSERT INTO `document_counters` (`id`, `type`, `year`, `month`, `last_number`, `created_at`, `updated_at`) VALUES
(9, 'journals', 2026, 5, 14, '2026-05-10 04:50:42', '2026-05-10 04:50:42'),
(10, 'purchases', 2026, 5, 2, '2026-05-10 04:53:19', '2026-05-10 04:53:19'),
(11, 'payments', 2026, 5, 6, '2026-05-10 04:53:35', '2026-05-10 04:53:35'),
(12, 'invoices', 2026, 5, 6, '2026-05-10 04:55:12', '2026-05-10 04:55:12'),
(13, 'journals', 2026, 8, 84, '2026-08-04 17:07:11', '2026-08-04 17:07:11'),
(14, 'purchases', 2026, 8, 1, '2026-08-04 17:10:41', '2026-08-04 17:10:41'),
(15, 'payments', 2026, 8, 1, '2026-08-04 17:11:16', '2026-08-04 17:11:16'),
(16, 'other_receipts', 2026, 8, 32, '2026-08-08 00:24:54', '2026-08-08 00:24:54'),
(17, 'journals', 2026, 9, 92, '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(18, 'other_receipts', 2026, 9, 32, '2026-09-02 19:02:44', '2026-09-02 19:02:44');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint UNSIGNED NOT NULL,
  `number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_id` bigint UNSIGNED NOT NULL,
  `journal_id` bigint UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `wallet_id` bigint UNSIGNED NOT NULL,
  `contact_id` bigint UNSIGNED DEFAULT NULL,
  `amount` decimal(18,4) NOT NULL,
  `include_tax` tinyint(1) NOT NULL DEFAULT '0',
  `receipt_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','posted','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `number`, `period_id`, `journal_id`, `date`, `name`, `account_id`, `wallet_id`, `contact_id`, `amount`, `include_tax`, `receipt_number`, `attachment`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'EXP-2026-0001', 16, 26, '2026-08-01', 'lain lain', 119, 4, NULL, 881228.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-08 00:27:37', '2026-08-08 00:27:48'),
(3, 'EXP-2026-0002', 16, 30, '2026-08-03', 'Makan Siang', 114, 4, NULL, 203000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-08 02:11:39', '2026-08-08 02:13:48'),
(4, 'EXP-2026-0003', 16, 31, '2026-08-03', 'TRANSFER', 119, 4, NULL, 658000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-08 02:12:49', '2026-08-08 02:13:58'),
(5, 'EXP-2026-0004', 16, 32, '2026-08-03', 'TRANSFER', 119, 4, NULL, 30000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-08 02:13:20', '2026-08-08 02:14:06'),
(6, 'EXP-2026-0005', 16, 34, '2026-08-04', 'TARIK TUNAI', 119, 4, NULL, 203000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-08 02:19:30', '2026-08-08 02:23:47'),
(7, 'EXP-2026-0006', 16, 35, '2026-08-04', 'TRANSFER', 119, 4, NULL, 275000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-08 02:20:12', '2026-08-08 02:23:59'),
(8, 'EXP-2026-0007', 16, 36, '2026-08-04', 'TRANSFER', 119, 4, NULL, 100000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-08 02:21:03', '2026-08-08 02:24:11'),
(9, 'EXP-2026-0008', 16, 37, '2026-08-04', 'TRANSFER', 119, 4, NULL, 112000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-08 02:21:48', '2026-08-08 02:24:21'),
(10, 'EXP-2026-0009', 16, 38, '2026-08-04', 'APLIKASI BELI', 119, 4, NULL, 19950.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-08 02:22:27', '2026-08-08 02:24:36'),
(11, 'EXP-2026-0010', 16, 39, '2026-08-04', 'APLIKASI BELI', 119, 4, NULL, 16000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-08 02:22:54', '2026-08-08 02:24:47'),
(12, 'EXP-2026-0011', 16, 40, '2026-08-04', 'TRANSFER', 119, 4, NULL, 114000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-08 02:23:24', '2026-08-08 02:24:58'),
(13, 'EXP-2026-0012', 16, 42, '2026-08-05', 'MAKAN MALAM', 114, 4, NULL, 190000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-08 02:32:35', '2026-08-08 02:32:53'),
(14, 'EXP-2026-0013', 16, 46, '2026-08-06', 'TRANSFER', 119, 4, NULL, 100000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-08 02:37:58', '2026-08-08 02:39:04'),
(15, 'EXP-2026-0014', 16, 45, '2026-08-06', 'TRANSFER', 119, 4, NULL, 203000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-08 02:38:31', '2026-08-08 02:38:48'),
(16, 'EXP-2026-0015', 16, 55, '2026-08-08', 'TARIK TUNAI', 119, 4, NULL, 403000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-10 03:08:14', '2026-08-10 03:16:48'),
(17, 'EXP-2026-0016', 16, 53, '2026-08-08', 'TRANSFER', 119, 4, NULL, 50000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-10 03:08:48', '2026-08-10 03:16:25'),
(18, 'EXP-2026-0017', 16, 52, '2026-08-08', 'TRANSFER', 119, 4, NULL, 250000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-10 03:09:35', '2026-08-10 03:16:16'),
(19, 'EXP-2026-0018', 16, 51, '2026-08-08', 'BELANJA PERLENGKAPAN/BAHAN', 119, 4, NULL, 1509200.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-10 03:11:45', '2026-08-10 03:16:07'),
(20, 'EXP-2026-0019', 16, 54, '2026-08-08', 'BELANJA PERLENGKAPAN/BAHAN', 119, 4, NULL, 1718920.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-10 03:13:41', '2026-08-10 03:16:35'),
(21, 'EXP-2026-0020', 16, 57, '2026-08-09', 'TUKAR SALDO CASH', 119, 4, NULL, 10000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-10 03:19:30', '2026-08-10 03:19:45'),
(22, 'EXP-2026-0021', 16, 60, '2026-08-10', 'TRANSFER', 119, 4, NULL, 260000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-10 19:28:55', '2026-08-11 03:00:10'),
(23, 'EXP-2026-0022', 16, 61, '2026-08-10', 'BELANJA PERLENGKAPAN/BAHAN', 119, 4, NULL, 600000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-11 02:58:59', '2026-08-11 03:00:20'),
(24, 'EXP-2026-0023', 16, 59, '2026-08-10', 'TRANSFER', 119, 4, NULL, 215000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-11 02:59:43', '2026-08-11 03:00:00'),
(25, 'EXP-2026-0024', 16, 63, '2026-08-11', 'MAKAN SIANG', 114, 4, NULL, 303000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-12 03:11:19', '2026-08-12 03:12:09'),
(26, 'EXP-2026-0025', 16, 64, '2026-08-11', 'BELANJA', 119, 4, NULL, 361770.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-12 03:11:49', '2026-08-12 03:12:16'),
(27, 'EXP-2026-0026', 16, 68, '2026-08-12', 'TARIK TUNAI', 119, 4, NULL, 323000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-12 03:14:59', '2026-08-12 03:15:55'),
(28, 'EXP-2026-0027', 16, 67, '2026-08-12', 'TRANSFER', 119, 4, NULL, 300000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-12 03:15:32', '2026-08-12 03:15:46'),
(29, 'EXP-2026-0028', 16, 71, '2026-08-14', 'TRANSFER BAYAR SEABANK', 119, 4, NULL, 893000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-14 01:48:38', '2026-08-14 01:48:58'),
(30, 'EXP-2026-0029', 16, 73, '2026-08-15', 'TRANSFER', 119, 4, NULL, 100000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-15 01:07:21', '2026-08-15 01:07:35'),
(31, 'EXP-2026-0030', 16, 74, '2026-08-15', 'KONSUMSI', 119, 4, NULL, 153000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-15 01:11:02', '2026-08-15 01:11:21'),
(32, 'EXP-2026-0031', 16, 76, '2026-08-15', 'PEMBAYARAN VIRTUAL ACCOUNT', 119, 4, NULL, 594540.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-16 23:51:45', '2026-08-16 23:52:05'),
(33, 'EXP-2026-0032', 16, 79, '2026-08-18', 'BELANJA KAIN', 119, 4, NULL, 2509593.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-18 01:29:32', '2026-08-18 01:30:00'),
(34, 'EXP-2026-0033', 16, 90, '2026-08-26', 'TRANSFER JAGO', 119, 4, NULL, 500000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-26 23:31:10', '2026-08-26 23:41:36'),
(35, 'EXP-2026-0034', 16, 87, '2026-08-26', 'TRANSFER SEABANK', 119, 4, NULL, 200000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-26 23:39:17', '2026-08-26 23:41:12'),
(36, 'EXP-2026-0035', 16, 89, '2026-08-26', 'ADMIN TRANSFER BANK JAGO-SEABANK', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-26 23:40:16', '2026-08-26 23:41:29'),
(37, 'EXP-2026-0036', 16, 88, '2026-08-26', 'TRANSFER JAGO', 119, 4, NULL, 2000000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-26 23:40:53', '2026-08-26 23:41:22'),
(38, 'EXP-2026-0037', 16, 94, '2026-08-27', 'BELANJA PLASTIK BENING 4 BENDEL', 119, 4, NULL, 846550.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-27 18:39:49', '2026-08-27 18:43:09'),
(39, 'EXP-2026-0038', 16, 93, '2026-08-27', 'BIAYA TRANSFER BANK JAGO-SEABANK', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-27 18:40:43', '2026-08-27 18:42:59'),
(40, 'EXP-2026-0039', 16, 91, '2026-08-27', 'BELI PULPEN 12PCS', 119, 4, NULL, 17750.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-27 18:41:31', '2026-08-27 18:42:43'),
(41, 'EXP-2026-0040', 16, 92, '2026-08-27', 'BIAYA ADMIN TRANSFER BANK JAGO-SEABANK', 119, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-27 18:42:11', '2026-08-27 18:42:51'),
(42, 'EXP-2026-0041', 16, 97, '2026-08-28', 'PAJAK BUNGA BANK JAGO', 119, 4, NULL, 110.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-27 23:40:16', '2026-08-27 23:41:54'),
(43, 'EXP-2026-0042', 16, 96, '2026-08-28', 'BELI STIKER SIZE', 119, 4, NULL, 61125.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-27 23:40:52', '2026-08-27 23:41:45'),
(44, 'EXP-2026-0043', 16, 98, '2026-08-28', 'BIAYA ADMIN TRANSFER BANK JAGO-SEABANK', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-27 23:41:23', '2026-08-27 23:42:00'),
(45, 'EXP-2026-0044', 16, 99, '2026-08-28', 'TRANSFER', 119, 4, NULL, 915000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-28 02:00:15', '2026-08-28 02:00:36'),
(46, 'EXP-2026-0045', 16, 101, '2026-08-28', 'TRANSFER', 119, 4, NULL, 738625.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-28 02:46:40', '2026-08-28 02:46:58'),
(47, 'EXP-2026-0046', 16, 103, '2026-08-29', 'MAKAN SIANG', 114, 4, NULL, 153000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-29 00:08:11', '2026-08-29 00:08:59'),
(48, 'EXP-2026-0047', 16, 104, '2026-08-29', 'ADMIN BANK TRANSFER', 114, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-08-29 00:08:46', '2026-08-29 00:09:06'),
(49, 'EXP-2026-0048', 17, 110, '2026-09-02', 'TRANSFER', 119, 4, NULL, 146000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-02 19:11:45', '2026-09-02 19:13:01'),
(50, 'EXP-2026-0049', 17, 111, '2026-09-03', 'BELI PLASTIK HITAM PACKING 12PACK', 119, 4, NULL, 397970.0000, 0, NULL, NULL, 'cancelled', NULL, 1, '2026-09-02 19:12:24', '2026-09-04 02:55:47'),
(51, 'EXP-2026-0050', 17, 113, '2026-09-03', 'TRANSFER', 119, 4, NULL, 477190.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-04 02:49:16', '2026-09-04 02:50:00'),
(52, 'EXP-2026-0051', 17, 114, '2026-09-04', 'MAKAN SIANG', 119, 4, NULL, 100000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-04 02:49:49', '2026-09-04 02:50:10'),
(53, 'EXP-2026-0052', 17, 118, '2026-09-03', 'BELI PLASTIK HITAM', 119, 4, NULL, 397972.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-04 02:56:32', '2026-09-04 02:56:53'),
(54, 'EXP-2026-0053', 17, 123, '2026-09-05', 'MAKAN SIANG', 114, 4, NULL, 203000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-06 19:21:24', '2026-09-06 19:22:56'),
(55, 'EXP-2026-0054', 17, 122, '2026-09-05', 'ADMIN TF BANK JAGO-BRI', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-06 19:22:08', '2026-09-06 19:22:45'),
(56, 'EXP-2026-0055', 17, 126, '2026-09-08', 'TRANSFER', 119, 4, NULL, 400000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-08 20:33:32', '2026-09-08 20:36:31'),
(57, 'EXP-2026-0056', 17, 128, '2026-09-09', 'BELANJA BAHAN', 119, 4, NULL, 2058370.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-08 20:34:27', '2026-09-08 20:36:50'),
(58, 'EXP-2026-0057', 17, 130, '2026-09-09', 'BIAYA TRANSFER BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-08 20:35:04', '2026-09-08 20:37:08'),
(59, 'EXP-2026-0058', 17, 129, '2026-09-09', 'BELANJA BAHAN', 119, 4, NULL, 1670497.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-08 20:35:42', '2026-09-08 20:37:00'),
(60, 'EXP-2026-0059', 17, 127, '2026-09-09', 'ADMIN TRANSFER BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-08 20:36:12', '2026-09-08 20:36:39'),
(61, 'EXP-2026-0060', 17, 131, '2026-09-09', 'TARIK TUNAI-KOPI', 119, 4, NULL, 203000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-09 02:37:17', '2026-09-09 02:38:13'),
(62, 'EXP-2026-0061', 17, 132, '2026-09-09', 'ADMIN TRANSFER BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-09 02:37:46', '2026-09-09 02:38:20'),
(63, 'EXP-2026-0062', 17, NULL, '2026-09-01', 'carry forward dari Agustus 2026', 119, 4, NULL, 44500.0000, 0, NULL, NULL, 'draft', NULL, 1, '2026-09-09 18:52:51', '2026-09-09 18:53:23'),
(64, 'EXP-2026-0063', 17, 134, '2026-09-10', 'carry forward dari Agustus 2026', 119, 4, NULL, 44500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-09 18:54:47', '2026-09-09 18:54:59'),
(65, 'EXP-2026-0064', 17, 139, '2026-09-11', 'BELI ZIPPER KUNING-OREN-JOTOL MASING MASING 2', 119, 4, NULL, 320700.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-10 20:43:03', '2026-09-10 20:45:08'),
(66, 'EXP-2026-0065', 17, 138, '2026-09-11', 'ADMIN TRANSFER BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-10 20:43:26', '2026-09-10 20:44:58'),
(67, 'EXP-2026-0066', 17, 136, '2026-09-11', 'BELI MATA AYAM 3PACK', 119, 4, NULL, 172200.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-10 20:43:57', '2026-09-10 20:44:40'),
(68, 'EXP-2026-0067', 17, 137, '2026-09-11', 'ADMIN TRANSFER BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-10 20:44:22', '2026-09-10 20:44:53'),
(69, 'EXP-2026-0068', 17, 143, '2026-09-12', 'TRANSFER', 119, 4, NULL, 200000.0000, 0, NULL, NULL, 'cancelled', NULL, 1, '2026-09-13 20:49:47', '2026-09-13 23:17:33'),
(70, 'EXP-2026-0069', 17, 145, '2026-09-12', 'TRANSFER', 119, 4, NULL, 250000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-13 23:18:21', '2026-09-13 23:18:35'),
(71, 'EXP-2026-0070', 17, 148, '2026-09-14', 'TARIK TUNAI', 119, 4, NULL, 103000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-14 18:41:32', '2026-09-14 18:42:55'),
(72, 'EXP-2026-0071', 17, 147, '2026-09-14', 'ADMIN BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-14 18:42:35', '2026-09-14 18:42:47'),
(73, 'EXP-2026-0072', 17, 149, '2026-09-15', 'BELI ZIPPER PUTIH 30', 119, 4, NULL, 688950.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-15 01:31:16', '2026-09-15 01:32:55'),
(74, 'EXP-2026-0073', 17, 150, '2026-09-15', 'ADMIN BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-15 01:31:38', '2026-09-15 01:33:01'),
(75, 'EXP-2026-0074', 17, 152, '2026-09-16', 'TRANSFER', 119, 4, NULL, 1000000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-16 00:20:12', '2026-09-16 00:31:39'),
(76, 'EXP-2026-0075', 17, 153, '2026-09-16', 'ADMIN BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-16 00:21:03', '2026-09-16 00:31:45'),
(77, 'EXP-2026-0076', 17, 155, '2026-09-16', 'TRANSFER', 119, 4, NULL, 1000000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-16 00:30:09', '2026-09-16 00:32:10'),
(78, 'EXP-2026-0077', 17, 154, '2026-09-16', 'ADMIN BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-16 00:30:33', '2026-09-16 00:32:03'),
(79, 'EXP-2026-0078', 17, 156, '2026-09-16', 'BELI PLASTIK BENING', 119, 4, NULL, 415800.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-16 00:36:52', '2026-09-16 00:37:54'),
(80, 'EXP-2026-0079', 17, 157, '2026-09-16', 'ADMIN BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-16 00:37:25', '2026-09-16 00:38:04'),
(81, 'EXP-2026-0080', 17, 160, '2026-09-18', 'TRANSFER', 119, 4, NULL, 2110321.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-17 23:34:25', '2026-09-17 23:34:52'),
(82, 'EXP-2026-0081', 17, 166, '2026-09-18', 'TARIK TUNAI KONSUMSI', 114, 4, NULL, 497000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-20 18:56:07', '2026-09-20 19:00:46'),
(83, 'EXP-2026-0082', 17, 168, '2026-09-18', 'ADMIN BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-20 18:56:44', '2026-09-20 19:00:59'),
(84, 'EXP-2026-0083', 17, 169, '2026-09-18', 'TARIK TUNAI + TITIPAN MB SALVA', 119, 4, NULL, 367000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-20 18:57:26', '2026-09-20 19:01:05'),
(85, 'EXP-2026-0084', 17, 167, '2026-09-18', 'ADMIN BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-20 19:00:25', '2026-09-20 19:00:52'),
(86, 'EXP-2026-0085', 17, 170, '2026-09-21', 'BELI PLASTIK HITAM 15', 119, 4, NULL, 473074.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-20 20:30:26', '2026-09-20 20:31:06'),
(87, 'EXP-2026-0086', 17, 171, '2026-09-21', 'ADMIN BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-20 20:30:51', '2026-09-20 20:31:13'),
(88, 'EXP-2026-0087', 17, 172, '2026-09-21', 'BELI MINYAK OBRAS 5', 119, 4, NULL, 262000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-21 03:10:29', '2026-09-21 03:11:03'),
(89, 'EXP-2026-0088', 17, 173, '2026-09-21', 'ADMIN BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-21 03:10:51', '2026-09-21 03:11:08'),
(90, 'EXP-2026-0089', 17, 176, '2026-09-21', 'TRANSFER', 119, 4, NULL, 400000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-21 18:33:12', '2026-09-21 18:36:39'),
(91, 'EXP-2026-0090', 17, 178, '2026-09-21', 'ADMIN BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-21 18:34:00', '2026-09-21 18:36:54'),
(92, 'EXP-2026-0091', 17, 177, '2026-09-21', 'SHELY CASH', 119, 4, NULL, 18000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-21 18:34:36', '2026-09-21 18:36:47'),
(93, 'EXP-2026-0092', 17, 175, '2026-09-21', 'ADMIN BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-21 18:35:03', '2026-09-21 18:36:32'),
(94, 'EXP-2026-0093', 17, 181, '2026-09-22', 'TARIK TUNAI', 119, 4, NULL, 203000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-22 18:07:22', '2026-09-22 18:08:06'),
(95, 'EXP-2026-0094', 17, 180, '2026-09-22', 'ADMIN BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-22 18:07:45', '2026-09-22 18:07:59'),
(96, 'EXP-2026-0095', 17, 186, '2026-09-24', 'TARIK TUNAI', 119, 4, NULL, 203000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-24 20:36:58', '2026-09-24 20:38:20'),
(97, 'EXP-2026-0096', 17, 185, '2026-09-24', 'ADMIN BANK', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-24 20:37:33', '2026-09-24 20:38:12'),
(98, 'EXP-2026-0097', 17, 187, '2026-09-25', 'TRANSFER', 119, 4, NULL, 1400000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-24 20:39:08', '2026-09-24 20:42:52'),
(99, 'EXP-2026-0098', 17, 188, '2026-09-25', 'ADMIN BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-24 20:39:54', '2026-09-24 20:42:59'),
(100, 'EXP-2026-0099', 17, 189, '2026-09-26', 'TRANSFER', 119, 4, NULL, 100000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-27 18:27:40', '2026-09-27 18:30:58'),
(101, 'EXP-2026-0100', 17, 190, '2026-09-26', 'ADMIN BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-27 18:28:13', '2026-09-27 18:31:13'),
(102, 'EXP-2026-0101', 17, 191, '2026-09-28', 'TARIK TUNAI', 119, 4, NULL, 200000.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-27 18:28:56', '2026-09-27 18:31:20'),
(103, 'EXP-2026-0102', 17, 192, '2026-09-28', 'ADMIN BANK JAGO', 113, 4, NULL, 2500.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-27 18:29:55', '2026-09-27 18:31:27'),
(104, 'EXP-2026-0103', 17, 197, '2026-09-28', 'PAJAK BUNGA BANK JAGO', 119, 4, NULL, 89.0000, 0, NULL, NULL, 'posted', NULL, 1, '2026-09-27 23:56:18', '2026-09-27 23:56:33');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fixed_assets`
--

CREATE TABLE `fixed_assets` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `acquisition_date` date NOT NULL,
  `acquisition_cost` decimal(16,2) NOT NULL,
  `salvage_value` decimal(16,2) NOT NULL DEFAULT '0.00',
  `useful_life_years` int NOT NULL,
  `depreciation_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'straight_line',
  `accumulated_depreciation` decimal(16,2) NOT NULL DEFAULT '0.00',
  `monthly_depreciation` decimal(16,2) NOT NULL DEFAULT '0.00',
  `is_fully_depreciated` tinyint(1) NOT NULL DEFAULT '0',
  `asset_account_id` bigint UNSIGNED DEFAULT NULL,
  `depreciation_account_id` bigint UNSIGNED DEFAULT NULL,
  `accumulated_depreciation_account_id` bigint UNSIGNED DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `disposed_date` date DEFAULT NULL,
  `disposal_amount` decimal(16,2) DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fund_transfers`
--

CREATE TABLE `fund_transfers` (
  `id` bigint UNSIGNED NOT NULL,
  `number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_id` bigint UNSIGNED NOT NULL,
  `journal_id` bigint UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `from_wallet_id` bigint UNSIGNED NOT NULL,
  `to_wallet_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(18,4) NOT NULL,
  `fee_amount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `fee_account_id` bigint UNSIGNED DEFAULT NULL,
  `reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','posted','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_movements`
--

CREATE TABLE `inventory_movements` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `type` enum('in','out','adjustment') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ref_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref_id` bigint UNSIGNED DEFAULT NULL,
  `qty` decimal(18,4) NOT NULL,
  `unit_cost` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `total_cost` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `journal_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_id` bigint UNSIGNED NOT NULL,
  `period_id` bigint UNSIGNED NOT NULL,
  `journal_id` bigint UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `subtotal` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `discount_amount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `tax_amount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `total` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `paid_amount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `due_amount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `status` enum('draft','posted','partially_paid','paid','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ref_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `is_cash_sale` tinyint(1) NOT NULL DEFAULT '0',
  `wallet_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` decimal(18,4) NOT NULL DEFAULT '1.0000',
  `unit` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `discount_percent` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `discount_amount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `subtotal` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `revenue_account_id` bigint UNSIGNED DEFAULT NULL,
  `cogs_account_id` bigint UNSIGNED DEFAULT NULL,
  `cost_price` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_taxes`
--

CREATE TABLE `invoice_taxes` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_id` bigint UNSIGNED NOT NULL,
  `tax_rule_id` bigint UNSIGNED NOT NULL,
  `tax_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` enum('exclusive','inclusive','withholding') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` decimal(8,4) NOT NULL,
  `base_amount` decimal(18,4) NOT NULL,
  `tax_amount` decimal(18,4) NOT NULL,
  `debit_account_id` bigint UNSIGNED NOT NULL,
  `credit_account_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journals`
--

CREATE TABLE `journals` (
  `id` bigint UNSIGNED NOT NULL,
  `number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `period_id` bigint UNSIGNED NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `type` enum('normal','reversal','void','closing','opening') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `ref_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref_id` bigint UNSIGNED DEFAULT NULL,
  `reversed_journal_id` bigint UNSIGNED DEFAULT NULL,
  `reversed_by_journal_id` bigint UNSIGNED DEFAULT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `journals`
--

INSERT INTO `journals` (`id`, `number`, `date`, `period_id`, `description`, `source`, `type`, `ref_type`, `ref_id`, `reversed_journal_id`, `reversed_by_journal_id`, `is_posted`, `created_by`, `created_at`, `updated_at`) VALUES
(25, 'JNL/2026/08/00004', '2026-08-01', 16, 'Saldo Awal: Bank Umum', 'opening', 'opening', 'wallets', 4, NULL, NULL, 1, 1, '2026-08-08 00:19:28', '2026-08-08 00:19:28'),
(26, 'JNL/2026/08/00005', '2026-08-08', 16, 'Beban: lain lain (EXP-2026-0001)', 'expense', 'normal', 'expenses', 2, NULL, NULL, 1, 1, '2026-08-08 00:27:48', '2026-08-08 00:27:48'),
(27, 'JNL/2026/08/00006', '2026-08-01', 16, 'Kas Masuk Lainnya: RCP/2026/08/00001 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 1, NULL, NULL, 1, 1, '2026-08-08 00:28:39', '2026-08-08 00:28:39'),
(28, 'JNL/2026/08/00007', '2026-08-02', 16, 'Kas Masuk Lainnya: RCP/2026/08/00002 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 2, NULL, NULL, 1, 1, '2026-08-08 02:06:39', '2026-08-08 02:06:39'),
(29, 'JNL/2026/08/00008', '2026-08-03', 16, 'Kas Masuk Lainnya: RCP/2026/08/00003 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 3, NULL, NULL, 1, 1, '2026-08-08 02:08:57', '2026-08-08 02:08:57'),
(30, 'JNL/2026/08/00009', '2026-08-03', 16, 'Beban: Makan Siang (EXP-2026-0002)', 'expense', 'normal', 'expenses', 3, NULL, NULL, 1, 1, '2026-08-08 02:13:48', '2026-08-08 02:13:48'),
(31, 'JNL/2026/08/00010', '2026-08-03', 16, 'Beban: TRANSFER (EXP-2026-0003)', 'expense', 'normal', 'expenses', 4, NULL, NULL, 1, 1, '2026-08-08 02:13:57', '2026-08-08 02:13:57'),
(32, 'JNL/2026/08/00011', '2026-08-03', 16, 'Beban: TRANSFER (EXP-2026-0004)', 'expense', 'normal', 'expenses', 5, NULL, NULL, 1, 1, '2026-08-08 02:14:06', '2026-08-08 02:14:06'),
(33, 'JNL/2026/08/00012', '2026-08-04', 16, 'Kas Masuk Lainnya: RCP/2026/08/00004 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 4, NULL, NULL, 1, 1, '2026-08-08 02:16:56', '2026-08-08 02:16:56'),
(34, 'JNL/2026/08/00013', '2026-08-04', 16, 'Beban: TARIK TUNAI (EXP-2026-0005)', 'expense', 'normal', 'expenses', 6, NULL, NULL, 1, 1, '2026-08-08 02:23:47', '2026-08-08 02:23:47'),
(35, 'JNL/2026/08/00014', '2026-08-04', 16, 'Beban: TRANSFER (EXP-2026-0006)', 'expense', 'normal', 'expenses', 7, NULL, NULL, 1, 1, '2026-08-08 02:23:59', '2026-08-08 02:23:59'),
(36, 'JNL/2026/08/00015', '2026-08-04', 16, 'Beban: TRANSFER (EXP-2026-0007)', 'expense', 'normal', 'expenses', 8, NULL, NULL, 1, 1, '2026-08-08 02:24:11', '2026-08-08 02:24:11'),
(37, 'JNL/2026/08/00016', '2026-08-04', 16, 'Beban: TRANSFER (EXP-2026-0008)', 'expense', 'normal', 'expenses', 9, NULL, NULL, 1, 1, '2026-08-08 02:24:21', '2026-08-08 02:24:21'),
(38, 'JNL/2026/08/00017', '2026-08-04', 16, 'Beban: APLIKASI BELI (EXP-2026-0009)', 'expense', 'normal', 'expenses', 10, NULL, NULL, 1, 1, '2026-08-08 02:24:36', '2026-08-08 02:24:36'),
(39, 'JNL/2026/08/00018', '2026-08-04', 16, 'Beban: APLIKASI BELI (EXP-2026-0010)', 'expense', 'normal', 'expenses', 11, NULL, NULL, 1, 1, '2026-08-08 02:24:47', '2026-08-08 02:24:47'),
(40, 'JNL/2026/08/00019', '2026-08-04', 16, 'Beban: TRANSFER (EXP-2026-0011)', 'expense', 'normal', 'expenses', 12, NULL, NULL, 1, 1, '2026-08-08 02:24:58', '2026-08-08 02:24:58'),
(41, 'JNL/2026/08/00020', '2026-08-05', 16, 'Kas Masuk Lainnya: RCP/2026/08/00005 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 5, NULL, NULL, 1, 1, '2026-08-08 02:30:59', '2026-08-08 02:30:59'),
(42, 'JNL/2026/08/00021', '2026-08-05', 16, 'Beban: MAKAN MALAM (EXP-2026-0012)', 'expense', 'normal', 'expenses', 13, NULL, NULL, 1, 1, '2026-08-08 02:32:53', '2026-08-08 02:32:53'),
(43, 'JNL/2026/08/00022', '2026-08-06', 16, 'Kas Masuk Lainnya: RCP/2026/08/00007 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 7, NULL, NULL, 1, 1, '2026-08-08 02:35:18', '2026-08-08 02:35:18'),
(44, 'JNL/2026/08/00023', '2026-08-06', 16, 'Kas Masuk Lainnya: RCP/2026/08/00006 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 6, NULL, NULL, 1, 1, '2026-08-08 02:35:27', '2026-08-08 02:35:27'),
(45, 'JNL/2026/08/00024', '2026-08-06', 16, 'Beban: TRANSFER (EXP-2026-0014)', 'expense', 'normal', 'expenses', 15, NULL, NULL, 1, 1, '2026-08-08 02:38:48', '2026-08-08 02:38:48'),
(46, 'JNL/2026/08/00025', '2026-08-06', 16, 'Beban: TRANSFER (EXP-2026-0013)', 'expense', 'normal', 'expenses', 14, NULL, NULL, 1, 1, '2026-08-08 02:39:04', '2026-08-08 02:39:04'),
(47, 'JNL/2026/08/00026', '2026-08-07', 16, 'Kas Masuk Lainnya: RCP/2026/08/00008 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 8, NULL, NULL, 1, 1, '2026-08-08 02:40:47', '2026-08-08 02:40:47'),
(48, 'JNL/2026/08/00027', '2026-08-08', 16, 'Kas Masuk Lainnya: RCP/2026/08/00009 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 9, NULL, NULL, 1, 1, '2026-08-10 03:06:46', '2026-08-10 03:06:46'),
(49, 'JNL/2026/08/00028', '2026-08-08', 16, 'Kas Masuk Lainnya: RCP/2026/08/00010 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 10, NULL, NULL, 1, 1, '2026-08-10 03:06:52', '2026-08-10 03:06:52'),
(50, 'JNL/2026/08/00029', '2026-08-08', 16, 'Kas Masuk Lainnya: RCP/2026/08/00011 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 11, NULL, NULL, 1, 1, '2026-08-10 03:06:58', '2026-08-10 03:06:58'),
(51, 'JNL/2026/08/00030', '2026-08-08', 16, 'Beban: BELANJA PERLENGKAPAN/BAHAN (EXP-2026-0018)', 'expense', 'normal', 'expenses', 19, NULL, NULL, 1, 1, '2026-08-10 03:16:07', '2026-08-10 03:16:07'),
(52, 'JNL/2026/08/00031', '2026-08-08', 16, 'Beban: TRANSFER (EXP-2026-0017)', 'expense', 'normal', 'expenses', 18, NULL, NULL, 1, 1, '2026-08-10 03:16:16', '2026-08-10 03:16:16'),
(53, 'JNL/2026/08/00032', '2026-08-08', 16, 'Beban: TRANSFER (EXP-2026-0016)', 'expense', 'normal', 'expenses', 17, NULL, NULL, 1, 1, '2026-08-10 03:16:25', '2026-08-10 03:16:25'),
(54, 'JNL/2026/08/00033', '2026-08-08', 16, 'Beban: BELANJA PERLENGKAPAN/BAHAN (EXP-2026-0019)', 'expense', 'normal', 'expenses', 20, NULL, NULL, 1, 1, '2026-08-10 03:16:35', '2026-08-10 03:16:35'),
(55, 'JNL/2026/08/00034', '2026-08-08', 16, 'Beban: TARIK TUNAI (EXP-2026-0015)', 'expense', 'normal', 'expenses', 16, NULL, NULL, 1, 1, '2026-08-10 03:16:48', '2026-08-10 03:16:48'),
(56, 'JNL/2026/08/00035', '2026-08-09', 16, 'Kas Masuk Lainnya: RCP/2026/08/00012 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 12, NULL, NULL, 1, 1, '2026-08-10 03:18:35', '2026-08-10 03:18:35'),
(57, 'JNL/2026/08/00036', '2026-08-09', 16, 'Beban: TUKAR SALDO CASH (EXP-2026-0020)', 'expense', 'normal', 'expenses', 21, NULL, NULL, 1, 1, '2026-08-10 03:19:45', '2026-08-10 03:19:45'),
(58, 'JNL/2026/08/00037', '2026-08-10', 16, 'Kas Masuk Lainnya: RCP/2026/08/00013 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 13, NULL, NULL, 1, 1, '2026-08-10 19:26:47', '2026-08-10 19:26:47'),
(59, 'JNL/2026/08/00038', '2026-08-10', 16, 'Beban: TRANSFER (EXP-2026-0023)', 'expense', 'normal', 'expenses', 24, NULL, NULL, 1, 1, '2026-08-11 03:00:00', '2026-08-11 03:00:00'),
(60, 'JNL/2026/08/00039', '2026-08-10', 16, 'Beban: TRANSFER (EXP-2026-0021)', 'expense', 'normal', 'expenses', 22, NULL, NULL, 1, 1, '2026-08-11 03:00:10', '2026-08-11 03:00:10'),
(61, 'JNL/2026/08/00040', '2026-08-10', 16, 'Beban: BELANJA PERLENGKAPAN/BAHAN (EXP-2026-0022)', 'expense', 'normal', 'expenses', 23, NULL, NULL, 1, 1, '2026-08-11 03:00:20', '2026-08-11 03:00:20'),
(62, 'JNL/2026/08/00041', '2026-08-11', 16, 'Kas Masuk Lainnya: RCP/2026/08/00014 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 14, NULL, NULL, 1, 1, '2026-08-12 03:10:17', '2026-08-12 03:10:17'),
(63, 'JNL/2026/08/00042', '2026-08-11', 16, 'Beban: MAKAN SIANG (EXP-2026-0024)', 'expense', 'normal', 'expenses', 25, NULL, NULL, 1, 1, '2026-08-12 03:12:09', '2026-08-12 03:12:09'),
(64, 'JNL/2026/08/00043', '2026-08-11', 16, 'Beban: BELANJA (EXP-2026-0025)', 'expense', 'normal', 'expenses', 26, NULL, NULL, 1, 1, '2026-08-12 03:12:16', '2026-08-12 03:12:16'),
(65, 'JNL/2026/08/00044', '2026-08-12', 16, 'Kas Masuk Lainnya: RCP/2026/08/00016 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 16, NULL, NULL, 1, 1, '2026-08-12 03:13:59', '2026-08-12 03:13:59'),
(66, 'JNL/2026/08/00045', '2026-08-12', 16, 'Kas Masuk Lainnya: RCP/2026/08/00015 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 15, NULL, NULL, 1, 1, '2026-08-12 03:14:07', '2026-08-12 03:14:07'),
(67, 'JNL/2026/08/00046', '2026-08-12', 16, 'Beban: TRANSFER (EXP-2026-0027)', 'expense', 'normal', 'expenses', 28, NULL, NULL, 1, 1, '2026-08-12 03:15:46', '2026-08-12 03:15:46'),
(68, 'JNL/2026/08/00047', '2026-08-12', 16, 'Beban: TARIK TUNAI (EXP-2026-0026)', 'expense', 'normal', 'expenses', 27, NULL, NULL, 1, 1, '2026-08-12 03:15:55', '2026-08-12 03:15:55'),
(69, 'JNL/2026/08/00048', '2026-08-13', 16, 'Kas Masuk Lainnya: RCP/2026/08/00017 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 17, NULL, NULL, 1, 1, '2026-08-13 02:50:13', '2026-08-13 02:50:13'),
(70, 'JNL/2026/08/00049', '2026-08-14', 16, 'Kas Masuk Lainnya: RCP/2026/08/00018 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 18, NULL, NULL, 1, 1, '2026-08-14 01:47:36', '2026-08-14 01:47:36'),
(71, 'JNL/2026/08/00050', '2026-08-14', 16, 'Beban: TRANSFER BAYAR SEABANK (EXP-2026-0028)', 'expense', 'normal', 'expenses', 29, NULL, NULL, 1, 1, '2026-08-14 01:48:58', '2026-08-14 01:48:58'),
(72, 'JNL/2026/08/00051', '2026-08-15', 16, 'Kas Masuk Lainnya: RCP/2026/08/00019 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 19, NULL, NULL, 1, 1, '2026-08-15 01:06:42', '2026-08-15 01:06:42'),
(73, 'JNL/2026/08/00052', '2026-08-15', 16, 'Beban: TRANSFER (EXP-2026-0029)', 'expense', 'normal', 'expenses', 30, NULL, NULL, 1, 1, '2026-08-15 01:07:35', '2026-08-15 01:07:35'),
(74, 'JNL/2026/08/00053', '2026-08-15', 16, 'Beban: KONSUMSI (EXP-2026-0030)', 'expense', 'normal', 'expenses', 31, NULL, NULL, 1, 1, '2026-08-15 01:11:21', '2026-08-15 01:11:21'),
(75, 'JNL/2026/08/00054', '2026-08-16', 16, 'Kas Masuk Lainnya: RCP/2026/08/00020 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 20, NULL, NULL, 1, 1, '2026-08-16 23:49:48', '2026-08-16 23:49:48'),
(76, 'JNL/2026/08/00055', '2026-08-15', 16, 'Beban: PEMBAYARAN VIRTUAL ACCOUNT (EXP-2026-0031)', 'expense', 'normal', 'expenses', 32, NULL, NULL, 1, 1, '2026-08-16 23:52:05', '2026-08-16 23:52:05'),
(77, 'JNL/2026/08/00056', '2026-08-17', 16, 'Kas Masuk Lainnya: RCP/2026/08/00021 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 21, NULL, NULL, 1, 1, '2026-08-16 23:54:10', '2026-08-16 23:54:10'),
(78, 'JNL/2026/08/00057', '2026-08-18', 16, 'Kas Masuk Lainnya: RCP/2026/08/00022 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 22, NULL, NULL, 1, 1, '2026-08-17 21:09:52', '2026-08-17 21:09:52'),
(79, 'JNL/2026/08/00058', '2026-08-18', 16, 'Beban: BELANJA KAIN (EXP-2026-0032)', 'expense', 'normal', 'expenses', 33, NULL, NULL, 1, 1, '2026-08-18 01:30:00', '2026-08-18 01:30:00'),
(80, 'JNL/2026/08/00059', '2026-08-20', 16, 'Kas Masuk Lainnya: RCP/2026/08/00023 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 23, NULL, NULL, 1, 1, '2026-08-19 20:37:09', '2026-08-19 20:37:09'),
(81, 'JNL/2026/08/00060', '2026-08-21', 16, 'Kas Masuk Lainnya: RCP/2026/08/00024 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 24, NULL, NULL, 1, 1, '2026-08-23 18:21:53', '2026-08-23 18:21:53'),
(82, 'JNL/2026/08/00061', '2026-08-22', 16, 'Kas Masuk Lainnya: RCP/2026/08/00025 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 25, NULL, NULL, 1, 1, '2026-08-23 18:22:00', '2026-08-23 18:22:00'),
(83, 'JNL/2026/08/00062', '2026-08-23', 16, 'Kas Masuk Lainnya: RCP/2026/08/00026 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 26, NULL, NULL, 1, 1, '2026-08-23 18:22:08', '2026-08-23 18:22:08'),
(84, 'JNL/2026/08/00063', '2026-08-24', 16, 'Kas Masuk Lainnya: RCP/2026/08/00027 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 27, NULL, NULL, 1, 1, '2026-08-24 03:18:38', '2026-08-24 03:18:38'),
(85, 'JNL/2026/08/00064', '2026-08-25', 16, 'Kas Masuk Lainnya: RCP/2026/08/00028 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 28, NULL, NULL, 1, 1, '2026-08-26 00:41:45', '2026-08-26 00:41:45'),
(86, 'JNL/2026/08/00065', '2026-08-27', 16, 'Kas Masuk Lainnya: RCP/2026/08/00029 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 29, NULL, NULL, 1, 1, '2026-08-26 23:13:30', '2026-08-26 23:13:30'),
(87, 'JNL/2026/08/00066', '2026-08-26', 16, 'Beban: TRANSFER SEABANK (EXP-2026-0034)', 'expense', 'normal', 'expenses', 35, NULL, NULL, 1, 1, '2026-08-26 23:41:12', '2026-08-26 23:41:12'),
(88, 'JNL/2026/08/00067', '2026-08-26', 16, 'Beban: TRANSFER JAGO (EXP-2026-0036)', 'expense', 'normal', 'expenses', 37, NULL, NULL, 1, 1, '2026-08-26 23:41:22', '2026-08-26 23:41:22'),
(89, 'JNL/2026/08/00068', '2026-08-26', 16, 'Beban: ADMIN TRANSFER BANK JAGO-SEABANK (EXP-2026-0035)', 'expense', 'normal', 'expenses', 36, NULL, NULL, 1, 1, '2026-08-26 23:41:29', '2026-08-26 23:41:29'),
(90, 'JNL/2026/08/00069', '2026-08-26', 16, 'Beban: TRANSFER JAGO (EXP-2026-0033)', 'expense', 'normal', 'expenses', 34, NULL, NULL, 1, 1, '2026-08-26 23:41:36', '2026-08-26 23:41:36'),
(91, 'JNL/2026/08/00070', '2026-08-27', 16, 'Beban: BELI PULPEN 12PCS (EXP-2026-0039)', 'expense', 'normal', 'expenses', 40, NULL, NULL, 1, 1, '2026-08-27 18:42:42', '2026-08-27 18:42:42'),
(92, 'JNL/2026/08/00071', '2026-08-27', 16, 'Beban: BIAYA ADMIN TRANSFER BANK JAGO-SEABANK (EXP-2026-0040)', 'expense', 'normal', 'expenses', 41, NULL, NULL, 1, 1, '2026-08-27 18:42:51', '2026-08-27 18:42:51'),
(93, 'JNL/2026/08/00072', '2026-08-27', 16, 'Beban: BIAYA TRANSFER BANK JAGO-SEABANK (EXP-2026-0038)', 'expense', 'normal', 'expenses', 39, NULL, NULL, 1, 1, '2026-08-27 18:42:59', '2026-08-27 18:42:59'),
(94, 'JNL/2026/08/00073', '2026-08-27', 16, 'Beban: BELANJA PLASTIK BENING 4 BENDEL (EXP-2026-0037)', 'expense', 'normal', 'expenses', 38, NULL, NULL, 1, 1, '2026-08-27 18:43:09', '2026-08-27 18:43:09'),
(95, 'JNL/2026/08/00074', '2026-08-28', 16, 'Kas Masuk Lainnya: RCP/2026/08/00030 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 30, NULL, NULL, 1, 1, '2026-08-27 23:39:35', '2026-08-27 23:39:35'),
(96, 'JNL/2026/08/00075', '2026-08-28', 16, 'Beban: BELI STIKER SIZE (EXP-2026-0042)', 'expense', 'normal', 'expenses', 43, NULL, NULL, 1, 1, '2026-08-27 23:41:45', '2026-08-27 23:41:45'),
(97, 'JNL/2026/08/00076', '2026-08-28', 16, 'Beban: PAJAK BUNGA BANK JAGO (EXP-2026-0041)', 'expense', 'normal', 'expenses', 42, NULL, NULL, 1, 1, '2026-08-27 23:41:54', '2026-08-27 23:41:54'),
(98, 'JNL/2026/08/00077', '2026-08-28', 16, 'Beban: BIAYA ADMIN TRANSFER BANK JAGO-SEABANK (EXP-2026-0043)', 'expense', 'normal', 'expenses', 44, NULL, NULL, 1, 1, '2026-08-27 23:42:00', '2026-08-27 23:42:00'),
(99, 'JNL/2026/08/00078', '2026-08-28', 16, 'Beban: TRANSFER (EXP-2026-0044)', 'expense', 'normal', 'expenses', 45, NULL, NULL, 1, 1, '2026-08-28 02:00:36', '2026-08-28 02:00:36'),
(100, 'JNL/2026/08/00079', '2026-08-28', 16, 'Kas Masuk Lainnya: RCP/2026/08/00031 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 31, NULL, NULL, 1, 1, '2026-08-28 02:45:46', '2026-08-28 02:45:46'),
(101, 'JNL/2026/08/00080', '2026-08-28', 16, 'Beban: TRANSFER (EXP-2026-0045)', 'expense', 'normal', 'expenses', 46, NULL, NULL, 1, 1, '2026-08-28 02:46:58', '2026-08-28 02:46:58'),
(102, 'JNL/2026/08/00081', '2026-08-29', 16, 'Kas Masuk Lainnya: RCP/2026/08/00032 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 32, NULL, NULL, 1, 1, '2026-08-29 00:07:30', '2026-08-29 00:07:30'),
(103, 'JNL/2026/08/00082', '2026-08-29', 16, 'Beban: MAKAN SIANG (EXP-2026-0046)', 'expense', 'normal', 'expenses', 47, NULL, NULL, 1, 1, '2026-08-29 00:08:59', '2026-08-29 00:08:59'),
(104, 'JNL/2026/08/00083', '2026-08-29', 16, 'Beban: ADMIN BANK TRANSFER (EXP-2026-0047)', 'expense', 'normal', 'expenses', 48, NULL, NULL, 1, 1, '2026-08-29 00:09:06', '2026-08-29 00:09:06'),
(105, 'JNL/2026/08/00084', '2026-08-31', 16, 'Jurnal Penutup — Agustus 2026', 'system', 'closing', NULL, NULL, NULL, NULL, 1, 1, '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(106, 'JNL/2026/09/00001', '2026-09-01', 17, 'Saldo Awal — September 2026 (carry forward dari Agustus 2026)', 'system', 'opening', NULL, NULL, NULL, NULL, 1, 1, '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(107, 'JNL/2026/09/00002', '2026-09-01', 17, 'Kas Masuk Lainnya: RCP/2026/09/00001 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 33, NULL, NULL, 1, 1, '2026-09-02 19:10:24', '2026-09-02 19:10:24'),
(108, 'JNL/2026/09/00003', '2026-09-02', 17, 'Kas Masuk Lainnya: RCP/2026/09/00002 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 34, NULL, NULL, 1, 1, '2026-09-02 19:10:31', '2026-09-02 19:10:31'),
(109, 'JNL/2026/09/00004', '2026-09-03', 17, 'Kas Masuk Lainnya: RCP/2026/09/00003 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 35, NULL, NULL, 1, 1, '2026-09-02 19:10:39', '2026-09-02 19:10:39'),
(110, 'JNL/2026/09/00005', '2026-09-02', 17, 'Beban: TRANSFER (EXP-2026-0048)', 'expense', 'normal', 'expenses', 49, NULL, NULL, 1, 1, '2026-09-02 19:13:01', '2026-09-02 19:13:01'),
(111, 'JNL/2026/09/00006', '2026-09-03', 17, 'Beban: BELI PLASTIK HITAM PACKING 12PACK (EXP-2026-0049)', 'expense', 'void', 'expenses', 50, NULL, 117, 1, 1, '2026-09-02 19:13:09', '2026-09-04 02:55:47'),
(112, 'JNL/2026/09/00007', '2026-09-04', 17, 'Kas Masuk Lainnya: RCP/2026/09/00004 (Pendapatan Lain-lain)', 'other_receipt', 'void', 'other_receipts', 36, NULL, 115, 1, 1, '2026-09-04 02:48:19', '2026-09-04 02:53:24'),
(113, 'JNL/2026/09/00008', '2026-09-03', 17, 'Beban: TRANSFER (EXP-2026-0050)', 'expense', 'normal', 'expenses', 51, NULL, NULL, 1, 1, '2026-09-04 02:50:00', '2026-09-04 02:50:00'),
(114, 'JNL/2026/09/00009', '2026-09-04', 17, 'Beban: MAKAN SIANG (EXP-2026-0051)', 'expense', 'normal', 'expenses', 52, NULL, NULL, 1, 1, '2026-09-04 02:50:10', '2026-09-04 02:50:10'),
(115, 'JNL/2026/09/00010', '2026-09-04', 17, 'Pembatalan penerimaan RCP/2026/09/00004', 'manual', 'reversal', 'journals', 112, NULL, NULL, 1, 1, '2026-09-04 02:53:24', '2026-09-04 02:53:24'),
(116, 'JNL/2026/09/00011', '2026-09-04', 17, 'Kas Masuk Lainnya: RCP/2026/09/00005 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 37, NULL, NULL, 1, 1, '2026-09-04 02:54:14', '2026-09-04 02:54:14'),
(117, 'JNL/2026/09/00012', '2026-09-04', 17, 'Pembatalan beban EXP-2026-0049', 'manual', 'reversal', 'journals', 111, NULL, NULL, 1, 1, '2026-09-04 02:55:47', '2026-09-04 02:55:47'),
(118, 'JNL/2026/09/00013', '2026-09-03', 17, 'Beban: BELI PLASTIK HITAM (EXP-2026-0052)', 'expense', 'normal', 'expenses', 53, NULL, NULL, 1, 1, '2026-09-04 02:56:53', '2026-09-04 02:56:53'),
(119, 'JNL/2026/09/00014', '2026-09-05', 17, 'Kas Masuk Lainnya: RCP/2026/09/00006 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 38, NULL, NULL, 1, 1, '2026-09-05 02:07:42', '2026-09-05 02:07:42'),
(120, 'JNL/2026/09/00015', '2026-09-06', 17, 'Kas Masuk Lainnya: RCP/2026/09/00007 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 39, NULL, NULL, 1, 1, '2026-09-06 19:20:20', '2026-09-06 19:20:20'),
(121, 'JNL/2026/09/00016', '2026-09-07', 17, 'Kas Masuk Lainnya: RCP/2026/09/00008 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 40, NULL, NULL, 1, 1, '2026-09-06 19:20:28', '2026-09-06 19:20:28'),
(122, 'JNL/2026/09/00017', '2026-09-05', 17, 'Beban: ADMIN TF BANK JAGO-BRI (EXP-2026-0054)', 'expense', 'normal', 'expenses', 55, NULL, NULL, 1, 1, '2026-09-06 19:22:45', '2026-09-06 19:22:45'),
(123, 'JNL/2026/09/00018', '2026-09-05', 17, 'Beban: MAKAN SIANG (EXP-2026-0053)', 'expense', 'normal', 'expenses', 54, NULL, NULL, 1, 1, '2026-09-06 19:22:56', '2026-09-06 19:22:56'),
(124, 'JNL/2026/09/00019', '2026-09-08', 17, 'Kas Masuk Lainnya: RCP/2026/09/00009 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 41, NULL, NULL, 1, 1, '2026-09-07 23:49:20', '2026-09-07 23:49:20'),
(125, 'JNL/2026/09/00020', '2026-09-09', 17, 'Kas Masuk Lainnya: RCP/2026/09/00010 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 42, NULL, NULL, 1, 1, '2026-09-08 20:31:05', '2026-09-08 20:31:05'),
(126, 'JNL/2026/09/00021', '2026-09-08', 17, 'Beban: TRANSFER (EXP-2026-0055)', 'expense', 'normal', 'expenses', 56, NULL, NULL, 1, 1, '2026-09-08 20:36:31', '2026-09-08 20:36:31'),
(127, 'JNL/2026/09/00022', '2026-09-09', 17, 'Beban: ADMIN TRANSFER BANK JAGO (EXP-2026-0059)', 'expense', 'normal', 'expenses', 60, NULL, NULL, 1, 1, '2026-09-08 20:36:39', '2026-09-08 20:36:39'),
(128, 'JNL/2026/09/00023', '2026-09-09', 17, 'Beban: BELANJA BAHAN (EXP-2026-0056)', 'expense', 'normal', 'expenses', 57, NULL, NULL, 1, 1, '2026-09-08 20:36:50', '2026-09-08 20:36:50'),
(129, 'JNL/2026/09/00024', '2026-09-09', 17, 'Beban: BELANJA BAHAN (EXP-2026-0058)', 'expense', 'normal', 'expenses', 59, NULL, NULL, 1, 1, '2026-09-08 20:37:00', '2026-09-08 20:37:00'),
(130, 'JNL/2026/09/00025', '2026-09-09', 17, 'Beban: BIAYA TRANSFER BANK JAGO (EXP-2026-0057)', 'expense', 'normal', 'expenses', 58, NULL, NULL, 1, 1, '2026-09-08 20:37:08', '2026-09-08 20:37:08'),
(131, 'JNL/2026/09/00026', '2026-09-09', 17, 'Beban: TARIK TUNAI-KOPI (EXP-2026-0060)', 'expense', 'normal', 'expenses', 61, NULL, NULL, 1, 1, '2026-09-09 02:38:13', '2026-09-09 02:38:13'),
(132, 'JNL/2026/09/00027', '2026-09-09', 17, 'Beban: ADMIN TRANSFER BANK JAGO (EXP-2026-0061)', 'expense', 'normal', 'expenses', 62, NULL, NULL, 1, 1, '2026-09-09 02:38:20', '2026-09-09 02:38:20'),
(133, 'JNL/2026/09/00028', '2026-09-10', 17, 'Kas Masuk Lainnya: RCP/2026/09/00011 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 43, NULL, NULL, 1, 1, '2026-09-09 18:51:26', '2026-09-09 18:51:26'),
(134, 'JNL/2026/09/00029', '2026-09-10', 17, 'Beban: carry forward dari Agustus 2026 (EXP-2026-0063)', 'expense', 'normal', 'expenses', 64, NULL, NULL, 1, 1, '2026-09-09 18:54:59', '2026-09-09 18:54:59'),
(135, 'JNL/2026/09/00030', '2026-09-11', 17, 'Kas Masuk Lainnya: RCP/2026/09/00012 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 44, NULL, NULL, 1, 1, '2026-09-10 20:00:50', '2026-09-10 20:00:50'),
(136, 'JNL/2026/09/00031', '2026-09-11', 17, 'Beban: BELI MATA AYAM 3PACK (EXP-2026-0066)', 'expense', 'normal', 'expenses', 67, NULL, NULL, 1, 1, '2026-09-10 20:44:40', '2026-09-10 20:44:40'),
(137, 'JNL/2026/09/00032', '2026-09-11', 17, 'Beban: ADMIN TRANSFER BANK JAGO (EXP-2026-0067)', 'expense', 'normal', 'expenses', 68, NULL, NULL, 1, 1, '2026-09-10 20:44:53', '2026-09-10 20:44:53'),
(138, 'JNL/2026/09/00033', '2026-09-11', 17, 'Beban: ADMIN TRANSFER BANK JAGO (EXP-2026-0065)', 'expense', 'normal', 'expenses', 66, NULL, NULL, 1, 1, '2026-09-10 20:44:58', '2026-09-10 20:44:58'),
(139, 'JNL/2026/09/00034', '2026-09-11', 17, 'Beban: BELI ZIPPER KUNING-OREN-JOTOL MASING MASING 2 (EXP-2026-0064)', 'expense', 'normal', 'expenses', 65, NULL, NULL, 1, 1, '2026-09-10 20:45:08', '2026-09-10 20:45:08'),
(140, 'JNL/2026/09/00035', '2026-09-12', 17, 'Kas Masuk Lainnya: RCP/2026/09/00013 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 45, NULL, NULL, 1, 1, '2026-09-11 18:42:35', '2026-09-11 18:42:35'),
(141, 'JNL/2026/09/00036', '2026-09-14', 17, 'Kas Masuk Lainnya: RCP/2026/09/00015 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 47, NULL, NULL, 1, 1, '2026-09-13 20:48:48', '2026-09-13 20:48:48'),
(142, 'JNL/2026/09/00037', '2026-09-13', 17, 'Kas Masuk Lainnya: RCP/2026/09/00014 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 46, NULL, NULL, 1, 1, '2026-09-13 20:48:56', '2026-09-13 20:48:56'),
(143, 'JNL/2026/09/00038', '2026-09-12', 17, 'Beban: TRANSFER (EXP-2026-0068)', 'expense', 'void', 'expenses', 69, NULL, 144, 1, 1, '2026-09-13 20:51:34', '2026-09-13 23:17:33'),
(144, 'JNL/2026/09/00039', '2026-09-14', 17, 'Pembatalan beban EXP-2026-0068', 'manual', 'reversal', 'journals', 143, NULL, NULL, 1, 1, '2026-09-13 23:17:33', '2026-09-13 23:17:33'),
(145, 'JNL/2026/09/00040', '2026-09-12', 17, 'Beban: TRANSFER (EXP-2026-0069)', 'expense', 'normal', 'expenses', 70, NULL, NULL, 1, 1, '2026-09-13 23:18:35', '2026-09-13 23:18:35'),
(146, 'JNL/2026/09/00041', '2026-09-15', 17, 'Kas Masuk Lainnya: RCP/2026/09/00016 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 48, NULL, NULL, 1, 1, '2026-09-14 18:40:43', '2026-09-14 18:40:43'),
(147, 'JNL/2026/09/00042', '2026-09-14', 17, 'Beban: ADMIN BANK JAGO (EXP-2026-0071)', 'expense', 'normal', 'expenses', 72, NULL, NULL, 1, 1, '2026-09-14 18:42:47', '2026-09-14 18:42:47'),
(148, 'JNL/2026/09/00043', '2026-09-14', 17, 'Beban: TARIK TUNAI (EXP-2026-0070)', 'expense', 'normal', 'expenses', 71, NULL, NULL, 1, 1, '2026-09-14 18:42:55', '2026-09-14 18:42:55'),
(149, 'JNL/2026/09/00044', '2026-09-15', 17, 'Beban: BELI ZIPPER PUTIH 30 (EXP-2026-0072)', 'expense', 'normal', 'expenses', 73, NULL, NULL, 1, 1, '2026-09-15 01:32:55', '2026-09-15 01:32:55'),
(150, 'JNL/2026/09/00045', '2026-09-15', 17, 'Beban: ADMIN BANK JAGO (EXP-2026-0073)', 'expense', 'normal', 'expenses', 74, NULL, NULL, 1, 1, '2026-09-15 01:33:01', '2026-09-15 01:33:01'),
(151, 'JNL/2026/09/00046', '2026-09-16', 17, 'Kas Masuk Lainnya: RCP/2026/09/00017 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 49, NULL, NULL, 1, 1, '2026-09-16 00:18:51', '2026-09-16 00:18:51'),
(152, 'JNL/2026/09/00047', '2026-09-16', 17, 'Beban: TRANSFER (EXP-2026-0074)', 'expense', 'normal', 'expenses', 75, NULL, NULL, 1, 1, '2026-09-16 00:31:39', '2026-09-16 00:31:39'),
(153, 'JNL/2026/09/00048', '2026-09-16', 17, 'Beban: ADMIN BANK JAGO (EXP-2026-0075)', 'expense', 'normal', 'expenses', 76, NULL, NULL, 1, 1, '2026-09-16 00:31:45', '2026-09-16 00:31:45'),
(154, 'JNL/2026/09/00049', '2026-09-16', 17, 'Beban: ADMIN BANK JAGO (EXP-2026-0077)', 'expense', 'normal', 'expenses', 78, NULL, NULL, 1, 1, '2026-09-16 00:32:03', '2026-09-16 00:32:03'),
(155, 'JNL/2026/09/00050', '2026-09-16', 17, 'Beban: TRANSFER (EXP-2026-0076)', 'expense', 'normal', 'expenses', 77, NULL, NULL, 1, 1, '2026-09-16 00:32:10', '2026-09-16 00:32:10'),
(156, 'JNL/2026/09/00051', '2026-09-16', 17, 'Beban: BELI PLASTIK BENING (EXP-2026-0078)', 'expense', 'normal', 'expenses', 79, NULL, NULL, 1, 1, '2026-09-16 00:37:54', '2026-09-16 00:37:54'),
(157, 'JNL/2026/09/00052', '2026-09-16', 17, 'Beban: ADMIN BANK JAGO (EXP-2026-0079)', 'expense', 'normal', 'expenses', 80, NULL, NULL, 1, 1, '2026-09-16 00:38:04', '2026-09-16 00:38:04'),
(158, 'JNL/2026/09/00053', '2026-09-17', 17, 'Kas Masuk Lainnya: RCP/2026/09/00018 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 50, NULL, NULL, 1, 1, '2026-09-16 18:35:00', '2026-09-16 18:35:00'),
(159, 'JNL/2026/09/00054', '2026-09-18', 17, 'Kas Masuk Lainnya: RCP/2026/09/00019 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 51, NULL, NULL, 1, 1, '2026-09-17 18:34:20', '2026-09-17 18:34:20'),
(160, 'JNL/2026/09/00055', '2026-09-18', 17, 'Beban: TRANSFER (EXP-2026-0080)', 'expense', 'normal', 'expenses', 81, NULL, NULL, 1, 1, '2026-09-17 23:34:51', '2026-09-17 23:34:51'),
(161, 'JNL/2026/09/00056', '2026-09-18', 17, 'Kas Masuk Lainnya: RCP/2026/09/00022 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 54, NULL, NULL, 1, 1, '2026-09-20 18:53:22', '2026-09-20 18:53:22'),
(162, 'JNL/2026/09/00057', '2026-09-18', 17, 'Kas Masuk Lainnya: RCP/2026/09/00021 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 53, NULL, NULL, 1, 1, '2026-09-20 18:53:30', '2026-09-20 18:53:30'),
(163, 'JNL/2026/09/00058', '2026-09-18', 17, 'Kas Masuk Lainnya: RCP/2026/09/00020 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 52, NULL, NULL, 1, 1, '2026-09-20 18:53:44', '2026-09-20 18:53:44'),
(164, 'JNL/2026/09/00059', '2026-09-20', 17, 'Kas Masuk Lainnya: RCP/2026/09/00023 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 55, NULL, NULL, 1, 1, '2026-09-20 18:54:18', '2026-09-20 18:54:18'),
(165, 'JNL/2026/09/00060', '2026-09-21', 17, 'Kas Masuk Lainnya: RCP/2026/09/00024 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 56, NULL, NULL, 1, 1, '2026-09-20 18:54:39', '2026-09-20 18:54:39'),
(166, 'JNL/2026/09/00061', '2026-09-18', 17, 'Beban: TARIK TUNAI KONSUMSI (EXP-2026-0081)', 'expense', 'normal', 'expenses', 82, NULL, NULL, 1, 1, '2026-09-20 19:00:46', '2026-09-20 19:00:46'),
(167, 'JNL/2026/09/00062', '2026-09-18', 17, 'Beban: ADMIN BANK JAGO (EXP-2026-0084)', 'expense', 'normal', 'expenses', 85, NULL, NULL, 1, 1, '2026-09-20 19:00:52', '2026-09-20 19:00:52'),
(168, 'JNL/2026/09/00063', '2026-09-18', 17, 'Beban: ADMIN BANK JAGO (EXP-2026-0082)', 'expense', 'normal', 'expenses', 83, NULL, NULL, 1, 1, '2026-09-20 19:00:59', '2026-09-20 19:00:59'),
(169, 'JNL/2026/09/00064', '2026-09-18', 17, 'Beban: TARIK TUNAI + TITIPAN MB SALVA (EXP-2026-0083)', 'expense', 'normal', 'expenses', 84, NULL, NULL, 1, 1, '2026-09-20 19:01:05', '2026-09-20 19:01:05'),
(170, 'JNL/2026/09/00065', '2026-09-21', 17, 'Beban: BELI PLASTIK HITAM 15 (EXP-2026-0085)', 'expense', 'normal', 'expenses', 86, NULL, NULL, 1, 1, '2026-09-20 20:31:06', '2026-09-20 20:31:06'),
(171, 'JNL/2026/09/00066', '2026-09-21', 17, 'Beban: ADMIN BANK JAGO (EXP-2026-0086)', 'expense', 'normal', 'expenses', 87, NULL, NULL, 1, 1, '2026-09-20 20:31:13', '2026-09-20 20:31:13'),
(172, 'JNL/2026/09/00067', '2026-09-21', 17, 'Beban: BELI MINYAK OBRAS 5 (EXP-2026-0087)', 'expense', 'normal', 'expenses', 88, NULL, NULL, 1, 1, '2026-09-21 03:11:03', '2026-09-21 03:11:03'),
(173, 'JNL/2026/09/00068', '2026-09-21', 17, 'Beban: ADMIN BANK JAGO (EXP-2026-0088)', 'expense', 'normal', 'expenses', 89, NULL, NULL, 1, 1, '2026-09-21 03:11:08', '2026-09-21 03:11:08'),
(174, 'JNL/2026/09/00069', '2026-09-22', 17, 'Kas Masuk Lainnya: RCP/2026/09/00025 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 57, NULL, NULL, 1, 1, '2026-09-21 18:31:11', '2026-09-21 18:31:11'),
(175, 'JNL/2026/09/00070', '2026-09-21', 17, 'Beban: ADMIN BANK JAGO (EXP-2026-0092)', 'expense', 'normal', 'expenses', 93, NULL, NULL, 1, 1, '2026-09-21 18:36:32', '2026-09-21 18:36:32'),
(176, 'JNL/2026/09/00071', '2026-09-21', 17, 'Beban: TRANSFER (EXP-2026-0089)', 'expense', 'normal', 'expenses', 90, NULL, NULL, 1, 1, '2026-09-21 18:36:39', '2026-09-21 18:36:39'),
(177, 'JNL/2026/09/00072', '2026-09-21', 17, 'Beban: SHELY CASH (EXP-2026-0091)', 'expense', 'normal', 'expenses', 92, NULL, NULL, 1, 1, '2026-09-21 18:36:47', '2026-09-21 18:36:47'),
(178, 'JNL/2026/09/00073', '2026-09-21', 17, 'Beban: ADMIN BANK JAGO (EXP-2026-0090)', 'expense', 'normal', 'expenses', 91, NULL, NULL, 1, 1, '2026-09-21 18:36:54', '2026-09-21 18:36:54'),
(179, 'JNL/2026/09/00074', '2026-09-22', 17, 'Kas Masuk Lainnya: RCP/2026/09/00026 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 58, NULL, NULL, 1, 1, '2026-09-21 23:39:07', '2026-09-21 23:39:07'),
(180, 'JNL/2026/09/00075', '2026-09-22', 17, 'Beban: ADMIN BANK JAGO (EXP-2026-0094)', 'expense', 'normal', 'expenses', 95, NULL, NULL, 1, 1, '2026-09-22 18:07:59', '2026-09-22 18:07:59'),
(181, 'JNL/2026/09/00076', '2026-09-22', 17, 'Beban: TARIK TUNAI (EXP-2026-0093)', 'expense', 'normal', 'expenses', 94, NULL, NULL, 1, 1, '2026-09-22 18:08:06', '2026-09-22 18:08:06'),
(182, 'JNL/2026/09/00077', '2026-09-24', 17, 'Kas Masuk Lainnya: RCP/2026/09/00027 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 59, NULL, NULL, 1, 1, '2026-09-23 18:28:08', '2026-09-23 18:28:08'),
(183, 'JNL/2026/09/00078', '2026-09-25', 17, 'Kas Masuk Lainnya: RCP/2026/09/00029 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 61, NULL, NULL, 1, 1, '2026-09-24 20:35:05', '2026-09-24 20:35:05'),
(184, 'JNL/2026/09/00079', '2026-09-25', 17, 'Kas Masuk Lainnya: RCP/2026/09/00028 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 60, NULL, NULL, 1, 1, '2026-09-24 20:35:15', '2026-09-24 20:35:15'),
(185, 'JNL/2026/09/00080', '2026-09-24', 17, 'Beban: ADMIN BANK (EXP-2026-0096)', 'expense', 'normal', 'expenses', 97, NULL, NULL, 1, 1, '2026-09-24 20:38:12', '2026-09-24 20:38:12'),
(186, 'JNL/2026/09/00081', '2026-09-24', 17, 'Beban: TARIK TUNAI (EXP-2026-0095)', 'expense', 'normal', 'expenses', 96, NULL, NULL, 1, 1, '2026-09-24 20:38:20', '2026-09-24 20:38:20'),
(187, 'JNL/2026/09/00082', '2026-09-25', 17, 'Beban: TRANSFER (EXP-2026-0097)', 'expense', 'normal', 'expenses', 98, NULL, NULL, 1, 1, '2026-09-24 20:42:52', '2026-09-24 20:42:52'),
(188, 'JNL/2026/09/00083', '2026-09-25', 17, 'Beban: ADMIN BANK JAGO (EXP-2026-0098)', 'expense', 'normal', 'expenses', 99, NULL, NULL, 1, 1, '2026-09-24 20:42:59', '2026-09-24 20:42:59'),
(189, 'JNL/2026/09/00084', '2026-09-26', 17, 'Beban: TRANSFER (EXP-2026-0099)', 'expense', 'normal', 'expenses', 100, NULL, NULL, 1, 1, '2026-09-27 18:30:58', '2026-09-27 18:30:58'),
(190, 'JNL/2026/09/00085', '2026-09-26', 17, 'Beban: ADMIN BANK JAGO (EXP-2026-0100)', 'expense', 'normal', 'expenses', 101, NULL, NULL, 1, 1, '2026-09-27 18:31:13', '2026-09-27 18:31:13'),
(191, 'JNL/2026/09/00086', '2026-09-28', 17, 'Beban: TARIK TUNAI (EXP-2026-0101)', 'expense', 'normal', 'expenses', 102, NULL, NULL, 1, 1, '2026-09-27 18:31:20', '2026-09-27 18:31:20'),
(192, 'JNL/2026/09/00087', '2026-09-28', 17, 'Beban: ADMIN BANK JAGO (EXP-2026-0102)', 'expense', 'normal', 'expenses', 103, NULL, NULL, 1, 1, '2026-09-27 18:31:27', '2026-09-27 18:31:27'),
(193, 'JNL/2026/09/00088', '2026-09-28', 17, 'Kas Masuk Lainnya: RCP/2026/09/00030 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 62, NULL, NULL, 1, 1, '2026-09-27 18:32:18', '2026-09-27 18:32:18'),
(194, 'JNL/2026/09/00089', '2026-09-28', 17, 'Kas Masuk Lainnya: RCP/2026/09/00031 (Pendapatan Lain-lain)', 'other_receipt', 'normal', 'other_receipts', 63, NULL, NULL, 1, 1, '2026-09-27 23:43:49', '2026-09-27 23:43:49'),
(195, 'JNL/2026/09/00090', '2026-09-28', 17, 'Kas Masuk Lainnya: RCP/2026/09/00032 (Pendapatan Lain-lain)', 'other_receipt', 'void', 'other_receipts', 64, NULL, 196, 1, 1, '2026-09-27 23:43:57', '2026-09-27 23:55:33'),
(196, 'JNL/2026/09/00091', '2026-09-28', 17, 'Pembatalan penerimaan RCP/2026/09/00032', 'manual', 'reversal', 'journals', 195, NULL, NULL, 1, 1, '2026-09-27 23:55:33', '2026-09-27 23:55:33'),
(197, 'JNL/2026/09/00092', '2026-09-28', 17, 'Beban: PAJAK BUNGA BANK JAGO (EXP-2026-0103)', 'expense', 'normal', 'expenses', 104, NULL, NULL, 1, 1, '2026-09-27 23:56:33', '2026-09-27 23:56:33');

-- --------------------------------------------------------

--
-- Table structure for table `journal_lines`
--

CREATE TABLE `journal_lines` (
  `id` bigint UNSIGNED NOT NULL,
  `journal_id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `debit_amount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `credit_amount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `wallet_id` bigint UNSIGNED DEFAULT NULL,
  `reconciled_at` timestamp NULL DEFAULT NULL,
  `reconciled_by` bigint UNSIGNED DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `journal_lines`
--

INSERT INTO `journal_lines` (`id`, `journal_id`, `account_id`, `debit_amount`, `credit_amount`, `wallet_id`, `reconciled_at`, `reconciled_by`, `description`, `created_at`, `updated_at`) VALUES
(73, 25, 7, 1189202.0000, 0.0000, 4, NULL, NULL, 'Saldo awal Bank Umum', '2026-08-08 00:19:28', '2026-08-08 00:19:28'),
(74, 25, 81, 0.0000, 1189202.0000, NULL, NULL, NULL, 'Lawan saldo awal Bank Umum', '2026-08-08 00:19:28', '2026-08-08 00:19:28'),
(75, 26, 119, 881228.0000, 0.0000, NULL, NULL, NULL, 'lain lain', '2026-08-08 00:27:48', '2026-08-08 00:27:48'),
(76, 26, 7, 0.0000, 881228.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0001', '2026-08-08 00:27:48', '2026-08-08 00:27:48'),
(77, 27, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00001: other_income', '2026-08-08 00:28:39', '2026-08-08 00:28:39'),
(78, 27, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00001: other_income', '2026-08-08 00:28:39', '2026-08-08 00:28:39'),
(79, 28, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00002: other_income', '2026-08-08 02:06:39', '2026-08-08 02:06:39'),
(80, 28, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00002: other_income', '2026-08-08 02:06:39', '2026-08-08 02:06:39'),
(81, 29, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00003: other_income', '2026-08-08 02:08:57', '2026-08-08 02:08:57'),
(82, 29, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00003: other_income', '2026-08-08 02:08:57', '2026-08-08 02:08:57'),
(83, 30, 114, 203000.0000, 0.0000, NULL, NULL, NULL, 'Makan Siang', '2026-08-08 02:13:48', '2026-08-08 02:13:48'),
(84, 30, 7, 0.0000, 203000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0002', '2026-08-08 02:13:48', '2026-08-08 02:13:48'),
(85, 31, 119, 658000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-08-08 02:13:58', '2026-08-08 02:13:58'),
(86, 31, 7, 0.0000, 658000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0003', '2026-08-08 02:13:58', '2026-08-08 02:13:58'),
(87, 32, 119, 30000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-08-08 02:14:06', '2026-08-08 02:14:06'),
(88, 32, 7, 0.0000, 30000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0004', '2026-08-08 02:14:06', '2026-08-08 02:14:06'),
(89, 33, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00004: other_income', '2026-08-08 02:16:56', '2026-08-08 02:16:56'),
(90, 33, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00004: other_income', '2026-08-08 02:16:56', '2026-08-08 02:16:56'),
(91, 34, 119, 203000.0000, 0.0000, NULL, NULL, NULL, 'TARIK TUNAI', '2026-08-08 02:23:47', '2026-08-08 02:23:47'),
(92, 34, 7, 0.0000, 203000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0005', '2026-08-08 02:23:47', '2026-08-08 02:23:47'),
(93, 35, 119, 275000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-08-08 02:23:59', '2026-08-08 02:23:59'),
(94, 35, 7, 0.0000, 275000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0006', '2026-08-08 02:23:59', '2026-08-08 02:23:59'),
(95, 36, 119, 100000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-08-08 02:24:11', '2026-08-08 02:24:11'),
(96, 36, 7, 0.0000, 100000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0007', '2026-08-08 02:24:11', '2026-08-08 02:24:11'),
(97, 37, 119, 112000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-08-08 02:24:21', '2026-08-08 02:24:21'),
(98, 37, 7, 0.0000, 112000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0008', '2026-08-08 02:24:21', '2026-08-08 02:24:21'),
(99, 38, 119, 19950.0000, 0.0000, NULL, NULL, NULL, 'APLIKASI BELI', '2026-08-08 02:24:36', '2026-08-08 02:24:36'),
(100, 38, 7, 0.0000, 19950.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0009', '2026-08-08 02:24:36', '2026-08-08 02:24:36'),
(101, 39, 119, 16000.0000, 0.0000, NULL, NULL, NULL, 'APLIKASI BELI', '2026-08-08 02:24:47', '2026-08-08 02:24:47'),
(102, 39, 7, 0.0000, 16000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0010', '2026-08-08 02:24:47', '2026-08-08 02:24:47'),
(103, 40, 119, 114000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-08-08 02:24:58', '2026-08-08 02:24:58'),
(104, 40, 7, 0.0000, 114000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0011', '2026-08-08 02:24:58', '2026-08-08 02:24:58'),
(105, 41, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00005: other_income', '2026-08-08 02:30:59', '2026-08-08 02:30:59'),
(106, 41, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00005: other_income', '2026-08-08 02:30:59', '2026-08-08 02:30:59'),
(107, 42, 114, 190000.0000, 0.0000, NULL, NULL, NULL, 'MAKAN MALAM', '2026-08-08 02:32:53', '2026-08-08 02:32:53'),
(108, 42, 7, 0.0000, 190000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0012', '2026-08-08 02:32:53', '2026-08-08 02:32:53'),
(109, 43, 7, 100000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00007: other_income', '2026-08-08 02:35:18', '2026-08-08 02:35:18'),
(110, 43, 92, 0.0000, 100000.0000, NULL, NULL, NULL, 'RCP/2026/08/00007: MB SALVA (TUKAR UANG)', '2026-08-08 02:35:18', '2026-08-08 02:35:18'),
(111, 44, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00006: other_income', '2026-08-08 02:35:27', '2026-08-08 02:35:27'),
(112, 44, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00006: other_income', '2026-08-08 02:35:27', '2026-08-08 02:35:27'),
(113, 45, 119, 203000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-08-08 02:38:48', '2026-08-08 02:38:48'),
(114, 45, 7, 0.0000, 203000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0014', '2026-08-08 02:38:48', '2026-08-08 02:38:48'),
(115, 46, 119, 100000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-08-08 02:39:04', '2026-08-08 02:39:04'),
(116, 46, 7, 0.0000, 100000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0013', '2026-08-08 02:39:04', '2026-08-08 02:39:04'),
(117, 47, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00008: other_income', '2026-08-08 02:40:47', '2026-08-08 02:40:47'),
(118, 47, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00008: other_income', '2026-08-08 02:40:47', '2026-08-08 02:40:47'),
(119, 48, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00009: other_income', '2026-08-10 03:06:47', '2026-08-10 03:06:47'),
(120, 48, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00009: other_income', '2026-08-10 03:06:47', '2026-08-10 03:06:47'),
(121, 49, 7, 50000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00010: other_income', '2026-08-10 03:06:52', '2026-08-10 03:06:52'),
(122, 49, 92, 0.0000, 50000.0000, NULL, NULL, NULL, 'RCP/2026/08/00010: other_income', '2026-08-10 03:06:52', '2026-08-10 03:06:52'),
(123, 50, 7, 600000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00011: other_income', '2026-08-10 03:06:58', '2026-08-10 03:06:58'),
(124, 50, 92, 0.0000, 600000.0000, NULL, NULL, NULL, 'RCP/2026/08/00011: other_income', '2026-08-10 03:06:58', '2026-08-10 03:06:58'),
(125, 51, 119, 1509200.0000, 0.0000, NULL, NULL, NULL, 'BELANJA PERLENGKAPAN/BAHAN', '2026-08-10 03:16:07', '2026-08-10 03:16:07'),
(126, 51, 7, 0.0000, 1509200.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0018', '2026-08-10 03:16:07', '2026-08-10 03:16:07'),
(127, 52, 119, 250000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-08-10 03:16:16', '2026-08-10 03:16:16'),
(128, 52, 7, 0.0000, 250000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0017', '2026-08-10 03:16:16', '2026-08-10 03:16:16'),
(129, 53, 119, 50000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-08-10 03:16:25', '2026-08-10 03:16:25'),
(130, 53, 7, 0.0000, 50000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0016', '2026-08-10 03:16:25', '2026-08-10 03:16:25'),
(131, 54, 119, 1718920.0000, 0.0000, NULL, NULL, NULL, 'BELANJA PERLENGKAPAN/BAHAN', '2026-08-10 03:16:35', '2026-08-10 03:16:35'),
(132, 54, 7, 0.0000, 1718920.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0019', '2026-08-10 03:16:35', '2026-08-10 03:16:35'),
(133, 55, 119, 403000.0000, 0.0000, NULL, NULL, NULL, 'TARIK TUNAI', '2026-08-10 03:16:48', '2026-08-10 03:16:48'),
(134, 55, 7, 0.0000, 403000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0015', '2026-08-10 03:16:48', '2026-08-10 03:16:48'),
(135, 56, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00012: other_income', '2026-08-10 03:18:35', '2026-08-10 03:18:35'),
(136, 56, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00012: other_income', '2026-08-10 03:18:35', '2026-08-10 03:18:35'),
(137, 57, 119, 10000.0000, 0.0000, NULL, NULL, NULL, 'TUKAR SALDO CASH', '2026-08-10 03:19:45', '2026-08-10 03:19:45'),
(138, 57, 7, 0.0000, 10000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0020', '2026-08-10 03:19:45', '2026-08-10 03:19:45'),
(139, 58, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00013: other_income', '2026-08-10 19:26:47', '2026-08-10 19:26:47'),
(140, 58, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00013: other_income', '2026-08-10 19:26:47', '2026-08-10 19:26:47'),
(141, 59, 119, 215000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-08-11 03:00:00', '2026-08-11 03:00:00'),
(142, 59, 7, 0.0000, 215000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0023', '2026-08-11 03:00:00', '2026-08-11 03:00:00'),
(143, 60, 119, 260000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-08-11 03:00:10', '2026-08-11 03:00:10'),
(144, 60, 7, 0.0000, 260000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0021', '2026-08-11 03:00:10', '2026-08-11 03:00:10'),
(145, 61, 119, 600000.0000, 0.0000, NULL, NULL, NULL, 'BELANJA PERLENGKAPAN/BAHAN', '2026-08-11 03:00:20', '2026-08-11 03:00:20'),
(146, 61, 7, 0.0000, 600000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0022', '2026-08-11 03:00:20', '2026-08-11 03:00:20'),
(147, 62, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00014: other_income', '2026-08-12 03:10:17', '2026-08-12 03:10:17'),
(148, 62, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00014: other_income', '2026-08-12 03:10:17', '2026-08-12 03:10:17'),
(149, 63, 114, 303000.0000, 0.0000, NULL, NULL, NULL, 'MAKAN SIANG', '2026-08-12 03:12:09', '2026-08-12 03:12:09'),
(150, 63, 7, 0.0000, 303000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0024', '2026-08-12 03:12:09', '2026-08-12 03:12:09'),
(151, 64, 119, 361770.0000, 0.0000, NULL, NULL, NULL, 'BELANJA', '2026-08-12 03:12:16', '2026-08-12 03:12:16'),
(152, 64, 7, 0.0000, 361770.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0025', '2026-08-12 03:12:16', '2026-08-12 03:12:16'),
(153, 65, 7, 20000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00016: other_income', '2026-08-12 03:13:59', '2026-08-12 03:13:59'),
(154, 65, 92, 0.0000, 20000.0000, NULL, NULL, NULL, 'RCP/2026/08/00016: TARIK TUNAI SEKALIAN', '2026-08-12 03:13:59', '2026-08-12 03:13:59'),
(155, 66, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00015: other_income', '2026-08-12 03:14:07', '2026-08-12 03:14:07'),
(156, 66, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00015: other_income', '2026-08-12 03:14:07', '2026-08-12 03:14:07'),
(157, 67, 119, 300000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-08-12 03:15:46', '2026-08-12 03:15:46'),
(158, 67, 7, 0.0000, 300000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0027', '2026-08-12 03:15:46', '2026-08-12 03:15:46'),
(159, 68, 119, 323000.0000, 0.0000, NULL, NULL, NULL, 'TARIK TUNAI', '2026-08-12 03:15:55', '2026-08-12 03:15:55'),
(160, 68, 7, 0.0000, 323000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0026', '2026-08-12 03:15:55', '2026-08-12 03:15:55'),
(161, 69, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00017: other_income', '2026-08-13 02:50:13', '2026-08-13 02:50:13'),
(162, 69, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00017: other_income', '2026-08-13 02:50:13', '2026-08-13 02:50:13'),
(163, 70, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00018: other_income', '2026-08-14 01:47:36', '2026-08-14 01:47:36'),
(164, 70, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00018: other_income', '2026-08-14 01:47:36', '2026-08-14 01:47:36'),
(165, 71, 119, 893000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER BAYAR SEABANK', '2026-08-14 01:48:58', '2026-08-14 01:48:58'),
(166, 71, 7, 0.0000, 893000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0028', '2026-08-14 01:48:58', '2026-08-14 01:48:58'),
(167, 72, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00019: other_income', '2026-08-15 01:06:42', '2026-08-15 01:06:42'),
(168, 72, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00019: other_income', '2026-08-15 01:06:42', '2026-08-15 01:06:42'),
(169, 73, 119, 100000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-08-15 01:07:35', '2026-08-15 01:07:35'),
(170, 73, 7, 0.0000, 100000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0029', '2026-08-15 01:07:35', '2026-08-15 01:07:35'),
(171, 74, 119, 153000.0000, 0.0000, NULL, NULL, NULL, 'KONSUMSI', '2026-08-15 01:11:21', '2026-08-15 01:11:21'),
(172, 74, 7, 0.0000, 153000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0030', '2026-08-15 01:11:21', '2026-08-15 01:11:21'),
(173, 75, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00020: other_income', '2026-08-16 23:49:48', '2026-08-16 23:49:48'),
(174, 75, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00020: other_income', '2026-08-16 23:49:48', '2026-08-16 23:49:48'),
(175, 76, 119, 594540.0000, 0.0000, NULL, NULL, NULL, 'PEMBAYARAN VIRTUAL ACCOUNT', '2026-08-16 23:52:05', '2026-08-16 23:52:05'),
(176, 76, 7, 0.0000, 594540.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0031', '2026-08-16 23:52:05', '2026-08-16 23:52:05'),
(177, 77, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00021: other_income', '2026-08-16 23:54:10', '2026-08-16 23:54:10'),
(178, 77, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00021: other_income', '2026-08-16 23:54:10', '2026-08-16 23:54:10'),
(179, 78, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00022: other_income', '2026-08-17 21:09:52', '2026-08-17 21:09:52'),
(180, 78, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00022: other_income', '2026-08-17 21:09:52', '2026-08-17 21:09:52'),
(181, 79, 119, 2509593.0000, 0.0000, NULL, NULL, NULL, 'BELANJA KAIN', '2026-08-18 01:30:00', '2026-08-18 01:30:00'),
(182, 79, 7, 0.0000, 2509593.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0032', '2026-08-18 01:30:00', '2026-08-18 01:30:00'),
(183, 80, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00023: other_income', '2026-08-19 20:37:09', '2026-08-19 20:37:09'),
(184, 80, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00023: other_income', '2026-08-19 20:37:09', '2026-08-19 20:37:09'),
(185, 81, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00024: other_income', '2026-08-23 18:21:54', '2026-08-23 18:21:54'),
(186, 81, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00024: other_income', '2026-08-23 18:21:54', '2026-08-23 18:21:54'),
(187, 82, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00025: other_income', '2026-08-23 18:22:00', '2026-08-23 18:22:00'),
(188, 82, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00025: other_income', '2026-08-23 18:22:00', '2026-08-23 18:22:00'),
(189, 83, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00026: other_income', '2026-08-23 18:22:08', '2026-08-23 18:22:08'),
(190, 83, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00026: other_income', '2026-08-23 18:22:08', '2026-08-23 18:22:08'),
(191, 84, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00027: other_income', '2026-08-24 03:18:38', '2026-08-24 03:18:38'),
(192, 84, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00027: other_income', '2026-08-24 03:18:38', '2026-08-24 03:18:38'),
(193, 85, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00028: other_income', '2026-08-26 00:41:45', '2026-08-26 00:41:45'),
(194, 85, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00028: other_income', '2026-08-26 00:41:45', '2026-08-26 00:41:45'),
(195, 86, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00029: other_income', '2026-08-26 23:13:30', '2026-08-26 23:13:30'),
(196, 86, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/08/00029: other_income', '2026-08-26 23:13:30', '2026-08-26 23:13:30'),
(197, 87, 119, 200000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER SEABANK', '2026-08-26 23:41:12', '2026-08-26 23:41:12'),
(198, 87, 7, 0.0000, 200000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0034', '2026-08-26 23:41:12', '2026-08-26 23:41:12'),
(199, 88, 119, 2000000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER JAGO', '2026-08-26 23:41:22', '2026-08-26 23:41:22'),
(200, 88, 7, 0.0000, 2000000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0036', '2026-08-26 23:41:22', '2026-08-26 23:41:22'),
(201, 89, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN TRANSFER BANK JAGO-SEABANK', '2026-08-26 23:41:29', '2026-08-26 23:41:29'),
(202, 89, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0035', '2026-08-26 23:41:29', '2026-08-26 23:41:29'),
(203, 90, 119, 500000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER JAGO', '2026-08-26 23:41:36', '2026-08-26 23:41:36'),
(204, 90, 7, 0.0000, 500000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0033', '2026-08-26 23:41:36', '2026-08-26 23:41:36'),
(205, 91, 119, 17750.0000, 0.0000, NULL, NULL, NULL, 'BELI PULPEN 12PCS', '2026-08-27 18:42:42', '2026-08-27 18:42:42'),
(206, 91, 7, 0.0000, 17750.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0039', '2026-08-27 18:42:43', '2026-08-27 18:42:43'),
(207, 92, 119, 2500.0000, 0.0000, NULL, NULL, NULL, 'BIAYA ADMIN TRANSFER BANK JAGO-SEABANK', '2026-08-27 18:42:51', '2026-08-27 18:42:51'),
(208, 92, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0040', '2026-08-27 18:42:51', '2026-08-27 18:42:51'),
(209, 93, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'BIAYA TRANSFER BANK JAGO-SEABANK', '2026-08-27 18:42:59', '2026-08-27 18:42:59'),
(210, 93, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0038', '2026-08-27 18:42:59', '2026-08-27 18:42:59'),
(211, 94, 119, 846550.0000, 0.0000, NULL, NULL, NULL, 'BELANJA PLASTIK BENING 4 BENDEL', '2026-08-27 18:43:09', '2026-08-27 18:43:09'),
(212, 94, 7, 0.0000, 846550.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0037', '2026-08-27 18:43:09', '2026-08-27 18:43:09'),
(213, 95, 7, 552.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00030: other_income', '2026-08-27 23:39:35', '2026-08-27 23:39:35'),
(214, 95, 92, 0.0000, 552.0000, NULL, NULL, NULL, 'RCP/2026/08/00030: BUNGA MASUK DARI BANK JAGO', '2026-08-27 23:39:35', '2026-08-27 23:39:35'),
(215, 96, 119, 61125.0000, 0.0000, NULL, NULL, NULL, 'BELI STIKER SIZE', '2026-08-27 23:41:45', '2026-08-27 23:41:45'),
(216, 96, 7, 0.0000, 61125.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0042', '2026-08-27 23:41:45', '2026-08-27 23:41:45'),
(217, 97, 119, 110.0000, 0.0000, NULL, NULL, NULL, 'PAJAK BUNGA BANK JAGO', '2026-08-27 23:41:54', '2026-08-27 23:41:54'),
(218, 97, 7, 0.0000, 110.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0041', '2026-08-27 23:41:54', '2026-08-27 23:41:54'),
(219, 98, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'BIAYA ADMIN TRANSFER BANK JAGO-SEABANK', '2026-08-27 23:42:00', '2026-08-27 23:42:00'),
(220, 98, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0043', '2026-08-27 23:42:00', '2026-08-27 23:42:00'),
(221, 99, 119, 915000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-08-28 02:00:36', '2026-08-28 02:00:36'),
(222, 99, 7, 0.0000, 915000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0044', '2026-08-28 02:00:36', '2026-08-28 02:00:36'),
(223, 100, 7, 738607.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00031: other_income', '2026-08-28 02:45:46', '2026-08-28 02:45:46'),
(224, 100, 92, 0.0000, 738607.0000, NULL, NULL, NULL, 'RCP/2026/08/00031: TARIK TIKTOK', '2026-08-28 02:45:46', '2026-08-28 02:45:46'),
(225, 101, 119, 738625.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-08-28 02:46:58', '2026-08-28 02:46:58'),
(226, 101, 7, 0.0000, 738625.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0045', '2026-08-28 02:46:58', '2026-08-28 02:46:58'),
(227, 102, 7, 200000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/08/00032: other_income', '2026-08-29 00:07:30', '2026-08-29 00:07:30'),
(228, 102, 92, 0.0000, 200000.0000, NULL, NULL, NULL, 'RCP/2026/08/00032: other_income', '2026-08-29 00:07:30', '2026-08-29 00:07:30'),
(229, 103, 114, 153000.0000, 0.0000, NULL, NULL, NULL, 'MAKAN SIANG', '2026-08-29 00:08:59', '2026-08-29 00:08:59'),
(230, 103, 7, 0.0000, 153000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0046', '2026-08-29 00:08:59', '2026-08-29 00:08:59'),
(231, 104, 114, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN BANK TRANSFER', '2026-08-29 00:09:06', '2026-08-29 00:09:06'),
(232, 104, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0047', '2026-08-29 00:09:06', '2026-08-29 00:09:06'),
(233, 105, 92, 17959159.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(234, 105, 84, 0.0000, 17959159.0000, NULL, NULL, NULL, NULL, '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(235, 105, 84, 7500.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(236, 105, 113, 0.0000, 7500.0000, NULL, NULL, NULL, NULL, '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(237, 105, 84, 851500.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(238, 105, 114, 0.0000, 851500.0000, NULL, NULL, NULL, NULL, '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(239, 105, 84, 18244861.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(240, 105, 119, 0.0000, 18244861.0000, NULL, NULL, NULL, NULL, '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(241, 105, 83, 1144702.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(242, 105, 84, 0.0000, 1144702.0000, NULL, NULL, NULL, NULL, '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(243, 106, 7, 44500.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(244, 106, 81, 0.0000, 1189202.0000, NULL, NULL, NULL, NULL, '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(245, 106, 83, 1144702.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(246, 107, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00001: other_income', '2026-09-02 19:10:24', '2026-09-02 19:10:24'),
(247, 107, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00001: other_income', '2026-09-02 19:10:24', '2026-09-02 19:10:24'),
(248, 108, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00002: other_income', '2026-09-02 19:10:31', '2026-09-02 19:10:31'),
(249, 108, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00002: other_income', '2026-09-02 19:10:31', '2026-09-02 19:10:31'),
(250, 109, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00003: other_income', '2026-09-02 19:10:39', '2026-09-02 19:10:39'),
(251, 109, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00003: other_income', '2026-09-02 19:10:39', '2026-09-02 19:10:39'),
(252, 110, 119, 146000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-09-02 19:13:01', '2026-09-02 19:13:01'),
(253, 110, 7, 0.0000, 146000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0048', '2026-09-02 19:13:01', '2026-09-02 19:13:01'),
(254, 111, 119, 397970.0000, 0.0000, NULL, NULL, NULL, 'BELI PLASTIK HITAM PACKING 12PACK', '2026-09-02 19:13:09', '2026-09-02 19:13:09'),
(255, 111, 7, 0.0000, 397970.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0049', '2026-09-02 19:13:09', '2026-09-02 19:13:09'),
(256, 112, 7, 950000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00004: other_income', '2026-09-04 02:48:19', '2026-09-04 02:48:19'),
(257, 112, 92, 0.0000, 950000.0000, NULL, NULL, NULL, 'RCP/2026/09/00004: other_income', '2026-09-04 02:48:19', '2026-09-04 02:48:19'),
(258, 113, 119, 477190.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-09-04 02:50:00', '2026-09-04 02:50:00'),
(259, 113, 7, 0.0000, 477190.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0050', '2026-09-04 02:50:00', '2026-09-04 02:50:00'),
(260, 114, 119, 100000.0000, 0.0000, NULL, NULL, NULL, 'MAKAN SIANG', '2026-09-04 02:50:10', '2026-09-04 02:50:10'),
(261, 114, 7, 0.0000, 100000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0051', '2026-09-04 02:50:10', '2026-09-04 02:50:10'),
(262, 115, 7, 0.0000, 950000.0000, 4, NULL, NULL, 'RCP/2026/09/00004: other_income', '2026-09-04 02:53:24', '2026-09-04 02:53:24'),
(263, 115, 92, 950000.0000, 0.0000, NULL, NULL, NULL, 'RCP/2026/09/00004: other_income', '2026-09-04 02:53:24', '2026-09-04 02:53:24'),
(264, 116, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00005: other_income', '2026-09-04 02:54:14', '2026-09-04 02:54:14'),
(265, 116, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00005: other_income', '2026-09-04 02:54:14', '2026-09-04 02:54:14'),
(266, 117, 119, 0.0000, 397970.0000, NULL, NULL, NULL, 'BELI PLASTIK HITAM PACKING 12PACK', '2026-09-04 02:55:47', '2026-09-04 02:55:47'),
(267, 117, 7, 397970.0000, 0.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0049', '2026-09-04 02:55:47', '2026-09-04 02:55:47'),
(268, 118, 119, 397972.0000, 0.0000, NULL, NULL, NULL, 'BELI PLASTIK HITAM', '2026-09-04 02:56:53', '2026-09-04 02:56:53'),
(269, 118, 7, 0.0000, 397972.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0052', '2026-09-04 02:56:53', '2026-09-04 02:56:53'),
(270, 119, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00006: other_income', '2026-09-05 02:07:42', '2026-09-05 02:07:42'),
(271, 119, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00006: other_income', '2026-09-05 02:07:42', '2026-09-05 02:07:42'),
(272, 120, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00007: other_income', '2026-09-06 19:20:20', '2026-09-06 19:20:20'),
(273, 120, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00007: other_income', '2026-09-06 19:20:20', '2026-09-06 19:20:20'),
(274, 121, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00008: other_income', '2026-09-06 19:20:28', '2026-09-06 19:20:28'),
(275, 121, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00008: other_income', '2026-09-06 19:20:28', '2026-09-06 19:20:28'),
(276, 122, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN TF BANK JAGO-BRI', '2026-09-06 19:22:45', '2026-09-06 19:22:45'),
(277, 122, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0054', '2026-09-06 19:22:45', '2026-09-06 19:22:45'),
(278, 123, 114, 203000.0000, 0.0000, NULL, NULL, NULL, 'MAKAN SIANG', '2026-09-06 19:22:56', '2026-09-06 19:22:56'),
(279, 123, 7, 0.0000, 203000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0053', '2026-09-06 19:22:56', '2026-09-06 19:22:56'),
(280, 124, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00009: other_income', '2026-09-07 23:49:20', '2026-09-07 23:49:20'),
(281, 124, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00009: other_income', '2026-09-07 23:49:20', '2026-09-07 23:49:20'),
(282, 125, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00010: other_income', '2026-09-08 20:31:05', '2026-09-08 20:31:05'),
(283, 125, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00010: other_income', '2026-09-08 20:31:05', '2026-09-08 20:31:05'),
(284, 126, 119, 400000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-09-08 20:36:31', '2026-09-08 20:36:31'),
(285, 126, 7, 0.0000, 400000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0055', '2026-09-08 20:36:31', '2026-09-08 20:36:31'),
(286, 127, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN TRANSFER BANK JAGO', '2026-09-08 20:36:39', '2026-09-08 20:36:39'),
(287, 127, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0059', '2026-09-08 20:36:39', '2026-09-08 20:36:39'),
(288, 128, 119, 2058370.0000, 0.0000, NULL, NULL, NULL, 'BELANJA BAHAN', '2026-09-08 20:36:50', '2026-09-08 20:36:50'),
(289, 128, 7, 0.0000, 2058370.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0056', '2026-09-08 20:36:50', '2026-09-08 20:36:50'),
(290, 129, 119, 1670497.0000, 0.0000, NULL, NULL, NULL, 'BELANJA BAHAN', '2026-09-08 20:37:00', '2026-09-08 20:37:00'),
(291, 129, 7, 0.0000, 1670497.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0058', '2026-09-08 20:37:00', '2026-09-08 20:37:00'),
(292, 130, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'BIAYA TRANSFER BANK JAGO', '2026-09-08 20:37:08', '2026-09-08 20:37:08'),
(293, 130, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0057', '2026-09-08 20:37:08', '2026-09-08 20:37:08'),
(294, 131, 119, 203000.0000, 0.0000, NULL, NULL, NULL, 'TARIK TUNAI-KOPI', '2026-09-09 02:38:13', '2026-09-09 02:38:13'),
(295, 131, 7, 0.0000, 203000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0060', '2026-09-09 02:38:13', '2026-09-09 02:38:13'),
(296, 132, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN TRANSFER BANK JAGO', '2026-09-09 02:38:20', '2026-09-09 02:38:20'),
(297, 132, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0061', '2026-09-09 02:38:20', '2026-09-09 02:38:20'),
(298, 133, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00011: other_income', '2026-09-09 18:51:26', '2026-09-09 18:51:26'),
(299, 133, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00011: other_income', '2026-09-09 18:51:26', '2026-09-09 18:51:26'),
(300, 134, 119, 44500.0000, 0.0000, NULL, NULL, NULL, 'carry forward dari Agustus 2026', '2026-09-09 18:54:59', '2026-09-09 18:54:59'),
(301, 134, 7, 0.0000, 44500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0063', '2026-09-09 18:54:59', '2026-09-09 18:54:59'),
(302, 135, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00012: other_income', '2026-09-10 20:00:50', '2026-09-10 20:00:50'),
(303, 135, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00012: other_income', '2026-09-10 20:00:50', '2026-09-10 20:00:50'),
(304, 136, 119, 172200.0000, 0.0000, NULL, NULL, NULL, 'BELI MATA AYAM 3PACK', '2026-09-10 20:44:40', '2026-09-10 20:44:40'),
(305, 136, 7, 0.0000, 172200.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0066', '2026-09-10 20:44:40', '2026-09-10 20:44:40'),
(306, 137, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN TRANSFER BANK JAGO', '2026-09-10 20:44:53', '2026-09-10 20:44:53'),
(307, 137, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0067', '2026-09-10 20:44:53', '2026-09-10 20:44:53'),
(308, 138, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN TRANSFER BANK JAGO', '2026-09-10 20:44:58', '2026-09-10 20:44:58'),
(309, 138, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0065', '2026-09-10 20:44:58', '2026-09-10 20:44:58'),
(310, 139, 119, 320700.0000, 0.0000, NULL, NULL, NULL, 'BELI ZIPPER KUNING-OREN-JOTOL MASING MASING 2', '2026-09-10 20:45:08', '2026-09-10 20:45:08'),
(311, 139, 7, 0.0000, 320700.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0064', '2026-09-10 20:45:08', '2026-09-10 20:45:08'),
(312, 140, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00013: other_income', '2026-09-11 18:42:35', '2026-09-11 18:42:35'),
(313, 140, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00013: other_income', '2026-09-11 18:42:35', '2026-09-11 18:42:35'),
(314, 141, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00015: other_income', '2026-09-13 20:48:48', '2026-09-13 20:48:48'),
(315, 141, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00015: other_income', '2026-09-13 20:48:48', '2026-09-13 20:48:48'),
(316, 142, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00014: other_income', '2026-09-13 20:48:56', '2026-09-13 20:48:56'),
(317, 142, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00014: other_income', '2026-09-13 20:48:56', '2026-09-13 20:48:56'),
(318, 143, 119, 200000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-09-13 20:51:34', '2026-09-13 20:51:34'),
(319, 143, 7, 0.0000, 200000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0068', '2026-09-13 20:51:34', '2026-09-13 20:51:34'),
(320, 144, 119, 0.0000, 200000.0000, NULL, NULL, NULL, 'TRANSFER', '2026-09-13 23:17:33', '2026-09-13 23:17:33'),
(321, 144, 7, 200000.0000, 0.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0068', '2026-09-13 23:17:33', '2026-09-13 23:17:33'),
(322, 145, 119, 250000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-09-13 23:18:35', '2026-09-13 23:18:35'),
(323, 145, 7, 0.0000, 250000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0069', '2026-09-13 23:18:35', '2026-09-13 23:18:35'),
(324, 146, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00016: other_income', '2026-09-14 18:40:43', '2026-09-14 18:40:43'),
(325, 146, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00016: other_income', '2026-09-14 18:40:43', '2026-09-14 18:40:43'),
(326, 147, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN BANK JAGO', '2026-09-14 18:42:47', '2026-09-14 18:42:47'),
(327, 147, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0071', '2026-09-14 18:42:47', '2026-09-14 18:42:47'),
(328, 148, 119, 103000.0000, 0.0000, NULL, NULL, NULL, 'TARIK TUNAI', '2026-09-14 18:42:55', '2026-09-14 18:42:55'),
(329, 148, 7, 0.0000, 103000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0070', '2026-09-14 18:42:55', '2026-09-14 18:42:55'),
(330, 149, 119, 688950.0000, 0.0000, NULL, NULL, NULL, 'BELI ZIPPER PUTIH 30', '2026-09-15 01:32:55', '2026-09-15 01:32:55'),
(331, 149, 7, 0.0000, 688950.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0072', '2026-09-15 01:32:55', '2026-09-15 01:32:55'),
(332, 150, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN BANK JAGO', '2026-09-15 01:33:01', '2026-09-15 01:33:01'),
(333, 150, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0073', '2026-09-15 01:33:01', '2026-09-15 01:33:01'),
(334, 151, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00017: other_income', '2026-09-16 00:18:51', '2026-09-16 00:18:51'),
(335, 151, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00017: other_income', '2026-09-16 00:18:51', '2026-09-16 00:18:51'),
(336, 152, 119, 1000000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-09-16 00:31:39', '2026-09-16 00:31:39'),
(337, 152, 7, 0.0000, 1000000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0074', '2026-09-16 00:31:39', '2026-09-16 00:31:39'),
(338, 153, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN BANK JAGO', '2026-09-16 00:31:45', '2026-09-16 00:31:45'),
(339, 153, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0075', '2026-09-16 00:31:45', '2026-09-16 00:31:45'),
(340, 154, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN BANK JAGO', '2026-09-16 00:32:03', '2026-09-16 00:32:03'),
(341, 154, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0077', '2026-09-16 00:32:03', '2026-09-16 00:32:03'),
(342, 155, 119, 1000000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-09-16 00:32:10', '2026-09-16 00:32:10'),
(343, 155, 7, 0.0000, 1000000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0076', '2026-09-16 00:32:10', '2026-09-16 00:32:10'),
(344, 156, 119, 415800.0000, 0.0000, NULL, NULL, NULL, 'BELI PLASTIK BENING', '2026-09-16 00:37:54', '2026-09-16 00:37:54'),
(345, 156, 7, 0.0000, 415800.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0078', '2026-09-16 00:37:54', '2026-09-16 00:37:54'),
(346, 157, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN BANK JAGO', '2026-09-16 00:38:04', '2026-09-16 00:38:04'),
(347, 157, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0079', '2026-09-16 00:38:04', '2026-09-16 00:38:04'),
(348, 158, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00018: other_income', '2026-09-16 18:35:00', '2026-09-16 18:35:00'),
(349, 158, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00018: other_income', '2026-09-16 18:35:00', '2026-09-16 18:35:00'),
(350, 159, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00019: other_income', '2026-09-17 18:34:20', '2026-09-17 18:34:20'),
(351, 159, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00019: other_income', '2026-09-17 18:34:20', '2026-09-17 18:34:20'),
(352, 160, 119, 2110321.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-09-17 23:34:52', '2026-09-17 23:34:52'),
(353, 160, 7, 0.0000, 2110321.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0080', '2026-09-17 23:34:52', '2026-09-17 23:34:52'),
(354, 161, 7, 170000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00022: other_income', '2026-09-20 18:53:22', '2026-09-20 18:53:22'),
(355, 161, 92, 0.0000, 170000.0000, NULL, NULL, NULL, 'RCP/2026/09/00022: TITIPAN MB SALVA', '2026-09-20 18:53:22', '2026-09-20 18:53:22'),
(356, 162, 7, 200000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00021: other_income', '2026-09-20 18:53:30', '2026-09-20 18:53:30'),
(357, 162, 92, 0.0000, 200000.0000, NULL, NULL, NULL, 'RCP/2026/09/00021: BELANJA BERAS DLL', '2026-09-20 18:53:30', '2026-09-20 18:53:30'),
(358, 163, 7, 500000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00020: other_income', '2026-09-20 18:53:44', '2026-09-20 18:53:44'),
(359, 163, 92, 0.0000, 500000.0000, NULL, NULL, NULL, 'RCP/2026/09/00020: BELANJA FROZEN FOOD', '2026-09-20 18:53:44', '2026-09-20 18:53:44'),
(360, 164, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00023: other_income', '2026-09-20 18:54:18', '2026-09-20 18:54:18'),
(361, 164, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00023: other_income', '2026-09-20 18:54:18', '2026-09-20 18:54:18'),
(362, 165, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00024: other_income', '2026-09-20 18:54:39', '2026-09-20 18:54:39'),
(363, 165, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00024: other_income', '2026-09-20 18:54:39', '2026-09-20 18:54:39'),
(364, 166, 114, 497000.0000, 0.0000, NULL, NULL, NULL, 'TARIK TUNAI KONSUMSI', '2026-09-20 19:00:46', '2026-09-20 19:00:46'),
(365, 166, 7, 0.0000, 497000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0081', '2026-09-20 19:00:46', '2026-09-20 19:00:46'),
(366, 167, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN BANK JAGO', '2026-09-20 19:00:52', '2026-09-20 19:00:52'),
(367, 167, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0084', '2026-09-20 19:00:52', '2026-09-20 19:00:52'),
(368, 168, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN BANK JAGO', '2026-09-20 19:00:59', '2026-09-20 19:00:59'),
(369, 168, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0082', '2026-09-20 19:00:59', '2026-09-20 19:00:59'),
(370, 169, 119, 367000.0000, 0.0000, NULL, NULL, NULL, 'TARIK TUNAI + TITIPAN MB SALVA', '2026-09-20 19:01:05', '2026-09-20 19:01:05'),
(371, 169, 7, 0.0000, 367000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0083', '2026-09-20 19:01:05', '2026-09-20 19:01:05'),
(372, 170, 119, 473074.0000, 0.0000, NULL, NULL, NULL, 'BELI PLASTIK HITAM 15', '2026-09-20 20:31:06', '2026-09-20 20:31:06'),
(373, 170, 7, 0.0000, 473074.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0085', '2026-09-20 20:31:06', '2026-09-20 20:31:06'),
(374, 171, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN BANK JAGO', '2026-09-20 20:31:13', '2026-09-20 20:31:13'),
(375, 171, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0086', '2026-09-20 20:31:13', '2026-09-20 20:31:13'),
(376, 172, 119, 262000.0000, 0.0000, NULL, NULL, NULL, 'BELI MINYAK OBRAS 5', '2026-09-21 03:11:03', '2026-09-21 03:11:03'),
(377, 172, 7, 0.0000, 262000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0087', '2026-09-21 03:11:03', '2026-09-21 03:11:03'),
(378, 173, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN BANK JAGO', '2026-09-21 03:11:08', '2026-09-21 03:11:08'),
(379, 173, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0088', '2026-09-21 03:11:08', '2026-09-21 03:11:08'),
(380, 174, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00025: other_income', '2026-09-21 18:31:11', '2026-09-21 18:31:11'),
(381, 174, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00025: other_income', '2026-09-21 18:31:11', '2026-09-21 18:31:11'),
(382, 175, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN BANK JAGO', '2026-09-21 18:36:32', '2026-09-21 18:36:32'),
(383, 175, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0092', '2026-09-21 18:36:32', '2026-09-21 18:36:32'),
(384, 176, 119, 400000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-09-21 18:36:39', '2026-09-21 18:36:39'),
(385, 176, 7, 0.0000, 400000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0089', '2026-09-21 18:36:39', '2026-09-21 18:36:39'),
(386, 177, 119, 18000.0000, 0.0000, NULL, NULL, NULL, 'SHELY CASH', '2026-09-21 18:36:47', '2026-09-21 18:36:47'),
(387, 177, 7, 0.0000, 18000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0091', '2026-09-21 18:36:47', '2026-09-21 18:36:47'),
(388, 178, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN BANK JAGO', '2026-09-21 18:36:54', '2026-09-21 18:36:54'),
(389, 178, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0090', '2026-09-21 18:36:54', '2026-09-21 18:36:54'),
(390, 179, 7, 10000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00026: other_income', '2026-09-21 23:39:08', '2026-09-21 23:39:08'),
(391, 179, 92, 0.0000, 10000.0000, NULL, NULL, NULL, 'RCP/2026/09/00026: MB SALVA CASH', '2026-09-21 23:39:08', '2026-09-21 23:39:08'),
(392, 180, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN BANK JAGO', '2026-09-22 18:07:59', '2026-09-22 18:07:59'),
(393, 180, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0094', '2026-09-22 18:07:59', '2026-09-22 18:07:59'),
(394, 181, 119, 203000.0000, 0.0000, NULL, NULL, NULL, 'TARIK TUNAI', '2026-09-22 18:08:06', '2026-09-22 18:08:06'),
(395, 181, 7, 0.0000, 203000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0093', '2026-09-22 18:08:06', '2026-09-22 18:08:06'),
(396, 182, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00027: other_income', '2026-09-23 18:28:08', '2026-09-23 18:28:08'),
(397, 182, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00027: other_income', '2026-09-23 18:28:08', '2026-09-23 18:28:08'),
(398, 183, 7, 50000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00029: other_income', '2026-09-24 20:35:05', '2026-09-24 20:35:05'),
(399, 183, 92, 0.0000, 50000.0000, NULL, NULL, NULL, 'RCP/2026/09/00029: MB SALVA CASH', '2026-09-24 20:35:05', '2026-09-24 20:35:05'),
(400, 184, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00028: other_income', '2026-09-24 20:35:15', '2026-09-24 20:35:15'),
(401, 184, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00028: other_income', '2026-09-24 20:35:15', '2026-09-24 20:35:15'),
(402, 185, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN BANK', '2026-09-24 20:38:12', '2026-09-24 20:38:12'),
(403, 185, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0096', '2026-09-24 20:38:12', '2026-09-24 20:38:12'),
(404, 186, 119, 203000.0000, 0.0000, NULL, NULL, NULL, 'TARIK TUNAI', '2026-09-24 20:38:20', '2026-09-24 20:38:20'),
(405, 186, 7, 0.0000, 203000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0095', '2026-09-24 20:38:20', '2026-09-24 20:38:20'),
(406, 187, 119, 1400000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-09-24 20:42:52', '2026-09-24 20:42:52'),
(407, 187, 7, 0.0000, 1400000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0097', '2026-09-24 20:42:52', '2026-09-24 20:42:52'),
(408, 188, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN BANK JAGO', '2026-09-24 20:42:59', '2026-09-24 20:42:59'),
(409, 188, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0098', '2026-09-24 20:42:59', '2026-09-24 20:42:59'),
(410, 189, 119, 100000.0000, 0.0000, NULL, NULL, NULL, 'TRANSFER', '2026-09-27 18:30:58', '2026-09-27 18:30:58'),
(411, 189, 7, 0.0000, 100000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0099', '2026-09-27 18:30:58', '2026-09-27 18:30:58'),
(412, 190, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN BANK JAGO', '2026-09-27 18:31:13', '2026-09-27 18:31:13'),
(413, 190, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0100', '2026-09-27 18:31:13', '2026-09-27 18:31:13'),
(414, 191, 119, 200000.0000, 0.0000, NULL, NULL, NULL, 'TARIK TUNAI', '2026-09-27 18:31:20', '2026-09-27 18:31:20'),
(415, 191, 7, 0.0000, 200000.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0101', '2026-09-27 18:31:20', '2026-09-27 18:31:20'),
(416, 192, 113, 2500.0000, 0.0000, NULL, NULL, NULL, 'ADMIN BANK JAGO', '2026-09-27 18:31:27', '2026-09-27 18:31:27'),
(417, 192, 7, 0.0000, 2500.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0102', '2026-09-27 18:31:27', '2026-09-27 18:31:27'),
(418, 193, 7, 650000.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00030: other_income', '2026-09-27 18:32:18', '2026-09-27 18:32:18'),
(419, 193, 92, 0.0000, 650000.0000, NULL, NULL, NULL, 'RCP/2026/09/00030: other_income', '2026-09-27 18:32:18', '2026-09-27 18:32:18'),
(420, 194, 7, 449.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00031: other_income', '2026-09-27 23:43:49', '2026-09-27 23:43:49'),
(421, 194, 92, 0.0000, 449.0000, NULL, NULL, NULL, 'RCP/2026/09/00031: BUNGA BANK JAGO', '2026-09-27 23:43:49', '2026-09-27 23:43:49'),
(422, 195, 7, 89.0000, 0.0000, 4, NULL, NULL, 'RCP/2026/09/00032: other_income', '2026-09-27 23:43:57', '2026-09-27 23:43:57'),
(423, 195, 92, 0.0000, 89.0000, NULL, NULL, NULL, 'RCP/2026/09/00032: PAJAK BUNGA BANK JAGO', '2026-09-27 23:43:57', '2026-09-27 23:43:57'),
(424, 196, 7, 0.0000, 89.0000, 4, NULL, NULL, 'RCP/2026/09/00032: other_income', '2026-09-27 23:55:33', '2026-09-27 23:55:33'),
(425, 196, 92, 89.0000, 0.0000, NULL, NULL, NULL, 'RCP/2026/09/00032: PAJAK BUNGA BANK JAGO', '2026-09-27 23:55:33', '2026-09-27 23:55:33'),
(426, 197, 119, 89.0000, 0.0000, NULL, NULL, NULL, 'PAJAK BUNGA BANK JAGO', '2026-09-27 23:56:33', '2026-09-27 23:56:33'),
(427, 197, 7, 0.0000, 89.0000, 4, NULL, NULL, 'Pembayaran: EXP-2026-0103', '2026-09-27 23:56:33', '2026-09-27 23:56:33');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2026_05_06_042629_create_periods_table', 1),
(6, '2026_05_06_042658_create_accounts_table', 1),
(7, '2026_05_06_042736_create_wallets_table', 1),
(8, '2026_05_06_042759_create_contacts_table', 1),
(9, '2026_05_06_042835_create_products_table', 1),
(10, '2026_05_06_042903_create_tax_rules_table', 1),
(11, '2026_05_06_042937_create_journals_table', 1),
(12, '2026_05_06_043018_create_journal_lines_table', 1),
(13, '2026_05_06_043048_create_opening_balances_table', 1),
(14, '2026_05_06_043126_create_invoices_table', 1),
(15, '2026_05_06_043156_create_invoice_items_and_taxes_table', 1),
(16, '2026_05_06_043227_create_purchases_table', 1),
(17, '2026_05_06_043252_create_payments_table', 1),
(18, '2026_05_06_043334_create_expenses_table', 1),
(19, '2026_05_06_043400_create_inventory_movements_table', 1),
(20, '2026_05_06_043438_create_subscriptions_table', 1),
(21, '2026_05_06_043509_create_settings_table', 1),
(22, '2026_05_06_043539_create_audit_logs_table', 1),
(23, '2026_05_06_061007_add_equity_account_id_to_wallets_table', 1),
(24, '2026_05_06_061623_add_reversed_by_journal_id_to_journals_table', 1),
(25, '2026_05_06_070106_fix_ppn_masukan_account_code_and_category', 1),
(26, '2026_05_06_082736_add_attachment_to_expenses_table', 1),
(27, '2026_05_06_092327_create_permission_tables', 1),
(28, '2026_05_06_100000_create_credit_notes_tables', 1),
(29, '2026_05_06_100100_create_debit_notes_tables', 1),
(30, '2026_05_06_200000_create_fixed_assets_table', 1),
(31, '2026_05_06_234339_create_document_counters_table', 1),
(32, '2026_05_07_112600_fix_journal_source_enum', 1),
(33, '2026_05_07_113458_add_discount_to_credit_debit_notes', 1),
(34, '2026_05_07_115847_add_soft_deletes_to_accounting_tables', 1),
(35, '2026_05_07_120834_add_cash_sale_to_invoices_table', 1),
(36, '2026_05_07_123352_create_notifications_table', 1),
(37, '2026_05_08_000001_create_fund_transfers_table', 1),
(38, '2026_05_08_000002_create_other_receipts_table', 1),
(39, '2026_05_10_000001_add_accumulated_depreciation_account_to_fixed_assets', 1),
(40, '2026_05_10_000002_add_include_tax_to_expenses_table', 1),
(41, '2026_05_10_000003_create_tax_payments_table', 1),
(42, '2026_05_10_000004_update_accounts_table_for_new_coa', 1),
(43, '2026_05_11_000001_create_prepaid_expenses_table', 2),
(44, '2026_05_11_000002_create_deferred_revenues_table', 2),
(45, '2026_05_11_000003_add_reconciled_at_to_journal_lines', 3),
(46, '2026_08_05_100000_add_tax_method_to_credit_debit_notes', 4),
(47, '2026_08_05_110000_add_detail_to_products_table', 4),
(48, '2026_08_05_130244_create_units_table', 5),
(49, '2026_08_05_130319_change_unit_to_unit_id_in_products_table', 5);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opening_balances`
--

CREATE TABLE `opening_balances` (
  `id` bigint UNSIGNED NOT NULL,
  `period_id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `wallet_id` bigint UNSIGNED DEFAULT NULL,
  `amount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `position` enum('debit','credit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `opening_balances`
--

INSERT INTO `opening_balances` (`id`, `period_id`, `account_id`, `wallet_id`, `amount`, `position`, `created_at`, `updated_at`) VALUES
(8, 16, 7, 4, 1189202.0000, 'debit', '2026-08-08 00:19:29', '2026-08-08 00:19:29'),
(9, 16, 81, NULL, 1189202.0000, 'credit', '2026-08-08 00:19:29', '2026-08-08 00:19:29'),
(10, 17, 7, NULL, 44500.0000, 'debit', '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(11, 17, 81, NULL, 1189202.0000, 'credit', '2026-09-02 18:58:36', '2026-09-02 18:58:36'),
(12, 17, 83, NULL, 1144702.0000, 'debit', '2026-09-02 18:58:36', '2026-09-02 18:58:36');

-- --------------------------------------------------------

--
-- Table structure for table `other_receipts`
--

CREATE TABLE `other_receipts` (
  `id` bigint UNSIGNED NOT NULL,
  `number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_id` bigint UNSIGNED NOT NULL,
  `journal_id` bigint UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `wallet_id` bigint UNSIGNED NOT NULL,
  `contact_id` bigint UNSIGNED DEFAULT NULL,
  `receipt_type` enum('capital_injection','owner_loan','other_income','refund','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(18,4) NOT NULL,
  `credit_account_id` bigint UNSIGNED NOT NULL,
  `reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','posted','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `other_receipts`
--

INSERT INTO `other_receipts` (`id`, `number`, `period_id`, `journal_id`, `date`, `wallet_id`, `contact_id`, `receipt_type`, `amount`, `credit_account_id`, `reference`, `notes`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'RCP/2026/08/00001', 16, 27, '2026-08-01', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-08 00:24:54', '2026-08-08 00:28:39'),
(2, 'RCP/2026/08/00002', 16, 28, '2026-08-02', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-08 02:05:35', '2026-08-08 02:06:39'),
(3, 'RCP/2026/08/00003', 16, 29, '2026-08-03', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-08 02:08:36', '2026-08-08 02:08:57'),
(4, 'RCP/2026/08/00004', 16, 33, '2026-08-04', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-08 02:16:31', '2026-08-08 02:16:56'),
(5, 'RCP/2026/08/00005', 16, 41, '2026-08-05', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-08 02:30:43', '2026-08-08 02:30:59'),
(6, 'RCP/2026/08/00006', 16, 44, '2026-08-06', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-08 02:33:53', '2026-08-08 02:35:27'),
(7, 'RCP/2026/08/00007', 16, 43, '2026-08-06', 4, NULL, 'other_income', 100000.0000, 92, NULL, 'MB SALVA (TUKAR UANG)', 'posted', 1, '2026-08-08 02:34:59', '2026-08-08 02:35:18'),
(8, 'RCP/2026/08/00008', 16, 47, '2026-08-07', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-08 02:40:19', '2026-08-08 02:40:47'),
(9, 'RCP/2026/08/00009', 16, 48, '2026-08-08', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-10 03:05:14', '2026-08-10 03:06:47'),
(10, 'RCP/2026/08/00010', 16, 49, '2026-08-08', 4, NULL, 'other_income', 50000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-10 03:05:55', '2026-08-10 03:06:52'),
(11, 'RCP/2026/08/00011', 16, 50, '2026-08-08', 4, NULL, 'other_income', 600000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-10 03:06:27', '2026-08-10 03:06:58'),
(12, 'RCP/2026/08/00012', 16, 56, '2026-08-09', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-10 03:17:35', '2026-08-10 03:18:35'),
(13, 'RCP/2026/08/00013', 16, 58, '2026-08-10', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-10 19:26:23', '2026-08-10 19:26:47'),
(14, 'RCP/2026/08/00014', 16, 62, '2026-08-11', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-12 03:09:58', '2026-08-12 03:10:17'),
(15, 'RCP/2026/08/00015', 16, 66, '2026-08-12', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-12 03:13:01', '2026-08-12 03:14:08'),
(16, 'RCP/2026/08/00016', 16, 65, '2026-08-12', 4, NULL, 'other_income', 20000.0000, 92, NULL, 'TARIK TUNAI SEKALIAN', 'posted', 1, '2026-08-12 03:13:39', '2026-08-12 03:13:59'),
(17, 'RCP/2026/08/00017', 16, 69, '2026-08-13', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-13 02:49:41', '2026-08-13 02:50:13'),
(18, 'RCP/2026/08/00018', 16, 70, '2026-08-14', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-14 01:46:50', '2026-08-14 01:47:36'),
(19, 'RCP/2026/08/00019', 16, 72, '2026-08-15', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-15 01:06:17', '2026-08-15 01:06:42'),
(20, 'RCP/2026/08/00020', 16, 75, '2026-08-16', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-16 23:49:29', '2026-08-16 23:49:48'),
(21, 'RCP/2026/08/00021', 16, 77, '2026-08-17', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-16 23:53:58', '2026-08-16 23:54:10'),
(22, 'RCP/2026/08/00022', 16, 78, '2026-08-18', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-17 21:09:04', '2026-08-17 21:09:52'),
(23, 'RCP/2026/08/00023', 16, 80, '2026-08-20', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-19 20:36:49', '2026-08-19 20:37:09'),
(24, 'RCP/2026/08/00024', 16, 81, '2026-08-21', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-23 18:20:29', '2026-08-23 18:21:54'),
(25, 'RCP/2026/08/00025', 16, 82, '2026-08-22', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-23 18:20:59', '2026-08-23 18:22:00'),
(26, 'RCP/2026/08/00026', 16, 83, '2026-08-23', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-23 18:21:35', '2026-08-23 18:22:08'),
(27, 'RCP/2026/08/00027', 16, 84, '2026-08-24', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-24 03:18:18', '2026-08-24 03:18:38'),
(28, 'RCP/2026/08/00028', 16, 85, '2026-08-25', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-26 00:41:11', '2026-08-26 00:41:46'),
(29, 'RCP/2026/08/00029', 16, 86, '2026-08-27', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-26 23:13:00', '2026-08-26 23:13:30'),
(30, 'RCP/2026/08/00030', 16, 95, '2026-08-28', 4, NULL, 'other_income', 552.0000, 92, NULL, 'BUNGA MASUK DARI BANK JAGO', 'posted', 1, '2026-08-27 23:39:13', '2026-08-27 23:39:36'),
(31, 'RCP/2026/08/00031', 16, 100, '2026-08-28', 4, NULL, 'other_income', 738607.0000, 92, NULL, 'TARIK TIKTOK', 'posted', 1, '2026-08-28 02:45:27', '2026-08-28 02:45:46'),
(32, 'RCP/2026/08/00032', 16, 102, '2026-08-29', 4, NULL, 'other_income', 200000.0000, 92, NULL, NULL, 'posted', 1, '2026-08-29 00:07:04', '2026-08-29 00:07:31'),
(33, 'RCP/2026/09/00001', 17, 107, '2026-09-01', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-02 19:02:44', '2026-09-02 19:10:24'),
(34, 'RCP/2026/09/00002', 17, 108, '2026-09-02', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-02 19:04:18', '2026-09-02 19:10:31'),
(35, 'RCP/2026/09/00003', 17, 109, '2026-09-03', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-02 19:04:53', '2026-09-02 19:10:39'),
(36, 'RCP/2026/09/00004', 17, 112, '2026-09-04', 4, NULL, 'other_income', 950000.0000, 92, NULL, NULL, 'cancelled', 1, '2026-09-04 02:48:02', '2026-09-04 02:53:24'),
(37, 'RCP/2026/09/00005', 17, 116, '2026-09-04', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-04 02:54:02', '2026-09-04 02:54:15'),
(38, 'RCP/2026/09/00006', 17, 119, '2026-09-05', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-05 02:07:18', '2026-09-05 02:07:42'),
(39, 'RCP/2026/09/00007', 17, 120, '2026-09-06', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-06 19:19:44', '2026-09-06 19:20:20'),
(40, 'RCP/2026/09/00008', 17, 121, '2026-09-07', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-06 19:20:03', '2026-09-06 19:20:28'),
(41, 'RCP/2026/09/00009', 17, 124, '2026-09-08', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-07 23:48:43', '2026-09-07 23:49:21'),
(42, 'RCP/2026/09/00010', 17, 125, '2026-09-09', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-08 20:30:46', '2026-09-08 20:31:06'),
(43, 'RCP/2026/09/00011', 17, 133, '2026-09-10', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-09 18:51:06', '2026-09-09 18:51:26'),
(44, 'RCP/2026/09/00012', 17, 135, '2026-09-11', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-10 19:59:59', '2026-09-10 20:00:51'),
(45, 'RCP/2026/09/00013', 17, 140, '2026-09-12', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-11 18:41:53', '2026-09-11 18:42:35'),
(46, 'RCP/2026/09/00014', 17, 142, '2026-09-13', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-13 20:48:09', '2026-09-13 20:48:56'),
(47, 'RCP/2026/09/00015', 17, 141, '2026-09-14', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-13 20:48:27', '2026-09-13 20:48:48'),
(48, 'RCP/2026/09/00016', 17, 146, '2026-09-15', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-14 18:40:22', '2026-09-14 18:40:43'),
(49, 'RCP/2026/09/00017', 17, 151, '2026-09-16', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-16 00:13:10', '2026-09-16 00:18:51'),
(50, 'RCP/2026/09/00018', 17, 158, '2026-09-17', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-16 18:34:00', '2026-09-16 18:35:00'),
(51, 'RCP/2026/09/00019', 17, 159, '2026-09-18', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-17 18:33:08', '2026-09-17 18:34:20'),
(52, 'RCP/2026/09/00020', 17, 163, '2026-09-18', 4, NULL, 'other_income', 500000.0000, 92, NULL, 'BELANJA FROZEN FOOD', 'posted', 1, '2026-09-20 18:34:06', '2026-09-20 18:53:44'),
(53, 'RCP/2026/09/00021', 17, 162, '2026-09-18', 4, NULL, 'other_income', 200000.0000, 92, NULL, 'BELANJA BERAS DLL', 'posted', 1, '2026-09-20 18:35:00', '2026-09-20 18:53:30'),
(54, 'RCP/2026/09/00022', 17, 161, '2026-09-18', 4, NULL, 'other_income', 170000.0000, 92, NULL, 'TITIPAN MB SALVA', 'posted', 1, '2026-09-20 18:41:24', '2026-09-20 18:53:22'),
(55, 'RCP/2026/09/00023', 17, 164, '2026-09-20', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-20 18:52:41', '2026-09-20 18:54:18'),
(56, 'RCP/2026/09/00024', 17, 165, '2026-09-21', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-20 18:53:04', '2026-09-20 18:54:39'),
(57, 'RCP/2026/09/00025', 17, 174, '2026-09-22', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-21 18:29:44', '2026-09-21 18:31:11'),
(58, 'RCP/2026/09/00026', 17, 179, '2026-09-22', 4, NULL, 'other_income', 10000.0000, 92, NULL, 'MB SALVA CASH', 'posted', 1, '2026-09-21 23:38:42', '2026-09-21 23:39:08'),
(59, 'RCP/2026/09/00027', 17, 182, '2026-09-24', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-23 18:27:47', '2026-09-23 18:28:08'),
(60, 'RCP/2026/09/00028', 17, 184, '2026-09-25', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-24 20:34:24', '2026-09-24 20:35:15'),
(61, 'RCP/2026/09/00029', 17, 183, '2026-09-25', 4, NULL, 'other_income', 50000.0000, 92, NULL, 'MB SALVA CASH', 'posted', 1, '2026-09-24 20:34:47', '2026-09-24 20:35:05'),
(62, 'RCP/2026/09/00030', 17, 193, '2026-09-28', 4, NULL, 'other_income', 650000.0000, 92, NULL, NULL, 'posted', 1, '2026-09-27 18:32:05', '2026-09-27 18:32:18'),
(63, 'RCP/2026/09/00031', 17, 194, '2026-09-28', 4, NULL, 'other_income', 449.0000, 92, NULL, 'BUNGA BANK JAGO', 'posted', 1, '2026-09-27 23:43:03', '2026-09-27 23:43:50'),
(64, 'RCP/2026/09/00032', 17, 195, '2026-09-28', 4, NULL, 'other_income', 89.0000, 92, NULL, 'PAJAK BUNGA BANK JAGO', 'cancelled', 1, '2026-09-27 23:43:32', '2026-09-27 23:55:33');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint UNSIGNED NOT NULL,
  `number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payable_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payable_id` bigint UNSIGNED NOT NULL,
  `journal_id` bigint UNSIGNED DEFAULT NULL,
  `period_id` bigint UNSIGNED NOT NULL,
  `wallet_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `amount` decimal(18,4) NOT NULL,
  `withholding_amount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `method` enum('cash','transfer','ewallet','giro','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','verified','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `periods`
--

CREATE TABLE `periods` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT '0',
  `closed_at` timestamp NULL DEFAULT NULL,
  `closed_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `periods`
--

INSERT INTO `periods` (`id`, `name`, `start_date`, `end_date`, `is_closed`, `closed_at`, `closed_by`, `created_at`, `updated_at`) VALUES
(16, 'Agustus 2026', '2026-08-01', '2026-08-31', 1, '2026-09-02 18:58:36', 1, '2026-08-05 06:20:09', '2026-09-02 18:58:36'),
(17, 'September 2026', '2026-09-01', '2026-09-30', 0, NULL, NULL, '2026-09-02 18:58:06', '2026-09-02 18:58:06');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'view_dashboard', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(2, 'manage_accounts', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(3, 'manage_contacts', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(4, 'manage_products', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(5, 'manage_wallets', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(6, 'manage_tax_rules', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(7, 'manage_periods', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(8, 'manage_invoices', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(9, 'manage_payments', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(10, 'manage_purchases', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(11, 'manage_supplier_payments', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(12, 'manage_expenses', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(13, 'manage_journals', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(14, 'view_reports', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(15, 'manage_settings', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(16, 'manage_users', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(17, 'manage_opening_balance', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(18, 'close_period', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(19, 'reopen_period', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prepaid_expenses`
--

CREATE TABLE `prepaid_expenses` (
  `id` bigint UNSIGNED NOT NULL,
  `period_id` bigint UNSIGNED NOT NULL,
  `asset_account_id` bigint UNSIGNED NOT NULL,
  `expense_account_id` bigint UNSIGNED NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(18,4) NOT NULL,
  `remaining_amount` decimal(18,4) NOT NULL,
  `start_date` date NOT NULL,
  `total_months` int NOT NULL,
  `months_amortized` int NOT NULL DEFAULT '0',
  `monthly_amount` decimal(18,4) NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `journal_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('goods','service','subscription','bundle') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'goods',
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `detail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `purchase_price` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `selling_price` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `stock_on_hand` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `stock_minimum` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `tax_rate` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `revenue_account_id` bigint UNSIGNED DEFAULT NULL,
  `cogs_account_id` bigint UNSIGNED DEFAULT NULL,
  `inventory_account_id` bigint UNSIGNED DEFAULT NULL,
  `purchase_account_id` bigint UNSIGNED DEFAULT NULL,
  `subscription_duration` int DEFAULT NULL,
  `subscription_unit` enum('day','month','year') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `code`, `name`, `type`, `unit_id`, `detail`, `description`, `purchase_price`, `selling_price`, `stock_on_hand`, `stock_minimum`, `tax_rate`, `revenue_account_id`, `cogs_account_id`, `inventory_account_id`, `purchase_account_id`, `subscription_duration`, `subscription_unit`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 'PRD-XMAB-0808-26', 'Test Product', 'goods', 1, NULL, NULL, 10000.0000, 15000.0000, 100.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-08-07 20:54:14', '2026-08-07 20:54:14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint UNSIGNED NOT NULL,
  `number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_id` bigint UNSIGNED NOT NULL,
  `period_id` bigint UNSIGNED NOT NULL,
  `journal_id` bigint UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `supplier_invoice_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `discount_amount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `tax_amount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `total` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `paid_amount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `due_amount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `status` enum('draft','posted','partially_paid','paid','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` decimal(18,4) NOT NULL DEFAULT '1.0000',
  `unit` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `discount_percent` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `discount_amount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `subtotal` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `account_id` bigint UNSIGNED DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_taxes`
--

CREATE TABLE `purchase_taxes` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED NOT NULL,
  `tax_rule_id` bigint UNSIGNED NOT NULL,
  `tax_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` enum('exclusive','inclusive','withholding') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` decimal(8,4) NOT NULL,
  `base_amount` decimal(18,4) NOT NULL,
  `tax_amount` decimal(18,4) NOT NULL,
  `debit_account_id` bigint UNSIGNED NOT NULL,
  `credit_account_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(2, 'accountant', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(3, 'cashier', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(4, 'viewer', 'web', '2026-05-10 03:56:51', '2026-05-10 03:56:51');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(17, 2),
(18, 2),
(19, 2),
(1, 3),
(3, 3),
(8, 3),
(9, 3),
(12, 3),
(1, 4),
(14, 4);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `label` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `group`, `label`, `description`, `created_at`, `updated_at`) VALUES
(1, 'ar_account_id', '10', 'integer', 'accounting', 'Akun Piutang Usaha Default', 'Akun piutang yang digunakan saat membuat invoice', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(2, 'ap_account_id', '56', 'integer', 'accounting', 'Akun Hutang Usaha Default', 'Akun hutang yang digunakan saat membuat purchase order', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(3, 'revenue_account_id', '88', 'integer', 'accounting', 'Akun Pendapatan Default', 'Akun pendapatan utama untuk penjualan', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(4, 'cogs_account_id', '99', 'integer', 'accounting', 'Akun HPP Default', 'Akun harga pokok penjualan untuk barang', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(5, 'inventory_account_id', '19', 'integer', 'accounting', 'Akun Persediaan Default', 'Akun persediaan barang dagang', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(6, 'equity_account_id', '81', 'integer', 'accounting', 'Akun Modal Default', 'Akun modal pemilik', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(7, 'retained_earnings_id', '83', 'integer', 'accounting', 'Akun Laba Ditahan', 'Akun laba ditahan untuk closing periode', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(8, 'income_summary_id', '84', 'integer', 'accounting', 'Akun Ikhtisar Laba Rugi', 'Akun ikhtisar L/R untuk proses penutupan periode', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(9, 'ppn_output_account_id', '69', 'integer', 'tax', 'Akun PPN Keluaran', 'Akun hutang PPN dari penjualan', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(10, 'ppn_input_account_id', '28', 'integer', 'tax', 'Akun PPN Masukan', 'Akun PPN dari pembelian', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(11, 'pph_payable_account_id', '70', 'integer', 'tax', 'Akun Hutang PPh', 'Akun hutang pajak penghasilan yang dipotong', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(12, 'pph_prepaid_account_id', '29', 'integer', 'tax', 'Akun PPh Dibayar Dimuka', 'Akun PPh pasal 23 yang dibayar dimuka', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(13, 'accumulated_depreciation_account_id', '36', 'integer', 'accounting', 'Akun Akumulasi Penyusutan Default', 'Akun default untuk akumulasi penyusutan aset tetap', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(14, 'stock_gain_account_id', '92', 'integer', 'accounting', 'Akun Pendapatan Selisih Stok', 'Akun untuk mencatat kelebihan stok saat opname', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(15, 'stock_loss_account_id', '108', 'integer', 'accounting', 'Akun Beban Selisih Stok', 'Akun untuk mencatat kekurangan stok saat opname', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(16, 'fixed_asset_account_id', '37', 'integer', 'general', NULL, NULL, NULL, '2026-05-10 03:56:51'),
(17, 'gain_on_disposal_account_id', '92', 'integer', 'general', NULL, NULL, NULL, '2026-05-10 03:56:51'),
(18, 'loss_on_disposal_account_id', '106', 'integer', 'general', NULL, NULL, NULL, '2026-05-10 03:56:51'),
(19, 'expense_account_id', '111', 'integer', 'general', NULL, NULL, NULL, '2026-05-10 03:56:51'),
(20, 'invoice_prefix', 'INV', 'string', 'format', 'Prefix Nomor Invoice', 'Awalan nomor faktur penjualan', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(21, 'purchase_prefix', 'PO', 'string', 'format', 'Prefix Nomor Pembelian', 'Awalan nomor purchase order', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(22, 'journal_prefix', 'JNL', 'string', 'format', 'Prefix Nomor Jurnal', 'Awalan nomor jurnal umum', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(23, 'payment_prefix', 'PAY', 'string', 'format', 'Prefix Nomor Pembayaran', 'Awalan nomor pembayaran', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(24, 'expense_prefix', 'EXP', 'string', 'format', 'Prefix Nomor Beban', 'Awalan nomor pencatatan beban', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(25, 'opname_prefix', 'SOP', 'string', 'format', 'Prefix Stock Opname', 'Awalan nomor stock opname', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(26, 'currency_code', 'IDR', 'string', 'format', 'Kode Mata Uang', 'Kode mata uang ISO 4217', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(27, 'currency_symbol', 'Rp', 'string', 'format', 'Simbol Mata Uang', 'Simbol yang ditampilkan di tampilan', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(28, 'decimal_separator', ',', 'string', 'format', 'Pemisah Desimal', 'Karakter pemisah angka desimal', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(29, 'thousand_separator', '.', 'string', 'format', 'Pemisah Ribuan', 'Karakter pemisah ribuan', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(30, 'default_tax_rate', '11', 'integer', 'tax', 'Tarif Pajak Default (%)', 'Persentase pajak default untuk transaksi', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(31, 'company_name', 'Onezie Accounting', 'string', 'general', 'Nama Perusahaan', 'Nama resmi perusahaan', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(32, 'company_address', '', 'string', 'general', 'Alamat Perusahaan', 'Alamat lengkap perusahaan', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(33, 'company_phone', '', 'string', 'general', 'Telepon Perusahaan', 'Nomor telepon perusahaan', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(34, 'company_tax_number', '', 'string', 'general', 'NPWP Perusahaan', 'Nomor Pokok Wajib Pajak', '2026-05-10 03:56:51', '2026-05-10 03:56:51');

-- --------------------------------------------------------

--
-- Table structure for table `stock_opnames`
--

CREATE TABLE `stock_opnames` (
  `id` bigint UNSIGNED NOT NULL,
  `number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_id` bigint UNSIGNED NOT NULL,
  `journal_id` bigint UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','posted','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_opname_items`
--

CREATE TABLE `stock_opname_items` (
  `id` bigint UNSIGNED NOT NULL,
  `stock_opname_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `qty_book` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `qty_actual` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `qty_diff` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `unit_cost` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `total_diff_value` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint UNSIGNED NOT NULL,
  `contact_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `price` decimal(18,4) NOT NULL,
  `billing_cycle` int NOT NULL DEFAULT '1',
  `billing_unit` enum('day','month','year') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'month',
  `next_invoice_date` date DEFAULT NULL,
  `auto_invoice` tinyint(1) NOT NULL DEFAULT '1',
  `status` enum('active','inactive','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tax_payments`
--

CREATE TABLE `tax_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `period_id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `journal_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `document_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(20,4) NOT NULL,
  `reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nomor SSP / NTPN',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tax_rules`
--

CREATE TABLE `tax_rules` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('ppn','pph','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` enum('sale','purchase','both') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'both',
  `method` enum('exclusive','inclusive','withholding') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` decimal(8,4) NOT NULL,
  `debit_account_id` bigint UNSIGNED NOT NULL,
  `credit_account_id` bigint UNSIGNED NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tax_rules`
--

INSERT INTO `tax_rules` (`id`, `code`, `name`, `type`, `module`, `method`, `rate`, `debit_account_id`, `credit_account_id`, `is_default`, `is_active`, `description`, `created_at`, `updated_at`) VALUES
(1, 'PPN_11', 'PPN 11%', 'ppn', 'both', 'exclusive', 11.0000, 28, 69, 1, 1, 'Pajak Pertambahan Nilai 11% untuk penjualan dan pembelian', '2026-05-10 03:56:51', '2026-05-10 03:56:51'),
(2, 'PPH23_2', 'PPh Pasal 23 - Jasa 2%', 'pph', 'purchase', 'withholding', 2.0000, 70, 70, 0, 1, 'Pemotongan PPh Pasal 23 atas jasa dengan tarif 2%', '2026-05-10 03:56:51', '2026-05-10 03:56:51');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `code`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Pieces', 'pcs', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(2, 'Unit', 'unit', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(3, 'Buah', 'buah', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(4, 'Kilogram', 'kg', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(5, 'Gram', 'gram', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(6, 'Ton', 'ton', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(7, 'Liter', 'liter', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(8, 'Mililiter', 'ml', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(9, 'Meter', 'meter', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(10, 'Sentimeter', 'cm', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(11, 'Kilometer', 'km', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(12, 'Box', 'box', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(13, 'Karton', 'karton', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(14, 'Lusin', 'lusin', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(15, 'Kodi', 'kodi', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(16, 'Rim', 'rim', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(17, 'Set', 'set', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(18, 'Pasang', 'pasang', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(19, 'Roll', 'roll', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(20, 'Lembar', 'lembar', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(21, 'Botol', 'botol', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(22, 'Kaleng', 'kaleng', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(23, 'Paket', 'paket', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(24, 'Pack', 'pack', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(25, 'Dus', 'dus', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(26, 'Bal', 'bal', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(27, 'Kwintal', 'kwintal', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(28, 'Jam', 'jam', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(29, 'Hari', 'hari', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(30, 'Minggu', 'minggu', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(31, 'Bulan', 'bulan', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09'),
(32, 'Tahun', 'tahun', 1, '2026-08-05 06:05:09', '2026-08-05 06:05:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@example.com', '2026-05-10 03:56:52', '$2y$12$n1mkfu8Ssc.aMlAFnVuJVOgPd4m3cdFrQUfZmZvxRIkI2mCkQoski', 'OMvWpIKDhIOrpSqMPDQtuaGWLd1Uw8fc8lNTdTg3iRodMyJy61PGAFyt2107', '2026-05-10 03:56:52', '2026-05-10 03:56:52');

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('cash','bank','ewallet') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_holder` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `opening_balance` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `equity_account_id` bigint UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `name`, `type`, `bank_name`, `account_number`, `account_holder`, `account_id`, `opening_balance`, `equity_account_id`, `is_active`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 'Bank Umum', 'bank', 'Bank BRI', NULL, NULL, 7, 1189202.0000, 81, 1, NULL, '2026-08-01 00:19:28', '2026-08-01 00:19:28', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `accounts_code_unique` (`code`),
  ADD KEY `accounts_category_is_active_index` (`category`,`is_active`),
  ADD KEY `accounts_parent_id_index` (`parent_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  ADD KEY `audit_logs_user_id_index` (`user_id`),
  ADD KEY `audit_logs_created_at_index` (`created_at`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contacts_code_unique` (`code`),
  ADD KEY `contacts_ar_account_id_foreign` (`ar_account_id`),
  ADD KEY `contacts_ap_account_id_foreign` (`ap_account_id`),
  ADD KEY `contacts_type_index` (`type`),
  ADD KEY `contacts_name_index` (`name`);

--
-- Indexes for table `credit_notes`
--
ALTER TABLE `credit_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `credit_notes_number_unique` (`number`),
  ADD KEY `credit_notes_period_id_foreign` (`period_id`),
  ADD KEY `credit_notes_journal_id_foreign` (`journal_id`),
  ADD KEY `credit_notes_created_by_foreign` (`created_by`),
  ADD KEY `credit_notes_status_date_index` (`status`,`date`),
  ADD KEY `credit_notes_contact_id_index` (`contact_id`),
  ADD KEY `credit_notes_invoice_id_index` (`invoice_id`);

--
-- Indexes for table `credit_note_items`
--
ALTER TABLE `credit_note_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `credit_note_items_credit_note_id_foreign` (`credit_note_id`),
  ADD KEY `credit_note_items_product_id_foreign` (`product_id`),
  ADD KEY `credit_note_items_invoice_item_id_foreign` (`invoice_item_id`),
  ADD KEY `credit_note_items_revenue_account_id_foreign` (`revenue_account_id`);

--
-- Indexes for table `debit_notes`
--
ALTER TABLE `debit_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `debit_notes_number_unique` (`number`),
  ADD KEY `debit_notes_period_id_foreign` (`period_id`),
  ADD KEY `debit_notes_journal_id_foreign` (`journal_id`),
  ADD KEY `debit_notes_created_by_foreign` (`created_by`),
  ADD KEY `debit_notes_status_date_index` (`status`,`date`),
  ADD KEY `debit_notes_contact_id_index` (`contact_id`),
  ADD KEY `debit_notes_purchase_id_index` (`purchase_id`);

--
-- Indexes for table `debit_note_items`
--
ALTER TABLE `debit_note_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `debit_note_items_debit_note_id_foreign` (`debit_note_id`),
  ADD KEY `debit_note_items_product_id_foreign` (`product_id`),
  ADD KEY `debit_note_items_purchase_item_id_foreign` (`purchase_item_id`),
  ADD KEY `debit_note_items_account_id_foreign` (`account_id`);

--
-- Indexes for table `deferred_revenues`
--
ALTER TABLE `deferred_revenues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deferred_revenues_liability_account_id_foreign` (`liability_account_id`),
  ADD KEY `deferred_revenues_revenue_account_id_foreign` (`revenue_account_id`),
  ADD KEY `deferred_revenues_created_by_foreign` (`created_by`),
  ADD KEY `deferred_revenues_journal_id_foreign` (`journal_id`),
  ADD KEY `deferred_revenues_status_index` (`status`),
  ADD KEY `deferred_revenues_period_id_status_index` (`period_id`,`status`);

--
-- Indexes for table `document_counters`
--
ALTER TABLE `document_counters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `document_counters_type_year_month_unique` (`type`,`year`,`month`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expenses_number_unique` (`number`),
  ADD KEY `expenses_journal_id_foreign` (`journal_id`),
  ADD KEY `expenses_wallet_id_foreign` (`wallet_id`),
  ADD KEY `expenses_contact_id_foreign` (`contact_id`),
  ADD KEY `expenses_created_by_foreign` (`created_by`),
  ADD KEY `expenses_period_id_date_index` (`period_id`,`date`),
  ADD KEY `expenses_account_id_index` (`account_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fixed_assets_code_unique` (`code`),
  ADD KEY `fixed_assets_asset_account_id_foreign` (`asset_account_id`),
  ADD KEY `fixed_assets_depreciation_account_id_foreign` (`depreciation_account_id`),
  ADD KEY `fixed_assets_created_by_foreign` (`created_by`),
  ADD KEY `fixed_assets_accumulated_depreciation_account_id_foreign` (`accumulated_depreciation_account_id`);

--
-- Indexes for table `fund_transfers`
--
ALTER TABLE `fund_transfers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fund_transfers_number_unique` (`number`),
  ADD KEY `fund_transfers_journal_id_foreign` (`journal_id`),
  ADD KEY `fund_transfers_from_wallet_id_foreign` (`from_wallet_id`),
  ADD KEY `fund_transfers_to_wallet_id_foreign` (`to_wallet_id`),
  ADD KEY `fund_transfers_fee_account_id_foreign` (`fee_account_id`),
  ADD KEY `fund_transfers_created_by_foreign` (`created_by`),
  ADD KEY `fund_transfers_period_id_date_index` (`period_id`,`date`);

--
-- Indexes for table `inventory_movements`
--
ALTER TABLE `inventory_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_movements_journal_id_foreign` (`journal_id`),
  ADD KEY `inventory_movements_created_by_foreign` (`created_by`),
  ADD KEY `inventory_movements_product_id_date_index` (`product_id`,`date`),
  ADD KEY `inventory_movements_ref_type_ref_id_index` (`ref_type`,`ref_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_number_unique` (`number`),
  ADD KEY `invoices_journal_id_foreign` (`journal_id`),
  ADD KEY `invoices_created_by_foreign` (`created_by`),
  ADD KEY `invoices_contact_id_status_index` (`contact_id`,`status`),
  ADD KEY `invoices_period_id_date_index` (`period_id`,`date`),
  ADD KEY `invoices_due_date_index` (`due_date`),
  ADD KEY `invoices_wallet_id_foreign` (`wallet_id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_items_revenue_account_id_foreign` (`revenue_account_id`),
  ADD KEY `invoice_items_cogs_account_id_foreign` (`cogs_account_id`),
  ADD KEY `invoice_items_invoice_id_index` (`invoice_id`),
  ADD KEY `invoice_items_product_id_index` (`product_id`);

--
-- Indexes for table `invoice_taxes`
--
ALTER TABLE `invoice_taxes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_taxes_tax_rule_id_foreign` (`tax_rule_id`),
  ADD KEY `invoice_taxes_debit_account_id_foreign` (`debit_account_id`),
  ADD KEY `invoice_taxes_credit_account_id_foreign` (`credit_account_id`),
  ADD KEY `invoice_taxes_invoice_id_index` (`invoice_id`);

--
-- Indexes for table `journals`
--
ALTER TABLE `journals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `journals_number_unique` (`number`),
  ADD KEY `journals_period_id_foreign` (`period_id`),
  ADD KEY `journals_reversed_journal_id_foreign` (`reversed_journal_id`),
  ADD KEY `journals_created_by_foreign` (`created_by`),
  ADD KEY `journals_date_period_id_index` (`date`,`period_id`),
  ADD KEY `journals_ref_type_ref_id_index` (`ref_type`,`ref_id`),
  ADD KEY `journals_source_index` (`source`),
  ADD KEY `journals_reversed_by_journal_id_foreign` (`reversed_by_journal_id`);

--
-- Indexes for table `journal_lines`
--
ALTER TABLE `journal_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_lines_journal_id_index` (`journal_id`),
  ADD KEY `journal_lines_account_id_index` (`account_id`),
  ADD KEY `journal_lines_wallet_id_index` (`wallet_id`),
  ADD KEY `journal_lines_reconciled_by_foreign` (`reconciled_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `opening_balances`
--
ALTER TABLE `opening_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ob_period_account_wallet_unique` (`period_id`,`account_id`,`wallet_id`),
  ADD KEY `opening_balances_wallet_id_foreign` (`wallet_id`),
  ADD KEY `opening_balances_account_id_index` (`account_id`);

--
-- Indexes for table `other_receipts`
--
ALTER TABLE `other_receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `other_receipts_number_unique` (`number`),
  ADD KEY `other_receipts_journal_id_foreign` (`journal_id`),
  ADD KEY `other_receipts_wallet_id_foreign` (`wallet_id`),
  ADD KEY `other_receipts_contact_id_foreign` (`contact_id`),
  ADD KEY `other_receipts_credit_account_id_foreign` (`credit_account_id`),
  ADD KEY `other_receipts_created_by_foreign` (`created_by`),
  ADD KEY `other_receipts_period_id_date_index` (`period_id`,`date`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_number_unique` (`number`),
  ADD KEY `payments_journal_id_foreign` (`journal_id`),
  ADD KEY `payments_verified_by_foreign` (`verified_by`),
  ADD KEY `payments_created_by_foreign` (`created_by`),
  ADD KEY `payments_payable_type_payable_id_index` (`payable_type`,`payable_id`),
  ADD KEY `payments_period_id_date_index` (`period_id`,`date`),
  ADD KEY `payments_wallet_id_index` (`wallet_id`);

--
-- Indexes for table `periods`
--
ALTER TABLE `periods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `periods_closed_by_foreign` (`closed_by`),
  ADD KEY `periods_start_date_end_date_index` (`start_date`,`end_date`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `prepaid_expenses`
--
ALTER TABLE `prepaid_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prepaid_expenses_asset_account_id_foreign` (`asset_account_id`),
  ADD KEY `prepaid_expenses_expense_account_id_foreign` (`expense_account_id`),
  ADD KEY `prepaid_expenses_created_by_foreign` (`created_by`),
  ADD KEY `prepaid_expenses_journal_id_foreign` (`journal_id`),
  ADD KEY `prepaid_expenses_status_index` (`status`),
  ADD KEY `prepaid_expenses_period_id_status_index` (`period_id`,`status`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_code_unique` (`code`),
  ADD KEY `products_revenue_account_id_foreign` (`revenue_account_id`),
  ADD KEY `products_cogs_account_id_foreign` (`cogs_account_id`),
  ADD KEY `products_inventory_account_id_foreign` (`inventory_account_id`),
  ADD KEY `products_purchase_account_id_foreign` (`purchase_account_id`),
  ADD KEY `products_type_is_active_index` (`type`,`is_active`),
  ADD KEY `products_unit_id_foreign` (`unit_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchases_number_unique` (`number`),
  ADD KEY `purchases_journal_id_foreign` (`journal_id`),
  ADD KEY `purchases_created_by_foreign` (`created_by`),
  ADD KEY `purchases_contact_id_status_index` (`contact_id`,`status`),
  ADD KEY `purchases_period_id_date_index` (`period_id`,`date`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_items_product_id_foreign` (`product_id`),
  ADD KEY `purchase_items_account_id_foreign` (`account_id`),
  ADD KEY `purchase_items_purchase_id_index` (`purchase_id`);

--
-- Indexes for table `purchase_taxes`
--
ALTER TABLE `purchase_taxes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_taxes_tax_rule_id_foreign` (`tax_rule_id`),
  ADD KEY `purchase_taxes_debit_account_id_foreign` (`debit_account_id`),
  ADD KEY `purchase_taxes_credit_account_id_foreign` (`credit_account_id`),
  ADD KEY `purchase_taxes_purchase_id_index` (`purchase_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`),
  ADD KEY `settings_group_index` (`group`);

--
-- Indexes for table `stock_opnames`
--
ALTER TABLE `stock_opnames`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_opnames_number_unique` (`number`),
  ADD KEY `stock_opnames_period_id_foreign` (`period_id`),
  ADD KEY `stock_opnames_journal_id_foreign` (`journal_id`),
  ADD KEY `stock_opnames_created_by_foreign` (`created_by`);

--
-- Indexes for table `stock_opname_items`
--
ALTER TABLE `stock_opname_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_opname_items_stock_opname_id_index` (`stock_opname_id`),
  ADD KEY `stock_opname_items_product_id_index` (`product_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscriptions_product_id_foreign` (`product_id`),
  ADD KEY `subscriptions_contact_id_status_index` (`contact_id`,`status`),
  ADD KEY `subscriptions_next_invoice_date_index` (`next_invoice_date`);

--
-- Indexes for table `tax_payments`
--
ALTER TABLE `tax_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tax_payments_document_number_unique` (`document_number`),
  ADD KEY `tax_payments_period_id_foreign` (`period_id`),
  ADD KEY `tax_payments_account_id_foreign` (`account_id`),
  ADD KEY `tax_payments_journal_id_foreign` (`journal_id`),
  ADD KEY `tax_payments_created_by_foreign` (`created_by`),
  ADD KEY `tax_payments_tax_type_status_index` (`tax_type`,`status`),
  ADD KEY `tax_payments_payment_date_index` (`payment_date`);

--
-- Indexes for table `tax_rules`
--
ALTER TABLE `tax_rules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tax_rules_code_unique` (`code`),
  ADD KEY `tax_rules_debit_account_id_foreign` (`debit_account_id`),
  ADD KEY `tax_rules_credit_account_id_foreign` (`credit_account_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `units_code_unique` (`code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallets_account_id_foreign` (`account_id`),
  ADD KEY `wallets_equity_account_id_foreign` (`equity_account_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `credit_notes`
--
ALTER TABLE `credit_notes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `credit_note_items`
--
ALTER TABLE `credit_note_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `debit_notes`
--
ALTER TABLE `debit_notes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `debit_note_items`
--
ALTER TABLE `debit_note_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deferred_revenues`
--
ALTER TABLE `deferred_revenues`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_counters`
--
ALTER TABLE `document_counters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fund_transfers`
--
ALTER TABLE `fund_transfers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_movements`
--
ALTER TABLE `inventory_movements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `invoice_taxes`
--
ALTER TABLE `invoice_taxes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `journals`
--
ALTER TABLE `journals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=198;

--
-- AUTO_INCREMENT for table `journal_lines`
--
ALTER TABLE `journal_lines`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=428;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `opening_balances`
--
ALTER TABLE `opening_balances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `other_receipts`
--
ALTER TABLE `other_receipts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `periods`
--
ALTER TABLE `periods`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prepaid_expenses`
--
ALTER TABLE `prepaid_expenses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `purchase_taxes`
--
ALTER TABLE `purchase_taxes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `stock_opnames`
--
ALTER TABLE `stock_opnames`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_opname_items`
--
ALTER TABLE `stock_opname_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tax_payments`
--
ALTER TABLE `tax_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tax_rules`
--
ALTER TABLE `tax_rules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_ap_account_id_foreign` FOREIGN KEY (`ap_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contacts_ar_account_id_foreign` FOREIGN KEY (`ar_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `credit_notes`
--
ALTER TABLE `credit_notes`
  ADD CONSTRAINT `credit_notes_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`),
  ADD CONSTRAINT `credit_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `credit_notes_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `credit_notes_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `credit_notes_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`);

--
-- Constraints for table `credit_note_items`
--
ALTER TABLE `credit_note_items`
  ADD CONSTRAINT `credit_note_items_credit_note_id_foreign` FOREIGN KEY (`credit_note_id`) REFERENCES `credit_notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `credit_note_items_invoice_item_id_foreign` FOREIGN KEY (`invoice_item_id`) REFERENCES `invoice_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `credit_note_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `credit_note_items_revenue_account_id_foreign` FOREIGN KEY (`revenue_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `debit_notes`
--
ALTER TABLE `debit_notes`
  ADD CONSTRAINT `debit_notes_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`),
  ADD CONSTRAINT `debit_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `debit_notes_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `debit_notes_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`),
  ADD CONSTRAINT `debit_notes_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `debit_note_items`
--
ALTER TABLE `debit_note_items`
  ADD CONSTRAINT `debit_note_items_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `debit_note_items_debit_note_id_foreign` FOREIGN KEY (`debit_note_id`) REFERENCES `debit_notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `debit_note_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `debit_note_items_purchase_item_id_foreign` FOREIGN KEY (`purchase_item_id`) REFERENCES `purchase_items` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `deferred_revenues`
--
ALTER TABLE `deferred_revenues`
  ADD CONSTRAINT `deferred_revenues_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `deferred_revenues_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`),
  ADD CONSTRAINT `deferred_revenues_liability_account_id_foreign` FOREIGN KEY (`liability_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `deferred_revenues_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`),
  ADD CONSTRAINT `deferred_revenues_revenue_account_id_foreign` FOREIGN KEY (`revenue_account_id`) REFERENCES `accounts` (`id`);

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `expenses_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`),
  ADD CONSTRAINT `expenses_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`);

--
-- Constraints for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  ADD CONSTRAINT `fixed_assets_accumulated_depreciation_account_id_foreign` FOREIGN KEY (`accumulated_depreciation_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fixed_assets_asset_account_id_foreign` FOREIGN KEY (`asset_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fixed_assets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fixed_assets_depreciation_account_id_foreign` FOREIGN KEY (`depreciation_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `fund_transfers`
--
ALTER TABLE `fund_transfers`
  ADD CONSTRAINT `fund_transfers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fund_transfers_fee_account_id_foreign` FOREIGN KEY (`fee_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fund_transfers_from_wallet_id_foreign` FOREIGN KEY (`from_wallet_id`) REFERENCES `wallets` (`id`),
  ADD CONSTRAINT `fund_transfers_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fund_transfers_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`),
  ADD CONSTRAINT `fund_transfers_to_wallet_id_foreign` FOREIGN KEY (`to_wallet_id`) REFERENCES `wallets` (`id`);

--
-- Constraints for table `inventory_movements`
--
ALTER TABLE `inventory_movements`
  ADD CONSTRAINT `inventory_movements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_movements_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`),
  ADD CONSTRAINT `invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`),
  ADD CONSTRAINT `invoices_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_cogs_account_id_foreign` FOREIGN KEY (`cogs_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoice_items_revenue_account_id_foreign` FOREIGN KEY (`revenue_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoice_taxes`
--
ALTER TABLE `invoice_taxes`
  ADD CONSTRAINT `invoice_taxes_credit_account_id_foreign` FOREIGN KEY (`credit_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `invoice_taxes_debit_account_id_foreign` FOREIGN KEY (`debit_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `invoice_taxes_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_taxes_tax_rule_id_foreign` FOREIGN KEY (`tax_rule_id`) REFERENCES `tax_rules` (`id`);

--
-- Constraints for table `journals`
--
ALTER TABLE `journals`
  ADD CONSTRAINT `journals_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journals_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`),
  ADD CONSTRAINT `journals_reversed_by_journal_id_foreign` FOREIGN KEY (`reversed_by_journal_id`) REFERENCES `journals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journals_reversed_journal_id_foreign` FOREIGN KEY (`reversed_journal_id`) REFERENCES `journals` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `journal_lines`
--
ALTER TABLE `journal_lines`
  ADD CONSTRAINT `journal_lines_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `journal_lines_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journal_lines_reconciled_by_foreign` FOREIGN KEY (`reconciled_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `journal_lines_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `opening_balances`
--
ALTER TABLE `opening_balances`
  ADD CONSTRAINT `opening_balances_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `opening_balances_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`),
  ADD CONSTRAINT `opening_balances_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `other_receipts`
--
ALTER TABLE `other_receipts`
  ADD CONSTRAINT `other_receipts_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `other_receipts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `other_receipts_credit_account_id_foreign` FOREIGN KEY (`credit_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `other_receipts_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `other_receipts_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`),
  ADD CONSTRAINT `other_receipts_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`),
  ADD CONSTRAINT `payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`);

--
-- Constraints for table `periods`
--
ALTER TABLE `periods`
  ADD CONSTRAINT `periods_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `prepaid_expenses`
--
ALTER TABLE `prepaid_expenses`
  ADD CONSTRAINT `prepaid_expenses_asset_account_id_foreign` FOREIGN KEY (`asset_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `prepaid_expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `prepaid_expenses_expense_account_id_foreign` FOREIGN KEY (`expense_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `prepaid_expenses_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`),
  ADD CONSTRAINT `prepaid_expenses_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_cogs_account_id_foreign` FOREIGN KEY (`cogs_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_inventory_account_id_foreign` FOREIGN KEY (`inventory_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_purchase_account_id_foreign` FOREIGN KEY (`purchase_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_revenue_account_id_foreign` FOREIGN KEY (`revenue_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`),
  ADD CONSTRAINT `purchases_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchases_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchases_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`);

--
-- Constraints for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `purchase_items_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_taxes`
--
ALTER TABLE `purchase_taxes`
  ADD CONSTRAINT `purchase_taxes_credit_account_id_foreign` FOREIGN KEY (`credit_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `purchase_taxes_debit_account_id_foreign` FOREIGN KEY (`debit_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `purchase_taxes_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_taxes_tax_rule_id_foreign` FOREIGN KEY (`tax_rule_id`) REFERENCES `tax_rules` (`id`);

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_opnames`
--
ALTER TABLE `stock_opnames`
  ADD CONSTRAINT `stock_opnames_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_opnames_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_opnames_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`);

--
-- Constraints for table `stock_opname_items`
--
ALTER TABLE `stock_opname_items`
  ADD CONSTRAINT `stock_opname_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `stock_opname_items_stock_opname_id_foreign` FOREIGN KEY (`stock_opname_id`) REFERENCES `stock_opnames` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`),
  ADD CONSTRAINT `subscriptions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `tax_payments`
--
ALTER TABLE `tax_payments`
  ADD CONSTRAINT `tax_payments_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `tax_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tax_payments_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tax_payments_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`);

--
-- Constraints for table `tax_rules`
--
ALTER TABLE `tax_rules`
  ADD CONSTRAINT `tax_rules_credit_account_id_foreign` FOREIGN KEY (`credit_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `tax_rules_debit_account_id_foreign` FOREIGN KEY (`debit_account_id`) REFERENCES `accounts` (`id`);

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `wallets_equity_account_id_foreign` FOREIGN KEY (`equity_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
