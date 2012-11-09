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
        $request = APIClient::API_VERSION."/badges";
        $response = $client->call($request);
        foreach($response as $stdObject) {
            $badgeList[] = $client->cast('Badge', $stdObject);
        }
        
        $app->view()->setData('current_page', 'badge-list');
        $app->view()->appendData(array('badgeList' => $badgeList));
        
        $app->render('badge-list.tpl');
    }
}
