<?php namespace storm;
/**
 * @author Dylan Vorster
 */
class StandardToolkit{
	
	/**
	 * Checks if there is internet connectivity
	 * 
	 * @param type $url
	 * @return boolean
	 */
	public static function hasInternet($url = 'www.google.com'){
		$connected = @\fsockopen($url, 80,$num,$err,5); //website and port
		if ($connected){
			fclose($connected);
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Searches for a class based
	 * @param type $dir
	 * @param type $class
	 * @return boolean
	 */
	public static function findClass($dir, $class,Array $extensions = ['class','interface','trait']) {
		$it = new \DirectoryIterator($dir);
		foreach ($it as $file) {
			if ($file->isFile()) {
				foreach ($extensions as $extension) {
					if ($file == "{$class}.{$extension}.php") {
						return "{$dir}/{$file}";
					}
				}
			}
			//look in the folder but ignore hidden directories
			else if ($file->isDir() && !$file->isDot() && $file->getFilename()[0] !== '.') {
				$response = self::findClass("{$dir}/" . $file->getFilename(), $class);
				if ($response !== false) {
					return $response;
				}
			}
		}
		return false;
	}
	
	/**
	 * 
	 * @param type $directory
	 * @param type $startsWith
	 * @return \DirectoryIterator
	 */
	public static function fileMatch($directory,$startsWith){
		$it = new \DirectoryIterator($directory);
		foreach ($it as $file) {
			if ($file->isDir()) {
				continue;
			}
			if(self::startsWith($file->getFilename(), $startsWith)){
				return $file;
			}
		}
		return false;
	}
	
	public static function startsWith($haystack, $needle){
		return $needle === "" || strpos($haystack, $needle) === 0;
	}
}