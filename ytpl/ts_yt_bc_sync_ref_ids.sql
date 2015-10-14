-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 07, 2015 at 04:54 PM
-- Server version: 5.5.44-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `thescene`
--

-- --------------------------------------------------------

--
-- Table structure for table `ts_yt_bc_sync_ref_ids`
--

DROP TABLE IF EXISTS `ts_yt_bc_sync_ref_ids`;
CREATE TABLE IF NOT EXISTS `ts_yt_bc_sync_ref_ids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `yt_pl_id` varchar(255) DEFAULT '0',
  `yt_id` varchar(255) NOT NULL,
  `bc_id` varchar(255) NOT NULL,
  `failed_yt_id` varchar(255) NOT NULL,
  `folder` enum('test','vogue','cnt') NOT NULL DEFAULT 'test',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `folder_move` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
