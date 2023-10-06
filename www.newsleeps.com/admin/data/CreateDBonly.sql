-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 06, 2023 at 11:29 AM
-- Server version: 5.7.41-0ubuntu0.16.04.1+esm1
-- PHP Version: 5.6.40-50+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `newsleeps`
--
CREATE DATABASE IF NOT EXISTS `newsleeps` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `newsleeps`;

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_data`
--

CREATE TABLE `affiliate_data` (
  `id` int(11) NOT NULL,
  `hotel_chain` varchar(80) NOT NULL DEFAULT '',
  `new_openings_url` varchar(255) DEFAULT NULL,
  `script_id` varchar(20) DEFAULT NULL,
  `last_verified_date` datetime DEFAULT NULL,
  `affiliate_url` varchar(255) DEFAULT NULL,
  `login` varchar(20) DEFAULT NULL,
  `pwd` varchar(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Reservations Affiliation Information';

-- --------------------------------------------------------

--
-- Table structure for table `new_hotels`
--

CREATE TABLE `new_hotels` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL DEFAULT '',
  `website` varchar(80) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `address1` varchar(100) DEFAULT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `city` varchar(40) DEFAULT NULL,
  `state_prov` varchar(40) DEFAULT NULL,
  `country` varchar(40) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `map_url` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `hotel_chain` varchar(80) DEFAULT NULL,
  `hotel_type` varchar(20) DEFAULT NULL,
  `open_date` date NOT NULL DEFAULT '0000-00-00',
  `rating` int(11) DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `last_verified_date` datetime DEFAULT NULL,
  `lat` float(10,6) DEFAULT '0.000000',
  `lng` float(10,6) DEFAULT '0.000000',
  `hotelid` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `renovations`
--

CREATE TABLE `renovations` (
  `hotelid` int(11) NOT NULL,
  `renov_desc` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `states_provinces`
--

CREATE TABLE `states_provinces` (
  `full_name` varchar(80) NOT NULL,
  `state_prov` varchar(4) NOT NULL,
  `lat` float(10,6) DEFAULT '0.000000',
  `lng` float(10,6) DEFAULT '0.000000'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `world_hotels`
--

CREATE TABLE `world_hotels` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL DEFAULT '',
  `url` varchar(255) DEFAULT NULL,
  `address1` varchar(100) DEFAULT NULL,
  `city` varchar(40) DEFAULT NULL,
  `country` varchar(40) DEFAULT NULL,
  `open_date` date NOT NULL DEFAULT '0000-00-00',
  `photo_url` varchar(255) DEFAULT NULL,
  `lat` float(10,6) DEFAULT '0.000000',
  `lng` float(10,6) DEFAULT '0.000000',
  `hotelid` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `affiliate_data`
--
ALTER TABLE `affiliate_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_chain` (`hotel_chain`);

--
-- Indexes for table `new_hotels`
--
ALTER TABLE `new_hotels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`,`city`,`state_prov`,`postal_code`);

--
-- Indexes for table `renovations`
--
ALTER TABLE `renovations`
  ADD UNIQUE KEY `hotelididx` (`hotelid`);

--
-- Indexes for table `world_hotels`
--
ALTER TABLE `world_hotels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`,`city`,`country`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `affiliate_data`
--
ALTER TABLE `affiliate_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT for table `new_hotels`
--
ALTER TABLE `new_hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=915;
--
-- AUTO_INCREMENT for table `world_hotels`
--
ALTER TABLE `world_hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=324;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
