<?php namespace storm;
/**
 * @author bagf
 */
class OpenVPN{

	static $clients;
	const KeyDir = '/etc/openvpn/easy-rsa/2.0/keys';
	const CCDDir = '/etc/openvpn/ccd';
	
	public static function getVPNCLient($commonName){
		$data = self::scanClients(true);
		foreach($data as $name => $client){
			if($name == $commonName){
				return $client;
			}
		}
		return false;
	}
	
	public static function scanClients($assoc = false, $force = false) {
		if (!is_array(self::$clients) || $force) self::$clients = array();
		
		if (!count(self::$clients)) {
			// Pull connections from management interface
			$checkStatus = true;
			try {
				$s = new ESocket("172.16.0.1", 5051);
			} catch (Exception $exp) {
				//die("<p><i>".$exp -> getMessage()."</i></p>");
				$checkStatus = false;
			}
			$str = $s -> receiveStr();
			$s -> sendLine('status');
			$ovpnHeadings = array();
			$data = array();
			
			while($line = $s -> receiveStr()){
				// Establish
				if(strpos($line,'GLOBAL STATS') !== false){
					break;
				}
				if(strpos($line,'ROUTING TABLE')!== false){
					$data['ROUTE_TBL'] = array();
					$ovpnHeadings = array();
					continue;
				} else if (strpos($line,'OpenVPN CLIENT LIST')!==false) {
					$line = $s -> receiveStr();
					$data['CLIENT_LS'] = array();
					$ovpnHeadings = array();
					continue;
				} else if (!count($data)) {
					continue;
				}
				if (!count($ovpnHeadings)) {
					$ovpnHeadings = explode(',',$line);
					continue;
				}
				
				end($data);
				$key = key($data);
				$lnExp = explode(',',$line);
				
				$temp = array();
				$cn = null;
				foreach($lnExp as $k => $d){
					if (trim($ovpnHeadings[$k]) == "Common Name") {
						$cn = $d;
					}
					$temp[''.trim($ovpnHeadings[$k])] = $d;
				}
				if (!is_null($cn)) {
					$data[$key][$cn] = $temp;
				} else {
					$data[$key][] = $temp;
				}
			}
			clearstatcache();
			$keys = new \DirectoryIterator(self::KeyDir);
			foreach ($keys as $k) {
				if ($k->isDot() || $k->isDir()) continue;
				$e = explode(".", $k->getFilename());
				if ($e[(count($e)-1)] != "crt") continue;
				if ($e[0] == "ca") continue;
				
				$e[0] = trim($e[0]);
				$time = $k->getCTime();
				
				
				$ovpn = new OpenVPNClient($e[0], $time);
				
				$route = (isset($data['ROUTE_TBL'][$e[0]])? $data['ROUTE_TBL'][$e[0]] : false );
				$client = (isset($data['CLIENT_LS'][$e[0]])? $data['CLIENT_LS'][$e[0]] : false );
				
				if (is_file(self::CCDDir."/{$e[0]}") && is_readable(self::CCDDir."/{$e[0]}")) {
					$file = file(self::CCDDir."/{$e[0]}");
					$clientAddr = explode(" ", $file[0]);
					if (isset($clientAddr[1])) {
						$ovpn->setAllocatedAddress($clientAddr[1]);
						$ovpn->setFixed();
					}
				} else if ($route) {
					$ovpn->setAllocatedAddress($route['Virtual Address']);
				}
				
				if ($client) {
					$ipExp = explode(":", $client['Real Address']);
					$ovpn->setRealAddress($ipExp[0]);
					$ovpn->setReceived($client['Bytes Sent']);
					$ovpn->setSent($client['Bytes Received']);
					$ovpn->setConnectedTime($client['Connected Since']);
				}
				
				if ($route && $client) $ovpn->setStatus(OpenVPNClient::ONLINE);
				
				if ($assoc) {
					self::$clients[$e[0]] = $ovpn;
				} else {
					self::$clients[] = $ovpn;
				}
			}
		}
		return self::$clients;
	}
	
	public static function getClients() {
		self::scanClients();
		return self::$clients;
	}
}
?>
