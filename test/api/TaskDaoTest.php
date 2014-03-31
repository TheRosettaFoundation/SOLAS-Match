<?php

namespace SolasMatch\Tests\API;

use \SolasMatch\Tests\UnitTestHelper;
use \SolasMatch\Common as Common;
use \SolasMatch\API as API;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TaskDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../Common/Enums/BadgeTypes.class.php';
require_once __DIR__.'/../../Common/Enums/TaskTypeEnum.class.php';
require_once __DIR__.'/../UnitTestHelper.php';

class TaskDaoTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        // Success
        $insertedTask = API\DAO\TaskDao::save($task);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $insertedTask);
        $this->assertNotNull($insertedTask->getId());
    }
    
    public function testUpdateTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $insertedTask);
        
        $task->setId($insertedTask->getId());
        $task->setTitle("Updated Title");
        $task->setComment("Updated Comment");
        $task->setWordCount(334455);
        $task->setDeadline("2025-03-29 19:25:12");
        
        $sourceLocale = new Common\Protobufs\Models\Locale();
        $sourceLocale->setLanguageCode("de");
        $sourceLocale->setCountryCode("DE");
        $task->setSourceLocale($sourceLocale);
                
        $targetLocale = new Common\Protobufs\Models\Locale();
        $targetLocale->setLanguageCode("es");
        $targetLocale->setCountryCode("ES");
        $task->setTargetLocale($targetLocale);
        
        $task->setPublished(0);
        $task->setTaskStatus(3);
        $task->setTaskType(3);
        $task->setCreatedTime("2030-07-14 12:24:02");
        
        // Success
        $updatedTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($updatedTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $updatedTask);
        $this->assertEquals($insertedTask->getId(), $task->getId());
        $this->assertEquals($task->getTitle(), $updatedTask->getTitle());
        $this->assertEquals($task->getComment(), $updatedTask->getComment());
        $this->assertEquals($task->getWordCount(), $updatedTask->getWordCount());
        $this->assertEquals($task->getDeadline(), $updatedTask->getDeadline());
        
        $this->assertEquals(
            $task->getSourceLocale()->getLanguageCode(),
            $updatedTask->getSourceLocale()->getLanguageCode()
        );
        $this->assertEquals(
            $task->getSourceLocale()->getCountryCode(),
            $updatedTask->getSourceLocale()->getCountryCode()
        );
        $this->assertEquals(
            $task->getTargetLocale()->getLanguageCode(),
            $updatedTask->getTargetLocale()->getLanguageCode()
        );
        $this->assertEquals(
            $task->getTargetLocale()->getCountryCode(),
            $updatedTask->getTargetLocale()->getCountryCode()
        );
        $this->assertEquals($task->getPublished(), $updatedTask->getPublished());
    }
    
    public function testGetTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $insertedTask);
        
        // Success
        $retrievedTask = API\DAO\TaskDao::getTask(
            $task->getId(),
            $task->getProjectId(),
            $task->getTitle(),
            $task->getWordCount(),
            $task->getSourceLocale()->getLanguageCode(),
            $task->getTargetLocale()->getLanguageCode(),
            $task->getCreatedTime(),
            $task->getSourceLocale()->getCountryCode(),
            $task->getTargetLocale()->getCountryCode(),
            $task->getComment(),
            $task->getTaskType(),
            $task->getTaskStatus(),
            $task->getPublished(),
            $task->getDeadline()
        );

        $this->assertNotNull($retrievedTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $retrievedTask);
        $this->assertEquals($insertedTask->getId(), $retrievedTask->getId());
        $this->assertEquals($task->getTitle(), $retrievedTask->getTitle());
        $this->assertEquals($task->getComment(), $retrievedTask->getComment());
        $this->assertEquals($task->getWordCount(), $retrievedTask->getWordCount());
        $this->assertEquals($task->getDeadline(), $retrievedTask->getDeadline());
        
        $this->assertEquals(
            $task->getSourceLocale()->getLanguageCode(),
            $retrievedTask->getSourceLocale()->getLanguageCode()
        );
        $this->assertEquals(
            $task->getSourceLocale()->getCountryCode(),
            $retrievedTask->getSourceLocale()->getCountryCode()
        );
        $this->assertEquals(
            $task->getTargetLocale()->getLanguageCode(),
            $retrievedTask->getTargetLocale()->getLanguageCode()
        );
        $this->assertEquals(
            $task->getTargetLocale()->getCountryCode(),
            $retrievedTask->getTargetLocale()->getCountryCode()
        );
        
        $this->assertEquals($task->getPublished(), $retrievedTask->getPublished());
        
        // Failure
        $nonExistantTask = API\DAO\TaskDao::getTask(999);
        $this->assertNull($nonExistantTask);
    }
    
    public function testGetTasks()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 1",
            "Task 1 Comment",
            "2022-03-29 16:30:00",
            11111,
            null,
            Common\Enums\TaskTypeEnum::TRANSLATION
        );
        $task2 = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 2",
            "Task 2 Comment",
            "2021-03-29 16:30:00",
            22222,
            null,
            Common\Enums\TaskTypeEnum::PROOFREADING
        );

        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        $proofReadingTask = API\DAO\TaskDao::save($task2);
        $this->assertNotNull($proofReadingTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $proofReadingTask);
        
        $getTasks = API\DAO\TaskDao::getTasks();
        $this->assertCount(2, $getTasks);
        foreach ($getTasks as $projTask) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $projTask);
        }
    }
    
    public function testDeleteTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $insertedTask);
        
        // Success
        $deletedTask = API\DAO\TaskDao::delete($insertedTask->getId());
        $this->assertEquals("1", $deletedTask);
        
        // Failure
        $deleteNonExistantTask = API\DAO\TaskDao::delete(999);
        $this->assertEquals("0", $deleteNonExistantTask);
    }
    
    public function testAddTaskPreReq()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 1",
            "Task 1 Comment",
            "2022-03-29 16:30:00",
            11111,
            null,
            Common\Enums\TaskTypeEnum::TRANSLATION
        );
        $task2 = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 2",
            "Task 2 Comment",
            "2021-03-29 16:30:00",
            22222,
            null,
            Common\Enums\TaskTypeEnum::PROOFREADING
        );

        //was using missing create function, changed to save
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        $proofReadingTask = API\DAO\TaskDao::save($task2);
        $this->assertNotNull($proofReadingTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $proofReadingTask);
        
        // Success
        $addTaskPreReq = API\DAO\TaskDao::addTaskPreReq($proofReadingTask->getId(), $translationTask->getId());
        $this->assertEquals("1", $addTaskPreReq);
        
        // Failure
        $addTaskPreReqDuplicate = API\DAO\TaskDao::addTaskPreReq(
            $proofReadingTask->getId(),
            $translationTask->getId()
        );
        $this->assertEquals("0", $addTaskPreReqDuplicate);
    }
    
    public function testRemoveTaskPreReq()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 1",
            "Task 1 Comment",
            "2022-03-29 16:30:00",
            11111,
            null,
            Common\Enums\TaskTypeEnum::TRANSLATION
        );
        $task2 = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 2",
            "Task 2 Comment",
            "2021-03-29 16:30:00",
            22222,
            null,
            Common\Enums\TaskTypeEnum::PROOFREADING
        );

        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        $proofReadingTask = API\DAO\TaskDao::save($task2);
        $this->assertNotNull($proofReadingTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $proofReadingTask);
        
        $addTaskPreReq = API\DAO\TaskDao::addTaskPreReq($proofReadingTask->getId(), $translationTask->getId());
        $this->assertEquals("1", $addTaskPreReq);
        
        // Success
        $removeTaskPreReq = API\DAO\TaskDao::removeTaskPreReq($proofReadingTask->getId(), $translationTask->getId());
        $this->assertEquals("1", $removeTaskPreReq);
        
        // Failure
        $removeTaskPreReqDuplicate = API\DAO\TaskDao::removeTaskPreReq(
            $proofReadingTask->getId(),
            $translationTask->getId()
        );
        $this->assertEquals("0", $removeTaskPreReqDuplicate);
    }
    
    public function testGetTaskPreReqs()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        // Failure
        $taskPreReqsFailure = API\DAO\TaskDao::getTaskPreReqs(999);
        $this->assertNull($taskPreReqsFailure);
        
        $task = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 1",
            "Task 1 Comment",
            "2021-03-29 16:30:00",
            11111,
            null,
            Common\Enums\TaskTypeEnum::TRANSLATION
        );
        $task2 = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 2",
            "Task 2 Comment",
            "2022-03-29 16:30:00",
            22222,
            null,
            Common\Enums\TaskTypeEnum::PROOFREADING
        );
        $task3 = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 3",
            "Task 3 Comment",
            "2023-03-29 16:30:00",
            33333,
            null,
            Common\Enums\TaskTypeEnum::TRANSLATION
        );

        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        $proofReadingTask = API\DAO\TaskDao::save($task2);
        $this->assertNotNull($proofReadingTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $proofReadingTask);
        
        $translationTask2 = API\DAO\TaskDao::save($task3);
        $this->assertNotNull($translationTask2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask2);
        
        $addTaskPreReq = API\DAO\TaskDao::addTaskPreReq($proofReadingTask->getId(), $translationTask->getId());
        $this->assertEquals("1", $addTaskPreReq);
        $addTaskPreReq2 = API\DAO\TaskDao::addTaskPreReq($proofReadingTask->getId(), $translationTask2->getId());
        $this->assertEquals("1", $addTaskPreReq2);
        
        // Success
        $taskPreReqs = API\DAO\TaskDao::getTaskPreReqs($proofReadingTask->getId());
        $this->assertCount(2, $taskPreReqs);
        foreach ($taskPreReqs as $task) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $task);
        }
    }
    
    public function testGetLatestAvailableTasks()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        // Failure
        $emptylatestTasks = API\DAO\TaskDao::getLatestAvailableTasks();
        $this->assertNull($emptylatestTasks);
        
        $task = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 1",
            "Task 1 Comment",
            "2021-03-29 16:30:00",
            11111,
            null,
            Common\Enums\TaskTypeEnum::TRANSLATION
        );
        $task2 = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 2",
            "Task 2 Comment",
            "2022-03-29 16:30:00",
            22222,
            null,
            Common\Enums\TaskTypeEnum::TRANSLATION
        );
        
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        $translationTask2 = API\DAO\TaskDao::save($task2);
        $this->assertNotNull($translationTask2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask2);
        
        // Success
        $latestTasks = API\DAO\TaskDao::getLatestAvailableTasks();
        $this->assertCount(2, $latestTasks);
        foreach ($latestTasks as $task) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $task);
        }
    }
    
    public function testGetUserTopTasks()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 1",
            "Task 1 Comment",
            "2021-03-29 16:30:00",
            11111,
            null,
            Common\Enums\TaskTypeEnum::TRANSLATION
        );
        $task2 = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 2",
            "Task 2 Comment",
            "2022-03-29 16:30:00",
            22222,
            null,
            Common\Enums\TaskTypeEnum::TRANSLATION
        );
        
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        $translationTask2 = API\DAO\TaskDao::save($task2);
        $this->assertNotNull($translationTask2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask2);
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        // Success
        $userTopTasks = API\DAO\TaskDao::getUserTopTasks(
            $insertedUser->getId(),
            false,
            30,
            0,
            Common\Enums\TaskTypeEnum::TRANSLATION,
            null,
            null
        );
        $this->assertCount(2, $userTopTasks);
        foreach ($userTopTasks as $task) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $task);
        }
    }
    
    public function testArchiveTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
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

        // Failure
        $archiveTaskFailure = API\DAO\TaskDao::moveToArchiveById($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("0", $archiveTaskFailure);
    }
    
    public function testClaimTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        // Success
        $claimTask = API\DAO\TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask);
        
        // Failure
        $claimTaskFailure = API\DAO\TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("0", $claimTaskFailure);
    }
    
    public function testUnClaimTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);

        $claimTask = API\DAO\TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask);
        
        // Success
        $unClaimTask = API\DAO\TaskDao::unClaimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $unClaimTask);
        
        // Success
        $unClaimTaskFailure = API\DAO\TaskDao::unClaimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("0", $unClaimTaskFailure);
    }
    
    public function hasUserClaimedTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        // Failure
        $hasUserClaimedTaskFailure = API\DAO\TaskDao::hasUserClaimedTask(
            $insertedUser->getId(),
            $translationTask->getId()
        );
        $this->assertEquals("0", $hasUserClaimedTaskFailure);

        $claimTask = API\DAO\TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask);
        
        // Success
        $hasUserClaimedTask = API\DAO\TaskDao::hasUserClaimedTask($insertedUser->getId(), $translationTask->getId());
        $this->assertEquals("1", $hasUserClaimedTask);
    }
    
    public function testTaskIsClaimed()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        // Failure
        $taskIsNotClaimed = API\DAO\TaskDao::taskIsClaimed($translationTask->getId());
        $this->assertEquals("0", $taskIsNotClaimed);

        $claimTask = API\DAO\TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask);
        
        // Success
        $taskIsClaimed = API\DAO\TaskDao::taskIsClaimed($translationTask->getId());
        $this->assertEquals("1", $taskIsClaimed);
    }
    
    public function testGetUserClaimedTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        // Failure
        $noTaskTranslator = API\DAO\TaskDao::getUserClaimedTask($translationTask->getId());
        $this->assertNull($noTaskTranslator);

        $claimTask = API\DAO\TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask);
        
        // Success
        $taskTranslator = API\DAO\TaskDao::getUserClaimedTask($translationTask->getId());
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $taskTranslator);
        $this->assertNotNull($taskTranslator->getId());
        $this->assertEquals($insertedUser->getId(), $taskTranslator->getId());
    }
    
    public function testHasUserClaimedSegmentationTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $task->setTaskType(Common\Enums\TaskTypeEnum::SEGMENTATION);
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $claimTask = API\DAO\TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask);
        $hasClaimedSeg = API\DAO\TaskDao::hasUserClaimedSegmentationTask(
            $insertedUser->getId(),
            $insertedProject->getId()
        );
        $this->assertEquals("1", $hasClaimedSeg);
    }
    
    public function testGetClaimedTime()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $claimTask = API\DAO\TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $theTime = time();
        $this->assertEquals("1", $claimTask);
        
        $getClaimedTime = API\DAO\TaskDao::getClaimedTime($translationTask->getId());
        //assert the times are equal, give or take 2 seconds
        //                  Expected    Actual               Failure message    Allowed difference
        $this->assertEquals($theTime, strtotime($getClaimedTime), "FAILUUUURRRE", 2);
    }
    
    public function testGetUserTasks()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        // Failure
        $userTasksFailure = API\DAO\TaskDao::getUserTasks($insertedUser->getId());
        $this->assertNull($userTasksFailure);
        
        $task = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 1",
            "Task 1 Comment",
            "2021-03-29 16:30:00",
            11111,
            null,
            Common\Enums\TaskTypeEnum::TRANSLATION
        );
        $task2 = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 2",
            "Task 2 Comment",
            "2022-03-29 16:30:00",
            22222,
            null,
            Common\Enums\TaskTypeEnum::TRANSLATION
        );
        
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        $translationTask2 = API\DAO\TaskDao::save($task2);
        $this->assertNotNull($translationTask2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask2);
        
        $claimTask = API\DAO\TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask);
        
        $claimTask2 = API\DAO\TaskDao::claimTask($translationTask2->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask2);
        
        // Success
        $userTasks = API\DAO\TaskDao::getUserTasks($insertedUser->getId());
        $this->assertCount(2, $userTasks);
        foreach ($userTasks as $task) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $task);
        }
    }
    
    public function testGetUserTasksCount()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        // Failure
        $userTasksFailure = API\DAO\TaskDao::getUserTasks($insertedUser->getId());
        $this->assertNull($userTasksFailure);
        
        $task = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 1",
            "Task 1 Comment",
            "2021-03-29 16:30:00",
            11111,
            null,
            Common\Enums\TaskTypeEnum::TRANSLATION
        );
        $task2 = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 2",
            "Task 2 Comment",
            "2022-03-29 16:30:00",
            22222,
            null,
            Common\Enums\TaskTypeEnum::TRANSLATION
        );
        
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        $translationTask2 = API\DAO\TaskDao::save($task2);
        $this->assertNotNull($translationTask2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask2);
        
        $claimTask = API\DAO\TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask);
        
        $claimTask2 = API\DAO\TaskDao::claimTask($translationTask2->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask2);
        
        $userTaskCount = API\DAO\TaskDao::getUserTasksCount($insertedUser->getId());
        $this->assertEquals("2", $userTaskCount);
    }
    
    public function testGetUserArchivedTasks()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        // Failure
        $userArchivedTasksFailure = API\DAO\TaskDao::getUserArchivedTasks($insertedUser->getId());
        $this->assertNull($userArchivedTasksFailure);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
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
        
        $claimTask = API\DAO\TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask);
        
        $archivedTask = API\DAO\TaskDao::moveToArchiveById($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $archivedTask);
        
        // Success
        $userArchivedTasks = API\DAO\TaskDao::getUserArchivedTasks($insertedUser->getId());
        $archivedTask = $userArchivedTasks[0];
        $this->assertCount(1, $userArchivedTasks);
        $this->assertNotNull($archivedTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\ArchivedTask", $archivedTask);
        $this->assertEquals($translationTask->getProjectId(), $archivedTask->getProjectId());
        $this->assertEquals($translationTask->getTitle(), $archivedTask->getTitle());
        $this->assertEquals($translationTask->getComment(), $archivedTask->getComment());
        $this->assertEquals($translationTask->getDeadline(), $archivedTask->getDeadline());
        $this->assertEquals(
            $translationTask->getSourceLocale()->getLanguageCode(),
            $archivedTask->getSourceLocale()->getLanguageCode()
        );
        $this->assertEquals(
            $translationTask->getSourceLocale()->getCountryCode(),
            $archivedTask->getSourceLocale()->getCountryCode()
        );
        $this->assertEquals(
            $translationTask->getTargetLocale()->getLanguageCode(),
            $archivedTask->getTargetLocale()->getLanguageCode()
        );
        $this->assertEquals(
            $translationTask->getTargetLocale()->getCountryCode(),
            $archivedTask->getTargetLocale()->getCountryCode()
        );
        $this->assertEquals($translationTask->getTaskType(), $archivedTask->getTaskType());
        $this->assertEquals(3, $archivedTask->getTaskStatus()); // Claimed the task, so status changes
        $this->assertEquals($translationTask->getPublished(), $archivedTask->getPublished());
        $this->assertNotNull($archivedTask->getArchivedDate());
    }
    
    public function testGetTasksWithTag()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $getTasksWithTagFailure = API\DAO\TaskDao::getTasksWithTag(999);
        $this->assertNull($getTasksWithTagFailure);
        
        $task = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 1",
            "Task 1 Comment",
            "2022-03-29 16:30:00",
            11111,
            null,
            Common\Enums\TaskTypeEnum::TRANSLATION
        );
        $task2 = UnitTestHelper::createTask(
            $insertedProject->getId(),
            null,
            "Task 2",
            "Task 2 Comment",
            "2021-03-29 16:30:00",
            22222,
            null,
            Common\Enums\TaskTypeEnum::TRANSLATION
        );
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        $translationTask2 = API\DAO\TaskDao::save($task2);
        $this->assertNotNull($translationTask2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask2);
        
        $tag = API\DAO\TagsDao::getTag(null, "Tags");
        
        // Success
        $getTasksWithTag = API\DAO\TaskDao::getTasksWithTag($tag->getId());
        $this->assertCount(2, $getTasksWithTag);
        foreach ($getTasksWithTag as $task) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $task);
        }
    }
    
    public function testCheckTaskFileVersion()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        // Success
        $checkTaskFileVersion = API\DAO\TaskDao::checkTaskFileVersion($translationTask->getId());
        $this->assertEquals(false, $checkTaskFileVersion);
    }
    
    public function testRecordFileUpload()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        // Success
        $recordFileUpload = API\DAO\TaskDao::recordFileUpload(
            $translationTask->getId(),
            "examplefile",
            "text/plain",
            $insertedUser->getId()
        );
        $this->assertNotNull($recordFileUpload);
    }
    
    public function testGetLatestFileVersion()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $translationTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($translationTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $translationTask);
        
        // Success
        $latestFileVersion = API\DAO\TaskDao::getLatestFileVersion($translationTask->getId());
        $this->assertEquals(0, $latestFileVersion);
    }
    
    public function testGetSubscribedUsers()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        $insertedUser2 = API\DAO\UserDao::create("bestuser@example.com", "testpw");
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser2);
        $this->assertNotNull($insertedUser2->getId());
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $insertedTask);
        
        $trackTask = API\DAO\UserDao::trackTask($insertedUser->getId(), $insertedTask->getId());
        $this->assertEquals("1", $trackTask);
        $trackTask2 = API\DAO\UserDao::trackTask($insertedUser2->getId(), $insertedTask->getId());
        $this->assertEquals("1", $trackTask2);
        
        $subbedUsers = API\DAO\TaskDao::getSubscribedUsers($task->getId());
        $this->assertCount(2, $subbedUsers);
        
        foreach ($subbedUsers as $sub) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $sub);
        }
        $this->assertEquals($insertedUser->getId(), $subbedUsers[0]->getId());
        $this->assertEquals($insertedUser2->getId(), $subbedUsers[1]->getId());
    }
}