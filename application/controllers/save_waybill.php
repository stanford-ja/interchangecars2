<?php
class Save_waybill extends CI_Controller {

	var $whr = "";
	
	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->library('mricf');
		//$this->load->library('mailer');
		$this->load->library('email');

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		
		// Security
		if(!$this->input->cookie('rr_sess')){echo "You are not logged in or the session  has expired."; exit();}
		
		// Load generic model for custom queries
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
		
		// Railroad array set up
		$this->load->model('Railroad_model','',TRUE); // Database connection! TRUE means connect to db.
		
		// Update last_act field in ichange_rr table for logged in rr.
		$this->last_act_update();
	}

	public function index(){
		$this->arr = $_POST;
		$this->load->model('Generic_model','',TRUE);
		$this->var_build();
		$this->qry_build();
		//echo $this->sql."<br />";
		$this->Generic_model->change($this->sql);
		if($this->arr['id'] == 0 || isset($this->email_wb)){
			$this->email_wb_to_grp();
		}
		$wb_id = $this->arr['id']; if($wb_id < 1){ $wb_id = $this->Generic_model->db->insert_id(); }
		$this->dat['htm'] = "<strong>The waybill record has been saved. Click a link below, or a menu link above.</strong><br /><br />";
		$this->dat['htm'] .= anchor('../home',"Home");
		if(strlen($this->arr['goTo']) && strpos("z".$this->arr['goTo'],"acquire") < 1){$this->dat['htm'] .= "<br /><a href=\"".$this->arr['goTo']."\">Return to ".$this->arr['goTo']."</a>";}
		//header('Location:home');
		
		if(isset($_POST['addXtraAutos']) && $_POST['addXtraAutos'] == 1){
			header("Location:".WEB_ROOT."/waybill/addAutoTrain/".$wb_id);
		}else{
			$this->arr['pgTitle'] = "MRICF - Model Rail Interchangecars Facility - Waybill Saved";
			$this->arr['rr_sess'] = 0;
			if(isset($_POST['express'])){ $this->arr['redirect'] = $this->arr['goTo']; }
			if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}
			$this->load->view('header', $this->arr);
			$this->load->view('menu', $this->arr);
			$this->load->view('save', $this->dat);
			$this->load->view('footer');
		}
	}
	
	function var_build(){
		// Builds variable values in prep for query build
		
		// Start From interchangecars/inc/waybill_save.php
		$flds_arr = array("","date","rr_id_from","rr_id_to","indust_origin_name","indust_dest_name","routing","status","waybill_num","car_num","car_aar","lading","alias_num","alias_aar","train_id","po","waybill_type","notes","rr_id_handling","return_to");
		$prog = array(); //$this->mricf->progWB($this->arr['id']); - COMMENTED OUT 2016-03-02 JS
		$last_prog = count($prog)-1;
		$returnToTmp = $this->arr['fld19'];
		//if($action == "NEW"){
		if($this->arr['id'] == 0){
			$returnToTmp = $this->mricf->qry("ichange_cars", @$this->arr['fld9'], "car_num", "special_instruct");
			if(strpos($returnToTmp, "ETURN TO") > 0){$this->arr['fld19'] = str_replace("RETURN TO","",$returnToTmp);}
		}			
		
		// Insert record into ichange_auto and add to progress if allocated to an automatic train.		
		$d_qry = $this->mricf->qry("ichange_trains", $this->arr['fld14'][0], "train_id", "auto");
		$d_wps = json_decode($d_qry, true);
		//$fld14b = $this->arr['fld14'][0];
		if(strlen($this->arr['pfld3'][0]) > 0){
			$this->Generic_model->change("DELETE FROM `ichange_auto` WHERE `waybill_num` = '".$this->arr['fld8']."'");
			//$this->arr['fld14'][0] = "";
		}
		// if($d_qry != 0 || strlen($d_qry) > 4){ // || count($d_wps) > 0){
		if(($d_qry != 0 || strlen($d_qry) > 4) && strlen(str_replace(array("{","}","\"",":"),"",$this->arr['route_json'])) > 3){ // || count($d_wps) > 0){
			//$autoSav = array("autotrain" => 1, "entry_waypoint" => $this->arr['entry_waypoint'], "exit_waypoint" => $this->arr['exit_waypoint'], "train_id" => $this->arr['fld14'][0], "waybill_num" => $this->arr['fld8'], "waybill_date" => $this->arr['fld1']);
			$autoSav = array("autotrain" => 1, "route" => @json_decode($this->arr['route_json'],TRUE), "train_id" => $this->arr['fld14'][0], "waybill_num" => $this->arr['fld8'], "waybill_date" => $this->arr['fld1'], "rr_id" => $this->arr['setRRAutos']);
			$this->mricf->autoSav($autoSav); // insert record into ichange_auto, update ichange_waybill.progress.				//if($action != "NEW"){$this->arr['fld14'][0] = "AUTO TRAIN";}
			if($this->arr['id'] > 0){$this->arr['fld14'][0] = "AUTO TRAIN";}
		}else{
			for($ul=0;$ul<count($this->arr['fld7']);$ul++){
				if($this->arr['unload_days'][$ul] > 0 && $this->arr['fld7'][$ul] == "UNLOADING"){ // fld7 test added 2013-07-07 so that auto unload only activates for unload days if status is UNLOADING.
					if(strlen($this->arr['pfld6'][$ul]) < 1){$this->arr['pfld6'][$ul] = @$prog[$last_prog]['map_locaton'];}
					if(strlen($this->arr['pfld6'][$ul]) < 1 && $ul>0){$this->arr['pfld6'][$ul] = $this->arr['pfld6'][$ul-1];}
					$nxt_date = date('U') + (60*60*24*$this->arr['unload_days'][$ul]);
					$nxt_date = date('Y-m-d',$nxt_date);
					$description = "UNLOADED";
					$autoSav = array("unload" => 1, "waybill_date" => $nxt_date, "waybill_num" => $this->arr['pfld4'][0], "description" => $description, "exit_waypoint" => $this->arr['pfld6'][$ul]);
					$this->mricf->autoSav($autoSav); // insert record into ichange_auto, update ichange_waybill.progress.
					//$this->arr['pfld3'][$ul] = $this->arr['fld7'][$ul]." *AUTOMATIC UNLOADING WILL BE COMPLETE ON ".$nxt_date."*";
					$this->arr['pfld3'][$ul] .= " *AUTOMATIC UNLOADING WILL BE COMPLETE ON ".$nxt_date."*";
					$ul = count($this->arr['fld7']);
				}
			}
			if(strlen($this->arr['pfld3'][0]) > 0 && $this->arr['fld14'][0] == "AUTO TRAIN"){$this->arr['fld14'][0] = "";} // Remove 'AUTO TRAIN' if cancelling Auto entries!
		}	

		for($prc=0;$prc<count($this->arr['pfld6']);$prc++){
			$this->arr['pfld6'][$prc] = str_replace(";",",",$this->arr['pfld6'][$prc]);
			$this->arr['pfld6'][$prc] = str_replace(", ",",",$this->arr['pfld6'][$prc]);
			$this->arr['pfld6'][$prc] = str_replace("  "," ",$this->arr['pfld6'][$prc]);
			$this->arr['pfld6'][$prc] = trim($this->arr['pfld6'][$prc]);
		}

		// build progress report if not allocated to an AUTO TRAIN.
		$this->prog_sql = "";
		if($d_qry < 1 && strlen($this->arr['pfld3'][0]) > 0){
		//if($d_qry == 0 && strlen($this->arr['pfld3'][0]) > 0){
			$this->email_wb = 1;
			if(!isset($autoSav['train_id'])){$autoSav['train_id'] = "";}
			if(!isset($autoSav['entry_waypoint'])){$autoSav['entry_waypoint'] = "";}
			if(!isset($autoSav['exit_waypoint'])){$autoSav['exit_waypoint'] = @$this->arr['exit_waypoint'];}
			for($pz=0;$pz<count($this->arr['pfld4']);$pz++){
				$pfld7_tmp = $this->arr['pfld7'][$pz].":".$this->arr['pfld8'][$pz];
				//if($this->arr['pfld2'][$pz] == date('Y-m-d')){$pfld7_tmp = date('H:i');} // Dont know why I put this line in, so un-remark if it become apparent why!
				/* DISABLED 2016-03-04 AS NOW IN ichange_progress TABLE!
				if($pz >= count($this->arr['pfld4'])){ // NOW ONLY HAS *ONE* PROGRESS REPORT - THE LATEST ONE 
					$prog[] = array(
					'date' => $this->arr['pfld2'][$pz], 
					'time' => $pfld7_tmp, 
					'text' => strtoupper($this->arr['pfld3'][$pz]),
					'waybill_num' => $this->arr['pfld4'][$pz], 
					'map_location' => strtoupper($this->arr['pfld6'][$pz]), 
					'status' => strtoupper($this->arr['fld7'][$pz]), 
					'train' => str_replace("NOT ALLOCATED","",$this->arr['fld14'][$pz]), 
					'rr' => $this->arr['fld18'], 
					'exit_location' => strtoupper($autoSav['entry_waypoint']), 
					'tzone' => @$_COOKIE['_tz']
					);
				}
				*/

				// Added 2016-03-02 - The $prog[] creation above can be changed to single (ie, taken out of this FOR loop) after 2016-06-02				
				$prog_sql = "INSERT INTO `ichange_progress` SET 
					`date` = '".$this->arr['pfld2'][$pz]."', 
					`time` = '".$pfld7_tmp."', 
					`text` = '".strtoupper($this->arr['pfld3'][$pz])."', 
					`waybill_num` = '".$this->arr['pfld4'][$pz]."', 
					`map_location` = '".strtoupper($this->arr['pfld6'][$pz])."', 
					`status` = '".strtoupper($this->arr['fld7'][$pz])."', 
					`train` = '".str_replace("NOT ALLOCATED","",$this->arr['fld14'][$pz])."', 
					`rr` = '".$this->arr['fld18']."', 
					`exit_location` = '".strtoupper($autoSav['entry_waypoint'])."', 
					`tzone` = '".@$_COOKIE['_tz']."', 
					`added` = '".date('U')."'";
				$this->Generic_model->change($prog_sql);
			}
		}
		$jprog = json_encode($prog);
		$this->full_jprog = $jprog;
		
		if($d_qry < 1 && strlen($this->arr['pfld3'][0]) > 0){$this->prog_sql = ", `progress` = '".$jprog."'";}
		// End progress updating
		
		// Start Build JSON array for Cars
		$cars = json_decode($this->arr['fld21'], true);
		$car_found = 0;
		$aar_reqd = "";
		if(is_array($cars)){
			for($cn=0;$cn<count($cars);$cn++){
				//if($_COOKIE['rr_sess'] == $cars[$cn]['RR']){
				if($this->arr['fld21_rr'] == $cars[$cn]['RR']){
					$car_found++;
					/*
					$cars[$cn]['NUM'] = strtoupper($this->arr['fld21_car']);
					$cars[$cn]['AAR'] = strtoupper($this->arr['fld21_aar']);
					*/
				}
			}
		}
		if($car_found == 0){
			$cars[] = array(
				'AAR_REQD' => strtoupper($this->arr['fld10']),
				'NUM' => strtoupper($this->arr['fld21_car']),
				'AAR' => strtoupper($this->arr['fld21_aar']),
				'RR' => $this->arr['fld21_rr']
			);
			// $this->arr['fld21_rr'] was $_COOKIE['rr_sess']
		}

		// Start Update carsused_index table to reflext current cars allocated to the waybill
		$this->Generic_model->change("DELETE FROM `ichange_carsused_index` WHERE `waybill_num` = '".$this->arr['fld8']."'");
		for($ciu=0;$ciu<count($cars);$ciu++){
			$cui = "INSERT `ichange_carsused_index` SET 
				`car_num` = '".$cars[$ciu]['NUM']."', 
				`waybill_num` = '".$this->arr['fld8']."', 
				`rr` = '".$cars[$ciu]['RR']."', 
				`added` = '".date('U')."'";
			$this->Generic_model->change($cui);
		}
		// End Update carsused_index table

		$jcars = $this->arr['fld21']; //json_encode($cars);
		// End build JSON array for Cars
		
		// Query string for adding / updating ichange_waybill
		// Changed 2011-12-05
		$sql_comm = "";
		for($i=1;$i<count($flds_arr);$i++){
			if($i>1){$sql_comm .= ", ";}
			$t_arr_tmp = ""; if(isset($this->arr['fld'.$i])){ $t_arr_tmp = $this->arr['fld'.$i]; }
			if(is_array($t_arr_tmp)){ $t_arr_tmp = $t_arr_tmp[0]; }
			$sql_comm .= "`".$flds_arr[$i]."` = '".$t_arr_tmp."'";
		}
				
		$sql_comm .= ", `cars` = '".$jcars."'".$this->prog_sql;
		// Update car location.
		if(strlen($this->arr['pfld6'][0]) > 0){
			for($cn=0;$cn<count($cars);$cn++){
				$carArrTmp = array('rr' => array($cars[$cn]['RR']), 'cars' => array($cars[$cn]['NUM']), 'lading' => $this->arr['fld11'], 'location' => strtoupper($this->arr['pfld6'][0]));
				$this->mricf->carStatusUpd($carArrTmp);
			}
		}
		
		// Create generated load if unloading AND not already existing for waybill.
		$arg_cnt = $this->Generic_model->qry("SELECT COUNT(`id`) AS `c` FROM `ichange_generated_loads` WHERE `waybill_num` = '".$this->arr['fld8']."'");
		$already_gen = $arg_cnt[0]->c; //q_cntr("ichange_generated_loads", "`waybill_num` = '".$this->arr['fld8']."'");
		if($already_gen < 1 && ($this->arr['fld7'] == "UNLOADING" || $this->arr['fld7'] == "UNLOADED")){
			$rec_goods = $this->mricf->qry("ichange_indust", $this->arr['fld5'], "indust_name", "freight_in");
			$rec_goods = str_replace(", ",",",$rec_goods);
			$rec_goods = str_replace("(","",$rec_goods);
			$rec_goods = str_replace(")","",$rec_goods);
			$rexplode = explode(",", $rec_goods);
			$generates = array();
			if(in_array($this->arr['fld11'],$rexplode)){
				$send_goods = $this->mricf->qry("ichange_indust", $this->arr['fld5'], "indust_name", "freight_out");
				$send_goods = str_replace(", ",",",$send_goods);
				$send_goods = str_replace("(","",$send_goods);
				$send_goods = str_replace(")","",$send_goods);
				$sends_out = explode(",", $send_goods);
		
				$gen_tmp = $this->mricf->qry("ichange_commod", $this->arr['fld11'], "commod_name", "generates");
				$gen_tmp = str_replace("; ",";",$gen_tmp);
				$genexp = explode(";",$gen_tmp);
				for($occ2=0;$occ2<count($genexp);$occ2++){
					if(in_array($genexp[$occ2],$sends_out)){$generates[] = $genexp[$occ2];}
				}
			}
		
			$r = @rand(0,count($generates)-1);
			if(strlen(@$generates[$r]) > 0){$gen_load = "INSERT INTO `ichange_generated_loads` SET 
				`added` = '".date('U')."', 
				`waybill_num` = '".$this->arr['fld8']."', 
				`commodity` = '".$generates[$r]."', 
				`orig_industry` = '".$this->arr['fld5']."', 
				`date_human` = '".date('Y-m-d')."', 
				`railroad` = '".$_COOKIE['rr_sess']."'";
			$this->Generic_model->change($gen_load);
			}
		}

		// If waybill created from a generated loads, delete record from generated loads table
		if(isset($this->arr['genload'])){
			$dgsql = "DELETE FROM `ichange_generated_loads` WHERE `id` = '".$this->arr['genload']."'";
			$this->Generic_model->change($dgsql);
		} 
		// End From interchangecars/inc/waybill_save.php
	}
	
	function qry_build(){
		// Builds SQL query string from $this->arr keys / values	

		$this->sql = "";
		$ignore = array('tbl','id','submit', 'not_uppercase'); // 'not_uppercase', if exists, disables conversion of strings to UPPER CASE!
		$arr_kys = array_keys($this->arr);
		$i=0;
		$cntr = 0;

		// Start compile other data
		$other_data = array();
		if(json_decode($_POST['other_data_json'],TRUE)){$other_data = @json_decode($_POST['other_data_json'],TRUE);}
		if($this->arr['fld11'] != "MT" && $this->arr['fld11'] != "EMPTY" && $this->arr['fld11'] != "MTY" && strlen($this->arr['fld11']) > 0){
			$other_data['commodity'] = $this->arr['fld11']; 
		}
		if(strlen($this->arr['fld4_indDesc']) > 0){$other_data['orig_ind_op'] = strtoupper($this->arr['fld4_indDesc']);}
		if(strlen($this->arr['fld5_indDesc']) > 0){$other_data['dest_ind_op'] = strtoupper($this->arr['fld5_indDesc']);}
		// End compile other data


		if($this->arr['id'] > 0){$this->sql = "UPDATE `".$this->arr['tbl']."` SET ";}
		else{$this->sql = "INSERT INTO `".$this->arr['tbl']."` SET `sw_order` = '50', ";}
		
		$this->sql .= "`date` = '".strtoupper($this->arr['fld1'])."', 
			`rr_id_from` = '".strtoupper($this->arr['fld2'])."', 
			`rr_id_to` = '".strtoupper($this->arr['fld3'])."', 
			`indust_origin_name` = '".strtoupper($this->arr['fld4'])."', 
			`indust_dest_name` = '".strtoupper($this->arr['fld5'])."', 
			`routing` = '".strtoupper($this->arr['fld6'])."', 
			`status` = '".strtoupper($this->arr['fld7'][count($this->arr['fld7'])-1])."', 
			`waybill_num` = '".strtoupper($this->arr['fld8'])."', 
			`lading` = '".strtoupper($this->arr['fld11'])."', 
			`train_id` = '".strtoupper($this->arr['fld14'][count($this->arr['fld14'])-1])."', 
			`po` = '".strtoupper($this->arr['fld15'])."', 
			`waybill_type` = '".strtoupper($this->arr['fld16'])."', 
			`notes` = '".strtoupper($this->arr['fld17'])."', 
			`rr_id_handling` = '".strtoupper($this->arr['fld18'])."', 
			`return_to` = '".strtoupper($this->arr['fld19'])."', 
			`cars` = '".strtoupper($this->arr['fld21'])."', 
			`other_data` = '".json_encode($other_data)."'".$this->prog_sql;
			//`progress` = '".$jprog."'";
			
		if($this->arr['id'] > 0){$this->sql .= " WHERE `id` = '".$this->arr['id']."'";}
	}
	
	function email_wb_to_grp(){
		// Sends an email to MRICC group
		$subj = "CREATED"; if($subj = "UPDATED"){;}
		$subject = 'Waybill '.$this->arr['fld8'].' has been '.$subj;
		$message = "Waybill Date: ".$this->arr['fld1']."\n";
		$message .= "Waybill: ".$this->arr['fld8']."\n";
		$message .= "Origin Industry: ".$this->arr['fld4']."\n";
		$message .= "Destination Industry: ".$this->arr['fld5']."\n";
		$message .= "Return To: ".$this->arr['fld19']."\n";
		$message .= "Status: ".$this->arr['fld7'][count($this->arr['fld7'])-1]."\n";
		$message .= "Lading: ".$this->arr['fld11']."\n";
		$message .= "In Train: ".$this->arr['fld14'][count($this->arr['fld14'])-1]."\n";
		$message .= "P/Ord #: ".$this->arr['fld15']."\n";
		$message .= "Notes: ".$this->arr['fld17']."\n";
		$message .= "--------------------\n";

		// Include latest progress report
		$sql = "SELECT * FROM `ichange_progress` WHERE `waybill_num` LIKE '".$this->arr['fld8']."' ORDER BY `id` DESC LIMIT 5";
		$tmp = (array)$this->Generic_model->qry($sql);
		$p_tmp = @json_decode($this->full_jprog, TRUE);
		$ps = count($p_tmp)-5; if($ps < 0){$ps = 0;}
		//for($p=0;$p<count($p_tmp);$p++){
		for($p=$ps;$p<count($p_tmp);$p++){
			$p_tmp[$p] = (array)$p_tmp[$p];
			if(!isset($p_tmp[$p]['train'])){$p_tmp[$p]['train'] = "";}
			$message .= $p_tmp[$p]['date']." ".$p_tmp[$p]['time']." - ".$p_tmp[$p]['status']." - ".$p_tmp[$p]['train']." - ".$p_tmp[$p]['text']."\n";
		}
		$message .= "--------------------\n";
		$message .= "MRICF V2.0 emailer";
		
		$this->email->from('mricf@stanfordhosting.net', 'MRICF');
		$this->email->to('MRICC@yahoogroups.com');

		$this->email->subject($subject);
		$this->email->message($message);

		$this->email->send();
		//echo nl2br($message);
	}

	function last_act_update(){
		$this->Generic_model->change("UPDATE `ichange_rr` SET `last_act` = '".date('U')."' WHERE `id` = '".@$_COOKIE['rr_sess']."'");		
	}

}
?>