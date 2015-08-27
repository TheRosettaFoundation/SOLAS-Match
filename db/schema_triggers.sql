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
