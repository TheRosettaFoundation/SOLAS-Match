<?php
require 'vendor/autoload.php';

mb_internal_encoding("UTF-8");

SmartyView::$smartyDirectory = 'vendor/smarty/smarty/distribution/libs';
SmartyView::$smartyCompileDirectory = 'templating/templates_compiled';
SmartyView::$smartyTemplatesDirectory = 'templating/templates';
SmartyView::$smartyExtensions = array(
    'vendor/slim/extras/Views/Extension/Smarty'
);

\DrSlump\Protobuf::autoload();

require_once '../Common/Settings.class.php';
require_once "../Common/lib/ModelFactory.class.php";
require_once "../Common/lib/BadgeTypes.class.php";
require_once 'FormatEnum.php';
require_once 'HttpMethodEnum.php';
require_once 'APIHelper.php';

class Dispatcher {
    private static $apiDispatcher = null;
    public static function  getDispatcher(){
         if( Dispatcher::$apiDispatcher == null){
            Dispatcher::$apiDispatcher = new Slim(array(
                 'debug' => true
                ,'view' => new SmartyView()
                ,'mode' => 'development' // default is development. TODO get from config file, or set in environment...... $_ENV['SLIM_MODE'] = 'production';
            ));
            $app = Dispatcher::$apiDispatcher;
            Dispatcher::$apiDispatcher->configureMode('production', function () use ($app) {
                $app->config(array(
                    'log.enable' => true,
                    'log.path' => '../../logs', // Need to set this...
                    'debug' => false
                ));
            });

            $app->configureMode('development', function () use ($app) {
                $app->config(array(
                    'log.enable' => false,
                    'debug' => true
                ));
            });
            
        }
        return Dispatcher::$apiDispatcher;
    }
    public static function init(){
       $path = Dispatcher::getDispatcher()->request()->getResourceUri();
       $path = explode("/", $path);
       $path =$path[1];
       $initFunc = "Dispatcher::init_".$path;
       call_user_func($initFunc); 
       Dispatcher::getDispatcher()->run();
        
    }
    public  static function init_v0(){
        require_once 'v0/Users.php';
        require_once 'v0/Tasks.php';
        require_once 'v0/Tags.php';
        require_once 'v0/Badges.php';
        require_once 'v0/Orgs.php';
        require_once 'v0/LoginAPI.php'; 
        require_once 'v0/RegisterAPI.php';
        require_once 'v0/Langs.php';
        require_once 'v0/Countries.php';
        require_once 'v0/PasswordReset.php';
        require_once 'v0/Stats.php';
    }
    
    public static function sendResponce($headers,$body,$code=200,$format=".json"){
        $response = Dispatcher::getDispatcher()->response();
        
        $formatCode=  APIHelper::getFormat($format); 
        switch ($formatCode){
            case FormatEnum::JSON: {
                $response['Content-Type'] = 'application/json';
                $body=APIHelper::serialiser($body,$format);
                break;
            }
            case FormatEnum::XML: {
               try{
                  $response['Content-Type'] = 'application/xml';
                   $body=APIHelper::serialiser($body,$format);
               } catch (Exception $e)  {  echo $e;}  
                break;
            }
            
            case FormatEnum::HTML: {
               try{
                   $response['Content-Type'] = 'text/html';
                   $body=APIHelper::serialiser($body,$format);
               } catch (Exception $e)  {  echo $e;}  
                break;
            }
            
            case FormatEnum::PHP:{
               try{
                  $response['Content-Type'] = 'text/plain';
                  $body=APIHelper::serialiser($body,$format);
               } catch (Exception $e)  {  echo $e;}  
                break;
            }
        }
        
        if($headers!=null){
            foreach($headers as $key=>$val){
                $response[$key]=$val;
            }
        }
        $response->body($body);
        
        if($code!=null)$response->status($code);
    }
    
    
    
    public static function register($httpMethod,$url,$function){
        switch($httpMethod){
            case HttpMethodEnum::DELETE:{
                Dispatcher::getDispatcher()->delete($url,$function);
                break;
            }
            case HttpMethodEnum::GET:{
                    Dispatcher::getDispatcher()->get($url,$function);
                break;
            }
            case HttpMethodEnum::POST:{
                Dispatcher::getDispatcher()->post($url,$function);
                break;
            }
            case HttpMethodEnum::PUT:{
                Dispatcher::getDispatcher()->put($url,$function);
                break;
            }
        }
    }
    
    public static function registerNamed($httpMethod,$url,$function,$name){
        
        switch($httpMethod){
            case HttpMethodEnum::DELETE:{
                Dispatcher::getDispatcher()->delete($url,$function)->name($name);
                break;
            }
            case HttpMethodEnum::GET:{
                    Dispatcher::getDispatcher()->get($url,$function)->name($name);
                break;
            }
            case HttpMethodEnum::POST:{
                Dispatcher::getDispatcher()->post($url,$function)->name($name);
                break;
            }
            case HttpMethodEnum::PUT:{
                Dispatcher::getDispatcher()->put($url,$function)->name($name);
                break;
            }
        }
    }
   

}
Dispatcher::init();
