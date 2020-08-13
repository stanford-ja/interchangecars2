<?php
class Admin extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!
	
	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->library('mricf');
		$this->load->helper("file");
		$this->load->library('upload');
		
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - AAR Codes";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(!isset($_COOKIE['_mricfadmin'])){
			echo "You do not have the required permission to be here!";
			exit();
		}

	}

	public function index(){
		$title = "Admin Area - Restricted access";
		$c = "<!doctype><head><title>".$title."</title></head>";
		$c .= "<html>";
		$c .= "<body>";
		$c .= "<h1>".$title."</h1>";
		$c .= "<ul>";
		$c .= "<li><a href=\"".WEB_ROOT.INDEX_PAGE."/admin/convert2TblImg\">Convert Image files to Data Table Base64 Data</a></li>";
		$c .= "<li><a href=\"".WEB_ROOT.INDEX_PAGE."/admin/convertMessaging2Forum\">Convert ichange_messages to FluxBB topics / posts</a></li>";
		$c .= "</ul>";
		$c .= "</body>";
		$c .= "</html>";
		
		$content['html'] = $c;
		
		$this->load->view("html",$content);
	}
	
	function convert2TblImg($yes=0){
		if($yes == 0){
			echo "Please add /1 to the end of the URL to confirm you wish to convert waybill_images/ to data table Base64 data.";
			exit();
		}

		// Converts images in the waybill_images/ directory to base64 data in the ichange_wb_img table.
		$sql = "SELECT * FROM `ichange_wb_img` WHERE LENGTH(`image`) < 1";
		$dbimg = (array)$this->Generic_model->qry($sql);
		//echo "DBIMG = <pre>"; print_r($dbimg); echo "</pre>";
		
		$this->filePath = DOC_ROOT.'/waybill_images/';
		$this->webPath = str_replace(DOC_ROOT,WEB_ROOT,$this->filePath);
		$wbimg = get_filenames($this->filePath);
		//echo "WBIMG = <pre>"; print_r($wbimg); echo "</pre>";

		for($z=0;$z<count($wbimg);$z++){
			if($wbimg[$z] != "index.php"){
			$image_base64 = base64_encode(file_get_contents($this->filePath.$wbimg[$z]));
			$image = 'data:image/jpg;base64,'.$image_base64;

			$sql = "SELECT COUNT(`id`) AS `cntr` FROM `ichange_wb_img` WHERE `img_name` = '".$wbimg[$z]."'";
			$dbimg = (array)$this->Generic_model->qry($sql);
			if($dbimg[0]->cntr == 0){
				$sql = "INSERT INTO `ichange_wb_img` SET `added` = '".date('U')."', `image` = '".$image."', `img_name` = '".$wbimg[$z]."'";
			}else{
				$sql = "UPDATE `ichange_wb_img` SET `added` = '".date('U')."', `image` = '".$image."' WHERE `img_name` = '".$wbimg[$z]."'";
			}
			$this->Generic_model->change($sql);
			unlink($this->filePath.$wbimg[$z]);
			//echo $sql;
			//echo "DBIMG = <pre>"; print_r($dbimg); echo "</pre>";
			}
		}		

		$sql = "DELETE FROM `ichange_wb_img` WHERE LENGTH(`image`) < 1";
		$this->Generic_model->change($sql);
		echo "Waybill image data has ben converted @ ".date('Y-m-d H:i:s').".";
	}

	function convertMessaging2Forum($yes=0){
		if($yes == 0){
			echo "Please add /1 to the end of the URL to confirm you wish to convert ichange_messaging data to FluxBB posts data.";
			exit();
		}
		
		// Converts ichange_messaging data to FluxBB topics, Waybill Messaging forum
		$sql = "SELECT `ichange_messages`.*, `ichange_waybill`.`waybill_num`, `ichange_rr`.`report_mark` 
			FROM `ichange_messages` 
			LEFT JOIN `ichange_waybill` ON `ichange_messages`.`waybill_id` = `ichange_waybill`.`id` 
			LEFT JOIN `ichange_rr` ON `ichange_messages`.`rr` = `ichange_rr`.`id` 
			WHERE LENGTH(`ichange_waybill`.`waybill_num`) > 1
			ORDER BY `ichange_messages`.`waybill_id`, `ichange_messages`.`datetime`";
		$dbmess = (array)$this->Generic_model->qry($sql);
		
		$topics_ids = array();
		$last_id = 0;
		$orig_ids = array();
		for($e=0;$e<count($dbmess);$e++){
			$orig_ids[] = $dbmess[$e]->id;
			if(!isset($dbmess[intval($e-1)]) || $dbmess[$e]->waybill_id != $dbmess[intval($e-1)]->waybill_id){
				$subj = "Waybill:".$dbmess[$e]->waybill_num;
				if(strlen($dbmess[$e]->subject) > 0){ $subj .= " - ".$dbmess[$e]->subject; }
				$sql = "INSERT INTO `ichange_fluxbb_topics` SET 
					`poster` = 'Administrator', 
					`subject` = '".$subj." (imported)' ,
					`posted` = '".strtotime($dbmess[$e]->datetime)."', 
					`forum_id` = 2,
					`last_poster` = 'Administrator', 
					`last_post` = '".date('U')."'";
				$last_id = $this->Generic_model->change($sql);
				$topics_ids[] = $last_id;
				//echo "<hr />".$sql."<br />";
			}
			$sql = "INSERT INTO `ichange_fluxbb_posts` SET 
				`poster` = 'Administrator', 
				`poster_id` = 2, 
				`message` = '(From: ".$dbmess[$e]->report_mark."): ".$dbmess[$e]->text."', 
				`posted` = '".strtotime($dbmess[$e]->datetime)."', 
				`topic_id` = '".$last_id."', 
				`poster_ip` = '127.0.0.1'";
			//echo $sql."<br />";
			$last_id2 = $this->Generic_model->change($sql);
		}

		// Adjust created data to work properly
		//echo "Adjustments<br />";
		$sql = "SELECT `ichange_fluxbb_topics`.* 
			FROM `ichange_fluxbb_topics` 
			WHERE `id` IN (".implode(",",$topics_ids).")
			ORDER BY `id`";
		$dbmess = (array)$this->Generic_model->qry($sql);
		//echo "DBMESS = <pre>"; print_r($dbmess); echo "</pre>";
		
		for($r=0;$r<count($dbmess);$r++){
			$sql = "SELECT COUNT(`id`) AS `num_replies`, MIN(`id`) AS `first_post_id`, 
				MAX(`id`) AS `last_post_id` 
				FROM `ichange_fluxbb_posts` 
				WHERE `topic_id` = '".$dbmess[$r]->id."'";
			$dbmess2 = (array)$this->Generic_model->qry($sql);
			//echo "DBMESS2 = <pre>"; print_r($dbmess2); echo "</pre>";
			
			$sql = "UPDATE `ichange_fluxbb_topics` SET 
				`num_replies` = '".intval($dbmess2[0]->num_replies-1)."', 
				`first_post_id` = '".$dbmess2[0]->first_post_id."', 
				`last_post_id` = '".$dbmess2[0]->last_post_id."' 
				WHERE `id` = '".$dbmess[$r]->id."'";
			$this->Generic_model->change($sql);
			//echo $sql."<br />";
		}
		
		// Delete original ichange_messages records
		$sql = "DELETE FROM `ichange_messages` WHERE `id` IN (".implode(",",$orig_ids).")";
		$this->Generic_model->change($sql);

		echo "Conversion of ichange_messages to FLuxBB topics / posts is complete @ ".date('Y-m-d H:i:s').".";
	}
}
?>
