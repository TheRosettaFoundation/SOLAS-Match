<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/TagsDao.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../UnitTestHelper.php';

class TagsDaoTest extends PHPUnit_Framework_TestCase
{
    
    public function testInsertTag()
    {
        UnitTestHelper::teardownDb(); 
        $tagDao = new TagsDao();  
        
        // Success
        $resultCreateTag = $tagDao->create("test");    
        $this->assertInstanceOf("Tag", $resultCreateTag);
        $this->assertNotNull($resultCreateTag->getId());
        $this->assertEquals("test", $resultCreateTag->getLabel());
        
        // Failure
        $resultCreateTag2 = $tagDao->create("test");    
        $this->assertNull($resultCreateTag2);
    }
    
    public function testGetTag()
    {
        UnitTestHelper::teardownDb(); 
        $tagDao = new TagsDao();         
        
        // Failure - No Tags
        $resultGetAllTagsFailure = $tagDao->getTag(null);
        $this->assertNull($resultGetAllTagsFailure);
        
        $resultCreateTag = $tagDao->create("test");    
        $this->assertInstanceOf("Tag", $resultCreateTag);
        
        $resultCreateTag2 = $tagDao->create("test2");    
        $this->assertInstanceOf("Tag", $resultCreateTag2);        
        
        // Success - Single Tag
        $resultGetTag = $tagDao->getTag(array("id" => null, "label" => "test"));
        $this->assertInstanceOf("Tag", $resultGetTag[0]);
        $this->assertNotNull($resultGetTag[0]->getId());
        $this->assertEquals("test", $resultGetTag[0]->getLabel());
        
        // Success - All Tags
        $resultGetAllTags = $tagDao->getTag(null);
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
        $tagDao = new TagsDao();
        
    }
    
    
    public function testDeleteTag()
    {
        UnitTestHelper::teardownDb(); 
        $tagDao = new TagsDao();
        
        $resultCreateTag = $tagDao->create("test");    
        $this->assertInstanceOf("Tag", $resultCreateTag);
        $this->assertNotNull($resultCreateTag->getId());
        
        // Success
        $resultDeleteTag = $tagDao->delete($resultCreateTag->getId());
        $this->assertEquals("1", $resultDeleteTag);
        
        // Failure
        $resultDeleteTag2 = $tagDao->delete($resultCreateTag->getId());
        $this->assertEquals("0", $resultDeleteTag2);
        
    }
}
?>
