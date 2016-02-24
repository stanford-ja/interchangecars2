<?php
@session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
//$tz = "America/Chicago";
//if(!isset($_SESSION['_tz']))$_SESSION['_tz'] = $tz;
//date_default_timezone_set($_SESSION['_tz']);

	$load_st = date('U');

	$pgTitle = "MRICF - Model Rail Interchanged Cars Facility";
	$row_bg1 = "white";
	$row_bg2 = "#cccccc";	
	
	$status_dd_arr = array(
		'P_ORDER' => "Purchase Order",
		'WAYBILL' => "Waybill Created",
		'CAR-ALLOC' => "Car Allocated",
		'FORWARD EMPTY' => "Forwarding Empty to Origin",
		'LOADING' => "Loading @ Origin",
		'IN TRANSIT' => "In Transit",
		'AT I-CHANGE' => "Spotted @ Interchange",
		'UNLOADING' => "Unloading @ Destination",
		'UNLOADED' => "Unloaded @ Destination",
		'RETURNING' => "Returning to Origin RR",
		'CLOSED' => "Closed"
	);

	$status_dropdown = "";
	$st_kys = array_keys($status_dd_arr);
	for($stk=0;$stk<count($st_kys);$stk++){
		$status_dropdown .= "<option value=\"".$st_kys[$stk]."\">".$status_dd_arr[$st_kys[$stk]]."</option>";
	}

	/*
	$status_dropdown = "<option value=\"P_ORDER\">Purchase Order</option>
						<option value=\"WAYBILL\">Waybill Created</option>
						<option value=\"CAR-ALLOC\">Car Allocated</option>
						<option value=\"FORWARD EMPTY\">Forwarding Empty to Origin</option>
						<option value=\"LOADING\">Loading at Origin</option>
						<option value=\"IN TRANSIT\">In Transit</option>
						<option value=\"AT I-CHANGE\">Spotted at Interchange</option>
						<option value=\"UNLOADING\">Unloading at Destination</option>
						<option value=\"UNLOADED\">Unloaded at Destination</option>
						<option value=\"RETURNING\">Returning to Owner RR</option>
						<option value=\"CLOSED\">Closed</option>";
	*/

	$tzone_opts = "";
	$tz_arr = array(
			"America/Chicago" , "America/Los_Angeles", "America/New_York", "America/Louisville", "America/Denver", "Australia/NSW", 
			"Europe/London", "Europe/Zurich", "Asia/Hong_Kong"
		);
	for($i=0;$i<count($tz_arr);$i++){
		$tzone_opts .= "<option value=\"".$tz_arr[$i]."\">".$tz_arr[$i]."</option>";
	}
		

?>