<?php

require_once "Serializer.class.php";

class XMLSerializer extends Serializer
{
    private $dom;

    public function getContentType()
    {
        return 'application/xml; charset=utf-8';
    }

    public function deserialize($data)
    {
        $ret = null;
        try {
            if($data!="<data></data>"){
                
                $array=json_decode(json_encode(simplexml_load_string($data)->xpath("//item")));
                if(!empty ($array)) {
                    $ret=$array;
                } else {
                    $ret = json_decode(json_encode(simplexml_load_string($data)->xpath("//data")));
                    if(is_array($ret)) {
                        $ret=$ret[0];
                    }
                }
            } else {
                $ret=null;
            }
        } catch (Exception $e) {
            echo "Failed to unserialize data: $data";
        }

        if (!is_null($data) && is_null($ret)) {
            if (strcasecmp($data, "null") == 0 || $data == "null") {
                $ret=null;
            }
        }

        if (is_object($ret) && is_a($ret, "stdClass")) {
            $sourceReflection = new ReflectionObject($ret);
            $sourceProperties = $sourceReflection->getProperties();
            if (sizeof($sourceProperties) == 1) {
                foreach ($sourceProperties as $sourceProperty) {
                    $sourceProperty->setAccessible(true);
                    if ($sourceProperty->getName() == "0") {
                        $ret = $sourceProperty->getValue($ret);
                    }
                }
            
            }
        }

        return $ret;
    }

    public function serialize($data)
    {
        $data = json_encode($data);
        $data = $this->convert($data, 'fragment');
        return $data;
    }

    private function convert($json, $return = 'document')
    {
        $this->dom = new DOMDocument('1.0', 'utf-8');
        $this->dom->formatOutput = true;
        $this->dom->preserveWhiteSpace = false; // Manuel - This is needed for formatOutput
        
        // remove callback functions from JSONP
        //        if (preg_match('/(\{|\[).*(\}|\])/s', $json, $matches)) {
        //            $json = $matches[0];
        //        } elseif($json!="null" && !is_numeric (str_replace ('"', '', $json))&&"false"!=$json&&"true"!=$json) {
        //            throw new Exception('JSON not formatted correctly');
        //        }
        
        if (is_string($json)) {
            $data = json_decode($json);
        }
        
        $data_element = $this->process($data, $this->dom->createElement('data'));
        $this->dom->appendChild($data_element);
        
        switch ($return)
        {
            case 'fragment': return $this->dom->saveXML($data_element);
            case 'object': return $this->dom;
            default: return $this->dom->saveXML();
        }
    }

    private function process($data, $element)
    {
        if (is_array($data)) {
            foreach ($data as $item) {
                $item_element = $this->process($item, $this->dom->createElement('item'));
                $element->appendChild($item_element);
            }
        } elseif (is_object($data)) {
            $vars = get_object_vars($data);
            foreach ($vars as $key => $value) {
                $key = $this->validateElementName($key);
                $var_element = $this->process($value, $this->dom->createElement($key));
                $element->appendChild($var_element);
            }
        } else {
//            if($data==null) $data="null";
            $element->appendChild($this->dom->createTextNode($data));
        }
        return $element;
    }

    private function validateElementName($name)
    {
        $name = preg_replace('/^(.*?)(xml)([a-z])/i', '$3', $name);
        $name = preg_replace('/[^a-z0-9_\-]/i', '', $name);
        return $name;
    }
}
