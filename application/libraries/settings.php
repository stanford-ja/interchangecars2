<?php
class Settings {
	// Class for holding all the functions used in MRICF
	var $arr = array();
	
	function __construct(){
		$this->CI =& get_instance();
		$this->setVars();
	}
	
	function setVars(){
		// Sets variables used by MRICF application
		$this->arr['pgTitle'] = "MRICF - Model Rail Interchangecars Car Forwarding v2.0";
		$this->arr['rr_sess'] = 0;
		$this->arr['message'] = $this->CI->session->flashdata('Message');
		return $this->arr;
	}
}
?>