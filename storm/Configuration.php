<?php namespace storm;
/**
 * @author Dylan Vorster <dylan@eezipay.com>
 * @author Rory van Heerden <rory@eishgaming.co.za>
*/
class Configuration {
    
    protected static $configurationInstances = [];
    /**
     *
     * @var array
     */
    protected $values;

    public function __construct($name = null) {
		if (is_null($name)) {
			
            $name = $this->extractClassName(get_class($this));
        }
        self::addConfiguration($name, $this);
        $this -> values = array();
    }
    
    public function set($name, $value) {
        $this->values[$name] = $value;
    }
    
    public function get($name) {
        if (!isset($this->values[$name])) {
            throw new \Exception("Required value '{$name}' not set");
        }
        return $this->values[$name];
    }
    
    public function getOptional($name, $ifnull) {
        try {
            return $this->get($name);
        } catch (\Exception $ex) {
            return $ifnull;
        }
    }
    
    protected static function extractClassName($class) {
        return str_replace(__NAMESPACE__."\\", '', $class);
    }


    /**
     * Sets a configuration value on base class instances with the name set to
     * the first param
     * @param string $configuration
     * @param string $name
     * @param string $value
     */
    public static function setValue($configuration, $name, $value) {
        try {
            $config = self::fetchFirstConfiguration($configuration);
        } catch (\Exception $ex) {
            $config = new static($configuration);
        }
        $config->set($name, $value);
    }
    /**
     * Storage
     */
    protected static function addConfiguration($name, Configuration &$instance) {
        if (!isset(self::$configurationInstances[$name])) {
            self::$configurationInstances[$name] = [];
        }
        self::$configurationInstances[$name][] = $instance;
    }
    
    /**
     * 
     * @param string $name
     * @throws \Exception
     * @returns array Array of \storm\Configuration
     */
    public static function fetchConfigurations($name) {
        if (!isset(self::$configurationInstances[$name])) {
            throw new \Exception("Error: No configuration exists for {$name}");
        }
        return self::$configurationInstances[$name];
    }
    
    /**
     * 
     * @param string $name
     * @throws \Exception
     * @returns \storm\Configuration
     */
    public static function fetchFirstConfiguration($name) {
        $configs = self::fetchConfigurations($name);
        return $configs[0];
    }
}