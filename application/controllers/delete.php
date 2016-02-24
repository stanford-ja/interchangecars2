<?php
class Delete extends CI_Controller {

	var $whr = "";
	
	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->library('mricf');		
		$this->load->library('settings');

		// Security
		if(!$this->input->cookie('rr_sess')){echo "You are not logged in or the session variable has expired."; exit();}
	}

	public function index(){
		$this->arr = $_POST;
		$this->load->model('Generic_model','',TRUE);
		if(isset($this->arr['id'])){
			$this->sql = "DELETE FROM `ichange_".$this->arr['tbl']."` WHERE `id` = '".$this->arr['id']."' LIMIT 1";
			$this->Generic_model->change($this->sql);
		}
		header('Location:'.$this->arr['tbl']);
	}
	
}
?>