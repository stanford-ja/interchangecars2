<?php
class Pages extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	var $vars = array('fils' => array());
	
	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->library('mricf');
		
		//$this->load->model('Aar_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Pages";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

	}

	public function index($p=''){
		// $p = view file
		// show list of pages
		$this->f_list();
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		$this->load->view('page',$this->vars);
	}

	function f_list(){
		// Generates list of logs files 
		$fls = scandir("application/views/pages/");
		$fcntr = count($fls);
		for($i=0;$i<$fcntr;$i++){
			if(strpos($fls[$i],".php") > 0){$this->vars['fils'][] = $fls[$i];}
		}
	}
	
	function view($p){
		// show indicated page
		$t = str_replace("_"," ",$p);
		$t = ucwords($t);
		//$this->arr['pgTitle'] .= " - ".$t;
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		$this->load->view("pages/".$p);
		$this->load->view('footer');
	}

}
?>