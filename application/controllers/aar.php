<?php
class Aar extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!
	
	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->library('mricf');
		
		$this->load->model('Aar_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - AAR Codes";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

	}

	public function index(){
		$this->lst();
	}
	
	public function lst(){
		$this->arr['pgTitle'] .= " - List";
		$randpos = array();
		$arrdat = (array)$this->Aar_model->get_allSorted();
		//$this->dat = array();
		$this->dat['fields'] 			= array('id', 'aar_code', 'desc','modified');
		$this->dat['field_names'] 		= array("ID", "AAR Code", "Description","Added/Modified");
		$this->dat['options']			= array(
				'Edit' => "aar/edit/"
			); // Paths to options method, with trailling slash!
		$this->dat['links']				= array(
				'New' => "aar/edit/0"
			); // Paths for other links!
		
		for($i=0;$i<count($arrdat);$i++){
			$this->dat['data'][$i]['id'] 						= $arrdat[$i]->id;
			$this->dat['data'][$i]['aar_code']				 	= $arrdat[$i]->aar_code;
			$this->dat['data'][$i]['desc'] 				= $arrdat[$i]->desc;
			$this->dat['data'][$i]['modified']					= "";
			if($arrdat[$i]->added > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$arrdat[$i]->added);}
			if($arrdat[$i]->modified > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$arrdat[$i]->modified);}
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
			$this->dat['data'] = (array)$this->Aar_model->get_single($id);
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

}
?>