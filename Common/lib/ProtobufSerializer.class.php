<?php

require_once 'Serializer.class.php';

class ProtobufSerializer extends Serializer
{
    public function serialize($data)
    {
        $ret = null;
        if(is_object($data)) {
            $ret = $body->serialize();
        } elseif (is_array($data)) {
            $ret = array();
            foreach ($data as $obj) {
                $ret[]=$obj->serialize();
            }
        } else {
            $ret = $data;
        }
        return $ret;
    }

    public function deserialize($data)
    {
        $ret = null;
        try {
            $ret = $data;
        } catch (Exception $e) {
            echo "Failed to unserialize data: $data";
        }

        if (!is_null($data) && is_null($ret)) {
            if (strcasecmp($data, "null") == 0 || $data == "null") {
                $ret=null;
            } elseif($data == "<data></data>") {
                $ret = $data;
            }
        }

        return $ret;
    }

    public function getContentType()
    {
        return 'application/x-protobuf; charset=utf-8';
    }
}
