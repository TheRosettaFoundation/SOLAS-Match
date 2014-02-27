<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TaskDao.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../UnitTestHelper.php';


class ProjectDaoTest extends PHPUnit_Framework_TestCase
{
    public function testProjectCreate()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
                
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        
        // Success
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);        
        $this->assertNotNull($insertedProject->getId());        
        $this->assertEquals($project->getTitle(), $insertedProject->getTitle());
        $this->assertEquals($project->getDescription(), $insertedProject->getDescription());
        $this->assertEquals($project->getDeadline(), $insertedProject->getDeadline());
        $this->assertEquals($project->getImpact(), $insertedProject->getImpact());
        $this->assertEquals($project->getReference(), $insertedProject->getReference());
        $this->assertEquals($project->getWordCount(), $insertedProject->getWordCount());
        
        $this->assertEquals($project->getSourceLocale()->getLanguageCode(), $insertedProject->getSourceLocale()->getLanguageCode());
        $this->assertEquals($project->getSourceLocale()->getCountryCode(), $insertedProject->getSourceLocale()->getCountryCode());
        
        $projectTags = $insertedProject->getTagList();
        $this->assertCount(2, $projectTags);
        foreach ($projectTags as $tag) {
            $this->assertInstanceOf("Tag", $tag);
        }
        
        $this->assertEquals($project->getOrganisationId(), $insertedProject->getOrganisationId());
        $this->assertNotNull($insertedProject->getCreatedTime());    

    }
    
    public function testProjectUpdate()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);          
        
        $org2 = UnitTestHelper::createOrg(null, "Organisation 2", "Organisation 2 Bio", "http://www.organisation2.org");
        $insertedOrg2 = OrganisationDao::insertAndUpdate($org2);
        $this->assertInstanceOf("Organisation", $insertedOrg2);
        $this->assertNotNull($insertedOrg2->getId());
        
        $insertedProject->setTitle("Updated Title");
        $insertedProject->setDescription("Updated Description");
        $insertedProject->setDeadline("2030-03-10 00:00:00");
        $insertedProject->setImpact("Updated Impact");
        $insertedProject->setReference("Updated Reference");
        $insertedProject->setWordCount(654321);
        
        $sourceLocale = new Locale();              
        $sourceLocale->setCountryCode("AZ");
        $sourceLocale->setLanguageCode("agx");
        $insertedProject->setSourceLocale($sourceLocale);  
        
        $newTags = array("Updated Project", "Updated Tags");
        foreach ($newTags as $tagLabel) {
            $insertedProject->addTag(TagsDao::create($tagLabel));
        }
        
        $insertedProject->setOrganisationId($insertedOrg2->getId());
        $insertedProject->setCreatedTime("2030-06-20 00:00:00");
  
        // Success
        $updatedProject = ProjectDao::createUpdate($insertedProject);
        $this->assertInstanceOf("Project", $updatedProject);
        $this->assertEquals($insertedProject->getTitle(), $updatedProject->getTitle());
        $this->assertEquals($insertedProject->getDescription(), $updatedProject->getDescription());
        $this->assertEquals($insertedProject->getDeadline(), $updatedProject->getDeadline());
        $this->assertEquals($insertedProject->getImpact(), $updatedProject->getImpact());
        $this->assertEquals($insertedProject->getReference(), $updatedProject->getReference());
        $this->assertEquals($insertedProject->getWordCount(), $updatedProject->getWordCount());
        
        $this->assertEquals($insertedProject->getSourceLocale()->getLanguageCode(), $updatedProject->getSourceLocale()->getLanguageCode());
        $this->assertEquals($insertedProject->getSourceLocale()->getCountryCode(), $updatedProject->getSourceLocale()->getCountryCode());

        $projectTagsAfterUpdate = $updatedProject->getTag();
        $this->assertCount(4, $projectTagsAfterUpdate);
        foreach ($projectTagsAfterUpdate as $tag) {
            $this->assertInstanceOf("Tag", $tag);
        }
        
        $this->assertEquals($insertedProject->getOrganisationId(), $updatedProject->getOrganisationId());
        $this->assertEquals($insertedProject->getCreatedTime(), $updatedProject->getCreatedTime()); 
        
    }
    
    public function testGetProject()
    {
        UnitTestHelper::teardownDb();

        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        
        // Success
        $resultGetProject = ProjectDao::getProject(
            $insertedProject->getId(), $project->getTitle(), $project->getDescription(), $project->getImpact(), $project->getDeadline(), $project->getOrganisationId(), $project->getReference(), $project->getWordCount(), $insertedProject->getCreatedTime(), $project->getSourceLocale()->getCountryCode(), $project->getSourceLocale()->getLanguageCode()
        );
        
        $this->assertCount(1, $resultGetProject);
        $this->assertInstanceOf("Project", $resultGetProject[0]);        
        
        // Failure
        $resultGetProjectFailure = ProjectDao::getProject(99);
        $this->assertNull($resultGetProjectFailure);
    }
    
    public function testDelete()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        
        // Success
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);
        $this->assertNotNull($insertedProject->getId());
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
        
        $afterDelete = ProjectDao::delete($insertedProject->getId());
        $this->assertEquals("1", $afterDelete);
        $tryRedelete = ProjectDao::delete($insertedProject->getId());
        $this->assertEquals("0", $tryRedelete);
    }
    
    public function testArchiveProject()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);
        $projectId = $insertedProject->getId();     
    
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $translationTask = TaskDao::save($task);
        
        //create task file info for non existant file
        $fileInfo = UnitTestHelper::createTaskFileInfo($translationTask->getId(), $insertedUser->getId());
        TaskDao::recordFileUpload(
            $fileInfo['taskId'],
            $fileInfo['filename'],
            $fileInfo['contentType'],
            $fileInfo['userId'],
            $fileInfo['version']
        );
        
        $this->assertInstanceOf("Task", $translationTask);
        $this->assertNotNull($translationTask->getId());
        
        //create project file info for non existant file
        $file = UnitTestHelper::createProjectFile($insertedUser->getId(), $projectId);
        ProjectDao::recordProjectFileInfo(
            $file->getProjectId(),
            $file->getFilename(),
            $file->getUserId(),
            $file->getMime()
        );
        
        // Success
        $resultArchiveProject = ProjectDao::archiveProject($insertedProject->getId(), $insertedUser->getId());
        $this->assertEquals("1", $resultArchiveProject);
                
        // Failure
        $resultArchiveProjectFailure = ProjectDao::archiveProject($insertedProject->getId(), $insertedUser->getId());
        $this->assertEquals("0", $resultArchiveProjectFailure);
    }
    
    
    public function testGetArchivedProject()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);  
        $projectId = $insertedProject->getId();       
    
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());

        $task = UnitTestHelper::createTask($insertedProject->getId());
        $translationTask = TaskDao::save($task);
        
        //create task file info for non existant file
        $fileInfo = UnitTestHelper::createTaskFileInfo($translationTask->getId(), $insertedUser->getId());
        TaskDao::recordFileUpload(
        $fileInfo['taskId'],
        $fileInfo['filename'],
        $fileInfo['contentType'],
        $fileInfo['userId'],
        $fileInfo['version']
        );
        
        //create project file info for non existant file
        $file = UnitTestHelper::createProjectFile($insertedUser->getId(), $projectId);
        ProjectDao::recordProjectFileInfo(
            $file->getProjectId(),
            $file->getFilename(),
            $file->getUserId(),
            $file->getMime()
        );
        
        $resultArchiveProject = ProjectDao::archiveProject($insertedProject->getId(), $insertedUser->getId());
        $this->assertEquals("1", $resultArchiveProject);
        
        // Success
        $resultGetArchivedProject = ProjectDao::getArchivedProject(
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
        $this->assertInstanceOf("ArchivedProject", $resultGetArchivedProject[0]);
        $resultGetArchivedProject = $resultGetArchivedProject[0];
        $this->assertEquals($insertedProject->getTitle(), $resultGetArchivedProject->getTitle());
        $this->assertEquals($insertedProject->getDescription(), $resultGetArchivedProject->getDescription());
        $this->assertEquals($insertedProject->getDeadline(), $resultGetArchivedProject->getDeadline());
        $this->assertEquals($insertedProject->getImpact(), $resultGetArchivedProject->getImpact());
        $this->assertEquals($insertedProject->getReference(), $resultGetArchivedProject->getReference());
        $this->assertEquals($insertedProject->getWordCount(), $resultGetArchivedProject->getWordCount());
        //        $this->assertEquals($insertedProject->getSourceCountryCode(), $resultGetArchivedProject->getCountryCode());
        //        $this->assertEquals($insertedProject->getSourceLanguageCode(), $resultGetArchivedProject->getLanguageCode());
        $this->assertNotNull($resultGetArchivedProject->getArchivedDate());
        $this->assertNotNull($resultGetArchivedProject->getUserIdArchived());
        
        // Failure
        $resultGetArchivedProjectFailure = ProjectDao::getArchivedProject(99);
        $this->assertNull($resultGetArchivedProjectFailure);
    }
    
    public function testGetProjectTasks()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);

        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2", "Task 2 Comment");        

        $insertedTask = TaskDao::save($task);
        $this->assertInstanceOf("Task", $insertedTask);
        
        $insertedTask2 = TaskDao::save($task2);
        $this->assertInstanceOf("Task", $insertedTask2);
        
        // Success
        $resultGetProjectTasks = ProjectDao::getProjectTasks($insertedProject->getId());
        $this->assertCount(2, $resultGetProjectTasks);
        foreach ($resultGetProjectTasks as $task) {
            $this->assertInstanceOf("Task", $task);
        }
        
        // Failure
        $resultGetProjectTasksFailure = ProjectDao::getProjectTasks(999);
        $this->assertNull($resultGetProjectTasksFailure);
    }
    
    public function testAddProjectTag()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());

        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);    
        $this->assertNotNull($project->getId());          

        $projectTag1 = TagsDao::create("New Project Tag");
        $this->assertInstanceOf("Tag", $projectTag1);
        $this->assertNotNull($projectTag1->getId());        
        $this->assertEquals("New Project Tag", $projectTag1->getLabel());
        
        // Success
        $resultAddProjectTag = ProjectDao::addProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("1", $resultAddProjectTag);
        
        // Failure
        $resultAddProjectTagFailure = ProjectDao::addProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("0", $resultAddProjectTagFailure);

    }
    
    
    public function testRemoveProjectTag()
    {
        UnitTestHelper::teardownDb();

        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());

        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);    
        $this->assertNotNull($project->getId());          

        $projectTag1 = TagsDao::create("New Project Tag");
        $this->assertInstanceOf("Tag", $projectTag1);
        $this->assertNotNull($projectTag1->getId());        
        $this->assertEquals("New Project Tag", $projectTag1->getLabel());
        
        $addProjectTag = ProjectDao::addProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("1", $addProjectTag);
        
        // Success
        $resultRemoveProjectTag = ProjectDao::removeProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("1", $resultRemoveProjectTag);
        
        // Failure
        $resultRemoveProjectTagFailure = ProjectDao::removeProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("0", $resultRemoveProjectTagFailure);
    }
    
    
    public function testGetTags()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());

        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);    
        $this->assertNotNull($project->getId());
        
        $resultGetTags = ProjectDao::getTags($project->getId());
        $this->assertCount(2, $resultGetTags);
        foreach ($resultGetTags as $projectTag) {
            $this->assertInstanceOf("Tag", $projectTag);
        }
    }
    
    public function testDeleteProjectTags()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);
        $this->assertNotNull($project->getId());
        
        //assert that there are tags associated with the project
        $resultGetTags = ProjectDao::getTags($project->getId());
        $this->assertCount(2, $resultGetTags);
        foreach ($resultGetTags as $projectTag) {
            $this->assertInstanceOf("Tag", $projectTag);
        }
        
        //assert that some project tags were deleted
        $afterDeleteTags = ProjectDao::deleteProjectTags($project->getId());
        $this->assertEquals("1", $afterDeleteTags);
        
        //assert that there are no project tags left after deleting all
        $getTagsAfterDelete = ProjectDao::getTags($project->getId());
        $this->assertNull($getTagsAfterDelete);
        
        //assert that a second call to deleteProjectTags() changes nothing.
        $tryRedelete = ProjectDao::deleteProjectTags($project->getId());
        $this->assertEquals("0", $tryRedelete);  
    }
    
    public function testRecordProjectFileInfo()
    {
        UnitTestHelper::teardownDb();        
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());        
    
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        // Success
        $resultRecordProjectFileInfo = ProjectDao::recordProjectFileInfo($insertedProject->getId(), "saveProjectFileTest.txt", $insertedUser->getId(), "text/plain");
        $this->assertNotNull($resultRecordProjectFileInfo);
        
        // Failure
        $resultRecordProjectFileInfoFailure = ProjectDao::recordProjectFileInfo($insertedProject->getId(), "saveProjectFileTest.txt", $insertedUser->getId(), "text/plain");
        $this->assertNull($resultRecordProjectFileInfoFailure);
    }    
    
    public function testGetProjectFileInfo()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());        
   
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        $resultRecordProjectFileInfo = ProjectDao::recordProjectFileInfo($insertedProject->getId(), "saveProjectFileTest.txt", $insertedUser->getId(), "text/plain");
        $this->assertNotNull($resultRecordProjectFileInfo);
        
        // Success
        $resultGetProjectFileInfoSuccess = ProjectDao::getProjectFileInfo($insertedProject->getId(), $insertedUser->getId(), "saveProjectFileTest.txt", "saveProjectFileTest.txt", "text/plain");
        $this->assertInstanceOf("ProjectFile", $resultGetProjectFileInfoSuccess);
        
        // Failure
        $resultGetProjectFileInfoFailure = ProjectDao::getProjectFileInfo(999, $insertedUser->getId(), "saveProjectFileTest.txt", "saveProjectFileTest.txt", "text/plain");
        $this->assertNull($resultGetProjectFileInfoFailure);
    }
}

?>
