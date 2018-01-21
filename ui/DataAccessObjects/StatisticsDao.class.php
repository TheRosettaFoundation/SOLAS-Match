<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\Common as Common;
use \SolasMatch\API\Lib as LibAPI;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/BaseDao.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";

class StatisticsDao extends BaseDao
{
    public function __construct()
    {
        $this->client = new Common\Lib\APIHelper(Common\Lib\Settings::get("ui.api_format"));
        $this->siteApi = Common\Lib\Settings::get("site.api");
    }

    public function getStats()
    {
        $stats = Common\Lib\CacheHelper::getCached(
            Common\Lib\CacheHelper::STATISTICS,
            Common\Enums\TimeToLiveEnum::HOUR,
            function ($args) {
                $request = "{$args[1]}v0/stats";
                return $args[0]->call(array("\SolasMatch\Common\Protobufs\Models\Statistic"), $request);
            },
            array($this->client, $this->siteApi)
        );
        return $stats;
    }
    
    public function getStat($stat)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/stats/$stat";
        $ret = $this->client->call("\SolasMatch\Common\Protobufs\Models\Statistic", $request);
        return $ret;
    }

    public function getUsers()
    {
        $result = LibAPI\PDOWrapper::call('getUsers', '');
        return $result;
    }

    public function active_now()
    {
        $result = LibAPI\PDOWrapper::call('active_now', '');
        return $result;
    }

    public function active_users()
    {
        $result = LibAPI\PDOWrapper::call('active_users', '');
        return $result;
    }

    public function unclaimed_tasks()
    {
        $result = LibAPI\PDOWrapper::call('unclaimed_tasks', '');
        return $result;
    }

    public function user_languages($code)
    {
        $result = LibAPI\PDOWrapper::call('user_languages', LibAPI\PDOWrapper::cleanseNullOrWrapStr($code));
        return $result;
    }

    public function user_task_languages($code)
    {
        $result = LibAPI\PDOWrapper::call('user_task_languages', LibAPI\PDOWrapper::cleanseNullOrWrapStr($code));
        return $result;
    }

    public function community_stats()
    {
        $result = LibAPI\PDOWrapper::call('community_stats', '');
        return $result;
    }

    public function community_stats_secondary()
    {
        $result = LibAPI\PDOWrapper::call('community_stats_secondary', '');
        return $result;
    }

    public function community_stats_words()
    {
        $result = LibAPI\PDOWrapper::call('community_stats_words', '');
        return $result;
    }

    public function all_orgs()
    {
        $result = LibAPI\PDOWrapper::call('all_orgs', '');
        return $result;
    }

    public function all_org_admins()
    {
        $result = LibAPI\PDOWrapper::call('all_org_admins', '');
        return $result;
    }

    public function all_org_members()
    {
        $result = LibAPI\PDOWrapper::call('all_org_members', '');
        return $result;
    }

    public function org_stats_words()
    {
        $result = LibAPI\PDOWrapper::call('org_stats_words', '');
        return $result;
    }

    public function org_stats_languages()
    {
        $result = LibAPI\PDOWrapper::call('org_stats_languages', '');
        return $result;
    }

    public function users_active()
    {
        $result = LibAPI\PDOWrapper::call('users_active', '');
        return $result;
    }

    public function users_signed_up()
    {
        $result = LibAPI\PDOWrapper::call('users_signed_up', '');
        return $result;
    }

    public function new_tasks()
    {
        $result = LibAPI\PDOWrapper::call('new_tasks', '');
        return $result;
    }

    public function average_time_to_assign()
    {
        $result = LibAPI\PDOWrapper::call('average_time_to_assign', '');
        return $result;
    }

    public function average_time_to_turnaround()
    {
        $result = LibAPI\PDOWrapper::call('average_time_to_turnaround', '');
        return $result;
    }

    public function search_user($name)
    {
        $result = LibAPI\PDOWrapper::call('search_user', LibAPI\PDOWrapper::cleanseNullOrWrapStr($name));
        return $result;
    }

    public function search_organisation($name)
    {
        $result = LibAPI\PDOWrapper::call('search_organisation', LibAPI\PDOWrapper::cleanseNullOrWrapStr($name));
        return $result;
    }

    public function search_project($name)
    {
        $result = LibAPI\PDOWrapper::call('search_project', LibAPI\PDOWrapper::cleanseNullOrWrapStr($name));
        return $result;
    }
}
