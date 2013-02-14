<?php

abstract class Serializer
{
    public abstract function serialize($data);
    public abstract function deserialize($data);

    public function cast($destination, $sourceObject)
    {
        if (is_null($destination) || is_null($sourceObject)) {
            return null;
        }
        
        $primitives = array("int", "integer", "string", "boolean");
        
        if (is_string($destination)) {
            if (in_array($destination, $primitives)) {
                return null;
            } else {
                $destination = new $destination();
            }
        }
        if (is_object($sourceObject)) {
            
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

                    if (is_array($value)) {
                        foreach ($value as $element) {
                            $index = array_search($element, $value);
                            if(is_object($element)) {
                                if (get_class($element) == "stdClass") {
                                    if (preg_match("/@var\s+[\\\\a-zA-Z]*[\\\\]([^\s]+)[[]]/",
                                                $propDest->getDocComment(), $matches)) {
                                        list( , $className) = $matches;
                                        $element = self::cast($className, $element);
                                    }
                                }
                            }
                                
                            $value[$index] = $element;
                        }
                    }
                        
                    if(is_object($value)&&get_class($value)=="stdClass"){
                        if (preg_match("/@var\s+[\\\\a-zA-Z]*[\\\\]([^\s]+)[[]]/",
                                        $propDest->getDocComment(), $matches)) {        
                            list( , $className) = $matches;             
                            $value = self::cast($className, $value);
                        }
                        $propDest->setValue($destination, null);
                    } else {
                        $propDest->setValue($destination, $value);
                    }
                } else {
                    $destination->$name = $value;
                }
            }
        } else {
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
