-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 17, 2014 at 11:47 AM
-- Server version: 5.5.37-cll
-- PHP Version: 5.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `alextr85_admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `wa_assoc`
--

CREATE TABLE IF NOT EXISTS `wa_assoc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tbl_1` int(10) unsigned NOT NULL,
  `tbl_2` int(10) unsigned NOT NULL,
  `tbl_assoc` int(10) unsigned NOT NULL,
  `pk_1` varchar(255) NOT NULL,
  `pk_2` varchar(255) NOT NULL,
  `fk_1` varchar(255) NOT NULL,
  `fk_2` varchar(255) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `wa_categories`
--

CREATE TABLE IF NOT EXISTS `wa_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `wa_cols`
--

CREATE TABLE IF NOT EXISTS `wa_cols` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('text','textarea','image','enum','fk','date','datetime','file') CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'text',
  `fk_in` int(10) unsigned DEFAULT NULL,
  `html_editor` enum('Yes','No') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No',
  `not_null` enum('Yes','No') COLLATE utf8_unicode_ci DEFAULT 'Yes',
  `default_value` text COLLATE utf8_unicode_ci,
  `id_table` int(11) NOT NULL DEFAULT '0',
  `enum_list` text COLLATE utf8_unicode_ci,
  `caption` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `read_only` enum('Yes','No') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No',
  `relevant` enum('Yes','No') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Yes',
  `visible` enum('Yes','No') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Yes',
  `order` int(11) NOT NULL DEFAULT '0',
  `image_rel_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `max_width` int(11) DEFAULT NULL,
  `max_height` int(11) DEFAULT NULL,
  `is_pk` enum('Yes','No') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No',
  PRIMARY KEY (`id`),
  KEY `id_table` (`id_table`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=210 ;

-- --------------------------------------------------------

--
-- Table structure for table `wa_tables`
--

CREATE TABLE IF NOT EXISTS `wa_tables` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `per_page` int(11) NOT NULL,
  `pk` varchar(255) NOT NULL DEFAULT 'id',
  `name_equiv_for_pk` varchar(255) NOT NULL,
  `display_fields` varchar(255) DEFAULT NULL,
  `tree` enum('Yes','No') DEFAULT 'No',
  `tree_parent` varchar(255) DEFAULT NULL,
  `order_by` varchar(255) DEFAULT NULL,
  `order_how` enum('asc','desc') NOT NULL DEFAULT 'asc',
  `custom_controller` varchar(255) DEFAULT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `visible` enum('Yes','No') NOT NULL DEFAULT 'Yes',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `wa_tabs`
--

CREATE TABLE IF NOT EXISTS `wa_tabs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL,
  `tab_id` int(10) unsigned NOT NULL,
  `parent_pk` varchar(255) NOT NULL,
  `tab_fk` varchar(255) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `multiple_upload` enum('Yes','No') DEFAULT 'No',
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `wa_users`
--

CREATE TABLE IF NOT EXISTS `wa_users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(48) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `superadmin` enum('y','n') COLLATE utf8_unicode_ci DEFAULT 'n',
  `ip_access` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `language` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'english.php',
  `session` varchar(48) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_visit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=17 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
