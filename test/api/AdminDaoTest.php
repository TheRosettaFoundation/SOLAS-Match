<?php

namespace SolasMatch\Tests\API;

use \SolasMatch\Tests\UnitTestHelper;
use \SolasMatch\API as API;
use \SolasMatch\Common as Common;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';

\DrSlump\Protobuf::autoload();

require_once __DIR__.'/../../api/DataAccessObjects/AdminDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../Common/Enums/BanTypeEnum.class.php';
require_once __DIR__.'/../../Common/protobufs/models/BannedUser.php';
require_once __DIR__.'/../UnitTestHelper.php';

class AdminDaoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers API\DAO\AdminDao::addSiteAdmin
     */
    public function testAddSiteAdmin()
    {
        UnitTestHelper::teardownDb();

        $user = UnitTestHelper::createUser(null, "Bob", "blah", "foo@coo.com");
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();

        API\DAO\AdminDao::addSiteAdmin($userId);
        $admins = API\DAO\AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());
    }
    
    /**
     * @covers API\DAO\AdminDao::removeAdmin
     */
    public function testRemoveAdmin()
    {
        UnitTestHelper::teardownDb();

        $user = UnitTestHelper::createUser(null, "Bob", "blah", "foo@coo.com");
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();

        API\DAO\AdminDao::addSiteAdmin($userId);
        $admins = API\DAO\AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());
        
        API\DAO\AdminDao::removeAdmin($userId);
        $admins = API\DAO\AdminDao::getAdmins();
        $this->assertNull($admins);
    }

    /**
     * @covers API\DAO\AdminDao::addOrgAdmin
     */
    public function testAddOrgAdmin()
    {
        UnitTestHelper::teardownDb();

        $user = UnitTestHelper::createUser(null, "Bob", "blah", "foo@coo.com");
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();

        $org = UnitTestHelper::createOrg(null, "Bunnyland");
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg);
        $orgId = $insertedOrg->getId();
        API\DAO\OrganisationDao::requestMembership($userId, $orgId);
        API\DAO\OrganisationDao::acceptMemRequest($orgId, $userId);

        API\DAO\AdminDao::addOrgAdmin($userId, $orgId);
        $adminsReturned = API\DAO\AdminDao::getAdmins($userId, $orgId);
        $this->assertNotNull($adminsReturned);
        $this->assertEquals($userId, $adminsReturned[0]->getId());
    }
    
    /**
     * @covers API\DAO\AdminDao::removeOrgAdmin
     */
    public function testRemoveOrgAdmin()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser(null, "Bob", "blah", "foo@coo.com");
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
        
        $org = UnitTestHelper::createOrg(null, "Bunnyland");
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg);
        $orgId = $insertedOrg->getId();
        API\DAO\OrganisationDao::requestMembership($userId, $orgId);
        API\DAO\OrganisationDao::acceptMemRequest($orgId, $userId);
        
        API\DAO\AdminDao::addOrgAdmin($userId, $orgId);
        $adminsReturned = API\DAO\AdminDao::getAdmins($userId, $orgId);
        $this->assertEquals($userId, $adminsReturned[0]->getId());
        
        API\DAO\AdminDao::removeOrgAdmin($userId, $orgId);
        $admins = API\DAO\AdminDao::getAdmins($orgId);
        $this->assertNull($admins);
    }

    /**
     * @covers API\DAO\AdminDao::saveBannedUser
     */
    public function testSaveBannedUser()
    {
        UnitTestHelper::teardownDb();

        $user = UnitTestHelper::createUser(null, "Bob", "blah", "foo@coo.com");
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
        API\DAO\AdminDao::addSiteAdmin($userId);
        $admins = API\DAO\AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());

        $user2 = UnitTestHelper::createUser(null, "John", "blah", " blah@coo.com");
        $insertedUser2 = API\DAO\UserDao::save($user);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser2);
        $this->assertNotNull($insertedUser2);
        $user2Id = $insertedUser2->getId();

        //Ban the user
        $bannedUser = UnitTestHelper::createBannedUser($user2Id, $userId, Common\Enums\BanTypeEnum::WEEK, "b&!!!!");
        API\DAO\AdminDao::saveBannedUser($bannedUser);
        $isBanned = API\DAO\AdminDao::isUserBanned($user2Id);
        $this->assertEquals("1", $isBanned);
    }
    
    /**
     * @covers API\DAO\AdminDao::getBannedUser
     */
    public function testGetBannedUser()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser(null, "Bob", "blah", "foo@coo.com");
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
        API\DAO\AdminDao::addSiteAdmin($userId);
        $admins = API\DAO\AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());
        
        $user2 = UnitTestHelper::createUser(null, "John", "blah", " blah@coo.com");
        $insertedUser2 = API\DAO\UserDao::save($user);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser2);
        $this->assertNotNull($insertedUser2);
        $user2Id = $insertedUser2->getId();
        
        $bannedUser = UnitTestHelper::createBannedUser($user2Id, $userId, Common\Enums\BanTypeEnum::WEEK, "b&!!!!");
        API\DAO\AdminDao::saveBannedUser($bannedUser);
        $isBanned = API\DAO\AdminDao::isUserBanned($user2Id);
        $this->assertEquals("1", $isBanned);
        
        $getBannedUser = API\DAO\AdminDao::getBannedUser($user2Id, $userId);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\BannedUser", $getBannedUser[0]);
        $this->assertEquals($bannedUser->getUserId(), $getBannedUser[0]->getUserId());
        $this->assertEquals($bannedUser->getUserIdAdmin(), $getBannedUser[0]->getUserIdAdmin());
        $this->assertEquals($bannedUser->getBanType(), $getBannedUser[0]->getBanType());
        $this->assertEquals($bannedUser->getComment(), $getBannedUser[0]->getComment());
    }
    
    /**
     * @covers API\DAO\AdminDao::unBanUser
     */
    public function testUnBanUser()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser(null, "Bob", "blah", "foo@coo.com");
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
        API\DAO\AdminDao::addSiteAdmin($userId);
        $admins = API\DAO\AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());
        
        $user2 = UnitTestHelper::createUser(null, "John", "blah", " blah@coo.com");
        $insertedUser2 = API\DAO\UserDao::save($user);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser2);
        $this->assertNotNull($insertedUser2);
        $user2Id = $insertedUser2->getId();
        
        //Ban the user
        $bannedUser = UnitTestHelper::createBannedUser($user2Id, $userId, Common\Enums\BanTypeEnum::WEEK, "b&!!!!");
        API\DAO\AdminDao::saveBannedUser($bannedUser);
        $isBanned = API\DAO\AdminDao::isUserBanned($user2Id);
        $this->assertEquals("1", $isBanned);
        
        API\DAO\AdminDao::unBanUser($user2Id);
        $isBanned = API\DAO\AdminDao::isUserBanned($user2Id);
        $this->assertEquals("0", $isBanned);
    }
    
    /**
     * @covers API\DAO\AdminDao::saveBannedOrg
     */
    public function testSaveBannedOrg()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser(null, "Bob", "blah", "foo@coo.com");
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertNotNull($insertedUser);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $userId = $insertedUser->getId();
        
        API\DAO\AdminDao::addSiteAdmin($userId);
        $admins = API\DAO\AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());
        
        $org = UnitTestHelper::createOrg(null, "Bunnyland");
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg);
        $orgId = $insertedOrg->getId();
        
        $bannedOrg = UnitTestHelper::createBannedOrg($orgId, $userId);
        API\DAO\AdminDao::saveBannedOrg($bannedOrg);
        $isBanned = API\DAO\AdminDao::isOrgBanned($orgId);
        $this->assertEquals("1", $isBanned);
    }
    
    /**
     * @covers API\DAO\AdminDao::getBannedOrg
     */
    public function testGetBannedOrg()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser(null, "Bob", "blah", "foo@coo.com");
        $insertedUser = API\DAO\UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($userId);
        
        API\DAO\AdminDao::addSiteAdmin($userId);
        $admins = API\DAO\AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());
        
        $org = UnitTestHelper::createOrg(null, "Bunnyland");
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg);
        $orgId = $insertedOrg->getId();
        
        $bannedOrg = UnitTestHelper::createBannedOrg($orgId, $userId);
        API\DAO\AdminDao::saveBannedOrg($bannedOrg);
        $isBanned = API\DAO\AdminDao::isOrgBanned($orgId);
        $this->assertEquals("1", $isBanned);
        
        $getBannedOrg = API\DAO\AdminDao::getBannedOrg($orgId, $userId);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\BannedOrganisation", $getBannedOrg[0]);
        $this->assertEquals($bannedOrg->getOrgId(), $getBannedOrg[0]->getOrgId());
        $this->assertEquals($bannedOrg->getUserIdAdmin(), $getBannedOrg[0]->getUserIdAdmin());
        $this->assertEquals($bannedOrg->getBanType(), $getBannedOrg[0]->getBanType());
        $this->assertEquals($bannedOrg->getComment(), $getBannedOrg[0]->getComment());
    }
    
    /**
     * @covers API\DAO\AdminDao::unBanOrg
     */
    public function testUnBanOrg()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser(null, "Bob", "blah", "foo@coo.com");
        $insertedUser = API\DAO\UserDao::save($user);
        $userId = $insertedUser->getId();
        $this->assertNotNull($userId);
        
        API\DAO\AdminDao::addSiteAdmin($userId);
        $admins = API\DAO\AdminDao::getAdmins();
        $this->assertNotNull($admins);
        $this->assertEquals($userId, $admins[0]->getId());
        
        $org = UnitTestHelper::createOrg(null, "Bunnyland");
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg);
        $orgId = $insertedOrg->getId();
        
        $bannedOrg = UnitTestHelper::createBannedOrg($orgId, $userId);
        API\DAO\AdminDao::saveBannedOrg($bannedOrg);
        $isBanned = API\DAO\AdminDao::isOrgBanned($orgId);
        $this->assertEquals("1", $isBanned);
        
        API\DAO\AdminDao::unBanOrg($orgId);
        $isBanned = API\DAO\AdminDao::isOrgBanned($orgId);
        $this->assertEquals("0", $isBanned);
    }

    /**
     * @covers API\DAO\AdminDao::isAdmin
     */
    public function testIsAdmin()
    {
        UnitTestHelper::teardownDb();

        $user = UnitTestHelper::createUser(null, "John", "blah", " blah@coo.com");
        $insertedUser = API\DAO\UserDao::save($user);
        $this->assertInstanceOf("\SolasMatch\Common\Protobufs\Models\User", $insertedUser);
        $this->assertNotNull($insertedUser);
        $userId = $insertedUser->getId();
        API\DAO\AdminDao::addSiteAdmin($userId);
        $this->assertEquals("1", API\DAO\AdminDao::isAdmin($userId, null));
    }
}
