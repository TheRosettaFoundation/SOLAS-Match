<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;

class BadgeRouteHandler
{
    public function init()
    {
        $app = \Slim\Slim::getInstance();
        $middleware = new Lib\Middleware();
        
        $app->get(
            "/badge/list/",
            array($middleware, "authUserIsLoggedIn"),
            array($this, "badgeList")
        )->name("badge-list");
    }

    public function badgeList()
    {
        $app = \Slim\Slim::getInstance();

        $org_list = array();
        $orgDao = new DAO\OrganisationDao();
        $badgeDao = new DAO\BadgeDao();
        $badgeList = $badgeDao->getBadges();
        foreach ($badgeList as $badge) {
            if ($badge->getOwnerId() != null) {
                $org = $orgDao->getOrganisation($badge->getOwnerId());
                $org_list[$badge->getOwnerId()] = $org;
            }
        }

        $siteName = \Settings::get('site.name');

        $app->view()->appendData(array(
                "current_page"  => "badge-list",
                "badgeList"     => $badgeList,
                'siteName'      => $siteName,
                "org_list"      => $org_list
        ));
        
        $app->render("badge/badge-list.tpl");
    }
}

$route_handler = new BadgeRouteHandler();
$route_handler->init();
unset ($route_handler);
