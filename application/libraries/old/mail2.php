<?php
	//$id = "";
	//$action = "";
	//if(isset($_GET['id'])){$id = $_GET['id'];}	
	//if(isset($_POST['id'])){$id = $_POST['id'];}	
	//if(isset($_GET['action'])){$action = $_GET['action'];}
	//if(isset($_POST['action'])){$action = $_POST['action'];}
	
	// INCLUDED BY save.php !
	
	//$mail_alert_msg = "";	
	$email_sent="";
	/*
	//$email = "james@stanfordhosting.net";
	$email = "MRICC@yahoogroups.com";
	$subject = "Waybill Generated or Changed";
	//$headers = "From: jimsmodeltrains@yahoo.com.au";
	$headers = "From: mricf@stanfordhosting.net";
	*/
	include('classes/mail.class.php');
	$render_fld = "";
	$url = "http://www.stanfordhosting.net/interchangecars";

	if(strlen($id) > 0 && strlen($action) > 0){
		// Send  message about a waybill	
		if($action == "NEW" && $type == "WAYBILL"){
			$mail_alert_msg = "A NEW WAYBILL HAS BEEN ADDED TO THE ONLINE WAYBILL MANAGEMENT SYSTEM AT ".$url.":\n\n";
			$subject = "NEW WAYBILL ".$id." GENERATED";
		}
		if($action == "EDIT" && $type == "WAYBILL"){
			$mail_alert_msg = "WAYBILL ".$id." HAS BEEN CHANGED OR UPDATED ON THE ONLINE WAYBILL MANAGEMENT SYSTEM AT ".$url." \n\n";
			$subject = "WAYBILL ".$id." CHANGED / UPDATED";
		}
		
		if($type == "PROGRESS"){
			$mail_alert_msg = "WAYBILL ".$id." PROGRESS HAS BEEN UPDATED ON THE ONLINE WAYBILL MANAGEMENT SYSTEM AT ".$url." \n\n";
			$subject = "PROGRESS ON WAYBILL ".$id." UPDATED";
		}
		
		//$wbTyp = qry($tbl, $id, 'waybill_num', "waybill_type");
		$wbTyp = "";
		if($type == "WAYBILL" || $type == "PROGRESS"){
			$wbTyp = qry("ichange_waybill", $id, 'waybill_num', "waybill_type");
		}
		
		//if(($type == "WAYBILL" && $action == "NEW" && $wbTyp != "INTERNAL") || ($type == "PROGRESS" && $wbTyp != "INTERNAL")){
		if($type == "WAYBILL"){
			//include('db_connect7465.php');
			//include('vars.php');
			include('waybill_query.php');
			$sql2 = "SELECT * FROM `ichange_rr` WHERE `id` = '".$fld2."'";
			$dosql2 = mysql_query($sql2);
			$result2 = mysql_fetch_array($dosql2);
	
			$fld1_1 = $result2['rr_name'];

			$sql3 = "SELECT * FROM `ichange_rr` WHERE `id` = '".$fld3."'";
			$dosql3 = mysql_query($sql3);
			$result3 = mysql_fetch_array($dosql3);

			$fld1_2 = $result3['rr_name'];
		
			$render_fld .= "Waybill: ".$fld8."\n";
			$render_fld .= "Date: ".$fld1."\n";
			$render_fld .= "Originating RR: ".$fld1_1."\n";
			$render_fld .= "Destination RR: ".$fld1_2."\n";
			$render_fld .= "Originating Industry: ".$fld4."\n";
			$render_fld .= "Destination Industry: ".$fld5."\n";
			$render_fld .= "Routing: ".$fld6."\n";
			$render_fld .= "Status: ".$fld7b."\n";
			$render_fld .= "Cars:\n";
			for($cn=0;$cn<count($fld21);$cn++){
				if(strlen($fld21[$cn]['NUM']) > 0){$render_fld .= $fld21[$cn]['NUM']." (".$fld21[$cn]['AAR'].")\n";}
			}
			/*
			$render_fld .= "Car: ".$fld9." (".$fld10.")\n";
			//$render_fld .= "AAR Code: ".$fld10."\n";
			$render_fld .= "Alias: ".$fld12." (".$fld13.")\n";
			//$render_fld .= "Alias AAR Code: ".$fld13."\n";
			*/
			$render_fld .= "Lading: ".$fld11."\n\n";
		}
	
		$render_waybill = $render_fld;
		
		$render_lst_progress = "";
		//if($type == "PROGRESS"){
			/*
			$sql2 = "SELECT * FROM `ichange_progress` WHERE `waybill_num` = '".$id."' ORDER BY `date` DESC, `id` DESC LIMIT 3";
			$dosql2 = mysql_query($sql2);
			$render_lst_progress = "-- ACTIVITY --\n";
			$fld2_1 = $id;
			while($result = mysql_fetch_array($dosql2)){
			
				$progress_id = $result['id'];
				$fldprogress_2 = $result['date'];
				$fldprogress_3 = $result['text'];

				$render_lst_progress .= $progress_id." -- ";
				$render_lst_progress .= $fldprogress_2." - ";
				$render_lst_progress .= strip_tags($fldprogress_3)."\n";
			}
			*/
			$render_lst_progress = "-- ACTIVITY --\n";
			$last_prog = count($prog);
			$cntr_prog=0;
			while($cntr_prog < $last_prog){
				if(!isset($prog[$cntr_prog]['id'])){$prog[$cntr_prog]['id'] = "";}
				$render_lst_progress .= $prog[$cntr_prog]['id']." -- ";
				$render_lst_progress .= $prog[$cntr_prog]['date']." - ";
				$render_lst_progress .= strip_tags($prog[$cntr_prog]['text'])."\n";	
				$cntr_prog++;		
			}
		//}

		if(strlen($render_lst_progress) < 20){
			$render_lst_progress .= "\nNO REPORTED ACTIVITY YET.";
		}
	
		$render_lst_progress .= "\n\n";
	
		// $mailbody = $mail_alert_msg.$render_waybill.$render_lst_cars.$render_lst_progress;	
		$mailbody = $mail_alert_msg.$render_waybill.$render_lst_progress;
		if($action == "MESSAGE"){
			$mailbody = $mail_alert_msg;
		}
		/*
		$email_sent="There was a problem sending the email.<br /><br />".$mailbody;
		if(mail($email, $subject, $mailbody, $headers)){
			$email_sent = "<h3>An email has been sent to ".$email." regarding Waybill ".$id.".</h3>";
		}
		*/
		$mail = new mailSender();
		$mail->setSubject($subject);
		$mail->setBody($mailbody);
		$mail->send();
	}

?>