<?php

namespace SolasMatch\API\DAO;

use \SolasMatch\API\Lib as Lib;
use \SolasMatch\Common as Common;

/**
 * Description of StatDao
 *
 * @author sean
 */
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";
//! Statistcs Data Access Object for getting site statistics in the API
/*!
  The Statistics Data Access Object for retrieving data from the Database. It has direct Database access through the
  use of the PDOWrapper. It is used by the API Route Handlers for retrieving data requested through the API. Statistics
  hold information about the site like the number of registered Organisations, the number of active Tasks, etc.
*/

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
                $ret[] = Common\Lib\ModelFactory::buildModel("Statistic", $row);
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