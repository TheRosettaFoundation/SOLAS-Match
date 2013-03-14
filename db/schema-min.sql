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

-- Dumping structure for table Solas-Match-Test.ArchivedProjects
CREATE TABLE IF NOT EXISTS `ArchivedProjects` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(4096) COLLATE utf8_unicode_ci DEFAULT NULL,
  `impact` varchar(4096) COLLATE utf8_unicode_ci NOT NULL,
  `deadline` datetime NOT NULL,
  `organisation_id` int(10) unsigned NOT NULL,
  `reference` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `word-count` int(10) DEFAULT NULL,
  `created` datetime NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `country_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `organisation_id` (`organisation_id`,`title`,`language_id`,`country_id`),
  CONSTRAINT `FK_archivedproject_organisation` FOREIGN KEY (`organisation_id`) REFERENCES `Organisations` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.ArchivedProjectsMetaData
CREATE TABLE IF NOT EXISTS `ArchivedProjectsMetaData` (
  `archived-project_id` int(10) unsigned NOT NULL,
  `archived-date` datetime NOT NULL,
  `user_id-archived` int(10) unsigned NOT NULL,
  UNIQUE KEY `archived-project_id` (`archived-project_id`),
  KEY `FK_ArchivedProjectsMetaData_Users` (`user_id-archived`),
  CONSTRAINT `FK_ArchivedProjectsMetaData_ArchivedProjects` FOREIGN KEY (`archived-project_id`) REFERENCES `ArchivedProjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedProjectsMetaData_Users` FOREIGN KEY (`user_id-archived`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.ArchivedTasks
CREATE TABLE IF NOT EXISTS `ArchivedTasks` (
  `id` bigint(20) unsigned NOT NULL,
  `project_id` int(20) unsigned NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `comment` varchar(4096) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deadline` datetime NOT NULL,
  `word-count` int(11) DEFAULT NULL,
  `created-time` datetime NOT NULL,
  `language_id-source` int(11) unsigned NOT NULL,
  `language_id-target` int(11) unsigned NOT NULL,
  `country_id-source` int(11) unsigned NOT NULL,
  `country_id-target` int(11) unsigned NOT NULL,
  `taskType_id` int(11) unsigned NOT NULL,
  `taskStatus_id` int(11) unsigned NOT NULL,
  `published` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `title` (`title`,`project_id`,`language_id-source`,`language_id-target`,`country_id-source`,`country_id-target`,`taskType_id`),
  KEY `FK_ArchivedTasks_Languages` (`language_id-source`),
  KEY `FK_ArchivedTasks_Languages_2` (`language_id-target`),
  KEY `FK_ArchivedTasks_Countries` (`country_id-source`),
  KEY `FK_ArchivedTasks_Countries_2` (`country_id-target`),
  KEY `FK_ArchivedTasks_TaskTypes` (`taskType_id`),
  KEY `FK_ArchivedTasks_TaskStatus` (`taskStatus_id`),
  KEY `id` (`id`),
  CONSTRAINT `FK_ArchivedTasks_Countries` FOREIGN KEY (`country_id-source`) REFERENCES `Countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasks_Countries_2` FOREIGN KEY (`country_id-target`) REFERENCES `Countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasks_Languages` FOREIGN KEY (`language_id-source`) REFERENCES `Languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasks_Languages_2` FOREIGN KEY (`language_id-target`) REFERENCES `Languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasks_TaskStatus` FOREIGN KEY (`taskStatus_id`) REFERENCES `TaskStatus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasks_TaskTypes` FOREIGN KEY (`taskType_id`) REFERENCES `TaskTypes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.ArchivedTasksMetadata
CREATE TABLE IF NOT EXISTS `ArchivedTasksMetadata` (
  `archivedTask_id` bigint(20) unsigned NOT NULL,
  `user_id-claimed` int(10) unsigned DEFAULT NULL,
  `user_id-archived` int(10) unsigned NOT NULL,
  `archived-date` datetime NOT NULL,
  UNIQUE KEY `archivedTask_id` (`archivedTask_id`),
  KEY `FK_ArchivedTasksMetadata_Users` (`user_id-claimed`),
  KEY `FK_ArchivedTasksMetadata_Users_2` (`user_id-archived`),
  CONSTRAINT `FK_ArchivedTasksMetadata_ArchivedTasks` FOREIGN KEY (`archivedTask_id`) REFERENCES `ArchivedTasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasksMetadata_Users` FOREIGN KEY (`user_id-claimed`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasksMetadata_Users_2` FOREIGN KEY (`user_id-archived`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


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

-- Dumping data for table Solas-Match-Test.Badges: ~4 rows (approximately)
/*!40000 ALTER TABLE `Badges` DISABLE KEYS */;
REPLACE INTO `Badges` (`id`, `owner_id`, `title`, `description`) VALUES
	(3, NULL, 'Profile-Filler', 'Filled in required info for user profile.'),
	(4, NULL, 'Registered', 'Successfully set up an account'),
	(5, NULL, 'Native-Language', 'Filled in your native language on your user profile.');


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
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `home-page` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `biography` varchar(4096) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.OrgRequests
CREATE TABLE IF NOT EXISTS `OrgRequests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `org_id` int(11) unsigned NOT NULL,
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
  `user_id` int(11) unsigned NOT NULL,
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
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(4096) COLLATE utf8_unicode_ci NOT NULL,
  `impact` varchar(4096) COLLATE utf8_unicode_ci NOT NULL,
  `deadline` datetime DEFAULT NULL,
  `organisation_id` int(10) unsigned NOT NULL,
  `reference` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `word-count` int(10) DEFAULT NULL,
  `created` datetime NOT NULL,
  `language_id` int(10) unsigned DEFAULT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `organisation_id` (`organisation_id`,`title`,`language_id`,`country_id`),
  KEY `FK_Projects_Languages` (`language_id`),
  KEY `FK_Projects_Countries` (`country_id`),
  CONSTRAINT `FK_Projects_Countries` FOREIGN KEY (`country_id`) REFERENCES `Countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_Projects_Languages` FOREIGN KEY (`language_id`) REFERENCES `Languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_project_organisation` FOREIGN KEY (`organisation_id`) REFERENCES `Organisations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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


-- Dumping structure for table Solas-Match-Test.Statistics
CREATE TABLE IF NOT EXISTS `Statistics` (
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `value` double NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


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
  `user_id` int(11) unsigned NOT NULL,
  `claimed-time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Tasks` (`task_id`,`user_id`),
  KEY `FK_task_claim_user` (`user_id`),
  CONSTRAINT `FK_task_claim_task` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_task_claim_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.TaskFileVersions
CREATE TABLE IF NOT EXISTS `TaskFileVersions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) unsigned NOT NULL,
  `version_id` int(11) NOT NULL COMMENT 'Gets incremented within the code',
  `filename` text COLLATE utf8_unicode_ci NOT NULL,
  `content-type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'Null while we don''t have logging in',
  `upload-time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `taskFile` (`task_id`,`version_id`,`user_id`),
  KEY `FK_task_file_version_user` (`user_id`),
  CONSTRAINT `FK_task_file_version_task1` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_task_file_version_user1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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


-- Dumping structure for table Solas-Match-Test.Tasks
CREATE TABLE IF NOT EXISTS `Tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(20) unsigned NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `word-count` int(11) DEFAULT NULL,
  `language_id-source` int(11) unsigned NOT NULL,
  `language_id-target` int(11) unsigned NOT NULL,
  `country_id-source` int(11) unsigned NOT NULL,
  `country_id-target` int(11) unsigned NOT NULL,
  `created-time` datetime NOT NULL,
  `deadline` datetime NOT NULL,
  `comment` varchar(4096) COLLATE utf8_unicode_ci DEFAULT NULL,
  `task-type_id` int(11) unsigned NOT NULL,
  `task-status_id` int(11) unsigned NOT NULL,
  `published` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
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
  `task_id` bigint(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
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
	(1, "Chunking"),
	(2, "Translation"),
	(3, "Proofreading"),
	(4, "Post-editing");



-- Dumping structure for table Solas-Match-Test.UserBadges
CREATE TABLE IF NOT EXISTS `UserBadges` (
  `user_id` int(11) unsigned NOT NULL,
  `badge_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`badge_id`),
  UNIQUE KEY `userBadge` (`user_id`,`badge_id`),
  KEY `FK_user_badges_badges` (`badge_id`),
  CONSTRAINT `FK_user_badges_badges` FOREIGN KEY (`badge_id`) REFERENCES `Badges` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_badges_users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.UserNotifications
CREATE TABLE IF NOT EXISTS `UserNotifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `task_id` bigint(11) unsigned NOT NULL,
  `created-time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_task_id` (`user_id`,`task_id`),
  KEY `FK_user_notifications_task` (`task_id`),
  CONSTRAINT `FK_user_notifications_task1` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_notifications_user1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  KEY `FK_user_language` (`language_id`),
  KEY `FK_user_country` (`country_id`),
  CONSTRAINT `FK_user_country` FOREIGN KEY (`country_id`) REFERENCES `Countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_language` FOREIGN KEY (`language_id`) REFERENCES `Languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.UserTags
CREATE TABLE IF NOT EXISTS `UserTags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userTag` (`user_id`,`tag_id`),
  KEY `FK_user_tag_user1` (`tag_id`),
  CONSTRAINT `FK_user_tag_tag1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_tag_user1` FOREIGN KEY (`tag_id`) REFERENCES `Tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.UserTaskScores
CREATE TABLE IF NOT EXISTS `UserTaskScores` (
  `user_id` int(11) unsigned NOT NULL,
  `task_id` bigint(11) unsigned NOT NULL,
  `score` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`user_id`,`task_id`),
  UNIQUE KEY `taskScore` (`task_id`,`user_id`),
  CONSTRAINT `FK_user_task_score_task1` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_task_score_user1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.UserTrackedProjects
CREATE TABLE IF NOT EXISTS `UserTrackedProjects` (
  `user_id` int(10) unsigned DEFAULT NULL,
  `Project_id` int(10) unsigned DEFAULT NULL,
  UNIQUE KEY `user_id` (`user_id`,`Project_id`),
  KEY `FK_UserTrackedProjects_Projects` (`Project_id`),
  CONSTRAINT `FK_UserTrackedProjects_Projects` FOREIGN KEY (`Project_id`) REFERENCES `Projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_UserTrackedProjects_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table Solas-Match-Test.UserTrackedTasks
CREATE TABLE IF NOT EXISTS `UserTrackedTasks` (
  `user_id` int(10) unsigned DEFAULT NULL,
  `task_id` bigint(10) unsigned DEFAULT NULL,
  UNIQUE KEY `user_id` (`user_id`,`task_id`),
  KEY `FK_UserTrackedTasks_Tasks` (`task_id`),
  CONSTRAINT `FK_UserTrackedTasks_Tasks` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_UserTrackedTasks_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.

/*---------------------------------------end of tables---------------------------------------------*/

/*---------------------------------------start of procs--------------------------------------------*/

-- Dumping structure for procedure Solas-Match-Test.acceptMemRequest
DROP PROCEDURE IF EXISTS `acceptMemRequest`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `acceptMemRequest`(IN `uID` INT, IN `orgID` INT)
BEGIN
	INSERT INTO OrganisationMembers (user_id, organisation_id) VALUES (uID,orgID);
	call removeMembershipRequest(uID,orgID);
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


-- Dumping structure for procedure Solas-Match-Test.archiveProject
DROP PROCEDURE IF EXISTS `archiveProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `archiveProject`(IN `projectId` INT, IN `user_id` INT)
    MODIFIES SQL DATA
BEGIN
    if not exists(select 1 from ArchivedProjects where id = projectId) then
            INSERT INTO `ArchivedProjects` (id, title, description, impact, deadline, organisation_id, reference, `word-count`, created,country_id,language_id)

              SELECT *
              FROM Projects p
              WHERE p.id=projectId;

            INSERT INTO `ArchivedProjectsMetaData` (`archived-project_id`, `archived-date`, `user_id-archived`)
              VALUES (projectId, NOW(), user_id);

            DELETE FROM Projects WHERE id=projectId;
            SELECT 1 as result;
    else
            SELECT 0 as result;
    end if;	    
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.archiveTask
DROP PROCEDURE IF EXISTS `archiveTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `archiveTask`(IN `tID` INT, IN `uID` INT)
    MODIFIES SQL DATA
BEGIN
	if not exists(select 1 from ArchivedTasks where id = tID) then
		INSERT INTO `ArchivedTasks` (`id`, `project_id`, `title`, `word-count`, `language_id-source`, `language_id-target`, `country_id-source`, `country_id-target`, `created-time`, `deadline`, `comment`, `taskType_id`, `taskStatus_id`, `published`)
			SELECT * FROM Tasks t WHERE t.id = tID;
		
		INSERT INTO ArchivedTasksMetadata 
		(`user_id-claimed`,`user_id-archived`,`archived-date`,`archivedTask_id`) 
		select 
		(SELECT  tc.user_id
			FROM TaskClaims tc
			WHERE tc.task_id = tID
			limit 1)  as `user_id-claimed`
		,uID
		,now()
		,tID;
	   
	   DELETE FROM Tasks WHERE id = tID ;
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


-- Dumping structure for procedure Solas-Match-Test.claimTask
DROP PROCEDURE IF EXISTS `claimTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `claimTask`(IN `tID` INT, IN `uID` INT)
BEGIN
	if not EXISTS(select 1 from TaskClaims tc where tc.task_id=tID and tc.user_id=uID) then
		insert into TaskClaims  (task_id,user_id,`claimed-time`) values (tID,uID,now());
		update Tasks set `task-status_id`=3 where id = tID;
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
delete from Badges where Badges.id = id;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.deleteOrg
DROP PROCEDURE IF EXISTS `deleteOrg`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteOrg`()
BEGIN
if EXISTS (select 1 from Organisations where Organisations.id=id) then
	delete from Organisations where Organisations.id=id;
	select 1 as result;
else
	select 0 as result;
end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.deleteTag
DROP PROCEDURE IF EXISTS `deleteTag`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteTag`()
BEGIN
if EXISTS (select 1 from Tags where Tags.id=id) then
	delete from Tags where Tags.id=id;
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


-- Dumping structure for procedure Solas-Match-Test.findOrganisation
DROP PROCEDURE IF EXISTS `findOrganisation`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `findOrganisation`(IN `id` INT)
    COMMENT 'finds an organisation by the data passed in.'
BEGIN
	SELECT *
	FROM Organisations o
	WHERE o.id=id; 
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.findOrganisationsUserBelongsTo
DROP PROCEDURE IF EXISTS `findOrganisationsUserBelongsTo`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `findOrganisationsUserBelongsTo`(IN `id` INT)
BEGIN
	SELECT o.*
	FROM OrganisationMembers om join Organisations o on om.organisation_id=o.id
	WHERE om.user_id = id;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getArchivedProject
DROP PROCEDURE IF EXISTS `getArchivedProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getArchivedProject`(IN `projectId` INT, IN `titleText` VARCHAR(128), IN `descr` VARCHAR(4096), IN `imp` VARCHAR(4096), IN `deadlineTime` DATETIME, IN `orgId` INT, IN `ref` VARCHAR(128), IN `wordCount` INT, IN `createdTime` DATETIME, IN `archiveDate` DATETIME, IN `archiverId` INT)
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

    set @q = "SELECT p.id, p.title, p.description, p.impact, p.deadline, p.organisation_id, p.reference, p.`word-count`, p.created, (select code from Languages where id =p.language_id) as language_id, (select code from Countries where id =p.country_id) as country_id, m.`archived-date`, m.`user_id-archived` FROM ArchivedProjects p JOIN ArchivedProjectsMetaData m ON p.id=m.`archived-project_id` WHERE 1";
    if projectId is not null then
        set @q = CONCAT(@q, " and p.id=", projectId);
    end if;
    if titleText is not null then
        set @q = CONCAT(@q, " and title='", titleText, "'");
    end if;
    if descr is not null then
        set @q = CONCAT(@q, " and description='", descr, "'");
    end if;
    if imp is not null then
        set @q = CONCAT(@q, " and impact='", imp, "'");
    end if;
    if (deadlineTime is not null and deadlineTime!='0000-00-00 00:00:00') then
        set @q = CONCAT(@q, " and deadline='", deadlineTime, "'");
    end if;
    if orgId is not null then
        set @q = CONCAT(@q, " and organisation_id=", orgId);
    end if;
    if ref is not null then
        set @q = CONCAT(@q, " and reference='", ref, "'");
    end if;
    if wordCount is not null then
        set @q = CONCAT(@q, " and `word-count`=", wordCount);
    end if;
    if (createdTime is not null and createdTime!='0000-00-00 00:00:00') then
        set @q = CONCAT(@q, " and created='", createdTime, "'");
    end if;
    if (archiveDate is not null and archiveDate!='0000-00-00 00:00:00') then
        set @q = CONCAT(@q, " and m.`archived-date`='", archiveDate, "'");
    end if;
    if archiverId is not null then
        set @q = CONCAT(@q, " and m.`user_id-archived`=", archiverId);
    end if;

    PREPARE stmt from @q;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getArchivedTasks
DROP PROCEDURE IF EXISTS `getArchivedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getArchivedTasks`(IN `arc_id` INT, IN `t_id` INT, IN `o_id` INT)
BEGIN

    if arc_id='' then set arc_id=null; end if;
    if t_id='' then set t_id=null; end if;
    if o_id='' then set o_id=null; end if;
    set @q="SELECT * FROM ArchivedTasks
                WHERE 1";
    if arc_id is not null then
        set @q=CONCAT(@q, " and id=", arc_id);
    end if;
    if t_id is not null then
        set @q=CONCAT(@q, " and task_id=", t_id);
    end if;
    if o_id is not null then
        set @q=CONCAT(@q, " and organisation_id=", o_id);
    end if;
    
    PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;

END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getBadge
DROP PROCEDURE IF EXISTS `getBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getBadge`(IN `id` INT, IN `name` VARCHAR(128), IN `des` VARCHAR(512), IN `orgID` INT)
    READS SQL DATA
BEGIN
	if id='' then set id=null;end if;
	if des='' then set des=null;end if;
	if name='' then set name=null;end if;
	if orgID='' then set orgID=null;end if;
	set @q= "SELECT *FROM Badges b where 1 ";-- set update
	if id is not null then 
#set paramaters to be updated
		set @q = CONCAT(@q," and b.id=",id) ;
	end if;
	if des is not null then 
		set @q = CONCAT(@q," and b.description='",des,"'") ;
	end if;
	if name is not null then 
		set @q = CONCAT(@q," and b.title='",name,"'") ;
	end if;
	
	if orgID is not null then 
		set @q = CONCAT(@q," and b.owner_id='",orgID,"'") ;
	end if;
	
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getCountries
DROP PROCEDURE IF EXISTS `getCountries`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCountries`()
BEGIN
SELECT  `en-name` as country, code, id FROM Countries order by `en-name`;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getCountry
DROP PROCEDURE IF EXISTS `getCountry`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCountry`(IN `id` INT, IN `code` VARCHAR(3), IN `name` VARCHAR(128))
BEGIN
	if id='' then set id=null;end if;
	if code='' then set code=null;end if;
	if name='' then set name=null;end if;
	set @q= "select `en-name` as country, code, id from Countries c where 1 ";-- set update
	if id is not null then 
#set paramaters to be updated
		set @q = CONCAT(@q," and c.id=",id) ;
	end if;
	if code is not null then 
		set @q = CONCAT(@q," and c.code='",code,"'") ;
	end if;
	if name is not null then 
		set @q = CONCAT(@q," and c.`en-name`='",name,"'") ;
	end if;
	
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getLanguage
DROP PROCEDURE IF EXISTS `getLanguage`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLanguage`(IN `id` INT, IN `code` VARCHAR(3), IN `name` VARCHAR(128))
BEGIN
	if id='' then set id=null;end if;
	if code='' then set code=null;end if;
	if name='' then set name=null;end if;
	set @q= "select `en-name` as language, code, id from Languages l where 1 ";-- set update
	if id is not null then 
#set paramaters to be updated
		set @q = CONCAT(@q," and l.id=",id) ;
	end if;
	if code is not null then 
		set @q = CONCAT(@q," and l.code='",code,"'") ;
	end if;
	if name is not null then 
		set @q = CONCAT(@q," and l.`en-name`='",name,"'") ;
	end if;
	
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getLanguages
DROP PROCEDURE IF EXISTS `getLanguages`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLanguages`()
BEGIN
SELECT  `en-name` as language, code, id FROM Languages order by `en-name`;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getLatestAvailableTasks
DROP PROCEDURE IF EXISTS `getLatestAvailableTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLatestAvailableTasks`(IN `lim` INT)
BEGIN
	 if (lim= '') then set lim=null; end if;
	 if(lim is not null) then
    set @q = Concat("SELECT t.* FROM Tasks AS t WHERE NOT exists (SELECT 1 FROM TaskClaims where TaskClaims.task_id = t.id) AND t.published = 1 AND t.`task-status_id` = 2 ORDER BY `created-time` DESC LIMIT ",lim);
    else
    set @q = "SELECT t.* FROM Tasks AS t WHERE NOT exists (SELECT 1 FROM TaskClaims where TaskClaims.task_id = t.id) AND t.published = 1 AND t.`task-status_id` = 2 ORDER BY `created-time` DESC";
    end if;
    PREPARE stmt FROM @q;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getLatestFileVersion
DROP PROCEDURE IF EXISTS `getLatestFileVersion`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLatestFileVersion`(IN `id` INT, IN `uID` INT)
BEGIN
	if uID='' then set uID=null;end if;
	set @q= "SELECT max(version_id) as latest_version  FROM TaskFileVersions tfv ";-- set update
	set @q = CONCAT(@q," where tfv.task_id =",id);
	if uID is not null then 
		set @q = CONCAT(@q," and tfv.user_id=",uID);
	end if;
	
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getLCID
DROP PROCEDURE IF EXISTS `getLCID`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLCID`(IN `lang` VARCHAR(128), IN `countryName` VARCHAR(128))
BEGIN
set @ll = "";
set @cc = "";
select c.code into @cc from Countries c where c.`en-name` = countryName;
select l.code into @ll from Languages l where l.`en-name` = lang;
select concat(@ll,"-",@cc) as lcid;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getMembershipRequests
DROP PROCEDURE IF EXISTS `getMembershipRequests`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMembershipRequests`(IN `orgID` INT)
BEGIN
	SELECT *
	FROM OrgRequests
   WHERE org_id = orgID
   ORDER BY `request-datetime` DESC;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getOrg
DROP PROCEDURE IF EXISTS `getOrg`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrg`(IN `id` INT, IN `name` VARCHAR(50), IN `url` VARCHAR(50), IN `bio` vARCHAR(50))
BEGIN
	if id='' then set id=null;end if;
	if name='' then set name=null;end if;
	if url='' then set url=null;end if;
	if bio='' then set bio=null;end if;
	set @q= "select * from Organisations o where 1 ";
	if id is not null then 
		set @q = CONCAT(@q," and o.id=",id) ;
	end if;
	if name is not null then 
		set @q = CONCAT(@q," and o.name='",name,"'") ;
	end if;
	if url is not null then 
		set @q = CONCAT(@q," and o.`home-page`='",url,"'") ;
	end if;
	if bio is not null then 
		set @q = CONCAT(@q," and o.biography='",bio,"'") ;
	end if;
	
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getOrgByUser
DROP PROCEDURE IF EXISTS `getOrgByUser`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrgByUser`(IN `id` INT)
BEGIN
	SELECT *
	FROM Organisations o
	WHERE o.id IN (SELECT organisation_id
						 FROM OrganisationMembers
					 	 WHERE user_id=id); 
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getOrgMembers
DROP PROCEDURE IF EXISTS `getOrgMembers`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrgMembers`(IN `id` INT)
BEGIN
select u.id,`display-name`,email,password,biography,(select l.code from Languages l where l.id =u.`language_id`) as `language_id` ,(select c.code from Countries c where c.id =u.`country_id`) as `country_id`, nonce,`created-time`
	FROM OrganisationMembers om JOIN Users u ON om.user_id = u.id
	WHERE organisation_id=id;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getPasswordResetRequests
DROP PROCEDURE IF EXISTS `getPasswordResetRequests`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getPasswordResetRequests`(IN `unique_id` CHAR(40), IN `userId` INT(11))
BEGIN
	if unique_id='' then set unique_id=null;end if;
    if userId='' then set userId=null;end if;
    set @q= "SELECT * FROM PasswordResetRequests p WHERE 1 ";
    if unique_id is not null then
        set @q= CONCAT(@q," and p.uid='",unique_id,"'");
    end if;
    if userId is not null then
        set @q= CONCAT(@q, " and p.user_id=", userId);
    end if;

	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getProject
DROP PROCEDURE IF EXISTS `getProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getProject`(IN `projectId` INT, IN `titleText` VARCHAR(128), IN `descr` VARCHAR(4096), IN `impact` VARCHAR(4096), IN `deadlineTime` DATETIME, IN `orgId` INT, IN `ref` VARCHAR(128), IN `wordCount` INT, IN `createdTime` DATETIME, IN `sCC` VARCHAR(3), IN `sCode` VARCHAR(3))
    READS SQL DATA
BEGIN
    if projectId='' then set projectId=null;end if;
    if titleText='' then set titleText=null;end if;
    if descr='' then set descr=null;end if;
    if impact='' then set impact=null;end if;
    if deadlineTime='' then set deadlineTime=null;end if;
    if orgId='' then set orgId=null;end if;
    if ref='' then set ref=null;end if;
    if wordCount='' then set wordCount=null;end if;
    if createdTime='' then set createdTime=null;end if;
    if sCC="" then set sCC=null; end if;
    if sCode="" then set sCode=null; end if;

    set @q="SELECT id, title, description, impact, deadline,organisation_id,reference,`word-count`, created,(select code from Countries where id =p.`country_id`) as country_id,(select code from Languages where id =p.`language_id`) as language_id, (select sum(tsk.`task-status_id`)/(count(tsk.`task-status_id`)*4) from Tasks tsk where tsk.project_id=p.id)as 'status'  FROM Projects p WHERE 1";
    if projectId is not null then
        set @q = CONCAT(@q, " and p.id=", projectId);
    end if;
    if titleText is not null then
        set @q = CONCAT(@q, " and p.title='", titleText, "'");
    end if;
    if descr is not null then
        set @q = CONCAT(@q, " and p.description='", descr, "'");
    end if;
    if impact is not null then
        set @q = CONCAT(@q, " and p.impact='", impact, "'");
    end if;
    if (deadlineTime is not null and deadlineTime!='0000-00-00 00:00:00') then
        set @q = CONCAT(@q, " and p.deadline='", deadlineTime, "'");
    end if;
    if orgId is not null then
        set @q = CONCAT(@q, " and p.organisation_id=", orgId);
    end if;
    if ref is not null then
        set @q = CONCAT(@q, " and p.reference='", ref, "'");
    end if;
    if wordCount is not null then
        set @q = CONCAT(@q, " and p.`word-count`=", wordCount);
    end if;
    if (createdTime is not null and createdTime!='0000-00-00 00:00:00') then
        set @q = CONCAT(@q, " and p.created='", createdTime, "'");
    end if;
    if sCC is not null then
    	set @scID=null;
		select c.id into @scID from Countries c where c.code=sCC;
    	set @q = CONCAT(@q, " and p.country_id=",@scID);
    end if;
    if sCode is not null then
      set @sID=null;
		select c.id into @sID from Languages l where l.code=sCode;
    	set @q = CONCAT(@q, " and p.language_id=", @sID);
    end if;

    PREPARE stmt FROM @q;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getProjectByTag
DROP PROCEDURE IF EXISTS `getProjectByTag`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getProjectByTag`(IN `tID` INT)
BEGIN
 select id, title, description, impact, deadline,organisation_id,reference,`word-count`, created,(select code from Countries where id =p.`country_id`) as country_id,(select code from Languages where id =p.`language_id`) as language_id, (select sum(tsk.`task-status_id`)/(count(tsk.`task-status_id`)*4) from Tasks tsk where tsk.project_id=p.id)as 'status'
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
   

    set @q="SELECT * FROM ProjectFiles p WHERE 1";
    
	 if pID is not null then
        set @q = CONCAT(@q, " and p.project_id=", pID);
    end if;
  	 if uID is not null then
        set @q = CONCAT(@q, " and p.user_id=", uID);
    end if;
  	 if fName is not null then
        set @q = CONCAT(@q, " and p.filename=", fName);
    end if;
 	 if token is not null then
        set @q = CONCAT(@q, " and p.`file-token`=",  token);
    end if;
 	 if mime is not null then
        set @q = CONCAT(@q, " and p.`mime-type`=",  mime);
    end if;

    
    

    PREPARE stmt FROM @q;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
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


-- Dumping structure for procedure Solas-Match-Test.getStatistics
DROP PROCEDURE IF EXISTS `getStatistics`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getStatistics`(IN `statName` VARCHAR(128))
BEGIN
	IF statName = '' THEN SET statName = NULL; END IF;
	
	set @q = "SELECT * FROM Statistics st where 1";
	
	if statName is not null then 
		set @q = CONCAT(@q," and st.name='", statName,"'");
	end if;
	
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getSubscribedUsers
DROP PROCEDURE IF EXISTS `getSubscribedUsers`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getSubscribedUsers`(IN `taskId` INT)
BEGIN
    SELECT *
    FROM UserTrackedTasks
    WHERE task_id = taskId;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTag
DROP PROCEDURE IF EXISTS `getTag`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTag`(IN `id` INT, IN `name` VARCHAR(50))
BEGIN
	if id='' then set id=null;end if;
	if name='' then set name=null;end if;
	set @q= "select t.id , t.label from Tags t where 1 ";-- set update
	if id is not null then 
#set paramaters to be updated
		set @q = CONCAT(@q," and t.id=",id) ;
	end if;
	if name is not null then 
		set @q = CONCAT(@q," and t.label='",name,"'") ;
	end if;
	
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTaggedTasks
DROP PROCEDURE IF EXISTS `getTaggedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaggedTasks`(IN `tID` INT, IN `lim` INT)
    READS SQL DATA
BEGIN
	set @q = Concat("SELECT id 
                         FROM Tasks t join ProjectTags pt on pt.project_id=t.project_id
                         WHERE pt.tag_id=? AND NOT  exists (
							  	SELECT 1		
								FROM TaskClaims
								WHERE task_id = t.id
							)
                         AND t.published = 1
                         AND t.`task-status_id` = 2 
                         ORDER BY t.`created-time` DESC
                         LIMIT ",lim);
        PREPARE stmt FROM @q;
        set @tID=tID;
        EXECUTE stmt using @tID;
        DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTask
DROP PROCEDURE IF EXISTS `getTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTask`(IN `id` BIGINT, IN `projectID` INT, IN `name` VARCHAR(50), IN `wordCount` INT, IN `sCode` VARCHAR(3), IN `tCode` VARCHAR(3), IN `created` DATETIME, IN `sCC` VARCHAR(3), IN `tCC` VARCHAR(3), IN `taskComment` VARCHAR(4096), IN `tType` INT, IN `tStatus` INT, IN `pub` TINYINT, IN `dLine` DATETIME)
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
	
	
	set @q= "select id,project_id,title,`word-count`,(select code from Languages where id =t.`language_id-source`) as `language_id-source`,(select code from Languages where id =t.`language_id-target`) as `language_id-target`,`created-time`, (select code from Countries where id =t.`country_id-source`) as `country_id-source`, (select code from Countries where id =t.`country_id-target`) as `country_id-target`, comment,  `task-type_id`, `task-status_id`, published, deadline from Tasks t where 1";-- set update
	if id is not null then 
#set paramaters to be updated
		set @q = CONCAT(@q," and t.id=",id) ;
	end if;
	if projectID is not null then 
		set @q = CONCAT(@q," and t.project_id=",projectID) ;
	end if;
	if name is not null then 
		set @q = CONCAT(@q," and t.title='",name,"'") ;
	end if;
	if sCode is not null then 
		set @sID=null;
		select l.id into @sID from Languages l where l.code=sCode;
		set @q = CONCAT(@q," and t.`language_id-source`=",@sID) ;
	end if;
	if tCode is not null then 
		set @tID=null;
		select l.id into @tID from Languages l where l.code=tCode;
		set @q = CONCAT(@q," and t.`language_id-target`=",@tID) ;
	end if;
	if sCC is not null then 
		set @scid=null;
			select c.id into @scid from Countries c where c.code=sCC;
		set @q = CONCAT(@q," and t.`country_id-source`=",@scid) ;
	end if;
	if tCC is not null then 
		set @tcid=null;
			select c.id into @tcid from Countries c where c.code=tCC;
		set @q = CONCAT(@q," and t.`country_id-target`=",@tcid) ;
	end if;
	if wordCount is not null then 
		set @q = CONCAT(@q," and t.`word-count`=",wordCount) ;
	end if;
	if (created is not null  and created!='0000-00-00 00:00:00') then 
		set @q = CONCAT(@q," and t.`created-time`='",created,"'") ;
	end if;
	if taskComment is not null then 
		set @q = CONCAT(@q," and t.`comment`='",taskComment,"'") ;
	end if;
	if tStatus is not null then 
		set @q = CONCAT(@q," and t.`task-status_id`=",tStatus) ;
	end if;
	if tType is not null then 
		set @q = CONCAT(@q," and t.`task-type_id`=",tType) ;
	end if;
	if pub is not null then 
		set @q = CONCAT(@q," and t.`published`=",pub) ;
	end if;
	if dLine is not null and dLine!='0000-00-00 00:00:00' then 
		set @q = CONCAT(@q," and t.`deadline`='",dLine,"'") ;
	end if;

	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
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
		set @q= "select task_id, version_id, filename, `content-type`, user_id, `upload-time` from TaskFileVersions t where 1 ";
	if tID is not null then 
		set @q = CONCAT(@q," and t.task_id=",tID) ;
	end if;
	if vID is not null then 
		set @q = CONCAT(@q," and t.version_id=",vID) ;
	end if;
	if name is not null then 
		set @q = CONCAT(@q," and t.filename='",name,"'") ;
	end if;
	if content is not null then 
		set @q = CONCAT(@q," and t.`content-type`='",content,"'") ;
	end if;
	if uID is not null then 
		set @q = CONCAT(@q," and t.user_id=",uID) ;
	end if;
	if (uTime is not null  and uTime!='0000-00-00 00:00:00')then 
		set @q = CONCAT(@q," and t.`upload-time`='",uTime,"'") ;
	end if;
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTaskPreReqs
DROP PROCEDURE IF EXISTS `getTaskPreReqs`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskPreReqs`(IN `taskId` INT)
    READS SQL DATA
BEGIN
	SELECT t.id, t.project_id, t.title, t.`word-count`,
	(SELECT lg.code FROM Languages lg WHERE lg.id=`language_id-source`) as `language_id-source`,
	(SELECT lg.code FROM Languages lg WHERE lg.id=`language_id-target`) as `language_id-target`,
	(SELECT ct.code FROM Countries ct WHERE ct.id=t.`country_id-source`) as `country_id-source`,
	(SELECT ct.code FROM Countries ct WHERE ct.id=t.`country_id-target`)as `country_id-target`,
	t.`created-time`, t.deadline, t.`comment`, t.`task-type_id`, t.`task-status_id`, t.published
		
	FROM Tasks t JOIN TaskPrerequisites tp ON tp.`task_id-prerequisite`=t.id
	WHERE tp.task_id=taskId;	
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTasksByOrgIDs
DROP PROCEDURE IF EXISTS `getTasksByOrgIDs`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTasksByOrgIDs`(IN `orgIDs` VARCHAR(1028), IN `orderby` VARCHAR(256))
    READS SQL DATA
BEGIN
	if orgIDs='' then set orgIDs=null;end if;
	if orderby='' then set orderby=null;end if;
	set @q= "SELECT id,organisation_id,title,`word-count`,source_id,target_id,`created-time`, `country_id-source`, `country_id-target` FROM Tasks WHERE 1 ";-- set update
	if orgIDs is not null then 
		set @q = CONCAT(@q," and organisation_id IN (",orgIDs,")") ;
	end if;

	if orderby is not null then 
		set @q = CONCAT(@q," order by ",orderby) ;
	end if;
	
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
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


-- Dumping structure for procedure Solas-Match-Test.getTaskTranslator
DROP PROCEDURE IF EXISTS `getTaskTranslator`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskTranslator`(IN `taskId` INT)
BEGIN
    SELECT user_id
    FROM TaskClaims
    WHERE task_id=taskId;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTopTags
DROP PROCEDURE IF EXISTS `getTopTags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTopTags`(IN `lim` INT)
    READS SQL DATA
BEGIN
set @q = Concat("   SELECT t.label AS label,t.id as d, COUNT( pt.tag_id ) AS frequency
                    FROM ProjectTags AS pt 
                    join Tags AS t on pt.tag_id = t.id
                    join Tasks tsk on tsk.project_id=pt.project_id
                    WHERE not exists (SELECT 1
                                       FROM TaskClaims tc
                                       where tc.task_id=tsk.id
                                     )
                    GROUP BY pt.tag_id
                    ORDER BY frequency DESC
                    LIMIT ",lim);
        PREPARE stmt FROM @q;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTotalArchivedTasks
DROP PROCEDURE IF EXISTS `getTotalArchivedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTotalArchivedTasks`(IN `dateTime` DATETIME)
BEGIN
    if dateTime is null then set dateTime='0000-00-00 00:00:00';end if;
    SET @archivedTasks = NULL;
    SELECT count(1) INTO @archivedTasks FROM ArchivedTasks ta
    WHERE ta.`created-time` >= dateTime;
    SELECT @archivedTasks AS result;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTotalClaimedTasks
DROP PROCEDURE IF EXISTS `getTotalClaimedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTotalClaimedTasks`(IN `dateTime` DATETIME)
BEGIN
    if dateTime is null then set dateTime='0000-00-00 00:00:00';end if;
    SET @claimedTasks = NULL;
    SELECT count(1) INTO @claimedTasks FROM TaskClaims tc
    WHERE tc.`claimed-time` >= dateTime;
    SELECT @claimedTasks AS result;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTotalOrgs
DROP PROCEDURE IF EXISTS `getTotalOrgs`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTotalOrgs`()
BEGIN
    SET @totalOrgs = NULL;	
    SELECT count(1) INTO @totalOrgs FROM Organisations;
    SELECT @totalOrgs AS result;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTotalTasks
DROP PROCEDURE IF EXISTS `getTotalTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTotalTasks`(IN `dateTime` DATETIME)
BEGIN
    SET @totalTasks = NULL;
    SET @claimedTasks = NULL;
    SET @unclaimedTasks = NULL;
    SET @archivedTasks = NULL;
    if dateTime is null then set dateTime='0000-00-00 00:00:00';end if;

    SELECT count(1) INTO @claimedTasks FROM TaskClaims tc
    WHERE tc.`claimed-time` >= dateTime;	

    SELECT count(1) into @unclaimedTasks from Tasks t
    WHERE t.id NOT IN
    (
        SELECT task_id
        FROM  TaskClaims
    );

    SELECT count(1) INTO @archivedTasks FROM ArchivedTasks ta
    WHERE ta.`created-time` >= dateTime;	

    SET @totalTasks = @claimedTasks + @unclaimedTasks + @archivedTasks;	
    SELECT @totalTasks AS result;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTotalUnclaimedTasks
DROP PROCEDURE IF EXISTS `getTotalUnclaimedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTotalUnclaimedTasks`(IN `dateTime` DATETIME)
BEGIN
   if dateTime is null then set dateTime='0000-00-00 00:00:00';end if;
   SET @unclaimedTasks = NULL;
   SELECT count(1) into @unclaimedTasks from Tasks t
	WHERE t.id NOT IN
            (
                SELECT task_id
                FROM  TaskClaims
            );
	SELECT @unclaimedTasks AS result;	
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTotalUsers
DROP PROCEDURE IF EXISTS `getTotalUsers`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTotalUsers`()
BEGIN
    SET @totalUsers = NULL;	
    SELECT count(1) INTO @totalUsers FROM Users;
    SELECT @totalUsers AS result;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getTrackedProjects
DROP PROCEDURE IF EXISTS `getTrackedProjects`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTrackedProjects`(IN `uID` INT)
BEGIN
select p.* from Projects p  
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
	
	set @q= "select id,`display-name`,email,password,biography,(select code from Languages where id =u.`language_id`) as `language_id` ,(select code from Countries where id =u.`country_id`) as `country_id`, nonce,`created-time` from Users u where 1 ";-- set update
	if id is not null then 
#set paramaters to be updated
		set @q = CONCAT(@q," and u.id=",id) ;
	end if;
	if name is not null then 
		set @q = CONCAT(@q," and u.`display-name`='",name,"'") ;
	end if;
	if mail is not null then 
		set @q = CONCAT(@q," and u.email='",mail,"'") ;
	end if;
	if pass is not null then 
		set @q = CONCAT(@q," and u.password='",pass,"'") ;
	end if;
	if bio is not null then 
		set @q = CONCAT(@q," and u.biography='",bio,"'") ;
	end if;
	if nonce is not null then 
		set @q = CONCAT(@q," and u.nonce=",nonce) ;
	end if;
	if (created is not null  and created!='0000-00-00 00:00:00') then 
		set @q = CONCAT(@q," and u.`created-time`='",created,"'") ;
	end if;
	if lang_id is not null then 
		set @q = CONCAT(@q," and u.language_id=",lang_id) ;
	end if;
	if region_id is not null then 
		set @q = CONCAT(@q," and u.country_id=",region_id) ;
	end if;
	
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getUserArchivedTasks
DROP PROCEDURE IF EXISTS `getUserArchivedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserArchivedTasks`(IN `uID` INT, IN `lim` INT)
BEGIN

set @q=Concat("SELECT * FROM ArchivedTasks as a 
                WHERE user_id = ?
                ORDER BY `created-time` DESC
                limit ", lim);
        PREPARE stmt FROM @q;
        set@uID = uID;
	EXECUTE stmt using @uID;
	DEALLOCATE PREPARE stmt;

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


-- Dumping structure for procedure Solas-Match-Test.getUserClaimedTask
DROP PROCEDURE IF EXISTS `getUserClaimedTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserClaimedTask`(IN `taskID` INT)
BEGIN
	SELECT u.* FROM Users u
	WHERE u.id IN (SELECT tc.user_id FROM TaskClaims tc
	WHERE tc.task_id = taskID);
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


-- Dumping structure for procedure Solas-Match-Test.getUsersWithBadge
DROP PROCEDURE IF EXISTS `getUsersWithBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUsersWithBadge`(IN `bID` INT)
BEGIN
	SELECT *
	FROM Users JOIN UserBadges ON Users.id = UserBadges.user_id
	WHERE badge_id = bID;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getUserTags
DROP PROCEDURE IF EXISTS `getUserTags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTags`(IN `id` INT, IN `lim` INT)
BEGIN
	 if (lim= '') then set lim=null; end if;
	 if(lim is not null) then
    set @q = Concat("SELECT t.*	FROM UserTags	JOIN Tags t ON UserTags.tag_id = t.id	WHERE user_id = ? LIMIT ",lim);
    else
    set @q = "SELECT t.*	FROM UserTags	JOIN Tags t ON UserTags.tag_id = t.id	WHERE user_id =  ?";
    end if;
    PREPARE stmt FROM @q;
    set @id=id;
    EXECUTE stmt using @id;
    DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getUserTasks
DROP PROCEDURE IF EXISTS `getUserTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTasks`(IN `uID` INT, IN `lim` INT)
BEGIN

set @q=Concat(" SELECT t.id, t.project_id, t.title, t.`word-count`, 
                (select code from Languages l where l.id =t.`language_id-source`) as `language_id-source`,
                (select code from Languages l where l.id =t.`language_id-target`) as `language_id-target`,
                t.`created-time`, (select code from Countries c where c.id =t.`country_id-source`) as `country_id-source`, 
                (select code from Countries c where c.id =t.`country_id-target`) as `country_id-target`, comment,
                `task-type_id`, `task-status_id`, published, deadline
                FROM Tasks t JOIN TaskClaims tc ON tc.task_id = t.id
                WHERE user_id = ?
                ORDER BY `created-time` DESC
                limit ", lim);
        PREPARE stmt FROM @q;
        set@uID = uID;
	EXECUTE stmt using @uID;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getUserTaskScore
DROP PROCEDURE IF EXISTS `getUserTaskScore`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTaskScore`(IN `uID` INT, IN `tID` INT)
BEGIN

	if uID='' then set uID=null;end if;
    if tID='' then set tID=null;end if;
	set @q= "select * from UserTaskScores where 1 ";
	if uID is not null then 
		set @q = CONCAT(@q," and user_id=",uID) ;
	end if;
	if tID is not null then 
		set @q = CONCAT(@q," and task_id=",tID) ;
	end if;
    PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getUserTopTasks
DROP PROCEDURE IF EXISTS `getUserTopTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTopTasks`(IN `uID` INT, IN `lim` INT)
    READS SQL DATA
    COMMENT 'relpace with more effient code later'
BEGIN
    if lim='' then set lim=null; end if;
    if lim is not null then
        set @q = Concat("select id,project_id,title,`word-count`,(select code from Languages where id =t.`language_id-source`) as `language_id-source`,(select code from Languages where id =t.`language_id-target`) as `language_id-target`,`created-time`, (select code from Countries where id =t.`country_id-source`) as `country_id-source`, (select code from Countries where id =t.`country_id-target`) as `country_id-target`, comment,  `task-type_id`, `task-status_id`, published, deadline from Tasks t LEFT JOIN (SELECT * FROM UserTaskScores WHERE user_id = ? ) AS uts ON t.id = uts.task_id WHERE t.id NOT IN (SELECT task_id FROM TaskClaims)AND t.published = 1 AND t.`task-status_id` = 2 and not exists(select 1 from TaskTranslatorBlacklist where user_id = ? and task_id=t.id) ORDER BY uts.score DESC limit ",lim);
    else
        set @q = Concat("select id,project_id,title,`word-count`,(select code from Languages where id =t.`language_id-source`) as `language_id-source`,(select code from Languages where id =t.`language_id-target`) as `language_id-target`,`created-time`, (select code from Countries where id =t.`country_id-source`) as `country_id-source`, (select code from Countries where id =t.`country_id-target`) as `country_id-target`, comment,  `task-type_id`, `task-status_id`, published, deadline from Tasks t LEFT JOIN (SELECT * FROM UserTaskScores WHERE user_id = ?) AS uts ON t.id = uts.task_id WHERE t.id NOT IN (SELECT task_id FROM TaskClaims) AND t.published = 1 AND t.`task-status_id` = 2 and not exists(select 1 from TaskTranslatorBlacklist where user_id = ? and task_id=t.id) ORDER BY uts.score DESC");
    end if;
    PREPARE stmt FROM @q;
    set @uID=uID;
    EXECUTE stmt using @uID,@uID;
    DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getUserTrackedTasks
DROP PROCEDURE IF EXISTS `getUserTrackedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTrackedTasks`(IN `id` INT)
BEGIN
	SELECT t.*
	FROM UserTrackedTasks utt join Tasks t on utt.task_id=t.id
	WHERE user_id = id;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.hasUserClaimedTask
DROP PROCEDURE IF EXISTS `hasUserClaimedTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `hasUserClaimedTask`(IN `tID` INT, IN `uID` INT)
BEGIN
SELECT exists	(	select 1
                        FROM TaskClaims
                        WHERE task_id = tID
                        AND user_id = uID
                 ) as result;
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


-- Dumping structure for procedure Solas-Match-Test.organisationInsertAndUpdate
DROP PROCEDURE IF EXISTS `organisationInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `organisationInsertAndUpdate`(IN `id` INT(10), IN `url` TEXT, IN `companyName` VARCHAR(255), IN `bio` VARCHAR(4096))
BEGIN
	if id='' then set id=null;end if;
	if url='' then set url=null;end if;
	if companyName='' then set companyName=null;end if;
	if bio='' then set bio=null;end if;

	
	if id is null and not exists(select * from Organisations o where (o.`home-page`= url or o.`home-page`= concat("http://",url) ) and o.name=companyName)then
	-- set insert
    if bio is null then set bio='';end if;
	insert into Organisations (name,`home-page`, biography) values (companyName,url,bio);

	else 
		set @first = true;
		set @q= "update Organisations o set ";-- set update
		if bio is not null then 
#set paramaters to be updated
			set @q = CONCAT(@q," o.biography='",bio,"'") ;
			set @first = false;
		end if;
		if url is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," o.`home-page`='",url,"'") ;
		end if;
		if companyName is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," o.name='",companyName,"'") ;
		end if;
	
#		set where
		if id is not null then 
			set @q = CONCAT(@q," where  o.id= ",id);
		elseif url is not null and companyName is not null then 
			set @q = CONCAT(@q," where o.`home-page`='",url,"' and o.name='",companyName,"'");
		end if;
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
#
	end if;
	
	select o.id as 'result' from Organisations o where (o.`home-page`= url or o.`home-page`= concat("http://",url) ) and o.name=companyName;
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `projectInsertAndUpdate`(IN `projectId` INT, IN `titleText` VARCHAR(128), IN `descr` VARCHAR(4096), IN `impact` VARCHAR(4096), IN `deadlineTime` DATETIME, IN `orgId` INT, IN `ref` VARCHAR(128), IN `wordCount` INT, IN `createdTime` DATETIME, IN `sCC` VARCHAR(3), IN `sCode` VARCHAR(3))
BEGIN
    if projectId="" then set projectId=null; end if;
    if titleText="" then set titleText=null; end if;
    if descr="" then set descr=null; end if;
    if impact="" then set impact=null; end if;
    if deadlineTime="" then set deadlineTime=null; end if;
    if orgId="" then set orgId=null; end if;
    if ref="" then set ref=null; end if;
    if wordCount="" then set wordCount=null; end if;
    if createdTime="" then set createdTime=null; end if;
    if sCC="" then set sCC=null; end if;
    if sCode="" then set sCode=null; end if;

    if projectId is null then
        if deadlineTime is null or deadlineTime='0000-00-00 00:00:00' then set deadlineTime=DATE_ADD(now(),INTERVAL 14 DAY); end if;
        	set @scID=null;
			select c.id into @scID from Countries c where c.code=sCC;
			set @sID=null;
			select l.id into @sID from Languages l where l.code=sCode;
	
        INSERT INTO Projects (title, description, impact, deadline, organisation_id, reference, `word-count`, created,language_id,country_id) VALUES (titleText, descr, impact, deadlineTime, orgId, ref, wordCount, NOW(),@sID,@scID);
        #select (projectId, titleText, descr, deadlineTime, orgId, ref, wordCount, createdTime,sCC,sCode);
#        call getProject(projectId, title, descr, deadlineTime, orgId, ref, wordCount, createdTime,sCC,sCode);
         call getProject(LAST_INSERT_ID(), NULL, NULL, NULL, NULL, NULL,NULL, NULL, NULL,NULL,NULL);
    elseif EXISTS (select 1 FROM Projects p WHERE p.id=projectId) then
        set @first = true;
        set @q = "UPDATE Projects p set";

        if titleText is not null then
            if (@first = false) then
                set @q = CONCAT(@q, ",");
            else
                set @first = false;
            end if;
            set @q = CONCAT(@q, " p.title='", titleText, "'");
        end if;
        if descr is not null then
            if (@first = false) then
                set @q = CONCAT(@q, ",");
            else
                set @first = false;
            end if;
            set @q = CONCAT(@q, " p.description='", descr, "'");
        end if;
        if impact is not null then
            if (@first = false) then
                set @q = CONCAT(@q, ",");
            else
                set @first = false;
            end if;
            set @q = CONCAT(@q, " p.impact='", impact, "'");
        end if;
        if (deadlineTime is not null and deadlineTime!='0000-00-00 00:00:00') then
            if (@first = false) then
                set @q = CONCAT(@q, ",");
            else
                set @first = false;
            end if;
            set @q = CONCAT(@q, " p.deadline='", deadlineTime, "'");
        end if;
        if orgId is not null then
            if (@first = false) then
                set @q = CONCAT(@q, ",");
            else
                set @first = false;
            end if;
            set @q = CONCAT(@q, " p.organisation_id=", orgId);
        end if;
        if ref is not null then
            if (@first = false) then
                set @q = CONCAT(@q, ",");
            else
                set @first = false;
            end if;
            set @q = CONCAT(@q, " p.reference='", ref, "'");
        end if;
        if wordCount is not null then
            if (@first = false) then
                set @q = CONCAT(@q, ",");
            else
                set @first = false;
            end if;
            set @q = CONCAT(@q, " p.`word-count`=", wordCount);
        end if;
        if sCC is not null then
            if (@first = false) then
                set @q = CONCAT(@q, ",");
            else
                set @first = false;
            end if;
            set @scID=null;
				select c.id into @scID from Countries c where c.code=sCC;
            set @q = CONCAT(@q, " p.`country_id`=", @scID);
        end if;
        if sCode is not null then
            if (@first = false) then
                set @q = CONCAT(@q, ",");
            else
                set @first = false;
            end if;
            set @sID=null;
				select l.id into @sID from Languages l where l.code=sCode;
            set @q = CONCAT(@q, " p.`language_id`=", @sID);
        end if;
        
        if (createdTime is not null and createdTime!='0000-00-00 00:00:00') then
            if (@first = false) then
                set @q = CONCAT(@q, ",");
            else
                set @first = false;
            end if;
            set @q = CONCAT(@q, " p.created='", createdTime, "'");
        end if;
        set @q = CONCAT(@q, " WHERE p.id=", projectId);
        PREPARE stmt FROM @q;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
        call getProject(projectId, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
    end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.recordFileUpload
DROP PROCEDURE IF EXISTS `recordFileUpload`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `recordFileUpload`(IN `tID` INT, IN `name` TeXT, IN `content` VARCHAR(255), IN `uID` INT)
    MODIFIES SQL DATA
BEGIN
set @maxVer =-1;
if not exists (select 1 from TaskFileVersions tfv where tfv.task_id=tID) then
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
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.removeMembershipRequest
DROP PROCEDURE IF EXISTS `removeMembershipRequest`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeMembershipRequest`(IN `uID` INT, IN `orgID` INT)
BEGIN
	DELETE FROM OrgRequests
   WHERE user_id=uID
   AND org_id=orgID;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.removePasswordResetRequest
DROP PROCEDURE IF EXISTS `removePasswordResetRequest`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removePasswordResetRequest`(IN `userId` INT)
BEGIN
    DELETE FROM PasswordResetRequests 
        WHERE user_id = userId;
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
	if exists( select 1 from TaskPrerequisites tp where tp.task_id=taskID and tp.`task_id-prerequisite`= preReqId) then
      DELETE FROM TaskPrerequisites
        WHERE task_id = taskId
        AND `task_id-prerequisite` = preReqId;
   	select 1 as "result";
   else
   	select 0 as "result";
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
	if @owner is not null  then
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `requestMembership`(IN `uID` INT, IN `orgID` INT)
    MODIFIES SQL DATA
BEGIN
if not exists (select 1 from OrgRequests where user_id=uID and org_id=orgID) then
	INSERT INTO OrgRequests (user_id, org_id) VALUES (uID, orgID);
	select 1 as result;
else 
	select 0 as result;
end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.revokeMembership
DROP PROCEDURE IF EXISTS `revokeMembership`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `revokeMembership`(IN `uID` INT, IN `orgID` INT)
BEGIN
	if exists(select 1 from OrganisationMembers om where om.user_id=uID and om.organisation_id = orgID) then
		delete from OrganisationMembers where user_id=uID and organisation_id = orgID;
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
	SELECT *
	    FROM Organisations
	    WHERE name LIKE CONCAT('%', org_name, '%');
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


-- Dumping structure for procedure Solas-Match-Test.statsUpdateTotalProjects
DROP PROCEDURE IF EXISTS `statsUpdateTotalProjects`;
DELIMITER //
CREATE DEFINER=`tester`@`%` PROCEDURE `statsUpdateTotalProjects`()
BEGIN
	SET @Projects = 0;
	SET @ArchivedProjects = 0;	
	
	SELECT count(1) INTO @Projects FROM Projects;	
	SELECT count(1) INTO @ArchivedProjects FROM ArchivedProjects;
	
	SET @totalProjects = @Projects + @ArchivedProjects;
	REPLACE INTO Statistics (name, value)
	VALUES ('TotalProjects', @totalProjects);	
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


-- Dumping structure for procedure Solas-Match-Test.tagInsert
DROP PROCEDURE IF EXISTS `tagInsert`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `tagInsert`(IN `name` VARCHAR(50))
BEGIN
insert into Tags (label) values (name);
select id from Tags where label=name;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.taskDownloadCount
DROP PROCEDURE IF EXISTS `taskDownloadCount`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `taskDownloadCount`(IN `tID` INT)
BEGIN
	SELECT count(*) times_downloaded
	FROM task_file_version_download
	WHERE task_id = tID;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.taskInsertAndUpdate
DROP PROCEDURE IF EXISTS `taskInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `taskInsertAndUpdate`(IN `id` INT, IN `projectID` INT, IN `name` VARCHAR(50), IN `wordCount` INT, IN `sCode` VARCHAR(3), IN `tCode` VARCHAR(3), IN `created` DATETIME, IN `taskComment` VARCHAR(4096), IN `sCC` VARCHAR(3), IN `tCC` VarCHAR(3), IN `dLine` DATETIME, IN `taskType` INT, IN `tStatus` INT, IN `pub` VARCHAR(50))
BEGIN

	if id='' then set id=null;end if;

	if projectID='' then set projectID=null;end if;

	if name='' then set name=null;end if;

	if sCode='' then set sCode=null;end if;

	if tCode='' then set tCode=null;end if;

	if wordCount='' then set wordCount=null;end if;

	if created='' then set created=null;end if;

	if taskComment='' then set taskComment=null;end if;

	if sCode='' then set sCode=null;end if;

	if tCode='' then set tCode=null;end if;

	if dLine='' then set dLine=null;end if;

	if taskType='' then set taskType=null;end if;

	if tStatus='' then set tStatus=null;end if;

	if pub='' then set pub=null;end if;

	

	if id is null then

		if taskComment is null then set taskComment="";end if;

		if created is null or created ='0000-00-00 00:00:00' then set created=now();end if;

		if dLine is null or dLine ='0000-00-00 00:00:00' then set dLine=DATE_ADD(now(),INTERVAL 14 DAY);end if;

		set @scid=null;

			select c.id into @scid from Countries c where c.code=sCC;

		set @tcid=null;

			select c.id into @tcid from Countries c where c.code=tCC;

		set @sID=null;

			select l.id into @sID from Languages l where l.code=sCode;

		set @tID=null;

			select l.id into @tID from Languages l where l.code=tCode;

			if pub is null then set pub=1;end if;	

			

		insert into Tasks (project_id,title,`word-count`,`language_id-source`,`language_id-target`,`created-time`,comment,`country_id-source`,`country_id-target`,`deadline`,`task-type_id`,`task-status_id`,`published`)

		 values (projectID,name,wordCount,@sID,@tID,created,taskComment,@scid,@tcid,dLine,taskType,tStatus,pub);

	elseif EXISTS (select 1 from Tasks t where t.id=id) then

		

		set @first = true;

		set @q= "update Tasks t set";-- set update

		if projectID is not null then 

			if (@first = false) then 

				set @q = CONCAT(@q,",");

			else

				set @first = false;

			end if;

			set @q = CONCAT(@q," t.project_id=",projectID) ;

		end if;

		if name is not null then 

			if (@first = false) then 

				set @q = CONCAT(@q,",");

			else

				set @first = false;

			end if;

			set @q = CONCAT(@q," t.title='",name,"'") ;

		end if;

		if sCode is not null then 

			if (@first = false) then 

				set @q = CONCAT(@q,",");

			else

				set @first = false;

			end if;

			set @sID=null;

			select l.id into @sID from Languages l where l.code=sCode;

			set @q = CONCAT(@q," t.`language_id-source`=",@sID) ;

		end if;

		if tCode is not null then 

			if (@first = false) then 

				set @q = CONCAT(@q,",");

			else

				set @first = false;

			end if;

			set @tID=null;

			select l.id into @tID from Languages l where l.code=tCode;

			set @q = CONCAT(@q," t.`language_id-target`=",@tID) ;

		end if;

		

		if sCC is not null then 

			if (@first = false) then 

				set @q = CONCAT(@q,",");

			else

				set @first = false;

			end if;

			set @scid=null;

			select c.id into @scid from Countries c where c.code=sCC;

			set @q = CONCAT(@q," t.`country_id-source`=",@scid) ;

		end if;

		if tCC is not null then 

			if (@first = false) then 

				set @q = CONCAT(@q,",");

			else

				set @first = false;

			end if;

			set @tcid=null;

			select c.id into @tcid from Countries c where c.code=tCC;

			set @q = CONCAT(@q," t.`country_id-target`=",@tcid) ;

		end if;

		

		if wordCount is not null then 

			if (@first = false) then 

				set @q = CONCAT(@q,",");

			else

				set @first = false;

			end if;

			set @q = CONCAT(@q," t.`word-count`=",wordCount) ;

		end if;

		if taskComment is not null then 

			if (@first = false) then 

				set @q = CONCAT(@q,",");

			else

				set @first = false;

			end if;

			set @q = CONCAT(@q," t.comment='",taskComment,"'");

		end if;

		if (created is not null  and created!='0000-00-00 00:00:00') then 

			if (@first = false) then 

				set @q = CONCAT(@q,",");

			else

				set @first = false;

			end if;

			set @q = CONCAT(@q," t.`created-time`='",created,"'") ;

		end if;

		if (dLine is not null  and dLine!='0000-00-00 00:00:00') then 

			if (@first = false) then 

				set @q = CONCAT(@q,",");

			else

				set @first = false;

			end if;

			set @q = CONCAT(@q," t.`deadline`='",dLine,"'") ;

		end if;

		if taskType is not null then 

			if (@first = false) then 

				set @q = CONCAT(@q,",");

			else

				set @first = false;

			end if;

			set @q = CONCAT(@q," t.`task-type_id`=",taskType) ;

		end if;

		if tStatus is not null then 

			if (@first = false) then 

				set @q = CONCAT(@q,",");

			else

				set @first = false;

			end if;

			set @q = CONCAT(@q," t.`task-status_id`=",tStatus) ;

		end if;

		if pub is not null then 

			if (@first = false) then 

				set @q = CONCAT(@q,",");

			else

				set @first = false;

			end if;

			set @q = CONCAT(@q," t.`published`=",pub) ;

		end if;

		set @q = CONCAT(@q," where  t.id= ",id);

		PREPARE stmt FROM @q;

		EXECUTE stmt;

		DEALLOCATE PREPARE stmt;

	end if;

	call getTask(id,projectID,name,wordCount,sCode,tCode,created,sCC,tCC,taskComment,taskType,tStatus,pub,dLine);

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


-- Dumping structure for procedure Solas-Match-Test.unClaimTask
DROP PROCEDURE IF EXISTS `unClaimTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `unClaimTask`(IN `tID` INT, IN `uID` INT)
BEGIN
	if EXISTS(select 1 from TaskClaims tc where tc.task_id=tID and tc.user_id=uID) then
      delete from TaskClaims where task_id=tID and user_id=uID;
      insert into TaskTranslatorBlacklist (task_id,user_id) values (tID,uID);
      update Tasks set `task-status_id`=2 where id = tID;
		select 1 as result;
	else
		select 0 as result;
	end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.unlinkStoredTags
DROP PROCEDURE IF EXISTS `unlinkStoredTags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `unlinkStoredTags`(IN `id` INT)
    MODIFIES SQL DATA
BEGIN
DELETE FROM TaskTags WHERE task_id = id;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.userFindByUserData
DROP PROCEDURE IF EXISTS `userFindByUserData`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userFindByUserData`(IN `id` INT, IN `pass` VARBINARY(128), IN `email` VARCHAR(256), IN `role` TINYINT)
BEGIN
	if(id is not null and pass is not null) then
		select u.id,u.`display-name`,u.email,u.password,u.biography,(select lg.code from Languages lg where lg.id =u.`language_id`) as `language_id` ,(select c.code from Countries c where c.id =u.`country_id`) as `country_id`, nonce,`created-time` from Users u where u.id = id and password= pass;
   elseif(id is not null and role=1) then
		select u.id,u.`display-name`,u.email,u.password,u.biography,(select lg.code from Languages lg where lg.id =u.`language_id`) as `language_id` ,(select c.code from Countries c where c.id =u.`country_id`) as `country_id`, nonce,`created-time` from Users u where u.id = id and EXISTS (select * from OrganisationMembers om where om.user_id = u.id);
	elseif(id is not null) then
		select u.id,u.`display-name`,u.email,u.password,u.biography,(select lg.code from Languages lg where lg.id =u.`language_id`) as `language_id` ,(select c.code from Countries c where c.id =u.`country_id`) as `country_id`, nonce,`created-time` from Users u where u.id = id;
   elseif (email is not null) then
   	select u.id,u.`display-name`,u.email,u.password,u.biography,(select lg.code from Languages lg where lg.id =u.`language_id`) as `language_id` ,(select c.code from Countries c where c.id =u.`country_id`) as `country_id`, nonce,`created-time` from Users u where u.email = email;
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


-- Dumping structure for procedure Solas-Match-Test.userInsertAndUpdate
DROP PROCEDURE IF EXISTS `userInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userInsertAndUpdate`(IN `email` VARCHAR(256), IN `nonce` int(11), IN `pass` char(128), IN `bio` TEXT, IN `name` VARCHAR(128), IN `lang` VARCHAR(3), IN `region` VARCHAR(3), IN `id` INT)
BEGIN
	if pass='' then set pass=null;end if;
	if bio='' then set bio=null;end if;
	if id='' then set id=null;end if;
	if nonce='' then set nonce=null;end if;
	if name='' then set name=null;end if;
	if email='' then set email=null;end if;
	if lang='' then set lang=null;end if;
    if region='' then set region=null;end if;
	
	if id is null and not exists(select * from Users u where u.email= email)then
	-- set insert
	set @countryID=null;
	select c.id into @countryID from Countries c where c.code=region;
	set @langID=null;
	select l.id into @langID from Languages l where l.code=lang;
	
	insert into Users (email, nonce, password, `created-time`, `display-name`, biography, language_id, country_id) 
              values (email, nonce, pass, NOW(), name, bio, @langID, @countryID);
            call getUser(LAST_INSERT_ID(),null,null,null,null,null,null,null,null);
	else 
		set @first = true;
		set @q= "update Users u set ";-- set update
		if bio is not null then 
#set paramaters to be updated
			set @q = CONCAT(@q," u.biography='",bio,"'") ;
			set @first = false;
		end if;
		if lang is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;

			set @langID=null;
			select l.id into @langID from Languages l where l.code=lang;
			set @q = CONCAT(@q," u.language_id='",@langID,"'") ;
		end if;
		if region is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @countryID=null;
			select c.id into @countryID from Countries c where c.code=region;
			set @q = CONCAT(@q," u.country_id='",@countryID,"'") ;
		end if;
		if name is not null then 
				if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," u.`display-name`='",name,"'");
		
		end if;
		
		if email is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," u.email='",email,"'");
		
		end if;
		if nonce is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," u.nonce=",nonce) ;
		
		end if;
		
		if pass is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," u.password='",pass,"'");
		
		end if;
#		set where
	
		if id is not null then 
			set @q = CONCAT(@q," where  u.id= ",id);
#    	allows email to be changed but not user id
		
		elseif email is not null then 
			set @q = CONCAT(@q," where  u.email= ,",email,"'");-- allows anything but email and user_id to change
		else
			set @q = CONCAT(@q," where  u.email= null AND u.id=null");-- will always fail to update anyting
		end if;
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
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


-- Dumping structure for procedure Solas-Match-Test.userTrackProject
DROP PROCEDURE IF EXISTS `userTrackProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userTrackProject`(IN `pID` INT, IN `uID` INT)
BEGIN
	if not exists (select 1 from UserTrackedProjects utp where utp.user_id=uID and utp.Project_id=pID) then
		insert into UserTrackedProjects (project_id,user_id) values (pID,uID);
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
	if exists (select 1 from UserTrackedProjects utp where utp.user_id=uID and utp.Project_id=pID) then
		delete from UserTrackedProjects  where user_id=uID and Project_id=pID;
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




/*---------------------------------------end of procs----------------------------------------------*/


/*---------------------------------------start of triggers-----------------------------------------*/

-- Dumping structure for trigger Solas-Match-Test.defaultUserName
DROP TRIGGER IF EXISTS `defaultUserName`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `defaultUserName` BEFORE INSERT ON `Users` FOR EACH ROW BEGIN
if new.`display-name` is null then set new.`display-name` = substring_index(new.email,'@',1); end if;
END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;


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

/*---------------------------------------end of triggers-------------------------------------------*/
SET FOREIGN_KEY_CHECKS=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

