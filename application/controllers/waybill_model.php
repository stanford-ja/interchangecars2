<?php
class Waybill_model extends CI_Model {
	var $tbl = "ichange_waybill";

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->library('mricf');
    }
    
    function get_latest_entries($a=10){
        //$query = $this->db->get($this->tbl, $a);
        $query = $this->db->query("SELECT * FROM `ichange_waybill` WHERE `status` != 'CLOSED' ORDER BY `date` DESC LIMIT ".$a);
        return $query->result();
    }

    function get_all(){
        $query = $this->db->get($this->tbl);
        return $query->result();
    }

    function get_allOpen($ordBy='waybill_num'){
        $query = $this->db->query("SELECT * FROM `ichange_waybill` WHERE `status` != 'CLOSED' ORDER BY `".$ordBy."`");
        return $query->result();
    }

    function get_single($id=0,$fld='waybill_num'){
        $query = $this->db->query("SELECT * FROM `ichange_waybill` WHERE `".$fld."` = '".$id."'");
        return $query->result();
    }
    
    function get_search_results($id=0,$fld='waybill_num'){
        $query = $this->db->query("SELECT * FROM `ichange_waybill` WHERE `".$fld."` LIKE '%".$id."%'");
        return $query->result();
    }
 
    function get_allOpenHome($s=''){
    	// $s = railroad WHERE condition - railroad logged in as or affil railroad (OR)
   	//if(strlen($s) > 0){$s = " AND ".$s;}
			$sql = "SELECT * FROM `ichange_waybill` WHERE `status` != 'CLOSED' AND ".$s." ORDER BY `waybill_num`";
			//$sql = "SELECT * FROM `ichange_waybill` WHERE `status` != 'CLOSED' AND (".$s."`routing` LIKE '%".$r."%' OR `status` = 'P_ORDER' ".$s1.") ORDER BY `waybill_num`";
			//echo $sql."<br />";
        $query = $this->db->query($sql);
        return $query->result();
    }
    
    function get_POrders(){
			$sql = "SELECT * FROM `ichange_waybill` WHERE `status` = 'P_ORDER' ORDER BY `waybill_num`";
        $query = $this->db->query($sql);
        return $query->result();
    }
    
	function get_allProgress(){
		// All locations from ALL existing waybills
		$sql = "SELECT `progress` FROM `ichange_waybill`";
		$query = $this->db->query($sql);
		return $query->result();  
	}

	function get_all4Train($trid=''){
		// $trid = train_id field where test
		$query = $this->db->query("SELECT * FROM `ichange_waybill` WHERE `train_id` = '".$trid."' AND `status` != 'CLOSED'");
		return $query->result();
	}

	function get_allTranshipped($wbnum=''){
		// $wbnum = waybill number (waybill_num field)
		$query = $this->db->query("SELECT `waybill_num` FROM `ichange_waybill` WHERE `waybill_num` LIKE '".$wbnum."%'");
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