<?php

require_once 'FormatEnum.php';
require_once 'JSONSerializer.class.php';
require_once 'XMLSerializer.class.php';
require_once 'HTMLSerializer.class.php';
require_once 'PHPSerializer.class.php';
require_once 'ProtobufSerializer.class.php';

class APIHelper
{
    private $_serializer;

    public function __construct($format)
    {
        $format = self::getFormatFromString($format);

        switch ($format)
        {
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

    public function call($url, $method = HTTP_Request2::METHOD_GET,
             $data = null, $query_args = array(), $file = null)
    {
        $url = $url.$this->_serializer->getFormat()."/?";
        $request = new HTTP_Request2($url, $method);

        if (!is_null($data) && "null" != $data) {
            $data=$this->_serializer->serialize($data);
            $request->setBody($data);
        }

        if (!is_null($file)) {
            $request->setBody($file);
        }
        
        if (count($query_args) > 0) {
            $requestUrl = $request->getUrl();
            $requestUrl->setQueryVariables($query_args);
        }
        
        $response = $request->send();
        $response_data = $this->_serializer->deserialize(trim($response->getBody()));
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

    public function castCall($destination, $url, $method = HTTP_Request2::METHOD_GET,
                    $data = null, $query_args = array())
    {
        $ret = null;
        $result = $this->call($url, $method, $data, $query_args);
        $ret = $this->cast($destination, $result);
        return $ret;
    }

    public function serialize($data)
    {
        return $this->_serializer->serialize($data);
    }

    public function deserialize($data)
    {
        return $this->_serializer->deserialize($data);
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
