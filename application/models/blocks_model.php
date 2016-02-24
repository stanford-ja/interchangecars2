<?php
class Blocks_model extends CI_Model {
	var $tbl = "ichange_blocks";

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

    function get_single($id=0,$fld='id'){
        $query = $this->db->query("SELECT * FROM `".$this->tbl."` WHERE `".$fld."` = '".$id."'");
        return $query->result();
    }

    function get_allSorted($fld='block_id'){
        $query = $this->db->query("SELECT * FROM `".$this->tbl."` ORDER BY `".$fld."`");
        return $query->result();
    }

    function get_all4RRSorted($rr=0,$fld='block_id'){
        $query = $this->db->query("SELECT * FROM `".$this->tbl."` WHERE `rr_id` = '".$rr."' ORDER BY `".$fld."`");
        return $query->result();
    }

	function get_fld_names(){
		$fields = $this->db->list_fields($this->tbl);
		return $fields;
	}

    function insert_entry($arr){
        $this->aar_code = $arr['aar_code'];
        $this->desc = $arr['desc'];

        $this->db->insert($this->tbl, $this);
    }

	function update_entry($arr){
        $this->aar_code = $arr['aar_code'];
        $this->desc = $arr['desc'];

		$this->db->update($this->tbl, $this, array('id' => $arr['id']));
	}

	function update_occupied($arr){
        $this->occupied_by = $arr['occupied_by'];

		//$this->db->update($this->tbl, $this, array('id' => $arr['id']));
		$this->db->update($this->tbl, $arr, array('id' => $arr['id']));
	}

}
?>