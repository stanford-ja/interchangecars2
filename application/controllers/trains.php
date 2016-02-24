<?php
class Trains extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	var $arr = array(
			'pgTitle' => "MRICF - Model Rail Interchangecars Facility" , 
			'rr_sess' => 0
		);
	
	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('mricf');
		$this->load->library('formgen');
		
		$this->load->model('Train_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Locomotives_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Railroad_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Trains";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

		$rrArrTmp = $this->mricf->rrFullArr();
		$rrArrTmp_kys = array_keys($rrArrTmp);
		for($r=0;$r<count(array_keys($rrArrTmp_kys));$r++){$this->arr[$rrArrTmp_kys[$r]] = $rrArrTmp[$rrArrTmp_kys[$r]];}

		$this->arr['allRR'][$this->arr['rr_sess']]->show_affil_wb = 1; // Required to get affil locos using affil_ids method! Not required elsewhere in class.
		$this->my_rr_ids = $this->mricf->affil_ids($this->arr['rr_sess'],$this->arr['allRR']);

	}

	public function index(){
		$this->lst();
	}
	
	public function lst(){
		$this->arr['pgTitle'] .= " - List";
		$randpos = array();
		if(isset($_POST['search_for'])){
			$traindat = (array)$this->Generic_model->get_search_results($_POST['search_for'],$_POST['search_in'],"ichange_trains");
		}else{$traindat = (array)$this->Train_model->get_all4RR_Sorted($this->arr['rr_sess']);}
		//$this->dat = array();
		$this->dat['fields'] 			= array('id', 'train_id', 'train_desc', 'loco_num', 'origin', 'destination', 'location' , 'railroad_id', 'tr_sheet_ord','modified');
		$this->dat['field_names'] 		= array("ID", "Train ID", "Description", "Motive Power", "Origin", "Destination", "Location", "Railroad", "Sheet Order","Added/Modified");
		$this->dat['options']			= array(
				'Edit' => "trains/edit/", 
				'Switchlist' => "switchlist/lst/"
			); // Paths to options method, with trailling slash!
		$this->dat['links']				= array(
				'New' => "trains/edit/0"
			); // Paths for other links!
		$this->dat['before_table'] = "Train Sheets: <select onchange=\"window.location = '".WEB_ROOT."/' + this.value;\">".
			"<option value=\"\" selected=\"selected\">-- Select --</option>".
			"<option value=\"trains/sheet/sun/0\">Sun, no Auto</option>".
			"<option value=\"trains/sheet/sun/1\">Sun, incl. Auto</option>".
			"<option value=\"trains/sheet/mon/0\">Mon, no Auto</option>".
			"<option value=\"trains/sheet/mon/1\">Mon, incl. Auto</option>".
			"<option value=\"trains/sheet/tues/0\">Tue, no Auto</option>".
			"<option value=\"trains/sheet/tues/1\">Tue, incl. Auto</option>".
			"<option value=\"trains/sheet/wed/0\">Wed, no Auto</option>".
			"<option value=\"trains/sheet/wed/1\">Wed, incl. Auto</option>".
			"<option value=\"trains/sheet/thu/0\">Thu, no Auto</option>".
			"<option value=\"trains/sheet/thu/1\">Thu, incl. Auto</option>".
			"<option value=\"trains/sheet/fri/0\">Fri, no Auto</option>".
			"<option value=\"trains/sheet/fri/1\">Fri, incl. Auto</option>".
			"<option value=\"trains/sheet/sat/0\">Sat, no Auto</option>".
			"<option value=\"trains/sheet/sat/1\">Sat, incl. Auto</option>".
			"</select>";
			/*
				'[Sun, no Auto]' => "trains/sheet/sun/0'", 
				'[Sun, incl. Auto]' => "trains/sheet/sun/1", 
				'[Mon, no Auto]' => "trains/sheet/mon/0", 
				'[Mon, incl. Auto]' => "trains/sheet/mon/1", 
				'[Tue, no Auto]' => "trains/sheet/tues/0", 
				'[Tue, incl. Auto]' => "trains/sheet/tues/1", 
				'[Wed, no Auto]' => "trains/sheet/wed/0", 
				'[Wed, incl. Auto]' => "trains/sheet/wed/1", 
				'[Thu, no Auto]' => "trains/sheet/thu/0", 
				'[Thu, incl. Auto]' => "trains/sheet/thu/1", 
				'[Fri, no Auto]' => "trains/sheet/fri/0", 
				'[Fri, incl. Auto]' => "trains/sheet/fri/1", 
				'[Sat, no Auto]' => "trains/sheet/sat/0", 
				'[Sat, incl. Auto]' => "trains/sheet/sat/1" 
			*/
		
		for($i=0;$i<count($traindat);$i++){
			$aut_inf = "";
			if(intval($traindat[$i]->auto) > 0){
				$aut_inf .= "<br />Auto Train Data: ";
				$aut_inf .= $traindat[$i]->auto." day/s";
			}elseif(json_decode($traindat[$i]->auto)){
				$aut = @json_decode($traindat[$i]->auto, true);
				$aut_inf .= "<br />Auto Train Data: ";
				$aut_ky = @array_keys($aut);
				for($t=0;$t<count($aut_ky);$t++){
					if($t > 0){$aut_inf .= ", ";}
					$aut_inf .= $aut_ky[$t]." (".$aut[$aut_ky[$t]]." day/s)";
				}
			}
			if($aut_inf != ''){$aut_inf = "<span style=\"color: #555; font-size: 9pt;\">".$aut_inf."</span>";}

			$this->dat['data'][$i]['id'] 						= $traindat[$i]->id;
			$this->dat['data'][$i]['train_id']			 		= $traindat[$i]->train_id;
			$this->dat['data'][$i]['train_desc'] 				= $traindat[$i]->train_desc.$aut_inf;
			$this->dat['data'][$i]['loco_num']			 		= $traindat[$i]->loco_num;
			$this->dat['data'][$i]['origin']					= $traindat[$i]->origin;
			$this->dat['data'][$i]['destination']				= $traindat[$i]->destination;
			$this->dat['data'][$i]['location']				= $traindat[$i]->location;
			$this->dat['data'][$i]['railroad_id']				= $this->mricf->qry("ichange_rr",$traindat[$i]->railroad_id,"id","report_mark");
			$this->dat['data'][$i]['tr_sheet_ord']			= $traindat[$i]->tr_sheet_ord;
			$this->dat['data'][$i]['modified']					= "";
			if($traindat[$i]->added > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$traindat[$i]->added);}
			if($traindat[$i]->modified > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$traindat[$i]->modified);}
		}

		$this->search_build();

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		if($this->arr['rr_sess'] > 0){
			$this->load->view('list', $this->dat);
		}else{
			$this->load->view('not_allowed');
		}
		$this->load->view('footer');
	}
	
	public function edit($id=0){
		// Used for editing existing (edit/[id]) and adding new (edit/0) records
		$this->load->helper('form');
		$this->dat['attribs'] = array('name' => "form"); // Attribs for form tag
		$this->dat['fields'] = array();
		$this->dat['field_names'] = array();
		if($id < 1){
			$this->arr['pgTitle'] .= " - New";
			$this->dat['data'][0]->id = 0;
			$this->dat['data'][0]->railroad_id = $this->arr['rr_sess'];
		}else{
			$this->arr['pgTitle'] .= " - Edit";
			$this->dat['data'] = (array)$this->Train_model->get_single($id);
		}
		
		//echo "<pre>"; print_r($this->dat['data']); echo "</pre>";
		$this->setFieldSpecs(); // Set field specs
		$this->formgen->setFormElements();
		/* REPLACED BY setFormElements METHOD ABOVE!
		for($i=0;$i<count($this->field_defs);$i++){
			$this->dat['field_names'][$i] = $this->field_defs[$i]['label'];
			if($this->field_defs[$i]['type'] == "checkbox"){
				$this->dat['fields'][$i] = form_checkbox($this->field_defs[$i]['def']).$this->dat['field_names'][$i];
				$this->dat['field_names'][$i] = "";
			}
			if($this->field_defs[$i]['type'] == "radio"){
				$this->dat['fields'][$i] = form_radio($this->field_defs[$i]['def']).$this->dat['field_names'][$i];
				$this->dat['field_names'][$i] = "";
			}
			if($this->field_defs[$i]['type'] == "input"){$this->dat['fields'][$i] = "<br />".form_input($this->field_defs[$i]['def']);}
			if($this->field_defs[$i]['type'] == "textarea"){$this->dat['fields'][$i] = "<br />".form_textarea($this->field_defs[$i]['def']);}
			if($this->field_defs[$i]['type'] == "select"){$this->dat['fields'][$i] = "<br />".form_dropdown($this->field_defs[$i]['name'],$this->field_defs[$i]['options'],$this->field_defs[$i]['value'],$this->field_defs[$i]['other']);}
			if($this->field_defs[$i]['type'] == "statictext"){$this->dat['fields'][$i] = "<br />".$this->field_defs[$i]['value'];}
		}
		*/
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		if($this->arr['rr_sess'] > 0){$this->load->view('edit', $this->dat);}
		else{
			$this->load->view('not_allowed');
		}
		$this->load->view('footer');
	}

	function sheet($day="sun",$auto=0){
		// Train Sheet for day = $tarr['day'], (sun,mon,tues,wed,thu,fri,sat)
		// Railroad $tarr['rr']; Show Auto Trains $tarr['auto']
		if(isset($_POST['day'])){$tarr = $_POST;}
		else{$tarr = array('day' => $day, 'auto' => $auto);}
		$this->arr['pgTitle'] .= " - Train Sheet for ".ucwords($tarr['day']);
		$randpos = array();
		$traindat = (array)$this->Train_model->get_all4day_sorted($this->arr['rr_sess'],$tarr['day'],$tarr['auto']); //get_all4RR_Sorted($this->arr['rr_sess']);
		//$this->dat = array();
		$this->dat['fields'] 			= array('id', 'train_id', 'train_desc', 'no_cars', 'op_notes', 'direction', 'tr_sheet_ord', 'origin', 'destination','location');
		$this->dat['field_names'] 		= array("ID", "Train ID", "Description", "Max Cars", "Op Notes", "Direction", "Sheet Order", "Origin", "Destination","Location (Enter a new location and click Update to change)");
		$this->dat['options']			= array(
				'Crew&nbsp;Allocated' => $_SERVER['SCRIPT_NAME']."/trains/crewTrId/".$tarr['day']."/".$tarr['auto']."/", 
				'Started' => $_SERVER['SCRIPT_NAME']."/trains/strtTrId/".$tarr['day']."/".$tarr['auto']."/", 
				'Completed' => $_SERVER['SCRIPT_NAME']."/trains/compTrId/".$tarr['day']."/".$tarr['auto']."/"
			); // Paths to options method, with trailling slash!
		$this->dat['links']				= array(
				'Train List' => $_SERVER['SCRIPT_NAME']."/trains", 
				'Reset Train Statuses' => array('href' => "javascript:{}", 'onclick' => "if(confirm('Are you sure you want to reset the status of all your trains?')){window.location = '".$_SERVER['SCRIPT_NAME']."/trains/compReset/".$tarr['day']."/".$tarr['auto']."';}")
			); // Paths for other links!
		
		for($i=0;$i<count($traindat);$i++){
			$aut_inf = "";
			$is_auto = 0;
			if(intval($traindat[$i]->auto) > 0){
				$aut_inf .= "<br />Auto Train Data: ";
				$aut_inf .= $traindat[$i]->auto." day/s";
				$is_auto = 1;
			}elseif(json_decode($traindat[$i]->auto)){
				$aut = @json_decode($traindat[$i]->auto, true);
				$aut_inf .= "<br />Auto Train Data: ";
				$aut_ky = @array_keys($aut);
				for($t=0;$t<count($aut_ky);$t++){
					if($t > 0){$aut_inf .= ", ";}
					$aut_inf .= $aut_ky[$t]." (".$aut[$aut_ky[$t]]." day/s)";
				}
				$is_auto = 1;
			}
			if($aut_inf != ''){$aut_inf = "<span style=\"color: #555; font-size: 9pt;\">".$aut_inf."</span>";}

			$c_omp = "";
			if(strtoupper($traindat[$i]->complete) == "Y"){$c_omp = "<br /><span style=\"background-color:yellow; color: blue\">[COMPLETED]</span>";}
			if(strtoupper($traindat[$i]->complete) == "S"){$c_omp = "<br /><span style=\"background-color:brown; color: white\">[STARTED]</span>";}
			if(strtoupper($traindat[$i]->complete) == "C"){$c_omp = "<br /><span style=\"background-color:gray; color: yellow\">[CREW ALLOCATED]</span>";}

			//if(($is_auto == 0 && $tarr['auto'] == 0) || $tarr['auto'] == 1){
				$this->dat['data'][$i]['id'] 						= $traindat[$i]->id;
				$this->dat['data'][$i]['train_id']			 		= $traindat[$i]->train_id.$c_omp;
				$this->dat['data'][$i]['train_desc'] 				= $traindat[$i]->train_desc.$aut_inf;
				$this->dat['data'][$i]['no_cars'] 				= $traindat[$i]->no_cars;
				$this->dat['data'][$i]['op_notes'] 				= $traindat[$i]->op_notes;
				$this->dat['data'][$i]['direction'] 				= $traindat[$i]->direction;
				$this->dat['data'][$i]['tr_sheet_ord']			= $traindat[$i]->tr_sheet_ord;
				$this->dat['data'][$i]['origin']					= $traindat[$i]->origin;
				$this->dat['data'][$i]['destination']				= $traindat[$i]->destination;
				$this->dat['data'][$i]['location']				= "<input type=\"text\" name=\"location".$traindat[$i]->id."\" style=\"border: 1px solid #555;\" value=\"".@$traindat[$i]->location."\" onchange=\"window.location = '".$_SERVER['SCRIPT_NAME']."/trains/locationTrId/".$tarr['day']."/".$tarr['auto']."/".$traindat[$i]->id."/' + this.value;\"/>&nbsp;<input type=\"button\" value=\"Update\" />";
				//$this->dat['data'][$i]['railroad_id']				= $this->mricf->qry("ichange_rr",$traindat[$i]->railroad_id,"id","report_mark");
			//}
		}

		$this->search_build();

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		if($this->arr['rr_sess'] > 0){
			$this->load->view('list', $this->dat);
		}else{
			$this->load->view('not_allowed');
		}
		$this->load->view('footer');
	}
	
	public function setFieldSpecs(){
		// Sets specific field definitions for the controller being used.
		$this->dat['fields'] = array();
		
		// Add custom model calls / queries under this line...
		//$this->load->model('Aar_model', '', TRUE);
		//$this->load->model('Railroad_model', '', TRUE);
		
		// Add other code for fields under this line...
		/*
		$aar_opts = array();
		$aar_tmp = (array)$this->Aar_model->get_allSorted();
		for($i=0;$i<count($aar_tmp);$i++){$aar_opts[$aar_tmp[$i]->aar_code] = $aar_tmp[$i]->aar_code." - ".substr($aar_tmp[$i]->desc,0,70);}
		*/
		
		$rr_opts = array();
		$rr_tmp = (array)$this->Railroad_model->get_allActive();
		for($i=0;$i<count($rr_tmp);$i++){$rr_opts[$rr_tmp[$i]->id] = $rr_tmp[$i]->report_mark." - ".substr($rr_tmp[$i]->rr_name,0,70);}

		$tr_opts = array('' => "Select one");
		$tr_tmp = (array)$this->Locomotives_model->getLocos4RR($this->arr['rr_sess'],array('rr','avail_to','loco_num'),$this->my_rr_ids,1);
		for($i=0;$i<count($tr_tmp);$i++){
			$who_owns = "This RR";
			if($tr_tmp[$i]->avail_to == 1 && $tr_tmp[$i]->rr != $this->arr['rr_sess']){$who_owns = "Affiliate RR";}
			if($tr_tmp[$i]->avail_to == 2 && $tr_tmp[$i]->rr != $this->arr['rr_sess']){$who_owns = "Other RR";}
			$tr_opts[$tr_tmp[$i]->loco_num] = $tr_tmp[$i]->loco_num." - ".$tr_tmp[$i]->model." (".$who_owns.")";
		}
		
		// Add form and field definitions specific to this controller under this line... 
		$this->dat['hidden'] = array('tbl' => 'trains', 'id' => @$this->dat['data'][0]->id);
		$this->dat['form_url'] = "../save";
		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Train ID', 'def' => array(
              'name'        => 'train_id',
              'id'          => 'train_id',
              'value'       => @$this->dat['data'][0]->train_id,
              'maxlength'   => '20',
              'size'        => '20'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Train Description', 'def' => array(
              'name'        => 'train_desc',
              'id'          => 'train_desc',
              'value'       => @$this->dat['data'][0]->train_desc,
              'maxlength'   => '60',
              'size'        => '60'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Max Cars', 'def' => array(
              'name'        => 'no_cars',
              'id'          => 'no_cars',
              'value'       => @$this->dat['data'][0]->no_cars,
              'maxlength'   => '3',
              'size'        => '3'
			)
		);

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Operation Notes', 'def' => array(
              'name'        => 'op_notes',
              'id'          => 'op_notes',
              'value'       => @$this->dat['data'][0]->op_notes,
              'rows'			 => '5',
              'cols'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Railroad', 'name' => 'railroad_id', 'value' => @$this->dat['data'][0]->railroad_id, 
			'other' => 'id="railroad_id"', 'options' => $rr_opts
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Locomotive', 'name' => 'loco_num', 'value' => @$this->dat['data'][0]->loco_num, 
			'other' => 'id="loco_num"', 'options' => $tr_opts
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Origin', 'def' => array(
              'name'        => 'origin',
              'id'          => 'origin',
              'value'       => @$this->dat['data'][0]->origin,
              'maxlength'   => '45',
              'size'        => '45'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Destination', 'def' => array(
              'name'        => 'destination',
              'id'          => 'destination',
              'value'       => @$this->dat['data'][0]->destination,
              'maxlength'   => '45',
              'size'        => '45'
			)
		);

		$this->field_defs[] =  array(
			'type' => "statictext", 'label' => '',
			'value' => '<div style="background-color: antiquewhite; border: 1px solid red; padding: 5px; font-size: 9pt;">Train Sheet Order / Depart Time can be either an integer train sheet order, or a time in 24 hours format such as HHMM or HH:MM. All trains for your railroad need to have the same format for this field for them to be displayed / printed in the proper order.</div>'
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Train Sheet Order / Depart Time', 'def' => array(
              'name'        => 'tr_sheet_ord',
              'id'          => 'tr_sheet_ord',
              'value'       => @$this->dat['data'][0]->tr_sheet_ord,
              'maxlength'   => '6',
              'size'        => '6'
			)
		);

		$this->field_defs[] =  array(
			'type' => "statictext", 'label' => '',
			'value' => '<strong>Days Operated On:</strong><br /><i>Set which days the train operates on by selecting an option for the days indicated.</i>'
		);


		$dy = array();
		$runs_or_not = array(0 => 'Doesnt Operate this day', 1 => 'Operates this day');
		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Sun', 'name' => 'sun', 'value' => @$this->dat['data'][0]->sun, 
			'other' => 'id="sun"', 'options' => $runs_or_not
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Monday', 'name' => 'mon', 'value' => @$this->dat['data'][0]->mon, 
			'other' => 'id="mon"', 'options' => $runs_or_not
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Tuesday', 'name' => 'tues', 'value' => @$this->dat['data'][0]->tues, 
			'other' => 'id="tues"', 'options' => $runs_or_not
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Wednesday', 'name' => 'wed', 'value' => @$this->dat['data'][0]->wed, 
			'other' => 'id="wed"', 'options' => $runs_or_not
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Thursday', 'name' => 'thu', 'value' => @$this->dat['data'][0]->thu, 
			'other' => 'id="thu"', 'options' => $runs_or_not
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Friday', 'name' => 'fri', 'value' => @$this->dat['data'][0]->fri, 
			'other' => 'id="fri"', 'options' => $runs_or_not
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Saturday', 'name' => 'sat', 'value' => @$this->dat['data'][0]->sat, 
			'other' => 'id="sat"', 'options' => $runs_or_not
		);
/*
		$dy['sun'] = FALSE; if(@$this->dat['data'][0]->sun == 1){$dy['sun'] = TRUE;}
		$this->field_defs[] =  array(
			'type' => "checkbox", 'label' => 'Sun', 'def' => array(
				'name'        => 'sun',
				'id'          => 'sun',
				'value'       => '1',
				'checked'     => $dy['sun'],
				'style'       => 'margin:10px',
			)
 	   );

		$dy['mon'] = FALSE; if(@$this->dat['data'][0]->mon == 1){$dy['mon'] = TRUE;}
		$this->field_defs[] =  array(
			'type' => "checkbox", 'label' => 'Mon', 'def' => array(
				'name'        => 'mon',
				'id'          => 'mon',
				'value'       => '1',
				'checked'     => $dy['mon'],
				'style'       => 'margin:10px',
			)
 	   );

		$dy['tues'] = FALSE; if(@$this->dat['data'][0]->tues == 1){$dy['tues'] = TRUE;}
		$this->field_defs[] =  array(
			'type' => "checkbox", 'label' => 'Tue', 'def' => array(
				'name'        => 'tues',
				'id'          => 'tues',
				'value'       => '1',
				'checked'     => $dy['tues'],
				'style'       => 'margin:10px',
			)
 	   );

		$dy['wed'] = FALSE; if(@$this->dat['data'][0]->wed == 1){$dy['wed'] = TRUE;}
		$this->field_defs[] =  array(
			'type' => "checkbox", 'label' => 'Wed', 'def' => array(
				'name'        => 'wed',
				'id'          => 'wed',
				'value'       => '1',
				'checked'     => $dy['wed'],
				'style'       => 'margin:10px',
			)
 	   );

		$dy['thu'] = FALSE; if(@$this->dat['data'][0]->thu == 1){$dy['thu'] = TRUE;}
		$this->field_defs[] =  array(
			'type' => "checkbox", 'label' => 'Thu', 'def' => array(
				'name'        => 'thu',
				'id'          => 'thu',
				'value'       => '1',
				'checked'     => $dy['thu'],
				'style'       => 'margin:10px',
			)
 	   );

		$dy['fri'] = FALSE; if(@$this->dat['data'][0]->fri == 1){$dy['fri'] = TRUE;}
		$this->field_defs[] =  array(
			'type' => "checkbox", 'label' => 'Fri', 'def' => array(
				'name'        => 'fri',
				'id'          => 'fri',
				'value'       => '1',
				'checked'     => $dy['fri'],
				'style'       => 'margin:10px',
			)
 	   );

		$dy['sat'] = FALSE; if(@$this->dat['data'][0]->sat == 1){$dy['sat'] = TRUE;}
		$this->field_defs[] =  array(
			'type' => "checkbox", 'label' => 'Sat', 'def' => array(
				'name'        => 'sat',
				'id'          => 'sat',
				'value'       => '1',
				'checked'     => $dy['sat'],
				'style'       => 'margin:10px',
			)
 	   );
*/

		$this->field_defs[] =  array(
			'type' => "statictext", 'label' => '',
			'value' => '<div style="background-color: antiquewhite; border: 1px solid red; padding: 5px; font-size: 9pt;">
				<strong>Auto Train Waypoints</strong> <a href="javascript:{}" onclick="winOpn(\'http://jstan2.pairserver.com/apps/interchangecars2/legacy/train_wps.php?id=\'+document.form.id.value,450,450);">Manage Auto Train Waypoints</a><br />
				To enter the number of days a train take to reach its destination only, enter the number in the Days / Waypoints for Auto Train field. To manage multiple waypoints for a train click the Manage Auto Train Waypoints link above.</div>'
		);

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Days / Waypoints for Auto Train', 'def' => array(
              'name'        => 'auto',
              'id'          => 'auto',
              'value'       => @$this->dat['data'][0]->auto,
              'rows'			 => '3',
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
			'type' => "textarea", 'label' => 'Train Description', 'def' => array(
              'name'        => 'generates',
              'id'          => 'generates',
              'value'       => @$this->dat['data'][0]->generates,
              'rows'			 => '3',
              'cols'        => '50'
			)
		);
		*/
		
		/*
		$this->field_defs[] =  array(
			'type' => "statictext", 'label' => '<br />Not Complete',
			'value' => '<h3>THIS VIEW HAS NOT BEEN SET UP YET!</h3>'
		);
		*/

	}

	function search_build(){		
		// Builds html for railroad listing.
		$this->dat['shtml'] = "<div class=\"box1\" style=\"left: 120px;\">";
		if(isset($_POST['search_for'])){$this->dat['shtml'] .= anchor("../trains","My Trains");}
		else{
			$this->dat['shtml'] .= "&nbsp;<a href=\"#\" id=\"search_expand\"><strong>Search</strong></a>&nbsp;<a href=\"#\" id=\"search_shrink\">Shrink</a><br />";
			$this->dat['shtml'] .= "<div id=\"search\" style=\"display: none;\">";
			//echo "<pre>"; print_r($this->myTrains); echo "</pre>";
			//echo "<pre>"; print_r($this->wbs_all); echo "</pre>";
		
			$search_opts = array('train_id' => "Train ID", 'train_desc' => "Description", 'op_notes' => "Op Notes", 'origin' => "Origin", 'destination' => "Destination", 'auto' => "Auto Train Waypoints");
			$this->dat['shtml'] .= "<strong>Search</strong>";
			$this->dat['shtml'] .= form_open_multipart("../trains");
			$this->dat['shtml'] .= "For ".form_input('search_for')."<br />";
			$this->dat['shtml'] .= "In ".form_dropdown('search_in',$search_opts);
			$this->dat['shtml'] .= " ".form_submit('submit','Search');
			$this->dat['shtml'] .= form_close();

			$day_opts = array('sun' => "Sunday", 'mon' => "Monday", 'tues' => "Tuesday", 'wed' => "Wednesday", 'thu' => "Thursday", 'fri' => "Friday", 'sat' => "Saturday");
			$auto_opts = array(0 => "No", 1 => "Yes");
			$this->dat['shtml'] .= "<hr /><strong>Train Sheet</strong>";
			$this->dat['shtml'] .= form_open_multipart("../trains/sheet");
			//$this->dat['shtml'] .= form_hidden('rr',$this->arr['rr_sess']);
			$this->dat['shtml'] .= "Day ".form_dropdown('day',$day_opts)."<br />";
			$this->dat['shtml'] .= "Show Auto Trains ".form_dropdown('auto',$auto_opts);
			$this->dat['shtml'] .= " ".form_submit('submit','Display');
			$this->dat['shtml'] .= form_close();

			$this->dat['shtml'] .= "</div>";
		}
		$this->dat['shtml'] .= "</div>";
	}

	// Methods for changing status of train on train sheet
	function crewTrId($day="sun",$auto=0,$id=0){
		// Allocate crew
		$arr = array('id' => $id);
		$this->Train_model->crewTrId($arr);
		header("Location:".$_SERVER['SCRIPT_NAME']."/trains/sheet/".$day."/".$auto);
	}

	function locationTrId($day="sun",$auto=0,$id=0,$location=''){
		// Allocate crew
		$arr = array('id' => $id);
		//$this->Train_model->crewTrId($arr);
		$location = str_replace("%20"," ",$location);
		$location = strtoupper($location);
		$this->Generic_model->change("UPDATE `ichange_trains` SET `location` = '".$location."' WHERE `id` = '".$id."'");
		header("Location:".$_SERVER['SCRIPT_NAME']."/trains/sheet/".$day."/".$auto);
	}

	function compTrId($day="sun",$auto=0,$id=0){
		// Complete train
		$arr = array('id' => $id);
		$this->Train_model->compTrId($arr);
		header("Location:".$_SERVER['SCRIPT_NAME']."/trains/sheet/".$day."/".$auto);
	}
	
	function strtTrId($day="sun",$auto=0,$id=0){
		// Start train
		$arr = array('id' => $id);
		$this->Train_model->strtTrId($arr);
		header("Location:".$_SERVER['SCRIPT_NAME']."/trains/sheet/".$day."/".$auto);
	}

	function compReset($day="sun",$auto=0){
		$arr = array('railroad_id' => $this->arr['rr_sess']);
		$this->Train_model->compReset($arr);
		header("Location:".$_SERVER['SCRIPT_NAME']."/trains/sheet/".$day."/".$auto);
	}

}
?>
