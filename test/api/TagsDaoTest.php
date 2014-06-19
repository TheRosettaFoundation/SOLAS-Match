<?php

namespace SolasMatch\Tests\API;

use \SolasMatch\Tests\UnitTestHelper;
use \SolasMatch\Common as Common;
use \SolasMatch\API as API;
use SolasMatch\API\DAO\TagsDao;
use SolasMatch\API\DAO\TaskDao;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/TagsDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TaskDao.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../UnitTestHelper.php';

class TagsDaoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers API\DAO\TagsDao::create
     */
    public function testCreate()
    {
        UnitTestHelper::teardownDb();
        
        // Success
        $resultCreateTag = API\DAO\TagsDao::create("test");
        $this->assertNotNull($resultCreateTag);
        $this->assertInstanceOf(UnitTestHelper::PROTO_TAG, $resultCreateTag);
        $this->assertEquals("test", $resultCreateTag->getLabel());
        
        // Failure
        $resultCreateTag2 = API\DAO\TagsDao::create("test");
        $this->assertNull($resultCreateTag2);
    }
    
    /**
     * @covers API\DAO\TagsDao::getTag
     */
    public function testGetTag()
    {
        UnitTestHelper::teardownDb();
        
        // Failure - No Tags
        $resultGetAllTagsFailure = API\DAO\TagsDao::getTag();
        $this->assertNull($resultGetAllTagsFailure);
        
        $resultCreateTag = API\DAO\TagsDao::create("test");
        $this->assertNotNull($resultCreateTag);
        $this->assertInstanceOf(UnitTestHelper::PROTO_TAG, $resultCreateTag);
        
        $resultCreateTag2 = API\DAO\TagsDao::create("test2");
        $this->assertNotNull($resultCreateTag2);
        $this->assertInstanceOf(UnitTestHelper::PROTO_TAG, $resultCreateTag2);
        
        // Success - Single Tag
        $resultGetTag = API\DAO\TagsDao::getTag(null, "test");
        $this->assertNotNull($resultGetTag);
        $this->assertInstanceOf(UnitTestHelper::PROTO_TAG, $resultGetTag);
        $this->assertEquals("test", $resultGetTag->getLabel());
    }
    
    /**
     * @covers API\DAO\TagsDao::getTags
     */
    public function testGetTags()
    {
        UnitTestHelper::teardownDb();
        
        $resultCreateTag = API\DAO\TagsDao::create("test");
        $this->assertNotNull($resultCreateTag);
        $this->assertInstanceOf(UnitTestHelper::PROTO_TAG, $resultCreateTag);
        
        $resultCreateTag2 = API\DAO\TagsDao::create("test2");
        $this->assertNotNull($resultCreateTag2);
        $this->assertInstanceOf(UnitTestHelper::PROTO_TAG, $resultCreateTag2);
        
        // Success - All Tags
        $resultGetAllTags = API\DAO\TagsDao::getTags();
        $this->assertCount(2, $resultGetAllTags);
        foreach ($resultGetAllTags as $tag) {
            $this->assertInstanceOf(UnitTestHelper::PROTO_TAG, $tag);
            $this->assertNotNull($tag->getId());
            $this->assertNotNull($tag->getLabel());
        }
    }
    
    /**
     * @covers API\DAO\TagsDao::getTopTags
     */
    public function testGetTopTags()
    {
        UnitTestHelper::teardownDb();
        
        // Failure - No Top Tags in the System
        $topTagsFailure = API\DAO\TagsDao::getTopTags();
        $this->assertNull($topTagsFailure);
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG, $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf(UnitTestHelper::PROTO_PROJECT, $insertedProject);
        
        $project2 = UnitTestHelper::createProject(
            $insertedOrg->getId(),
            null,
            "Project 2",
            "Project 2 Description",
            "2020-03-29 16:30:00",
            "Project 2 Impact",
            "Project 2 Reference",
            123456,
            "IE",
            "en",
            array("Project", "Tags", "Extra", "More")
        );
        $insertedProject2 = API\DAO\ProjectDao::save($project2);
        $this->assertNotNull($insertedProject2);
        $this->assertInstanceOf(UnitTestHelper::PROTO_PROJECT, $insertedProject2);
        
        //There is no task created for the project yet, so no tags will be returned
        $getTopTagsFailNoTask = API\DAO\TagsDao::getTopTags(10);
        $this->assertNull($getTopTagsFailNoTask);
        
        $task = UnitTestHelper::createTask($insertedProject2->getId());
        $insertedTask = API\DAO\TaskDao::save($task);
        $this->assertNotNull($insertedTask);
        $this->assertInstanceOf(UnitTestHelper::PROTO_TASK, $insertedTask);
        
        // Success
        $successTopTags = API\DAO\TagsDao::getTopTags(10);
        $this->assertCount(4, $successTopTags);
        foreach ($successTopTags as $tag) {
            $this->assertInstanceOf(UnitTestHelper::PROTO_TAG, $tag);
            $this->assertNotNull($tag->getId());
            $this->assertNotNull($tag->getLabel());
        }
    }
    
    /**
     * @covers API\DAO\TagsDao::delete
     */
    public function testDeleteTag()
    {
        UnitTestHelper::teardownDb();
        
        $resultCreateTag = API\DAO\TagsDao::create("test");
        $this->assertNotNull($resultCreateTag);
        $this->assertInstanceOf(UnitTestHelper::PROTO_TAG, $resultCreateTag);
        
        // Success
        $resultDeleteTag = API\DAO\TagsDao::delete($resultCreateTag->getId());
        $this->assertEquals("1", $resultDeleteTag);
        
        // Failure
        $resultDeleteTag2 = API\DAO\TagsDao::delete($resultCreateTag->getId());
        $this->assertEquals("0", $resultDeleteTag2);
    }
    
    /**
     * @covers API\DAO\TagsDao::updateTags
     */
    public function testUpdateTags()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = API\DAO\OrganisationDao::insertAndUpdate($org);
        $this->assertNotNull($insertedOrg);
        $this->assertInstanceOf(UnitTestHelper::PROTO_ORG, $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        $insertedProject = API\DAO\ProjectDao::save($project);
        $this->assertNotNull($insertedProject);
        $this->assertInstanceOf(UnitTestHelper::PROTO_PROJECT, $insertedProject);
        
        $tag = UnitTestHelper::createTag();
        $tag2 = UnitTestHelper::createTag(null, "Projects");
        $tagList = array($tag, $tag2);
        API\DAO\TagsDao::updateTags($insertedProject->getId(), $tagList);
        $updatedTagList = API\DAO\ProjectDao::getTags($insertedProject->getId());
        
        $this->assertCount(2, $updatedTagList);
        foreach ($updatedTagList as $projTag) {
            $this->assertInstanceOf(UnitTestHelper::PROTO_TAG, $projTag);
        }
        
        $this->assertContains($tag->getLabel(), $updatedTagList[0]->getLabel());
        $this->assertContains($tag2->getLabel(), $updatedTagList[1]->getLabel());
    }
}