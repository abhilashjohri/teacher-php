-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2024 at 01:57 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `teachers_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `resume` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `role` enum('Admin','Teacher') NOT NULL DEFAULT 'Teacher'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `name`, `email`, `password`, `resume`, `image`, `created_at`, `updated_at`, `status`, `role`) VALUES
(1, 'Priyansh Soni 2', 'fdfdf@gmail.com', '$2y$10$RCri8ewCYTYM4KDSgLOMveehyJ05HVT8x/z31nuYjq5tEVQL.ufFm', '669e593af30d58.86860834.pdf', '669e593af30e41.81133310.jpg', '2024-07-22 13:06:03', '2024-07-22 13:06:03', 'Active', 'Teacher'),
(5, 'admin', 'admin@gmail.com', '$2y$10$8zdzef4DFr1MCP17h/FMwOurAGe0MRDipz.difuDLxn0P8xpZWP0m', '669f40783f0284.86284349.pdf', '669f40783f03a1.62308717.jpg', '2024-07-23 05:32:40', '2024-07-23 05:32:40', 'Active', 'Admin'),
(6, 'test 2121', 'test@test.com', '$2y$10$Lrm1cEdWTaU3/qakhuyedOGxhAIlmsXC/PfbUPsAqBf4GhiqbXx5u', '669f43fb9259e7.28244912.pdf', '669f43fb925af7.46244868.jpg', '2024-07-23 05:47:39', '2024-07-23 05:47:39', 'Active', 'Teacher'),
(8, 'adferwerwr', 'adferwerwr@ghhhbb.vv', '$2y$10$wrT8m.CYGD2Xse7CHOUk4e8814/BS9CPz5A7KYGOLX6MLNxlF/LJS', '', '', '2024-07-23 10:37:56', '2024-07-23 10:37:56', 'Active', 'Teacher'),
(10, 'test', 'test32@test.com', '$2y$10$P5NYG3hK3PvS47cEUdizS.v.skQj6Oayg9z/CK1bg23n.Se9EQ.0K', '669f940cc78538.26378558.pdf', '669f940cc7bb02.30874923.jpeg', '2024-07-23 11:29:16', '2024-07-23 11:29:16', 'Active', 'Teacher');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
