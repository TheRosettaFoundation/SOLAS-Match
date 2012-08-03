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

    public function getOrgByUser($user_id) {
        $ret = null;
        $db = new MySQLWrapper();
        $db->init();
        $query = 'SELECT *
                    FROM organisation
                    WHERE id IN (SELECT organisation_id
                                    FROM organisation_member
                                    WHERE user_id='.$user_id.'
        )';
        
        if($result = $db->Select($query)) {
            $ret = $this->create_org_from_sql_result($result);
        }
        return $ret;
    }

    public function getOrgMembers($org_id) {
        $ret = null;
        $db = new MySQLWrapper();
        $db->init();
        $query = 'SELECT user_id
                    FROM organisation_member
                    WHERE organisation_id='.$db->cleanse($org_id);

        if($result = $db->Select($query)) {
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
        $db = new MySQLWrapper();
        $db->init();
        $insert = array();
        $insert['name'] = $org->getName();
        $insert['home_page'] = $org->getHomePage();
        $insert['biography'] = $org->getBiography();

        if($org_id = $db->Insert('organisation', $insert)) {
            return $this->find(array('id' => $org_id));
        } else {
            return null;
        }
    }

    private function _update($org) {
        $db = new MySQLWrapper();
        $db->init();
        $update = 'UPDATE organisation
                    SET name='.$db->cleanseWrapStr($org->getName()).',
                    home_page='.$db->cleanseWrapStr($org->getHomePage()).',
                    biography='.$db->cleanseWrapStr($org->getBiography()).'
                    WHERE id='.$db->cleanse($org->getId()).'
                    LIMIT 1';
        return $db->Update($update);
    }
}
