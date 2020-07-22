<?php
// Graphic upload view, all types
?>
<?php echo $form; ?>
<?php if(strlen($referrer) > 0){ echo "<input type=\"hidden\" name=\"referrer\" value=\"".$referrer."\" />"; } ?>
<?php if(isset($desc_form)){ /* START DESCRIPTION FORM */ ?>
	<input type="hidden" name="img_name" value="<?php echo $img_name; ?>" />
	<?php if(file_exists(DOC_ROOT."/waybill_images/".$img_name)){ ?>
	<img src="<?php echo WEB_ROOT."/waybill_images/".$img_name; ?>" style="width: 300px;" /><br /><br />
	<div style="display: inline-block; padding: 0px;">
		Enter Image Description Below:<br />
		<input type="text" name="description" value="<?php echo @$description; ?>" size="30" maxlength="80" onkeyup="document.getElementById('desc_len').innerHTML = 'Lengh: '+this.value.length;" /> <span id="desc_len"></span><br />
		<input type="submit" name="submit" value="Submit" />
	</div>		
	<?php }else{ echo "No photo exists for the selecteds railroad / waybill combination!</form></html>"; exit(); } ?>
<?php /* END DESCRIPTION FORM */ }elseif(isset($car_form)){ /* START CAR UPLOAD FORM */ ?>
	<input type="hidden" name="car" value="<?php echo $car; ?>" />
	<input type="hidden" name="car_num" value="<?php echo $car_num; ?>" />
	<div style="display: block; padding: 2px;">
		<div style="display: inline-block; width: 50px; padding: 0px;">&nbsp;
		</div>
		<div style="display: inline-block; padding: 0px;">
			<?php if(file_exists(DOC_ROOT."/car_images/".$car_num.".jpg")){ echo "<img src=\"".WEB_ROOT."/car_images/".$car_num.".jpg\" style=\"width: 200px;\" />"; } ?>
		</div>
	</div>
	<div style="display: block; padding: 2px;">
		<div style="display: inline-block; width: 50px; padding: 0px;">
		File
		</div>
		<div style="display: inline-block; padding: 0px;">
			<?php echo form_upload('user_file'); ?>
		</div>
	</div>
	<div style="display: block; padding: 2px;">
		<div style="display: inline-block; width: 50px; padding: 0px;">
			&nbsp;
		</div>
		<div style="display: inline-block; padding: 0px;">
			<input type="submit" name="submit" value="Upload" />
		</div>
	</div>
<?php /* END CAR UPLOAD FORM */ }elseif(isset($car_form)){ /* START IMG UPLOAD FORM */ ?>
	<input type="hidden" name="id" value="<?php echo $id; ?>" />
	<input type="hidden" name="type" value="<?php echo $type; ?>" />
	<div style="display: block; padding: 2px;">
		<div style="display: inline-block; width: 50px; padding: 0px;">
		File
		</div>
		<div style="display: inline-block; padding: 0px;">
			<?php echo form_upload('user_file'); ?>
		</div>
	</div>
	<div style="display: block; padding: 2px;">
		<div style="display: inline-block; width: 50px; padding: 0px;">
			Desc
		</div>
		<div style="display: inline-block; padding: 0px;">
			<input type="text" name="description" value="" size="30" maxlength="80" onkeyup="document.getElementById('desc_len').innerHTML = 'Lengh: '+this.value.length;" /> <span id="desc_len"></span><br />
		</div>
	</div>
	<div style="display: block; padding: 2px;">
		<div style="display: inline-block; width: 50px; padding: 0px;">
			&nbsp;
		</div>
		<div style="display: inline-block; padding: 0px;">
			<input type="submit" name="submit" value="Upload" />
		</div>
	</div>
<?php /* END IMG UPLOAD FORM */ }else{ /* START MAP UPLOAD FORM */ ?>
	<div style="display: block; padding: 2px;">
		<div style="display: inline-block; width: 50px; padding: 0px;">
		File
		</div>
		<div style="display: inline-block; padding: 0px;">
			<?php echo form_upload('user_file'); ?>
		</div>
	</div>
	<div style="display: block; padding: 2px;">
		<div style="display: inline-block; width: 50px; padding: 0px;">
			&nbsp;
		</div>
		<div style="display: inline-block; padding: 0px;">
			<input type="submit" name="submit" value="Upload" />
		</div>
	</div>
<?php /* END MAP UPLOAD FORM */ } ?>
</form>
