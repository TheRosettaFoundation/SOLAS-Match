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
require_once __DIR__.'/../../ui/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../ui/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../ui/lib/Localisation.php';
require_once __DIR__.'/../UnitTestHelper.php';

use \SolasMatch\UI as UI;
use \SolasMatch\API as API;
use \SolasMatch\Tests as Tests;

class BadgeDaoTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateBadge()
    {
        Tests\UnitTestHelper::teardownDb();
        
        $userDao = new UI\DAO\UserDao();
        $isRegistered = $userDao->register("blah@test.com", "password");
        $this->assertTrue($isRegistered);
        
        $registerUser = API\DAO\UserDao::getUser(null,"blah@test.com");
        $registerUser = $registerUser[0];
        $this->assertNotNull($registerUser);
        $this->assertInstanceOf("User", $registerUser);
        API\DAO\UserDao::finishRegistration($registerUser->getId());
        $loginUser = $userDao->login("blah@test.com","password");
        
        /* $badgeDao = new UI\DAO\BadgeDao();
        $badge = Tests\UnitTestHelper::createBadge();
        $insertedBadge = $badgeDao->createBadge($badge);
        $this->assertInstanceOf("Badge",$insertedBadge);
        $this->assertEquals($badge,$insertedBadge); */
    }
    
    /* public function testGetBadge()
    {
       UnitTestHelper::teardownDb();
       
       $badgeDao = new BadgeDao();
    } */
}