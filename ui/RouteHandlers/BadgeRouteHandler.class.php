<?php

class BadgeRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();
        $app->get("/badge/list", array($this, "badgeList"))->name("badge-list");
    }

    public function badgeList()
    {
        $app = Slim::getInstance();

        $org_list = array();
        $orgDao = new OrganisationDao();
        $badgeDao = new BadgeDao();
        $badgeList = $badgeDao->getBadges();
        foreach ($badgeList as $badge) {
            if ($badge->getOwnerId() != null) {
                $org = $orgDao->getOrganisation(array('id' => $badge->getOwnerId()));;
                $org_list[$badge->getOwnerId()] = $org;
            }
        }

        $app->view()->setData("current_page", "badge-list");
        $app->view()->appendData(array(
                "badgeList" => $badgeList,
                "org_list"  => $org_list
        ));
        
        $app->render("badge-list.tpl");
    }
}
