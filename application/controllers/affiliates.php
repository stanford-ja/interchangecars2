<?php
class Affiliates extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->library('mricf');
		$this->load->helper("form");
		
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Locations";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

	}

	public function index(){
		$this->lst();
	}
	
	public function mv(){
		// Move affiliate industries, trains, etc from an affiliated railroad to railroad currently logged in as.
		$this->arr['pgTitle'] .= " - List";
		if($this->arr['rr_sess'] > 0){
			$this->rr = (array)$this->Generic_model->qry("SELECT `owner_name`,`id` FROM `ichange_rr` WHERE `id` LIKE '".$this->arr['rr_sess']."'");
			$this->dat['affils'] = (array)$this->Generic_model->qry("SELECT `report_mark`,`id` FROM `ichange_rr` WHERE `owner_name` LIKE '".$this->rr[0]->owner_name."' AND `id` != '".$this->arr['rr_sess']."'");
			for($r=0;$r<count($this->dat['affils']);$r++){
				$this->dat['ichange_indust'][] = (array)$this->Generic_model->qry("SELECT * FROM `ichange_indust` WHERE `rr` = '".$this->dat['affils'][$r]->id."'");
				$this->dat['ichange_cars'][] = (array)$this->Generic_model->qry("SELECT * FROM `ichange_cars` WHERE `rr` = '".$this->dat['affils'][$r]->id."'");
				$this->dat['ichange_locos'][] = (array)$this->Generic_model->qry("SELECT * FROM `ichange_locos` WHERE `rr` = '".$this->dat['affils'][$r]->id."'");
				$this->dat['ichange_trains'][] = (array)$this->Generic_model->qry("SELECT * FROM `ichange_trains` WHERE `railroad_id` = '".$this->dat['affils'][$r]->id."'");
			}
		} 
		
		//echo "<pre>"; print_r($this->dat); echo "</pre>"; //exit();

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		if($this->arr['rr_sess'] > 0){$this->load->view('affiliate_move', $this->dat);}
		else{
			$this->load->view('not_allowed');
		}
		$this->load->view('footer');
	}
	
	function savemv(){
		// Moves checked items to currently
		if($this->arr['rr_sess'] > 0){ /* DONT DO ANYTHING IF COOKIE HAS GONE GOODBYE */
			$p = $_POST;
			
			if(!isset($p['ichange_indust'])){$p['ichange_indust'] = array();}
			for($i=0;$i<count($p['ichange_indust']);$i++){
				$sql = "UPDATE `ichange_indust` SET `rr` = '".$this->arr['rr_sess']."', `modified` = '".date('U')."' WHERE `id` = '".$p['ichange_indust'][$i]."'";
				$this->Generic_model->change($sql);
			}

			if(!isset($p['ichange_cars'])){$p['ichange_cars'] = array();}
			for($i=0;$i<count($p['ichange_cars']);$i++){
				$sql = "UPDATE `ichange_cars` SET `rr` = '".$this->arr['rr_sess']."', `modified` = '".date('U')."' WHERE `id` = '".$p['ichange_cars'][$i]."'";
				$this->Generic_model->change($sql);
			}

			if(!isset($p['ichange_locos'])){$p['ichange_locos'] = array();}
			for($i=0;$i<count($p['ichange_locos']);$i++){
				$sql = "UPDATE `ichange_locos` SET `rr` = '".$this->arr['rr_sess']."', `modified` = '".date('U')."' WHERE `id` = '".$p['ichange_locos'][$i]."'";
				$this->Generic_model->change($sql);
			}

			if(!isset($p['ichange_trains'])){$p['ichange_trains'] = array();}
			for($i=0;$i<count($p['ichange_trains']);$i++){
				$sql = "UPDATE `ichange_trains` SET `railroad_id` = '".$this->arr['rr_sess']."', `modified` = '".date('U')."' WHERE `id` = '".$p['ichange_trains'][$i]."'";
				$this->Generic_model->change($sql);
			}
		}
		
		header("Location:".WEB_ROOT."/affiliates/mv");
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
		$this->dat['hidden'] = array('tbl' => 'locations', 'id' => @$this->dat['data'][0]->id);
		$this->dat['form_url'] = "../save";
		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Fictional Location', 'def' => array(
              'name'        => 'fictional_location',
              'id'          => 'fictional_location',
              'value'       => @$this->dat['data'][0]->fictional_location,
              'maxlength'   => '60',
              'size'        => '60'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Real Location', 'def' => array(
              'name'        => 'real_location',
              'id'          => 'real_location',
              'value'       => @$this->dat['data'][0]->real_location,
              'maxlength'   => '60',
              'size'        => '60'
			)
		);

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

}
?>