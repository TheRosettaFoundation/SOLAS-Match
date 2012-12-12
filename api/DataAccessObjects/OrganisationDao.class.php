<?php

require_once '../Common/models/Organisation.php';
require_once '../Common/models/MembershipRequest.php';
require_once '../Common/lib/PDOWrapper.class.php';

class OrganisationDao {
    
    public function find($params)
    {
        $ret = null;
        if (isset($params['id'])) {
            if ($result = PDOWrapper::call("findOganisation", PDOWrapper::cleanse($params['id']))) {
                $ret = $this->create_org_from_sql_result($result[0]);
            }
        } elseif (isset($params['name'])) {
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
        if ($result = self::getOrg(null, $name, null, null)) {
            return $result[0];
        } else {
            return null;
        }
    }
        
    public static function getOrg($id, $name, $homepage, $bio)
    {
        $ret = array();
        
        if ($result = PDOWrapper::call("getOrg", PDOWrapper::cleanseNull($id)
                                        .",".PDOWrapper::cleanseNullOrWrapStr($name)
                                        .",".PDOWrapper::cleanseNullOrWrapStr($homepage)
                                        .",".PDOWrapper::cleanseNullOrWrapStr($bio))) {
            foreach ($result as $row) {
                $ret[] = self::createOrgFromSqlResult($row);
            }
        }
        
        return $ret;
    }
    
    public function getOrgByUser($user_id) //currently not used
    {
        $ret = null;
        
        if ($result = PDOWrapper::call("getOrgByUser", PDOWrapper::cleanse($user_id))) {
            $ret = $this->create_org_from_sql_result($result[0]);
        }        
        return $ret;
    }

    public function getOrgMembers($org_id)
    {
        $ret = null;
        if ($result = PDOWrapper::call("getOrgMembers", PDOWrapper::cleanse($org_id))) {
            $ret = $result;
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
        PDOWrapper::call("acceptMemRequest", PDOWrapper::cleanse($user_id).",".PDOWrapper::cleanse($org_id));
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

    private static function createOrgFromSqlResult($result)
    {
        $org_data = array(
                    'id' => $result['id'],
                    'name' => $result['name'],
                    'home_page' => $result['home_page'],
                    'biography' => $result['biography']
        );

        return ModelFactory::buildModel("Organisation", $org_data);
    }
   
    
    public function save($org)
    {
        if (is_null($org->getId())) {
            return $this->insert($org);       //Create new organisation
        } else {
            return $this->update($org);       //Update the data row
        }
    }

    private function insert($org)
    {
        if ($org_id = PDOWrapper::call("organisationInsertAndUpdate",
                                "null,".PDOWrapper::cleanse($org->getHomePage())
                                .",".PDOWrapper::cleanse($org->getName())
                                .",".PDOWrapper::cleanse($org->getBiography()))) {
            return $this->find(array('id' => $org_id[0]['result']));
        } else {
            return null;
        }
    }

    private function update($org)
    {     
        return PDOWrapper::call("organisationInsertAndUpdate", PDOWrapper::cleanse($org->getId())
                                                    .",".PDOWrapper::cleanse($org->getHomePage())
                                                    .",".PDOWrapper::cleanse($org->getName())
                                                    .",".PDOWrapper::cleanse($org->getBiography()));
    }
    
    public function delete($orgID)
    {      
        return PDOWrapper::call("deleteOrg", PDOWrapper::cleanse($orgID));
    }
}
