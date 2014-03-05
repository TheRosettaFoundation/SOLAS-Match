<?php

namespace SolasMatch\Common\Lib;

require_once __DIR__."/Serializer.class.php";
require_once __DIR__."/../protobufs/models/ProtoList.php";

class ProtobufSerializer extends Serializer
{
    public function __construct()
    {
        $this->format = ".proto";
    }

    public function serialize($data)
    {
        $ret = null;
        if (is_object($data)) {
            $ret = $data->serialize();
        } elseif (is_array($data)) {
            $ret = new \ProtoList();
            foreach ($data as $obj) {
                if (!is_null($obj)) {
                    $ret->addItem($obj->serialize());
                }
            }
            $ret = $ret->serialize();
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
            $ret->parse($data);
            $result = array();
            
            foreach ($ret->getItemList() as $item) {
                $current = new $type[0];
                $current->parse($item);
                $result[] = $current;
            }
        } else {
            $current = new $type;
            
            $current->parse($data);
            $result = $current;
        }
        return $result;
    }

    public function getContentType()
    {
        return 'application/x-protobuf; charset=utf-8';
    }
}
