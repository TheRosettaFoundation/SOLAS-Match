<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\API\Lib as Lib;
use \SolasMatch\Common as Common;

require_once __DIR__."/BaseDao.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";

class SubscriptionDao extends BaseDao
{
    public function number_of_projects_ever($org_id)
    {
        $ret = 0;
        $result = Lib\PDOWrapper::call('number_of_projects_ever', Lib\PDOWrapper::cleanse($org_id));
        if (!empty($result)) {
            $ret = $result[0];
        }
        return $ret;
    }

    public number_of_projects_within_year($org_id)
    {
        $ret = 0;
        $result = Lib\PDOWrapper::call('number_of_projects_within_year', Lib\PDOWrapper::cleanse($org_id));
        if (!empty($result)) {
            $ret = $result[0];
        }
        return $ret;
    }
}
