<?php
require '../vendor/autoload.php';
require '../app/Settings.class.php';
require_once 'FormatEnum.php';
require_once 'HttpMethodEnum.php';
require_once 'XML/Serializer.php';


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
        
    }
    public  static function init_v0(){
        require_once 'v0/Users.php';
        Dispatcher::getDispatcher()->run();
    }
    
    public static function sendResponce($headers,$body,$code,$format){
        $format=  Dispatcher::getFormat($format);
        switch ($format){
            case FormatEnum::JSON: {
                echo json_encode($body);
                break;
            }
            case FormatEnum::XML: {
               try{
                  echo wddx_serialize_value($body);
               } catch (Exception $e)  {  echo $e;}  
                break;
            }
            
            case FormatEnum::HTML: {
               try{
                  echo htmlspecialchars(wddx_serialize_value($body));
               } catch (Exception $e)  {  echo $e;}  
                break;
            }
            
            case FormatEnum::PHP:{
               try{
                  echo serialize($body);
               } catch (Exception $e)  {  echo $e;}  
                break;
            }
        }
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
    public static function getFormat($format){
       if($format==".json") $format=  FormatEnum::JSON;
       else if($format==".xml") $format=  FormatEnum::XML;
       else if($format==".php") $format=  FormatEnum::PHP;
       else if($format==".html") $format=  FormatEnum::HTML;
       else if($format==".proto") $format=  FormatEnum::JSON;//change when implmented.
       else $format=  FormatEnum::JSON;
       return $format;
    }
    

}
Dispatcher::init();

?>
