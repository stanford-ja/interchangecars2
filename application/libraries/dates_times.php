<?php
class Dates_times {

	var $tzXLocation = array();
	var $tzOptions = "";
	
	function __construct(){
		$this->setTZArr();
	}

	//function get_timezone_offset($remote_tz1, $origin_tz2='Australia/Sydney') {
	function get_timezone_offset($tz1, $tz2='') {
		// returns timezone offset between two timezones
		// tz2 is the server timezone if the comparison is being made between a server timezone and remote timezone
		/*
   	$origin_dtz2 = new DateTimeZone($origin_tz2);
	   $remote_dtz1 = new DateTimeZone($remote_tz1);
   	$origin_dt2 = new DateTime("now", $origin_dtz2);
	   $remote_dt1 = new DateTime("now", $remote_dtz1);
   	$offset = $origin_dtz2->getOffset($origin_dt2) - $remote_dtz1->getOffset($remote_dt1);
   	*/
   	if(strlen($tz2) < 1){$tz2 = $this->getServerTZ();}
   	$dtz2 = new DateTimeZone($tz2);
	   $dtz1 = new DateTimeZone($tz1);
   	$dt2 = new DateTime("now", $dtz2);
	   $dt1 = new DateTime("now", $dtz1);
   	$offset = $dtz2->getOffset($dt2) - $dtz1->getOffset($dt1);
	   return $offset;
	}
	
	function getUnixDate4TZ($loc,$loc2=''){
		// Adjusts date according to timezone
		// $loc is a location that matches the keys in $this->tzXLocation array
		if(strlen($loc2) < 1){$loc2 = $this->getServerTZ();}
		$tz = $this->tzXLocation[$loc];
		$tz2 = $this->tzXLocation[$loc2];
		echo $tz2;
		$tz_diff = $this->get_timezone_offset($tz,$tz2);
		$dt = date('U') - $tz_diff;
		return $dt;
	}
	
	function humanDate($u,$f='YmdHi'){
		$d = date($f,$u);
		return $d;
	}
	
	function setTZArr(){
		$this->tzXLocation = array();
		$timezone_identifiers = DateTimeZone::listIdentifiers();
		for ($i=0; $i < count($timezone_identifiers); $i++) {
			$this->tzXLocation[$timezone_identifiers[$i]] = $timezone_identifiers[$i];
			$sel = ""; if($timezone_identifiers[$i] == @$_COOKIE['_tz']){$sel = " selected=\"selected\"";}
			$this->tzOptions .= "<option".$sel." value=\"".$timezone_identifiers[$i]."\">".$timezone_identifiers[$i]."</option>\n";
		}
	}
	
	function getTZArr(){
		return $this->tzXLocation;
	}

	function getTZOptions(){
		return $this->tzOptions;
	}
	
	function getServerTZ(){
		return date('e');
	}
	
}
?>