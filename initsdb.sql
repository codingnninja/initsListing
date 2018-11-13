-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 13, 2018 at 06:27 AM
-- Server version: 5.7.19
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `initsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_sessions`
--

DROP TABLE IF EXISTS `admin_sessions`;
CREATE TABLE IF NOT EXISTS `admin_sessions` (
  `session_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` varchar(11) NOT NULL,
  `hash` varchar(255) NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `biz_addresses`
--

DROP TABLE IF EXISTS `biz_addresses`;
CREATE TABLE IF NOT EXISTS `biz_addresses` (
  `address_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `biz_address` varchar(120) NOT NULL,
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `biz_admins`
--

DROP TABLE IF EXISTS `biz_admins`;
CREATE TABLE IF NOT EXISTS `biz_admins` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(120) NOT NULL,
  `admin_pass` varchar(120) NOT NULL,
  `salt` varchar(120) NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `biz_analytics`
--

DROP TABLE IF EXISTS `biz_analytics`;
CREATE TABLE IF NOT EXISTS `biz_analytics` (
  `biz_id` int(11) NOT NULL AUTO_INCREMENT,
  `views` int(11) NOT NULL,
  PRIMARY KEY (`biz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `biz_categories`
--

DROP TABLE IF EXISTS `biz_categories`;
CREATE TABLE IF NOT EXISTS `biz_categories` (
  `bizcat_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(30) NOT NULL,
  PRIMARY KEY (`bizcat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `biz_cat_pivot`
--

DROP TABLE IF EXISTS `biz_cat_pivot`;
CREATE TABLE IF NOT EXISTS `biz_cat_pivot` (
  `bizcat_id` int(11) NOT NULL,
  `biz_id` int(11) NOT NULL,
  KEY `biz_id` (`biz_id`),
  KEY `bizcat_id` (`bizcat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `biz_images`
--

DROP TABLE IF EXISTS `biz_images`;
CREATE TABLE IF NOT EXISTS `biz_images` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `img_path` varchar(120) DEFAULT NULL,
  `biz_id` int(11) NOT NULL,
  PRIMARY KEY (`image_id`),
  KEY `biz_id` (`biz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `biz_listings`
--

DROP TABLE IF EXISTS `biz_listings`;
CREATE TABLE IF NOT EXISTS `biz_listings` (
  `biz_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `biz_name` varchar(20) NOT NULL,
  `biz_description` varchar(120) NOT NULL,
  `biz_email` varchar(120) NOT NULL,
  `biz_website` varchar(80) NOT NULL,
  PRIMARY KEY (`biz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `biz_phones`
--

DROP TABLE IF EXISTS `biz_phones`;
CREATE TABLE IF NOT EXISTS `biz_phones` (
  `biz_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_phone` varchar(15) NOT NULL,
  `second_phone` varchar(15) NOT NULL,
  PRIMARY KEY (`biz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `biz_analytics`
--
ALTER TABLE `biz_analytics`
  ADD CONSTRAINT `biz_analytics_ibfk_1` FOREIGN KEY (`biz_id`) REFERENCES `biz_listings` (`biz_id`) ON DELETE CASCADE;

--
-- Constraints for table `biz_cat_pivot`
--
ALTER TABLE `biz_cat_pivot`
  ADD CONSTRAINT `biz_cat_pivot_ibfk_1` FOREIGN KEY (`biz_id`) REFERENCES `biz_listings` (`biz_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `biz_cat_pivot_ibfk_2` FOREIGN KEY (`bizcat_id`) REFERENCES `biz_categories` (`bizcat_id`) ON DELETE CASCADE;

--
-- Constraints for table `biz_images`
--
ALTER TABLE `biz_images`
  ADD CONSTRAINT `biz_images_ibfk_1` FOREIGN KEY (`biz_id`) REFERENCES `biz_listings` (`biz_id`) ON DELETE CASCADE;

--
-- Constraints for table `biz_phones`
--
ALTER TABLE `biz_phones`
  ADD CONSTRAINT `biz_phones_ibfk_1` FOREIGN KEY (`biz_id`) REFERENCES `biz_listings` (`biz_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
