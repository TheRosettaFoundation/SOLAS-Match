<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TaskDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/StatDao.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
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
        sleep(1); // Sleep for 1 second so the two created tasks have a guaranteed different created time
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
        
        // Success - 0 Total Tasks
        $statDao = new StatDao();
        $resultZeroTotalArchivedTasks = $statDao->getTotalArchivedTasks(null);
        $this->assertEquals(0, $resultZeroTotalArchivedTasks);
        
        $userDao = new UserDao();       
        $user = UnitTestHelper::createUser();
        $insertedUser = $userDao->save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        
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
        sleep(1); // Sleep for 1 second so the two created tasks have a guaranteed different created time
        $insertedTask2 = $taskDao->create($task2);
        $this->assertInstanceOf("Task", $insertedTask2);    
        
        $archivedTask = $taskDao->archiveTask($insertedTask->getId(), $insertedUser->getUserId());
        $this->assertEquals(1, $archivedTask);
        sleep(1); // Sleep for 1 second so the two created tasks have a guaranteed different archived time
        $archivedTask2 = $taskDao->archiveTask($insertedTask2->getId(), $insertedUser->getUserId());
        $this->assertEquals(1, $archivedTask2);
        
        // Success 2 Total Archived Tasks
        $resultGetTotalArchivedTasks = $statDao->getTotalArchivedTasks(null);
        $this->assertEquals(2, $resultGetTotalArchivedTasks);
        
        // Success - 1 Total Archived Task
        $resultTotalArchivedTasksByTime = $statDao->getTotalArchivedTasks($insertedTask2->getCreatedTime());
        $this->assertEquals(1, $resultTotalArchivedTasksByTime);
    }
    
    public function testGetClaimedTasks()
    {
        UnitTestHelper::teardownDb();
        
        // Success - 0 Total Claimed Tasks
        $statDao = new StatDao();
        $resultZeroTotalClaimedTasks = $statDao->getTotalClaimedTasks(null);
        $this->assertEquals(0, $resultZeroTotalClaimedTasks);
        
        $userDao = new UserDao();       
        $user = UnitTestHelper::createUser();
        $insertedUser = $userDao->save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        
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
        sleep(1); // Sleep for 1 second so the two created tasks have a guaranteed different created time
        $insertedTask2 = $taskDao->create($task2);
        $this->assertInstanceOf("Task", $insertedTask2);    
        
        $claimedTask = $taskDao->claimTask($insertedTask->getId(), $insertedUser->getUserId());
        $this->assertEquals(1, $claimedTask);
        sleep(1); // Sleep for 1 second so the two created tasks have a guaranteed different claim time
        $claimedTask2 = $taskDao->claimTask($insertedTask2->getId(), $insertedUser->getUserId());
        $this->assertEquals(1, $claimedTask2);
        
        // Success 2 Total Claimed Tasks
        $resultGetAllClaimedTasks = $statDao->getTotalClaimedTasks(null);
        $this->assertEquals(2, $resultGetAllClaimedTasks);
        
        // Currently no way to get the claimed-time of a task.
        // Success - 1 Total Claimed Task
        //$resultTotalClaimedTasksByTime = $statDao->getTotalClaimedTasks($claimedTask2->getCreatedTime());
        //$this->assertEquals(1, $resultTotalClaimedTasksByTime);        
    }
    
    public function testGetTotalOrgs()
    {
        UnitTestHelper::teardownDb();        
        
        // Success - 0 Total Orgs
        $statDao = new StatDao();
        $resultZeroTotalOrgs = $statDao->getTotalOrgs();
        $this->assertEquals(0, $resultZeroTotalOrgs);
        
        $orgDao = new OrganisationDao();
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        
        $org2 = UnitTestHelper::createOrg(null, "Organisation2", "Organisation 2 Bio");
        $insertedOrg2 = $orgDao->insertAndUpdate($org2);
        $this->assertInstanceOf("Organisation", $insertedOrg2);
        $this->assertNotNull($insertedOrg2->getId());
        
        // Success - 2 Total Orgs
        $resultTotalOrgs = $statDao->getTotalOrgs();
        $this->assertEquals("2", $resultTotalOrgs);        
    }
    
    public function testGetTotalUsers()
    {
        UnitTestHelper::teardownDb();        
        
        // Success - 0 Total Users
        $statDao = new StatDao();
        $resultZeroTotalUsers = $statDao->getTotalUsers();
        $this->assertEquals(0, $resultZeroTotalUsers);
        
        $userDao = new UserDao();       
        $user = UnitTestHelper::createUser();
        $insertedUser = $userDao->save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        
        $user2 = UnitTestHelper::createUser(null, "User 2", "User 2 Bio", "user2@test.com");
        $insertedUser2 = $userDao->save($user2);
        $this->assertInstanceOf("User", $insertedUser2);
        $this->assertNotNull($insertedUser2->getUserId());
        
        // Success - 2 Total Users
        $resultTotalUsers = $statDao->getTotalUsers();
        $this->assertEquals("2", $resultTotalUsers);
    }
    
    public function testGetUnclaimedTasks()
    {
        UnitTestHelper::teardownDb();
        
        // Success - 0 Total Unclaimed Tasks
        $statDao = new StatDao();
        $resultZeroUnclaimedTasks = $statDao->getTotalUnclaimedTasks(null);
        $this->assertEquals(0, $resultZeroUnclaimedTasks);
        
        $orgDao = new OrganisationDao();
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $projectDao = new ProjectDao();
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = $projectDao->createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2", "Task 2 Comment");        

        $taskDao = new TaskDao();
        $insertedTask = $taskDao->create($task);
        $this->assertInstanceOf("Task", $insertedTask);
        sleep(1); // Sleep for 1 second so the two created tasks have a guaranteed different created time
        $insertedTask2 = $taskDao->create($task2);
        $this->assertInstanceOf("Task", $insertedTask2);     
        
        // Success - 2 Total Unclaimed Task
        $resultUnclaimedTasks = $statDao->getTotalUnclaimedTasks(null);
        $this->assertEquals(2, $resultUnclaimedTasks);
        
        // Success - 1 Total Unclaimed Task
        $resultUnclaimedTasksByDate = $statDao->getTotalUnclaimedTasks($insertedTask2->getCreatedTime());
        $this->assertEquals(1, $resultUnclaimedTasksByDate);
        
        
    }
    
    public function testGetStatistics()
    {
        UnitTestHelper::teardownDb();
        $statDao = new StatDao();
        
        // Success - Total 12 Statistics
        $resultAllStatistics = $statDao->getStatistics(null);
        $this->assertCount(12, $resultAllStatistics);
        foreach($resultAllStatistics as $stat) {
            $this->assertInstanceOf("Statistic", $stat);
        }
        
        // Success - Get a single statistic - Total Badges
        $resultAllBadges = $statDao->getStatistics("Badges");
        $this->assertCount(1, $resultAllBadges);
        $this->assertInstanceOf("Statistic", $resultAllBadges[0]);
        $this->assertEquals("Badges", $resultAllBadges[0]->getName());
        $this->assertEquals(3, $resultAllBadges[0]->getValue());
    }
}

?>
