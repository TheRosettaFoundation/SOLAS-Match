<?php

namespace SolasMatch\Tests\UI;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../Common/protobufs/models/WorkflowGraph.php'; //Why does this have to be required?
require_once __DIR__.'/../../Common/protobufs/models/WorkflowNode.php';
require_once __DIR__.'/../../api/DataAccessObjects/AdminDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TagsDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../api/lib/Notify.class.php';
require_once __DIR__.'/../../Common/Enums/BadgeTypes.class.php';
require_once __DIR__.'/../../Common/Enums/HttpMethodEnum.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../../Common/lib/SolasMatchException.php';
require_once __DIR__.'/../../Common/lib/UserSession.class.php';
require_once __DIR__.'/../../ui/DataAccessObjects/AdminDao.class.php';
require_once __DIR__.'/../../ui/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../ui/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../ui/DataAccessObjects/TaskDao.class.php';
require_once __DIR__.'/../../ui/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../ui/lib/Localisation.php';
require_once __DIR__.'/../UnitTestHelper.php';

use \SolasMatch\API as API;
use \SolasMatch\Common as Common;
use \SolasMatch\Tests as Tests;
use \SolasMatch\UI as UI;
use SolasMatch\Tests\UnitTestHelper;

class ProjectDaoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers UI\DAO\ProjectDao::createProject
     */
    public function testCreateProject()
    {
        UnitTestHelper::teardownDb();
    
        $userDao = new UI\DAO\UserDao();
        $orgDao = new UI\DAO\OrganisationDao();
        $projectDao = new UI\DAO\ProjectDao();
        $taskDao = new UI\DAO\TaskDao();
    
        $userEmail = "blah@test.com";
        $userPw = "password";
        $isRegistered = $userDao->register($userEmail, $userPw);
        $this->assertTrue($isRegistered);
    
        $registerUser = API\DAO\UserDao::getUser(null, $userEmail);
        $userId = $registerUser->getId();
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
    
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation",$insertedOrg);
    
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = $projectDao->createProject($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
    
    }
    
    /**
     * @covers UI\DAO\ProjectDao::getProject
     */
    public function testGetProject()
    {
        UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
        $orgDao = new UI\DAO\OrganisationDao();
        $projectDao = new UI\DAO\ProjectDao();
        $taskDao = new UI\DAO\TaskDao();
        
        $userEmail = "blah@test.com";
        $userPw = "password";
        $isRegistered = $userDao->register($userEmail, $userPw);
        $this->assertTrue($isRegistered);
        
        $registerUser = API\DAO\UserDao::getUser(null, $userEmail);
        $userId = $registerUser->getId();
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation",$insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = $projectDao->createProject($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $getProject = $projectDao->getProject($insertedProject->getId());
        $this->assertNotNull($getProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $getProject);
        $this->assertEquals($insertedProject, $getProject);
    }
    
    /**
     * @covers UI\DAO\ProjectDao::getProjectTasks
     */
    public function testGetProjectTasks()
    {
        UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
        $orgDao = new UI\DAO\OrganisationDao();
        $projectDao = new UI\DAO\ProjectDao();
        $taskDao = new UI\DAO\TaskDao();
        
        $userEmail = "blah@test.com";
        $userPw = "password";
        $isRegistered = $userDao->register($userEmail, $userPw);
        $this->assertTrue($isRegistered);
        
        $registerUser = API\DAO\UserDao::getUser(null, $userEmail);
        $userId = $registerUser->getId();
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = $projectDao->createProject($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = $taskDao->createTask($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $insertedTask);
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2");
        $insertedTask2 = $taskDao->createTask($task2);
        $this->assertNotNull($insertedTask2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $insertedTask2);
        
        $projectTasks = $projectDao->getProjectTasks($insertedProject->getId());
        $this->assertNotNull($projectTasks);
        $this->assertCount(2, $projectTasks);
        
        foreach ($projectTasks as $projTask) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $projTask);
        }
    }
    
    /**
     * @covers UI\DAO::getProjectGraph
     */
    public function testGetProjectGraph()
    {
        UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
        $orgDao = new UI\DAO\OrganisationDao();
        $projectDao = new UI\DAO\ProjectDao();
        $taskDao = new UI\DAO\TaskDao();
        
        $userEmail = "blah@test.com";
        $userPw = "password";
        $isRegistered = $userDao->register($userEmail, $userPw);
        $this->assertTrue($isRegistered);
        
        $registerUser = API\DAO\UserDao::getUser(null, $userEmail);
        $userId = $registerUser->getId();
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = $projectDao->createProject($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = $taskDao->createTask($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $insertedTask);
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2");
        $insertedTask2 = $taskDao->createTask($task2);
        $this->assertNotNull($insertedTask2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $insertedTask2);
        
        $projectTasks = $projectDao->getProjectTasks($insertedProject->getId());
        $this->assertNotNull($projectTasks);
        $this->assertCount(2, $projectTasks);
        
        foreach ($projectTasks as $projTask) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $projTask);
        }
        
        $graph = $projectDao->getProjectGraph($insertedProject->getId());
        $this->assertNotNull($graph);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\WorkflowGraph", $graph);
    }
    
    /**
     * @covers UI\ProjectDao::getProjectTags
     */
    public function testGetProjectTags()
    {
        UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
        $orgDao = new UI\DAO\OrganisationDao();
        $projectDao = new UI\DAO\ProjectDao();
        $taskDao = new UI\DAO\TaskDao();
        
        $userEmail = "blah@test.com";
        $userPw = "password";
        $isRegistered = $userDao->register($userEmail, $userPw);
        $this->assertTrue($isRegistered);
        
        $registerUser = API\DAO\UserDao::getUser(null, $userEmail);
        $userId = $registerUser->getId();
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = $projectDao->createProject($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = $taskDao->createTask($task);
        $this->assertNotNull($insertedTask);
        
        $getTags = $projectDao->getProjectTags($insertedProject->getId());
        $this->assertNotNull($getTags);
        $this->assertCount(2, $getTags); //UnitTestHelper gives a project 2 tags by default
        foreach ($getTags as $tag) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Tag", $tag);
        }
    }
}