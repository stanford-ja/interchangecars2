<?php
class Api extends CI_Controller {
	
	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->library('mricf');
		
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		//$this->arr['pgTitle'] .= " - AAR Codes";
		//$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		//if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

	}

	public function index(){
		echo "Move along, nopthing to see here!";
	}
	
	public function getData($key="",$tbl="",$whr=""){
		echo $results;
	}

}
?>