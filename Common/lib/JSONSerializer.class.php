<?php

namespace SolasMatch\Common\Lib;

use \SolasMatch\Common\Protobufs\Models as Models;

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
            //if the data is an associative array just json_encode it.
        } elseif (is_array($data)) {
            if ($this->isAssocArr($data)) {
                $ret = json_encode($data);
            } else {
                $ret = new Models\ProtoList();
                foreach ($data as $obj) {
                    if (!is_null($obj)) {
                        $ret->addItem($obj->serialize(new \DrSlump\Protobuf\Codec\Json()));
                    }
                }
                $ret = $ret->serialize(new \DrSlump\Protobuf\Codec\Json());
             }
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
            $ret = new Models\ProtoList();
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
    
    /*
     * Checks if an array is associative
     * Credit to http://stackoverflow.com/a/4254008/1799985
     */
    private function isAssocArr($array)
    {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }
}
