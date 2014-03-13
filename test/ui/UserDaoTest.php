<?php

namespace SolasMatch\Tests\UI;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/AdminDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../api/lib/Notify.class.php';
require_once __DIR__.'/../../Common/Enums/BadgeTypes.class.php';
require_once __DIR__.'/../../Common/Enums/HttpMethodEnum.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../../Common/lib/SolasMatchException.php';
require_once __DIR__.'/../../Common/lib/UserSession.class.php';
require_once __DIR__.'/../../ui/DataAccessObjects/BadgeDao.class.php';
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
        
        /* $userDao->login($userEmail,$userPw);
        $userDao->register("foo@web.com","passw"); */
    }
    
    public function testFinishRegistration()
    {
        Tests\UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
        $userEmail = "blah@test.com";
        $userPw = "password";
        $isRegistered = $userDao->register($userEmail, $userPw);
        $this->assertTrue($isRegistered);
        
        //TODO Edit code to expect single value, not array when system is updated to
        //remove unnecessary array returns
        $registerUser = API\DAO\UserDao::getUser(null, $userEmail);
        $registerUser = $registerUser[0];
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($registerUser->getId());
        $this->assertEquals("1", $finishRegResult);
    }
    
    public function testIsUserVerified()
    {
        Tests\UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
        $userEmail = "blah@test.com";
        $userPw = "password";
        $isRegistered = $userDao->register($userEmail, $userPw);
        $this->assertTrue($isRegistered);
        
        //TODO Edit code to expect single value, not array when system is updated to
        //remove unnecessary array returns
        $registerUser = API\DAO\UserDao::getUser(null, $userEmail);
        $registerUser = $registerUser[0];
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($registerUser->getId());
        $this->assertEquals("1", $finishRegResult);
        
        $isVerified = $userDao->isUserVerified($registerUser->getId());
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
        
        //TODO Edit code to expect single value, not array when system is updated to
        //remove unnecessary array returns
        $registerUser = API\DAO\UserDao::getUser(null, $userEmail);
        $registerUser = $registerUser[0];
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($registerUser->getId());
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
        
        //TODO Edit code to expect single value, not array when system is updated to
        //remove unnecessary array returns
        $registerUser = API\DAO\UserDao::getUser(null, $userEmail);
        $registerUser = $registerUser[0];
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($registerUser->getId());
        $this->assertEquals("1", $finishRegResult);
        
        $loggedIn = $userDao->login($userEmail, $userPw);
        $this->assertNotNull($loggedIn);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $loggedIn);
        $this->assertEquals($registerUser->getId(), $loggedIn->getId());
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
    
        //TODO Edit code to expect single value, not array when system is updated to
        //remove unnecessary array returns
        $registerUser = API\DAO\UserDao::getUser(null, $userEmail);
        $registerUser = $registerUser[0];
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($registerUser->getId());
        $this->assertEquals("1", $finishRegResult);
        
        $isRegistered2 = $userDao->register($user2Email, $userPw);
        $this->assertTrue($isRegistered2);
        
        //TODO Edit code to expect single value, not array when system is updated to
        //remove unnecessary array returns
        $user = API\DAO\UserDao::getUser(null, $user2Email);
        $user = $user[0];
        $this->assertNotNull($user);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $user);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult2 = API\DAO\UserDao::finishRegistration($user->getId());
        $this->assertEquals("1", $finishRegResult2);
        $userDao->login($user2Email, $userPw);
        
        //assert that user can be retrieved after registration is completed
        //User must have admin privileges?
        try {
            $getVerifiedUser = $userDao->getUser($user->getId());
        } catch (Common\Exceptions\SolasMatchException $e) {
            error_log("IN CATCH Block");
            $error = $e->getMessage();
            //TODO Correctly format this assertion
            //$this->assertEquals("message",$error);
        }
        //Add user "blah" as a site admin to test retrieving user
        API\DAO\AdminDao::addSiteAdmin($registerUser->getId());
        //$this->assertEquals("1", $addAdminResult);
        $loggedIn = $userDao->login($userEmail, $userPw);
        $this->assertNotNull($loggedIn);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $loggedIn);
        
        $getUser = $userDao->getUser($registerUser->getId());
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
    
        //TODO Edit code to expect single value, not array when system is updated to
        //remove unnecessary array returns
        $registerUser = API\DAO\UserDao::getUser(null, $userEmail);
        $registerUser = $registerUser[0];
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($registerUser->getId());
        $this->assertEquals("1", $finishRegResult);
    
        $isRegistered2 = $userDao->register($user2Email, $userPw);
        $this->assertTrue($isRegistered2);
    
        //TODO Edit code to expect single value, not array when system is updated to
        //remove unnecessary array returns
        $user = API\DAO\UserDao::getUser(null, $user2Email);
        $user = $user[0];
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
        API\DAO\AdminDao::addSiteAdmin($registerUser->getId());
        //$this->assertEquals("1", $addAdminResult);
        $loggedIn = $userDao->login($userEmail, $userPw);
        $this->assertNotNull($loggedIn);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $loggedIn);
    
        $getUser = $userDao->getUserDart($registerUser->getId());
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
        
        //TODO Edit code to expect single value, not array when system is updated to
        //remove unnecessary array returns
        $registerUser = API\DAO\UserDao::getUser(null, $userEmail);
        $registerUser = $registerUser[0];
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($registerUser->getId());
        $this->assertEquals("1", $finishRegResult);
        
        $personalInfo = Tests\UnitTestHelper::createUserPersonalInfo($registerUser->getId());
        //try to create personal info when not logged in, fails
        try {
            $insertedInfo = $userDao->createPersonalInfo($registerUser->getId(), $personalInfo);
        } catch (Common\Exceptions\SolasMatchException $e) {
            $error = $e->getMessage();
            $this->assertEquals(
                "The Authorization header does not match the current user or the user does not ".
                "have permission to access the current resource",
                $error
            );
        }
        
        $userDao->login($userEmail, $userPw);
        $insertedInfo = $userDao->createPersonalInfo($registerUser->getId(), $personalInfo);
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
        
        //TODO Edit code to expect single value, not array when system is updated to
        //remove unnecessary array returns
        $registerUser = API\DAO\UserDao::getUser(null, $userEmail);
        $registerUser = $registerUser[0];
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($registerUser->getId());
        $this->assertEquals("1", $finishRegResult);
        
        $personalInfo = Tests\UnitTestHelper::createUserPersonalInfo($registerUser->getId());
        //try to create personal info when not logged in, fails
        try {
            $insertedInfo = $userDao->createPersonalInfo($registerUser->getId(), $personalInfo);
        } catch (Common\Exceptions\SolasMatchException $e) {
            $error = $e->getMessage();
            $this->assertEquals(
                    "The Authorization header does not match the current user or the user does not ".
                    "have permission to access the current resource",
                    $error
            );
        }
        
        $userDao->login($userEmail, $userPw);
        $insertedInfo = $userDao->createPersonalInfo($registerUser->getId(), $personalInfo);
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
        $updatedInfo = $userDao->updatePersonalInfo($registerUser->getId(), $insertedInfo);
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
        
        //TODO Edit code to expect single value, not array when system is updated to
        //remove unnecessary array returns
        $registerUser = API\DAO\UserDao::getUser(null, $userEmail);
        $registerUser = $registerUser[0];
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($registerUser->getId());
        $this->assertEquals("1", $finishRegResult);
        
        $personalInfo = Tests\UnitTestHelper::createUserPersonalInfo($registerUser->getId());
        //try to create personal info when not logged in, fails
        try {
            $insertedInfo = $userDao->createPersonalInfo($registerUser->getId(), $personalInfo);
        } catch (Common\Exceptions\SolasMatchException $e) {
            $error = $e->getMessage();
            $this->assertEquals(
                    "The Authorization header does not match the current user or the user does not ".
                    "have permission to access the current resource",
                    $error
            );
        }
        
        $userDao->login($userEmail, $userPw);
        $insertedInfo = $userDao->createPersonalInfo($registerUser->getId(), $personalInfo);
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
        
        $getInfo = $userDao->getPersonalInfo($registerUser->getId());
        $this->assertNotNull($getInfo);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\UserPersonalInformation", $getInfo);
        $this->assertEquals($insertedInfo, $getInfo);
    }
}
