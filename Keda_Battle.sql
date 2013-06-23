-- phpMyAdmin SQL Dump
-- version 3.5.8
-- http://www.phpmyadmin.net
--
-- Host: sql4.nano.lv
-- Generation Time: Jun 23, 2013 at 04:48 PM
-- Server version: 5.5.30-cll
-- PHP Version: 5.3.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `baumuin_battle`
--

-- --------------------------------------------------------

--
-- Table structure for table `geo`
--

CREATE TABLE IF NOT EXISTS `geo` (
  `lng` float NOT NULL,
  `lat` float NOT NULL,
  `img` varchar(300) NOT NULL,
  `album` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`img`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE IF NOT EXISTS `ratings` (
  `img` varchar(300) NOT NULL,
  `votes` int(11) NOT NULL DEFAULT '0',
  `views` int(11) DEFAULT NULL,
  `album` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `iso` int(11) DEFAULT NULL,
  `fstop` float DEFAULT NULL,
  `exposure` float DEFAULT NULL,
  `focallength` float DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`img`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `tag` varchar(50) CHARACTER SET utf8 NOT NULL,
  `img` varchar(300) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `img` (`img`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32379 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
