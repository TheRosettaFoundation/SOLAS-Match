<?php

namespace SolasMatch\Common\Exceptions;

class SolasMatchException extends \Exception
{
    public function __construct($message, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
