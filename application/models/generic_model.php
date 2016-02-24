<?php
class Generic_model extends CI_Model {

	function __construct()	{
		// Call the Model constructor
		parent::__construct();
	}
    
	function qry($s){
		$query = $this->db->query($s);
		//return $query->result();
		return $query->result();
	}

	function change($s){
		$query = $this->db->query($s);
		//return $query->result();
		//return $query->result();
	}

	function get_search_results($id=0,$fld='',$tbl=''){
		$whr = " `".$fld."` LIKE '%".$id."%'";
		if(is_numeric($id)){$whr = " `".$fld."` = '".$id."'";}
		$query = $this->db->query("SELECT * FROM `".$tbl."` WHERE".$whr);
		return $query->result();
	}

	function get_fld_names($tbl=''){
		$fields = $this->db->list_fields($tbl);
		return $fields;
	}
  
}
?>