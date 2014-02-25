<?php namespace storm;
/**
 * This class is specifically designed for XML manipulation
 * @author Dylan
 */
class XMLFunctions{

	/* This class validates as to whether there are any null values in the launchpad request */
	public static function checkForNull($xml,$parent = false){
		$data = $xml -> children();
		foreach($data as $d => $value){
			if($value -> count() != 0){
				if($d  =='Request'){
					$res = self::checkForNull($value,true);
				}else{
					$res = self::checkForNull($value);
				}
				if($res == true){
					return true;
				}
			}else if(((string)$value) == null){
					if(!$parent){
						return true;
					}
				}
		}
		return false;
	}

	/* This function takes an array and checks to see if the nodes exist */
	public static function checkForNodes($xml,$array){
		$data = $xml -> children();
		foreach($array as $a){
			if(is_array($a)){ if(self::checkForNodes($data,$a) == false) return false;}
			$valid = false;
			foreach($data as $d => $value){

				if(is_array($a)){
					if(strpos(key($a),' | ') === false){
						if(key($a) == (string)$d){$valid = true; break; }
					}else{
						$temp = explode(' | ',key($a));
						foreach($temp as $t){
							if($t == (string)$d){$valid = true; break; }
						};
					}
				}
				else{
					if(strpos($a,' | ') === false){
						if($a == (string)$d){$valid = true; break; }
					}else{
						$temp = explode(' | ',$a);
						foreach($temp as $t){
							if($t == (string)$d){$valid = true; break; }
						}
					}
				}
			}
			if(!$valid) return false;
		}
		return true;
	}

	public static function getNode($xml,$name){
		if(strpos($name,' | ') !== false){
			$temp = explode(' | ',$name);
			foreach($temp as $t){
				if(isset($xml -> $t)){
					return $xml -> $t;
				}
			}
			return null;
		}
		return $xml -> $name;
	}

	
	public static function arrayToXML($arr,$name=null) {
		$str = "";
		foreach($arr as $k => $v) {
			if(is_array($v)){
				$key = array_keys($v);
				if(count($key)>0){
					if(is_numeric($key[0])){
						$str.= self::arrayToXML($v,$k);						
						continue;
					}
				}
			}
			$temp =explode(' ',$k);
			if(is_numeric($k)){
				$str .= "<{$name}";
			}else{
				$str .= "<{$temp[0]}";
			}
			if (count($temp) > 1) {
				for($i=1;$i<count($temp);$i++) {
					$str .= " {$temp[$i]}";
				}
			}
			if (is_array($v)) {
				if (is_numeric($k)) {
					$str .= ">".self::arrayToXML($v,$k)."</{$name}>";
				} else {
					$str .= ">".self::arrayToXML($v,$k)."</{$temp[0]}>";
				}
			} else if (is_null($v)) {
					$str .= " />";
				} else {
				if(is_numeric($k)){ 
					$str .= ">".utf8_encode($v)."</{$name}>";
				}else{
					$str .= ">".utf8_encode($v)."</{$temp[0]}>";
				}
			}
		}
		return $str;
	}

	public static function arrayToLaunchpadXML($arr) {
		
		if (is_array($arr)) {

			$str = "<Code>0</Code>" . self::arrayToXML($arr);
		} else {
			$str = "<Code>0</Code>";
		}
		return $str;
	}
}
?>