<?php
class Tests extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	var $arr = array(
			'pgTitle' => "MRICF - Model Rail Interchangecars Facility" , 
			'rr_sess' => 0
		);
	
	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('mricf');
		$this->load->library('formgen');
		
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();
	}

	public function index(){
		echo "Whatever";
		exit();
	}
	
	public function moveProgJSON2Tbl($t=''){
		// Once off to move progress from JSON array for waybills to progress table.
		if($t == "james"){
			// If James then ok to perform, otherwise npthing!
			$sql = "SELECT `id`,`waybill_num`,`progress` FROM `ichange_waybill` WHERE status != 'CLOSED'";
			echo "Start.<br />";
			$wbs = (array)$this->Generic_model->qry($sql);
			for($i=0;$i<count($wbs);$i++){
				$prog_arr = @json_decode($wbs[$i]->progress,true);
				echo "<pre>"; print_r($prog_arr); echo "</pre>Count prog_arr = ".count($prog_arr)."<br />";
				//$new_prog_arr[0] = $prog_arr[(count($prog_arr)-1)];
				//$new_prog_arr[0]['text'] = strip_tags($new_prog_arr[0]['text']); 
				//$new_prog_arr[0]['text'] = str_replace("'","",$prog_arr[(count($prog_arr)-1)]['text']); //str_replace("/","&#47;",$prog_arr[(count($prog_arr)-1)]['text']);
				for($p=0;$p<count($prog_arr);$p++){
					$text = strip_tags($prog_arr[$p]['text']);
					$text = str_replace(array("<strong>","'","&lt;/strong&gt;"),"",$text);
					$prog_sql = "INSERT INTO `ichange_progress` SET 
						`date` = '".$prog_arr[$p]['date']."', 
						`time` = '".$prog_arr[$p]['time']."', 
						`text` = '".$text."', 
						`waybill_num` = '".$wbs[$i]->waybill_num."', 
						`map_location` = '".$prog_arr[$p]['map_location']."', 
						`status` = '".$prog_arr[$p]['status']."', 
						`train` = '".$prog_arr[$p]['train']."', 
						`rr` = '".$prog_arr[$p]['rr']."', 
						`exit_location` = '".$prog_arr[$p]['exit_location']."', 
						`tzone` = '".$prog_arr[$p]['tzone']."', 
						`added` = '".date('U')."'";
					echo "<pre>".$prog_sql."</pre>";
					$this->Generic_model->change($prog_sql);
				}
				//$new_prog_sql = "UPDATE `ichange_waybill` SET `progress` = '".json_encode($new_prog_arr)."' WHERE `id` = '".$wbs[$i]->id."'";
				$new_prog_sql = "UPDATE `ichange_waybill` SET `progress` = '[]' WHERE `id` = '".$wbs[$i]->id."'";
				echo $new_prog_sql."<hr />";
				$this->Generic_model->change($new_prog_sql);
			}
			echo "End.<br />";
		}
	}
	
}
?>
