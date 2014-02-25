<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;

class StaticRouteHandeler
{
    public function init()
    {

        $app = \Slim\Slim::getInstance(); 

        $app->get("/static/privacy/", array($this, "privacy"))->name("privacy");
        $app->get("/static/terms/", array($this, "terms"))->name("terms");
        $app->get("/static/faq/", array($this, "faq"))->name("faq");
        $app->get("/static/videos/", array($this, "videos"))->name("videos");
        $app->get("/static/siteLanguage/", array($this, "siteLanguage"))->via("POST")->name("siteLanguage");
        $app->get("/static/getDefaultStrings/", array($this, "getDefaultStrings"))->name("staticGetDefaultStrings");
        $app->get("/static/getUserStrings/", array($this, "getUserStrings"))->name("staticGetUserStrings");
        $app->get("/static/getUser/", array($this, "getUser"))->name("staticGetUser");
        $app->get("/static/getUserHash/", array($this, "getUserHash"))->name("staticGetUserHash");
        $app->notFound("\SolasMatch\UI\Lib\Middleware::notFound");
    }

    public function privacy()
    {
         $app = \Slim\Slim::getInstance();
         $app->render("static/privacy.tpl");
    }
    
    public function terms()
    {
         $app = \Slim\Slim::getInstance();
         $app->render("static/terms.tpl");
    }
    
    public function faq()
    {
         $app = \Slim\Slim::getInstance();
         $app->view()->appendData(array('current_page' => 'faq'));
         $app->render("static/FAQ.tpl");
    }
    
    public function videos()
    {
         $app = \Slim\Slim::getInstance();
         $app->view()->setData("current_page", "videos");
         $app->render("static/videos.tpl");
    }
    
    public function siteLanguage()
    {

        $app = \Slim\Slim::getInstance(); 
        if ($post = $app->request()->post()) {
            if (isset($post['language'])) {
                \UserSession::setUserLanguage($post['language']);
            }
            $app->redirect($app->request()->getReferrer());
        } else {
            $app->response()->body(\UserSession::getUserLanguage());
        }
    }
    
    public function getUser()
    {
        if (!is_null(\UserSession::getCurrentUserID())) {
            $dao = new DAO\UserDao();

            \Slim\Slim::getInstance()->response()->body($dao->getUserDart(\UserSession::getCurrentUserID()));
        }
    }
    
    public function getUserHash()
    {
        if (!is_null(\UserSession::getAccessToken())) {
            \Slim\Slim::getInstance()->response()->body(\UserSession::getAccessToken()->getToken()); 
        }
    }
    
    public function getDefaultStrings()
    {
        \Slim\Slim::getInstance()->response()->body(Lib\Localisation::getDefaultStrings());
    }

    public function getUserStrings()
    {
        \Slim\Slim::getInstance()->response()->body(Lib\Localisation::getUserStrings());
    }
}

$route_handler = new StaticRouteHandeler();
$route_handler->init();
unset ($route_handler);
