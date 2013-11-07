<?php namespace storm;
/**
 * @author Dylan Vorster <dylan@eezipay.com>
 * @author Rory van Heerden <rory@eishgaming.co.za>
*/
require_once __DIR__.'/../autoloader.php';

echo '<pre>';

Configuration::setValue("SomeApplication", "someSetting", "someValue");

class SQLConfiguration extends Configuration { }

$config = new SQLConfiguration();
$config->set("host", "localhost");
//$config->set("username", "root");
$config->set("password", "testing");

// Real life example
Configuration::setValue("PSQLConfiguration", "host", "localhost");
Configuration::setValue("PSQLConfiguration", "user", "root");
Configuration::setValue("PSQLConfiguration", "pass", "");
Configuration::setValue("PSQLConfiguration", "database", "LP_USERS");

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
    
    echo "SQLConfiguration->get->password".
        Configuration::fetchFirstConfiguration("SQLConfiguration")->get("database");
    echo "\n";
} catch (\Exception $ex) {
    echo $ex->getMessage();
    echo "\n";
}

try {
    PSQL::readConfig();
} catch (\Exception $ex) {
    echo $ex->getMessage();
    echo "\n";
}
PSQL::connect();

echo '</pre>';