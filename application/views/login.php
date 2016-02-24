<?php
	//@session_start();
?>
<div style="margin: 0px; margin-left: 15%; margin-right: 15%;">
<h3>Select your railroad and enter the password in the fields below, then click the Login button.</h3>
Railroad: 
<?php 
echo form_open_multipart(WEB_ROOT.'/index.php/login/chk');
echo form_dropdown('rr_selected',$rr_opts,'','onchange="hideEle(\'nwpd\');if(this.value == \'9999\'){showEle(\'nwpd\');}"');
?>
</select>
&nbsp;&nbsp;&nbsp;
Password: <input type="password" name="p_word" size="8" value="" />
<input type="hidden" name="goTo" value="<?php echo $_SERVER['PHP_SELF']; ?>" />
<input type="hidden" name="goToQry" value="<?php echo @$qry_string; ?>" />&nbsp;&nbsp;&nbsp;
<input name="submit" value="Login" type="submit" class="submit" />

</div>
<?php echo form_close(); ?>