<?php namespace storm;
/**
 * @author Dylan Vorster <dylan@eezipay.com>
 * @author Rory van Heerden <rory@eishgaming.co.za>
*/

class PSQL {
    /*
     * Connection Infomation
     */
    private static $DBHost = null;
    private static $DBUser = null;
    private static $DBPass = null;
    private static $DBName = null;
    
    /*
     * Instances
     */
    public static $sqlres = null;
    public static $result = null;
    public static $queries = null;

    /**
     * Configuration methods
     */
    public static function readConfig($database = null) {
        if ((!is_null(static::$DBHost) || !is_null(static::$DBUser) || !is_null(static::$DBPass) || !is_null(static::$DBName)) && $database == static::$DBName) {
            return false;
        }

        $config = PSQLConfiguration::getDatabaseConfig($database);
        
        static::$DBHost = $config->getOptional("host", "localhost");
        static::$DBUser = $config->getOptional("user", "root");
        static::$DBPass = $config->getOptional("pass", "");
        static::$DBName = $config->getOptional("database", null);
        return true;
    }

    /**
      Internal dont use it (most cases)
     */
    public static function connect($database = null) {
        static::readConfig($database);
        if (is_null(static::$sqlres)) {
            static::$sqlres = array();
        }

        if (!isset(static::$sqlres[static::$DBName])) {
            static::$sqlres[static::$DBName] = new \PDO('mysql:host=' . static::$DBHost . ';dbname=' . static::$DBName, static::$DBUser, static::$DBPass);
            static::$sqlres[static::$DBName]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
    }

    /**
      Internal dont use it (most cases)
     */
    public static function prepare($sql, $database = null) {
        static::connect($database);
        //if the queries are null, add them
        if (is_null(static::$queries)) {
            static::$queries = array();
        }

        //if the db is not found added it
        if (!isset(static::$queries[static::$DBName])) {
            static::$queries[static::$DBName] = array();
        }

        //if the queries are null, add them
        foreach (static::$queries[static::$DBName] AS $query) {
            if ($query['query'] == $sql) {
                return $query['statement'];
            }
        }

        $stmt = static::$sqlres[static::$DBName]->prepare($sql);
        static::$queries[static::$DBName][] = array('query' => $sql, 'statement' => $stmt);
        return $stmt;
    }

    /**
     * Use this function to insert data into a table
     * @param sql - the SQL Query with '?' for placeholders
     * @param db - the database
     * @param vars - the variables in an array format
     */
    public static function insert($sql, $vars = array(), $database = null) {
        $stmt = static::prepare($sql, $database);
        if (!is_array($vars)) {
            die("VARS must be an array");
        }
        $length = count($vars);
        for ($i = 1; $i <= $length; $i++) {
            $stmt->bindParam($i, $vars[$i - 1]);
        }
        $stmt->execute();
        return static::$sqlres[static::$DBName]->lastInsertId();
    }

    /**
     * Use this function to execute a prepared statement
     * @param sql - the SQL Query with '?' for placeholders
     * @param db - the database
     * @param vars - the variables in an array format
     */
    public static function query($sql, $vars = array(), $database = null) {
        $stmt = static::prepare($sql, $database);
        if (!is_array($vars)) {
            die("VARS must be an array");
        }
        $length = count($vars);
        for ($i = 1; $i <= $length; $i++) {
            $stmt->bindParam($i, $vars[$i - 1]);
        }
        $stmt->execute();
        return $stmt;
    }

}
?>
