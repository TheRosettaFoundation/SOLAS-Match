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

        $app->get('/static/statistics[/]', '\SolasMatch\UI\RouteHandlers\StaticRouteHandler:statistics')
            ->setName('statistics');

        $app->get('/static/privacy[/]', '\SolasMatch\UI\RouteHandlers\StaticRouteHandler:privacy')
            ->setName('privacy');

        $app->get('/static/terms[/]', '\SolasMatch\UI\RouteHandlers\StaticRouteHandler:terms')
            ->setName('terms');

        $app->get('/static/getDefaultStrings[/]', '\SolasMatch\UI\RouteHandlers\StaticRouteHandler:getDefaultStrings')
            ->setName('staticGetDefaultStrings');

        $app->get('/static/getUserStrings[/]', '\SolasMatch\UI\RouteHandlers\StaticRouteHandler:getUserStrings')
            ->setName('staticGetUserStrings');

        $app->get('/static/getUserHash[/]', '\SolasMatch\UI\RouteHandlers\StaticRouteHandler:getUserHash')
            ->setName('staticGetUserHash');
    }

    public function statistics(Request $request, Response $response)
    {
        global $app;
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

    public function privacy(Request $request, Response $response)
    {
         global $app;
         $app->render("static/privacy.tpl");
    }
    
    public function terms(Request $request, Response $response)
    {
         global $app;
         $app->render("static/terms.tpl");
    }
    
    public function siteLanguage(Request $request, Response $response)
    {
        global $app;
        if ($post = $app->request()->post()) {
            if (isset($post['language'])) {
                Common\Lib\UserSession::setUserLanguage($post['language']);
            }
            $app->redirect($app->request()->getReferrer());
        } else {
            $app->response()->body(Common\Lib\UserSession::getUserLanguage());
        }
    }

    public function getUserHash(Request $request, Response $response)
    {
        if (!is_null(Common\Lib\UserSession::getAccessToken())) {
            \Slim\Slim::getInstance()->response()->body(Common\Lib\UserSession::getAccessToken()->getToken());
        }
    }
    
    public function getDefaultStrings(Request $request, Response $response)
    {
        \Slim\Slim::getInstance()->response()->body(Lib\Localisation::getDefaultStrings());
    }

    public function getUserStrings(Request $request, Response $response)
    {
        \Slim\Slim::getInstance()->response()->body(Lib\Localisation::getUserStrings());
    }
}

$route_handler = new StaticRouteHandler();
$route_handler->init();
unset ($route_handler);
