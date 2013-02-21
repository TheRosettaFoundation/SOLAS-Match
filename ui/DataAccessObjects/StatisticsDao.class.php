<?php

require_once 'Common/lib/APIHelper.class.php';

class StatisticsDao
{
    private $client;
    private $siteApi;

    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    }

    public function getStats()
    {
        $ret = null;
        $request = "{$this->siteApi}/v0/stats";
        $ret = $this->client->call($request);
        return $ret;
    }

    public function getTotalTasks()
    {
        $ret = null;
        $request = "{$this->siteApi}/v0/stats/totalTasks";
        $ret = $this->client->call($request);
        return $ret;
    }

    public function getTotalArchivedTasks()
    {
        $ret = null;
        $request = "{$this->siteApi}/v0/stats/totalArchivedTasks";
        $ret = $this->client->call($request);
        return $ret;
    }

    public function getTotalClaimedTasks()
    {
        $ret = null;
        $request = "{$this->siteApi}/v0/stats/totalClaimedTasks";
        $ret = $this->client->call($request);
        return $ret;
    }

    public function getTotalUnclaimedTasks()
    {
        $ret = null;
        $request = "{$this->siteApi}/v0/stats/totalUnclaimedTasks";
        $ret = $this->client->call($request);
        return $ret;
    }

    public function getTotalUsers()
    {
        $ret = null;
        $request = "{$this->siteApi}/v0/stats/totalUsers";
        $ret = $this->client->call($request);
        return $ret;
    }

    public function getTotalOrgs()
    {
        $ret = null;
        $request = "{$this->siteApi}/v0/stats/totalOrgs";
        $ret = $this->client->call($request);
        return $ret;
    }
}
