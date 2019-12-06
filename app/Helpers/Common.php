<?php
namespace App\Helpers;

class Common {
    public static function nullIfEmpty($variable){
        return $variable ? $variable : null;
    }

    public static function isDateValid($date) {
    	$arr = explode('-', $date);

		if(count($arr) != 3) {
			return false;
		}

		if(!is_numeric($arr[0]) || !is_numeric($arr[1]) || !is_numeric($arr[2])) {
			return false;
		}

		$year = +$arr[0];
		$month = +$arr[1];
		$day = +$arr[2];

		return checkdate($month, $day, $year);
    }

    public static function addMineParam($params = null) {
    	$finalParams = [];

    	if($params !== null) {
    		if(is_array($params)) {
    			$finalParams = $params;
    		}
    		else {
    			$finalParams[] = $params;
    		}
    	}

    	if(request()->mine) {
    		$finalParams['mine'] = request()->mine;
    	}

    	return $finalParams;
    }
}