<?php
class Save extends CI_Controller {

	var $whr = "";
	
	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->library('mricf');
		
		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";

		// Security
		if(!$this->input->cookie('rr_sess')){echo "You are not logged in or the session variable has expired."; exit();}
		
		// Load generic model for custom queries
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
		
		// Railroad array set up
		$this->load->model('Railroad_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->arr['myRR'] = $this->Railroad_model->get_single($this->arr['rr_sess']);
		
		$arRR = (array)$this->Railroad_model->get_allActive();
		$this->arr['allRR'] = array();
		$this->arr['allRRKys'] = array();
		for($i=0;$i<count($arRR);$i++){
			$this->arr['allRR'][$arRR[$i]->id] = $arRR[$i]; // Used to get data for specific RR , id field is key for array.
			$this->arr['allRRKys'][] = $arRR[$i]->id; // Used to order by Report Mark.
		}
		//echo "<pre>"; print_r($this->arr['allRR']); echo "</pre>";
				
		// Cars data for railroad logged in as
		$this->load->model('Cars_model','',TRUE); // Database connection! TRUE means connect to db.
		$ca = (array)$this->Cars_model->getCars4RR($this->arr['rr_sess']);
		$cars_arr = array();
		for($i=0;$i<count($ca);$i++){
			$this->arr['myCars'][$ca[$i]->car_num] = $ca[$i];
		} 
		
		// Update last_act field in ichange_rr table for logged in rr.
		$this->last_act_update();
	}

	public function index(){
		$this->arr = $_POST;
		$this->message = "";
		$this->load->model('Generic_model','',TRUE);
		$this->qry_build();
		//echo $this->sql."<br />";
		$this->Generic_model->change($this->sql);
		if(isset($this->arr['filepath']) && isset($this->arr['save_as_filename'])){
			$this->img_upload(); 
		}
		//if(strlen($this->message) > 0){ 
			$this->session->set_flashdata('Message', $this->message); 
		//}
		header('Location:'.$this->arr['tbl']);
	}
	
	function qry_build(){
		// Builds SQL query string from $this->arr keys / values
		$this->sql = "";
		$ignore = array('tbl','id','submit', 'not_uppercase','userfile','filepath','save_as_filename','filemaxdim'); // 'not_uppercase', if exists, disables conversion of strings to UPPER CASE!
		if($this->arr['tbl'] == "rr" && strlen($this->arr['pw']) < 1){ unset($this->arr['pw']); }
		elseif($this->arr['tbl'] == "rr"){ $this->arr['pw'] = md5($this->arr['pw']); }
		$arr_kys = array_keys($this->arr);
		$i=0;
		$cntr = 0;
		//echo "<pre>"; print_r($this->arr); echo "</pre>"; exit();
		while($i<count($arr_kys)){
			if(!in_array($arr_kys[$i],$ignore) && strpos("z".$arr_kys[$i],"OpenLayers_Control") < 1){
				if($cntr > 0){$this->sql .= ", ";}
				if(is_array($this->arr[$arr_kys[$i]])){
					$val_tmp = implode("|",$this->arr[$arr_kys[$i]]);
				}else{
					$val_tmp = $this->arr[$arr_kys[$i]];
				}
				$val_tmp = $this->mricf->strip_spec($val_tmp);
				if(!isset($this->arr['not_uppercase'])){$val_tmp = strtoupper($val_tmp);}
				//$this->sql .= "`".$arr_kys[$i]."` = '".strtoupper($this->arr[$arr_kys[$i]])."'";
				$this->sql .= "`".$arr_kys[$i]."` = '".$val_tmp."'";
				$cntr++;
			}
			$i++;
		}
		if($this->arr['id'] > 0){$this->sql = "UPDATE `ichange_".$this->arr['tbl']."` SET ".$this->sql.", `modified` = '".date('U')."' WHERE `id` = '".$this->arr['id']."'";}
		else{$this->sql = "INSERT INTO `ichange_".$this->arr['tbl']."` SET ".$this->sql.", `added` = '".date('U')."'";}
	}

	function last_act_update(){
		$this->Generic_model->change("UPDATE `ichange_rr` SET `last_act` = '".date('U')."' WHERE `id` = '".@$_COOKIE['rr_sess']."'");		
	}
	
	function img_upload(){
		// Saves uploaded file selected in waybill() method.
		// Requires...
		// $this->arr['save_as_filename'] which the actual file name incl. extension (eg, picture.jpg)
		// $this->arr['filepath'] which is the path to save $this->arr['save_as_filename'] in (eg, /var/www/html/img/).
		// Optional...
		// $this->arr['filemaxdim'] which is the maximum length of the longest side in pixels (eg, 500).

		$this->uconfig = array();
		$this->uconfig['upload_path'] = DOC_ROOT.$this->arr['filepath'];
		$this->uconfig['allowed_types'] = 'jpg';
		$this->uconfig['file_name'] = $this->arr['save_as_filename'];
		$this->uconfig['overwrite'] = true;
		$this->uconfig['max_size']	= '512';
		//$this->uconfig['max_width'] = '350';
		//$this->uconfig['max_height'] = '350';
		$this->load->library('upload', $this->uconfig);
		//$this->upload->initialize($this->uconfig);

		if(!$this->upload->do_upload()){
			//echo "There was a problem uploading the file!<br /><a href=\"".WEB_ROOT."/graphics/waybill/".$p['id']."\">Try Again!</a><br />";
			//echo $this->upload->display_errors()."<br />";
			$this->message = "There was a problem uploading the file! ".$this->upload->display_errors();
		}else	{
			if(!isset($this->arr['filemaxdim'])){ $this->arr['filemaxdim'] = 500; }
			$ex = "convert ".DOC_ROOT.$this->arr['filepath'].$this->uconfig['file_name']." -resize ".$this->arr['filemaxdim']." ".DOC_ROOT.$this->arr['filepath'].$this->uconfig['file_name'];
			shell_exec($ex);
			$this->message = "Industry graphic <strong>".$this->uconfig['file_name']."</strong> uploaded successfully.";
		}
	}

}
?>