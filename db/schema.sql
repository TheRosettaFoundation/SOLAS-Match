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

/*--------------------START OF ALTER TABLES TO NORMALISE DATA LENGTHS/TYPES -----------------------*/
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "OrgRequests"
                                        AND COLUMN_NAME = "user_id"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE OrgRequests CHANGE `user_id` `user_id` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "PasswordResetRequests"
                                        AND COLUMN_NAME = "user_id"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE PasswordResetRequests CHANGE `user_id` `user_id` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "TaskClaims"
                                        AND COLUMN_NAME = "user_id"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE TaskClaims CHANGE `user_id` `user_id` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "TaskFileVersions"
                                        AND COLUMN_NAME = "user_id"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE TaskFileVersions CHANGE `user_id` `user_id` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "TaskUnclaims"
                                        AND COLUMN_NAME = "user_id"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE TaskUnclaims CHANGE `user_id` `user_id` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "UserBadges"
                                        AND COLUMN_NAME = "user_id"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE UserBadges CHANGE `user_id` `user_id` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "UserNotifications"
                                        AND COLUMN_NAME = "user_id"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE UserNotifications CHANGE `user_id` `user_id` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "UserTags"
                                        AND COLUMN_NAME = "user_id"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE UserTags CHANGE `user_id` `user_id` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "UserTaskScores"
                                        AND COLUMN_NAME = "user_id"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE UserTaskScores CHANGE `user_id` `user_id` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "UserTaskStreamNotifications"
                                        AND COLUMN_NAME = "user_id"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE UserTaskStreamNotifications CHANGE `user_id` `user_id` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "ArchivedTasks"
                                        AND COLUMN_NAME = "country_id-source"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE ArchivedTasks CHANGE `country_id-source` `country_id-source` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "ArchivedTasks"
                                        AND COLUMN_NAME = "country_id-target"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE ArchivedTasks CHANGE `country_id-target` `country_id-target` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "Tasks"
                                        AND COLUMN_NAME = "country_id-source"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE Tasks CHANGE `country_id-source` `country_id-source` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "Tasks"
                                        AND COLUMN_NAME = "country_id-target"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE Tasks CHANGE `country_id-target` `country_id-target` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "TaskTranslatorBlacklist"
                                        AND COLUMN_NAME = "task_id"
                                        AND COLUMN_TYPE = 'BIGINT(10) UNSIGNED') then
                    ALTER TABLE TaskTranslatorBlacklist CHANGE `task_id` `task_id` BIGINT(20) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "UserNotifications"
                                        AND COLUMN_NAME = "task_id"
                                        AND COLUMN_TYPE = 'BIGINT(11) UNSIGNED') then
                    ALTER TABLE UserNotifications CHANGE `task_id` `task_id` BIGINT(20) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "UserTaskScores"
                                        AND COLUMN_NAME = "task_id"
                                        AND COLUMN_TYPE = 'BIGINT(11) UNSIGNED') then
                    ALTER TABLE UserTaskScores CHANGE `task_id` `task_id` BIGINT(20) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "UserTrackedTasks"
                                        AND COLUMN_NAME = "task_id"
                                        AND COLUMN_TYPE = 'BIGINT(10) UNSIGNED') then
                    ALTER TABLE UserTrackedTasks CHANGE `task_id` `task_id` BIGINT(20) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "ArchivedTasks"
                                        AND COLUMN_NAME = "language_id-source"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE ArchivedTasks CHANGE `language_id-source` `language_id-source` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "ArchivedTasks"
                                        AND COLUMN_NAME = "language_id-target"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE ArchivedTasks CHANGE `language_id-target` `language_id-target` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "Tasks"
                                        AND COLUMN_NAME = "language_id-source"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE Tasks CHANGE `language_id-source` `language_id-source` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "Tasks"
                                        AND COLUMN_NAME = "language_id-target"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE Tasks CHANGE `language_id-target` `language_id-target` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "OrgRequests"
                                        AND COLUMN_NAME = "org_id"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE OrgRequests CHANGE `org_id` `org_id` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "ArchivedTasks"
                                        AND COLUMN_NAME = "project_id"
                                        AND COLUMN_TYPE = 'INT(20) UNSIGNED') then
                    ALTER TABLE ArchivedTasks CHANGE `project_id` `project_id` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "Tasks"
                                        AND COLUMN_NAME = "project_id"
                                        AND COLUMN_TYPE = 'INT(20) UNSIGNED') then
                    ALTER TABLE Tasks CHANGE `project_id` `project_id` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "UserTags"
                                        AND COLUMN_NAME = "tag_id"
                                        AND COLUMN_TYPE = 'INT(11) UNSIGNED') then
                    ALTER TABLE UserTags CHANGE `tag_id` `tag_id` INT(10) UNSIGNED NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;
                 
DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "ArchivedTasks"
                                        AND COLUMN_NAME = "published"
                                        AND DATA_TYPE = 'VARCHAR'
                                        AND CHARACTER_MAXIMUM_LENGTH = 50) then
                    ALTER TABLE ArchivedTasks MODIFY `published` BIT(1) DEFAULT 0 NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;

DROP PROCEDURE IF EXISTS alterTable;
                 DELIMITER //
                 CREATE PROCEDURE alterTable()
                 BEGIN
                    IF EXISTS(SELECT 1
                                        FROM information_schema.`COLUMNS`
                                        WHERE TABLE_SCHEMA = database()
                                        AND TABLE_NAME = "Tasks"
                                        AND COLUMN_NAME = "published"
                                        AND DATA_TYPE = 'VARCHAR'
                                        AND CHARACTER_MAXIMUM_LENGTH = 50) then
                    ALTER TABLE Tasks MODIFY `published` BIT(1) DEFAULT 0 NOT NULL;
                    END IF;
                 END//
                 DELIMITER ;
                 CALL alterTable();
                 DROP PROCEDURE alterTable;

/*-------------------------------------------END OF ALTER TABLES-----------------------------------*/

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

/*---------------------------------------start of procs--------------------------------------------*/

-- Dumping structure for procedure Solas-Match-Test.acceptMemRequest
DROP PROCEDURE IF EXISTS `acceptMemRequest`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `acceptMemRequest`(IN `uID` INT, IN `orgID` INT)
BEGIN
    IF NOT EXISTS (SELECT user_id
                    FROM OrganisationMembers
                    WHERE user_id = uID
                    AND organisation_id = orgID) then
    	INSERT INTO OrganisationMembers (user_id, organisation_id) VALUES (uID,orgID);
        if EXISTS (SELECT user_id
                FROM OrgRequests
                WHERE user_id = uID
                AND org_id = orgID) then
    	    call removeMembershipRequest(uID,orgID);
        else
            SELECT 1 as result;
        end if;
    else
        select 0 as result;
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure debug-test3.addAdmin
DROP PROCEDURE IF EXISTS `addAdmin`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `addAdmin`(IN `userId` INT, IN `orgId` INT)
BEGIN
	IF orgId = '' THEN SET orgId=NULL; END IF;

	IF NOT EXISTS (SELECT 1 FROM Admins a WHERE a.user_id=userId and a.organisation_id=orgId) THEN	
		INSERT INTO Admins (user_id,organisation_id) VALUES(userId,orgId);
	END IF;	
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.addBadge
DROP PROCEDURE IF EXISTS `addBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `addBadge`(IN `ownerID` INT, IN `name` VARCHAR(50), IN `disc` VARCHAR(50))
BEGIN
if not exists (select 1 from Badges b where b.title=name and b.description=disc and b.owner_id=ownerID) then
	insert into Badges (owner_id,title,description) values (ownerID,name,disc);
	select 1 as result;
else
	select 0 as result;
end if;

END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.addPasswordResetRequest
DROP PROCEDURE IF EXISTS `addPasswordResetRequest`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `addPasswordResetRequest`(IN `uniqueId` CHAR(40), IN `userId` INT)
BEGIN
    INSERT INTO PasswordResetRequests (uid, user_id, `request-time`) VALUES (uniqueId,userId,NOW());
    SELECT 1 AS result;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.addProjectFile
DROP PROCEDURE IF EXISTS `addProjectFile`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `addProjectFile`(IN `pID` INT, IN `uID` INT, IN `fname` VARCHAR(128), IN `token` VARCHAR(128), IN `mime` varCHAR(128))
    MODIFIES SQL DATA
BEGIN
	if not exists (select 1 from ProjectFiles pf where pf.project_id=pID) then
		insert into ProjectFiles (project_id,user_id,filename,`file-token`, `mime-type`) values(pID,uID,fName,token, mime);
		select 1 as result;
	else
		select 0 as result;
	end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.addProjectTag
DROP PROCEDURE IF EXISTS `addProjectTag`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `addProjectTag`(IN `projectID` INT, IN `tagID` INT)
    MODIFIES SQL DATA
BEGIN
    if not exists (select 1 from ProjectTags where project_id=projectID and tag_id =tagID) then
	    insert into ProjectTags  (project_id,tag_id) values (projectID,tagID);
    	select 1 as result;
    else
	    select 0 as result;
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.addTaskPreReq
DROP PROCEDURE IF EXISTS `addTaskPreReq`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `addTaskPreReq`(IN `taskId` INT, IN `preReqId` INT)
    MODIFIES SQL DATA
BEGIN
	if not exists( select 1 from TaskPrerequisites tp where tp.task_id=taskID and tp.`task_id-prerequisite`= preReqId) then
       INSERT INTO TaskPrerequisites (`task_id`, `task_id-prerequisite`)
            VALUES (taskId, preReqId);
       	select 1 as "result";
    else
   	    select 0 as "result";
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.addUserToTaskBlacklist
DROP PROCEDURE IF EXISTS `addUserToTaskBlacklist`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `addUserToTaskBlacklist`(IN `userId` INT, IN `taskId` INT)
    MODIFIES SQL DATA
BEGIN
	if not exists(SELECT 1
                    FROM TaskTranslatorBlacklist
                    WHERE user_id = userId
                    AND task_id = taskId) then
        INSERT INTO TaskTranslatorBlacklist (task_id, user_id)
            VALUES (taskId, userId);
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure debug-test.archiveProject
DROP PROCEDURE IF EXISTS `archiveProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `archiveProject`(IN `projectId` INT, IN `user_id` INT)
    MODIFIES SQL DATA
BEGIN
	Declare taskId int;
	DECLARE done INT DEFAULT FALSE;
	DECLARE cur1 CURSOR FOR SELECT t.id FROM Tasks t WHERE t.project_id=projectId;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	
		 
	if not exists(select 1 from ArchivedProjects where id = projectId) then		

		set @`userIdProjectCreator` = null;
		set @`filename` = null;
		set @`fileToken` = null;
		set @`mimeType` = null;
		set @`projectTags` = null;
		
		SELECT pf.user_id INTO @`userIdProjectCreator` FROM ProjectFiles pf WHERE pf.project_id=projectId;
		SELECT pf.filename INTO @`filename` FROM ProjectFiles pf WHERE pf.project_id=projectId;
		SELECT pf.`file-token` INTO @`fileToken` FROM ProjectFiles pf WHERE pf.project_id=projectId;
		SELECT pf.`mime-type` INTO @`mimeType` FROM ProjectFiles pf WHERE pf.project_id=projectId;
		SELECT GROUP_CONCAT(t.label) INTO @`projectTags` FROM Tags t JOIN ProjectTags pt ON t.id = pt.tag_id WHERE pt.project_id=projectId;
			
		START TRANSACTION;
		INSERT INTO `ArchivedProjects` (id, title, description, impact, deadline, organisation_id, reference, `word-count`, created,language_id, country_id, image_uploaded, image_approved)
		SELECT *
		FROM Projects p
		WHERE p.id=projectId;
		
		INSERT INTO `ArchivedProjectsMetadata` (`archivedProject_id`,`user_id-archived`,`user_id-projectCreator`,`filename`,`file-token`,`mime-type`,`archived-date`,`tags`)
		VALUES (projectId,user_id,@`userIdProjectCreator`,@`filename`,@`fileToken`,@`mimeType`,NOW(),@`projectTags`);
		
		OPEN cur1;
		
		read_loop: LOOP
			FETCH cur1 INTO taskId;
			IF done THEN
			 	LEAVE read_loop;
			END IF;
         call archiveTask(taskId, user_id);
		END LOOP;
		CLOSE cur1;
			  
		
		OPEN cur1;
		
		read_loop: LOOP
			FETCH cur1 INTO taskId;
			IF done THEN
			 	LEAVE read_loop;
			END IF;
         call deleteTask(taskId);
		END LOOP;
		CLOSE cur1;	
		
		DELETE FROM Projects WHERE id=projectId;
		
		COMMIT;
	    SELECT 1 AS result;
   ELSE
      SELECT 0 AS result;
   END IF;	
	  
END//
DELIMITER ;

-- Dumping structure for procedure debug-test.archiveTask
DROP PROCEDURE IF EXISTS `archiveTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `archiveTask`(IN `tID` INT, IN `uID` INT)
    MODIFIES SQL DATA
BEGIN

	if not exists(select 1 from ArchivedTasks where id = tID) then
		
		set @`version` = null;
		set @`filename` = null;
		set @`contentType` = null;
		set @`userIdClaimed` = null;
		set @`preRequisites` = null;
		set @`userIdTaskCreator` = null;
		set @`uploadTime` = null;
	
		SELECT MAX(tf.version_id) INTO @`version` FROM TaskFileVersions tf WHERE tf.task_id=tID;
		SELECT `filename` INTO @`filename` FROM TaskFileVersions tf WHERE tf.task_id=tID LIMIT 1;
		SELECT `content-type` INTO @`contentType` FROM TaskFileVersions tf WHERE tf.task_id=tID LIMIT 1;
		SELECT `upload-time` INTO @`uploadTime` FROM TaskFileVersions tf WHERE tf.task_id=tID LIMIT 1;
		SELECT tc.`user_id` INTO @`userIdClaimed` FROM TaskClaims tc WHERE tc.`task_id` = tID LIMIT 1; 
		SELECT GROUP_CONCAT(p.`task_id-prerequisite`) INTO @`preRequisites` FROM TaskPrerequisites p WHERE p.task_id=tID;
		SELECT tf.user_id INTO @`userIdTaskCreator` FROM TaskFileVersions tf WHERE tf.task_id=tID AND tf.version_id=0 LIMIT 1;
	
		START TRANSACTION;
			INSERT INTO `ArchivedTasks` (`id`, `project_id`, `title`, `word-count`, `language_id-source`, `language_id-target`, `country_id-source`, `country_id-target`, `created-time`, `deadline`, `comment`, `taskType_id`, `taskStatus_id`, `published`)
			SELECT t.* FROM Tasks t WHERE t.id = tID;
			
			INSERT INTO ArchivedTasksMetadata 
			(`archivedTask_id`,`version`,`filename`,`content-type`,`user_id-claimed`,`user_id-archived`,`prerequisites`,`user_id-taskCreator`,`upload-time`,`archived-date`) 
	
			VALUES
			(tID, @`version`,@`filename`,@`contentType`,@`userIdClaimed`,uID,@`prerequisites`,@`userIdTaskCreator`,@`uploadTime`,NOW());
            IF EXISTS(SELECT 1 FROM TaskUnclaims WHERE task_id = tID) THEN
                UPDATE TaskUnclaims tuc SET tuc.task_is_archived = 1 WHERE tuc.task_id = tID;
            END IF;
            IF EXISTS(SELECT 1 FROM TaskViews WHERE task_id = tID) THEN
                UPDATE TaskViews tvs SET tvs.task_is_archived = 1 WHERE tvs.task_id = tID;
            END IF;
		COMMIT;
	   select 1 as result;
   else
      select 0 as result;
   end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.assignBadge
DROP PROCEDURE IF EXISTS `assignBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `assignBadge`(IN `uid` INT, IN `bid` INT)
BEGIN
if not EXISTS (select 1 from UserBadges where user_id=uid and badge_id=bid) then
	INSERT INTO UserBadges (user_id, badge_id) VALUES (uid,bid);
	select 1 as result;
else
	select 0 as result;
end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.badgeInsertAndUpdate
DROP PROCEDURE IF EXISTS `badgeInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `badgeInsertAndUpdate`(IN `badgeID` INT, IN `ownerID` INT, IN `name` VARCHAR(50), IN `disc` MEDIUMTEXT)
BEGIN
	if badgeID='' then set badgeID=null;end if;
	if ownerID='' then set ownerID=null;end if;
	if name='' then set name=null;end if;
	if disc='' then set disc=null;end if;
	
	if not exists (select 1 from Badges b where b.id = badgeID) then
		insert into Badges (owner_id,title,description) values (ownerID,name,disc);
		CALL getBadge(LAST_INSERT_ID(), null, null, null);
	else
		update Badges bg set bg.title = name, bg.description = disc
		where bg.id = badgeID;
		CALL getBadge(BadgeID, null, null, null);
	end if;
END//
DELIMITER ;

-- Dumping structure for procedure debug-test3.bannedOrgInsert
DROP PROCEDURE IF EXISTS `bannedOrgInsert`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `bannedOrgInsert`(IN `orgId` INT, IN `userIdAdmin` INT, IN `bannedTypeId` INT, IN `adminComment` VARCHAR(4096))
BEGIN

	Declare userId int;
	DECLARE done INT DEFAULT FALSE;
	DECLARE cur1 CURSOR FOR SELECT m.user_id FROM OrganisationMembers m WHERE  m.organisation_id=orgId AND m.user_id NOT IN (SELECT s.user_id FROM Admins s WHERE s.organisation_id IS NULL);
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

	IF NOT EXISTS(SELECT 1 FROM BannedOrganisations b WHERE b.org_id = orgId) THEN
		if orgId='' then set orgId=null;end if;
		if userIdAdmin='' then set userIdAdmin=null;end if;
		if bannedTypeId='' then set bannedTypeId=null;end if;
		if adminComment='' then set adminComment=null;end if;
	
	 	START TRANSACTION;
		INSERT INTO BannedOrganisations (org_id,`user_id-admin`,`bannedtype_id`,`comment`,`banned-date`)
		VALUES (orgId, userIdAdmin, bannedTypeId, adminComment,NOW());
		
		set @orgName = null;
		SELECT o.name INTO @orgName FROM Organisations o WHERE o.id=orgId;
		
		OPEN cur1;
		
		read_loop: LOOP
			FETCH cur1 INTO userId;
			IF done THEN
			 	LEAVE read_loop;
			END IF;
	      call bannedUserInsert(userId, userIdAdmin, bannedTypeId, Concat('You have been banned because the organisation ',@Orgname,' has been banned. ', adminComment));
		END LOOP;
		CLOSE cur1;
		COMMIT;
		
	END IF;

END//
DELIMITER ;

-- Dumping structure for procedure debug-test3.bannedUserInsert
DROP PROCEDURE IF EXISTS `bannedUserInsert`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `bannedUserInsert`(IN `userId` INT, IN `userIdAdmin` INT, IN `bannedTypeId` INT, IN `adminComment` VARCHAR(4096))
BEGIN

	if userId='' then set userId=null;end if;
	if userIdAdmin='' then set userIdAdmin=null;end if;
	if bannedTypeId='' then set bannedTypeId=null;end if;
	if adminComment='' then set adminComment=null;end if;
	
	IF NOT EXISTS (SELECT 1 FROM BannedUsers b WHERE b.user_id=userId) THEN
		INSERT INTO BannedUsers (`user_id`,`user_id-admin`,`bannedtype_id`,`comment`,`banned-date`)
		VALUES (userId, userIdAdmin, bannedTypeId, adminComment,NOW());
	END IF;

END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Test.claimTask
DROP PROCEDURE IF EXISTS `claimTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `claimTask`(IN `tID` INT, IN `uID` INT)
BEGIN
	if not EXISTS(select 1 from TaskClaims tc where tc.task_id=tID and tc.user_id=uID) then
		START TRANSACTION;
		insert into TaskClaims  (task_id,user_id,`claimed-time`) values (tID,uID,now());
		update Tasks set `task-status_id`=3 where id = tID;
		COMMIT;
		select 1 as result;
	else
	select 0 as result;
	end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.deleteBadge
DROP PROCEDURE IF EXISTS `deleteBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteBadge`(IN `id` INT)
BEGIN
	IF EXISTS(SELECT b.id FROM Badges b WHERE b.id = id) THEN
		DELETE FROM Badges WHERE Badges.id = id;
		SELECT 1 AS result;
	ELSE
		SELECT 0 AS result;
	END IF;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.deleteOrg
DROP PROCEDURE IF EXISTS `deleteOrg`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteOrg`(IN `id` INT)
BEGIN
if EXISTS (select 1 from Organisations o where o.id=id) then
	delete from Organisations where Organisations.id=id;
	select 1 as result;
else
	select 0 as result;
end if;
END//
DELIMITER ;


-- Dumping structure for procedure debug-test3.deleteProject
DROP PROCEDURE IF EXISTS `deleteProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteProject`(IN `projectId` INT)
BEGIN
	IF EXISTS(SELECT 1 FROM Projects p WHERE p.id = projectId) THEN	
		DELETE FROM Projects WHERE id = projectId;
		SELECT 1 AS result;
	ELSE
		SELECT 0 AS result;
	END IF;
END//
DELIMITER ;


-- Dumping structure for procedure debug-test3.deleteProjectTags
DROP PROCEDURE IF EXISTS `deleteProjectTags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteProjectTags`(IN `projectId` INT)
BEGIN

	IF EXISTS (SELECT 1 FROM ProjectTags p WHERE p.project_id = projectId) THEN
		DELETE FROM ProjectTags WHERE project_id = projectId;
		SELECT 1 AS result;
	ELSE
		SELECT 0 AS result;
	END IF;

END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.deleteTag
DROP PROCEDURE IF EXISTS `deleteTag`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteTag`(IN `tagID` INT)
BEGIN
    if EXISTS (select 1 from Tags where Tags.id=tagID) then
	    delete from Tags where Tags.id=tagID;
    	select 1 as result;
    else
	    select 0 as result;
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.deleteTask
DROP PROCEDURE IF EXISTS `deleteTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteTask`(IN `id` INT)
BEGIN
    if EXISTS (select 1 from Tasks where Tasks.id=id) then
	    delete from Tasks where Tasks.id=id;
    	select 1 as result;
    else
	    select 0 as result;
    end if;
END//
DELIMITER ;

-- Dumping structure for procedure debug-test3.deleteUser
DROP PROCEDURE IF EXISTS `deleteUser`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteUser`(IN `userId` INT)
BEGIN
    if EXISTS (select 1 from Users where Users.id = userId) then
	    delete from Users where Users.id = userId;
        select 1 as result;
    else
        select 0 as result;
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.findOrganisation
DROP PROCEDURE IF EXISTS `findOrganisation`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `findOrganisation`(IN `id` INT)
    COMMENT 'finds an organisation by the data passed in.'
BEGIN
	SELECT o.id, o.name, o.biography, o.`home-page` as homepage, o.`e-mail` as email, o.address, o.city, o.country,
        o.`regional-focus` as regionalFocus
	FROM Organisations o
	WHERE o.id=id; 
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.findOrganisationsUserBelongsTo
DROP PROCEDURE IF EXISTS `findOrganisationsUserBelongsTo`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `findOrganisationsUserBelongsTo`(IN `id` INT)
BEGIN
	IF EXISTS (SELECT * FROM Admins a WHERE a.organisation_id is null and a.user_id=id) THEN
		call getOrg(null,null,null,null,null,null,null,null,null);
	ELSE		
		SELECT o.id, o.name, o.biography, o.`home-page` as homepage, o.`e-mail` as email, o.address, o.city, o.country,
        o.`regional-focus` as regionalFocus
		FROM OrganisationMembers om join Organisations o on om.organisation_id=o.id
		WHERE om.user_id = id
		UNION
		SELECT o.id, o.name, o.biography, o.`home-page` as homepage, o.`e-mail` as email, o.address, o.city, o.country,
        o.`regional-focus` as regionalFocus
		FROM Organisations o
		JOIN Admins a ON
		a.organisation_id=o.id
		WHERE a.user_id=id;
	END IF;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Test.finishRegistration
DROP PROCEDURE IF EXISTS `finishRegistration`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `finishRegistration`(IN `userId` INT)
BEGIN
    if exists (SELECT 1
                FROM RegisteredUsers
                WHERE user_id = userId) then
        DELETE FROM RegisteredUsers
            WHERE user_id = userId;
        SELECT 1 as result;
    else
        SELECT 0 as result;
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.finishRegistrationManually
DROP PROCEDURE IF EXISTS `finishRegistrationManually`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `finishRegistrationManually`(IN `emailToVerify` VARCHAR(128))
BEGIN
    SET @ru_user_id = -99999;
    SELECT ru.user_id INTO @ru_user_id FROM Users u, RegisteredUsers ru WHERE u.id=ru.user_id AND u.email=emailToVerify LIMIT 1;
    DELETE FROM RegisteredUsers WHERE user_id=@ru_user_id;
    IF ROW_COUNT() > 0 THEN
        SELECT @ru_user_id as result;
    ELSE
        SELECT 0 as result;
    END IF;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getActiveLanguages
DROP PROCEDURE IF EXISTS `getActiveLanguages`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getActiveLanguages`()
BEGIN
    SELECT `en-name` as language, code, id
        FROM Languages
        WHERE id IN (SELECT `language_id-source`
                        FROM Tasks
                        WHERE published = 1 AND `task-status_id` = 2)
        OR id IN (SELECT `language_id-target`
                        FROM Tasks
                        WHERE published = 1 AND `task-status_id` = 2);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Dev.getActiveSourceLanguages
DROP PROCEDURE IF EXISTS `getActiveSourceLanguages`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getActiveSourceLanguages`()
 READS SQL DATA
BEGIN
    SELECT `en-name` as name, code, id
        FROM Languages
        WHERE id IN (SELECT `language_id-source`
                        FROM Tasks
                        WHERE published = 1 AND `task-status_id` = 2);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Dev.getActiveTargetLanguages
DROP PROCEDURE IF EXISTS `getActiveTargetLanguages`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getActiveTargetLanguages`()
    READS SQL DATA
BEGIN
    SELECT `en-name` as name, code, id
        FROM Languages
        WHERE id IN (SELECT `language_id-target`
                        FROM Tasks
                        WHERE published = 1 AND `task-status_id` = 2);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Dev.getAdmins
DROP PROCEDURE IF EXISTS `getAdmins`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getAdmins`(IN `orgId` INT)
BEGIN

	IF orgId = null OR orgId = '' THEN SET orgId = NULL; END IF;	
	
	SELECT u.id,u.`display-name` as display_name,u.email,u.password,u.biography, 
            (SELECT `en-name` FROM Languages l WHERE l.id = u.`language_id`) AS `languageName`, 
            (SELECT code FROM Languages l WHERE l.id = u.`language_id`) AS `languageCode`, 
            (SELECT `en-name` FROM Countries c WHERE c.id = u.`country_id`) AS `countryName`, 
            (SELECT code FROM Countries c WHERE c.id = u.`country_id`) AS `countryCode`, 
            u.nonce,u.`created-time` as created_time 

        FROM Users u JOIN Admins a ON a.user_id = u.id 
        
        WHERE (a.organisation_id is null or a.organisation_id = orgId);
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.getAdmin
DROP PROCEDURE IF EXISTS `getAdmin`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getAdmin`(IN `userId` INT, IN `orgId` INT)
BEGIN

	IF userId = null OR userId = '' THEN SET userId = NULL; END IF;
	IF orgId = null OR orgId = '' THEN SET orgId = NULL; END IF;	
	
	IF userId IS NOT null AND orgId IS NOT null THEN
		SELECT u.id,u.`display-name` as display_name,u.email,u.password,u.biography, 
		    (SELECT `en-name` FROM Languages l WHERE l.id = u.`language_id`) AS `languageName`, 
		    (SELECT code FROM Languages l WHERE l.id = u.`language_id`) AS `languageCode`, 
		    (SELECT `en-name` FROM Countries c WHERE c.id = u.`country_id`) AS `countryName`, 
		    (SELECT code FROM Countries c WHERE c.id = u.`country_id`) AS `countryCode`, 
		    u.nonce,u.`created-time` as created_time

		FROM Users u JOIN Admins a ON a.user_id = u.id 
		WHERE a.user_id = userId AND a.organisation_id = orgId;
	ELSEIF userId IS NOT null AND orgId IS null THEN
		SELECT u.id,u.`display-name` as display_name,u.email,u.password,u.biography, 
		    (SELECT `en-name` FROM Languages l WHERE l.id = u.`language_id`) AS `languageName`, 
		    (SELECT code FROM Languages l WHERE l.id = u.`language_id`) AS `languageCode`, 
		    (SELECT `en-name` FROM Countries c WHERE c.id = u.`country_id`) AS `countryName`, 
		    (SELECT code FROM Countries c WHERE c.id = u.`country_id`) AS `countryCode`, 
		    u.nonce,u.`created-time` as created_time

		FROM Users u JOIN Admins a ON a.user_id = u.id 
		WHERE a.user_id = userId AND a.organisation_id is null;
	ELSEIF userId IS null AND orgId IS NOT null THEN
		SELECT u.id,u.`display-name` as display_name,u.email,u.password,u.biography, 
		    (SELECT `en-name` FROM Languages l WHERE l.id = u.`language_id`) AS `languageName`, 
		    (SELECT code FROM Languages l WHERE l.id = u.`language_id`) AS `languageCode`, 
		    (SELECT `en-name` FROM Countries c WHERE c.id = u.`country_id`) AS `countryName`, 
		    (SELECT code FROM Countries c WHERE c.id = u.`country_id`) AS `countryCode`, 
		    u.nonce,u.`created-time` as created_time

		FROM Users u JOIN Admins a ON a.user_id = u.id 
		WHERE a.organisation_id = orgId;
	ELSEIF userId IS null AND orgId IS null THEN
		SELECT u.id,u.`display-name` as display_name,u.email,u.password,u.biography, 
		    (SELECT `en-name` FROM Languages l WHERE l.id = u.`language_id`) AS `languageName`, 
		    (SELECT code FROM Languages l WHERE l.id = u.`language_id`) AS `languageCode`, 
		    (SELECT `en-name` FROM Countries c WHERE c.id = u.`country_id`) AS `countryName`, 
		    (SELECT code FROM Countries c WHERE c.id = u.`country_id`) AS `countryCode`, 
		    u.nonce,u.`created-time` as created_time

		FROM Users u JOIN Admins a ON a.user_id = u.id 
		WHERE (a.organisation_id is null or a.organisation_id = orgId);
	END IF;
END//
DELIMITER ;

-- Dumping structure for procedure trommonsUpdateTest.getArchivedProject
DROP PROCEDURE IF EXISTS `getArchivedProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getArchivedProject`(IN `projectId` INT, IN `titleText` VARCHAR(128), IN `descr` VARCHAR(4096), IN `imp` VARCHAR(4096), IN `deadlineTime` DATETIME, IN `orgId` INT, IN `ref` VARCHAR(128), IN `wordCount` INT, IN `createdTime` DATETIME, IN `archiveDate` DATETIME, IN `archiverId` INT, IN `lCode` VARCHAR(3), IN `cCode` VARCHAR(2), IN imageUploaded BIT(1), IN imageApproved BIT(1))
    READS SQL DATA
BEGIN
    if projectId='' then set projectId=null;end if;
    if titleText='' then set titleText=null;end if;
    if descr='' then set descr=null;end if;
    if imp='' then set imp=null;end if;
    if deadlineTime='' then set deadlineTime=null;end if;
    if orgId='' then set orgId=null;end if;
    if ref='' then set ref=null;end if;
    if wordCount='' then set wordCount=null;end if;
    if createdTime='' then set createdTime=null;end if;
    if archiveDate='' then set archiveDate=null;end if;
    if archiverId='' then set archiverId=null;end if;
    if lCode='' then set lCode=null;end if;
    if cCode='' then set cCode=null;end if;
    if imageUploaded='' then set imageUploaded=null;end if;
    if imageApproved='' then set imageApproved=null;end if;
    set @lID=null;
    set @cID=null;

    SELECT id INTO @lID FROM Languages WHERE code = lCode;
    SELECT id INTO @cID FROM Countries WHERE code = cCode;

    SELECT p.id, p.title, p.description, p.impact, p.deadline, p.organisation_id as organisationId, p.reference, p.`word-count` as wordCount, p.created, 
        (select `en-name` from Languages l where l.id = p.language_id) as sourceLanguageName,            
        (select code from Languages l where l.id = p.language_id) as sourceLanguageCode,
        (select `en-name` from Countries c where c.id = p.country_id) as sourceCountryName, 
        (select code from Countries c where c.id = p.country_id) as sourceCountryCode, 
        m.`archived-date` as archivedDate, m.`user_id-archived` as userIdArchived, m.`user_id-projectCreator` as userIdProjectCreator,
        p.image_uploaded as imageUploaded, p.image_approved as imageApproved

    FROM ArchivedProjects p JOIN ArchivedProjectsMetadata m ON p.id = m.archivedProject_id 

    WHERE (projectId is null or p.id= projectId) 
        and (titleText is null or p.title=titleText) 
        and (descr is null or p.description= descr) 
        and (imp is null or p.impact=imp)
        and (deadlineTime is null or p.deadline=deadlineTime or deadlineTime='0000-00-00 00:00:00') 
        and (orgId is null or p.organisation_id=orgId) 
        and (ref is null or p.reference=ref)
        and (wordCount is null or p.`word-count`=wordCount) 
        and (createdTime is null or p.created = createdTime)
        and (lCode is null or @lID=lCode)
        and (cCode is null or @cID=cCode)
        and (@lID is null or p.language_id=@lID)
        and (@cID is null or p.country_id = @cID)
        and (archiveDate is null or m.`archived-date`=archiveDate or archiveDate='0000-00-00 00:00:00') 
        and (archiverId is null or m.`user_id-archived`= archiverId)
        AND (imageUploaded IS NULL OR p.image_uploaded = imageUploaded)
        AND (imageApproved IS NULL OR p.image_approved = imageApproved);

END//
DELIMITER ;


-- Dumping structure for procedure debug-test.getArchivedTask
DROP PROCEDURE IF EXISTS `getArchivedTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getArchivedTask`(IN `archiveId` BIGINT, IN `projectId` INT, IN `title` VARCHAR(128), IN `comment` VARCHAR(4096), IN `deadline` DATETIME, IN `wordCount` INT, IN `createdTime` DATETIME, IN `sourceLanguageId` INT, IN `targetLanguageId` INT, IN `sourceCountryId` INT, IN `targetCountryId` INT, IN `taskTypeId` INT, IN `taskStatusId` INT, IN `published` BIT(1))
BEGIN

	if archiveId='' then set archiveId=null; end if;
	if projectId='' then set projectId=null; end if;
	if title='' then set title=null; end if;
	if `comment`='' then set `comment`=null; end if;
	if deadline='' then set deadline=null; end if;
	if wordCount='' then set wordCount=null; end if;
	if createdTime='' then set createdTime=null; end if;	
	if sourceLanguageId='' then set sourceLanguageId=null; end if;
	if targetLanguageId='' then set targetLanguageId=null; end if;
	if sourceCountryId='' then set sourceCountryId=null; end if;
	if targetCountryId='' then set targetCountryId=null; end if;
	if taskTypeId='' then set taskTypeId=null; end if;
	if taskStatusId='' then set taskStatusId=null; end if;
	if published='' then set published=null; end if;

    SELECT t.id, t.project_id as projectId, t.title, t.`comment`, t.deadline, t.`word-count` as wordCount, t.`created-time` as createdTime,
            (select `en-name` from Languages l where l.id = t.`language_id-source`) as `sourceLanguageName`, 
            (select code from Languages l where l.id = t.`language_id-source`) as `sourceLanguageCode`, 
            (select `en-name` from Languages l where l.id = t.`language_id-target`) as `targetLanguageName`, 
            (select code from Languages l where l.id = t.`language_id-target`) as `targetLanguageCode`, 
            (select `en-name` from Countries c where c.id = t.`country_id-source`) as `sourceCountryName`, 
            (select code from Countries c where c.id = t.`country_id-source`) as `sourceCountryCode`, 
            (select `en-name` from Countries c where c.id = t.`country_id-target`) as `targetCountryName`, 
            (select code from Countries c where c.id = t.`country_id-target`) as `targetCountryCode`, 
        t.`taskType_id` as taskType, t.`taskStatus_id` as taskStatus, t.published, tm.version, tm.filename, tm.`content-type` as contentType, tm.`upload-time` as uploadTime,
        tm.`user_id-claimed` as useridClaimed, tm.`user_id-archived` as userIdArchived, tm.prerequisites, tm.`user_id-taskCreator` as userIdTaskCreator, tm.`archived-date` as archivedDate

        FROM ArchivedTasks t JOIN ArchivedTasksMetadata tm ON t.id = tm.archivedTask_id 

        WHERE (archiveId is null or t.id = archiveId)
            and (projectId is null or t.project_id = projectId)
            and (title is null or t.title = title)
            and (`comment` is null or t.`comment` = `comment`)
            and (deadline is null or deadline = '0000-00-00 00:00:00' or t.deadline = deadline)
            and (wordCount is null or t.`word-count` = wordCount)
            and (createdTime is null or createdTime = '0000-00-00 00:00:00' or t.`created-time` = createdTime)
            and (sourceLanguageId is null or t.`language_id-source` = sourceLanguageId) 
            and (targetLanguageId is null or t.`language_id-target` = targetLanguageId)
            and (sourceCountryId is null or t.`country_id-source` = sourceCountryId)
            and (targetCountryId is null or t.`country_id-target` = targetCountryId)
            and (taskTypeId is null or t.`taskType_id` = taskTypeId)
            and (taskStatusId is null or t.`taskStatus_id` = taskStatusId)
            and (published is null or t.`published` = published);

END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Dev.getBadge
DROP PROCEDURE IF EXISTS `getBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getBadge`(IN `id` INT, IN `name` VARCHAR(128), IN `des` VARCHAR(512), IN `orgID` INT)
    READS SQL DATA
BEGIN
	if id='' then set id=null;end if;
	if des='' then set des=null;end if;
	if name='' then set name=null;end if;
	if orgID='' then set orgID=null;end if;

	SELECT * 
        FROM Badges b 
    	where (b.id=id or id is null) 
        and (b.owner_id=orgID or orgID is null) 
        and (b.title=name or name is null) 
        and (b.description=des or des is null);
END//
DELIMITER ;



-- Dumping structure for procedure Solas-Match-Dev.getBannedOrg
DROP PROCEDURE IF EXISTS `getBannedOrg`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getBannedOrg`(IN `orgId` INT, IN `userIdAdmin` INT, IN `bannedTypeId` INT, IN `adminComment` VARCHAR(4096), IN `bannedDate` DATETIME)
BEGIN
	if orgId='' then set orgId=null;end if;
	if userIdAdmin='' then set userIdAdmin=null;end if;
	if bannedTypeId='' then set bannedTypeId=null;end if;
	if adminComment='' then set adminComment=null;end if;
	if bannedDate='' then set bannedDate=null;end if;

    SELECT b.org_id as orgId, b.`user_id-admin` as userIdAdmin, 
           b.bannedtype_id as banType, b.`comment`,
           b.`banned-date` as bannedDate 
    	FROM BannedOrganisations b 
	    WHERE isNullOrEqual(b.org_id,orgId) 
        and isNullOrEqual(b.`user_id-admin`,userIdAdmin) 
        and isNullOrEqual(b.bannedtype_id,bannedTypeId) 
    	and isNullOrEqual(b.`comment`,adminComment) 
        and isNullOrEqual(b.`banned-date`,bannedDate); 

END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Dev.getBannedUser
DROP PROCEDURE IF EXISTS `getBannedUser`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getBannedUser`(IN `userId` INT, IN `userIdAdmin` INT, IN `bannedTypeId` INT, IN `adminComment` VARCHAR(4096), IN `bannedDate` DATETIME)
BEGIN
	if userId='' then set userId=null;end if;
	if userIdAdmin='' then set userIdAdmin=null;end if;
	if bannedTypeId='' then set bannedTypeId=null;end if;
	if adminComment='' then set adminComment=null;end if;
	if bannedDate='' then set bannedDate=null;end if;

	SELECT b.user_id as userId, b.`user_id-admin` as userIdAdmin, b.bannedtype_id as banType, b.`comment`, b.`banned-date` as bannedDate 
	    FROM BannedUsers b 
    	WHERE isNullOrEqual(b.user_id,userId) 
        and isNullOrEqual(b.`user_id-admin`,userIdAdmin) 
        and isNullOrEqual(b.bannedtype_id,bannedTypeId)
	    and isNullOrEqual(b.`comment`,adminComment) 
        and isNullOrEqual(b.`banned-date`, bannedDate);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getCountries
DROP PROCEDURE IF EXISTS `getCountries`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCountries`()
BEGIN
SELECT  `en-name` as name, code, id FROM Countries order by `en-name`;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getCountriesByPattern`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCountriesByPattern`(IN `pattern` VARCHAR(64))
BEGIN
SELECT  `en-name` as country, code, id FROM Countries
WHERE   `en-name` LIKE CONCAT(`pattern`,'%')
order by `en-name`;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Dev.getCountry
DROP PROCEDURE IF EXISTS `getCountry`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCountry`(IN `id` INT, IN `code` VARCHAR(3), IN `name` VARCHAR(128))
BEGIN
	if id='' then set id=null;end if;
	if code='' then set code=null;end if;
	if name='' then set name=null;end if;

	select `en-name` as name, c.code, c.id 
	    from Countries c 
        where isNullOrEqual(c.id,id) 
        and isNullOrEqual(c.code,code) 
        and isNullOrEqual(c.`en-name`, name);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Dev.getLanguage
DROP PROCEDURE IF EXISTS `getLanguage`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLanguage`(IN `id` INT, IN `code` VARCHAR(3), IN `name` VARCHAR(128))
BEGIN
	if id='' then set id=null;end if;
	if code='' then set code=null;end if;
	if name='' then set name=null;end if;

	select `en-name` as name, l.code, l.id 
	    from Languages l 
    	where isNullOrEqual(l.id,id) 
        and isNullOrEqual(l.code,code) 
        and isNullOrEqual(l.`en-name`,name);
END//
DELIMITER ;



-- Dumping structure for procedure Solas-Match-Test.getLanguages
DROP PROCEDURE IF EXISTS `getLanguages`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLanguages`()
BEGIN
SELECT  `en-name` as name, code, id FROM Languages order by `en-name`;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Dev.getLatestAvailableTasks
DROP PROCEDURE IF EXISTS `getLatestAvailableTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLatestAvailableTasks`(IN `lim` INT, IN `offset` INT)
BEGIN
    if lim= '' or lim is null then set lim = ~0; end if;
    if offset='' or offset is null then set offset=0; end if;

    SELECT id, project_id as projectId, title, `word-count` as wordCount, 
            (SELECT `en-name` from Languages where id =t.`language_id-source`) as `sourceLanguageName`, 
            (SELECT code from Languages where id =t.`language_id-source`) as `sourceLanguageCode`, 
            (SELECT `en-name` from Languages where id =t.`language_id-target`) as `targetLanguageName`, 
            (SELECT code from Languages where id =t.`language_id-target`) as `targetLanguageCode`, 
            (SELECT `en-name` from Countries where id =t.`country_id-source`) as `sourceCountryName`, 
            (SELECT code from Countries where id =t.`country_id-source`) as `sourceCountryCode`, 
            (SELECT `en-name` from Countries where id =t.`country_id-target`) as `targetCountryName`, 
            (SELECT code from Countries where id =t.`country_id-target`) as `targetCountryCode`, 
            comment, `task-type_id` as taskType, `task-status_id` as taskStatus, published, deadline, `created-time` as createdTime 
        FROM Tasks t 
        WHERE NOT exists (SELECT 1 
                            FROM TaskClaims 
                            WHERE TaskClaims.task_id = t.id) 
        AND t.published = 1 
        AND t.`task-status_id` = 2 
        ORDER BY `created-time` DESC 
        LIMIT offset, lim;
END//
DELIMITER ;

-- Dumping structure for procedure getLatestAvailableTasksCount
DROP PROCEDURE IF EXISTS `getLatestAvailableTasksCount`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLatestAvailableTasksCount`()
BEGIN
    SELECT count(*) as result
        FROM Tasks t 
        WHERE NOT exists (SELECT 1 
                            FROM TaskClaims 
                            WHERE TaskClaims.task_id = t.id) 
        AND t.published = 1 
        AND t.`task-status_id` = 2;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.getLatestFileVersion
DROP PROCEDURE IF EXISTS `getLatestFileVersion`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLatestFileVersion`(IN `id` INT, IN `uID` INT)
BEGIN
	if uID='' then set uID=null;end if;

	SELECT max(version_id) as latest_version  
	    FROM TaskFileVersions tfv 
    	where isNullOrEqual(tfv.task_id,id) 
        and isNullOrEqual(tfv.user_id,uID);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Dev.getLoginCount
DROP PROCEDURE IF EXISTS `getLoginCount`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLoginCount`(IN `startDate` DATETIME, IN `endDate` DATETIME)
BEGIN
    SELECT COUNT(1) as result
        FROM UserLogins
        WHERE success = 1
        AND `login-time` >= startDate
        AND `login-time` < endDate;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getMembershipRequests
DROP PROCEDURE IF EXISTS `getMembershipRequests`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMembershipRequests`(IN `orgID` INT)
BEGIN
    SELECT id, user_id, org_id, `request-datetime` as request_time
    	FROM OrgRequests
        WHERE org_id = orgID
        ORDER BY `request-datetime` DESC;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getOrg
DROP PROCEDURE IF EXISTS `getOrg`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrg`(IN `id` INT, IN `name` VARCHAR(50), IN `url` VARCHAR(50), IN `bio` vARCHAR(50), IN `email` VARCHAR(50), IN `address` VARCHAR(50), IN `city` VARCHAR(50), IN `country` VARCHAR(50), IN `regionalFocus` VARCHAR(50))
BEGIN
	if id='' then set id=null;end if;
	if name='' then set name=null;end if;
	if url='' then set url=null;end if;
	if bio='' then set bio=null;end if;
	if email='' then set email=null;end if;
	if address='' then set address=null;end if;
	if city='' then set city=null;end if;
	if country='' then set country=null;end if;
	if regionalFocus='' then set regionalFocus=null;end if;
	
	select o.id, o.name, o.`home-page` as homepage, o.biography, o.`e-mail` as 'email', o.address, o.city, o.country, o.`regional-focus` as regionalFocus from Organisations o
        where (id is null or o.id = id)
        and (name is null or o.name = name)
        and (url is null or o.`home-page` = url)
        and (bio is null or o.biography = bio)
        and (email is null or o.`e-mail` = email)
        and (address is null or o.address = address) 
        and (city is null or o.city = city)
        and (country is null or o.country = country) 
        and (regionalFocus is null or o.`regional-focus` = regionalFocus)
    	GROUP BY o.name;
END//
DELIMITER ;


-- Dumping structure for procedure big-merge.getOrgMembers
DROP PROCEDURE IF EXISTS `getOrgMembers`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrgMembers`(IN `orgId` INT)
BEGIN
    select u.id,`display-name` as display_name,email,password,biography,
            (select `en-name` from Languages where id =u.`language_id`) as `languageName`, 
            (select code from Languages where id =u.`language_id`) as `languageCode`, 
            (select `en-name` from Countries where id =u.`country_id`) as `countryName`, 
            (select code from Countries where id =u.`country_id`) as `countryCode`, 
            nonce,`created-time` as created_time
    	FROM OrganisationMembers om JOIN Users u ON om.user_id = u.id
	    WHERE organisation_id=orgId;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getOverdueTasks
DROP PROCEDURE IF EXISTS `getOverdueTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getOverdueTasks`()
BEGIN
    select id, project_id as projectId, title, `word-count` as wordCount, 
            (select `en-name` from Languages where id =t.`language_id-source`) as `sourceLanguageName`,
            (select code from Languages where id =t.`language_id-source`) as `sourceLanguageCode`,
            (select `en-name` from Languages where id =t.`language_id-target`) as `targetLanguageName`,
            (select code from Languages where id =t.`language_id-target`) as `targetLanguageCode`,
            (select `en-name` from Countries where id =t.`country_id-source`) as `sourceCountryName`,
            (select code from Countries where id =t.`country_id-source`) as `sourceCountryCode`,
            (select `en-name` from Countries where id =t.`country_id-target`) as `targetCountryName`, 
            (select code from Countries where id =t.`country_id-target`) as `targetCountryCode`, 
            comment,  `task-type_id` as taskType, `task-status_id` as taskStatus, published, deadline 
        FROM Tasks t 
        where deadline < NOW()
        AND `task-status_id` != 4
        AND published = 1;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getPasswordResetRequests
DROP PROCEDURE IF EXISTS `getPasswordResetRequests`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getPasswordResetRequests`(IN `unique_id` CHAR(40), IN `email` VARCHAR(128))
BEGIN
    if unique_id='' then set unique_id=null;end if;
    if email='' then set email=null;end if;
    
    SELECT p.user_id, p.uid as 'key', p.`request-time` as requestTime 
        FROM PasswordResetRequests p 
        WHERE (unique_id is null or p.uid = unique_id)
        and (email is null or p.user_id IN
                (SELECT id FROM Users u WHERE email = u.email));

END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getProject
DROP PROCEDURE IF EXISTS `getProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getProject`(IN `projectId` INT, IN `titleText` VARCHAR(128), IN `descr` VARCHAR(4096), IN `impactText` VARCHAR(4096), IN `deadlineTime` DATETIME, IN `orgId` INT, IN `ref` VARCHAR(128), IN `wordCount` INT, IN `createdTime` DATETIME, IN `sourceCountryCode` VARCHAR(3), IN `sourceLanguageCode` VARCHAR(3), IN imageUploaded BIT(1), IN imageApproved BIT(1))
    READS SQL DATA
BEGIN
    if projectId='' then set projectId=null;end if;
    if titleText='' then set titleText=null;end if;
    if descr='' then set descr=null;end if;
    if impactText='' then set impactText=null;end if;
    if deadlineTime='' then set deadlineTime=null;end if;
    if orgId='' then set orgId=null;end if;
    if ref='' then set ref=null;end if;
    if wordCount='' then set wordCount=null;end if;
    if createdTime='' then set createdTime=null;end if;
    if sourceCountryCode="" then set sourceCountryCode=null; end if;
    if sourceLanguageCode="" then set sourceLanguageCode=null; end if;
    if imageUploaded="" then set imageUploaded=null; end if;
    if imageApproved="" then set imageApproved=null; end if;

    SELECT id, title, description, impact, deadline,organisation_id as organisationId,reference,`word-count` as wordCount, created as createdTime,
        (select `en-name` from Languages l where l.id = p.`language_id`) as `languageName`, 
        (select code from Languages l where l.id = p.`language_id`) as `languageCode`, 
        (select `en-name` from Countries c where c.id = p.`country_id`) as `countryName`, 
        (select code from Countries c where c.id = p.`country_id`) as `countryCode`, 
        (select sum(tsk.`task-status_id`) / (count(tsk.`task-status_id`) *4) FROM Tasks tsk where tsk.project_id = p.id) as 'status',
        image_uploaded as imageUploaded, image_approved as imageApproved FROM Projects p
    
    WHERE (projectId is null or p.id = projectId)
        AND (titleText is null or p.title = titleText)
        AND (descr is null or p.description = descr)
        AND (impactText is null or p.impact = impactText)
        AND (deadlineTime is null or deadlineTime = '0000-00-00 00:00:00' or p.deadline = deadlineTime)
        AND (orgId is null or p.organisation_id = orgId)
        AND (ref is null or p.reference = ref)
        AND (wordCount is null or p.`word-count`= wordCount)
        AND (createdTime is null or createdTime = '0000-00-00 00:00:00' or p.created = createdTime)
        AND (sourceCountryCode is null or p.country_id = (select c.id from Countries c where c.code = sourceCountryCode))
        AND (sourceLanguageCode is null or p.language_id=(select l.id from Languages l where l.code = sourceLanguageCode))
        AND (imageUploaded IS NULL OR p.image_uploaded = imageUploaded)
        AND (imageApproved IS NULL OR p.image_approved = imageApproved)
        ORDER BY p.created DESC;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getProjectByTag
DROP PROCEDURE IF EXISTS `getProjectByTag`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getProjectByTag`(IN `tID` INT)
BEGIN
    select id, title, description, impact, deadline,organisation_id as organisationId,reference,`word-count` as wordCount, created,
            (select code from Countries where id =p.`country_id`) as country_id,
            (select code from Languages where id =p.`language_id`) as language_id, 
            (select sum(tsk.`task-status_id`)/(count(tsk.`task-status_id`)*4) 
                from Tasks tsk 
                where tsk.project_id=p.id) as 'status', image_uploaded as imageUploaded, image_approved as imageApproved
        from Projects p 
        join ProjectTags pt 
        on pt.project_id=p.id
        where pt.tag_id= tID;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getProjectFile
DROP PROCEDURE IF EXISTS `getProjectFile`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getProjectFile`(IN `pID` INT, IN `uID` INT, IN `fName` VARCHAR(128), IN `token` VARCHAR(128), IN `mime` VARCHAR(128))
BEGIN
    if pID='' then set pID=null;end if;
    if uID='' then set uID=null;end if;
    if fName='' then set fName=null;end if;
    if token='' then set token=null;end if;
    if mime='' then set mime=null;end if;
   

    SELECT p.project_id as projectId, p.user_id as userId, p.filename, p.`file-token` as token, p.`mime-type` as mime 
        FROM ProjectFiles p 
        WHERE (pID is null or p.project_id = pID)
        and (uID is null or p.user_id = uID)
        and (fName is null or p.filename = fName)
        and (token is null or p.`file-token` =  token)
        and (mime is null or p.`mime-type` =  mime);

END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getProjectTags
DROP PROCEDURE IF EXISTS `getProjectTags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getProjectTags`(IN `pID` INT)
BEGIN
    select t.* 
        from Tags t 
        join ProjectTags pt
        on pt.tag_id = t.id
        where pt.project_id = pID;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getRegistrationId
DROP PROCEDURE IF EXISTS `getRegistrationId`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getRegistrationId`(IN `userId` INT)
BEGIN
    SELECT unique_id
        FROM RegisteredUsers
        WHERE user_id = userId;
END//
DELIMITER ;


-- Dumping structure for procedure SolasMatch.getRegisteredUser
DROP PROCEDURE IF EXISTS `getRegisteredUser`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getRegisteredUser`(IN `uuid` VARCHAR(128))
BEGIN
    if EXISTS (SELECT 1
                FROM RegisteredUsers
                WHERE unique_id = uuid) then
        CALL getUser((SELECT user_id
                        FROM RegisteredUsers
                        WHERE unique_id = uuid limit 1), null, null, null, null, null, null, null, null);
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getStatistics
DROP PROCEDURE IF EXISTS `getStatistics`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getStatistics`(IN `statName` VARCHAR(128))
BEGIN
	IF statName = '' THEN SET statName = NULL; END IF;
	
	SELECT * 
        FROM Statistics st 
        where (statName is null or st.name = statName);

END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.getSubscribedUsers
DROP PROCEDURE IF EXISTS `getSubscribedUsers`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getSubscribedUsers`(IN `taskId` INT)
BEGIN
    if EXISTS (SELECT 1
                FROM UserTrackedTasks
                WHERE task_id = taskId) then
        SELECT u.id,`display-name` as display_name,email,u.password,biography, 
                (select `en-name` 
                    from Languages 
                    where id =u.`language_id`) as `languageName`, 
                (select code 
                    from Languages 
                    where id =u.`language_id`) as `languageCode`, 
                (select `en-name` 
                    from Countries 
                    where id =u.`country_id`) as `countryName`, 
                (select code 
                    from Countries 
                    where id =u.`country_id`) as `countryCode`, 
                nonce,`created-time` as created_time
            from Users u
            join UserTrackedTasks utt on u.id=utt.user_id
            WHERE task_id = taskId;
    else
        SET @orgId = -1;
        SELECT p.organisation_id INTO @orgId
            FROM Tasks t JOIN Projects p
            ON t.project_id = p.id
            WHERE t.id = taskId;
        CALL getAdmin(@orgId);
    end if;
END//
DELIMITER ;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;


-- Dumping structure for procedure Solas-Match-Test.getTag
DROP PROCEDURE IF EXISTS `getTag`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTag`(IN `id` INT, IN `name` VARCHAR(50), IN `lim` INT)
BEGIN
     -- if limit is null, set to maxBigInt unsigned
    if lim = '' or lim is null then set lim = ~0; end if;
    if id='' then set id=null;end if;
	if name='' then set name=null;end if;

    SELECT t.id , t.label 
        FROM Tags t 
        WHERE (id is null or t.id = id) 
        AND (name is null or t.label = name)
        LIMIT lim;

END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTaggedTasks
DROP PROCEDURE IF EXISTS `getTaggedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaggedTasks`(IN `tID` INT, IN `lim` INT)
    READS SQL DATA
BEGIN
    -- if limit is null, set to maxBigInt unsigned
    if lim = '' or lim is null then set lim = ~0; end if;
  
    SELECT t.id, t.project_id as projectId, t.title, t.`word-count` as wordCount,
            (select `en-name` from Languages l where l.id = t.`language_id-source`) as `sourceLanguageName`,     
            (select code from Languages l where l.id = t.`language_id-source`) as `sourceLanguageCode`, 
            (select `en-name` from Languages l where l.id = t.`language_id-target`) as `targetLanguageName`, 
            (select code from Languages l where l.id = t.`language_id-target`) as `targetLanguageCode`, 
            (select `en-name` from Countries c where c.id = t.`country_id-source`) as `sourceCountryName`, 
            (select code from Countries c where c.id = t.`country_id-source`) as `sourceCountryCode`, 
            (select `en-name` from Countries c where c.id = t.`country_id-target`) as `targetCountryName`, 
            (select code from Countries c where c.id = t.`country_id-target`) as `targetCountryCode`, t.`created-time` as createdTime, 
            t.`comment`,  t.`task-type_id` as taskType, t.`task-status_id` as taskStatus, t.published, t.deadline  

        FROM Tasks t join ProjectTags pt on pt.project_id = t.project_id
        WHERE pt.tag_id = `tID`
        AND NOT  exists (SELECT 1 FROM TaskClaims WHERE task_id = t.id)
        AND t.published = 1
        AND t.`task-status_id` = 2 
        ORDER BY t.`created-time` DESC
        LIMIT lim;

END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTask
DROP PROCEDURE IF EXISTS `getTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTask`(IN `id` BIGINT, IN `projectID` INT, IN `name` VARCHAR(50), IN `wordCount` INT, IN `sCode` VARCHAR(3), IN `tCode` VARCHAR(3), IN `created` DATETIME, IN `sCC` VARCHAR(3), IN `tCC` VARCHAR(3), IN `taskComment` VARCHAR(4096), IN `tType` INT, IN `tStatus` INT, IN `pub` BIT(1), IN `dLine` DATETIME)
    READS SQL DATA
BEGIN
	if id='' then set id=null;end if;
	if projectID='' then set projectID=null;end if;
	if name='' then set name=null;end if;
	if sCode='' then set sCode=null;end if;
	if tCode='' then set tCode=null;end if;
	if wordCount='' then set wordCount=null;end if;
	if created='' then set created=null;end if;
	if sCC='' then set sCC=null;end if;
	if tCC='' then set tCC=null;end if;
	if taskComment='' then set taskComment=null;end if;
	if tStatus='' then set tStatus=null;end if;
	if tType='' then set tType=null;end if;
	if pub ='' then set pub = null;end if;
	if dLine='' then set dLine=null;end if;
	
	
	select t.id, t.project_id as projectId, t.title, `word-count` as wordCount, 
            (select `en-name` from Languages l where l.id = t.`language_id-source`) as `sourceLanguageName`, 
            (select code from Languages l where l.id = t.`language_id-source`) as `sourceLanguageCode`, 
            (select `en-name` from Languages l where l.id = t.`language_id-target`) as `targetLanguageName`, 
            (select code from Languages l where l.id = t.`language_id-target`) as `targetLanguageCode`, 
            (select `en-name` from Countries c where c.id = t.`country_id-source`) as `sourceCountryName`, 
            (select code from Countries c where c.id = t.`country_id-source`) as `sourceCountryCode`, 
            (select `en-name` from Countries c where c.id = t.`country_id-target`) as `targetCountryName`, 
            (select code from Countries c where c.id = t.`country_id-target`) as `targetCountryCode`, t.`comment`,
            `task-type_id` as taskType, `task-status_id` as taskStatus, published, deadline, `created-time` as createdTime 

        from Tasks t 

        where (id is null or t.id = id)
            and (projectID is null or t.project_id = projectID)
            and (name is null or t.title = name)
            and (sCode is null or t.`language_id-source` = (select l.id from Languages l where l.code = sCode))
            and (tCode is null or t.`language_id-target` = (select l.id from Languages l where l.code = tCode))
            and (sCC is null or t.`country_id-source` = (select c.id from Countries c where c.code = sCC))
            and (tCC is null or t.`country_id-target` = (select c.id from Countries c where c.code = tCC))
            and (wordCount is null or t.`word-count` = wordCount)
            and (created is null or created = '0000-00-00 00:00:00' or t.`created-time` = created)
            and (taskComment is null or t.`comment`= taskComment)
            and (tStatus is null or t.`task-status_id` = tStatus)
            and (tType is null or t.`task-type_id` = tType)
            and (pub is null or t.`published` = pub)
            and (dLine is null or dLine = '0000-00-00 00:00:00' or t.`deadline` = dLine);

END//
DELIMITER ;


-- Dumping structure for procedure debug-test3.getTaskClaimedTime
DROP PROCEDURE IF EXISTS `getTaskClaimedTime`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskClaimedTime`(IN `taskId` INT)
BEGIN
	IF EXISTS ( SELECT 1 FROM TaskClaims WHERE task_id = taskId) THEN
		SELECT t.`claimed-time` as result FROM TaskClaims t WHERE t.task_id=taskId;
	ELSE
		SELECT 0 as result;
	END IF;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTaskFileMetaData
DROP PROCEDURE IF EXISTS `getTaskFileMetaData`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskFileMetaData`(IN `tID` INT, IN `vID` INT, IN `name` TEXT, IN `content` VARCHAR(255), IN `uID` INT, IN `uTime` DATETIME)
    READS SQL DATA
BEGIN
	if tID='' then set tID=null;end if;
	if vID='' then set vID=null;end if;
	if name='' then set name=null;end if;
	if content='' then set content=null;end if;
	if uID='' then set uID=null;end if;
	if uTime='' then set uTime=null;end if;
	
    select task_id as 'id', version_id as version, filename, `content-type` as content_type, user_id, `upload-time` as upload_time 
        from TaskFileVersions t 
        where (tID is null or t.task_id = tID)
        and (vID is null or t.version_id = vID)
        and (name is null or t.filename = name)
        and (content is null or t.`content-type` = content)
        and (uID is null or t.user_id = uID)
        and (uTime is null or uTime = '0000-00-00 00:00:00' or t.`upload-time` = uTime);
	
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTaskPreReqs
DROP PROCEDURE IF EXISTS `getTaskPreReqs`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskPreReqs`(IN `taskId` INT)
    READS SQL DATA
BEGIN

	if taskId='' then set taskId=null;end if;
	
	SELECT t.id, t.project_id as projectId, t.title, t.`word-count` as wordCount,
        	(select `en-name` from Languages l where l.id = t.`language_id-source`) as `sourceLanguageName`, 
            (select code from Languages l where l.id = t.`language_id-source`) as `sourceLanguageCode`, 
            (select `en-name` from Languages l where l.id = t.`language_id-target`) as `targetLanguageName`, 
            (select code from Languages l where l.id = t.`language_id-target`) as `targetLanguageCode`, 
            (select `en-name` from Countries c where c.id = t.`country_id-source`) as `sourceCountryName`, 
            (select code from Countries c where c.id = t.`country_id-source`) as `sourceCountryCode`, 
            (select `en-name` from Countries c where c.id = t.`country_id-target`) as `targetCountryName`, 
            (select code from Countries c where c.id = t.`country_id-target`) as `targetCountryCode`,
        	t.`created-time` as createdTime, t.deadline, t.`comment`, t.`task-type_id` as taskType, t.`task-status_id` as taskStatus, t.published
    	FROM Tasks t JOIN TaskPrerequisites tp ON tp.`task_id-prerequisite`=t.id
	    WHERE (tp.task_id=taskId or tp.task_id is null);	
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Test.getTasksFromPreReq
DROP PROCEDURE IF EXISTS `getTasksFromPreReq`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTasksFromPreReq`(IN `preReqId` INT, IN `projectId` INT)
    READS SQL DATA
BEGIN

	if preReqId='' then set preReqId=NULL;end if;
	if projectId='' then set projectId=NULL;end if;
	if preReqId is not null then set projectId=(select pt.project_id from Tasks pt where pt.id = preReqId);end if;
	
	SELECT t.id, t.project_id as projectId, t.title, t.`word-count` as wordCount,
    		(select `en-name` from Languages l where l.id = t.`language_id-source`) as `sourceLanguageName`,     
            (select code from Languages l where l.id = t.`language_id-source`) as `sourceLanguageCode`, 
            (select `en-name` from Languages l where l.id = t.`language_id-target`) as `targetLanguageName`, 
            (select code from Languages l where l.id = t.`language_id-target`) as `targetLanguageCode`, 
            (select `en-name` from Countries c where c.id = t.`country_id-source`) as `sourceCountryName`, 
            (select code from Countries c where c.id = t.`country_id-source`) as `sourceCountryCode`, 
            (select `en-name` from Countries c where c.id = t.`country_id-target`) as `targetCountryName`, 
            (select code from Countries c where c.id = t.`country_id-target`) as `targetCountryCode`,
    		t.`created-time` as createdTime, t.deadline, t.`comment`, t.`task-type_id` as taskType, t.`task-status_id` as taskStatus, t.published
    	FROM Tasks t LEFT JOIN TaskPrerequisites tp ON tp.task_id=t.id
    	WHERE (tp.`task_id-prerequisite`=preReqId or tp.`task_id-prerequisite` is null)	
	    and (t.project_id = projectId or projectId is null);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTaskReviews
DROP PROCEDURE IF EXISTS `getTaskReviews`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskReviews`(IN `projectId` INT, IN `taskId` INT, IN `userId` INT, IN `correction` INT, IN `gram` INT, IN `spell` INT, IN `consis` INT, IN `comm` VARCHAR(2048))
    READS SQL DATA
BEGIN

    if projectId = '' then set projectId = NULL; end if;
    if taskId = '' then set taskId = NULL; end if;
    if userId = '' then set userId = NULL; end if;
    if correction = '' then set correction = NULL; end if;
    if gram = '' then set gram = NULL; end if;
    if spell = '' then set spell = NULL; end if;
    if consis = '' then set consis = NULL; end if;
    if comm = '' then set comm = NULL; end if;

    SELECT project_id, task_id, user_id, corrections, grammar, spelling, consistency, comment 
        FROM TaskReviews tr
        WHERE (projectId is null or tr.project_id = projectId) 
        and (taskId is null or tr.task_id = taskId)
        and (userId is null or tr.user_id = userId)
        and (correction is null or tr.corrections = correction)
        and (gram is null or tr.grammar = grammar)
        and (spell is null or tr.spelling = spell)
        and (consis is null or tr.consistency = consis)
        and (comm is null or tr.comment = comm);

END//
DELIMITER ;

-- Dumping structure for procedure DropTableFix.getTaskTagIds
DROP PROCEDURE IF EXISTS `getTaskTagIds`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskTagIds`(IN `lim` INT, IN `offs` INT)
BEGIN
    if lim = '' or lim is null then set lim = ~0; end if; 
    if offs = '' or offs is null then set offs=0; end if;	

    SELECT t.id as task_id , pt.tag_id 
        FROM ProjectTags pt join Tasks t on t.project_id = pt.project_id 
        ORDER BY t.id 
        LIMIT lim 
        OFFSET offs;

END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTaskTags
DROP PROCEDURE IF EXISTS `getTaskTags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskTags`(IN `tID` INT)
BEGIN
	set @pID = null;
	select project_id into @pID  from Tasks where id=tID;
	call getProjectTags(@pID);
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Test.getTaskType
DROP PROCEDURE IF EXISTS `getTaskType`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskType`(IN `tID` INT)
BEGIN
	select name from TaskTypes where id=tID;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTopTags
DROP PROCEDURE IF EXISTS `getTopTags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTopTags`(IN `lim` INT)
    READS SQL DATA

BEGIN
    -- if limit is null, set to maxBigInt unsigned
    if lim = '' or lim is null then set lim = ~0; end if;

    SELECT tag.label AS label,tag.id as id, COUNT( pt.tag_id ) AS frequency
        FROM ProjectTags pt
        JOIN Tags tag on pt.tag_id = tag.id
        JOIN Tasks t on t.project_id = pt.project_id
        WHERE t.`task-status_id` = 2
        GROUP BY pt.tag_id
        ORDER BY frequency DESC, tag.label
        LIMIT lim;

END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Test.getTrackedProjects
DROP PROCEDURE IF EXISTS `getTrackedProjects`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTrackedProjects`(IN `uID` INT)
BEGIN
    select id,title,description,deadline,organisation_id as organisationId,impact,reference,`word-count` as wordCount, created,
        (select `en-name` from Languages l where l.id = p.`language_id`) as `sourceLanguageName`, 
        (select code from Languages l where l.id = p.`language_id`) as `sourceLanguageCode`, 
        (select `en-name` from Languages l where l.id = p.`language_id`) as `targetLanguageName`, 
        (select code from Languages l where l.id = p.`language_id`) as `targetLanguageCode`, 
        (select `en-name` from Countries c where c.id = p.`country_id`) as `sourceCountryName`, 
        (select code from Countries c where c.id = p.`country_id`) as `sourceCountryCode`, 
        (select `en-name` from Countries c where c.id = p.`country_id`) as `targetCountryName`, 
        (select code from Countries c where c.id = p.`country_id`) as `targetCountryCode`,
        image_uploaded as imageUploaded, image_approved as imageApproved
        from Projects p  
        join UserTrackedProjects utp 
        on p.id=utp.Project_id
        where utp.user_id=uID;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getUser
DROP PROCEDURE IF EXISTS `getUser`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUser`(IN `id` INT, IN `name` VARCHAR(128), IN `mail` VARCHAR(128), IN `pass` char(128), IN `bio` TEXT, IN `nonce` INT, IN `created` DATETIME, IN `lang_id` INT, IN `region_id` INT)
    READS SQL DATA
BEGIN
	if id='' then set id=null;end if;
	if name='' then set name=null;end if;
	if mail='' then set mail=null;end if;
	if pass='' then set pass=null;end if;
	if bio='' then set bio=null;end if;
	if nonce='' then set nonce=null;end if;
	if created='' then set created=null;end if;
	if lang_id='' then set lang_id=null;end if;
	if region_id='' then set region_id=null;end if;
	
	select u.id,u.`display-name` as display_name, u.email, u.password, u.biography, 
            (select `en-name` from Languages l where l.id = u.`language_id`) as `languageName`, 
            (select code from Languages l where l.id = u.`language_id`) as `languageCode`, 
            (select `en-name` from Countries c where c.id = u.`country_id`) as `countryName`, 
            (select code from Countries c where c.id = u.`country_id`) as `countryCode`, 
            u.nonce, u.`created-time` as created_time
        
        from Users u  

        where   (id is null or u.id = id)
            and (name is null or u.`display-name` = name)
            and (mail is null or (LOWER(u.email) = LOWER(mail)))
            and (pass is null or u.password = pass)
            and (bio is null or u.biography = bio)
            and (nonce is null or u.nonce = nonce)
            and (created is null or created = '0000-00-00 00:00:00' or u.`created-time` = created)
            and (lang_id is null or u.language_id = lang_id)
            and (region_id is null or u.country_id = region_id);

END//
DELIMITER ;

-- Dumping structure for procedure debug-test.getUserArchivedTasks
DROP PROCEDURE IF EXISTS `getUserArchivedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserArchivedTasks`(IN `uID` INT, IN `lim` INT, IN `offset` INT)
BEGIN
    if lim = '' or lim is null then set lim = ~0; end if;
    if offset='' or offset is null then set offset = 0; end if;

    SELECT t.id,t.project_id as projectId,title,`word-count` as wordCount, 
            (select `en-name` from Languages where id =t.`language_id-source`) as `sourceLanguageName`, 
            (select code from Languages where id =t.`language_id-source`) as `sourceLanguageCode`, 
            (select `en-name` from Languages where id =t.`language_id-target`) as `targetLanguageName`, 
            (select code from Languages where id =t.`language_id-target`) as `targetLanguageCode`, 
            (select `en-name` from Countries where id =t.`country_id-source`) as `sourceCountryName`, 
            (select code from Countries where id =t.`country_id-source`) as `sourceCountryCode`, 
            (select `en-name` from Countries where id =t.`country_id-target`) as `targetCountryName`, 
            (select code from Countries where id =t.`country_id-target`) as `targetCountryCode`, 
            `comment`, `taskType_id` as taskType, `taskStatus_id` as taskStatus, published, deadline, `created-time` as createdTime , am.version, 
            am.filename, am.`content-type` as contentType, am.`upload-time` as uploadTime, am.`user_id-claimed` as userIdClaimed,
            am.`user_id-archived` as userIdArchived, am.prerequisites, am.`user_id-taskCreator` as userIdTaskCreator, am.`archived-date` as archivedDate
    	FROM ArchivedTasks t 
	    JOIN ArchivedTasksMetadata am
    	ON t.id=am.archivedTask_id
    	WHERE am.`user_id-claimed` = uID
        LIMIT offset, lim;
END//
DELIMITER ;

-- Procedure getUserArchivedTasksCount
DROP PROCEDURE IF EXISTS `getUserArchivedTasksCount`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserArchivedTasksCount`(IN `userId` INT)
BEGIN
    SELECT COUNT(*) as "count"
	FROM ArchivedTasks t 
    JOIN ArchivedTasksMetadata am
	ON t.id=am.archivedTask_id
	WHERE am.`user_id-claimed` = `userId`;

END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Test.getUserBadges
DROP PROCEDURE IF EXISTS `getUserBadges`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserBadges`(IN `id` INT)
BEGIN
    SELECT b.*
        FROM UserBadges ub JOIN Badges b ON ub.badge_id = b.id
        WHERE user_id = id;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getUserByOAuthToken
DROP PROCEDURE IF EXISTS `getUserByOAuthToken`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserByOAuthToken`(IN `accessToken` CHAR(40))
    READS SQL DATA
BEGIN
    SELECT u.id,u.`display-name` as display_name, u.email, u.password, u.biography,
        (select `en-name` from Languages l where l.id = u.`language_id`) as `languageName`,
        (select code from Languages l where l.id = u.`language_id`) as `languageCode`,
        (select `en-name` from Countries c where c.id = u.`country_id`) as `countryName`,
        (select code from Countries c where c.id = u.`country_id`) as `countryCode`,
        u.nonce, u.`created-time` as created_time
        FROM Users u
        JOIN oauth_sessions sessions
        ON u.id = sessions.owner_id
        JOIN oauth_session_access_tokens tokens
        ON sessions.id = tokens.session_id
        WHERE tokens.access_token = accessToken;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getUserClaimedTask
DROP PROCEDURE IF EXISTS `getUserClaimedTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserClaimedTask`(IN `taskID` INT)
BEGIN
	IF EXISTS( 	SELECT 1	FROM TaskClaims tc WHERE tc.task_id=taskId) THEN
		SET @userId = false;			
		SELECT user_id INTO @userId FROM TaskClaims WHERE task_id=taskId;
		call getUser(@userId,null,null,null,null,null,null,null,null);
	END IF;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Test.getUserIdsPendingTaskStreamNotification
DROP PROCEDURE IF EXISTS `getUserIdsPendingTaskStreamNotification`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserIdsPendingTaskStreamNotification`()
BEGIN
	SELECT u.user_id FROM UserTaskStreamNotifications u
	WHERE `last-sent` is NULL
    OR (u.interval = 1 
            AND `last-sent` < NOW() - INTERVAL 1 DAY)
    OR (u.interval = 2
            AND `last-sent` < NOW() - INTERVAL 1 WEEK)
    OR (u.interval = 3
            AND `last-sent` < NOW() - INTERVAL 1 MONTH);
END//
DELIMITER ;

-- Dumping structure for procedure DropTableFix.getUserLCCodes
DROP PROCEDURE IF EXISTS `getUserLCCodes`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserLCCodes`(IN `lim` INT, IN `offs` INT)
BEGIN

     -- if limit is null, set to maxBigInt unsigned
    if lim = '' or lim is null then set lim = ~0; end if;
	if offs ='' or offs is null then set offs=0;end if;
	
	select s.user_id, 
            (select lg.code from Languages lg where lg.id = s.language_id) as languageCode, 
            (select c.code from Countries c where c.id = s.country_id) as countryCode 
        FROM UserSecondaryLanguages s 
        order by s.user_id
	    LIMIT lim	
    	OFFSET offs;

END//
DELIMITER ;

-- Dumping structure for procedure DropTableFix.getUserNativeLCCodes
DROP PROCEDURE IF EXISTS `getUserNativeLCCodes`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserNativeLCCodes`(IN `lim` INT, IN `offs` INT)
BEGIN
	-- if limit is null, set to maxBigInt unsigned
    if lim = '' or lim is null then set lim = ~0; end if;
	if offs = '' or offs is null then set offs=0;end if;
	
	SELECT u.id, 
            (select l.code from Languages l where l.id = u.language_id) as languageCode, 
            (select c.code from Countries c where c.id = u.country_id) as countryCode from Users u
        LIMIT lim	
        OFFSET offs;	

END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getUserNotifications
DROP PROCEDURE IF EXISTS `getUserNotifications`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserNotifications`(IN `id` INT)
BEGIN
	SELECT *
	    FROM UserNotifications
    	WHERE user_id = id;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getUserRealName
DROP PROCEDURE IF EXISTS `getUserRealName`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserRealName`(IN `userId` INT)
BEGIN
	IF EXISTS(SELECT 1
                FROM UserPersonalInformation
                WHERE user_id = userId
                AND receive_credit = 1) then
        SELECT CONCAT(`first-name`, ' ', `last-name`) as real_name
            FROM UserPersonalInformation
            WHERE user_id = userId;
    ELSE
        SELECT '' as real_name;
    END IF;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getUsersWithBadge
DROP PROCEDURE IF EXISTS `getUsersWithBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUsersWithBadge`(IN `bID` INT)
BEGIN
	SELECT id, `display-name` as display_name, email, password, biography,
        (select `en-name` from Languages l where l.id = u.`language_id`) as `languageName`,
        (select code from Languages l where l.id = u.`language_id`) as `languageCode`,
        (select `en-name` from Countries c where c.id = u.`country_id`) as `countryName`,
        (select code from Countries c where c.id = u.`country_id`) as `countryCode`,
        nonce, `created-time` as created_time
	    FROM Users u JOIN UserBadges ON u.id = UserBadges.user_id
    	WHERE badge_id = bID;
END//
DELIMITER ;


-- Dumping structure for procedure DropTableFix.getUserTagIds
DROP PROCEDURE IF EXISTS `getUserTagIds`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTagIds`(IN `lim` INT, IN `offs` INT)
BEGIN
	-- if limit is null, set to maxBigInt unsigned
    if lim = '' or lim is null then set lim = ~0;  end if;
	if offs ='' or offs is null then set offs= 0;   end if;
	
	select ut.user_id, ut.tag_id 
        from UserTags ut 
        order by ut.user_id
        LIMIT lim	
        OFFSET offs;

END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getUserTags
DROP PROCEDURE IF EXISTS `getUserTags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTags`(IN `id` INT, IN `lim` INT)
BEGIN
	-- if limit is null, set to maxBigInt unsigned
    if lim = '' or lim is null then set lim = ~0;  end if;

    SELECT t.*
        FROM UserTags JOIN Tags t ON UserTags.tag_id = t.id	
        WHERE user_id = id LIMIT lim;
    
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getUserTasks
DROP PROCEDURE IF EXISTS `getUserTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTasks`(IN `uID` INT, IN `lim` INT, IN `offs` INT)
BEGIN
    -- if limit is null, set to maxBigInt unsigned
    if lim = '' or lim is null then set lim = ~0; end if;
    if offs = '' or offs is null then set offs = 0; end if;

    SELECT t.id, t.project_id as projectId, t.title, t.`word-count` as wordCount,
            (select `en-name` from Languages l where l.id = t.`language_id-source`) as `sourceLanguageName`, 
            (select code from Languages l where l.id = t.`language_id-source`) as `sourceLanguageCode`, 
            (select `en-name` from Languages l where l.id = t.`language_id-target`) as `targetLanguageName`, 
            (select code from Languages l where l.id = t.`language_id-target`) as `targetLanguageCode`, 
            (select `en-name` from Countries c where c.id = t.`country_id-source`) as `sourceCountryName`, 
            (select code from Countries c where c.id = t.`country_id-source`) as `sourceCountryCode`, 
            (select `en-name` from Countries c where c.id = t.`country_id-target`) as `targetCountryName`, 
            (select code from Countries c where c.id = t.`country_id-target`) as `targetCountryCode`, 
            t.`created-time` as createdTime, t.`comment`, t.`task-type_id` as taskType, t.`task-status_id` as taskStatus, t.published, t.deadline
        FROM Tasks t JOIN TaskClaims tc ON tc.task_id = t.id
        WHERE tc.user_id = uID
        ORDER BY `created-time` DESC
        LIMIT lim
        OFFSET offs;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Test.getUserTasksCount
DROP PROCEDURE IF EXISTS `getUserTasksCount`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTasksCount`(IN `uID` INT)
BEGIN
    SELECT COUNT(1) as result
        FROM TaskClaims
        WHERE user_id = uID;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Test.getUserTaskStreamNotification
DROP PROCEDURE IF EXISTS `getUserTaskStreamNotification`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTaskStreamNotification`(IN `uID` INT)
BEGIN
    SELECT CAST(u.strict as UNSIGNED) AS strict, u.user_id, u.`interval`, u.`last-sent` as last_sent
        FROM UserTaskStreamNotifications u
        WHERE u.user_id = uID;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getUserTaskScore
DROP PROCEDURE IF EXISTS `getUserTaskScore`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTaskScore`(IN `uID` INT, IN `tID` INT)
BEGIN
	if uID='' then set uID=null;end if;
    if tID='' then set tID=null;end if;

	select * from UserTaskScores 
        where (uID is null or user_id = uID)
        and (tID is null or task_id = tID);

END//
DELIMITER ;


-- Dumping structure for procedure debug-test3.getUserTopTasks
DROP PROCEDURE IF EXISTS `getUserTopTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTopTasks`(IN `uID` INT, IN `strict` INT, IN `lim` INT, 
IN `offset` INT, IN `taskType` INT, IN `sourceLanguage` VARCHAR(3), IN `targetLanguage` VARCHAR(3))

    READS SQL DATA

BEGIN
    -- if limit is null, set to maxBigInt unsigned
    if lim = '' or lim is null then set lim = ~0; end if;
    if offset='' or offset is null then set offset = 0; end if;

    if taskType = ''       then set taskType = null; end if;
    if sourceLanguage = '' then set sourceLanguage = null; end if;
    if targetLanguage = '' then set targetLanguage = null; end if;

    (SELECT id,project_id as projectId,title,`word-count` as wordCount,
            (SELECT `en-name` from Languages l where l.id = t.`language_id-source`) as `sourceLanguageName`, 
            (SELECT code from Languages l where l.id = t.`language_id-source`) as `sourceLanguageCode`, 
            (SELECT `en-name` from Languages l where l.id = t.`language_id-target`) as `targetLanguageName`, 
            (SELECT code from Languages l where l.id = t.`language_id-target`) as `targetLanguageCode`, 
            (SELECT `en-name` from Countries c where c.id = t.`country_id-source`) as `sourceCountryName`, 
            (SELECT code from Countries c where c.id = t.`country_id-source`) as `sourceCountryCode`, 
            (SELECT `en-name` from Countries c where c.id = t.`country_id-target`) as `targetCountryName`, 
            (SELECT code from Countries c where c.id = t.`country_id-target`) as `targetCountryCode`, 
            `comment`, `task-type_id` as taskType, `task-status_id` as taskStatus, published, deadline, `created-time` as createdTime 
        FROM Tasks t LEFT JOIN (SELECT * FROM UserTaskScores u WHERE u.user_id = uID ) AS uts 
        ON t.id = uts.task_id 
        WHERE t.id NOT IN ( SELECT t.task_id FROM TaskClaims t)
        AND t.published = 1 
        AND t.`task-status_id` = 2 
        AND not exists( SELECT 1 FROM TaskTranslatorBlacklist t WHERE t.user_id = uID AND t.task_id = t.id)
        AND (taskType is null or t.`task-type_id` = taskType)
        AND (sourceLanguage is null or t.`language_id-source` = (SELECT l.id FROM Languages l WHERE l.code = sourceLanguage))
        AND (targetLanguage is null or t.`language_id-target` = (SELECT l.id FROM Languages l WHERE l.code = targetLanguage))
        AND (strict = 0
            OR ((t.`language_id-source` IN 
                        (SELECT language_id FROM Users WHERE id =  uID)
                    OR t.`language_id-source` IN 
                        (SELECT language_id FROM UserSecondaryLanguages WHERE user_id =  uID))
                AND (t.`language_id-target` IN 
                        (SELECT language_id FROM Users WHERE id = uID)
                    OR t.`language_id-target` IN 
                        (SELECT language_id FROM UserSecondaryLanguages WHERE user_id = uID))))
             ORDER BY uts.score DESC limit offset, lim);
END//
DELIMITER ;

-- Dumping structure for getUserTopTasksCount
DROP PROCEDURE IF EXISTS `getUserTopTasksCount`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTopTasksCount` (IN `uID` INT, IN `strict` INT, IN `taskType` INT, IN `sourceLanguage` VARCHAR(3), IN `targetLanguage` VARCHAR(3))

    READS SQL DATA

BEGIN
    if taskType = ''       then set taskType = null; end if;
    if sourceLanguage = '' then set sourceLanguage = null; end if;
    if targetLanguage = '' then set targetLanguage = null; end if;

    (SELECT count(*) as `result`
        FROM Tasks t LEFT JOIN (SELECT * FROM UserTaskScores u WHERE u.user_id = uID ) AS uts 
        ON t.id = uts.task_id 
        WHERE t.id NOT IN ( SELECT t.task_id FROM TaskClaims t)
        AND t.published = 1 
        AND t.`task-status_id` = 2 
        AND not exists( SELECT 1 FROM TaskTranslatorBlacklist t WHERE t.user_id = uID AND t.task_id = t.id)
        AND (taskType is null or t.`task-type_id` = taskType)
        AND (sourceLanguage is null or t.`language_id-source` = (SELECT l.id FROM Languages l WHERE l.code = sourceLanguage))
        AND (targetLanguage is null or t.`language_id-target` = (SELECT l.id FROM Languages l WHERE l.code = targetLanguage))
        AND (strict = 0
            OR ((t.`language_id-source` IN 
                        (SELECT language_id FROM Users WHERE id =  uID)
                    OR t.`language_id-source` IN 
                        (SELECT language_id FROM UserSecondaryLanguages WHERE user_id =  uID))
                AND (t.`language_id-target` IN 
                        (SELECT language_id FROM Users WHERE id = uID)
                    OR t.`language_id-target` IN 
                        (SELECT language_id FROM UserSecondaryLanguages WHERE user_id = uID)))));
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getFilteredUserClaimedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getFilteredUserClaimedTasks`(IN `userID` INT, IN `lim` INT, IN `offset` INT, IN `taskType` INT, IN `taskStatus` INT, IN `orderBy` INT)

    READS SQL DATA

BEGIN
    -- if limit is null, set to maxBigInt unsigned
    if lim = '' or lim is null then set lim = ~0; end if;
    if offset='' or offset is null then set offset = 0; end if;

    if taskType = 0 then set taskType = null; end if;
    if taskStatus = 0 then set taskStatus = null; end if;

    (SELECT id,project_id as projectId,title,`word-count` as wordCount,
            (SELECT `en-name` from Languages l where l.id = t.`language_id-source`) as `sourceLanguageName`,
            (SELECT code from Languages l where l.id = t.`language_id-source`) as `sourceLanguageCode`,
            (SELECT `en-name` from Languages l where l.id = t.`language_id-target`) as `targetLanguageName`,
            (SELECT code from Languages l where l.id = t.`language_id-target`) as `targetLanguageCode`,
            (SELECT `en-name` from Countries c where c.id = t.`country_id-source`) as `sourceCountryName`,
            (SELECT code from Countries c where c.id = t.`country_id-source`) as `sourceCountryCode`,
            (SELECT `en-name` from Countries c where c.id = t.`country_id-target`) as `targetCountryName`,
            (SELECT code from Countries c where c.id = t.`country_id-target`) as `targetCountryCode`,
            `comment`, `task-type_id` as 'taskType', `task-status_id` as 'taskStatus', published, deadline, `created-time` as createdTime
        FROM Tasks t
        WHERE t.id IN (SELECT tc.task_id FROM TaskClaims tc WHERE tc.user_id = userID)
        AND (taskType is null or t.`task-type_id` = taskType)
        AND (taskStatus is null or t.`task-status_id` = taskStatus)
        ORDER BY
            CASE
             WHEN orderBy = 1 THEN `created-time`
             WHEN orderBy = 2 THEN deadline
             WHEN orderBy = 4 THEN title
            END ASC
          , CASE
             WHEN orderBy = 0 THEN `created-time`
             WHEN orderBy = 3 THEN deadline
             WHEN orderBy = 5 THEN title
            END DESC
            LIMIT offset, lim);
END//
DELIMITER ;



DROP PROCEDURE IF EXISTS `getFilteredUserClaimedTasksCount`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getFilteredUserClaimedTasksCount`(IN `userID` INT, IN `taskType` INT, IN `taskStatus` INT)

    READS SQL DATA

BEGIN
    if taskType = 0 then set taskType = null; end if;
    if taskStatus = 0 then set taskStatus = null; end if;

    SELECT COUNT(1) as result
        FROM Tasks t
        WHERE t.id IN (SELECT tc.task_id FROM TaskClaims tc WHERE tc.user_id = userID)
        AND (taskType is null or t.`task-type_id` = taskType)
        AND (taskStatus is null or t.`task-status_id` = taskStatus);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getUserRecentTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserRecentTasks`(IN `userID` INT, IN `lim` INT, IN `offset` INT)

    READS SQL DATA

BEGIN
    -- if limit is null, set to maxBigInt unsigned
    if lim = '' or lim is null then set lim = ~0; end if;
    if offset='' or offset is null then set offset = 0; end if;

    (SELECT recentTasks.id, recentTasks.project_id as projectId,title,`word-count` as wordCount,
            (SELECT `en-name` from Languages l where l.id = recentTasks.`language_id-source`) as `sourceLanguageName`,
            (SELECT code from Languages l where l.id = recentTasks.`language_id-source`) as `sourceLanguageCode`,
            (SELECT `en-name` from Languages l where l.id = recentTasks.`language_id-target`) as `targetLanguageName`,
            (SELECT code from Languages l where l.id = recentTasks.`language_id-target`) as `targetLanguageCode`,
            (SELECT `en-name` from Countries c where c.id = recentTasks.`country_id-source`) as `sourceCountryName`,
            (SELECT code from Countries c where c.id = recentTasks.`country_id-source`) as `sourceCountryCode`,
            (SELECT `en-name` from Countries c where c.id = recentTasks.`country_id-target`) as `targetCountryName`,
            (SELECT code from Countries c where c.id = recentTasks.`country_id-target`) as `targetCountryCode`,
            `comment`, `task-type_id` as 'taskType', `task-status_id` as 'taskStatus', published, deadline, `created-time` as createdTime
        FROM
		(SELECT tv.`viewed-time`, t.* FROM TaskViews tv
		JOIN Tasks AS t on tv.task_id = t.id
		where tv.user_id = userID and tv.task_is_archived = 0 and t.`task-status_id` = 2 order by tv.`viewed-time` desc) as recentTasks group by id order by `viewed-time` desc
        LIMIT offset, lim);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getUserRecentTasksCount`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserRecentTasksCount`(IN `userID` INT)

    READS SQL DATA

BEGIN

   SELECT count(distinct tv.task_id) as result FROM TaskViews tv
		JOIN Tasks AS t on tv.task_id = t.id
		where tv.user_id = userID and tv.task_is_archived = 0 and t.`task-status_id` = 2;
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `alsoViewedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `alsoViewedTasks`(IN `taskID` INT, IN `lim` INT, IN `offset` INT)

    READS SQL DATA

BEGIN
    -- if limit is null, set to maxBigInt unsigned
    DECLARE current_task_langSource INT DEFAULT 0;
    DECLARE current_task_langTarget INT DEFAULT 0;
    DECLARE current_task_countrySource INT DEFAULT 0;
    DECLARE current_task_countryTarget INT DEFAULT 0;
    if lim = '' or lim is null then set lim = ~0; end if;
    if offset='' or offset is null then set offset = 0; end if;

  

    SELECT `language_id-source`, `language_id-target`, `country_id-source`, `country_id-target`  
    INTO current_task_langSource, current_task_langTarget, current_task_countrySource, current_task_countryTarget FROM Tasks WHERE id = taskID;
	
    (SELECT t2.id,t2.project_id as projectId,t2.title,t2.`word-count` as wordCount,
    (SELECT `en-name` from Languages l where l.id = t2.`language_id-source`) as `sourceLanguageName`,
    (SELECT code from Languages l where l.id = t2.`language_id-source`) as `sourceLanguageCode`,
    (SELECT `en-name` from Languages l where l.id = t2.`language_id-target`) as `targetLanguageName`,
    (SELECT code from Languages l where l.id = t2.`language_id-target`) as `targetLanguageCode`,
    (SELECT `en-name` from Countries c where c.id = t2.`country_id-source`) as `sourceCountryName`,
    (SELECT code from Countries c where c.id = t2.`country_id-source`) as `sourceCountryCode`,
    (SELECT `en-name` from Countries c where c.id = t2.`country_id-target`) as `targetCountryName`,
    (SELECT code from Countries c where c.id = t2.`country_id-target`) as `targetCountryCode`,
    `comment`, `task-type_id` as 'taskType', `task-status_id` as 'taskStatus', published, deadline, `created-time` as createdTime
     FROM
    (SELECT t.id, count(*) AS task_count FROM TaskViews tv JOIN Tasks t ON t.id = tv.task_id AND tv.user_id IN 
    (SELECT DISTINCT user_id FROM `TaskViews` WHERE `task_id` = taskID) AND t.id != taskID AND t.`task-status_id` = 2 and
	 t.`language_id-source` = current_task_langSource and t.`language_id-target` = current_task_langTarget GROUP BY task_id ORDER BY task_count DESC) 
     AS t1 JOIN Tasks t2 ON t1.id = t2.id LIMIT offset,lim);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getUserTrackedTasks
DROP PROCEDURE IF EXISTS `getUserTrackedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTrackedTasks`(IN `id` INT)
BEGIN
	SELECT t.id, t.project_id as projectId, t.title, `word-count` as wordCount,
            (select `en-name` from Languages l where l.id = t.`language_id-source`) as `sourceLanguageName`,
            (select code from Languages l where l.id = t.`language_id-source`) as `sourceLanguageCode`,
            (select `en-name` from Languages l where l.id = t.`language_id-target`) as `targetLanguageName`,
            (select code from Languages l where l.id = t.`language_id-target`) as `targetLanguageCode`,
            (select `en-name` from Countries c where c.id = t.`country_id-source`) as `sourceCountryName`,
            (select code from Countries c where c.id = t.`country_id-source`) as `sourceCountryCode`,
            (select `en-name` from Countries c where c.id = t.`country_id-target`) as `targetCountryName`,
            (select code from Countries c where c.id = t.`country_id-target`) as `targetCountryCode`,
            t.`comment`, `task-type_id` as taskType, `task-status_id` as taskStatus, published, deadline, `created-time` as createdTime
	FROM UserTrackedTasks utt join Tasks t on utt.task_id=t.id
	WHERE user_id = id;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getUserWithBadge
DROP PROCEDURE IF EXISTS `getUserWithBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserWithBadge`(IN `id` INT)
BEGIN
	SELECT Users.id,Users.`display-name`,Users.email,Users.password,Users.biography, 
		    (SELECT `en-name` FROM Languages l WHERE l.id = Users.`language_id`) AS `languageName`, 
		    (SELECT code FROM Languages l WHERE l.id = Users.`language_id`) AS `languageCode`, 
		    (SELECT `en-name` FROM Countries c WHERE c.id = Users.`country_id`) AS `countryName`, 
		    (SELECT code FROM Countries c WHERE c.id = Users.`country_id`) AS `countryCode`, 
		    Users.nonce,Users.`created-time`
	    FROM Users JOIN UserBadges ON Users.id = UserBadges.user_id
    	WHERE UserBadges.user_id = uID AND UserBadges.badge_id = bID;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.hasUserClaimedTask
DROP PROCEDURE IF EXISTS `hasUserClaimedTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `hasUserClaimedTask`(IN `tID` INT, IN `uID` INT)
BEGIN
    SELECT exists (
        select 1
            FROM TaskClaims
            WHERE task_id = tID
            AND user_id = uID
        ) as result;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.hasUserClaimedSegmentationTask
DROP PROCEDURE IF EXISTS `hasUserClaimedSegmentationTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `hasUserClaimedSegmentationTask`(IN `uID` INT, IN `pID` INT)
BEGIN
SELECT exists	 (	select 1
                        FROM TaskClaims tc JOIN Tasks t ON tc.task_id = t.id
                        WHERE `user_id` = uID
                        AND `project_id` = pID
                        AND `task-type_id` = 1
                 ) as result;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Test.isAdmin
DROP PROCEDURE IF EXISTS `isAdmin`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `isAdmin`(IN `userId` INT, IN `orgId` INT)
BEGIN
    SELECT exists (SELECT 1
                    FROM Admins
                    WHERE user_id = userID
                    AND (organisation_id = orgId
                        OR organisation_id is NULL)
                  ) as result;
END//
DELIMITER ;


-- Dumping structure for function Solas-Match-Dev.isNullOrEqual
DROP FUNCTION IF EXISTS `isNullOrEqual`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` FUNCTION `isNullOrEqual`(`x` TEXT, `y` teXT) RETURNS int(11)
BEGIN
    return (x=y or x is null or y is null or '0000-00-00 00:00:00' = x or '0000-00-00 00:00:00'=y);
END//
DELIMITER ;



-- Dumping structure for procedure Solas-Match-Test.isUserVerified
DROP PROCEDURE IF EXISTS `isUserVerified`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `isUserVerified`(IN `userId` INT)
BEGIN
    IF EXISTS (SELECT 1
                FROM Users
                WHERE id = userId)
        AND NOT EXISTS (SELECT 1
                        FROM RegisteredUsers
                        WHERE user_id = userId) then
        SELECT 1 as result;
    else
        SELECT 0 as result;
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure debug-test3.isOrgBanned
DROP PROCEDURE IF EXISTS `isOrgBanned`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `isOrgBanned`(IN `orgId` INT)
BEGIN
    SELECT exists (SELECT 1 FROM BannedOrganisations b WHERE b.org_id=orgId) as result;
END//
DELIMITER ;


-- Dumping structure for procedure debug-test3.isUserBanned
DROP PROCEDURE IF EXISTS `isUserBanned`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `isUserBanned`(IN `userId` INT)
BEGIN
    if EXISTS (SELECT 1 
                FROM BannedUsers 
                WHERE user_id = userId) then
        if NOT EXISTS (SELECT 1
                    FROM BannedUsers
                    WHERE user_id = userId
                    AND ((bannedtype_id = 1 AND DATE_ADD(`banned-date`, INTERVAL 1 DAY) < NOW())
                    OR (bannedtype_id = 2 AND DATE_ADD(`banned-date`, INTERVAL 1 WEEK) < NOW())
                    OR (bannedtype_id = 3 AND DATE_ADD(`banned-date`, INTERVAL 1 MONTH) < NOW()))) then
            SELECT 1 as result;
        else
            DELETE FROM BannedUsers
                WHERE user_id = userId;
                SELECT 0 as result;
        end if;
    else
    	SELECT 0 as result;
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure debug-test3.isUserBlacklistedForTask
DROP PROCEDURE IF EXISTS `isUserBlacklistedForTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `isUserBlacklistedForTask`(IN `userId` INT, IN `taskId` INT)
BEGIN
	IF EXISTS(SELECT 1 FROM TaskTranslatorBlacklist t WHERE t.task_id = taskId AND t.user_id = userId) THEN
		SELECT 1 as result;
	ELSE
		SELECT 0 as result;
	END IF;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `isUserBlacklistedForTaskByAdmin`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `isUserBlacklistedForTaskByAdmin`(IN `userId` INT, IN `taskId` INT)
BEGIN
	IF EXISTS(SELECT 1 FROM TaskTranslatorBlacklist t WHERE t.task_id = taskId AND t.user_id = userId AND t.revoked_by_admin = 1) THEN
		SELECT 1 as result;
	ELSE
		SELECT 0 as result;
	END IF;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Test.logFileDownload
DROP PROCEDURE IF EXISTS `logFileDownload`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `logFileDownload`(IN `tID` INT, IN `vID` INT, IN `uID` INT)
    MODIFIES SQL DATA
BEGIN
	insert into task_file_version_download (task_id,version_id,user_id,time_downloaded) 
	values (tID,uID,vID,Now());
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthAssociateAccessToken
DROP PROCEDURE IF EXISTS `oauthAssociateAccessToken`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthAssociateAccessToken`(IN `sessionId` INT, IN `accessToken` CHAR(40), IN `expireTime` INT)
    MODIFIES SQL DATA
BEGIN
    INSERT INTO oauth_session_access_tokens (session_id, access_token, access_token_expires)
        VALUES (sessionId, accessToken, expireTime);
    SELECT LAST_INSERT_ID();
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthAssociateAuthCode
DROP PROCEDURE IF EXISTS `oauthAssociateAuthCode`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthAssociateAuthCode`(IN `sessionId` INT, IN `authCode` CHAR(40), IN `expireTime` INT)
    MODIFIES SQL DATA
BEGIN
    INSERT INTO oauth_session_authcodes (session_id, auth_code, auth_code_expires)
        VALUEs (sessionId, authCode, expireTime);
    SELECT LAST_INSERT_ID();
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthAssociateAuthCodeScope
DROP PROCEDURE IF EXISTS `oauthAssociateAuthCodeScope`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthAssociateAuthCodeScope`(IN `authCodeId` INT, IN `scopeId` INT)
    MODIFIES SQL DATA
BEGIN
    INSERT INTO `oauth_session_authcode_scopes` (`oauth_session_authcode_id`, `scope_id`)
        VALUES (authCodeId, scopeId);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthAssociateRedirectUri
DROP PROCEDURE IF EXISTS `oauthAssociateRedirectUri`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthAssociateRedirectUri`(IN `sessionId` INT, IN `redirectUri` VARCHAR(255))
    MODIFIES SQL DATA
BEGIN
    INSERT INTO oauth_session_redirects (session_id, redirect_uri)
        VALUES (sessionId, redirectUri);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthAssociateRefreshToken
DROP PROCEDURE IF EXISTS `oauthAssociateRefreshToken`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthAssociateRefreshToken`(IN `accessTokenId` INT, IN `refreshToken` CHAR(40), IN `expireTime` INT, IN `clientId` CHAR(40))
    MODIFIES SQL DATA
BEGIN
    INSERT INTO oauth_session_refresh_tokens (session_access_token_id, refresh_token, refresh_token_expires, client_id)
        VALUES (accessTokenId, refreshToken, expireTime, clientId);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthAssociateScope
DROP PROCEDURE IF EXISTS `oauthAssociateScope`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthAssociateScope`(IN `accessTokenId` INT, IN `scopeId` INT)
    MODIFIES SQL DATA
BEGIN
    INSERT INTO `oauth_session_token_scopes` (session_access_token_id, scope_id)
        VALUES (accessTokenId, scopeId);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthCreateSession
DROP PROCEDURE IF EXISTS `oauthCreateSession`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthCreateSession`(IN `clientId` CHAR(40), IN `ownerType` ENUM('user', 'client'), IN `ownerId` VARCHAR(255))
    MODIFIES SQL DATA
BEGIN
    INSERT INTO oauth_sessions (client_id, owner_type, owner_id)
        VALUES (clientId, ownerType, ownerId);
    SELECT LAST_INSERT_ID();
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthDeleteSession
DROP PROCEDURE IF EXISTS `oauthDeleteSession`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthDeleteSession`(IN `clientId` CHAR(40), IN `ownerType` ENUM('user', 'client'), IN `ownerId` VARCHAR(255))
    MODIFIES SQL DATA
BEGIN
    DELETE FROM oauth_sessions
        WHERE client_id = clientId
        AND owner_type = ownerType
        AND owner_id = ownerId;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthGetAccessToken
DROP PROCEDURE IF EXISTS `oauthGetAccessToken`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthGetAccessToken`(IN `accessTokenId` INT)
    READS SQL DATA
BEGIN
    SELECT *
        FROM `oauth_session_access_tokens`
        WHERE id = accessTokenId;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthGetAuthCodeScopes
DROP PROCEDURE IF EXISTS `oauthGetAuthCodeScopes`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthGetAuthCodeScopes`(IN `authCodeId` INT)
    READS SQL DATA
BEGIN
    SELECT scope_id
        FROM `oauth_session_authcode_scopes`
        WHERE oauth_session_authcode_id = authCodeId;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthGetClient
DROP PROCEDURE IF EXISTS `oauthGetClient`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthGetClient`(IN `clientId` CHAR(40), IN `clientSecret` CHAR(40), IN `redirectUri` VARCHAR(255))
    READS SQL DATA
BEGIN
    IF clientSecret = '' then
        SET clientSecret = NULL;
    END IF;
    IF redirectUri = '' then
        SET redirectUri = NULL;
    END IF;

    SELECT `oauth_clients`.id, `oauth_clients`.secret, `oauth_client_endpoints`.redirect_uri, `oauth_clients`.name, `oauth_clients`.auto_approve
        FROM `oauth_clients`
        JOIN `oauth_client_endpoints`
        ON `oauth_clients`.id = `oauth_client_endpoints`.client_id
        WHERE `oauth_clients`.id = clientId
        AND (clientSecret IS NULL OR `oauth_clients`.secret = clientSecret)
        AND (redirectUri IS NULL OR `oauth_client_endpoints`.redirect_uri = redirectUri);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthGetScope
DROP PROCEDURE IF EXISTS `oauthGetScope`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthGetScope`(IN `requestedScope` VARCHAR(255))
    READS SQL DATA
BEGIN
    SELECT *
        FROM oauth_scopes
        WHERE scope = requestedScope;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthGetScopes
DROP PROCEDURE IF EXISTS `oauthGetScopes`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthGetScopes`(IN `accessToken` VARCHAR(40))
    READS SQL DATA
BEGIN
    SELECT oauth_scopes.*
        FROM oauth_session_token_scopes
        JOIN oauth_session_access_tokens
        ON oauth_session_access_tokens.`id` = `oauth_session_token_scopes`.`session_access_token_id`
        JOIN oauth_scopes
        ON oauth_scopes.id = `oauth_session_token_scopes`.scope_id
        WHERE access_token = accessToken;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthRemoveAuthCode
DROP PROCEDURE IF EXISTS `oauthRemoveAuthCode`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthRemoveAuthCode`(IN `sessionId` INT)
    MODIFIES SQL DATA
BEGIN
    DELETE FROM oauth_session_authcodes
        WHERE session_id = sessionId;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthRemoveRefreshToken
DROP PROCEDURE IF EXISTS `oauthRemoveRefreshToken`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthRemoveRefreshToken`(IN `refreshToken` CHAR(40))
    MODIFIES SQL DATA
BEGIN
    DELETE FROM `oauth_session_refresh_tokens`
        WHERE refresh_token = refreshToken;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthValidateAccessToken
DROP PROCEDURE IF EXISTS `oauthValidateAccessToken`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthValidateAccessToken`(IN `accessToken` CHAR(40))
    READS SQL DATA
BEGIN
    SELECT session_id, sessions.`client_id`, sessions.`owner_id`, sessions.`owner_type`
        FROM `oauth_session_access_tokens`
        JOIN oauth_sessions sessions
        ON sessions.id = session_id
        WHERE access_token = accessToken
        AND access_token_expires >= UNIX_TIMESTAMP(NOW());
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthValidateAuthCode
DROP PROCEDURE IF EXISTS `oauthValidateAuthCode`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthValidateAuthCode`(IN `clientId` CHAR(40), IN `redirectUri` VARCHAR(255), IN `authCode` CHAR(40))
    READS SQL DATA
BEGIN
    SELECT sessions.id AS session_id, authcodes.id AS authcode_id
        FROM oauth_sessions as sessions
        JOIN oauth_session_authcodes as authcodes
        ON authcodes.`session_id` = sessions.id
        JOIN oauth_session_redirects as redirects
        ON redirects.`session_id` = sessions.id
        WHERE sessions.client_id = clientId
        AND authcodes.`auth_code` = authCode
        AND authcodes.`auth_code_expires` >= UNIX_TIMESTAMP(NOW())
        AND redirects.`redirect_uri` = redirectUri;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.oauthValidateRefreshToken
DROP PROCEDURE IF EXISTS `oauthValidateRefreshToken`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthValidateRefreshToken`(IN `refreshToken` CHAR(40), IN `clientId` CHAR(40))
    READS SQL DATA
BEGIN
    SELECT session_access_token_id
        FROM `oauth_session_refresh_tokens`
        WHERE refresh_token = refreshToken
        AND refresh_token_expires >= UNIX_TIMESTAMP(NOW())
        AND client_id = clientId;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.organisationInsertAndUpdate
DROP PROCEDURE IF EXISTS `organisationInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `organisationInsertAndUpdate`(IN `id` INT(10), IN `url` TEXT, IN `companyName` VARCHAR(128), IN `bio` VARCHAR(4096), IN `email` VARCHAR(128), IN `address` VARCHAR(128), IN `city` VARCHAR(128), IN `country` VARCHAR(128), IN `regionalFocus` VARCHAR(128))
BEGIN
	if id='' then set id=null;end if;
	if url='' then set url=null;end if;
	if companyName='' then set companyName=null;end if;
	if bio='' then set bio=null;end if;
	if email='' then set email=null;end if;
	if address='' then set address=null;end if;
	if city='' then set city=null;end if;
	if country='' then set country=null;end if;
	if regionalFocus='' then set regionalFocus=null;end if;
	
	IF id IS NULL AND NOT EXISTS(select * FROM Organisations o WHERE o.name = companyName) THEN
		INSERT INTO Organisations ( name, biography, `home-page`, `e-mail`, address, city, country, `regional-focus`) 
                values ( companyName, bio, url, email, address, city, country, regionalFocus);

		CALL getOrg(NULL,companyName,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
	ELSE
		
                if companyName is not null 
                and companyName != (select o.name from Organisations o  WHERE o.id = id)
                or (select o.name from Organisations o  WHERE o.id = id) is null
                    then update Organisations org set org.name = companyName WHERE org.id = id;
		end if;

                if url is not null 
                and url != (select o.`home-page` from Organisations o WHERE o.id = id) 
                or (select o.`home-page` from Organisations o WHERE o.id = id) is null
                    then update Organisations org set  org.`home-page` = url WHERE org.id = id;
		end if;

                if bio is not null 
                and bio != (select o.biography from Organisations o WHERE o.id = id) 
                or (select o.biography from Organisations o WHERE o.id = id) is null
                    then 
                        # set bio = REPLACE(bio, '\\n', '');
                        # set bio = REPLACE(bio, '\\r', '\n');
                        update Organisations org set  org.biography = bio WHERE org.id = id;
		end if;

		if email is not null 
                and email != (select o.`e-mail` from Organisations o WHERE o.id = id) 
                or (select o.`e-mail` from Organisations o WHERE o.id = id) is null
                    then update Organisations org set org.`e-mail` = email WHERE org.id = id;
		end if;

                if address is not null 
                and address != (select o.address from Organisations o WHERE o.id = id) 
                or (select o.address from Organisations o WHERE o.id = id) is null
                    then 
                        # set address = REPLACE(address, '\\n', '');
                        # set address = REPLACE(address, '\\r', '\n');
                        update Organisations org set org.address = address WHERE org.id = id;
		end if;

                if city is not null 
                and city != (select o.city from Organisations o WHERE o.id = id) 
                or (select o.city from Organisations o WHERE o.id = id) is null
                    then update Organisations org set org.city = city WHERE org.id = id;
		end if;

                if country is not null 
                and country != (select o.country from Organisations o  WHERE o.id = id) 
                or (select o.country from Organisations o  WHERE o.id = id) is null
                    then update Organisations org set org.country = country WHERE org.id = id;
		end if;

                if regionalFocus is not null 
                and regionalFocus != (select o.`regional-focus` from Organisations o WHERE o.id = id) 
                or (select o.`regional-focus` from Organisations o WHERE o.id = id) is null
                    then update Organisations org set org.`regional-focus` = regionalFocus WHERE org.id = id;
		end if;
		
        CALL getOrg(id,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

	END IF;		
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.orgHasMember
DROP PROCEDURE IF EXISTS `orgHasMember`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `orgHasMember`(IN `oID` INT, IN `uID` INT)
BEGIN
    select exists (select 1 from OrganisationMembers om where om.user_id=uID and om.organisation_id=oID) as result;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.projectInsertAndUpdate
DROP PROCEDURE IF EXISTS `projectInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `projectInsertAndUpdate`(IN `projectId` INT, IN `titleText` VARCHAR(128), IN `descr` VARCHAR(4096), IN `impactText` VARCHAR(4096), IN `deadlineTime` DATETIME, IN `orgId` INT, IN `ref` VARCHAR(128), IN `wordCount` INT, IN `createdTime` DATETIME, IN `sourceCountryCode` VARCHAR(3), IN `sourceLanguageCode` VARCHAR(3), IN imageUploaded BIT(1), IN imageApproved BIT(1))
BEGIN
    if projectId="" then set projectId=null; end if;
    if deadlineTime="" then set deadlineTime=null; end if;
    if orgId="" then set orgId=null; end if;
    if wordCount="" then set wordCount=null; end if;
    if createdTime="" then set createdTime=null; end if;
    if sourceCountryCode="" then set sourceCountryCode=null; end if;
    if sourceLanguageCode="" then set sourceLanguageCode=null; end if;
    if titleText="" then set titleText=null; end if;
    if descr="" then set descr=null; end if;
    if impactText="" then set impactText=null; end if;
    if ref="" then set ref=null; end if;
    if imageUploaded="" OR imageUploaded= NULL then set imageUploaded=0; end if;
    if imageApproved="" OR imageApproved= NULL then set imageApproved=0; end if;

    if projectId is null then

        if deadlineTime is null or deadlineTime = '0000-00-00 00:00:00' 
        then set deadlineTime = DATE_ADD(now(),INTERVAL 14 DAY); end if;

        	set @scID=null;
			select c.id into @scID from Countries c where c.code=sourceCountryCode;
			set @sID=null;
			select l.id into @sID from Languages l where l.code=sourceLanguageCode;
	
        INSERT INTO Projects (title, description, impact, deadline, organisation_id, reference, `word-count`, created,language_id,country_id, image_uploaded, image_approved) 
        VALUES (titleText, descr, impactText, deadlineTime, orgId, ref, wordCount, NOW(),@sID,@scID,imageUploaded, imageApproved);

         call getProject(LAST_INSERT_ID(), NULL, NULL, NULL, NULL, NULL,NULL, NULL, NULL, NULL, NULL, NULL, NULL);

    elseif EXISTS (select 1 FROM Projects p WHERE p.id = projectId) then

        if titleText is not null 
        and titleText != (select p.title from Projects p WHERE p.id = projectId)
        or (select p.title from Projects p WHERE p.id = projectId) is null

            then 
                    update Projects p set p.title = titleText WHERE p.id = projectId;
        end if;

        if descr is not null 
        and descr != (select p.description from Projects p WHERE p.id = projectId)
        or (select p.description from Projects p WHERE p.id = projectId) is null

            then 
                    update Projects p set p.description = descr WHERE p.id = projectId;
        end if;


        if impactText is not null 
        and impactText != (select p.impact from Projects p WHERE p.id = projectId)
        or (select p.impact from Projects p WHERE p.id = projectId) is null

            then 
                    update Projects p set p.impact = impactText WHERE p.id = projectId;
        end if;

        if deadlineTime is not null 
        and deadlineTime != '0000-00-00 00:00:00'
        and deadlineTime != (select p.deadline from Projects p WHERE p.id = projectId)
        or (select p.deadline from Projects p WHERE p.id = projectId) is null

            then update Projects p set p.deadline = deadlineTime WHERE p.id = projectId;
        end if;

        if orgId is not null 
        and orgId != (select p.organisation_id from Projects p WHERE p.id = projectId)
        or (select p.organisation_id from Projects p WHERE p.id = projectId) is null

            then update Projects p set p.organisation_id = orgId WHERE p.id = projectId;
        end if;

        if ref is not null 
        and ref != (select p.reference from Projects p WHERE p.id = projectId)
        or (select p.reference from Projects p WHERE p.id = projectId) is null
            then update Projects p set p.reference = ref WHERE p.id = projectId;
        else
            update Projects p set p.reference = ref WHERE p.id = projectId;
        end if;

        if wordCount is not null 
        and wordCount != (select p.`word-count` from Projects p WHERE p.id = projectId)
        or (select p.`word-count` from Projects p WHERE p.id = projectId) is null

            then update Projects p set p.`word-count` = wordCount WHERE p.id = projectId;
        end if;

        if sourceCountryCode is not null 
        and ((select c.id from Countries c where c.code = sourceCountryCode) != (select p.`country_id` from Projects p WHERE p.id = projectId))
        or (select p.`country_id` from Projects p WHERE p.id = projectId) is null

            then update Projects p set p.`country_id` = (select c.id from Countries c where c.code = sourceCountryCode) WHERE p.id = projectId;
        end if;

        if sourceLanguageCode is not null 
        and ((select l.id from Languages l where l.code=sourceLanguageCode) != (select p.`language_id` from Projects p WHERE p.id = projectId))
        or (select p.`language_id` from Projects p WHERE p.id = projectId) is null

            then update Projects p set p.`language_id` = (select l.id from Languages l where l.code=sourceLanguageCode) WHERE p.id = projectId;
        end if;
        
        if createdTime is not null 
        and createdTime != '0000-00-00 00:00:00'
        and createdTime != (select p.created from Projects p WHERE p.id = projectId)
        or (select p.created from Projects p WHERE p.id = projectId) is null

            then update Projects p set p.created = createdTime WHERE p.id = projectId;
        end if;
        
       
        IF imageUploaded != (SELECT p.image_uploaded FROM Projects p WHERE p.id = projectId)
            THEN UPDATE Projects p SET p.image_uploaded = imageUploaded WHERE p.id = projectId;
        END IF;

        IF imageApproved != (SELECT p.image_approved FROM Projects p WHERE p.id = projectId)
            THEN UPDATE Projects p SET p.image_approved = imageApproved WHERE p.id = projectId;
        END IF;

        CALL getProject(projectId, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.recordFileUpload
DROP PROCEDURE IF EXISTS `recordFileUpload`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `recordFileUpload`(IN `tID` INT, IN `name` TeXT, IN `content` VARCHAR(255), IN `uID` INT, IN `ver` INT)
    MODIFIES SQL DATA
BEGIN
    if ver is null then
        set @maxVer =-1;
        if not exists (select 1 
                        from TaskFileVersions tfv 
                        where tfv.task_id=tID
                        and version_id = 1+@maxVer) then
	        INSERT INTO `TaskFileVersions` (`task_id`, `version_id`, `filename`, `content-type`, `user_id`, `upload-time`) 
        	VALUES (tID,1+@maxVer,name, content, uID, Now());
        else
	        select tfv.version_id into @maxVer
        	    from TaskFileVersions tfv 
            	where tfv.task_id=tID 
            	order by tfv.version_id desc
            	limit 1;

            INSERT INTO `TaskFileVersions` (`task_id`, `version_id`, `filename`, `content-type`, `user_id`, `upload-time`) 
            	VALUES (tID,1+@maxVer,name, content, uID, Now());
        end if;
        select 1+@maxVer as version;
    else
        if not exists (select 1 
                        from TaskFileVersions tfv 
                        where tfv.task_id=tID
                        and version_id = ver) then
            INSERT INTO `TaskFileVersions` (`task_id`, `version_id`, `filename`, `content-type`, `user_id`, `upload-time`)
                VALUES (tID, ver, name, content, uID, NOW());
        else
            UPDATE `TaskFileVersions`
                SET filename = name, `content-type` = content, user_id = uID, `upload-time` = NOW()
                WHERE task_id = tID
                AND version_id = ver;
        end if;
        select ver as version;
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.registerUser
DROP PROCEDURE IF EXISTS `registerUser`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `registerUser`(IN `userId` INT, IN `uuid` VARCHAR(128))
BEGIN
    if not EXISTS (SELECT 1
                    FROM RegisteredUsers
                    WHERE user_id = userId)
            AND userId IS NOT NULL then
        INSERT INTO RegisteredUsers (`user_id`, `unique_id`)
            VALUES (userId, uuid);
        SELECT 1 as result;
    else
        SELECT 0 as result;
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure debug-test3.removeAdmin
DROP PROCEDURE IF EXISTS `removeAdmin`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeAdmin`(IN `userId` INT, IN `orgId` INT)
BEGIN
	IF orgId='' THEN SET orgId=NULL; END IF;
	
	IF orgId IS NOT NULL THEN	
		DELETE a FROM Admins a WHERE a.user_id = userId AND a.organisation_id = orgId;
	ELSE
		DELETE a FROM Admins a WHERE a.user_id = userId AND a.organisation_id IS NULL;
	END IF;

END//
DELIMITER ;

-- Dumping structure for procedure debug-test3.removeBannedOrg
DROP PROCEDURE IF EXISTS `removeBannedOrg`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeBannedOrg`(IN `orgId` INT)
BEGIN
	DELETE FROM BannedOrganisations WHERE org_id=orgId;
END//
DELIMITER ;

-- Dumping structure for procedure debug-test3.removeBannedUser
DROP PROCEDURE IF EXISTS `removeBannedUser`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeBannedUser`(IN `userId` INT)
BEGIN
	DELETE FROM BannedUsers WHERE user_id = userId;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.removeMembershipRequest
DROP PROCEDURE IF EXISTS `removeMembershipRequest`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeMembershipRequest`(IN `userId` INT, IN `orgId` INT)
BEGIN
	IF EXISTS (SELECT 1
                FROM OrgRequests r
                WHERE r.user_id = userId
                AND r.org_id = orgId) THEN
        DELETE FROM OrgRequests
            WHERE user_id = userId
            AND org_id = orgId;
        SELECT 1 AS result;
	ELSE
		SELECT 0 AS result;
	END IF;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.removePasswordResetRequest
DROP PROCEDURE IF EXISTS `removePasswordResetRequest`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removePasswordResetRequest`(IN `userId` INT)
BEGIN
	IF EXISTS (SELECT 1 FROM PasswordResetRequests p WHERE p.user_id = userId) THEN
		DELETE FROM PasswordResetRequests
    	WHERE user_id = userId;
    	SELECT 1 AS result;
   ELSE
   	SELECT 0 AS result;   
   END IF;   
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.removeProjectTag
DROP PROCEDURE IF EXISTS `removeProjectTag`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeProjectTag`(IN `projectID` INT, IN `tagID` INT)
BEGIN
if exists (select 1 from ProjectTags where project_id=projectID and tag_id =tagID) then
	delete from ProjectTags  where project_id=projectID and tag_id =tagID;
	select 1 as result;
else
	select 0 as result;
end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.removeTaskPreReq
DROP PROCEDURE IF EXISTS `removeTaskPreReq`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeTaskPreReq`(IN `taskId` INT, IN `preReqId` INT)
    MODIFIES SQL DATA
BEGIN
	if exists(select 1 from TaskPrerequisites tp where tp.task_id=taskID and tp.`task_id-prerequisite`= preReqId) then
      DELETE FROM TaskPrerequisites
        WHERE task_id = taskId
        AND `task_id-prerequisite` = preReqId;
   	select 1 as "result";
   else
   	select 0 as "result";
   end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.removeTaskStreamNotification
DROP PROCEDURE IF EXISTS `removeTaskStreamNotification`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeTaskStreamNotification`(IN `userId` INT)
    MODIFIES SQL DATA
BEGIN
    if exists(select 1 from UserTaskStreamNotifications utsn where utsn.user_id=userId) then
      DELETE FROM UserTaskStreamNotifications
        WHERE user_id = userId;
    select 1 as 'result';
   else
    select 0 as 'result';
   end if;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Test.removeUserBadge
DROP PROCEDURE IF EXISTS `removeUserBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeUserBadge`(IN `uID` INT, IN `bID` INT)
BEGIN
	set @owner = null;
	select b.owner_id into @owner from Badges b where b.id=bID;
        if @owner is not null  or bID in(6,7,8) then
            DELETE FROM UserBadges
            WHERE user_id=uID
            AND badge_id=bID;
            select 1 as result;
        else 
            select 0 as result;
        end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.removeUserNotification
DROP PROCEDURE IF EXISTS `removeUserNotification`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeUserNotification`(IN `userId` INT, IN `taskId` INT)
    COMMENT 'Remove a task from the Users notification list'
BEGIN
	if EXISTS(  SELECT *
	                FROM UserNotifications
	                WHERE user_id = userId
	                AND task_id = taskId) then                 
		DELETE 	FROM UserNotifications	WHERE user_id=userId AND task_id =taskId; 
		select 1 as 'result';
	else
	select 0 as 'result';
	end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.removeUserTag
DROP PROCEDURE IF EXISTS `removeUserTag`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeUserTag`(IN `id` INT, IN `tagID` INT)
    COMMENT 'unsubscripse a user for the given tag'
BEGIN
	if EXISTS(  SELECT user_id, tag_id
	                FROM UserTags
	                WHERE user_id = id
	                AND tag_id = tagID) then                 
		DELETE 	FROM UserTags	WHERE user_id=id AND tag_id =tagID; 
		select 1 as 'result';
	else
	select 0 as 'result';
	end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.requestMembership
DROP PROCEDURE IF EXISTS `requestMembership`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `requestMembership`(IN `userId` INT, IN `orgId` INT)
    MODIFIES SQL DATA
BEGIN
    if not exists (select 1
                    from OrgRequests
                    where user_id = userId and org_id=orgId)
        AND NOT EXISTS (SELECT 1
                    FROM OrganisationMembers om
                    WHERE om.user_id = userId
                    AND om.organisation_id = orgId) then
        INSERT INTO OrgRequests (user_id, org_id) VALUES (userId, orgId);
        select 1 as result;
    else
	    select 0 as result;
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.revokeMembership
DROP PROCEDURE IF EXISTS `revokeMembership`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `revokeMembership`(IN `userId` INT, IN `orgId` INT)
BEGIN
	if exists(select 1
                from OrganisationMembers om
                where om.user_id = userId
                and om.organisation_id = orgId) then
		delete from OrganisationMembers
            where user_id = userId
            and organisation_id = orgId;
		select 1 as result;
	else
		select 0 as result;
	end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.saveUserTaskScore
DROP PROCEDURE IF EXISTS `saveUserTaskScore`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveUserTaskScore`(IN `uID` INT, IN `tID` INT, IN `points` INT)
BEGIN
    if not exists(SELECT * FROM UserTaskScores where user_id=uID and task_id=tID) then
        insert into UserTaskScores (`user_id`, `task_id`, `score`)
        VALUES (uID, tID, points);
    else
        UPDATE UserTaskScores SET score=points WHERE user_id=uID and task_id=tID;
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.searchForOrg
DROP PROCEDURE IF EXISTS `searchForOrg`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `searchForOrg`(IN `org_name` VARCHAR(128))
    COMMENT 'Search for an organisation by name'
BEGIN
	SELECT o.id, o.name, o.biography, o.`home-page` as homepage, o.`e-mail` as email, o.address, o.city, o.country,
        o.`regional-focus` as regionalFocus
	    FROM Organisations o
	    WHERE name LIKE CONCAT('%', org_name, '%');
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.searchForTag
DROP PROCEDURE IF EXISTS `searchForTag`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `searchForTag`(IN `tagName` VARCHAR(50))
    COMMENT 'Search for a tag by label'
BEGIN
	SELECT *
	    FROM Tags
	    WHERE label LIKE CONCAT('%', tagName, '%');
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.setTaskStatus
DROP PROCEDURE IF EXISTS `setTaskStatus`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `setTaskStatus`(IN `tID` INT, IN `sID` INT)
BEGIN
	update Tasks 
		set Tasks.`task-status_id`=sID
		where Tasks.id=tID;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.statsUpdateAll
DROP PROCEDURE IF EXISTS `statsUpdateAll`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `statsUpdateAll`()
BEGIN
	CALL statsUpdateArchivedProjects;
	CALL statsUpdateArchivedTasks;
	CALL statsUpdateBadges;
	CALL statsUpdateClaimedTasks;
	CALL statsUpdateOrganisations;
	CALL statsUpdateOrgMemberRequests;
	CALL statsUpdateProjects;
	CALL statsUpdateTags;
	CALL statsUpdateTasks;
	CALL statsUpdateTasksWithPreReqs;
	CALL statsUpdateUnclaimedTasks;
	CALL statsUpdateUsers;
    CALL statsUpdateCompleteTasks;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.statsUpdateArchivedProjects
DROP PROCEDURE IF EXISTS `statsUpdateArchivedProjects`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `statsUpdateArchivedProjects`()
BEGIN
	SET @totalArchivedProjects = 0;	
	SELECT count(1) INTO @totalArchivedProjects FROM ArchivedProjects;
	REPLACE INTO Statistics (name, value)
	VALUES ('ArchivedProjects', @totalArchivedProjects);	
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.statsUpdateArchivedTasks
DROP PROCEDURE IF EXISTS `statsUpdateArchivedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `statsUpdateArchivedTasks`()
BEGIN
	SET @totalArchivedTasks = 0;
	SELECT count(1) INTO @totalArchivedTasks FROM ArchivedTasks;
	REPLACE INTO Statistics (name, value)
	VALUES ('ArchivedTasks', @totalArchivedTasks);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.statsUpdateCompleteTasks
DROP PROCEDURE IF EXISTS `statsUpdateCompleteTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `statsUpdateCompleteTasks`()
BEGIN
	SET @totalCompleteTasks = 0;
	SELECT count(1)
        INTO @totalCompleteTasks
        FROM Tasks
        WHERE `task-status_id` = 4;
	REPLACE INTO Statistics (name, value)
	VALUES ('CompleteTasks', @totalCompleteTasks);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.statsUpdateBadges
DROP PROCEDURE IF EXISTS `statsUpdateBadges`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `statsUpdateBadges`()
BEGIN
	SET @totalBadges = 0;
	SELECT count(1) INTO @totalBadges FROM Badges;		
	REPLACE INTO Statistics (name, value)
	VALUES ('Badges', @totalBadges);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.statsUpdateClaimedTasks
DROP PROCEDURE IF EXISTS `statsUpdateClaimedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `statsUpdateClaimedTasks`()
BEGIN
	SET @claimedTasks = 0;
	SELECT count(1) INTO @claimedTasks FROM TaskClaims;
	REPLACE INTO Statistics (name, value)
	VALUES ('ClaimedTasks', @claimedTasks);	
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.statsUpdateOrganisations
DROP PROCEDURE IF EXISTS `statsUpdateOrganisations`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `statsUpdateOrganisations`()
BEGIN
	SET @totalOrgs = 0;	
	SELECT count(1) INTO @totalOrgs FROM Organisations;
	REPLACE INTO Statistics (name, value)
	VALUES ('Organisations', @totalOrgs);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.statsUpdateOrgMemberRequests
DROP PROCEDURE IF EXISTS `statsUpdateOrgMemberRequests`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `statsUpdateOrgMemberRequests`()
BEGIN
	SET @orgMemberRequests = 0;	
	SELECT count(1) INTO @orgMemberRequests FROM OrgRequests;	
	REPLACE INTO Statistics (name, value)
	VALUES ('OrgMembershipRequests', @orgMemberRequests);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.statsUpdateProjects
DROP PROCEDURE IF EXISTS `statsUpdateProjects`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `statsUpdateProjects`()
BEGIN
	SET @Projects = 0;		
	SELECT count(1) INTO @Projects FROM Projects;
	REPLACE INTO Statistics (name, value)
	VALUES ('Projects', @Projects);	
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.statsUpdateTags
DROP PROCEDURE IF EXISTS `statsUpdateTags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `statsUpdateTags`()
BEGIN
	SET @totalTags = 0;
	SELECT count(1) INTO @totalTags FROM Tags;		
	REPLACE INTO Statistics (name, value)
	VALUES ('Tags', @totalTags);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.statsUpdateTasks
DROP PROCEDURE IF EXISTS `statsUpdateTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `statsUpdateTasks`()
BEGIN
	SET @totalTasks = 0;	
	SELECT count(1) INTO @totalTasks FROM Tasks;
	REPLACE INTO Statistics (name, value)
	VALUES ('Tasks', @totalTasks);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.statsUpdateTasksWithPreReqs
DROP PROCEDURE IF EXISTS `statsUpdateTasksWithPreReqs`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `statsUpdateTasksWithPreReqs`()
BEGIN
	SET @totalTasksWithPreReqs = 0;
	SELECT count(DISTINCT tp.task_id) INTO @totalTasksWithPreReqs FROM TaskPrerequisites tp;	
	REPLACE INTO Statistics (name, value)
	VALUES ('TasksWithPreReqs', @totalTasksWithPreReqs);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.statsUpdateUnclaimedTasks
DROP PROCEDURE IF EXISTS `statsUpdateUnclaimedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `statsUpdateUnclaimedTasks`()
BEGIN
        SET @unclaimedTasks = 0;
        SELECT count(1) into @unclaimedTasks from Tasks t
        WHERE t.id NOT IN
        (
            SELECT task_id
            FROM  TaskClaims
        );
        REPLACE INTO Statistics (name, value)
        VALUES ('UnclaimedTasks', @unclaimedTasks);
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.statsUpdateUsers
DROP PROCEDURE IF EXISTS `statsUpdateUsers`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `statsUpdateUsers`()
BEGIN
	SET @totalUsers = 0;	
	SELECT count(1) INTO @totalUsers FROM Users;
	REPLACE INTO Statistics (name, value)
	VALUES ('Users', @totalUsers);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `submitTaskReview`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `submitTaskReview`(IN projectId INT, IN taskId INT, IN userId INT, IN correction INT, IN gram INT, IN spell INT, IN consis INT, IN comm VARCHAR(2048))
BEGIN
    IF NOT EXISTS (SELECT 1 
                    FROM TaskReviews
                    WHERE task_id = taskId
                    AND user_id = userId
                    AND project_id = projectId) then
        INSERT INTO TaskReviews (project_id, task_id, user_id, corrections, grammar, spelling, consistency, comment)
            VALUES (projectId, taskId, userId, correction, gram, spell, consis, comm);
        SELECT 1 as result;
    else
        SELECT 0 as result;
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.tagInsert
DROP PROCEDURE IF EXISTS `tagInsert`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `tagInsert`(IN `name` VARCHAR(50))
BEGIN
	insert into Tags (label) values (name);
	select *  from Tags t where t.id = LAST_INSERT_ID();
END//
DELIMITER ;


-- Dumping structure for procedure debug-test3.taskInsertAndUpdate
DROP PROCEDURE IF EXISTS `taskInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `taskInsertAndUpdate`(IN `id` BIGINT, IN `projectID` INT, IN `name` VARCHAR(128), IN `wordCount` INT, IN `sCode` VARCHAR(3), IN `tCode` VARCHAR(3), IN `taskComment` VARCHAR(4096), IN `sCC` VARCHAR(3), IN `tCC` VARCHAR(3), IN `dLine` DATETIME, IN `taskType` INT, IN `tStatus` INT, IN `pub` bit(1))
BEGIN

	if id='' then set id=null;end if;
	if projectID='' then set projectID=null;end if;
        if name='' then set name=null; end if;
	if sCode='' then set sCode=null;end if;
	if tCode='' then set tCode=null;end if;
	if wordCount='' then set wordCount=null;end if;
        if taskComment is null then set taskComment=""; end if;
	if sCode='' then set sCode=null;end if;
	if tCode='' then set tCode=null;end if;
	if dLine='' then set dLine=null;end if;
	if taskType='' then set taskType=null;end if;
	if tStatus='' then set tStatus=null;end if;
	if pub is null then set pub=1;end if;

	if id is null then
	        if taskComment is null then set taskComment="";end if;
	        if dLine is null or dLine ='0000-00-00 00:00:00' then set dLine=DATE_ADD(now(),INTERVAL 14 DAY);end if;

	        set @scid=null;
                select c.id into @scid from Countries c where c.code=sCC;

	        set @tcid=null;
                select c.id into @tcid from Countries c where c.code=tCC;
	
	        set @sID=null;
                select l.id into @sID from Languages l where l.code=sCode;

	        set @tID=null;
                select l.id into @tID from Languages l where l.code=tCode;

        	insert into Tasks (project_id,title,`word-count`,`language_id-source`,`language_id-target`,`created-time`,comment,`country_id-source`,`country_id-target`,`deadline`,`task-type_id`,`task-status_id`,`published`)
		 values (projectID,name,wordCount,@sID,@tID,now(),taskComment,@scid,@tcid,dLine,taskType,tStatus,pub);
		 call getTask(LAST_INSERT_ID(),null,null,null,null,null,null,null,null,null,null,null,null,null);

	elseif EXISTS (select 1 from Tasks t where t.id=id) then

		if projectID is not null 
                and projectID != (SELECT t.project_id FROM Tasks t WHERE  t.id = id)
                or (select t.project_id from Tasks t WHERE t.id = id) is null

                    then update Tasks t SET t.project_id = projectID WHERE t.id = id;
		end if;

                if name is not null 
                and name != (SELECT t.title FROM Tasks t WHERE  t.id = id)
                or (select t.title from Tasks t WHERE t.id = id) is null

                    then update Tasks t SET t.title = name WHERE t.id = id;
		end if;

                if sCode is not null 
                and (select l.id from Languages l where l.code = sCode) != (SELECT t.`language_id-source` FROM Tasks t WHERE  t.id = id)
                or (select t.`language_id-source` from Tasks t WHERE t.id = id) is null

                    then update Tasks t SET t.`language_id-source` = (select l.id from Languages l where l.code = sCode) WHERE t.id = id;
		end if;

                if tCode is not null 
                and (select l.id from Languages l where l.code = tCode) != (SELECT t.`language_id-target` FROM Tasks t WHERE  t.id = id)
                or (select t.`language_id-target` from Tasks t WHERE t.id = id) is null

                    then update Tasks t SET t.`language_id-target` = (select l.id from Languages l where l.code = tCode) WHERE t.id = id;
		end if;
                
                if sCC is not null 
                and (select c.id from Countries c where c.code = sCC) != (SELECT t.`country_id-source` FROM Tasks t WHERE  t.id = id)
                or (select t.`country_id-source` from Tasks t WHERE t.id = id) is null

                    then update Tasks t SET t.`country_id-source` = (select c.id from Countries c where c.code = sCC) WHERE t.id = id;
		end if;
                
                if tCC is not null 
                and (select c.id from Countries c where c.code = tCC) != (SELECT t.`country_id-target` FROM Tasks t WHERE  t.id = id)
                or (select t.`country_id-target` from Tasks t WHERE t.id = id) is null

                    then update Tasks t SET t.`country_id-target` = (select c.id from Countries c where c.code = tCC) WHERE t.id = id;
                end if;

                if wordCount is not null 
                and wordCount != (SELECT t.`word-count` FROM Tasks t WHERE  t.id = id)
                or (select t.`word-count` from Tasks t WHERE t.id = id) is null

                    then update Tasks t SET t.`word-count` = wordCount WHERE t.id = id;
		end if;
		
                if taskComment is not null 
                and taskComment != (SELECT t.comment FROM Tasks t WHERE  t.id = id)
                or (select t.comment from Tasks t WHERE t.id = id) is null

                    then update Tasks t SET t.comment = taskComment WHERE t.id = id;
		end if;
                
                if dLine is not null and dLine != '0000-00-00 00:00:00'
                and dLine != (SELECT t.`deadline` FROM Tasks t WHERE  t.id = id)
                or (select t.`deadline` from Tasks t WHERE t.id = id) is null

                    then update Tasks t SET t.`deadline` = dLine WHERE t.id = id;
		end if;

                if taskType is not null 
                and taskType != (SELECT t.`task-type_id` FROM Tasks t WHERE  t.id = id)
                or (select t.`task-type_id` from Tasks t WHERE t.id = id) is null

                    then update Tasks t SET t.`task-type_id` = taskType WHERE t.id = id;
		end if;
                
                if tStatus is not null 
                and tStatus != (SELECT t.`task-status_id` FROM Tasks t WHERE  t.id = id)
                or (select t.`task-status_id` from Tasks t WHERE t.id = id) is null

                    then update Tasks t SET t.`task-status_id` = tStatus WHERE t.id = id;
		end if;
                
                if pub is not null then
                    if (pub=1) then 
                        update Tasks t SET t.`published` = 1 WHERE t.id = id;
                    else
                        update Tasks t SET t.`published` = 0 WHERE t.id = id;
                    end if; 
		end if;
		
		call getTask(id,null,null,null,null,null,null,null,null,null,null,null,null,null);

	end if;	

END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.taskIsClaimed
DROP PROCEDURE IF EXISTS `taskIsClaimed`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `taskIsClaimed`(IN `tID` INT)
BEGIN
Select exists (SELECT 1	FROM TaskClaims WHERE task_id = tID) as result;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.taskStreamNotificationSent
DROP PROCEDURE IF EXISTS `taskStreamNotificationSent`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `taskStreamNotificationSent`(IN `uID` INT, IN `sentDate` DATETIME)
BEGIN
    IF EXISTS (SELECT user_id
                FROM UserTaskStreamNotifications
                WHERE user_id = uID) 
    then
        UPDATE UserTaskStreamNotifications
            SET `last-sent` = sentDate
            WHERE user_id = uID;
        SELECT 1 as 'result';
    else
        SELECT 0 as 'result';
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.unClaimTask
DROP PROCEDURE IF EXISTS `unClaimTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `unClaimTask`(IN `tID` INT, IN `uID` INT, IN `userFeedback` VARCHAR(4096), IN `unclaimByAdmin` BIT(1))
BEGIN
	if EXISTS(select 1 from TaskClaims tc where tc.task_id=tID and tc.user_id=uID) then
		START TRANSACTION;
      delete from TaskClaims where task_id=tID and user_id=uID;
      # insert into TaskTranslatorBlacklist (task_id,user_id, revoked_by_admin) values (tID,uID,unclaimByAdmin);
      INSERT INTO TaskUnclaims (id, task_id, user_id, `unclaim-comment`, `unclaimed-time`) VALUES (NULL, tID, uID, userFeedback, NOW());
      update Tasks set `task-status_id`=2 where id = tID;
      COMMIT;
		select 1 as result;
	else
		select 0 as result;
	end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.userHasBadge
DROP PROCEDURE IF EXISTS `userHasBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userHasBadge`(IN `userID` INT, IN `badgeID` INT)
BEGIN
	Select EXISTS( SELECT 1 FROM UserBadges WHERE user_id = userID AND badge_id = badgeID) as result;
END//
DELIMITER ;


-- Dumping structure for procedure debug-test3.userInsertAndUpdate
DROP PROCEDURE IF EXISTS `userInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userInsertAndUpdate`(IN `email` VARCHAR(128), IN `nonce` int(11), IN `pass` char(128), IN `bio` TEXT, IN `name` VARCHAR(128), IN `lang` VARCHAR(3), IN `region` VARCHAR(3), IN `id` INT)
BEGIN
	if pass='' then set pass=null;end if;
	if bio='' then set bio=null;end if;
	if id='' then set id=null;end if;
	if nonce='' then set nonce=null;end if;
	if name='' then set name=null;end if;
	if email='' then set email=null;end if;
	if lang='' then set lang=null;end if;
    if region='' then set region=null;end if;
	
    -- if new user
	if id is null and not exists(select * from Users u where u.email= email) then
	-- set insert
    	set @countryID=null;
	    select c.id into @countryID from Countries c where c.code=region;
    	set @langID=null;
    	select l.id into @langID from Languages l where l.code=lang;
	
        insert into Users (email, nonce, password, `created-time`, `display-name`, biography, language_id, country_id) 
            values (email, nonce, pass, NOW(), name, bio, @langID, @countryID);
        call getUser(LAST_INSERT_ID(),null,null,null,null,null,null,null,null);
	
    else 
        if bio is not null 
            and bio != IFNULL(
                (SELECT u.biography 
                    FROM Users u 
                    WHERE u.id = id),
                '')
        then 
            update Users u 
                set u.biography = bio
                WHERE u.id = id;
		end if;
                
		if lang is not null 
            and (select l.id 
                from Languages l 
                where l.code=lang
            ) != IFNULL(
                (SELECT u.language_id 
                    FROM Users u
                    WHERE u.id = id),
                '')
        then 
            update Users u 
                set u.language_id = (select l.id from Languages l where l.code=lang)
                WHERE u.id = id;
		end if;

        if region is not null 
            and (select c.id 
                from Countries c 
                where c.code = region
            ) != IFNULL(
                (SELECT u.country_id 
                    FROM Users u 
                    WHERE u.id = id),
                '')
        then 
            update Users u 
                set u.country_id = (select c.id from Countries c where c.code = region)
                WHERE u.id = id;
		end if;

        if name is not null 
            and name NOT LIKE BINARY IFNULL(
                (SELECT u.`display-name`
                    FROM Users u 
                    WHERE u.id = id),
                '')
        then 
            update Users u 
                set u.`display-name` = name
                WHERE u.id = id;
		end if;

        if email is not null 
            and email != (SELECT u.email 
                FROM Users u 
                WHERE u.id = id)
        then
            update Users u 
                set u.email = email
                WHERE u.id = id;
		end if;

        if nonce is not null 
            and nonce != (SELECT u.nonce 
                FROM Users u 
                WHERE u.id = id)
        then
            update Users u
                set u.nonce = nonce
                WHERE u.id = id;
		end if;
		
        if pass is not null 
            and pass != (SELECT u.password 
                FROM Users u 
                WHERE u.id = id)
        then
            update Users u
                set u.password = pass
                WHERE u.id = id;
        end if;

       	call getUser(id,null,null,null,null,null,null,null,null);
	end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.userLikeTag
DROP PROCEDURE IF EXISTS `userLikeTag`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userLikeTag`(IN `id` INT, IN `tagID` INT)
BEGIN
	if not EXISTS(  SELECT user_id, tag_id
	                FROM UserTags
	                WHERE user_id = id
	                AND tag_id = tagID) then                 
		INSERT INTO UserTags (user_id, tag_id)VALUES (id,tagID);
		select 1 as 'result';
	else
	select 0 as 'result';
	end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.userNotificationsInsertAndUpdate
DROP PROCEDURE IF EXISTS `userNotificationsInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userNotificationsInsertAndUpdate`(IN `user_id` INT, IN `task_id` INT)
BEGIN
	insert into UserNotifications  (user_id, task_id, `created-time`) values (user_id, task_id, NOW());
    select 1 as "result";
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.userSubscribedToProject
DROP PROCEDURE IF EXISTS `userSubscribedToProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userSubscribedToProject`(IN `userId` INT, IN `projectId` INT)
BEGIN
	if EXISTS (SELECT project_id 
                	FROM UserTrackedProjects
                	WHERE user_id = userId
                    AND project_id = projectId) then
		select 1 as 'result';
	else
    	select 0 as 'result';
	end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.userSubscribedToTask
DROP PROCEDURE IF EXISTS `userSubscribedToTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userSubscribedToTask`(IN `userId` INT, IN `taskId` INT)
BEGIN
	if EXISTS (SELECT task_id 
                	FROM UserTrackedTasks
                	WHERE user_id = userId
                    AND task_id = taskId) then
		select 1 as 'result';
	else
    	select 0 as 'result';
	end if;
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `userTaskStreamNotificationInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userTaskStreamNotificationInsertAndUpdate`(IN `uID` INT, IN `nInterval` INT, IN `strictEnabled` INT)
BEGIN
    REPLACE INTO `UserTaskStreamNotifications` (`user_id`, `interval`, `strict`)
    VALUES (uID, nInterval, strictEnabled);
    select 1 as 'result';
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.userTrackProject
DROP PROCEDURE IF EXISTS `userTrackProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userTrackProject`(IN `pID` INT, IN `uID` INT)
BEGIN
	
	DECLARE taskId INT DEFAULT FALSE;
	DECLARE done INT DEFAULT FALSE;
	DECLARE cur1 CURSOR FOR SELECT t.id FROM Tasks t WHERE t.project_id=pID;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	
	
	if not exists (select 1 from UserTrackedProjects utp where utp.user_id=uID and utp.Project_id=pID) then
	
		START TRANSACTION;
		insert into UserTrackedProjects (project_id,user_id) values (pID,uID);
		
		OPEN cur1;
		
		read_loop: LOOP
			FETCH cur1 INTO taskId;
			IF done THEN
			 	LEAVE read_loop;
			END IF;
         call userTrackTask(uID, taskId);
		END LOOP;
		CLOSE cur1;
		
		COMMIT;
		select 1 as result;
	else
		select 0 as result;
	end if;
	
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.userTrackTask
DROP PROCEDURE IF EXISTS `userTrackTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userTrackTask`(IN `uID` INT, IN `tID` BIGINT)
    MODIFIES SQL DATA
BEGIN
	if not exists(select 1 from UserTrackedTasks utt where utt.user_id=uID and utt.task_id=tID) then
		insert into UserTrackedTasks (user_id,task_id) values (uID,tID);
		select 1 as `result`;
	else
		select 0 as `result`;
	end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.userUnTrackProject
DROP PROCEDURE IF EXISTS `userUnTrackProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userUnTrackProject`(IN `pID` INT, IN `uID` INT)
BEGIN
	DECLARE taskId INT DEFAULT FALSE;
	DECLARE done INT DEFAULT FALSE;
	DECLARE cur1 CURSOR FOR SELECT t.id FROM Tasks t WHERE t.project_id=pID;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	
	if exists (select 1 from UserTrackedProjects utp where utp.user_id=uID and utp.Project_id=pID) then
		
		START TRANSACTION;
		delete from UserTrackedProjects  where user_id=uID and Project_id=pID;
		
		OPEN cur1;
		
		read_loop: LOOP
			FETCH cur1 INTO taskId;
			IF done THEN
			 	LEAVE read_loop;
			END IF;
         call userUnTrackTask(uID, taskId);
		END LOOP;
		CLOSE cur1;
		COMMIT;
		
		select 1 as result;
	else
		select 0 as result;
	end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.userUnTrackTask
DROP PROCEDURE IF EXISTS `userUnTrackTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userUnTrackTask`(IN `uID` INT, IN `tID` BIGINT)
BEGIN
	if exists(select 1 from UserTrackedTasks utt where utt.user_id=uID and utt.task_id=tID) then
		delete from UserTrackedTasks  where user_id=uID and task_id=tID;
		select 1 as `result`;
	else
		select 0 as `result`;
	end if;
END//
DELIMITER ;

-- Dumping structure for procedure SolasUpgrade2.userSecondaryLanguageInsert
DROP PROCEDURE IF EXISTS `userSecondaryLanguageInsert`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userSecondaryLanguageInsert`(IN `userId` INT, IN `languageCode` VARCHAR(128), IN `countryCode` VARCHAR(128))
BEGIN

	set @languageId=null;	
	select l.id into @languageId from Languages l where l.code=languageCode;
	
	set @countryId=null;
	select c.id into @countryId from Countries c where c.code=countryCode;	
	
	INSERT INTO UserSecondaryLanguages (user_id, language_id, country_id)
	VALUES(userId, @languageId, @countryId);
	
	SELECT (select `en-name` from Languages where id =@languageId) as `languageName`,
			 (select code from Languages where id =@languageId) as `languageCode`,
			 (select `en-name` from Countries where id =@countryId) as `countryName`,
			 (select code from Countries where id =@countryId) as `countryCode` FROM UserSecondaryLanguages ul
	WHERE ul.id = LAST_INSERT_ID();

END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Test.getUserSecondaryLanguages
DROP PROCEDURE IF EXISTS `getUserSecondaryLanguages`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserSecondaryLanguages`(IN `userId` INT)
BEGIN
	SELECT (select `en-name` from Languages where id =u.`language_id`) as `languageName`,
			 (select code from Languages where id =u.`language_id`) as `languageCode`,
			 (select `en-name` from Countries where id =u.`country_id`) as `countryName`,
			 (select code from Countries where id =u.`country_id`) as `countryCode`	
			  FROM UserSecondaryLanguages u WHERE u.user_id = userId;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `deleteUserSecondaryLanguage`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteUserSecondaryLanguage`(IN `userId` INT, IN `languageCode` VARCHAR(128), IN `countryCode` VARCHAR(128))
BEGIN
	IF EXISTS (SELECT 1 FROM UserSecondaryLanguages u WHERE u.user_id=userId AND u.language_id = (SELECT l.id FROM Languages l WHERE l.code=languageCode) AND u.country_id = (SELECT c.id FROM Countries c WHERE c.code=countryCode)) THEN
		DELETE FROM UserSecondaryLanguages WHERE user_id=userId AND language_id = (SELECT l.id FROM Languages l WHERE l.code=languageCode) AND country_id = (SELECT c.id FROM Countries c WHERE c.code=countryCode);
		SELECT 1 AS result;
	ELSE
		SELECT 0 AS result;
	END IF;
END//
DELIMITER ;

-- Dumping structure for procedure big-merge.userPersonalInfoInsertAndUpdate
DROP PROCEDURE IF EXISTS `userPersonalInfoInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userPersonalInfoInsertAndUpdate`(IN `id` INT, IN `userId` INT, IN `firstName` VARCHAR(128), IN `lastName` VARCHAR(128), IN `mobileNumber` VARCHAR(128), IN `businessNumber` VARCHAR(128), IN `languagePreference` INT, IN `jobTitle` VARCHAR(128), IN `address` VARCHAR(128), IN `city` VARCHAR(128), IN `country` VARCHAR(128), IN `receiveCredit` BIT(1))
BEGIN
	if id='' then set id=null;end if;
	if userId='' then set userId=null;end if;
	if firstName='' then set firstName=null;end if;
	if lastName='' then set lastName=null;end if;
	if mobileNumber='' then set mobileNumber=null;end if;
	if businessNumber='' then set businessNumber=null;end if;
    if languagePreference='' then set languagePreference=null;end if;
	if jobTitle='' then set jobTitle=null;end if;
	if address='' then set address=null;end if;
	if city='' then set city=null;end if;
	if country='' then set country=null;end if;
    if receiveCredit IS NULL THEN SET receiveCredit = 0; END IF;
		
	IF id IS NULL AND NOT EXISTS(select 1 FROM UserPersonalInformation p WHERE p.`user_id`=userId) THEN
		INSERT INTO UserPersonalInformation (`user_id`,`first-name`,`last-name`,`mobile-number`,`business-number`, `language-preference`, `job-title`,`address`,`city`,`country`, `receive_credit`)
		VALUES (userId,firstName,lastName,mobileNumber,businessNumber,languagePreference,jobTitle,address,city,country,receiveCredit);
		CALL getUserPersonalInfo(LAST_INSERT_ID(),NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
	ELSE
        if userId is not null 
                and userId != (select p.`user_id` from UserPersonalInformation p WHERE p.id = id)
                or (select p.`user_id` from UserPersonalInformation p WHERE p.id = id) is null
                    then UPDATE UserPersonalInformation p SET p.`user_id` = userId WHERE p.id = id;
		end if;

        if firstName is not null 
                and firstName != (select p.`first-name` from UserPersonalInformation p WHERE p.id = id)
                or (select p.`first-name` from UserPersonalInformation p WHERE p.id = id) is null
                    then UPDATE UserPersonalInformation p SET p.`first-name` = firstName WHERE p.id = id;
		end if;

        if lastName is not null 
                and lastName != (select p.`last-name` from UserPersonalInformation p WHERE p.id = id)
                or (select p.`last-name` from UserPersonalInformation p WHERE p.id = id) is null
                    then UPDATE UserPersonalInformation p SET p.`last-name` = lastName WHERE p.id = id;
		end if;

        if mobileNumber is not null 
                and mobileNumber != (select p.`mobile-number` from UserPersonalInformation p WHERE p.id = id)
                or (select p.`mobile-number` from UserPersonalInformation p WHERE p.id = id) is null
                    then UPDATE UserPersonalInformation p SET p.`mobile-number` = mobileNumber WHERE p.id = id;
		end if;

        if businessNumber is not null 
                and businessNumber != (select p.`business-number` from UserPersonalInformation p WHERE p.id = id)
                or (select p.`business-number` from UserPersonalInformation p WHERE p.id = id) is null
                    then UPDATE UserPersonalInformation p SET p.`business-number` = businessNumber WHERE p.id = id;
		end if;
        
        IF languagePreference IS NOT NULL
            AND languagePreference != (SELECT p.`language-preference` FROM UserPersonalInformation p WHERE p.`id` = id)
            OR (SELECT p.`language-preference` FROM UserPersonalInformation p WHERE p.`id` = id) IS NULL
        THEN UPDATE UserPersonalInformation p SET p.`language-preference` = languagePreference WHERE p.id = id;
        END IF;

        if jobTitle is not null 
                and jobTitle != (select p.`job-title` from UserPersonalInformation p WHERE p.id = id)
                or (select p.`job-title` from UserPersonalInformation p WHERE p.id = id) is null
                    then UPDATE UserPersonalInformation p SET p.`job-title` = jobTitle WHERE p.id = id;
		end if;
                
        if address is not null 
                and address != (select p.address from UserPersonalInformation p WHERE p.id = id)
                or (select p.address from UserPersonalInformation p WHERE p.id = id) is null
                    then 
                            UPDATE UserPersonalInformation p SET p.address = address WHERE p.id = id;
		end if;

        if city is not null 
                and city != (select p.city from UserPersonalInformation p WHERE p.id = id)
                or (select p.city from UserPersonalInformation p WHERE p.id = id) is null
                    then UPDATE UserPersonalInformation p SET p.city = city WHERE p.id = id;
		end if;
                
        if country is not null 
                and country != (select p.country from UserPersonalInformation p WHERE p.id = id)
                or (select p.country from UserPersonalInformation p WHERE p.id = id) is null
                    then UPDATE UserPersonalInformation p SET p.country = country WHERE p.id = id;
		end if;

        if country is not null 
                and country != (select p.country from UserPersonalInformation p WHERE p.id = id)
                or (select p.country from UserPersonalInformation p WHERE p.id = id) is null
                    then UPDATE UserPersonalInformation p SET p.country = country WHERE p.id = id;
		end if;

        if receiveCredit != (select p.receive_credit from UserPersonalInformation p WHERE p.id = id)
                or (select p.receive_credit FROM UserPersonalInformation p WHERE p.id = id) is null
                    then UPDATE UserPersonalInformation p SET p.receive_credit = receiveCredit WHERE p.id = id;
        end if;

		CALL getUserPersonalInfo(id,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

	end if;
END//
DELIMITER ;

-- Dumping structure for procedure big-merge.getUserPersonalInfo
DROP PROCEDURE IF EXISTS `getUserPersonalInfo`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserPersonalInfo`(IN `id` INT, IN `userId` INT, IN `firstName` VARCHAR(128), IN `lastName` VARCHAR(128), IN `mobileNumber` VARCHAR(128), IN `businessNumber` VARCHAR(128), IN `languagePreference` INT, IN `jobTitle` VARCHAR(128), IN `address` VARCHAR(128), IN `city` VARCHAR(128), IN `country` VARCHAR(128), IN `receiveCredit` BIT(1))
BEGIN
	if id='' then set id=null;end if;
	if userId='' then set userId=null;end if;
	if firstName='' then set firstName=null;end if;
	if lastName='' then set lastName=null;end if;
	if mobileNumber='' then set mobileNumber=null;end if;
	if businessNumber='' then set businessNumber=null;end if;
    if languagePreference='' then set languagePreference=null;end if;
	if jobTitle='' then set jobTitle=null;end if;
	if address='' then set address=null;end if;
	if city='' then set city=null;end if;
	if country='' then set country=null;end if;
    if receiveCredit = '' then set receiveCredit = null; end if;
	
	select p.id, p.user_id as userId, p.`first-name` as firstName, p.`last-name` as lastName, p.`mobile-number` as mobileNumber,
    p.`business-number` as businessNumber, p.`job-title` as jobTitle, p.address, p.city, p.country, p.receive_credit, p.`language-preference` as languagePreference 
    from UserPersonalInformation p 
        
        where (id is null or p.id = id)
            and (userId is null or p.user_id = userId)
            and (firstName is null or p.`first-name` = firstName)
            and (lastName is null or p.`last-name` = lastName)
            and (mobileNumber is null or p.`mobile-number` = mobileNumber)
            and (businessNumber is null or p.`business-number` = businessNumber)
            AND (languagePreference IS NULL OR p.`language-preference` = languagePreference)
            and (jobTitle is null or p.`job-title` = jobTitle) 
            and (address is null or p.address = address) 
            and (city is null or p.city = city) 
            and (country is null or p.country = country)
            and (receiveCredit is null or p.receive_credit = receiveCredit);
	
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `setDefaultUserLanguagePref`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `setDefaultUserLanguagePref`()
BEGIN

DECLARE userId INT DEFAULT 0;
DECLARE done INT DEFAULT FALSE;
DECLARE createDefaultLangPref CURSOR  FOR SELECT u.id FROM Users u WHERE u.id NOT IN (SELECT user_id FROM UserPersonalInformation);
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

SET @englishID = NULL;
SELECT id INTO @englishID FROM Languages WHERE code = "en";

UPDATE UserPersonalInformation SET `language-preference` = @englishID WHERE `language-preference` IS NULL;
OPEN createDefaultLangPref;
		read_loop: LOOP
		FETCH createDefaultLangPref INTO userId;
		IF done THEN
	        LEAVE read_loop;
	    END IF;
		CALL userPersonalInfoInsertAndUpdate(NULL,userId,NULL,NULL,NULL,NULL,@englishID,NULL,NULL,NULL,NULL,NULL);
		END LOOP;
CLOSE createDefaultLangPref;
END//
DELIMITER ;

-- Dumping structure for procedure debug-test3.userLoginInsert
DROP PROCEDURE IF EXISTS `userLoginInsert`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userLoginInsert`(IN `userId` INT, IN `eMail` VARCHAR(128), IN `loginSuccess` CHAR(1))
BEGIN

	IF userId = '' THEN SET userId = NULL; END IF;
	IF eMail = '' THEN SET eMail = NULL;END IF;
	IF loginSuccess = '' THEN SET loginSuccess = NULL; END IF;
	
	INSERT INTO UserLogins VALUES(userId, eMail, loginSuccess, NOW());

END//
DELIMITER ;


-- Dumping structure for procedure getTrackedOrganisations
DROP PROCEDURE IF EXISTS `getTrackedOrganisations`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTrackedOrganisations`(IN `userId` INT)
BEGIN
    SELECT o.id, o.name, o.biography, o.`home-page` as homepage, o.`e-mail` as email, o.address, o.city, o.country,
        o.`regional-focus` as regionalFocus
        FROM Organisations o
        JOIN UserTrackedOrganisations uto
        ON o.id=uto.organisation_id
        WHERE uto.user_id=userId;
END//
DELIMITER ;

-- Dumping structure for procedure getUsersTrackingOrg
DROP PROCEDURE IF EXISTS `getUsersTrackingOrg`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUsersTrackingOrg`(IN `orgId` INT)
BEGIN
    SELECT u.id,u.`display-name` as display_name, u.email, u.password, u.biography,
        (SELECT `en-name` FROM Languages l WHERE l.id = u.`language_id`) AS `languageName`,
        (SELECT code FROM Languages l WHERE l.id = u.`language_id`) AS `languageCode`,
        (SELECT `en-name` FROM Countries c WHERE c.id = u.`country_id`) AS `countryName`,
        (SELECT code FROM Countries c WHERE c.id = u.`country_id`) AS `countryCode`,
        u.nonce, u.`created-time` as created_time
        FROM Users u
        JOIN UserTrackedOrganisations uto
        ON u.id=uto.user_id
        WHERE uto.organisation_id=orgId;
END//
DELIMITER ;

-- Dumping structure for procedure userTrackOrganisation
DROP PROCEDURE IF EXISTS `userTrackOrganisation`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userTrackOrganisation`(IN `userId` INT, IN `orgId` INT)
    MODIFIES SQL DATA
BEGIN
    IF NOT EXISTS(SELECT 1
        FROM UserTrackedOrganisations
        WHERE user_id=userId
        AND organisation_id=orgId) THEN

           INSERT INTO UserTrackedOrganisations(user_id,organisation_id,created)
           VALUES (userId, orgId, NOW());

           SELECT 1 AS `result`;

    ELSE

        SELECT 0 AS `result`;

    END IF;
END//
DELIMITER ;

-- Dumping structure for procedure userUnTrackOrganisation
DROP PROCEDURE IF EXISTS `userUnTrackOrganisation`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userUnTrackOrganisation`(IN `userId` INT, IN `orgId` INT)
BEGIN
    IF EXISTS(SELECT 1
        FROM UserTrackedOrganisations
        WHERE user_id=userId
        AND organisation_id=orgId) THEN

	    DELETE FROM UserTrackedOrganisations
            WHERE user_id=userId AND organisation_id=orgId;

	    SELECT 1 AS `result`;

	ELSE

	    SELECT 0 AS `result`;

	END IF;
END//
DELIMITER ;


-- Dumping structure for procedure userSubscribedToOrganisation
DROP PROCEDURE IF EXISTS `userSubscribedToOrganisation`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userSubscribedToOrganisation`(IN `userId` INT, IN `orgId` INT)
BEGIN
    if EXISTS (SELECT organisation_id
        FROM UserTrackedOrganisations
        WHERE user_id = userId
        AND organisation_id = orgId) THEN

        SELECT 1 AS 'result';

    ELSE

    	SELECT 0 AS 'result';

    END IF;
END//
DELIMITER ;

-- Dumping structure for procedure updateProjectWordCount
DROP PROCEDURE IF EXISTS `updateProjectWordCount`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateProjectWordCount`(IN `pID` INT, IN `newWordCount` INT)
    MODIFIES SQL DATA
BEGIN
 set @distinctTaskWordCount = -1;
 set @segmentationTaskCount =0;

 if (pID is not null) and  (newWordCount is not null ) then
      if not exists (select 1 from Tasks where project_id=pID) then
        UPDATE Projects SET `word-count`=newWordCount WHERE id=pID;
        select 1 as result;
      else
      select count(*) into @segmentationTaskCount from Tasks where project_id=pID and (`task-type_id`=4 or `task-type_id`=1);
	if  @segmentationTaskCount  > 0 then
   	    select 2 as result;
        else
           select count(distinct `word-count`) into @distinctTaskWordCount from Tasks where project_id=pID;
 	       if @distinctTaskWordCount = 1 then
                   UPDATE Projects SET `word-count` = newWordCount WHERE id=pID;
                   UPDATE Tasks SET `word-count` = newWordCount WHERE project_id=pID;
                   select 1 as result;
               else
                   select 2 as result;
               end if;
	end if;
      end if;
 else
  select 0 as result;
 end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.recordTaskViews
DROP PROCEDURE IF EXISTS `recordTaskView`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `recordTaskView`(IN `tID` INT, IN `uID` INT)
BEGIN
	IF `tID` = null OR `tID` = '' THEN SET `tID` = NULL; END IF;	
	IF `uID` = null OR `uID` = '' THEN SET `uID` = NULL; END IF;
	
	IF (`uID` IS NOT NULL) AND (`tID` IS NOT NULL) THEN

		INSERT INTO TaskViews (id, task_id, user_id, `viewed-time`) VALUES (NULL, `tID`, `uID`, NOW());
		SELECT 1 as result;

	ELSE
		SELECT 0 as result;
	END IF;
	
END//
DELIMITER ;

-- Dumping structure for procedure getProofreadTask
DROP PROCEDURE IF EXISTS `getProofreadTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getProofreadTask`(IN `tID` INT)
BEGIN
	DECLARE projectId INT DEFAULT 0;
	DECLARE taskTitle varchar(128);
	DECLARE langSource INT DEFAULT 0;
	DECLARE langTarget INT DEFAULT 0;
	DECLARE countrySource INT DEFAULT 0;
	DECLARE countryTarget INT DEFAULT 0;
	DECLARE taskType INT DEFAULT 0;
	DECLARE taskStatus INT DEFAULT 0;		
	
	select project_id, title, `language_id-source`, `language_id-target`, `country_id-source`, `country_id-target`, `task-type_id`, `task-status_id` 
            into projectId, taskTitle, langSource,  langTarget,   countrySource,   countryTarget,  taskType, taskStatus from Tasks where id = `tID`;

	if taskType = 2 then
		
            select t.id, t.project_id as projectId, t.title, `word-count` as wordCount, 
            (select `en-name` from Languages l where l.id = t.`language_id-source`) as `sourceLanguageName`, 
            (select code from Languages l where l.id = t.`language_id-source`) as `sourceLanguageCode`, 
            (select `en-name` from Languages l where l.id = t.`language_id-target`) as `targetLanguageName`, 
            (select code from Languages l where l.id = t.`language_id-target`) as `targetLanguageCode`, 
            (select `en-name` from Countries c where c.id = t.`country_id-source`) as `sourceCountryName`, 
            (select code from Countries c where c.id = t.`country_id-source`) as `sourceCountryCode`, 
            (select `en-name` from Countries c where c.id = t.`country_id-target`) as `targetCountryName`, 
            (select code from Countries c where c.id = t.`country_id-target`) as `targetCountryCode`, t.`comment`,
            `task-type_id` as taskType, `task-status_id` as taskStatus, published, deadline, `created-time` as createdTime 

            from Tasks t 

            where (t.project_id =  projectId)
 	        and (t.title =  taskTitle)
                and (t.`language_id-source` = langSource)
                and (t.`language_id-target` = langTarget)
                and (t.`country_id-source` = countrySource)
                and (t.`country_id-target` = countryTarget)
                and (t.`task-status_id` = 4)
                and (t.`task-type_id` = 3);
	end if;

END//
DELIMITER ;

/*---------------------------------------end of procs----------------------------------------------*/


/*---------------------------------------start of triggers-----------------------------------------*/

-- Dumping structure for trigger Solas-Match-Dev.afterProjectUpdate
DROP TRIGGER IF EXISTS `afterProjectUpdate`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `afterProjectUpdate` AFTER UPDATE ON `Projects` FOR EACH ROW BEGIN
if (old.language_id!= new.language_id) or (old.country_id !=new.country_id) then
update Tasks set `language_id-source`=new.language_id, `country_id-source` = new.country_id where project_id = old.id;
end if;
END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;

-- Dumping structure for trigger debug-test3.beforeUserLoginInsert
DROP TRIGGER IF EXISTS `beforeUserLoginInsert`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `beforeUserLoginInsert` BEFORE INSERT ON `UserLogins` FOR EACH ROW BEGIN

set @loginAttempts = null;
 
	SELECT count(1) INTO @loginAttempts  FROM UserLogins u WHERE u.user_id = NEW.user_id AND u.success = 0 AND u.`login-time` >=  DATE_SUB(NOW(), INTERVAL 1 MINUTE); 
	
	IF @loginAttempts = 4 THEN
		INSERT INTO BannedUsers VALUES (NEW.user_id, 0, 5, 'Sorry, this account has been locked for an hour due to excessive login attempts.', NOW());
	END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;


-- Dumping structure for trigger Solas-Match-Test.defaultUserName
DROP TRIGGER IF EXISTS `defaultUserName`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `defaultUserName` BEFORE INSERT ON `Users` FOR EACH ROW BEGIN
if new.`display-name` is null then set new.`display-name` = substring_index(new.email,'@',1); end if;
END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping structure for trigger debug-test.deleteArchiveTaskOnArchiveProjectDeletion
DROP TRIGGER IF EXISTS `deleteArchiveTaskOnArchiveProjectDeletion`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `deleteArchiveTaskOnArchiveProjectDeletion` BEFORE DELETE ON `ArchivedProjects` FOR EACH ROW BEGIN

	DELETE FROM ArchivedTasks WHERE project_id=OLD.id;

END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;


-- Dumping structure for trigger Solas-Match-Test.onTasksUpdate
DROP TRIGGER IF EXISTS `onTasksUpdate`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `onTasksUpdate` AFTER UPDATE ON `Tasks` FOR EACH ROW BEGIN
    DECLARE userId INT DEFAULT 0;
    DECLARE done INT DEFAULT FALSE;
    DECLARE dependantTaskId INT DEFAULT 0;
    DECLARE dependantTasks CURSOR FOR SELECT task_id FROM TaskPrerequisites WHERE `task_id-prerequisite` = new.id;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    if (new.`task-status_id`=4) then
        set @userID = null;
		SELECT user_id INTO @userId
                FROM TaskClaims
                WHERE task_id = new.id;

        if new.`task-type_id` = 2 and NOT EXISTS (SELECT 1
                            FROM UserBadges
                            WHERE user_id = @userId
                            AND badge_id = 6) then
        		INSERT INTO UserBadges (user_id, badge_id) VALUES (@userId, 6);
        end if;
        if new.`task-type_id` = 3  
		  and NOT EXISTS (SELECT 1
                            FROM UserBadges
                            WHERE user_id = @userId
                            AND badge_id = 7)then
            INSERT INTO UserBadges (user_id, badge_id) VALUES (@userId, 7);
        end if;

        OPEN dependantTasks;
        read_loop: LOOP
            FETCH dependantTasks INTO dependantTaskId;
            if done then
                LEAVE read_loop;
            end if;
            CALL addUserToTaskBlacklist(@userId, dependantTaskId);
        END LOOP;
        CLOSE dependantTasks;

        DELETE FROM UserTaskScores WHERE task_id = NEW.id;
    end if;
END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;



-- Dumping structure for trigger Solas-Match-Test.updateDependentTaskOnComplete
DROP TRIGGER IF EXISTS `updateDependentTaskOnComplete`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `updateDependentTaskOnComplete` BEFORE INSERT ON `TaskFileVersions` FOR EACH ROW BEGIN
DECLARE done INT DEFAULT 0;

DECLARE tID INT DEFAULT 0;

DECLARE cursor1 CURSOR FOR

select t.id

from Tasks t

join TaskPrerequisites tp

on t.id = tp.`task_id`

where t.`task-status_id`=1

and tp.`task_id-prerequisite`=new.task_id

and not exists (select 1

                    from TaskPrerequisites pre

                    join Tasks tsk
                    on tsk.id= pre.`task_id-prerequisite`
                    where pre.task_id=t.id
                    and tsk.`task-status_id`!=4
						  and tsk.`id`!=new.task_id);
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;




	if exists (
		select 1 from TaskFileVersions tf
		where tf.task_id = new.task_id
		and new.version_id > 0) then
		
		set @preStatus = null;
		select `task-status_id` into @preStatus from Tasks t where t.id=new.task_id;
		
		OPEN cursor1;
		read_loop: LOOP
		FETCH cursor1 INTO tID;
		IF done THEN
	   LEAVE read_loop;
	   END IF;
		call setTaskStatus(tID,2);
		END LOOP;
		CLOSE cursor1;
		
		

		UPDATE Tasks t SET t.`task-status_id`=4 WHERE t.id= new.task_id;
		
	end if;
	

END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;


-- Dumping structure for trigger Solas-Match-Test.updateTaskStatusDeletePrereq
DROP TRIGGER IF EXISTS `updateTaskStatusDeletePrereq`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `updateTaskStatusDeletePrereq` AFTER DELETE ON `TaskPrerequisites` FOR EACH ROW BEGIN
if exists 
	(select 1 
	 from Tasks t
	 where t.id = old.task_id
	 and t.`task-status_id`=1
	 and not exists (select 1 
						  from TaskPrerequisites tp
						  join Tasks tsk 
                    on tsk.id=tp.`task_id-prerequisite`
						  where tp.task_id=t.id
						  and tsk.`task-status_id`!= 4 )
	 )
	 then
		update Tasks set `task-status_id`=2
			 where id = old.task_id;
end if;
END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;


-- Dumping structure for trigger Solas-Match-Test.updateTaskStatusOnAddPrereq
DROP TRIGGER IF EXISTS `updateTaskStatusOnAddPrereq`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `updateTaskStatusOnAddPrereq` AFTER INSERT ON `TaskPrerequisites` FOR EACH ROW BEGIN
if exists 
	(select 1 
	 from Tasks t
	 where t.id = new.task_id
	 and t.`task-status_id`=2
	 and exists (select 1 
						  from TaskPrerequisites tp
						  join Tasks tsk 
                    on tsk.id=tp.`task_id-prerequisite`
						  where tp.task_id=t.id
						  and tsk.`task-status_id`!= 4 )
	 )
	 then
		update Tasks set `task-status_id`=1
			 where id = new.task_id;
end if;
END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;


-- Dumping structure for trigger Solas-Match-Test.validateHomepageInsert
DROP TRIGGER IF EXISTS `validateHomepageInsert`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `validateHomepageInsert` BEFORE INSERT ON `Organisations` FOR EACH ROW BEGIN
	if not (new.`home-page` like "http://%" or new.`home-page`  like "https://%") then
	set new.`home-page` = concat("http://",new.`home-page`);
	end if;
END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;


-- Dumping structure for trigger Solas-Match-Test.validateHomepageUpdate
DROP TRIGGER IF EXISTS `validateHomepageUpdate`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `validateHomepageUpdate` BEFORE UPDATE ON `Organisations` FOR EACH ROW BEGIN
	if not (new.`home-page` like "http://%" or new.`home-page`  like "https://%") then
	set new.`home-page` = concat("http://",new.`home-page`);
	end if;
END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;

-- Dumping structure for trigger SolasUpgrade2.userSecondaryLanguageInsert
DROP TRIGGER IF EXISTS `userSecondaryLanguageInsert`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `userSecondaryLanguageInsert` BEFORE INSERT ON `UserSecondaryLanguages` FOR EACH ROW BEGIN

    IF EXISTS (SELECT 1 FROM Users u WHERE u.id = NEW.user_id AND u.language_id = NEW.language_id AND u.country_id = NEW.country_id) THEN
            set NEW.user_id = null;
    END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;


-- Dumping structure for trigger Solas-Match.onUserUpdate
DROP TRIGGER IF EXISTS `onUserUpdate`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `onUserUpdate` BEFORE UPDATE ON `Users` FOR EACH ROW BEGIN
	IF ((( old.language_id IS not NULL) OR (OLD.language_id != NEW.language_id))
		AND (( old.country_id IS not NULL) OR (OLD.country_id != NEW.country_id))
		AND (( old.biography IS not NULL) OR (OLD.biography != NEW.biography))
		AND NOT EXISTS (SELECT 1 FROM UserBadges b WHERE b.user_id = OLD.id AND b.badge_id=3)) THEN
		
		INSERT INTO UserBadges VALUES(OLD.id, 3);
		
	END IF;	
END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;


-- Dumping structure for trigger debug-test3.afterTaskCreate
DROP TRIGGER IF EXISTS `afterTaskCreate`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `afterTaskCreate` AFTER INSERT ON `Tasks` FOR EACH ROW BEGIN
	Declare userId int;
	DECLARE done INT DEFAULT FALSE;
	DECLARE cur1 CURSOR FOR SELECT u.user_id FROM UserTrackedProjects u WHERE u.Project_id = NEW.project_id;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

	OPEN cur1;
	
	read_loop: LOOP
		FETCH cur1 INTO userId;
		IF done THEN
		 	LEAVE read_loop;
		END IF;
      INSERT INTO UserTrackedTasks VALUES(userId, NEW.id);
	END LOOP;
	CLOSE cur1;

END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;

-- Dumping structure for trigger debug-test3.afterDeleteTaskClaim
DROP TRIGGER IF EXISTS `afterDeleteTaskClaim`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `afterDeleteTaskClaim` AFTER DELETE ON `TaskClaims` FOR EACH ROW BEGIN
	IF EXISTS (SELECT 1 FROM Tasks t WHERE t.id = old.task_id AND t.`task-status_id` = 3) THEN
		UPDATE Tasks SET `task-status_id` = 2 WHERE id = old.task_id;
	END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;


/*---------------------------------------end of triggers-------------------------------------------*/
SET FOREIGN_KEY_CHECKS=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

