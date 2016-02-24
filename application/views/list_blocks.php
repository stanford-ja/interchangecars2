<?php
$lnk_kys = array_keys(@$links);
//echo "<pre>"; print_r($lnk_kys[0]); echo "</pre>";
for($i=0;$i<count($lnk_kys);$i++){
	echo "<a href=\"".$links[$lnk_kys[$i]]."\">".$lnk_kys[$i]."</a>&nbsp;";
}
?>
<div style="width: 100%; display: table;">
	<div style="display: table-row">
	<?php
	if(!isset($field_names)){$field_names = $fields;} 
	for($i=0;$i<count($field_names);$i++){
	?>
		<div style="display: table-cell" class="td_title"><?php echo $field_names[$i] ; ?></div>
	<?php } ?>
	<div style="display: table-cell" class="td_title">Options</div>
	</div>
<?php
//echo "<pre>";print_r($porders);echo "</pre>";
for($i=0;$i<count(@$data);$i++){
	$cls = "td1";
	$styl = "background-color: lightgreen;";
	//if(intval($i/2) == floatval($i/2)){$cls = "td2";}
	if(strlen($data[$i]['occupied_by']) > 0){$styl = "background-color: black; color: white;";}
	echo "<div style=\"display: table-row\">";
	for($j=0;$j<count($fields);$j++){
		echo "<div style=\"display: table-cell;".$styl."\" class=\"".$cls."\">".$data[$i][$fields[$j]]."&nbsp;</div>";
	}
	/*
	echo "<div style=\"display: table-cell\" class=\"".$cls."\">";
	if(strlen($data[$i]['occupied_by']) < 1){
		echo "<form name=\"frm".$i."\" method=\"post\" action=\"../save/\">";
		echo "<select name=\"tr_selector\" style=\"font-size:9pt;\" onchange=\"window.location = 'set_occupied/".$data[$i]['id']."/' + this.value;\">
			<option value=\"\" selected=\"selected\"></option>".$train_opts."
			</select>";
		echo "</form>";
	}else{
		//echo "<a href=\"clear_occupied/".$data[$i]['id']."\">Make Available</a>";
	}
	echo "&nbsp;</div>";
	*/
	echo "<div style=\"display: table-cell\" class=\"".$cls."\">";
	if(strlen($data[$i]['occupied_by']) < 1){
		$opt_kys = array_keys($options);
		for($o=0;$o<count($opt_kys);$o++){echo "<a href=\"".$options[$opt_kys[$o]].$data[$i]['id']."\">".$opt_kys[$o]."</a><br />";}
	}else{
		echo "<a href=\"blocks/clear_occupied/".$data[$i]['id']."\">Make Available</a>";
	}

	echo "</div>";
	echo "</div>";
}
?>
</div>