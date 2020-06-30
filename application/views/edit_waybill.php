<?php $rr_kys = array_keys($rr_options); 
$local=0;
if(strpos($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'],"www/Applications/") > 0){
	$local=1;
}
$mths = array("","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
if(@$myRR[0]->use_tz_time == 1 && strlen(@$myRR[0]->tzone) > 0){	date_default_timezone_set($myRR[0]->tzone);}

$op_days = array();
if(isset($traindata[0]->sun) && $traindata[0]->sun == 1){$op_days[] = "Sun";}
if(isset($traindata[0]->mon) && $traindata[0]->mon == 1){$op_days[] = "Mon";}
if(isset($traindata[0]->tues) && $traindata[0]->tues == 1){$op_days[] = "Tue";}
if(isset($traindata[0]->wed) && $traindata[0]->wed == 1){$op_days[] = "Wed";}
if(isset($traindata[0]->thu) && $traindata[0]->thu == 1){$op_days[] = "Thu";}
if(isset($traindata[0]->fri) && $traindata[0]->fri == 1){$op_days[] = "Fri";}
if(isset($traindata[0]->sat) && $traindata[0]->sat == 1){$op_days[] = "Sat";}

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
					<a href="<?php echo WEB_ROOT; ?>/messaging/lst/<?php echo $id; ?>">Messages</a>&nbsp;
					<?php if($id > 0){ ?><a href="javascript:{}" onclick="window.open('<?php echo WEB_ROOT; ?>/graphics/waybill/<?php echo $id; ?>','WB<?php echo $id; ?>','width=500, height=700');">Upload Image</a>&nbsp;<?php } ?>
					<a href="<?php echo WEB_ROOT; ?>/waybill/tranship/<?php echo $id; ?>" style=\"color: yellow;\">Tranship</a>&nbsp;
					<!-- <a href="<?php echo WEB_ROOT; ?>/edit.php?type=WAYBILL&id=20120517115321&action=EDIT" style=\"color: yellow;\">Orig. Waybill</a>&nbsp; // -->
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
				<td style="padding: 3px;" colspan="2">
					
					<div style="display: block; float: right; width: 200px; height: 150px; vertical-align: top; background-color: yellow; padding: 3px;">
					<strong><u>Car Search</u></strong><br />&nbsp;
					<span data-balloon="Start to enter a car location or number and the results will appear as you type." data-balloon-pos="left" data-balloon-length="large">
					Find cars at: 
					<input type="text" size="20" name="mtcars" id="mtcars" onkeyup="carsAutoFind(this.value,'location');" />
					</span>
					<span id="mtcars_load" style="visibility: hidden;">Loading...</span>
					<br />
					<span id="mtcars_span" style="font-size: 9pt; max-height: 125px; overflow: auto;">&nbsp;</span>
					</div>

					<span style="display: none;"><textarea name="fld21" id="fld21" cols="50" rows="3"><?php echo $fld21; ?></textarea>
					</span><input type="hidden" name="fld10" id="fld10" value="<?php echo $fld10; ?>" />
					<table style="margin-bottom: 5px; background-color: #F4A460;">
					<tr>
						<td colspan="7">
							<div id="alreadyOnWB" style="width: 90%; background-color: yellow; border: 1px solid maroon; border-radius: 4px; padding: 5px; font-size: 12pt; display: none; text-align: center;"></div>
						</td>
					</tr>
					<tr>
					<td colspan="2">
					<strong>Cars attached to waybill</strong>
					</td>
					<td rowspan="7" style="vertical-align: top;">
					<span style="font-size: 9pt; font-weight: bold;">Cars on waybill</span><br />
					<div id="carsHTM" style="font-size: 9pt; padding: 2px; border: 1px solid #777;background-color: #DEB887;min-width: 180px; max-height: 150px; overflow: auto">&nbsp;</div>
					</td>
					<td rowspan="5" style="font-weight: bold; color: maroon; font-size: 8pt; vertical-align: top;"><?php echo $sugg_car_types; ?></td>
					</tr>
					<tr><td>Car </td>
					<td>
						<div id="autocomp"><div id="field">
						<span data-balloon="Enter the full Car Number including reporting mark." data-balloon-pos="right" data-balloon-length="xlarge">
						<input name="fld21_car" id="fld21_car" value="" style="width: 200px;" onchange="this.value=this.value.toUpperCase(); carUsed(this.value);" />
						</span>
						</div></div>
					</td>
					</tr>
					<tr><td>AAR </td>
					<td>
						<span data-balloon="Select an AAR Code for the car you wish to add." data-balloon-pos="right" data-balloon-length="large">
						<select name="fld21_aar" id="fld21_aar" style="width: 150px; font-size: 9pt;">
							<option value=""></option>
							<?php echo $aar_options; ?>
						</select>
						</span>
					</td></tr>
					<tr><td>Attach to RR</td>
						<td>
							<span data-balloon="Select the railroad the entered car will be added for." data-balloon-pos="right" data-balloon-length="large">
							<select name="fld21_rr" id="fld21_rr">
							<?php for($i=0;$i<count($affil);$i++){echo "<option value=\"".$affil[$i]."\">".$allRR[$affil[$i]]->report_mark."</option>";} ?>
							</select>
							</span>
							&nbsp;<input type="button" value="Add" onclick="addCar();" />
							</td></tr>
							<tr><td colspan="2"><span id="fld9drop">
							<?php echo "<pre>"; /* print_r($cars_options);*/ echo "</pre>"; ?>
							Car Select<br />
							<span data-balloon="Select a car from the list, then click the Add Car button to add it to the car list for this waybill." data-balloon-pos="right" data-balloon-length="large"> 
							<select id="fld9sel" name="fld9sel" style="width: 320px;" onChange="var expSt = explodeStr('\,',document.getElementById('fld9sel').value); option0 = new Option(expSt[1],expSt[1]); document.form1.fld21_car.value = expSt[0]; document.form1.fld21_aar.options[0] = option0; document.form1.fld21_aar.options[0].selected = true;">
							<option value="">--Select Car Number or enter in Field above--</option>
							<?php $last_aar = ""; for($c=0;$c<count($cars_options);$c++){
								$this_aar = substr($cars_options[$c]['aar_type'],0,1);
								if($last_aar != $this_aar){echo "<option style=\"background-color: brown; color: white; font-weight: bold;\">AAR Type: ".$this_aar."</option>";}
								$opt_val = $cars_options[$c]['car_num'].",".$cars_options[$c]['aar_type'].",".$cars_options[$c]['rr'];
								echo "<option value=\"".$opt_val."\">".$opt_val." - ".substr($cars_options[$c]['desc'],0,25)."</option>";
								$last_aar = substr($cars_options[$c]['aar_type'],0,1);
							} ?>
							</select></span>
							<br />
							<span style="font-size: 8pt;">(Only cars not already allocated to a waybill are shown in the Car Selector!)</span>
						</td></tr></table><br />
				</td>
<!--
				<td rowspan="1" style="vertical-align: top; background-color: yellow; padding: 3px;">
					<strong><u>Car Search</u></strong><br />&nbsp;
					<span data-balloon="Start to enter a car location or number and the results will appear as you type." data-balloon-pos="left" data-balloon-length="large">
					Find cars at: 
					<input type="text" size="20" name="mtcars" id="mtcars" onkeyup="carsAutoFind(this.value,'location');" />
					</span>
					<span id="mtcars_load" style="visibility: hidden;">Loading...</span>
					<br />
					<div id="mtcars_span" style="font-size: 9pt; max-height: 125px; overflow: auto;">&nbsp;</span>
				</td>
// -->
			</tr>
			<tr>
			<td colspan="2" style="background-color: peru;">&nbsp;&nbsp;Industries / Locations details</td>
			<td style="background-color: peru">&nbsp;&nbsp;Photos</td>
			</tr>
			<tr>
				<td>Lading</td>
				<!-- <td style="border: 1px solid black; background-color:#bbb; padding: 3px;"><input type="text" id="fld11" name="fld11" value="<?php echo $fld11; ?>" onKeyUp="autoComp(this.value,'ichange_commod','commod_name,generates','fld11', {'target':'fld11_span'}); document.getElementById('fld11_span').style.display = 'block';" /> // -->
				<td style="border: 1px solid black; background-color:#bbb; padding: 3px;">
				<!-- <input type="text" id="fld11" name="fld11" value="<?php echo $fld11; ?>" onKeyUp="autoComp(this.value,'ichange_commod','commod_name,generates','fld11');" /> // -->
				<div id="autocomp">
				<div id="field">
				<span data-balloon="The cargo being transported on this waybill. Start typing and commodity matches will appear." data-balloon-pos="right" data-balloon-length="large">
				<input type="text" id="fld11" name="fld11" value="<?php echo $fld11; ?>" onKeyUp="autoComp(this.value,'ichange_commod','commod_name','fld11');" />
				</span>
				</div>
				</div>
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

				<div id="wb_image_div">
				<?php 
				if($id > 0){
				$fils = get_filenames(DOC_ROOT."/waybill_images/");
				for($i=0;$i<count($fils);$i++){
					if(strpos("Z".$fils[$i],$id."-") > 0){ // ".WEB_ROOT."/waybill_images/".$fils[$i]."
						$fil_html .= "<a href=\"javascript:{}\" onclick=\"window.open('".WEB_ROOT."/graphics/wbview/".str_replace(".jpg","",$fils[$i])."','".$i."','width=500,height=650');\">";
						$fil_html .= "<img src=\"".WEB_ROOT."/waybill_images/".$fils[$i]."\" style=\"height: 80px; margin: 3px;\">";
						$fil_html .= "</a>";
					}
				}
				}
				if(isset($fil_html) && strlen($fil_html) > 0){
					$fil_html = "<div id=\"wb_image_div\" style=\"color: #555; padding: 10px; margin: 3px; background-color: antiquewhite;\">
						".$fil_html."
						</div>";
					echo $fil_html;
				}
				?>
				</div>

				</td>
			</tr>
			<tr>
				<td>Origin</td>
				<td><a name="ind1"></a>
					<!-- <input type"text" name="fld4" id="fld4" value="<?php echo $fld4; ?>" size="50" onKeyUp="industAutoComp(this.value,'ichange_indust','fld4','fld4',1, {'target':'fld4_span'}); document.getElementById('fld4_span').style.display = 'block';" onfocus="showEle('orig_ind_info');" onblur="hideEle('orig_ind_info');" /> // -->
					<span data-balloon="The originating industry for this waybill." data-balloon-pos="right" data-balloon-length="large">
					<input type"text" name="fld4" id="fld4" value="<?php echo $fld4; ?>" size="50" onKeyUp="industAutoComp(this.value,'ichange_indust','fld4','fld4',1)" onfocus="showEle('orig_ind_info');" onblur="hideEle('orig_ind_info');" />
					</span>
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
					<span data-balloon="The destination industry for this waybill." data-balloon-pos="right" data-balloon-length="large">
					<input type"text" name="fld5" id="fld5" value="<?php echo $fld5; ?>" size="50" onKeyUp="industAutoComp(this.value,'ichange_indust','fld5','fld5',2);" onfocus="showEle('dest_ind_info');" onblur="hideEle('dest_ind_info');" />
					</span>
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
					<span data-balloon="Where to return the car/s on this waybill to when they have been unloaded." data-balloon-pos="right" data-balloon-length="large">
					<input type="text" id="fld19" name="fld19" size="40" maxsize="40" value="<?php echo $fld19; ?>" />
					</span>
				</td>
				<td>
				</td>
			</tr>
			<?php if(!in_array($fld11,array("","MT","EMPTY","MTY")) && $fld21_cntr > 1){ ?>
			<tr>
				<td>Store at</td>
				<td>
					<select name="storage" onchange="if(this.value.length > 0){if(confirm('This will store the lading\nfor the number of cars indicated\nand mark this waybill as unloaded.\n\nAre you sure?')){ window.location = '<?php echo WEB_ROOT."/waybill/store/".$id."/"; ?>'+this.value;} }">
						<option value="" selected="selected">To Bulk Store this WB, select...</option>
						<?php for($st=0;$st<count($stodat);$st++){
							echo "<option value=\"".$stodat[$st]->id."\">".substr($stodat[$st]->indust_name,0,35)."... (".$stodat[$st]->town.")</option>";
						} ?>
					</select>
				</td>
			</tr>
			<?php } ?>
			<tr>
			<td colspan="3" style="background-color: peru;">&nbsp;&nbsp;Railroad Operation details</td>
			</tr>
			<tr>
				<td>From</td>
				<td>
					<span data-balloon="The railroad that serves the Originating Industry" data-balloon-pos="right" data-balloon-length="large">
					<select id="fld2" name="fld2">
					<?php for($r=0;$r<count($rr_kys);$r++){
						$sel = ""; if($rr_options[$rr_kys[$r]]->id == $fld2){$sel = "selected=\"selected\" ";}
						if($rr_options[$rr_kys[$r]]->common_flag != @$rr_options[$rr_kys[$r-1]]->common_flag && $r > 0){
							echo "<option value=\"\" style=\"background-color: brown; color: white;\">-- COMMON --</option>";
						}else	if($rr_options[$rr_kys[$r]]->inactive != @$rr_options[$rr_kys[$r-1]]->inactive && $r > 0){
							echo "<option value=\"\" style=\"background-color: brown; color: white;\">-- INACTIVE --</option>";
						}
						echo "<option ".$sel."value=\"".$rr_options[$rr_kys[$r]]->id."\">".$rr_options[$rr_kys[$r]]->rr_name." (".$rr_options[$rr_kys[$r]]->report_mark.")</option>\n";
					} 
					?>
					</select>
					</span>
				</td>
				<td rowspan="5" style="vertical-align: top;">Notes<br />
				<span data-balloon="Any notes pertinent to the movement of cars on this waybill. Can include information that does not appear anywhere else on the waybill, but will help handling railroads expedite this waybill." data-balloon-pos="left" data-balloon-length="large">
				<textarea name="fld17" id="fld17" cols="50" rows="4"><?php echo $fld17; ?></textarea>
				</span><br />
				</td>

			</tr>
			<tr>
				<td>To</td>
				<td>
					<span data-balloon="The railroad that serves the Destination Industry." data-balloon-pos="right" data-balloon-length="large">
					<select id="fld3" name="fld3">
					<?php for($r=0;$r<count($rr_kys);$r++){
						$sel = ""; if($rr_options[$rr_kys[$r]]->id == $fld3){$sel = "selected=\"selected\" ";}
						if($rr_options[$rr_kys[$r]]->common_flag != @$rr_options[$rr_kys[$r-1]]->common_flag && $r > 0){
							echo "<option value=\"\" style=\"background-color: brown; color: white;\">-- COMMON --</option>";
						}else	if($rr_options[$rr_kys[$r]]->inactive != @$rr_options[$rr_kys[$r-1]]->inactive && $r > 0){
							echo "<option value=\"\" style=\"background-color: brown; color: white;\">-- INACTIVE --</option>";
						}
						echo "<option ".$sel."value=\"".$rr_options[$rr_kys[$r]]->id."\">".$rr_options[$rr_kys[$r]]->rr_name." (".$rr_options[$rr_kys[$r]]->report_mark.")</option>\n";
					} 
					?>
					</select>
					</span>
</td>
			</tr>
			<tr>
				<td>Assigned to</td>
				<td>
					<span data-balloon="The railroad this waybill is currently assigned to. Normally this would be either the From or To railroad, or a railroad indicated in the Routing field, or an intermediate railroad that helps connect them." data-balloon-pos="right" data-balloon-length="large">
					<select id="fld18" name="fld18">
					<?php for($r=0;$r<count($rr_kys);$r++){
						$sel = ""; if($rr_options[$rr_kys[$r]]->id == $fld18){$sel = "selected=\"selected\" ";}
						if($rr_options[$rr_kys[$r]]->common_flag != @$rr_options[$rr_kys[$r-1]]->common_flag && $r > 0){
							echo "<option value=\"\" style=\"background-color: brown; color: white;\">-- COMMON --</option>";
						}else	if($rr_options[$rr_kys[$r]]->inactive != @$rr_options[$rr_kys[$r-1]]->inactive && $r > 0){
							echo "<option value=\"\" style=\"background-color: brown; color: white;\">-- INACTIVE --</option>";
						}
						echo "<option ".$sel."value=\"".$rr_options[$rr_kys[$r]]->id."\">".$rr_options[$rr_kys[$r]]->rr_name." (".$rr_options[$rr_kys[$r]]->report_mark.")</option>\n";
					} 
					?>
					</select>
					</span>
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
				<td>
					<!--
					<input type="text" id="fld6" size="25" name="fld6" value="<?php echo $fld6; ?>" onKeyUp="autoComp(this.value,'ichange_waybill','routing','fld6');" />
					<div id="fld6_span" style="display: none; border: 1px solid black; background-color: yellow; font-size: 9pt; padding: 5px;"></div>
					// -->
					<div id="autocomp">
					<div id="field">
					<span data-balloon="The route the car/s on the waybill will take to get from Origin to Destination (and back). This should include the reporting marks of ALL railroads that will handle this waybill, NOT just the Origin and Destination railroads." data-balloon-pos="right" data-balloon-length="xlarge">
					<input type="text" id="fld6" size="25" name="fld6" value="<?php echo $fld6; ?>" />
					</span>
					</div>
					</div>
				</td>
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
			<!-- 
			<tr>
				<td>Status</td>
				<td>
					<select id="fld7" name="fld7[]" onchange="updateOnStatChg(); hideEle('auto_ul_lab'); if(this.value == 'UNLOADING'){showEle('auto_ul_lab');}">
					<select id="fld7" name="fld7[]" onchange="updateOnStatChg(this,document.getElementById('pfld3_0'),document.getElementById('pfld6_0'),document.getElementById('auto_ul_lab')); hideEle('auto_ul_lab'); if(this.value == 'UNLOADING'){showEle('auto_ul_lab');}">
							<?php echo $fld7; ?>
					</select>
					<div id="auto_ul_lab" style="display: none;">Auto Unload In&nbsp;
						<select name="unload_days[]" id="unload_days">
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
			// -->
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
					<span data-balloon="The symbol of train the waybill is currently in or allocated to. To search for a train, enter either the location to be served, part or all of the train symbol, origin, or destination, or an auto train waypoint, then click the Find button." data-balloon-pos="right" data-balloon-length="xlarge">
                <input type="text" size="35" name="fld14[]" id="fld14" value="<?php echo $fld14; ?>" onchange="this.value = this.value.toUpperCase();selTrain(this.value);rebuildDateSel('pfld2_0','fld14');" />&nbsp;<input type="button" name="fndTrn" value="Find" onclick="trainAutoComp(document.getElementById('fld14').value,'fld14','train_autocomp');" />
                </span>
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
		</table>
		<?php 
		$in_future=0;
		if($last_prog_date.$last_prog_time > date('YmdHi')){ 
			$in_future=1;
		?>
		<div id="prog_table_info" style="display: block; font-size: 13pt; font-weight: bold; color: red; padding: 10px; background-color: yellow; border: 1px solid red;">
		The Progress Report form date selectors options have be limited, and the multi-progress reporting option disabled, because there is a progress report on this waybill that is later than the current server Date/Time. 
		Once the Date/Time of the latest progress report on this waybill has past the full range of date selector options and multi-progress reporting will once again become available. 
		</div>
		<?php } ?>
		<div id="prog_table" style="width: 100%;"> <!-- START OF DIV CONTAINER FOR PROGRESS REPORTS // --> 
		<a name="progfrm"></a><div style="background-color: peru; padding: 1px;">&nbsp;&nbsp;Progress</div>

		<div style="display: table; background-color: transparent; border: 1px solid brown; width: 100%; padding: 1px;"> <!-- START OF TABLE // -->
			<div style="display: table-row;">
				<!-- 
				<div style="display: table-cell; background-color: #D2B48C; padding: 4px; border: 1px solid peru; width: 20%;">Date</div>
				<div style="display: table-cell; background-color: #D2B48C; padding: 4px; border: 1px solid peru; width: 12%;">Time</div>
				// -->
				<div style="display: table-cell; background-color: #D2B48C; padding: 4px; border: 1px solid peru; width: 20%;">Date / Time<br />In / Allocated To</div>
				<div style="display: table-cell; background-color: #D2B48C; padding: 4px; border: 1px solid peru; width: 10%;">Express Save</div>
				<div style="display: table-cell; background-color: #D2B48C; padding: 4px; border: 1px solid peru; width: 30%;">Progress Description<br />Status</div>
				<div style="display: table-cell; background-color: #D2B48C; padding: 4px; border: 1px solid peru; width: 28%;">Map Location</div>
			</div>
		</div>
		<?php /* MAKE SURE THAT IF ANY CHANGES ARE MADE TO THE FIELDS IN THE PROGRESS REPORT BELOW THAT THE SAME CHANGES ARE MADE TO THE multi-prog.php -> multiProg FUNCTION! */ ?>
		<!-- START PROGRESS REPORT FORM ELEMENTS TABLE 1 // -->
		<div style="display: table; background-color: transparent; border: 1px solid brown; width: 100%; padding: 1px;">
			<div style="display: table-row;">
				<div style="display: table-cell; vertical-align:top; background-color: #DCDCDC; padding: 4px; border: 1px solid peru; width: 20%;">
				<!-- <input type="hidden" id="pfld2_0" name="pfld2[]" size="12" maxsize="12" value="<?php echo date('Y-m-d'); ?>" /> // -->
				<select id="pfld2_0" name="pfld2[]">
					<?php for($joe=date('U',$last_prog_date_ux);$joe<intval(date('U')+(86400*15));$joe=$joe+86400){
						if(in_array(date('D',$joe),$op_days) || count($op_days) == 0){
							$sel = ""; 
							if(date('U') <= $joe && !isset($trselected)){
								$sel = " selected=\"selected\"";
								$trselected = 1;
							}
							echo "<option value=\"".date('Y-m-d',$joe)."\"".$sel.">".date('Y-m-d (D)',$joe)."</option>";
						}
					} ?>
				</select>
				<!--
				<select name="pfld2_y[]" id="pfld2_0_y" onchange="set_human_date('pfld2_0')">
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
				<select name="pfld2_m[]" id="pfld2_0_m" onchange="set_human_date('pfld2_0')">
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
				<select name="pfld2_d[]" id="pfld2_0_d" onchange="set_human_date('pfld2_0')">
					<?php 
					$p_dy = 1;
					$p_dy2 = 31;
					if($last_prog_date_arr[2] > $p_dy && $in_future == 1){$p_dy = intval($last_prog_date_arr[2]); $p_dy2 = intval($last_prog_date_arr[2]);}
					for($i=$p_dy;$i<=$p_dy2;$i++){
						$ii = $i; if($ii<10){$ii = "0".$ii;}
						$sel = ""; if($ii == date('d')){$sel = " selected=\"selected\"";}
						echo "<option".$sel." value=\"".$ii."\">".$ii."</option>";
					} ?>
				</select> // -->
				<br />
				<select name="tzone_NOT" id="tzone_NOT" style="display: none;"><?php echo $tz_opts; ?></select>
				<!-- </div>
				<div style="display: table-cell; vertical-align: top; background-color: #DCDCDC; padding: 4px; border: 1px solid peru; width: 12%;">
				// -->
					<select id="pfld7_0" name="pfld7[]">
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
					<select id="pfld8_0" name="pfld8[]">
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
				TZ:<input type="text" readonly="readonly" name="tzone[]" id="tzone_0" value="<?php echo @$myRR[0]->tzone; ?>" style="border: none; background-color: transparent; width: auto;" />
				</div>
				<div style="display: table-cell; vertical-align: top; background-color: #DCDCDC; padding: 4px; border: 1px solid peru; width: 10%;">
					<input type="checkbox" name="express" id="express" value="Y" />
				</div>
				<div style="display: table-cell; vertical-align: top; background-color: #DCDCDC; padding: 4px; border: 1px solid peru; width: 30%;"><textarea name="pfld3[]" id="pfld3_0" style="width:95%; height: 90px;"></textarea>
				</div>
				<div style="display: table-cell; vertical-align: top; background-color: #DCDCDC; padding: 4px; border: 1px solid peru; width: 28%;">
				<div id="autocomp"><div id="field">
				<input type="text" id="pfld6" name="pfld6[0]" size="16" maxsize="16" value="" />
				</div></div>
				<!--
				<br />
				<select id="pfld6b" name="fld6b[]" size="4" onChange="document.getElementById('pfld6').value = this.value" style="width: 100%;">
				<?php echo $map_lst; ?>
				</select>
				// -->
				</div>
			</div>
			<div style="display: table-row">
				<div style="display: table-cell; vertical-align: top; background-color: #DCDCDC; padding: 4px; border: 1px solid peru; width: 20%;">&nbsp;</div>
				<div style="display: table-cell; vertical-align: top; background-color: #DCDCDC; padding: 4px; border: 1px solid peru; width: 10%;">&nbsp;</div>
				<div style="display: table-cell; vertical-align: top; background-color: #DCDCDC; padding: 4px; border: 1px solid peru; width: 30%;">
					<select id="fld7" name="fld7[]" onchange="updateOnStatChg(this,document.getElementById('pfld3_0'),document.getElementById('pfld6_0'),document.getElementById('auto_ul_lab')); hideEle('auto_ul_lab'); if(this.value == 'UNLOADING'){showEle('auto_ul_lab');}" style="max-width: 320px;">
							<?php echo $fld7; ?>
					</select>
					<div id="auto_ul_lab" style="display: none;">Auto Unload In&nbsp;
						<select name="unload_days[]" id="unload_days">
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
				</div>
				<div style="display: table-cell; vertical-align: top; background-color: #DCDCDC; padding: 4px; border: 1px solid peru; width: 28%;">
				</div>
			</div>
			
			<div style="display: none;">
				<input type="hidden" name="pfld4[]" id="pfld4" value="<?php echo $fld8; ?>" />
				<input type="hidden" name="prog_cntr" id="prog_cntr" value="" />
				<input type="hidden" name="goTo" value="<?php echo @$_SERVER['HTTP_REFERER']; ?>" />
  			</div>
      </div>
      <!-- END PROGRESS REPORT FORM ELEMENTS TABLE 1 // -->
		</div> <!-- END OF DIV CONTAINER FOR PROGRESS REPORTS // --> 
		
		<?php if($in_future == 0 && $fld14 != "AUTO TRAIN"){ ?><a href="javascript:{}" onclick="addProgFrm();">Add Extra Progress Report Form</a><?php } ?>
      <div style="margin: 3px;">
      <input id="submit" name="submit" value="Save Changes" type="submit" />
      </div>
      <?php echo $prog_lst; ?>
      <textarea style="display: none;" name="other_data_json"><?php echo $oth_dat_json; ?></textarea>

	</form>	
