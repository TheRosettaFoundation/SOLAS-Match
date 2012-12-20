<?php

/*
 * A serializer for encoding and decoding data for the API
 *
 * @author: Dave O Carroll
 */

require_once 'FormatEnum.class.php';

class Serializer
{
    public static function serialize($data, $format = FormatEnum::JSON)
    {
        $ret = null;
        switch ($format) {
            case FormatEnum::JSON: {
                $ret = json_encode($data);
                break;
            }
            case FormatEnum::XML: {
                $ret = wddx_serialize_value($data);
                break;
            }
            case FormatEnum::HTML: {
                $ret = htmlspecialchars(wddx_serialize_value($data));
                break;
            }
            case FormatEnum::PHP: {
                $ret = serialize($data);
                break;
            }
        }
        return $ret;
    }

    public static function deserialize($data, $format = FormatEnum::JSON)
    {
        //deserialize data here
        $ret = null;
        switch ($format) {
            case FormatEnum::JSON: {
                try {
                    $ret = json_decode($data);
                } catch (Exception $e) {
                    echo "Failed to unserialize data: $data";
                }
                break;
            }
            case FormatEnum::XML: {
                try {
                    if($data!="<data></data>"){
                       
                        $array=json_decode(json_encode(simplexml_load_string($data)->xpath("//item")));
                        if(!empty ($array)) {
                            $ret=$array;
                        }
                        else{
                            $ret = json_decode(json_encode(simplexml_load_string($data)->xpath("//data")));
                            if(is_array($ret)) {
                                $ret=$ret[0];
                            }
                        }
                    }else{
                        $ret=null;
                    }
                } catch (Exception $e) {
                    echo "Failed to unserialize data: $data";
                }
                break;
            }
            case FormatEnum::HTML: {
                try {
                    $ret = json_decode(json_encode(simplexml_load_string(htmlspecialchars_decode($data))->xpath("//data")));
                } catch (Exception $e) {
                    echo "Failed to unserialize data: $data";
                }
                break;
            }
            case FormatEnum::PHP: {
                try {
                    $ret = unserialize($data);
                } catch (Exception $e) {
                    echo "Failed to unserialize data: $data";
                }
                break;
            }
            case FormatEnum::PROTOBUFS: {
                try {
                    $ret = $data;
                } catch (Exception $e) {
                    echo "Failed to unserialize data: $data";
                }
                break;
            }
        }
        if (!is_null($data) && is_null($ret)) {
            if (strcasecmp($data, "null") == 0 || $data == "null"||(FormatEnum::PHP==$format&&$data=="N;")) {
                $ret=null;
            } elseif(!(FormatEnum::XML==$format)&&$data=="<data></data>") {
                $ret=$data;
            }            
        }
        if(FormatEnum::XML==$format&&is_object($ret)&& is_a($ret, "stdClass")){
            $sourceReflection = new ReflectionObject($ret);
            $sourceProperties = $sourceReflection->getProperties();
            if(sizeof($sourceProperties)==1){
                foreach ($sourceProperties as $sourceProperty) {
                    $sourceProperty->setAccessible(true);
                    if($sourceProperty->getName()=="0"){
                        $ret=$sourceProperty->getValue($ret);
                    }
                }
                
            }
        }
        return $ret;
    }

    public static function cast($destination, $sourceObject)
    {
        if (is_null($destination) || is_null($sourceObject)) {
            return null;
        }
       
        
        if (is_string($destination)) {
            $destination = new $destination();
        }
        if(is_object($sourceObject)){
            if(get_class($destination)==get_class($sourceObject)) return $sourceObject;
        
        $sourceReflection = new ReflectionObject($sourceObject);
        $destinationReflection = new ReflectionObject($destination);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);
            $name = $sourceProperty->getName();
            $value = $sourceProperty->getValue($sourceObject);
            if ($destinationReflection->hasProperty($name)) {
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);
                
                if(is_object($value)&&get_class($value)=="stdClass"){
                    $propDest->setValue($destination, null);
                }else{
                    $propDest->setValue($destination, $value);
                }
            } else {
                $destination->$name = $value;
            }
        }
        }else{
          $destination->parse($sourceObject);
        }
        return $destination;
        
    }
    
    public static function getFormat($format)
    {
        if ($format == ".json") {
            $format = FormatEnum::JSON;
        } elseif (strcasecmp($format, '.xml') == 0) {
            $format = FormatEnum::XML;
        } elseif (strcasecmp($format, '.php') == 0) {
            $format = FormatEnum::PHP;
        } elseif (strcasecmp($format, '.html') == 0) {
            $format = FormatEnum::HTML;
        } elseif (strcasecmp($format, '.proto') == 0) {
            $format = FormatEnum::PROTOBUFS;//change when implmented.
        } else {
            $format = FormatEnum::JSON;
        }
        return $format;
    }
}
