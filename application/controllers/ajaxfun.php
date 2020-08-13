<?php
// ** CAN ONLY BE USED WHEN URI CHARACTERS ON config.php ARE NOT TOO RESTRICTIVE! **
class Ajaxfun extends CI_Controller {
	// JQuery file is in JS_ROOT/js/jquery-1.8.2.min.js!  

	function __construct(){
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!! 
		$this->load->model("Generic_model",'',TRUE);
		$this->load->helper('file');
	}

	function index(){
		exit();
	}
	
	function example_method($fld,$id){
		// Example ajax method. References a method in the example_model.
		$r = $this->Example_model->get_ajax_single("rr","id",$id);
		$rr = (array)$r[0];
		echo $rr[$fld];
	}
	
	function wbImages($id=0){
		// Generates HTML to display images for waybill with waybill.id of $id.
		/* REPLACED BY BELOW - 2020-08-14
		$fils = get_filenames(DOC_ROOT."/waybill_images/");
		for($i=0;$i<count($fils);$i++){
			if(strpos("Z".$fils[$i],$id."-") > 0){
				$fil_html .= "<a href=\"javascript:{}\" onclick=\"window.open('".WEB_ROOT."/waybill_images/".$fils[$i]."','".$i."','width=500,height=500');\">";
				$fil_html .= "<img src=\"".WEB_ROOT."/waybill_images/".$fils[$i]."\" style=\"height: 100px; margin: 3px;\">";
				$fil_html .= "</a>";
			}
		}
		if(strlen($fil_html) > 0){
			$fil_html = "<div style=\"color: #555; padding: 10px; margin: 3px; background-color: antiquewhite;\">
				".$fil_html."
				</div>";
			echo $fil_html;
		}else{ echo ""; }
		*/
		$fils = (array)$this->Generic_model->qry("SELECT * FROM `ichange_wb_img` WHERE LENGTH(`image`) > 0 AND `img_name` LIKE '".$id."-%' ORDER BY `img_name`"); //get_filenames($this->filePath); - REPLACED - 2020-08-13
		//echo "<pre>"; print_r($fils); echo "</pre>";
		$content = "";
		for($i=0;$i<count($fils);$i++){
			$tmp = explode("-",str_replace(".jpg","",$fils[$i]->img_name));
			//echo "<pre>"; print_r($tmp); echo "</pre>";
			$wb = (array)$this->Generic_model->qry("SELECT * FROM `ichange_waybill` WHERE `id` = '".$tmp[0]."'");
			$rr = (array)$this->Generic_model->qry("SELECT `report_mark` FROM `ichange_rr` WHERE `id` = '".$tmp[1]."'");
			$im = $fils[$i];
			$content .= "<div style=\"display: inline-block; padding: 5px; text-align: center; vertical-align: top; height: auto; max-width: 200px;\">";
			$content .= "<a href=\"javascript:{}\" onclick=\"window.open('".$im->image."','".$i."','width=600,height=650');\">";
			$content .= "<img src=\"".$im->image."\" style=\"height: 100px; margin: 3px;\">";
			$content .= "</a>";
			$content .= "<br /><span style=\"font-size: 8pt;\">File Name: ".$im->img_name."</span>";
			$content .= "<br />".@$rr[0]->report_mark;
			if(isset($im->description) && strlen($im->description) > 0){ $content .= "<br /><span style=\"font-size: 9pt;\">".$im->description."</span>"; }
			$content .= "</div>";
			
		}
		echo $content;
	}

	// Supporting functions
	function charConv($str,$from,$to){
		// Converts characters where necessary
		$str = str_replace($from,$to,$str);
		return $str;
	}

	function qry($tbl, $data, $ky, $fld){
		// Suitable to return ONE field of the db table, where the field name and data to search for are provided.
		// $tbl = the table to search in.		
		// $data = the data string to search for.
		// $ky = the name of the field to search in.
		// $fld = Field name to return value of.
		// $ret = Returned value of the function.
		$this->db_conn();
		$sql_com = "SELECT * FROM `".$tbl."` WHERE `".$ky."` = '".$data."' LIMIT 1";
		$dosql_com = $this->Generic_model->qry($sql_com); //mysqli_query($sql_com);
		$ret = "";
		//while($resultcom = mysqli_fetch_array($dosql_com)){
			$res = (array)$dosql_com[0];
			$ret = $res[$fld]; //$resultcom[$fld];		
		//}
		
		return $ret; //Value to return.
	}	
	
}
