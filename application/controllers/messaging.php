<?php
class Messaging extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('mricf');
		
		$this->load->model('Waybill_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Railroad_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Messaging";
		$this->arr['html'] = "";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

		$rrArrTmp = $this->mricf->rrFullArr();
		$rrArrTmp_kys = array_keys($rrArrTmp);
		for($r=0;$r<count(array_keys($rrArrTmp_kys));$r++){$this->arr[$rrArrTmp_kys[$r]] = $rrArrTmp[$rrArrTmp_kys[$r]];}
		$this->my_rr_ids = $this->mricf->affil_ids($this->arr['rr_sess'],$this->arr['allRR']);

		// ONLY NEEDED TO CREATE messages TABLE AND DATA! ONCE DONE, CAN BE REMOVED. 2017-05-21
		$tbls = (array)$this->Generic_model->qry("SHOW TABLES WHERE Tables_in_jstan2_general LIKE 'ichange_messages'");
		if(count($tbls) == 0){ $this->createMsgTbl(); }
	}

	public function index(){
		$this->pos = @$_POST;
		$this->lst();
	}
	
	public function lst($id=0){
		//$wbdat = (array)$this->Waybill_model->get_messages($id);
		$messdat = (array)$this->Waybill_model->get_messages($id);
		//$messdat = @json_decode($wbdat[0]->messages,TRUE);
		$this->arr['pgTitle'] .= " - Messages for ".$messdat[0]->waybill_num; //$wbdat[0]->waybill_num;
		$this->id = $id;
		$randpos = array();
		//$this->dat = array();
		// [{"datetime":"2012-11-09 08:46","read":{"40":1,"3":1},"rr":"40","torr":"3","subject":"SUBJECT","text":"MESSAGE TEXT.","origmess":""}]
		$this->datl['fields'] 			= array('datetime', 'rr', 'torr', 'subject', 'text', 'read');
		$this->datl['field_names'] 		= array("Date/Time", "From", "To", "Subject", "Message", "Acknowledged");
		$this->datl['options']			= array();
		/*
				'Edit' => "messaging/read/"
			); // Paths to options method, with trailling slash!
		*/
		$this->dat['no_delete_form'] = 1;
		$this->dat['links']				= array();
		/*
				'New' => "indust/edit/0"
			); // Paths for other links!
		*/
		//echo "<pre>"; print_r($this->my_rr_ids); echo "</pre>";
		for($i=0;$i<count($messdat);$i++){
			$messdat[$i] = (array)$messdat[$i];
			$this->datl['data'][$i]['id'] 					= $messdat[$i]['id'];
			$this->datl['data'][$i]['datetime']			= $messdat[$i]['datetime'];
			$this->datl['data'][$i]['rr']	 				= $this->mricf->qry("ichange_rr", $messdat[$i]['rr'], "id", "report_mark");
			$this->datl['data'][$i]['torr'] 				= $this->mricf->qry("ichange_rr", $messdat[$i]['torr'], "id", "report_mark");
			$this->datl['data'][$i]['subject'] 			= $messdat[$i]['subject'];
			$this->datl['data'][$i]['text']					= $messdat[$i]['text'];
			$this->datl['data'][$i]['read']					= "";
			if($messdat[$i]['ack'] != 1 && in_array($messdat[$i]['torr'],$this->my_rr_ids)){
				$this->datl['data'][$i]['read']	 = "<a href=\"".WEB_ROOT."/index.php/messaging/ack/".$messdat[$i]['id']."\">Acknowledge</a>";
			}elseif($messdat[$i]['ack'] == 1 && in_array($messdat[$i]['torr'],$this->my_rr_ids)){
				$this->datl['data'][$i]['read']	 = "Yes";
			}
		}

		// Selector to move car to wherever.
		$this->setFieldSpecs(); // Set field specs
		for($i=0;$i<count($this->field_defs);$i++){
			$this->dat['field_names'][$i] = $this->field_defs[$i]['label'];
			if($this->field_defs[$i]['type'] == "checkbox"){
				$this->dat['fields'][$i] = form_checkbox($this->field_defs[$i]['def']).$this->dat['field_names'][$i];
				$this->dat['field_names'][$i] = "";
			}
			if($this->field_defs[$i]['type'] == "input"){$this->dat['fields'][$i] = "<br />".form_input($this->field_defs[$i]['def']);}
			if($this->field_defs[$i]['type'] == "textarea"){$this->dat['fields'][$i] = "<br />".form_textarea($this->field_defs[$i]['def']);}
			if($this->field_defs[$i]['type'] == "select"){$this->dat['fields'][$i] = "<br />".form_dropdown($this->field_defs[$i]['name'],$this->field_defs[$i]['options'],$this->field_defs[$i]['value'],$this->field_defs[$i]['other']);}
			if($this->field_defs[$i]['type'] == "statictext"){$this->dat['fields'][$i] = "<br />".$this->field_defs[$i]['value'];}
		}

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		//$this->load->view('edit', @$this->flddat);
		if($this->arr['rr_sess'] > 0){
			$this->load->view('edit', $this->dat);
			$this->load->view('list', $this->datl);
		}else{
			$this->load->view('not_allowed');
		}
		$this->load->view('footer');
	}
	
	public function edit($id=0){
		// Used for editing existing (edit/[id]) and adding new (edit/0) records
	}
	
	public function ack($id=0){
		// Mark message with $id sent to railroad id $rr as acknowledged.
		$sql = "UPDATE ichange_messages SET ack = 1 WHERE id = '".$id."'";
		$this->Generic_model->change($sql);
		header("Location:".WEB_ROOT."/index.php/messaging");
	}
	
	public function setFieldSpecs(){
		// Sets specific field definitions for the controller being used.
		$this->dat['fields'] = array();
		
		// Add custom model calls / queries under this line...
		//$this->load->model('Aar_model', '', TRUE);
		$this->load->model('Railroad_model', '', TRUE);
		
		// Add other code for fields under this line...
		/*
		$aar_opts = array();
		$aar_tmp = (array)$this->Aar_model->get_allSorted();
		for($i=0;$i<count($aar_tmp);$i++){$aar_opts[$aar_tmp[$i]->aar_code] = $aar_tmp[$i]->aar_code." - ".substr($aar_tmp[$i]->desc,0,70);}
		*/
		
		$rr_opts = array();
		$rr_tmp = (array)$this->Railroad_model->get_allActive();
		for($i=0;$i<count($rr_tmp);$i++){$rr_opts[$rr_tmp[$i]->id] = $rr_tmp[$i]->report_mark." - ".substr($rr_tmp[$i]->rr_name,0,70);}
		
		// Add form and field definitions specific to this controller under this line... 
		$this->dat['hidden'] = array('rr' => $this->arr['rr_sess'], 'waybill_id' => $this->id); //'tbl' => 'indust', 'id' => @$this->dat['data'][0]->id);
		$this->dat['form_url'] = "../messaging/send_it";
		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Message Type', 'name' => 'type', 'value' => "", 
			'other' => 'id="type"', 'options' => array(0 => "Message", 1 => "Email")
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'To RR', 'name' => 'torr', 'value' => "", 
			'other' => 'id="torr"', 'options' => $rr_opts
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Subject', 'def' => array(
              'name'        => 'subject',
              'id'          => 'subject',
              'value'       => "",
              'maxlength'   => '60',
              'size'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Message', 'def' => array(
              'name'        => 'text',
              'id'          => 'text',
              'value'       => "",
              'rows'			 => '5',
              'cols'        => '50'
			)
		);

		/*
		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'AAR Type', 'name' => 'aar_type', 'value' => @$this->dat['data'][0]->aar_type, 
			'other' => 'id="aar_type"', 'options' => $aar_opts
		);
		*/

		/*
		$this->field_defs[] =  array(
			'type' => "statictext", 'label' => '<br />Freight Out Auto Generation',
			'value' => '<div style="border: 1px solid red; background-color: yellow; font-size: 9pt; padding: 5px;">To allow Generated Loads for a Commodity, the values in the Freight Out MUST be comma (,) separated and match exactly a semi-colon (;) separated value in the Commodities: Generates these Commods field.</div>'
		);
		*/


	}
	
	// Message sending methods
	function send_it(){
		// $id is id from ichange_waybill
		$id = $_POST['id'];
		if($_POST['type'] == 0){$this->send_message($id);}
		if($_POST['type'] == 1){$this->send_email($id);}
	}

	function send_message($id){
		// Sends message to be stored with Waybill in messages field
		$wbdat = (array)$this->Waybill_model->get_messages($id);
		$messages = @json_decode($wbdat[0]->messages,TRUE);
		$tmp_arr = $_POST;
		$tmp_kys = array_keys($tmp_arr);
		for($z=0;$z<count($tmp_kys);$z++){ $tmp_arr[$tmp_kys[$z]] = str_replace(array("'","`","\""),"",$tmp_arr[$tmp_kys[$z]]); }
		echo "<pre>"; print_r($tmp_arr); echo "</pre>"; //exit();
		$tmp_arr['datetime'] = date('Y-m-d H:i');
		$messages[] = $tmp_arr;
		//echo "<pre>"; print_r($messages); echo "</pre>"; //exit();
		//$sql = "UPDATE `ichange_waybill` SET `messages` = '".json_encode($messages)."' WHERE `id` = '".$id."'";
		$sql = "INSERT INTO `ichange_messages` SET 
			`waybill_id` = '".$tmp_arr['waybill_id']."', 
			`rr` = '".$tmp_arr['rr']."', 
			`type` = '0', 
			`torr` = '".$tmp_arr['torr']."', 
			`subject` = '".$tmp_arr['subject']."', 
			`text` = '".$tmp_arr['text']."', 
			`datetime` = '".date('Y-m-d H:i:s')."'";
		$this->Generic_model->change($sql);
		//echo $sql; exit();
		header("Location:../messaging/lst/".$id);
		exit();
	}
	
	function send_email($id){
		// Sends email to virtual_ops group.

		$subject = "MESSAGE FROM ".$this->mricf->qry("ichange_rr", $_POST['rr'], "id", "report_mark")." TO ".$this->mricf->qry("ichange_rr", $_POST['torr'], "id", "report_mark"); 
		$message = "SUBJECT: ".$_POST['subject']."\n";
		$message .= "--------------------------------\n";
		$message .= "MESSAGE:\n".$_POST['text']."\n";
		$message .= "--------------------------------\n";
		$message .= "Sent via MRICF V2.0 emailer";

		$this->load->library('email');		
		$this->email->from('mricf@stanfordhosting.net', 'MRICF');
		$this->email->to('virtual_ops@yahoogroups.com');
		$this->email->subject($subject);
		$this->email->message($message);
		if($this->email->send()){$this->arr['html'] = "<h2>Message emailled to virtual_ops group successfully</h2>";}
		else{$this->arr['html'] = "<h2>Message was not emailled due to an error.</h2>";}

		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		$this->load->view('html',$this->arr);
		$this->load->view('footer');
		
		
		//header("Location:../messaging/success/".$id);
		//exit();
	}
	
	function createMsgTbl(){
		// Creates ichange_messages table, and imports data from ichange_waybills.messages field.
		// CAN BE REMOVED AFTER FIRST RUN ON LIVE SERVER. 2017-05-21
		
		// Create table.
		$sql = "CREATE TABLE IF NOT EXISTS `ichange_messages` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `waybill_id` int(11) NOT NULL COMMENT 'ichange_waybills.id value. Was id in old JSON object',
		  `rr` int(11) NOT NULL COMMENT 'Railroad ID if railroad se4nding message',
		  `type` tinyint(3) NOT NULL,
		  `torr` int(11) NOT NULL COMMENT 'Railroad ID to send message to',
		  `subject` varchar(70) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Subject of message',
		  `text` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Message text',
		  `ack` tinyint(3) NOT NULL COMMENT '0 = not acknowledged, 1 = acknowledged',
		  `datetime` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Date Time in YYYY-MM-DD HH:MM:SS',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Holds Waybill Messages - replaced JSON object in ichange_waybills.messages field' AUTO_INCREMENT=1";
		$this->Generic_model->change($sql);
		
		// Create messages array from ichange_waybill table data
		$sql = "SELECT id, messages FROM ichange_waybill WHERE LENGTH(messages) > 2";
		$msgs = (array)$this->Generic_model->qry($sql);
		$msg_arr = array();
		for($z=0;$z<count($msgs);$z++){
			$tmp = json_decode($msgs[$z]->messages,TRUE);
			for($t=0;$t<count($tmp);$t++){
				$tmp2 = $tmp[$t]; 
				$tmp2['waybill_id'] = $msgs[$z]->id;
				unset($tmp2['id']);
				unset($tmp2['submit']);
				$msg_arr[] = $tmp2;
			}
		}
		//echo "<pre>"; print_r($msg_arr); echo "</pre>";
		
		// Create records in ichange_messages table
		for($m=0;$m<count($msg_arr);$m++){
			$sql = "";
			$m_kys = array_keys($msg_arr[$m]);
			for($mk=0;$mk<count($m_kys);$mk++){
				if($mk > 0){$sql .= ", ";}
				$sql .= $m_kys[$mk]." = '".$msg_arr[$m][$m_kys[$mk]]."'";
			}
			$sql = "INSERT INTO ichange_messages SET ".$sql;
			//echo $sql."<br />";
			$this->Generic_model->change($sql);
		}
		
		//exit();
		echo "Messaging system has been converted from JSON to Table data format. <a href=\"".WEB_ROOT."/index.php/home\">Click here to continue.";
		exit();
	}
	
}
?>