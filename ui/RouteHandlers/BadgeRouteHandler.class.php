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
        $client = new APIClient();

        $badgeList = array();
        $org_list = array();
        $request = APIClient::API_VERSION."/badges";
        $response = $client->call($request);
        foreach ($response as $stdObject) {
            $badge = $client->cast('Badge', $stdObject);
            $badgeList[] = $badge;
            if ($badge->getOwnerId() != null) {
                $mRequest = APIClient::API_VERSION."/orgs/".$badge->getOwnerId();
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
