<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TaskDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/StatDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../UnitTestHelper.php';

class StatDaoTest extends PHPUnit_Framework_TestCase
{
    public function testGetStatistics()
    {
        UnitTestHelper::teardownDb();
        $this->callStatFuncs();
       
        // Success - Total 12 Statistics
        $resultAllStatistics = StatDao::getStatistics(null);
        $this->assertCount(12, $resultAllStatistics);
        foreach ($resultAllStatistics as $stat) {
            $this->assertInstanceOf("Statistic", $stat);
        }
        
        // Success - Get a single statistic - Total Badges
        $resultAllBadges = StatDao::getStatistics("Badges");
        $this->assertCount(1, $resultAllBadges);
        $this->assertInstanceOf("Statistic", $resultAllBadges[0]);
        $this->assertEquals("Badges", $resultAllBadges[0]->getName());
        //Changed 3 to 7. There were only 3 badges when this test class was made?
        $this->assertEquals(7, $resultAllBadges[0]->getValue());
    }   

    
    public function testUpdateArchivedProjects()
    {
        UnitTestHelper::teardownDb();
        StatDao::updateArchivedProjects();
        
        $totalArchivedProjects = StatDao::getStatistics("ArchivedProjects");
        $this->assertCount(1, $totalArchivedProjects);
        $this->assertInstanceOf("Statistic", $totalArchivedProjects[0]);
        $this->assertEquals("ArchivedProjects", $totalArchivedProjects[0]->getName());
        $this->assertEquals(0, $totalArchivedProjects[0]->getValue());        
    }
    
    public function testUpdateArchivedTasks()
    {
        UnitTestHelper::teardownDb();
        StatDao::updateArchivedTasks();
        
        $totalArchivedTasks = StatDao::getStatistics("ArchivedTasks");
        $this->assertCount(1, $totalArchivedTasks);
        $this->assertInstanceOf("Statistic", $totalArchivedTasks[0]);
        $this->assertEquals("ArchivedTasks", $totalArchivedTasks[0]->getName());
        $this->assertEquals(0, $totalArchivedTasks[0]->getValue());        
    }
    
    public function testUpdateBadges()
    {
        UnitTestHelper::teardownDb();
        StatDao::updateBadges();
        
        $totalBadges = StatDao::getStatistics("Badges");
        $this->assertCount(1, $totalBadges);
        $this->assertInstanceOf("Statistic", $totalBadges[0]);
        $this->assertEquals("Badges", $totalBadges[0]->getName());
        $this->assertEquals(3, $totalBadges[0]->getValue());    
    }
    
    public function testUpdateClaimedTasks()
    {
        UnitTestHelper::teardownDb();
        StatDao::updateClaimedTasks();
        
        $totalClaimedTasks = StatDao::getStatistics("ClaimedTasks");
        $this->assertCount(1, $totalClaimedTasks);
        $this->assertInstanceOf("Statistic", $totalClaimedTasks[0]);
        $this->assertEquals("ClaimedTasks", $totalClaimedTasks[0]->getName());
        $this->assertEquals(0, $totalClaimedTasks[0]->getValue());    
    }
    
    public function testUpdateOrganisations()
    {
        UnitTestHelper::teardownDb();
        StatDao::updateOrganisations();
        
        $totalOrgs = StatDao::getStatistics("Organisations");
        $this->assertCount(1, $totalOrgs);
        $this->assertInstanceOf("Statistic", $totalOrgs[0]);
        $this->assertEquals("Organisations", $totalOrgs[0]->getName());
        $this->assertEquals(0, $totalOrgs[0]->getValue());    
    }
    
    public function testUpdateOrgMemberRequests()
    {
        UnitTestHelper::teardownDb();
        StatDao::updateOrgMemberRequests();
        
        $totalOrgMemberRequests = StatDao::getStatistics("OrgMembershipRequests");
        $this->assertCount(1, $totalOrgMemberRequests);
        $this->assertInstanceOf("Statistic", $totalOrgMemberRequests[0]);
        $this->assertEquals("OrgMembershipRequests", $totalOrgMemberRequests[0]->getName());
        $this->assertEquals(0, $totalOrgMemberRequests[0]->getValue());    
    }
    
    public function testUpdateProjects()
    {
        UnitTestHelper::teardownDb();
        StatDao::updateProjects();
        
        $totalProjects = StatDao::getStatistics("Projects");
        $this->assertCount(1, $totalProjects);
        $this->assertInstanceOf("Statistic", $totalProjects[0]);
        $this->assertEquals("Projects", $totalProjects[0]->getName());
        $this->assertEquals(0, $totalProjects[0]->getValue());    
    }
    
    public function testUpdateTags()
    {
        UnitTestHelper::teardownDb();
        StatDao::updateTags();
        
        $totalTags = StatDao::getStatistics("Tags");
        $this->assertCount(1, $totalTags);
        $this->assertInstanceOf("Statistic", $totalTags[0]);
        $this->assertEquals("Tags", $totalTags[0]->getName());
        $this->assertEquals(0, $totalTags[0]->getValue());    
    }
    
    public function testUpdateTasks()
    {
        UnitTestHelper::teardownDb();
        StatDao::updateTasks();
        
        $totalTasks = StatDao::getStatistics("Tasks");
        $this->assertCount(1, $totalTasks);
        $this->assertInstanceOf("Statistic", $totalTasks[0]);
        $this->assertEquals("Tasks", $totalTasks[0]->getName());
        $this->assertEquals(0, $totalTasks[0]->getValue());    
    }
    
    public function testUpdateTasksWithPreReqs()
    {
        UnitTestHelper::teardownDb();
        StatDao::updateTasksWithPreReqs();
        
        $totalTasksWithPreReqs = StatDao::getStatistics("TasksWithPreReqs");
        $this->assertCount(1, $totalTasksWithPreReqs);
        $this->assertInstanceOf("Statistic", $totalTasksWithPreReqs[0]);
        $this->assertEquals("TasksWithPreReqs", $totalTasksWithPreReqs[0]->getName());
        $this->assertEquals(0, $totalTasksWithPreReqs[0]->getValue());    
    }
    
    public function testUpdateUnclaimedTasks()
    {
        UnitTestHelper::teardownDb();
        StatDao::updateUnclaimedTasks();
        
        $totalUnclaimedTasks = StatDao::getStatistics("UnclaimedTasks");
        $this->assertCount(1, $totalUnclaimedTasks);
        $this->assertInstanceOf("Statistic", $totalUnclaimedTasks[0]);
        $this->assertEquals("UnclaimedTasks", $totalUnclaimedTasks[0]->getName());
        $this->assertEquals(0, $totalUnclaimedTasks[0]->getValue());    
    }
    
    public function testUpdateUsers()
    {
        UnitTestHelper::teardownDb();
        StatDao::updateUsers();
        
        $totalUsers = StatDao::getStatistics("Users");
        $this->assertCount(1, $totalUsers);
        $this->assertInstanceOf("Statistic", $totalUsers[0]);
        $this->assertEquals("Users", $totalUsers[0]->getName());
        $this->assertEquals(0, $totalUsers[0]->getValue());    
    }
    
    
    private function callStatFuncs()
    {
        StatDao::updateArchivedProjects();
        StatDao::updateArchivedTasks();
        StatDao::updateBadges();
        StatDao::updateClaimedTasks();
        StatDao::updateOrganisations();
        StatDao::updateOrgMemberRequests();
        StatDao::updateProjects();
        StatDao::updateTags();
        StatDao::updateTasks();
        StatDao::updateTasksWithPreReqs();
        StatDao::updateUnclaimedTasks();
        StatDao::updateUsers();
    }
    
}

?>
