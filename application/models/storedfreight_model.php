<?php
class Storedfreight_model extends CI_Model {
	var $tbl = "ichange_indust_stored";

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->library('mricf');
    }
    
    function get_latest_entries($a=10){
        //$query = $this->db->get($this->tbl, $a);
		$sql = "SELECT `ichange_indust_stored`.*,`ichange_indust`.`indust_name` 
			FROM `ichange_indust_stored` 
			LEFT JOIN `ichange_indust` ON `ichange_indust_stored`.`indust_id` = `ichange_indust`.`id`, `ichange_indust`.`town` 
			ORDER BY `added` DESC LIMIT ".$a;

        $query = $this->db->query("SELECT * FROM `ichange_indust_stored` WHERE `status` != 'CLOSED' ORDER BY `added` DESC LIMIT ".$a);
        return $query->result();
    }

    function get_all(){
    	if(!isset($this->whr)){ $this->whr = "`ichange_indust_stored`.`id` > 0"; }
		$sql = "SELECT `ichange_indust_stored`.*,`ichange_indust`.`indust_name`, `ichange_indust`.`town` 
			FROM `ichange_indust_stored` 
			LEFT JOIN `ichange_indust` ON `ichange_indust_stored`.`indust_id` = `ichange_indust`.`id` 
			WHERE ".$this->whr."
			ORDER BY `ichange_indust`.`indust_name`";
        $query = $this->db->query($sql);
        return $query->result();
    }
    
    function get_all_nonzero(){
    	// Get all non-zero cars available records
    	$this->whr = "`ichange_indust_stored`.`qty_cars` > 0";
    	return $this->get_all();
    }

    function get_single($id=0,$fld='ichange_indust_stored`.`id'){
    		$sql = "SELECT `ichange_indust_stored`.*,`ichange_indust`.`indust_name`, `ichange_indust`.`town` 
			FROM `ichange_indust_stored` 
			LEFT JOIN `ichange_indust` ON `ichange_indust_stored`.`indust_id` = `ichange_indust`.`id` WHERE `".$fld."` = '".$id."'";
        $query = $this->db->query($sql);
        return $query->result();
    }
 
	function get_fld_names(){
		$fields = $this->db->list_fields($this->tbl);
		return $fields;
	}
	
	function updateQtyCars($arr){
		$this->qty_cars = $arr['qty_cars'];
		$this->db->update($this->tbl, $this, array('id' => $arr['id']));
	}
	    
    function insert_entry($arr){
        $this->added = date('U');
        $this->indust_id = $arr['indust_id'];
        $this->qty_cars = $arr['qty_cars'];
        $this->commodity = $arr['commodity'];

        $this->db->insert($this->tbl, $this);
    }

	function update_entry($arr){
        $this->added = date('U');
        $this->indust_id = $arr['indust_id'];
        $this->qty_cars = $arr['qty_cars'];
        $this->commodity = $arr['commodity'];

		$this->db->update($this->tbl, $this, array('id' => $arr['id']));
	}

}
?>