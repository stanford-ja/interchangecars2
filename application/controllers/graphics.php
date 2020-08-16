<?php
class Graphics extends CI_Controller {

	var $whr = "";
	var $content = array('html' => '','phtml' => '', 'rhtml' => '', 'thtml' => '', 'ahtml' => '', 'shtml' => "", 'mhtml' => "", 'ghtml' => "");
	var $waybills = array();
	var $porders = array();
	var $myCars = array();
	var $horiz_loc = 120;
	var $my_rr_ids = array();
	
	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!

		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->helper("file");
		$this->load->library('upload');
		$this->load->library('mricf');
		$this->load->library('dates_times');
		$this->load->library('email');

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Graphics";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		$this->arr['affil'] = array();
		
		// Security
		//echo "rr_sess = ".$this->input->cookie('rr_sess', TRUE);
		if($this->input->cookie('rr_sess')){$this->arr['rr_sess'] = $this->input->cookie('rr_sess', TRUE);}
		else{
			echo "You must be logged in to use this feature.";
			exit();
			//header("Location:".WEB_ROOT."/login");
		}
		
		// Load generic model for custom queries
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
		
		// Railroad array set up
		$this->load->model('Railroad_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->arr['myRR'] = $this->Railroad_model->get_single($this->arr['rr_sess']);

		$rrArrTmp = $this->mricf->rrFullArr();
		$rrArrTmp_kys = array_keys($rrArrTmp);
		for($r=0;$r<count(array_keys($rrArrTmp_kys));$r++){$this->arr[$rrArrTmp_kys[$r]] = $rrArrTmp[$rrArrTmp_kys[$r]];}
		//echo "<pre>"; print_r($this->arr); echo "</pre>"; exit();
		/*		
		$arRR = (array)$this->Railroad_model->get_all(); //Active();
		$this->arr['allRR'] = array();
		$this->arr['allRRKys'] = array();
		for($i=0;$i<count($arRR);$i++){
			$this->arr['allRR'][$arRR[$i]->id] = $arRR[$i]; // Used to get data for specific RR , id field is key for array.
			$this->arr['allRRKys'][] = $arRR[$i]->id; // Used to order by Report Mark.
			$this->arr['allRRRepMark'][$arRR[$i]->report_mark] = $arRR[$i]->id;
		}
		*/
		//echo "<pre>"; print_r($this->arr['allRR']); echo "</pre>";		

		// File Path variables
		$this->filePath = DOC_ROOT.'/waybill_images/';
		$this->webPath = str_replace(DOC_ROOT,WEB_ROOT,$this->filePath);

		// File Upload COnfig array
		$this->uconfig = array();
		$this->uconfig['upload_path'] = $this->filePath;
		$this->uconfig['overwrite'] = true;
		$this->uconfig['allowed_types'] = 'jpg'; //'gif|jpg|png';
		//$this->uconfig['max_size']	= '100';
		//$this->uconfig['max_width'] = '1024';
		//$this->uconfig['max_height'] = '768';
	}

	public function index(){
		exit();
	}
	
	public function waybill($id=0){
		// Form to upload graphic for logged-in railroad for waybill.id = $id.
		//	Generate list of waybills for rr logged in as
		$dat = (array)$this->Generic_model->qry("SELECT `waybill_num`,`indust_origin_name`, `indust_dest_name` FROM `ichange_waybill` WHERE `id` = '".$id."'");
		$content['id'] = $id;
		$content['type'] = "waybill";
		$content['form'] = form_open_multipart("../graphics/waybillUpload");
		$content2['title'] = "Waybill Image Upload";
		$content2['data'][0] = (array)$dat[0];
		$content2['field_names'] = array("Waybill #","Origin Industry","Destination Industry");
		
		$content3['html'] = "<div style=\"text-align: center; color: #555; padding: 10px; margin: 3px;  background-color: antiquewhite;\">";
		//$content3['html'] .= "Max file size: ".@$this->uconfig['max_size']."kb, ".@$this->uconfig['max_width']."px X ".@$this->uconfig['max_height']."px,<br />Allowed File Type: ".$this->uconfig['allowed_types']."<br />Max Description Length: 80 characters.<br />";
		//$content3['html'] .= @$this->uconfig['max_width']."px X ".@$this->uconfig['max_height']."px,";
		$content3['html'] .= "Allowed File Type: ".$this->uconfig['allowed_types']."<br />Max Description Length: 80 characters.<br />";
		$content3['html'] .= "One image per railroad per waybill is allowed. If you upload a new image and one already exists for your railroad it will replace the previous one. If the replacement image does not display after the upload, <a href=\"javascript:{}\" onclick=\"window.location.reload();\">Click Here</a>.<br />";

		/*
		$fils = get_filenames($this->filePath);
		//echo "<pre>"; print_r($fils); echo "</pre>";
		for($i=0;$i<count($fils);$i++){
			if(strpos("Z".$fils[$i],$id."-") > 0){
				$content3['html'] .= "<a href=\"javascript:{}\" onclick=\"window.open('".$this->webPath.$fils[$i]."','".$i."','width=500,height=500');\">";
				$content3['html'] .= "<img src=\"".$this->webPath.$fils[$i]."\" style=\"height: 100px; margin: 3px;\">";
				$content3['html'] .= "</a>";
			}
		}
		*/
		
		// Get image from db table
		$sql = "SELECT `image` FROM `ichange_wb_img` WHERE `img_name` LIKE '".$id."-%'";
		$dat = (array)$this->Generic_model->qry($sql);
		for($d=0;$d<count($dat);$d++){
			$content3['html'] .= "<img src=\"".$dat[$d]->image."\" style=\"height: 100px; margin: 3px;\">";
			$content3['html'] .= "<br />";			
			$content3['html'] .= "<a href=\"javascript:{}\" onclick=\"window.open('".$dat[$d]->image."','".$d."','width=500,height=500');\">View Full Size</a><b />";
			$content3['html'] .= "<br />";			
		}
		
		$content3['html'] .= "</div>";

		// Load views
		$this->load->view('header', $this->arr);
		//$this->load->view('menu', $this->arr);
		$this->load->view("view",$content2);
		$this->load->view("html",$content3);
		$this->load->view('graphic', $content);
		$this->load->view('footer');
	}
	
	public function waybillUpload(){
		// Saves uploaded file selected in waybill() method.
		$p = $_POST;
		if(isset($p['description']) && strlen($p['description']) > 80){ $p['description'] = substr($p['description'],0,80); }
		$p['rr_sess'] = $this->arr['rr_sess'];
		$this->uconfig['file_name'] = $p['id']."-".$p['rr_sess'].".jpg"; // - REPLACED 2020-08-13
		$image_base64 = base64_encode(file_get_contents($_FILES['user_file']['tmp_name']) );
		$image = 'data:'.$_FILES['user_file']['type'].';base64,'.$image_base64;
		//$img_thumb = 'data:'.$_FILES['user_file']['type'].';base64,'.$this->createThumbnail($_FILES['user_file']['tmp_name'],"");

		//$this->load->library('upload', $config);
		$this->upload->initialize($this->uconfig);

		if(!$this->upload->do_upload("user_file")){
			echo "There was a problem uploading the file!<br /><a href=\"".WEB_ROOT."/graphics/waybill/".$p['id']."\">Try Again!</a><br />";
			echo $this->upload->display_errors()."<br />";
			//echo "<pre>"; print_r($config); "</pre>";
			//echo "<pre>"; print_r($this->upload->data()); echo "</pre>";
			//exit();
		}else{
			//$imagick = new \Imagick(realpath(DOC_ROOT."/waybill_images/".$this->uconfig['file_name']));
			//$imagick->resizeImage($width, $height, $filterType, $blur, $bestFit);
			//$imagick->resizeImage( 200, 200,  $imagick::FILTER_LANCZOS, 1, TRUE);
			$ex = "convert ".DOC_ROOT."/waybill_images/".$this->uconfig['file_name']." -resize 120 ".DOC_ROOT."/waybill_images/".$this->uconfig['file_name'];
			//echo $ex; exit();
			shell_exec($ex);
			$image_base64 = base64_encode(file_get_contents(DOC_ROOT."/waybill_images/".$this->uconfig['file_name']) );
			$img_thumb = 'data:jpeg;base64,'.$image_base64;
			unlink(DOC_ROOT."/waybill_images/".$this->uconfig['file_name']);
			/* REPLACED WITH BELOW = 2020-08-13
			$this->Generic_model->change("DELETE FROM `ichange_wb_img` WHERE `img_name` = '".$this->uconfig['file_name']."'");
			if(strlen($p['description']) > 0){
				$this->Generic_model->change("INSERT INTO `ichange_wb_img` SET `added` = '".date('U')."', `img_name` = '".$this->uconfig['file_name']."', `description` = '".str_replace("'","",$p['description'])."'");
			} */
			$this->Generic_model->change("DELETE FROM `ichange_wb_img` WHERE `img_name` = '".$this->uconfig['file_name']."'");
			$sql = "INSERT INTO `ichange_wb_img` SET 
				`added` = '".date('U')."', 
				`img_name` = '".$this->uconfig['file_name']."', 
				`description` = '".str_replace("'","",@$p['description'])."', 
				`image` = '".$image."', 
				`img_thumb` = '".$img_thumb."'";
			$this->Generic_model->change($sql);
			header("Location:".WEB_ROOT.INDEX_PAGE."/graphics/waybill/".$p['id']);
			//$data = array('upload_data' => $this->upload->data());
			//$this->load->view('upload_success', $data);
		}
	}
	
	public function desc($wb=0,$refer=''){
		// Change only the description for a photo
		// $wb = Waybill ID.
		// $refer = referrer URL relative to WEB_ROOT."/" with '/' replace by '.' (eg, waybill/edit becomes waybill.edit)
		$img = $wb."-".$this->arr['rr_sess'].".jpg";
		$im = (array)$this->Generic_model->qry("SELECT * FROM `ichange_wb_img` WHERE `img_name` = '".$img."'");
		$content['desc_form'] = 1; // Used to test in graphics view which elements to display
		$content['form'] = form_open_multipart("../graphics/descSave");
		$content['referrer'] = $refer;
		$content['img_name'] = $img; //$img; - CHANGED - 2020-08-13
		$content['image'] = $im[0]->image; //$img; - CHANGED - 2020-08-13
		$content['description'] = $im[0]->description;
		$content2['html'] = "<h2>Change Image Description</h2>";

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view("html",$content2);
		$this->load->view("graphic",$content);
		$this->load->view('footer');
	}
	
	public function descSave(){
		// SAve method for 'description for a photo' form
		$p = $_POST;
		// Process data
		/* REPLACED WITH BELOW - 2020-08-13
		$this->Generic_model->change("DELETE FROM `ichange_wb_img` WHERE `img_name` = '".$p['img_name']."'");
		$sql = "INSERT INTO `ichange_wb_img` SET `description` = '".str_replace("'","",$p['description'])."', `img_name` = '".$p['img_name']."', `added` = '".date('U')."'";
		*/
		$sql = "UPDATE `ichange_wb_img` SET `description` = '".str_replace("'","",$p['description'])."', `added` = '".date('U')."' WHERE `img_name` = '".$p['img_name']."'";
		$this->Generic_model->change($sql);
		
		// Redirest or display message
		if(isset($p['referrer'])){ header("Location:".WEB_ROOT.INDEX_PAGE."/".str_replace(".","/",$p['referrer'])); }else{ 
			$content['html'] = "Image description updated, you can now close this window.";
			$this->load->view('header', $this->arr);
			$this->load->view("html",$content);
			$this->load->view('footer');
		}
	}
	
	function car($car,$refer='cars'){
		// Car Upload form.
		// $car = Car ID.
		// $refer = referrer URL relative to WEB_ROOT."/" with '/' replace by '.' (eg, waybill/edit becomes waybill.edit)
		$content['car_form'] = 1; // Used to test in graphics view which elements to display
		$content['car'] = $car;
		$content['form'] = form_open_multipart("../graphics/carUpload");
		$content['referrer'] = $refer;
		$content['car_record'] = (array)$this->Generic_model->qry("SELECT * FROM `ichange_cars` WHERE `id` = '".$car."'");
		$content['car_num'] = str_replace("&","",$content['car_record'][0]->car_num);
		$content2['html'] = "<h2>Upload Car Image for ".$content['car_record'][0]->car_num."</h2>";
		$content2['html'] .= "<a href=\"".WEB_ROOT."/".str_replace(".","/",$refer)."\">Cancel</a>";

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view("html",$content2);
		$this->load->view("graphic",$content);
		$this->load->view('footer');
	}
	
	function carUpload(){
		// Car upload save method
		$p = $_POST;
		$car_num = str_replace(array("&","_","-"," "),"",$p['car_num']);
		$this->filePath = DOC_ROOT.'/car_images/';
		$this->webPath = str_replace(DOC_ROOT,WEB_ROOT,$this->filePath);
		$this->uconfig['upload_path'] = $this->filePath;
		$this->uconfig['file_name'] = $car_num.".jpg";
		$this->uconfig['max_size']	= '30';
		$this->uconfig['max_width'] = '150';
		$this->uconfig['max_height'] = '100';

		//$this->load->library('upload', $config);
		$this->upload->initialize($this->uconfig);

		if(!$this->upload->do_upload("user_file")){
			echo "There was a problem uploading the file!<br /><a href=\"".WEB_ROOT."/graphics/car/".$p['id']."\">Try Again!</a><br />";
			echo $this->upload->display_errors()."<br />";
			//echo "<pre>"; print_r($config); "</pre>";
			//echo "<pre>"; print_r($this->upload->data()); echo "</pre>";
			exit();
		}

		// Redirest or display message
		if(isset($p['referrer'])){ header("Location:".WEB_ROOT."/".str_replace(".","/",$p['referrer'])); }else{ 
			$content['html'] = "Image uploaded, you can now close this window.";
			$this->load->view('header', $this->arr);
			$this->load->view("html",$content);
			$this->load->view('footer');
		}
	}
	
	public function wbviewall(){
		// Display all waybill images as thumbnails
		
		$fils = (array)$this->Generic_model->qry("SELECT `img_name` FROM `ichange_wb_img` WHERE LENGTH(`image`) > 0 ORDER BY `img_name`"); //get_filenames($this->filePath); - REPLACED - 2020-08-13
		//rsort($fils);
		//echo "<pre>"; print_r($fils); echo "</pre>";
		$content['html'] = "<h2>All Waybill Images</h2>Click an image to view it larger in a pop-up window.<br /><br />";
		for($i=0;$i<count($fils);$i++){
			$fils[$i] = $fils[$i]->img_name;
			$tmp = explode("-",str_replace(".jpg","",$fils[$i]));
			//echo "<pre>"; print_r($tmp); echo "</pre>";
			$wb = (array)$this->Generic_model->qry("SELECT * FROM `ichange_waybill` WHERE `id` = '".$tmp[0]."'");
			$rr = (array)$this->Generic_model->qry("SELECT `report_mark` FROM `ichange_rr` WHERE `id` = '".$tmp[1]."'");
			$im = (array)$this->Generic_model->qry("SELECT * FROM `ichange_wb_img` WHERE `img_name` = '".$fils[$i]."'");
			//echo "<pre>"; print_r($wb); echo "</pre>";
			$content['html'] .= "<div style=\"display: inline-block; padding: 5px; text-align: center; vertical-align: top; height: auto; max-width: 200px;\">";
			/* REPLACED BY BELOW - 2020-08-13
			$content['html'] .= "<a href=\"javascript:{}\" onclick=\"window.open('".WEB_ROOT."/graphics/wbview/".str_replace(".jpg","",$fils[$i])."','".$i."','width=600,height=650');\">";
			$content['html'] .= "<img src=\"".$this->webPath.$fils[$i]."\" style=\"height: 100px; margin: 3px;\">";
			*/
			$content['html'] .= "<a href=\"javascript:{}\" onclick=\"window.open('".WEB_ROOT.INDEX_PAGE."/graphics/wbview/".str_replace(".jpg","",$fils[$i])."','".$i."','width=600,height=650');\">";
			$content['html'] .= "<img src=\"".$im[0]->image."\" style=\"height: 100px; margin: 3px;\">";
			$content['html'] .= "</a>";
			$content['html'] .= "<br /><span style=\"font-size: 8pt;\">File Name: ".$im[0]->img_name."</span>";
			$content['html'] .= "<br />Uploaded by ".@$rr[0]->report_mark;
			if($tmp[1] == $this->arr['rr_sess']){ 
				$content['html'] .= "<br /><a href=\"".WEB_ROOT.INDEX_PAGE."/graphics/desc/".$tmp[0]."/graphics.wbviewall\">Edit</a> "; 
				$content['html'] .= "<a href=\"javascript:{}\" onclick=\"if(confirm('Are you sure you want to delete\\nthis image?')){ window.location = '".WEB_ROOT.INDEX_PAGE."/graphics/wbdel/".$tmp[0]."'; }\">Delete</a>"; 
			}
			if(isset($wb[0]->waybill_num) && strlen($wb[0]->waybill_num) > 0){ $content['html'] .= "<br /><a href=\"".WEB_ROOT.INDEX_PAGE."/waybill/view/".$tmp[0]."\">View WB ".$wb[0]->waybill_num."</a>"; }
			if(isset($im[0]->description) && strlen($im[0]->description) > 0){ $content['html'] .= "<br /><span style=\"font-size: 9pt;\">".$im[0]->description."</span>"; }
			$content['html'] .= "</div>";
		}

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		$this->load->view("html",$content);
		$this->load->view('footer');
	}
	
	public function wbdel($fil='',$where2=''){
		// Delete a waybill image. $fil is the WAYBILL ID. The rr_sess variable is used to complete the filename to delete
		if(strlen($where2) < 1){ $where2 = "graphics.wbviewall"; }
		unlink(DOC_ROOT."/waybill_images/".$fil."-".$this->arr['rr_sess'].".jpg");
		$this->Generic_model->change("DELETE FROM `ichange_wb_img` WHERE `img_name` = '".$fil."-".$this->arr['rr_sess'].".jpg'");
		header("Location:".WEB_ROOT."/".str_replace(".","/",$where2));
	}


	
	public function wbview($img=''){
		// Displays a single image, with info.
		$img .= ".jpg";
		//echo $img;
		$tmp = explode("-",str_replace(".jpg","",$img));
		$wb = (array)$this->Generic_model->qry("SELECT * FROM `ichange_waybill` WHERE `id` = '".$tmp[0]."'");
		$rr = (array)$this->Generic_model->qry("SELECT `report_mark` FROM `ichange_rr` WHERE `id` = '".$tmp[1]."'");
		$im = (array)$this->Generic_model->qry("SELECT * FROM `ichange_wb_img` WHERE `img_name` = '".$img."'");

		$content['html'] = "<h2>View Waybill Image</h2>";
		//$content['html'] .= "<img src=\"".$this->webPath.$img."\" style=\"width: 500px;\" />";
		$content['html'] .= "<img src=\"".$im[0]->image."\" style=\"width: 500px;\" />";
		$content['html'] .= "<br />Uploaded by ".$rr[0]->report_mark;
		if(isset($wb[0]->waybill_num) && strlen($wb[0]->waybill_num) > 0){ $content['html'] .= "<br />WB ".$wb[0]->waybill_num; }
		if(isset($im[0]->description) && strlen($im[0]->description) > 0){ $content['html'] .= "<br /><span style=\"font-size: 9pt;\">".$im[0]->description."</span>"; }
		
		// Load views
		$this->load->view('header', $this->arr);
		//$this->load->view('menu', $this->arr);
		$this->load->view('html', $content);
		$this->load->view('footer');
	}

	public function rrMap(){
		$id=$this->arr['rr_sess'];
		$filename = $this->mricf->rrMap($id);

		$content2['html'] = "<h2>Railroad Map</h2>";
		$m=0;
		if(isset($filename[$m]) && strlen($filename[$m]) > 0){
			if(strpos($filename[$m],".pdf") > 0){
				$content2['html'] .= "The railroad map is in a PDF file. <a href=\"".WEB_ROOT.$filename[$m]."\" target=\"mapPdf".date('U')."\">Click to view PDF</a>";
			}else{
				$content2['html'] .= "<img src=\"".WEB_ROOT.$filename[$m]."\" style=\"width: 500px;\" />";
			}
		}
		//$content['html'] .= "<br />Uploaded by ".$rr[0]->report_mark;

		$content['map_form'] = 1; // Used to test in graphics view which elements to display
		$content['form'] = form_open_multipart("../graphics/rrMapUpload");
		$content['referrer'] = ""; //$refer;
		//$content['img_name'] = $filename[0];
		//$content['description'] = $im[0]->description;

		// Load views
		$this->load->view('header', $this->arr);
		//$this->load->view('menu', $this->arr);
		$this->load->view('html', $content2);
		$this->load->view("graphic",$content);
		$this->load->view('footer');
	}
	
	public function rrMapUpload(){
		$id=$this->arr['rr_sess'];

		$fntmp = $this->mricf->rrMap($id);
		for($m=0;$m<count($fntmp);$m++){
			unlink(DOC_ROOT.$fntmp[$m]);
		}

		$tmp = explode(".",$_FILES['user_file']['name']);
		$ext = strtolower($tmp[intval(count($tmp)-1)]);
		$this->filePath = DOC_ROOT.'/map_files/';
		$this->webPath = str_replace(DOC_ROOT,WEB_ROOT,$this->filePath);
		$this->uconfig['upload_path'] = $this->filePath;
		$this->uconfig['file_name'] = $id.".".$ext;
		if(strpos(strtolower(),".") > 0){ ; }
		$this->uconfig['allowed_types'] = 'png|jpg|pdf';
		$this->uconfig['overwrite'] = true;
		$this->uconfig['max_size']	= '300';
		$this->uconfig['max_width'] = '1200';
		$this->uconfig['max_height'] = '1200';

		$this->upload->initialize($this->uconfig);

		if(!$this->upload->do_upload("user_file")){
			echo "There was a problem uploading the file!<br /><a href=\"".WEB_ROOT."/graphics/car/".$p['id']."\">Try Again!</a><br />";
			echo $this->upload->display_errors()."<br />";
			//echo "<pre>"; print_r($config); "</pre>";
			//echo "<pre>"; print_r($this->upload->data()); echo "</pre>";
			exit();
		}

		header("Location:".WEB_ROOT."/graphics/rrMap");
	}
	
	function createThumbnail($url, $filename, $width = 150, $height = true) {
	//function createThumbnail($base64, $width = 120, $height = true) {
		// download and create gd image
		$image = ImageCreateFromString(file_get_contents($url));
		//$image = ImageCreateFromString($base64);

		// calculate resized ratio
		// Note: if $height is set to TRUE then we automatically calculate the height based on the ratio
		$height = $height === true ? (ImageSY($image) * $width / ImageSX($image)) : $height;

		// create image 
		$output = ImageCreateTrueColor($width, $height);
		ImageCopyResampled($output, $image, 0, 0, 0, 0, $width, $height, ImageSX($image), ImageSY($image));

		// save image
		ImageJPEG($output, $filename, 95); 

		// return resized image
		return $output; // if you need to use it
	}

}
