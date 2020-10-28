<?php
class Locos extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->helper('download');
		$this->load->library('mricf');
		
		$this->load->model('Locomotives_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Motive Power";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

		$this->avail_to_opts = array(0=>"Owner RR", 1=>"Owner+Affiliated RRs", 2=>"All RRs");

	}

	public function index(){
		$this->lst();
	}
	
	public function lst(){
		$this->arr['jquery'] = "\$('.table1').DataTable({ 
			paging: false, 
			searching: true, 
			responsive: true, 
			info: false, 
			stateSave: false,
			order: [[ 1, 'asc' ]] });";
		$this->arr['pgTitle'] .= " - List";
		$randpos = array();
		if(isset($_POST['search_for'])){
			$locosdat = (array)$this->Generic_model->get_search_results($_POST['search_for'],$_POST['search_in'],"ichange_locomotives");
		}else{$locosdat = (array)$this->Locomotives_model->getLocos4RR($this->arr['rr_sess']);}
		//$this->dat = array();
		$this->dat['fields'] 			= array('id', 'loco_num', 'model', 'hp', 'rr', 'avail_to', 'modified');
		$this->dat['field_names'] 		= array("ID", "Loco Num", "Model", "Horsepower", "Railroad", "Avail To", "Added/Modified");
		$this->dat['options']			= array(
				'Edit' => "locos/edit/"
			); // Paths to options method, with trailling slash!
		$this->dat['links']				= array(
				'New' => "locos/edit/0",
				'Export' => "locos/csv_export/"
			); // Paths for other links!
		
		for($i=0;$i<count($locosdat);$i++){
			$cui = array(); //(array)$this->Generic_model->qry("SELECT COUNT(`id`) AS `used_cntr` FROM `ichange_locosused_index` WHERE `loco_num` = '".$locosdat[$i]->loco_num."'");
			$used_style = "font-weight: normal;";
			//if($cui[0]->used_cntr > 3){$used_style = "font-weight: bold; font-size: 11pt;";}
			//if($cui[0]->used_cntr > 5){$used_style = "font-weight: bolder; font-size: 12pt;";}
			$emph = "<span style=\"".$used_style.";\">"; 
			
			$emph_end = "</span>";
			$this->dat['data'][$i]['id'] 						= $locosdat[$i]->id;
			$this->dat['data'][$i]['loco_num']				 	= $emph.$locosdat[$i]->loco_num.$emph_end;
			$this->dat['data'][$i]['model'] 				= $emph.$locosdat[$i]->model.$emph_end;
			$this->dat['data'][$i]['hp'] 					= $emph.$locosdat[$i]->hp.$emph_end;
			$this->dat['data'][$i]['rr'] 						= $emph.$this->mricf->qry("ichange_rr",$locosdat[$i]->rr,"id","report_mark").$emph_end;
			$this->dat['data'][$i]['avail_to'] 						= $emph.$this->avail_to_opts[$locosdat[$i]->avail_to].$emph_end;
			$this->dat['data'][$i]['modified']					= "";
			if($locosdat[$i]->added > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$locosdat[$i]->added);}
			if($locosdat[$i]->modified > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$locosdat[$i]->modified);}
		}

		$this->search_build();

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		//$this->load->view('list', $this->dat);
		$this->load->view('table', $this->dat);
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
			$this->dat['data'][0]->rr = $this->arr['rr_sess'];
		}else{
			$this->arr['pgTitle'] .= " - Edit";
			$this->dat['data'] = (array)$this->Locomotives_model->get_single($id);
		}
		
		//echo "<pre>"; print_r($this->dat['data']); echo "</pre>";
		$this->setFieldSpecs(); // Set field specs
		for($i=0;$i<count($this->field_defs);$i++){
			$this->dat['field_names'][$i] = $this->field_defs[$i]['label'];
			if($this->field_defs[$i]['type'] == "input"){$this->dat['fields'][$i] = "<br />".form_input($this->field_defs[$i]['def']);}
			if($this->field_defs[$i]['type'] == "textarea"){$this->dat['fields'][$i] = "<br />".form_textarea($this->field_defs[$i]['def']);}
			if($this->field_defs[$i]['type'] == "select"){$this->dat['fields'][$i] = "<br />".form_dropdown($this->field_defs[$i]['name'],$this->field_defs[$i]['options'],$this->field_defs[$i]['value'],$this->field_defs[$i]['other']);}
		}
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		if($this->arr['rr_sess'] > 0){$this->load->view('edit', $this->dat);}
		else{
			$this->load->view('not_allowed');
		}
		$this->load->view('footer');
	}

	function csv_export(){
		$this->load->dbutil();
		//$qry = $this->Generic_model->qry($this->sql);
		$qry = $this->db->query("SELECT * FROM `ichange_locos` WHERE `rr` = '".$this->arr['rr_sess']."'");
		$delimiter = ",";
		$newline = "\r\n";
		force_download('locos.csv', $this->dbutil->csv_from_result($qry, $delimiter, $newline));

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
		for($i=0;$i<count($aar_tmp);$i++){$aar_opts[$aar_tmp[$i]->aar_code] = $aar_tmp[$i]->aar_code." - ".substr($aar_tmp[$i]->desc,0,70);}
		
		$rr_opts = array();
		$rr_tmp = (array)$this->Railroad_model->get_allActive();
		for($i=0;$i<count($rr_tmp);$i++){$rr_opts[$rr_tmp[$i]->id] = $rr_tmp[$i]->report_mark." - ".substr($rr_tmp[$i]->rr_name,0,70);}
		
		// Add form and field definitions specific to this controller under this line... 
		$this->dat['hidden'] = array('tbl' => 'locos', 'id' => @$this->dat['data'][0]->id);
		$this->dat['form_url'] = "../save";
		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Loco Number', 'def' => array(
              'name'        => 'loco_num',
              'id'          => 'loco_num',
              'value'       => @$this->dat['data'][0]->loco_num,
              'maxlength'   => '25',
              'size'        => '25'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Manufacturer', 'def' => array(
              'name'        => 'manufacturer',
              'id'          => 'manufacturer',
              'value'       => @$this->dat['data'][0]->manufacturer,
              'maxlength'   => '25',
              'size'        => '25'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Model', 'def' => array(
              'name'        => 'model',
              'id'          => 'model',
              'value'       => @$this->dat['data'][0]->model,
              'maxlength'   => '25',
              'size'        => '25'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Horsepower Rating', 'def' => array(
              'name'        => 'hp',
              'id'          => 'hp',
              'value'       => @$this->dat['data'][0]->hp,
              'maxlength'   => '5',
              'size'        => '5'
			)
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Owner RR', 'name' => 'rr', 'value' => @$this->dat['data'][0]->rr, 
			'other' => 'id="rr"', 'options' => $rr_opts
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Available To', 'name' => 'avail_to', 'value' => @$this->dat['data'][0]->avail_to, 
			'other' => 'id="avail_to"', 'options' => $this->avail_to_opts
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
	}

	function search_build(){
		/*
		// Builds html for railroad listing.
		$this->dat['shtml'] = "<div class=\"box1\" style=\"left: 120px;\">";
		if(isset($_POST['search_for'])){$this->dat['shtml'] .= anchor("../locos","My locos");}
		else{
			$this->dat['shtml'] .= "&nbsp;<a href=\"#\" id=\"search_expand\"><strong>Search</strong></a>&nbsp;<a href=\"#\" id=\"search_shrink\">Shrink</a><br />";
			$this->dat['shtml'] .= "<div id=\"search\" style=\"display: none;\">";
			//echo "<pre>"; print_r($this->myTrains); echo "</pre>";
			//echo "<pre>"; print_r($this->wbs_all); echo "</pre>";
		
			$search_opts = array('loco_num' => "loco Report Mark & Num", 'aar_type' => "AAR Code", 'desc' => "Description", 'location' => "Location");
			$this->dat['shtml'] .= form_open_multipart("../locos");
			$this->dat['shtml'] .= "For ".form_input('search_for')."<br />";
			$this->dat['shtml'] .= "In ".form_dropdown('search_in',$search_opts);
			$this->dat['shtml'] .= " ".form_submit('submit','Search');
			$this->dat['shtml'] .= form_close();

			$this->dat['shtml'] .= "</div>";
		}
		$this->dat['shtml'] .= "</div>";
		*/
	}

}
?>
