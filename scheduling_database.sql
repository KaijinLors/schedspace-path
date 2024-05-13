-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2024 at 11:11 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `scheduling_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `smanagers`
--

CREATE TABLE `smanagers` (
  `Id` int(11) NOT NULL,
  `Mname` varchar(255) NOT NULL,
  `Memail` varchar(255) NOT NULL,
  `Mpass` varchar(255) NOT NULL,
  `CompanyN` varchar(255) NOT NULL,
  `Uc` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sproject`
--

CREATE TABLE `sproject` (
  `Id` int(11) NOT NULL,
  `Projectn` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Priority` varchar(255) NOT NULL,
  `Status` varchar(255) NOT NULL,
  `Due` varchar(255) NOT NULL,
  `CompanyN` varchar(255) NOT NULL,
  `Uc` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stask`
--

CREATE TABLE `stask` (
  `Id` int(11) NOT NULL,
  `Taskn` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Priority` varchar(255) NOT NULL,
  `Status` varchar(255) NOT NULL,
  `Critical` varchar(255) NOT NULL,
  `Due` varchar(255) NOT NULL,
  `Userid` varchar(255) NOT NULL,
  `Projectid` varchar(255) NOT NULL,
  `Prerequisite` varchar(255) NOT NULL,
  `EST` varchar(255) NOT NULL,
  `EFT` varchar(255) NOT NULL,
  `LST` varchar(255) NOT NULL,
  `LFT` varchar(255) NOT NULL,
  `CompanyN` varchar(255) NOT NULL,
  `Uc` varchar(255) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `startDate` date DEFAULT NULL,
  `expectedDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sworkers`
--

CREATE TABLE `sworkers` (
  `Id` int(11) NOT NULL,
  `Sname` varchar(100) NOT NULL,
  `Semail` varchar(100) NOT NULL,
  `Spass` varchar(255) NOT NULL,
  `code` int(255) DEFAULT NULL,
  `TotalT` int(255) NOT NULL,
  `FinisihedT` int(255) NOT NULL,
  `CompanyN` varchar(255) NOT NULL,
  `Uc` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `smanagers`
--
ALTER TABLE `smanagers`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `sproject`
--
ALTER TABLE `sproject`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `stask`
--
ALTER TABLE `stask`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `sworkers`
--
ALTER TABLE `sworkers`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `smanagers`
--
ALTER TABLE `smanagers`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sproject`
--
ALTER TABLE `sproject`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `stask`
--
ALTER TABLE `stask`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2464;

--
-- AUTO_INCREMENT for table `sworkers`
--
ALTER TABLE `sworkers`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
