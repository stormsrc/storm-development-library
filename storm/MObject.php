<?php namespace storm;
/**
 * Extend this class to make a cachable object
 * @author Dylan Vorster
 */
abstract class MObject{
	
	protected $id;
	private static $memcache;
	public static $debug = false;
	
	function __construct($id) {
		$this->id = $id;
	}
	
	/**
	 * 
	 * @param type $id
	 * @return \static
	 */
	public static function get($id,$force = false){
		if(self::$debug){
			$force = true;
		}
		//make a memcache object if we dont have one
		if(self::$memcache === NULL){
			self::$memcache = new \Memcache();
			self::$memcache->connect('localhost', 11211);
		}
		if(!$force){
			$get = self::$memcache->get(self::getStoreKey($id));
		}
		
		//could not get the object
		if($force || $get === false){
			$get = new static($id);
			
			//store the item for an hour
			self::$memcache->set(self::getStoreKey($id), $get, false, 60*10);
		}
		return $get;
	}
	
	public final function save(){
		if(self::$memcache === NULL){
			self::$memcache = new \Memcache();
			self::$memcache->connect('localhost', 11211);
		}
		self::$memcache->set(self::getStoreKey($this->id), $this, false, 60*10);
	}
	
	public static function getStoreKey($key){
		return get_called_class().'__'.$key;
	}
	
	public function getID() {
		return $this->id;
	}

}