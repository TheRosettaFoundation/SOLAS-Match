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
require_once __DIR__.'/../../Common/Enums/NotificationIntervalEnum.class.php';
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
    /**
     * @covers UI\DAO\UserDao::register
     */
    public function testRegister()
    {
        Tests\UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
        $userEmail = "blah@test.com";
        $userPw = "password";
        $isRegistered = $userDao->register($userEmail, $userPw);
        $this->assertTrue($isRegistered);
    }
    
    /**
     * @covers UI\DAO\UserDao::isUserVerified
     */
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $isVerified = $userDao->isUserVerified($userId);
        //Perhaps should change to return true? returns 1 atm for true
        $this->assertEquals("1", $isVerified);
    }
    
    /**
     * @covers UI\DAO\UserDao::login
     */
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
    
        $loggedIn = $userDao->login($userEmail, $userPw);
        $this->assertNotNull($loggedIn);
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $loggedIn);
        $this->assertEquals($userId, $loggedIn->getId());
    }
    
    /**
     * @covers UI\DAO\UserDao::updateUser
     */
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $updatedUser);
        $this->assertEquals($registerUser->getEmail(), $updatedUser->getEmail());
        $this->assertEquals($registerUser->getBiography(), $updatedUser->getBiography());
    }
    
    /**
     * @covers UI\DAO\UserDao::getUser
     */
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $isRegistered2 = $userDao->register($user2Email, $userPw);
        $this->assertTrue($isRegistered2);
        
        $user = API\DAO\UserDao::getUser(null, $user2Email);
        $user2Id = $user->getId();
        $this->assertNotNull($user);
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $user);
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $loggedIn);
        
        $getUser = $userDao->getUser($userId);
        $this->assertNotNull($getUser);
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $getUser);
    }
    
    /**
     * @covers UI\DAO\UserDao::getUserDart
     */
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
    
        $isRegistered2 = $userDao->register($user2Email, $userPw);
        $this->assertTrue($isRegistered2);
    
        $user = API\DAO\UserDao::getUser(null, $user2Email);
        $this->assertNotNull($user);
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $user);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult2 = API\DAO\UserDao::finishRegistration($user->getId());
        $this->assertEquals("1", $finishRegResult2);
    
        //assert that user can be retrieved after registration is completed
        //User must have admin privileges?
        try {
            $getVerifiedUser = $userDao->getUser($user->getId());
        } catch (Common\Exceptions\SolasMatchException $e) {
            $error = $e->getMessage();
            $this->assertEquals(
                "The Authorization header does not match the current user or the user does not ".
                "have permission to access the current resource",
                $error
            );
        }
        //Add user "blah" as a site admin to test retrieving user
        API\DAO\AdminDao::addSiteAdmin($userId);
        //$this->assertEquals("1", $addAdminResult);
        $loggedIn = $userDao->login($userEmail, $userPw);
        $this->assertNotNull($loggedIn);
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $loggedIn);
    
        $getUser = $userDao->getUserDart($userId);
        $this->assertNotNull($getUser);
        $this->assertJson($getUser);
    }
    
    /**
     * @covers UI\DAO\UserDao::createPersonalInfo
     */
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER_INFO, $insertedInfo);
        $this->assertEquals($personalInfo->getUserId(), $insertedInfo->getUserId());
        $this->assertEquals($personalInfo->getFirstName(), $insertedInfo->getFirstName());
        $this->assertEquals($personalInfo->getLastName(), $insertedInfo->getLastName());
        $this->assertEquals($personalInfo->getMobileNumber(), $insertedInfo->getMobileNumber());
        $this->assertEquals($personalInfo->getBusinessNumber(), $insertedInfo->getBusinessNumber());
        $this->assertEquals($personalInfo->getJobTitle(), $insertedInfo->getJobTitle());
        $this->assertEquals($personalInfo->getAddress(), $insertedInfo->getAddress());
        $this->assertEquals($personalInfo->getCity(), $insertedInfo->getCity());
        $this->assertEquals($personalInfo->getCountry(), $insertedInfo->getCountry());
    }
    
    /**
     * @covers UI\DAO\UserDao::updatePersonalInfo
     */
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER_INFO, $insertedInfo);
        $this->assertEquals($personalInfo->getUserId(), $insertedInfo->getUserId());
        $this->assertEquals($personalInfo->getFirstName(), $insertedInfo->getFirstName());
        $this->assertEquals($personalInfo->getLastName(), $insertedInfo->getLastName());
        $this->assertEquals($personalInfo->getMobileNumber(), $insertedInfo->getMobileNumber());
        $this->assertEquals($personalInfo->getBusinessNumber(), $insertedInfo->getBusinessNumber());
        $this->assertEquals($personalInfo->getJobTitle(), $insertedInfo->getJobTitle());
        $this->assertEquals($personalInfo->getAddress(), $insertedInfo->getAddress());
        $this->assertEquals($personalInfo->getCity(), $insertedInfo->getCity());
        $this->assertEquals($personalInfo->getCountry(), $insertedInfo->getCountry());
        
        $insertedInfo->setFirstName("Harry");
        $insertedInfo->setLastName("Harry");
        $updatedInfo = $userDao->updatePersonalInfo($userId, $insertedInfo);
        $this->assertNotNull($updatedInfo);
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER_INFO, $updatedInfo);
        $this->assertEquals($insertedInfo->getFirstName(), $updatedInfo->getFirstName());
        $this->assertEquals($insertedInfo->getLastName(), $updatedInfo->getLastName());
    }
    
    /**
     * @covers UI\DAO\UserDao::getPersonalInfo
     */
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER_INFO, $insertedInfo);
        $this->assertEquals($personalInfo->getUserId(), $insertedInfo->getUserId());
        $this->assertEquals($personalInfo->getFirstName(), $insertedInfo->getFirstName());
        $this->assertEquals($personalInfo->getLastName(), $insertedInfo->getLastName());
        $this->assertEquals($personalInfo->getMobileNumber(), $insertedInfo->getMobileNumber());
        $this->assertEquals($personalInfo->getBusinessNumber(), $insertedInfo->getBusinessNumber());
        $this->assertEquals($personalInfo->getJobTitle(), $insertedInfo->getJobTitle());
        $this->assertEquals($personalInfo->getAddress(), $insertedInfo->getAddress());
        $this->assertEquals($personalInfo->getCity(), $insertedInfo->getCity());
        $this->assertEquals($personalInfo->getCountry(), $insertedInfo->getCountry());
        
        $getInfo = $userDao->getPersonalInfo($userId);
        $this->assertNotNull($getInfo);
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER_INFO, $getInfo);
        $this->assertEquals($insertedInfo, $getInfo);
    }
    
    /**
     * @covers UI\DAO\UserDao::getUserOrgs
     */
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG, $insertedOrg);
        
        $userOrgs = $userDao->getUserOrgs($userId);
        $this->assertCount(1, $userOrgs);
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG, $userOrgs[0]);
        $this->assertEquals($insertedOrg->getId(), $userOrgs[0]->getId());       
    }
    
    /**
     * @covers UI\DAO\UserDao::trackOrganisation
     */
    public function testTrackOrganisation()
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG,$insertedOrg);
        
        $trackResult = $userDao->trackOrganisation($userId, $insertedOrg->getId());
        $this->assertEquals("1", $trackResult);
        $retrackResult = $userDao->trackOrganisation($userId, $insertedOrg->getId());
        $this->assertEquals("0", $retrackResult);
    }
    
    /**
     * @covers UI\UserDao::untrackOrganisation
     */
    public function testUntrackOrganisation()
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG,$insertedOrg);
        
        $trackResult = $userDao->trackOrganisation($userId, $insertedOrg->getId());
        $this->assertEquals("1", $trackResult);
        $retrackResult = $userDao->trackOrganisation($userId, $insertedOrg->getId());
        $this->assertEquals("0", $retrackResult);
        
        $untrackResult = $userDao->untrackOrganisation($userId, $insertedOrg->getId());
        $this->assertEquals("1", $untrackResult);
        $reuntrackResult = $userDao->untrackOrganisation($userId, $insertedOrg->getId());
        $this->assertEquals("0", $reuntrackResult);
    }
    
    /**
     * @covers UI\DAO\UserDao::isSubscribedToOrganisation
     */
    public function testIsSubscribedToOrganisation()
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG,$insertedOrg);
        
        $trackResult = $userDao->trackOrganisation($userId, $insertedOrg->getId());
        $this->assertEquals("1", $trackResult);
        $retrackResult = $userDao->trackOrganisation($userId, $insertedOrg->getId());
        $this->assertEquals("0", $retrackResult);
        
        $isSubbed = $userDao->isSubscribedToOrganisation($userId, $insertedOrg->getId());
        $this->assertEquals("1", $isSubbed);
        $userDao->untrackOrganisation($userId, $insertedOrg->getId());
        $isNotSubbed = $userDao->isSubscribedToOrganisation($userId, $insertedOrg->getId());
        $this->assertEquals("0", $isNotSubbed);
    }
    
    /**
     * @covers UI\DAO\UserDao::trackProject
     */
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG,$insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = $projectDao->createProject($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf(UnitTestHelper::PROTO_PROJECT, $insertedProject);
        
        $trackProject = $userDao->trackProject($userId, $insertedProject->getId());
        $this->assertEquals("1", $trackProject);
        $retrackProject = $userDao->trackProject($userId, $insertedProject->getId());
        $this->assertEquals("0", $retrackProject);
    }
    
    /**
     * @covers UI\DAO\UserDao::unTrackProject
     */
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
    
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG,$insertedOrg);
    
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = $projectDao->createProject($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf(UnitTestHelper::PROTO_PROJECT, $insertedProject);
    
        $trackProject = $userDao->trackProject($userId, $insertedProject->getId());
        $this->assertEquals("1", $trackProject);
        $retrackProject = $userDao->trackProject($userId, $insertedProject->getId());
        $this->assertEquals("0", $retrackProject);
        
        $untrackProject = $userDao->untrackProject($userId, $insertedProject->getId());
        $this->assertEquals("1", $untrackProject);
        $reuntrackProject = $userDao->untrackProject($userId, $insertedProject->getId());
        $this->assertEquals("0", $reuntrackProject);
    }
    
    /**
     * @covers UI\DAO\UserDao::isSubscribedToProject
     */
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
    
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG,$insertedOrg);
    
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = $projectDao->createProject($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf(UnitTestHelper::PROTO_PROJECT, $insertedProject);
    
        $notSubbed = $userDao->isSubscribedToProject($userId, $insertedProject->getId());
        $this->assertEquals("0", $notSubbed);
        $trackProject = $userDao->trackProject($userId, $insertedProject->getId());
        $this->assertEquals("1", $trackProject);
        
        $isSubbed = $userDao->isSubscribedToProject($userId, $insertedProject->getId());
        $this->assertEquals("1", $isSubbed);
    }
    
    /**
     * @covers UI\DAO\UserDao::trackTask
     */
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG,$insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = $projectDao->createProject($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf(UnitTestHelper::PROTO_PROJECT, $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = $taskDao->createTask($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf(UnitTestHelper::PROTO_TASK, $insertedTask);
        
        $trackTask = $userDao->trackTask($userId, $insertedTask->getId());
        $this->assertEquals("1", $trackTask);
        $retrackTask = $userDao->trackTask($userId, $insertedTask->getId());
        $this->assertEquals("0", $retrackTask);
    }
    
    /**
     * @covers UI\DAO\UserDao::unTrackTask
     */
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
    
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG,$insertedOrg);
    
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = $projectDao->createProject($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf(UnitTestHelper::PROTO_PROJECT, $insertedProject);
    
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = $taskDao->createTask($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf(UnitTestHelper::PROTO_TASK, $insertedTask);
    
        $trackTask = $userDao->trackTask($userId, $insertedTask->getId());
        $this->assertEquals("1", $trackTask);
        $retrackTask = $userDao->trackTask($userId, $insertedTask->getId());
        $this->assertEquals("0", $retrackTask);
        
        $untrackTask = $userDao->untrackTask($userId, $insertedTask->getId());
        $this->assertEquals("1", $untrackTask);
        $reUntracktask = $userDao->untrackTask($userId, $insertedTask->getId());
        $this->assertEquals("0", $reUntracktask);
    }
    
    /**
     * @covers UI\DAO\UserDao::isSubscribedToTask
     */
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG,$insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = $projectDao->createProject($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf(UnitTestHelper::PROTO_PROJECT, $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = $taskDao->createTask($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf(UnitTestHelper::PROTO_TASK, $insertedTask);
        
        $notSubbed = $userDao->isSubscribedToTask($userId, $insertedTask->getId());
        $this->assertEquals("0", $notSubbed);
        $trackTask = $userDao->trackTask($userId, $insertedTask->getId());
        $this->assertEquals("1", $trackTask);
        
        $isSubbed = $userDao->isSubscribedToTask($userId, $insertedTask->getId());
        $this->assertEquals("1", $isSubbed);
    }
    
    /**
     * @covers UI\DAO\UserDao::createSecondaryLanguage
     */
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $userDao->login($userEmail, $userPw);
        $locale = UnitTestHelper::createLocale();
        $userLang = $userDao->createSecondaryLanguage($userId, $locale);
        $this->assertNotNull($userLang);
        $this->assertInstanceOf(UnitTestHelper::PROTO_LOCALE, $userLang);
        $this->assertEquals($locale, $userLang);
    }
    
    /**
     * @covers UI\DAO\UserDao::getSecondaryLanguages
     */
    public function testGetSecondaryLanguages()
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $userDao->login($userEmail, $userPw);
        $locale = UnitTestHelper::createLocale();
        $userLang = $userDao->createSecondaryLanguage($userId, $locale);
        $this->assertNotNull($userLang);
        $this->assertInstanceOf(UnitTestHelper::PROTO_LOCALE, $userLang);
        $this->assertEquals($locale, $userLang);
        $locale2 = UnitTestHelper::createLocale("Japanese", "ja", "JAPAN", "JP");
        $userLang2 = $userDao->createSecondaryLanguage($userId, $locale2);
        $this->assertNotNull($userLang2);
        $this->assertInstanceOf(UnitTestHelper::PROTO_LOCALE, $userLang2);
        $this->assertEquals($locale2, $userLang2);
        
        $getLangs = $userDao->getSecondaryLanguages($userId);
        $this->assertCount(2, $getLangs);
        foreach ($getLangs as $lang) {
            $this->assertInstanceOf(UnitTestHelper::PROTO_LOCALE, $lang);
        }
    }
    
    /**
     * @covers UI\DAO\UserDao::deleteSecondaryLanguage
     */
    public function testDeleteSecondaryLanguage() 
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $userDao->login($userEmail, $userPw);
        $locale = UnitTestHelper::createLocale();
        $userLang = $userDao->createSecondaryLanguage($userId, $locale);
        $this->assertNotNull($userLang);
        $this->assertInstanceOf(UnitTestHelper::PROTO_LOCALE, $userLang);
        $this->assertEquals($locale, $userLang);
        
        $deleteResult = $userDao->deleteSecondaryLanguage($userId, $locale);
        $this->assertEquals("1", $deleteResult);
        $redeleteResult = $userDao->deleteSecondaryLanguage($userId, $locale);
        $this->assertEquals("0", $redeleteResult);
    }

    /**
     * @covers UI\DAO\UserDao::resetPassword
     */
    public function testResetPassword()
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        API\DAO\UserDao::addPasswordResetRequest("gooog", $userId);
        $resetResult = $userDao->resetPassword("blah", "gooog");
        $this->assertEquals("1", $resetResult);        
    }
    
    /**
     * @covers UI\DAO\getPasswordResetRequest
     */
    public function testGetPasswordResetRequest()
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        API\DAO\UserDao::addPasswordResetRequest("gooog", $userId);
        $getReq = $userDao->getPasswordResetRequest("gooog");
        $this->assertNotNull($getReq);
        $this->assertInstanceOf(UnitTestHelper::PROTO_PASSWORD_RESET_REQ, $getReq);
    }
    
    /**
     * @covers UI\DAO\UserDao::claimTask
     */
    public function testClaimTask()
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG, $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = $projectDao->createProject($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf(UnitTestHelper::PROTO_PROJECT, $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = $taskDao->createTask($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf(UnitTestHelper::PROTO_TASK, $insertedTask);
        
        $claimResult = $userDao->claimTask($userId, $insertedTask->getId());
        $this->assertEquals("1", $claimResult);
        try {
            $reclaimResult = $userDao->claimTask($userId, $insertedTask->getId());
        } catch (Common\Exceptions\SolasMatchException $e) {
            $error = $e->getMessage();
            $this->assertEquals("Unable to claim task. This Task has been claimed by another user", $error);
        }
    }
    
    /**
     * @covers UI\DAO\UserDao::unclaimTask
     */
    public function testUnclaimTask()
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG, $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = $projectDao->createProject($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf(UnitTestHelper::PROTO_PROJECT, $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = $taskDao->createTask($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf(UnitTestHelper::PROTO_TASK, $insertedTask);
        
        $claimResult = $userDao->claimTask($userId, $insertedTask->getId());
        $this->assertEquals("1", $claimResult);
        try {
            $reclaimResult = $userDao->claimTask($userId, $insertedTask->getId());
        } catch (Common\Exceptions\SolasMatchException $e) {
            $error = $e->getMessage();
            $this->assertEquals("Unable to claim task. This Task has been claimed by another user", $error);
        }
        
        $unclaimResult = $userDao->unclaimTask($userId, $insertedTask->getId());
        $this->assertEquals("1", $unclaimResult);
        $reunclaimResult = $userDao->unclaimTask($userId, $insertedTask->getId());
        $this->assertEquals("0", $reunclaimResult);
    }
    
    /**
     * @covers UI\DAO\UserDao::requestTaskStreamNotification
     */
    public function testRequestTaskStreamNotification()
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG, $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = $projectDao->createProject($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf(UnitTestHelper::PROTO_PROJECT, $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = $taskDao->createTask($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf(UnitTestHelper::PROTO_TASK, $insertedTask);
        
        $notification = new Common\Protobufs\Models\UserTaskStreamNotification();
        $notification->setUserId($registerUser->getId());
        $notification->setInterval(Common\Enums\NotificationIntervalEnum::DAILY);
        $notification->setStrict(false);
        
        $notifReq = $userDao->requestTaskStreamNotification($notification);
        $this->assertEquals("1",$notifReq);
    }
    
    /**
     * @covers UI\DAO\UserDao::removeTaskStreamNotification
     */
    public function testRemoveTaskStreamNotification()
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
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($userId);
        $this->assertEquals("1", $finishRegResult);
        
        $userDao->login($userEmail, $userPw);
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG, $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = $projectDao->createProject($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf(UnitTestHelper::PROTO_PROJECT, $insertedProject);
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = $taskDao->createTask($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf(UnitTestHelper::PROTO_TASK, $insertedTask);
        
        $notification = new Common\Protobufs\Models\UserTaskStreamNotification();
        $notification->setUserId($registerUser->getId());
        $notification->setInterval(Common\Enums\NotificationIntervalEnum::DAILY);
        $notification->setStrict(false);
        
        $notifReq = $userDao->requestTaskStreamNotification($notification);
        $this->assertEquals("1",$notifReq);
        $removeNotif = $userDao->removeTaskStreamNotification($userId);
        $this->assertTrue($removeNotif);
    }
}