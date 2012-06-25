-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 25, 2012 at 01:33 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `SolasMatch`
--

-- --------------------------------------------------------

--
-- Table structure for table `archived_task`
--

CREATE TABLE IF NOT EXISTS `archived_task` (
  `archived_task_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) NOT NULL,
  `organisation_id` int(10) unsigned NOT NULL,
  `title` text NOT NULL,
  `word_count` int(10) unsigned DEFAULT NULL,
  `source_id` int(10) unsigned DEFAULT NULL COMMENT 'foreign key from the `language` table',
  `target_id` int(10) unsigned DEFAULT NULL COMMENT 'foreign key from the `language` table',
  `created_time` datetime NOT NULL,
  `archived_time` datetime NOT NULL,
  PRIMARY KEY (`archived_task_id`),
  KEY `source` (`source_id`),
  KEY `target` (`target_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `archived_task`
--

INSERT INTO `archived_task` (`archived_task_id`, `task_id`, `organisation_id`, `title`, `word_count`, `source_id`, `target_id`, `created_time`, `archived_time`) VALUES
(4, 10, 6, 'Sample\r\nfile\r\nfor\r\ntesting\r\npurposes', NULL, 7, 8, '2012-06-18 15:52:24', '2012-06-21 16:01:36'),
(5, 14, 6, 'A\r\nfully\r\nworking\r\nsample', 75, 7, 10, '2012-06-22 10:51:44', '2012-06-22 10:55:15'),
(6, 18, 6, 'Sample\r\nFile\r\n-\r\nTest\r\nOnly', NULL, NULL, NULL, '2012-06-25 09:47:06', '2012-06-25 09:49:02'),
(7, 17, 6, 'File\r\nTo Test', NULL, NULL, NULL, '2012-06-25 09:46:44', '2012-06-25 09:49:20'),
(8, 13, 6, 'sample1.txt', NULL, NULL, NULL, '2012-06-21 16:52:42', '2012-06-25 09:49:37');

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE IF NOT EXISTS `badges` (
  `badge_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`badge_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `badges`
--

INSERT INTO `badges` (`badge_id`, `title`, `description`) VALUES
(3, 'Profile-Filler', 'Filled in required info for user profile.'),
(4, 'Registered', 'Successfully set up an account'),
(5, 'Native-Language', 'Filled in your native language on your user profile.');

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE IF NOT EXISTS `language` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL COMMENT '"en", for example',
  `en_name` varchar(255) NOT NULL COMMENT '"English", for example',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`id`, `code`, `en_name`) VALUES
(7, '', 'English'),
(8, '', 'French'),
(9, '', 'Arabic'),
(10, '', 'Spanish');

-- --------------------------------------------------------

--
-- Table structure for table `old_task_file`
--

CREATE TABLE IF NOT EXISTS `old_task_file` (
  `task_id` bigint(20) unsigned NOT NULL,
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` text NOT NULL,
  `filename` text NOT NULL,
  `content_type` varchar(255) NOT NULL COMMENT 'Mime type',
  `user_id` int(11) DEFAULT NULL COMMENT 'Can be null while users table is empty! Remove this option once logins working',
  `upload_time` datetime NOT NULL,
  PRIMARY KEY (`task_id`,`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `organisation`
--

CREATE TABLE IF NOT EXISTS `organisation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `home_page` text NOT NULL,
  `biography` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `organisation`
--

INSERT INTO `organisation` (`id`, `name`, `home_page`, `biography`) VALUES
(6, 'test_org', 'http://127.0.0.1/SOLAS-Match/', 'A sample organisation for testing purposes');

-- --------------------------------------------------------

--
-- Table structure for table `organisation_member`
--

CREATE TABLE IF NOT EXISTS `organisation_member` (
  `user_id` int(10) unsigned NOT NULL,
  `organisation_id` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `organisation_member`
--

INSERT INTO `organisation_member` (`user_id`, `organisation_id`, `created`) VALUES
(24, 6, '2012-06-18 14:20:15');

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(50) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`tag_id`, `label`) VALUES
(3, 'test'),
(4, 'hyphen-test'),
(5, 'the-rosetta-foundation'),
(6, 'quotes');

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE IF NOT EXISTS `task` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `organisation_id` int(10) unsigned NOT NULL,
  `title` text NOT NULL,
  `word_count` int(10) unsigned DEFAULT NULL,
  `source_id` int(10) unsigned DEFAULT NULL COMMENT 'foreign key from the `language` table',
  `target_id` int(10) unsigned DEFAULT NULL COMMENT 'foreign key from the `language` table',
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `source` (`source_id`),
  KEY `target` (`target_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`id`, `organisation_id`, `title`, `word_count`, `source_id`, `target_id`, `created_time`) VALUES
(16, 6, 'Rosetta\r\nFoundation\r\n-\r\nTest\r\nFile', NULL, NULL, NULL, '2012-06-25 09:46:01'),
(15, 6, 'Another\r\ntest\r\nfile', NULL, NULL, NULL, '2012-06-25 09:45:40'),
(11, 6, 'This\r\nis a\r\nsecond\r\ntest\r\nfile', NULL, 7, 9, '2012-06-21 14:11:42'),
(12, 6, 'sample1.txt', NULL, NULL, NULL, '2012-06-21 16:51:58');

-- --------------------------------------------------------

--
-- Table structure for table `task_claim`
--

CREATE TABLE IF NOT EXISTS `task_claim` (
  `claim_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `claimed_time` datetime NOT NULL,
  PRIMARY KEY (`claim_id`),
  KEY `task_user` (`task_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `task_claim`
--

INSERT INTO `task_claim` (`claim_id`, `task_id`, `user_id`, `claimed_time`) VALUES
(8, 10, 24, '2012-06-21 14:13:38'),
(9, 14, 25, '2012-06-22 10:54:34'),
(10, 11, 24, '2012-06-22 14:29:24'),
(11, 13, 24, '2012-06-22 14:37:05'),
(12, 18, 24, '2012-06-25 09:47:57'),
(13, 17, 24, '2012-06-25 09:48:11');

-- --------------------------------------------------------

--
-- Table structure for table `task_file_version`
--

CREATE TABLE IF NOT EXISTS `task_file_version` (
  `task_id` bigint(20) NOT NULL,
  `version_id` int(11) NOT NULL COMMENT 'Gets incremented within the code',
  `filename` text NOT NULL,
  `content_type` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'Null while we don''t have logging in',
  `upload_time` datetime NOT NULL,
  KEY `task_id` (`task_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `task_file_version`
--

INSERT INTO `task_file_version` (`task_id`, `version_id`, `filename`, `content_type`, `user_id`, `upload_time`) VALUES
(8, 0, 'sample1.txt', 'text/plain', NULL, '2012-06-18 15:36:01'),
(9, 0, 'sample1.txt', 'text/plain', NULL, '2012-06-18 15:51:28'),
(10, 0, 'sample1.txt', 'text/plain', NULL, '2012-06-18 15:52:24'),
(11, 0, 'sample1.txt', 'text/plain', NULL, '2012-06-21 14:11:42'),
(10, 1, 'sample1.txt', 'text/plain', NULL, '2012-06-21 14:14:32'),
(10, 2, 'sample1.txt', 'text/plain', NULL, '2012-06-21 14:21:36'),
(13, 0, 'sample1.txt', 'text/plain', NULL, '2012-06-21 16:52:42'),
(14, 0, 'sample1.txt', 'text/plain', 24, '2012-06-22 10:51:44'),
(14, 1, 'sample1.txt', 'text/plain', 24, '2012-06-22 10:54:49'),
(15, 0, 'sample1.txt', 'text/plain', 24, '2012-06-25 09:45:40'),
(16, 0, 'sample1.txt', 'text/plain', 24, '2012-06-25 09:46:01'),
(17, 0, 'sample1.txt', 'text/plain', 24, '2012-06-25 09:46:44'),
(18, 0, 'sample1.txt', 'text/plain', 24, '2012-06-25 09:47:06'),
(18, 1, 'sample1.txt', 'text/plain', 24, '2012-06-25 09:48:47'),
(17, 1, 'sample1.txt', 'text/plain', 24, '2012-06-25 09:49:13'),
(13, 1, 'sample1.txt', 'text/plain', 24, '2012-06-25 09:49:33');

-- --------------------------------------------------------

--
-- Table structure for table `task_file_version_download`
--

CREATE TABLE IF NOT EXISTS `task_file_version_download` (
  `task_id` bigint(20) unsigned NOT NULL,
  `file_id` int(10) unsigned NOT NULL,
  `version_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `time_downloaded` datetime NOT NULL,
  KEY `task_id` (`task_id`,`file_id`,`version_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `task_file_version_download`
--

INSERT INTO `task_file_version_download` (`task_id`, `file_id`, `version_id`, `user_id`, `time_downloaded`) VALUES
(10, 0, 0, NULL, '2012-06-21 14:12:47'),
(10, 0, 2, NULL, '2012-06-21 16:01:24'),
(14, 0, 0, NULL, '2012-06-22 10:52:45'),
(14, 0, 1, NULL, '2012-06-22 10:54:58'),
(11, 0, 0, NULL, '2012-06-22 14:29:15'),
(13, 0, 0, NULL, '2012-06-22 14:37:03'),
(18, 0, 0, NULL, '2012-06-25 09:47:54'),
(17, 0, 0, NULL, '2012-06-25 09:48:08'),
(17, 0, 1, NULL, '2012-06-25 09:49:17');

-- --------------------------------------------------------

--
-- Table structure for table `task_tag`
--

CREATE TABLE IF NOT EXISTS `task_tag` (
  `task_id` bigint(20) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `task_tag` (`task_id`,`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `task_tag`
--

INSERT INTO `task_tag` (`task_id`, `tag_id`, `created_time`) VALUES
(10, 3, '2012-06-18 14:53:11'),
(11, 3, '2012-06-21 13:12:28'),
(11, 4, '2012-06-21 13:12:28'),
(14, 3, '2012-06-22 09:52:32'),
(14, 5, '2012-06-22 09:52:32'),
(14, 6, '2012-06-22 09:52:32'),
(16, 3, '2012-06-25 08:46:24'),
(17, 3, '2012-06-25 08:47:00'),
(18, 3, '2012-06-25 08:47:20');

-- --------------------------------------------------------

--
-- Table structure for table `translator`
--

CREATE TABLE IF NOT EXISTS `translator` (
  `user_id` int(11) NOT NULL,
  `role_added` datetime NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `display_name` varchar(128) DEFAULT NULL,
  `email` varchar(256) NOT NULL,
  `password` char(128) NOT NULL,
  `biography` text,
  `native_language` varchar(256) DEFAULT NULL,
  `nonce` int(11) unsigned NOT NULL,
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `display_name`, `email`, `password`, `biography`, `native_language`, `nonce`, `created_time`) VALUES
(24, 'Dave', 'spaceindaver0@gmail.com', 'cb02cd5988d5b3766f4fbd3c821c704e75d8721c1cbc396761a36e7f644260eed114c17c67fe5ab16430c9cbae7f598501da5aa817242347f2c5ba4906ea69a2', 'I am a programmer.', 'English', 1578591369, '2012-06-15 09:44:19'),
(25, 'Paddy', '', '', NULL, '', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_badges`
--

CREATE TABLE IF NOT EXISTS `user_badges` (
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`badge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_badges`
--

INSERT INTO `user_badges` (`user_id`, `badge_id`) VALUES
(24, 3),
(24, 4);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
