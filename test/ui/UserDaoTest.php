<?php

namespace SolasMatch\Tests\UI;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
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
require_once __DIR__.'/../../ui/DataAccessObjects/BadgeDao.class.php';
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

class UserDaoTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        Tests\UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
        $userEmail = "blah@test.com";
        $userPw = "password";
        $isRegistered = $userDao->register($userEmail, $userPw);
        $this->assertTrue($isRegistered);
    }
    
    public function testFinishRegistration()
    {
        UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
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
    }
    
    public function testIsUserVerified()
    {
        UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
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
        
        $isVerified = $userDao->isUserVerified($userId);
        //Perhaps should change to return true? returns 1 atm for true
        $this->assertEquals("1", $isVerified);
    }
    
    public function testUpdateUser()
    {
        Tests\UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
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
        
        //Try to update user while not logged in, fails
        try {
            $registerUser->setEmail("newmail@mailmail.com");
            $registerUser->setBiography("Rada rada rada");
            $updatedUser = $userDao->updateUser($registerUser);
        } catch (Common\Exceptions\SolasMatchException $e) {
            $error = $e->getMessage();
            $this->assertEquals(
                "The Authorization header does not match the current user or the user does not ".
                "have permission to access the current resource",
                $error
            );
        }
        
        $userDao->login($userEmail, $userPw);
        $registerUser->setEmail("newmail@mailmail.com");
        $registerUser->setBiography("Rada rada rada");
        $updatedUser = $userDao->updateUser($registerUser);
        $this->assertNotNull($updatedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $updatedUser);
        $this->assertEquals($registerUser->getEmail(), $updatedUser->getEmail());
        $this->assertEquals($registerUser->getBiography(), $updatedUser->getBiography());
    }
    
    public function testLogin()
    {
        Tests\UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
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
        
        $loggedIn = $userDao->login($userEmail, $userPw);
        $this->assertNotNull($loggedIn);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $loggedIn);
        $this->assertEquals($userId, $loggedIn->getId());
    }
    
    public function testGetUser()
    {
        Tests\UnitTestHelper::teardownDb();
    
        $userDao = new UI\DAO\UserDao();
        $userEmail = "blah@test.com";
        $user2Email = "hoooo@test.com";
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
        
        $isRegistered2 = $userDao->register($user2Email, $userPw);
        $this->assertTrue($isRegistered2);
        
        $user = API\DAO\UserDao::getUser(null, $user2Email);
        $user2Id = $user->getId();
        $this->assertNotNull($user);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $user);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult2 = API\DAO\UserDao::finishRegistration($user2Id);
        $this->assertEquals("1", $finishRegResult2);
        $userDao->login($user2Email, $userPw);
        
        //assert that user can be retrieved after registration is completed
        //User must have admin privileges?
        $getVerifiedUser = $userDao->getUser($user->getId());
        
        //Add user "blah" as a site admin to test retrieving user
        API\DAO\AdminDao::addSiteAdmin($userId);
        //$this->assertEquals("1", $addAdminResult);
        $loggedIn = $userDao->login($userEmail, $userPw);
        $this->assertNotNull($loggedIn);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $loggedIn);
        
        $getUser = $userDao->getUser($userId);
        $this->assertNotNull($getUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $getUser);
    }
    
    public function testGetUserDart()
    {
        Tests\UnitTestHelper::teardownDb();
    
        $userDao = new UI\DAO\UserDao();
        $userEmail = "blah@test.com";
        $user2Email = "hoooo@test.com";
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
    
        $isRegistered2 = $userDao->register($user2Email, $userPw);
        $this->assertTrue($isRegistered2);
    
        $user = API\DAO\UserDao::getUser(null, $user2Email);
        $this->assertNotNull($user);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $user);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult2 = API\DAO\UserDao::finishRegistration($user->getId());
        $this->assertEquals("1", $finishRegResult2);
    
        //assert that user can be retrieved after registration is completed
        //User must have admin privileges?
        try {
            $getVerifiedUser = $userDao->getUser($user->getId());
        } catch (Common\Exceptions\SolasMatchException $e) {
            $error = $e->getMessage();
            //TODO Correctly format this assertion
            //$this->assertEquals("message",$error);
        }
        //Add user "blah" as a site admin to test retrieving user
        API\DAO\AdminDao::addSiteAdmin($userId);
        //$this->assertEquals("1", $addAdminResult);
        $loggedIn = $userDao->login($userEmail, $userPw);
        $this->assertNotNull($loggedIn);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $loggedIn);
    
        $getUser = $userDao->getUserDart($userId);
        $this->assertNotNull($getUser);
        $this->assertJson($getUser);
    }
    
    public function testCreatePersonalInfo()
    {
        Tests\UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
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
        
        $personalInfo = Tests\UnitTestHelper::createUserPersonalInfo($userId);
        //try to create personal info when not logged in, fails
        try {
            $insertedInfo = $userDao->createPersonalInfo($userId, $personalInfo);
        } catch (Common\Exceptions\SolasMatchException $e) {
            $error = $e->getMessage();
            $this->assertEquals(
                "The Authorization header does not match the current user or the user does not ".
                "have permission to access the current resource",
                $error
            );
        }
        
        $userDao->login($userEmail, $userPw);
        $insertedInfo = $userDao->createPersonalInfo($userId, $personalInfo);
        $this->assertNotNull($insertedInfo);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\UserPersonalInformation", $insertedInfo);
        $this->assertEquals($personalInfo->getUserId(), $insertedInfo->getUserId());
        $this->assertEquals($personalInfo->getFirstName(), $insertedInfo->getFirstName());
        $this->assertEquals($personalInfo->getLastName(), $insertedInfo->getLastName());
        $this->assertEquals($personalInfo->getMobileNumber(), $insertedInfo->getMobileNumber());
        $this->assertEquals($personalInfo->getBusinessNumber(), $insertedInfo->getBusinessNumber());
        $this->assertEquals($personalInfo->getSip(), $insertedInfo->getSip());
        $this->assertEquals($personalInfo->getJobTitle(), $insertedInfo->getJobTitle());
        $this->assertEquals($personalInfo->getAddress(), $insertedInfo->getAddress());
        $this->assertEquals($personalInfo->getCity(), $insertedInfo->getCity());
        $this->assertEquals($personalInfo->getCountry(), $insertedInfo->getCountry());
    }
    
    public function testUpdatePersonalInfo()
    {
        Tests\UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
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
        
        $personalInfo = Tests\UnitTestHelper::createUserPersonalInfo($userId);
        //try to create personal info when not logged in, fails
        try {
            $insertedInfo = $userDao->createPersonalInfo($userId, $personalInfo);
        } catch (Common\Exceptions\SolasMatchException $e) {
            $error = $e->getMessage();
            $this->assertEquals(
                    "The Authorization header does not match the current user or the user does not ".
                    "have permission to access the current resource",
                    $error
            );
        }
        
        $userDao->login($userEmail, $userPw);
        $insertedInfo = $userDao->createPersonalInfo($userId, $personalInfo);
        $this->assertNotNull($insertedInfo);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\UserPersonalInformation", $insertedInfo);
        $this->assertEquals($personalInfo->getUserId(), $insertedInfo->getUserId());
        $this->assertEquals($personalInfo->getFirstName(), $insertedInfo->getFirstName());
        $this->assertEquals($personalInfo->getLastName(), $insertedInfo->getLastName());
        $this->assertEquals($personalInfo->getMobileNumber(), $insertedInfo->getMobileNumber());
        $this->assertEquals($personalInfo->getBusinessNumber(), $insertedInfo->getBusinessNumber());
        $this->assertEquals($personalInfo->getSip(), $insertedInfo->getSip());
        $this->assertEquals($personalInfo->getJobTitle(), $insertedInfo->getJobTitle());
        $this->assertEquals($personalInfo->getAddress(), $insertedInfo->getAddress());
        $this->assertEquals($personalInfo->getCity(), $insertedInfo->getCity());
        $this->assertEquals($personalInfo->getCountry(), $insertedInfo->getCountry());
        
        $insertedInfo->setFirstName("Harry");
        $insertedInfo->setLastName("Harry");
        $updatedInfo = $userDao->updatePersonalInfo($userId, $insertedInfo);
        $this->assertNotNull($updatedInfo);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\UserPersonalInformation", $updatedInfo);
        $this->assertEquals($insertedInfo->getFirstName(), $updatedInfo->getFirstName());
        $this->assertEquals($insertedInfo->getLastName(), $updatedInfo->getLastName());
    }
    
    public function testGetPersonalInfo()
    {
        Tests\UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
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
        
        $personalInfo = Tests\UnitTestHelper::createUserPersonalInfo($userId);
        //try to create personal info when not logged in, fails
        try {
            $insertedInfo = $userDao->createPersonalInfo($userId, $personalInfo);
        } catch (Common\Exceptions\SolasMatchException $e) {
            $error = $e->getMessage();
            $this->assertEquals(
                    "The Authorization header does not match the current user or the user does not ".
                    "have permission to access the current resource",
                    $error
            );
        }
        
        $userDao->login($userEmail, $userPw);
        $insertedInfo = $userDao->createPersonalInfo($userId, $personalInfo);
        $this->assertNotNull($insertedInfo);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\UserPersonalInformation", $insertedInfo);
        $this->assertEquals($personalInfo->getUserId(), $insertedInfo->getUserId());
        $this->assertEquals($personalInfo->getFirstName(), $insertedInfo->getFirstName());
        $this->assertEquals($personalInfo->getLastName(), $insertedInfo->getLastName());
        $this->assertEquals($personalInfo->getMobileNumber(), $insertedInfo->getMobileNumber());
        $this->assertEquals($personalInfo->getBusinessNumber(), $insertedInfo->getBusinessNumber());
        $this->assertEquals($personalInfo->getSip(), $insertedInfo->getSip());
        $this->assertEquals($personalInfo->getJobTitle(), $insertedInfo->getJobTitle());
        $this->assertEquals($personalInfo->getAddress(), $insertedInfo->getAddress());
        $this->assertEquals($personalInfo->getCity(), $insertedInfo->getCity());
        $this->assertEquals($personalInfo->getCountry(), $insertedInfo->getCountry());
        
        $getInfo = $userDao->getPersonalInfo($userId);
        $this->assertNotNull($getInfo);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\UserPersonalInformation", $getInfo);
        $this->assertEquals($insertedInfo, $getInfo);
    }
    
    public function testGetUserOrgs()
    {
        UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
        $orgDao = new UI\DAO\OrganisationDao();
        
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
        
        $org = UnitTestHelper::createOrg();
        try {
            $insertedOrg = $orgDao->createOrg($org, $userId);
        } catch (Common\Exceptions\SolasMatchException $e) {
            $error = $e->getMessage();
            $this->assertEquals(
                    "The Authorization header does not match the current user or the user does not ".
                    "have permission to access the current resource",
                    $error
            );
        }
        $userDao->login($userEmail, $userPw);
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $userOrgs = $userDao->getUserOrgs($userId);
        $this->assertCount(1, $userOrgs);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $userOrgs[0]);
        $this->assertEquals($insertedOrg->getId(), $userOrgs[0]->getId());       
    }
    
    public function testTrackProject()
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
        
        $trackProject = $userDao->trackProject($userId, $insertedProject->getId());
        $this->assertEquals("1", $trackProject);
        $retrackProject = $userDao->trackProject($userId, $insertedProject->getId());
        $this->assertEquals("0", $retrackProject);
    }
    
    public function testUntrackProject()
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
    
        $trackProject = $userDao->trackProject($userId, $insertedProject->getId());
        $this->assertEquals("1", $trackProject);
        $retrackProject = $userDao->trackProject($userId, $insertedProject->getId());
        $this->assertEquals("0", $retrackProject);
        
        $untrackProject = $userDao->untrackProject($userId, $insertedProject->getId());
        $this->assertEquals("1", $untrackProject);
        $reuntrackProject = $userDao->untrackProject($userId, $insertedProject->getId());
        $this->assertEquals("0", $reuntrackProject);
    }
    
    public function testIsSubscribedToProject()
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
    
        $notSubbed = $userDao->isSubscribedToProject($userId, $insertedProject->getId());
        $this->assertEquals("0", $notSubbed);
        $trackProject = $userDao->trackProject($userId, $insertedProject->getId());
        $this->assertEquals("1", $trackProject);
        
        $isSubbed = $userDao->isSubscribedToProject($userId, $insertedProject->getId());
        $this->assertEquals("1", $isSubbed);
    }
    
    public function testTrackTask()
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
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = $taskDao->createTask($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $insertedTask);
        
        $trackTask = $userDao->trackTask($userId, $insertedTask->getId());
        $this->assertEquals("1", $trackTask);
        $retrackTask = $userDao->trackTask($userId, $insertedTask->getId());
        $this->assertEquals("0", $retrackTask);
    }
    
    public function testUntrackTask()
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
    
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = $taskDao->createTask($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $insertedTask);
    
        $trackTask = $userDao->trackTask($userId, $insertedTask->getId());
        $this->assertEquals("1", $trackTask);
        $retrackTask = $userDao->trackTask($userId, $insertedTask->getId());
        $this->assertEquals("0", $retrackTask);
        
        $untrackTask = $userDao->untrackTask($userId, $insertedTask->getId());
        $this->assertEquals("1", $untrackTask);
        $reUntracktask = $userDao->untrackTask($userId, $insertedTask->getId());
        $this->assertEquals("0", $reUntracktask);
    }
    
    public function testIsSubscribedToTask()
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
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = $taskDao->createTask($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $insertedTask);
        
        $notSubbed = $userDao->isSubscribedToTask($userId, $insertedTask->getId());
        $this->assertEquals("0", $notSubbed);
        $trackTask = $userDao->trackTask($userId, $insertedTask->getId());
        $this->assertEquals("1", $trackTask);
        
        $isSubbed = $userDao->isSubscribedToTask($userId, $insertedTask->getId());
        $this->assertEquals("1", $isSubbed);
    }
    
    public function testCreateSecondaryLanguage()
    {
        UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
        
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
        $locale = UnitTestHelper::createLocale();
        $userLang = $userDao->createSecondaryLanguage($userId, $locale);
        $this->assertNotNull($userLang);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Locale", $userLang);
        $this->assertEquals($locale, $userLang);
    }
}
