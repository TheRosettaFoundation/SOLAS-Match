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

    private function create_org_from_sql_result($result) {
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
