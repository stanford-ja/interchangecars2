<?php

class Rest extends CI_Controller {
	// Test REST controller - Not working as it should be, at least using the /var/www/Applications/restful_mricf/local_files/test.php
	function __construct(){
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		/*
		$this->load->library('xmlrpc');
		$this->load->library('xmlrpcs');

		echo "got into controller<br />";

		$config['functions']['home'] = array('function' => 'Rest.boing');
		$config['functions']['update_post'] = array('function' => 'My_blog.update_entry');
		$config['object'] = $this;

		$this->xmlrpcs->initialize($config);
		$this->xmlrpcs->serve();

		echo "got to end of construct<br />";
		*/
	}
	
	function test(){
		$arr = array(
			'unixts' => date('U'),
			'date_time' => date('Y-m-d H:i:s'),
			'method' => __FUNCTION__,
			'application' => "mricf",
			'aar_codes' => array(),
		);
		$sqli = new mysqli("localhost","admin","admin","jstan2_general");
		$qry = $sqli->query("SELECT * FROM `ichange_aar` ORDER BY `aar_code`");
		while($res = $qry->fetch_assoc()){
			$arr['aar_codes'][] = $res;
		}
		echo json_encode($arr);
	}

}
