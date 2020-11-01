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
		
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Railroad_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Locomotives_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Waybill_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Train_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Switchlist";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";

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
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

		$this->arr['allRR'][$this->arr['rr_sess']]->show_affil_wb = 1; // Required to get affil locos using affil_ids method! Not required elsewhere in class.
		$this->my_rr_ids = $this->mricf->affil_ids($this->arr['rr_sess'],$this->arr['allRR']);
		//print_r($this->my_rr_ids);
		
		$this->dates = array('mth' => array("","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"));
	}

	public function index($id=0){
		header("Location:lst/".$id); exit();
		$this->lst($id=0);
	}
	
	public function lst($id=0){
		$this->arr['id'] = $id;
		$this->arr['jquery'] = "\$('.table1').DataTable({ 
			paging: false, 
			searching: false, 
			responsive: {
				details: {
					display: $.fn.dataTable.Responsive.display.childRowImmediate,
					type: ''
				}
			}, 
			info: false, 
			stateSave: false,
			order: [[ 2, 'asc' ]] 
			});\n";
		$this->arr['jquery'] .= "\$('.table2').DataTable({ 
			paging: false, 
			searching: false, 
			responsive: true, 
			info: false, 
			stateSave: false
			});\n";
		$this->arr['jquery'] .= "\$('.table3').DataTable({ 
			paging: false, 
			searching: false, 
			responsive: true, 
			info: false, 
			stateSave: false
			});\n";
		$this->arr['jquery'] .= "\$('#addWBSearch').click(function(){ 
			\$('#searchtable1popup').modal();
			\$('#searchtable1popup').html('Loading...');
			var p1 = '".WEB_ROOT.INDEX_PAGE."/search/waybillSummarySW/0/".$this->arr['rr_sess']."/".$id."/add2SWxSearch/1';
			\$.get(p1,function(data1){ 
				\$('#searchtable1popup').html(data1);
				\$('#searchtable1').DataTable({ responsive: true, order: [[ 1, 'asc' ]] });\n
			});
			return false;
		}); \n";
		$this->arr['jquery'] .= "\$('#addCarSearch').click(function(){ 
			\$('#searchtable1popup').modal();
			\$('#searchtable1popup').html('Loading...');
			var p1 = '".WEB_ROOT.INDEX_PAGE."/search/carSummarySW/0/".$this->arr['rr_sess']."/".$id."/addC2SWxSearch/1';
			\$.get(p1,function(data1){ 
				\$('#searchtable1popup').html(data1);
				\$('#searchtable1').DataTable({ responsive: true, order: [[ 1, 'asc' ]] });\n
			});
			return false;
		}); \n";
		//$this->arr['pgTitle'] .= " - Switchlist";
		$randpos = array();
		$trdat = (array)$this->Train_model->get_single($id); // Single train indicated by `id`
		$trsdat = (array)$this->Train_model->get_all4RR_Sorted($this->arr['rr_sess'],'train_id',1);
		$rrdat = (array)$this->Railroad_model->get_allActive();
		$stodat = $this->mricf->getStoredIndust($this->arr['rr_sess']); //(array)$this->Generic_model->qry("SELECT `id`,`indust_name` FROM `ichange_indust` WHERE `storage` = '1' AND `rr` = '".$this->arr['rr_sess']."'");
		//echo "<pre>"; print_r($trdat); echo "</pre>";
		$this->traindat = $trdat;
		$op_days = "";
		if($trdat[0]->sun == 1){$op_days .= "<div class=\"wb_btn\">SUN</div>";}
		if($trdat[0]->mon == 1){$op_days .= "<div class=\"wb_btn\">MON</div>";}
		if($trdat[0]->tues == 1){$op_days .= "<div class=\"wb_btn\">TUE</div>";}
		if($trdat[0]->wed == 1){$op_days .= "<div class=\"wb_btn\">WED</div>";}
		if($trdat[0]->thu == 1){$op_days .= "<div class=\"wb_btn\">THU</div>";}
		if($trdat[0]->fri == 1){$op_days .= "<div class=\"wb_btn\">FRI</div>";}
		if($trdat[0]->sat == 1){$op_days .= "<div class=\"wb_btn\">SAT</div>";}
		$tr_opts = "<option value=\"".$trdat[0]->train_id."\">".$trdat[0]->train_id."</option>";
		for($trc=0;$trc<count($trsdat);$trc++){$tr_opts .= "<option value=\"".$trsdat[$trc]->train_id."\">".$trsdat[$trc]->train_id."</option>";}
		
		// Waypoints for train
		$waypoints = "";
		if(strlen($trdat[0]->waypoints) > 6){
			$wp_arr = @json_decode($trdat[0]->waypoints,true);
			for($wp=0;$wp<count($wp_arr);$wp++){
				$tm = ""; if($wp_arr[$wp]['TIME'] > 0){ $tm = " (".$wp_arr[$wp]['TIME'].")"; }
				$waypoints .= "<div class=\"wb_btn\" style=\"width: auto;\">".$wp_arr[$wp]['LOCATION'].$tm."</div>";
			}
		}

		$lo_opts = array('' => "Select one");
		$lo_tmp = (array)$this->Locomotives_model->getLocos4RR($this->arr['rr_sess'],array('rr','avail_to','loco_num'),$this->my_rr_ids,1);
		for($i=0;$i<count($lo_tmp);$i++){
			$who_owns = "This RR";
			if($lo_tmp[$i]->avail_to == 1 && $lo_tmp[$i]->rr != $this->arr['rr_sess']){$who_owns = "Affiliate RR";}
			if($lo_tmp[$i]->avail_to == 2 && $lo_tmp[$i]->rr != $this->arr['rr_sess']){$who_owns = "Other RR";}
			$lo_opts[$lo_tmp[$i]->loco_num] = $lo_tmp[$i]->loco_num." - ".$lo_tmp[$i]->model." (".$who_owns.")";
		}
		$lo_select = "<div style=\"background-color: antiquewhite; border: 1px solid brown; padding: 5px; margin: 5px; display: none\" id=\"loco_sel_div\">".
			"<strong>Change Loco for Switchlist</strong><br /><br />".
			form_open_multipart(WEB_ROOT.INDEX_PAGE.'/switchlist/chgloco').
			form_hidden('id',$id).
			form_dropdown("loco_select",$lo_opts,$trdat[0]->loco_num,'style="display: inline;"')."&nbsp;".
			form_submit("submit","Change").form_close()."<a href=\"javascript:{}\" onclick=\"document.getElementById('loco_sel_div').style.display = 'none';\">[ Close ]</a></div>";
		$wb_select = "<div id=\"add2SWLst\" style=\"background-color: antiquewhite; border: 1px solid brown; padding: 5px; margin: 5px; display: none\">
			<strong>Add A Waybill to Switchlist</strong><br /><div id=\"add2SWLst2\" style=\"display: block;\">This is the add waybill to switchlist list.</div>
			<a href=\"javascript:{}\" onclick=\"document.getElementById('add2SWLst').style.display = 'none';\">[ Close ]</a>
			</div>";
		$wb_select .= "<div id=\"addC2SWLst\" style=\"background-color: antiquewhite; border: 1px solid brown; padding: 5px; margin: 5px; display: none\">
			<strong>Add Car/s to Switchlist</strong><br /><div id=\"addC2SWLst2\" style=\"display: block;\">This is the add cars to switchlist list.</div>
			<a href=\"javascript:{}\" onclick=\"document.getElementById('addC2SWLst').style.display = 'none';\">[ Close ]</a>
			</div>";

		$this->trdat['field_names'] = array("Train ID / Motive Power", "Train Description", "Origin / Destination", "Waypoints", "Operation Days", "Direction", "Operation Notes");
		$this->trdat['data'][0]['train_id'] = "<span style=\"float: right;\" class=\"dontprint\">
			<a href=\"javascript:{}\" onclick=\"document.getElementById('loco_sel_div').style.display = 'block'; document.getElementById('add2SWLst').style.display = 'none'; document.getElementById('addC2SWLst').style.display = 'none';\">Change / Add Motive Power</a>&nbsp; 
			<a href=\"javascript:{}\" onclick=\"add2SW('".$id."');\">Add Waybill to Switchlist</a> 
			<a href=\"javascript:{}\" onclick=\"addC2SW('".$id."');\">Add Cars to Switchlist</a><br />
			<a href=\"javascript:{}\" id=\"addWBSearch\" class=\"searchLink\">Waybill Search & Add to SW</a>
			<a href=\"javascript:{}\" id=\"addCarSearch\" class=\"searchLink\">Car Search & Add to SW</a>
			</span>".
			$trdat[0]->train_id." / ".$trdat[0]->loco_num."&nbsp;".
			$lo_select.$wb_select;
		//$this->trdat['data'][0]['loco_num'] = $trdat[0]->loco_num;
		$this->trdat['data'][0]['train_desc'] = $trdat[0]->train_desc;
		//if(strlen($trdat[0]->location) < 1){$trdat[0]->location = $trdat[0]->destination;}
		$this->trdat['data'][0]['origin'] = "<span class=\"dontprint\" style=\"float: right; font-size: 13pt; font-weight: bold;\">Location: ".$trdat[0]->location."</span><div class=\"wb_btn\" style=\"width: auto;\">".$trdat[0]->origin."</div> -> <div class=\"wb_btn\" style=\"width: auto;\">".$trdat[0]->destination."</div>";
		$this->trdat['data'][0]['waypoints'] = $waypoints;
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
		$this->dat['fields'] 			= array('waybill_num', 'cars','sw_ord','info','lading','routing','rr_id_handling');
		$this->dat['field_names'] 		= array("Waybill No.", "Cars on waybill","Order","Details","Lading","Route","On Railroad");
		//$this->dat['field_styles']		= array(0 => "width: 180px;");
		$this->dat['field_classes']			= array(2 => "dontprint", 3 => "printwide");
		$this->dat['options']			= array();
		if($this->arr['rr_sess'] > 0){$this->dat['options']	= array('Edit' => "onclick:if(isNaN('[id]')){ alert('You cannot edit this as it is not a waybill.'); }else{ window.location = '../../waybill/edit/[id]'; }",
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
			$this->Waybill_model->get_carsOnAllMyWaybills($this->my_rr_ids);
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

			$latest_ux = 0;			
			for($i=0;$i<count($arrdat);$i++){
				// List of cars for Rr and affiliates
				// Cars for waybill, incl affiliates
				$cars = "";
				$cars_cntr = 0;
				$cars_arr = @json_decode($arrdat[$i]->cars,TRUE);
				if(!is_array($cars_arr)){ $cars_arr = array(); }
				for($c=0;$c<count($cars_arr);$c++){
					if(in_array($cars_arr[$c]['RR'],$this->my_rr_ids) && strlen($cars_arr[$c]['NUM']) > 0){
						$cars_cntr++;
						$cars .= $cars_arr[$c]['NUM']." (".$cars_arr[$c]['AAR'].") [".$this->arr['allRR'][$cars_arr[$c]['RR']]->report_mark."]<br />";
					}
				}						
				
				// Progress entries array
				//$prog = @json_decode($arrdat[$i]->progress,TRUE);
				$last_prog_sql = "SELECT * FROM `ichange_progress` WHERE `waybill_num` = '".$arrdat[$i]->waybill_num."' ORDER BY id DESC, date DESC, time DESC LIMIT 1";
				$prog_res = (array)$this->Generic_model->qry($last_prog_sql);
				$prog[0] = array(); if(isset($prog_res[0])){ $prog[0] = (array)$prog_res[0]; } //json_decode($this->waybills[$tmp]->progress, true);
				$map_loc = "";
				if(isset($prog[count($prog)-1]['map_location'])){
					if(strlen($prog[count($prog)-1]['map_location']) > 0 && strpos("Z".$arrdat[$i]->status,"AT") < 1){$map_loc = "<br />At: ".$prog[count($prog)-1]['map_location'];}
				}
				$last_prog = "";
				if(isset($prog[count($prog)-1])){
					$last_prog = @$prog[count($prog)-1]['date']."&nbsp;".@$prog[count($prog)-1]['time']." - ".@$prog[count($prog)-1]['text'];
				}
				
				// Listing of waybill details allocated to train
				$this->dat['data'][$i]['id']						= $arrdat[$i]->id;
				$this->dat['data'][$i]['waybill_num'] 		= $arrdat[$i]->waybill_num; //." (".$arrdat[$i]->sw_order.")";
				$this->dat['data'][$i]['move_to']				= "";

				// Get last progress date for waybill
				//$prog_dat_json = @json_decode($arrdat[$i]->progress,TRUE);
  	   		//$last_prog_date_arr = explode("-",$prog_dat_json[count($prog_dat_json)-1]['date']);
  	   		$last_prog_date_arr = array(); if(isset($prog[0]['date'])){ $last_prog_date_arr = explode("-",$prog[0]['date']); }
		  	   $last_prog_date_ux = 0; if(count($last_prog_date_arr) == 3){ $last_prog_date_ux = mktime(12,0,0,$last_prog_date_arr[1],$last_prog_date_arr[2],$last_prog_date_arr[0]); }
		  	   if($latest_ux < $last_prog_date_ux){$latest_ux = $last_prog_date_ux;}

				if(in_array($arrdat[$i]->rr_id_handling,$this->my_rr_ids)){
					$wb_affected_ids .= $arrdat[$i]->id.";";
					$indust_name = $this->mricf->qry("ichange_waybill", $arrdat[$i]->id, "id", "indust_dest_name");
					$this->dat['data'][$i]['waybill_num'] .= "<br /><span class=\"sw_hide\">".form_hidden('wb_id[]',$arrdat[$i]->id)."
						<select name=\"move_to_ind[]\" style=\"padding: 0px;\" onchange=\"hideEle('wbdisp".$i."'); var uli = document.getElementById('unload_in_".$arrdat[$i]->id."'); uli.style.display='none'; if(this.value=='UNLOADING'){uli.style.display = 'inline';}if(this.value.length > 0){document.getElementById('wbdisp".$i."').style.display = 'inline';}\">".$move_to_opts."
						<option value=\"LOADING\">Loading at ".substr($this->mricf->qry("ichange_waybill", $arrdat[$i]->id, "id", "indust_origin_name"),0,20)."...</option>\n
						<option value=\"UNLOADING\">Unloading at ".substr($indust_name,0,20)."...</option>\n";
					if(!in_array($arrdat[$i]->lading,array("","MT","EMPTY","MTY"))){ 
						$tmp = $stodat; // = (array)$this->Generic_model->qry("SELECT `id`,`indust_name` FROM `ichange_indust` WHERE `storage` = '1' AND `rr` = '".$this->arr['rr_sess']."'");
						for($ic=0;$ic<count($tmp);$ic++){ 
							$this->dat['data'][$i]['waybill_num'] .= "<option value=\"STORING:".$tmp[$ic]->id.":".$cars_cntr.":".$arrdat[$i]->lading.":".$this->arr['rr_sess']."\">Storing at ".substr($tmp[$ic]->indust_name,0,20)."...</option>\n";
						} 
					}
					$this->dat['data'][$i]['waybill_num'] .= "</select>";
					$this->dat['data'][$i]['waybill_num'] .= "<span id=\"wbdisp".$i."\" style=\"display: none;\">";
					$this->dat['data'][$i]['waybill_num'] .= "<span style=\"display: none;\" id=\"unload_in_".$arrdat[$i]->id."\"><br /><select name=\"uli[]\" style=\"font-size:8pt;\">".$uli_opts."</select></span>";
					$this->dat['data'][$i]['waybill_num'] .= "<br /><select name=\"move_to_dt[]\" style=\"font-size:8pt;\">".$this->dt_opts($last_prog_date_ux)."</select>";
					$this->dat['data'][$i]['waybill_num'] .= "&nbsp;<br /><select name=\"move_to_hr[]\" style=\"font-size:8pt;\">".$this->hr_opts()."</select>:";
					$this->dat['data'][$i]['waybill_num'] .= "<select name=\"move_to_mi[]\" style=\"font-size:8pt;\">".$this->mi_opts()."</select> ";
					//$this->dat['data'][$i]['waybill_num'] .= "</span>";
					$this->dat['data'][$i]['waybill_num'] .= "<select name=\"alloc_to_train[]\" style=\"font-size:8pt;\">".$tr_opts."</select> ";
					$this->dat['data'][$i]['waybill_num'] .= "<select name=\"alloc_to_rr[]\" style=\"font-size: 8pt;\"><option value=\"".$this->arr['rr_sess']."\" selected=\"selected\">".$this->arr['allRR'][$this->arr['rr_sess']]->report_mark."</option>".$this->mricf->rrOpts()."</select>";
					$this->dat['data'][$i]['waybill_num'] .= "</span>"; // end of wbdisp span
					$this->dat['data'][$i]['waybill_num'] .= "</span>"; // end of sw_hide span
					
					$sw_ord_opts = "";
					for($swo=0;$swo<100;$swo++){
						$sel = ""; $lab = ""; if($swo == $arrdat[$i]->sw_order){ $sel = " selected=\"selected\""; $lab = "Order: "; }
						$sw_ord_opts .= "<option value=\"".$swo."\"".$sel.">".$lab.$swo."</option>\n";
					}
					//$this->dat['data'][$i]['waybill_num'] .= "<select name=\"sw_order[]\">".$sw_ord_opts."</select>";
					$this->dat['data'][$i]['waybill_num'] .= "<br /><select name=\"sw_ord_".$i."\" onchange=\"window.location = '".WEB_ROOT.INDEX_PAGE."/switchlist/sword/".$id."/".$arrdat[$i]->id."/' + this.value;\">".$sw_ord_opts."</select>";
				}

				// Detect whether imag for industries exist, andif so create link to allow user to view the image.
				$origin_name = $arrdat[$i]->indust_origin_name; 
				if(strpos($arrdat[$i]->indust_origin_name,"]") > 0){
					$ind_name = explode("]",$arrdat[$i]->indust_origin_name);
					$ind_name[0] = str_replace("[","",$ind_name[0]);
					if(file_exists(DOC_ROOT."/indust_images/".$ind_name[0].".jpg")){ 
						$origin_name = "<span data-balloon=\"Click to view Industry image\" data-balloon-pos=\"right\" data-balloon-length=\"large\"><a href=\"javascript:{}\" onclick=\"window.open('".WEB_ROOT.INDEX_PAGE."/indust_images/".$ind_name[0].".jpg"."','','width=300,height=300');\">".$origin_name."</a></span>"; 
					}
				}
				$dest_name = $arrdat[$i]->indust_dest_name;
				if(strpos($arrdat[$i]->indust_dest_name,"]") > 0){
					$ind_name = explode("]",$arrdat[$i]->indust_dest_name);
					$ind_name[0] = str_replace("[","",$ind_name[0]);
					if(file_exists(DOC_ROOT."/indust_images/".$ind_name[0].".jpg")){ 
						$dest_name = "<span data-balloon=\"Click to view Industry image\" data-balloon-pos=\"right\" data-balloon-length=\"large\"><a href=\"javascript:{}\" onclick=\"window.open('".WEB_ROOT.INDEX_PAGE."/indust_images/".$ind_name[0].".jpg"."','','width=300,height=300');\">".$dest_name."</a></span>"; 
					}
				}
				
				$this->dat['data'][$i]['cars']				 	= $cars; //$arrdat[$i]->cars;
				$this->dat['data'][$i]['info']					= "<div style=\"display: inline-block; border: 1px solid red; background-color: antiquewhite; padding: 3px; float: right;\">".$arrdat[$i]->status.$map_loc."</div>";
				if(strlen($origin_name.$dest_name) > 0){ 
					$this->dat['data'][$i]['info'] .= "<div style=\"display: block; border-bottom: 1px solid #999; padding: 5px; margin: 2px;\">From ".$origin_name; 
					$this->dat['data'][$i]['info'] .= "<br />To ".$dest_name."</div>"; 
				}
					//if(strlen($origin_name.$dest_name) > 1){ $this->dat['data'][$i]['info'] .= "<hr />"; }
				if(strlen($last_prog) > 9){ $this->dat['data'][$i]['info'] .= "<div style=\"display: block; font-size: 9pt;border-bottom: 1px solid #999; padding: 5px; margin: 2px;\"<em>".$last_prog."</em></div>"; }
				$prog_locs_txt = "";				
				$prog_locs = (array)$this->Generic_model->qry("SELECT `map_location` FROM `ichange_progress` WHERE LENGTH(`map_location`) > 0 AND `waybill_num` = '".$arrdat[$i]->waybill_num."' ORDER BY date,time");
				for($pl=0;$pl<count($prog_locs);$pl++){
					$prev_prog_loc = ""; if(isset($prog_locs[($pl-1)]->map_location)){ $prev_prog_loc = $prog_locs[($pl-1)]->map_location; }
					if(strlen($prog_locs[$pl]->map_location) > 0 && $prog_locs[$pl]->map_location != $prev_prog_loc){ 
						$prog_locs_txt .= "[".$prog_locs[$pl]->map_location."] -> "; 
					}
				}
				if(strlen($prog_locs_txt) > 0){ $this->dat['data'][$i]['info'] .= "<div class=\"dontprint\" style=\"font-size: 9pt;border-bottom: 1px solid #999; padding: 5px; margin: 2px;\">Journey so far: ".$prog_locs_txt."</div>"; }
				if(strlen($arrdat[$i]->notes) > 0){			$this->dat['data'][$i]['info'] .= "<div style=\"display: block; font-size: 9pt;border-bottom: 1px solid #999; padding: 5px; margin: 2px;\"><em>".$arrdat[$i]->notes."</em></div>";}
				$sw_ord = 999; if(isset($arrdat[$i]->sw_order) && $arrdat[$i]->sw_order > -1){ $sw_ord = $arrdat[$i]->sw_order; }
				$this->dat['data'][$i]['routing']				= $arrdat[$i]->routing;
				$this->dat['data'][$i]['lading']				= $arrdat[$i]->lading;
				$this->dat['data'][$i]['sw_ord']				= $sw_ord;
				$this->dat['data'][$i]['rr_id_handling']		= ""; if(isset($this->arr['allRR'][$arrdat[$i]->rr_id_handling])){ $this->dat['data'][$i]['rr_id_handling'] = $this->arr['allRR'][$arrdat[$i]->rr_id_handling]->report_mark; }
				
				$this->dat['widths'] = array(2=>"8%", 3=>"45%", 4=>"4%" , 5=>"4%" , 6=>"10%", 7=>"8%");
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
			$this->flddat['fields'][] = "<div class=\"dontprint\">";
			$this->flddat['fields'][] = "<table class=\"table2\" style=\"width: 95%;\">
					<thead>
					<tr>
						<td class=\"td_title2\">Move to</td>
						<td class=\"td_title2\">Date/Time</td>
						<td class=\"td_title2\">Action</td>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td><select name=\"move_to\" style=\"font-size: 9pt; width: 95%;\">".$move_to_opts."</select></td>
						<td>
							<div style=\"display: inline-block;\"><select name=\"move_to_dt\" style=\"font-size:9pt;\">".$this->dt_opts($latest_ux)."</select></div>
							&nbsp;<div style=\"display: inline-block;\"><select name=\"move_to_hr\" style=\"font-size:9pt;\">".$this->hr_opts()."</select> : <select name=\"move_to_mi\" style=\"font-size:9pt;\">".$this->mi_opts()."</select></div>
						</td>
						<td>".form_submit('submit', 'Deliver Cars')."</td>
					</tr>
					</tbody>
				</table>";
			$this->flddat['fields'][] = "<br /><table class=\"table3\" style=\"width: 95%;\">
					<thead>
						<td class=\"td_title2\">Train Location</td>
						<td class=\"td_title2\">Action</td>
					</thead>
					<tbody>
					<tr>
						<td><input type=\"text\" name=\"tr_location\" style=\"width: 95%\" value=\"".$trdat[0]->location."\" onchange=\"document.tr_ind.tr_location.value = this.value;\" /></td>
						<td>".form_submit('submit', 'Change Location')."</td>
					</tr>
					</tbody>
				</table>";
			$this->flddat['fields'][] = form_close().
				"<span style=\"font-size: 10pt;\"><strong>To change only the Train Location:</strong> Enter the location and click the Change Location button.<br />
				<strong>To change the train location AND deliver cars:</strong> Select Deliver To options and click the Deliver Cars or Deliver Cars Individually buttons.</span>";
			$this->flddat['fields'][] = "</div>"; // Closing div.dontprint element.


			// Form for table contents
			$tri_attribs = array('name' => 'tr_ind');
			$this->dat['before_table'] = form_open_multipart('../switchlist/move_to_individual',$tri_attribs).
				form_hidden('train_id',$trdat[0]->train_id).
				form_hidden('id',$id).
				form_hidden('tr_location',$trdat[0]->location);
			$this->dat['after_table'] = form_submit('submit', 'Deliver Cars Individually').form_close();
			
		}
		

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		if($this->arr['rr_sess'] > 0){
			$this->load->view('view', $this->trdat);
			$this->load->view('fields', @$this->flddat);
			//$this->load->view('list', $this->dat);
			$this->load->view('table', $this->dat);
		}else{
			$this->load->view('not_allowed');
		}
		$this->load->view('footer');
	}
	
	public function edit($id=0){
		// Edit method - for editing records, not need for switchlist.
	}

	public function move_to(){
		// Move cars on waybill to wherever
		$this->arr = $_POST;
		$this->load->model('Generic_model','',TRUE);
		$this->Generic_model->change("UPDATE `ichange_trains` SET `location` = '".strtoupper($this->arr['tr_location'])."' WHERE `id` = '".$this->arr['id']."'");

		$wb_aff_id = explode(";",$this->arr['wb_affected_ids']);
		//echo "<pre>"; print_r($wb_aff_id);echo "</pre>";
		$this->arr['move_to'] = strtoupper($this->arr['move_to']);
		$this->email_txt = "";
		
		if(strlen($this->arr['move_to']) > 0){
		for($w=0;$w<count($wb_aff_id);$w++){
			// Progress report manipulation
			$prog = array(); //$this->mricf->progWB($wb_aff_id[$w]); - COMMENTED OUT 2016-03-02 JS
			/* DISABLED 2016-03-04 AS NOW IN ichange_progress TABLE!
			$prog[] = array(
				'date' => $this->arr['move_to_dt'], 
				'time' => $this->arr['move_to_hr'].":".$this->arr['move_to_mi'], 
				'text' => "CAR/S ON WAYBILL HAVE BEEN MOVED BY TRAIN <strong>".$this->arr['train_id']."</strong> AND ARE NOW LOCATED <strong>".$this->arr['move_to']."</strong> (v2.0)",
				'waybill_num' => 0,
				'map_location' => str_replace("AT ","",$this->arr['move_to']),
				'train' => $this->arr['train_id'],
				'status' => $this->arr['move_to']
			);
			*/
			$jprog = json_encode($prog);
			$s = "UPDATE `ichange_waybill` SET `status` = '".$this->arr['move_to']."', `progress` = '".$jprog."' WHERE `id` = '".$wb_aff_id[$w]."'";
			$this->Generic_model->change($s);

			// Added 2016-03-02 - The $prog[] creation above can be changed to single (ie, taken out of this FOR loop) after 2016-06-02				
			$prog_sql = "INSERT INTO `ichange_progress` SET 
				`date` = '".$this->arr['move_to_dt']."', 
				`time` = '".$this->arr['move_to_hr'].":".$this->arr['move_to_mi']."', 
				`text` = 'CAR/S ON WAYBILL HAVE BEEN MOVED BY TRAIN <strong>".$this->arr['train_id']."</strong> AND ARE NOW LOCATED <strong>".$this->arr['move_to']."</strong> (v2.0)', 
				`waybill_num` = '".$this->mricf->qry("ichange_waybill", $wb_aff_id[$w], "id", "waybill_num")."', 
				`map_location` = '".str_replace("AT ","",$this->arr['move_to'])."', 
				`status` = '".$this->arr['move_to']."', 
				`train` = '".$this->arr['train_id']."', 
				`tzone` = 'America/Chicago', 
				`added` = '".date('U')."'";
			$this->Generic_model->change($prog_sql);

			// Cars retreive and list			
			$cars_arr = $this->mricf->carsWB($wb_aff_id[$w]);
			$cars_txt = "";
			for($c=0;$c<count($cars_arr);$c++){
				if(strlen($cars_txt) > 0){$cars_txt .= "/ ";}
				$cars_txt .= $cars_arr[$c]['NUM']."(".$cars_arr[$c]['AAR'].") ";
			}
			if(strlen($cars_txt) > 0){$cars_txt .= " - ";}

			// Send email, if necessary
			$this->email_txt .= $this->arr['move_to_dt']." - ".$this->arr['move_to_hr'].":".$this->arr['move_to_mi']." - ".$cars_txt.$this->arr['move_to']."\n";
		}
		}
		if(strlen($this->email_txt) > 0){$this->email_sw_to_grp();}
		header("Location:../switchlist/lst/".$this->arr['id']);
		exit();

	}
	
	public function move_to_individual(){
		$this->arr = $_POST;
		$this->load->model('Generic_model','',TRUE);
		//echo "<pre>"; print_r($this->arr); echo "</pre>"; exit();
		$this->email_txt = "";
		$this->Generic_model->change("UPDATE `ichange_trains` SET `location` = '".strtoupper($this->arr['tr_location'])."' WHERE `id` = '".$this->arr['id']."'");
		
		for($w=0;$w<count($this->arr['wb_id']);$w++){
			if(strlen($this->arr['move_to_ind'][$w]) > 0){
				$wb_num = $this->mricf->qry("ichange_waybill", $this->arr['wb_id'][$w], "id", "waybill_num");
				$this->Generic_model->change("DELETE FROM `ichange_auto` WHERE `waybill_num` = '".$wb_num."'");
				$loc = str_replace(array("AT ","UNLOADING","LOADING"),"",$this->arr['move_to_ind'][$w]);
				$this->arr['move_to_ind'][$w] = strtoupper($this->arr['move_to_ind'][$w]);
				$txt = "CAR/S ON WAYBILL HAVE BEEN MOVED BY TRAIN <strong>".$this->arr['train_id']."</strong> AND ARE NOW LOCATED <strong>".$this->arr['move_to_ind'][$w]."</strong> (v2.0)";
				if($this->arr['move_to_ind'][$w] == "UNLOADING"){
					$ind = $this->mricf->qry("ichange_waybill", $this->arr['wb_id'][$w], "id", "indust_dest_name");
					$rr = $this->mricf->qry("ichange_waybill", $this->arr['wb_id'][$w], "id", "rr_id_to");
					$loc = $this->loc_qry($ind,$rr);
					$txt = "UNLOADING AT ".$ind;
					if($this->arr['uli'][$w] > 0){
						//$wb_num = $this->mricf->qry("ichange_waybill", $this->arr['wb_id'][$w], "id", "waybill_num");
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
				if(strpos("z".$this->arr['move_to_ind'][$w],"STORING") > 0){
					$this->mricf->storeFreight($this->arr['move_to_ind'][$w]);
					$tmp_arr = explode(":",$this->arr['move_to_ind'][$w]);
					$ind = $this->mricf->qry("ichange_indust", $tmp_arr[1], "id", "indust_name");
					$rr = $this->mricf->qry("ichange_waybill", $this->arr['wb_id'][$w], "id", "rr_id_from");
					$loc = $this->loc_qry($ind,$rr);
					$txt = "FREIGHT UNLOADED AND STORED AT ".$ind.". READY TO START EMPTY RETURN JOURNEY.";
					$this->arr['move_to_ind'][$w] = "UNLOADED";
					$this->arr['alloc_to_train'][$w] = "";
				}
							
				$prog = array(); //$this->mricf->progWB($this->arr['wb_id'][$w]); - COMMENTED OUT 2016-03-02 JS
				/* DISABLED 2016-03-04 AS NOW IN ichange_progress TABLE!
				$prog[] = array(
					'date' => $this->arr['move_to_dt'][$w], 
					'time' => $this->arr['move_to_hr'][$w].":".$this->arr['move_to_mi'][$w], 
					'text' => $txt,
					'waybill_num' => 0,
					'map_location' => $loc,
					'train' => $this->arr['train_id'],
					'status' => $this->arr['move_to_ind'][$w]
				);
				*/
				$jprog = json_encode($prog);

				// Added 2016-03-02 - The $prog[] creation above can be changed to single (ie, taken out of this FOR loop) after 2016-06-02				
				$prog_sql = "INSERT INTO `ichange_progress` SET 
					`date` = '".$this->arr['move_to_dt'][$w]."', 
					`time` = '".$this->arr['move_to_hr'][$w].":".$this->arr['move_to_mi'][$w]."', 
					`text` = '".$txt."', 
					`waybill_num` = '".$this->mricf->qry("ichange_waybill", $this->arr['wb_id'][$w], "id", "waybill_num")."', 
					`map_location` = '".$loc."', 
					`status` = '".$this->arr['move_to_ind'][$w]."', 
					`train` = '".$this->arr['train_id']."', 
					`tzone` = 'America/Chicago', 
					`added` = '".date('U')."'";
				$this->Generic_model->change($prog_sql);

				// Cars retreive and list			
				$cars_arr = $this->mricf->carsWB($this->arr['wb_id'][$w]);
				$cars_txt = "";
				for($c=0;$c<count($cars_arr);$c++){
					if(strlen($cars_txt) > 0){$cars_txt .= "/ ";}
					$cars_txt .= $cars_arr[$c]['NUM']."(".$cars_arr[$c]['AAR'].") ";
				}
				if(strlen($cars_txt) > 0){$cars_txt .= " - ";}

				//$this->email_txt .= $this->arr['move_to_dt'][$w]." - ".$this->arr['move_to_hr'][$w].":".$this->arr['move_to_mi'][$w]." - ".$cars_txt." - ".$txt."\n";
				$this->email_txt .= $this->arr['move_to_dt'][$w]." - ".$this->arr['move_to_hr'][$w].":".$this->arr['move_to_mi'][$w]." - ".$cars_txt.$loc."\n";
				$s = "UPDATE `ichange_waybill` SET `train_id` = '".$this->arr['alloc_to_train'][$w]."', `status` = '".$this->arr['move_to_ind'][$w]."', `rr_id_handling` = '".$this->arr['alloc_to_rr'][$w]."', `progress` = '".$jprog."' WHERE `id` = '".$this->arr['wb_id'][$w]."'";
				$this->Generic_model->change($s);
				//$this->Generic_model->change("DELETE FROM `ichange_auto` WHERE `waybill_num` = '".$wb_num."'");
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
		if(strpos("a".$id,"TR") > 0){
			$tmp_id = str_replace("TR","",$id);
			$tr = $this->Generic_model->qry("SELECT `trains_id` AS `id` FROM `ichange_tr_cars` WHERE `id` = '".$tmp_id."'");
			$this->Generic_model->change("DELETE FROm `ichange_tr_cars` WHERE `id` = '".str_replace("TR","",$id)."'");
		}else{
			$wb = $this->Generic_model->qry("SELECT `train_id` FROM `ichange_waybill` WHERE `id` = '".$id."'");
			$tr = $this->Generic_model->qry("SELECT `id` FROM `ichange_trains` WHERE `train_id` = '".$wb[0]->train_id."' LIMIT 1");
			$this->Generic_model->change("UPDATE `ichange_waybill` SET `train_id` = '' WHERE `id` = '".$id."'");
		}
		header("Location:../../switchlist/lst/".$tr[0]->id);
		exit();
	}
	
	public function chgloco(){
		$this->load->model('Generic_model','',TRUE);
		$s = "UPDATE `ichange_trains` SET `loco_num` = '".$_POST['loco_select']."' WHERE `id` = '".$_POST['id']."'";
		//echo $s; exit();
		$this->Generic_model->change($s);
		header("Location:".WEB_ROOT.INDEX_PAGE."/switchlist/lst/".$_POST['id']);
	}
	
	public function sword($trid=0,$wbid=0,$ord=0){
		// Sets order for selected waybill in train's swlist
		// $trid = train id (needed to re-load switchlist)
		// $wbid = ichange_waybills.id value.
		// $ord = sw_order value
		$this->load->model('Generic_model','',TRUE);
		$s = "UPDATE `ichange_waybill` SET `sw_order` = '".$ord."' WHERE `id` = '".$wbid."'";
		$this->Generic_model->change($s);
		header("Location:".WEB_ROOT.INDEX_PAGE."/switchlist/lst/".$trid);
	}
	
	public function add2SW($wb_id,$sw_id){
		// Adds waybill to switchlist and redirects to switchlist to display it.
		// $wb_id = ichange_waybill.id value.
		// $sw_id = ichange_trains.id value.

		// Get train id from ichange_trains table.
		$this->load->model('Generic_model','',TRUE);
		$s = "SELECT `train_id` FROM `ichange_trains` WHERE `id` = '".$sw_id."'";
		$tr = $this->Generic_model->qry($s);
		$train_id = $tr[0]->train_id;
		
		// Update waybill so it is on switchlist for train
		$t = "UPDATE `ichange_waybill` SET `train_id` = '".$train_id."' WHERE `id` = '".$wb_id."'";
		$this->Generic_model->change($t);
		
		header("Location:".WEB_ROOT.INDEX_PAGE."/switchlist/lst/".$sw_id);
	}

	public function addC2SW(){
		// Adds selected non-waybilled car/s to switchlist and redirects to switchlist to display it.
		// $sw_id = ichange_trains.id value.

		$sw_id = $_POST['sw_id'];
		// Get train id from ichange_trains table.
		$this->load->model('Generic_model','',TRUE);
		$s = "SELECT `train_id` FROM `ichange_trains` WHERE `id` = '".$sw_id."'";
		$tr = $this->Generic_model->qry($s);
		$train_id = $tr[0]->train_id;
		
		// Update waybill so it is on switchlist for train
		for($a=0;$a<count($_POST['Car2Add2SW']);$a++){
			$inst = "";
			if(isset($_POST['instructions'][$_POST['Car2Add2SW'][$a]])){ $inst = str_replace("'","",$_POST['instructions'][$_POST['Car2Add2SW'][$a]]); }
			$t = "INSERT INTO `ichange_tr_cars` SET `trains_id` = '".$sw_id."', `cars_id` = '".$_POST['Car2Add2SW'][$a]."', `instructions` = '".strtoupper($inst)."'";
			$this->Generic_model->change($t);
		}
		
		header("Location:".WEB_ROOT.INDEX_PAGE."/switchlist/lst/".$sw_id);
	}
	
	public function addC2SWxSearch(){
		$sw_id = $_POST['sw_id'];
		$car2add2SW = $_POST['car2add2SW'];
		$instructions = $_POST['instructions'];
		$t = "INSERT INTO `ichange_tr_cars` SET `trains_id` = '".$sw_id."', `cars_id` = '".$car2add2SW."', `instructions` = '".strtoupper($instructions)."'";
		$this->Generic_model->change($t);
		//header("Location:".WEB_ROOT.INDEX_PAGE."/switchlist/lst/".$sw_id);
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
	
	
	function dt_opts($ux=0){
		$dt = date('U');
		if($ux > $dt){$dt = $ux;}
		$dt_end = $dt + intval(15*86400); // 15 days
		$opts = "";
		$op_days = array();
		if($this->traindat[0]->sun == 1){$op_days[] = "Sun";}
		if($this->traindat[0]->mon == 1){$op_days[] = "Mon";}
		if($this->traindat[0]->tues == 1){$op_days[] = "Tue";}
		if($this->traindat[0]->wed == 1){$op_days[] = "Wed";}
		if($this->traindat[0]->thu == 1){$op_days[] = "Thu";}
		if($this->traindat[0]->fri == 1){$op_days[] = "Fri";}
		if($this->traindat[0]->sat == 1){$op_days[] = "Sat";}

		for($i=$dt;$i<$dt_end;$i=$i+86400){
			if(in_array(date('D',$i),$op_days) || count($op_days) == 0){
				$sel = ""; if($i == date('Y-m-d')){$sel = " selected=\"selected\"";}
				$opts .= "<option value=\"".date('Y-m-d',$i)."\"".$sel.">".date('Y-m-d (D)',$i)."</option>";
			}
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
		// $ind = industry (name OR indust.id) to get town/location for
		// $rr = railroad id that serves the industry
		$tmp = (array)$this->Generic_model->qry("SELECT `town` FROM `ichange_indust` WHERE (`indust_name` LIKE '%".$ind."%' OR `id` = '".$ind."') AND `rr` = '".$rr."'");
		if(isset($tmp[0]->town)){$loc = @$tmp[0]->town;}
		return @$loc;
	}

	function email_sw_to_grp(){
		// Sends an email to MRICC group
		$subject = 'Train '.$this->arr['train_id'].' moved cars #SwitchlistUpdate #MRICF'; //#mricf_switchlist_update';
		$message = "Train ".$this->arr['train_id']." has moved the following cars\n\n".$this->email_txt;

		$message .= "--------------------\n";
		$message .= "MRICF V2.1 emailer";
		
		$email_to_arr = array('MRICC@groups.io');
		$this->email->from('mricf@stanfordhosting.net', 'MRICF');
		$this->email->reply_to('mricf@stanfordhosting.net', 'MRICF');
		$this->email->to($email_to_arr); //$this->email->to('MRICC@yahoogroups.com');

		$this->email->subject($subject);
		$this->email->message($message);

		$this->email->send();
		//echo nl2br($message);
	}


}
?>
