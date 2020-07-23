<?php
class Rr extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->library('mricf');
		$this->load->library('dates_times');
		
		$this->load->model('Railroad_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Railroad";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

	}

	public function index(){
		//$this->view($this->arr['rr_sess']);
		$this->lst();
	}

	public function lst(){
		$this->arr['pgTitle'] .= " - Railroad List";
		$randpos = array();
		if(isset($_POST['search_for'])){
			$rrdat = (array)$this->Generic_model->qry($_POST['search_for'],$_POST['search_in'],"ichange_rr");
		}else{
			$rrdat = (array)$this->Railroad_model->get_allActive('report_mark',1);
		}
		//echo "<pre>"; print_r($rrdat); echo "</pre>"; exit();
		//$this->dat = array();
		$this->dat['fields'] 			= array('id', 'rr_name', 'report_mark','owner_name', 'interchanges', 'modified');
		$this->dat['field_names'] 		= array("ID", "Name", "Report Mark", "Owner Name", "Interchanges", "Added/Modified");
		$this->dat['widths'] = array("5%","10%","20%","10%","35%","10%");
		$this->dat['options']			= array(
				'View' => "rr/view/"
			); // Paths to options method, with trailling slash!
		$this->dat['links']				= array(); // Paths for other links!
		$ic_div = "<div style=\"margin: 2px; padding: 4px; display: inline-block; background-color: antiquewhite;\">";
		
		for($i=0;$i<count($rrdat);$i++){
			$this->dat['data'][$i]['id'] 						= $rrdat[$i]->id;
			$this->dat['data'][$i]['rr_name']			 	= $rrdat[$i]->rr_name;
			$this->dat['data'][$i]['report_mark']			 	= $rrdat[$i]->report_mark;
			$this->dat['data'][$i]['owner_name']			 	= $rrdat[$i]->owner_name;
			$this->dat['data'][$i]['interchanges']			 	= $ic_div.str_replace(";","</div>".$ic_div,$rrdat[$i]->interchanges)."</div>";
			$this->dat['data'][$i]['modified'] = "";
			if($rrdat[$i]->added > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$rrdat[$i]->added);}
			if($rrdat[$i]->modified > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$rrdat[$i]->modified);}

			$tmp = $this->mricf->rrMap($rrdat[$i]->id);
			if(isset($tmp[0]) && strlen($tmp[0]) > 0){
				$this->dat['data'][$i]['rr_name'] .= "<br /><a href=\"javascript:{}\" onclick=\"window.open('".WEB_ROOT.$tmp[0]."','','width=500,height=500');\">System Map</a>";
			}
		}

		//$this->rr_opts_build(10); // $this->mricf->rrOpts()
		//$this->search_build();
		
		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		$this->load->view('list', $this->dat);
		$this->load->view('footer');
	}
	
	public function view($id=0){
		$this->arr['pgTitle'] .= " - View";
		$randpos = array();
		$rrdat = (array)$this->Railroad_model->get_single($id);
		$this->dat['field_names'] = array('ID',"Report Mark", "RR Name", "Description", "Owner Name", "Interchanges", "Affiliates", "Website", "Social Networks","Timezone");
		//$this->dat = array();
		$this->dat['options']			= array(
				'Edit' => "aar/edit/"
			); // Paths to options method, with trailling slash!
		$this->dat['links']				= array(
				'New' => "aar/edit/0"
			); // Paths for other links!
		
		for($i=0;$i<count($rrdat);$i++){
			$this->dat['data'][0]['id'] 					= $rrdat[$i]->id;
			$this->dat['data'][0]['report_mark']	 	= $rrdat[$i]->report_mark;
			$this->dat['data'][0]['rr_name'] 			= $rrdat[$i]->rr_name;
			$this->dat['data'][0]['rr_desc'] 			= html_entity_decode($rrdat[$i]->rr_desc);
			$this->dat['data'][0]['owner_name'] 		= $rrdat[$i]->owner_name;
			$this->dat['data'][0]['interchanges']		= str_replace(";","<br />",$rrdat[$i]->interchanges);
			$this->dat['data'][0]['affiliates'] 		= $rrdat[$i]->affiliates;
			$rrdat[$i]->website = str_replace("&#47;","/",$rrdat[$i]->website);
			$rrdat[$i]->social = str_replace("&#47;","/",$rrdat[$i]->social);
			$this->dat['data'][0]['website'] 			= auto_link($rrdat[$i]->website, 'url', TRUE);
			$this->dat['data'][0]['social'] 			= auto_link(str_replace(";","<br />",$rrdat[$i]->social), 'url', TRUE);
			$this->dat['data'][0]['tzone'] 			= $rrdat[$i]->tzone;

			$tmp = $this->mricf->rrMap($rrdat[$i]->id);
			if(isset($tmp[0]) && strlen($tmp[0]) > 0){
				$this->dat['data'][$i]['rr_name'] .= "<div style=\"float: right; display: inline-block; padding: 12px; background-color: ivory; border: 1px solid #999; border-radius: 10px;\">
					<h3>RR System Map</h3>";



					if(strpos($tmp[0],".pdf") > 0){
						$this->dat['data'][$i]['rr_name'] .= "RR's System Map is in a PDF file.<br /> 
							<a href=\"javascript:{}\" onclick=\"window.open('".WEB_ROOT.$tmp[0]."','','width=500,height=500');\">Click to view PDF</a>";
					}else{
						$this->dat['data'][$i]['rr_name'] .= "<a href=\"javascript:{}\" onclick=\"window.open('".WEB_ROOT.$tmp[0]."','','width=500,height=500');\">
							<img src=\"".WEB_ROOT.$tmp[0]."\" style=\"max-width: 700px; max-height: 700px;\" />
							</a>";
					}

				$this->dat['data'][$i]['rr_name'] .= "</div>";


			}

		}

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		$this->load->view('view', $this->dat);
		$this->load->view('footer');
	}
	
	public function edit($id=0){
		// Used for editing existing (edit/[id]) and adding new (edit/0) records
		if($this->arr['rr_sess'] != $id && $id > 0){header("Location:../../rr/view/".$id); exit();}
		$this->load->helper('form');
		$this->dat['attribs'] = array('name' => "form"); // Attribs for form tag
		$this->dat['fields'] = array();
		$this->dat['field_names'] = array();
		$this->dat['no_delete_form'] = 1;
		$this->arr['tinymce_flds'] = "rr_desc";
		if($id < 1){
			$this->arr['pgTitle'] .= " - New";
			$this->dat['data'] = (array)$this->Railroad_model->get_single($this->arr['rr_sess']);
			$this->dat['data'][0]->id = 0;
			unset($this->dat['data'][0]->report_mark);
			unset($this->dat['data'][0]->pw);
			unset($this->dat['data'][0]->admin_flag);
			unset($this->dat['data'][0]->rr_name);
			unset($this->dat['data'][0]->rr_desc);
			unset($this->dat['data'][0]->affiliates);
			unset($this->dat['data'][0]->interchanges);
			unset($this->dat['data'][0]->last_act);
			unset($this->dat['data'][0]->inactive);
			unset($this->dat['data'][0]->common_flag);
			unset($this->dat['data'][0]->quick_select);
			unset($this->dat['data'][0]->menu_type);
			unset($this->dat['data'][0]->home_disp);
			unset($this->dat['data'][0]->home_disp_v2);
			unset($this->dat['data'][0]->show_allocto_only);
			unset($this->dat['data'][0]->show_generated_loads);
			unset($this->dat['data'][0]->hide_auto);
			unset($this->dat['data'][0]->show_affil_wb);
			unset($this->dat['data'][0]->website);
			unset($this->dat['data'][0]->social);
			unset($this->dat['data'][0]->added);
			unset($this->dat['data'][0]->modified);
		}else{
			$this->arr['pgTitle'] .= " - Edit";
			$this->dat['data'] = (array)$this->Railroad_model->get_single($id);
		}
		
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
		//$this->load->model('Railroad_model', '', TRUE);
		
		// Add other code for fields under this line...
		/*
		$aar_opts = array();
		$aar_tmp = (array)$this->Aar_model->get_allSorted();
		*/
		$yn = array(0 => 'No', 1 => 'Yes');
		$home_disp = $this->mricf->homeDispType();
		
		$tz_opts = $this->dates_times->getTZArr();
				
		// Add form and field definitions specific to this controller under this line... 
		$this->dat['hidden'] = array('tbl' => 'rr', 'id' => @$this->dat['data'][0]->id, 'not_uppercase' => 1);
		if($this->dat['data'][0]->id < 1){$this->dat['hidden']['owner_name'] = @$this->dat['data'][0]->owner_name;}
		$this->dat['form_url'] = "../save";
		$this->dat['attribs'] = " onsubmit=\"return chckFrm();\"";
		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Report Mark', 'def' => array(
              'name'        => 'report_mark',
              'id'          => 'report_mark',
              'value'       => @str_replace("&#47;","/",$this->dat['data'][0]->report_mark),
              'maxlength'   => '8',
              'size'        => '8',
              'onchange'	=> 'this.value = this.value.toUpperCase();'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'RR Name', 'def' => array(
              'name'        => 'rr_name',
              'id'          => 'rr_name',
              'value'       => @str_replace("&#47;","/",$this->dat['data'][0]->rr_name),
              'maxlength'   => '60',
              'size'        => '60',
              'onchange'	=> 'this.value = this.value.toUpperCase();'
			)
		);

		if($this->dat['data'][0]->id < 1){
			$tmp = ""; if($this->dat['data'][0]->id < 1){$tmp = "<br />(You can edit the owner name by editing the railroad after you create it if you are creating the railroad for another user!)";}
			$this->field_defs[] =  array(
				'type' => "statictext", 'label' => "Owner Name", 'value' => @$this->dat['data'][0]->owner_name.$tmp
			);
		}else{
		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Owner Name', 'def' => array(
              'name'        => 'owner_name',
              'id'          => 'owner_name',
              'value'       => @$this->dat['data'][0]->owner_name,
              'maxlength'   => '60',
              'size'        => '60',
              'onchange'	=> 'this.value = this.value.toUpperCase();'
			)
		);
		}

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Description', 'def' => array(
             'name'        => 'rr_desc',
             'id'          => 'rr_desc',
             'value'       => @str_replace("&#47;","/",$this->dat['data'][0]->rr_desc),
             'rows'			 => '5',
             'cols'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Interchanges', 'def' => array(
             'name'        => 'interchanges',
             'id'          => 'interchanges',
             'value'       => @str_replace("&#47;","/",$this->dat['data'][0]->interchanges),
             'rows'			 => '3',
             'cols'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Affiliates', 'def' => array(
              'name'        => 'affiliates',
              'id'          => 'affiliates',
              'value'       => @str_replace("&#47;","/",$this->dat['data'][0]->affiliates),
              'maxlength'   => '30',
              'size'        => '30'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Website', 'def' => array(
              'name'        => 'website',
              'id'          => 'website',
              'value'       => @str_replace("&#47;","/",$this->dat['data'][0]->website),
              'maxlength'   => '60',
              'size'        => '60'
			)
		);

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Social Website Links', 'def' => array(
             'name'        => 'social',
             'id'          => 'social',
             'value'       => @str_replace("&#47;","/",$this->dat['data'][0]->social),
             'rows'			 => '3',
             'cols'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Password', 'def' => array(
              'name'        => 'pw',
              'id'          => 'pw',
              'value'       => '', /*@$this->dat['data'][0]->pw,*/
              'maxlength'   => '20',
              'size'        => '20'
			)
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Home Display', 'name' => 'home_disp', 'value' => @$this->dat['data'][0]->home_disp, 
			'other' => 'id="home_disp"', 'options' => $home_disp
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Show Allocated to Only', 'name' => 'show_allocto_only', 'value' => @$this->dat['data'][0]->show_allocto_only, 
			'other' => 'id="show_allocto_only"', 'options' => $yn
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Show Generated Loads', 'name' => 'show_generated_loads', 'value' => @$this->dat['data'][0]->show_generated_loads, 
			'other' => 'id="show_generated_loads"', 'options' => $yn
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Show Affiliates WBs', 'name' => 'show_affil_wb', 'value' => @$this->dat['data'][0]->show_affil_wb, 
			'other' => 'id="show_affil_wb"', 'options' => $yn
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Hide Waybills in Auto Trains', 'name' => 'hide_auto', 'value' => @$this->dat['data'][0]->hide_auto, 
			'other' => 'id="hide_auto"', 'options' => $yn
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Common Flag', 'name' => 'common_flag', 'value' => @$this->dat['data'][0]->common_flag, 
			'other' => 'id="common_flag"', 'options' => $yn
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Quick Select', 'name' => 'quick_select', 'value' => @$this->dat['data'][0]->quick_select, 
			'other' => 'id="quick_select"', 'options' => $yn
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Timezone', 'name' => 'tzone', 'value' => @str_replace("&#47;","/",$this->dat['data'][0]->tzone), 
			'other' => 'id="tzone"', 'options' => $tz_opts
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Use TZ Time', 'name' => 'use_tz_time', 'value' => @$this->dat['data'][0]->use_tz_time, 
			'other' => 'id="use_tz_time"', 'options' => $yn
		);

		if(@$this->dat['data'][0]->admin_flag == 1){
			$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Admin Flag', 'name' => 'admin_flag', 'value' => @$this->dat['data'][0]->admin_flag, 
			'other' => 'id="admin_flag"', 'options' => $yn
			);
		}

		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Custom Styles & Views', 'def' => array(
             'name'        => 'home_disp_v2',
             'id'          => 'home_disp_v2',
             'value'       => @$this->dat['data'][0]->home_disp_v2,
             'rows'			 => '3',
             'cols'        => '50'
			)
		);

		$this->field_defs[] =  array(
			'type' => "statictext", 'label' => '<strong>To edit the Custom Styles and Views click the link below</strong>',
			'value' => '<a href="javascript:{}" onclick="window.open(\''.WEB_ROOT.'/legacy/rr_stylesviews.php\',\'CustomStylesViews\',\'width=850,height=500,scrollbars=1\');">Edit Styles & Views</a>'
		);

		$this->field_defs[] =  array(
			'type' => "statictext", 'label' => '',
			'value' => '<hr /><strong>ONLY SET THE INACTIVE OPTION TO YES BELOW IF YOU NO LONGER WANT TO ACCESS THIS RAILROAD!<br />If you set the Inactive setting to Yes below then this railroad will cease to be available as a login option.</strong>'
		);

		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Inactive', 'name' => 'inactive', 'value' => @$this->dat['data'][0]->inactive, 
			'other' => 'id="inactive"', 'options' => $yn
		);

		$this->field_defs[] =  array(
			'type' => "statictext", 'label' => '',
			'value' => '<hr />'
		);

		/*
		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'Owner RR', 'name' => 'rr', 'value' => @$this->dat['data'][0]->rr, 
			'other' => 'id="rr"', 'options' => $rr_opts
		);
		*/

		/*
		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Password', 'def' => array(
             'name'        => 'pw',
             'id'          => 'pw',
             'value'       => @$this->dat['data'][0]->pw,
             'rows'			 => '5',
             'cols'        => '50'
			)
		);
		*/
	}

}
?>
