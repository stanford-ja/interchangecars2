<?php
class Switchlist extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('mricf');
		$this->load->library('email');
		
		$this->load->model('Railroad_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Locomotives_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Waybill_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Train_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Switchlist";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

		$rrArrTmp = $this->mricf->rrFullArr();
		$rrArrTmp_kys = array_keys($rrArrTmp);
		for($r=0;$r<count(array_keys($rrArrTmp_kys));$r++){$this->arr[$rrArrTmp_kys[$r]] = $rrArrTmp[$rrArrTmp_kys[$r]];}
		/*
		$arRR = (array)$this->Railroad_model->get_all(); //Active();
		$this->arr['allRR'] = array();
		$this->arr['allRRKys'] = array();
		for($i=0;$i<count($arRR);$i++){
			$this->arr['allRR'][$arRR[$i]->id] = $arRR[$i]; // Used to get data for specific RR , id field is key for array.
			$this->arr['allRRKys'][] = $arRR[$i]->id; // Used to order by Report Mark.
			$this->arr['allRRRepMark'][$arRR[$i]->report_mark] = $arRR[$i]->id;
		}
		*/

		$this->my_rr_ids = $this->mricf->affil_ids($this->arr['rr_sess'],$this->arr['allRR']);
		
		$this->dates = array('mth' => array("","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"));
	}

	public function index($id=0){
		header("Location:lst/".$id); exit();
		$this->lst($id=0);
	}
	
	public function lst($id=0){
		//$this->arr['pgTitle'] .= " - Switchlist";
		$randpos = array();
		$trdat = (array)$this->Train_model->get_single($id);
		//echo "<pre>"; print_r($trdat); echo "</pre>";
		$op_days = "";
		if($trdat[0]->sun == 1){$op_days .= "SUN ";}
		if($trdat[0]->mon == 1){$op_days .= "MON ";}
		if($trdat[0]->tues == 1){$op_days .= "TUE ";}
		if($trdat[0]->wed == 1){$op_days .= "WED ";}
		if($trdat[0]->thu == 1){$op_days .= "THU ";}
		if($trdat[0]->fri == 1){$op_days .= "FRI ";}
		if($trdat[0]->sat == 1){$op_days .= "SAT ";}

		$lo_opts = array('' => "Select one");
		$lo_tmp = (array)$this->Locomotives_model->getLocos4RR($this->arr['rr_sess']);
		for($i=0;$i<count($lo_tmp);$i++){$lo_opts[$lo_tmp[$i]->loco_num] = $lo_tmp[$i]->loco_num." - ".$lo_tmp[$i]->model;}
		$lo_select = "<div style=\"z-index: 15; position: absolute; left: 200px; top: 200px; background-color: antiquewhite; border: 1px solid brown; padding: 5px; display: none\" id=\"loco_sel_div\">".
			"<h2>Change Loco for Switchlist</h2>".
			form_open_multipart(WEB_ROOT.'/switchlist/chgloco').
			form_hidden('id',$id).
			form_dropdown("loco_select",$lo_opts,$trdat[0]->loco_num,'style="display: inline;"')."&nbsp;".
			form_submit("submit","Change").form_close()."<a href=\"javascript:{}\" onclick=\"document.getElementById('loco_sel_div').style.display = 'none';\">[ Close ]</a></div>";

		$this->trdat['field_names'] = array("Train ID / Motive Power", "Train Description", "Origin / Destination", "Operation Days", "Direction", "Operation Notes");
		$this->trdat['data'][0]['train_id'] = $lo_select.$trdat[0]->train_id." / ".$trdat[0]->loco_num."&nbsp;<a href=\"javascript:{}\" onclick=\"document.getElementById('loco_sel_div').style.display = 'block';\">Change / Add Motive Power</a>";
		//$this->trdat['data'][0]['loco_num'] = $trdat[0]->loco_num;
		$this->trdat['data'][0]['train_desc'] = $trdat[0]->train_desc;
		$this->trdat['data'][0]['origin'] = $trdat[0]->origin." - to - ".$trdat[0]->destination;
		//$this->trdat['data'][0]['destination'] = $trdat[0]->destination;
		$this->trdat['data'][0]['op_days'] = $op_days;
		$this->trdat['data'][0]['direction'] = $trdat[0]->direction;
		$this->trdat['data'][0]['op_notes'] = $trdat[0]->op_notes;
		$this->arr['pgTitle'] .= " - ".$trdat[0]->train_id;

		//$this->dat = array();
		/*
		$this->dat['fields'] 			= array('waybill_num', 'move_to', 'cars','info','lading','routing','rr_id_handling');
		$this->dat['field_names'] 		= array("Waybill No.", "Action", "Cars on waybill","Details","Lading","Route","On Railroad");
		*/
		$this->dat['fields'] 			= array('waybill_num', 'cars','info','lading','routing','rr_id_handling');
		$this->dat['field_names'] 		= array("Waybill No.", "Cars on waybill","Details","Lading","Route","On Railroad");
		//$this->dat['field_styles']		= array(0 => "width: 180px;");
		$this->dat['options']			= array();
		if($this->arr['rr_sess'] > 0){$this->dat['options']	= array('Edit' => "../../waybill/edit/",
				'Remove' => "../../switchlist/remove_wb/"
			); // Paths to options method, with trailling slash!
		}
		$this->dat['links']				= array();
			/*
				'New' => "/edit/0"
			); // Paths for other links!
			*/
		
		$wb_affected_ids = "";
		if(isset($trdat[0]->train_id)){
			$arrdat = (array)$this->Waybill_model->get_all4Train($trdat[0]->train_id);
			
			$move_to_opts = $this->mricf->rr_ichange_lst("",0,array('where' => "`id` = '".$this->arr['rr_sess']."'"));
			//$move_to_opts .= "<option value=\"UNLOADING\">Unloading</option>\n";
			$uli_opts = "<option value=\"0\" selected=\"selected\">Manual Unload</option>
				<option value=\"1\">1 day</option>
				<option value=\"2\">2 days</option>
				<option value=\"3\">3 days</option>
				<option value=\"4\">4 days</option>
				<option value=\"5\">5 days</option>
				<option value=\"6\">6 days</option>
				<option value=\"7\">7 days</option>
				<option value=\"8\">8 days</option>
				<option value=\"9\">9 days</option>";
			
			for($i=0;$i<count($arrdat);$i++){
				// List of cars for Rr and affiliates
				// Cars for waybill, incl affiliates
				$cars = "";
				$cars_arr = @json_decode($arrdat[$i]->cars,TRUE);
				for($c=0;$c<count($cars_arr);$c++){
					if(in_array($cars_arr[$c]['RR'],$this->my_rr_ids) && strlen($cars_arr[$c]['NUM']) > 0){
						$cars .= $cars_arr[$c]['NUM']." (".$cars_arr[$c]['AAR'].") [".$this->arr['allRR'][$cars_arr[$c]['RR']]->report_mark."]<br />";
					}
				}						
				
				// Progress entries array
				$prog = @json_decode($arrdat[$i]->progress,TRUE);
				$map_loc = "";
				if(isset($prog[count($prog)-1]['map_location'])){
					if(strlen($prog[count($prog)-1]['map_location']) > 0 && strpos("Z".$arrdat[$i]->status,"AT") < 1){$map_loc = "<br />At: ".$prog[count($prog)-1]['map_location'];}
				}
				$last_prog = $prog[count($prog)-1]['date']."&nbsp;".$prog[count($prog)-1]['time']." - ".$prog[count($prog)-1]['text'];
				
				// Listing of waybill details allocated to train
				$this->dat['data'][$i]['id']						= $arrdat[$i]->id;
				$this->dat['data'][$i]['waybill_num'] 		= $arrdat[$i]->waybill_num;
				$this->dat['data'][$i]['move_to']				= "";
				if(in_array($arrdat[$i]->rr_id_handling,$this->my_rr_ids)){
					/*
					$wb_affected_ids .= $arrdat[$i]->id.";";
					$this->dat['data'][$i]['move_to'] = "<span class=\"sw_hide\">".form_hidden('wb_id[]',$arrdat[$i]->id)."<select name=\"move_to_ind[]\" style=\"padding: 0px;\" onchange=\"var uli = document.getElementById('unload_in_".$arrdat[$i]->id."'); uli.style.display='none'; if(this.value=='UNLOADING'){uli.style.display = 'inline';}\">".$move_to_opts."
						<option value=\"LOADING\">Loading at ".substr($this->mricf->qry("ichange_waybill", $arrdat[$i]->id, "id", "indust_origin_name"),0,20)."...</option>\n
						<option value=\"UNLOADING\">Unloading at ".substr($this->mricf->qry("ichange_waybill", $arrdat[$i]->id, "id", "indust_dest_name"),0,20)."...</option>\n
						</select>";
					$this->dat['data'][$i]['move_to'] .= "<span style=\"display: none;\" id=\"unload_in_".$arrdat[$i]->id."\"><br /><select name=\"uli[]\" style=\"font-size:8pt;\">".$uli_opts."</select></span>";
					$this->dat['data'][$i]['move_to'] .= "<br /><select name=\"move_to_dt[]\" style=\"font-size:8pt;\">".$this->dt_opts()."</select>";
					$this->dat['data'][$i]['move_to'] .= "&nbsp;<select name=\"move_to_hr[]\" style=\"font-size:8pt;\">".$this->hr_opts()."</select>:";
					$this->dat['data'][$i]['move_to'] .= "<select name=\"move_to_mi[]\" style=\"font-size:8pt;\">".$this->mi_opts()."</select>";
					$this->dat['data'][$i]['move_to'] .= "</span>";
					*/
					$wb_affected_ids .= $arrdat[$i]->id.";";
					$this->dat['data'][$i]['waybill_num'] .= "<span class=\"sw_hide\">".form_hidden('wb_id[]',$arrdat[$i]->id)."<select name=\"move_to_ind[]\" style=\"padding: 0px;\" onchange=\"var uli = document.getElementById('unload_in_".$arrdat[$i]->id."'); uli.style.display='none'; if(this.value=='UNLOADING'){uli.style.display = 'inline';}\">".$move_to_opts."
						<option value=\"LOADING\">Loading at ".substr($this->mricf->qry("ichange_waybill", $arrdat[$i]->id, "id", "indust_origin_name"),0,20)."...</option>\n
						<option value=\"UNLOADING\">Unloading at ".substr($this->mricf->qry("ichange_waybill", $arrdat[$i]->id, "id", "indust_dest_name"),0,20)."...</option>\n
						</select>";
					$this->dat['data'][$i]['waybill_num'] .= "<span style=\"display: none;\" id=\"unload_in_".$arrdat[$i]->id."\"><br /><select name=\"uli[]\" style=\"font-size:8pt;\">".$uli_opts."</select></span>";
					$this->dat['data'][$i]['waybill_num'] .= "<br /><select name=\"move_to_dt[]\" style=\"font-size:8pt;\">".$this->dt_opts()."</select>";
					$this->dat['data'][$i]['waybill_num'] .= "&nbsp;<select name=\"move_to_hr[]\" style=\"font-size:8pt;\">".$this->hr_opts()."</select>:";
					$this->dat['data'][$i]['waybill_num'] .= "<select name=\"move_to_mi[]\" style=\"font-size:8pt;\">".$this->mi_opts()."</select>";
					$this->dat['data'][$i]['waybill_num'] .= "</span>";
				}
				$this->dat['data'][$i]['cars']				 	= $cars; //$arrdat[$i]->cars;
				$this->dat['data'][$i]['info']					= "<div style=\"border: 1px solid red; background-color: antiquewhite; padding: 3px; float: right;\">".$arrdat[$i]->status.$map_loc."</div>"."From ".$arrdat[$i]->indust_origin_name."<br />To ".$arrdat[$i]->indust_dest_name."<hr /><em>".$last_prog."</em>";
				$this->dat['data'][$i]['routing']				= $arrdat[$i]->routing;
				$this->dat['data'][$i]['lading']				= $arrdat[$i]->lading;
				$this->dat['data'][$i]['rr_id_handling']		= $this->arr['allRR'][$arrdat[$i]->rr_id_handling]->report_mark;
			}
		}

		// Run train form
		// Needs to be here so that wb_affected_ids is populated!
		if($this->arr['rr_sess'] > 0){
			$this->flddat = array('fields' => array());
			// Selector to move car to wherever.
			//$move_to_opts = $this->mricf->rr_ichange_lst("",0,array('where' => "`id` = '".$this->arr['rr_sess']."'"));
			$this->flddat['fields'][] = form_open_multipart('../switchlist/move_to');
			$this->flddat['fields'][] = form_hidden('id',$id);
			$this->flddat['fields'][] = form_hidden('train_id',$trdat[0]->train_id);
			$this->flddat['fields'][] = form_hidden('wb_affected_ids',$wb_affected_ids);
			$this->flddat['fields'][] = "<span style=\"font-size: 10pt;\">Move to: <select name=\"move_to\">".$move_to_opts."</select>&nbsp;".
				"Date/Time: <select name=\"move_to_dt\" style=\"font-size:9pt;\">".$this->dt_opts()."</select>".
				"&nbsp;<select name=\"move_to_hr\" style=\"font-size:9pt;\">".$this->hr_opts()."</select>:".
				"<select name=\"move_to_mi\" style=\"font-size:9pt;\">".$this->mi_opts()."</select></span>";
			$this->flddat['fields'][] = "&nbsp;".form_submit('submit', 'Deliver Cars');
			$this->flddat['fields'][] = form_close();

			// Form for table contents
			$this->dat['before_table'] = form_open_multipart('../switchlist/move_to_individual').
				form_hidden('train_id',$trdat[0]->train_id).
				form_hidden('id',$id);
			$this->dat['after_table'] = form_submit('submit', 'Deliver Cars Individually').form_close();
			
		}
		

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		$this->load->view('view', $this->trdat);
		$this->load->view('fields', @$this->flddat);
		$this->load->view('list', $this->dat);
		$this->load->view('footer');
	}
	
	public function edit($id=0){
		// Edit method - for editing records, not need for switchlist.
	}

	public function move_to(){
		// Move cars on waybill to wherever
		$this->arr = $_POST;
		$this->load->model('Generic_model','',TRUE);
		$wb_aff_id = explode(";",$this->arr['wb_affected_ids']);
		//echo "<pre>"; print_r($wb_aff_id);echo "</pre>";
		$this->arr['move_to'] = strtoupper($this->arr['move_to']);
		$this->email_txt = "";
		
		for($w=0;$w<count($wb_aff_id);$w++){
			$prog = $this->mricf->progWB($wb_aff_id[$w]);
			$prog[] = array(
				'date' => $this->arr['move_to_dt'], 
				'time' => $this->arr['move_to_hr'].":".$this->arr['move_to_mi'], 
				'text' => "CAR/S ON WAYBILL HAVE BEEN MOVED BY TRAIN <strong>".$this->arr['train_id']."</strong> AND ARE NOW LOCATED <strong>".$this->arr['move_to']."</strong> (v2.0)",
				'waybill_num' => 0,
				'map_location' => str_replace("AT ","",$this->arr['move_to']),
				'train' => $this->arr['train_id'],
				'status' => $this->arr['move_to']
			);
			$jprog = json_encode($prog);
			$this->email_txt .= $this->arr['move_to_dt']." - ".$this->arr['move_to_hr'].":".$this->arr['move_to_mi']." - ".$this->arr['move_to']."\n";
			$s = "UPDATE `ichange_waybill` SET `status` = '".$this->arr['move_to']."', `progress` = '".$jprog."' WHERE `id` = '".$wb_aff_id[$w]."'";
			$this->Generic_model->change($s);
		}
		if(strlen($this->email_txt) > 0){$this->email_sw_to_grp();}
		header("Location:../switchlist/lst/".$this->arr['id']);
		exit();

	}
	
	public function move_to_individual(){
		$this->arr = $_POST;
		$this->load->model('Generic_model','',TRUE);
		//echo "<pre>"; print_r($this->arr); echo "</pre>";
		$this->email_txt = "";

		for($w=0;$w<count($this->arr['wb_id']);$w++){
			if(strlen($this->arr['move_to_ind'][$w]) > 0){
				$loc = str_replace(array("AT ","UNLOADING","LOADING"),"",$this->arr['move_to_ind'][$w]);
				$this->arr['move_to_ind'][$w] = strtoupper($this->arr['move_to_ind'][$w]);
				$txt = "CAR/S ON WAYBILL HAVE BEEN MOVED BY TRAIN <strong>".$this->arr['train_id']."</strong> AND ARE NOW LOCATED <strong>".$this->arr['move_to_ind'][$w]."</strong> (v2.0)";
				if($this->arr['move_to_ind'][$w] == "UNLOADING"){
					$ind = $this->mricf->qry("ichange_waybill", $this->arr['wb_id'][$w], "id", "indust_dest_name");
					$rr = $this->mricf->qry("ichange_waybill", $this->arr['wb_id'][$w], "id", "rr_id_to");
					$loc = $this->loc_qry($ind,$rr);
					$txt = "UNLOADING AT ".$ind;
					if($this->arr['uli'][$w] > 0){
						$wb_num = $this->mricf->qry("ichange_waybill", $this->arr['wb_id'][$w], "id", "waybill_num");
						$dt = date('U') + (60*60*24*($this->arr['uli'][$w]));
						$txt .= " *AUTOMATIC UNLOADING WILL BE COMPLETED ON ".date('Y-m-d',$dt)."*";
						$sql_cro = "INSERT INTO `ichange_auto` SET 
							`act_date` = '".date('Y-m-d', $dt)."', 
							`waypoint` = '', 
							`train_id` = '', 
							`waybill_num` = '".$wb_num."', 
							`description` = 'UNLOADED'";
						$this->Generic_model->change($sql_cro);
					}
				}
				if($this->arr['move_to_ind'][$w] == "LOADING"){
					$ind = $this->mricf->qry("ichange_waybill", $this->arr['wb_id'][$w], "id", "indust_origin_name");
					$rr = $this->mricf->qry("ichange_waybill", $this->arr['wb_id'][$w], "id", "rr_id_from");
					$loc = $this->loc_qry($ind,$rr);
					$txt = "LOADING AT ".$ind;
				}
				$prog = $this->mricf->progWB($this->arr['wb_id'][$w]);
				$prog[] = array(
					'date' => $this->arr['move_to_dt'][$w], 
					'time' => $this->arr['move_to_hr'][$w].":".$this->arr['move_to_mi'][$w], 
					'text' => $txt,
					'waybill_num' => 0,
					'map_location' => $loc,
					'train' => $this->arr['train_id'],
					'status' => $this->arr['move_to_ind'][$w]
				);
				$this->email_txt .= $this->arr['move_to_dt'][$w]." - ".$this->arr['move_to_hr'][$w].":".$this->arr['move_to_mi'][$w]." - ".$loc." - ".$txt."\n";
				$jprog = json_encode($prog);
				$s = "UPDATE `ichange_waybill` SET `status` = '".$this->arr['move_to_ind'][$w]."', `progress` = '".$jprog."' WHERE `id` = '".$this->arr['wb_id'][$w]."'";
				$this->Generic_model->change($s);
			}
		}
		if(strlen($this->email_txt) > 0){$this->email_sw_to_grp();}

		//echo "<pre>"; print_r($this->arr); echo "</pre>"; exit();

		header("Location:../switchlist/lst/".$this->arr['id']);
		exit();
	}
	
	public function remove_wb($id=0){
		// Remove waybill with id=$id from switchlist for train.
		$this->load->model('Generic_model','',TRUE);
		$wb = $this->Generic_model->qry("SELECT `train_id` FROM `ichange_waybill` WHERE `id` = '".$id."'");
		$tr = $this->Generic_model->qry("SELECT `id` FROM `ichange_trains` WHERE `train_id` = '".$wb[0]->train_id."' LIMIT 1");
		$this->Generic_model->change("UPDATE `ichange_waybill` SET `train_id` = '' WHERE `id` = '".$id."'");
		header("Location:../../switchlist/lst/".$tr[0]->id);
		exit();
	}
	
	public function chgloco(){
		$this->load->model('Generic_model','',TRUE);
		$s = "UPDATE `ichange_trains` SET `loco_num` = '".$_POST['loco_select']."' WHERE `id` = '".$_POST['id']."'";
		//echo $s; exit();
		$this->Generic_model->change($s);
		header("Location:".WEB_ROOT."/switchlist/lst/".$_POST['id']);
	}

	public function setFieldSpecs(){
		// Sets specific field definitions for the controller being used.
		$this->dat['fields'] = array();
		
		// Add custom model calls / queries under this line...
		$this->load->model('Aar_model', '', TRUE);
		$this->load->model('Railroad_model', '', TRUE);
		
		// Add other code for fields under this line...
		$aar_opts = array();
		$aar_tmp = (array)$this->Aar_model->get_allSorted();
				
		// Add form and field definitions specific to this controller under this line... 
		$this->dat['hidden'] = array('tbl' => 'aar', 'id' => @$this->dat['data'][0]->id);
		$this->dat['form_url'] = "../save";
		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'AAR Code', 'def' => array(
              'name'        => 'aar_code',
              'id'          => 'aar_code',
              'value'       => @$this->dat['data'][0]->aar_code,
              'maxlength'   => '10',
              'size'        => '10'
			)
		);

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Description', 'def' => array(
              'name'        => 'desc',
              'id'          => 'desc',
              'value'       => @$this->dat['data'][0]->desc,
              'rows'			 => '5',
              'cols'        => '50'
			)
		);
		
		/*
		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Owner RR', 'name' => 'rr', 'value' => @$this->dat['data'][0]->rr, 
			'other' => 'id="rr"', 'options' => $rr_opts
		);
		*/

	}
	
	
	function dt_opts(){
		$dt = date('U');
		$dt_end = date('U') + intval(10*86400); // 10 days
		$opts = "";
		for($i=$dt;$i<$dt_end;$i=$i+86400){
			$sel = ""; if($i == date('Y-m-d')){$sel = " selected=\"selected\"";}
			$opts .= "<option value=\"".date('Y-m-d',$i)."\"".$sel.">".date('Y-m-d',$i)."</option>";
		}
		return $opts;
	}
	
	function hr_opts(){
		$opts = "";
		for($i=0;$i<24;$i++){
			$ii = $i; if($i < 10){$ii = "0".$i;}
			$sel = ""; if($ii == date('H')){$sel = " selected=\"selected\"";}
			$opts .= "<option value=\"".$ii."\"".$sel.">".$ii."</option>";
		}
		return $opts;
	}
	
	function mi_opts(){
		$opts = "";
		for($i=0;$i<60;$i++){
			$ii = $i; if($i < 10){$ii = "0".$i;}
			$sel = ""; if($ii == date('i')){$sel = " selected=\"selected\"";}
			$opts .= "<option value=\"".$ii."\"".$sel.">".$ii."</option>";
		}
		return $opts;
	}
	
	function loc_qry($ind='',$rr=0){
		// Sets location for progress report.
		// $ind = industry to get town/location for
		// $rr = railroad id that serves the industry
		$tmp = (array)$this->Generic_model->qry("SELECT `town` FROM `ichange_indust` WHERE `indust_name` LIKE '%".$ind."%' AND `rr` = '".$rr."'");
		if(isset($tmp[0]->town)){$loc = @$tmp[0]->town;}
		return $loc;
	}

	function email_sw_to_grp(){
		// Sends an email to MRICC group
		$subject = 'Train '.$this->arr['train_id'].' has moved cars';
		$message = "Train ".$this->arr['train_id']." has moved the following cars\n\n".$this->email_txt;

		$message .= "--------------------\n";
		$message .= "MRICF V2.0 emailer";
		
		$this->email->from('mricf@stanfordhosting.net', 'MRICF');
		$this->email->to('MRICC@yahoogroups.com');

		$this->email->subject($subject);
		$this->email->message($message);

		$this->email->send();
		//echo nl2br($message);
	}


}
?>
