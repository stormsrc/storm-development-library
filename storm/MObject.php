<?php namespace storm;
/**
 * Extend this class to make a cachable object
 * 
 * @author Dylan Vorster
 */
abstract class MObject{
	
	protected $id;
	private static $memcache;
	public static $debug = false;
	
	function __construct($id) {
		$this->id = $id;
	}
	
	private static function checkResource(){
		//make a memcache object if we dont have one
		if(self::$memcache === NULL){
			self::$memcache = new \Memcache();
			self::$memcache->connect('localhost', 11211);
		}
	}
	
	/**
	 * Checks whether the item exists or not
	 * @param type $id
	 * @return type
	 */
	public static function isStored($id){
		self::checkResource();
		return self::$memcache->get(self::getStoreKey(get_called_class(),$id)) !== false;
	}
	
	/**
	 * 
	 * @param type $id
	 * @return \static
	 */
	public static function &get($id,$force = false){
		if(self::$debug){
			$force = true;
		}
		//make a memcache object if we dont have one
		self::checkResource();
		if(!$force){
			$get = self::$memcache->get(self::getStoreKey(get_called_class(),$id));
		}
		
		//could not get the object
		if($force || $get === false){
			$get = new static($id);
			$get->save();
		}
		return $get;
	}
	
	/**
	 * Save the object in memory
	 */
	public function save(){
		
		//remove all parents if any
		self::checkResource();
		
		//clear the old ones
		$parents = class_parents($this);
		foreach ($parents as $value) {
			self::$memcache->delete(self::getStoreKey($value, $this->id));
		}
		
		//create the new ones
		self::$memcache->set(self::getStoreKey(get_called_class(),$this->id), $this, false, 60*10);
	}
	
	public static function getStoreKey($classname,$key){
		return $classname.'__'.$key;
	}
	
	public function getID() {
		return $this->id;
	}

}