<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../UnitTestHelper.php';


class BadgeValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testValidateUserBadge()
    {
        UnitTestHelper::teardownDb();
        
        $user = UnitTestHelper::createUser();
        $badgeDao = new BadgeDao();
        $userDao = new UserDao();
        
        $insertedUser = $userDao->save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $badge = UnitTestHelper::createBadge();
        $insertedBadge = $badgeDao->insertAndUpdateBadge($badge);
        $this->assertInstanceOf("Badge", $insertedBadge);    
        
        $badgeValidator = new BadgeValidator();        
        $resultValidate = $badgeValidator->validateUserBadge($insertedUser, $insertedBadge);
        $this->assertEquals("1", $resultValidate);         
    }    
}
?>
