<?php

namespace SolasMatch\Common\Lib;

require_once __DIR__."/Serializer.class.php";

class JSONSerializer extends Serializer
{
    public function __construct()
    {
        $this->format = ".json";
    }

    public function serialize($data)
    {
        $ret = null;
        if (is_object($data)) {
            $ret = $data->serialize(new \DrSlump\Protobuf\Codec\Json());
        } elseif (is_array($data)) {
            $ret = new \ProtoList();
            foreach ($data as $obj) {
                if (!is_null($obj)) {
                    $ret->addItem($obj->serialize(new \DrSlump\Protobuf\Codec\Json()));
                }
            }
            $ret = $ret->serialize(new \DrSlump\Protobuf\Codec\Json());
        } else {
            $ret = (is_null($data) || $data == "null") ? null : $data;
        }
        return $ret;
    }

    public function deserialize($data, $type)
    {
        if ($data == null || $data == "null" || $data == '') {
            return null;
        }
        if (is_null($type)) {
            return $data;
        }
        $result = null;
        if (is_array($type)) {
            $ret = new \ProtoList();
            $ret->parse($data, new \DrSlump\Protobuf\Codec\Json());
            $result = array();
            
            foreach ($ret->getItemList() as $item) {
                $current = new $type[0];
                $current->parse($item, new \DrSlump\Protobuf\Codec\Json());
                $result[] = $current;
            }
        } else {
            
            $current = new $type;
            
            $current->parse($data, new \DrSlump\Protobuf\Codec\Json());
            $result = $current;
        }
        return $result;
    }
    
    public function getContentType()
    {
        return 'application/json; charset=utf-8';
    }
}
