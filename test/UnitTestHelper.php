<?php

namespace SolasMatch\Tests;

use \SolasMatch\Common as Common;
use \SolasMatch\API as API;
use SolasMatch\Common\Lib\Settings;

require_once __DIR__.'/../Common/lib/Settings.class.php';
require_once __DIR__.'/../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../api/lib/PDOWrapper.class.php';
require_once __DIR__.'/../Common/Enums/TaskTypeEnum.class.php';
require_once __DIR__.'/../Common/Enums/TaskStatusEnum.class.php';
require_once __DIR__.'/../Common/Enums/BanTypeEnum.class.php';
require_once __DIR__.'/../Common/protobufs/models/Locale.php';
require_once __DIR__.'/../Common/lib/APIHelper.class.php';

class UnitTestHelper
{
    private static $initalised = false;
    const PROTO_ADMIN = "\SolasMatch\Common\Protobufs\Models\Admin";
    const PROTO_ARCHIVED_PROJECT = "\SolasMatch\Common\Protobufs\Models\ArchivedProject";
    const PROTO_ARCHIVED_TASK = "\SolasMatch\Common\Protobufs\Models\ArchivedTask";
    const PROTO_BADGE = "\SolasMatch\Common\Protobufs\Models\Badge";
    const PROTO_BANNED_ORG = "\SolasMatch\Common\Protobufs\Models\BannedOrganisation";
    const PROTO_BANNED_USER = "\SolasMatch\Common\Protobufs\Models\BannedUser";
    const PROTO_COUNTRY = "\SolasMatch\Common\Protobufs\Models\Country";
    const PROTO_LANGUAGE = "\SolasMatch\Common\Protobufs\Models\Language";
    const PROTO_LOCALE = "\SolasMatch\Common\Protobufs\Models\Locale";
    const PROTO_LOGIN = "\SolasMatch\Common\Protobufs\Models\Login";
    const PROTO_MEMBERSHIP_REQ = "\SolasMatch\Common\Protobufs\Models\MembershipRequest";
    const PROTO_OAUTH_RESPONSE = "\SolasMatch\Common\Protobufs\Models\OAuthResponse";
    const PROTO_ORG = "\SolasMatch\Common\Protobufs\Models\Organisation";
    const PROTO_PASSWORD_RESET = "\SolasMatch\Common\Protobufs\Models\PasswordReset";
    const PROTO_PASSWORD_RESET_REQ = "\SolasMatch\Common\Protobufs\Models\PasswordResetRequest";
    const PROTO_PROJECT = "\SolasMatch\Common\Protobufs\Models\Project";
    const PROTO_PROJECT_FILE = "\SolasMatch\Common\Protobufs\Models\ProjectFile";
    const PROTO_REGISTER = "\SolasMatch\Common\Protobufs\Models\Register";
    const PROTO_STATISTIC = "\SolasMatch\Common\Protobufs\Models\Statistic";
    const PROTO_TAG = "\SolasMatch\Common\Protobufs\Models\Tag";
    const PROTO_TASK = "\SolasMatch\Common\Protobufs\Models\Task";
    const PROTO_TASK_METADATA = "\SolasMatch\Common\Protobufs\Models\TaskMetadata";
    const PROTO_TASK_REVIEW = "\SolasMatch\Common\Protobufs\Models\TaskReview";
    const PROTO_USER = "\SolasMatch\Common\Protobufs\Models\User";
    const PROTO_USER_INFO = "\SolasMatch\Common\Protobufs\Models\UserPersonalInformation";
    const PROTO_USER_TSN = "\SolasMatch\Common\Protobufs\Models\UserTaskStreamNotification";
    const PROTO_WORKFLOW_GRAPH = "\SolasMatch\Common\Protobufs\Models\WorkflowGraph";
    const PROTO_WORKFLOW_NODE = "\SolasMatch\Common\Protobufs\Models\WorkflowNode";
    
    
    private function __constuct()
    {
        // Default CTOR
    }
    
    public static function teardownDb()
    {
        $dsn = "mysql:dbname=".Common\Lib\Settings::get('unit_test.database').
        ";host=".Common\Lib\Settings::get('unit_test.server').
        ";port=".Common\Lib\Settings::get('unit_test.port');
        $dsn1 = "mysql:dbname=".Common\Lib\Settings::get('database.database').
        ";host=".Common\Lib\Settings::get('database.server').
        ";port=".Common\Lib\Settings::get('database.server_port');
        assert(
            $dsn1 != $dsn &&
            Common\Lib\Settings::get('database.database') != Common\Lib\Settings::get('unit_test.database')
        );
        $schemaFile = 'schema.sql';
        
        API\Lib\PDOWrapper::$unitTesting = true;        // For API testing
        Common\Lib\APIHelper::$UNIT_TESTING = true;     // For UI testing
        $conn = new \PDO(
            $dsn,
            Common\Lib\Settings::get('unit_test.username'),
            Common\Lib\Settings::get('unit_test.password'),
            array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4")
        );
        //Make PDO throw exceptions if they arise
        $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        unset($dsn);
        unset($dsn1);
        
        $oauthClientId = Common\Lib\Settings::get('oauth.client_id');
        $oauthClientSecret = Common\Lib\Settings::get('oauth.client_secret');
        $redirectUri = Settings::get('site.location')."login/";
        if (!self::$initalised) {
            $result = $conn->exec("drop database `".Common\Lib\Settings::get('unit_test.database')."`");
            $result = $conn->exec(
                "CREATE DATABASE `".
                Common\Lib\Settings::get('unit_test.database').
                "` /*!40100 CHARACTER SET utf8 COLLATE 'utf8_unicode_ci' */"
            );
            $result = $conn->exec("use `".Common\Lib\Settings::get('unit_test.database')."`");
           
            $schema = file_get_contents(__DIR__.'/../api/vendor/league/oauth2-server/sql/mysql.sql');
            $result = $conn->exec($schema);
            $conn->exec("INSERT INTO oauth_clients (id, secret, name, auto_approve)".
                    "VALUES('$oauthClientId', '$oauthClientSecret', 'test_user',1)");
            
            $conn->exec("INSERT INTO oauth_client_endpoints (client_id, redirect_uri)".
                    "VALUES ('$oauthClientId', '$redirectUri')");
          
             $schema = file_get_contents(__DIR__.'/../db/'.$schemaFile);
            $schema = str_replace("DELIMITER //", "", $schema);
            $schema = str_replace("DELIMITER ;", "", $schema);
            $schema = str_replace("END//", "END;", $schema);
           
            $result = $conn->exec($schema);
            $schema = file_get_contents(__DIR__.'/../db/languages.sql');
            $result = $conn->exec($schema);
            $schema = file_get_contents(__DIR__.'/../db/country_codes.sql');
            $result = $conn->exec($schema); 

            self::$initalised = true;
         } else {
            $result = $conn->exec("use `".Common\Lib\Settings::get('unit_test.database')."`");
            $tables = $conn->query(
                "SELECT t.TABLE_NAME FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA='Unit-Test'
                 AND t.TABLE_NAME NOT IN('Languages','Countries', 'TaskTypes', 'TaskStatus')"
            );

            foreach ($tables as $table) {
                $conn->exec("DELETE FROM $table[0]");
            }
            
            $conn->exec("INSERT INTO oauth_clients (id, secret, name, auto_approve)".
                    "VALUES('$oauthClientId', '$oauthClientSecret', 'test_user',1)");
            $conn->exec("INSERT INTO oauth_client_endpoints (client_id, redirect_uri)".
                    "VALUES ('$oauthClientId', '$redirectUri')");
            $schema = file_get_contents(__DIR__.'/../db/'.$schemaFile);
            $schema = str_replace("DELIMITER //", "", $schema);
            $schema = str_replace("DELIMITER ;", "", $schema);
            $schema = str_replace("END//", "END;", $schema);
           
            $result = $conn->exec($schema);
        } 
    }
    
   
    // Create system badge by default
    public static function createBadge(
        $id = null,
        $title = "System Badge 1",
        $description = "System Badge 1 Description",
        $ownerId = null
    ) {
        $newBadge = new Common\Protobufs\Models\Badge();
        $newBadge->setId($id);
        $newBadge->setTitle($title);
        $newBadge->setDescription($description);
        $newBadge->setOwnerId($ownerId);
        return $newBadge;
    }
    public static function createBannedUser(
        $userId,
        $userIdAdmin,
        $banType = Common\Enums\BanTypeEnum::DAY,
        $comment = "FOOOO"
    ) {
        $bannedUser = new Common\Protobufs\Models\BannedUser();
        $bannedUser->setUserId($userId);
        $bannedUser->setUserIdAdmin($userIdAdmin);
        $bannedUser->setBanType($banType);
        $bannedUser->setComment($comment);
        
        return $bannedUser;
    }
    
    public static function createBannedOrg(
        $orgId,
        $userIdAdmin,
        $banType = Common\Enums\BanTypeEnum::DAY,
        $comment = "FOOOO"
    ) {
        $bannedOrg = new Common\Protobufs\Models\BannedOrganisation();
        $bannedOrg->setOrgId($orgId);
        $bannedOrg->setUserIdAdmin($userIdAdmin);
        $bannedOrg->setBanType($banType);
        $bannedOrg->setComment($comment);
        
        return $bannedOrg;
    }
    
    public static function createLocale(
        $langName = "French",
        $langCode = "fr",
        $countryName = "FRANCE",
        $countryCode = "FR"
    ) {
        $locale = new Common\Protobufs\Models\Locale();
        
        $locale->setLanguageName($langName);
        $locale->setLanguageCode($langCode);
        $locale->setCountryName($countryName);
        $locale->setCountryCode($countryCode);
        
        return $locale;
    }
    
    public static function createOrg(
        $id = null,
        $name = "Organisation 1",
        $biography = "Organisation Biography 1",
        $homepage = "http://www.organisation1.org"
    ) {
        $org = new Common\Protobufs\Models\Organisation();
        $org->setId($id);
        $org->setName($name);
        $org->setBiography($biography);
        $org->setHomePage($homepage);
        return $org;
    }
    
    // password = hash("sha512", "abcdefghikjlmnop")
    public static function createUser(
        $userId = null,
        $displayName = "User 1",
        $biography = "User 1 Bio",
        $email = "user1@test.com",
        $nonce = "123456789",
        $password = "2d5e2eb5e2d5b1358161c8418e2fd3f46a431452a724257907d4a3317677a99414463452507ef607941e14044363aab9669578ce5f9517cb36c9acb32f492393",
        $languageCode = null,
        $countryCode = null,
        $createdTime = null
    ) {
        $locale = new Common\Protobufs\Models\Locale();
        $user = new Common\Protobufs\Models\User();
        
        $user->setId($userId);
        $user->setDisplayName($displayName);
        $user->setBiography($biography);
        $user->setEmail($email);
        $user->setNonce($nonce);
        $user->setPassword($password);
        
        $locale->setLanguageCode($languageCode);
        $locale->setCountryCode($countryCode);
        $user->setNativeLocale($locale);
        
        $user->setCreatedTime($createdTime);
        return $user;
    }
    
    public static function createUserPersonalInfo
       (
        $userId,
        $id = null,
        $firstName = "John",
        $lastName = "Doe",
        $mobileNumber = 333444666,
        $businessNumber = 42,
        $langPref = 1785, //Current ID for English in database
        $jobTitle = "Derp",
        $address = "This is a real place",
        $city = "Lightless City",
        $country = "Forgotten Land",
        $receiveCredit = 0
    ) {
        $userInfo = new Common\Protobufs\Models\UserPersonalInformation();
        
        $userInfo->setUserId($userId);
        $userInfo->setId($id);
        $userInfo->setFirstName($firstName);
        $userInfo->setLastName($lastName);
        $userInfo->setMobileNumber($mobileNumber);
        $userInfo->setBusinessNumber($businessNumber);
        $userInfo->setLanguagePreference($langPref);
        $userInfo->setJobTitle($jobTitle);
        $userInfo->setAddress($address);
        $userInfo->setCity($city);
        $userInfo->setCountry($country);
        $userInfo->setReceiveCredit($receiveCredit);
        
        return $userInfo ;
    }
    
    // Create default projects by specifying just the organisation id
    public static function createProject
       (
        $organisationId,
        $id = null,
        $title = "Project 1",
        $description = "Project 1 Description",
        $deadline = "2020-03-29 16:30:00",
        $impact = "Project 1 Impact",
        $reference = "Project 1 Reference",
        $wordcount = 123456,
        $sourceCountryCode = "IE",
        $sourceLanguageCode = "en",
        $tags = array("Project", "Tags"),
        $createdTime = null
    ) {
        $sourceLocale = new Common\Protobufs\Models\Locale();
        $project = new Common\Protobufs\Models\Project();
        
        $project->setId($id);
        $project->setTitle($title);
        $project->setDescription($description);
        $project->setDeadline($deadline);
        $project->setImpact($impact);
        $project->setReference($reference);
        $project->setWordCount($wordcount);
        
        $sourceLocale->setCountryCode($sourceCountryCode);
        $sourceLocale->setLanguageCode($sourceLanguageCode);
        $project->setSourceLocale($sourceLocale);
        
        //disabled tag related code to avoid issues arising from use of updateTags function
        $projectTagList = array();
        foreach ($tags as $tagLabel) {
            $tag = new Common\Protobufs\Models\Tag();
            $tag->setLabel($tagLabel);
            $projectTagList[] = $tag;
        }
        $projectTags = API\DAO\TagsDao::updateTags($project->getId(), $projectTagList);
        
        foreach ($projectTags as $projectTag) {
            $project->addTag($projectTag);
        }
        
        $project->setOrganisationId($organisationId);
        $project->setCreatedTime($createdTime);
        return $project;
    }
    
    public static function createTag($id = null, $label = "I'm a tag")
    {
        $tag = new Common\Protobufs\Models\Tag();
        $tag->setLabel($label);
        
        return $tag;
    }
    
    public static function createTask
       (
        $projectId,
        $id = null,
        $title = "Task 1",
        $comment = "Task 1 Comment",
        $deadline = "2020-03-29 16:30:00",
        $wordcount = 123456,
        $tags = null,
        $type = Common\Enums\TaskTypeEnum::TRANSLATION,
        $status = Common\Enums\TaskStatusEnum::PENDING_CLAIM,
        $sourceCountryCode = "IE",
        $sourceLanguageCode = "en",
        $targetCountryCode = "FR",
        $targetLanguageCode = "fr",
        $published = 1,
        $createdTime = null
    ) {
        $task = new Common\Protobufs\Models\Task();
        $task->setId($id);
        $task->setProjectId($projectId);
        $task->setTitle($title);
        $task->setComment($comment);
        $task->setDeadline($deadline);
        $task->setWordCount($wordcount);
        $task->setTaskType($type);
        $task->setTaskStatus($status);
        
        $sourceLocale = new Common\Protobufs\Models\Locale();
        $sourceLocale->setLanguageCode($sourceLanguageCode);
        $sourceLocale->setCountryCode($sourceCountryCode);
        $task->setSourceLocale($sourceLocale);
        
        $targetLocale = new Common\Protobufs\Models\Locale();
        $targetLocale->setLanguageCode($targetLanguageCode);
        $targetLocale->setCountryCode($targetCountryCode);
        $task->setTargetLocale($targetLocale);
        
        $task->setPublished($published);
        $task->setCreatedTime($createdTime);
    
        if (!is_null($tags)) {
            $i = 0;
            $taskTag = new Common\Protobufs\Models\Tag();
            foreach ($tags as $tagLabel) {
                $taskTag->setId($i+100);
                $taskTag->setLabel($tagLabel[0]);
                $task->addTag($taskTag);
                $i++;
            }
        }
        
        return $task;
    }

    public static function createProjectFile
       (
        $userId,
        $projectid,
        $filename = "createProjectFileTest.txt",
        $mime = "text/plain",
        $token = "createProjectFileTest.txt"
    ) {
        $projectFile = new Common\Protobufs\Models\ProjectFile();
        $projectFile->setUserId($userId);
        $projectFile->setProjectId($projectid);
        $projectFile->setFilename($filename);
        $projectFile->setMime($mime);
        $projectFile->setToken($token);
        return $projectFile;
    }

    public static function createTaskFileInfo
       (
        $taskId,
        $userId,
        $filename = "dummy_file.txt",
        $contentType = "text/plain",
        $version = 0
    ) {
        return array(
            "taskId" => $taskId,
            "userId" => $userId,
            "filename" => $filename,
            "contentType" => $contentType,
            "version" => $version
        );
    }
}
