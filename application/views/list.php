<?php
if(isset($shtml)){echo $shtml;} // Search facility!
$lnk_kys = array_keys(@$links);
//echo "<pre>"; print_r($lnk_kys[0]); echo "</pre>";
for($i=0;$i<count($lnk_kys);$i++){
	if(is_array($links[$lnk_kys[$i]])){
		$larr_kys = array_keys($links[$lnk_kys[$i]]);
		$l_tmp = "<a";
		for($lk=0;$lk<count($larr_kys);$lk++){$l_tmp .= " ".$larr_kys[$lk]."=\"".$links[$lnk_kys[$i]][$larr_kys[$lk]]."\"";}
		$l_tmp .= ">".$lnk_kys[$i]."</a>&nbsp;";
		echo $l_tmp;
	}else{
		echo "<a href=\"".$links[$lnk_kys[$i]]."\">".$lnk_kys[$i]."</a>&nbsp;";
	}
}
if(isset($before_table)){echo $before_table;}
?>
<div style="width: 100%; display: block;">
	<div style="display: block;" class="td_title">
	<?php
	if(!isset($field_names)){$field_names = $fields;}
	for($i=0;$i<count($field_names);$i++){
		$wid = intval(100/(count($field_names)+1))."%";
		if(isset($widths[$i])){ $wid = $widths[$i]; }
	?>
		<div style="display: inline-block; overflow: hidden; vertical-align: top; padding: 3px; min-width: 80px; width: <?php echo $wid; ?>;"><?php echo $field_names[$i] ; ?></div>
	<?php } ?>
	<div style="display: inline-block; padding: 3px; overflow: hidden; vertical-align: top; width: 80px;">Options</div>
	</div>
<?php
//echo "<pre>";print_r($porders);echo "</pre>";
for($i=0;$i<count(@$data);$i++){
	$cls = "td1";
	if(intval($i/2) == floatval($i/2)){$cls = "td2";}
	echo "<div style=\"display: block;\" class=\"".$cls."\">";
	$l_disp = 0; // If > 0 then show links, otherwise dont.
	for($j=0;$j<count($fields);$j++){
		$wid = intval(100/(count($field_names)+1))."%";
		if(isset($widths[$j])){ $wid = $widths[$j]; }
		$styl = "";if(isset($field_styles[$j])){$styl = $field_styles[$j];}
		if(isset($data[$i][$fields[$j]])){
			$l_disp++;
			$data[$i][$fields[$j]] = str_replace(date('Y-m-d '),"<span style=\"background-color: yellow;\">".date('Y-m-d ')."</span>",$data[$i][$fields[$j]]);
			echo "<div style=\"display: inline-block; overflow: hidden; vertical-align: top; padding: 3px; min-width: 80px; width:".$wid.";".$styl."\">".str_replace(",",", ",$data[$i][$fields[$j]])."&nbsp;</div>";
		}
	}
	if($l_disp > 0){
		echo "<div style=\"display: inline-block; overflow: hidden; padding: 3px; vertical-align: top; width: 80px;\">";
		$opt_kys = array_keys($options);
		for($o=0;$o<count($opt_kys);$o++){echo "<a href=\"".$options[$opt_kys[$o]].@$data[$i]['id']."\">".$opt_kys[$o]."</a><br />";}
		echo "</div>";
	}
	echo "</div>";
}
?>
</div>
<?php if(isset($after_table)){echo $after_table;} ?>
