<?php
class Cars extends CI_Controller {
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
		
		$this->load->model('Cars_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Cars";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

	}

	public function index(){
		$this->lst();
	}
	
	public function lst(){
		$this->arr['pgTitle'] .= " - List";
		$randpos = array();
		if(isset($_POST['search_for'])){
			$carsdat = (array)$this->Generic_model->get_search_results($_POST['search_for'],$_POST['search_in'],"ichange_cars");
		}else{$carsdat = (array)$this->Cars_model->getCars4RR($this->arr['rr_sess']);}
		//$this->dat = array();
		$this->dat['fields'] 			= array('id', 'car_num', 'aar_type', 'desc', 'rr', 'used_cntr','modified');
		$this->dat['field_names'] 		= array("ID", "Car Num", "AAR Type", "Description", "Railroad", "Used # Times","Added/Modified");
		$this->dat['options']			= array(
				'Edit' => "cars/edit/",
				'Upload Image' => "graphics/car/"
			); // Paths to options method, with trailling slash!
		$this->dat['links']				= array(
				'New' => "cars/edit/0",
				'Export' => "cars/csv_export/"
			); // Paths for other links!
		
		for($i=0;$i<count($carsdat);$i++){
			$cui = (array)$this->Generic_model->qry("SELECT COUNT(`id`) AS `used_cntr` FROM `ichange_carsused_index` WHERE `car_num` = '".$carsdat[$i]->car_num."'");
			$used_style = "font-weight: normal;";
			if($cui[0]->used_cntr > 3){$used_style = "font-weight: bold; font-size: 11pt;";}
			if($cui[0]->used_cntr > 5){$used_style = "font-weight: bolder; font-size: 12pt;";}
			$emph = "<span style=\"".$used_style.";\">"; 
			
			$emph_end = "</span>";
			$car_img = ""; if(file_exists(DOC_ROOT."/car_images/".str_replace("&","",$carsdat[$i]->car_num).".jpg")){
				$car_img = "<br /><img src=\"".WEB_ROOT."/car_images/".str_replace("&","",$carsdat[$i]->car_num).".jpg\" style=\"width: 120px;\" />";
			}
			$this->dat['data'][$i]['id'] 						= $carsdat[$i]->id;
			$this->dat['data'][$i]['car_num']				 	= $emph.$carsdat[$i]->car_num.$emph_end;
			$this->dat['data'][$i]['aar_type'] 				= $emph.$carsdat[$i]->aar_type.$emph_end;
			$this->dat['data'][$i]['desc'] 					= $emph.$carsdat[$i]->desc.$emph_end.$car_img;
			//$this->dat['data'][$i]['special_instruct']		= $carsdat[$i]->special_instruct;
			//$this->dat['data'][$i]['status'] 					= $carsdat[$i]->status;
			$this->dat['data'][$i]['rr'] 						= $emph.$this->mricf->qry("ichange_rr",$carsdat[$i]->rr,"id","report_mark").$emph_end;
			$this->dat['data'][$i]['used_cntr']				= $emph.$cui[0]->used_cntr.$emph_end;
			//$this->dat['data'][$i]['bad_order'] 				= $carsdat[$i]->bad_order;
			//$this->dat['data'][$i]['bad_desc'] 				= $carsdat[$i]->bad_desc;
			//$this->dat['data'][$i]['location'] 				= $carsdat[$i]->location;
			//$this->dat['data'][$i]['lading'] 					= $carsdat[$i]->lading;
			$this->dat['data'][$i]['modified']					= "";
			if($carsdat[$i]->added > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$carsdat[$i]->added);}
			if($carsdat[$i]->modified > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$carsdat[$i]->modified);}
		}

		$this->search_build();

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		$this->load->view('list', $this->dat);
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
			$this->dat['data'] = (array)$this->Cars_model->get_single($id);
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
		$qry = $this->db->query("SELECT * FROM `ichange_cars` WHERE `rr` = '".$this->arr['rr_sess']."'");
		$delimiter = ",";
		$newline = "\r\n";
		force_download('cars.csv', $this->dbutil->csv_from_result($qry, $delimiter, $newline));

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
		$this->dat['hidden'] = array('tbl' => 'cars', 'id' => @$this->dat['data'][0]->id);
		$this->dat['form_url'] = "../save";
		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Car Number', 'def' => array(
              'name'        => 'car_num',
              'id'          => 'car_num',
              'value'       => @$this->dat['data'][0]->car_num,
              'maxlength'   => '50',
              'size'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'AAR Type', 'name' => 'aar_type', 'value' => @$this->dat['data'][0]->aar_type, 
			'other' => 'id="aar_type"', 'options' => $aar_opts
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

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Special Instructions', 'def' => array(
              'name'        => 'special_instruct',
              'id'          => 'special_instruct',
              'value'       => @$this->dat['data'][0]->special_instruct,
              'rows'			 => '5',
              'cols'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Owner RR', 'name' => 'rr', 'value' => @$this->dat['data'][0]->rr, 
			'other' => 'id="rr"', 'options' => $rr_opts
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Bad Order', 'name' => 'bad_order', 'value' => @$this->dat['data'][0]->bad_order, 
			'other' => 'id="bad_order"', 'options' => array('0' => 'No', '1' => 'Yes')
		);

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Bad Order Description', 'def' => array(
              'name'        => 'bad_desc',
              'id'          => 'bad_desc',
              'value'       => @$this->dat['data'][0]->bad_desc,
              'rows'			 => '5',
              'cols'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Location', 'def' => array(
              'name'        => 'location',
              'id'          => 'location',
              'value'       => @$this->dat['data'][0]->location,
              'maxlength'   => '50',
              'size'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Lading', 'def' => array(
              'name'        => 'lading',
              'id'          => 'lading',
              'value'       => @$this->dat['data'][0]->lading,
              'maxlength'   => '50',
              'size'        => '50'
			)
		);
	}

	function search_build(){		
		// Builds html for railroad listing.
		$this->dat['shtml'] = "<div class=\"box1\" style=\"left: 120px;\">";
		if(isset($_POST['search_for'])){$this->dat['shtml'] .= anchor("../cars","My Cars");}
		else{
			$this->dat['shtml'] .= "&nbsp;<a href=\"#\" id=\"search_expand\"><strong>Search</strong></a>&nbsp;<a href=\"#\" id=\"search_shrink\">Shrink</a><br />";
			$this->dat['shtml'] .= "<div id=\"search\" style=\"display: none;\">";
			//echo "<pre>"; print_r($this->myTrains); echo "</pre>";
			//echo "<pre>"; print_r($this->wbs_all); echo "</pre>";
		
			$search_opts = array('car_num' => "Car Report Mark & Num", 'aar_type' => "AAR Code", 'desc' => "Description", 'location' => "Location");
			$this->dat['shtml'] .= form_open_multipart("../cars");
			$this->dat['shtml'] .= "For ".form_input('search_for')."<br />";
			$this->dat['shtml'] .= "In ".form_dropdown('search_in',$search_opts);
			$this->dat['shtml'] .= " ".form_submit('submit','Search');
			$this->dat['shtml'] .= form_close();

			$this->dat['shtml'] .= "</div>";
		}
		$this->dat['shtml'] .= "</div>";
	}

}
?>