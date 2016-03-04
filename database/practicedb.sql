-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 04, 2016 at 10:12 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `practicedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_course`
--

CREATE TABLE IF NOT EXISTS `tbl_course` (
  `courseID` int(10) NOT NULL AUTO_INCREMENT,
  `course` varchar(50) NOT NULL,
  PRIMARY KEY (`courseID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tbl_course`
--

INSERT INTO `tbl_course` (`courseID`, `course`) VALUES
(1, 'BSIT'),
(2, 'BSBA');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE IF NOT EXISTS `tbl_user` (
  `userID` int(10) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `courseID` int(10) NOT NULL,
  `dateAdded` datetime NOT NULL,
  `visible` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`userID`, `fullname`, `email`, `courseID`, `dateAdded`, `visible`) VALUES
(1, 'dued', 'dude@gmail.com', 1, '2016-03-04 08:22:00', 1),
(2, 'julius', 'juls@gmail.com', 1, '2016-03-04 08:33:46', 1),
(3, 'julius', 'juls@gmail.com', 1, '2016-03-04 08:37:16', 1),
(4, 'ewqew', 'wedawe', 2, '2016-03-04 08:37:23', 1),
(5, 'bon', 'sdadsa', 2, '2016-03-04 08:37:41', 1),
(6, 'BON', 'BON@GMAIL.COM', 2, '2016-03-04 08:52:23', 1),
(7, 'BON', 'BON@GMAIL.COM', 2, '2016-03-04 08:52:28', 1),
(8, 'BON', 'BON@GMAIL.COM', 1, '2016-03-04 08:52:31', 1),
(9, 'BON', 'BON@GMAIL.COM', 2, '2016-03-04 08:52:31', 1),
(10, 'BON', 'BON@GMAIL.COM', 2, '2016-03-04 08:52:32', 1),
(11, 'BON', 'BON@GMAIL.COM', 2, '2016-03-04 08:52:32', 1),
(12, 'BON', 'BON@GMAIL.COM', 2, '2016-03-04 08:52:32', 1),
(13, 'aa', 'aa@yahoo.com', 1, '2016-03-04 09:51:21', 1),
(14, 'dued', 'asdsadsadsadsadas', 2, '2016-03-04 09:57:14', 1),
(15, 'qweq', 'qw', 1, '2016-03-04 09:58:08', 1),
(16, 'qweq', 'qw', 2, '2016-03-04 09:59:33', 1),
(17, 'ew', 'we', 1, '2016-03-04 10:00:25', 1),
(18, 'qwe', 'we', 1, '2016-03-04 10:00:58', 1),
(19, 'er', 'er', 1, '2016-03-04 10:02:02', 1),
(20, 'wr', 'we', 1, '2016-03-04 10:02:25', 1),
(21, 'er', 'r', 2, '2016-03-04 10:02:38', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
