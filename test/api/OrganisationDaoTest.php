<?php

class OrganisationDaoTest extends PHPUnit_Framework_TestCase
{
    public function testIsMember()
    {
        UnitTestHelper::teardownDb(); 
        
        $orgDao = new OrganisationDao();
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        $orgId = $insertedOrg->getId();

        $userDao = new UserDao();
        $user = UnitTestHelper::createUser();
        $insertedUser = $userDao->save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        $userId = $insertedUser->getUserId();
        
        $resultAcceptMembership = $orgDao->acceptMemRequest($orgId, $userId);
        $this->assertEquals("1", $resultAcceptMembership);
        
        $resultIsMember = $orgDao->isMember($orgId, $userId);
        $this->assertEquals("1", $resultIsMember);
    }
    
    public function testGetOrg()
    {
        UnitTestHelper::teardownDb();
        
        $orgDao = new OrganisationDao();
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->insertAndUpdate($org);        
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        $orgId = $insertedOrg->getId();
        
        $resultFoundOrg = $orgDao->getOrg($orgId, NULL, NULL, NULL);
        $this->assertInstanceOf("Organisation", $resultFoundOrg[0]);
        
        $resultFoundOrgByName = $orgDao->getOrg($orgId, $org->getName(), NULL, NULL);
        $this->assertInstanceOf("Organisation", $resultFoundOrgByName[0]);
        
        $resultFoundOrgByBio = $orgDao->getOrg($orgId, NULL, NULL, $org->getBiography());
        $this->assertInstanceOf("Organisation", $resultFoundOrgByBio[0]);
        
        $resultFoundOrgByHomePage = $orgDao->getOrg($orgId, NULL, $org->getHomePage(), NULL );
        $this->assertInstanceOf("Organisation", $resultFoundOrgByHomePage[0]);
        
        $resultFoundOrgByAll = $orgDao->getOrg($orgId, $org->getName(), $org->getHomePage(), $org->getBiography());
        $this->assertInstanceOf("Organisation", $resultFoundOrgByAll[0]);
        
        $org2 = UnitTestHelper::createOrg(NULL, "Organisation 2", "Organisation 2 Bio", "http://www.organisation2.org");
        $insertedOrg2 = $orgDao->insertAndUpdate($org2);
        $this->assertInstanceOf("Organisation", $insertedOrg2);        
        
        $resultFoundAllOrgs = $orgDao->getOrg(NULL, NULL, NULL, NULL);
        $this->assertCount(2, $resultFoundAllOrgs);
        foreach($resultFoundAllOrgs as $org) {
            $this->assertInstanceOf("Organisation", $org);
        }
    }
    
    public function testGetOrgByUser()
    {
        UnitTestHelper::teardownDb();
        
        $orgDao = new OrganisationDao();
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        $orgId = $insertedOrg->getId();

        $userDao = new UserDao();
        $user = UnitTestHelper::createUser();
        $insertedUser = $userDao->save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        $userId = $insertedUser->getUserId();
        
        $resultAcceptMembership = $orgDao->acceptMemRequest($orgId, $userId);
        $this->assertEquals("1", $resultAcceptMembership);
        
        $resultFoundOrgByUser = $orgDao->getOrgByUser($userId);
        $this->assertInstanceOf("Organisation", $resultFoundOrgByUser);
    }
    
    public function testGetOrgMembers()
    {
        UnitTestHelper::teardownDb();
        
        $orgDao = new OrganisationDao();
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->insertAndUpdate($org);        
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        $orgId = $insertedOrg->getId();
        
        $userDao = new UserDao();       
        $user = UnitTestHelper::createUser();
        $insertedUser = $userDao->save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());
        $userId = $insertedUser->getUserId();
        
        $resultAcceptMembership = $orgDao->acceptMemRequest($orgId, $userId);
        $this->assertEquals("1", $resultAcceptMembership);
        
        $user2 = UnitTestHelper::createUser(NULL, "User 2", "User 2 Bio", "user2@test.com");
        $insertedUser2 = $userDao->save($user2);
        $this->assertInstanceOf("User", $insertedUser2);    
        $this->assertNotNull($insertedUser2->getUserId());
        $userId2 = $insertedUser2->getUserId();
        
        $resultAcceptMembership2 = $orgDao->acceptMemRequest($orgId, $userId2);
        $this->assertEquals("1", $resultAcceptMembership2);        
        
        $resultOrgMembers = $orgDao->getOrgMembers($orgId);
        $this->assertCount(2, $resultOrgMembers);
        foreach($resultOrgMembers as $member) {
            $this->assertInstanceOf("User", $member);
        }
        
        
        
        
    }

    
    
    
}
?>
