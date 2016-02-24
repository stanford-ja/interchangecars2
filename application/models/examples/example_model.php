<?php
class Example_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_latest_entries($a=10){
        $query = $this->db->get('ichange_rr', $a);
        return $query->result();
    }

    function get_all(){
        $query = $this->db->get('ichange_rr');
        return $query->result();
    }

    function get_single($id=0){
        $query = $this->db->query("SELECT * FROM `ichange_rr` WHERE `id` = '".$id."'");
        return $query->result();
    }
    
    function insert_entry($arr){
        $this->rr_name = $arr['rr_name']; // please read the below note
        $this->rr_desc = $arr['rr_desc'];
        $this->report_mark = $arr['report_mark'];
        $this->home_disp = $arr['home_disp'];

        $this->db->insert('ichange_rr', $this);
    }

	function update_entry($arr){
		$this->rr_name = $arr['rr_name']; // please read the below note
		$this->rr_desc = $arr['rr_desc'];
		$this->report_mark = $arr['report_mark'];
      $this->home_disp = $arr['home_disp'];

		$this->db->update('ichange_rr', $this, array('id' => $arr['id']));
	}

}
?>