<?php

require_once 'Serializer.class.php';

class JSONSerializer extends Serializer
{
    public function serialize($data)
    {
        return json_encode($data);
    }

    public function deserialize($data)
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
            } elseif ($data=="<data></data>") {
                $ret=$data;
            }
        }

        return $ret;
    }
}
