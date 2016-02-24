<?php
	$id = "";
	$action = "";
	if(isset($_GET['id'])){$id = $_GET['id'];}	
	if(isset($_POST['id'])){$id = $_POST['id'];}	
	if(isset($_GET['action'])){$action = $_GET['action'];}
	if(isset($_POST['action'])){$action = $_POST['action'];}
	
	$mail_alert_msg = "";	
	$email_sent="";
	/*
	//$email = "james@stanfordhosting.net";
	$email = "MRICC@yahoogroups.com";
	$subject = "Waybill Generated or Changed";
	//$headers = "From: jimsmodeltrains@yahoo.com.au";
	$headers = "From: mricf@stanfordhosting.net";
	*/
	$render_fld = "";
	$url = "http://www.stanfordhosting.net/interchangecars";

	
	if($action == "MESSAGE"){
		$subject = $_POST['subject'];
		$mail_alert_msg = "From: ".$_POST['rr']."\n".$_POST['mess'];
	}

	if(strlen($id) > 0 && strlen($action) > 0){
		// Send  message about a waybill	
		if($action == "NEW"){
			$mail_alert_msg = "A NEW WAYBILL HAS BEEN ADDED TO THE ONLINE WAYBILL MANAGEMENT SYSTEM AT ".$url.":\n\n";
			$subject = "NEW WAYBILL ".$id." GENERATED";
		}		
		if($action == "EDIT"){
			$mail_alert_msg = "WAYBILL ".$id." HAS BEEN CHANGED OR UPDATED ON THE ONLINE WAYBILL MANAGEMENT SYSTEM AT ".$url." \n\n";
			$subject = "WAYBILL ".$id." CHANGED / UPDATED";
		}		
		
		include('db_connect7465.php');
		include('vars.php');
		include('classes/mail.class.php');
		if($action != "MESSAGE"){
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
			$render_fld .= "Return to: ".$fld19."\n";
			$render_fld .= "Routing: ".$fld6."\n";
			$render_fld .= "Status: ".$fld7b."\n";
			$render_fld .= "Car: ".$fld9." ";
			$render_fld .= "AAR Code: ".$fld10."\n";
			$render_fld .= "Alias: ".$fld12." ";
			$render_fld .= "Alias AAR Code: ".$fld13."\n";
			$render_fld .= "Lading: ".$fld11."\n\n";
		
			$render_waybill = $render_fld;
			
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

			if(strlen($render_lst_progress) < 20){
				$render_lst_progress .= "\nNO REPORTED ACTIVITY YET.";
			}
		
			$render_lst_progress .= "\n\n";
		
			// $mailbody = $mail_alert_msg.$render_waybill.$render_lst_cars.$render_lst_progress;	
			$mailbody = $mail_alert_msg.$render_waybill.$render_lst_progress;
			if($action == "MESSAGE"){
				$mailbody = $mail_alert_msg;
			}

			//echo $mailbody;
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
		
	}

	if($action == "MESSAGE"){
		// Send a general message.
		//echo $mailbody;
		$mailbody = "From: ".$_POST['rr']."\n".$_POST['mess'];
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
<html>
<head>
	<link REL="stylesheet" HREF="style.css" TYPE="text/css" MEDIA="screen">
</head>
<body>
<table>
<tr>
<td>
<?php echo $email_sent; ?>
<?php if($action == "MESSAGE"){ ?>
<a href="#" onClick="window.close()">Close Window</a>
<?php }else{ ?>
<a href="index.php">Home</a>
<?php }?>
</td>
</tr>
</table>
</body>
</html>
<?php
	@mysql_close();
?>