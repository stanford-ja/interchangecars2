<?php

require_once("mysqli_class.php");

class Mricf {
// Class for holding all the functions used in MRICF
// Re-activate methods as needed.

function sqli_instance(){
	$sqli = new sqli;
	$sqli->select_db("jstan_general");
	return $sqli;
}

function __construct(){
	$this->CI =& get_instance(); // Global CodeIgniter object for use in methods.
}

// FUNCTIONS FROM functions.php

/*
// Start PHPLiveX related functions
*/

function showData($jsonArr){
	/*
	Used by PHPLiveX / Javascript functions - gets a single record and displays as HTML.
	$arr is a JSON array!
	$arr['val'] = value to search for
	$arr['key'] = field to search in
	$arr['tbl'] = data table for search
	$arr['fld'] = array of field names to return values for
	In JS: ---
		var i = document.form1.field1.value;
		var arr = '{"val":' + i + ',"key":"id","tbl":"ichange_indust","fld":["indust_name","desc","freight_in","freight_out"]}';
		showData(arr, {
			"preloader": "pr", 
			"onFinish": function(response){
				** ACTIONS TO COMPLETE **
			}
		});
	*/
	$arr = json_decode($jsonArr, true);
	include("db_connect7465.php");
	$val = $arr['val'];
	$key = $arr['key'];
	$tbl = $arr['tbl'];
	$fld = $arr['fld'];
	$sql = "SELECT * FROM `".$tbl."` WHERE `".$key."` = '".$val."'";
	$qu = mysql_query($sql);
	$res = mysql_fetch_array($qu);
	$ret = "";
	for($i=0;$i<count($fld);$i++){
		$ret .= ucwords(str_replace("_"," ",$fld[$i])).": ".$res[$fld[$i]]."<br />";
	}
	//$ret .= $res['indust_name']."<br />".$res['desc']."<br />".$res['freight_in']."<br />".$res['freight_out'];
	mysql_close();
	return $ret;
}

function getData($jsonArr){
	/*
	Used by PHPLiveX / Javascript functions to build a JSON array from data retreived
	$arr is a JSON array!
	$arr['val'] = value to search for
	$arr['key'] = field to search in
	$arr['tbl'] = data table for search
	$arr['fld'] = array of field names to return values for
	$arr['name'] = name of array to build
	In JS: ---
		var i = document.form1.field2.value;
		var arr = '{"val":' + i + ',"key":"rr","tbl":"ichange_indust","name":"industry","fld":["id","indust_name","desc","freight_in","freight_out"]}';
   	getData(arr, {
			"preloader": "pr",  
			"content_type": "json",  
			"onFinish": function(response){
				** ACTIONS TO COMPLETE **
			}  
		});  
	*/
	$arr = json_decode($jsonArr, true);
	include("db_connect7465.php");
	$val = $arr['val'];
	$key = $arr['key'];
	$tbl = $arr['tbl'];
	$fld = $arr['fld'];
	$name = $arr['name'];
	$sql = "SELECT * FROM `".$tbl."`";
	if(strlen($val) > 0 || $val > 0){$sql .= " WHERE `".$key."` = '".$val."'";}
	//echo $s; exit();
	$qu = mysql_query($sql);
	$arr = array();
	//$arr[$name]['train_id'] = $sql;
	while($res = mysql_fetch_array($qu)){
		$fldVals = array();
		for($i=0;$i<count($fld);$i++){$fldVals[$fld[$i]] = $res[$fld[$i]];}
		//$arr[$name] = $fldVals;
		$arr[$name][] = $fldVals;
	}
	mysql_close();
	return $arr;
}  
// End PHPLiveX related functions

/*
function isInHdeArr($id){
	// Checks whether a waybill is in the Hde cookie.
	// Returns 0 if in array, 1 if not
	// $wb = waybill #.
	$c_chk = array();
	
	if(isset($_COOKIE['hde'])){
		$c_chk = $_COOKIE['hde'];
		$c_chk = explode(",",$c_chk);
	}
	if(in_array($id,$c_chk)){ return 1;}
	return 2;
}
*/

// FUNCTIONS FROM arr_functions.php
function rrArrAllocToOnly($rrArr,$routing){
	// Requires $rrArr, $routing to be passed otherwise returns array of ALL show_allocto_only RRs.
	$arr = array();
	$rrArrKys = array_keys($rrArr);
	for($k=0;$k<count($rrArrKys); $k++){
		$incl = 0;
		if($rrArr[$rrArrKys[$k]]['show_allocto_only'] == 1 && (strpos("a".$routing,$rrArr[$rrArrKys[$k]]['report_mark']) > 0 || strlen($routing) < 1)){$incl++;}
		if($incl > 0){
			$arr[] = $rrArr[$rrArrKys[$k]]['report_mark'];
		}
	}
	return $arr;
}

/*
function socialLinks($str,$delim){
	// $str = string to create links from
	// $delim = Delimiter (separater)
	$str = str_replace("\n","",$str);
	$str = str_replace(" ","",$str);
	$e = explode($delim,$str);
	$lnks = "";
	for($i=0;$i<count($e);$i++){
		$label = "Social".$i;
		if(strlen($e[$i]) > 0){
			if(strpos("a".$e[$i],"facebook") > 0){$label = "Facebook";}
			if(strpos("a".$e[$i],"twitter") > 0){$label = "Twitter";}
			if(strpos("a".$e[$i],"google") > 0){$label = "Google";}
			if(strpos("a".$e[$i],"yahoo") > 0){$label = "Yahoo";}
			if(strpos("a".$e[$i],"youtube") > 0){$label = "YouTube";}
			$lnks .= "&nbsp;<a href=\"".$e[$i]."\" target=\"soc".$i."\">".$label."</a>";
		}
	}
	return $lnks;
}
*/

// FUNCTIONS FROM query_functions.php
function qry($tbl, $data, $ky, $fld){
	// Suitable to return ONE field of the db table, where the field name and data to search for are provided.
	// $tbl = the table to search in.		
	// $data = the data string to search for.
	// $ky = the name of the field to search in.
	// $fld = Field name to return value of.
	// $ret = Returned value of the function.
	$sql_com = "SELECT * FROM `".$tbl."` WHERE `".$ky."` = '".$data."' LIMIT 1";
	/*
	$dosql_com = mysql_query($sql_com);
	$ret = "";
	while($resultcom = mysql_fetch_array($dosql_com)){			
		$ret = $resultcom[$fld];		
	}
	*/
	$sqli = $this->sqli_instance();
	$q = $sqli->query($sql_com);
	$ret = "";
	while($resultcom = $q->fetch_array()){			
		$ret = $resultcom[$fld];	
	}
		
	return $ret; //Value to return.
}

function q_cntr($tbl, $where){
	// Simple Record Counter - Counts all the records in table $tbl, that match $where, and returns $ret.
	// $where is the WHERE portion of the query. eg. WHERE `id` = '27'. The `id` = '27' would be the string in $where.
	$sql_com = "SELECT `id` FROM `".$tbl."`";
	if(strlen($where) > 0){$sql_com .= " WHERE ".$where;}
	$sqli = $this->sqli_instance();
	$q = $sqli->query($sql_com);
	$ret = $sqli->num_rows($q);
	/*
	$dosql_com = mysql_query($sql_com);
	$ret = mysql_num_rows($dosql_com);
	*/ 
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
	$sqli = $this->sqli_instance();
	$qry = $sqli->query($sql);
	//$qry = mysql_query($sql);
	if($retArr == 0){$lst = "";}else{$lst = array();}
	//while($res = mysql_fetch_array($qry)){
	while($res = $qry->fetch_array()){
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

/*
function affil($rr=0){
	if($rr < 1){return array();}
	$af_sql = "SELECT `affiliates`,`id` FROM `ichange_rr` WHERE `id` = '".$rr."' LIMIT 1";
	$sqli = $this->sqli_instance();
	$af_qry = $sqli->query($sql);
	$af_res = $qry->fetch_array();
	if(strlen($af_res['affiliates']) > 0){
		$af_ret = ""; //"<span style=\"font-size: 10pt;\">Affiliated RRs: </span>";
		$af_ret .= "<select style=\"font-size:8pt; margin: 2px; padding: 2px; width: 100px;\" onchange=\"window.location = 'index.php?rr_sess=' + this.value\">";
		$af_ret .= "<option value=\"0\" selected=\"selected\">Affiliated RRs </option>";
		for($i=0;$i<count($af_lst);$i++){
			$rr_id = qry("ichange_rr", $af_lst[$i], "report_mark", "id");		
			//$af_ret .= "<span style=\"font-size: 10pt;\"><a href=\"index.php?rr_sess=".$rr_id."\">".$af_lst[$i]."</a> (".q_cntr("ichange_waybill", "`status` != 'CLOSED' AND (`rr_id_from` = '".$rr_id."' OR `rr_id_to` = '".$rr_id."' OR `routing` LIKE '%%".$af_lst[$i]."%%')").")&nbsp;";
			$af_ret .= "<option value=\"".$rr_id."\">".$af_lst[$i]." (".q_cntr("ichange_waybill", "`status` != 'CLOSED' AND (`rr_id_from` = '".$rr_id."' OR `rr_id_to` = '".$rr_id."' OR `routing` LIKE '%%".$af_lst[$i]."%%')").")</option>";
		}
		$af_ret .= "</select>";
		return $af_ret;
		//$afil_arr = explode(";",$af_res['affiliates']);
		//return $afil_arr;
		return $af_ret;
	}else{
		return array(); //"";
	}
}
*/

function affil_ids($rr=0,$rr_arr){
	if(isset($this->my_rr_ids)){$o = $this->my_rr_ids;}else{
		$o = array($rr);
  		if(@$rr_arr[$rr]->show_affil_wb == 1){
  			$myRRs_kys = explode(";", $rr_arr[$rr]->affiliates);
   			for($i=1;$i<count($rr_arr);$i++){
   				if(in_array(@$rr_arr[$i]->report_mark,$myRRs_kys)){
	    			//$this->whr .= " OR (".$this->allHomeWBs($i,@$this->arr['allRR'][$i]->report_mark).")";
   	 			$o[] = $i;
    			}
	  		}
  		}
  	}
  	return $o;
}

function rrOpts(){
	// Generates a set of Options tags for Railroads, no selected value!
	$o = "";
	if(isset($this->railroad_select_options)){$o = $this->railroad_select_options;}else{
		$s = "SELECT `id`,`report_mark` FROM `ichange_rr` WHERE `inactive` = 0 OR `common_flag` = 1 ORDER BY `report_mark`";
		$q = mysql_query($s);
		while($r = mysql_fetch_array($q)){
			$sel = "";
			//if($c == $r['id']){$sel = " selected=\"selected\" ";}
			$o .= "<option value=\"".$r['id']."\"".$sel.">".$r['report_mark']."</option>";
		}
		$this->railroad_select_options = $o;
	}
	return $o;
}

/*
function rrArray($rr=0,$rr_rep_mark = ""){
	// used to return all railroad for rr id in $rr variables in an associative array
	$s = "SELECT * FROM `ichange_rr` WHERE `id` = '".$rr."'";
	$q = mysql_query($s);
	$r = mysql_fetch_array($q);
	return $r;
}
*/

function rrFullArr(){
	if(isset($this->railroads)){return $this->railroads;}else{
		$this->railroads['allRR'] = array();
		$this->railroads['allRRKys'] = array();
		$this->railroads['allRRRepMark'] = array();

		$arRR = (array)$this->CI->Railroad_model->get_all();
		for($i=0;$i<count($arRR);$i++){
			$this->railroads['allRR'][$arRR[$i]->id] = $arRR[$i]; // Used to get data for specific RR , id field is key for array.
			$this->railroads['allRRKys'][] = $arRR[$i]->id; // Used to order by Report Mark.
			$this->railroads['allRRRepMark'][$arRR[$i]->report_mark] = $arRR[$i]->id;
		}
	}
	return $this->railroads;
}

function trainOpts($opts=array(),$trainsArr=array()){ //$rr=0,$auto="N",$trainsArr=array()){
	// Creates Option tag sets for trains - value=train_id (not id!), does NOT include AUTO trains
	// $opts['rr'] = railroad
	// $opts['auto'] = show auto trains? (Y/N)
	// $opts['onlyrr'] = if set, then show only trains for $opts['rr'].]
	if(isset($this->train_select_options)){$tOpts = $this->train_select_options;}else{
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
		$this->train_select_options = $tOpts;
	}
	return $tOpts;
}

function trainsArray($opts=array()){
	// Builds a multi-dim assoc array
	$ord_by = "id"; if(isset($opts['sort'])){$ord_by = $opts['sort'];}
	$s = "SELECT * FROM `ichange_trains` ORDER BY `".$ord_by."`";
	$sqli = $this->sqli_instance();
	$q = $sqli->query($s);
	$ret = "";
	$arr = array();
	while($r = $q->fetch_assoc()){
	//$q = mysql_query($s);
	//while($r = mysql_fetch_assoc($q)){
		//$arr[$r['id']] = $r;
		$arr[] = $r;
	}
	//echo "<pre>"; print_r($arr); echo "</pre>";
	return $arr;
}

function carStatusUpd($carArr){
	// Updates location of cars. 
	$sqli = $this->sqli_instance();
	$locn = $carArr['location']; // Single value
	$lading = $carArr['lading'];
	$cars = $carArr['cars']; // Array!
	$rr = $carArr['rr']; // railroad id's array!
	$rarr = join(',',$carArr['rr']); // ['rr'] is railroad id's array!
	for($i=0;$i<count($cars);$i++){
		if(strlen($cars[$i]) > 0){
			$sql = "UPDATE `ichange_cars` SET `location` = '".$locn."', `lading` = '".$lading."' WHERE `car_num` = '".$cars[$i]."' AND `rr` IN (".$rarr.")";
			//mysql_query($sql);
			$sqli->query($sql);
		}
	}
}

function autoSav($arr){
	// Used to insert records into the ichange_auto table

	$sqli = $this->sqli_instance();
	if(!isset($arr['exit_waypoint'])){$arr['exit_waypoint'] = "";}
	if(!isset($arr['train_id'])){$arr['train_id'] = "";}
	if(!isset($arr['description'])){$arr['description'] = "";}
	$arr['entry_waypoint'] = ""; if(isset($arr['entry_waypoint'])){strtoupper($arr['entry_waypoint']);}

	$trsql = "SELECT `train_desc`,`destination`, `origin`, `auto` FROM `ichange_trains` WHERE `train_id` = '".$arr['train_id']."'";
	//echo $trsql."";
	//$qry = mysql_query($trsql);
	$qry = $sqli->query($trsql);
	//$res = mysql_fetch_array($qry);
	$res = $qry->fetch_array();
	$t_qry = $res['train_desc'];
	$date_now_t = date('Y-m-d');
	$date_now = strtotime($date_now_t);
	$res['destination'] = str_replace(", ",",",@$res['destination']);
	$res['origin'] = str_replace(", ",",",@$res['origin']); // added 2012-02-09
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
				
		// Getting keys for json array and loop through results and add to ichange_auto table.
		$mdKys = @array_keys($autoArr);
		//for($u=0;$u<count($mdKys);$u++){
		//mysql_query("DELETE FROM `ichange_auto` WHERE `waybill_num` = '".$arr['waybill_num']."'");
		$sqli->query("DELETE FROM `ichange_auto` WHERE `waybill_num` = '".$arr['waybill_num']."'");
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
				//mysql_query($sql_cro);
				$sqli->query($sql_cro);
			}
		}

		$txt = "*AUTO GENERATED* - ALLOCATED TO CONSIST FOR TRAIN <strong>".$arr['train_id']." (".$t_qry.")</strong> @ ".$arr['entry_waypoint'].". CAR/S ON THIS WAYBILL WILL BE SPOTTED AT ".$exit_waypoint.".";
		$wb_id = $this->qry("ichange_waybill", $arr['waybill_num'], "waybill_num", "id");
		$prog = $this->progWB($wb_id);
		$prog[] = array(
			'date' => date('Y-m-d'), 
			'time' => date('H:i'), 
			'waybill_num' => $arr['waybill_num'],
			'text' => $txt, 
			'status' => "IN TRANSIT", 
			'train' => str_replace("NOT ALLOCATED","",$arr['train_id']), 
			'map_location' => $arr['entry_waypoint'], 
			'tzone' => @$_SESSION['_tz']
		);
		//'exit_location' => strtoupper($autoSav['entry_waypoint'])
		$jprog = json_encode($prog);
		$sql_prog_auto = "UPDATE `ichange_waybill` SET `progress` = '".$jprog."' WHERE `waybill_num` = '".$arr['waybill_num']."'";
		//mysql_query($sql_prog_auto);
		$sqli->query($sql_prog_auto);
	}
	if(isset($arr['unload'])){
		$sql_cro = "INSERT INTO `ichange_auto` SET 
			`act_date` = '".$arr['waybill_date']."', 
			`waypoint` = '', 
			`train_id` = 'NOT ALLOCATED', 
			`waybill_num` = '".$arr['waybill_num']."', 
			`description` = 'UNLOADED'";
		//mysql_query($sql_cro);
		$sqli->query($sql_cro);
	}
	//return $retArr; // return PHP array of Progress.
}



// FUNCTIONS FROM str_functions.php
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

function get_car_image($car_num,$aar_type=""){
	$g = "&nbsp;";
	if(strlen($aar_type) > 0 && file_exists("images/".substr($aar_type, 0, 1).".gif")){
		$g = "&nbsp;&nbsp;<img src=\"../images/".substr($aar_type, 0, 1).".gif\" border=\"0\" title=\"".$aar_type."\" />";
	}
	if(strlen($car_num) > 0 && file_exists("car_images/".$this->st_cv($car_num,".jpg","r"))){
		$g = "&nbsp;&nbsp;<img style=\"max-width: 100px\" src=\"../car_images/".$this->st_cv($car_num,".jpg","r")."\" border=\"0\" title=\"".$aar_type."\" />";
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

function homeDispType($n=0, $opt = 0){
	// $n = the number in the home_disp field in the ichange_rr table.
	// $opt = whether to render as a set of Option tags. 1 = yes.
	$t = array();
	$t['logged_out'] = "Public / Non-Member";
	$t[0] = "Standard";
	$t[1] = "Cars first, display Train assign";
	$t[2] = "Minimal";
	$t[9999] = "";
	$tky = array_keys($t);
	//if($opt == 0){return @$t[$n];}
	$o = "";
	for($i=0;$i<count($tky);$i++){
		if(is_numeric($tky[$i])){$o .= "<option value=\"".$tky[$i]."\">".$t[$tky[$i]]."</option>";}
	}
	//return $o;
	return $t;
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

function status_dropdowns(){
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
	return $status_dd_arr;
}

function progWB($id=0){
		$sql_p = "SELECT `progress` FROM `ichange_waybill` WHERE `id` = '".$id."'";
		$sqli = $this->sqli_instance();
		$q = $sqli->query($sql_p);
		//$res_p = mysql_fetch_array($qry_p);
		$res_p = $q->fetch_array();	
		$prog = json_decode($res_p['progress'], true);
		if(!is_array($prog)){$prog = array();}
		return $prog;
}

function strip_spec($orig_str){
	// strips excape and control characters from a string.
   for($c=0;$c<32;$c++){$orig_str = str_replace(chr($c),"", $orig_str);}
   for($c=127;$c<256;$c++){$orig_str = str_replace(chr($c),"", $orig_str);}
   //$orig_str = str_replace("\r\n","",$orig_str);
   return $orig_str;
}

function aarOpts($ord_by="aar_code"){
	// Generates AAR Options for a select field
	$s = "SELECT * FROM `ichange_aar` ORDER BY `".$ord_by."`";
	$sqli = $this->sqli_instance();
	$q = $sqli->query($s);
	$ret = "";
	//$arr = array();
	//$curr_typ = "-";
	while($r = $q->fetch_assoc()){
		//if(substr($r['aar_code'],0,1) != $curr_typ){$ret .= "<option value=\"".$r['aar_code']."\">Type ".substr($r['aar_code'],0,1)."</option>";}
		$ret .= "<option value=\"".$r['aar_code']."\">[".$r['aar_code']."] ".substr($r['desc'],0,25)."</option>";
		//$curr_type = substr($r['aar_code'],0,1);
	}
	return $ret;
	
}


}
?>