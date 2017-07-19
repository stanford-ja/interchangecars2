<?php
// Ajax function caller
// syntax: ajax.php?f=[function]&d=[data to send]
// Functions need to be added to the function caller below to be available to JQuery!

// Start function caller
if(isset($_GET['f'])){
	if($_GET['f'] == "multiProg"){multiProg(@$_GET['i'],@$_GET['w'],@$_GET['t'],@$_GET['r']);}
	if($_GET['f'] == "dateRebuild"){dateRebuild(@$_GET['t'],@$_GET['r'],@$_GET['w']);}
}
// End function caller

// Ajax accessible functions

function multiProg($c,$w,$t,$r){
	// Adds a progress report form
	// $c = previous prog form array number (0 is the first prog report)
	// $w = waybill number (NOT record id)
	// $t = timezone
	// $r = railroad id
	db_conn();
	$dbs = db_conn_settings();
	$sqli = new mysqli($dbs['dbhost'],$dbs['dbusername'],$dbs['dbpassword'],$dbs['dbname']);
	$t = charConv($t,"[AMP]","&"); // Require to convert [AMP] back to '&'
	$c++; // Advance one so that IDs of new form are not the same the existing ones.

	if(qry("ichange_rr", $r, "id", "use_tz_time") == 1 && strlen(@$_GET['t']) > 0){date_default_timezone_set($t);}
	$wbd = $sqli->query("SELECT * FROM `ichange_waybill` WHERE `waybill_num` = '".$w."'"); // Waybill data
	$wbres = $wbd->fetch_assoc();
	$dt_opts = dateRebuildReturn("",$r,$w); // dateRebuildReturn($wbres['train_id'],$r,$w); // HERE!
	
	$bgcol = "#eee"; if(intval($c/2) == floatval($c/2)){$bgcol = "#DCDCDC";}
	$htm = "";
	$htm .= "<div style=\"display: table; background-color: transparent; border: 1px solid brown; width: 100%; padding: 1px;\">"; // start table
	$htm .= "<div style=\"display: table-row;\">"; // start table-row
	$htm .= "<div style=\"display: table-cell; vertical-align:top; background-color: ".$bgcol."; padding: 4px; border: 1px solid peru; width: 20%;\">"; // start table-cell
	//$htm .= "<input type=\"hidden\" id=\"pfld2_".$c."\" name=\"pfld2[]\" size=\"12\" maxsize=\"12\" value=\"".date('Y-m-d')."\" />";
	$htm .= "<select id=\"pfld2_".$c."\" name=\"pfld2[]\">".$dt_opts."</select>";
	/*
	$htm .= "<select name=\"pfld2_y[]\" id=\"pfld2_".$c."_y\" onchange=\"set_human_date('pfld2_".$c."')\">";
	$p_yr = date('Y'); 
	$p_yr2 = date('Y')+1;
	for($i=$p_yr;$i<=$p_yr2;$i++){
		$ii = $i;
		$sel = ""; if($i == date('Y')){$sel = " selected=\"selected\"";}
		$htm .= "<option".$sel." value=\"".$ii."\">".$ii."</option>";
	}
	$htm .= "</select> - ";
	$htm .= "<select name=\"pfld2_m[]\" id=\"pfld2_".$c."_m\" onchange=\"set_human_date('pfld2_".$c."')\">";
	$p_mt = 1;
	$p_mt2 = 12;
	$mths = array("","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
	for($i=$p_mt;$i<=$p_mt2;$i++){
		$ii = $i; if($ii<10){$ii = "0".$ii;}
		$sel = ""; if($ii == date('m')){$sel = " selected=\"selected\"";}
		$htm .= "<option".$sel." value=\"".$ii."\">".$mths[$i]."</option>";
	}
	$htm .= "</select> - ";
	$htm .= "<select name=\"pfld2_d[]\" id=\"pfld2_".$c."_d\" onchange=\"set_human_date('pfld2_".$c."')\">";
	$p_dy = 1;
	$p_dy2 = 31;
	for($i=$p_dy;$i<=$p_dy2;$i++){
		$ii = $i; if($ii<10){$ii = "0".$ii;}
		$sel = ""; if($ii == date('d')){$sel = " selected=\"selected\"";}
		$htm .= "<option".$sel." value=\"".$ii."\">".$ii."</option>";
	}
	$htm .= "</select>";
	*/
	$htm .= "<br />";
	//$htm .= "<select name=\"tzone_NOT\" id=\"tzone_NOT\" style=\"display: none;\">".$tz_opts."</select>";
	//$htm .= "</div>"; // end table-cell
	//$htm .= "<div style=\"display: table-cell; vertical-align: top; background-color: ".$bgcol."; padding: 4px; border: 1px solid peru; width: 12%;\">"; // start table-cell
	$htm .= "<select id=\"pfld7_".$c."\" name=\"pfld7[]\">";
	$p_hr = 0;
	$p_hr2 = 23;
	$hr_sel = date('H');
	for($i=$p_hr;$i<=$p_hr2;$i++){
		$ii = $i; if($ii<10){$ii = "0".$ii;}
		$sel = ""; if($ii == $hr_sel){$sel = " selected=\"selected\"";}
		$htm .= "<option".$sel." value=\"".$ii."\">".$ii."</option>";
	}
	$htm .= "</select>:";
	$htm .= "<select id=\"pfld8_".$c."\" name=\"pfld8[]\">";
	$p_mi = 0;
	$p_mi2 = 59;
	$mi_sel = date("i");
	for($i=$p_mi;$i<=$p_mi2;$i++){
		$ii = $i; if($ii<10){$ii = "0".$ii;}
		$sel = ""; if($ii == $mi_sel){$sel = " selected=\"selected\"";}
		$htm .= "<option".$sel." value=\"".$ii."\">".$ii."</option>";
	}
	$tr_s = "SELECT * FROM `ichange_trains` WHERE `railroad_id` = '".$r."' AND (LENGTH(`auto`) < 2 AND `auto` < 1) AND `train_id` != 'NOT ALLOCATED' ORDER BY `train_id`";
	$tr_q = mysql_query($tr_s);
	//$tr_lst = "<option value=\"\">NOT ALLOCATED</option>";
	$tr_lst = "<input type=\"hidden\" name=\"fld14[]\" id=\"fld14_".$c."\" /><input type=\"radio\" name=\"tr_tmp_".$c."\" onchange=\"document.getElementById('fld14_".$c."').value = '';rebuildDateSel('pfld2_".$c."','fld14_".$c."');\" /> NOT ALLOCATED<br />";
	while($tr_r = mysql_fetch_array($tr_q)){
		//$tr_lst .= "<option value=\"".$tr_r['train_id']."\">(".$tr_r['train_id'].") ".substr($tr_r['train_desc'],0,15)."</option>";
		//$tr_lst .= "<option value=\"".$tr_r['train_id']."\">".$tr_r['train_id']."</option>";
		$tr_lst .= "<input type=\"radio\" name=\"tr_tmp_".$c."\" onchange=\"document.getElementById('fld14_".$c."').value = '".$tr_r['train_id']."';rebuildDateSel('pfld2_".$c."','fld14_".$c."');\" />(".$tr_r['train_id'].") ".$tr_r['train_desc']."<br />";
	}
	$htm .= "</select><br />";
	$htm .= "TZ:<input type=\"text\" readonly=\"readonly\" name=\"tzone[]\" value=\"".$t."\" style=\"border: none; background-color: transparent; width: auto;\" />";
	$htm .= "</div>"; // end table-cell
	$htm .= "<div style=\"display: table-cell; vertical-align: top; background-color: ".$bgcol."; padding: 4px; border: 1px solid peru; width: 10%;\">&nbsp;</div>"; // Only need express field for first pro report!
	$htm .= "<div style=\"display: table-cell; vertical-align: top; background-color: ".$bgcol."; padding: 4px; border: 1px solid peru; width: 30%;\"><textarea name=\"pfld3[]\" id=\"pfld3_".$c."\" style=\"width:95%; height: 90px;\"></textarea>	</div>"; // start & end table-cell
	$htm .= "<div style=\"display: table-cell; vertical-align: top; background-color: ".$bgcol."; padding: 4px; border: 1px solid peru; width: 28%;\">"; // start table-cell
	$htm .= "<input type=\"text\" id=\"pfld6_".$c."\" name=\"pfld6[]\" size=\"16\" maxsize=\"16\" value=\"\" /><br />"; // No need for location selector in this prog form!!
	$htm .= "</div>"; // end table-cell
	$htm .= "</div>"; // end table-row;
	//$htm .= "</div>"; // end table
   //$htm .= "<div style=\"display: table; background-color: transparent; border: 1px solid brown; width: 100%; padding: 1px;\">"; // start table
	$htm .= "<div style=\"display: table-row;\">"; // start table-row
	$htm .= "<div style=\"display: table-cell; vertical-align:top; background-color: ".$bgcol."; padding: 4px; border: 1px solid peru; width: 20%;\">";
	//$htm .= "<select name=\"fld14[]\" style=\"font-size: 9pt;\">".$tr_lst."</select>";
	//$htm .= "<input name=\"fld14[]\" onchange=\"this.value = this.value.toUpperCase();selTrain(this.value);\" />";
	$htm .= "<div style=\"font-size: 9pt; max-height: 50px; overflow: auto;\">".$tr_lst."</div>";
	$htm .= "</div>"; // end table-cell
	$htm .= "<div style=\"display: table-cell; vertical-align:top; background-color: ".$bgcol."; padding: 4px; border: 1px solid peru; width: 10%;\">&nbsp;</div>"; // start table-cell
	$htm .= "<div style=\"display: table-cell; vertical-align:top; background-color: ".$bgcol."; padding: 4px; border: 1px solid peru; width: 30%;\">"; // start table-cell
	$htm .= "<select id=\"fld7_".$c."\" name=\"fld7[]\" onchange=\"updateOnStatChg(this,document.getElementById('pfld3_".$c."'),document.getElementById('pfld6_".$c."'),document.getElementById('auto_ul_lab".$c."')); hideEle('auto_ul_lab".$c."'); if(this.value == 'UNLOADING'){showEle('auto_ul_lab".$c."');}\">";
	$htm .= "<option value=\"IN TRANSIT\">IN TRANSIT</option><option value=\"P_ORDER\">Purchase Order</option><option value=\"WAYBILL\">Waybill Created</option><option value=\"CAR-ALLOC\">Car Allocated</option><option value=\"FORWARD EMPTY\">Forwarding Empty to Origin</option><option value=\"LOADING\">Loading @ Origin</option><option value=\"IN TRANSIT\">In Transit</option><option value=\"AT I-CHANGE\">Spotted @ Interchange</option><option value=\"UNLOADING\">Unloading @ Destination</option><option value=\"UNLOADED\">Unloaded @ Destination</option><option value=\"RETURNING\">Returning to Origin RR</option><option value=\"CLOSED\">Closed</option>";
	$htm .= "</select>";
	$htm .= "<div id=\"auto_ul_lab".$c."\" style=\"display: none;\">Auto Unload In&nbsp;"; // start auto_ul_lab# div
	$htm .= "<select name=\"unload_days[]\" id=\"unload_days_".$c."\">";
	$htm .= "<option value=\"0\" selected=\"selected\">Manual Unload</option>";
	$htm .= "<option value=\"1\">1 day</option>";
	$htm .= "<option value=\"2\">2 days</option>";
	$htm .= "<option value=\"3\">3 days</option>";
	$htm .= "<option value=\"4\">4 days</option>";
	$htm .= "<option value=\"5\">5 days</option>";
	$htm .= "<option value=\"6\">6 days</option>";
	$htm .= "<option value=\"7\">7 days</option>";
	$htm .= "<option value=\"8\">8 days</option>";
	$htm .= "<option value=\"9\">9 days</option>";
	$htm .= "</select>";
	$htm .= "</div>"; // End auto_ul_lab# div
	$htm .= "</div>"; // End table-cell
	$htm .= "<div style=\"display: table-cell; vertical-align:top; background-color: ".$bgcol."; padding: 4px; border: 1px solid peru; width: 28%;\">&nbsp;</div>"; // start table-cell
	$htm .= "</div>"; // end table-row
 	$htm .= "<div style=\"display: none;\">";
	$htm .= "<input type=\"hidden\" name=\"pfld4[]\" id=\"pfld4_".$c."\" value=\"".$w."\" />"; // prog_cntr and goTo fields are only needed in first prog form!
  	$htm .= "</div>";
   $htm .= "</div>"; // end table

	@mysql_close();
	$sqli->close();
	echo $htm;
}

function dateRebuildReturn($t,$r,$w){ // Non-AJAX date options rebuild
	// Returns option tags for the train indicated in $t	
	// $t = train id
	// $r = railroad id
	// $w = waybill_num
	$t = charConv($t,"[AMP]","&");
	$dbs = db_conn_settings();
	$sqli = new mysqli($dbs['dbhost'],$dbs['dbusername'],$dbs['dbpassword'],$dbs['dbname']);
	$trd = $sqli->query("SELECT sun,mon,tues,wed,thu,fri,sat FROM `ichange_trains` WHERE `train_id` = '".$t."'");
	$trres = $trd->fetch_assoc();

	$op_days = array();
	if(strlen($t) > 0){
		if($trres['sun'] == 1){$op_days[] = "Sun";}
		if($trres['mon'] == 1){$op_days[] = "Mon";}
		if($trres['tues'] == 1){$op_days[] = "Tue";}
		if($trres['wed'] == 1){$op_days[] = "Wed";}
		if($trres['thu'] == 1){$op_days[] = "Thu";}
		if($trres['fri'] == 1){$op_days[] = "Fri";}
		if($trres['sat'] == 1){$op_days[] = "Sat";}
	}

	// Get last progress report info and create date options.
	$prd  = $sqli->query("SELECT * FROM `ichange_progress` WHERE `waybill_num` = '".$w."' ORDER BY `date` DESC, `time` DESC LIMIT 1"); // Latest Progress data
	$progs = $prd->fetch_assoc();
	//$progs = @json_decode(qry("ichange_waybill",$w,"waybill_num","progress"),TRUE);
	$last_prog_date = explode("-",$progs['date']); //explode("-",$progs[count($progs)-1]['date']);
	$last_prog_date_ux = mktime(12,0,0,$last_prog_date[1],$last_prog_date[2],$last_prog_date[0]);
	$dt_opts = "";
	for($joe=date('U',$last_prog_date_ux);$joe<intval(date('U')+(86400*15));$joe=$joe+86400){
		if(in_array(date('D',$joe),$op_days) || count($op_days) == 0){
			$sel = ""; 
			if(date('U') <= $joe && !isset($trselected)){
				$sel = " selected=\"selected\"";
				$trselected = 1;
			}
			$dt_opts .= "<option value=\"".date('Y-m-d',$joe)."\"".$sel.">".date('Y-m-d (D)',$joe)."</option>";
		}
	}

	$sqli->close();
	return $dt_opts;
}

function dateRebuild($t,$r,$w){ // Container for AJAX date options rebuild
	echo dateRebuildReturn($t,$r,$w);
}

// Supporting functions
function db_conn(){
	$dbs = db_conn_settings();
	$dbhost=$dbs['dbhost'];
	$dbusername=$dbs['dbusername']; //"jstan_6_w";
	$dbpassword=$dbs['dbpassword']; //"Js120767";
	$dbname=$dbs['dbname']; //"jstan_general";

	$dbcnx = mysql_connect($dbhost, $dbusername, $dbpassword);
	$seldb = mysql_select_db($dbname);
}

function db_conn_settings(){
	// LIVE SERVER
	$dbhost="db150c.pair.com";//"db72d.pair.com";
	$dbusername="jstan2_2"; //"jstan_6_w";
	$dbpassword="Rs300777"; //"Js120767";
	$dbname="jstan2_general"; //"jstan_general";

	// TESTING
	$LocTst = $_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'];
	if(strpos($LocTst,"www/Applications/") > 0){
		$dbhost="localhost";
		$dbusername="admin";
		$dbpassword="admin";
		//$dbname="jstan_general";
	}
	$tmp = array(
		'dbhost' => $dbhost,
		'dbusername' => $dbusername,
		'dbpassword' => $dbpassword,
		'dbname' => $dbname
	);
	return $tmp;
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
	//db_conn();
	$dbs = db_conn_settings();
	$sqli = new mysqli($dbs['dbhost'],$dbs['dbusername'],$dbs['dbpassword'],$dbs['dbname']);
	$sql_com = "SELECT * FROM `".$tbl."` WHERE `".$ky."` = '".$data."' LIMIT 1";
	$dosql_com = $sqli->query($sql_com); //mysql_query($sql_com);
	$ret = "";
	while($resultcom = $dosql_com->fetch_assoc()){ //mysql_fetch_array($dosql_com)){			
		$ret = $resultcom[$fld];		
	}
	$sqli->close();
		
	return $ret; //Value to return.
}

function qry_cntr($tbl, $data, $ky){
	// Suitable to return ONE field of the db table, where the field name and data to search for are provided.
	// $tbl = the table to search in.		
	// $data = the data string to search for.
	// $ky = the name of the field to search in.
	// $ret = Returned value of the function.
	db_conn();
	$sql = "SELECT `id` FROM `".$tbl."` WHERE `".$ky."` = '".$data."'";
	$qry = mysql_query($sql);
	//if($resultcom = mysql_fetch_array($dosql_com)){
	if(mysql_num_rows($qry)){return mysql_num_rows($qry);}
	else{return 0;} //$resultcom['cntr']; //Value to return.
	//}
}
?>