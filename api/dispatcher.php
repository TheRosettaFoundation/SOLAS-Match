<?php
require '../vendor/autoload.php';
require '../app/Settings.class.php';
require_once 'FormatEnum.php';
require_once 'XML/Serializer.php';

class Dispatcher {
    private static $apiDispatcher = null;
    public static function getDispatcher(){
         if( Dispatcher::$apiDispatcher == null){
            Dispatcher::$apiDispatcher = new Slim(array(
                'debug' => true,
                'mode' => 'development' // default is development. TODO get from config file, or set in environment...... $_ENV['SLIM_MODE'] = 'production';
            ));
        }
        return Dispatcher::$apiDispatcher;
    }

    public  static function init(){
        require_once 'Users.php';
        Dispatcher::getDispatcher()->run();
    }
    
    public static function sendResponce($headers,$body,$code,$format){
        switch ($format){
            case FormatEnum::JSON: {
                echo json_encode($body);
                break;
            }
            case FormatEnum::XML: {
               try{
                $serializer = new XML_Serializer();
                $serializer->serialize($body);
                echo $serializer->getSerializedData();
               } catch (Exception $e)  {  echo $e;}  
                break;
            }
        }
    }
    

}
Dispatcher::init();

?>
