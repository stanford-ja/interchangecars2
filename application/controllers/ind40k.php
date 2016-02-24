<?php
class Ind40k extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('mricf');
		
		$this->load->model('Ind40k_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - OpSig 40k Industries";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

	}

	public function index(){
		$this->pos = @$_POST;
		$this->lst();
	}
	
	public function lst(){
		$this->arr['pgTitle'] .= " - List";
		$randpos = array();
		$whr="LENGTH(`industry`) > 0";
		if(strlen(@$this->pos['city']) > 0){$whr .= " AND `city` LIKE '%".$this->pos['city']."%'";}
		if(strlen(@$this->pos['state']) > 0){$whr .= " AND `state` LIKE '%".$this->pos['state']."%'";}
		if(strlen(@$this->pos['commodity']) > 0){$whr .= " AND `commodity` LIKE '%".$this->pos['commodity']."%'";}
		if(strlen(@$this->pos['era']) > 0){$whr .= " AND `era` LIKE '%".$this->pos['era']."%'";}
		if(strlen(@$this->pos['stcc']) > 0){$whr .= " AND `stcc` LIKE '%".$this->pos['stcc']."%'";}
		$industdat = (array)$this->Ind40k_model->get_search($whr);
		//$this->dat = array();
		$this->dat['fields'] 			= array('id', 'era', 'industry', 'city', 'state', 'serving_rr', 'ships_receives', 'commodity', 'stcc');
		$this->dat['field_names'] 		= array("ID", "Era", "Industry", "City", "State", "RR","Ship/Rec", "Commodity", "STCC");
		$this->dat['options']			= array();
		/*
				'Edit' => "indust/edit/"
			); // Paths to options method, with trailling slash!
		*/
		$this->dat['links']				= array();
		/*
				'New' => "indust/edit/0"
			); // Paths for other links!
		*/
		
		for($i=0;$i<count($industdat);$i++){
			$this->dat['data'][$i]['id'] 						= $industdat[$i]->id;
			$this->dat['data'][$i]['era']			 	= $industdat[$i]->era;
			$this->dat['data'][$i]['industry'] 					= $industdat[$i]->industry;
			$this->dat['data'][$i]['city'] 						= $industdat[$i]->city;
			$this->dat['data'][$i]['state']				= $industdat[$i]->state;
			$this->dat['data'][$i]['serving_rr'] 			= $industdat[$i]->serving_rr;
			$this->dat['data'][$i]['ships_recives'] 			= $industdat[$i]->ships_receives;
			$this->dat['data'][$i]['commodity'] 			= $industdat[$i]->commodity;
			$this->dat['data'][$i]['stcc'] 			= $industdat[$i]->stcc;
		}

		$this->flddat = array('fields' => array());
		// Selector to move car to wherever.
		$move_to_opts = $this->mricf->rr_ichange_lst("",0,array('where' => "`id` = '".$this->arr['rr_sess']."'"));
		$this->flddat['fields'][] = "<div style=\"padding: 5px; background-color: moccasin; font-size: 10pt;\">	
	This is a search facility for the OpSig 40,000 industry database. 
	This listing includes industries from the lower 48 states, and Canadian provinces. 
	If you are a MRICC member and are logged in you can create an industry in the Industries table from an industry listed here by clicking the \"Create Industry\" button on the row of the industry you want to create. 
	Once an industry from this listing is created in the Industries table it can be used in the Waybill entry and maintenance parts of the application.<br><br>
	The OpSig Industry Database is available at <a href=\"http://www.opsig.org/reso/inddb/\" target=\"opsig_ind\">www.opsig.org/reso/inddb/</a>.
	</div>";
		$this->flddat['fields'][] = form_open_multipart('../ind40k');
		$this->flddat['fields'][] = "<span style=\"font-size: 10pt;\">City: </span>".form_input(array('name' => "city", 'size' => 15))."&nbsp;";
		$this->flddat['fields'][] = "<span style=\"font-size: 10pt;\">State: </span>".form_input(array('name' => "state", 'size' => 5))."&nbsp;";
		$this->flddat['fields'][] = "<span style=\"font-size: 10pt;\">Commodity: </span>".form_input(array('name' => "commodity", 'size' => 15))."<br />";
		$this->flddat['fields'][] = "<span style=\"font-size: 10pt;\">Era: </span>".form_input(array('name' => "era", 'size' => 10))."&nbsp;";
		$this->flddat['fields'][] = "<span style=\"font-size: 10pt;\">STCC: </span>".form_input(array('name' => "stcc", 'size' => 10))."&nbsp;";
		$this->flddat['fields'][] = form_submit('submit', 'Search');
		$this->flddat['fields'][] = form_close();

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		$this->load->view('fields', @$this->flddat);
		$this->load->view('list', $this->dat);
		$this->load->view('footer');
	}
	
	public function edit($id=0){
		// Used for editing existing (edit/[id]) and adding new (edit/0) records
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
		
		// Add form and field definitions specific to this controller under this line... 
		$this->dat['hidden'] = array('tbl' => 'indust', 'id' => @$this->dat['data'][0]->id);
		$this->dat['form_url'] = "../save";
		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Industry Name', 'def' => array(
              'name'        => 'indust_name',
              'id'          => 'indust_name',
              'value'       => @$this->dat['data'][0]->indust_name,
              'rows'			 => '3',
              'cols'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Description', 'def' => array(
              'name'        => 'desc',
              'id'          => 'desc',
              'value'       => @$this->dat['data'][0]->desc,
              'rows'			 => '3',
              'cols'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Serving RR', 'name' => 'rr', 'value' => @$this->dat['data'][0]->rr, 
			'other' => 'id="rr"', 'options' => $rr_opts
		);

		/*
		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'AAR Type', 'name' => 'aar_type', 'value' => @$this->dat['data'][0]->aar_type, 
			'other' => 'id="aar_type"', 'options' => $aar_opts
		);
		*/

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Freight In', 'def' => array(
              'name'        => 'freight_in',
              'id'          => 'freight_in',
              'value'       => @$this->dat['data'][0]->freight_in,
              'rows'			 => '3',
              'cols'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "statictext", 'label' => '<br />Freight Out Auto Generation',
			'value' => '<div style="border: 1px solid red; background-color: yellow; font-size: 9pt; padding: 5px;">To allow Generated Loads for a Commodity, the values in the Freight Out MUST be comma (,) separated and match exactly a semi-colon (;) separated value in the Commodities: Generates these Commods field.</div>'
		);

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Freight Out', 'def' => array(
              'name'        => 'freight_out',
              'id'          => 'freight_out',
              'value'       => @$this->dat['data'][0]->freight_out,
              'rows'			 => '3',
              'cols'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'RR Operation Info', 'def' => array(
              'name'        => 'op_info',
              'id'          => 'op_info',
              'value'       => @$this->dat['data'][0]->op_info,
              'rows'			 => '3',
              'cols'        => '50'
			)
		);

	}

}
?>