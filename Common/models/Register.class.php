<?php

class Register {
    public $email;
    public $pass;
    
    public function __construct($e="",$p="") {
         $this->pass=$p;
         $this->email=$e;
    }
    
}

?>
