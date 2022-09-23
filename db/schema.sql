-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.28-0ubuntu0.12.04.3 - (Ubuntu)
-- Server OS:                    debian-linux-gnu
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2013-01-09 15:51:55
-- --------------------------------------------------------ul

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
SET FOREIGN_KEY_CHECKS=0;


/*--------------------------------------------------start of tables--------------------------------*/


CREATE TABLE IF NOT EXISTS `Admins` (
  `user_id` INT(10) UNSIGNED NOT NULL,
  `organisation_id` INT(10) UNSIGNED NULL,
  UNIQUE INDEX `user_id` (`user_id`, `organisation_id`),
  INDEX `FK_Admins_Organisations` (`organisation_id`),
  CONSTRAINT `FK_Admins_Organisations` FOREIGN KEY (`organisation_id`) REFERENCES `Organisations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_Admins_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `ArchivedProjects` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(4096) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `impact` varchar(4096) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deadline` datetime NOT NULL,
  `organisation_id` int(10) unsigned NOT NULL,
  `reference` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `word-count` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `country_id` int(10) unsigned NOT NULL,
  `image_uploaded` BIT(1) DEFAULT 0 NOT NULL,
  `image_approved` BIT(1) DEFAULT 0 NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `organisation_id` (`organisation_id`,`language_id`,`country_id`),
  KEY `key_organisation_id` (`organisation_id`),
  KEY `FK_ArchivedProjects_Languages` (`language_id`),
  KEY `FK_ArchivedProjects_Countries` (`country_id`),
  CONSTRAINT `FK_archivedproject_organisation` FOREIGN KEY (`organisation_id`) REFERENCES `Organisations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedProjects_Languages` FOREIGN KEY (`language_id`) REFERENCES `Languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedProjects_Countries` FOREIGN KEY (`country_id`) REFERENCES `Countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `ArchivedProjectsMetadata` (
  `archivedProject_id` int(10) unsigned NOT NULL,
  `user_id-archived` int(10) unsigned NOT NULL,
  `user_id-projectCreator` int(10) unsigned NOT NULL,
  `filename` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file-token` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime-type` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived-date` datetime NOT NULL,
  `tags` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  UNIQUE KEY `archivedProject_id` (`archivedProject_id`),
  KEY `FK_ArchivedProjectsMetadata_Users` (`user_id-archived`),
  KEY `FK_ArchivedProjectsMetadata_Users_2` (`user_id-projectCreator`),
  CONSTRAINT `FK_ArchivedProjectsMetadata_ArchivedProjects` FOREIGN KEY (`archivedProject_id`) REFERENCES `ArchivedProjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedProjectsMetadata_Users` FOREIGN KEY (`user_id-archived`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedProjectsMetadata_Users_2` FOREIGN KEY (`user_id-projectCreator`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `ArchivedTasks` (
  `id` bigint(20) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` varchar(4096) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `ArchivedTasksMetadata` (
  `archivedTask_id` bigint(20) unsigned NOT NULL,
  `version` int(10) unsigned DEFAULT NULL,
  `filename` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content-type` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upload-time` datetime DEFAULT NULL,
  `user_id-claimed` int(10) unsigned DEFAULT NULL,
  `user_id-archived` int(10) unsigned NOT NULL,
  `prerequisites` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id-taskCreator` int(10) unsigned DEFAULT NULL,
  `archived-date` datetime NOT NULL,
  UNIQUE KEY `archivedTask_id` (`archivedTask_id`),
  KEY `FK_ArchivedTasksMetadata_Users` (`user_id-claimed`),
  KEY `FK_ArchivedTasksMetadata_Users_2` (`user_id-archived`),
  KEY `FK_ArchivedTasksMetadata_Users_3` (`user_id-taskCreator`),
  CONSTRAINT `FK_ArchivedTasksMetadata_ArchivedTasks` FOREIGN KEY (`archivedTask_id`) REFERENCES `ArchivedTasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasksMetadata_Users` FOREIGN KEY (`user_id-claimed`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasksMetadata_Users_2` FOREIGN KEY (`user_id-archived`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArchivedTasksMetadata_Users_3` FOREIGN KEY (`user_id-taskCreator`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `Badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) unsigned DEFAULT NULL,
  `title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `badge` (`owner_id`,`title`),
  CONSTRAINT `FK_badges_organisation` FOREIGN KEY (`owner_id`) REFERENCES `Organisations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `BannedOrganisations` (
  `org_id` int(10) unsigned NOT NULL,
  `user_id-admin` int(10) unsigned NOT NULL,
  `bannedtype_id` int(10) unsigned NOT NULL,
  `comment` varchar(4096) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banned-date` datetime NOT NULL,
  UNIQUE KEY `org_id` (`org_id`),
  KEY `FK_BannedOrganisations_Users` (`user_id-admin`),
  KEY `FK_BannedOrganisations_BannedTypes` (`bannedtype_id`),
  CONSTRAINT `FK_BannedOrganisations_BannedTypes` FOREIGN KEY (`bannedtype_id`) REFERENCES `BannedTypes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_BannedOrganisations_Organisations` FOREIGN KEY (`org_id`) REFERENCES `Organisations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_BannedOrganisations_Users` FOREIGN KEY (`user_id-admin`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `BannedTypes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

REPLACE INTO `BannedTypes` (`id`, `type`) VALUES
  (1, 'Day'),
    (2, 'Week'),
  (3, 'Month'),
  (4, 'Permanent'),
    (5, 'Hour');


CREATE TABLE IF NOT EXISTS `BannedUsers` (
  `user_id` int(10) unsigned NOT NULL,
  `user_id-admin` int(10) unsigned NOT NULL,
  `bannedtype_id` int(10) unsigned NOT NULL,
  `comment` varchar(4096) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banned-date` datetime NOT NULL,
  UNIQUE KEY `user_id` (`user_id`),
  KEY `FK_BannedUsers_Users_2` (`user_id-admin`),
  KEY `FK_BannedUsers_BannedTypes` (`bannedtype_id`),
  CONSTRAINT `FK_BannedUsers_BannedTypes` FOREIGN KEY (`bannedtype_id`) REFERENCES `BannedTypes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_BannedUsers_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_BannedUsers_Users_2` FOREIGN KEY (`user_id-admin`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/*!40000 ALTER TABLE `Badges` DISABLE KEYS */;
REPLACE INTO `Badges` (`id`, `owner_id`, `title`, `description`) VALUES
    ( 3, NULL, 'Profile-Filler',      'Filled in all info for user  public profile.'),
    ( 4, NULL, 'Registered',          'Successfully set up an account'),
    ( 5, NULL, 'Native-Language',     'Filled in your native language on your user profile successfully.'),
    ( 6, NULL, 'Translator',          'This volunteer is available for translation tasks.'),
    ( 7, NULL, 'Reviewer',            'This volunteer is available for revising tasks.'),
    ( 8, NULL, 'Interpreter',         'This volunteer is available for interpreting tasks.'),
    ( 9, NULL, 'Polyglot',            'One or more secondary languages selected on your profile.'),
    (10, NULL, 'Subtitling',          'This volunteer is available for subtitling tasks.'),
    (11, NULL, 'Monolingual editing', 'This volunteer is available for monolingual editing tasks.'),
    (12, NULL, 'DTP',                 'This volunteer is available for DTP tasks.'),
    (13, NULL, 'Voiceover',           'This volunteer is available for voiceover tasks.');
ALTER TABLE `Badges` AUTO_INCREMENT=100;


CREATE TABLE IF NOT EXISTS `Countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '"IE", for example',
  `en-name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `Languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '"en", for example',
  `en-name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `NotificationIntervals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

REPLACE INTO `NotificationIntervals` (`id`, `name`) VALUES
  (1, "Daily"),
  (2, "Weekly"),
  (3, "Monthly");


CREATE TABLE IF NOT EXISTS `OrganisationMembers` (
  `user_id` int(10) unsigned NOT NULL,
  `organisation_id` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `user_id` (`user_id`,`organisation_id`),
  KEY `FK_organisation_member_organisation` (`organisation_id`),
  CONSTRAINT `FK_organisation_member_organisation` FOREIGN KEY (`organisation_id`) REFERENCES `Organisations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_organisation_member_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `Organisations` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL COLLATE 'utf8mb4_unicode_ci',
  `biography` VARCHAR(4096) NULL COLLATE 'utf8mb4_unicode_ci',
  `home-page` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
  `e-mail` VARCHAR(128) NULL,
  `address` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
  `city` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
  `country` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
  `regional-focus` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `OrganisationExtendedProfiles` (
  `id` INT(10) UNSIGNED NOT NULL,
  `facebook`            VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `linkedin`            VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `primaryContactName`  VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `primaryContactTitle` VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `primaryContactEmail` VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `primaryContactPhone` VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `otherContacts`       VARCHAR(1000) NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `structure`           VARCHAR(4096) NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `affiliations`        VARCHAR(4096) NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `urlVideo1`           VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `urlVideo2`           VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `urlVideo3`           VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `subjectMatters`      VARCHAR(1000) NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `activitys`           VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `employees`           VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `fundings`            VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `finds`               VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `translations`        VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `requests`            VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `contents`            VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `pages`               VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `sources`             VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `targets`             VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `oftens`              VARCHAR(255)  NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `OrgTranslatorBlacklist` (
  `org_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `org_id` (`org_id`,`user_id`),
  KEY `FK_OrgTranslatorBlacklist_Users` (`user_id`),
  CONSTRAINT `FK_OrgTranslatorBlacklist_Organisations` FOREIGN KEY (`org_id`) REFERENCES `Organisations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_OrgTranslatorBlacklist_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `PasswordResetRequests` (
  `uid` char(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `request-time` datetime DEFAULT NULL,
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `FK_password_reset_user1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `ProjectFiles` (
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `filename` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file-token` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime-type` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  UNIQUE KEY `project_id` (`project_id`),
  KEY `FK_ProjectFiles_Users` (`user_id`),
  CONSTRAINT `FK_ProjectFiles_Projects` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ProjectFiles_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `Projects` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(128) NOT NULL COLLATE 'utf8mb4_unicode_ci',
  `description` VARCHAR(4096) NOT NULL COLLATE 'utf8mb4_unicode_ci',
  `impact` VARCHAR(4096) NOT NULL COLLATE 'utf8mb4_unicode_ci',
  `deadline` DATETIME NOT NULL,
  `organisation_id` INT(10) UNSIGNED NOT NULL,
  `reference` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
  `word-count` INT(10) UNSIGNED NOT NULL,
  `created` DATETIME NOT NULL,
  `language_id` INT(10) UNSIGNED NOT NULL,
  `country_id` INT(10) UNSIGNED NOT NULL,
    `image_uploaded` BIT(1) DEFAULT 0 NOT NULL,
    `image_approved` BIT(1) DEFAULT 0 NOT NULL,
  PRIMARY KEY (`id`),
    KEY `key_organisation_id` (`organisation_id`),
  INDEX `FK_Projects_Languages` (`language_id`),
  INDEX `FK_Projects_Countries` (`country_id`),
  CONSTRAINT `FK_Projects_Countries` FOREIGN KEY (`country_id`) REFERENCES `Countries` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_Projects_Languages` FOREIGN KEY (`language_id`) REFERENCES `Languages` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_project_organisation` FOREIGN KEY (`organisation_id`) REFERENCES `Organisations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `ProjectTags` (
  `project_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `project_id` (`project_id`,`tag_id`),
  KEY `FK_ProjectTags_Tags` (`tag_id`),
  CONSTRAINT `FK_ProjectTags_Projects` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ProjectTags_Tags` FOREIGN KEY (`tag_id`) REFERENCES `Tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `RegisteredUsers` (
  `user_id` int(10) unsigned NOT NULL,
  `unique_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `FK_RegisteredUsers_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `Statistics` (
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` double NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `Tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `label` (`label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `TaskClaims` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `claimed-time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Tasks` (`task_id`,`user_id`),
  UNIQUE KEY `FK_task_claim_task` (`task_id`),
  KEY `FK_task_claim_user` (`user_id`),
  CONSTRAINT `FK_task_claim_task` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_task_claim_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `queue_claim_tasks` (
  task_id BIGINT(20) UNSIGNED NOT NULL,
  user_id INT(10)    UNSIGNED NOT NULL,
  UNIQUE KEY FK_queue_claim_tasks_task_id (task_id),
  CONSTRAINT FK_queue_claim_tasks_task_id FOREIGN KEY (task_id) REFERENCES Tasks (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FK_queue_claim_tasks_user_id FOREIGN KEY (user_id) REFERENCES Users (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `AsanaProjects` (
  project_id INT(10) UNSIGNED NOT NULL,
  run_time   DATETIME NOT NULL,
  PRIMARY KEY (run_time),
  KEY FK_AsanaProjects_project_id (project_id),
  CONSTRAINT FK_AsanaProjects_project_id FOREIGN KEY (project_id) REFERENCES Projects (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `AsanaTasks` (
  project_id INT(10) UNSIGNED NOT NULL,
  language_code_source VARCHAR(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  language_code_target VARCHAR(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  asana_task_id        VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  KEY `project_id` (`project_id`),
  CONSTRAINT `FK_AsanaTasks_Projects` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `TaskFileVersions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) unsigned NOT NULL,
  `version_id` int(11) NOT NULL COMMENT 'Gets incremented within the code',
  `filename` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `content-type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL COMMENT 'Null while we don''t have logging in',
  `upload-time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `taskFile` (`task_id`,`version_id`,`user_id`),
  KEY `FK_task_file_version_user` (`user_id`),
  CONSTRAINT `FK_TaskFileVersions_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TaskFileVersions_Tasks` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `TaskPrerequisites` (
  `task_id` bigint(20) unsigned NOT NULL,
  `task_id-prerequisite` bigint(20) unsigned NOT NULL,
  UNIQUE KEY `task_id` (`task_id`,`task_id-prerequisite`),
  KEY `FK_TaskPrerequisites_Tasks_1` (`task_id`),
  KEY `FK_TaskPrerequisites_Tasks_2` (`task_id-prerequisite`),
  CONSTRAINT `FK_TaskPrerequisites_Tasks` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TaskPrerequisites_Tasks_2` FOREIGN KEY (`task_id-prerequisite`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `TaskReviews` (
  `project_id` int(10) unsigned NOT NULL,
  `task_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `corrections` int(11) unsigned NOT NULL,
  `grammar` int(11) unsigned NOT NULL,
  `spelling` int(11) unsigned NOT NULL,
  `consistency` int(11) unsigned NOT NULL,
  revise_task_id BIGINT(20) UNSIGNED DEFAULT NULL,
  `comment` VARCHAR(8192) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  UNIQUE KEY `user_task_project` (`task_id`,`user_id`,`project_id`),
  KEY key_revise_task_id (revise_task_id),
  CONSTRAINT `FK_TaskReviews_Tasks` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TaskReviews_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TaskReviews_Projects` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `Tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `word-count` int(11) DEFAULT NULL,
  `language_id-source` int(10) unsigned NOT NULL,
  `language_id-target` int(10) unsigned NOT NULL,
  `country_id-source` int(10) unsigned NOT NULL,
  `country_id-target` int(10) unsigned NOT NULL,
  `created-time` datetime NOT NULL,
  `deadline` datetime NOT NULL,
  `comment` varchar(4096) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `task-type_id` int(11) unsigned NOT NULL,
  `task-status_id` int(11) unsigned NOT NULL,
  `published` BIT(1) DEFAULT 0 NOT NULL,
  PRIMARY KEY (`id`),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `TaskNotificationSent` (
  `task_id` BIGINT(20) UNSIGNED NOT NULL,
  `notification` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`task_id`),
  CONSTRAINT `FK_TaskNotificationSent_Tasks` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `TaskStatus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

REPLACE INTO `TaskStatus` (`id`, `name`) VALUES
  (1, "Waiting PreReqs"),
  (2, "Pending Claim"),
  (3, "In Progress"),
  (4, "Complete");


CREATE TABLE IF NOT EXISTS `TaskTranslatorBlacklist` (
  `task_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `revoked_by_admin` BIT(1) DEFAULT 0 NOT NULL,
  UNIQUE KEY `task_id` (`task_id`,`user_id`),
  KEY `FK_TaskTranslatorBlacklist_Users` (`user_id`),
  CONSTRAINT `FK_TaskTranslatorBlacklist_Tasks` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TaskTranslatorBlacklist_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `TaskTypes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

REPLACE INTO `TaskTypes` (`id`, `name`) VALUES
  (1, "Segmentation"),
  (2, "Translation"),
  (3, "Proofreading"),
  (4, "Desegmentation");


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
  CONSTRAINT `FK_task_unclaim_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `UserBadges` (
  `user_id` int(10) unsigned NOT NULL,
  `badge_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`badge_id`),
  UNIQUE KEY `userBadge` (`user_id`,`badge_id`),
  KEY `FK_user_badges_badges` (`badge_id`),
  CONSTRAINT `FK_user_badges_badges` FOREIGN KEY (`badge_id`) REFERENCES `Badges` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_badges_users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `UserLogins` (
  `user_id` int(10) unsigned DEFAULT NULL,
  `email` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `success` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login-time` datetime NOT NULL,
  KEY `FK_UserLogins_Users` (`user_id`),
  CONSTRAINT `FK_UserLogins_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `UserPersonalInformation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `first-name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last-name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile-number` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business-number` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language-preference` INT(10) UNSIGNED DEFAULT NULL,
  `job-title` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receive_credit` BIT(1) DEFAULT 0 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `FK_UserPersonalInformation_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_UserPersonalInformation_Languages` FOREIGN KEY (`language-preference`) REFERENCES `Languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `Users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `display-name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` char(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `biography` text COLLATE utf8mb4_unicode_ci,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `UserTags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userTag` (`user_id`,`tag_id`),
  KEY `FK_user_tag_user1` (`tag_id`),
  CONSTRAINT `FK_user_tag_tag1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_tag_user1` FOREIGN KEY (`tag_id`) REFERENCES `Tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `UserTaskScores` (
  `user_id` int(10) unsigned NOT NULL,
  `task_id` bigint(20) unsigned NOT NULL,
  `score` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`user_id`,`task_id`),
  KEY `FK_user_task_score_user1` (`user_id`),
  KEY `FK_user_task_score_task1` (`task_id`),
  CONSTRAINT `FK_user_task_score_task1` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_task_score_user1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `UserTaskScoresUpdatedTime` (
  `id` int(10) unsigned NOT NULL,
  `unix_epoch` BIGINT(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `UserTaskStreamNotifications` (
  `user_id` int(10) unsigned NOT NULL,
  `interval` int(10) unsigned NOT NULL,
  `strict` int(1) NOT NULL DEFAULT '0',
  `last-sent` DATETIME DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `FK_user_task_stream_notification_user1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_task_stream_notification_interval1` FOREIGN KEY (`interval`) REFERENCES `NotificationIntervals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `SpecialTranslators` (
    user_id INT (10) UNSIGNED NOT NULL,
    type    INT (10) UNSIGNED DEFAULT 0,
    PRIMARY KEY FK_special_user_id (user_id),
    CONSTRAINT FK_special_user_id FOREIGN KEY (user_id) REFERENCES Users (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `UserTrackedProjects` (
  `user_id` INT(10) UNSIGNED NOT NULL,
  `Project_id` INT(10) UNSIGNED NOT NULL,
  UNIQUE INDEX `user_id` (`user_id`, `Project_id`),
  INDEX `FK_UserTrackedProjects_Projects` (`Project_id`),
  CONSTRAINT `FK_UserTrackedProjects_Projects` FOREIGN KEY (`Project_id`) REFERENCES `Projects` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_UserTrackedProjects_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `UserTrackedTasks` (
  `user_id` INT(10) UNSIGNED NOT NULL,
  `task_id` BIGINT(20) UNSIGNED NOT NULL,
  UNIQUE INDEX `user_id` (`user_id`, `task_id`),
  INDEX `FK_UserTrackedTasks_Tasks` (`task_id`),
  CONSTRAINT `FK_UserTrackedTasks_Tasks` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_UserTrackedTasks_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `UserTrackedOrganisations` (
    `user_id` INT(10) UNSIGNED NOT NULL,
    `organisation_id` INT(10) UNSIGNED NOT NULL,
    `created` datetime NOT NULL,
    UNIQUE INDEX `user_id` (`user_id`, `organisation_id`),
    INDEX `FK_UserTrackedOrganisations_Organisations` (`organisation_id`),
    CONSTRAINT `FK_UserTrackedOrganisations_Organisations` FOREIGN KEY (`organisation_id`) REFERENCES `Organisations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `FK_UserTrackedOrganisations_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `TaskViews` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` BIGINT(20) UNSIGNED NOT NULL,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `viewed-time` DATETIME NOT NULL,
  `task_is_archived` BIT(1) DEFAULT 0 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `TaskViewTimeStamps` (`task_id`, `user_id`, `viewed-time`),
  KEY `FK_task_viewed_user` (`user_id`),
  CONSTRAINT `FK_task_viewed_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS Subscriptions (
  organisation_id INT(10) UNSIGNED NOT NULL,
  level INT(10) UNSIGNED NOT NULL,
  spare INT(10) UNSIGNED DEFAULT 0 NOT NULL,
  start_date DATETIME NOT NULL,
  comment VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (organisation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS SubscriptionsRecorded (
  time_stamp DATETIME NOT NULL,
  organisation_id INT(10) UNSIGNED NOT NULL,
  level INT(10) UNSIGNED NOT NULL,
  spare INT(10) UNSIGNED DEFAULT 0 NOT NULL,
  start_date DATETIME NOT NULL,
  comment VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  KEY (organisation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `RestrictedTasks` (
  `restricted_task_id` BIGINT(20) UNSIGNED NOT NULL,
  UNIQUE KEY `FK_restricted_task_id` (`restricted_task_id`),
  CONSTRAINT `FK_restricted_task_id` FOREIGN KEY (`restricted_task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `WordCountRequestForProjects` (
    project_id INT(10) UNSIGNED NOT NULL,
    matecat_id_project INT(10) UNSIGNED NOT NULL,
    matecat_id_project_pass VARCHAR(50) NOT NULL,
    source_language VARCHAR(10) NOT NULL,
    target_languages VARCHAR(100) NOT NULL,
    user_word_count INT(10) UNSIGNED NOT NULL,
    matecat_word_count INT(10) UNSIGNED NOT NULL,
    state INT(10) UNSIGNED NOT NULL,
    KEY state (state),
    KEY FK_WordCountRequestForProjects_project_id (project_id),
    CONSTRAINT FK_WordCountRequestForProjects_project_id FOREIGN KEY (project_id) REFERENCES Projects (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `WordCountRequestForProjectsErrors` (
    project_id INT(10) UNSIGNED NOT NULL,
    status  VARCHAR(30)  NOT NULL,
    message VARCHAR(255) NOT NULL,
    KEY FK_WordCountRequestForProjectsErrors_project_id (project_id),
    CONSTRAINT FK_WordCountRequestForProjectsErrors_project_id FOREIGN KEY (project_id) REFERENCES Projects (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `MatecatLanguagePairs` (
    task_id BIGINT(20) UNSIGNED NOT NULL,
    project_id INT(10) UNSIGNED NOT NULL,
    type_id  INT(10) UNSIGNED NOT NULL,
    matecat_langpair  VARCHAR(50) NOT NULL,
    matecat_id_job INT(10) UNSIGNED NOT NULL,
    matecat_id_job_password VARCHAR(50) NOT NULL,
    matecat_id_file INT(10) UNSIGNED NOT NULL,
    UNIQUE KEY FK_matecat_language_pair_task_id (task_id),
    CONSTRAINT FK_matecat_language_pair_task_id FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY FK_matecat_language_pair_project_id (project_id),
    CONSTRAINT FK_matecat_language_pair_project_id FOREIGN KEY (project_id) REFERENCES Projects (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `TaskChunks` (
    task_id BIGINT(20) UNSIGNED NOT NULL,
    project_id INT(10) UNSIGNED NOT NULL,
    type_id    INT(10) UNSIGNED NOT NULL,
    matecat_langpair   VARCHAR(50) NOT NULL,
    matecat_id_job INT(10) UNSIGNED NOT NULL,
    chunk_number   INT(10) UNSIGNED NOT NULL,
    matecat_id_chunk_password VARCHAR(50) NOT NULL,
    job_first_segment         VARCHAR(50) NOT NULL DEFAULT '',
    UNIQUE KEY FK_task_chunks_task_id (task_id),
    CONSTRAINT FK_task_chunks_task_id FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY FK_task_chunks_matecat_id_job (matecat_id_job),
    KEY FK_task_chunks_project_id (project_id),
    CONSTRAINT FK_task_chunks_project_id FOREIGN KEY (project_id) REFERENCES Projects (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `UserQualifiedPairs` (
  user_id              INT(10) UNSIGNED NOT NULL,
  language_id_source   INT(10) UNSIGNED NOT NULL,
  language_code_source VARCHAR(3) NOT NULL,
  country_id_source    INT(10) UNSIGNED NOT NULL,
  country_code_source  VARCHAR(4) NOT NULL,
  language_id_target   INT(10) UNSIGNED NOT NULL,
  language_code_target VARCHAR(3) NOT NULL,
  country_id_target    INT(10) UNSIGNED NOT NULL,
  country_code_target  VARCHAR(4) NOT NULL,
  qualification_level  INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (user_id, language_code_source, country_code_source, language_code_target, country_code_target),
  KEY `FK_user_qualified_pairs_user` (`user_id`),
  CONSTRAINT `FK_user_qualified_pairs_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_UserQualifiedPairs_language_id_source` FOREIGN KEY (`language_id_source`) REFERENCES `Languages` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_UserQualifiedPairs_country_id_source`  FOREIGN KEY (`country_id_source`)  REFERENCES `Countries` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_UserQualifiedPairs_language_id_target` FOREIGN KEY (`language_id_target`) REFERENCES `Languages` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_UserQualifiedPairs_country_id_target`  FOREIGN KEY (`country_id_target`)  REFERENCES `Countries` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `RequiredOrgQualificationLevels` (
  org_id                       INT(10) UNSIGNED NOT NULL,
  required_qualification_level INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (org_id),
  CONSTRAINT `FK_RequiredOrgQualificationLevels_org_id` FOREIGN KEY (`org_id`) REFERENCES `Organisations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `RequiredTaskQualificationLevels` (
  task_id                      BIGINT(20) UNSIGNED NOT NULL,
  required_qualification_level INT(10)    UNSIGNED NOT NULL,
  PRIMARY KEY (task_id),
  CONSTRAINT `FK_RequiredTaskQualificationLevels_task_id` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `OrgIDMatchingNeon` (
  org_id_neon  INT(10) UNSIGNED NOT NULL,
  org_id       INT(10) UNSIGNED NOT NULL,
  created_time DATETIME DEFAULT NULL,
  PRIMARY KEY (org_id_neon),
  KEY FK_OrgIDMatchingNeon_Organisations (org_id),
  CONSTRAINT FK_OrgIDMatchingNeon_Organisations FOREIGN KEY (org_id) REFERENCES Organisations (id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `TaskTranslatedInMatecat` (
  `task_id` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `TaskInviteSentToUsers` (
    task_id BIGINT(20) UNSIGNED NOT NULL,
    user_id INT   (10) UNSIGNED NOT NULL,
    date_sent_invite datetime NOT NULL,
    KEY FK_invite_task_id (task_id),
    CONSTRAINT FK_invite_task_id FOREIGN KEY (task_id) REFERENCES Tasks (id) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY FK_invite_user_id (user_id),
    CONSTRAINT FK_invite_user_id FOREIGN KEY (user_id) REFERENCES Users (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `TermsAcceptedUsers` (
    user_id        INT (10) UNSIGNED NOT NULL,
    accepted_level INT (10) UNSIGNED NOT NULL,
    UNIQUE KEY FK_terms_user_id (user_id),
    CONSTRAINT FK_terms_user_id FOREIGN KEY (user_id) REFERENCES Users (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `PrivateTMKeys` (
    project_id INT(10) UNSIGNED NOT NULL,
    mt_engine INT(10)  UNSIGNED NOT NULL,
    pretranslate_100   INT(10) UNSIGNED NOT NULL,
    lexiqa INT(10)     UNSIGNED NOT NULL,
    private_tm_key     VARCHAR(255) NOT NULL,
    KEY FK_PrivateTMKeys_project_id (project_id),
    CONSTRAINT FK_PrivateTMKeys_project_id FOREIGN KEY (project_id) REFERENCES Projects (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `UserNeonAccount` (
  user_id    INT(10) UNSIGNED NOT NULL,
  account_id INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY FK_UserNeonAccount_user_id (user_id),
  KEY         account_id                 (account_id),
  CONSTRAINT FK_UserNeonAccount_user_id FOREIGN KEY (user_id) REFERENCES Users (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `MatecatRecordedJobStatus` (
    matecat_id_job          INT(10) UNSIGNED NOT NULL,
    matecat_id_job_password VARCHAR(50) NOT NULL,
    job_status              VARCHAR(20) NOT NULL,
    UNIQUE KEY job_job_password (matecat_id_job, matecat_id_job_password)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `TaskCompleteDates` (
  task_id       BIGINT(20) UNSIGNED NOT NULL,
  complete_date DATETIME NOT NULL,
  PRIMARY KEY (`task_id`),
  KEY key_complete_date (complete_date),
  CONSTRAINT `FK_TaskCompleteDates_task_id` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `DiscourseID` (
  project_id INT(10) UNSIGNED NOT NULL,
  topic_id   INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (project_id),
  CONSTRAINT FK_DiscourseID_project_id FOREIGN KEY (project_id) REFERENCES Projects (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `UserURLs` (
  user_id INT(10) UNSIGNED NOT NULL,
  url_key VARCHAR(20)  COLLATE utf8mb4_unicode_ci NOT NULL,
  url     VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  UNIQUE KEY `UserURLs` (`user_id`, `url_key`),
  KEY `FK_UserURLs_Users` (`user_id`),
  CONSTRAINT `FK_UserURLs_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `UserExpertises` (
  user_id       INT(10) UNSIGNED NOT NULL,
  expertise_key VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  UNIQUE KEY `UserExpertises` (`user_id`, `expertise_key`),
  KEY `FK_UserExpertises_Users` (`user_id`),
  CONSTRAINT `FK_UserExpertises_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `UserHowheards` (
  user_id      INT(10) UNSIGNED NOT NULL,
  reviewed     INT(10) UNSIGNED NOT NULL DEFAULT 0,
  howheard_key VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY `FK_UserHowheards_Users` (`user_id`),
  KEY         `FK_UserHowheards_reviewed` (`reviewed`),
  CONSTRAINT  `FK_UserHowheards_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `communications_consents` (
  user_id      INT(10) UNSIGNED NOT NULL,
  accepted     INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY `FK_communications_consents_Users` (`user_id`),
  CONSTRAINT  `FK_communications_consents_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `UserCertifications` (
  id                INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id           INT(10) UNSIGNED NOT NULL,
  vid               INT(10) UNSIGNED NOT NULL default 0,
  reviewed          INT(10) UNSIGNED NOT NULL DEFAULT 0,
  certification_key VARCHAR(20)  COLLATE utf8mb4_unicode_ci NOT NULL,
  filename          VARCHAR(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  mimetype          VARCHAR(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  note              TEXT         COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_UserCertifications_Users` (`user_id`),
  KEY `FK_UserCertifications_reviewed` (`reviewed`),
  CONSTRAINT `FK_UserCertifications_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `admin_comment` (
  id            INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id       INT(10) UNSIGNED NOT NULL,
  admin_id      INT(10) UNSIGNED NOT NULL,
  work_again    INT(10) UNSIGNED NOT NULL,
  created       DATETIME NOT NULL,
  admin_comment VARCHAR(2000) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_admin_comment_Users` (`user_id`),
  CONSTRAINT `FK_admin_comment_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `adjust_points` (
  id            INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id       INT(10) UNSIGNED NOT NULL,
  admin_id      INT(10) UNSIGNED NOT NULL,
  points        INT(10) SIGNED NOT NULL,
  created       datetime NOT NULL,
  admin_comment VARCHAR(2000) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_adjust_points_Users` (`user_id`),
  CONSTRAINT `FK_adjust_points_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `adjust_points_strategic` (
  id            INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id       INT(10) UNSIGNED NOT NULL,
  admin_id      INT(10) UNSIGNED NOT NULL,
  points        INT(10) SIGNED NOT NULL,
  created       datetime NOT NULL,
  admin_comment VARCHAR(2000) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_adjust_points_Users` (`user_id`),
  CONSTRAINT `FK_adjust_points_strategic_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `TrackCodes` (
  id INT(10) UNSIGNED NOT NULL,
  track_code VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO TrackCodes VALUES (1, '');


CREATE TABLE IF NOT EXISTS `TrackedRegistrations` (
  user_id INT(10) UNSIGNED NOT NULL,
  referer VARCHAR(128) NOT NULL,
  PRIMARY KEY (user_id),
  CONSTRAINT `FK_TrackedRegistrations_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `TestingCenterProjects` (
  user_id                 INT(10) UNSIGNED NOT NULL,
  project_id              INT(10) UNSIGNED NOT NULL,
  translation_task_id  BIGINT(20) UNSIGNED NOT NULL,
  proofreading_task_id BIGINT(20) UNSIGNED NOT NULL,
  project_to_copy_id      INT(10) UNSIGNED NOT NULL,
  language_code_source VARCHAR(3)          NOT NULL,
  language_code_target VARCHAR(3)          NOT NULL,
  KEY FK_TestingCenterProjects_Users (user_id),
  CONSTRAINT FK_TestingCenterProjects_Users FOREIGN KEY (user_id) REFERENCES Users (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `ProjectRestrictions` (
  project_id               INT(10) UNSIGNED NOT NULL,
  restrict_translate_tasks INT(10) UNSIGNED NOT NULL,
  restrict_revise_tasks    INT(10) UNSIGNED NOT NULL,
  UNIQUE KEY `project_id` (`project_id`),
  CONSTRAINT `FK_ProjectRestrictions_Projects` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `MemsourceUsers` (
  user_id           INT(10) UNSIGNED NOT NULL,
  memsource_user_id BIGINT(20) UNSIGNED NOT NULL,
  memsource_user_uid VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY FK_MemsourceUsers_user_id (user_id),
          KEY memsource_user_uid        (memsource_user_uid),
  CONSTRAINT FK_MemsourceUsers_user_id FOREIGN KEY (user_id) REFERENCES Users (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `MemsourceClients` (
  org_id               INT(10) UNSIGNED NOT NULL,
  memsource_client_id  BIGINT(20) UNSIGNED NOT NULL,
  memsource_client_uid VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY FK_MemsourceClients_org_id (org_id),
  UNIQUE  KEY memsource_client_id        (memsource_client_id),
  CONSTRAINT FK_MemsourceClients_org_id FOREIGN KEY (org_id) REFERENCES Organisations (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `MemsourceProjects` (
  project_id            INT(10) UNSIGNED NOT NULL,
  memsource_project_id  BIGINT(20) UNSIGNED NOT NULL,
  memsource_project_uid VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  created_by_uid        VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  owner_uid             VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  created_by_id         BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  owner_id              BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  workflow_level_1      VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  workflow_level_2      VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  workflow_level_3      VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  workflow_level_4      VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  workflow_level_5      VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  workflow_level_6      VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  workflow_level_7      VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  workflow_level_8      VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  workflow_level_9      VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  workflow_level_10     VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  workflow_level_11     VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  workflow_level_12     VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY FK_MemsourceProjects_project_id (project_id),
  UNIQUE  KEY memsource_project_id            (memsource_project_id),
  UNIQUE  KEY memsource_project_uid           (memsource_project_uid),
  CONSTRAINT FK_MemsourceProjects_project_id FOREIGN KEY (project_id) REFERENCES Projects (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `MemsourceProjectLanguages` (
  project_id               INT(10) UNSIGNED NOT NULL,
  kp_source_language_pair  VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  kp_target_language_pairs VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY FK_MemsourceProjectLanguages_project_id (project_id),
  CONSTRAINT FK_MemsourceProjectLanguages_project_id FOREIGN KEY (project_id) REFERENCES Projects (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `MemsourceSelfServiceProjects` (
  memsource_project_id  BIGINT(20) UNSIGNED NOT NULL,
  split                    INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (memsource_project_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `MemsourceTasks` (
  task_id            BIGINT(20) UNSIGNED NOT NULL,
  memsource_task_id  BIGINT(20) UNSIGNED NOT NULL,
  memsource_task_uid VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  task               VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  internalId         VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL
  workflowLevel      INT(10) UNSIGNED NOT NULL,
  beginIndex         INT(10) UNSIGNED NOT NULL,
  endIndex           INT(10) UNSIGNED NOT NULL,
  prerequisite       BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY FK_MemsourceTasks_task_id (task_id),
  UNIQUE  KEY memsource_task_uid        (memsource_task_uid),
  CONSTRAINT FK_MemsourceTasks_task_id FOREIGN KEY (task_id) REFERENCES Tasks (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `ProcessedMemsourceTaskUIDs` (
  memsource_task_uid VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (memsource_task_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `memsource_statuses` (
  task_id            BIGINT(20) UNSIGNED NOT NULL,
  memsource_task_uid VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  status             VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  status_time        DATETIME NOT NULL,
  KEY FK_memsource_statuses_task_id         (task_id),
  KEY memsource_statuses_memsource_task_uid (memsource_task_uid),
  CONSTRAINT FK_memsource_statuses_task_id FOREIGN KEY (task_id) REFERENCES Tasks (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `queue_copy_task_original_files` (
  project_id         INT(10) UNSIGNED NOT NULL,
  task_id            BIGINT(20) UNSIGNED NOT NULL,
  memsource_task_uid VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  filename           VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  UNIQUE KEY FK_queue_copy_task_original_files_task_id (task_id),
  CONSTRAINT FK_queue_copy_task_original_files_task_id FOREIGN KEY (task_id) REFERENCES Tasks (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `Referers` (
  referer VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (referer)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `GoogleUserDetails` (
  email      VARCHAR(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  first_name VARCHAR(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  last_name  VARCHAR(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  retrieved  datetime NOT NULL,
  KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `WillBeDeletedUsers` (
  user_id     INT(10) UNSIGNED NOT NULL,
  date_warned DATETIME DEFAULT NULL,
  KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `master_kato_tm_tasks` (
  task_id BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (task_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `prozdata` (
  `id` int(8) NOT NULL,
   user_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email2` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sourcelang` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `targlang` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wordstranslated` int(8) NOT NULL,
  `taskscompleted` int(8) NOT NULL,
  `org` tinyint(2) NOT NULL,
  `kpid` int(8) NOT NULL,
  `prozid` int(8) NOT NULL,
  `profilelink` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `email2` (`email2`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `TaskPaids` (
  task_id BIGINT(20) UNSIGNED NOT NULL,
  level      INT(10) UNSIGNED NOT NULL,
  UNIQUE KEY FK_TaskPaid (task_id),
  CONSTRAINT FK_TaskPaid FOREIGN KEY (task_id) REFERENCES Tasks (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `strategic_cut_offs` (
  `language_id-source` INT(10) UNSIGNED NOT NULL,
  `language_id-target` INT(10) UNSIGNED NOT NULL,
  `nigeria`            INT(10) UNSIGNED NOT NULL,
  language_code_source VARCHAR(3)       NOT NULL,
  language_code_target VARCHAR(3)       NOT NULL,
  start                DATETIME         NOT NULL,
  end                  DATETIME         NOT NULL,
  KEY `FK_strategic_cut_offs_Languages_s` (`language_id-source`),
  KEY `FK_strategic_cut_offs_Languages_t` (`language_id-target`),
  KEY (language_code_source),
  KEY (language_code_target),
  CONSTRAINT `FK_strategic_cut_offs_Languages_s` FOREIGN KEY (`language_id-source`) REFERENCES `Languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_strategic_cut_offs_Languages_t` FOREIGN KEY (`language_id-target`) REFERENCES `Languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `Services` (
  id     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `desc` VARCHAR(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  ord    INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*
INSERT INTO Services (id, `desc`, ord) VALUES ( 6, 'Translation',         1);
INSERT INTO Services (id, `desc`, ord) VALUES ( 7, 'Revision',            2);
INSERT INTO Services (id, `desc`, ord) VALUES ( 8, 'Interpretation',      7);
INSERT INTO Services (id, `desc`, ord) VALUES (10, 'Subtitling',          3);
INSERT INTO Services (id, `desc`, ord) VALUES (11, 'Monolingual editing', 4);
INSERT INTO Services (id, `desc`, ord) VALUES (12, 'DTP',                 5);
INSERT INTO Services (id, `desc`, ord) VALUES (13, 'Voiceover',           6);
*/


CREATE TABLE IF NOT EXISTS `UserServices` (
  user_id     INT(10) UNSIGNED NOT NULL,
  service_id  INT(10) UNSIGNED NOT NULL,
  approved    INT(10) UNSIGNED DEFAULT 0,
  approved_by INT(10) UNSIGNED DEFAULT 0,
  KEY FK_user_services_users    (user_id),
  KEY FK_user_services_services (service_id),
  CONSTRAINT FK_user_services_users    FOREIGN KEY (user_id)    REFERENCES Users    (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FK_user_services_services FOREIGN KEY (service_id) REFERENCES Services (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `project_complete_dates` (
  project_id    INT(10) UNSIGNED NOT NULL,
  status        INT(10) UNSIGNED NOT NULL,
  complete_date DATETIME NOT NULL,
  PRIMARY KEY (project_id),
  KEY key_complete_date (complete_date),
  CONSTRAINT `FK_project_complete_dates_project_id` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `post_login_messages` (
  id         INT(11) NOT NULL AUTO_INCREMENT,
  user_id    INT(10) UNSIGNED NOT NULL,
  `show`     INT(10) UNSIGNED NOT NULL,
  date_shown DATETIME NOT NULL,
  message    TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (id),
  KEY         FK_post_login_messages_users (user_id),
  CONSTRAINT  FK_post_login_messages_users FOREIGN KEY (user_id) REFERENCES Users (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `selections` (
  language_code VARCHAR(3) NOT NULL,
  country_code  VARCHAR(4) NOT NULL,
  selection     VARCHAR(255) NOT NULL,
  memsource     VARCHAR(12) NOT NULL,
  memsource_name VARCHAR(255) NOT NULL DEFAULT '',
  enabled       INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (language_code, country_code),
  UNIQUE  KEY (selection),
  UNIQUE  KEY (memsource),
  KEY (enabled)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `taskclaims_required_to_make_claimable` (
  task_id           BIGINT(20) UNSIGNED NOT NULL,
  claimable_task_id BIGINT(20) UNSIGNED NOT NULL,
  project_id        INT(10)    UNSIGNED NOT NULL,
  PRIMARY KEY (task_id, claimable_task_id),
          KEY FK_claimable_task_id (claimable_task_id),
          KEY FK_required_task_id  (task_id),
          KEY FK_required_project_id  (project_id),
  CONSTRAINT FK_claimable_task_id FOREIGN KEY (claimable_task_id) REFERENCES Tasks (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FK_required_task_id  FOREIGN KEY (task_id)           REFERENCES Tasks (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FK_required_project_id FOREIGN KEY (project_id)      REFERENCES Projects (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/*---------------------------------------end of tables---------------------------------------------*/

/*---------------------------------------start of procs--------------------------------------------*/


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


DROP PROCEDURE IF EXISTS `addPasswordResetRequest`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `addPasswordResetRequest`(IN `uniqueId` CHAR(40), IN `userId` INT)
BEGIN
    INSERT INTO PasswordResetRequests (uid, user_id, `request-time`) VALUES (uniqueId,userId,NOW());
    SELECT 1 AS result;
END//
DELIMITER ;


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


DROP PROCEDURE IF EXISTS `get_creator`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_creator`(IN `projectID` INT)
BEGIN
    SELECT u.*
    FROM ProjectFiles p
    JOIN Users        u ON p.user_id=u.id
    WHERE p.project_id=projectID;
END//
DELIMITER ;


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

DROP PROCEDURE IF EXISTS `removeUserFromTaskBlacklist`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeUserFromTaskBlacklist`(IN userId INT, IN taskId BIGINT)
BEGIN
    DELETE FROM TaskTranslatorBlacklist WHERE user_id=userId AND task_id=taskId;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `archiveProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `archiveProject`(IN `projectId` INT, IN `user_id` INT)
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

    IF EXISTS(SELECT 1 FROM ProjectFiles WHERE project_id=projectId) THEN
      SELECT pf.user_id INTO @`userIdProjectCreator` FROM ProjectFiles pf WHERE pf.project_id=projectId;
      SELECT pf.filename INTO @`filename` FROM ProjectFiles pf WHERE pf.project_id=projectId;
      SELECT pf.`file-token` INTO @`fileToken` FROM ProjectFiles pf WHERE pf.project_id=projectId;
      SELECT pf.`mime-type` INTO @`mimeType` FROM ProjectFiles pf WHERE pf.project_id=projectId;
    ELSE
      set @`userIdProjectCreator` = 3297;
      set @`filename` = 'none';
      set @`fileToken` = 'none';
      set @`mimeType` = 'none';
    END IF;
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

DROP PROCEDURE IF EXISTS `queue_claim_task`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `queue_claim_task`(IN uID INT, IN tID BIGINT)
BEGIN
    INSERT INTO queue_claim_tasks
               (user_id, task_id)
        VALUES (    uID,     tID);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_queue_claim_tasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_queue_claim_tasks`()
BEGIN
    SELECT * FROM queue_claim_tasks;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `dequeue_claim_task`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `dequeue_claim_task`(IN tID BIGINT)
BEGIN
    DELETE FROM queue_claim_tasks WHERE task_id=tID;
END//
DELIMITER ;

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


DROP PROCEDURE IF EXISTS `deleteOrg`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteOrg`(IN `id` INT)
BEGIN
if EXISTS (select 1 from Organisations o where o.id=id) then
  DELETE FROM Organisations WHERE Organisations.id=id;
  DELETE FROM OrganisationExtendedProfiles WHERE OrganisationExtendedProfiles.id=id;
  select 1 as result;
else
  select 0 as result;
end if;
END//
DELIMITER ;


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


DROP PROCEDURE IF EXISTS `deleteTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteTask`(IN `id` INT)
BEGIN
    if EXISTS (select 1 from Tasks where Tasks.id=id) then

      # Double nested SELECT used to force use of temporary table, because otherwise, nested tables are not allowed in UPDATE. Temporary table will be very small.
      # If any WAITING_FOR_PREREQUISITES Task has a Prerequisite Task which is being deleted and it does not have any other non COMPLETE Prerequisites (SUM(...)=0), set it to PENDING_CLAIM
      UPDATE Tasks tt SET tt.`task-status_id`=2 WHERE tt.id IN (
        SELECT * FROM (
          SELECT t.id
          FROM       TaskPrerequisites tpa
          INNER JOIN Tasks             t   ON tpa.task_id=t.id AND t.`task-status_id`=1
          INNER JOIN TaskPrerequisites tp  ON t.id=tp.task_id
          INNER JOIN Tasks             tsk ON tp.`task_id-prerequisite`=tsk.id
          WHERE tpa.`task_id-prerequisite`=id
          GROUP BY t.id
          HAVING SUM(tsk.`task-status_id`!=4 AND tsk.id!=id)=0
        ) AS to_be_made_pending
      );

      SELECT project_id INTO @pID FROM Tasks WHERE Tasks.id=id;

      delete from Tasks where Tasks.id=id;

      call update_project_complete_date_project(@pID);

      select 1 as result;
    else
      select 0 as result;
    end if;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `deleteUser`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteUser`(IN `userId` INT)
BEGIN
    if EXISTS (select 1 from Users where Users.id = userId) then

        SELECT email INTO @email FROM Users WHERE id=userId;
        DELETE FROM GoogleUserDetails WHERE email=@email;

        UPDATE UserPersonalInformation SET
           `first-name`='',
           `last-name`='',
           `mobile-number`='',
           `business-number`='',
           `language-preference`=1786,
           `job-title`='',
           `address`='',
           `city`='',
           `country`='',
           `receive_credit`=0
        WHERE user_id=userId;

        UPDATE Users SET
           `display-name`='',
           `email`=CONCAT(FLOOR(RAND() * 1000000000000), '@aaa.bbb'),
           `password`='',
           `biography`='',
           `language_id`=1786,
           `country_id`=1,
           `nonce`=0,
           `created-time`='2000-01-01 01:01:01'
        WHERE id=userId;

        DELETE FROM UserLogins
        WHERE user_id=userId;

        DELETE FROM UserQualifiedPairs
        WHERE user_id=userId;

        DELETE FROM UserURLs
        WHERE user_id=userId;

        DELETE FROM UserExpertises
        WHERE user_id=userId;

        DELETE FROM UserHowheards
        WHERE user_id=userId;

        DELETE FROM UserTaskStreamNotifications
        WHERE user_id=userId;

        DELETE FROM Admins
        WHERE user_id=userId;

        select 1 as result;
    else
        select 0 as result;
    end if;
END//
DELIMITER ;


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


DROP PROCEDURE IF EXISTS `getActiveSourceLanguages`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getActiveSourceLanguages`()
 READS SQL DATA
BEGIN
    SELECT `en-name` as name, code, id
        FROM Languages
        WHERE id IN (SELECT `language_id-source`
                        FROM Tasks
                        WHERE published = 1 AND `task-status_id` = 2)
    ORDER BY `en-name`;
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `getActiveTargetLanguages`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getActiveTargetLanguages`()
    READS SQL DATA
BEGIN
    SELECT `en-name` as name, code, id
        FROM Languages
        WHERE id IN (SELECT `language_id-target`
                        FROM Tasks
                        WHERE published = 1 AND `task-status_id` = 2)
    ORDER BY `en-name`;
END//
DELIMITER ;


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

DROP PROCEDURE IF EXISTS `getAdminsForOrg`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getAdminsForOrg`(IN `orgId` INT)
BEGIN
    SELECT u.id, u.`display-name` as display_name, u.email, u.password, u.biography,
        (SELECT `en-name` FROM Languages l WHERE l.id = u.`language_id`) AS `languageName`,
        (SELECT code FROM Languages l WHERE l.id = u.`language_id`) AS `languageCode`,
        (SELECT `en-name` FROM Countries c WHERE c.id = u.`country_id`) AS `countryName`,
        (SELECT code FROM Countries c WHERE c.id = u.`country_id`) AS `countryCode`,
        u.nonce, u.`created-time` as created_time
    FROM Users u JOIN Admins a ON a.user_id=u.id
    WHERE a.organisation_id=orgId;
END//
DELIMITER ;

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

DROP PROCEDURE IF EXISTS `getArchivedProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getArchivedProject`(IN `projectId` INT, IN `titleText` VARCHAR(128), IN `descr` VARCHAR(4096), IN `imp` VARCHAR(4096), IN `deadlineTime` DATETIME, IN `orgId` INT, IN `ref` VARCHAR(128), IN `wordCount` INT, IN `createdTime` DATETIME, IN `archiveDate` DATETIME, IN `archiverId` INT, IN `lCode` VARCHAR(3), IN `cCode` VARCHAR(4), IN imageUploaded BIT(1), IN imageApproved BIT(1))
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
        and (deadlineTime is null or p.deadline=deadlineTime)
        and (orgId is null or p.organisation_id=orgId) 
        and (ref is null or p.reference=ref)
        and (wordCount is null or p.`word-count`=wordCount) 
        and (createdTime is null or p.created = createdTime)
        and (lCode is null or @lID=lCode)
        and (cCode is null or @cID=cCode)
        and (@lID is null or p.language_id=@lID)
        and (@cID is null or p.country_id = @cID)
        and (archiveDate is null or m.`archived-date`=archiveDate)
        and (archiverId is null or m.`user_id-archived`= archiverId)
        AND (imageUploaded IS NULL OR p.image_uploaded = imageUploaded)
        AND (imageApproved IS NULL OR p.image_approved = imageApproved);

END//
DELIMITER ;


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


DROP PROCEDURE IF EXISTS `getCountry`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCountry`(IN `id` INT, IN `code` VARCHAR(4), IN `name` VARCHAR(128))
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



DROP PROCEDURE IF EXISTS `getLanguages`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLanguages`()
BEGIN
SELECT  `en-name` as name, code, id FROM Languages order by `en-name`;
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `getLatestAvailableTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLatestAvailableTasks`(IN `lim` INT, IN `offset` INT)
BEGIN
    if lim= '' or lim is null then set lim = ~0; end if;
    if offset='' or offset is null then set offset=0; end if;

    SELECT t.id, project_id as projectId, t.title, t.`word-count` as wordCount,
            (SELECT `en-name` from Languages where id =t.`language_id-source`) as `sourceLanguageName`,
            (SELECT code from Languages where id =t.`language_id-source`) as `sourceLanguageCode`, 
            (SELECT `en-name` from Languages where id =t.`language_id-target`) as `targetLanguageName`, 
            (SELECT code from Languages where id =t.`language_id-target`) as `targetLanguageCode`, 
            (SELECT `en-name` from Countries where id =t.`country_id-source`) as `sourceCountryName`, 
            (SELECT code from Countries where id =t.`country_id-source`) as `sourceCountryCode`, 
            (SELECT `en-name` from Countries where id =t.`country_id-target`) as `targetCountryName`, 
            (SELECT code from Countries where id =t.`country_id-target`) as `targetCountryCode`, 
            comment, `task-type_id` as taskType, `task-status_id` as taskStatus, published, t.deadline, t.`created-time` as createdTime
        FROM Tasks t 
        JOIN      Projects p ON t.project_id=p.id
        JOIN      RequiredTaskQualificationLevels tq ON t.id=tq.task_id
        LEFT JOIN RestrictedTasks r ON t.id=r.restricted_task_id
        WHERE NOT exists (SELECT 1 
                            FROM TaskClaims 
                            WHERE TaskClaims.task_id = t.id) 
        AND t.published = 1 
        AND t.`task-status_id` = 2 
        AND r.restricted_task_id IS NULL
        AND tq.required_qualification_level=1
        ORDER BY `created-time` DESC 
        LIMIT offset, lim;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getLatestAvailableTasksCount`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLatestAvailableTasksCount`()
BEGIN
    SELECT count(*) as result
        FROM Tasks t 
        JOIN      Projects p ON t.project_id=p.id
        JOIN      RequiredTaskQualificationLevels tq ON t.id=tq.task_id
        LEFT JOIN RestrictedTasks r ON t.id=r.restricted_task_id
        WHERE NOT exists (SELECT 1 
                            FROM TaskClaims 
                            WHERE TaskClaims.task_id = t.id) 
        AND t.published = 1 
        AND t.`task-status_id` = 2
        AND r.restricted_task_id IS NULL
        AND tq.required_qualification_level=1;
END//
DELIMITER ;

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


DROP PROCEDURE IF EXISTS `getOrg`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrg`(IN `id` INT, IN `name` VARCHAR(128), IN `url` VARCHAR(128), IN `bio` VARCHAR(4096), IN `email` VARCHAR(128), IN `address` VARCHAR(128), IN `city` VARCHAR(128), IN `country` VARCHAR(128), IN `regionalFocus` VARCHAR(128))
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


DROP PROCEDURE IF EXISTS `getOrganisationExtendedProfile`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrganisationExtendedProfile`(IN `id` INT)
BEGIN
  SELECT * FROM OrganisationExtendedProfiles o
  WHERE o.id=id;
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `getOrgMembers`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrgMembers`(IN `orgId` INT)
BEGIN
    SELECT id, `display-name` AS display_name, email, password, biography,
            (select `en-name` from Languages where id =u.`language_id`) as `languageName`, 
            (select code from Languages where id =u.`language_id`) as `languageCode`, 
            (select `en-name` from Countries where id =u.`country_id`) as `countryName`, 
            (select code from Countries where id =u.`country_id`) as `countryCode`, 
            nonce,`created-time` as created_time
    FROM OrganisationMembers om JOIN Users u ON om.user_id=u.id
    WHERE organisation_id=orgId
    UNION
    SELECT id, `display-name` AS display_name, email, password, biography,
            (select `en-name` from Languages where id =u.`language_id`) as `languageName`,
            (select code from Languages where id =u.`language_id`) as `languageCode`,
            (select `en-name` from Countries where id =u.`country_id`) as `countryName`,
            (select code from Countries where id =u.`country_id`) as `countryCode`,
            nonce,`created-time` as created_time
    FROM Admins om JOIN Users u ON om.user_id=u.id
    WHERE organisation_id=orgId
    ORDER BY 2;
END//
DELIMITER ;


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
        AND   deadline > DATE_SUB(DATE_SUB(NOW(), INTERVAL 1 DAY), INTERVAL 30 MINUTE)
        AND `task-status_id` != 4
        AND published = 1;
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `getEarlyWarningTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getEarlyWarningTasks`()
BEGIN
    SELECT
        t.id, t.project_id AS projectId, t.title, t.`word-count` AS wordCount,
        (SELECT `en-name` FROM Languages WHERE id=t.`language_id-source`) AS `sourceLanguageName`,
        (SELECT code      FROM Languages WHERE id=t.`language_id-source`) AS `sourceLanguageCode`,
        (SELECT `en-name` FROM Languages WHERE id=t.`language_id-target`) AS `targetLanguageName`,
        (SELECT code      FROM Languages WHERE id=t.`language_id-target`) AS `targetLanguageCode`,
        (SELECT `en-name` FROM Countries WHERE id=t.`country_id-source`)  AS `sourceCountryName`,
        (SELECT code      FROM Countries WHERE id=t.`country_id-source`)  AS `sourceCountryCode`,
        (SELECT `en-name` FROM Countries WHERE id=t.`country_id-target`)  AS `targetCountryName`,
        (SELECT code      FROM Countries WHERE id=t.`country_id-target`)  AS `targetCountryCode`,
        t.comment, t.`task-type_id` as taskType, t.`task-status_id` AS taskStatus, t.published, t.deadline
    FROM Tasks t
    LEFT JOIN TaskNotificationSent n ON t.id=n.task_id
    WHERE
        t.deadline < DATE_ADD(NOW(), INTERVAL 1 WEEK) AND
        t.deadline > DATE_SUB(DATE_ADD(NOW(), INTERVAL 1 WEEK), INTERVAL 30 HOUR) AND
        t.`task-status_id`!=4 AND
        t.published=1 AND
        n.notification IS NULL;
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `getLateWarningTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLateWarningTasks`()
BEGIN
    SELECT
        t.id, t.project_id AS projectId, t.title, t.`word-count` AS wordCount,
        (SELECT `en-name` FROM Languages WHERE id=t.`language_id-source`) AS `sourceLanguageName`,
        (SELECT code      FROM Languages WHERE id=t.`language_id-source`) AS `sourceLanguageCode`,
        (SELECT `en-name` FROM Languages WHERE id=t.`language_id-target`) AS `targetLanguageName`,
        (SELECT code      FROM Languages WHERE id=t.`language_id-target`) AS `targetLanguageCode`,
        (SELECT `en-name` FROM Countries WHERE id=t.`country_id-source`)  AS `sourceCountryName`,
        (SELECT code      FROM Countries WHERE id=t.`country_id-source`)  AS `sourceCountryCode`,
        (SELECT `en-name` FROM Countries WHERE id=t.`country_id-target`)  AS `targetCountryName`,
        (SELECT code      FROM Countries WHERE id=t.`country_id-target`)  AS `targetCountryCode`,
        t.comment, t.`task-type_id` as taskType, t.`task-status_id` AS taskStatus, t.published, t.deadline
    FROM Tasks t
    LEFT JOIN TaskNotificationSent n ON t.id=n.task_id
    WHERE
        t.deadline < DATE_SUB(NOW(), INTERVAL 1 WEEK) AND
        t.`task-status_id`!=4 AND
        (n.notification IS NULL OR n.notification<2);
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `taskNotificationSentInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `taskNotificationSentInsertAndUpdate`(IN `taskId` INT, IN `notification` INT)
BEGIN
    REPLACE INTO `TaskNotificationSent` (`task_id`, `notification`) VALUES (taskId, notification);
    select 1 as 'result';
END//
DELIMITER ;


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


DROP PROCEDURE IF EXISTS `getProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getProject`(IN `projectId` INT, IN `titleText` VARCHAR(128), IN `descr` VARCHAR(4096), IN `impactText` VARCHAR(4096), IN `deadlineTime` DATETIME, IN `orgId` INT, IN `ref` VARCHAR(128), IN `wordCount` INT, IN `createdTime` DATETIME, IN `sourceCountryCode` VARCHAR(4), IN `sourceLanguageCode` VARCHAR(3), IN imageUploaded BIT(1), IN imageApproved BIT(1))
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
        AND (deadlineTime is null or p.deadline = deadlineTime)
        AND (orgId is null or p.organisation_id = orgId)
        AND (ref is null or p.reference = ref)
        AND (wordCount is null or p.`word-count`= wordCount)
        AND (createdTime is null or p.created = createdTime)
        AND (sourceCountryCode is null or p.country_id = (select c.id from Countries c where c.code = sourceCountryCode))
        AND (sourceLanguageCode is null or p.language_id=(select l.id from Languages l where l.code = sourceLanguageCode))
        AND (imageUploaded IS NULL OR p.image_uploaded = imageUploaded)
        AND (imageApproved IS NULL OR p.image_approved = imageApproved)
        ORDER BY p.created DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getOrgProjects`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrgProjects`(IN `orgId` INT, IN `months` INT)
BEGIN
    SELECT
        id,
        title,
        description,
        impact,
        deadline,
        organisation_id AS organisationId,
        reference,
        `word-count` AS wordCount,
        created AS createdTime,
        (SELECT SUM(tsk.`task-status_id`)/(COUNT(tsk.`task-status_id`)*4) FROM Tasks tsk WHERE tsk.project_id=p.id) AS status,
        image_uploaded AS imageUploaded,
        image_approved AS imageApproved
    FROM Projects p
    WHERE
        p.organisation_id=orgId AND
        p.deadline > DATE_SUB(NOW(), INTERVAL months MONTH)
    ORDER BY p.created DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_project_id_for_latest_org_image`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_project_id_for_latest_org_image`(IN `orgId` INT)
BEGIN
    SELECT id
    FROM Projects p
    WHERE
        p.organisation_id=orgId AND
        image_uploaded AND image_approved
    ORDER BY p.created DESC
    LIMIT 1;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_uploaded_approved`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_uploaded_approved`(IN `projectID` INT)
BEGIN
    UPDATE Projects SET image_uploaded=b'1', image_approved=b'1' WHERE id=projectID;
END//
DELIMITER ;

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


DROP PROCEDURE IF EXISTS `getRegistrationId`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getRegistrationId`(IN `userId` INT)
BEGIN
    SELECT unique_id
        FROM RegisteredUsers
        WHERE user_id = userId;
END//
DELIMITER ;


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
        SELECT * FROM Users WHERE FALSE;
    end if;
END//
DELIMITER ;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;


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


DROP PROCEDURE IF EXISTS `getTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTask`(IN `id` BIGINT, IN `projectID` INT, IN `name` VARCHAR(128), IN `wordCount` INT, IN `sCode` VARCHAR(3), IN `tCode` VARCHAR(3), IN `created` DATETIME, IN `sCC` VARCHAR(4), IN `tCC` VARCHAR(4), IN `taskComment` VARCHAR(4096), IN `tType` INT, IN `tStatus` INT, IN `pub` BIT(1), IN `dLine` DATETIME)
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
            and (created is null or t.`created-time` = created)
            and (taskComment is null or t.`comment`= taskComment)
            and (tStatus is null or t.`task-status_id` = tStatus)
            and (tType is null or t.`task-type_id` = tType)
            and (pub is null or t.`published` = pub)
            and (dLine is null or t.`deadline` = dLine)
  ORDER BY targetLanguageName, targetCountryName, t.`task-type_id`, t.id;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getVolunteerProjectTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getVolunteerProjectTasks`(IN `projectID` INT, IN `uID` INT)
BEGIN
    SELECT
        t.id AS task_id,
        t.title,
        (SELECT code      FROM Languages l WHERE l.id=t.`language_id-target`) AS target_language_code,
        (SELECT code      FROM Countries c WHERE c.id=t.`country_id-target` ) AS target_country_code,
        (SELECT `en-name` FROM Languages l WHERE l.id=t.`language_id-target`) AS target_language_name,
        (SELECT `en-name` FROM Countries c WHERE c.id=t.`country_id-target` ) AS target_country_name,
        t.`task-type_id`   AS type_id,
        t.`task-status_id` AS status_id,
        t.deadline
    FROM      Tasks                            t
    JOIN      Projects                         p ON t.project_id=p.id
    JOIN      RequiredTaskQualificationLevels tq ON t.id=tq.task_id
    LEFT JOIN Badges                           b ON p.organisation_id=b.owner_id AND b.title='Qualified'
    LEFT JOIN RestrictedTasks                  r ON t.id=r.restricted_task_id
    LEFT JOIN UserQualifiedPairs             uqp ON
        uqp.user_id=uID AND
        t.`language_id-source`=uqp.language_id_source AND
        t.`language_id-target`=uqp.language_id_target
    WHERE
        t.project_id=projectID AND
        t.published=1 AND
        ((uqp.user_id IS NOT NULL AND tq.required_qualification_level<=uqp.qualification_level)) AND
        (
            r.restricted_task_id IS NULL OR
            b.id IS NULL OR
            b.id IN (SELECT ub.badge_id FROM UserBadges ub WHERE ub.user_id=uID)
        )
    GROUP BY t.id
    ORDER BY target_language_name, target_country_name, t.`task-type_id`, t.id;
END//
DELIMITER ;

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


DROP PROCEDURE IF EXISTS `getTaskTags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskTags`(IN `tID` INT)
BEGIN
  set @pID = null;
  select project_id into @pID  from Tasks where id=tID;
  call getProjectTags(@pID);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getTaskType`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskType`(IN `tID` INT)
BEGIN
  select name from TaskTypes where id=tID;
END//
DELIMITER ;


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

DROP PROCEDURE IF EXISTS `getUserBadges`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserBadges`(IN `id` INT)
BEGIN
    SELECT b.*
        FROM UserBadges ub JOIN Badges b ON ub.badge_id = b.id
        WHERE user_id = id;
END//
DELIMITER ;


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


DROP PROCEDURE IF EXISTS `getUserClaimedTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserClaimedTask`(IN `taskID` INT)
BEGIN
  IF EXISTS( SELECT 1 FROM TaskClaims tc WHERE tc.task_id=taskId) THEN
    SET @userId = false;
    SELECT user_id INTO @userId FROM TaskClaims WHERE task_id=taskId;
    call getUser(@userId,null,null,null,null,null,null,null,null);
  END IF;
END//
DELIMITER ;

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


DROP PROCEDURE IF EXISTS `getUserNotifications`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserNotifications`(IN `id` INT)
BEGIN
  SELECT *
      FROM UserNotifications
      WHERE user_id = id;
END//
DELIMITER ;


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

DROP PROCEDURE IF EXISTS `getUserTasksCount`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTasksCount`(IN `uID` INT)
BEGIN
    SELECT COUNT(1) as result
        FROM TaskClaims
        WHERE user_id = uID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getUserTaskStreamNotification`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTaskStreamNotification`(IN `uID` INT)
BEGIN
    SELECT CAST(u.strict as UNSIGNED) AS strict, u.user_id, u.`interval`, u.`last-sent` as last_sent
        FROM UserTaskStreamNotifications u
        WHERE u.user_id = uID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_special_translator`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_special_translator`(IN userID INT, IN typeID INT)
BEGIN
    REPLACE INTO SpecialTranslators (user_id, type) VALUES (userID, typeID);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_special_translator`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_special_translator`(IN `userID` INT)
BEGIN
    SELECT * FROM SpecialTranslators WHERE user_id=userID;
END//
DELIMITER ;

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


DROP PROCEDURE IF EXISTS `getUserTopTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTopTasks`(IN `uID` INT, IN `strict` INT, IN `lim` INT, IN `offset` INT, IN `taskType` INT, IN `sourceLanguage` VARCHAR(3), IN `targetLanguage` VARCHAR(3))
BEGIN
    IF lim=''    OR lim    IS NULL THEN SET lim    = ~0; END IF;
    IF offset='' OR offset IS NULL THEN SET offset =  0; END IF;
    IF taskType=''       THEN SET taskType       = NULL; END IF;
    IF sourceLanguage='' THEN SET sourceLanguage = NULL; END IF;
    IF targetLanguage='' THEN SET targetLanguage = NULL; END IF;

    SET @isSiteAdmin = 0;
    IF EXISTS (SELECT 1 FROM Admins WHERE user_id=uID AND organisation_id IS NULL) THEN
        SET @isSiteAdmin = 1;
    END IF;

    SELECT
        t.id, t.project_id as projectId, t.title, t.`word-count` AS wordCount,
        (SELECT `en-name` FROM Languages l WHERE l.id=t.`language_id-source`) AS `sourceLanguageName`,
        (SELECT `code`    FROM Languages l WHERE l.id=t.`language_id-source`) AS `sourceLanguageCode`,
        (SELECT `en-name` FROM Languages l WHERE l.id=t.`language_id-target`) AS `targetLanguageName`,
        (SELECT `code`    FROM Languages l WHERE l.id=t.`language_id-target`) AS `targetLanguageCode`,
        (SELECT `en-name` FROM Countries c WHERE c.id=t.`country_id-source`)  AS `sourceCountryName`,
        (SELECT `code`    FROM Countries c WHERE c.id=t.`country_id-source`)  AS `sourceCountryCode`,
        (SELECT `en-name` FROM Countries c WHERE c.id=t.`country_id-target`)  AS `targetCountryName`,
        (SELECT `code`    FROM Countries c WHERE c.id=t.`country_id-target`)  AS `targetCountryCode`,
        t.`comment`, t.`task-type_id` AS taskType, t.`task-status_id` AS taskStatus, t.published, t.deadline, t.`created-time` AS createdTime 
    FROM
        Users u,
        Tasks t
    JOIN      Projects p ON t.project_id=p.id
    JOIN      RequiredTaskQualificationLevels tq ON t.id=tq.task_id
    LEFT JOIN Badges   b ON p.organisation_id=b.owner_id AND b.title='Qualified'
    LEFT JOIN RestrictedTasks r ON t.id=r.restricted_task_id
    LEFT JOIN UserQualifiedPairs uqp ON
        uqp.user_id=uID AND
        t.`language_id-source`=uqp.language_id_source AND
        t.`language_id-target`=uqp.language_id_target AND
        t.`country_id-target`=uqp.country_id_target
    WHERE
        u.id=uID AND
        t.id NOT IN (SELECT t.task_id FROM TaskClaims t) AND
        t.published=1 AND
        t.`task-status_id`=2 AND
        NOT EXISTS (SELECT 1 FROM TaskTranslatorBlacklist t WHERE t.user_id=uID AND t.task_id=t.id) AND
        (taskType IS NULL OR t.`task-type_id`=taskType) AND
        (@isSiteAdmin=1 OR (uqp.user_id IS NOT NULL AND tq.required_qualification_level<=uqp.qualification_level)) AND
        (sourceLanguage IS NULL OR t.`language_id-source`=(SELECT l.id FROM Languages l WHERE l.code=sourceLanguage)) AND
        (targetLanguage IS NULL OR t.`language_id-target`=(SELECT l.id FROM Languages l WHERE l.code=targetLanguage)) AND
        (strict=0 OR uqp.user_id IS NOT NULL) AND
        (
            @isSiteAdmin=1 OR
            r.restricted_task_id IS NULL OR
            b.id IS NULL OR
            b.id IN (SELECT ub.badge_id FROM UserBadges ub WHERE ub.user_id=uID)
        )
    GROUP BY t.id
    ORDER BY
        IF(t.`language_id-target`=u.language_id, 500 + IF(u.country_id=t.`country_id-target`, 50, 0), 0) +
        IF(t.`language_id-source`=u.language_id, 250 + IF(u.country_id=t.`country_id-source`, 25, 0), 0) +
        IF(COUNT(uqp.user_id), 1000,
            IF(t.`language_id-target`=u.language_id, 500 + IF(u.country_id=t.`country_id-target`, 50, 0), 0) +
            IF(t.`language_id-source`=u.language_id, 500 + IF(u.country_id=t.`country_id-source`, 50, 0), 0)
        ) +
        IF(SUM(IFNULL(uqp.country_id_target, 0)=t.`country_id-target`), 50, 0) +
        IF(SUM(IFNULL(uqp.country_id_source, 0)=t.`country_id-source`), 50, 0) +
        (SELECT 250.*(1.0-POWER(0.75, COUNT(*)))/(1.0-0.75) FROM ProjectTags pt WHERE pt.project_id=t.project_id AND pt.tag_id IN (SELECT ut.tag_id FROM UserTags ut WHERE user_id=uID)) +
        LEAST(DATEDIFF(CURDATE(), t.`created-time`), 700) +
        IF(DATEDIFF(CURDATE(), t.deadline) > 91, -5000, 0)
        DESC
    LIMIT offset, lim;
END//
DELIMITER ;

-- Dumping structure for getUserTopTasksCount
DROP PROCEDURE IF EXISTS `getUserTopTasksCount`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTopTasksCount` (IN `uID` INT, IN `strict` INT, IN `taskType` INT, IN `sourceLanguage` VARCHAR(3), IN `targetLanguage` VARCHAR(3))
BEGIN
    if taskType = ''       then set taskType = null; end if;
    if sourceLanguage = '' then set sourceLanguage = null; end if;
    if targetLanguage = '' then set targetLanguage = null; end if;

    SET @isSiteAdmin = 0;
    IF EXISTS (SELECT 1 FROM Admins WHERE user_id=uID AND organisation_id IS NULL) THEN
        SET @isSiteAdmin = 1;
    END IF;

    SELECT COUNT(*) AS result FROM (
        SELECT t.id
        FROM Tasks t
        JOIN      Projects p ON t.project_id=p.id
        JOIN      RequiredTaskQualificationLevels tq ON t.id=tq.task_id
        LEFT JOIN Badges   b ON p.organisation_id=b.owner_id AND b.title='Qualified'
        LEFT JOIN RestrictedTasks r ON t.id=r.restricted_task_id
        LEFT JOIN UserQualifiedPairs uqp ON
            uqp.user_id=uID AND
            t.`language_id-source`=uqp.language_id_source AND
            t.`language_id-target`=uqp.language_id_target AND
            t.`country_id-target`=uqp.country_id_target
        WHERE t.id NOT IN ( SELECT t.task_id FROM TaskClaims t)
        AND t.published = 1 
        AND t.`task-status_id` = 2 
        AND not exists( SELECT 1 FROM TaskTranslatorBlacklist t WHERE t.user_id = uID AND t.task_id = t.id)
        AND (taskType is null or t.`task-type_id` = taskType)
        AND (@isSiteAdmin=1 OR (uqp.user_id IS NOT NULL AND tq.required_qualification_level<=uqp.qualification_level))
        AND (sourceLanguage is null or t.`language_id-source` = (SELECT l.id FROM Languages l WHERE l.code = sourceLanguage))
        AND (targetLanguage is null or t.`language_id-target` = (SELECT l.id FROM Languages l WHERE l.code = targetLanguage))
        AND (strict=0 OR uqp.user_id IS NOT NULL)
        AND
        (
            @isSiteAdmin=1 OR
            r.restricted_task_id IS NULL OR
            b.id IS NULL OR
            b.id IN (SELECT ub.badge_id FROM UserBadges ub WHERE ub.user_id=uID)
        )
        GROUP BY t.id
    ) AS tasks_to_be_counted;
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `alsoViewedTasks`(IN `taskID` INT, IN userID INT, IN `offset` INT)
BEGIN
    DECLARE current_task_langSource INT DEFAULT 0;
    DECLARE current_task_langTarget INT DEFAULT 0;
    DECLARE current_task_countrySource INT DEFAULT 0;
    DECLARE current_task_countryTarget INT DEFAULT 0;
    if offset='' or offset is null then set offset = 0; end if;

    SELECT `language_id-source`, `language_id-target`, `country_id-source`, `country_id-target`  
    INTO current_task_langSource, current_task_langTarget, current_task_countrySource, current_task_countryTarget FROM Tasks WHERE id = taskID;

    (
    SELECT
        t2.id,
        t2.project_id AS projectId,
        t2.title,
        t2.`word-count` AS wordCount,
        (SELECT `en-name` from Languages l where l.id = t2.`language_id-source`) as `sourceLanguageName`,
        (SELECT code from Languages l where l.id = t2.`language_id-source`) as `sourceLanguageCode`,
        (SELECT `en-name` from Languages l where l.id = t2.`language_id-target`) as `targetLanguageName`,
        (SELECT code from Languages l where l.id = t2.`language_id-target`) as `targetLanguageCode`,
        (SELECT `en-name` from Countries c where c.id = t2.`country_id-source`) as `sourceCountryName`,
        (SELECT code from Countries c where c.id = t2.`country_id-source`) as `sourceCountryCode`,
        (SELECT `en-name` from Countries c where c.id = t2.`country_id-target`) as `targetCountryName`,
        (SELECT code from Countries c where c.id = t2.`country_id-target`) as `targetCountryCode`,
        `comment`,
        `task-type_id` AS 'taskType',
        `task-status_id` AS 'taskStatus',
        published,
        deadline,
        `created-time` AS createdTime
     FROM
        (
        SELECT
            t.id,
            COUNT(*) AS task_count
        FROM TaskViews tv
        JOIN Tasks     t  ON
            t.id=tv.task_id AND
            tv.user_id IN (SELECT DISTINCT tv2.user_id FROM TaskViews tv2 WHERE tv2.task_id=taskID) AND
            t.id!=taskID AND
            t.`task-status_id`=2 AND
            t.`language_id-source`=current_task_langSource AND
            t.`language_id-target`=current_task_langTarget AND
            t.published=1
        JOIN      Projects p ON t.project_id=p.id
        JOIN      RequiredTaskQualificationLevels tq ON t.id=tq.task_id
        JOIN      UserQualifiedPairs             uqp ON
            uqp.user_id=userID AND
            t.`language_id-source`=uqp.language_id_source AND
            t.`language_id-target`=uqp.language_id_target AND
            t.`country_id-target`=uqp.country_id_target
        LEFT JOIN RestrictedTasks r ON t.id=r.restricted_task_id
        WHERE
            r.restricted_task_id IS NULL AND
            tq.required_qualification_level<=uqp.qualification_level
        GROUP BY tv.task_id
        ORDER BY task_count DESC
        ) AS t1
    JOIN Tasks t2 ON t1.id=t2.id
    LIMIT offset, 3
    );
END//
DELIMITER ;


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


DROP PROCEDURE IF EXISTS `hasUserClaimedSegmentationTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `hasUserClaimedSegmentationTask`(IN `uID` INT, IN `pID` INT)
BEGIN
SELECT exists (  select 1
                        FROM TaskClaims tc JOIN Tasks t ON tc.task_id = t.id
                        WHERE `user_id` = uID
                        AND `project_id` = pID
                        AND `task-type_id` = 1
                 ) as result;
END//
DELIMITER ;

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

DROP PROCEDURE IF EXISTS `is_admin_or_org_member`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `is_admin_or_org_member`(IN `uID` INT)
BEGIN
    SELECT EXISTS (
        SELECT user_id
        FROM Admins
        WHERE user_id=uID
        UNION
        SELECT user_id
        FROM OrganisationMembers
        WHERE user_id=uID
    ) AS result;
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


DROP PROCEDURE IF EXISTS `isOrgBanned`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `isOrgBanned`(IN `orgId` INT)
BEGIN
    SELECT exists (SELECT 1 FROM BannedOrganisations b WHERE b.org_id=orgId) as result;
END//
DELIMITER ;


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

DROP PROCEDURE IF EXISTS `logFileDownload`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `logFileDownload`(IN `tID` INT, IN `vID` INT, IN `uID` INT)
    MODIFIES SQL DATA
BEGIN
  insert into task_file_version_download (task_id,version_id,user_id,time_downloaded)
  values (tID,uID,vID,Now());
END//
DELIMITER ;


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


DROP PROCEDURE IF EXISTS `oauthAssociateAuthCodeScope`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthAssociateAuthCodeScope`(IN `authCodeId` INT, IN `scopeId` INT)
    MODIFIES SQL DATA
BEGIN
    INSERT INTO `oauth_session_authcode_scopes` (`oauth_session_authcode_id`, `scope_id`)
        VALUES (authCodeId, scopeId);
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `oauthAssociateRedirectUri`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthAssociateRedirectUri`(IN `sessionId` INT, IN `redirectUri` VARCHAR(255))
    MODIFIES SQL DATA
BEGIN
    INSERT INTO oauth_session_redirects (session_id, redirect_uri)
        VALUES (sessionId, redirectUri);
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `oauthAssociateRefreshToken`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthAssociateRefreshToken`(IN `accessTokenId` INT, IN `refreshToken` CHAR(40), IN `expireTime` INT, IN `clientId` CHAR(40))
    MODIFIES SQL DATA
BEGIN
    INSERT INTO oauth_session_refresh_tokens (session_access_token_id, refresh_token, refresh_token_expires, client_id)
        VALUES (accessTokenId, refreshToken, expireTime, clientId);
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `oauthAssociateScope`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthAssociateScope`(IN `accessTokenId` INT, IN `scopeId` INT)
    MODIFIES SQL DATA
BEGIN
    INSERT INTO `oauth_session_token_scopes` (session_access_token_id, scope_id)
        VALUES (accessTokenId, scopeId);
END//
DELIMITER ;


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


DROP PROCEDURE IF EXISTS `oauthGetClient`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthGetClient`(IN `clientId` CHAR(40), IN `clientSecret` CHAR(42), IN `redirectUri` VARCHAR(255))
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
        AND (clientSecret IS NULL OR `oauth_clients`.secret = SUBSTRING(clientSecret, 1, 40))
        AND (redirectUri IS NULL OR `oauth_client_endpoints`.redirect_uri = redirectUri);
END//
DELIMITER ;


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


DROP PROCEDURE IF EXISTS `oauthRemoveAuthCode`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthRemoveAuthCode`(IN `sessionId` INT)
    MODIFIES SQL DATA
BEGIN
    DELETE FROM oauth_session_authcodes
        WHERE session_id = sessionId;
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `oauthRemoveRefreshToken`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `oauthRemoveRefreshToken`(IN `refreshToken` CHAR(40))
    MODIFIES SQL DATA
BEGIN
    DELETE FROM `oauth_session_refresh_tokens`
        WHERE refresh_token = refreshToken;
END//
DELIMITER ;


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


DROP PROCEDURE IF EXISTS `organisationExtendedProfileInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `organisationExtendedProfileInsertAndUpdate`(
  IN `id` INT(10),
  IN `facebook` VARCHAR(255),
  IN `linkedin` VARCHAR(255),
  IN `primaryContactName` VARCHAR(255),
  IN `primaryContactTitle` VARCHAR(255),
  IN `primaryContactEmail` VARCHAR(255),
  IN `primaryContactPhone` VARCHAR(255),
  IN `otherContacts` VARCHAR(4096),
  IN `structure` VARCHAR(4096),
  IN `affiliations` VARCHAR(4096),
  IN `urlVideo1` VARCHAR(255),
  IN `urlVideo2` VARCHAR(255),
  IN `urlVideo3` VARCHAR(255),
  IN `subjectMatters` VARCHAR(4096),
  IN `activitys` VARCHAR(255),
  IN `employees` VARCHAR(255),
  IN `fundings` VARCHAR(255),
  IN `finds` VARCHAR(255),
  IN `translations` VARCHAR(255),
  IN `requests` VARCHAR(255),
  IN `contents` VARCHAR(255),
  IN `pages` VARCHAR(255),
  IN `sources` VARCHAR(255),
  IN `targets` VARCHAR(255),
  IN `oftens` VARCHAR(255))
BEGIN
  REPLACE INTO OrganisationExtendedProfiles
    (`id`,
     `facebook`,
     `linkedin`,
     `primaryContactName`,
     `primaryContactTitle`,
     `primaryContactEmail`,
     `primaryContactPhone`,
     `otherContacts`,
     `structure`,
     `affiliations`,
     `urlVideo1`,
     `urlVideo2`,
     `urlVideo3`,
     `subjectMatters`,
     `activitys`,
     `employees`,
     `fundings`,
     `finds`,
     `translations`,
     `requests`,
     `contents`,
     `pages`,
     `sources`,
     `targets`,
     `oftens`)
  VALUES
    (id,
     facebook,
     linkedin,
     primaryContactName,
     primaryContactTitle,
     primaryContactEmail,
     primaryContactPhone,
     otherContacts,
     structure,
     affiliations,
     urlVideo1,
     urlVideo2,
     urlVideo3,
     subjectMatters,
     activitys,
     employees,
     fundings,
     finds,
     translations,
     requests,
     contents,
     pages,
     sources,
     targets,
     oftens);
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `orgHasMember`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `orgHasMember`(IN `oID` INT, IN `uID` INT)
BEGIN
    select exists (select 1 from OrganisationMembers om where om.user_id=uID and om.organisation_id=oID) as result;
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `projectInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `projectInsertAndUpdate`(IN `projectId` INT, IN `titleText` VARCHAR(128), IN `descr` VARCHAR(4096), IN `impactText` VARCHAR(4096), IN `deadlineTime` DATETIME, IN `orgId` INT, IN `ref` VARCHAR(128), IN `wordCount` INT, IN `createdTime` DATETIME, IN `sourceCountryCode` VARCHAR(4), IN `sourceLanguageCode` VARCHAR(3), IN imageUploaded BIT(1), IN imageApproved BIT(1))
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

        if deadlineTime is null
        then set deadlineTime = DATE_ADD(now(),INTERVAL 14 DAY); end if;

                set @scID=null;
                        select c.id into @scID from Countries c where c.code=sourceCountryCode;
                        set @sID=null;
                        select l.id into @sID from Languages l where l.code=sourceLanguageCode;

        INSERT INTO Projects (title, description, impact, deadline, organisation_id, reference, `word-count`, created,language_id,country_id, image_uploaded, image_approved)
        VALUES (titleText, descr, impactText, deadlineTime, orgId, ref, wordCount, NOW(),@sID,@scID,imageUploaded, imageApproved);

        call insert_project_complete_date(LAST_INSERT_ID());

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

DROP PROCEDURE IF EXISTS `add_to_project_word_count`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_to_project_word_count`(IN `projectId` INT, IN `wordCount` INT)
BEGIN
    UPDATE Projects SET `word-count`=IF(`word-count`<=1, 0, `word-count`)+wordCount  WHERE id=projectId;
 END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `delete_from_project_word_count`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_from_project_word_count`(IN `projectId` INT, IN `wordCount` INT)
BEGIN
    UPDATE Projects SET `word-count`=IF(`word-count`<=(wordCount+1), 1, `word-count`-wordCount)  WHERE id=projectId;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `recordFileUpload`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `recordFileUpload`(IN `tID` INT, IN `name` TeXT, IN `content` VARCHAR(255), IN `uID` INT, IN `ver` INT)
    MODIFIES SQL DATA
BEGIN
    call set_task_complete_date(tID);

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


DROP PROCEDURE IF EXISTS `changeEmail`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `changeEmail`(IN `userId` INT, IN `eMail` VARCHAR(128))
BEGIN
    UPDATE Users u SET u.email=eMail WHERE u.id=userId;
END//
DELIMITER ;


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

DROP PROCEDURE IF EXISTS `removeBannedOrg`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeBannedOrg`(IN `orgId` INT)
BEGIN
  DELETE FROM BannedOrganisations WHERE org_id=orgId;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `removeBannedUser`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeBannedUser`(IN `userId` INT)
BEGIN
  DELETE FROM BannedUsers WHERE user_id = userId;
END//
DELIMITER ;


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

DROP PROCEDURE IF EXISTS `removeUserBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeUserBadge`(IN `uID` INT, IN `bID` INT)
BEGIN
  set @owner = null;
  select b.owner_id into @owner from Badges b where b.id=bID;
        if @owner is not null  or bID in(6,7,8,10,11,12,13) then
            DELETE FROM UserBadges
            WHERE user_id=uID
            AND badge_id=bID;
            select 1 as result;
        else 
            select 0 as result;
        end if;
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `removeUserNotification`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeUserNotification`(IN `userId` INT, IN `taskId` INT)
    COMMENT 'Remove a task from the Users notification list'
BEGIN
  if EXISTS(  SELECT *
                  FROM UserNotifications
                  WHERE user_id = userId
                  AND task_id = taskId) then
    DELETE FROM UserNotifications WHERE user_id=userId AND task_id =taskId;
    select 1 as 'result';
  else
  select 0 as 'result';
  end if;
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `removeUserTag`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeUserTag`(IN `id` INT, IN `tagID` INT)
    COMMENT 'unsubscripse a user for the given tag'
BEGIN
  if EXISTS(  SELECT user_id, tag_id
                  FROM UserTags
                  WHERE user_id = id
                  AND tag_id = tagID) then
    DELETE FROM UserTags WHERE user_id=id AND tag_id =tagID;
    select 1 as 'result';
  else
  select 0 as 'result';
  end if;
END//
DELIMITER ;


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


DROP PROCEDURE IF EXISTS `getUserTaskScoresUpdatedTime`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTaskScoresUpdatedTime`()
BEGIN
    SELECT unix_epoch FROM `UserTaskScoresUpdatedTime` WHERE id=1;
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `recordUserTaskScoresUpdatedTime`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `recordUserTaskScoresUpdatedTime`(IN `unixEpochIn` BIGINT)
BEGIN
    REPLACE INTO `UserTaskScoresUpdatedTime` (`id`, `unix_epoch`) VALUES (1, unixEpochIn);
    select 1 as 'result';
END//
DELIMITER ;


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


DROP PROCEDURE IF EXISTS `setTaskStatus`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `setTaskStatus`(IN `tID` INT, IN `sID` INT)
BEGIN
  update Tasks
    set Tasks.`task-status_id`=sID
    where Tasks.id=tID;

  IF sID=3 THEN
    call reset_project_complete(tID);
  END IF;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getTaskStatus`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskStatus`(IN `tID` BIGINT)
BEGIN
  SELECT * FROM Tasks WHERE id=tID;
END//
DELIMITER ;

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
CREATE DEFINER=`root`@`localhost` PROCEDURE `submitTaskReview`(IN projectId INT, IN taskId INT, IN userId INT, IN correction INT, IN gram INT, IN spell INT, IN consis INT, IN reviseTaskId INT, IN comm VARCHAR(8192))
BEGIN
    IF NOT EXISTS (SELECT 1 
                    FROM TaskReviews
                    WHERE task_id = taskId
                    AND user_id = userId
                    AND project_id = projectId) then
        INSERT INTO TaskReviews (project_id, task_id, user_id, corrections, grammar, spelling, consistency, revise_task_id, comment)
                         VALUES ( projectId,  taskId,  userId,  correction,    gram,    spell,      consis,   reviseTaskId,    comm);
        SELECT 1 as result;
    else
        SELECT 0 as result;
    end if;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `delete_review`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_review`(IN taskID BIGINT, IN userID INT)
BEGIN
    DELETE FROM TaskReviews WHERE task_id=taskID AND user_id=userID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `tagInsert`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `tagInsert`(IN `name` VARCHAR(50))
BEGIN
  insert into Tags (label) values (name);
  select *  from Tags t where t.id = LAST_INSERT_ID();
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS `taskInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `taskInsertAndUpdate`(IN `id` BIGINT, IN `projectID` INT, IN `name` VARCHAR(128), IN `wordCount` INT, IN `sCode` VARCHAR(3), IN `tCode` VARCHAR(3), IN `taskComment` VARCHAR(4096), IN `sCC` VARCHAR(4), IN `tCC` VARCHAR(4), IN `dLine` DATETIME, IN `taskType` INT, IN `tStatus` INT, IN `pub` bit(1))
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
                if dLine is null then set dLine=DATE_ADD(now(),INTERVAL 14 DAY);end if;

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

                call reset_project_complete(LAST_INSERT_ID());

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

                if dLine is not null
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


DROP PROCEDURE IF EXISTS `taskIsClaimed`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `taskIsClaimed`(IN `tID` INT)
BEGIN
Select exists (SELECT 1 FROM TaskClaims WHERE task_id = tID) as result;
END//
DELIMITER ;


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

    SELECT
        t.project_id,
        t.`language_id-source`,
        t.`language_id-target`,
        t.`country_id-source`,
        t.`country_id-target`,
        IF(t.`task-type_id`=2, 3, 2),
        tc.chunk_number
    INTO
        @projectid,
        @language_source,
        @language_target,
        @country_source,
        @country_target,
        @bl_type_to_delete,
        @chunknumber
    FROM      Tasks       t
    LEFT JOIN TaskChunks tc ON task_id=t.id
    WHERE
    t.id=tID AND
    t.`task-type_id` IN (2, 3);

    SELECT
        MAX(t.id) INTO @bl_id_to_delete
    FROM      Tasks       t
    LEFT JOIN TaskChunks tc ON task_id=t.id
    WHERE
        t.project_id          =@projectid AND
        t.`language_id-source`=@language_source AND
        t.`language_id-target`=@language_target AND
        t.`country_id-source` =@country_source AND
        t.`country_id-target` =@country_target AND
        t.`task-type_id`      =@bl_type_to_delete AND
        (tc.chunk_number      =@chunknumber OR tc.chunk_number IS NULL);

    IF @bl_id_to_delete IS NOT NULL THEN
        DELETE FROM TaskTranslatorBlacklist
        WHERE
            user_id=uID AND
            task_id=@bl_id_to_delete;
    END IF;

    call reset_project_complete(tID);

    select 1 as result;
  else
    select 0 as result;
  end if;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `unClaimTaskMemsource`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `unClaimTaskMemsource`(IN tID BIGINT, IN uID INT, IN userFeedback VARCHAR(4096), IN unclaimByAdmin BIT(1))
BEGIN
  IF EXISTS (SELECT 1 FROM TaskClaims tc WHERE tc.task_id=tID AND tc.user_id=uID) THEN
    START TRANSACTION;
      DELETE FROM TaskClaims WHERE task_id=tID AND user_id=uID;
      INSERT INTO TaskUnclaims (id, task_id, user_id, `unclaim-comment`, `unclaimed-time`) VALUES (NULL, tID, uID, userFeedback, NOW());
      UPDATE Tasks SET `task-status_id`=2 where id = tID;
    COMMIT;

    call reset_project_complete(tID);

    SELECT 1 as result;
  ELSE
    SELECT 0 as result;
  END IF;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `userHasBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userHasBadge`(IN `userID` INT, IN `badgeID` INT)
BEGIN
  Select EXISTS( SELECT 1 FROM UserBadges WHERE user_id = userID AND badge_id = badgeID) as result;
END//
DELIMITER ;


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


DROP PROCEDURE IF EXISTS `userNotificationsInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userNotificationsInsertAndUpdate`(IN `user_id` INT, IN `task_id` INT)
BEGIN
  insert into UserNotifications  (user_id, task_id, `created-time`) values (user_id, task_id, NOW());
    select 1 as "result";
END//
DELIMITER ;


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

DROP PROCEDURE IF EXISTS `userUnTrackProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userUnTrackProject`(IN `pID` INT, IN `uID` INT)
BEGIN
  DELETE FROM UserTrackedProjects WHERE user_id=uID AND Project_id=pID;
  DELETE FROM UserTrackedTasks    WHERE user_id=uID AND task_id IN (SELECT id FROM Tasks WHERE project_id=pID);
  SELECT 1 as result;
END//
DELIMITER ;

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

DROP PROCEDURE IF EXISTS `updateSubscription`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateSubscription`(IN `organisation_id` INT, IN `level` INT, IN `spare` INT, IN `start_date` DATETIME, IN `comment` VARCHAR(255))
BEGIN
    REPLACE INTO Subscriptions (`organisation_id`, `level`, `spare`, `start_date`, `comment`) VALUES (organisation_id, level, spare, start_date, comment);
    INSERT INTO SubscriptionsRecorded (`time_stamp`, `organisation_id`, `level`, `spare`, `start_date`, `comment`) VALUES (NOW(), organisation_id, level, spare, start_date, comment);
    SELECT 1 as result;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getSubscription`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getSubscription`(IN `org_id` INT)
BEGIN
  SELECT `organisation_id`, `level`, `spare`, `start_date`, `comment` FROM Subscriptions WHERE `organisation_id`=org_id;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `number_of_projects_ever`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `number_of_projects_ever`(IN `org_id` INT)
BEGIN
    SET @totalArchivedProjects = 0;
    SET @totalProjects = 0;
    SELECT COUNT(*) INTO @totalArchivedProjects FROM ArchivedProjects WHERE `organisation_id`=org_id;
    SELECT COUNT(*) INTO @totalProjects         FROM Projects         WHERE `organisation_id`=org_id;
    SELECT @totalArchivedProjects+@totalProjects AS result;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `number_of_projects_since_last_donation`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `number_of_projects_since_last_donation`(IN `org_id` INT)
BEGIN
    SET @subscription_start_date = 0;
    SELECT `start_date` INTO @subscription_start_date FROM Subscriptions WHERE `organisation_id`=org_id;

    SET @totalArchivedProjects = 0;
    SET @totalProjects = 0;
    SELECT COUNT(*) INTO @totalArchivedProjects FROM ArchivedProjects WHERE `organisation_id`=org_id AND `created`>@subscription_start_date;
    SELECT COUNT(*) INTO @totalProjects         FROM Projects         WHERE `organisation_id`=org_id AND `created`>@subscription_start_date;
    SELECT @totalArchivedProjects+@totalProjects AS result;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `number_of_projects_since_donation_anniversary`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `number_of_projects_since_donation_anniversary`(IN `org_id` INT)
BEGIN
    SET @subscription_start_date = 0;
    SELECT `start_date` + INTERVAL 1 YEAR INTO @subscription_start_date FROM Subscriptions WHERE `organisation_id`=org_id;

    SET @totalArchivedProjects = 0;
    SET @totalProjects = 0;
    SELECT COUNT(*) INTO @totalArchivedProjects FROM ArchivedProjects WHERE `organisation_id`=org_id AND `created`>@subscription_start_date;
    SELECT COUNT(*) INTO @totalProjects         FROM Projects         WHERE `organisation_id`=org_id AND `created`>@subscription_start_date;
    SELECT @totalArchivedProjects+@totalProjects AS result;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `organisationHasQualifiedBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `organisationHasQualifiedBadge`(IN `ownerID` INT)
BEGIN
    SELECT b.id FROM Badges b WHERE b.owner_id=ownerID AND b.title='Qualified';
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `setRestrictedTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `setRestrictedTask`(IN `taskID` INT)
BEGIN
    REPLACE INTO RestrictedTasks (`restricted_task_id`) VALUES (taskID);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `removeRestrictedTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeRestrictedTask`(IN `taskID` INT)
BEGIN
    DELETE FROM RestrictedTasks WHERE restricted_task_id=taskID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getRestrictedTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getRestrictedTask`(IN `taskID` INT)
BEGIN
    SELECT restricted_task_id FROM RestrictedTasks WHERE restricted_task_id=taskID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `isUserRestrictedFromTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `isUserRestrictedFromTask`(IN `taskID` INT, IN `userID` INT)
BEGIN
    IF EXISTS (
        SELECT 1
        FROM Admins
        WHERE
            user_id=userID AND
            organisation_id IS NULL
    ) THEN
        SELECT 0 AS result;

    ELSEIF EXISTS (
        SELECT 1
        FROM Tasks                t
        JOIN Projects             p ON t.project_id=p.id
        JOIN OrganisationMembers om ON p.organisation_id=om.organisation_id
        WHERE
            t.id=taskID AND
            om.user_id=userID
    ) THEN
        SELECT 0 AS result;

    ELSEIF EXISTS (
        SELECT 1
        FROM Tasks                t
        JOIN Projects             p ON t.project_id=p.id
        JOIN Admins              oa ON p.organisation_id=oa.organisation_id
        WHERE
            t.id=taskID AND
            oa.user_id=userID
    ) THEN
        SELECT 0 AS result;

    ELSEIF EXISTS (
        SELECT t.id
        FROM Tasks            t
        JOIN RestrictedTasks  r ON t.id=r.restricted_task_id
        JOIN Projects         p ON t.project_id=p.id
        JOIN Badges           b ON p.organisation_id=b.owner_id AND b.title='Qualified'
        LEFT JOIN UserBadges ub ON b.id=ub.badge_id AND ub.user_id=userID
        WHERE
            t.id=taskID AND
            ub.badge_id IS NULL
    ) THEN
        SELECT 1 AS result;

    ELSEIF NOT EXISTS (
        SELECT t.id
        FROM Tasks t
        JOIN RequiredTaskQualificationLevels tq ON t.id=tq.task_id
        JOIN UserQualifiedPairs uqp ON
            uqp.user_id=userID AND
            t.`language_id-source`=uqp.language_id_source AND
            t.`language_id-target`=uqp.language_id_target
        WHERE
            t.id=taskID AND
            tq.required_qualification_level<=uqp.qualification_level
    ) THEN
        SELECT 1 AS result;

    ELSE
    SELECT 0 AS result;

    END IF;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `isUserRestrictedFromTaskButAllowTranslatorToDownload`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `isUserRestrictedFromTaskButAllowTranslatorToDownload`(IN `taskID` INT, IN `userID` INT)
BEGIN
    IF EXISTS (
        SELECT 1
        FROM Admins
        WHERE
            user_id=userID AND
            organisation_id IS NULL
    ) THEN
        SELECT 0 AS result;

    ELSEIF EXISTS (
        SELECT 1
        FROM Tasks                t
        JOIN Projects             p ON t.project_id=p.id
        JOIN OrganisationMembers om ON p.organisation_id=om.organisation_id
        WHERE
            t.id=taskID AND
            om.user_id=userID
    ) THEN
        SELECT 0 AS result;

    ELSEIF EXISTS (
        SELECT 1
        FROM Tasks                t
        JOIN Projects             p ON t.project_id=p.id
        JOIN Admins              oa ON p.organisation_id=oa.organisation_id
        WHERE
            t.id=taskID AND
            oa.user_id=userID
    ) THEN
        SELECT 0 AS result;

    ELSEIF EXISTS (
        SELECT t.id
        FROM Tasks            t
        JOIN RestrictedTasks  r ON t.id=r.restricted_task_id
        JOIN Projects         p ON t.project_id=p.id
        JOIN Badges           b ON p.organisation_id=b.owner_id AND b.title='Qualified'
        LEFT JOIN UserBadges ub ON b.id=ub.badge_id AND ub.user_id=userID
        WHERE
            t.id=taskID AND
            ub.badge_id IS NULL
    ) THEN
        SELECT 1 AS result;

    ELSEIF EXISTS (
        SELECT 1
        FROM Tasks  t
        JOIN Tasks t2 ON t.project_id=t2.project_id AND
                         t.`language_id-source`=t2.`language_id-source` AND
                         t.`language_id-target`=t2.`language_id-target` AND
                         t.`country_id-source` =t2.`country_id-source`  AND
                         t.`country_id-target` =t2.`country_id-target`  AND
                         t2.`task-type_id`=2
        JOIN      TaskClaims tcl ON tcl.user_id=userID AND t2.id=tcl.task_id
        LEFT JOIN TaskChunks  tc ON t.id=tc.task_id
        WHERE
            t.id=taskID AND
            t.`task-type_id`=3 AND
            tc.task_id IS NULL
    ) THEN
        SELECT 0 AS result;

    ELSEIF EXISTS (
        SELECT 1
        FROM MatecatLanguagePairs lp
        JOIN TaskChunks           tc ON lp.matecat_id_job=tc.matecat_id_job
        JOIN TaskClaims          tcl ON tcl.user_id=userID AND tc.task_id=tcl.task_id
        WHERE
            lp.task_id=taskID
    ) THEN
        SELECT 0 AS result;

    ELSEIF NOT EXISTS (
        SELECT t.id
        FROM Tasks t
        JOIN RequiredTaskQualificationLevels tq ON t.id=tq.task_id
        JOIN UserQualifiedPairs uqp ON
            uqp.user_id=userID AND
            t.`language_id-source`=uqp.language_id_source AND
            t.`language_id-target`=uqp.language_id_target
        WHERE
            t.id=taskID AND
            tq.required_qualification_level<=uqp.qualification_level
    ) THEN
        SELECT 1 AS result;

    ELSE
    SELECT 0 AS result;

    END IF;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `isUserRestrictedFromProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `isUserRestrictedFromProject`(IN `projectID` INT, IN `userID` INT)
BEGIN
    IF EXISTS (
        SELECT 1
        FROM Admins
        WHERE
            user_id=userID AND
            organisation_id IS NULL
    ) THEN
        SELECT 0 AS result;

    ELSEIF EXISTS (
        SELECT 1
        FROM Projects             p
        JOIN OrganisationMembers om ON p.organisation_id=om.organisation_id
        WHERE
            p.id=projectID AND
            om.user_id=userID
    ) THEN
        SELECT 0 AS result;

    ELSEIF EXISTS (
        SELECT 1
        FROM Projects             p
        JOIN Admins              oa ON p.organisation_id=oa.organisation_id
        WHERE
            p.id=projectID AND
            oa.user_id=userID
    ) THEN
        SELECT 0 AS result;

    ELSEIF EXISTS (
        SELECT t.id
        FROM      Tasks                            t
        JOIN      Projects                         p ON t.project_id=p.id
        JOIN      RequiredTaskQualificationLevels tq ON t.id=tq.task_id
        LEFT JOIN Badges                           b ON p.organisation_id=b.owner_id AND b.title='Qualified'
        LEFT JOIN RestrictedTasks                  r ON t.id=r.restricted_task_id
        LEFT JOIN UserQualifiedPairs             uqp ON
            uqp.user_id=userID AND
            t.`language_id-source`=uqp.language_id_source AND
            t.`language_id-target`=uqp.language_id_target
        WHERE
            t.project_id=projectID AND
            t.published=1 AND
            NOT EXISTS (SELECT 1 FROM TaskTranslatorBlacklist tb WHERE tb.user_id=userID AND tb.task_id=t.id) AND
            ((uqp.user_id IS NOT NULL AND tq.required_qualification_level<=uqp.qualification_level)) AND
            (
                r.restricted_task_id IS NULL OR
                b.id IS NULL OR
                b.id IN (SELECT ub.badge_id FROM UserBadges ub WHERE ub.user_id=userID)
            )
        GROUP BY t.id
    ) THEN
        SELECT 0 AS result;

    ELSE
        SELECT 1 AS result;

    END IF;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getUsers`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUsers`()
BEGIN
    SELECT
        u.id,
        u.`display-name` as display_name,
        u.email,
        u.biography,
        u.`created-time` as created_time,
        l.`en-name` AS native_language,
        c.`en-name` AS native_country,
        i.city,
        i.country,
        i.`first-name` AS first_name,
        i.`last-name` AS last_name,
        MAX(IF(tau.user_id IS NOT NULL, 'Yes', '')) AS terms,
        MAX(IF( ad.user_id IS NOT NULL, 'Yes', '')) AS admin
    FROM Users u
    LEFT JOIN UserPersonalInformation i ON u.id=i.user_id
    LEFT JOIN Countries c ON u.country_id=c.id
    LEFT JOIN Languages l ON u.language_id=l.id
    LEFT JOIN TermsAcceptedUsers tau ON u.id=tau.user_id
    LEFT JOIN Admins              ad ON u.id=ad.user_id
    GROUP BY u.id
    ORDER BY u.id DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `list_qualified_translators`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `list_qualified_translators`(IN `taskID` INT)
BEGIN
        SELECT DISTINCT
            uqp.user_id,
            CONCAT(u.email, ' (', IFNULL(i.`first-name`, ''), ' ', IFNULL(i.`last-name`, ''), ')') as name
        FROM Tasks t
        JOIN RequiredTaskQualificationLevels tq ON t.id=tq.task_id
        JOIN UserQualifiedPairs uqp ON
            t.`language_id-source`=uqp.language_id_source AND
            t.`language_id-target`=uqp.language_id_target AND
            tq.required_qualification_level<=uqp.qualification_level
        JOIN Users u ON uqp.user_id=u.id
        LEFT JOIN UserPersonalInformation i ON u.id=i.user_id
        WHERE
            t.id=taskID
        ORDER BY u.email;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `active_now`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `active_now`()
BEGIN
    SELECT
        u.id AS user_id,
        u.`display-name` AS display_name,
        u.email,
        t.title AS task_title,
        t.id AS task_id,
        p.title AS project_title,
        p.id AS project_id
    FROM Projects    p
    JOIN Tasks       t ON p.id=t.project_id
    JOIN TaskClaims tc ON t.id=tc.task_id
    JOIN Users       u ON tc.user_id=u.id
    WHERE t.`task-status_id`=3
    ORDER BY t.id;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `active_now_matecat`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `active_now_matecat`()
BEGIN
    SELECT
        u.id AS user_id,
        u.`display-name` AS display_name,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`, '') AS last_name,
        t.title AS task_title,
        t.id AS task_id,
        t.`word-count` AS word_count,
        t.`created-time` AS created_time,
        t.deadline,
        t.`task-type_id` AS task_type,
        CASE
            WHEN t.`task-type_id`=1 THEN 'Segmentation'
            WHEN t.`task-type_id`=2 THEN 'Translation'
            WHEN t.`task-type_id`=3 THEN 'Proofreading'
            WHEN t.`task-type_id`=4 THEN 'Desegmentation'
        END
        AS task_type_text,
        IFNULL(lp.matecat_langpair,        '') AS matecat_langpair_or_blank,
        IFNULL(lp.matecat_id_job,           0) AS matecat_id_job_or_zero,
        IFNULL(lp.matecat_id_job_password, '') AS matecat_id_job_password_or_blank,
        IFNULL(lp.matecat_id_file,          0) AS matecat_id_file_or_zero,
        CONCAT(l.code, '|', l2.code) AS language_pair,
        o.id AS org_id,
        o.name AS org_name,
        p.title AS project_title,
        p.id AS project_id
    FROM Projects    p
    JOIN Organisations o ON p.organisation_id=o.id
    JOIN Tasks       t ON p.id=t.project_id
    JOIN TaskClaims tc ON t.id=tc.task_id
    JOIN Users       u ON tc.user_id=u.id
    JOIN UserPersonalInformation    i ON u.id=i.user_id
    JOIN Languages   l ON t.`language_id-source`=l.id
    JOIN Languages  l2 ON t.`language_id-target`=l2.id
    LEFT JOIN MatecatLanguagePairs lp ON t.id=lp.task_id
    WHERE t.`task-status_id`=3
    ORDER BY o.name, t.title, lp.matecat_langpair, t.`task-type_id`;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `testing_center`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `testing_center`()
BEGIN
    SELECT
        t.title AS task_title,
        t.id    AS task_id,
        p.id    AS project_id,
        p.title AS project_title,
        IF(t.`task-type_id`=2, 'Translation', 'Revising')               AS task_type,
        CASE
            WHEN t.`task-status_id`=1 THEN 'Waiting'
            WHEN t.`task-status_id`=2 THEN 'Pending'
            WHEN t.`task-status_id`=3 THEN 'In Progress'
            WHEN t.`task-status_id`=4 THEN 'Complete'
        END                                                             AS task_status,
        t.`created-time`                                                AS created,
        t.deadline,
        CONCAT(tcp.language_code_source, '|', tcp.language_code_target) AS language_pair,
        IFNULL(u.email, '')                                             AS user_email,
        IFNULL(u.`display-name`, '')                                    AS display_name,
        IFNULL(u.id, '')                                                AS user_id,
        CASE
            WHEN uqp.qualification_level=1 THEN ''
            WHEN uqp.qualification_level=2 THEN 'Verified'
            WHEN uqp.qualification_level=3 THEN 'Senior'
        END                                                             AS level,
        IFNULL(tr.corrections,        '')                               AS accuracy,
        IFNULL(tr.grammar,            '')                               AS fluency,
        IFNULL(tr.spelling,           '')                               AS terminology,
        IFNULL(tr.consistency   % 10, '')                               AS style,
        IFNULL(tr.consistency DIV 10, '')                               AS design,
        IFNULL(tr.comment,            '')                               AS comment,
        tcp.proofreading_task_id,
        CASE
            WHEN prooft.`task-status_id`=1 THEN 'Waiting'
            WHEN prooft.`task-status_id`=2 THEN 'Pending'
            WHEN prooft.`task-status_id`=3 THEN 'In Progress'
            WHEN prooft.`task-status_id`=4 THEN 'Complete'
        END                                                             AS proofreading_task_status,
        IFNULL(proofu.email, '')                                        AS proofreading_email
    FROM      Projects                p
    JOIN      PrivateTMKeys         tmk ON p.id=tmk.project_id AND tmk.private_tm_key='new'
    JOIN      Tasks                   t ON p.id=t.project_id
    LEFT JOIN TaskClaims             tc ON t.id=tc.task_id
    LEFT JOIN Users                   u ON tc.user_id=u.id
    LEFT JOIN TaskReviews            tr ON t.id=tr.task_id
    LEFT JOIN TestingCenterProjects tcp ON p.id=tcp.project_id
    LEFT JOIN Tasks              prooft ON tcp.proofreading_task_id=prooft.id
    LEFT JOIN TaskClaims        prooftc ON tcp.proofreading_task_id=prooftc.task_id
    LEFT JOIN Users              proofu ON prooftc.user_id=proofu.id
    LEFT JOIN UserQualifiedPairs    uqp ON tc.user_id=uqp.user_id AND tcp.language_code_source=uqp.language_code_source AND tcp.language_code_target=uqp.language_code_target
    WHERE
        t.`task-type_id`=2
    ORDER BY t.id DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insert_testing_center_project`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_testing_center_project`(IN uID INT, IN pID INT, IN tID BIGINT, IN ptID BIGINT, IN pIDtoCopy INT, IN sourceCode VARCHAR(3), IN targetCode VARCHAR(3))
BEGIN
    INSERT INTO TestingCenterProjects
               (user_id,  project_id,  translation_task_id,  proofreading_task_id,  project_to_copy_id,  language_code_source,  language_code_target)
        VALUES (    uID,         pID,                  tID,                  ptID,           pIDtoCopy,            sourceCode,            targetCode);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_testing_center_projects`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_testing_center_projects`(IN uID INT)
BEGIN
    SELECT * FROM TestingCenterProjects WHERE user_id=uID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `late_matecat`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `late_matecat`()
BEGIN
    SELECT
        u.id AS user_id,
        u.`display-name` AS display_name,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`, '') AS last_name,
        t.title AS task_title,
        t.id AS task_id,
        t.`word-count` AS word_count,
        t.`created-time` AS created_time,
        t.deadline,
        IF(NOW()>t.deadline, 1, 0) AS red,
        t.`task-type_id` AS task_type,
        CASE
            WHEN t.`task-type_id`=1 THEN 'Segmentation'
            WHEN t.`task-type_id`=2 THEN 'Translation'
            WHEN t.`task-type_id`=3 THEN 'Revising'
            WHEN t.`task-type_id`=4 THEN 'Desegmentation'
        END
        AS task_type_text,
        IFNULL(tc.`claimed-time`, '') AS claimed_time,
        IFNULL(lp.matecat_langpair,        '') AS matecat_langpair_or_blank,
        IFNULL(lp.matecat_id_job,           0) AS matecat_id_job_or_zero,
        IFNULL(lp.matecat_id_job_password, '') AS matecat_id_job_password_or_blank,
        IFNULL(lp.matecat_id_file,          0) AS matecat_id_file_or_zero,
        CONCAT(l.code, '|', l2.code)           AS language_pair,
        o.id AS org_id,
        o.name AS org_name,
        p.title AS project_title,
        p.id AS project_id
    FROM Projects    p
    JOIN Organisations o ON p.organisation_id=o.id
    JOIN Tasks       t ON p.id=t.project_id
    JOIN Languages   l ON t.`language_id-source`=l.id
    JOIN Languages  l2 ON t.`language_id-target`=l2.id
    LEFT JOIN TaskClaims              tc ON t.id=tc.task_id
    LEFT JOIN Users                    u ON tc.user_id=u.id
    LEFT JOIN UserPersonalInformation  i ON u.id=i.user_id
    LEFT JOIN MatecatLanguagePairs    lp ON t.id=lp.task_id
    WHERE
        (t.`task-status_id`=3 OR t.`task-status_id`=2) AND
        (t.`created-time` > NOW() - INTERVAL 3 MONTH) AND
        NOW() > t.deadline - INTERVAL 1 week
    ORDER BY o.name, t.title, lp.matecat_langpair, CONCAT(l.code, '|', l2.code), t.`task-type_id`;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `complete_matecat`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `complete_matecat`()
BEGIN
    SELECT
        u.id AS user_id,
        u.`display-name` AS display_name,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`, '') AS last_name,
        t.title AS task_title,
        t.id AS task_id,
        t.`word-count` AS word_count,
        t.`created-time` AS created_time,
        t.deadline,
        t.`task-type_id` AS task_type,
        CASE
            WHEN t.`task-type_id`=1 THEN 'Segmentation'
            WHEN t.`task-type_id`=2 THEN 'Translation'
            WHEN t.`task-type_id`=3 THEN 'Proofreading'
            WHEN t.`task-type_id`=4 THEN 'Desegmentation'
        END
        AS task_type_text,
        tc.`claimed-time` AS claimed_time,
        IFNULL(lp.matecat_langpair,        '') AS matecat_langpair_or_blank,
        IFNULL(lp.matecat_id_job,           0) AS matecat_id_job_or_zero,
        IFNULL(lp.matecat_id_job_password, '') AS matecat_id_job_password_or_blank,
        IFNULL(lp.matecat_id_file,          0) AS matecat_id_file_or_zero,
        CONCAT(l.code, '|', l2.code) AS language_pair,
        o.id AS org_id,
        o.name AS org_name,
        p.title AS project_title,
        p.id AS project_id
    FROM Projects    p
    JOIN Organisations o ON p.organisation_id=o.id
    JOIN Tasks       t ON p.id=t.project_id
    JOIN TaskClaims tc ON t.id=tc.task_id
    JOIN Users       u ON tc.user_id=u.id
    JOIN UserPersonalInformation    i ON u.id=i.user_id
    JOIN Languages   l ON t.`language_id-source`=l.id
    JOIN Languages  l2 ON t.`language_id-target`=l2.id
    LEFT JOIN MatecatLanguagePairs lp ON t.id=lp.task_id
    WHERE t.`task-status_id`=4
    ORDER BY o.name, t.title, lp.matecat_langpair, t.`task-type_id`;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `user_task_reviews`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `user_task_reviews`()
BEGIN
    SELECT
        u.id AS user_id,
        u.`display-name` AS display_name,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`, '') AS last_name,
        CONCAT(l1.code, '-', c1.code, '|', l2.code, '-', c2.code) AS language_pair,
        IF(SUM(consistency<10), FORMAT(SUM(IF(consistency<10, tr.corrections, 0))/SUM(consistency<10), 1), '') AS cor,
        IF(SUM(consistency<10), FORMAT(SUM(IF(consistency<10, tr.grammar,     0))/SUM(consistency<10), 1), '') AS gram,
        IF(SUM(consistency<10), FORMAT(SUM(IF(consistency<10, tr.spelling,    0))/SUM(consistency<10), 1), '') AS spell,
        IF(SUM(consistency<10), FORMAT(SUM(IF(consistency<10, tr.consistency, 0))/SUM(consistency<10), 1), '') AS cons,
        SUM(consistency<10) AS num_legacy,
        IF(SUM(consistency>=10), FORMAT(SUM(IF(consistency>=10, tr.corrections,        0))/SUM(consistency>=10), 1), '') AS accuracy,
        IF(SUM(consistency>=10), FORMAT(SUM(IF(consistency>=10, tr.grammar,            0))/SUM(consistency>=10), 1), '') AS fluency,
        IF(SUM(consistency>=10), FORMAT(SUM(IF(consistency>=10, tr.spelling,           0))/SUM(consistency>=10), 1), '') AS terminology,
        IF(SUM(consistency>=10), FORMAT(SUM(IF(consistency>=10, tr.consistency   % 10, 0))/SUM(consistency>=10), 1), '') AS style,
        IF(SUM(consistency>=10), FORMAT(SUM(IF(consistency>=10, tr.consistency DIV 10, 0))/SUM(consistency>=10), 1), '') AS design,
        SUM(consistency>=10) AS num_new,
        COUNT(*)             AS num
    FROM TaskReviews            tr
    JOIN Tasks                   t  ON tr.task_id=t.id
    JOIN Languages              l1 ON t.`language_id-source`=l1.id
    JOIN Languages              l2 ON t.`language_id-target`=l2.id
    JOIN Countries              c1 ON t.`country_id-source`=c1.id
    JOIN Countries              c2 ON t.`country_id-target`=c2.id
    JOIN TaskClaims             tc  ON tr.task_id=tc.task_id
    JOIN Users                   u  ON tc.user_id=u.id
    JOIN UserPersonalInformation i ON u.id=i.user_id
    WHERE t.`task-status_id`=4
    GROUP BY u.id, l1.code, c1.code, l2.code, c2.code
    ORDER BY u.email, l1.code, c1.code, l2.code, c2.code;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `submitted_task_reviews`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `submitted_task_reviews`()
BEGIN
    SELECT
        tcd.complete_date,
        tr.revise_task_id,
        tr.user_id AS reviser_id,
        tc.user_id AS translator_id,
        CONCAT(l1.code, '-', c1.code, '|', l2.code, '-', c2.code) AS language_pair,
        tr.corrections         AS accuracy,
        tr.grammar             AS fluency,
        tr.spelling            AS terminology,
        tr.consistency   % 10  AS style,
        tr.consistency DIV 10  AS design,
        IFNULL(tr.comment, '') AS comment,
        tr.task_id,
        rev.title AS task_title,
        CONCAT(IFNULL(i .`first-name`, ''), ' ', IFNULL(i .`last-name`, ''), ' (', u .email, ')') AS translator_name,
        CONCAT(IFNULL(i2.`first-name`, ''), ' ', IFNULL(i2.`last-name`, ''), ' (', u2.email, ')') AS reviser_name
    FROM TaskReviews             tr
    JOIN Tasks                    t ON tr.task_id=t.id
    JOIN Languages               l1 ON t.`language_id-source`=l1.id
    JOIN Languages               l2 ON t.`language_id-target`=l2.id
    JOIN Countries               c1 ON t.`country_id-source`=c1.id
    JOIN Countries               c2 ON t.`country_id-target`=c2.id
    JOIN TaskClaims              tc ON tr.task_id=tc.task_id
    JOIN Users                    u ON tc.user_id=u.id
    JOIN UserPersonalInformation  i ON u.id=i.user_id
    JOIN Users                   u2 ON tr.user_id=u2.id
    JOIN UserPersonalInformation i2 ON u2.id=i2.user_id
    JOIN Tasks                  rev ON tr.revise_task_id=rev.id
    JOIN TaskCompleteDates      tcd ON tr.revise_task_id=tcd.task_id
    WHERE
        tr.revise_task_id IS NOT NULL AND
        rev.`task-status_id`=4 AND
        tr.consistency>=10
    ORDER BY tcd.complete_date DESC, tr.revise_task_id DESC, tr.user_id DESC
    LIMIT 4000;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `tasks_no_reviews`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `tasks_no_reviews`()
BEGIN
    SELECT
        tcd.complete_date,
        rev.id AS revise_task_id,
        tc.user_id AS reviser_id,
        CONCAT(l1.code, '-', c1.code, '|', l2.code, '-', c2.code) AS language_pair,
        rev.title AS task_title,
        CONCAT(IFNULL(i2.`first-name`, ''), ' ', IFNULL(i2.`last-name`, ''), ' (', u2.email, ')') AS reviser_name
    FROM Tasks                  rev
    LEFT JOIN TaskReviews        tr ON rev.id=tr.revise_task_id
    JOIN TaskCompleteDates      tcd ON rev.id=tcd.task_id
    JOIN Languages               l1 ON rev.`language_id-source`=l1.id
    JOIN Languages               l2 ON rev.`language_id-target`=l2.id
    JOIN Countries               c1 ON rev.`country_id-source`=c1.id
    JOIN Countries               c2 ON rev.`country_id-target`=c2.id
    JOIN TaskClaims              tc ON rev.id=tc.task_id
    JOIN Users                   u2 ON tc.user_id=u2.id
    JOIN UserPersonalInformation i2 ON u2.id=i2.user_id
    WHERE
        tr.revise_task_id IS NULL AND
        rev.`task-status_id`=4 AND
        rev.`task-type_id`=3
    ORDER BY tcd.complete_date DESC, rev.id DESC
    LIMIT 4000;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `project_source_file_scores`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `project_source_file_scores`()
BEGIN
    SELECT
        scores.*,
        IF(MIN(t.`task-status_id`=4), MAX(tcd.complete_date), '') AS completed
    FROM
        (SELECT
            p.id AS project_id,
            p.title,
            p.organisation_id,
            o.name,
            p.created,
            IF(SUM(consistency<10), FORMAT(SUM(IF(consistency<10, tr.corrections, 0))/SUM(consistency<10), 1), '') AS cor,
            IF(SUM(consistency<10), FORMAT(SUM(IF(consistency<10, tr.grammar,     0))/SUM(consistency<10), 1), '') AS gram,
            IF(SUM(consistency<10), FORMAT(SUM(IF(consistency<10, tr.spelling,    0))/SUM(consistency<10), 1), '') AS spell,
            IF(SUM(consistency<10), FORMAT(SUM(IF(consistency<10, tr.consistency, 0))/SUM(consistency<10), 1), '') AS cons,
            GROUP_CONCAT(tr.comment ORDER BY tr.comment SEPARATOR '\\r\\n') AS comments
        FROM TaskReviews  tr
        JOIN Projects      p ON tr.project_id=p.id
        JOIN Organisations o ON p.organisation_id=o.id
        WHERE tr.task_id IS NULL
        GROUP BY tr.project_id
        ORDER BY tr.project_id DESC
        LIMIT 4000
        ) AS scores
    JOIN      Tasks               t ON scores.project_id=t.project_id
    LEFT JOIN TaskCompleteDates tcd ON t.id=tcd.task_id
    GROUP BY scores.project_id
    ORDER BY scores.project_id DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `active_users`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `active_users`()
BEGIN
    SELECT
        u.id AS user_id,
        u.`display-name` AS display_name,
        u.email,
        t.id AS task_id,
        t.title AS task_title,
        t.`created-time` AS created_time,
        IFNULL(tv.user_id, '') AS creator_id,
        IFNULL(u2.email, '') AS creator_email,
        p.id AS project_id,
        p.title AS project_title
    FROM Projects          p
    JOIN Tasks             t ON p.id=t.project_id
    JOIN TaskClaims       tc ON t.id=tc.task_id
    JOIN Users             u ON tc.user_id=u.id
    LEFT JOIN TaskFileVersions tv ON t.id=tv.task_id AND tv.version_id=0
    LEFT JOIN Users            u2 ON tv.user_id=u2.id
    ORDER BY t.id;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `unclaimed_tasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `unclaimed_tasks`()
BEGIN
    SELECT
        t.id AS task_id,
        t.title AS task_title,
        t.`word-count` AS word_count,
        t.`created-time` AS created_time,
        t.deadline,
        t.`task-type_id` AS task_type,
        CASE
            WHEN t.`task-type_id`=1 THEN 'Segmentation'
            WHEN t.`task-type_id`=2 THEN 'Translation'
            WHEN t.`task-type_id`=3 THEN 'Proofreading'
            WHEN t.`task-type_id`=4 THEN 'Desegmentation'
        END
        AS task_type_text,
        CASE
            WHEN tq.required_qualification_level=1 THEN ''
            WHEN tq.required_qualification_level=2 THEN 'Verified'
            WHEN tq.required_qualification_level=3 THEN 'Senior'
        END AS level,
        IFNULL(tv.user_id, '') AS creator_id,
        u2.email AS creator_email,
        IFNULL(lp.matecat_langpair,        '') AS matecat_langpair_or_blank,
        IFNULL(lp.matecat_id_job,           0) AS matecat_id_job_or_zero,
        IFNULL(lp.matecat_id_job_password, '') AS matecat_id_job_password_or_blank,
        IFNULL(lp.matecat_id_file,          0) AS matecat_id_file_or_zero,
        CONCAT(l.code, '|', l2.code) AS language_pair,
        o.id AS org_id,
        o.name AS org_name,
        p.id AS project_id,
        p.title AS project_title,
        IF(t.`task-status_id`=1, 'Waiting for Prerequisites', 'Pending Claim') AS status
    FROM Projects          p
    JOIN Organisations     o ON p.organisation_id=o.id
    JOIN Tasks             t ON p.id=t.project_id
    JOIN Languages   l ON t.`language_id-source`=l.id
    JOIN Languages  l2 ON t.`language_id-target`=l2.id
    JOIN RequiredTaskQualificationLevels tq ON t.id=tq.task_id
    LEFT JOIN MatecatLanguagePairs lp ON t.id=lp.task_id
    LEFT JOIN TaskFileVersions tv ON t.id=tv.task_id AND tv.version_id=0
    LEFT JOIN Users            u2 ON tv.user_id=u2.id
    WHERE t.`task-status_id`<3
    ORDER BY o.name, t.title, lp.matecat_langpair, t.`task-type_id`;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `user_languages`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `user_languages`(IN `languageCode` VARCHAR(3))
BEGIN
(
    SELECT
        u.id AS user_id,
        u.`display-name` AS display_name,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`, '') AS last_name,
        l.code AS language_code,
        l.`en-name` AS language_name,
        c.code AS country_code,
        c.`en-name` AS country_name,
        'Native' AS native_or_secondary,
        '' AS level
    FROM Users     u
    JOIN UserPersonalInformation i ON u.id=i.user_id
    JOIN Languages l ON u.language_id=l.id
    JOIN Countries c ON u.country_id=c.id
    WHERE languageCode IS NULL OR l.code=languageCode
)
UNION
(
    SELECT
        u.id AS user_id,
        u.`display-name` AS display_name,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`, '') AS last_name,
        l.code AS language_code,
        l.`en-name` AS language_name,
        c.code AS country_code,
        c.`en-name` AS country_name,
        'Source' AS native_or_secondary,
        CASE
            WHEN uqp.qualification_level=1 THEN 'Translator'
            WHEN uqp.qualification_level=2 THEN 'Verified Translator'
            WHEN uqp.qualification_level=3 THEN 'Senior Translator'
        END AS level
    FROM Users                   u
    JOIN UserPersonalInformation i ON u.id=i.user_id
    JOIN UserQualifiedPairs    uqp ON u.id=uqp.user_id
    JOIN Languages               l ON uqp.language_id_source=l.id
    JOIN Countries               c ON uqp.country_id_source=c.id
    WHERE languageCode IS NULL OR l.code=languageCode
)
UNION
(
    SELECT
        u.id AS user_id,
        u.`display-name` AS display_name,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`, '') AS last_name,
        l.code AS language_code,
        l.`en-name` AS language_name,
        c.code AS country_code,
        c.`en-name` AS country_name,
        'Target' AS native_or_secondary,
        CASE
            WHEN uqp.qualification_level=1 THEN 'Translator'
            WHEN uqp.qualification_level=2 THEN 'Verified Translator'
            WHEN uqp.qualification_level=3 THEN 'Senior Translator'
        END AS level
    FROM Users                   u
    JOIN UserPersonalInformation i ON u.id=i.user_id
    JOIN UserQualifiedPairs    uqp ON u.id=uqp.user_id
    JOIN Languages               l ON uqp.language_id_target=l.id
    JOIN Countries               c ON uqp.country_id_target=c.id
    WHERE languageCode IS NULL OR l.code=languageCode
)
ORDER BY language_name, country_name, display_name;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `search_users_by_language_pair`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `search_users_by_language_pair`(IN `languageCodeSource` VARCHAR(3), IN `languageCodeTarget` VARCHAR(3))
BEGIN
    SELECT
        u.id AS user_id,
        u.`display-name` AS display_name,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`, '') AS last_name,
        l1.`en-name` AS language_name_source,
        c1.`en-name` AS country_name_source,
        l2.`en-name` AS language_name_target,
        c2.`en-name` AS country_name_target,
        uqp.qualification_level,
        CASE
            WHEN uqp.qualification_level=1 THEN 'Translator'
            WHEN uqp.qualification_level=2 THEN 'Verified Translator'
            WHEN uqp.qualification_level=3 THEN 'Senior Translator'
        END AS level,
        IFNULL(ln.`en-name`, '') AS language_name_native,
        IFNULL(cn.`en-name`, '') AS country_name_native
    FROM Users                   u
    JOIN UserPersonalInformation i ON u.id=i.user_id
    JOIN UserQualifiedPairs    uqp ON u.id=uqp.user_id
    JOIN Languages              l1 ON uqp.language_id_source=l1.id
    JOIN Countries              c1 ON uqp.country_id_source=c1.id
    JOIN Languages              l2 ON uqp.language_id_target=l2.id
    JOIN Countries              c2 ON uqp.country_id_target=c2.id
    LEFT JOIN Languages         ln ON u.language_id=ln.id
    LEFT JOIN Countries         cn ON u.country_id=cn.id
    WHERE
        l1.code=languageCodeSource AND
        l2.code=languageCodeTarget
    ORDER BY u.email;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `community_stats`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `community_stats`()
BEGIN
    SELECT
        u.id,
        u.`display-name` as display_name,
        u.email,
        u.`created-time` as created_time,
        IFNULL(l.`en-name`, '') AS native_language,
        IFNULL(l.code, '') AS native_code,
        IFNULL(c.`en-name`, '') AS native_country,
        IFNULL(i.city, '') AS city,
        IFNULL(i.country, '') AS country,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`, '') AS last_name,
        IFNULL(MAX(ul.`login-time`), '') AS last_accessed
    FROM Users u
    LEFT JOIN UserPersonalInformation i ON u.id=i.user_id
    LEFT JOIN Countries c ON u.country_id=c.id
    LEFT JOIN Languages l ON u.language_id=l.id
    LEFT JOIN UserLogins ul ON u.id=ul.user_id
    GROUP BY u.id
    ORDER BY u.id DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `community_stats_secondary`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `community_stats_secondary`()
BEGIN
    SELECT
        u.id,
        IFNULL(GROUP_CONCAT(l1.code ORDER BY l1.code SEPARATOR ', '), '') AS source_codes,
        IFNULL(GROUP_CONCAT(l1.`en-name` ORDER BY l1.`en-name` SEPARATOR ', '), '') AS source_languages,
        IFNULL(GROUP_CONCAT(l2.code ORDER BY l2.code SEPARATOR ', '), '') AS target_codes,
        IFNULL(GROUP_CONCAT(l2.`en-name` ORDER BY l2.`en-name` SEPARATOR ', '), '') AS target_languages
    FROM Users u
    LEFT JOIN UserQualifiedPairs uqp ON u.id=uqp.user_id
    LEFT JOIN Languages l1 ON uqp.language_id_source=l1.id
    LEFT JOIN Languages l2 ON uqp.language_id_target=l2.id
    GROUP BY u.id;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `community_stats_words`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `community_stats_words`()
BEGIN
    SELECT
        u.id,
        SUM(IF(`task-type_id`=2 AND `task-status_id`=4, t.`word-count`, 0)) AS words_translated,
        SUM(IF(`task-type_id`=3 AND `task-status_id`=4, t.`word-count`, 0)) AS words_proofread
    FROM Users u
    LEFT JOIN TaskClaims tc ON u.id=tc.user_id
    LEFT JOIN Tasks t ON tc.task_id=t.id
    GROUP BY u.id;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `user_task_languages`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `user_task_languages`(IN `languageCode` VARCHAR(3))
BEGIN
    SELECT
        u.id AS user_id,
        u.`display-name` AS display_name,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`, '') AS last_name,
        t.id AS task_id,
        t.title AS task_title,
        CASE
            WHEN t.`task-type_id`=1 THEN 'Segmentation'
            WHEN t.`task-type_id`=2 THEN 'Translation'
            WHEN t.`task-type_id`=3 THEN 'Proofreading'
            WHEN t.`task-type_id`=4 THEN 'Desegmentation'
        END
        AS task_type,
        t.`word-count` AS word_count,
        tc.`claimed-time` AS claimed_time,
        CONCAT(l.code, '-', l2.code) AS language_pair,
        l.`en-name`  AS language_name_source,
        l2.`en-name` AS language_name_target
    FROM Tasks       t
    JOIN TaskClaims tc ON t.id=tc.task_id
    JOIN Languages   l ON t.`language_id-source`=l.id
    JOIN Languages  l2 ON t.`language_id-target`=l2.id
    JOIN Users       u ON tc.user_id=u.id
    JOIN UserPersonalInformation i ON u.id=i.user_id
    WHERE languageCode IS NULL OR l.code=languageCode OR l2.code=languageCode
    ORDER BY language_name_source, language_name_target, display_name, task_title;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `user_words_by_language`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `user_words_by_language`()
BEGIN
    SELECT
        u.id AS user_id,
        u.`display-name` AS display_name,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`,  '') AS last_name,
        MAX(CASE
            WHEN uqp.qualification_level=1 THEN ''
            WHEN uqp.qualification_level=2 THEN 'Verified'
            WHEN uqp.qualification_level=3 THEN 'Senior'
        END) AS level,
        SUM(IF(t.`task-type_id`=2 AND t.`task-status_id`=4, t.`word-count`, 0)) AS words_translated,
        SUM(IF(t.`task-type_id`=3 AND t.`task-status_id`=4, t.`word-count`, 0)) AS words_proofread,
        CONCAT(l1.code, '-', c1.code, '|', l2.code, '-', c2.code) AS language_pair
    FROM Tasks       t
    JOIN TaskClaims tc ON t.id=tc.task_id
    JOIN Users       u ON tc.user_id=u.id
    JOIN UserPersonalInformation i ON u.id=i.user_id
    JOIN UserQualifiedPairs    uqp ON u.id=uqp.user_id
    JOIN Languages  l1 ON t.`language_id-source`=l1.id
    JOIN Languages  l2 ON t.`language_id-target`=l2.id
    JOIN Countries  c1 ON t.`country_id-source` =c1.id
    JOIN Countries  c2 ON t.`country_id-target` =c2.id
    WHERE
        t.`task-status_id`=4 AND
       (t.`task-type_id`=2 OR
        t.`task-type_id`=3)
    GROUP BY u.id, l1.code, c1.code, l2.code, c2.code
    ORDER BY u.email, l1.code, c1.code, l2.code, c2.code;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `language_work_requested`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `language_work_requested`()
BEGIN
    SELECT
        COUNT(*) AS tasks,
        SUM(`word-count`) AS words,
        YEAR(t.`created-time`) AS created,
        CONCAT(l.code, '-', l2.code) AS language_pair
    FROM Tasks t
    JOIN Languages l ON t.`language_id-source`=l.id
    JOIN Languages l2 ON t.`language_id-target`=l2.id
    WHERE
        t.`task-type_id`=2
    GROUP BY CONCAT(l.code, '-', l2.code), YEAR(t.`created-time`)
    ORDER BY CONCAT(l.code, '-', l2.code), YEAR(t.`created-time`) DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `translators_for_language_pairs`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `translators_for_language_pairs`()
BEGIN
    SELECT
        CONCAT(language_code_source, '-', language_code_target) AS pair,
        CASE
            WHEN qualification_level=1 THEN 'Translator'
            WHEN qualification_level=2 THEN 'Verified Translator'
            WHEN qualification_level=3 THEN 'Senior Translator'
        END AS level,
        COUNT(*) AS number
    FROM UserQualifiedPairs
    GROUP BY language_code_source, language_code_target, qualification_level
    ORDER BY language_code_source, language_code_target, qualification_level;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insertWordCountRequestForProjects`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertWordCountRequestForProjects`(IN `pID` INT, IN sourceLanguage VARCHAR(10), IN targetLanguages VARCHAR(100), IN `userWordCount` INT)
BEGIN
    INSERT INTO WordCountRequestForProjects
               (project_id, matecat_id_project, matecat_id_project_pass, source_language, target_languages, user_word_count, matecat_word_count, state)
        VALUES (pID,                         0,                      '',  sourceLanguage,  targetLanguages,   userWordCount,                  0,     0);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insertWordCountRequestForProjectsErrors`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertWordCountRequestForProjectsErrors`(IN `pID` INT, IN statusIN VARCHAR(30), IN messageIN VARCHAR(255))
BEGIN
    INSERT INTO WordCountRequestForProjectsErrors
               (project_id, status,    message)
        VALUES (pID,        statusIN,  messageIN);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `matecat_analyse_status`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `matecat_analyse_status`()
BEGIN
    SELECT
        wc.project_id,
        MIN(wc.matecat_id_project)      AS matecat_id_project,
        MIN(wc.matecat_id_project_pass) AS matecat_id_project_pass,
        MIN(wc.matecat_word_count)      AS matecat_word_count,
        MIN(wc.state)                   AS state,
        MIN(p.title)                    AS title,
        IFNULL(MIN(tv.user_id), '')     AS creator_id,
        IFNULL(MIN(u.email), '')        AS creator_email,
        IFNULL(MIN(wce.status),  '')    AS status,
        IFNULL(MIN(wce.message), '')    AS message
    FROM      WordCountRequestForProjects        wc
    JOIN      Projects                            p ON wc.project_id=p.id
    JOIN      Tasks                               t ON wc.project_id=t.project_id
    LEFT JOIN WordCountRequestForProjectsErrors wce ON wc.project_id=wce.project_id
    LEFT JOIN TaskFileVersions                   tv ON t.id=tv.task_id AND tv.version_id=0 AND t.`task-type_id`=2
    LEFT JOIN Users                               u ON tv.user_id=u.id
    GROUP BY wc.project_id
    ORDER BY project_id DESC
    LIMIT 250;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `list_memsource_projects`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `list_memsource_projects`()
BEGIN
    SELECT
        mp.*,
        p.*,
        IF(mu.user_id IS NOT NULL AND mu.user_id!=99269, mu.user_id, pf.user_id) AS creator_id,
        IF( u.email   IS NOT NULL AND  u.email!='projects@translatorswithoutborders.org', u.email, u2.email) AS creator_email,
        o.name
    FROM      MemsourceProjects mp
    JOIN      Projects           p ON mp.project_id=p.id
    JOIN      Organisations      o ON p.organisation_id=o.id
    LEFT JOIN MemsourceUsers    mu ON mp.owner_uid=memsource_user_uid
    LEFT JOIN Users              u ON mu.user_id=u.id
    LEFT JOIN ProjectFiles      pf ON mp.project_id=pf.project_id
    LEFT JOIN Users             u2 ON pf.user_id=u2.id
    ORDER BY mp.project_id DESC
    LIMIT 250;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `updateWordCountRequestForProjects`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateWordCountRequestForProjects`(IN `pID` INT, IN `matecatID` INT, IN `matecatPW` VARCHAR(50), IN `matecatWordCount` INT, IN `setState` INT)
BEGIN
    UPDATE WordCountRequestForProjects SET matecat_id_project=matecatID, matecat_id_project_pass=matecatPW, matecat_word_count=matecatWordCount, state=setState WHERE project_id=pID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getWordCountRequestForProjects`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getWordCountRequestForProjects`(IN `getState` INT)
BEGIN
    SELECT * FROM WordCountRequestForProjects WHERE state=getState;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getWordCountRequestForProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getWordCountRequestForProject`(IN `projectID` INT)
BEGIN
    SELECT * FROM WordCountRequestForProjects WHERE project_id=projectID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `updateWordCountForProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateWordCountForProject`(IN `pID` INT, IN `matecatWordCount` INT)
BEGIN
    UPDATE Projects SET `word-count`=matecatWordCount WHERE id=pID;
    UPDATE Tasks SET `word-count`=matecatWordCount WHERE project_id=pID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `search_user`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `search_user`(IN `name` VARCHAR(128))
BEGIN
    SELECT
        u.id AS user_id,
        u.email,
        CONCAT(IFNULL(CONCAT(i.`first-name`, ' ', i.`last-name`), ''), ' (', u.`display-name`, ') ') as name
    FROM Users u
    LEFT JOIN UserPersonalInformation i ON u.id=i.user_id
    WHERE
        u.email LIKE CONCAT('%', name, '%') OR
        u.`display-name` LIKE CONCAT('%', name, '%') OR
        i.`first-name` LIKE CONCAT('%', name, '%') OR
        i.`last-name` LIKE CONCAT('%', name, '%')
    ORDER BY u.email
    LIMIT 20;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `search_organisation`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `search_organisation`(IN `orgName` VARCHAR(128))
BEGIN
    SELECT
        o.id AS org_id,
        IFNULL(o.`e-mail`, '') AS email,
        name
    FROM Organisations o
    WHERE
        o.`e-mail` LIKE CONCAT('%', orgName, '%') OR
        o.name LIKE CONCAT('%', orgName, '%')
    ORDER BY o.name
    LIMIT 20;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `search_project`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `search_project`(IN `name` VARCHAR(128))
BEGIN
    SELECT
        p.id AS proj_id,
        p.title AS proj_title,
        IFNULL(t.id, '') AS task_id,
        CONCAT(t.title, ' (', (SELECT l.code FROM Languages l WHERE l.id=t.`language_id-target`), ')') AS task_title
    FROM Projects p
    LEFT JOIN Tasks t ON p.id=t.project_id
    WHERE
        p.title LIKE CONCAT('%', name, '%') OR
        t.title LIKE CONCAT('%', name, '%')
    ORDER BY p.title, t.title, t.id
    LIMIT 20;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insertMatecatLanguagePairs`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertMatecatLanguagePairs`(IN `tID` BIGINT, IN `pID` INT, IN `typeID` INT, IN matecatLangpair VARCHAR(50))
BEGIN
    INSERT INTO MatecatLanguagePairs
               (task_id, project_id, type_id, matecat_langpair, matecat_id_job, matecat_id_job_password, matecat_id_file)
        VALUES (    tID,        pID,  typeID,  matecatLangpair,              0,                      '',               0);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `updateMatecatLanguagePairs`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateMatecatLanguagePairs`(IN `pID` INT, IN `typeID` INT, IN matecatLangpair VARCHAR(50), IN `matecatIdJob` INT, IN `matecatIdJobPW` VARCHAR(50), IN `matecatIdFile` INT)
BEGIN
    UPDATE MatecatLanguagePairs
    SET
        matecat_id_job=matecatIdJob,
        matecat_id_job_password=matecatIdJobPW,
        matecat_id_file=matecatIdFile
   WHERE
       project_id=pID AND
       type_id=typeID AND
       matecat_langpair=matecatLangpair;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getMatecatLanguagePairs`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMatecatLanguagePairs`(IN `tID` BIGINT)
BEGIN
    SELECT * FROM MatecatLanguagePairs WHERE task_id=tID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getMatecatLanguagePairsForProject`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMatecatLanguagePairsForProject`(IN `pID` INT)
BEGIN
    SELECT * FROM MatecatLanguagePairs WHERE project_id=pID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insertTaskChunks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertTaskChunks`(IN `tID` BIGINT, IN `pID` INT, IN `typeID` INT, IN matecatLangpair VARCHAR(50), IN matecatIdJob INT, IN chunkNumber INT, IN chunkPassword VARCHAR(50), IN firstSegment VARCHAR(50))
BEGIN
    INSERT INTO TaskChunks
               (task_id, project_id, type_id, matecat_langpair, matecat_id_job, chunk_number, matecat_id_chunk_password, job_first_segment)
        VALUES (    tID,        pID,  typeID,  matecatLangpair,   matecatIdJob,  chunkNumber,             chunkPassword,      firstSegment);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getTaskChunks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskChunks`(IN `pID` INT)
BEGIN
    SELECT * FROM TaskChunks WHERE project_id=pID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getTaskChunk`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskChunk`(IN `tID` INT)
BEGIN
    SELECT * FROM TaskChunks WHERE task_id=tID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getTaskSubChunks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskSubChunks`(IN `jID` INT)
BEGIN
    SELECT * FROM TaskChunks WHERE matecat_id_job=jID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `is_chunk_or_parent_of_chunk`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `is_chunk_or_parent_of_chunk`(IN `pID` INT, IN `tID` INT)
BEGIN
    SELECT
        lp.task_id,
        tc.task_id AS tc_task_id,
        lp.matecat_id_job
    FROM MatecatLanguagePairs lp
    JOIN TaskChunks tc ON lp.matecat_id_job=tc.matecat_id_job
    WHERE
        lp.project_id=pID AND
        (
            lp.task_id=tID OR
            tc.task_id=tID
        );
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `is_parent_of_chunk`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `is_parent_of_chunk`(IN `pID` INT, IN `tID` INT)
BEGIN
    SELECT
        lp.task_id,
        tc.task_id AS tc_task_id,
        lp.matecat_id_job
    FROM MatecatLanguagePairs lp
    JOIN TaskChunks tc ON lp.matecat_id_job=tc.matecat_id_job
    WHERE
        lp.project_id=pID AND
        lp.task_id=tID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `all_chunked_active_projects`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `all_chunked_active_projects`()
BEGIN
    SELECT
        tc.project_id,
        tc.task_id,
        tc.matecat_id_job,
        tc.type_id,
        tc.matecat_id_chunk_password,
        t.`created-time` AS created,
        t.deadline
    FROM TaskChunks tc
    JOIN Tasks       t ON tc.task_id=t.id
    WHERE
        t.`task-status_id`=3;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getMatchingTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMatchingTask`(IN id_job INT, IN id_chunk_password VARCHAR(50), IN matching_type_id INT)
BEGIN
    SELECT
        t.id,
        t.project_id AS projectId,
        t.title,
        `word-count` AS wordCount,
        (SELECT `en-name` FROM Languages l where l.id = t.`language_id-source`) AS `sourceLanguageName`,
        (SELECT  code     FROM Languages l where l.id = t.`language_id-source`) AS `sourceLanguageCode`,
        (SELECT `en-name` FROM Languages l where l.id = t.`language_id-target`) AS `targetLanguageName`,
        (SELECT  code     FROM Languages l where l.id = t.`language_id-target`) AS `targetLanguageCode`,
        (SELECT `en-name` FROM Countries c where c.id = t.`country_id-source` ) AS `sourceCountryName`,
        (SELECT  code     FROM Countries c where c.id = t.`country_id-source` ) AS `sourceCountryCode`,
        (SELECT `en-name` FROM Countries c where c.id = t.`country_id-target` ) AS `targetCountryName`,
        (SELECT  code     FROM Countries c where c.id = t.`country_id-target` ) AS `targetCountryCode`,
        t.`comment`,
        t.`task-type_id`   AS taskType,
        t.`task-status_id` AS taskStatus,
        t.published,
        t.deadline,
        t.`created-time`   AS createdTime
    FROM Tasks       t
    JOIN TaskChunks tc ON t.id=tc.task_id
    WHERE
        tc.matecat_id_job=id_job AND
        tc.matecat_id_chunk_password=id_chunk_password AND
        tc.type_id=matching_type_id;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getParentTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getParentTask`(IN project INT, IN id_job INT, IN matching_type_id INT)
BEGIN
    SELECT
        t.id,
        t.project_id AS projectId,
        t.title, `word-count` AS wordCount,
        (SELECT `en-name` FROM Languages l where l.id = t.`language_id-source`) AS `sourceLanguageName`,
        (SELECT  code     FROM Languages l where l.id = t.`language_id-source`) AS `sourceLanguageCode`,
        (SELECT `en-name` FROM Languages l where l.id = t.`language_id-target`) AS `targetLanguageName`,
        (SELECT  code     FROM Languages l where l.id = t.`language_id-target`) AS `targetLanguageCode`,
        (SELECT `en-name` FROM Countries c where c.id = t.`country_id-source` ) AS `sourceCountryName`,
        (SELECT  code     FROM Countries c where c.id = t.`country_id-source` ) AS `sourceCountryCode`,
        (SELECT `en-name` FROM Countries c where c.id = t.`country_id-target` ) AS `targetCountryName`,
        (SELECT  code     FROM Countries c where c.id = t.`country_id-target` ) AS `targetCountryCode`,
        t.`comment`,
        t.`task-type_id`   AS taskType,
        t.`task-status_id` AS taskStatus,
        t.published,
        t.deadline,
        t.`created-time`   AS createdTime
    FROM Tasks                 t
    JOIN MatecatLanguagePairs lp ON t.id=lp.task_id
    WHERE
        lp.project_id=project AND
        lp.matecat_id_job=id_job AND
        lp.type_id=matching_type_id;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_parent_transation_task`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_parent_transation_task`(IN tID INT)
BEGIN
    SELECT
        lp.task_id
    FROM TaskChunks tc
    JOIN MatecatLanguagePairs lp ON tc.project_id=lp.project_id
    WHERE
        tc.task_id=tID AND
        tc.matecat_id_job=lp.matecat_id_job AND
        lp.type_id=2;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getOtherPendingChunks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getOtherPendingChunks`(IN `tID` BIGINT, IN `typeID` INT, IN `matecatIdJob` INT)
BEGIN
    SELECT tc.task_id
    FROM TaskChunks tc
    JOIN Tasks t ON tc.task_id=t.id
    WHERE
        tc.matecat_id_job=matecatIdJob AND
        tc.type_id=typeID AND
        t.`task-status_id`=2 AND
        tc.task_id!=tID
    ORDER BY chunk_number ASC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getOtherPendingMemsourceJobs`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getOtherPendingMemsourceJobs`(IN tID BIGINT, IN typeID INT, IN pID INT, IN iID VARCHAR(30))
BEGIN
    SELECT mt.task_id
    FROM Tasks           t
    JOIN MemsourceTasks mt ON t.id=mt.task_id
    WHERE
        t.project_id=pID AND
        t.`task-status_id`=2 AND
        t.`task-type_id`=typeID AND
        SUBSTRING_INDEX(mt.internalId, ".", 1)=SUBSTRING_INDEX(iID, ".", 1) AND
        mt.task_id!=tID
    ORDER BY mt.internalId ASC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insertMatecatRecordedJobStatus`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertMatecatRecordedJobStatus`(IN jobID INT, IN jobPassword VARCHAR(50), IN jobStatus VARCHAR(20))
BEGIN
    REPLACE INTO MatecatRecordedJobStatus (matecat_id_job, matecat_id_job_password, job_status) VALUES (jobID, jobPassword, jobStatus);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getMatecatRecordedJobStatus`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMatecatRecordedJobStatus`(IN jobID INT, IN jobPassword VARCHAR(50))
BEGIN
    SELECT job_status FROM MatecatRecordedJobStatus WHERE matecat_id_job=jobID AND matecat_id_job_password=jobPassword;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_task_complete_date`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_task_complete_date`(IN `tID` INT)
BEGIN
    REPLACE INTO TaskCompleteDates (task_id, complete_date) VALUES (tID, now());
    call update_project_complete_date(tID);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_task_complete_date`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_task_complete_date`(IN `tID` INT)
BEGIN
    SELECT * FROM TaskCompleteDates WHERE task_id=tID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `all_orgs`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `all_orgs`()
BEGIN
    SELECT o.id, o.name, IFNULL(o.`home-page`, '') as homepage, IFNULL(o.`e-mail`, '') AS email
    FROM Organisations o
    ORDER BY o.name;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `all_org_admins`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `all_org_admins`()
BEGIN
    SELECT u.id, u.email, a.organisation_id
    FROM Admins a
    JOIN Users u ON a.user_id=u.id
    WHERE a.organisation_id IS NOT NULL
    ORDER BY u.email;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `all_org_members`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `all_org_members`()
BEGIN
    SELECT u.id, u.email, om.organisation_id
    FROM OrganisationMembers om
    JOIN Users u ON om.user_id=u.id
    ORDER BY u.email;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `org_stats_words`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `org_stats_words`()
BEGIN
    SELECT
        p.organisation_id,
        YEAR(tc.`claimed-time`) AS year,
        SUM(IF(`task-type_id`=2, t.`word-count`, 0)) AS words_translated,
        SUM(IF(`task-type_id`=3, t.`word-count`, 0)) AS words_proofread
    FROM Projects p
    JOIN Tasks t ON p.id=t.project_id
    JOIN TaskClaims tc ON t.id=tc.task_id
    LEFT JOIN TaskChunks c ON t.id=c.task_id
    WHERE
        (t.`task-type_id`=2 OR t.`task-type_id`=3) AND
        t.`task-status_id`=4 AND
        c.task_id IS NULL
    GROUP BY p.organisation_id, YEAR(tc.`claimed-time`);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `org_stats_words_req`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `org_stats_words_req`()
BEGIN
    SELECT
        p.organisation_id,
        YEAR(t.`created-time`) AS year,
        SUM(IF(`task-type_id`=2, t.`word-count`, 0)) AS words_translated_req,
        SUM(IF(`task-type_id`=3, t.`word-count`, 0)) AS words_proofread_req
    FROM Projects p
    JOIN Tasks t ON p.id=t.project_id
    LEFT JOIN TaskChunks c ON t.id=c.task_id
    WHERE
        (t.`task-type_id`=2 OR t.`task-type_id`=3) AND
        c.task_id IS NULL
    GROUP BY p.organisation_id, YEAR(t.`created-time`);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `org_stats_languages`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `org_stats_languages`()
BEGIN
    SELECT DISTINCT
        p.organisation_id,
        YEAR(tc.`claimed-time`) AS year,
        CONCAT(l.code, '-', l2.code) AS language_pair
    FROM Projects p
    JOIN Tasks t ON p.id=t.project_id
    JOIN Languages l ON t.`language_id-source`=l.id
    JOIN Languages l2 ON t.`language_id-target`=l2.id
    JOIN TaskClaims tc ON t.id=tc.task_id
    WHERE
        (t.`task-type_id`=2 OR t.`task-type_id`=3) AND
        t.`task-status_id`=4
    ORDER BY CONCAT(l.code, '-', l2.code);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `users_active`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `users_active`()
BEGIN
SELECT COUNT(DISTINCT user_id) AS users_active, CONCAT(SUBSTRING(MONTHNAME(tc.`claimed-time`),1, 3), '-', SUBSTRING(YEAR(tc.`claimed-time`), 3)) AS month
FROM TaskClaims tc
GROUP BY YEAR(tc.`claimed-time`), MONTH(tc.`claimed-time`)
ORDER BY YEAR(tc.`claimed-time`) DESC, MONTH(tc.`claimed-time`) DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `users_signed_up`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `users_signed_up`()
BEGIN
SELECT COUNT(*) AS users_signed_up,  CONCAT(SUBSTRING(MONTHNAME(u.`created-time`),1, 3), '-', SUBSTRING(YEAR(u.`created-time`), 3)) AS month
FROM Users u
GROUP BY YEAR(u.`created-time`), MONTH(u.`created-time`)
ORDER BY YEAR(u.`created-time`) ASC, MONTH(u.`created-time`) ASC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `new_tasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `new_tasks`()
BEGIN
SELECT COUNT(*) AS new_tasks,  CONCAT(SUBSTRING(MONTHNAME(t.`created-time`),1, 3), '-', SUBSTRING(YEAR(t.`created-time`), 3)) AS month
FROM Tasks t
GROUP BY YEAR(t.`created-time`), MONTH(t.`created-time`)
ORDER BY YEAR(t.`created-time`) DESC, MONTH(t.`created-time`) DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `average_time_to_assign`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `average_time_to_assign`()
BEGIN
SELECT ROUND(AVG(UNIX_TIMESTAMP(tc.`claimed-time`) - UNIX_TIMESTAMP(t.`created-time`))/(60.*60.)) AS average_time_to_assign, CONCAT(SUBSTRING(MONTHNAME(tc.`claimed-time`),1, 3), '-', SUBSTRING(YEAR(tc.`claimed-time`), 3)) AS month
FROM Tasks t
JOIN TaskClaims tc ON t.id=tc.task_id
GROUP BY YEAR(tc.`claimed-time`), MONTH(tc.`claimed-time`)
ORDER BY YEAR(tc.`claimed-time`) DESC, MONTH(tc.`claimed-time`) DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `average_time_to_turnaround`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `average_time_to_turnaround`()
BEGIN
SELECT
    ROUND(AVG(UNIX_TIMESTAMP(tcd.complete_date) - UNIX_TIMESTAMP(t.`created-time`))/(60.*60.)) AS average_time_to_turnaround,
    MAX(CONCAT(SUBSTRING(MONTHNAME(tcd.complete_date), 1, 3), '-', SUBSTRING(YEAR(tcd.complete_date), 3))) AS month
FROM Tasks               t
JOIN TaskCompleteDates tcd ON t.id=tcd.task_id
WHERE t.`task-status_id`=4
GROUP BY YEAR(tcd.complete_date), MONTH(tcd.complete_date)
ORDER BY YEAR(tcd.complete_date) DESC, MONTH(tcd.complete_date) DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `users_who_logged_in`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `users_who_logged_in`()
BEGIN
SELECT COUNT(u.user_id) AS all_logins, COUNT(DISTINCT u.user_id) AS distinct_logins, CONCAT(SUBSTRING(MONTHNAME(u.`login-time`),1, 3), '-', SUBSTRING(YEAR(u.`login-time`), 3)) AS month
FROM UserLogins u
WHERE u.success=1
GROUP BY YEAR(u.`login-time`), MONTH(u.`login-time`)
ORDER BY YEAR(u.`login-time`) ASC, MONTH(u.`login-time`) ASC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `createUserQualifiedPair`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `createUserQualifiedPair`(IN userID INT, IN languageCodeSource VARCHAR(3), IN countryCodeSource VARCHAR(4), IN languageCodeTarget VARCHAR(3), IN countryCodeTarget VARCHAR(4), IN qualificationLevel INT)
BEGIN
  if NOT EXISTS (
    SELECT 1
    FROM UserQualifiedPairs
    WHERE
        user_id=userID AND
        language_code_source=languageCodeSource AND
        country_code_source=countryCodeSource AND
        language_code_target=languageCodeTarget AND
        country_code_target=countryCodeTarget
  )
  THEN
    INSERT INTO UserQualifiedPairs (
        user_id,
        language_id_source,
        language_code_source,
        country_id_source,
        country_code_source,
        language_id_target,
        language_code_target,
        country_id_target,
        country_code_target,
        qualification_level
    ) VALUES (
        userID,
        (SELECT id FROM Languages WHERE code=languageCodeSource),
        languageCodeSource,
        (SELECT id FROM Countries WHERE code=countryCodeSource),
        countryCodeSource,
        (SELECT id FROM Languages WHERE code=languageCodeTarget),
        languageCodeTarget,
        (SELECT id FROM Countries WHERE code=countryCodeTarget),
        countryCodeTarget,
        qualificationLevel
    );
  ELSE
    UPDATE UserQualifiedPairs SET qualification_level=qualificationLevel
    WHERE
        user_id=userID AND
        language_code_source=languageCodeSource AND
        country_code_source=countryCodeSource AND
        language_code_target=languageCodeTarget AND
        country_code_target=countryCodeTarget;
  END IF;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `updateUserQualifiedPair`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateUserQualifiedPair`(IN userID INT, IN languageCodeSource VARCHAR(3), IN countryCodeSource VARCHAR(4), IN languageCodeTarget VARCHAR(3), IN countryCodeTarget VARCHAR(4), IN qualificationLevel INT)
BEGIN
    UPDATE UserQualifiedPairs SET qualification_level=qualificationLevel
    WHERE
        user_id=userID AND
        language_code_source=languageCodeSource AND
        country_code_source=countryCodeSource AND
        language_code_target=languageCodeTarget AND
        country_code_target=countryCodeTarget;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getUserQualifiedPairs`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserQualifiedPairs`(IN userID INT)
BEGIN
    SELECT uqp.*, l1.`en-name` AS language_source, c1.`en-name` AS country_source, l2.`en-name` AS language_target, c2.`en-name` AS country_target
    FROM UserQualifiedPairs uqp
    JOIN Languages l1 ON uqp.language_id_source=l1.id
    JOIN Countries c1 ON uqp.country_id_source=c1.id
    JOIN Languages l2 ON uqp.language_id_target=l2.id
    JOIN Countries c2 ON uqp.country_id_target=c2.id
    WHERE user_id=userID
    ORDER BY l1.`en-name`, l2.`en-name`, c1.`en-name`,c2.`en-name`;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `removeUserQualifiedPair`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeUserQualifiedPair`(IN userID INT, IN languageCodeSource VARCHAR(3), IN countryCodeSource VARCHAR(4), IN languageCodeTarget VARCHAR(3), IN countryCodeTarget VARCHAR(4))
BEGIN
    DELETE FROM UserQualifiedPairs
    WHERE
        user_id=userID AND
        language_code_source=languageCodeSource AND
        country_code_source=countryCodeSource AND
        language_code_target=languageCodeTarget AND
        country_code_target=countryCodeTarget;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `updateRequiredOrgQualificationLevel`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateRequiredOrgQualificationLevel`(IN orgID INT, IN requiredQualificationLevel INT)
BEGIN
    REPLACE INTO RequiredOrgQualificationLevels (org_id, required_qualification_level) VALUES (orgID, requiredQualificationLevel);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getRequiredOrgQualificationLevel`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getRequiredOrgQualificationLevel`(IN orgID INT)
BEGIN
    SELECT *
    FROM RequiredOrgQualificationLevels
    WHERE org_id=orgID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `inheritRequiredTaskQualificationLevel`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `inheritRequiredTaskQualificationLevel`(IN taskID BIGINT)
BEGIN
    INSERT INTO RequiredTaskQualificationLevels
        (task_id, required_qualification_level)
    VALUES (
        taskID,
        IFNULL(
            (
                SELECT oql.required_qualification_level
                FROM Tasks t
                JOIN Projects p ON t.project_id=p.id
                JOIN RequiredOrgQualificationLevels oql ON p.organisation_id=oql.org_id
                WHERE t.id=taskID
            ),
            1)
    );
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `updateRequiredTaskQualificationLevel`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateRequiredTaskQualificationLevel`(IN taskID BIGINT, IN requiredQualificationLevel INT)
BEGIN
    REPLACE INTO RequiredTaskQualificationLevels (task_id, required_qualification_level) VALUES (taskID, requiredQualificationLevel);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getRequiredTaskQualificationLevel`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getRequiredTaskQualificationLevel`(IN taskID BIGINT)
BEGIN
    SELECT *
    FROM RequiredTaskQualificationLevels
    WHERE task_id=taskID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getOrgIDMatchingNeon`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrgIDMatchingNeon`(IN orgIDNeon INT)
BEGIN
    SELECT org_id FROM OrgIDMatchingNeon WHERE org_id_neon=orgIDNeon;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insertOrgIDMatchingNeon`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertOrgIDMatchingNeon`(IN orgID INT, IN orgIDNeon INT)
BEGIN
    INSERT INTO OrgIDMatchingNeon
               (org_id_neon, org_id, created_time)
        VALUES (  orgIDNeon,  orgID, NOW());
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `record_task_translated_in_matecat`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `record_task_translated_in_matecat`(IN `taskId` INT)
BEGIN
    REPLACE INTO TaskTranslatedInMatecat (task_id) VALUES (taskId);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `is_task_translated_in_matecat`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `is_task_translated_in_matecat`(IN `taskId` INT)
BEGIN
    SELECT * FROM TaskTranslatedInMatecat WHERE task_id=taskId;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insert_task_invite_sent_to_users`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_task_invite_sent_to_users`(IN `valueList` TEXT)
BEGIN
    SET @query = CONCAT(
        'INSERT INTO TaskInviteSentToUsers
        (task_id, user_id, date_sent_invite)
        VALUES',
        valueList);
    PREPARE statement from @query;
    execute statement;
    DEALLOCATE PREPARE statement;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `list_task_invites_sent`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `list_task_invites_sent`(IN `taskID` INT)
BEGIN
    SELECT
        ti.user_id,
        MAX(ti.date_sent_invite) AS date_sent_invite,
        IFNULL(MAX(tv.`viewed-time`),  '') AS date_viewed_task,
        IFNULL(MAX(tc.`claimed-time`), '') AS date_claimed_task,
        u.`display-name` AS display_name,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`, '') AS last_name
    FROM TaskInviteSentToUsers       ti
    JOIN Users                        u ON ti.user_id=u.id
    LEFT JOIN UserPersonalInformation i ON ti.user_id=i.user_id
    LEFT JOIN TaskClaims             tc ON ti.user_id=tc.user_id AND ti.task_id=tc.task_id
    LEFT JOIN TaskViews              tv ON ti.user_id=tv.user_id AND tv.task_id=taskID
    WHERE
        ti.task_id=taskID
    GROUP BY ti.user_id
    ORDER BY MAX(ti.date_sent_invite) ASC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `list_task_invites_not_sent_strict`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `list_task_invites_not_sent_strict`(IN `taskID` INT)
BEGIN
    SELECT
        u.id AS user_id,
        u.`display-name` AS display_name,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`, '') AS last_name,
        CASE
            WHEN MAX(uqp.qualification_level=1) THEN 'Translator'
            WHEN MAX(uqp.qualification_level=2) THEN 'Verified Translator'
            WHEN MAX(uqp.qualification_level=3) THEN 'Senior Translator'
        END AS level,
        IFNULL(ln.`en-name`, '') AS language_name_native,
        IFNULL(cn.`en-name`, '') AS country_name_native
    FROM Tasks                            t
    JOIN Projects                         p ON t.project_id=p.id
    JOIN RequiredTaskQualificationLevels tq ON t.id=tq.task_id
    JOIN UserQualifiedPairs             uqp ON
        t.`language_id-source`=uqp.language_id_source AND
        t.`language_id-target`=uqp.language_id_target AND
        t.`country_id-target`=uqp.country_id_target AND
        tq.required_qualification_level<=uqp.qualification_level
    JOIN Users                            u ON uqp.user_id=u.id
    LEFT JOIN Languages                  ln ON u.language_id=ln.id
    LEFT JOIN Countries                  cn ON u.country_id=cn.id
    LEFT JOIN UserPersonalInformation     i ON u.id=i.user_id
    LEFT JOIN TaskInviteSentToUsers     tis ON u.id=tis.user_id AND tis.task_id=taskID
    LEFT JOIN SpecialTranslators     st ON u.id=st.user_id
    LEFT JOIN Admins                      a ON uqp.user_id=a.user_id
    LEFT JOIN OrganisationMembers         o ON uqp.user_id=o.user_id
    LEFT JOIN Badges                      b ON p.organisation_id=b.owner_id AND b.title='Qualified'
    LEFT JOIN RestrictedTasks             r ON t.id=r.restricted_task_id
    WHERE
        t.id=taskID AND
        tis.user_id IS NULL AND
        (st.user_id IS NULL OR st.type=0) AND
        a.user_id IS NULL AND
        o.user_id IS NULL AND
        NOT EXISTS (SELECT 1 FROM TaskTranslatorBlacklist tbl WHERE tbl.user_id=uqp.user_id AND tbl.task_id=t.id) AND
        (
            r.restricted_task_id IS NULL OR
            b.id IS NULL OR
            b.id IN (SELECT ub.badge_id FROM UserBadges ub WHERE ub.user_id=uqp.user_id)
        )
    GROUP BY uqp.user_id
    ORDER BY uqp.user_id DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `list_task_invites_not_sent`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `list_task_invites_not_sent`(IN `taskID` INT)
BEGIN
    SELECT
        u.id AS user_id,
        u.`display-name` AS display_name,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`, '') AS last_name,
        CASE
            WHEN MAX(uqp.qualification_level=1) THEN 'Translator'
            WHEN MAX(uqp.qualification_level=2) THEN 'Verified Translator'
            WHEN MAX(uqp.qualification_level=3) THEN 'Senior Translator'
        END AS level,
        IFNULL(ln.`en-name`, '') AS language_name_native,
        IFNULL(cn.`en-name`, '') AS country_name_native
    FROM Tasks                            t
    JOIN Projects                         p ON t.project_id=p.id
    JOIN RequiredTaskQualificationLevels tq ON t.id=tq.task_id
    JOIN UserQualifiedPairs             uqp ON
        t.`language_id-source`=uqp.language_id_source AND
        t.`language_id-target`=uqp.language_id_target AND
        tq.required_qualification_level<=uqp.qualification_level
    JOIN Users                            u ON uqp.user_id=u.id
    LEFT JOIN Languages                  ln ON u.language_id=ln.id
    LEFT JOIN Countries                  cn ON u.country_id=cn.id
    LEFT JOIN UserPersonalInformation     i ON u.id=i.user_id
    LEFT JOIN TaskInviteSentToUsers     tis ON u.id=tis.user_id AND tis.task_id=taskID
    LEFT JOIN SpecialTranslators     st ON u.id=st.user_id
    LEFT JOIN Admins                      a ON uqp.user_id=a.user_id
    LEFT JOIN OrganisationMembers         o ON uqp.user_id=o.user_id
    LEFT JOIN Badges                      b ON p.organisation_id=b.owner_id AND b.title='Qualified'
    LEFT JOIN RestrictedTasks             r ON t.id=r.restricted_task_id
    WHERE
        t.id=taskID AND
        tis.user_id IS NULL AND
        (st.user_id IS NULL OR st.type=0) AND
        a.user_id IS NULL AND
        o.user_id IS NULL AND
        NOT EXISTS (SELECT 1 FROM TaskTranslatorBlacklist tbl WHERE tbl.user_id=uqp.user_id AND tbl.task_id=t.id) AND
        (
            r.restricted_task_id IS NULL OR
            b.id IS NULL OR
            b.id IN (SELECT ub.badge_id FROM UserBadges ub WHERE ub.user_id=uqp.user_id)
        )
    GROUP BY uqp.user_id
    ORDER BY uqp.user_id DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `list_task_invites_not_sent_words`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `list_task_invites_not_sent_words`(IN `taskID` INT)
BEGIN
    SELECT
        tc.user_id,
        SUM(IF((t.`task-type_id`=2 OR t.`task-type_id`=3) AND t.`task-status_id`=4,                                                             t.`word-count`, 0)) AS words_delivered,
        SUM(IF((t.`task-type_id`=2 OR t.`task-type_id`=3) AND t.`task-status_id`=4 AND (tc.`claimed-time` > DATE_SUB(NOW(), INTERVAL 3 MONTH)), t.`word-count`, 0)) AS words_delivered_last_3_months
    FROM Tasks       t
    JOIN TaskClaims tc ON t.id=tc.task_id
    WHERE
        tc.user_id IN (
            SELECT 
                uqp1.user_id
            FROM Tasks t1
            JOIN UserQualifiedPairs uqp1 ON
                t1.`language_id-source`=uqp1.language_id_source AND
                t1.`language_id-target`=uqp1.language_id_target
            WHERE
                t1.id=taskID)
    GROUP BY tc.user_id
    ORDER BY words_delivered DESC, tc.user_id DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `list_task_invites_not_sent_tags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `list_task_invites_not_sent_tags`(IN `taskID` INT)
BEGIN
    SELECT
        ut.user_id,
        GROUP_CONCAT(t.label ORDER BY t.label ASC SEPARATOR ', ') AS user_liked_tags
    FROM UserTags ut
    JOIN Tags      t ON ut.tag_id=t.id 
    WHERE
        ut.user_id IN (
            SELECT 
                uqp1.user_id
            FROM Tasks t1
            JOIN UserQualifiedPairs uqp1 ON
                t1.`language_id-source`=uqp1.language_id_source AND
                t1.`language_id-target`=uqp1.language_id_target
            WHERE
                t1.id=taskID)
    GROUP BY ut.user_id;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `terms_accepted`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `terms_accepted`(IN userID INT)
BEGIN
     SET @level = 0;
     SELECT accepted_level INTO @level FROM TermsAcceptedUsers WHERE user_id=userID;
     SELECT @level AS accepted_level;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `update_terms_accepted`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_terms_accepted`(IN userID INT, IN acceptedLevel INT)
BEGIN
    REPLACE INTO TermsAcceptedUsers (user_id, accepted_level) VALUES (userID, acceptedLevel);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_post_login_message`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_post_login_message`(IN userID INT)
BEGIN
     SELECT *
     FROM post_login_messages
     WHERE
         user_id=userID AND
         `show`>0
     ORDER BY id;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `update_post_login_message`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_post_login_message`(IN userID INT, IN s INT)
BEGIN
    SET @target_id = 0;
    SELECT id INTO @target_id
    FROM post_login_messages
    WHERE
        user_id=userID AND
        `show`>0
    ORDER BY id
    LIMIT 1;

    UPDATE post_login_messages SET `show`=s, date_shown=NOW() WHERE id=@target_id;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_project_tm_key`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_project_tm_key`(IN `projectID` INT, IN `mtEngine` INT, IN `preTranslate` INT, IN `lexi_QA` INT, IN `privateTMKey` VARCHAR(255))
BEGIN
    INSERT INTO PrivateTMKeys (project_id, mt_engine, pretranslate_100, lexiqa, private_tm_key) VALUES (projectID, mtEngine, preTranslate, lexi_QA, privateTMKey);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_project_tm_key`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_project_tm_key`(IN `projectID` INT)
BEGIN
    SELECT * FROM PrivateTMKeys WHERE project_id=projectID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_neon_account`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_neon_account`(IN userID INT)
BEGIN
     SELECT * FROM UserNeonAccount WHERE user_id=userID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_neon_account`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_neon_account`(IN userID INT, IN accountID INT)
BEGIN
    REPLACE INTO UserNeonAccount (user_id, account_id) VALUES (userID, accountID);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_discourse_id`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_discourse_id`(IN projectID INT, IN topicID INT)
BEGIN
    INSERT INTO DiscourseID (project_id, topic_id) VALUES (projectID, topicID);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_discourse_id`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_discourse_id`(IN projectID INT)
BEGIN
    SELECT * FROM DiscourseID WHERE project_id=projectID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getUserURLs`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserURLs`(IN uID INT)
BEGIN
    SELECT * FROM UserURLs WHERE user_id=uID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insertUserURL`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertUserURL`(IN uID INT, IN ukey VARCHAR(20), IN value VARCHAR(255))
BEGIN
    REPLACE INTO UserURLs
               (user_id, url_key,   url)
        VALUES (    uID,    ukey, value);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getUserExpertises`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserExpertises`(IN uID INT)
BEGIN
    SELECT * FROM UserExpertises WHERE user_id=uID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `addUserExpertise`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `addUserExpertise`(IN uID INT, IN ekey VARCHAR(20))
BEGIN
    REPLACE INTO UserExpertises
               (user_id, expertise_key)
        VALUES (    uID,          ekey);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `removeUserExpertise`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeUserExpertise`(IN uID INT, IN ekey VARCHAR(20))
BEGIN
    DELETE
    FROM UserExpertises
    WHERE
        user_id=uID AND
        expertise_key=ekey;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getUserHowheards`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserHowheards`(IN uID INT)
BEGIN
    SELECT * FROM UserHowheards WHERE user_id=uID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insertUserHowheard`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertUserHowheard`(IN uID INT, IN hkey VARCHAR(20))
BEGIN
    REPLACE INTO UserHowheards
               (user_id, howheard_key)
        VALUES (    uID,         hkey);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insert_communications_consent`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_communications_consent`(IN uID INT, IN acc INT)
BEGIN
    REPLACE INTO communications_consents
               (user_id, accepted)
        VALUES (    uID,      acc);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_communications_consent`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_communications_consent`(IN uID INT)
BEGIN
    SELECT * FROM communications_consents WHERE user_id=uID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `updateUserHowheard`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateUserHowheard`(IN uID INT, IN r INT)
BEGIN
    UPDATE UserHowheards
    SET reviewed=r
    WHERE user_id=uID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getUserCertifications`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserCertifications`(IN uID INT)
BEGIN
    SELECT *
    FROM UserCertifications
    WHERE user_id=uID
    ORDER BY note, vid;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getUserCertificationByID`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserCertificationByID`(IN primaryID INT)
BEGIN
    SELECT *
    FROM UserCertifications
    WHERE id=primaryID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insertUserCertification`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertUserCertification`(IN uID INT, IN `versionID` INT, IN ckey VARCHAR(20), IN file VARCHAR(128), IN mime VARCHAR(128), IN n TEXT)
BEGIN
    REPLACE INTO UserCertifications
               (user_id,       vid, certification_key, filename, mimetype,  note)
        VALUES (    uID, versionID,              ckey, file,     mime,         n);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `updateCertification`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateCertification`(IN primaryID INT, IN r INT)
BEGIN
    UPDATE UserCertifications
    SET reviewed=r
    WHERE id=primaryID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `deleteCertification`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteCertification`(IN primaryID INT)
BEGIN
    DELETE FROM UserCertifications WHERE id=primaryID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `users_review`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `users_review`()
BEGIN
(
    SELECT
        0    AS cert_id,
        ''   AS certificate,
        u.id AS user_id,
        CONCAT (IFNULL(i.`first-name`, ''), ' ', IFNULL(i.`last-name`, '')) AS name,
        CONCAT (l.`en-name`, '(' , c.`en-name`, ')')                   AS native_language,
        IFNULL(i.country, '')                                          AS country_address
    FROM UserHowheards          hh
    JOIN Users                   u ON hh.user_id=u.id
    JOIN UserPersonalInformation i ON u.id=i.user_id
    JOIN Languages               l ON u.language_id=l.id
    JOIN Countries               c ON u.country_id=c.id
    WHERE
        hh.reviewed=99
)
UNION
(
    SELECT
        uc.id   AS cert_id,
        uc.note AS certificate,
        u.id    AS user_id,
        CONCAT (IFNULL(i.`first-name`, ''), ' ', IFNULL(i.`last-name`, '')) AS name,
        CONCAT (l.`en-name`, '(' , c.`en-name`, ')')                   AS native_language,
        IFNULL(i.country, '')                                          AS country_address
    FROM UserCertifications     uc
    JOIN Users                   u ON uc.user_id=u.id
    JOIN UserPersonalInformation i ON u.id=i.user_id
    JOIN Languages               l ON u.language_id=l.id
    JOIN Countries               c ON u.country_id=c.id
    WHERE
        uc.reviewed=0 AND
        uc.certification_key NOT IN ('TRANSLATOR', 'TWB')
)
ORDER BY user_id, certificate;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `users_new`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `users_new`()
BEGIN
    SELECT
        hh.user_id,
        IF(hh.reviewed!=0, '(Reviewed)', '')                               AS reviewed_text,
        CONCAT(IFNULL(i.`first-name`, ''), ' ', IFNULL(i.`last-name`, '')) AS name,
        SUBSTRING(u.`created-time`, 1, 10)                                 AS created_time,
        CONCAT(l.`en-name`, '(' , c.`en-name`, ')')                        AS native_language,
        IFNULL(GROUP_CONCAT(DISTINCT CONCAT(uqp.language_code_source, '-', uqp.country_code_source, '|', uqp.language_code_target, '-', uqp.country_code_target) ORDER BY CONCAT(uqp.language_code_source, '-', uqp.country_code_source, '|', uqp.language_code_target, '-', uqp.country_code_target) SEPARATOR ', '), '') AS language_pairs,
        IFNULL(u.biography, '')                                                                              AS bio,
        IFNULL(GROUP_CONCAT(DISTINCT uc.certification_key ORDER BY uc.certification_key SEPARATOR ', '), '') AS certificates,
        u.email
    FROM UserHowheards            hh
    JOIN Users                     u ON hh.user_id=u.id
    JOIN UserPersonalInformation   i ON u.id=i.user_id
    JOIN Languages                 l ON u.language_id=l.id
    JOIN Countries                 c ON u.country_id=c.id
    LEFT JOIN UserQualifiedPairs uqp ON u.id=uqp.user_id
    LEFT JOIN UserCertifications  uc ON u.id=uc.user_id
    WHERE u.id>=37052
    GROUP BY u.id
    ORDER BY hh.reviewed, hh.user_id DESC
    LIMIT 500;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `supported_ngos`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `supported_ngos`(IN uID INT)
BEGIN
    SELECT DISTINCT
        o.name AS org_name,
        o.id AS org_id
    FROM TaskClaims   tc
    JOIN Tasks         t ON tc.task_id=t.id
    JOIN Projects      p ON t.project_id=p.id
    JOIN Organisations o ON p.organisation_id=o.id
    WHERE
        tc.user_id=uID AND
        t.`task-status_id`=4
    ORDER BY o.name;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `supported_ngos_paid`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `supported_ngos_paid`(IN uID INT)
BEGIN
    SELECT DISTINCT
        o.name AS org_name,
        o.id AS org_id
    FROM TaskClaims   tc
    JOIN TaskPaids    tp ON tc.task_id=tp.task_id
    JOIN Tasks         t ON tc.task_id=t.id
    JOIN Projects      p ON t.project_id=p.id
    JOIN Organisations o ON p.organisation_id=o.id
    WHERE
        tc.user_id=uID AND
        t.`task-status_id`=4
    ORDER BY o.name;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `quality_score`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `quality_score`(IN `uID` INT)
BEGIN
    SELECT
        IF(SUM(consistency<10), FORMAT(SUM(IF(consistency<10, tr.corrections, 0))/SUM(consistency<10), 1), '') AS cor,
        IF(SUM(consistency<10), FORMAT(SUM(IF(consistency<10, tr.grammar,     0))/SUM(consistency<10), 1), '') AS gram,
        IF(SUM(consistency<10), FORMAT(SUM(IF(consistency<10, tr.spelling,    0))/SUM(consistency<10), 1), '') AS spell,
        IF(SUM(consistency<10), FORMAT(SUM(IF(consistency<10, tr.consistency, 0))/SUM(consistency<10), 1), '') AS cons,
        SUM(consistency<10) AS num_legacy,
        IF(SUM(consistency>=10), FORMAT(SUM(IF(consistency>=10, tr.corrections,        0))/SUM(consistency>=10), 1), '') AS accuracy,
        IF(SUM(consistency>=10), FORMAT(SUM(IF(consistency>=10, tr.grammar,            0))/SUM(consistency>=10), 1), '') AS fluency,
        IF(SUM(consistency>=10), FORMAT(SUM(IF(consistency>=10, tr.spelling,           0))/SUM(consistency>=10), 1), '') AS terminology,
        IF(SUM(consistency>=10), FORMAT(SUM(IF(consistency>=10, tr.consistency   % 10, 0))/SUM(consistency>=10), 1), '') AS style,
        IF(SUM(consistency>=10), FORMAT(SUM(IF(consistency>=10, tr.consistency DIV 10, 0))/SUM(consistency>=10), 1), '') AS design,
        SUM(consistency>=10) AS num_new,
        COUNT(*)             AS num
    FROM TaskReviews            tr
    JOIN Tasks                   t  ON tr.task_id=t.id
    JOIN TaskClaims             tc  ON tr.task_id=tc.task_id
    WHERE
        t.`task-status_id`=4 AND
        tc.user_id=uID
    GROUP BY tc.user_id;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `admin_comments`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `admin_comments`(IN uID INT)
BEGIN
    SELECT
        ac.*,
         u.email AS admin_email
    FROM admin_comment ac
    JOIN Users          u ON ac.admin_id=u.id
    WHERE ac.user_id=uID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `admin_comments_average`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `admin_comments_average`(IN uID INT)
BEGIN
    SELECT ROUND(AVG(work_again), 2) AS average FROM admin_comment WHERE user_id=uID GROUP BY user_id;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insert_admin_comment`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_admin_comment`(IN uID INT, IN aID INT, IN work INT, IN comment VARCHAR(2000))
BEGIN
    INSERT INTO admin_comment
               (user_id, admin_id, work_again, created, admin_comment)
        VALUES (    uID,      aID,       work,   NOW(),       comment);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `delete_admin_comment`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_admin_comment`(IN primaryID INT)
BEGIN
    DELETE FROM admin_comment WHERE id=primaryID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `adjust_points`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `adjust_points`(IN uID INT)
BEGIN
    SELECT
        ap.*,
         u.email AS admin_email
    FROM adjust_points ap
    JOIN Users          u ON ap.admin_id=u.id
    WHERE ap.user_id=uID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insert_adjust_points`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_adjust_points`(IN uID INT, IN aID INT, IN point INT, IN comment VARCHAR(2000))
BEGIN
    INSERT INTO adjust_points
               (user_id, admin_id, points, created, admin_comment)
        VALUES (    uID,      aID,  point,   NOW(),       comment);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `delete_adjust_points`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_adjust_points`(IN primaryID INT)
BEGIN
    DELETE FROM adjust_points WHERE id=primaryID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `adjust_points_strategic`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `adjust_points_strategic`(IN uID INT)
BEGIN
    SELECT
        ap.*,
         u.email AS admin_email
    FROM adjust_points_strategic ap
    JOIN Users                    u ON ap.admin_id=u.id
    WHERE ap.user_id=uID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insert_adjust_points_strategic`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_adjust_points_strategic`(IN uID INT, IN aID INT, IN point INT, IN comment VARCHAR(2000))
BEGIN
    INSERT INTO adjust_points_strategic
               (user_id, admin_id, points, created, admin_comment)
        VALUES (    uID,      aID,  point,   NOW(),       comment);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `delete_adjust_points_strategic`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_adjust_points_strategic`(IN primaryID INT)
BEGIN
    DELETE FROM adjust_points_strategic WHERE id=primaryID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `covid_projects`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `covid_projects`()
BEGIN
    SELECT
        p.title AS project_title,
        o.name AS org_name,
        MAX(u.email) AS creator_email,
        p.`word-count` AS word_count,
        GROUP_CONCAT(DISTINCT CONCAT(l1.code, '-', c1.code, '|', l2.code, '-', c2.code) ORDER BY CONCAT(l1.code, '-', c1.code, '|', l2.code, '-', c2.code) ASC SEPARATOR ', ') AS language_pairs,
        COUNT(DISTINCT CONCAT(l1.code, '-', c1.code, '|', l2.code, '-', c2.code)) AS language_pairs_number,
        p.created,
        p.deadline,
        IF(MIN(t.`task-status_id`=4), MAX(tcd.complete_date), '') AS completed
    FROM Projects               p
    JOIN ProjectTags           pt ON p.id=pt.project_id AND pt.tag_id=4334
    JOIN Organisations          o ON p.organisation_id=o.id
    JOIN Tasks                  t ON p.id=t.project_id
    LEFT JOIN TaskFileVersions tv ON t.id=tv.task_id AND tv.version_id=0 AND t.`task-type_id`=2
    LEFT JOIN Users             u ON tv.user_id=u.id
    JOIN Languages             l1 ON p. language_id=l1.id
    JOIN Countries             c1 ON p. country_id =c1.id
    JOIN Languages             l2 ON t.`language_id-target`=l2.id
    JOIN Countries             c2 ON t.`country_id-target` =c2.id
    LEFT JOIN TaskCompleteDates tcd ON t.id=tcd.task_id
    GROUP BY p.id
    ORDER BY p.created DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `afghanistan_2021_projects`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `afghanistan_2021_projects`()
BEGIN
    SELECT
        p.title AS project_title,
        o.name AS org_name,
        MAX(u.email) AS creator_email,
        p.`word-count` AS word_count,
        GROUP_CONCAT(DISTINCT CONCAT(l1.code, '-', c1.code, '|', l2.code, '-', c2.code) ORDER BY CONCAT(l1.code, '-', c1.code, '|', l2.code, '-', c2.code) ASC SEPARATOR ', ') AS language_pairs,
        COUNT(DISTINCT CONCAT(l1.code, '-', c1.code, '|', l2.code, '-', c2.code)) AS language_pairs_number,
        p.created,
        p.deadline,
        IF(MIN(t.`task-status_id`=4), MAX(tcd.complete_date), '') AS completed
    FROM Projects               p
    JOIN ProjectTags           pt ON p.id=pt.project_id AND pt.tag_id=4613
    JOIN Organisations          o ON p.organisation_id=o.id
    JOIN Tasks                  t ON p.id=t.project_id
    LEFT JOIN TaskFileVersions tv ON t.id=tv.task_id AND tv.version_id=0 AND t.`task-type_id`=2
    LEFT JOIN Users             u ON tv.user_id=u.id
    JOIN Languages             l1 ON p. language_id=l1.id
    JOIN Countries             c1 ON p. country_id =c1.id
    JOIN Languages             l2 ON t.`language_id-target`=l2.id
    JOIN Countries             c2 ON t.`country_id-target` =c2.id
    LEFT JOIN TaskCompleteDates tcd ON t.id=tcd.task_id
    GROUP BY p.id
    ORDER BY p.created DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `haiti_2021_projects`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `haiti_2021_projects`()
BEGIN
    SELECT
        p.title AS project_title,
        o.name AS org_name,
        MAX(u.email) AS creator_email,
        p.`word-count` AS word_count,
        GROUP_CONCAT(DISTINCT CONCAT(l1.code, '-', c1.code, '|', l2.code, '-', c2.code) ORDER BY CONCAT(l1.code, '-', c1.code, '|', l2.code, '-', c2.code) ASC SEPARATOR ', ') AS language_pairs,
        COUNT(DISTINCT CONCAT(l1.code, '-', c1.code, '|', l2.code, '-', c2.code)) AS language_pairs_number,
        p.created,
        p.deadline,
        IF(MIN(t.`task-status_id`=4), MAX(tcd.complete_date), '') AS completed
    FROM Projects               p
    JOIN ProjectTags           pt ON p.id=pt.project_id AND pt.tag_id=4614
    JOIN Organisations          o ON p.organisation_id=o.id
    JOIN Tasks                  t ON p.id=t.project_id
    LEFT JOIN TaskFileVersions tv ON t.id=tv.task_id AND tv.version_id=0 AND t.`task-type_id`=2
    LEFT JOIN Users             u ON tv.user_id=u.id
    JOIN Languages             l1 ON p. language_id=l1.id
    JOIN Countries             c1 ON p. country_id =c1.id
    JOIN Languages             l2 ON t.`language_id-target`=l2.id
    JOIN Countries             c2 ON t.`country_id-target` =c2.id
    LEFT JOIN TaskCompleteDates tcd ON t.id=tcd.task_id
    GROUP BY p.id
    ORDER BY p.created DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `record_track_code`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `record_track_code`(IN trackCode VARCHAR(255))
BEGIN
    REPLACE INTO TrackCodes (id, track_code) VALUES (1, trackCode);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insert_tracked_registration`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_tracked_registration`(IN uID INT, IN trackCode VARCHAR(255))
BEGIN
    SELECT UNHEX(trackCode) INTO @binary_track_code;
    IF @binary_track_code IS NOT NULL THEN
        SELECT AES_DECRYPT(@binary_track_code, 'helks5nesahel') INTO @decrypted;
        IF @decrypted IS NOT NULL THEN
            REPLACE INTO TrackedRegistrations VALUES (uID, @decrypted);
        END IF;
    END IF;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_tracked_registration`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_tracked_registration`(IN uID INT)
BEGIN
    SELECT referer FROM TrackedRegistrations WHERE user_id=uID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `users_tracked`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `users_tracked`()
BEGIN
    SELECT
        tr.referer,
        u.id                                                               AS user_id,
        CONCAT(IFNULL(i.`first-name`, ''), ' ', IFNULL(i.`last-name`, '')) AS name,
        SUBSTRING(u.`created-time`, 1, 10)                                 AS created_time,
        CONCAT(l.`en-name`, '(' , c.`en-name`, ')')                        AS native_language,
        IFNULL(GROUP_CONCAT(DISTINCT CONCAT(uqp.language_code_source, '-', uqp.country_code_source, '|', uqp.language_code_target, '-', uqp.country_code_target) ORDER BY CONCAT(uqp.language_code_source, '-', uqp.country_code_source, '|', uqp.language_code_target, '-', uqp.country_code_target) SEPARATOR ', '), '') AS language_pairs,
        IFNULL(u.biography, '')                                                                              AS bio,
        IFNULL(GROUP_CONCAT(DISTINCT uc.certification_key ORDER BY uc.certification_key SEPARATOR ', '), '') AS certificates,
        u.email
    FROM TrackedRegistrations     tr
    JOIN Users                     u ON tr.user_id=u.id
    JOIN UserPersonalInformation   i ON u.id=i.user_id
    JOIN Languages                 l ON u.language_id=l.id
    JOIN Countries                 c ON u.country_id=c.id
    LEFT JOIN UserQualifiedPairs uqp ON u.id=uqp.user_id
    LEFT JOIN UserCertifications  uc ON u.id=uc.user_id
    GROUP BY u.id
    ORDER BY u.`created-time` DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `peer_to_peer_vetting`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `peer_to_peer_vetting`()
BEGIN
    SELECT
        u.id AS user_id,
        u.`display-name`                                                        AS display_name,
        u.email,
        native.code                                                             AS native_language_code,
        native.`en-name`                                                        AS native_language_name,
        SUM(IF(t.`task-type_id`=2, t.`word-count`, 0))                          AS words_translated,
        SUM(IF(t.`task-type_id`=3, t.`word-count`, 0))                          AS words_revised,
        CONCAT(l1.code, '|', l2.code)                                           AS language_pair,
        CONCAT(u.id, '-', l1.code, '|', l2.code)                                AS user_language_pair,
        CONCAT(u.id, '-', l1.code, '|', l2.code)                                AS user_language_pair_reduced,
        GROUP_CONCAT(DISTINCT CONCAT(l1.code, '-', c1.code, '|', l2.code, '-', c2.code) ORDER BY CONCAT(l1.code, '-', c1.code, '|', l2.code, '-', c2.code) SEPARATOR ', ') AS language_pair_list,
        MAX(tc.`claimed-time`)                                                  AS last_task
    FROM Tasks                     t
    JOIN TaskClaims               tc ON t.id=tc.task_id
    JOIN Users                     u ON tc.user_id=u.id
    JOIN Languages            native ON u.language_id=native.id
    JOIN Languages                l1 ON t.`language_id-source`=l1.id
    JOIN Languages                l2 ON t.`language_id-target`=l2.id
    JOIN Countries                c1 ON t.`country_id-source`=c1.id
    JOIN Countries                c2 ON t.`country_id-target`=c2.id
    LEFT JOIN TaskReviews         tr ON t.id=tr.task_id
    WHERE
        t.`task-status_id`=4
    GROUP BY tc.user_id, t.`language_id-source`, t.`language_id-target`
    ORDER BY l1.code, l2.code, u.email;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `peer_to_peer_vetting_qualification_level`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `peer_to_peer_vetting_qualification_level`()
BEGIN
    SELECT
        uqp.user_id,
        uqp.language_code_source,
        uqp.language_code_target,
        CONCAT(uqp.user_id, '-', uqp.language_code_source, '|', uqp.language_code_target) AS user_language_pair_reduced,
        CASE
            WHEN MAX(uqp.qualification_level)=1 THEN 'Translator'
            WHEN MAX(uqp.qualification_level)=2 THEN 'Verified Translator'
            WHEN MAX(uqp.qualification_level)=3 THEN 'Senior Translator'
        END                                                                               AS level
    FROM UserQualifiedPairs uqp
    GROUP BY uqp.user_id, uqp.language_code_source, uqp.language_code_target;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `peer_to_peer_vetting_reviews`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `peer_to_peer_vetting_reviews`()
BEGIN
    SELECT
        tc.user_id,
        CONCAT(tc.user_id, '-', l1.code, '|', l2.code) AS user_language_pair,
        FORMAT(
            SUM((tr.corrections + tr.grammar + tr.spelling + tr.consistency % 10 + tr.consistency DIV 10)/5.)
                /
            SUM(1.),
            1
        )                                              AS average_reviews,
        SUM(1)                                         AS number_reviews
    FROM Tasks        t
    JOIN TaskReviews tr ON t.id=tr.task_id
    JOIN Languages   l1 ON t.`language_id-source`=l1.id
    JOIN Languages   l2 ON t.`language_id-target`=l2.id
    JOIN TaskClaims  tc ON t.id=tc.task_id
    WHERE
        tr.task_id IS NOT NULL AND
        tr.consistency>=10 AND
        t.`task-status_id`=4
    GROUP BY tc.user_id, t.`language_id-source`, t.`language_id-target`;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insert_project_restrictions`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_project_restrictions`(IN projectID INT, IN translate INT, IN revise INT)
BEGIN
    INSERT INTO ProjectRestrictions (project_id, restrict_translate_tasks, restrict_revise_tasks) VALUES (projectID, translate, revise);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_project_restrictions`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_project_restrictions`(IN projectID INT)
BEGIN
    SELECT *
    FROM ProjectRestrictions
    WHERE project_id=projectID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_memsource_user`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_memsource_user`(IN userID INT, IN memsourceID BIGINT, IN memsourceUID VARCHAR(30))
BEGIN
    INSERT INTO MemsourceUsers (user_id, memsource_user_id, memsource_user_uid) VALUES (userID, memsourceID, memsourceUID);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_memsource_user`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_memsource_user`(IN userID INT)
BEGIN
    SELECT * FROM MemsourceUsers WHERE user_id=userID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_user_id_from_memsource_user`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_user_id_from_memsource_user`(IN memsourceUID VARCHAR(30))
BEGIN
    SELECT * FROM MemsourceUsers WHERE memsource_user_uid=memsourceUID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_memsource_client`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_memsource_client`(IN orgID INT, IN memsourceID BIGINT, IN memsourceUID VARCHAR(30))
BEGIN
    INSERT INTO MemsourceClients (org_id, memsource_client_id, memsource_client_uid) VALUES (orgID, memsourceID, memsourceUID);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_memsource_client`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_memsource_client`(IN orgID INT)
BEGIN
    SELECT * FROM MemsourceClients WHERE org_id=orgID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_memsource_client_by_memsource_id`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_memsource_client_by_memsource_id`(IN memsourceID BIGINT)
BEGIN
    SELECT * FROM MemsourceClients WHERE memsource_client_id=memsourceID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_memsource_project`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_memsource_project`(IN projectID INT, IN memsourceID BIGINT, IN memsourceUID VARCHAR(30), IN createdUID VARCHAR(30), IN ownerUID VARCHAR(30), IN workflow1 VARCHAR(30), IN workflow2 VARCHAR(30), IN workflow3 VARCHAR(30), IN workflow4 VARCHAR(30), IN workflow5 VARCHAR(30), IN workflow6 VARCHAR(30), IN workflow7 VARCHAR(30), IN workflow8 VARCHAR(30), IN workflow9 VARCHAR(30), IN workflow10 VARCHAR(30), IN workflow11 VARCHAR(30), IN workflow12 VARCHAR(30))
BEGIN
    INSERT INTO MemsourceProjects (project_id, memsource_project_id, memsource_project_uid, created_by_uid, owner_uid, workflow_level_1, workflow_level_2, workflow_level_3, workflow_level_4, workflow_level_5, workflow_level_6, workflow_level_7, workflow_level_8, workflow_level_9, workflow_level_10, workflow_level_11, workflow_level_12)
    VALUES                        ( projectID,          memsourceID,          memsourceUID,     createdUID,  ownerUID,        workflow1,        workflow2,        workflow3,        workflow4,        workflow5,        workflow6,        workflow7,        workflow8,        workflow9,        workflow10,        workflow11,        workflow12);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `update_memsource_project_owner`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_memsource_project_owner`(IN projectID INT, IN ownerUID BIGINT)
BEGIN
    UPDATE MemsourceProjects
    SET owner_uid=ownerUID
    WHERE project_id=projectID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `update_memsource_project`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_memsource_project`(IN projectID INT, IN workflow1 VARCHAR(30), IN workflow2 VARCHAR(30), IN workflow3 VARCHAR(30), IN workflow4 VARCHAR(30), IN workflow5 VARCHAR(30), IN workflow6 VARCHAR(30), IN workflow7 VARCHAR(30), IN workflow8 VARCHAR(30), IN workflow9 VARCHAR(30), IN workflow10 VARCHAR(30), IN workflow11 VARCHAR(30), IN workflow12 VARCHAR(30))
BEGIN
    UPDATE MemsourceProjects
    SET workflow_level_1=workflow1, workflow_level_2=workflow2, workflow_level_3=workflow3, workflow_level_4=workflow4, workflow_level_5=workflow5, workflow_level_6=workflow6, workflow_level_7=workflow7, workflow_level_8=workflow8, workflow_level_9=workflow9, workflow_level_10=workflow10, workflow_level_11=workflow11, workflow_level_12=workflow12
    WHERE project_id=projectID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_memsource_self_service_project`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_memsource_self_service_project`(IN memsourceID BIGINT, IN sp INT)
BEGIN
    INSERT INTO MemsourceSelfServiceProjects (memsource_project_id, split)
    VALUES                                   (         memsourceID,    sp);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_memsource_self_service_project`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_memsource_self_service_project`(IN memsourceID BIGINT)
BEGIN
    SELECT * FROM MemsourceSelfServiceProjects WHERE memsource_project_id=memsourceID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_memsource_project`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_memsource_project`(IN projectID INT)
BEGIN
    SELECT * FROM MemsourceProjects WHERE project_id=projectID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_memsource_project_by_memsource_id`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_memsource_project_by_memsource_id`(IN memsourceID BIGINT)
BEGIN
    SELECT * FROM MemsourceProjects WHERE memsource_project_id=memsourceID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_memsource_project_by_memsource_uid`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_memsource_project_by_memsource_uid`(IN memsourceUID VARCHAR(30))
BEGIN
    SELECT * FROM MemsourceProjects WHERE memsource_project_uid=memsourceUID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `record_memsource_project_languages`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `record_memsource_project_languages`(IN projectID INT, source VARCHAR(30), target VARCHAR(255))
BEGIN
    INSERT INTO MemsourceProjectLanguages (project_id, kp_source_language_pair, kp_target_language_pairs)
    VALUES                                ( projectID,                  source,                   target);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_memsource_project_languages`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_memsource_project_languages`(IN projectID INT)
BEGIN
    SELECT * FROM MemsourceProjectLanguages WHERE project_id=projectID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_memsource_task`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_memsource_task`(IN taskID BIGINT, IN memsourceID BIGINT, IN memsourceUID VARCHAR(30), IN t VARCHAR(30), IN intID VARCHAR(30), IN level INT, IN begin INT, IN end INT, IN prereq BIGINT)
BEGIN
    INSERT IGNORE INTO MemsourceTasks (task_id, memsource_task_id, memsource_task_uid, task, internalId, workflowLevel,beginIndex, endIndex, prerequisite)
    VALUES                            ( taskID,       memsourceID,       memsourceUID,    t,      intID,         level,     begin,      end,       prereq);
    SELECT IF(ROW_COUNT()=0, 0, 1) AS result;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `update_memsource_task`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_memsource_task`(IN taskID BIGINT, IN memsourceID BIGINT, IN t VARCHAR(30), IN intID VARCHAR(30), IN begin INT, IN end INT)
BEGIN
    UPDATE MemsourceTasks
    SET
        memsource_task_id=memsourceID,
        task=t,
        internalId=intID,
        beginIndex=begin,
        endIndex=end
WHERE task_id=taskID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_memsource_task`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_memsource_task`(IN taskID BIGINT)
BEGIN
    SELECT * FROM MemsourceTasks WHERE task_id=taskID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_memsource_task_by_memsource_id`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_memsource_task_by_memsource_id`(IN memsourceID BIGINT)
BEGIN
    SELECT * FROM MemsourceTasks WHERE memsource_task_id=memsourceID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_memsource_task_by_memsource_uid`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_memsource_task_by_memsource_uid`(IN memsourceUID VARCHAR(30))
BEGIN
    SELECT * FROM MemsourceTasks WHERE memsource_task_uid=memsourceUID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `is_job_uid_already_processed`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `is_job_uid_already_processed`(IN memsourceUID VARCHAR(30))
BEGIN
    INSERT IGNORE INTO ProcessedMemsourceTaskUIDs (memsource_task_uid)
    VALUES                                        (memsourceUID);
    SELECT IF(ROW_COUNT()=0, 1, 0) AS result;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_memsource_tasks_for_project_language_type`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_memsource_tasks_for_project_language_type`(IN projectID INT, IN taskUID VARCHAR(30), IN typeID INT)
BEGIN
    SELECT
        mt.*,
        t.`language_id-source`,
        t.`language_id-target`,
        t.`country_id-source`,
        t.`country_id-target`,
        t.`task-status_id`
    FROM Tasks           t
    JOIN MemsourceTasks mt ON t.id=mt.task_id
    WHERE
        t.project_id=projectID AND
        BINARY mt.task=taskUID AND
        t.`task-type_id`=typeID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_memsource_tasks_for_project_internal_id_type`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_memsource_tasks_for_project_internal_id_type`(IN projectID INT, IN intID VARCHAR(30), IN typeID INT)
BEGIN
    SELECT
        mt.*,
        t.`language_id-source`,
        t.`language_id-target`,
        t.`country_id-source`,
        t.`country_id-target`,
        t.`task-status_id`
    FROM Tasks           t
    JOIN MemsourceTasks mt ON t.id=mt.task_id
    WHERE
        t.project_id=projectID AND
        mt.internalId=intID AND
        t.`task-type_id`=typeID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_memsource_status`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_memsource_status`(IN taskID BIGINT, IN memsourceUID VARCHAR(30), IN statusID VARCHAR(30))
BEGIN
    INSERT INTO memsource_statuses (task_id, memsource_task_uid,   status, status_time)
    VALUES                         ( taskID,       memsourceUID, statusID,       NOW());
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `queue_copy_task_original_file`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `queue_copy_task_original_file`(IN projectID INT, IN taskID INT, IN memsourceUID VARCHAR(30), IN name VARCHAR(255))
BEGIN
    INSERT INTO queue_copy_task_original_files (project_id, task_id, memsource_task_uid, filename)
    VALUES                                     ( projectID,  taskID,       memsourceUID,     name);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_queue_copy_task_original_files`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_queue_copy_task_original_files`()
BEGIN
    SELECT * FROM queue_copy_task_original_files;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `dequeue_copy_task_original_file`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `dequeue_copy_task_original_file`(IN taskID BIGINT)
BEGIN
    DELETE FROM queue_copy_task_original_files WHERE task_id=taskID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `queue_asana_project`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `queue_asana_project`(IN pID INT)
BEGIN
  IF NOT EXISTS (SELECT 1 FROM AsanaProjects WHERE project_id=pID) THEN
    INSERT INTO AsanaProjects (project_id, run_time)
                       VALUES (       pID, DATE_ADD(NOW(), INTERVAL 10 MINUTE));
  ELSE
    UPDATE AsanaProjects SET run_time=DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE project_id=pID;
  END IF;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_queue_asana_projects`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_queue_asana_projects`()
BEGIN
    SELECT * FROM AsanaProjects WHERE run_time < NOW();
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `dequeue_asana_project`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `dequeue_asana_project`(IN pID INT)
BEGIN
    DELETE FROM AsanaProjects WHERE project_id=pID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_asana_task`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_asana_task`(IN pID INT, IN code_source VARCHAR(10), IN code_target VARCHAR(10), IN asana_id VARCHAR(30))
BEGIN
    INSERT INTO AsanaTasks (project_id, language_code_source, language_code_target, asana_task_id)
    VALUES                 (       pID,          code_source,          code_target,      asana_id);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_asana_tasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_asana_tasks`(IN pID INT)
BEGIN
    SELECT * FROM AsanaTasks WHERE project_id=pID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_first_project_task`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_first_project_task`(IN projectID INT)
BEGIN
    SELECT MIN(id) AS min_id
    FROM  Tasks
    WHERE project_id=projectID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `update_project_due_date`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_project_due_date`(IN projectID INT, IN deadlineTime DATETIME)
BEGIN
    UPDATE Projects SET deadline=deadlineTime WHERE id=projectID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `update_project_description`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_project_description`(IN projectID INT, IN d VARCHAR(4096))
BEGIN
    UPDATE Projects SET description=d WHERE id=projectID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `update_project_organisation`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_project_organisation`(IN projectID INT, IN orgID INT)
BEGIN
    UPDATE Projects SET organisation_id=orgID WHERE id=projectID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `update_task_due_date`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_task_due_date`(IN taskID BIGINT, IN deadlineTime DATETIME)
BEGIN
    UPDATE Tasks SET deadline=deadlineTime WHERE id=taskID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_user`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_user`(IN uID INT)
BEGIN
    SELECT * FROM Users WHERE id=uID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_tasks_for_project`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_tasks_for_project`(IN projectID INT)
BEGIN
    SELECT t.*, mt.*
    FROM Tasks           t
    JOIN MemsourceTasks mt ON t.id=mt.task_id
    WHERE
        t.project_id=projectID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `delete_task_directly`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_task_directly`(IN taskID BIGINT)
BEGIN
    SELECT project_id INTO @pID FROM Tasks WHERE id=taskID;
    DELETE FROM Tasks WHERE id=taskID;
    call update_project_complete_date_project(@pID);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `record_referer`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `record_referer`(IN ref VARCHAR(30))
BEGIN
    REPLACE INTO Referers (referer) VALUES (ref);
    SELECT CONCAT('https://kato.translatorswb.org/register_track/', HEX(AES_ENCRYPT(ref, 'helks5nesahel')), '/') AS url;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_referers`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_referers`()
BEGIN
    SELECT * FROM Referers;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_google_user_details`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_google_user_details`(IN mail VARCHAR(128), IN firstName VARCHAR(128), IN lastName VARCHAR(128))
BEGIN
    INSERT INTO GoogleUserDetails (email, first_name, last_name, retrieved) VALUES (mail, firstName, lastName, NOW());
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_google_user_details`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_google_user_details`(IN mail VARCHAR(128))
BEGIN
    SELECT * FROM GoogleUserDetails WHERE email=mail ORDER BY retrieved DESC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getUsersAddedLast30Days`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUsersAddedLast30Days`()
BEGIN
    SELECT count(DATE_FORMAT(`created-time`, '%m/%d/%Y')) as users_joined
    FROM Users
    WHERE `created-time` BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE();
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `delete_not_accepted_user`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_not_accepted_user`()
BEGIN
    DECLARE uID INT;
    DECLARE mail VARCHAR(128) CHARSET utf8mb4 COLLATE 'utf8mb4_unicode_ci';

    SELECT u.id, u.email INTO uID, mail
    FROM      Users               u
    LEFT JOIN TermsAcceptedUsers ta ON u.id=ta.user_id
    LEFT JOIN Admins              a ON u.id=a.user_id
    LEFT JOIN OrganisationMembers o ON u.id=o.user_id
    WHERE
        (ta.user_id IS NULL OR ta.accepted_level!=3) AND
        u.`created-time`>'2021-10-25 07:00:00' AND
        u.`created-time`<(NOW() - INTERVAL 204 HOUR) AND
        a.user_id IS NULL AND
        o.user_id IS NULL
        ORDER BY u.`created-time`
    LIMIT 1;

    IF uID IS NOT NULL THEN
        DELETE FROM GoogleUserDetails WHERE email=mail;
        DELETE FROM Users WHERE id=uID;
    END IF;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getRecordWarningUsers`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getRecordWarningUsers`()
BEGIN
    SELECT
        u.id,
        `display-name` AS display_name,
        email,
        u.password,
        biography,
        (SELECT `en-name`
            FROM Languages
            WHERE id=u.language_id) AS languageName,
        (SELECT code
            FROM Languages
            WHERE id=u.language_id) AS languageCode,
        (SELECT `en-name`
            FROM Countries
            WHERE id=u.country_id)  AS countryName,
        (SELECT code
            FROM Countries
            WHERE id=u.country_id)  AS countryCode,
        nonce,
        `created-time` AS created_time
    FROM      Users                u
    LEFT JOIN TermsAcceptedUsers  ta ON u.id=ta.user_id
    LEFT JOIN WillBeDeletedUsers wdu ON u.id=wdu.user_id
    WHERE
        (ta.user_id IS NULL OR ta.accepted_level!=3) AND
        u.`created-time`>'2021-10-25 07:00:00' AND
        u.`created-time`<(NOW() - INTERVAL 12 HOUR) AND
        wdu.user_id IS NULL;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insertWillBeDeletedUser`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertWillBeDeletedUser`(IN uID INT)
BEGIN
    INSERT INTO WillBeDeletedUsers (user_id, date_warned) VALUES (uID, NOW());
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `user_has_strategic_languages`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `user_has_strategic_languages`(IN `userID` INT)
BEGIN
    SELECT
        uqp.*,
        sco.nigeria
    FROM UserQualifiedPairs uqp
    JOIN strategic_cut_offs sco ON uqp.language_code_source=sco.language_code_source OR uqp.language_code_target=sco.language_code_target
    WHERE
        uqp.user_id=userID
    ORDER BY sco.nigeria DESC, uqp.language_code_target ASC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_points_for_badges`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_points_for_badges`(IN uID INT)
BEGIN
    SELECT
        u.id AS user_id,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`,  '') AS last_name,
        CONCAT(IFNULL(i.`first-name`, ''), ' ', IFNULL(i.`last-name`,  '')) AS name,
        IFNULL(SUM(IF(t.`task-type_id`=2 AND t.`task-status_id`=4, t.`word-count`, 0)), 0) AS words_translated,
        IFNULL(SUM(IF(t.`task-type_id`=3 AND t.`task-status_id`=4, t.`word-count`, 0)), 0) AS words_proofread,
        IFNULL(SUM(IF(tp.task_id IS NULL AND (t.`task-type_id`= 2 OR  t.`task-type_id`= 3) AND t.`task-status_id`= 4, t.`word-count`, 0)), 0) + (SELECT IFNULL(SUM(pd.wordstranslated), 0) FROM prozdata pd WHERE u.id=pd.user_id) AS words_donated,
        ROUND(
            IFNULL(SUM(IF(t.`task-type_id`=2 AND tp.task_id IS NULL AND t.`task-status_id`=4, t.`word-count`, 0)), 0) +
            IFNULL(SUM(IF(t.`task-type_id`=3 AND tp.task_id IS NULL AND t.`task-status_id`=4, t.`word-count`, 0)), 0)*0.5 +
            (SELECT IFNULL(SUM(pd.wordstranslated), 0) FROM prozdata pd WHERE u.id=pd.user_id) +
            (SELECT IFNULL(SUM(ap.points), 0) FROM adjust_points ap WHERE u.id=ap.user_id)
        ) AS recognition_points,
        ROUND(
            IFNULL(SUM(
                IF(
                    t.`task-type_id`=2 AND
                    tp.task_id IS NULL AND
                    t.`task-status_id`=4 AND
                    sco.start IS NOT NULL AND
                    t.`created-time`>=sco.start,
                    t.`word-count`, 0)
            ), 0) +
            IFNULL(SUM(
                IF(
                    t.`task-type_id`=3 AND
                    tp.task_id IS NULL AND
                    t.`task-status_id`=4 AND
                    sco.start IS NOT NULL AND
                    t.`created-time`>=sco.start,
                    t.`word-count`, 0)
            ), 0)*0.5 +
            (SELECT IFNULL(SUM(ap.points), 0) FROM adjust_points_strategic ap WHERE u.id=ap.user_id)
        ) AS strategic_points,
        0 AS taskscompleted
    FROM Tasks       t
    JOIN TaskClaims tc ON t.id=tc.task_id
    JOIN Users       u ON tc.user_id=u.id
    JOIN UserPersonalInformation i ON u.id=i.user_id
    LEFT JOIN TaskPaids tp ON t.id=tp.task_id
    LEFT JOIN strategic_cut_offs sco ON t.`language_id-source`=sco.`language_id-source` OR t.`language_id-target`=sco.`language_id-target`
    WHERE
        u.id=uID
    GROUP BY u.id

UNION

    SELECT
        u.id AS user_id,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`,  '') AS last_name,
        CONCAT(IFNULL(i.`first-name`, ''), ' ', IFNULL(i.`last-name`,  '')) AS name,
        0 AS words_translated,
        0 AS words_proofread,
        (SELECT IFNULL(SUM(pd.wordstranslated), 0) FROM prozdata pd WHERE u.id=pd.user_id) AS words_donated,
        ROUND(
            (SELECT IFNULL(SUM(pd.wordstranslated), 0) FROM prozdata pd WHERE u.id=pd.user_id) +
            (SELECT IFNULL(SUM(ap.points), 0) FROM adjust_points ap WHERE u.id=ap.user_id)
        ) AS recognition_points,
        ROUND(
            (SELECT IFNULL(SUM(ap.points), 0) FROM adjust_points_strategic ap WHERE u.id=ap.user_id)
        ) AS strategic_points,
        0 AS taskscompleted
    FROM Users                        u
    JOIN      UserPersonalInformation i ON u.id=i.user_id
    LEFT JOIN TaskClaims             tc ON u.id=tc.user_id
    WHERE
        u.id=uID AND
        tc.user_id IS NULL
    GROUP BY u.id;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_paid_status`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_paid_status`(IN tID BIGINT)
BEGIN
    SELECT * FROM TaskPaids WHERE task_id=tID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_paid_status`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_paid_status`(IN tID BIGINT)
BEGIN
    INSERT INTO TaskPaids VALUES (tID, 1);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `clear_paid_status`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `clear_paid_status`(IN tID BIGINT)
BEGIN
    DELETE FROM TaskPaids WHERE task_id=tID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_all_as_paid`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_all_as_paid`(IN pID INT)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM Tasks JOIN TaskPaids ON id=task_id WHERE project_id=pID) THEN
        SELECT 1 AS result;
    ELSEIF 1 = (SELECT MIN(IF(task_id IS NULL, 0, 1)) FROM Tasks LEFT JOIN TaskPaids ON id=task_id WHERE project_id=pID) THEN
        SELECT 2 AS result;
    ELSE
        SELECT 0 AS result;
    END IF;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_all_as_paid`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_all_as_paid`(IN pID INT)
BEGIN
    INSERT INTO TaskPaids (SELECT id, 1 FROM Tasks WHERE project_id=pID);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_revision_as_paid`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_revision_as_paid`(IN pID INT)
BEGIN
    INSERT INTO TaskPaids (SELECT id, 1 FROM Tasks WHERE project_id=pID AND `task-type_id`=3);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `clear_all_as_paid`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `clear_all_as_paid`(IN pID INT)
BEGIN
    DELETE FROM TaskPaids WHERE task_id IN (SELECT id FROM Tasks WHERE project_id=pID);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_points_for_badges_details`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_points_for_badges_details`(IN uID INT)
BEGIN
SELECT
    main.user_id,
    main.email,
    main.first_name,
    main.last_name,
    main.name,
    main.words_translated,
    main.words_proofread,
    IFNULL(proz.words_proz, 0) AS words_proz,
    IFNULL(adjust.points_adjustment, 0) AS points_adjustment,
    IFNULL(adjust_strategic.points_adjustment_strategic, 0) AS points_adjustment_strategic,
    main.words_paid_uncounted,
    main.words_not_complete_uncounted,
    main.words_donated_unadjusted + IFNULL(proz.words_proz, 0) AS words_donated,
    main.points_recognition_unadjusted + IFNULL(proz.words_proz, 0) + IFNULL(adjust.points_adjustment, 0) AS points_recognition,
    main.points_strategic_unadjusted + IFNULL(adjust_strategic.points_adjustment_strategic, 0) AS points_strategic
FROM
(
    SELECT
        u.id AS user_id,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`,  '') AS last_name,
        CONCAT(IFNULL(i.`first-name`, ''), ' ', IFNULL(i.`last-name`,  '')) AS name,
        SUM(IF(t.`task-type_id`=2 AND t.`task-status_id`=4, t.`word-count`, 0)) AS words_translated,
        SUM(IF(t.`task-type_id`=3 AND t.`task-status_id`=4, t.`word-count`, 0)) AS words_proofread,
        SUM(IF(tp.task_id IS NULL     AND (t.`task-type_id`= 2 OR  t.`task-type_id`= 3) AND t.`task-status_id`= 4, t.`word-count`, 0)) AS words_donated_unadjusted,
        SUM(IF(tp.task_id IS NOT NULL AND (t.`task-type_id`= 2 OR  t.`task-type_id`= 3) AND t.`task-status_id`= 4, t.`word-count`, 0)) AS words_paid_uncounted,
        SUM(IF(                           (t.`task-type_id`!=2 AND t.`task-type_id`!=3) OR  t.`task-status_id`!=4, t.`word-count`, 0)) AS words_not_complete_uncounted,
        ROUND(
            SUM(IF(t.`task-type_id`=2 AND tp.task_id IS NULL AND t.`task-status_id`=4, t.`word-count`, 0)) +
            SUM(IF(t.`task-type_id`=3 AND tp.task_id IS NULL AND t.`task-status_id`=4, t.`word-count`, 0))*0.5
        ) AS points_recognition_unadjusted,
        ROUND(
            SUM(
                IF(
                    t.`task-type_id`=2 AND
                    tp.task_id IS NULL AND
                    t.`task-status_id`=4 AND
                    sco.start IS NOT NULL AND
                    t.`created-time`>=sco.start,
                    t.`word-count`, 0)
            ) +
            SUM(
                IF(
                    t.`task-type_id`=3 AND
                    tp.task_id IS NULL AND
                    t.`task-status_id`=4 AND
                    sco.start IS NOT NULL AND
                    t.`created-time`>=sco.start,
                    t.`word-count`, 0)
            )*0.5
        ) AS points_strategic_unadjusted
    FROM Tasks       t
    JOIN TaskClaims tc ON t.id=tc.task_id
    JOIN Users       u ON tc.user_id=u.id
    JOIN UserPersonalInformation i ON u.id=i.user_id
    LEFT JOIN TaskPaids tp ON t.id=tp.task_id
    LEFT JOIN strategic_cut_offs sco ON t.`language_id-source`=sco.`language_id-source` OR t.`language_id-target`=sco.`language_id-target`
    WHERE u.id=uID
    GROUP BY u.id
) AS main
LEFT JOIN
(
    SELECT
        u.id AS user_id,
        SUM(pd.wordstranslated) AS words_proz
    FROM Users     u
    JOIN prozdata pd ON u.id=pd.user_id
    WHERE u.id=uID
    GROUP BY u.id
) AS proz ON main.user_id=proz.user_id
LEFT JOIN
(
    SELECT
        u.id AS user_id,
        SUM(ap.points) AS points_adjustment
    FROM Users     u
    JOIN adjust_points ap ON u.id=ap.user_id
    WHERE u.id=uID
    GROUP BY u.id
) AS adjust ON main.user_id=adjust.user_id
LEFT JOIN
(
    SELECT
        u.id AS user_id,
        SUM(ap.points) AS points_adjustment_strategic
    FROM Users                    u
    JOIN adjust_points_strategic ap ON u.id=ap.user_id
    WHERE u.id=uID
    GROUP BY u.id
) AS adjust_strategic ON main.user_id=adjust_strategic.user_id

UNION

SELECT
    proz.user_id,
    proz.email,
    proz.first_name,
    proz.last_name,
    proz.name,
    0 AS words_translated,
    0 AS words_proofread,
    IFNULL(proz.words_proz, 0) AS words_proz,
    IFNULL(adjust.points_adjustment, 0) AS points_adjustment,
    IFNULL(adjust_strategic.points_adjustment_strategic, 0) AS points_adjustment_strategic,
    0 AS words_paid_uncounted,
    0 AS words_not_complete_uncounted,
    IFNULL(proz.words_proz, 0) AS words_donated,
    IFNULL(proz.words_proz, 0) + IFNULL(adjust.points_adjustment, 0) AS points_recognition,
    IFNULL(adjust_strategic.points_adjustment_strategic, 0) AS points_strategic
FROM
(
    SELECT
        u.id AS user_id,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`,  '') AS last_name,
        CONCAT(IFNULL(i.`first-name`, ''), ' ', IFNULL(i.`last-name`,  '')) AS name,
        SUM(pd.wordstranslated) AS words_proz
    FROM Users                   u
    JOIN prozdata               pd ON u.id=pd.user_id
    JOIN UserPersonalInformation i ON u.id=i.user_id
    WHERE u.id=uID
    GROUP BY u.id
) AS proz
JOIN
(
    SELECT
        u.id AS user_id
    FROM      Users       u
    LEFT JOIN TaskClaims tc ON u.id=tc.user_id
    WHERE
        tc.user_id IS NULL
        AND u.id=uID
    GROUP BY u.id
) AS main ON proz.user_id=main.user_id
LEFT JOIN
(
    SELECT
        u.id AS user_id,
        SUM(ap.points) AS points_adjustment
    FROM Users     u
    JOIN adjust_points ap ON u.id=ap.user_id
    WHERE u.id=uID
    GROUP BY u.id
) AS adjust ON main.user_id=adjust.user_id
LEFT JOIN
(
    SELECT
        u.id AS user_id,
        SUM(ap.points) AS points_adjustment_strategic
    FROM Users                    u
    JOIN adjust_points_strategic ap ON u.id=ap.user_id
    WHERE u.id=uID
    GROUP BY u.id
) AS adjust_strategic ON main.user_id=adjust_strategic.user_id;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_user_services`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_user_services`(IN uID INT)
BEGIN
    SELECT
        s.id,
        s.desc,
        IF(us.user_id IS NOT NULL, 1, 0) AS state
    FROM      Services      s
    LEFT JOIN UserServices us ON s.id=us.service_id AND us.user_id=uID
    ORDER BY s.ord;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `add_user_service`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_user_service`(IN uID INT, IN sID INT)
BEGIN
    INSERT INTO UserServices (user_id, service_id) VALUES (uID, sID);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `remove_user_service`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `remove_user_service`(IN uID INT, IN sID INT)
BEGIN
    DELETE FROM UserServices WHERE user_id=uID AND service_id=sID;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `insert_project_complete_date`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_project_complete_date`(IN pID INT)
BEGIN
    INSERT INTO project_complete_dates (project_id, status, complete_date) VALUES (pID, 0, '1000-01-01 00:00:00');
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `update_project_complete_date`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_project_complete_date`(IN tID BIGINT)
BEGIN
    SELECT project_id INTO @pID FROM Tasks WHERE id=tID;

    SET @project_complete=0;

    SELECT
        IFNULL(MAX(tcd.complete_date), '1000-01-01 00:00:00'),
        MIN(IF(IFNULL(t.`task-status_id`, 0)!=4, 0, 1))
        INTO @project_complete_date, @project_complete
    FROM      Tasks             t
    LEFT JOIN TaskCompleteDates tcd ON t.id=tcd.task_id
    WHERE
        t.project_id=@pID
    GROUP BY t.project_id;

    IF @project_complete THEN
        UPDATE project_complete_dates SET status=1, complete_date=@project_complete_date WHERE project_id=@pID;
    ELSE
        UPDATE project_complete_dates SET status=0 WHERE project_id=@pID;
    END IF;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `update_project_complete_date_project`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_project_complete_date_project`(IN pID INT)
BEGIN
    SET @project_complete=0;

    SELECT
        IFNULL(MAX(tcd.complete_date), '1000-01-01 00:00:00'),
        MIN(IF(IFNULL(t.`task-status_id`, 0)!=4, 0, 1))
        INTO @project_complete_date, @project_complete
    FROM      Tasks             t
    LEFT JOIN TaskCompleteDates tcd ON t.id=tcd.task_id
    WHERE
        t.project_id=pID
    GROUP BY t.project_id;

    IF @project_complete THEN
        UPDATE project_complete_dates SET status=1, complete_date=@project_complete_date WHERE project_id=pID;
    ELSE
        UPDATE project_complete_dates SET status=0 WHERE project_id=pID;
    END IF;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `reset_project_complete`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `reset_project_complete`(IN tID BIGINT)
BEGIN
    UPDATE project_complete_dates SET status=0 WHERE project_id=(SELECT project_id FROM Tasks WHERE id=tID LIMIT 1);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `get_selections`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_selections`()
BEGIN
    SELECT * FROM selections;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `set_taskclaims_required_to_make_claimable`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `set_taskclaims_required_to_make_claimable`(IN tID BIGINT, IN claimable_tID BIGINT, IN pID INT)
BEGIN
    INSERT INTO taskclaims_required_to_make_claimable (task_id, claimable_task_id, project_id) VALUES (tID, claimable_tID, pID);
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `is_task_claimable`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `is_task_claimable`(IN claimable_tID BIGINT)
BEGIN
    IF EXISTS (SELECT 1 FROM make_claimable_regardless WHERE claimable_task_id=claimable_tID) THEN
        SELECT 1 AS result;
    ELSEIF NOT EXISTS (
        SELECT
        FROM taskclaims_required_to_make_claimable tc
        JOIN Tasks                                  t ON tc.task_id=t.id
        WHERE
            claimable_task_id=claimable_tID AND
            t.`task-status_id`<3
    ) THEN
        SELECT 1 AS result;
    ELSE
        SELECT 0 AS result;
    END IF;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `make_tasks_claimable`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `make_tasks_claimable`(IN pID INT)
BEGIN
    UPDATE Tasks t0 SET t0.`task-status_id`=2 WHERE t0.id IN (
        SELECT
            t.id
        FROM      Tasks                                  t
        LEFT JOIN taskclaims_required_to_make_claimable tc ON t.id=tc.claimable_task_id
        LEFT JOIN Tasks                                 t1 ON tc.task_id=t1.id
        WHERE
            p.project_id=pID AND
            t.`task-status_id`=1
        GROUP BY t.id
        HAVING SUM(IF(t1.`task-status_id`<3, 1, 0))=0
    );
END//
DELIMITER ;


/*---------------------------------------end of procs----------------------------------------------*/


/*---------------------------------------start of triggers-----------------------------------------*/


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


DROP TRIGGER IF EXISTS `defaultUserName`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `defaultUserName` BEFORE INSERT ON `Users` FOR EACH ROW BEGIN
if new.`display-name` is null then set new.`display-name` = substring_index(new.email,'@',1); end if;
END//
DELIMITER ;


SET SQL_MODE=@OLD_SQL_MODE;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;


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


/* Recognition SQL for Metabase Report, given to Manuel
SELECT
    main.user_id,
    main.email,
    main.first_name,
    main.last_name,
    main.name,
    main.words_translated,
    main.words_proofread,
    IFNULL(proz.words_proz, 0) AS words_proz,
    IFNULL(adjust.points_adjustment, 0) AS points_adjustment,
    IFNULL(adjust_strategic.points_adjustment_strategic, 0) AS points_adjustment_strategic,
    main.words_paid_uncounted,
    main.words_not_complete_uncounted,
    main.words_donated_unadjusted + IFNULL(proz.words_proz, 0) AS words_donated,
    main.points_recognition_unadjusted + IFNULL(proz.words_proz, 0) + IFNULL(adjust.points_adjustment, 0) AS points_recognition,
    main.points_strategic_unadjusted + IFNULL(adjust_strategic.points_adjustment_strategic, 0) AS points_strategic
FROM
(
    SELECT
        u.id AS user_id,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`,  '') AS last_name,
        CONCAT(IFNULL(i.`first-name`, ''), ' ', IFNULL(i.`last-name`,  '')) AS name,
        SUM(IF(t.`task-type_id`=2 AND t.`task-status_id`=4, t.`word-count`, 0)) AS words_translated,
        SUM(IF(t.`task-type_id`=3 AND t.`task-status_id`=4, t.`word-count`, 0)) AS words_proofread,
        SUM(IF(tp.task_id IS NULL     AND (t.`task-type_id`= 2 OR  t.`task-type_id`= 3) AND t.`task-status_id`= 4, t.`word-count`, 0)) AS words_donated_unadjusted,
        SUM(IF(tp.task_id IS NOT NULL AND (t.`task-type_id`= 2 OR  t.`task-type_id`= 3) AND t.`task-status_id`= 4, t.`word-count`, 0)) AS words_paid_uncounted,
        SUM(IF(                           (t.`task-type_id`!=2 AND t.`task-type_id`!=3) OR  t.`task-status_id`!=4, t.`word-count`, 0)) AS words_not_complete_uncounted,
        ROUND(
            SUM(IF(t.`task-type_id`=2 AND tp.task_id IS NULL AND t.`task-status_id`=4, t.`word-count`, 0)) +
            SUM(IF(t.`task-type_id`=3 AND tp.task_id IS NULL AND t.`task-status_id`=4, t.`word-count`, 0))*0.5
        ) AS points_recognition_unadjusted,
        ROUND(
            SUM(
                IF(
                    t.`task-type_id`=2 AND
                    tp.task_id IS NULL AND
                    t.`task-status_id`=4 AND
                    sco.start IS NOT NULL AND
                    t.`created-time`>=sco.start,
                    t.`word-count`, 0)
            ) +
            SUM(
                IF(
                    t.`task-type_id`=3 AND
                    tp.task_id IS NULL AND
                    t.`task-status_id`=4 AND
                    sco.start IS NOT NULL AND
                    t.`created-time`>=sco.start,
                    t.`word-count`, 0)
            )*0.5
        ) AS points_strategic_unadjusted
    FROM Tasks       t
    JOIN TaskClaims tc ON t.id=tc.task_id
    JOIN Users       u ON tc.user_id=u.id
    JOIN UserPersonalInformation i ON u.id=i.user_id
    LEFT JOIN TaskPaids tp ON t.id=tp.task_id
    LEFT JOIN strategic_cut_offs sco ON t.`language_id-source`=sco.`language_id-source` OR t.`language_id-target`=sco.`language_id-target`
    GROUP BY u.id
) AS main
LEFT JOIN
(
    SELECT
        u.id AS user_id,
        SUM(pd.wordstranslated) AS words_proz
    FROM Users     u
    JOIN prozdata pd ON u.id=pd.user_id
    GROUP BY u.id
) AS proz ON main.user_id=proz.user_id
LEFT JOIN
(
    SELECT
        u.id AS user_id,
        SUM(ap.points) AS points_adjustment
    FROM Users     u
    JOIN adjust_points ap ON u.id=ap.user_id
    GROUP BY u.id
) AS adjust ON main.user_id=adjust.user_id
LEFT JOIN
(
    SELECT
        u.id AS user_id,
        SUM(ap.points) AS points_adjustment_strategic
    FROM Users                    u
    JOIN adjust_points_strategic ap ON u.id=ap.user_id
    GROUP BY u.id
) AS adjust_strategic ON main.user_id=adjust_strategic.user_id

UNION

SELECT
    proz.user_id,
    proz.email,
    proz.first_name,
    proz.last_name,
    proz.name,
    0 AS words_translated,
    0 AS words_proofread,
    IFNULL(proz.words_proz, 0) AS words_proz,
    IFNULL(adjust.points_adjustment, 0) AS points_adjustment,
    IFNULL(adjust_strategic.points_adjustment_strategic, 0) AS points_adjustment_strategic,
    0 AS words_paid_uncounted,
    0 AS words_not_complete_uncounted,
    IFNULL(proz.words_proz, 0) AS words_donated,
    IFNULL(proz.words_proz, 0) + IFNULL(adjust.points_adjustment, 0) AS points_recognition,
    IFNULL(adjust_strategic.points_adjustment_strategic, 0) AS points_strategic
FROM
(
    SELECT
        u.id AS user_id,
        u.email,
        IFNULL(i.`first-name`, '') AS first_name,
        IFNULL(i.`last-name`,  '') AS last_name,
        CONCAT(IFNULL(i.`first-name`, ''), ' ', IFNULL(i.`last-name`,  '')) AS name,
        SUM(pd.wordstranslated) AS words_proz
    FROM Users                   u
    JOIN prozdata               pd ON u.id=pd.user_id
    JOIN UserPersonalInformation i ON u.id=i.user_id
    GROUP BY u.id
) AS proz
JOIN
(
    SELECT
        u.id AS user_id
    FROM      Users       u
    LEFT JOIN TaskClaims tc ON u.id=tc.user_id
    WHERE
        tc.user_id IS NULL
    GROUP BY u.id
) AS main ON proz.user_id=main.user_id
LEFT JOIN
(
    SELECT
        u.id AS user_id,
        SUM(ap.points) AS points_adjustment
    FROM Users     u
    JOIN adjust_points ap ON u.id=ap.user_id
    GROUP BY u.id
) AS adjust ON main.user_id=adjust.user_id
LEFT JOIN
(
    SELECT
        u.id AS user_id,
        SUM(ap.points) AS points_adjustment_strategic
    FROM Users                    u
    JOIN adjust_points_strategic ap ON u.id=ap.user_id
    GROUP BY u.id
) AS adjust_strategic ON main.user_id=adjust_strategic.user_id;
*/


SET FOREIGN_KEY_CHECKS=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
