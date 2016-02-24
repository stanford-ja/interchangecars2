<table>
<?php
// Home 0 view
for($tmp=0;$tmp<count($waybills);$tmp++){
	$td_cla = "td2";
	if(is_int($tmp/2)){$td_cla = "td1";}
	
	// Latest progress report from JSON data
	$prog_all = json_decode($waybills[$tmp]->progress, true);
	$last_prog = count($prog_all) - 1; 
	$fld1_1 = $prog_all[$last_prog]['date'];
	$fld1_2 = $prog_all[$last_prog]['text'];
	$fld1_3 = $prog_all[$last_prog]['map_location'];
	
	// Cars from JSON array
	$wb_cars = @json_decode($waybills[$tmp]->cars, true);

	
$wb_lst = "";
$cols_2_span = 5;
$wb_lst .= "<tr>";
$wb_lst .= "<td valign=\"top\" class=\"".$td_cla." topcell\"><a href=\"edit.php?type=WAYBILL&action=EDIT&id=".$waybills[$tmp]->waybill_num."\">".$waybills[$tmp]->waybill_num."</a> <span style=\"background-color: yellow; font-weight: bold;\">".$waybills[$tmp]->waybill_type."</span><br />".$waybills[$tmp]->date."&nbsp;</td>\n";
//$wb_lst .= "<td valign=\"top\" class=\"".$td_cla."\">$fld1</td>\n";
$wb_lst .= "<td valign=\"top\" class=\"".$td_cla." topcell\">".$waybills[$tmp]->indust_origin_name."&nbsp;</td>\n";
$wb_lst .= "<td valign=\"top\" class=\"".$td_cla." topcell\">".$waybills[$tmp]->indust_dest_name."&nbsp;</td>\n";
$wb_lst .= "<td valign=\"top\" class=\"".$td_cla." topcell\">".$waybills[$tmp]->return_to."&nbsp;</td>\n";
$wb_lst .= "<td valign=\"top\" class=\"".$td_cla." topcell\">".$waybills[$tmp]->status."&nbsp;</td>\n";
$wb_lst .= "<td valign=\"top\" class=\"".$td_cla." topcell\" width=\"18%\" rowspan=\"3\">";
$wb_lst .= "<a href=\"view.php?type=WAYBILL&id=".$waybills[$tmp]->waybill_num."\">View WB</a><br />";

if(isset($_COOKIE['rr_sess']) && @$icr == 0){ 
	$wb_lst .= "<a style=\"font-size: 10pt;\" href=\"edit.php?type=WAYBILL&action=EDIT&id=".$waybills[$tmp]->waybill_num."\">Edit WB</a><br />";
	//$wb_lst .= "<a style=\"font-size: 10pt;\" href=\"edit.php?type=PROGRESS&action=NEW&id=$fld8\">Edit Progress</a><br />";
	$wb_lst .= "<a style=\"font-size: 10pt;\" href=\"javascript:{}\" onClick=\"mess_win('$fld8')\">Send Email</a><br />";
	$wb_lst .= "<a style=\"font-size: 10pt;\" href=\"messages.php?id=".$waybills[$tmp]->waybill_num."\">Messages</a>(".count($messages).")<br />";
}
$wb_lst .= "<a style=\"font-size: 10pt;\" href=\"map.php?wid=".$waybills[$tmp]->waybill_num."\">View Map</a><br />";
if(@$icr == 1 && isset($_COOKIE['rr_sess'])){$wb_lst .= "<a href=\"save.php?type=RR2WB&id=$fld8&rr=".$allRR[$_COOKIE['rr_sess']]->report_mark."\"><span style=\"white-space: nowrap;\">Add RR to Routing</span></a><br />";}				
				
//if($icr == 0){$wb_lst .= "<a style=\"font-size: 10pt;\" href=\"index.php?hde=".$id."\">Hide (for 24hrs)</a>";}
if(@$icr == 0){
	$wb_lst .= "<select name=\"hde\" style=\"font-size: 8pt;\" onchange=\"window.location = 'index.php?hde=".$waybills[$tmp]->id."&hrs=' + this.value;\">
		<option value=\"0\">Hide for</option>
		<option value=\"1\">1 hr</option>
		<option value=\"3\">3 hr</option>
		<option value=\"6\">6 hr</option>
		<option value=\"9\">9 hrs</option>
		<option value=\"12\">12 hrs</option>
		<option value=\"24\">1 day</option>
		</select>";
}
$wb_lst .= "<br /><br />";
if(strlen($waybills[$tmp]->car_num) > 0 || is_array(@json_decode($waybills[$tmp]->cars, true))){
	$g = "";
	if(isset($wb_cars[0])){$g = $this->mricf->get_car_image($wb_cars[0]['NUM'], $wb_cars[0]['AAR_REQD']);}	
	$wb_lst .= $g;
}
$wb_lst .= "&nbsp;</td>\n</tr><tr><td valign=\"top\" colspan=\"".$cols_2_span."\" class=\"".$td_cla."\">";
$wb_lst .= "<span style=\"font-size: 10pt; font-weight: bolder; color: maroon\">Routing: ".$waybills[$tmp]->routing."</span><br />";
$wb_lst .= "<span style=\"font-weight: bolder; color: maroon\">Currently on / allocated to: ".@$fld18b."</span><br />";
if(strlen(@$fld14b) > 0){$wb_lst .= "<span style=\"font-weight: bolder; color: maroon\">In Train: ".@$fld14b."
	</span><br />";}
$wb_lst .= "&nbsp;</td>\n</tr>";
$wb_lst .= "<tr><td valign=\"top\" colspan=\"".$cols_2_span."\" class=\"".$td_cla."\">";
if((strlen($waybills[$tmp]->lading) > 0 || strlen($waybills[$tmp]->notes) > 2)){
	$wb_lst .= "<div style=\"float: right; background-color:#FFFF99; padding: 3px; border: 1px solid brown; max-width: 200px; text-align: right; font-size: 9pt\">".$waybills[$tmp]->lading;
	if(strlen($waybills[$tmp]->notes) > 2){$wb_lst .= "<br /><span style=\"color: brown; font-weight: bold;\">[ ".$waybills[$tmp]->notes." ]</span></div>";}
	$wb_lst .= "</div>";
}
$wb_lst .= "<span style=\"font-size: 10pt;\">".$fld1_1." - ".$fld1_2."</span>";
if(is_array($wb_cars)){
	$carNum = $wb_cars;
	for($cn=0;$cn<count($carNum);$cn++){
		if(strlen($carNum[$cn]['NUM']) > 0){
			$wb_lst .= "<br /><span style=\"color: #4169E1; font-size: 10pt; font-weight: bold\">";
			if(array_key_exists($carNum[$cn]['NUM'],$myCars)){$wb_lst .= "<a href=\"manage.php?id=".$myCars[$carNum[$cn]['NUM']]->id."&view=cars&trans=EDIT\">";}
			$wb_lst .= $carNum[$cn]['NUM']." (".$carNum[$cn]['AAR'].") (".@$allRR[$carNum[$cn]['RR']]->report_mark.")";
			if(array_key_exists($carNum[$cn]['NUM'],$myCars)){$wb_lst .= "</a>";}
			$wb_lst .= "</span> ";
			if(isset($myCars[$carNum[$cn]['NUM']])){$specInst = @$myCars[$carNum[$cn]['NUM']]->special_instruct;}
			else{$specInst = @$myCars[$carNum[$cn]]->special_instruct;}
			if(strlen($specInst) > 0){$wb_lst .= "<span style=\"font-weight: bold\"> - ".$specInst."</span>";$specInst = "";}
		}
	}				
}
//if(strlen($fld1_3) > 0){$wb_lst .= "<div id=\"mapcanvas".$fld8."\" style=\"width: 400px; height: 300px; display:none;\"><br /><a href=\"\" onClick=\"document.getElementById('mapcanvas".$fld8."').style.display = 'none'\">Close</a></div>";}
$wb_lst .= "</td></tr>\n";
echo $wb_lst;
}
?>
</table>