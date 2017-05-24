<?php
class Login extends CI_Controller {

	var $rr_sess = 0;
	
	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('mricf');
		
		$this->load->model('Generic_model','',TRUE);
		
		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Waybill";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";

		// Railroad array set up
		$this->load->model('Railroad_model','',TRUE); // Database connection! TRUE means connect to db.
		
		$arRR = (array)$this->Railroad_model->get_allActive();
		$this->arr['allRR'] = array();
		$this->arr['allRRKys'] = array();
		for($i=0;$i<count($arRR);$i++){
			$this->arr['allRR'][$arRR[$i]->id] = $arRR[$i]; // Used to get data for specific RR , id field is key for array.
			$this->arr['allRRKys'][] = $arRR[$i]->id; // Used to order by Report Mark.
		}
		//echo "<pre>"; print_r($this->arr['allRR']); echo "</pre>";
		//echo "<pre>"; print_r($this->arr['allRRKys']); echo "</pre>";
		
	}

	public function index(){
		$rr_opts = array();
		$rr_opts['Active'] = "-- A C T I V E --";
		for($i=0;$i<count($this->arr['allRRKys']);$i++){
			if($this->arr['allRR'][$this->arr['allRRKys'][$i]]->inactive != 1){
				$inact = ""; 
				$rr_opts[$this->arr['allRRKys'][$i]] = $this->arr['allRR'][$this->arr['allRRKys'][$i]]->report_mark." (".$this->arr['allRR'][$this->arr['allRRKys'][$i]]->owner_name.$inact.")";
			}
		}
		$rr_opts['Inactive'] = "-- I N A C T I V E --";
		for($i=0;$i<count($this->arr['allRRKys']);$i++){
			if($this->arr['allRR'][$this->arr['allRRKys'][$i]]->inactive == 1){
				$inact = "-Inactive!";
				$rr_opts[$this->arr['allRRKys'][$i]] = $this->arr['allRR'][$this->arr['allRRKys'][$i]]->report_mark." (".$this->arr['allRR'][$this->arr['allRRKys'][$i]]->owner_name.$inact.")";
			}
		}
		$this->arr['rr_opts'] = $rr_opts;
		$this->load->view('header',$this->arr);
		$this->load->view('login', $this->arr);
		$this->load->view('footer');
	}
	
	public function chk(){
		//print_r($_POST);
		if(md5($_POST['p_word']) == $this->arr['allRR'][$_POST['rr_selected']]->pw){
			$this->input->set_cookie('rr_sess',$_POST['rr_selected'],86500);
			$this->input->set_cookie('_tz',$this->arr['allRR'][$_POST['rr_selected']]->tzone,86500);
			if(@$this->arr['allRR'][$_POST['rr_selected']]->admin_flag == 1){$this->input->set_cookie('_mricfadmin',1,86500);}
			$this->last_act_update($_POST['rr_selected']);
			$this->session->set_flashdata('loginSuccess', '1');
			header('Location:'.WEB_ROOT.'/index.php/home');
		}else{header('Location:'.WEB_ROOT.'/index.php/login');}
	}
	
	public function logout(){
		$this->input->set_cookie('rr_sess',0,0);
		$this->input->set_cookie('_tz',"",0);
		$this->input->set_cookie('_mricfadmin',"",0);
		header('Location:../home');
	}
	
	public function switch_to($id=0){
		if(!$this->input->cookie('rr_sess')){header("Location:../"); exit();}
		//$_COOKIE['rr_sess'] = $id;
		
		// START check that railroad being switched to has the same Owner Name as the one being switched from.
		if(strtoupper($this->arr['allRR'][$id]->owner_name) == strtoupper($this->arr['allRR'][$this->input->cookie('rr_sess')]->owner_name) || isset($_COOKIE['_mricfadmin'])){
			$this->input->set_cookie('rr_sess',$id,86500); 
			$this->input->set_cookie('_tz',$this->arr['allRR'][$id]->tzone,86500);
			$this->last_act_update($id);
			header("Location:../../home");
			exit();			
		}else{
			$this->arr['html'] = "<div style=\"font-size: 15pt; font-weight: bold; color: maroon; margin: 20px;\">The railroad you are trying to switch to does not have the same Owner Name as the railroad you are currently logged in as! If you know the password for the railroad you are trying to switch to please logout of the current railroad, then log-in as the railroad you are trying to switch to and change the Owner Name so it is exactly the same as the railroad you are currently logged in as.</div>";
			$this->load->view('header',$this->arr);
			$this->load->view('menu',$this->arr);
			$this->load->view('html', $this->arr);
			$this->load->view('footer');
		}
		// END check that railroad being switched to has the same Owner Name as the one being switched from.

		/* MOVED UP INTO OWNER NAME CHECK ABOVE
		$this->input->set_cookie('rr_sess',$id,86500); 
		$this->input->set_cookie('_tz',$this->arr['allRR'][$id]->tzone,86500);
		$this->last_act_update($id);
		header("Location:../../home");
		exit();
		*/
	}	

	function last_act_update($id=0){
		$this->Generic_model->change("UPDATE `ichange_rr` SET `last_act` = '".date('U')."' WHERE `id` = '".$id."'");		
	}
}
?>
