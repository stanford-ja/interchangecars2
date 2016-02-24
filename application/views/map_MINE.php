<?php	
$state_kys = array_keys($states);
$url_path = "http://".$_SERVER['SERVER_NAME']."/apps/interchangecars2/"; 
if(strpos($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'],"www/Applications/") > 0){
	$url_path = "http://localhost/Applications/interchangecars2/";
}
//echo "<pre>"; print_r($wbs); echo "</pre>";
//echo "<pre>"; print_r($state_kys); echo "</pre>";
$pgTitle = "MRICF - Model Rail Interchange Cars Facility v2.0";
?>
	<?php if($rr_sess < 1){echo "<strong>Waybills for states and other functions will only work when you are logged in!</strong><br />";} ?>
	<a href="../home">Home</a>&nbsp;
	<a href="usa">USA</a>&nbsp;
	<a href="canada">Canada</a>&nbsp;
	<a href="aust_nz">Australia / NZ</a>&nbsp;
	<div style="position: fixed; right: 0px; top: 150px; max-width: 500px; padding: 5px; background-color: peru; moccasin: white; z-index: 30;">
	<strong>Waybills x Region</strong><br />
	<?php
	$wbs_cntr_kys = array_keys($wbs_cntr); 
	for($wc=0;$wc<count($wbs_cntr_kys);$wc++){
		echo "<strong>".strtoupper($wbs_cntr_kys[$wc])."</strong> (".$wbs_cntr[$wbs_cntr_kys[$wc]].")<br />";
	}
	echo $pin_form;
	echo $route_form;
	?>
	</div>
	<div id="map_container" style="width: 1000px; height: 500px; overflow: auto;">
		<div id="graphic" style="width: 1200px; height: 726px; position: relative;">
		<img src="<?php echo $url_path; ?>images/grid.png" style="position: absolute; top: 0px; left: 0px;" />
		<img src="<?php echo $url_path; ?>images/routes_<?php echo $map_id; ?>.png" style="position: absolute; top: 0px; left: 0px;" />
		<img src="<?php echo $url_path; ?>images/<?php echo $use_map; ?>" style="position: absolute; top: 0px; left: 0px;"/>
		<?php 
		for($p=0;$p<count($pins);$p++){
			$info = "Coords ".$pins[$p]->coord1.", ".$pins[$p]->coord2;
			echo "<span style=\"position: absolute; top: ".$pins[$p]->coord2."px; left: ".$pins[$p]->coord1."px; font-size: 8pt;\"><a href=\"#\" title=\"".$info."\" alt=\"".$info."\"><img src=\"".$url_path."images/dot.png\" />".$pins[$p]->name."</a>";
			if($rr_sess > 0){echo "&nbsp;<a href=\"javascript:{}\" onclick=\"if(confirm('Are you sure you want to delete ".$pins[$p]->name."?')){window.location = '../map/pin_delete/".$pins[$p]->id."/".$pins[$p]->map."';}\">[D]</a>";}
			echo "</span>";
		}
		for($m=0;$m<count($state_kys);$m++){
			//echo "<div style=\"z-index: 10; position: absolute; top: ".$us_states[$state_kys[$m]]['top']."px; left: ".$us_states[$state_kys[$m]]['left']."px; width: ".$us_states[$state_kys[$m]]['width']."px; height: ".$us_states[$state_kys[$m]]['height']."px; font-weight: bold; font-size: 12pt; background-color: lightskyblue; border: 1px solid blue; overflow: hidden; padding: 2px;\" id=\"".$state_kys[$m]."\">";
			echo "<div style=\"z-index: 10; position: absolute; top: ".$states[$state_kys[$m]]['top']."px; left: ".$states[$state_kys[$m]]['left']."px; width: 50px; height: ".$states[$state_kys[$m]]['height']."px; font-weight: bold; font-size: 12pt; background-color: transparent; border: none; overflow: hidden; padding: 2px;\" id=\"".$state_kys[$m]."\">\n";
			echo "<a href=\"javascript:{}\" onclick=\"expandDiv('".$state_kys[$m]."');\" style=\"color: black; text-decoration: none;\">".$states[$state_kys[$m]]['code']."</a>";
			if(@$wbs[$state_kys[$m]]['cntr'] > 0){
				//echo "(".$wbs[$state_kys[$m]]['cntr'].")\n";
				echo "*<br />";
			}
			//echo "<a href=\"javascript:{}\" onclick=\"collapseDiv('".$state_kys[$m]."');\" style=\"color: black; text-decoration: none;\">Close</a><br />";
			echo "</div>";
		} ?>
		</div>
	</div>
	<div id="wb_details" style="z-index: 30; display: none; position: absolute; top: 170px; left: 25px; padding: 10px; background-color: antiquewhite; max-width: 500px; color: black; max-height: 300px; overflow: auto; border: 1px solid red;"></div>
</body>
</html>
