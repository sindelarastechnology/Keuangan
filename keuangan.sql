-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 22, 2026 at 02:03 AM
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
-- Database: `keuangan`
--

-- --------------------------------------------------------

--
-- Table structure for table `akun_perkiraan`
--

CREATE TABLE `akun_perkiraan` (
  `id` bigint UNSIGNED NOT NULL,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` enum('aset','kewajiban','modal','pendapatan','beban') COLLATE utf8mb4_unicode_ci NOT NULL,
  `saldo_normal` enum('debit','kredit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_header` tinyint(1) NOT NULL DEFAULT '0',
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `is_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `akun_perkiraan`
--

INSERT INTO `akun_perkiraan` (`id`, `kode`, `nama`, `jenis`, `saldo_normal`, `is_header`, `parent_id`, `keterangan`, `is_aktif`, `created_at`, `updated_at`, `user_id`) VALUES
(1, '526', 'Beban Penyusutan', 'beban', 'debit', 0, NULL, NULL, 1, '2026-09-15 01:26:26', '2026-09-15 01:26:26', NULL),
(2, '1', 'ASET', 'aset', 'debit', 1, NULL, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(3, '11', 'Aset Lancar', 'aset', 'debit', 1, 2, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(4, '111', 'Kas', 'aset', 'debit', 0, 3, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(5, '112', 'Bank', 'aset', 'debit', 0, 3, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(6, '113', 'Piutang Dagang', 'aset', 'debit', 0, 3, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(7, '114', 'Persediaan Bahan Baku', 'aset', 'debit', 0, 3, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(8, '115', 'Persediaan Barang Dagang', 'aset', 'debit', 0, 3, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(9, '12', 'Aset Tetap', 'aset', 'debit', 1, 2, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(10, '121', 'Peralatan', 'aset', 'debit', 0, 9, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(11, '122', 'Akomulasi Penyusutan', 'aset', 'kredit', 0, 9, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(12, '2', 'KEWAJIBAN', 'kewajiban', 'kredit', 1, NULL, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(13, '21', 'Kewajiban Lancar', 'kewajiban', 'kredit', 1, 12, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(14, '211', 'Utang Usaha', 'kewajiban', 'kredit', 0, 13, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(15, '212', 'Utang PPN Keluaran', 'kewajiban', 'kredit', 0, 13, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(16, '213', 'PPN Masukan', 'kewajiban', 'kredit', 0, 13, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(17, '3', 'MODAL', 'modal', 'kredit', 1, NULL, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(18, '31', 'Modal', 'modal', 'kredit', 0, 17, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(19, '32', 'Laba Ditahan', 'modal', 'kredit', 0, 17, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(20, '33', 'Laba / Rugi Berjalan', 'modal', 'kredit', 0, 17, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(21, '4', 'PENDAPATAN', 'pendapatan', 'kredit', 1, NULL, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(22, '41', 'Pendapatan Usaha', 'pendapatan', 'kredit', 1, 21, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(23, '411', 'Penjualan Barang', 'pendapatan', 'kredit', 0, 22, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(24, '412', 'Pendapatan Jasa', 'pendapatan', 'kredit', 0, 22, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(25, '42', 'Pendapatan Lain', 'pendapatan', 'kredit', 1, 21, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(26, '421', 'Pendapatan Lain-lain', 'pendapatan', 'kredit', 0, 25, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(27, '5', 'BEBAN', 'beban', 'debit', 1, NULL, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(28, '51', 'Harga Pokok Penjualan', 'beban', 'debit', 0, 27, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(29, '52', 'Beban Operasional', 'beban', 'debit', 1, 27, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(30, '521', 'Beban Gaji', 'beban', 'debit', 0, 29, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(31, '522', 'Beban Sewa', 'beban', 'debit', 0, 29, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(32, '523', 'Beban Listrik, Air & Telepon', 'beban', 'debit', 0, 29, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(33, '524', 'Beban Transportasi', 'beban', 'debit', 0, 29, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(34, '525', 'Beban Perlengkapan', 'beban', 'debit', 0, 29, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(35, '526', 'Beban Penyusutan', 'beban', 'debit', 0, 29, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(36, '53', 'Beban Lain-lain', 'beban', 'debit', 1, 27, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(37, '531', 'Beban Lain-lain', 'beban', 'debit', 0, 36, NULL, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(38, '1', 'ASET', 'aset', 'debit', 1, NULL, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(39, '11', 'Aset Lancar', 'aset', 'debit', 1, 38, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(40, '111', 'Kas', 'aset', 'debit', 0, 39, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(41, '112', 'Bank', 'aset', 'debit', 0, 39, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(42, '113', 'Piutang Dagang', 'aset', 'debit', 0, 39, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(43, '114', 'Persediaan Bahan Baku', 'aset', 'debit', 0, 39, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(44, '115', 'Persediaan Barang Dagang', 'aset', 'debit', 0, 39, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(45, '12', 'Aset Tetap', 'aset', 'debit', 1, 38, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(46, '121', 'Peralatan', 'aset', 'debit', 0, 45, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(47, '122', 'Akomulasi Penyusutan', 'aset', 'kredit', 0, 45, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(48, '2', 'KEWAJIBAN', 'kewajiban', 'kredit', 1, NULL, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(49, '21', 'Kewajiban Lancar', 'kewajiban', 'kredit', 1, 48, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(50, '211', 'Utang Usaha', 'kewajiban', 'kredit', 0, 49, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(51, '212', 'Utang PPN Keluaran', 'kewajiban', 'kredit', 0, 49, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(52, '213', 'PPN Masukan', 'kewajiban', 'kredit', 0, 49, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(53, '3', 'MODAL', 'modal', 'kredit', 1, NULL, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(54, '31', 'Modal', 'modal', 'kredit', 0, 53, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(55, '32', 'Laba Ditahan', 'modal', 'kredit', 0, 53, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(56, '33', 'Laba / Rugi Berjalan', 'modal', 'kredit', 0, 53, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(57, '4', 'PENDAPATAN', 'pendapatan', 'kredit', 1, NULL, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(58, '41', 'Pendapatan Usaha', 'pendapatan', 'kredit', 1, 57, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(59, '411', 'Penjualan Barang', 'pendapatan', 'kredit', 0, 58, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(60, '412', 'Pendapatan Jasa', 'pendapatan', 'kredit', 0, 58, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(61, '42', 'Pendapatan Lain', 'pendapatan', 'kredit', 1, 57, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(62, '421', 'Pendapatan Lain-lain', 'pendapatan', 'kredit', 0, 61, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(63, '5', 'BEBAN', 'beban', 'debit', 1, NULL, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(64, '51', 'Harga Pokok Penjualan', 'beban', 'debit', 0, 63, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(65, '52', 'Beban Operasional', 'beban', 'debit', 1, 63, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(66, '521', 'Beban Gaji', 'beban', 'debit', 0, 65, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(67, '522', 'Beban Sewa', 'beban', 'debit', 0, 65, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(68, '523', 'Beban Listrik, Air & Telepon', 'beban', 'debit', 0, 65, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(69, '524', 'Beban Transportasi', 'beban', 'debit', 0, 65, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(70, '525', 'Beban Perlengkapan', 'beban', 'debit', 0, 65, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(71, '526', 'Beban Penyusutan', 'beban', 'debit', 0, 65, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(72, '53', 'Beban Lain-lain', 'beban', 'debit', 1, 63, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(73, '531', 'Beban Lain-lain', 'beban', 'debit', 0, 72, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(74, '1', 'ASET', 'aset', 'debit', 1, NULL, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(75, '11', 'Aset Lancar', 'aset', 'debit', 1, 74, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(76, '111', 'Kas', 'aset', 'debit', 0, 75, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(77, '112', 'Bank', 'aset', 'debit', 0, 75, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(78, '113', 'Piutang Dagang', 'aset', 'debit', 0, 75, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(79, '114', 'Persediaan Bahan Baku', 'aset', 'debit', 0, 75, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(80, '115', 'Persediaan Barang Dagang', 'aset', 'debit', 0, 75, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(81, '12', 'Aset Tetap', 'aset', 'debit', 1, 74, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(82, '121', 'Peralatan', 'aset', 'debit', 0, 81, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(83, '122', 'Akomulasi Penyusutan', 'aset', 'kredit', 0, 81, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(84, '2', 'KEWAJIBAN', 'kewajiban', 'kredit', 1, NULL, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(85, '21', 'Kewajiban Lancar', 'kewajiban', 'kredit', 1, 84, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(86, '211', 'Utang Usaha', 'kewajiban', 'kredit', 0, 85, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(87, '212', 'Utang PPN Keluaran', 'kewajiban', 'kredit', 0, 85, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(88, '213', 'PPN Masukan', 'kewajiban', 'kredit', 0, 85, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(89, '3', 'MODAL', 'modal', 'kredit', 1, NULL, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(90, '31', 'Modal', 'modal', 'kredit', 0, 89, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(91, '32', 'Laba Ditahan', 'modal', 'kredit', 0, 89, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(92, '33', 'Laba / Rugi Berjalan', 'modal', 'kredit', 0, 89, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(93, '4', 'PENDAPATAN', 'pendapatan', 'kredit', 1, NULL, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(94, '41', 'Pendapatan Usaha', 'pendapatan', 'kredit', 1, 93, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(95, '411', 'Penjualan Barang', 'pendapatan', 'kredit', 0, 94, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(96, '412', 'Pendapatan Jasa', 'pendapatan', 'kredit', 0, 94, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(97, '42', 'Pendapatan Lain', 'pendapatan', 'kredit', 1, 93, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(98, '421', 'Pendapatan Lain-lain', 'pendapatan', 'kredit', 0, 97, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(99, '5', 'BEBAN', 'beban', 'debit', 1, NULL, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(100, '51', 'Harga Pokok Penjualan', 'beban', 'debit', 0, 99, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(101, '52', 'Beban Operasional', 'beban', 'debit', 1, 99, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(102, '521', 'Beban Gaji', 'beban', 'debit', 0, 101, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(103, '522', 'Beban Sewa', 'beban', 'debit', 0, 101, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(104, '523', 'Beban Listrik, Air & Telepon', 'beban', 'debit', 0, 101, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(105, '524', 'Beban Transportasi', 'beban', 'debit', 0, 101, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(106, '525', 'Beban Perlengkapan', 'beban', 'debit', 0, 101, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(107, '526', 'Beban Penyusutan', 'beban', 'debit', 0, 101, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(108, '53', 'Beban Lain-lain', 'beban', 'debit', 1, 99, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(109, '531', 'Beban Lain-lain', 'beban', 'debit', 0, 108, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(110, '1', 'ASET', 'aset', 'debit', 1, NULL, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(111, '11', 'Aset Lancar', 'aset', 'debit', 1, 110, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(112, '111', 'Kas', 'aset', 'debit', 0, 111, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(113, '112', 'Bank', 'aset', 'debit', 0, 111, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(114, '113', 'Piutang Dagang', 'aset', 'debit', 0, 111, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(115, '114', 'Persediaan Bahan Baku', 'aset', 'debit', 0, 111, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(116, '115', 'Persediaan Barang Dagang', 'aset', 'debit', 0, 111, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(117, '12', 'Aset Tetap', 'aset', 'debit', 1, 110, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(118, '121', 'Peralatan', 'aset', 'debit', 0, 117, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(119, '122', 'Akomulasi Penyusutan', 'aset', 'kredit', 0, 117, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(120, '2', 'KEWAJIBAN', 'kewajiban', 'kredit', 1, NULL, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(121, '21', 'Kewajiban Lancar', 'kewajiban', 'kredit', 1, 120, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(122, '211', 'Utang Usaha', 'kewajiban', 'kredit', 0, 121, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(123, '212', 'Utang PPN Keluaran', 'kewajiban', 'kredit', 0, 121, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(124, '213', 'PPN Masukan', 'kewajiban', 'kredit', 0, 121, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(125, '3', 'MODAL', 'modal', 'kredit', 1, NULL, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(126, '31', 'Modal', 'modal', 'kredit', 0, 125, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(127, '32', 'Laba Ditahan', 'modal', 'kredit', 0, 125, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(128, '33', 'Laba / Rugi Berjalan', 'modal', 'kredit', 0, 125, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(129, '4', 'PENDAPATAN', 'pendapatan', 'kredit', 1, NULL, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(130, '41', 'Pendapatan Usaha', 'pendapatan', 'kredit', 1, 129, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(131, '411', 'Penjualan Barang', 'pendapatan', 'kredit', 0, 130, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(132, '412', 'Pendapatan Jasa', 'pendapatan', 'kredit', 0, 130, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(133, '42', 'Pendapatan Lain', 'pendapatan', 'kredit', 1, 129, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(134, '421', 'Pendapatan Lain-lain', 'pendapatan', 'kredit', 0, 133, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(135, '5', 'BEBAN', 'beban', 'debit', 1, NULL, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(136, '51', 'Harga Pokok Penjualan', 'beban', 'debit', 0, 135, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(137, '52', 'Beban Operasional', 'beban', 'debit', 1, 135, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(138, '521', 'Beban Gaji', 'beban', 'debit', 0, 137, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(139, '522', 'Beban Sewa', 'beban', 'debit', 0, 137, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(140, '523', 'Beban Listrik, Air & Telepon', 'beban', 'debit', 0, 137, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(141, '524', 'Beban Transportasi', 'beban', 'debit', 0, 137, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(142, '525', 'Beban Perlengkapan', 'beban', 'debit', 0, 137, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(143, '526', 'Beban Penyusutan', 'beban', 'debit', 0, 137, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(144, '53', 'Beban Lain-lain', 'beban', 'debit', 1, 135, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(145, '531', 'Beban Lain-lain', 'beban', 'debit', 0, 144, NULL, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(146, '1', 'ASET', 'aset', 'debit', 1, NULL, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(147, '11', 'Aset Lancar', 'aset', 'debit', 1, 146, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(148, '111', 'Kas', 'aset', 'debit', 0, 147, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(149, '112', 'Bank', 'aset', 'debit', 0, 147, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(150, '113', 'Piutang Dagang', 'aset', 'debit', 0, 147, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(151, '114', 'Persediaan Bahan Baku', 'aset', 'debit', 0, 147, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(152, '115', 'Persediaan Barang Dagang', 'aset', 'debit', 0, 147, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(153, '12', 'Aset Tetap', 'aset', 'debit', 1, 146, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(154, '121', 'Peralatan', 'aset', 'debit', 0, 153, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(155, '122', 'Akomulasi Penyusutan', 'aset', 'kredit', 0, 153, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(156, '2', 'KEWAJIBAN', 'kewajiban', 'kredit', 1, NULL, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(157, '21', 'Kewajiban Lancar', 'kewajiban', 'kredit', 1, 156, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(158, '211', 'Utang Usaha', 'kewajiban', 'kredit', 0, 157, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(159, '212', 'Utang PPN Keluaran', 'kewajiban', 'kredit', 0, 157, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(160, '213', 'PPN Masukan', 'kewajiban', 'kredit', 0, 157, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(161, '3', 'MODAL', 'modal', 'kredit', 1, NULL, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(162, '31', 'Modal', 'modal', 'kredit', 0, 161, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(163, '32', 'Laba Ditahan', 'modal', 'kredit', 0, 161, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(164, '33', 'Laba / Rugi Berjalan', 'modal', 'kredit', 0, 161, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(165, '4', 'PENDAPATAN', 'pendapatan', 'kredit', 1, NULL, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(166, '41', 'Pendapatan Usaha', 'pendapatan', 'kredit', 1, 165, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(167, '411', 'Penjualan Barang', 'pendapatan', 'kredit', 0, 166, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(168, '412', 'Pendapatan Jasa', 'pendapatan', 'kredit', 0, 166, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(169, '42', 'Pendapatan Lain', 'pendapatan', 'kredit', 1, 165, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(170, '421', 'Pendapatan Lain-lain', 'pendapatan', 'kredit', 0, 169, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(171, '5', 'BEBAN', 'beban', 'debit', 1, NULL, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(172, '51', 'Harga Pokok Penjualan', 'beban', 'debit', 0, 171, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(173, '52', 'Beban Operasional', 'beban', 'debit', 1, 171, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(174, '521', 'Beban Gaji', 'beban', 'debit', 0, 173, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(175, '522', 'Beban Sewa', 'beban', 'debit', 0, 173, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(176, '523', 'Beban Listrik, Air & Telepon', 'beban', 'debit', 0, 173, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(177, '524', 'Beban Transportasi', 'beban', 'debit', 0, 173, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(178, '525', 'Beban Perlengkapan', 'beban', 'debit', 0, 173, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(179, '526', 'Beban Penyusutan', 'beban', 'debit', 0, 173, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(180, '53', 'Beban Lain-lain', 'beban', 'debit', 1, 171, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(181, '531', 'Beban Lain-lain', 'beban', 'debit', 0, 180, NULL, 1, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(182, '1', 'ASET', 'aset', 'debit', 1, NULL, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(183, '11', 'Aset Lancar', 'aset', 'debit', 1, 182, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(184, '111', 'Kas', 'aset', 'debit', 0, 183, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(185, '112', 'Bank', 'aset', 'debit', 0, 183, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(186, '113', 'Piutang Dagang', 'aset', 'debit', 0, 183, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(187, '114', 'Persediaan Bahan Baku', 'aset', 'debit', 0, 183, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(188, '115', 'Persediaan Barang Dagang', 'aset', 'debit', 0, 183, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(189, '12', 'Aset Tetap', 'aset', 'debit', 1, 182, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(190, '121', 'Peralatan', 'aset', 'debit', 0, 189, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(191, '122', 'Akomulasi Penyusutan', 'aset', 'kredit', 0, 189, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(192, '2', 'KEWAJIBAN', 'kewajiban', 'kredit', 1, NULL, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(193, '21', 'Kewajiban Lancar', 'kewajiban', 'kredit', 1, 192, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(194, '211', 'Utang Usaha', 'kewajiban', 'kredit', 0, 193, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(195, '212', 'Utang PPN Keluaran', 'kewajiban', 'kredit', 0, 193, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(196, '213', 'PPN Masukan', 'kewajiban', 'kredit', 0, 193, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(197, '3', 'MODAL', 'modal', 'kredit', 1, NULL, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(198, '31', 'Modal', 'modal', 'kredit', 0, 197, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(199, '32', 'Laba Ditahan', 'modal', 'kredit', 0, 197, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(200, '33', 'Laba / Rugi Berjalan', 'modal', 'kredit', 0, 197, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(201, '4', 'PENDAPATAN', 'pendapatan', 'kredit', 1, NULL, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(202, '41', 'Pendapatan Usaha', 'pendapatan', 'kredit', 1, 201, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(203, '411', 'Penjualan Barang', 'pendapatan', 'kredit', 0, 202, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(204, '412', 'Pendapatan Jasa', 'pendapatan', 'kredit', 0, 202, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(205, '42', 'Pendapatan Lain', 'pendapatan', 'kredit', 1, 201, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(206, '421', 'Pendapatan Lain-lain', 'pendapatan', 'kredit', 0, 205, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(207, '5', 'BEBAN', 'beban', 'debit', 1, NULL, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(208, '51', 'Harga Pokok Penjualan', 'beban', 'debit', 0, 207, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(209, '52', 'Beban Operasional', 'beban', 'debit', 1, 207, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(210, '521', 'Beban Gaji', 'beban', 'debit', 0, 209, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(211, '522', 'Beban Sewa', 'beban', 'debit', 0, 209, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(212, '523', 'Beban Listrik, Air & Telepon', 'beban', 'debit', 0, 209, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(213, '524', 'Beban Transportasi', 'beban', 'debit', 0, 209, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(214, '525', 'Beban Perlengkapan', 'beban', 'debit', 0, 209, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(215, '526', 'Beban Penyusutan', 'beban', 'debit', 0, 209, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(216, '53', 'Beban Lain-lain', 'beban', 'debit', 1, 207, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(217, '531', 'Beban Lain-lain', 'beban', 'debit', 0, 216, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(218, '1', 'ASET', 'aset', 'debit', 1, NULL, NULL, 1, '2026-09-15 22:01:39', '2026-09-15 22:01:39', 7),
(219, '11', 'Aset Lancar', 'aset', 'debit', 1, 218, NULL, 1, '2026-09-15 22:01:39', '2026-09-15 22:01:39', 7),
(220, '111', 'Kas', 'aset', 'debit', 0, 219, NULL, 1, '2026-09-15 22:01:39', '2026-09-15 22:01:39', 7),
(221, '112', 'Bank', 'aset', 'debit', 0, 219, NULL, 1, '2026-09-15 22:01:39', '2026-09-15 22:01:39', 7),
(222, '113', 'Piutang Dagang', 'aset', 'debit', 0, 219, NULL, 1, '2026-09-15 22:01:39', '2026-09-15 22:01:39', 7),
(223, '114', 'Persediaan Bahan Baku', 'aset', 'debit', 0, 219, NULL, 1, '2026-09-15 22:01:39', '2026-09-15 22:01:39', 7),
(224, '115', 'Persediaan Barang Dagang', 'aset', 'debit', 0, 219, NULL, 1, '2026-09-15 22:01:39', '2026-09-15 22:01:39', 7),
(225, '12', 'Aset Tetap', 'aset', 'debit', 1, 218, NULL, 1, '2026-09-15 22:01:39', '2026-09-15 22:01:39', 7),
(226, '121', 'Peralatan', 'aset', 'debit', 0, 225, NULL, 1, '2026-09-15 22:01:39', '2026-09-15 22:01:39', 7),
(227, '122', 'Akomulasi Penyusutan', 'aset', 'kredit', 0, 225, NULL, 1, '2026-09-15 22:01:39', '2026-09-15 22:01:39', 7),
(228, '2', 'KEWAJIBAN', 'kewajiban', 'kredit', 1, NULL, NULL, 1, '2026-09-15 22:01:39', '2026-09-15 22:01:39', 7),
(229, '21', 'Kewajiban Lancar', 'kewajiban', 'kredit', 1, 228, NULL, 1, '2026-09-15 22:01:39', '2026-09-15 22:01:39', 7),
(230, '211', 'Utang Usaha', 'kewajiban', 'kredit', 0, 229, NULL, 1, '2026-09-15 22:01:39', '2026-09-15 22:01:39', 7),
(231, '212', 'Utang PPN Keluaran', 'kewajiban', 'kredit', 0, 229, NULL, 1, '2026-09-15 22:01:39', '2026-09-15 22:01:39', 7),
(232, '213', 'PPN Masukan', 'kewajiban', 'kredit', 0, 229, NULL, 1, '2026-09-15 22:01:39', '2026-09-15 22:01:39', 7),
(233, '3', 'MODAL', 'modal', 'kredit', 1, NULL, NULL, 1, '2026-09-15 22:01:39', '2026-09-15 22:01:39', 7),
(234, '31', 'Modal', 'modal', 'kredit', 0, 233, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(235, '32', 'Laba Ditahan', 'modal', 'kredit', 0, 233, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(236, '33', 'Laba / Rugi Berjalan', 'modal', 'kredit', 0, 233, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(237, '4', 'PENDAPATAN', 'pendapatan', 'kredit', 1, NULL, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(238, '41', 'Pendapatan Usaha', 'pendapatan', 'kredit', 1, 237, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(239, '411', 'Penjualan Barang', 'pendapatan', 'kredit', 0, 238, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(240, '412', 'Pendapatan Jasa', 'pendapatan', 'kredit', 0, 238, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(241, '42', 'Pendapatan Lain', 'pendapatan', 'kredit', 1, 237, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(242, '421', 'Pendapatan Lain-lain', 'pendapatan', 'kredit', 0, 241, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(243, '5', 'BEBAN', 'beban', 'debit', 1, NULL, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(244, '51', 'Harga Pokok Penjualan', 'beban', 'debit', 0, 243, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(245, '52', 'Beban Operasional', 'beban', 'debit', 1, 243, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(246, '521', 'Beban Gaji', 'beban', 'debit', 0, 245, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(247, '522', 'Beban Sewa', 'beban', 'debit', 0, 245, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(248, '523', 'Beban Listrik, Air & Telepon', 'beban', 'debit', 0, 245, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(249, '524', 'Beban Transportasi', 'beban', 'debit', 0, 245, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(250, '525', 'Beban Perlengkapan', 'beban', 'debit', 0, 245, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(251, '526', 'Beban Penyusutan', 'beban', 'debit', 0, 245, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(252, '53', 'Beban Lain-lain', 'beban', 'debit', 1, 243, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(253, '531', 'Beban Lain-lain', 'beban', 'debit', 0, 252, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(254, '1', 'ASET', 'aset', 'debit', 1, NULL, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(255, '11', 'Aset Lancar', 'aset', 'debit', 1, 254, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(256, '111', 'Kas', 'aset', 'debit', 0, 255, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(257, '112', 'Bank', 'aset', 'debit', 0, 255, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(258, '113', 'Piutang Dagang', 'aset', 'debit', 0, 255, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(259, '114', 'Persediaan Bahan Baku', 'aset', 'debit', 0, 255, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(260, '115', 'Persediaan Barang Dagang', 'aset', 'debit', 0, 255, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(261, '12', 'Aset Tetap', 'aset', 'debit', 1, 254, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(262, '121', 'Peralatan', 'aset', 'debit', 0, 261, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(263, '122', 'Akomulasi Penyusutan', 'aset', 'kredit', 0, 261, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(264, '2', 'KEWAJIBAN', 'kewajiban', 'kredit', 1, NULL, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(265, '21', 'Kewajiban Lancar', 'kewajiban', 'kredit', 1, 264, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(266, '211', 'Utang Usaha', 'kewajiban', 'kredit', 0, 265, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(267, '212', 'Utang PPN Keluaran', 'kewajiban', 'kredit', 0, 265, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(268, '213', 'PPN Masukan', 'kewajiban', 'kredit', 0, 265, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(269, '3', 'MODAL', 'modal', 'kredit', 1, NULL, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(270, '31', 'Modal', 'modal', 'kredit', 0, 269, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(271, '32', 'Laba Ditahan', 'modal', 'kredit', 0, 269, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(272, '33', 'Laba / Rugi Berjalan', 'modal', 'kredit', 0, 269, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(273, '4', 'PENDAPATAN', 'pendapatan', 'kredit', 1, NULL, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(274, '41', 'Pendapatan Usaha', 'pendapatan', 'kredit', 1, 273, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(275, '411', 'Penjualan Barang', 'pendapatan', 'kredit', 0, 274, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(276, '412', 'Pendapatan Jasa', 'pendapatan', 'kredit', 0, 274, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(277, '42', 'Pendapatan Lain', 'pendapatan', 'kredit', 1, 273, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(278, '421', 'Pendapatan Lain-lain', 'pendapatan', 'kredit', 0, 277, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(279, '5', 'BEBAN', 'beban', 'debit', 1, NULL, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(280, '51', 'Harga Pokok Penjualan', 'beban', 'debit', 0, 279, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(281, '52', 'Beban Operasional', 'beban', 'debit', 1, 279, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(282, '521', 'Beban Gaji', 'beban', 'debit', 0, 281, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(283, '522', 'Beban Sewa', 'beban', 'debit', 0, 281, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(284, '523', 'Beban Listrik, Air & Telepon', 'beban', 'debit', 0, 281, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(285, '524', 'Beban Transportasi', 'beban', 'debit', 0, 281, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(286, '525', 'Beban Perlengkapan', 'beban', 'debit', 0, 281, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(287, '526', 'Beban Penyusutan', 'beban', 'debit', 0, 281, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(288, '53', 'Beban Lain-lain', 'beban', 'debit', 1, 279, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(289, '531', 'Beban Lain-lain', 'beban', 'debit', 0, 288, NULL, 1, '2026-09-16 00:21:16', '2026-09-16 00:21:16', 8),
(290, '1', 'ASET', 'aset', 'debit', 1, NULL, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(291, '11', 'Aset Lancar', 'aset', 'debit', 1, 290, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(292, '111', 'Kas', 'aset', 'debit', 0, 291, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(293, '112', 'Bank', 'aset', 'debit', 0, 291, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(294, '113', 'Piutang Dagang', 'aset', 'debit', 0, 291, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(295, '114', 'Persediaan Bahan Baku', 'aset', 'debit', 0, 291, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(296, '115', 'Persediaan Barang Dagang', 'aset', 'debit', 0, 291, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(297, '12', 'Aset Tetap', 'aset', 'debit', 1, 290, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(298, '121', 'Peralatan', 'aset', 'debit', 0, 297, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(299, '122', 'Akomulasi Penyusutan', 'aset', 'kredit', 0, 297, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(300, '2', 'KEWAJIBAN', 'kewajiban', 'kredit', 1, NULL, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(301, '21', 'Kewajiban Lancar', 'kewajiban', 'kredit', 1, 300, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(302, '211', 'Utang Usaha', 'kewajiban', 'kredit', 0, 301, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(303, '212', 'Utang PPN Keluaran', 'kewajiban', 'kredit', 0, 301, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(304, '213', 'PPN Masukan', 'kewajiban', 'kredit', 0, 301, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(305, '3', 'MODAL', 'modal', 'kredit', 1, NULL, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(306, '31', 'Modal', 'modal', 'kredit', 0, 305, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(307, '32', 'Laba Ditahan', 'modal', 'kredit', 0, 305, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(308, '33', 'Laba / Rugi Berjalan', 'modal', 'kredit', 0, 305, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(309, '4', 'PENDAPATAN', 'pendapatan', 'kredit', 1, NULL, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(310, '41', 'Pendapatan Usaha', 'pendapatan', 'kredit', 1, 309, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(311, '411', 'Penjualan Barang', 'pendapatan', 'kredit', 0, 310, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(312, '412', 'Pendapatan Jasa', 'pendapatan', 'kredit', 0, 310, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(313, '42', 'Pendapatan Lain', 'pendapatan', 'kredit', 1, 309, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(314, '421', 'Pendapatan Lain-lain', 'pendapatan', 'kredit', 0, 313, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(315, '5', 'BEBAN', 'beban', 'debit', 1, NULL, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(316, '51', 'Harga Pokok Penjualan', 'beban', 'debit', 0, 315, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(317, '52', 'Beban Operasional', 'beban', 'debit', 1, 315, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(318, '521', 'Beban Gaji', 'beban', 'debit', 0, 317, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(319, '522', 'Beban Sewa', 'beban', 'debit', 0, 317, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(320, '523', 'Beban Listrik, Air & Telepon', 'beban', 'debit', 0, 317, NULL, 1, '2026-09-16 19:59:49', '2026-09-16 19:59:49', 9),
(321, '524', 'Beban Transportasi', 'beban', 'debit', 0, 317, NULL, 1, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(322, '525', 'Beban Perlengkapan', 'beban', 'debit', 0, 317, NULL, 1, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(323, '526', 'Beban Penyusutan', 'beban', 'debit', 0, 317, NULL, 1, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(324, '53', 'Beban Lain-lain', 'beban', 'debit', 1, 315, NULL, 1, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(325, '531', 'Beban Lain-lain', 'beban', 'debit', 0, 324, NULL, 1, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(326, '1', 'ASET', 'aset', 'debit', 1, NULL, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(327, '11', 'Aset Lancar', 'aset', 'debit', 1, 326, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(328, '111', 'Kas', 'aset', 'debit', 0, 327, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(329, '112', 'Bank', 'aset', 'debit', 0, 327, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(330, '113', 'Piutang Dagang', 'aset', 'debit', 0, 327, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(331, '114', 'Persediaan Bahan Baku', 'aset', 'debit', 0, 327, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(332, '115', 'Persediaan Barang Dagang', 'aset', 'debit', 0, 327, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(333, '12', 'Aset Tetap', 'aset', 'debit', 1, 326, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(334, '121', 'Peralatan', 'aset', 'debit', 0, 333, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(335, '122', 'Akomulasi Penyusutan', 'aset', 'kredit', 0, 333, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(336, '2', 'KEWAJIBAN', 'kewajiban', 'kredit', 1, NULL, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(337, '21', 'Kewajiban Lancar', 'kewajiban', 'kredit', 1, 336, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(338, '211', 'Utang Usaha', 'kewajiban', 'kredit', 0, 337, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(339, '212', 'Utang PPN Keluaran', 'kewajiban', 'kredit', 0, 337, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(340, '213', 'PPN Masukan', 'kewajiban', 'kredit', 0, 337, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(341, '3', 'MODAL', 'modal', 'kredit', 1, NULL, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(342, '31', 'Modal', 'modal', 'kredit', 0, 341, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(343, '32', 'Laba Ditahan', 'modal', 'kredit', 0, 341, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(344, '33', 'Laba / Rugi Berjalan', 'modal', 'kredit', 0, 341, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(345, '4', 'PENDAPATAN', 'pendapatan', 'kredit', 1, NULL, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(346, '41', 'Pendapatan Usaha', 'pendapatan', 'kredit', 1, 345, NULL, 1, '2026-09-16 20:28:22', '2026-09-16 20:28:22', 10),
(347, '411', 'Penjualan Barang', 'pendapatan', 'kredit', 0, 346, NULL, 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(348, '412', 'Pendapatan Jasa', 'pendapatan', 'kredit', 0, 346, NULL, 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(349, '42', 'Pendapatan Lain', 'pendapatan', 'kredit', 1, 345, NULL, 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(350, '421', 'Pendapatan Lain-lain', 'pendapatan', 'kredit', 0, 349, NULL, 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(351, '5', 'BEBAN', 'beban', 'debit', 1, NULL, NULL, 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(352, '51', 'Harga Pokok Penjualan', 'beban', 'debit', 0, 351, NULL, 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(353, '52', 'Beban Operasional', 'beban', 'debit', 1, 351, NULL, 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(354, '521', 'Beban Gaji', 'beban', 'debit', 0, 353, NULL, 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(355, '522', 'Beban Sewa', 'beban', 'debit', 0, 353, NULL, 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(356, '523', 'Beban Listrik, Air & Telepon', 'beban', 'debit', 0, 353, NULL, 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(357, '524', 'Beban Transportasi', 'beban', 'debit', 0, 353, NULL, 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(358, '525', 'Beban Perlengkapan', 'beban', 'debit', 0, 353, NULL, 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(359, '526', 'Beban Penyusutan', 'beban', 'debit', 0, 353, NULL, 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(360, '53', 'Beban Lain-lain', 'beban', 'debit', 1, 351, NULL, 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(361, '531', 'Beban Lain-lain', 'beban', 'debit', 0, 360, NULL, 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(362, '1', 'ASET', 'aset', 'debit', 1, NULL, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(363, '11', 'Aset Lancar', 'aset', 'debit', 1, 362, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(364, '111', 'Kas', 'aset', 'debit', 0, 363, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(365, '112', 'Bank', 'aset', 'debit', 0, 363, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(366, '113', 'Piutang Dagang', 'aset', 'debit', 0, 363, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(367, '114', 'Persediaan Bahan Baku', 'aset', 'debit', 0, 363, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(368, '115', 'Persediaan Barang Dagang', 'aset', 'debit', 0, 363, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(369, '12', 'Aset Tetap', 'aset', 'debit', 1, 362, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(370, '121', 'Peralatan', 'aset', 'debit', 0, 369, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(371, '122', 'Akomulasi Penyusutan', 'aset', 'kredit', 0, 369, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(372, '2', 'KEWAJIBAN', 'kewajiban', 'kredit', 1, NULL, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(373, '21', 'Kewajiban Lancar', 'kewajiban', 'kredit', 1, 372, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(374, '211', 'Utang Usaha', 'kewajiban', 'kredit', 0, 373, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(375, '212', 'Utang PPN Keluaran', 'kewajiban', 'kredit', 0, 373, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(376, '213', 'PPN Masukan', 'kewajiban', 'kredit', 0, 373, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(377, '3', 'MODAL', 'modal', 'kredit', 1, NULL, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(378, '31', 'Modal', 'modal', 'kredit', 0, 377, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(379, '32', 'Laba Ditahan', 'modal', 'kredit', 0, 377, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(380, '33', 'Laba / Rugi Berjalan', 'modal', 'kredit', 0, 377, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(381, '4', 'PENDAPATAN', 'pendapatan', 'kredit', 1, NULL, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(382, '41', 'Pendapatan Usaha', 'pendapatan', 'kredit', 1, 381, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(383, '411', 'Penjualan Barang', 'pendapatan', 'kredit', 0, 382, NULL, 1, '2026-09-17 02:52:16', '2026-09-17 02:52:16', 11),
(384, '412', 'Pendapatan Jasa', 'pendapatan', 'kredit', 0, 382, NULL, 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(385, '42', 'Pendapatan Lain', 'pendapatan', 'kredit', 1, 381, NULL, 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(386, '421', 'Pendapatan Lain-lain', 'pendapatan', 'kredit', 0, 385, NULL, 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(387, '5', 'BEBAN', 'beban', 'debit', 1, NULL, NULL, 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(388, '51', 'Harga Pokok Penjualan', 'beban', 'debit', 0, 387, NULL, 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(389, '52', 'Beban Operasional', 'beban', 'debit', 1, 387, NULL, 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(390, '521', 'Beban Gaji', 'beban', 'debit', 0, 389, NULL, 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(391, '522', 'Beban Sewa', 'beban', 'debit', 0, 389, NULL, 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(392, '523', 'Beban Listrik, Air & Telepon', 'beban', 'debit', 0, 389, NULL, 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(393, '524', 'Beban Transportasi', 'beban', 'debit', 0, 389, NULL, 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(394, '525', 'Beban Perlengkapan', 'beban', 'debit', 0, 389, NULL, 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(395, '526', 'Beban Penyusutan', 'beban', 'debit', 0, 389, NULL, 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(396, '53', 'Beban Lain-lain', 'beban', 'debit', 1, 387, NULL, 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(397, '531', 'Beban Lain-lain', 'beban', 'debit', 0, 396, NULL, 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(398, '1', 'ASET', 'aset', 'debit', 1, NULL, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(399, '11', 'Aset Lancar', 'aset', 'debit', 1, 398, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(400, '111', 'Kas', 'aset', 'debit', 0, 399, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(401, '112', 'Bank', 'aset', 'debit', 0, 399, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(402, '113', 'Piutang Dagang', 'aset', 'debit', 0, 399, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(403, '114', 'Persediaan Bahan Baku', 'aset', 'debit', 0, 399, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(404, '115', 'Persediaan Barang Dagang', 'aset', 'debit', 0, 399, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(405, '12', 'Aset Tetap', 'aset', 'debit', 1, 398, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(406, '121', 'Peralatan', 'aset', 'debit', 0, 405, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(407, '122', 'Akomulasi Penyusutan', 'aset', 'kredit', 0, 405, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(408, '2', 'KEWAJIBAN', 'kewajiban', 'kredit', 1, NULL, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(409, '21', 'Kewajiban Lancar', 'kewajiban', 'kredit', 1, 408, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(410, '211', 'Utang Usaha', 'kewajiban', 'kredit', 0, 409, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(411, '212', 'Utang PPN Keluaran', 'kewajiban', 'kredit', 0, 409, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(412, '213', 'PPN Masukan', 'kewajiban', 'kredit', 0, 409, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(413, '3', 'MODAL', 'modal', 'kredit', 1, NULL, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(414, '31', 'Modal', 'modal', 'kredit', 0, 413, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(415, '32', 'Laba Ditahan', 'modal', 'kredit', 0, 413, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(416, '33', 'Laba / Rugi Berjalan', 'modal', 'kredit', 0, 413, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(417, '4', 'PENDAPATAN', 'pendapatan', 'kredit', 1, NULL, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(418, '41', 'Pendapatan Usaha', 'pendapatan', 'kredit', 1, 417, NULL, 1, '2026-09-17 04:06:12', '2026-09-17 04:06:12', 12),
(419, '411', 'Penjualan Barang', 'pendapatan', 'kredit', 0, 418, NULL, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(420, '412', 'Pendapatan Jasa', 'pendapatan', 'kredit', 0, 418, NULL, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(421, '42', 'Pendapatan Lain', 'pendapatan', 'kredit', 1, 417, NULL, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(422, '421', 'Pendapatan Lain-lain', 'pendapatan', 'kredit', 0, 421, NULL, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(423, '5', 'BEBAN', 'beban', 'debit', 1, NULL, NULL, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(424, '51', 'Harga Pokok Penjualan', 'beban', 'debit', 0, 423, NULL, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(425, '52', 'Beban Operasional', 'beban', 'debit', 1, 423, NULL, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(426, '521', 'Beban Gaji', 'beban', 'debit', 0, 425, NULL, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(427, '522', 'Beban Sewa', 'beban', 'debit', 0, 425, NULL, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(428, '523', 'Beban Listrik, Air & Telepon', 'beban', 'debit', 0, 425, NULL, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(429, '524', 'Beban Transportasi', 'beban', 'debit', 0, 425, NULL, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(430, '525', 'Beban Perlengkapan', 'beban', 'debit', 0, 425, NULL, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(431, '526', 'Beban Penyusutan', 'beban', 'debit', 0, 425, NULL, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(432, '53', 'Beban Lain-lain', 'beban', 'debit', 1, 423, NULL, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(433, '531', 'Beban Lain-lain', 'beban', 'debit', 0, 432, NULL, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(434, '1111', 'Kas Kas Umun', 'aset', 'debit', 0, 111, 'Dibuat otomatis untuk rekening Kas Umun', 1, '2026-09-18 20:03:10', '2026-09-18 20:03:10', 4);
INSERT INTO `akun_perkiraan` (`id`, `kode`, `nama`, `jenis`, `saldo_normal`, `is_header`, `parent_id`, `keterangan`, `is_aktif`, `created_at`, `updated_at`, `user_id`) VALUES
(435, '1112', 'Bank MANDIRI', 'aset', 'debit', 0, 111, 'Dibuat otomatis untuk rekening MANDIRI', 1, '2026-09-18 20:03:10', '2026-09-18 20:03:10', 4),
(436, '1121', 'Bank BRI', 'aset', 'debit', 0, 39, 'Dibuat otomatis untuk rekening BRI', 1, '2026-09-18 20:03:10', '2026-09-18 20:03:10', 2),
(437, '1122', 'Bank BRI 2', 'aset', 'debit', 0, 39, 'Dibuat otomatis untuk rekening BRI 2', 1, '2026-09-18 20:03:10', '2026-09-18 20:03:10', 2),
(439, '1', 'ASET', 'aset', 'debit', 1, NULL, NULL, 1, '2026-09-19 03:49:28', '2026-09-19 03:49:28', 13),
(440, '11', 'Aset Lancar', 'aset', 'debit', 1, 439, NULL, 1, '2026-09-19 03:49:28', '2026-09-19 03:49:28', 13),
(441, '111', 'Kas', 'aset', 'debit', 0, 440, NULL, 1, '2026-09-19 03:49:28', '2026-09-19 03:49:28', 13),
(442, '112', 'Bank', 'aset', 'debit', 0, 440, NULL, 1, '2026-09-19 03:49:28', '2026-09-19 03:49:28', 13),
(443, '113', 'Piutang Dagang', 'aset', 'debit', 0, 440, NULL, 1, '2026-09-19 03:49:28', '2026-09-19 03:49:28', 13),
(444, '114', 'Persediaan Bahan Baku', 'aset', 'debit', 0, 440, NULL, 1, '2026-09-19 03:49:28', '2026-09-19 03:49:28', 13),
(445, '115', 'Persediaan Barang Dagang', 'aset', 'debit', 0, 440, NULL, 1, '2026-09-19 03:49:28', '2026-09-19 03:49:28', 13),
(446, '12', 'Aset Tetap', 'aset', 'debit', 1, 439, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(447, '121', 'Peralatan', 'aset', 'debit', 0, 446, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(448, '122', 'Akomulasi Penyusutan', 'aset', 'kredit', 0, 446, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(449, '2', 'KEWAJIBAN', 'kewajiban', 'kredit', 1, NULL, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(450, '21', 'Kewajiban Lancar', 'kewajiban', 'kredit', 1, 449, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(451, '211', 'Utang Usaha', 'kewajiban', 'kredit', 0, 450, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(452, '212', 'Utang PPN Keluaran', 'kewajiban', 'kredit', 0, 450, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(453, '213', 'PPN Masukan', 'kewajiban', 'kredit', 0, 450, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(454, '3', 'MODAL', 'modal', 'kredit', 1, NULL, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(455, '31', 'Modal', 'modal', 'kredit', 0, 454, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(456, '32', 'Laba Ditahan', 'modal', 'kredit', 0, 454, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(457, '33', 'Laba / Rugi Berjalan', 'modal', 'kredit', 0, 454, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(458, '4', 'PENDAPATAN', 'pendapatan', 'kredit', 1, NULL, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(459, '41', 'Pendapatan Usaha', 'pendapatan', 'kredit', 1, 458, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(460, '411', 'Penjualan Barang', 'pendapatan', 'kredit', 0, 459, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(461, '412', 'Pendapatan Jasa', 'pendapatan', 'kredit', 0, 459, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(462, '42', 'Pendapatan Lain', 'pendapatan', 'kredit', 1, 458, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(463, '421', 'Pendapatan Lain-lain', 'pendapatan', 'kredit', 0, 462, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(464, '5', 'BEBAN', 'beban', 'debit', 1, NULL, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(465, '51', 'Harga Pokok Penjualan', 'beban', 'debit', 0, 464, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(466, '52', 'Beban Operasional', 'beban', 'debit', 1, 464, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(467, '521', 'Beban Gaji', 'beban', 'debit', 0, 466, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(468, '522', 'Beban Sewa', 'beban', 'debit', 0, 466, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(469, '523', 'Beban Listrik, Air & Telepon', 'beban', 'debit', 0, 466, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(470, '524', 'Beban Transportasi', 'beban', 'debit', 0, 466, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(471, '525', 'Beban Perlengkapan', 'beban', 'debit', 0, 466, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(472, '526', 'Beban Penyusutan', 'beban', 'debit', 0, 466, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(473, '53', 'Beban Lain-lain', 'beban', 'debit', 1, 464, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(474, '531', 'Beban Lain-lain', 'beban', 'debit', 0, 473, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(475, '1', 'ASET', 'aset', 'debit', 1, NULL, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(476, '11', 'Aset Lancar', 'aset', 'debit', 1, 475, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(477, '111', 'Kas', 'aset', 'debit', 0, 476, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(478, '112', 'Bank', 'aset', 'debit', 0, 476, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(479, '113', 'Piutang Dagang', 'aset', 'debit', 0, 476, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(480, '114', 'Persediaan Bahan Baku', 'aset', 'debit', 0, 476, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(481, '115', 'Persediaan Barang Dagang', 'aset', 'debit', 0, 476, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(482, '12', 'Aset Tetap', 'aset', 'debit', 1, 475, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(483, '121', 'Peralatan', 'aset', 'debit', 0, 482, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(484, '122', 'Akomulasi Penyusutan', 'aset', 'kredit', 0, 482, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(485, '2', 'KEWAJIBAN', 'kewajiban', 'kredit', 1, NULL, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(486, '21', 'Kewajiban Lancar', 'kewajiban', 'kredit', 1, 485, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(487, '211', 'Utang Usaha', 'kewajiban', 'kredit', 0, 486, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(488, '212', 'Utang PPN Keluaran', 'kewajiban', 'kredit', 0, 486, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(489, '213', 'PPN Masukan', 'kewajiban', 'kredit', 0, 486, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(490, '3', 'MODAL', 'modal', 'kredit', 1, NULL, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(491, '31', 'Modal', 'modal', 'kredit', 0, 490, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(492, '32', 'Laba Ditahan', 'modal', 'kredit', 0, 490, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(493, '33', 'Laba / Rugi Berjalan', 'modal', 'kredit', 0, 490, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(494, '4', 'PENDAPATAN', 'pendapatan', 'kredit', 1, NULL, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(495, '41', 'Pendapatan Usaha', 'pendapatan', 'kredit', 1, 494, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(496, '411', 'Penjualan Barang', 'pendapatan', 'kredit', 0, 495, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(497, '412', 'Pendapatan Jasa', 'pendapatan', 'kredit', 0, 495, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(498, '42', 'Pendapatan Lain', 'pendapatan', 'kredit', 1, 494, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(499, '421', 'Pendapatan Lain-lain', 'pendapatan', 'kredit', 0, 498, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(500, '5', 'BEBAN', 'beban', 'debit', 1, NULL, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(501, '51', 'Harga Pokok Penjualan', 'beban', 'debit', 0, 500, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(502, '52', 'Beban Operasional', 'beban', 'debit', 1, 500, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(503, '521', 'Beban Gaji', 'beban', 'debit', 0, 502, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(504, '522', 'Beban Sewa', 'beban', 'debit', 0, 502, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(505, '523', 'Beban Listrik, Air & Telepon', 'beban', 'debit', 0, 502, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(506, '524', 'Beban Transportasi', 'beban', 'debit', 0, 502, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(507, '525', 'Beban Perlengkapan', 'beban', 'debit', 0, 502, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(508, '526', 'Beban Penyusutan', 'beban', 'debit', 0, 502, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(509, '53', 'Beban Lain-lain', 'beban', 'debit', 1, 500, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(510, '531', 'Beban Lain-lain', 'beban', 'debit', 0, 509, NULL, 1, '2026-09-20 07:24:07', '2026-09-20 07:24:07', 14),
(511, '1111', 'Kas UTAMA', 'aset', 'debit', 0, 476, 'Dibuat otomatis untuk rekening UTAMA', 1, '2026-09-20 07:27:00', '2026-09-20 07:27:00', 14);

-- --------------------------------------------------------

--
-- Table structure for table `asets`
--

CREATE TABLE `asets` (
  `id` bigint UNSIGNED NOT NULL,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `lokasi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_perolehan` date NOT NULL,
  `harga_perolehan` decimal(18,2) NOT NULL,
  `nilai_residu` decimal(18,2) NOT NULL DEFAULT '0.00',
  `masa_manfaat_bulan` smallint UNSIGNED NOT NULL,
  `akun_aset_id` bigint UNSIGNED NOT NULL,
  `akun_akumulasi_id` bigint UNSIGNED NOT NULL,
  `akun_beban_id` bigint UNSIGNED NOT NULL,
  `sumber_dana_id` bigint UNSIGNED DEFAULT NULL,
  `rekening_id` bigint UNSIGNED DEFAULT NULL,
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `catat_perolehan` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('aktif','nonaktif','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `disposisi_alasan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disposisi_harga_jual` decimal(18,2) DEFAULT NULL,
  `disposisi_laba` decimal(18,2) DEFAULT NULL,
  `disposisi_tanggal` date DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_templates`
--

CREATE TABLE `asset_templates` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_kategori` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `masa_manfaat_bulan` smallint UNSIGNED NOT NULL,
  `persen_residu` decimal(5,2) NOT NULL DEFAULT '0.00',
  `akun_aset_id` bigint UNSIGNED DEFAULT NULL,
  `akun_akumulasi_id` bigint UNSIGNED DEFAULT NULL,
  `akun_beban_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asset_templates`
--

INSERT INTO `asset_templates` (`id`, `nama_kategori`, `masa_manfaat_bulan`, `persen_residu`, `akun_aset_id`, `akun_akumulasi_id`, `akun_beban_id`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'Peralatan Kantor', 60, 0.00, NULL, NULL, 1, '2026-09-15 01:26:26', '2026-09-15 01:26:26', NULL),
(2, 'Laptop/Komputer', 48, 10.00, NULL, NULL, 1, '2026-09-15 01:26:26', '2026-09-15 01:26:26', NULL),
(3, 'Kendaraan', 96, 20.00, NULL, NULL, 1, '2026-09-15 01:26:26', '2026-09-15 01:26:26', NULL),
(4, 'Mesin Produksi', 60, 0.00, NULL, NULL, 1, '2026-09-15 01:26:26', '2026-09-15 01:26:26', NULL),
(5, 'Inventaris', 60, 0.00, NULL, NULL, 1, '2026-09-15 01:26:26', '2026-09-15 01:26:26', NULL),
(6, 'Peralatan Kantor', 60, 0.00, 10, 11, 35, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(7, 'Laptop/Komputer', 48, 10.00, 10, 11, 35, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(8, 'Kendaraan', 96, 20.00, 10, 11, 35, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(9, 'Mesin Produksi', 60, 0.00, 10, 11, 35, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(10, 'Inventaris', 60, 0.00, 10, 11, 35, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(11, 'Peralatan Kantor', 60, 0.00, 46, 47, 71, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(12, 'Laptop/Komputer', 48, 10.00, 46, 47, 71, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(13, 'Kendaraan', 96, 20.00, 46, 47, 71, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(14, 'Mesin Produksi', 60, 0.00, 46, 47, 71, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(15, 'Inventaris', 60, 0.00, 46, 47, 71, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(16, 'Peralatan Kantor', 60, 0.00, 82, 83, 107, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(17, 'Laptop/Komputer', 48, 10.00, 82, 83, 107, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(18, 'Kendaraan', 96, 20.00, 82, 83, 107, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(19, 'Mesin Produksi', 60, 0.00, 82, 83, 107, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(20, 'Inventaris', 60, 0.00, 82, 83, 107, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(21, 'Peralatan Kantor', 60, 0.00, 118, 119, 143, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(22, 'Laptop/Komputer', 48, 10.00, 118, 119, 143, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(23, 'Kendaraan', 96, 20.00, 118, 119, 143, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(24, 'Mesin Produksi', 60, 0.00, 118, 119, 143, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(25, 'Inventaris', 60, 0.00, 118, 119, 143, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(26, 'Peralatan Kantor', 60, 0.00, 154, 155, 179, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(27, 'Laptop/Komputer', 48, 10.00, 154, 155, 179, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(28, 'Kendaraan', 96, 20.00, 154, 155, 179, '2026-09-15 18:53:00', '2026-09-15 18:53:00', 5),
(29, 'Mesin Produksi', 60, 0.00, 154, 155, 179, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 5),
(30, 'Inventaris', 60, 0.00, 154, 155, 179, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 5),
(31, 'Peralatan Kantor', 60, 0.00, 190, 191, 215, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(32, 'Laptop/Komputer', 48, 10.00, 190, 191, 215, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(33, 'Kendaraan', 96, 20.00, 190, 191, 215, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(34, 'Mesin Produksi', 60, 0.00, 190, 191, 215, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(35, 'Inventaris', 60, 0.00, 190, 191, 215, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(36, 'Peralatan Kantor', 60, 0.00, 226, 227, 251, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(37, 'Laptop/Komputer', 48, 10.00, 226, 227, 251, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(38, 'Kendaraan', 96, 20.00, 226, 227, 251, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(39, 'Mesin Produksi', 60, 0.00, 226, 227, 251, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(40, 'Inventaris', 60, 0.00, 226, 227, 251, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(41, 'Peralatan Kantor', 60, 0.00, 262, 263, 287, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 8),
(42, 'Laptop/Komputer', 48, 10.00, 262, 263, 287, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 8),
(43, 'Kendaraan', 96, 20.00, 262, 263, 287, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 8),
(44, 'Mesin Produksi', 60, 0.00, 262, 263, 287, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 8),
(45, 'Inventaris', 60, 0.00, 262, 263, 287, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 8),
(46, 'Peralatan Kantor', 60, 0.00, 298, 299, 323, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(47, 'Laptop/Komputer', 48, 10.00, 298, 299, 323, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(48, 'Kendaraan', 96, 20.00, 298, 299, 323, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(49, 'Mesin Produksi', 60, 0.00, 298, 299, 323, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(50, 'Inventaris', 60, 0.00, 298, 299, 323, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(51, 'Peralatan Kantor', 60, 0.00, 334, 335, 359, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(52, 'Laptop/Komputer', 48, 10.00, 334, 335, 359, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(53, 'Kendaraan', 96, 20.00, 334, 335, 359, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(54, 'Mesin Produksi', 60, 0.00, 334, 335, 359, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(55, 'Inventaris', 60, 0.00, 334, 335, 359, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(56, 'Peralatan Kantor', 60, 0.00, 370, 371, 395, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(57, 'Laptop/Komputer', 48, 10.00, 370, 371, 395, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(58, 'Kendaraan', 96, 20.00, 370, 371, 395, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(59, 'Mesin Produksi', 60, 0.00, 370, 371, 395, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(60, 'Inventaris', 60, 0.00, 370, 371, 395, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(61, 'Peralatan Kantor', 60, 0.00, 406, 407, 431, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(62, 'Laptop/Komputer', 48, 10.00, 406, 407, 431, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(63, 'Kendaraan', 96, 20.00, 406, 407, 431, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(64, 'Mesin Produksi', 60, 0.00, 406, 407, 431, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(65, 'Inventaris', 60, 0.00, 406, 407, 431, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(66, 'Peralatan Kantor', 60, 0.00, 447, 448, 472, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(67, 'Laptop/Komputer', 48, 10.00, 447, 448, 472, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(68, 'Kendaraan', 96, 20.00, 447, 448, 472, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(69, 'Mesin Produksi', 60, 0.00, 447, 448, 472, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(70, 'Inventaris', 60, 0.00, 447, 448, 472, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(71, 'Peralatan Kantor', 60, 0.00, 483, 484, 508, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 14),
(72, 'Laptop/Komputer', 48, 10.00, 483, 484, 508, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 14),
(73, 'Kendaraan', 96, 20.00, 483, 484, 508, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 14),
(74, 'Mesin Produksi', 60, 0.00, 483, 484, 508, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 14),
(75, 'Inventaris', 60, 0.00, 483, 484, 508, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 14);

-- --------------------------------------------------------

--
-- Table structure for table `audit_trails`
--

CREATE TABLE `audit_trails` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `auditable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_id` bigint UNSIGNED NOT NULL,
  `action` enum('created','updated','deleted','posted','voided','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'updated',
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE `banks` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_rekening` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_pemilik` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `akun_id` bigint UNSIGNED NOT NULL,
  `saldo_awal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `is_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id` bigint UNSIGNED NOT NULL,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kategori` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `merek` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ukuran` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warna` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipe` enum('barang','jasa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'barang',
  `gudang_id` bigint UNSIGNED DEFAULT NULL,
  `stok` decimal(18,2) NOT NULL DEFAULT '0.00',
  `harga_avg` decimal(18,2) NOT NULL DEFAULT '0.00',
  `harga_beli` decimal(18,2) NOT NULL DEFAULT '0.00',
  `harga_jual` decimal(18,2) NOT NULL DEFAULT '0.00',
  `min_stok` decimal(18,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `barcode` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id`, `kode`, `nama`, `foto`, `satuan`, `kategori`, `merek`, `ukuran`, `warna`, `tipe`, `gudang_id`, `stok`, `harga_avg`, `harga_beli`, `harga_jual`, `min_stok`, `keterangan`, `barcode`, `is_aktif`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'BRG-0001', 'Mie Sedap Goreng', 'barang/9NZES6cMP2SlOX6HMP9VTDc79OVPSAXPl3EF2mN1.jpg', 'pcs', 'Makanan', NULL, NULL, 'merah', 'barang', 3, 0.50, 2000.00, 2000.00, 2500.00, 10.00, NULL, '8991906106311', 1, '2026-09-15 21:09:01', '2026-09-15 21:09:01', 2),
(2, 'BRG-0001', 'Fleece Hitam', NULL, 'Kg', 'Fleece', NULL, NULL, NULL, 'barang', 7, 0.00, 62500.00, 62500.00, 66500.00, 25.00, NULL, NULL, 1, '2026-09-15 21:52:08', '2026-09-15 21:52:08', 6),
(3, 'BRG-0002', 'Gula Pasir 50kg', NULL, 'pcs', 'Makanan', NULL, NULL, NULL, 'barang', 3, 220.00, 3000.00, 3000.00, 5000.00, 5.00, NULL, NULL, 1, '2026-09-16 20:26:04', '2026-09-18 19:15:05', 2),
(4, 'BRG-0001', 'Tepung 3kg', 'barang/N0zW9CkvxlWejItJg4GM7Ia6O0n4pzPkD6HzOfO1.jpg', 'Kg', 'Bahan', NULL, NULL, NULL, 'barang', 10, 18.00, 8000.00, 8000.00, 15000.00, 10.00, 'Tepung terigu', NULL, 1, '2026-09-16 20:28:42', '2026-09-16 20:31:37', 9),
(5, 'BRG-0001', 'Fleece Hitam', NULL, 'Kg', 'Fleece', NULL, NULL, NULL, 'barang', 9, 49.12, 60000.00, 60000.00, 67000.00, 25.00, NULL, NULL, 1, '2026-09-16 21:45:12', '2026-09-18 05:45:41', 8),
(6, 'BRG-0006', 'Fleece Navy', NULL, 'Kg', 'Fleece', NULL, NULL, NULL, 'barang', 9, 0.00, 60000.00, 60000.00, 67000.00, 12.00, NULL, NULL, 1, '2026-09-18 05:02:58', '2026-09-18 05:02:58', 8),
(7, 'BRG-0007', 'Fleece Maron', NULL, 'Kg', 'Fleece', NULL, NULL, NULL, 'barang', 9, 0.00, 60000.00, 60000.00, 67000.00, 12.00, NULL, NULL, 1, '2026-09-18 05:03:28', '2026-09-18 05:03:28', 8),
(8, 'BRG-0008', 'Fleece Hijau Botol', NULL, 'Kg', 'Fleece', NULL, NULL, NULL, 'barang', 9, 24.84, 60000.00, 60000.00, 67000.00, 12.00, NULL, NULL, 1, '2026-09-18 05:03:58', '2026-09-18 05:45:41', 8),
(9, 'BRG-0009', 'Fleece Cream', NULL, 'Kg', 'Fleece', NULL, NULL, NULL, 'barang', 9, 25.19, 60000.00, 60000.00, 67000.00, 12.00, NULL, NULL, 1, '2026-09-18 05:04:21', '2026-09-18 05:45:41', 8),
(10, 'BRG-0010', 'Fleece Abu Misty', NULL, 'Kg', 'Fleece', NULL, NULL, NULL, 'barang', 9, 25.29, 60000.00, 60000.00, 67000.00, 12.00, NULL, NULL, 1, '2026-09-18 05:04:47', '2026-09-18 05:45:41', 8),
(11, 'BRG-0011', 'Fleece Putih', NULL, 'Kg', 'Fleece', NULL, NULL, NULL, 'barang', 9, 0.00, 60000.00, 60000.00, 67000.00, 12.00, NULL, NULL, 1, '2026-09-18 05:05:12', '2026-09-18 05:05:12', 8),
(12, 'BRG-0012', 'Fleece Mint', NULL, 'Kg', 'Fleece', NULL, NULL, NULL, 'barang', 9, 0.00, 60000.00, 60000.00, 67000.00, 12.00, NULL, NULL, 1, '2026-09-18 05:05:43', '2026-09-18 05:05:43', 8),
(13, 'BRG-0013', 'Fleece Mustard', NULL, 'Kg', 'Fleece', NULL, NULL, NULL, 'barang', 9, 0.00, 60000.00, 60000.00, 67000.00, 12.00, NULL, NULL, 1, '2026-09-18 05:06:12', '2026-09-18 05:06:12', 8),
(14, 'BRG-0014', 'Fleece Denim', NULL, 'Kg', 'Fleece', NULL, NULL, NULL, 'barang', 9, 24.21, 60000.00, 60000.00, 67000.00, 12.00, NULL, NULL, 1, '2026-09-18 05:06:35', '2026-09-18 05:45:41', 8),
(15, 'BRG-0015', 'Fleece Beige', NULL, 'Kg', 'Fleece', NULL, NULL, NULL, 'barang', 9, 25.00, 60000.00, 60000.00, 67000.00, 12.00, NULL, NULL, 1, '2026-09-18 05:07:01', '2026-09-18 05:45:41', 8),
(16, 'BRG-0016', 'Rib Hitam', NULL, 'Kg', 'Rib', NULL, NULL, NULL, 'barang', 9, 11.40, 60500.00, 60500.00, 67000.00, 3.00, NULL, NULL, 1, '2026-09-18 05:22:42', '2026-09-18 05:45:41', 8),
(17, 'BRG-0017', 'Rib Navy', NULL, 'Kg', 'Rib', NULL, NULL, NULL, 'barang', 9, 0.00, 60500.00, 60500.00, 67000.00, 3.00, NULL, NULL, 1, '2026-09-18 05:23:50', '2026-09-18 05:23:50', 8),
(18, 'BRG-0018', 'Rib Maron', NULL, 'Kg', 'Rib', NULL, NULL, NULL, 'barang', 9, 0.00, 60500.00, 60500.00, 67000.00, 3.00, NULL, NULL, 1, '2026-09-18 05:24:35', '2026-09-18 05:24:35', 8),
(19, 'BRG-0019', 'Rib Hijau Botol', NULL, 'Kg', 'Rib', NULL, NULL, NULL, 'barang', 9, 5.05, 60500.00, 60500.00, 67000.00, 3.00, NULL, NULL, 1, '2026-09-18 05:25:06', '2026-09-18 05:45:41', 8),
(20, 'BRG-0020', 'Rib Cream', NULL, 'Kg', 'Rib', NULL, NULL, NULL, 'barang', 9, 5.00, 60500.00, 60500.00, 67000.00, 3.00, NULL, NULL, 1, '2026-09-18 05:25:35', '2026-09-18 05:45:41', 8),
(21, 'BRG-0021', 'Rib Abu Misty', NULL, 'Kg', 'Rib', NULL, NULL, NULL, 'barang', 9, 5.10, 60500.00, 60500.00, 67000.00, 3.00, NULL, NULL, 1, '2026-09-18 05:26:55', '2026-09-18 05:45:41', 8),
(22, 'BRG-0022', 'Rib Putih', NULL, 'Kg', 'Rib', NULL, NULL, NULL, 'barang', 9, 0.00, 60500.00, 60500.00, 67000.00, 3.00, NULL, NULL, 1, '2026-09-18 05:28:25', '2026-09-18 05:28:25', 8),
(23, 'BRG-0023', 'Rib Mint', NULL, 'Kg', 'Rib', NULL, NULL, NULL, 'barang', 9, 0.00, 60500.00, 60500.00, 67000.00, 3.00, NULL, NULL, 1, '2026-09-18 05:28:47', '2026-09-18 05:28:47', 8),
(24, 'BRG-0024', 'Rib Denim', NULL, 'Kg', 'Rib', NULL, NULL, NULL, 'barang', 9, 4.00, 60500.00, 60500.00, 67000.00, 3.00, NULL, NULL, 1, '2026-09-18 05:29:10', '2026-09-18 05:45:41', 8),
(25, 'BRG-0025', 'Rib Beige', NULL, 'Kg', 'Rib', NULL, NULL, NULL, 'barang', 9, 5.00, 60500.00, 60500.00, 67000.00, 3.00, NULL, NULL, 1, '2026-09-18 05:29:31', '2026-09-18 05:45:41', 8),
(26, 'BRG-0026', 'Fleece Merah', NULL, 'Kg', 'Fleece', NULL, NULL, NULL, 'barang', 9, 10.05, 61500.00, 60000.00, 67000.00, 5.00, NULL, NULL, 1, '2026-09-18 05:30:13', '2026-09-18 05:45:41', 8),
(27, 'BRG-0001', 'Tepung', NULL, 'Pcs', 'Makanan', NULL, NULL, NULL, 'barang', 5, 100.00, 2000.00, 2000.00, 3500.00, 10.00, NULL, NULL, 1, '2026-09-18 06:21:04', '2026-09-18 06:21:39', 4),
(28, 'BRG-0001', 'B1', NULL, 'PCS', 'UMUM', 'UNIQLO', NULL, 'biru dodger', 'barang', 16, 99.00, 994.95, 1000.00, 2000.00, 10.00, 'asdASdasdasd', NULL, 1, '2026-09-20 07:25:48', '2026-09-20 07:57:25', 14);

-- --------------------------------------------------------

--
-- Table structure for table `bb_hutang`
--

CREATE TABLE `bb_hutang` (
  `id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `jurnal_id` bigint UNSIGNED DEFAULT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `debit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `kredit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `saldo` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pembelian_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bb_persediaan`
--

CREATE TABLE `bb_persediaan` (
  `id` bigint UNSIGNED NOT NULL,
  `ref_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref_id` bigint UNSIGNED DEFAULT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `masuk_qty` decimal(18,2) NOT NULL DEFAULT '0.00',
  `masuk_harga` decimal(18,2) NOT NULL DEFAULT '0.00',
  `keluar_qty` decimal(18,2) NOT NULL DEFAULT '0.00',
  `keluar_harga` decimal(18,2) NOT NULL DEFAULT '0.00',
  `saldo_qty` decimal(18,2) NOT NULL DEFAULT '0.00',
  `saldo_harga` decimal(18,2) NOT NULL DEFAULT '0.00',
  `ratt` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `barang_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bb_persediaan`
--

INSERT INTO `bb_persediaan` (`id`, `ref_type`, `ref_id`, `tanggal`, `keterangan`, `masuk_qty`, `masuk_harga`, `keluar_qty`, `keluar_harga`, `saldo_qty`, `saldo_harga`, `ratt`, `created_at`, `updated_at`, `barang_id`, `user_id`) VALUES
(1, NULL, NULL, '2026-09-16', 'Saldo Awal Mie Sedap Goreng', 0.50, 2000.00, 0.00, 0.00, 0.50, 1000.00, 2000.00, '2026-09-15 21:09:01', '2026-09-15 21:09:01', 1, 2),
(2, NULL, NULL, '2026-09-17', 'Saldo Awal Gula Pasir 50kg', 10.00, 3000.00, 0.00, 0.00, 10.00, 30000.00, 3000.00, '2026-09-16 20:26:04', '2026-09-16 20:26:04', 3, 2),
(3, NULL, NULL, '2026-09-17', 'Saldo Awal Tepung 3kg', 15.00, 8000.00, 0.00, 0.00, 15.00, 120000.00, 8000.00, '2026-09-16 20:28:43', '2026-09-16 20:28:43', 4, 9),
(4, 'App\\Models\\Penjualan', 1, '2026-09-17', 'Penjualan PJ/09/2026/0001 - UMUM', 0.00, 0.00, 7.00, 8000.00, 8.00, 64000.00, 8000.00, '2026-09-16 20:30:13', '2026-09-16 20:30:13', 4, 9),
(5, 'App\\Models\\Pembelian', 1, '2026-09-17', 'Pembelian PB/09/2026/0001', 10.00, 8000.00, 0.00, 0.00, 18.00, 144000.00, 8000.00, '2026-09-16 20:31:37', '2026-09-16 20:31:37', 4, 9),
(6, 'App\\Models\\Pembelian', 2, '2026-09-18', 'Pembelian PB/09/2026/0001', 49.12, 60000.00, 0.00, 0.00, 49.12, 2947200.00, 60000.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 5, 8),
(7, 'App\\Models\\Pembelian', 2, '2026-09-18', 'Pembelian PB/09/2026/0001', 24.84, 60000.00, 0.00, 0.00, 24.84, 1490400.00, 60000.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 8, 8),
(8, 'App\\Models\\Pembelian', 2, '2026-09-18', 'Pembelian PB/09/2026/0001', 11.40, 60500.00, 0.00, 0.00, 11.40, 689700.00, 60500.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 16, 8),
(9, 'App\\Models\\Pembelian', 2, '2026-09-18', 'Pembelian PB/09/2026/0001', 5.05, 60500.00, 0.00, 0.00, 5.05, 305525.00, 60500.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 19, 8),
(10, 'App\\Models\\Pembelian', 2, '2026-09-18', 'Pembelian PB/09/2026/0001', 25.29, 60000.00, 0.00, 0.00, 25.29, 1517400.00, 60000.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 10, 8),
(11, 'App\\Models\\Pembelian', 2, '2026-09-18', 'Pembelian PB/09/2026/0001', 5.10, 60500.00, 0.00, 0.00, 5.10, 308550.00, 60500.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 21, 8),
(12, 'App\\Models\\Pembelian', 2, '2026-09-18', 'Pembelian PB/09/2026/0001', 25.00, 60000.00, 0.00, 0.00, 25.00, 1500000.00, 60000.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 15, 8),
(13, 'App\\Models\\Pembelian', 2, '2026-09-18', 'Pembelian PB/09/2026/0001', 5.00, 60500.00, 0.00, 0.00, 5.00, 302500.00, 60500.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 25, 8),
(14, 'App\\Models\\Pembelian', 2, '2026-09-18', 'Pembelian PB/09/2026/0001', 25.19, 60000.00, 0.00, 0.00, 25.19, 1511400.00, 60000.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 9, 8),
(15, 'App\\Models\\Pembelian', 2, '2026-09-18', 'Pembelian PB/09/2026/0001', 5.00, 60500.00, 0.00, 0.00, 5.00, 302500.00, 60500.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 20, 8),
(16, 'App\\Models\\Pembelian', 2, '2026-09-18', 'Pembelian PB/09/2026/0001', 24.21, 60000.00, 0.00, 0.00, 24.21, 1452600.00, 60000.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 14, 8),
(17, 'App\\Models\\Pembelian', 2, '2026-09-18', 'Pembelian PB/09/2026/0001', 4.00, 60500.00, 0.00, 0.00, 4.00, 242000.00, 60500.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 24, 8),
(18, 'App\\Models\\Pembelian', 2, '2026-09-18', 'Pembelian PB/09/2026/0001', 10.05, 61500.00, 0.00, 0.00, 10.05, 618075.00, 61500.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 26, 8),
(19, 'App\\Models\\Pembelian', 2, '2026-09-18', 'BATAL Pembelian PB/09/2026/0001', 0.00, 0.00, 49.12, 60000.00, 0.00, 0.00, 0.00, '2026-09-18 05:38:04', '2026-09-18 05:38:04', 5, 8),
(20, 'App\\Models\\Pembelian', 2, '2026-09-18', 'BATAL Pembelian PB/09/2026/0001', 0.00, 0.00, 24.84, 60000.00, 0.00, 0.00, 0.00, '2026-09-18 05:38:04', '2026-09-18 05:38:04', 8, 8),
(21, 'App\\Models\\Pembelian', 2, '2026-09-18', 'BATAL Pembelian PB/09/2026/0001', 0.00, 0.00, 11.40, 60500.00, 0.00, 0.00, 0.00, '2026-09-18 05:38:04', '2026-09-18 05:38:04', 16, 8),
(22, 'App\\Models\\Pembelian', 2, '2026-09-18', 'BATAL Pembelian PB/09/2026/0001', 0.00, 0.00, 5.05, 60500.00, 0.00, 0.00, 0.00, '2026-09-18 05:38:04', '2026-09-18 05:38:04', 19, 8),
(23, 'App\\Models\\Pembelian', 2, '2026-09-18', 'BATAL Pembelian PB/09/2026/0001', 0.00, 0.00, 25.29, 60000.00, 0.00, 0.00, 0.00, '2026-09-18 05:38:04', '2026-09-18 05:38:04', 10, 8),
(24, 'App\\Models\\Pembelian', 2, '2026-09-18', 'BATAL Pembelian PB/09/2026/0001', 0.00, 0.00, 5.10, 60500.00, 0.00, 0.00, 0.00, '2026-09-18 05:38:04', '2026-09-18 05:38:04', 21, 8),
(25, 'App\\Models\\Pembelian', 2, '2026-09-18', 'BATAL Pembelian PB/09/2026/0001', 0.00, 0.00, 25.00, 60000.00, 0.00, 0.00, 0.00, '2026-09-18 05:38:04', '2026-09-18 05:38:04', 15, 8),
(26, 'App\\Models\\Pembelian', 2, '2026-09-18', 'BATAL Pembelian PB/09/2026/0001', 0.00, 0.00, 5.00, 60500.00, 0.00, 0.00, 0.00, '2026-09-18 05:38:04', '2026-09-18 05:38:04', 25, 8),
(27, 'App\\Models\\Pembelian', 2, '2026-09-18', 'BATAL Pembelian PB/09/2026/0001', 0.00, 0.00, 25.19, 60000.00, 0.00, 0.00, 0.00, '2026-09-18 05:38:04', '2026-09-18 05:38:04', 9, 8),
(28, 'App\\Models\\Pembelian', 2, '2026-09-18', 'BATAL Pembelian PB/09/2026/0001', 0.00, 0.00, 5.00, 60500.00, 0.00, 0.00, 0.00, '2026-09-18 05:38:04', '2026-09-18 05:38:04', 20, 8),
(29, 'App\\Models\\Pembelian', 2, '2026-09-18', 'BATAL Pembelian PB/09/2026/0001', 0.00, 0.00, 24.21, 60000.00, 0.00, 0.00, 0.00, '2026-09-18 05:38:04', '2026-09-18 05:38:04', 14, 8),
(30, 'App\\Models\\Pembelian', 2, '2026-09-18', 'BATAL Pembelian PB/09/2026/0001', 0.00, 0.00, 4.00, 60500.00, 0.00, 0.00, 0.00, '2026-09-18 05:38:04', '2026-09-18 05:38:04', 24, 8),
(31, 'App\\Models\\Pembelian', 2, '2026-09-18', 'BATAL Pembelian PB/09/2026/0001', 0.00, 0.00, 10.05, 61500.00, 0.00, 0.00, 0.00, '2026-09-18 05:38:04', '2026-09-18 05:38:04', 26, 8),
(32, 'App\\Models\\Pembelian', 3, '2026-09-18', 'Pembelian PB/09/2026/0002', 49.12, 60000.00, 0.00, 0.00, 49.12, 2947200.00, 60000.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 5, 8),
(33, 'App\\Models\\Pembelian', 3, '2026-09-18', 'Pembelian PB/09/2026/0002', 24.84, 60000.00, 0.00, 0.00, 24.84, 1490400.00, 60000.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 8, 8),
(34, 'App\\Models\\Pembelian', 3, '2026-09-18', 'Pembelian PB/09/2026/0002', 25.29, 60000.00, 0.00, 0.00, 25.29, 1517400.00, 60000.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 10, 8),
(35, 'App\\Models\\Pembelian', 3, '2026-09-18', 'Pembelian PB/09/2026/0002', 25.00, 60000.00, 0.00, 0.00, 25.00, 1500000.00, 60000.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 15, 8),
(36, 'App\\Models\\Pembelian', 3, '2026-09-18', 'Pembelian PB/09/2026/0002', 25.19, 60000.00, 0.00, 0.00, 25.19, 1511400.00, 60000.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 9, 8),
(37, 'App\\Models\\Pembelian', 3, '2026-09-18', 'Pembelian PB/09/2026/0002', 24.21, 60000.00, 0.00, 0.00, 24.21, 1452600.00, 60000.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 14, 8),
(38, 'App\\Models\\Pembelian', 3, '2026-09-18', 'Pembelian PB/09/2026/0002', 11.40, 60500.00, 0.00, 0.00, 11.40, 689700.00, 60500.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 16, 8),
(39, 'App\\Models\\Pembelian', 3, '2026-09-18', 'Pembelian PB/09/2026/0002', 5.05, 60500.00, 0.00, 0.00, 5.05, 305525.00, 60500.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 19, 8),
(40, 'App\\Models\\Pembelian', 3, '2026-09-18', 'Pembelian PB/09/2026/0002', 5.10, 60500.00, 0.00, 0.00, 5.10, 308550.00, 60500.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 21, 8),
(41, 'App\\Models\\Pembelian', 3, '2026-09-18', 'Pembelian PB/09/2026/0002', 5.00, 60500.00, 0.00, 0.00, 5.00, 302500.00, 60500.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 25, 8),
(42, 'App\\Models\\Pembelian', 3, '2026-09-18', 'Pembelian PB/09/2026/0002', 5.00, 60500.00, 0.00, 0.00, 5.00, 302500.00, 60500.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 20, 8),
(43, 'App\\Models\\Pembelian', 3, '2026-09-18', 'Pembelian PB/09/2026/0002', 4.00, 60500.00, 0.00, 0.00, 4.00, 242000.00, 60500.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 24, 8),
(44, 'App\\Models\\Pembelian', 3, '2026-09-18', 'Pembelian PB/09/2026/0002', 10.05, 61500.00, 0.00, 0.00, 10.05, 618075.00, 61500.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 26, 8),
(45, 'App\\Models\\Pembelian', 4, '2026-09-18', 'Pembelian PB/09/2026/0001', 100.00, 2000.00, 0.00, 0.00, 100.00, 200000.00, 2000.00, '2026-09-18 06:21:39', '2026-09-18 06:21:39', 27, 4),
(46, 'App\\Models\\Pembelian', 5, '2026-09-19', 'Pembelian PB/09/2026/0001', 100.00, 3000.00, 0.00, 0.00, 110.00, 330000.00, 3000.00, '2026-09-18 19:11:54', '2026-09-18 19:11:54', 3, 2),
(47, 'App\\Models\\Pembelian', 6, '2026-09-19', 'Pembelian PB/09/2026/0002', 10.00, 3000.00, 0.00, 0.00, 120.00, 360000.00, 3000.00, '2026-09-18 19:14:45', '2026-09-18 19:14:45', 3, 2),
(48, 'App\\Models\\Pembelian', 7, '2026-09-19', 'Pembelian PB/09/2026/0003', 100.00, 3000.00, 0.00, 0.00, 220.00, 660000.00, 3000.00, '2026-09-18 19:15:05', '2026-09-18 19:15:05', 3, 2),
(49, NULL, NULL, '2026-09-20', 'Saldo Awal B1', 100.00, 1000.00, 0.00, 0.00, 100.00, 100000.00, 1000.00, '2026-09-20 07:25:49', '2026-09-20 07:25:49', 28, 14),
(50, 'App\\Models\\Pembelian', 8, '2026-09-20', 'Pembelian PB/09/2026/0001', 100.00, 1000.00, 0.00, 0.00, 200.00, 200000.00, 1000.00, '2026-09-20 07:27:41', '2026-09-20 07:27:41', 28, 14),
(51, 'App\\Models\\ReturPembelian', 1, '2026-09-20', 'Retur Pembelian RPB/09/2026/0001', 0.00, 0.00, 100.00, 1000.00, 100.00, 100000.00, 1000.00, '2026-09-20 07:28:02', '2026-09-20 07:28:02', 28, 14),
(52, 'App\\Models\\Pembelian', 9, '2026-09-20', 'Pembelian PB/09/2026/0002', 100.00, 2000.00, 0.00, 0.00, 200.00, 300000.00, 1500.00, '2026-09-20 07:29:30', '2026-09-20 07:29:30', 28, 14),
(53, 'App\\Models\\Penjualan', 2, '2026-09-20', 'Penjualan PJ/09/2026/0001 - UMUM', 0.00, 0.00, 7.00, 1500.00, 193.00, 289500.00, 1500.00, '2026-09-20 07:31:39', '2026-09-20 07:31:39', 28, 14),
(54, 'App\\Models\\Penjualan', 2, '2026-09-20', 'BATAL Penjualan PJ/09/2026/0001', 7.00, 1500.00, 0.00, 0.00, 200.00, 300000.00, 1500.00, '2026-09-20 07:31:51', '2026-09-20 07:31:51', 28, 14),
(55, 'App\\Models\\Penjualan', 3, '2026-09-20', 'Penjualan PJ/09/2026/0002 - UMUM', 0.00, 0.00, 5.00, 1500.00, 195.00, 292500.00, 1500.00, '2026-09-20 07:33:30', '2026-09-20 07:33:30', 28, 14),
(56, 'App\\Models\\ReturPenjualan', 1, '2026-09-20', 'Retur Penjualan RPJ/09/2026/0001', 5.00, 1500.00, 0.00, 0.00, 200.00, 300000.00, 1500.00, '2026-09-20 07:34:45', '2026-09-20 07:34:45', 28, 14),
(57, 'App\\Models\\PerubahanStok', 1, '2026-09-20', 'Perubahan Stok PS/09/2026/0001 (Salah)', 0.00, 0.00, 1.00, 1500.00, 199.00, 298500.00, 1500.00, '2026-09-20 07:36:28', '2026-09-20 07:36:28', 28, 14),
(61, 'App\\Models\\ReturPembelian', 3, '2026-08-20', 'Retur Pembelian RPB/08/2026/0001', 0.00, 0.00, 100.00, 2000.00, 99.00, 98500.05, 994.95, '2026-09-20 07:57:25', '2026-09-20 07:57:25', 28, 14);

-- --------------------------------------------------------

--
-- Table structure for table `bb_piutang`
--

CREATE TABLE `bb_piutang` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `jurnal_id` bigint UNSIGNED DEFAULT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `debit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `kredit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `saldo` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `penjualan_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bb_piutang`
--

INSERT INTO `bb_piutang` (`id`, `customer_id`, `jurnal_id`, `tanggal`, `keterangan`, `debit`, `kredit`, `saldo`, `created_at`, `updated_at`, `penjualan_id`, `user_id`) VALUES
(1, 15, 26, '2026-09-20', 'Penjualan PJ/09/2026/0002', 10000.00, 0.00, 10000.00, '2026-09-20 07:33:30', '2026-09-20 07:33:30', 3, 14),
(2, 15, 28, '2026-09-20', 'Pelunasan Kas Masuk KM/09/2026/0001 - Pelunasan PJ/09/2026/0002', 0.00, 2000.00, 8000.00, '2026-09-20 07:33:50', '2026-09-20 07:33:50', 3, 14),
(3, 15, NULL, '2026-09-20', 'Retur Penjualan RPJ/09/2026/0001', 0.00, 10000.00, -2000.00, '2026-09-20 07:34:45', '2026-09-20 07:34:45', 3, 14);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('kaspro-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:2;', 1790002703),
('kaspro-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1790002703;', 1790002703),
('kaspro-cache-admin@example.com|127.0.0.1', 'i:2;', 1789914141),
('kaspro-cache-admin@example.com|127.0.0.1:timer', 'i:1789914141;', 1789914141),
('kaspro-cache-asmin@admin.com|127.0.0.1', 'i:1;', 1789815006),
('kaspro-cache-asmin@admin.com|127.0.0.1:timer', 'i:1789815006;', 1789815006),
('kaspro-cache-boost:mcp:database-schema:mysql:jurnal_umum:0:0:0:1', 'a:2:{s:6:\"engine\";s:5:\"mysql\";s:6:\"tables\";a:1:{s:11:\"jurnal_umum\";a:5:{s:7:\"columns\";a:18:{s:2:\"id\";a:4:{s:4:\"type\";s:15:\"bigint unsigned\";s:8:\"nullable\";b:0;s:7:\"default\";N;s:14:\"auto_increment\";b:1;}s:5:\"nomor\";a:4:{s:4:\"type\";s:11:\"varchar(50)\";s:8:\"nullable\";b:0;s:7:\"default\";N;s:14:\"auto_increment\";b:0;}s:7:\"tanggal\";a:4:{s:4:\"type\";s:4:\"date\";s:8:\"nullable\";b:0;s:7:\"default\";N;s:14:\"auto_increment\";b:0;}s:10:\"keterangan\";a:4:{s:4:\"type\";s:4:\"text\";s:8:\"nullable\";b:1;s:7:\"default\";N;s:14:\"auto_increment\";b:0;}s:4:\"tipe\";a:4:{s:4:\"type\";s:213:\"enum(\'kas_masuk\',\'kas_keluar\',\'mutasi_bank\',\'pembelian\',\'penjualan\',\'manual\',\'tutup_buku\',\'hpp\',\'pembayaran\',\'retur_penjualan\',\'retur_pembelian\',\'penyesuaian_stok\',\'perolehan_aset\',\'penyusutan\',\'penghapusan_aset\')\";s:8:\"nullable\";b:0;s:7:\"default\";s:6:\"manual\";s:14:\"auto_increment\";b:0;}s:8:\"ref_type\";a:4:{s:4:\"type\";s:12:\"varchar(255)\";s:8:\"nullable\";b:1;s:7:\"default\";N;s:14:\"auto_increment\";b:0;}s:6:\"ref_id\";a:4:{s:4:\"type\";s:15:\"bigint unsigned\";s:8:\"nullable\";b:1;s:7:\"default\";N;s:14:\"auto_increment\";b:0;}s:9:\"is_posted\";a:4:{s:4:\"type\";s:10:\"tinyint(1)\";s:8:\"nullable\";b:0;s:7:\"default\";s:1:\"1\";s:14:\"auto_increment\";b:0;}s:10:\"created_at\";a:4:{s:4:\"type\";s:9:\"timestamp\";s:8:\"nullable\";b:1;s:7:\"default\";N;s:14:\"auto_increment\";b:0;}s:10:\"updated_at\";a:4:{s:4:\"type\";s:9:\"timestamp\";s:8:\"nullable\";b:1;s:7:\"default\";N;s:14:\"auto_increment\";b:0;}s:10:\"created_by\";a:4:{s:4:\"type\";s:15:\"bigint unsigned\";s:8:\"nullable\";b:1;s:7:\"default\";N;s:14:\"auto_increment\";b:0;}s:10:\"updated_by\";a:4:{s:4:\"type\";s:15:\"bigint unsigned\";s:8:\"nullable\";b:1;s:7:\"default\";N;s:14:\"auto_increment\";b:0;}s:11:\"approved_by\";a:4:{s:4:\"type\";s:15:\"bigint unsigned\";s:8:\"nullable\";b:1;s:7:\"default\";N;s:14:\"auto_increment\";b:0;}s:15:\"approval_status\";a:4:{s:4:\"type\";s:52:\"enum(\'draft\',\'pending_review\',\'approved\',\'rejected\')\";s:8:\"nullable\";b:0;s:7:\"default\";s:8:\"approved\";s:14:\"auto_increment\";b:0;}s:15:\"approval_reason\";a:4:{s:4:\"type\";s:4:\"text\";s:8:\"nullable\";b:1;s:7:\"default\";N;s:14:\"auto_increment\";b:0;}s:11:\"approved_at\";a:4:{s:4:\"type\";s:9:\"timestamp\";s:8:\"nullable\";b:1;s:7:\"default\";N;s:14:\"auto_increment\";b:0;}s:13:\"change_reason\";a:4:{s:4:\"type\";s:4:\"text\";s:8:\"nullable\";b:1;s:7:\"default\";N;s:14:\"auto_increment\";b:0;}s:7:\"user_id\";a:4:{s:4:\"type\";s:15:\"bigint unsigned\";s:8:\"nullable\";b:1;s:7:\"default\";N;s:14:\"auto_increment\";b:0;}}s:7:\"indexes\";a:7:{s:31:\"jurnal_umum_approved_by_foreign\";a:4:{s:7:\"columns\";a:1:{i:0;s:11:\"approved_by\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:0;s:10:\"is_primary\";b:0;}s:30:\"jurnal_umum_created_by_foreign\";a:4:{s:7:\"columns\";a:1:{i:0;s:10:\"created_by\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:0;s:10:\"is_primary\";b:0;}s:33:\"jurnal_umum_ref_type_ref_id_index\";a:4:{s:7:\"columns\";a:2:{i:0;s:8:\"ref_type\";i:1;s:6:\"ref_id\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:0;s:10:\"is_primary\";b:0;}s:30:\"jurnal_umum_updated_by_foreign\";a:4:{s:7:\"columns\";a:1:{i:0;s:10:\"updated_by\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:0;s:10:\"is_primary\";b:0;}s:25:\"jurnal_umum_user_id_index\";a:4:{s:7:\"columns\";a:1:{i:0;s:7:\"user_id\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:0;s:10:\"is_primary\";b:0;}s:32:\"jurnal_umum_user_id_nomor_unique\";a:4:{s:7:\"columns\";a:2:{i:0;s:7:\"user_id\";i:1;s:5:\"nomor\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:1;s:10:\"is_primary\";b:0;}s:7:\"primary\";a:4:{s:7:\"columns\";a:1:{i:0;s:2:\"id\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:1;s:10:\"is_primary\";b:1;}}s:12:\"foreign_keys\";a:3:{i:0;a:7:{s:4:\"name\";s:31:\"jurnal_umum_approved_by_foreign\";s:7:\"columns\";a:1:{i:0;s:11:\"approved_by\";}s:14:\"foreign_schema\";s:8:\"keuangan\";s:13:\"foreign_table\";s:5:\"users\";s:15:\"foreign_columns\";a:1:{i:0;s:2:\"id\";}s:9:\"on_update\";s:9:\"no action\";s:9:\"on_delete\";s:8:\"set null\";}i:1;a:7:{s:4:\"name\";s:30:\"jurnal_umum_created_by_foreign\";s:7:\"columns\";a:1:{i:0;s:10:\"created_by\";}s:14:\"foreign_schema\";s:8:\"keuangan\";s:13:\"foreign_table\";s:5:\"users\";s:15:\"foreign_columns\";a:1:{i:0;s:2:\"id\";}s:9:\"on_update\";s:9:\"no action\";s:9:\"on_delete\";s:8:\"set null\";}i:2;a:7:{s:4:\"name\";s:30:\"jurnal_umum_updated_by_foreign\";s:7:\"columns\";a:1:{i:0;s:10:\"updated_by\";}s:14:\"foreign_schema\";s:8:\"keuangan\";s:13:\"foreign_table\";s:5:\"users\";s:15:\"foreign_columns\";a:1:{i:0;s:2:\"id\";}s:9:\"on_update\";s:9:\"no action\";s:9:\"on_delete\";s:8:\"set null\";}}s:8:\"triggers\";a:0:{}s:17:\"check_constraints\";a:0:{}}}}', 1789789122),
('kaspro-cache-boost:mcp:database-schema:mysql:jurnal:0:0:0:0', 'a:2:{s:6:\"engine\";s:5:\"mysql\";s:6:\"tables\";a:2:{s:12:\"jurnal_items\";a:5:{s:7:\"columns\";a:9:{s:2:\"id\";a:1:{s:4:\"type\";s:15:\"bigint unsigned\";}s:9:\"jurnal_id\";a:1:{s:4:\"type\";s:15:\"bigint unsigned\";}s:7:\"akun_id\";a:1:{s:4:\"type\";s:15:\"bigint unsigned\";}s:5:\"debit\";a:1:{s:4:\"type\";s:13:\"decimal(18,2)\";}s:6:\"kredit\";a:1:{s:4:\"type\";s:13:\"decimal(18,2)\";}s:10:\"keterangan\";a:1:{s:4:\"type\";s:4:\"text\";}s:10:\"created_at\";a:1:{s:4:\"type\";s:9:\"timestamp\";}s:10:\"updated_at\";a:1:{s:4:\"type\";s:9:\"timestamp\";}s:7:\"user_id\";a:1:{s:4:\"type\";s:15:\"bigint unsigned\";}}s:7:\"indexes\";a:4:{s:28:\"jurnal_items_akun_id_foreign\";a:4:{s:7:\"columns\";a:1:{i:0;s:7:\"akun_id\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:0;s:10:\"is_primary\";b:0;}s:30:\"jurnal_items_jurnal_id_foreign\";a:4:{s:7:\"columns\";a:1:{i:0;s:9:\"jurnal_id\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:0;s:10:\"is_primary\";b:0;}s:26:\"jurnal_items_user_id_index\";a:4:{s:7:\"columns\";a:1:{i:0;s:7:\"user_id\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:0;s:10:\"is_primary\";b:0;}s:7:\"primary\";a:4:{s:7:\"columns\";a:1:{i:0;s:2:\"id\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:1;s:10:\"is_primary\";b:1;}}s:12:\"foreign_keys\";a:2:{i:0;a:7:{s:4:\"name\";s:28:\"jurnal_items_akun_id_foreign\";s:7:\"columns\";a:1:{i:0;s:7:\"akun_id\";}s:14:\"foreign_schema\";s:8:\"keuangan\";s:13:\"foreign_table\";s:14:\"akun_perkiraan\";s:15:\"foreign_columns\";a:1:{i:0;s:2:\"id\";}s:9:\"on_update\";s:9:\"no action\";s:9:\"on_delete\";s:9:\"no action\";}i:1;a:7:{s:4:\"name\";s:30:\"jurnal_items_jurnal_id_foreign\";s:7:\"columns\";a:1:{i:0;s:9:\"jurnal_id\";}s:14:\"foreign_schema\";s:8:\"keuangan\";s:13:\"foreign_table\";s:11:\"jurnal_umum\";s:15:\"foreign_columns\";a:1:{i:0;s:2:\"id\";}s:9:\"on_update\";s:9:\"no action\";s:9:\"on_delete\";s:7:\"cascade\";}}s:8:\"triggers\";a:0:{}s:17:\"check_constraints\";a:0:{}}s:11:\"jurnal_umum\";a:5:{s:7:\"columns\";a:18:{s:2:\"id\";a:1:{s:4:\"type\";s:15:\"bigint unsigned\";}s:5:\"nomor\";a:1:{s:4:\"type\";s:11:\"varchar(50)\";}s:7:\"tanggal\";a:1:{s:4:\"type\";s:4:\"date\";}s:10:\"keterangan\";a:1:{s:4:\"type\";s:4:\"text\";}s:4:\"tipe\";a:1:{s:4:\"type\";s:213:\"enum(\'kas_masuk\',\'kas_keluar\',\'mutasi_bank\',\'pembelian\',\'penjualan\',\'manual\',\'tutup_buku\',\'hpp\',\'pembayaran\',\'retur_penjualan\',\'retur_pembelian\',\'penyesuaian_stok\',\'perolehan_aset\',\'penyusutan\',\'penghapusan_aset\')\";}s:8:\"ref_type\";a:1:{s:4:\"type\";s:12:\"varchar(255)\";}s:6:\"ref_id\";a:1:{s:4:\"type\";s:15:\"bigint unsigned\";}s:9:\"is_posted\";a:1:{s:4:\"type\";s:10:\"tinyint(1)\";}s:10:\"created_at\";a:1:{s:4:\"type\";s:9:\"timestamp\";}s:10:\"updated_at\";a:1:{s:4:\"type\";s:9:\"timestamp\";}s:10:\"created_by\";a:1:{s:4:\"type\";s:15:\"bigint unsigned\";}s:10:\"updated_by\";a:1:{s:4:\"type\";s:15:\"bigint unsigned\";}s:11:\"approved_by\";a:1:{s:4:\"type\";s:15:\"bigint unsigned\";}s:15:\"approval_status\";a:1:{s:4:\"type\";s:52:\"enum(\'draft\',\'pending_review\',\'approved\',\'rejected\')\";}s:15:\"approval_reason\";a:1:{s:4:\"type\";s:4:\"text\";}s:11:\"approved_at\";a:1:{s:4:\"type\";s:9:\"timestamp\";}s:13:\"change_reason\";a:1:{s:4:\"type\";s:4:\"text\";}s:7:\"user_id\";a:1:{s:4:\"type\";s:15:\"bigint unsigned\";}}s:7:\"indexes\";a:7:{s:31:\"jurnal_umum_approved_by_foreign\";a:4:{s:7:\"columns\";a:1:{i:0;s:11:\"approved_by\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:0;s:10:\"is_primary\";b:0;}s:30:\"jurnal_umum_created_by_foreign\";a:4:{s:7:\"columns\";a:1:{i:0;s:10:\"created_by\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:0;s:10:\"is_primary\";b:0;}s:33:\"jurnal_umum_ref_type_ref_id_index\";a:4:{s:7:\"columns\";a:2:{i:0;s:8:\"ref_type\";i:1;s:6:\"ref_id\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:0;s:10:\"is_primary\";b:0;}s:30:\"jurnal_umum_updated_by_foreign\";a:4:{s:7:\"columns\";a:1:{i:0;s:10:\"updated_by\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:0;s:10:\"is_primary\";b:0;}s:25:\"jurnal_umum_user_id_index\";a:4:{s:7:\"columns\";a:1:{i:0;s:7:\"user_id\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:0;s:10:\"is_primary\";b:0;}s:32:\"jurnal_umum_user_id_nomor_unique\";a:4:{s:7:\"columns\";a:2:{i:0;s:7:\"user_id\";i:1;s:5:\"nomor\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:1;s:10:\"is_primary\";b:0;}s:7:\"primary\";a:4:{s:7:\"columns\";a:1:{i:0;s:2:\"id\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:1;s:10:\"is_primary\";b:1;}}s:12:\"foreign_keys\";a:3:{i:0;a:7:{s:4:\"name\";s:31:\"jurnal_umum_approved_by_foreign\";s:7:\"columns\";a:1:{i:0;s:11:\"approved_by\";}s:14:\"foreign_schema\";s:8:\"keuangan\";s:13:\"foreign_table\";s:5:\"users\";s:15:\"foreign_columns\";a:1:{i:0;s:2:\"id\";}s:9:\"on_update\";s:9:\"no action\";s:9:\"on_delete\";s:8:\"set null\";}i:1;a:7:{s:4:\"name\";s:30:\"jurnal_umum_created_by_foreign\";s:7:\"columns\";a:1:{i:0;s:10:\"created_by\";}s:14:\"foreign_schema\";s:8:\"keuangan\";s:13:\"foreign_table\";s:5:\"users\";s:15:\"foreign_columns\";a:1:{i:0;s:2:\"id\";}s:9:\"on_update\";s:9:\"no action\";s:9:\"on_delete\";s:8:\"set null\";}i:2;a:7:{s:4:\"name\";s:30:\"jurnal_umum_updated_by_foreign\";s:7:\"columns\";a:1:{i:0;s:10:\"updated_by\";}s:14:\"foreign_schema\";s:8:\"keuangan\";s:13:\"foreign_table\";s:5:\"users\";s:15:\"foreign_columns\";a:1:{i:0;s:2:\"id\";}s:9:\"on_update\";s:9:\"no action\";s:9:\"on_delete\";s:8:\"set null\";}}s:8:\"triggers\";a:0:{}s:17:\"check_constraints\";a:0:{}}}}', 1789787041),
('kaspro-cache-boost:mcp:database-schema:mysql:pengaturan:0:0:0:0', 'a:2:{s:6:\"engine\";s:5:\"mysql\";s:6:\"tables\";a:1:{s:10:\"pengaturan\";a:5:{s:7:\"columns\";a:6:{s:2:\"id\";a:1:{s:4:\"type\";s:15:\"bigint unsigned\";}s:3:\"key\";a:1:{s:4:\"type\";s:12:\"varchar(100)\";}s:5:\"value\";a:1:{s:4:\"type\";s:4:\"text\";}s:10:\"created_at\";a:1:{s:4:\"type\";s:9:\"timestamp\";}s:10:\"updated_at\";a:1:{s:4:\"type\";s:9:\"timestamp\";}s:7:\"user_id\";a:1:{s:4:\"type\";s:15:\"bigint unsigned\";}}s:7:\"indexes\";a:3:{s:24:\"pengaturan_user_id_index\";a:4:{s:7:\"columns\";a:1:{i:0;s:7:\"user_id\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:0;s:10:\"is_primary\";b:0;}s:29:\"pengaturan_user_id_key_unique\";a:4:{s:7:\"columns\";a:2:{i:0;s:7:\"user_id\";i:1;s:3:\"key\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:1;s:10:\"is_primary\";b:0;}s:7:\"primary\";a:4:{s:7:\"columns\";a:1:{i:0;s:2:\"id\";}s:4:\"type\";s:5:\"btree\";s:9:\"is_unique\";b:1;s:10:\"is_primary\";b:1;}}s:12:\"foreign_keys\";a:0:{}s:8:\"triggers\";a:0:{}s:17:\"check_constraints\";a:0:{}}}}', 1789879239);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('kaspro-cache-roster:project:v3:2251e656c4fac389c333ea2f253e1c43', 'O:26:\"Laravel\\Roster\\ProjectScan\":8:{s:8:\"basePath\";s:23:\"D:\\SERVER\\www\\Keuangan\\\";s:3:\"php\";O:35:\"Laravel\\Roster\\Ecosystems\\Ecosystem\":2:{s:9:\"\0*\0byName\";a:129:{s:23:\"barryvdh/laravel-dompdf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"barryvdh/laravel-dompdf\";s:10:\"\0*\0version\";s:5:\"3.1.2\";s:9:\"\0*\0source\";E:43:\"Laravel\\Roster\\Enums\\PackageSource:Composer\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.1\";s:7:\"\0*\0path\";s:53:\"D:\\SERVER\\www\\Keuangan\\vendor\\barryvdh\\laravel-dompdf\";}s:10:\"brick/math\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"brick/math\";s:10:\"\0*\0version\";s:6:\"0.18.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"D:\\SERVER\\www\\Keuangan\\vendor\\brick\\math\";}s:31:\"carbonphp/carbon-doctrine-types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"carbonphp/carbon-doctrine-types\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\SERVER\\www\\Keuangan\\vendor\\carbonphp\\carbon-doctrine-types\";}s:13:\"composer/pcre\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"composer/pcre\";s:10:\"\0*\0version\";s:5:\"3.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"D:\\SERVER\\www\\Keuangan\\vendor\\composer\\pcre\";}s:15:\"composer/semver\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"composer/semver\";s:10:\"\0*\0version\";s:5:\"3.4.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\vendor\\composer\\semver\";}s:23:\"dflydev/dot-access-data\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"dflydev/dot-access-data\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\SERVER\\www\\Keuangan\\vendor\\dflydev\\dot-access-data\";}s:18:\"doctrine/inflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"doctrine/inflector\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\vendor\\doctrine\\inflector\";}s:14:\"doctrine/lexer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"doctrine/lexer\";s:10:\"\0*\0version\";s:5:\"3.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\vendor\\doctrine\\lexer\";}s:13:\"dompdf/dompdf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"dompdf/dompdf\";s:10:\"\0*\0version\";s:5:\"3.1.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"D:\\SERVER\\www\\Keuangan\\vendor\\dompdf\\dompdf\";}s:19:\"dompdf/php-font-lib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"dompdf/php-font-lib\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\SERVER\\www\\Keuangan\\vendor\\dompdf\\php-font-lib\";}s:18:\"dompdf/php-svg-lib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"dompdf/php-svg-lib\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\vendor\\dompdf\\php-svg-lib\";}s:29:\"dragonmantank/cron-expression\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"dragonmantank/cron-expression\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\SERVER\\www\\Keuangan\\vendor\\dragonmantank\\cron-expression\";}s:23:\"egulias/email-validator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"egulias/email-validator\";s:10:\"\0*\0version\";s:5:\"4.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\SERVER\\www\\Keuangan\\vendor\\egulias\\email-validator\";}s:18:\"fruitcake/php-cors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"fruitcake/php-cors\";s:10:\"\0*\0version\";s:5:\"1.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\vendor\\fruitcake\\php-cors\";}s:27:\"graham-campbell/result-type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"graham-campbell/result-type\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\SERVER\\www\\Keuangan\\vendor\\graham-campbell\\result-type\";}s:17:\"guzzlehttp/guzzle\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"guzzlehttp/guzzle\";s:10:\"\0*\0version\";s:5:\"8.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\vendor\\guzzlehttp\\guzzle\";}s:19:\"guzzlehttp/promises\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"guzzlehttp/promises\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\SERVER\\www\\Keuangan\\vendor\\guzzlehttp\\promises\";}s:15:\"guzzlehttp/psr7\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"guzzlehttp/psr7\";s:10:\"\0*\0version\";s:5:\"3.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\vendor\\guzzlehttp\\psr7\";}s:23:\"guzzlehttp/uri-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"guzzlehttp/uri-template\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\SERVER\\www\\Keuangan\\vendor\\guzzlehttp\\uri-template\";}s:17:\"laravel/framework\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"laravel/framework\";s:10:\"\0*\0version\";s:7:\"13.30.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^13.17\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\vendor\\laravel\\framework\";}s:15:\"laravel/prompts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"laravel/prompts\";s:10:\"\0*\0version\";s:6:\"0.3.24\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\vendor\\laravel\\prompts\";}s:28:\"laravel/serializable-closure\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"laravel/serializable-closure\";s:10:\"\0*\0version\";s:6:\"2.0.16\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\SERVER\\www\\Keuangan\\vendor\\laravel\\serializable-closure\";}s:14:\"laravel/tinker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/tinker\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.0\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\vendor\\laravel\\tinker\";}s:17:\"league/commonmark\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"league/commonmark\";s:10:\"\0*\0version\";s:6:\"2.10.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\vendor\\league\\commonmark\";}s:13:\"league/config\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"league/config\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"D:\\SERVER\\www\\Keuangan\\vendor\\league\\config\";}s:16:\"league/flysystem\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"league/flysystem\";s:10:\"\0*\0version\";s:6:\"3.36.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\vendor\\league\\flysystem\";}s:22:\"league/flysystem-local\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"league/flysystem-local\";s:10:\"\0*\0version\";s:6:\"3.35.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\SERVER\\www\\Keuangan\\vendor\\league\\flysystem-local\";}s:26:\"league/mime-type-detection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"league/mime-type-detection\";s:10:\"\0*\0version\";s:6:\"1.17.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\SERVER\\www\\Keuangan\\vendor\\league\\mime-type-detection\";}s:10:\"league/uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"league/uri\";s:10:\"\0*\0version\";s:5:\"7.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"D:\\SERVER\\www\\Keuangan\\vendor\\league\\uri\";}s:21:\"league/uri-interfaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"league/uri-interfaces\";s:10:\"\0*\0version\";s:5:\"7.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\SERVER\\www\\Keuangan\\vendor\\league\\uri-interfaces\";}s:17:\"maatwebsite/excel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"maatwebsite/excel\";s:10:\"\0*\0version\";s:5:\"4.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^4.0\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\vendor\\maatwebsite\\excel\";}s:23:\"maennchen/zipstream-php\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"maennchen/zipstream-php\";s:10:\"\0*\0version\";s:5:\"3.2.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\SERVER\\www\\Keuangan\\vendor\\maennchen\\zipstream-php\";}s:17:\"markbaker/complex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"markbaker/complex\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\vendor\\markbaker\\complex\";}s:16:\"markbaker/matrix\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"markbaker/matrix\";s:10:\"\0*\0version\";s:5:\"3.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\vendor\\markbaker\\matrix\";}s:17:\"masterminds/html5\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"masterminds/html5\";s:10:\"\0*\0version\";s:6:\"2.11.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\vendor\\masterminds\\html5\";}s:15:\"monolog/monolog\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"monolog/monolog\";s:10:\"\0*\0version\";s:6:\"3.11.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\vendor\\monolog\\monolog\";}s:13:\"nesbot/carbon\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"nesbot/carbon\";s:10:\"\0*\0version\";s:6:\"3.13.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"D:\\SERVER\\www\\Keuangan\\vendor\\nesbot\\carbon\";}s:12:\"nette/schema\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"nette/schema\";s:10:\"\0*\0version\";s:5:\"1.3.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"D:\\SERVER\\www\\Keuangan\\vendor\\nette\\schema\";}s:11:\"nette/utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"nette/utils\";s:10:\"\0*\0version\";s:5:\"4.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"D:\\SERVER\\www\\Keuangan\\vendor\\nette\\utils\";}s:16:\"nikic/php-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"nikic/php-parser\";s:10:\"\0*\0version\";s:5:\"5.8.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\vendor\\nikic\\php-parser\";}s:19:\"nunomaduro/termwind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"nunomaduro/termwind\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\SERVER\\www\\Keuangan\\vendor\\nunomaduro\\termwind\";}s:24:\"phpoffice/phpspreadsheet\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"phpoffice/phpspreadsheet\";s:10:\"\0*\0version\";s:5:\"5.9.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\SERVER\\www\\Keuangan\\vendor\\phpoffice\\phpspreadsheet\";}s:19:\"phpoption/phpoption\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpoption/phpoption\";s:10:\"\0*\0version\";s:6:\"1.10.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\SERVER\\www\\Keuangan\\vendor\\phpoption\\phpoption\";}s:9:\"psr/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psr/clock\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"D:\\SERVER\\www\\Keuangan\\vendor\\psr\\clock\";}s:13:\"psr/container\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"psr/container\";s:10:\"\0*\0version\";s:5:\"2.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"D:\\SERVER\\www\\Keuangan\\vendor\\psr\\container\";}s:20:\"psr/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"psr/event-dispatcher\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\SERVER\\www\\Keuangan\\vendor\\psr\\event-dispatcher\";}s:15:\"psr/http-client\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"psr/http-client\";s:10:\"\0*\0version\";s:5:\"1.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\vendor\\psr\\http-client\";}s:16:\"psr/http-factory\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-factory\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\vendor\\psr\\http-factory\";}s:16:\"psr/http-message\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-message\";s:10:\"\0*\0version\";s:3:\"2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\vendor\\psr\\http-message\";}s:7:\"psr/log\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"psr/log\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:37:\"D:\\SERVER\\www\\Keuangan\\vendor\\psr\\log\";}s:16:\"psr/simple-cache\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/simple-cache\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\vendor\\psr\\simple-cache\";}s:9:\"psy/psysh\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psy/psysh\";s:10:\"\0*\0version\";s:7:\"0.12.24\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"D:\\SERVER\\www\\Keuangan\\vendor\\psy\\psysh\";}s:17:\"ramsey/collection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"ramsey/collection\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\vendor\\ramsey\\collection\";}s:11:\"ramsey/uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ramsey/uuid\";s:10:\"\0*\0version\";s:5:\"4.9.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"D:\\SERVER\\www\\Keuangan\\vendor\\ramsey\\uuid\";}s:25:\"sabberworm/php-css-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"sabberworm/php-css-parser\";s:10:\"\0*\0version\";s:5:\"9.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\SERVER\\www\\Keuangan\\vendor\\sabberworm\\php-css-parser\";}s:13:\"symfony/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"symfony/clock\";s:10:\"\0*\0version\";s:5:\"7.4.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\clock\";}s:15:\"symfony/console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/console\";s:10:\"\0*\0version\";s:6:\"7.4.18\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\console\";}s:20:\"symfony/css-selector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"symfony/css-selector\";s:10:\"\0*\0version\";s:6:\"7.4.18\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\css-selector\";}s:29:\"symfony/deprecation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/deprecation-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\deprecation-contracts\";}s:21:\"symfony/error-handler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/error-handler\";s:10:\"\0*\0version\";s:6:\"7.4.17\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\error-handler\";}s:24:\"symfony/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"symfony/event-dispatcher\";s:10:\"\0*\0version\";s:6:\"7.4.17\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\event-dispatcher\";}s:34:\"symfony/event-dispatcher-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"symfony/event-dispatcher-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\event-dispatcher-contracts\";}s:14:\"symfony/finder\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/finder\";s:10:\"\0*\0version\";s:6:\"7.4.17\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\finder\";}s:23:\"symfony/http-foundation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"symfony/http-foundation\";s:10:\"\0*\0version\";s:6:\"7.4.18\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\http-foundation\";}s:19:\"symfony/http-kernel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/http-kernel\";s:10:\"\0*\0version\";s:6:\"7.4.18\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\http-kernel\";}s:14:\"symfony/mailer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/mailer\";s:10:\"\0*\0version\";s:6:\"7.4.17\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\mailer\";}s:12:\"symfony/mime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/mime\";s:10:\"\0*\0version\";s:6:\"7.4.18\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\mime\";}s:22:\"symfony/polyfill-ctype\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-ctype\";s:10:\"\0*\0version\";s:6:\"1.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\polyfill-ctype\";}s:30:\"symfony/polyfill-intl-grapheme\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"symfony/polyfill-intl-grapheme\";s:10:\"\0*\0version\";s:6:\"1.41.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\polyfill-intl-grapheme\";}s:25:\"symfony/polyfill-intl-idn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-intl-idn\";s:10:\"\0*\0version\";s:6:\"1.42.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\polyfill-intl-idn\";}s:32:\"symfony/polyfill-intl-normalizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"symfony/polyfill-intl-normalizer\";s:10:\"\0*\0version\";s:6:\"1.42.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\polyfill-intl-normalizer\";}s:25:\"symfony/polyfill-mbstring\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-mbstring\";s:10:\"\0*\0version\";s:6:\"1.38.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\polyfill-mbstring\";}s:22:\"symfony/polyfill-php80\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php80\";s:10:\"\0*\0version\";s:6:\"1.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\polyfill-php80\";}s:22:\"symfony/polyfill-php82\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php82\";s:10:\"\0*\0version\";s:6:\"1.38.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\polyfill-php82\";}s:22:\"symfony/polyfill-php83\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php83\";s:10:\"\0*\0version\";s:6:\"1.41.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\polyfill-php83\";}s:22:\"symfony/polyfill-php84\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php84\";s:10:\"\0*\0version\";s:6:\"1.38.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\polyfill-php84\";}s:22:\"symfony/polyfill-php85\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php85\";s:10:\"\0*\0version\";s:6:\"1.41.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\polyfill-php85\";}s:22:\"symfony/polyfill-php86\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php86\";s:10:\"\0*\0version\";s:6:\"1.41.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\polyfill-php86\";}s:21:\"symfony/polyfill-uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/polyfill-uuid\";s:10:\"\0*\0version\";s:6:\"1.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\polyfill-uuid\";}s:15:\"symfony/process\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/process\";s:10:\"\0*\0version\";s:6:\"7.4.18\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\process\";}s:15:\"symfony/routing\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/routing\";s:10:\"\0*\0version\";s:6:\"7.4.18\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\routing\";}s:25:\"symfony/service-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/service-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\service-contracts\";}s:14:\"symfony/string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/string\";s:10:\"\0*\0version\";s:6:\"7.4.15\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\string\";}s:19:\"symfony/translation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/translation\";s:10:\"\0*\0version\";s:6:\"7.4.17\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\translation\";}s:29:\"symfony/translation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/translation-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\translation-contracts\";}s:11:\"symfony/uid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"symfony/uid\";s:10:\"\0*\0version\";s:6:\"7.4.17\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\uid\";}s:18:\"symfony/var-dumper\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"symfony/var-dumper\";s:10:\"\0*\0version\";s:6:\"7.4.18\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\var-dumper\";}s:21:\"thecodingmachine/safe\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"thecodingmachine/safe\";s:10:\"\0*\0version\";s:5:\"3.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\SERVER\\www\\Keuangan\\vendor\\thecodingmachine\\safe\";}s:33:\"tijsverkoyen/css-to-inline-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"tijsverkoyen/css-to-inline-styles\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\SERVER\\www\\Keuangan\\vendor\\tijsverkoyen\\css-to-inline-styles\";}s:16:\"vlucas/phpdotenv\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"vlucas/phpdotenv\";s:10:\"\0*\0version\";s:5:\"5.7.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\vendor\\vlucas\\phpdotenv\";}s:19:\"voku/portable-ascii\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"voku/portable-ascii\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\SERVER\\www\\Keuangan\\vendor\\voku\\portable-ascii\";}s:14:\"fakerphp/faker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"fakerphp/faker\";s:10:\"\0*\0version\";s:6:\"1.24.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.23\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\vendor\\fakerphp\\faker\";}s:11:\"filp/whoops\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"filp/whoops\";s:10:\"\0*\0version\";s:6:\"2.18.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"D:\\SERVER\\www\\Keuangan\\vendor\\filp\\whoops\";}s:21:\"hamcrest/hamcrest-php\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"hamcrest/hamcrest-php\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\SERVER\\www\\Keuangan\\vendor\\hamcrest\\hamcrest-php\";}s:22:\"laravel/agent-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"laravel/agent-detector\";s:10:\"\0*\0version\";s:5:\"2.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\SERVER\\www\\Keuangan\\vendor\\laravel\\agent-detector\";}s:13:\"laravel/boost\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"laravel/boost\";s:10:\"\0*\0version\";s:5:\"2.7.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^2.7\";s:7:\"\0*\0path\";s:43:\"D:\\SERVER\\www\\Keuangan\\vendor\\laravel\\boost\";}s:14:\"laravel/breeze\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/breeze\";s:10:\"\0*\0version\";s:5:\"2.4.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^2.4\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\vendor\\laravel\\breeze\";}s:11:\"laravel/mcp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/mcp\";s:10:\"\0*\0version\";s:5:\"0.9.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"D:\\SERVER\\www\\Keuangan\\vendor\\laravel\\mcp\";}s:12:\"laravel/pail\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pail\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.2.5\";s:7:\"\0*\0path\";s:42:\"D:\\SERVER\\www\\Keuangan\\vendor\\laravel\\pail\";}s:11:\"laravel/pao\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/pao\";s:10:\"\0*\0version\";s:5:\"1.1.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.0.6\";s:7:\"\0*\0path\";s:41:\"D:\\SERVER\\www\\Keuangan\\vendor\\laravel\\pao\";}s:12:\"laravel/pint\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pint\";s:10:\"\0*\0version\";s:6:\"1.30.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.27\";s:7:\"\0*\0path\";s:42:\"D:\\SERVER\\www\\Keuangan\\vendor\\laravel\\pint\";}s:14:\"laravel/roster\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/roster\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\vendor\\laravel\\roster\";}s:15:\"mockery/mockery\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"mockery/mockery\";s:10:\"\0*\0version\";s:6:\"1.6.15\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^1.6\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\vendor\\mockery\\mockery\";}s:17:\"myclabs/deep-copy\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"myclabs/deep-copy\";s:10:\"\0*\0version\";s:6:\"1.14.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\vendor\\myclabs\\deep-copy\";}s:20:\"nunomaduro/collision\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"nunomaduro/collision\";s:10:\"\0*\0version\";s:5:\"8.9.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^8.6\";s:7:\"\0*\0path\";s:50:\"D:\\SERVER\\www\\Keuangan\\vendor\\nunomaduro\\collision\";}s:16:\"phar-io/manifest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"phar-io/manifest\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\vendor\\phar-io\\manifest\";}s:15:\"phar-io/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phar-io/version\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\vendor\\phar-io\\version\";}s:25:\"phpunit/php-code-coverage\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-code-coverage\";s:10:\"\0*\0version\";s:6:\"12.5.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\SERVER\\www\\Keuangan\\vendor\\phpunit\\php-code-coverage\";}s:25:\"phpunit/php-file-iterator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-file-iterator\";s:10:\"\0*\0version\";s:5:\"6.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\SERVER\\www\\Keuangan\\vendor\\phpunit\\php-file-iterator\";}s:19:\"phpunit/php-invoker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpunit/php-invoker\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\SERVER\\www\\Keuangan\\vendor\\phpunit\\php-invoker\";}s:25:\"phpunit/php-text-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-text-template\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\SERVER\\www\\Keuangan\\vendor\\phpunit\\php-text-template\";}s:17:\"phpunit/php-timer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"phpunit/php-timer\";s:10:\"\0*\0version\";s:5:\"8.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\vendor\\phpunit\\php-timer\";}s:15:\"phpunit/phpunit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phpunit/phpunit\";s:10:\"\0*\0version\";s:7:\"12.5.34\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:8:\"^12.5.12\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\vendor\\phpunit\\phpunit\";}s:20:\"sebastian/cli-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/cli-parser\";s:10:\"\0*\0version\";s:5:\"4.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\SERVER\\www\\Keuangan\\vendor\\sebastian\\cli-parser\";}s:20:\"sebastian/comparator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/comparator\";s:10:\"\0*\0version\";s:5:\"7.1.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\SERVER\\www\\Keuangan\\vendor\\sebastian\\comparator\";}s:20:\"sebastian/complexity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/complexity\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\SERVER\\www\\Keuangan\\vendor\\sebastian\\complexity\";}s:14:\"sebastian/diff\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/diff\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\vendor\\sebastian\\diff\";}s:21:\"sebastian/environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"sebastian/environment\";s:10:\"\0*\0version\";s:5:\"8.1.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\SERVER\\www\\Keuangan\\vendor\\sebastian\\environment\";}s:18:\"sebastian/exporter\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"sebastian/exporter\";s:10:\"\0*\0version\";s:5:\"7.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\vendor\\sebastian\\exporter\";}s:22:\"sebastian/global-state\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"sebastian/global-state\";s:10:\"\0*\0version\";s:5:\"8.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\SERVER\\www\\Keuangan\\vendor\\sebastian\\global-state\";}s:23:\"sebastian/lines-of-code\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"sebastian/lines-of-code\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\SERVER\\www\\Keuangan\\vendor\\sebastian\\lines-of-code\";}s:27:\"sebastian/object-enumerator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/object-enumerator\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\SERVER\\www\\Keuangan\\vendor\\sebastian\\object-enumerator\";}s:26:\"sebastian/object-reflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"sebastian/object-reflector\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\SERVER\\www\\Keuangan\\vendor\\sebastian\\object-reflector\";}s:27:\"sebastian/recursion-context\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/recursion-context\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\SERVER\\www\\Keuangan\\vendor\\sebastian\\recursion-context\";}s:14:\"sebastian/type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/type\";s:10:\"\0*\0version\";s:5:\"6.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\vendor\\sebastian\\type\";}s:17:\"sebastian/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"sebastian/version\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\vendor\\sebastian\\version\";}s:28:\"staabm/side-effects-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"staabm/side-effects-detector\";s:10:\"\0*\0version\";s:5:\"1.0.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\SERVER\\www\\Keuangan\\vendor\\staabm\\side-effects-detector\";}s:12:\"symfony/yaml\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/yaml\";s:10:\"\0*\0version\";s:6:\"7.4.18\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"D:\\SERVER\\www\\Keuangan\\vendor\\symfony\\yaml\";}s:17:\"theseer/tokenizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"theseer/tokenizer\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\vendor\\theseer\\tokenizer\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:129:{i:0;r:5;i:1;r:13;i:2;r:21;i:3;r:29;i:4;r:37;i:5;r:45;i:6;r:53;i:7;r:61;i:8;r:69;i:9;r:77;i:10;r:85;i:11;r:93;i:12;r:101;i:13;r:109;i:14;r:117;i:15;r:125;i:16;r:133;i:17;r:141;i:18;r:149;i:19;r:157;i:20;r:165;i:21;r:173;i:22;r:181;i:23;r:189;i:24;r:197;i:25;r:205;i:26;r:213;i:27;r:221;i:28;r:229;i:29;r:237;i:30;r:245;i:31;r:253;i:32;r:261;i:33;r:269;i:34;r:277;i:35;r:285;i:36;r:293;i:37;r:301;i:38;r:309;i:39;r:317;i:40;r:325;i:41;r:333;i:42;r:341;i:43;r:349;i:44;r:357;i:45;r:365;i:46;r:373;i:47;r:381;i:48;r:389;i:49;r:397;i:50;r:405;i:51;r:413;i:52;r:421;i:53;r:429;i:54;r:437;i:55;r:445;i:56;r:453;i:57;r:461;i:58;r:469;i:59;r:477;i:60;r:485;i:61;r:493;i:62;r:501;i:63;r:509;i:64;r:517;i:65;r:525;i:66;r:533;i:67;r:541;i:68;r:549;i:69;r:557;i:70;r:565;i:71;r:573;i:72;r:581;i:73;r:589;i:74;r:597;i:75;r:605;i:76;r:613;i:77;r:621;i:78;r:629;i:79;r:637;i:80;r:645;i:81;r:653;i:82;r:661;i:83;r:669;i:84;r:677;i:85;r:685;i:86;r:693;i:87;r:701;i:88;r:709;i:89;r:717;i:90;r:725;i:91;r:733;i:92;r:741;i:93;r:749;i:94;r:757;i:95;r:765;i:96;r:773;i:97;r:781;i:98;r:789;i:99;r:797;i:100;r:805;i:101;r:813;i:102;r:821;i:103;r:829;i:104;r:837;i:105;r:845;i:106;r:853;i:107;r:861;i:108;r:869;i:109;r:877;i:110;r:885;i:111;r:893;i:112;r:901;i:113;r:909;i:114;r:917;i:115;r:925;i:116;r:933;i:117;r:941;i:118;r:949;i:119;r:957;i:120;r:965;i:121;r:973;i:122;r:981;i:123;r:989;i:124;r:997;i:125;r:1005;i:126;r:1013;i:127;r:1021;i:128;r:1029;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:2:\"js\";O:37:\"Laravel\\Roster\\Ecosystems\\JsEcosystem\":3:{s:9:\"\0*\0byName\";a:190:{s:24:\"@alcalzone/ansi-tokenize\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"@alcalzone/ansi-tokenize\";s:10:\"\0*\0version\";s:5:\"0.3.0\";s:9:\"\0*\0source\";E:38:\"Laravel\\Roster\\Enums\\PackageSource:Npm\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@alcalzone\\ansi-tokenize\";}s:18:\"@laravel/multiplex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@laravel/multiplex\";s:10:\"\0*\0version\";s:5:\"0.4.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^0.4.1\";s:7:\"\0*\0path\";s:54:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@laravel\\multiplex\";}s:12:\"ansi-escapes\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"ansi-escapes\";s:10:\"\0*\0version\";s:5:\"7.3.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\node_modules\\ansi-escapes\";}s:10:\"ansi-regex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"ansi-regex\";s:10:\"\0*\0version\";s:5:\"6.3.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\node_modules\\ansi-regex\";}s:11:\"ansi-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ansi-styles\";s:10:\"\0*\0version\";s:5:\"6.2.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\node_modules\\ansi-styles\";}s:9:\"auto-bind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"auto-bind\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\node_modules\\auto-bind\";}s:5:\"chalk\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"chalk\";s:10:\"\0*\0version\";s:5:\"5.6.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"D:\\SERVER\\www\\Keuangan\\node_modules\\chalk\";}s:9:\"cli-boxes\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"cli-boxes\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\node_modules\\cli-boxes\";}s:10:\"cli-cursor\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"cli-cursor\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\node_modules\\cli-cursor\";}s:12:\"cli-truncate\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"cli-truncate\";s:10:\"\0*\0version\";s:5:\"6.1.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\node_modules\\cli-truncate\";}s:12:\"code-excerpt\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"code-excerpt\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\node_modules\\code-excerpt\";}s:9:\"commander\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"commander\";s:10:\"\0*\0version\";s:6:\"15.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\node_modules\\commander\";}s:17:\"convert-to-spaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"convert-to-spaces\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\SERVER\\www\\Keuangan\\node_modules\\convert-to-spaces\";}s:11:\"environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"environment\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\node_modules\\environment\";}s:10:\"es-toolkit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"es-toolkit\";s:10:\"\0*\0version\";s:6:\"1.52.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\node_modules\\es-toolkit\";}s:20:\"escape-string-regexp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"escape-string-regexp\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\SERVER\\www\\Keuangan\\node_modules\\escape-string-regexp\";}s:20:\"get-east-asian-width\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"get-east-asian-width\";s:10:\"\0*\0version\";s:5:\"1.6.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\SERVER\\www\\Keuangan\\node_modules\\get-east-asian-width\";}s:12:\"html5-qrcode\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"html5-qrcode\";s:10:\"\0*\0version\";s:5:\"2.3.8\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^2.3.8\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\node_modules\\html5-qrcode\";}s:13:\"indent-string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"indent-string\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\SERVER\\www\\Keuangan\\node_modules\\indent-string\";}s:3:\"ink\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"ink\";s:10:\"\0*\0version\";s:5:\"7.1.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"D:\\SERVER\\www\\Keuangan\\node_modules\\ink\";}s:23:\"is-fullwidth-code-point\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"is-fullwidth-code-point\";s:10:\"\0*\0version\";s:5:\"5.1.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\SERVER\\www\\Keuangan\\node_modules\\is-fullwidth-code-point\";}s:8:\"is-in-ci\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"is-in-ci\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\node_modules\\is-in-ci\";}s:8:\"mimic-fn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"mimic-fn\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\node_modules\\mimic-fn\";}s:7:\"onetime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"onetime\";s:10:\"\0*\0version\";s:5:\"5.1.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"D:\\SERVER\\www\\Keuangan\\node_modules\\onetime\";}s:13:\"patch-console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"patch-console\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\SERVER\\www\\Keuangan\\node_modules\\patch-console\";}s:5:\"react\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"react\";s:10:\"\0*\0version\";s:6:\"19.2.8\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"D:\\SERVER\\www\\Keuangan\\node_modules\\react\";}s:16:\"react-reconciler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"react-reconciler\";s:10:\"\0*\0version\";s:6:\"0.33.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\SERVER\\www\\Keuangan\\node_modules\\react-reconciler\";}s:14:\"restore-cursor\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"restore-cursor\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\SERVER\\www\\Keuangan\\node_modules\\restore-cursor\";}s:9:\"scheduler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"scheduler\";s:10:\"\0*\0version\";s:6:\"0.27.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\node_modules\\scheduler\";}s:11:\"signal-exit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"signal-exit\";s:10:\"\0*\0version\";s:5:\"3.0.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\node_modules\\signal-exit\";}s:10:\"slice-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"slice-ansi\";s:10:\"\0*\0version\";s:5:\"9.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\node_modules\\slice-ansi\";}s:11:\"stack-utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"stack-utils\";s:10:\"\0*\0version\";s:5:\"2.0.6\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\node_modules\\stack-utils\";}s:12:\"string-width\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"string-width\";s:10:\"\0*\0version\";s:5:\"8.2.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\node_modules\\string-width\";}s:10:\"strip-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"strip-ansi\";s:10:\"\0*\0version\";s:5:\"7.2.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\node_modules\\strip-ansi\";}s:10:\"tagged-tag\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"tagged-tag\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\node_modules\\tagged-tag\";}s:13:\"terminal-size\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"terminal-size\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\SERVER\\www\\Keuangan\\node_modules\\terminal-size\";}s:9:\"type-fest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"type-fest\";s:10:\"\0*\0version\";s:5:\"5.9.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\node_modules\\type-fest\";}s:11:\"widest-line\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"widest-line\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\node_modules\\widest-line\";}s:9:\"wrap-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"wrap-ansi\";s:10:\"\0*\0version\";s:6:\"10.0.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\node_modules\\wrap-ansi\";}s:2:\"ws\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:2:\"ws\";s:10:\"\0*\0version\";s:6:\"8.21.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"D:\\SERVER\\www\\Keuangan\\node_modules\\ws\";}s:11:\"yoga-layout\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"yoga-layout\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\node_modules\\yoga-layout\";}s:16:\"@alloc/quick-lru\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"@alloc/quick-lru\";s:10:\"\0*\0version\";s:5:\"5.3.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@alloc\\quick-lru\";}s:23:\"@jridgewell/gen-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"@jridgewell/gen-mapping\";s:10:\"\0*\0version\";s:6:\"0.3.13\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@jridgewell\\gen-mapping\";}s:21:\"@jridgewell/remapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"@jridgewell/remapping\";s:10:\"\0*\0version\";s:5:\"2.3.5\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@jridgewell\\remapping\";}s:23:\"@jridgewell/resolve-uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"@jridgewell/resolve-uri\";s:10:\"\0*\0version\";s:5:\"3.1.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@jridgewell\\resolve-uri\";}s:27:\"@jridgewell/sourcemap-codec\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"@jridgewell/sourcemap-codec\";s:10:\"\0*\0version\";s:5:\"1.6.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@jridgewell\\sourcemap-codec\";}s:25:\"@jridgewell/trace-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"@jridgewell/trace-mapping\";s:10:\"\0*\0version\";s:6:\"0.3.31\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@jridgewell\\trace-mapping\";}s:19:\"@nodelib/fs.scandir\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"@nodelib/fs.scandir\";s:10:\"\0*\0version\";s:5:\"2.1.5\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@nodelib\\fs.scandir\";}s:16:\"@nodelib/fs.stat\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"@nodelib/fs.stat\";s:10:\"\0*\0version\";s:5:\"2.0.5\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@nodelib\\fs.stat\";}s:16:\"@nodelib/fs.walk\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"@nodelib/fs.walk\";s:10:\"\0*\0version\";s:5:\"1.2.8\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@nodelib\\fs.walk\";}s:18:\"@oxc-project/types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@oxc-project/types\";s:10:\"\0*\0version\";s:7:\"0.148.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@oxc-project\\types\";}s:34:\"@rolldown/binding-android-arm-eabi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-android-arm-eabi\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@rolldown\\binding-android-arm-eabi\";}s:31:\"@rolldown/binding-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@rolldown/binding-android-arm64\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@rolldown\\binding-android-arm64\";}s:30:\"@rolldown/binding-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@rolldown/binding-darwin-arm64\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@rolldown\\binding-darwin-arm64\";}s:28:\"@rolldown/binding-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"@rolldown/binding-darwin-x64\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@rolldown\\binding-darwin-x64\";}s:29:\"@rolldown/binding-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"@rolldown/binding-freebsd-x64\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@rolldown\\binding-freebsd-x64\";}s:37:\"@rolldown/binding-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:37:\"@rolldown/binding-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@rolldown\\binding-linux-arm-gnueabihf\";}s:33:\"@rolldown/binding-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-arm64-gnu\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@rolldown\\binding-linux-arm64-gnu\";}s:34:\"@rolldown/binding-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-linux-arm64-musl\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@rolldown\\binding-linux-arm64-musl\";}s:33:\"@rolldown/binding-linux-ppc64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-ppc64-gnu\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@rolldown\\binding-linux-ppc64-gnu\";}s:33:\"@rolldown/binding-linux-s390x-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-s390x-gnu\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@rolldown\\binding-linux-s390x-gnu\";}s:31:\"@rolldown/binding-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@rolldown/binding-linux-x64-gnu\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@rolldown\\binding-linux-x64-gnu\";}s:32:\"@rolldown/binding-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@rolldown/binding-linux-x64-musl\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@rolldown\\binding-linux-x64-musl\";}s:35:\"@rolldown/binding-openharmony-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@rolldown/binding-openharmony-arm64\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@rolldown\\binding-openharmony-arm64\";}s:34:\"@rolldown/binding-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-win32-arm64-msvc\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@rolldown\\binding-win32-arm64-msvc\";}s:32:\"@rolldown/binding-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@rolldown/binding-win32-x64-msvc\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@rolldown\\binding-win32-x64-msvc\";}s:21:\"@rolldown/pluginutils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"@rolldown/pluginutils\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@rolldown\\pluginutils\";}s:18:\"@tailwindcss/forms\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@tailwindcss/forms\";s:10:\"\0*\0version\";s:6:\"0.5.11\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^0.5.2\";s:7:\"\0*\0path\";s:54:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@tailwindcss\\forms\";}s:17:\"@tailwindcss/node\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/node\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@tailwindcss\\node\";}s:18:\"@tailwindcss/oxide\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@tailwindcss/oxide\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@tailwindcss\\oxide\";}s:32:\"@tailwindcss/oxide-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@tailwindcss/oxide-android-arm64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@tailwindcss\\oxide-android-arm64\";}s:31:\"@tailwindcss/oxide-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@tailwindcss/oxide-darwin-arm64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@tailwindcss\\oxide-darwin-arm64\";}s:29:\"@tailwindcss/oxide-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"@tailwindcss/oxide-darwin-x64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@tailwindcss\\oxide-darwin-x64\";}s:30:\"@tailwindcss/oxide-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@tailwindcss/oxide-freebsd-x64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@tailwindcss\\oxide-freebsd-x64\";}s:38:\"@tailwindcss/oxide-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:38:\"@tailwindcss/oxide-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@tailwindcss\\oxide-linux-arm-gnueabihf\";}s:34:\"@tailwindcss/oxide-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@tailwindcss/oxide-linux-arm64-gnu\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@tailwindcss\\oxide-linux-arm64-gnu\";}s:35:\"@tailwindcss/oxide-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@tailwindcss/oxide-linux-arm64-musl\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@tailwindcss\\oxide-linux-arm64-musl\";}s:32:\"@tailwindcss/oxide-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@tailwindcss/oxide-linux-x64-gnu\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@tailwindcss\\oxide-linux-x64-gnu\";}s:33:\"@tailwindcss/oxide-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@tailwindcss/oxide-linux-x64-musl\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@tailwindcss\\oxide-linux-x64-musl\";}s:30:\"@tailwindcss/oxide-wasm32-wasi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@tailwindcss/oxide-wasm32-wasi\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@tailwindcss\\oxide-wasm32-wasi\";}s:35:\"@tailwindcss/oxide-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@tailwindcss/oxide-win32-arm64-msvc\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@tailwindcss\\oxide-win32-arm64-msvc\";}s:33:\"@tailwindcss/oxide-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@tailwindcss/oxide-win32-x64-msvc\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@tailwindcss\\oxide-win32-x64-msvc\";}s:17:\"@tailwindcss/vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/vite\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^4.0.0\";s:7:\"\0*\0path\";s:53:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@tailwindcss\\vite\";}s:15:\"@vue/reactivity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"@vue/reactivity\";s:10:\"\0*\0version\";s:6:\"3.5.42\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@vue\\reactivity\";}s:11:\"@vue/shared\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"@vue/shared\";s:10:\"\0*\0version\";s:6:\"3.5.42\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\node_modules\\@vue\\shared\";}s:8:\"alpinejs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"alpinejs\";s:10:\"\0*\0version\";s:6:\"3.17.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^3.4.2\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\node_modules\\alpinejs\";}s:11:\"any-promise\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"any-promise\";s:10:\"\0*\0version\";s:5:\"1.3.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\node_modules\\any-promise\";}s:8:\"anymatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"anymatch\";s:10:\"\0*\0version\";s:5:\"3.1.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\node_modules\\anymatch\";}s:3:\"arg\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"arg\";s:10:\"\0*\0version\";s:5:\"5.0.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"D:\\SERVER\\www\\Keuangan\\node_modules\\arg\";}s:12:\"autoprefixer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"autoprefixer\";s:10:\"\0*\0version\";s:6:\"10.5.4\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^10.4.2\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\node_modules\\autoprefixer\";}s:24:\"baseline-browser-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"baseline-browser-mapping\";s:10:\"\0*\0version\";s:7:\"2.11.20\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"D:\\SERVER\\www\\Keuangan\\node_modules\\baseline-browser-mapping\";}s:17:\"binary-extensions\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"binary-extensions\";s:10:\"\0*\0version\";s:5:\"2.3.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\SERVER\\www\\Keuangan\\node_modules\\binary-extensions\";}s:6:\"braces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"braces\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"D:\\SERVER\\www\\Keuangan\\node_modules\\braces\";}s:12:\"browserslist\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"browserslist\";s:10:\"\0*\0version\";s:6:\"4.28.8\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\node_modules\\browserslist\";}s:13:\"camelcase-css\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"camelcase-css\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\SERVER\\www\\Keuangan\\node_modules\\camelcase-css\";}s:12:\"caniuse-lite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"caniuse-lite\";s:10:\"\0*\0version\";s:12:\"1.0.30001810\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\node_modules\\caniuse-lite\";}s:8:\"chokidar\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"chokidar\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\node_modules\\chokidar\";}s:5:\"cliui\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"cliui\";s:10:\"\0*\0version\";s:5:\"9.0.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"D:\\SERVER\\www\\Keuangan\\node_modules\\cliui\";}s:12:\"concurrently\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"concurrently\";s:10:\"\0*\0version\";s:6:\"10.0.5\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^10.0.3\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\node_modules\\concurrently\";}s:6:\"cssesc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"cssesc\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"D:\\SERVER\\www\\Keuangan\\node_modules\\cssesc\";}s:11:\"detect-libc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"detect-libc\";s:10:\"\0*\0version\";s:5:\"2.1.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\node_modules\\detect-libc\";}s:10:\"didyoumean\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"didyoumean\";s:10:\"\0*\0version\";s:5:\"1.2.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\node_modules\\didyoumean\";}s:3:\"dlv\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"dlv\";s:10:\"\0*\0version\";s:5:\"1.1.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"D:\\SERVER\\www\\Keuangan\\node_modules\\dlv\";}s:20:\"electron-to-chromium\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"electron-to-chromium\";s:10:\"\0*\0version\";s:7:\"1.5.420\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\SERVER\\www\\Keuangan\\node_modules\\electron-to-chromium\";}s:11:\"emoji-regex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"emoji-regex\";s:10:\"\0*\0version\";s:6:\"10.6.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\node_modules\\emoji-regex\";}s:16:\"enhanced-resolve\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"enhanced-resolve\";s:10:\"\0*\0version\";s:6:\"5.24.5\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\SERVER\\www\\Keuangan\\node_modules\\enhanced-resolve\";}s:9:\"es-errors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"es-errors\";s:10:\"\0*\0version\";s:5:\"1.3.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\node_modules\\es-errors\";}s:8:\"escalade\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"escalade\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\node_modules\\escalade\";}s:9:\"fast-glob\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"fast-glob\";s:10:\"\0*\0version\";s:5:\"3.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\node_modules\\fast-glob\";}s:5:\"fastq\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"fastq\";s:10:\"\0*\0version\";s:6:\"1.20.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"D:\\SERVER\\www\\Keuangan\\node_modules\\fastq\";}s:10:\"fill-range\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"fill-range\";s:10:\"\0*\0version\";s:5:\"7.1.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\node_modules\\fill-range\";}s:11:\"fraction.js\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"fraction.js\";s:10:\"\0*\0version\";s:5:\"5.3.4\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\node_modules\\fraction.js\";}s:8:\"fsevents\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"fsevents\";s:10:\"\0*\0version\";s:5:\"2.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\node_modules\\fsevents\";}s:13:\"function-bind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"function-bind\";s:10:\"\0*\0version\";s:5:\"1.1.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\SERVER\\www\\Keuangan\\node_modules\\function-bind\";}s:15:\"get-caller-file\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"get-caller-file\";s:10:\"\0*\0version\";s:5:\"2.0.5\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\SERVER\\www\\Keuangan\\node_modules\\get-caller-file\";}s:11:\"glob-parent\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"glob-parent\";s:10:\"\0*\0version\";s:5:\"6.0.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\node_modules\\glob-parent\";}s:11:\"graceful-fs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"graceful-fs\";s:10:\"\0*\0version\";s:6:\"4.2.11\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\node_modules\\graceful-fs\";}s:6:\"hasown\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"hasown\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"D:\\SERVER\\www\\Keuangan\\node_modules\\hasown\";}s:14:\"is-binary-path\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"is-binary-path\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\SERVER\\www\\Keuangan\\node_modules\\is-binary-path\";}s:14:\"is-core-module\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"is-core-module\";s:10:\"\0*\0version\";s:6:\"2.16.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\SERVER\\www\\Keuangan\\node_modules\\is-core-module\";}s:10:\"is-extglob\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"is-extglob\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\node_modules\\is-extglob\";}s:7:\"is-glob\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"is-glob\";s:10:\"\0*\0version\";s:5:\"4.0.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"D:\\SERVER\\www\\Keuangan\\node_modules\\is-glob\";}s:9:\"is-number\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"is-number\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\node_modules\\is-number\";}s:4:\"jiti\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"jiti\";s:10:\"\0*\0version\";s:5:\"2.7.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"D:\\SERVER\\www\\Keuangan\\node_modules\\jiti\";}s:19:\"laravel-vite-plugin\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"laravel-vite-plugin\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.1\";s:7:\"\0*\0path\";s:55:\"D:\\SERVER\\www\\Keuangan\\node_modules\\laravel-vite-plugin\";}s:12:\"lightningcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"lightningcss\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\node_modules\\lightningcss\";}s:26:\"lightningcss-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"lightningcss-android-arm64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"D:\\SERVER\\www\\Keuangan\\node_modules\\lightningcss-android-arm64\";}s:25:\"lightningcss-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"lightningcss-darwin-arm64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\SERVER\\www\\Keuangan\\node_modules\\lightningcss-darwin-arm64\";}s:23:\"lightningcss-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"lightningcss-darwin-x64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\SERVER\\www\\Keuangan\\node_modules\\lightningcss-darwin-x64\";}s:24:\"lightningcss-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"lightningcss-freebsd-x64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"D:\\SERVER\\www\\Keuangan\\node_modules\\lightningcss-freebsd-x64\";}s:32:\"lightningcss-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"lightningcss-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"D:\\SERVER\\www\\Keuangan\\node_modules\\lightningcss-linux-arm-gnueabihf\";}s:28:\"lightningcss-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"lightningcss-linux-arm64-gnu\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"D:\\SERVER\\www\\Keuangan\\node_modules\\lightningcss-linux-arm64-gnu\";}s:29:\"lightningcss-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"lightningcss-linux-arm64-musl\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\SERVER\\www\\Keuangan\\node_modules\\lightningcss-linux-arm64-musl\";}s:26:\"lightningcss-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"lightningcss-linux-x64-gnu\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"D:\\SERVER\\www\\Keuangan\\node_modules\\lightningcss-linux-x64-gnu\";}s:27:\"lightningcss-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"lightningcss-linux-x64-musl\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\SERVER\\www\\Keuangan\\node_modules\\lightningcss-linux-x64-musl\";}s:29:\"lightningcss-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"lightningcss-win32-arm64-msvc\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\SERVER\\www\\Keuangan\\node_modules\\lightningcss-win32-arm64-msvc\";}s:27:\"lightningcss-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"lightningcss-win32-x64-msvc\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\SERVER\\www\\Keuangan\\node_modules\\lightningcss-win32-x64-msvc\";}s:9:\"lilconfig\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"lilconfig\";s:10:\"\0*\0version\";s:5:\"3.1.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\node_modules\\lilconfig\";}s:17:\"lines-and-columns\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"lines-and-columns\";s:10:\"\0*\0version\";s:5:\"1.2.4\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\SERVER\\www\\Keuangan\\node_modules\\lines-and-columns\";}s:12:\"magic-string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"magic-string\";s:10:\"\0*\0version\";s:7:\"0.30.21\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\node_modules\\magic-string\";}s:6:\"merge2\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"merge2\";s:10:\"\0*\0version\";s:5:\"1.4.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"D:\\SERVER\\www\\Keuangan\\node_modules\\merge2\";}s:10:\"micromatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"micromatch\";s:10:\"\0*\0version\";s:5:\"4.0.8\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\node_modules\\micromatch\";}s:17:\"mini-svg-data-uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"mini-svg-data-uri\";s:10:\"\0*\0version\";s:5:\"1.4.4\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\SERVER\\www\\Keuangan\\node_modules\\mini-svg-data-uri\";}s:2:\"mz\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:2:\"mz\";s:10:\"\0*\0version\";s:5:\"2.7.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"D:\\SERVER\\www\\Keuangan\\node_modules\\mz\";}s:6:\"nanoid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"nanoid\";s:10:\"\0*\0version\";s:6:\"3.3.18\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"D:\\SERVER\\www\\Keuangan\\node_modules\\nanoid\";}s:13:\"node-releases\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"node-releases\";s:10:\"\0*\0version\";s:6:\"2.0.54\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\SERVER\\www\\Keuangan\\node_modules\\node-releases\";}s:14:\"normalize-path\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"normalize-path\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\SERVER\\www\\Keuangan\\node_modules\\normalize-path\";}s:13:\"object-assign\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"object-assign\";s:10:\"\0*\0version\";s:5:\"4.1.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\SERVER\\www\\Keuangan\\node_modules\\object-assign\";}s:11:\"object-hash\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"object-hash\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\node_modules\\object-hash\";}s:10:\"path-parse\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"path-parse\";s:10:\"\0*\0version\";s:5:\"1.0.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\node_modules\\path-parse\";}s:10:\"picocolors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"picocolors\";s:10:\"\0*\0version\";s:5:\"1.1.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\node_modules\\picocolors\";}s:9:\"picomatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"picomatch\";s:10:\"\0*\0version\";s:5:\"2.3.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\node_modules\\picomatch\";}s:7:\"pirates\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"pirates\";s:10:\"\0*\0version\";s:5:\"4.0.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"D:\\SERVER\\www\\Keuangan\\node_modules\\pirates\";}s:7:\"postcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"postcss\";s:10:\"\0*\0version\";s:6:\"8.5.26\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^8.4.31\";s:7:\"\0*\0path\";s:43:\"D:\\SERVER\\www\\Keuangan\\node_modules\\postcss\";}s:14:\"postcss-import\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"postcss-import\";s:10:\"\0*\0version\";s:6:\"15.1.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\SERVER\\www\\Keuangan\\node_modules\\postcss-import\";}s:10:\"postcss-js\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"postcss-js\";s:10:\"\0*\0version\";s:5:\"4.1.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\node_modules\\postcss-js\";}s:19:\"postcss-load-config\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"postcss-load-config\";s:10:\"\0*\0version\";s:5:\"6.0.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\SERVER\\www\\Keuangan\\node_modules\\postcss-load-config\";}s:14:\"postcss-nested\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"postcss-nested\";s:10:\"\0*\0version\";s:5:\"6.2.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\SERVER\\www\\Keuangan\\node_modules\\postcss-nested\";}s:23:\"postcss-selector-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"postcss-selector-parser\";s:10:\"\0*\0version\";s:5:\"6.1.4\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\SERVER\\www\\Keuangan\\node_modules\\postcss-selector-parser\";}s:20:\"postcss-value-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"postcss-value-parser\";s:10:\"\0*\0version\";s:5:\"4.2.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\SERVER\\www\\Keuangan\\node_modules\\postcss-value-parser\";}s:15:\"queue-microtask\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"queue-microtask\";s:10:\"\0*\0version\";s:5:\"1.2.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\SERVER\\www\\Keuangan\\node_modules\\queue-microtask\";}s:10:\"read-cache\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"read-cache\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\node_modules\\read-cache\";}s:8:\"readdirp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"readdirp\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\node_modules\\readdirp\";}s:7:\"resolve\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"resolve\";s:10:\"\0*\0version\";s:7:\"1.22.12\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"D:\\SERVER\\www\\Keuangan\\node_modules\\resolve\";}s:7:\"reusify\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"reusify\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"D:\\SERVER\\www\\Keuangan\\node_modules\\reusify\";}s:8:\"rolldown\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"rolldown\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\SERVER\\www\\Keuangan\\node_modules\\rolldown\";}s:12:\"run-parallel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"run-parallel\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\node_modules\\run-parallel\";}s:4:\"rxjs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"rxjs\";s:10:\"\0*\0version\";s:5:\"7.8.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"D:\\SERVER\\www\\Keuangan\\node_modules\\rxjs\";}s:11:\"shell-quote\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"shell-quote\";s:10:\"\0*\0version\";s:5:\"1.9.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\node_modules\\shell-quote\";}s:13:\"source-map-js\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"source-map-js\";s:10:\"\0*\0version\";s:5:\"1.2.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\SERVER\\www\\Keuangan\\node_modules\\source-map-js\";}s:7:\"sucrase\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"sucrase\";s:10:\"\0*\0version\";s:6:\"3.35.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"D:\\SERVER\\www\\Keuangan\\node_modules\\sucrase\";}s:14:\"supports-color\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"supports-color\";s:10:\"\0*\0version\";s:6:\"10.2.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\SERVER\\www\\Keuangan\\node_modules\\supports-color\";}s:31:\"supports-preserve-symlinks-flag\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"supports-preserve-symlinks-flag\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"D:\\SERVER\\www\\Keuangan\\node_modules\\supports-preserve-symlinks-flag\";}s:11:\"tailwindcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"tailwindcss\";s:10:\"\0*\0version\";s:6:\"3.4.19\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^3.1.0\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\node_modules\\tailwindcss\";}s:7:\"tapable\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"tapable\";s:10:\"\0*\0version\";s:5:\"2.3.3\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"D:\\SERVER\\www\\Keuangan\\node_modules\\tapable\";}s:7:\"thenify\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"thenify\";s:10:\"\0*\0version\";s:5:\"3.3.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"D:\\SERVER\\www\\Keuangan\\node_modules\\thenify\";}s:11:\"thenify-all\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"thenify-all\";s:10:\"\0*\0version\";s:5:\"1.6.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\SERVER\\www\\Keuangan\\node_modules\\thenify-all\";}s:10:\"tinyglobby\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"tinyglobby\";s:10:\"\0*\0version\";s:6:\"0.2.17\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\SERVER\\www\\Keuangan\\node_modules\\tinyglobby\";}s:14:\"to-regex-range\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"to-regex-range\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\SERVER\\www\\Keuangan\\node_modules\\to-regex-range\";}s:9:\"tree-kill\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"tree-kill\";s:10:\"\0*\0version\";s:5:\"1.2.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\SERVER\\www\\Keuangan\\node_modules\\tree-kill\";}s:20:\"ts-interface-checker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"ts-interface-checker\";s:10:\"\0*\0version\";s:6:\"0.1.13\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\SERVER\\www\\Keuangan\\node_modules\\ts-interface-checker\";}s:5:\"tslib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"tslib\";s:10:\"\0*\0version\";s:5:\"2.8.1\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"D:\\SERVER\\www\\Keuangan\\node_modules\\tslib\";}s:22:\"update-browserslist-db\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"update-browserslist-db\";s:10:\"\0*\0version\";s:5:\"1.3.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\SERVER\\www\\Keuangan\\node_modules\\update-browserslist-db\";}s:14:\"util-deprecate\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"util-deprecate\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\SERVER\\www\\Keuangan\\node_modules\\util-deprecate\";}s:4:\"vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"vite\";s:10:\"\0*\0version\";s:5:\"8.2.2\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^8.0.0\";s:7:\"\0*\0path\";s:40:\"D:\\SERVER\\www\\Keuangan\\node_modules\\vite\";}s:23:\"vite-plugin-full-reload\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"vite-plugin-full-reload\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\SERVER\\www\\Keuangan\\node_modules\\vite-plugin-full-reload\";}s:4:\"y18n\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"y18n\";s:10:\"\0*\0version\";s:5:\"5.0.8\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"D:\\SERVER\\www\\Keuangan\\node_modules\\y18n\";}s:5:\"yargs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"yargs\";s:10:\"\0*\0version\";s:6:\"18.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"D:\\SERVER\\www\\Keuangan\\node_modules\\yargs\";}s:12:\"yargs-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"yargs-parser\";s:10:\"\0*\0version\";s:6:\"22.0.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\SERVER\\www\\Keuangan\\node_modules\\yargs-parser\";}s:4:\"fdir\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"fdir\";s:10:\"\0*\0version\";s:5:\"6.5.0\";s:9:\"\0*\0source\";r:1174;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"D:\\SERVER\\www\\Keuangan\\node_modules\\fdir\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:190:{i:0;r:1171;i:1;r:1179;i:2;r:1187;i:3;r:1195;i:4;r:1203;i:5;r:1211;i:6;r:1219;i:7;r:1227;i:8;r:1235;i:9;r:1243;i:10;r:1251;i:11;r:1259;i:12;r:1267;i:13;r:1275;i:14;r:1283;i:15;r:1291;i:16;r:1299;i:17;r:1307;i:18;r:1315;i:19;r:1323;i:20;r:1331;i:21;r:1339;i:22;r:1347;i:23;r:1355;i:24;r:1363;i:25;r:1371;i:26;r:1379;i:27;r:1387;i:28;r:1395;i:29;r:1403;i:30;r:1411;i:31;r:1419;i:32;r:1427;i:33;r:1435;i:34;r:1443;i:35;r:1451;i:36;r:1459;i:37;r:1467;i:38;r:1475;i:39;r:1483;i:40;r:1491;i:41;r:1499;i:42;r:1507;i:43;r:1515;i:44;r:1523;i:45;r:1531;i:46;r:1539;i:47;r:1547;i:48;r:1555;i:49;r:1563;i:50;r:1571;i:51;r:1579;i:52;r:1587;i:53;r:1595;i:54;r:1603;i:55;r:1611;i:56;r:1619;i:57;r:1627;i:58;r:1635;i:59;r:1643;i:60;r:1651;i:61;r:1659;i:62;r:1667;i:63;r:1675;i:64;r:1683;i:65;r:1691;i:66;r:1699;i:67;r:1707;i:68;r:1715;i:69;r:1723;i:70;r:1731;i:71;r:1739;i:72;r:1747;i:73;r:1755;i:74;r:1763;i:75;r:1771;i:76;r:1779;i:77;r:1787;i:78;r:1795;i:79;r:1803;i:80;r:1811;i:81;r:1819;i:82;r:1827;i:83;r:1835;i:84;r:1843;i:85;r:1851;i:86;r:1859;i:87;r:1867;i:88;r:1875;i:89;r:1883;i:90;r:1891;i:91;r:1899;i:92;r:1907;i:93;r:1915;i:94;r:1923;i:95;r:1931;i:96;r:1939;i:97;r:1947;i:98;r:1955;i:99;r:1963;i:100;r:1971;i:101;r:1979;i:102;r:1987;i:103;r:1995;i:104;r:2003;i:105;r:2011;i:106;r:2019;i:107;r:2027;i:108;r:2035;i:109;r:2043;i:110;r:2051;i:111;r:2059;i:112;r:2067;i:113;r:2075;i:114;r:2083;i:115;r:2091;i:116;r:2099;i:117;r:2107;i:118;r:2115;i:119;r:2123;i:120;r:2131;i:121;r:2139;i:122;r:2147;i:123;r:2155;i:124;r:2163;i:125;r:2171;i:126;r:2179;i:127;r:2187;i:128;r:2195;i:129;r:2203;i:130;r:2211;i:131;r:2219;i:132;r:2227;i:133;r:2235;i:134;r:2243;i:135;r:2251;i:136;r:2259;i:137;r:2267;i:138;r:2275;i:139;r:2283;i:140;r:2291;i:141;r:2299;i:142;r:2307;i:143;r:2315;i:144;r:2323;i:145;r:2331;i:146;r:2339;i:147;r:2347;i:148;r:2355;i:149;r:2363;i:150;r:2371;i:151;r:2379;i:152;r:2387;i:153;r:2395;i:154;r:2403;i:155;r:2411;i:156;r:2419;i:157;r:2427;i:158;r:2435;i:159;r:2443;i:160;r:2451;i:161;r:2459;i:162;r:2467;i:163;r:2475;i:164;r:2483;i:165;r:2491;i:166;r:2499;i:167;r:2507;i:168;r:2515;i:169;r:2523;i:170;r:2531;i:171;r:2539;i:172;r:2547;i:173;r:2555;i:174;r:2563;i:175;r:2571;i:176;r:2579;i:177;r:2587;i:178;r:2595;i:179;r:2603;i:180;r:2611;i:181;r:2619;i:182;r:2627;i:183;r:2635;i:184;r:2643;i:185;r:2651;i:186;r:2659;i:187;r:2667;i:188;r:2675;i:189;r:2683;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:17:\"\0*\0packageManager\";E:41:\"Laravel\\Roster\\Enums\\JsPackageManager:Npm\";}s:6:\"stacks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:1:{i:0;E:32:\"Laravel\\Roster\\Enums\\Stack:Blade\";}}s:21:\"browserTestFrameworks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:9:\"frontends\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:6:\"agents\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:5:{i:0;E:37:\"Laravel\\Roster\\Enums\\Agent:ClaudeCode\";i:1;E:33:\"Laravel\\Roster\\Enums\\Agent:Cursor\";i:2;E:32:\"Laravel\\Roster\\Enums\\Agent:Codex\";i:3;E:31:\"Laravel\\Roster\\Enums\\Agent:Kiro\";i:4;E:35:\"Laravel\\Roster\\Enums\\Agent:OpenCode\";}}s:7:\"editors\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}}', 1789911433);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `room_id` bigint UNSIGNED NOT NULL,
  `sender_id` bigint UNSIGNED DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `moderated_at` timestamp NULL DEFAULT NULL,
  `moderated_by` bigint UNSIGNED DEFAULT NULL,
  `moderated_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `room_id`, `sender_id`, `body`, `type`, `moderated_at`, `moderated_by`, `moderated_reason`, `created_at`, `updated_at`) VALUES
(4, 1, 4, 'Halo', 'user', NULL, NULL, NULL, '2026-09-15 19:19:47', '2026-09-15 19:19:47'),
(5, 1, 2, 'Halo', 'user', NULL, NULL, NULL, '2026-09-15 19:19:58', '2026-09-15 19:19:58'),
(6, 1, 4, '😀', 'user', NULL, NULL, NULL, '2026-09-15 19:57:51', '2026-09-15 19:57:51'),
(7, 1, 2, 'Test', 'user', NULL, NULL, NULL, '2026-09-15 19:58:07', '2026-09-15 19:58:07'),
(8, 1, 4, 'Test Chat', 'user', NULL, NULL, NULL, '2026-09-15 19:58:49', '2026-09-15 19:58:49'),
(9, 1, 4, 'Test', 'user', NULL, NULL, NULL, '2026-09-15 19:59:19', '2026-09-15 19:59:19'),
(10, 1, 2, 'Test', 'user', NULL, NULL, NULL, '2026-09-15 20:02:12', '2026-09-15 20:02:12'),
(11, 1, 4, 'Halo', 'user', NULL, NULL, NULL, '2026-09-15 20:38:15', '2026-09-15 20:38:15'),
(12, 1, 2, 'halo', 'user', NULL, NULL, NULL, '2026-09-15 20:38:24', '2026-09-15 20:38:24'),
(13, 1, 6, 'laiyo\nngene loh', 'user', NULL, NULL, NULL, '2026-09-15 21:50:06', '2026-09-15 21:50:06'),
(14, 1, 2, 'wkwk layo 😂', 'user', NULL, NULL, NULL, '2026-09-15 22:42:44', '2026-09-15 22:42:44'),
(15, 1, 10, 'Halo', 'user', NULL, NULL, NULL, '2026-09-16 20:30:10', '2026-09-16 20:30:10'),
(16, 1, 2, '🔥🔥🔥🔥', 'user', NULL, NULL, NULL, '2026-09-16 20:47:23', '2026-09-16 20:47:23'),
(17, 1, 2, 'halo', 'user', NULL, NULL, NULL, '2026-09-17 06:13:15', '2026-09-17 06:13:15'),
(18, 1, 4, 'halo', 'user', NULL, NULL, NULL, '2026-09-17 06:15:06', '2026-09-17 06:15:06'),
(19, 1, 4, '🔥🔥🔥🔥', 'user', NULL, NULL, NULL, '2026-09-17 06:15:12', '2026-09-17 06:15:12'),
(20, 1, 2, '😮', 'user', NULL, NULL, NULL, '2026-09-17 06:15:20', '2026-09-17 06:15:20'),
(21, 1, 2, '✅', 'user', NULL, NULL, NULL, '2026-09-17 06:15:29', '2026-09-17 06:15:29'),
(22, 1, 2, '❤️', 'user', NULL, NULL, NULL, '2026-09-17 06:15:35', '2026-09-17 06:15:35'),
(23, 1, 8, 'Ini kenapa ya', 'user', NULL, NULL, NULL, '2026-09-18 05:00:18', '2026-09-18 05:00:18'),
(24, 1, 13, 'koyoe lk nggawe manipulasi DOM tambah smooth iki', 'user', NULL, NULL, NULL, '2026-09-19 03:52:42', '2026-09-19 03:52:42'),
(25, 1, 2, 'oke tampung, akan segera diimplementasikan', 'user', NULL, NULL, NULL, '2026-09-19 06:17:54', '2026-09-19 06:17:54');

-- --------------------------------------------------------

--
-- Table structure for table `chat_reports`
--

CREATE TABLE `chat_reports` (
  `id` bigint UNSIGNED NOT NULL,
  `message_id` bigint UNSIGNED NOT NULL,
  `reporter_id` bigint UNSIGNED NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `handled_by` bigint UNSIGNED DEFAULT NULL,
  `handled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_rooms`
--

CREATE TABLE `chat_rooms` (
  `id` bigint UNSIGNED NOT NULL,
  `tipe` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dm',
  `user_a_id` bigint UNSIGNED DEFAULT NULL,
  `user_b_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_rooms`
--

INSERT INTO `chat_rooms` (`id`, `tipe`, `user_a_id`, `user_b_id`, `created_at`, `updated_at`) VALUES
(1, 'global', NULL, NULL, '2026-09-15 05:10:14', '2026-09-19 06:17:54');

-- --------------------------------------------------------

--
-- Table structure for table `chat_room_reads`
--

CREATE TABLE `chat_room_reads` (
  `user_id` bigint UNSIGNED NOT NULL,
  `room_id` bigint UNSIGNED NOT NULL,
  `last_read_message_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_room_reads`
--

INSERT INTO `chat_room_reads` (`user_id`, `room_id`, `last_read_message_id`, `created_at`, `updated_at`) VALUES
(2, 1, 25, '2026-09-15 19:19:54', '2026-09-20 05:39:51'),
(3, 1, 16, '2026-09-15 21:16:25', '2026-09-16 21:16:10'),
(4, 1, 23, '2026-09-15 19:19:47', '2026-09-18 06:16:38'),
(6, 1, 14, '2026-09-15 21:49:50', '2026-09-16 01:48:28'),
(8, 1, 25, '2026-09-16 00:57:35', '2026-09-21 07:35:24'),
(9, 1, 15, '2026-09-16 20:08:04', '2026-09-16 20:30:27'),
(10, 1, 16, '2026-09-16 20:29:38', '2026-09-16 22:08:01'),
(11, 1, 16, '2026-09-17 02:57:52', '2026-09-17 02:57:57'),
(13, 1, 24, '2026-09-19 03:49:35', '2026-09-19 03:52:47');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telepon` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `is_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `kode`, `nama`, `alamat`, `telepon`, `email`, `npwp`, `keterangan`, `is_aktif`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-15 01:26:29', '2026-09-15 01:26:29', NULL),
(2, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-15 01:27:17', '2026-09-15 01:27:17', 1),
(3, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(4, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(5, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-15 18:42:59', '2026-09-15 18:42:59', 4),
(6, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 5),
(7, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(8, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(9, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 8),
(10, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(11, 'UMUM', 'SUPRA DINASTY AYANI', NULL, '+62 818-0552-6744', NULL, NULL, NULL, 1, '2026-09-16 20:28:23', '2026-09-17 03:41:04', 10),
(12, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(13, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(14, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(15, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 14);

-- --------------------------------------------------------

--
-- Table structure for table `daftar_harga`
--

CREATE TABLE `daftar_harga` (
  `id` bigint UNSIGNED NOT NULL,
  `entitas` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'supplier',
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `harga` decimal(18,2) NOT NULL,
  `is_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `min_qty` decimal(12,2) NOT NULL DEFAULT '1.00',
  `max_qty` decimal(12,2) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `barang_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `daftar_harga`
--

INSERT INTO `daftar_harga` (`id`, `entitas`, `supplier_id`, `customer_id`, `harga`, `is_aktif`, `min_qty`, `max_qty`, `tanggal_mulai`, `tanggal_selesai`, `keterangan`, `created_at`, `updated_at`, `barang_id`, `user_id`) VALUES
(1, 'supplier', 3, NULL, 2000.00, 1, 1.00, NULL, '2026-09-16', NULL, NULL, '2026-09-15 21:09:01', '2026-09-15 21:09:01', 1, 2),
(2, 'customer', NULL, 3, 2500.00, 1, 1.00, NULL, '2026-09-16', NULL, NULL, '2026-09-15 21:09:01', '2026-09-15 21:09:01', 1, 2),
(3, 'supplier', 7, NULL, 62500.00, 1, 1.00, NULL, '2026-09-16', NULL, NULL, '2026-09-15 21:52:08', '2026-09-15 21:52:08', 2, 6),
(4, 'customer', NULL, 7, 66500.00, 1, 1.00, NULL, '2026-09-16', NULL, NULL, '2026-09-15 21:52:08', '2026-09-15 21:52:08', 2, 6),
(5, 'supplier', 3, NULL, 3000.00, 1, 1.00, NULL, '2026-09-17', NULL, NULL, '2026-09-16 20:26:04', '2026-09-16 20:26:04', 3, 2),
(6, 'customer', NULL, 3, 5000.00, 1, 1.00, NULL, '2026-09-17', NULL, NULL, '2026-09-16 20:26:04', '2026-09-16 20:26:04', 3, 2),
(7, 'supplier', 10, NULL, 8000.00, 1, 1.00, NULL, '2026-09-17', NULL, NULL, '2026-09-16 20:28:42', '2026-09-16 20:28:42', 4, 9),
(8, 'customer', NULL, 10, 15000.00, 1, 1.00, NULL, '2026-09-17', NULL, NULL, '2026-09-16 20:28:42', '2026-09-16 20:28:42', 4, 9),
(9, 'supplier', 9, NULL, 60000.00, 1, 1.00, NULL, '2026-09-17', NULL, NULL, '2026-09-16 21:45:12', '2026-09-18 05:02:26', 5, 8),
(10, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-17', NULL, NULL, '2026-09-16 21:45:12', '2026-09-18 05:02:26', 5, 8),
(11, 'supplier', 9, NULL, 60000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:02:58', '2026-09-18 05:02:58', 6, 8),
(12, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:02:58', '2026-09-18 05:02:58', 6, 8),
(13, 'supplier', 9, NULL, 60000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:03:28', '2026-09-18 05:03:28', 7, 8),
(14, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:03:28', '2026-09-18 05:03:28', 7, 8),
(15, 'supplier', 9, NULL, 60000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:03:58', '2026-09-18 05:03:58', 8, 8),
(16, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:03:58', '2026-09-18 05:03:58', 8, 8),
(17, 'supplier', 9, NULL, 60000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:04:21', '2026-09-18 05:04:21', 9, 8),
(18, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:04:21', '2026-09-18 05:04:21', 9, 8),
(19, 'supplier', 9, NULL, 60000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:04:47', '2026-09-18 05:04:47', 10, 8),
(20, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:04:47', '2026-09-18 05:04:47', 10, 8),
(21, 'supplier', 9, NULL, 60000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:05:12', '2026-09-18 05:05:12', 11, 8),
(22, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:05:12', '2026-09-18 05:05:12', 11, 8),
(23, 'supplier', 9, NULL, 60000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:05:43', '2026-09-18 05:05:43', 12, 8),
(24, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:05:43', '2026-09-18 05:05:43', 12, 8),
(25, 'supplier', 9, NULL, 60000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:06:12', '2026-09-18 05:06:12', 13, 8),
(26, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:06:12', '2026-09-18 05:06:12', 13, 8),
(27, 'supplier', 9, NULL, 60000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:06:35', '2026-09-18 05:06:35', 14, 8),
(28, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:06:35', '2026-09-18 05:06:35', 14, 8),
(29, 'supplier', 9, NULL, 60000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:07:01', '2026-09-18 05:07:01', 15, 8),
(30, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:07:01', '2026-09-18 05:07:01', 15, 8),
(31, 'supplier', 9, NULL, 60500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:22:42', '2026-09-18 05:22:42', 16, 8),
(32, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:22:42', '2026-09-18 05:22:42', 16, 8),
(33, 'supplier', 9, NULL, 60500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:23:50', '2026-09-18 05:23:50', 17, 8),
(34, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:23:50', '2026-09-18 05:23:50', 17, 8),
(35, 'supplier', 9, NULL, 60500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:24:35', '2026-09-18 05:24:35', 18, 8),
(36, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:24:35', '2026-09-18 05:24:35', 18, 8),
(37, 'supplier', 9, NULL, 60500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:25:06', '2026-09-18 05:25:06', 19, 8),
(38, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:25:06', '2026-09-18 05:25:06', 19, 8),
(39, 'supplier', 9, NULL, 60500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:25:35', '2026-09-18 05:25:35', 20, 8),
(40, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:25:35', '2026-09-18 05:25:35', 20, 8),
(41, 'supplier', 9, NULL, 60500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:26:55', '2026-09-18 05:26:55', 21, 8),
(42, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:26:55', '2026-09-18 05:26:55', 21, 8),
(43, 'supplier', 9, NULL, 60500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:28:25', '2026-09-18 05:28:25', 22, 8),
(44, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:28:25', '2026-09-18 05:28:25', 22, 8),
(45, 'supplier', 9, NULL, 60500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:28:47', '2026-09-18 05:28:47', 23, 8),
(46, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:28:47', '2026-09-18 05:28:47', 23, 8),
(47, 'supplier', 9, NULL, 60500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:29:10', '2026-09-18 05:29:10', 24, 8),
(48, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:29:10', '2026-09-18 05:29:10', 24, 8),
(49, 'supplier', 9, NULL, 60500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:29:31', '2026-09-18 05:29:31', 25, 8),
(50, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:29:31', '2026-09-18 05:29:31', 25, 8),
(51, 'supplier', 9, NULL, 60000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:30:13', '2026-09-18 05:30:13', 26, 8),
(52, 'customer', NULL, 9, 67000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:30:13', '2026-09-18 05:30:13', 26, 8),
(53, 'supplier', 14, NULL, 60000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 5, 8),
(54, 'supplier', 14, NULL, 60000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 8, 8),
(55, 'supplier', 14, NULL, 60500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 16, 8),
(56, 'supplier', 14, NULL, 60500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 19, 8),
(57, 'supplier', 14, NULL, 60000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 10, 8),
(58, 'supplier', 14, NULL, 60500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 21, 8),
(59, 'supplier', 14, NULL, 60000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 15, 8),
(60, 'supplier', 14, NULL, 60500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 25, 8),
(61, 'supplier', 14, NULL, 60000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 9, 8),
(62, 'supplier', 14, NULL, 60500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 20, 8),
(63, 'supplier', 14, NULL, 60000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 14, 8),
(64, 'supplier', 14, NULL, 60500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 24, 8),
(65, 'supplier', 14, NULL, 61500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 26, 8),
(66, 'supplier', 5, NULL, 2000.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 06:21:04', '2026-09-18 06:21:04', 27, 4),
(67, 'customer', NULL, 5, 3500.00, 1, 1.00, NULL, '2026-09-18', NULL, NULL, '2026-09-18 06:21:04', '2026-09-18 06:21:04', 27, 4),
(68, 'supplier', 16, NULL, 2000.00, 1, 1.00, NULL, '2026-09-20', NULL, NULL, '2026-09-20 07:25:49', '2026-09-20 07:29:30', 28, 14),
(69, 'customer', NULL, 15, 2000.00, 1, 1.00, NULL, '2026-09-20', NULL, NULL, '2026-09-20 07:25:49', '2026-09-20 07:25:49', 28, 14);

-- --------------------------------------------------------

--
-- Table structure for table `daftar_harga_riwayat`
--

CREATE TABLE `daftar_harga_riwayat` (
  `id` bigint UNSIGNED NOT NULL,
  `entitas` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barang_id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `tipe` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_lama` decimal(15,2) DEFAULT NULL,
  `harga_baru` decimal(15,2) DEFAULT NULL,
  `min_qty_lama` decimal(15,2) DEFAULT NULL,
  `max_qty_lama` decimal(15,2) DEFAULT NULL,
  `min_qty_baru` decimal(15,2) DEFAULT NULL,
  `max_qty_baru` decimal(15,2) DEFAULT NULL,
  `tanggal_mulai_lama` date DEFAULT NULL,
  `tanggal_mulai_baru` date DEFAULT NULL,
  `catatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `daftar_harga_riwayat`
--

INSERT INTO `daftar_harga_riwayat` (`id`, `entitas`, `barang_id`, `supplier_id`, `customer_id`, `tipe`, `harga_lama`, `harga_baru`, `min_qty_lama`, `max_qty_lama`, `min_qty_baru`, `max_qty_baru`, `tanggal_mulai_lama`, `tanggal_mulai_baru`, `catatan`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'supplier', 1, 3, NULL, 'buat', NULL, 2000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-16', 'Dibuat dari transaksi pembelian', '2026-09-15 21:09:01', '2026-09-15 21:09:01', 2),
(2, 'customer', 1, NULL, 3, 'buat', NULL, 2500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-16', 'Dibuat dari transaksi penjualan', '2026-09-15 21:09:01', '2026-09-15 21:09:01', 2),
(3, 'supplier', 2, 7, NULL, 'buat', NULL, 62500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-16', 'Dibuat dari transaksi pembelian', '2026-09-15 21:52:08', '2026-09-15 21:52:08', 6),
(4, 'customer', 2, NULL, 7, 'buat', NULL, 66500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-16', 'Dibuat dari transaksi penjualan', '2026-09-15 21:52:08', '2026-09-15 21:52:08', 6),
(5, 'supplier', 3, 3, NULL, 'buat', NULL, 3000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-17', 'Dibuat dari transaksi pembelian', '2026-09-16 20:26:04', '2026-09-16 20:26:04', 2),
(6, 'customer', 3, NULL, 3, 'buat', NULL, 5000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-17', 'Dibuat dari transaksi penjualan', '2026-09-16 20:26:04', '2026-09-16 20:26:04', 2),
(7, 'supplier', 4, 10, NULL, 'buat', NULL, 8000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-17', 'Dibuat dari transaksi pembelian', '2026-09-16 20:28:42', '2026-09-16 20:28:42', 9),
(8, 'customer', 4, NULL, 10, 'buat', NULL, 15000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-17', 'Dibuat dari transaksi penjualan', '2026-09-16 20:28:42', '2026-09-16 20:28:42', 9),
(9, 'supplier', 5, 9, NULL, 'buat', NULL, 62500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-17', 'Dibuat dari transaksi pembelian', '2026-09-16 21:45:12', '2026-09-16 21:45:12', 8),
(10, 'customer', 5, NULL, 9, 'buat', NULL, 66500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-17', 'Dibuat dari transaksi penjualan', '2026-09-16 21:45:12', '2026-09-16 21:45:12', 8),
(11, 'supplier', 5, 9, NULL, 'ubah', 62500.00, 60000.00, 1.00, NULL, 1.00, NULL, '2026-09-17', '2026-09-17', 'Diperbarui otomatis dari transaksi pembelian', '2026-09-18 05:02:26', '2026-09-18 05:02:26', 8),
(12, 'customer', 5, NULL, 9, 'ubah', 66500.00, 67000.00, 1.00, NULL, 1.00, NULL, '2026-09-17', '2026-09-17', 'Diperbarui otomatis dari transaksi penjualan', '2026-09-18 05:02:26', '2026-09-18 05:02:26', 8),
(13, 'supplier', 6, 9, NULL, 'buat', NULL, 60000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:02:58', '2026-09-18 05:02:58', 8),
(14, 'customer', 6, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:02:58', '2026-09-18 05:02:58', 8),
(15, 'supplier', 7, 9, NULL, 'buat', NULL, 60000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:03:28', '2026-09-18 05:03:28', 8),
(16, 'customer', 7, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:03:28', '2026-09-18 05:03:28', 8),
(17, 'supplier', 8, 9, NULL, 'buat', NULL, 60000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:03:58', '2026-09-18 05:03:58', 8),
(18, 'customer', 8, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:03:58', '2026-09-18 05:03:58', 8),
(19, 'supplier', 9, 9, NULL, 'buat', NULL, 60000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:04:21', '2026-09-18 05:04:21', 8),
(20, 'customer', 9, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:04:21', '2026-09-18 05:04:21', 8),
(21, 'supplier', 10, 9, NULL, 'buat', NULL, 60000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:04:47', '2026-09-18 05:04:47', 8),
(22, 'customer', 10, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:04:47', '2026-09-18 05:04:47', 8),
(23, 'supplier', 11, 9, NULL, 'buat', NULL, 60000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:05:12', '2026-09-18 05:05:12', 8),
(24, 'customer', 11, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:05:12', '2026-09-18 05:05:12', 8),
(25, 'supplier', 12, 9, NULL, 'buat', NULL, 60000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:05:43', '2026-09-18 05:05:43', 8),
(26, 'customer', 12, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:05:43', '2026-09-18 05:05:43', 8),
(27, 'supplier', 13, 9, NULL, 'buat', NULL, 60000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:06:12', '2026-09-18 05:06:12', 8),
(28, 'customer', 13, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:06:12', '2026-09-18 05:06:12', 8),
(29, 'supplier', 14, 9, NULL, 'buat', NULL, 60000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:06:35', '2026-09-18 05:06:35', 8),
(30, 'customer', 14, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:06:35', '2026-09-18 05:06:35', 8),
(31, 'supplier', 15, 9, NULL, 'buat', NULL, 60000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:07:01', '2026-09-18 05:07:01', 8),
(32, 'customer', 15, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:07:01', '2026-09-18 05:07:01', 8),
(33, 'supplier', 16, 9, NULL, 'buat', NULL, 60500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:22:42', '2026-09-18 05:22:42', 8),
(34, 'customer', 16, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:22:42', '2026-09-18 05:22:42', 8),
(35, 'supplier', 17, 9, NULL, 'buat', NULL, 60500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:23:50', '2026-09-18 05:23:50', 8),
(36, 'customer', 17, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:23:50', '2026-09-18 05:23:50', 8),
(37, 'supplier', 18, 9, NULL, 'buat', NULL, 60500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:24:35', '2026-09-18 05:24:35', 8),
(38, 'customer', 18, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:24:35', '2026-09-18 05:24:35', 8),
(39, 'supplier', 19, 9, NULL, 'buat', NULL, 60500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:25:06', '2026-09-18 05:25:06', 8),
(40, 'customer', 19, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:25:06', '2026-09-18 05:25:06', 8),
(41, 'supplier', 20, 9, NULL, 'buat', NULL, 60500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:25:35', '2026-09-18 05:25:35', 8),
(42, 'customer', 20, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:25:35', '2026-09-18 05:25:35', 8),
(43, 'supplier', 21, 9, NULL, 'buat', NULL, 60500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:26:55', '2026-09-18 05:26:55', 8),
(44, 'customer', 21, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:26:55', '2026-09-18 05:26:55', 8),
(45, 'supplier', 22, 9, NULL, 'buat', NULL, 60500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:28:25', '2026-09-18 05:28:25', 8),
(46, 'customer', 22, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:28:25', '2026-09-18 05:28:25', 8),
(47, 'supplier', 23, 9, NULL, 'buat', NULL, 60500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:28:47', '2026-09-18 05:28:47', 8),
(48, 'customer', 23, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:28:47', '2026-09-18 05:28:47', 8),
(49, 'supplier', 24, 9, NULL, 'buat', NULL, 60500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:29:10', '2026-09-18 05:29:10', 8),
(50, 'customer', 24, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:29:10', '2026-09-18 05:29:10', 8),
(51, 'supplier', 25, 9, NULL, 'buat', NULL, 60500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:29:31', '2026-09-18 05:29:31', 8),
(52, 'customer', 25, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:29:31', '2026-09-18 05:29:31', 8),
(53, 'supplier', 26, 9, NULL, 'buat', NULL, 60000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:30:13', '2026-09-18 05:30:13', 8),
(54, 'customer', 26, NULL, 9, 'buat', NULL, 67000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 05:30:13', '2026-09-18 05:30:13', 8),
(55, 'supplier', 5, 14, NULL, 'buat', NULL, 60000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:37:34', '2026-09-18 05:37:34', 8),
(56, 'supplier', 8, 14, NULL, 'buat', NULL, 60000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:37:34', '2026-09-18 05:37:34', 8),
(57, 'supplier', 16, 14, NULL, 'buat', NULL, 60500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:37:34', '2026-09-18 05:37:34', 8),
(58, 'supplier', 19, 14, NULL, 'buat', NULL, 60500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:37:34', '2026-09-18 05:37:34', 8),
(59, 'supplier', 10, 14, NULL, 'buat', NULL, 60000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:37:34', '2026-09-18 05:37:34', 8),
(60, 'supplier', 21, 14, NULL, 'buat', NULL, 60500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:37:34', '2026-09-18 05:37:34', 8),
(61, 'supplier', 15, 14, NULL, 'buat', NULL, 60000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:37:34', '2026-09-18 05:37:34', 8),
(62, 'supplier', 25, 14, NULL, 'buat', NULL, 60500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:37:34', '2026-09-18 05:37:34', 8),
(63, 'supplier', 9, 14, NULL, 'buat', NULL, 60000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:37:34', '2026-09-18 05:37:34', 8),
(64, 'supplier', 20, 14, NULL, 'buat', NULL, 60500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:37:34', '2026-09-18 05:37:34', 8),
(65, 'supplier', 14, 14, NULL, 'buat', NULL, 60000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:37:34', '2026-09-18 05:37:34', 8),
(66, 'supplier', 24, 14, NULL, 'buat', NULL, 60500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:37:34', '2026-09-18 05:37:34', 8),
(67, 'supplier', 26, 14, NULL, 'buat', NULL, 61500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 05:37:34', '2026-09-18 05:37:34', 8),
(68, 'supplier', 27, 5, NULL, 'buat', NULL, 2000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi pembelian', '2026-09-18 06:21:04', '2026-09-18 06:21:04', 4),
(69, 'customer', 27, NULL, 5, 'buat', NULL, 3500.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-18', 'Dibuat dari transaksi penjualan', '2026-09-18 06:21:04', '2026-09-18 06:21:04', 4),
(70, 'supplier', 28, 16, NULL, 'buat', NULL, 1000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-20', 'Dibuat dari transaksi pembelian', '2026-09-20 07:25:49', '2026-09-20 07:25:49', 14),
(71, 'customer', 28, NULL, 15, 'buat', NULL, 2000.00, NULL, NULL, 1.00, NULL, NULL, '2026-09-20', 'Dibuat dari transaksi penjualan', '2026-09-20 07:25:49', '2026-09-20 07:25:49', 14),
(72, 'supplier', 28, 16, NULL, 'ubah', 1000.00, 2000.00, 1.00, NULL, 1.00, NULL, '2026-09-20', '2026-09-20', 'Diperbarui otomatis dari transaksi pembelian', '2026-09-20 07:29:30', '2026-09-20 07:29:30', 14);

-- --------------------------------------------------------

--
-- Table structure for table `donasis`
--

CREATE TABLE `donasis` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `nominal` decimal(15,2) DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bukti_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donasis`
--

INSERT INTO `donasis` (`id`, `user_id`, `nominal`, `keterangan`, `bukti_path`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, 'ini untuk njajan', 'bukti-donasi/Cx84GiaMrgGCOqdYFkbsc8XYQC3Fc96AGL7acwPH.jpg', 'pending', '2026-09-15 18:14:12', '2026-09-15 18:14:12');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gudangs`
--

CREATE TABLE `gudangs` (
  `id` bigint UNSIGNED NOT NULL,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `is_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gudangs`
--

INSERT INTO `gudangs` (`id`, `kode`, `nama`, `alamat`, `keterangan`, `is_aktif`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'GDG-0001', 'Gudang Umum', NULL, 'Gudang default untuk semua barang.', 1, '2026-09-15 01:26:31', '2026-09-15 01:26:31', NULL),
(2, 'GDG-0001', 'Gudang Umum', NULL, 'Gudang default untuk semua barang.', 1, '2026-09-15 01:27:17', '2026-09-15 01:27:17', 1),
(3, 'GDG-0001', 'Gudang Umum', NULL, 'Gudang default untuk semua barang.', 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(4, 'GDG-0001', 'Gudang Umum', NULL, 'Gudang default untuk semua barang.', 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(5, 'GDG-0001', 'Gudang Umum', NULL, 'Gudang default untuk semua barang.', 1, '2026-09-15 18:42:59', '2026-09-15 18:42:59', 4),
(6, 'GDG-0001', 'Gudang Umum', NULL, 'Gudang default untuk semua barang.', 1, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 5),
(7, 'GDG-0001', 'Gudang Umum', NULL, 'Gudang default untuk semua barang.', 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(8, 'GDG-0001', 'Gudang Umum', NULL, 'Gudang default untuk semua barang.', 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(9, 'GDG-0001', 'Gudang Umum', NULL, 'Gudang default untuk semua barang.', 1, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 8),
(10, 'GDG-0001', 'Gudang Umum', NULL, 'Gudang default untuk semua barang.', 1, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(11, 'GDG-0001', 'Gudang Umum', NULL, 'Gudang default untuk semua barang.', 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(12, 'GDG-0011', 'Gudang dayu', 'Jl Ciliwung', 'Gudang deket smp', 1, '2026-09-16 20:29:18', '2026-09-16 20:29:18', 9),
(13, 'GDG-0001', 'Gudang Umum', NULL, 'Gudang default untuk semua barang.', 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(14, 'GDG-0001', 'Gudang Umum', NULL, 'Gudang default untuk semua barang.', 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(15, 'GDG-0001', 'Gudang Umum', NULL, 'Gudang default untuk semua barang.', 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(16, 'GDG-0001', 'Gudang Umum', NULL, 'Gudang default untuk semua barang.', 1, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 14);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jurnal_items`
--

CREATE TABLE `jurnal_items` (
  `id` bigint UNSIGNED NOT NULL,
  `jurnal_id` bigint UNSIGNED NOT NULL,
  `akun_id` bigint UNSIGNED NOT NULL,
  `debit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `kredit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jurnal_items`
--

INSERT INTO `jurnal_items` (`id`, `jurnal_id`, `akun_id`, `debit`, `kredit`, `keterangan`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 1, 293, 250000.00, 0.00, 'Saldo Awal Gopay', '2026-09-16 20:12:51', '2026-09-19 05:25:41', 9),
(2, 1, 306, 0.00, 250000.00, 'Setoran Modal Awal Rekening Gopay', '2026-09-16 20:12:51', '2026-09-16 20:12:51', 9),
(3, 2, 293, 105000.00, 0.00, NULL, '2026-09-16 20:30:13', '2026-09-19 05:25:41', 9),
(4, 2, 311, 0.00, 105000.00, NULL, '2026-09-16 20:30:13', '2026-09-16 20:30:13', 9),
(5, 3, 316, 56000.00, 0.00, NULL, '2026-09-16 20:30:13', '2026-09-16 20:30:13', 9),
(6, 3, 296, 0.00, 56000.00, NULL, '2026-09-16 20:30:13', '2026-09-16 20:30:13', 9),
(7, 4, 296, 80000.00, 0.00, NULL, '2026-09-16 20:31:37', '2026-09-16 20:31:37', 9),
(8, 4, 293, 0.00, 80000.00, NULL, '2026-09-16 20:31:37', '2026-09-19 05:25:41', 9),
(9, 5, 365, 1000000.00, 0.00, 'Saldo Awal Bank UOB Indonesia', '2026-09-17 03:01:40', '2026-09-17 03:01:40', 11),
(10, 5, 378, 0.00, 1000000.00, 'Setoran Modal Awal Rekening Bank UOB Indonesia', '2026-09-17 03:01:40', '2026-09-17 03:01:40', 11),
(11, 6, 260, 13187850.00, 0.00, NULL, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 8),
(12, 6, 256, 0.00, 13187850.00, NULL, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 8),
(13, 7, 260, 0.00, 13187850.00, 'BALIK: ', '2026-09-18 05:38:04', '2026-09-18 05:38:04', 8),
(14, 7, 256, 13187850.00, 0.00, 'BALIK: ', '2026-09-18 05:38:04', '2026-09-18 05:38:04', 8),
(15, 8, 260, 13187850.00, 0.00, NULL, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 8),
(16, 8, 285, 659000.00, 0.00, NULL, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 8),
(17, 8, 256, 0.00, 13846850.00, NULL, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 8),
(18, 9, 275, 0.00, 600000.00, 'Clothes', '2026-09-18 05:52:37', '2026-09-18 05:52:37', 8),
(19, 9, 275, 0.00, 150000.00, 'Colection', '2026-09-18 05:52:37', '2026-09-18 05:52:37', 8),
(20, 9, 256, 750000.00, 0.00, NULL, '2026-09-18 05:52:37', '2026-09-18 05:52:37', 8),
(21, 10, 116, 200000.00, 0.00, NULL, '2026-09-18 06:21:39', '2026-09-18 06:21:39', 4),
(22, 10, 112, 0.00, 200000.00, NULL, '2026-09-18 06:21:39', '2026-09-18 06:21:39', 4),
(23, 11, 40, 100000.00, 0.00, 'Saldo Awal Kas Utama', '2026-09-18 19:11:30', '2026-09-18 19:11:30', 2),
(24, 11, 54, 0.00, 100000.00, 'Setoran Modal Awal Rekening Kas Utama', '2026-09-18 19:11:30', '2026-09-18 19:11:30', 2),
(25, 12, 44, 300000.00, 0.00, NULL, '2026-09-18 19:11:54', '2026-09-18 19:11:54', 2),
(26, 12, 41, 0.00, 300000.00, NULL, '2026-09-18 19:11:54', '2026-09-18 19:11:54', 2),
(27, 13, 44, 30000.00, 0.00, NULL, '2026-09-18 19:14:45', '2026-09-18 19:14:45', 2),
(28, 13, 40, 0.00, 30000.00, NULL, '2026-09-18 19:14:45', '2026-09-18 19:14:45', 2),
(29, 14, 44, 300000.00, 0.00, NULL, '2026-09-18 19:15:05', '2026-09-18 19:15:05', 2),
(30, 14, 40, 0.00, 300000.00, NULL, '2026-09-18 19:15:05', '2026-09-18 19:15:05', 2),
(31, 15, 41, 100000.00, 0.00, 'Saldo Awal BRI 2', '2026-09-18 19:15:39', '2026-09-18 19:15:39', 2),
(32, 15, 54, 0.00, 100000.00, 'Setoran Modal Awal Rekening BRI 2', '2026-09-18 19:15:39', '2026-09-18 19:15:39', 2),
(33, 16, 41, 0.00, 100000.00, 'BALIK: Saldo Awal BRI 2', '2026-09-18 20:03:10', '2026-09-18 20:03:10', 2),
(34, 16, 54, 100000.00, 0.00, 'BALIK: Setoran Modal Awal Rekening BRI 2', '2026-09-18 20:03:10', '2026-09-18 20:03:10', 2),
(35, 17, 437, 100000.00, 0.00, 'Saldo Awal BRI 2', '2026-09-18 20:03:10', '2026-09-18 20:03:10', 2),
(36, 17, 54, 0.00, 100000.00, 'Setoran Modal Awal Rekening BRI 2', '2026-09-18 20:03:10', '2026-09-18 20:03:10', 2),
(37, 18, 54, 0.00, 1000000.00, 'Suntikan Owner', '2026-09-19 22:48:25', '2026-09-19 22:48:25', 2),
(38, 18, 436, 1000000.00, 0.00, NULL, '2026-09-19 22:48:25', '2026-09-19 22:48:25', 2),
(39, 19, 481, 100000.00, 0.00, NULL, '2026-09-20 07:27:41', '2026-09-20 07:27:41', 14),
(40, 19, 511, 0.00, 100000.00, NULL, '2026-09-20 07:27:41', '2026-09-20 07:27:41', 14),
(41, 20, 481, 0.00, 100000.00, NULL, '2026-09-20 07:28:02', '2026-09-20 07:28:02', 14),
(42, 20, 511, 100000.00, 0.00, NULL, '2026-09-20 07:28:02', '2026-09-20 07:28:02', 14),
(43, 21, 481, 200000.00, 0.00, NULL, '2026-09-20 07:29:30', '2026-09-20 07:29:30', 14),
(44, 21, 511, 0.00, 200000.00, NULL, '2026-09-20 07:29:30', '2026-09-20 07:29:30', 14),
(45, 22, 511, 14430.00, 0.00, NULL, '2026-09-20 07:31:39', '2026-09-20 07:31:39', 14),
(46, 22, 496, 0.00, 13000.00, NULL, '2026-09-20 07:31:39', '2026-09-20 07:31:39', 14),
(47, 22, 488, 0.00, 1430.00, NULL, '2026-09-20 07:31:39', '2026-09-20 07:31:39', 14),
(48, 23, 501, 10500.00, 0.00, NULL, '2026-09-20 07:31:39', '2026-09-20 07:31:39', 14),
(49, 23, 481, 0.00, 10500.00, NULL, '2026-09-20 07:31:39', '2026-09-20 07:31:39', 14),
(50, 24, 511, 0.00, 14430.00, 'BALIK: ', '2026-09-20 07:31:51', '2026-09-20 07:31:51', 14),
(51, 24, 496, 13000.00, 0.00, 'BALIK: ', '2026-09-20 07:31:51', '2026-09-20 07:31:51', 14),
(52, 24, 488, 1430.00, 0.00, 'BALIK: ', '2026-09-20 07:31:51', '2026-09-20 07:31:51', 14),
(53, 25, 501, 0.00, 10500.00, 'BALIK: ', '2026-09-20 07:31:51', '2026-09-20 07:31:51', 14),
(54, 25, 481, 10500.00, 0.00, 'BALIK: ', '2026-09-20 07:31:51', '2026-09-20 07:31:51', 14),
(55, 26, 479, 10000.00, 0.00, NULL, '2026-09-20 07:33:30', '2026-09-20 07:33:30', 14),
(56, 26, 496, 0.00, 10000.00, NULL, '2026-09-20 07:33:30', '2026-09-20 07:33:30', 14),
(57, 27, 501, 7500.00, 0.00, NULL, '2026-09-20 07:33:30', '2026-09-20 07:33:30', 14),
(58, 27, 481, 0.00, 7500.00, NULL, '2026-09-20 07:33:30', '2026-09-20 07:33:30', 14),
(59, 28, 511, 2000.00, 0.00, NULL, '2026-09-20 07:33:50', '2026-09-20 07:33:50', 14),
(60, 28, 479, 0.00, 2000.00, 'Pelunasan PJ/09/2026/0002', '2026-09-20 07:33:50', '2026-09-20 07:33:50', 14),
(61, 29, 496, 10000.00, 0.00, NULL, '2026-09-20 07:34:45', '2026-09-20 07:34:45', 14),
(62, 29, 479, 0.00, 10000.00, NULL, '2026-09-20 07:34:45', '2026-09-20 07:34:45', 14),
(63, 30, 481, 7500.00, 0.00, NULL, '2026-09-20 07:34:45', '2026-09-20 07:34:45', 14),
(64, 30, 501, 0.00, 7500.00, NULL, '2026-09-20 07:34:45', '2026-09-20 07:34:45', 14),
(65, 31, 510, 1500.00, 0.00, NULL, '2026-09-20 07:36:28', '2026-09-20 07:36:28', 14),
(66, 31, 481, 0.00, 1500.00, NULL, '2026-09-20 07:36:28', '2026-09-20 07:36:28', 14),
(67, 32, 510, 0.00, 1500.00, NULL, '2026-09-20 07:39:00', '2026-09-20 07:39:00', 14),
(68, 32, 492, 1500.00, 0.00, NULL, '2026-09-20 07:39:00', '2026-09-20 07:39:00', 14),
(69, 33, 481, 0.00, 200000.00, NULL, '2026-09-20 07:57:25', '2026-09-20 07:57:25', 14),
(70, 33, 511, 200000.00, 0.00, NULL, '2026-09-20 07:57:25', '2026-09-20 07:57:25', 14);

-- --------------------------------------------------------

--
-- Table structure for table `jurnal_umum`
--

CREATE TABLE `jurnal_umum` (
  `id` bigint UNSIGNED NOT NULL,
  `nomor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `tipe` enum('kas_masuk','kas_keluar','mutasi_bank','pembelian','penjualan','manual','tutup_buku','hpp','pembayaran','retur_penjualan','retur_pembelian','penyesuaian_stok','perolehan_aset','penyusutan','penghapusan_aset') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `ref_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref_id` bigint UNSIGNED DEFAULT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT '1',
  `voided_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approval_status` enum('draft','pending_review','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `approval_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `change_reason` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jurnal_umum`
--

INSERT INTO `jurnal_umum` (`id`, `nomor`, `tanggal`, `keterangan`, `tipe`, `ref_type`, `ref_id`, `is_posted`, `voided_at`, `created_at`, `updated_at`, `created_by`, `updated_by`, `approved_by`, `approval_status`, `approval_reason`, `approved_at`, `change_reason`, `user_id`) VALUES
(1, 'MAN/09/2026/0001', '2026-09-17', 'Saldo Awal Rekening Gopay', 'manual', 'App\\Models\\Rekening', 1, 1, NULL, '2026-09-16 20:12:51', '2026-09-16 20:12:51', 9, 9, 9, 'approved', NULL, '2026-09-16 20:12:51', NULL, 9),
(2, 'PEN/09/2026/0001', '2026-09-17', 'Penjualan PJ/09/2026/0001 - UMUM', 'penjualan', 'App\\Models\\Penjualan', 1, 1, NULL, '2026-09-16 20:30:13', '2026-09-16 20:30:13', 9, 9, 9, 'approved', NULL, '2026-09-16 20:30:13', NULL, 9),
(3, 'HPP/09/2026/0001', '2026-09-17', 'HPP Penjualan PJ/09/2026/0001', 'hpp', 'App\\Models\\Penjualan', 1, 1, NULL, '2026-09-16 20:30:13', '2026-09-16 20:30:13', 9, 9, 9, 'approved', NULL, '2026-09-16 20:30:13', NULL, 9),
(4, 'PEM/09/2026/0001', '2026-09-17', 'Pembelian PB/09/2026/0001 - UMUM', 'pembelian', 'App\\Models\\Pembelian', 1, 1, NULL, '2026-09-16 20:31:37', '2026-09-16 20:31:37', 9, 9, 9, 'approved', NULL, '2026-09-16 20:31:37', NULL, 9),
(5, 'MAN/09/2026/0001', '2026-09-17', 'Saldo Awal Rekening Bank UOB Indonesia', 'manual', 'App\\Models\\Rekening', 2, 1, NULL, '2026-09-17 03:01:40', '2026-09-17 03:01:40', 11, 11, 11, 'approved', NULL, '2026-09-17 03:01:40', NULL, 11),
(6, 'PEM/09/2026/0001', '2026-09-18', '[DIBATALKAN] Pembelian PB/09/2026/0001 - Hilmi', 'pembelian', 'App\\Models\\Pembelian', 2, 1, NULL, '2026-09-18 05:37:34', '2026-09-18 05:38:04', 8, 8, 8, 'approved', NULL, '2026-09-18 05:37:34', NULL, 8),
(7, 'PEM/09/2026/0002', '2026-09-18', 'Pembatalan pembelian — PEM/09/2026/0001', 'pembelian', NULL, NULL, 1, NULL, '2026-09-18 05:38:04', '2026-09-18 05:38:04', 8, 8, 8, 'approved', NULL, '2026-09-18 05:38:04', NULL, 8),
(8, 'PEM/09/2026/0003', '2026-09-18', 'Pembelian PB/09/2026/0002 - Hilmi', 'pembelian', 'App\\Models\\Pembelian', 3, 1, NULL, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 8, 8, 8, 'approved', NULL, '2026-09-18 05:45:41', NULL, 8),
(9, 'KAS/09/2026/0001', '2026-09-18', 'Kas Masuk KM/09/2026/0001', 'kas_masuk', 'App\\Models\\KasMasuk', 1, 1, NULL, '2026-09-18 05:52:37', '2026-09-18 05:52:37', 8, 8, 8, 'approved', NULL, '2026-09-18 05:52:37', NULL, 8),
(10, 'PEM/09/2026/0001', '2026-09-18', 'Pembelian PB/09/2026/0001 - UMUM', 'pembelian', 'App\\Models\\Pembelian', 4, 1, NULL, '2026-09-18 06:21:39', '2026-09-18 06:21:39', 4, 4, 4, 'approved', NULL, '2026-09-18 06:21:39', NULL, 4),
(11, 'MAN/09/2026/0001', '2026-09-19', 'Saldo Awal Rekening Kas Utama', 'manual', 'App\\Models\\Rekening', 11, 1, NULL, '2026-09-18 19:11:30', '2026-09-18 19:11:30', 2, 2, 2, 'approved', NULL, '2026-09-18 19:11:30', NULL, 2),
(12, 'PEM/09/2026/0001', '2026-09-19', 'Pembelian PB/09/2026/0001 - UMUM', 'pembelian', 'App\\Models\\Pembelian', 5, 1, NULL, '2026-09-18 19:11:54', '2026-09-18 19:11:54', 2, 2, 2, 'approved', NULL, '2026-09-18 19:11:54', NULL, 2),
(13, 'PEM/09/2026/0002', '2026-09-19', 'Pembelian PB/09/2026/0002 - UMUM', 'pembelian', 'App\\Models\\Pembelian', 6, 1, NULL, '2026-09-18 19:14:45', '2026-09-18 19:14:45', 2, 2, 2, 'approved', NULL, '2026-09-18 19:14:45', NULL, 2),
(14, 'PEM/09/2026/0003', '2026-09-19', 'Pembelian PB/09/2026/0003 - UMUM', 'pembelian', 'App\\Models\\Pembelian', 7, 1, NULL, '2026-09-18 19:15:05', '2026-09-18 19:15:05', 2, 2, 2, 'approved', NULL, '2026-09-18 19:15:05', NULL, 2),
(15, 'MAN/09/2026/0002', '2026-09-19', '[DIBATALKAN] Saldo Awal Rekening BRI 2', 'manual', 'App\\Models\\Rekening', 13, 1, NULL, '2026-09-18 19:15:39', '2026-09-18 20:03:10', 2, 2, 2, 'approved', NULL, '2026-09-18 19:15:39', NULL, 2),
(16, 'MAN/09/2026/0003', '2026-09-19', 'Pemindahan akun rekening — MAN/09/2026/0002', 'manual', NULL, NULL, 1, NULL, '2026-09-18 20:03:10', '2026-09-18 20:03:10', 2, 2, 2, 'approved', NULL, '2026-09-18 20:03:10', NULL, 2),
(17, 'MAN/09/2026/0004', '2026-09-19', 'Saldo Awal Rekening BRI 2', 'manual', 'App\\Models\\Rekening', 13, 1, NULL, '2026-09-18 20:03:10', '2026-09-18 20:03:10', 2, 2, 2, 'approved', NULL, '2026-09-18 20:03:10', NULL, 2),
(18, 'KAS/09/2026/0001', '2026-09-20', 'Kas Masuk KM/09/2026/0001', 'kas_masuk', 'App\\Models\\KasMasuk', 2, 1, NULL, '2026-09-19 22:48:25', '2026-09-19 22:48:25', 2, 2, 2, 'approved', NULL, '2026-09-19 22:48:25', NULL, 2),
(19, 'PEM/09/2026/0001', '2026-09-20', 'Pembelian PB/09/2026/0001 - UMUM', 'pembelian', 'App\\Models\\Pembelian', 8, 1, NULL, '2026-09-20 07:27:41', '2026-09-20 07:27:41', 14, 14, 14, 'approved', NULL, '2026-09-20 07:27:41', NULL, 14),
(20, 'RET/09/2026/0001', '2026-09-20', 'Retur Pembelian RPB/09/2026/0001 - UMUM', 'retur_pembelian', 'App\\Models\\ReturPembelian', 1, 1, NULL, '2026-09-20 07:28:02', '2026-09-20 07:28:02', 14, 14, 14, 'approved', NULL, '2026-09-20 07:28:02', NULL, 14),
(21, 'PEM/09/2026/0002', '2026-09-20', 'Pembelian PB/09/2026/0002 - UMUM', 'pembelian', 'App\\Models\\Pembelian', 9, 1, NULL, '2026-09-20 07:29:30', '2026-09-20 07:29:30', 14, 14, 14, 'approved', NULL, '2026-09-20 07:29:30', NULL, 14),
(22, 'PEN/09/2026/0001', '2026-09-20', '[DIBATALKAN] Penjualan PJ/09/2026/0001 - UMUM', 'penjualan', 'App\\Models\\Penjualan', 2, 1, '2026-09-20 07:31:51', '2026-09-20 07:31:39', '2026-09-20 07:31:51', 14, 14, 14, 'approved', NULL, '2026-09-20 07:31:39', NULL, 14),
(23, 'HPP/09/2026/0001', '2026-09-20', '[DIBATALKAN] HPP Penjualan PJ/09/2026/0001', 'hpp', 'App\\Models\\Penjualan', 2, 1, '2026-09-20 07:31:51', '2026-09-20 07:31:39', '2026-09-20 07:31:51', 14, 14, 14, 'approved', NULL, '2026-09-20 07:31:39', NULL, 14),
(24, 'PEN/09/2026/0002', '2026-09-20', 'Pembatalan penjualan — PEN/09/2026/0001', 'penjualan', 'App\\Models\\JurnalUmum', 22, 1, NULL, '2026-09-20 07:31:51', '2026-09-20 07:31:51', 14, 14, 14, 'approved', NULL, '2026-09-20 07:31:51', NULL, 14),
(25, 'HPP/09/2026/0002', '2026-09-20', 'Pembatalan penjualan — HPP/09/2026/0001', 'hpp', 'App\\Models\\JurnalUmum', 23, 1, NULL, '2026-09-20 07:31:51', '2026-09-20 07:31:51', 14, 14, 14, 'approved', NULL, '2026-09-20 07:31:51', NULL, 14),
(26, 'PEN/09/2026/0003', '2026-09-20', 'Penjualan PJ/09/2026/0002 - UMUM', 'penjualan', 'App\\Models\\Penjualan', 3, 1, NULL, '2026-09-20 07:33:30', '2026-09-20 07:33:30', 14, 14, 14, 'approved', NULL, '2026-09-20 07:33:30', NULL, 14),
(27, 'HPP/09/2026/0003', '2026-09-20', 'HPP Penjualan PJ/09/2026/0002', 'hpp', 'App\\Models\\Penjualan', 3, 1, NULL, '2026-09-20 07:33:30', '2026-09-20 07:33:30', 14, 14, 14, 'approved', NULL, '2026-09-20 07:33:30', NULL, 14),
(28, 'KAS/09/2026/0001', '2026-09-20', 'Pelunasan PJ/09/2026/0002', 'kas_masuk', 'App\\Models\\KasMasuk', 3, 1, NULL, '2026-09-20 07:33:50', '2026-09-20 07:33:50', 14, 14, 14, 'approved', NULL, '2026-09-20 07:33:50', NULL, 14),
(29, 'RET/09/2026/0002', '2026-09-20', 'Retur Penjualan RPJ/09/2026/0001 - UMUM', 'retur_penjualan', 'App\\Models\\ReturPenjualan', 1, 1, NULL, '2026-09-20 07:34:45', '2026-09-20 07:34:45', 14, 14, 14, 'approved', NULL, '2026-09-20 07:34:45', NULL, 14),
(30, 'HPP/09/2026/0004', '2026-09-20', 'HPP Retur Penjualan RPJ/09/2026/0001', 'hpp', 'App\\Models\\ReturPenjualan', 1, 1, NULL, '2026-09-20 07:34:45', '2026-09-20 07:34:45', 14, 14, 14, 'approved', NULL, '2026-09-20 07:34:45', NULL, 14),
(31, 'PEN/09/2026/0004', '2026-09-20', 'Perubahan Stok PS/09/2026/0001 (Salah)', 'penyesuaian_stok', 'App\\Models\\PerubahanStok', 1, 1, NULL, '2026-09-20 07:36:28', '2026-09-20 07:36:28', 14, 14, 14, 'approved', NULL, '2026-09-20 07:36:28', NULL, 14),
(32, 'TUT/09/2026/0001', '2026-09-30', 'Tutup Buku September 2026', 'tutup_buku', NULL, NULL, 1, NULL, '2026-09-20 07:39:00', '2026-09-20 07:39:00', 14, 14, 14, 'approved', NULL, '2026-09-20 07:39:00', NULL, 14),
(33, 'RET/08/2026/0001', '2026-08-20', 'Retur Pembelian RPB/08/2026/0001 - UMUM', 'retur_pembelian', 'App\\Models\\ReturPembelian', 3, 1, NULL, '2026-09-20 07:57:25', '2026-09-20 07:57:25', 14, 14, 14, 'approved', NULL, '2026-09-20 07:57:25', NULL, 14);

-- --------------------------------------------------------

--
-- Table structure for table `kas`
--

CREATE TABLE `kas` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `akun_id` bigint UNSIGNED NOT NULL,
  `saldo_awal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `is_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kas_keluar`
--

CREATE TABLE `kas_keluar` (
  `id` bigint UNSIGNED NOT NULL,
  `rekening_id` bigint UNSIGNED DEFAULT NULL,
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `nomor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `total` decimal(18,2) NOT NULL DEFAULT '0.00',
  `pajak_id` bigint UNSIGNED DEFAULT NULL,
  `pajak_nominal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approval_status` enum('draft','pending_review','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `approval_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kas_keluar_items`
--

CREATE TABLE `kas_keluar_items` (
  `id` bigint UNSIGNED NOT NULL,
  `kas_keluar_id` bigint UNSIGNED NOT NULL,
  `akun_id` bigint UNSIGNED NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `nominal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kas_masuk`
--

CREATE TABLE `kas_masuk` (
  `id` bigint UNSIGNED NOT NULL,
  `rekening_id` bigint UNSIGNED DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `nomor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `total` decimal(18,2) NOT NULL DEFAULT '0.00',
  `pajak_id` bigint UNSIGNED DEFAULT NULL,
  `pajak_nominal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approval_status` enum('draft','pending_review','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `approval_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kas_masuk`
--

INSERT INTO `kas_masuk` (`id`, `rekening_id`, `customer_id`, `nomor`, `tanggal`, `keterangan`, `total`, `pajak_id`, `pajak_nominal`, `grand_total`, `created_at`, `updated_at`, `created_by`, `updated_by`, `approved_by`, `approval_status`, `approval_reason`, `approved_at`, `user_id`) VALUES
(1, 4, NULL, 'KM/09/2026/0001', '2026-09-18', NULL, 750000.00, NULL, 0.00, 750000.00, '2026-09-18 05:52:37', '2026-09-18 05:52:37', 8, NULL, NULL, 'approved', NULL, NULL, 8),
(2, 12, NULL, 'KM/09/2026/0001', '2026-09-20', NULL, 1000000.00, NULL, 0.00, 1000000.00, '2026-09-19 22:48:25', '2026-09-19 22:48:25', 2, NULL, NULL, 'approved', NULL, NULL, 2),
(3, 14, 15, 'KM/09/2026/0001', '2026-09-20', 'Pelunasan PJ/09/2026/0002', 2000.00, NULL, 0.00, 2000.00, '2026-09-20 07:33:50', '2026-09-20 07:33:50', 14, NULL, NULL, 'approved', NULL, NULL, 14);

-- --------------------------------------------------------

--
-- Table structure for table `kas_masuk_items`
--

CREATE TABLE `kas_masuk_items` (
  `id` bigint UNSIGNED NOT NULL,
  `kas_masuk_id` bigint UNSIGNED NOT NULL,
  `akun_id` bigint UNSIGNED NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `nominal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kas_masuk_items`
--

INSERT INTO `kas_masuk_items` (`id`, `kas_masuk_id`, `akun_id`, `keterangan`, `nominal`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 1, 275, 'Clothes', 600000.00, '2026-09-18 05:52:37', '2026-09-18 05:52:37', 8),
(2, 1, 275, 'Colection', 150000.00, '2026-09-18 05:52:37', '2026-09-18 05:52:37', 8),
(3, 2, 54, 'Suntikan Owner', 1000000.00, '2026-09-19 22:48:25', '2026-09-19 22:48:25', 2),
(4, 3, 479, 'Pelunasan PJ/09/2026/0002', 2000.00, '2026-09-20 07:33:50', '2026-09-20 07:33:50', 14);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'Makanan', '2026-09-15 21:08:40', '2026-09-15 21:08:40', 2),
(2, 'Fleece', '2026-09-15 21:51:47', '2026-09-15 21:51:47', 6),
(3, 'Makanan', '2026-09-16 20:19:06', '2026-09-16 20:19:06', 9),
(4, 'Bahan', '2026-09-16 20:20:14', '2026-09-16 20:20:14', 9),
(5, 'Fleece', '2026-09-16 21:45:01', '2026-09-16 21:45:01', 8),
(6, 'Iklan video Ai', '2026-09-17 03:03:17', '2026-09-17 03:03:17', 11),
(7, 'Pisang', '2026-09-17 03:48:47', '2026-09-17 03:48:47', 10),
(8, 'Rib', '2026-09-18 05:22:24', '2026-09-18 05:22:24', 8),
(9, 'Makanan', '2026-09-18 06:20:49', '2026-09-18 06:20:49', 4),
(10, 'UMUM', '2026-09-20 07:25:09', '2026-09-20 07:25:09', 14);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_09_03_034336_create_master_tables', 1),
(5, '2026_09_03_034339_create_transaksi_tables', 1),
(6, '2026_09_03_034342_create_akuntansi_tables', 1),
(7, '2026_09_03_034400_add_harga_net_to_pembelian_items', 1),
(8, '2026_09_03_040000_create_rekenings_merge', 1),
(9, '2026_09_03_050000_drop_legacy_kas_bank_columns', 1),
(10, '2026_09_03_084527_create_barang_merge_bahan_produk', 1),
(11, '2026_09_03_104320_fix_daftar_harga_unique_index', 1),
(12, '2026_09_04_080000_add_contact_to_kas_tables', 1),
(13, '2026_09_04_090000_enhance_daftar_harga_tiered_pricing', 1),
(14, '2026_09_05_014154_add_foto_barcode_to_barang_table', 1),
(15, '2026_09_05_014155_create_satuan_kategori_tables', 1),
(16, '2026_09_05_100000_daftar_harga_add_entitas_customer', 1),
(17, '2026_09_06_000001_add_audit_and_approval_to_journals', 1),
(18, '2026_09_06_000002_add_period_locking_and_audit_trails', 1),
(19, '2026_09_06_100000_create_daftar_harga_riwayat_table', 1),
(20, '2026_09_07_000001_create_inventory_tables', 1),
(21, '2026_09_07_000002_add_inventory_types_to_jurnal_tipe', 1),
(22, '2026_09_07_052711_add_role_to_users_table', 1),
(23, '2026_09_07_060000_create_asets_table', 1),
(24, '2026_09_07_060001_create_penyusutan_table', 1),
(25, '2026_09_07_060002_add_asset_types_to_jurnal_tipe', 1),
(26, '2026_09_07_152318_create_asset_templates_table', 1),
(27, '2026_09_07_152319_alter_asets_for_aset_refactor', 1),
(28, '2026_09_07_152320_add_penghapusan_aset_to_jurnal_tipe', 1),
(29, '2026_09_08_000001_add_ongkir_to_transaksi_tables', 1),
(30, '2026_09_08_041508_create_default_umum_rekanan', 1),
(31, '2026_09_09_045014_add_pending_status_and_sync_harga_to_transaksi', 1),
(32, '2026_09_09_224745_add_penjualan_id_to_bb_piutang_table', 1),
(33, '2026_09_09_230922_add_pembelian_id_to_bb_hutang_table', 1),
(34, '2026_09_10_100000_remove_role_from_users_table', 1),
(35, '2026_09_11_003631_create_gudangs_table', 1),
(36, '2026_09_11_003635_create_stok_gudang_table', 1),
(37, '2026_09_11_003638_add_gudang_id_to_barang_table', 1),
(38, '2026_09_11_003642_create_transfer_gudang_tables', 1),
(39, '2026_09_11_003646_create_tutup_buku_tahunan_table', 1),
(40, '2026_09_11_120648_add_user_id_to_tenant_tables', 1),
(41, '2026_09_11_120652_backfill_tenant_user_id', 1),
(42, '2026_09_11_120655_add_google_id_to_users_table', 1),
(43, '2026_09_11_120659_convert_unique_constraints_to_tenant', 1),
(44, '2026_09_11_135919_make_password_nullable_on_users_table', 1),
(45, '2026_09_13_021049_add_gudang_id_to_pembelian_penjualan_tables', 1),
(46, '2026_09_13_054804_add_status_to_users_table', 1),
(47, '2026_09_15_010732_create_notifications_table', 1),
(48, '2026_09_15_010735_create_chat_rooms_table', 1),
(49, '2026_09_15_010739_create_chat_messages_table', 1),
(50, '2026_09_15_010743_create_chat_room_reads_table', 1),
(51, '2026_09_15_010747_create_chat_reports_table', 1),
(52, '2026_09_15_010751_add_plan_to_users_table', 1),
(53, '2026_09_15_033937_add_approval_status_to_transaction_tables_bridge', 1),
(54, '2026_09_15_033938_add_google_and_plan_columns_to_users_table', 1),
(55, '2026_09_15_033939_add_user_id_to_tenant_tables_bridge', 1),
(56, '2026_09_15_234339_create_donasis_table', 2),
(57, '2026_09_19_033932_add_voided_at_to_jurnal_umum_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `mutasi_bank`
--

CREATE TABLE `mutasi_bank` (
  `id` bigint UNSIGNED NOT NULL,
  `nomor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `rekening_asal_id` bigint UNSIGNED DEFAULT NULL,
  `rekening_tujuan_id` bigint UNSIGNED DEFAULT NULL,
  `nominal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('20d99a84-b32a-47c9-9235-b3055b139bc1', 'App\\Notifications\\NotifikasiDalamAplikasi', 'App\\Models\\User', 6, '{\"title\":\"Paket langganan diperbarui\",\"body\":\"Paket Anda kini: Gratis.\",\"type\":\"plan.change\"}', NULL, '2026-09-16 01:49:00', '2026-09-16 01:49:00'),
('617c8308-b725-43bd-a317-28f894f6ebff', 'App\\Notifications\\NotifikasiDalamAplikasi', 'App\\Models\\User', 2, '{\"title\":\"Paket langganan diperbarui\",\"body\":\"Paket Anda kini: Pro.\",\"type\":\"plan.change\"}', '2026-09-20 05:39:59', '2026-09-17 06:11:00', '2026-09-20 05:39:59'),
('764db4d4-6557-4fe4-8141-728170e916b8', 'App\\Notifications\\NotifikasiDalamAplikasi', 'App\\Models\\User', 9, '{\"title\":\"Paket langganan diperbarui\",\"body\":\"Paket Anda kini: Pro.\",\"type\":\"plan.change\"}', NULL, '2026-09-16 20:48:24', '2026-09-16 20:48:24'),
('8bdf0af4-feb1-4378-8013-ed052f90ffdf', 'App\\Notifications\\NotifikasiDalamAplikasi', 'App\\Models\\User', 6, '{\"title\":\"Paket langganan diperbarui\",\"body\":\"Paket Anda kini: Pro.\",\"type\":\"plan.change\"}', NULL, '2026-09-16 01:49:53', '2026-09-16 01:49:53'),
('b936b4bf-c32d-4c44-9328-48ac6789978d', 'App\\Notifications\\NotifikasiDalamAplikasi', 'App\\Models\\User', 2, '{\"title\":\"Paket langganan diperbarui\",\"body\":\"Paket Anda kini: Gratis.\",\"type\":\"plan.change\"}', '2026-09-20 05:39:59', '2026-09-17 06:10:42', '2026-09-20 05:39:59'),
('cfa49c0c-9e08-48ec-8b17-9c867efa887a', 'App\\Notifications\\NotifikasiDalamAplikasi', 'App\\Models\\User', 9, '{\"title\":\"Paket langganan diperbarui\",\"body\":\"Paket Anda kini: Gratis.\",\"type\":\"plan.change\"}', '2026-09-16 20:17:48', '2026-09-16 20:16:50', '2026-09-16 20:17:48');

-- --------------------------------------------------------

--
-- Table structure for table `pajak`
--

CREATE TABLE `pajak` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` decimal(5,2) NOT NULL DEFAULT '11.00',
  `is_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pajak`
--

INSERT INTO `pajak` (`id`, `nama`, `rate`, `is_aktif`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'PPN', 11.00, 1, '2026-09-15 01:27:16', '2026-09-15 01:27:16', 1),
(2, 'PPN', 11.00, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(3, 'PPN', 11.00, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(4, 'PPN', 11.00, 1, '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(5, 'PPN', 11.00, 1, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 5),
(6, 'PPN', 11.00, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(7, 'PPN', 11.00, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(8, 'PPN', 11.00, 1, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 8),
(9, 'PPN', 11.00, 1, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(10, 'PPN', 11.00, 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(11, 'PPN', 11.00, 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(12, 'PPN', 11.00, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(13, 'PPN', 11.00, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(15, '11', 11.00, 1, '2026-09-20 07:49:23', '2026-09-20 07:49:23', 14);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembelians`
--

CREATE TABLE `pembelians` (
  `id` bigint UNSIGNED NOT NULL,
  `rekening_id` bigint UNSIGNED DEFAULT NULL,
  `nomor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `gudang_id` bigint UNSIGNED DEFAULT NULL,
  `metode_bayar` enum('tunai','kredit') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tunai',
  `subtotal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `diskon` decimal(18,2) NOT NULL DEFAULT '0.00',
  `diskon_tipe` enum('nominal','persen') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nominal',
  `diskon_nominal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `pajak_id` bigint UNSIGNED DEFAULT NULL,
  `pajak_nominal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total` decimal(18,2) NOT NULL DEFAULT '0.00',
  `status` enum('posted','draft','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'posted',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `sync_harga` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approval_status` enum('draft','pending_review','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `approval_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `ongkir` decimal(18,2) NOT NULL DEFAULT '0.00',
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pembelians`
--

INSERT INTO `pembelians` (`id`, `rekening_id`, `nomor`, `tanggal`, `supplier_id`, `gudang_id`, `metode_bayar`, `subtotal`, `diskon`, `diskon_tipe`, `diskon_nominal`, `pajak_id`, `pajak_nominal`, `total`, `status`, `keterangan`, `sync_harga`, `created_at`, `updated_at`, `created_by`, `updated_by`, `approved_by`, `approval_status`, `approval_reason`, `approved_at`, `ongkir`, `user_id`) VALUES
(1, 1, 'PB/09/2026/0001', '2026-09-17', 10, 10, 'tunai', 80000.00, 0.00, 'nominal', 0.00, NULL, 0.00, 80000.00, 'posted', NULL, 1, '2026-09-16 20:31:37', '2026-09-16 20:31:37', 9, NULL, NULL, 'approved', NULL, NULL, 0.00, 9),
(2, 4, 'PB/09/2026/0001', '2026-09-18', 14, 9, 'tunai', 13187850.00, 0.00, 'nominal', 0.00, NULL, 0.00, 13187850.00, 'draft', NULL, 1, '2026-09-18 05:37:20', '2026-09-18 05:38:04', 8, NULL, NULL, 'approved', NULL, NULL, 0.00, 8),
(3, 4, 'PB/09/2026/0002', '2026-09-18', 14, 9, 'tunai', 13187850.00, 0.00, 'nominal', 0.00, NULL, 0.00, 13846850.00, 'posted', NULL, 1, '2026-09-18 05:43:42', '2026-09-18 05:45:41', 8, NULL, NULL, 'approved', NULL, NULL, 659000.00, 8),
(4, 7, 'PB/09/2026/0001', '2026-09-18', 5, 5, 'tunai', 200000.00, 0.00, 'nominal', 0.00, NULL, 0.00, 200000.00, 'posted', NULL, 1, '2026-09-18 06:21:39', '2026-09-18 06:21:39', 4, NULL, NULL, 'approved', NULL, NULL, 0.00, 4),
(5, 10, 'PB/09/2026/0001', '2026-09-19', 3, 3, 'tunai', 300000.00, 0.00, 'nominal', 0.00, NULL, 0.00, 300000.00, 'posted', NULL, 1, '2026-09-18 19:11:54', '2026-09-18 19:11:54', 2, NULL, NULL, 'approved', NULL, NULL, 0.00, 2),
(6, 11, 'PB/09/2026/0002', '2026-09-19', 3, 3, 'tunai', 30000.00, 0.00, 'nominal', 0.00, NULL, 0.00, 30000.00, 'posted', NULL, 1, '2026-09-18 19:14:45', '2026-09-18 19:14:45', 2, NULL, NULL, 'approved', NULL, NULL, 0.00, 2),
(7, 11, 'PB/09/2026/0003', '2026-09-19', 3, 3, 'tunai', 300000.00, 0.00, 'nominal', 0.00, NULL, 0.00, 300000.00, 'posted', NULL, 1, '2026-09-18 19:15:05', '2026-09-18 19:15:05', 2, NULL, NULL, 'approved', NULL, NULL, 0.00, 2),
(8, 14, 'PB/09/2026/0001', '2026-09-20', 16, 16, 'tunai', 100000.00, 0.00, 'nominal', 0.00, NULL, 0.00, 100000.00, 'posted', NULL, 1, '2026-09-20 07:27:41', '2026-09-20 07:27:41', 14, NULL, NULL, 'approved', NULL, NULL, 0.00, 14),
(9, 14, 'PB/09/2026/0002', '2026-09-20', 16, 16, 'tunai', 200000.00, 0.00, 'nominal', 0.00, NULL, 0.00, 200000.00, 'posted', NULL, 1, '2026-09-20 07:29:30', '2026-09-20 07:29:30', 14, NULL, NULL, 'approved', NULL, NULL, 0.00, 14);

-- --------------------------------------------------------

--
-- Table structure for table `pembelian_items`
--

CREATE TABLE `pembelian_items` (
  `id` bigint UNSIGNED NOT NULL,
  `pembelian_id` bigint UNSIGNED NOT NULL,
  `jumlah` decimal(18,2) NOT NULL,
  `harga_satuan` decimal(18,2) NOT NULL,
  `harga_net` decimal(18,2) NOT NULL DEFAULT '0.00',
  `diskon` decimal(18,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `barang_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pembelian_items`
--

INSERT INTO `pembelian_items` (`id`, `pembelian_id`, `jumlah`, `harga_satuan`, `harga_net`, `diskon`, `subtotal`, `created_at`, `updated_at`, `barang_id`, `user_id`) VALUES
(1, 1, 10.00, 8000.00, 8000.00, 0.00, 80000.00, '2026-09-16 20:31:37', '2026-09-16 20:31:37', 4, 9),
(15, 2, 49.12, 60000.00, 60000.00, 0.00, 2947200.00, '2026-09-18 05:37:33', '2026-09-18 05:37:33', 5, 8),
(16, 2, 24.84, 60000.00, 60000.00, 0.00, 1490400.00, '2026-09-18 05:37:33', '2026-09-18 05:37:33', 8, 8),
(17, 2, 11.40, 60500.00, 60500.00, 0.00, 689700.00, '2026-09-18 05:37:33', '2026-09-18 05:37:33', 16, 8),
(18, 2, 5.05, 60500.00, 60500.00, 0.00, 305525.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 19, 8),
(19, 2, 25.29, 60000.00, 60000.00, 0.00, 1517400.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 10, 8),
(20, 2, 5.10, 60500.00, 60500.00, 0.00, 308550.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 21, 8),
(21, 2, 25.00, 60000.00, 60000.00, 0.00, 1500000.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 15, 8),
(22, 2, 5.00, 60500.00, 60500.00, 0.00, 302500.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 25, 8),
(23, 2, 25.19, 60000.00, 60000.00, 0.00, 1511400.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 9, 8),
(24, 2, 5.00, 60500.00, 60500.00, 0.00, 302500.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 20, 8),
(25, 2, 24.21, 60000.00, 60000.00, 0.00, 1452600.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 14, 8),
(26, 2, 4.00, 60500.00, 60500.00, 0.00, 242000.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 24, 8),
(27, 2, 10.05, 61500.00, 61500.00, 0.00, 618075.00, '2026-09-18 05:37:34', '2026-09-18 05:37:34', 26, 8),
(54, 3, 49.12, 60000.00, 60000.00, 0.00, 2947200.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 5, 8),
(55, 3, 24.84, 60000.00, 60000.00, 0.00, 1490400.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 8, 8),
(56, 3, 25.29, 60000.00, 60000.00, 0.00, 1517400.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 10, 8),
(57, 3, 25.00, 60000.00, 60000.00, 0.00, 1500000.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 15, 8),
(58, 3, 25.19, 60000.00, 60000.00, 0.00, 1511400.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 9, 8),
(59, 3, 24.21, 60000.00, 60000.00, 0.00, 1452600.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 14, 8),
(60, 3, 11.40, 60500.00, 60500.00, 0.00, 689700.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 16, 8),
(61, 3, 5.05, 60500.00, 60500.00, 0.00, 305525.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 19, 8),
(62, 3, 5.10, 60500.00, 60500.00, 0.00, 308550.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 21, 8),
(63, 3, 5.00, 60500.00, 60500.00, 0.00, 302500.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 25, 8),
(64, 3, 5.00, 60500.00, 60500.00, 0.00, 302500.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 20, 8),
(65, 3, 4.00, 60500.00, 60500.00, 0.00, 242000.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 24, 8),
(66, 3, 10.05, 61500.00, 61500.00, 0.00, 618075.00, '2026-09-18 05:45:41', '2026-09-18 05:45:41', 26, 8),
(67, 4, 100.00, 2000.00, 2000.00, 0.00, 200000.00, '2026-09-18 06:21:39', '2026-09-18 06:21:39', 27, 4),
(68, 5, 100.00, 3000.00, 3000.00, 0.00, 300000.00, '2026-09-18 19:11:54', '2026-09-18 19:11:54', 3, 2),
(69, 6, 10.00, 3000.00, 3000.00, 0.00, 30000.00, '2026-09-18 19:14:45', '2026-09-18 19:14:45', 3, 2),
(70, 7, 100.00, 3000.00, 3000.00, 0.00, 300000.00, '2026-09-18 19:15:05', '2026-09-18 19:15:05', 3, 2),
(71, 8, 100.00, 1000.00, 1000.00, 0.00, 100000.00, '2026-09-20 07:27:41', '2026-09-20 07:27:41', 28, 14),
(72, 9, 100.00, 2000.00, 2000.00, 0.00, 200000.00, '2026-09-20 07:29:30', '2026-09-20 07:29:30', 28, 14);

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `key`, `value`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'nama_perusahaan', 'Perusahaan Saya', '2026-09-15 01:27:17', '2026-09-15 01:27:17', 1),
(2, 'alamat_perusahaan', '', '2026-09-15 01:27:17', '2026-09-15 01:27:17', 1),
(3, 'telepon_perusahaan', '', '2026-09-15 01:27:17', '2026-09-15 01:27:17', 1),
(4, 'email_perusahaan', '', '2026-09-15 01:27:17', '2026-09-15 01:27:17', 1),
(5, 'kota_perusahaan', '', '2026-09-15 01:27:17', '2026-09-15 01:27:17', 1),
(6, 'pajak', 'PPN', '2026-09-15 01:27:17', '2026-09-15 01:27:17', 1),
(7, 'nama_perusahaan', 'Perusahaan Saya', '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(8, 'alamat_perusahaan', '', '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(9, 'telepon_perusahaan', '', '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(10, 'email_perusahaan', '', '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(11, 'kota_perusahaan', '', '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(12, 'pajak', 'PPN', '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(13, 'nama_perusahaan', 'Perusahaan Saya', '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(14, 'alamat_perusahaan', '', '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(15, 'telepon_perusahaan', '', '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(16, 'email_perusahaan', '', '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(17, 'kota_perusahaan', '', '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(18, 'pajak', 'PPN', '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(19, 'nama_perusahaan', 'Perusahaan Saya', '2026-09-15 18:42:58', '2026-09-15 18:42:58', 4),
(20, 'alamat_perusahaan', '', '2026-09-15 18:42:59', '2026-09-15 18:42:59', 4),
(21, 'telepon_perusahaan', '', '2026-09-15 18:42:59', '2026-09-15 18:42:59', 4),
(22, 'email_perusahaan', '', '2026-09-15 18:42:59', '2026-09-15 18:42:59', 4),
(23, 'kota_perusahaan', '', '2026-09-15 18:42:59', '2026-09-15 18:42:59', 4),
(24, 'pajak', 'PPN', '2026-09-15 18:42:59', '2026-09-15 18:42:59', 4),
(25, 'nama_perusahaan', 'Perusahaan Saya', '2026-09-15 18:53:01', '2026-09-15 18:53:01', 5),
(26, 'alamat_perusahaan', '', '2026-09-15 18:53:01', '2026-09-15 18:53:01', 5),
(27, 'telepon_perusahaan', '', '2026-09-15 18:53:01', '2026-09-15 18:53:01', 5),
(28, 'email_perusahaan', '', '2026-09-15 18:53:01', '2026-09-15 18:53:01', 5),
(29, 'kota_perusahaan', '', '2026-09-15 18:53:01', '2026-09-15 18:53:01', 5),
(30, 'pajak', 'PPN', '2026-09-15 18:53:01', '2026-09-15 18:53:01', 5),
(31, 'nama_perusahaan', 'Perusahaan Saya', '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(32, 'alamat_perusahaan', '', '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(33, 'telepon_perusahaan', '', '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(34, 'email_perusahaan', '', '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(35, 'kota_perusahaan', '', '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(36, 'pajak', 'PPN', '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(37, 'nama_perusahaan', 'Perusahaan Saya', '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(38, 'alamat_perusahaan', '', '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(39, 'telepon_perusahaan', '', '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(40, 'email_perusahaan', '', '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(41, 'kota_perusahaan', '', '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(42, 'pajak', 'PPN', '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(43, 'nama_perusahaan', 'Perusahaan Saya', '2026-09-16 00:21:17', '2026-09-16 00:21:17', 8),
(44, 'alamat_perusahaan', '', '2026-09-16 00:21:17', '2026-09-16 00:21:17', 8),
(45, 'telepon_perusahaan', '', '2026-09-16 00:21:17', '2026-09-16 00:21:17', 8),
(46, 'email_perusahaan', '', '2026-09-16 00:21:17', '2026-09-16 00:21:17', 8),
(47, 'kota_perusahaan', '', '2026-09-16 00:21:17', '2026-09-16 00:21:17', 8),
(48, 'pajak', 'PPN', '2026-09-16 00:21:17', '2026-09-16 00:21:17', 8),
(49, 'barang_kolom', '[\"kode\",\"nama\",\"kategori\",\"stok\",\"min_stok\",\"harga_beli\",\"harga_jual\",\"aksi\"]', '2026-09-16 02:37:57', '2026-09-18 05:21:55', 8),
(50, 'nama_perusahaan', 'Perusahaan Saya', '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(51, 'alamat_perusahaan', '', '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(52, 'telepon_perusahaan', '', '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(53, 'email_perusahaan', '', '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(54, 'kota_perusahaan', '', '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(55, 'pajak', 'PPN', '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(56, 'seq_MAN_09_2026', '1', '2026-09-16 20:12:51', '2026-09-16 20:12:51', 9),
(57, 'akun_perkiraan_kolom', '[\"kode\",\"nama\",\"jenis\",\"kelompok\",\"posisi\",\"aksi\"]', '2026-09-16 20:14:45', '2026-09-16 20:14:45', 9),
(58, 'nama_perusahaan', 'Perusahaan Saya', '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(59, 'alamat_perusahaan', '', '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(60, 'telepon_perusahaan', '', '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(61, 'email_perusahaan', '', '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(62, 'kota_perusahaan', '', '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(63, 'pajak', 'PPN', '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(64, 'seq_PJ_09_2026', '1', '2026-09-16 20:30:13', '2026-09-16 20:30:13', 9),
(65, 'seq_PEN_09_2026', '1', '2026-09-16 20:30:13', '2026-09-16 20:30:13', 9),
(66, 'seq_HPP_09_2026', '1', '2026-09-16 20:30:13', '2026-09-16 20:30:13', 9),
(67, 'seq_PB_09_2026', '1', '2026-09-16 20:31:37', '2026-09-16 20:31:37', 9),
(68, 'seq_PEM_09_2026', '1', '2026-09-16 20:31:37', '2026-09-16 20:31:37', 9),
(69, 'daftar_harga_kolom', '[\"tier\",\"harga\",\"keterangan\",\"status\",\"barang\",\"aksi\"]', '2026-09-16 22:12:43', '2026-09-17 03:30:11', 10),
(70, 'barang_kolom', '[\"foto\",\"kode\",\"nama\",\"kategori\",\"warna\",\"stok\",\"harga_beli\",\"harga_jual\",\"aksi\"]', '2026-09-16 22:17:39', '2026-09-17 03:29:10', 10),
(71, 'nama_perusahaan', 'Perusahaan Saya', '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(72, 'alamat_perusahaan', '', '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(73, 'telepon_perusahaan', '', '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(74, 'email_perusahaan', '', '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(75, 'kota_perusahaan', '', '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(76, 'pajak', 'PPN', '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(77, 'seq_MAN_09_2026', '1', '2026-09-17 03:01:40', '2026-09-17 03:01:40', 11),
(78, 'nama_perusahaan', 'Perusahaan Saya', '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(79, 'alamat_perusahaan', '', '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(80, 'telepon_perusahaan', '', '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(81, 'email_perusahaan', '', '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(82, 'kota_perusahaan', '', '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(83, 'pajak', 'PPN', '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(84, 'seq_PB_09_2026', '2', '2026-09-18 05:37:20', '2026-09-18 05:43:42', 8),
(85, 'seq_PEM_09_2026', '3', '2026-09-18 05:37:34', '2026-09-18 05:45:41', 8),
(86, 'seq_KM_09_2026', '1', '2026-09-18 05:52:37', '2026-09-18 05:52:37', 8),
(87, 'seq_KAS_09_2026', '1', '2026-09-18 05:52:37', '2026-09-18 05:52:37', 8),
(88, 'seq_PB_09_2026', '1', '2026-09-18 06:21:39', '2026-09-18 06:21:39', 4),
(89, 'seq_PEM_09_2026', '1', '2026-09-18 06:21:39', '2026-09-18 06:21:39', 4),
(90, 'seq_MAN_09_2026', '4', '2026-09-18 19:11:30', '2026-09-18 20:03:10', 2),
(91, 'seq_PB_09_2026', '3', '2026-09-18 19:11:54', '2026-09-18 19:15:05', 2),
(92, 'seq_PEM_09_2026', '3', '2026-09-18 19:11:54', '2026-09-18 19:15:05', 2),
(93, 'nama_perusahaan', 'Perusahaan Saya', '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(94, 'alamat_perusahaan', '', '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(95, 'telepon_perusahaan', '', '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(96, 'email_perusahaan', '', '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(97, 'kota_perusahaan', '', '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(98, 'pajak', 'PPN', '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(99, 'seq_KM_09_2026', '1', '2026-09-19 22:48:25', '2026-09-19 22:48:25', 2),
(100, 'seq_KAS_09_2026', '1', '2026-09-19 22:48:25', '2026-09-19 22:48:25', 2),
(101, 'nama_perusahaan', 'Perusahaan Saya', '2026-09-20 07:24:08', '2026-09-20 07:24:08', 14),
(102, 'alamat_perusahaan', '', '2026-09-20 07:24:08', '2026-09-20 07:24:08', 14),
(103, 'telepon_perusahaan', '', '2026-09-20 07:24:08', '2026-09-20 07:24:08', 14),
(104, 'email_perusahaan', '', '2026-09-20 07:24:08', '2026-09-20 07:24:08', 14),
(105, 'kota_perusahaan', '', '2026-09-20 07:24:08', '2026-09-20 07:24:08', 14),
(106, 'pajak', 'PPN', '2026-09-20 07:24:08', '2026-09-20 07:24:08', 14),
(107, 'seq_PB_09_2026', '2', '2026-09-20 07:27:40', '2026-09-20 07:29:30', 14),
(108, 'seq_PEM_09_2026', '2', '2026-09-20 07:27:41', '2026-09-20 07:29:30', 14),
(109, 'seq_RPB_09_2026', '1', '2026-09-20 07:28:02', '2026-09-20 07:28:02', 14),
(110, 'seq_RET_09_2026', '2', '2026-09-20 07:28:02', '2026-09-20 07:34:45', 14),
(111, 'seq_PJ_09_2026', '2', '2026-09-20 07:31:39', '2026-09-20 07:33:30', 14),
(112, 'seq_PEN_09_2026', '4', '2026-09-20 07:31:39', '2026-09-20 07:36:28', 14),
(113, 'seq_HPP_09_2026', '4', '2026-09-20 07:31:39', '2026-09-20 07:34:45', 14),
(114, 'seq_KM_09_2026', '1', '2026-09-20 07:33:50', '2026-09-20 07:33:50', 14),
(115, 'seq_KAS_09_2026', '1', '2026-09-20 07:33:50', '2026-09-20 07:33:50', 14),
(116, 'seq_RPJ_09_2026', '1', '2026-09-20 07:34:45', '2026-09-20 07:34:45', 14),
(117, 'seq_PS_09_2026', '1', '2026-09-20 07:36:28', '2026-09-20 07:36:28', 14),
(118, 'seq_TUT_09_2026', '1', '2026-09-20 07:39:00', '2026-09-20 07:39:00', 14),
(119, 'seq_RPB_08_2026', '1', '2026-09-20 07:57:25', '2026-09-20 07:57:25', 14),
(120, 'seq_RET_08_2026', '1', '2026-09-20 07:57:25', '2026-09-20 07:57:25', 14);

-- --------------------------------------------------------

--
-- Table structure for table `penjualans`
--

CREATE TABLE `penjualans` (
  `id` bigint UNSIGNED NOT NULL,
  `rekening_id` bigint UNSIGNED DEFAULT NULL,
  `nomor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `gudang_id` bigint UNSIGNED DEFAULT NULL,
  `metode_bayar` enum('tunai','kredit') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tunai',
  `subtotal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `diskon` decimal(18,2) NOT NULL DEFAULT '0.00',
  `diskon_tipe` enum('nominal','persen') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nominal',
  `diskon_nominal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `pajak_id` bigint UNSIGNED DEFAULT NULL,
  `pajak_nominal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total` decimal(18,2) NOT NULL DEFAULT '0.00',
  `hpp_total` decimal(18,2) NOT NULL DEFAULT '0.00',
  `status` enum('posted','draft','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'posted',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `sync_harga` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approval_status` enum('draft','pending_review','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `approval_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `ongkir` decimal(18,2) NOT NULL DEFAULT '0.00',
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penjualans`
--

INSERT INTO `penjualans` (`id`, `rekening_id`, `nomor`, `tanggal`, `customer_id`, `gudang_id`, `metode_bayar`, `subtotal`, `diskon`, `diskon_tipe`, `diskon_nominal`, `pajak_id`, `pajak_nominal`, `total`, `hpp_total`, `status`, `keterangan`, `sync_harga`, `created_at`, `updated_at`, `created_by`, `updated_by`, `approved_by`, `approval_status`, `approval_reason`, `approved_at`, `ongkir`, `user_id`) VALUES
(1, 1, 'PJ/09/2026/0001', '2026-09-17', 10, 10, 'tunai', 105000.00, 0.00, 'nominal', 0.00, NULL, 0.00, 105000.00, 56000.00, 'posted', NULL, 1, '2026-09-16 20:30:13', '2026-09-16 20:30:13', 9, NULL, NULL, 'approved', NULL, NULL, 0.00, 9),
(2, 14, 'PJ/09/2026/0001', '2026-09-20', 15, 16, 'tunai', 13000.00, 0.00, 'nominal', 0.00, NULL, 1430.00, 14430.00, 10500.00, 'draft', NULL, 1, '2026-09-20 07:31:39', '2026-09-20 07:31:51', 14, NULL, NULL, 'approved', NULL, NULL, 0.00, 14),
(3, NULL, 'PJ/09/2026/0002', '2026-09-20', 15, 16, 'kredit', 10000.00, 0.00, 'nominal', 0.00, NULL, 0.00, 10000.00, 7500.00, 'posted', NULL, 1, '2026-09-20 07:33:30', '2026-09-20 07:33:30', 14, NULL, NULL, 'approved', NULL, NULL, 0.00, 14);

-- --------------------------------------------------------

--
-- Table structure for table `penjualan_items`
--

CREATE TABLE `penjualan_items` (
  `id` bigint UNSIGNED NOT NULL,
  `penjualan_id` bigint UNSIGNED NOT NULL,
  `jumlah` decimal(18,2) NOT NULL,
  `harga_satuan` decimal(18,2) NOT NULL,
  `diskon` decimal(18,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `hpp` decimal(18,2) NOT NULL DEFAULT '0.00',
  `hpp_total` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `barang_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penjualan_items`
--

INSERT INTO `penjualan_items` (`id`, `penjualan_id`, `jumlah`, `harga_satuan`, `diskon`, `subtotal`, `hpp`, `hpp_total`, `created_at`, `updated_at`, `barang_id`, `user_id`) VALUES
(1, 1, 7.00, 15000.00, 0.00, 105000.00, 8000.00, 56000.00, '2026-09-16 20:30:13', '2026-09-16 20:30:13', 4, 9),
(2, 2, 7.00, 2000.00, 1000.00, 13000.00, 1500.00, 10500.00, '2026-09-20 07:31:39', '2026-09-20 07:31:39', 28, 14),
(3, 3, 5.00, 2000.00, 0.00, 10000.00, 1500.00, 7500.00, '2026-09-20 07:33:30', '2026-09-20 07:33:30', 28, 14);

-- --------------------------------------------------------

--
-- Table structure for table `penyusutan`
--

CREATE TABLE `penyusutan` (
  `id` bigint UNSIGNED NOT NULL,
  `aset_id` bigint UNSIGNED NOT NULL,
  `periode` char(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `beban` decimal(18,2) NOT NULL,
  `akumulasi_setelah` decimal(18,2) NOT NULL,
  `nilai_buku_setelah` decimal(18,2) NOT NULL,
  `jurnal_id` bigint UNSIGNED DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `periode_akuntansi`
--

CREATE TABLE `periode_akuntansi` (
  `id` bigint UNSIGNED NOT NULL,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bulan` char(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` char(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_open` tinyint(1) NOT NULL DEFAULT '0',
  `is_closed` tinyint(1) NOT NULL DEFAULT '0',
  `tanggal_buka` date DEFAULT NULL,
  `tanggal_tutup` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `locked_at` timestamp NULL DEFAULT NULL,
  `locked_by` bigint UNSIGNED DEFAULT NULL,
  `lock_reason` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `periode_akuntansi`
--

INSERT INTO `periode_akuntansi` (`id`, `kode`, `bulan`, `tahun`, `is_open`, `is_closed`, `tanggal_buka`, `tanggal_tutup`, `created_at`, `updated_at`, `is_locked`, `locked_at`, `locked_by`, `lock_reason`, `user_id`) VALUES
(1, '202601', '01', '2026', 0, 0, NULL, NULL, '2026-09-15 01:27:17', '2026-09-15 01:27:17', 0, NULL, NULL, NULL, 1),
(2, '202602', '02', '2026', 0, 0, NULL, NULL, '2026-09-15 01:27:17', '2026-09-15 01:27:17', 0, NULL, NULL, NULL, 1),
(3, '202603', '03', '2026', 0, 0, NULL, NULL, '2026-09-15 01:27:17', '2026-09-15 01:27:17', 0, NULL, NULL, NULL, 1),
(4, '202604', '04', '2026', 0, 0, NULL, NULL, '2026-09-15 01:27:17', '2026-09-15 01:27:17', 0, NULL, NULL, NULL, 1),
(5, '202605', '05', '2026', 0, 0, NULL, NULL, '2026-09-15 01:27:17', '2026-09-15 01:27:17', 0, NULL, NULL, NULL, 1),
(6, '202606', '06', '2026', 0, 0, NULL, NULL, '2026-09-15 01:27:17', '2026-09-15 01:27:17', 0, NULL, NULL, NULL, 1),
(7, '202607', '07', '2026', 0, 0, NULL, NULL, '2026-09-15 01:27:17', '2026-09-15 01:27:17', 0, NULL, NULL, NULL, 1),
(8, '202608', '08', '2026', 0, 0, NULL, NULL, '2026-09-15 01:27:17', '2026-09-15 01:27:17', 0, NULL, NULL, NULL, 1),
(9, '202609', '09', '2026', 1, 0, '2026-09-15', NULL, '2026-09-15 01:27:17', '2026-09-15 01:27:17', 0, NULL, NULL, NULL, 1),
(10, '202610', '10', '2026', 0, 0, NULL, NULL, '2026-09-15 01:27:17', '2026-09-15 01:27:17', 0, NULL, NULL, NULL, 1),
(11, '202611', '11', '2026', 0, 0, NULL, NULL, '2026-09-15 01:27:17', '2026-09-15 01:27:17', 0, NULL, NULL, NULL, 1),
(12, '202612', '12', '2026', 0, 0, NULL, NULL, '2026-09-15 01:27:17', '2026-09-15 01:27:17', 0, NULL, NULL, NULL, 1),
(13, '202601', '01', '2026', 0, 0, NULL, NULL, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 0, NULL, NULL, NULL, 2),
(14, '202602', '02', '2026', 0, 0, NULL, NULL, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 0, NULL, NULL, NULL, 2),
(15, '202603', '03', '2026', 0, 0, NULL, NULL, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 0, NULL, NULL, NULL, 2),
(16, '202604', '04', '2026', 0, 0, NULL, NULL, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 0, NULL, NULL, NULL, 2),
(17, '202605', '05', '2026', 0, 0, NULL, NULL, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 0, NULL, NULL, NULL, 2),
(18, '202606', '06', '2026', 0, 0, NULL, NULL, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 0, NULL, NULL, NULL, 2),
(19, '202607', '07', '2026', 0, 0, NULL, NULL, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 0, NULL, NULL, NULL, 2),
(20, '202608', '08', '2026', 0, 0, NULL, NULL, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 0, NULL, NULL, NULL, 2),
(21, '202609', '09', '2026', 1, 0, '2026-09-15', NULL, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 0, NULL, NULL, NULL, 2),
(22, '202610', '10', '2026', 0, 0, NULL, NULL, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 0, NULL, NULL, NULL, 2),
(23, '202611', '11', '2026', 0, 0, NULL, NULL, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 0, NULL, NULL, NULL, 2),
(24, '202612', '12', '2026', 0, 0, NULL, NULL, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 0, NULL, NULL, NULL, 2),
(25, '202601', '01', '2026', 0, 0, NULL, NULL, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 0, NULL, NULL, NULL, 3),
(26, '202602', '02', '2026', 0, 0, NULL, NULL, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 0, NULL, NULL, NULL, 3),
(27, '202603', '03', '2026', 0, 0, NULL, NULL, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 0, NULL, NULL, NULL, 3),
(28, '202604', '04', '2026', 0, 0, NULL, NULL, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 0, NULL, NULL, NULL, 3),
(29, '202605', '05', '2026', 0, 0, NULL, NULL, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 0, NULL, NULL, NULL, 3),
(30, '202606', '06', '2026', 0, 0, NULL, NULL, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 0, NULL, NULL, NULL, 3),
(31, '202607', '07', '2026', 0, 0, NULL, NULL, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 0, NULL, NULL, NULL, 3),
(32, '202608', '08', '2026', 0, 0, NULL, NULL, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 0, NULL, NULL, NULL, 3),
(33, '202609', '09', '2026', 1, 0, '2026-09-16', NULL, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 0, NULL, NULL, NULL, 3),
(34, '202610', '10', '2026', 0, 0, NULL, NULL, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 0, NULL, NULL, NULL, 3),
(35, '202611', '11', '2026', 0, 0, NULL, NULL, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 0, NULL, NULL, NULL, 3),
(36, '202612', '12', '2026', 0, 0, NULL, NULL, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 0, NULL, NULL, NULL, 3),
(37, '202601', '01', '2026', 0, 0, NULL, NULL, '2026-09-15 18:42:59', '2026-09-15 18:42:59', 0, NULL, NULL, NULL, 4),
(38, '202602', '02', '2026', 0, 0, NULL, NULL, '2026-09-15 18:42:59', '2026-09-15 18:42:59', 0, NULL, NULL, NULL, 4),
(39, '202603', '03', '2026', 0, 0, NULL, NULL, '2026-09-15 18:42:59', '2026-09-15 18:42:59', 0, NULL, NULL, NULL, 4),
(40, '202604', '04', '2026', 0, 0, NULL, NULL, '2026-09-15 18:42:59', '2026-09-15 18:42:59', 0, NULL, NULL, NULL, 4),
(41, '202605', '05', '2026', 0, 0, NULL, NULL, '2026-09-15 18:42:59', '2026-09-15 18:42:59', 0, NULL, NULL, NULL, 4),
(42, '202606', '06', '2026', 0, 0, NULL, NULL, '2026-09-15 18:42:59', '2026-09-15 18:42:59', 0, NULL, NULL, NULL, 4),
(43, '202607', '07', '2026', 0, 0, NULL, NULL, '2026-09-15 18:42:59', '2026-09-15 18:42:59', 0, NULL, NULL, NULL, 4),
(44, '202608', '08', '2026', 0, 0, NULL, NULL, '2026-09-15 18:42:59', '2026-09-15 18:42:59', 0, NULL, NULL, NULL, 4),
(45, '202609', '09', '2026', 1, 0, '2026-09-16', NULL, '2026-09-15 18:42:59', '2026-09-15 18:42:59', 0, NULL, NULL, NULL, 4),
(46, '202610', '10', '2026', 0, 0, NULL, NULL, '2026-09-15 18:42:59', '2026-09-15 18:42:59', 0, NULL, NULL, NULL, 4),
(47, '202611', '11', '2026', 0, 0, NULL, NULL, '2026-09-15 18:42:59', '2026-09-15 18:42:59', 0, NULL, NULL, NULL, 4),
(48, '202612', '12', '2026', 0, 0, NULL, NULL, '2026-09-15 18:42:59', '2026-09-15 18:42:59', 0, NULL, NULL, NULL, 4),
(49, '202601', '01', '2026', 0, 0, NULL, NULL, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 0, NULL, NULL, NULL, 5),
(50, '202602', '02', '2026', 0, 0, NULL, NULL, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 0, NULL, NULL, NULL, 5),
(51, '202603', '03', '2026', 0, 0, NULL, NULL, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 0, NULL, NULL, NULL, 5),
(52, '202604', '04', '2026', 0, 0, NULL, NULL, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 0, NULL, NULL, NULL, 5),
(53, '202605', '05', '2026', 0, 0, NULL, NULL, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 0, NULL, NULL, NULL, 5),
(54, '202606', '06', '2026', 0, 0, NULL, NULL, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 0, NULL, NULL, NULL, 5),
(55, '202607', '07', '2026', 0, 0, NULL, NULL, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 0, NULL, NULL, NULL, 5),
(56, '202608', '08', '2026', 0, 0, NULL, NULL, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 0, NULL, NULL, NULL, 5),
(57, '202609', '09', '2026', 1, 0, '2026-09-16', NULL, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 0, NULL, NULL, NULL, 5),
(58, '202610', '10', '2026', 0, 0, NULL, NULL, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 0, NULL, NULL, NULL, 5),
(59, '202611', '11', '2026', 0, 0, NULL, NULL, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 0, NULL, NULL, NULL, 5),
(60, '202612', '12', '2026', 0, 0, NULL, NULL, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 0, NULL, NULL, NULL, 5),
(61, '202601', '01', '2026', 0, 0, NULL, NULL, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 0, NULL, NULL, NULL, 6),
(62, '202602', '02', '2026', 0, 0, NULL, NULL, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 0, NULL, NULL, NULL, 6),
(63, '202603', '03', '2026', 0, 0, NULL, NULL, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 0, NULL, NULL, NULL, 6),
(64, '202604', '04', '2026', 0, 0, NULL, NULL, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 0, NULL, NULL, NULL, 6),
(65, '202605', '05', '2026', 0, 0, NULL, NULL, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 0, NULL, NULL, NULL, 6),
(66, '202606', '06', '2026', 0, 0, NULL, NULL, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 0, NULL, NULL, NULL, 6),
(67, '202607', '07', '2026', 0, 0, NULL, NULL, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 0, NULL, NULL, NULL, 6),
(68, '202608', '08', '2026', 0, 0, NULL, NULL, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 0, NULL, NULL, NULL, 6),
(69, '202609', '09', '2026', 1, 0, '2026-09-16', NULL, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 0, NULL, NULL, NULL, 6),
(70, '202610', '10', '2026', 0, 0, NULL, NULL, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 0, NULL, NULL, NULL, 6),
(71, '202611', '11', '2026', 0, 0, NULL, NULL, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 0, NULL, NULL, NULL, 6),
(72, '202612', '12', '2026', 0, 0, NULL, NULL, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 0, NULL, NULL, NULL, 6),
(73, '202601', '01', '2026', 0, 0, NULL, NULL, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 0, NULL, NULL, NULL, 7),
(74, '202602', '02', '2026', 0, 0, NULL, NULL, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 0, NULL, NULL, NULL, 7),
(75, '202603', '03', '2026', 0, 0, NULL, NULL, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 0, NULL, NULL, NULL, 7),
(76, '202604', '04', '2026', 0, 0, NULL, NULL, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 0, NULL, NULL, NULL, 7),
(77, '202605', '05', '2026', 0, 0, NULL, NULL, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 0, NULL, NULL, NULL, 7),
(78, '202606', '06', '2026', 0, 0, NULL, NULL, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 0, NULL, NULL, NULL, 7),
(79, '202607', '07', '2026', 0, 0, NULL, NULL, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 0, NULL, NULL, NULL, 7),
(80, '202608', '08', '2026', 0, 0, NULL, NULL, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 0, NULL, NULL, NULL, 7),
(81, '202609', '09', '2026', 1, 0, '2026-09-16', NULL, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 0, NULL, NULL, NULL, 7),
(82, '202610', '10', '2026', 0, 0, NULL, NULL, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 0, NULL, NULL, NULL, 7),
(83, '202611', '11', '2026', 0, 0, NULL, NULL, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 0, NULL, NULL, NULL, 7),
(84, '202612', '12', '2026', 0, 0, NULL, NULL, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 0, NULL, NULL, NULL, 7),
(85, '202601', '01', '2026', 0, 0, NULL, NULL, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 0, NULL, NULL, NULL, 8),
(86, '202602', '02', '2026', 0, 0, NULL, NULL, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 0, NULL, NULL, NULL, 8),
(87, '202603', '03', '2026', 0, 0, NULL, NULL, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 0, NULL, NULL, NULL, 8),
(88, '202604', '04', '2026', 0, 0, NULL, NULL, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 0, NULL, NULL, NULL, 8),
(89, '202605', '05', '2026', 0, 0, NULL, NULL, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 0, NULL, NULL, NULL, 8),
(90, '202606', '06', '2026', 0, 0, NULL, NULL, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 0, NULL, NULL, NULL, 8),
(91, '202607', '07', '2026', 0, 0, NULL, NULL, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 0, NULL, NULL, NULL, 8),
(92, '202608', '08', '2026', 0, 0, NULL, NULL, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 0, NULL, NULL, NULL, 8),
(93, '202609', '09', '2026', 1, 0, '2026-09-16', NULL, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 0, NULL, NULL, NULL, 8),
(94, '202610', '10', '2026', 0, 0, NULL, NULL, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 0, NULL, NULL, NULL, 8),
(95, '202611', '11', '2026', 0, 0, NULL, NULL, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 0, NULL, NULL, NULL, 8),
(96, '202612', '12', '2026', 0, 0, NULL, NULL, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 0, NULL, NULL, NULL, 8),
(97, '202601', '01', '2026', 0, 0, NULL, NULL, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 0, NULL, NULL, NULL, 9),
(98, '202602', '02', '2026', 0, 0, NULL, NULL, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 0, NULL, NULL, NULL, 9),
(99, '202603', '03', '2026', 0, 0, NULL, NULL, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 0, NULL, NULL, NULL, 9),
(100, '202604', '04', '2026', 0, 0, NULL, NULL, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 0, NULL, NULL, NULL, 9),
(101, '202605', '05', '2026', 0, 0, NULL, NULL, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 0, NULL, NULL, NULL, 9),
(102, '202606', '06', '2026', 0, 0, NULL, NULL, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 0, NULL, NULL, NULL, 9),
(103, '202607', '07', '2026', 0, 0, NULL, NULL, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 0, NULL, NULL, NULL, 9),
(104, '202608', '08', '2026', 0, 0, NULL, NULL, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 0, NULL, NULL, NULL, 9),
(105, '202609', '09', '2026', 1, 0, '2026-09-17', NULL, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 0, NULL, NULL, NULL, 9),
(106, '202610', '10', '2026', 0, 0, NULL, NULL, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 0, NULL, NULL, NULL, 9),
(107, '202611', '11', '2026', 0, 0, NULL, NULL, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 0, NULL, NULL, NULL, 9),
(108, '202612', '12', '2026', 0, 0, NULL, NULL, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 0, NULL, NULL, NULL, 9),
(109, '202601', '01', '2026', 0, 0, NULL, NULL, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 0, NULL, NULL, NULL, 10),
(110, '202602', '02', '2026', 0, 0, NULL, NULL, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 0, NULL, NULL, NULL, 10),
(111, '202603', '03', '2026', 0, 0, NULL, NULL, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 0, NULL, NULL, NULL, 10),
(112, '202604', '04', '2026', 0, 0, NULL, NULL, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 0, NULL, NULL, NULL, 10),
(113, '202605', '05', '2026', 0, 0, NULL, NULL, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 0, NULL, NULL, NULL, 10),
(114, '202606', '06', '2026', 0, 0, NULL, NULL, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 0, NULL, NULL, NULL, 10),
(115, '202607', '07', '2026', 0, 0, NULL, NULL, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 0, NULL, NULL, NULL, 10),
(116, '202608', '08', '2026', 0, 0, NULL, NULL, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 0, NULL, NULL, NULL, 10),
(117, '202609', '09', '2026', 1, 0, '2026-09-17', NULL, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 0, NULL, NULL, NULL, 10),
(118, '202610', '10', '2026', 0, 0, NULL, NULL, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 0, NULL, NULL, NULL, 10),
(119, '202611', '11', '2026', 0, 0, NULL, NULL, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 0, NULL, NULL, NULL, 10),
(120, '202612', '12', '2026', 0, 0, NULL, NULL, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 0, NULL, NULL, NULL, 10),
(121, '202601', '01', '2026', 0, 0, NULL, NULL, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 0, NULL, NULL, NULL, 11),
(122, '202602', '02', '2026', 0, 0, NULL, NULL, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 0, NULL, NULL, NULL, 11),
(123, '202603', '03', '2026', 0, 0, NULL, NULL, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 0, NULL, NULL, NULL, 11),
(124, '202604', '04', '2026', 0, 0, NULL, NULL, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 0, NULL, NULL, NULL, 11),
(125, '202605', '05', '2026', 0, 0, NULL, NULL, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 0, NULL, NULL, NULL, 11),
(126, '202606', '06', '2026', 0, 0, NULL, NULL, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 0, NULL, NULL, NULL, 11),
(127, '202607', '07', '2026', 0, 0, NULL, NULL, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 0, NULL, NULL, NULL, 11),
(128, '202608', '08', '2026', 0, 0, NULL, NULL, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 0, NULL, NULL, NULL, 11),
(129, '202609', '09', '2026', 1, 0, '2026-09-17', NULL, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 0, NULL, NULL, NULL, 11),
(130, '202610', '10', '2026', 0, 0, NULL, NULL, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 0, NULL, NULL, NULL, 11),
(131, '202611', '11', '2026', 0, 0, NULL, NULL, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 0, NULL, NULL, NULL, 11),
(132, '202612', '12', '2026', 0, 0, NULL, NULL, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 0, NULL, NULL, NULL, 11),
(133, '202601', '01', '2026', 0, 0, NULL, NULL, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 0, NULL, NULL, NULL, 12),
(134, '202602', '02', '2026', 0, 0, NULL, NULL, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 0, NULL, NULL, NULL, 12),
(135, '202603', '03', '2026', 0, 0, NULL, NULL, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 0, NULL, NULL, NULL, 12),
(136, '202604', '04', '2026', 0, 0, NULL, NULL, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 0, NULL, NULL, NULL, 12),
(137, '202605', '05', '2026', 0, 0, NULL, NULL, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 0, NULL, NULL, NULL, 12),
(138, '202606', '06', '2026', 0, 0, NULL, NULL, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 0, NULL, NULL, NULL, 12),
(139, '202607', '07', '2026', 0, 0, NULL, NULL, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 0, NULL, NULL, NULL, 12),
(140, '202608', '08', '2026', 0, 0, NULL, NULL, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 0, NULL, NULL, NULL, 12),
(141, '202609', '09', '2026', 1, 0, '2026-09-17', NULL, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 0, NULL, NULL, NULL, 12),
(142, '202610', '10', '2026', 0, 0, NULL, NULL, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 0, NULL, NULL, NULL, 12),
(143, '202611', '11', '2026', 0, 0, NULL, NULL, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 0, NULL, NULL, NULL, 12),
(144, '202612', '12', '2026', 0, 0, NULL, NULL, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 0, NULL, NULL, NULL, 12),
(145, '202601', '01', '2026', 0, 0, NULL, NULL, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 0, NULL, NULL, NULL, 13),
(146, '202602', '02', '2026', 0, 0, NULL, NULL, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 0, NULL, NULL, NULL, 13),
(147, '202603', '03', '2026', 0, 0, NULL, NULL, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 0, NULL, NULL, NULL, 13),
(148, '202604', '04', '2026', 0, 0, NULL, NULL, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 0, NULL, NULL, NULL, 13),
(149, '202605', '05', '2026', 0, 0, NULL, NULL, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 0, NULL, NULL, NULL, 13),
(150, '202606', '06', '2026', 0, 0, NULL, NULL, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 0, NULL, NULL, NULL, 13),
(151, '202607', '07', '2026', 0, 0, NULL, NULL, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 0, NULL, NULL, NULL, 13),
(152, '202608', '08', '2026', 0, 0, NULL, NULL, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 0, NULL, NULL, NULL, 13),
(153, '202609', '09', '2026', 1, 0, '2026-09-19', NULL, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 0, NULL, NULL, NULL, 13),
(154, '202610', '10', '2026', 0, 0, NULL, NULL, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 0, NULL, NULL, NULL, 13),
(155, '202611', '11', '2026', 0, 0, NULL, NULL, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 0, NULL, NULL, NULL, 13),
(156, '202612', '12', '2026', 0, 0, NULL, NULL, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 0, NULL, NULL, NULL, 13),
(157, '202601', '01', '2026', 0, 0, NULL, NULL, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 0, NULL, NULL, NULL, 14),
(158, '202602', '02', '2026', 0, 0, NULL, NULL, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 0, NULL, NULL, NULL, 14),
(159, '202603', '03', '2026', 0, 0, NULL, NULL, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 0, NULL, NULL, NULL, 14),
(160, '202604', '04', '2026', 0, 0, NULL, NULL, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 0, NULL, NULL, NULL, 14),
(161, '202605', '05', '2026', 0, 0, NULL, NULL, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 0, NULL, NULL, NULL, 14),
(162, '202606', '06', '2026', 0, 0, NULL, NULL, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 0, NULL, NULL, NULL, 14),
(163, '202607', '07', '2026', 0, 0, NULL, NULL, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 0, NULL, NULL, NULL, 14),
(164, '202608', '08', '2026', 1, 0, '2026-09-20', NULL, '2026-09-20 07:24:08', '2026-09-20 07:56:57', 0, NULL, NULL, NULL, 14),
(165, '202609', '09', '2026', 0, 1, '2026-09-20', '2026-09-20', '2026-09-20 07:24:08', '2026-09-20 07:39:00', 0, NULL, NULL, NULL, 14),
(166, '202610', '10', '2026', 0, 0, NULL, NULL, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 0, NULL, NULL, NULL, 14),
(167, '202611', '11', '2026', 0, 0, NULL, NULL, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 0, NULL, NULL, NULL, 14),
(168, '202612', '12', '2026', 0, 0, NULL, NULL, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 0, NULL, NULL, NULL, 14);

-- --------------------------------------------------------

--
-- Table structure for table `perubahan_stok`
--

CREATE TABLE `perubahan_stok` (
  `id` bigint UNSIGNED NOT NULL,
  `nomor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `jenis` enum('rusak','hilang','salah','lebih','lainnya') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'lainnya',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `status` enum('posted','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'posted',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approval_status` enum('draft','pending_review','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `approval_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `perubahan_stok`
--

INSERT INTO `perubahan_stok` (`id`, `nomor`, `tanggal`, `jenis`, `keterangan`, `status`, `created_by`, `updated_by`, `approved_by`, `approval_status`, `approval_reason`, `approved_at`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'PS/09/2026/0001', '2026-09-20', 'salah', NULL, 'posted', 14, NULL, NULL, 'approved', NULL, NULL, '2026-09-20 07:36:28', '2026-09-20 07:36:28', 14);

-- --------------------------------------------------------

--
-- Table structure for table `perubahan_stok_items`
--

CREATE TABLE `perubahan_stok_items` (
  `id` bigint UNSIGNED NOT NULL,
  `perubahan_stok_id` bigint UNSIGNED NOT NULL,
  `barang_id` bigint UNSIGNED DEFAULT NULL,
  `arah` enum('masuk','keluar') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'masuk',
  `jumlah` decimal(18,2) NOT NULL,
  `harga` decimal(18,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `perubahan_stok_items`
--

INSERT INTO `perubahan_stok_items` (`id`, `perubahan_stok_id`, `barang_id`, `arah`, `jumlah`, `harga`, `subtotal`, `keterangan`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 1, 28, 'keluar', 1.00, 1500.00, 1500.00, NULL, '2026-09-20 07:36:28', '2026-09-20 07:36:28', 14);

-- --------------------------------------------------------

--
-- Table structure for table `rekenings`
--

CREATE TABLE `rekenings` (
  `id` bigint UNSIGNED NOT NULL,
  `jenis` enum('kas','bank') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kas',
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_rekening` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_pemilik` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `akun_id` bigint UNSIGNED NOT NULL,
  `saldo_awal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `is_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rekenings`
--

INSERT INTO `rekenings` (`id`, `jenis`, `nama`, `nomor_rekening`, `nama_pemilik`, `akun_id`, `saldo_awal`, `is_aktif`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'bank', 'Gopay', '085855366044', 'Achmad Fadli Faturrochim', 293, 250000.00, 1, '2026-09-16 20:12:51', '2026-09-19 05:25:41', 9),
(2, 'bank', 'Bank UOB Indonesia', '737315567985', 'Yabes tampan', 365, 1000000.00, 1, '2026-09-17 03:01:40', '2026-09-17 03:01:40', 11),
(3, 'bank', 'Wileo Fajar Anggara', '615801013089533', 'Wileo', 329, 0.00, 1, '2026-09-17 03:43:32', '2026-09-17 03:43:32', 10),
(4, 'bank', 'Seabank', NULL, NULL, 256, 0.00, 1, '2026-09-18 05:01:01', '2026-09-18 05:01:01', 8),
(7, 'bank', 'BRI', '098664332244', 'Oscar', 112, 0.00, 1, '2026-09-18 06:16:29', '2026-09-18 06:16:29', 4),
(8, 'kas', 'Kas Umun', NULL, NULL, 434, 0.00, 1, '2026-09-18 06:17:20', '2026-09-18 20:03:10', 4),
(9, 'bank', 'MANDIRI', NULL, NULL, 435, 0.00, 1, '2026-09-18 06:17:41', '2026-09-18 20:03:10', 4),
(10, 'bank', 'Mandiri', '123456789', 'oscar', 41, 0.00, 1, '2026-09-18 19:11:14', '2026-09-18 19:11:14', 2),
(11, 'kas', 'Kas Utama', NULL, NULL, 40, 100000.00, 1, '2026-09-18 19:11:30', '2026-09-18 19:11:30', 2),
(12, 'bank', 'BRI', '123456789', 'Wahyu', 436, 0.00, 1, '2026-09-18 19:12:23', '2026-09-18 20:03:10', 2),
(13, 'bank', 'BRI 2', '123456789', NULL, 437, 100000.00, 1, '2026-09-18 19:15:39', '2026-09-18 20:03:10', 2),
(14, 'kas', 'UTAMA', NULL, NULL, 511, 0.00, 1, '2026-09-20 07:27:00', '2026-09-20 07:27:00', 14);

-- --------------------------------------------------------

--
-- Table structure for table `retur_pembelian`
--

CREATE TABLE `retur_pembelian` (
  `id` bigint UNSIGNED NOT NULL,
  `nomor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `pembelian_id` bigint UNSIGNED DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `status` enum('posted','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'posted',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approval_status` enum('draft','pending_review','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `approval_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `retur_pembelian`
--

INSERT INTO `retur_pembelian` (`id`, `nomor`, `tanggal`, `pembelian_id`, `keterangan`, `status`, `created_by`, `updated_by`, `approved_by`, `approval_status`, `approval_reason`, `approved_at`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'RPB/09/2026/0001', '2026-09-20', 8, NULL, 'posted', 14, NULL, NULL, 'approved', NULL, NULL, '2026-09-20 07:28:02', '2026-09-20 07:28:02', 14),
(3, 'RPB/08/2026/0001', '2026-08-20', 9, NULL, 'posted', 14, NULL, NULL, 'approved', NULL, NULL, '2026-09-20 07:57:25', '2026-09-20 07:57:25', 14);

-- --------------------------------------------------------

--
-- Table structure for table `retur_pembelian_items`
--

CREATE TABLE `retur_pembelian_items` (
  `id` bigint UNSIGNED NOT NULL,
  `retur_pembelian_id` bigint UNSIGNED NOT NULL,
  `pembelian_item_id` bigint UNSIGNED DEFAULT NULL,
  `barang_id` bigint UNSIGNED DEFAULT NULL,
  `jumlah` decimal(18,2) NOT NULL,
  `harga_satuan` decimal(18,2) NOT NULL,
  `subtotal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `retur_pembelian_items`
--

INSERT INTO `retur_pembelian_items` (`id`, `retur_pembelian_id`, `pembelian_item_id`, `barang_id`, `jumlah`, `harga_satuan`, `subtotal`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 1, 71, 28, 100.00, 1000.00, 100000.00, '2026-09-20 07:28:02', '2026-09-20 07:28:02', 14),
(3, 3, 72, 28, 100.00, 2000.00, 200000.00, '2026-09-20 07:57:25', '2026-09-20 07:57:25', 14);

-- --------------------------------------------------------

--
-- Table structure for table `retur_penjualan`
--

CREATE TABLE `retur_penjualan` (
  `id` bigint UNSIGNED NOT NULL,
  `nomor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `penjualan_id` bigint UNSIGNED DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `status` enum('posted','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'posted',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approval_status` enum('draft','pending_review','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `approval_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `retur_penjualan`
--

INSERT INTO `retur_penjualan` (`id`, `nomor`, `tanggal`, `penjualan_id`, `keterangan`, `status`, `created_by`, `updated_by`, `approved_by`, `approval_status`, `approval_reason`, `approved_at`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'RPJ/09/2026/0001', '2026-09-20', 3, NULL, 'posted', 14, NULL, NULL, 'approved', NULL, NULL, '2026-09-20 07:34:45', '2026-09-20 07:34:45', 14);

-- --------------------------------------------------------

--
-- Table structure for table `retur_penjualan_items`
--

CREATE TABLE `retur_penjualan_items` (
  `id` bigint UNSIGNED NOT NULL,
  `retur_penjualan_id` bigint UNSIGNED NOT NULL,
  `penjualan_item_id` bigint UNSIGNED DEFAULT NULL,
  `barang_id` bigint UNSIGNED DEFAULT NULL,
  `jumlah` decimal(18,2) NOT NULL,
  `harga_satuan` decimal(18,2) NOT NULL,
  `hpp` decimal(18,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `hpp_total` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `retur_penjualan_items`
--

INSERT INTO `retur_penjualan_items` (`id`, `retur_penjualan_id`, `penjualan_item_id`, `barang_id`, `jumlah`, `harga_satuan`, `hpp`, `subtotal`, `hpp_total`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 1, 3, 28, 5.00, 2000.00, 1500.00, 10000.00, 7500.00, '2026-09-20 07:34:45', '2026-09-20 07:34:45', 14);

-- --------------------------------------------------------

--
-- Table structure for table `satuan`
--

CREATE TABLE `satuan` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `satuan`
--

INSERT INTO `satuan` (`id`, `nama`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'pcs', '2026-09-15 21:08:34', '2026-09-15 21:08:34', 2),
(2, 'Kg', '2026-09-15 21:51:53', '2026-09-15 21:51:53', 6),
(3, 'Pcs', '2026-09-16 20:19:12', '2026-09-16 20:19:12', 9),
(4, 'Kg', '2026-09-16 20:20:04', '2026-09-16 20:20:04', 9),
(5, 'Kg', '2026-09-16 21:44:54', '2026-09-16 21:44:54', 8),
(6, '30 detik', '2026-09-17 03:03:00', '2026-09-17 03:03:00', 11),
(7, '1 pcs', '2026-09-17 03:48:36', '2026-09-17 03:48:36', 10),
(8, 'Pcs', '2026-09-18 06:20:44', '2026-09-18 06:20:44', 4),
(9, 'PCS', '2026-09-20 07:25:03', '2026-09-20 07:25:03', 14);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('En1w9vwj8wdF8Qn5pZgPyj1q6RGzcTxs9PGEXbMZ', 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiIzMms0R05IeTJFejRBRGVYa3JDMURSMDRSVkI3MzlydWZCWGkzQ3d2IiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2thc3Byby5zaW5kZWxhcmFzdGVjaG5vbG9neS5teS5pZFwvbm90aWZpa2FzaVwvcmVjZW50Iiwicm91dGUiOiJub3RpZmlrYXNpLnJlY2VudCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjo4fQ==', 1790042572);

-- --------------------------------------------------------

--
-- Table structure for table `stok_gudang`
--

CREATE TABLE `stok_gudang` (
  `id` bigint UNSIGNED NOT NULL,
  `barang_id` bigint UNSIGNED NOT NULL,
  `gudang_id` bigint UNSIGNED NOT NULL,
  `qty` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stok_gudang`
--

INSERT INTO `stok_gudang` (`id`, `barang_id`, `gudang_id`, `qty`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 1, 3, 0.50, '2026-09-15 21:09:01', '2026-09-15 21:09:01', 2),
(2, 2, 7, 0.00, '2026-09-15 21:52:08', '2026-09-15 21:52:08', 6),
(3, 3, 3, 220.00, '2026-09-16 20:26:04', '2026-09-18 19:15:05', 2),
(4, 4, 10, 18.00, '2026-09-16 20:28:42', '2026-09-16 20:31:37', 9),
(5, 5, 9, 49.12, '2026-09-16 21:45:12', '2026-09-18 05:45:41', 8),
(6, 6, 9, 0.00, '2026-09-18 05:02:58', '2026-09-18 05:02:58', 8),
(7, 7, 9, 0.00, '2026-09-18 05:03:28', '2026-09-18 05:03:28', 8),
(8, 8, 9, 24.84, '2026-09-18 05:03:58', '2026-09-18 05:45:41', 8),
(9, 9, 9, 25.19, '2026-09-18 05:04:21', '2026-09-18 05:45:41', 8),
(10, 10, 9, 25.29, '2026-09-18 05:04:47', '2026-09-18 05:45:41', 8),
(11, 11, 9, 0.00, '2026-09-18 05:05:12', '2026-09-18 05:05:12', 8),
(12, 12, 9, 0.00, '2026-09-18 05:05:43', '2026-09-18 05:05:43', 8),
(13, 13, 9, 0.00, '2026-09-18 05:06:12', '2026-09-18 05:06:12', 8),
(14, 14, 9, 24.21, '2026-09-18 05:06:35', '2026-09-18 05:45:41', 8),
(15, 15, 9, 25.00, '2026-09-18 05:07:01', '2026-09-18 05:45:41', 8),
(16, 16, 9, 11.40, '2026-09-18 05:22:42', '2026-09-18 05:45:41', 8),
(17, 17, 9, 0.00, '2026-09-18 05:23:50', '2026-09-18 05:23:50', 8),
(18, 18, 9, 0.00, '2026-09-18 05:24:35', '2026-09-18 05:24:35', 8),
(19, 19, 9, 5.05, '2026-09-18 05:25:06', '2026-09-18 05:45:41', 8),
(20, 20, 9, 5.00, '2026-09-18 05:25:35', '2026-09-18 05:45:41', 8),
(21, 21, 9, 5.10, '2026-09-18 05:26:55', '2026-09-18 05:45:41', 8),
(22, 22, 9, 0.00, '2026-09-18 05:28:25', '2026-09-18 05:28:25', 8),
(23, 23, 9, 0.00, '2026-09-18 05:28:47', '2026-09-18 05:28:47', 8),
(24, 24, 9, 4.00, '2026-09-18 05:29:10', '2026-09-18 05:45:41', 8),
(25, 25, 9, 5.00, '2026-09-18 05:29:31', '2026-09-18 05:45:41', 8),
(26, 26, 9, 10.05, '2026-09-18 05:30:13', '2026-09-18 05:45:41', 8),
(27, 27, 5, 100.00, '2026-09-18 06:21:04', '2026-09-18 06:21:39', 4),
(28, 28, 16, 99.00, '2026-09-20 07:25:49', '2026-09-20 07:57:25', 14);

-- --------------------------------------------------------

--
-- Table structure for table `stok_opname`
--

CREATE TABLE `stok_opname` (
  `id` bigint UNSIGNED NOT NULL,
  `nomor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `status` enum('posted','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'posted',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approval_status` enum('draft','pending_review','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `approval_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stok_opname_items`
--

CREATE TABLE `stok_opname_items` (
  `id` bigint UNSIGNED NOT NULL,
  `stok_opname_id` bigint UNSIGNED NOT NULL,
  `barang_id` bigint UNSIGNED DEFAULT NULL,
  `stok_sistem` decimal(18,2) NOT NULL DEFAULT '0.00',
  `stok_fisik` decimal(18,2) NOT NULL DEFAULT '0.00',
  `selisih` decimal(18,2) NOT NULL DEFAULT '0.00',
  `harga` decimal(18,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint UNSIGNED NOT NULL,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telepon` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `is_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `kode`, `nama`, `alamat`, `telepon`, `email`, `npwp`, `keterangan`, `is_aktif`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-15 01:26:29', '2026-09-15 01:26:29', NULL),
(2, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-15 01:27:17', '2026-09-15 01:27:17', 1),
(3, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-15 03:56:03', '2026-09-15 03:56:03', 2),
(4, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-15 18:30:38', '2026-09-15 18:30:38', 3),
(5, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-15 18:42:59', '2026-09-15 18:42:59', 4),
(6, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-15 18:53:01', '2026-09-15 18:53:01', 5),
(7, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-15 21:49:42', '2026-09-15 21:49:42', 6),
(8, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-15 22:01:40', '2026-09-15 22:01:40', 7),
(9, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-16 00:21:17', '2026-09-16 00:21:17', 8),
(10, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-16 19:59:50', '2026-09-16 19:59:50', 9),
(11, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-16 20:28:23', '2026-09-16 20:28:23', 10),
(12, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-17 02:52:17', '2026-09-17 02:52:17', 11),
(13, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-17 04:06:13', '2026-09-17 04:06:13', 12),
(14, 'SUP-0010', 'Hilmi', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-18 05:31:33', '2026-09-18 05:31:33', 8),
(15, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-19 03:49:29', '2026-09-19 03:49:29', 13),
(16, 'UMUM', 'UMUM', NULL, NULL, NULL, NULL, NULL, 1, '2026-09-20 07:24:08', '2026-09-20 07:24:08', 14);

-- --------------------------------------------------------

--
-- Table structure for table `transfer_gudang`
--

CREATE TABLE `transfer_gudang` (
  `id` bigint UNSIGNED NOT NULL,
  `nomor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `gudang_asal` bigint UNSIGNED NOT NULL,
  `gudang_tujuan` bigint UNSIGNED NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `status` enum('posted','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'posted',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transfer_gudang_items`
--

CREATE TABLE `transfer_gudang_items` (
  `id` bigint UNSIGNED NOT NULL,
  `transfer_gudang_id` bigint UNSIGNED NOT NULL,
  `barang_id` bigint UNSIGNED DEFAULT NULL,
  `jumlah` decimal(18,2) NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tutup_buku`
--

CREATE TABLE `tutup_buku` (
  `id` bigint UNSIGNED NOT NULL,
  `periode_id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `laba_rugi` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total_pendapatan` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total_beban` decimal(18,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tutup_buku`
--

INSERT INTO `tutup_buku` (`id`, `periode_id`, `tanggal`, `laba_rugi`, `total_pendapatan`, `total_beban`, `keterangan`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 165, '2026-09-20', -1500.00, 0.00, 1500.00, NULL, '2026-09-20 07:39:00', '2026-09-20 07:39:00', 14);

-- --------------------------------------------------------

--
-- Table structure for table `tutup_buku_tahunan`
--

CREATE TABLE `tutup_buku_tahunan` (
  `id` bigint UNSIGNED NOT NULL,
  `tahun` char(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `laba_rugi` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total_pendapatan` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total_beban` decimal(18,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'free',
  `plan_expires_at` timestamp NULL DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `status`, `remember_token`, `created_at`, `updated_at`, `google_id`, `plan`, `plan_expires_at`, `trial_ends_at`) VALUES
(1, 'Administrator', 'admin@keuangan.test', '2026-09-15 01:27:16', '$2y$12$7dd8hRq60U.0yFnD5IAr9OQiotnvFqF04E9OniD/eCY4F9hRzs7Ry', 'aktif', NULL, '2026-09-15 01:27:16', '2026-09-15 01:27:16', NULL, 'pro', NULL, NULL),
(2, 'sindelaras technology', 'sindelarastechnology@gmail.com', '2026-09-15 03:56:03', NULL, 'aktif', NULL, '2026-09-15 03:56:03', '2026-09-17 06:11:38', '112933348604873776654', 'pro', NULL, NULL),
(3, 'Pradana Luluk', 'pradanaluluk@gmail.com', '2026-09-15 18:30:38', NULL, 'aktif', NULL, '2026-09-15 18:30:38', '2026-09-15 18:30:38', '104871261644945619832', 'pro', NULL, NULL),
(4, 'Ma\'ruf Iskandar', 'marufiskandar69@gmail.com', '2026-09-15 18:42:58', NULL, 'aktif', NULL, '2026-09-15 18:42:58', '2026-09-15 18:42:58', '102416599325866183223', 'pro', NULL, NULL),
(6, 'PC', 'desktoppc3121@gmail.com', '2026-09-15 21:49:42', NULL, 'aktif', NULL, '2026-09-15 21:49:42', '2026-09-16 01:49:53', '106767045324031602900', 'pro', NULL, NULL),
(7, 'aldzaky', 'admin@aldzaky.com', NULL, '$2y$12$G1UOliGs20HxKd3pGesDpe5LOn.cS7SEEbRp8b9VlW/8ZLDcdA/va', 'aktif', NULL, '2026-09-15 22:01:31', '2026-09-15 22:01:31', NULL, 'pro', NULL, NULL),
(8, 'aldzaky', 'aldzakycorp@gmail.com', '2026-09-16 00:21:39', '$2y$12$48r9q3mOsHoOrNxGqeKE0uqjJgQkbo65F/vBmf5K8Duap5KWWsugq', 'aktif', NULL, '2026-09-16 00:21:11', '2026-09-18 04:59:56', '109037498174822556298', 'pro', NULL, NULL),
(9, 'Achmad Fadli', 'achmad12fadli03@gmail.com', '2026-09-16 19:59:49', NULL, 'aktif', NULL, '2026-09-16 19:59:49', '2026-09-16 20:48:24', '109310542814273998539', 'pro', NULL, NULL),
(10, 'Wileo Fajar', 'wileolovers@gmail.com', '2026-09-16 20:28:22', NULL, 'aktif', NULL, '2026-09-16 20:28:22', '2026-09-16 20:28:22', '106763965194044668230', 'pro', NULL, NULL),
(11, 'Yabs', 'freeonlyfans323@gmail.com', '2026-09-17 02:56:39', '$2y$12$Ig8dB5btHhTU26aVBcyIxe5JPYK809GSXNbfzymlWiakgKYoJnEZW', 'aktif', NULL, '2026-09-17 02:52:11', '2026-09-17 02:56:39', NULL, 'pro', NULL, NULL),
(12, 'Figa Sri uning', 'figasriuning@gmail.com', '2026-09-17 04:06:12', NULL, 'aktif', NULL, '2026-09-17 04:06:12', '2026-09-17 04:06:12', '115646077131198270796', 'pro', NULL, NULL),
(13, 'industech', 'learnind10@gmail.com', '2026-09-19 03:49:28', NULL, 'aktif', NULL, '2026-09-19 03:49:28', '2026-09-19 03:50:07', '103768492835799958625', 'pro', NULL, NULL),
(14, 'Fauzi Rozikin', 'fauzirozikin869@gmail.com', '2026-09-20 07:24:07', NULL, 'aktif', NULL, '2026-09-20 07:24:07', '2026-09-20 07:24:07', '109596838774848680148', 'pro', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `akun_perkiraan`
--
ALTER TABLE `akun_perkiraan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `akun_perkiraan_user_id_kode_unique` (`user_id`,`kode`),
  ADD KEY `akun_perkiraan_parent_id_foreign` (`parent_id`),
  ADD KEY `akun_perkiraan_user_id_index` (`user_id`);

--
-- Indexes for table `asets`
--
ALTER TABLE `asets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asets_user_id_kode_unique` (`user_id`,`kode`),
  ADD KEY `asets_akun_aset_id_foreign` (`akun_aset_id`),
  ADD KEY `asets_akun_akumulasi_id_foreign` (`akun_akumulasi_id`),
  ADD KEY `asets_akun_beban_id_foreign` (`akun_beban_id`),
  ADD KEY `asets_sumber_dana_id_foreign` (`sumber_dana_id`),
  ADD KEY `asets_rekening_id_foreign` (`rekening_id`),
  ADD KEY `asets_supplier_id_foreign` (`supplier_id`),
  ADD KEY `asets_user_id_index` (`user_id`);

--
-- Indexes for table `asset_templates`
--
ALTER TABLE `asset_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asset_templates_user_id_nama_kategori_unique` (`user_id`,`nama_kategori`),
  ADD KEY `asset_templates_akun_aset_id_foreign` (`akun_aset_id`),
  ADD KEY `asset_templates_akun_akumulasi_id_foreign` (`akun_akumulasi_id`),
  ADD KEY `asset_templates_akun_beban_id_foreign` (`akun_beban_id`),
  ADD KEY `asset_templates_user_id_index` (`user_id`);

--
-- Indexes for table `audit_trails`
--
ALTER TABLE `audit_trails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_trails_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  ADD KEY `audit_trails_user_id_index` (`user_id`),
  ADD KEY `audit_trails_created_at_index` (`created_at`);

--
-- Indexes for table `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `banks_akun_id_foreign` (`akun_id`);

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `barang_user_id_kode_unique` (`user_id`,`kode`),
  ADD KEY `barang_gudang_id_foreign` (`gudang_id`),
  ADD KEY `barang_user_id_index` (`user_id`);

--
-- Indexes for table `bb_hutang`
--
ALTER TABLE `bb_hutang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bb_hutang_supplier_id_foreign` (`supplier_id`),
  ADD KEY `bb_hutang_jurnal_id_foreign` (`jurnal_id`),
  ADD KEY `bb_hutang_pembelian_id_foreign` (`pembelian_id`),
  ADD KEY `bb_hutang_user_id_index` (`user_id`);

--
-- Indexes for table `bb_persediaan`
--
ALTER TABLE `bb_persediaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bb_persediaan_ref_type_ref_id_index` (`ref_type`,`ref_id`),
  ADD KEY `bb_persediaan_barang_id_foreign` (`barang_id`),
  ADD KEY `bb_persediaan_user_id_index` (`user_id`);

--
-- Indexes for table `bb_piutang`
--
ALTER TABLE `bb_piutang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bb_piutang_customer_id_foreign` (`customer_id`),
  ADD KEY `bb_piutang_jurnal_id_foreign` (`jurnal_id`),
  ADD KEY `bb_piutang_penjualan_id_foreign` (`penjualan_id`),
  ADD KEY `bb_piutang_user_id_index` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_messages_moderated_by_foreign` (`moderated_by`),
  ADD KEY `chat_messages_room_id_id_index` (`room_id`,`id`),
  ADD KEY `chat_messages_sender_id_index` (`sender_id`);

--
-- Indexes for table `chat_reports`
--
ALTER TABLE `chat_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_reports_reporter_id_foreign` (`reporter_id`),
  ADD KEY `chat_reports_handled_by_foreign` (`handled_by`),
  ADD KEY `chat_reports_status_created_at_index` (`status`,`created_at`),
  ADD KEY `chat_reports_message_id_index` (`message_id`);

--
-- Indexes for table `chat_rooms`
--
ALTER TABLE `chat_rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chat_rooms_user_a_id_user_b_id_unique` (`user_a_id`,`user_b_id`),
  ADD KEY `chat_rooms_user_b_id_foreign` (`user_b_id`),
  ADD KEY `chat_rooms_tipe_index` (`tipe`);

--
-- Indexes for table `chat_room_reads`
--
ALTER TABLE `chat_room_reads`
  ADD PRIMARY KEY (`user_id`,`room_id`),
  ADD KEY `chat_room_reads_room_id_foreign` (`room_id`),
  ADD KEY `chat_room_reads_last_read_message_id_foreign` (`last_read_message_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_user_id_kode_unique` (`user_id`,`kode`),
  ADD KEY `customers_user_id_index` (`user_id`);

--
-- Indexes for table `daftar_harga`
--
ALTER TABLE `daftar_harga`
  ADD PRIMARY KEY (`id`),
  ADD KEY `daftar_harga_barang_id_foreign` (`barang_id`),
  ADD KEY `daftar_harga_supplier_id_foreign` (`supplier_id`),
  ADD KEY `daftar_harga_customer_id_foreign` (`customer_id`),
  ADD KEY `daftar_harga_entitas_supplier_id_customer_id_barang_id_index` (`entitas`,`supplier_id`,`customer_id`,`barang_id`),
  ADD KEY `daftar_harga_user_id_index` (`user_id`);

--
-- Indexes for table `daftar_harga_riwayat`
--
ALTER TABLE `daftar_harga_riwayat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `daftar_harga_riwayat_entitas_barang_id_index` (`entitas`,`barang_id`),
  ADD KEY `daftar_harga_riwayat_entitas_supplier_id_barang_id_index` (`entitas`,`supplier_id`,`barang_id`),
  ADD KEY `daftar_harga_riwayat_entitas_customer_id_barang_id_index` (`entitas`,`customer_id`,`barang_id`),
  ADD KEY `daftar_harga_riwayat_user_id_index` (`user_id`);

--
-- Indexes for table `donasis`
--
ALTER TABLE `donasis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donasis_user_id_index` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `gudangs`
--
ALTER TABLE `gudangs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gudangs_user_id_kode_unique` (`user_id`,`kode`),
  ADD KEY `gudangs_user_id_index` (`user_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jurnal_items`
--
ALTER TABLE `jurnal_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jurnal_items_jurnal_id_foreign` (`jurnal_id`),
  ADD KEY `jurnal_items_akun_id_foreign` (`akun_id`),
  ADD KEY `jurnal_items_user_id_index` (`user_id`);

--
-- Indexes for table `jurnal_umum`
--
ALTER TABLE `jurnal_umum`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jurnal_umum_user_id_nomor_unique` (`user_id`,`nomor`),
  ADD KEY `jurnal_umum_ref_type_ref_id_index` (`ref_type`,`ref_id`),
  ADD KEY `jurnal_umum_created_by_foreign` (`created_by`),
  ADD KEY `jurnal_umum_updated_by_foreign` (`updated_by`),
  ADD KEY `jurnal_umum_approved_by_foreign` (`approved_by`),
  ADD KEY `jurnal_umum_user_id_index` (`user_id`);

--
-- Indexes for table `kas`
--
ALTER TABLE `kas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kas_akun_id_foreign` (`akun_id`);

--
-- Indexes for table `kas_keluar`
--
ALTER TABLE `kas_keluar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kas_keluar_user_id_nomor_unique` (`user_id`,`nomor`),
  ADD KEY `kas_keluar_pajak_id_foreign` (`pajak_id`),
  ADD KEY `kas_keluar_rekening_id_foreign` (`rekening_id`),
  ADD KEY `kas_keluar_supplier_id_foreign` (`supplier_id`),
  ADD KEY `kas_keluar_created_by_foreign` (`created_by`),
  ADD KEY `kas_keluar_updated_by_foreign` (`updated_by`),
  ADD KEY `kas_keluar_approved_by_foreign` (`approved_by`),
  ADD KEY `kas_keluar_user_id_index` (`user_id`);

--
-- Indexes for table `kas_keluar_items`
--
ALTER TABLE `kas_keluar_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kas_keluar_items_kas_keluar_id_foreign` (`kas_keluar_id`),
  ADD KEY `kas_keluar_items_akun_id_foreign` (`akun_id`),
  ADD KEY `kas_keluar_items_user_id_index` (`user_id`);

--
-- Indexes for table `kas_masuk`
--
ALTER TABLE `kas_masuk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kas_masuk_user_id_nomor_unique` (`user_id`,`nomor`),
  ADD KEY `kas_masuk_pajak_id_foreign` (`pajak_id`),
  ADD KEY `kas_masuk_rekening_id_foreign` (`rekening_id`),
  ADD KEY `kas_masuk_customer_id_foreign` (`customer_id`),
  ADD KEY `kas_masuk_created_by_foreign` (`created_by`),
  ADD KEY `kas_masuk_updated_by_foreign` (`updated_by`),
  ADD KEY `kas_masuk_approved_by_foreign` (`approved_by`),
  ADD KEY `kas_masuk_user_id_index` (`user_id`);

--
-- Indexes for table `kas_masuk_items`
--
ALTER TABLE `kas_masuk_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kas_masuk_items_kas_masuk_id_foreign` (`kas_masuk_id`),
  ADD KEY `kas_masuk_items_akun_id_foreign` (`akun_id`),
  ADD KEY `kas_masuk_items_user_id_index` (`user_id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kategori_user_id_nama_unique` (`user_id`,`nama`),
  ADD KEY `kategori_user_id_index` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mutasi_bank`
--
ALTER TABLE `mutasi_bank`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mutasi_bank_user_id_nomor_unique` (`user_id`,`nomor`),
  ADD KEY `mutasi_bank_rekening_asal_id_foreign` (`rekening_asal_id`),
  ADD KEY `mutasi_bank_rekening_tujuan_id_foreign` (`rekening_tujuan_id`),
  ADD KEY `mutasi_bank_user_id_index` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `pajak`
--
ALTER TABLE `pajak`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pajak_user_id_index` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pembelians`
--
ALTER TABLE `pembelians`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pembelians_user_id_nomor_unique` (`user_id`,`nomor`),
  ADD KEY `pembelians_supplier_id_foreign` (`supplier_id`),
  ADD KEY `pembelians_pajak_id_foreign` (`pajak_id`),
  ADD KEY `pembelians_rekening_id_foreign` (`rekening_id`),
  ADD KEY `pembelians_created_by_foreign` (`created_by`),
  ADD KEY `pembelians_updated_by_foreign` (`updated_by`),
  ADD KEY `pembelians_approved_by_foreign` (`approved_by`),
  ADD KEY `pembelians_user_id_index` (`user_id`),
  ADD KEY `pembelians_gudang_id_foreign` (`gudang_id`);

--
-- Indexes for table `pembelian_items`
--
ALTER TABLE `pembelian_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembelian_items_pembelian_id_foreign` (`pembelian_id`),
  ADD KEY `pembelian_items_barang_id_foreign` (`barang_id`),
  ADD KEY `pembelian_items_user_id_index` (`user_id`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pengaturan_user_id_key_unique` (`user_id`,`key`),
  ADD KEY `pengaturan_user_id_index` (`user_id`);

--
-- Indexes for table `penjualans`
--
ALTER TABLE `penjualans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `penjualans_user_id_nomor_unique` (`user_id`,`nomor`),
  ADD KEY `penjualans_customer_id_foreign` (`customer_id`),
  ADD KEY `penjualans_pajak_id_foreign` (`pajak_id`),
  ADD KEY `penjualans_rekening_id_foreign` (`rekening_id`),
  ADD KEY `penjualans_created_by_foreign` (`created_by`),
  ADD KEY `penjualans_updated_by_foreign` (`updated_by`),
  ADD KEY `penjualans_approved_by_foreign` (`approved_by`),
  ADD KEY `penjualans_user_id_index` (`user_id`),
  ADD KEY `penjualans_gudang_id_foreign` (`gudang_id`);

--
-- Indexes for table `penjualan_items`
--
ALTER TABLE `penjualan_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penjualan_items_penjualan_id_foreign` (`penjualan_id`),
  ADD KEY `penjualan_items_barang_id_foreign` (`barang_id`),
  ADD KEY `penjualan_items_user_id_index` (`user_id`);

--
-- Indexes for table `penyusutan`
--
ALTER TABLE `penyusutan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `penyusutan_user_id_aset_id_periode_unique` (`user_id`,`aset_id`,`periode`),
  ADD KEY `penyusutan_jurnal_id_foreign` (`jurnal_id`),
  ADD KEY `penyusutan_user_id_index` (`user_id`),
  ADD KEY `penyusutan_aset_id_index` (`aset_id`);

--
-- Indexes for table `periode_akuntansi`
--
ALTER TABLE `periode_akuntansi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `periode_akuntansi_user_id_kode_unique` (`user_id`,`kode`),
  ADD UNIQUE KEY `periode_akuntansi_user_id_bulan_tahun_unique` (`user_id`,`bulan`,`tahun`),
  ADD KEY `periode_akuntansi_locked_by_foreign` (`locked_by`),
  ADD KEY `periode_akuntansi_user_id_index` (`user_id`);

--
-- Indexes for table `perubahan_stok`
--
ALTER TABLE `perubahan_stok`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `perubahan_stok_user_id_nomor_unique` (`user_id`,`nomor`),
  ADD KEY `perubahan_stok_created_by_foreign` (`created_by`),
  ADD KEY `perubahan_stok_updated_by_foreign` (`updated_by`),
  ADD KEY `perubahan_stok_approved_by_foreign` (`approved_by`),
  ADD KEY `perubahan_stok_user_id_index` (`user_id`);

--
-- Indexes for table `perubahan_stok_items`
--
ALTER TABLE `perubahan_stok_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `perubahan_stok_items_perubahan_stok_id_foreign` (`perubahan_stok_id`),
  ADD KEY `perubahan_stok_items_barang_id_foreign` (`barang_id`),
  ADD KEY `perubahan_stok_items_user_id_index` (`user_id`);

--
-- Indexes for table `rekenings`
--
ALTER TABLE `rekenings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rekenings_akun_id_foreign` (`akun_id`),
  ADD KEY `rekenings_user_id_index` (`user_id`);

--
-- Indexes for table `retur_pembelian`
--
ALTER TABLE `retur_pembelian`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `retur_pembelian_user_id_nomor_unique` (`user_id`,`nomor`),
  ADD KEY `retur_pembelian_pembelian_id_foreign` (`pembelian_id`),
  ADD KEY `retur_pembelian_created_by_foreign` (`created_by`),
  ADD KEY `retur_pembelian_updated_by_foreign` (`updated_by`),
  ADD KEY `retur_pembelian_approved_by_foreign` (`approved_by`),
  ADD KEY `retur_pembelian_user_id_index` (`user_id`);

--
-- Indexes for table `retur_pembelian_items`
--
ALTER TABLE `retur_pembelian_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `retur_pembelian_items_retur_pembelian_id_foreign` (`retur_pembelian_id`),
  ADD KEY `retur_pembelian_items_pembelian_item_id_foreign` (`pembelian_item_id`),
  ADD KEY `retur_pembelian_items_barang_id_foreign` (`barang_id`),
  ADD KEY `retur_pembelian_items_user_id_index` (`user_id`);

--
-- Indexes for table `retur_penjualan`
--
ALTER TABLE `retur_penjualan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `retur_penjualan_user_id_nomor_unique` (`user_id`,`nomor`),
  ADD KEY `retur_penjualan_penjualan_id_foreign` (`penjualan_id`),
  ADD KEY `retur_penjualan_created_by_foreign` (`created_by`),
  ADD KEY `retur_penjualan_updated_by_foreign` (`updated_by`),
  ADD KEY `retur_penjualan_approved_by_foreign` (`approved_by`),
  ADD KEY `retur_penjualan_user_id_index` (`user_id`);

--
-- Indexes for table `retur_penjualan_items`
--
ALTER TABLE `retur_penjualan_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `retur_penjualan_items_retur_penjualan_id_foreign` (`retur_penjualan_id`),
  ADD KEY `retur_penjualan_items_penjualan_item_id_foreign` (`penjualan_item_id`),
  ADD KEY `retur_penjualan_items_barang_id_foreign` (`barang_id`),
  ADD KEY `retur_penjualan_items_user_id_index` (`user_id`);

--
-- Indexes for table `satuan`
--
ALTER TABLE `satuan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `satuan_user_id_nama_unique` (`user_id`,`nama`),
  ADD KEY `satuan_user_id_index` (`user_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stok_gudang`
--
ALTER TABLE `stok_gudang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stok_gudang_user_id_barang_id_gudang_id_unique` (`user_id`,`barang_id`,`gudang_id`),
  ADD KEY `stok_gudang_user_id_index` (`user_id`),
  ADD KEY `stok_gudang_barang_id_index` (`barang_id`),
  ADD KEY `stok_gudang_gudang_id_index` (`gudang_id`);

--
-- Indexes for table `stok_opname`
--
ALTER TABLE `stok_opname`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stok_opname_user_id_nomor_unique` (`user_id`,`nomor`),
  ADD KEY `stok_opname_created_by_foreign` (`created_by`),
  ADD KEY `stok_opname_updated_by_foreign` (`updated_by`),
  ADD KEY `stok_opname_approved_by_foreign` (`approved_by`),
  ADD KEY `stok_opname_user_id_index` (`user_id`);

--
-- Indexes for table `stok_opname_items`
--
ALTER TABLE `stok_opname_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stok_opname_items_stok_opname_id_foreign` (`stok_opname_id`),
  ADD KEY `stok_opname_items_barang_id_foreign` (`barang_id`),
  ADD KEY `stok_opname_items_user_id_index` (`user_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_user_id_kode_unique` (`user_id`,`kode`),
  ADD KEY `suppliers_user_id_index` (`user_id`);

--
-- Indexes for table `transfer_gudang`
--
ALTER TABLE `transfer_gudang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transfer_gudang_user_id_nomor_unique` (`user_id`,`nomor`),
  ADD KEY `transfer_gudang_gudang_asal_foreign` (`gudang_asal`),
  ADD KEY `transfer_gudang_gudang_tujuan_foreign` (`gudang_tujuan`),
  ADD KEY `transfer_gudang_created_by_foreign` (`created_by`),
  ADD KEY `transfer_gudang_user_id_index` (`user_id`);

--
-- Indexes for table `transfer_gudang_items`
--
ALTER TABLE `transfer_gudang_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transfer_gudang_items_transfer_gudang_id_foreign` (`transfer_gudang_id`),
  ADD KEY `transfer_gudang_items_barang_id_foreign` (`barang_id`),
  ADD KEY `transfer_gudang_items_user_id_index` (`user_id`);

--
-- Indexes for table `tutup_buku`
--
ALTER TABLE `tutup_buku`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tutup_buku_periode_id_foreign` (`periode_id`),
  ADD KEY `tutup_buku_user_id_index` (`user_id`);

--
-- Indexes for table `tutup_buku_tahunan`
--
ALTER TABLE `tutup_buku_tahunan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tutup_buku_tahunan_user_id_tahun_unique` (`user_id`,`tahun`),
  ADD KEY `tutup_buku_tahunan_user_id_index` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_google_id_unique` (`google_id`),
  ADD KEY `users_status_index` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `akun_perkiraan`
--
ALTER TABLE `akun_perkiraan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=512;

--
-- AUTO_INCREMENT for table `asets`
--
ALTER TABLE `asets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_templates`
--
ALTER TABLE `asset_templates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `audit_trails`
--
ALTER TABLE `audit_trails`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `bb_hutang`
--
ALTER TABLE `bb_hutang`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bb_persediaan`
--
ALTER TABLE `bb_persediaan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `bb_piutang`
--
ALTER TABLE `bb_piutang`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `chat_reports`
--
ALTER TABLE `chat_reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_rooms`
--
ALTER TABLE `chat_rooms`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `daftar_harga`
--
ALTER TABLE `daftar_harga`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `daftar_harga_riwayat`
--
ALTER TABLE `daftar_harga_riwayat`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `donasis`
--
ALTER TABLE `donasis`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gudangs`
--
ALTER TABLE `gudangs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jurnal_items`
--
ALTER TABLE `jurnal_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `jurnal_umum`
--
ALTER TABLE `jurnal_umum`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `kas`
--
ALTER TABLE `kas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kas_keluar`
--
ALTER TABLE `kas_keluar`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kas_keluar_items`
--
ALTER TABLE `kas_keluar_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kas_masuk`
--
ALTER TABLE `kas_masuk`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kas_masuk_items`
--
ALTER TABLE `kas_masuk_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `mutasi_bank`
--
ALTER TABLE `mutasi_bank`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pajak`
--
ALTER TABLE `pajak`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `pembelians`
--
ALTER TABLE `pembelians`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pembelian_items`
--
ALTER TABLE `pembelian_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `penjualans`
--
ALTER TABLE `penjualans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `penjualan_items`
--
ALTER TABLE `penjualan_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `penyusutan`
--
ALTER TABLE `penyusutan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `periode_akuntansi`
--
ALTER TABLE `periode_akuntansi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT for table `perubahan_stok`
--
ALTER TABLE `perubahan_stok`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `perubahan_stok_items`
--
ALTER TABLE `perubahan_stok_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rekenings`
--
ALTER TABLE `rekenings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `retur_pembelian`
--
ALTER TABLE `retur_pembelian`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `retur_pembelian_items`
--
ALTER TABLE `retur_pembelian_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `retur_penjualan`
--
ALTER TABLE `retur_penjualan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `retur_penjualan_items`
--
ALTER TABLE `retur_penjualan_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `stok_gudang`
--
ALTER TABLE `stok_gudang`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `stok_opname`
--
ALTER TABLE `stok_opname`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stok_opname_items`
--
ALTER TABLE `stok_opname_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `transfer_gudang`
--
ALTER TABLE `transfer_gudang`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transfer_gudang_items`
--
ALTER TABLE `transfer_gudang_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tutup_buku`
--
ALTER TABLE `tutup_buku`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tutup_buku_tahunan`
--
ALTER TABLE `tutup_buku_tahunan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `akun_perkiraan`
--
ALTER TABLE `akun_perkiraan`
  ADD CONSTRAINT `akun_perkiraan_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `akun_perkiraan` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `asets`
--
ALTER TABLE `asets`
  ADD CONSTRAINT `asets_akun_akumulasi_id_foreign` FOREIGN KEY (`akun_akumulasi_id`) REFERENCES `akun_perkiraan` (`id`),
  ADD CONSTRAINT `asets_akun_aset_id_foreign` FOREIGN KEY (`akun_aset_id`) REFERENCES `akun_perkiraan` (`id`),
  ADD CONSTRAINT `asets_akun_beban_id_foreign` FOREIGN KEY (`akun_beban_id`) REFERENCES `akun_perkiraan` (`id`),
  ADD CONSTRAINT `asets_rekening_id_foreign` FOREIGN KEY (`rekening_id`) REFERENCES `rekenings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `asets_sumber_dana_id_foreign` FOREIGN KEY (`sumber_dana_id`) REFERENCES `akun_perkiraan` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `asets_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `asset_templates`
--
ALTER TABLE `asset_templates`
  ADD CONSTRAINT `asset_templates_akun_akumulasi_id_foreign` FOREIGN KEY (`akun_akumulasi_id`) REFERENCES `akun_perkiraan` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `asset_templates_akun_aset_id_foreign` FOREIGN KEY (`akun_aset_id`) REFERENCES `akun_perkiraan` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `asset_templates_akun_beban_id_foreign` FOREIGN KEY (`akun_beban_id`) REFERENCES `akun_perkiraan` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `audit_trails`
--
ALTER TABLE `audit_trails`
  ADD CONSTRAINT `audit_trails_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `banks`
--
ALTER TABLE `banks`
  ADD CONSTRAINT `banks_akun_id_foreign` FOREIGN KEY (`akun_id`) REFERENCES `akun_perkiraan` (`id`);

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_gudang_id_foreign` FOREIGN KEY (`gudang_id`) REFERENCES `gudangs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bb_hutang`
--
ALTER TABLE `bb_hutang`
  ADD CONSTRAINT `bb_hutang_jurnal_id_foreign` FOREIGN KEY (`jurnal_id`) REFERENCES `jurnal_umum` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bb_hutang_pembelian_id_foreign` FOREIGN KEY (`pembelian_id`) REFERENCES `pembelians` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bb_hutang_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `bb_persediaan`
--
ALTER TABLE `bb_persediaan`
  ADD CONSTRAINT `bb_persediaan_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bb_piutang`
--
ALTER TABLE `bb_piutang`
  ADD CONSTRAINT `bb_piutang_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `bb_piutang_jurnal_id_foreign` FOREIGN KEY (`jurnal_id`) REFERENCES `jurnal_umum` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bb_piutang_penjualan_id_foreign` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualans` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_moderated_by_foreign` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chat_messages_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `chat_rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `chat_reports`
--
ALTER TABLE `chat_reports`
  ADD CONSTRAINT `chat_reports_handled_by_foreign` FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chat_reports_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `chat_messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_reports_reporter_id_foreign` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_rooms`
--
ALTER TABLE `chat_rooms`
  ADD CONSTRAINT `chat_rooms_user_a_id_foreign` FOREIGN KEY (`user_a_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chat_rooms_user_b_id_foreign` FOREIGN KEY (`user_b_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `chat_room_reads`
--
ALTER TABLE `chat_room_reads`
  ADD CONSTRAINT `chat_room_reads_last_read_message_id_foreign` FOREIGN KEY (`last_read_message_id`) REFERENCES `chat_messages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chat_room_reads_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `chat_rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_room_reads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `daftar_harga`
--
ALTER TABLE `daftar_harga`
  ADD CONSTRAINT `daftar_harga_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `daftar_harga_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `daftar_harga_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `donasis`
--
ALTER TABLE `donasis`
  ADD CONSTRAINT `donasis_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jurnal_items`
--
ALTER TABLE `jurnal_items`
  ADD CONSTRAINT `jurnal_items_akun_id_foreign` FOREIGN KEY (`akun_id`) REFERENCES `akun_perkiraan` (`id`),
  ADD CONSTRAINT `jurnal_items_jurnal_id_foreign` FOREIGN KEY (`jurnal_id`) REFERENCES `jurnal_umum` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jurnal_umum`
--
ALTER TABLE `jurnal_umum`
  ADD CONSTRAINT `jurnal_umum_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `jurnal_umum_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `jurnal_umum_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kas`
--
ALTER TABLE `kas`
  ADD CONSTRAINT `kas_akun_id_foreign` FOREIGN KEY (`akun_id`) REFERENCES `akun_perkiraan` (`id`);

--
-- Constraints for table `kas_keluar`
--
ALTER TABLE `kas_keluar`
  ADD CONSTRAINT `kas_keluar_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kas_keluar_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kas_keluar_pajak_id_foreign` FOREIGN KEY (`pajak_id`) REFERENCES `pajak` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kas_keluar_rekening_id_foreign` FOREIGN KEY (`rekening_id`) REFERENCES `rekenings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kas_keluar_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kas_keluar_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kas_keluar_items`
--
ALTER TABLE `kas_keluar_items`
  ADD CONSTRAINT `kas_keluar_items_akun_id_foreign` FOREIGN KEY (`akun_id`) REFERENCES `akun_perkiraan` (`id`),
  ADD CONSTRAINT `kas_keluar_items_kas_keluar_id_foreign` FOREIGN KEY (`kas_keluar_id`) REFERENCES `kas_keluar` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kas_masuk`
--
ALTER TABLE `kas_masuk`
  ADD CONSTRAINT `kas_masuk_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kas_masuk_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kas_masuk_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kas_masuk_pajak_id_foreign` FOREIGN KEY (`pajak_id`) REFERENCES `pajak` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kas_masuk_rekening_id_foreign` FOREIGN KEY (`rekening_id`) REFERENCES `rekenings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kas_masuk_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kas_masuk_items`
--
ALTER TABLE `kas_masuk_items`
  ADD CONSTRAINT `kas_masuk_items_akun_id_foreign` FOREIGN KEY (`akun_id`) REFERENCES `akun_perkiraan` (`id`),
  ADD CONSTRAINT `kas_masuk_items_kas_masuk_id_foreign` FOREIGN KEY (`kas_masuk_id`) REFERENCES `kas_masuk` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mutasi_bank`
--
ALTER TABLE `mutasi_bank`
  ADD CONSTRAINT `mutasi_bank_rekening_asal_id_foreign` FOREIGN KEY (`rekening_asal_id`) REFERENCES `rekenings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `mutasi_bank_rekening_tujuan_id_foreign` FOREIGN KEY (`rekening_tujuan_id`) REFERENCES `rekenings` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pembelians`
--
ALTER TABLE `pembelians`
  ADD CONSTRAINT `pembelians_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pembelians_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pembelians_gudang_id_foreign` FOREIGN KEY (`gudang_id`) REFERENCES `gudangs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pembelians_pajak_id_foreign` FOREIGN KEY (`pajak_id`) REFERENCES `pajak` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pembelians_rekening_id_foreign` FOREIGN KEY (`rekening_id`) REFERENCES `rekenings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pembelians_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `pembelians_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pembelian_items`
--
ALTER TABLE `pembelian_items`
  ADD CONSTRAINT `pembelian_items_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pembelian_items_pembelian_id_foreign` FOREIGN KEY (`pembelian_id`) REFERENCES `pembelians` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penjualans`
--
ALTER TABLE `penjualans`
  ADD CONSTRAINT `penjualans_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `penjualans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `penjualans_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `penjualans_gudang_id_foreign` FOREIGN KEY (`gudang_id`) REFERENCES `gudangs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `penjualans_pajak_id_foreign` FOREIGN KEY (`pajak_id`) REFERENCES `pajak` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `penjualans_rekening_id_foreign` FOREIGN KEY (`rekening_id`) REFERENCES `rekenings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `penjualans_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `penjualan_items`
--
ALTER TABLE `penjualan_items`
  ADD CONSTRAINT `penjualan_items_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `penjualan_items_penjualan_id_foreign` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penyusutan`
--
ALTER TABLE `penyusutan`
  ADD CONSTRAINT `penyusutan_aset_id_foreign` FOREIGN KEY (`aset_id`) REFERENCES `asets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penyusutan_jurnal_id_foreign` FOREIGN KEY (`jurnal_id`) REFERENCES `jurnal_umum` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `periode_akuntansi`
--
ALTER TABLE `periode_akuntansi`
  ADD CONSTRAINT `periode_akuntansi_locked_by_foreign` FOREIGN KEY (`locked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `perubahan_stok`
--
ALTER TABLE `perubahan_stok`
  ADD CONSTRAINT `perubahan_stok_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `perubahan_stok_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `perubahan_stok_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `perubahan_stok_items`
--
ALTER TABLE `perubahan_stok_items`
  ADD CONSTRAINT `perubahan_stok_items_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `perubahan_stok_items_perubahan_stok_id_foreign` FOREIGN KEY (`perubahan_stok_id`) REFERENCES `perubahan_stok` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rekenings`
--
ALTER TABLE `rekenings`
  ADD CONSTRAINT `rekenings_akun_id_foreign` FOREIGN KEY (`akun_id`) REFERENCES `akun_perkiraan` (`id`);

--
-- Constraints for table `retur_pembelian`
--
ALTER TABLE `retur_pembelian`
  ADD CONSTRAINT `retur_pembelian_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `retur_pembelian_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `retur_pembelian_pembelian_id_foreign` FOREIGN KEY (`pembelian_id`) REFERENCES `pembelians` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `retur_pembelian_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `retur_pembelian_items`
--
ALTER TABLE `retur_pembelian_items`
  ADD CONSTRAINT `retur_pembelian_items_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `retur_pembelian_items_pembelian_item_id_foreign` FOREIGN KEY (`pembelian_item_id`) REFERENCES `pembelian_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `retur_pembelian_items_retur_pembelian_id_foreign` FOREIGN KEY (`retur_pembelian_id`) REFERENCES `retur_pembelian` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `retur_penjualan`
--
ALTER TABLE `retur_penjualan`
  ADD CONSTRAINT `retur_penjualan_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `retur_penjualan_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `retur_penjualan_penjualan_id_foreign` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `retur_penjualan_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `retur_penjualan_items`
--
ALTER TABLE `retur_penjualan_items`
  ADD CONSTRAINT `retur_penjualan_items_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `retur_penjualan_items_penjualan_item_id_foreign` FOREIGN KEY (`penjualan_item_id`) REFERENCES `penjualan_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `retur_penjualan_items_retur_penjualan_id_foreign` FOREIGN KEY (`retur_penjualan_id`) REFERENCES `retur_penjualan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stok_gudang`
--
ALTER TABLE `stok_gudang`
  ADD CONSTRAINT `stok_gudang_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stok_gudang_gudang_id_foreign` FOREIGN KEY (`gudang_id`) REFERENCES `gudangs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stok_opname`
--
ALTER TABLE `stok_opname`
  ADD CONSTRAINT `stok_opname_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stok_opname_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stok_opname_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stok_opname_items`
--
ALTER TABLE `stok_opname_items`
  ADD CONSTRAINT `stok_opname_items_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stok_opname_items_stok_opname_id_foreign` FOREIGN KEY (`stok_opname_id`) REFERENCES `stok_opname` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transfer_gudang`
--
ALTER TABLE `transfer_gudang`
  ADD CONSTRAINT `transfer_gudang_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transfer_gudang_gudang_asal_foreign` FOREIGN KEY (`gudang_asal`) REFERENCES `gudangs` (`id`),
  ADD CONSTRAINT `transfer_gudang_gudang_tujuan_foreign` FOREIGN KEY (`gudang_tujuan`) REFERENCES `gudangs` (`id`);

--
-- Constraints for table `transfer_gudang_items`
--
ALTER TABLE `transfer_gudang_items`
  ADD CONSTRAINT `transfer_gudang_items_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transfer_gudang_items_transfer_gudang_id_foreign` FOREIGN KEY (`transfer_gudang_id`) REFERENCES `transfer_gudang` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tutup_buku`
--
ALTER TABLE `tutup_buku`
  ADD CONSTRAINT `tutup_buku_periode_id_foreign` FOREIGN KEY (`periode_id`) REFERENCES `periode_akuntansi` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
