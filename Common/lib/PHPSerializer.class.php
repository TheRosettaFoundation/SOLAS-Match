<?php

require_once __DIR__."/Serializer.class.php";

class PHPSerializer extends Serializer
{
    public function __construct()
    {
        $this->format = ".php";
    }

      public function serialize($data)
    {
        $ret = null;
        if(is_object($data)) {
            $ret = $data->serialize(new \DrSlump\Protobuf\Codec\PhpArray());
        } elseif (is_array($data)) {
            $ret = new ProtoList();
            foreach ($data as $obj) {
               if(!is_null($obj)) $ret->addItem(serialize($obj->serialize(new \DrSlump\Protobuf\Codec\PhpArray())));
            }
            $ret=$ret->serialize(new \DrSlump\Protobuf\Codec\PhpArray());
        } else {
            $ret =(is_null($data)||$data=="null")?null:$data;
        }
        return serialize($ret);
    }

    public function deserialize($data,$type)
    {
        $data=unserialize($data);
        if($data==null ||$data=="null" || $data == '') {
            return null;
        }
        if(is_null($type)) return $data;
        $result = null;
        if(is_array($type)){
            $ret = new ProtoList();
            $ret->parse($data,new \DrSlump\Protobuf\Codec\PhpArray());
            $result = array();
            
            foreach ($ret->getItemList() as $item){
                $current = new $type[0];
                $current->parse(unserialize($item),new \DrSlump\Protobuf\Codec\PhpArray());
                $result[]=$current;
            }
        }
        else {
            
            $current = new $type;
            
            $current->parse($data,new \DrSlump\Protobuf\Codec\PhpArray());
            $result=$current;
         }
        return $result;
    }

    public function getContentType()
    {
        return 'text/html; charset=utf-8';
    }
}
