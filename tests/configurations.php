<?php namespace storm;
/**
 * @author Dylan Vorster <dylan@eezipay.com>
 * @author Rory van Heerden <rory@eishgaming.co.za>
*/
require_once __DIR__.'/../autoload.php';

echo '<pre>';

Configuration::setValue("SomeApplication", "someSetting", "someValue");

class SQLConfiguration extends Configuration { }

$config = new SQLConfiguration();
$config->set("host", "localhost");
//$config->set("username", "root");
$config->set("password", "testing");

// Real life example
PSQLConfiguration::setValue("PSQLConfiguration", "host", "127.0.0.1");
PSQLConfiguration::setValue("PSQLConfiguration", "port", "3306");
PSQLConfiguration::setValue("PSQLConfiguration", "user", "root");
PSQLConfiguration::setValue("PSQLConfiguration", "pass", "unsafepassword");
PSQLConfiguration::setValue("PSQLConfiguration", "db", "test");
// This next value is not required but recommended, it defaults the db value so 
// if you don't expect the database name to change then it's ok to leave this
// out
PSQLConfiguration::setValue("PSQLConfiguration", "name", "readwrite_user");

// Create read only user for example
$config = new PSQLConfiguration("127.0.0.1", "3306", "readonly", "donttrythisathomekids", "test", "readonly_user");

echo "SomeApplication->get->someSetting: ".
        Configuration::fetchFirstConfiguration("SomeApplication")->get("someSetting");
echo "\n";

try {
    echo "SQLConfiguration->get->host: ".
        Configuration::fetchFirstConfiguration("SQLConfiguration")->get("host");
    echo "\n";
    
    echo "SQLConfiguration->getOptional->username: ".
        Configuration::fetchFirstConfiguration("SQLConfiguration")->getOptional("username", "default_root");
    echo "\n";
    
    echo "SQLConfiguration->get->password: ".
        Configuration::fetchFirstConfiguration("SQLConfiguration")->get("password");
    echo "\n";
    
    // This will fail because db is not optional in this example
    echo "SQLConfiguration->get->db".
        Configuration::fetchFirstConfiguration("SQLConfiguration")->get("db");
    echo "\n";
} catch (\Exception $ex) {
    echo $ex->getMessage();
    echo "\n";
}

echo "\n";
echo "Testing PSQL";
echo "\n";
PSQL::debugMode(true);
echo "Running PSQL readConfig\n";
PSQL::readConfig();
echo "Running PSQL connect\n";
PSQL::connect();
echo "Running PSQL query CREATE TABLE IF NOT EXISTS `test` with readwrite_user\n";
PSQL::query("
    CREATE TABLE IF NOT EXISTS `test` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `info` int(11),
    `string` VARCHAR(255) NOT NULL,
    `bool` tinyint(1) NOT NULL,
    PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8
", "readwrite_user");
$i = 0;
do {
    try {
        $connection = ($i % 2 == 1?"readwrite_user":"readonly_user");

        echo "Running PSQL query SELECT * FROM `test` with {$connection}\n";
        $q2 = PSQL::query("SELECT * FROM `test`", $connection);
        while ($r1 = $q2 -> fetch()) {
            print_r($r1);
        }

        echo "Running PSQL query INSERT INTO `test`(`info`, `string`, `bool`) VALUES(?, ?, ?) with {$connection}\n";
        $newID = PSQL::insert("INSERT INTO `test`(`info`, `string`, `bool`) VALUES(?, ?, ?)", $connection,
                array(rand(100,999), "No bindvars test with true bool", true));

        echo "Running PSQL query SELECT * FROM `test` WHERE `id` = ? with {$connection}\n";
        $q1 = PSQL::query("SELECT * FROM `test` WHERE `id` = ?", $connection, array($newID));
        while ($r1 = $q1 -> fetch()) {
            print_r($r1);
        }

        echo "Running PSQL query SELECT * FROM `test` with {$connection} (again)\n";
        $q3 = PSQL::query("SELECT * FROM `test`", $connection);
        while ($r1 = $q3 -> fetch()) {
            print_r($r1);
        }

        echo "Running PSQL query INSERT INTO `test`(`info`, `string`, `bool`) VALUES(:intvalue, :stringvalue, :boolvalue) with {$connection}\n";
        $newID = PSQL::insert("INSERT INTO `test`(`info`, `string`, `bool`) VALUES(:intvalue, :stringvalue, :boolvalue)", $connection,
                array(
                    ":intvalue" => null,
                    ":stringvalue" => "With bindvars test with false bool",
                    ":boolvalue" => false)
                );
        
        echo "Running PSQL query INSERT INTO `test`(`info`, `string`, `bool`) VALUES(:intvalue, :stringvalue, :boolvalue) with {$connection}\n";
        $newID = PSQL::insert("INSERT INTO `test`(`info`, `string`, `bool`) VALUES(:intvalue, :stringvalue, :boolvalue)", $connection,
                array(
                    ":intvalue" => null,
                    ":stringvalue" => "With bindvars test with true bool",
                    ":boolvalue" => true)
                );
    } catch (\Exception $ex) {
        echo $ex->getMessage();
        echo "\n";
    }
    $i++;
} while ($i<3);

try {
    echo "Selecting all the nulls \n";
    $null = null;
    $nulls = PSQL::query("SELECT * FROM `test` WHERE `info` = :nullvalue", $connection, array(":nullvalue" => $null));
    while ($r1 = $nulls -> fetch()) {
        print_r($r1);
    }
    
    echo "Selecting all the trues \n";
    $trues = PSQL::query("SELECT * FROM `test` WHERE `bool` = :truevalue", $connection, array(":truevalue" => true));
    while ($r1 = $trues -> fetch()) {
        print_r($r1);
    }
    
    echo "Selecting all the falses \n";
    $falses = PSQL::query("SELECT * FROM `test` WHERE `bool` = :falsevalue", $connection, array(":falsevalue" => false));
    while ($r1 = $falses -> fetch()) {
        print_r($r1);
    }
} catch (\Exception $ex) {
        echo $ex->getMessage();
        echo "\n";
    }

echo '</pre>';
