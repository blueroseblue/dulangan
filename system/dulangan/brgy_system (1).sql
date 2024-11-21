-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 13, 2024 at 05:57 AM
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
-- Database: `brgy_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `contact_no` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `register_date` date NOT NULL DEFAULT current_timestamp(),
  `is_accepted` int(11) NOT NULL,
  `valid_id` varchar(255) NOT NULL,
  `profile` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_id`, `fullname`, `username`, `contact_no`, `password`, `register_date`, `is_accepted`, `valid_id`, `profile`) VALUES
(6, 'Marjorie Alcazar', 'marj@yahoo.com', '9515868083', '$2y$10$Cvxr6VqrOrDnbnL2VXezN.6E9blIPrJxj9JGqwsZQ9ZhxJ4tOUzaO', '2024-11-07', 1, 'national-ID.png', '438051637_1124924268551940_4291776171965875940_n.jpg'),
(7, 'Lloyd Allan Magboo', 'lloyd@yahoo.com', '9515868083', '$2y$10$3ugiI76Eg6dfVJM15w7r2u1iDaiEPkff1S6ZU8fFblPsSi5bbWSwS', '2024-11-07', 0, 'national-ID.png', 'uploads/profile/default_profile.png'),
(8, 'Jhello Sawali', 'jhello@yahoo.com', '9515868083', '$2y$10$ZnHqKtS7YW7hK5DnmFBGLOORxQtUllnzc9VzPfRcciiRBX0a1qq4q', '2024-11-07', 0, 'national-ID.png', 'uploads/profile/default_profile.png'),
(9, 'Aryan Joy Del Amor', 'ayan@gmail.com', '9515868083', '$2y$10$8nDCnxmbGFJG/61CmTxYsup/BhtgmYZOSxg8Goct7UkwG1M.m6vwO', '2024-11-07', 0, 'national-ID.png', 'uploads/profile/default_profile.png'),
(11, 'Dennis', 'dennis', '9559253567', '$2y$10$kbTuguhyiUEFUg9294Ec.OXAONpA9IbVLTigv/iv4ayOgln3FBNMy', '2024-11-10', 1, 'national-ID.png', 'uploads/profile/default_profile.png'),
(12, 'Kris Lawrence d. de VI', 'KRIS LAWRENCE', '9065719288', '$2y$10$8wAvok.twLP1oMDcLVr7He3uVutsmJzKlaY/S5oK60kOdT4nbo4EK', '2024-11-10', 1, 'national-ID.png', 'uploads/profile/default_profile.png'),
(15, 'marian tracie paran', 'marian', '9464368714', '$2y$10$Flo8JpJDHUNZScCDdYHVUOd9H5wdccPAyTsqUN4XRYS/ydGHwmS7W', '2024-11-12', 1, 'Screenshot 2024-10-09 122232.png', 'ph.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1=Admin,2=Brgy.official'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `username`, `password`, `type`) VALUES
(1, 'admin', 'admin', '$2y$10$h.grghQtjnaZ55y6Zb604.0lMEOhHXjFRKj7Y/EBCihyUY6KnSbbq', 1),
(39, 'lloyd', 'lloyd', '$2y$10$EG2aF8k60s6y/xKjlYOv1uvltpx3Q5ex4ggl5.OM8A/lw34vtPYQ.', 2),
(40, 'lloyd s allan', 'la', '$2y$10$5ODIgWR8jFkYMUzhqHOfZeCF2LFtl.7mcpoE4oVq9jyE3VdorioE2', 2),
(41, 'mico castor', 'mico', '$2y$10$PqGJniRBrm31sWV0TcVtMO70ozEk8jRzLqnYBvqMzxiuXy7B9hxO2', 2);

-- --------------------------------------------------------

--
-- Table structure for table `clearance_req`
--

CREATE TABLE `clearance_req` (
  `request_id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `purok` varchar(255) NOT NULL,
  `document` varchar(255) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `payment_type` varchar(255) NOT NULL,
  `payment_receipt` varchar(255) NOT NULL,
  `request_date` date NOT NULL DEFAULT current_timestamp(),
  `req_status` int(11) NOT NULL,
  `pickup_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clearance_req`
--

INSERT INTO `clearance_req` (`request_id`, `resident_id`, `fullname`, `age`, `status`, `purok`, `document`, `purpose`, `price`, `payment_type`, `payment_receipt`, `request_date`, `req_status`, `pickup_status`) VALUES
(37, 6, 'Marjorie Alcazar', 22, 'single', '2', 'business_permit', 'fiesta', 130, 'walkin', 'none', '2024-11-07', 0, 0),
(38, 6, 'Marjorie Alcazar', 22, 'single', '5', 'relationship', 'financial', 80, 'online', '672cce13442bb.jpg', '2024-11-07', 0, 0),
(39, 6, 'Marjorie Alcazar', 22, 'single', '5', 'residency', 'loan', 80, 'walkin', 'none', '2024-11-07', 1, 0),
(40, 6, 'Marjorie Alcazar', 22, 'single', '4', 'certification', 'loan\r\n', 0, 'walkin', 'none', '2024-11-07', 1, 1),
(44, 6, 'Marjorie Alcazar', 22, 'single', '1', 'indigency', 'ftjsp', 0, 'walkin', 'none', '2024-11-11', 0, 0),
(45, 6, 'Marjorie Alcazar', 22, 'single', '5', 'certification', 'financial', 80, 'walkin', 'none', '2024-11-11', 0, 0),
(46, 15, 'marian tracie paran', 24, 'single', '1', 'indigency', 'educational_assistance', 0, 'walkin', 'none', '2024-11-12', 2, 0),
(47, 6, 'Marjorie Alcazar', 22, 'single', '3B', 'business_clearance', 'fiesta', 130, 'walkin', 'none', '2024-11-13', 0, 0),
(55, 15, 'marian tracie paran', 45, 'married', '4', 'business_permit', 'asgsebe', 130, 'walkin', 'none', '2024-11-13', 0, 0),
(56, 15, 'marian tracie paran', 34, 'single', '4', 'business_permit', 'ewfgerbreb', 130, 'walkin', 'none', '2024-11-13', 0, 0),
(57, 15, 'marian tracie paran', 34, 'single', '3B', 'business_clearance', 'bilyar', 130, 'walkin', 'none', '2024-11-13', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

CREATE TABLE `residents` (
  `resident_id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `purok` varchar(255) NOT NULL,
  `years` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`resident_id`, `fullname`, `age`, `purok`, `years`) VALUES
(9, 'Lloyd Allan Magboo', 21, '2', '2003'),
(10, 'Marjorie Alcazar', 22, '1', '2013'),
(11, 'Jhello Sawali', 21, '3', '2021'),
(12, 'Aryan Joy Del Amor', 21, '3', '2003'),
(13, 'Kimberly Falcon', 21, '4', '2019'),
(14, 'Edelmar Anuran', 20, '1', '2015'),
(15, 'Lisa Manoban', 27, '1', '2009'),
(22, 'Jennie Kim', 28, '2', '2000'),
(23, 'Jisoo Kim', 29, '2', '2005'),
(24, 'Noriel Razon', 21, '2', '2015'),
(25, 'Jhoanna Robles', 20, '3B', '2009');

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `resource_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_borrowed` int(11) NOT NULL,
  `on_borrow` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `hasTextbox` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`resource_id`, `name`, `is_borrowed`, `on_borrow`, `quantity`, `hasTextbox`) VALUES
(1, 'Tent', 0, 0, 4, 0),
(2, 'Chainsaw', 0, 0, 2, 0),
(3, 'Grass Cutter', 0, 0, 4, 0),
(4, 'Response Vehicle', 0, 0, 1, 0),
(5, 'Ambulance', 0, 0, 1, 0),
(6, 'Chair', 0, 0, 170, 0),
(7, 'Sphygmomanometer', 0, 0, 1, 0),
(8, 'Nebulizer', 0, 0, 3, 0),
(9, 'Oxygen Tank', 0, 0, 5, 0),
(10, 'Ladder', 0, 0, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `resource_req`
--

CREATE TABLE `resource_req` (
  `request_id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `purok` varchar(255) NOT NULL,
  `resource` varchar(255) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `request_quantity` int(11) NOT NULL,
  `return_date` date NOT NULL,
  `request_date` date NOT NULL DEFAULT current_timestamp(),
  `req_status` int(11) NOT NULL,
  `is_returned` int(11) NOT NULL,
  `pickup_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resource_req`
--

INSERT INTO `resource_req` (`request_id`, `resident_id`, `resource_id`, `fullname`, `age`, `status`, `purok`, `resource`, `purpose`, `request_quantity`, `return_date`, `request_date`, `req_status`, `is_returned`, `pickup_status`) VALUES
(24, 6, 9, 'Marjorie Alcazar', 22, '', '3B', 'Oxygen Tank', 'fiesta', 1, '2024-11-25', '2024-11-07', 0, 0, 0),
(25, 6, 1, 'Marjorie Alcazar', 22, '', '4', 'Tent', 'birthday', 2, '2024-11-22', '2024-11-07', 2, 0, 0),
(26, 6, 5, 'Marjorie Alcazar', 22, '', '6', 'Ambulance', 'emergency', 1, '2024-11-22', '2024-11-07', 1, 2, 1),
(29, 6, 8, 'Marjorie Alcazar', 21, '', '4', 'Nebulizer', 'asthma', 1, '2024-11-14', '2024-11-13', 1, 2, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `clearance_req`
--
ALTER TABLE `clearance_req`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `residents`
--
ALTER TABLE `residents`
  ADD PRIMARY KEY (`resident_id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`resource_id`);

--
-- Indexes for table `resource_req`
--
ALTER TABLE `resource_req`
  ADD PRIMARY KEY (`request_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `clearance_req`
--
ALTER TABLE `clearance_req`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `resident_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `resource_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `resource_req`
--
ALTER TABLE `resource_req`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
