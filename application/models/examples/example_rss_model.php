<?php
class Example_rss_model extends CI_Model {  
    
    // get all postings  
	function getWaybills($limit = 10){
		$qry = "SELECT * FROM `ichange_waybill` WHERE `status` != 'CLOSED' ORDER BY `date` DESC LIMIT 0,".$limit;  
		//return $this->db->get('ichange_waybill', $limit);  
		return $this->db->query($qry);
	}
} 
?>