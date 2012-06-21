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
            $org_data = array(
                    'id' => $result[0]['id'],
                    'name' => $result[0]['name']
            );
            $ret = new Organisation($org_data);
        }
        return $ret;
    }
}
