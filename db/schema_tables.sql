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


-- Dumping structure for table debug-test.ArchivedProjects
CREATE TABLE IF NOT EXISTS `ArchivedProjects` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(4096) COLLATE utf8_unicode_ci DEFAULT NULL,
  `impact` varchar(4096) COLLATE utf8_unicode_ci NOT NULL,
  `deadline` datetime NOT NULL,
  `organisation_id` int(10) unsigned NOT NULL,
  `reference` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `word-count` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `country_id` int(10) unsigned NOT NULL,
  `image_uploaded` BIT(1) DEFAULT 0 NOT NULL,
  `image_approved` BIT(1) DEFAULT 0 NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `organisation_id` (`organisation_id`,`language_id`,`country_id`),
  KEY `FK_ArchivedProjects_Languages` (`language_id`),
  KEY `FK_ArchivedProjects_Countries` (`country_id`),
  CONSTRAINT `FK_archivedproject_organisation` FOREIGN KEY (`organisation_id`) REFERENCES `Organisations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedProjects_Languages` FOREIGN KEY (`language_id`) REFERENCES `Languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedProjects_Countries` FOREIGN KEY (`country_id`) REFERENCES `Countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP PROCEDURE IF EXISTS alterTable;
 DELIMITER //
 CREATE PROCEDURE alterTable()
 BEGIN
     IF NOT EXISTS(SELECT 1
                     FROM information_schema.`COLUMNS`
                     WHERE TABLE_SCHEMA = database()
                     AND TABLE_NAME = "ArchivedProjects"
                     AND COLUMN_NAME = "image_uploaded") then
         ALTER TABLE ArchivedProjects
             ADD `image_uploaded` BIT(1) DEFAULT 0 NOT NULL;
     END IF;
 END//
 DELIMITER ;
 CALL alterTable();
 DROP PROCEDURE alterTable;

 
 DROP PROCEDURE IF EXISTS alterTable;
 DELIMITER //
 CREATE PROCEDURE alterTable()
 BEGIN
     IF NOT EXISTS(SELECT 1
                     FROM information_schema.`COLUMNS`
                     WHERE TABLE_SCHEMA = database()
                     AND TABLE_NAME = "ArchivedProjects"
                     AND COLUMN_NAME = "image_approved") then
         ALTER TABLE ArchivedProjects
             ADD `image_approved` BIT(1) DEFAULT 0 NOT NULL;
     END IF;
 END//
 DELIMITER ;
 CALL alterTable();
 DROP PROCEDURE alterTable;

-- Dumping structure for table debug-test.ArchivedProjectsMetadata
CREATE TABLE IF NOT EXISTS `ArchivedProjectsMetadata` (
  `archivedProject_id` int(10) unsigned NOT NULL,
  `user_id-archived` int(10) unsigned NOT NULL,
  `user_id-projectCreator` int(10) unsigned NOT NULL,
  `filename` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `file-token` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `mime-type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `archived-date` datetime NOT NULL,
  `tags` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `archivedProject_id` (`archivedProject_id`),
  KEY `FK_ArchivedProjectsMetadata_Users` (`user_id-archived`),
  KEY `FK_ArchivedProjectsMetadata_Users_2` (`user_id-projectCreator`),
  CONSTRAINT `FK_ArchivedProjectsMetadata_ArchivedProjects` FOREIGN KEY (`archivedProject_id`) REFERENCES `ArchivedProjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedProjectsMetadata_Users` FOREIGN KEY (`user_id-archived`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedProjectsMetadata_Users_2` FOREIGN KEY (`user_id-projectCreator`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Dumping structure for table debug-test.ArchivedTasks
CREATE TABLE IF NOT EXISTS `ArchivedTasks` (
  `id` bigint(20) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `comment` varchar(4096) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deadline` datetime NOT NULL,
  `word-count` int(11) NOT NULL,
  `created-time` datetime NOT NULL,
  `language_id-source` int(10) unsigned NOT NULL,
  `language_id-target` int(10) unsigned NOT NULL,
  `country_id-source` int(10) unsigned NOT NULL,
  `country_id-target` int(10) unsigned NOT NULL,
  `taskType_id` int(11) unsigned NOT NULL,
  `taskStatus_id` int(11) unsigned NOT NULL,
  `published` BIT(1) DEFAULT 0 NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `FK_ArchivedTasks_Languages` (`language_id-source`),
  KEY `FK_ArchivedTasks_Languages_2` (`language_id-target`),
  KEY `FK_ArchivedTasks_Countries` (`country_id-source`),
  KEY `FK_ArchivedTasks_Countries_2` (`country_id-target`),
  KEY `FK_ArchivedTasks_TaskTypes` (`taskType_id`),
  KEY `FK_ArchivedTasks_TaskStatus` (`taskStatus_id`),
  CONSTRAINT `FK_ArchivedTasks_Countries` FOREIGN KEY (`country_id-source`) REFERENCES `Countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasks_Countries_2` FOREIGN KEY (`country_id-target`) REFERENCES `Countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasks_Languages` FOREIGN KEY (`language_id-source`) REFERENCES `Languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasks_Languages_2` FOREIGN KEY (`language_id-target`) REFERENCES `Languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasks_TaskStatus` FOREIGN KEY (`taskStatus_id`) REFERENCES `TaskStatus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasks_TaskTypes` FOREIGN KEY (`taskType_id`) REFERENCES `TaskTypes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Dumping structure for table debug-test.ArchivedTasksMetadata
CREATE TABLE IF NOT EXISTS `ArchivedTasksMetadata` (
  `archivedTask_id` bigint(20) unsigned NOT NULL,
  `version` int(10) unsigned NOT NULL,
  `filename` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `content-type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `upload-time` datetime NOT NULL,
  `user_id-claimed` int(10) unsigned DEFAULT NULL,
  `user_id-archived` int(10) unsigned NOT NULL,
  `prerequisites` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id-taskCreator` int(10) unsigned NOT NULL,
  `archived-date` datetime NOT NULL,
  UNIQUE KEY `archivedTask_id` (`archivedTask_id`),
  KEY `FK_ArchivedTasksMetadata_Users` (`user_id-claimed`),
  KEY `FK_ArchivedTasksMetadata_Users_2` (`user_id-archived`),
  KEY `FK_ArchivedTasksMetadata_Users_3` (`user_id-taskCreator`),
  CONSTRAINT `FK_ArchivedTasksMetadata_ArchivedTasks` FOREIGN KEY (`archivedTask_id`) REFERENCES `ArchivedTasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasksMetadata_Users` FOREIGN KEY (`user_id-claimed`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasksMetadata_Users_2` FOREIGN KEY (`user_id-archived`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasksMetadata_Users_3` FOREIGN KEY (`user_id-taskCreator`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Dumping structure for table Solas-Match-Test.Badges
CREATE TABLE IF NOT EXISTS `Badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) unsigned DEFAULT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `badge` (`owner_id`,`title`),
  CONSTRAINT `FK_badges_organisation` FOREIGN KEY (`owner_id`) REFERENCES `Organisations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Dumping structure for table debug-test3.BannedOrganisations
CREATE TABLE IF NOT EXISTS `BannedOrganisations` (
  `org_id` int(10) unsigned NOT NULL,
  `user_id-admin` int(10) unsigned NOT NULL,
  `bannedtype_id` int(10) unsigned NOT NULL,
  `comment` varchar(4096) COLLATE utf8_unicode_ci DEFAULT NULL,
  `banned-date` datetime NOT NULL,
  UNIQUE KEY `org_id` (`org_id`),
  KEY `FK_BannedOrganisations_Users` (`user_id-admin`),
  KEY `FK_BannedOrganisations_BannedTypes` (`bannedtype_id`),
  CONSTRAINT `FK_BannedOrganisations_BannedTypes` FOREIGN KEY (`bannedtype_id`) REFERENCES `BannedTypes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_BannedOrganisations_Organisations` FOREIGN KEY (`org_id`) REFERENCES `Organisations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_BannedOrganisations_Users` FOREIGN KEY (`user_id-admin`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Dumping structure for table debug-test3.BannedTypes
CREATE TABLE IF NOT EXISTS `BannedTypes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

REPLACE INTO `BannedTypes` (`id`, `type`) VALUES
	(1, 'Day'),
    (2, 'Week'),
	(3, 'Month'),
	(4, 'Permanent'),
    (5, 'Hour');

-- Dumping structure for table debug-test3.BannedUsers
CREATE TABLE IF NOT EXISTS `BannedUsers` (
  `user_id` int(10) unsigned NOT NULL,
  `user_id-admin` int(10) unsigned NOT NULL,
  `bannedtype_id` int(10) unsigned NOT NULL,
  `comment` varchar(4096) COLLATE utf8_unicode_ci DEFAULT NULL,
  `banned-date` datetime NOT NULL,
  UNIQUE KEY `user_id` (`user_id`),
  KEY `FK_BannedUsers_Users_2` (`user_id-admin`),
  KEY `FK_BannedUsers_BannedTypes` (`bannedtype_id`),
  CONSTRAINT `FK_BannedUsers_BannedTypes` FOREIGN KEY (`bannedtype_id`) REFERENCES `BannedTypes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_BannedUsers_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_BannedUsers_Users_2` FOREIGN KEY (`user_id-admin`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table Solas-Match-Test.Badges: ~4 rows (approximately)
/*!40000 ALTER TABLE `Badges` DISABLE KEYS */;
REPLACE INTO `Badges` (`id`, `owner_id`, `title`, `description`) VALUES
	(3, NULL, 'system_badge_profile_filler_title', 'system_badge_profile_filler_desc'),
	(4, NULL, 'system_badge_registered_title', 'system_badge_registered_desc'),
	(5, NULL, 'system_badge_native_language_title', 'system_badge_native_language_desc'),
        (6, NULL, 'system_badge_translator_title', 'system_badge_translator_desc'),
        (7, NULL, 'system_badge_proofreader_title', 'system_badge_proofreader_desc'),
        (8, NULL, 'system_badge_interpreter_title', 'system_badge_interpreter_desc'),
        (9, NULL, 'system_badge_polyglot_title', 'system_badge_polyglot_desc');
ALTER TABLE `Badges` AUTO_INCREMENT=100;

-- Dumping structure for table Solas-Match-Test.Countries
CREATE TABLE IF NOT EXISTS `Countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(2) COLLATE utf8_unicode_ci NOT NULL COMMENT '"IE", for example',
  `en-name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table Solas-Match-Test.Languages
CREATE TABLE IF NOT EXISTS `Languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(3) COLLATE utf8_unicode_ci NOT NULL COMMENT '"en", for example',
  `en-name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.NotificationIntervals
CREATE TABLE IF NOT EXISTS `NotificationIntervals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

REPLACE INTO `NotificationIntervals` (`id`, `name`) VALUES
	(1, "Daily"),
	(2, "Weekly"),
	(3, "Monthly");

-- Dumping structure for table Solas-Match-Test.OrganisationMembers
CREATE TABLE IF NOT EXISTS `OrganisationMembers` (
  `user_id` int(10) unsigned NOT NULL,
  `organisation_id` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `user_id` (`user_id`,`organisation_id`),
  KEY `FK_organisation_member_organisation` (`organisation_id`),
  CONSTRAINT `FK_organisation_member_organisation` FOREIGN KEY (`organisation_id`) REFERENCES `Organisations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_organisation_member_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table Solas-Match-Test.Organisations
CREATE TABLE IF NOT EXISTS `Organisations` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`biography` VARCHAR(4096) NULL COLLATE 'utf8_unicode_ci',
	`home-page` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`e-mail` VARCHAR(128) NULL,
	`address` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`city` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`country` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`regional-focus` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.OrgRequests
CREATE TABLE IF NOT EXISTS `OrgRequests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `org_id` int(10) unsigned NOT NULL,
  `request-datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userRequest` (`user_id`,`org_id`),
  KEY `FK_org_request_queue_organisation` (`org_id`),
  CONSTRAINT `FK_org_request_queue_organisation1` FOREIGN KEY (`org_id`) REFERENCES `Organisations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_org_request_queue_user2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table Solas-Match-Test.OrgTranslatorBlacklist
CREATE TABLE IF NOT EXISTS `OrgTranslatorBlacklist` (
  `org_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `org_id` (`org_id`,`user_id`),
  KEY `FK_OrgTranslatorBlacklist_Users` (`user_id`),
  CONSTRAINT `FK_OrgTranslatorBlacklist_Organisations` FOREIGN KEY (`org_id`) REFERENCES `Organisations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_OrgTranslatorBlacklist_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.PasswordResetRequests
CREATE TABLE IF NOT EXISTS `PasswordResetRequests` (
  `uid` char(40) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `request-time` datetime DEFAULT NULL,
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `FK_password_reset_user1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.ProjectFiles
CREATE TABLE IF NOT EXISTS `ProjectFiles` (
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `filename` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `file-token` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `mime-type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `project_id` (`project_id`),
  KEY `FK_ProjectFiles_Users` (`user_id`),
  CONSTRAINT `FK_ProjectFiles_Projects` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ProjectFiles_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.Projects
CREATE TABLE IF NOT EXISTS `Projects` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`description` VARCHAR(4096) NOT NULL COLLATE 'utf8_unicode_ci',
	`impact` VARCHAR(4096) NOT NULL COLLATE 'utf8_unicode_ci',
	`deadline` DATETIME NOT NULL,
	`organisation_id` INT(10) UNSIGNED NOT NULL,
	`reference` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`word-count` INT(10) UNSIGNED NOT NULL,
	`created` DATETIME NOT NULL,
	`language_id` INT(10) UNSIGNED NOT NULL,
	`country_id` INT(10) UNSIGNED NOT NULL,
    `image_uploaded` BIT(1) DEFAULT 0 NOT NULL,
    `image_approved` BIT(1) DEFAULT 0 NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `organisation_id` (`organisation_id`, `title`, `language_id`, `country_id`),
	INDEX `FK_Projects_Languages` (`language_id`),
	INDEX `FK_Projects_Countries` (`country_id`),
	CONSTRAINT `FK_Projects_Countries` FOREIGN KEY (`country_id`) REFERENCES `Countries` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_Projects_Languages` FOREIGN KEY (`language_id`) REFERENCES `Languages` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_project_organisation` FOREIGN KEY (`organisation_id`) REFERENCES `Organisations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP PROCEDURE IF EXISTS alterTable;
 DELIMITER //
 CREATE PROCEDURE alterTable()
 BEGIN
     IF NOT EXISTS(SELECT 1
                     FROM information_schema.`COLUMNS`
                     WHERE TABLE_SCHEMA = database()
                     AND TABLE_NAME = "Projects"
                     AND COLUMN_NAME = "image_uploaded") then
         ALTER TABLE Projects
             ADD `image_uploaded` BIT(1) DEFAULT 0 NOT NULL;
     END IF;
 END//
 DELIMITER ;
 CALL alterTable();
 DROP PROCEDURE alterTable;

DROP PROCEDURE IF EXISTS alterTable;
 DELIMITER //
 CREATE PROCEDURE alterTable()
 BEGIN
     IF NOT EXISTS(SELECT 1
                     FROM information_schema.`COLUMNS`
                     WHERE TABLE_SCHEMA = database()
                     AND TABLE_NAME = "Projects"
                     AND COLUMN_NAME = "image_approved") then
         ALTER TABLE Projects
             ADD `image_approved` BIT(1) DEFAULT 0 NOT NULL;
     END IF;
 END//
 DELIMITER ;
 CALL alterTable();
 DROP PROCEDURE alterTable;
-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.ProjectTags
CREATE TABLE IF NOT EXISTS `ProjectTags` (
  `project_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `project_id` (`project_id`,`tag_id`),
  KEY `FK_ProjectTags_Tags` (`tag_id`),
  CONSTRAINT `FK_ProjectTags_Projects` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ProjectTags_Tags` FOREIGN KEY (`tag_id`) REFERENCES `Tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table SolasMatch.RegisteredUsers
CREATE TABLE IF NOT EXISTS `RegisteredUsers` (
  `user_id` int(10) unsigned NOT NULL,
  `unique_id` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `FK_RegisteredUsers_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.Statistics
CREATE TABLE IF NOT EXISTS `Statistics` (
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `value` double NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Dumping structure for table Solas-Match-Test.Tags
CREATE TABLE IF NOT EXISTS `Tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `label` (`label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.TaskClaims
CREATE TABLE IF NOT EXISTS `TaskClaims` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `claimed-time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Tasks` (`task_id`,`user_id`),
  KEY `FK_task_claim_user` (`user_id`),
  CONSTRAINT `FK_task_claim_task` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_task_claim_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table SolasMatch.TaskFileVersions

CREATE TABLE IF NOT EXISTS `TaskFileVersions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) unsigned NOT NULL,
  `version_id` int(11) NOT NULL COMMENT 'Gets incremented within the code',
  `filename` text COLLATE utf8_unicode_ci NOT NULL,
  `content-type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL COMMENT 'Null while we don''t have logging in',
  `upload-time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `taskFile` (`task_id`,`version_id`,`user_id`),
  KEY `FK_task_file_version_user` (`user_id`),
  CONSTRAINT `FK_TaskFileVersions_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TaskFileVersions_Tasks` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.TaskPrerequisites
CREATE TABLE IF NOT EXISTS `TaskPrerequisites` (
  `task_id` bigint(20) unsigned NOT NULL,
  `task_id-prerequisite` bigint(20) unsigned NOT NULL,
  UNIQUE KEY `task_id` (`task_id`,`task_id-prerequisite`),
  KEY `FK_TaskPrerequisites_Tasks_2` (`task_id-prerequisite`),
  CONSTRAINT `FK_TaskPrerequisites_Tasks` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TaskPrerequisites_Tasks_2` FOREIGN KEY (`task_id-prerequisite`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.TaskReviews
CREATE TABLE IF NOT EXISTS `TaskReviews` (
  `project_id` int(10) unsigned NOT NULL,
  `task_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `corrections` int(11) unsigned NOT NULL,
  `grammar` int(11) unsigned NOT NULL,
  `spelling` int(11) unsigned NOT NULL,
  `consistency` int(11) unsigned NOT NULL,
  `comment` VARCHAR(2048) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `user_task_project` (`task_id`,`user_id`,`project_id`),
  CONSTRAINT `FK_TaskReviews_Tasks` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TaskReviews_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TaskReviews_Projects` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table Solas-Match-Test.Tasks
CREATE TABLE IF NOT EXISTS `Tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `word-count` int(11) DEFAULT NULL,
  `language_id-source` int(10) unsigned NOT NULL,
  `language_id-target` int(10) unsigned NOT NULL,
  `country_id-source` int(10) unsigned NOT NULL,
  `country_id-target` int(10) unsigned NOT NULL,
  `created-time` datetime NOT NULL,
  `deadline` datetime NOT NULL,
  `comment` varchar(4096) COLLATE utf8_unicode_ci DEFAULT NULL,
  `task-type_id` int(11) unsigned NOT NULL,
  `task-status_id` int(11) unsigned NOT NULL,
  `published` BIT(1) DEFAULT 0 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`,`project_id`,`language_id-source`,`language_id-target`,`country_id-source`,`country_id-target`,`task-type_id`),
  KEY `FK_Tasks_Languages` (`language_id-source`),
  KEY `FK_Tasks_Languages_2` (`language_id-target`),
  KEY `FK_Tasks_Countries` (`country_id-source`),
  KEY `FK_Tasks_Countries_2` (`country_id-target`),
  KEY `FK_Tasks_TaskTypes` (`task-type_id`),
  KEY `FK_Tasks_TaskStatus` (`task-status_id`),
  KEY `FK_Tasks_Projects` (`project_id`),
  CONSTRAINT `FK_Tasks_Countries` FOREIGN KEY (`country_id-source`) REFERENCES `Countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_Tasks_Countries_2` FOREIGN KEY (`country_id-target`) REFERENCES `Countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_Tasks_Languages` FOREIGN KEY (`language_id-source`) REFERENCES `Languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_Tasks_Languages_2` FOREIGN KEY (`language_id-target`) REFERENCES `Languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_Tasks_Projects` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_Tasks_TaskStatus` FOREIGN KEY (`task-status_id`) REFERENCES `TaskStatus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_Tasks_TaskTypes` FOREIGN KEY (`task-type_id`) REFERENCES `TaskTypes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


CREATE TABLE IF NOT EXISTS `TaskNotificationSent` (
  `task_id` BIGINT(20) UNSIGNED NOT NULL,
  `notification` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`task_id`),
  CONSTRAINT `FK_TaskNotificationSent_Tasks` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Dumping structure for table Solas-Match-Test.TaskStatus
CREATE TABLE IF NOT EXISTS `TaskStatus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

REPLACE INTO `TaskStatus` (`id`, `name`) VALUES
	(1, "Waiting PreReqs"),
	(2, "Pending Claim"),
	(3, "In Progress"),
	(4, "Complete");


-- Dumping structure for table Solas-Match-Test.TaskTranslatorBlacklist
CREATE TABLE IF NOT EXISTS `TaskTranslatorBlacklist` (
  `task_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `revoked_by_admin` BIT(1) DEFAULT 0 NOT NULL,
  UNIQUE KEY `task_id` (`task_id`,`user_id`),
  KEY `FK_TaskTranslatorBlacklist_Users` (`user_id`),
  CONSTRAINT `FK_TaskTranslatorBlacklist_Tasks` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TaskTranslatorBlacklist_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.TaskTypes
CREATE TABLE IF NOT EXISTS `TaskTypes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

REPLACE INTO `TaskTypes` (`id`, `name`) VALUES
	(1, "Segmentation"),
	(2, "Translation"),
	(3, "Proofreading"),
	(4, "Desegmentation");

-- Structure of table TaskUnclaims
CREATE TABLE IF NOT EXISTS `TaskUnclaims` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` BIGINT(20) UNSIGNED NOT NULL,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `unclaim-comment` VARCHAR(4096),
  `unclaimed-time` DATETIME NOT NULL,
  `task_is_archived` BIT(1) DEFAULT 0 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Tasks` (`task_id`, `user_id`, `unclaimed-time`),
  KEY `FK_task_unclaim_user` (`user_id`),
  CONSTRAINT `FK_task_unclaim_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping structure for table Solas-Match-Test.UserBadges
CREATE TABLE IF NOT EXISTS `UserBadges` (
  `user_id` int(10) unsigned NOT NULL,
  `badge_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`badge_id`),
  UNIQUE KEY `userBadge` (`user_id`,`badge_id`),
  KEY `FK_user_badges_badges` (`badge_id`),
  CONSTRAINT `FK_user_badges_badges` FOREIGN KEY (`badge_id`) REFERENCES `Badges` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_badges_users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table debug-test3.UserLogins
CREATE TABLE IF NOT EXISTS `UserLogins` (
  `user_id` int(10) unsigned DEFAULT NULL,
  `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `success` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `login-time` datetime NOT NULL,
  KEY `FK_UserLogins_Users` (`user_id`),
  CONSTRAINT `FK_UserLogins_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 

-- Dumping structure for table Solas-Match-Test.UserNotifications
CREATE TABLE IF NOT EXISTS `UserNotifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `task_id` bigint(20) unsigned NOT NULL,
  `created-time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_task_id` (`user_id`,`task_id`),
  KEY `FK_user_notifications_task` (`task_id`),
  CONSTRAINT `FK_user_notifications_task1` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_notifications_user1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping structure for table big-merge.UserPersonalInformation
CREATE TABLE IF NOT EXISTS `UserPersonalInformation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `first-name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last-name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile-number` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `business-number` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `language-preference` INT(10) UNSIGNED DEFAULT NULL,
  `job-title` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `receive_credit` BIT(1) DEFAULT 0 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `FK_UserPersonalInformation_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_UserPersonalInformation_Languages` FOREIGN KEY (`language-preference`) REFERENCES `Languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF EXISTS(SELECT 1 FROM information_schema.`COLUMNS`
              WHERE TABLE_SCHEMA = database()
              AND TABLE_NAME = "UserPersonalInformation"
              AND COLUMN_NAME = "sip"
              AND DATA_TYPE = 'VARCHAR'
              AND CHARACTER_MAXIMUM_LENGTH = 128) THEN
        ALTER TABLE UserPersonalInformation DROP COLUMN sip;
    END IF;
END//
DELIMITER ;
CALL alterTable();
DROP PROCEDURE alterTable;

DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF NOT EXISTS(SELECT 1 FROM information_schema.`COLUMNS`
              WHERE TABLE_SCHEMA = database()
              AND TABLE_NAME = "UserPersonalInformation"
              AND COLUMN_NAME = "language-preference") THEN
        ALTER TABLE UserPersonalInformation ADD `language-preference` INT(10) UNSIGNED DEFAULT NULL;
        ALTER TABLE UserPersonalInformation ADD CONSTRAINT `FK_UserPersonalInformation_Languages` FOREIGN KEY (`language-preference`) REFERENCES `Languages` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
    END IF;
END//
DELIMITER ;
CALL alterTable();
DROP PROCEDURE alterTable;

-- Data exporting was unselected.

-- Dumping structure for table Solas-Match-Test.Users
CREATE TABLE IF NOT EXISTS `Users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `display-name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `password` char(128) COLLATE utf8_unicode_ci NOT NULL,
  `biography` text COLLATE utf8_unicode_ci,
  `language_id` int(10) unsigned DEFAULT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  `nonce` int(11) unsigned NOT NULL,
  `created-time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `user_pass` (`password`),
  KEY `FK_user_language` (`language_id`),
  KEY `FK_user_country` (`country_id`),
  CONSTRAINT `FK_user_country` FOREIGN KEY (`country_id`) REFERENCES `Countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_language` FOREIGN KEY (`language_id`) REFERENCES `Languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table Solas-Match-Test.UserSecondaryLanguages
CREATE TABLE IF NOT EXISTS `UserSecondaryLanguages` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT(10) UNSIGNED NOT NULL,
	`language_id` INT(10) UNSIGNED NOT NULL,
	`country_id` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `user_id` (`user_id`, `language_id`, `country_id`),
	CONSTRAINT `FK_UserSecondaryLanguages_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_UserSecondaryLanguages_Languages` FOREIGN KEY (`language_id`) REFERENCES `Languages` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_UserSecondaryLanguages_Countries` FOREIGN KEY (`country_id`) REFERENCES `Countries` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Dumping structure for table Solas-Match-Test.UserTags
CREATE TABLE IF NOT EXISTS `UserTags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userTag` (`user_id`,`tag_id`),
  KEY `FK_user_tag_user1` (`tag_id`),
  CONSTRAINT `FK_user_tag_tag1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_tag_user1` FOREIGN KEY (`tag_id`) REFERENCES `Tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.UserTaskScores
CREATE TABLE IF NOT EXISTS `UserTaskScores` (
  `user_id` int(10) unsigned NOT NULL,
  `task_id` bigint(20) unsigned NOT NULL,
  `score` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`user_id`,`task_id`),
  UNIQUE KEY `taskScore` (`task_id`,`user_id`),
  CONSTRAINT `FK_user_task_score_task1` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_task_score_user1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.UserTaskStreamNotifications
CREATE TABLE IF NOT EXISTS `UserTaskStreamNotifications` (
  `user_id` int(10) unsigned NOT NULL,
  `interval` int(10) unsigned NOT NULL,
  `strict` int(1) NOT NULL DEFAULT '0',
  `last-sent` DATETIME DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `FK_user_task_stream_notification_user1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_task_stream_notification_interval1` FOREIGN KEY (`interval`) REFERENCES `NotificationIntervals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.UserTrackedProjects
CREATE TABLE IF NOT EXISTS `UserTrackedProjects` (
	`user_id` INT(10) UNSIGNED NOT NULL,
	`Project_id` INT(10) UNSIGNED NOT NULL,
	UNIQUE INDEX `user_id` (`user_id`, `Project_id`),
	INDEX `FK_UserTrackedProjects_Projects` (`Project_id`),
	CONSTRAINT `FK_UserTrackedProjects_Projects` FOREIGN KEY (`Project_id`) REFERENCES `Projects` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_UserTrackedProjects_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.UserTrackedTasks
CREATE TABLE IF NOT EXISTS `UserTrackedTasks` (
	`user_id` INT(10) UNSIGNED NOT NULL,
	`task_id` BIGINT(20) UNSIGNED NOT NULL,
	UNIQUE INDEX `user_id` (`user_id`, `task_id`),
	INDEX `FK_UserTrackedTasks_Tasks` (`task_id`),
	CONSTRAINT `FK_UserTrackedTasks_Tasks` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_UserTrackedTasks_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping structure for table Solas-Match-UserTrackedOrganisations
CREATE TABLE IF NOT EXISTS `UserTrackedOrganisations` (
    `user_id` INT(10) UNSIGNED NOT NULL,
    `organisation_id` INT(10) UNSIGNED NOT NULL,
    `created` datetime NOT NULL,
    UNIQUE INDEX `user_id` (`user_id`, `organisation_id`),
    INDEX `FK_UserTrackedOrganisations_Organisations` (`organisation_id`),
    CONSTRAINT `FK_UserTrackedOrganisations_Organisations` FOREIGN KEY (`organisation_id`) REFERENCES `Organisations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `FK_UserTrackedOrganisations_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Structure of table TaskViews
CREATE TABLE IF NOT EXISTS `TaskViews` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` BIGINT(20) UNSIGNED NOT NULL,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `viewed-time` DATETIME NOT NULL,
  `task_is_archived` BIT(1) DEFAULT 0 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `TaskViewTimeStamps` (`task_id`, `user_id`, `viewed-time`),
  KEY `FK_task_viewed_user` (`user_id`),
  CONSTRAINT `FK_task_viewed_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.

/*---------------------------------------end of tables---------------------------------------------*/

SET FOREIGN_KEY_CHECKS=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
