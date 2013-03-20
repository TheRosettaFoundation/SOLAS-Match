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
        
        $badge = UnitTestHelper::createBadge();
        
        $badgeDao = new BadgeDao();        
        $insertedBadge = $badgeDao->insertAndUpdateBadge($badge);        
        $this->assertInstanceOf("Badge", $insertedBadge);
        $this->assertNotNull($insertedBadge->getId());
        
        $this->assertEquals($badge->getTitle(), $insertedBadge->getTitle());
        $this->assertEquals($badge->getDescription() , $insertedBadge->getDescription());
        $this->assertEquals($badge->getOwnerId(), $insertedBadge->getOwnerId());
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

        $orgBadges = $badgeDao->getOrgBadges($orgId);        
        $this->assertCount(2, $orgBadges);
        foreach($orgBadges as $badge) {
            $this->assertInstanceOf("Badge", $badge);
        }        
    }
    
    public function testAssignBadge()
    {
        UnitTestHelper::teardownDb();
        
        $userDao = new UserDao();       
        $user = UnitTestHelper::createUser();
        $insertedUser = $userDao->save($user);
        $this->assertInstanceOf("User", $insertedUser);
        
        $badgeDao = new BadgeDao();
        $badge = UnitTestHelper::createBadge();
        $insertedBadge = $badgeDao->insertAndUpdateBadge($badge);
        $this->assertInstanceOf("Badge", $insertedBadge);
        
        $result = $badgeDao->assignBadge($insertedUser, $insertedBadge);
        $this->assertEquals("1", $result);
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

        $badgeDao = new BadgeDao();
        $badge = UnitTestHelper::createBadge(NULL, "Test Remove Badge", "Testing Remove badge", $insertedOrg->getId());
        $insertedBadge = $badgeDao->insertAndUpdateBadge($badge);
        $this->assertInstanceOf("Badge", $insertedBadge);

        $resultAssign = $badgeDao->assignBadge($insertedUser, $insertedBadge);
        $this->assertEquals("1", $resultAssign);
        
        $resultRemove = $badgeDao->removeUserBadge($insertedUser, $insertedBadge);
        $this->assertEquals("1", $resultRemove);
    }
    
    public function testDeleteBadge()
    {
        UnitTestHelper::teardownDb();
        $badgeDao = new BadgeDao();
        
        $badge = UnitTestHelper::createBadge();
        $insertedBadge = $badgeDao->insertAndUpdateBadge($badge);
        $this->assertInstanceOf("Badge", $insertedBadge);
        
        $this->assertNotNull($insertedBadge->getId());
        $resultDelete = $badgeDao->deleteBadge($insertedBadge->getId());
        $this->assertEquals("1", $resultDelete);               
    }
    

}
?>