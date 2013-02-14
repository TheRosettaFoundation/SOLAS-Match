<?php

require_once 'Serializer.class.php';

class HTMLSerializer extends Serializer
{

    public function __construct()
    {
        $this->format = ".html";
    }

    public function serialize($data)
    {
        $ret = htmlspecialchars(wddx_serialize_value($data));
        return $ret;
    }

    public function deserialize($data)
    {
        $ret = null;
        try {
            //WTF
            $ret = json_decode(json_encode(simplexml_load_string(htmlspecialchars_decode($data))->xpath("//data")));
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
