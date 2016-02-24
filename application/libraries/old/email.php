<?php
	// Main email creation file
	// THIS FILE CAN BE CUSTOMISED, SO **DONT** COPY TO OTHER FOLDERS THAT USE TARE. 
	// Only use the TARE as the STARTING POINT for the application.
	// Included by other parts of the application.
	$snd2 = array();
	$snd2[] = $email_to;
	echo $message."<br />";
	if(strpos($email_to,",") > 0){$snd2 = explode(",",$email_to);}
	for($chewbaka=0;$chewbaka<count($snd2);$chewbaka++){
		if(mail($snd2[$chewbaka],"Email from ".$title." Application", str_replace("<br />","\n",$message), $headers)){
			$mess .= "Email sent to: ".$snd2[$chewbaka].". Email sent from: ".$email_from."<br />";
		}
	}
?>
