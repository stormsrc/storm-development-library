<?php namespace storm;
/**
 * @author Dylan Vorster <dylan@eezipay.com>
 * @author Rory van Heerden <rory@eishgaming.co.za>
*/
class PSQLConfiguration extends Configuration {
    /**
     * 
     * @param type $database
     * @return \storm\Configuration
     */
    public static function getDatabaseConfig($database) {
        $configs = static::fetchConfigurations(static::extractClassName(__CLASS__));
        foreach ($configs as $config) {
            if ($config->getOptional("database", null) == $database) {
                return $config;
            }
        }
        return static::fetchFirstConfiguration(static::extractClassName(__CLASS__));
    }
}