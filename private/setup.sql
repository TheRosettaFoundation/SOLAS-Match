-- phpMyAdmin SQL Dump
-- version 3.3.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 06, 2011 at 02:24 PM
-- Server version: 5.1.54
-- PHP Version: 5.3.5-1ubuntu7.2

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

CREATE TABLE IF NOT EXISTS `organisation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `organisation`
--

INSERT INTO `organisation` (`id`, `name`) VALUES
(1, 'PeopleOrg'),
(2, 'TransOrg'),
(3, 'MedOrg'),
(4, 'TestOrg');

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(50) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`id`, `label`) VALUES
(1, 'mission'),
(2, 'marketing'),
(3, 'informal'),
(4, 'website'),
(5, 'online'),
(6, 'formal'),
(7, 'document'),
(8, 'email'),
(9, 'fun'),
(10, 'technical'),
(11, 'news'),
(12, 'test'),
(13, 'leaflet');

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE IF NOT EXISTS `task` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `organisation_id` int(10) unsigned NOT NULL,
  `title` text NOT NULL,
  `word_count` int(10) unsigned DEFAULT NULL,
  `source` text,
  `target` text,
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `source` (`source`(255)),
  KEY `target` (`target`(255))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`id`, `organisation_id`, `title`, `word_count`, `source`, `target`, `created_time`) VALUES
(1, 1, 'Our\r\nmission\r\nstatement\r\npage.\r\nPlease\r\nhelp\r\ntranslate\r\nit to\r\nFrench\r\nfor\r\na\r\nweb\r\naudience.', 300, 'English', 'French', '2011-04-08 09:25:24'),
(2, 1, 'Seeking\r\nexperienced\r\ntranslators.\r\nThis\r\nis a\r\ntranslation\r\npack\r\nfor\r\nour\r\nwebsite,\r\nand\r\nformatting\r\nneeds\r\nto be\r\nmaintained.', 2000, 'English', 'Spanish', '2011-04-08 09:26:46'),
(3, 1, 'Simple\r\ntext\r\nfile\r\nfor\r\ntranslation.\r\nThis\r\nwill\r\nbe a\r\nblurb\r\non our\r\nsite''s\r\nhome\r\npage.\r\nHelp\r\nus convince\r\npeople\r\nto sign\r\nup to\r\nour\r\nmailing\r\nlist.', 150, 'English', 'French', '2011-04-08 09:28:52'),
(4, 1, 'Quick\r\ntranslation\r\n-\r\nintroduction\r\nto our\r\nNGO\r\nto be\r\ntranslated\r\nto Spanish,\r\nand\r\nwill\r\nbe included\r\nin our\r\nannual\r\nreport.', 280, 'English', 'Spanish', '2011-04-08 09:31:10'),
(5, 1, 'Russian\r\ntranslation\r\nrequired\r\nfor\r\n"Get\r\nInvolved"\r\nweb\r\npage.\r\nIt will\r\nhelp\r\nour\r\nNGO\r\nto recruit\r\nvolunteers\r\nin a\r\nnew\r\nlanguage\r\nmarket.', 450, 'English', 'Russian', '2011-04-08 09:34:16'),
(6, 1, 'New\r\nedition\r\nof monthly\r\nemail\r\nmessage\r\nneeds\r\nto be\r\ntranslated\r\nto Chinese.\r\nPlease\r\nkeep\r\nHTML\r\nformatting\r\nin place,\r\nand\r\ntake\r\ncare\r\nwith\r\nthe\r\ndocument\r\nencoding.', 505, 'English', 'Chinese', '2011-04-08 09:38:22'),
(7, 1, 'Do you\r\nhave\r\nspecific\r\nexperience\r\nin translating\r\nuser\r\ndocumentation?\r\nLocalising\r\ndocumentation\r\nfor\r\nour\r\nopen\r\nsource\r\ntool\r\nfor\r\nvolunteers.', 2100, 'English', 'Amharic', '2011-04-08 10:06:48'),
(8, 1, '"Contact\r\nUs"\r\npage\r\nfor\r\nour\r\nweb\r\nsite,\r\nstraight-forward\r\ntranslation,\r\nbut\r\nplease\r\nmaintain\r\nformatting.', 120, 'English', 'French', '2011-04-08 10:14:03'),
(9, 1, 'Tranlsate\r\nthis\r\nnews\r\nitem\r\nthat\r\nwill\r\nbe feature\r\non our\r\nweb\r\nsite', 400, 'English', 'French', '2011-04-08 11:43:24'),
(10, 1, 'Test', 3, 'English', 'Chinese', '2011-04-15 11:31:23'),
(11, 1, 'Online\r\ntext\r\nneeds\r\ntranslation\r\nto Chinese,\r\nwith\r\nsome\r\nspecific\r\nterminology\r\nterms.', 550, 'English', 'Chinese', '2011-04-26 10:29:16'),
(12, 1, 'Volunteer\r\ninstructions\r\nfor\r\nour\r\nyearly\r\nevent.\r\nPlease\r\nhelp\r\ntranslate\r\nthis\r\nleaflet.', 380, 'English', 'Chinese', '2011-05-17 11:45:29'),
(13, 1, 'Instructions\r\nfor\r\nvolunteers:\r\nsecond\r\npart\r\nof this\r\nproject.', 280, 'English', 'Chinese', '2011-05-17 12:04:19'),
(14, 1, 'Our\r\nlatest\r\nemail\r\nnewsletter\r\nfor\r\nmembers,\r\nsending\r\nout\r\nnext\r\nweek.', 400, 'English', 'Arabic', '2011-05-17 15:07:39'),
(15, 1, 'Help\r\nus translate\r\nthis', 200, 'English', 'Chinese', '2011-05-17 16:27:18');

-- --------------------------------------------------------

--
-- Table structure for table `task_file`
--

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
(1, 1, '/home/eoin/sites/rosettaplatform/public_html/../uploads/org-1/task-1/v-0', 'ourmission.html', 'text/html', NULL, '2011-04-08 09:25:24'),
(2, 1, '/home/eoin/sites/rosettaplatform/public_html/../uploads/org-1/task-2/v-0', 'translation pack.zip', 'application/x-zip-compressed', NULL, '2011-04-08 09:26:46'),
(3, 1, '/home/eoin/sites/rosettaplatform/public_html/../uploads/org-1/task-3/v-0', 'marketing.txt', 'text/plain', NULL, '2011-04-08 09:28:52'),
(4, 1, '/home/eoin/sites/rosettaplatform/public_html/../uploads/org-1/task-4/v-0', 'ourmission.html', 'text/html', NULL, '2011-04-08 09:31:10'),
(5, 1, '/home/eoin/sites/rosettaplatform/public_html/../uploads/org-1/task-5/v-0', 'getinvolved.html', 'text/html', NULL, '2011-04-08 09:34:16'),
(6, 1, '/home/eoin/sites/rosettaplatform/public_html/../uploads/org-1/task-6/v-0', 'translation pack.zip', 'application/x-zip-compressed', NULL, '2011-04-08 09:38:22'),
(7, 1, '/home/eoin/sites/rosettaplatform/public_html/../uploads/org-1/task-7/v-0', 'translation pack.zip', 'application/x-zip-compressed', NULL, '2011-04-08 10:06:48'),
(8, 1, '/home/eoin/sites/rosettaplatform/public_html/../uploads/org-1/task-8/v-0', 'getinvolved.html', 'text/html', NULL, '2011-04-08 10:14:03'),
(9, 1, '/home/eoin/sites/rosettaplatform/public_html/../uploads/org-1/task-9/v-0', 'news.thankyou.2011.htm', 'text/html', NULL, '2011-04-08 11:43:24'),
(10, 1, '/home/eoin/sites/rosettaplatform/public_html/../uploads/org-1/task-10/v-0', 'Google.html', 'text/html', NULL, '2011-04-15 11:31:23'),
(11, 1, '/home/eoin/sites/rosettaplatform/public_html/../uploads/org-1/task-11/v-0', 'index.html.xlf', 'application/octet-stream', NULL, '2011-04-26 10:29:16'),
(12, 1, '/home/eoin/sites/rosettaplatform/private/includes/../uploads/org-1/task-12/v-0', 'index.html.xlf', 'application/x-xliff', NULL, '2011-05-17 11:45:29'),
(13, 1, '/home/eoin/sites/rosettaplatform/private/includes/../uploads/org-1/task-13/v-0', 'news.thankyou.2011.htm', 'text/html', NULL, '2011-05-17 12:04:19'),
(14, 1, '/home/eoin/sites/rosettaplatform/private/includes/../uploads/org-1/task-14/v-0', 'translation pack.zip', 'application/x-zip-compressed', NULL, '2011-05-17 15:07:39'),
(15, 1, '/home/eoin/sites/rosettaplatform/private/includes/../uploads/org-1/task-15/v-0', 'index.html.xlf', 'application/octet-stream', NULL, '2011-05-17 16:27:18');

-- --------------------------------------------------------

--
-- Table structure for table `task_file_version`
--

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
(1, 1, 0, 'ourmission.html', 'text/html', NULL, '2011-04-08 09:25:24'),
(2, 1, 0, 'translation pack.zip', 'application/x-zip-compressed', NULL, '2011-04-08 09:26:46'),
(2, 1, 1, 'translation pack.zip', 'application/x-zip-compressed', NULL, '2011-04-08 09:27:20'),
(3, 1, 0, 'marketing.txt', 'text/plain', NULL, '2011-04-08 09:28:52'),
(4, 1, 0, 'ourmission.html', 'text/html', NULL, '2011-04-08 09:31:10'),
(5, 1, 0, 'getinvolved.html', 'text/html', NULL, '2011-04-08 09:34:16'),
(6, 1, 0, 'translation pack.zip', 'application/x-zip-compressed', NULL, '2011-04-08 09:38:22'),
(7, 1, 0, 'translation pack.zip', 'application/x-zip-compressed', NULL, '2011-04-08 10:06:48'),
(7, 1, 1, 'translation pack.zip', 'application/x-zip-compressed', NULL, '2011-04-08 10:11:40'),
(8, 1, 0, 'getinvolved.html', 'text/html', NULL, '2011-04-08 10:14:03'),
(8, 1, 1, 'getinvolved.html', 'text/html', NULL, '2011-04-08 10:32:05'),
(6, 1, 1, 'translation pack.zip', 'application/x-zip-compressed', NULL, '2011-04-08 11:42:05'),
(9, 1, 0, 'news.thankyou.2011.htm', 'text/html', NULL, '2011-04-08 11:43:24'),
(10, 1, 0, 'Google.html', 'text/html', NULL, '2011-04-15 11:31:23'),
(11, 1, 0, 'index.html.xlf', 'application/octet-stream', NULL, '2011-04-26 10:29:16'),
(11, 1, 1, 'index.html.xlf', 'application/x-xliff', NULL, '2011-05-17 11:38:45'),
(12, 1, 0, 'index.html.xlf', 'application/x-xliff', NULL, '2011-05-17 11:45:29'),
(13, 1, 0, 'news.thankyou.2011.htm', 'text/html', NULL, '2011-05-17 12:04:19'),
(14, 1, 0, 'translation pack.zip', 'application/x-zip-compressed', NULL, '2011-05-17 15:07:39'),
(11, 1, 2, 'news.thankyou.2011.htm', 'text/html', NULL, '2011-05-17 16:26:43'),
(15, 1, 0, 'index.html.xlf', 'application/octet-stream', NULL, '2011-05-17 16:27:18');

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
(2, 1, 0, NULL, '2011-04-08 09:27:10'),
(7, 1, 0, NULL, '2011-04-08 10:10:13'),
(7, 1, 0, NULL, '2011-04-08 10:10:43'),
(7, 1, 0, NULL, '2011-04-08 10:11:02'),
(7, 1, 0, NULL, '2011-04-08 10:11:15'),
(8, 1, 0, NULL, '2011-04-08 10:31:16'),
(6, 1, 0, NULL, '2011-04-08 10:32:17'),
(6, 1, 0, NULL, '2011-04-08 10:32:22'),
(6, 1, 0, NULL, '2011-04-08 11:10:08'),
(6, 1, 0, NULL, '2011-04-08 11:41:35'),
(10, 1, 0, NULL, '2011-04-15 11:44:19'),
(11, 1, 0, NULL, '2011-04-26 10:29:19'),
(11, 1, 0, NULL, '2011-05-09 14:19:20'),
(11, 1, 0, NULL, '2011-05-09 14:23:11'),
(11, 1, 0, NULL, '2011-05-09 14:23:54'),
(11, 1, 0, NULL, '2011-05-17 11:36:56'),
(11, 1, 1, NULL, '2011-05-17 11:38:47'),
(12, 1, 0, NULL, '2011-05-17 11:45:38'),
(12, 1, 0, NULL, '2011-05-17 12:02:41'),
(13, 1, 0, NULL, '2011-05-17 12:04:22'),
(14, 1, 0, NULL, '2011-05-17 15:55:44');

-- --------------------------------------------------------

--
-- Table structure for table `task_tag`
--

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
(1, 1, '2011-04-08 09:25:24'),
(1, 2, '2011-04-08 09:25:24'),
(1, 3, '2011-04-08 09:25:24'),
(2, 4, '2011-04-08 09:26:46'),
(2, 2, '2011-04-08 09:26:46'),
(2, 5, '2011-04-08 09:26:46'),
(3, 4, '2011-04-08 09:28:52'),
(3, 2, '2011-04-08 09:28:52'),
(3, 3, '2011-04-08 09:28:52'),
(4, 6, '2011-04-08 09:31:10'),
(4, 7, '2011-04-08 09:31:10'),
(5, 4, '2011-04-08 09:34:16'),
(5, 3, '2011-04-08 09:34:16'),
(6, 8, '2011-04-08 09:38:22'),
(6, 9, '2011-04-08 09:38:22'),
(6, 3, '2011-04-08 09:38:22'),
(7, 10, '2011-04-08 10:06:48'),
(7, 6, '2011-04-08 10:06:48'),
(8, 4, '2011-04-08 10:14:03'),
(8, 3, '2011-04-08 10:14:03'),
(8, 2, '2011-04-08 10:14:03'),
(9, 11, '2011-04-08 11:43:24'),
(9, 3, '2011-04-08 11:43:24'),
(10, 12, '2011-04-15 11:31:23'),
(11, 3, '2011-04-26 10:29:16'),
(11, 2, '2011-04-26 10:29:16'),
(12, 13, '2011-05-17 11:45:29'),
(13, 13, '2011-05-17 12:04:19'),
(14, 8, '2011-05-17 15:07:39'),
(15, 3, '2011-05-17 16:27:18'),
(15, 2, '2011-05-17 16:27:18');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(256) NOT NULL,
  `password` char(128) NOT NULL,
  `nonce` int(11) unsigned NOT NULL,
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `email`, `password`, `nonce`, `created_time`) VALUES
(17, 'eoinoconchuir+r1@gmail.com', '724da6625cfd42d5722eb7ffca082a0a2ad1ff66327a050c51b91cf41f2f50e97056a59e00e66d7fcc21ad1a34340b37354429086e982d47af6a800c1e577eaf', 1759816993, '2011-04-13 15:28:55'),
(20, 'eoin.oconchuir@ul.ie', '253b63f19498fba91bbbf258ec0c0d336cbb9e56c4375a7ff13b81d93898248024bd2be7fdb63c25c2c72c06681c52e68f1cfc3a18e7673eff99fddd2c8f6a74', 735631749, '2011-05-17 11:25:14');
