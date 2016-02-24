<?php
/*

	Various Functions that can be used in other files.
	This file needs to be included first (preferably near the top of the including file).

*/

function sel_fld($fldNam, $table, $id){
	$sql = "SELECT `".$fldNam."` FROM `".$table."` WHERE `id` = '".$id."'";
	$dosql = mysql_query($sql);
	$result = @mysql_fetch_array($dosql);
	return $result[$fldNam];
}

function renderEle($renderTitle, $renderVar, $styl){
	// Renders a form element
	if(strlen($renderTitle) > 0){
		return "<tr><td valign=\"top\" style=\"text-align: right; ".$styl."\">\n".$renderTitle."\n</td>\n<td valign=\"top\" style=\"text-align: left; ".$styl."\">".$renderVar."</td></tr>\n";
	}
}

function valLn($fldNam){
	// Create a Javascript Validation Line
	return "var ".$fldNam." = document.getElementById('".$fldNam."');";
}

function option_lst($tbl,$fld1,$fld2,$where){
	// performs a Query on table $tbl, and creates an option list for a HTML select tag set.
	// $fld1 is the field to place inside the value inside the option tag.
	// $fld2 is the field to display BETWEEN the option tags.
	// $where is the WHERE clause for the query. if none specified, finds ALL the records.
	$max_len = 75; // Maximum length of an option text.
	$sql_lst = "SELECT * FROM `".$tbl."`";
	if(strlen($where) > 0){$sql_lst = $sql_lst." WHERE ".$where;}
	$sql_lst = $sql_lst." ORDER BY `".$fld2."`";
	$dosql_lst = mysql_query($sql_lst);
	// $dosql_lst = sq_lst($tbl,$fld1,$fld2,$where);
	$fldHtml = "";
	$fldValue = "";
	while($result_lst = mysql_fetch_array($dosql_lst)){
		$txt = $result_lst[$fld2];
		if(strlen($txt) > $max_len){$txt = substr($txt,0,$max_len);}
		$fldHtml= $fldHtml."<option value=\"".$result_lst[$fld1]."\">".$txt."</option>\n";
	}
	return $fldHtml;
}

function ul_lst($tbl,$fld1,$fld2,$where){
	// performs a Query on table $tbl, and creates an UL list for a HTML select tag set.
	// $fld1 is the field to place inside the value inside the option tag.
	// $fld2 is the field to display BETWEEN the option tags.
	// $where is the WHERE clause for the query. if none specified, finds ALL the records.
	$sql_lst = "SELECT * FROM `".$tbl."`";
	if(strlen($where) > 0){$sql_lst = $sql_lst." WHERE ".$where;}
	$sql_lst = $sql_lst." ORDER BY `".$fld2."`";
	$dosql_lst = mysql_query($sql_lst);
	// $dosql_lst = sq_lst($tbl,$fld1,$fld2,$where);
	$fldHtml = "<ul>";
	$fldValue = "";
	$cntr = 0;
	$fldHtml = "";
	while($result_lst = mysql_fetch_array($dosql_lst)){
		$cntr = $cntr + 1;
		$fldHtml= $fldHtml."<li>".$result_lst[$fld1]." - ".$result_lst[$fld2]."</li>\n";
		// if(floatval($cntr / 15) == intval($cntr / 15)){$fldHtml = $fldHtml."</td><td>\n";}
	}
	$fldHtml = $fldHtml."</ul>";
	return $fldHtml;
}

function ret_arr($tbl,$fld1,$where){
	// performs a Query on table $tbl, and creates an comma separated array.
	// $fld1 is the field to get data from.
	// $where is the WHERE clause for the query. if none specified, finds ALL the records.
	$sql_lst = "SELECT * FROM `".$tbl."`";
	if(strlen($where) > 0){$sql_lst = $sql_lst." WHERE ".$where;}
	$sql_lst = $sql_lst." ORDER BY `".$fld1."`";
	$dosql_lst = mysql_query($sql_lst);
	$fldArr = "";
	$fldValue = "";
	while($result_lst = mysql_fetch_array($dosql_lst)){
		$fldArr= $fldArr."\"".$result_lst[$fld1]."\", ";
	}
	$fldArr = substr_replace($fldArr ,"",-2);
	return $fldArr;
	
}

function strip_spec($orig_str){
	// strips excape and control characters from a string.
   for($c=0;$c<32;$c++){$orig_str = str_replace(chr($c),"", $orig_str);}
   for($c=127;$c<256;$c++){$orig_str = str_replace(chr($c),"", $orig_str);}
   //$orig_str = str_replace("\r\n","",$orig_str);
   return $orig_str;
}

function frmTag($typ, $nam, $val = "", $cols = 40, $rows = 5, $styl = ""){
	// $typ = Type of form tag to render - input, select, hidden, readonly, textarea
	// $nam = Form tag 'name'. name="$nam"
	// $val = value, or options
	// $cols =  columns / size of element
	// $rows = rows of element (textarea)
	// $styl = Inline Style to apply to element
	$rnFld = "";
	if(strlen($styl) > 0){$styl=" style=\"".$styl."\"";}
	$typ = strtolower($typ);
	if($typ == "input"){
		$rnFld = "<input type=\"text\" id=\"".$nam."\" name=\"".$nam."\" size=\"".$cols."\" maxsize=\"".$cols."\" value=\"".$val."\"".$styl." />\n";
	}
	if($typ == "select"){
		$rnFld = "<select id=\"".$nam."\" name=\"".$nam."\"".$styl." />".$val."</select>\n";
	}
	if($typ == "hidden"){
		$rnFld = "<input type=\"hidden\" name=\"".$nam."\" id=\"".$nam."\" value=\"".$val."\"".$styl." />\n";
	}
	if($typ == "readonly"){
		$rnFld = "<input type=\"hidden\" name=\"".$nam."\" id=\"".$nam."\" value=\"".$val."\" />".$val;
	}
	if($typ == "textarea"){
		$rnFld = "<textarea name=\"".$nam."\" id=\"".$nam."\"".$styl." cols=\"".$cols."\" rows=\"".$rows."\">".$val."</textarea>\n";
	}
	return $rnFld;
}

// Start PHPLiveX related functions
function autoComp($str,$tbl,$fld,$sct = NULL){
	// Auto Complete function called by AJAX.
	// Checks whether an entry exists in the database
	// $fld = field to search in, or comma separated list where first field is field to search in and subsequent ones display in span.
	// $tbl = table to search in
	// $str = string to search for in field $fld
    // $sct = field name in form to add value to.
	include("db_connect7465.php");
	$xtra_flds = "";
	if(strpos($fld,",") > 0){
		$fld_tmp = explode (",",$fld);
		$fld = $fld_tmp[0];
		for($i=1;$i<count($fld_tmp);$i++){$xtra_flds .= ", `".$fld_tmp[$i]."`";}
	}
	$sql = "SELECT DISTINCT `".$fld."`".$xtra_flds." FROM `".$tbl."` WHERE `".$fld."` LIKE '%".$str."%' LIMIT 4";
	$qry = mysql_query($sql);
	$lst = "<span style=\"float: right;\"><a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a></span>";
	while($res = mysql_fetch_array($qry)){
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
	mysql_close();
	return $lst;
}

function showData($jsonArr){
	/*
	Used by PHPLiveX / Javascript functions - gets a single record and displays as HTML.
	$arr is a JSON array!
	$arr['val'] = value to search for
	$arr['key'] = field to search in
	$arr['tbl'] = data table for search
	$arr['fld'] = array of field names to return values for
	In JS: ---
		var i = document.form1.field1.value;
		var arr = '{"val":' + i + ',"key":"id","tbl":"ichange_indust","fld":["indust_name","desc","freight_in","freight_out"]}';
		showData(arr, {
			"preloader": "pr", 
			"onFinish": function(response){
				** ACTIONS TO COMPLETE **
			}
		});
	*/
	$arr = json_decode($jsonArr, true);
	include("db_connect7465.php");
	$val = $arr['val'];
	$key = $arr['key'];
	$tbl = $arr['tbl'];
	$fld = $arr['fld'];
	$sql = "SELECT * FROM `".$tbl."` WHERE `".$key."` = '".$val."'";
	$qu = mysql_query($sql);
	$res = mysql_fetch_array($qu);
	$ret = "";
	for($i=0;$i<count($fld);$i++){
		$ret .= ucwords(str_replace("_"," ",$fld[$i])).": ".$res[$fld[$i]]."<br />";
	}
	//$ret .= $res['indust_name']."<br />".$res['desc']."<br />".$res['freight_in']."<br />".$res['freight_out'];
	mysql_close();
	return $ret;
}  

function getData($jsonArr){
	/*
	Used by PHPLiveX / Javascript functions to build a JSON array from data retreived
	$arr is a JSON array!
	$arr['val'] = value to search for
	$arr['key'] = field to search in
	$arr['tbl'] = data table for search
	$arr['fld'] = array of field names to return values for
	$arr['name'] = name of array to build
	In JS: ---
		var i = document.form1.field2.value;
		var arr = '{"val":' + i + ',"key":"rr","tbl":"ichange_indust","name":"industry","fld":["id","indust_name","desc","freight_in","freight_out"]}';
   	getData(arr, {
			"preloader": "pr",  
			"content_type": "json",  
			"onFinish": function(response){
				** ACTIONS TO COMPLETE **
			}  
		});  
	*/
	$arr = json_decode($jsonArr, true);
	include("db_connect7465.php");
	$val = $arr['val'];
	$key = $arr['key'];
	$tbl = $arr['tbl'];
	$fld = $arr['fld'];
	$name = $arr['name'];
	$sql = "SELECT * FROM `".$tbl."`";
	if(strlen($val) > 0 || $val > 0){$sql .= " WHERE `".$key."` = '".$val."'";}
	//echo $s; exit();
	$qu = mysql_query($sql);
	$arr = array();
	//$arr[$name]['train_id'] = $sql;
	while($res = mysql_fetch_array($qu)){
		$fldVals = array();
		for($i=0;$i<count($fld);$i++){$fldVals[$fld[$i]] = $res[$fld[$i]];}
		//$arr[$name] = $fldVals;
		$arr[$name][] = $fldVals;
	}
	mysql_close();
	return $arr;
}  
// End PHPLiveX related functions

function industAutoComp($str,$tbl,$fld,$sct = NULL,$sr = NULL){
	// Auto Complete function called by AJAX.
	// Checks whether an entry exists in the database
	// $fld = field to search in
	// $tbl = table to search in
	// $str = string to search for in field $fld - can be industry name, location, or commodity
	// $sct = span to display results in
	// $sr = whether a commodity is sent or received by the industry: 0 or null = all, 1=send, 2=receive
	include("db_connect7465.php");
	$str = strtoupper($str);
	$sql_sr = " OR `freight_in` LIKE '%".$str."%' OR `freight_out` LIKE '%".$str."%'";
	if($sr == 1){$sql_sr = " OR `freight_out` LIKE '%".$str."%'";}
	if($sr == 2){$sql_sr = " OR `freight_in` LIKE '%".$str."%'";}
	$sql = "SELECT * FROM `".$tbl."` WHERE `indust_name` LIKE '%".$str."%'".$sql_sr." LIMIT 9";
	$qry = mysql_query($sql);
	$lst = "<a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a><br />";
	while($res = mysql_fetch_array($qry)){
		$rr_mark = qry("ichange_rr", $res['rr'], "id", "report_mark");
		$recs = $res['freight_in'];
		$sends = $res['freight_out'];
		
		$autoCompNote = "";
		if(strpos("a".strtoupper($sends),$str) > 0 && $sr == 1){$autoCompNote = "<span style=\"text-decoration: underline;\">Sends: ".$str."</span>";}
		if(strpos("a".strtoupper($recs),$str) > 0 && $sr == 2){$autoCompNote = "<span style=\"text-decoration: underline;\">Receives: ".$str."</span>";}

		$op_info = trim($res['op_info']); $show_indDescDiv = "";
		if(strlen($op_info) > 0){$show_indDescDiv = " document.getElementById('".$sct."_indDescDiv').style.display = 'block';";}		
		$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\" onClick=\"document.getElementById('".$sct."').value = '".trim($res['indust_name'])."'; document.getElementById('".$sct."_indDesc').value = '".trim($res['op_info'])."'; document.getElementById('".$fld."_span').style.display = 'none';".$show_indDescDiv."\">".$res['indust_name']."</a>&nbsp;".$autoCompNote."&nbsp(".$rr_mark.")<br />";
		if(strlen($res['desc']) > 1){$lst .= "<div style=\"display: block; font-size:8pt; max-width: 600px; color: #333\">&nbsp;&nbsp;&nbsp;".$res['desc']."</div>";}
	}
	$sql = "SELECT * FROM `ichange_ind40k` WHERE `industry` LIKE '%".$str."%' OR `city` LIKE '%".$str."%' OR `state` LIKE '%".$str."%' OR `commodity` LIKE '%".$str."%' LIMIT 9";
	$qry = mysql_query($sql);
	$rows = mysql_num_rows($qry);
	if($rows > 0){$lst .= "===== 40,000 Industry Records Found =====<br />";}
	while($res = mysql_fetch_array($qry)){
		$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\" onClick=\"document.getElementById('".$sct."').value = '".trim(strtoupper($res['industry'].",".$res['city'].",".$res['state']))."'; document.getElementById('".$fld."_span').style.display = 'none';\">".strtoupper($res['industry'].",".$res['city'].",".$res['state'])."</a><span style=\"font-size: 8pt;\"></span><br />";
	}
	if(strlen($lst) < 1){
		$lst = "No results found!";
	}else{
		$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a>";
	}
	mysql_close();
	return $lst;
}

function carsAutoFind($str,$fld,$sct = NULL, $fun = ""){
	// Auto Complete function called by AJAX.
	// Checks whether an entry exists in the database
	// $fld = field to search in
	// $str = string to search for in field $fld
	// $sct = span to display results in
	// $fun = javascript function to run on click
	include("db_connect7465.php");
	$str = strtoupper($str);
	$sql = "SELECT * FROM `ichange_cars` WHERE (`".$fld."` LIKE '%".$str."%' OR `car_num` LIKE '%".$str."%' OR `aar_type` LIKE '%".$str."%') AND `rr` = '".$_COOKIE['rr_sess']."' ORDER BY `car_num` LIMIT 25";
	$qry = mysql_query($sql);
	$lst = "<table style=\"padding: 1px; background-color: transparent; border: none;\">"; //"<a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a><br />";
	$lst .= "<tr><td class=\"td_title\">Car #</td><td class=\"td_title\">AAR</td><td class=\"td_title\">Lading</td><td class=\"td_title\">Location</td></tr>";
	$cntr=0;
	while($res = mysql_fetch_array($qry)){
		$lad = ""; if(strlen($res['lading']) > 1){$lad = $res['lading'];}
		$loc = ""; if(strlen($res['location']) > 0){$loc = $res['location'];}
		//$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\" onClick=\"document.getElementById('".$sct."').value = '".trim($res['car_num'])."'; ".$fun."\">Car</a> | Alias ".$res['car_num']." (".$res['aar_type'].") - ".$lad." - ".$loc."<br />";
		$lst .= "<tr><td class=\"td1\"><a href=\"javascript:{}\" class=\"autocompletetxt\" style=\"text-decoration: none;\"></a>".$res['car_num']."</td><td class=\"td1\">(".$res['aar_type'].")</td><td class=\"td1\">".$lad."</td><td class=\"td1\">".$loc."</td></tr>";
	}
	if(strlen($lst) < 1){
		$lst = "<tr><td>No results found!</td></tr>";
	}else{
		//$lst .= "<a href=\"javascript:{}\" class=\"autocompletetxt\" onClick=\"document.getElementById('".$sct."_span').style.display = 'none';\">[ Close this box ]</a>";
	}
	$lst .= "</table>";
	mysql_close();
	return $lst;
}

function selTrain($fld14){
	// Display Train Selected function called by AJAX.

	include("db_connect7465.php");
	$sql = "SELECT * FROM `ichange_trains` WHERE `train_id` = '".$fld14."' LIMIT 1";
	$qry = mysql_query($sql);
	$lst = "";
	while($res = mysql_fetch_array($qry)){
		$lst .= $res['train_desc']."<br />";
		if(strlen($res['origin'].$res['destination']) > 0){$lst .= $res['origin']." to ".$res['destination']."<br />";}
		$lst .= $res['op_notes']."<br />";
		$lst .= "<hr/>Valid AUTO Train Waypoints (click to add a location to the Entry or Exit Location fields):<br />";
		$wps = json_decode($res['auto'], true);
		$wps_kys = @array_keys($wps);
		for($o=0;$o<count($wps_kys);$o++){
			//$lst .= "<a href=\"javascript:{}\" onclick=\"document.getElementById('exit_waypoint').value = '".str_replace(", ",",",$wps_kys[$o])."'\">".$wps_kys[$o]."</a>, ";
			$lst .= "<div id=\"tr".$o."\" style=\"\">";
			$lst .= "&nbsp;<a href=\"javascript:{}\" onclick=\"document.getElementById('entry_waypoint').value = '".str_replace(", ",",",$wps_kys[$o])."'; document.getElementById('tr".$o."').style.display = 'none';\">(ENTRY)</a>,&nbsp;";
			$lst .= "<a href=\"javascript:{}\" onclick=\"document.getElementById('exit_waypoint').value = '".str_replace(", ",",",$wps_kys[$o])."'; document.getElementById('tr".$o."').style.display = 'none';\">(EXIT)</a>&nbsp;";
			$lst .= "<strong>".$wps_kys[$o]."</strong> (".$wps[$wps_kys[$o]]." days)";
			$lst .= "</div>";
			
		}
		//$lst .= "<a href=\"javascript:{}\" onclick=\"document.getElementById('exit_waypoint').value = '".$res['destination']."'\">".$res['destination']."</a>, ";
	}
	if(strlen($lst) < 1){
		$lst = "No results found!";
	}else{
		$lst .= "<br /><a href=\"javascript:{}\" onClick=\"document.getElementById('train_disp_span').style.display = 'none';\">[ Close this box ]</a>";
	}
	mysql_close();
	return $lst;
}

function isInHdeArr($id){
	// Checks whether a waybill is in the Hde cookie.
	// Returns 0 if in array, 1 if not
	// $wb = waybill #.
	$c_chk = array();
	
	if(isset($_COOKIE['hde'])){
		$c_chk = $_COOKIE['hde'];
		$c_chk = explode(",",$c_chk);
	}
	if(in_array($id,$c_chk)){ return 1;}
	return 2;
}

?>