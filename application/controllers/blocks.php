<?php
class Blocks extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->library('mricf');
		
		$this->load->model('Blocks_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Blocks";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

	}

	public function index(){
		$this->lst();
	}
	
	public function lst(){
		$this->arr['pgTitle'] .= " - List";
		$randpos = array();
		$blockdat = (array)$this->Blocks_model->get_all4RRSorted($this->arr['rr_sess']);
		//$this->dat = array();
		$this->dat['fields'] 			= array('id', 'block_id', 'block_name', 'occupied_by','restricts');
		$this->dat['field_names'] 		= array("ID", "Block ID", "Block Name", "Occupied By", "Restrictions / Other Info");
		$this->dat['options']			= array(
				'Edit' => "blocks/edit/",
				'Allocate to Train' => "blocks/allocate/"
			); // Paths to options method, with trailling slash!
		$this->dat['links']				= array(
				'New' => "blocks/edit/0"
			); // Paths for other links!
		
		for($i=0;$i<count($blockdat);$i++){
			$this->dat['data'][$i]['id'] 							= $blockdat[$i]->id;
			$this->dat['data'][$i]['block_id']					 	= $blockdat[$i]->block_id;
			$this->dat['data'][$i]['block_name'] 					= $blockdat[$i]->block_name;
			$this->dat['data'][$i]['occupied_by'] 				= $blockdat[$i]->occupied_by;
			$this->dat['data'][$i]['restricts'] 					= $blockdat[$i]->restricts;
		}

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		$this->load->view('list_blocks', $this->dat);
		//$this->load->view('list', $this->dat);
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
			$this->dat['data'][0]->rr_id = $this->arr['rr_sess'];
		}else{
			$this->arr['pgTitle'] .= " - Edit";
			$this->dat['data'] = (array)$this->Blocks_model->get_single($id);
		}
		$this->setFieldSpecs(); // Set field specs
		$this->doIt();
	}

	public function allocate($id=0){
		// Used for editing existing (edit/[id]) and adding new (edit/0) records
		$this->load->helper('form');
		$this->dat['attribs'] = array('name' => "form"); // Attribs for form tag
		$this->dat['fields'] = array();
		$this->dat['field_names'] = array();
		if($id < 1){
			// Do nothing!
		}else{
			$this->arr['pgTitle'] .= " - Allocate to Train";
			$this->dat['data'] = (array)$this->Blocks_model->get_single($id);
		}
		$this->setAllocateFieldSpecs(); // Set field specs
		$this->doIt();
	}
		
	public function doIt(){
		//echo "<pre>"; print_r($this->dat['data']); echo "</pre>";
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
	
	function clear_occupied($id=0){
		// id is id of block to clear occupied for
		$arr = array('id' => $id, 'occupied_by' => "");
		$this->Blocks_model->update_occupied($arr);
		header("Location:../../blocks/");
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
		$this->dat['hidden'] = array('tbl' => 'blocks', 'id' => @$this->dat['data'][0]->id, 'rr_id' => $this->arr['rr_sess']);
		$this->dat['form_url'] = "../save";
		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Block ID', 'def' => array(
              'name'        => 'block_id',
              'id'          => 'block_id',
              'value'       => @$this->dat['data'][0]->block_id,
              'maxlength'   => '25',
              'size'        => '25'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Block Name', 'def' => array(
              'name'        => 'block_name',
              'id'          => 'block_name',
              'value'       => @$this->dat['data'][0]->block_name,
              'maxlength'   => '50',
              'size'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Description', 'def' => array(
              'name'        => 'block_desc',
              'id'          => 'block_desc',
              'value'       => @$this->dat['data'][0]->block_desc,
              'rows'			 => '3',
              'cols'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Restrictions / Other Info', 'def' => array(
              'name'        => 'restricts',
              'id'          => 'restricts',
              'value'       => @$this->dat['data'][0]->restricts,
              'rows'			 => '3',
              'cols'        => '50'
			)
		);

		/*
		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Real Location', 'def' => array(
              'name'        => 'real_location',
              'id'          => 'real_location',
              'value'       => @$this->dat['data'][0]->real_location,
              'maxlength'   => '60',
              'size'        => '60'
			)
		);
		*/

		/*
		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Real Location', 'def' => array(
              'name'        => 'real_location',
              'id'          => 'real_location',
              'value'       => @$this->dat['data'][0]->real_location,
              'rows'			 => '3',
              'cols'        => '50'
			)
		);
		*/

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
	
	function setAllocateFieldSpecs(){
		// Trains Options for train selector in Options column 
		// Add custom model calls / queries under this line...
		$this->load->model('Train_model', '', TRUE);
		//$this->load->model('Railroad_model', '', TRUE);
		
		// Add other code for fields under this line...
		$tr_opts = array('' => "");
		$tr_tmp = (array)$this->Train_model->get_all4RR_Sorted($this->arr['rr_sess']);
		for($i=0;$i<count($tr_tmp);$i++){$tr_opts[$tr_tmp[$i]->train_id] = $tr_tmp[$i]->train_id;}

		$this->dat['hidden'] = array('tbl' => 'blocks', 'id' => @$this->dat['data'][0]->id, 'rr_id' => $this->arr['rr_sess']);
		$this->dat['form_url'] = "../save";

		$this->field_defs[] =  array(
			'type' => "statictext", 'label' => '<br />Block Details',
			'value' => '<div style="border: 1px solid red; background-color: yellow; font-size: 9pt; padding: 5px;">
				'.@$this->dat['data'][0]->block_name.'&nbsp;'.@$this->dat['data'][0]->block_desc.'&nbsp;'.@$this->dat['data'][0]->restricts.'
				</div>'
		);

		/*
		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Block ID', 'def' => array(
              'name'        => 'block_id',
              'id'          => 'block_id',
              'value'       => @$this->dat['data'][0]->block_id,
              'maxlength'   => '25',
              'size'        => '25'
			)
		);
		*/

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Train ID', 'name' => 'occupied_by', 'value' => "", 
			'other' => 'id="occupied_by"', 'options' => $tr_opts
		);

	}

}
?>