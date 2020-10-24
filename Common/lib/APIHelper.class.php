<?php

namespace SolasMatch\Common\Lib;

use \SolasMatch\Common\Enums as Enums;
use \SolasMatch\Common\Exceptions as Exceptions;

require_once __DIR__."/../Enums/FormatEnum.class.php";
require_once __DIR__."/JSONSerializer.class.php";
//require_once __DIR__."/XMLSerializer.class.php";
//require_once __DIR__."/HTMLSerializer.class.php";
//require_once __DIR__."/PHPSerializer.class.php";
//require_once __DIR__."/ProtobufSerializer.class.php";

class APIHelper
{
    public static $UNIT_TESTING = false;
    private $serializer;
    private $responseCode;
    private $outputHeaders;

    public function __construct($format)
    {
        $format = self::getFormatFromString($format);

        switch ($format)
        {
            default:
            case Enums\FormatEnum::JSON:
                $this->serializer = new JSONSerializer();
                break;
//            case Enums\FormatEnum::XML:
//                $this->serializer = new XMLSerializer();
//                break;
//            case Enums\FormatEnum::HTML:
//                $this->serializer = new HTMLSerializer();
//                break;
//            case Enums\FormatEnum::PHP:
//                $this->serializer = new PHPSerializer();
//                break;
//            case Enums\FormatEnum::PROTOBUFS:
//                $this->serializer = new ProtobufSerializer();
//                break;
        }
    }
    
    public function call(
        $destination,
        $url,
        $method = Enums\HttpMethodEnum::GET,
        $data = null,
        $query_args = array(),
        $file = null,
        $headers = array()
    ){
        $url = $url.$this->serializer->getFormat()."/?";
        if (!empty($query_args) && count($query_args) > 0) {
            $first = true;
            foreach ($query_args as $key => $val) {
                if (!$first) {
                    $url .= "&";
                } else {
                    $first = false;
                }
                $url .= "$key=$val";
            }
        }
        $re = curl_init($url);
        curl_setopt($re, CURLOPT_CUSTOMREQUEST, $method);
        $length = 0;
        if (!is_null($data) && "null" != $data) {
            $data = $this->serializer->serialize($data);
            curl_setopt($re, CURLOPT_POSTFIELDS, $data);
            $length = strlen($data);
        }
        
        if (!is_null($file)) {
            $length = strlen($file);
            curl_setopt($re, CURLOPT_POSTFIELDS, $file);
        }

        curl_setopt($re, CURLOPT_COOKIESESSION, true);
        if (isset($_COOKIE['slim_session'])) {
            curl_setopt($re, CURLOPT_COOKIE, "slim_session=".urlencode($_COOKIE['slim_session']).";");
        }
        
        curl_setopt($re, CURLOPT_AUTOREFERER, true);
        $token = UserSession::getAccessToken();
        $httpHeaders = array(
            $this->serializer->getContentType(),
            'Expect:',
            'Content-Length:'.$length
        );
        if (!is_null($token = UserSession::getAccessToken())) {
            $httpHeaders[] = 'Authorization: Bearer '.$token->getToken();
        }
        if (self::$UNIT_TESTING) {
            $headers[] = 'X-UNIT-TESTING: 1';
        }
        if (!is_null($headers)) {
            $httpHeaders = array_merge($httpHeaders, $headers);
        }
        curl_setopt($re, CURLOPT_HTTPHEADER, $httpHeaders);
        curl_setopt($re, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($re, CURLOPT_HEADER, true);
        curl_setopt($re, CURLOPT_SSL_VERIFYHOST, false); // Calls will be local so no need to verify hostname (& test server may not have proper certificate)
        curl_setopt($re, CURLOPT_SSL_VERIFYPEER, false); // Calls will be local so no need to verify certificate (& test server may not have proper certificate)
        $res = curl_exec($re);
        $header_size = curl_getinfo($re, CURLINFO_HEADER_SIZE);
        $header = substr($res, 0, $header_size);
        $this->outputHeaders = http_parse_headers($header);
        $res = substr($res, $header_size);
        $success = array(200,201,202,203,204,301,303);
        $this->responseCode = curl_getinfo($re, CURLINFO_HTTP_CODE);

        curl_close($re);
        
        if (in_array($this->responseCode, $success)) {
            $response_data = $this->serializer->deserialize($res, $destination);
        } else {
            throw new Exceptions\SolasMatchException($res, $this->responseCode);
        }
        return $response_data;
    }

    public function externalCall( //this function is mainly being used in Google+ Sign In calls
        $destination,
        $url,
        $method = Enums\HttpMethodEnum::GET,
        $data = null,
        $query_args = array(),
        $file = null,
        $authorization_header = null,
        $headers = array()
    ) {
        $url = $url."/?";
        if (!empty($query_args) && count($query_args) > 0) {
            $first = true;
            foreach ($query_args as $key => $val) {
                if (!$first) {
                    $url .= "&";
                } else {
                    $first = false;
                }
                $url .= "$key=$val";
            }
        }
        
        $re = curl_init($url);
        curl_setopt($re, CURLOPT_CUSTOMREQUEST, $method);
        $length = 0;
        if (!is_null($data) && "null" != $data) {
            $data = $this->serializer->serialize($data);
            curl_setopt($re, CURLOPT_POSTFIELDS, $data);
            $length = strlen($data);
        }
        
        if (!is_null($file)) {
            $length = strlen($file);
            curl_setopt($re, CURLOPT_POSTFIELDS, $file);
        }

        curl_setopt($re, CURLOPT_COOKIESESSION, true);
        if (isset($_COOKIE['slim_session'])) {
            curl_setopt($re, CURLOPT_COOKIE, "slim_session=".urlencode($_COOKIE['slim_session']).";");
        }
        
        curl_setopt($re, CURLOPT_AUTOREFERER, true);
        $token = UserSession::getAccessToken();
        
        $httpHeaders = array(
            $this->serializer->getContentType(),
            'Expect:',
            'Content-Length:'.$length
        );
        if (!is_null($authorization_header)) {
            $httpHeaders[] = 'Authorization: Bearer '.$authorization_header;
        }
        if (self::$UNIT_TESTING) {
            $headers[] = 'X-UNIT-TESTING: 1';
        }
        if (!is_null($headers)) {
            $httpHeaders = array_merge($httpHeaders, $headers);
        }
        curl_setopt($re, CURLOPT_HTTPHEADER, $httpHeaders);
        curl_setopt($re, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($re, CURLOPT_HEADER, true);
        $res = curl_exec($re);
        $header_size = curl_getinfo($re, CURLINFO_HEADER_SIZE);
        $header = substr($res, 0, $header_size);
        $this->outputHeaders = http_parse_headers($header);
        $res = substr($res, $header_size);
        $success = array(200,201,202,203,204,301,303);
        $this->responseCode = curl_getinfo($re, CURLINFO_HTTP_CODE);

        curl_close($re);
        if (in_array($this->responseCode, $success)) {
            $response_data = $this->serializer->deserialize($res, $destination);
        } else {
            throw new Exceptions\SolasMatchException($res, $this->responseCode);
        }
        return $response_data;
    }

    public function cast($destination, $sourceObject)
    {
        $ret = null;
        if (is_array($destination)) {
            if ($sourceObject) {
                foreach ($sourceObject as $row) {
                    $ret[] = $this->serializer->cast($destination[0], $row);
                }
            }
        } elseif (is_array($sourceObject)) {
            $ret = $this->serializer->cast($destination, $sourceObject[0]);
        } else {
            $ret = $this->serializer->cast($destination, $sourceObject);
        }

        return $ret;
    }

    public function serialize($data)
    {
        return $this->serializer->serialize($data);
    }

    public function deserialize($data, $type)
    {
        return $this->serializer->deserialize($data, $type);
    }

    public static function getFormatFromString($format)
    {
        if ($format == ".json") {
            $format = Enums\FormatEnum::JSON;
//        } elseif (strcasecmp($format, '.xml') == 0) {
//            $format = Enums\FormatEnum::XML;
//        } elseif (strcasecmp($format, '.php') == 0) {
//            $format = Enums\FormatEnum::PHP;
//        } elseif (strcasecmp($format, '.html') == 0) {
//            $format = Enums\FormatEnum::HTML;
//        } elseif (strcasecmp($format, '.proto') == 0) {
//            $format = Enums\FormatEnum::PROTOBUFS;//change when implmented.
        } else {
            $format = Enums\FormatEnum::JSON;
        }
        return $format;
    }

    public static function parseFilterString($filter)
    {
        $ret = array();
        $pairs = explode(";", $filter);
        foreach ($pairs as $pair) {
            if ($pair != '') {
                $keyValue = explode(":", $pair);
                $ret[$keyValue[0]] = $keyValue[1];
            }
        }
        return $ret;
    }

    public function getContentType()
    {
        $ret = null;
        if ($this->serializer) {
            $ret = $this->serializer->getContentType();
        }
        return $ret;
    }
    
    public function getResponseCode()
    {
        return $this->responseCode;
    }
    
    public function getHeaders()
    {
        return $this->outputHeaders;
    }
    
    
    // http://stackoverflow.com/a/1147952
    private function systemExtensionMimeTypes()
    {
        //Returns the system MIME type mapping of extensions to MIME types, as defined in /etc/mime.types.
        $out = array();
        $file = fopen('/etc/mime.types', 'r');
        while (($line = fgets($file)) !== false) {
            $line = trim(preg_replace('/#.*/', '', $line));
            if (!$line) {
                continue;
            }
            $parts = preg_split('/\s+/', $line);
            if (empty($parts) || count($parts) == 1) {
                continue;
            }
            $type = array_shift($parts);
            foreach ($parts as $part) {
                $out[$part] = $type;
            }
        }
        fclose($file);
        return $out;
    }

    private function getMimeTypeFromSystem($ext)
    {
        static $types;
        if (!isset($types)) {
            $types = $this->systemExtensionMimeTypes();
        }
  
        return isset($types[$ext]) ? $types[$ext] : null;
    }
    
    public function getCanonicalMime($filename)
    {
        $mimeMap = array(
             "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            ,"xlsm" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            ,"xltx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.template"
            ,"potx" => "application/vnd.openxmlformats-officedocument.presentationml.template"
            ,"ppsx" => "application/vnd.openxmlformats-officedocument.presentationml.slideshow"
            ,"pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation"
            ,"sldx" => "application/vnd.openxmlformats-officedocument.presentationml.slide"
            ,"docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
            ,"dotx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.template"
            ,"xlam" => "application/vnd.ms-excel.addin.macroEnabled.12"
            ,"xlsb" => "application/vnd.ms-excel.sheet.binary.macroEnabled.12"
            ,"xlf"  => "application/xliff+xml"
        );
        
        $extension = explode(".", $filename);
        $extension =  strtolower($extension[count($extension)-1]);

        return array_key_exists($extension, $mimeMap)? $mimeMap[$extension] : $this->getMimeTypeFromSystem($extension);
    }
}

if (!function_exists('http_parse_headers')) {
    function http_parse_headers($header)
    {
        $retVal = array();
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
        foreach ($fields as $field) {
            if (preg_match('/([^:]+): (.+)/m', $field, $match)) {
                $match[1] = preg_replace_callback(
                    '/(?<=^|[\x09\x20\x2D])./',
                    function ($m) {
                        return strtoupper($m[0]);
                    },
                    strtolower(trim($match[1]))
                );
                if (isset($retVal[$match[1]])) {
                    $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
                } else {
                    $retVal[$match[1]] = trim($match[2]);
                }
            }
        }
        return $retVal;
    }
}
