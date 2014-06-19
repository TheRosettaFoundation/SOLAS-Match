<?php

namespace SolasMatch\Tests\UI;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../api/lib/Notify.class.php';
require_once __DIR__.'/../../Common/Enums/BadgeTypes.class.php';
require_once __DIR__.'/../../Common/Enums/HttpMethodEnum.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../../Common/lib/SolasMatchException.php';
require_once __DIR__.'/../../Common/lib/UserSession.class.php';
require_once __DIR__.'/../../ui/DataAccessObjects/AdminDao.class.php';
require_once __DIR__.'/../../ui/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../ui/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../ui/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../ui/lib/Localisation.php';
require_once __DIR__.'/../UnitTestHelper.php';

use \SolasMatch\UI as UI;
use \SolasMatch\API as API;
use \SolasMatch\Tests\UnitTestHelper;

class BadgeDaoTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers UI\DAO\BadgeDao::createBadge
     */
    public function testCreateBadge()
    {
        UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
        $badgeDao = new UI\DAO\BadgeDao();
        $orgDao = new UI\DAO\OrganisationDao();
        
        $userEmail = "blah@test.com";
        $userPw = "password";
        $isRegistered = $userDao->register($userEmail, $userPw);
        $this->assertTrue($isRegistered);
        
        $registerUser = API\DAO\UserDao::getUser(null, $userEmail);
        $userId = $registerUser->getId();
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf(UnitTestHelper::PROTO_USER, $registerUser);
        API\DAO\UserDao::finishRegistration($registerUser->getId());
        $loginUser = $userDao->login("blah@test.com", "password");
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->createOrg($org, $userId);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG, $insertedOrg);
        
        $badge = UnitTestHelper::createBadge();
        $insertedBadge = $badgeDao->createBadge($badge);
        $this->assertNotNull($insertedBadge);
        $this->assertInstanceOf(UnitTestHelper::PROTO_BADGE, $insertedBadge);
    }
    
    /* public function testGetBadge()
    {
       UnitTestHelper::teardownDb();
       
       $badgeDao = new UI\DAO\BadgeDao();
    } */
}
