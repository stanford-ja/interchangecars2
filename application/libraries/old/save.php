<?php	 
	include('db_connect7465.php');
	include('query_functions.php');
	include('functions.php');
	@session_start();
	
	$action = "";
	$type = "";
	$id = "";	
	$win = "";
	$chkd = "";
	$mail_alert_msg = "";
	$sql_prefix = "";
	$sql_suffix = "";
	$sql_comm = ""; // Fields to be changed. Does not include UPDATE or INSERT keywords, etc.
	$sql_comm2 = ""; // HAS TO HAVE THE *FULL* QUERY, NOT JUST THE FIELDS TO BE CHANGED!
	$activity = "";
	$email_wb = 0;
/*

	type = the type of transaction (eg. waybill, indust). Uppercase!
	action = what to do with 'type' data (eg, edit, new, delete). Uppercase!
	id = Record ID, or other identifier for the record to be actioned
	win = Y or empty. if Y then the input has been through a popup 'window'.

*/
	if(isset($_GET['action'])){$action = $_GET['action'];}
	if(isset($_GET['type'])){$type = $_GET['type'];}
	if(isset($_GET['id'])){$id = $_GET['id'];}
	if(isset($_GET['win'])){$win = $_GET['win'];}
	if(isset($_GET['chkd'])){$chkd = $_GET['chkd'];}

	if(isset($_POST['action'])){$action = $_POST['action'];}
	if(isset($_POST['type'])){$type = $_POST['type'];}
	if(isset($_POST['id'])){$id = $_POST['id'];}
	if(isset($_POST['win'])){$win = $_POST['win'];}
	if(isset($_POST['chkd'])){$chkd = $_POST['chkd'];}
	
	// $_POST['goTo'] / $goTo is the URL to redirect after attempted save.
	$goTo = "";
	if(isset($_POST['goTo'])){
		$goTo = $_POST['goTo'];
	}
	
	$fld1 = "";
	$fld2 = "";
	$fld3 = "";
	$fld4 = "";
	$fld5 = "";
	$fld6 = "";
	$fld7 = "";
	$fld8 = "";
	$fld9 = "";
	$fld10 = "";
	$fld11 = "";
	$fld12 = "";
	$fld13 = "";
	$fld14 = "";
	$fld15 = "";
	$fld16 = "";
	$fld17 = "";
	$fld18 = "";
	$fld19 = "";
	$fld20 = "";
	$fld21 = "";
	
	//echo "id = ".$id."<br />";
	//echo "action = ".$action."<br />";
	//echo "type = ".$type."<br />";
	// strip_spec($orig_str) will strip chr(0) thru chr(32)!
	/*
	if(isset($_POST['fld1'])){$fld1 = str_replace("\n","",$_POST['fld1']);}
	if(isset($_POST['fld2'])){$fld2 = str_replace("\n","",$_POST['fld2']);}
	if(isset($_POST['fld3'])){$fld3 = str_replace("\n","",$_POST['fld3']);}
	if(isset($_POST['fld4'])){$fld4 = str_replace("\n","",$_POST['fld4']);}
	if(isset($_POST['fld5'])){$fld5 = str_replace("\n","",$_POST['fld5']);}
	if(isset($_POST['fld6'])){$fld6 = str_replace("\n","",$_POST['fld6']);}
	if(isset($_POST['fld7'])){$fld7 = str_replace("\n","",$_POST['fld7']);}
	if(isset($_POST['fld8'])){$fld8 = str_replace("\n","",$_POST['fld8']);}
	if(isset($_POST['fld9'])){$fld9 = str_replace("\n","",$_POST['fld9']);}
	if(isset($_POST['fld10'])){$fld10 = str_replace("\n","",$_POST['fld10']);}
	if(isset($_POST['fld11'])){$fld11 = str_replace("\n","",$_POST['fld11']);}
	if(isset($_POST['fld12'])){$fld12 = str_replace("\n","",$_POST['fld12']);}
	if(isset($_POST['fld13'])){$fld13 = str_replace("\n","",$_POST['fld13']);}
	if(isset($_POST['fld14'])){$fld14 = str_replace("\n","",$_POST['fld14']);}
	if(isset($_POST['fld15'])){$fld15 = str_replace("\n","",$_POST['fld15']);}
	if(isset($_POST['fld16'])){$fld16 = str_replace("\n","",$_POST['fld16']);}
	if(isset($_POST['fld17'])){$fld17 = str_replace("\n","",$_POST['fld17']);}
	if(isset($_POST['fld18'])){$fld18 = str_replace("\n","",$_POST['fld18']);}
	if(isset($_POST['fld19'])){$fld19 = str_replace("\n","",$_POST['fld19']);}
	if(isset($_POST['fld20'])){$fld20 = str_replace("\n","",$_POST['fld20']);}
	if(isset($_POST['fld21'])){$fld21 = str_replace("\n","",$_POST['fld21']);}
	*/
	if(isset($_POST['fld1'])){$fld1 = strip_spec($_POST['fld1']);}
	if(isset($_POST['fld2'])){$fld2 = strip_spec($_POST['fld2']);}
	if(isset($_POST['fld3'])){$fld3 = strip_spec($_POST['fld3']);}
	if(isset($_POST['fld4'])){$fld4 = strip_spec($_POST['fld4']);}
	if(isset($_POST['fld5'])){$fld5 = strip_spec($_POST['fld5']);}
	if(isset($_POST['fld6'])){$fld6 = strip_spec($_POST['fld6']);}
	if(isset($_POST['fld7'])){$fld7 = strip_spec($_POST['fld7']);}
	if(isset($_POST['fld8'])){$fld8 = strip_spec($_POST['fld8']);}
	if(isset($_POST['fld9'])){$fld9 = strip_spec($_POST['fld9']);}
	if(isset($_POST['fld10'])){$fld10 = strip_spec($_POST['fld10']);}
	if(isset($_POST['fld11'])){$fld11 = strip_spec($_POST['fld11']);}
	if(isset($_POST['fld12'])){$fld12 = strip_spec($_POST['fld12']);}
	if(isset($_POST['fld13'])){$fld13 = strip_spec($_POST['fld13']);}
	if(isset($_POST['fld14'])){$fld14 = strip_spec($_POST['fld14']);}
	if(isset($_POST['fld15'])){$fld15 = strip_spec($_POST['fld15']);}
	if(isset($_POST['fld16'])){$fld16 = strip_spec($_POST['fld16']);}
	if(isset($_POST['fld17'])){$fld17 = strip_spec($_POST['fld17']);}
	if(isset($_POST['fld18'])){$fld18 = strip_spec($_POST['fld18']);}
	if(isset($_POST['fld19'])){$fld19 = strip_spec($_POST['fld19']);}
	if(isset($_POST['fld20'])){$fld20 = strip_spec($_POST['fld20']);}
	if(isset($_POST['fld21'])){$fld21 = strip_spec($_POST['fld21']);}
	
	$max_flds = 25;
	if($type != "RR"){
		for($i=1;$i<$max_flds;$i++){
			if(isset($_POST['fld'.$i])){
				$_POST['fld'.$i] = strtoupper($_POST['fld'.$i]);
			}
		}
		$fld1 = strtoupper($fld1);
		$fld2 = strtoupper($fld2);
		$fld3 = strtoupper($fld3);
		$fld4 = strtoupper($fld4);
		$fld5 = strtoupper($fld5);
		$fld6 = strtoupper($fld6);
		$fld7 = strtoupper($fld7);
		$fld8 = strtoupper($fld8);
		$fld9 = strtoupper($fld9);
		$fld10 = strtoupper($fld10);
		$fld11 = strtoupper($fld11);
		$fld12 = strtoupper($fld12);
		$fld13 = strtoupper($fld13);
		$fld14 = strtoupper($fld14);
		$fld15 = strtoupper($fld15);
		$fld16 = strtoupper($fld16);
		$fld17 = strtoupper($fld17);
		$fld18 = strtoupper($fld18);
		$fld19 = strtoupper($fld19);
		$fld20 = strtoupper($fld20);
		$fld21 = strtoupper($fld21);
	}else{
		$_POST['fld6'] = strtoupper($_POST['fld6']);
		$fld6 = strtoupper($fld6);
		$_POST['fld16'] = strtoupper($_POST['fld16']);
		$fld16 = strtoupper($fld16);
	}
	
	if($type == "WAYBILL"){include("inc/waybill_save.php");}
	if($type == "TRANSHIP"){include("inc/tranship_save.php");}

	if($type == "RANDOMWB"){
		$tbl = "ichange_randomwb";
		if($action == "NEW"){
			$sql_prefix = "INSERT INTO `".$tbl."` SET";
			$sql_suffix = "";
			$mail_alert_msg = "NEW";
			$activity = " - Created Random Customer PO for ".$_POST['fld11']." from ".$_POST['fld4'];// " - Created New Random WB ".$_POST['fld8'];
			$id = $_POST['fld8'];
		}
		if($action == "EDIT"){
			$sql_prefix = "UPDATE `".$tbl."` SET";
			$sql_suffix = "WHERE `id` = '$id'";
			$mail_alert_msg = "EDIT";
			$activity = " - Edited Random Customer PO for ".$_POST['fld11']." from ".$_POST['fld4']; //" - Edited Waybill ".$_POST['fld8;
		}
		if($action == "DELETE"){
			$sql_prefix = "DELETE FROM `".$tbl."`";
			$sql_suffix = "WHERE `id` = '$id'";
			$mail_alert_msg = "EDIT";
			$activity = " - Deleted Random Customer PO for ".$_POST['fld11']." from ".$_POST['fld4']; //" - Deleted Waybill ".$id;
		}				
		// Query string for adding / updating ichange_randomwb
		$sql_comm = "`rr_id_from` = '".$_POST['fld2']."',	
					`rr_id_to` = '".$_POST['fld3']."',
					`indust_origin_name` = '".$_POST['fld4']."',
					`indust_dest_name` = '".$_POST['fld5']."',
					`routing` = '".$_POST['fld6']."',
					`car_aar` = '".$_POST['fld10']."', 
					`alias_aar` = '".$_POST['fld10']."', 
					`lading` = '".$_POST['fld11']."',
					`notes` = '".$_POST['fld17']."' ";
	}


	if($type == "RR"){
		$tbl = "ichange_rr";
		if($action == "NEW"){
			$sql_prefix = "INSERT INTO `".$tbl."` SET";
			$sql_suffix = "";
			$activity = " - New Railroad Added ".$_POST['fld2'];
		}
		if($action == "EDIT"){
			$sql_prefix = "UPDATE `".$tbl."` SET";
			$sql_suffix = "WHERE `id` = '$id'";
			$activity = " - Railroad ".$_POST['fld2']." edited ";
		}
		if($action == "DELETE"){
			$sql_prefix = "DELETE FROM `".$tbl."`";
			$sql_suffix = "WHERE `id` = '$id'";
			$mail_alert_msg = "EDIT";
		}
		
		$sql_comm = "`report_mark` = '".strtoupper($_POST['fld1'])."',
					`rr_name` = '".$_POST['fld2']."',					
					`rr_desc` = '".$_POST['fld3']."',
					`owner_name` = '".$_POST['fld4']."',
					`interchanges` = '".$_POST['fld5']."',
					`affiliates` = '".strtoupper($_POST['fld6'])."', 
					`common_flag` = '".$_POST['fld7']."', 
					`inactive` = '".$_POST['fld8']."', 
					`home_disp` = '".$_POST['fld9']."', 
					`pw` = '".$_POST['fld10']."',
					`quick_select` = '".$_POST['fld11']."', 
					`hide_auto` = '".$_POST['fld12']."', 
					`show_generated_loads` = '".$_POST['fld13']."', 
					`social` = '".$_POST['fld14']."', 
					`website` = '".$_POST['fld15']."', 
					`show_affil_wb` = '".$_POST['fld16']."', 
					`tzone` = '".$_POST['fld17']."'";
		$_SESSION['_tz'] = $_POST['fld17'];
	}

	if($type == "CARS"){
		$tbl = "ichange_cars";
		if($action == "NEW"){
			$sql_prefix = "INSERT INTO `".$tbl."` SET";
			$sql_suffix = "";
			$mail_alert_msg = "EDIT";
			$activity = " - New Car Added ".$_POST['fld2'];
		}
		if($action == "EDIT"){
			$sql_prefix = "UPDATE `".$tbl."` SET";
			$sql_suffix = "WHERE `id` = '$id'";
			$mail_alert_msg = "EDIT";
			$activity = " - Car ".$_POST['fld2']." edited";
		}
		if($action == "DELETE"){
			$sql_prefix = "DELETE FROM `".$tbl."`";
			$sql_suffix = "WHERE `id` = '$id'";
			$mail_alert_msg = "EDIT";
			$activity = " - Car Deleted - ".$_POST['fld2'];
		}
		
		$sql_comm = "`car_num` = '".$_POST['fld2']."',					
					`aar_type` = '".$_POST['fld3']."',
					`desc` = '".$_POST['fld4']."',
					`rr` = '".$_POST['fld5']."'";
	}

	if($type == "AAR"){
		$tbl = "ichange_aar";
		if($action == "NEW"){
			$sql_prefix = "INSERT INTO `".$tbl."` SET";
			$sql_suffix = "";
			$activity = " - AAR Code ".$_POST['fld1']." Added";
		}
		if($action == "EDIT"){
			$sql_prefix = "UPDATE `".$tbl."` SET";
			$sql_suffix = "WHERE `id` = '$id'";
			$activity = " - AAR Code ".$_POST['fld1']." Edited";
		}
		if($action == "DELETE"){
			$sql_prefix = "DELETE FROM `".$tbl."`";
			$sql_suffix = "WHERE `id` = '$id'";
		}
		
		$sql_comm = "`aar_code` = '".$_POST['fld1']."',
					`desc` = '".$_POST['fld2']."'";
	}

	if($type == "INDUST" || $type == "COMMOD"){
		$tbl = "ichange_".strtolower($type);
		if($action == "NEW"){
			$sql_prefix = "INSERT INTO `".$tbl."` SET";
			$sql_suffix = "";
			$mail_alert_msg = "EDIT";
			$activity = " - ".$type." ".$_POST['fld1']." Added";
		}
		if($action == "EDIT"){
			$sql_prefix = "UPDATE `".$tbl."` SET";
			$sql_suffix = "WHERE `id` = '$id'";
			$mail_alert_msg = "EDIT";
			$activity = " - ".$type." ".$_POST['fld1']." Edited";
		}
		if($action == "DELETE"){
			$sql_prefix = "DELETE FROM `".$tbl."`";
			$sql_suffix = "WHERE `id` = '$id'";
			$mail_alert_msg = "EDIT";
		}
		
		$sql_comm = "`".strtolower($type)."_name` = '".$_POST['fld1']."'";
		if($type == "INDUST"){
			$sql_comm .= ", `rr` = '".$_POST['fld2']."'";
		}else{
			$sql_comm .= ", `generates` = '".$_POST['fld2']."'";
		}
	}

	if($type == "TRAINS"){
		$tbl = "ichange_trains";
		if($action == "NEW"){
			$sql_prefix = "INSERT INTO `".$tbl."` SET";
			$sql_suffix = "";
			$mail_alert_msg = "EDIT";
			$activity = " - Train ".$_POST['fld1']." Added";
		}
		if($action == "EDIT"){
			$sql_prefix = "UPDATE `".$tbl."` SET";
			$sql_suffix = "WHERE `id` = '$id'";
			$mail_alert_msg = "EDIT";
			$activity = " - Train ".$_POST['fld1']." Edited";
		}
		if($action == "DELETE"){
			$sql_prefix = "DELETE FROM `".$tbl."`";
			$sql_suffix = "WHERE `id` = '$id'";
			$mail_alert_msg = "EDIT";
		}
		
		$sql_comm = "`train_id` = '".$_POST['fld1']."',
						`train_desc` = '".$_POST['fld2']."',
						`no_cars` = '".$_POST['fld3']."',
						`sun` = '".$_POST['fld4']."',
						`mon` = '".$_POST['fld5']."',
						`tues` = '".$_POST['fld6']."',
						`wed` = '".$_POST['fld7']."',
						`thu` = '".$_POST['fld8']."',
						`fri` = '".$_POST['fld9']."',
						`sat` = '".$_POST['fld10']."',
						`op_notes` = '".$_POST['fld11']."',
						`direction` = '".$_POST['fld12']."',
						`tr_sheet_ord` = '".$_POST['fld13']."',
						`railroad_id` = '".$_POST['fld14']."',
						`origin` = '".$_POST['fld15']."',
						`destination` = '".$_POST['fld16']."'";
	}

	if($type == "BLOCKS"){
		$tbl = "ichange_blocks";
		if($action == "NEW"){
			$sql_prefix = "INSERT INTO `".$tbl."` SET";
			$sql_suffix = "";
			$mail_alert_msg = "EDIT";
			$activity = " - New Block Added ".$_POST['fld2'];
		}
		if($action == "EDIT"){
			$sql_prefix = "UPDATE `".$tbl."` SET";
			$sql_suffix = "WHERE `id` = '$id'";
			$mail_alert_msg = "EDIT";
			$activity = " - Block ".$_POST['fld2']." edited";
		}
		if($action == "DELETE"){
			$sql_prefix = "DELETE FROM `".$tbl."`";
			$sql_suffix = "WHERE `id` = '$id'";
			$mail_alert_msg = "EDIT";
			$activity = " - Block Deleted - ".$_POST['fld2'];
		}
		
		$sql_comm = "`block_id` = '".$_POST['fld2']."',					
					`block_name` = '".$_POST['fld3']."',
					`block_desc` = '".$_POST['fld4']."',
					`restricts` = '".$_POST['fld6']."',
					`rr_id` = '".$_POST['fld7']."'";
	}

	if($type == "LOCATIONS"){
		$tbl = "ichange_locations";
		if($action == "NEW"){
			$sql_prefix = "INSERT INTO `".$tbl."` SET";
			$sql_suffix = "";
			$mail_alert_msg = "EDIT";
			$activity = " - New Location Added ".$_POST['fld4'];
		}
		if($action == "EDIT"){
			$sql_prefix = "UPDATE `".$tbl."` SET";
			$sql_suffix = "WHERE `id` = '$id'";
			$mail_alert_msg = "EDIT";
			$activity = " - Location ".$_POST['fld4']." edited";
		}
		if($action == "DELETE"){
			$sql_prefix = "DELETE FROM `".$tbl."`";
			$sql_suffix = "WHERE `id` = '$id'";
			$mail_alert_msg = "EDIT";
			$activity = " - Location Deleted - ".$_POST['fld4'];
		}

		$_POST['fld1'] = str_replace(";",",",$_POST['fld1']);		
		$_POST['fld1'] = str_replace(", ",",",$_POST['fld1']);
		$_POST['fld1'] = str_replace("  "," ",$_POST['fld1']);
		$_POST['fld1'] = trim($_POST['fld1']);		
		$_POST['fld2'] = str_replace(";",",",$_POST['fld2']);		
		$_POST['fld2'] = str_replace(", ",",",$_POST['fld2']);		
		$_POST['fld2'] = str_replace("  "," ",$_POST['fld2']);
		$_POST['fld2'] = trim($_POST['fld2']);
		
		$sql_comm = "`fictional_location` = '".$_POST['fld1']."',	
					`real_location` = '".$_POST['fld2']."'";
	}

	if($type == "RR2WB"){
		$tbl = "ichange_waybill";
		$sql_g = "SELECT * FROM `".$tbl."` WHERE `waybill_num` = '$id'";
		$qry_g = mysql_query($sql_g);
		$res_g = mysql_fetch_array($qry_g);
		$route = $res_g['routing'];
		if(isset($_GET['rr'])){$route .= " (via ".$_GET['rr'].")";}
		$sql_prefix = "UPDATE `".$tbl."` SET";
		$sql_suffix = "WHERE `waybill_num` = '$id'";	
		$mail_alert_msg = "EDIT";
		$sql_comm = "`routing` = '$route'";	
	}
	
	$do_sql_query = $sql_prefix." ".$sql_comm." ".$sql_suffix;
	//echo "do_sql_query=".$do_sql_query; exit();
	if($action == "DELETE"){
		$do_sql_query = $sql_prefix." ".$sql_suffix;
	}
	$do_sql_query2 = $sql_comm2;
	
	if($type != "INVALID"){	
		if(mysql_query($do_sql_query)){
			if(strlen($id) > 0 || $action == "NEW"){
				$done =  "Updated Table ".strtoupper($tbl)." successfully. ";
				$log_activity = date('Y-m-d H:i:s')." - ".$activity;		
				$sql_act = "INSERT INTO `ichange_activity` SET `activity` = '".$log_activity."', `added` = '".date('U')."'";
				mysql_query($sql_act);
			}else{
				$done =  "Nothing to update. ";
			}
			if(strlen($do_sql_query2) > 0){
				mysql_query($do_sql_query2);
			}
			if($type == "WAYBILL" && $action != "DELETE"){
				$carsTmp = $cars;
				for($cn=0;$cn<count($carsTmp);$cn++){
					//$fld9_comp = str_replace(" ", "", $_POST['fld9']);
					$carsTmp[$cn]['NUM'] = str_replace(" ", "", $carsTmp[$cn]['NUM']);
					if(strlen($carsTmp[$cn]['NUM']) < 15){
						$cntr_cars = q_cntr("ichange_cars", "(`car_num` = '".$carsTmp[$cn]['NUM']."' OR `car_num` = '".$cars[$cn]['NUM']."')");
						if($cntr_cars == 0){
							mysql_query("INSERT INTO `ichange_cars` SET `car_num` = '".$carsTmp[$cn]['NUM']."', `aar_type` = '".$cars[$cn]['AAR']."', `rr` = '".$cars[$cn]['RR']."'");
							$done .= "<br />Car ".$carsTmp[$cn]['NUM']." added to CARS table successfully.";
						}
					}
				}
				$done .= "<br />";
			}
			@mysql_query("UPDATE `ichange_rr` SET `last_act` = '".date('U')."' WHERE `id` = '".$_COOKIE['rr_sess']."'");							
		}else{
			$done = "There was a problem updating the table ".strtoupper($tbl).".<br /><br />".$do_sql_query.".<br /><br />".mysql_error().".";
		}

	}else{
		$done = "There was not a complete record to update. Press the BACK button on your browser and complete all required fields, then submit again.<br /><br />";
	}

	if(isset($_POST['express'])){
		if($_POST['express'] == "Y"){
			$wid = qry("ichange_waybill", $_POST['fld8'], "waybill_num", "id");
			//$wid = $id;
			/*
			if(isset($_COOKIE['hde'])){
				$wid .= ",".$_COOKIE['hde'];
				$wid = str_replace("hdehde","hde",$wid);
			}		
			setcookie("hde", $wid, time()+(60*60*24));
			*/
			header("Location:index.php?hde=".$wid);
		}
	}
	
	if(strpos($goTo,"save.php") > 0){ $goTo = "";}
?>
<html>
<head>
		<link REL="stylesheet" HREF="style.css" TYPE="text/css" MEDIA="screen">
        <?php if(strlen($goTo) > 0){ ?><meta http-equiv="refresh" content="10;URL=<?php echo $goTo; ?>"><?php } ?>
</head>
<body>
	<table>
	<tr>
	<td>
	<?php include('menu.php'); ?>
	<?php echo $done; ?>
	<br /><br />

	<?php //if($type == "WAYBILL" || $type == "CARS" || $type == "PROGRESS"){
	if(($type == "WAYBILL" && $action == "NEW") || $type == "PROGRESS" || $email_wb == 1){
		if(strlen($mail_alert_msg) > 0 && $action != "DELETE" && strlen($id) > 0){
			include("mail2.php"); // As of 2011-08-04, send emails automatically for New Waybill & Progress updates
			//echo "To send an email to <strong>MRICC</strong> with the details of waybill ".$id.", click <a href=\"mail.php?action=".$mail_alert_msg."&id=".$id."\">Here</a>.<br /><br />";
		}
		// echo "You can add Cars or a Progreses Report to a Waybill by clicking the Home link below and clicking the <strong>Edit/View WB</strong> link next to the waybill you want to manage.<br />";
	} 
	if($type == "WAYBILL" && $action == "EDIT" && $email_wb != 1){
		echo "To send an email to <strong>MRICC</strong> with the details of waybill ".$id.", click <a href=\"mail.php?action=".$mail_alert_msg."&id=".$id."\">Here</a>.<br /><br />";
	}
	?>
	<br />
	<?php if($win != "Y"){ ?>
		<a href="index.php">Home</a> 
<?php
	if(isset($_COOKIE['LastAct'])){
	if(strlen($_COOKIE['LastAct']) > 0 && $type != "WAYBILL" && $type != "PROGRESS"){
?>
	<?php if($type != "BLOCKS" && $type != "RANDOMWB" && $type != "RR"){ ?>
	<a href="<?php echo $_COOKIE['LastAct']; ?>">Edit Another Record of the same type</a>
	<?php } ?>
	<?php if($type == "BLOCKS"){ ?>
	<a href="edit.php?type=BLOCKS&action=NEW">Edit Another Record of the same type</a>
	<?php } ?>
<?php
	}
	}
?>
    <?php if(strlen($goTo) > 0){ ?><br /><br />If you do not click a link on this page you will be redirected to <br /><a href="<?php echo $goTo; ?>"><strong><?php echo $goTo; ?></strong></a><br />shortly.<?php } ?>
	<?php }else{ ?>
		<a href="#" onClick="window.close()">Close Window</a>
	<?php } ?>
	<div style="width: 100%; text-align: right; font-size: 9pt;">Last Modified: <?php echo date('Y-m-d H:i',filemtime("save.php")); ?></div>
	</td>
	</tr>
	</table>
</body>
</html>
<?php
	mysql_close();
?>