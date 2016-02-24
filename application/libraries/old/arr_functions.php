<?php
// Array based functions
function rrArrAllocToOnly($rrArr,$routing){
	// Requires $rrArr, $routing to be passed otherwise returns array of ALL show_allocto_only RRs.
	$arr = array();
	$rrArrKys = array_keys($rrArr);
	for($k=0;$k<count($rrArrKys); $k++){
		$incl = 0;
		if($rrArr[$rrArrKys[$k]]['show_allocto_only'] == 1 && (strpos("a".$routing,$rrArr[$rrArrKys[$k]]['report_mark']) > 0 || strlen($routing) < 1)){$incl++;}
		if($incl > 0){
			$arr[] = $rrArr[$rrArrKys[$k]]['report_mark'];
		}
	}
	return $arr;
}

function socialLinks($str,$delim){
	// $str = string to create links from
	// $delim = Delimiter (separater)
	$str = str_replace("\n","",$str);
	$str = str_replace(" ","",$str);
	$e = explode($delim,$str);
	$lnks = "";
	for($i=0;$i<count($e);$i++){
		$label = "Social".$i;
		if(strlen($e[$i]) > 0){
			if(strpos("a".$e[$i],"facebook") > 0){$label = "Facebook";}
			if(strpos("a".$e[$i],"twitter") > 0){$label = "Twitter";}
			if(strpos("a".$e[$i],"google") > 0){$label = "Google";}
			if(strpos("a".$e[$i],"yahoo") > 0){$label = "Yahoo";}
			if(strpos("a".$e[$i],"youtube") > 0){$label = "YouTube";}
			$lnks .= "&nbsp;<a href=\"".$e[$i]."\" target=\"soc".$i."\">".$label."</a>";
		}
	}
	return $lnks;
}
?>