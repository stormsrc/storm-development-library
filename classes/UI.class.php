<?php namespace storm;
/**
	@author Dylan Vorster
*/
class UI{
	
	/**
		Returns NULL or the specified if the value is NULL
	*/
	public static function pretty($data,$pretty = true,$other = 'na'){
		if($pretty){
			if($data === NULL){
				return '<i style="color:gray">'.$other.'</i>';
			}else if($data === ''){
				return '<i style="color:#00a9ff">empty</i>';
			}
		}
		return $data;
	}
	
	public static function doPostCheck($details = array()){
		$args = func_get_args();
		foreach($args as $arg){
			if(!isset($_POST[$arg])){
				self::err('API Error','Important parameters where missing: '.$arg);
			}
		}
		return true;
	}
	
	public static function sqlBtn($name,$id,$modID,$pageID,$extras = [],$pageLoad = true,$displayAs = true){
		$temp = "{id:',$id,'";
		foreach($extras as $key => $value){
			$temp.=','.$key.":\'".$value."\'";
		}
		$temp .= '}';
		if($pageLoad){
			return "CONCAT('<button onclick=\"LP.getPage($modID,$pageID,$temp)\">$name</button>')".($displayAs?" AS '$name'":'');
		}else{
			return "CONCAT('<button onclick=\"LP.sendForm($modID,$pageID,$pageID,$temp)\">$name</button>')".($displayAs?" AS '$name'":'');
		}
	}
	
	
	public static function clean($data = NULL){
		$isPost = false;
		if(is_null($data)){
			$data = $_POST;
			$isPost = true;
		}
		
		if(is_array($data)){
			foreach($data as $key => $d){
				if($d !== NULL){
					$data[$key] = self::clean($d);	
				}
			}
		}else{
			//this is needed because the DB is Western European Latin (ISO)
			//utf8_decode => converts to this format
			$data = utf8_decode(trim($data));
			if(''.$data == ''){
				$data = NULL;
			}
		}
		if($isPost){
			$_POST = $data;
		}
		return $data;
	}
	
	//!------------ START OF ACTIONS -------------
	
	public static function logout(){
		die(json_encode(array('LOGOUT'=>'TRUE')));
	}
	
	public static function reload($extras = array(),$title = NULL,$message = NULL){
		self::go($_POST['MODULE_ID'],$_POST['PAGE_ID'],$extras,$title,$message);
	}
	
	public static function go($moduleID,$pageID,$extras = array(),$title = NULL, $message = NULL){
		$response = array();
		if(!is_null($title)){
			$response['TITLE'] = $title;
		}
		if(!is_null($message)){
			$response['MESSAGE'] = $message;
		}
		$response = array_merge($response,$extras);
		$response['MODULE_ID'] = $moduleID;
		$response['PAGE_ID']=$pageID;
		die(json_encode($response));
		
	}
	
	public static function ok($title = NULL,$message = NULL){
		$response = array();
		if(!is_null($title)){
			$response['TITLE'] = $title;
		}
		if(!is_null($message)){
			$response['MESSAGE'] = $message;
		}
		die(json_encode($response));
	}
	
	public static function err($title,$message){
		die(json_encode(array('TITLE'=>$title,'ERROR'=>$message)));
	}
	
	/**
		@depreciated
	*/
	public static function sendRawData($data){
		die(json_encode(array('RAW_DATA'=>$data)));
	}
			
	public static function data($data){
		self::sendRawData($data);
	}
	
	public static function toMoneyFormat($amount){
		//do a null check
		if($amount === NULL || !is_numeric($amount)){
			return $amount;
		}
		
		$originalAmount = $amount;
		$amount = abs($amount);
		
		$temp = "";
		if ($amount < 10) {
			$temp = "0.0{$amount}";
		} else if ($amount < 100) {
				$temp = "0.{$amount}";
			} else if ($amount < 100000) {
				$temp = "" + $amount;
				$cents = substr($temp, (strlen($temp) - 2));
				$rands = substr($temp, 0, (strlen($temp) - 2));
				$temp = "{$rands}.{$cents}";
			} else {
			$temp = $amount;
			$cents = substr($temp, (strlen($temp) - 2));
			$rands = substr($temp, 0, (strlen($temp) - 2));
			$ran = "";
			$j = 0;
			for ($i = strlen($rands) - 1; $i >= 0; $i--) {
				if ($j == 3) {
					$ran = substr($rands, $i, 1)." ".$ran;
					$j = 0;
				} else {
					$ran = $ran = substr($rands, $i, 1).$ran;
				}
				$j++;
			}
			$temp = "{$ran}.{$cents}";
		}
		if($originalAmount < 0){
			return "R -{$temp}";
		}
		return "R {$temp}";
	}
	
	/**
		Converts a currency string to cents as a integer
		
		@param $error - If true this method would call UI::err if the string cannot be converted, otherwise it just returns 0
	*/
	public static function fromMoneyFormat($amount, $error = false) {
		$amount = str_replace("R", "", $amount);
		$amount = str_replace(",", ".", $amount);
		$amount = str_replace(" ", "", $amount);
		
		if (!is_numeric($amount) || @floatval($amount) < 0) {
			// Something is up here
			if ($error) {
				static::err("Invalid Amount", "Please enter a valid amount for example: R 100.00");
			} else {
				return 0;
			}
		}
			
		return round(floatval($amount), 2)/0.01;
	}
	
	public static function select($name,$query,$db,$values = array(),$nameCol,$valueCol,$selected = '',$other = ''){
		
		$data = PSQL::query($query,$db,$values);
		
		if($data->rowCount() == 0){
			return false;
		}
		
		echo '<select '.$other.' name="'.$name.'">';
		
		//echo the actual ones
		while($row = $data->fetch()){
			echo '<option value="'.$row[$valueCol].'" '.($selected == $row[$valueCol]?'selected':'').'>'.$row[$nameCol].'</option>';
		}
		
		echo '</select>';
	}

}
?>