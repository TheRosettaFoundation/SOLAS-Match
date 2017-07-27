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

    public function search_user($name)
    {
        $result = LibAPI\PDOWrapper::call('search_user', LibAPI\PDOWrapper::cleanseNullOrWrapStr($name));
        return $result;
    }

    public function search_organisation($name)
    {
        $result = LibAPI\PDOWrapper::call('search_organisation', LibAPI\PDOWrapper::cleanseNullOrWrapStr($name));
error_log(LibAPI\PDOWrapper::cleanseNullOrWrapStr($name) . ' Expectáéíóú');
        return $result;
    }
}
