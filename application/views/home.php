<?php
// Home view
if(@$_COOKIE['rr_sess'] > 0){
	echo "<div class=\"box1\" style=\"left: 20px; height: 20px; width: 650px; display: none;\">&nbsp;Search/Select";
	echo $phtml;
	echo $rhtml;
	echo $thtml;
	echo $ahtml; 	
	echo $shtml;
	echo $mhtml;
	echo $ghtml;
	echo "</div>";
}
echo $html;
?>
