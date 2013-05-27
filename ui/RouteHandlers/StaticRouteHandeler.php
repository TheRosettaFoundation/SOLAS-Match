<?php

class StaticRouteHandeler
{
    public function init()
    {
        $app = Slim::getInstance();       
        
        $app->get("/static/privacy", array($this, "privacy"))->name("privacy");
        $app->get("/static/terms", array($this, "terms"))->name("terms");
        $app->get("/static/videos", array($this, "videos"))->name("videos");
        $app->notFound(array("Middleware::notFound"));
    }

    public function privacy()
    {
         $app = Slim::getInstance();
         $app->render("static/privacy.tpl");
    }
    
    public function terms()
    {
         $app = Slim::getInstance();
         $app->render("static/terms.tpl");
    }
    
    public function videos()
    {
         $app = Slim::getInstance();
         $app->view()->setData("current_page", "videos");
         $app->render("static/videos.tpl");
    }
}

$route_handler = new StaticRouteHandeler();
$route_handler->init();
unset ($route_handler);