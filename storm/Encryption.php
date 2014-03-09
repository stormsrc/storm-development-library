<?php namespace storm;
/**
 * @author Dylan Vorster <dylan@eezipay.com>
 * @author Rory van Heerden <rory@eishgaming.co.za>
*/
class Encryption{
	
	public static function generateKey($length){
		$newKey = "";
		for ($i = 0; $i < $length; $i++) {
			if (rand(0, 1) == 0) {
				$newKey = $newKey.rand(0, 9);
			} else {
				$newKey = $newKey.chr(rand(97, 122));
			}
		}
		return $newKey;
	}
	
	public static function generatePin($length){
		$newKey = "";
		for ($i = 0; $i < $length; $i++) {
			$newKey = $newKey.rand(0, 9);
		}
		return $newKey;
	}
	
}
?>