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
        $query = $this->db->query("SELECT * FROM `".$this->tbl."` WHERE (`railroad_id` = '".$rr."' OR `railroad_id` = '0' OR LENGTH(`railroad_id`) < 1)".$aut." ORDER BY `railroad_id` DESC, `".$fld."`");
        return $query->result();
    }

    function get_allNot4RR_Sorted($rr=0,$fld='train_id'){
        $query = $this->db->query("SELECT * FROM `".$this->tbl."` WHERE `railroad_id` != '".$rr."' ORDER BY `railroad_id` DESC, `".$fld."`");
        return $query->result();
    }

	function get_all4day_sorted($rr=0,$d='sun',$a=0){
		// $rr = railroad, $d = day, $au = show auto trains (0=no, 1=yes)
		$s = "SELECT * FROM `".$this->tbl."` WHERE `railroad_id` = '".$rr."' AND `".$d."` = 1";
		//if($a == 0){$s .= " AND (LENGTH(`auto`) < 1 OR `auto` < 1)";}
		if($a == 0){$s .= " AND `auto` = 0 AND LENGTH(`auto`) < 2";}
		$s .= " ORDER BY `tr_sheet_ord`";
		//echo $s; exit();
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
		$s = "UPDATE `".$this->tbl."` SET `complete` = 'C' WHERE `id` = '".$arr['id']."'";
		$this->db->query($s);
		//$this->db->update($this->tbl, $this, array('id' => $arr['id']));
	}

	function compTrId($arr){
		$s = "UPDATE `".$this->tbl."` SET `complete` = 'Y' WHERE `id` = '".$arr['id']."'";
		$this->db->query($s);
		//$this->db->update($this->tbl, $this, array('id' => $arr['id']));
	}
	
	function strtTrId($arr){
		$s = "UPDATE `".$this->tbl."` SET `complete` = 'S' WHERE `id` = '".$arr['id']."'";
		$this->db->query($s);
		//$this->db->update($this->tbl, $this, array('id' => $arr['id']));
	}

	function compReset($arr){
		$s = "UPDATE `".$this->tbl."` SET `complete` = '' WHERE `railroad_id` = '".$arr['railroad_id']."'";
		$this->db->query($s);
		//$this->db->update($this->tbl, $this, array('railroad_id' => $arr['railroad_id']));
	}

}
?>