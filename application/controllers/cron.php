<?php
class Cron extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
	}

	public function index(){
		echo "Cron controller. Nothing to see here!";
	}
	
	public function isAdminViable(){
		$viable = (array)$this->Generic_model->qry("SELECT `contact_email`,`last_act` FROM `ichange_rr` WHERE `admin_flag` = '1' ORDER BY `id`");
		$days_ago = 1; // 14 - IF LESS THAN 7 WILL ASSUME TESTING AND SEND TO ADMIN'S EMAIL INSTEAD OF GROUP.
		$days_to_VI = 1; // 7 IS BEST DEFAULT
		$email_to = "";
		$subj = "Admin please respond to MRICF alert";
		$trigger_ts = date('U')-(60*60*24*$days_ago); // Days ago of last act to trigger alert to admin user.
		$alertVI_ts = date('U')-(60*60*24*intval($days_ago+$days_to_VI)); // Days ago of last act to trigger message to virtual_ops group.
		for($i=0;$i<count($viable);$i++){
			if(strlen($viable[$i]->contact_email) > 0 && $trigger_ts > $viable[$i]->last_act){
				$mess = "If has been ".$days_ago." since you last logged into the MRICF. Please go to the MRICF and do something to update the Last Activity (log in should do).\n\nIf no response is received within ".$days_to_VI." days of this message, an email message will be sent to the virtual_ops group to activate contingency plan.";
				$email_to = $viable[$i]->contact_email;
				mail($email_to,$subj,$mess);
				$i = count($viable)+1;
			}elseif(strlen($viable[$i]->contact_email) > 0 && $alertVI_ts > $viable[$i]->last_act){
				$mess = "If has been ".intval($days_ago+$days_to_VI)." since the MRICF Admin user last logged into the MRICF even after repeated automated requests to do so. This may be because he/she is away and not using the MRICF for a time or it could indicate that the MRICF Admin is not viable. Please activate backup contingency plan, and attempt manual contact of MRICF Admin by email ".$viable[$i]->contact_email." . If the MRICF Admin does not respond within a week, you should go to ".WEB_ROOT."/index.php/backup and follow instructions there to create a backup server and database.";
				$email_to = "virtual_ops@yahoogroups.com";
				if($days_ago < 7){ $email_to = $viable[$i]->contact_email; }
				mail($email_to,$subj,$mess);
				$i = count($viable)+1;
				
			}
		}
		echo "Message: ".$mess."<br />has been sent to ".$email_to
	}
	
}
?>