<?php

namespace SolasMatch\Common\Lib;

require_once __DIR__."/Serializer.class.php";

class XMLSerializer extends Serializer
{
    private $dom;

    public function __construct()
    {
        $this->format = ".xml";
    }

    public function getContentType()
    {
        return 'application/xml; charset=utf-8';
    }

    public function serialize($data)
    {
        $ret = null;
        if (is_object($data)) {
            $ret = $data->serialize(new \DrSlump\Protobuf\Codec\Xml());
        } elseif (is_array($data)) {
            $ret = new \SolasMatch\Common\Protobufs\Models\ProtoList();
            foreach ($data as $obj) {
                if (!is_null($obj)) {
                    $ret->addItem($obj->serialize(new \DrSlump\Protobuf\Codec\Xml()));
                }
            }
            $ret = $ret->serialize(new \DrSlump\Protobuf\Codec\Xml());
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
            $ret = new \SolasMatch\Common\Protobufs\Models\ProtoList();
            $ret->parse($data, new \DrSlump\Protobuf\Codec\Xml());
            $result = array();
            
            foreach ($ret->getItemList() as $item) {
                $current = new $type[0];
                $current->parse($item, new \DrSlump\Protobuf\Codec\Xml());
                $result[] = $current;
            }
        } else {
            $current = new $type;
            
            $current->parse($data, new \DrSlump\Protobuf\Codec\Xml());
            $result = $current;
        }
        return $result;
    }
}
