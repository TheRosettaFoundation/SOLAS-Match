<?php

namespace SolasMatch\API\DAO;

use \SolasMatch\API\Lib as Lib;
use \SolasMatch\Common as Common;

require_once __DIR__."/../../api/lib/PDOWrapper.class.php";

//! Statistcs Data Access Object for getting site statistics in the API
/*!
  The Statistics Data Access Object for retrieving data from the Database. It has direct Database access through the
  use of the PDOWrapper. It is used by the API Route Handlers for retrieving data requested through the API. Statistics
  hold information about the site like the number of registered Organisations, the number of active Tasks, etc.
*/

class StatDao
{
    //! Get a Statistic by name
    /*!
      Retrieve a statistic from the Database by name. If a name is not passed then all statistics are returned.
      @param string $name is the name of the requested statistic or null for all statistics
      @return Returns a list of Statistic objects or null
    */
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
    
    //! Update the Archived Projects Statistic in the Database
    /*!
      Recalculate the Archived Projects Statistic. <b>Note:</b> This is a direct call to the Database and occurs
      synchronously.
      @return No return
    */
    public static function updateArchivedProjects()
    {
        Lib\PDOWrapper::call("statsUpdateArchivedProjects", "");
    }
    
    //! Update the Archived Tasks Statistic in the Database
    /*!
      Recalculate the Archived Tasks Statistic. <b>Note:</b> This is a direct call to the Database and occurs
      synchronously.
      @return No return
    */
    public static function updateArchivedTasks()
    {
        Lib\PDOWrapper::call("statsUpdateArchivedTasks", "");
    }
    
    //! Update the Badge count Statistic in the Database
    /*!
      Recalculate the Badge count Statistic. <b>Note:</b> This is a direct call to the Database and occurs
      synchronously.
      @return No return
    */
    public static function updateBadges()
    {
        Lib\PDOWrapper::call("statsUpdateBadges", "");
    }
    
    //! Update the claimed Tasks Statistic in the Database
    /*!
      Recalculate the claimed Tasks Statistic. <b>Note:</b> This is a direct call to the Database and occurs
      synchronously.
      @return No return
    */
    public static function updateClaimedTasks()
    {
        Lib\PDOWrapper::call("statsUpdateClaimedTasks", "");
    }
    
    //! Get a count of the successful logins
    /*!
      Count the number of successful login attempts starting at startDate up to but not including endDate.
      @param String $startDate is the date to start counting from in the format "YYYY-MM-DD HH:MM:SS"
      @param String $endDate is the date to count up to in the format "YYYY-MM-DD HH:MM:SS"
      @return Returns an int represting the number of successful login attempts between start and end dates provided.
    */
    public static function getLoginCount($startDate, $endDate)
    {
        $args = Lib\PDOWrapper::cleanseNullOrWrapStr($startDate).', '.
            Lib\PDOWrapper::cleanseNullOrWrapStr($endDate);
        $result = Lib\PDOWrapper::call('getLoginCount', $args);
        return $result[0]['result'];
    }

    //! Update the Organisation count Statistic in the Database
    /*!
      Recalculate the Organisation count Statistic. <b>Note:</b> This is a direct call to the Database and occurs
      synchronously.
      @return No return
    */
    public static function updateOrganisations()
    {
        Lib\PDOWrapper::call("statsUpdateOrganisations", "");
    }
    
    //! Update the Organisation membership requests Statistic in the Database
    /*!
      Recalculate the Organisation membership requests Statistic. <b>Note:</b> This is a direct call to the Database and
      occurs synchronously.
      @return No return
    */
    public static function updateOrgMemberRequests()
    {
        Lib\PDOWrapper::call("statsUpdateOrgMemberRequests", "");
    }
    
    //! Update the Project count Statistic in the Database
    /*!
      Recalculate the Project count Statistic. <b>Note:</b> This is a direct call to the Database and occurs
      synchronously.
      @return No return
    */
    public static function updateProjects()
    {
        Lib\PDOWrapper::call("statsUpdateProjects", "");
    }
    
    //! Update the Tag count Statistic in the Database
    /*!
      Recalculate the Tag count Statistic. <b>Note:</b> This is a direct call to the Database and occurs
      synchronously.
      @return No return
    */
    public static function updateTags()
    {
        Lib\PDOWrapper::call("statsUpdateTags", "");
    }
    
    //! Update the Task count Statistic in the Database
    /*!
      Recalculate the Task count Statistic. <b>Note:</b> This is a direct call to the Database and occurs
      synchronously.
      @return No return
    */
    public static function updateTasks()
    {
        Lib\PDOWrapper::call("statsUpdateTasks", "");
    }
    
    //! Update the Task with prerequisites count Statistic in the Database
    /*!
      Recalculate the Tasks with prerequisites count Statistic. <b>Note:</b> This is a direct call to the Database and
      occurs synchronously.
      @return No return
    */
    public static function updateTasksWithPreReqs()
    {
        Lib\PDOWrapper::call("statsUpdateTasksWithPreReqs", "");
    }
    
    //! Update the Unclaimed Tasks Statistic in the Database
    /*!
      Recalculate the Unclaimed Tasks Statistic. <b>Note:</b> This is a direct call to the Database and occurs
      synchronously.
      @return No return
    */
    public static function updateUnclaimedTasks()
    {
        Lib\PDOWrapper::call("statsUpdateUnclaimedTasks", "");
    }
    
    //! Update the User count Statistic in the Database
    /*!
      Recalculate the User count Statistic. <b>Note:</b> This is a direct call to the Database and occurs
      synchronously.
      @return No return
    */
    public static function updateUsers()
    {
        Lib\PDOWrapper::call("statsUpdateUsers", "");
    }
}
