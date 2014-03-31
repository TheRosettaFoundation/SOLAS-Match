<?php

namespace SolasMatch\Tests\API;

use \SolasMatch\Tests\UnitTestHelper;
use \SolasMatch\Common as Common;
use \SolasMatch\API as API;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../api/lib/Notify.class.php';
require_once __DIR__.'/../../Common/Enums/BadgeTypes.class.php';
require_once __DIR__.'/../UnitTestHelper.php';

class BadgeDaoTest extends \PHPUnit_Framework_TestCase
{
    //number of badges in the system by default. Correct as of 31st March 2014
    const BADGE_COUNT = 7 ;
    
    /**
     * @covers API\DAO\BadgeDao::insertAndUpdateBadge
     */
    public function testCreateBadge()
    {
        UnitTestHelper::teardownDb();

        $systemBadge = UnitTestHelper::createBadge();
        
        // Success
        $insertedBadge = API\DAO\BadgeDao::insertAndUpdateBadge($systemBadge);
        $this->assertNotNull($insertedBadge);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $insertedBadge);
        
        $this->assertEquals($systemBadge->getTitle(), $insertedBadge->getTitle());
        $this->assertEquals($systemBadge->getDescription(), $insertedBadge->getDescription());
        $this->assertEquals($systemBadge->getOwnerId(), $insertedBadge->getOwnerId());
    }
    
    /**
     * @covers API\DAO\BadgeDao::insertAndUpdateBadge
     */
    public function testUpdateBadge()
    {
        UnitTestHelper::teardownDb();
        
        $newBadge = UnitTestHelper::createBadge();
     
        $insertedBadge = API\DAO\BadgeDao::insertAndUpdateBadge($newBadge);
        $this->assertNotNull($insertedBadge);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $insertedBadge);
        
        $this->assertEquals($newBadge->getTitle(), $insertedBadge->getTitle());
        $this->assertEquals($newBadge->getDescription(), $insertedBadge->getDescription());
        $this->assertEquals($newBadge->getOwnerId(), $insertedBadge->getOwnerId());
        
        $insertedBadge->setTitle("Updated Inserted System Badge");
        $insertedBadge->setDescription("Updated Inserted System Badge Description");
        $insertedBadge->setOwnerId(null);
        
        // Success
        $updatedBadge = API\DAO\BadgeDao::insertAndUpdateBadge($insertedBadge);
        $this->assertNotNull($updatedBadge);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $updatedBadge);
        
        $this->assertEquals($updatedBadge->getTitle(), $insertedBadge->getTitle());
        $this->assertEquals($updatedBadge->getDescription(), $insertedBadge->getDescription());
        $this->assertEquals($updatedBadge->getOwnerId(), $insertedBadge->getOwnerId());
    }
    
    /**
     * @covers API\DAO\BadgeDao::getBadge
     */
    public function testGetBadge()
    {
        UnitTestHelper::teardownDb();
        
        $systemBadge = UnitTestHelper::createBadge();
        
        // Success
        $insertedBadge = API\DAO\BadgeDao::insertAndUpdateBadge($systemBadge);
        $this->assertNotNull($insertedBadge);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $insertedBadge);
        
        $getBadge = API\DAO\BadgeDao::getBadge($insertedBadge->getId());
        $this->assertNotNull($getBadge);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $getBadge);
        $this->assertEquals($insertedBadge, $getBadge);
    }
    
    /**
     * @covers API\DAO\BadgeDao::getBadges
     */
    public function testGetBadges()
    {
        UnitTestHelper::teardownDb();
        
        $systemBadge = UnitTestHelper::createBadge(null, "Polybadge");
        // Success
        $insertedBadge = API\DAO\BadgeDao::insertAndUpdateBadge($systemBadge);
        $this->assertNotNull($insertedBadge);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $insertedBadge);
        
        $badge = UnitTestHelper::createBadge(null, "Polybadge");
        $insertedBadge = API\DAO\BadgeDao::insertAndUpdateBadge($systemBadge);
        $this->assertNotNull($insertedBadge);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $insertedBadge);
        
        $getBadges = API\DAO\BadgeDao::getBadges(null, "Polybadge");
        $this->assertCount(2, $getBadges);
    }
    
    //getAllBadges is not a DAO function, but this function tests trying to get all badges from the DB
    public function testGetAllBadges()
    {
        UnitTestHelper::teardownDb();
     
        $allBadges = API\DAO\BadgeDao::getBadges(null);
        // Success
        //BADGE_COUNT is the number of badges in the system by default.
        //So, the number existing should always be at least this.
        $this->assertGreaterThanOrEqual($this::BADGE_COUNT, count($allBadges));
        foreach ($allBadges as $badge) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $badge);
        }
    }
    
    /**
     * @covers API\DAO\BadgeDao::getOrgBadges
     */
    public function testGetOrgBadges()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $orgId = $insertedOrg->getId();
                
        $orgBadge1 = UnitTestHelper::createBadge(null, "Org Badge 1", "Org Badge 1 Description", $orgId);
        $orgBadge2 = UnitTestHelper::createBadge(null, "Org Badge 2", "Org Badge 2 Description", $orgId);
        
        $insertedBadge1 = API\DAO\BadgeDao::insertAndUpdateBadge($orgBadge1);
        $this->assertNotNull($insertedBadge1);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $insertedBadge1);
        $insertedBadge2 = API\DAO\BadgeDao::insertAndUpdateBadge($orgBadge2);
        $this->assertNotNull($insertedBadge2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $insertedBadge2);

        // Success
        $orgBadges = API\DAO\BadgeDao::getOrgBadges($orgId);
        $this->assertCount(2, $orgBadges);
        foreach ($orgBadges as $badge) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $badge);
        }
        
        $org2 = UnitTestHelper::createOrg(null, "Organisation 2", "Organisation 2 Bio", "http://www.organisation2.org");
        $insertedOrg2 = API\DAO\OrganisationDao::insertAndUpdate($org2);
        $this->assertNotNull($insertedOrg2->getId());
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg2);
        $orgId2 = $insertedOrg2->getId();
        
        // Failure
        $orgBadgesFailure = API\DAO\BadgeDao::getOrgBadges($orgId2);
        $this->assertNull($orgBadgesFailure);
    }
    
    /**
     * @covers API\DAO\BadgeDao::assignBadge
     */
    public function testAssignBadge()
    {
        UnitTestHelper::teardownDb();
      
        $user = UnitTestHelper::createUser();

        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userID = $insertedUser->getId();
        
        $badge = UnitTestHelper::createBadge();
        $insertedBadge = API\DAO\BadgeDao::insertAndUpdateBadge($badge);
        $this->assertNotNull($insertedBadge);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $insertedBadge);
        
        // Success
        $resultAssignBadge = API\DAO\BadgeDao::assignBadge($insertedUser->getId(), $insertedBadge->getId());
        $this->assertEquals("1", $resultAssignBadge);
    }
    
    /**
     * @covers API\DAO\BadgeDao::removeUserBadge
     */
    public function testRemoveUserBadge()
    {
        UnitTestHelper::teardownDb();
     
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();

        $badge = UnitTestHelper::createBadge(null, "Test Remove Badge", "Testing Remove badge", $insertedOrg->getId());
        $insertedBadge = API\DAO\BadgeDao::insertAndUpdateBadge($badge);
        $this->assertNotNull($insertedBadge);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $insertedBadge);
        $badgeId = $insertedBadge->getId();

        $resultAssign = API\DAO\BadgeDao::assignBadge($userId, $badgeId);
        $this->assertEquals("1", $resultAssign);
        
        // Success
        $resultRemove = API\DAO\BadgeDao::removeUserBadge($userId, $badgeId);
        $this->assertEquals("1", $resultRemove);
        
        $badge2 = UnitTestHelper::createBadge(null, "Test Remove Badge 2", "Testing Remove badge 2", null);
        $insertedBadge2 = API\DAO\BadgeDao::insertAndUpdateBadge($badge2);
        $this->assertNotNull($insertedBadge2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $insertedBadge2);
        
        // Failure
        $resultRemoveFailure = API\DAO\BadgeDao::removeUserBadge($userId, $insertedBadge2->getId());
        $this->assertEquals("0", $resultRemoveFailure);
    }
    
    /**
     * @covers API\DAO\BadgeDao::deleteBadge
     */
    public function testDeleteBadge()
    {
        UnitTestHelper::teardownDb();
        
        $badge = UnitTestHelper::createBadge();
        $insertedBadge = API\DAO\BadgeDao::insertAndUpdateBadge($badge);
        $this->assertNotNull($insertedBadge);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $insertedBadge);
        
        // Success
        $resultDelete = API\DAO\BadgeDao::deleteBadge($insertedBadge->getId());
        $this->assertEquals("1", $resultDelete);
        
        // Failure
        $resultDeleteFailure = API\DAO\BadgeDao::deleteBadge($insertedBadge->getId());
        $this->assertEquals("0", $resultDeleteFailure);
    }
    
    /**
     * @covers API\DAO\BadgeDao::validateUserBadge
     */
    public function testValidateUserBadge()
    {
        UnitTestHelper::teardownDb();

        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        
        $badge = UnitTestHelper::createBadge();
        $insertedBadge = API\DAO\BadgeDao::insertAndUpdateBadge($badge);
        $this->assertNotNull($insertedBadge->getId());
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Badge", $insertedBadge);
        
        $userAssignedBadge = API\DAO\BadgeDao::assignBadge($insertedUser->getId(), $insertedBadge->getId());
        $this->assertEquals("1", $userAssignedBadge);
        
        // Success
        $resultValidate = API\DAO\BadgeDao::validateUserBadge($insertedUser->getId(), $insertedBadge->getId());
        $this->assertEquals("1", $resultValidate);
        
        $badge2 = UnitTestHelper::createBadge(null, "Badge 2", "Badge 2 Description", null);
        
        // Failure
        $resultValidateFailure = API\DAO\BadgeDao::validateUserBadge($insertedUser->getId(), $badge2->getId());
        $this->assertEquals("0", $resultValidateFailure);
    }
}
