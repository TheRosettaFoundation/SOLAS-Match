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
            }
            case FormatEnum::XML: {
                $ret = wddx_serialize_value($data);
            }
            case FormatEnum::HTML: {
                $ret = htmlspecialchars(wddx_serialize_value($data));
            }
            case FormatEnum::PHP: {
                $ret = serialize($data);
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
                    $ret = wddx_deserialize($data);
                } catch (Exception $e) {
                    echo "Failed to unserialize data: $data";
                }
                break;
            }
            case FormatEnum::HTML: {
                try {
                    $ret = wddx_deserialize(htmlspecialchars_decode($data));
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
        }
        return $ret;
    }

    public static function cast($destination, $sourceObject)
    {
        if (is_string($destination)) {
            $destination = new $destination();
        }
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
                $propDest->setValue($destination,$value);
            } else {
                $destination->$name = $value;
            }
        }

        return $destination;
    }
}
