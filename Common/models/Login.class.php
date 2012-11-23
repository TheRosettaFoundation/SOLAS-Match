<?php

/**
 * Description of Login
 *
 * @author sean
 */

class Login {
    
    public  $email;
    public  $pass;
    
    public function __construct($e = "", $p = "") {         
         $this->email=$e;
         $this->pass=$p;
    }
  
    
}

?>
