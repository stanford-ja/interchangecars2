<?php
class Home extends CI_Controller {

	var $whr = "";
	var $content = array('html' => '','phtml' => '', 'rhtml' => '', 'thtml' => '', 'ahtml' => '', 'shtml' => "", 'mhtml' => "", 'ghtml' => "");
	var $waybills = array();
	var $porders = array();
	var $myCars = array();
	var $horiz_loc = 120;
	var $my_rr_ids = array();
	
	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!

		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('mricf');
		$this->load->library('dates_times');

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Waybills List";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		$this->arr['affil'] = array();
		
		// Security
		//echo "rr_sess = ".$this->input->cookie('rr_sess', TRUE);
		if($this->input->cookie('rr_sess')){$this->arr['rr_sess'] = $this->input->cookie('rr_sess', TRUE);}
		
		// Load generic model for custom queries
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
		
		// Railroad array set up
		$this->load->model('Railroad_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->arr['myRR'] = $this->Railroad_model->get_single($this->arr['rr_sess']);

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
		//echo "<pre>"; print_r($this->arr['allRR']); echo "</pre>";
				
		// Cars data for railroad logged in as
		$this->load->model('Cars_model','',TRUE); // Database connection! TRUE means connect to db.
		$ca = (array)$this->Cars_model->getCars4RR($this->arr['rr_sess']);
		//$cars_arr = array();
		//echo "<pre>";print_r($ca);echo "</pre>";
		//echo "<hr />";
		for($i=0;$i<count($ca);$i++){
			//$this->arr['myCars'][$ca[$i]->car_num] = $ca[$i];
			$this->myCars[$ca[$i]->car_num] = $ca[$i];
		} 
		//echo "<pre>";print_r($this->myCars);echo "</pre>";
		
		// Get train records
		$s = "SELECT `ichange_trains`.* FROM `ichange_trains` WHERE `ichange_trains`.`railroad_id` = '".$this->arr['rr_sess']."' ORDER BY `ichange_trains`.`train_id`";
		$this->myTrains = (array)$this->Generic_model->qry($s);
		
		// Create various selector options
		$this->trains_opts_lst = $this->mricf->trainOpts(array('rr' => $this->arr['rr_sess'], 'auto' => "N", 'onlyrr' => 1));
		$this->railroad_opts_lst = $this->mricf->rrOpts();

	}

	public function index(){
		//	Generate list of waybills for rr logged in as
		$this->load->model('Waybill_model','',TRUE);

		// Generate Afil WB list (if applicable)
		//echo "<pre>"; print_r($this->arr['myRR'][0]); echo "</pre>";
		$this->my_rr_ids = $this->mricf->affil_ids($this->arr['rr_sess'],$this->arr['allRR']);
		/*
		$this->my_rr_ids = array($this->arr['rr_sess']);
   	if(@$this->arr['myRR'][0]->show_affil_wb == 1){
   		$myRRs_kys = explode(";", $this->arr['myRR'][0]->affiliates);
    		for($i=1;$i<count($this->arr['allRR']);$i++){
    			if(in_array(@$this->arr['allRR'][$i]->report_mark,$myRRs_kys)){
	    			//$this->whr .= " OR (".$this->allHomeWBs($i,@$this->arr['allRR'][$i]->report_mar).")";
	    			$this->my_rr_ids[] = $i;
	    		}
    		}
    	}
    	*/

		// Waybill array set up
		$this->wbs_all = array();
		$this->pos_all = array();
		if($this->arr['rr_sess'] == 0){
			$this->wbs_all[] = $this->Waybill_model->get_latest_entries(20);
		}else{
			if(isset($_POST['search_for'])){
				$this->wbs_all[] = $this->Generic_model->get_search_results($_POST['search_for'],$_POST['search_in'],"ichange_waybill");				
			}else{
				for($rid=0;$rid<count($this->my_rr_ids);$rid++){
					//$this->allHomeWBs($this->arr['rr_sess'],$this->arr['myRR'][0]->report_mark);
					$this->allHomeWBs($this->my_rr_ids[$rid],$this->arr['allRR'][$this->my_rr_ids[$rid]]->report_mark);
					$this->wbs_all[] = $this->Waybill_model->get_allOpenHome($this->whr);
				}
				$this->pos_all = $this->Waybill_model->get_POrders();
			}
		}

		$wb_arr = array();
		for($w=0;$w<count($this->wbs_all);$w++){
			for($i=0;$i<count($this->wbs_all[$w]);$i++){
				$wb_arr[] = $this->wbs_all[$w][$i];
			}
		}
		//$this->arr['waybills'] = $wb_arr;
		$this->waybills = $wb_arr;

		//$po_arr = array();
		for($i=0;$i<count($this->pos_all);$i++){
			//$po_arr[] = $this->pos_all[$i];
			$this->porders[] = $this->pos_all[$i];
		}
		//$this->arr['porders'] = $po_arr;
		//$this->porders = $po_arr;
		$this->view0_build(); // Will be replaced with logic to view specific view setting for RR logged in as
		if($this->arr['rr_sess'] > 0){
			$this->porders_build();
			$this->rrlst_build();
			$this->trlst_build();
			$this->afillst_build();
			$this->search_build();
			$this->mess_build();
			$this->home_view_settings();
			$this->genload_build();
		}
		$this->view();
	}

	function view0_build(){
		// Home 0 view html generation
		$this->content['html'] = "<div style=\"width: 100%;\">".form_open_multipart("bulk_update")."<input type=\"checkbox\" name=\"do_bulk\" id=\"do_bulk\" value=\"1\" onchange=\"hideEle('ubu'); if(this.checked === true){document.getElementById('ubu').style.display = 'inline';}\" />Do bulk update (not individual selections!)";
		$this->content['html'] .= "<span style=\"display: none; float: right;\" id=\"ubu\">".form_submit("bulk","Update Bulk")."</span>";
		// Start FOR loop.
		for($tmp=0;$tmp<count($this->waybills);$tmp++){
			$td_cla = "td2";
			if(is_int($tmp/2)){$td_cla = "td1";}
			$this->latest_prog($tmp); // Latest progress report from JSON data
			$this->dt_styling(); // Waybill info styling
			$wb_cars = @json_decode($this->waybills[$tmp]->cars, true); // Cars from JSON array
	
			//$wb_lst = "";
			$cols_2_span = 5;
			$this->content['html'] .= "<div style=\"display: table; width: 100%; border-top: 2px solid black;\"><div style=\"display: table-row;\">";
			$this->content['html'] .= "<div style=\"display: table-cell; width: 7%;\" class=\"".$td_cla."\"><a href=\"waybill/edit/".$this->waybills[$tmp]->id."\">".$this->waybills[$tmp]->waybill_num."</a> <span style=\"background-color: yellow; font-weight: bold;\">".$this->waybills[$tmp]->waybill_type."</span><br />".$this->waybills[$tmp]->date."&nbsp;</div>\n";
			//$this->content['html'] .= "<td valign=\"top\" class=\"".$td_cla."\">$fld1</td>\n";
			$this->content['html'] .= "<div style=\"display: table-cell; width: 22%;\" class=\"".$td_cla."\"><span class=\"tiny_txt\">From: </span><br />".$this->waybills[$tmp]->indust_origin_name."&nbsp;</div>\n";
			$this->content['html'] .= "<div style=\"display: table-cell; width: 22%;\" class=\"".$td_cla."\"><span class=\"tiny_txt\">To: </span><br />".$this->waybills[$tmp]->indust_dest_name."&nbsp;</div>\n";
			$this->content['html'] .= "<div style=\"display: table-cell; width: 20%;\" class=\"".$td_cla."\"><span class=\"tiny_txt\">Return To: </span><br />".$this->waybills[$tmp]->return_to."&nbsp;</div>\n";
			$this->content['html'] .= "<div style=\"display: table-cell;\" class=\"".$td_cla."\"><span class=\"tiny_txt\">Status: </span><br />".$this->waybills[$tmp]->status."&nbsp;</div>\n";
			$this->content['html'] .= "<div style=\"display: table-cell; text-align: right; width: 10%; height: 10px;\" id=\"options".$this->waybills[$tmp]->id."\" class=\"".$td_cla."\">";

			if(@$icr == 1 && isset($_COOKIE['rr_sess'])){$this->content['html'] .= "<a href=\"save.php?type=RR2WB&id=$fld8&rr=".$allRR[$_COOKIE['rr_sess']]->report_mark."\"><span style=\"white-space: nowrap;\">Add RR to Routing</span></a><br />";}
				
			//if($icr == 0){$this->content['html'] .= "<a style=\"font-size: 10pt;\" href=\"index.php?hde=".$id."\">Hide (for 24hrs)</a>";}
			if(@$icr == 0){
				/*
				$this->content['html'] .= "<select name=\"hde\" style=\"font-size: 8pt;\" onchange=\"window.location = 'index.php?hde=".$this->waybills[$tmp]->id."&hrs=' + this.value;\">
					<option value=\"0\">Hide for</option>
					<option value=\"1\">1 hr</option>
					<option value=\"3\">3 hr</option>
					<option value=\"6\">6 hr</option>
					<option value=\"9\">9 hrs</option>
					<option value=\"12\">12 hrs</option>
					<option value=\"24\">1 day</option>
					</select>";
				*/
			}
			$this->content['html'] .= "&nbsp;</div></div></div><div style=\"display: block;\" class=\"".$td_cla."\">";
			$this->wb_lnk_mess($tmp); // Links for each waybill displayed

			$this->content['html'] .= "<span class=\"routing\">Routing: ".$this->waybills[$tmp]->routing."</span><br />";
			//$this->content['html'] .= "<select name=\"tr_sel\" style=\"font-size: 8pt; height: 24px;\"><option value=\"".@$this->waybills[$tmp]->train_id."\">".@$this->waybills[$tmp]->train_id."</option>".$this->trains_opts_lst."</select></span><br />";
			if($this->arr['rr_sess'] > 0){
				$this->content['html'] .= $this->wb_update_frm($tmp); // Update form for each waybill displayed
			}
			$this->content['html'] .= "&nbsp;</div>\n";
			$this->content['html'] .= "<div style=\"display: block;\" class=\"".$td_cla."\">";
			if(strlen($this->waybills[$tmp]->lading) > 0){
				$this->content['html'] .= "<div class=\"lading\">".$this->waybills[$tmp]->lading;
				$this->content['html'] .= "</div>";
			}
			$this->content['html'] .= "<span class=\"progress\">".$this->fld1_1." ".$this->fld1_5.$this->fld1_4." - ".$this->fld1_2."</span>";
			$this->carNum = $wb_cars;
			$this->wb_cars_display(); // Cars displayed for Waybill - text & image/s
			//if(strlen($this->fld1_3) > 0){$this->content['html'] .= "<div id=\"mapcanvas".$fld8."\" style=\"width: 400px; height: 300px; display:none;\"><br /><a href=\"\" onClick=\"document.getElementById('mapcanvas".$fld8."').style.display = 'none'\">Close</a></div>";}
			if(strlen($this->waybills[$tmp]->notes) > 2){$this->content['html'] .= "<div class=\"notes\">".$this->waybills[$tmp]->notes."</div>";}
			$this->content['html'] .= "</div>\n";
			$this->content['html'] .= "</div>";
		} // End FOR loop??
		$this->content['html'] .= form_close();
	}

	function porders_build(){
		// Builds html for purchase orders.
		$this->content['phtml'] = "<div class=\"box1\" style=\"left: ".$this->horiz_loc."px\">"; // 120px
		$this->content['phtml'] .= "&nbsp;<a href=\"#\" id=\"po_expand\"><strong>P/Orders</strong></a>&nbsp;<a href=\"#\" id=\"po_shrink\">Shrink</a><br />";
		$this->content['phtml'] .= "<div id=\"pos\" style=\"display: none;\">";
		for($p=0;$p<count($this->porders);$p++){
			$rr_from = "??"; if(isset($this->arr['allRR'][$this->porders[$p]->rr_id_from]->report_mark)){$rr_from = $this->arr['allRR'][$this->porders[$p]->rr_id_from]->report_mark;}
			$rr_to = "??"; if(isset($this->arr['allRR'][$this->porders[$p]->rr_id_to]->report_mark)){$rr_to = $this->arr['allRR'][$this->porders[$p]->rr_id_to]->report_mark;}
			$this->content['phtml'] .= "Rec # <a href=\"waybill/edit/".$this->porders[$p]->id."\">".$this->porders[$p]->waybill_num."</a><br />";
			$this->content['phtml'] .= $this->porders[$p]->lading."<br />".$rr_from." to ".$rr_to."<hr />";
		}
		$this->content['phtml'] .= "</div>";
		$this->content['phtml'] .= "</div>";
		$this->horiz_loc = $this->horiz_loc + 110;
	}
	
	function rrlst_build(){
		// Builds html for railroad listing.
		$this->content['rhtml'] = "<div class=\"box1\" style=\"left: ".$this->horiz_loc."px;\">"; // 230px
		$this->content['rhtml'] .= "&nbsp;<a href=\"#\" id=\"rr_expand\"><strong>Railroads</strong></a>&nbsp;<a href=\"#\" id=\"rr_shrink\">Shrink</a><br />";
		$this->content['rhtml'] .= "<div id=\"rrs\" style=\"display: none;\">";
		$max_id_qry = $this->Generic_model->qry("SELECT MAX(`id`) AS `max_id` FROM `ichange_rr`");
		$max_id = $max_id_qry[0]->max_id;
  		for($i=1;$i<=$max_id;$i++){
  			if(isset($this->arr['allRR'][$i])){
  				if($this->arr['allRR'][$i]->inactive != 1){
	  				$this->content['rhtml'] .= "<span style=\"float: right; font-size: 13pt; font-weight: bold; background-color: #ccc;\">&nbsp;id: ".@$this->arr['allRR'][$i]->id."&nbsp;</span>";
					if(@$this->arr['allRR'][$i]->id == $this->arr['rr_sess'] && $this->arr['rr_sess'] > 0){$this->content['rhtml'] .= "&nbsp;&nbsp;<a href=\"rr/edit/".$this->arr['allRR'][$i]->id."\">Edit</a>";}
					$this->content['rhtml'] .= "&nbsp;&nbsp;<a href=\"rr/view/".$this->arr['allRR'][$i]->id."\">View</a>";
					$this->content['rhtml'] .= "<br />".@$this->arr['allRR'][$i]->report_mark." - ";
					$this->content['rhtml'] .= @$this->arr['allRR'][$i]->rr_name."<br />";
					$this->content['rhtml'] .= @$this->arr['allRR'][$i]->owner_name;
					if(@$this->arr['allRR'][$i]->last_act > 0){$this->content['rhtml'] .= "&nbsp;&nbsp;(".date('Y-m-d',@$this->arr['allRR'][$i]->last_act).")";}
					if(@$this->arr['allRR'][$i]->common_flag == 1){$this->content['rhtml'] .= "<br />&nbsp;&nbsp;<strong>(COMMON RR)</strong>";}
					$this->content['rhtml'] .= "<hr />";
				}
			}
		}
		$this->content['rhtml'] .= "</div>";
		$this->content['rhtml'] .= "</div>";
		$this->horiz_loc = $this->horiz_loc + 115;
	}

	function trlst_build(){
		// Builds html for railroad listing.
		$this->content['thtml'] = "<div class=\"box1\" style=\"left: ".$this->horiz_loc."px;\">"; // 345px
		$this->content['thtml'] .= "&nbsp;<a href=\"#\" id=\"tr_expand\"><strong>Trains</strong></a>&nbsp;<a href=\"#\" id=\"tr_shrink\">Shrink</a><br />";
		$this->content['thtml'] .= "<div id=\"trs\" style=\"display: none;\">";
		//echo "<pre>"; print_r($this->myTrains); echo "</pre>";
		//echo "<pre>"; print_r($this->wbs_all); echo "</pre>";
  		for($i=0;$i<count($this->myTrains);$i++){
  			$tr_wbs = 0;
  			for($r=0;$r<count($this->wbs_all);$r++){
  				for($w=0;$w<count($this->wbs_all[$r]);$w++){
  					if(@$this->wbs_all[$r][$w]->train_id == $this->myTrains[$i]->train_id){$tr_wbs++;}
  				}
  			}
  			$wb_num = ""; if($tr_wbs > 0){$wb_num = " (".$tr_wbs.")";}
			$this->content['thtml'] .= $this->myTrains[$i]->train_id.$wb_num."&nbsp;&nbsp;<a href=\"switchlist/lst/".$this->myTrains[$i]->id."\">S/list</a>";
			$this->content['thtml'] .= "<hr />";
		}
		$this->content['thtml'] .= "</div>";
		$this->content['thtml'] .= "</div>";
		$this->horiz_loc = $this->horiz_loc + 95;
	}

	function afillst_build(){
		// Builds html for railroad listing.
		$this->content['ahtml'] = "<div class=\"box1\" style=\"left: ".$this->horiz_loc."px;\">"; // 440px;
		$this->content['ahtml'] .= "&nbsp;<a href=\"#\" id=\"af_expand\"><strong>Affiliates</strong></a>&nbsp;<a href=\"#\" id=\"af_shrink\">Shrink</a><br />";
		$this->content['ahtml'] .= "<div id=\"afs\" style=\"display: none;\">";
		$afils = explode(";",$this->arr['myRR'][0]->affiliates);
		for($i=0;$i<count($afils);$i++){
			//$this->arr['allRRRepMark'];
			if(isset($this->arr['allRRRepMark'][$afils[$i]])){
				$this->content['ahtml'] .= $afils[$i]."&nbsp;<a href=\"login/switch_to/".@$this->arr['allRRRepMark'][$afils[$i]]."\">Switch to</a>";
				//$this->content['ahtml'] .= $this->arr['allRRRepMark'][$afils]."&nbsp;&nbsp;<a href=\"trains/switchlist/".$this->myTrains[$i]->id."\">S/list</a>";
				$this->content['ahtml'] .= "<hr />";
			}
		}
		$this->content['ahtml'] .= "</div>";
		$this->content['ahtml'] .= "</div>";
		$this->horiz_loc = $this->horiz_loc + 110;
	}

	function search_build(){
		// Builds html for railroad listing.
		$this->content['shtml'] = "<div class=\"box1\" style=\"left: ".$this->horiz_loc."px;\">"; // 553px;
		if(isset($_POST['search_for'])){$this->content['shtml'] .= anchor("../home","My WBs");}
		else{
			$this->content['shtml'] .= "&nbsp;<a href=\"#\" id=\"search_expand\"><strong>Search</strong></a>&nbsp;<a href=\"#\" id=\"search_shrink\">Shrink</a><br />";
			$this->content['shtml'] .= "<div id=\"search\" style=\"display: none;\">";
		
			$search_opts = array('waybill_num' => "Waybill Number", 'lading' => "Lading", 'indust_origin_name' => "Origin Industry", 'indust_dest_name' => "Destination Industry", 'cars' => "Cars");
			$this->content['shtml'] .= form_open_multipart("../home");
			$this->content['shtml'] .= "For ".form_input('search_for')."<br />";
			$this->content['shtml'] .= "In ".form_dropdown('search_in',$search_opts);
			$this->content['shtml'] .= " ".form_submit('submit','Search');
			$this->content['shtml'] .= form_close();

			$this->content['shtml'] .= "</div>";
		}
		$this->content['shtml'] .= "</div>";
		$this->horiz_loc = $this->horiz_loc + 100;
	}

	function mess_build(){
		// Builds html for messages listing.
		$tmp = "";
		$cntr=0;
		for($ri=0;$ri<count($this->my_rr_ids);$ri++){
			$wbdat = (array)$this->Waybill_model->get_messages(0,$this->my_rr_ids[$ri]);
			for($i=0;$i<count($wbdat);$i++){
				$mess = @json_decode($wbdat[$i]->messages);
				for($m=0;$m<count($mess);$m++){
					if(isset($mess[$m]->datetime)){
						if($mess[$m]->torr == $this->my_rr_ids[$ri]){
						$m = (array)$mess[$m];
						$tmp .= "<a href=\"messaging/lst/".$wbdat[$i]->id."\">".$wbdat[$i]->waybill_num."</a> - ".$this->wb_message_details($m);
						$tmp .= "<hr />";
						$cntr++;
						}
					}
				}
			}
		}
		if($cntr > 0){
			$this->content['mhtml'] = "<div class=\"box1\" style=\"left: ".$this->horiz_loc."px; background-color: yellow; max-width: 270px;\">"; // 655px
			$this->content['mhtml'] .= "&nbsp;<a href=\"#\" id=\"me_expand\"><strong>Messages (".$cntr.")</strong></a>&nbsp;<a href=\"#\" id=\"me_shrink\">Shrink</a><br />";
			$this->content['mhtml'] .= "<div id=\"mes\" style=\"display: none;\">";
			$this->content['mhtml'] .= $tmp."</div>";
			$this->content['mhtml'] .= "</div>";
			$this->horiz_loc = $this->horiz_loc + 135;
		}
	}

	function genload_build(){
		// Builds html for generated loads listing.
		if(@$this->arr['myRR'][0]->show_generated_loads == 1){
			$two_weeks_ago = date('U') - (60*60*24*14);
			$sql = "SELECT * FROM `ichange_generated_loads` WHERE `added` > '".$two_weeks_ago."' AND `railroad` = '".$this->arr['rr_sess']."'";
			$gldat = (array)$this->Generic_model->qry($sql);
			$tmp = "";
			$cntr=0;
			for($i=0;$i<count($gldat);$i++){
				$tmp .= "<span style=\"background-color: yellow; float: right;\">&nbsp;<a href=\"javascript:{}\" onclick=\"gl_del(".$gldat[$i]->id.")\">[Del]</a>&nbsp;</span><a href=\"javascript:{}\"  onclick=\"gl_cre(".$gldat[$i]->id.")\">".$gldat[$i]->commodity."</a> - ".$gldat[$i]->date_human." - avail @ ".$gldat[$i]->orig_industry."<hr />";
				$cntr++;
			}
			if($cntr > 0){
				$this->content['ghtml'] = "<div class=\"box1\" style=\"left: ".$this->horiz_loc."px; background-color: red; \">"; // 850px;
				$this->content['ghtml'] .= "&nbsp;<a href=\"#\" id=\"gl_expand\"><strong>Generated Loads (".$cntr.")</strong></a>&nbsp;<a href=\"#\" id=\"gl_shrink\">Shrink</a><br />";
				$this->content['ghtml'] .= "<div id=\"genl\" style=\"display: none;\">";
				$this->content['ghtml'] .= $tmp."</div>";
				$this->content['ghtml'] .= "</div>";
				$this->horiz_loc = $this->horiz_loc + 200;
			}
		}
	}
	
	public function view(){
		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		//$this->load->view('home0', $this->arr);
		$this->load->view('home', $this->content);
		$this->load->view('footer');
	}

    function allHomeWBs($rr=0,$rep_mark=""){
    	$this->whr = "(`rr_id_to` = '".$rr."' OR `rr_id_from` = '".$rr."' OR `rr_id_handling` = '".$rr."' OR `routing` LIKE '%".$rep_mark."%')";
    	if($this->arr['allRR'][$rr]->show_allocto_only == 1){
    		$this->whr = "`rr_id_handling` = '".$rr."'";
    	}
    	
    	$whr = "";
    	if($this->arr['allRR'][$rr]->hide_auto == 1){
    		//if(strlen($whr) > 0){$whr .= " AND";}
    		$this->whr .= " AND `train_id` != 'AUTO TRAIN'";
    	}
    	$this->whr .= $whr;
    }

	// Home Display methods
	function wb_link_view($id){
		$this->content['html'] .= "<a href=\"waybill/view/".$id."\">View WB</a> ";
	}

	function wb_link_edit($id){
		$this->content['html'] .= "<a href=\"waybill/edit/".$id."\">Edit WB</a> ";
	}
	
	function wb_link_messaging($id){
		$this->content['html'] .= "<a href=\"messaging/lst/".$id."\">Email / Messages</a> ";
	}
	
	function wb_messages($me){
		$mess = @json_decode($this->waybills[$me]->messages, TRUE);
		if(count($mess) > 0){
			$this->content['html'] .= count($mess)." messages:<br />";
			$this->content['html'] .= "Latest: ";
			$this->content['html'] .= $this->wb_message_details($mess[count($mess)-1])."<hr />";
			//echo "<pre>"; print_r($mess); echo "</pre>";
			for($wi=0;$wi<count($mess)-1;$wi++){
				$this->content['html'] .= $this->wb_message_details($mess[$wi])."<hr />";
			}
		}
	}
	
	function wb_message_details($mess_arr){
		$m = $mess_arr['datetime']."<br />From: ".$this->mricf->qry("ichange_rr", $mess_arr['rr'], "id", "report_mark").", To: ".$this->mricf->qry("ichange_rr", $mess_arr['torr'], "id", "report_mark")."&nbsp;<br /><strong>".$mess_arr['text']."</strong>"; //.anchor("../messaging/lst/".$wbdat[$i]->id,"View");
		return $m;
	}

	function wb_link_map($id){
		$this->content['html'] .= "<a href=\"map/view/".$id."\">View Map</a><br />";	
	}
	
	function wb_lnk_mess($tmp){
		$this->content['html'] .= "<div class=\"wb_lnk_mess\">";
		$this->wb_link_view($this->waybills[$tmp]->id);
		if(isset($_COOKIE['rr_sess']) && @$icr == 0){ 
			$this->wb_link_edit($this->waybills[$tmp]->id);
			$this->wb_link_messaging($this->waybills[$tmp]->id);
		}
		$this->wb_link_map($this->waybills[$tmp]->id);
		$this->wb_messages($tmp);
		$this->content['html'] .= "</div>";
	}
	
	function wb_cars_display(){
		//if(is_array($wb_cars)){
		if(is_array($this->carNum)){
			//$this->carNum = $wb_cars;
			$g = "";
			for($cn=0;$cn<count($this->carNum);$cn++){
				if(strlen(@$this->carNum[$cn]['NUM']) > 0 && @$this->carNum[$cn]['NUM'] != "UNDEFINED"){
					$this->content['html'] .= "<br /><span style=\"color: #4169E1; font-size: 10pt; font-weight: bold\">";
					if(@array_key_exists($this->carNum[$cn]['NUM'],@$this->myCars)){$this->content['html'] .= "<a href=\"cars/edit/".$this->myCars[$this->carNum[$cn]['NUM']]->id."\">";}
					$this->content['html'] .= $this->carNum[$cn]['NUM']." (".$this->carNum[$cn]['AAR'].") (".@$allRR[$this->carNum[$cn]['RR']]->report_mark.")";
					if(@array_key_exists($this->carNum[$cn]['NUM'],@$this->myCars)){$this->content['html'] .= "</a>";}
					$this->content['html'] .= "</span> ";
					if(isset($myCars[$this->carNum[$cn]['NUM']])){$specInst = @$myCars[$this->carNum[$cn]['NUM']]->special_instruct;}
					else{$specInst = @$myCars[$this->carNum[$cn]]->special_instruct;}
					if(strlen($specInst) > 0){$this->content['html'] .= "<span style=\"font-weight: bold\"> - ".$specInst."</span>";$specInst = "";}
					
					if(strpos("foo".$g,substr($this->carNum[$cn]['AAR'],0,1)) < 1 || strlen($g) < 1){
						$g .= $this->mricf->get_car_image(@$this->carNum[$cn]['NUM'], @$this->carNum[$cn]['AAR'])." ";
					}
				}
			}
			$this->content['html'] .= "<span style=\"float: right;\">";
			$this->content['html'] .= $g."&nbsp;";
			$this->content['html'] .= "&nbsp;&nbsp;</span>";
		}
	}

	function latest_prog($tmp){
		$prog_all = json_decode($this->waybills[$tmp]->progress, true);
		$last_prog = count($prog_all) - 1; 
		$this->fld1_1 = $prog_all[$last_prog]['date']; //"Server Date/Time: ".$prog_all[$last_prog]['date'];
		$this->fld1_2 = $prog_all[$last_prog]['text'];
		$this->fld1_3 = $prog_all[$last_prog]['map_location'];
		$this->fld1_4 = ""; //if(isset($prog_all[$last_prog]['tzone']) && isset($_COOKIE['_tz'])){$this->fld1_4 = " (TZ Time: ".$prog_all[$last_prog]['tzone']." ".date('Y-m-d H:i',date('U')+$this->dates_times->get_timezone_offset($prog_all[$last_prog]['tzone'],$_COOKIE['_tz'])).")";}
		$this->fld1_5 = $prog_all[$last_prog]['time'];
	}
	
	function wb_update_frm($tmp){
		$str = "";
		$str .= "<span class=\"rr_tr\">Currently on / allocated to: ";
		$str .= "<select name=\"rr_sel[]\ style=\"font-size: 8pt; height: 22px;\" onchange=\"home_update('allocRR',this.value,".$this->waybills[$tmp]->id.");\"><option value=\"".$this->waybills[$tmp]->rr_id_handling."\">".$this->arr['allRR'][$this->waybills[$tmp]->rr_id_handling]->report_mark."</option>".$this->railroad_opts_lst."</select><br />";
		$str .= form_hidden("wb_id[]",$this->waybills[$tmp]->id);
		$str .= "In Train: <select name=\"tr_sel[]\" style=\"font-size: 8pt; height: 22px;\" onchange=\"home_update('allocTrain',this.value,".$this->waybills[$tmp]->id.");\"><option selected=\"selected\" value=\"".@$this->waybills[$tmp]->train_id."\">".@$this->waybills[$tmp]->train_id."</option>".$this->trains_opts_lst."</select>";
		$str .= "</span><br />";
		return $str;
	}
	
	function dt_styling(){
		if($this->fld1_1 == date('Y-m-d')){$this->fld1_1 = "<span style=\"background-color: yellow;\">".$this->fld1_1."</span>";}
	}

	// Bulk Update methods
	function bulk_update(){
		// Updates selections in BULK (all at once, rather than doing just one selection change at a time!)
		$this->arr = $_POST;
		for($i=0;$i<count($this->arr['wb_id']);$i++){
			$this->Generic_model->change("UPDATE `ichange_waybill` SET `rr_id_handling` = '".$this->arr['rr_sel'][$i]."', `train_id` = '".$this->arr['tr_sel'][$i]."' WHERE `id` = '".$this->arr['wb_id'][$i]."'");
		}
		header("Location:../home");
	}
	
	function home_view_settings(){
		// Styling and view options for the home page - not yet completed - hard coded for now.
		if(strlen($this->arr['myRR'][0]->home_disp_v2) > 4){$this->arr['home_view_settings'] = @json_decode($this->arr['myRR'][0]->home_disp_v2,true);}
	}
	
}
?>
