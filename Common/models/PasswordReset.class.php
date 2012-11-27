<?php

/**
 * Description of PasswordReset
 *
 * @author sean
 */
class PasswordReset {
    public  $pass;
    public  $key;
    
    public function __construct($p = "", $k = "") {
         $this->pass=$p;
         $this->key=$k;
    }
  
   
}

?>
