<?php
class Example_jquery extends CI_Controller {
	// JQuery file is in ../js/jquery-1.8.2.min.js!  
	var $arr = array(
			'title' => "JavaScript example" ,
			'header' => "Javascript / JQuery example",
			'library_src' => "<script language=\"javascript\" src=\"js/common.js\"></script>"
		);

	function __construct(){
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!! 
		//$this->load->library('javascript', array('js_library_driver' => 'jquery', 'autoload' => TRUE));
		$this->load->library('javascript');
	}

	function index(){
		// Example of JQuery functions.
		$this->load->view('header', $this->arr);
		$this->load->view('example_jquery'); 
		$this->load->view('footer'); 
	}
	
}
?>