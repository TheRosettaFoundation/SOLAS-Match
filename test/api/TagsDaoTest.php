<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/TagsDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TaskDao.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../UnitTestHelper.php';

class TagsDaoTest extends PHPUnit_Framework_TestCase
{
    
    public function testInsertTag()
    {
        UnitTestHelper::teardownDb(); 
        
        // Success
        $resultCreateTag = TagsDao::create("test");    
        $this->assertInstanceOf("Tag", $resultCreateTag);
        $this->assertNotNull($resultCreateTag->getId());
        $this->assertEquals("test", $resultCreateTag->getLabel());
        
        // Failure
        $resultCreateTag2 = TagsDao::create("test");    
        $this->assertNull($resultCreateTag2);
    }
    
    public function testGetTag()
    {
        UnitTestHelper::teardownDb();        
        
        // Failure - No Tags
        $resultGetAllTagsFailure = TagsDao::getTag(null);
        $this->assertNull($resultGetAllTagsFailure);
        
        $resultCreateTag = TagsDao::create("test");    
        $this->assertInstanceOf("Tag", $resultCreateTag);
        
        $resultCreateTag2 = TagsDao::create("test2");    
        $this->assertInstanceOf("Tag", $resultCreateTag2);        
        
        // Success - Single Tag
        $resultGetTag = TagsDao::getTag(array("id" => null, "label" => "test"));
        $this->assertInstanceOf("Tag", $resultGetTag[0]);
        $this->assertNotNull($resultGetTag[0]->getId());
        $this->assertEquals("test", $resultGetTag[0]->getLabel());
        
        // Success - All Tags
        $resultGetAllTags = TagsDao::getTag(null);
        $this->assertCount(2, $resultGetAllTags);
        foreach($resultGetAllTags as $tag) {
            $this->assertInstanceOf("Tag", $tag);
            $this->assertNotNull($tag->getId());
            $this->assertNotNull($tag->getLabel());
        }
    }
    
    public function testGetTopTags()
    {
        UnitTestHelper::teardownDb(); 
        
        // Failure - No Top Tags in the System
        $topTagsFailure = TagsDao::getTopTags();
        $this->assertNull($topTagsFailure);
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());
        
        $project2 = UnitTestHelper::createProject($insertedOrg->getId(), null, "Project 2", "Project 2 Description", "2020-03-29 16:30:00",
                "Project 2 Impact", "Project 2 Reference", 123456, "IE", "en", array("Project", "Tags", "Extra", "More"));        
        $insertedProject2 = ProjectDao::createUpdate($project2);
        $this->assertInstanceOf("Project", $insertedProject2); 
        $this->assertNotNull($insertedProject2->getId());
        
        
        // Success
        $successTopTags = TagsDao::getTopTags();
        $this->assertCount(4, $successTopTags);
        foreach($successTopTags as $tag) {
            $this->assertInstanceOf("Tag", $tag);
            $this->assertNotNull($tag->getId());
            $this->assertNotNull($tag->getLabel());
        }
    }    
    
    public function testDeleteTag()
    {
        UnitTestHelper::teardownDb(); 
        
        $resultCreateTag = TagsDao::create("test");    
        $this->assertInstanceOf("Tag", $resultCreateTag);
        $this->assertNotNull($resultCreateTag->getId());
        
        // Success
        $resultDeleteTag = TagsDao::delete($resultCreateTag->getId());
        $this->assertEquals("1", $resultDeleteTag);
        
        // Failure
        $resultDeleteTag2 = TagsDao::delete($resultCreateTag->getId());
        $this->assertEquals("0", $resultDeleteTag2);        
    }

}
?>
