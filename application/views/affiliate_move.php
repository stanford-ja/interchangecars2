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

<div style="display: table; padding: 10px">
	<div style="display: table-row;">
		<div style="display: table-cell; width: 600px;" class="td_title">Industry Name</div>
		<div style="display: table-cell; width: 200px;" class="td_title">Town</div>
		<div style="display: table-cell; width: 100px;" class="td_title">Move</div>
	</div>
<?php for($j=0;$j<count($ichange_indust[$i]);$j++){ $tdclss = "td1"; if(floatval($j/2) == intval($j/2)){$tdclss = "td2";} ?> 
	<div style="display: table-row;">
		<div style="display: table-cell;" class="<?php echo $tdclss; ?>"><?php echo $ichange_indust[$i][$j]->indust_name; ?></div>
		<div style="display: table-cell;" class="<?php echo $tdclss; ?>"><?php echo $ichange_indust[$i][$j]->town; ?></div>
		<div style="display: table-cell;" class="<?php echo $tdclss; ?>"><input type="checkbox" name="ichange_indust[]" value="<?php echo $ichange_indust[$i][$j]->id; ?>" /></div>
	</div>
<?php } ?>
</div>
<?php } } ?>

<h2>Car Pool</h2>
<?php for($i=0;$i<count($ichange_cars);$i++){ if(count($ichange_cars[$i]) > 0){ ?>
<strong><?php echo $affils[$i]->report_mark; ?></strong><br />

<div style="display: table; padding: 10px">
	<div style="display: table-row;">
		<div style="display: table-cell; width: 300px;" class="td_title">Car Number</div>
		<div style="display: table-cell; width: 200px;" class="td_title">Aar type</div>
		<div style="display: table-cell; width: 100px;" class="td_title">Move</div>
	</div>
<?php for($j=0;$j<count($ichange_cars[$i]);$j++){ $tdclss = "td1"; if(floatval($j/2) == intval($j/2)){$tdclss = "td2";} ?> 
	<div style="display: table-row;">
		<div style="display: table-cell;" class="<?php echo $tdclss; ?>"><?php echo $ichange_cars[$i][$j]->car_num ?></div>
		<div style="display: table-cell;" class="<?php echo $tdclss; ?>"><?php echo $ichange_cars[$i][$j]->aar_type; ?></div>
		<div style="display: table-cell;" class="<?php echo $tdclss; ?>"><input type="checkbox" name="ichange_cars[]" value="<?php echo $ichange_cars[$i][$j]->id; ?>" /></div>
	</div>
<?php } ?>
</div>
<?php } } ?>

<h2>Motive Powers</h2>
<?php for($i=0;$i<count($ichange_locos);$i++){ if(count($ichange_locos[$i]) > 0){ ?>
<strong><?php echo $affils[$i]->report_mark; ?></strong><br />

<div style="display: table; padding: 10px">
	<div style="display: table-row;">
		<div style="display: table-cell; width: 300px;" class="td_title">Number</div>
		<div style="display: table-cell; width: 200px;" class="td_title">Manufacturer</div>
		<div style="display: table-cell; width: 100px;" class="td_title">Model</div>
		<div style="display: table-cell; width: 100px;" class="td_title">Move</div>
	</div>
<?php for($j=0;$j<count($ichange_locos[$i]);$j++){ $tdclss = "td1"; if(floatval($j/2) == intval($j/2)){$tdclss = "td2";} ?> 
	<div style="display: table-row;">
		<div style="display: table-cell;" class="<?php echo $tdclss; ?>"><?php echo $ichange_locos[$i][$j]->loco_num; ?></div>
		<div style="display: table-cell;" class="<?php echo $tdclss; ?>"><?php echo $ichange_locos[$i][$j]->manufacturer; ?></div>
		<div style="display: table-cell;" class="<?php echo $tdclss; ?>"><?php echo $ichange_locos[$i][$j]->model; ?></div>
		<div style="display: table-cell;" class="<?php echo $tdclss; ?>"><input type="checkbox" name="ichange_locos[]" value="<?php echo $ichange_locos[$i][$j]->id; ?>" /></div>
	</div>
<?php } ?>
</div>
<?php } } ?>

<h2>Trains</h2>
<?php for($i=0;$i<count($ichange_trains);$i++){ if(count($ichange_trains[$i]) > 0){ ?>
<strong><?php echo $affils[$i]->report_mark; ?></strong><br />

<div style="display: table; padding: 10px">
	<div style="display: table-row;">
		<div style="display: table-cell; width: 200px;" class="td_title">Train ID</div>
		<div style="display: table-cell; width: 350px;" class="td_title">Description</div>
		<div style="display: table-cell; width: 250px;" class="td_title">Origin</div>
		<div style="display: table-cell; width: 250px;" class="td_title">Destination</div>
		<div style="display: table-cell; width: 100px;" class="td_title">Move</div>
	</div>
<?php for($j=0;$j<count($ichange_trains[$i]);$j++){ $tdclss = "td1"; if(floatval($j/2) == intval($j/2)){$tdclss = "td2";} ?> 
	<div style="display: table-row;">
		<div style="display: table-cell;" class="<?php echo $tdclss; ?>"><?php echo $ichange_trains[$i][$j]->train_id; ?></div>
		<div style="display: table-cell;" class="<?php echo $tdclss; ?>"><?php echo $ichange_trains[$i][$j]->train_desc; ?></div>
		<div style="display: table-cell;" class="<?php echo $tdclss; ?>"><?php echo $ichange_trains[$i][$j]->origin; ?></div>
		<div style="display: table-cell;" class="<?php echo $tdclss; ?>"><?php echo $ichange_trains[$i][$j]->destination; ?></div>
		<div style="display: table-cell;" class="<?php echo $tdclss; ?>"><input type="checkbox" name="ichange_trains[]" value="<?php echo $ichange_trains[$i][$j]->id; ?>" /></div>
	</div>
<?php } ?>
</div>
<?php } } ?>
<input type="submit" name="submit" value="Move" />
</form>