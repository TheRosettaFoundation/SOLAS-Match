<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../UnitTestHelper.php';

class BadgeDaoTest extends PHPUnit_Framework_TestCase
{    
    public function testCreateSystemBadge()
    {
        UnitTestHelper::teardownDb();
        
        $badgeDao = new BadgeDao();
        $systemBadge = UnitTestHelper::createBadge();
        
        // Success
        $insertedBadge = $badgeDao->insertAndUpdateBadge($systemBadge);        
        $this->assertInstanceOf("Badge", $insertedBadge);
        $this->assertNotNull($insertedBadge->getId());
        
        $this->assertEquals($systemBadge->getTitle(), $insertedBadge->getTitle());
        $this->assertEquals($systemBadge->getDescription() , $insertedBadge->getDescription());
        $this->assertEquals($systemBadge->getOwnerId(), $insertedBadge->getOwnerId());
    }
    
    public function testUpdateSystemBadge()
    {
        UnitTestHelper::teardownDb(); 
        
        $newBadge = UnitTestHelper::createBadge();
        
        $badgeDao = new BadgeDao();        
        $insertedBadge = $badgeDao->insertAndUpdateBadge($newBadge);        
        $this->assertInstanceOf("Badge", $insertedBadge);
        $this->assertNotNull($insertedBadge->getId());
        
        $this->assertEquals($newBadge->getTitle(), $insertedBadge->getTitle());
        $this->assertEquals($newBadge->getDescription(), $insertedBadge->getDescription());
        $this->assertEquals($newBadge->getOwnerId(), $insertedBadge->getOwnerId());
        
        $insertedBadge->setTitle("Updated Inserted System Badge");
        $insertedBadge->setDescription("Updated Inserted System Badge Description");
        $insertedBadge->setOwnerId(NULL);
        
        // Success
        $updatedBadge = $badgeDao->insertAndUpdateBadge($insertedBadge);
        $this->assertInstanceOf("Badge", $updatedBadge);
        $this->assertNotNull($updatedBadge->getId());
        
        $this->assertEquals($updatedBadge->getTitle(), $insertedBadge->getTitle());
        $this->assertEquals($updatedBadge->getDescription(), $insertedBadge->getDescription());
        $this->assertEquals($updatedBadge->getOwnerId(), $insertedBadge->getOwnerId());
    }
    
    public function testGetAllBadges()
    {
        UnitTestHelper::teardownDb();
        
        $badgeDao = new BadgeDao();        
        $allBadges = $badgeDao->getAllBadges();        
        
        // Success
        $this->assertGreaterThanOrEqual(3, count($allBadges));        
        foreach($allBadges as $badge) {
            $this->assertInstanceOf("Badge", $badge);
        }
    }
    
    public function testGetOrgBadges()
    {
        UnitTestHelper::teardownDb();        
        
        $orgDao = new OrganisationDao();
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        $orgId = $insertedOrg->getId();       
                
        $badgeDao = new BadgeDao();
        $orgBadge1 = UnitTestHelper::createBadge(NULL, "Org Badge 1", "Org Badge 1 Description", $orgId);
        $orgBadge2 = UnitTestHelper::createBadge(NULL, "Org Badge 2", "Org Badge 2 Description", $orgId);
        
        $insertedBadge1 = $badgeDao->insertAndUpdateBadge($orgBadge1);
        $this->assertInstanceOf("Badge", $insertedBadge1);
        $insertedBadge2 = $badgeDao->insertAndUpdateBadge($orgBadge2);
        $this->assertInstanceOf("Badge", $insertedBadge2);

        // Success
        $orgBadges = $badgeDao->getOrgBadges($orgId);        
        $this->assertCount(2, $orgBadges);
        foreach($orgBadges as $badge) {
            $this->assertInstanceOf("Badge", $badge);
        }        
        
        $org2 = UnitTestHelper::createOrg(NULL, "Organisation 2", "Organisation 2 Bio", "http://www.organisation2.org");
        $insertedOrg2 = $orgDao->insertAndUpdate($org2);
        $this->assertInstanceOf("Organisation", $insertedOrg2);
        $this->assertNotNull($insertedOrg2->getId());
        $orgId2 = $insertedOrg2->getId(); 
        
        // Failure
        $orgBadgesFailure = $badgeDao->getOrgBadges($orgId2);
        $this->assertNull($orgBadgesFailure);        

    }
    
    public function testAssignBadge()
    {
        UnitTestHelper::teardownDb();
        
        $userDao = new UserDao();       
        $user = UnitTestHelper::createUser();
        $insertedUser = $userDao->save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        
        $badgeDao = new BadgeDao();
        $badge = UnitTestHelper::createBadge();
        $insertedBadge = $badgeDao->insertAndUpdateBadge($badge);
        $this->assertInstanceOf("Badge", $insertedBadge);
        $this->assertNotNull($insertedBadge->getId());
        
        // Success
        $resultAssignBadge = $badgeDao->assignBadge($insertedUser->getUserId(), $insertedBadge->getId());
        $this->assertEquals("1", $resultAssignBadge);
        
        
        $badge2 = UnitTestHelper::createBadge(NULL, "Badge 2", "Badge 2 Description", NULL);
        
        // Failure
        $resultAssignBadgeFailure = $badgeDao->assignBadge($insertedUser->getUserId(), $badge2->getId());
        $this->assertEquals("0", $resultAssignBadgeFailure);
        
    }
    
    public function testRemoveUserBadge()
    {
        UnitTestHelper::teardownDb();

        $orgDao = new OrganisationDao();        
        $org = UnitTestHelper::createOrg();        
        $insertedOrg = $orgDao->insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        
        $userDao = new UserDao();
        $user = UnitTestHelper::createUser();
        $insertedUser = $userDao->save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        $userId = $insertedUser->getUserId();

        $badgeDao = new BadgeDao();
        $badge = UnitTestHelper::createBadge(NULL, "Test Remove Badge", "Testing Remove badge", $insertedOrg->getId());
        $insertedBadge = $badgeDao->insertAndUpdateBadge($badge);
        $this->assertInstanceOf("Badge", $insertedBadge);
        $this->assertNotNull($insertedBadge->getId());
        $badgeId = $insertedBadge->getId();

        $resultAssign = $badgeDao->assignBadge($userId, $badgeId);
        $this->assertEquals("1", $resultAssign);
        
        // Success
        $resultRemove = $badgeDao->removeUserBadge($userId, $badgeId);
        $this->assertEquals("1", $resultRemove);
        
        $badge2 = UnitTestHelper::createBadge(NULL, "Test Remove Badge 2", "Testing Remove badge 2", NULL);
        $insertedBadge2 = $badgeDao->insertAndUpdateBadge($badge2);
        $this->assertInstanceOf("Badge", $insertedBadge2);
        $this->assertNotNull($insertedBadge2->getId());
        
        // Failure
        $resultRemoveFailure = $badgeDao->removeUserBadge($userId, $insertedBadge2->getId());
        $this->assertEquals("0", $resultRemoveFailure);
    }
    
    public function testDeleteBadge()
    {
        UnitTestHelper::teardownDb();
        $badgeDao = new BadgeDao();
        
        $badge = UnitTestHelper::createBadge();
        $insertedBadge = $badgeDao->insertAndUpdateBadge($badge);
        $this->assertInstanceOf("Badge", $insertedBadge);        
        $this->assertNotNull($insertedBadge->getId());       
        
        // Success
        $resultDelete = $badgeDao->deleteBadge($insertedBadge->getId());
        $this->assertEquals("1", $resultDelete);  
        
        // Failure
        $resultDeleteFailure = $badgeDao->deleteBadge($insertedBadge->getId());
        $this->assertEquals("0", $resultDeleteFailure);
    }
    

}
?>