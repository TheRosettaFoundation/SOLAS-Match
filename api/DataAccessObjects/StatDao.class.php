<?php

/**
 * Description of StatDao
 *
 * @author sean
 */

class StatDao
{        
    public static function getStatistics($name)
    {
        $ret = null;
        $result = PDOWrapper::call('getStatistics', PDOWrapper::cleanseNullOrWrapStr($name));
        if ($result) {            
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Statistic", $row);
                //$ret[$stat->getName()] = $stat;
            }
        }
        return $ret;
    }
    
    public static function updateArchivedProjects()
    {       
       PDOWrapper::call("statsUpdateArchivedProjects", "");        
    }
    
    public static function updateArchivedTasks()
    {       
       PDOWrapper::call("statsUpdateArchivedTasks", "");        
    }
    
    public static function updateBadges()
    {       
       PDOWrapper::call("statsUpdateBadges", "");        
    }
    
    public static function updateClaimedTasks()
    {       
       PDOWrapper::call("statsUpdateClaimedTasks", "");        
    }
    
    public static function updateOrganisations()
    {       
       PDOWrapper::call("statsUpdateOrganisations", "");        
    }
    
    public static function updateOrgMemberRequests()
    {       
       PDOWrapper::call("statsUpdateOrgMemberRequests", "");        
    }
    
    public static function updateProjects()
    {       
       PDOWrapper::call("statsUpdateProjects", "");        
    }
    
    public static function updateTags()
    {       
       PDOWrapper::call("statsUpdateTags", "");        
    }
    
    public static function updateTasks()
    {       
       PDOWrapper::call("statsUpdateTasks", "");        
    }
    
    public static function updateTasksWithPreReqs()
    {       
       PDOWrapper::call("statsUpdateTasksWithPreReqs", "");        
    }
    
    public static function updateUnclaimedTasks()
    {       
       PDOWrapper::call("statsUpdateUnclaimedTasks", "");        
    }
    
    public static function updateUsers()
    {       
       PDOWrapper::call("statsUpdateUsers", "");        
    }    
}
