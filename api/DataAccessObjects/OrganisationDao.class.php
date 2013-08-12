<?php

require_once __DIR__."/../../Common/models/Organisation.php";
require_once __DIR__."/../../Common/models/MembershipRequest.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";

class OrganisationDao
{     
    
    public static function isMember($orgID,$userID)
    {
        $ret=null;
        $args = PDOWrapper::cleanseNull($orgID)
                .",".PDOWrapper::cleanseNull($userID);
        
        if ($result = PDOWrapper::call("orgHasMember", $args)){
            $ret=$result[0]['result'];
        }
        return $ret;
    }
    
    public static function getOrg($id=null, $name=null, $homepage=null, $bio=null, $email=null, $address=null, $city=null,
                                    $country=null, $regionalFocus=null)
    {
        $ret = array();
        
        $args = PDOWrapper::cleanseNull($id)
                .",".PDOWrapper::cleanseNullOrWrapStr($name)
                .",".PDOWrapper::cleanseNullOrWrapStr($homepage)
                .",".PDOWrapper::cleanseNullOrWrapStr($bio)
                .",".PDOWrapper::cleanseNullOrWrapStr($email)
                .",".PDOWrapper::cleanseNullOrWrapStr($address)
                .",".PDOWrapper::cleanseNullOrWrapStr($city)
                .",".PDOWrapper::cleanseNullOrWrapStr($country)
                .",".PDOWrapper::cleanseNullOrWrapStr($regionalFocus);
        
        $result = PDOWrapper::call("getOrg", $args);
        if(is_array($result)) {
            foreach ($result as $row) {
                $ret[] = ModelFactory::buildModel("Organisation", $row);
            }
        } else {
            $ret = null;
        }        
        return $ret;
    }
    
    public static function searchForOrg($org_name)
    {
         $ret = null;
         $args = PDOWrapper::cleanseWrapStr($org_name);
         
         if ($result = PDOWrapper::call("searchForOrg", $args)) {
             $ret = array();
             foreach ($result as $row) {
                 $ret[] = ModelFactory::buildModel("Organisation", $row);
             }
         }

         return $ret;
     }   
    
    public static function getOrgByUser($user_id) //currently not used
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($user_id);
        
        if ($result = PDOWrapper::call("getOrgByUser", $args)) {
            if(is_array($result)) {
                $ret = ModelFactory::buildModel("Organisation", $result[0]);
            }
        }        
        return $ret;
    }

    public static function getOrgMembers($org_id)
    {
        $ret = null;
        $args = PDOWrapper::cleanseNull($org_id);
        
        if ($result = PDOWrapper::call("getOrgMembers", $args)) {
            foreach ($result as $user){
                $ret[]= ModelFactory::buildModel("User", $user);
            }
        }
        
        return $ret;
    }

    public static function requestMembership($user_id, $org_id)
    {
        $args = PDOWrapper::cleanseNull($user_id)
                .",".PDOWrapper::cleanseNull($org_id);
        
        $result = PDOWrapper::call("requestMembership", $args);
        return $result[0]['result'];
    }
        

    public static function getMembershipRequests($org_id)
    {
        $ret = null;
        $args = PDOWrapper::cleanse($org_id);
        
        if ($results = PDOWrapper::call("getMembershipRequests", $args)) {
            foreach ($results as $result) {  
                $ret[] = ModelFactory::buildModel("MembershipRequest", $result);
            }
        }
        
        return $ret;
    }

    public static function acceptMemRequest($org_id, $user_id)
    {
        $args = PDOWrapper::cleanseNull($user_id)
                .",".PDOWrapper::cleanseNull($org_id);
        
        $result = PDOWrapper::call("acceptMemRequest", $args);
        return $result[0]['result'];
    }

    public static function refuseMemRequest($org_id, $user_id)
    {
        return self::removeMembershipRequest($org_id, $user_id);
    }

    private static function removeMembershipRequest($org_id, $user_id)
    {
        $args = PDOWrapper::cleanseNull($user_id)
                .",".PDOWrapper::cleanseNull($org_id);
        
        $result = PDOWrapper::call("removeMembershipRequest", $args);
        return $result[0]['result']; 
    }
    
    public static function revokeMembership($org_id, $user_id)
    {
        $args = PDOWrapper::cleanseNull($user_id)
                .",".PDOWrapper::cleanseNull($org_id);
        
        $result = PDOWrapper::call("revokeMembership", $args);
        return $result[0]['result'];
    } 
    
    public static function insertAndUpdate($org)
    {
        $ret = null;  
               
        $args = PDOWrapper::cleanseNullOrWrapStr($org->getId())
                .",".PDOWrapper::cleanseWrapStr($org->getHomePage())
                .",".PDOWrapper::cleanseNullOrWrapStr($org->getName())
                .",".PDOWrapper::cleanseNullOrWrapStr($org->getBiography())
                .",".PDOWrapper::cleanseNullOrWrapStr($org->getEmail())
                .",".PDOWrapper::cleanseNullOrWrapStr($org->getAddress())
                .",".PDOWrapper::cleanseNullOrWrapStr($org->getCity())
                .",".PDOWrapper::cleanseNullOrWrapStr($org->getCountry())
                .",".PDOWrapper::cleanseNullOrWrapStr($org->getRegionalFocus());                
                
        $result = PDOWrapper::call("organisationInsertAndUpdate", $args);
        if(is_array($result)) {
            $ret = ModelFactory::buildModel("Organisation", $result[0]);
        }
        
        return $ret;
    }

    public static function delete($orgID)
    {      
        $args = PDOWrapper::cleanse($orgID);
        
        $result= PDOWrapper::call("deleteOrg", $args);
        return $result[0]['result'];
    }    

}
