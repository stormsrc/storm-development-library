<?php namespace storm;
/**
 * @author Dylan Vorster <dylan@eezipay.com>
 * @author Rory van Heerden <rory@eishgaming.co.za>
*/
class ESocket
{
	protected $handle;
	
	// Address will be NS resolved
	function __construct($address, $port) {
		if (!filter_var($address, FILTER_VALIDATE_IP)) {
			$address = gethostbyname($address);
		}
		if ($port < 1024 || $port > 65535) {
			throw new \Exception("Invalid port");
		}
		$fh = @fsockopen($address, $port, $errorno, $errorstr);
		if (!$fh) {
			throw new \Exception("Failed to open socket error no {$errorno} [{$errorstr}]");
		}
		$this -> handle = $fh;
	}
	
	function sendStr($str, $len = NULL) {
		if (is_null($len)) {
			$len = strlen($str);
		}
		fwrite($this -> handle, $str, $len); // write some datas
	}
	
	function receiveStr() {
		return fgets($this -> handle, 6000); // Fetch some datas
	}
	
	function sendLine($str) {
		return $this -> sendStr($str."
");
	}
	
	function hasEnded() {
		return feof($this -> handle);
	}
	
	function __destruct() {
		if (!is_null($this -> handle))
		fclose($this -> handle);
	}
}
?>