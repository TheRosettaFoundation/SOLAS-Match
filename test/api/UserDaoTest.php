<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TagsDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TaskDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../Common/lib/BadgeTypes.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../UnitTestHelper.php';

class UserDaoTest extends PHPUnit_Framework_TestCase
{
    public function testCreateUser()
    {
        UnitTestHelper::teardownDb();
        
        // Success
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        $this->assertEquals("testuser@example.com", $insertedUser->getEmail());
        $this->assertNotNull($insertedUser->getPassword());
        $this->assertNotNull($insertedUser->getNonce());
    }
    
    public function testUpdateUser()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        $insertedUser->setDisplayName("Updated DisplayName");
        $insertedUser->setEmail("updatedEmail@test.com");
        $insertedUser->setBiography("Updated Bio");
        
        $locale = new Locale();
        $locale->setLanguageCode("en");
        $locale->setCountryCode("IE");
        $insertedUser->setNativeLocale($locale);

        $insertedUser->setNonce(123456789);
        $insertedUser->setPassword(md5("derpymerpy"));
      
        // Success
        $updatedUser = UserDao::save($insertedUser);

        $this->assertInstanceOf("User", $updatedUser);
        $this->assertEquals($insertedUser->getId(), $updatedUser->getId());
        $this->assertEquals($insertedUser->getDisplayName(), $updatedUser->getDisplayName()); //Failure!!
        $this->assertEquals($insertedUser->getEmail(), $updatedUser->getEmail());
        $this->assertEquals($insertedUser->getBiography(), $updatedUser->getBiography());
        
        $this->assertEquals($insertedUser->getNativeLocale()->getLanguageCode(), $updatedUser->getNativeLocale()->getLanguageCode());
        $this->assertEquals($insertedUser->getNativeLocale()->getCountryCode(), $updatedUser->getNativeLocale()->getCountryCode());
        
        $this->assertEquals($insertedUser->getNonce(), $updatedUser->getNonce());
        $this->assertEquals($insertedUser->getPassword(), $updatedUser->getPassword());
    }
    
    public function testGetUser()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        $insertedUser->setDisplayName("Updated DisplayName");
        $insertedUser->setEmail("updatedEmail@test.com");
        $insertedUser->setBiography("Updated Bio");
        
        $locale = new Locale();
        $locale->setLanguageCode("en");
        $locale->setCountryCode("IE");
        $insertedUser->setNativeLocale($locale);
        
        
        $insertedUser->setNonce(123456789);
        $insertedUser->setPassword(md5("derpymerpy"));      
        $updatedUser = UserDao::save($insertedUser);
        $this->assertInstanceOf("User", $updatedUser);
              
        // Success
        $getUpdatedUser = UserDao::getUser($updatedUser->getId());
        $this->assertInstanceOf("User", $getUpdatedUser[0]);
        $this->assertEquals($insertedUser->getId(), $getUpdatedUser[0]->getId());
        
        $this->assertEquals($insertedUser->getDisplayName(), $getUpdatedUser[0]->getDisplayName());
        $this->assertEquals($insertedUser->getEmail(), $getUpdatedUser[0]->getEmail());
        $this->assertEquals($insertedUser->getBiography(), $getUpdatedUser[0]->getBiography());
        
        $this->assertEquals($insertedUser->getNativeLocale()->getLanguageCode(), $updatedUser->getNativeLocale()->getLanguageCode());
        $this->assertEquals($insertedUser->getNativeLocale()->getCountryCode(), $updatedUser->getNativeLocale()->getCountryCode());
        
        $this->assertEquals($insertedUser->getNonce(), $getUpdatedUser[0]->getNonce());
        $this->assertEquals($insertedUser->getPassword(), $getUpdatedUser[0]->getPassword());   
    }
    
    public function testChangePassword()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        // Success
        $userWithChangedPw = UserDao::changePassword($insertedUser->getId(), "New Password");
        $this->assertInstanceof("User", $userWithChangedPw);
        $this->assertNotEquals($insertedUser->getPassword(), $userWithChangedPw->getPassword());
        $this->assertNotEquals($insertedUser->getNonce(), $userWithChangedPw->getNonce());        
    }
    
    public function testFindOrganisationsUserBelongsTo()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        $resultRequestMembership = OrganisationDao::requestMembership($insertedUser->getId(), $insertedOrg->getId());
        $this->assertEquals("1", $resultRequestMembership);
        
        $resultAcceptMembership = OrganisationDao::acceptMemRequest($insertedOrg->getId(), $insertedUser->getId());
        $this->assertEquals("1", $resultAcceptMembership);
        
        $org2 = UnitTestHelper::createOrg(NULL, "Organisation 2", "Organisation 2 Bio", "http://www.organisation2.org");
        $insertedOrg2 = OrganisationDao::insertAndUpdate($org2);
        $this->assertInstanceOf("Organisation", $insertedOrg2);
        
        
        $resultRequestMembership2 = OrganisationDao::requestMembership($insertedUser->getId(), $insertedOrg2->getId());
        $this->assertEquals("1", $resultRequestMembership2);
        
        $resultAcceptMembership2 = OrganisationDao::acceptMemRequest($insertedOrg2->getId(), $insertedUser->getId());
        $this->assertEquals("1", $resultAcceptMembership2);
        
        // Success
        $userOrgs = UserDao::findOrganisationsUserBelongsTo($insertedUser->getId());
        $this->assertCount(2, $userOrgs);
        foreach($userOrgs as $org) {
            $this->assertInstanceOf("Organisation", $org);
        }
    }
    
    public function testGetUserBadges()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());  
        $userId = $insertedUser->getId();
        
        $userBadges = UserDao::getUserBadges($userId);
        $this->assertNull($userBadges);
        
        $assignBadge = BadgeDao::assignBadge($userId, 3);
        $this->assertEquals(1, $assignBadge);
        
        $userBadges1 = UserDao::getUserBadges($userId);
        $this->assertCount(1, $userBadges1);
        foreach($userBadges1 as $badge) {
            $this->assertInstanceof("Badge", $badge);
        }        
        
        $assignBadge1 = BadgeDao::assignBadge($userId, 4);
        $this->assertEquals(1, $assignBadge1);
        
        $userBadges2 = UserDao::getUserBadges($userId);
        $this->assertCount(2, $userBadges2);
        foreach($userBadges2 as $badge) {
            $this->assertInstanceof("Badge", $badge);
        }        
        
        $assignBadge2 = BadgeDao::assignBadge($userId, 5);
        $this->assertEquals(1, $assignBadge2);
        
        // Success
        $userBadges3 = UserDao::getUserBadges($userId);
        $this->assertCount(3, $userBadges3);
        foreach($userBadges3 as $badge) {
            $this->assertInstanceof("Badge", $badge);
        }        
    }
    
    public function testGetUserTags()
    {
        UnitTestHelper::teardownDb();        
       
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        $userId = $insertedUser->getId();
        
        // Failure
        $noUserTags = UserDao::getUserTags($userId);
        $this->assertNull($noUserTags);
        
        $tag = TagsDao::create("English");
        $this->assertInstanceOf("Tag", $tag);
        $this->assertNotNull($tag->getId());
        
        $tag2 = TagsDao::create("French");
        $this->assertInstanceOf("Tag", $tag2);
        $this->assertNotNull($tag2->getId());
        
        $tagLiked = UserDao::likeTag($userId, $tag->getId());
        $this->assertEquals("1", $tagLiked);
        
        // Success
        $oneUserTag = UserDao::getUserTags($userId);
        $this->assertCount(1, $oneUserTag);
        foreach($oneUserTag as $tag) {
            $this->assertInstanceof("Tag", $tag);
        }
        
        $tag2Liked = UserDao::likeTag($userId, $tag2->getId());
        $this->assertEquals("1", $tag2Liked);
        
        // Success
        $twoUserTags = UserDao::getUserTags($userId);
        $this->assertCount(2, $twoUserTags);
        foreach($twoUserTags as $tag) {
            $this->assertInstanceof("Tag", $tag);
        }

    }
    
    public function testGetUsersWithBadge()
    {
        UnitTestHelper::teardownDb();    
        
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        // Failure
        $noUsersWithBadge = UserDao::getUsersWithBadge(3);
        $this->assertNull($noUsersWithBadge);
        
        $assignBadge = BadgeDao::assignBadge($insertedUser->getId(), 3);        
        $this->assertEquals(1, $assignBadge);
        
        // Success
        $oneUserWithBadge = UserDao::getUsersWithBadge(3);
        $this->assertCount(1, $oneUserWithBadge);
        $this->assertInstanceOf("User", $oneUserWithBadge[0]);
        
        $insertedUser2 = UserDao::create("testuser2@example.com", "testpw2");
        $this->assertInstanceOf("User", $insertedUser2);
        $this->assertNotNull($insertedUser2->getId());        
        
        $assignBadge2 = BadgeDao::assignBadge($insertedUser2->getId(), 3);
        $this->assertEquals(1, $assignBadge2);
        
        // Success
        $twoUsersWithBadge = UserDao::getUsersWithBadge(3);
        $this->assertCount(2, $twoUsersWithBadge);
        foreach($twoUsersWithBadge as $user) {
            $this->assertInstanceOf("User", $user);
        }
    }
    
    public function testLikeTag()
    {
        UnitTestHelper::teardownDb();        
       
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        $userId = $insertedUser->getId();
        
        $tag = TagsDao::create("English");
        $this->assertInstanceOf("Tag", $tag);
        $this->assertNotNull($tag->getId());
       
        // Success
        $tagLiked = UserDao::likeTag($userId, $tag->getId());
        $this->assertEquals("1", $tagLiked);   
        
        // Failure
        $tagLikedFailure = UserDao::likeTag($userId, $tag->getId());
        $this->assertEquals("0", $tagLikedFailure);   
    }
    
    public function testRemoveTag()
    {
        UnitTestHelper::teardownDb();        
       
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        $userId = $insertedUser->getId();
        
        $tag = TagsDao::create("English");
        $this->assertInstanceOf("Tag", $tag);
        $this->assertNotNull($tag->getId());
        
        $tagLiked = UserDao::likeTag($userId, $tag->getId());
        $this->assertEquals("1", $tagLiked);        
        
        // Success
        $removedTag = UserDao::removeTag($userId, $tag->getId());
        $this->assertEquals("1", $removedTag);
        
        // Failure
        $removedTagFailure = UserDao::removeTag($userId, $tag->getId());
        $this->assertEquals("0", $removedTagFailure);        
    }
    
    public function testTrackTask()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        //following line was using TaskDao::create() which is nonexistant
        $insertedTask = TaskDao::save($task);
        $this->assertInstanceOf("Task", $insertedTask);
        $this->assertNotNull($insertedTask->getId());
        
        // Success
        $trackTask = UserDao::trackTask($insertedUser->getId(), $insertedTask->getId());
        $this->assertEquals("1", $trackTask); 
        
        // Failure
        $trackTaskFail = UserDao::trackTask($insertedUser->getId(), $insertedTask->getId());
        $this->assertEquals("0", $trackTaskFail); 
        
    }
    
    public function testIgnoreTask()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        
        $insertedTask = TaskDao::save($task);
        $this->assertInstanceOf("Task", $insertedTask);
        $this->assertNotNull($insertedTask->getId());
        
        $trackTask = UserDao::trackTask($insertedUser->getId(), $insertedTask->getId());
        $this->assertEquals("1", $trackTask); 
        
        // Success
        $ignoreTask = UserDao::ignoreTask($insertedUser->getId(), $insertedTask->getId());
        $this->assertEquals("1", $ignoreTask); 
        
        // Failure
        $ignoreTaskFail = UserDao::ignoreTask($insertedUser->getId(), $insertedTask->getId());
        $this->assertEquals("0", $ignoreTaskFail); 
    }
    
    public function testIsSubscribedToTask()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        
        $insertedTask = TaskDao::save($task);
        $this->assertInstanceOf("Task", $insertedTask);
        $this->assertNotNull($insertedTask->getId());
        
        // Failure
        $isTrackingTaskFail = UserDao::isSubscribedToTask($insertedUser->getId(), $insertedTask->getId());
        $this->assertEquals("0", $isTrackingTaskFail);
        
        $trackTask = UserDao::trackTask($insertedUser->getId(), $insertedTask->getId());
        $this->assertEquals("1", $trackTask);
        
        // Success
        $isTrackingTask = UserDao::isSubscribedToTask($insertedUser->getId(), $insertedTask->getId());
        $this->assertEquals("1", $isTrackingTask);
    }
  //TODO - retest function when issue is resolved
    public function testGetTrackedTasks()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        
        $insertedTask = TaskDao::save($task);
        $this->assertInstanceOf("Task", $insertedTask);
        $this->assertNotNull($insertedTask->getId());
        
        // Failure
        $getNoTrackedTasks = UserDao::getTrackedTasks($insertedUser->getId());
        $this->assertNull($getNoTrackedTasks);
        
        $trackTask = UserDao::trackTask($insertedUser->getId(), $insertedTask->getId());
        $this->assertEquals("1", $trackTask); 
        
        // Success
        $getTrackedTasks = UserDao::getTrackedTasks($insertedUser->getId());
        $this->assertCount(1, $getTrackedTasks);
        $this->assertInstanceOf("Task", $getTrackedTasks[0]);
    }
    
    public function testCreatePasswordResetRequest()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId()); 
        
        // Success
        $createPwResetRequest = UserDao::addPasswordResetRequest("asfjkosagijo".$insertedUser->getId(), $insertedUser->getId());
        $this->assertEquals("1", $createPwResetRequest);
    }
    
    public function testHasRequestedPasswordReset()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        $createPwResetRequest = UserDao::addPasswordResetRequest("asfjkosagijo".$insertedUser->getId(), $insertedUser->getId());
        $this->assertEquals("1", $createPwResetRequest);
        $hasPwResetReq = UserDao::hasRequestedPasswordReset($insertedUser->getEmail());
        $this->assertTrue($hasPwResetReq);
    }
    
    public function testRemovePasswordResetRequest()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId()); 
        
        $createPwResetRequest = UserDao::addPasswordResetRequest("asfjkosagijo".$insertedUser->getId(), $insertedUser->getId());
        $this->assertEquals("1", $createPwResetRequest);  
        
        // Success
        $removePwResetRequest = UserDao::removePasswordResetRequest($insertedUser->getId());
        $this->assertEquals("1", $removePwResetRequest); 
        
        // Failure
        $removePwResetRequestFail = UserDao::removePasswordResetRequest($insertedUser->getId());
        $this->assertEquals("0", $removePwResetRequestFail); 
    }
    
    public function testGetPasswordResetRequests()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId()); 
        
        $createPwResetRequest = UserDao::addPasswordResetRequest
                                ("asfjkosagijo".$insertedUser->getId(),
                                 $insertedUser->getId()
                                );
        $this->assertEquals("1", $createPwResetRequest);
        
        // Success        
        $passwordResetRequest = UserDao::getPasswordResetRequests
                                ($insertedUser->getEmail(),
                                "asfjkosagijo".$insertedUser->getId()
                                );
        $this->assertInstanceOf("PasswordResetRequest", $passwordResetRequest);        
        
        $removePwResetRequest = UserDao::removePasswordResetRequest($insertedUser->getId());
        $this->assertEquals("1", $removePwResetRequest); 
        
        // Failure
        $passwordResetRequestFailure = UserDao::getPasswordResetRequests($insertedUser->getId());
        $this->assertNull($passwordResetRequestFailure);
    }
    
    public function testTrackProject()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        // Failure
        $trackProjectFailure = UserDao::trackProject(999, $insertedUser->getId());
        $this->assertNull($trackProjectFailure);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);   
        $this->assertNotNull($insertedProject->getId());   
        
        // Success
        $trackProject = UserDao::trackProject($insertedProject->getId(), $insertedUser->getId());
        $this->assertEquals("1", $trackProject);
    }
    
    public function testUnTrackProject()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);   
        $this->assertNotNull($insertedProject->getId());  
        
        // Failure
        $untrackProjectFailure = UserDao::unTrackProject($insertedProject->getId(), $insertedUser->getId());
        $this->assertEquals("0", $untrackProjectFailure);
        
        $trackProject = UserDao::trackProject($insertedProject->getId(), $insertedUser->getId());
        $this->assertEquals("1", $trackProject);
        
        // Success
        $untrackProject = UserDao::unTrackProject($insertedProject->getId(), $insertedUser->getId());
        $this->assertEquals("1", $untrackProject);
    }
    
    public function testGetTrackedProjects()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);   
        $this->assertNotNull($insertedProject->getId()); 
        
        $project2 = UnitTestHelper::createProject($insertedOrg->getId(), null, "Project 2 Title", "Project 2 Description");
        $insertedProject2 = ProjectDao::createUpdate($project2);
        $this->assertInstanceOf("Project", $insertedProject2);   
        $this->assertNotNull($insertedProject2->getId()); 
        
        $trackProject = UserDao::trackProject($insertedProject->getId(), $insertedUser->getId());
        $this->assertEquals("1", $trackProject);
        
        $trackProject2 = UserDao::trackProject($insertedProject2->getId(), $insertedUser->getId());
        $this->assertEquals("1", $trackProject2);
        
        // Success
        $trackedProjects = UserDao::getTrackedProjects($insertedUser->getId());
        $this->assertCount(2, $trackedProjects);
        foreach($trackedProjects as $project) {
            $this->assertInstanceOf("Project", $project);
        }
    }
    
    
    public function testIsSubscribedToProject()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);   
        $this->assertNotNull($insertedProject->getId()); 
        
        // Failure
        $isSubscribedToProjectFailure = UserDao::isSubscribedToProject
                                        ($insertedUser->getId(),
                                         $insertedProject->getId()
                                        );
        $this->assertEquals("0", $isSubscribedToProjectFailure);
        
        $trackProject = UserDao::trackProject($insertedProject->getId(), $insertedUser->getId());
        $this->assertEquals("1", $trackProject);
        
        // Success
        $isSubscribedToProject = UserDao::isSubscribedToProject($insertedUser->getId(), $insertedProject->getId());
        $this->assertEquals("1", $isSubscribedToProject);
    }
    
    public function testGetUserTaskStreamNotification()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);
        $this->assertNotNull($insertedProject->getId());
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        $insertedTask = TaskDao::save($task);
        $this->assertInstanceOf("Task", $insertedTask);
        $this->assertNotNull($insertedTask->getId());
    }
}
