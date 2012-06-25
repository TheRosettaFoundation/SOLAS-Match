<?php

require('models/Organisation.class.php');

class OrganisationDao {
    public function find($params) {
        $query = null;
        $db = new MySQLWrapper();
        $db->init();
        if (isset($params['id'])) {
            $query = 'SELECT *
                        FROM organisation
                        WHERE id='.$params['id'];
        }

        $ret = null;
        if ($result = $db->Select($query)) {

            $ret = $this->create_org_from_sql_result($result);

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

    private function create_org_from_sql_result($result) {
        $org_data = array(
                    'id' => $result[0]['id'],
                    'name' => $result[0]['name'],
                    'home_page' => $result[0]['home_page'],
                    'biography' => $result[0]['biography']
        );

        return new Organisation($org_data);
    }
}
