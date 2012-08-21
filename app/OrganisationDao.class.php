<?php

require('models/Organisation.class.php');

class OrganisationDao {
    public function find($params) {
        $ret = null;
        $db = new PDOWrapper();
        $db->init();
        if (isset($params['id'])) {
            if ($result = $db->call("findOganisation", $db->cleanse($params['id']))) {

                $ret = $this->create_org_from_sql_result($result);

            }
        }
        return $ret;
    }
    
    public static function nameFromId($organisation_id)
	{
                $result = self::getOrg($organisation_id, null, null, null);
                return $result[0]->getName();
	}
        
    public static function getOrg($id,$name,$homepage,$bio){
         $ret = array();
        $db = new PDOWrapper();
        $db->init();
        
        if($result = $db->call("getOrg", "{$db->cleanse($id)},{$db->cleanseNullOrWrapStr($name)},{$db->cleanseNullOrWrapStr($homepage)},{$db->cleanseNullOrWrapStr($bio)}")) {
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
            $ret = $this->create_org_from_sql_result($result);
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

    public function requestMembership($user_id, $org_id)
    {
        //Check if the user has already requested membership
        $previous_requests = $this->getMembershipRequests($org_id);
        if(!is_null($previous_requests)) {
            foreach($previous_requests as $request) {
                if($request['user_id'] == $user_id) {
                    //User has already sent a request, return
                    return false;
                }
            }
        }
        $db = new MySQLWrapper();
        $db->init();
        
        $insert = "INSERT INTO org_request_queue (user_id, org_id)
                    VALUES (".$db->cleanse($user_id).", ".$db->cleanse($org_id).")";
        if($db->insertStr($insert)) {
            return true;
        } else {
            return false;
        }        
    }
        

    public function getMembershipRequests($org_id)
    {
        $db = new MySQLWrapper();
        $db->init();
        $query = "SELECT *
                    FROM org_request_queue
                    WHERE org_id = ".$db->cleanse($org_id)."
                    ORDER BY request_datetime DESC";
        $ret = null;
        if($result = $db->Select($query)) {
            $ret = $result;
        }
        return $ret;
    }

    public function acceptMemRequest($org_id, $user_id) {
        $db = new MySQLWrapper();
        $db->init();
        //Add user as org member
        $insert = "INSERT INTO organisation_member (user_id, organisation_id)
                VALUES (".$db->cleanse($user_id).", ".$db->cleanse($org_id).")";
        $db->insertStr($insert);

        $this->removeMembershipRequest($org_id, $user_id);
    }

    public function refuseMemRequest($org_id, $user_id) {
        //Simply remove the membership request
        $this->removeMembershipRequest($org_id, $user_id);
    }

    private function removeMembershipRequest($org_id, $user_id) {
        $db = new MySQLWrapper();
        $db->init();
        $delete = "DELETE FROM org_request_queue
                WHERE user_id=".$db->cleanse($user_id)."
                AND org_id=".$db->cleanse($org_id);
        $db->Delete($delete);
    }

    private static function create_org_from_sql_result($result) {
        $org_data = array(
                    'id' => $result[0]['id'],
                    'name' => $result[0]['name'],
                    'home_page' => $result[0]['home_page'],
                    'biography' => $result[0]['biography']
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
}
