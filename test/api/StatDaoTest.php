<?php

namespace SolasMatch\Tests\API;

use \SolasMatch\Tests\UnitTestHelper;
use \SolasMatch\API as API;
use \SolasMatch\Common as Common;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TaskDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/StatDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../UnitTestHelper.php';

class StatDaoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers API\DAO\StatDao::getStatistics
     */
    public function testGetStatistics()
    {
        UnitTestHelper::teardownDb();
        $this->callStatFuncs();
       
        // Success - Total 12 Statistics
        $resultAllStatistics = API\DAO\StatDao::getStatistics(null);
        $this->assertCount(12, $resultAllStatistics);
        foreach ($resultAllStatistics as $stat) {
            $this->assertInstanceOf(UnitTestHelper::PROTO_STATISTIC, $stat);
        }
        
        // Success - Get a single statistic - Total Badges
        $resultAllBadges = API\DAO\StatDao::getStatistics("Badges");
        $this->assertCount(1, $resultAllBadges);
        $this->assertNotNull($resultAllBadges[0]);
        $this->assertInstanceOf(UnitTestHelper::PROTO_STATISTIC, $resultAllBadges[0]);
        $this->assertEquals("Badges", $resultAllBadges[0]->getName());
        $this->assertEquals(7, $resultAllBadges[0]->getValue());
    }

    
    public function testUpdateArchivedProjects()
    {
        UnitTestHelper::teardownDb();
        API\DAO\StatDao::updateArchivedProjects();
        
        $totalArchivedProjects = API\DAO\StatDao::getStatistics("ArchivedProjects");
        $this->assertCount(1, $totalArchivedProjects);
        $this->assertNotNull($totalArchivedProjects[0]);
        $this->assertInstanceOf(UnitTestHelper::PROTO_STATISTIC, $totalArchivedProjects[0]);
        $this->assertEquals("ArchivedProjects", $totalArchivedProjects[0]->getName());
        $this->assertEquals(0, $totalArchivedProjects[0]->getValue());
    }
    
    public function testUpdateArchivedTasks()
    {
        UnitTestHelper::teardownDb();
        API\DAO\StatDao::updateArchivedTasks();
        
        $totalArchivedTasks = API\DAO\StatDao::getStatistics("ArchivedTasks");
        $this->assertCount(1, $totalArchivedTasks);
        $this->assertNotNull($totalArchivedTasks[0]);
        $this->assertInstanceOf(UnitTestHelper::PROTO_STATISTIC, $totalArchivedTasks[0]);
        $this->assertEquals("ArchivedTasks", $totalArchivedTasks[0]->getName());
        $this->assertEquals(0, $totalArchivedTasks[0]->getValue());
    }
    
    public function testUpdateBadges()
    {
        UnitTestHelper::teardownDb();
        API\DAO\StatDao::updateBadges();
        
        $totalBadges = API\DAO\StatDao::getStatistics("Badges");
        $this->assertCount(1, $totalBadges);
        $this->assertNotNull($totalBadges[0]);
        $this->assertInstanceOf(UnitTestHelper::PROTO_STATISTIC, $totalBadges[0]);
        $this->assertEquals("Badges", $totalBadges[0]->getName());
        $this->assertEquals(7, $totalBadges[0]->getValue());
    }
    
    public function testUpdateClaimedTasks()
    {
        UnitTestHelper::teardownDb();
        API\DAO\StatDao::updateClaimedTasks();
        
        $totalClaimedTasks = API\DAO\StatDao::getStatistics("ClaimedTasks");
        $this->assertCount(1, $totalClaimedTasks);
        $this->assertNotNull($totalClaimedTasks[0]);
        $this->assertInstanceOf(UnitTestHelper::PROTO_STATISTIC, $totalClaimedTasks[0]);
        $this->assertEquals("ClaimedTasks", $totalClaimedTasks[0]->getName());
        $this->assertEquals(0, $totalClaimedTasks[0]->getValue());
    }
    
    public function testUpdateOrganisations()
    {
        UnitTestHelper::teardownDb();
        API\DAO\StatDao::updateOrganisations();
        
        $totalOrgs = API\DAO\StatDao::getStatistics("Organisations");
        $this->assertCount(1, $totalOrgs);
        $this->assertNotNull($totalOrgs[0]);
        $this->assertInstanceOf(UnitTestHelper::PROTO_STATISTIC, $totalOrgs[0]);
        $this->assertEquals("Organisations", $totalOrgs[0]->getName());
        $this->assertEquals(0, $totalOrgs[0]->getValue());
    }
    
    public function testUpdateOrgMemberRequests()
    {
        UnitTestHelper::teardownDb();
        API\DAO\StatDao::updateOrgMemberRequests();
        
        $totalOrgMemberRequests = API\DAO\StatDao::getStatistics("OrgMembershipRequests");
        $this->assertCount(1, $totalOrgMemberRequests);
        $this->assertNotNull($totalOrgMemberRequests[0]);
        $this->assertInstanceOf(UnitTestHelper::PROTO_STATISTIC, $totalOrgMemberRequests[0]);
        $this->assertEquals("OrgMembershipRequests", $totalOrgMemberRequests[0]->getName());
        $this->assertEquals(0, $totalOrgMemberRequests[0]->getValue());
    }
    
    public function testUpdateProjects()
    {
        UnitTestHelper::teardownDb();
        API\DAO\StatDao::updateProjects();
        
        $totalProjects = API\DAO\StatDao::getStatistics("Projects");
        $this->assertCount(1, $totalProjects);
        $this->assertNotNull($totalProjects[0]);
        $this->assertInstanceOf(UnitTestHelper::PROTO_STATISTIC, $totalProjects[0]);
        $this->assertEquals("Projects", $totalProjects[0]->getName());
        $this->assertEquals(0, $totalProjects[0]->getValue());
    }
    
    public function testUpdateTags()
    {
        UnitTestHelper::teardownDb();
        API\DAO\StatDao::updateTags();
        
        $totalTags = API\DAO\StatDao::getStatistics("Tags");
        $this->assertCount(1, $totalTags);
        $this->assertNotNull($totalTags[0]);
        $this->assertInstanceOf(UnitTestHelper::PROTO_STATISTIC, $totalTags[0]);
        $this->assertEquals("Tags", $totalTags[0]->getName());
        $this->assertEquals(0, $totalTags[0]->getValue());
    }
    
    public function testUpdateTasks()
    {
        UnitTestHelper::teardownDb();
        API\DAO\StatDao::updateTasks();
        
        $totalTasks = API\DAO\StatDao::getStatistics("Tasks");
        $this->assertCount(1, $totalTasks);
        $this->assertNotNull($totalTasks[0]);
        $this->assertInstanceOf(UnitTestHelper::PROTO_STATISTIC, $totalTasks[0]);
        $this->assertEquals("Tasks", $totalTasks[0]->getName());
        $this->assertEquals(0, $totalTasks[0]->getValue());
    }
    
    public function testUpdateTasksWithPreReqs()
    {
        UnitTestHelper::teardownDb();
        API\DAO\StatDao::updateTasksWithPreReqs();
        
        $totalTasksWithPreReqs = API\DAO\StatDao::getStatistics("TasksWithPreReqs");
        $this->assertCount(1, $totalTasksWithPreReqs);
        $this->assertNotNull($totalTasksWithPreReqs[0]);
        $this->assertInstanceOf(UnitTestHelper::PROTO_STATISTIC, $totalTasksWithPreReqs[0]);
        $this->assertEquals("TasksWithPreReqs", $totalTasksWithPreReqs[0]->getName());
        $this->assertEquals(0, $totalTasksWithPreReqs[0]->getValue());
    }
    
    public function testUpdateUnclaimedTasks()
    {
        UnitTestHelper::teardownDb();
        API\DAO\StatDao::updateUnclaimedTasks();
        
        $totalUnclaimedTasks = API\DAO\StatDao::getStatistics("UnclaimedTasks");
        $this->assertCount(1, $totalUnclaimedTasks);
        $this->assertNotNull($totalUnclaimedTasks[0]);
        $this->assertInstanceOf(UnitTestHelper::PROTO_STATISTIC, $totalUnclaimedTasks[0]);
        $this->assertEquals("UnclaimedTasks", $totalUnclaimedTasks[0]->getName());
        $this->assertEquals(0, $totalUnclaimedTasks[0]->getValue());
    }
    
    public function testUpdateUsers()
    {
        UnitTestHelper::teardownDb();
        API\DAO\StatDao::updateUsers();
        
        $totalUsers = API\DAO\StatDao::getStatistics("Users");
        $this->assertCount(1, $totalUsers);
        $this->assertNotNull($totalUsers[0]);
        $this->assertInstanceOf(UnitTestHelper::PROTO_STATISTIC, $totalUsers[0]);
        $this->assertEquals("Users", $totalUsers[0]->getName());
        $this->assertEquals(0, $totalUsers[0]->getValue());
    }
    
    
    private function callStatFuncs()
    {
        API\DAO\StatDao::updateArchivedProjects();
        API\DAO\StatDao::updateArchivedTasks();
        API\DAO\StatDao::updateBadges();
        API\DAO\StatDao::updateClaimedTasks();
        API\DAO\StatDao::updateOrganisations();
        API\DAO\StatDao::updateOrgMemberRequests();
        API\DAO\StatDao::updateProjects();
        API\DAO\StatDao::updateTags();
        API\DAO\StatDao::updateTasks();
        API\DAO\StatDao::updateTasksWithPreReqs();
        API\DAO\StatDao::updateUnclaimedTasks();
        API\DAO\StatDao::updateUsers();
    }
}
