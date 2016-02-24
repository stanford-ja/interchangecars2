<div class="td3" style="text-align: center; padding: 5px; margin: 3px; margin-left: 10%; margin-right: 10%;">
<?php if(strlen(@$error) > 0){echo "<span class=\"error\">".$error."</span>";} ?>
<?php
for($f=0;$f<count(@$fields);$f++){
	echo $fields[$f];
}
?>
</div>