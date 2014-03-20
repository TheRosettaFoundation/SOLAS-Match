<?php

namespace SolasMatch\Tests\API;

use \SolasMatch\Tests\UnitTestHelper;
use \SolasMatch\API as API;
use \SolasMatch\Common as Common;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../UnitTestHelper.php';


class OrganisationDaoTest extends \PHPUnit_Framework_TestCase
{
    
    public function testInsertOrg()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        
        // Success
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $this->assertEquals($org->getName(), $insertedOrg->getName());
        $this->assertEquals($org->getBiography(), $insertedOrg->getBiography());
        $this->assertEquals($org->getHomePage(), $insertedOrg->getHomePage());
    }
    
    public function testUpdateOrg()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $insertedOrg->setName("Updated Name");
        $insertedOrg->setBiography("Updated Bio");
        $insertedOrg->setHomePage("http://www.updatedhomepage.org");
        
        // Success
        $updatedOrg = API\DAO\OrganisationDao::insertAndUpdate($insertedOrg);
        $this->assertNotNull($updatedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $updatedOrg);
        $this->assertEquals($insertedOrg->getName(), $updatedOrg->getName());
        $this->assertEquals($insertedOrg->getBiography(), $updatedOrg->getBiography());
        $this->assertEquals($insertedOrg->getHomePage(), $updatedOrg->getHomePage());
        
    }
    
    public function testGetOrg()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $orgId = $insertedOrg->getId();
        
        // Success
        $resultFoundOrg = API\DAO\OrganisationDao::getOrg($orgId);
        $this->assertNotNull($resultFoundOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $resultFoundOrg);
        
        // Failure
        $resultFoundOrgFailure = API\DAO\OrganisationDao::getOrg(99);
        $this->assertNull($resultFoundOrgFailure);
        
        // Success
        $resultFoundOrgByName = API\DAO\OrganisationDao::getOrg($orgId, $org->getName());
        $this->assertNotNull($resultFoundOrgByName);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $resultFoundOrgByName);
        
        // Failure
        $resultFoundOrgByNameFailure = API\DAO\OrganisationDao::getOrg($orgId, "x");
        $this->assertNull($resultFoundOrgByNameFailure);
        
        $org2 = UnitTestHelper::createOrg(null, "Organisation 2", "Organisation 2 Bio", "http://www.organisation2.org");
        $insertedOrg2 = API\DAO\OrganisationDao::insertAndUpdate($org2);
        $this->assertNotNull($insertedOrg2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg2);
    }
    
    public function testGetOrgs()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $org2 = UnitTestHelper::createOrg(null, "Bunnyland");
        $insertedOrg2 = API\DAO\OrganisationDao::insertAndUpdate($org2);
        $this->assertNotNull($insertedOrg2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg2);
        
        $getAllOrgs = API\DAO\OrganisationDao::getOrgs();
        $this->assertCount(2, $getAllOrgs);
        foreach ($getAllOrgs as $org) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $org);
        }
        
        //try to get orgs with city "Limerick", returns nothing
        $getLimerickOrgs = API\DAO\OrganisationDao::getOrgs(null, null, null, null, null, null, "Limerick");
        $this->assertNull($getLimerickOrgs);
    }
    
    public function testRequestMembership()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $orgId = $insertedOrg->getId();
     
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
        
        // Success
        $resultRequestMembership = API\DAO\OrganisationDao::requestMembership($userId, $orgId);
        $this->assertEquals("1", $resultRequestMembership);

        //Failure
        $resultRequestMembershipFail = API\DAO\OrganisationDao::requestMembership($userId, $orgId);
        $this->assertEquals("0", $resultRequestMembershipFail);
    }
    
    public function testAcceptMembershipRequest()
    {
        UnitTestHelper::teardownDb();

        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $orgId = $insertedOrg->getId();
   
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
        
        $resultRequestMembership = API\DAO\OrganisationDao::requestMembership($userId, $orgId);
        $this->assertEquals("1", $resultRequestMembership);
                
        // Success
        $resultAcceptMembership = API\DAO\OrganisationDao::acceptMemRequest($orgId, $userId);
        $this->assertEquals("1", $resultAcceptMembership);
        
        $user2 = UnitTestHelper::createUser(null, "User 2", "User 2 Bio", "user2@test.com");
        $insertedUser2 = API\DAO\UserDao::save($user2);
        $this->assertNotNull($insertedUser2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser2);
        $userId2 = $insertedUser2->getId();
        
        //Assert that a user who did not request membership can be added to the org. This is valid, the example
        //use case is an (org) admin adding the user to the org.
        $resultAcceptMembershipNoReq = API\DAO\OrganisationDao::acceptMemRequest($orgId, $userId2);
        $this->assertEquals("1", $resultAcceptMembershipNoReq);
    }
    
    public function testIsMember()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $orgId = $insertedOrg->getId();

        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
      
        // Failure
        $resultIsMemberFailure = API\DAO\OrganisationDao::isMember($orgId, $userId);
        $this->assertEquals("0", $resultIsMemberFailure);
        
        $resultRequestMembership = API\DAO\OrganisationDao::requestMembership($userId, $orgId);
        $this->assertEquals("1", $resultRequestMembership);
        
        $resultAcceptMembership = API\DAO\OrganisationDao::acceptMemRequest($orgId, $userId);
        $this->assertEquals("1", $resultAcceptMembership);
        
        // Success
        $resultIsMember = API\DAO\OrganisationDao::isMember($orgId, $userId);
        $this->assertEquals("1", $resultIsMember);
    }
    
    public function testGetOrgByUser()
    {
        UnitTestHelper::teardownDb();

        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $orgId = $insertedOrg->getId();

        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
        
        $resultRequestMembership = API\DAO\OrganisationDao::requestMembership($userId, $orgId);
        $this->assertEquals("1", $resultRequestMembership);
        
        $resultAcceptMembership = API\DAO\OrganisationDao::acceptMemRequest($orgId, $userId);
        $this->assertEquals("1", $resultAcceptMembership);
        
        // Success
        $resultFoundOrgByUser = API\DAO\OrganisationDao::getOrgByUser($userId);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $resultFoundOrgByUser);
        
        // Failure
        $resultFoundOrgByUserFailure = API\DAO\OrganisationDao::getOrgByUser(999);
        $this->assertNull($resultFoundOrgByUserFailure);
    }
    
    public function testGetOrgMembers()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $orgId = $insertedOrg->getId();
     
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
        
        $resultRequestMembership = API\DAO\OrganisationDao::requestMembership($userId, $orgId);
        $this->assertEquals("1", $resultRequestMembership);
        
        $resultAcceptMembership = API\DAO\OrganisationDao::acceptMemRequest($orgId, $userId);
        $this->assertEquals("1", $resultAcceptMembership);
        
        $user2 = UnitTestHelper::createUser(null, "User 2", "User 2 Bio", "user2@test.com");
        $insertedUser2 = API\DAO\UserDao::save($user2);
        $this->assertNotNull($insertedUser2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser2);
        $userId2 = $insertedUser2->getId();
        
        $resultRequestMembership2 = API\DAO\OrganisationDao::requestMembership($userId2, $orgId);
        $this->assertEquals("1", $resultRequestMembership2);
        
        $resultAcceptMembership2 = API\DAO\OrganisationDao::acceptMemRequest($orgId, $userId2);
        $this->assertEquals("1", $resultAcceptMembership2);
        
        // Success
        $resultOrgMembers = API\DAO\OrganisationDao::getOrgMembers($orgId);
        $this->assertCount(2, $resultOrgMembers);
        foreach ($resultOrgMembers as $member) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $member);
        }
        
        // Failure
        $resultOrgMembersFailure = API\DAO\OrganisationDao::getOrgMembers(999);
        $this->assertNull($resultOrgMembersFailure);
    }
    

    public function testSearchForOrg()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        
        $org2 = UnitTestHelper::createOrg(null, "Organisation 2", "Organisation 2 Bio", "http://www.organisation2.org");
        $insertedOrg2 = API\DAO\OrganisationDao::insertAndUpdate($org2);
        $this->assertNotNull($insertedOrg2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg2);
        
        // Success
        $resultFoundOrgs = API\DAO\OrganisationDao::searchForOrg("organisation");
        $this->assertCount(2, $resultFoundOrgs);
        foreach ($resultFoundOrgs as $foundOrg) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $foundOrg);
        }
        
        // Failure
        $resultFoundOrgsFailure = API\DAO\OrganisationDao::searchForOrg("x");
        $this->assertNull($resultFoundOrgsFailure);
    }
    
    public function testGetMembershipRequests()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $orgId = $insertedOrg->getId();
    
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
        
        $resultRequestMembership = API\DAO\OrganisationDao::requestMembership($userId, $orgId);
        $this->assertEquals("1", $resultRequestMembership);
        
        $user2 = UnitTestHelper::createUser(null, "User 2", "User 2 Bio", "user2@test.com");
        $insertedUser2 = API\DAO\UserDao::save($user2);
        $this->assertNotNull($insertedUser2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser2);
        $userId2 = $insertedUser2->getId();
        
        $resultRequestMembership2 = API\DAO\OrganisationDao::requestMembership($userId2, $orgId);
        $this->assertEquals("1", $resultRequestMembership2);
        
        // Success
        $resultGetMembershipRequests = API\DAO\OrganisationDao::getMembershipRequests($orgId);
        $this->assertCount(2, $resultGetMembershipRequests);
        foreach ($resultGetMembershipRequests as $request) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\MembershipRequest", $request);
        }
        
        // Failure
        $resultGetMembershipRequestsFailure = API\DAO\OrganisationDao::getMembershipRequests(999);
        $this->assertNull($resultGetMembershipRequestsFailure);
    }

    public function testRefuseMembershipRequest()
    {
        UnitTestHelper::teardownDb();

        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $orgId = $insertedOrg->getId();
     
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
        
        $resultRequestMembership = API\DAO\OrganisationDao::requestMembership($userId, $orgId);
        $this->assertEquals("1", $resultRequestMembership);
        
        // Success
        $resultRefuseMembership = API\DAO\OrganisationDao::refuseMemRequest($orgId, $userId);
        $this->assertEquals("1", $resultRefuseMembership);
        
        // Failure
        $resultRefuseMembershipFailure = API\DAO\OrganisationDao::refuseMemRequest($orgId, 999);
        $this->assertEquals("0", $resultRefuseMembershipFailure);
    }
    
    public function testRevokeMembership()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $orgId = $insertedOrg->getId();
   
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
        
        $resultRequestMembership = API\DAO\OrganisationDao::requestMembership($userId, $orgId);
        $this->assertEquals("1", $resultRequestMembership);
        $resultAcceptMembership = API\DAO\OrganisationDao::acceptMemRequest($orgId, $userId);
        $this->assertEquals("1", $resultAcceptMembership);
        
        // Success
        $resultRevokeMembership = API\DAO\OrganisationDao::revokeMembership($orgId, $userId);
        $this->assertEquals("1", $resultRevokeMembership);
        
        // Failure
        $resultRevokeMembershipFailure = API\DAO\OrganisationDao::revokeMembership($orgId, $userId);
        $this->assertEquals("0", $resultRevokeMembershipFailure);
    }
    
    public function testDelete()
    {
        UnitTestHelper::teardownDb();
    
        //create an organisation and save in DB
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $orgId = $insertedOrg->getId();
        
        //delete the organisation that was just added
        $deleteOrg = API\DAO\OrganisationDao::delete($orgId);
        $this->assertEquals("1", $deleteOrg); //successfully deleting an org should return 1

        //try to get the org that was deleted back from DB
        $noOrg = API\DAO\OrganisationDao::getOrg($orgId);
        $this->assertNull($noOrg);

        //try to delete an org that is not in the DB
        $deleteOrg = API\DAO\OrganisationDao::delete($orgId);
        $this->assertEquals("0", $deleteOrg); //failing to delete an org because it is not in DB should return 0
    }
    
    public function testGetUsersTrackingOrg()
    {
        UnitTestHelper::teardownDb();
        
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $orgId = $insertedOrg->getId();
        
        $user = UnitTestHelper::createUser();
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
        
        $user2 = UnitTestHelper::createUser(null, "John Doe", null, "doejohn@com.com");
        $insertedUser2 = API\DAO\UserDao::save($user2);
        $this->assertNotNull($insertedUser2);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser2);
        $user2Id = $insertedUser2->getId();
        
        $trackOrg = API\DAO\UserDao::trackOrganisation($userId, $orgId);
        $this->assertEquals("1", $trackOrg);
        $trackOrg2 = API\DAO\UserDao::trackOrganisation($user2Id, $orgId);
        $this->assertEquals("1", $trackOrg2);
        
        $getTrackingUsers = API\DAO\OrganisationDao::getUsersTrackingOrg($orgId);
        $this->assertCount(2, $getTrackingUsers);
        foreach ($getTrackingUsers as $trackingUser) {
            $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $trackingUser);
        }
    }
}