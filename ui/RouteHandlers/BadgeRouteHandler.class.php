<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BadgeRouteHandler
{
    public function init()
    {
        global $app;
        
        $app->get(
            '/badge/list[/]',
            '\SolasMatch\UI\RouteHandlers\BadgeRouteHandler:badgeList')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('badge-list');
    }

    public function badgeList(Request $request, Response $response)
    {
        $template_data = [];

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

        $siteName = Common\Lib\Settings::get('site.name');

        $template_data = array_merge($template_data, array(
                "current_page"  => "badge-list",
                "badgeList"     => $badgeList,
                'siteName'      => $siteName,
                "org_list"      => $org_list
        ));
        
        UserRouteHandler::render("badge/badge-list.tpl", $template_data, $response);
        return $response;
    }
}

$route_handler = new BadgeRouteHandler();
$route_handler->init();
unset ($route_handler);
