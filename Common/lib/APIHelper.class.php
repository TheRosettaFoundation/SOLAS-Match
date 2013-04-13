<?php

require_once __DIR__."/FormatEnum.php";
require_once __DIR__."/JSONSerializer.class.php";
require_once __DIR__."/XMLSerializer.class.php";
require_once __DIR__."/HTMLSerializer.class.php";
require_once __DIR__."/PHPSerializer.class.php";
require_once __DIR__."/ProtobufSerializer.class.php";

class APIHelper
{
    private $_serializer;

    public function __construct($format)
    {
        $format = self::getFormatFromString($format);

        switch ($format)
        {
            default:
            case FormatEnum::JSON:
                $this->_serializer = new JSONSerializer();
                break;
            case FormatEnum::XML:
                $this->_serializer = new XMLSerializer();
                break;
            case FormatEnum::HTML:
                $this->_serializer = new HTMLSerializer();
                break;
            case FormatEnum::PHP:
                $this->_serializer = new PHPSerializer();
                break;
            case FormatEnum::PROTOBUFS:
                $this->_serializer = new ProtobufSerializer();
                break;
        }
    }

    public function call($destination,$url, $method = HttpMethodEnum::GET,
             $data = null, $query_args = array(), $file = null)
    {
        $url = $url.$this->_serializer->getFormat()."/?";
//        $request = new HTTP_Request2($url, $method);
        if (count($query_args) > 0) {
//            $requestUrl = $request->getUrl();
            $first= true;
            foreach ($query_args as $key=>$val){
                if(!$first)$url.="&";
                $url.="$key=$val";
            }
            
//            $requestUrl->setQueryVariables($query_args);
        }
        $re = curl_init($url);
        
//        switch ($method){
//        case HTTP_Request2::METHOD_GET: curl_setopt($re, CURLOPT_CUSTOMREQUEST, "GET"); break;
//        case HTTP_Request2::METHOD_POST: curl_setopt($re, CURLOPT_CUSTOMREQUEST, "POST"); break;
//        case HTTP_Request2::METHOD_PUT: curl_setopt($re, CURLOPT_CUSTOMREQUEST, "PUT"); break;
//        case HTTP_Request2::METHOD_DELETE: curl_setopt($re, CURLOPT_CUSTOMREQUEST, "DELETE"); break;
//        }
        curl_setopt($re, CURLOPT_CUSTOMREQUEST, $method);
       
        $lenght = 0;
        if (!is_null($data) && "null" != $data) {
            $data=$this->_serializer->serialize($data);
            curl_setopt($re, CURLOPT_POSTFIELDS, $data);
            $lenght=strlen($data);
//            $request->setBody($data);
        }
        
        if (!is_null($file)) {
//            $request->setBody($file);
            $lenght=strlen($file);
            curl_setopt($re, CURLOPT_POSTFIELDS, $file);
        }
        
        
        curl_setopt($re, CURLOPT_HTTPHEADER, array(                                                                          
            $this->_serializer->getContentType(),                                                                                
            'Content-Length: ' . $lenght)                                                                       
        );
        curl_setopt($re, CURLOPT_RETURNTRANSFER, true); 
        $res=curl_exec($re);
//        $response = $request->send();
        $response_data = $this->_serializer->deserialize($res,$destination);
        return $response_data;
    }

    public function cast($destination, $sourceObject)
    {
        $ret = null;
        if (is_array($destination)) {
            if ($sourceObject) {
                foreach ($sourceObject as $row) {
                    $ret[] = $this->_serializer->cast($destination[0], $row);
                }
            }
        } elseif (is_array($sourceObject)) {
            $ret = $this->_serializer->cast($destination, $sourceObject[0]);
        } else { 
            $ret = $this->_serializer->cast($destination, $sourceObject);
        }

        return $ret;
    }

    public function castCall($destination, $url, $method = HttpMethodEnum::GET,
                    $data = null, $query_args = array(), $file = null)
    {
//        $ret = null;
        $result = $this->call($destination,$url, $method, $data, $query_args,$file);
//        $ret = $this->cast($destination, $result);
        return $result;
    }

    public function serialize($data)
    {
        return $this->_serializer->serialize($data);
    }

    public function deserialize($data,$type)
    {
        return $this->_serializer->deserialize($data,$type);
    }

    public static function getFormatFromString($format)
    {
        if ($format == ".json") {
            $format = FormatEnum::JSON;
        } elseif (strcasecmp($format, '.xml') == 0) {
            $format = FormatEnum::XML;
        } elseif (strcasecmp($format, '.php') == 0) {
            $format = FormatEnum::PHP;
        } elseif (strcasecmp($format, '.html') == 0) {
            $format = FormatEnum::HTML;
        } elseif (strcasecmp($format, '.proto') == 0) {
            $format = FormatEnum::PROTOBUFS;//change when implmented.
        } else {
            $format = FormatEnum::JSON;
        }
        return $format;
    }

    public function getContentType()
    {
        $ret = null;
        if($this->_serializer) {
            $ret = $this->_serializer->getContentType();
        }
        return $ret;
    }
}
