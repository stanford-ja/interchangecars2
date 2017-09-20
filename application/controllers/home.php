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
		$this->load->helper("file");
		$this->load->library('mricf');
		$this->load->library('dates_times');
		$this->load->library('email');

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
		$this->load->model('Storedfreight_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Waybill_model','',TRUE);
		
		// Railroad array set up
		$this->load->model('Railroad_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->arr['myRR'] = $this->Railroad_model->get_single($this->arr['rr_sess']);

		$rrArrTmp = $this->mricf->rrFullArr();
		$rrArrTmp_kys = array_keys($rrArrTmp);
		for($r=0;$r<count(array_keys($rrArrTmp_kys));$r++){$this->arr[$rrArrTmp_kys[$r]] = $rrArrTmp[$rrArrTmp_kys[$r]];}
		//echo "<pre>"; print_r($this->arr); echo "</pre>"; exit();
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
		for($i=0;$i<count($ca);$i++){
			$this->myCars[$ca[$i]->car_num] = $ca[$i];
		} 
		
		// Get train records
		$s = "SELECT `ichange_trains`.* FROM `ichange_trains` WHERE `ichange_trains`.`railroad_id` = '".$this->arr['rr_sess']."' ORDER BY `ichange_trains`.`train_id`";
		$this->myTrains = (array)$this->Generic_model->qry($s);
		
		// Create various selector options
		$this->trains_opts_lst = $this->mricf->trainOpts(array('rr' => $this->arr['rr_sess'], 'auto' => "Y", 'onlyrr' => 1));
		$this->railroad_opts_lst = $this->mricf->rrOpts();
		//print_r($this->mricf->rrOpts());
		
		// Get cars where from / to is a rr of user.
		$this->Waybill_model->get_carsOnAllMyWaybills(@$this->arr['myRR'][0]->owner_name);
		$this->carsOnAllMyWBs = $this->Waybill_model->carsOnAllMyWBs;
		//$this->carsOnAllMyWBsKys = $this->Waybill_model->carsOnAllMyWBsKys;

		// ONLY NEEDED TO CREATE messages TABLE AND DATA! ONCE DONE, CAN BE REMOVED. 2017-05-21
		$tbls = (array)$this->Generic_model->qry("SHOW TABLES WHERE Tables_in_jstan2_general LIKE 'ichange_messages'");
		if(count($tbls) == 0){ 
			header("Location:messaging");
			exit(); 
		}
	}

	public function index(){
		//	Generate list of waybills for rr logged in as
		//$this->load->model('Waybill_model','',TRUE);
		
		// Get list of waybill images
		$this->fils = get_filenames(DOC_ROOT."/waybill_images/");

		// Generate Afil WB list (if applicable)
		$this->my_rr_ids = $this->mricf->affil_ids($this->arr['rr_sess'],$this->arr['allRR']);

		$this->wbs_all = array();
		$this->pos_all = array();
		$this->set_wb_arr();
		$this->set_po_arr();

		if($this->arr['rr_sess'] > 0){$this->home_view_settings();}
		if(!isset($this->arr['myRR'][0]->home_disp)){
			$this->view0_build(); // Display list view
		}elseif($this->arr['myRR'][0]->home_disp == 0){
			$this->view0_build(); // Display list view
		}else{
			$this->view1_build(); // Display columns view
		}
		if(isset($this->arr['myRR'][0])){$this->common_wb_disp();}
		if($this->arr['rr_sess'] > 0){
			$this->porders_build();
			$this->rrlst_build();
			$this->trlst_build();
			$this->afillst_build();
			$this->search_build();
			$this->mess_build();
			$this->genload_build();
		}
		$this->view();
		//echo "<pre>"; print_r(get_browser(null,true)); echo "</pre><br />".$_SERVER['HTTP_USER_AGENT'];
	}

	// START PINTEREST-LIKE COLUMN DISPLAY PRIMARY METHOD
	public function columns(){
		//	Generate list of waybills for rr logged in as
		//$this->load->model('Waybill_model','',TRUE);

		// Generate Afil WB list (if applicable)
		$this->my_rr_ids = $this->mricf->affil_ids($this->arr['rr_sess'],$this->arr['allRR']);

		$this->wbs_all = array();
		$this->pos_all = array();
		$this->set_wb_arr();
		$this->set_po_arr();

		if($this->arr['rr_sess'] > 0){$this->home_view_settings();}
		$this->view1_build(); // Will be replaced with logic to view specific view setting for RR logged in as
		if($this->arr['rr_sess'] > 0){
			$this->porders_build();
			$this->rrlst_build();
			$this->trlst_build();
			$this->afillst_build();
			$this->search_build();
			$this->mess_build();
			$this->genload_build();
		}
		$this->view();
		//echo "<pre>"; print_r(get_browser(null,true)); echo "</pre><br />".$_SERVER['HTTP_USER_AGENT'];
	}
	// END PINTEREST-LIKE COLUMN DISPLAY PRIMARY METHOD
	
	function view0_build(){
		// Home 0 view html generation
		$tbl_start = "<div style=\"display: table; width: 100%; border-top: 2px solid black;\"><div style=\"display: table-row;\">";
		$this->content['html'] = "<div style=\"width: 100%;\">".form_open_multipart("bulk_update")."<input type=\"checkbox\" name=\"do_bulk\" id=\"do_bulk\" value=\"1\" onchange=\"hideEle('ubu'); if(this.checked === true){document.getElementById('ubu').style.display = 'inline';}\" />Do bulk update (not individual selections!)";
		$this->content['html'] .= "<span style=\"display: none; float: right;\" id=\"ubu\">".form_submit("bulk","Update Bulk")."</span>";
		$this->content['html'] .= $tbl_start.$this->wb_cars_in_use()."</div></div>";
		//$this->content['html'] .= "<div id=\"container\" class=\"js-masonry\" data-masonry-options='{ \"columnWidth\": 200, \"itemSelector\": \".item\" }'>";

		// Start element display definitions
		$elements = array("waybill_num","indust_origin_name","indust_dest_name","return_to","status","routing","notes","lading","cars");
		if(isset($this->arr['home_view_settings']['elements'])){$elements = $this->arr['home_view_settings']['elements'];}
		//echo "<pre>"; print_r($this->arr['home_view_settings']['elements']); echo "</pre>";
		// End element display definitions
				
		// Start FOR loop.
		for($tmp=0;$tmp<count($this->waybills);$tmp++){
			$td_cla = "td2";
			if(is_int($tmp/2)){$td_cla = "td1";}
			$this->latest_prog($tmp); // Latest progress report from JSON data
			$this->dt_styling(); // Waybill info styling
	
			//$wb_lst = "";
			$this->content['html'] .= $tbl_start;
			if(in_array("waybill_num",$elements)){$this->content['html'] .= "<div style=\"display: table-cell; width: 7%;\" class=\"".$td_cla."\"><a href=\"waybill/edit/".$this->waybills[$tmp]->id."\">".$this->waybills[$tmp]->waybill_num."</a> <span style=\"background-color: yellow; font-weight: bold;\">".$this->waybills[$tmp]->waybill_type."</span><br />".$this->waybills[$tmp]->date."&nbsp;</div>\n";}
			if(in_array("indust_origin_name",$elements)){$this->content['html'] .= "<div style=\"display: table-cell; width: 22%;\" class=\"".$td_cla."\"><span class=\"tiny_txt\">From: </span><br />".$this->waybills[$tmp]->indust_origin_name."&nbsp;</div>\n";}
			if(in_array("indust_dest_name",$elements)){$this->content['html'] .= "<div style=\"display: table-cell; width: 22%;\" class=\"".$td_cla."\"><span class=\"tiny_txt\">To: </span><br />".$this->waybills[$tmp]->indust_dest_name."&nbsp;</div>\n";}
			if(in_array("return_to",$elements)){$this->content['html'] .= "<div style=\"display: table-cell; width: 20%;\" class=\"".$td_cla."\"><span class=\"tiny_txt\">Return To: </span><br />".$this->waybills[$tmp]->return_to."&nbsp;</div>\n";}
			if(in_array("status",$elements)){$this->content['html'] .= "<div style=\"display: table-cell;\" class=\"".$td_cla."\"><span class=\"tiny_txt\">Status: </span><br />".$this->waybills[$tmp]->status."&nbsp;</div>\n";}
				/*
			$this->content['html'] .= "<div style=\"display: table-cell; text-align: right; width: 10%; height: 10px;\" id=\"options".$this->waybills[$tmp]->id."\" class=\"".$td_cla."\">";
			if(@$icr == 1 && isset($_COOKIE['rr_sess'])){$this->content['html'] .= "<a href=\"save.php?type=RR2WB&id=$fld8&rr=".$allRR[$_COOKIE['rr_sess']]->report_mark."\"><span style=\"white-space: nowrap;\">Add RR to Routing</span></a><br />";}
			//if($icr == 0){$this->content['html'] .= "<a style=\"font-size: 10pt;\" href=\"index.php?hde=".$id."\">Hide (for 24hrs)</a>";}
			if(@$icr == 0){
			}
			$this->content['html'] .= "&nbsp;</div>";
			*/
			$this->content['html'] .= "</div></div>";
			$this->content['html'] .= "<div style=\"display: block;\" class=\"".$td_cla."\">";
			$this->wb_images($tmp);
			$this->wb_lnk_mess($tmp); // Links for each waybill displayed

			if(in_array("routing",$elements)){$this->content['html'] .= "<span class=\"routing\">Routing: ".$this->waybills[$tmp]->routing."</span><br />";}
			if($this->arr['rr_sess'] > 0){
				$this->content['html'] .= $this->wb_update_frm($tmp); // Update form for each waybill displayed
			}
			$this->content['html'] .= "&nbsp;</div>\n";
			$this->content['html'] .= "<div style=\"display: block;\" class=\"".$td_cla."\">";
			if(strlen($this->waybills[$tmp]->lading) > 0 && in_array("lading",$elements)){
				$this->content['html'] .= "<div class=\"lading\">".$this->waybills[$tmp]->lading;
				$this->content['html'] .= "</div>";
			}
			$this->content['html'] .= "<span class=\"progress\">".$this->fld1_1." ".$this->fld1_5.$this->fld1_4." - ".$this->fld1_2."</span>";
			if(in_array("cars",$elements)){
				$this->carNum = @json_decode($this->waybills[$tmp]->cars, true); // Cars from JSON array
				$this->wb_cars_display(); // Cars displayed for Waybill - text & image/s
			}
			if(strlen($this->waybills[$tmp]->notes) > 2 && in_array("notes",$elements)){$this->content['html'] .= "<div class=\"notes\">".$this->waybills[$tmp]->notes."</div>";}
			$this->content['html'] .= "</div>\n";
			$this->content['html'] .= "</div>";
		} // End FOR loop??
		$this->content['html'] .= form_close();
	}

	// START EXPERIMENTAL PINTEREST-LIKE COLUMN DISPLAY VIEW GENERATOR - uses this if ichange_rr.home_disp = 1
	function view1_build(){
		// Home 1 view html generation - COLUMNS
		$div_start = "<div class=\"item\">";
		$this->content['html'] = "<div style=\"width: 100%;\">".form_open_multipart("bulk_update")."
			<input type=\"checkbox\" name=\"do_bulk\" id=\"do_bulk\" value=\"1\" onchange=\"hideEle('ubu'); if(this.checked === true){document.getElementById('ubu').style.display = 'inline';}\" />Do bulk update (not individual selections!)";
		$this->content['html'] .= "<span style=\"display: none; float: right;\" id=\"ubu\">".form_submit("bulk","Update Bulk")."</span>";
		$this->content['html'] .= "<div id=\"container\" class=\"js-masonry\" data-masonry-options='{ \"columnWidth\": 650, \"itemSelector\": \".item\" }'>";	
		$this->content['html'] .= $div_start.$this->wb_cars_in_use()."</div>";
		
		// Start element display definitions
		$elements = array("waybill_num","indust_origin_name","indust_dest_name","return_to","status","routing","notes","lading","cars");
		if(isset($this->arr['home_view_settings']['elements'])){$elements = $this->arr['home_view_settings']['elements'];}
		//echo "<pre>"; print_r($this->arr['home_view_settings']['elements']); echo "</pre>";
		// End element display definitions
				
		// Start FOR loop.
		for($tmp=0;$tmp<count($this->waybills);$tmp++){
			$td_cla = "td2";
			if(is_int($tmp/2)){$td_cla = "td1";}
			$this->latest_prog($tmp); // Latest progress report from JSON data
			$this->dt_styling(); // Waybill info styling
	
			//$wb_lst = "";
			$this->content['html'] .= $div_start;
			$this->wb_images($tmp);
			$this->wb_lnk_mess($tmp); // Links for each waybill displayed
			if(in_array("waybill_num",$elements)){$this->content['html'] .= "<a href=\"waybill/edit/".$this->waybills[$tmp]->id."\">".$this->waybills[$tmp]->waybill_num."</a> <span style=\"background-color: yellow; font-weight: bold;\">".$this->waybills[$tmp]->waybill_type."</span>&nbsp;".$this->waybills[$tmp]->date."&nbsp;<br />\n";}
			if(in_array("indust_origin_name",$elements)){$this->content['html'] .= "<span class=\"tiny_txt\">From: </span><br />".$this->waybills[$tmp]->indust_origin_name."&nbsp;<br />\n";}
			if(in_array("indust_dest_name",$elements)){$this->content['html'] .= "<span class=\"tiny_txt\">To: </span><br />".$this->waybills[$tmp]->indust_dest_name."&nbsp;<br />\n";}
			if(in_array("return_to",$elements)){$this->content['html'] .= "<span class=\"tiny_txt\">Return To: </span><br />".$this->waybills[$tmp]->return_to."&nbsp;<br />\n";}
			if(in_array("status",$elements)){$this->content['html'] .= "<span class=\"tiny_txt\">Status: </span><br />".$this->waybills[$tmp]->status."&nbsp;<br />\n";}

			if(in_array("routing",$elements)){$this->content['html'] .= "<span class=\"routing\">Routing: ".$this->waybills[$tmp]->routing."</span><br />";}
			if($this->arr['rr_sess'] > 0){
				$this->content['html'] .= $this->wb_update_frm($tmp); // Update form for each waybill displayed
			}
			if(strlen($this->waybills[$tmp]->lading) > 0 && in_array("lading",$elements)){
				$this->content['html'] .= "<div class=\"lading\">".$this->waybills[$tmp]->lading;
				$this->content['html'] .= "</div><br />";
			}
			$this->content['html'] .= "<span class=\"progress\">".$this->fld1_1." ".$this->fld1_5.$this->fld1_4." - ".$this->fld1_2."</span><br />";
			if(in_array("cars",$elements)){
				$this->carNum = @json_decode($this->waybills[$tmp]->cars, true); // Cars from JSON array
				$this->wb_cars_display(); // Cars displayed for Waybill - text & image/s
			}
			if(strlen($this->waybills[$tmp]->notes) > 2 && in_array("notes",$elements)){$this->content['html'] .= "<div class=\"notes\">".$this->waybills[$tmp]->notes."</div>";}
			$this->content['html'] .= "</div>";
		} // End FOR loop??
		$this->content['html'] .= "</div>"; // End of container div
		$this->content['html'] .= form_close();
	}
	// END EXPERIMENTAL PINTEREST-LIKE COLUMN DISPLAY VIEW GENERATOR
	
	function common_wb_disp(){
		// Displays waybills that are allocated to common railroads (so waybills dont get lost as easily)
		$max_id_qry = $this->Generic_model->qry("SELECT MAX(`id`) AS `max_id` FROM `ichange_rr`");
		$max_id = $max_id_qry[0]->max_id;
		$comm_rrs_sql = "'0'";
		for($i=1;$i<=$max_id;$i++){
			if(isset($this->arr['allRR'][$i])){
				if($this->arr['allRR'][$i]->common_flag == 1){$comm_rrs_sql .= ",'".$this->arr['allRR'][$i]->id."'";}
			}
		}
		$tmp = (array)$this->Generic_model->qry("SELECT * FROM `ichange_waybill` WHERE `rr_id_handling` IN (".$comm_rrs_sql.") AND `status` != 'P_ORDER' AND `status` != 'CLOSED'");	
		$this->content['html'] .= "<hr /><div class=\"td_title\">
			<span style=\"font-size: 16pt; font-weight: bold;\">Waybills allocated to Common Railroads (".count($tmp)."):</span>
			</div>
			<div id=\"container2\" class=\"js-masonry\" data-masonry-options='{ \"columnWidth\": 325, \"itemSelector\": \".item2\" }'>";
		for($w=0;$w<count($tmp);$w++){
			$prog_all = json_decode($tmp[$w]->progress, true);
			$last_prog = count($prog_all) - 1; 
			$fld1_1 = @$prog_all[$last_prog]['date']; //"Server Date/Time: ".$prog_all[$last_prog]['date'];
			$fld1_2 = @$prog_all[$last_prog]['text'];
			$fld1_3 = @$prog_all[$last_prog]['map_location'];
			$fld1_4 = ""; //if(isset($prog_all[$last_prog]['tzone']) && isset($_COOKIE['_tz'])){$this->fld1_4 = " (TZ Time: ".$prog_all[$last_prog]['tzone']." ".date('Y-m-d H:i',date('U')+$this->dates_times->get_timezone_offset($prog_all[$last_prog]['tzone'],$_COOKIE['_tz'])).")";}
			$fld1_5 = @$prog_all[$last_prog]['time'];

			$this->content['html'] .= "<div class=\"item2\">";
			$this->content['html'] .= "<a href=\"waybill/edit/".$tmp[$w]->id."\">".$tmp[$w]->waybill_num."</a> <span style=\"background-color: yellow; font-weight: bold;\">".$tmp[$w]->waybill_type."</span>&nbsp;".$tmp[$w]->date."&nbsp;<br />\n";
			$this->content['html'] .= "<span class=\"tiny_txt\">From: </span><br />".$tmp[$w]->indust_origin_name."&nbsp;<br />\n";
			$this->content['html'] .= "<span class=\"tiny_txt\">To: </span><br />".$tmp[$w]->indust_dest_name."&nbsp;<br />\n";
			$this->content['html'] .= "<span class=\"tiny_txt\">Return To: </span><br />".$tmp[$w]->return_to."&nbsp;<br />\n";
			$this->content['html'] .= "<span class=\"tiny_txt\">Status: </span><br />".$tmp[$w]->status."&nbsp;<br />\n";
			$this->content['html'] .= "<span class=\"tiny_txt\">Allocated to:</span><br />".@$this->arr['allRR'][$tmp[$w]->rr_id_handling]->report_mark."<br />";

			$this->content['html'] .= "<span class=\"tiny_txt\">Routing:</span><br />".$tmp[$w]->routing."<br />";
			if(strlen($tmp[$w]->lading) > 0){
				$this->content['html'] .= "<span class=\"tiny_txt\">Lading:</span><br />".$tmp[$w]->lading."<br />";
			}
			$this->content['html'] .= "<span class=\"tiny_txt\">Progress:</span><br />".$fld1_1." ".$fld1_5.$fld1_4." - ".$fld1_2."<br />";
			if(strlen($fld1_3) > 0){$this->content['html'] .=  "<span class=\"tiny_txt\">Map Location:</span> ".$fld1_3."<br />";}
			//if(in_array("cars",$elements)){
				$this->carNum = @json_decode($tmp[$w]->cars, true); // Cars from JSON array
				$this->wb_cars_display(); // Cars displayed for Waybill - text & image/s
			//}
			if(strlen($tmp[$w]->notes) > 2){$this->content['html'] .= "<span class=\"tiny_txt\">Notes:</span><br />".$tmp[$w]->notes."<br />";}
			$this->wb_link_edit($tmp[$w]->id);
			$this->content['html'] .= "</div>"; // end of .item2 div
		}
		$this->content['html'] .= "</div>"; // End of container2 div
	}

	function porders_build(){
		// Builds html for purchase orders.
		$this->content['phtml'] = "<!-- <div class=\"box1\" style=\"left: ".$this->horiz_loc."px\"> // -->"; // 120px
		$this->content['phtml'] .= "<br /><a href=\"#\" id=\"po_expand\" title=\"Click this link to view available purchase orders\"><strong>P/Orders</strong></a><br />";
		$this->content['phtml'] .= "<div id=\"pos\" style=\"display: none; position: fixed; left: 10px; top: 25px; z-index:99; max-height: 300px; overflow: auto;\"><a href=\"#\" id=\"po_shrink\">Shrink</a><br />";
		$this->content['phtml'] .= "<strong>Purchase Orders</strong></a><br />";
		if(count($this->porders) > 0){
			for($p=0;$p<count($this->porders);$p++){
				$rr_from = "??"; if(isset($this->arr['allRR'][$this->porders[$p]->rr_id_from]->report_mark)){$rr_from = $this->arr['allRR'][$this->porders[$p]->rr_id_from]->report_mark;}
				$rr_to = "??"; if(isset($this->arr['allRR'][$this->porders[$p]->rr_id_to]->report_mark)){$rr_to = $this->arr['allRR'][$this->porders[$p]->rr_id_to]->report_mark;}
				$this->content['phtml'] .= "Rec # <a href=\"waybill/edit/".$this->porders[$p]->id."\">".$this->porders[$p]->waybill_num."</a><br />";
				$this->content['phtml'] .= $this->porders[$p]->lading."<br />";
				$this->content['phtml'] .= $rr_from." to ".$rr_to;
				if(json_decode($this->porders[$p]->progress,TRUE)){
					$j_tmp = json_decode($this->porders[$p]->progress,TRUE);
					$this->content['phtml'] .= "<br /><span style=\"color: #888; font-size: 7pt;\">".$j_tmp[count($j_tmp)-1]['date']." - ".$j_tmp[count($j_tmp)-1]['text']."</span>";
				}
				$this->content['phtml'] .= "<hr />";
			}
		}

		if(count($this->storedpo) > 0){
			$this->content['phtml'] .= "<strong>Stored Freight</strong></a><br />";
			for($p=0;$p<count($this->storedpo);$p++){
				$this->content['phtml'] .= $this->storedpo[$p]->commodity." x ".$this->storedpo[$p]->qty_cars." cars<br />";
				$this->content['phtml'] .= $this->storedpo[$p]->indust_name;
				$this->content['phtml'] .= " (".$this->storedpo[$p]->town.")";
				$this->content['phtml'] .= " <a href=\"storedfreight/acquire/".$this->storedpo[$p]->id."\">Acquire</a>";
				$this->content['phtml'] .= "<hr />";
			}
		}

		$this->content['phtml'] .= "</div>";
		$this->content['phtml'] .= "<!-- </div> // -->";
		$this->arr['phtml'] = $this->content['phtml'];
		$this->content['phtml'] = "";
		$this->horiz_loc = $this->horiz_loc + 110;
	}
	
	function rrlst_build(){
		// Builds html for railroad listing.
		$max_id_qry = $this->Generic_model->qry("SELECT MAX(`id`) AS `max_id` FROM `ichange_rr`");
		$max_id = $max_id_qry[0]->max_id;
		$my_rhtml = "";
  		for($i=1;$i<=$max_id;$i++){
  			if(isset($this->arr['allRR'][$i])){
  				if($this->arr['allRR'][$i]->inactive != 1){
  					$rhtml = "";
	  				$rhtml .= "<span style=\"float: right; font-size: 13pt; font-weight: bold; background-color: #ddd;\">&nbsp;id: ".@$this->arr['allRR'][$i]->id."&nbsp;</span>";
					if(@$this->arr['allRR'][$i]->id == $this->arr['rr_sess'] && $this->arr['rr_sess'] > 0){$rhtml .= "&nbsp;&nbsp;<a href=\"rr/edit/".$this->arr['allRR'][$i]->id."\">Edit</a>";}
					$rhtml .= "&nbsp;&nbsp;<a href=\"rr/view/".$this->arr['allRR'][$i]->id."\">View</a>";
					$rhtml .= "<br />".@$this->arr['allRR'][$i]->report_mark." - ";
					$rhtml .= @$this->arr['allRR'][$i]->rr_name."<br />";
					$rhtml .= @$this->arr['allRR'][$i]->owner_name;
					if(@$this->arr['allRR'][$i]->last_act > 0){$rhtml .= "&nbsp;&nbsp;(".date('Y-m-d',@$this->arr['allRR'][$i]->last_act).")";}
					if(@$this->arr['allRR'][$i]->common_flag == 1){$rhtml .= "<br />&nbsp;&nbsp;<strong>(COMMON RR)</strong>";}
					//$this->content['rhtml'] .= "<hr />";
					
					$my_rrs = 0;
					if(date('Ymd') < 20140215){
						// Old affiliate by report_mark reckoning. Can be deleted after 2014-02-15!
						if(@$this->arr['allRR'][$i]->id == $this->arr['rr_sess'] && $this->arr['rr_sess'] > 0){$my_rrs = 1; $styl = "background-color: #98FB98";}
						if(strpos("a".$this->arr['allRR'][$i]->affiliates,@$this->arr['allRR'][$this->arr['rr_sess']]->report_mark) > 0){$my_rrs = 2; $styl = "background-color: #F5DEB3;";}
					}else{
						// New affiliate by owner name reckoning.
						if(@$this->arr['allRR'][$i]->id == $this->arr['rr_sess'] && $this->arr['rr_sess'] > 0){$my_rrs = 1; $styl = "background-color: #98FB98";}
						elseif(strtoupper($this->arr['allRR'][$i]->owner_name) == @strtoupper($this->arr['allRR'][$this->arr['rr_sess']]->owner_name)){$my_rrs = 2; $styl = "background-color: #F5DEB3;";}
					}
					if($my_rrs > 0){
						if($my_rrs == 1){$rhtml = "<span style=\"float: left; font-weight: bold;\">&nbsp;{My Railroad}&nbsp;</span>".$rhtml;}
						if($my_rrs == 2){$rhtml = "<span style=\"float: left; font-weight: bold;\">&nbsp;{Affiliate}&nbsp;<a href=\"login/switch_to/".@$i."\">Switch to</a></span>".$rhtml;}
						$my_rhtml .= "<div style=\"".$styl."; padding: 3px;\">".$rhtml."</div><hr />";
					}
					else{$this->content['rhtml'] .= $rhtml."<hr />";}
					
					//$this->content['rhtml'] .= $rhtml."<hr />";
				}
			}
		}
		$this->content['rhtml'] = ""; /* "<div class=\"box1\" style=\"left: ".$this->horiz_loc."px;\">".
			"&nbsp;<a href=\"#\" id=\"rr_expand\" title=\"Click this link to see the Railroads list\"><strong>Railroads</strong></a>&nbsp;<a href=\"#\" id=\"rr_shrink\">Shrink</a><br />".
			"<div id=\"rrs\" style=\"display: none;\">".$my_rhtml.$this->content['rhtml'].
			"</div>".
			"</div>"; */
		$this->arr['rhtml'] = "<a href=\"#\" id=\"rr_expand\" title=\"Click this link to see the Railroads list\"><strong>Railroads</strong></a><br />".
			"<div id=\"rrs\" style=\"display: none; position: fixed; left: 10px; top: 25px; z-index:99; max-height: 300px; overflow: auto;\"><a href=\"#\" id=\"rr_shrink\">Shrink</a><br />".$my_rhtml.$this->content['rhtml'].
			"</div>";
		$this->horiz_loc = $this->horiz_loc + 115;
	}

	function trlst_build(){
		// Builds html for railroad listing.
		$no_cars_thtml = "";
		$this->content['thtml'] = "<div class=\"box1\" style=\"left: ".$this->horiz_loc."px;\">"; // 345px
		$this->arr['thtml'] = "";
		$this->content['thtml'] .= "&nbsp;<a href=\"#\" id=\"tr_expand\" title=\"Click this link to show the trains list\"><strong>Trains</strong></a>&nbsp;<a href=\"#\" id=\"tr_shrink\">Shrink</a><br />";
		$this->arr['thtml'] .= "<a href=\"#\" id=\"tr_expand\" title=\"Click this link to show the trains list\"><strong>Trains</strong></a><br />";
		$this->content['thtml'] .= "<div id=\"trs\" style=\"display: none;\"><strong>Cars allocated</strong><br />";
		$this->arr['thtml'] .= "<div id=\"trs\" style=\"display: none; max-width: 200px; position: fixed; left: 10px; top: 25px; z-index: 99; max-height: 300px; overflow: auto;\"><a href=\"#\" id=\"tr_shrink\">Shrink</a><br /><strong>Cars allocated</strong><br />";
  		for($i=0;$i<count($this->myTrains);$i++){
  			$tr_wbs = 0;
  			for($r=0;$r<count($this->wbs_all);$r++){
  				for($w=0;$w<count($this->wbs_all[$r]);$w++){
  					if(@$this->wbs_all[$r][$w]->train_id == $this->myTrains[$i]->train_id){$tr_wbs++;}
  				}
  			}
  			$wb_num = ""; if($tr_wbs > 0){$wb_num = " (".$tr_wbs.")";}
  			if($tr_wbs > 0){
				$this->content['thtml'] .= $this->myTrains[$i]->train_id.$wb_num."&nbsp;&nbsp;<a href=\"switchlist/lst/".$this->myTrains[$i]->id."\">S/list</a>";
				$this->arr['thtml'] .= $this->myTrains[$i]->train_id.$wb_num."&nbsp;&nbsp;<a href=\"switchlist/lst/".$this->myTrains[$i]->id."\">S/list</a>";
				$this->content['thtml'] .= "<hr />";
				$this->arr['thtml'] .= "<hr />";
			}else{
				$no_cars_thtml .= $this->myTrains[$i]->train_id.$wb_num;
				$no_cars_thtml .= "<hr />";
			}
		}
		$this->content['thtml'] .= "<strong>No cars allocated</strong><br />".$no_cars_thtml;
		$this->arr['thtml'] .= "<strong>No cars allocated</strong><br />".$no_cars_thtml;
		$this->content['thtml'] .= "</div>";
		$this->arr['thtml'] .= "</div>";
		$this->content['thtml'] .= "</div>";
		$this->content['thtml'] = "";
		$this->horiz_loc = $this->horiz_loc + 95;
	}

	function afillst_build(){
		// Builds html for railroad listing.
		$this->content['ahtml'] = ""; // Merged into Railroads listing
		/*
		$this->content['ahtml'] = "<div class=\"box1\" style=\"left: ".$this->horiz_loc."px;\">"; // 440px;
		$this->content['ahtml'] .= "&nbsp;<a href=\"#\" id=\"af_expand\"><strong>Affiliates</strong></a>&nbsp;<a href=\"#\" id=\"af_shrink\">Shrink</a><br />";
		$this->content['ahtml'] .= "<div id=\"afs\" style=\"display: none;\">";
		$afils = explode(";",$this->arr['myRR'][0]->affiliates);
		for($i=0;$i<count($afils);$i++){
			if(isset($this->arr['allRRRepMark'][$afils[$i]])){
				$this->content['ahtml'] .= $afils[$i]."&nbsp;<a href=\"login/switch_to/".@$this->arr['allRRRepMark'][$afils[$i]]."\">Switch to</a>";
				$this->content['ahtml'] .= "<hr />";
			}
		}
		$this->content['ahtml'] .= "</div>";
		$this->content['ahtml'] .= "</div>";
		$this->horiz_loc = $this->horiz_loc + 110;
		*/
	}

	function search_build(){
		// Builds html for railroad listing.
		$this->content['shtml'] = ""; //"<div class=\"box1\" style=\"left: ".$this->horiz_loc."px;\">"; // 553px;
		if(isset($_POST['search_for'])){$this->content['shtml'] .= anchor("../home","My WBs")."<br />";}
		else{
			$this->content['shtml'] .= "&nbsp;<a href=\"#\" id=\"search_expand\" title=\"Click this to expand the Search form\"><strong>Search</strong></a><br />";
			$this->content['shtml'] .= "<div id=\"search\" style=\"display: none; position: fixed; left: 10px; top: 25px; z-index: 99; max-height: 300px; overflow: auto;\"><a href=\"#\" id=\"search_shrink\">Shrink</a><br />";
		
			$search_opts = array('waybill_num' => "Waybill Number", 'lading' => "Lading", 'indust_origin_name' => "Origin Industry", 'indust_dest_name' => "Destination Industry", 'cars' => "Cars", 'routing' => "Routing", 'train_id' => "In / Allocateted to Train ID");
			$this->content['shtml'] .= form_open_multipart("../home");
			$this->content['shtml'] .= "For ".form_input('search_for')."<br />";
			$this->content['shtml'] .= "In ".form_dropdown('search_in',$search_opts);
			$this->content['shtml'] .= " ".form_submit('submit','Search');
			$this->content['shtml'] .= form_close();

			$this->content['shtml'] .= "</div>";
		}
		//$this->content['shtml'] .= "</div>";
		$this->arr['shtml'] = $this->content['shtml'];
		$this->content['shtml'] = "";
		$this->horiz_loc = $this->horiz_loc + 100;
	}

	function mess_build(){
		// Builds html for messages listing.
		$tmp = "";
		$cntr=0;
		for($ri=0;$ri<count($this->my_rr_ids);$ri++){
			$wbdat = (array)$this->Waybill_model->get_messages(0,$this->my_rr_ids[$ri],1);
			for($i=0;$i<count($wbdat);$i++){
				$mess = $wbdat[$i]; //]@json_decode($wbdat[$i]->messages);
				/*
				for($m=0;$m<count($mess);$m++){
					if(isset($mess[$m]->datetime)){
						if($mess[$m]->torr == $this->my_rr_ids[$ri]){
						$m = (array)$mess[$m];
						$tmp .= "<a href=\"messaging/lst/".$wbdat[$i]->wb_id."\">".$wbdat[$i]->waybill_num."</a> - ".$this->wb_message_details($m);
						$tmp .= "<hr />";
						$cntr++;
						}
					}
				}
				*/
				if(isset($mess->datetime)){
					if($mess->torr == $this->my_rr_ids[$ri]){
					$m = (array)$mess;
					$tmp .= "<a href=\"messaging/lst/".$wbdat[$i]->wb_id."\">".$wbdat[$i]->waybill_num."</a> - ".$this->wb_message_details($m);
					$tmp .= "<hr />";
					$cntr++;
					}
				}
			}
		}
		if($cntr > 0){
			$this->content['mhtml'] = ""; //"<div class=\"box1\" style=\"left: ".$this->horiz_loc."px; background-color: yellow; max-width: 270px;\">"; // 655px
			$this->content['mhtml'] .= "&nbsp;<a href=\"#\" id=\"me_expand\" title=\"Click this link to show the Messages list\"><strong>Messages (".$cntr.")</strong></a><br />";
			$this->content['mhtml'] .= "<div id=\"mes\" style=\"display: none; position: fixed; left: 10px; top: 25px; z-index: 99; max-height: 300px; overflow: auto;\"><a href=\"#\" id=\"me_shrink\">Shrink</a><br />";
			$this->content['mhtml'] .= $tmp."</div>";
			//$this->content['mhtml'] .= "</div>";
			$this->arr['mhtml'] = $this->content['mhtml'];
			$this->content['mhtml'] = "";
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
				$this->content['ghtml'] .= "<a href=\"#\" id=\"gl_expand\"><strong>Generated Loads (".$cntr.")</strong></a><br />";
				$this->content['ghtml'] .= "<div id=\"genl\" style=\"display: none; position: fixed; left: 10px; top: 25px; z-index: 99\"><a href=\"#\" id=\"gl_shrink\">Shrink</a><br />";
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
    		$this->whr .= " AND `train_id` != 'AUTO TRAIN'";
    	}
    	$this->whr .= $whr;
    }
   
   // Setting waybills
   function set_wb_arr(){
		// Waybill array set up
		if($this->arr['rr_sess'] == 0){
			$this->wbs_all[] = $this->Waybill_model->get_latest_entries(20);
		}else{
			if(isset($_POST['search_for'])){
				$this->wbs_all[] = $this->Generic_model->get_search_results($_POST['search_for'],$_POST['search_in'],"ichange_waybill");				
			}else{
				for($rid=0;$rid<count($this->my_rr_ids);$rid++){
					//$this->allHomeWBs($this->arr['rr_sess'],$this->arr['myRR'][0]->report_mark);
					$this->allHomeWBs($this->my_rr_ids[$rid],$this->arr['allRR'][$this->my_rr_ids[$rid]]->report_mark);
					$this->wbs_all[] = $this->Waybill_model->get_allOpenHome($this->whr,"train_id`,`sw_order`,`waybill_num");
				}
				$this->pos_all = $this->Waybill_model->get_POrders();
				$this->pos_sto = $this->Storedfreight_model->get_all_nonzero(); //get_all();
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
   }
   
   function set_po_arr(){
		//$po_arr = array();
		for($i=0;$i<count($this->pos_all);$i++){
			//$po_arr[] = $this->pos_all[$i];
			$this->porders[] = $this->pos_all[$i];
		}
		//$this->arr['porders'] = $po_arr;
		//$this->porders = $po_arr;
		for($i=0;$i<count($this->pos_sto);$i++){
			$this->storedpo[] = $this->pos_sto[$i];
		}
   }

	// Home Display methods
	function wb_link_view($id){
		//$this->content['html'] .= "<input type=\"button\" id=\"view_wb_btn\" title=\"Click this button to view the waybill\" value=\"View WB\" onclick=\"window.location = '".WEB_ROOT."/waybill/view/".$id."'\" />&nbsp;"; //"<a href=\"waybill/view/".$id."\">View WB</a> ";
		$this->content['html'] .= "<div class=\"wb_btn\"><a href=\"javascript:{}\" onclick=\"window.location = '".WEB_ROOT."/waybill/view/".$id."'\">View WB</a></div>";
	}

	function wb_link_edit($id){
		//$this->content['html'] .= "<input type=\"button\" id=\"edit_wb_btn\" title=\"Click this button to edit the waybill\" value=\"Edit WB\" onclick=\"window.location = '".WEB_ROOT."/waybill/edit/".$id."'\" />&nbsp;"; //"<a href=\"waybill/edit/".$id."\">Edit WB</a> ";
		$this->content['html'] .= "<div class=\"wb_btn\"><a href=\"javascript:{}\" onclick=\"window.location = '".WEB_ROOT."/waybill/edit/".$id."'\">Edit WB</a></div>";
	}
	
	function wb_link_messaging($id){
		//$this->content['html'] .= "<input type=\"button\" value=\"Email / Messages\" onclick=\"window.location = '".WEB_ROOT."/messaging/lst/".$id."'\" />&nbsp;"; //"<a href=\"messaging/lst/".$id."\">Email / Messages</a> ";
		$this->content['html'] .= "<div class=\"wb_btn\"><a href=\"javascript:{}\" onclick=\"window.location = '".WEB_ROOT."/messaging/lst/".$id."'\">Email / Msgs</a></div>";
	}

	function wb_link_image($id){
		//$this->content['html'] .= "<input type=\"button\" value=\"Email / Messages\" onclick=\"window.location = '".WEB_ROOT."/messaging/lst/".$id."'\" />&nbsp;"; //"<a href=\"messaging/lst/".$id."\">Email / Messages</a> ";
		$this->content['html'] .= "<div class=\"wb_btn\"><a href=\"javascript:{}\" onclick=\"window.open('".WEB_ROOT."/graphics/waybill/".$id."','".$id."','width=500,height=700');\">Upload Img</a></div>";
	}
	
	function wb_cars_in_use(){
		$cars_in_use = "";
		if(count($this->carsOnAllMyWBs) > 0){
			$cars_in_use .= "<div style=\"display: block; border: 1px solid peru; background-color: lightgreen; padding: 5px;\">
				<strong>Cars in Use Summary:</strong><br />";
			for($i=0;$i<count($this->carsOnAllMyWBs);$i++){
				$cars_in_use .= "<div style=\"display: inline-block; padding: 3px; border: 1px solid white; margin: 2px; background-color: #ccc; font-size: 9pt;\">".$this->carsOnAllMyWBs[$i]['NUM']."&nbsp;(".$this->carsOnAllMyWBs[$i]['AAR'].")&nbsp;on&nbsp;".$this->carsOnAllMyWBs[$i]['REP_MK']."&nbsp;/&nbsp;".$this->carsOnAllMyWBs[$i]['TR_ID']."</div>";
			}
			$cars_in_use .= "</div>";
		}
		return $cars_in_use;
	}
	
	function wb_messages($me){
		//$mess = @json_decode($this->waybills[$me]->messages, TRUE);
		$msgs = "";
		$cntr = 0;
		//for($ri=0;$ri<count($this->my_rr_ids);$ri++){
			$mess = (array)$this->Waybill_model->get_messages($this->waybills[$me]->id,0);
			if(count($mess) > 0){
				//echo "<pre>";print_r($this->waybills[$me]); echo "</pre>";
				//echo "<pre>";print_r($mess); echo "</pre>";
				//$this->content['html'] .= "<br />".count($mess)." messages:<br />";
				//$this->content['html'] .= "Latest: ";
				//$this->content['html'] .= $this->wb_message_details($mess[count($mess)-1])."<hr />";
				//for($wi=0;$wi<count($mess)-1;$wi++){
				for($wi=0;$wi<count($mess);$wi++){
					$mess[$wi] = (array)$mess[$wi];
					$msgs .= $this->wb_message_details($mess[$wi])."<hr />";
					$cntr++;
				}
			}
		//}
		if($cntr > 0){ $this->content['html'] .= "<br />".$cntr." messages:<br />".$msgs; }
	}
	
	function wb_images($me){
		for($i=0;$i<count($this->fils);$i++){
			if(strpos("Z".$this->fils[$i],$this->waybills[$me]->id."-") > 0){
				$tmp = explode("-",str_replace(".jpg","",$this->fils[$i]));
				$fil_html .= "<a href=\"javascript:{}\" onclick=\"window.open('".WEB_ROOT."/graphics/view/".str_replace(".jpg","",$this->fils[$i])."','".$i."','width=600,height=650');\">";
				$fil_html .= "<img src=\"".WEB_ROOT."/waybill_images/".$this->fils[$i]."\" title=\"Uploaded by ".$this->arr['allRR'][$tmp[1]]->report_mark."\" alt=\"\" style=\"width: 100px; margin: 3px;\">";
				$fil_html .= "</a>";
			}
		}
		if(isset($fil_html) && strlen($fil_html) > 0){
			$fil_html = "<div style=\"width: auto; float: right; max-height: 120px; border: 1px solid brown; width: 130px; overflow: auto;\">
				".$fil_html."
				</div>";
			$this->content['html'] .= $fil_html;
		}
	}
	
	function wb_message_details($mess_arr){
		$m = $mess_arr['datetime']."<br />From: ".$this->mricf->qry("ichange_rr", $mess_arr['rr'], "id", "report_mark").", To: ".$this->mricf->qry("ichange_rr", $mess_arr['torr'], "id", "report_mark")."&nbsp;<br /><strong>".$mess_arr['text']."</strong>"; //.anchor("../messaging/lst/".$wbdat[$i]->id,"View");
		if($mess_arr['ack'] < 1){ $m .= "<div style=\"background-yellow; padding: 5px; text-align: center;\">Not acknowledged</div>"; }
		return $m;
	}

	function wb_link_map($id){
		//$this->content['html'] .= "<input type=\"button\" value=\"View Map\" onclick=\"window.location = '".WEB_ROOT."/map/view/".$id."'\" />"; //"<a href=\"map/view/".$id."\">View Map</a><br />";
		$this->content['html'] .= "<div class=\"wb_btn\"><a href=\"javascript:{}\" onclick=\"window.location = '".WEB_ROOT."/map/view/".$id."'\">View Map</a></div><br />";	
	}
	
	function wb_lnk_mess($tmp){
		$this->content['html'] .= "<div class=\"wb_lnk_mess\">";
		$this->wb_link_view($this->waybills[$tmp]->id);
		if(isset($_COOKIE['rr_sess']) && @$icr == 0){ 
			$this->wb_link_edit($this->waybills[$tmp]->id);
			$this->wb_link_messaging($this->waybills[$tmp]->id);
			$this->wb_link_image($this->waybills[$tmp]->id);
		}
		$this->wb_link_map($this->waybills[$tmp]->id);
		$this->wb_messages($tmp);
		$this->content['html'] .= "</div>";
	}
	
	function wb_cars_display(){
		if(is_array($this->carNum)){
			$g = "";
			$this->content['html'] .= "<br />";
			for($cn=0;$cn<count($this->carNum);$cn++){
				if(strlen(@$this->carNum[$cn]['NUM']) > 0 && @$this->carNum[$cn]['NUM'] != "UNDEFINED"){
					$this->content['html'] .= "<span style=\"color: #4169E1; font-size: 10pt; font-weight: bold\">";
					if(@array_key_exists($this->carNum[$cn]['NUM'],@$this->myCars)){$this->content['html'] .= "<a href=\"cars/edit/".$this->myCars[$this->carNum[$cn]['NUM']]->id."\">";}
					$this->content['html'] .= $this->carNum[$cn]['NUM']." (".$this->carNum[$cn]['AAR'].") (".@$this->arr['allRR'][$this->carNum[$cn]['RR']]->report_mark.")";
					if(@array_key_exists($this->carNum[$cn]['NUM'],@$this->myCars)){$this->content['html'] .= "</a>";}
					$this->content['html'] .= "</span> ";
					if(isset($this->myCars[$this->carNum[$cn]['NUM']])){$specInst = @$this->myCars[$this->carNum[$cn]['NUM']]->special_instruct;}
					else{$specInst = @$this->myCars[$this->carNum[$cn]]->special_instruct;}
					if(strlen($specInst) > 0){$this->content['html'] .= "<span style=\"font-weight: bold\"> - ".$specInst."</span>";$specInst = "";}
					$this->content['html'] .= "<br />";
					
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
		// Get latest Progress Report
		$sql = "SELECT * FROM `ichange_progress` WHERE `waybill_num` = '".$this->waybills[$tmp]->waybill_num."' ORDER BY id DESC, date DESC, time DESC LIMIT 1";
		$prog_res = (array)$this->Generic_model->qry($sql);
		$prog_all[0] = (array)$prog_res[0]; //json_decode($this->waybills[$tmp]->progress, true);
		$last_prog = 0; //count($prog_all) - 1; 
		$this->fld1_1 = $prog_all[$last_prog]['date']; //"Server Date/Time: ".$prog_all[$last_prog]['date'];
		$this->fld1_2 = $prog_all[$last_prog]['text'];
		$this->fld1_3 = $prog_all[$last_prog]['map_location'];
		$this->fld1_4 = ""; //if(isset($prog_all[$last_prog]['tzone']) && isset($_COOKIE['_tz'])){$this->fld1_4 = " (TZ Time: ".$prog_all[$last_prog]['tzone']." ".date('Y-m-d H:i',date('U')+$this->dates_times->get_timezone_offset($prog_all[$last_prog]['tzone'],$_COOKIE['_tz'])).")";}
		$this->fld1_5 = $prog_all[$last_prog]['time'];
	}
	
	function wb_update_frm($tmp){
		$sw_ord = ""; 
		for($sw=0;$sw<100;$sw++){
			$sw_sel = ""; if($this->waybills[$tmp]->sw_order == $sw){$sw_sel = " selected=\"selected\"";}
			$sw_ord .= "<option value=\"".$sw."\"".$sw_sel.">".$sw."</option>";
		}
		$str = "";
		$str .= "<span class=\"rr_tr\">Currently on / allocated to: ";
		$str .= "<select name=\"rr_sel[]\ style=\"font-size: 8pt; height: 22px;\" onchange=\"home_update('allocRR',this.value,".$this->waybills[$tmp]->id.");\"><option value=\"".@$this->waybills[$tmp]->rr_id_handling."\">".@$this->arr['allRR'][$this->waybills[$tmp]->rr_id_handling]->report_mark."</option>".$this->railroad_opts_lst."</select>";
		$str .= "&nbsp;&nbsp;SW Order: <select name=\"sw_order[]\" onchange=\"home_update('swOrd',this.value,".$this->waybills[$tmp]->id.");\">".$sw_ord."</select>"; 
		$str .= "<br />";
		$str .= form_hidden("wb_id[]",$this->waybills[$tmp]->id);
		$str .= "In Train: <select name=\"tr_sel[]\" style=\"font-size: 8pt; height: 22px;\" onchange=\"home_update('allocTrain',this.value,".$this->waybills[$tmp]->id.");\"><option selected=\"selected\" value=\"".@$this->waybills[$tmp]->train_id."\">".@$this->waybills[$tmp]->train_id."</option>".$this->trains_opts_lst."</select><br />";
		if(count($this->mricf->cars4RR4WB($this->arr['rr_sess'],$this->waybills[$tmp]->id)) > 1 && !in_array($this->waybills[$tmp]->lading,array("","MT","EMPTY","MTY"))){ 
			$stodat = $this->mricf->getStoredIndust($this->arr['rr_sess']);
			$str .= "Store: <select name=\"stome[]\" style=\"font-size: 8pt; height: 22px;\" onchange=\"if(this.value.length > 0){if(confirm('This will store the lading\\nfor the number of cars indicate\\nand mark this waybill as unloaded.\\n\\nAre you sure?')){ window.location = '".WEB_ROOT."/waybill/store/".$this->waybills[$tmp]->id."/'+this.value;} }\"><option value=\"\">To Bulk Store this WB, select...</option>";
			for($st=0;$st<count($stodat);$st++){
				$str .= "<option value=\"".$stodat[$st]->id."\">".substr($stodat[$st]->indust_name,0,35)."... (".$stodat[$st]->town.")</option>";
			} 
			$str .= "</select>";
		}
		$str .= form_hidden("wb_num[]",$this->waybills[$tmp]->waybill_num);
		$str .= "</span><br />";
		return $str;
	}
	
	function dt_styling(){
		if($this->fld1_1 == date('Y-m-d')){$this->fld1_1 = "<span style=\"background-color: yellow;\">".$this->fld1_1."</span>";}
	}

	// Bulk Update methods
	function bulk_update(){
		// Updates selections in BULK (all at once, rather than doing just one selection change at a time!)
		$rrs = $this->arr['allRR'];
		$this->arr = $_POST;
		$this->email_mess = "Waybills updated from MRICF Home Page ".date('Y-m-d H:i:s')." by ".$rrs[$this->input->cookie('rr_sess')]->report_mark."\n\n";
		for($i=0;$i<count($this->arr['wb_id']);$i++){
			$this->Generic_model->change("UPDATE `ichange_waybill` SET `sw_order` = '".$this->arr['sw_order'][$i]."', `rr_id_handling` = '".$this->arr['rr_sel'][$i]."', `train_id` = '".$this->arr['tr_sel'][$i]."' WHERE `id` = '".$this->arr['wb_id'][$i]."'");
			$em_tmp = "";
			if(strlen($this->arr['tr_sel'][$i])>0){$em_tmp .= "In Train: ".$this->arr['tr_sel'][$i].". ";}
			if($this->arr['rr_sel'][$i] != $this->input->cookie('rr_sess')){$em_tmp .= "RR Handling: ".$rrs[$this->arr['rr_sel'][$i]]->report_mark.". ";}
			if(strlen($em_tmp) > 0){$this->email_mess .= $this->arr['wb_num'][$i]." - ".$em_tmp."\n";}
		}
		$this->email_wb_to_grp();
		header("Location:".WEB_ROOT."/home");
	}
	
	function home_view_settings(){
		// Styling and view options for the home page - not yet completed - hard coded for now.
		if(strlen($this->arr['myRR'][0]->home_disp_v2) > 4){$this->arr['home_view_settings'] = @json_decode($this->arr['myRR'][0]->home_disp_v2,true);}
	}

	// Emailer method	
	function email_wb_to_grp(){
		// Sends an email to MRICC group
		$subject = 'Waybills updated from MRICF Home Page';
		
		$this->email->from('mricf@stanfordhosting.net', 'MRICF');
		$this->email->to('MRICC@yahoogroups.com');

		$this->email->subject($subject);
		$this->email->message($this->email_mess);

		$this->email->send();
		//echo nl2br($message);
	}

}
?>
