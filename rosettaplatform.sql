-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 29, 2011 at 03:44 PM
-- Server version: 5.1.49
-- PHP Version: 5.3.3-1ubuntu9.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `rosettaplatform`
--

-- --------------------------------------------------------

--
-- Table structure for table `organisation`
--

DROP TABLE IF EXISTS `organisation`;
CREATE TABLE IF NOT EXISTS `organisation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `organisation`
--

INSERT INTO `organisation` (`id`, `name`) VALUES
(1, 'PeopleOrg'),
(2, 'TransOrg'),
(3, 'MedOrg');

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(50) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`id`, `label`) VALUES
(3, 'to-french'),
(4, 'informal'),
(5, 'to-spanish'),
(6, 'medical'),
(7, 'to-russian'),
(8, 'indesign'),
(9, 'information-technology'),
(10, 'girls'),
(11, 'young'),
(12, 'women'),
(13, 'scouts'),
(14, 'empower'),
(15, ''),
(16, 'empowering'),
(17, 'developing'),
(18, 'countries'),
(19, 'file'),
(20, 'html');

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
CREATE TABLE IF NOT EXISTS `task` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `organisation_id` int(10) unsigned NOT NULL,
  `title` text NOT NULL,
  `word_count` int(10) unsigned DEFAULT NULL,
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`id`, `organisation_id`, `title`, `word_count`, `created_time`) VALUES
(2, 1, 'Testing\r\nupload\r\nfile.', NULL, '2011-03-22 14:27:14'),
(3, 1, 'Please\r\ntranslate\r\nfor\r\nour\r\norganisation\r\n-\r\nneeds\r\nto keep\r\nits\r\ninformal\r\ntone.', NULL, '2011-03-22 14:29:02'),
(4, 1, 'Spanish\r\ndocument\r\nfor\r\ndoctors.', NULL, '2011-03-22 14:29:50'),
(5, 1, 'I\r\nwant\r\nthis\r\nfile\r\ntranslated,\r\nand\r\nI''m\r\nadding\r\na\r\nreally\r\nlong\r\ntitle\r\nto it\r\nto be\r\nreally\r\ndescriptive.\r\nThis\r\nwill\r\nhelp\r\nvolunteers.', NULL, '2011-03-22 14:30:30'),
(6, 1, 'This\r\nis a\r\nbig\r\nzip\r\nfile\r\nof lots\r\nof documents\r\nto be\r\ntranslated.\r\n3,000\r\nwords.', NULL, '2011-03-22 14:31:13'),
(7, 1, 'Expert\r\nmedical\r\ntranslator\r\nrequired\r\nfor\r\nSpanish\r\ntext.\r\nEnclosed\r\nin the\r\nzip\r\nfile\r\nare\r\nthree\r\nInDesign\r\nbrochures\r\nthat\r\nwe need\r\ntranslate.', NULL, '2011-03-22 14:33:35'),
(8, 1, 'This\r\nis Eoin\r\ntesting\r\nfrom\r\nanother\r\nnetwork\r\nPC.\r\nPlease\r\ntranslate\r\nmy IT-related\r\ndocuments\r\nfor\r\nour\r\nNGO.', NULL, '2011-03-22 14:37:21'),
(9, 1, 'Empowering Girls and Young Women in \r\nDeveloping Countries', NULL, '2011-03-22 14:42:45'),
(10, 1, 'Empowering girls and young women in \r\ndeveloping countries', NULL, '2011-03-22 14:44:21'),
(11, 1, 'Empowering girls and young women in \r\ndeveloping countries', NULL, '2011-03-22 14:45:03'),
(12, 1, 'Testing.\r\nDoes\r\nthis\r\nfile\r\nget\r\nuploaded?', NULL, '2011-03-25 14:23:00'),
(13, 1, 'Testing.\r\nDoes\r\nthis\r\nfile\r\nget\r\nuploaded?', NULL, '2011-03-25 14:23:19'),
(14, 1, 'Uploading\r\nthe\r\nfile\r\nfrom\r\nGoogle.com,\r\ntesting', NULL, '2011-03-25 16:01:54'),
(15, 1, 'Uploading\r\nthe\r\nfile\r\nfrom\r\nGoogle.com,\r\ntesting', NULL, '2011-03-25 16:11:10'),
(16, 1, 'Uploading\r\nthe\r\nfile\r\nfrom\r\nGoogle.com,\r\ntesting', NULL, '2011-03-25 16:11:18'),
(17, 1, 'Uploading\r\nthe\r\nfile\r\nfrom\r\nGoogle.com,\r\ntesting', NULL, '2011-03-25 16:11:47'),
(18, 1, 'Uploading\r\nthe\r\nfile\r\nfrom\r\nGoogle.com,\r\ntesting', NULL, '2011-03-25 16:12:03'),
(19, 1, 'Uploading\r\nthe\r\nfile\r\nfrom\r\nGoogle.com,\r\ntesting', NULL, '2011-03-25 16:13:00'),
(20, 1, 'Uploading\r\nthe\r\nfile\r\nfrom\r\nGoogle.com,\r\ntesting', NULL, '2011-03-25 16:13:26'),
(21, 1, 'Test', NULL, '2011-03-25 16:14:41'),
(22, 1, 'Uploading\r\nfile,\r\nHTML\r\nform.at', NULL, '2011-03-25 16:17:53'),
(23, 1, 'Adding\r\nnew\r\ntask,\r\nwill\r\nthe\r\nwordcount\r\nget\r\nrecorded?', 30000, '2011-03-25 16:42:48');

-- --------------------------------------------------------

--
-- Table structure for table `task_file`
--

DROP TABLE IF EXISTS `task_file`;
CREATE TABLE IF NOT EXISTS `task_file` (
  `task_id` bigint(20) unsigned NOT NULL,
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` text NOT NULL,
  `filename` text NOT NULL,
  `content_type` varchar(255) NOT NULL COMMENT 'Mime type',
  `user_id` int(11) DEFAULT NULL COMMENT 'Can be null while users table is empty! Remove this option once logins working',
  `upload_time` datetime NOT NULL,
  PRIMARY KEY (`task_id`,`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `task_file`
--

INSERT INTO `task_file` (`task_id`, `file_id`, `path`, `filename`, `content_type`, `user_id`, `upload_time`) VALUES
(2, 1, '/home/eoin/sites/rosettaplatform/Rosetta-Foundation/public_html/../uploads/org-1/task-2/v-0', 'translate_me.txt', 'text/plain', NULL, '2011-03-22 14:27:14'),
(3, 1, '/home/eoin/sites/rosettaplatform/Rosetta-Foundation/public_html/../uploads/org-1/task-3/v-0', 'translate_me.txt.zip', 'application/download', NULL, '2011-03-22 14:29:02'),
(4, 1, '/home/eoin/sites/rosettaplatform/Rosetta-Foundation/public_html/../uploads/org-1/task-4/v-0', 'translate_me.txt', 'text/plain', NULL, '2011-03-22 14:29:50'),
(5, 1, '/home/eoin/sites/rosettaplatform/Rosetta-Foundation/public_html/../uploads/org-1/task-5/v-0', 'translate_me.txt', 'text/plain', NULL, '2011-03-22 14:30:30'),
(6, 1, '/home/eoin/sites/rosettaplatform/Rosetta-Foundation/public_html/../uploads/org-1/task-6/v-0', 'translate_me.txt.zip', 'application/download', NULL, '2011-03-22 14:31:13'),
(7, 1, '/home/eoin/sites/rosettaplatform/Rosetta-Foundation/public_html/../uploads/org-1/task-7/v-0', 'translate_me.txt.zip', 'application/download', NULL, '2011-03-22 14:33:35'),
(8, 1, '/home/eoin/sites/rosettaplatform/Rosetta-Foundation/public_html/../uploads/org-1/task-8/v-0', 'translate_me.txt', 'text/plain', NULL, '2011-03-22 14:37:21'),
(13, 1, '/home/eoin/sites/rosettaplatform/public_html/../uploads/org-1/task-13/v-0', 'translate_me.txt', 'text/plain', NULL, '2011-03-25 14:23:19'),
(22, 1, '/home/eoin/sites/rosettaplatform/public_html/../uploads/org-1/task-22/v-0', 'Google.html', 'text/html', NULL, '2011-03-25 16:17:53'),
(23, 1, '/home/eoin/sites/rosettaplatform/public_html/../uploads/org-1/task-23/v-0', 'Google.html', 'text/html', NULL, '2011-03-25 16:42:48');

-- --------------------------------------------------------

--
-- Table structure for table `task_file_version`
--

DROP TABLE IF EXISTS `task_file_version`;
CREATE TABLE IF NOT EXISTS `task_file_version` (
  `task_id` bigint(20) NOT NULL,
  `file_id` int(11) NOT NULL,
  `version_id` int(11) NOT NULL COMMENT 'Gets incremented within the code',
  `filename` text NOT NULL,
  `content_type` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'Null while we don''t have logging in',
  `upload_time` datetime NOT NULL,
  UNIQUE KEY `task_id` (`task_id`,`file_id`,`version_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `task_file_version`
--

INSERT INTO `task_file_version` (`task_id`, `file_id`, `version_id`, `filename`, `content_type`, `user_id`, `upload_time`) VALUES
(2, 1, 0, 'translate_me.txt', 'text/plain', NULL, '2011-03-22 14:27:14'),
(2, 1, 1, 'translate_me.txt', 'text/plain', NULL, '2011-03-22 14:28:22'),
(3, 1, 0, 'translate_me.txt.zip', 'application/download', NULL, '2011-03-22 14:29:02'),
(4, 1, 0, 'translate_me.txt', 'text/plain', NULL, '2011-03-22 14:29:50'),
(5, 1, 0, 'translate_me.txt', 'text/plain', NULL, '2011-03-22 14:30:30'),
(6, 1, 0, 'translate_me.txt.zip', 'application/download', NULL, '2011-03-22 14:31:13'),
(7, 1, 0, 'translate_me.txt.zip', 'application/download', NULL, '2011-03-22 14:33:35'),
(8, 1, 0, 'translate_me.txt', 'text/plain', NULL, '2011-03-22 14:37:21'),
(8, 1, 1, 'translate_me.txt', 'text/plain', NULL, '2011-03-22 14:46:44'),
(13, 1, 0, 'translate_me.txt', 'text/plain', NULL, '2011-03-25 14:23:19'),
(13, 1, 1, 'translate_me.txt', 'text/plain', NULL, '2011-03-25 14:23:34'),
(22, 1, 0, 'Google.html', 'text/html', NULL, '2011-03-25 16:17:53'),
(23, 1, 0, 'Google.html', 'text/html', NULL, '2011-03-25 16:42:48');

-- --------------------------------------------------------

--
-- Table structure for table `task_file_version_download`
--

DROP TABLE IF EXISTS `task_file_version_download`;
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
(2, 1, 0, NULL, '2011-03-22 14:27:55'),
(2, 1, 1, NULL, '2011-03-22 14:28:24'),
(2, 1, 1, NULL, '2011-03-22 14:28:28'),
(3, 1, 0, NULL, '2011-03-22 14:29:07'),
(8, 1, 0, NULL, '2011-03-22 14:45:53'),
(8, 1, 0, NULL, '2011-03-22 14:46:07'),
(8, 1, 1, NULL, '2011-03-22 14:47:18'),
(13, 1, 0, NULL, '2011-03-25 14:23:22'),
(13, 1, 1, NULL, '2011-03-25 14:23:35'),
(22, 1, 0, NULL, '2011-03-25 16:17:56');

-- --------------------------------------------------------

--
-- Table structure for table `task_tag`
--

DROP TABLE IF EXISTS `task_tag`;
CREATE TABLE IF NOT EXISTS `task_tag` (
  `task_id` bigint(20) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  `created_time` datetime NOT NULL,
  UNIQUE KEY `task_tag` (`task_id`,`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `task_tag`
--

INSERT INTO `task_tag` (`task_id`, `tag_id`, `created_time`) VALUES
(2, 3, '2011-03-22 14:27:14'),
(3, 3, '2011-03-22 14:29:02'),
(3, 4, '2011-03-22 14:29:02'),
(4, 5, '2011-03-22 14:29:50'),
(4, 6, '2011-03-22 14:29:50'),
(5, 7, '2011-03-22 14:30:30'),
(5, 6, '2011-03-22 14:30:30'),
(6, 5, '2011-03-22 14:31:13'),
(6, 6, '2011-03-22 14:31:13'),
(7, 5, '2011-03-22 14:33:35'),
(7, 6, '2011-03-22 14:33:35'),
(7, 8, '2011-03-22 14:33:35'),
(8, 3, '2011-03-22 14:37:21'),
(8, 9, '2011-03-22 14:37:21'),
(9, 10, '2011-03-22 14:42:45'),
(9, 11, '2011-03-22 14:42:45'),
(9, 12, '2011-03-22 14:42:45'),
(9, 13, '2011-03-22 14:42:45'),
(9, 14, '2011-03-22 14:42:45'),
(10, 10, '2011-03-22 14:44:21'),
(10, 15, '2011-03-22 14:44:21'),
(10, 11, '2011-03-22 14:44:21'),
(10, 12, '2011-03-22 14:44:21'),
(10, 13, '2011-03-22 14:44:21'),
(10, 16, '2011-03-22 14:44:21'),
(10, 17, '2011-03-22 14:44:21'),
(10, 18, '2011-03-22 14:44:21'),
(11, 10, '2011-03-22 14:45:03'),
(11, 11, '2011-03-22 14:45:03'),
(11, 12, '2011-03-22 14:45:03'),
(11, 17, '2011-03-22 14:45:03'),
(11, 18, '2011-03-22 14:45:03'),
(11, 16, '2011-03-22 14:45:03'),
(12, 19, '2011-03-25 14:23:00'),
(13, 19, '2011-03-25 14:23:19'),
(14, 20, '2011-03-25 16:01:54'),
(14, 6, '2011-03-25 16:01:54'),
(15, 20, '2011-03-25 16:11:10'),
(15, 6, '2011-03-25 16:11:10'),
(16, 20, '2011-03-25 16:11:18'),
(16, 6, '2011-03-25 16:11:18'),
(17, 20, '2011-03-25 16:11:47'),
(17, 6, '2011-03-25 16:11:47'),
(18, 20, '2011-03-25 16:12:03'),
(18, 6, '2011-03-25 16:12:03'),
(19, 20, '2011-03-25 16:13:00'),
(19, 6, '2011-03-25 16:13:00'),
(20, 20, '2011-03-25 16:13:26'),
(20, 6, '2011-03-25 16:13:26'),
(21, 19, '2011-03-25 16:14:41'),
(22, 20, '2011-03-25 16:17:53'),
(23, 19, '2011-03-25 16:42:48');
