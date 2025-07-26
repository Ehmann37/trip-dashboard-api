-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+deb12u1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 26, 2025 at 02:31 PM
-- Server version: 10.11.11-MariaDB-0+deb12u1
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `s24103754_trip-dashboard-api`
--

-- --------------------------------------------------------

--
-- Table structure for table `bus`
--

CREATE TABLE `bus` (
  `bus_id` varchar(20) NOT NULL,
  `route_id` int(11) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `conductor_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive','in maintenance') DEFAULT 'inactive',
  `next_maintenance` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus`
--

INSERT INTO `bus` (`bus_id`, `route_id`, `driver_id`, `conductor_id`, `company_id`, `status`, `next_maintenance`) VALUES
('1', 1, 2, 9, 1, 'active', '2025-07-24'),
('2', NULL, NULL, NULL, 1, 'inactive', '2025-07-31'),
('3', NULL, NULL, NULL, 1, 'inactive', NULL),
('4', NULL, NULL, NULL, 1, 'inactive', NULL),
('5', NULL, NULL, NULL, 1, 'inactive', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bus_companies`
--

CREATE TABLE `bus_companies` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_companies`
--

INSERT INTO `bus_companies` (`company_id`, `company_name`, `created_at`) VALUES
(1, 'Ceres Liners', '2025-07-13 17:21:28'),
(2, 'CZACH COCK', '2025-07-23 15:11:32');

-- --------------------------------------------------------

--
-- Table structure for table `conductors`
--

CREATE TABLE `conductors` (
  `conductor_id` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conductors`
--

INSERT INTO `conductors` (`conductor_id`, `status`) VALUES
(7, 'inactive'),
(8, 'inactive'),
(9, 'active'),
(10, 'inactive'),
(11, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `driver_id` int(11) NOT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `full_name` varchar(50) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`driver_id`, `license_number`, `full_name`, `contact_number`, `company_id`, `status`) VALUES
(1, 'CRU-1085-1234567', 'John Doe', '9090889213', 1, 'inactive'),
(2, 'DOM-0793-2345678', 'Jason Williams', '9231083131', 1, 'active'),
(3, 'SAN-0290-3456789', 'Terry Stotts', '9237183123', 1, 'active'),
(4, 'DEV-1201-4567890', 'Bronny James', '9231723182', 1, 'inactive'),
(5, 'FER-0111-5678901', 'Bill Windows', '9231276321', 1, 'inactive');

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `route_id` int(11) NOT NULL,
  `route_name` varchar(100) DEFAULT NULL,
  `schedule_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`route_id`, `route_name`, `schedule_id`) VALUES
(1, 'CSBT to Bato via Oslob', 1),
(2, 'Bato to CSBT via Oslob', 2),
(3, 'CSBT to Bato via Barili', 3),
(4, 'Bato to CSBT via Barili', 4);

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `first_trip` time DEFAULT NULL,
  `last_trip` time DEFAULT NULL,
  `time_interval` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `first_trip`, `last_trip`, `time_interval`) VALUES
(1, '03:00:00', '19:00:00', 30),
(2, '02:30:00', '17:30:00', 30),
(3, '03:00:00', '18:30:00', 30),
(4, '02:30:00', '17:30:00', 30),
(5, '06:00:00', '14:00:00', 180),
(6, '03:00:00', '14:00:00', 180),
(7, '04:00:00', '18:30:00', 30),
(8, '04:00:00', '18:00:00', 30),
(9, '04:00:00', '16:00:00', 60),
(10, '03:00:00', '19:00:00', 20);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `trip_id` varchar(20) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `passenger_category` enum('regular','student','senior','pwd') DEFAULT NULL,
  `fare_amount` decimal(10,2) DEFAULT NULL,
  `payment_mode` varchar(50) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`trip_id`, `ticket_id`, `passenger_category`, `fare_amount`, `payment_mode`, `company_id`) VALUES
('a0itIPqa2q3tCOJZZIo', 104, 'pwd', 13.23, 'online', 1),
('a0itIPqa2q3tCOJZZIo', 105, 'senior', 13.23, 'online', 1),
('a0itIPqa2q3tCOJZZIo', 106, 'regular', 16.54, 'online', 1),
('a0itIPqa2q3tCOJZZIo', 107, 'student', 13.23, 'cash', 1),
('a0itIPqa2q3tCOJZZIo', 108, 'senior', 13.23, 'cash', 1),
('a0itIPqa2q3tCOJZZIo', 109, 'pwd', 13.23, 'cash', 1),
('a0itIPqa2q3tCOJZZIo', 110, 'student', 13.23, 'cash', 1),
('a0itIPqa2q3tCOJZZIo', 111, 'student', 13.23, 'cash', 1),
('a0itIPqa2q3tCOJZZIo', 112, 'regular', 16.54, 'cash', 1),
('dnmBGodQBpNZ0eA0MhC', 100, 'student', 13.23, 'cash', 1),
('dnmBGodQBpNZ0eA0MhC', 101, 'pwd', 13.23, 'cash', 1),
('dnmBGodQBpNZ0eA0MhC', 102, 'senior', 13.23, 'cash', 1),
('dnmBGodQBpNZ0eA0MhC', 103, 'pwd', 13.23, 'cash', 1),
('Ikqsap95tuPqMRxar4p', 113, 'student', 13.23, 'online', 1),
('Ikqsap95tuPqMRxar4p', 114, 'pwd', 13.23, 'online', 1),
('Ikqsap95tuPqMRxar4p', 115, 'regular', 16.54, 'cash', 1),
('lEm6NLypV4LWtGXhuKGQ', 95, 'student', 13.23, 'online', 1),
('lEm6NLypV4LWtGXhuKGQ', 96, 'pwd', 13.23, 'online', 1),
('lEm6NLypV4LWtGXhuKGQ', 97, 'pwd', 26.34, 'cash', 1),
('lEm6NLypV4LWtGXhuKGQ', 98, 'student', 26.34, 'cash', 1),
('lEm6NLypV4LWtGXhuKGQ', 99, 'senior', 26.34, 'cash', 1);

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `trip_id` varchar(20) NOT NULL,
  `route_id` int(11) DEFAULT NULL,
  `boarding_time` datetime DEFAULT NULL,
  `arrival_time` datetime DEFAULT NULL,
  `total_passenger` int(11) DEFAULT NULL,
  `total_revenue` int(11) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `conductor_id` int(11) DEFAULT NULL,
  `bus_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`trip_id`, `route_id`, `boarding_time`, `arrival_time`, `total_passenger`, `total_revenue`, `driver_id`, `conductor_id`, `bus_id`) VALUES
('a0itIPqa2q3tCOJZZIo', 1, '2025-07-24 15:24:24', '2025-07-24 15:27:09', 9, 126, 1, 9, '1'),
('dnmBGodQBpNZ0eA0MhC', 1, '2025-07-24 14:59:37', '2025-07-24 15:24:17', 4, 530, 1, 9, '1'),
('Ikqsap95tuPqMRxar4p', 1, '2025-07-24 16:17:18', '2025-07-24 16:31:27', 3, 76, 1, 9, '1'),
('lEm6NLypV4LWtGXhuKGQ', 1, '2025-07-24 14:43:18', '2025-07-24 14:50:03', 5, 105, 1, 9, '1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `hashed_password` varchar(255) NOT NULL,
  `company_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `token` text DEFAULT NULL,
  `role` enum('operator','conductor') NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `hashed_password`, `company_id`, `created_at`, `token`, `role`, `contact_number`) VALUES
(1, 'Czach Villarin', 'c@ceres.com', '$2a$12$26j3YjQeFpzRzjDhzvQ/huaFjT5nrn7RayhjvXqAMGwvXtHJbhxeS', 1, '2025-07-14 01:29:07', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjUsImVtYWlsIjoiZXl5eUBleGFtcGxlLmNvbSIsInJvbGUiOiJvcGVyYXRvciIsImlhdCI6MTc1Mjg2OTc0MywiZXhwIjoxNzUyOTU1OTQzfQ.-cYYYTO6gFYRu8lRN203XrYKOOanBJvTdhW6KYx0znk', 'operator', NULL),
(2, 'Emman Cañete', 'e@ceres.com', '$2a$12$26j3YjQeFpzRzjDhzvQ/huaFjT5nrn7RayhjvXqAMGwvXtHJbhxeS', 1, '2025-07-14 01:29:07', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYsImVtYWlsIjoiakBjZXJlcy5jb20iLCJyb2xlIjoib3BlcmF0b3IiLCJpYXQiOjE3NTMzMzkzNjcsImV4cCI6MTc1MzQyNTU2N30.vyQscTOSH0VRKbQsvkbEjmHos19tD_uiA-iW6IGvXbQ', 'operator', NULL),
(3, 'Ryan Romero', 'r@ceres.com', '$2a$12$26j3YjQeFpzRzjDhzvQ/huaFjT5nrn7RayhjvXqAMGwvXtHJbhxeS', 1, '2025-07-14 01:29:07', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjcsImVtYWlsIjoibWlrZUBjZXJlcy5jb20iLCJyb2xlIjoib3BlcmF0b3IiLCJpYXQiOjE3NTI4OTc2NTIsImV4cCI6MTc1Mjk4Mzg1Mn0.dU2K35cYCy0yIbOXYXoRZSMz1IRsdewiUlg-iIHJk3M', 'operator', NULL),
(4, 'Lebron James', 'l@ceres.com', '$2a$12$26j3YjQeFpzRzjDhzvQ/huaFjT5nrn7RayhjvXqAMGwvXtHJbhxeS', 1, '2025-07-16 13:48:03', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjksImVtYWlsIjoibEBjZXJlcy5jb20iLCJyb2xlIjoiY29uZHVjdG9yIiwiaWF0IjoxNzUzMDM1NjUwLCJleHAiOjE3NTMxMjE4NTB9._I8j2fEXwOzwre4KVwxWKQV5G2a8_wUwShO8GNcfj7U', 'operator', '9763167670'),
(5, 'Gian Epanto', 'g@ceres.com', '$2a$12$26j3YjQeFpzRzjDhzvQ/huaFjT5nrn7RayhjvXqAMGwvXtHJbhxeS', 1, '2025-07-16 13:48:03', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjUsImVtYWlsIjoiZ2lhbkBjZXJlcy5jb20iLCJyb2xlIjoib3BlcmF0b3IiLCJpYXQiOjE3NTMzNDEwNTIsImV4cCI6MTc1MzQyNzI1Mn0.GDggRb1oJkRLJIQ4squ4xW8FyNi3k0DE5YAPP28E3Hw', 'operator', '9459824342'),
(6, 'Gian Opone', 'fgopone@ceres.com', '$2a$12$26j3YjQeFpzRzjDhzvQ/huaFjT5nrn7RayhjvXqAMGwvXtHJbhxeS', 1, '2025-07-23 01:07:06', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYsImVtYWlsIjoiZmdvcG9uZUBjZXJlcy5jb20iLCJyb2xlIjoib3BlcmF0b3IiLCJpYXQiOjE3NTM0MTY5MzksImV4cCI6MTc1MzUwMzEzOX0.56EN0-rg760b1OEt2A4aPWuwwFhs53VVjlmVI9rbHPw', 'operator', '45646'),
(7, 'Usain Nuts', 'un@ceres.com', '$2y$10$oqNIwTWtQQE0ktV1.PxPhORxmv0LZLblqFrOiUoqO6zh9gQJB2LsW', 1, '2025-07-24 15:21:33', NULL, 'conductor', '9738946172'),
(8, 'Mike Gray', 'mg@ceres.com', '$2y$10$2wikRzU.ZJ1cMfpWopZiPOW3JNqjM/r9t9tCfV3eKJLjwRzmUC9ZC', 1, '2025-07-24 15:23:15', NULL, 'conductor', '9231873213'),
(9, 'Jenny Mason', 'jm@ceres.com', '$2y$10$FDld/kDM33G6gfM3oYJKmO1dBY2YiLswwahN/G5vKxkqbIZSl51SO', 1, '2025-07-24 15:23:51', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjksImVtYWlsIjoiam1AY2VyZXMuY29tIiwicm9sZSI6ImNvbmR1Y3RvciIsImlhdCI6MTc1MzM2NTY2NSwiZXhwIjoxNzUzNDUxODY1fQ.XmStnSMdqVXIDiEpNLZC5H0HeMyntuKpGgZRvgK8qEE', 'conductor', '9172398213'),
(10, 'Jane Doe', 'jd@ceres.com', '$2y$10$X8rF9GBiRSV8qOJNdMLtmuZXigmB8LLHvZYRjO1Z0HMDFNG82R45W', 1, '2025-07-24 15:24:31', NULL, 'conductor', '9238671261'),
(11, 'Marie Jackson', 'mj@ceres.com', '$2y$10$nswT.MlIwTJ79DnguolzzOfrmVUUgAQ.Do6TTNf6C.uTXBX02pPZe', 1, '2025-07-24 15:24:54', NULL, 'conductor', '9238712310');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bus`
--
ALTER TABLE `bus`
  ADD PRIMARY KEY (`bus_id`),
  ADD KEY `route_id` (`route_id`),
  ADD KEY `conductor_id` (`conductor_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `bus_ibfk_2` (`driver_id`);

--
-- Indexes for table `bus_companies`
--
ALTER TABLE `bus_companies`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `conductors`
--
ALTER TABLE `conductors`
  ADD PRIMARY KEY (`conductor_id`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`driver_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`route_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`trip_id`,`ticket_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`trip_id`),
  ADD KEY `trip_ibfk_1` (`bus_id`),
  ADD KEY `trip_ibfk_2` (`conductor_id`),
  ADD KEY `trip_ibfk_3` (`driver_id`),
  ADD KEY `trip_ibfk_4` (`route_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `token` (`token`) USING HASH;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `conductors`
--
ALTER TABLE `conductors`
  MODIFY `conductor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bus`
--
ALTER TABLE `bus`
  ADD CONSTRAINT `bus_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`),
  ADD CONSTRAINT `bus_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`driver_id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `bus_ibfk_3` FOREIGN KEY (`conductor_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bus_ibfk_4` FOREIGN KEY (`company_id`) REFERENCES `bus_companies` (`company_id`);

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `drivers_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `bus_companies` (`company_id`);

--
-- Constraints for table `routes`
--
ALTER TABLE `routes`
  ADD CONSTRAINT `routes_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`schedule_id`);

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`trip_id`),
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `bus_companies` (`company_id`);

--
-- Constraints for table `trips`
--
ALTER TABLE `trips`
  ADD CONSTRAINT `trip_ibfk_1` FOREIGN KEY (`bus_id`) REFERENCES `bus` (`bus_id`),
  ADD CONSTRAINT `trip_ibfk_2` FOREIGN KEY (`conductor_id`) REFERENCES `conductors` (`conductor_id`),
  ADD CONSTRAINT `trip_ibfk_3` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`driver_id`),
  ADD CONSTRAINT `trip_ibfk_4` FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
