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
            $ret = json_encode($data);
            //if the data is an associative array just json_encode it.
        } elseif (is_array($data)) {
            if ($this->isAssocArr($data)) {
                $ret = json_encode($data);
            } else {
                $ret = new Models\ProtoList();
                foreach ($data as $obj) {
                    if (!is_null($obj)) {
                        $ret->appendItem(json_encode($obj));
                    }
                }
                $ret = json_encode($ret);
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
            $ret = json_decode($data, true);
            $result = array();
            $blah = print_r($type, true);
            error_log("Logging \"type\" info in deserialize()");
            error_log($blah);
            error_log(" {****** JSON DECODED *** ");
            $temp = print_r($ret,true);
            error_log($temp);
            error_log(" ****** JSON DECODED *** }");
            foreach ($ret["item"] as $item) {
                $current = new $type[0];
                
                $arr = json_decode($item, true);
                $current = ModelFactory::buildModel(self::stripNamespace($type[0]), $arr);
                $result[] = $current;
            }
        } else {
            $current = new $type;
            
            $arr = json_decode($data, true);
            error_log("Logging 'data' in deserialize()...");
            error_log($data);
            $errorVar = print_r($arr, true);
            $bareType = self::stripNamespace($type);
            if ($bareType == 'Task' || $bareType == 'WorkflowGraph') {
                $current = ModelFactory::buildModel($bareType, $arr);
            } else if ($bareType == 'Project') {
                if (isset($arr['tag'])) {
                    $projTags = $arr['tag'];
                    unset($arr['tag']);
                    $arr = self::array_flatten($arr);
                    $arr['tag'] = $projTags; 
                    $current = ModelFactory::buildModel($bareType, $arr);
                } else {
                    $arr = self::array_flatten($arr);
                    $current = ModelFactory::buildModel($bareType, $arr);
                }
            } else {
                $arr1 = self::array_flatten($arr, array());
                error_log("LOGGING array_flatten result...");
                $errorVar2 = print_r($arr1, true);
                error_log($errorVar2);
                $current = ModelFactory::buildModel($bareType, $arr1);
            }
            error_log("Logging array in deserialize()...");
            error_log($errorVar);
            
            $result = $current;
        }
        return $result;
    }
    
    public function getContentType()
    {
        return 'application/json; charset=utf-8';
    }
    
    private static function array_flatten($array) 
    {
        $return = array();
        foreach ($array as $key => $val) {
            if (!is_array($val)) {
                $return[$key] = $val;
            } else {
                $return = array_merge($return, self::array_flatten($val));
            }
        }
        return $return;
    }
    
    /*
     * Checks if an array is associative
     * Credit to http://stackoverflow.com/a/4254008/1799985
     */
    private function isAssocArr($array)
    {
        return (bool) count(array_filter(array_keys($array), 'is_string'));
    }
    
    private static function stripNamespace($classString)
    {
        $pos = strrpos($classString, "\\");
        
        return substr($classString, $pos + 1);
    }
}
