<?php
/**
 * @author Dylan Vorster
 */
spl_autoload_register(function($className){
	$className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
	$fileName = __DIR__.DIRECTORY_SEPARATOR."{$className}.php";
        if (!is_file($fileName)) {
            return false;
        }
	require_once $fileName;
        return true;
},false);
?>
