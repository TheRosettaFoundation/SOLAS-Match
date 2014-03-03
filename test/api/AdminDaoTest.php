<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/AdminDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../../Common/BanTypeEnum.php';
require_once __DIR__.'/../../Common/models/BannedUser.php';
require_once __DIR__.'/../UnitTestHelper.php';

class AdminDaoTest extends PHPUnit_Framework_TestCase
{

    public function testAddSiteAdmin()
    {
        UnitTestHelper::teardownDb();

        $user = UnitTestHelper::createUser(null, "Bob", "blah", "foo@coo.com");
        $insertedUser = UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($userId);

        AdminDao::addSiteAdmin($userId);
        $admins = AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());
    }
    
    public function testRemoveAdmin()
    {
        UnitTestHelper::teardownDb();

        $user = UnitTestHelper::createUser(null, "Bob", "blah", " foo@coo.com");
        $insertedUser = UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($userId);

        AdminDao::addSiteAdmin($userId);
        $admins = AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());
        
        AdminDao::removeAdmin($userId);
        $admins = AdminDao::getAdmins();
        $this->assertNull($admins);
    }

    public function testAddOrgAdmin()
    {
        UnitTestHelper::teardownDb();

        $user = UnitTestHelper::createUser(null, "Bob", "blah", "foo@coo.com");
        $insertedUser = UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($userId);

        $org = UnitTestHelper::createOrg(null, "Bunnyland");
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $orgId = $insertedOrg->getId();
        $this->assertNotNull($orgId);
        OrganisationDao::requestMembership($userId, $orgId);
        OrganisationDao::acceptMemRequest($orgId, $userId);

        AdminDao::addOrgAdmin($userId, $orgId);
        $adminsReturned = AdminDao::getAdmins($orgId);
        $this->assertEquals($userId, $adminsReturned[0]->getId());
    }
    
    public function testRemoveOrgAdmin()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser(null, "Bob", "blah", "foo@coo.com");
        $insertedUser = UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($userId);
        
        $org = UnitTestHelper::createOrg(null, "Bunnyland");
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $orgId = $insertedOrg->getId();
        $this->assertNotNull($orgId);
        OrganisationDao::requestMembership($userId, $orgId);
        OrganisationDao::acceptMemRequest($orgId, $userId);
        
        AdminDao::addOrgAdmin($userId, $orgId);
        $adminsReturned = AdminDao::getAdmins($orgId);
        $this->assertEquals($userId, $adminsReturned[0]->getId());
        
        AdminDao::removeOrgAdmin($userId, $orgId);
        $admins = AdminDao::getAdmins($orgId);
        $this->assertNull($admins);
    }

    public function testSaveBannedUser()
    {
        UnitTestHelper::teardownDb();

        $user = UnitTestHelper::createUser(null, "Bob", "blah", " foo@coo.com");
        $insertedUser = UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($userId);
        AdminDao::addSiteAdmin($userId);
        $admins = AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());

        $user2 = UnitTestHelper::createUser(null, "John", "blah", " blah@coo.com");
        $insertedUser2 = UserDao::save($user);
        $user2Id = $insertedUser2->getId();
        $this->assertNotNull($user2Id);

        //Ban the user
        $bannedUser = UnitTestHelper::createBannedUser($user2Id, $userId, BanTypeEnum::WEEK, "b&!!!!");
        AdminDao::saveBannedUser($bannedUser);
        $isBanned = AdminDao::isUserBanned($user2Id);
        $this->assertEquals("1", $isBanned);
    }
    
    public function testGetBannedUser()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser(null, "Bob", "blah", " foo@coo.com");
        $insertedUser = UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($userId);
        AdminDao::addSiteAdmin($userId);
        $admins = AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());
        
        $user2 = UnitTestHelper::createUser(null, "John", "blah", " blah@coo.com");
        $insertedUser2 = UserDao::save($user);
        $user2Id = $insertedUser2->getId();
        $this->assertNotNull($user2Id);
        
        $bannedUser = UnitTestHelper::createBannedUser($user2Id, $userId, BanTypeEnum::WEEK, "b&!!!!");
        AdminDao::saveBannedUser($bannedUser);
        $isBanned = AdminDao::isUserBanned($user2Id);
        $this->assertEquals("1", $isBanned);
        
        $getBannedUser = AdminDao::getBannedUser($user2Id, $userId);
        $this->assertInstanceOf("BannedUser", $getBannedUser[0]);
        $this->assertEquals($bannedUser->getUserId(), $getBannedUser[0]->getUserId());
        $this->assertEquals($bannedUser->getUserIdAdmin(), $getBannedUser[0]->getUserIdAdmin());
        $this->assertEquals($bannedUser->getBanType(), $getBannedUser[0]->getBanType());
        $this->assertEquals($bannedUser->getComment(), $getBannedUser[0]->getComment());
    }
    
    public function testUnBanUser()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser(null, "Bob", "blah", " foo@coo.com");
        $insertedUser = UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($userId);
        AdminDao::addSiteAdmin($userId);
        $admins = AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());
        
        $user2 = UnitTestHelper::createUser(null, "John", "blah", " blah@coo.com");
        $insertedUser2 = UserDao::save($user);
        $user2Id = $insertedUser2->getId();
        $this->assertNotNull($user2Id);
        
        //Ban the user
        $bannedUser = UnitTestHelper::createBannedUser($user2Id, $userId, BanTypeEnum::WEEK, "b&!!!!");
        AdminDao::saveBannedUser($bannedUser);
        $isBanned = AdminDao::isUserBanned($user2Id);
        $this->assertEquals("1", $isBanned);
        
        AdminDao::unBanUser($user2Id);
        $isBanned = AdminDao::isUserBanned($user2Id);
        $this->assertEquals("0", $isBanned);
    }
    
    public function testSaveBannedOrg()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser(null, "Bob", "blah", "foo@coo.com");
        $insertedUser = UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($userId);
        
        AdminDao::addSiteAdmin($userId);
        $admins = AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());
        
        $org = UnitTestHelper::createOrg(null, "Bunnyland");
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $orgId = $insertedOrg->getId();
        $this->assertNotNull($orgId);
        
        $bannedOrg = UnitTestHelper::createBannedOrg($orgId, $userId);
        AdminDao::saveBannedOrg($bannedOrg);
        $isBanned = AdminDao::isOrgBanned($orgId);
        $this->assertEquals("1", $isBanned);
    }
    
    public function testGetBannedOrg()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser(null, "Bob", "blah", "foo@coo.com");
        $insertedUser = UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($userId);
        
        AdminDao::addSiteAdmin($userId);
        $admins = AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());
        
        $org = UnitTestHelper::createOrg(null, "Bunnyland");
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $orgId = $insertedOrg->getId();
        $this->assertNotNull($orgId);
        
        $bannedOrg = UnitTestHelper::createBannedOrg($orgId, $userId);
        AdminDao::saveBannedOrg($bannedOrg);
        $isBanned = AdminDao::isOrgBanned($orgId);
        $this->assertEquals("1", $isBanned);
        
        $getBannedOrg = AdminDao::getBannedOrg($orgId, $userId);
        $this->assertInstanceOf("BannedOrganisation", $getBannedOrg[0]);
        $this->assertEquals($bannedOrg->getOrgId(), $getBannedOrg[0]->getOrgId());
        $this->assertEquals($bannedOrg->getUserIdAdmin(), $getBannedOrg[0]->getUserIdAdmin());
        $this->assertEquals($bannedOrg->getBanType(), $getBannedOrg[0]->getBanType());
        $this->assertEquals($bannedOrg->getComment(), $getBannedOrg[0]->getComment());
    }
    
    public function testUnBanOrg()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser(null, "Bob", "blah", "foo@coo.com");
        $insertedUser = UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($userId);
        
        AdminDao::addSiteAdmin($userId);
        $admins = AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());
        
        $org = UnitTestHelper::createOrg(null, "Bunnyland");
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $orgId = $insertedOrg->getId();
        $this->assertNotNull($orgId);
        
        $bannedOrg = UnitTestHelper::createBannedOrg($orgId, $userId);
        AdminDao::saveBannedOrg($bannedOrg);
        $isBanned = AdminDao::isOrgBanned($orgId);
        $this->assertEquals("1", $isBanned);
        
        AdminDao::unBanOrg($orgId);
        $isBanned = AdminDao::isOrgBanned($orgId);
        $this->assertEquals("0", $isBanned);
    }
    
    public function testIsAdmin()
    {
        UnitTestHelper::teardownDb();

        $user = UnitTestHelper::createUser(null, "John", "blah", " blah@coo.com");
        $insertedUser = UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($userId);
        AdminDao::addSiteAdmin($userId);
        $this->assertEquals("1", AdminDao::isAdmin($userId, null));
    }
}
