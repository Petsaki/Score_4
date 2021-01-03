-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 01, 2021 at 04:29 PM
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
-- Procedure clean_board: Αρχικοποιω την board μέσο της replace με την board_empty
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `clean_board` ()  BEGIN
		REPLACE INTO `board` SELECT * FROM `board_empty`;
        UPDATE `players` SET username=NULL, token=NULL;
		UPDATE `game_status` SET `status`='not active', `color_turn`=NULL, `result`=NULL;
	END$$

--
-- Procedure put_piece: τοποθετώ μία μάρκα σύμφωνα με τα IN στον board και ενημερώνω το last_action και game_status ποιανού σειρά είναι
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `put_piece` (IN `y_input` TINYINT, IN `color_input` VARCHAR(50))  NO SQL
piece_placed:
    BEGIN
        DECLARE x1 INT;
        SET x1 = 1;
        REPEAT
            IF (SELECT color FROM `board` WHERE x=x1 AND y=y_input)IS NULL THEN
                UPDATE `board`
                SET color=color_input
                WHERE x=x1 AND y=y_input;
                UPDATE players SET last_action=NOW() WHERE color_picked=color_input;
                UPDATE game_status SET color_turn=IF(color_input='Y','R','Y');
                LEAVE piece_placed;
            END IF;
            SET x1 = x1 + 1;
            UNTIL x1 > 6
      END REPEAT;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Η δομή του πίνακα board
--

CREATE TABLE `board` (
  `x` tinyint(1) NOT NULL,
  `y` tinyint(1) NOT NULL,
  `color` enum('Y','R') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Τα δεδομένα που περιέχει ο board
--

INSERT INTO `board` (`x`, `y`, `color`) VALUES
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
-- Η δομή του πίνακα board_empty
--

CREATE TABLE `board_empty` (
  `x` tinyint(1) NOT NULL,
  `y` tinyint(1) NOT NULL,
  `color` enum('Y','R') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Τα δεδομένα που περιέχει ο board_empty
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
-- Η δομή του πίνακα game_status
--

CREATE TABLE `game_status` (
  `status` enum('not active','initialized','started','ended','aborded') NOT NULL DEFAULT 'not active',
  `color_turn` enum('Y','R') DEFAULT NULL,
  `result` enum('Y','R','D') DEFAULT NULL,
  `last_change` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Τα δεδομένα του game_status
--

INSERT INTO `game_status` (`status`, `color_turn`, `result`, `last_change`) VALUES
('not active', NULL, NULL, '2021-01-03 14:02:43');

--
-- Triggers `game_status`: Πριν από κάθε update του πίνακα game_status να ενημερώσει την στήλη last_change με την τωρινή ώρα
--
DELIMITER $$
CREATE TRIGGER `game_status_update` BEFORE UPDATE ON `game_status` FOR EACH ROW BEGIN
		SET NEW.last_change=NOW();
	END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Η δομή του πίνακα players
--

CREATE TABLE `players` (
  `username` varchar(50) DEFAULT NULL,
  `color_picked` enum('Y','R') NOT NULL,
  `token` varchar(100) NOT NULL,
  `last_action` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Τα δεδομένα του players
--

INSERT INTO `players` (`username`, `color_picked`, `token`, `last_action`) VALUES
(NULL, 'Y', '', '2021-01-03 13:55:59'),
(NULL, 'R', '', '2021-01-03 13:56:35');


--
-- Indexes for dumped tables
--

--
-- Τα κύρια κλειδιά του board
--
ALTER TABLE `board`
  ADD PRIMARY KEY (`x`,`y`);

--
-- Τα κύρια κλειδιά του board_empty
--
ALTER TABLE `board_empty`
  ADD PRIMARY KEY (`x`,`y`);

--
-- Τα κύρια κλειδιά του players
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`color_picked`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
