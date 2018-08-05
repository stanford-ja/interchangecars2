<?php
// Ajax function caller
// syntax: ajax.php?f=[function]&d=[data to send]
// Functions need to be added to the function caller below to be available to JQuery!

// Start function caller
if(isset($_GET['f'])){
	$get_kys = array_keys($_GET);
	//for($g=0;$g<count($get_kys);$g++){$_GET[$get_kys[$g]] = str_replace("&","[AMP]",$_GET[$get_kys[$g]]);}
	//print_r($_GET);
	if($_GET['f'] == "carUsed"){carUsed(@$_GET['d'],@$_GET['r']);}
	if($_GET['f'] == "selTrain"){selTrain(@$_GET['d'],@$_GET['la'],@$_GET['rr']);}//,@$_GET['r']);}
	if($_GET['f'] == "selRoute"){selRoute(@$_GET['d'],@$_GET['s'],@$_GET['e'],@$_GET['g']);}//,@$_GET['r']);}
	if($_GET['f'] == "carsAutoFind"){carsAutoFind(@$_GET['a'],@$_GET['b']);}
	if($_GET['f'] == "industAutoComp"){industAutoComp(@$_GET['a'],@$_GET['b'],@$_GET['c'],@$_GET['d'],@$_GET['e']);}
	if($_GET['f'] == "trainAutoComp"){trainAutoComp(@$_GET['a'],@$_GET['c'],@$_GET['d'],$_GET['la']);}
	if($_GET['f'] == "autoComp"){autoComp(@$_GET['a'],@$_GET['b'],@$_GET['c'],@$_GET['d']);}
	if($_GET['f'] == "allocTrain"){allocTrain(@$_GET['w'],@$_GET['t']);}
	if($_GET['f'] == "allocRR"){allocRR(@$_GET['w'],@$_GET['t']);}
	if($_GET['f'] == "swOrd"){swOrd(@$_GET['w'],@$_GET['t']);}
	if($_GET['f'] == "mapDetails"){mapDetails(@$_GET['s']);}
	if($_GET['f'] == "mapWBDetails"){mapWBDetails(@$_GET['w']);}
	if($_GET['f'] == "glCreate"){glCreate(@$_GET['i']);}
	if($_GET['f'] == "glDel"){glDel(@$_GET['i']);}
	if($_GET['f'] == "add2SW"){add2SW(@$_GET['s']);}
}
// End function caller

// Ajax accessible functions
function carUsed($cn,$rr){
	db_conn();
	$cn = charConv($cn,"[AMP]","&"); // Require to convert [AMP] back to '&'
	$q = mysql_query("SELECT `waybill_num` FROM `ichange_waybill` WHERE `cars` LIKE '%\"".$cn."\"%' LIMIT 1");
	$w = "";
	while($r = mysql_fetch_array($q)){$w = $r['waybill_num'];}
	echo $w;
}

function selTrain($fld14,$la='',$rr=0){
	// Display Train Selected function called by AJAX.
	db_conn();
	$fld14 = charConv($fld14,"[AMP]","&"); // Require to convert [AMP] back to '&'
	if(strlen($la) < 1){ $la = date('Y-m-d'); }
	$la_arr = explode("-",$la);
	$la_ts = intval(mktime (12, 0, 0, $la_arr[1], $la_arr[2], $la_arr[0]) + 86400);
	//echo $fld14;
	$sql = "SELECT * FROM `ichange_trains` WHERE `train_id` = '".$fld14."' LIMIT 1";
	$qry = mysql_query($sql);
	$lst = "";
	while($res = mysql_fetch_array($qry)){
		$lst .= $res['train_desc']."<br />";
		if(strlen($res['origin'].$res['destination']) > 0){$lst .= $res['origin']." to ".$res['destination']."<br />";}
		$lst .= $res['op_notes']."<br />";
		$lst .= "<hr/>";//Valid AUTO Train Waypoints (click to add a location to the Entry or Exit Location fields):<br />";
		$wps = json_decode($res['auto'], true);
		$wps_kys = @array_keys($wps);
		$opts = "<option value=\"".$res['origin']."\">".$res['origin']." (origin)</option>";
		for($o=0;$o<count($wps_kys);$o++){
			$opts .= "<option value=\"".$wps_kys[$o]."\">".$wps_kys[$o]." (".$wps[$wps_kys[$o]]." days)</option>";
		}
		//if(intval($res['auto']) > 0 || strlen($res['auto']) > 4){
		if(strlen($res['auto']) > 4){
			$lst .= "<div id=\"tr_valid8\" style=\"font-size: 12pt; font-weight: bold; color: maroon;\">Make sure you select an Entry and Exit Waypoint or the Auto Train routing will not be correct!</div>";
			$lst .= "<div style=\"display: inline-block; padding: 3px; white-space: nowrap;\">Entry Waypoint: <select id=\"entry_waypoint\" name=\"entry_waypoint\" onchange=\"route_valid8();\"><option value=\"\" selected=\"selected\">Select Entry</option>".$opts."</select></div>";
			$lst .= "<div style=\"display: inline-block; padding: 3px; white-space: nowrap;\">Exit Waypoint: <select id=\"exit_waypoint\" name=\"exit_waypoint\" onchange=\"route_valid8();\"><option value=\"\" selected=\"selected\">Select Exit</option>".$opts."</select></div>";
		}elseif(intval($res['auto']) > 0){
			$lst .= "<div id=\"tr_valid8\" style=\"font-size: 12pt; font-weight: bold; color: maroon;\">Enter the Entry and Exit Waypoint or the Auto Train routing will not be correct!</div>";
			$lst .= "<div style=\"display: inline-block; padding: 3px; white-space: nowrap;\">Entry Waypoint: <input type=\"text\" id=\"entry_waypoint\" name=\"entry_waypoint\" value=\"\" onchange=\"this.value = this.value.toUpperCase();route_valid8();\" /></div>";
			$lst .= "<div style=\"display: inline-block; padding: 3px; white-space: nowrap;\">Exit Waypoint: <input type=\"text\" id=\"exit_waypoint\" name=\"exit_waypoint\" value=\"\" onchange=\"this.value = this.value.toUpperCase(); if(document.form1.pfld6){ document.form1.pfld6.value = this.value; } route_valid8();\" /></div>";
		}else{
			$lst .= "<input type=\"hidden\" id=\"entry_waypoint\" name=\"entry_waypoint\" value=\"\" />";
			$lst .= "Exit Waypoint: <input type=\"text\" id=\"exit_waypoint\" name=\"exit_waypoint\" value=\"\" onchange=\"this.value = this.value.toUpperCase(); if(document.form1.pfld6){ document.form1.pfld6.value = this.value; }\" />";
		}
		if(intval($res['auto']) > 0 || strlen($res['auto']) > 4){
			$mx_dys = 10; $dy_opts = "";
			$dy_opts = dateRebuildReturnCommon($fld14,$rr,$la_ts,1);
			/*
			for($md=0;$md<$mx_dys;$md++){
				$dt_unix = intval($la_ts+($md*86400)); //intval(date('U')+($md*86400));
				$dy_opts .= "<option value=\"".intval($md+1)."\">".date('Y-m-d',$dt_unix)."</option>";
			}
			*/
			$lst .= "<div style=\"display: inline-block; padding: 3px; white-space: nowrap;\">Start Move On: <select name=\"auto_start_dt\" id=\"auto_start_dt\" onchange=\"route_valid8();\">".$dy_opts."</select></div>"; 
			$lst .= "<div style=\"display: inline-block; padding: 3px; white-space: nowrap;\">Add extra Auto Trains on Save: <select name=\"addXtraAutos\"><option value=\"0\">No</option><option value=\"1\">Yes</option></select></div>";
			if($rr > 0){
				$sql_rr = "SELECT `id`,`report_mark` FROM `ichange_rr` WHERE `inactive` = 0 ORDER BY `report_mark`";
				$qry_rr = mysql_query($sql_rr);
				$rr_opts = "";
				while($res_rr = mysql_fetch_array($qry_rr)){
					$sel_rr = ""; if($res_rr['id'] == $rr){ $sel_rr = " selected=\"selected\""; }
					$rr_opts .= "<option value=\"".$res_rr['id']."\"".$sel_rr.">".$res_rr['report_mark']."</option>";
				}
				$lst .= "<div style=\"display: inline-block; padding: 3px; white-space: nowrap;\">Allocate to RR: <select name=\"setRRAutos\">".$rr_opts."</select></div>";
			}	
			$lst .= "&nbsp;<input type=\"button\" name=\"calc_route\" value=\"Calc Route\" onclick=\"selRoute();\" />";
		}
		//$lst .= "<a href=\"javascript:{}\" onclick=\"document.getElementById('exit_waypoint').value = '".$res['destination']."'\">".$res['destination']."</a>, ";
	}
	if(strlen($lst) < 1){
		$lst = "No exact Train ID matching ".$fld14." found!";
	}else{
		$lst .= "<br /><a href=\"javascript:{}\" onClick=\"document.getElementById('train_disp_span').style.display = 'none';\">[ Close this box ]</a>";
	}
	mysql_close();
	echo $lst;
}

function selRoute($trid,$start,$finish,$dt=0){
        // Generates route for auto train (ichange_trains.train_id = $trid).
        // $start = place were car is put in train. Can be empty if ichange_trains.auto is an integer > 0.
        // $finish = place where car is spotted by train.
        db_conn();
        $trid = charConv($trid,"[AMP]","&"); // Require to convert [AMP] back to '&'
        $start = charConv($start,"[AMP]","&");
        $finish = charConv($finish,"[AMP]","&");
        $sql = "SELECT `auto`,`origin`,`destination` FROM `ichange_trains` WHERE `train_id` = '".$trid."' LIMIT 1";
        $qry = mysql_query($sql);
        $res = mysql_fetch_array($qry);
        if(intval($res['auto']) > 0){
                // Number of days to complete
                $arr = array($start => $dt, $finish => $res['auto']+$dt);
        }else{
                // JSON array
                $arr_tmp = @json_decode($res['auto'],TRUE);
                $arr_tmp[$res['origin']] = 0;
                asort($arr_tmp);
                $arr_kys = array_keys($arr_tmp);
                $orig = $arr_tmp[$start]; // day value of entry waypoint
                $dest = $arr_tmp[$finish]; // day value of exit waypoint
                for($i=0;$i<count($arr_kys);$i++){
                        if($arr_tmp[$arr_kys[$i]] > $orig && $arr_tmp[$arr_kys[$i]] < $dest){
                                $arr[$arr_kys[$i]] = $arr_tmp[$arr_kys[$i]] - $orig;
                        }
                }
                $arr[$start] = 0;
                $arr[$finish] = $dest-$orig;
                $a_kys = array_keys($arr);
                for($i=0;$i<count($a_kys);$i++){
              		$arr[$a_kys[$i]] = $arr[$a_kys[$i]] + $dt;
                }
                asort($arr);
        }
        $json = json_encode($arr);
        echo $json;
}

function carsAutoFind($str,$fld){
	// Auto Complete function called by AJAX.
	// Checks whether an entry exists in the database
	// $fld = field to search in
	// $str = string to search for in field $fld
	db_conn();
	$str = charConv($str,"[AMP]","&"); // Require to convert [AMP] back to '&'
	$str = strtoupper($str);
	$sql = "SELECT * FROM `ichange_cars` WHERE (`".$fld."` LIKE '%".$str."%' OR `car_num` LIKE '%".$str."%' OR `aar_type` LIKE '%".$str."%') AND `rr` = '".$_COOKIE['rr_sess']."' ORDER BY `car_num` LIMIT 25";
	//$sql = "SELECT `ichange_cars`.*,COUNT(`ichange_carsused_index`.`id`) AS `cntr` FROM `ichange_cars` LEFT JOIN `ichange_carsused_index` ON `ichange_cars`.`car_num` = `ichange_carsused_index`.`car_num` WHERE (`ichange_cars`.`".$fld."` LIKE '%".$str."%' OR `ichange_cars`.`car_num` LIKE '%".$str."%' OR `ichange_cars`.`aar_type` LIKE '%".$str."%') AND `ichange_cars`.`rr` = '".$_COOKIE['rr_sess']."' ORDER BY `ichange_cars`.`car_num` LIMIT 25";
	//$sql = "SELECT `ichange_cars`.*,COUNT(`ichange_carsused_index`.`id`) AS `cntr` FROM `ichange_carsused_index` LEFT JOIN `ichange_cars` ON `ichange_carsused_index`.`car_num` = `ichange_cars`.`car_num` WHERE (`ichange_cars`.`".$fld."` LIKE '%".$str."%' OR `ichange_cars`.`car_num` LIKE '%".$str."%' OR `ichange_cars`.`aar_type` LIKE '%".$str."%') AND `ichange_cars`.`rr` = '".$_COOKIE['rr_sess']."' ORDER BY `ichange_cars`.`car_num` LIMIT 25";
	//$sql = "SELECT `ichange_cars`.*,COUNT(`ichange_carsused_index`.`car_num`) AS `cntr` FROM `ichange_cars`, `ichange_carsused_index` WHERE (`ichange_cars`.`".$fld."` LIKE '%".$str."%' OR `ichange_cars`.`car_num` LIKE '%".$str."%' OR `ichange_cars`.`aar_type` LIKE '%".$str."%') AND `ichange_cars`.`rr` = '".$_COOKIE['rr_sess']."' ORDER BY `ichange_cars`.`car_num` LIMIT 25";
	$qry = mysql_query($sql);
	$lst = "<table style=\"padding: 1px; background-color: transparent; border: none;\">";
	$lst .= "<tr><td class=\"td_title\">Car #</td><td class=\"td_title\">AAR</td><td class=\"td_title\">Lading</td><td class=\"td_title\">Location</td></tr>";
	$cntr=0;
	$cars = array();
	while($res = mysql_fetch_array($qry)){
		$cars[] = array(
			'car_num' => $res['car_num'], 
			'aar_type' => $res['aar_type'], 
			'lading' => $res['lading'], 
			'location' => $res['location'],
			'cntr' => qry_cntr("ichange_carsused_index", $res['car_num'], "car_num")
		);	
	}
	for($i=0;$i<count($cars);$i++){
		$lu = "background-color: transparent;";
		if($cars[$i]['cntr'] > 3){$used_style = "font-weight: bold;";}
		if($cars[$i]['cntr'] > 5){$used_style = "font-weight: bolder;";}
		$lst .= "<tr><td class=\"td1\"><a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\"></a>".$cars[$i]['car_num']." (".$cars[$i]['cntr'].")</td><td class=\"td1\">(".$cars[$i]['aar_type'].")</td><td class=\"td1\">".$cars[$i]['lading']."</td><td class=\"td1\">".$cars[$i]['location']."</td></tr>";
	}
	if(count($cars) < 1){$lst = "<tr><td>No results found!</td></tr>";}
	$lst .= "</table>";
	mysql_close();
	echo $lst;
}

function industAutoComp($str,$tbl,$fld,$sct = NULL,$sr = NULL){
	// Auto Complete function called by AJAX.
	// Checks whether an entry exists in the database
	// $fld = field to search in
	// $tbl = table to search in
	// $str = string to search for in field $fld - can be industry name, location, or commodity
	// $sct = span to display results in
	// $sr = whether a commodity is sent or received by the industry: 0 or null = all, 1=send, 2=receive
	db_conn();
	$str = charConv($str,"[AMP]","&"); // Require to convert [AMP] back to '&'
	$tbl = "ichange_indust";
	$str = strtoupper($str);
	$sql_sr = " OR `freight_in` LIKE '%".$str."%' OR `freight_out` LIKE '%".$str."%'";
	if($sr == 1){$sql_sr = " OR `freight_out` LIKE '%".$str."%'";}
	if($sr == 2){$sql_sr = " OR `freight_in` LIKE '%".$str."%'";}
	$sql = "SELECT * FROM `".$tbl."` WHERE `indust_name` LIKE '%".$str."%'".$sql_sr." LIMIT 9";
	$qry = mysql_query($sql);
	$lst = "<a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a><br />";
	while($res = mysql_fetch_array($qry)){
		$rr_mark = qry("ichange_rr", $res['rr'], "id", "report_mark");
		$recs = $res['freight_in'];
		$sends = $res['freight_out'];
		
		$autoCompNote = "";
		if(strlen($str) > 0){
			if(strpos("a".strtoupper($sends),$str) > 0 && $sr == 1){$autoCompNote = "<span style=\"text-decoration: underline;\">Sends: ".$str."</span>";}
			if(strpos("a".strtoupper($recs),$str) > 0 && $sr == 2){$autoCompNote = "<span style=\"text-decoration: underline;\">Receives: ".$str."</span>";}
		}

		$op_info = trim($res['op_info']); $show_indDescDiv = "";
		if(strlen($op_info) > 0){$show_indDescDiv = " document.getElementById('".$sct."_indDescDiv').style.display = 'block';";}		
		$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\" onClick=\"document.getElementById('".$sct."').value = '[".$res['id']."] ".trim($res['indust_name'])."'; document.getElementById('".$sct."_indDesc').value = '".trim($res['op_info'])."'; document.getElementById('".$fld."_span').style.display = 'none';".$show_indDescDiv."\">".$res['indust_name']."</a>&nbsp;".$autoCompNote."&nbsp(".$rr_mark.")<br />";
		if(strlen($res['desc']) > 1){$lst .= "<div style=\"display: block; font-size:8pt; max-width: 600px; color: #333\">&nbsp;&nbsp;&nbsp;".$res['desc']."</div>";}
	}
	$sql = "SELECT * FROM `ichange_ind40k` WHERE `industry` LIKE '%".$str."%' OR `city` LIKE '%".$str."%' OR `state` LIKE '%".$str."%' OR `commodity` LIKE '%".$str."%' LIMIT 9";
	$qry = mysql_query($sql);
	$rows = mysql_num_rows($qry);
	if($rows > 0){$lst .= "===== 40,000 Industry Records Found =====<br />";}
	while($res = mysql_fetch_array($qry)){
		$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\" onClick=\"document.getElementById('".$sct."').value = '".trim(strtoupper($res['industry'].",".$res['city'].",".$res['state']))."'; document.getElementById('".$fld."_span').style.display = 'none';\">".strtoupper($res['industry'].",".$res['city'].",".$res['state'])."</a><span style=\"font-size: 8pt;\"></span><br />";
	}
	if(strlen($lst) < 1){
		$lst = "No results found!";
	}else{
		$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a>";
	}
	mysql_close();
	echo $lst;
}

function trainAutoComp($str,$fld,$sct = NULL,$la=0){
	// Auto Complete function called by AJAX.
	// Checks whether an entry exists in the database
	// $fld = field to search in
	// $tbl = table to search in
	// $str = string to search for in field $fld - can be train_id, train_desc, origin, destination, or auto waypoint
	// $sct = span to display results in
	db_conn(); //$sqli = db_conn();
	//echo "<pre>"; print_r($sqli); echo "</pre>";
	$str = charConv($str,"[AMP]","&"); // Require to convert [AMP] back to '&'
	$tbl = "ichange_trains";
	$str = strtoupper($str);
	$sql = "SELECT * FROM `".$tbl."` WHERE (`train_id` LIKE '%".$str."%' OR `train_desc` LIKE '%".$str."%' OR `origin` LIKE '%".$str."%' OR `destination` LIKE '%".$str."%' OR `auto` LIKE '%".$str."%' OR `op_notes` LIKE '%".$str."%')";
	if($la == 1){ $sql .= " AND (`auto` > 0 OR `auto` LIKE '%:%')"; }
	$sql .= " ORDER BY `train_id`";
	//echo $sql;
	$qry = mysql_query($sql);
	//echo "<pre>"; print_r($qry); echo "</pre>";
	$lst = "<a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a><br />";
	while($res = mysql_fetch_array($qry)){
		$fnd = 0;
		$xtra = "";
		if(intval($res['auto']) > 0 || strlen($res['auto']) > 3){$xtra .= "&nbsp;&nbsp;&nbsp;<span style=\"color: red; font-weight: bold;\">Is an AUTO train.</span><br />";}
		if(strlen($str) > 2){
			if(strpos("z".$res['train_desc'],$str) > 0){$fnd = 1;}
			if(strpos("z".$res['auto'],$str) > 0){$xtra .= "&nbsp;&nbsp;&nbsp;Waypoint ".strtoupper($str)." served by train.<br />";$fnd = 1;}
			if(strpos("z".$res['origin'],$str) > 0){$xtra .= "&nbsp;&nbsp;&nbsp;".strtoupper($str)." is origin of train.<br />";$fnd = 1;}
			if(strpos("z".$res['destination'],$str) > 0){$xtra .= "&nbsp;&nbsp;&nbsp;".strtoupper($str)." is destination of train.<br />";$fnd = 1;}
			if(strpos("z".$res['op_notes'],$str) > 0 && $fnd == 0){$xtra .= "&nbsp;&nbsp;&nbsp;".strtoupper($str)." is mentioned in the Op Notes.<br />";}
		}
		if(strlen($xtra) > 0){$xtra = "<span style=\"font-size: 8pt;\"> ".$xtra."</span>";}
		$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\" onclick=\"document.getElementById('".$fld."').value='".trim(strtoupper($res['train_id']))."'; document.getElementById('".$sct."_span').style.display='none'; selTrain('".$res['train_id']."');rebuildDateSel('pfld2_0','fld14');\">".$res['train_id']."</a> - ".$res['train_desc']."<br />".$xtra;
	}
	if(strlen($lst) < 1){
		$lst = "No results found!";
	}else{
		$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a>";
	}
	mysql_close();
	//$sqli->close();
	echo $lst;
}

function autoComp($str,$tbl,$fld,$sct = NULL){
	// Auto Complete function called by AJAX.
	// Checks whether an entry exists in the database
	// $fld = field to search in, or comma separated list where first field is field to search in and subsequent ones display in span.
	// $tbl = table to search in
	// $str = string to search for in field $fld
    // $sct = field name in form to add value to.
	db_conn();
	$str = charConv($str,"[AMP]","&"); // Require to convert [AMP] back to '&'
	$xtra_flds = "";
	if(strpos($fld,",") > 0){
		$fld_tmp = explode (",",$fld);
		$fld = $fld_tmp[0];
		for($i=1;$i<count($fld_tmp);$i++){$xtra_flds .= ", `".$fld_tmp[$i]."`";}
	}
	$sql = "SELECT DISTINCT `".$fld."`".$xtra_flds." FROM `".$tbl."` WHERE `".$fld."` LIKE '%".$str."%' LIMIT 4";
	$qry = mysql_query($sql);
	$lst = "<span style=\"float: right;\"><a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a></span>";
	while($res = mysql_fetch_array($qry)){
		//$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\" onClick=\"document.getElementById('".$sct."').value = '".trim($res[$fld])."'; document.getElementById('".$fld."_span').style.display = 'none';\">".$res[$fld]."</a><br />";
		$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\" onClick=\"document.getElementById('".$sct."').value = '".trim($res[$fld])."'; document.getElementById('".$sct."_span').style.display = 'none';\">".$res[$fld]."</a><br />";
		if(isset($fld_tmp)){
			for($o=1;$o<count($fld_tmp);$o++){
				if(strlen($res[$fld_tmp[$o]]) > 0){$lst .= "<span style=\"color: #777; padding-left: 5px;\">".ucwords($fld_tmp[$o]).": ".$res[$fld_tmp[$o]]."</span><br />";}
			}
		}
	}
	if(strlen($lst) < 1){
		$lst = "No results found!";
	}else{
		$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a>";
	}
	mysql_close();
	echo $lst;
}

function allocTrain($wb,$tr){
	// Auto Complete function called by AJAX.
	// Checks whether an entry exists in the database
	// $w = waybill
	// $t = train to allocate to waybill
	db_conn();
	$tr = charConv($tr,"[AMP]","&"); // Require to convert [AMP] back to '&'
	$wb = charConv($wb,"[AMP]","&"); // Require to convert [AMP] back to '&'
	$sql = "UPDATE `ichange_waybill` SET `train_id` = '".$tr."' WHERE `id` = '".$wb."'";
	$qry = mysql_query($sql);
	mysql_close();
}

function allocRR($wb,$tr){
	// Auto Complete function called by AJAX.
	// Checks whether an entry exists in the database
	// $w = waybill
	// $tr = railroad to allocate waybill to
	db_conn();
	$sql = "UPDATE `ichange_waybill` SET `rr_id_handling` = '".$tr."', `train_id` = '' WHERE `id` = '".$wb."'";
	$qry = mysql_query($sql);
	mysql_close();
}

function swOrd($wb,$val){
	db_conn();
	$sql = "UPDATE `ichange_waybill` SET `sw_order` = '".$val."' WHERE `id` = '".$wb."'";
	$qry = mysql_query($sql);
	mysql_close();
}

function mapDetails($s){
	// Builds map details for waybills, for the state/province code selected in $s
	db_conn();
	$s = strtoupper($s);
	$sql = "SELECT `id`,`status`,`waybill_num`,`progress` FROM `ichange_waybill` WHERE `status` != 'CLOSED' AND `progress` LIKE '%,".$s."%'";
	$qry = mysql_query($sql);
	$info = "";
	while($r=mysql_fetch_array($qry)){
			$prog = json_decode($r['progress'], true);
			$progCntr = count($prog)-1;
			$date = $prog[$progCntr]['date'];
			$text = $prog[$progCntr]['text'];
			$map_location = str_replace(", ",",",$prog[$progCntr]['map_location']);
			if(strpos($prog[$progCntr]['map_location'],",".$s) > 0){
				//$info .= "<hr />".$r['waybill_num']." - ".$r['date']."<br />".$text." - ".$prog[$progCntr]['map_location'];
				$info .= "<hr /><a href=\"javascript:{}\" onclick=\"wbDetails('".$r['id']."');\">".$r['waybill_num']."</a><br />".$date."<br />".$r['status']."<br />".$prog[$progCntr]['map_location']."&nbsp;<a href=\"../waybill/edit/".$r['id']."\">Edit</a>";
			}
	}
	echo $info;
}

function mapWBDetails($s){
	// Builds map details for waybills, for the state/province code selected in $s
	db_conn();
	$s = strtoupper($s);
	$sql = "SELECT * FROM `ichange_waybill` WHERE `id` = '".$s."'";
	$qry = mysql_query($sql);
	$info = "";
	while($r=mysql_fetch_array($qry)){
			$prog = json_decode($r['progress'], true);
			$progCntr = count($prog)-1;
			$date = $prog[$progCntr]['date'];
			$text = $prog[$progCntr]['text'];
			$map_location = str_replace(", ",",",$prog[$progCntr]['map_location']);
			//$info .= "<hr />".$r['waybill_num']." - ".$r['date']."<br />".$text." - ".$prog[$progCntr]['map_location'];
			$info .= "<span style=\"font-size:12pt; font-weight: bold;\">".$r['waybill_num']."</span><br />".$r['date']."<br />At: ".$prog[$progCntr]['map_location']."<br />";
			$info .= "From ".$r['indust_origin_name']."<br />To: ".$r['indust_dest_name']."<br />";
			$info .= "Routing: ".$r['routing']."<br />";
			$info .= "<hr />Progress:<hr />";
			for($i=0;$i<count($prog);$i++){
				$info .= $prog[$i]['date']." - ".$prog[$i]['text']."<br />";
			}
	}
	echo $info;
}

function glCreate($i){
	// Convert a generate load to a waybill
	db_conn();
	$sql = "SELECT * FROM `ichange_generated_loads` WHERE `id` = '".$i."'";
	$qry = mysql_query($sql);
	while($res = mysql_fetch_array($qry)){		
		$sqc = "INSERT INTO `ichange_waybill` SET 
			`rr_id_from` = '".$res['railroad']."', 
			`rr_id_handling` = '".$res['railroad']."', 
			`rr_id_to` = '0',
			`indust_origin_name` = '".$res['orig_industry']."', 
			`indust_dest_name` = '??', 
			`return_to` = '".$res['orig_industry']."', 
			`waybill_num` = '".date('YmdHis')."', 
			`status` = 'WAYBILL', 
			`notes` = 'GENERATED FROM GOODS DELIVERED TO ".$res['orig_industry']." ON WAYBILL ".$res['waybill_num']."', 
			`lading` = '".$res['commodity']."', 
			`date` = '".$res['date_human']."'";
		mysql_query($sqc);
	}
	glDel($i);
}

function glDel($i){
	// Delete generated load record
	db_conn();
	$sql = "DELETE FROM `ichange_generated_loads` WHERE `id` = '".$i."'";
	mysql_query($sql);
}

function add2SW($id){
	// Get waybills allocated to railroad logged in as that are not already in switchlist id = $id
	db_conn();
	$ret = "";
	$path = explode("js/ajax.php",$_SERVER['REQUEST_URI']);
	
	// Get train details
	$sql = "SELECT `train_id` FROM `ichange_trains` WHERE `id` = '".$id."'";
	$qry = mysql_query($sql);
	$res = mysql_fetch_assoc($qry);
	$train_id = $res['train_id'];
	
	// Get waybills not already on switchlist
	$sql = "SELECT `id`, `routing`, `return_to`, `lading`, `train_id`, `waybill_num`, `indust_origin_name`, `indust_dest_name`, `status` FROM `ichange_waybill` WHERE `rr_id_handling` = '".@$_COOKIE['rr_sess']."' AND `train_id` != '".$train_id."' AND `train_id` != 'AUTO TRAIN' AND `status` != 'CLOSED'";
	$qry = mysql_query($sql);
	while($res = mysql_fetch_assoc($qry)){
		$ret .= "<div style=\"display: inline-block; border: 1px solid #ccc; border-radius: 5px; background-color: ivory; padding: 4px; margin: 2px; width: 300px; height: 93px; overflow: hidden; font-size: 9pt;\"><a href=\"javascript:{}\" onclick=\"if(confirm('Add this waybill to this switchlist?')){ window.location = '".$path[0]."switchlist/add2SW/".$res['id']."/".$id."'; }\">".$res['waybill_num']."</a> - ".$res['status'].".<br /><strong>".$res['indust_origin_name']." -> ".$res['indust_dest_name']." -> ".$res['return_to']."</strong><br />In train: <strong>".$res['train_id']."</strong><br />Lading: <strong>".$res['lading']."</strong><br />Routing: <strong>".$res['routing']."</strong></div>";
	}
	$ret .= "";
	
	echo $ret;
}

// Supporting functions
function db_conn(){
	$dbs = db_conn_settings();
	$dbhost=$dbs['dbhost'];
	$dbusername=$dbs['dbusername']; //"jstan_6_w";
	$dbpassword=$dbs['dbpassword']; //"Js120767";
	$dbname=$dbs['dbname']; //"jstan_general";

	$dbcnx = mysql_connect($dbhost, $dbusername, $dbpassword);
	$seldb = mysql_select_db($dbname);
}

function db_conn_settings(){
	// LIVE SERVER
	$dbhost="db150c.pair.com";//"db72d.pair.com";
	$dbusername="jstan2_2"; //"jstan_6_w";
	$dbpassword="Rs300777"; //"Js120767";
	$dbname="jstan2_general"; //"jstan_general";

	// TESTING
	$LocTst = $_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'];
	if(strpos($LocTst,"www/Applications/") > 0){
		$dbhost="localhost";
		$dbusername="admin";
		$dbpassword="admin";
		//$dbname="jstan_general";
	}
	$tmp = array(
		'dbhost' => $dbhost,
		'dbusername' => $dbusername,
		'dbpassword' => $dbpassword,
		'dbname' => $dbname
	);
	return $tmp;
}

function charConv($str,$from,$to){
	// Converts characters where necessary
	$str = str_replace($from,$to,$str);
	return $str;
}

function qry($tbl, $data, $ky, $fld){
	// Suitable to return ONE field of the db table, where the field name and data to search for are provided.
	// $tbl = the table to search in.		
	// $data = the data string to search for.
	// $ky = the name of the field to search in.
	// $fld = Field name to return value of.
	// $ret = Returned value of the function.
	db_conn();
	$sql_com = "SELECT * FROM `".$tbl."` WHERE `".$ky."` = '".$data."' LIMIT 1";
	$dosql_com = mysql_query($sql_com);
	$ret = "";
	while($resultcom = mysql_fetch_array($dosql_com)){			
		$ret = $resultcom[$fld];		
	}
		
	return $ret; //Value to return.
}

function qry_cntr($tbl, $data, $ky){
	// Suitable to return ONE field of the db table, where the field name and data to search for are provided.
	// $tbl = the table to search in.		
	// $data = the data string to search for.
	// $ky = the name of the field to search in.
	// $ret = Returned value of the function.
	db_conn();
	$sql = "SELECT `id` FROM `".$tbl."` WHERE `".$ky."` = '".$data."'";
	$qry = mysql_query($sql);
	//if($resultcom = mysql_fetch_array($dosql_com)){
	if(mysql_num_rows($qry)){return mysql_num_rows($qry);}
	else{return 0;} //$resultcom['cntr']; //Value to return.
	//}
}

function dateRebuildReturnCommon($t,$r,$ts,$f=0){ 
	// Non-AJAX date options rebuild - COPIED FROM dateRebuildReturn IN multi-prog.php!
	// Returns option tags for the train indicated in $t	
	// $t = train id
	// $r = railroad id
	// $ts = timestamp for first possible option
	// $f = format of option value. If 1 then use timestamp instead of YYYY-MM-DD format
	//$t = charConv($t,"[AMP]","&");
	$dbs = db_conn_settings();
	$drr_sqli = new mysqli($dbs['dbhost'],$dbs['dbusername'],$dbs['dbpassword'],$dbs['dbname']);
	$trd = $drr_sqli->query("SELECT sun,mon,tues,wed,thu,fri,sat FROM `ichange_trains` WHERE `train_id` = '".$t."'");
	$trres = $trd->fetch_assoc();

	$op_days = array();
	if(strlen($t) > 0){
		if($trres['sun'] == 1){$op_days[] = "Sun";}
		if($trres['mon'] == 1){$op_days[] = "Mon";}
		if($trres['tues'] == 1){$op_days[] = "Tue";}
		if($trres['wed'] == 1){$op_days[] = "Wed";}
		if($trres['thu'] == 1){$op_days[] = "Thu";}
		if($trres['fri'] == 1){$op_days[] = "Fri";}
		if($trres['sat'] == 1){$op_days[] = "Sat";}
	}

	// Get last progress report info and create date options.
	//$prd  = $sqli->query("SELECT * FROM `ichange_progress` WHERE `waybill_num` = '".$w."' ORDER BY `date` DESC, `time` DESC LIMIT 1"); // Latest Progress data
	//$progs = $prd->fetch_assoc();
	//$progs = @json_decode(qry("ichange_waybill",$w,"waybill_num","progress"),TRUE);
	//$last_prog_date = explode("-",$progs['date']); //explode("-",$progs[count($progs)-1]['date']);
	$last_prog_date_ux = $ts; //mktime(12,0,0,$last_prog_date[1],$last_prog_date[2],$last_prog_date[0]);
	$dt_opts = "";
	//$dt_opts .= "<option value=\"\">t = ".$t."</option>";
	//$dt_opts .= "<option value=\"\">rr = ".$r."</option>";
	//$dt_opts .= "<option value=\"\">tr = ".$ts."</option>";
	//$dt_opts .= "<option value=\"\">ts h-r = ".date('Y-m-d H:i:s',$ts)."</option>";
	if($last_prog_date_ux < intval(date('U')-(86400*20))){ $last_prog_date_ux = intval(date('U')-(86400*20)); }

	$mx_dys = 15;
	if($f == 1){
		for($md=0;$md<$mx_dys;$md++){
			$dt_unix = intval($ts+($md*86400)); //intval(date('U')+($md*86400));
			if(in_array(date('D',$dt_unix),$op_days) || count($op_days) == 0){
				$dt_opts .= "<option value=\"".intval($md+1)."\">".date('Y-m-d (D)',$dt_unix)."</option>";
			}
		}
	}else{
		//for($joe=date('U',$last_prog_date_ux);$joe<intval(date('U')+(86400*15));$joe=$joe+86400){
		for($joe=$last_prog_date_ux;$joe<intval($last_prog_date_ux+(86400*$mx_dys));$joe=$joe+86400){
			if(in_array(date('D',$joe),$op_days) || count($op_days) == 0){
				$sel = ""; 
				$joe_val = date('Y-m-d',$joe);
				if($f == 1){ $joe_val = $joe; 	}
				if($last_prog_date_ux <= $joe && !isset($trselected)){
					$sel = " selected=\"selected\"";
					$trselected = 1;
				}
				$dt_opts .= "<option value=\"".$joe_val."\"".$sel.">".date('Y-m-d (D)',$joe)."</option>";
			}
		}
	}

	$drr_sqli->close();
	return $dt_opts;
}
?>