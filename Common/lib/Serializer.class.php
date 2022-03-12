<?php

namespace SolasMatch\Common\Lib;

abstract class Serializer
{
    protected $format;

    abstract public function serialize($data);
    abstract public function deserialize($data, $type);
    abstract public function getContentType();
}
