-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.31-0ubuntu0.12.04.2 - (Ubuntu)
-- Server OS:                    debian-linux-gnu
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2013-07-29 14:27:25
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping structure for view Reporting.ArchivedProjects
DROP VIEW IF EXISTS `ArchivedProjects`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `ArchivedProjects` (
	`id` INT(10) UNSIGNED NOT NULL,
	`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`description` VARCHAR(4096) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`impact` VARCHAR(4096) NOT NULL COLLATE 'utf8_unicode_ci',
	`deadline` DATETIME NOT NULL,
	`organisationId` INT(10) UNSIGNED NOT NULL,
	`reference` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`wordCount` INT(10) NOT NULL,
	`created` DATETIME NOT NULL,
	`languageId` INT(10) UNSIGNED NOT NULL,
	`countryId` INT(10) UNSIGNED NOT NULL,
	`archiveDate` DATETIME NOT NULL,
	`archiver` INT(10) UNSIGNED NOT NULL
) ENGINE=MyISAM;


-- Dumping structure for view Reporting.ArchivedTasks
DROP VIEW IF EXISTS `ArchivedTasks`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `ArchivedTasks` (
	`id` BIGINT(20) UNSIGNED NOT NULL,
	`project_id` INT(20) UNSIGNED NOT NULL,
	`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`comment` VARCHAR(4096) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`deadline` DATETIME NOT NULL,
	`word-count` INT(11) NOT NULL,
	`created-time` DATETIME NOT NULL,
	`language_id-source` INT(11) UNSIGNED NOT NULL,
	`language_id-target` INT(11) UNSIGNED NOT NULL,
	`country_id-source` INT(11) UNSIGNED NOT NULL,
	`country_id-target` INT(11) UNSIGNED NOT NULL,
	`taskType_id` INT(11) UNSIGNED NOT NULL,
	`taskStatus_id` INT(11) UNSIGNED NOT NULL,
	`published` VARCHAR(50) NOT NULL COLLATE 'utf8_unicode_ci',
	`volenteerID` INT(10) UNSIGNED NULL DEFAULT NULL,
	`archiverId` INT(10) UNSIGNED NOT NULL,
	`archiveDate` DATETIME NOT NULL
) ENGINE=MyISAM;


-- Dumping structure for view Reporting.Badges
DROP VIEW IF EXISTS `Badges`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `Badges` (
	`id` INT(11) NOT NULL DEFAULT '0',
	`owner_id` INT(11) UNSIGNED NULL DEFAULT NULL,
	`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`description` MEDIUMTEXT NOT NULL COLLATE 'utf8_unicode_ci'
) ENGINE=MyISAM;


-- Dumping structure for view Reporting.Countries
DROP VIEW IF EXISTS `Countries`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `Countries` (
	`id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`code` VARCHAR(2) NOT NULL COMMENT '"IE", for example' COLLATE 'utf8_unicode_ci',
	`name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci'
) ENGINE=MyISAM;


-- Dumping structure for procedure Reporting.GetUserLanguages
DROP PROCEDURE IF EXISTS `GetUserLanguages`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserLanguages`()
BEGIN
SELECT
     l.`code` AS Languages_code,
     l.`name` AS Languages_name,
     u.`displayName` AS Users_displayName,
     u.`email` AS Users_email,
     u.`languageId` AS Users_languageId,
     u.`countryId` AS Users_countryId,
     c.`code` AS Countries_code,
     c.`name` AS Countries_name
FROM `Users` u
join `Languages` l  on l.id = u.languageId
join `Countries` c on c.id = u.countryId
left join `SecondaryLanguages` sl on sl.country_id = c.id  AND sl.language_id = l.id  AND u.id = sl.user_id;
END//
DELIMITER ;


-- Dumping structure for view Reporting.Languages
DROP VIEW IF EXISTS `Languages`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `Languages` (
	`id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`code` VARCHAR(3) NOT NULL COMMENT '"en", for example' COLLATE 'utf8_unicode_ci',
	`name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci'
) ENGINE=MyISAM;


-- Dumping structure for view Reporting.NGOContacts
DROP VIEW IF EXISTS `NGOContacts`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `NGOContacts` (
	`userId` INT(10) UNSIGNED NULL DEFAULT NULL,
	`firstName` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`lastName` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`mobileNumber` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`businessNumber` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`jobTitle` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`address` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`city` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`country` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`NGOId` INT(10) UNSIGNED NOT NULL,
	`id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`displayName` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`email` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`biography` TEXT NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`languageID` INT(10) UNSIGNED NULL DEFAULT NULL,
	`countryID` INT(10) UNSIGNED NULL DEFAULT NULL,
	`createdtime` DATETIME NOT NULL
) ENGINE=MyISAM;


-- Dumping structure for view Reporting.NGOs
DROP VIEW IF EXISTS `NGOs`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `NGOs` (
	`id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`name` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`biography` VARCHAR(4096) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`homePage` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`email` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`address` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`city` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`country` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`regionalFocus` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci'
) ENGINE=MyISAM;


-- Dumping structure for view Reporting.Projects
DROP VIEW IF EXISTS `Projects`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `Projects` (
	`id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`description` VARCHAR(4096) NOT NULL COLLATE 'utf8_unicode_ci',
	`impact` VARCHAR(4096) NOT NULL COLLATE 'utf8_unicode_ci',
	`deadline` DATETIME NOT NULL,
	`organisationId` INT(10) UNSIGNED NOT NULL,
	`reference` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`wordCount` INT(10) UNSIGNED NOT NULL,
	`created` DATETIME NOT NULL,
	`languageId` INT(10) UNSIGNED NOT NULL,
	`countryId` INT(10) UNSIGNED NOT NULL
) ENGINE=MyISAM;


-- Dumping structure for view Reporting.SecondaryLanguages
DROP VIEW IF EXISTS `SecondaryLanguages`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `SecondaryLanguages` (
	`id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`user_id` INT(10) UNSIGNED NOT NULL,
	`language_id` INT(10) UNSIGNED NOT NULL,
	`country_id` INT(10) UNSIGNED NOT NULL
) ENGINE=MyISAM;


-- Dumping structure for view Reporting.Stats
DROP VIEW IF EXISTS `Stats`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `Stats` (
	`name` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`value` DOUBLE NOT NULL
) ENGINE=MyISAM;


-- Dumping structure for view Reporting.TaskReviews
DROP VIEW IF EXISTS `TaskReviews`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `TaskReviews` (
	`project_id` INT(10) UNSIGNED NOT NULL,
	`task_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
	`user_id` INT(10) UNSIGNED NOT NULL,
	`corrections` INT(11) UNSIGNED NOT NULL,
	`grammar` INT(11) UNSIGNED NOT NULL,
	`spelling` INT(11) UNSIGNED NOT NULL,
	`consistency` INT(11) UNSIGNED NOT NULL,
	`comment` VARCHAR(2048) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci'
) ENGINE=MyISAM;


-- Dumping structure for view Reporting.Tasks
DROP VIEW IF EXISTS `Tasks`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `Tasks` (
	`id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
	`projectId` INT(20) UNSIGNED NOT NULL,
	`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`wordCount` INT(11) NULL DEFAULT NULL,
	`sourceLanguageId` INT(11) UNSIGNED NOT NULL,
	`targetLanguageId` INT(11) UNSIGNED NOT NULL,
	`sourceCountryId` INT(11) UNSIGNED NOT NULL,
	`targetCountryId` INT(11) UNSIGNED NOT NULL,
	`createdTime` DATETIME NOT NULL,
	`deadline` DATETIME NOT NULL,
	`comment` VARCHAR(4096) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`taskTypeId` INT(11) UNSIGNED NOT NULL,
	`taskStatusId` INT(11) UNSIGNED NOT NULL,
	`published` VARCHAR(50) NOT NULL COLLATE 'utf8_unicode_ci',
	`filename` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
	`contentType` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`creatorId` INT(11) UNSIGNED NOT NULL COMMENT 'Null while we don\'t have logging in',
	`volenteerId` INT(11) UNSIGNED NULL DEFAULT NULL
) ENGINE=MyISAM;


-- Dumping structure for view Reporting.UnVerifiedUsers
DROP VIEW IF EXISTS `UnVerifiedUsers`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `UnVerifiedUsers` (
	`id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`displayName` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`email` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`createdTime` DATETIME NOT NULL
) ENGINE=MyISAM;


-- Dumping structure for view Reporting.UserBadges
DROP VIEW IF EXISTS `UserBadges`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `UserBadges` (
	`id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`displayName` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`email` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`biography` TEXT NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`languageId` INT(10) UNSIGNED NULL DEFAULT NULL,
	`countryId` INT(10) UNSIGNED NULL DEFAULT NULL,
	`city` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`country` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`createdTime` DATETIME NOT NULL,
	`badgeId` INT(11) NOT NULL DEFAULT '0',
	`badgeCreator` INT(11) UNSIGNED NULL DEFAULT NULL,
	`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`description` MEDIUMTEXT NOT NULL COLLATE 'utf8_unicode_ci'
) ENGINE=MyISAM;


-- Dumping structure for view Reporting.UserLanguages
DROP VIEW IF EXISTS `UserLanguages`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `UserLanguages` (
	`displayName` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`email` VARCHAR(128) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci',
	`Language_code` VARCHAR(3) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci',
	`Language_name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`Country_code` VARCHAR(2) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci',
	`Country_name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci'
) ENGINE=MyISAM;


-- Dumping structure for view Reporting.Users
DROP VIEW IF EXISTS `Users`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `Users` (
	`id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`displayName` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`email` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`biography` TEXT NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`languageId` INT(10) UNSIGNED NULL DEFAULT NULL,
	`countryId` INT(10) UNSIGNED NULL DEFAULT NULL,
	`city` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`country` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`createdTime` DATETIME NOT NULL
) ENGINE=MyISAM;


-- Dumping structure for view Reporting.ArchivedProjects
DROP VIEW IF EXISTS `ArchivedProjects`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `ArchivedProjects`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ArchivedProjects` AS select `ap`.`id` AS `id`,`ap`.`title` AS `title`,`ap`.`description` AS `description`,`ap`.`impact` AS `impact`,`ap`.`deadline` AS `deadline`,`ap`.`organisation_id` AS `organisationId`,`ap`.`reference` AS `reference`,`ap`.`word-count` AS `wordCount`,`ap`.`created` AS `created`,`ap`.`language_id` AS `languageId`,`ap`.`country_id` AS `countryId`,`apm`.`archived-date` AS `archiveDate`,`apm`.`user_id-archived` AS `archiver` from (`SolasMatch`.`ArchivedProjects` `ap` join `SolasMatch`.`ArchivedProjectsMetadata` `apm` on((`ap`.`id` = `apm`.`archivedProject_id`)));


-- Dumping structure for view Reporting.ArchivedTasks
DROP VIEW IF EXISTS `ArchivedTasks`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `ArchivedTasks`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ArchivedTasks` AS select `at`.`id` AS `id`,`at`.`project_id` AS `project_id`,`at`.`title` AS `title`,`at`.`comment` AS `comment`,`at`.`deadline` AS `deadline`,`at`.`word-count` AS `word-count`,`at`.`created-time` AS `created-time`,`at`.`language_id-source` AS `language_id-source`,`at`.`language_id-target` AS `language_id-target`,`at`.`country_id-source` AS `country_id-source`,`at`.`country_id-target` AS `country_id-target`,`at`.`taskType_id` AS `taskType_id`,`at`.`taskStatus_id` AS `taskStatus_id`,`at`.`published` AS `published`,`atm`.`user_id-claimed` AS `volenteerID`,`atm`.`user_id-archived` AS `archiverId`,`atm`.`archived-date` AS `archiveDate` from (`SolasMatch`.`ArchivedTasks` `at` join `SolasMatch`.`ArchivedTasksMetadata` `atm` on((`at`.`id` = `atm`.`archivedTask_id`)));


-- Dumping structure for view Reporting.Badges
DROP VIEW IF EXISTS `Badges`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `Badges`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `Badges` AS select `SolasMatch`.`Badges`.`id` AS `id`,`SolasMatch`.`Badges`.`owner_id` AS `owner_id`,`SolasMatch`.`Badges`.`title` AS `title`,`SolasMatch`.`Badges`.`description` AS `description` from `SolasMatch`.`Badges`;


-- Dumping structure for view Reporting.Countries
DROP VIEW IF EXISTS `Countries`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `Countries`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `Countries` AS select `SolasMatch`.`Countries`.`id` AS `id`,`SolasMatch`.`Countries`.`code` AS `code`,`SolasMatch`.`Countries`.`en-name` AS `name` from `SolasMatch`.`Countries`;


-- Dumping structure for view Reporting.Languages
DROP VIEW IF EXISTS `Languages`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `Languages`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `Languages` AS select `SolasMatch`.`Languages`.`id` AS `id`,`SolasMatch`.`Languages`.`code` AS `code`,`SolasMatch`.`Languages`.`en-name` AS `name` from `SolasMatch`.`Languages`;


-- Dumping structure for view Reporting.NGOContacts
DROP VIEW IF EXISTS `NGOContacts`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `NGOContacts`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `NGOContacts` AS select `up`.`user_id` AS `userId`,`up`.`first-name` AS `firstName`,`up`.`last-name` AS `lastName`,`up`.`mobile-number` AS `mobileNumber`,`up`.`business-number` AS `businessNumber`,`up`.`job-title` AS `jobTitle`,`up`.`address` AS `address`,`up`.`city` AS `city`,`up`.`country` AS `country`,`om`.`organisation_id` AS `NGOId`,`u`.`id` AS `id`,`u`.`display-name` AS `displayName`,`u`.`email` AS `email`,`u`.`biography` AS `biography`,`u`.`language_id` AS `languageID`,`u`.`country_id` AS `countryID`,`u`.`created-time` AS `createdtime` from ((`SolasMatch`.`OrganisationMembers` `om` left join `SolasMatch`.`UserPersonalInformation` `up` on((`up`.`user_id` = `om`.`user_id`))) join `SolasMatch`.`Users` `u` on((`om`.`user_id` = `u`.`id`)));


-- Dumping structure for view Reporting.NGOs
DROP VIEW IF EXISTS `NGOs`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `NGOs`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `NGOs` AS select `o`.`id` AS `id`,`o`.`name` AS `name`,`o`.`biography` AS `biography`,`o`.`home-page` AS `homePage`,`o`.`e-mail` AS `email`,`o`.`address` AS `address`,`o`.`city` AS `city`,`o`.`country` AS `country`,`o`.`regional-focus` AS `regionalFocus` from `SolasMatch`.`Organisations` `o`;


-- Dumping structure for view Reporting.Projects
DROP VIEW IF EXISTS `Projects`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `Projects`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `Projects` AS select `p`.`id` AS `id`,`p`.`title` AS `title`,`p`.`description` AS `description`,`p`.`impact` AS `impact`,`p`.`deadline` AS `deadline`,`p`.`organisation_id` AS `organisationId`,`p`.`reference` AS `reference`,`p`.`word-count` AS `wordCount`,`p`.`created` AS `created`,`p`.`language_id` AS `languageId`,`p`.`country_id` AS `countryId` from `SolasMatch`.`Projects` `p`;


-- Dumping structure for view Reporting.SecondaryLanguages
DROP VIEW IF EXISTS `SecondaryLanguages`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `SecondaryLanguages`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `SecondaryLanguages` AS select `SolasMatch`.`UserSecondaryLanguages`.`id` AS `id`,`SolasMatch`.`UserSecondaryLanguages`.`user_id` AS `user_id`,`SolasMatch`.`UserSecondaryLanguages`.`language_id` AS `language_id`,`SolasMatch`.`UserSecondaryLanguages`.`country_id` AS `country_id` from `SolasMatch`.`UserSecondaryLanguages`;


-- Dumping structure for view Reporting.Stats
DROP VIEW IF EXISTS `Stats`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `Stats`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `Stats` AS select `SolasMatch`.`Statistics`.`name` AS `name`,`SolasMatch`.`Statistics`.`value` AS `value` from `SolasMatch`.`Statistics`;


-- Dumping structure for view Reporting.TaskReviews
DROP VIEW IF EXISTS `TaskReviews`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `TaskReviews`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `TaskReviews` AS select `SolasMatch`.`TaskReviews`.`project_id` AS `project_id`,`SolasMatch`.`TaskReviews`.`task_id` AS `task_id`,`SolasMatch`.`TaskReviews`.`user_id` AS `user_id`,`SolasMatch`.`TaskReviews`.`corrections` AS `corrections`,`SolasMatch`.`TaskReviews`.`grammar` AS `grammar`,`SolasMatch`.`TaskReviews`.`spelling` AS `spelling`,`SolasMatch`.`TaskReviews`.`consistency` AS `consistency`,`SolasMatch`.`TaskReviews`.`comment` AS `comment` from `SolasMatch`.`TaskReviews`;


-- Dumping structure for view Reporting.Tasks
DROP VIEW IF EXISTS `Tasks`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `Tasks`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `Tasks` AS select `t`.`id` AS `id`,`t`.`project_id` AS `projectId`,`t`.`title` AS `title`,`t`.`word-count` AS `wordCount`,`t`.`language_id-source` AS `sourceLanguageId`,`t`.`language_id-target` AS `targetLanguageId`,`t`.`country_id-source` AS `sourceCountryId`,`t`.`country_id-target` AS `targetCountryId`,`t`.`created-time` AS `createdTime`,`t`.`deadline` AS `deadline`,`t`.`comment` AS `comment`,`t`.`task-type_id` AS `taskTypeId`,`t`.`task-status_id` AS `taskStatusId`,`t`.`published` AS `published`,`tv`.`filename` AS `filename`,`tv`.`content-type` AS `contentType`,`tv`.`user_id` AS `creatorId`,`tc`.`user_id` AS `volenteerId` from ((`SolasMatch`.`Tasks` `t` join `SolasMatch`.`TaskFileVersions` `tv` on(((`t`.`id` = `tv`.`task_id`) and (`tv`.`version_id` = 0)))) left join `SolasMatch`.`TaskClaims` `tc` on((`t`.`id` = `tc`.`task_id`)));


-- Dumping structure for view Reporting.UnVerifiedUsers
DROP VIEW IF EXISTS `UnVerifiedUsers`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `UnVerifiedUsers`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `UnVerifiedUsers` AS select `u`.`id` AS `id`,`u`.`display-name` AS `displayName`,`u`.`email` AS `email`,`u`.`created-time` AS `createdTime` from `SolasMatch`.`Users` `u` where exists(select 1 from `SolasMatch`.`RegisteredUsers` `ru` where (`ru`.`user_id` = `u`.`id`));


-- Dumping structure for view Reporting.UserBadges
DROP VIEW IF EXISTS `UserBadges`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `UserBadges`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `UserBadges` AS select `u`.`id` AS `id`,`u`.`displayName` AS `displayName`,`u`.`email` AS `email`,`u`.`biography` AS `biography`,`u`.`languageId` AS `languageId`,`u`.`countryId` AS `countryId`,`u`.`city` AS `city`,`u`.`country` AS `country`,`u`.`createdTime` AS `createdTime`,`b`.`id` AS `badgeId`,`b`.`owner_id` AS `badgeCreator`,`b`.`title` AS `title`,`b`.`description` AS `description` from ((`Reporting`.`Users` `u` join `SolasMatch`.`UserBadges` `ub` on((`u`.`id` = `ub`.`user_id`))) join `Reporting`.`Badges` `b` on((`ub`.`badge_id` = `b`.`id`)));


-- Dumping structure for view Reporting.UserLanguages
DROP VIEW IF EXISTS `UserLanguages`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `UserLanguages`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `UserLanguages` AS select `u`.`displayName` AS `displayName`,`u`.`email` AS `email`,`l`.`code` AS `Language_code`,`l`.`name` AS `Language_name`,`c`.`code` AS `Country_code`,`c`.`name` AS `Country_name` from ((`Reporting`.`Users` `u` join `Reporting`.`Languages` `l` on((`l`.`id` = `u`.`languageId`))) join `Reporting`.`Countries` `c` on((`c`.`id` = `u`.`countryId`))) union select `u`.`displayName` AS `displayName`,`u`.`email` AS `email`,`l`.`code` AS `Language_code`,`l`.`name` AS `Language_name`,`c`.`code` AS `Country_code`,`c`.`name` AS `Country_name` from (((`Reporting`.`Users` `u` join `Reporting`.`SecondaryLanguages` `sl` on((`u`.`id` = `sl`.`user_id`))) join `Reporting`.`Languages` `l` on((`sl`.`language_id` = `l`.`id`))) join `Reporting`.`Countries` `c` on((`sl`.`country_id` = `c`.`id`)));


-- Dumping structure for view Reporting.Users
DROP VIEW IF EXISTS `Users`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `Users`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `Users` AS select `u`.`id` AS `id`,`u`.`display-name` AS `displayName`,`u`.`email` AS `email`,`u`.`biography` AS `biography`,`u`.`language_id` AS `languageId`,`u`.`country_id` AS `countryId`,`upi`.`city` AS `city`,`upi`.`country` AS `country`,`u`.`created-time` AS `createdTime` from (`SolasMatch`.`Users` `u` left join `SolasMatch`.`UserPersonalInformation` `upi` on((`u`.`id` = `upi`.`user_id`)));
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
