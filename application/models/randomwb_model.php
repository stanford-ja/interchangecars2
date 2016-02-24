<?php
class Randomwb_model extends CI_Model {
	var $tbl = "ichange_randomwb";

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

	function get_fld_names(){
		$fields = $this->db->list_fields($this->tbl);
		return $fields;
	}
    
    function insert_entry($arr){
        $this->rr_name = $arr['rr_name'];
        $this->rr_desc = $arr['rr_desc'];
        $this->report_mark = $arr['report_mark'];
        $this->home_disp = $arr['home_disp'];

        $this->db->insert($this->tbl, $this);
    }

	function update_entry($arr){
		$this->rr_name = $arr['rr_name'];
		$this->rr_desc = $arr['rr_desc'];
		$this->report_mark = $arr['report_mark'];
      $this->home_disp = $arr['home_disp'];

		$this->db->update($this->tbl, $this, array('id' => $arr['id']));
	}

}
?>