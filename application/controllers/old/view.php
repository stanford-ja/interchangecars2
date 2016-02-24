<?php
	$_maxFlds = 20;	
	if(isset($_REQUEST['type'])){
		$type = strtoupper($_REQUEST['type']);
		$inc_file = strtolower($_REQUEST['type']).".php";
	}else{$type = ""; $inc_file="";}
	if(isset($_REQUEST['action'])){
		$action = strtoupper($_REQUEST['action']);
	}else{$action = "";}
	if(isset($_REQUEST['id'])){
		$id = $_REQUEST['id'];
	}else{$id = "";}
	
	include('vars.php');	
	include('db_connect7465.php');
	include('query_functions.php');

	$rr_sess = 0;
	$afil_ids = array();
	$cars_arr = array();
	if(isset($_COOKIE['rr_sess'])){
		$rr_sess = $_COOKIE['rr_sess'];
		$rr_arr = rrArray($rr_sess);
		$cars_arr = carArray($rr_sess);
	}
	//if(isset($_COOKIE['rr_sess'])){$cars_arr = carArray($rr_sess);} // Multi dim of cars, with primary key as car_num field.
	//echo "<pre>"; print_r($cars_arr); echo "</pre>";
//echo "here";exit(); 

	if($type == "SWITCHLIST"){
		$afil_rr_sql = "";
		if(@$rr_arr['show_affil_wb'] == 1){
		
			$afil_wb = str_replace(" ","",$rr_arr['affiliates']); //$rr_arr['show_affil_wb']);
			$afil_wbs = explode(";",$afil_wb);
			for($wir=0;$wir<count($afil_wbs);$wir++){
				$rr_id_afil = qry("ichange_rr", $afil_wbs[$wir], "report_mark", "id");
				$afil_rr_sql .= " OR `ichange_waybill`.`rr_id_handling` = '".$rr_id_afil."'";
				$afil_ids[] = $rr_id_afil;
			}
		
			//echo "<pre>"; print_r($afil_ids); echo "</pre>";
		
		
		}
		$ex_status = "('P_ORDER','UNLOADING','CLOSED')";
		//include('trains_query.php');
		
		if(isset($_GET['addWB']) || isset($_GET['remWB'])){
			if(isset($_GET['addWB'])){$wotTr = $id; $wotWB = $_GET['addWB'];}
			if(isset($_GET['remWB'])){$wotTr = ""; $wotWB = $_GET['remWB'];}
			$addWbS = "UPDATE `ichange_waybill` SET `train_id` = '".$wotTr."' WHERE `waybill_num` = '".$wotWB."'";
			mysql_query($addWbS);
		}
			
		if(isset($_REQUEST['runTrain'])){
			$wbArr = array();
			$sql = "SELECT `waybill_num` FROM `ichange_waybill` WHERE `status` NOT IN ".$ex_status." AND `train_id` = '".$id."' AND (`rr_id_handling` = '".$rr_sess."'".$afil_rr_sql.")";
			$qry = mysql_query($sql);
			while($res = mysql_fetch_array($qry)){$wbArr[] = $res['waybill_num'];}
			for($wbi=0;$wbi<count($wbArr);$wbi++){
				$mloc = strtoupper($_REQUEST['status']); 
				$mloc = str_replace("AT ","",$mloc);
				$mloca = explode("(",$mloc."(");


				$prog = progWB($wbArr[$wbi]);
				$prog[] = array(
					'date' => date('Y-m-d'), 
					'time' => date('H:i'), 
					'text' => "TRAIN ".$id." HAS MOVED CARS ON THIS WAYBILL TO ".$mloca[0], 
					'waybill_num' => $wbArr[$wbi], 
					'map_location' => $mloca[0], 
					'train' => $id, 
					'status' => strtoupper($_REQUEST['status'])
				);

				//'exit_location' => strtoupper($autoSav['entry_waypoint'])

				$jprog = json_encode($prog);
				//if(!isset($s_fld5)){$s_fld5 = "";}
				$sql_comm = "`progress` = '".$jprog."'"; //.$s_fld5;


				$sqlP = "UPDATE `ichange_waybill` SET `status` = '".strtoupper($_REQUEST['status'])."', `progress` = '".$jprog."' WHERE `waybill_num` = '".$wbArr[$wbi]."'"; 
				mysql_query($sqlP);
			}
			$goTo = "Location:view.php?type=SWITCHLIST&id=".$id;
			//echo $goTo;
			header($goTo);
		}
	}
?>
<html>
	<head>
		<title><?php echo $pgTitle; ?></title>
		<link REL="StyleSheet" HREF="style.css" TYPE="text/css" MEDIA="screen">
		<link REL="StyleSheet" HREF="print.css" TYPE="text/css" MEDIA="print">
		<link href="http://fonts.googleapis.com/css?family=Waiting+for+the+Sunrise" rel="stylesheet" type="text/css">
		<?php if($type == "WAYBILL"){ ?>
		<style>
			/* .td1 {font-family: Waiting for the Sunrise; font-size: 13pt; font-weight: bold; padding: 4px;} */
			.td1 {font-size: 13pt; padding: 5px;}
			.td3 {font-weight: bold; font-size: 13pt; padding: 5px;}
		</style>
		<?php } ?>
	</head>
	<script type="text/javascript">
		function copSel(fld1, fld2){
			// Copy data in fld1 to fld2.
			var cFld1 = document.getElementById(fld1).value;
			document.getElementById(fld2).value = cFld1;
		}
		
		function hideEle(fld3){
			document.getElementById(fld3).style.display = 'none';
		}
		
		function mess_win(type){
			win = window.open("message.php?type="+type, "", "width=400px, height=550px, resizable");
		}

	</script>
	<body>
	<h2><?php echo $pgTitle; ?> - <?php echo $action; ?> <?php echo $type; ?></h2>
	<table border="0" width="100%" align="center">
	<tr>
	<td>
		<?php include('menu.php'); ?>
	</td>
	</tr>
	</table>
	<table border="0" width="100%">
	
	<!-- start If type = SWITCHLIST // -->
	<?php if($type == "SWITCHLIST"){
			//include('trains_query.php');
			
			$op_days = "";
			$sql = "SELECT * FROM `ichange_trains` WHERE `train_id` = '".$id."' LIMIT 1";
			$dosql = mysql_query($sql);
			$result = mysql_fetch_array($dosql);
			
			$fld1 = $result['train_id'];
			$fld2 = $result['train_desc'];

			$fld3 = $result['no_cars'];
			$fld4 = $result['sun'];
			$fld5 = $result['mon'];
			$fld6 = $result['tues'];
			$fld7 = $result['wed'];
			$fld8 = $result['thu'];
			$fld9 = $result['fri'];
			$fld10 = $result['sat'];
			$fld11 = $result['op_notes'];
			$fld12 = $result['direction'];
			$fld15 = $result['origin'];
			$fld16 = $result['destination'];
			
			if($fld4 == 1){$op_days .= "SUN ";}
			if($fld5 == 1){$op_days .= "MON ";}
			if($fld6 == 1){$op_days .= "TUE ";}
			if($fld7 == 1){$op_days .= "WED ";}
			if($fld8 == 1){$op_days .= "THU ";}
			if($fld9 == 1){$op_days .= "FRI ";}
			if($fld10 == 1){$op_days .= "SAT ";}
			
			$render_lst = "<tr>
									<td class=\"td3\" valign=\"top\" colspan=\"3\"><span class=\"med_txt\">Train ID: ".$fld1."</span></td><td valign=\"bottom\" class=\"td3\" colspan=\"3\">Operation Days: ".$op_days."</td>
								</tr>
								<tr>
									<td class=\"td3\" valign=\"top\" colspan=\"3\">".$fld2."</td><td class=\"td3\" colspan=\"3\">Direction: ".$fld12."</td>
								</tr>
								<tr>
									<td class=\"td3\" valign=\"top\" colspan=\"3\">Origin: ".$fld15."<br />Destination: ".$fld16."</td><td class=\"td3\" colspan=\"3\">Operation Notes:<br />".$fld11."<br /><br /></td>
								</tr>";

			if(isset($_COOKIE['rr_sess']) && $id != "NOT ALLOCATED"){
				$rr_view = " AND (`ichange_waybill`.`rr_id_handling` = '".$_COOKIE['rr_sess']."'".$afil_rr_sql.")";
				$rr_id = ""; //$_COOKIE['rr_sess'];
				/*
				if(isset($_REQUEST['rr'])){
					if($_REQUEST['rr'] == "all"){$rr_view = "";$rr_id = "&rr=".$_REQUEST['rr'];}
					else{$rr_view = " AND `ichange_waybill`.`rr_id_handling` = '".$_REQUEST['rr']."'";}
				}
				*/
				$render_lst .= "<tr>";
				$render_lst .= "<td colspan=\"3\" style=\"text-align: right; font-size: 10pt;\">Add waybill / cars to this switchlist:</td>";
				$render_lst .= "<td colspan=\"3\"> ";
				$render_lst .= "<select style=\"font-size: 7pt;\" name=\"addWb\" onChange=\"window.location = 'view.php?type=SWITCHLIST&id=".$id."&addWB=' + this.value + '".$rr_id."';\">";
				$render_lst .= "<option value=\"\">-- Select --</option>";
				//$sw = "SELECT `ichange_waybill`.*,`ichange_progress`.`text`, `ichange_progress`.`map_location` FROM `ichange_progress`,`ichange_waybill` WHERE `ichange_waybill`.`waybill_num` = `ichange_progress`.`waybill_num` AND `ichange_waybill`.`train_id` != '".$id."' AND `ichange_waybill`.`status` != 'CLOSED'";
				$sw = "SELECT `ichange_waybill`.* FROM `ichange_waybill` WHERE `ichange_waybill`.`train_id` != '".$id."'".$rr_view." AND `ichange_waybill`.`status` != 'CLOSED'";
				$qw = mysql_query($sw);
				while($rw = mysql_fetch_array($qw)){
					$car_num_arr = @json_decode($rw['cars'],true);
					$car_num = "";
					for($ci=0;$ci<count($car_num_arr);$ci++){
						//if($car_num_arr[$ci]['RR'] == $_COOKIE['rr_sess']){$car_num .= $car_num_arr[$ci]['NUM']."(".$car_num_arr[$ci]['AAR']."), ";}
						if (in_array($car_num_arr[$ci]['RR'],$afil_ids) || $car_num_arr[$ci]['RR'] == $rr_sess){
							$car_num .= $car_num_arr[$ci]['NUM']."(".$car_num_arr[$ci]['AAR'].") ";
						}
					}
					$render_lst .= "<option value=\"".$rw['waybill_num']."\">".substr($car_num,0,40)." - ".$rw['status']." to ".$rw['indust_dest_name']."</option>";
				}
				$render_lst .= "</select>";
				$render_lst .= "</tr>";
				
				$render_lst .= "<tr>";
				$render_lst .= "<td colspan=\"3\" style=\"text-align: right; font-size: 10pt;\">Move cars in this train to locate them at:<br />";
				$render_lst .= "<span style=\"font-size: 8pt;\"><em>[excluding ".str_replace(",",", ",$ex_status)." status types]</em></span></td><td colspan=\"3\">";
				$render_lst .= "<form name=\"runtrain\" method=\"post\" action=\"view.php\">";
				$render_lst .= "<input type=\"hidden\" name=\"id\" value=\"".$id."\" />";
				$render_lst .= "<input type=\"hidden\" name=\"type\" value=\"".$type."\" />";
				$render_lst .= "<input type=\"hidden\" name=\"runTrain\" value=\"1\" />";
				$render_lst .= "<select name=\"status\">".rr_ichange_lst("",0,array('where' => "`id` = '".$_COOKIE['rr_sess']."'"))."</select>";
				$render_lst .= "&nbsp;<input type=\"submit\" name=\"submit\" value=\"Run Train\" />";
				$render_lst .= "</form>";
				$render_lst .= "</td>";
				$render_lst .= "</tr>";
			}

			$sql2 = "SELECT * FROM `ichange_waybill` WHERE `train_id` = '$id' AND `status` != 'CLOSED'  AND (`rr_id_handling` = '".$rr_sess."'".$afil_rr_sql.") ORDER BY `car_num` ASC";
			$dosql2 = mysql_query($sql2);
			$render_lst .= "<tr>
									<td class=\"td_title\">Waybill</td>
									<td class=\"td_title\">Car Num / AAR</td>
									<td class=\"td_title\">Alias Num / AAR</td>
									<td class=\"td_title\">Origin</td>
									<td class=\"td_title\">Destination</td>
									<td class=\"td_title\">Lading</td>
									<td class=\"td_title\">Routing</td>
								</tr>";
			$fld2_1 = $id;
			$wcntr=0;
			while($result2 = mysql_fetch_array($dosql2)){
				$wcntr++;
				$waybill_id = $result2['id'];
				$fld2_2 = $result2['car_num'];
				$fld2_3 = $result2['car_aar'];
				$fld2_4 = $result2['alias_num'];
				$fld2_5 = $result2['alias_aar'];
				$fld2_6 = $result2['indust_origin_name'];
				$fld2_7 = $result2['indust_dest_name'];
				$fld2_8 = $result2['lading'];
				$fld2_9 = $result2['waybill_num'];
				$fld2_10 = $result2['status'];
				$fld2_11 = json_decode($result2['cars'], true);
				$fld2_12 = ""; if(strlen($result2['routing']) > 0){$fld2_12 = str_replace("-"," - ",$result2['routing']);}
				$other_data = @json_decode($result2['other_data'], true);

				if(strlen($result2['progress']) > 0){
					$prog = json_decode($result2['progress'], true);
					$last_prog = count($prog) - 1; 
					$progLn = $prog[$last_prog]['date']." - ".$prog[$last_prog]['text']." (".$prog[$last_prog]['map_location'].")";
				}else{
					$sql2b = "SELECT * FROM `ichange_progress` WHERE `waybill_num` = '".$fld2_9."' ORDER BY `date` DESC, `id` DESC LIMIT 1";
					$dosql2b = mysql_query($sql2b);
					$res2b = mysql_fetch_array($dosql2b);
					$progLn = $res2b['date']." - ".$res2b['text']." (".$res2b['map_location'].")";
				}
				
				/*
				$sql2b = "SELECT * FROM `ichange_progress` WHERE `waybill_num` = '".$fld2_9."' ORDER BY `date` DESC, `id` DESC LIMIT 0,1";
				$qry2b = mysql_query($sql2b);
				$res2b = mysql_fetch_array($qry2b);
				$progLn = $res2b['date']." - ".$res2b['text']." (".$res2b['map_location'].")";
*/
				$td_styl = "";
				if(is_int($wcntr/2)){
					// $td_styl = "background-color: ".$row_bg1."; font-size: 10pt;";
					$td_cla = "td1";
				}else{
					// $td_styl = "background-color: ".$row_bg2."; font-size: 10pt;";
					$td_cla = "td2";
				}
				
				$render_lst .= "<tr><td valign=\"top\" class=\"td1\">
					<a href=\"edit.php?action=EDIT&type=WAYBILL&id=".$fld2_9."\">Waybill</a><br />
					<a href=\"view.php?type=SWITCHLIST&id=".$id."&remWB=".$fld2_9."\">Remove</a>
					</td>"; // <a href=\"edit.php?action=EDIT&type=WAYBILL&id=".$fld2_9."\">..".substr($fld2_9,-5)."<br /><a href=\"edit.php?type=PROGRESS&action=NEW&id=".$fld2_9."\">Progress</a><br />
				if(count($fld2_11) > 0){
					$render_lst .= "<td colspan=\"2\" valign=\"top\" class=\"".$td_cla."\">";
					$found_car = 0;
					for($cn=0;$cn<count($fld2_11);$cn++){
						
						$rr_car_id = $fld2_11[$cn]['RR'];
						//echo $rr_car_id." - found = ".$found_car." - ";
						//echo "in arr: ".in_array($rr_car_id, $afil_ids)."<br />";
						if(($_COOKIE['rr_sess'] == $rr_car_id || in_array($rr_car_id, $afil_ids)) && $found_car == 0){
							if(strlen($fld2_11[$cn]['NUM']) > 0){				
							$render_lst .= $fld2_11[$cn]['NUM']." (".$fld2_11[$cn]['AAR'].")&nbsp;";
							$render_lst .= "&nbsp;<img src=\"images/".substr($fld2_11[$cn]['AAR'], 0, 1).".gif\" border=\"0\" title=\"".$fld2_11[$cn]['AAR']."\" /><br />";
							$render_lst .= "<em>".@$cars_arr[$fld2_11[$cn]['NUM']]['desc']."</em>";
							if(in_array($rr_car_id, $afil_ids)){
								$render_lst .= "<br /><span style=\"background-color: yellow;\">(Affiliate RR Car)</span>";
							} 
							$render_lst .= "<br />";
							//$found_car = 1;
							}
						}
					}
					$render_lst .= "</td>";
				}else{
					$render_lst .= "<td valign=\"top\" class=\"".$td_cla."\">".$fld2_2." (".$fld2_3.")&nbsp;&nbsp;<img src=\"images/".substr($fld2_3, 0, 1).".gif\" border=\"0\" title=\"".$fld2_3."\" /></td>";
					$render_lst .= "<td valign=\"top\" class=\"".$td_cla."\">".$fld2_4." (".$fld2_5.")&nbsp;&nbsp;<img src=\"images/".substr($fld2_5, 0, 1).".gif\" border=\"0\" title=\"".$fld2_5."\" /></td>";
				}
				if(isset($other_data['orig_ind_op'])){$fld2_6 .= "<span style=\"font-size: 8pt;\"><br /><br />".$other_data['orig_ind_op']."</span>";}
				if(isset($other_data['dest_ind_op'])){$fld2_7 .= "<span style=\"font-size: 8pt;\"><br /><br />".$other_data['dest_ind_op']."</span>";}

				$render_lst .= "<td valign=\"top\" class=\"".$td_cla."\">".$fld2_6."</td>";
				$render_lst .= "<td valign=\"top\" class=\"".$td_cla."\">".$fld2_7."</td>";
				$render_lst .= "<td valign=\"top\" class=\"".$td_cla."\">".$fld2_8."</td>";
				$render_lst .= "<td valign=\"top\" class=\"".$td_cla."\">".$fld2_12."</td>";
				$render_lst .= "<td>&nbsp;</td></tr>";
				$render_lst .= "<tr><td colspan=\"6\" class=\"".$td_cla."\" style=\"border-bottom: 2px solid brown;\">".$progLn."</td>";
				$render_lst .= "<td class=\"".$td_cla."\" style=\"border-bottom: 2px solid brown; text-align: right; font-weight: bold; padding-right: 5px;\">".$fld2_10."</td></tr>";
			}
			
			
			// include('cars.php');		
			// include($inc_file); 
	} 
	?>
	<!-- end If type = SWITCHLIST // -->

	<!-- start If type = WAYBILL // -->
	<?php if($type == "WAYBILL"){
			//include('trains_query.php');
			$op_days = "";
			$sql = "SELECT * FROM `ichange_waybill` WHERE `waybill_num` = '".$id."' LIMIT 1";
			$dosql = mysql_query($sql);
			$result = mysql_fetch_array($dosql);
			$other_data = @json_decode($result['other_data'], true);						
			$fld1 = $result['id'];
			$fld2 = $result['date'];
			// $fld3 = $result['rr_id_from'];
			$fld3 = qry("ichange_rr",$result['rr_id_from'], "id", "report_mark");
			// $fld4 = $result['rr_id_to'];
			$fld4 = qry("ichange_rr",$result['rr_id_to'], "id", "report_mark");
			$fld5 = $result['indust_origin_name'];
			$fld6 = $result['indust_dest_name'];
			$fld7 = $result['routing'];
			$fld8 = $result['status'];
			$fld9 = $result['waybill_num'];
			$fld10 = $result['car_num'];
			$fld11 = $result['car_aar'];
			$json_cars = @json_decode($result['cars'], true);
			for($oo=0;$oo<count($json_cars);$oo++){
				if(strlen($json_cars[$oo]['NUM']) > 0){
				$fld10 .= "[".$json_cars[$oo]['NUM']." (".$json_cars[$oo]['AAR']."),".qry("ichange_rr",$json_cars[$oo]['RR'], "id", "report_mark")."] ";
				}
			}
			$fld12 = $result['lading'];
			if(isset($other_data['commodity'])){$fld12 .= " / Previously: ".$other_data['commodity'];}
			$fld13 = $result['alias_num'];
			$fld14 = $result['alias_aar'];
			$fld15 = $result['train_id'];
			$fld16 = $result['po'];
			
			$render_lst = "";

			$render_lst .= "<tr><td class=\"td3\" style=\"font-family: Arial;\">Waybill #</td><td class=\"td1\">".$fld9."</td></tr>";
			$render_lst .= "<tr><td class=\"td3\" style=\"font-family: Arial;\">P/O</td><td class=\"td1\">".$fld16."</td></tr>";

			$render_lst .= "<tr><td class=\"td3\" style=\"font-family: Arial;\">Car Number</td><td class=\"td1\">".$fld10."</td></tr>";
			//$render_lst .= "<tr><td class=\"td3\" style=\"font-family: Arial;\">Alias Number</td><td class=\"td1\">".$fld13."</td></tr>";

			/*			
			$render_lst .= "<tr><td class=\"td3\" style=\"font-family: Arial;\">Car Number</td><td class=\"td1\">".$fld10." (AAR: ".$fld11.")</td></tr>";
			$render_lst .= "<tr><td class=\"td3\" style=\"font-family: Arial;\">Alias Number</td><td class=\"td1\">".$fld13." (AAR: ".$fld14.")</td></tr>";
			*/
			
			$render_lst .= "<tr><td class=\"td3\" style=\"font-family: Arial;\">Lading</td><td class=\"td1\">".$fld12."</td></tr>";
			$render_lst .= "<tr><td class=\"td3\" style=\"font-family: Arial;\">Date</td><td class=\"td1\">".$fld2."</td></tr>";
			$render_lst .= "<tr><td class=\"td3\" style=\"font-family: Arial;\">From Railroad</td><td class=\"td1\">".$fld3."</td></tr>";
			$render_lst .= "<tr><td class=\"td3\" style=\"font-family: Arial;\">To Railroad</td><td class=\"td1\">".$fld4."</td></tr>";
			$render_lst .= "<tr><td class=\"td3\" style=\"font-family: Arial;\">Origin Industry</td><td class=\"td1\">".$fld5."</td></tr>";
			$render_lst .= "<tr><td class=\"td3\" style=\"font-family: Arial;\">Destination Industry</td><td class=\"td1\">".$fld6."</td></tr>";
			$render_lst .= "<tr><td class=\"td3\" style=\"font-family: Arial;\">Routing</td><td class=\"td1\">".$fld7."</td></tr>";
			$render_lst .= "<tr><td class=\"td3\" style=\"font-family: Arial;\">Status</td><td class=\"td1\">".$fld8."</td></tr>";
			$render_lst .= "<tr><td class=\"td3\" style=\"font-family: Arial;\">In or Allocated to Train</td><td class=\"td1\">".$fld15."</td></tr>";
			
			// include('cars.php');		
			// include($inc_file); 
		$render_lst .= "<tr><td colspan=\"2\" style=\"background-color: peru\">PROGRESS ENTRIES</td></tr>";

		if(strlen($result['progress']) > 0){
			$prog = json_decode($result['progress'], true);
			$ip = count($prog)-1;
			while($ip>=0){
				//$render_lst .= "<tr><td valign=\"top\" class=\"td1\">".$prog[$ip]['date']." (".$prog[$ip]['waybill_num'].")"."</td>";
				$render_lst .= "<tr><td valign=\"top\" class=\"td1\">".$prog[$ip]['date']."</td>";
				$render_lst .= "<td valign=\"top\" class=\"td1\">".$prog[$ip]['text']."</td></tr>";
				$ip = $ip - 1;
			}
		}
		$sql2 = "SELECT * FROM `ichange_progress` WHERE `waybill_num` = '".$fld9."' ORDER BY `date` DESC";
		$dosql2 = mysql_query($sql2);
		$fld2_1 = $id;
		while($result = mysql_fetch_array($dosql2)){
		
			$progress_id = $result['id'];
			$fld2_2 = $result['date'];
			$fld2_3 = $result['text'];
			//$fld2_5 = $result['waybill_num'];
			//$render_lst .= "<tr><td valign=\"top\" class=\"td1\">".$fld2_2." (".$fld2_5.")"."</td>";
			$render_lst .= "<tr><td valign=\"top\" class=\"td1\">".$fld2_2."</td>";
			$render_lst .= "<td valign=\"top\" class=\"td1\">".$fld2_3."</td></tr>";
		}

	} 


	?>
	<!-- end If type = WAYBILL // -->

	<!-- start If type = WAYBILL LIST // -->
	<?php if($type == "WBLST"){
			//include('trains_query.php');
			
			$op_days = "";
			$render_lst = "<tr>
							<td>Date</td>
							<td>WB #</td>
							<td>Commodity</td>
							<td>From RR</td>
							<td>To RR</td>
							<td>Options</td>
							</tr>";
			$sql = "SELECT * FROM `ichange_waybill` WHERE `status` = 'CLOSED' ORDER BY `date` DESC LIMIT 1000";
			$dosql = mysql_query($sql);
			while($result = mysql_fetch_array($dosql)){			
				$other_data = @json_decode($result['other_data'], true);
				$fld1 = $result['id'];
				$fld2 = $result['date'];
				// $fld3 = $result['rr_id_from'];
				$fld3 = qry("ichange_rr",$result['rr_id_from'], "id", "report_mark");
				// $fld4 = $result['rr_id_to'];
				$fld4 = qry("ichange_rr",$result['rr_id_to'], "id", "report_mark");
				$fld5 = $result['indust_origin_name'];
				$fld6 = $result['indust_dest_name'];
				$fld7 = $result['routing'];
				$fld8 = $result['status'];
				$fld9 = $result['waybill_num'];
				//$fld10 = $result['car_num'];
				//$fld11 = $result['car_aar'];
				$fld12 = $result['lading'];
				if(isset($other_data['commodity'])){$fld12 .= " / Previously: ".$other_data['commodity'];}
				$fld13 = $result['alias_num'];
				$fld14 = $result['alias_aar'];
				$fld15 = $result['train_id'];
				$fld16 = $result['po'];
			

				$render_lst .= "<tr><td class=\"td1\">".$fld2."</td>";
				$render_lst .= "<td class=\"td1\">".$fld9."</td>";
				//$render_lst .= "<td class=\"td1\">".$fld10." (AAR: ".$fld11.")</td>";
				//$render_lst .= "<td class=\"td1\">".$fld13." (AAR: ".$fld14.")</td>";
				$render_lst .= "<td class=\"td1\">".$fld12."</td>";
				$render_lst .= "<td class=\"td1\">".$fld3."</td>";
				$render_lst .= "<td class=\"td1\">".$fld4."</td>";
				$render_lst .= "<td class=\"td1\"><a href=\"view.php?type=WAYBILL&id=".$fld9."\">View</a></td></tr>";
			
				// include('cars.php');		
				// include($inc_file);
			} 
	} 

	?>
	<!-- end If type = WAYBILL LIST // -->


		<?php echo $render_lst; ?>

		<tr>
			<td colspan="2"><a href="index.php">Home</a></td>
		</tr>
	</table>
	</form>
	</body>
</html>
<?php
	mysql_close(); 
?>