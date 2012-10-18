<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of APIHelper
 *
 * @author sean
 */


file_exists('FormatEnum.php')? require_once 'FormatEnum.php':'api/FormatEnum.php';
file_exists('HttpMethodEnum.php')? require_once 'HttpMethodEnum.php':'api/HttpMethodEnum.php';
require_once '../app/lib/JsonToXml.php';




//require_once '../api/dispatcher.php';
class APIHelper {
    
    public static function serialiser($body,$format=".json"){
        $format=  APIHelper::getFormat($format);
        
        switch ($format){
            case FormatEnum::JSON: {
                
                return json_encode($body);
            }
            case FormatEnum::XML: {
                $data = json_encode($body);
                $data =Json_to_xml::convert($data,'fragment');
                return $data;
            }
            
            case FormatEnum::HTML: {
               return htmlspecialchars(wddx_serialize_value($body));
            }
            
            case FormatEnum::PHP:{
               return serialize($body);
            }
        }
    }
    public static function deserialiser($body,$format=".json"){
        $format=  APIHelper::getFormat($format); 
        switch ($format){
            case FormatEnum::JSON: {
                try{
                 return json_decode($body);
                }catch (Exception $e){
                    // change to exception and send responce from api
                  //  Dispatcher::sendResponce(null, "request format error. please resend in json or append .xml,.php,.html,.proto or .json as appropriate",400,".json");
                }
                break;
            }
            case FormatEnum::XML: {
                try{
//                $xslt = new XSLTProcessor();
//                $xslt->importStylesheet(simplexml_load_file("xmlToJson.xsl"));
//                $data= $xslt->transformToXml(new SimpleXMLElement()); 
                 return json_decode(json_encode(simplexml_load_string($body)->xpath("//item")));
                }catch (Exception $e){
                    //Dispatcher::sendResponce(null, "request format error. please resend in json or append .xml,.php,.html,.proto or .json as appropriate",400,".json");
                }
                break;
            }
            
            case FormatEnum::HTML: {
                try{
                 return  wddx_deserialize(htmlspecialchars_decode($body));
                }catch (Exception $e){
                    //Dispatcher::sendResponce(null, "request format error. please resend in json or append .xml,.php,.html,.proto or .json as appropriate",400,".json");
                }
                break;
            }
            
            
            
            case FormatEnum::PHP:{
              try{
                 return unserialize($body);
                }catch (Exception $e){
                    //Dispatcher::sendResponce(null, "request format error. please resend in json or append .xml,.php,.html,.proto or .json as appropriate",400,".json");
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
                return APIHelper::deserialiser($request->send()->getBody(),$format);
            }
            case HttpMethodEnum::GET:{
                $request = new HttpRequest($url,  HttpRequest::METH_GET);
                return APIHelper::deserialiser($request->send()->getBody(),$format);
            }
            case HttpMethodEnum::POST:{
                $request = new HttpRequest($url,  HttpRequest::METH_POST);
                $request->setRawPostData(Dispatcher::serialiser($data,$format));
                return APIHelper::deserialiser($request->send()->getBody(),$format);
            }
            case HttpMethodEnum::PUT:{
                $request = new HttpRequest($url,  HttpRequest::METH_PUT);
                $request->setPutData(Dispatcher::serialiser($data,$format));
                return APIHelper::deserialiser($request->send()->getBody(),$format);
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

    public static function cast($destination, $sourceObject)
    {
        if (is_string($destination)) {
            $destination = new $destination();
        }
        $sourceReflection = new ReflectionObject($sourceObject);
        $destinationReflection = new ReflectionObject($destination);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);
            $name = $sourceProperty->getName();
            $value = $sourceProperty->getValue($sourceObject);
            if ($destinationReflection->hasProperty($name)) {
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);
                $propDest->setValue($destination,$value);
            } else {
                $destination->$name = $value;
            }
        }
        return $destination;
    }
}
