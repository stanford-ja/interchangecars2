<?php 
class MessageHook {
	function __construct(){
		$this->CI =& get_instance();
		$this->my_rr_ids = array(); 
		//if(isset($this->CI->my_rr_ids)){ $this->my_rr_ids = $this->CI->my_rr_ids; }
		//elseif(isset($_COOKIE['rr_sess'])){ $this->my_rr_ids = array($_COOKIE['rr_sess']); }
		if(isset($this->CI->mricf) && isset($_COOKIE['rr_sess']) && isset($this->CI->arr['allRR'])){
			$this->my_rr_ids = $this->CI->mricf->affil_ids($_COOKIE['rr_sess'],$this->CI->arr['allRR']);
		}
	}
	
	function getCntr(){
		$msCntr = 0;
		if($this->CI->session->flashdata('loginSuccess') == 1){ // Only remind user they have un-ack messages immediately after login!
			if(isset($this->CI->Generic_model) && count($this->my_rr_ids) > 0){ 
				for($ri=0;$ri<count($this->my_rr_ids);$ri++){
					$msCntr1 = (array)$this->CI->Generic_model->qry("SELECT COUNT(id) AS msCntr FROM ichange_messages WHERE torr = '".$this->my_rr_ids[$ri]."' AND ack < 1");
					$msCntr = intval($msCntr+$msCntr1[0]->msCntr);
				}
			}
		}
		$this->CI->arr['msCntr'] = $msCntr;
	}
}
?>
