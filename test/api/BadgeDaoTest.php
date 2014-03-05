<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../api/lib/Notify.class.php';
require_once __DIR__.'/../../Common/lib/BadgeTypes.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../UnitTestHelper.php';

class BadgeDaoTest extends PHPUnit_Framework_TestCase
{
    
    public function testCreateSystemBadge()
    {
        UnitTestHelper::teardownDb();

        $systemBadge = UnitTestHelper::createBadge();
        
        // Success
        $insertedBadge = BadgeDao::insertAndUpdateBadge($systemBadge);
        $this->assertInstanceOf("Badge", $insertedBadge);
        $this->assertNotNull($insertedBadge->getId());
        
        $this->assertEquals($systemBadge->getTitle(), $insertedBadge->getTitle());
        $this->assertEquals($systemBadge->getDescription(), $insertedBadge->getDescription());
        $this->assertEquals($systemBadge->getOwnerId(), $insertedBadge->getOwnerId());
    }
    
    public function testUpdateSystemBadge()
    {
        UnitTestHelper::teardownDb();
        
        $newBadge = UnitTestHelper::createBadge();
     
        $insertedBadge = BadgeDao::insertAndUpdateBadge($newBadge);
        $this->assertInstanceOf("Badge", $insertedBadge);
        $this->assertNotNull($insertedBadge->getId());
        
        $this->assertEquals($newBadge->getTitle(), $insertedBadge->getTitle());
        $this->assertEquals($newBadge->getDescription(), $insertedBadge->getDescription());
        $this->assertEquals($newBadge->getOwnerId(), $insertedBadge->getOwnerId());
        
        $insertedBadge->setTitle("Updated Inserted System Badge");
        $insertedBadge->setDescription("Updated Inserted System Badge Description");
        $insertedBadge->setOwnerId(null);
        
        // Success
        $updatedBadge = BadgeDao::insertAndUpdateBadge($insertedBadge);
        $this->assertInstanceOf("Badge", $updatedBadge);
        $this->assertNotNull($updatedBadge->getId());
        
        $this->assertEquals($updatedBadge->getTitle(), $insertedBadge->getTitle());
        $this->assertEquals($updatedBadge->getDescription(), $insertedBadge->getDescription());
        $this->assertEquals($updatedBadge->getOwnerId(), $insertedBadge->getOwnerId());
    }
    
    public function testGetAllBadges()
    {
        UnitTestHelper::teardownDb();
     
        $allBadges = BadgeDao::getBadge();
        // Success
        $this->assertGreaterThanOrEqual(3, count($allBadges));
        foreach ($allBadges as $badge) {
            $this->assertInstanceOf("Badge", $badge);
        }
    }
    
    public function testGetOrgBadges()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        $orgId = $insertedOrg->getId();
                
        $orgBadge1 = UnitTestHelper::createBadge(null, "Org Badge 1", "Org Badge 1 Description", $orgId);
        $orgBadge2 = UnitTestHelper::createBadge(null, "Org Badge 2", "Org Badge 2 Description", $orgId);
        
        $insertedBadge1 = BadgeDao::insertAndUpdateBadge($orgBadge1);
        $this->assertInstanceOf("Badge", $insertedBadge1);
        $insertedBadge2 = BadgeDao::insertAndUpdateBadge($orgBadge2);
        $this->assertInstanceOf("Badge", $insertedBadge2);

        // Success
        $orgBadges = BadgeDao::getOrgBadges($orgId);
        $this->assertCount(2, $orgBadges);
        foreach ($orgBadges as $badge) {
            $this->assertInstanceOf("Badge", $badge);
        }
        
        $org2 = UnitTestHelper::createOrg(null, "Organisation 2", "Organisation 2 Bio", "http://www.organisation2.org");
        $insertedOrg2 = OrganisationDao::insertAndUpdate($org2);
        $this->assertInstanceOf("Organisation", $insertedOrg2);
        $this->assertNotNull($insertedOrg2->getId());
        $orgId2 = $insertedOrg2->getId();
        
        // Failure
        $orgBadgesFailure = BadgeDao::getOrgBadges($orgId2);
        $this->assertNull($orgBadgesFailure);

    }
    
    public function testAssignBadge()
    {
        UnitTestHelper::teardownDb();
      
        $user = UnitTestHelper::createUser();

        $insertedUser = UserDao::save($user);
           $userID = $insertedUser->getId();

        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        $badge = UnitTestHelper::createBadge();
        $insertedBadge = BadgeDao::insertAndUpdateBadge($badge);
        $this->assertInstanceOf("Badge", $insertedBadge);
        $this->assertNotNull($insertedBadge->getId());
        
        // Success
        $resultAssignBadge = BadgeDao::assignBadge($insertedUser->getId(), $insertedBadge->getId());
        $this->assertEquals("1", $resultAssignBadge);
           $blah = $insertedBadge->getId();
        
        $badge2 = UnitTestHelper::createBadge(null, "Badge 2", "Badge 2 Description", null);
        
        // Failure
        //$resultAssignBadgeFailure = BadgeDao::assignBadge($insertedUser->getId(), $badge2->getId());
        //$this->assertEquals("0", $resultAssignBadgeFailure);
        
    }
    
    public function testRemoveUserBadge()
    {
        UnitTestHelper::teardownDb();
     
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        $userId = $insertedUser->getId();

        $badge = UnitTestHelper::createBadge(null, "Test Remove Badge", "Testing Remove badge", $insertedOrg->getId());
        $insertedBadge = BadgeDao::insertAndUpdateBadge($badge);
        $this->assertInstanceOf("Badge", $insertedBadge);
        $this->assertNotNull($insertedBadge->getId());
        $badgeId = $insertedBadge->getId();

        $resultAssign = BadgeDao::assignBadge($userId, $badgeId);
        $this->assertEquals("1", $resultAssign);
        
        // Success
        $resultRemove = BadgeDao::removeUserBadge($userId, $badgeId);
        $this->assertEquals("1", $resultRemove);
        
        $badge2 = UnitTestHelper::createBadge(null, "Test Remove Badge 2", "Testing Remove badge 2", null);
        $insertedBadge2 = BadgeDao::insertAndUpdateBadge($badge2);
        $this->assertInstanceOf("Badge", $insertedBadge2);
        $this->assertNotNull($insertedBadge2->getId());
        
        // Failure
        $resultRemoveFailure = BadgeDao::removeUserBadge($userId, $insertedBadge2->getId());
        $this->assertEquals("0", $resultRemoveFailure);
    }
    
    public function testDeleteBadge()
    {
        UnitTestHelper::teardownDb();
        
        $badge = UnitTestHelper::createBadge();
        $insertedBadge = BadgeDao::insertAndUpdateBadge($badge);
        $this->assertInstanceOf("Badge", $insertedBadge);
        $this->assertNotNull($insertedBadge->getId());
        
        // Success
        $resultDelete = BadgeDao::deleteBadge($insertedBadge->getId());
        $this->assertEquals("1", $resultDelete);
        
        // Failure
        $resultDeleteFailure = BadgeDao::deleteBadge($insertedBadge->getId());
        $this->assertEquals("0", $resultDeleteFailure);
    }
    
    public function testValidateUserBadge()
    {
        UnitTestHelper::teardownDb();

        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser);
        
        $badge = UnitTestHelper::createBadge();
        $insertedBadge = BadgeDao::insertAndUpdateBadge($badge);
        $this->assertInstanceOf("Badge", $insertedBadge);
        $this->assertNotNull($insertedBadge->getId());
        
        $userAssignedBadge = BadgeDao::assignBadge($insertedUser->getId(), $insertedBadge->getId());
        $this->assertEquals("1", $userAssignedBadge);
        
        // Success
        $resultValidate = BadgeDao::validateUserBadge($insertedUser->getId(), $insertedBadge->getId());
        $this->assertEquals("1", $resultValidate);
        
        $badge2 = UnitTestHelper::createBadge(99, "Badge 2", "Badge 2 Description", null);
        
        // Failure
        $resultValidateFailure = BadgeDao::validateUserBadge($insertedUser->getId(), $badge2->getId());
        $this->assertEquals("0", $resultValidateFailure);
    }
}
