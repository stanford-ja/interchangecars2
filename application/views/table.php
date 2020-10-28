<?php 
/*
TO MAKE THE TABLE USE THE JQuery DataTables PLUGIN, THE FOLLOWING NEEDS TO BE SET IN THE CONTROLLER:
	$this->arr['jquery'] = "\$('.table1').DataTable({ paging: false, searching: false, responsive: true, info: false, stateSave: true, order: [[ 0, 'asc' ], [ 1, 'asc' ]] });";
	
	The Following settigns can be used:
	* paging: show Paging numbers
	* searching: allow search
	* responsive: make responsive to screen size (handy for mobile phones)
	* info: (eg, "1 to xxd of xx records")
	* stateSave: save current state, including ordering, etc, to browser's localstorage (if supported)
	* order: column numbers to order by and direction
*/

if(isset($shtml)){echo $shtml;} // Search facility!
$lnk_kys = array_keys(@$links);
//echo "<pre>"; print_r($lnk_kys[0]); echo "</pre>";
echo "<p>";
for($i=0;$i<count($lnk_kys);$i++){
	if(is_array($links[$lnk_kys[$i]])){
		$larr_kys = array_keys($links[$lnk_kys[$i]]);
		$l_tmp = "<a";
		for($lk=0;$lk<count($larr_kys);$lk++){$l_tmp .= " ".$larr_kys[$lk]."=\"".$links[$lnk_kys[$i]][$larr_kys[$lk]]."\"";}
		$l_tmp .= ">".$lnk_kys[$i]."</a>&nbsp;";
		echo "<div style=\"display: inline-block;\">".$l_tmp."</div>";
	}else{
		echo "<div style=\"display: inline-block;\"><a href=\"".$links[$lnk_kys[$i]]."\">".$lnk_kys[$i]."</a></div>&nbsp;";
	}
}
echo "</p>";
if(isset($before_table)){echo $before_table;}
if(isset($list_order_NOTNEEDED)){ ?>
	<div style="display: inline-block; padding: 5px; float: right;">
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<select name="order_by"> 
		<?php for($ol=0;$ol<count($list_order);$ol++){ 
			$sel = ""; if(isset($_POST['order_by']) && $_POST['order_by'] == $list_order[$ol]['field']){ $sel = " selected=\"selected\""; }
			echo "<option value=\"".$list_order[$ol]['field']."\"".$sel.">".$list_order[$ol]['label']."</option>\n";
		} ?>
		</select> 
		<input type="submit" name="list_order" value="Order List" />
	</form>
	</div>
<?php } ?>
<table class="table1 hover order-column" style="width: 95%;">
	<thead style="margin-top: 3px;">
	<tr>
	<?php
	if(!isset($field_names)){$field_names = $fields;}
	for($i=0;$i<count($field_names);$i++){
		$wid = intval(100/(count($field_names)+1))."%";
		if(isset($widths[$i])){ $wid = $widths[$i]; }
	?>
		<td class="td_title"><?php echo $field_names[$i] ; ?></td>
	<?php } ?>
		<td class="td_title">Options</td>
	</tr>
	</thead>
	<tbody>
<?php
//echo "<pre>";print_r($porders);echo "</pre>";
for($i=0;$i<count(@$data);$i++){
	echo "<tr>";
	$l_disp = 0; // If > 0 then show links, otherwise dont.
	for($j=0;$j<count($fields);$j++){
		$wid = intval(100/(count($field_names)+1))."%";
		if(isset($widths[$j])){ $wid = $widths[$j]; }
		$styl = "";if(isset($field_styles[$j])){$styl = $field_styles[$j];}
		if(isset($data[$i][$fields[$j]])){
			$l_disp++;
			$data[$i][$fields[$j]] = str_replace(date('Y-m-d '),"<span style=\"background-color: yellow;\">".date('Y-m-d ')."</span>",$data[$i][$fields[$j]]);
			echo "<td>".str_replace(",",", ",$data[$i][$fields[$j]])."</td>";
		}
	}
	echo "<td>";
	if($l_disp > 0){
		$opt_kys = array_keys($options);
		for($o=0;$o<count($opt_kys);$o++){ 
		$dat_id = @$data[$i]['id'];
		$href_act = "href";
		$ooko = $options[$opt_kys[$o]];
		if(strpos("a".$ooko,"onclick:")){ 
			$href_act = "href=\"javascript:{}\" onclick"; 
			$ooko = str_replace("onclick:","",$ooko);
		}
		if(strpos("a".$ooko,"[id]") > 0){ 
			$ooko = str_replace("[id]",$dat_id,$ooko);
			$ooko = str_replace("[id]",$dat_id,$ooko);
			$dat_id = ""; 
		}
		echo "<div style=\"display: inline-block;\"><a ".$href_act."=\"".$ooko.$dat_id."\">".$opt_kys[$o]."</a></div> "; 
		}
	}
	echo "&nbsp;</td>";
	echo "</tr>";
}
?>
	</tbody>
</table>
<?php if(isset($after_table)){echo $after_table;} ?>
