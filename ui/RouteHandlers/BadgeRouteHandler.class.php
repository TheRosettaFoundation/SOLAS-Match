<?php

class BadgeRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();
        $app->get('/badge/list', array($this, 'badgeList'))->name('badge-list');
    }

    public function badgeList()
    {
        $app = Slim::getInstance();
        $client = new APIHelper(Settings::get("ui.api_format"));
        $siteApi = Settings::get("site.api");

        $badgeList = array();
        $org_list = array();
        $request = "$siteApi/v0/badges";
        $response = $client->call($request);
        $badgeList = $client->cast(array("Badge"), $response);
        foreach ($badgeList as $badge) {
            if ($badge->getOwnerId() != null) {
                $mRequest = "$siteApi/v0/orgs/".$badge->getOwnerId();
                $mResponse = $client->call($mRequest);
                $org = $client->cast('Organisation', $mResponse);
                $org_list[$badge->getOwnerId()] = $org;
            }
        }

        $app->view()->setData('current_page', 'badge-list');
        $app->view()->appendData(array(
                'badgeList' => $badgeList,
                'org_list'  => $org_list
        ));
        
        $app->render('badge-list.tpl');
    }
}
