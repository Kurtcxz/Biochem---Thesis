-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 16, 2024 at 03:59 AM
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
-- Database: `biochem`
--

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `rep_first_name` varchar(50) DEFAULT NULL,
  `rep_last_name` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `address`, `email`, `contact_number`, `rep_first_name`, `rep_last_name`, `created_at`) VALUES
(1, 'PogiINC', '12558 Sampaloc Street Dau-Homesite', 'pogi@gmail.com', '09053031833', 'Allen', 'Durham', '2024-10-08 03:45:17'),
(2, 'b', '12558 Sampaloc Street Dau-Homesite', 'b@gmail.com', '09053031833', 'Carlos', 'John', '2024-10-08 11:47:27'),
(3, 'STI College Angeles', '12558 Sampaloc Street Dau-Homesite', 'sti123@angeles.sti.edu.ph', NULL, 'Denzel Aivan', 'Palo', '2024-11-11 02:23:35'),
(4, 'C', '12558 Sampaloc Street Dau-Homesite', 'c@gmail.com', '09053031833', 'Denzel Aivan', 'Palo', '2024-11-11 02:55:29'),
(5, 'Systems Plus College Foundation', 'Balibago, Angwlws City', 'asas@gmail.com', '09053031833', 'Donna ', 'Cruz', '2024-11-11 04:10:02');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tests` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('registered','pending_tests','tests_assigned','sent_to_marketing','sent_to_front_desk','processed') DEFAULT 'registered',
  `sent_to_front_desk_at` datetime DEFAULT NULL,
  `patient_id` varchar(10) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `company_id`, `first_name`, `last_name`, `age`, `gender`, `birthday`, `email`, `contact_number`, `created_at`, `tests`, `address`, `status`, `sent_to_front_desk_at`, `patient_id`, `username`, `password`, `appointment_date`, `appointment_time`) VALUES
(75, 1, 'Juan', 'tytree', 22, 'male', '2002-01-01', 'am@pogi.com', '09053031833', '2024-11-11 02:33:26', NULL, NULL, 'processed', '2024-11-12 00:44:12', 'P00001', 'P00001_tytree', '20020101_tytree', '2024-11-15', '10:00:00'),
(76, 1, 'ric', 't', 11, 'male', '2002-01-01', 'aa@gmail.com', '09053031833', '2024-11-11 02:58:33', NULL, NULL, 'sent_to_front_desk', '2024-11-11 12:25:03', NULL, NULL, NULL, NULL, NULL),
(77, 5, 'Juan', 'Dela Cruz', 22, 'male', '2002-01-01', 'bbb2@gmail.com', '09053031833', '2024-11-11 04:21:36', NULL, NULL, 'registered', NULL, NULL, NULL, NULL, NULL, NULL),
(78, 2, 'kurtAlvero', 'alvero', 22, 'male', '2002-01-01', 'denzelpalo077@gmail.com', '09053031833', '2024-11-15 04:44:41', NULL, NULL, 'sent_to_front_desk', '2024-11-15 12:45:25', NULL, NULL, NULL, NULL, NULL),
(79, 1, 'Paul', 'Maceda', NULL, NULL, NULL, 'pmaceda@gmail.com', '09053031834', '2024-11-16 01:57:56', NULL, '12551 Sampaloc Street Dau-Homesite', 'pending_tests', NULL, NULL, NULL, NULL, NULL, NULL),
(80, 1, 'Gian ', 'Salvador', NULL, NULL, NULL, 'gsalvador@gmail.com', '09053031835', '2024-11-16 01:57:57', NULL, '12559 Sampaloc Street Dau-Homesite', 'pending_tests', NULL, NULL, NULL, NULL, NULL, NULL),
(81, 1, 'Paul', 'Maceda', NULL, NULL, NULL, 'pmaceda@gmail.com', '09053031834', '2024-11-16 01:57:57', NULL, '12551 Sampaloc Street Dau-Homesite', 'pending_tests', NULL, NULL, NULL, NULL, NULL, NULL),
(82, 1, 'Gian ', 'Salvador', NULL, NULL, NULL, 'gsalvador@gmail.com', '09053031835', '2024-11-16 01:57:57', NULL, '12559 Sampaloc Street Dau-Homesite', 'pending_tests', NULL, NULL, NULL, NULL, NULL, NULL),
(83, 1, 'medo', 'sasazakik', NULL, NULL, NULL, 'sample@gmai.com', '09124123123', '2024-11-16 02:33:46', NULL, '23300 - angeles city', 'pending_tests', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_lab_tests`
--

CREATE TABLE `employee_lab_tests` (
  `employee_id` int(11) NOT NULL,
  `lab_test_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_lab_tests`
--

INSERT INTO `employee_lab_tests` (`employee_id`, `lab_test_id`) VALUES
(75, 1),
(75, 2),
(75, 3),
(76, 3),
(76, 4),
(76, 5),
(77, 1),
(77, 2),
(78, 1),
(78, 11);

-- --------------------------------------------------------

--
-- Table structure for table `employee_tests`
--

CREATE TABLE `employee_tests` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `test_id` int(11) DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_details`
--

CREATE TABLE `invoice_details` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `test_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_tests`
--

CREATE TABLE `lab_tests` (
  `id` int(11) NOT NULL,
  `test_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_tests`
--

INSERT INTO `lab_tests` (`id`, `test_name`) VALUES
(1, 'Complete blood count'),
(2, 'Blood typing'),
(3, 'Prothrombin Time'),
(4, 'Partial Thromboplastin Time'),
(5, 'Routine Urinalysis'),
(6, 'Pregnancy Test'),
(7, 'Lipid Profile'),
(8, 'Liver Function Test'),
(9, 'HIV Test'),
(10, 'Hepatitis B Test'),
(11, 'Chest X-ray'),
(12, 'Spine X-ray');

-- --------------------------------------------------------

--
-- Table structure for table `package_tests`
--

CREATE TABLE `package_tests` (
  `package_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_change_requests`
--

CREATE TABLE `password_change_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `patient_id` varchar(10) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `birthday` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `address` text DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_schedules`
--

CREATE TABLE `patient_schedules` (
  `id` int(11) NOT NULL,
  `patient_id` varchar(10) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_tests`
--

CREATE TABLE `patient_tests` (
  `id` int(11) NOT NULL,
  `patient_id` varchar(10) NOT NULL,
  `test_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_tests`
--

INSERT INTO `patient_tests` (`id`, `patient_id`, `test_id`) VALUES
(1, 'P00062', 1),
(2, 'P00062', 10),
(3, 'P00062', 12),
(4, 'P00064', 5),
(5, 'P00064', 6),
(6, 'P00060', 1),
(7, 'P00060', 3),
(8, 'P00060', 4),
(9, 'P00035', 1),
(10, 'P00035', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`id`, `name`, `category`, `price`) VALUES
(1, 'Complete Blood count with pc', 'Hematology', 150.00),
(2, 'Blood Typing/ RH factor', 'Hematology', 200.00),
(3, 'Clotting time', 'Coagulation', 90.00),
(4, 'Bleeding time', 'Coagulation', 90.00),
(5, 'Partial Thromboplastin Time (PTT)', 'Coagulation', 400.00),
(6, 'Prothrombin Time (PT)', 'Coagulation', 270.00),
(7, 'Routine Fecalysis', 'Stool', 70.00),
(8, 'Occult Blood', 'Stool', 150.00),
(9, 'FBS/RBS', 'Clinical Chemistry', 120.00),
(10, 'UREA (Bun)', 'Clinical Chemistry', 120.00),
(11, 'Creatinine', 'Clinical Chemistry', 120.00),
(12, 'Uric Acid (BUA)', 'Clinical Chemistry', 120.00),
(13, 'Total Chole', 'Clinical Chemistry', 120.00),
(14, 'Triglycerides', 'Clinical Chemistry', 180.00),
(15, 'Lipoprotein (HDL/LDL)', 'Clinical Chemistry', 320.00),
(16, 'SGOT/AST', 'Clinical Chemistry', 160.00),
(17, 'SGPT/ALT', 'Clinical Chemistry', 160.00),
(18, 'HBA1C (Glycosylated HGB)', 'Clinical Chemistry', 720.00),
(19, 'Amylase', 'Clinical Chemistry', 800.00),
(20, 'CK-MB', 'Clinical Chemistry', 1000.00),
(21, 'CK-MM', 'Clinical Chemistry', 1000.00),
(22, 'NA/K/CL', 'Clinical Chemistry', 230.00),
(23, 'Ionized Calcium', 'Clinical Chemistry', 320.00),
(24, 'Inorganic Phosphorus', 'Clinical Chemistry', 180.00),
(25, 'Magnesium', 'Clinical Chemistry', 250.00),
(26, 'Total Calcium', 'Clinical Chemistry', 375.00),
(27, 'Iron', 'Clinical Chemistry', 650.00),
(28, 'Troponin T', 'Clinical Chemistry', 1000.00),
(29, 'Troponin I', 'Clinical Chemistry', 1200.00),
(30, 'Urine Pregnancy Test', 'Pregnancy Test', 150.00),
(31, 'Serum Pregnancy Test', 'Pregnancy Test', 200.00),
(32, 'RPR/VDRL', 'Serology', 180.00),
(33, 'ASO Titer', 'Serology', 300.00),
(34, 'HIV', 'Serology', 720.00),
(35, 'THPHIDOT', 'Serology', 900.00),
(36, 'H. Pylori Serum', 'Serology', 1200.00),
(37, 'HBsAg', 'Immunology/ Serology', 200.00),
(38, 'Hepa-B Profile', 'Immunology/ Serology', 1300.00),
(39, 'Hepa B Profile IGM', 'Immunology/ Serology', 1500.00),
(40, 'Anti-HBS', 'Immunology/ Serology', 220.00),
(41, 'Anti-HBE', 'Immunology/ Serology', 420.00),
(42, 'Anti-HBC IGG', 'Immunology/ Serology', 350.00),
(43, 'Anti-HBC IGM', 'Immunology/ Serology', 400.00),
(44, 'Anti-HAV IGM', 'Immunology/ Serology', 300.00),
(45, 'Anti-HCV', 'Immunology/ Serology', 800.00),
(46, 'Total IGE', 'Immunology/ Serology', 1200.00),
(47, 'HAV', 'Immunology/ Serology', 250.00),
(48, 'T3', 'Endocrinology', 500.00),
(49, 'T4', 'Endocrinology', 500.00),
(50, 'FT3', 'Endocrinology', 500.00),
(51, 'FT4', 'Endocrinology', 500.00),
(52, 'TSH', 'Endocrinology', 700.00),
(53, 'FT4i', 'Endocrinology', 2000.00),
(54, 'CEA', 'Endocrinology', 900.00),
(55, 'Testosterone', 'Endocrinology', 900.00),
(56, 'Chest PA', 'Xray', 250.00),
(57, 'Chest Apicolordotic', 'Xray', 370.00),
(58, 'Chest APL', 'Xray', 500.00),
(59, 'Clinic Consultation', 'Clinic', 350.00),
(60, 'ECG', 'Clinic', 200.00),
(61, 'Medical Certification', 'Clinic', 50.00);

-- --------------------------------------------------------

--
-- Table structure for table `test_packages`
--

CREATE TABLE `test_packages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `test_results`
--

CREATE TABLE `test_results` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `lab_test_id` int(11) NOT NULL,
  `result` text NOT NULL,
  `date_entered` datetime NOT NULL,
  `entered_by` int(11) NOT NULL,
  `status` enum('pending_review','approved','rejected') NOT NULL DEFAULT 'pending_review',
  `reviewed_by` int(11) DEFAULT NULL,
  `review_date` datetime DEFAULT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `test_selection_history`
--

CREATE TABLE `test_selection_history` (
  `id` int(11) NOT NULL,
  `test_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `selection_count` int(11) DEFAULT 0,
  `last_selected` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `birthday` date DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','marketing_manager','front_desk','junior_medtech','senior_medtech','invoice_manager','company_rep') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `company_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive','archived') NOT NULL DEFAULT 'inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `birthday`, `email`, `contact_number`, `address`, `username`, `password`, `role`, `created_at`, `company_id`) VALUES
(1, 'Denzel Aivan', 'Palo', '2002-02-01', 'denzelpalo077@gmail.com', '09053031833', '12558 Sampaloc Street Dau-Homesite', 'admin', '$2y$10$wQvhdS0eZs6ym1tAfx7bs.snIBNLT/VB07aZLSKbgHMSNkm0mJzC6', 'admin', '2024-10-08 03:25:47', NULL),
(2, 'John', 'Doe', '1994-01-01', 'JD@gnail.com', '09053031833', '12558 Sampaloc Street Dau-Homesite', 'mama', '$2y$10$xw8bRKr4F/ve4.scpO8XMech092dJw884XP.RPMI8dsmQWCwsG.SO', 'marketing_manager', '2024-10-08 03:35:18', NULL),
(3, 'Allen', 'Durham', NULL, 'pogi@gmail.com', '09053031833', '12558 Sampaloc Street Dau-Homesite', 'rep_a', '$2y$10$oEIYt91MwKEmVpQiqm6RK..ldytO4jRJrgVN6moqtnWBIqRXzwzaq', 'company_rep', '2024-10-08 03:55:28', 1),
(4, 'Carlos', 'John', NULL, 'b@gmail.com', '09053031833', '12558 Sampaloc Street Dau-Homesite', 'rep_b', '$2y$10$Yr21HzzcMDDTctmO8PMEK.M/3.q6IYwjkUkx2UeWP9tJCSUsE4kK6', 'company_rep', '2024-10-08 11:48:40', 2),
(5, 'Amiel', 'Calma', '2002-01-01', 'am@pogi.com', '09053031833', '12558 Sampaloc Street Dau-Homesite', 'fd01', '$2y$10$Ji2MGEQZvzxQk.Zi5FUiI.t11PE5XjePUU5k1QNMhNHwzEm7FCKgK', 'front_desk', '2024-10-11 05:41:26', NULL),
(6, 'Allan', 'K', '2002-01-01', 'denzelaivan@pogi.com', '09053031833', '12558 Sampaloc Street Dau-Homesite', 'jrm', '$2y$10$2lALaS.2pjKfAcpaLHX32OnUcb5zfTPSFgMlkHlcmSskAMBH2HyKy', 'junior_medtech', '2024-10-11 07:20:14', NULL),
(7, 'Denzel Aivan', 'Palo', NULL, 'sti123@angeles.sti.edu.ph', '', '12558 Sampaloc Street Dau-Homesite', 'rep3', '$2y$10$cix8K/Rda.UiMEza4OqRLO8qyUFOT.kahkgaEkxDoBGgmdaYaYZG.', 'company_rep', '2024-11-11 02:25:05', 3),
(8, 'james', 'Arthur', '2002-01-01', 'ad@gmail.com', '09053031833', '12558 Sampaloc Street Dau-Homesite', 'mm2', '$2y$10$Hik4kahJ4xtCHBXWdaJY6uIiRdqje.I6I42sodKKawRmIG3D./4w6', 'marketing_manager', '2024-11-11 04:06:01', NULL),
(9, 'Donna ', 'Cruz', NULL, 'asas@gmail.com', '09053031833', 'Balibago, Angwlws City', 'aaa', '$2y$10$0VN9PJYN9YDVFsakeCsAeOAFdIFhwZj2g77mO8uyJfMT63XgmpTjm', 'company_rep', '2024-11-11 04:17:58', 5);
-- --------------------------------------------------------

--
-- Table structure for table `user_logs`
--

CREATE TABLE `user_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `walkin_patients`
--

CREATE TABLE `walkin_patients` (
  `id` int(11) NOT NULL,
  `patient_id` varchar(10) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `tests` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patient_id` (`patient_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `employee_lab_tests`
--
ALTER TABLE `employee_lab_tests`
  ADD PRIMARY KEY (`employee_id`,`lab_test_id`),
  ADD KEY `lab_test_id` (`lab_test_id`);

--
-- Indexes for table `employee_tests`
--
ALTER TABLE `employee_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `test_id` (`test_id`),
  ADD KEY `assigned_by` (`assigned_by`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `invoice_details`
--
ALTER TABLE `invoice_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `lab_tests`
--
ALTER TABLE `lab_tests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `package_tests`
--
ALTER TABLE `package_tests`
  ADD PRIMARY KEY (`package_id`,`test_id`),
  ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `password_change_requests`
--
ALTER TABLE `password_change_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patient_id` (`patient_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `patient_schedules`
--
ALTER TABLE `patient_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_schedules_ibfk_1` (`patient_id`);

--
-- Indexes for table `patient_tests`
--
ALTER TABLE `patient_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_packages`
--
ALTER TABLE `test_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_results`
--
ALTER TABLE `test_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `lab_test_id` (`lab_test_id`),
  ADD KEY `entered_by` (`entered_by`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `test_selection_history`
--
ALTER TABLE `test_selection_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_id` (`test_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_company` (`company_id`);

--
-- Indexes for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `walkin_patients`
--
ALTER TABLE `walkin_patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patient_id` (`patient_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `employee_tests`
--
ALTER TABLE `employee_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_details`
--
ALTER TABLE `invoice_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_tests`
--
ALTER TABLE `lab_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `password_change_requests`
--
ALTER TABLE `password_change_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `patient_schedules`
--
ALTER TABLE `patient_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `patient_tests`
--
ALTER TABLE `patient_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `test_packages`
--
ALTER TABLE `test_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_results`
--
ALTER TABLE `test_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_selection_history`
--
ALTER TABLE `test_selection_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `walkin_patients`
--
ALTER TABLE `walkin_patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Constraints for table `employee_lab_tests`
--
ALTER TABLE `employee_lab_tests`
  ADD CONSTRAINT `employee_lab_tests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `employee_lab_tests_ibfk_2` FOREIGN KEY (`lab_test_id`) REFERENCES `lab_tests` (`id`);

--
-- Constraints for table `employee_tests`
--
ALTER TABLE `employee_tests`
  ADD CONSTRAINT `employee_tests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `employee_tests_ibfk_2` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`),
  ADD CONSTRAINT `employee_tests_ibfk_3` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `invoice_details`
--
ALTER TABLE `invoice_details`
  ADD CONSTRAINT `invoice_details_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
  ADD CONSTRAINT `invoice_details_ibfk_2` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`);

--
-- Constraints for table `package_tests`
--
ALTER TABLE `package_tests`
  ADD CONSTRAINT `package_tests_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `test_packages` (`id`),
  ADD CONSTRAINT `package_tests_ibfk_2` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`);

--
-- Constraints for table `password_change_requests`
--
ALTER TABLE `password_change_requests`
  ADD CONSTRAINT `password_change_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Constraints for table `patient_schedules`
--
ALTER TABLE `patient_schedules`
  ADD CONSTRAINT `patient_schedules_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`);

--
-- Constraints for table `test_results`
--
ALTER TABLE `test_results`
  ADD CONSTRAINT `test_results_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `test_results_ibfk_2` FOREIGN KEY (`lab_test_id`) REFERENCES `lab_tests` (`id`),
  ADD CONSTRAINT `test_results_ibfk_3` FOREIGN KEY (`entered_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `test_results_ibfk_4` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `test_selection_history`
--
ALTER TABLE `test_selection_history`
  ADD CONSTRAINT `test_selection_history_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`),
  ADD CONSTRAINT `test_selection_history_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
