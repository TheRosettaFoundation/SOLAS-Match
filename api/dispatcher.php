<?php
require '../vendor/autoload.php';
require '../app/Settings.class.php';
require_once 'FormatEnum.php';
require_once 'HttpMethodEnum.php';
require_once '../app/APIHelper.php';



class Dispatcher {
    private static $apiDispatcher = null;
    public static function  getDispatcher(){
         if( Dispatcher::$apiDispatcher == null){
            Dispatcher::$apiDispatcher = new Slim(array(
                'debug' => true,
                'mode' => 'development' // default is development. TODO get from config file, or set in environment...... $_ENV['SLIM_MODE'] = 'production';
            ));
        }
        return Dispatcher::$apiDispatcher;
    }
    public static function init(){
       $path = $_SERVER['PATH_INFO'];
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
        $response->status($code);
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

?>
