<?php
class Indust extends CI_Controller {
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
		
		$this->load->model('Indust_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Railroad_model', '', TRUE);
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Industries";
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
			$industdat = (array)$this->Generic_model->get_search_results($_POST['search_for'],$_POST['search_in'],"ichange_indust");
		}else{
			$industdat = (array)$this->Indust_model->get_all4RR_Sorted($this->arr['rr_sess']);
		}
		//$this->dat = array();
		$this->dat['fields'] 			= array('id', 'indust_name', 'town', 'desc', 'rr', 'freight_in', 'freight_out','modified');
		$this->dat['field_names'] 		= array("ID", "Name", "Town", "Desc", "RR", "Freight In", "Freight Out","Added/Modified");
		$this->dat['options']			= array(
				'Edit' => "indust/edit/"
			); // Paths to options method, with trailling slash!
		$this->dat['links']				= array(
				'New' => "indust/edit/0",
				'Export' => "indust/csv_export"
			); // Paths for other links!
		
		for($i=0;$i<count($industdat);$i++){
			$this->dat['data'][$i]['id'] 						= $industdat[$i]->id;
			$this->dat['data'][$i]['indust_name']			 	= $industdat[$i]->indust_name;
			$this->dat['data'][$i]['town']			 	= $industdat[$i]->town;
			$this->dat['data'][$i]['desc'] 					= "<div style=\"overflow: auto; max-height: 50px;\">".$industdat[$i]->desc."</div>";
			$this->dat['data'][$i]['rr'] 						= $this->mricf->qry("ichange_rr",$industdat[$i]->rr,"id","report_mark");
			$this->dat['data'][$i]['freight_in']				= "<div style=\"overflow: auto; max-height: 50px;\">".$industdat[$i]->freight_in."</div>";
			$this->dat['data'][$i]['freight_out'] 			= "<div style=\"overflow: auto; max-height: 50px;\">".$industdat[$i]->freight_out."</div>";
			$this->dat['data'][$i]['modified']					= "";
			if($industdat[$i]->added > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$industdat[$i]->added);}
			if($industdat[$i]->modified > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$industdat[$i]->modified);}
		}

		$this->rr_opts_build(10); // $this->mricf->rrOpts()
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
			$this->dat['data'] = (array)$this->Indust_model->get_single($id);
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

	function csv_export(){
		$this->load->dbutil();
		//$qry = $this->Generic_model->qry($this->sql);
		$qry = $this->db->query("SELECT * FROM `ichange_indust` WHERE `rr` = '".$this->arr['rr_sess']."'");
		$delimiter = ",";
		$newline = "\r\n";
		force_download('cars.csv', $this->dbutil->csv_from_result($qry, $delimiter, $newline));

	}
	
	public function setFieldSpecs(){
		// Sets specific field definitions for the controller being used.
		$this->dat['fields'] = array();
		
		// Add custom model calls / queries under this line...
		//$this->load->model('Aar_model', '', TRUE);
		
		// Add other code for fields under this line...
		/*
		$aar_opts = array();
		$aar_tmp = (array)$this->Aar_model->get_allSorted();
		for($i=0;$i<count($aar_tmp);$i++){$aar_opts[$aar_tmp[$i]->aar_code] = $aar_tmp[$i]->aar_code." - ".substr($aar_tmp[$i]->desc,0,70);}
		*/
		
		$this->rr_opts = array();
		$this->rr_opts_build();
		
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
			'type' => "input", 'label' => 'Town', 'def' => array(
              'name'        => 'town',
              'id'          => 'town',
              'value'       => @$this->dat['data'][0]->town,
              'maxlength'   => '50',
              'size'        => '45'
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
			'other' => 'id="rr"', 'options' => $this->rr_opts
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
			'type' => "select", 'label' => 'Allow Bulk Storage', 'name' => 'storage', 'value' => @$this->dat['data'][0]->storage, 
			'other' => 'id="storage"', 'options' => array(0 => "No", 1 => "Yes")
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

	function search_build(){		
		// Builds html for railroad listing.
		$this->dat['shtml'] = "<div class=\"box1\" style=\"left: 120px;\">";
		if(isset($_POST['search_for'])){$this->dat['shtml'] .= anchor("../indust","My Industries");}
		else{
			$this->dat['shtml'] .= "&nbsp;<a href=\"#\" id=\"search_expand\"><strong>Search</strong></a>&nbsp;<a href=\"#\" id=\"search_shrink\">Shrink</a><br />";
			$this->dat['shtml'] .= "<div id=\"search\" style=\"display: none;\">";
			//echo "<pre>"; print_r($this->myTrains); echo "</pre>";
			//echo "<pre>"; print_r($this->wbs_all); echo "</pre>";
		
			$search_opts = array('indust_name' => "Industry Name", 'freight_in' => "Freight In", 'freight_out' => "Freight Out");
			$this->dat['shtml'] .= form_open_multipart("../indust");
			$this->dat['shtml'] .= "For ".form_input('search_for')."<br />";
			$this->dat['shtml'] .= "In ".form_dropdown('search_in',$search_opts);
			$this->dat['shtml'] .= " ".form_submit('submit','Search');
			$this->dat['shtml'] .= form_close()."<hr /><strong>OR Railroad</strong><br />";
			$this->dat['shtml'] .= form_open_multipart("../indust");
			$this->dat['shtml'] .= "For ".form_dropdown('search_for',$this->rr_opts);
			$this->dat['shtml'] .= form_hidden('search_in',"rr");
			$this->dat['shtml'] .= " ".form_submit('submit','Search');
			$this->dat['shtml'] .= form_close();

			$this->dat['shtml'] .= "</div>";
		}
		$this->dat['shtml'] .= "</div>";
	}
	
	function rr_opts_build($flds=11){
		// $flds indicates which fields to include in label of dropdown - 11 = all, 10 = report mark, 1 rr name
		$rr_tmp = (array)$this->Railroad_model->get_allActive('report_mark',1);
		for($i=0;$i<count($rr_tmp);$i++){
			$rr_lab = "";
			if($flds == 1){$rr_lab = substr($rr_tmp[$i]->rr_name,0,70);}
			elseif($flds == 10){$rr_lab = $rr_tmp[$i]->report_mark;}
			elseif($flds == 11){$rr_lab = $rr_tmp[$i]->report_mark." - ".substr($rr_tmp[$i]->rr_name,0,70);}
			$this->rr_opts[$rr_tmp[$i]->id] = $rr_lab;
		}
	}

}
?>