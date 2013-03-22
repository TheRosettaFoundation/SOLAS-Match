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
        
        $orgDao = new OrganisationDao();
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        
        $projectDao = new ProjectDao();
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = $projectDao->createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);    
        $this->assertNotNull($project->getId());          

        $tagsDao = new TagsDao();
        $projectTag1 = $tagsDao->create("New Project Tag");
        $this->assertInstanceOf("Tag", $projectTag1);
        $this->assertNotNull($projectTag1->getId());        
        $this->assertEquals("New Project Tag", $projectTag1->getLabel());
        
        $projectTagsDao = new ProjectTags();
        // Success
        $resultAddProjectTag = $projectTagsDao->addProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("1", $resultAddProjectTag);
        
        // Failure
        $resultAddProjectTagFailure = $projectTagsDao->addProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("0", $resultAddProjectTagFailure);

    }
    
    
    public function testRemoveProjectTag()
    {
        UnitTestHelper::teardownDb();

        $orgDao = new OrganisationDao();
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        
        $projectDao = new ProjectDao();
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = $projectDao->createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);    
        $this->assertNotNull($project->getId());          

        $tagsDao = new TagsDao();
        $projectTag1 = $tagsDao->create("New Project Tag");
        $this->assertInstanceOf("Tag", $projectTag1);
        $this->assertNotNull($projectTag1->getId());        
        $this->assertEquals("New Project Tag", $projectTag1->getLabel());
        
        $projectTagsDao = new ProjectTags();
        $addProjectTag = $projectTagsDao->addProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("1", $addProjectTag);
        
        // Success
        $resultRemoveProjectTag = $projectTagsDao->removeProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("1", $resultRemoveProjectTag);
        
        // Failure
        $resultRemoveProjectTagFailure = $projectTagsDao->removeProjectTag($project->getId(), $projectTag1->getId());
        $this->assertEquals("0", $resultRemoveProjectTagFailure);
    }
    
    
    public function testGetTags()
    {
        UnitTestHelper::teardownDb();
        
        $orgDao = new OrganisationDao();
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
                
        $projectDao = new ProjectDao();
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = $projectDao->createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);    
        $this->assertNotNull($project->getId());
        
        $projectTagsDao = new ProjectTags();
        $resultGetTags = $projectTagsDao->getTags($project->getId());
        $this->assertCount(2, $resultGetTags);
        foreach($resultGetTags as $projectTag) {
            $this->assertInstanceOf("Tag", $projectTag);
        }
    }
    
     
}
?>
