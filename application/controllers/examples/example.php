<?php
class Example extends CI_Controller {
	var $arr = array(
			'title' => "title, title title" ,
			'header' => "This is a heading"
		);
	
	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->model('Example_model','',TRUE); // Database connection! TRUE means connect to db.
	}

	public function index(){
		$arr2 = array(
			'heading' => "Welcome at ".date('Y-m-d H:i:s'), 
			'content' => "This is some content passed via an array"
		);
		
		$this->load->view('header', $this->arr);
		$this->load->view('example', $arr2);
		$this->load->view('footer');
	}

	public function comments($r=''){
		// Display data passed in array or $this->?? to views/example_comments.php
		$this->r = $r;
		$arr = array(
			'comment' => 'A comment passed via an array ',
		);
		$this->load->view('header', $this->arr);
		$this->load->view('example_comments', $arr);
		$this->load->view('footer');
	}
	
	public function listing(){
		// Listing of array in views/example_list.php, use of html helper.
		$this->load->helper('form');
		$arr2 = array(
			'heading' => "Welcome at ".date('Y-m-d H:i:s'),
			'list_items' => array("Biff", "Bob", "Jock", "Fred", "Jon")
		);

		$this->load->view('header',$this->arr);
		$this->load->view('example_list', $arr2);
		$this->load->view('footer');
	}
	
	public function db_listing(){
		$this->last_num = 10;
		$arr2 = array(
			'heading' => "Welcome at ".date('Y-m-d H:i:s'),
			'query' => $this->Example_model->get_latest_entries($this->last_num),
			'query2' => $this->Example_model->get_all()
		);

		$this->load->view('header',$this->arr);
		$this->load->view('example_dblist', $arr2);
		$this->load->view('footer');
	}

	public function a_form($id=0){
		$this->load->helper('form');
		$query = $this->db->query("SELECT * FROM ichange_rr WHERE `id` = '".$id."' LIMIT 1");
		$ar = array();
		foreach ($query->result_array() as $row){$ar[] = $row;}
		$arr2 = array(
			'heading' => "Welcome at ".date('Y-m-d H:i:s'),
			'id' => $id, 
			'data' => $ar
		);

		$this->load->view('header',$this->arr);
		$this->load->view('example_form', $arr2);
		$this->load->view('footer');
	}
}
?>