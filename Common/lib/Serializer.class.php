<?php

abstract class Serializer
{
    protected $format;

    public abstract function serialize($data);
    public abstract function deserialize($data);
    public abstract function getContentType();

    public function getFormat()
    {
        return $this->format;
    }

    public function cast($destination, $sourceObject)
    {
        if ( is_null($sourceObject)) {
            return null;
        }elseif(is_null($destination)){
            return $sourceObject;
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
}
