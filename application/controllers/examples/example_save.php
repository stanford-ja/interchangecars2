<?php
// Example_save Controller - Saves details in forms to correct table

class Example_save extends CI_Controller {

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
	}

	public function index(){
	}

	public function rr(){
		// Display data passed in array or $this->?? to views/example_comments.php
		$this->load->model('Example_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->arr = $_POST;
		if($this->arr['id'] > 0){$this->Example_model->update_entry($this->arr);
		}else{unset($this->arr['id']); $this->Example_model->insert_entry($this->arr);}
		$this->show_info(array('url'=>"index.php/example/db_listing", 'label'=>"Example Listing"));
	}
	
	public function show_info($a=array()){
		$arr = array(
			'title' => "title, title title" ,
			'header' => "This is a heading"
		);
		$arr2 = array(	'link' => $a);
		$this->load->view('header',$arr);
		$this->load->view('example_show_info',$arr2);
		$this->load->view('footer');
	}
	
}


?>