<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TaskDao.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
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
        $this->assertEquals($insertedTask->getId(), $updatedTask->getId());
    }
    
}
?>
