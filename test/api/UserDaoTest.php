<?php

namespace SolasMatch\Tests\API;

use \SolasMatch\Tests\UnitTestHelper;
use \SolasMatch\Common as Common;
use \SolasMatch\API as API;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TagsDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TaskDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__."/../../Common/lib/Authentication.class.php";
require_once __DIR__."/../../Common/Enums/NotificationIntervalEnum.class.php";
require_once __DIR__.'/../../Common/Enums/BadgeTypes.class.php';
require_once __DIR__.'/../UnitTestHelper.php';

class UserDaoTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateUser()
    {
        UnitTestHelper::teardownDb();
        
        // Success
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $this->assertEquals("testuser@example.com", $insertedUser->getEmail());
        $this->assertNotNull($insertedUser->getPassword());
        $this->assertNotNull($insertedUser->getNonce());
    }
    
    public function testUpdateUser()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $insertedUser->setDisplayName("Updated DisplayName");
        $insertedUser->setEmail("updatedEmail@test.com");
        $insertedUser->setBiography("Updated Bio");
        
        $locale = new Common\Protobufs\Models\Locale();
        $locale->setLanguageCode("en");
        $locale->setCountryCode("IE");
        $insertedUser->setNativeLocale($locale);

        $insertedUser->setNonce(123456789);
        $insertedUser->setPassword(md5("derpymerpy"));
      
        // Success
        $updatedUser = API\DAO\UserDao::save($insertedUser);
        $this->assertNotNull($updatedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $updatedUser);
        $this->assertEquals($insertedUser->getId(), $updatedUser->getId());
        $this->assertEquals($insertedUser->getDisplayName(), $updatedUser->getDisplayName()); //Failure!!
        $this->assertEquals($insertedUser->getEmail(), $updatedUser->getEmail());
        $this->assertEquals($insertedUser->getBiography(), $updatedUser->getBiography());
        
        $this->assertEquals(
            $insertedUser->getNativeLocale()->getLanguageCode(),
            $updatedUser->getNativeLocale()->getLanguageCode()
        );
        $this->assertEquals(
            $insertedUser->getNativeLocale()->getCountryCode(),
            $updatedUser->getNativeLocale()->getCountryCode()
        );
        
        $this->assertEquals($insertedUser->getNonce(), $updatedUser->getNonce());
        $this->assertEquals($insertedUser->getPassword(), $updatedUser->getPassword());
    }
    
    public function testGetUser()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $insertedUser->setDisplayName("Updated DisplayName");
        $insertedUser->setEmail("updatedEmail@test.com");
        $insertedUser->setBiography("Updated Bio");
        
        $locale = new Common\Protobufs\Models\Locale();
        $locale->setLanguageCode("en");
        $locale->setCountryCode("IE");
        $insertedUser->setNativeLocale($locale);
        
        $insertedUser->setNonce(123456789);
        $insertedUser->setPassword(md5("derpymerpy"));
        $updatedUser = API\DAO\UserDao::save($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $updatedUser);
              
        // Success
        $getUpdatedUser = API\DAO\UserDao::getUser($updatedUser->getId());
        $this->assertNotNull($getUpdatedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $getUpdatedUser);
        $this->assertEquals($insertedUser->getId(), $getUpdatedUser->getId());
        
        $this->assertEquals($insertedUser->getDisplayName(), $getUpdatedUser->getDisplayName());
        $this->assertEquals($insertedUser->getEmail(), $getUpdatedUser->getEmail());
        $this->assertEquals($insertedUser->getBiography(), $getUpdatedUser->getBiography());
        
        $this->assertEquals(
            $insertedUser->getNativeLocale()->getLanguageCode(),
            $updatedUser->getNativeLocale()->getLanguageCode()
        );
        $this->assertEquals(
            $insertedUser->getNativeLocale()->getCountryCode(),
            $updatedUser->getNativeLocale()->getCountryCode()
        );
        
        $this->assertEquals($insertedUser->getNonce(), $getUpdatedUser->getNonce());
        $this->assertEquals($insertedUser->getPassword(), $getUpdatedUser->getPassword());
    }
    
    public function testGetUsers()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $user2 = UnitTestHelper::createUser(null,"Foo",null,"foofoo@com.com");
        $insertedUser2 = API\DAO\UserDao::save($user2);
        $this->assertNotNull($insertedUser2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser2);
        
        $getUsers = API\DAO\UserDao::getUsers();
        $this->assertCount(2, $getUsers);
        foreach ($getUsers as $savedUser) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $savedUser);
        }
    }
    
    public function testDeleteUser()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        API\DAO\UserDao::deleteUser($insertedUser->getId());
        $getDeletedUser = API\DAO\UserDao::getUser($insertedUser->getId());
        $this->assertNull($getDeletedUser);
    }
    
    public function testChangePassword()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        // Success
        $userWithChangedPw = API\DAO\UserDao::changePassword($insertedUser->getId(), "New Password");
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $userWithChangedPw);
        $this->assertNotEquals($insertedUser->getPassword(), $userWithChangedPw->getPassword());
        $this->assertNotEquals($insertedUser->getNonce(), $userWithChangedPw->getNonce());
    }
    
    public function testApiRegister()
    {
        UnitTestHelper::teardownDb();
        
        $email = "foocoochoo@blah.com";
        $pw = "password";
        
        $result = API\DAO\UserDao::apiRegister($email, $pw);
        $this->assertEquals('1', $result);

        $user = API\DAO\UserDao::getUser(null, $email);
        $this->assertNotNull($user);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $user);
        $this->assertEquals($email, $user->getEmail());
        
        //Get user's nonce and use it to generate the hashed password that is stored in $regUser and assert that this
        //result does indeed match the password stored in $regUser
        $nonce = $user->getNonce();
        $hashpw = Common\Lib\Authentication::hashPassword($pw, $nonce);
        $this->assertEquals($user->getPassword(), $hashpw);
    }
    
    public function testFinishRegistration()
    {
        UnitTestHelper::teardownDb();
        
        $email = "foocoochoo@blah.com";
        $pw = "password";
        
        $result = API\DAO\UserDao::apiRegister($email, $pw);
        $this->assertEquals('1', $result);

        $user = API\DAO\UserDao::getUser(null, $email);
        $this->assertNotNull($user);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $user);
        $this->assertEquals($email, $user->getEmail());
        
        //Get user's nonce and use it to generate the hashed password that is stored in $regUser and assert that this
        //result does indeed match the password stored in $regUser
        $nonce = $user->getNonce();
        $hashpw = Common\Lib\Authentication::hashPassword($pw, $nonce);
        $this->assertEquals($user->getPassword(), $hashpw);
        
        $finishReg = API\DAO\UserDao::finishRegistration($user->getId());
        $this->assertEquals("1", $finishReg);
    }
    
    public function testIsUserVerified()
    {
        UnitTestHelper::teardownDb();
    
        $email = "foocoochoo@blah.com";
        $pw = "password";
    
        $result = API\DAO\UserDao::apiRegister($email, $pw);
        $this->assertEquals('1', $result);

        $user = API\DAO\UserDao::getUser(null, $email);
        $this->assertNotNull($user);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $user);
        $this->assertEquals($email, $user->getEmail());
    
        //Get user's nonce and use it to generate the hashed password that is stored in $regUser and assert that this
        //result does indeed match the password stored in $regUser
        $nonce = $user->getNonce();
        $hashpw = Common\Lib\Authentication::hashPassword($pw, $nonce);
        $this->assertEquals($user->getPassword(), $hashpw);
    
        $finishReg = API\DAO\UserDao::finishRegistration($user->getId());
        $this->assertEquals("1", $finishReg);
        
        $isVerified = API\DAO\UserDao::isUserVerified($user->getId());
        $this->assertEquals("1", $isVerified);
    }
    
    public function testFindOrganisationsUserBelongsTo()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg->getId());
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $resultRequestMembership = API\DAO\OrganisationDao::requestMembership(
            $insertedUser->getId(),
            $insertedOrg->getId()
        );
        $this->assertEquals("1", $resultRequestMembership);
        
        $resultAcceptMembership = API\DAO\OrganisationDao::acceptMemRequest(
            $insertedOrg->getId(),
            $insertedUser->getId()
        );
        $this->assertEquals("1", $resultAcceptMembership);
        
        $org2 = UnitTestHelper::createOrg(
            null,
            "Organisation 2",
            "Organisation 2 Bio",
            "http://www.organisation2.org"
        );
        $insertedOrg2 = API\DAO\OrganisationDao::insertAndUpdate($org2);
        $this->assertNotNull($insertedOrg2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg2);
        
        $resultRequestMembership2 = API\DAO\OrganisationDao::requestMembership(
            $insertedUser->getId(),
            $insertedOrg2->getId()
        );
        $this->assertEquals("1", $resultRequestMembership2);
        
        $resultAcceptMembership2 = API\DAO\OrganisationDao::acceptMemRequest(
            $insertedOrg2->getId(),
            $insertedUser->getId()
        );
        $this->assertEquals("1", $resultAcceptMembership2);
        
        // Success
        $userOrgs = API\DAO\UserDao::findOrganisationsUserBelongsTo($insertedUser->getId());
        $this->assertCount(2, $userOrgs);
        foreach ($userOrgs as $org) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $org);
        }
    }
    
    public function testGetUserBadges()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
        
        $userBadges = API\DAO\UserDao::getUserBadges($userId);
        $this->assertNull($userBadges);
        
        $assignBadge = API\DAO\BadgeDao::assignBadge($userId, 3);
        $this->assertEquals(1, $assignBadge);
        
        $userBadges1 = API\DAO\UserDao::getUserBadges($userId);
        $this->assertCount(1, $userBadges1);
        foreach ($userBadges1 as $badge) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $badge);
        }
        
        $assignBadge1 = API\DAO\BadgeDao::assignBadge($userId, 4);
        $this->assertEquals(1, $assignBadge1);
        
        $userBadges2 = API\DAO\UserDao::getUserBadges($userId);
        $this->assertCount(2, $userBadges2);
        foreach ($userBadges2 as $badge) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $badge);
        }
        
        $assignBadge2 = API\DAO\BadgeDao::assignBadge($userId, 5);
        $this->assertEquals(1, $assignBadge2);
        
        // Success
        $userBadges3 = API\DAO\UserDao::getUserBadges($userId);
        $this->assertCount(3, $userBadges3);
        foreach ($userBadges3 as $badge) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $badge);
        }
    }
    
    public function testGetUserTags()
    {
        UnitTestHelper::teardownDb();
       
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
        
        // Failure
        $noUserTags = API\DAO\UserDao::getUserTags($userId);
        $this->assertNull($noUserTags);
        
        $tag = API\DAO\TagsDao::create("English");
        $this->assertNotNull($tag);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Tag", $tag);
        
        $tag2 = API\DAO\TagsDao::create("French");
        $this->assertNotNull($tag2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Tag", $tag2);
        
        $tagLiked = API\DAO\UserDao::likeTag($userId, $tag->getId());
        $this->assertEquals("1", $tagLiked);
        // Success
        $oneUserTag = API\DAO\UserDao::getUserTags($userId);
        $this->assertCount(1, $oneUserTag);
        foreach ($oneUserTag as $tag) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Tag", $tag);
        }

        $tagLiked2 = API\DAO\UserDao::likeTag($userId, $tag2->getId());
        $this->assertEquals("1", $tagLiked2);
        
        // Success
        $twoUserTags = API\DAO\UserDao::getUserTags($userId);
        $this->assertCount(2, $twoUserTags);
        foreach ($twoUserTags as $tag) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Tag", $tag);
        }
    }
    
    public function testGetUsersWithBadge()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        // Failure
        $noUsersWithBadge = API\DAO\UserDao::getUsersWithBadge(3);
        $this->assertNull($noUsersWithBadge);
        
        $assignBadge = API\DAO\BadgeDao::assignBadge($insertedUser->getId(), 3);
        $this->assertEquals(1, $assignBadge);
        
        // Success
        $oneUserWithBadge = API\DAO\UserDao::getUsersWithBadge(3);
        $this->assertCount(1, $oneUserWithBadge);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $oneUserWithBadge[0]);
        
        $insertedUser2 = API\DAO\UserDao::create("testuser2@example.com", "testpw2");
        $this->assertNotNull($insertedUser2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser2);
        
        $assignBadge2 = API\DAO\BadgeDao::assignBadge($insertedUser2->getId(), 3);
        $this->assertEquals(1, $assignBadge2);
        
        // Success
        $twoUsersWithBadge = API\DAO\UserDao::getUsersWithBadge(3);
        $this->assertCount(2, $twoUsersWithBadge);
        foreach ($twoUsersWithBadge as $user) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $user);
        }
    }
    
    public function testLikeTag()
    {
        UnitTestHelper::teardownDb();
       
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
        
        $tag = API\DAO\TagsDao::create("English");
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Tag", $tag);
        $this->assertNotNull($tag->getId());
       
        // Success
        $tagLiked = API\DAO\UserDao::likeTag($userId, $tag->getId());
        $this->assertEquals("1", $tagLiked);
        
        // Failure
        $tagLikedFailure = API\DAO\UserDao::likeTag($userId, $tag->getId());
        $this->assertEquals("0", $tagLikedFailure);
    }
    
    public function testRemoveTag()
    {
        UnitTestHelper::teardownDb();
       
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
        
        $tag = API\DAO\TagsDao::create("English");
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Tag", $tag);
        $this->assertNotNull($tag->getId());
        
        $tagLiked = API\DAO\UserDao::likeTag($userId, $tag->getId());
        $this->assertEquals("1", $tagLiked);
        
        // Success
        $removedTag = API\DAO\UserDao::removeTag($userId, $tag->getId());
        $this->assertEquals("1", $removedTag);
        
        // Failure
        $removedTagFailure = API\DAO\UserDao::removeTag($userId, $tag->getId());
        $this->assertEquals("0", $removedTagFailure);
    }
    
    public function testTrackTask()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
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
        
        $trackTaskFail = API\DAO\UserDao::trackTask($insertedUser->getId(), $insertedTask->getId());
        $this->assertEquals("0", $trackTaskFail);
    }
    
    public function testIgnoreTask()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
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
        
        $ignoreTask = API\DAO\UserDao::ignoreTask($insertedUser->getId(), $insertedTask->getId());
        $this->assertEquals("1", $ignoreTask);
        
        $ignoreTaskFail = API\DAO\UserDao::ignoreTask($insertedUser->getId(), $insertedTask->getId());
        $this->assertEquals("0", $ignoreTaskFail);
    }
    
    public function testIsSubscribedToTask()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
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
        
        $isTrackingTaskFail = API\DAO\UserDao::isSubscribedToTask($insertedUser->getId(), $insertedTask->getId());
        $this->assertEquals("0", $isTrackingTaskFail);
        
        $trackTask = API\DAO\UserDao::trackTask($insertedUser->getId(), $insertedTask->getId());
        $this->assertEquals("1", $trackTask);
        
        $isTrackingTask = API\DAO\UserDao::isSubscribedToTask($insertedUser->getId(), $insertedTask->getId());
        $this->assertEquals("1", $isTrackingTask);
    }

    public function testGetTrackedTasks()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
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
        
        //User has no tracked tasks, nothing is returned
        $getNoTrackedTasks = API\DAO\UserDao::getTrackedTasks($insertedUser->getId());
        $this->assertNull($getNoTrackedTasks);
        
        $trackTask = API\DAO\UserDao::trackTask($insertedUser->getId(), $insertedTask->getId());
        $this->assertEquals("1", $trackTask);
        
        //User has tracked a task, something will be returned
        $getTrackedTasks = API\DAO\UserDao::getTrackedTasks($insertedUser->getId());
        $this->assertCount(1, $getTrackedTasks);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Task", $getTrackedTasks[0]);
        $this->assertEquals($insertedTask->getId(), $getTrackedTasks[0]->getId());
        $this->assertEquals($insertedTask->getTaskStatus(), $getTrackedTasks[0]->getTaskStatus());
    }
    
    public function testCreatePasswordResetRequest()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        // Success
        $createPwResetRequest = API\DAO\UserDao::addPasswordResetRequest(
            "asfjkosagijo".$insertedUser->getId(),
            $insertedUser->getId()
        );
        $this->assertEquals("1", $createPwResetRequest);
    }
    
    public function testHasRequestedPasswordReset()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $createPwResetRequest = API\DAO\UserDao::addPasswordResetRequest(
            "asfjkosagijo".$insertedUser->getId(),
            $insertedUser->getId()
        );
        $this->assertEquals("1", $createPwResetRequest);
        $hasPwResetReq = API\DAO\UserDao::hasRequestedPasswordReset($insertedUser->getEmail());
        $this->assertTrue($hasPwResetReq);
    }
    
    public function testRemovePasswordResetRequest()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = API\DAO\UserDao::create("testuser@example.com", "testpw");
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $createPwResetRequest = API\DAO\UserDao::addPasswordResetRequest(
            "asfjkosagijo".$insertedUser->getId(),
            $insertedUser->getId()
        );
        $this->assertEquals("1", $createPwResetRequest);
        
        // Success
        $removePwResetRequest = API\DAO\UserDao::removePasswordResetRequest($insertedUser->getId());
        $this->assertEquals("1", $removePwResetRequest);
        
        // Failure
        $removePwResetRequestFail = API\DAO\UserDao::removePasswordResetRequest($insertedUser->getId());
        $this->assertEquals("0", $removePwResetRequestFail);
    }
    
    public function testGetPasswordResetRequests()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $createPwResetRequest = API\DAO\UserDao::addPasswordResetRequest(
            "asfjkosagijo".$insertedUser->getId(),
            $insertedUser->getId()
        );
        $this->assertEquals("1", $createPwResetRequest);
        
        // Success
        $passwordResetRequest = API\DAO\UserDao::getPasswordResetRequests(
            $insertedUser->getEmail(),
            "asfjkosagijo".$insertedUser->getId()
        );
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\PasswordResetRequest", $passwordResetRequest);
        
        $removePwResetRequest = API\DAO\UserDao::removePasswordResetRequest($insertedUser->getId());
        $this->assertEquals("1", $removePwResetRequest);
        
        // Failure
        $passwordResetRequestFailure = API\DAO\UserDao::getPasswordResetRequests($insertedUser->getId());
        $this->assertNull($passwordResetRequestFailure);
    }
    
    public function testTrackProject()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        // Failure
        $trackProjectFailure = API\DAO\UserDao::trackProject(999, $insertedUser->getId());
        $this->assertNull($trackProjectFailure);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        // Success
        $trackProject = API\DAO\UserDao::trackProject($insertedProject->getId(), $insertedUser->getId());
        $this->assertEquals("1", $trackProject);
    }
    
    public function testUnTrackProject()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        //User has not already tracked the project, valid failure
        $untrackProjectFailure = API\DAO\UserDao::unTrackProject($insertedProject->getId(), $insertedUser->getId());
        $this->assertEquals("0", $untrackProjectFailure);
        
        $trackProject = API\DAO\UserDao::trackProject($insertedProject->getId(), $insertedUser->getId());
        $this->assertEquals("1", $trackProject);
        
        //User has tracked the project, successful untracking
        $untrackProject = API\DAO\UserDao::unTrackProject($insertedProject->getId(), $insertedUser->getId());
        $this->assertEquals("1", $untrackProject);
    }
    
    public function testGetTrackedProjects()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        $project2 = UnitTestHelper::createProject(
            $insertedOrg->getId(),
            null,
            "Project 2 Title",
            "Project 2 Description"
        );
        $insertedProject2 = API\DAO\ProjectDao::save($project2);
        $this->assertNotNull($insertedProject2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject2);
        
        $trackProject = API\DAO\UserDao::trackProject($insertedProject->getId(), $insertedUser->getId());
        $this->assertEquals("1", $trackProject);
        
        $trackProject2 = API\DAO\UserDao::trackProject($insertedProject2->getId(), $insertedUser->getId());
        $this->assertEquals("1", $trackProject2);
        
        // Success
        $trackedProjects = API\DAO\UserDao::getTrackedProjects($insertedUser->getId());
        $this->assertCount(2, $trackedProjects);
        foreach ($trackedProjects as $project) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $project);
        }
    }
    
    public function testIsSubscribedToProject()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Project", $insertedProject);
        
        //User is not subscribed
        $isSubscribedToProjectFailure = API\DAO\UserDao::isSubscribedToProject(
            $insertedUser->getId(),
            $insertedProject->getId()
        );
        $this->assertEquals("0", $isSubscribedToProjectFailure);
        
        $trackProject = API\DAO\UserDao::trackProject($insertedProject->getId(), $insertedUser->getId());
        $this->assertEquals("1", $trackProject);
        
        //User is subscribed
        $isSubscribedToProject = API\DAO\UserDao::isSubscribedToProject(
            $insertedUser->getId(),
            $insertedProject->getId()
        );
        $this->assertEquals("1", $isSubscribedToProject);
    }
    
    public function testGetUserTaskStreamNotification()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
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
        
        $notification = new Common\Protobufs\Models\UserTaskStreamNotification();
        $notification->setUserId($insertedUser->getId());
        $notification->setInterval(Common\Enums\NotificationIntervalEnum::DAILY);
        $notification->setStrict(false);
        API\DAO\UserDao::requestTaskStreamNotification($notification);
        
        $getTsn = API\DAO\UserDao::getUserTaskStreamNotification($insertedUser->getId());
        $this->assertNotNull($getTsn);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\UserTaskStreamNotification", $getTsn);
        $this->assertEquals($insertedUser->getId(), $getTsn->getUserId());
        $this->assertEquals(Common\Enums\NotificationIntervalEnum::DAILY, $getTsn->getInterval());
    }
    
    public function testRemoveTaskStreamNotification()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
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
        
        $notification = new Common\Protobufs\Models\UserTaskStreamNotification();
        $notification->setUserId($insertedUser->getId());
        $notification->setInterval(Common\Enums\NotificationIntervalEnum::DAILY);
        $notification->setStrict(false);
        API\DAO\UserDao::requestTaskStreamNotification($notification);
        
        $getTsn = API\DAO\UserDao::getUserTaskStreamNotification($insertedUser->getId());
        $this->assertNotNull($getTsn);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\UserTaskStreamNotification", $getTsn);
        $this->assertEquals($insertedUser->getId(), $getTsn->getUserId());
        $this->assertEquals(Common\Enums\NotificationIntervalEnum::DAILY, $getTsn->getInterval());
        
        $removeTsn = API\DAO\UserDao::removeTaskStreamNotification($insertedUser->getId());
        $this->assertTrue($removeTsn);
    }
    
    public function testCreatePersonalInfo()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $userInfo = UnitTestHelper::createUserPersonalInfo($insertedUser->getId());
        $insertedInfo = API\DAO\UserDao::createPersonalInfo($userInfo);
        $this->assertNotNull($insertedInfo);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\UserPersonalInformation", $insertedInfo);
        $this->assertEquals($userInfo->getReceiveCredit(), $insertedInfo->getReceiveCredit());
    }
    
    public function testUpdatePersonalInfo()
    {
        UnitTestHelper::teardownDb();
    
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
    
        $userInfo = UnitTestHelper::createUserPersonalInfo($insertedUser->getId());
        $insertedInfo = API\DAO\UserDao::createPersonalInfo($userInfo);
        $this->assertNotNull($insertedInfo);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\UserPersonalInformation", $insertedInfo);
        
        $insertedInfo->setFirstName("Roy");
        $insertedInfo->setLastName("Jaeger");
        $insertedInfo->setMobileNumber(55555221333);
        $updatedInfo = API\DAO\UserDao::updatePersonalInfo($insertedInfo);
        $this->assertNotNull($updatedInfo);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\UserPersonalInformation", $updatedInfo);
        
        $this->assertEquals($insertedInfo->getFirstName(), $updatedInfo->getFirstName());
        $this->assertEquals($insertedInfo->getLastName(), $updatedInfo->getLastName());
        $this->assertEquals($insertedInfo->getMobileNumber(), $updatedInfo->getMobileNumber());
    }
    
    public function testGetPersonalInfo()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $userInfo = UnitTestHelper::createUserPersonalInfo($insertedUser->getId());
        $insertedInfo = API\DAO\UserDao::createPersonalInfo($userInfo);
        $this->assertNotNull($insertedInfo);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\UserPersonalInformation", $insertedInfo);
        
        $getInfo = API\DAO\UserDao::getPersonalInfo($insertedInfo->getId());
        $this->assertNotNull($getInfo);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\UserPersonalInformation", $getInfo);
        $this->assertEquals($insertedInfo, $getInfo);
    }
    
    public function testCreateSecondaryLanguage()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $user->getNativeLocale()->setLanguageCode("en");
        $user->getNativeLocale()->setCountryCode("IE");
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $locale = new Common\Protobufs\Models\Locale();
        $locale->setLanguageCode("ja");
        $locale->setCountryCode("JP");
        
        $afterCreate = API\DAO\UserDao::createSecondaryLanguage($insertedUser->getId(), $locale);
        $this->assertNotNull($afterCreate);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Locale", $afterCreate);
        $this->assertEquals($locale->getLanguageCode(), $afterCreate->getLanguageCode());
        $this->assertEquals($locale->getCountryCode(), $afterCreate->getCountryCode());
    }
    
    public function testGetSecondaryLanguages()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $user->getNativeLocale()->setLanguageCode("en");
        $user->getNativeLocale()->setCountryCode("IE");
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $locale = new Common\Protobufs\Models\Locale();
        $locale->setLanguageCode("ja");
        $locale->setCountryCode("JP");
        
        $afterCreate = API\DAO\UserDao::createSecondaryLanguage($insertedUser->getId(), $locale);
        $this->assertNotNull($afterCreate);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Locale", $afterCreate);
        $this->assertEquals($locale->getLanguageCode(), $afterCreate->getLanguageCode());
        $this->assertEquals($locale->getCountryCode(), $afterCreate->getCountryCode());
        
        $locale2 = new Common\Protobufs\Models\Locale();
        $locale2->setLanguageCode("ga");
        $locale2->setCountryCode("IE");
        
        $afterCreate2 = API\DAO\UserDao::createSecondaryLanguage($insertedUser->getId(), $locale2);
        $this->assertNotNull($afterCreate2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Locale", $afterCreate2);
        $this->assertEquals($locale2->getLanguageCode(), $afterCreate2->getLanguageCode());
        $this->assertEquals($locale2->getCountryCode(), $afterCreate2->getCountryCode());
        
        $getSecondaryLangs = API\DAO\UserDao::getSecondaryLanguages($insertedUser->getId());
        
        $this->assertCount(2, $getSecondaryLangs);
        foreach ($getSecondaryLangs as $lang) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Locale", $lang);
        }
    }
    
    public function testDeleteSecondaryLanguage()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $user->getNativeLocale()->setLanguageCode("en");
        $user->getNativeLocale()->setCountryCode("IE");
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $locale = new Common\Protobufs\Models\Locale();
        $locale->setLanguageCode("ja");
        $locale->setCountryCode("JP");
        
        $afterCreate = API\DAO\UserDao::createSecondaryLanguage($insertedUser->getId(), $locale);
        $this->assertNotNull($afterCreate);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Locale", $afterCreate);
        $this->assertEquals($locale->getLanguageCode(), $afterCreate->getLanguageCode());
        $this->assertEquals($locale->getCountryCode(), $afterCreate->getCountryCode());
        
        $locale2 = new Common\Protobufs\Models\Locale();
        $locale2->setLanguageCode("ga");
        $locale2->setCountryCode("IE");
        
        $afterCreate2 = API\DAO\UserDao::createSecondaryLanguage($insertedUser->getId(), $locale2);
        $this->assertNotNull($afterCreate2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Locale", $afterCreate2);
        $this->assertEquals($locale2->getLanguageCode(), $afterCreate2->getLanguageCode());
        $this->assertEquals($locale2->getCountryCode(), $afterCreate2->getCountryCode());
        
        $afterDeleteLang = API\DAO\UserDao::deleteSecondaryLanguage($insertedUser->getId(), "ga", "IE");
        //assert that delete worked
        $this->assertEquals("1", $afterDeleteLang);
        $tryRedeleteLang = API\DAO\UserDao::deleteSecondaryLanguage($insertedUser->getId(), "ga", "IE");
        //assert that redelete attempt did nothing
        $this->assertEquals("0", $tryRedeleteLang);
        
        $getLangs = API\DAO\UserDao::getSecondaryLanguages($insertedUser->getId());
        $this->assertNotContains($locale2, $getLangs);
    }
    
    public function testTrackOrganisation()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $orgId = $insertedOrg->getId();
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $trackOrg = API\DAO\UserDao::trackOrganisation($userId, $orgId);
        $this->assertEquals("1", $trackOrg);
        $tryTrackOrgAgain = API\DAO\UserDao::trackOrganisation($userId, $orgId);
        $this->assertEquals("0", $tryTrackOrgAgain);
    }
    
    public function testUnTrackOrganisation()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $orgId = $insertedOrg->getId();
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $trackOrg = API\DAO\UserDao::trackOrganisation($userId, $orgId);
        $this->assertEquals("1", $trackOrg);
        
        $unTrackOrg = API\DAO\UserDao::unTrackOrganisation($userId, $orgId);
        $this->assertEquals("1", $unTrackOrg);
    }
    
    public function testGetTrackedOrganisations()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $orgId = $insertedOrg->getId();
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $org2 = UnitTestHelper::createOrg(null,"Bunnyland");
        $insertedOrg2 = API\DAO\OrganisationDao::insertAndUpdate($org2);
        $orgId2 = $insertedOrg2->getId();
        $this->assertNotNull($insertedOrg2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg2);
        
        $trackOrg = API\DAO\UserDao::trackOrganisation($userId, $orgId);
        $this->assertEquals("1", $trackOrg);
        $trackOrg2 = API\DAO\UserDao::trackOrganisation($userId, $orgId2);
        $this->assertEquals("1", $trackOrg2);
        
        $userTrackedOrgs = API\DAO\UserDao::getTrackedOrganisations($userId);
        $this->assertCount(2, $userTrackedOrgs);
        foreach ($userTrackedOrgs as $trackedOrg) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $trackedOrg);
        }
    }
}
