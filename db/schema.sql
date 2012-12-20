-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.24-0ubuntu0.12.04.1 - (Ubuntu)
-- Server OS:                    debian-linux-gnu
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2012-08-07 10:35:31
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
SET FOREIGN_KEY_CHECKS=0;

DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='user'and c.TABLE_SCHEMA = database())) THEN  
        IF EXISTS (SELECT * FROM information_schema.COLUMNS cols
                WHERE cols.TABLE_SCHEMA = database()
                AND cols.TABLE_NAME = 'user' 
                AND cols.COLUMN_NAME = 'native_language') then
            ALTER TABLE `user` 
                DROP COLUMN `native_language`,
                ADD COLUMN `native_lang_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'foreign key from the `language` table',
                ADD COLUMN `native_region_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT 'foreign key from the `country` table';
        end if;
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc 
                        where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='user'and tc.CONSTRAINT_NAME='FK_user_language') then
            ALTER TABLE `user`
            ADD CONSTRAINT `FK_user_language` FOREIGN KEY (`native_lang_id`) REFERENCES `language` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        end if;
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc 
                        where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='user'and tc.CONSTRAINT_NAME='FK_user_country') then
            ALTER TABLE `user`
            ADD CONSTRAINT `FK_user_country` FOREIGN KEY (`native_region_id`) REFERENCES `country` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        end if;

        IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='user') THEN
                
                ALTER TABLE `user`
                DROP FOREIGN KEY `FK_user_language`,
                DROP FOREIGN KEY `FK_user_country`;
					 
					 ALTER TABLE `user_badges`
				 	 DROP FOREIGN KEY `FK_user_badges_user`;

                ALTER TABLE `user_notifications`
				    DROP FOREIGN KEY `FK_user_notifications_user`;
                
                ALTER TABLE `user_tag`
					 DROP FOREIGN KEY `FK_user_tag_user`;
					 
					 ALTER TABLE `user_task_score`
				  	 DROP FOREIGN KEY `FK_user_task_score_user`;

				  	 ALTER TABLE `task_claim`
					 DROP FOREIGN KEY `FK_task_claim_user`;
					 
					 ALTER TABLE `organisation_member`
					 DROP FOREIGN KEY `FK_organisation_member_user`;
					 
					 ALTER TABLE `org_request_queue`
					 DROP FOREIGN KEY `FK_org_request_queue_user`;
					 
					 ALTER TABLE `password_reset_requests`
					 DROP FOREIGN KEY `FK_password_reset_user`;
					 
					 ALTER TABLE `task_file_version`
					 DROP FOREIGN KEY `FK_task_file_version_user`;
					 
					 ALTER TABLE `task_file_version_download`
					 DROP FOREIGN KEY `FK_task_file_version_download_user`;
					 
					 ALTER TABLE `translator`
	             DROP FOREIGN KEY `FK_translator_user`;
                
					 RENAME TABLE `user` TO `Users`;
                ALTER TABLE `Users` 
					 change `user_id` `id` int unsigned NOT NULL AUTO_INCREMENT,
					 CHANGE `email` `email` VARCHAR(128) NOT NULL AFTER `display-name`,
                change `display_name` `display-name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
                change `native_lang_id` `language_id` INT UNSIGNED NULL DEFAULT NULL,
                change `native_region_id` `country_id` INT UNSIGNED NULL DEFAULT NULL,
                change `created_time` `created-time` DATETIME NOT NULL;

                ALTER TABLE `Users`
                ADD CONSTRAINT `FK_user_language` FOREIGN KEY (`language_id`) REFERENCES `Languages` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
                ADD CONSTRAINT `FK_user_country` FOREIGN KEY (`country_id`) REFERENCES `Countries` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        END IF;
    END IF;
END//

DELIMITER ;

CALL alterTable();

DROP PROCEDURE alterTable;


-- Dumping structure for table Solas-Match-test.Users
CREATE TABLE IF NOT EXISTS `Users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `display-name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `password` char(128) COLLATE utf8_unicode_ci NOT NULL,
  `biography` text COLLATE utf8_unicode_ci,
  `language_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT 'foreign key from the `Languages` table',
  `country_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT 'foreign key from the `Countries` table',
  `nonce` int(11) unsigned NOT NULL,
  `created-time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  CONSTRAINT `FK_user_language` FOREIGN KEY (`language_id`) REFERENCES `Languages` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_user_country` FOREIGN KEY (`country_id`) REFERENCES `Countries` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='archived_task'and c.TABLE_SCHEMA = database())) THEN
            if not exists (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='archived_task'and c.TABLE_SCHEMA = database() and (c.COLUMN_NAME="impact" or c.COLUMN_NAME="reference_page")) then
                ALTER TABLE `archived_task`
                add column `impact` text COLLATE utf8_unicode_ci NOT NULL,
                add column`reference_page` varchar(128) COLLATE utf8_unicode_ci NOT NULL;
            end if;
            if exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='archived_task' and tc.CONSTRAINT_NAME='source') then
                ALTER TABLE `archived_task`
                DROP INDEX `source`;
            end if;
            if exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='archived_task' and tc.CONSTRAINT_NAME='target') then
                ALTER TABLE `archived_task`
                DROP INDEX `target`;
            end if;
            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='archived_task' and tc.CONSTRAINT_NAME='task_id') then
                ALTER TABLE `archived_task`
                ADD UNIQUE INDEX `task_id` (`task_id`);
            end if;
            if not exists (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='archived_task'and c.TABLE_SCHEMA = database() and c.COLUMN_NAME="user_id") then
                ALTER TABLE `archived_task`
                add column`user_id` INT(10) UNSIGNED DEFAULT NULL;
            end if;


            IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='archived_task') THEN
                    RENAME TABLE `archived_task` TO `ArchivedTasks`;
                    ALTER TABLE `ArchivedTasks` change `archived_task_id` `id` BIGINT;
                    ALTER TABLE `ArchivedTasks` change `reference_page` `reference-page` VARCHAR (128);
                    ALTER TABLE `ArchivedTasks` change `word_count` `word-count` INT;
                    ALTER TABLE `ArchivedTasks` change `created_time` `created-time` DATETIME;
                    ALTER TABLE `ArchivedTasks` change `archived_time` `archived-time` DATETIME;
            END IF;

            ALTER TABLE `ArchivedTasks` 
            ENGINE InnoDB, CONVERT TO CHARSET utf8 COLLATE 'utf8_unicode_ci';
    END IF;
END//

DELIMITER ;

CALL alterTable();

DROP PROCEDURE alterTable;

-- Dumping structure for table Solas-Match-test.ArchivedTasks
CREATE TABLE IF NOT EXISTS `ArchivedTasks` (
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`task_id` BIGINT(20) NOT NULL,
	`organisation_id` INT(10) UNSIGNED NOT NULL,
	`title` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
	`impact` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
	`reference-page` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`word-count` INT(10) UNSIGNED NULL DEFAULT NULL,
	`source_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'foreign key from the `Languages` table',
	`target_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'foreign key from the `Languages` table',
	`created-time` DATETIME NOT NULL,
	`archived-time` DATETIME NOT NULL,
        `user_id` INT(10) UNSIGNED DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `task_id` (`task_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Dumping data for table Solas-Match-test.ArchivedTasks: 0 rows
/*!40000 ALTER TABLE `ArchivedTasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `ArchivedTasks` ENABLE KEYS */;


DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='badges'and c.TABLE_SCHEMA = database())) THEN
            ALTER TABLE `badges` 
            CHANGE COLUMN `owner_id` `owner_id` INT(11) UNSIGNED NULL AFTER `badge_id`,
            ENGINE InnoDB, CONVERT TO CHARSET utf8 COLLATE 'utf8_unicode_ci';

            if not exists (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='badges'and c.TABLE_SCHEMA = database() and c.COLUMN_NAME='owner_id') then
                    ALTER TABLE `badges`
                        add column `owner_id` int(11) COLLATE utf8_unicode_ci DEFAULT NULL;
            end if;
            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='badges'and tc.CONSTRAINT_NAME='badge') then
                ALTER TABLE `badges`
                ADD UNIQUE INDEX `badge` (`owner_id`, `title`);
            end if;
            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='badges'and tc.CONSTRAINT_NAME='FK_badges_organisation') then
                ALTER TABLE `badges`
                ADD CONSTRAINT `FK_badges_organisation` FOREIGN KEY (`owner_id`) REFERENCES `organisation` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
            end if;

            IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='badges') THEN
                ALTER TABLE `badges`
					 DROP FOREIGN KEY `FK_badges_organisation`;
					
					 ALTER TABLE `user_badges`
					 DROP FOREIGN KEY `FK_user_badges_badges`;
					
					 RENAME TABLE `badges` TO `Badges`;
					
					 ALTER TABLE `Badges`
					 change `badge_id` `id` INT,
					 ADD CONSTRAINT `FK_badges_organisation` FOREIGN KEY (`owner_id`) REFERENCES `Organisations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;

            END IF;
    END IF;
END//

DELIMITER ;

CALL alterTable();

DROP PROCEDURE alterTable;


-- Dumping structure for table Solas-Match-test.Badges
CREATE TABLE IF NOT EXISTS `Badges` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`owner_id` INT(11) UNSIGNED NULL DEFAULT NULL,
	`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`description` MEDIUMTEXT NOT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `badge` (`owner_id`, `title`),
	CONSTRAINT `FK_badges_organisation` FOREIGN KEY (`owner_id`) REFERENCES `Organisations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

		REPLACE INTO `Badges` (`id`, `title`, `description`) VALUES
		(3, 'Profile-Filler', 'Filled in required info for user profile.'),
		(4, 'Registered', 'Successfully set up an account'),
		(5, 'Native-Language', 'Filled in your native language on your user profile.');



DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='language'and c.TABLE_SCHEMA = database())) THEN
            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='language'and tc.CONSTRAINT_NAME='code') then
                ALTER TABLE `language` 
                ADD UNIQUE INDEX `code` (`code`);
            end if;
            ALTER TABLE `language` 
            CHANGE COLUMN `code` `code` VARCHAR(3) NOT NULL COMMENT '"en", for example' AFTER `id`,
            ENGINE InnoDB, CONVERT TO CHARSET utf8 COLLATE 'utf8_unicode_ci';

            IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='language') THEN
                    RENAME TABLE `language` TO `Languages`;
                    ALTER TABLE `Languages` change `en_name` `en-name` VARCHAR (255);
            END IF;
    END IF;
END//

DELIMITER ;

CALL alterTable();

DROP PROCEDURE alterTable;


-- Dumping structure for table Solas-Match-test.Languages
CREATE TABLE IF NOT EXISTS `Languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(3) COLLATE utf8_unicode_ci NOT NULL COMMENT '"en", for example',
  `en-name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '"English", for example',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- Dumping data for table Solas-Match-test.Languages: 0 rows
/*!40000 ALTER TABLE `Languages` DISABLE KEYS */;
/*!40000 ALTER TABLE `Languages` ENABLE KEYS */;

DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='country'and c.TABLE_SCHEMA = database())) THEN
            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='country'and tc.CONSTRAINT_NAME='code') then
                ALTER TABLE `country` 
                ADD UNIQUE INDEX `code` (`code`);
            end if;
            ALTER TABLE `country` ENGINE InnoDB, CONVERT TO CHARSET utf8 COLLATE 'utf8_unicode_ci';

            IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='country') THEN
                    RENAME TABLE `country` TO `Countries`;
                    ALTER TABLE `Countries` change `en_name` `en-name` VARCHAR (255);
            END IF;
    END IF;
END//

DELIMITER ;

CALL alterTable();

DROP PROCEDURE alterTable;


CREATE TABLE IF NOT EXISTS `Countries` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`code` VARCHAR(2) NOT NULL COMMENT '"IE", for example',
	`en-name` VARCHAR(255) NOT NULL COMMENT '"Ireland", for example',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `code` (`code`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;


DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='organisation'and c.TABLE_SCHEMA = database())) THEN
            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='organisation'and tc.CONSTRAINT_NAME='name') then
                ALTER TABLE `organisation` 
                ADD UNIQUE INDEX `name` (`name`);
            else 
                ALTER TABLE `organisation` 
                DROP INDEX `name`,
                ADD UNIQUE INDEX `name` (`name`);
            end if;
            ALTER TABLE `organisation`
            CHANGE COLUMN `name` `name` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci' AFTER `id`,
            CHANGE COLUMN `home_page` `home_page` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci' AFTER `name`,
            CHANGE COLUMN `biography` `biography` VARCHAR(4096) NOT NULL COLLATE 'utf8_unicode_ci' AFTER `home_page`,
            ENGINE InnoDB, CONVERT TO CHARSET utf8 COLLATE 'utf8_unicode_ci';

            IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='organisation') THEN
                    RENAME TABLE `organisation` TO `Organisations`;
                    ALTER TABLE `Organisations` change `home_page` `home-page` VARCHAR (128);
            END IF;
    END IF;
END//

DELIMITER ;

CALL alterTable();

DROP PROCEDURE alterTable;

-- Dumping structure for table Solas-Match-test.Organisations
CREATE TABLE IF NOT EXISTS `Organisations` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`home-page` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`biography` VARCHAR(4096) NOT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Dumping data for table Solas-Match-test.Organisations: 0 rows
/*!40000 ALTER TABLE `Organisations` DISABLE KEYS */;
/*!40000 ALTER TABLE `Organisations` ENABLE KEYS */;


DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='organisation_member'and c.TABLE_SCHEMA = database())) THEN
            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='organisation_member'and tc.CONSTRAINT_NAME='user_id') then
                ALTER TABLE `organisation_member`
                ADD UNIQUE INDEX `user_id` (`user_id`, `organisation_id`);
            else 
                ALTER TABLE `organisation_member`
                DROP INDEX `user_id`,
                ADD UNIQUE INDEX `user_id` (`user_id`, `organisation_id`);
            end if;
            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='organisation_member'and tc.CONSTRAINT_NAME='FK_organisation_member_user') then
                ALTER TABLE `organisation_member`
                    ADD CONSTRAINT `FK_organisation_member_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE;
            end if; 
            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='organisation_member'and tc.CONSTRAINT_NAME='FK_organisation_member_organisation') then
                ALTER TABLE `organisation_member`
                ADD CONSTRAINT `FK_organisation_member_organisation` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
            end if; 
            ALTER TABLE `organisation_member` ENGINE InnoDB, CONVERT TO CHARSET utf8 COLLATE 'utf8_unicode_ci';

            IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='organisation_member') THEN
                    ALTER TABLE `organisation_member`
						  DROP FOREIGN KEY `FK_organisation_member_user`,
						  DROP FOREIGN KEY `FK_organisation_member_organisation`;
						  
						  
						  RENAME TABLE `organisation_member` TO `OrganisationMembers`;

                    ALTER TABLE `OrganisationMembers`
                    ADD CONSTRAINT `FK_organisation_member_organisation` FOREIGN KEY (`organisation_id`) REFERENCES `Organisations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
                    ADD CONSTRAINT `FK_organisation_member_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
            END IF;
    END IF;
END//

DELIMITER ;

CALL alterTable();

DROP PROCEDURE alterTable;


-- Dumping structure for table Solas-Match-test.OrganisationMembers
CREATE TABLE IF NOT EXISTS `OrganisationMembers` (
	`user_id` INT(10) UNSIGNED NOT NULL,
	`organisation_id` INT(10) UNSIGNED NOT NULL,
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	UNIQUE INDEX `user_id` (`user_id`, `organisation_id`),
	INDEX `FK_organisation_member_organisation` (`organisation_id`),
	CONSTRAINT `FK_organisation_member_organisation` FOREIGN KEY (`organisation_id`) REFERENCES `Organisations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_organisation_member_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Dumping data for table Solas-Match-test.OrganisationMembers: 0 rows
/*!40000 ALTER TABLE `OrganisationMembers` DISABLE KEYS */;
/*!40000 ALTER TABLE `OrganisationMembers` ENABLE KEYS */;


-- --------------------------------------------------------

DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='org_request_queue'and c.TABLE_SCHEMA = database())) THEN
            ALTER TABLE `org_request_queue` 
            CHANGE COLUMN `user_id` `user_id` INT(11) UNSIGNED NOT NULL AFTER `request_id`,
            CHANGE COLUMN `org_id` `org_id` INT(11) UNSIGNED NOT NULL AFTER `user_id`,
            ENGINE InnoDB, CONVERT TO CHARSET utf8 COLLATE 'utf8_unicode_ci';

            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='org_request_queue'and tc.CONSTRAINT_NAME='userRequest') then
                ALTER TABLE `org_request_queue`            
                ADD UNIQUE INDEX `userRequest` (`user_id`, `org_id`);
            end if;
            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='org_request_queue'and tc.CONSTRAINT_NAME='FK_org_request_queue_user') then
                ALTER TABLE `org_request_queue`
                ADD CONSTRAINT `FK_org_request_queue_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE;
            end if;
            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='org_request_queue'and tc.CONSTRAINT_NAME='FK_org_request_queue_organisation') then
                ALTER TABLE `org_request_queue`
                ADD CONSTRAINT `FK_org_request_queue_organisation` FOREIGN KEY (`org_id`) REFERENCES `organisation` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
            end if;

            IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='org_request_queue') THEN
                    ALTER TABLE `org_request_queue`
						  DROP FOREIGN KEY `FK_org_request_queue_organisation`,
						  DROP FOREIGN KEY `FK_org_request_queue_user`;
						  
						  RENAME TABLE `org_request_queue` TO `OrgRequests`;
                    ALTER TABLE `OrgRequests` 
						  change `request_id` `id` INT,
                    change `request_datetime` `request-datetime` TIMESTAMP;

                    ALTER TABLE `OrgRequests`
                    ADD CONSTRAINT `FK_org_request_queue_organisation1` FOREIGN KEY (`org_id`) REFERENCES `Organisations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
                    ADD CONSTRAINT `FK_org_request_queue_user2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
            END IF;
    END IF;     
END//

DELIMITER ;

CALL alterTable();

DROP PROCEDURE alterTable;

--
-- Table structure for table `OrgRequests`
--

CREATE TABLE IF NOT EXISTS `OrgRequests` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) UNSIGNED NOT NULL,
	`org_id` INT(11) UNSIGNED NOT NULL,
	`request-datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `userRequest` (`user_id`, `org_id`),
	INDEX `FK_org_request_queue_organisation` (`org_id`),
	CONSTRAINT `FK_org_request_queue_organisation` FOREIGN KEY (`org_id`) REFERENCES `Organisations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_org_request_queue_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='tag'and c.TABLE_SCHEMA = database())) THEN
            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='tag'and tc.CONSTRAINT_NAME='label') then
                ALTER TABLE `tag`
                ADD UNIQUE INDEX `label` (`label`);
            end if;
            ALTER TABLE `tag` ENGINE InnoDB, CONVERT TO CHARSET utf8 COLLATE 'utf8_unicode_ci';

            IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='tag') THEN
                    RENAME TABLE `tag` TO `Tags`;
                    ALTER TABLE `task_tag` drop FOREIGN key `FK_task_tag_tag`;
                    ALTER TABLE `user_tag` drop FOREIGN key `FK_user_tag_tag`;
                    ALTER TABLE `Tags` change `tag_id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;
            END IF;
    END IF;
END//

DELIMITER ;

CALL alterTable();

DROP PROCEDURE alterTable;

-- Dumping structure for table Solas-Match-test.Tags
CREATE TABLE IF NOT EXISTS `Tags` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`label` VARCHAR(50) NOT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `label` (`label`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Dumping data for table Solas-Match-test.Tags: 0 rows
/*!40000 ALTER TABLE `Tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `Tags` ENABLE KEYS */;


DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN   
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='task'and c.TABLE_SCHEMA = database())) THEN     
            if not exists (SELECT * FROM information_schema.COLUMNS c where c.TABLE_NAME='task'and c.TABLE_SCHEMA = database() and (c.COLUMN_NAME="sourceCountry" or c.COLUMN_NAME="targetCountry")) then
                                    ALTER TABLE `task`
                ADD COLUMN `sourceCountry` INT NULL AFTER `created_time`,
                ADD COLUMN `targetCountry` INT NULL AFTER `sourceCountry`;
                      end if;

                      ALTER TABLE `task` 
            CHANGE COLUMN `title` `title` VARCHAR(50) NOT NULL COLLATE 'utf8_unicode_ci' AFTER `organisation_id`,
            CHANGE COLUMN `sourceCountry` `sourceCountry` INT UNSIGNED NULL DEFAULT NULL AFTER `created_time`,
            CHANGE COLUMN `targetCountry` `targetCountry` INT UNSIGNED NULL DEFAULT NULL AFTER `sourceCountry`,
            CHANGE COLUMN `source_id` `source_id` INT UNSIGNED NULL COMMENT 'foreign key from the `language` table' AFTER `word_count`,
                      CHANGE COLUMN `target_id` `target_id` INT UNSIGNED NULL COMMENT 'foreign key from the `language` table' AFTER `source_id`,
            ENGINE InnoDB, CONVERT TO CHARSET utf8 COLLATE 'utf8_unicode_ci';
            if not exists (SELECT * FROM information_schema.COLUMNS c where c.TABLE_NAME='task'and c.TABLE_SCHEMA = database() and (c.COLUMN_NAME="impact" or c.COLUMN_NAME="reference_page")) then
                ALTER TABLE `task`
                add column `impact` text COLLATE utf8_unicode_ci NOT NULL,
                add column`reference_page` varchar(128) COLLATE utf8_unicode_ci NOT NULL;
                      end if;
            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task'and tc.CONSTRAINT_NAME='task') then
                ALTER TABLE `task`
                ADD UNIQUE INDEX `task` (`organisation_id`, `source_id`, `target_id`, `title`, `sourceCountry`, `targetCountry`);
            end if;
            if exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task'and tc.CONSTRAINT_NAME='task') then
                ALTER TABLE `task`
                DROP INDEX `task`,
                ADD UNIQUE INDEX `task` (`organisation_id`, `source_id`, `target_id`, `title`, `sourceCountry`, `targetCountry`);
            end if;
            if exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task'and tc.CONSTRAINT_NAME='source') then
                ALTER TABLE `task`
                DROP INDEX `source`;
            end if;
            if exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task'and tc.CONSTRAINT_NAME='target') then
                ALTER TABLE `task`
                DROP INDEX `target`;
            end if;
                      if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task'and tc.CONSTRAINT_NAME='FK_task_organisation') then
                ALTER TABLE `task`
                ADD CONSTRAINT `FK_task_organisation` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
            end if;
            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task'and tc.CONSTRAINT_NAME='FK_task_language') then
                ALTER TABLE `task`
                ADD CONSTRAINT `FK_task_language` FOREIGN KEY (`source_id`) REFERENCES `language` (`id`) ON UPDATE CASCADE;
            end if;
            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task'and tc.CONSTRAINT_NAME='FK_task_language_2') then
                ALTER TABLE `task`
                ADD CONSTRAINT `FK_task_language_2` FOREIGN KEY (`target_id`) REFERENCES `language` (`id`) ON UPDATE CASCADE;
            end if;
            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task'and tc.CONSTRAINT_NAME='FK_task_country') then
                ALTER TABLE `task`
                ADD CONSTRAINT `FK_task_country` FOREIGN KEY (`sourceCountry`) REFERENCES `country` (`id`) ON UPDATE CASCADE;
            end if;
            if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task'and tc.CONSTRAINT_NAME='FK_task_country2') then
                ALTER TABLE `task`
                ADD CONSTRAINT `FK_task_country2` FOREIGN KEY (`targetCountry`) REFERENCES `country` (`id`) ON UPDATE CASCADE;
            end if;

            IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='task') THEN
                    ALTER TABLE `task`
                    DROP FOREIGN KEY `FK_task_country2`,
                    DROP FOREIGN KEY `FK_task_country`,
                    DROP FOREIGN KEY `FK_task_language`,
                    DROP FOREIGN KEY `FK_task_language_2`,
                    DROP FOREIGN KEY `FK_task_organisation`;
						  
						  RENAME TABLE `task` TO `Tasks`;
                    
						  ALTER TABLE `Tasks`
						  change `reference_page` `reference-page` VARCHAR(128),
                    change `word_count` `word-count` INT UNSIGNED,
                    change `created_time` `created-time` DATETIME,
                    change `sourceCountry` `country_id-source` INT UNSIGNED,
                    change `targetCountry` `country_id-target` INT UNSIGNED;

                    ALTER TABLE `Tasks`
                    ADD CONSTRAINT `FK_task_country2` FOREIGN KEY (`country_id-target`) REFERENCES `Countries` (`id`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `FK_task_country` FOREIGN KEY (`country_id-source`) REFERENCES `Countries` (`id`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `FK_task_language` FOREIGN KEY (`source_id`) REFERENCES `Languages` (`id`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `FK_task_language_2` FOREIGN KEY (`target_id`) REFERENCES `Languages` (`id`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `FK_task_organisation` FOREIGN KEY (`organisation_id`) REFERENCES `Organisations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;

            END IF;
    END IF;
END//

DELIMITER ;

CALL alterTable();

DROP PROCEDURE alterTable;


-- Dumping structure for table Solas-Match-test.Tasks
CREATE TABLE IF NOT EXISTS `Tasks` (
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`organisation_id` INT(10) UNSIGNED NOT NULL,
	`title` VARCHAR(50) NOT NULL COLLATE 'utf8_unicode_ci',
	`impact` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
	`reference-page` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`word-count` INT(10) UNSIGNED NULL DEFAULT NULL,
	`source_id` INT(10) UNSIGNED NOT NULL COMMENT 'foreign key from the `Languages` table',
	`target_id` INT(10) UNSIGNED NOT NULL COMMENT 'foreign key from the `Languages` table',
	`created-time` DATETIME NOT NULL,
	`country_id-source` INT(11) UNSIGNED NULL DEFAULT NULL,
	`country_id-target` INT(11) UNSIGNED NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `Tasks` (`organisation_id`, `source_id`, `target_id`, `title`, `country_id-source`, `country_id-target`),
	INDEX `FK_task_language` (`source_id`),
	INDEX `FK_task_language_2` (`target_id`),
	INDEX `FK_task_country` (`country_id-source`),
	INDEX `FK_task_country2` (`country_id-target`),
	CONSTRAINT `FK_task_country2` FOREIGN KEY (`country_id-target`) REFERENCES `Countries` (`id`) ON UPDATE CASCADE,
   CONSTRAINT `FK_task_country` FOREIGN KEY (`country_id-source`) REFERENCES `Countries` (`id`) ON UPDATE CASCADE,
   CONSTRAINT `FK_task_language` FOREIGN KEY (`source_id`) REFERENCES `Languages` (`id`) ON UPDATE CASCADE,
   CONSTRAINT `FK_task_language_2` FOREIGN KEY (`target_id`) REFERENCES `Languages` (`id`) ON UPDATE CASCADE,
	CONSTRAINT `FK_task_organisation` FOREIGN KEY (`organisation_id`) REFERENCES `Organisations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table Solas-Match-test.Tasks: 0 rows
/*!40000 ALTER TABLE `Tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `Tasks` ENABLE KEYS */;


DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='task_claim'and c.TABLE_SCHEMA = database())) THEN  
        ALTER TABLE `task_claim`
            CHANGE COLUMN `task_id` `task_id` BIGINT(20) UNSIGNED NOT NULL AFTER `claim_id`,
            CHANGE COLUMN `user_id` `user_id` INT(11) UNSIGNED NOT NULL AFTER `task_id`, 
            ENGINE InnoDB, CONVERT TO CHARSET utf8 COLLATE 'utf8_unicode_ci';
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task_claim'and tc.CONSTRAINT_NAME='task') then
            ALTER TABLE `task_claim`
            ADD UNIQUE INDEX `task` (`task_id`, `user_id`);
        end if;
        if exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task_claim'and tc.CONSTRAINT_NAME='task_user') then
            ALTER TABLE `task_claim`
            DROP INDEX `task_user`;
        end if;
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task_claim'and tc.CONSTRAINT_NAME='FK_task_claim_task') then
            ALTER TABLE `task_claim`
            ADD CONSTRAINT `FK_task_claim_task` FOREIGN KEY (`task_id`) REFERENCES `task` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        end if;
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task_claim'and tc.CONSTRAINT_NAME='FK_task_claim_user') then
            ALTER TABLE `task_claim`
            ADD CONSTRAINT `FK_task_claim_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE;
        end if;

	IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='task_claim') THEN
		RENAME TABLE `task_claim` TO `TaskClaims`;
                ALTER TABLE `TaskClaims` change `claim_id` `id` INT;
                ALTER TABLE `TaskClaims` change `claimed_time` `claimed-time` DATETIME;

                ALTER TABLE `TaskClaims`
                DROP FOREIGN KEY `FK_task_claim_user`,
                ADD CONSTRAINT `FK_task_claim_user1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;

                ALTER TABLE `TaskClaims`
                DROP FOREIGN KEY `FK_task_claim_task`,
                ADD CONSTRAINT `FK_task_claim_task1` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
	END IF;
    END IF;

END//

DELIMITER ;

CALL alterTable();

DROP PROCEDURE alterTable;


-- Dumping structure for table Solas-Match-test.TaskClaims
CREATE TABLE IF NOT EXISTS `TaskClaims` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`task_id` BIGINT(20) UNSIGNED NOT NULL,
	`user_id` INT(11) UNSIGNED NOT NULL,
	`claimed-time` DATETIME NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `Tasks` (`task_id`, `user_id`),
	INDEX `FK_task_claim_user` (`user_id`),
	CONSTRAINT `FK_task_claim_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_task_claim_task` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table Solas-Match-test.TaskClaims: 0 rows
/*!40000 ALTER TABLE `TaskClaims` DISABLE KEYS */;
/*!40000 ALTER TABLE `TaskClaims` ENABLE KEYS */;

DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='task_file_version'and c.TABLE_SCHEMA = database())) THEN  
        if exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task_file_version'and tc.CONSTRAINT_NAME='task_id') then
            ALTER TABLE `task_file_version`
            DROP INDEX `task_id`;
        end if;
        if not exists (SELECT * FROM information_schema.COLUMNS c where c.TABLE_NAME='task_file_version'and c.TABLE_SCHEMA = database() and c.COLUMN_NAME='id') then
            ALTER TABLE `task_file_version`
            ADD COLUMN `id` BIGINT NULL AUTO_INCREMENT FIRST,
            ADD PRIMARY KEY (`id`);
		  end if;

		  ALTER TABLE `task_file_version` 
		  CHANGE COLUMN `task_id` `task_id` BIGINT(20) UNSIGNED NOT NULL AFTER `id`,
		  CHANGE COLUMN `user_id` `user_id` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'Null while we don\'t have logging in' AFTER `content_type`,
		  ENGINE InnoDB, CONVERT TO CHARSET utf8 COLLATE 'utf8_unicode_ci';
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task_file_version'and tc.CONSTRAINT_NAME='taskFile') then
            ALTER TABLE `task_file_version`
            ADD UNIQUE INDEX `taskFile` (`task_id`, `version_id`, `user_id`);
        end if;
        
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task_file_version'and tc.CONSTRAINT_NAME='PRIMARY') then
            ALTER TABLE `task_file_version`
            ADD PRIMARY KEY (`id`);
        end if;
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task_file_version'and tc.CONSTRAINT_NAME='FK_task_file_version_task') then
            ALTER TABLE `task_file_version`
				ADD CONSTRAINT `FK_task_file_version_task` FOREIGN KEY (`task_id`) REFERENCES `task` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        end if;
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task_file_version'and tc.CONSTRAINT_NAME='FK_task_file_version_user') then
            ALTER TABLE `task_file_version`
				ADD CONSTRAINT `FK_task_file_version_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE;
        end if;  

	IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='task_file_version') THEN
		RENAME TABLE `task_file_version` TO `TaskFileVersions`;
                ALTER TABLE `TaskFileVersions` change `content_type` `content-type` VARCHAR (255);
                ALTER TABLE `TaskFileVersions` change `upload_time` `upload-time` DATETIME;

                ALTER TABLE `TaskFileVersions`
                DROP FOREIGN KEY `FK_task_file_version_user`,
                ADD CONSTRAINT `FK_task_file_version_user1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;

                ALTER TABLE `TaskFileVersions`
                DROP FOREIGN KEY `FK_task_file_version_task`,
                ADD CONSTRAINT `FK_task_file_version_task1` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
	END IF; 
    END IF;
END//
DELIMITER ;

CALL alterTable();

DROP PROCEDURE alterTable;


-- Dumping structure for table Solas-Match-test.TaskFileVersions
CREATE TABLE IF NOT EXISTS `TaskFileVersions` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`task_id` BIGINT(20) UNSIGNED NOT NULL,
	`version_id` INT(11) NOT NULL COMMENT 'Gets incremented within the code',
	`filename` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
	`content-type` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`user_id` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'Null while we don\'t have logging in',
	`upload-time` DATETIME NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `taskFile` (`task_id`, `version_id`, `user_id`),
	INDEX `FK_task_file_version_user` (`user_id`),
	CONSTRAINT `FK_task_file_version_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_task_file_version_task` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='task_tag'and c.TABLE_SCHEMA = database())) THEN  
        ALTER TABLE `task_tag` ENGINE InnoDB, CONVERT TO CHARSET utf8 COLLATE 'utf8_unicode_ci';
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task_tag'and tc.CONSTRAINT_NAME='FK_task_tag_task') then
           ALTER TABLE `task_tag`
           ADD CONSTRAINT `FK_task_tag_task` FOREIGN KEY (`task_id`) REFERENCES `task` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        end if;
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='task_tag'and tc.CONSTRAINT_NAME='FK_task_tag_tag') then
           ALTER TABLE `task_tag`
           ADD CONSTRAINT `FK_task_tag_tag` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`) ON UPDATE CASCADE ON DELETE CASCADE;
        end if;

       IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='task_tag') THEN
           	ALTER TABLE `task_tag`
				DROP FOREIGN KEY `FK_task_tag_task`,
           	drop foreign key `FK_task_tag_tag`;

            RENAME TABLE `task_tag` TO `TaskTags`;
            ALTER TABLE `TaskTags` 
				change `created_time` `created-time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

            ALTER TABLE `TaskTags`
            ADD CONSTRAINT `FK_task_tag_tag` FOREIGN KEY (`tag_id`) REFERENCES `Tags` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            ADD CONSTRAINT `FK_task_tag_task` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
           
       END IF;
    END IF;
END//

DELIMITER ;

CALL alterTable();

-- Dumping structure for table Solas-Match-test.TaskTags
CREATE TABLE IF NOT EXISTS `TaskTags` (
	`task_id` BIGINT(20) UNSIGNED NOT NULL,
	`tag_id` INT(10) UNSIGNED NOT NULL,
	`created-time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	UNIQUE INDEX `TaskTags` (`task_id`, `tag_id`),
	INDEX `FK_task_tag_tag` (`tag_id`),
	CONSTRAINT `FK_task_tag_tag` FOREIGN KEY (`tag_id`) REFERENCES `Tags` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_task_tag_task` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Dumping data for table Solas-Match-test.TaskTags: 0 rows
/*!40000 ALTER TABLE `TaskTags` DISABLE KEYS */;
/*!40000 ALTER TABLE `TaskTags` ENABLE KEYS */;




DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='password_reset_requests'and c.TABLE_SCHEMA = database())) THEN
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='password_reset_requests'and tc.CONSTRAINT_NAME='FK_password_reset_user') then
            ALTER TABLE `password_reset_requests`
            ADD CONSTRAINT `FK_password_reset_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE;
        end if;
        if NOT EXISTS (SELECT * FROM information_schema.COLUMNS cols
            WHERE cols.TABLE_SCHEMA = database()
            AND cols.TABLE_NAME = 'password_reset_requests'
            AND cols.COLUMN_NAME = 'request_time') then
        ALTER TABLE `password_reset_requests`
            ADD COLUMN `request_time` datetime NOT NULL;
        end if;

        IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='password_reset_requests') THEN
                RENAME TABLE `password_reset_requests` TO `PasswordResetRequests`;
                ALTER TABLE `PasswordResetRequests` change `request_time` `request-time` DATETIME;
        END IF;
    END IF;
END//

DELIMITER ;

CALL alterTable();

DROP PROCEDURE alterTable;


CREATE TABLE IF NOT EXISTS `PasswordResetRequests` (
    `uid` CHAR(40) NOT NULL,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `request-time` datetime NOT NULL,
    PRIMARY KEY (`uid`, `user_id`),
    CONSTRAINT `FK_password_reset_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='user_badges'and c.TABLE_SCHEMA = database())) THEN  
        ALTER TABLE `user_badges` 
        CHANGE COLUMN `user_id` `user_id` INT(11) UNSIGNED NOT NULL FIRST,
        ENGINE InnoDB, CONVERT TO CHARSET utf8 COLLATE 'utf8_unicode_ci';
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='user_badges'and tc.CONSTRAINT_NAME='userBadge') then
            ALTER TABLE `user_badges`
            ADD UNIQUE INDEX `userBadge` (`user_id`, `badge_id`);
        end if;
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='user_badges'and tc.CONSTRAINT_NAME='FK_user_badges_user') then
            ALTER TABLE `user_badges`
            ADD CONSTRAINT `FK_user_badges_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE;
        end if;
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='user_badges'and tc.CONSTRAINT_NAME='FK_user_badges_badges') then
            ALTER TABLE `user_badges`
            ADD CONSTRAINT `FK_user_badges_badges` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`badge_id`) ON UPDATE CASCADE ON DELETE CASCADE;
        end if;

        IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='user_badges') THEN
            ALTER TABLE `user_badges`
	         DROP FOREIGN KEY `FK_user_badges_user`,
	         DROP FOREIGN KEY `FK_user_badges_badges`;
				RENAME TABLE `user_badges` TO `UserBadges`;
            ALTER TABLE `UserBadges`
				ADD CONSTRAINT `FK_user_badges_badges` FOREIGN KEY (`badge_id`) REFERENCES `Badges` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
				ADD CONSTRAINT `FK_user_badges_users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;                                
        END IF;
    END IF;
END//

DELIMITER ;

CALL alterTable();

DROP PROCEDURE alterTable;

-- Dumping structure for table Solas-Match-test.UserBadges
CREATE TABLE IF NOT EXISTS `UserBadges` (
	`user_id` INT(11) NOT NULL,
	`badge_id` INT(11) NOT NULL,
	UNIQUE INDEX `userBadge` (`user_id`, `badge_id`),
	PRIMARY KEY (`user_id`, `badge_id`),
        CONSTRAINT `FK_user_badges_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `FK_user_badges_badges` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`badge_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='user_tag'and c.TABLE_SCHEMA = database())) THEN  
        ALTER TABLE `user_tag` ENGINE InnoDB, CONVERT TO CHARSET utf8 COLLATE 'utf8_unicode_ci';
        if not exists (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='user_tag'and c.TABLE_SCHEMA = database() and c.COLUMN_NAME='id') then
            ALTER TABLE `user_tag`
            ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
            CHANGE COLUMN `user_id` `user_id` INT(11) UNSIGNED NOT NULL AFTER `id`,
            CHANGE COLUMN `tag_id` `tag_id` INT(11) UNSIGNED NOT NULL AFTER `user_id`,
            DROP PRIMARY KEY,
            ADD PRIMARY KEY (`id`);
	end if;
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='user_tag'and tc.CONSTRAINT_NAME='userTag') then
            ALTER TABLE `user_tag`
            ADD UNIQUE INDEX `userTag` (`user_id`, `tag_id`);
        end if;
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='user_tag'and tc.CONSTRAINT_NAME='FK_user_tag_user') then
            ALTER TABLE `user_tag`
            ADD CONSTRAINT `FK_user_tag_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE;
        end if;
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='user_tag'and tc.CONSTRAINT_NAME='FK_user_tag_tag') then
            ALTER TABLE `user_tag`
            ADD CONSTRAINT `FK_user_tag_tag` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`) ON UPDATE CASCADE ON DELETE CASCADE;
        end if;

        IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='user_tag') THEN
	         ALTER TABLE `user_tag`
	      	drop FOREIGN key `FK_user_tag_tag`;
				RENAME TABLE `user_tag` TO `UserTags`;
	         ALTER TABLE `UserTags`
	      	ADD CONSTRAINT `FK_user_tag_tag` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        END IF;
    END IF;
END//

DELIMITER ;

CALL alterTable();

DROP PROCEDURE alterTable;

-- Dumping structure for table Solas-Match-test.UserTags
CREATE TABLE IF NOT EXISTS `UserTags` (
	`user_id` INT(11) NOT NULL,
	`tag_id` INT(11) NOT NULL,
	PRIMARY KEY (`user_id`, `tag_id`),
	UNIQUE INDEX `userTag` (`user_id`, `tag_id`),
        CONSTRAINT `FK_user_tag_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `FK_user_tag_tag` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='user_notifications'and c.TABLE_SCHEMA = database())) THEN  
        ALTER TABLE `user_notifications` ENGINE InnoDB, CONVERT TO CHARSET utf8 COLLATE 'utf8_unicode_ci';
        if not exists (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='user_notifications'and c.TABLE_SCHEMA = database() and c.COLUMN_NAME='id') then
            ALTER TABLE `user_notifications`
            ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
            CHANGE COLUMN `user_id` `user_id` INT(11) UNSIGNED NOT NULL AFTER `id`,
            CHANGE COLUMN `task_id` `task_id` BIGINT(11) UNSIGNED NOT NULL AFTER `user_id`,
            DROP PRIMARY KEY,
            ADD PRIMARY KEY (`id`),
            ADD UNIQUE INDEX `user_id_task_id` (`user_id`, `task_id`);
	end if;

        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='user_notifications'and tc.CONSTRAINT_NAME='FK_user_notifications_user') then
            ALTER TABLE `user_notifications`
            ADD CONSTRAINT `FK_user_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE;
        end if;

        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='user_notifications'and tc.CONSTRAINT_NAME='FK_user_notifications_task') then
            ALTER TABLE `user_notifications`
            ADD CONSTRAINT `FK_user_notifications_task` FOREIGN KEY (`task_id`) REFERENCES `task` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        end if;

        IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='user_notifications') THEN
                RENAME TABLE `user_notifications` TO `UserNotifications`;
                ALTER TABLE `UserNotifications` change `created_time` `created-time` DATETIME;

                ALTER TABLE `UserNotifications`
                DROP FOREIGN KEY `FK_user_notifications_task`,
                ADD CONSTRAINT `FK_user_notifications_task1` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;

                ALTER TABLE `UserNotifications`
                DROP FOREIGN KEY `FK_user_notifications_user`,
                ADD CONSTRAINT `FK_user_notifications_user1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        END IF;
    END IF;
END//

DELIMITER ;

CALL alterTable();

DROP PROCEDURE alterTable;


-- Dumping data for table Solas-Match-test.UserTags: ~0 rows (approximately)
/*!40000 ALTER TABLE `UserTags` DISABLE KEYS */;
/*!40000 ALTER TABLE `UserTags` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `UserNotifications` (
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) UNSIGNED NOT NULL,
	`task_id` BIGINT(11) UNSIGNED NOT NULL,
	`created-time` DATETIME NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `user_id_task_id` (`user_id`, `task_id`),
	INDEX `FK_user_notifications_task` (`task_id`),
	CONSTRAINT `FK_user_notifications_task` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_user_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP PROCEDURE IF EXISTS alterTable;
DELIMITER //
CREATE PROCEDURE alterTable()
BEGIN
    IF (EXISTS (SELECT 1 FROM information_schema.COLUMNS c where c.TABLE_NAME='user_task_score'and c.TABLE_SCHEMA = database())) THEN  
        ALTER TABLE `user_task_score` 
        CHANGE COLUMN `user_id` `user_id` INT(11) UNSIGNED NOT NULL FIRST,
	CHANGE COLUMN `task_id` `task_id` BIGINT(11) UNSIGNED NOT NULL AFTER `user_id`,
        ENGINE InnoDB, CONVERT TO CHARSET utf8 COLLATE 'utf8_unicode_ci';
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='user_task_score'and tc.CONSTRAINT_NAME='taskScore') then
            ALTER TABLE `user_task_score`
            ADD UNIQUE INDEX `taskScore` (`task_id`, `user_id`);
        end if;
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='user_task_score'and tc.CONSTRAINT_NAME='FK_user_task_score_user') then
            ALTER TABLE `user_task_score`
            ADD CONSTRAINT `FK_user_task_score_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE;
        end if;
        if not exists (SELECT 1 FROM information_schema.TABLE_CONSTRAINTS tc where tc.TABLE_SCHEMA=database() and tc.TABLE_NAME='user_task_score'and tc.CONSTRAINT_NAME='FK_user_task_score_task') then
            ALTER TABLE `user_task_score`
            ADD CONSTRAINT `FK_user_task_score_task` FOREIGN KEY (`task_id`) REFERENCES `task` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
        end if;

        IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='user_task_score') THEN
                RENAME TABLE `user_task_score` TO `UserTaskScores`;
        END IF;
    END IF;
END//

DELIMITER ;

CALL alterTable();

DROP PROCEDURE alterTable;

-- Dumping structure for table Solas-Match-test.UserTaskScores
CREATE TABLE IF NOT EXISTS `UserTaskScores` (
	`user_id` INT(11) NOT NULL,
	`task_id` INT(11) NOT NULL,
	`score` INT(11) NOT NULL DEFAULT '-1',
	PRIMARY KEY (`user_id`, `task_id`),
	UNIQUE INDEX `taskScore` (`task_id`, `user_id`),
        CONSTRAINT `FK_user_task_score_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `FK_user_task_score_task` FOREIGN KEY (`task_id`) REFERENCES `task` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



#----------------------------stored procedures-------------------------------

-- Dumping data for table Solas-Match-test.UserTaskScores: ~0 rows (approximately)
/*!40000 ALTER TABLE `UserTaskScores` DISABLE KEYS */;
/*!40000 ALTER TABLE `UserTaskScores` ENABLE KEYS */;


-- Dumping structure for procedure Solas-Match-test.findOrganisation
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


-- Dumping structure for procedure Solas-Match-test.getOrgByUser
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


-- Dumping structure for procedure Solas-Match-test.getOrgMembers
DROP PROCEDURE IF EXISTS `getOrgMembers`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrgMembers`(IN `id` INT)
BEGIN
	SELECT u.*
	FROM OrganisationMembers om JOIN Users u ON om.user_id = u.id
	WHERE organisation_id=id;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-test.getUserBadges
DROP PROCEDURE IF EXISTS `getUserBadges`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserBadges`(IN `id` INT)
BEGIN
SELECT b.*
FROM UserBadges ub JOIN Badges b ON ub.badge_id = b.id
WHERE user_id = id;
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


-- Dumping structure for procedure Solas-Match-test.organisationInsertAndUpdate
DROP PROCEDURE IF EXISTS `organisationInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `organisationInsertAndUpdate`(IN `id` INT(10), IN `url` TEXT, IN `companyName` VARCHAR(255), IN `bio` VARCHAR(4096))
BEGIN
	if id='' then set id=null;end if;
	if url='' then set url=null;end if;
	if companyName='' then set companyName=null;end if;
	if bio='' then set bio=null;end if;

	
	if id is null and not exists(select * from Organisations o where (o.home-page= url or o.home-page= concat("http://",url) ) and o.name=companyName)then
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
			set @q = CONCAT(@q," o.home-page='",url,"'") ;
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
			set @q = CONCAT(@q," where o.home-page='",url,"' and o.name='",companyName,"'");
		end if;
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
#
	end if;
	
	select o.id as 'result' from Organisations o where (o.home-page= url or o.home-page= concat("http://",url) ) and o.name=companyName;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-test.removeUserTag
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


-- Dumping structure for procedure Solas-Match-test.userFindByUserData
DROP PROCEDURE IF EXISTS `userFindByUserData`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userFindByUserData`(IN `id` INT, IN `pass` VARBINARY(128), IN `email` VARCHAR(256), IN `role` TINYINT)
BEGIN
	if(id is not null and pass is not null) then
		select * from Users u where u.id = id and password= pass;
   elseif(id is not null and role=1) then
		select * from Users u where u.id = id and EXISTS (select * from OrganisationMembers om where om.user_id = u.id);
	elseif(id is not null) then
 		select * from Users u where u.id = id;
   elseif (email is not null) then
   	select * from Users u where u.email = email;
	end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-test.userInsertAndUpdate
DROP PROCEDURE IF EXISTS `userInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userInsertAndUpdate`(IN `email` VARCHAR(256), IN `nonce` int(11), IN `pass` char(128), IN `bio` TEXT, IN `name` VARCHAR(128), IN `lang` INT, IN `region` INT, IN `id` INT)
	LANGUAGE SQL
	NOT DETERMINISTIC
	CONTAINS SQL
	SQL SECURITY DEFINER
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
	insert into Users (email, nonce, password, `created-time`, `display-name`, biography, language_id, country_id) 
              values (email, nonce, pass, NOW(), name, bio, lang, region);
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
			set @q = CONCAT(@q," u.language_id='",lang,"'") ;
		end if;
		if region is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," u.country_id='",region,"'") ;
		end if;
		if name is not null then 
				if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," u.display-name='",name,"'");
		
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

	end if;
	
	select u.id from Users u where u.email= email;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-test.userLikeTag
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


-- Dumping structure for procedure Solas-Match-Test.getCountries
DROP PROCEDURE IF EXISTS `getCountries`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCountries`()
BEGIN
SELECT  en-name as country, code, id FROM Countries order by en-name;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getLanguages
DROP PROCEDURE IF EXISTS `getLanguages`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLanguages`()
BEGIN
SELECT  en-name as language, code, id FROM Languages order by en-name;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Test.getLCID
DROP PROCEDURE IF EXISTS `getLCID`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLCID`(IN `lang` VARCHAR(128), IN `countryName` VARCHAR(128))
BEGIN
set @ll = "";
set @cc = "";
select c.code into @cc from Countries c where c.en-name = countryName;
select l.code into @ll from Languages l where l.en-name = lang;
select concat(@ll,"-",@cc) as lcid;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.userHasBadge
DROP PROCEDURE IF EXISTS `userHasBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userHasBadge`(IN `userID` INT, IN `badgeID` INT)
BEGIN
	Select EXISTS( SELECT 1 FROM UserBadges WHERE user_id = userID AND badge_id = badgeID) as result;
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

-- Dumping structure for procedure Solas-Match-Dev.getTag
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

-- Dumping structure for procedure Solas-Match-Dev.tagInsert
DROP PROCEDURE IF EXISTS `tagInsert`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `tagInsert`(IN `name` VARCHAR(50))
BEGIN
insert into Tags (label) values (name);
select id from Tags where label=name;
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
	set @q= "SELECT id,organisation_id,title,word-count,source_id,target_id,created-time, country_id-source, country_id-target FROM Tasks WHERE 1 ";-- set update
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

-- Dumping structure for procedure SolasMatch.getUser
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
	
	set @q= "select * from Users u where 1 ";-- set update
	if id is not null then 
#set paramaters to be updated
		set @q = CONCAT(@q," and u.id=",id) ;
	end if;
	if name is not null then 
		set @q = CONCAT(@q," and u.display-name='",name,"'") ;
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
		set @q = CONCAT(@q," and u.created-time='",created,"'") ;
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

-- Dumping structure for procedure Solas-Match-Test.getTask
DROP PROCEDURE IF EXISTS `getTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTask`(IN `id` INT, IN `orgID` INT, IN `name` VARCHAR(50), IN `wordCount` INT, IN `sID` INT, IN `tID` INT, IN `created` DATETIME, IN `impact` TEXT, IN `ref` VARCHAR(128), IN `sCC` VARCHAR(3), IN `tCC` VARCHAR(3))
    READS SQL DATA
BEGIN
	if id='' then set id=null;end if;
	if orgID='' then set orgID=null;end if;
	if name='' then set name=null;end if;
	if sID='' then set sID=null;end if;
	if tID='' then set tID=null;end if;
	if wordCount='' then set wordCount=null;end if;
	if created='' then set created=null;end if;
	if impact='' then set impact=null;end if;
	if ref='' then set ref=null;end if;
	if sCC='' then set sCC=null;end if;
	if tCC='' then set tCC=null;end if;
	
	set @q= "select id,organisation_id,title,word-count,source_id,target_id,created-time,impact,reference-page, (select code from Countries where id =t.country_id-source) as country_id-source, (select code from Countries where id =t.country_id-target) as country_id-target from Tasks t where 1";-- set update
	if id is not null then 
#set paramaters to be updated
		set @q = CONCAT(@q," and t.id=",id) ;
	end if;
	if orgID is not null then 
		set @q = CONCAT(@q," and t.organisation_id=",orgID) ;
	end if;
	if name is not null then 
		set @q = CONCAT(@q," and t.title='",name,"'") ;
	end if;
	if sID is not null then 
		set @q = CONCAT(@q," and t.source_id=",sID) ;
	end if;
	if tID is not null then 
		set @q = CONCAT(@q," and t.target_id=",tID) ;
	end if;
	if sCC is not null then 
		set @scid=null;
			select c.id into @scid from Countries c where c.code=sCC;
		set @q = CONCAT(@q," and t.country_id-source=",@scid) ;
	end if;
	if tCC is not null then 
		set @tcid=null;
			select c.id into @tcid from Countries c where c.code=tCC;
		set @q = CONCAT(@q," and t.country_id-target=",@tcid) ;
	end if;
	if wordCount is not null then 
		set @q = CONCAT(@q," and t.word-count=",wordCount) ;
	end if;
	if (created is not null  and created!='0000-00-00 00:00:00') then 
		set @q = CONCAT(@q," and t.created-time='",created,"'") ;
	end if;
	if impact is not null then 
		set @q = CONCAT(@q," and t.impact='",impact,"'") ;
	end if;
	if ref is not null then 
		set @q = CONCAT(@q," and t.reference-page='",ref,"'") ;
	end if;
	
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Test.taskInsertAndUpdate
DROP PROCEDURE IF EXISTS `taskInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `taskInsertAndUpdate`(IN `id` INT, IN `orgID` INT, IN `name` VARCHAR(50), IN `wordCount` INT, IN `sID` INT, IN `tID` INT, IN `created` DATETIME, IN `impactValue` TEXT, IN `ref` VARCHAR(128), IN `sCC` VARCHAR(3), IN `tCC` VarCHAR(3))
BEGIN
	if id='' then set id=null;end if;
	if orgID='' then set orgID=null;end if;
	if name='' then set name=null;end if;
	if sID='' then set sID=null;end if;
	if tID='' then set tID=null;end if;
	if wordCount='' then set wordCount=null;end if;
	if created='' then set created=null;end if;
	if impactValue='' then set impactValue=null;end if;
	if ref='' then set ref=null;end if;
	if sCC='' then set sCC=null;end if;
	if tCC='' then set tCC=null;end if;
	
	
	if id is null then
		if impactValue is null then set impactValue="";end if;
		if ref is null then set ref="";end if;
		if created is null or created ='0000-00-00 00:00:00' then set created=now();end if;
		set @scid=null;
			select c.id into @scid from Countries c where c.code=sCC;
		set @tcid=null;
			select c.id into @tcid from Countries c where c.code=tCC;
		insert into Tasks (organisation_id,title,`word-count`,source_id,target_id,`created-time`,impact,`reference-page`,`country_id-source`,`country_id-target`)
		 values (orgID,name,wordCount,sID,tID,created,impactValue,ref,@scid,@tcid);
	elseif EXISTS (select 1 from Tasks t where t.id=id) then
		set @first = true;
		set @q= "update Tasks t set";-- set update
		if orgID is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," t.organisation_id=",orgID) ;
		end if;
		if name is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," t.title='",name,"'") ;
		end if;
		if sID is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," t.source_id=",sID) ;
		end if;
		if tID is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," t.target_id=",tID) ;
		end if;
		
		if sCC is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @scid=null;
			select c.id into @scid from Countries c where c.code=sCC;
			set @q = CONCAT(@q," t.country_id-source=",@scid) ;
		end if;
		if tCC is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @tcid=null;
			select c.id into @tcid from Countries c where c.code=tCC;
			set @q = CONCAT(@q," t.country_id-target=",@tcid) ;
		end if;
		
		if wordCount is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," t.word-count=",wordCount) ;
		end if;
		if impactValue is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," t.impact='",impactValue,"'");
		end if;
		if ref is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," t.reference-page='",ref,"'") ;
		end if;
		if (created is not null  and created!='0000-00-00 00:00:00') then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," t.created-time='",created,"'") ;
		end if;
		set @q = CONCAT(@q," where  t.id= ",id);
		PREPARE stmt FROM @q;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
	end if;
	call getTask(id,orgID,name,wordCount,sID,tID,created,impactValue,ref,sCC,tCC);
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

-- Dumping structure for procedure Solas-Match-Dev.getTaskTags
DROP PROCEDURE IF EXISTS `getTaskTags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskTags`(IN `id` INT)
BEGIN
	if id='' then set id=null;end if;
	set @q= "select t.id , t.label from Tags t join TaskTags tt on t.id= tt.tag_id where 1 ";-- set update
	if id is not null then 
#set paramaters to be updated
		set @q = CONCAT(@q," and tt.task_id=",id) ;
	end if;	
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.unlinkStoredTags
DROP PROCEDURE IF EXISTS `unlinkStoredTags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `unlinkStoredTags`(IN `id` INT)
    MODIFIES SQL DATA
BEGIN
DELETE FROM TaskTags WHERE task_id = id;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.storeTagLinks
DROP PROCEDURE IF EXISTS `storeTagLinks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `storeTagLinks`(IN `taskID` INT, IN `tagID` INT)
    MODIFIES SQL DATA
BEGIN
insert into TaskTags  (task_id,tag_id) values (taskID,tagID);
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Test.getLatestAvailableTasks
DROP PROCEDURE IF EXISTS `getLatestAvailableTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLatestAvailableTasks`(IN `lim` INT)
BEGIN
	 if (lim= '') then set lim=null; end if;
	 if(lim is not null) then
    set @q = Concat("SELECT t.id FROM Tasks AS t WHERE t.id NOT IN (SELECT task_id FROM TaskClaims) ORDER BY created-time DESC LIMIT ",lim);
    else
    set @q = "SELECT t.id FROM Tasks AS t WHERE t.id NOT IN (SELECT task_id FROM TaskClaims) ORDER BY created-time DESC ";
    end if;
    PREPARE stmt FROM @q;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.getUserTopTasks
DROP PROCEDURE IF EXISTS `getUserTopTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTopTasks`(IN `uID` INT, IN `lim` INT)
    READS SQL DATA
    COMMENT 'relpace with more effient code later'
BEGIN
    set @q = Concat("SELECT t.id FROM Tasks AS t LEFT JOIN (SELECT *FROM UserTaskScores WHERE user_id = ?) AS uts ON t.id = uts.task_id WHERE t.id NOT IN (SELECT task_id FROM TaskClaims) ORDER BY uts.score DESC limit ",lim);
    PREPARE stmt FROM @q;
    set @uID=uID;
    EXECUTE stmt using @uID;
    DEALLOCATE PREPARE stmt;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.getTaggedTasks
DROP PROCEDURE IF EXISTS `getTaggedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaggedTasks`(IN `tID` INT, IN `lim` INT)
    READS SQL DATA
BEGIN
	set @q = Concat("SELECT id 
                         FROM Tasks t join TaskTags tt on tt.task_id=t.id
                         WHERE tt.tag_id=? AND NOT  exists (
							  	SELECT 1		
								FROM TaskClaims
								WHERE task_id = t.id
							)
                         ORDER BY t.created-time DESC
                         LIMIT ",lim);
        PREPARE stmt FROM @q;
        set @tID=tID;
        EXECUTE stmt using @tID;
        DEALLOCATE PREPARE stmt;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.getTopTags
DROP PROCEDURE IF EXISTS `getTopTags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTopTags`(IN `lim` INT)
    READS SQL DATA
BEGIN
set @q = Concat("   SELECT t.label AS label,t.id as tag_id, COUNT( tt.tag_id ) AS frequency
                    FROM TaskTags AS tt 
                    join Tags AS t on tt.tag_id = t.id
                    join Tasks as tsk on tsk.id=tt.task_id
                    WHERE not exists ( SELECT 1
                                        FROM TaskClaims tc
                                        where tc.task_id=tt.task_id
                                        )
                    GROUP BY tt.tag_id
                    ORDER BY frequency DESC
                    LIMIT ",lim);
        PREPARE stmt FROM @q;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
END//
DELIMITER ;



-- Dumping structure for procedure Solas-Match-Dev.getLatestFileVersion
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


-- Dumping structure for procedure Solas-Match-Dev.recordFileUpload
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

-- Dumping structure for procedure Solas-Match-Dev.archiveTask
DROP PROCEDURE IF EXISTS `archiveTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `archiveTask`(IN `tID` INT)
    MODIFIES SQL DATA
BEGIN
	INSERT INTO `ArchivedTasks`(task_id, organisation_id, title, `word-count`, source_id, target_id, `created-time`, `archived-time`, user_id)
		
		SELECT id, organisation_id, title, `word-count`, source_id, target_id, `created-time`, NOW(), tc.user_id
		FROM Tasks tt
		LEFT JOIN TaskClaims tc ON tc.task_id = tt.id
		WHERE id =tID;
   
   DELETE FROM Tasks WHERE id = tID ;
   DELETE FROM UserTaskScores WHERE task_id = tID;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.getTaskFileMetaData
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
		set @q= "select task_id, version_id, filename, content-type, user_id, upload-time from TaskFileVersions t where 1 ";
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
		set @q = CONCAT(@q," and t.content-type='",content,"'") ;
	end if;
	if uID is not null then 
		set @q = CONCAT(@q," and t.user_id=",uID) ;
	end if;
	if (uTime is not null  and uTime!='0000-00-00 00:00:00')then 
		set @q = CONCAT(@q," and t.upload-time='",uTime,"'") ;
	end if;
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.claimTask
DROP PROCEDURE IF EXISTS `claimTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `claimTask`(IN `tID` INT, IN `uID` INT)
BEGIN
	insert into TaskClaims  (task_id,user_id) values (tID,uID);
	select 1 as result;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `getTaskTranslator`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskTranslator` (IN `taskId` INT)
BEGIN
    SELECT user_id
    FROM TaskClaims
    WHERE task_id=taskId;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.hasUserClaimedTask
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

-- Dumping structure for procedure Solas-Match-Dev.taskIsClaimed
DROP PROCEDURE IF EXISTS `taskIsClaimed`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `taskIsClaimed`(IN `tID` INT)
BEGIN
Select exists (SELECT 1	FROM TaskClaims WHERE task_id = tID) as result;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.getArchivedTasks
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

-- Dumping structure for procedure Solas-Match-Dev.getUserArchivedTasks
DROP PROCEDURE IF EXISTS `getUserArchivedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserArchivedTasks`(IN `uID` INT, IN `lim` INT)
BEGIN

set @q=Concat("SELECT * FROM ArchivedTasks as a 
                WHERE user_id = ?
                ORDER BY created-time DESC
                limit ", lim);
        PREPARE stmt FROM @q;
        set@uID = uID;
	EXECUTE stmt using @uID;
	DEALLOCATE PREPARE stmt;

END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Dev.getUserTasks
DROP PROCEDURE IF EXISTS `getUserTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTasks`(IN `uID` INT, IN `lim` INT)
BEGIN

set @q=Concat(" SELECT * 
                FROM Tasks JOIN TaskClaims ON TaskClaims.task_id = Tasks.id
                WHERE user_id = ?
                ORDER BY created-time DESC
                limit ", lim);
        PREPARE stmt FROM @q;
        set@uID = uID;
	EXECUTE stmt using @uID;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;

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

DROP PROCEDURE IF EXISTS `getUserNotifications`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserNotifications`(IN `id` INT)
BEGIN
	SELECT *
	FROM UserNotifications
	WHERE user_id = id;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `userSubscribedToTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userSubscribedToTask`(IN `userId` INT, IN `taskId` INT)
BEGIN
	if EXISTS (SELECT task_id 
                	FROM UserNotifications
                	WHERE user_id = userId
                    AND task_id = taskId) then
		select 1 as 'result';
	else
    	select 0 as 'result';
	end if;
END//
DELIMITER ;

DROP Procedure IF EXISTS `getSubscribedUsers`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getSubscribedUsers`(IN `taskId` INT)
BEGIN
    SELECT *
    FROM UserNotifications
    WHERE task_id = taskId;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Dev.getOrg
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
		set @q = CONCAT(@q," and o.home-page='",url,"'") ;
	end if;
	if bio is not null then 
		set @q = CONCAT(@q," and o.biography='",bio,"'") ;
	end if;
	
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `userNotificationsInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userNotificationsInsertAndUpdate`(IN `user_id` INT, IN `task_id` INT)
	LANGUAGE SQL
	NOT DETERMINISTIC
	CONTAINS SQL
	SQL SECURITY DEFINER
BEGIN
	insert into UserNotifications  (user_id, task_id, `created-time`) values (user_id, task_id, NOW());
    select 1 as "result";
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
		DELETE 	FROM UserNotifications	WHERE user_id=userId AND task_id =taskId; 
		select 1 as 'result';
	else
	select 0 as 'result';
	end if;
END//
DELIMITER ;

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

-- Dumping structure for procedure Solas-Match-Dev.requestMembership
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



-- Dumping structure for procedure Solas-Match-Dev.getMembershipRequests
DROP PROCEDURE IF EXISTS `getMembershipRequests`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMembershipRequests`(IN `orgID` INT)
BEGIN
	SELECT *
	FROM OrgRequests
   WHERE org_id = orgID
   ORDER BY request-datetime DESC;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.removeMembershipRequest
DROP PROCEDURE IF EXISTS `removeMembershipRequest`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeMembershipRequest`(IN `uID` INT, IN `orgID` INT)
BEGIN
	DELETE FROM OrgRequests
   WHERE user_id=uID
   AND org_id=orgID;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.acceptMemRequest
DROP PROCEDURE IF EXISTS `acceptMemRequest`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `acceptMemRequest`(IN `uID` INT, IN `orgID` INT)
BEGIN
	INSERT INTO OrganisationMembers (user_id, organisation_id) VALUES (uID,orgID);
	call removeMembershipRequest(uID,orgID);
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.removeUserBadge
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

-- Dumping structure for procedure Solas-Match-Dev.addBadge
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

-- Dumping structure for procedure Solas-Match-Dev.getUsersWithBadge
DROP PROCEDURE IF EXISTS `getUsersWithBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUsersWithBadge`(IN `bID` INT)
BEGIN
	SELECT *
	FROM Users JOIN UserBadges ON Users.id = UserBadges.user_id
	WHERE badge_id = bID;
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
	set @q= "select en-name as language, code, id from Languages l where 1 ";-- set update
	if id is not null then 
#set paramaters to be updated
		set @q = CONCAT(@q," and l.id=",id) ;
	end if;
	if code is not null then 
		set @q = CONCAT(@q," and l.code='",code,"'") ;
	end if;
	if name is not null then 
		set @q = CONCAT(@q," and l.en-name='",name,"'") ;
	end if;
	
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
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
	set @q= "select en-name as country, code, id from Countries c where 1 ";-- set update
	if id is not null then 
#set paramaters to be updated
		set @q = CONCAT(@q," and c.id=",id) ;
	end if;
	if code is not null then 
		set @q = CONCAT(@q," and c.code='",code,"'") ;
	end if;
	if name is not null then 
		set @q = CONCAT(@q," and c.en-name='",name,"'") ;
	end if;
	
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;

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

DROP PROCEDURE IF EXISTS `addPasswordResetRequest`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `addPasswordResetRequest`(IN `uniqueId` CHAR(40), IN `userId` INT)
BEGIN
    INSERT INTO PasswordResetRequests (uid, user_id, `request-time`) VALUES (uniqueId,userId,NOW());
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `removePasswordResetRequest`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removePasswordResetRequest`(IN `userId` INT)
BEGIN
    DELETE FROM PasswordResetRequests 
        WHERE user_id = userId;
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

-- Dumping structure for procedure Solas-Match-Test.getUserTrackedTasks
DROP PROCEDURE IF EXISTS `getUserTrackedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTrackedTasks`(IN `id` INT)
BEGIN
	SELECT t.*
	FROM UserNotifications un join Tasks t on un.task_id=t.id
	WHERE user_id = id;
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

-- Dumping structure for procedure Solas-Match-Test.deleteBadge
DROP PROCEDURE IF EXISTS `deleteBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteBadge`(IN `id` INT)
BEGIN
delete from Badges where Badges.id = id;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Test.badgeInsertAndUpdate
DROP PROCEDURE IF EXISTS `badgeInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `badgeInsertAndUpdate`(IN `badgeID` INT, IN `ownerID` INT, IN `name` VARCHAR(50), IN `disc` MEDIUMTEXT)
BEGIN
if not exists (select 1 from Badges b where b.id = badgeID) then
	insert into Badges (owner_id,title,description) values (ownerID,name,disc);
	select 1 as result;
else
	update Badges bg set bg.title = name, bg.description = disc
	where bg.id = badgeID;
	select 1 as result;
end if;
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

-- Dumping structure for procedure Solas-Match-Test.getTotalClaimedTasks
DROP PROCEDURE IF EXISTS `getTotalClaimedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTotalClaimedTasks`(IN `dateTime` DATETIME)
BEGIN
    if dateTime is null then set dateTime='0000-00-00 00:00:00';end if;
    SET @claimedTasks = NULL;
    SELECT count(1) INTO @claimedTasks FROM TaskClaims tc
    WHERE tc.claimed-time >= dateTime;
    SELECT @claimedTasks AS result;
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

-- Dumping structure for procedure Solas-Match-Test.getTotalArchivedTasks
DROP PROCEDURE IF EXISTS `getTotalArchivedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTotalArchivedTasks`(IN `dateTime` DATETIME)
BEGIN
    if dateTime is null then set dateTime='0000-00-00 00:00:00';end if;
    SET @archivedTasks = NULL;
    SELECT count(1) INTO @archivedTasks FROM ArchivedTasks ta
    WHERE ta.created-time >= dateTime;
    SELECT @archivedTasks AS result;
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
    WHERE tc.claimed-time >= dateTime;	

    SELECT count(1) into @unclaimedTasks from Tasks t
    WHERE t.id NOT IN
    (
        SELECT task_id
        FROM  TaskClaims
    );

    SELECT count(1) INTO @archivedTasks FROM ArchivedTasks ta
    WHERE ta.created-time >= dateTime;	

    SET @totalTasks = @claimedTasks + @unclaimedTasks + @archivedTasks;	
    SELECT @totalTasks AS result;
END//
DELIMITER ;

/*---------------------put triggers below this line------------------------------------------*/


-- Dumping structure for trigger Solas-Match-test.validateHomepageInsert
DROP TRIGGER IF EXISTS `validateHomepageInsert`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `validateHomepageInsert` BEFORE INSERT ON `Organisations` FOR EACH ROW 
BEGIN
	if not (new.`home-page` like "http://%" or new.`home-page`  like "https://%") then
	set new.`home-page` = concat("http://",new.`home-page`);
	end if;
END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;


-- Dumping structure for trigger Solas-Match-test.validateHomepageUpdate
DROP TRIGGER IF EXISTS `validateHomepageUpdate`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `validateHomepageUpdate` BEFORE UPDATE ON `Organisations` FOR EACH ROW 
BEGIN
	if not (new.`home-page` like "http://%" or new.`home-page`  like "https://%") then
	set new.`home-page` = concat("http://",new.`home-page`);
	end if;
END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;

DROP TRIGGER IF EXISTS `removeTaskInfo`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `removeTaskInfo` AFTER DELETE ON `Tasks` FOR EACH ROW BEGIN
    DELETE FROM UserTaskScores WHERE task_id = old.id;
    DELETE FROM TaskClaims WHERE task_id = old.id;
    DELETE FROM TaskFileVersions WHERE task_id = old.id;
    DELETE FROM TaskTags WHERE task_id = old.id;
END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;

/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;



/* These statements below may be removed, if they have already been called at least ONCE */
DROP PROCEDURE IF EXISTS removeOldTables;
DELIMITER //
CREATE PROCEDURE removeOldTables()
BEGIN
    IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='old_task_file') THEN
            DROP TABLE `old_task_file`;
    END IF;

    IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='translator') THEN
            DROP TABLE `translator`;
    END IF;

    IF EXISTS(SELECT 1 FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA=database() and t.TABLE_NAME='task_file_version_download') THEN
            DROP TABLE `task_file_version_download`;
    END IF;
END//
DELIMITER ;

CALL removeOldTables();
DROP PROCEDURE removeOldTables;

/* Remove when necessary*/
DROP PROCEDURE IF EXISTS `findOganisation`;


SET FOREIGN_KEY_CHECKS=1;