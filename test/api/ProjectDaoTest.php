<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../UnitTestHelper.php';



class ProjectDaoTest extends PHPUnit_Framework_TestCase
{
    public function testProjectCreate()
    {
        UnitTestHelper::teardownDb();
        
        $projectDao = new ProjectDao();
        $orgDao = new OrganisationDao();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        
        // Success
        $insertedProject = $projectDao->createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);        
        $this->assertNotNull($insertedProject->getId());        
        $this->assertEquals($project->getTitle(), $insertedProject->getTitle());
        $this->assertEquals($project->getDescription(), $insertedProject->getDescription());
        $this->assertEquals($project->getDeadline(), $insertedProject->getDeadline());
        $this->assertEquals($project->getImpact(), $insertedProject->getImpact());
        $this->assertEquals($project->getReference(), $insertedProject->getReference());
        $this->assertEquals($project->getWordCount(), $insertedProject->getWordCount());
        $this->assertEquals($project->getSourceCountryCode(), $insertedProject->getSourceCountryCode());
        $this->assertEquals($project->getSourceLanguageCode(), $insertedProject->getSourceLanguageCode());
        
        $projectTags = $insertedProject->getTag();
        $this->assertCount(2, $projectTags);
        foreach($projectTags as $tag) {
            $this->assertInstanceOf("Tag", $tag);
        }
        
        $this->assertEquals($project->getOrganisationId(), $insertedProject->getOrganisationId());
        $this->assertNotNull($insertedProject->getCreatedTime());    

    }
    
    public function testProjectUpdate()
    {
        UnitTestHelper::teardownDb();
        
        $projectDao = new ProjectDao();
        $orgDao = new OrganisationDao();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = $projectDao->createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);          
        
        $org2 = UnitTestHelper::createOrg(NULL, "Organisation 2", "Organisation 2 Bio", "http://www.organisation2.org");
        $insertedOrg2 = $orgDao->insertAndUpdate($org2);
        $this->assertInstanceOf("Organisation", $insertedOrg2);
        $this->assertNotNull($insertedOrg2->getId());
        
        $insertedProject->setTitle("Updated Title");
        $insertedProject->setDescription("Updated Description");
        $insertedProject->setDeadline("2030-03-10 00:00:00");
        $insertedProject->setImpact("Updated Impact");
        $insertedProject->setReference("Updated Reference");
        $insertedProject->setWordCount(654321);
        $insertedProject->setSourceCountryCode("AZ");
        $insertedProject->setSourceLanguageCode("agx");
        $insertedProject->setTag(array("Updated Project", "Updated Tags"));
        $insertedProject->setOrganisationId($insertedOrg2->getId());
        $insertedProject->setCreatedTime("2030-06-20 00:00:00");
  
        // Success
        $updatedProject = $projectDao->createUpdate($insertedProject);
        $this->assertInstanceOf("Project", $updatedProject);
        $this->assertEquals($insertedProject->getTitle(), $updatedProject->getTitle());
        $this->assertEquals($insertedProject->getDescription(), $updatedProject->getDescription());
        $this->assertEquals($insertedProject->getDeadline(), $updatedProject->getDeadline());
        $this->assertEquals($insertedProject->getImpact(), $updatedProject->getImpact());
        $this->assertEquals($insertedProject->getReference(), $updatedProject->getReference());
        $this->assertEquals($insertedProject->getWordCount(), $updatedProject->getWordCount());
        $this->assertEquals($insertedProject->getSourceCountryCode(), $updatedProject->getSourceCountryCode());
        $this->assertEquals($insertedProject->getSourceLanguageCode(), $updatedProject->getSourceLanguageCode());

        $projectTagsAfterUpdate = $updatedProject->getTag();
        $this->assertCount(2, $projectTagsAfterUpdate);
        foreach($projectTagsAfterUpdate as $tag) {
            $this->assertInstanceOf("Tag", $tag);
        }       
        $this->assertEquals("Updated Project", $projectTagsAfterUpdate[0]->getLabel());
        $this->assertEquals("Updated Tags", $projectTagsAfterUpdate[1]->getLabel());
        
        $this->assertEquals($insertedProject->getOrganisationId(), $updatedProject->getOrganisationId());
        $this->assertEquals($insertedProject->getCreatedTime(), $updatedProject->getCreatedTime()); 
        
    }
    
    public function testGetProject()
    {
        UnitTestHelper::teardownDb();
        
        $projectDao = new ProjectDao();
        $orgDao = new OrganisationDao();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = $orgDao->insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = $projectDao->createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        
        $params = array(
            "id"                => $insertedProject->getId(),
            "title"             => $project->getTitle(),
            "description"       => $project->getDescription(),
            "impact"            => $project->getImpact(),
            "deadline"          => $project->getDeadline(),
            "organisation_id"   => $project->getOrganisationId(),
            "reference"         => $project->getReference(),
            "word-count"        => $project->getWordCount(),
            "created"           => $insertedProject->getCreatedTime(),
            "language_id"       => $project->getSourceLanguageCode(),
            "country_id"        => $project->getSourceCountryCode()            
        );
        
        // Success
        $resultGetProject = $projectDao->getProject($params);
        $this->assertCount(1, $resultGetProject);
        $this->assertInstanceOf("Project", $resultGetProject[0]);
        

        
    }
    
}
?>
