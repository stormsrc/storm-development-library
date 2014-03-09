<?php namespace storm;
/**
	@author Dylan Vorster
*/
class UI{	
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