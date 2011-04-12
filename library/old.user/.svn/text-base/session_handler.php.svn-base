<?php 
/* This class code is based on the tutorial of Matt Wade 
from http://www.zend.com/zend/spotlight/code-gallery-wade8.php */

/* Create new object of class */ 
$ses_class = new session(); 

/* Change the save_handler to use the class functions */ 
session_set_save_handler (array(&$ses_class, '_open'), 
                          array(&$ses_class, '_close'), 
                          array(&$ses_class, '_read'), 
                          array(&$ses_class, '_write'), 
                          array(&$ses_class, '_destroy'), 
                          array(&$ses_class, '_gc')); 

/* Start the session */ 
if (!isset($_SESSION))
{
	session_start();
}

class session {  
    /* Define the mysql table you wish to use with 
       this class, this table MUST exist. */ 
    var $ses_table = SESSION_TABLE; 

    /* Change to 'Y' if you want to connect to a db in 
       the _open function */ 
    var $db_con = "Y"; 

    /* Configure the info to connect to MySQL, only required 
       if $db_con is set to 'Y' */ 
    var $db_host = BITEY_DB_SERVER; 
    var $db_user = BITEY_DB_USER; 
    var $db_pass = BITEY_DB_PASSWORD; 
    var $db_dbase = BITEY_DB_NAME; 

    /* Create a connection to a database */ 
    function db_connect() { 
        $mysql_connect = @mysql_pconnect ($this->db_host, 
                                          $this->db_user, 
                                          $this->db_pass); 
        $mysql_db = @mysql_select_db ($this->db_dbase); 

        if (!$mysql_connect || !$mysql_db) { 
            return FALSE; 
        } else { 
            return TRUE; 
        } 
    } 

    /* Open session, if you have your own db connection 
       code, put it in here! */ 
    function _open($path, $name) { 
        if ($this->db_con == "Y") { 
            $this->db_connect(); 
        } 

        return TRUE; 
    } 

    /* Close session */ 
    function _close() { 
        /* This is used for a manual call of the 
           session gc function */ 
        $this->_gc(0); 
        return TRUE; 
    } 

    /* Read session data from database */ 
    function _read($ses_id) { 
        $session_sql = "SELECT * FROM " . $this->ses_table 
                     . " WHERE ses_id = '$ses_id'"; 
        $session_res = @mysql_query($session_sql); 

        if (!$session_res) { 
            return ''; 
        } 
        $session_num = @mysql_num_rows ($session_res); 
        if ($session_num > 0) { 
            $session_row = mysql_fetch_assoc ($session_res); 
            $ses_data = $session_row["ses_value"]; 
            return $ses_data; 
        } else { 
            return ''; 
        } 
    } 

    /* Write new data to database */ 
    function _write($ses_id, $data) { 
        $session_sql = "UPDATE " . $this->ses_table 
                     . " SET ses_time='" . time() 
                     . "', ses_value='$data' WHERE ses_id='$ses_id'"; 
        $session_res = @mysql_query ($session_sql); 
        if (!$session_res) { 
            return FALSE; 
        } 
        if (mysql_affected_rows ()) { 
            return TRUE; 
        } 

        $session_sql = "INSERT INTO " . $this->ses_table 
                     . " (ses_id, ses_time, ses_start, ses_value)" 
                     . " VALUES ('$ses_id', '" . time() 
                     . "', '" . time() . "', '$data')"; 
        $session_res = @mysql_query ($session_sql); 
        if (!$session_res) {     
            return FALSE; 
        }         else { 
            return TRUE; 
        } 
    } 

    /* Destroy session record in database */ 
    function _destroy($ses_id) { 
        $session_sql = "DELETE FROM " . $this->ses_table 
                     . " WHERE ses_id = '$ses_id'";
        $session_res = @mysql_query ($session_sql);       
        if (!$session_res) { 
        	return FALSE; 
        }         else { 
        	return TRUE; 
        } 
    } 

    /* Garbage collection, deletes old sessions */ 
    function _gc($life) { 
        $ses_life = strtotime("-5 minutes"); 

        $session_sql = "DELETE FROM " . $this->ses_table 
                     . " WHERE ses_time < $ses_life"; 
        $session_res = @mysql_query ($session_sql); 


        if (!$session_res) { 
            return FALSE; 
        }         else { 
            return TRUE; 
        } 
    } 
} 
