<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TaskDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/StatDao.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../UnitTestHelper.php';

class StatDaoTest extends PHPUnit_Framework_TestCase
{
    public function testGetTotalTasks()
    {
        UnitTestHelper::teardownDb();
        
        $orgDao = new OrganisationDao();
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        
        $projectDao = new ProjectDao();
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = $projectDao->createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);    
        $this->assertNotNull($project->getId()); 
        
        $taskDao = new TaskDao();
        $task = UnitTestHelper::createTask($project->getId());
        $task2 = UnitTestHelper::createTask($project->getId(), null, "Task 2", "Task 2 Comment");
        
        $insertedTask = $taskDao->create($task);
        $this->assertInstanceOf("Task", $insertedTask);
        sleep(3); // Sleep for 3 seconds so the two created tasks have a different created time
        $insertedTask2 = $taskDao->create($task2);
        $this->assertInstanceOf("Task", $insertedTask2);
        
        // Success - 2 Total Tasks
        $statDao = new StatDao();
        $resultTotalTasks = $statDao->getTotalTasks(null);
        $this->assertEquals(2, $resultTotalTasks);
        
        // Success - 1 Total Task
        $resultTotalTasksByCreatedTime = $statDao->getTotalTasks($insertedTask2->getCreatedTime());
        $this->assertEquals(1, $resultTotalTasksByCreatedTime);
        
        // Success - 0 Total Tasks
        UnitTestHelper::teardownDb();
        $resultZeroTotalTasks = $statDao->getTotalTasks(null);
        $this->assertEquals(0, $resultZeroTotalTasks);

    }
    
    
    public function testGetTotalArchivedTasks()
    {
        UnitTestHelper::teardownDb();
        
    }
    /*
    public function testGetClaimedTasks()
    {
        UnitTestHelper::teardownDb();
    }
    
    public function testGetTotalOrgs()
    {
        UnitTestHelper::teardownDb();
    }
    
    public function testGetTotalUsers()
    {
        UnitTestHelper::teardownDb();
    }
    
    public function testGetUnclaimedTasks()
    {
        UnitTestHelper::teardownDb();
    }
    
    public function testGetStatistics()
    {
        UnitTestHelper::teardownDb();
    }
     * */
     
    
}

?>
