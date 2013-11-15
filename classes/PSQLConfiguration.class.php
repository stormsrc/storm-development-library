<?php namespace storm;

/**
 * @author Dylan Vorster <dylan@eezipay.com>
 * @author Rory van Heerden <rory@eishgaming.co.za>
 */
class PSQLConfiguration extends Configuration {

	/**
	 * 
	 * @param type $database
	 * @return \storm\PSQLConfiguration
	 */
	public static function getDatabaseConfig($database) {
		$configs = static::fetchConfigurations(static::extractClassName(__CLASS__));
		foreach ($configs as $config) {
			if ($config->getOptional("db", null) == $database) {
				return $config;
			}
		}
		return self::fetchFirstConfiguration(static::extractClassName(__CLASS__));
	}

	public function __construct($host = NULL, $port = NULL, $user = NULL, $pass = NULL, $db = NULL,$name = NULL) {	
		//standard invoke
		if ($port == NULL) {
			parent::__construct($host);
		}else{
			parent::__construct($name);
			$this->setup($host, $port, $user, $pass, $db);
		}
	}

	public function setup($host, $port, $user, $pass, $db = NULL) {
		$this->set('user', $user);
		$this->set('pass', $pass);
		$this->set('host', $host);
		$this->set('port', $port);
		$this->set('db', $db);
	}

	public function getUser() {
		return $this->get('user');
	}

	public function getPass() {
		return $this->get('pass');
	}

	public function getHost() {
		return $this->get('host');
	}

	public function getPort() {
		return $this->get('port');
	}

	public function getDB() {
		return $this->getOptional('db', NULL);
	}

}
