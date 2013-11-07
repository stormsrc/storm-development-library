<?php
/**
 * @author Dylan Vorster
 */
spl_autoload_register(function($className){
	$exploded = explode('\\', $className);
	if(count($exploded) !== 2){
		return false;
	}
	if($exploded[0] == 'storm'){
		require_once __DIR__.'/classes/'.$exploded[1].'.class.php';
		return true;
	}
	return false;
},false);
?>
