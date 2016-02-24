<?php

class Rest extends CI_Controller {
// Test REST controller - Not working as it should be, at least using the /var/www/Applications/restful_mricf/local_files/test.php
function __construct(){
	parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
	$this->load->library('xmlrpc');
	$this->load->library('xmlrpcs');

	echo "got into controller<br />";

	$config['functions']['home'] = array('function' => 'Rest.boing');
	$config['functions']['update_post'] = array('function' => 'My_blog.update_entry');
	$config['object'] = $this;

	$this->xmlrpcs->initialize($config);
	$this->xmlrpcs->serve();

	echo "got to end of construct<br />";
}

function boing(){
	echo "got into boing<br />";
		$parameters = $request->output_parameters();

		$response = array(
							array(
									'you_said'  => $parameters['0'],
									'i_respond' => 'Not bad at all.'),
							'struct');

		echo "return". $this->xmlrpc->send_response($response);
}

}
?>
