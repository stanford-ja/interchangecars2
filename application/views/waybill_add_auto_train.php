<?php $rr_kys = array_keys($rr_options); 
$local=0;
if(strpos($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'],"www/Applications/") > 0){
	$local=1;
}
?>
<div style="text-align: center;">
<div style="display: inline-block; width: 80%;">
<table border="0" width="100%" align="center" style="background-color: transparent; border: none;">
	<tr>
		<td class="td_title">Action Date</td>
		<td class="td_title">Waypoint</td>
		<td class="td_title">Description</td>
		<td class="td_title">Train ID</td>
	</tr>
<?php 
for($i=0;$i<count($auto_data);$i++){ $styl = "td1"; if(floatval($i/2) == intval($i/2)){ $styl = "td2"; } ?>
	<tr>
		<td class="<?php echo $styl; ?>"><?php echo $auto_data[$i]->act_date; ?></td>
		<td class="<?php echo $styl; ?>"><?php echo $auto_data[$i]->waypoint; ?></td>
		<td class="<?php echo $styl; ?>"><?php echo $auto_data[$i]->description; ?></td>
		<td class="<?php echo $styl; ?>"><?php echo $auto_data[$i]->train_id; ?></td>
	</td>
<?php } ?>
</table>
</div>
</div>

			<form id="form1" name="form1" method="post" action="../../waybill/addAutoTrain/<?php echo $id; ?>" autocomplete="off">
	<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
	<input type="hidden" name="tbl" id="tbl" value="ichange_waybill" />
	<input type="hidden" name="last_action" id="last_action" value="<?php echo $last_action; ?>" />
		<table border="0" width="100%" align="center" style="background-color: transparent; border: none;">						
			<tr>
			<td colspan="3" style="background-color: peru;">&nbsp;&nbsp;Train details</td>
			</tr>
			<tr>
				<td colspan="3">
					<span style="font-size: 10pt; color: maroon">To add an Auto Train to this waybill enter the train symbol (Train ID), words in the Train Description, Origin, Destination, or an Auto Waypoint in the <strong>In / Allocated To</strong> field below then click the <strong>Find</strong> button to see a list of matching trains.</span>
				</td>
			</tr>
			<tr>
				<td valign="top"><span style="white-space: nowrap;">In / Allocated To</span></td>
				<td>
					<span data-balloon="The symbol of train the waybill is currently in or allocated to. To search for a train, enter either the location to be served, part or all of the train symbol, origin, or destination, or an auto train waypoint, then click the Find button." data-balloon-pos="right" data-balloon-length="xlarge">
                <input type="text" size="35" name="fld14[]" id="fld14" value="" onchange="this.value = this.value.toUpperCase();selTrain(this.value);" />&nbsp;<input type="button" name="fndTrn" value="Find" onclick="trainAutoComp(document.getElementById('fld14').value,'fld14','train_autocomp');" />
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
		
      <div style="margin: 3px;">
      <input id="submit" name="submit" value="Save Changes" type="submit" />
      </div>

	</form>	