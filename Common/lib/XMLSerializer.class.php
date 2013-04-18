<?php

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
        if(is_object($data)) {
            $ret = $data->serialize(new \DrSlump\Protobuf\Codec\Xml());
        } elseif (is_array($data)) {
            $ret = new ProtoList();
            foreach ($data as $obj) {
                $ret->addItem($obj->serialize(new \DrSlump\Protobuf\Codec\Xml()));
            }
            $ret=$ret->serialize(new \DrSlump\Protobuf\Codec\Xml());
        } else {
            $ret =(is_null($data)||$data=="null")?null:$data;
        }
        return $ret;
    }

    public function deserialize($data,$type)
    {
        if($data==null ||$data=="null") {
            return null;
        }
        if(is_null($type)) return $data;
        $result = null;
        if(is_array($type)){
            $ret = new ProtoList();
            $ret->parse($data,new \DrSlump\Protobuf\Codec\Xml());
            $result = array();
            
            foreach ($ret->getItemList() as $item){
                $current = new $type[0];
                $current->parse($item,new \DrSlump\Protobuf\Codec\Xml());
                $result[]=$current;
            }
        }
        else {
            
            $current = new $type;
            
            $current->parse($data,new \DrSlump\Protobuf\Codec\Xml());
            $result=$current;
         }
        return $result;
    }

//    private function convert($json, $return = 'document')
//    {
//        $this->dom = new DOMDocument('1.0', 'utf-8');
//        $this->dom->formatOutput = true;
//        $this->dom->preserveWhiteSpace = false; // Manuel - This is needed for formatOutput
//        
//        // remove callback functions from JSONP
//        //        if (preg_match('/(\{|\[).*(\}|\])/s', $json, $matches)) {
//        //            $json = $matches[0];
//        //        } elseif($json!="null" && !is_numeric (str_replace ('"', '', $json))&&"false"!=$json&&"true"!=$json) {
//        //            throw new Exception('JSON not formatted correctly');
//        //        }
//        
//        if (is_string($json)) {
//            $data = json_decode($json);
//        }
//        
//        $data_element = $this->process($data, $this->dom->createElement('data'));
//        $this->dom->appendChild($data_element);
//        
//        switch ($return)
//        {
//            case 'fragment': return $this->dom->saveXML($data_element);
//            case 'object': return $this->dom;
//            default: return $this->dom->saveXML();
//        }
//    }

//    private function process($data, $element)
//    {
//        if (is_array($data)) {
//            foreach ($data as $item) {
//                $item_element = $this->process($item, $this->dom->createElement('item'));
//                $element->appendChild($item_element);
//            }
//        } elseif (is_object($data)) {
//            $vars = get_object_vars($data);
//            foreach ($vars as $key => $value) {
//                $key = $this->validateElementName($key);
//                $var_element = $this->process($value, $this->dom->createElement($key));
//                $element->appendChild($var_element);
//            }
//        } else {
////            if($data==null) $data="null";
//            $element->appendChild($this->dom->createTextNode($data));
//        }
//        return $element;
//    }

//    private function validateElementName($name)
//    {
//        $name = preg_replace('/^(.*?)(xml)([a-z])/i', '$3', $name);
//        $name = preg_replace('/[^a-z0-9_\-]/i', '', $name);
//        return $name;
//    }
}
