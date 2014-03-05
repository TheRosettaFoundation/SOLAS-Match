<?php

namespace SolasMatch\API\Lib;

use \SolasMatch\Common as Common;

/**
 * Description of PDOWrapper
 *
 * @author sean
 */

require_once __DIR__."/../../Common/lib/Settings.class.php";

class PDOWrapper
{
    public static $unitTesting = false;
    private static $instance = null;
    private $logfile = '';
    private $logging = false; // debug on or off
    private $show_errors = false; // output errors. true/false
    private $show_sql = false; // turn off for production version.
    private $use_permanent_connection = false;

    // Do not change the variables below
    private $connection;
    private $FILE_HANDLER;
    private $ERROR_MSG      = '';
    private $sql_errored    = ''; // Filled with the query that failed

    private function __construct()
    {
        // Set up the connection
        $this->logging = (strlen($this->logfile)>0) ? true : false;
        if ($this->logging) {
            $this->logfile = Common\Lib\Settings::get('database.log_file'); // full path to debug logfile. Use only in debug mode!
        }
        $this->show_errors = (Common\Lib\Settings::get('database.show_errors') == 'y') ? true : false;
        $this->show_sql = (Common\Lib\Settings::get('database.show_sql') == 'y') ? true : false;
        $this->init();
    }

    /*
     * Call init() on the new MySQLWrapper object in order to establish 
     * the DB connection.
     */
    private function init()
    {
        $this->initLogfile();
        $ret = $this->openConnection();
        return $ret;
    }
    
    public static function getInstance()
    {
        if (!is_null(PDOWrapper::$instance)) {
            return PDOWrapper::$instance;
        } else {
            PDOWrapper::$instance = new PDOWrapper();
            return PDOWrapper::$instance;
        }
    }

    public function getConnection()
    {
        return is_null($this->connection) ? $this->openConnection() : $this->connection;
    }
    
    /*
     * Connect to the database itself.
     */
    private function openConnection()
    {
        $conn = false;
        $ret = false;
        $dbName = self::$unitTesting ?
            Common\Lib\Settings::get('unit_test.database') :
            Common\Lib\Settings::get('database.database');
        $server = self::$unitTesting ?
            Common\Lib\Settings::get('unit_test.server') :
            Common\Lib\Settings::get('database.server');
        $server_port = self::$unitTesting ?
            Common\Lib\Settings::get('unit_test.port') :
            Common\Lib\Settings::get('database.server_port');
        $username = self::$unitTesting ?
            Common\Lib\Settings::get('unit_test.username') :
            Common\Lib\Settings::get('database.username');
        $password = self::$unitTesting ?
            Common\Lib\Settings::get('unit_test.password') :
            Common\Lib\Settings::get('database.password');
        
        if ($this->use_permanent_connection) {
            $conn = new \PDO(
                "mysql:host=$server;dbname=$dbName;port=$server_port",
                $username,
                $password,
                array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", \PDO::ATTR_PERSISTENT => true)
            );
        } else {
             $conn = new \PDO(
                 "mysql:host=$server;dbname=$dbName;port=$server_port",
                 $username,
                 $password,
                 array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
             );
        }
        
        unset($password);
        unset($dbName);
        unset($server);
        unset($server_port);
        unset($username);
        
        if (!$conn) {
            $this->error_msg = "\r\n" . "Unable to connect to database - " . date('H:i:s');
            $this->debug();
            $ret = false;
        } else {
            // Success!
            //mysql_set_charset('utf8', $conn);
            $this->connection = $conn;
            $ret = true;
        }
        return $ret;
    }

    /*
     * Initial the log file.
     */
    private function initLogfile()
    {
        if ($this->logging) {
            $this->file_handler = fopen($this->logfile, 'a') ;
            $this->debug();
        }
    }

    /*
     * Discrepency: this function is currently NOT called, as
     * no database "close connection" call is made.
     */
    private function logfileClose()
    {
        if ($this->logging && $this->file_handler) {
            fclose($this->file_handler);
        }
    }

    /*
     * Logs and displays errors.
     * Prerequisite: initLogfile() should have been called by now.
     */
    private function debug()
    {
        // Spit out the error to the browsers - probably not very wise for a production system.
        if ($this->show_errors) {
            echo $this->error_msg;
            if (strlen($this->sql_errored) > 0) {
                echo '<br>' . $this->sql_errored;
            }
        }
        // Write to the logging file.
        if ($this->logging) {
            if ($this->file_handler) {
                fwrite($this->file_handler, $this->error_msg);
            } else {
                return false;
            }
        }
    }

    public static function call($procedure, $procArgs)
    {
        $db = PDOWrapper::getInstance();
        if (!is_array($procArgs)) {
            $sql = "CALL $procedure ($procArgs)";
        } else {
            $sql = "CALL $procedure (".implode(', ', $procArgs).")";
        }

        if ($db->show_sql) {
            $db->showSQL($sql);
        }
        
        if ((empty($sql)) || (empty($db->connection))) {
            $db->error_msg = "\r\n" . "SQL Statement is <code>null</code> or connection is null - " . date('H:i:s');
            $db->sql_errored = $sql;
            $db->debug();
            return false;
        }
        
        $conn = $db->connection;
        $data = array();

        if ($result = $conn->query($sql)) {
            foreach ($result as $row) {
                $data[] = $row;
            }
        }
        return empty($data) ? false : $data;
    }

    /*
     * Cleanes variable for SQL, so escapes quotation marks, etc.
     */
    public static function cleanse($str)
    {
        if (get_magic_quotes_gpc()) {
            $str = stripslashes($str);
        }
        $special = array(
            "\x00" => '\x00',
            "\n" => '\n',
            "\r" => '\r',
            '"' => 'ʺ',
            '\\' => '\\\\',
            "'" => "\\'",
            '"' => '\"',
            "\x1a" => '\x1a'
        );
        foreach ($special as $key => $val) {
            if (is_bool($str)) {
                return $str ? 1 : 0;
            }
            $str = str_replace($key, $val, $str);
        }

        return $str;
        
        //return mysql_real_escape_string(strip_tags(trim($str)));
    }

    /*
     * If the string isn't empty, cleanse it. Otherwise, return NULL to
     * be included directly in an SQL query.
     */
    public static function cleanseNull($str)
    {
        return (is_null($str)) ? 'NULL' : PDOWrapper::cleanse($str);
    }
    
    public static function cleanseNullOrWrapStr($str)
    {
        return (!$str) ? 'NULL' : PDOWrapper::cleanseWrapStr($str);
    }
    
    private function showSQL($q)
    {
        echo '<pre>'.$q.'</pre>';
    }

    public static function cleanseWrapStr($str)
    {
        return '\'' . PDOWrapper::cleanse($str) . '\'';
    }
}
