<?php

namespace SolasMatch\Tests\API;

use \SolasMatch\Tests\UnitTestHelper;
use \SolasMatch\API as API;
use \SolasMatch\Common as Common;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TaskDao.class.php';
require_once __DIR__.'/../UnitTestHelper.php';


class ProjectDaoTest extends \PHPUnit_Framework_TestCase
{
    public function testProjectCreate()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
                
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::createUpdate($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        $this->assertEquals($project->getTitle(), $insertedProject->getTitle());
        $this->assertEquals($project->getDescription(), $insertedProject->getDescription());
        $this->assertEquals($project->getDeadline(), $insertedProject->getDeadline());
        $this->assertEquals($project->getImpact(), $insertedProject->getImpact());
        $this->assertEquals($project->getReference(), $insertedProject->getReference());
        $this->assertEquals($project->getWordCount(), $insertedProject->getWordCount());
        
        $this->assertEquals(
            $project->getSourceLocale()->getLanguageCode(),
            $insertedProject->getSourceLocale()->getLanguageCode()
        );
        $this->assertEquals(
            $project->getSourceLocale()->getCountryCode(),
            $insertedProject->getSourceLocale()->getCountryCode()
        );
        
        $projectTags = $insertedProject->getTagList();
        $this->assertCount(2, $projectTags);
        foreach ($projectTags as $tag) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Tag", $tag);
        }
        
        $this->assertEquals($project->getOrganisationId(), $insertedProject->getOrganisationId());
        $this->assertNotNull($insertedProject->getCreatedTime());

    }
    
    public function testProjectUpdate()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::createUpdate($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $org2 = UnitTestHelper::createOrg(null, "Organisation 2", "Organisation 2 Bio", "http://www.organisation2.org");
        $insertedOrg2 = API\DAO\OrganisationDao::insertAndUpdate($org2);
        $this->assertNotNull($insertedOrg2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg2);

        
        $insertedProject->setTitle("Updated Title");
        $insertedProject->setDescription("Updated Description");
        $insertedProject->setDeadline("2030-03-10 00:00:00");
        $insertedProject->setImpact("Updated Impact");
        $insertedProject->setReference("Updated Reference");
        $insertedProject->setWordCount(654321);
        
        $sourceLocale = new Common\Protobufs\Models\Locale();
        $sourceLocale->setCountryCode("AZ");
        $sourceLocale->setLanguageCode("agx");
        $insertedProject->setSourceLocale($sourceLocale);
        
        $newTags = array("Updated Project", "Updated Tags");
        foreach ($newTags as $tagLabel) {
            $insertedProject->addTag(API\DAO\TagsDao::create($tagLabel));
        }
        
        $insertedProject->setOrganisationId($insertedOrg2->getId());
        $insertedProject->setCreatedTime("2030-06-20 00:00:00");
  
        // Success
        $updatedProject = API\DAO\ProjectDao::createUpdate($insertedProject);
        $this->assertNotNull($updatedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $updatedProject);
        $this->assertEquals($insertedProject->getTitle(), $updatedProject->getTitle());
        $this->assertEquals($insertedProject->getDescription(), $updatedProject->getDescription());
        $this->assertEquals($insertedProject->getDeadline(), $updatedProject->getDeadline());
        $this->assertEquals($insertedProject->getImpact(), $updatedProject->getImpact());
        $this->assertEquals($insertedProject->getReference(), $updatedProject->getReference());
        $this->assertEquals($insertedProject->getWordCount(), $updatedProject->getWordCount());
        
        $this->assertEquals(
            $insertedProject->getSourceLocale()->getLanguageCode(),
            $updatedProject->getSourceLocale()->getLanguageCode()
        );
        $this->assertEquals(
            $insertedProject->getSourceLocale()->getCountryCode(),
            $updatedProject->getSourceLocale()->getCountryCode()
        );

        $projectTagsAfterUpdate = API\DAO\ProjectDao::getTags($insertedProject->getId());
        $this->assertCount(4, $projectTagsAfterUpdate);
        foreach ($projectTagsAfterUpdate as $tag) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Tag", $tag);
        }
        
        $this->assertEquals($insertedProject->getOrganisationId(), $updatedProject->getOrganisationId());
        $this->assertEquals($insertedProject->getCreatedTime(), $updatedProject->getCreatedTime());
        
    }
    
    public function testGetProject()
    {
        UnitTestHelper::teardownDb();

        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::createUpdate($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        // Success
        $resultGetProject = API\DAO\ProjectDao::getProject($insertedProject->getId());
        $this->assertNotNull($resultGetProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $resultGetProject);
        
        // Failure
        $resultGetProjectFailure = API\DAO\ProjectDao::getProject(99);
        $this->assertNull($resultGetProjectFailure);
    }
    
    public function testDelete()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::createUpdate($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        $this->assertEquals($project->getTitle(), $insertedProject->getTitle());
        $this->assertEquals($project->getDescription(), $insertedProject->getDescription());
        $this->assertEquals($project->getDeadline(), $insertedProject->getDeadline());
        $this->assertEquals($project->getImpact(), $insertedProject->getImpact());
        $this->assertEquals($project->getReference(), $insertedProject->getReference());
        $this->assertEquals($project->getWordCount(), $insertedProject->getWordCount());
        
        $this->assertEquals(
            $project->getSourceLocale()->getLanguageCode(),
            $insertedProject->getSourceLocale()->getLanguageCode()
        );
        $this->assertEquals(
            $project->getSourceLocale()->getCountryCode(),
            $insertedProject->getSourceLocale()->getCountryCode()
        );
        
        $afterDelete = API\DAO\ProjectDao::delete($insertedProject->getId());
        $this->assertEquals("1", $afterDelete);
        $tryRedelete = API\DAO\ProjectDao::delete($insertedProject->getId());
        $this->assertEquals("0", $tryRedelete);
    }
    
    public function testArchiveProject()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::createUpdate($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        $projectId = $insertedProject->getId();
    
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $task = UnitTestHelper::createTask($projectId);
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        //create task file info for non existant file
        $fileInfo = UnitTestHelper::createTaskFileInfo($translationTask->getId(), $insertedUser->getId());
        API\DAO\TaskDao::recordFileUpload(
            $fileInfo['taskId'],
            $fileInfo['filename'],
            $fileInfo['contentType'],
            $fileInfo['userId'],
            $fileInfo['version']
        );
        
        //create project file info for non existant file
        $file = UnitTestHelper::createProjectFile($insertedUser->getId(), $projectId);
        API\DAO\ProjectDao::recordProjectFileInfo(
            $file->getProjectId(),
            $file->getFilename(),
            $file->getUserId(),
            $file->getMime()
        );
        
        // Success
        $resultArchiveProject = API\DAO\ProjectDao::archiveProject($insertedProject->getId(), $insertedUser->getId());
        $this->assertEquals("1", $resultArchiveProject);
                
        // Failure
        $resultArchiveProjectFailure = API\DAO\ProjectDao::archiveProject($projectId, $insertedUser->getId());
        $this->assertEquals("0", $resultArchiveProjectFailure);
    }
    
    
    public function testGetArchivedProject()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::createUpdate($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        $projectId = $insertedProject->getId();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);

        $task = UnitTestHelper::createTask($projectId);
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        //create task file info for non existant file
        $fileInfo = UnitTestHelper::createTaskFileInfo($translationTask->getId(), $insertedUser->getId());
        API\DAO\TaskDao::recordFileUpload(
            $fileInfo['taskId'],
            $fileInfo['filename'],
            $fileInfo['contentType'],
            $fileInfo['userId'],
            $fileInfo['version']
        );
        
        //create project file info for non existant file
        $file = UnitTestHelper::createProjectFile($insertedUser->getId(), $projectId);
        API\DAO\ProjectDao::recordProjectFileInfo(
            $file->getProjectId(),
            $file->getFilename(),
            $file->getUserId(),
            $file->getMime()
        );
                
        $resultArchiveProject = API\DAO\ProjectDao::archiveProject($projectId, $insertedUser->getId());
        $this->assertEquals("1", $resultArchiveProject);
        
        // Success
        $resultGetArchivedProject = API\DAO\ProjectDao::getArchivedProject(
            $insertedProject->getId(),
            $insertedProject->getOrganisationId(),
            $insertedProject->getTitle(),
            $insertedProject->getDescription(),
            $insertedProject->getImpact(),
            $insertedProject->getDeadline(),
            $insertedProject->getReference(),
            $insertedProject->getWordCount(),
            $insertedProject->getCreatedTime(),
            date("Y-m-d H:i:s"),
            $insertedUser->getId()
        );
        
        $this->assertCount(1, $resultGetArchivedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\ArchivedProject", $resultGetArchivedProject[0]);
        $resultGetArchivedProject = $resultGetArchivedProject[0];
        $this->assertEquals($insertedProject->getTitle(), $resultGetArchivedProject->getTitle());
        $this->assertEquals($insertedProject->getDescription(), $resultGetArchivedProject->getDescription());
        $this->assertEquals($insertedProject->getDeadline(), $resultGetArchivedProject->getDeadline());
        $this->assertEquals($insertedProject->getImpact(), $resultGetArchivedProject->getImpact());
        $this->assertEquals($insertedProject->getReference(), $resultGetArchivedProject->getReference());
        $this->assertEquals($insertedProject->getWordCount(), $resultGetArchivedProject->getWordCount());
        $this->assertEquals(
            $insertedProject->getSourceLocale()->getCountryCode(),
            $resultGetArchivedProject->getSourceLocale()->getCountryCode()
        );
        $this->assertEquals(
            $insertedProject->getSourceLocale()->getLanguageCode(),
            $resultGetArchivedProject->getSourceLocale()->getLanguageCode()
        );
        $this->assertNotNull($resultGetArchivedProject->getArchivedDate());
        $this->assertNotNull($resultGetArchivedProject->getUserIdArchived());
        
        // Failure
        $resultGetArchivedProjectFailure = API\DAO\ProjectDao::getArchivedProject(99);
        $this->assertNull($resultGetArchivedProjectFailure);
    }
    
    public function testGetProjectTasks()
    {
        UnitTestHelper::teardownDb();
        
         $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);

        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::createUpdate($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2", "Task 2 Comment");

        $insertedTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $insertedTask);
        
        $insertedTask2 = API\DAO\TaskDao::save($task2);
        $this->assertNotNull($insertedTask2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $insertedTask2);
        
        // Success
        $resultGetProjectTasks = API\DAO\ProjectDao::getProjectTasks($insertedProject->getId());
        $this->assertCount(2, $resultGetProjectTasks);
        foreach ($resultGetProjectTasks as $task) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $task);
        }
        
        // Failure
        $resultGetProjectTasksFailure = API\DAO\ProjectDao::getProjectTasks(999);
        $this->assertNull($resultGetProjectTasksFailure);
    }
    
    public function testAddProjectTag()
    {
        UnitTestHelper::teardownDb();
        
         $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);

        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::createUpdate($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);

        $projectTag1 = API\DAO\TagsDao::create("New Project Tag");
        $this->assertNotNull($projectTag1);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Tag", $projectTag1);
        $this->assertEquals("New Project Tag", $projectTag1->getLabel());
        
        //Try to add tag, succeeds
        $resultAddProjectTag = API\DAO\ProjectDao::addProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("1", $resultAddProjectTag);
        
        //Try to add same tag again, fails
        $resultAddProjectTagFailure = API\DAO\ProjectDao::addProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("0", $resultAddProjectTagFailure);
    }
    
    
    public function testRemoveProjectTag()
    {
        UnitTestHelper::teardownDb();

         $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);

        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::createUpdate($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);

        $projectTag1 = API\DAO\TagsDao::create("New Project Tag");
        $this->assertNotNull($projectTag1);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Tag", $projectTag1);
        $this->assertEquals("New Project Tag", $projectTag1->getLabel());
        
        $addProjectTag = API\DAO\ProjectDao::addProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("1", $addProjectTag);
        
        //try to remove tag, succeeds
        $resultRemoveProjectTag = API\DAO\ProjectDao::removeProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("1", $resultRemoveProjectTag);
        
        //try to remove already deleted tag, fails
        $resultRemoveProjectTagFailure = API\DAO\ProjectDao::removeProjectTag(
            $project->getId(),
            $projectTag1->getId()
        );
        $this->assertEquals("0", $resultRemoveProjectTagFailure);
    }
    
    
    public function testGetTags()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);

        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::createUpdate($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $resultGetTags = API\DAO\ProjectDao::getTags($project->getId());
        $this->assertCount(2, $resultGetTags);
        foreach ($resultGetTags as $projectTag) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Tag", $projectTag);
        }
    }
    
    public function testDeleteProjectTags()
    {
        UnitTestHelper::teardownDb();
        
         $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::createUpdate($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        //assert that there are tags associated with the project
        $resultGetTags = API\DAO\ProjectDao::getTags($project->getId());
        $this->assertCount(2, $resultGetTags);
        foreach ($resultGetTags as $projectTag) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Tag", $projectTag);
        }
        
        //assert that some project tags were deleted
        $afterDeleteTags = API\DAO\ProjectDao::deleteProjectTags($project->getId());
        $this->assertEquals("1", $afterDeleteTags);
        
        //assert that there are no project tags left after deleting all
        $getTagsAfterDelete = API\DAO\ProjectDao::getTags($project->getId());
        $this->assertNull($getTagsAfterDelete);
        
        //assert that a second call to deleteProjectTags() changes nothing.
        $tryRedelete = API\DAO\ProjectDao::deleteProjectTags($project->getId());
        $this->assertEquals("0", $tryRedelete);
    }
    
    public function testRecordProjectFileInfo()
    {
        UnitTestHelper::teardownDb();
        
         $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::createUpdate($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
    
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        // Success
        $resultRecordProjectFileInfo = API\DAO\ProjectDao::recordProjectFileInfo(
            $insertedProject->getId(),
            "saveProjectFileTest.txt",
            $insertedUser->getId(),
            "text/plain"
        );
        $this->assertNotNull($resultRecordProjectFileInfo);
        
        // Failure
        $resultRecordProjectFileInfoFailure = API\DAO\ProjectDao::recordProjectFileInfo(
            $insertedProject->getId(),
            "saveProjectFileTest.txt",
            $insertedUser->getId(),
            "text/plain"
        );
        $this->assertNull($resultRecordProjectFileInfoFailure);
    }
    
    public function testGetProjectFileInfo()
    {
        UnitTestHelper::teardownDb();
        
         $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::createUpdate($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
   
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $resultRecordProjectFileInfo = API\DAO\ProjectDao::recordProjectFileInfo(
            $insertedProject->getId(),
            "saveProjectFileTest.txt",
            $insertedUser->getId(),
            "text/plain"
        );
        $this->assertNotNull($resultRecordProjectFileInfo);
        
        // Success
        $resultGetProjectFileInfoSuccess = API\DAO\ProjectDao::getProjectFileInfo(
            $insertedProject->getId(),
            $insertedUser->getId(),
            "saveProjectFileTest.txt",
            "saveProjectFileTest.txt",
            "text/plain"
        );
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\ProjectFile", $resultGetProjectFileInfoSuccess);
        
        // Failure
        $resultGetProjectFileInfoFailure = API\DAO\ProjectDao::getProjectFileInfo(
            999,
            $insertedUser->getId(),
            "saveProjectFileTest.txt",
            "saveProjectFileTest.txt",
            "text/plain"
        );
        $this->assertNull($resultGetProjectFileInfoFailure);
    }
    
    public function testGetArchivedTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::createUpdate($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId(),null,"My Task");
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        //create task file info for non existant file
        $fileInfo = UnitTestHelper::createTaskFileInfo($translationTask->getId(), $insertedUser->getId());
        API\DAO\TaskDao::recordFileUpload(
        $fileInfo['taskId'],
        $fileInfo['filename'],
        $fileInfo['contentType'],
        $fileInfo['userId'],
        $fileInfo['version']
        );
        
        // Success
        $archiveTask = API\DAO\TaskDao::moveToArchiveById($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $archiveTask);
        
        $getArchiveTask = API\DAO\ProjectDao::getArchivedTask($insertedProject->getId(),null,"My Task");
        $this->assertNotNull($getArchiveTask[0]);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\ArchivedTask", $getArchiveTask[0]);
        $this->assertEquals("My Task", $getArchiveTask[0]->getTitle());
    }
}
