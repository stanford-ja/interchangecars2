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

    function getLocos4RR($id=0,$ord=array('loco_num'),$afils=array(),$not_mine = 0){ //,$sw=0,$swn='0'){
    	// Get all locos for a railroad.
    	// $sw = allow loco NOT ON SWITCHLIST only.
    	// $swn = ichange_train.id number to ignore for test
    	// $afils = array of affiliate railroad ids
    	// $not_mine = include locos that are NOT in motive power list for $id? 1 = yes, all else = no.
    	$olist = ""; 
    	for($o=0;$o<count($ord);$o++){
    		if($o>0){$olist .= ", ";}
    		$olist .= "`".$ord[$o]."`";
    	}
    	$afil_sql = "";
    	for($a=0;$a<count($afils);$a++){
    		//if($a>0){$afil_sql .= ", ";}
    		$afil_sql .= ",'".$afils[$a]."'";
    	}
    	$xtra_whr = ""; if($not_mine == 1){$xtra_whr = " OR (`avail_to` = 1 AND `rr` IN (''".$afil_sql.")) OR `avail_to` = 2";}
    	if(strlen($olist) > 0){$olist = " ORDER BY ".$olist;}
  		//$loco_sql_arr = "";
  		//$sql = "SELECT * FROM `".$this->tbl."` 	WHERE (`rr` = '".$id."' OR `rr` = '0' OR LENGTH(`rr`) = 0) AND `loco_num` NOT IN (''".$loco_sql_arr.") ".$olist;
  		$sql = "SELECT * FROM `".$this->tbl."` 	WHERE (`rr` = '".$id."' OR `rr` = '0' OR LENGTH(`rr`) = 0".$xtra_whr.") ".$olist; // AND `loco_num` NOT IN (''".$loco_sql_arr.") ".$olist;
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

/*    
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
*/
}
?>