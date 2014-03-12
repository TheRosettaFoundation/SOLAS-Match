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
        $registerUser = API\DAO\UserDao::getUser(null,$userEmail);
        $registerUser = $registerUser[0];
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($registerUser->getId());
        $this->assertEquals("1", $finishRegResult);
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
        $registerUser = API\DAO\UserDao::getUser(null,$userEmail);
        $registerUser = $registerUser[0];
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($registerUser->getId());
        $this->assertEquals("1", $finishRegResult);
        
        $loggedIn = $userDao->login($userEmail, $userPw);
        $this->assertNotNull($loggedIn);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $loggedIn);
        //TODO Investigate to see if UserDao::login should be changed
        //$this->assertEquals($registerUser, $loggedIn);
    }
    
    public function testGetUser()
    {
        Tests\UnitTestHelper::teardownDb();
    
        $userDao = new UI\DAO\UserDao();
        $userEmail = "blah@test.com";
        $userPw = "password";
        $isRegistered = $userDao->register($userEmail, $userPw);
        $this->assertTrue($isRegistered);
    
        //TODO Edit code to expect single value, not array when system is updated to
        //remove unnecessary array returns
        $registerUser = API\DAO\UserDao::getUser(null,$userEmail);
        $registerUser = $registerUser[0];
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($registerUser->getId());
        $this->assertEquals("1", $finishRegResult);
        
        $isRegistered2 = $userDao->register("hoooo@test.com", $userPw);
        $this->assertTrue($isRegistered2);
        
        //TODO Edit code to expect single value, not array when system is updated to
        //remove unnecessary array returns
        $user = API\DAO\UserDao::getUser(null,"hoooo@test.com");
        $user = $user[0];
        $this->assertNotNull($user);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $user);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult2 = API\DAO\UserDao::finishRegistration($user->getId());
        $this->assertEquals("1", $finishRegResult2);
        
        //assert that user can be retrieved after registration is completed
        //User must have admin priviledges?
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
        
        $getUser = $userDao->getUser($registerUser->getId());
        $this->assertNotNull($getUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $getUser);
    }
    
    public function testGetUserDart()
    {
        Tests\UnitTestHelper::teardownDb();
    
        $userDao = new UI\DAO\UserDao();
        $userEmail = "blah@test.com";
        $userPw = "password";
        $isRegistered = $userDao->register($userEmail, $userPw);
        $this->assertTrue($isRegistered);
    
        //TODO Edit code to expect single value, not array when system is updated to
        //remove unnecessary array returns
        $registerUser = API\DAO\UserDao::getUser(null,$userEmail);
        $registerUser = $registerUser[0];
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $registerUser);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult = API\DAO\UserDao::finishRegistration($registerUser->getId());
        $this->assertEquals("1", $finishRegResult);
    
        $isRegistered2 = $userDao->register("hoooo@test.com", $userPw);
        $this->assertTrue($isRegistered2);
    
        //TODO Edit code to expect single value, not array when system is updated to
        //remove unnecessary array returns
        $user = API\DAO\UserDao::getUser(null,"hoooo@test.com");
        $user = $user[0];
        $this->assertNotNull($user);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $user);
        //Use API DAO because UI one requires UUID which we cannot retrieve (it would be emailed to the user)
        $finishRegResult2 = API\DAO\UserDao::finishRegistration($user->getId());
        $this->assertEquals("1", $finishRegResult2);
    
        //assert that user can be retrieved after registration is completed
        //User must have admin priviledges?
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
        print_r($getUser);
        //$this->assertNotNull($getUser);
        //$this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $getUser);
    }
}