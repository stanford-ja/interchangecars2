<?php
class Waybill extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->helper("file");
		$this->load->library('mricf');
		$this->load->library('dates_times');
		
		$this->load->model('Waybill_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Waybill";
		$this->arr['affil'] = array();
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){
			$this->arr['rr_sess'] = $_COOKIE['rr_sess'];
			$this->arr['affil'][] = $_COOKIE['rr_sess'];
		}

		// Load other required models
		$this->load->model('Railroad_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Cars_model', '', TRUE);		
		$this->load->model('Train_model', '', TRUE);		
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.

		// Railroad array set up
		$this->arr['myRR'] = $this->Railroad_model->get_single($this->arr['rr_sess']);
		
		$rrArrTmp = $this->mricf->rrFullArr();
		$rrArrTmp_kys = array_keys($rrArrTmp);
		for($r=0;$r<count(array_keys($rrArrTmp_kys));$r++){$this->arr[$rrArrTmp_kys[$r]] = $rrArrTmp[$rrArrTmp_kys[$r]];}
		//$arRR = (array)$this->Railroad_model->get_allActive();
		/*
		$arRR = (array)$this->Railroad_model->get_all();
		$this->arr['allRR'] = array();
		$this->arr['allRRKys'] = array();
		for($i=0;$i<count($arRR);$i++){
			$this->arr['allRR'][$arRR[$i]->id] = $arRR[$i]; // Used to get data for specific RR , id field is key for array.
			$this->arr['allRRKys'][] = $arRR[$i]->id; // Used to order by Report Mark.
			$this->arr['allRRRepMark'][$arRR[$i]->report_mark] = $arRR[$i]->id;
		}
		*/

		$this->dat['rr_options'] = (array)$this->Railroad_model->get_allActive('rr_name');
		//$this->dat['rr_options'] = (array)$this->Railroad_model->get_all();
		
		// Generate Afil WB list (if applicable)
		$this->my_rr_ids = $this->mricf->affil_ids($this->arr['rr_sess'],$this->arr['allRR']);
		$this->arr['affil'] = $this->my_rr_ids;
		/*
		//echo "<pre>"; print_r($this->arr['myRR'][0]); echo "</pre>";
		$my_rr_ids = array($this->arr['rr_sess']);
   	if(@$this->arr['myRR'][0]->show_affil_wb == 1){
   		$myRRs_kys = explode(";", $this->arr['myRR'][0]->affiliates);
    		//for($i=1;$i<count($this->arr['allRR']);$i++){
    		for($i=1;$i<count($this->arr['allRR']);$i++){
    			if(in_array(@$this->arr['allRR'][$i]->report_mark,$myRRs_kys)){
	    			//$this->whr .= " OR (".$this->allHomeWBs($i,@$this->arr['allRR'][$i]->report_mar).")";
	    			$my_rr_ids[] = $i;
	    			$this->arr['affil'][] = $i;
	    		}
    		}
    	}
    	*/
    	
	}

	public function index(){
		$this->view($this->arr['rr_sess']);
	}
	
	public function view($id=0){
		$this->arr['pgTitle'] .= " - View";
		$randpos = array();
		$wbdat = (array)$this->Waybill_model->get_single($id,"id");
		$this->dat['field_names'] = array('ID',"Date", "Waybill No.", "Waybill Type", "Purchase Order No.", "Lading", "Status", "RR From", "RR To", "RR Allocated To", "Industry - Origin", "Industry - Destination", "Return to", "Routing", "Cars", "Train ID", "Notes", "Progress");
		
		for($i=0;$i<count($wbdat);$i++){
			// Cars listing JSON to string.
			$cars = @json_decode($wbdat[$i]->cars, TRUE);
			$cars_dat = "";
			for($c=0;$c<count($cars);$c++){
				if(strlen(@$cars[$c]['NUM']) > 0){$cars_dat .= @$cars[$c]['NUM']." (".@$cars[$c]['AAR'].") (".@$this->arr['allRR'][$cars[$c]['RR']]->report_mark.")<br />";}
			}

			//$prog_dat = $this->prog_lst($wbdat[0]->progress);
			$prog_dat = (array)$this->Generic_model->qry("SELECT * FROM `ichange_progress` WHERE `waybill_num` = '".$wbdat[$i]->waybill_num."' ORDER BY `id` DESC, `date` DESC, `time` DESC");

			$this->dat['data'][0]['id'] 					= $wbdat[$i]->id;
			$this->dat['data'][0]['date']			 	= $wbdat[$i]->date;
			$this->dat['data'][0]['waybill_num']	 	= $wbdat[$i]->waybill_num;
			$this->dat['data'][0]['waybill_type']	 	= $wbdat[$i]->waybill_type;
			$this->dat['data'][0]['po']				 	= $wbdat[$i]->po;
			$this->dat['data'][0]['lading']	 			= $wbdat[$i]->lading;
			$this->dat['data'][0]['status'] 			= $wbdat[$i]->status;
			$this->dat['data'][0]['rr_id_from'] 		= $this->arr['allRR'][$wbdat[$i]->rr_id_from]->report_mark;
			$this->dat['data'][0]['rr_id_to'] 			= $this->arr['allRR'][$wbdat[$i]->rr_id_to]->report_mark;
			$this->dat['data'][0]['rr_id_handling']	= $this->arr['allRR'][$wbdat[$i]->rr_id_handling]->report_mark;
			$this->dat['data'][0]['indust_origin_name']		= $wbdat[$i]->indust_origin_name;
			$this->dat['data'][0]['indust_dest_name'] 		= $wbdat[$i]->indust_dest_name;
			$this->dat['data'][0]['return_to'] 		= $wbdat[$i]->return_to;
			$this->dat['data'][0]['routing'] 			= $wbdat[$i]->routing;
			$this->dat['data'][0]['cars']				= $cars_dat;
			$this->dat['data'][0]['train_id'] 			= $wbdat[$i]->train_id;
			$this->dat['data'][0]['notes'] 				= $wbdat[$i]->notes;
			$this->dat['data'][0]['progress']			= $prog_dat;
			$this->dat['data'][0]['other_data']		= @json_decode($wbdat[$i]->other_data);
		}

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		$this->load->view('view', $this->dat);
		$this->load->view('footer');
	}

	public function edit2($id=0,$fld="id"){
		// Used to get waybill ID and then redirect to edit
		$tmp = (array)$this->Waybill_model->get_single($id,$fld);
		header("Location:".WEB_ROOT."/waybill/edit/".$tmp[0]->id);
	}
	
	public function edit($id=0){
		// Used for editing existing (edit/[id]) and adding new (edit/0) records
		if($this->arr['rr_sess'] == 0){header("Location:".WEB_ROOT."/waybill/view/".$id); exit();}
		$this->load->helper('form');
		$this->dat['attribs'] = array('name' => "form"); // Attribs for form tag
		$this->dat['fields'] = array();
		$this->dat['field_names'] = array();
		$e=0;
		if($id < 1){
			$this->arr['pgTitle'] .= " - New";
			$this->dat['data'][0] = array('id' => 0);
			$wb_num = date('YmdHis');
			$wb_date = date('Y-m-d');
		}else{
			$this->arr['pgTitle'] .= " - Edit";
			$this->dat['data'] = (array)$this->Waybill_model->get_single($id,"id");
			$wb_num = $this->dat['data'][0]->waybill_num;
			$wb_date = $this->dat['data'][0]->date;
			$e=1;
		}

		// List of cars for RR
		$cars_opts = (array)$this->Cars_model->getCars4RR($this->arr['rr_sess'],array('aar_type','car_num'),1,$wb_num);
		for($ca=0;$ca<count($cars_opts);$ca++){
			$this->dat['cars_options'][$ca] = array('car_num' => @$cars_opts[$ca]->car_num, 'aar_type' => @$cars_opts[$ca]->aar_type, 'desc' => @$cars_opts[$ca]->desc, 'rr' => @$this->arr['allRR'][$cars_opts[$ca]->rr]->report_mark);
		}
		
		// Bulk Storage industries
		$this->dat['stodat'] = $this->mricf->getStoredIndust($this->arr['rr_sess']);

		// Transhipped waybills
		$status_dropdown = $this->stat_opts().$this->stat_opt_ic();
		$this->dat['twbs'] = ""; if($id>0){$this->dat['twbs'] = $this->transhipped_wbs(@$this->dat['data'][0]->waybill_num);}
		//$this->dat['auto_num'] = 0; if($id>0){$this->dat['auto_num'] = $this->chk_auto(@$this->dat['data'][0]->waybill_num);}
		$this->dat['auto_num'] = 0; if($id>0){
			$this->chk_auto(@$this->dat['data'][0]->waybill_num);
			$this->dat['auto_num'] = count($this->dat['auto_ent']);
		}
		$this->map_opts();
		$this->train_opts();		
		
		// AAR Code Options
		$this->dat['aar_options'] = $this->mricf->aarOpts();	
		
		// Progress Array
		$prog_dat = @$this->dat['data'][0]->progress;
		//$prog_dat_json = @json_decode($prog_dat,TRUE);
		$prog_data = (array)$this->Generic_model->qry("SELECT * FROM `ichange_progress` WHERE `waybill_num` = '".$wb_num."' ORDER BY `id` DESC, `date` DESC, `time` DESC");
		$this->dat['traindata'] = (array)$this->Generic_model->qry("SELECT * FROM `ichange_trains` WHERE `train_id` = '".@$this->dat['data'][0]->train_id."'");
				
		// Create data variables for waybill form fields
		$this->dat['id'] = $id;
		//$this->arr['id'] = $id;
		$this->dat['fld1'] = $wb_date;
		$this->dat['fld2'] = @$this->dat['data'][0]->rr_id_from;
		$this->dat['fld3'] = @$this->dat['data'][0]->rr_id_to;
		$this->dat['fld4'] = @$this->dat['data'][0]->indust_origin_name;
		$this->dat['fld5'] = @$this->dat['data'][0]->indust_dest_name;
		$this->dat['fld6'] = @$this->dat['data'][0]->routing;
		$this->dat['fld7'] = "<option value=\"".@$this->dat['data'][0]->status."\" selected=\"selected\">".@$this->dat['data'][0]->status."</option>".$status_dropdown;
		$this->dat['fld7b'] = @$this->dat['data'][0]->status;
		$this->dat['fld8'] = $wb_num;
		$this->dat['fld9'] = @$this->dat['data'][0]->car_num;
		$this->dat['fld10'] = @$this->dat['data'][0]->car_aar;
		$this->dat['fld11'] = @$this->dat['data'][0]->lading;
		$this->dat['fld12'] = @$this->dat['data'][0]->alias_num;
		$this->dat['fld13'] = @$this->dat['data'][0]->alias_aar;
		$this->dat['fld14'] = @$this->dat['data'][0]->train_id;
		$this->dat['fld15'] = @$this->dat['data'][0]->po;
  	   $this->dat['fld16'] = @$this->dat['data'][0]->waybill_type;
   	$this->dat['fld17'] = @$this->dat['data'][0]->notes;
     	$this->dat['fld18'] = @$this->dat['data'][0]->rr_id_handling;
  	   $this->dat['fld19'] = @$this->dat['data'][0]->return_to;
   	$this->dat['fld21'] = @json_decode($this->dat['data'][0]->cars, true);
  	   $this->arr['fld21'] = @$this->dat['data'][0]->cars;
  	   $this->arr['fld21_cntr'] = count($this->mricf->cars4RR4WB($this->arr['rr_sess'],$this->dat['id'])); //count(json_decode($this->arr['fld21'],true));
  	   $this->dat['prog_lst'] = $this->prog_lst($prog_data); //$prog_dat);//(@$this->dat['data'][0]->progress);
  	   $this->dat['oth_dat_json'] = @$this->dat['data'][0]->other_data;
  	   $this->dat['sugg_car_types'] = ""; 
  	   if(strlen(@$this->dat['data'][0]->lading)){
  	   	$commod_arr = (array)$this->Generic_model->qry("SELECT `aar_types` FROM `ichange_commod` WHERE `commod_name` LIKE '%".@$this->dat['data'][0]->lading."%' LIMIT 1");
  	   	if(strlen(@$commod_arr[0]->aar_types) > 0){
	  	   	$c_typ = explode(";",$commod_arr[0]->aar_types);
  		   	for($tmp=0;$tmp<count($c_typ);$tmp++){
  	   			$aar_arr = (array)$this->Generic_model->qry("SELECT `desc` FROM `ichange_aar` WHERE `aar_code` = '".$c_typ[$tmp]."' LIMIT 1");
  	   			$this->dat['sugg_car_types'] .= "<li>".$c_typ[$tmp]." - ".@$aar_arr[0]->desc."</li>";
	  	   	}
  		   	$this->dat['sugg_car_types'] = "Commodities Data Suggested Car Types: <ul>".$this->dat['sugg_car_types']."</ul>";
  	   	}
  	  	}
   	if(strlen(@$this->dat['data'][0]->car_aar) > 0){
   		$aar_arr = (array)$this->Generic_model->qry("SELECT `desc` FROM `ichange_aar` WHERE `aar_code` = '".@$this->dat['data'][0]->car_aar."'");
   		$aar_tmp = "<li>".$this->dat['data'][0]->car_aar." - ".@$aar_arr[0]->desc."</li>";
   		$this->dat['sugg_car_types'] .= "Car Type from Purchase Order: <ul>".$aar_tmp."</ul>";
   	}
  	   
  	   $oth_dat = @json_decode($this->dat['data'][0]->other_data,TRUE);
  	   $this->dat['fld4_indDesc'] = @$oth_dat['orig_ind_op'];
  	   $this->dat['fld5_indDesc'] = @$oth_dat['dest_ind_op'];
  	   $this->dat['fld11_prev'] = @$oth_dat['commodity'];
  	   
  	   $this->dat['tz_opts'] = $this->dates_times->getTZOptions();
  	   //$prog_data2 = (array)$prog_data[count($prog_data)-1];
  	   $prog_data2 = array(); if(isset($prog_data[0])){ $prog_data2 = (array)$prog_data[0]; }
  	   if(!isset($prog_data2['date'])){ $prog_data2['date'] = date('Y-m-d'); }
  	   if(!isset($prog_data2['time'])){ $prog_data2['time'] = "00:00"; }
  	   /*
  	   $this->dat['last_prog_date_arr'] = explode("-",$prog_dat_json[count($prog_dat_json)-1]['date']);
  	   $this->dat['last_prog_time_arr'] = explode(":",$prog_dat_json[count($prog_dat_json)-1]['time']);
  	   $this->dat['last_prog_date'] = str_replace("-","",$prog_dat_json[count($prog_dat_json)-1]['date']);
  	   $this->dat['last_prog_date_ux'] = mktime(12,0,0,$this->dat['last_prog_date_arr'][1],$this->dat['last_prog_date_arr'][2],$this->dat['last_prog_date_arr'][0]);
  	   $this->dat['last_prog_time'] = str_replace(":","",$prog_dat_json[count($prog_dat_json)-1]['time']);
  	   */
  	   /*
  	   $this->dat['last_prog_date_arr'] = explode("-",$prog_data[count($prog_data)-1]['date']);
  	   $this->dat['last_prog_time_arr'] = explode(":",$prog_data[count($prog_data)-1]['time']);
  	   $this->dat['last_prog_date'] = str_replace("-","",$prog_data[count($prog_data)-1]['date']);
  	   $this->dat['last_prog_date_ux'] = mktime(12,0,0,$this->dat['last_prog_date_arr'][1],$this->dat['last_prog_date_arr'][2],$this->dat['last_prog_date_arr'][0]);
  	   $this->dat['last_prog_time'] = str_replace(":","",$prog_data[count($prog_data)-1]['time']);
  	   */
  	   $this->dat['last_prog_date_arr'] = explode("-",$prog_data2['date']);
  	   $this->dat['last_prog_time_arr'] = explode(":",$prog_data2['time']);
  	   $this->dat['last_prog_date'] = str_replace("-","",$prog_data2['date']);
  	   $this->dat['last_prog_date_ux'] = mktime(12,0,0,$this->dat['last_prog_date_arr'][1],$this->dat['last_prog_date_arr'][2],$this->dat['last_prog_date_arr'][0]);
  	   $this->dat['last_prog_time'] = str_replace(":","",$prog_data2['time']);

  	   $fld6_tmp = explode("-",str_replace(" ","",str_replace(array("/","(",")"),"-",$this->dat['fld6'])));
  	   $this->dat['route_rr_arr'] = array(); // Array of Report Marks
  	   $this->dat['rr_ics'] = array(); // Array of Interchanges
  	   $this->dat['rr_maps'] = array();
  	   for($i6=0;$i6<count($fld6_tmp);$i6++){
  	   	$rr_data = $this->Generic_model->qry("SELECT `id`,`interchanges`,`show_allocto_only` FROM `ichange_rr` WHERE `report_mark` = '".$fld6_tmp[$i6]."'");
  	   	if(isset($rr_data[0]->id)){
			if(!in_array($fld6_tmp[$i6],array_keys($this->dat['rr_maps']))){
				$tmp = $this->mricf->rrMap($rr_data[0]->id);
				if(isset($tmp[0]) && strlen($tmp[0]) > 0){ $this->dat['rr_maps'][$fld6_tmp[$i6]] = $tmp[0]; }
			}
  	   		if($rr_data[0]->show_allocto_only == 1){if(!in_array($fld6_tmp[$i6],$this->dat['route_rr_arr'])){$this->dat['route_rr_arr'][] = $fld6_tmp[$i6];}}
  	   		if(strlen($rr_data[0]->interchanges) > 0){
				$ics_tmp_arr = explode(";",$rr_data[0]->interchanges);
				$ics_tmp = "";
				for($ita=0;$ita<count($ics_tmp_arr);$ita++){
					$ics_tmp .= "<a href=\"javascript:{}\" onclick=\"document.getElementById('pfld3_0').value = 'Located at ".$fld6_tmp[$i6]." interchange at ".str_replace("'","",$ics_tmp_arr[$ita]).". '\" style=\"text-decoration: none; color: black;\">".$ics_tmp_arr[$ita]."</a><br />";
				}
				//$ics_tmp = str_replace(";","<br />",$rr_data[0]->interchanges);
				$this->dat['rr_ics'][$fld6_tmp[$i6]] = array('ics' => $ics_tmp);
			}
  	   	}
  	   	
  	   }
  	   	
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		//if($this->arr['rr_sess'] > 0){$this->load->view('edit', $this->dat);}
		if($this->arr['rr_sess'] > 0){$this->load->view('edit_waybill', $this->dat);}
		else{
			$this->load->view('not_allowed');
		}
		$this->load->view('footer');
	}
	
	// helper Methods for doing various things
	function map_opts(){
		// Create array of map_location's from progress report for ALL waybills setting array keys as map_location, then sort keys.
		/* SUPERCEDED BY JQuery AutoCOmplete FUNCTION IN js/waybill.php!
		$this->dat['map_lst'] = "";
		$map_arr = array();
		$prog_lst = (array)$this->Waybill_model->get_allProgress();
		for($p=0;$p<count($prog_lst);$p++){
			$map_json = json_decode($prog_lst[$p]->progress, true);
			for($mi=0;$mi<count($map_json);$mi++){
				$map_tmp = str_replace(", ",",",trim($map_json[$mi]['map_location']));
				if(strlen($map_tmp) > 0){$map_arr[$map_tmp] = 1;}	
			}
		}
		$map_kys = array_keys($map_arr);
		sort($map_kys);
		for($mi=0;$mi<count($map_kys);$mi++){
			$map_disp = substr($map_kys[$mi],0,30);
			$this->dat['map_lst'] .= "<option value=\"".$map_kys[$mi]."\">".$map_disp."</option>\n";
		}
		//echo "<pre>"; print_r($this->dat['map_lst']); echo "</pre>";
		//echo $this->dat['map_lst'];
		*/
	}

	function stat_opts(){
		// Status dropdowns creation
		$status_dropdown = "";
		$status_dropdown_arr = $this->mricf->status_dropdowns();
		$status_dd_kys = array_keys($status_dropdown_arr);
		for($s=0;$s<count($status_dd_kys);$s++){$status_dropdown .= "<option value=\"".$status_dd_kys[$s]."\">".$status_dropdown_arr[$status_dd_kys[$s]]."</option>";}
		return $status_dropdown;
	}
	
	function stat_opt_ic(){
		// Status interchange options.
		$ic_locs = "";
		$rr_ics = (array)$this->Railroad_model->get_allActiveInterchanges($ordBy='report_mark');
		for($r=0;$r<count($rr_ics);$r++){
			if(strlen($rr_ics[$r]->interchanges) > 0){
				$ic_rr = explode(";",$rr_ics[$r]->interchanges);
				$ic_locs .= "<option value=\"\" style=\"background-color: maroon; color: white;\">[** ".$rr_ics[$r]->report_mark." I/Changes **]</option>";
				for($i=0;$i<count($ic_rr);$i++){
					if(strlen($ic_rr[$i]) > 2){
						$ic_locs .= "<option value=\"AT ".strtoupper($ic_rr[$i])."\">&nbsp;>>&nbsp;&nbsp;AT ".strtoupper($ic_rr[$i])."</option>";
					}
				}
			}
		}
		return $ic_locs;
	}
		
	function train_opts(){
		// Create trains dropdowns.
		$this->dat['trains_lst'] = $this->mricf->trainOpts(array('rr' => $this->arr['rr_sess'], 'auto' => "Y"));
		/*
		$this->dat['trains_lst'] = "<option value=\"\" style=\"background-color: brown; color: white;\">-- Your trains --</option>";		
		$myTrains = (array)$this->Train_model->get_all4RR_Sorted($this->arr['rr_sess'],'train_id');
		$notMyTrains = (array)$this->Train_model->get_allNot4RR_Sorted($this->arr['rr_sess'],'train_id');
		for($t=0;$t<count($myTrains);$t++){
			$styl = ""; if($myTrains[$t]->auto > 0 || strlen($myTrains[$t]->auto) > 5){$styl = "style=\"background-color: yellow;\" ";}
			$this->dat['trains_lst'] .= "<option ".$styl."value=\"".$myTrains[$t]->train_id."\">".substr($myTrains[$t]->train_desc,0,25)." (".$myTrains[$t]->train_id.")</option>";
		}
		$this->dat['trains_lst'] .= "<option value=\"\" style=\"background-color: brown; color: white;\">-- Other trains --</option>";		
		for($t=0;$t<count($notMyTrains);$t++){
			$styl = ""; if($notMyTrains[$t]->auto > 0 || strlen($notMyTrains[$t]->auto) > 5){$styl = "style=\"background-color: yellow;\" ";}
			$this->dat['trains_lst'] .= "<option ".$styl."value=\"".$notMyTrains[$t]->train_id."\">".substr($notMyTrains[$t]->train_desc,0,25)." (".$notMyTrains[$t]->train_id.")</option>";
		}
		*/
	}
	
	function transhipped_wbs($fld8){
		// Start Transhipped Waybills List
		$twbs = "";
		$fld8_t = explode("T",$fld8);
		$ts1 = "SELECT `waybill_num`,`id` FROM `ichange_waybill` WHERE `waybill_num` LIKE '".$fld8."T%' AND `waybill_num` != '".$fld8."'";
		$tq1 = (array)$this->Generic_model->qry($ts1);
		//while($tr1 = mysqli_fetch_array($tq1)){
		for($t=0;$t<count($tq1);$t++){
			$twbs .=  "&nbsp;".anchor("../waybill/edit/".$tq1[$t]->id, $tq1[$t]->waybill_num, 'title="'.$tq1[$t]->waybill_num.'"'); //<a href=\"../waybill/edit/".$tr1['waybill_num']."\">".$tr1['waybill_num']."</a>";
		}
		return $twbs;
		// End Transhiopped Waybills List
	}
	
	function prog_lst($wbdat){
			// Progress listing JSON to string.
			//$prog = @json_decode($wbdat, TRUE);
			$prog = $wbdat;
			$prog_d = "<p style=\"font-size: 15pt; font-weight: bold; color: red; text-align: center; padding: 10px;\">No progress reports on this waybill yet!</p>";
			if(count($prog) > 0){
			$prog_d = "<div style=\"display: table; width: 100%;\">";
			$prog_d .= "<div style=\"display: table-row;\">";
			$prog_d .= "<div style=\"display: table-cell;\" class=\"td_title\">Date / Time</div>";
			$prog_d .= "<div style=\"display: table-cell;\" class=\"td_title\">Details</div>";
			$prog_d .= "<div style=\"display: table-cell;\" class=\"td_title\">Location</div>";
			$prog_d .= "<div style=\"display: table-cell;\" class=\"td_title\">Train</div>";
			$prog_d .= "<div style=\"display: table-cell;\" class=\"td_title\">Status</div>";
			$prog_d .= "</div>";
			//for($p=count($prog)-1;$p>=0;$p=$p-1){
			for($p=0;$p<count($prog);$p++){
				$prog[$p] = (array)$prog[$p];
				$tc="td1"; if(floatval($p/2) == intval($p/2)){$tc="td2";}
				$prog_d .= "<div style=\"display: table-row;\">";
				$prog_d .= "<div style=\"display: table-cell;\" class=\"".$tc."\">".@$prog[$p]['date']."&nbsp;".@$prog[$p]['time']."<br />".@$prog[$p]['tzone']."</div>";
				$prog_d .= "<div style=\"display: table-cell;\" class=\"".$tc."\">".@$prog[$p]['text']."&nbsp;</div>";
				$prog_d .= "<div style=\"display: table-cell;\" class=\"".$tc."\">".@$prog[$p]['map_location']."&nbsp;</div>";
				$prog_d .= "<div style=\"display: table-cell;\" class=\"".$tc."\">".@$prog[$p]['train']."&nbsp;</div>";
				$prog_d .= "<div style=\"display: table-cell;\" class=\"".$tc."\">".@$prog[$p]['status']."&nbsp;</div>";
				$prog_d .= "</div>";
			}
			$prog_d .= "</div>";
			}
		return $prog_d;
	}
	
	function chk_auto($wb){
		//$s = "SELECT COUNT(`id`) AS `c` FROM `ichange_auto` WHERE `waybill_num` = '".$wb."'";
		$s = "SELECT * FROM `ichange_auto` WHERE `waybill_num` = '".$wb."'";
		$this->dat['auto_ent'] = (array)$this->Generic_model->qry($s);
		//$q = (array)$this->Generic_model->qry($s);
		//$v = $q[0]->c;
		//return $v;
	}

	function tranship($id=0){
		//$s3 = "SELECT `waybill_num` FROM `ichange_waybill` WHERE `waybill_num` LIKE '".$id."%'";
		$wb = (array)$this->Waybill_model->get_single($id,'id');
		$r3 = (array)$this->Waybill_model->get_allTranshipped($wb[0]->waybill_num);
		$flds_arr = $this->Waybill_model->get_fld_names();
		/*
		$q3 = mysqli_query($s3);
		$wb_arr = array();
		while($r3 = mysqli_fetch_array($q3)){$wb_arr[] = $r3['waybill_num'];}
		$t_wb = $id; if(strpos($t_wb,"T") < 1){$t_wb = $id."T";}
		for($i3=1;$i3<10;$i3++){
			if(!in_array($t_wb.$i3, $wb_arr)){
				$t_wb .= $i3;
				$i3 = 99;
			}
		}
		*/
		for($it=0;$it<count($r3);$it++){$wb_arr[] = $r3[$it]->waybill_num;}
		$t_wb = $wb[0]->waybill_num; if(strpos($t_wb,"T") < 1){$t_wb = $wb[0]->waybill_num."T";}
		for($i3=1;$i3<10;$i3++){
			if(!in_array($t_wb.$i3, $wb_arr)){
				$t_wb .= $i3;
				$i3 = 99;
			}
		}

		// Start get orig waybill data and manipulate
		//$s1 = "SELECT * FROM `ichange_waybill` WHERE `waybill_num` = '".$id."'";
		//$r1 = (array)$this->Generic_model->qry($s1);
		/*
		$q1 = mysqli_query($s1);
		$r1 = mysqli_fetch_assoc($q1);
		$r1['waybill_num'] = $t_wb;
		$r1['notes'] = "";
		$r1['date'] = date('Y-m-d');
		$r1['cars'] = "";
		$r1_prog = @json_decode($r1['progress'], true);
		$r1_max = count($r1_prog) - 1;
		$r1_last = $r1_prog[$r1_max];
		$r1_progress = array();
		$r1_progress[] = $r1_last;
		$r1_progress[] = array(
			'date' => date('Y-m-d'),
			'time' => date('H:i'), 
			'text' => "TRANSHIPPED FROM WB ".$id, 
			'waybill_num' => $t_wb, 
			'status' => "TRANSHIPPED", 
			'map_location' => "", 
			'tzone' => $_SESSION['_tz']
		);
		$r1['progress'] = json_encode($r1_progress);
		*/
		$r1 = (array)$wb[0];
		$r1['waybill_num'] = $t_wb;
		//$r1['notes'] = "";
		$r1['date'] = date('Y-m-d');
		$r1['cars'] = "";
		$r1_prog = @json_decode($r1['progress'], true);
		$r1_max = count($r1_prog) - 1;
		$r1_last = $r1_prog[$r1_max];
		$r1_progress = array();
		$r1_progress[] = $r1_last;
		// Added 2016-03-02			
		$prog_sql = "INSERT INTO `ichange_progress` SET 
			`date` = '".$r1_last['date']."', 
			`time` = '".$r1_last['time']."', 
			`text` = '".$r1_last['text']."', 
			`waybill_num` = '".$t_wb."', 
			`map_location` = '".$r1_last['map_location']."', 
			`status` = '".$r1_last['status']."', 
			`train` = '".$r1_last['train']."', 
			`rr` = '".$r1_last['rr']."', 
			`exit_location` = '".$r1_last['exit_location']."', 
			`tzone` = '".$r1_last['tzone']."', 
			`added` = '".date('U')."'";
		$this->Generic_model->change($prog_sql);

		$r1_progress[] = array(
			'date' => date('Y-m-d'),
			'time' => date('H:i'), 
			'text' => "TRANSHIPPED FROM WB ".$wb[0]->waybill_num, 
			'waybill_num' => $t_wb, 
			'status' => "TRANSHIPPED", 
			'map_location' => "", 
			'tzone' => @$_SESSION['_tz']
		);
		// Added 2016-03-02 			
		$prog_sql = "INSERT INTO `ichange_progress` SET 
			`date` = '".date('Y-m-d')."', 
			`time` = '".date('H:i')."', 
			`text` = 'TRANSHIPPED FROM WB ".$wb[0]->waybill_num."', 
			`waybill_num` = '".$t_wb."', 
			`map_location` = '', 
			`status` = 'TRANSHIPPED', 
			`tzone` = '".@$_SESSION['_tz']."', 
			`added` = '".date('U')."'";
		$this->Generic_model->change($prog_sql);
		
		$prog_sql = "";
		$r1['progress'] = json_encode($r1_progress);
		// End get orig waybill data and manipulate
		
		//echo "<pre>"; print_r($r1); echo "</pre>";
		//exit();

		// Start create new waybill
		$r1_kys = array_keys($r1);
		$s2 = "INSERT INTO `ichange_waybill` SET ";
		for($i2=1;$i2<count($r1_kys);$i2++){
			if($i2 > 1){$s2 .= ", ";}
			$s2 .= "`".$r1_kys[$i2]."` = '".$r1[$r1_kys[$i2]]."'";
		}
		//mysqli_query($s2);
		$this->Generic_model->change($s2);
		// End create new waybill

		// Start Update Orig Waybill
		$r1_prog[] = array(
			'date' => date('Y-m-d'), 
			'time' => date('H:i'), 
			'text' => "FREIGHT TRANSHIPPED TO WB ".$t_wb.". CAR EMPTY AND READY FOR RETURN JOURNEY.", 
			'waybill_num' => $id, 
			'status' => "UNLOADED", 
			'map_location' => "", 
			'tzone' => @$_SESSION['_tz']
		);
		$json_prog = json_encode($r1_prog);
		//$s4 = "UPDATE `ichange_waybill` SET `progress` = '".$json_prog."', `status` = 'UNLOADED', `lading` = 'MT' WHERE `waybill_num` = '".$id."'";
		$s4 = "UPDATE `ichange_waybill` SET `progress` = '".$json_prog."', `status` = 'UNLOADED', `lading` = 'MT' WHERE `id` = '".$id."'";
		//mysqli_query($s4);
		$this->Generic_model->change($s4);
		// End Update Orig Waybill

		// Redirect to SWAP orig / Dest, etc.
		//header("Location:edit.php?action=EDIT&type=WAYBILL&id=".$id."&swap=1&unsaved=1"); //edit.php?type=WAYBILL&id=".$id."&action=EDIT");
		header("Location:../../waybill/edit/".$id); //edit.php?type=WAYBILL&id=".$id."&action=EDIT");
	}
	
	function swap($id=0){
		//echo "UNTEST FUNCTION swap() IN waybill.php!"; exit();
		/*
		$swapLnk = "&nbsp;<a href=\"edit.php?action=EDIT&type=WAYBILL&id=".$fld8."\">Original origin / destination / lading</a>";
		$fld4_tmp = $fld4;
		$fld5_tmp = $fld5;
		$fld2_tmp = $fld2; 
		$fld3_tmp = $fld3;
		//$od_orig_ind_op = @$other_data['orig_ind_op'];
		//$od_dest_ind_op = @$other_data['dest_ind_op'];		

		$fld4 = $fld5_tmp;
		if(strlen($fld19) > 0){$fld5 = $fld19;}else{$fld5 = $fld4_tmp;} //$fld4_tmp;
		$fld2 = $fld3_tmp;
		$fld3 = $fld2_tmp;
		$other_data['orig_ind_op'] = @$other_data['dest_ind_op']; // = $od_dest_ind_op;
		unset($other_data['dest_ind_op']); // = $od_orig_ind_op;
		$fld11 = "MT";
		*/
		
		$orig_wb = (array)$this->Generic_model->qry("SELECT * FROM `ichange_waybill` WHERE `id` = '".$id."'");
		//echo "<pre>"; print_r($orig_wb); echo "</pre>";
		$indust_dest_name = $orig_wb[0]->indust_origin_name;
		$indust_origin_name = $orig_wb[0]->indust_dest_name;
		$rr_id_from = $orig_wb[0]->rr_id_to;
		$rr_id_to = $orig_wb[0]->rr_id_from;
		$other_data_json = @json_decode($orig_wb[0]->other_data,TRUE);
		$other_data_json['dest_ind_op'] = @$other_data_json['orig_ind_op'];
		unset($other_data_json['orig_ind_op']);
		$other_data = json_encode($other_data_json);
		
		$sql = "UPDATE `ichange_waybill` SET 
			`indust_origin_name` = '".$indust_origin_name."', 
			`indust_dest_name` = '".$indust_dest_name."', 
			`rr_id_to` = '".$rr_id_to."', 
			`rr_id_from` = '".$rr_id_from."', 
			`other_data` = '".$other_data."', 
			`lading` = 'MT' 
			WHERE `id` = '".$id."'";
		//echo $sql."<br />"; exit();
		$this->Generic_model->change($sql);
		
		//$other_data = json_decode($orig_wb[0]->other_data,TRUE);
		header("Location:../../waybill/edit/".$id);
	}
	
	function store($wb_id=0,$indust_id=0){
		// Store waybill at selected industry, close waybill, add progress report.
		$wbdat = (array)$this->Waybill_model->get_single($wb_id,"id");
		$indat = (array)$this->Generic_model->qry("SELECT * FROM `ichange_indust` WHERE `id` = '".$indust_id."'")	;	
		$dat = array('indust_id'=>$indust_id,'qty_cars'=>count($this->mricf->cars4RR4WB($this->arr['rr_sess'],$wb_id)),'commodity'=>$wbdat[0]->lading,'rr_sess'=>$this->arr['rr_sess']);
		$this->mricf->storeFreight($dat);

		$prog_sql = "INSERT INTO `ichange_progress` SET 
			`date` = '".date('Y-m-d')."', 
			`time` = '".date('H:i')."', 
			`text` = 'FREIGHT UNLOADED AND STORED AT ".$indat[0]->indust_name.". READY TO START EMPTY RETURN JOURNEY.', 
			`waybill_num` = '".$wbdat[0]->waybill_num."', 
			`map_location` = '".$indat[0]->town."', 
			`status` = 'UNLOADED', 
			`train` = '".$wbdat[0]->train_id."', 
			`tzone` = 'America/Chicago', 
			`added` = '".date('U')."'";
		$this->Generic_model->change($prog_sql);
		$s = "UPDATE `ichange_waybill` SET `train_id` = '', `status` = 'UNLOADED', `lading` = 'MT' WHERE `id` = '".$wb_id."'";
		$this->Generic_model->change($s);

		header("Location:".WEB_ROOT."/home");
	}

	function addAutoTrain($id=0){
		// Allow addition of extra Auto Trains
		$wb = (array)$this->Generic_model->qry("SELECT * FROM `ichange_waybill` WHERE `id` = '".$id."'"); // Waybill data
		
		// START PROCESS OF SUBMISSION
		if(isset($_POST['submit'])){
			$po = $_POST;
			$last_arr = explode("-",$po['last_action']);
			$po['start_date'] = intval(mktime(23,0,0,$last_arr[1],$last_arr[2],$last_arr[0]));
			$po['route_arr'] = json_decode($po['route_json'],TRUE);
			
			$ra_kys = array_keys($po['route_arr']);
			for($z=0;$z<count($ra_kys);$z++){
				$act_date = date('Y-m-d',$po['start_date']+($po['route_arr'][$ra_kys[$z]]*86400));
				$desc = "";
				if($z == count($ra_kys)-1){ $desc = "SPOTTED"; }
				$sql = "INSERT INTO `ichange_auto` SET 
					`act_date` = '".$act_date."', 
					`waypoint` = '".$ra_kys[$z]."', 
					`description` = '".$desc."', 
					`train_id` = '".$po['fld14'][0]."', 
					`rr_id` = '".$po['setRRAutos']."', 
					`waybill_num` = '".$wb[0]->waybill_num."'	";
				$this->Generic_model->change($sql);
			}
		}
		// END PROCESS OF SUBMISSION
		
		$prog = (array)$this->Generic_model->qry("SELECT `date`,`map_location`,`text` FROM `ichange_progress` WHERE `waybill_num` = '".$wb[0]->waybill_num."' ORDER BY `date` DESC LIMIT 1"); // Latest Progress report date
		$auto = (array)$this->Generic_model->qry("SELECT * FROM `ichange_auto` WHERE `waybill_num` = '".$wb[0]->waybill_num."' ORDER BY `act_date` DESC, `description` DESC"); // Latest Auto Train date
		//echo "<pre>"; print_r($wb); print_r($prog);print_r($auto);echo "</pre>";
		$last_date = date('Y-m-d');
		$last_location = "";
		$details = "";
		if(isset($prog[0]->date) && $prog[0]->date > $last_date){ 
			$last_date = $prog[0]->date; 
			$last_location = $prog[0]->map_location;
			$details = $prog[0]->text;
		}
		if($auto[0]->act_date > $last_date){ 
			$last_date = $auto[0]->act_date; 
			$last_location = $auto[0]->waypoint;
			$details = $auto[0]->train_id;
		}

		$this->arr['id'] = $id;
		$this->dat['field_names'] = array("waybill_num","date","indust_origin_name","indust_dest_name","return_to","status","routing","notes","last_action","last_location","details");
		$this->dat['data'][0] = array($wb[0]->waybill_num,$wb[0]->date,$wb[0]->indust_origin_name,$wb[0]->indust_dest_name,$wb[0]->return_to,$wb[0]->status,$wb[0]->routing,$wb[0]->notes,$last_date,$last_location,$details);
		$this->dat2 = array(
			'last_action' => $last_date,
			'id' => $id
		);
		$this->dat2['auto_data'] = $auto;

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		$this->load->view('view', $this->dat);
		$this->load->view('waybill_add_auto_train', $this->dat2);
		$this->load->view('footer');
	}
}
?>
