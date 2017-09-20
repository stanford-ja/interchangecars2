<?php
class Waybill_model extends CI_Model {
	var $tbl = "ichange_waybill";

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->library('mricf');
    }

	function insert_id(){ return $this->db->insert_id(); }
    
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
 
    function get_allOpenHome($s='',$o='waybill_num'){
    	// $s = railroad WHERE condition - railroad logged in as or affil railroad (OR)
   	//if(strlen($s) > 0){$s = " AND ".$s;}
			$sql = "SELECT * FROM `ichange_waybill` WHERE `status` != 'CLOSED' AND ".$s." ORDER BY `".$o."`";
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
		$query = $this->db->query("SELECT * FROM `ichange_waybill` WHERE `train_id` = '".$trid."' AND `status` != 'CLOSED' ORDER BY `sw_order`");
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
	
	function get_messages($id=0,$rr=0,$no_ack=0){
		// $id = waybill to get messages for.
		// $rr = railroad id to get messages for 
		// If $id=0 and $rr=0, get all messages.
		// $no_ack - 0 = get all, 1 = only get un-Acknowledged
		/* REPLACED JSON with TABLE - 2017-05
		$sql = "SELECT `id`,`waybill_num`,`messages` FROM `ichange_waybill` WHERE ";
		if($id > 0){$sql .= "`id` = '".$id."'";}
		elseif($rr > 0){$sql .= "`messages` LIKE '%\"torr\":\"".$rr."\"%' AND `status` != 'CLOSED'";}
		else{$sql .= "`status` != 'CLOSED' AND LENGTH(`messages`) > 5";}
		$sql .= " ORDER BY `date` DESC";
		*/
		$sql = "SELECT ichange_messages.*,ichange_waybill.id AS wb_id, ichange_waybill.waybill_num 
			FROM `ichange_messages` 
			LEFT JOIN ichange_waybill ON ichange_messages.waybill_id = ichange_waybill.id 
			WHERE ";
		if($id > 0){$sql .= "ichange_messages.waybill_id = '".$id."'";}
		elseif($rr > 0){$sql .= "torr = '".$rr."' AND ichange_waybill.status != 'CLOSED'";}
		else{$sql .= "ichange_waybill.status != 'CLOSED'";}
		if($no_ack == 1){ $sql .= " AND ichange_messages.ack != 1"; }
		$sql .= " ORDER BY ichange_messages.datetime DESC";
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	function get_carsOnAllMyWaybills($owner_name=""){
		// Get cars where from / to is a rr of user.
		$s = "SELECT `ichange_waybill`.`cars`, ichange_waybill.waybill_num, `rral`.`report_mark`, `ichange_waybill`.`train_id` 
			FROM `ichange_waybill` 
			LEFT JOIN `ichange_rr` AS `rrto` ON `ichange_waybill`.`rr_id_to` = `rrto`.`id` 
			LEFT JOIN `ichange_rr` AS `rrfr` ON `ichange_waybill`.`rr_id_from` = `rrfr`.`id` 
			LEFT JOIN `ichange_rr` AS `rral` ON `ichange_waybill`.`rr_id_handling` = `rral`.`id`
			WHERE (`rrto`.`owner_name` = '".$owner_name."' OR `rrfr`.`owner_name` = '".$owner_name."')";// AND `rral`.`owner_name` != '".$owner_name."'";
			//echo $s; 
		$tmp = $this->db->query($s);
		$tmp = $tmp->result();
		//echo "<pre>"; print_r($tmp); echo "</pre>";
		$this->carsOnAllMyWBs = array();
		$this->carsOnAllMyWBsKys = array();
		for($i=0;$i<count($tmp);$i++){
			$tmp2 = @json_decode($tmp[$i]->cars,TRUE);
			for($ii=0;$ii<count($tmp2);$ii++){
				if(strlen($tmp2[$ii]['NUM']) > 0 && $tmp2[$ii]['NUM'] != "UNDEFINED" && !in_array($tmp2[$ii]['NUM'],$this->carsOnAllMyWBsKys)){
					$tmp2[$ii]['NUM'] = str_replace(" ","",$tmp2[$ii]['NUM']);
					$tmp2[$ii]['TR_ID'] = $tmp[$i]->train_id;
					$tmp2[$ii]['REP_MK'] = $tmp[$i]->report_mark;
					$this->carsOnAllMyWBsKys[] = $tmp2[$ii]['NUM']; 
					$this->carsOnAllMyWBs[] = $tmp2[$ii]; 
				}
			}
		}		
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