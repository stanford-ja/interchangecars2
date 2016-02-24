<?php
class Railroad_model extends CI_Model {
	var $tbl = "ichange_rr";

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

    function get_allActive($ordBy='report_mark',$opts=0){
    	// $opts = 1 means include common_flag = 1 in returned results
    	$wot2incl = "(`inactive` != '1' OR `common_flag` = '1')" ;
    	if($opts == 1){$wot2incl = "`inactive` != '1'" ;}
  		$obgb = "ORDER"; 
  		$obrn = ""; 
  		if($ordBy == "report_mark"){
  			$obgb = "GROUP";
  		}else{$obrn = " `inactive`,`common_flag`,";}
  		//$sql = "SELECT * FROM `".$this->tbl."` WHERE (`inactive` != '1' OR `common_flag` = '1') AND LENGTH(`rr_name`) > 1 AND LENGTH(`owner_name`) > 0 ".$obgb." BY SUBSTRING(`".$ordBy."`,1,4)";
  		$sql = "SELECT *, IF(`inactive` = 1,' [INACTIVE]','') AS `inactive_txt`, IF(`common_flag` = 1,' [COMMON]','') AS common_txt FROM `".$this->tbl."` WHERE ".$wot2incl." AND LENGTH(`rr_name`) > 1 AND LENGTH(`owner_name`) > 0 ".$obgb." BY".$obrn." SUBSTRING(`".$ordBy."`,1,4)";
		$query = $this->db->query($sql);
		return $query->result();
    }

    function get_allActiveInterchanges($ordBy='report_mark'){
        $query = $this->db->query("SELECT `report_mark`,`interchanges` FROM `".$this->tbl."` WHERE `inactive` != '1' AND LENGTH(`rr_name`) > 1 AND LENGTH(`owner_name`) > 0 ORDER BY `".$ordBy."`");
        return $query->result();
    }

    function get_single($id=0){
        $query = $this->db->query("SELECT * FROM `".$this->tbl."` WHERE `id` = '".$id."'");
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