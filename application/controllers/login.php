<?php
class Login extends CI_Controller {

	var $rr_sess = 0;
	var $fluxbb_users = "ichange_fluxbb_users"; // FluxBB users table.
	
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
		//echo "<pre>"; print_r($this->arr['allRRKys']); echo "</pre>"; //exit();
		
		$this->cookiedie = intval(86400*3);
	}

	public function index(){
		// Do bulk update of all railroad report marks in Forum then go there to log in.
		for($i=0;$i<count($this->arr['allRRKys']);$i++){
			$this->doForumUpdate($this->arr['allRRKys'][$i]);
		}
		header("Location:".WEB_ROOT."/forum/login.php");

		/* REPLACED BY LOGIN VIA FORUM ABOVE! 2020-08-26
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
		*/
	}

	public function chkFromForumLogin($reqUsername="",$reqPassword="",$expire=0){
		$sql = "SELECT `id` FROM `ichange_rr` WHERE MD5(`report_mark`) = '".$reqUsername."' AND `pw` = '".$reqPassword."'";
		$qry = (array)$this->Generic_model->qry($sql);
		if(isset($qry[0]->id) && $qry[0]->id > 0){
			if($expire > 0){
				$this->cookiedie = $expire;
			}
			$this->input->set_cookie('rr_sess',$qry[0]->id,$this->cookiedie);
			$this->input->set_cookie('_tz',$this->arr['allRR'][$qry[0]->id]->tzone,$this->cookiedie);
			if(@$this->arr['allRR'][$qry[0]->id]->admin_flag == 1){$this->input->set_cookie('_mricfadmin',1,$$this->cookiedie);}
			$this->last_act_update($qry[0]->id);
			$this->session->set_flashdata('loginSuccess', '1');
			header("Location:".WEB_ROOT.INDEX_PAGE."/home");
		}else{
			header("Location:".WEB_ROOT."/forum/?MRICFLF=1");
		}
	}
	
	public function chk(){
		//print_r($_POST);
		if(md5($_POST['p_word']) == $this->arr['allRR'][$_POST['rr_selected']]->pw){
			$this->input->set_cookie('rr_sess',$_POST['rr_selected'],$this->cookiedie);
			$this->input->set_cookie('_tz',$this->arr['allRR'][$_POST['rr_selected']]->tzone,$this->cookiedie);
			if(@$this->arr['allRR'][$_POST['rr_selected']]->admin_flag == 1){$this->input->set_cookie('_mricfadmin',1,$this->cookiedie);}
			$this->last_act_update($_POST['rr_selected']);
			$this->session->set_flashdata('loginSuccess', '1');
			
			// Update Forum user record for this user.
			$this->doForumUpdate($_POST['rr_selected']);
			
			// Now do login to forum so it is available to this user too
			//header('Location:'.WEB_ROOT.'/forum/login.php?fromMRICF=1');  
			
			header('Location:'.WEB_ROOT.INDEX_PAGE.'/home');
		}else{header('Location:'.WEB_ROOT.INDEX_PAGE.'/login');}
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
			$this->input->set_cookie('rr_sess',$id,$this->cookiedie); 
			$this->input->set_cookie('_tz',$this->arr['allRR'][$id]->tzone,$this->cookiedie);
			$this->last_act_update($id);
			$this->doForumUpdate($id);
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
	
	// Updates or adds record for report_mark to fluxbb users table so when a user logs into MRICF the FluxBB password is the same as MRICF.
	function doForumUpdate($id=0){
		$id2 = 0;
		if(isset($this->arr['allRR'][$id]->report_mark)){
			$sql = "SELECT COUNT(`id`) AS `cntr`, `id` FROM `".$this->fluxbb_users."` WHERE `username` = '".$this->arr['allRR'][$id]->report_mark."'";
			$res = (array)$this->Generic_model->qry($sql);
			if($res[0]->cntr > 0){
				$sql = "UPDATE `".$this->fluxbb_users."` SET 
					`password` = '".$this->arr['allRR'][$id]->pw."',
					`title` = '".$this->arr['allRR'][$id]->rr_name."',
					`realname` = '".$this->arr['allRR'][$id]->owner_name."' 
					WHERE `username` = '".$this->arr['allRR'][$id]->report_mark."'";
				$this->Generic_model->change($sql);
				$id2 = $res[0]->id;
			}else{
				$sql = "INSERT INTO `".$this->fluxbb_users."` SET 
					`username` = '".$this->arr['allRR'][$id]->report_mark."', 
					`password` = '".$this->arr['allRR'][$id]->pw."',
					`title` = '".$this->arr['allRR'][$id]->rr_name."',
					`realname` = '".$this->arr['allRR'][$id]->owner_name."', 
					`email_setting` = 1,
					`language` = 'English',
					`style` = 'Air',
					`registered` = '".date('U')."',
					`group_id` = 4,
					`registration_ip` = '".$_SERVER['REMOTE_ADDR']."'";
				$id2 = $this->Generic_model->change($sql);
			}
		}
		//return $id2;
	}
}
?>
