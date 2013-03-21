<?php

require_once __DIR__.'/../Common/Settings.class.php';
require_once __DIR__.'/../Common/lib/PDOWrapper.class.php';
require_once __DIR__.'/../Common/TaskTypeEnum.php';
require_once __DIR__.'/../Common/TaskStatusEnum.php';

class UnitTestHelper
{
    private function __constuct() {}
    
    public static function teardownDb()
    {
        $dsn = "mysql:host=".Settings::get('unit_test.server').";dbname=".Settings::get('unit_test.database').
                ";port=".Settings::get('unit_test.port');
        $dsn1 = "mysql:host=".Settings::get('database.server').";dbname=".Settings::get('database.database').
                ";port=".Settings::get('database.server_port');
        assert($dsn1 != $dsn);
        
        PDOWrapper::$unitTesting = true;
        $conn = new PDO($dsn,
                        Settings::get('unit_test.username'), Settings::get('unit_test.password'),
                        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));         
        unset($dsn);
        unset($dsn1);
        $tables = $conn->query("SELECT t.TABLE_NAME FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA='Unit-Test'
                                AND t.TABLE_NAME NOT IN('Languages','Countries', 'TaskTypes', 'TaskStatus')");
        
        foreach($tables as $table) $conn->query("DELETE FROM $table[0]");
        
        $conn->query("REPLACE INTO `Badges` (`id`, `owner_id`, `title`, `description`) VALUES
                    (3, NULL, 'Profile-Filler', 'Filled in required info for user profile.'),
                    (4, NULL, 'Registered', 'Successfully set up an account'),
                    (5, NULL, 'Native-Language', 'Filled in your native language on your user profile.');");
    }
    
    
   
    // Create system badge by default
    public static function createBadge($id = null, $title = "System Badge 1", $description = "System Badge 1 Description", $ownerId = null)
    {       
        $newBadge = new Badge();      
        $newBadge->setId($id);
        $newBadge->setTitle($title);
        $newBadge->setDescription($description);
        $newBadge->setOwnerId($ownerId);       
        return $newBadge;
    }
    
    public static function createOrg($id = null, $name = "Organisation 1", $biography = "Organisation Biography 1", $homepage = "http://www.organisation1.org")
    {
        $org = new Organisation();
        $org->setId($id);
        $org->setName($name);
        $org->setBiography($biography);
        $org->setHomePage($homepage);        
        return $org;
    }
    
    // password = hash("sha512", "abcdefghikjlmnop")
    public static function createUser($userId = null, $displayName = "User 1", $biography = "User 1 Bio", $email = "user1@test.com", $nonce = "123456789"
            , $password = "2d5e2eb5e2d5b1358161c8418e2fd3f46a431452a724257907d4a3317677a99414463452507ef607941e14044363aab9669578ce5f9517cb36c9acb32f492393"
            , $nativeLangId = null, $nativeRegionId = null, $createdTime = null)
    {
        $user = new User();
        $user->setUserId($userId);
        $user->setDisplayName($displayName);   
        $user->setBiography($biography);
        $user->setEmail($email);
        $user->setNonce($nonce);
        $user->setPassword($password);
        $user->setNativeLangId($nativeLangId);
        $user->setNativeRegionId($nativeRegionId);
        $user->setCreatedTime($createdTime);    
        return $user;
    }
    
    // Create default projects by specifying just the organisation id
    public static function createProject($organisationId ,$id = null, $title = "Project 1", $description = "Project 1 Description",
            $deadline = "2020-03-29 16:30:00", $impact = "Project 1 Impact", $reference = "Project 1 Reference",
            $wordcount = 123456, $sourceCountryCode = "IE", $sourceLanguageCode = "en", $tags = array("Project", "Tags"), $createdTime = null)
    {
        $project = new Project();                
        $project->setId($id);
        $project->setTitle($title);
        $project->setDescription($description);
        $project->setDeadline($deadline);
        $project->setImpact($impact);
        $project->setReference($reference);
        $project->setWordCount($wordcount);
        $project->setSourceCountryCode($sourceCountryCode);
        $project->setSourceLanguageCode($sourceLanguageCode);
        $project->setTag($tags);
        $project->setOrganisationId($organisationId);
        $project->setCreatedTime($createdTime);
        return $project;
    }
    
    public static function createTask($projectId, $id = null, $title = "Task 1", $comment = "Task 1 Comment", $deadline = "2020-03-29 16:30:00",
            $wordcount = 123456, $tags = array("Task", "Tags"), $type = TaskTypeEnum::TRANSLATION, $status = TaskStatusEnum::PENDING_CLAIM,
            $sourceCountryCode = "IE", $sourceLanguageCode = "en", $targetCountryCode = "FR", $targetCountryLanguage = "fr",
            $published = 1, $createdTime = null)
    {
        $task = new Task();
        $task->setId($id);
        $task->setProjectId($projectId);
        $task->setTitle($title);        
        $task->setComment($comment);        
        $task->setDeadline($deadline);
        $task->setWordCount($wordcount);
        $task->setTaskType($type);
        $task->setTaskStatus($status);
        $task->setTargetCountryCode($targetCountryCode);
        $task->setTargetLanguageCode($targetCountryLanguage);
        $task->setSourceCountryCode($sourceCountryCode);
        $task->setSourceLanguageCode($sourceLanguageCode);
        $task->setPublished($published);
        $task->setCreatedTime($createdTime);
        
        $i = 0;
        foreach($tags as $tagLabel) {
            $taskTag = new Tag();
            $taskTag->setId($i+100);
            $taskTag->setLabel($tagLabel[0]);
            $task->addTag($taskTag);
            $i++;
        }
        
        return $task;
    }

    public static function createProjectFile($userId, $projectid, $filename = "saveProjectFileTest.txt",
            $mime = "saveProjectFileTest.txt", $token = "text/plain")
    {
        $projectFile = new ProjectFile();
        $projectFile->setUserId($userId);
        $projectFile->setProjectId($projectid);
        $projectFile->setFilename($filename);
        $projectFile->setMime($mime);
        $projectFile->setToken($token);        
        return $projectFile;
    }
    
    
}

?>
