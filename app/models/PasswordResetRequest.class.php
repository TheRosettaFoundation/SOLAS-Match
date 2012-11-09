<?php

/**
 * Description of PasswordReset
 *
 * @author sean
 */
class PasswordResetRequest {
    public  $userID;
    public  $key;
    
    public function __construct($u = "", $k = "") {
         $this->userID=$u;
         $this->key=$k;
    }
    public function getUserID() {
        return $this->userID;
    }

    public function setUserID($userID) {
        $this->userID = $userID;
    }

    public function getKey() {
        return $this->key;
    }

    public function setKey($key) {
        $this->key = $key;
    }   
}

?>
