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

        $badge_dao = new BadgeDao();
        $badgeList = $badge_dao->getAllBadges();
        
        $app->view()->setData('current_page', 'badge-list');
        $app->view()->appendData(array('badgeList' => $badgeList));
        
        $app->render('badge-list.tpl');
    }
}
