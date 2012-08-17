-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.24-0ubuntu0.12.04.1 - (Ubuntu)
-- Server OS:                    debian-linux-gnu
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2012-08-07 10:35:31
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping structure for table Solas-Match-test.archived_task
CREATE TABLE IF NOT EXISTS `archived_task` (
  `archived_task_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) NOT NULL,
  `organisation_id` int(10) unsigned NOT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `impact` text COLLATE utf8_unicode_ci NOT NULL,
  `reference_page` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `word_count` int(10) unsigned DEFAULT NULL,
  `source_id` int(10) unsigned DEFAULT NULL COMMENT 'foreign key from the `language` table',
  `target_id` int(10) unsigned DEFAULT NULL COMMENT 'foreign key from the `language` table',
  `created_time` datetime NOT NULL,
  `archived_time` datetime NOT NULL,
  PRIMARY KEY (`archived_task_id`),
  KEY `source` (`source_id`),
  KEY `target` (`target_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `archived_task`
	COLLATE='utf8_unicode_ci',
	ENGINE=InnoDB,
	CONVERT TO CHARSET utf8;

-- Dumping data for table Solas-Match-test.archived_task: 0 rows
/*!40000 ALTER TABLE `archived_task` DISABLE KEYS */;
/*!40000 ALTER TABLE `archived_task` ENABLE KEYS */;


-- Dumping structure for table Solas-Match-test.badges
CREATE TABLE IF NOT EXISTS `badges` (
  `badge_id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`badge_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `badges`
	COLLATE='utf8_unicode_ci',
	ENGINE=InnoDB,
	CONVERT TO CHARSET utf8;

DROP PROCEDURE IF EXISTS addcol;
DELIMITER //
CREATE PROCEDURE addcol()
BEGIN
	if not exists (SELECT * FROM information_schema.COLUMNS c where c.TABLE_NAME='badges'and c.TABLE_SCHEMA like "Solas-Match%" and c.COLUMN_NAME="owner_id") then
		ALTER TABLE `task`
		    add column `owner_id` int(11) COLLATE utf8_unicode_ci DEFAULT NULL;
	end if;
END//

DELIMITER ;

CALL addcol();

DROP PROCEDURE addcol;

-- Dumping data for table Solas-Match-test.badges: ~3 rows (approximately)
/*!40000 ALTER TABLE `badges` DISABLE KEYS */;
REPLACE INTO `badges` (`badge_id`, `title`, `description`) VALUES
	(3, 'Profile-Filler', 'Filled in required info for user profile.'),
	(4, 'Registered', 'Successfully set up an account'),
	(5, 'Native-Language', 'Filled in your native language on your user profile.');
/*!40000 ALTER TABLE `badges` ENABLE KEYS */;






-- Dumping structure for table Solas-Match-test.language
CREATE TABLE IF NOT EXISTS `language` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(5) COLLATE utf8_unicode_ci NOT NULL COMMENT '"en", for example',
  `en_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '"English", for example',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `language`
	COLLATE='utf8_unicode_ci',
	ENGINE=InnoDB,
	CONVERT TO CHARSET utf8;

-- Dumping data for table Solas-Match-test.language: 0 rows
/*!40000 ALTER TABLE `language` DISABLE KEYS */;
/*!40000 ALTER TABLE `language` ENABLE KEYS */;


-- Dumping structure for table Solas-Match-test.old_task_file
CREATE TABLE IF NOT EXISTS `old_task_file` (
  `task_id` bigint(20) unsigned NOT NULL,
  `file_id` int(10) unsigned NOT NULL,
  `path` text COLLATE utf8_unicode_ci NOT NULL,
  `filename` text COLLATE utf8_unicode_ci NOT NULL,
  `content_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Mime type',
  `user_id` int(11) DEFAULT NULL COMMENT 'Can be null while users table is empty! Remove this option once logins working',
  `upload_time` datetime NOT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `old_task_file`
	CHANGE COLUMN `file_id` `file_id` INT(10) UNSIGNED NOT NULL AFTER `task_id`,
	DROP PRIMARY KEY,
	ADD PRIMARY KEY (`task_id`),
	COLLATE='utf8_unicode_ci',
	ENGINE=InnoDB,
	CONVERT TO CHARSET utf8;

-- Dumping data for table Solas-Match-test.old_task_file: 0 rows
/*!40000 ALTER TABLE `old_task_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `old_task_file` ENABLE KEYS */;


-- Dumping structure for table Solas-Match-test.organisation
CREATE TABLE IF NOT EXISTS `organisation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `home_page` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `biography` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`home_page`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `organisation`
	CHANGE COLUMN `name` `name` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci' AFTER `id`,
	CHANGE COLUMN `home_page` `home_page` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci' AFTER `name`,
	CHANGE COLUMN `biography` `biography` VARCHAR(4096) NOT NULL COLLATE 'utf8_unicode_ci' AFTER `home_page`,
	ADD UNIQUE INDEX (`name`, `home_page`);

ALTER TABLE `organisation`
	COLLATE='utf8_unicode_ci',
	ENGINE=InnoDB,
	CONVERT TO CHARSET utf8;

-- Dumping data for table Solas-Match-test.organisation: 0 rows
/*!40000 ALTER TABLE `organisation` DISABLE KEYS */;
/*!40000 ALTER TABLE `organisation` ENABLE KEYS */;





-- Dumping structure for table Solas-Match-test.organisation_member
CREATE TABLE IF NOT EXISTS `organisation_member` (
  `user_id` int(10) unsigned NOT NULL,
  `organisation_id` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `organisation_member`
	COLLATE='utf8_unicode_ci',
	ENGINE=InnoDB,
	CONVERT TO CHARSET utf8;

-- Dumping data for table Solas-Match-test.organisation_member: 0 rows
/*!40000 ALTER TABLE `organisation_member` DISABLE KEYS */;
/*!40000 ALTER TABLE `organisation_member` ENABLE KEYS */;


-- --------------------------------------------------------

--
-- Table structure for table `org_request_queue`
--

CREATE TABLE IF NOT EXISTS `org_request_queue` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `request_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;
ALTER TABLE `org_request_queue`
	COLLATE=`utf8_unicode_ci`,
	ENGINE=InnoDB,
	CONVERT TO CHARSET utf8;

-- --------------------------------------------------------


-- Dumping structure for table Solas-Match-test.tag
CREATE TABLE IF NOT EXISTS `tag` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `tag`
	COLLATE='utf8_unicode_ci',
	ENGINE=InnoDB,
	CONVERT TO CHARSET utf8;

-- Dumping data for table Solas-Match-test.tag: 0 rows
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;


-- Dumping structure for table Solas-Match-test.task
CREATE TABLE IF NOT EXISTS `task` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `organisation_id` int(10) unsigned NOT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `impact` text COLLATE utf8_unicode_ci NOT NULL,
  `reference_page` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `word_count` int(10) unsigned DEFAULT NULL,
  `source_id` int(10) unsigned DEFAULT NULL COMMENT 'foreign key from the `language` table',
  `target_id` int(10) unsigned DEFAULT NULL COMMENT 'foreign key from the `language` table',
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `source` (`source_id`),
  KEY `target` (`target_id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP PROCEDURE IF EXISTS addcol;
DELIMITER //
CREATE PROCEDURE addcol()
BEGIN
	if not exists (SELECT * FROM information_schema.COLUMNS c where c.TABLE_NAME='task'and c.TABLE_SCHEMA like "Solas-Match%" and (c.COLUMN_NAME="impact" or c.COLUMN_NAME="reference_page")) then
		ALTER TABLE `task`
		    add column `impact` text COLLATE utf8_unicode_ci NOT NULL,
		    add column`reference_page` varchar(128) COLLATE utf8_unicode_ci NOT NULL;
	end if;
END//

DELIMITER ;

CALL addcol();

DROP PROCEDURE addcol;

ALTER TABLE `task`
    COLLATE='utf8_unicode_ci',
    ENGINE=InnoDB,
    CONVERT TO CHARSET utf8;
-- Dumping data for table Solas-Match-test.task: 0 rows
/*!40000 ALTER TABLE `task` DISABLE KEYS */;
/*!40000 ALTER TABLE `task` ENABLE KEYS */;


-- Dumping structure for table Solas-Match-test.task_claim
CREATE TABLE IF NOT EXISTS `task_claim` (
  `claim_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `claimed_time` datetime NOT NULL,
  PRIMARY KEY (`claim_id`),
  KEY `task_user` (`task_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `task_claim`
	COLLATE='utf8_unicode_ci',
	ENGINE=InnoDB,
	CONVERT TO CHARSET utf8;

-- Dumping data for table Solas-Match-test.task_claim: 0 rows
/*!40000 ALTER TABLE `task_claim` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_claim` ENABLE KEYS */;


-- Dumping structure for table Solas-Match-test.task_file_version
CREATE TABLE IF NOT EXISTS `task_file_version` (
  `task_id` bigint(20) NOT NULL,
  `version_id` int(11) NOT NULL COMMENT 'Gets incremented within the code',
  `filename` text COLLATE utf8_unicode_ci NOT NULL,
  `content_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'Null while we don''t have logging in',
  `upload_time` datetime NOT NULL,
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `task_file_version`
	COLLATE='utf8_unicode_ci',
	ENGINE=InnoDB,
	CONVERT TO CHARSET utf8;

-- Dumping data for table Solas-Match-test.task_file_version: 0 rows
/*!40000 ALTER TABLE `task_file_version` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_file_version` ENABLE KEYS */;


-- Dumping structure for table Solas-Match-test.task_file_version_download
CREATE TABLE IF NOT EXISTS `task_file_version_download` (
  `task_id` bigint(20) unsigned NOT NULL,
  `file_id` int(10) unsigned NOT NULL,
  `version_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `time_downloaded` datetime NOT NULL,
  KEY `task_id` (`task_id`,`file_id`,`version_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `task_file_version_download`
	COLLATE='utf8_unicode_ci',
	ENGINE=InnoDB,
	CONVERT TO CHARSET utf8;

-- Dumping data for table Solas-Match-test.task_file_version_download: 0 rows
/*!40000 ALTER TABLE `task_file_version_download` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_file_version_download` ENABLE KEYS */;


-- Dumping structure for table Solas-Match-test.task_tag
CREATE TABLE IF NOT EXISTS `task_tag` (
  `task_id` bigint(20) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `task_tag` (`task_id`,`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `task_tag`
	COLLATE='utf8_unicode_ci',
	ENGINE=InnoDB,
	CONVERT TO CHARSET utf8;

-- Dumping data for table Solas-Match-test.task_tag: 0 rows
/*!40000 ALTER TABLE `task_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_tag` ENABLE KEYS */;


-- Dumping structure for table Solas-Match-test.translator
CREATE TABLE IF NOT EXISTS `translator` (
  `user_id` int(11) NOT NULL,
  `role_added` datetime NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `translator`
	COLLATE='utf8_unicode_ci',
	ENGINE=InnoDB,
	CONVERT TO CHARSET utf8;

-- Dumping data for table Solas-Match-test.translator: 0 rows
/*!40000 ALTER TABLE `translator` DISABLE KEYS */;
/*!40000 ALTER TABLE `translator` ENABLE KEYS */;


-- Dumping structure for table Solas-Match-test.user
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `display_name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `password` char(128) COLLATE utf8_unicode_ci NOT NULL,
  `biography` text COLLATE utf8_unicode_ci,
  `native_language` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nonce` int(11) unsigned NOT NULL,
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `user`
        CHANGE COLUMN `email` `email` VARCHAR(128) NOT NULL AFTER `display_name`,
	COLLATE='utf8_unicode_ci',
	ENGINE=InnoDB,
	CONVERT TO CHARSET utf8;




-- Dumping structure for table Solas-Match-test.user_badges
CREATE TABLE IF NOT EXISTS `user_badges` (
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`badge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `user_badges`
	COLLATE='utf8_unicode_ci',
	ENGINE=InnoDB,
	CONVERT TO CHARSET utf8;
-- Dumping data for table Solas-Match-test.user_badges: ~0 rows (approximately)
/*!40000 ALTER TABLE `user_badges` DISABLE KEYS */;
REPLACE INTO `user_badges` (`user_id`, `badge_id`) VALUES
	(45, 4);
/*!40000 ALTER TABLE `user_badges` ENABLE KEYS */;


-- Dumping structure for table Solas-Match-test.user_tag
CREATE TABLE IF NOT EXISTS `user_tag` (
  `user_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `user_tag`
	COLLATE='utf8_unicode_ci',
	ENGINE=InnoDB,
	CONVERT TO CHARSET utf8;

-- Dumping data for table Solas-Match-test.user_tag: ~0 rows (approximately)
/*!40000 ALTER TABLE `user_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_tag` ENABLE KEYS */;


-- Dumping structure for table Solas-Match-test.user_task_score
CREATE TABLE IF NOT EXISTS `user_task_score` (
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `score` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`user_id`,`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `user_task_score`
	COLLATE='utf8_unicode_ci',
	ENGINE=InnoDB,
	CONVERT TO CHARSET utf8;

-- Dumping data for table Solas-Match-test.user_task_score: ~0 rows (approximately)
/*!40000 ALTER TABLE `user_task_score` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_task_score` ENABLE KEYS */;


-- Dumping structure for procedure Solas-Match-test.findOganisation
DROP PROCEDURE IF EXISTS `findOganisation`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `findOganisation`(IN `id` INT)
    COMMENT 'finds an organisation by the data passed in.'
BEGIN
	SELECT *
	FROM organisation o
	WHERE o.id=id; 
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-test.findOrganisationsUserBelongsTo
DROP PROCEDURE IF EXISTS `findOrganisationsUserBelongsTo`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `findOrganisationsUserBelongsTo`(IN `id` INT)
BEGIN
	SELECT organisation_id
	FROM organisation_member
	WHERE user_id = id;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-test.getOrgByUser
DROP PROCEDURE IF EXISTS `getOrgByUser`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrgByUser`(IN `id` INT)
BEGIN
	SELECT *
	FROM organisation o
	WHERE o.id IN (SELECT organisation_id
						 FROM organisation_member
					 	 WHERE user_id=id); 
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-test.getOrgMembers
DROP PROCEDURE IF EXISTS `getOrgMembers`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrgMembers`(IN `id` INT)
BEGIN
	SELECT user_id
	FROM organisation_member 
	WHERE organisation_id=id;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-test.getUserBadges
DROP PROCEDURE IF EXISTS `getUserBadges`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserBadges`(IN `id` INT)
BEGIN
SELECT badge_id
FROM user_badges
WHERE user_id = id;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-test.getUserTags
DROP PROCEDURE IF EXISTS `getUserTags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTags`(IN `id` INT)
BEGIN
	SELECT label
	FROM user_tag
	JOIN tag ON user_tag.tag_id = tag.tag_id
	WHERE user_id = id; 
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

	
	if id is null and not exists(select * from organisation o where (o.home_page= url or o.home_page= concat("http://",url) ) and o.name=companyName)then
	-- set insert
	insert into organisation (name,home_page, biography) values (companyName,url,bio);

	else 
		set @first = true;
		set @q= "update organisation o set ";-- set update
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
			set @q = CONCAT(@q," o.home_page='",url,"'") ;
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
			set @q = CONCAT(@q," where o.home_page='",url,"' and o.name='",companyName,"'");
		end if;
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
#
	end if;
	
	select o.id as 'result' from organisation o where (o.home_page= url or o.home_page= concat("http://",url) ) and o.name=companyName;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-test.removeUserTag
DROP PROCEDURE IF EXISTS `removeUserTag`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeUserTag`(IN `id` INT, IN `tagID` INT)
    COMMENT 'unsubscripse a user for the given tag'
BEGIN
	if EXISTS(  SELECT user_id, tag_id
	                FROM user_tag
	                WHERE user_id = id
	                AND tag_id = tagID) then                 
		DELETE 	FROM user_tag	WHERE user_id=id AND tag_id =tagID; 
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
		select * from user where user_id = id and password= pass;
   elseif(id is not null and role=1) then
		select * from user where user_id = id and EXISTS (select * from organisation_member where user_id = id);
	elseif(id is not null) then
 		select * from user where user_id = id;
   elseif (email is not null) then
   	select * from user u where u.email = email;
	end if;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-test.userInsertAndUpdate
DROP PROCEDURE IF EXISTS `userInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userInsertAndUpdate`(IN `email` VARCHAR(256), IN `nonce` int(11), IN `pass` char(128), IN `bio` TEXT, IN `name` VARCHAR(128), IN `lang` VARCHAR(256), IN `id` INT)
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
	
	if id is null and not exists(select * from user u where u.email= email)then
	-- set insert
	insert into user (email,nonce,password,created_time,display_name,biography,native_language) values (email,nonce,pass,NOW(),name,bio,lang);
#	set @q="insert into user (email,nonce,password,created_time,display_name,biography,native_language) values ('"+email+"',"+nonce+",'"+pass+"',"+NOW()+",'"+name+"','"+bio+"','"+lang+"');";
	else 
		set @first = true;
		set @q= "update user u set ";-- set update
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
			set @q = CONCAT(@q," u.native_language='",lang,"'") ;
		end if;
		if name is not null then 
				if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," u.display_name='",name,"'");
		
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
			set @q = CONCAT(@q," where  u.user_id= ",id);
#    	allows email to be changed but not user_id
		
		elseif email is not null then 
			set @q = CONCAT(@q," where  u.email= ,",email,"'");-- allows anything but email and user_id to change
		else
			set @q = CONCAT(@q," where  u.email= null AND u.user_id=null");-- will always fail to update anyting
		end if;
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;

	end if;
	
	select u.user_id from user u where u.email= email;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-test.userLikeTag
DROP PROCEDURE IF EXISTS `userLikeTag`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userLikeTag`(IN `id` INT, IN `tagID` INT)
BEGIN
	if not EXISTS(  SELECT user_id, tag_id
	                FROM user_tag
	                WHERE user_id = id
	                AND tag_id = tagID) then                 
		INSERT INTO user_tag (user_id, tag_id)VALUES (id,tagID);
		select 1 as 'result';
	else
	select 0 as 'result';
	end if;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.userHasBadge
DROP PROCEDURE IF EXISTS `userHasBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `userHasBadge`(IN `userID` INT, IN `badgeID` INT)
BEGIN
	Select EXISTS( SELECT 1 FROM user_badges WHERE user_id = userID AND badge_id = badgeID) as result;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.getBadge
DROP PROCEDURE IF EXISTS `getBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getBadge`(IN `id` INT, IN `name` VARCHAR(128), IN `des` VARCHAR(512))
    READS SQL DATA
BEGIN
	if id='' then set id=null;end if;
	if des='' then set des=null;end if;
	if name='' then set name=null;end if;
	set @q= "SELECT *FROM badges b where 1 ";-- set update
	if id is not null then 
#set paramaters to be updated
		set @q = CONCAT(@q," and b.badge_id=",id) ;
	end if;
	if des is not null then 
		set @q = CONCAT(@q," and b.description='",des,"'") ;
	end if;
	if name is not null then 
		set @q = CONCAT(@q," and b.title='",name,"'") ;
	end if;
	
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.assignBadge
DROP PROCEDURE IF EXISTS `assignBadge`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `assignBadge`(IN `uid` INT, IN `bid` INT)
BEGIN
INSERT INTO user_badges (user_id, badge_id) VALUES (uid,bid);
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.getTag
DROP PROCEDURE IF EXISTS `getTag`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTag`(IN `id` INT, IN `name` VARCHAR(50))
BEGIN
	if id='' then set id=null;end if;
	if name='' then set name=null;end if;
	set @q= "select t.tag_id , t.label from tag t where 1 ";-- set update
	if id is not null then 
#set paramaters to be updated
		set @q = CONCAT(@q," and t.tag_id=",id) ;
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
insert into tag (label) values (name);
select tag_id from tag where label=name;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.getTasksByOrgIDs
DROP PROCEDURE IF EXISTS `getTasksByOrgIDs`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTasksByOrgIDs`(IN `orgIDs` VARCHAR(1028), IN `orderby` VARCHAR(256))
    READS SQL DATA
BEGIN
	if orgIDs='' then set orgIDs=null;end if;
	if orderby='' then set orderby=null;end if;
	set @q= "SELECT id,organisation_id,title,word_count,source_id,target_id,created_time FROM task WHERE 1 ";-- set update
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


-- Dumping structure for procedure Solas-Match-Dev.getTask
DROP PROCEDURE IF EXISTS `getTask`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTask`(IN `id` INT, IN `orgID` INT, IN `name` VARCHAR(50), IN `wordCount` INT, IN `sID` INT, IN `tID` INT, IN `created` DATETIME, IN `impact` TEXT, IN `ref` VARCHAR(128))
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
	
	set @q= "select id,organisation_id,title,word_count,source_id,target_id,created_time,impact,reference_page from task t where 1 ";-- set update
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
	if wordCount is not null then 
		set @q = CONCAT(@q," and t.word_count=",wordCount) ;
	end if;
	if (created is not null  and created!='0000-00-00 00:00:00') then 
		set @q = CONCAT(@q," and t.created_time='",created,"'") ;
	end if;
	if impact is not null then 
		set @q = CONCAT(@q," and t.impactt=",impact) ;
	end if;
	if ref is not null then 
		set @q = CONCAT(@q," and t.reference_page=",ref) ;
	end if;
	
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Dev.taskInsertAndUpdate
DROP PROCEDURE IF EXISTS `taskInsertAndUpdate`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `taskInsertAndUpdate`(IN `id` INT, IN `orgID` INT, IN `name` VARCHAR(50), IN `wordCount` INT, IN `sID` INT, IN `tID` INT, IN `created` DATETIME, IN `impact` TEXT, IN `ref` VARCHAR(128))
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
	
	if id is null then
		insert into task (organisation_id,title,word_count,source_id,target_id,created_time)
		 values (orgID,name,wordCount,sID,tID,created);
	elseif EXISTS (select 1 from task t where t.id=id) then
		set @first = true;
		set @q= "update task t set";-- set update
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
		if wordCount is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," t.word_count=",wordCount) ;
		end if;
		if impact is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," t.impact='",impact,"'");
		end if;
		if ref is not null then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," t.reference_page='",ref,"'") ;
		end if;
		if (created is not null  and created!='0000-00-00 00:00:00') then 
			if (@first = false) then 
				set @q = CONCAT(@q,",");
			else
				set @first = false;
			end if;
			set @q = CONCAT(@q," t.created_time='",created,"'") ;
		end if;
		set @q = CONCAT(@q," where  t.id= ",id);
		PREPARE stmt FROM @q;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
	end if;
	call getTask(id,orgID,name,wordCount,sID,tID,created,impact,ref);
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.getTaskTags
DROP PROCEDURE IF EXISTS `getTaskTags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaskTags`(IN `id` INT)
BEGIN
	if id='' then set id=null;end if;
	set @q= "select t.tag_id , t.label from tag t join task_tag tt on t.tag_id= tt.tag_id where 1 ";-- set update
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
DELETE FROM task_tag WHERE task_id = id;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.storeTagLinks
DROP PROCEDURE IF EXISTS `storeTagLinks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `storeTagLinks`(IN `taskID` INT, IN `tagID` INT)
    MODIFIES SQL DATA
BEGIN
insert into task_tag  (task_id,tag_id) values (taskID,tagID);
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.getLatestAvailableTasks
DROP PROCEDURE IF EXISTS `getLatestAvailableTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLatestAvailableTasks`(IN `lim` INT)
BEGIN
SELECT t.id
FROM task AS t
WHERE t.id NOT IN (SELECT task_id FROM task_claim)						
ORDER BY created_time DESC
LIMIT lim;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.getUserTopTasks
DROP PROCEDURE IF EXISTS `getUserTopTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTopTasks`(IN `uID` INT, IN `lim` INT)
    READS SQL DATA
    COMMENT 'relpace with more effient code later'
BEGIN
SELECT t.id
FROM task AS t LEFT JOIN (SELECT *FROM user_task_score WHERE user_id = uID) AS uts ON t.id = uts.task_id
WHERE t.id NOT IN (SELECT task_id FROM task_claim)
ORDER BY uts.score DESC limit lim;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.getTaggedTasks
DROP PROCEDURE IF EXISTS `getTaggedTasks`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTaggedTasks`(IN `tID` INT, IN `lim` INT)
    READS SQL DATA
BEGIN
	SELECT id 
	FROM task t join task_tag tt on tt.task_id=t.id
	WHERE tt.tag_id=tID AND NOT  exists (
							  	SELECT 1		
								FROM task_claim
								WHERE task_id = t.id
							)
	ORDER BY t.created_time DESC
	LIMIT lim; 
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.getTopTags
DROP PROCEDURE IF EXISTS `getTopTags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTopTags`(IN `lim` INT)
    READS SQL DATA
BEGIN
SELECT t.label AS label, COUNT( tt.tag_id ) AS frequency
FROM task_tag AS tt 
join tag AS t on tt.tag_id = t.tag_id
join task as tsk on tsk.id=tt.task_id
WHERE not exists ( SELECT 1
                    FROM task_claim tc
                    where tc.task_id=tt.task_id
                	)
GROUP BY tt.tag_id
ORDER BY frequency DESC
LIMIT lim;
END//
DELIMITER ;



-- Dumping structure for procedure Solas-Match-Dev.getLatestFileVersion
DROP PROCEDURE IF EXISTS `getLatestFileVersion`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLatestFileVersion`(IN `id` INT)
BEGIN
	SELECT max(version_id) as latest_version FROM task_file_version WHERE task_id =id;
END//
DELIMITER ;


-- Dumping structure for procedure Solas-Match-Dev.recordFileUpload
DROP PROCEDURE IF EXISTS `recordFileUpload`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `recordFileUpload`(IN `tID` INT, IN `name` TeXT, IN `content` VARCHAR(255), IN `uID` INT)
    MODIFIES SQL DATA
BEGIN
set @maxVer =-1;
if not exists (select 1 from task_file_version tfv where tfv.task_id=tID AND tfv.filename=name and tfv.content_type =content) then
	INSERT INTO `task_file_version` (`task_id`, `version_id`, `filename`, `content_type`, `user_id`, `upload_time`) 
	VALUES (tID,1+@maxVer,name, content, uID, Now());
else
	
	select tfv.version_id into @maxVer
	from task_file_version tfv 
	where tfv.task_id=tID 
	AND tfv.filename=name 
	and tfv.content_type =content 
	order by tfv.version_id desc
	limit 1;
	INSERT INTO `task_file_version` (`task_id`, `version_id`, `filename`, `content_type`, `user_id`, `upload_time`) 
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
	INSERT INTO `archived_task`(task_id, organisation_id, title, word_count, source_id, target_id, created_time, archived_time)
		SELECT id, organisation_id, title, word_count, source_id, target_id, created_time, NOW()
		FROM task   WHERE id =tID;
   
   DELETE FROM task WHERE id = tID ;
   DELETE FROM user_task_score WHERE task_id = tID;
END//
DELIMITER ;

-- Dumping structure for procedure Solas-Match-Dev.logFileDownload
DROP PROCEDURE IF EXISTS `logFileDownload`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `logFileDownload`(IN `tID` INT, IN `vID` INT, IN `uID` INT)
    MODIFIES SQL DATA
BEGIN
	insert into task_file_version_download (task_id,version_id,user_id,time_downloaded) 
	values (tID,uID,vID,Now());
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
		set @q= "select task_id, version_id, filename, content_type, user_id, upload_time from task_file_version t where 1 ";
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
		set @q = CONCAT(@q," and t.content_type='",content,"'") ;
	end if;
	if uID is not null then 
		set @q = CONCAT(@q," and t.user_id=",uID) ;
	end if;
	if (uTime is not null  and uTime!='0000-00-00 00:00:00')then 
		set @q = CONCAT(@q," and t.upload_time='",uTime,"'") ;
	end if;
	PREPARE stmt FROM @q;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
END//
DELIMITER ;

-- Dumping structure for trigger Solas-Match-test.validateHomepageInsert
DROP TRIGGER IF EXISTS `validateHomepageInsert`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `validateHomepageInsert` BEFORE INSERT ON `organisation` FOR EACH ROW 
BEGIN
	if not (new.home_page like "http://%" or new.home_page  like "https://%") then
	set new.home_page = concat("http://",new.home_page);
	end if;
END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;


-- Dumping structure for trigger Solas-Match-test.validateHomepageUpdate
DROP TRIGGER IF EXISTS `validateHomepageUpdate`;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `validateHomepageUpdate` BEFORE UPDATE ON `organisation` FOR EACH ROW 
BEGIN
	if not (new.home_page like "http://%" or new.home_page  like "https://%") then
	set new.home_page = concat("http://",new.home_page);
	end if;
END//
DELIMITER ;
SET SQL_MODE=@OLD_SQL_MODE;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
