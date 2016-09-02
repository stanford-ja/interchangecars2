<?php
class Map extends CI_Controller {

	var $wbs_cntr = array('usa' => 0, 'aust_nz' => 0, 'canada' => 0, 'other' => 0);
	var $maps = array("usa" => "USA", "aust_nz" => "Aust / NZ", "canada" => "Canada"); // for dropdowns for pins
	var $map_id = "";
	
	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->model('Waybill_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Railroad_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
		
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('mricf');
		$this->load->library("images");
		
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Map";
		$this->arr['rr_sess'] = -1;
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

	}

	/*	
	public function index($wid=""){
		$this->usa($wid);
	}
	*/
		
	public function usa($wid=""){
		$this->states = array(
		"WA" => array('code' => "WA", 'top' => 40, 'left' => 155, 'height' => 20, 'width' => 40), 
		"OR" => array('code' => "OR", 'top' => 130, 'left' => 145, 'height' => 20, 'width' => 40), 
		"ID" => array('code' => "ID", 'top' => 140, 'left' => 230, 'height' => 20, 'width' => 40), 
		"MT" => array('code' => "MT", 'top' => 80, 'left' => 350, 'height' => 20, 'width' => 40), 
		"WY" => array('code' => "WY", 'top' => 210, 'left' => 380, 'height' => 20, 'width' => 40), 
		"ND" => array('code' => "ND", 'top' => 100, 'left' => 520, 'height' => 20, 'width' => 40), 
		"SD" => array('code' => "SD", 'top' => 180, 'left' => 520, 'height' => 20, 'width' => 40), 
		"NE" => array('code' => "NE", 'top' => 265, 'left' => 520, 'height' => 20, 'width' => 40), 
		"MN" => array('code' => "MN", 'top' => 120, 'left' => 630, 'height' => 20, 'width' => 40), 
		"IA" => array('code' => "IA", 'top' => 240, 'left' => 650, 'height' => 20, 'width' => 40), 
		"WI" => array('code' => "WI", 'top' => 180, 'left' => 720, 'height' => 20, 'width' => 40), 
		"IL" => array('code' => "IL", 'top' => 280, 'left' => 740, 'height' => 20, 'width' => 40), 
		"MO" => array('code' => "MO", 'top' => 360, 'left' => 680, 'height' => 20, 'width' => 40), 
		"AR" => array('code' => "AR", 'top' => 440, 'left' => 680, 'height' => 20, 'width' => 40), 
		"LA" => array('code' => "LA", 'top' => 550, 'left' => 690, 'height' => 20, 'width' => 40), 
		"KS" => array('code' => "KS", 'top' => 420, 'left' => 570, 'height' => 20, 'width' => 40), 
		"OK" => array('code' => "OK", 'top' => 340, 'left' => 530, 'height' => 20, 'width' => 40), 
		"CO" => array('code' => "CO", 'top' => 320, 'left' => 400, 'height' => 20, 'width' => 40), 
		"UT" => array('code' => "UT", 'top' => 305, 'left' => 280, 'height' => 20, 'width' => 40), 
		"NV" => array('code' => "NV", 'top' => 280, 'left' => 170, 'height' => 20, 'width' => 40), 
		"CA" => array('code' => "CA", 'top' => 345, 'left' => 120, 'height' => 20, 'width' => 40), 
		"AZ" => array('code' => "AZ", 'top' => 435, 'left' => 260, 'height' => 20, 'width' => 40), 
		"NM" => array('code' => "NM", 'top' => 465, 'left' => 360, 'height' => 20, 'width' => 40), 
		"TX" => array('code' => "TX", 'top' => 540, 'left' => 540, 'height' => 20, 'width' => 40), 
		"MS" => array('code' => "MS", 'top' => 510, 'left' => 750, 'height' => 20, 'width' => 40), 
		"IN" => array('code' => "IN", 'top' => 300, 'left' => 800, 'height' => 20, 'width' => 40), 
		"OH" => array('code' => "OH", 'top' => 270, 'left' => 870, 'height' => 20, 'width' => 40), 
		"PA" => array('code' => "PA", 'top' => 240, 'left' => 960, 'height' => 20, 'width' => 40), 
		"MI" => array('code' => "MI", 'top' => 205, 'left' => 820, 'height' => 20, 'width' => 40), 
		"NY" => array('code' => "NY", 'top' => 175, 'left' => 990, 'height' => 20, 'width' => 40), 
		"NJ" => array('code' => "NJ", 'top' => 255, 'left' => 1035, 'height' => 20, 'width' => 40), 
		"MD" => array('code' => "MD", 'top' => 280, 'left' => 995, 'height' => 20, 'width' => 40), 
		"DE" => array('code' => "DE", 'top' => 285, 'left' => 1030, 'height' => 20, 'width' => 40), 
		"RI" => array('code' => "RI", 'top' => 205, 'left' => 1085, 'height' => 20, 'width' => 40), 
		"CT" => array('code' => "CT", 'top' => 195, 'left' => 1050, 'height' => 20, 'width' => 40), 
		"MA" => array('code' => "MA", 'top' => 170, 'left' => 1070, 'height' => 20, 'width' => 40), 
		"NH" => array('code' => "NH", 'top' => 140, 'left' => 1065, 'height' => 20, 'width' => 40), 
		"VT" => array('code' => "VT", 'top' => 120, 'left' => 1040, 'height' => 20, 'width' => 40), 
		"ME" => array('code' => "ME", 'top' => 80, 'left' => 1090, 'height' => 20, 'width' => 40), 
		"WV" => array('code' => "MV", 'top' => 310, 'left' => 910, 'height' => 20, 'width' => 40), 
		"VA" => array('code' => "VA", 'top' => 315, 'left' => 970, 'height' => 20, 'width' => 40), 
		"KY" => array('code' => "KY", 'top' => 350, 'left' => 830, 'height' => 20, 'width' => 40), 
		"TN" => array('code' => "TN", 'top' => 400, 'left' => 810, 'height' => 20, 'width' => 40), 
		"NC" => array('code' => "NC", 'top' => 390, 'left' => 980, 'height' => 20, 'width' => 40), 
		"SC" => array('code' => "SC", 'top' => 434, 'left' => 925, 'height' => 20, 'width' => 40), 
		"GA" => array('code' => "GA", 'top' => 470, 'left' => 880, 'height' => 20, 'width' => 40), 
		"AL" => array('code' => "AL", 'top' => 460, 'left' => 800, 'height' => 20, 'width' => 40), 
		"FL" => array('code' => "FL", 'top' => 560, 'left' => 920, 'height' => 20, 'width' => 40), 
		"HI" => array('code' => "HI", 'top' => 460, 'left' => 60, 'height' => 20, 'width' => 40), 
		"AK" => array('code' => "AK", 'top' => 600, 'left' => 200, 'height' => 20, 'width' => 40)
		);
		$this->wid = $wid;
		$this->use_map = "united-states-map-with-states-1200.png";
		$this->map_id = "usa";
		$this->wbCollector();
	}
	
	function aust_nz($wid=""){
		$this->states = array(
		"TAS-AUS" => array('code' => "TAS", 'top' => 680, 'left' => 590, 'height' => 20, 'width' => 40), 
		"VIC-AUS" => array('code' => "VIC", 'top' => 540, 'left' => 570, 'height' => 20, 'width' => 40), 
		"QLD-AUS" => array('code' => "QLD", 'top' => 250, 'left' => 600, 'height' => 20, 'width' => 40), 
		"NSW-AUS" => array('code' => "NSW", 'top' => 440, 'left' => 630, 'height' => 20, 'width' => 40), 
		"SA-AUS" => array('code' => "SA", 'top' => 390, 'left' => 360, 'height' => 20, 'width' => 40), 
		"WA-AUS" => array('code' => "WA", 'top' => 300, 'left' => 170, 'height' => 20, 'width' => 40), 
		"NT-AUS" => array('code' => "NT", 'top' => 200, 'left' => 360, 'height' => 20, 'width' => 40), 
		"NZ" => array('code' => "NZ", 'top' => 650, 'left' => 1060, 'height' => 20, 'width' => 40) 
		);
		$this->wid = $wid;
		$this->use_map = "australia-nz-map-with-states-1200.png";
		$this->map_id = "aust_nz";
		$this->wbCollector();
	}
	
	function canada($wid=""){
		$this->states = array(
		"ON-CAN" => array('code' => "ON", 'top' => 750, 'left' => 600, 'height' => 20, 'width' => 40), 
		"QC-CAN" => array('code' => "QC", 'top' => 700, 'left' => 830, 'height' => 20, 'width' => 40), 
		"NS-CAN" => array('code' => "NS", 'top' => 780, 'left' => 1020, 'height' => 20, 'width' => 40), 
		"NB-CAN" => array('code' => "NB", 'top' => 780, 'left' => 960, 'height' => 20, 'width' => 40), 
		"MB-CAN" => array('code' => "MB", 'top' => 660, 'left' => 470, 'height' => 20, 'width' => 40), 
		"BC-CAN" => array('code' => "BC", 'top' => 600, 'left' => 120, 'height' => 20, 'width' => 40), 
		"PE-CAN" => array('code' => "PE", 'top' => 750, 'left' => 1000, 'height' => 20, 'width' => 40), 
		"SK-CAN" => array('code' => "SK", 'top' => 660, 'left' => 360, 'height' => 20, 'width' => 40), 
		"YT-CAN" => array('code' => "YT", 'top' => 340, 'left' => 110, 'height' => 20, 'width' => 40), 
		"NT-CAN" => array('code' => "NT", 'top' => 380, 'left' => 240, 'height' => 20, 'width' => 40), 
		"AB-CAN" => array('code' => "AB", 'top' => 640, 'left' => 250, 'height' => 20, 'width' => 40), 
		"NU-CAN" => array('code' => "NU", 'top' => 450, 'left' => 470, 'height' => 20, 'width' => 40), 
		"NL-CAN" => array('code' => "NL", 'top' => 600, 'left' => 940, 'height' => 20, 'width' => 40)
		);
		$this->wid = $wid;
		$this->use_map = "canada-map-with-states-1200.png";
		$this->map_id = "canada";
		$this->wbCollector();
	}
	
	function wbCollector(){
		$this->pins();
		$this->pin_form(0);
		$this->route_form(0);
		
		$rrArr = (array)$this->mricf->rrFullArr();
		$this->wbs = array();
		$sql = "SELECT `progress` FROM `ichange_waybill` WHERE `status` != 'CLOSED'";
		$wb = (array)$this->Generic_model->qry($sql);
		for($i=0;$i<count($wb);$i++){
			$prog = json_decode($wb[$i]->progress, true);
			$progCntr = count($prog)-1;
			$date = $prog[$progCntr]['date'];
			$text = $prog[$progCntr]['text'];
			$map_location = str_replace(", ",",",$prog[$progCntr]['map_location']);
			$map_location = $this->mricf->strip_spec($map_location);
			$map_location = trim($map_location);
			$map_location_b = $map_location;

			$ml = explode(",",$map_location);
			$ml_cntr = count($ml)-1;
			if(strlen($ml[$ml_cntr]) < 1){$ml[$ml_cntr] = "N/A";}
			if(isset($this->wbs[$ml[$ml_cntr]])){$v = count($this->wbs[$ml[$ml_cntr]]) + 1;}else{$v=1;}
			$this->wbs[$ml[$ml_cntr]] = array('cntr' => $v);
			if(strpos($ml[$ml_cntr],"-CAN") > 0){$this->wbs['canada']++;}
			elseif(strpos($ml[$ml_cntr],"-AUS") > 0 || strpos($ml[$ml_cntr],"NZ") > 0){$this->wbs_cntr['aust_nz']++;}
			elseif($ml[$ml_cntr] == "N/A"){$this->wbs_cntr['other']++;}
			else{$this->wbs_cntr['usa']++;}
		}
		
		//echo "<pre>"; print_r($this->wbs); echo "</pre>";

		/*
			//$g[] = get_car_image($fld9, $fld10);
			//$g[] = substr($fld10,0,1).".gif";
			
			//	$g = "<img src=\"images/".substr($fld10, 0, 1).".gif\" border=\"0\" title=\"".$fld10."\" />";
			//	if(file_exists("car_images/".st_cv($fld9,".jpg","r"))){
			//		$g = "&nbsp;&nbsp;<img style=\"max-width: 100px\" src=\"car_images/".st_cv($fld9,".jpg","r")."\" border=\"0\" title=\"".$fld10."\" />";
			//	}	
		*/
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		//$this->load->view('home0', $this->arr);
		$this->load->view("map",$this);
		$this->load->view('footer');
	}

	// START NEW OpenMAP API Map
	function openMap(){
		// Set array for states / provinces & country
		$content['states_arr'] = array(
			"WA,USA" => array('lat' => 47.2868352, 'lon' => -120.2126139), 
			"OR,USA" => array('lat' => 43.9792797, 'lon' => -120.737257), 
			"ID,USA" => array('lat' => 43.6447642, 'lon' => -114.0154071), 
			"MT,USA" => array('lat' => 47.3752671, 'lon' => -109.6387579), 
			"WY,USA" => array('lat' => 43.1700264, 'lon' => -107.5685348), 
			"ND,USA" => array('lat' => 47.6201461, 'lon' => -100.540737), 
			"SD,USA" => array('lat' => 44.6471761, 'lon' => -100.348761), 
			"NE,USA" => array('lat' => 41.7370229, 'lon' => -99.5873816), 
			"MN,USA" => array('lat' => 45.9896587, 'lon' => -94.6113288), 
			"IA,USA" => array('lat' => 41.9216734, 'lon' => -93.3122705), 
			"WI,USA" => array('lat' => 44.4308975, 'lon' => -89.6884637), 
			"IL,USA" => array('lat' => 40.5, 'lon' => -89.2), 
			"MO,USA" => array('lat' => 38.7604815, 'lon' => -92.5617875), 
			"AR,USA" => array('lat' => 35.2048883, 'lon' => -92.4479108), 
			"LA,USA" => array('lat' => 31, 'lon' => -92), 
			"KS,USA" => array('lat' => 38.27312, 'lon' => -98.5821872), 
			"OK,USA" => array('lat' => 34.9550817, 'lon' => -97.2684063), 
			"CO,USA" => array('lat' => 38.7251776, 'lon' => -105.6077167), 
			"UT,USA" => array('lat' => 39.4225192, 'lon' => -111.7143584), 
			"NV,USA" => array('lat' => 39.5158825, 'lon' => -116.8537227), 
			"CA,USA" => array('lat' => 36.7014631, 'lon' => -118.7559974), 
			"AZ,USA" => array('lat' => 34.395342, 'lon' => -111.7632755), 
			"NM,USA" => array('lat' => 34.5708167, 'lon' => -105.993007), 
			"TX,USA" => array('lat' => 31.8160381, 'lon' => -99.5120986), 
			"MS,USA" => array('lat' => 32.9715645, 'lon' => -89.7348497), 
			"IN,USA" => array('lat' => 40.3270127, 'lon' => -86.1746933), 
			"OH,USA" => array('lat' => 40.2253569, 'lon' => -82.6881395), 
			"PA,USA" => array('lat' => 40.9699889, 'lon' => -77.7278831), 
			"MI,USA" => array('lat' => 43.6211955, 'lon' => -84.6824346), 
			"NY,USA" => array('lat' => 43.1561681, 'lon' => -75.8449946), 
			"NJ,USA" => array('lat' => 40.0757384, 'lon' => -74.4041622), 
			"MD,USA" => array('lat' => 39.5162234, 'lon' => -76.9382069), 
			"DE,USA" => array('lat' => 38.6920451, 'lon' => -75.4013315), 
			"RI,USA" => array('lat' => 41.7962409, 'lon' => -71.5992372), 
			"CT,USA" => array('lat' => 41.6500201, 'lon' => -72.7342163), 
			"MA,USA" => array('lat' => 42.3788774, 'lon' => -72.032366), 
			"NH,USA" => array('lat' => 43.4849133, 'lon' => -71.6553992), 
			"VT,USA" => array('lat' => 44.5990718, 'lon' => -72.5002608), 
			"ME,USA" => array('lat' => 45.709097, 'lon' => -68.8590201), 
			"WV,USA" => array('lat' => 38.4758406, 'lon' => -80.8408415), 
			"VA,USA" => array('lat' => 37.1232245, 'lon' => -78.4927721), 
			"KY,USA" => array('lat' => 37.5726028, 'lon' => -85.1551411), 
			"TN,USA" => array('lat' => 35.7730076, 'lon' => -86.2820081), 
			"NC,USA" => array('lat' => 35.6729639, 'lon' => -79.0392919), 
			"SC,USA" => array('lat' => 33.6874388, 'lon' => -80.4363743), 
			"GA,USA" => array('lat' => 32.3293809, 'lon' => -83.1137366), 
			"AL,USA" => array('lat' => 33.2588817, 'lon' => -86.8295337), 
			"FL,USA" => array('lat' => 27.7567667, 'lon' => -81.4639835), 
			"HI,USA" => array('lat' => 21.2160437, 'lon' => -157.975203), 
			"AK,USA" => array('lat' => 64.4459613, 'lon' => -149.680909), 
			 
			"QC,CANADA" => array('lat' => 54, 'lon' => -72), 
			"NS,CANADA" => array('lat' => 45.0000002, 'lon' => -62.9999999), 
			"NB,CANADA" => array('lat' => 46.5, 'lon' => -66.75), 
			"MB,CANADA" => array('lat' => 55.0000001, 'lon' => -97.0000001), 
			"BC,CANADA" => array('lat' => 55, 'lon' => -125), 
			"PE,CANADA" => array('lat' => 46.25, 'lon' => -63), 
			"SK,CANADA" => array('lat' => 54, 'lon' => -106), 
			"YT,CANADA" => array('lat' => 63.0000001, 'lon' => -136.0000001), 
			"NT,CANADA" => array('lat' => 63, 'lon' => -119), 
			"AB,CANADA" => array('lat' => 55, 'lon' => -114.9999999), 
			"NU,CANADA" => array('lat' => 70.0000073 , 'lon' => -90), 
			"NL,CANADA" => array('lat' => 52.0000002, 'lon' => -56.0000001),
			"ON,CANADA" => array('lat' => 50.0000002, 'lon' => -86.0000001),
			
			"TAS,AUSTRALIA" => array('lat' => -42.035067, 'lon' => 146.6366887), 
			"VIC,AUSTRALIA" => array('lat' => -36.5986096, 'lon' => 144.6780052), 
			"QLD,AUSTRALIA" => array('lat' => -21.9182856, 'lon' => 144.4588889),
			"NSW,AUSTRALIA" => array('lat' => -31.8759835, 'lon' => 147.2869493), 
			"SA,AUSTRALIA" => array('lat' => -30.5343665, 'lon' => 135.6301212), 
			"WA,AUSTRALIA" => array('lat' => -25.2303005, 'lon' => 121.0187246),
			"NT,AUSTRALIA" => array('lat' => -19.8516101, 'lon' => 133.2303375), 
			"ACT,AUSTRALIA" => array('lat' => -35.4021016, 'lon' => 148.9464218), 
			"NZ" => array('lat' => -41, 'lon' => 174)
		);
		$content['states'] = array_keys($content['states_arr']);
		
		
		$sql = "SELECT `id`,`cars`,`train_id`,`status`,`waybill_num`,`progress` FROM `ichange_waybill` WHERE `status` != 'CLOSED'";
		$wb = (array)$this->Generic_model->qry($sql);
		$content['locations'] = array(); // Array of town locations
		$content['states_fnd'] = array(); // Array of states / countries found
		$content['waybills'] = array(); // Array of waybill ids 
		
		$rr = $this->arr['rr_sess']; if($rr < 1){$rr = 0;}else{
			$mfn = DOC_ROOT."/map_files/textfile-".$rr.".txt";
			unlink($mfn);
			$mapfil = fopen($mfn,'a'); // Create map data file for railroad
		}

		$map_file_content = array();
		$poi_file_content = array();
		
		for($i=0;$i<count($wb);$i++){
			$sql = "SELECT * FROM ichange_progress WHERE `waybill_num` = '".$wb[$i]->waybill_num."' ORDER BY `date` DESC, `time` DESC LIMIT 1";
			$wb_prog = (array)$this->Generic_model->qry($sql);
			$prog = (array)$wb_prog[0]; //json_decode($wb[$i]->progress, TRUE);
			$progCntr = count($prog)-1;
			$map_location = str_replace(", ",",",$prog[$progCntr]['map_location']);
			$map_location = $this->mricf->strip_spec($map_location);
			$map_location = trim($map_location);
			//$map_location_b = $map_location;
			$map_arr = explode(",",$map_location); // town, state[, country]

			$cars = json_decode($wb[$i]->cars,TRUE);
			$car = $cars[0];
			
			if(strlen($map_arr[0]) > 0 && isset($map_arr[1])){
				if(!isset($map_arr[2])){$map_arr[2] = "USA";}
				if(in_array($map_arr[1].",".$map_arr[2],$content['states'])){
					if(!in_array($map_arr[1].",".$map_arr[2],$content['states_fnd'])){ $content['states_fnd'][] = $map_arr[1].",".$map_arr[2]; }
					//$map_assoc_arr = array('town'=>$map_arr[0]);
					//if(isset($map_arr[2])){ $map_assoc_arr['country'] = $map_arr[2]; }else{ $map_assoc_arr['country'] = "USA"; }
					if(!isset($content['locations'][$map_arr[1].",".$map_arr[2]])){ $content['locations'][$map_arr[1].",".$map_arr[2]] = array(); }
					if(!in_array($map_arr[0].",".$map_arr[1].",".$map_arr[2],$content['locations'][$map_arr[1].",".$map_arr[2]])){ $content['locations'][$map_arr[1].",".$map_arr[2]][] = $map_arr[0].",".$map_arr[1].",".$map_arr[2]; }
					$content['waybills'][$map_arr[0].",".$map_arr[1].",".$map_arr[2]][] = array('id' => $wb[$i]->id, 'wb_num' => $wb[$i]->waybill_num);

					if(!isset($map_file_content[$content['states'][$map_arr[1].",".$map_arr[2]]])){ $map_file_content[$content['states'][$map_arr[1].",".$map_arr[2]]] = ""; }
					$pro_img = "steamtrain"; $alt = $wb[$i]->waybill_num." In Transit";
					if($wb[$i]->status == "LOADING"){ $pro_img = "factory"; $alt = "Loading ".$wb[$i]->waybill_num; }
					if($wb[$i]->status == "UNLOADING"){ $pro_img = "departmentstore"; $alt = "Unloading ".$wb[$i]->waybill_num; }
					if($wb[$i]->train_id == "AUTO TRAIN"){ $pro_img = "train"; $alt = $wb[$i]->waybill_num. " in Auto Train "; }
					
					$map_file_content[$map_arr[1].",".$map_arr[2]] .= "<tr><td><img src=\"".IMAGE_ROOT."/".$pro_img.".png\" alt=\"".$alt."\" title=\"".$alt."\" /></td><td class=\"small_txt\">".$map_arr[0]."</td><td>".$this->mricf->get_car_image($car['NUM'],$car['AAR'])."</td></tr>";
				}
			}
		}

		// Start POIs [Point Of Interest]
		$sql = "SELECT * FROM `ichange_locations` WHERE LENGTH(`latitude`) > 0 AND LENGTH(`longitude`) > 0";
		$poi = (array)$this->Generic_model->qry($sql);
		//echo "<pre>"; print_r($poi); exit();
		$pro_img = "information";
		for($po=0;$po<count($poi);$po++){
			$tmp = explode(",",$poi[$po]->real_location);
			if(strlen($poi[$po]->fictional_location) > 0){$tmp = explode(",",$poi[$po]->fictional_location);}
			$label = $tmp[0];
			if(isset($tmp[1])){ $label .= ",".$tmp[1]; }
			$poi_file_content[$poi[$po]->real_location] = $poi[$po]->latitude.",".$poi[$po]->longitude.chr(9)."Place Of Interest".chr(9)."<div class=\"small_txt\">".$label."</div>".chr(9).IMAGE_ROOT."/".$pro_img.".png".chr(9)."18,18".chr(9)."0,-18";
		}
		//echo "<pre>"; print_r($poi_file_content); exit();
		// Start POIs [Point Of Interest]

		// start map popup file creation
		/*
point	title	description	icon
10,20	my orange title	my orange description	
2,4	my aqua title	my aqua description	
42,-71	my purple title	my purple description<br/>is great.	http://www.openlayers.org/api/img/zoom-world-mini.png

OTHER COLUMNS AVAILABLE: iconSize [syntax: xx,yy]	iconOffset	popupSize [syntax: xx,yy]
		*/
		if($rr > 0){
			fwrite($mapfil,"point".chr(9)."title".chr(9)."description".chr(9)."icon".chr(9)."iconSize".chr(9)."iconOffset\n");

			$poi_kys = array_keys($poi_file_content);
			//echo ""; print_r($poi_kys);
			for($po=0;$po<count($poi_kys);$po++){
				$txt = $poi_file_content[$poi_kys[$po]]."\n";
				//echo $txt."<br />";
				fwrite ($mapfil, $txt);
			}

			$mfc_kys = array_keys($map_file_content);
			//echo "<pre>"; print_r($mfc_kys); echo "</pre>"; exit();
			for($mf=0;$mf<count($mfc_kys);$mf++){
				if(strlen($mfc_kys[$mf]) > 0){
					$txt = $content['states_arr'][$mfc_kys[$mf]]['lat'].",".$content['states_arr'][$mfc_kys[$mf]]['lon'].chr(9).$mfc_kys[$mf].chr(9)."<table>".$map_file_content[$mfc_kys[$mf]]."</table>".chr(9).IMAGE_ROOT."/steamtrain.png".chr(9)."35,35".chr(9)."-20,-35\n";
					fwrite ($mapfil, $txt);
				}
			}
			
			fclose($mapfil);
		}
		// end map popup file creation



		//echo "<pre>";
		//print_r($content);
		//echo "</pre>";
		//exit();

		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		//$this->load->view('home0', $this->arr);
		//$this->load->view("map",$this);
		//$this->load->view("map_openMap_popup",$content);
		$this->load->view("map_openMap",$content);
		$this->load->view('footer');
	}
	// END NEW OpenMAP API Map
	
	function pins(){
		// Displays pins on relevant map
		$sql = "SELECT * FROM `ichange_pins` WHERE `map` = '".$this->map_id."'";
		$this->pins = (array)$this->Generic_model->qry($sql);
	}
	
	function pin_edit(){
		$sql = "UPDATE";
		if($_POST['id'] == 0){$sql = "INSERT INTO";}
		$sql .= " `ichange_pins` SET `name` = '".$_POST['name']."', `map` = '".$_POST['map']."', `coord1` = '".$_POST['coord1']."', `coord2` = '".$_POST['coord2']."'";
		if($_POST['id'] > 0){$sql .= " WHERE `id` = '".$_POST['id']."'";}
		$this->Generic_model->change($sql);
		header("Location:".$_POST['map']);
	}
	
	function pin_delete($d=0,$m="usa"){
		// Deletes a pin
		$sql = "DELETE FROM `ichange_pins` WHERE `id` = '".$d."'";
		$this->Generic_model->change($sql);
		header("Location:../../".$m);
	}
	
	function pin_form($i=0){
		// Form for adding / editing pins
		$this->pin_form = "<hr />New Pin:<br />";
		if($this->arr['rr_sess'] > 0){
			$this->pin_form = form_open_multipart('../map/pin_edit');
			$this->pin_form .= form_hidden('id', $i);
			$this->pin_form .= "Map:".form_dropdown('map',$this->maps, $this->map_id)."<br />";
			$this->pin_form .= "Pin Name:".form_input('name','','size="10"')."<br />";
			$this->pin_form .= "Horz Coord:".form_input('coord1','','size="4"')."<br />";
			$this->pin_form .= "Vert Coord:".form_input('coord2','','size="4"')."<br />";
			$this->pin_form .= form_submit('submit','Submit');
			$this->pin_form .= form_close();
		}
	}

	function route_edit(){
		$sql = "UPDATE";
		if($_POST['id'] == 0){$sql = "INSERT INTO";}
		$this->map_id = $_POST['map'];
		$sql .= " `ichange_maproutes` SET 
			`map` = '".$_POST['map']."', 
			`coord1a` = '".$_POST['coord1a']."', 
			`coord2a` = '".$_POST['coord2a']."', 
			`coord1b` = '".$_POST['coord1b']."', 
			`coord2b` = '".$_POST['coord2b']."', 
			`route_name` = '".$_POST['route_name']."'";
		if($_POST['id'] > 0){$sql .= " WHERE `id` = '".$_POST['id']."'";}
		$this->Generic_model->change($sql);
		$this->update_route_map();
		header("Location:".$_POST['map']);
	}
	
	function route_delete($d=0,$m="usa"){
		// Deletes a pin
		$sql = "DELETE FROM `ichange_maproutes` WHERE `id` = '".$d."'";
		$this->Generic_model->change($sql);
		$this->update_route_map();
		header("Location:../../".$m);
	}
	
	function route_form($i=0){
		$this->route_form = "<hr />New Route:<br />";
		if($this->arr['rr_sess'] > 0){
			$this->route_form = form_open_multipart('../map/route_edit');
			$this->route_form .= form_hidden('id', $i);
			$this->route_form .= "Map:".form_dropdown('map',$this->maps, $this->map_id)."<br />";
			$this->route_form .= "Route Name:".form_input('route_name','','size="10"')."<br />";
			$this->route_form .= "Horz Coord 1:".form_input('coord1a','','size="4"')."<br />";
			$this->route_form .= "Vert Coord 1:".form_input('coord2a','','size="4"')."<br />";
			$this->route_form .= "Horz Coord 2:".form_input('coord1b','','size="4"')."<br />";
			$this->route_form .= "Vert Coord 2:".form_input('coord2b','','size="4"')."<br />";
			$this->route_form .= form_submit('submit','Submit');
			$this->route_form .= form_close();
		}
	}
	
	function update_route_map(){
		$this->images->create_map_routes($this->map_id);
	}
}
?>
