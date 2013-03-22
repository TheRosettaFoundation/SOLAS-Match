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
        
        $userDao = new UserDao();
        $user = UnitTestHelper::createUser();
        $insertedUser = $userDao->save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser);
        
        $badgeDao = new BadgeDao();
        $badge = UnitTestHelper::createBadge();
        $insertedBadge = $badgeDao->insertAndUpdateBadge($badge);
        $this->assertInstanceOf("Badge", $insertedBadge);    
        $this->assertNotNull($insertedBadge->getId());
        
        $userAssignedBadge = $badgeDao->assignBadge($insertedUser->getUserId(), $insertedBadge->getId());
        $this->assertEquals("1", $userAssignedBadge);
        
        $badgeValidator = new BadgeValidator();   
        
        // Success
        $resultValidate = $badgeValidator->validateUserBadge($insertedUser->getUserId(), $insertedBadge->getId());
        $this->assertEquals("1", $resultValidate);   
        
        $badge2 = UnitTestHelper::createBadge(99, "Badge 2", "Badge 2 Description", NULL);
        
        // Failure
        $resultValidateFailure = $badgeValidator->validateUserBadge($insertedUser->getUserId(), $badge2->getId());
        $this->assertEquals("0", $resultValidateFailure);    
    }    
}
?>
