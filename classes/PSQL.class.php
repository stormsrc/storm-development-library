<?php namespace storm;
/**
 * @author Dylan Vorster <dylan@eezipay.com>
 * @author Rory van Heerden <rory@eishgaming.co.za>
 */
class PSQL {

	// configuration
    /**
     *
     * @var \storm\PSQLConfiguration
     */
        private static $config = NULL;
        private static $debug = false;
        
	//instances
	public static $sqlres = NULL;
	public static $result = NULL;
	public static $queries = NULL;
        public static $defaultDatabase = NULL;

	/**
	 * Configuration methods
	 */
        private static function fetchDefault($name) {
            return (is_null($name)?self::$defaultDatabase:$name);
        }
        
	public static function readConfig($name = NULL) {
            if (self::$debug) echo "=>".__METHOD__."\n"; 
                $name = self::fetchDefault($name);
		if (!is_null($name) && !is_null(self::$defaultDatabase) && $name == self::$defaultDatabase) {
                    return false;
                }
                $config = PSQLConfiguration::getDatabaseConfig($name);
                
                if (is_null($name)) {
                    // Discover default database
                    self::$defaultDatabase = $config->getName();
                }
                
		self::setConnectionDetails($config);
		return true;
	}
	
	public static function setConnectionDetails(PSQLConfiguration $config){
            if (self::$debug) echo "=>".__METHOD__."\n";     
            self::$config = $config; 
	}

	/**
	  Internal dont use it (most cases)
	 */
	public static function connect($name = NULL) {
            if (self::$debug) echo "=>".__METHOD__."\n"; 
            $name = self::fetchDefault($name);
            if (is_null(static::$sqlres)) {
		self::$sqlres = array();
            }

            if (!isset(static::$sqlres[$name])) {
                self::readConfig($name);
                if (self::$debug) echo "==>Adding new PDO object\n";
                self::$sqlres[$name] = new \PDO('mysql:host=' . self::$config->getHost() . ';dbname=' . self::$config->getDB(), self::$config->getUser(), self::$config->getPass());
                self::$sqlres[$name]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }
	}

	/**
	 * 
	 * @param string $sql
	 * @param string $name
	 * @return \PDOStatement
	 */
	public static function prepare($sql, $name = null) {
            if (self::$debug) echo "=>".__METHOD__."\n"; 
            $name = self::fetchDefault($name);
            static::connect($name);
		//if the queries are null, add them
		if (is_null(static::$queries)) {
			static::$queries = array();
		}

		//if the db is not found add it
		if (!isset(static::$queries[$name])) {
			static::$queries[$name] = array();
		}

		//if the queries are null, add them
		foreach (static::$queries[$name] AS $query) {
			if ($query['query'] == $sql) {
                            if (self::$debug) echo "==>Preparing old query\n";
                            return $query['statement'];
			}
		}
                if (self::$debug) echo "==>Preparing new query\n";
		$stmt = static::$sqlres[$name]->prepare($sql);
		static::$queries[$name][] = array('query' => $sql, 'statement' => $stmt);
		return $stmt;
	}

	/**
	 * Use this function to insert data into a table
	 * @param string $sql - the SQL Query with '?' for placeholders
	 * @param string $name - the database config name
	 * @param array $vars - the variables in an array format
	 */
	public static function insert($sql, $name, $vars = array()) {
            if (self::$debug) echo "=>".__METHOD__."\n"; 
            $stmt = static::prepare($sql, $name);
		if (!is_array($vars)) {
			throw new \Exception("vars needs to be an array in PSQL");
		}
		foreach ($vars as $key => $var) {
			if (is_string($key)) {
				$stmt->bindParam($key, $var);
			} else {
				$stmt->bindValue($key + 1, $var);
			}
		}
		$stmt->execute();
		return static::$sqlres[$name]->lastInsertId();
	}

	/**
	 * Use this function to execute a prepared statement
	 * @param string $sql - the SQL Query with '?' for placeholders
	 * @param string $name - the database config name
	 * @param array $vars - the variables in an array format
	 * @return \PDOStatement "the results"
	 */
	public static function query($sql, $name, $vars = array()) {
		if (self::$debug) echo "=>".__METHOD__."\n"; 
                $stmt = static::prepare($sql, $name);
		if (!is_array($vars)) {
			throw new \Exception("vars needs to be an array in PSQL");
		}

		foreach ($vars as $key => $var) {
			if (is_string($key)) {
				$stmt->bindParam($key, $var);
			} else {
				$stmt->bindValue($key + 1, $var);
			}
		}
		$stmt->execute();
		return $stmt;
	}
        
        
        public static function debugMode($setting) {
            self::$debug = ($setting == true);
        }
}

?>
