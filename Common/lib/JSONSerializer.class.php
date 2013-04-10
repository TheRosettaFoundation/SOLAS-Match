<?php

require_once __DIR__."/Serializer.class.php";

class JSONSerializer extends Serializer
{
    public function __construct()
    {
        $this->format = ".json";
    }

    public function serialize($data)
    {
        return json_encode($data,JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT);
    }

    public function deserialize($data,$type)
    {
        $ret = null;
        try {
            $ret = json_decode($data);
        } catch (Exception $e) {
            $ret = "Failed to unserialize data: $data";
        }

        if (!is_null($data) && is_null($ret)) {
            if (strcasecmp($data, "null") == 0 || $data == "null") {
                $ret=null;
            } elseif ($data=="<data></data>") {// this is for the xml serialisation
                $ret=$data;
            }
        }
        $result=null;
        if (is_array($type)) {
            if ($ret) {
                foreach ($ret as $row) {
                    $result[] = $this->cast($type[0], $row);
                }
            }
        } elseif (is_array($ret)) {
            $result = $this->cast($type, $ret[0]);
        } else { 
            $result = $this->cast($type, $ret);
        }
        return $result;
    }

    public function getContentType()
    {
        return 'application/json; charset=utf-8';
    }
}
