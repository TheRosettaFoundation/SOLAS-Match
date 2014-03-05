<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\Common as Common;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/BaseDao.php";

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
                return $args[0]->call(array("Statistic"), $request);
            },
            array($this->client, $this->siteApi)
        );
        return $stats;
    }
    
    public function getStat($stat)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/stats/$stat";
        $ret = $this->client->call("Statistic", $request);
        return $ret;
    }
}
