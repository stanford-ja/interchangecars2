<?php
class Forum_model extends CI_Model {
	var $tbl_prefix = "ichange_fluxbb_";

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_recent_for_rr($rm="",$lim=15){
        $sql = "SELECT `".$this->tbl_prefix."posts`.*, `".$this->tbl_prefix."topics`.`subject`, 
            `".$this->tbl_prefix."topics`.`subject`,`".$this->tbl_prefix."topics`.`forum_id`, 
            `".$this->tbl_prefix."forums`.`forum_name` 
            FROM `".$this->tbl_prefix."posts` 
            LEFT JOIN `".$this->tbl_prefix."topics` ON `".$this->tbl_prefix."posts`.`topic_id` = `".$this->tbl_prefix."topics`.`id` 
            LEFT JOIN `".$this->tbl_prefix."forums` ON `".$this->tbl_prefix."topics`.`forum_id` = `".$this->tbl_prefix."forums`.`id` 
            WHERE `".$this->tbl_prefix."posts`.`poster` = '".$rm."' AND `".$this->tbl_prefix."posts`.`posted` > ".intval(date('U')-(86400*90))." 
            ORDER BY `".$this->tbl_prefix."posts`.`posted` DESC 
            LIMIT 0,".$lim;
        $query = $this->db->query($sql);
        return $query->result();
    }


}
