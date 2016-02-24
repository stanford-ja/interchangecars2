<?php
class About extends CI_Controller {

	var $whr = "";
	var $content = array('html' => '','phtml' => '', 'rhtml' => '', 'thtml' => '', 'ahtml' => '', 'shtml' => "", 'mhtml' => "", 'ghtml' => "");
	var $waybills = array();
	var $porders = array();
	var $myCars = array();
	var $horiz_loc = 120;
	var $my_rr_ids = array();
	
	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!

		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('mricf');
		$this->load->library('dates_times');
		$this->load->library('email');

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - About";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		$this->arr['affil'] = array();
		
	}

	public function index(){
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		$this->load->view('pages/application_information.php', $this->content);
		$this->load->view('footer');
	}
	

}
?>
