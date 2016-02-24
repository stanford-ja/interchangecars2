<?php
// ** NOT USED - FAILED ATTEMPT AT USING CI CONTROLLER FOR AJAX CALLS - URI CHARACTERS ON config.php TOO RESTRICTIVE!**
class Ajax extends CI_Controller {
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

	function carUsed($cn){
		$this->db_conn();
		$cn = $this->charConv($cn,".AMP.","&"); // Require to convert .AMP. back to '&'
		$cn = $this->charConv($cn,".SPACE."," ");
		//$q = mysql_query("SELECT `waybill_num` FROM `ichange_waybill` WHERE `cars` LIKE '%\"".$cn."\"%' LIMIT 1");
		$q = $this->Generic_model->qry("SELECT `waybill_num` FROM `ichange_waybill` WHERE `cars` LIKE '%\"".$cn."\"%' LIMIT 1");
		$w = "";
		if(isset($q[0]->waybill_num)){$w = $q[0]->waybill_num;}
		echo $w;
	}

	function selTrain($fld14){
		// Display Train Selected function called by AJAX.
		$this->db_conn();
		$fld14 = $this->charConv($fld14,".AMP.","&"); // Require to convert .AMP. back to '&'
		$fld14 = $this->charConv($fld14,".SPACE."," ");
		//echo $fld14;
		$sql = "SELECT * FROM `ichange_trains` WHERE `train_id` = '".$fld14."' LIMIT 1";
		//$qry = mysql_query($sql);
		$qry = $this->Generic_model->qry($sql);
		$lst = "";
		//while($res = mysql_fetch_array($qry)){
		if($qry[0]){
			$res = (array)$qry[0];
			$lst .= $res['train_desc']."<br />";
			if(strlen($res['origin'].$res['destination']) > 0){$lst .= $res['origin']." to ".$res['destination']."<br />";}
			$lst .= $res['op_notes']."<br />";
			$lst .= "<hr/>"; //Valid AUTO Train Waypoints (click to add a location to the Entry or Exit Location fields):<br />";
			$wps = json_decode($res['auto'], true);
			$wps_kys = @array_keys($wps);
			$opts = "<option value=\"".$res['origin']."\">".$res['origin']." (origin)</option>";
			for($o=0;$o<count($wps_kys);$o++){
				$opts .= "<option value=\"".$wps_kys[$o]."\">".$wps_kys[$o]." (".$wps[$wps_kys[$o]]." days)</option>";
			}
			if(strlen($res['auto']) > 4){
				$lst .= "Entry Waypoint: <select id=\"entry_waypoint\" name=\"entry_waypoint\" />".$opts."</select>&nbsp;";
				$lst .= "Exit Waypoint: <select id=\"exit_waypoint\" name=\"exit_waypoint\" />".$opts."</select>";
			}elseif(intval($res['auto']) > 0){
				$lst .= "Entry Waypoint: <input type=\"text\" id=\"entry_waypoint\" name=\"entry_waypoint\" value=\"\" onchange=\"this.value = this.value.toUpperCase();\" />&nbsp;";
				$lst .= "Exit Waypoint: <input type=\"text\" id=\"exit_waypoint\" name=\"exit_waypoint\" value=\"\" onchange=\"this.value = this.value.toUpperCase(); document.form1.pfld6.value = this.value;\" />";
			}else{
				$lst .= "<input type=\"hidden\" id=\"entry_waypoint\" name=\"entry_waypoint\" value=\"\" />";
				$lst .= "Exit Waypoint: <input type=\"text\" id=\"exit_waypoint\" name=\"exit_waypoint\" value=\"\" onchange=\"this.value = this.value.toUpperCase(); document.form1.pfld6.value = this.value;\" />";
			}
			if(intval($res['auto']) > 0 || strlen($res['auto']) > 4){$lst .= "&nbsp;<input type=\"button\" name=\"calc_route\" value=\"Calc Route\" onclick=\"selRoute();\" />";}
			//$lst .= "<a href=\"javascript:{}\" onclick=\"document.getElementById('exit_waypoint').value = '".$res['destination']."'\">".$res['destination']."</a>, ";
		}
		if(strlen($lst) < 1){
			$lst = "No results found!";
		}else{
			$lst .= "<br /><a href=\"javascript:{}\" onClick=\"document.getElementById('train_disp_span').style.display = 'none';\">[ Close this box ]</a>";
		}
		echo $lst;
	}

	function selRoute($trid,$start,$finish){
		// Generates route for auto train (ichange_trains.train_id = $trid).
		// $start = place were car is put in train. Can be empty if ichange_trains.auto is an integer > 0.
		// $finish = place where car is spotted by train.
		$this->db_conn();
		$trid = $this->charConv($trid,".AMP.","&"); // Require to convert .AMP. back to '&'
		$start = $this->charConv($start,".AMP.","&");
		$finish = $this->charConv($finish,".AMP.","&");

		$trid = $this->charConv($trid,".SPACE."," "); // Require to convert .AMP. back to '&'
		$start = $this->charConv($start,".SPACE."," ");
		$finish = $this->charConv($finish,".SPACE."," ");
		
		$sql = "SELECT `auto`,`origin`,`destination` FROM `ichange_trains` WHERE `train_id` = '".$trid."' LIMIT 1";
		//$qry = mysql_query($sql);
		$qry = $this->Generic_model->qry($sql);
		$res = (array)$qry[0]; //mysql_fetch_array($qry);
		if(intval($res['auto']) > 0){
			// Number of days to complete
			$arr = array($start => 0, $finish => $res['auto']);
		}else{
			// JSON array
			$arr_tmp = @json_decode($res['auto'],TRUE);
			$arr_tmp[$res['origin']] = 0;
			asort($arr_tmp);
			$arr_kys = array_keys($arr_tmp);
			print_r($arr_kys);
			$orig = $arr_tmp[$start]; // day value of entry waybpoint
			$dest = $arr_tmp[$finish]; // day value of exit waypoint
			for($i=0;$i<count($arr_kys);$i++){
				if($arr_tmp[$arr_kys[$i]] > $orig && $arr_tmp[$arr_kys[$i]] < $dest){
					$arr[$arr_kys[$i]] = $arr_tmp[$arr_kys[$i]] - $orig;
				}
			}
			$arr[$start] = 0;
			$arr[$finish] = $dest-$orig;
			asort($arr);
		}
		$json = json_encode($arr);
		echo $json;
	}

	function carsAutoFind($str,$fld){
		// Auto Complete function called by AJAX.
		// Checks whether an entry exists in the database
		// $fld = field to search in
		// $str = string to search for in field $fld
		$this->db_conn();
		$str = $this->charConv($str,".AMP.","&"); // Require to convert .AMP. back to '&'
		$str = $this->charConv($str,".SPACE."," ");
		$str = strtoupper($str);
		$sql = "SELECT * FROM `ichange_cars` WHERE (`".$fld."` LIKE '%".$str."%' OR `car_num` LIKE '%".$str."%' OR `aar_type` LIKE '%".$str."%') AND `rr` = '".$_COOKIE['rr_sess']."' ORDER BY `car_num` LIMIT 25";
		//$qry = mysql_query($sql);
		$qry = $this->Generic_model->qry($sql);
		$lst = "<table style=\"padding: 1px; background-color: transparent; border: none;\">";
		$lst .= "<tr><td class=\"td_title\">Car #</td><td class=\"td_title\">AAR</td><td class=\"td_title\">Lading</td><td class=\"td_title\">Location</td></tr>";
		$cntr=0;
		//while($res = mysql_fetch_array($qry)){
		for($i=0;$i<count($qry);$i++){
			$res = (array)$qry[$i];
			$lad = ""; if(strlen($res['lading']) > 1){$lad = $res['lading'];}
			$loc = ""; if(strlen($res['location']) > 0){$loc = $res['location'];}
			$lst .= "<tr><td class=\"td1\"><a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\"></a>".$res['car_num']."</td><td class=\"td1\">(".$res['aar_type'].")</td><td class=\"td1\">".$lad."</td><td class=\"td1\">".$loc."</td></tr>";
		}
		if(strlen($lst) < 1){
			$lst = "<tr><td>No results found!</td></tr>";
		}
		$lst .= "</table>";
		echo $lst;
	}

	function industAutoComp($str,$tbl,$fld,$sct = NULL,$sr = NULL){
		// Auto Complete function called by AJAX.
		// Checks whether an entry exists in the database
		// $fld = field to search in
		// $tbl = table to search in
		// $str = string to search for in field $fld - can be industry name, location, or commodity
		// $sct = span to display results in
		// $sr = whether a commodity is sent or received by the industry: 0 or null = all, 1=send, 2=receive
		$this->db_conn();
		$str = $this->charConv($str,".AMP.","&"); // Require to convert .AMP. back to '&'
		$str = $this->charConv($str,".SPACE."," ");
		$tbl = "ichange_indust";
		$str = strtoupper($str);
		$sql_sr = " OR `freight_in` LIKE '%".$str."%' OR `freight_out` LIKE '%".$str."%'";
		if($sr == 1){$sql_sr = " OR `freight_out` LIKE '%".$str."%'";}
		if($sr == 2){$sql_sr = " OR `freight_in` LIKE '%".$str."%'";}
		$sql = "SELECT * FROM `".$tbl."` WHERE `indust_name` LIKE '%".$str."%'".$sql_sr." LIMIT 9";
		$qry = $this->Generic_model->qry($sql); //mysql_query($sql);
		$lst = "<a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a><br />";
		//while($res = mysql_fetch_array($qry)){
		for($i=0;$i<count($qry);$i++){
			$res = (array)$qry[$i];
			$rr_mark = $this->qry("ichange_rr", $res['rr'], "id", "report_mark");
			$recs = $res['freight_in'];
			$sends = $res['freight_out'];
		
			$autoCompNote = "";
			if(strlen($str) > 0){
				if(strpos("a".strtoupper($sends),$str) > 0 && $sr == 1){$autoCompNote = "<span style=\"text-decoration: underline;\">Sends: ".$str."</span>";}
				if(strpos("a".strtoupper($recs),$str) > 0 && $sr == 2){$autoCompNote = "<span style=\"text-decoration: underline;\">Receives: ".$str."</span>";}
			}

			$op_info = trim($res['op_info']); $show_indDescDiv = "";
			if(strlen($op_info) > 0){$show_indDescDiv = " document.getElementById('".$sct."_indDescDiv').style.display = 'block';";}		
			$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\" onClick=\"document.getElementById('".$sct."').value = '".trim($res['indust_name'])."'; document.getElementById('".$sct."_indDesc').value = '".trim($res['op_info'])."'; document.getElementById('".$fld."_span').style.display = 'none';".$show_indDescDiv."\">".$res['indust_name']."</a>&nbsp;".$autoCompNote."&nbsp(".$rr_mark.")<br />";
			if(strlen($res['desc']) > 1){$lst .= "<div style=\"display: block; font-size:8pt; max-width: 600px; color: #333\">&nbsp;&nbsp;&nbsp;".$res['desc']."</div>";}
		}
		$sql = "SELECT * FROM `ichange_ind40k` WHERE `industry` LIKE '%".$str."%' OR `city` LIKE '%".$str."%' OR `state` LIKE '%".$str."%' OR `commodity` LIKE '%".$str."%' LIMIT 9";
		$qry = $this->Generic_model->qry($sql); //mysql_query($sql);
		$rows = count($qry); //mysql_num_rows($qry);
		if($rows > 0){$lst .= "===== 40,000 Industry Records Found =====<br />";}
		//while($res = mysql_fetch_array($qry)){
		for($i=0;$i<count($qry);$i++){
			$res = (array)$qry[$i];
			$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\" onClick=\"document.getElementById('".$sct."').value = '".trim(strtoupper($res['industry'].",".$res['city'].",".$res['state']))."'; document.getElementById('".$fld."_span').style.display = 'none';\">".strtoupper($res['industry'].",".$res['city'].",".$res['state'])."</a><span style=\"font-size: 8pt;\"></span><br />";
		}
		if(strlen($lst) < 1){
			$lst = "No results found!";
		}else{
			$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a>";
		}
		echo $lst;
	}

	function autoComp($str,$tbl,$fld,$sct = NULL){
		// Auto Complete function called by AJAX.
		// Checks whether an entry exists in the database
		// $fld = field to search in, or comma separated list where first field is field to search in and subsequent ones display in span.
		// $tbl = table to search in
		// $str = string to search for in field $fld
	    // $sct = field name in form to add value to.
		$this->db_conn();
		$str = $this->charConv($str,".AMP.","&"); // Require to convert .AMP. back to '&'
		$fld = $this->charConv($fld,".COMMA.",",");
		$xtra_flds = "";
		if(strpos($fld,",") > 0){
			$fld_tmp = explode (",",$fld);
			$fld = $fld_tmp[0];
			for($i=1;$i<count($fld_tmp);$i++){$xtra_flds .= ", `".$fld_tmp[$i]."`";}
		}
		$sql = "SELECT DISTINCT `".$fld."`".$xtra_flds." FROM `".$tbl."` WHERE `".$fld."` LIKE '%".$str."%' LIMIT 4";
		$qry = $this->Generic_model->qry($sql); //mysql_query($sql);
		$lst = "<span style=\"float: right;\"><a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a></span>";
		//while($res = mysql_fetch_array($qry)){
		for($i=0;$i<count($qry);$i++){
			$res = (array)$qry[$i];
			//$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\" onClick=\"document.getElementById('".$sct."').value = '".trim($res[$fld])."'; document.getElementById('".$fld."_span').style.display = 'none';\">".$res[$fld]."</a><br />";
			$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\" onClick=\"document.getElementById('".$sct."').value = '".trim($res[$fld])."'; document.getElementById('".$sct."_span').style.display = 'none';\">".$res[$fld]."</a><br />";
			if(isset($fld_tmp)){
				for($o=1;$o<count($fld_tmp);$o++){
					if(strlen($res[$fld_tmp[$o]]) > 0){$lst .= "<span style=\"color: #777; padding-left: 5px;\">".ucwords($fld_tmp[$o]).": ".$res[$fld_tmp[$o]]."</span><br />";}
				}
			}
		}
		if(strlen($lst) < 1){
			$lst = "No results found!";
		}else{
			$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a>";
		}
		echo $lst;
	}

	function allocTrain($wb,$tr){
		// Auto Complete function called by AJAX.
		// Checks whether an entry exists in the database
		// $w = waybill
		// $t = train to allocate to waybill
		$this->db_conn();
		$tr = $this->charConv($tr,".AMP.","&"); // Require to convert .AMP. back to '&'
		$wb = $this->charConv($wb,".AMP.","&"); // Require to convert .AMP. back to '&'
		$sql = "UPDATE `ichange_waybill` SET `train_id` = '".$tr."' WHERE `id` = '".$wb."'";
		$this->Generic_model->change($sql); //mysql_query($sql);
	}

	function allocRR($wb,$tr){
		// Auto Complete function called by AJAX.
		// Checks whether an entry exists in the database
		// $w = waybill
		// $tr = railroad to allocate waybill to
		$this->db_conn();
		$sql = "UPDATE `ichange_waybill` SET `rr_id_handling` = '".$tr."', `train_id` = '' WHERE `id` = '".$wb."'";
		$this->Generic_model->change($sql); //mysql_query($sql);
	}

	function mapDetails($s){
		// Builds map details for waybills, for the state/province code selected in $s
		$this->db_conn();
		$s = strtoupper($s);
		$sql = "SELECT `id`,`status`,`waybill_num`,`progress` FROM `ichange_waybill` WHERE `status` != 'CLOSED' AND `progress` LIKE '%,".$s."%'";
		$qry = $this->Generic_model->qry($sql); //mysql_query($sql);
		$info = "";
		//while($r=mysql_fetch_array($qry)){
		for($i=0;$i<count($qry);$i++){
			$r = (array)$qry[$i];
			$prog = json_decode($r['progress'], true);
			$progCntr = count($prog)-1;
			$date = $prog[$progCntr]['date'];
			$text = $prog[$progCntr]['text'];
			$map_location = str_replace(", ",",",$prog[$progCntr]['map_location']);
			if(strpos($prog[$progCntr]['map_location'],",".$s) > 0){
				//$info .= "<hr />".$r['waybill_num']." - ".$r['date']."<br />".$text." - ".$prog[$progCntr]['map_location'];
				$info .= "<hr /><a href=\"javascript:{}\" onclick=\"wbDetails('".$r['id']."');\">".$r['waybill_num']."</a><br />".$date."<br />".$r['status']."<br />".$prog[$progCntr]['map_location']."&nbsp;<a href=\"../waybill/edit/".$r['id']."\">Edit</a>";
			}
		}
		echo $info;
	}

	function mapWBDetails($s){
		// Builds map details for waybills, for the state/province code selected in $s
		$this->db_conn();
		$s = strtoupper($s);
		$sql = "SELECT * FROM `ichange_waybill` WHERE `id` = '".$s."'";
		$qry = $this->Generic_model->qry($sql); //mysql_query($sql);
		$info = "";
		//while($r=mysql_fetch_array($qry)){
		for($i=0;$i<count($qry);$i++){
			$r = (array)$qry[$i];
			$prog = json_decode($r['progress'], true);
			$progCntr = count($prog)-1;
			$date = $prog[$progCntr]['date'];
			$text = $prog[$progCntr]['text'];
			$map_location = str_replace(", ",",",$prog[$progCntr]['map_location']);
			//$info .= "<hr />".$r['waybill_num']." - ".$r['date']."<br />".$text." - ".$prog[$progCntr]['map_location'];
			$info .= "<span style=\"font-size:12pt; font-weight: bold;\">".$r['waybill_num']."</span><br />".$r['date']."<br />At: ".$prog[$progCntr]['map_location']."<br />";
			$info .= "From ".$r['indust_origin_name']."<br />To: ".$r['indust_dest_name']."<br />";
			$info .= "Routing: ".$r['routing']."<br />";
			$info .= "<hr />Progress:<hr />";
			for($i=0;$i<count($prog);$i++){
				$info .= $prog[$i]['date']." - ".$prog[$i]['text']."<br />";
			}
		}
		echo $info;
	}
	
	function wbImages($id=0){
		// Generates HTML to display images for waybill with waybill.id of $id.
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
	}

	// Supporting functions
	function db_conn(){
		/*
		// LIVE SERVER
		$dbhost="db72d.pair.com";
		$dbusername="jstan_6_w";
		$dbpassword="Js120767";
		$dbname="jstan_general";

		// TESTING
		$LocTst = $_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'];
		if(strpos($LocTst,"www/Applications/") > 0){
			$dbhost="localhost";
			$dbusername="admin";
			$dbpassword="admin";
			$dbname="jstan_general";
		}

		$dbcnx = mysql_connect($dbhost, $dbusername, $dbpassword);
		$seldb = mysql_select_db($dbname);
		*/
	}

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
		$dosql_com = $this->Generic_model->qry($sql_com); //mysql_query($sql_com);
		$ret = "";
		//while($resultcom = mysql_fetch_array($dosql_com)){
			$res = (array)$dosql_com[0];
			$ret = $res[$fld]; //$resultcom[$fld];		
		//}
		
		return $ret; //Value to return.
	}	
	
}
?><?php
// ** NOT USED - FAILED ATTEMPT AT USING CI CONTROLLER FOR AJAX CALLS - URI CHARACTERS ON config.php TOO RESTRICTIVE!**
class Ajax extends CI_Controller {
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

	function carUsed($cn){
		$this->db_conn();
		$cn = $this->charConv($cn,".AMP.","&"); // Require to convert .AMP. back to '&'
		$cn = $this->charConv($cn,".SPACE."," ");
		//$q = mysql_query("SELECT `waybill_num` FROM `ichange_waybill` WHERE `cars` LIKE '%\"".$cn."\"%' LIMIT 1");
		$q = $this->Generic_model->qry("SELECT `waybill_num` FROM `ichange_waybill` WHERE `cars` LIKE '%\"".$cn."\"%' LIMIT 1");
		$w = "";
		if(isset($q[0]->waybill_num)){$w = $q[0]->waybill_num;}
		echo $w;
	}

	function selTrain($fld14){
		// Display Train Selected function called by AJAX.
		$this->db_conn();
		$fld14 = $this->charConv($fld14,".AMP.","&"); // Require to convert .AMP. back to '&'
		$fld14 = $this->charConv($fld14,".SPACE."," ");
		//echo $fld14;
		$sql = "SELECT * FROM `ichange_trains` WHERE `train_id` = '".$fld14."' LIMIT 1";
		//$qry = mysql_query($sql);
		$qry = $this->Generic_model->qry($sql);
		$lst = "";
		//while($res = mysql_fetch_array($qry)){
		if($qry[0]){
			$res = (array)$qry[0];
			$lst .= $res['train_desc']."<br />";
			if(strlen($res['origin'].$res['destination']) > 0){$lst .= $res['origin']." to ".$res['destination']."<br />";}
			$lst .= $res['op_notes']."<br />";
			$lst .= "<hr/>"; //Valid AUTO Train Waypoints (click to add a location to the Entry or Exit Location fields):<br />";
			$wps = json_decode($res['auto'], true);
			$wps_kys = @array_keys($wps);
			$opts = "<option value=\"".$res['origin']."\">".$res['origin']." (origin)</option>";
			for($o=0;$o<count($wps_kys);$o++){
				$opts .= "<option value=\"".$wps_kys[$o]."\">".$wps_kys[$o]." (".$wps[$wps_kys[$o]]." days)</option>";
			}
			if(strlen($res['auto']) > 4){
				$lst .= "Entry Waypoint: <select id=\"entry_waypoint\" name=\"entry_waypoint\" />".$opts."</select>&nbsp;";
				$lst .= "Exit Waypoint: <select id=\"exit_waypoint\" name=\"exit_waypoint\" />".$opts."</select>";
			}elseif(intval($res['auto']) > 0){
				$lst .= "Entry Waypoint: <input type=\"text\" id=\"entry_waypoint\" name=\"entry_waypoint\" value=\"\" onchange=\"this.value = this.value.toUpperCase();\" />&nbsp;";
				$lst .= "Exit Waypoint: <input type=\"text\" id=\"exit_waypoint\" name=\"exit_waypoint\" value=\"\" onchange=\"this.value = this.value.toUpperCase(); document.form1.pfld6.value = this.value;\" />";
			}else{
				$lst .= "<input type=\"hidden\" id=\"entry_waypoint\" name=\"entry_waypoint\" value=\"\" />";
				$lst .= "Exit Waypoint: <input type=\"text\" id=\"exit_waypoint\" name=\"exit_waypoint\" value=\"\" onchange=\"this.value = this.value.toUpperCase(); document.form1.pfld6.value = this.value;\" />";
			}
			if(intval($res['auto']) > 0 || strlen($res['auto']) > 4){$lst .= "&nbsp;<input type=\"button\" name=\"calc_route\" value=\"Calc Route\" onclick=\"selRoute();\" />";}
			//$lst .= "<a href=\"javascript:{}\" onclick=\"document.getElementById('exit_waypoint').value = '".$res['destination']."'\">".$res['destination']."</a>, ";
		}
		if(strlen($lst) < 1){
			$lst = "No results found!";
		}else{
			$lst .= "<br /><a href=\"javascript:{}\" onClick=\"document.getElementById('train_disp_span').style.display = 'none';\">[ Close this box ]</a>";
		}
		echo $lst;
	}

	function selRoute($trid,$start,$finish){
		// Generates route for auto train (ichange_trains.train_id = $trid).
		// $start = place were car is put in train. Can be empty if ichange_trains.auto is an integer > 0.
		// $finish = place where car is spotted by train.
		$this->db_conn();
		$trid = $this->charConv($trid,".AMP.","&"); // Require to convert .AMP. back to '&'
		$start = $this->charConv($start,".AMP.","&");
		$finish = $this->charConv($finish,".AMP.","&");

		$trid = $this->charConv($trid,".SPACE."," "); // Require to convert .AMP. back to '&'
		$start = $this->charConv($start,".SPACE."," ");
		$finish = $this->charConv($finish,".SPACE."," ");
		
		$sql = "SELECT `auto`,`origin`,`destination` FROM `ichange_trains` WHERE `train_id` = '".$trid."' LIMIT 1";
		//$qry = mysql_query($sql);
		$qry = $this->Generic_model->qry($sql);
		$res = (array)$qry[0]; //mysql_fetch_array($qry);
		if(intval($res['auto']) > 0){
			// Number of days to complete
			$arr = array($start => 0, $finish => $res['auto']);
		}else{
			// JSON array
			$arr_tmp = @json_decode($res['auto'],TRUE);
			$arr_tmp[$res['origin']] = 0;
			asort($arr_tmp);
			$arr_kys = array_keys($arr_tmp);
			print_r($arr_kys);
			$orig = $arr_tmp[$start]; // day value of entry waybpoint
			$dest = $arr_tmp[$finish]; // day value of exit waypoint
			for($i=0;$i<count($arr_kys);$i++){
				if($arr_tmp[$arr_kys[$i]] > $orig && $arr_tmp[$arr_kys[$i]] < $dest){
					$arr[$arr_kys[$i]] = $arr_tmp[$arr_kys[$i]] - $orig;
				}
			}
			$arr[$start] = 0;
			$arr[$finish] = $dest-$orig;
			asort($arr);
		}
		$json = json_encode($arr);
		echo $json;
	}

	function carsAutoFind($str,$fld){
		// Auto Complete function called by AJAX.
		// Checks whether an entry exists in the database
		// $fld = field to search in
		// $str = string to search for in field $fld
		$this->db_conn();
		$str = $this->charConv($str,".AMP.","&"); // Require to convert .AMP. back to '&'
		$str = $this->charConv($str,".SPACE."," ");
		$str = strtoupper($str);
		$sql = "SELECT * FROM `ichange_cars` WHERE (`".$fld."` LIKE '%".$str."%' OR `car_num` LIKE '%".$str."%' OR `aar_type` LIKE '%".$str."%') AND `rr` = '".$_COOKIE['rr_sess']."' ORDER BY `car_num` LIMIT 25";
		//$qry = mysql_query($sql);
		$qry = $this->Generic_model->qry($sql);
		$lst = "<table style=\"padding: 1px; background-color: transparent; border: none;\">";
		$lst .= "<tr><td class=\"td_title\">Car #</td><td class=\"td_title\">AAR</td><td class=\"td_title\">Lading</td><td class=\"td_title\">Location</td></tr>";
		$cntr=0;
		//while($res = mysql_fetch_array($qry)){
		for($i=0;$i<count($qry);$i++){
			$res = (array)$qry[$i];
			$lad = ""; if(strlen($res['lading']) > 1){$lad = $res['lading'];}
			$loc = ""; if(strlen($res['location']) > 0){$loc = $res['location'];}
			$lst .= "<tr><td class=\"td1\"><a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\"></a>".$res['car_num']."</td><td class=\"td1\">(".$res['aar_type'].")</td><td class=\"td1\">".$lad."</td><td class=\"td1\">".$loc."</td></tr>";
		}
		if(strlen($lst) < 1){
			$lst = "<tr><td>No results found!</td></tr>";
		}
		$lst .= "</table>";
		echo $lst;
	}

	function industAutoComp($str,$tbl,$fld,$sct = NULL,$sr = NULL){
		// Auto Complete function called by AJAX.
		// Checks whether an entry exists in the database
		// $fld = field to search in
		// $tbl = table to search in
		// $str = string to search for in field $fld - can be industry name, location, or commodity
		// $sct = span to display results in
		// $sr = whether a commodity is sent or received by the industry: 0 or null = all, 1=send, 2=receive
		$this->db_conn();
		$str = $this->charConv($str,".AMP.","&"); // Require to convert .AMP. back to '&'
		$str = $this->charConv($str,".SPACE."," ");
		$tbl = "ichange_indust";
		$str = strtoupper($str);
		$sql_sr = " OR `freight_in` LIKE '%".$str."%' OR `freight_out` LIKE '%".$str."%'";
		if($sr == 1){$sql_sr = " OR `freight_out` LIKE '%".$str."%'";}
		if($sr == 2){$sql_sr = " OR `freight_in` LIKE '%".$str."%'";}
		$sql = "SELECT * FROM `".$tbl."` WHERE `indust_name` LIKE '%".$str."%'".$sql_sr." LIMIT 9";
		$qry = $this->Generic_model->qry($sql); //mysql_query($sql);
		$lst = "<a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a><br />";
		//while($res = mysql_fetch_array($qry)){
		for($i=0;$i<count($qry);$i++){
			$res = (array)$qry[$i];
			$rr_mark = $this->qry("ichange_rr", $res['rr'], "id", "report_mark");
			$recs = $res['freight_in'];
			$sends = $res['freight_out'];
		
			$autoCompNote = "";
			if(strlen($str) > 0){
				if(strpos("a".strtoupper($sends),$str) > 0 && $sr == 1){$autoCompNote = "<span style=\"text-decoration: underline;\">Sends: ".$str."</span>";}
				if(strpos("a".strtoupper($recs),$str) > 0 && $sr == 2){$autoCompNote = "<span style=\"text-decoration: underline;\">Receives: ".$str."</span>";}
			}

			$op_info = trim($res['op_info']); $show_indDescDiv = "";
			if(strlen($op_info) > 0){$show_indDescDiv = " document.getElementById('".$sct."_indDescDiv').style.display = 'block';";}		
			$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\" onClick=\"document.getElementById('".$sct."').value = '".trim($res['indust_name'])."'; document.getElementById('".$sct."_indDesc').value = '".trim($res['op_info'])."'; document.getElementById('".$fld."_span').style.display = 'none';".$show_indDescDiv."\">".$res['indust_name']."</a>&nbsp;".$autoCompNote."&nbsp(".$rr_mark.")<br />";
			if(strlen($res['desc']) > 1){$lst .= "<div style=\"display: block; font-size:8pt; max-width: 600px; color: #333\">&nbsp;&nbsp;&nbsp;".$res['desc']."</div>";}
		}
		$sql = "SELECT * FROM `ichange_ind40k` WHERE `industry` LIKE '%".$str."%' OR `city` LIKE '%".$str."%' OR `state` LIKE '%".$str."%' OR `commodity` LIKE '%".$str."%' LIMIT 9";
		$qry = $this->Generic_model->qry($sql); //mysql_query($sql);
		$rows = count($qry); //mysql_num_rows($qry);
		if($rows > 0){$lst .= "===== 40,000 Industry Records Found =====<br />";}
		//while($res = mysql_fetch_array($qry)){
		for($i=0;$i<count($qry);$i++){
			$res = (array)$qry[$i];
			$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\" onClick=\"document.getElementById('".$sct."').value = '".trim(strtoupper($res['industry'].",".$res['city'].",".$res['state']))."'; document.getElementById('".$fld."_span').style.display = 'none';\">".strtoupper($res['industry'].",".$res['city'].",".$res['state'])."</a><span style=\"font-size: 8pt;\"></span><br />";
		}
		if(strlen($lst) < 1){
			$lst = "No results found!";
		}else{
			$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a>";
		}
		echo $lst;
	}

	function autoComp($str,$tbl,$fld,$sct = NULL){
		// Auto Complete function called by AJAX.
		// Checks whether an entry exists in the database
		// $fld = field to search in, or comma separated list where first field is field to search in and subsequent ones display in span.
		// $tbl = table to search in
		// $str = string to search for in field $fld
	    // $sct = field name in form to add value to.
		$this->db_conn();
		$str = $this->charConv($str,".AMP.","&"); // Require to convert .AMP. back to '&'
		$fld = $this->charConv($fld,".COMMA.",",");
		$xtra_flds = "";
		if(strpos($fld,",") > 0){
			$fld_tmp = explode (",",$fld);
			$fld = $fld_tmp[0];
			for($i=1;$i<count($fld_tmp);$i++){$xtra_flds .= ", `".$fld_tmp[$i]."`";}
		}
		$sql = "SELECT DISTINCT `".$fld."`".$xtra_flds." FROM `".$tbl."` WHERE `".$fld."` LIKE '%".$str."%' LIMIT 4";
		$qry = $this->Generic_model->qry($sql); //mysql_query($sql);
		$lst = "<span style=\"float: right;\"><a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a></span>";
		//while($res = mysql_fetch_array($qry)){
		for($i=0;$i<count($qry);$i++){
			$res = (array)$qry[$i];
			//$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\" onClick=\"document.getElementById('".$sct."').value = '".trim($res[$fld])."'; document.getElementById('".$fld."_span').style.display = 'none';\">".$res[$fld]."</a><br />";
			$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\" onClick=\"document.getElementById('".$sct."').value = '".trim($res[$fld])."'; document.getElementById('".$sct."_span').style.display = 'none';\">".$res[$fld]."</a><br />";
			if(isset($fld_tmp)){
				for($o=1;$o<count($fld_tmp);$o++){
					if(strlen($res[$fld_tmp[$o]]) > 0){$lst .= "<span style=\"color: #777; padding-left: 5px;\">".ucwords($fld_tmp[$o]).": ".$res[$fld_tmp[$o]]."</span><br />";}
				}
			}
		}
		if(strlen($lst) < 1){
			$lst = "No results found!";
		}else{
			$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a>";
		}
		echo $lst;
	}

	function allocTrain($wb,$tr){
		// Auto Complete function called by AJAX.
		// Checks whether an entry exists in the database
		// $w = waybill
		// $t = train to allocate to waybill
		$this->db_conn();
		$tr = $this->charConv($tr,".AMP.","&"); // Require to convert .AMP. back to '&'
		$wb = $this->charConv($wb,".AMP.","&"); // Require to convert .AMP. back to '&'
		$sql = "UPDATE `ichange_waybill` SET `train_id` = '".$tr."' WHERE `id` = '".$wb."'";
		$this->Generic_model->change($sql); //mysql_query($sql);
	}

	function allocRR($wb,$tr){
		// Auto Complete function called by AJAX.
		// Checks whether an entry exists in the database
		// $w = waybill
		// $tr = railroad to allocate waybill to
		$this->db_conn();
		$sql = "UPDATE `ichange_waybill` SET `rr_id_handling` = '".$tr."', `train_id` = '' WHERE `id` = '".$wb."'";
		$this->Generic_model->change($sql); //mysql_query($sql);
	}

	function mapDetails($s){
		// Builds map details for waybills, for the state/province code selected in $s
		$this->db_conn();
		$s = strtoupper($s);
		$sql = "SELECT `id`,`status`,`waybill_num`,`progress` FROM `ichange_waybill` WHERE `status` != 'CLOSED' AND `progress` LIKE '%,".$s."%'";
		$qry = $this->Generic_model->qry($sql); //mysql_query($sql);
		$info = "";
		//while($r=mysql_fetch_array($qry)){
		for($i=0;$i<count($qry);$i++){
			$r = (array)$qry[$i];
			$prog = json_decode($r['progress'], true);
			$progCntr = count($prog)-1;
			$date = $prog[$progCntr]['date'];
			$text = $prog[$progCntr]['text'];
			$map_location = str_replace(", ",",",$prog[$progCntr]['map_location']);
			if(strpos($prog[$progCntr]['map_location'],",".$s) > 0){
				//$info .= "<hr />".$r['waybill_num']." - ".$r['date']."<br />".$text." - ".$prog[$progCntr]['map_location'];
				$info .= "<hr /><a href=\"javascript:{}\" onclick=\"wbDetails('".$r['id']."');\">".$r['waybill_num']."</a><br />".$date."<br />".$r['status']."<br />".$prog[$progCntr]['map_location']."&nbsp;<a href=\"../waybill/edit/".$r['id']."\">Edit</a>";
			}
		}
		echo $info;
	}

	function mapWBDetails($s){
		// Builds map details for waybills, for the state/province code selected in $s
		$this->db_conn();
		$s = strtoupper($s);
		$sql = "SELECT * FROM `ichange_waybill` WHERE `id` = '".$s."'";
		$qry = $this->Generic_model->qry($sql); //mysql_query($sql);
		$info = "";
		//while($r=mysql_fetch_array($qry)){
		for($i=0;$i<count($qry);$i++){
			$r = (array)$qry[$i];
			$prog = json_decode($r['progress'], true);
			$progCntr = count($prog)-1;
			$date = $prog[$progCntr]['date'];
			$text = $prog[$progCntr]['text'];
			$map_location = str_replace(", ",",",$prog[$progCntr]['map_location']);
			//$info .= "<hr />".$r['waybill_num']." - ".$r['date']."<br />".$text." - ".$prog[$progCntr]['map_location'];
			$info .= "<span style=\"font-size:12pt; font-weight: bold;\">".$r['waybill_num']."</span><br />".$r['date']."<br />At: ".$prog[$progCntr]['map_location']."<br />";
			$info .= "From ".$r['indust_origin_name']."<br />To: ".$r['indust_dest_name']."<br />";
			$info .= "Routing: ".$r['routing']."<br />";
			$info .= "<hr />Progress:<hr />";
			for($i=0;$i<count($prog);$i++){
				$info .= $prog[$i]['date']." - ".$prog[$i]['text']."<br />";
			}
		}
		echo $info;
	}
	
	function wbImages($id=0){
		// Generates HTML to display images for waybill with waybill.id of $id.
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
	}

	// Supporting functions
	function db_conn(){
		/*
		// LIVE SERVER
		$dbhost="db72d.pair.com";
		$dbusername="jstan_6_w";
		$dbpassword="Js120767";
		$dbname="jstan_general";

		// TESTING
		$LocTst = $_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'];
		if(strpos($LocTst,"www/Applications/") > 0){
			$dbhost="localhost";
			$dbusername="admin";
			$dbpassword="admin";
			$dbname="jstan_general";
		}

		$dbcnx = mysql_connect($dbhost, $dbusername, $dbpassword);
		$seldb = mysql_select_db($dbname);
		*/
	}

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
		$dosql_com = $this->Generic_model->qry($sql_com); //mysql_query($sql_com);
		$ret = "";
		//while($resultcom = mysql_fetch_array($dosql_com)){
			$res = (array)$dosql_com[0];
			$ret = $res[$fld]; //$resultcom[$fld];		
		//}
		
		return $ret; //Value to return.
	}	
	
}
?>