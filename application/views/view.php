<?php
// View view, generic
if(isset($title)){ echo "<h2>".$title."</h2>"; }
echo "<div class=\"info_container\">";
if(!isset($data[0])){$data[0] = array();}
$d_kys = (array)array_keys(@$data[0]);
for($i=0;$i<count($d_kys);$i++){
	$d_tmp = $data[0][$d_kys[$i]];
	if(@strlen($d_tmp) > 0){
		echo "<div class=\"small_txt\" style=\"font-weight: bold; color: #676;\">".$field_names[$i]."</div>";
		echo "<div class=\"info_div\">".$data[0][$d_kys[$i]]."</div>";
	}
}
echo "</div>";
?>
