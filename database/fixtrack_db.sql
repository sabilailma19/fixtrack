-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 22, 2026 at 02:01 PM
-- Server version: 8.0.40
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fixtrack_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Kerusakan Elektronik', 'Kerusakan perangkat elektronik kampus.', 1, '2026-11-19 06:43:33', '2026-11-19 06:43:33'),
(2, 'Kerusakan Furniture', 'Kerusakan meja, kursi, lemari, dan perlengkapan.', 1, '2026-11-19 06:43:33', '2026-11-19 06:43:33'),
(3, 'Kerusakan Bangunan', 'Kerusakan dinding, plafon, pintu, dan jendela.', 1, '2026-11-19 06:43:33', '2026-11-19 06:43:33'),
(4, 'Instalasi Listrik', 'Gangguan lampu, stop kontak, dan instalasi listrik.', 1, '2026-11-19 06:43:33', '2026-11-19 06:43:33'),
(5, 'Kerusakan Sanitasi', 'Kerusakan toilet, wastafel, dan saluran air.', 1, '2026-11-19 06:43:33', '2026-11-19 06:43:33'),
(6, 'Jaringan Internet', 'Gangguan koneksi Wi-Fi dan jaringan kampus.', 1, '2026-11-19 06:43:33', '2026-11-19 06:43:33');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int NOT NULL,
  `complaint_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reporter_id` int NOT NULL,
  `category_id` int NOT NULL,
  `facility_id` int NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('menunggu_verifikasi','diverifikasi','ditolak','ditugaskan','diproses','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu_verifikasi',
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `complaint_code`, `reporter_id`, `category_id`, `facility_id`, `title`, `description`, `photo`, `status`, `admin_note`, `rejection_reason`, `created_at`, `updated_at`) VALUES
(1, 'FT-20261119-5299D6', 3, 1, 1, 'proyektor ga bisa nyala', 'ga bis anyal', 'uploads/complaints/85b22b2098117754cc26566cb8239245.jpg', 'selesai', NULL, NULL, '2026-11-19 18:55:55', '2026-11-19 19:33:56'),
(2, 'FT-20261120-6D6A60', 3, 1, 2, 'ac rusak', 'ac rusaknga idngaiokPUFIO3WSJ9IOGS,P[dsWGSEJ Mc2', 'uploads/complaints/e385bc75e994382e5d95ff9078d6b804.jpg', 'selesai', NULL, NULL, '2026-11-20 06:06:04', '2026-11-20 06:27:05');

-- --------------------------------------------------------

--
-- Table structure for table `complaint_assignments`
--

CREATE TABLE `complaint_assignments` (
  `id` int NOT NULL,
  `complaint_id` int NOT NULL,
  `officer_id` int NOT NULL,
  `assigned_by` int NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `completion_notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('ditugaskan','diterima','diproses','selesai','dibatalkan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ditugaskan',
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `accepted_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `complaint_assignments`
--

INSERT INTO `complaint_assignments` (`id`, `complaint_id`, `officer_id`, `assigned_by`, `notes`, `completion_notes`, `status`, `assigned_at`, `accepted_at`, `completed_at`) VALUES
(1, 1, 2, 1, '', NULL, 'selesai', '2026-11-19 19:17:55', '2026-11-20 02:33:53', '2026-11-20 02:33:56'),
(2, 2, 2, 1, 'hijjuiihihhuhuhhhhiiijojjnhbvbyjbhb  hjjih', 'mamoakpppa,dmoml', 'selesai', '2026-11-20 06:09:43', '2026-11-20 13:11:24', '2026-11-20 13:27:05');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` int NOT NULL,
  `building_name` varchar(100) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `facility_name` varchar(150) NOT NULL,
  `description` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `building_name`, `room_name`, `facility_name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Gedung Fasilkom', 'Lab Komputer 1', 'Komputer', 'Perangkat komputer laboratorium.', 1, '2026-11-19 06:50:16', '2026-11-19 06:50:16'),
(2, 'Gedung Fasilkom', 'Ruang 101', 'AC', 'Pendingin ruangan kelas.', 1, '2026-11-19 06:50:16', '2026-11-19 06:50:16'),
(3, 'Gedung Fasilkom', 'Ruang 102', 'Proyektor', 'Perangkat presentasi kelas.', 1, '2026-11-19 06:50:16', '2026-11-19 06:50:16'),
(4, 'Gedung Rektorat', 'Ruang Rapat', 'Meja', 'Meja ruang rapat.', 1, '2026-11-19 06:50:16', '2026-11-19 06:50:16'),
(5, 'Gedung Perpustakaan', 'Lantai 1', 'Lampu', 'Penerangan area perpustakaan.', 1, '2026-11-19 06:50:16', '2026-11-19 06:50:16'),
(6, 'Gedung Fasilkom', 'Lab Komputer 2', 'Kursi', 'Kursi laboratorium.', 1, '2026-11-19 06:50:16', '2026-11-19 06:50:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas','pelapor') NOT NULL DEFAULT 'pelapor',
  `identity_number` varchar(30) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `identity_number`, `phone`, `is_active`, `created_at`) VALUES
(1, 'Administrator', 'admin@fixtrack.test', '$2y$10$joPgeTQ/Cu54K1C.KO8zBOVufyT4O34ElR/8s1TBwwGX5poDpF4f6', 'admin', NULL, NULL, 1, '2026-11-19 05:41:25'),
(2, 'Petugas Kampus', 'petugas@fixtrack.test', '$2y$10$KlTokI9k3Rc8yoD1G0wNs.87aslwuUJYmv/hULO1eW2i3FD9w2kWm', 'petugas', NULL, NULL, 1, '2026-11-19 05:41:25'),
(3, 'Mahasiswa', 'pelapor@fixtrack.test', '$2y$10$QmEu9blhRqoDZquOU9/WkuCSfo11JrK8P32Lfe.li7IxiqTHAD8.q', 'pelapor', NULL, NULL, 1, '2026-11-19 05:41:25'),
(4, '23081010039 SABILA ILMA IRJAYANA', '23081010039@student.upnjatim.ac.id', '$2y$10$wEQ9CZNhLmaFEISJlbrmUOww8tluAuQpIMkC4lycd6/aqxi7oPiHq', 'pelapor', '23081010039', '083835349590', 1, '2026-11-19 06:42:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `complaint_code` (`complaint_code`),
  ADD KEY `idx_reporter` (`reporter_id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_facility` (`facility_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `complaint_assignments`
--
ALTER TABLE `complaint_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_assignment_complaint` (`complaint_id`),
  ADD KEY `idx_assignment_officer` (`officer_id`),
  ADD KEY `fk_assignment_admin` (`assigned_by`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `complaint_assignments`
--
ALTER TABLE `complaint_assignments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `fk_complaint_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_complaint_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_complaint_reporter` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `complaint_assignments`
--
ALTER TABLE `complaint_assignments`
  ADD CONSTRAINT `fk_assignment_admin` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_assignment_complaint` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_assignment_officer` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
