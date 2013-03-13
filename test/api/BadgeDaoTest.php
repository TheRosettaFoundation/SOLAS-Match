<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../UnitTestHelper.php';

class BadgeDaoTest extends PHPUnit_Framework_TestCase
{    
    public function testCreateSystemBadge()
    {
        UnitTestHelper::teardownDb();
        
        $newBadge = $this->createSystemBadge();

        $badgeDao = new BadgeDao();        
        $insertedBadge = $badgeDao->insertAndUpdateBadge($newBadge);        
        $this->assertInstanceOf("Badge", $insertedBadge);
        $this->assertNotNull($insertedBadge->getId());
        
        $this->assertEquals($newBadge->getTitle(), $insertedBadge->getTitle());
        $this->assertEquals($newBadge->getDescription() , $insertedBadge->getDescription());
        $this->assertEquals($newBadge->getOwnerId(), $insertedBadge->getOwnerId());
    }
    
    public function testUpdateSystemBadge()
    {
        UnitTestHelper::teardownDb(); 
        
        $newBadge = $this->createSystemBadge();
        
        $badgeDao = new BadgeDao();        
        $insertedBadge = $badgeDao->insertAndUpdateBadge($newBadge);        
        $this->assertInstanceOf("Badge", $insertedBadge);
        $this->assertNotNull($insertedBadge->getId());
        
        $this->assertEquals($newBadge->getTitle("New Title"), $insertedBadge->getTitle());
        $this->assertEquals($newBadge->getDescription("New Description"), $insertedBadge->getDescription());
        $this->assertEquals($newBadge->getOwnerId(null), $insertedBadge->getOwnerId());
        
        $insertedBadge->setTitle("New Title");
        $insertedBadge->setDescription("New Description");
        $insertedBadge->setOwnerId(null);
        
        $updatedBadge = $badgeDao->insertAndUpdateBadge($insertedBadge);
        $this->assertInstanceOf("Badge", $updatedBadge);
        $this->assertNotNull($updatedBadge->getId());
        
        $this->assertEquals($updatedBadge->getTitle(), $insertedBadge->getTitle());
        $this->assertEquals($updatedBadge->getDescription(), $insertedBadge->getDescription());
        $this->assertEquals($updatedBadge->getOwnerId(), $insertedBadge->getOwnerId());
    }
    
    public function testGetAllBadges()
    {
        //UnitTestHelper::teardownDb();
        $badgeDao = new BadgeDao();
        
        $allBadges = $badgeDao->getAllBadges();
        

        
    }
    
    private function createSystemBadge()
    {   
        $id = null;
        $title = "Unit Test Badge";
        $description = "Testing Badge DAO";
        $ownerId = null;
        
        $newBadge = new Badge();
        $newBadge->setId($id);
        $newBadge->setTitle($title);
        $newBadge->setDescription($description);
        $newBadge->setOwnerId($ownerId);        
        return $newBadge;
    }
}
?>