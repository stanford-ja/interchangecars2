<?php 
$lnk_kys = array_keys(@$links);
if(isset($before_table)){echo $before_table;}
?>
<table style="width: 100%;">
	<thead style="margin-top: 3px;">
	<tr>
	<?php
	if(!isset($field_names)){$field_names = $fields;}
	for($i=0;$i<count($field_names);$i++){
		$clss = "";if(isset($field_classes[$i])){$clss = " ".$field_classes[$i];}
		$wid = "auto"; //intval(100/(count($field_names)+1))."%";
		if(isset($widths[$i])){ $wid = $widths[$i]; }
	?>
		<td class="td_title<?php echo $clss; ?>" style="width:<?php echo $wid; ?>"><?php echo $field_names[$i] ; ?><br /></td>
	<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
for($i=0;$i<count(@$data);$i++){
	echo "<tr>";
	$l_disp = 0; // If > 0 then show links, otherwise dont.
	for($j=0;$j<count($fields);$j++){
		$styl = "";if(isset($field_styles[$j])){$styl = $field_styles[$j];}
		$clss = "";if(isset($field_classes[$j])){$clss = $field_classes[$j];}
		if(isset($data[$i][$fields[$j]])){
			$l_disp++;
			$data[$i][$fields[$j]] = str_replace(date('Y-m-d '),"<span style=\"background-color: yellow;\">".date('Y-m-d ')."</span>",$data[$i][$fields[$j]]);
			//echo "<td class=\"".$clss."\" style=\"".$styl.";width:".$wid."\">".str_replace(",",", ",$data[$i][$fields[$j]])."</td>";
			echo "<td>".str_replace(",",", ",$data[$i][$fields[$j]])."</td>";
		}
	}
	echo "</tr>";
}
?>
	</tbody>
</table>
<?php if(isset($after_table)){echo $after_table;} ?>
