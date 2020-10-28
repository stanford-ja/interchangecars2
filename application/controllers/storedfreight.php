<?php
class Storedfreight extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->library('mricf');
		
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Storedfreight_model','',TRUE); // Database connection! TRUE means connect to db.
		//$this->load->model('Railroad_model','',TRUE);
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Freight stored at Industries";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

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
		$this->Generic_model->change("DELETE FROM `ichange_indust_stored` WHERE `qty_cars` = 0");
		$stored = (array)$this->Storedfreight_model->get_all_nonzero($this->arr['rr_sess']);
		//$this->dat = array();
		$this->dat['fields'] 			= array('id', 'indust_name', 'qty_cars', 'commodity', 'availability', 'added');
		$this->dat['field_names'] 		= array("ID", "Stored at Industry", "Qty of Cars", "Commodity", "Availability", "Added");
		$this->dat['options']			= array(
				'Acquire' => WEB_ROOT.INDEX_PAGE."/storedfreight/acquire/",
				'Make Public' => WEB_ROOT.INDEX_PAGE."/storedfreight/makepublic/",
				'Make Private' => WEB_ROOT.INDEX_PAGE."/storedfreight/makeprivate/"
			); // Paths to options method, with trailling slash!
		
		//$rr_me = (array)$this->Railroad_model->get_single(@$this->arr['rr_sess']);
		for($i=0;$i<count($stored);$i++){
			//$rr_from = (array)$this->Railroad_model->get_single($stored[$i]->rr_id_from);
			//$rr_to = (array)$this->Railroad_model->get_single($stored[$i]->rr_id_to);
			//$rr_f_rm = ""; if(isset($rr_from[0]->report_mark)){$rr_f_rm = $rr_from[0]->report_mark;}
			//$rr_t_rm = ""; if(isset($rr_to[0]->report_mark)){$rr_t_rm = $rr_to[0]->report_mark;}
			//if(isset($rr_me[0]->report_mark)){
			//	if($rr_f_rm == $rr_me[0]->report_mark){$rr_f_rm = "<span style=\"background-color: yellow;\">&nbsp;".$rr_f_rm."&nbsp;</span>";}
			//	if($rr_t_rm == $rr_me[0]->report_mark){$rr_t_rm = "<span style=\"background-color: yellow;\">&nbsp;".$rr_t_rm."&nbsp;</span>";}
			//}
			$this->dat['data'][$i]['id'] 						= $stored[$i]->id;
			$this->dat['data'][$i]['indust_name'] 	= $stored[$i]->indust_name;
			$this->dat['data'][$i]['qty_cars'] 		= $stored[$i]->qty_cars;
			$this->dat['data'][$i]['commodity'] 					= $stored[$i]->commodity;
			$this->dat['data'][$i]['availability']	= ($stored[$i]->availability == $this->arr['rr_sess'] ? "This RR" : "All RRs");
			$this->dat['data'][$i]['added'] 					= date('Y-m-d H:i', $stored[$i]->added);
		}

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		if($this->arr['rr_sess'] > 0){
			//$this->load->view('list', $this->dat);
			$this->load->view('table', $this->dat);
		}else{
			$this->load->view('not_allowed');
		}
		//$this->load->view('list', $this->dat);
		$this->load->view('footer');
	}

	public function acquire($id=0){
		// Acquire some stored freight and create waybill stream
		$this->dat['attribs'] = array('name' => "form"); // Attribs for form tag
		$this->load->helper('form');
		$this->dat['fields'] = array();
		$this->dat['field_names'] = array();
		$this->arr['pgTitle'] .= " - Acquire";
		$this->dat['data'] = (array)$this->Storedfreight_model->get_single($id);

		$this->setFieldSpecs();
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
		if($this->arr['rr_sess'] > 0){
			$this->load->view('edit', $this->dat);
		}else{
			$this->load->view('not_allowed');
		}
		$this->load->view('footer');
	}
	
	public function acquire2(){
		// Create waybill for selected acquisition
		//echo "<h2>NOT YET FINISHED!</h2>";
		$p = $_POST;
		$dat = (array)$this->Storedfreight_model->get_single($p['id']);
		$qty = intval($dat[0]->qty_cars-$p['qty_cars']);
		if($p['qty_cars'] > $dat[0]->qty_cars){ $qty = $dat[0]->qty_cars; }

		// Update qty_cars for ichange_indust_stored record
		$arr = array(
			'id'=>$p['id'],
			'qty_cars'=>$qty
		);
		//$this->Storedfreight_model->updateQtyCars($arr);
		$this->Generic_model->change("UPDATE `ichange_indust_stored` SET `qty_cars` = '".$qty."' WHERE `id` = '".$p['id']."'");
		
		// Create waybill
		$wbnum = date('YmdHis')."-A".$this->arr['rr_sess'];
		$sql = "INSERT INTO `ichange_waybill` SET 
			`waybill_num` = '".$wbnum."', 
			`lading` = '".$dat[0]->commodity."', 
			`status` = 'WAYBILL', 
			`rr_id_to` = '".$this->arr['rr_sess']."', 
			`rr_id_from` = '".$this->mricf->qry("ichange_indust",$dat[0]->indust_name,"indust_name","rr")."', 
			`rr_id_handling` = '".$this->mricf->qry("ichange_indust",$dat[0]->indust_name,"indust_name","rr")."', 
			`indust_origin_name` = '".$dat[0]->indust_name."', 
			`notes` = 'REQUIRES *".$p['qty_cars']."* CARS. CREATED FROM STORED FREIGHT', 
			`date` = '".date('Y-m-d')."'";

		$this->Generic_model->change($sql);
		$wb = (array)$this->Generic_model->qry("SELECT `id` FROM `ichange_waybill` WHERE `waybill_num` = '".$wbnum."'");
	
		//echo "<pre>"; print_r($dat); print_r($p); print_r($arr); print_r($wb); echo "</pre>";
		//echo $sql;
		header("Location:".WEB_ROOT.INDEX_PAGE."/waybill/edit/".$wb[0]->id);
	}

	function makepublic($id=0){
		$sql = "UPDATE `ichange_indust_stored` SET `availability` = '0' WHERE `id` = '".$id."'";
		$this->Generic_model->change($sql);
		header("Location:".WEB_ROOT.INDEX_PAGE."/storedfreight");
	}
	
	function makeprivate($id=0){
		$sql = "UPDATE `ichange_indust_stored` SET `availability` = '".$this->arr['rr_sess']."' WHERE `id` = '".$id."'";
		$this->Generic_model->change($sql);
		header("Location:".WEB_ROOT.INDEX_PAGE."/storedfreight");
	}
	
	public function setFieldSpecs(){
		// Sets specific field definitions for the controller being used.
		$this->dat['fields'] = array();
		
		// Add form and field definitions specific to this controller under this line... 
		$this->dat['hidden'] = array('tbl' => 'ichange_indust_stored', 'id' => @$this->dat['data'][0]->id);
		$this->dat['form_url'] = "../storedfreight/acquire2";


		$this->field_defs[] =  array(
			'type' => "statictext", 'label' => '', 
              'value'       => "From: ".@$this->dat['data'][0]->indust_name.", located at ".@$this->dat['data'][0]->town.
              	"<br />Commodity: ".@$this->dat['data'][0]->commodity
		);

		$car_opts = array();
		for($c=0;$c<=@$this->dat['data'][0]->qty_cars;$c++){ $car_opts[$c] = $c; }
		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Qty of Cars to Acquire', 'name' => 'qty_cars', 'value' => 0, 
			'other' => 'id="car_aar"', 'options' => $car_opts
		);

		/*
		$this->field_defs[] =  array(
			'type' => "statictext", 'label' => 'Origin Industry', 'def' => array(
              'name'        => 'indust_origin_name',
              'id'          => 'indust_origin_name',
              'value'       => @$this->dat['data'][0]->indust_name,
              'maxlength'   => '50',
              'size'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Destination Industry', 'def' => array(
              'name'        => 'indust_dest_name',
              'id'          => 'indust_dest_name',
              'value'       => @$this->dat['data'][0]->indust_dest_name,
              'maxlength'   => '50',
              'size'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Routing', 'def' => array(
              'name'        => 'routing',
              'id'          => 'routing',
              'value'       => @$this->dat['data'][0]->routing,
              'size'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Car AAR Type', 'name' => 'car_aar', 'value' => @$this->dat['data'][0]->car_aar, 
			'other' => 'id="car_aar"', 'options' => $aar_opts
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'RR From', 'name' => 'rr_id_from', 'value' => @$this->dat['data'][0]->rr_id_from, 
			'other' => 'id="rr_id_from"', 'options' => $rr_opts
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'RR To', 'name' => 'rr_id_to', 'value' => @$this->dat['data'][0]->rr_id_to, 
			'other' => 'id="rr_id_to"', 'options' => $rr_opts
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Lading', 'def' => array(
              'name'        => 'lading',
              'id'          => 'lading',
              'value'       => @$this->dat['data'][0]->lading,
              'maxlength'   => '60',
              'size'        => '60'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Regularity', 'def' => array(
              'name'        => 'regularity',
              'id'          => 'regularity',
              'value'       => @$this->dat['data'][0]->regularity,
              'size'        => '20'
			)
		);

		$this->field_defs[] =  array(
			'type' => "statictext", 'label' => '', 
              'value'       => "<div style=\"font-size: 9pt; background-color: yellow; border: 1px solid red; padding: 3px;\">Syntax for regular customer POs:<br />
              		empty = randomly generated,<br />
              		##-## = [day]-[month] waybill is generated,<br />
              		##-##/##/## - [day]-[2 digit months separated by /]</div>"
		);

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Notes', 'def' => array(
              'name'        => 'notes',
              'id'          => 'notes',
              'value'       => @$this->dat['data'][0]->notes,
              'rows'			 => '5',
              'cols'        => '50'
			)
		);
		*/
	}

}
?>
