<?php

require_once __DIR__.'/../Common/Settings.class.php';
require_once __DIR__.'/../Common/lib/PDOWrapper.class.php';

class UnitTestHelper
{
    private function __constuct() {}
    
    public static function teardownDb()
    {
        $dsn = "mysql:host=".Settings::get('db.unit_test_server').";dbname=".Settings::get('db.unit_test_database').";port=".Settings::get('db.unit_test_server_port');
        $dsn1 = "mysql:host=".Settings::get('db.server').";dbname=".Settings::get('db.database').";port=".Settings::get('db.server_port');
        assert($dsn1 != $dsn);
        
        PDOWrapper::$unitTesting = true;
        $conn = new PDO($dsn,
                        Settings::get('db.unit_test_username'), Settings::get('db.unit_test_password'),
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
