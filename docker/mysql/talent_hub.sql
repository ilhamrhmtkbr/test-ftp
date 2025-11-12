CREATE DATABASE IF NOT EXISTS talent_hub;

USE talent_hub;

-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 02 Jan 2025 pada 06.59
-- Versi server: 10.4.20-MariaDB
-- Versi PHP: 8.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `talent_hub_test`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `job_id` int(11) NOT NULL,
  `status` enum('applied','interviewed','hired','rejected') DEFAULT 'applied',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `candidates`
--

INSERT INTO `candidates` (`id`, `user_id`, `job_id`, `status`, `created_at`) VALUES
(1, 'ilhamrhmtkbr@gmail.com', 1, 'hired', '2024-12-14 06:21:34'),
(2, 'budi.santoso@example.com', 2, 'interviewed', '2024-12-03 20:49:16'),
(4, 'dian.purnama@example.com', 4, 'applied', '2024-12-03 20:49:16'),
(5, 'eka.maulana@example.com', 5, 'interviewed', '2024-12-03 20:49:16'),
(6, 'fajar.ramadhan@example.com', 6, 'applied', '2024-12-03 20:49:16'),
(7, 'gita.anggraini@example.com', 7, 'applied', '2024-12-03 20:49:16'),
(8, 'hendra.setiawan@example.com', 8, 'applied', '2024-12-03 20:49:16'),
(9, 'indah.sari@example.com', 9, 'applied', '2024-12-03 20:49:16'),
(10, 'joko.widodo@example.com', 10, 'applied', '2024-12-03 20:49:16'),
(229, 'budi@gmail.com', 2, 'applied', '2024-12-29 12:09:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `company_employee_projects`
--

CREATE TABLE `company_employee_projects` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('ongoing','completed','on-hold') DEFAULT 'ongoing',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `company_employee_projects`
--

INSERT INTO `company_employee_projects` (`id`, `name`, `description`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Proyek Aplikasi Web', 'Proyek pengembangan aplikasi berbasis web untuk klien XYZ', '2023-01-01', '2023-12-31', 'ongoing', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(2, 'Proyek Pengembangan Produk', 'Pengembangan produk baru untuk pasar internasional', '2023-03-15', '2024-06-30', 'ongoing', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(3, 'Proyek Desain Grafis', 'Pembuatan materi pemasaran untuk kampanye terbaru', '2023-04-01', '2023-11-30', 'ongoing', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(4, 'Proyek Pembangunan Infrastruktur', 'Pembangunan jembatan untuk kota A', '2022-06-01', '2023-12-31', 'ongoing', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(5, 'Proyek Pengembangan Aplikasi Mobile', 'Pengembangan aplikasi mobile untuk transaksi e-commerce', '2023-02-01', '2023-09-30', 'ongoing', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(6, 'Proyek Penelitian Pasar', 'Penelitian dan analisis tren pasar untuk produk baru', '2023-05-01', '2024-05-01', 'ongoing', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(7, 'Proyek Pemasaran Digital', 'Peluncuran kampanye pemasaran digital untuk produk terbaru', '2023-06-01', '2023-12-01', 'ongoing', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(8, 'Proyek Pengelolaan Keuangan', 'Pengelolaan keuangan perusahaan selama tahun fiskal 2023', '2023-01-01', '2023-12-31', 'ongoing', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(9, 'Proyek Implementasi Sistem', 'Implementasi sistem ERP untuk perusahaan ABC', '2023-07-01', '2024-07-01', 'ongoing', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(10, 'Proyek Jaringan & Infrastruktur IT', 'Pembangunan infrastruktur jaringan untuk perusahaan besar', '2023-03-01', '2024-03-01', 'ongoing', '2024-12-03 20:49:16', '2024-12-03 20:49:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `company_employee_roles`
--

CREATE TABLE `company_employee_roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `company_employee_roles`
--

INSERT INTO `company_employee_roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'CEO', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(2, 'CTO', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(3, 'CFO', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(4, 'HRD', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(5, 'Manajer Pemasaran', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(6, 'Manajer Proyek', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(7, 'Sales Manager', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(8, 'Legal Counsel', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(9, 'Manajer Logistik', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(10, 'Desainer UI/UX', '2024-12-03 20:49:16', '2024-12-03 20:49:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `company_office_departments`
--

CREATE TABLE `company_office_departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `company_office_departments`
--

INSERT INTO `company_office_departments` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'IT', 'Mengelola infrastruktur teknologi informasi perusahaan', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(2, 'Keuangan', 'Bertanggung jawab atas pengelolaan keuangan dan laporan pajak', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(3, 'SDM', 'Mengelola sumber daya manusia dan kesejahteraan karyawan', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(4, 'Pemasaran', 'Menangani strategi pemasaran dan pengembangan produk', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(5, 'Operasional', 'Mengelola operasi sehari-hari perusahaan', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(6, 'Penjualan', 'Menangani proses penjualan dan hubungan dengan klien', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(7, 'Legal', 'Memberikan nasihat hukum dan menangani permasalahan hukum', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(8, 'Logistik', 'Mengelola pengiriman barang dan distribusi', '2024-12-03 20:49:16', '2024-12-29 07:08:19'),
(9, 'Riset & Pengembangan', 'Mengembangkan produk baru dan penelitian pasar', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(10, 'Desain', 'Mendesain produk dan materi pemasaran', '2024-12-03 20:49:16', '2024-12-03 20:49:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `company_office_financial_transactions`
--

CREATE TABLE `company_office_financial_transactions` (
  `id` int(11) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `company_office_financial_transactions`
--

INSERT INTO `company_office_financial_transactions` (`id`, `type`, `amount`, `transaction_date`, `description`, `created_at`, `updated_at`) VALUES
(1, 'income', '400000.00', '2025-01-25', 'Pendapatan Hasil Usaha', '2024-12-29 07:54:27', '2024-12-29 07:54:39'),
(2, 'expense', '500000.00', '2024-12-02', 'Pembayaran gaji karyawan', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
(3, 'income', '500000.00', '2025-01-25', 'Pendapatan Hasil Usaha', '2024-12-29 07:54:27', '2024-12-29 07:55:09'),
(4, 'expense', '250000.00', '2024-12-04', 'Pembelian material konstruksi', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
(5, 'income', '300000.00', '2025-01-25', 'Pendapatan Hasil Usaha', '2024-12-29 07:54:27', '2024-12-29 07:55:18'),
(6, 'expense', '800000.00', '2024-12-06', 'Pembayaran pajak tahunan', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
(7, 'income', '900000.00', '2025-01-25', 'Pendapatan Hasil Usaha', '2024-12-29 07:54:27', '2024-12-29 07:55:21'),
(8, 'expense', '400000.00', '2024-12-08', 'Pembayaran utilitas kantor', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
(9, 'expense', '600000.00', '2024-12-10', 'Pembayaran biaya operasional', '2024-12-03 20:49:15', '2024-12-29 07:55:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `company_office_recruitments`
--

CREATE TABLE `company_office_recruitments` (
  `id` int(11) NOT NULL,
  `job_title` varchar(100) NOT NULL,
  `department_id` int(11) NOT NULL,
  `job_description` text DEFAULT NULL,
  `status` enum('open','closed') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `company_office_recruitments`
--

INSERT INTO `company_office_recruitments` (`id`, `job_title`, `department_id`, `job_description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Software Engineer', 1, 'Bertanggung jawab untuk mengembangkan aplikasi berbasis web dan mobile.', 'open', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(2, 'Akuntan', 2, 'Mengelola laporan keuangan perusahaan dan memastikan kepatuhan pajak.', 'open', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(3, 'HR Manager', 3, 'Mengelola rekrutmen, pelatihan, dan kesejahteraan karyawan.', 'open', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(4, 'Digital Marketing Specialist', 4, 'Merancang dan melaksanakan kampanye pemasaran digital.', 'open', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(5, 'Project Manager', 5, 'Mengelola proyek perusahaan dari perencanaan hingga penyelesaian.', 'open', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(6, 'Sales Executive', 6, 'Menjual produk dan layanan perusahaan ke klien dan pelanggan potensial.', 'open', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(7, 'Legal Advisor', 7, 'Memberikan nasihat hukum dan menangani sengketa hukum perusahaan.', 'open', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(8, 'Logistics Manager', 8, 'Mengelola distribusi barang dan memastikan pengiriman tepat waktu.', 'open', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(9, 'R&D Specialist', 9, 'Melakukan penelitian dan pengembangan produk baru.', 'open', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
(10, 'UI/UX Designer', 10, 'Merancang antarmuka pengguna dan pengalaman pengguna aplikasi perusahaan.', 'open', '2024-12-03 20:49:16', '2024-12-03 20:49:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employees`
--

CREATE TABLE `employees` (
  `user_id` varchar(100) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `salary` int(11) DEFAULT 0,
  `hire_date` varchar(40) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `employees`
--

INSERT INTO `employees` (`user_id`, `role_id`, `department_id`, `salary`, `hire_date`, `status`, `created_at`, `updated_at`) VALUES
('arif@contoh.com', 6, 6, 12500000, '2021-08-10', 'active', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
('dina@contoh.com', 9, 9, 14500000, '2022-10-05', 'active', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
('ilhamrhmtkbr@gmail.com', 1, 1, 25000000, '2024-12-17', 'active', '2024-12-14 06:22:15', '2024-12-14 06:22:15'),
('joni@contoh.com', 5, 4, 14000000, '2023-02-05', 'active', '2024-12-03 20:49:16', '2024-12-11 22:40:07'),
('nina@contoh.com', 7, 7, 13500000, '2022-04-12', 'active', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
('rani@contoh.com', 4, 4, 11000000, '2022-09-20', 'active', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
('siti@contoh.com', 2, 2, 12000000, '2021-05-10', 'active', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
('wawan@contoh.com', 10, 10, 12000000, '2021-11-15', 'active', '2024-12-03 20:49:16', '2024-12-03 20:49:16'),
('yudi@contoh.com', 8, 8, 15000000, '2023-01-15', 'active', '2024-12-03 20:49:16', '2024-12-03 20:49:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_attendance`
--

CREATE TABLE `employee_attendance` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `attendance_date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `status` enum('Present','Late','Absent','On Leave') NOT NULL,
  `late_penalty` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `employee_attendance`
--

INSERT INTO `employee_attendance` (`id`, `employee_id`, `attendance_date`, `check_in_time`, `check_out_time`, `status`, `late_penalty`, `created_at`, `updated_at`) VALUES
(1, 'arif@contoh.com', '2024-12-10', '08:05:00', '16:30:00', 'Present', NULL, '2024-12-16 21:09:07', '2024-12-16 21:09:07'),
(3, 'dina@contoh.com', '2024-12-10', '08:00:00', '16:45:00', 'Present', NULL, '2024-12-16 21:09:07', '2024-12-16 21:09:07'),
(4, 'ilhamrhmtkbr@gmail.com', '2024-12-10', '08:30:00', '17:00:00', 'Late', NULL, '2024-12-16 21:09:07', '2024-12-16 21:09:07'),
(5, 'joni@contoh.com', '2024-12-10', NULL, NULL, 'Absent', NULL, '2024-12-16 21:09:07', '2024-12-16 21:09:07'),
(6, 'nina@contoh.com', '2024-12-10', '08:15:00', '16:35:00', 'Present', NULL, '2024-12-16 21:09:07', '2024-12-16 21:09:07'),
(7, 'rani@contoh.com', '2024-12-10', '08:05:00', '16:30:00', 'Present', NULL, '2024-12-16 21:09:07', '2024-12-16 21:09:07'),
(8, 'siti@contoh.com', '2024-12-10', '08:00:00', '16:45:00', 'Present', NULL, '2024-12-16 21:09:07', '2024-12-16 21:09:07'),
(9, 'wawan@contoh.com', '2024-12-10', '08:35:00', '17:05:00', 'Late', NULL, '2024-12-16 21:09:07', '2024-12-16 21:09:07'),
(10, 'yudi@contoh.com', '2024-12-10', '08:10:00', '16:40:00', 'Present', NULL, '2024-12-16 21:09:07', '2024-12-16 21:09:07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_attendance_rules`
--

CREATE TABLE `employee_attendance_rules` (
  `id` int(11) NOT NULL,
  `rule_name` varchar(50) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `late_threshold` time NOT NULL,
  `penalty_for_late` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `employee_attendance_rules`
--

INSERT INTO `employee_attendance_rules` (`id`, `rule_name`, `start_time`, `end_time`, `late_threshold`, `penalty_for_late`, `created_at`, `updated_at`) VALUES
(1, 'Shift', '08:00:00', '17:00:00', '00:15:00', '5000.00', '2024-12-17 01:49:42', '2024-12-22 03:09:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_contracts`
--

CREATE TABLE `employee_contracts` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `contract_start_date` date NOT NULL,
  `contract_end_date` date DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `contract_terms` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `employee_contracts`
--

INSERT INTO `employee_contracts` (`id`, `employee_id`, `contract_start_date`, `contract_end_date`, `salary`, `contract_terms`, `created_at`, `updated_at`) VALUES
(1, 'arif@contoh.com', '2021-08-10', '2024-08-09', '12500000.00', 'Full-Time', '2024-12-16 22:21:26', '2024-12-16 22:21:26'),
(3, 'dina@contoh.com', '2022-10-05', '2025-10-04', '14500000.00', 'Part-Time', '2024-12-16 22:21:26', '2024-12-16 22:21:26'),
(4, 'ilhamrhmtkbr@gmail.com', '2024-12-17', '2027-12-16', '25000000.00', 'Full-Time', '2024-12-16 22:21:26', '2024-12-16 22:21:26'),
(5, 'joni@contoh.com', '2023-02-05', '2026-02-04', '14000000.00', 'Full-Time', '2024-12-16 22:21:26', '2024-12-16 22:21:26'),
(6, 'nina@contoh.com', '2022-04-12', '2025-04-11', '13500000.00', 'Contract-Based', '2024-12-16 22:21:26', '2024-12-16 22:21:26'),
(7, 'rani@contoh.com', '2022-09-20', '2025-09-19', '11000000.00', 'Full-Time', '2024-12-16 22:21:26', '2024-12-16 22:21:26'),
(8, 'siti@contoh.com', '2021-05-10', '2024-05-09', '12000000.00', 'Part-Time', '2024-12-16 22:21:26', '2024-12-16 22:21:26'),
(9, 'wawan@contoh.com', '2021-11-15', '2024-11-14', '12000000.00', 'Contract-Based', '2024-12-16 22:21:26', '2024-12-16 22:21:26'),
(10, 'yudi@contoh.com', '2023-01-15', '2026-01-14', '15000000.00', 'Full-Time', '2024-12-16 22:21:26', '2024-12-16 22:21:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_leave_requests`
--

CREATE TABLE `employee_leave_requests` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `leave_type` enum('Sick','Vacation','Personal','Unpaid') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `employee_leave_requests`
--

INSERT INTO `employee_leave_requests` (`id`, `employee_id`, `leave_type`, `start_date`, `end_date`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 'arif@contoh.com', 'Sick', '2024-11-15', '2024-11-17', 'Pending', 'Family Emergency', '2024-12-16 22:30:00', '2024-12-16 22:30:00'),
(3, 'dina@contoh.com', 'Sick', '2024-12-05', '2024-12-07', 'Pending', 'Vacation', '2024-12-16 22:30:00', '2024-12-16 22:30:00'),
(4, 'joni@contoh.com', 'Sick', '2024-10-01', '2024-10-03', 'Approved', 'Medical Leave', '2024-12-16 22:30:00', '2024-12-16 22:30:00'),
(5, 'nina@contoh.com', 'Vacation', '2024-11-10', '2024-11-12', 'Approved', 'Family Emergency', '2024-12-16 22:30:00', '2024-12-16 22:30:00'),
(6, 'rani@contoh.com', 'Personal', '2024-09-15', '2024-09-16', 'Approved', 'Personal Reason', '2024-12-16 22:30:00', '2024-12-16 22:30:00'),
(7, 'siti@contoh.com', 'Unpaid', '2024-07-20', '2024-07-25', 'Rejected', 'Vacation', '2024-12-16 22:30:00', '2024-12-16 22:30:00'),
(8, 'wawan@contoh.com', 'Unpaid', '2024-06-10', '2024-06-12', 'Rejected', 'Medical Leave', '2024-12-16 22:30:00', '2024-12-16 22:30:00'),
(9, 'yudi@contoh.com', 'Personal', '2024-08-01', '2024-08-03', 'Rejected', 'Vacation', '2024-12-16 22:30:00', '2024-12-16 22:30:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_overtime`
--

CREATE TABLE `employee_overtime` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `overtime_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `total_hours` decimal(5,2) GENERATED ALWAYS AS (timestampdiff(MINUTE,`start_time`,`end_time`) / 60) STORED,
  `overtime_rate` decimal(10,2) NOT NULL,
  `total_payment` decimal(10,2) GENERATED ALWAYS AS (`total_hours` * `overtime_rate`) STORED,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `employee_overtime`
--

INSERT INTO `employee_overtime` (`id`, `employee_id`, `overtime_date`, `start_time`, `end_time`, `overtime_rate`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 'arif@contoh.com', '2024-12-08', '17:00:00', '19:00:00', '40000.00', 'Project Deadline', '2024-12-17 00:36:47', '2024-12-17 00:54:48'),
(3, 'dina@contoh.com', '2024-12-08', '17:00:00', '21:00:00', '60000.00', 'System Maintenance', '2024-12-17 00:36:47', '2024-12-17 00:54:55'),
(4, 'ilhamrhmtkbr@gmail.com', '2024-12-08', '17:00:00', '21:00:00', '30000.00', 'Client Presentation', '2024-12-17 00:36:47', '2024-12-17 00:54:57'),
(5, 'joni@contoh.com', '2024-12-08', '17:00:00', '21:00:00', '70000.00', 'Report Preparation', '2024-12-17 00:36:47', '2024-12-17 00:55:01'),
(6, 'nina@contoh.com', '2024-12-08', '17:00:00', '21:00:00', '40000.00', 'Project Deadline', '2024-12-17 00:36:47', '2024-12-17 00:55:04'),
(7, 'rani@contoh.com', '2024-12-08', '17:00:00', '19:00:00', '30000.00', 'Documentation', '2024-12-17 00:36:47', '2024-12-17 00:55:08'),
(8, 'siti@contoh.com', '2024-12-08', '17:00:00', '19:00:00', '40000.00', 'Meeting Preparation', '2024-12-17 00:36:47', '2024-12-17 00:55:11'),
(9, 'wawan@contoh.com', '2024-12-08', '17:00:00', '19:00:00', '60000.00', 'System Upgrade', '2024-12-17 00:36:47', '2024-12-17 00:55:14'),
(10, 'yudi@contoh.com', '2024-12-08', '17:00:00', '19:00:00', '70000.00', 'Data Analysis', '2024-12-17 00:36:47', '2024-12-17 00:55:17'),
(21, 'yudi@contoh.com', '2024-12-11', '06:07:00', '07:05:00', '999.00', 'Vacation 123', '2024-12-18 23:05:24', '2024-12-18 23:12:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_payrolls`
--

CREATE TABLE `employee_payrolls` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `payroll_month` date NOT NULL,
  `base_salary` decimal(10,2) NOT NULL,
  `total_overtime` decimal(10,2) DEFAULT 0.00,
  `late_penalties` decimal(10,2) DEFAULT 0.00,
  `net_salary` decimal(10,2) GENERATED ALWAYS AS (`base_salary` + `total_overtime` - `late_penalties`) STORED,
  `status` enum('Pending','Paid') DEFAULT 'Pending',
  `payment_date` date DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `employee_payrolls`
--

INSERT INTO `employee_payrolls` (`id`, `employee_id`, `payroll_month`, `base_salary`, `total_overtime`, `late_penalties`, `status`, `payment_date`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 'arif@contoh.com', '2024-08-01', '12500000.00', '300000.00', '0.00', 'Pending', '2024-12-31', 'Akan dibayar', '2024-12-17 00:50:22', '2024-12-24 03:58:44'),
(3, 'dina@contoh.com', '2024-08-01', '14500000.00', '400000.00', '0.00', 'Pending', '2024-12-31', 'Akan dibayar', '2024-12-17 00:50:22', '2024-12-24 03:58:22'),
(4, 'ilhamrhmtkbr@gmail.com', '2024-08-01', '25000000.00', '500000.00', '100000.00', 'Pending', '2024-12-31', 'Akan dibayar', '2024-12-17 00:50:22', '2024-12-24 03:58:22'),
(5, 'joni@contoh.com', '2024-08-01', '14000000.00', '300000.00', '0.00', 'Pending', '2024-12-31', 'Akan dibayar', '2024-12-17 00:50:22', '2024-12-24 03:58:22'),
(6, 'nina@contoh.com', '2024-08-01', '13500000.00', '200000.00', '0.00', 'Pending', '2024-12-31', 'Akan dibayar', '2024-12-17 00:50:22', '2024-12-24 03:58:22'),
(7, 'rani@contoh.com', '2024-08-01', '11000000.00', '100000.00', '0.00', 'Pending', '2024-12-31', 'Akan dibayar', '2024-12-17 00:50:22', '2024-12-24 03:58:22'),
(8, 'siti@contoh.com', '2024-08-01', '12000000.00', '200000.00', '0.00', 'Pending', '2024-12-31', 'Akan dibayar', '2024-12-17 00:50:22', '2024-12-24 03:58:22'),
(9, 'wawan@contoh.com', '2024-08-01', '12000000.00', '400000.00', '100000.00', 'Pending', '2024-12-31', 'Akan dibayar', '2024-12-17 00:50:22', '2024-12-24 03:58:22'),
(10, 'yudi@contoh.com', '2024-08-01', '15000000.00', '300000.00', '0.00', 'Pending', '2024-12-31', 'Akan dibayar', '2024-12-17 00:50:22', '2024-12-24 03:58:35'),
(12, 'ilhamrhmtkbr@gmail.com', '2024-09-01', '1200000.00', '0.00', '0.00', 'Paid', '2024-12-25', 'Vacation 321', '2024-12-24 05:11:49', '2024-12-24 05:17:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_project_assignments`
--

CREATE TABLE `employee_project_assignments` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(100) NOT NULL,
  `project_id` int(11) NOT NULL,
  `role_in_project` varchar(100) NOT NULL,
  `assigned_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `employee_project_assignments`
--

INSERT INTO `employee_project_assignments` (`id`, `employee_id`, `project_id`, `role_in_project`, `assigned_date`) VALUES
(2, 'siti@contoh.com', 2, 'Project Manager', '2024-12-04'),
(4, 'rani@contoh.com', 4, 'Civil Engineer', '2024-12-04'),
(5, 'joni@contoh.com', 5, 'Mobile Developer', '2024-12-04'),
(6, 'arif@contoh.com', 6, 'Market Research Analyst', '2024-12-04'),
(7, 'nina@contoh.com', 7, 'Digital Marketing Specialist', '2024-12-04'),
(8, 'yudi@contoh.com', 8, 'Financial Analyst', '2024-12-04'),
(9, 'dina@contoh.com', 9, 'ERP Implementation Specialist', '2024-12-04'),
(10, 'wawan@contoh.com', 10, 'IT Network Engineer', '2024-12-04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `email` varchar(100) NOT NULL,
  `name` varchar(255) DEFAULT 'User',
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`email`, `name`, `password`, `created_at`, `updated_at`) VALUES
('andi.saputra@example.com', 'Andi Saputra', 'password123', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
('arif@contoh.com', 'Arif Wibowo', 'password303', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
('budi.santoso@example.com', 'Budi Santoso', '$2y$10$qnSrUpVkl//SG2K34Ld3CORzYC1BIqdcP3MZUyw099f/CKBKq2.j2', '2024-12-03 20:49:15', '2024-12-23 01:18:05'),
('budi@gmail.com', 'Budi Santoso', '$2y$10$eUzl7aHcfEWDvAjlZfUDj.BqtojbxTkcNrdAEF0rddthDx8Ecvu2y', '2024-12-03 20:49:15', '2024-12-19 15:05:33'),
('citra.lestari@example.com', 'Citra Lestari', 'mypassword789', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
('dian.purnama@example.com', 'Dian Purnama', 'adminpass123', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
('dina@contoh.com', 'Dina Sari', 'password606', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
('eka.maulana@example.com', 'Eka Maulana', 'ekapass456', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
('fajar.ramadhan@example.com', 'Fajar Ramadhan', 'ramadhan789', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
('gita.anggraini@example.com', 'Gita Anggraini', 'gitasecret123', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
('hendra.setiawan@example.com', 'Hendra Setiawan', 'hendrapass456', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
('ilhamrhmtkbr@gmail.com', 'Ilham Rahmat Akbar', '$2y$10$YwFiKkMzRD9j1EcabfXd6.IlfVAieqPim2wdggjY.3Xx5MIuOmtxa', '2024-12-03 20:49:15', '2024-12-23 01:16:59'),
('indah.sari@example.com', 'Indah Sari', 'indahpass789', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
('joko.widodo@example.com', 'Joko Widodo', 'jokopass123', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
('joni@contoh.com', 'Joni Susanto', 'password202', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
('nina@contoh.com', 'Nina Putri', 'password404', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
('rani@contoh.com', 'Rani Anggraini', '$2y$10$Il0qLmumnRzKH11KLsWdPu9yv2rAwLT9ixPXkGTcdruwdpzytFMC.', '2024-12-03 20:49:15', '2024-12-08 14:22:56'),
('siti@contoh.com', 'Siti Nurhaliza', 'password456', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
('wawan@contoh.com', 'Wawan Setiawan', 'password707', '2024-12-03 20:49:15', '2024-12-03 20:49:15'),
('yudi@contoh.com', 'Yudi Prasetyo', 'password505', '2024-12-03 20:49:15', '2024-12-03 20:49:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_advance_personal`
--

CREATE TABLE `user_advance_personal` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `headline` varchar(400) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user_advance_personal`
--

INSERT INTO `user_advance_personal` (`id`, `user_id`, `image`, `phone`, `headline`, `location`) VALUES
(1, 'ilhamrhmtkbr@gmail.com', 'andi.jpg', '081234567890', 'Software Engineer dengan pengalaman dalam pengembangan aplikasi web.', 'Jakarta'),
(2, 'siti@contoh.com', 'siti.jpg', '082345678901', 'Marketing Manager dengan pengalaman lebih dari 5 tahun.', 'Yogyakarta'),
(3, 'budi@gmail.com', 'budi.jpg', '083456789012', 'Project Manager dengan pengalaman dalam berbagai proyek besar.', 'Surabaya'),
(4, 'rani@contoh.com', 'rani.jpg', '084567890123', 'Civil Engineer yang berfokus pada pembangunan infrastruktur.', 'Bandung'),
(5, 'joni@contoh.com', 'joni.jpg', '085678901234', 'Nurse dengan pengalaman di rumah sakit dan klinik.', 'Medan'),
(6, 'arif@contoh.com', 'arif.jpg', '086789012345', 'Graphic Designer dengan kreativitas tinggi dan pengalaman di desain digital.', 'Bali'),
(7, 'nina@contoh.com', 'nina.jpg', '087890123456', 'Lawyer dengan pengalaman dalam konsultasi hukum bisnis.', 'Jakarta'),
(8, 'yudi@contoh.com', 'yudi.jpg', '088901234567', 'Accountant yang ahli dalam pengelolaan keuangan perusahaan.', 'Semarang'),
(9, 'dina@contoh.com', 'dina.jpg', '089012345678', 'Mechanical Engineer yang berfokus pada pengembangan produk otomotif.', 'Malang'),
(10, 'wawan@contoh.com', 'wawan.jpg', '090123456789', 'Psychologist yang berpengalaman dalam terapi individu dan kelompok.', 'Makassar');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_advance_skills`
--

CREATE TABLE `user_advance_skills` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `rating` int(5) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user_advance_skills`
--

INSERT INTO `user_advance_skills` (`id`, `user_id`, `name`, `rating`, `description`) VALUES
(1, 'ilhamrhmtkbr@gmail.com', 'Java Programming', 5, 'Menguasai pemrograman Java dan berbagai framework terkait.'),
(2, 'siti@contoh.com', 'Digital Marketing', 4, 'Menguasai strategi pemasaran digital dengan berbagai platform.'),
(3, 'budi@gmail.com', 'Project Management', 4, 'Memiliki pengalaman dalam manajemen proyek dan pengelolaan tim.'),
(4, 'rani@contoh.com', 'Civil Engineering', 5, 'Memiliki keahlian dalam mendesain dan merancang proyek konstruksi.'),
(5, 'joni@contoh.com', 'Nursing', 4, 'Memiliki keterampilan dalam merawat pasien dengan berbagai kondisi.'),
(6, 'arif@contoh.com', 'Graphic Design', 5, 'Mahasiswa desain grafis dengan kemampuan membuat desain kreatif.'),
(7, 'arif@contoh.com', 'Law', 5, 'Mahir dalam hukum perdata dan hukum pidana.'),
(8, 'yudi@contoh.com', 'Accounting', 4, 'Memiliki keterampilan dalam pembukuan dan perencanaan pajak.'),
(9, 'dina@contoh.com', 'Mechanical Design', 4, 'Pengalaman dalam merancang dan mengembangkan produk mesin.'),
(10, 'wawan@contoh.com', 'Psychology', 5, 'Keahlian dalam terapi psikologi dan konseling individu.');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_advance_socials`
--

CREATE TABLE `user_advance_socials` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) DEFAULT NULL,
  `app_name` enum('instagram','linkedln') NOT NULL,
  `url_link` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user_advance_socials`
--

INSERT INTO `user_advance_socials` (`id`, `user_id`, `app_name`, `url_link`, `created_at`) VALUES
(1, 'ilhamrhmtkbr@gmail.com', 'instagram', 'https://instagram.com/andi', '2024-12-03 20:49:15'),
(2, 'siti@contoh.com', 'instagram', 'https://instagram.com/siti', '2024-12-03 20:49:15'),
(3, 'budi@gmail.com', 'linkedln', 'https://linkedin.com/in/budi', '2024-12-03 20:49:15'),
(4, 'rani@contoh.com', 'instagram', 'https://instagram.com/rani', '2024-12-03 20:49:15'),
(5, 'joni@contoh.com', 'instagram', 'https://instagram.com/joni', '2024-12-03 20:49:15'),
(6, 'arif@contoh.com', 'instagram', 'https://instagram.com/arif', '2024-12-03 20:49:15'),
(7, 'nina@contoh.com', 'linkedln', 'https://linkedin.com/in/nina', '2024-12-03 20:49:15'),
(8, 'yudi@contoh.com', 'linkedln', 'https://linkedln.com/yudi', '2024-12-03 20:49:15'),
(9, 'dina@contoh.com', 'linkedln', 'https://linkedln.com/dina', '2024-12-03 20:49:15'),
(10, 'wawan@contoh.com', 'instagram', 'https://instagram.com/wawan', '2024-12-03 20:49:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_profile_education`
--

CREATE TABLE `user_profile_education` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `degree_id` int(11) NOT NULL,
  `institution` varchar(255) NOT NULL,
  `field` varchar(255) NOT NULL,
  `graduation_year` year(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user_profile_education`
--

INSERT INTO `user_profile_education` (`id`, `user_id`, `degree_id`, `institution`, `field`, `graduation_year`, `created_at`) VALUES
(1, 'andi.saputra@example.com', 1, 'Universitas Indonesia', 'Teknik Informatika', 2020, '2024-12-03 20:49:15'),
(2, 'budi.santoso@example.com', 2, 'Institut Teknologi Bandung', 'Manajemen', 2018, '2024-12-03 20:49:15'),
(3, 'citra.lestari@example.com', 3, 'Universitas Gadjah Mada', 'Teknik Sipil', 2017, '2024-12-03 20:49:15'),
(4, 'dian.purnama@example.com', 4, 'Politeknik Negeri Jakarta', 'Akuntansi', 2016, '2024-12-03 20:49:15'),
(5, 'eka.maulana@example.com', 5, 'Universitas Airlangga', 'Hukum', 2015, '2024-12-03 20:49:15'),
(6, 'fajar.ramadhan@example.com', 6, 'Universitas Binus', 'Teknologi Informasi', 2020, '2024-12-03 20:49:15'),
(7, 'gita.anggraini@example.com', 7, 'Universitas Trisakti', 'Ekonomi', 2019, '2024-12-03 20:49:15'),
(8, 'hendra.setiawan@example.com', 8, 'Politeknik Pariwisata Bali', 'Manajemen Pariwisata', 2021, '2024-12-03 20:49:15'),
(9, 'indah.sari@example.com', 9, 'Universitas Negeri Malang', 'Pendidikan', 2022, '2024-12-03 20:49:15'),
(10, 'joko.widodo@example.com', 10, 'Universitas Padjadjaran', 'Psikologi', 2023, '2024-12-03 20:49:15'),
(11, 'ilhamrhmtkbr@gmail.com', 1, 'Universitas Indonesia', 'Teknik Informatika', 2020, '2024-12-03 20:49:15'),
(12, 'siti@contoh.com', 2, 'Universitas Gadjah Mada', 'Ekonomi', 2018, '2024-12-03 20:49:15'),
(13, 'budi@gmail.com', 3, 'Universitas Airlangga', 'Manajemen', 2022, '2024-12-03 20:49:15'),
(14, 'rani@contoh.com', 4, 'Universitas Kristen Satya Wacana', 'Teknik Sipil', 2019, '2024-12-03 20:49:15'),
(15, 'joni@contoh.com', 5, 'Politeknik Kesehatan', 'Keperawatan', 2020, '2024-12-03 20:49:15'),
(16, 'arif@contoh.com', 6, 'Institut Seni Indonesia', 'Desain Grafis', 2021, '2024-12-03 20:49:15'),
(17, 'nina@contoh.com', 7, 'Universitas Diponegoro', 'Hukum', 2023, '2024-12-03 20:49:15'),
(18, 'yudi@contoh.com', 8, 'Politeknik Negeri Jakarta', 'Akuntansi', 2022, '2024-12-03 20:49:15'),
(19, 'dina@contoh.com', 9, 'Institut Teknologi Bandung', 'Teknik Mesin', 2019, '2024-12-03 20:49:15'),
(20, 'wawan@contoh.com', 10, 'Universitas Sanata Dharma', 'Psikologi', 2020, '2024-12-03 20:49:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_profile_education_degree`
--

CREATE TABLE `user_profile_education_degree` (
  `id` int(11) NOT NULL,
  `degree` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user_profile_education_degree`
--

INSERT INTO `user_profile_education_degree` (`id`, `degree`) VALUES
(1, 'Sarjana Komputer'),
(2, 'Magister Manajemen'),
(3, 'Sarjana Teknik'),
(4, 'Diploma Akuntansi'),
(5, 'Sarjana Hukum'),
(6, 'Magister Teknologi Informasi'),
(7, 'Sarjana Ekonomi'),
(8, 'Diploma Pariwisata'),
(9, 'Magister Pendidikan'),
(10, 'Sarjana Psikologi'),
(11, 'Sarjana Teknik Informatika'),
(12, 'Sarjana Ekonomi'),
(13, 'Magister Manajemen'),
(14, 'Sarjana Teknik Sipil'),
(15, 'Diploma Keperawatan'),
(16, 'Sarjana Desain Grafis'),
(17, 'Magister Hukum'),
(18, 'Diploma Akuntansi'),
(19, 'Sarjana Teknik Mesin'),
(20, 'Sarjana Psikologi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_profile_experience`
--

CREATE TABLE `user_profile_experience` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `job_description` text NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `work_duration` varchar(111) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user_profile_experience`
--

INSERT INTO `user_profile_experience` (`id`, `user_id`, `job_title`, `job_description`, `company_name`, `work_duration`, `created_at`) VALUES
(1, 'andi.saputra@example.com', 'Software Engineer', 'Mengembangkan aplikasi berbasis web dan mobile.', 'PT Teknologi Nusantara', '3 tahun', '2024-12-03 20:49:15'),
(2, 'budi.santoso@example.com', 'Manajer Keuangan', 'Mengelola anggaran dan laporan keuangan.', 'PT Keuangan Jaya', '5 tahun', '2024-12-03 20:49:15'),
(3, 'citra.lestari@example.com', 'Site Engineer', 'Mengawasi konstruksi dan infrastruktur.', 'PT Bangun Sejahtera', '4 tahun', '2024-12-03 20:49:15'),
(4, 'dian.purnama@example.com', 'Akuntan', 'Menyusun laporan keuangan perusahaan.', 'PT Laporan Cerdas', '3 tahun', '2024-12-03 20:49:15'),
(5, 'eka.maulana@example.com', 'Pengacara', 'Memberikan konsultasi hukum.', 'Kantor Hukum Indonesia', '5 tahun', '2024-12-03 20:49:15'),
(6, 'fajar.ramadhan@example.com', 'IT Specialist', 'Mengelola sistem IT perusahaan.', 'PT Digital Pro', '2 tahun', '2024-12-03 20:49:15'),
(7, 'gita.anggraini@example.com', 'Marketing Executive', 'Meningkatkan penjualan dan branding.', 'PT Promosi Hebat', '4 tahun', '2024-12-03 20:49:15'),
(8, 'hendra.setiawan@example.com', 'Tour Guide', 'Memandu wisatawan domestik dan internasional.', 'Bali Tour Agency', '6 tahun', '2024-12-03 20:49:15'),
(9, 'indah.sari@example.com', 'Guru', 'Mengajar mata pelajaran Matematika.', 'SMA Negeri 1 Jakarta', '5 tahun', '2024-12-03 20:49:15'),
(10, 'joko.widodo@example.com', 'Psikolog', 'Memberikan layanan konsultasi psikologi.', 'Klinik Sehat Jiwa', '2 tahun', '2024-12-03 20:49:15'),
(11, 'ilhamrhmtkbr@gmail.com', 'Software Engineer', 'Bertanggung jawab untuk mengembangkan aplikasi berbasis web.', 'PT. Techno Indonesia', '2 Tahun', '2024-12-03 20:49:15'),
(12, 'siti@contoh.com', 'Marketing Manager', 'Memimpin tim pemasaran dan merancang strategi pemasaran.', 'PT. Marketing Nusantara', '3 Tahun', '2024-12-03 20:49:15'),
(13, 'budi@gmail.com', 'Project Manager', 'Mengelola proyek dan tim untuk memastikan keberhasilan proyek.', 'PT. Proyek Indonesia', '1 Tahun', '2024-12-03 20:49:15'),
(14, 'rani@contoh.com', 'Civil Engineer', 'Mendesain dan mengawasi pembangunan infrastruktur.', 'PT. Konstruksi Maju', '3 Tahun', '2024-12-03 20:49:15'),
(15, 'joni@contoh.com', 'Nurse', 'Merawat pasien dan memastikan kenyamanan mereka di rumah sakit.', 'RSU Sehat Selalu', '2 Tahun', '2024-12-03 20:49:15'),
(16, 'arif@contoh.com', 'Graphic Designer', 'Merancang desain grafis untuk berbagai media.', 'PT. Desain Kreatif', '1 Tahun', '2024-12-03 20:49:15'),
(17, 'nina@contoh.com', 'Lawyer', 'Memberikan konsultasi hukum kepada klien.', 'Kantor Hukum Maju', '5 Tahun', '2024-12-03 20:49:15'),
(18, 'yudi@contoh.com', 'Accountant', 'Mengelola keuangan dan laporan pajak perusahaan.', 'PT. Akuntansi Jaya', '4 Tahun', '2024-12-03 20:49:15'),
(19, 'dina@contoh.com', 'Mechanical Engineer', 'Merancang dan menguji sistem mekanik di perusahaan.', 'PT. Mesin Inovasi', '2 Tahun', '2024-12-03 20:49:15'),
(20, 'wawan@contoh.com', 'Psychologist', 'Memberikan terapi kepada pasien untuk membantu mereka mengatasi masalah psikologis.', 'Klinik Psikologi Sehat', '3 Tahun', '2024-12-03 20:49:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_profile_portfolio`
--

CREATE TABLE `user_profile_portfolio` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `link` varchar(255) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user_profile_portfolio`
--

INSERT INTO `user_profile_portfolio` (`id`, `user_id`, `title`, `description`, `link`, `picture`, `created_at`) VALUES
(1, 'andi.saputra@example.com', 'Aplikasi Mobile Banking', 'Aplikasi untuk transaksi perbankan.', 'https://github.com/andi/mobilebanking', 'img/portfolio1.jpg', '2024-12-03 20:49:15'),
(2, 'budi.santoso@example.com', 'Sistem Keuangan Perusahaan', 'Membangun sistem manajemen keuangan.', 'https://github.com/budi/financesystem', 'img/portfolio2.jpg', '2024-12-03 20:49:15'),
(3, 'citra.lestari@example.com', 'Jembatan Layang XYZ', 'Proyek infrastruktur besar.', 'https://example.com/citra_project', 'img/portfolio3.jpg', '2024-12-03 20:49:15'),
(4, 'dian.purnama@example.com', 'Laporan Keuangan Tahunan', 'Penyusunan laporan akuntansi.', 'https://example.com/dian_report', 'img/portfolio4.jpg', '2024-12-03 20:49:15'),
(5, 'eka.maulana@example.com', 'Draft Kontrak Kerja', 'Menyusun dokumen kontrak hukum.', 'https://example.com/eka_contract', 'img/portfolio5.jpg', '2024-12-03 20:49:15'),
(6, 'fajar.ramadhan@example.com', 'Pengaturan Server Perusahaan', 'Mengelola infrastruktur IT.', 'https://github.com/fajar/serverconfig', 'img/portfolio6.jpg', '2024-12-03 20:49:15'),
(7, 'gita.anggraini@example.com', 'Kampanye Produk Baru', 'Strategi marketing produk.', 'https://example.com/gita_campaign', 'img/portfolio7.jpg', '2024-12-03 20:49:15'),
(8, 'hendra.setiawan@example.com', 'Panduan Wisata Bali', 'Paket wisata terlengkap.', 'https://example.com/hendra_tour', 'img/portfolio8.jpg', '2024-12-03 20:49:15'),
(9, 'indah.sari@example.com', 'Modul Pembelajaran Online', 'Materi untuk siswa.', 'https://example.com/indah_module', 'img/portfolio9.jpg', '2024-12-03 20:49:15'),
(10, 'joko.widodo@example.com', 'Studi Kasus Psikologi', 'Penelitian tentang psikologi.', 'https://example.com/joko_psych', 'img/portfolio10.jpg', '2024-12-03 20:49:15'),
(11, 'ilhamrhmtkbr@gmail.com', 'Aplikasi Manajemen Keuangan', 'Aplikasi untuk mengelola pengeluaran dan pendapatan pribadi.', 'http://portfolio-andi.com', 'image1.jpg', '2024-12-03 20:49:15'),
(12, 'siti@contoh.com', 'Website E-commerce', 'Website untuk jual beli produk secara online.', 'http://portfolio-siti.com', 'image2.jpg', '2024-12-03 20:49:15'),
(13, 'budi@gmail.com', 'Sistem Manajemen Proyek', 'Sistem untuk melacak dan mengelola proyek dan tim.', 'http://portfolio-budi.com', 'image3.jpg', '2024-12-03 20:49:15'),
(14, 'rani@contoh.com', 'Proyek Jembatan', 'Pembangunan jembatan untuk menghubungkan dua kota.', 'http://portfolio-rani.com', 'image4.jpg', '2024-12-03 20:49:15'),
(15, 'joni@contoh.com', 'Aplikasi Kesehatan', 'Aplikasi untuk memantau kesehatan dan jadwal perawatan pasien.', 'http://portfolio-joni.com', 'image5.jpg', '2024-12-03 20:49:15'),
(16, 'arif@contoh.com', 'Desain Brosur', 'Desain brosur untuk perusahaan-perusahaan besar.', 'http://portfolio-arif.com', 'image6.jpg', '2024-12-03 20:49:15'),
(17, 'nina@contoh.com', 'Konsultasi Hukum', 'Website untuk layanan konsultasi hukum online.', 'http://portfolio-nina.com', 'image7.jpg', '2024-12-03 20:49:15'),
(18, 'yudi@contoh.com', 'Laporan Keuangan', 'Membuat laporan keuangan untuk perusahaan besar.', 'http://portfolio-yudi.com', 'image8.jpg', '2024-12-03 20:49:15'),
(19, 'dina@contoh.com', 'Desain Mesin', 'Proyek desain mesin untuk sektor otomotif.', 'http://portfolio-dina.com', 'image9.jpg', '2024-12-03 20:49:15'),
(20, 'wawan@contoh.com', 'Terapi Psikologi', 'Aplikasi untuk melacak terapi dan perkembangan pasien.', 'http://portfolio-wawan.com', 'image10.jpg', '2024-12-03 20:49:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_sessions`
--

CREATE TABLE `user_sessions` (
  `user_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user_sessions`
--

INSERT INTO `user_sessions` (`user_id`) VALUES
('budi@gmail.com'),
('ilhamrhmtkbr@gmail.com'),
('rani@contoh.com');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `candidates_ibfk_2` (`user_id`);

--
-- Indeks untuk tabel `company_employee_projects`
--
ALTER TABLE `company_employee_projects`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `company_employee_roles`
--
ALTER TABLE `company_employee_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `company_office_departments`
--
ALTER TABLE `company_office_departments`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `company_office_financial_transactions`
--
ALTER TABLE `company_office_financial_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `company_office_recruitments`
--
ALTER TABLE `company_office_recruitments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indeks untuk tabel `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indeks untuk tabel `employee_attendance`
--
ALTER TABLE `employee_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indeks untuk tabel `employee_attendance_rules`
--
ALTER TABLE `employee_attendance_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `employee_contracts`
--
ALTER TABLE `employee_contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indeks untuk tabel `employee_leave_requests`
--
ALTER TABLE `employee_leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indeks untuk tabel `employee_overtime`
--
ALTER TABLE `employee_overtime`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indeks untuk tabel `employee_payrolls`
--
ALTER TABLE `employee_payrolls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indeks untuk tabel `employee_project_assignments`
--
ALTER TABLE `employee_project_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `user_advance_personal`
--
ALTER TABLE `user_advance_personal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `user_advance_skills`
--
ALTER TABLE `user_advance_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `user_advance_socials`
--
ALTER TABLE `user_advance_socials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `user_profile_education`
--
ALTER TABLE `user_profile_education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `degree_id` (`degree_id`);

--
-- Indeks untuk tabel `user_profile_education_degree`
--
ALTER TABLE `user_profile_education_degree`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `user_profile_experience`
--
ALTER TABLE `user_profile_experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `user_profile_portfolio`
--
ALTER TABLE `user_profile_portfolio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;

--
-- AUTO_INCREMENT untuk tabel `company_employee_projects`
--
ALTER TABLE `company_employee_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT untuk tabel `company_employee_roles`
--
ALTER TABLE `company_employee_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT untuk tabel `company_office_departments`
--
ALTER TABLE `company_office_departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT untuk tabel `company_office_financial_transactions`
--
ALTER TABLE `company_office_financial_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT untuk tabel `company_office_recruitments`
--
ALTER TABLE `company_office_recruitments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT untuk tabel `employee_attendance`
--
ALTER TABLE `employee_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `employee_attendance_rules`
--
ALTER TABLE `employee_attendance_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT untuk tabel `employee_contracts`
--
ALTER TABLE `employee_contracts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT untuk tabel `employee_leave_requests`
--
ALTER TABLE `employee_leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT untuk tabel `employee_overtime`
--
ALTER TABLE `employee_overtime`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT untuk tabel `employee_payrolls`
--
ALTER TABLE `employee_payrolls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT untuk tabel `employee_project_assignments`
--
ALTER TABLE `employee_project_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT untuk tabel `user_advance_personal`
--
ALTER TABLE `user_advance_personal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT untuk tabel `user_advance_skills`
--
ALTER TABLE `user_advance_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;

--
-- AUTO_INCREMENT untuk tabel `user_advance_socials`
--
ALTER TABLE `user_advance_socials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT untuk tabel `user_profile_education`
--
ALTER TABLE `user_profile_education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT untuk tabel `user_profile_education_degree`
--
ALTER TABLE `user_profile_education_degree`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `user_profile_experience`
--
ALTER TABLE `user_profile_experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT untuk tabel `user_profile_portfolio`
--
ALTER TABLE `user_profile_portfolio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `company_office_recruitments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `candidates_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `company_office_recruitments`
--
ALTER TABLE `company_office_recruitments`
  ADD CONSTRAINT `company_office_recruitments_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `company_office_departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `company_office_departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `employees_ibfk_3` FOREIGN KEY (`role_id`) REFERENCES `company_employee_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `employee_attendance`
--
ALTER TABLE `employee_attendance`
  ADD CONSTRAINT `employee_attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `employee_contracts`
--
ALTER TABLE `employee_contracts`
  ADD CONSTRAINT `employee_contracts_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`user_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `employee_leave_requests`
--
ALTER TABLE `employee_leave_requests`
  ADD CONSTRAINT `employee_leave_requests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `employee_overtime`
--
ALTER TABLE `employee_overtime`
  ADD CONSTRAINT `employee_overtime_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`user_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `employee_payrolls`
--
ALTER TABLE `employee_payrolls`
  ADD CONSTRAINT `employee_payrolls_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`user_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `employee_project_assignments`
--
ALTER TABLE `employee_project_assignments`
  ADD CONSTRAINT `employee_project_assignments_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `employee_project_assignments_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `company_employee_projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_advance_personal`
--
ALTER TABLE `user_advance_personal`
  ADD CONSTRAINT `user_advance_personal_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_advance_skills`
--
ALTER TABLE `user_advance_skills`
  ADD CONSTRAINT `user_advance_skills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_advance_socials`
--
ALTER TABLE `user_advance_socials`
  ADD CONSTRAINT `user_advance_socials_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_profile_education`
--
ALTER TABLE `user_profile_education`
  ADD CONSTRAINT `user_profile_education_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_profile_education_ibfk_2` FOREIGN KEY (`degree_id`) REFERENCES `user_profile_education_degree` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_profile_experience`
--
ALTER TABLE `user_profile_experience`
  ADD CONSTRAINT `user_profile_experience_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_profile_portfolio`
--
ALTER TABLE `user_profile_portfolio`
  ADD CONSTRAINT `user_profile_portfolio_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
