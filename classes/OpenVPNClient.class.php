<?php namespace storm;
/**
 * @author bagf
 */
class OpenVPNClient{
	const ONLINE = 1;
	const OFFLINE = 2;
	
	protected $commonName;
	protected $createdDate;
	protected $allocatedAddr = null;
	protected $fixedAddr = null;
	protected $status;
	protected $realAddress;
	protected $received;
	protected $sent;
	protected $connectedTime;

	function __construct($commonName, $createdDate) {
		$this -> commonName = $commonName;
		$this -> createdDate = $createdDate;
		$this -> fixedAddr = false;
		$this -> status = self::OFFLINE;
		$this -> realAddress = null;
		$this -> connectedTime = '';
	}
	
	function setRealAddress($address) {
		$this ->realAddress = $address;
	}
	
	function setAllocatedAddress($address) {
		$this ->allocatedAddr = $address;
	}
	
	function setFixed($fixed = true) {
		$this -> fixedAddr = $fixed;
	}
	
	function setStatus($status) {
		$this ->status = $status;
	}
	
	function setReceived($received) {
		$this ->received = $received;
	}
	
	function setSent($sent) {
		$this ->sent = $sent;
	}
	
	function setConnectedTime($time) {
		$this ->connectedTime = $time;
	}
	
	function getRealAddress() {
		return $this ->realAddress;
	}
	
	function getAllocatedAddress() {
		return $this ->allocatedAddr;
	}
	
	function isFixed() {
		return $this ->fixedAddr;
	}
	
	function getStatus() {
		return $this ->status;
	}
	
	function getCommonName() {
		return $this ->commonName;
	}
	
	function getCreatedDate() {
		return $this ->createdDate;
	}
	
	function getServerStatus() {
		return $this ->status;
	}
	
	function getReceived() {
		return $this ->received;
	}
	
	function getSent() {
		return $this ->sent;
	}
	
	function getConnectedTime() {
		return $this ->connectedTime;
	}
}
?>
