<?php

require_once __DIR__.'/../../Common/models/Organisation.php';
require_once __DIR__.'/../../Common/models/MembershipRequest.php';
require_once __DIR__.'/../../Common/lib/PDOWrapper.class.php';

class OrganisationDao {    
     
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
        
        if ($result = PDOWrapper::call("getOrg", PDOWrapper::cleanseNull($id)
                                        .",".PDOWrapper::cleanseNullOrWrapStr($name)
                                        .",".PDOWrapper::cleanseNullOrWrapStr($homepage)
                                        .",".PDOWrapper::cleanseNullOrWrapStr($bio)));
        if(is_array($result)) {
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Organisation", $row);
            }
        } else {
            $ret = null;
        }        
        return $ret;
    }
    
    public function getOrgByUser($user_id) //currently not used
    {
        $ret = null;
        
        if ($result = PDOWrapper::call("getOrgByUser", PDOWrapper::cleanse($user_id))) {
            $ret = ModelFactory::buildModel("Organisation", $result[0]);
        }        
        return $ret;
    }

    public function getOrgMembers($org_id)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getOrgMembers", PDOWrapper::cleanse($org_id))) {
            foreach ($result as $user){
                $ret[]= ModelFactory::buildModel("User", $user);
            }
        }
        
        return $ret;
    }

    public function searchForOrg($org_name)
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

    public function requestMembership($user_id, $org_id)
    {
        $result = PDOWrapper::call("requestMembership", PDOWrapper::cleanse($user_id).",".PDOWrapper::cleanse($org_id));
        return $result[0]['result'];
    }
        

    public function getMembershipRequests($org_id)
    {
        $ret = null;
        if ($results = PDOWrapper::call("getMembershipRequests", PDOWrapper::cleanse($org_id))) {
            foreach ($results as $result) {  
                $ret[] = ModelFactory::buildModel("MembershipRequest", $result);
            }
        }
        
        return $ret;
    }

    public function acceptMemRequest($org_id, $user_id)
    {
        if($result = PDOWrapper::call("acceptMemRequest", PDOWrapper::cleanse($user_id).",".PDOWrapper::cleanse($org_id))) {
            return $result[0]['result'];
        } else {
            return null;
        }
    }

    public function refuseMemRequest($org_id, $user_id)
    {
        //Simply remove the membership request
        $this->removeMembershipRequest($org_id, $user_id);
    }

    private function removeMembershipRequest($org_id, $user_id)
    {
        PDOWrapper::call("removeMembershipRequest", PDOWrapper::cleanse($user_id).",".PDOWrapper::cleanse($org_id));
    }
    
    public static function revokeMembership($org_id, $user_id)
    {
        if ($result = PDOWrapper::call("revokeMembership", PDOWrapper::cleanse($user_id).
                                        ",".PDOWrapper::cleanse($org_id))) {
            return $result[0]['result'];
        } else {
            return 0;
        }
    } 
    
    public function insertAndUpdate($org)
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
    
    public function delete($orgID)
    {      
        return PDOWrapper::call("deleteOrg", PDOWrapper::cleanse($orgID));
    }
}
