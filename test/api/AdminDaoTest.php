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

        $user = UnitTestHelper::createUser(null, "Bob", "blah"," foo@coo.com");
        $insertedUser = UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($userId);

        AdminDao::addSiteAdmin($userId);
        $admins = AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());
        
        $admins = AdminDao::removeAdmin($userId);
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
        $adminsReturned = AdminDao::getAdmins();
        $this->assertEquals($userId, $adminsReturned[0]->getId()); //Failing, adminsReturned is null
    }

    public function testSaveBannedUser()

    {
        UnitTestHelper::teardownDb();

        $user = UnitTestHelper::createUser(null, "Bob", "blah"," foo@coo.com");
        $insertedUser = UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($userId);
        AdminDao::addSiteAdmin($userId);
        $admins = AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());

        $user2 = UnitTestHelper::createUser(null, "John", "blah"," blah@coo.com");
        $insertedUser2 = UserDao::save($user);
        $user2Id = $insertedUser2->getId();
        $this->assertNotNull($user2Id);

        //Ban not being recorded in the db, why?
        $bannedUser = new BannedUser($user2Id,$userId,BanTypeEnum::WEEK,"b&!!!!!",date("y/m/d"));
        AdminDao::saveBannedUser($bannedUser);
        $this->assertEquals("1",AdminDao::isUserBanned($user2Id));
    }

    public function testIsAdmin()
    {
        UnitTestHelper::teardownDb();

        $user = UnitTestHelper::createUser(null, "John", "blah"," blah@coo.com");
        $insertedUser = UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($userId);
        AdminDao::addSiteAdmin($userId);
        $this->assertEquals("1",AdminDao::isAdmin($userId,null));
    }
}
?>
