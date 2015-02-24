<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;

class StaticRouteHandler
{
    public function init()
    {

        $app = \Slim\Slim::getInstance();

        $app->get('/static/statistics/', array($this, 'statistics'))->name("statistics");
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

    public function statistics()
    {
        $app = \Slim\Slim::getInstance();
        $extraScripts = "
<script type=\"text/javascript\" src=\"https://www.google.com/jsapi\"></script>
<script type=\"application/dart\" src=\"{$app->urlFor("home")}ui/dart/web/Scripts/statistics.dart\"></script>
<script src=\"{$app->urlFor("home")}ui/dart/build/packages/browser/dart.js\"></script>
<script src=\"{$app->urlFor("home")}ui/dart/build/packages/browser/interop.js\"></script>
        ";
        $app->view()->appendData(array(
            'extra_scripts' => $extraScripts
        ));
        $app->render("static/statistics.tpl");
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
         $currentUILanguage ="";
         $currentUILanguage = Common\Lib\UserSession::getUserLanguage();
         $langDao = new DAO\LanguageDao();
         $locales = array();
         //get default language code
         $locales[] = $langDao->getLanguageByCode(Common\Lib\Settings::get('site.default_site_language_code'));
         $defaultCode = $locales[0]->getCode();
         if (trim($currentUILanguage)=="") //if current language is nothing, set it to default language
         {
             $currentUILanguage = $defaultCode;
         }
         $includePath = __DIR__."/../localisation/FAQ_".$currentUILanguage.".html";
         $htmlFileExists = False;
         if (file_exists($includePath)) { //check whether FAQ html exists for the currently selected locale
             $htmlFileExists = True;
         } else {
             //fallback to English version
             $htmlFileExists = False;
             $includePath = __DIR__."/../localisation/FAQ_".$defaultCode.".html";
         }
         $app = \Slim\Slim::getInstance();
         $app->view()->appendData(array(
             'current_page' => 'faq', 
             'includeFile' => $includePath, 
             'htmlFileExist' => $htmlFileExists));
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
                Common\Lib\UserSession::setUserLanguage($post['language']);
            }
            $app->redirect($app->request()->getReferrer());
        } else {
            $app->response()->body(Common\Lib\UserSession::getUserLanguage());
        }
    }
    
    public function getUser()
    {
        if (!is_null(Common\Lib\UserSession::getCurrentUserID())) {
            $dao = new DAO\UserDao();

            \Slim\Slim::getInstance()->response()->body($dao->getUserDart(Common\Lib\UserSession::getCurrentUserID()));
        }
    }
    
    public function getUserHash()
    {
        if (!is_null(Common\Lib\UserSession::getAccessToken())) {
            \Slim\Slim::getInstance()->response()->body(Common\Lib\UserSession::getAccessToken()->getToken());
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

$route_handler = new StaticRouteHandler();
$route_handler->init();
unset ($route_handler);
