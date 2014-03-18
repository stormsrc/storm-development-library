<?php

namespace storm;

/**
 * @author Dylan Vorster <dylan@eezipay.com>
 * @author Rory van Heerden <rory@eishgaming.co.za>
 */
class PSQLConfiguration extends Configuration {

	/**
	 * 
	 * @param string $name
	 * @return \storm\PSQLConfiguration
	 */
	public static function getDatabaseConfig($name) {
		if (!is_null($name)) {
			$configs = static::fetchConfigurations(static::extractClassName(__CLASS__));

			foreach ($configs as $config) {
				if (!$config instanceof PSQLConfiguration) {
					continue;
				}
				if ($config->getName() == $name) {
					return $config;
				}
			}
		}
		return self::fetchFirstConfiguration(static::extractClassName(__CLASS__));
	}

	public function __construct($host = NULL, $port = NULL, $user = NULL, $pass = NULL, $db = NULL, $name = NULL, $charset = NULL) {
		//standard invoke
		if (is_null($port) && is_null($user) && is_null($pass) && is_null($db) && is_null($name)) {
			parent::__construct($host);
		} else {
			parent::__construct();
			$this->setup($host, $port, $user, $pass, $db, $name, $charset);
		}
	}

	public function setup($host, $port, $user, $pass, $db, $name = NULL, $charset = NULL) {
		$this->set('user', $user);
		$this->set('pass', $pass);
		$this->set('host', $host);
		$this->set('port', $port);
		$this->set('db', $db);
		$this->set('name', (is_null($name) ? $db : $name));
		$this->set('charset', $charset);
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
		return $this->getOptional('port',NULL);
	}

	public function getDB() {
		return $this->getOptional('db',NULL);
	}

	public function getName() {
		return $this->getOptional('name', $this->getDB());
	}
        
	public function getCharset() {
		return $this->getOptional('charset', NULL);
	}

}
