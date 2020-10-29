<?php
echo form_open_multipart(WEB_ROOT."/affiliates/savemv");
?>
<p>
Listed below are various data records that belong to railroads affiliated with the railroad you are currently logged in as. 
Any or all of these records can be moved to the railroad you are currently logged in as by ticking the checkbox next to the records you wish to move. 
When you have checked the checkboxes for the records you wish to move, click the Move button at the bottom of the page. <br /><br />
Once the Submit button has been clicked the records selected will disappear from this list - this confirms that the records has been moved successfully to the railroad you are currently logged in as.
</p>
<h2>Industries</h2>
<?php for($i=0;$i<count($ichange_indust);$i++){ if(count($ichange_indust[$i]) > 0){ ?>
<strong><?php echo $affils[$i]->report_mark; ?></strong><br />

<table class="table1 hover order-column">
	<thead>
		<tr>
			<td class="td_title">Industry Name</div>
			<td class="td_title">Town</div>
			<td class="td_title">Move</div>
		</tr>
	</thead>
	<tbody>
<?php for($j=0;$j<count($ichange_indust[$i]);$j++){ $tdclss = "td1"; if(floatval($j/2) == intval($j/2)){$tdclss = "td2";} ?> 
		<tr>
			<td><?php echo $ichange_indust[$i][$j]->indust_name; ?></td>
			<td><?php echo $ichange_indust[$i][$j]->town; ?></td>
			<td><input type="checkbox" name="ichange_indust[]" value="<?php echo $ichange_indust[$i][$j]->id; ?>" /></td>
		</tr>
<?php } ?>
	</tbody>
</table>
<br />
<?php } } ?>

<h2>Car Pool</h2>
<?php for($i=0;$i<count($ichange_cars);$i++){ if(count($ichange_cars[$i]) > 0){ ?>
<strong><?php echo $affils[$i]->report_mark; ?></strong><br />

<table class="table2 hover order-column">
	<thead>
		<tr>
			<td class="td_title">Car Number</td>
			<td class="td_title">Aar type</td>
			<td class="td_title">Move</td>
		</tr>
	</thead>
	<tbody>
<?php for($j=0;$j<count($ichange_cars[$i]);$j++){ $tdclss = "td1"; if(floatval($j/2) == intval($j/2)){$tdclss = "td2";} ?> 
		<tr>
			<td><?php echo $ichange_cars[$i][$j]->car_num ?></td>
			<td><?php echo $ichange_cars[$i][$j]->aar_type; ?></td>
			<td><input type="checkbox" name="ichange_cars[]" value="<?php echo $ichange_cars[$i][$j]->id; ?>" /></td>
		</tr>
<?php } ?>
	</tbody>
</table>
<br />
<?php } } ?>

<h2>Motive Power</h2>
<?php for($i=0;$i<count($ichange_locos);$i++){ if(count($ichange_locos[$i]) > 0){ ?>
<strong><?php echo $affils[$i]->report_mark; ?></strong><br />

<table class="table3 hover order-column">
	<thead>
		<tr>
			<td class="td_title">Number</td>
			<td class="td_title">Manufacturer</td>
			<td class="td_title">Model</td>
			<td class="td_title">Move</td>
		</tr>
	</thead>
	<tbody>
<?php for($j=0;$j<count($ichange_locos[$i]);$j++){ $tdclss = "td1"; if(floatval($j/2) == intval($j/2)){$tdclss = "td2";} ?> 
		<tr>
			<td><?php echo $ichange_locos[$i][$j]->loco_num; ?></td>
			<td><?php echo $ichange_locos[$i][$j]->manufacturer; ?></td>
			<td><?php echo $ichange_locos[$i][$j]->model; ?></td>
			<td><input type="checkbox" name="ichange_locos[]" value="<?php echo $ichange_locos[$i][$j]->id; ?>" /></td>
		</tr>
<?php } ?>
	</tbody>
</table>
<br />
<?php } } ?>

<h2>Trains</h2>
<?php for($i=0;$i<count($ichange_trains);$i++){ if(count($ichange_trains[$i]) > 0){ ?>
<strong><?php echo $affils[$i]->report_mark; ?></strong><br />

<table class="table4 hover order-column">
	<thead>
		<tr>
			<td class="td_title">Train ID</td>
			<td class="td_title">Description</td>
			<td class="td_title">Origin</td>
			<td class="td_title">Destination</td>
			<td class="td_title">Move</td>
		</tr>
	</thead>
	<tbody>
<?php for($j=0;$j<count($ichange_trains[$i]);$j++){ $tdclss = "td1"; if(floatval($j/2) == intval($j/2)){$tdclss = "td2";} ?> 
		<tr>
			<td><?php echo $ichange_trains[$i][$j]->train_id; ?></td>
			<td><?php echo $ichange_trains[$i][$j]->train_desc; ?></td>
			<td><?php echo $ichange_trains[$i][$j]->origin; ?></td>
			<td><?php echo $ichange_trains[$i][$j]->destination; ?></td>
			<td><input type="checkbox" name="ichange_trains[]" value="<?php echo $ichange_trains[$i][$j]->id; ?>" /></td>
		</tr>
<?php } ?>
	</tbody>
</table>
<br />
<?php } } ?>
<input type="submit" name="submit" value="Move" />
</form>
