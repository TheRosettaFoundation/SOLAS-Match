<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StaticRouteHandler
{
    public function init()
    {
        global $app;

        $app->get('/static/statistics/', '\SolasMatch\UI\RouteHandlers\UserRouteHandler:statistics'))->name("statistics");
            ->setName('statistics');

        $app->get("/static/privacy/", '\SolasMatch\UI\RouteHandlers\UserRouteHandler:privacy'))->name("privacy");
            ->setName('privacy');

        $app->get("/static/terms/", '\SolasMatch\UI\RouteHandlers\UserRouteHandler:terms'))->name("terms");
            ->setName('terms');

        $app->get("/static/videos/", '\SolasMatch\UI\RouteHandlers\UserRouteHandler:videos'))->name("videos");
            ->setName('videos');

        $app->map(['GET', 'POST'],"/static/siteLanguage/", '\SolasMatch\UI\RouteHandlers\UserRouteHandler:siteLanguage'))->via("POST")->name("siteLanguage");
            ->setName('siteLanguage');

        $app->get("/static/getDefaultStrings/", '\SolasMatch\UI\RouteHandlers\UserRouteHandler:getDefaultStrings'))->name("staticGetDefaultStrings");
            ->setName('staticGetDefaultStrings');

        $app->get("/static/getUserStrings/", '\SolasMatch\UI\RouteHandlers\UserRouteHandler:getUserStrings'))->name("staticGetUserStrings");
            ->setName('staticGetUserStrings');

        $app->get("/static/getUserHash/", '\SolasMatch\UI\RouteHandlers\UserRouteHandler:getUserHash'))->name("staticGetUserHash");
            ->setName('staticGetUserHash');
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
