<?php
/*

THIS FILE HAS VARIOUS COMMON USED MYSQL QUERIES WHICH CAN BE INCLUDED AND USED TO RETURN
VALUES OF A QUERY ACCORDING TO THE FUNCTION'S OPTIONS.

*/

class query_functions {
	function qry($tbl, $data, $ky, $fld){
		// Suitable to return ONE field of the db table, where the field name and data to search for are provided.
		// $tbl = the table to search in.		
		// $data = the data string to search for.
		// $ky = the name of the field to search in.
		// $fld = Field name to return value of.
		// $ret = Returned value of the function.
		$sql_com = "SELECT * FROM `".$tbl."` WHERE `".$ky."` = '".$data."' LIMIT 1";
		$dosql_com = mysqli_query($this->sqli,$sql_com);
		$ret = "";
		while($resultcom = mysqli_fetch_array($dosql_com)){			
			$ret = $resultcom[$fld];		
		}
		
		return $ret; //Value to return.
	}
	
	function qry_complex($tbl, $data, $ky, $fld, $other){
		// Fields the same as for qry() above,
		// $other = any remaining clauses to include (eg, LIMIT and ORDER BY **NOT WHERE!**).
		$sql_com = "SELECT * FROM `".$tbl."` WHERE `".$ky."` = '".$data."' ".$other;
		$dosql_com = mysqli_query($this->sqli,$sql_com);
		$ret = "";
		while($resultcom = mysqli_fetch_array($dosql_com)){			
			$ret = $resultcom[$fld];				
		}
		return $ret;
	}
	
	function q_cntr($tbl, $where){
		// Simple Record Counter - Counts all the records in table $tbl, that match $where, and returns $ret.
		// $where is the WHERE portion of the query. eg. WHERE `id` = '27'. The `id` = '27' would be the string in $where.
		$sql_com = "SELECT `id` FROM `".$tbl."`";
		if(strlen($where) > 0){$sql_com = $sql_com." WHERE ".$where;}
		$dosql_com = mysqli_query($this->sqli,$sql_com);
		$ret = mysqli_num_rows($dosql_com); 
		return $ret;
	}

function rr_ichange_lst($curr_stat,$retArr=0,$opts=array()){
	// $curr_stat is the current status, before this list is rendered.
	// $retArr = 1 means return an array rather than option tags
	// $opts  = Array of options for query, with following keys: 
	//		where => where portion of sql statement
	//		orderby => order by portion of query
	// $max_ic = maximum number of interchange points per railroad.
	$max_ic = 25;
	$whr = ""; if(isset($opts['where'])){$whr = " WHERE ".$opts['where'];}
	$ord_by = " ORDER BY `report_mark`";
	if(isset($opts['ordby'])){$ord_by = " ORDER BY ".$opts['ordby'];}
	$sql = "SELECT * FROM `ichange_rr`".$whr.$ord_by;
	$qry = mysqli_query($this->sqli,$sql);
	if($retArr == 0){$lst = "";}else{$lst = array();}
	while($res = mysqli_fetch_array($qry)){
		$report_mark = $res['report_mark'];
		$interchanges = $res['interchanges'];
		$interchanges = str_replace("; ", ";", $interchanges);
		$wot = "";
		if($curr_stat == "RETURNING" || strpos($curr_stat,"ETURNING") > 0){$wot = "(RETURNING) ";}
		if(strlen($interchanges) > 0){
			$ic_lst = explode(";",$interchanges);
			if($retArr == 0){$lst .= "<option style=\"font-weight: bold; color: white; background-color: maroon;\" value=\"\">-- ".$report_mark." I/changes --</option>\n";}
			for($i=0;$i<$max_ic;$i++){
				if(isset($ic_lst[$i])){
					$ic_val = $wot."AT ".trim($ic_lst[$i]);
					if(strlen($ic_val) > 40){$ic_val = substr($ic_val, 0, 40);}
					if($retArr == 0){$lst .= "<option value=\"".$ic_val."\">At ".ucwords($ic_lst[$i])."</option>\n";}
					else{$lst[] = $ic_val;}
				}
			}
		}
	}
	return $lst;
}

function affil($rr=0){
	if($rr < 1){return "";}
	$af_sql = "SELECT `affiliates`,`id` FROM `ichange_rr` WHERE `id` = '".$rr."' LIMIT 1";
	$af_qry = mysqli_query($this->sqli,$af_sql);
	$af_res = mysqli_fetch_array($af_qry);
	$af_lst = explode(";",str_replace(" ","",$af_res['affiliates']));
	if(strlen($af_res['affiliates']) > 0){
		$af_ret = ""; //"<span style=\"font-size: 10pt;\">Affiliated RRs: </span>";
		$af_ret .= "<select style=\"font-size:8pt; margin: 2px; padding: 2px; width: 100px;\" onchange=\"window.location = 'index.php?rr_sess=' + this.value\">";
		$af_ret .= "<option value=\"0\" selected=\"selected\">Affiliated RRs </option>";
		for($i=0;$i<count($af_lst);$i++){
			$rr_id = $this->qry("ichange_rr", $af_lst[$i], "report_mark", "id");		
			//$af_ret .= "<span style=\"font-size: 10pt;\"><a href=\"index.php?rr_sess=".$rr_id."\">".$af_lst[$i]."</a> (".q_cntr("ichange_waybill", "`status` != 'CLOSED' AND (`rr_id_from` = '".$rr_id."' OR `rr_id_to` = '".$rr_id."' OR `routing` LIKE '%%".$af_lst[$i]."%%')").")&nbsp;";
			$af_ret .= "<option value=\"".$rr_id."\">".$af_lst[$i]." (".$this->q_cntr("ichange_waybill", "`status` != 'CLOSED' AND (`rr_id_from` = '".$rr_id."' OR `rr_id_to` = '".$rr_id."' OR `routing` LIKE '%%".$af_lst[$i]."%%')").")</option>";
		}
		$af_ret .= "</select>";
		return $af_ret;
	}else{
		return "";
	}
}

function rrOpts($c=""){
	// Generates a set of Options tags for Railroads, with $c as the selected value.
	$o = "";
	$s = "SELECT `id`,`report_mark` FROM `ichange_rr` WHERE `inactive` = 0 OR `common_flag` = 1 ORDER BY `report_mark`";
	$q = mysqli_query($this->sqli,$s);
	while($r = mysqli_fetch_array($q)){
		$sel = "";
		if($c == $r['id']){$sel = " selected=\"selected\" ";}
		$o .= "<option value=\"".$r['id']."\"".$sel.">".$r['report_mark']."</option>";
	}
	return $o;
}

function rrArray($rr=0,$rr_rep_mark = ""){
	// used to return all railroad for rr id in $rr variables in an associative array
	$s = "SELECT * FROM `ichange_rr` WHERE `id` = '".$rr."'";
	$q = mysqli_query($this->sqli,$s);
	$r = mysqli_fetch_array($q);
	return $r;
}

function rrFullArr($srt="rr_name"){
	// Returns a multi-dim array with railroad 'id' as the primary key
	$s = "SELECT `ichange_rr`.* FROM `ichange_rr` ORDER BY `ichange_rr`.`".$srt."`";
	//$s = "SELECT `ichange_rr`.*, COUNT(`ichange_waybill`.`id`) AS `wb_cntr` FROM `ichange_rr`,`ichange_waybill` ORDER BY `ichange_rr`.`".$srt."`";
	//$s = "SELECT `ichange_rr`.*, COUNT(`ichange_waybill`.`id`) AS `wb_cntr` FROM `ichange_rr` LEFT JOIN `ichange_waybill` ON `ichange_waybill`.`rr_id_handling` = `ichange_rr`.`id` WHERE `ichange_waybill`.`status` != 'CLOSED' ORDER BY `ichange_rr`.`".$srt."`";
	$q = mysqli_query($this->sqli,$s);
	$arr = array();
	while($r = mysqli_fetch_assoc($q)){
		$rtmp = $r['id'];
		$r['pw'] = "[PRIVATE]";
		$arr[$rtmp] = $r;
		$arr[$rtmp]['wb_cntr'] = $this->q_cntr("ichange_waybill", "`status` != 'CLOSED' AND (`rr_id_from` = '".$r['id']."' OR `rr_id_to` = '".$r['id']."' OR `routing` LIKE '%%".$r['report_mark']."%%')");
	}
	return $arr;	
}

function trainOpts($opts=array(),$trainsArr=array()){ //$rr=0,$auto="N",$trainsArr=array()){
	// Creates Option tag sets for trains - value=train_id (not id!), does NOT include AUTO trains
	// $opts['rr'] = railroad
	// $opts['auto'] = show auto trains? (Y/N)
	// $opts['onlyrr'] = if set, then show only trains for $opts['rr'].
	if(count($trainsArr) < 1){$trainsArr = $this->trainsArray();}
	$rr = 0; if(isset($opts['rr'])){$rr = $opts['rr'];}
	$auto = "N"; if(isset($opts['auto'])){$auto = $opts['auto'];}
	$onlyrr = 0; if(isset($opts['onlyrr'])){$onlyrr = 1;}
	$tOpts_rr = "";
	$tOpts_oth = "";
	if($rr < 1 && isset($_COOKIE['rr_sess'])){$rr = $_COOKIE['rr_sess'];}
	$s = array();
	$kys = @array_keys($trainsArr);
	for($i=0;$i<count($kys);$i++){
		$show = 1;
		$wps = @array_keys(json_decode($trainsArr[$kys[$i]]['auto'], true));
		if($auto != "Y" && (count($wps) > 0 || intval($trainsArr[$kys[$i]]['auto']) > 0)){$show = 0;}
		if($onlyrr == 1 && $trainsArr[$kys[$i]]['railroad_id'] != $rr && $trainsArr[$kys[$i]]['train_id'] != "NOT ALLOCATED"){$show = 0;}
		if($show > 0){
			$td = $trainsArr[$kys[$i]]['train_desc'];
			if(strlen($td) > 25){$td = substr($td,0,25);}
			$opt_styl = "";
			if(intval($trainsArr[$kys[$i]]['auto']) > 0 || count($wps) > 0){$opt_styl = " style=\"background-color: yellow\"";}
			if($rr == $trainsArr[$kys[$i]]['railroad_id']){$tOpts_rr .= "<option ".$opt_styl."value=\"".$trainsArr[$kys[$i]]['train_id']."\">".$td." (".$trainsArr[$kys[$i]]['train_id'].")</option>";}
			else{$tOpts_oth .= "<option ".$opt_styl."value=\"".$trainsArr[$kys[$i]]['train_id']."\">".$td." (".$trainsArr[$kys[$i]]['train_id'].")</option>";}
		}
	}
	$tOpts = "<option value=\"\" style=\"background-color: brown; color: white;\">-- Your trains --</option>".
		$tOpts_rr.
		"<option value=\"\" style=\"background-color: brown; color: white;\">-- Other trains --</option>".
		$tOpts_oth;
	return $tOpts;
}

function trainsArray($opts=array()){
	// Builds a multi-dim assoc array
	$ord_by = "id"; if(isset($opts['sort'])){$ord_by = $opts['sort'];}
	$s = "SELECT * FROM `ichange_trains` ORDER BY `".$ord_by."`";
	$q = mysqli_query($this->sqli,$s);
	$arr = array();
	while($r = mysqli_fetch_assoc($q)){
		//$arr[$r['id']] = $r;
		$arr[] = $r;
	}
	//echo "<pre>"; print_r($arr); echo "</pre>";
	return $arr;
}

function carArray($rr){
	// Creates multi-dim array of cars with car_num as the primary key for the railroad logged in as.
	/*
		$arr = array(
			'{car_num}' => array($r)
	*/
	$s = "SELECT * FROM `ichange_cars` WHERE `rr` = '".$rr."'";
	$q = mysqli_query($this->sqli,$s);
	$arr = array();
	while($r = mysqli_fetch_array($q)){
		$rtmp = $r['car_num'];
		$arr[$rtmp] = $r;
	}
	return $arr;
}

function carStatusUpd($carArr){
	// Updates location of cars. 
	$locn = $carArr['location']; // Single value
	$lading = $carArr['lading'];
	$cars = $carArr['cars']; // Array!
	$rr = $carArr['rr']; // railroad id's array!
	$rarr = join(',',$carArr['rr']); // ['rr'] is railroad id's array!
	for($i=0;$i<count($cars);$i++){
		if(strlen($cars[$i]) > 0){
			$sql = "UPDATE `ichange_cars` SET `location` = '".$locn."', `lading` = '".$lading."' WHERE `car_num` = '".$cars[$i]."' AND `rr` IN (".$rarr.")";
			mysqli_query($this->sqli,$sql);
		}
	}
}

function autoSav($arr){
	// Used to insert records into the ichange_auto table

	if(!isset($arr['exit_waypoint'])){$arr['exit_waypoint'] = "";}
	if(!isset($arr['train_id'])){$arr['train_id'] = "";}
	if(!isset($arr['description'])){$arr['description'] = "";}
	$arr['entry_waypoint'] = strtoupper($arr['entry_waypoint']);

	$trsql = "SELECT `train_desc`,`destination`, `origin`, `auto` FROM `ichange_trains` WHERE `train_id` = '".$arr['train_id']."'";
	//echo $trsql."";
	$qry = mysqli_query($this->sqli,$trsql);
	$res = mysqli_fetch_array($qry);
	$t_qry = $res['train_desc'];
	$date_now_t = date('Y-m-d');
	$date_now = strtotime($date_now_t);
	$res['destination'] = str_replace(", ",",",$res['destination']);
	$res['origin']; // added 2012-02-09
	//$retArr = array(); // Array to return to calling application code

	if(isset($arr['autotrain'])){
		$date_wb = strtotime($arr['waybill_date']);
		// Converting exit_waypoint to google maps friendly location
		$exit_waypoint = "";
		//if(isset($arr['exit_waypoint'])){$exit_waypoint = strtoupper($arr['exit_waypoint']);}
		$exit_waypoint = strtoupper($arr['exit_waypoint']);
		if(strlen($exit_waypoint) < 1){$exit_waypoint = $res['destination'];}
		$exit_waypoint = str_replace(";",",",$exit_waypoint);	
		$exit_waypoint = str_replace(", ",",",$exit_waypoint);
		$exit_waypoint = str_replace("  "," ",$exit_waypoint);
		$exit_waypoint = trim($exit_waypoint);
	
		// tests on auto field - is it numeric or a json array. If not json array, make it json!
		$autoArr = @json_decode($res['auto'], true);
		if(is_numeric($res['auto'])){$autoArr = array($exit_waypoint => $res['auto']);}
		elseif(!isset($autoArr[$exit_waypoint])){
			// Sets maxDays to highest value in waypoints array and reset array with just exit_waypoint = maxDays.
			$maxDays = 1;
			$mdKy = @array_keys($autoArr);
 			for($md=0;$md<count($mdKy);$md++){
 				if($autoArr[$mdKy[$md]] > $maxDays){$maxDays = $autoArr[$mdKy[$md]];} 
 			}
			$autoArr = array($exit_waypoint => $maxDays);
		}

		// Re-Setting autoArr from entry waypoint value to exit waypoint value. ADDED 2012-02-09 
		if(strlen($arr['entry_waypoint']) < 1){ $arr['entry_waypoint'] = $res['origin'];}
		//echo "BEFORE IN_ARRAY TEST: <pre>";print_r($autoArr); echo "</pre>";
		//if(in_array($arr['entry_waypoint'],$autoArr)){
		if(isset($autoArr[$arr['entry_waypoint']])){
			//echo "found entry_waypoint<br />";
		}else{ 
			//echo "not found entry waypoint<br />"; 
			$autoArr[$arr['entry_waypoint']] = 0;
		}

		asort($autoArr); // sorts array by values not keys
		$p = $autoArr[$arr['entry_waypoint']];
		//echo "AFTER: <pre>";print_r($autoArr); echo "</pre>";
		//echo "p = ".$p."<br />";
		//echo "arr[entry_waypoint] = ".$arr['entry_waypoint']."<br />";
				
		// Getting keys for json array and loop through results and add to ichange_auto table.
		$mdKys = @array_keys($autoArr);
		//for($u=0;$u<count($mdKys);$u++){
		mysqli_query($this->sqli,"DELETE FROM `ichange_auto` WHERE `waybill_num` = '".$arr['waybill_num']."'");
		//for($u=$p;$u<count($mdKys);$u++){
		for($u=0;$u<count($mdKys);$u++){
			$nxt_date_tmp = $date_now;
			if($date_wb > $date_now){$nxt_date_tmp = $date_wb;}
			if($autoArr[$mdKys[$u]] <= $autoArr[$exit_waypoint] && $autoArr[$mdKys[$u]] > $p){
				$nxt_date_tmp = $nxt_date_tmp + (60*60*24*($autoArr[$mdKys[$u]]-$p));
				$nxt_date = date('Y-m-d', $nxt_date_tmp);
				if($mdKys[$u] == $exit_waypoint){$arr['description'] = "SPOTTED";}
				$sql_cro = "INSERT INTO `ichange_auto` SET 
					`act_date` = '".$nxt_date."', 
					`waypoint` = '".$mdKys[$u]."', 
					`train_id` = '".$arr['train_id']."', 
					`waybill_num` = '".$arr['waybill_num']."', 
					`description` = '".$arr['description']."'";
				//echo $sql_cro."<hr />";
				mysqli_query($this->sqli,$sql_cro);
			}
		}

		$txt = "*AUTO GENERATED* - ALLOCATED TO CONSIST FOR TRAIN <strong>".$arr['train_id']." (".$t_qry.")</strong> @ ".$arr['entry_waypoint'].". CAR/S ON THIS WAYBILL ARE DESTINED FOR ".$exit_waypoint.".";
		$prog = $this->progWB($arr['waybill_num']);
		$prog[] = array(
			'date' => date('Y-m-d'), 
			'time' => date('H:i'), 
			'waybill_num' => $arr['waybill_num'],
			'text' => $txt, 
			'status' => "IN TRANSIT", 
			'train' => str_replace("NOT ALLOCATED","",$arr['train_id']), 
			'map_location' => $arr['entry_waypoint'], 
			'tzone' => $_SESSION['_tz']
		);
		//'exit_location' => strtoupper($autoSav['entry_waypoint'])
		$jprog = json_encode($prog);
		$sql_prog_auto = "UPDATE `ichange_waybill` SET `progress` = '".$jprog."' WHERE `waybill_num` = '".$arr['waybill_num']."'";
		mysqli_query($this->sqli,$sql_prog_auto);
	}
	if(isset($arr['unload'])){
		$sql_cro = "INSERT INTO `ichange_auto` SET 
			`act_date` = '".$arr['waybill_date']."', 
			`waypoint` = '', 
			`train_id` = 'NOT ALLOCATED', 
			`waybill_num` = '".$arr['waybill_num']."', 
			`description` = 'UNLOADED'";
		mysqli_query($this->sqli,$sql_cro);
	}
	//return $retArr; // return PHP array of Progress.
}

// JSON progress array functions
function progWB($id=0){
		$sql_p = "SELECT `progress` FROM `ichange_waybill` WHERE `waybill_num` = '".$id."'";
		$qry_p = mysqli_query($this->sqli,$sql_p);
		$res_p = mysqli_fetch_array($qry_p);
		$prog = json_decode($res_p['progress'], true);
		if(!is_array($prog)){$prog = array();}
		return $prog;
}

}

$qfunc = new query_functions;
$qfunc->sqli = $sqli;


?>
