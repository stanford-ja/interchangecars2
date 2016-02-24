<!-- This file is included in the edit.php file where appropriate // -->
<?php

/*
//echo "<pre>"; print_r($rrArr); echo "</pre><hr />";

		$car_rr_opts = $rr_afils;
		
		//$car_rr_opts = array($_COOKIE['rr_sess']);
		//if($rrArr[$_COOKIE['rr_sess']]['show_affil_wb'] == 1){
		//	$afil_rr = str_replace(" ","",$rrArr[$_COOKIE['rr_sess']]['affiliates']);
		//	$afil_rr = explode(";",$afil_rr);
		//	$rrArr_kys = array_keys($rrArr);
		//	for($ri=0;$ri<count($rrArr_kys);$ri++){
		//		if(in_array($rrArr[$rrArr_kys[$ri]]['report_mark'], $afil_rr)){ $car_rr_opts[] = $rrArr_kys[$ri]; }
		//	}
		//}

		$fld1 = date('Y-m-d');
		// $fld2 = "";
		// $fld3 = "";
		$fld4 = "";
		$fld5 = "";
		$fld6 = "";
		$fld7 = $status_dropdown;
		$fld8 = date('Ymdhis');
		$fld9 = "";
		$fld10 = "";
		$fld11 = "";
		$fld12 = "";
		$fld13 = "";
		$fld14 = "";
		$fld15 = "";
   	$fld16 = "";
   	$fld17 = "";
   	$fld18 = "";
   	$fld19 = "";
   	$fld20 = "";
   	$fld21 = array();
	
		$swapLnk = "";
		$rm_restricted_view = "";
		if($action != "NEW"){
			include('waybill_query.php');

			// Start check for show_allocto_only rrs.
			$rm_restricted_view = $this->mricf->rrArrAllocToOnly($rrArr,$fld6);
			// End check for show_allocto_only rrs.
			
			$swapLnk = "&nbsp;<a href=\"edit.php?action=EDIT&type=WAYBILL&id=".$fld8."&swap=1\">Swap origin / destination, mark Lading as MT</a>";
			if(isset($_GET['swap'])){
				$swapLnk = "&nbsp;<a href=\"edit.php?action=EDIT&type=WAYBILL&id=".$fld8."\">Original origin / destination / lading</a>";
				$fld4_tmp = $fld4;
				$fld5_tmp = $fld5;
				$fld2_tmp = $fld2; 
				$fld3_tmp = $fld3;
				//$od_orig_ind_op = @$other_data['orig_ind_op'];
				//$od_dest_ind_op = @$other_data['dest_ind_op'];
				
			
				$fld4 = $fld5_tmp;
				if(strlen($fld19) > 0){$fld5 = $fld19;}else{$fld5 = $fld4_tmp;} //$fld4_tmp;
				$fld2 = $fld3_tmp;
				$fld3 = $fld2_tmp;
				$other_data['orig_ind_op'] = @$other_data['dest_ind_op']; // = $od_dest_ind_op;
				unset($other_data['dest_ind_op']); // = $od_orig_ind_op;
				$fld11 = "MT";
			}
		}else{
			$fld2 = 0;
			$fld3 = 0;
		}	
		
		// To get info passed from AvailCars facility (edit.php?type=AVAILCARS)
		if(isset($_GET['fld9'])){$fld9 = $_GET['fld9'];$fld21[0]['NUM'] = $_GET['fld9'];} // 'car_num' from Avail Cars
		if(isset($_GET['fld10'])){$fld10 = $_GET['fld10'];$fld21[0]['AAR'] = $_GET['fld10']; $fld21[0]['AAR_REQD'] = $_GET['fld10'];} // 'car_type' from Avail Cars
		if(isset($_GET['fld2'])){$fld2 = $_GET['fld2'];$fld21[0]['RR'] = $_GET['fld2'];} // 'rr_id_from' from Avail Cars
		if(isset($_GET['fld4'])){
			 // 'from' from Avail Cars
			 // Can't have REQUIRED as pos = 0! So search for EQUIRED, just in case word is at pos=0
			$fld4 = $_GET['fld4'];
			if(isset($_GET['fld1'])){$fld1 = $_GET['fld1'];}
			if(!isset($_GET['genload'])){$fld17 = "BECOMES AVAILABLE AT ORIGINATING RR INTERCHANGE POINT AROUND ".$fld1;}
			if(strpos(strtoupper($_GET['fld4']),"EQUIRED") > 0){
				// Can't search for REQUIRED as it can be pos = 0 (first char of string=0)! 
				// So search for EQUIRED, just in case word REQUIRED is at pos=0.
				$fld1 = date('Y-m-d');
				$fld4 = "";				
				$fld5 = $_GET['fld4'];
				$fld3 = $fld2;
				$fld2 = 0;
				$fld17 = "NEEDS TO BE AT INTERCHANGE LOCATION FOR DESTINATION RR BY ".$_GET['fld1'];
			}
		}
		//if(isset($_GET['fld1'])){$fld1 = $_GET['fld1'];} // 'date_avail' from Avail Cars
		if(isset($_GET['fld11'])){$fld11 = $_GET['fld11'];}	// 'lading' from Avail Cars
		if(isset($_GET['fld14'])){$fld14 = $_GET['fld14'];}
		$sql_cars = "SELECT `ichange_cars`.* FROM `ichange_cars` WHERE `ichange_cars`.`rr` = '".$_COOKIE['rr_sess']."' ORDER BY `bad_order` ASC, `aar_type` ASC, `car_num` ASC";
		//$sql_cars = "SELECT `ichange_cars`.*, LOCATE(`ichange_cars`.`car_num`,`ichange_waybill`.`cars`) AS `in_wb` FROM `jstan_general`.`ichange_cars`,`jstan_general`.`ichange_waybill` WHERE `ichange_cars`.`rr` = '".$_COOKIE['rr_sess']."' ORDER BY `ichange_cars`.`bad_order` ASC, `ichange_cars`.`car_num` ASC";
		//echo $sql_cars; exit();
		$dosql_cars = mysql_query($sql_cars);
		$cars_lst = "";
		$prev_aar = "";
		while($result_cars = mysql_fetch_array($dosql_cars)){
			$in_wb = q_cntr("ichange_waybill", "`cars` LIKE '%%\"".$result_cars['car_num']."\",\"AAR\":\"".$result_cars['aar_type']."\",\"RR\":\"".$_COOKIE['rr_sess']."\"%%' AND `status` != 'CLOSED' AND `waybill_num` != '".$fld8."'");
			if($in_wb == 0){
				$cars_id = $result_cars['id'];
				$fldcars_2 = $result_cars['car_num'];
				$fldcars_3 = $result_cars['aar_type'];
				$fldcars_3_grp = substr($result_cars['aar_type'],0,1);
				$fldcars_4 = $result_cars['desc'];
				$fldcars_5 = $result_cars['rr'];

				if(strlen($fldcars_4) > 0){$fldcars_4 = " - ".$fldcars_4;}				
				if(strlen($fldcars_4) > 30){
					$fld4_2 = substr($fldcars_4, 0, 30);
				}

				if($prev_aar != $fldcars_3_grp){$cars_lst .= "<option style=\"background-color: brown; color: white; font-weight: bold;\">AAR Type: ".$fldcars_3_grp."</option>";}
				$fldcars_5b = qry("ichange_rr", $fldcars_5, "id", "report_mark");
				$styl = "color: black;";
				$fldcars_3b = "";
				if($result_cars['bad_order'] == 1){$styl = "color: #888;"; $fldcars_3b .= " [BAD ORDERED]";}
				if(strlen($fldcars_2) < 20){$cars_lst .= "<option style=\"".$styl."\" value=\"".$fldcars_2.",".$fldcars_3.",".$fldcars_5b."\">".$fldcars_2.",".$fldcars_3.$fldcars_3b.",".$fldcars_5b.$fldcars_4."</option>\n";}
				$prev_aar = $fldcars_3_grp;

			}
		}		
		
		$sql4 = "SELECT * FROM `ichange_aar` ORDER BY `aar_code` ASC";
		$dosql4 = mysql_query($sql4);
		$aar_lst = "";
		while($result4 = mysql_fetch_array($dosql4)){
			$fld4_1 = $result4['aar_code'];
			$fld4_2 = $result4['desc'];
			if(strlen($fld4_2) > 30){
				$fld4_2 = substr($fld4_2, 0, 30);
			}
			
			$aar_lst .= "<option value=\"".$fld4_1."\">[".$fld4_1."] - ".$fld4_2."</option>";
		}
			
		include('rr_query.php');
		
		// Start Transhipped Waybills List
		$twbs = "";
		$fld8_t = explode("T",$fld8);
		$ts1 = "SELECT `waybill_num` FROM `ichange_waybill` WHERE `waybill_num` LIKE '".$fld8."%' AND `waybill_num` != '".$fld8."'";
		$tq1 = mysql_query($ts1);
		while($tr1 = mysql_fetch_array($tq1)){
			$twbs .= "&nbsp;<a href=\"edit.php?type=WAYBILL&id=".$tr1['waybill_num']."&action=EDIT\">".$tr1['waybill_num']."</a>";
		}
		// End Transhiopped Waybills List

		$trains_lst = "<option value=\"NOT ALLOCATED\">NOT ALLOCATED</option>";
       $aut_cntr = 0;
       $js_tr_arr = "";// "var js_tr_arr = new Array;\n";
      // $js_ln = " onchange=\"autTrSel(this.value); selTrain(this.value,'','','', {'target':'train_disp_span'}); document.getElementById('train_disp_span').style.display = 'block';\"";
       $js_ln = " onchange=\"selTrain(this.value,'','','', {'target':'train_disp_span'}); document.getElementById('train_disp_span').style.display = 'block';\"";

		$trains_lst .= trainOpts(array('rr'=>$_COOKIE['rr_sess'],'auto'=>"Y"),$trainsArr); //$rr=0,$auto="Y");

		$fldrrorig_1 = $fld2; // $result2['id'];
		$fldrrorig_2 = "";
		if(strlen($fld2) > 0){$fldrrorig_2 = qry("ichange_rr", $fld2, "id", "rr_name");} //$result2['rr_name'];
		$fldrrdest_1 = $fld3;// $result3['id'];
		$fldrrdest_2 = "";			
		if(strlen($fld3) > 0){$fldrrdest_2 = qry("ichange_rr", $fld3, "id", "rr_name");} // $result3['rr_name'];

		$render_fld1 = frmTag("readonly", "fld1", $fld1);
		
		$render_fld2 = frmTag("select", "fld2", $fldrr_2);
		$render_fld3 = frmTag("select", "fld3", $fldrr_3);
		$render_fld4 = "<input type\"text\" name=\"fld4\" id=\"fld4\" value=\"".$fld4."\" size=\"50\" onKeyUp=\"industAutoComp(this.value,'ichange_indust','fld4','fld4',1, {'target':'fld4_span'}); document.getElementById('fld4_span').style.display = 'block';\" onfocus=\"showEle('orig_ind_info');\" onblur=\"hideEle('orig_ind_info');\" />
			<div id=\"fld4_span\"  style=\"display: none; border: 1px solid black; background-color: yellow; font-size: 9pt; padding: 5px; max-height: 100px; overflow: auto;\"></div>"; // frmTag("input", "fld4", $fld4, 50);
		$render_fld5 = "<input type\"text\" name=\"fld5\" id=\"fld5\" value=\"".$fld5."\" size=\"50\" onKeyUp=\"industAutoComp(this.value,'ichange_indust','fld5','fld5',2, {'target':'fld5_span'}); document.getElementById('fld5_span').style.display = 'block';\" onfocus=\"showEle('dest_ind_info');\" onblur=\"hideEle('dest_ind_info');\" />
			<div id=\"fld5_span\"  style=\"display: none; border: 1px solid black; background-color: yellow; font-size: 9pt; padding: 5px; max-height: 100px; overflow: auto;\"></div>"; //frmTag("input", "fld5", $fld5, 50);
		$render_fld6 = "<input type=\"text\" id=\"fld6\" size=\"25\" name=\"fld6\" value=\"".$fld6."\" onKeyUp=\"autoComp(this.value,'ichange_waybill','routing','fld6', {'target':'fld6_span'}); document.getElementById('fld6_span').style.display = 'block';\" />
                        <div id=\"fld6_span\" style=\"display: none; border: 1px solid black; background-color: yellow; font-size: 9pt; padding: 5px;\">
                        </div>"; //frmTag("input", "fld6", $fld6).;
		$render_fld7 = "<select id=\"fld7\" name=\"fld7\" onchange=\"updateOnStatChg(); hideEle('auto_ul_lab'); if(this.value == 'UNLOADING'){showEle('auto_ul_lab');}\">\n
							".$fld7."\n".rr_ichange_lst(qry("ichange_waybill", $fld8, "waybill_num", "status"),0,array('ordby' => "FIELD(`report_mark`, '".$rrArr[$_COOKIE['rr_sess']]['report_mark']."') DESC, `report_mark`"))."</select>"; //frmTag("select", "fld7", $fld7.rr_ichange_lst(qry("ichange_waybill", $fld8, "waybill_num", "status")));
		$render_fld8 = frmTag("readonly", "fld8", $fld8);
		//$render_fld9 = frmTag("textarea", "fld9", $fld9, 60, 2);
		$render_fld9 = "<input name=\"fld9\" size=\"50\" id=\"fld9\" onChange=\"carUsed(this.value);\" value=\"".$fld9."\">";//"<textarea name=\"fld9\" cols=\"60\" rows=\"1\" id=\"fld9\" onChange=\"carUsed(this.value);\">".$fld9."</textarea>";
		$render_fld10 = frmTag("hidden", "fld10", $fld10);// if($action == "NEW"){frmTag("select", "fld10", "<option selected value=\"".$fld10."\">".$fld10."</option>".$aar_lst);}
		$render_fld11 = "<input type=\"text\" id=\"fld11\" name=\"fld11\" value=\"".$fld11."\" onKeyUp=\"autoComp(this.value,'ichange_commod','commod_name,generates','fld11', {'target':'fld11_span'}); document.getElementById('fld11_span').style.display = 'block';\" />
			<div id=\"fld11_span\" style=\"display: none; border: 1px solid black; background-color: yellow; font-size: 9pt; padding: 5px;\"></div>";
		$render_fld12 = "<input name=\"fld12\" size=\"50\" id=\"fld12\" onChange=\"carUsed(this.value);\" value=\"".$fld12."\">"; //"<textarea name=\"fld12\" cols=\"60\" rows=\"1\" id=\"fld12\" onChange=\"carUsed(this.value);\">".$fld12."</textarea>"; // frmTag("textarea", "fld12", $fld12, 60, 2);
		$render_fld13 = frmTag("select", "fld13", "<option selected value=\"".$fld13."\">".$fld13."</option>".$aar_lst);
		$render_fld14 = "<select id=\"fld14\" name=\"fld14\"".$js_ln.">\n
                        <option selected value=\"".$fld14."\">".$fld14."</option>\n".$trains_lst."
                        </select>\n
                        <div id=\"train_disp_span\" style=\"display: none; border: 1px solid black; background-color: yellow; font-size: 9pt; padding: 5px;\">
                        </div>";
		$render_fld15 = frmTag("input", "fld15", $fld15, 15);
		$render_fld16 = frmTag("select", "fld16", "<option value=\"".$fld16."\">".$fld16."</option><option value=\"\">STANDARD</option><option value=\"INTERNAL\">INTERNAL</option>");
		$render_fld17 = frmTag("textarea", "fld17", $fld17, 50, 4);
		$render_fld18 = frmTag("select", "fld18", "<option value=\"".$fld18."\">".qry("ichange_rr", $fld18, "id", "rr_name")."</option>".$fldrr_on."<option value=\"\" style=\"background-color: yellow;\">NOT ALLOCATED</option>");
		$render_fld19 = frmTag("input", "fld19", $fld19);
		$render_fld21 = "<span style=\"display: none;\">".frmtag("textarea", "fld21", json_encode($fld21), 50,3)."</span>";

		// progress display, fields
		$last_fld2_4 = "";
		$last_status = "";
		$j_cntr = 0;if(isset($prog)){$j_cntr=count($prog);}
		$render_lst = "";
		while($j_cntr > 0){
			$j_cntr = $j_cntr - 1;
			if(!isset($prog[$j_cntr]['status'])){$prog[$j_cntr]['status'] = "";}
			if(!isset($prog[$j_cntr]['train'])){$prog[$j_cntr]['train'] = "";}
			if(!isset($prog[$j_cntr]['rr'])){$prog[$j_cntr]['rr'] = 0;}
			if(!isset($prog[$j_cntr]['exit_location'])){$prog[$j_cntr]['exit_location'] = "";}
			$rp_mrk = ""; if(isset($rrArr[$prog[$j_cntr]['rr']]['report_mark'])){$rp_mrk = $rrArr[$prog[$j_cntr]['rr']]['report_mark'];}
			$exit_wp = ""; if(strlen($prog[$j_cntr]['exit_location']) > 0){$exit_wp = " --> ".$prog[$j_cntr]['exit_location'];}
			$render_lst .= "<tr><td valign=\"top\" class=\"td1\">";
			$render_lst .= $prog[$j_cntr]['date']."&nbsp;".@$prog[$j_cntr]['time']."&nbsp;<br />".@$prog[$j_cntr]['tzone']."</td>";
			$render_lst .= "<td valign=\"top\" class=\"td1\">";
			$render_lst .= "<span style=\"float: right; font-weight: bold;\">".$prog[$j_cntr]['status']."</span>".$prog[$j_cntr]['text']." (JSON)</td>";
			$render_lst .= "<td valign=\"top\" class=\"td1\">".$prog[$j_cntr]['train'].$exit_wp."</td>";
			$render_lst .= "<td valign=\"top\" class=\"td1\">".$rp_mrk."</td>";
			$render_lst .= "<td valign=\"top\" class=\"td1\">".$prog[$j_cntr]['map_location']."</td>";
			$render_lst .= "</td></tr>";
		}

		// Create array of map_location's setting array keys as map_location, then sort keys. 
		$sql_lst1 = "SELECT `progress` FROM `ichange_waybill`";
		$qry_lst1 = mysql_query($sql_lst1);
		$map_lst = "";
		$map_arr = array();
		while($res_lst1 = mysql_fetch_array($qry_lst1)){
			$map_json = json_decode($res_lst1['progress'], true);
			for($mi=0;$mi<count($map_json);$mi++){
				$map_tmp = $map_json[$mi]['map_location'];
				if(strlen($map_tmp) > 0){$map_arr[$map_tmp] = 1;}			
			}
		}
		$map_kys = array_keys($map_arr);
		sort($map_kys);
		for($mi=0;$mi<count($map_kys);$mi++){
			$map_disp = substr($map_kys[$mi],0,25);
			$map_lst .= "<option value=\"".$map_kys[$mi]."\">".$map_disp."</option>\n";
		}			

		// Progress field rendering variables
		//$renderprog_fld1 = frmTag("hidden", "id", $id);
		$renderprog_fld1 = frmTag("hidden", "id",$fld8);
		$renderprog_fld2 = frmTag("input", "pfld2", date('Y-m-d'), 12);
		$renderprog_fld3 = frmTag("textarea", "pfld3", "", 40, 4);
		$renderprog_fld6 = frmTag("input", "pfld6", $last_fld2_4, 16);
		$renderprog_fld6b = "<select id=\"pfld6b\" name=\"fld6b\" size=\"4\" onChange=\"document.getElementById('pfld6').value = this.value\" style=\"min-width: 200px;\">".$map_lst."</select>";
		//$renderprog_fld4 = frmTag("hidden", "pfld4", $id);
		$renderprog_fld4 = frmTag("hidden", "pfld4", $fld8);
*/
?>

			<?php $aut_name = "Exit Location"; $aut_name2 = "Entry Location"; ?>			
			<tr>
				<td colspan="3">
			<?php 
			$woopwoopwoop = "";
			if(isset($_GET['unsaved'])){
				$woopwoopwoop .= "<span style=\"color: red;\">THIS WAYBILL HAS UNSAVED INFORMATION!</span> Please click the Save Details button to update the waybill or the changes will be lost!";
			}
			if($this->mricf->q_cntr("ichange_auto", "`waybill_num` = '".$fld8."'") > 0){
				$woopwoopwoop .= "This waybill has automatic activity to complete in the future. 
				Doing any of the following then clicking the Save Changes button will cancel <u>ALL</u> of those automatic activities:
				<ul>
					<li>Selecting a status that inserts data into the Progress text box.</li>
					<li>Selecting the <u>Unloading @ Destination</u> option in the Status selector and selecting a value in the Auto Unload In selector <u>other than</u> Manual Unload.</li>
					<li>Entering data into the Progress text box.</li>
					<li>Selecting a different <u>Auto Train</u>.</li>
				</ul>
				If you wish for the future automatic events scheduled for this waybill to be completed please dont do any of those things listed above."; 
			}
			
			if(strlen($woopwoopwoop) > 0){
				echo "<div style=\"border: 2px solid red; background-color: yellow; padding: 4px; font-size: 12pt; font-weight: bold;\">";
				echo $woopwoopwoop;
				echo "</div>";
			} 
			?>
					<span style="font-size: 10pt;">
					<?php echo $swapLnk; ?>&nbsp;&nbsp;&nbsp;<a href="#progfrm">Progress</a>&nbsp;&nbsp;&nbsp;
					<a href="messages.php?id=<?php echo $fld8; ?>">Messages</a>&nbsp;
					<a href="save.php?type=TRANSHIP&id=<?php echo $fld8; ?>" style=\"color: yellow;\">Tranship</a>&nbsp;
					<?php if(strpos ($fld8,"T") > 0){ ?>
					<a href="edit.php?type=WAYBILL&id=<?php echo $fld8_t[0]; ?>&action=EDIT" style=\"color: yellow;\">Orig. Waybill</a>&nbsp;
					<?php } ?>
					</span>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="background-color: #A0522D; color: yellow; padding: 5px;">
					Waybill Number&nbsp;<?php echo $render_fld8; ?>&nbsp;
					<?php if(strlen($twbs) > 0){
						echo "<strong> WAYBILLS:";
						echo $twbs;
						echo "</strong>";
					} ?>
					<br />
              	<span style="white-space: nowrap">
					Date&nbsp;<?php echo $render_fld1; ?>&nbsp;&nbsp;&nbsp;
              	Waybill Type&nbsp;
              	<?php echo $render_fld16; ?>&nbsp;&nbsp;&nbsp;&nbsp;
              	Purchase Order #&nbsp;<span class="small_txt">(if applicable)</span>&nbsp;
              	<?php echo $render_fld15; ?>
              	</span>
             </td>
         </tr>
			<tr>
			<td colspan="3" style="background-color: peru;">&nbsp;&nbsp;Cars details</td>
			</tr>
			<tr>
				<!--<td>Cars attached to waybill</td>// -->
				<td style="padding: 3px;" colspan="2">
					<?php 
						echo $render_fld21; // $render_fld9;
						echo $render_fld10;
						$my_aar = "";
						$my_car = "";
						$oth_lst = "\n<span style=\"font-size: 9pt; font-weight: bold;\">Cars on waybill</span><br /><div id=\"carsHTM\" style=\"font-size: 9pt; padding: 2px; border: 1px solid #777;background-color: #DEB887;\">\n";
						if(is_array($fld21)){
						for($cn=0;$cn<count($fld21);$cn++){
							/*
							if($fld21[$cn]['RR'] == $_COOKIE['rr_sess']){
								$my_aar = @$fld21[$cn]['AAR'];
								$my_car = @$fld21[$cn]['NUM'];
							}else{
							*/
								@$oth_lst .= "<strong>".$fld21[$cn]['NUM']."</strong> (".$fld21[$cn]['AAR'].") <em>[".$rrArr[$fld21[$cn]['RR']]['report_mark']."]</em>";
								if(in_array($fld21[$cn]['RR'],$rr_afils)){$oth_lst .= "&nbsp;<a href=\"javascript:{}\" onclick=\"delCar('".$cn."')\">Del</a>";}
								$oth_lst .= "<br />\n";
							//}
						}
						}
						$oth_lst .= "</div>\n";
						echo "<table style=\"margin-bottom: 5px; background-color: #F4A460;\">";
						echo "<tr><td colspan=\"2\"><strong>Cars attached to waybill</strong></td><td rowspan=\"4\">".$oth_lst."</td></tr>";
						echo "<tr><td>Car </td><td><input name=\"fld21_car\" id=\"fld21_car\" value=\"".$my_car."\" style=\"width: 150px;\" onchange=\"this.value=this.value.toUpperCase(); carUsed(this.value);\" /></td>";
						//echo "<td rowspan=\"3\">".$oth_lst."</td>";
						echo "</tr>";
						echo "<tr><td>AAR </td><td><select name=\"fld21_aar\" id=\"fld21_aar\" style=\"width: 150px; font-size: 9pt;\">
							<option value=\"".$my_aar."\">".$my_aar."</option>".$aar_lst.
							"</select></td></tr>";
						echo "<tr><td>Attach to RR</td><td>";
						echo "<select name=\"fld21_rr\" id=\"fld21_rr\">";
						for($cri=0;$cri<count($car_rr_opts);$cri++){
							echo "<option value=\"".$car_rr_opts[$cri]."\">".$rrArr[$car_rr_opts[$cri]]['report_mark']."</option>";
						}
						echo "</select>&nbsp;";
						echo "<input type=\"button\" value=\"Add\" onclick=\"addCar();\" />";
						echo "</td></tr>";
						echo "<tr><td colspan=\"3\"><span id=\"fld9drop\">";
						echo "Car Select <select id=\"fld9sel\" name=\"fld9sel\" style=\"width: 320px;\" onChange=\"var expSt = explodeStr('\,',document.getElementById('fld9sel').value); option0 = new Option(expSt[1],expSt[1]); document.form1.fld21_car.value = expSt[0]; document.form1.fld21_aar.options[0] = option0; document.form1.fld21_aar.options[0].selected = true;\"><option value=\"\">--Select Car Number or enter in Field above--</option>".$cars_lst."</select>";
						echo "</span>";
						echo "<br /><span style=\"font-size: 8pt;\">(Only cars not already allocated to a waybill are shown in the Car Selector!)</span>";
						echo "</td></tr></table>";
						//echo $oth_lst;
					?>
					<br />
				</td>
				<td rowspan="1" style="vertical-align: top; background-color: yellow; padding: 3px;">
					<strong><u>Car Search</u></strong><br />&nbsp;
					Find cars at: <input type="text" size="20" name="mtcars" id="mtcars" onkeyup="carsAutoFind(this.value,'location','mtcars', '', {'preloader':'mtcars_load', 'target':'mtcars_span'});" />
					<span id="mtcars_load" style="visibility: hidden;">Loading...</span>
					<br />
					<div id="mtcars_span" style="font-size: 9pt; max-height: 125px; overflow: auto;">&nbsp;</span>
				</td>
			</tr>
			<!--
			<tr>
				<td>Car AAR Code</td>
				<td><?php echo $render_fld10; ?></td>
			</tr>
			<tr>
				<td>Alias Report<br />Mark & Number</td>
				<td style="border: 1px solid black; background-color: lightgrey; padding: 3px;">
					<?php echo $render_fld12; ?>
					<span id="fld12drop">
					<select id="fld12sel" name="fld12sel" style="width: 320px;" onChange="var expSt = explodeStr('\,',document.getElementById('fld12sel').value); hideEle('fld12drop'); option0 = new Option(expSt[1],expSt[1]); document.form1.fld12.value = expSt[0]; document.form1.fld13.options[0] = option0; document.form1.fld13[0].selected = '1'"><option value="">--Select Car Number or enter in Field above--</option><?php echo $cars_lst; ?></select>
					</span>
				</td>
			</tr>
			<tr>
				<td>Alias AAR Code</td>
				<td><?php echo $render_fld13; ?></td>
			</tr> // -->
			<tr>
			<td colspan="3" style="background-color: peru;">&nbsp;&nbsp;Industries / Locations details</td>
			</tr>
			<tr>
				<td>Lading</td>
				<td style="border: 1px solid black; background-color:#bbb; padding: 3px;"><?php echo $render_fld11; ?>
				<?php if(($fld11  == "MT" || $fld11 == "EMPTY" || $fld11  == "MTY") && isset($other_data['commodity'])){
					echo "<br /><span style=\"font-size: 9pt;\">was : <a href=\"javascript:{}\" onclick=\"document.getElementById('fld11').value = '".$other_data['commodity']."'\">".$other_data['commodity']."</a></span>";
				} ?>
				</td>
				<td valign="top" rowspan="4">
				Map Location<br /><?php echo $renderprog_fld6; ?><br />
				<?php echo $renderprog_fld6b; ?>
				</td>
			</tr>
			<tr>
				<td>Origin</td>
				<td><a name="ind1"></a>
					<?php echo $render_fld4; ?>
					<div id="fld4_indDescDiv" style="display: <?php if(isset($other_data['orig_ind_op'])){echo "block;";}else{echo "none;";} ?>">
					<textarea cols="50" name="fld4_indDesc" id="fld4_indDesc" onchange="this.parent.style.display = 'block'"><?php echo @$other_data['orig_ind_op']; ?></textarea>
					</span>
					<span style="font-size: 9pt; display: none;" id="orig_ind_info">Enter the commodity to ship, the industry name, city or state for a list of industries.<br /></span>
				</td>
			</tr>
			<tr>
				<td>Destination</td>
				<td><a name="ind2"></a>
					<?php echo $render_fld5; ?>
					<div id="fld5_indDescDiv" style="display: <?php if(isset($other_data['dest_ind_op'])){echo "block;";}else{echo "none;";} ?>">
					<textarea cols="50" name="fld5_indDesc" id="fld5_indDesc" onchange="this.parent.style.display = 'block'"><?php echo @$other_data['dest_ind_op']; ?></textarea>
					</div>
					<span style="font-size: 9pt; display: none;" id="dest_ind_info">Enter the commodity to ship, the industry name, city or state for a list of industries.<br /></span>
				</td>
			</tr>
			<tr>
				<td>Return to</td>
				<td>
					<?php echo $render_fld19; ?>
				</td>
				<td>
				</td>
			</tr>
			<tr>
			<td colspan="3" style="background-color: peru;">&nbsp;&nbsp;Railroad Operation details</td>
			</tr>
			<tr>
				<td>From</td>
				<td><?php echo $render_fld2; ?></td>
				<td rowspan="5" style="vertical-align: top;">Notes<br />
				<?php echo $render_fld17; ?>
				</td>

			</tr>
			<tr>
				<td>To</td>
				<td><?php echo $render_fld3; ?></td>
			</tr>
			<tr>
				<td>Assigned to</td>
				<td><?php echo $render_fld18; ?>
				<?php if(count($rm_restricted_view) > 0 && $action != "NEW"){
					echo "<br />";
					echo "<div style=\"font-weight: bold; padding: 5px; font-size: 9pt; background-color: yellow;\">";
					echo "<span style=\"display: inline; background-color: red; color: white;\">&nbsp;";
					for($rmc=0;$rmc<count($rm_restricted_view);$rmc++){
						if($rmc > 0){echo ", ";}
						echo $rm_restricted_view[$rmc];
					}
					echo "&nbsp;</span>";
					echo " railroad/s are set to only show trains allocated to them on the Home page. Assign To them when the car/s are to be under their control.";
					echo "</div>";
				} ?>
				</td>
			</tr>
			<tr>
				<td>Routing</td>
				<td><?php echo $render_fld6; ?></td>
			</tr>
			<tr>
				<td>Status</td>
				<td>
					<?php echo $render_fld7; ?>
					<div id="auto_ul_lab" style="display: none;">Auto Unload In&nbsp;
						<select name="unload_days" id="unload_days">
							<option value="0" selected="selected">Manual Unload</option>
							<option value="1">1 day</option>
							<option value="2">2 days</option>
							<option value="3">3 days</option>
							<option value="4">4 days</option>
							<option value="5">5 days</option>
							<option value="6">6 days</option>
							<option value="7">7 days</option>
							<option value="8">8 days</option>
							<option value="9">9 days</option>
						</select>
					</div>
				</td>
			</tr>
			<tr>
			<td colspan="3" style="background-color: peru;">&nbsp;&nbsp;Train details</td>
			</tr>
			<tr>
				<td valign="top"><span style="white-space: nowrap;">In / Allocated To</span></td>
				<td><?php echo $render_fld14; ?>
					<div style="font-size: 10pt; max-width: 600px; display: none" id="trains_info">
					<span style="float: right;">&nbsp;&nbsp;<a href="javascript:{}" onclick="document.getElementById('trains_info').style.display = 'none';">[X]</a></span>
					Automatic Trains are trains that will automatically deliver the car/s on a waybill to the <?php echo $aut_name; ?>, 
					or the Destination for the train if no <?php echo $aut_name; ?> is indicated. 
					To set a train as Automatic, enter a number greater than zero in the <strong>Days For Auto Train</strong> 
					field in the <strong>Trains Management</strong> application. 
					Automatic Trains are listed in color in the drop down list above!
					</div>
					<br />					
					<a href="javascript:{}" onclick="document.getElementById('trains_info').style.display = 'block';">Help</a> | 
					<a href="edit2.php?type=TRAINS&action=NEW" target="_blank">Manage Trains</a>.
 
				</td>
				<td style="vertical-align: top;">
				</td>
			</tr>
			<tr>
				<td valign="top"><?php echo $aut_name2; ?></td>
				<td>
					<input type="input" readonly="readonly" id="entry_waypoint" name="entry_waypoint" value="" />&nbsp;
					<a href="javascript:{}" onclick="document.getElementById('entry_waypoint').value = '';">Clear</a>
				</td>
			</tr>
			<tr>
				<td valign="top"><?php echo $aut_name; ?></td>
				<td>
					<input type="input" id="exit_waypoint" name="exit_waypoint" value="" onchange="document.form1.pfld6.value = this.value;" />&nbsp;<a href="javascript:{}" onclick="document.getElementById('waypoint_info').style.display = 'block';">Help</a><br />
					&nbsp;<a href="javascript:{}" onclick="document.getElementById('waypoint_info').style.display = 'block';">Help</a><br />
					
					<div style="font-size: 10pt; max-width: 600px; display: none;" id="waypoint_info">
<span style="float: right;">&nbsp;&nbsp;<a href="javascript:{}" onclick="document.getElementById('waypoint_info').style.display = 'none';">[X]</a></span>						<em>						
						The <strong><?php echo $aut_name; ?></strong> field above is only used when allocating 
						a waybill to an Automatic Train, otherwise it is ignored. 
						To exit an automatic train at a specific location en-route, enter the location in the 
						<strong><?php echo $aut_name; ?></strong> field above. 
						If this waybill is allocated to an Automatic Train and no location is entered in the <strong><?php echo $aut_name; ?></strong> 
						field above, then it will be assumed the car is going to the destination of the train.
						</em>
					</div>
				</td>
			</tr>
		</table>
		<table border="0" width="100%" align="center">
			<tr><td colspan="5" style="background-color: peru;"><a name="progfrm"></a>&nbsp;&nbsp;Progress
			</td></tr>
			<tr>
				<td valign="top"><?php echo $renderprog_fld2; ?><br />
					<select name="pfld7"><?php echo hr(); ?></select>:
					<select name="pfld8"><?php echo mins(); ?></select>
					<br />
					<span style="white-space: nowrap;">Express Save: <input type="checkbox" name="express" id="express" value="Y" /></span><br />
				</td>
				<td valign="top"><?php echo $renderprog_fld3; ?></td>
				<td valign="top">
				</td>
			</tr>
			<tr><td colspan="3">
			<!-- For waybill: // --> <?php echo $renderprog_fld4; ?>
				<?php if(isset($_GET['genload'])){ ?>
				<input type="hidden" name="genload" value="<?php echo $_GET['genload']; ?>" />
				<?php } ?>
				<input type="hidden" name="goTo" value="<?php echo $http_referer; ?>" />
            <input id="submit" name="submit" value="Save Changes" type="submit" />
            <?php if(strlen($http_referer) > 0){ ?>
			<span style="font-size: 7pt;"><em>
			Will be redirected to <strong> 
			<?php echo $http_referer; ?>
			</strong> on save.
			</em></span>
            <?php } ?>
			</td></tr>
				<td class="td_title">Date / Time / Timezone</td>
				<td class="td_title">Progress Report</td>
				<td class="td_title">In Train</td>
				<td class="td_title">On Railroad</td>
				<td class="td_title">Map Location</td>
			</tr>
			<?php echo $render_lst; ?>
