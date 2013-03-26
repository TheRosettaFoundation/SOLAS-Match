<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TaskDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../../Common/TaskTypeEnum.php';
require_once __DIR__.'/../UnitTestHelper.php';

class TaskDaoTest extends PHPUnit_Framework_TestCase
{
    public function testCreateTask()
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
        
        // Success
        $insertedTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $insertedTask);
        $this->assertNotNull($insertedTask->getId());
        
        // Failure - Duplicate Task
        $insertedTask2 = TaskDao::create($task);
        $this->assertNull($insertedTask2);
        
    }
    
    public function testUpdateTask()
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

        $insertedTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $insertedTask);
        $this->assertNotNull($insertedTask->getId());
        
        $task->setId($insertedTask->getId());
        $task->setTitle("Updated Title");
        $task->setComment("Updated Comment");
        $task->setWordCount(334455);
        $task->setDeadline("2025-03-29 19:25:12");
        $task->setSourceCountryCode("DE");
        $task->setSourceLanguageCode("de");        
        $task->setTargetCountryCode("ES");
        $task->setTargetLanguageCode("es");
        $task->setPublished(0);
        
        $i = 0;
        foreach($insertedProject->getTag() as $tag) {
            $task->setTag($tag, $i);
            $i++;
        }
        $task->setTaskStatus(3);
        $task->setTaskType(3);        
        $task->setCreatedTime("2030-07-14 12:24:02");
        
        // Success
        $updatedTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $updatedTask);
        $this->assertEquals($insertedTask->getId(), $task->getId());
        $this->assertEquals($task->getTitle(), $updatedTask->getTitle());
        $this->assertEquals($task->getComment(), $updatedTask->getComment());
        $this->assertEquals($task->getWordCount(), $updatedTask->getWordCount());
        $this->assertEquals($task->getDeadline(), $updatedTask->getDeadline());
        $this->assertEquals($task->getSourceCountryCode(), $updatedTask->getSourceCountryCode());
        $this->assertEquals($task->getSourceLanguageCode(), $updatedTask->getSourceLanguageCode());
        $this->assertEquals($task->getTargetCountryCode(), $updatedTask->getTargetCountryCode());
        $this->assertEquals($task->getTargetLanguageCode(), $updatedTask->getTargetLanguageCode());
        $this->assertEquals($task->getPublished(), $updatedTask->getPublished());

    }
    
    public function testGetTask()
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

        $insertedTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $insertedTask);
        $this->assertNotNull($insertedTask->getId());
        
        // Success
        $retrievedTask = TaskDao::getTask(array(
            "id"                    => $task->getId(),
            "project_id"            => $task->getProjectId(),
            "title"                 => $task->getTitle(),
            "word-count"            => $task->getWordCount(),
            "language_id-source"    => $task->getSourceLanguageCode(),
            "language_id-target"    => $task->getTargetLanguageCode(),
            "created-time"          => $task->getCreatedTime(),
            "country_id-source"     => $task->getSourceCountryCode(),
            "country_id-target"     => $task->getTargetCountryCode(),
            "comment"               => $task->getComment(),
            "taskType_id"           => $task->getTaskType(),
            "taskStatus_id"         => $task->getTaskStatus(),
            "published"             => $task->getPublished(),
            "deadline"              => $task->getDeadline()
        ));
        
        $this->assertInstanceOf("Task", $retrievedTask[0]);
        $this->assertEquals($insertedTask->getId(), $retrievedTask[0]->getId());
        $this->assertEquals($task->getTitle(), $retrievedTask[0]->getTitle());
        $this->assertEquals($task->getComment(), $retrievedTask[0]->getComment());
        $this->assertEquals($task->getWordCount(), $retrievedTask[0]->getWordCount());
        $this->assertEquals($task->getDeadline(), $retrievedTask[0]->getDeadline());
        $this->assertEquals($task->getSourceCountryCode(), $retrievedTask[0]->getSourceCountryCode());
        $this->assertEquals($task->getSourceLanguageCode(), $retrievedTask[0]->getSourceLanguageCode());
        $this->assertEquals($task->getTargetCountryCode(), $retrievedTask[0]->getTargetCountryCode());
        $this->assertEquals($task->getTargetLanguageCode(), $retrievedTask[0]->getTargetLanguageCode());
        $this->assertEquals($task->getPublished(), $retrievedTask[0]->getPublished());       
        
        
        // Failure
        $nonExistantTask = TaskDao::getTask(array(
            "id" => 999
        ));
        $this->assertNull($nonExistantTask);
        
    }
    
    
    public function testDeleteTask()
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

        $insertedTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $insertedTask);
        $this->assertNotNull($insertedTask->getId());
        
        // Success
        $deletedTask = TaskDao::delete($insertedTask->getId());
        $this->assertEquals("1", $deletedTask);
        
        // Failure
        $deleteNonExistantTask = TaskDao::delete(999);
        $this->assertEquals("0", $deleteNonExistantTask);
    }
    
    public function testAddTaskPreReq()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 1", "Task 1 Comment", "2022-03-29 16:30:00", 11111, null, TaskTypeEnum::TRANSLATION);
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2", "Task 2 Comment", "2021-03-29 16:30:00", 22222, null, TaskTypeEnum::PROOFREADING);        

        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask);
        
        $proofReadingTask = TaskDao::create($task2);
        $this->assertInstanceOf("Task", $proofReadingTask);
        
        // Success
        $addTaskPreReq = TaskDao::addTaskPreReq($proofReadingTask->getId(), $translationTask->getId());      
        $this->assertEquals("1", $addTaskPreReq);
        
        // Failure
        $addTaskPreReqDuplicate = TaskDao::addTaskPreReq($proofReadingTask->getId(), $translationTask->getId());      
        $this->assertEquals("0", $addTaskPreReqDuplicate);
    }
    
    public function testRemoveTaskPreReq()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 1", "Task 1 Comment", "2022-03-29 16:30:00", 11111, null, TaskTypeEnum::TRANSLATION);
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2", "Task 2 Comment", "2021-03-29 16:30:00", 22222, null, TaskTypeEnum::PROOFREADING);        

        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask);
        
        $proofReadingTask = TaskDao::create($task2);
        $this->assertInstanceOf("Task", $proofReadingTask);
        
        $addTaskPreReq = TaskDao::addTaskPreReq($proofReadingTask->getId(), $translationTask->getId());      
        $this->assertEquals("1", $addTaskPreReq);
        
        // Success
        $removeTaskPreReq = TaskDao::removeTaskPreReq($proofReadingTask->getId(), $translationTask->getId());      
        $this->assertEquals("1", $removeTaskPreReq);   
        
        // Failure
        $removeTaskPreReqDuplicate = TaskDao::removeTaskPreReq($proofReadingTask->getId(), $translationTask->getId());      
        $this->assertEquals("0", $removeTaskPreReqDuplicate);  
        
    }
    
    public function testGetTaskPreReqs()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        // Failure
        $taskPreReqsFailure = TaskDao::getTaskPreReqs(999);
        $this->assertNull($taskPreReqsFailure);
        
        $task = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 1", "Task 1 Comment", "2021-03-29 16:30:00", 11111, null, TaskTypeEnum::TRANSLATION);
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2", "Task 2 Comment", "2022-03-29 16:30:00", 22222, null, TaskTypeEnum::PROOFREADING);    
        $task3 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 3", "Task 3 Comment", "2023-03-29 16:30:00", 33333, null, TaskTypeEnum::TRANSLATION);

        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask); 
        
        $proofReadingTask = TaskDao::create($task2);
        $this->assertInstanceOf("Task", $proofReadingTask);
        $this->assertNotNull($proofReadingTask->getId());
        
        $translationTask2 = TaskDao::create($task3);        
        $this->assertInstanceOf("Task", $translationTask2);
        
        $addTaskPreReq = TaskDao::addTaskPreReq($proofReadingTask->getId(), $translationTask->getId());      
        $this->assertEquals("1", $addTaskPreReq);
        $addTaskPreReq2 = TaskDao::addTaskPreReq($proofReadingTask->getId(), $translationTask2->getId());      
        $this->assertEquals("1", $addTaskPreReq2);
        
        // Success
        $taskPreReqs = TaskDao::getTaskPreReqs($proofReadingTask->getId());
        $this->assertCount(2, $taskPreReqs);
        foreach($taskPreReqs as $task) {
            $this->assertInstanceOf("Task", $task);
        }
        
    }
    
    public function testGetLatestAvailableTasks()
    {
        UnitTestHelper::teardownDb();        
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        // Failure
        $emptylatestTasks = TaskDao::getLatestAvailableTasks();
        $this->assertNull($emptylatestTasks);
        
        $task = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 1", "Task 1 Comment", "2021-03-29 16:30:00", 11111, null, TaskTypeEnum::TRANSLATION);
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2", "Task 2 Comment", "2022-03-29 16:30:00", 22222, null, TaskTypeEnum::TRANSLATION);
        
        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask); 
        
        $translationTask2 = TaskDao::create($task2);
        $this->assertInstanceOf("Task", $translationTask2);
        
        // Success
        $latestTasks = TaskDao::getLatestAvailableTasks();
        $this->assertCount(2, $latestTasks);
        foreach($latestTasks as $task) {
            $this->assertInstanceOf("Task", $task);
        }
    }
    
    public function testGetUserTopTasks()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 1", "Task 1 Comment", "2021-03-29 16:30:00", 11111, null, TaskTypeEnum::TRANSLATION);
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2", "Task 2 Comment", "2022-03-29 16:30:00", 22222, null, TaskTypeEnum::TRANSLATION);
        
        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask); 
        
        $translationTask2 = TaskDao::create($task2);
        $this->assertInstanceOf("Task", $translationTask2);
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        
        // Success
        $userTopTasks = TaskDao::getUserTopTasks($insertedUser->getUserId(), 30);
        $this->assertCount(2, $userTopTasks);
        foreach($userTopTasks as $task) {
            $this->assertInstanceOf("Task", $task);
        }
    }
    
}
?>
