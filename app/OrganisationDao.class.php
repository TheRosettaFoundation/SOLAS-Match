<?php

require('models/Organisation.class.php');
require('models/MembershipRequest.class.php');
require_once ('PDOWrapper.class.php');

class OrganisationDao {
    public function find($params) {
        $ret = null;
        $db = new PDOWrapper();
        $db->init();
        if (isset($params['id'])) {
            if ($result = $db->call("findOganisation", $db->cleanse($params['id']))) {

                $ret = $this->create_org_from_sql_result($result[0]);

            }
        } elseif(isset($params['name'])) {
            $ret = self::getOrg(null, $params['name'], null, null);
        }
        return $ret;
    }
    
    public static function nameFromId($organisation_id)
	{
                $result = self::getOrg($organisation_id, null, null, null);
                return $result[0]->getName();
	}
        
    public static function getOrgByName($name)
    {
            if($result = self::getOrg(null, $name, null, null)){
                return $result[0];
            }else return null;
    }
        
    public static function getOrg($id,$name,$homepage,$bio)
    {
        $ret = array();
        $db = new PDOWrapper();
        $db->init();
        
        if($result = $db->call("getOrg", "{$db->cleanseNull($id)},{$db->cleanseNullOrWrapStr($name)},{$db->cleanseNullOrWrapStr($homepage)},{$db->cleanseNullOrWrapStr($bio)}")) {
            foreach ($result as $row){
                $ret[] = self::create_org_from_sql_result($row);
            }
        }
        return $ret;
    }
    
    public function getOrgByUser($user_id) {//currently not used
        $ret = null;
        $db = new PDOWrapper();
        $db->init();
        
        if($result = $db->call("getOrgByUser", $db->cleanse($user_id))) {
            $ret = $this->create_org_from_sql_result($result[0]);
        }
        return $ret;
    }

    public function getOrgMembers($org_id) {
        $ret = null;
        $db = new PDOWrapper();
        $db->init();
        if($result = $db->call("getOrgMembers", $db->cleanse($org_id))) {
            $ret = $result;
        }

        return $ret;
    }

    public function searchForOrg($org_name)
    {
        $ret = null;
        $db = new PDOWrapper();
        $db->init();
        if($result = $db->call("searchForOrg", $db->cleanseWrapStr($org_name))) {
            $ret = array();
            foreach($result as $row) {
                $ret[] = new Organisation($row);
            }
        }

        return $ret;
    }

    public function requestMembership($user_id, $org_id)
    {
        $db = new PDOWrapper();
        $db->init();
        $result = $db->call("requestMembership", "{$db->cleanse($user_id)},{$db->cleanse($org_id)}");
        return $result[0]['result'];
    }
        

    public function getMembershipRequests($org_id)
    {
        $db = new PDOWrapper();
        $db->init();
        $ret = null;
        if($results = $db->call("getMembershipRequests", "{$db->cleanse($org_id)}")) {
            foreach($results as $result){  
            $ret[] = new MembershipRequest($result);
            }
        }
        return $ret;
    }

    public function acceptMemRequest($org_id, $user_id) {
        $db = new PDOWrapper();
        $db->init();
        $db->call("acceptMemRequest", "{$db->cleanse($user_id)},{$db->cleanse($org_id)}");
   }

    public function refuseMemRequest($org_id, $user_id) {
        //Simply remove the membership request
        $this->removeMembershipRequest($org_id, $user_id);
    }

    private function removeMembershipRequest($org_id, $user_id) {
        $db = new PDOWrapper();
        $db->init();
        $db->call("removeMembershipRequest", "{$db->cleanse($user_id)},{$db->cleanse($org_id)}");
    }
    
    public static function revokeMembership($org_id, $user_id) {
        $db = new PDOWrapper();
        $db->init();
        if($result=$db->call("revokeMembership", "{$db->cleanse($user_id)},{$db->cleanse($org_id)}")){
            return $result[0]['result'];
        }else return 0;
    }

    private static function create_org_from_sql_result($result) {
        $org_data = array(
                    'id' => $result['id'],
                    'name' => $result['name'],
                    'home_page' => $result['home_page'],
                    'biography' => $result['biography']
        );

        return new Organisation($org_data);
    }
   
    
    public function save($org) {
        if(is_null($org->getId())) {
            return $this->_insert($org);       //Create new organisation
        } else {
            return $this->_update($org);       //Update the data row
        }
    }

    private function _insert($org) {
        $db = new PDOWrapper();
        $db->init();
        if($org_id = $db->call("organisationInsertAndUpdate", "null,'{$db->cleanse($org->getHomePage())}','{$db->cleanse($org->getName())}','{$db->cleanse($org->getBiography())}'")) {
            return $this->find(array('id' => $org_id[0]['result']));
        } else {
            return null;
        }
    }

    private function _update($org) {
        $db = new PDOWrapper();
        $db->init();
        return $db->call("organisationInsertAndUpdate", "{$db->cleanse($org->getId())},'{$db->cleanse($org->getHomePage())}','{$db->cleanse($org->getName())}','{$db->cleanse($org->getBiography())}'");
    }
    public function delete($orgID){
        $db = new PDOWrapper();
        $db->init();
        return $db->call("deleteOrg","{$db->cleanse($orgID)}" );
    }
}
