<?php

namespace SolasMatch\API\DAO;

use \SolasMatch\API\Lib as Lib;

/**
 * Description of StatDao
 *
 * @author sean
 */
include_once __DIR__."/../../api/lib/PDOWrapper.class.php";

class StatDao
{
    public static function getStatistics($name)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNullOrWrapStr($name);
        $result = Lib\PDOWrapper::call('getStatistics', $args);
        if ($result) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = \ModelFactory::buildModel("Statistic", $row);
            }
        }
        return $ret;
    }
    
    public static function updateArchivedProjects()
    {
        Lib\PDOWrapper::call("statsUpdateArchivedProjects", "");
    }
    
    public static function updateArchivedTasks()
    {
        Lib\PDOWrapper::call("statsUpdateArchivedTasks", "");
    }
    
    public static function updateBadges()
    {
        Lib\PDOWrapper::call("statsUpdateBadges", "");
    }
    
    public static function updateClaimedTasks()
    {
        Lib\PDOWrapper::call("statsUpdateClaimedTasks", "");
    }
    
    public static function updateOrganisations()
    {
        Lib\PDOWrapper::call("statsUpdateOrganisations", "");
    }
    
    public static function updateOrgMemberRequests()
    {
        Lib\PDOWrapper::call("statsUpdateOrgMemberRequests", "");
    }
    
    public static function updateProjects()
    {
        Lib\PDOWrapper::call("statsUpdateProjects", "");
    }
    
    public static function updateTags()
    {
        Lib\PDOWrapper::call("statsUpdateTags", "");
    }
    
    public static function updateTasks()
    {
        Lib\PDOWrapper::call("statsUpdateTasks", "");
    }
    
    public static function updateTasksWithPreReqs()
    {
        Lib\PDOWrapper::call("statsUpdateTasksWithPreReqs", "");
    }
    
    public static function updateUnclaimedTasks()
    {
        Lib\PDOWrapper::call("statsUpdateUnclaimedTasks", "");
    }
    
    public static function updateUsers()
    {
        Lib\PDOWrapper::call("statsUpdateUsers", "");
    }
}
