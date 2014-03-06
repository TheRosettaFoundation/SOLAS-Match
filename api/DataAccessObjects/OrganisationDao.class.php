<?php

namespace SolasMatch\API\DAO;

use \SolasMatch\API\Lib as Lib;
use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/protobufs/models/Organisation.php";
require_once __DIR__."/../../Common/protobufs/models/MembershipRequest.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";

class OrganisationDao
{
    public static function isMember($orgID, $userID)
    {
        $ret=null;
        $args = Lib\PDOWrapper::cleanseNull($orgID).",".
            Lib\PDOWrapper::cleanseNull($userID);
        
        if ($result = Lib\PDOWrapper::call("orgHasMember", $args)) {
            $ret=$result[0]['result'];
        }
        return $ret;
    }
    
    public static function getOrg(
        $id = null,
        $name = null,
        $homepage = null,
        $bio = null,
        $email = null,
        $address = null,
        $city = null,
        $country = null,
        $regionalFocus = null
    ) {
        $ret = array();
        $args = Lib\PDOWrapper::cleanseNull($id).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($name).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($homepage).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($bio).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($email).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($address).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($city).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($country).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($regionalFocus);
        $result = Lib\PDOWrapper::call("getOrg", $args);
        if (is_array($result)) {
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Organisation", $row);
            }
        } else {
            $ret = null;
        }
        return $ret;
    }
    
    public static function searchForOrg($org_name)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseWrapStr($org_name);
        if ($result = Lib\PDOWrapper::call("searchForOrg", $args)) {
            $ret = array();
            foreach ($result as $row) {
                $ret[] = Common\Lib\ModelFactory::buildModel("Organisation", $row);
            }
        }
        return $ret;
    }
    
    public static function getOrgByUser($user_id) //currently not used
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($user_id);
        if ($result = Lib\PDOWrapper::call("getOrgByUser", $args)) {
            if (is_array($result)) {
                $ret = Common\Lib\ModelFactory::buildModel("Organisation", $result[0]);
            }
        }
        return $ret;
    }

    public static function getOrgMembers($org_id)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($org_id);
        if ($result = Lib\PDOWrapper::call("getOrgMembers", $args)) {
            foreach ($result as $user) {
                $ret[]= Common\Lib\ModelFactory::buildModel("User", $user);
            }
        }
        
        return $ret;
    }

    public static function requestMembership($user_id, $org_id)
    {
        $args = Lib\PDOWrapper::cleanseNull($user_id).",".
            Lib\PDOWrapper::cleanseNull($org_id);
        $result = Lib\PDOWrapper::call("requestMembership", $args);
        return $result[0]['result'];
    }
    
    public static function getMembershipRequests($org_id)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanse($org_id);
        if ($results = Lib\PDOWrapper::call("getMembershipRequests", $args)) {
            foreach ($results as $result) {
                $ret[] = Common\Lib\ModelFactory::buildModel("MembershipRequest", $result);
            }
        }
        
        return $ret;
    }

    public static function acceptMemRequest($org_id, $user_id)
    {
        $args = Lib\PDOWrapper::cleanseNull($user_id).",".
            Lib\PDOWrapper::cleanseNull($org_id);
        
        $result = Lib\PDOWrapper::call("acceptMemRequest", $args);
        return $result[0]['result'];
    }

    public static function refuseMemRequest($org_id, $user_id)
    {
        return self::removeMembershipRequest($org_id, $user_id);
    }

    private static function removeMembershipRequest($org_id, $user_id)
    {
        $args = Lib\PDOWrapper::cleanseNull($user_id).",".
            Lib\PDOWrapper::cleanseNull($org_id);
        $result = Lib\PDOWrapper::call("removeMembershipRequest", $args);
        return $result[0]['result'];
    }
    
    public static function revokeMembership($org_id, $user_id)
    {
        $args = Lib\PDOWrapper::cleanseNull($user_id).",".
            Lib\PDOWrapper::cleanseNull($org_id);
        $result = Lib\PDOWrapper::call("revokeMembership", $args);
        return $result[0]['result'];
    }
    
    public static function insertAndUpdate($org)
    {
        $ret = null;
        $args = Lib\PDOWrapper::cleanseNull($org->getId()).",".
            Lib\PDOWrapper::cleanseWrapStr($org->getHomePage()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($org->getName()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($org->getBiography()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($org->getEmail()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($org->getAddress()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($org->getCity()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($org->getCountry()).",".
            Lib\PDOWrapper::cleanseNullOrWrapStr($org->getRegionalFocus());
        $result = Lib\PDOWrapper::call("organisationInsertAndUpdate", $args);
        if (is_array($result)) {
            $ret = Common\Lib\ModelFactory::buildModel("Organisation", $result[0]);
        }
        return $ret;
    }

    public static function delete($orgID)
    {
        $args = Lib\PDOWrapper::cleanse($orgID);
        $result= Lib\PDOWrapper::call("deleteOrg", $args);
        return $result[0]['result'];
    }
}
