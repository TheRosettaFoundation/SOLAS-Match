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
        require_once 'v0/Tasks.php';
        require_once 'v0/Tags.php';
        require_once 'v0/Badges.php';
        require_once 'v0/Orgs.php';
        Dispatcher::getDispatcher()->run();
    }
    
    public static function sendResponce($headers,$body,$code=200,$format=".json"){
        $response = Dispatcher::getDispatcher()->response();
        
        $formatCode=  Dispatcher::getFormat($format); 
        switch ($formatCode){
            case FormatEnum::JSON: {
                $response['Content-Type'] = 'application/json';
                $body=Dispatcher::serialiser($body,$format);
                break;
            }
            case FormatEnum::XML: {
               try{
                  $response['Content-Type'] = 'application/xml';
                   $body=Dispatcher::serialiser($body,$format);
               } catch (Exception $e)  {  echo $e;}  
                break;
            }
            
            case FormatEnum::HTML: {
               try{
                   $response['Content-Type'] = 'text/html';
                   $body=Dispatcher::serialiser($body,$format);
               } catch (Exception $e)  {  echo $e;}  
                break;
            }
            
            case FormatEnum::PHP:{
               try{
                  $response['Content-Type'] = 'text/plain';
                  $body=Dispatcher::serialiser($body,$format);
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
    
    public static function serialiser($body,$format=".json"){
        $response = Dispatcher::getDispatcher()->response();
        
        $format=  Dispatcher::getFormat($format); 
        switch ($format){
            case FormatEnum::JSON: {
                return json_encode($body);
            }
            case FormatEnum::XML: {
               return wddx_serialize_value($body);
            }
            
            case FormatEnum::HTML: {
               return htmlspecialchars(wddx_serialize_value($body));
            }
            
            case FormatEnum::PHP:{
               return serialize($body);
            }
        }
    }
    public static function deserialiser($data,$format=".json"){
        $format=  Dispatcher::getFormat($format); 
        switch ($format){
            case FormatEnum::JSON: {
                try{
                 return json_encode($body);
                }catch (Exception $e){
                    Dispatcher::sendResponce(null, "request format error. please resend in json or append .xml,.php,.html,.proto or .json as appropriate",400,".json");
                }
                break;
            }
            case FormatEnum::XML: {
                try{
                 return wddx_deserialize($body);
                }catch (Exception $e){
                    Dispatcher::sendResponce(null, "request format error. please resend in json or append .xml,.php,.html,.proto or .json as appropriate",400,".json");
                }
                break;
            }
            
            case FormatEnum::HTML: {
                try{
                 return  wddx_deserialize(htmlspecialchars_decode($body));
                }catch (Exception $e){
                    Dispatcher::sendResponce(null, "request format error. please resend in json or append .xml,.php,.html,.proto or .json as appropriate",400,".json");
                }
                break;
            }
            
            
            
            case FormatEnum::PHP:{
              try{
                 return unserialize($body);
                }catch (Exception $e){
                    Dispatcher::sendResponce(null, "request format error. please resend in json or append .xml,.php,.html,.proto or .json as appropriate",400,".json");
                }
                break;
            }
        }
        
    }
    
    public static function call($url,$data,$httpMethod,$queryArgs=array(), $format=".php"){
        $url.=$format."?";
        foreach ($queryArgs as $key=>$val) $url.=$key."=".$val."&";
         switch($httpMethod){
            case HttpMethodEnum::DELETE:{
                $request = new HttpRequest($url,  HttpRequest::METH_DELETE);
                return Dispatcher::deserialiser($request->send()->getBody(),$format);
            }
            case HttpMethodEnum::GET:{
                $request = new HttpRequest($url,  HttpRequest::METH_GET);
                return Dispatcher::deserialiser($request->send()->getBody(),$format);
            }
            case HttpMethodEnum::POST:{
                $request = new HttpRequest($url,  HttpRequest::METH_POST);
                $request->setRawPostData(Dispatcher::serialiser($data,$format));
                return Dispatcher::deserialiser($request->send()->getBody(),$format);
            }
            case HttpMethodEnum::PUT:{
                $request = new HttpRequest($url,  HttpRequest::METH_PUT);
                $request->setPutData(Dispatcher::serialiser($data,$format));
                return Dispatcher::deserialiser($request->send()->getBody(),$format);
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
       elseif(strcasecmp($format,'.xml')==0) $format=  FormatEnum::XML;
       elseif(strcasecmp($format,'.php')==0) $format=  FormatEnum::PHP;
       elseif(strcasecmp($format,'.html')==0) $format=  FormatEnum::HTML;
       elseif(strcasecmp($format,'.proto')==0) $format=  FormatEnum::JSON;//change when implmented.
       else $format=  FormatEnum::JSON;
       return $format;
    }
    

}
Dispatcher::init();

?>
