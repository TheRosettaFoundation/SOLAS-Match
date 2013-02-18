<?php

class StatisticsDao
{
    public function getTotalTasks()
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/stats/totalTasks";
        $ret = $client->call($request);
        return $ret;
    }

    public function getTotalArchivedTasks()
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/stats/totalArchivedTasks";
        $ret = $client->call($request);
        return $ret;
    }

    public function getTotalClaimedTasks()
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/stats/totalClaimedTasks";
        $ret = $client->call($request);
        return $ret;
    }

    public function getTotalUnclaimedTasks()
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/stats/totalUnclaimedTasks";
        $ret = $client->call($request);
        return $ret;
    }

    public function getTotalUsers()
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/stats/totalUsers";
        $ret = $client->call($request);
        return $ret;
    }

    public function getTotalOrgs()
    {
        $ret = null;
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");
        $request = "$siteApi/v0/stats/totalOrgs";
        $ret = $client->call($request);
        return $ret;
    }
}
