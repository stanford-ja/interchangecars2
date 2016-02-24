<?php $rr_kys = array_keys($rr_options); 
$local=0;
if(strpos($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'],"www/Applications/") > 0){
	$local=1;
}
$mths = array("","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
if(@$myRR[0]->use_tz_time == 1 && strlen(@$myRR[0]->tzone) > 0){	date_default_timezone_set($myRR[0]->tzone);}

?>

			<div style="display: none;">
		<a href="export.php?tbl=ichange_waybill" target="e">Export</a>
	</div>
			<form id="form1" name="form1" method="post" action="../../save_waybill" onSubmit="return chckFrm()" autocomplete="off">
	<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
	<input type="hidden" name="tbl" id="tbl" value="ichange_waybill" />
	<input type="hidden" name="win" id="win" value="" />
	<input type="hidden" name="chkd" id="chkd" value="" />
		<table border="0" width="100%" align="center" style="background-color: transparent; border: none;">						
			<tr>
				<td colspan="3">
				<!-- <h2 style="background-color: red; color: yellow; padding: 4px;">THIS VIEW IS STILL A WORK IN PROGRESS. THE SAVE BUTTON HAS BEEN REMOVED UNTIL BUILD IS COMPLETE! USE THE CURRENT VERSION OF THE MRICF FOR WAYBILL CHANGES.</h2> // -->
			<?php if(count(@$auto_ent) > 0){ ?>
			<div style="border: 2px solid red; background-color: yellow; padding: 4px; font-size: 10pt; font-weight: bold;">
				<div style="float: right; padding: 5px; height: 100px; overflow: auto; font-size: 10pt; border: 1px solid #7CFC00; background-color: #90EE90;">
				<span style="font-size:12pt; text-decoration: underline;">Scheduled Auto Events</span><br />
				<?php for($a=0;$a<count($auto_ent);$a++){echo $auto_ent[$a]->act_date." - ".str_replace("NOT ALLOCATED","",$auto_ent[$a]->train_id)." - ".$auto_ent[$a]->waypoint." - ".$auto_ent[$a]->description."<br />";} ?>
				</div>
				This waybill has automatic activity to complete in the future. 
				Doing any of the following then clicking the Save Changes button will cancel <u>ALL</u> of those automatic activities:
				<ul>
					<li>Selecting a status that inserts data into the Progress text box.</li>
					<li>Selecting the <u>Unloading @ Destination</u> option in the Status selector and selecting a value in the Auto Unload In selector <u>other than</u> Manual Unload.</li>
					<li>Entering data into the Progress text box.</li>
					<li>Selecting a different <u>Auto Train</u>.</li>
				</ul>
				If you wish for the future automatic events scheduled for this waybill to be completed please dont do any of those things listed above.
			</div>			
			<?php } ?>		
			<span style="font-size: 10pt;">
					<?php if($fld11 != "MT" && $fld11 != "MTY"){ ?>
					<a href="../../waybill/swap/<?php echo $id; ?>">Swap origin / destination, mark Lading as MT</a>&nbsp;&nbsp;&nbsp;
					<?php } ?>
					<a href="#progfrm">Progress</a>&nbsp;&nbsp;&nbsp;
					<a href="../../messaging/lst/<?php echo $id; ?>">Messages</a>&nbsp;
					<a href="../../waybill/tranship/<?php echo $id; ?>" style=\"color: yellow;\">Tranship</a>&nbsp;
					<a href="edit.php?type=WAYBILL&id=20120517115321&action=EDIT" style=\"color: yellow;\">Orig. Waybill</a>&nbsp;
				</span>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="background-color: #A0522D; color: yellow; padding: 5px;">
					Waybill Number&nbsp;<input type="hidden" name="fld8" id="fld8" value="<?php echo $fld8; ?>" /><?php echo $fld8; ?>&nbsp;
					<!-- <strong> WAYBILLS:&nbsp;<a href="edit.php?type=WAYBILL&id=20120517115321T11&action=EDIT">20120517115321T11</a></strong> // -->
					<?php if(strlen($twbs) > 0){
						echo "<strong> WAYBILLS:";
						echo $twbs;
						echo "</strong>";
					} ?>
					<br />
              	<span style="white-space: nowrap">
					Date&nbsp;<input type="hidden" name="fld1" id="fld1" value="<?php echo $fld1; ?>" /><?php echo $fld1; ?>&nbsp;&nbsp;&nbsp;
              	Waybill Type&nbsp;
              	<select id="fld16" name="fld16" /><option selected="selected" value="<?php echo $fld16; ?>"><?php echo @$fld16; ?></option><option value="">STANDARD</option><option value="INTERNAL">INTERNAL</option></select>
&nbsp;&nbsp;&nbsp;&nbsp;
              	Purchase Order #&nbsp;<span class="small_txt">(if applicable)</span>&nbsp;
              	<input type="text" id="fld15" name="fld15" size="15" maxsize="15" value="<?php echo $fld15; ?>" />
              	</span>
             </td>
         </tr>
			<tr>
			<td colspan="3" style="background-color: peru;">&nbsp;&nbsp;Cars details</td>
			</tr>
			<tr>
				<!--<td>Cars attached to waybill</td>// -->
				<td style="padding: 3px;" colspan="2">
					<span style="display: none;"><textarea name="fld21" id="fld21" cols="50" rows="3"><?php echo $fld21; ?></textarea>
					</span><input type="hidden" name="fld10" id="fld10" value="<?php echo $fld10; ?>" />
					<table style="margin-bottom: 5px; background-color: #F4A460;">
					<tr><td colspan="2">
					<strong>Cars attached to waybill</strong>
					</td><td rowspan="4">
					<span style="font-size: 9pt; font-weight: bold;">Cars on waybill</span><br />
					<div id="carsHTM" style="font-size: 9pt; padding: 2px; border: 1px solid #777;background-color: #DEB887;min-width: 140px;">&nbsp;</div>
					</td>
					<td rowspan="5" style="font-weight: bold; color: maroon; font-size: 8pt;"><?php echo $sugg_car_types; ?></td>
					</tr>
					<tr><td>Car </td>
					<td><input name="fld21_car" id="fld21_car" value="" style="width: 150px;" onchange="this.value=this.value.toUpperCase(); carUsed(this.value);" />
					</td>
					</tr>
					<tr><td>AAR </td>
					<td><select name="fld21_aar" id="fld21_aar" style="width: 150px; font-size: 9pt;">
							<option value=""></option>
							<?php echo $aar_options; ?>
							</select></td></tr><tr><td>Attach to RR</td>
							<td>
							<select name="fld21_rr" id="fld21_rr">
							<?php for($i=0;$i<count($affil);$i++){echo "<option value=\"".$affil[$i]."\">".$allRR[$affil[$i]]->report_mark."</option>";} ?>
							</select>&nbsp;<input type="button" value="Add" onclick="addCar();" />
							</td></tr>
							<tr><td colspan="3"><span id="fld9drop">
							<?php echo "<pre>"; /* print_r($cars_options);*/ echo "</pre>"; ?>
							Car Select <select id="fld9sel" name="fld9sel" style="width: 320px;" onChange="var expSt = explodeStr('\,',document.getElementById('fld9sel').value); option0 = new Option(expSt[1],expSt[1]); document.form1.fld21_car.value = expSt[0]; document.form1.fld21_aar.options[0] = option0; document.form1.fld21_aar.options[0].selected = true;">
							<option value="">--Select Car Number or enter in Field above--</option>
							<?php $last_aar = ""; for($c=0;$c<count($cars_options);$c++){
								$this_aar = substr($cars_options[$c]['aar_type'],0,1);
								if($last_aar != $this_aar){echo "<option style=\"background-color: brown; color: white; font-weight: bold;\">AAR Type: ".$this_aar."</option>";}
								$opt_val = $cars_options[$c]['car_num'].",".$cars_options[$c]['aar_type'].",".$cars_options[$c]['rr'];
								echo "<option value=\"".$opt_val."\">".$opt_val." - ".substr($cars_options[$c]['desc'],0,25)."</option>";
								$last_aar = substr($cars_options[$c]['aar_type'],0,1);
							} ?>
							</select></span><br />
							<span style="font-size: 8pt;">(Only cars not already allocated to a waybill are shown in the Car Selector!)</span>
						</td></tr></table><br />
				</td>
				<td rowspan="1" style="vertical-align: top; background-color: yellow; padding: 3px;">
					<strong><u>Car Search</u></strong><br />&nbsp;
					<!-- Find cars at: <input type="text" size="20" name="mtcars" id="mtcars" onkeyup="carsAutoFind(this.value,'location','mtcars', '', {'preloader':'mtcars_load', 'target':'mtcars_span'});" /> // -->
					Find cars at: <input type="text" size="20" name="mtcars" id="mtcars" onkeyup="carsAutoFind(this.value,'location');" />
					<span id="mtcars_load" style="visibility: hidden;">Loading...</span>
					<br />
					<div id="mtcars_span" style="font-size: 9pt; max-height: 125px; overflow: auto;">&nbsp;</span>
				</td>
			</tr>
			<tr>
			<td colspan="3" style="background-color: peru;">&nbsp;&nbsp;Industries / Locations details</td>
			</tr>
			<tr>
				<td>Lading</td>
				<!-- <td style="border: 1px solid black; background-color:#bbb; padding: 3px;"><input type="text" id="fld11" name="fld11" value="<?php echo $fld11; ?>" onKeyUp="autoComp(this.value,'ichange_commod','commod_name,generates','fld11', {'target':'fld11_span'}); document.getElementById('fld11_span').style.display = 'block';" /> // -->
				<td style="border: 1px solid black; background-color:#bbb; padding: 3px;">
				<input type="text" id="fld11" name="fld11" value="<?php echo $fld11; ?>" onKeyUp="autoComp(this.value,'ichange_commod','commod_name,generates','fld11');" />
				<?php if($fld11 == "MT" || $fld11 == "MTY"){ ?>
				<br />was&nbsp;<a href="javascript:{}" onclick="document.getElementById('fld11').value = '<?php echo trim($fld11_prev); ?>'"><?php echo $fld11_prev; ?></a>
				<?php } ?>
				<div id="fld11_span" style="display: none; border: 1px solid black; background-color: yellow; font-size: 9pt; padding: 5px;"></div>
				</td>
				<td valign="top" rowspan="4">
				<!-- 
				Map Location<br /><input type="text" id="pfld6" name="pfld6" size="16" maxsize="16" value="" />
<br />
				<select id="pfld6b" name="fld6b" size="4" onChange="document.getElementById('pfld6').value = this.value" style="min-width: 200px;">
				<?php echo $map_lst; ?>
				</select>
				// -->
				</td>
			</tr>
			<tr>
				<td>Origin</td>
				<td><a name="ind1"></a>
					<!-- <input type"text" name="fld4" id="fld4" value="<?php echo $fld4; ?>" size="50" onKeyUp="industAutoComp(this.value,'ichange_indust','fld4','fld4',1, {'target':'fld4_span'}); document.getElementById('fld4_span').style.display = 'block';" onfocus="showEle('orig_ind_info');" onblur="hideEle('orig_ind_info');" /> // -->
					<input type"text" name="fld4" id="fld4" value="<?php echo $fld4; ?>" size="50" onKeyUp="industAutoComp(this.value,'ichange_indust','fld4','fld4',1)" onfocus="showEle('orig_ind_info');" onblur="hideEle('orig_ind_info');" />
			<div id="fld4_span"  style="display: none; border: 1px solid black; background-color: yellow; font-size: 9pt; padding: 5px; max-height: 100px; overflow: auto;"></div>
			<div id="fld4_indDescDiv" style="display: none;">
					<textarea cols="50" name="fld4_indDesc" id="fld4_indDesc" onchange="this.parent.style.display = 'block'"><?php echo $fld4_indDesc; ?></textarea>
					</span>
					<span style="font-size: 9pt; display: none;" id="orig_ind_info">Enter the commodity to ship, the industry name, city or state for a list of industries.<br /></span>
				</td>
			</tr>
			<tr>
				<td>Destination</td>
				<td><a name="ind2"></a>
					<!-- <input type"text" name="fld5" id="fld5" value="<?php echo $fld5; ?>" size="50" onKeyUp="industAutoComp(this.value,'ichange_indust','fld5','fld5',2, {'target':'fld5_span'}); document.getElementById('fld5_span').style.display = 'block';" onfocus="showEle('dest_ind_info');" onblur="hideEle('dest_ind_info');" /> // -->
					<input type"text" name="fld5" id="fld5" value="<?php echo $fld5; ?>" size="50" onKeyUp="industAutoComp(this.value,'ichange_indust','fld5','fld5',2);" onfocus="showEle('dest_ind_info');" onblur="hideEle('dest_ind_info');" />
			<div id="fld5_span"  style="display: none; border: 1px solid black; background-color: yellow; font-size: 9pt; padding: 5px; max-height: 100px; overflow: auto;"></div>
			<div id="fld5_indDescDiv" style="display: none;">
					<textarea cols="50" name="fld5_indDesc" id="fld5_indDesc" onchange="this.parent.style.display = 'block'"><?php echo $fld5_indDesc; ?></textarea>
					</div>
					<span style="font-size: 9pt; display: none;" id="dest_ind_info">Enter the commodity to ship, the industry name, city or state for a list of industries.<br /></span>
				</td>
			</tr>
			<tr>
				<td>Return to</td>
				<td>
					<input type="text" id="fld19" name="fld19" size="40" maxsize="40" value="<?php echo $fld19; ?>" />
				</td>
				<td>
				</td>
			</tr>
			<tr>
			<td colspan="3" style="background-color: peru;">&nbsp;&nbsp;Railroad Operation details</td>
			</tr>
			<tr>
				<td>From</td>
				<td><select id="fld2" name="fld2" />
					<?php for($r=0;$r<count($rr_kys);$r++){
						$sel = ""; if($rr_options[$rr_kys[$r]]->id == $fld2){$sel = "selected=\"selected\" ";}
						echo "<option ".$sel."value=\"".$rr_options[$rr_kys[$r]]->id."\">".$rr_options[$rr_kys[$r]]->rr_name."</option>\n";
					} 
					?>
				</td>
				<td rowspan="5" style="vertical-align: top;">Notes<br />
				<textarea name="fld17" id="fld17" cols="50" rows="4"><?php echo $fld17; ?></textarea><br />
				</td>

			</tr>
			<tr>
				<td>To</td>
				<td><select id="fld3" name="fld3" />
					<?php for($r=0;$r<count($rr_kys);$r++){
						$sel = ""; if($rr_options[$rr_kys[$r]]->id == $fld3){$sel = "selected=\"selected\" ";}
						echo "<option ".$sel."value=\"".$rr_options[$rr_kys[$r]]->id."\">".$rr_options[$rr_kys[$r]]->rr_name."</option>\n";
					} 
					?>
</td>
			</tr>
			<tr>
				<td>Assigned to</td>
				<td><select id="fld18" name="fld18" />
					<?php for($r=0;$r<count($rr_kys);$r++){
						$sel = ""; if($rr_options[$rr_kys[$r]]->id == $fld18){$sel = "selected=\"selected\" ";}
						echo "<option ".$sel."value=\"".$rr_options[$rr_kys[$r]]->id."\">".$rr_options[$rr_kys[$r]]->rr_name."</option>\n";
					} 
					?>
					</select>
				<?php if(count($route_rr_arr) > 0){
					echo "<div style=\"background-color: yellow; border: 1px solid red; margin: 2px; padding: 5px;\">The following railroads should have the waybill assigned to them when they need to action this waybill: ";
					for($rri=0;$rri<count($route_rr_arr);$rri++){echo "<span style=\"background-color: red; color: white;\">".$route_rr_arr[$rri]."</span>&nbsp;";}
					echo "</div>";
				}
				?>
				</td>
			</tr>
			<tr>
				<td>Routing</td>
				<!-- <td><input type="text" id="fld6" size="25" name="fld6" value="<?php echo $fld6; ?>" onKeyUp="autoComp(this.value,'ichange_waybill','routing','fld6', {'target':'fld6_span'}); document.getElementById('fld6_span').style.display = 'block';" /> // -->
				<td><input type="text" id="fld6" size="25" name="fld6" value="<?php echo $fld6; ?>" onKeyUp="autoComp(this.value,'ichange_waybill','routing','fld6');" />
                        <div id="fld6_span" style="display: none; border: 1px solid black; background-color: yellow; font-size: 9pt; padding: 5px;">
                        </div></td>
			</tr>
			<?php if(count($rr_ics) > 0){ ?>
			<tr>
				<td>Interchanges</td>
				<td colspan="3">
				<table style="background-color: transparent; border: none;">
					<tr>
				<?php
				$rr_ics_k = array_keys($rr_ics);
				$icl = "";
				for($ric=0;$ric<count($rr_ics_k);$ric++){
					//echo "<a href=\"#".$rr_ics_k[$ric]."\">".$rr_ics_k[$ric]."</a>&nbsp;";
					$icl .= "<td style=\"border: 1px solid maroon; padding: 3px; background-color: transparent; font-size: 9pt; vertical-align: top; background-color: #ddd;\"><div style=\"max-height: 70px; overflow: auto;\"><strong>".$rr_ics_k[$ric]."</strong><br />".$rr_ics[$rr_ics_k[$ric]]['ics']."</div></td>";
				} 
				echo $icl;
				?>
					</tr>
				</table>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td>Status</td>
				<td>
					<select id="fld7" name="fld7" onchange="updateOnStatChg(); hideEle('auto_ul_lab'); if(this.value == 'UNLOADING'){showEle('auto_ul_lab');}">

							<?php echo $fld7; ?>
					</select>
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
				<td colspan="3">
					<span style="font-size: 10pt; color: maroon">To change the train this waybill is allocated to enter the train symbol (Train ID), words in the Train Description, Origin, Destination, or an Auto Waypoint in the <strong>In / Allocated To</strong> field below then click the <strong>Find</strong> button to see a list of matching trains.</span>
				</td>
			</tr>
			<tr>
				<td valign="top"><span style="white-space: nowrap;">In / Allocated To</span></td>
				<td>
					<!-- <select id="fld14" name="fld14" onchange="selTrain(this.value,'','','', {'target':'train_disp_span'}); document.getElementById('train_disp_span').style.display = 'block';"> // -->
					<!-- 
					<select id="fld14" name="fld14" onchange="selTrain(this.value);">
		            <option selected="selected" value="<?php echo $fld14; ?>"><?php echo $fld14; ?></option>
		            <option value="">NOT ALLOCATED</option>
		            <?php echo $trains_lst; ?>
                </select>
                // -->
                <input type="text" size="35" name="fld14" id="fld14" value="<?php echo $fld14; ?>" onchange="this.value = this.value.toUpperCase();selTrain(this.value);"/>&nbsp;<input type="button" name="fndTrn" value="Find" onclick="trainAutoComp(document.getElementById('fld14').value,'fld14','train_autocomp');" />
                <div id="train_autocomp_span" style="display: none; border: 1px solid #777; background-color: yellow; font-size: 9pt; padding: 5px; margin: 1px; max-height: 150px; overflow: auto;">
                </div>
            	 <div id="train_disp_span" style="display: none; border: 1px solid #777; background-color: yellow; font-size: 9pt; padding: 5px; margin: 1px;">
              		<input type="hidden" id="entry_waypoint" name="entry_waypoint" value="" />
						<input type="hidden" id="exit_waypoint" name="entry_waypoint" value="" />
                </div>
            	 <div id="train_disp_span2" style="display: none; border: 1px solid #777; background-color: #ADFF2F; font-size: 9pt; padding: 5px; margin: 1px;">
                </div>
                <textarea name="route_json" id="route_json" style="display: none;"></textarea>
                <div style="font-size: 10pt; max-width: 600px; display: none" id="trains_info">
					<span style="float: right;">&nbsp;&nbsp;<a href="javascript:{}" onclick="document.getElementById('trains_info').style.display = 'none';">[X]</a></span>
					Automatic Trains are trains that will automatically deliver the car/s on a waybill to the Exit Location, 
					or the Destination for the train if no Exit Location is indicated. 
					To set a train as Automatic, enter a number greater than zero in the <strong>Days For Auto Train</strong> 
					field in the <strong>Trains Management</strong> application. 
					Automatic Trains are listed in color in the drop down list above!
					</div>
					<br />					
					<a href="javascript:{}" onclick="document.getElementById('trains_info').style.display = 'block';">Help</a> | 
					<!-- <a href="edit2.php?type=TRAINS&action=NEW" target="_blank">Manage Trains</a>. // -->
 
				</td>
			</tr>
			<tr>
			<!--
				<td valign="top">Entry Location</td>
				<td>
					<input type="input" readonly="readonly" id="entry_waypoint" name="entry_waypoint" value="" />&nbsp;
					<a href="javascript:{}" onclick="document.getElementById('entry_waypoint').value = '';">Clear</a>
				</td>
			</tr>
			<tr>
				<td valign="top">Exit Location</td>
				<td>
					<input type="input" id="exit_waypoint" name="exit_waypoint" value="" onchange="document.form1.pfld6.value = this.value;" />&nbsp;<a href="javascript:{}" onclick="document.getElementById('waypoint_info').style.display = 'block';">Help</a><br />
					&nbsp;<a href="javascript:{}" onclick="document.getElementById('waypoint_info').style.display = 'block';">Help</a><br />
					
					<div style="font-size: 10pt; max-width: 600px; display: none;" id="waypoint_info">
<span style="float: right;">&nbsp;&nbsp;<a href="javascript:{}" onclick="document.getElementById('waypoint_info').style.display = 'none';">[X]</a></span>						<em>						
						The <strong>Exit Location</strong> field above is only used when allocating 
						a waybill to an Automatic Train, otherwise it is ignored. 
						To exit an automatic train at a specific location en-route, enter the location in the 
						<strong>Exit Location</strong> field above. 
						If this waybill is allocated to an Automatic Train and no location is entered in the <strong>Exit Location</strong> 
						field above, then it will be assumed the car is going to the destination of the train.
						</em>
					</div>
				</td>
				// -->
			</tr>
		</table>
		<?php 
		$in_future=0;
		if($last_prog_date.$last_prog_time > date('YmdHi')){ 
			$in_future=1;
		?>
		<div id="prog_table_info" style="display: block; font-size: 13pt; font-weight: bold; color: red; padding: 10px; background-color: yellow; border: 1px solid red;">
		The Progress Report form date selectors options have be limited because there is a progress report on this waybill that is later than the current server Date/Time. 
		Once the Date/Time of the latest progress report on this waybill has past the full range of date selector options will once again become available.
		</div>
		<?php } ?>
		<div id="prog_table">
		<table align="center" style="background-color: transparent; border: 1px solid brown; width: 100%; padding: 1px;">
			<tr>
			<td colspan="5" style="background-color: peru;"><a name="progfrm"></a>&nbsp;&nbsp;Progress
			</td>
			</tr>
			<tr>
				<td style="background-color: #D2B48C; padding: 2px;">Date</td>
				<td style="background-color: #D2B48C; padding: 2px;">Time</td>
				<td style="background-color: #D2B48C; padding: 2px;">Express Save</td>
				<td style="background-color: #D2B48C; padding: 2px;">Progress Description</td>
				<td style="background-color: #D2B48C; padding: 2px;">Map Location</td>
			</tr>
			<tr>
				<td style="vertical-align:top; background-color: #DCDCDC; padding: 4px;">
				<input type="hidden" id="pfld2" name="pfld2" size="12" maxsize="12" value="<?php echo date('Y-m-d'); ?>" />
				<select name="pfld2_y" id="pfld2_y" onchange="set_human_date()">
					<?php 
					$p_yr = date('Y'); 
					$p_yr2 = date('Y')+1;
					if($last_prog_date_arr[0] > $p_yr && $in_future == 1){$p_yr = $last_prog_date_arr[0]; $p_yr2 = $last_prog_date_arr[0];}
					for($i=$p_yr;$i<=$p_yr2;$i++){
						$ii = $i;
						$sel = ""; if($i == date('Y')){$sel = " selected=\"selected\"";}
						echo "<option".$sel." value=\"".$ii."\">".$ii."</option>";
					} ?>
				</select> - 
				<select name="pfld2_m" id="pfld2_m" onchange="set_human_date()">
					<?php 
					$p_mt = 1;
					$p_mt2 = 12;
					if($last_prog_date_arr[1] > $p_mt && $in_future == 1){$p_mt = intval($last_prog_date_arr[1]); $p_mt2 = intval($last_prog_date_arr[1]);}
					for($i=$p_mt;$i<=$p_mt2;$i++){
						$ii = $i; if($ii<10){$ii = "0".$ii;}
						$sel = ""; if($ii == date('m')){$sel = " selected=\"selected\"";}
						echo "<option".$sel." value=\"".$ii."\">".$mths[$i]."</option>";
					} ?>
				</select> - 
				<select name="pfld2_d" id="pfld2_d" onchange="set_human_date()">
					<?php 
					$p_dy = 1;
					$p_dy2 = 31;
					if($last_prog_date_arr[2] > $p_dy && $in_future == 1){$p_dy = intval($last_prog_date_arr[2]); $p_dy2 = intval($last_prog_date_arr[2]);}
					for($i=$p_dy;$i<=$p_dy2;$i++){
						$ii = $i; if($ii<10){$ii = "0".$ii;}
						$sel = ""; if($ii == date('d')){$sel = " selected=\"selected\"";}
						echo "<option".$sel." value=\"".$ii."\">".$ii."</option>";
					} ?>
				</select><br />
				<!-- <select name="tzone" id="tzone"><?php echo $tz_opts; ?></select> // -->
				TZ:<input type="text" readonly="readonly" name="tzone" value="<?php echo @$myRR[0]->tzone; ?>" style="border: none; background-color: transparent" />
				</td>
				<td style="vertical-align: top; background-color: #DCDCDC; padding: 4px;">
					<select name="pfld7">
					<?php 
					$p_hr = 0;
					$p_hr2 = 23;
					$hr_sel = date('H');
					if($last_prog_time_arr[0] > $p_hr && $in_future == 1){$p_hr = intval($last_prog_time_arr[0]); $hr_sel = $p_hr;}
					for($i=$p_hr;$i<=$p_hr2;$i++){
						$ii = $i; if($ii<10){$ii = "0".$ii;}
						$sel = ""; if($ii == $hr_sel){$sel = " selected=\"selected\"";}
						echo "<option".$sel." value=\"".$ii."\">".$ii."</option>";
					} ?>
					</select>:
					<select name="pfld8">
					<?php 
					$p_mi = 0;
					$p_mi2 = 59;
					$mi_sel = date("i");
					if($last_prog_time_arr[1] > $p_mi && $in_future == 1){$p_mi = intval($last_prog_time_arr[1]); $mi_sel = $p_mi;}
					for($i=$p_mi;$i<=$p_mi2;$i++){
						$ii = $i; if($ii<10){$ii = "0".$ii;}
						$sel = ""; if($ii == $mi_sel){$sel = " selected=\"selected\"";}
						echo "<option".$sel." value=\"".$ii."\">".$ii."</option>";
					} ?>
					</select><br />
				</td>
				<td style="vertical-align: top; background-color: #DCDCDC; padding: 4px;">
					<input type="checkbox" name="express" id="express" value="Y" />
				</td>
				<td style="vertical-align: top; background-color: #DCDCDC; padding: 4px;"><textarea name="pfld3" id="pfld3" cols="40" rows="4"></textarea>
				</td>
				<td style="vertical-align: top; background-color: #DCDCDC; padding: 4px;">
				<input type="text" id="pfld6" name="pfld6" size="16" maxsize="16" value="" /><br />
				<select id="pfld6b" name="fld6b" size="4" onChange="document.getElementById('pfld6').value = this.value" style="min-width: 200px;">
				<?php echo $map_lst; ?>
				</select>
				</td>
			</tr>
			<tr><td colspan="6" style="background-color: #DCDCDC; padding: 4px;">
			<!-- For waybill: // --> <input type="hidden" name="pfld4" id="pfld4" value="<?php echo $fld8; ?>" />
				<input type="hidden" name="goTo" value="<?php echo @$_SERVER['HTTP_REFERER']; ?>" />
				<?php if(strlen(@$_SERVER['HTTP_REFERER']) > 0){ ?>An option to return to <?php echo $_SERVER['HTTP_REFERER']; ?> will be displayed when Save is clicked.<br /><?php } ?>
           <?php if($local == 1 || $rr_sess == 1){ ?><?php } ?>
  			</td></tr>
      </table>
      </div>
      <div style="margin: 3px;">
      <input id="submit" name="submit" value="Save Changes" type="submit" />
      </div>
      <?php echo $prog_lst; ?>
      <textarea style="display: none;" name="other_data_json"><?php echo $oth_dat_json; ?></textarea>

	</form>	
	<?php echo $last_prog_date; ?>
		<?php echo $last_prog_time; ?>