<?php
class Train_model extends CI_Model {
	var $tbl = "ichange_trains";

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

	function get_all4RR_Sorted($rr=0,$fld='train_id',$notAuto=0){
		$aut = ""; if($notAuto == 1){$aut = " AND `auto` < 1 AND LENGTH(`auto`) < 5";}
		$ord_by = "`railroad_id` DESC"; if(isset($this->order_by) & strlen($this->order_by) > 0){ $ord_by = $this->order_by; }
		$query = $this->db->query("SELECT * FROM `".$this->tbl."` WHERE (`railroad_id` = '".$rr."' OR `railroad_id` = '0' OR LENGTH(`railroad_id`) < 1)".$aut." ORDER BY ".$ord_by.", `".$fld."`");
		return $query->result();
	}

    function get_allNot4RR_Sorted($rr=0,$fld='train_id'){
        $query = $this->db->query("SELECT * FROM `".$this->tbl."` WHERE `railroad_id` != '".$rr."' ORDER BY `railroad_id` DESC, `".$fld."`");
        return $query->result();
    }

	function get_all4day_sorted($rr=0,$d='sun',$a=0){
		// $rr = railroad, $d = day, $au = show auto trains (0=no, 1=yes, 2=trains with sheet order)
		$s = "SELECT `".$this->tbl."`.*, COUNT(`ichange_waybill`.`id`) AS `wb_alloc`, (SELECT COUNT(`ichange_tr_cars`.`id`) FROM `ichange_tr_cars` WHERE `ichange_tr_cars`.`trains_id` = `ichange_trains`.`id`) AS `tr_alloc`, `ichange_waybill`.`cars` AS `wb_cars` 
			FROM `".$this->tbl."` 
			LEFT JOIN `ichange_waybill` ON `".$this->tbl."`.`train_id` = `ichange_waybill`.`train_id` 
			WHERE `railroad_id` = '".$rr."' AND `".$d."` = 1";
		//if($a == 0){$s .= " AND (LENGTH(`auto`) < 1 OR `auto` < 1)";}
		if($a == 0){$s .= " AND `auto` = 0 AND LENGTH(`auto`) < 2";}
		if($a == 2){$s .= " AND LENGTH(`tr_sheet_ord`) > 0";}
		$s .= " GROUP BY ichange_trains.train_id ORDER BY tr_sheet_ord";
		echo $s; //exit();

		$query = $this->db->query($s);
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

	// Methods for train sheet status updating!
	function crewTrId($arr){
		$complete = $this->getCompleteArr($arr['id']);	
		$complete[$arr['day']] = "C";	
		$s = "UPDATE `".$this->tbl."` SET `complete` = '".json_encode($complete)."' WHERE `id` = '".$arr['id']."'";
		$this->db->query($s);
		//$this->db->update($this->tbl, $this, array('id' => $arr['id']));
	}

	function compTrId($arr){
		$complete = $this->getCompleteArr($arr['id']);	
		$complete[$arr['day']] = "Y";	
		$s = "UPDATE `".$this->tbl."` SET `complete` = '".json_encode($complete)."' WHERE `id` = '".$arr['id']."'";
		$this->db->query($s);
		//$this->db->update($this->tbl, $this, array('id' => $arr['id']));
	}
	
	function strtTrId($arr){
		$complete = $this->getCompleteArr($arr['id']);	
		$complete[$arr['day']] = "S";	
		$s = "UPDATE `".$this->tbl."` SET `complete` = '".json_encode($complete)."' WHERE `id` = '".$arr['id']."'";
		$this->db->query($s);
		//$this->db->update($this->tbl, $this, array('id' => $arr['id']));
	}

	function compReset($arr){
		$s = "UPDATE `".$this->tbl."` SET `complete` = '' WHERE `railroad_id` = '".$arr['railroad_id']."'";
		$this->db->query($s);
		//$this->db->update($this->tbl, $this, array('railroad_id' => $arr['railroad_id']));
	}

	function getCompleteArr($id=0){
		$s = "SELECT `complete` FROM `".$this->tbl."` WHERE `id` = '".$id."'";
		$q = $this->db->query($s);
		$r = $q->result();

		if(strlen($r[0]->complete) < 5){
			$arr = array(
				"sun" => $r[0]->complete,
				"mon" => $r[0]->complete,
				"tues" => $r[0]->complete,
				"wed" => $r[0]->complete,
				"thu" => $r[0]->complete,
				"fri" => $r[0]->complete,
				"sat" => $r[0]->complete,
			);
		}else{ $arr = @json_decode($r[0]->complete,true); }
		if(!isset($arr['sun'])){ $arr['sun'] = ""; }
		if(!isset($arr['mon'])){ $arr['mon'] = ""; }
		if(!isset($arr['tues'])){ $arr['tues'] = ""; }
		if(!isset($arr['wed'])){ $arr['wed'] = ""; }
		if(!isset($arr['thu'])){ $arr['thu'] = ""; }
		if(!isset($arr['fri'])){ $arr['fri'] = ""; }
		if(!isset($arr['sat'])){ $arr['sat'] = ""; }
		
		return $arr;
	}
	
	function getNonCompletedCountXDay($day="",$rrid=0){
		$s = "SELECT COUNT(`id`) AS `cntr` FROM `".$this->tbl."` WHERE `complete` NOT LIKE '%\"".$day."\":\"Y\"%' AND `".$day."` = '1' AND `auto` < 1 AND LENGTH(`auto`) < 5 AND (`railroad_id` = '".$rrid."' OR `railroad_id` = '0' OR LENGTH(`railroad_id`) < 1)";
		$q = $this->db->query($s);
		$r = $q->result();
		$cntr = $r[0]->cntr;

		return $cntr;
	}
}
?>
