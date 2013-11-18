<?php namespace storm;
/**
 * @author Dylan Vorster <dylan@eezipay.com>
 * @author Rory van Heerden <rory@eishgaming.co.za>
 */
class PSQL {

	//connection info
	private static $DBHost = NULL;
	private static $DBUser = NULL;
	private static $DBPass = NULL;
	private static $DBName = NULL;
	private static $DBPort = NULL;
	//instances
	public static $sqlres = NULL;
	public static $result = NULL;
	public static $queries = NULL;
	public static $readConfig = true;

	/**
	 * Configuration methods
	 */
	public static function readConfig($database = NULL) {
		if(!self::$readConfig){
			return false;
		}
		if ((!is_null(self::$DBHost) ||
				!is_null(self::$DBUser) ||
				!is_null(self::$DBPass) ||
				!is_null(self::$DBName)) &&
				$database == self::$DBName) {
			return false;
		}

		$config = PSQLConfiguration::getDatabaseConfig($database);
		self::$DBHost = $config->getHost();
		self::$DBUser = $config->getUser();
		self::$DBPass = $config->getPass();
		self::$DBName = $config->getDB();
		self::$DBPort = $config->getPort();
		return true;
	}
	
	public static function setConnectionDetails(PSQLConfiguration $config){
		self::$readConfig = false;
		self::$DBHost = $config->getHost();
		self::$DBUser = $config->getUser();
		self::$DBPass = $config->getPass();
		self::$DBName = $config->getDB();
		self::$DBPort = $config->getPort();
	}

	/**
	  Internal dont use it (most cases)
	 */
	public static function connect($database = NULL) {
		self::readConfig($database);
		if (is_null(static::$sqlres)) {
			self::$sqlres = array();
		}

		if (!isset(static::$sqlres[$database])) {
			self::$sqlres[$database] = new \PDO('mysql:host=' . self::$DBHost . ';dbname=' . $database, static::$DBUser, self::$DBPass);
			self::$sqlres[$database]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}
	}

	/**
	 * 
	 * @param type $sql
	 * @param type $database
	 * @return \PDOStatement
	 */
	public static function prepare($sql, $database = null) {
		static::connect($database);
		//if the queries are null, add them
		if (is_null(static::$queries)) {
			static::$queries = array();
		}

		//if the db is not found add it
		if (!isset(static::$queries[$database])) {
			static::$queries[$database] = array();
		}

		//if the queries are null, add them
		foreach (static::$queries[$database] AS $query) {
			if ($query['query'] == $sql) {
				return $query['statement'];
			}
		}

		$stmt = static::$sqlres[$database]->prepare($sql);
		static::$queries[$database][] = array('query' => $sql, 'statement' => $stmt);
		return $stmt;
	}

	/**
	 * Use this function to insert data into a table
	 * @param sql - the SQL Query with '?' for placeholders
	 * @param db - the database
	 * @param vars - the variables in an array format
	 */
	public static function insert($sql, $title, $vars = array()) {
		$stmt = static::prepare($sql, $title);
		if (!is_array($vars)) {
			throw new Exception("vars needs to be an array in PSQL");
		}
		foreach ($vars as $key => $var) {
			if (is_string($key)) {
				$stmt->bindParam($key, $var);
			} else {
				$stmt->bindValue($key + 1, $var);
			}
		}
		$stmt->execute();
		return static::$sqlres[$title]->lastInsertId();
	}

	/**
	 * Use this function to execute a prepared statement
	 * @param sql - the SQL Query with '?' for placeholders
	 * @param db - the database
	 * @param vars - the variables in an array format
	 * @return \PDOStatement "the results"
	 */
	public static function query($sql, $title, $vars = array()) {
		$stmt = static::prepare($sql, $title);
		if (!is_array($vars)) {
			throw new Exception("vars needs to be an array in PSQL");
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

}

?>