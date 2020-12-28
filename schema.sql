-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 28, 2020 at 11:51 AM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `score_4`
--

DELIMITER $$
--
-- Procedures: Αρχικοποιω την board μέσο της replace με την board_empty
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `clean_board` ()  BEGIN
		REPLACE INTO `board` SELECT * FROM `board_empty`;
	END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Φτιάχνω τον board
--

CREATE TABLE `board` (
  `x` tinyint(1) NOT NULL,
  `y` tinyint(1) NOT NULL,
  `color` enum('Y','R') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `board`
--

INSERT INTO `board` (`x`, `y`, `color`) VALUES
(1, 1, 'R'),
(1, 2, NULL),
(1, 3, NULL),
(1, 4, 'R'),
(1, 5, 'Y'),
(1, 6, NULL),
(1, 7, NULL),
(2, 1, NULL),
(2, 2, NULL),
(2, 3, NULL),
(2, 4, NULL),
(2, 5, NULL),
(2, 6, NULL),
(2, 7, NULL),
(3, 1, NULL),
(3, 2, NULL),
(3, 3, NULL),
(3, 4, NULL),
(3, 5, NULL),
(3, 6, NULL),
(3, 7, NULL),
(4, 1, NULL),
(4, 2, NULL),
(4, 3, NULL),
(4, 4, NULL),
(4, 5, NULL),
(4, 6, NULL),
(4, 7, NULL),
(5, 1, NULL),
(5, 2, NULL),
(5, 3, NULL),
(5, 4, NULL),
(5, 5, NULL),
(5, 6, NULL),
(5, 7, NULL),
(6, 1, NULL),
(6, 2, NULL),
(6, 3, NULL),
(6, 4, NULL),
(6, 5, NULL),
(6, 6, NULL),
(6, 7, NULL);

-- --------------------------------------------------------

--
-- Φτιάχνω τον board_empty
--

CREATE TABLE `board_empty` (
  `x` tinyint(1) NOT NULL,
  `y` tinyint(1) NOT NULL,
  `color` enum('Y','R') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Γεμιζω την board_empty με τα αρχικα δεδομενα
--

INSERT INTO `board_empty` (`x`, `y`, `color`) VALUES
(1, 1, NULL),
(1, 2, NULL),
(1, 3, NULL),
(1, 4, NULL),
(1, 5, NULL),
(1, 6, NULL),
(1, 7, NULL),
(2, 1, NULL),
(2, 2, NULL),
(2, 3, NULL),
(2, 4, NULL),
(2, 5, NULL),
(2, 6, NULL),
(2, 7, NULL),
(3, 1, NULL),
(3, 2, NULL),
(3, 3, NULL),
(3, 4, NULL),
(3, 5, NULL),
(3, 6, NULL),
(3, 7, NULL),
(4, 1, NULL),
(4, 2, NULL),
(4, 3, NULL),
(4, 4, NULL),
(4, 5, NULL),
(4, 6, NULL),
(4, 7, NULL),
(5, 1, NULL),
(5, 2, NULL),
(5, 3, NULL),
(5, 4, NULL),
(5, 5, NULL),
(5, 6, NULL),
(5, 7, NULL),
(6, 1, NULL),
(6, 2, NULL),
(6, 3, NULL),
(6, 4, NULL),
(6, 5, NULL),
(6, 6, NULL),
(6, 7, NULL);

-- --------------------------------------------------------

--
-- Φτιάχνω τον game_status
--

CREATE TABLE `game_status` (
  `status` enum('not active','initialized','started','ended','aborded') NOT NULL DEFAULT 'not active',
  `color_turn` enum('Y','R') DEFAULT NULL,
  `result` enum('Y','R','D') DEFAULT NULL,
  `last_change` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `game_status`
--

INSERT INTO `game_status` (`status`, `color_turn`, `result`, `last_change`) VALUES
('not active', NULL, NULL, NULL);

--
-- Triggers: Καθε φορα που γινετε update στην `game_status` βαζει στο last_change την ημερομηνια εκεινης της στιγμης
--
DELIMITER $$
CREATE TRIGGER `game_status_update` BEFORE UPDATE ON `game_status` FOR EACH ROW BEGIN
		SET NEW.last_change=NOW();
	END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Φτιάχνω τον πίνακα players
--

CREATE TABLE `players` (
  `username` varchar(50) DEFAULT NULL,
  `color_picked` enum('Y','R') NOT NULL,
  `token` varchar(100) NOT NULL,
  `last_action` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`username`, `color_picked`, `token`, `last_action`) VALUES
(NULL, 'Y', '', NULL),
(NULL, 'R', '', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `board`
--
ALTER TABLE `board`
  ADD PRIMARY KEY (`x`,`y`);

--
-- Indexes for table `board_empty`
--
ALTER TABLE `board_empty`
  ADD PRIMARY KEY (`x`,`y`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`color_picked`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
