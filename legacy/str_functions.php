<?php
/*

THIS FILE HAS VARIOUS COMMON USED STRING MANIPULATION FUNCTIONS.

*/
	function st_cv($st, $app){
		// Removes & and whitespace from a string. Useful for FILENAMES!
		// $st = String to convert.
		// $append = String to append to the end of the string. Useful for putting a file extension!
		$st = str_replace(" ","",$st);
		$st = str_replace("&","",$st);
		//$st = str_replace("!","",$st);
		//$st = str_replace("@","",$st);
		//$st = str_replace("#","",$st);
		//$st = str_replace("$","",$st);
		//$st = str_replace("%","",$st);
		//$st = str_replace("^","",$st);
		//$st = str_replace("*","",$st);
		//$st = str_replace("|","",$st);
		$ret = $st.$app;		
		return $ret; //Value to return.
	}

	function get_car_image($car_num,$aar_type){
		$g = "&nbsp;";
		if(strlen(@$arr_type) > 0 && file_exists("images/".substr($aar_type, 0, 1).".gif")){
			$g = "&nbsp;&nbsp;<img src=\"images/".substr($aar_type, 0, 1).".gif\" border=\"0\" title=\"".$aar_type."\" />";
		}
		if(strlen($car_num) > 0 && file_exists("car_images/".st_cv($car_num,".jpg","r"))){
			$g = "&nbsp;&nbsp;<img style=\"max-width: 100px\" src=\"car_images/".st_cv($car_num,".jpg","r")."\" border=\"0\" title=\"".$aar_type."\" />";
		}
		return $g;				
	}			

function dt_conv($dt){
	// take human readable date yyyy-mm-dd and returns unix timestmap
	// $dt = formated date
	$dt_arr = explode(" ",$dt);
	$dt_arr2 = explode("-",$dt_arr[0]);
	if(!isset($dt_arr2[0])){$dt_arr2[0] = 1970;}
	if(!isset($dt_arr2[1])){$dt_arr2[1] = 01;}
	if(!isset($dt_arr2[2])){$dt_arr2[2] = 01;}
	
	//return date('U',mktime(0,0,0,$dt_arr2[1],$dt_arr2[2],$dt_arr2[0]));
}

function homeDispType($n, $opt = 0){
	// $n = the number in the home_disp field in the ichange_rr table.
	// $opt = whether to render as a set of Option tags. 1 = yes.
	$t = array();
	$t['logged_out'] = "Public / Non-Member";
	$t[0] = "Standard";
	$t[1] = "Cars first, display Train assign";
	$t[2] = "Minimal";
	$t[9999] = "";
	$tky = array_keys($t);
	if($opt == 0){return @$t[$n];}
	$o = "";
	for($i=0;$i<count($tky);$i++){
		if(is_numeric($tky[$i])){$o .= "<option value=\"".$tky[$i]."\">".$t[$tky[$i]]."</option>";}
	}
	return $o;
}

// Date functions
function hr($hr=-1){
	// Returns select options for Hours, with $hr value selected
	if($hr < 0){$hr = date('H');}
	$opts = "<option value=\"".$hr."\">".$hr."</option>";
	for($i=0;$i<24;$i++){
		$ii = $i; if($ii < 10){$ii = "0".$ii;}
		$opts .= "<option value\"".$ii."\">".$ii."</option>";
	}
	return $opts;
}

function mins($mi=-1){
	// Returns select options for Minutes, with $mi value selected
	if($mi < 0){$mi = date('i');}
	$opts = "<option value=\"".$mi."\">".$mi."</option>";
	for($i=0;$i<60;$i++){
		$ii = $i; if($ii < 10){$ii = "0".$ii;}
		$opts .= "<option value\"".$ii."\">".$ii."</option>";
	}
	return $opts;
}

function convWBSql($sql_t = '',$arr=array()){
	// Converts [] tags in sql statement to valid sql syntax
	$sql_t = str_replace("[wb_whr]",$arr['wb_whr'],$sql_t);
	$sql_t = str_replace("[rr]",$arr['rr'],$sql_t);
	$sql_t = str_replace("[sort_wb]",$arr['sort_wb'],$sql_t);
	$sql_t = str_replace("[sort_ord_wb]",$arr['sort_ord_wb'],$sql_t);
	return $sql_t;
}
?>
