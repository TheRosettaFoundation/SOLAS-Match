<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TagsDao.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../UnitTestHelper.php';

class ProjectTagsTest extends PHPUnit_Framework_TestCase
{
    public function testAddProjectTag()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());

        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);    
        $this->assertNotNull($project->getId());          

        $projectTag1 = TagsDao::create("New Project Tag");
        $this->assertInstanceOf("Tag", $projectTag1);
        $this->assertNotNull($projectTag1->getId());        
        $this->assertEquals("New Project Tag", $projectTag1->getLabel());
        
        // Success
        $resultAddProjectTag = ProjectTags::addProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("1", $resultAddProjectTag);
        
        // Failure
        $resultAddProjectTagFailure = ProjectTags::addProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("0", $resultAddProjectTagFailure);

    }
    
    
    public function testRemoveProjectTag()
    {
        UnitTestHelper::teardownDb();

        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());

        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);    
        $this->assertNotNull($project->getId());          

        $projectTag1 = TagsDao::create("New Project Tag");
        $this->assertInstanceOf("Tag", $projectTag1);
        $this->assertNotNull($projectTag1->getId());        
        $this->assertEquals("New Project Tag", $projectTag1->getLabel());
        
        $addProjectTag = ProjectTags::addProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("1", $addProjectTag);
        
        // Success
        $resultRemoveProjectTag = ProjectTags::removeProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("1", $resultRemoveProjectTag);
        
        // Failure
        $resultRemoveProjectTagFailure = ProjectTags::removeProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("0", $resultRemoveProjectTagFailure);
    }
    
    
    public function testGetTags()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());

        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);    
        $this->assertNotNull($project->getId());
        
        $resultGetTags = ProjectTags::getTags($project->getId());
        $this->assertCount(2, $resultGetTags);
        foreach($resultGetTags as $projectTag) {
            $this->assertInstanceOf("Tag", $projectTag);
        }
    }
    
     
}
?>
