-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.28-0ubuntu0.12.04.3 - (Ubuntu)
-- Server OS:                    debian-linux-gnu
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2013-01-09 15:51:55
-- --------------------------------------------------------ul

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
SET FOREIGN_KEY_CHECKS=0;

/*--------------------------------------------------start of tables--------------------------------*/

-- Dumping structure for table Solas-Match-Test.Admins
CREATE TABLE IF NOT EXISTS `Admins` (
	`user_id` INT(10) UNSIGNED NOT NULL,
	`organisation_id` INT(10) UNSIGNED NULL,
	UNIQUE INDEX `user_id` (`user_id`, `organisation_id`),
	INDEX `FK_Admins_Organisations` (`organisation_id`),
	CONSTRAINT `FK_Admins_Organisations` FOREIGN KEY (`organisation_id`) REFERENCES `Organisations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_Admins_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*---------------------------------------end of tables---------------------------------------------*/

SET FOREIGN_KEY_CHECKS=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
