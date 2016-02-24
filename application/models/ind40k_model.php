<?php
class Ind40k_model extends CI_Model {
	var $tbl = "ichange_ind40k";

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_latest_entries($a=10){
        $query = $this->db->get($this->tbl, $a);
        return $query->result();
    }

    function get_all(){
        $query = $this->db->get($this->tbl);
        return $query->result();
    }

    function get_search($whr='LENGTH(`industry`) > 0',$lim=150){
        $query = $this->db->query("SELECT * FROM `".$this->tbl."` WHERE ".$whr." LIMIT ".$lim);
        return $query->result();
    }

    function get_single($id=0,$fld='id'){
        $query = $this->db->query("SELECT * FROM `".$this->tbl."` WHERE `".$fld."` = '".$id."'");
        return $query->result();
    }

    function get_allSorted($fld='industry'){
        $query = $this->db->query("SELECT * FROM `".$this->tbl."` ORDER BY `".$fld."`");
        return $query->result();
    }

	function get_fld_names(){
		$fields = $this->db->list_fields($this->tbl);
		return $fields;
	}

}
?>