<?php
	// This file holds Session handling / creating routines.	
	// @session_start();

	include('db_connect7465.php');
	$goTo = "index.php";
	$url_qry = "";
	$sess_rr = "";
	if(isset($_GET['rr_sess']) && isset($_COOKIE['rr_sess'])){
		setcookie("rr_sess", $_GET['rr_sess'], time()+3600*24);
		header("Location: index.php");
	}
	$rr_cook_id = 0;
	$rr_fldname = "rr";
	if(isset($type)){
		if($type == "TRAINS"){$rr_fldname = "railroad_id";}
	}

	/*
	if(isset($_SESSION['rr_sess'])){
		$rr = " AND (`rr_id_from` = '".$_SESSION['rr_sess']."' OR `rr_id_to` = '".$_SESSION['rr_sess']."') ";
		$sess_rr = "WHERE `".$rr_fldname."` = 0 OR `".$rr_fldname."` = '".$_SESSION['rr_sess']."' "; 
	}
	*/
	if(isset($_COOKIE['rr_sess'])){
		$rr_cook_id = $_COOKIE['rr_sess'];
		$sql_mark = "SELECT `affiliates`,`report_mark` FROM `ichange_rr` WHERE `id` = '".$_COOKIE['rr_sess']."'";
		$qry_mark = mysql_query($sql_mark);
		$res_mark = mysql_fetch_array($qry_mark);
		$rr_mark_4_wb = " OR `routing` LIKE '%%".$res_mark['report_mark']."%%'";
		$rr =     " AND (`rr_id_from` = '".$_COOKIE['rr_sess']."' OR `rr_id_to` = '".$_COOKIE['rr_sess']."' OR `rr_id_handling` = '".$_COOKIE['rr_sess']."'".$rr_mark_4_wb.") ";
		$rr_not = " AND (`rr_id_from` != '".$_COOKIE['rr_sess']."' AND `rr_id_to` != '".$_COOKIE['rr_sess']."' AND `routing` NOT LIKE '%%".$res_mark['report_mark']."%%') ";
		//$rr_not = ""; //" AND (`rr_id_from` != '".$_COOKIE['rr_sess']."' AND `rr_id_to` != '".$_COOKIE['rr_sess']."') ";
		$sess_rr = "WHERE `".$rr_fldname."` = 0 OR `".$rr_fldname."` = '".$_COOKIE['rr_sess']."' "; 
	}

	$con = "";	
	$url_qry = "";
	if(isset($_POST['logout']) || isset($_GET['logout'])){
		// session_destroy();
		setcookie("rr_sess", $_POST['rr_selected'], time()-120); // Expires RIGHT NOW!
		if(isset($_COOKIE['rr_admin'])){setcookie("rr_admin", "AyRa1lR0adA4m1n", time()-120);}
		$con = "1";
	}

	// Login form submitted test and actions	
	if(isset($_POST['rr_selected'])){
		// Check for Admin flag set
		$ps = "SELECT `admin_flag` FROM `ichange_rr` WHERE `id` = '".$_POST['rr_selected']."' AND `pw` = '".$_POST['p_word']."'";
		$pq = mysql_query($ps);
		$pr = mysql_fetch_array($pq);
		$prows = mysql_num_rows($pq);
		$yay = 0;
		if($prows > 0){
			$yay = 1;
			if($pr['admin_flag'] == 1){setcookie("rr_admin", "AyRa1lR0adA4m1n", time()+3600*24*7);}
		}
		if($_POST['rr_selected'] == 9999){
			$is = "SELECT `value` FROM `ichange_parameters` WHERE `param_name` = 'new_user_pass'";
			$iq = mysql_query($is);
			$ir = mysql_fetch_array($iq);
			if($_POST['p_word'] == $ir['value']){
				$new_pw_str = "abcdefghjkmnpqrstuvwxyz23456789";
				$new_pw = "";
				$nupd = date('Y-m-d H:i');
				for($p=0;$p<7;$p++){$new_pw .= $new_pw_str[rand(1,strlen($new_pw_str))];}
				mysql_query("UPDATE `ichange_parameters` SET `value` = '".$new_pw."' WHERE `param_name` = 'new_user_pass'");
				mysql_query("UPDATE `ichange_parameters` SET `value` = '".$nupd."' WHERE `param_name` = 'new_user_pass_date'");
				$email = "MRICC@yahoogroups.com";
				$subject = "NEW RR PASSWORD ".$nupd;
				$headers = "From: mricf@stanfordhosting.net";
				$mailbody = "The (New RR) password has changed.\n\nIt is now: ".$new_pw."\n";
				mail($email, $subject, $mailbody, $headers);
				$yay = 1;
			}
		}
		if($yay == 1){
			// $_SESSION['rr_sess'] = $_POST['rr_selected'];
			setcookie("rr_sess", $_POST['rr_selected'], time()+3600*24*7); // Expires in 7 days
		}else{$url_qry .= "loginerr=1";}
		$con = "1";

		// Page to go to on successful login
		$goTo = $_POST['goTo'];
		$goToQry = $_POST['goToQry'];
		if(strlen($goToQry) > 0){
			if(strlen($url_qry) > 0){$url_qry .= "&";}
			$url_qry .= $goToQry;
		}
	}
		
	// Final actions for this file.
	if(strlen($url_qry) > 0){$url_qry = "?".$url_qry;}
	mysql_close();
	if($con > 0){header("Location:".$goTo.$url_qry);}
	
?>