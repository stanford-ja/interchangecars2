<?php
class Commod extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->library('mricf');
		
		$this->load->model('Commod_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Commodities";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

	}

	public function index(){
		$this->lst();
	}
	
	public function lst(){
		$this->arr['pgTitle'] .= " - List";
		$randpos = array();
		$commodat = (array)$this->Commod_model->get_allSorted();
		//$this->dat = array();
		$this->dat['fields'] 			= array('id', 'commod_name', 'aar_types', 'generates','modified');
		$this->dat['field_names'] 		= array("ID", "Commodity", "AAR Car Types", "Generates These Commodities","Added/Modified");
		$this->dat['options']			= array(
				'Edit' => "commod/edit/"
			); // Paths to options method, with trailling slash!
		$this->dat['links']				= array(
				'New' => "commod/edit/0"
			); // Paths for other links!
		
		for($i=0;$i<count($commodat);$i++){
			$this->dat['data'][$i]['id'] 						= $commodat[$i]->id;
			$this->dat['data'][$i]['commod_name']			 	= $commodat[$i]->commod_name;
			$this->dat['data'][$i]['aar_types']			 	= str_replace(";","; ",$commodat[$i]->aar_types);
			$this->dat['data'][$i]['generates'] 				= str_replace(";","; ",$commodat[$i]->generates);
			$this->dat['data'][$i]['modified']					= "";
			if($commodat[$i]->added > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$commodat[$i]->added);}
			if($commodat[$i]->modified > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$commodat[$i]->modified);}
		}

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
			$this->dat['data'][0] = array('id' => 0);
		}else{
			$this->arr['pgTitle'] .= " - Edit";
			$this->dat['data'] = (array)$this->Commod_model->get_single($id);
		}
		
		//echo "<pre>"; print_r($this->dat['data']); echo "</pre>";
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
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		if($this->arr['rr_sess'] > 0){$this->load->view('edit', $this->dat);}
		else{
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
		
		/*
		$rr_opts = array();
		$rr_tmp = (array)$this->Railroad_model->get_allActive();
		for($i=0;$i<count($rr_tmp);$i++){$rr_opts[$rr_tmp[$i]->id] = $rr_tmp[$i]->report_mark." - ".substr($rr_tmp[$i]->rr_name,0,70);}
		*/
		
		// Add form and field definitions specific to this controller under this line... 
		$this->dat['hidden'] = array('tbl' => 'commod', 'id' => @$this->dat['data'][0]->id);
		$this->dat['form_url'] = "../save";
		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Commodity', 'def' => array(
              'name'        => 'commod_name',
              'id'          => 'commod_name',
              'value'       => @$this->dat['data'][0]->commod_name,
              'maxlength'   => '45',
              'size'        => '45'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'AAR Car Types', 'def' => array(
              'name'        => 'aar_types',
              'id'          => 'aar_types',
              'value'       => @$this->dat['data'][0]->aar_types,
              'maxlength'   => '30',
              'size'        => '30'
			)
		);

		$this->field_defs[] =  array(
			'type' => "statictext", 'label' => '',
			'value' => '<strong>AAR Car Types:</strong><br /><i>Enter 1 or more AAR Car Type Code separated by semi-colons (eg, XM;LO;HT).</i><br />'
		);

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Generates These Commodities', 'def' => array(
              'name'        => 'generates',
              'id'          => 'generates',
              'value'       => @$this->dat['data'][0]->generates,
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
			'type' => "statictext", 'label' => '<br />Freight Out Auto Generation',
			'value' => '<div style="border: 1px solid red; background-color: yellow; font-size: 9pt; padding: 5px;">To allow Generated Loads for a Commodity, the values in the Freight Out MUST be comma (,) separated and match exactly a semi-colon (;) separated value in the Commodities: Generates these Commods field.</div>'
		);
		*/

	}

}
?>