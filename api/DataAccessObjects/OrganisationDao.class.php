<?php

require_once __DIR__.'/../../Common/models/Organisation.php';
require_once __DIR__.'/../../Common/models/MembershipRequest.php';
require_once __DIR__.'/../../Common/lib/PDOWrapper.class.php';

class OrganisationDao
{     
    
    public static function isMember($orgID,$userID)
    {
        $ret=null;
        if ($result = PDOWrapper::call("orgHasMember", 
                PDOWrapper::cleanseNull($orgID).",".
                PDOWrapper::cleanseNull($userID))){
            $ret=$result[0]['result'];
        }
        return $ret;
    }
    
    public static function getOrg($id, $name, $homepage, $bio)
    {
        $ret = array();
        
        $result = PDOWrapper::call("getOrg", PDOWrapper::cleanseNull($id)
                                        .",".PDOWrapper::cleanseNullOrWrapStr($name)
                                        .",".PDOWrapper::cleanseNullOrWrapStr($homepage)
                                        .",".PDOWrapper::cleanseNullOrWrapStr($bio));
        if(is_array($result)) {
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Organisation", $row);
            }
        } else {
            $ret = null;
        }        
        return $ret;
    }
    
    public static function getOrgByUser($user_id) //currently not used
    {
        $ret = null;
        
        if ($result = PDOWrapper::call("getOrgByUser", PDOWrapper::cleanse($user_id))) {
            if(is_array($result)) {
                $ret = ModelFactory::buildModel("Organisation", $result[0]);
            }
        }        
        return $ret;
    }

    public static function getOrgMembers($org_id)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getOrgMembers", PDOWrapper::cleanse($org_id))) {
            foreach ($result as $user){
                $ret[]= ModelFactory::buildModel("User", $user);
            }
        }
        
        return $ret;
    }

    public static function searchForOrg($org_name)
    {
        $ret = null;
        if ($result = PDOWrapper::call("searchForOrg", PDOWrapper::cleanseWrapStr($org_name))) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Organisation", $row);
            }
        }
        
        return $ret;
    }

    public static function requestMembership($user_id, $org_id)
    {
        $result = PDOWrapper::call("requestMembership", PDOWrapper::cleanse($user_id).",".PDOWrapper::cleanse($org_id));
        return $result[0]['result'];
    }
        

    public static function getMembershipRequests($org_id)
    {
        $ret = null;
        if ($results = PDOWrapper::call("getMembershipRequests", PDOWrapper::cleanse($org_id))) {
            foreach ($results as $result) {  
                $ret[] = ModelFactory::buildModel("MembershipRequest", $result);
            }
        }
        
        return $ret;
    }

    public static function acceptMemRequest($org_id, $user_id)
    {
        $result = PDOWrapper::call("acceptMemRequest", PDOWrapper::cleanse($user_id).",".PDOWrapper::cleanse($org_id));
        return $result[0]['result'];
    }

    public static function refuseMemRequest($org_id, $user_id)
    {
        return self::removeMembershipRequest($org_id, $user_id);
    }

    private static function removeMembershipRequest($org_id, $user_id)
    {
        $result = PDOWrapper::call("removeMembershipRequest", PDOWrapper::cleanse($user_id).",".PDOWrapper::cleanse($org_id));
        return $result[0]['result']; 
    }
    
    public static function revokeMembership($org_id, $user_id)
    {
        $result = PDOWrapper::call("revokeMembership", PDOWrapper::cleanse($user_id).
                                        ",".PDOWrapper::cleanse($org_id));
        return $result[0]['result'];
    } 
    
    public static function insertAndUpdate($org)
    {
        if($result = PDOWrapper::call("organisationInsertAndUpdate", PDOWrapper::cleanseNullOrWrapStr($org->getId())
                                                    .",".PDOWrapper::cleanseWrapStr($org->getHomePage())
                                                    .",".PDOWrapper::cleanseNullOrWrapStr($org->getName())
                                                    .",".PDOWrapper::cleanseNullOrWrapStr($org->getBiography())));
        if(is_array($result)) {
            return ModelFactory::buildModel("Organisation", $result[0]);
        } else {
            return null;
        }
    }
    
    public static function delete($orgID)
    {      
        return PDOWrapper::call("deleteOrg", PDOWrapper::cleanse($orgID));
    }
}
