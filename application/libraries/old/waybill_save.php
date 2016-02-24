<?php
// waybill_save file included by ../save.php
	$flds_arr = array("","date","rr_id_from","rr_id_to","indust_origin_name","indust_dest_name","routing","status","waybill_num","car_num","car_aar","lading","alias_num","alias_aar","train_id","po","waybill_type","notes","rr_id_handling","return_to");
	//echo "<pre>"; print_r($flds_arr); echo "</pre>";
	$tbl = "ichange_waybill";
	$prog = progWB($id);
	$last_prog = count($prog)-1;
	if($action == "NEW"){
		$sql_prefix = "INSERT INTO `".$tbl."` SET";
		$sql_suffix = "";
		$mail_alert_msg = "NEW";
		$activity = " - Created New Waybill ".$_POST['fld8'];
		$id = $_POST['fld8'];
		if($chkd > 0){
		 $sql_comm2 = "UPDATE `ichange_availcars` SET `taken` = '1' WHERE `id` = '".$chkd."'";
		}
	}
	if($action == "EDIT"){
		$sql_prefix = "UPDATE `".$tbl."` SET";
		$sql_suffix = "WHERE `waybill_num` = '$id'";
		$mail_alert_msg = "EDIT";
		$activity = " - Edited Waybill ".$_POST['fld8'];
	}
	if($action == "DELETE"){
		$sql_prefix = "DELETE FROM `".$tbl."`";
		$sql_suffix = "WHERE `id` = '$id'";
		$mail_alert_msg = "EDIT";
		$activity = " - Deleted Waybill ".$id;
	}
	// Setting Return To field in waybill, where necessary
	if($_POST['fld16'] == "INTERNAL"){$mail_alert_msg = "";}
	if(strlen($_POST['fld19']) < 1){$_POST['fld19'] = $_POST['fld4'];}
	$returnToTmp = $_POST['fld19'];
	if($action == "NEW"){
		$returnToTmp = qry("ichange_cars", $_POST['fld9'], "car_num", "special_instruct");
		if(strpos($returnToTmp, "ETURN TO") > 0){$_POST['fld19'] = str_replace("RETURN TO","",$returnToTmp);}
	}			
		
	// Insert record into ichange_auto and add to progress if allocated to an automatic train.		
	$d_qry = qry("ichange_trains", $_POST['fld14'], "train_id", "auto");
	$d_wps = json_decode($d_qry, true);
	$fld14b = $_POST['fld14'];
	if(strlen($_POST['pfld3']) > 0){
		mysql_query("DELETE FROM `ichange_auto` WHERE `waybill_num` = '".$_POST['fld8']."'");
	}
	if($d_qry != 0 || strlen($d_qry) > 4){ // || count($d_wps) > 0){
		//echo $d_qry."<br />";echo "<pre>"; print_r($d_wps); echo "</pre>";
		$autoSav = array("autotrain" => 1, "entry_waypoint" => $_POST['entry_waypoint'], "exit_waypoint" => $_POST['exit_waypoint'], "train_id" => $_POST['fld14'], "waybill_num" => $_POST['fld8'], "waybill_date" => $_POST['fld1']);
		autoSav($autoSav); // insert record into ichange_auto, update ichange_waybill.progress.				if($action != "NEW"){$_POST['fld14'] = "AUTO TRAIN";}
	}elseif($_POST['unload_days'] > 0){
		if(strlen($_POST['pfld6']) < 1){$_POST['pfld6'] = $prog[$last_prog]['map_locaton'];}
		$nxt_date = date('U') + (60*60*24*$_POST['unload_days']);
		$nxt_date = date('Y-m-d',$nxt_date);
		$description = "UNLOADED";
		$autoSav = array("unload" => 1, "waybill_date" => $nxt_date, "waybill_num" => $_POST['pfld4'], "description" => $description, "exit_waypoint" => $_POST['pfld6']);
		autoSav($autoSav); // insert record into ichange_auto, update ichange_waybill.progress.
		//$_POST['pfld3'] = $_POST['fld7']." *AUTOMATIC UNLOADING WILL BE COMPLETE ON ".$nxt_date."*";
		$_POST['pfld3'] .= " *AUTOMATIC UNLOADING WILL BE COMPLETE ON ".$nxt_date."*";
	}	

	$_POST['pfld6'] = str_replace(";",",",$_POST['pfld6']);
	$_POST['pfld6'] = str_replace(", ",",",$_POST['pfld6']);
	$_POST['pfld6'] = str_replace("  "," ",$_POST['pfld6']);
	$_POST['pfld6'] = trim($_POST['pfld6']);

	// build progress report if not allocated to an AUTO TRAIN.
	$prog_sql = "";
	if($d_qry < 1 && strlen($_POST['pfld3']) > 0){
	//if($d_qry == 0 && strlen($_POST['pfld3']) > 0){
		$email_wb = 1;
		if(!isset($autoSav['train_id'])){$autoSav['train_id'] = "";}
		if(!isset($autoSav['entry_waypoint'])){$autoSav['entry_waypoint'] = "";}
		if(!isset($autoSav['exit_waypoint'])){$autoSav['exit_waypoint'] = $_POST['exit_waypoint'];}
		$prog[] = array(
		'date' => $_POST['pfld2'], 
		'time' => $_POST['pfld7'].":".$_POST['pfld8'], 
		'text' => strtoupper($_POST['pfld3']), 
		'waybill_num' => $_POST['pfld4'], 
		'map_location' => strtoupper($_POST['pfld6']), 
		'status' => strtoupper($_POST['fld7']), 
		'train' => str_replace("NOT ALLOCATED","",$fld14b), 
		'rr' => $_POST['fld18'], 
		'exit_location' => strtoupper($autoSav['entry_waypoint']), 
		'tzone' => $_SESSION['_tz']
		);
	}
	$jprog = json_encode($prog);
	if($d_qry < 1 && strlen($_POST['pfld3']) > 0){$prog_sql = ", `progress` = '".$jprog."'";}
	// End progress updating
		
	// Start Build JSON array for Cars
	$cars = json_decode($_POST['fld21'], true);
	$car_found = 0;
	$aar_reqd = "";
	if(is_array($cars)){
		for($cn=0;$cn<count($cars);$cn++){
			//if($_COOKIE['rr_sess'] == $cars[$cn]['RR']){
			if($_POST['fld21_rr'] == $cars[$cn]['RR']){
				$car_found++;
				$cars[$cn]['NUM'] = strtoupper($_POST['fld21_car']);
				$cars[$cn]['AAR'] = strtoupper($_POST['fld21_aar']);
			}
		}
	}
	if($car_found == 0){
		$cars[] = array(
			'AAR_REQD' => strtoupper($_POST['fld10']),
			'NUM' => strtoupper($_POST['fld21_car']),
			'AAR' => strtoupper($_POST['fld21_aar']),
			'RR' => $_POST['fld21_rr']
		);
		// $_POST['fld21_rr'] was $_COOKIE['rr_sess']
	}
	$jcars = $_POST['fld21']; //json_encode($cars);
	// End build JSON array for Cars
		
	// Query string for adding / updating ichange_waybill
	// Changed 2011-12-05
	$sql_comm = "";
	for($i=1;$i<count($flds_arr);$i++){
		if($i>1){$sql_comm .= ", ";}
		$sql_comm .= "`".$flds_arr[$i]."` = '".@$_POST['fld'.$i]."'";
	}
	//echo "sql-comm = ".$sql_comm."<br />";
		
	// Start compile other data
	if($_POST['fld11'] != "MT" && $_POST['fld11'] != "EMPTY" && $_POST['fld11'] != "MTY" && strlen($_POST['fld11']) > 0){
		$other_data['commodity'] = $_POST['fld11']; 
	}
	if(strlen($_POST['fld4_indDesc']) > 0){$other_data['orig_ind_op'] = strtoupper($_POST['fld4_indDesc']);}
	if(strlen($_POST['fld5_indDesc']) > 0){$other_data['dest_ind_op'] = strtoupper($_POST['fld5_indDesc']);}
	if(isset($other_data) > 0){$sql_comm .= ", `other_data` = '".json_encode($other_data)."'";}
	// End compile other data
		
	$sql_comm .= ", `cars` = '".$jcars."'".$prog_sql;
		
	// Update car location.
	if(strlen($_POST['pfld6']) > 0){
		for($cn=0;$cn<count($cars);$cn++){
			//$carArrTmp = array('rr' => array($_POST['fld2'], $_POST['fld3'], $_POST['fld18']), 'cars' => array($_POST['fld9'], $_POST['fld12']), 'lading' => $_POST['fld11'], 'location' => strtoupper($_POST['pfld6']));
			$carArrTmp = array('rr' => array($cars[$cn]['RR']), 'cars' => array($cars[$cn]['NUM']), 'lading' => $_POST['fld11'], 'location' => strtoupper($_POST['pfld6']));
			carStatusUpd($carArrTmp);
		}
	}
		
	// Create generated load if unloading AND not already existing for waybill.
	$already_gen = q_cntr("ichange_generated_loads", "`waybill_num` = '".$_POST['fld8']."'");
	if($already_gen < 1 && ($_POST['fld7'] == "UNLOADING" || $_POST['fld7'] == "UNLOADED")){
		$rec_goods = qry("ichange_indust", $_POST['fld5'], "indust_name", "freight_in");
		$rec_goods = str_replace(", ",",",$rec_goods);
		$rec_goods = str_replace("(","",$rec_goods);
		$rec_goods = str_replace(")","",$rec_goods);
		$rexplode = explode(",", $rec_goods);
		$generates = array();
		if(in_array($_POST['fld11'],$rexplode)){
			$send_goods = qry("ichange_indust", $_POST['fld5'], "indust_name", "freight_out");
			$send_goods = str_replace(", ",",",$send_goods);
			$send_goods = str_replace("(","",$send_goods);
			$send_goods = str_replace(")","",$send_goods);
			$sends_out = explode(",", $send_goods);
		
			$gen_tmp = qry("ichange_commod", $_POST['fld11'], "commod_name", "generates");
			$gen_tmp = str_replace("; ",";",$gen_tmp);
			$genexp = explode(";",$gen_tmp);
			for($occ2=0;$occ2<count($genexp);$occ2++){
				if(in_array($genexp[$occ2],$sends_out)){$generates[] = $genexp[$occ2];}
			}
		}
		
		$r = @rand(0,count($generates)-1);
		if(strlen(@$generates[$r]) > 0){$gen_load = "INSERT INTO `ichange_generated_loads` SET 
			`added` = '".date('U')."', 
			`waybill_num` = '".$_POST['fld8']."', 
			`commodity` = '".$generates[$r]."', 
			`orig_industry` = '".$_POST['fld5']."', 
			`date_human` = '".date('Y-m-d')."', 
			`railroad` = '".$_COOKIE['rr_sess']."'";
		mysql_query($gen_load);
		}
	}

	// If waybill created from a generated loads, delete record from generated loads table
	if(isset($_POST['genload'])){
		$dgsql = "DELETE FROM `ichange_generated_loads` WHERE `id` = '".$_POST['genload']."'";
		mysql_query($dgsql);
	} 
?>