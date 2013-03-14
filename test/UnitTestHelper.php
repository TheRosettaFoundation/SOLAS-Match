<?php

require_once __DIR__.'/../Common/Settings.class.php';
require_once __DIR__.'/../Common/lib/PDOWrapper.class.php';

class UnitTestHelper
{
    private function __constuct() {}
    
    public static function teardownDb()
    {
        $dsn = "mysql:host=".Settings::get('unit_test.server').";dbname=".Settings::get('unit_test.database').
                ";port=".Settings::get('unit_test.port');
        $dsn1 = "mysql:host=".Settings::get('database.server').";dbname=".Settings::get('database.database').
                ";port=".Settings::get('database.server_port');
        assert($dsn1 != $dsn);
        
        PDOWrapper::$unitTesting = true;
        $conn = new PDO($dsn,
                        Settings::get('unit_test.username'), Settings::get('unit_test.password'),
                        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));         
        unset($dsn);
        unset($dsn1);
        $tables = $conn->query("SELECT t.TABLE_NAME FROM information_schema.`TABLES` t WHERE t.TABLE_SCHEMA='Unit-Test'
                                AND t.TABLE_NAME NOT IN('Languages','Countries', 'TaskTypes', 'TaskStatus')");
        
        foreach($tables as $table) $conn->query("DELETE FROM $table[0]");
        
        $conn->query("REPLACE INTO `Badges` (`id`, `owner_id`, `title`, `description`) VALUES
                    (3, NULL, 'Profile-Filler', 'Filled in required info for user profile.'),
                    (4, NULL, 'Registered', 'Successfully set up an account'),
                    (5, NULL, 'Native-Language', 'Filled in your native language on your user profile.');");
    }
    
    
    
    
}

?>
