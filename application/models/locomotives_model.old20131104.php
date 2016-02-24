<?php
class Locomotives_model extends CI_Model {
	var $tbl = "ichange_locos";

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

    function getLocos4RR($id=0,$ord=array('loco_num'),$sw=0,$swn='0'){
    	// Get all cars for a railroad, excluding ones that are already on waybills. (changed 2013-08-05)
    	// $sw = allow loco NOT ON SWITCHLIST only.
    	// $swn = ichange_train.id number to ignore for test
    	$olist = ""; 
    	for($o=0;$o<count($ord);$o++){
    		if($o>0){$olist .= ", ";}
    		$olist .= "`".$ord[$o]."`";
    	}
    	if(strlen($olist) > 0){$olist = " ORDER BY ".$olist;}
    	$crs = ""; // Array list of cars already on waybill
    	$sql = "SELECT * FROM `".$this->tbl."` WHERE `rr` = '".$id."' OR `rr` = '0' OR LENGTH(`rr`) = 0".$olist;
  		$loco_sql_arr = "";
    	if($sw == 1){
    		$s2 = "SELECT `loco_num` FROM `ichange_trains` WHERE `railroad_id` = '".$id."'";
    		$q2 = $this->db->query($s2);
    		$r2 = (array)$q2->result();
    		for($qi=0;$qi<count($r2);$qi++){
    			if(@strpos("a".$car_sql_arr,$qc[$ci]['NUM']) < 1 && $qc[$ci]['NUM'] != "UNDEFINED"){$loco_sql_arr .= ",'".$qc[$ci]['NUM']."'";}
    		}
    	}
  		$sql = "SELECT * FROM `".$this->tbl."` 	WHERE (`rr` = '".$id."' OR `rr` = '0' OR LENGTH(`rr`) = 0) AND `loco_num` NOT IN (''".$loco_sql_arr.") ".$olist;
      $query = $this->db->query($sql);
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
        $this->loco_num = $arr['car_num'];
        $this->manufacturer = $arr['manufacturer'];
        $this->model = $arr['desc'];
        $this->hp = $arr['special_instruct'];
        $this->rr = $arr['rr'];
        $this->desc = $arr['desc'];

        $this->db->insert($this->tbl, $this);
	}

	function update_entry($arr){
        $this->loco_num = $arr['car_num'];
        $this->manufacturer = $arr['manufacturer'];
        $this->model = $arr['desc'];
        $this->hp = $arr['special_instruct'];
        $this->rr = $arr['rr'];
        $this->desc = $arr['desc'];

		$this->db->update($this->tbl, $this, array('id' => $arr['id']));
	}

}
?>