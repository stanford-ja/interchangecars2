<?php
class Randomwb extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->library('mricf');
		
		$this->load->model('Randomwb_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Railroad_model','',TRUE); 
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Customer POs";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

	}

	public function index(){
		$this->lst();
	}
	
	public function lst(){
		$this->arr['pgTitle'] .= " - List";
		$randpos = array();
		$custpos = (array)$this->Randomwb_model->get_all();
		//$this->dat = array();
		$this->dat['fields'] 			= array('id', 'indust_origin_name', 'indust_dest_name', 'lading', 'regularity', 'rr_id_from', 'rr_id_to','create_as','modified');
		$this->dat['field_names'] 		= array("ID", "Origin Industry", "Destination Industry", "Lading", "Regularity","From RR","To RR","Create As","Added/Modified");
		$this->dat['options']			= array(
				'Edit' => "randomwb/edit/"
			); // Paths to options method, with trailling slash!
		$this->dat['links']				= array(
				'New' => "randomwb/edit/0"
			); // Paths for other links!
		
		$rr_me = (array)$this->Railroad_model->get_single(@$this->arr['rr_sess']);
		for($i=0;$i<count($custpos);$i++){
			$rr_from = (array)$this->Railroad_model->get_single($custpos[$i]->rr_id_from);
			$rr_to = (array)$this->Railroad_model->get_single($custpos[$i]->rr_id_to);
			$rr_f_rm = ""; if(isset($rr_from[0]->report_mark)){$rr_f_rm = $rr_from[0]->report_mark;}
			$rr_t_rm = ""; if(isset($rr_to[0]->report_mark)){$rr_t_rm = $rr_to[0]->report_mark;}
			if(isset($rr_me[0]->report_mark)){
				if($rr_f_rm == $rr_me[0]->report_mark){$rr_f_rm = "<span style=\"background-color: yellow;\">&nbsp;".$rr_f_rm."&nbsp;</span>";}
				if($rr_t_rm == $rr_me[0]->report_mark){$rr_t_rm = "<span style=\"background-color: yellow;\">&nbsp;".$rr_t_rm."&nbsp;</span>";}
			}
			$regla = $custpos[$i]->regularity;
			if(strlen($regla) < 1){$regla = "RANDOM";}
			$this->dat['data'][$i]['id'] 						= $custpos[$i]->id;
			$this->dat['data'][$i]['indust_origin_name'] 	= $custpos[$i]->indust_origin_name;
			$this->dat['data'][$i]['indust_dest_name'] 		= $custpos[$i]->indust_dest_name;
			$this->dat['data'][$i]['lading'] 					= $custpos[$i]->lading;
			$this->dat['data'][$i]['rr_id_from'] 					= $rr_f_rm."&nbsp;";
			$this->dat['data'][$i]['rr_id_to'] 					= $rr_t_rm."&nbsp;";
			$this->dat['data'][$i]['regularity']				= $regla;
			$this->dat['data'][$i]['create_as']				= $custpos[$i]->create_as;
			$this->dat['data'][$i]['modified']					= "";
			if($custpos[$i]->added > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$custpos[$i]->added);}
			if($custpos[$i]->modified > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$custpos[$i]->modified);}
		}

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		$this->load->view('list', $this->dat);
		$this->load->view('footer');
	}
	
	public function edit($id=0){
		// Used for editing existing (edit/[id]) and adding new (edit/0) records
		$this->dat['attribs'] = array('name' => "form"); // Attribs for form tag
		$this->load->helper('form');
		$this->dat['fields'] = array();
		$this->dat['field_names'] = array();
		if($id < 1){
			$this->arr['pgTitle'] .= " - New";
			$this->dat['data'][0] = array('id' => 0);
		}else{
			$this->arr['pgTitle'] .= " - Edit";
			$this->dat['data'] = (array)$this->Randomwb_model->get_single($id);
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
		for($i=0;$i<count($aar_tmp);$i++){$aar_opts[$aar_tmp[$i]->aar_code] = $aar_tmp[$i]->aar_code." - ".substr($aar_tmp[$i]->desc,0,70);}
		
		$rr_opts = array();
		$rr_tmp = (array)$this->Railroad_model->get_allActive();
		for($i=0;$i<count($rr_tmp);$i++){$rr_opts[$rr_tmp[$i]->id] = $rr_tmp[$i]->report_mark." - ".substr($rr_tmp[$i]->rr_name,0,70);}
		
		// Add form and field definitions specific to this controller under this line... 
		$this->dat['hidden'] = array('tbl' => 'randomwb', 'id' => @$this->dat['data'][0]->id);
		$this->dat['form_url'] = "../save";

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Create As', 'name' => 'create_as', 'value' => @$this->dat['data'][0]->create_as, 
			'other' => 'id="create_as"', 'options' => array('P_ORDER' => "Purchase Order", 'WAYBILL' => "Waybill")
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Origin Industry', 'def' => array(
              'name'        => 'indust_origin_name',
              'id'          => 'indust_origin_name',
              'value'       => @$this->dat['data'][0]->indust_origin_name,
              'maxlength'   => '50',
              'size'        => '50',
              'onKeyUp'		=> "industAutoComp(this.value,'ichange_indust','indust_origin_name','indust_origin_name',1)",
              'onfocus'		=> "showEle('indust_origin_name_span');",
              'onblur'		=> "hideEle('orig_ind_info');"
			)
		);

		$this->field_defs[] =  array(
			'type' => "statictext", 'label' => '', 
              'value'       => "<div id=\"indust_origin_name_span\" style=\"display: none; border: 1px solid black; background-color: yellow; font-size: 9pt; padding: 5px; max-height: 100px; overflow: auto;\"></div>
											<div id=\"indust_origin_name_indDescDiv\" style=\"display: none;\"></div>"
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Destination Industry', 'def' => array(
              'name'        => 'indust_dest_name',
              'id'          => 'indust_dest_name',
              'value'       => @$this->dat['data'][0]->indust_dest_name,
              'maxlength'   => '50',
              'size'        => '50',
              'onKeyUp'		=> "industAutoComp(this.value,'ichange_indust','indust_dest_name','indust_dest_name',1)",
              'onfocus'		=> "showEle('indust_dest_name_span');",
              'onblur'		=> "hideEle('dest_ind_info');"
			)
		);

		$this->field_defs[] =  array(
			'type' => "statictext", 'label' => '', 
              'value'       => "<div id=\"indust_dest_name_span\" style=\"display: none; border: 1px solid black; background-color: yellow; font-size: 9pt; padding: 5px; max-height: 100px; overflow: auto;\"></div>
											<div id=\"indust_dest_name_indDescDiv\" style=\"display: none;\"></div>"
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
              'value'       => @str_replace("&#47;","/",$this->dat['data'][0]->regularity),
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
	}

}
?>
