<?php

namespace SolasMatch\Common\Lib;

require_once __DIR__."/Serializer.class.php";

class JSONSerializer extends Serializer
{
    public function __construct()
    {
        $this->format = ".json";
    }

    // Use PHP serialize() to replace Protobufs
    public function serialize($data)
    {
        if (is_object($data)) {
            $ret = serialize($data);
        } elseif (is_array($data)) {
            $ret = serialize($data);
        } else {
            $ret = (is_null($data) || $data == "null") ? null : $data;
        }
        return $ret;
    }

    // Used for AMQPMessage to backend
    public function serializeToString($data)
    {
        return json_encode($data);
    }

    // Use PHP unserialize() to replace Protobufs
    public function deserialize($data, $type)
    {
        if ($data == null || $data == "null" || $data == '') {
            return null;
        }
        if (is_null($type)) {
            return $data;
        }
        $result = unserialize($data);
        return $result;
    }
    
    public function getContentType()
    {
        return 'application/json; charset=utf-8';
    }
}
