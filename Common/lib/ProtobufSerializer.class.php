<?php

require_once __DIR__."/Serializer.class.php";
require_once __DIR__."/../ProtoList.php";

//require __dir__."/../../ui/vendor/autoload.php";
//\DrSlump\Protobuf::autoload();
//require_once '../models/Badge.php';
//require_once '../models/Tag.php';

class ProtobufSerializer extends Serializer
{
    public function __construct()
    {
        $this->format = ".proto";
    }

    public function serialize($data)
    {
        $ret = null;
        if(is_object($data)) {
            $ret = $data->serialize();
        } elseif (is_array($data)) {
            $ret = new ProtoList();
            foreach ($data as $obj) {
                $ret->addItem($obj->serialize());
            }
            $ret=$ret->serialize();
        } else {
            $ret = $data->serialize();
        }
        return $ret;
    }

    public function deserialize($data,$type=array("Tag"))
    {
        $ret = is_array($type)?array():null;
        try {
            
            $temp = new ProtoList();
            $temp->parse($data);
            foreach ($temp->getItemList() as $value) {
               $obj = is_array($type)? new $type[0]: new $type;
               $obj->parse($value);
               $ret[]=$obj;
            }
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

//$temp = new ProtobufSerializer();
//$ary = array();
//$stat = new Badge();
//$stat->setTitle("test");
//$stat->setId(25);
//$ary[]=$stat;
//$ary[]=$stat;
//$ary[]=$stat;
//$serial = $temp->serialize($ary);
//echo $serial;
//$deSerial= $temp->deserialize($serial,array("Tag"));
//echo " equal? :".($ary ==$deSerial);