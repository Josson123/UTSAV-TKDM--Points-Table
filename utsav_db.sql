-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2025 at 07:40 PM
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
-- Database: `utsavtkdm`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `slno` int(2) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`slno`, `username`, `password`) VALUES
(1, 'TKDMForaneyuvadeepti', 'ThrickodithanamForane');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `Event_slno` int(3) NOT NULL COMMENT 'serial number of events',
  `Event_name` varchar(50) DEFAULT NULL COMMENT 'name of event',
  `Event_type` tinyint(1) NOT NULL COMMENT '1=Rachana,0=Avatharanam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`Event_slno`, `Event_name`, `Event_type`) VALUES
(1, 'കവിതാരചന ', 0),
(2, 'കഥാരചന ', 0),
(3, 'ഉപന്യാസം', 0),
(4, 'പത്രവാർത്താ രചന', 0),
(5, 'കൊളാഷ് ', 0),
(6, 'നോട്ടീസ് രചന ', 0),
(7, 'മുദ്രാവാക്യ രചന ', 0),
(8, 'കാർട്ടൂൺ രചന ', 0),
(9, 'വാട്ടർ കളർ ', 0),
(10, 'പോസ്റ്റർ രചന ', 0),
(11, 'ഡിജിറ്റൽ പോസ്റ്റർ', 0),
(12, 'ട്രോൾ മേക്കിങ് ', 0),
(13, 'പെൻസിൽ  ഡ്രോയിങ് ', 0),
(14, 'പ്രസംഗം മലയാളം A', 1),
(15, 'പ്രസംഗം മലയാളം B', 1),
(16, 'ലളിതഗാനം A', 1),
(17, 'ലളിതഗാനം B', 1),
(18, 'മിമിക്രി A', 1),
(19, 'മിമിക്രി B', 1),
(20, 'മോണോആക്ട്  A', 1),
(21, 'മോണോആക്ട്  B', 1),
(22, 'സംഘഗാനം ', 1),
(23, 'ഡിബേറ്റ് ', 1),
(24, 'ഫോട്ടോഗ്രഫി A', 1),
(25, 'ഫോട്ടോഗ്രഫി B', 1),
(26, 'യുവദീപ്തി ആന്തം ', 1),
(27, 'മൈം ', 1),
(28, 'മാർഗംകളി B', 0),
(29, 'കവിതാരചന ', 0),
(30, 'കഥാരചന ', 0),
(31, 'ഉപന്യാസം', 0),
(32, 'പത്രവാർത്താ രചന', 0),
(33, 'കൊളാഷ് ', 0),
(34, 'നോട്ടീസ് രചന ', 0),
(35, 'മുദ്രാവാക്യ രചന ', 0),
(36, 'കാർട്ടൂൺ രചന ', 0),
(37, 'വാട്ടർ കളർ ', 0),
(38, 'പോസ്റ്റർ രചന ', 0),
(39, 'ഡിജിറ്റൽ പോസ്റ്റർ', 0),
(40, 'ട്രോൾ മേക്കിങ് ', 0),
(41, 'പെൻസിൽ  ഡ്രോയിങ് ', 0),
(42, 'പ്രസംഗം മലയാളം A', 1),
(43, 'പ്രസംഗം മലയാളം B', 1),
(44, 'ലളിതഗാനം A', 1),
(45, 'ലളിതഗാനം B', 1),
(46, 'മിമിക്രി A', 1),
(47, 'മിമിക്രി B', 1),
(48, 'മോണോആക്ട്  A', 1),
(49, 'മോണോആക്ട്  B', 1),
(50, 'സംഘഗാനം ', 1),
(51, 'ഡിബേറ്റ് ', 1),
(52, 'ഫോട്ടോഗ്രഫി A', 1),
(53, 'ഫോട്ടോഗ്രഫി B', 1),
(54, 'യുവദീപ്തി ആന്തം ', 1),
(55, 'മൈം ', 1),
(56, 'മാർഗംകളി B', 1);

-- --------------------------------------------------------

--
-- Table structure for table `points`
--

CREATE TABLE `points` (
  `unit_slno` int(11) NOT NULL COMMENT 'primary key from table "unit"',
  `Event_slno` int(11) NOT NULL COMMENT 'primary key from table "events"',
  `Points` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `unit_slno` int(2) NOT NULL COMMENT 'sl no.of units',
  `unit_name` varchar(30) NOT NULL COMMENT 'name of unit',
  `total_points` int(4) NOT NULL COMMENT 'total points of unit'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='This is the table containing unit info';

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`unit_slno`, `unit_name`, `total_points`) VALUES
(1, 'Chanjody', 0),
(2, 'Fathimapuram', 0),
(3, 'Kodinattumkunnu', 0),
(4, 'Kunnamthanam', 0),
(5, 'Mundupalam', 0),
(6, 'Muthoor', 0),
(7, 'Nalukody', 0),
(8, 'Paippad', 0),
(9, 'TKDM Central', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`slno`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`Event_slno`);

--
-- Indexes for table `points`
--
ALTER TABLE `points`
  ADD KEY `test` (`unit_slno`),
  ADD KEY `test2` (`Event_slno`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`unit_slno`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `slno` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `Event_slno` int(3) NOT NULL AUTO_INCREMENT COMMENT 'serial number of events', AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `unit_slno` int(2) NOT NULL AUTO_INCREMENT COMMENT 'sl no.of units', AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `points`
--
ALTER TABLE `points`
  ADD CONSTRAINT `test` FOREIGN KEY (`unit_slno`) REFERENCES `units` (`unit_slno`),
  ADD CONSTRAINT `test2` FOREIGN KEY (`Event_slno`) REFERENCES `events` (`Event_slno`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
