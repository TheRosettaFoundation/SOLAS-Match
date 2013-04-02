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
        $this->assertNotNull($insertedUser->getUserId());
        $this->assertEquals("testuser@example.com", $insertedUser->getEmail());
        $this->assertNotNull($insertedUser->getPassword());
        $this->assertNotNull($insertedUser->getNonce());
    }
    
    public function testUpdateUser()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        
        $newUserWithUpdates = new User();
        $newUserWithUpdates->setUserId($insertedUser->getUserId());
        $newUserWithUpdates->setDisplayName("Updated DisplayName");
        $newUserWithUpdates->setEmail("updatedEmail@test.com");
        $newUserWithUpdates->setBiography("Updated Bio");
        $newUserWithUpdates->setNativeLangId("en");
        $newUserWithUpdates->setNativeRegionId("IE");
        $newUserWithUpdates->setNonce(123456789);
        $newUserWithUpdates->setPassword(md5("derpymerpy"));
              
        // Success
        $updatedUser = UserDao::save($newUserWithUpdates);
        $this->assertInstanceOf("User", $updatedUser);
        $this->assertEquals($newUserWithUpdates->getUserId(), $updatedUser->getUserId());
        $this->assertEquals($newUserWithUpdates->getDisplayName(), $updatedUser->getDisplayName());
        $this->assertEquals($newUserWithUpdates->getEmail(), $updatedUser->getEmail());
        $this->assertEquals($newUserWithUpdates->getBiography(), $updatedUser->getBiography());
        $this->assertEquals($newUserWithUpdates->getNativeLangId(), $updatedUser->getNativeLangId());
        $this->assertEquals($newUserWithUpdates->getNativeRegionId(), $updatedUser->getNativeRegionId());
        $this->assertEquals($newUserWithUpdates->getNonce(), $updatedUser->getNonce());
        $this->assertEquals($newUserWithUpdates->getPassword(), $updatedUser->getPassword());
    }
    
    public function testGetUser()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        
        $newUserWithUpdates = new User();
        $newUserWithUpdates->setUserId($insertedUser->getUserId());
        $newUserWithUpdates->setDisplayName("Updated DisplayName");
        $newUserWithUpdates->setEmail("updatedEmail@test.com");
        $newUserWithUpdates->setBiography("Updated Bio");
        $newUserWithUpdates->setNativeLangId("en");
        $newUserWithUpdates->setNativeRegionId("IE");
        $newUserWithUpdates->setNonce(123456789);
        $newUserWithUpdates->setPassword(md5("derpymerpy"));
        
        $updatedUser = UserDao::save($newUserWithUpdates);
        $this->assertInstanceOf("User", $updatedUser);
              
        // Success
        $getUpdatedUser = UserDao::getUser($updatedUser->getUserId());
        $this->assertInstanceOf("User", $getUpdatedUser[0]);
        $this->assertEquals($newUserWithUpdates->getUserId(), $getUpdatedUser[0]->getUserId());
        $this->assertEquals($newUserWithUpdates->getDisplayName(), $getUpdatedUser[0]->getDisplayName());
        $this->assertEquals($newUserWithUpdates->getEmail(), $getUpdatedUser[0]->getEmail());
        $this->assertEquals($newUserWithUpdates->getBiography(), $getUpdatedUser[0]->getBiography());
        $this->assertEquals($newUserWithUpdates->getNativeLangId(), $getUpdatedUser[0]->getNativeLangId());
        $this->assertEquals($newUserWithUpdates->getNativeRegionId(), $getUpdatedUser[0]->getNativeRegionId());
        $this->assertEquals($newUserWithUpdates->getNonce(), $getUpdatedUser[0]->getNonce());
        $this->assertEquals($newUserWithUpdates->getPassword(), $getUpdatedUser[0]->getPassword());   
    }
    
    public function testChangePassword()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        
        // Success
        $userWithChangedPw = UserDao::changePassword($insertedUser->getUserId(), "New Password");
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
        $this->assertNotNull($insertedUser->getUserId());
        
        $resultRequestMembership = OrganisationDao::requestMembership($insertedUser->getUserId(), $insertedOrg->getId());
        $this->assertEquals("1", $resultRequestMembership);
        
        $resultAcceptMembership = OrganisationDao::acceptMemRequest($insertedOrg->getId(), $insertedUser->getUserId());
        $this->assertEquals("1", $resultAcceptMembership);
        
        $org2 = UnitTestHelper::createOrg(NULL, "Organisation 2", "Organisation 2 Bio", "http://www.organisation2.org");
        $insertedOrg2 = OrganisationDao::insertAndUpdate($org2);
        $this->assertInstanceOf("Organisation", $insertedOrg2);
        
        
        $resultRequestMembership2 = OrganisationDao::requestMembership($insertedUser->getUserId(), $insertedOrg2->getId());
        $this->assertEquals("1", $resultRequestMembership2);
        
        $resultAcceptMembership2 = OrganisationDao::acceptMemRequest($insertedOrg2->getId(), $insertedUser->getUserId());
        $this->assertEquals("1", $resultAcceptMembership2);
        
        // Success
        $userOrgs = UserDao::findOrganisationsUserBelongsTo($insertedUser->getUserId());
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
        $this->assertNotNull($insertedUser->getUserId());  
        $userId = $insertedUser->getUserId();
        
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
        $this->assertNotNull($insertedUser->getUserId());
        $userId = $insertedUser->getUserId();
        
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
        $this->assertNotNull($insertedUser->getUserId());
        
        // Failure
        $noUsersWithBadge = UserDao::getUsersWithBadge(3);
        $this->assertNull($noUsersWithBadge);
        
        $assignBadge = BadgeDao::assignBadge($insertedUser->getUserId(), 3);        
        $this->assertEquals(1, $assignBadge);
        
        // Success
        $oneUserWithBadge = UserDao::getUsersWithBadge(3);
        $this->assertCount(1, $oneUserWithBadge);
        $this->assertInstanceOf("User", $oneUserWithBadge[0]);
        
        $insertedUser2 = UserDao::create("testuser2@example.com", "testpw2");
        $this->assertInstanceOf("User", $insertedUser2);
        $this->assertNotNull($insertedUser2->getUserId());        
        
        $assignBadge2 = BadgeDao::assignBadge($insertedUser2->getUserId(), 3);
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
        $this->assertNotNull($insertedUser->getUserId());
        $userId = $insertedUser->getUserId();
        
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
        $this->assertNotNull($insertedUser->getUserId());
        $userId = $insertedUser->getUserId();
        
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
        $this->assertNotNull($insertedUser->getUserId());
        
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
        
        // Success
        $trackTask = UserDao::trackTask($insertedUser->getUserId(), $insertedTask->getId());
        $this->assertEquals("1", $trackTask); 
        
        // Failure
        $trackTaskFail = UserDao::trackTask($insertedUser->getUserId(), $insertedTask->getId());
        $this->assertEquals("0", $trackTaskFail); 
        
    }
    
    public function testIgnoreTask()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        
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
        
        $trackTask = UserDao::trackTask($insertedUser->getUserId(), $insertedTask->getId());
        $this->assertEquals("1", $trackTask); 
        
        // Success
        $ignoreTask = UserDao::ignoreTask($insertedUser->getUserId(), $insertedTask->getId());
        $this->assertEquals("1", $ignoreTask); 
        
        // Failure
        $ignoreTaskFail = UserDao::ignoreTask($insertedUser->getUserId(), $insertedTask->getId());
        $this->assertEquals("0", $ignoreTaskFail); 
    }
    
    public function testIsSubscribedToTask()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        
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
        
        // Failure
        $isTrackingTaskFail = UserDao::isSubscribedToTask($insertedUser->getUserId(), $insertedTask->getId());
        $this->assertEquals("0", $isTrackingTaskFail);
        
        $trackTask = UserDao::trackTask($insertedUser->getUserId(), $insertedTask->getId());
        $this->assertEquals("1", $trackTask);
        
        // Success
        $isTrackingTask = UserDao::isSubscribedToTask($insertedUser->getUserId(), $insertedTask->getId());
        $this->assertEquals("1", $isTrackingTask);
    }
    
    public function testGetTrackedTasks()
    {
        UnitTestHelper::teardownDb();
        
        $insertedUser = UserDao::create("testuser@example.com", "testpw");
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        
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
        
        // Failure
        $getNoTrackedTasks = UserDao::getTrackedTasks($insertedUser->getUserId());
        $this->assertNull($getNoTrackedTasks);
        
        $trackTask = UserDao::trackTask($insertedUser->getUserId(), $insertedTask->getId());
        $this->assertEquals("1", $trackTask); 
        
        // Success
        $getTrackedTasks = UserDao::getTrackedTasks($insertedUser->getUserId());
        $this->assertCount(1, $getTrackedTasks);
        $this->assertInstanceOf("Task", $getTrackedTasks[0]);
    }
    
    public function testCreatePasswordResetRequest()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId()); 
        
        // Success
        $createPwResetRequest = UserDao::createPasswordReset($insertedUser->getUserId());
        $this->assertEquals("1", $createPwResetRequest);
    }
    
    public function testRemovePasswordResetRequest()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId()); 
        
        $createPwResetRequest = UserDao::createPasswordReset($insertedUser->getUserId());
        $this->assertEquals("1", $createPwResetRequest);  
        
        // Success
        $removePwResetRequest = UserDao::removePasswordResetRequest($insertedUser->getUserId());
        $this->assertEquals("1", $removePwResetRequest); 
        
        // Failure
        $removePwResetRequestFail = UserDao::removePasswordResetRequest($insertedUser->getUserId());
        $this->assertEquals("0", $removePwResetRequestFail); 
    }
    
    public function testGetPasswordResetRequests()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId()); 
        
        $createPwResetRequest = UserDao::createPasswordReset($insertedUser->getUserId());
        $this->assertEquals("1", $createPwResetRequest);
        
        // Success        
        $passwordResetRequest = UserDao::getPasswordResetRequests($insertedUser->getUserId());
        $this->assertInstanceOf("PasswordResetRequest", $passwordResetRequest);        
        
        $removePwResetRequest = UserDao::removePasswordResetRequest($insertedUser->getUserId());
        $this->assertEquals("1", $removePwResetRequest); 
        
        // Failure
        $passwordResetRequestFailure = UserDao::getPasswordResetRequests($insertedUser->getUserId());
        $this->assertNull($passwordResetRequestFailure);
    }
    
    public function testTrackProject()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        // Failure
        $trackProjectFailure = UserDao::trackProject(999, $insertedUser->getUserId());
        $this->assertNull($trackProjectFailure);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);   
        $this->assertNotNull($insertedProject->getId());   
        
        // Success
        $trackProject = UserDao::trackProject($insertedProject->getId(), $insertedUser->getUserId());
        $this->assertEquals("1", $trackProject);
    }
    
    public function testUnTrackProject()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);   
        $this->assertNotNull($insertedProject->getId());  
        
        // Failure
        $untrackProjectFailure = UserDao::unTrackProject($insertedProject->getId(), $insertedUser->getUserId());
        $this->assertEquals("0", $untrackProjectFailure);
        
        $trackProject = UserDao::trackProject($insertedProject->getId(), $insertedUser->getUserId());
        $this->assertEquals("1", $trackProject);
        
        // Success
        $untrackProject = UserDao::unTrackProject($insertedProject->getId(), $insertedUser->getUserId());
        $this->assertEquals("1", $untrackProject);
    }
    
    public function testGetTrackedProjects()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        
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
        
        $trackProject = UserDao::trackProject($insertedProject->getId(), $insertedUser->getUserId());
        $this->assertEquals("1", $trackProject);
        
        $trackProject2 = UserDao::trackProject($insertedProject2->getId(), $insertedUser->getUserId());
        $this->assertEquals("1", $trackProject2);
        
        // Success
        $trackedProjects = UserDao::getTrackedProjects($insertedUser->getUserId());
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
        $this->assertNotNull($insertedUser->getUserId());
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);   
        $this->assertNotNull($insertedProject->getId()); 
        
        // Failure
        $isSubscribedToProjectFailure = UserDao::isSubscribedToProject($insertedUser->getUserId(), $insertedProject->getId());
        $this->assertEquals("0", $isSubscribedToProjectFailure);
        
        $trackProject = UserDao::trackProject($insertedProject->getId(), $insertedUser->getUserId());
        $this->assertEquals("1", $trackProject);
        
        // Success
        $isSubscribedToProject = UserDao::isSubscribedToProject($insertedUser->getUserId(), $insertedProject->getId());
        $this->assertEquals("1", $isSubscribedToProject);
    }
}

?>
