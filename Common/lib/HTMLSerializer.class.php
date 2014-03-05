<?php

namespace SolasMatch\Common\Lib;

require_once __DIR__."/XMLSerializer.class.php";

class HTMLSerializer extends Serializer
{
    private $xmlSerial;
    
    public function __construct()
    {
        $this->format = ".html";
        $this->xmlSerial = new XMLSerializer();
    }

    public function serialize($data)
    {
        $ret = htmlspecialchars($this->xmlSerial->serialize($data), ENT_NOQUOTES);
        return $ret;
    }

    public function deserialize($data, $type)
    {
        $ret = null;
        try {
            //WTF
            $ret = $this->xmlSerial->deserialize(htmlspecialchars_decode($data, ENT_NOQUOTES), $type);
        } catch (Exception $e) {
            echo "Failed to unserialize data: $data";
        }

        if (!is_null($data) && is_null($ret)) {
            if (strcasecmp($data, "null") == 0 || $data == "null") {
                $ret=null;
            }
        }

        return $ret;
    }

    public function getContentType()
    {
        return 'text/html; charset=utf-8';
    }
}
