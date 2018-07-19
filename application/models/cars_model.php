<?php
class Cars_model extends CI_Model {
	var $tbl = "ichange_cars";

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

    function getCars4RR($id=0,$ord=array('car_num'),$nw=0,$wb='0'){
    	// Get all cars for a railroad, excluding ones that are already on waybills. (changed 2013-08-05)
    	// $nw = allow cars NOT ON WAYBILL only.
    	// $wb = waybill number to ignore for test
    	$olist = ""; 
    	for($o=0;$o<count($ord);$o++){
    		if($o>0){$olist .= ", ";}
    		$olist .= "`".$ord[$o]."`";
    	}
    	if(strlen($olist) > 0){$olist = " ORDER BY ".$olist;}
    	if(isset($this->order_by) && strlen($this->order_by) > 0){ $olist = " ORDER BY ".$this->order_by; }
    	$crs = ""; // Array list of cars already on waybill
    	$sql = "SELECT * FROM `".$this->tbl."` WHERE `rr` = '".$id."' OR `rr` = '0' OR LENGTH(`rr`) = 0".$olist;
  		$car_sql_arr = "";
    	if($nw == 1){
    		$s2 = "SELECT `cars` FROM `ichange_waybill` WHERE `status` != 'CLOSED' AND `waybill_num` != '".$wb."'";
    		$q2 = $this->db->query($s2);
    		$r2 = (array)$q2->result();
    		for($qi=0;$qi<count($r2);$qi++){
    			$qc = @json_decode($r2[$qi]->cars, TRUE);
    			for($ci=0;$ci<count($qc);$ci++){
    				if(@strpos("a".$car_sql_arr,$qc[$ci]['NUM']) < 1 && $qc[$ci]['NUM'] != "UNDEFINED"){$car_sql_arr .= ",'".$qc[$ci]['NUM']."'";}
    			}
    		}
    	}
  		$sql = "SELECT * FROM `".$this->tbl."` 	WHERE (`rr` = '".$id."' OR `rr` = '0' OR LENGTH(`rr`) = 0) AND `car_num` NOT IN (''".$car_sql_arr.") ".$olist;
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
        $this->car_num = $arr['car_num'];
        $this->aar_type = $arr['aar_type'];
        $this->desc = $arr['desc'];
        $this->special_instruct = $arr['special_instruct'];
        $this->rr = $arr['rr'];
        $this->bad_order = $arr['bad_order'];
        $this->bad_desc = $arr['bad_desc'];
        $this->location = $arr['location'];
        $this->lading = $arr['lading'];

        $this->db->insert($this->tbl, $this);
	}

	function update_entry($arr){
        $this->car_num = $arr['car_num'];
        $this->aar_type = $arr['aar_type'];
        $this->desc = $arr['desc'];
        $this->special_instruct = $arr['special_instruct'];
        $this->rr = $arr['rr'];
        $this->bad_order = $arr['bad_order'];
        $this->bad_desc = $arr['bad_desc'];
        $this->location = $arr['location'];
        $this->lading = $arr['lading'];

		$this->db->update($this->tbl, $this, array('id' => $arr['id']));
	}

}
?>