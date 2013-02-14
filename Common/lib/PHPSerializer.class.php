<?php

require_once 'Serializer.class.php';

class PHPSerializer extends Serializer
{
    public function serialize($data)
    {
        $ret = serialize($data);
        return $ret;
    }

    public function deserialize($data)
    {
        $ret = unserialize($data);

        if (!is_null($data) && is_null($ret)) {
            if (strcasecmp($data, "null") == 0 || $data == "null" || $data=="N;") {
                $ret=null;
            } elseif($data=="<data></data>") {
                $ret=$data;
            }
        }

        return $ret;
    }
}
